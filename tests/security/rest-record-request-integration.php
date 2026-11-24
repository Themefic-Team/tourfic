<?php
/**
 * Real WordPress REST dispatch and MySQL regression matrix with synthetic data.
 *
 * Run only with a freshly installed disposable WordPress site:
 * TOURFIC_SECURITY_WP_ROOT=/private/tmp/.../wordpress \
 * TOURFIC_SECURITY_DISPOSABLE=1 php tests/security/rest-record-request-integration.php [--pro]
 * The site must have .tourfic-security-test containing 'tourfic-rest-security'.
 * Optional TOURFIC_SECURITY_SOURCE_ROOT selects a preview/baseline plugin tree.
 * This suite deliberately never bootstraps the developer's existing site.
 */

$site = realpath( getenv( 'TOURFIC_SECURITY_WP_ROOT' ) ?: '' );
if ( 'cli' !== PHP_SAPI || '1' !== getenv( 'TOURFIC_SECURITY_DISPOSABLE' ) || ! $site
	|| 0 !== strpos( $site, '/private/tmp/tourfic-rest-security-' )
	|| ! is_file( $site . '/.tourfic-security-test' )
	|| 'tourfic-rest-security' !== trim( file_get_contents( $site . '/.tourfic-security-test' ) ) ) {
	fwrite( STDERR, "Refusing to run outside an explicitly marked disposable site.\n" );
	exit( 1 );
}
define( 'SAVEQUERIES', true );
require $site . '/wp-load.php';
if ( 'tourfic_rest_security' !== $wpdb->get_var( 'SELECT DATABASE()' ) ) {
	throw new RuntimeException( 'The database must be the dedicated tourfic_rest_security fixture.' );
}
$root   = dirname( __DIR__, 2 );
$source = getenv( 'TOURFIC_SECURITY_SOURCE_ROOT' ) ?: $root;
$pro    = in_array( '--pro', $argv, true );
define( 'TOURFIC_INC_PATH', $root . '/inc/' );
require $root . '/autoloader.php';
require_once $root . '/inc/Classes/Helper.php';
if ( ! class_exists( 'WC_Payment_Gateways' ) ) {
	throw new RuntimeException( 'Activate WooCommerce in the disposable site first.' );
}
if ( $pro ) {
	$pro_source = dirname( $source ) . '/tourfic-pro';
	foreach ( array( 'Rest_API', 'Booking_Rest_API', 'Enquiry_Rest_API' ) as $file ) {
		require $pro_source . '/inc/frontend-dashboard/classes/TF_FD_' . $file . '.php';
	}
	foreach ( array( 'Tour', 'Apartment', 'Rental', 'Hotel', 'Room', 'User', 'Vendor', 'Taxonomy', 'Hotel_Backend_Booking', 'Tour_Backend_Booking', 'Integration' ) as $file ) {
		$relative = '/inc/frontend-dashboard/classes/TF_FD_' . $file . '_Rest_API.php';
		$path = is_file( $pro_source . $relative ) ? $pro_source . $relative : dirname( $root ) . '/tourfic-pro' . $relative;
		require $path;
	}
	require $pro_source . '/inc/frontend-dashboard/classes/TF_FD_API_Routes.php';
	remove_action( 'rest_api_init', array( TF_FD_API_Routes::get_instance(), 'register_all_routes' ) );
	// This fixture loads the real vendor SQL provider without unrelated hooks.
	$fixture = file_get_contents( $root . '/tests/fixtures/rest-pro-provider.php' );
	$start   = strpos( $fixture, '$tokens = ' );
	$end     = strpos( $fixture, '\nfunction tfopt', $start );
	if ( false === $end ) { $end = strpos( $fixture, "\nfunction tfopt", $start ); }
	$pro_root = dirname( $source ) . '/tourfic-pro/';
	eval( substr( $fixture, $start, $end - $start ) );
	if ( ! function_exists( 'tfopt' ) ) {
		function tfopt( $key = '', $default = null ) { return \Tourfic\Classes\Helper::tfopt( $key, $default ); }
	}
	$bookings = new TF_FD_Booking_Rest_API();
	$enquiries = new TF_FD_Enquiry_Rest_API();
	$route_class = 'TF_FD_API_Routes';
} else {
	foreach ( array( 'Rest_API', 'Booking_Rest_API', 'Enquiry_Rest_API', 'API_Routes' ) as $file ) {
		require $source . '/inc/Classes/REST_API/TF_' . $file . '.php';
	}
	$bookings = new Tourfic_Booking_Rest_API();
	$enquiries = new Tourfic_Enquiry_Rest_API();
	$route_class = 'Tourfic_API_Routes';
}
$reflection = new ReflectionClass( $route_class );
$routes = $reflection->newInstanceWithoutConstructor();
$property = $reflection->getProperty( 'api_classes' );
$property->setAccessible( true );
$property->setValue( $routes, array( 'booking' => $bookings, 'enquiry' => $enquiries ) );
add_action( 'rest_api_init', function() use ( $routes, $reflection ) {
	foreach ( array( 'register_booking_routes', 'register_enquiry_routes' ) as $name ) {
		$method = $reflection->getMethod( $name );
		$method->setAccessible( true );
		$method->invoke( $routes );
	}
} );
rest_get_server();

