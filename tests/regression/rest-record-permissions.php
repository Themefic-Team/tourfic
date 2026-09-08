<?php
/**
 * Isolated REST authorization regressions using actual handlers and SQLite SQL.
 * Run: php tests/regression/rest-record-permissions.php
 * Pro: php tests/regression/rest-record-permissions.php --pro
 * No WordPress bootstrap, network, persistent database or real account changes.
 */

namespace Tourfic\Traits {
	trait Singleton {}
	trait TF_Fonts {}
	trait Action_Helper {}
}

namespace Tourfic\Core {
	class Enquiry {
		public static function convert_to_wp_timezone( $date ) { return $date; }
	}
}

namespace {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
	define( 'ARRAY_A', 'ARRAY_A' );
	set_error_handler( function ( $severity, $message, $file, $line ) {
		throw new \ErrorException( $message, 0, $severity, $file, $line );
	} );
	if ( ! extension_loaded( 'pdo_sqlite' ) ) {
		throw new \RuntimeException( 'pdo_sqlite is required for the isolated SQL regression.' );
	}

	class WP_Error {
		public $code;
		public $data;
		public function __construct( $code, $message, $data ) { $this->code = $code; $this->data = $data; }
	}
	class WP_REST_Request {
		private $params;
		public function __construct( $params = array() ) { $this->params = $params; }
		public function get_param( $name ) { return $this->params[ $name ] ?? null; }
	}
	class WC_Payment_Gateways {
		public static function instance() { return new self(); }
		public function get_available_payment_gateways() { return array(); }
	}
	class Tourfic_Test_REST_DB {
		public $prefix = 'wp_';
		public $posts = 'wp_posts';
		public $queries = array();
		public $pdo;
		public function __construct() {
			$this->pdo = new \PDO( 'sqlite::memory:' );
			$this->pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
		}
		public function prepare( $sql, ...$values ) {
			if ( isset( $values[0] ) && is_array( $values[0] ) ) { $values = $values[0]; }
			$index = 0;
			$sql = preg_replace_callback( '/%[ds]/', function ( $match ) use ( $values, &$index ) {
				$value = $values[ $index++ ];
				return '%d' === $match[0] ? (string) (int) $value : $this->pdo->quote( (string) $value );
			}, $sql );
			if ( $index !== count( $values ) ) { throw new \RuntimeException( 'SQL placeholder mismatch.' ); }
			return $sql;
		}
		public function get_results( $sql, $format = null ) {
			$this->queries[] = $sql;
			return $this->pdo->query( $sql )->fetchAll( ARRAY_A === $format ? \PDO::FETCH_ASSOC : \PDO::FETCH_OBJ );
		}
		public function get_row( $sql, $format = null ) { return $this->get_results( $sql, $format )[0] ?? null; }
		public function insert_fixture( $table, $row ) {
			$sql = 'INSERT INTO ' . $table . ' (' . implode( ',', array_keys( $row ) ) . ') VALUES ('
				. implode( ',', array_fill( 0, count( $row ), '?' ) ) . ')';
			$this->pdo->prepare( $sql )->execute( array_values( $row ) );
		}
	}

	$GLOBALS['tourfic_rest_tests'] = 0;
	$GLOBALS['tourfic_rest_actor'] = 17;
	$GLOBALS['tourfic_rest_caps'] = array();
	$GLOBALS['tourfic_rest_settings'] = array();
	$GLOBALS['tourfic_rest_posts'] = array();
	$GLOBALS['tourfic_rest_users'] = array();
	$GLOBALS['tourfic_rest_user_queries'] = array();
	$GLOBALS['wpdb'] = new Tourfic_Test_REST_DB();
	function tourfic_rest_assert( $condition, $message ) {
		++$GLOBALS['tourfic_rest_tests'];
		if ( ! $condition ) { throw new \RuntimeException( $message ); }
	}
	function tourfic_rest_error( $response, $status, $message ) {
		tourfic_rest_assert( $response instanceof WP_Error && $status === $response->data['status'], $message );
	}
	function tourfic_rest_actor( $caps = array(), $roles = array( 'custom_agent' ), $id = 17 ) {
		$GLOBALS['tourfic_rest_caps'] = array_fill_keys( $caps, true );
		$GLOBALS['tourfic_rest_actor'] = $id;
		$GLOBALS['tourfic_rest_settings'] = array();
		$GLOBALS['tourfic_rest_users'][17]->roles = $roles;
		$GLOBALS['wpdb']->queries = array();
		$GLOBALS['tourfic_rest_user_queries'] = array();
	}
	function get_current_user_id() { return $GLOBALS['tourfic_rest_actor']; }
	function is_user_logged_in() { return get_current_user_id() > 0; }
	function current_user_can( $cap, ...$args ) {
		$key = $args ? $cap . ':' . implode( ':', $args ) : $cap;
		return ! empty( $GLOBALS['tourfic_rest_caps'][ $key ] );
	}
	function get_userdata( $id ) { return $GLOBALS['tourfic_rest_users'][ $id ] ?? false; }
	function get_user_by( $field, $value ) { return get_userdata( $value ); }
	function get_option( $key ) { return $GLOBALS['tourfic_rest_settings']; }
	function get_post( $id ) { return $GLOBALS['tourfic_rest_posts'][ $id ] ?? null; }
	function get_post_field( $key, $id ) { return get_post( $id )->$key ?? ''; }
	function get_post_meta() { return array(); }
	function get_user_meta() { return ''; }
	function get_the_title( $id ) { return 'Fixture ' . $id; }
	function get_the_permalink( $id ) { return 'https://example.invalid/' . $id; }
	function site_url( $path = '' ) { return 'https://example.invalid/' . $path; }
	function esc_html__( $text, $domain = 'default' ) { return $text; }
	function __( $text, $domain ) { return $text; }
	function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES ); }
	function sanitize_key( $key ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) ); }
	function sanitize_text_field( $text ) { return trim( strip_tags( (string) $text ) ); }
	function absint( $value ) { return abs( (int) $value ); }
	function is_wp_error( $value ) { return $value instanceof WP_Error; }
	function wp_kses_post( $text ) { return $text; }
	function wp_date( $format, $time ) { return gmdate( $format, $time ); }
	function rest_ensure_response( $data ) { return $data; }
	function wc_price( $number ) { return '$' . $number; }
	function get_author_posts_url( $id ) { return 'https://example.invalid/author/' . $id; }
	function get_user_locale() { return 'en_US'; }
	function get_avatar_url() { return ''; }
	function is_super_admin() { return false; }
	function get_users( $args ) {
		$GLOBALS['tourfic_rest_user_queries'][] = $args;
		return array_values( array_filter( $GLOBALS['tourfic_rest_users'], function ( $user ) use ( $args ) {
			return empty( $args['role__in'] ) || array_intersect( $user->roles, $args['role__in'] );
			} ) );
	}

	require ABSPATH . 'inc/Classes/Helper.php';
	$tourfic_rest_pro = in_array( '--pro', $argv, true );
	if ( $tourfic_rest_pro ) {
		require __DIR__ . '/../fixtures/rest-pro-provider.php';
	} else {
		require ABSPATH . 'inc/Classes/REST_API/TF_Rest_API.php';
		require ABSPATH . 'inc/Classes/REST_API/TF_Booking_Rest_API.php';
		require ABSPATH . 'inc/Classes/REST_API/TF_Enquiry_Rest_API.php';
		require ABSPATH . 'inc/Classes/REST_API/TF_User_Rest_API.php';
	}
	$bookings = new Tourfic_Booking_Rest_API();
	$enquiries = new Tourfic_Enquiry_Rest_API();
	$users = new Tourfic_User_Rest_API();
	$wpdb->pdo->exec( 'CREATE TABLE wp_posts (ID INTEGER PRIMARY KEY, post_type TEXT, post_author INTEGER)' );
	$wpdb->pdo->exec( 'CREATE TABLE wp_tf_order_data (id INTEGER PRIMARY KEY, order_id INTEGER, post_id INTEGER, post_type TEXT, billing_details TEXT, order_details TEXT, order_date TEXT, checkinout TEXT, check_in TEXT, check_out TEXT, ostatus TEXT, customer_id INTEGER, payment_method TEXT)' );
	$wpdb->pdo->exec( 'CREATE TABLE wp_tf_enquiry_data (id INTEGER PRIMARY KEY, post_id INTEGER, post_type TEXT, author_id INTEGER, enquiry_status TEXT, created_at TEXT, reply_data TEXT)' );
	$wpdb->pdo->exec( 'CREATE TABLE wp_tf_vendor_balance_history (amount INTEGER, wstatus TEXT, vendor_id INTEGER)' );
	$wpdb->pdo->exec( 'CREATE TABLE wp_tf_vendor_balance (vendor_id INTEGER, total_amount INTEGER, total_withdraw INTEGER)' );
	foreach ( array( 17 => 'custom_agent', 40 => 'tf_vendor', 41 => 'administrator', 42 => 'subscriber' ) as $id => $role ) {
		$GLOBALS['tourfic_rest_users'][ $id ] = (object) array(
			'ID' => $id, 'roles' => array( $role ), 'user_login' => 'user' . $id, 'user_email' => 'u' . $id . '@example.invalid',
			'display_name' => 'User ' . $id, 'user_firstname' => 'User', 'user_lastname' => '', 'user_url' => '',
			'description' => '', 'user_nicename' => 'user' . $id, 'user_registered' => '2026-09-01 12:00:00',
			'allcaps' => array(), 'caps' => array(),
		);
	}
	$services = array(
		'hotel' => array( 'tf_hotel', 'edit_tf_hotels', 'edit_others_tf_hotels', 'hotel' ),
		'tour' => array( 'tf_tours', 'edit_tf_tourss', 'edit_others_tf_tourss', 'tour' ),
		'apartment' => array( 'tf_apartment', 'edit_tf_apartments', 'edit_others_tf_apartments', 'apartment' ),
		'car' => array( 'tf_carrental', 'edit_tf_carrentals', 'edit_others_tf_carrentals', 'car_rental' ),
	);
	$base = 100;
	foreach ( $services as $type => $service ) {
		list( $cpt, $cap, $others_cap, $view ) = $service;
		foreach ( array( 0 => 17, 1 => 40, 2 => 17 ) as $offset => $author ) {
			$post = array( 'ID' => $base + $offset, 'post_author' => $author, 'post_type' => 2 === $offset ? 'post' : $cpt );
			$GLOBALS['tourfic_rest_posts'][ $post['ID'] ] = (object) $post;
			$wpdb->insert_fixture( 'wp_posts', $post );
		}
		for ( $offset = 0; $offset < 4; ++$offset ) {
			$wpdb->insert_fixture( 'wp_tf_order_data', array(
				'id' => $base + $offset, 'order_id' => 1000 + $base + $offset, 'post_id' => $base + $offset,
				'post_type' => $type, 'billing_details' => '{"billing_email":"private@example.invalid"}',
				'order_details' => '{"check_in":"2026-09-10","check_out":"2026-09-11"}',
				'order_date' => '2026-09-01 12:00:00', 'checkinout' => 'not', 'check_in' => '2026-09-10',
				'check_out' => '2026-09-11', 'ostatus' => 1 === $offset ? 'pending' : 'completed',
				'customer_id' => 42, 'payment_method' => 'offline',
			) );
			$wpdb->insert_fixture( 'wp_tf_enquiry_data', array(
				'id' => $base + $offset, 'post_id' => 2 === $offset ? $base + 1 : $base + $offset,
				'post_type' => $cpt, 'author_id' => $offset >= 2 ? 17 : 40, 'enquiry_status' => 'unread',
				'created_at' => '2026-09-01 12:00:00', 'reply_data' => '[]',
			) );
		}
		$request = new WP_REST_Request( array( 'post_type' => $type ) );
		foreach ( array( 'administrator', 'tf_manager', 'tf_vendor' ) as $role ) {
			tourfic_rest_actor( array(), array( $role ) );
			tourfic_rest_error( $bookings->tf_order_permission_callback( $request ), 403, "$type: role alone cannot authorize route." );
			tourfic_rest_error( $bookings->tf_get_orders( $request ), 403, "$type: role alone cannot read collection directly." );
			tourfic_rest_assert( ! $wpdb->queries, "$type: denial must precede collection SQL." );
		}
		tourfic_rest_actor( array( $cap ) );
		tourfic_rest_assert( true === $bookings->tf_order_permission_callback( $request ), "$type: custom service capability is accepted." );
		$result = $bookings->tf_get_orders( $request );
		tourfic_rest_assert( array( $base ) === array_map( 'intval', array_column( $result['data'], 'id' ) ), "$type: owner sees only correct-type owned order." );
		tourfic_rest_assert( array( $base ) === array_map( 'intval', array_column( $result['events'], 'id' ) ), "$type: events cannot leak excluded orders." );
		tourfic_rest_assert( false !== strpos( end( $wpdb->queries ), 'post_author = 17' ), "$type: author scope is applied in SQL." );
		tourfic_rest_assert( $base === (int) $bookings->tf_get_order_details( new WP_REST_Request( array( 'id' => $base ) ) )['id'], "$type: owned detail works." );
		foreach ( array( $base + 1, $base + 2, $base + 3 ) as $denied_id ) {
			tourfic_rest_error( $bookings->tf_get_order_details( new WP_REST_Request( array( 'id' => $denied_id ) ) ), 403, "$type: foreign, wrong-CPT and orphan details fail closed." );
		}
		tourfic_rest_assert( ! $bookings->tf_get_orders( new WP_REST_Request( array( 'post_type' => $cpt, 'post_id' => $base + 1, 'user_id' => 40 ) ) )['data'], "$type: client post/user filter cannot escape author scope." );
		foreach ( array( 'tf_manager_options', 'manage_options', $others_cap ) as $all_cap ) {
			tourfic_rest_actor( array( $cap, $all_cap ) );
			tourfic_rest_assert( 4 === count( $bookings->tf_get_orders( $request )['data'] ), "$type: authorized cross-owner access retained." );
			tourfic_rest_assert( $base + 3 === (int) $bookings->tf_get_order_details( new WP_REST_Request( array( 'id' => $base + 3 ) ) )['id'], "$type: authorized manager can read historical orphan." );
			tourfic_rest_actor( array( $all_cap ) );
			tourfic_rest_error( $bookings->tf_get_orders( $request ), 403, "$type: removing service cap revokes cross-owner read." );
		}
		foreach ( array( 'manager' => 'tf_manager_options', 'vendor' => 'tf_vendor_options' ) as $group => $scope_cap ) {
			tourfic_rest_actor( array( $cap, $scope_cap ), array( 'tf_' . $group ) );
			$GLOBALS['tourfic_rest_settings']['tf_user_permission'] = array( $group . '_can_manage' => array() );
			tourfic_rest_error( $bookings->tf_get_orders( $request ), 403, "$type: saved empty view list denies reads." );
			tourfic_rest_error( $bookings->tf_get_order_details( new WP_REST_Request( array( 'id' => $base ) ) ), 403, "$type: saved view restriction also applies to detail." );
			$GLOBALS['tourfic_rest_settings']['tf_user_permission'][ $group . '_can_manage' ] = array( 'view_' . $view . '_booking' );
			tourfic_rest_assert( ( 'manager' === $group ? 4 : 1 ) === count( $bookings->tf_get_orders( $request )['data'] ), "$type: correct configured view restores intended scope." );
		}
		if ( 'car' !== $type ) {
			$enquiry_request = new WP_REST_Request( array( 'post_type' => $cpt ) );
			tourfic_rest_actor( array( $cap ) );
			$result = $enquiries->tf_get_enquiries( $enquiry_request );
			tourfic_rest_assert( array( $base + 3, $base + 2, $base ) === array_map( 'intval', array_column( $result, 'id' ) ), "$type: enquiry listing includes assigned and currently owned records only." );
			foreach ( $result as $enquiry ) {
				tourfic_rest_assert( $enquiry['id'] === $enquiries->tf_get_enquiry_details( new WP_REST_Request( array( 'id' => $enquiry['id'] ) ) )['id'], "$type: enquiry collection/detail policy agrees." );
			}
			tourfic_rest_error( $enquiries->tf_get_enquiry_details( new WP_REST_Request( array( 'id' => $base + 1 ) ) ), 403, "$type: foreign unassigned enquiry denied." );
			tourfic_rest_actor( array( $cap, 'tf_manager_options' ) );
			tourfic_rest_assert( 4 === count( $enquiries->tf_get_enquiries( $enquiry_request ) ), "$type: manager enquiry scope retained." );
			$GLOBALS['tourfic_rest_settings']['tf_user_permission'] = array( 'manager_can_manage' => array( 'view_' . $view . '_booking' ) );
			tourfic_rest_error( $enquiries->tf_get_enquiries( $enquiry_request ), 403, "$type: booking view cannot grant enquiry view." );
			tourfic_rest_actor( array( 'tf_manager_options' ) );
			tourfic_rest_error( $enquiries->tf_get_enquiries( $enquiry_request ), 403, "$type: enquiry requires service cap." );
		}
		$base += 10;
	}

	tourfic_rest_actor( array( 'edit_tf_hotels' ) );
	tourfic_rest_error( $bookings->tf_get_orders( new WP_REST_Request( array( 'post_type' => 'tour' ) ) ), 403, 'Hotel rights do not grant tour collection access.' );
	tourfic_rest_error( $bookings->tf_get_order_details( new WP_REST_Request( array( 'id' => 110, 'post_type' => 'hotel' ) ) ), 403, 'A spoofed type cannot grant access to a stored tour record.' );
	tourfic_rest_error( $bookings->tf_get_order_details( new WP_REST_Request( array( 'id' => 9999 ) ) ), 404, 'Missing order retains 404 response.' );
	tourfic_rest_error( $enquiries->tf_get_enquiry_details( new WP_REST_Request( array( 'id' => 9999 ) ) ), 404, 'Missing enquiry retains 404 response.' );
	foreach ( array( null, array( 'hotel' ), 'post', 'hotel OR 1=1' ) as $type ) {
		tourfic_rest_error( $bookings->tf_get_orders( new WP_REST_Request( array( 'post_type' => $type ) ) ), 403, 'Unsupported collection type fails closed.' );
	}
	tourfic_rest_error( $bookings->tf_get_orders( new WP_REST_Request( array( 'post_type' => 'hotel', 'post_id' => array( 100 ) ) ) ), 400, 'Malformed filter remains rejected.' );
	tourfic_rest_assert( 1 === count( $bookings->tf_get_orders( new WP_REST_Request( array( 'post_type' => 'hotel', 'order_status' => 'completed', 'checkinout' => 'not' ) ) )['data'] ), 'Allowed filters remain functional with owner scope.' );
	tourfic_rest_actor( array( 'edit_tf_carrentals', 'manage_options' ) );
	tourfic_rest_error( $enquiries->tf_get_enquiries( new WP_REST_Request( array( 'post_type' => 'tf_carrental' ) ) ), 403, 'No unintended car enquiry route is enabled.' );
	tourfic_rest_actor( array( 'edit_tf_hotels', 'manage_options' ), array(), 0 );
	tourfic_rest_error( $bookings->tf_get_orders( new WP_REST_Request( array( 'post_type' => 'hotel' ) ) ), 403, 'Guest denied even with stray capability fixture.' );
	tourfic_rest_error( $users->tf_get_users( new WP_REST_Request() ), 403, 'Guest cannot read user directory.' );

	foreach ( array( 'administrator', 'tf_manager', 'tf_vendor' ) as $role ) {
		tourfic_rest_actor( array(), array( $role ) );
		tourfic_rest_error( $users->tf_get_users( new WP_REST_Request() ), 403, 'Role alone does not grant user directory.' );
		tourfic_rest_assert( ! $GLOBALS['tourfic_rest_user_queries'], 'Denied directory performs no query.' );
		tourfic_rest_error( $users->tf_get_user( new WP_REST_Request( array( 'id' => 41 ) ) ), 403, 'Role alone cannot read unrelated profile.' );
		tourfic_rest_assert( 17 === $users->tf_get_user( new WP_REST_Request( array( 'id' => 17 ) ) )['id'], 'Own profile remains readable.' );
	}
	tourfic_rest_actor( array( 'manage_vendors', 'tf_manager_options' ) );
	tourfic_rest_assert( array( 40 ) === array_column( $users->tf_get_users( new WP_REST_Request() ), 'id' ), 'Vendor manager defaults to vendor-only directory.' );
	tourfic_rest_assert( array( 40 ) === array_column( $users->tf_get_users( new WP_REST_Request( array( 'roles' => 'tf_vendor' ) ) ), 'id' ), 'Existing Pro comma-string role request works.' );
	tourfic_rest_assert( 40 === $users->tf_get_user( new WP_REST_Request( array( 'id' => 40 ) ) )['id'], 'Vendor manager can read vendor detail.' );
	tourfic_rest_error( $users->tf_get_user( new WP_REST_Request( array( 'id' => 41 ) ) ), 403, 'Vendor capability cannot read administrator profile.' );
	foreach ( array( 'administrator', 'tf_vendor,subscriber', array( 'tf_vendor', 'administrator' ) ) as $roles ) {
		tourfic_rest_error( $users->tf_get_users( new WP_REST_Request( array( 'roles' => $roles ) ) ), 403, 'Vendor reader cannot broaden requested roles.' );
	}
	foreach ( array( 42, array( array( 'tf_vendor' ) ), '<b>tf_vendor</b>' ) as $roles ) {
		tourfic_rest_error( $users->tf_get_users( new WP_REST_Request( array( 'roles' => $roles ) ) ), 400, 'Malformed user role payload rejected.' );
	}
	$GLOBALS['tourfic_rest_settings']['tf_user_permission'] = array( 'manager_can_manage' => array() );
	tourfic_rest_error( $users->tf_get_users( new WP_REST_Request() ), 403, 'Saved vendor view restriction enforced.' );
	tourfic_rest_error( $users->tf_get_user( new WP_REST_Request( array( 'id' => 40 ) ) ), 403, 'Saved vendor view restriction also enforced on detail.' );
	tourfic_rest_actor( array( 'tf_manager_options' ) );
	tourfic_rest_error( $users->tf_get_users( new WP_REST_Request() ), 403, 'Removing manage_vendors revokes vendor directory.' );
	tourfic_rest_actor( array( 'list_users' ) );
	tourfic_rest_assert( 4 === count( $users->tf_get_users( new WP_REST_Request() ) ), 'Custom list_users capability supports full directory.' );
	tourfic_rest_assert( array( 40, 42 ) === array_column( $users->tf_get_users( new WP_REST_Request( array( 'roles' => 'tf_vendor,subscriber' ) ) ), 'id' ), 'General directory supports comma-separated role filter.' );
	tourfic_rest_actor( array( 'edit_user:42' ) );
	tourfic_rest_assert( 42 === $users->tf_get_user( new WP_REST_Request( array( 'id' => 42 ) ) )['id'], 'Explicit target edit capability supports detail.' );
	tourfic_rest_error( $users->tf_get_user( new WP_REST_Request( array( 'id' => 41 ) ) ), 403, 'Target edit capability cannot reach a different user.' );

	// The generic query helper must remain unchanged when author scope is absent.
	$all = \Tourfic\Classes\Helper::tourfic_order_table_data( array( 'select' => 'id', 'post_type' => 'hotel', 'orderby' => 'id', 'order' => 'ASC', 'limit' => array( 'offset' => 1, 'per_page' => 2 ) ) );
	tourfic_rest_assert( array( 101, 102 ) === array_map( 'intval', array_column( $all, 'id' ) ), 'Existing unrestricted helper sorting and pagination unchanged.' );
	$none = \Tourfic\Classes\Helper::tourfic_order_table_data( array( 'select' => '*', 'post_type' => 'hotel', 'post_author' => 17 ) );
	tourfic_rest_assert( array() === $none, 'Incomplete author scope fails closed instead of dropping the constraint.' );
	if ( $tourfic_rest_pro ) {
		tourfic_rest_verify_pro_contracts( $bookings, $users );
	} else {
		tourfic_rest_assert( ! function_exists( 'tourfic_vendor_order_table_data' ), 'All owner queries succeeded without loading the Pro query provider.' );
	}
	echo 'PASS: ' . $GLOBALS['tourfic_rest_tests'] . ' ' . ( $tourfic_rest_pro ? 'Pro' : 'Free' ) . " REST permission assertions (isolated handlers + in-memory SQL).\n";
}