$assertions = 0;
$failures = array();
function tourfic_security_expect( $condition, $label ) {
	++$GLOBALS['assertions'];
	if ( ! $condition ) { $GLOBALS['failures'][] = $label; }
}
function tourfic_security_request( $route, $params = array(), $body = null ) {
	$request = new WP_REST_Request( 'GET', '/tf/v1/' . $route );
	$request->set_query_params( $params );
	if ( null !== $body ) {
		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body( wp_json_encode( $body ) );
	}
	$before = count( $GLOBALS['wpdb']->queries );
	ob_start();
	$response = rest_do_request( $request );
	$output = ob_get_clean();
	$queries = array_slice( $GLOBALS['wpdb']->queries, $before );
	$protected_queries = array_filter( $queries, function( $query ) {
		return false !== strpos( $query[0], 'tf_order_data' ) || false !== strpos( $query[0], 'tf_enquiry_data' );
	} );
	tourfic_security_expect( '' === $output, $route . ': no diagnostic output' );
	return array( $response->get_status(), $response->get_data(), array_values( $protected_queries ) );
}
function tourfic_security_ids( $data, $kind ) {
	return array_map( 'intval', array_column( 'orders' === $kind ? $data['data'] : $data, 'id' ) );
}

// Explicit table definitions exercise the actual persisted column names.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}tf_order_data" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}tf_enquiry_data" );
$wpdb->query( "CREATE TABLE {$wpdb->prefix}tf_order_data (
 id bigint PRIMARY KEY, order_id bigint, post_id bigint, post_type varchar(30), room_id varchar(40), room_number int,
 check_in varchar(30), check_out varchar(30), billing_details longtext, shipping_details longtext, order_details longtext,
 customer_id bigint, payment_method varchar(30), ostatus varchar(30), order_date datetime,
 checkinout varchar(10), checkinout_by longtext)" );
$wpdb->query( "CREATE TABLE {$wpdb->prefix}tf_enquiry_data (
 id bigint PRIMARY KEY, post_id bigint, post_type varchar(30), author_id bigint, uname varchar(100),
 uemail varchar(100), udescription text, enquiry_status varchar(30), created_at datetime, reply_data longtext,
 server_data text, author_roles varchar(50))" );
$users = array();
foreach ( array( 'administrator', 'subscriber', 'subscriber', 'subscriber', 'subscriber' ) as $index => $role ) {
	$login = 'rest-fixture-' . $index;
	$existing = get_user_by( 'login', $login );
	$id = $existing ? $existing->ID : wp_insert_user( array(
		'user_login' => $login, 'user_pass' => wp_generate_password(), 'user_email' => $login . '@example.invalid', 'role' => $role,
	) );
	if ( is_wp_error( $id ) ) { throw new RuntimeException( 'Cannot create synthetic account.' ); }
	$user = new WP_User( $id );
	$user->set_role( $role );
	$users[] = $user;
}
list( $admin, $subscriber_a, $subscriber_b, $owner_a, $owner_b ) = $users;
$services = array(
	'hotel' => array( 'tf_hotel', 'edit_tf_hotels', 'edit_others_tf_hotels' ),
	'tour' => array( 'tf_tours', 'edit_tf_tourss', 'edit_others_tf_tourss' ),
	'apartment' => array( 'tf_apartment', 'edit_tf_apartments', 'edit_others_tf_apartments' ),
	'car' => array( 'tf_carrental', 'edit_tf_carrentals', 'edit_others_tf_carrentals' ),
);
$base = 100;
foreach ( $services as $type => $service ) {
	list( $cpt, $capability, $others ) = $service;
	register_post_type( $cpt, array( 'public' => true, 'capability_type' => $cpt, 'map_meta_cap' => true ) );
	$admin->add_cap( $capability );
	$owner_a->add_cap( $capability );
	$owner_b->add_cap( $capability );
	$posts = array();
	foreach ( array( $owner_a, $owner_b ) as $owner ) {
		$posts[] = wp_insert_post( array( 'post_type' => $cpt, 'post_status' => 'publish', 'post_author' => $owner->ID, 'post_title' => 'Synthetic service' ) );
	}
	foreach ( $posts as $offset => $post_id ) {
		$wpdb->insert( $wpdb->prefix . 'tf_order_data', array(
			'id' => $base + $offset, 'order_id' => 1000 + $base + $offset, 'post_id' => $post_id, 'post_type' => $type,
			'check_in' => '2026-12-01', 'check_out' => '2026-12-02', 'billing_details' => '{"billing_email":"synthetic@example.invalid"}',
			'shipping_details' => '{}', 'order_details' => '{"check_in":"2026-12-01","check_out":"2026-12-02"}',
			'customer_id' => $subscriber_a->ID, 'payment_method' => 'offline', 'ostatus' => $offset ? 'pending' : 'completed',
			'order_date' => '2026-11-23 10:00:00', 'checkinout' => $offset ? 'out' : 'in', 'checkinout_by' => '{}',
		) );
		$wpdb->insert( $wpdb->prefix . 'tf_enquiry_data', array(
			'id' => $base + $offset, 'post_id' => $post_id, 'post_type' => $cpt, 'author_id' => $offset ? $owner_b->ID : $owner_a->ID,
			'uname' => 'Synthetic customer', 'uemail' => 'synthetic@example.invalid', 'udescription' => 'Synthetic enquiry',
			'enquiry_status' => $offset ? 'replied' : 'unread', 'created_at' => '2026-11-23 10:00:00', 'reply_data' => '[]',
			'server_data' => '{}', 'author_roles' => 'subscriber',
		) );
	}
	foreach ( array( 'orders' => $type, 'enquiries' => $cpt ) as $kind => $post_type ) {
		if ( 'car' === $type && 'enquiries' === $kind ) { continue; }
		$params = array( 'post_type' => $post_type );
		foreach ( array( 0 => 401, $subscriber_a->ID => 403, $subscriber_b->ID => 403 ) as $actor => $expected ) {
			wp_set_current_user( $actor );
			foreach ( array( array(), array( 'user_id' => $admin->ID ), array( 'user_id' => $owner_a->ID, 'post_id' => $posts[0] ) ) as $spoof ) {
				list( $status, $data, $sql ) = tourfic_security_request( $kind, array_merge( $params, $spoof ) );
				tourfic_security_expect( $expected === $status && ! $sql, "$type/$kind: unauthenticated/subscriber identity manipulation denied before SQL" );
			}
		}
		wp_set_current_user( $subscriber_a->ID );
		$detail_route = 'orders' === $kind ? 'order/' : 'enquiries/';
		list( $status, $data, $sql ) = tourfic_security_request( $detail_route . $base );
		tourfic_security_expect( 403 === $status && ! $sql, "$type/$kind: subscriber detail denied before SQL" );
		wp_set_current_user( $owner_a->ID );
		list( $status, $data ) = tourfic_security_request( $kind, $params );
		tourfic_security_expect( 200 === $status && array( $base ) === tourfic_security_ids( $data, $kind ), "$type/$kind: owned records only" );
		list( $status, $data ) = tourfic_security_request( $kind, $params + array( 'post_id' => $posts[1], 'user_id' => $admin->ID ) );
		tourfic_security_expect( 200 === $status && array() === tourfic_security_ids( $data, $kind ), "$type/$kind: foreign list filter stays scoped" );
		$detail = 'orders' === $kind ? 'order/' : 'enquiries/';
		list( $status ) = tourfic_security_request( $detail . $base );
		tourfic_security_expect( 200 === $status, "$type/$kind: owned detail works" );
		list( $status, $data, $sql ) = tourfic_security_request( $detail . ( $base + 1 ) );
		tourfic_security_expect( 403 === $status && 1 === count( $sql ) && false === strpos( $sql[0][0], 'SELECT *' ), "$type/$kind: foreign detail denied before customer fields are read" );
		list( $status ) = tourfic_security_request( $detail . '99999' );
		tourfic_security_expect( 404 === $status, "$type/$kind: absent record returns 404" );
		list( $status ) = tourfic_security_request( $detail . $base, array( 'id' => $base + 1 ) );
		tourfic_security_expect( 400 === $status, "$type/$kind: URL/query ID conflict rejected" );
		foreach ( array( 'post_id', 'user_id' ) as $param ) {
			foreach ( array( -1, '-1', 0, '0', '1.9', 1.9, '1e2', ' 1', '+1', '01', str_repeat( '9', 40 ), true, array( 1 ), '1\'', '[MALFORMED_TEST_VALUE]' ) as $invalid ) {
				list( $status, $data, $sql ) = tourfic_security_request( $kind, $params + array( $param => $invalid ) );
				tourfic_security_expect( 400 === $status && ! $sql, "$type/$kind: $param rejects invalid identifier before SQL" );
			}
		}
		$filters = 'orders' === $kind ? array( 'checkinout' => 'in', 'order_status' => 'completed' ) : array( 'filters' => 'unread' );
		foreach ( $filters as $param => $valid ) {
			foreach ( array( strtoupper( $valid ), '<' . $valid . '>', $valid . "'", array( $valid ), array( 'unexpected' => $valid ), '[MALFORMED_TEST_VALUE]' ) as $invalid ) {
				list( $status, $data, $sql ) = tourfic_security_request( $kind, $params + array( $param => $invalid ) );
				tourfic_security_expect( 400 === $status && ! $sql, "$type/$kind: invalid enum/schema rejected" );
			}
			list( $status, $data ) = tourfic_security_request( $kind, $params + array( $param => $valid ) );
			tourfic_security_expect( 200 === $status && array( $base ) === tourfic_security_ids( $data, $kind ), "$type/$kind: valid filter works" );
		}
		foreach ( array( array(), array( 'post_type' => 'post' ), array( 'post_type' => array( $post_type ) ) ) as $invalid ) {
			list( $status, $data, $sql ) = tourfic_security_request( $kind, $invalid );
			tourfic_security_expect( 400 === $status && ! $sql, "$type/$kind: missing or invalid service type rejected" );
		}
		list( $status, $data, $sql ) = tourfic_security_request( $kind, $params + array( 'post_id' => $posts[0] ), array( 'post_id' => $posts[1] ) );
		tourfic_security_expect( 400 === $status && ! $sql, "$type/$kind: conflicting JSON/query filters rejected" );
		parse_str( 'post_id=' . $posts[1] . '&post_id=' . $posts[0], $duplicates );
		list( $status, $data ) = tourfic_security_request( $kind, $params + $duplicates + array( 'unknown_filter' => "'", 'orderby' => "'" ) );
		tourfic_security_expect( 200 === $status && array( $base ) === tourfic_security_ids( $data, $kind ), "$type/$kind: PHP last-scalar duplicate and ignored unknown keys are deterministic" );
		wp_set_current_user( $admin->ID );
		list( $status, $data ) = tourfic_security_request( $kind, $params );
		tourfic_security_expect( 200 === $status && 2 === count( tourfic_security_ids( $data, $kind ) ), "$type/$kind: administrator cross-owner scope" );
		// A controlled missing fixture table exercises wpdb failure with display enabled.
		$table = $wpdb->prefix . ( 'orders' === $kind ? 'tf_order_data' : 'tf_enquiry_data' );
		$wpdb->query( "RENAME TABLE $table TO {$table}_fixture_backup" );
		$display = $wpdb->show_errors( true );
		$suppress = $wpdb->suppress_errors( false );
		try {
			list( $status, $data ) = tourfic_security_request( $kind, $params );
			tourfic_security_expect( 500 === $status && 'tourfic_rest_database_error' === ( $data['code'] ?? '' ), "$type/$kind: generic database error response" );
			tourfic_security_expect( false === $wpdb->suppress_errors, "$type/$kind: previous error display state restored" );
			list( $detail_status, $detail_data ) = tourfic_security_request( $detail . $base );
			tourfic_security_expect( 500 === $detail_status && 'tourfic_rest_database_error' === ( $detail_data['code'] ?? '' ), "$type/$kind: detail query failure returns generic error" );
		} finally {
			$wpdb->suppress_errors( $suppress );
			$wpdb->show_errors( $display );
			$wpdb->query( "RENAME TABLE {$table}_fixture_backup TO $table" );
		}
	}
	if ( $pro ) {
		wp_set_current_user( $owner_a->ID );
		list( $status, $data ) = tourfic_security_request( 'orders', array( 'post_type' => $type, 'order_id' => 1000 + $base ) );
		tourfic_security_expect( 200 === $status && array( $base ) === tourfic_security_ids( $data, 'orders' ), "$type: Pro order_id filter retained" );
		list( $status, $data, $sql ) = tourfic_security_request( 'orders', array( 'post_type' => $type, 'order_id' => '-1' ) );
		tourfic_security_expect( 400 === $status && ! $sql, "$type: Pro order_id strict validation" );
		if ( in_array( $type, array( 'hotel', 'car' ), true ) ) {
			$reader = 'hotel' === $type ? new TF_FD_Hotel_Rest_API() : new TF_FD_Rental_Rest_API();
			$method = 'hotel' === $type ? 'tf_fd_get_hotel_orders' : 'tf_fd_get_rental_orders';
			$request = new WP_REST_Request( 'GET' );
			$request->set_query_params( array( 'user_id' => $admin->ID ) );
			foreach ( array( 0, $subscriber_a->ID, $subscriber_b->ID ) as $actor ) {
				wp_set_current_user( $actor );
				$result = $reader->$method( $request );
				tourfic_security_expect( is_wp_error( $result ) && ( $actor ? 403 : 401 ) === $result->get_error_data()['status'], "$type: additional reader ignores supplied identity" );
			}
			wp_set_current_user( $owner_a->ID );
			$result = $reader->$method( $request );
			tourfic_security_expect( array( $base ) === array_map( 'intval', array_column( $result['orders'], 'id' ) ), "$type: additional reader scopes to authenticated owner" );
			$detail_method = 'hotel' === $type ? 'tf_fd_get_hotel_order_details' : 'tf_fd_get_rental_order_details';
			$request->set_param( 'id', $base + 1 );
			$result = $reader->$detail_method( $request );
			tourfic_security_expect( is_wp_error( $result ) && 403 === $result->get_error_data()['status'], "$type: additional detail reader denies a foreign record" );
			$request->set_param( 'id', $base );
			$result = $reader->$detail_method( $request );
			tourfic_security_expect( ! is_wp_error( $result ) && $base === (int) $result['id'], "$type: additional detail reader preserves own-record rendering" );
			wp_set_current_user( $subscriber_a->ID );
			$result = $reader->$detail_method( $request );
			tourfic_security_expect( is_wp_error( $result ) && 403 === $result->get_error_data()['status'], "$type: additional detail reader denies a subscriber" );
		}
	}
	$base += 10;
}
// Query helpers retain sorting/pagination and bind string values separately.
wp_set_current_user( $admin->ID );
$result = \Tourfic\Classes\Helper::tourfic_order_table_data( array( 'select' => 'id', 'post_type' => 'hotel', 'orderby' => 'id', 'order' => 'ASC', 'limit' => array( 'offset' => 1, 'per_page' => 1 ) ) );
tourfic_security_expect( array( 101 ) === array_map( 'intval', array_column( $result, 'id' ) ), 'Shared SQL helper sorting/pagination preserved' );
$result = \Tourfic\Classes\Helper::tourfic_order_table_data( array( 'select' => 'id', 'post_type' => 'hotel', 'where' => array( 'room_id' => "synthetic'quote" ) ) );
tourfic_security_expect( array() === $result && '' === $wpdb->last_error, 'Shared SQL helper safely binds a quoted string' );
if ( $pro ) {
	foreach ( array( 'id, undeclared_column', '[MALFORMED_TEST_VALUE]' ) as $projection ) {
		$result = tourfic_vendor_order_table_data( array( 'select' => $projection, 'post_type' => 'hotel', 'author' => $owner_a->ID, 'orderby' => 'id', 'order' => 'ASC' ) );
		tourfic_security_expect( array( 'id' ) === array_keys( $result[0] ) && '' === $wpdb->last_error, 'Pro vendor query projection is allowlisted' );
	}
}
foreach ( $failures as $failure ) { fwrite( STDERR, 'FAIL: ' . $failure . "\n" ); }
echo ( $failures ? 'FAIL' : 'PASS' ) . ': ' . $assertions . ' ' . ( $pro ? 'Pro' : 'Free' ) . " assertions using WordPress REST dispatch and isolated MySQL.\n";
exit( $failures ? 1 : 0 );
