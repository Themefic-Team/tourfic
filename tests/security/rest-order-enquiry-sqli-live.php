<?php
/**
 * Live regression matrix for REST order/enquiry SQL injection and authorization.
 *
 * Run from the Tourfic Free plugin root:
 * wp eval-file tests/security/rest-order-enquiry-sqli-live.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "This check must run inside WordPress.\n" );
	exit( 1 );
}

class TF_REST_Security_Matrix_Failure extends RuntimeException {}

function tf_rest_security_matrix_assert( $condition, $message ) {
	if ( ! $condition ) {
		throw new TF_REST_Security_Matrix_Failure( $message );
	}
}

function tf_rest_security_matrix_request( $method, $route, $params = array() ) {
	$request = new WP_REST_Request( $method, $route );

	foreach ( $params as $key => $value ) {
		$request->set_param( $key, $value );
	}

	return rest_do_request( $request );
}

function tf_rest_security_matrix_status( $response ) {
	if ( is_wp_error( $response ) ) {
		return absint( $response->get_error_data()['status'] ?? 500 );
	}

	return absint( $response->get_status() );
}

function tf_rest_security_matrix_data( $response ) {
	return is_wp_error( $response ) ? $response->get_error_data() : $response->get_data();
}

function tf_rest_security_matrix_assert_status( $response, $expected, $message ) {
	$actual = tf_rest_security_matrix_status( $response );

	tf_rest_security_matrix_assert(
		$expected === $actual,
		$message . " Expected HTTP {$expected}, got HTTP {$actual}."
	);
}

function tf_rest_security_matrix_assert_safe_error( $response, $message ) {
	$data       = strtolower( wp_json_encode( tf_rest_security_matrix_data( $response ) ) );
	$signatures = array(
		'tf_order_data',
		'tf_enquiry_data',
		'sql syntax',
		'mysqli',
		'password_hash',
		'user_pass',
	);

	foreach ( $signatures as $signature ) {
		tf_rest_security_matrix_assert(
			false === strpos( $data, $signature ),
			$message . " Response exposed the internal signature '{$signature}'."
		);
	}
}

function tf_rest_security_matrix_order_rows( $response ) {
	$data = tf_rest_security_matrix_data( $response );

	if ( isset( $data['data'] ) && is_array( $data['data'] ) ) {
		return $data['data'];
	}

	return is_array( $data ) ? $data : array();
}

function tf_rest_security_matrix_has_row( $rows, $row_id ) {
	foreach ( $rows as $row ) {
		if ( absint( $row['id'] ?? 0 ) === absint( $row_id ) ) {
			return true;
		}
	}

	return false;
}

function tf_rest_security_matrix_insert_order( $data ) {
	global $wpdb;

	$inserted = $wpdb->insert(
		$wpdb->prefix . 'tf_order_data',
		array(
			'order_id'        => $data['order_id'],
			'post_id'         => $data['post_id'],
			'post_type'       => 'hotel',
			'room_number'     => '1',
			'check_in'        => '2026-08-10',
			'check_out'       => '2026-08-12',
			'billing_details' => wp_json_encode(
				array(
					'billing_first_name' => 'Security',
					'billing_last_name'  => 'Fixture',
					'billing_email'      => 'security-fixture@example.com',
				)
			),
			'shipping_details' => '{}',
			'order_details'    => wp_json_encode(
				array(
					'total_price' => 100,
					'adult'       => '1 × 100',
				)
			),
			'customer_id'     => $data['customer_id'],
			'payment_method'  => 'cod',
			'ostatus'         => $data['status'],
			'order_date'      => current_time( 'mysql' ),
			'checkinout'      => $data['checkinout'],
		),
		array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s' )
	);

	tf_rest_security_matrix_assert( false !== $inserted, 'Could not create an order fixture.' );

	return absint( $wpdb->insert_id );
}

function tf_rest_security_matrix_insert_enquiry( $data ) {
	global $wpdb;

	$inserted = $wpdb->insert(
		$wpdb->prefix . 'tf_enquiry_data',
		array(
			'post_id'        => $data['post_id'],
			'post_type'      => 'tf_hotel',
			'uname'          => 'Security Fixture',
			'uemail'         => 'security-fixture@example.com',
			'udescription'   => 'Authorized local security regression fixture.',
			'author_id'      => $data['author_id'],
			'author_roles'   => $data['author_role'],
			'enquiry_status' => $data['status'],
			'server_data'    => '{}',
			'reply_data'     => '',
			'created_at'     => current_time( 'mysql' ),
		),
		array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s' )
	);

	tf_rest_security_matrix_assert( false !== $inserted, 'Could not create an enquiry fixture.' );

	return absint( $wpdb->insert_id );
}

function tf_rest_security_matrix_insert_payout( $vendor_id ) {
	global $wpdb;

	$inserted = $wpdb->insert(
		$wpdb->prefix . 'tf_vendor_withdraw',
		array(
			'vendor_id'      => $vendor_id,
			'amount'         => 10,
			'payment_method' => 'bank',
			'note'           => 'Authorized local security regression fixture.',
			'wstatus'        => 'pending',
			'udate'          => current_time( 'Y-m-d' ),
			'rdate'          => '',
		),
		array( '%d', '%f', '%s', '%s', '%s', '%s', '%s' )
	);

	tf_rest_security_matrix_assert( false !== $inserted, 'Could not create a payout fixture.' );

	return absint( $wpdb->insert_id );
}

function tf_rest_security_matrix_cleanup( $fixtures ) {
	global $wpdb;

	wp_set_current_user( 0 );

	foreach ( $fixtures['payouts'] as $payout_id ) {
		$wpdb->delete( $wpdb->prefix . 'tf_vendor_withdraw', array( 'id' => absint( $payout_id ) ), array( '%d' ) );
	}

	foreach ( $fixtures['orders'] as $order_id ) {
		$wpdb->delete( $wpdb->prefix . 'tf_order_data', array( 'id' => absint( $order_id ) ), array( '%d' ) );
	}

	foreach ( $fixtures['enquiries'] as $enquiry_id ) {
		$wpdb->delete( $wpdb->prefix . 'tf_enquiry_data', array( 'id' => absint( $enquiry_id ) ), array( '%d' ) );
	}

	foreach ( $fixtures['posts'] as $post_id ) {
		wp_delete_post( absint( $post_id ), true );
	}

	if ( ! function_exists( 'wp_delete_user' ) ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';
	}

	foreach ( $fixtures['users'] as $user_id ) {
		wp_delete_user( absint( $user_id ) );
	}
}

global $wpdb;

$fixtures = array(
	'users'     => array(),
	'posts'     => array(),
	'orders'    => array(),
	'enquiries' => array(),
	'payouts'   => array(),
);
$failure  = null;

try {
	rest_get_server();

	tf_rest_security_matrix_assert( null !== get_role( 'tf_vendor' ), 'The tf_vendor role is required for ownership checks.' );
	tf_rest_security_matrix_assert( null !== get_role( 'tf_manager' ), 'The tf_manager role is required for capability checks.' );
	tf_rest_security_matrix_assert( null !== get_role( 'customer' ), 'The WooCommerce customer role is required.' );

	$token = strtolower( wp_generate_password( 10, false, false ) );
	$users = array(
		'admin'      => array( 'role' => 'administrator' ),
		'manager'    => array( 'role' => 'tf_manager' ),
		'subscriber' => array( 'role' => 'subscriber' ),
		'vendor'     => array( 'role' => 'tf_vendor' ),
	);

	foreach ( $users as $key => $user_data ) {
		$user_id = wp_insert_user(
			array(
				'user_login' => "tf_rest_security_{$key}_{$token}",
				'user_email' => "tf-rest-security-{$key}-{$token}@example.com",
				'user_pass'  => wp_generate_password( 32, true, true ),
				'role'       => $user_data['role'],
			)
		);

		tf_rest_security_matrix_assert( ! is_wp_error( $user_id ), "Could not create the {$key} fixture user." );
		$users[ $key ]['id']   = absint( $user_id );
		$fixtures['users'][] = absint( $user_id );
	}

	$subscriber_user = new WP_User( $users['subscriber']['id'] );
	$subscriber_user->add_role( 'customer' );

	$admin_post_id = wp_insert_post(
		array(
			'post_type'   => 'tf_hotel',
			'post_status' => 'publish',
			'post_title'  => "REST Security Admin {$token}",
			'post_author' => $users['admin']['id'],
		),
		true
	);
	tf_rest_security_matrix_assert( ! is_wp_error( $admin_post_id ), 'Could not create the administrator hotel fixture.' );
	$fixtures['posts'][] = absint( $admin_post_id );

	$vendor_post_id = wp_insert_post(
		array(
			'post_type'   => 'tf_hotel',
			'post_status' => 'publish',
			'post_title'  => "REST Security Vendor {$token}",
			'post_author' => $users['vendor']['id'],
		),
		true
	);
	tf_rest_security_matrix_assert( ! is_wp_error( $vendor_post_id ), 'Could not create the vendor hotel fixture.' );
	$fixtures['posts'][] = absint( $vendor_post_id );

	$subscriber_order_id = tf_rest_security_matrix_insert_order(
		array(
			'order_id'    => wp_rand( 70000000, 79999999 ),
			'post_id'     => $vendor_post_id,
			'customer_id' => $users['subscriber']['id'],
			'status'      => 'completed',
			'checkinout'  => 'in',
		)
	);
	$fixtures['orders'][] = $subscriber_order_id;

	$admin_order_id = tf_rest_security_matrix_insert_order(
		array(
			'order_id'    => wp_rand( 80000000, 89999999 ),
			'post_id'     => $admin_post_id,
			'customer_id' => $users['admin']['id'],
			'status'      => 'pending',
			'checkinout'  => 'out',
		)
	);
	$fixtures['orders'][] = $admin_order_id;

	$vendor_enquiry_id = tf_rest_security_matrix_insert_enquiry(
		array(
			'post_id'     => $vendor_post_id,
			'author_id'   => $users['vendor']['id'],
			'author_role' => 'tf_vendor',
			'status'      => 'unread',
		)
	);
	$fixtures['enquiries'][] = $vendor_enquiry_id;

	$admin_enquiry_id = tf_rest_security_matrix_insert_enquiry(
		array(
			'post_id'     => $admin_post_id,
			'author_id'   => $users['admin']['id'],
			'author_role' => 'administrator',
			'status'      => 'read',
		)
	);
	$fixtures['enquiries'][] = $admin_enquiry_id;

	$payout_id = tf_rest_security_matrix_insert_payout( $users['vendor']['id'] );
	$fixtures['payouts'][] = $payout_id;

	$free_rest_files = array(
		'TF_Rest_API'         => WP_PLUGIN_DIR . '/tourfic/inc/Classes/REST_API/TF_Rest_API.php',
		'TF_Booking_Rest_API' => WP_PLUGIN_DIR . '/tourfic/inc/Classes/REST_API/TF_Booking_Rest_API.php',
		'TF_Enquiry_Rest_API' => WP_PLUGIN_DIR . '/tourfic/inc/Classes/REST_API/TF_Enquiry_Rest_API.php',
	);
	foreach ( $free_rest_files as $class => $file ) {
		if ( ! class_exists( $class ) && is_readable( $file ) ) {
			require_once $file;
		}
	}

	tf_rest_security_matrix_assert(
		class_exists( 'TF_Booking_Rest_API' ) && class_exists( 'TF_Enquiry_Rest_API' ),
		'Free order and enquiry handlers must be available for direct validation.'
	);

	wp_set_current_user( $users['admin']['id'] );
	$free_order_request = new WP_REST_Request( 'GET', '/tf/v1/orders' );
	$free_order_request->set_param( 'post_type', 'hotel' );
	$free_order_request->set_param( 'post_id', '1.5' );
	$free_order_result = TF_Booking_Rest_API::get_instance()->tf_get_orders( $free_order_request );
	tf_rest_security_matrix_assert(
		is_wp_error( $free_order_result ) && 400 === absint( $free_order_result->get_error_data()['status'] ?? 0 ),
		'Free order handler must reject a decimal post_id before querying.'
	);

	$free_order_request->set_param( 'post_id', $vendor_post_id );
	$free_order_request->set_param( 'checkinout', 'in' );
	$free_order_request->set_param( 'order_status', 'completed' );
	$free_order_result = TF_Booking_Rest_API::get_instance()->tf_get_orders( $free_order_request );
	tf_rest_security_matrix_assert(
		is_array( $free_order_result )
			&& tf_rest_security_matrix_has_row( $free_order_result['data'] ?? array(), $subscriber_order_id ),
		'Free order handler must preserve valid structured filtering.'
	);

	$free_enquiry_request = new WP_REST_Request( 'GET', '/tf/v1/enquiries' );
	$free_enquiry_request->set_param( 'post_type', 'tf_hotel' );
	$free_enquiry_request->set_param( 'filters', "unread' UNION SELECT 1--" );
	$free_enquiry_result = TF_Enquiry_Rest_API::get_instance()->tf_get_enquiries( $free_enquiry_request );
	tf_rest_security_matrix_assert(
		is_wp_error( $free_enquiry_result ) && 400 === absint( $free_enquiry_result->get_error_data()['status'] ?? 0 ),
		'Free enquiry handler must reject SQL metacharacters before querying.'
	);

	$free_enquiry_request->set_param( 'post_id', $vendor_post_id );
	$free_enquiry_request->set_param( 'filters', 'unread' );
	$free_enquiry_result = TF_Enquiry_Rest_API::get_instance()->tf_get_enquiries( $free_enquiry_request );
	tf_rest_security_matrix_assert(
		is_array( $free_enquiry_result ) && tf_rest_security_matrix_has_row( $free_enquiry_result, $vendor_enquiry_id ),
		'Free enquiry handler must preserve valid prepared filtering.'
	);

	wp_set_current_user( 0 );
	foreach ( array( '/tf/v1/orders', '/tf/v1/enquiries', '/tf/v1/user-bookings' ) as $route ) {
		$params = array();
		if ( '/tf/v1/orders' === $route ) {
			$params['post_type'] = 'hotel';
		} elseif ( '/tf/v1/enquiries' === $route ) {
			$params['post_type'] = 'tf_hotel';
		}

		$response = tf_rest_security_matrix_request( 'GET', $route, $params );
		tf_rest_security_matrix_assert_status( $response, 403, "Unauthenticated {$route} request must be denied." );
		tf_rest_security_matrix_assert_safe_error( $response, "Unauthenticated {$route} request" );
	}

	wp_set_current_user( $users['subscriber']['id'] );
	tf_rest_security_matrix_assert_status(
		tf_rest_security_matrix_request( 'GET', '/tf/v1/user/' . $users['subscriber']['id'] ),
		200,
		'Subscriber must read their own user record.'
	);
	tf_rest_security_matrix_assert_status(
		tf_rest_security_matrix_request( 'GET', '/tf/v1/user/' . $users['admin']['id'] ),
		403,
		'Subscriber must not read an administrator user record.'
	);
	tf_rest_security_matrix_assert_status(
		tf_rest_security_matrix_request(
			'GET',
			'/tf/v1/orders',
			array(
				'user_id'   => $users['admin']['id'],
				'post_type' => 'hotel',
			)
		),
		403,
		'Subscriber must not use an administrator user_id to access orders.'
	);
	tf_rest_security_matrix_assert_status(
		tf_rest_security_matrix_request(
			'GET',
			'/tf/v1/enquiries',
			array(
				'user_id'   => $users['admin']['id'],
				'post_type' => 'tf_hotel',
			)
		),
		403,
		'Subscriber must not use an administrator user_id to access enquiries.'
	);

	$subscriber_bookings = tf_rest_security_matrix_request(
		'GET',
		'/tf/v1/user-bookings',
		array(
			'user_id'      => $users['admin']['id'],
			'booking_type' => 'hotel',
		)
	);
	tf_rest_security_matrix_assert_status( $subscriber_bookings, 200, 'Subscriber must retain access to their own bookings.' );
	$subscriber_rows = tf_rest_security_matrix_order_rows( $subscriber_bookings );
	tf_rest_security_matrix_assert(
		tf_rest_security_matrix_has_row( $subscriber_rows, $subscriber_order_id ),
		'Subscriber bookings must include the subscriber fixture.'
	);
	tf_rest_security_matrix_assert(
		! tf_rest_security_matrix_has_row( $subscriber_rows, $admin_order_id ),
		'Subscriber bookings must ignore an attacker-supplied administrator user_id.'
	);

	wp_set_current_user( $users['vendor']['id'] );
	$vendor_orders = tf_rest_security_matrix_request(
		'GET',
		'/tf/v1/orders',
		array(
			'user_id'     => $users['admin']['id'],
			'post_type'   => 'hotel',
			'post_id'     => $vendor_post_id,
			'checkinout'  => 'in',
			'order_status' => 'completed',
		)
	);
	tf_rest_security_matrix_assert_status( $vendor_orders, 200, 'Vendor valid order filtering must succeed.' );
	$vendor_order_rows = tf_rest_security_matrix_order_rows( $vendor_orders );
	tf_rest_security_matrix_assert(
		tf_rest_security_matrix_has_row( $vendor_order_rows, $subscriber_order_id ),
		'Vendor order filtering must return the vendor-owned fixture.'
	);
	tf_rest_security_matrix_assert(
		! tf_rest_security_matrix_has_row( $vendor_order_rows, $admin_order_id ),
		'Vendor order filtering must not return another author’s fixture.'
	);

	$vendor_other_orders = tf_rest_security_matrix_request(
		'GET',
		'/tf/v1/orders',
		array(
			'post_type' => 'hotel',
			'post_id'   => $admin_post_id,
		)
	);
	tf_rest_security_matrix_assert_status( $vendor_other_orders, 200, 'Vendor out-of-scope order filtering must fail closed.' );
	tf_rest_security_matrix_assert(
		! tf_rest_security_matrix_has_row( tf_rest_security_matrix_order_rows( $vendor_other_orders ), $admin_order_id ),
		'Vendor collection must not return another author’s order.'
	);

	tf_rest_security_matrix_assert_status(
		tf_rest_security_matrix_request( 'GET', '/tf/v1/order/' . $subscriber_order_id ),
		200,
		'Vendor must read a vendor-owned order detail.'
	);
	tf_rest_security_matrix_assert_status(
		tf_rest_security_matrix_request( 'GET', '/tf/v1/order/' . $admin_order_id ),
		403,
		'Vendor must not read another author’s order detail.'
	);
	tf_rest_security_matrix_assert_status(
		tf_rest_security_matrix_request(
			'POST',
			'/tf/v1/update-order-status/' . $admin_order_id,
			array( 'order_status' => 'completed' )
		),
		403,
		'Vendor must not update another author’s order status.'
	);

	$vendor_enquiries = tf_rest_security_matrix_request(
		'GET',
		'/tf/v1/enquiries',
		array(
			'user_id'   => $users['admin']['id'],
			'post_type' => 'tf_hotel',
			'post_id'   => $vendor_post_id,
			'filters'   => 'unread',
		)
	);
	tf_rest_security_matrix_assert_status( $vendor_enquiries, 200, 'Vendor valid enquiry filtering must succeed.' );
	$vendor_enquiry_rows = tf_rest_security_matrix_data( $vendor_enquiries );
	tf_rest_security_matrix_assert(
		tf_rest_security_matrix_has_row( $vendor_enquiry_rows, $vendor_enquiry_id ),
		'Vendor enquiry filtering must return the vendor-owned fixture.'
	);
	tf_rest_security_matrix_assert_status(
		tf_rest_security_matrix_request( 'GET', '/tf/v1/enquiries/' . $vendor_enquiry_id ),
		200,
		'Vendor must read a vendor-owned enquiry detail.'
	);
	tf_rest_security_matrix_assert_status(
		tf_rest_security_matrix_request( 'GET', '/tf/v1/enquiries/' . $admin_enquiry_id ),
		403,
		'Vendor must not read another author’s enquiry detail.'
	);

	$vendor_user = new WP_User( $users['vendor']['id'] );
	$vendor_user->add_cap( 'tf_vendor_options', false );
	wp_set_current_user( 0 );
	wp_set_current_user( $users['vendor']['id'] );
	tf_rest_security_matrix_assert_status(
		tf_rest_security_matrix_request( 'GET', '/tf/v1/orders', array( 'post_type' => 'hotel' ) ),
		403,
		'A vendor with a revoked dashboard capability must be denied.'
	);
	$vendor_user->remove_cap( 'tf_vendor_options' );
	wp_set_current_user( 0 );

	wp_set_current_user( $users['manager']['id'] );
	$manager_orders = tf_rest_security_matrix_request( 'GET', '/tf/v1/orders', array( 'post_type' => 'hotel' ) );
	tf_rest_security_matrix_assert_status( $manager_orders, 200, 'Manager order access must remain available.' );
	tf_rest_security_matrix_assert(
		tf_rest_security_matrix_has_row( tf_rest_security_matrix_order_rows( $manager_orders ), $admin_order_id ),
		'Manager order access must include records managed through the dashboard.'
	);

	$manager_user = new WP_User( $users['manager']['id'] );
	$manager_user->add_cap( 'tf_manager_options', false );
	wp_set_current_user( 0 );
	wp_set_current_user( $users['manager']['id'] );
	tf_rest_security_matrix_assert_status(
		tf_rest_security_matrix_request( 'GET', '/tf/v1/orders', array( 'post_type' => 'hotel' ) ),
		403,
		'A manager with a revoked dashboard capability must be denied.'
	);
	$manager_user->remove_cap( 'tf_manager_options' );
	wp_set_current_user( 0 );

	wp_set_current_user( $users['admin']['id'] );
	$admin_orders = tf_rest_security_matrix_request(
		'GET',
		'/tf/v1/orders',
		array(
			'post_type'   => 'hotel',
			'post_id'     => $vendor_post_id,
			'checkinout'  => 'in',
			'order_status' => 'completed',
		)
	);
	tf_rest_security_matrix_assert_status( $admin_orders, 200, 'Administrator valid order filtering must succeed.' );
	tf_rest_security_matrix_assert(
		tf_rest_security_matrix_has_row( tf_rest_security_matrix_order_rows( $admin_orders ), $subscriber_order_id ),
		'Administrator valid order filtering must return the matching fixture.'
	);

	$admin_enquiries = tf_rest_security_matrix_request(
		'GET',
		'/tf/v1/enquiries',
		array(
			'post_type' => 'tf_hotel',
			'post_id'   => $vendor_post_id,
			'filters'   => 'unread',
		)
	);
	tf_rest_security_matrix_assert_status( $admin_enquiries, 200, 'Administrator valid enquiry filtering must succeed.' );
	tf_rest_security_matrix_assert(
		tf_rest_security_matrix_has_row( tf_rest_security_matrix_data( $admin_enquiries ), $vendor_enquiry_id ),
		'Administrator valid enquiry filtering must return the matching fixture.'
	);

	$valid_status_update = tf_rest_security_matrix_request(
		'POST',
		'/tf/v1/update-order-status/' . $admin_order_id,
		array( 'order_status' => 'completed' )
	);
	tf_rest_security_matrix_assert_status( $valid_status_update, 200, 'A valid order status update must remain available.' );
	tf_rest_security_matrix_assert(
		'completed' === $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ostatus FROM {$wpdb->prefix}tf_order_data WHERE id = %d",
				$admin_order_id
			)
		),
		'A valid order status update must persist the allowlisted value.'
	);
	$wpdb->update(
		$wpdb->prefix . 'tf_order_data',
		array( 'ostatus' => 'pending' ),
		array( 'id' => $admin_order_id ),
		array( '%s' ),
		array( '%d' )
	);

	$invalid_status_update = tf_rest_security_matrix_request(
		'POST',
		'/tf/v1/update-order-status/' . $admin_order_id,
		array( 'order_status' => "completed' OR '1'='1" )
	);
	tf_rest_security_matrix_assert_status( $invalid_status_update, 400, 'Malformed order status updates must be rejected.' );
	tf_rest_security_matrix_assert_safe_error( $invalid_status_update, 'Malformed order status update' );
	tf_rest_security_matrix_assert(
		'pending' === $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ostatus FROM {$wpdb->prefix}tf_order_data WHERE id = %d",
				$admin_order_id
			)
		),
		'Malformed order status updates must not change the database record.'
	);

	$valid_payout_update = tf_rest_security_matrix_request(
		'POST',
		'/tf/v1/update-payout-status/' . $payout_id,
		array( 'payment_status' => 'decline' )
	);
	tf_rest_security_matrix_assert_status( $valid_payout_update, 200, 'A valid payout status update must remain available.' );
	tf_rest_security_matrix_assert(
		'decline' === $wpdb->get_var(
			$wpdb->prepare(
				"SELECT wstatus FROM {$wpdb->prefix}tf_vendor_withdraw WHERE id = %d",
				$payout_id
			)
		),
		'A valid payout status update must persist the allowlisted value.'
	);

	$invalid_payout_update = tf_rest_security_matrix_request(
		'POST',
		'/tf/v1/update-payout-status/' . $payout_id,
		array( 'payment_status' => "completed' OR '1'='1" )
	);
	tf_rest_security_matrix_assert_status( $invalid_payout_update, 400, 'Malformed payout status updates must be rejected.' );
	tf_rest_security_matrix_assert_safe_error( $invalid_payout_update, 'Malformed payout status update' );
	tf_rest_security_matrix_assert(
		'decline' === $wpdb->get_var(
			$wpdb->prepare(
				"SELECT wstatus FROM {$wpdb->prefix}tf_vendor_withdraw WHERE id = %d",
				$payout_id
			)
		),
		'Malformed payout status updates must not change the database record.'
	);

	$invalid_order_params = array(
		array(),
		array( 'post_type' => '' ),
		array( 'post_type' => 'product' ),
		array( 'post_type' => "hotel' UNION SELECT 1--" ),
		array( 'post_type' => array( 'hotel', 'tour' ) ),
		array( 'post_type' => 'hotel', 'post_id' => '-1' ),
		array( 'post_type' => 'hotel', 'post_id' => '1.5' ),
		array( 'post_type' => 'hotel', 'post_id' => '1e2' ),
		array( 'post_type' => 'hotel', 'post_id' => "1' OR '1'='1" ),
		array( 'post_type' => 'hotel', 'post_id' => array( 1, 2 ) ),
		array( 'post_type' => 'hotel', 'order_id' => '-1' ),
		array( 'post_type' => 'hotel', 'checkinout' => "in' UNION SELECT 1--" ),
		array( 'post_type' => 'hotel', 'order_status' => 'private' ),
		array( 'post_type' => 'hotel', 'order_status' => "completed' OR '1'='1" ),
	);

	foreach ( $invalid_order_params as $index => $params ) {
		$response = tf_rest_security_matrix_request( 'GET', '/tf/v1/orders', $params );
		tf_rest_security_matrix_assert_status( $response, 400, "Invalid order parameter case {$index} must be rejected." );
		tf_rest_security_matrix_assert_safe_error( $response, "Invalid order parameter case {$index}" );
	}

	$invalid_enquiry_params = array(
		array(),
		array( 'post_type' => '' ),
		array( 'post_type' => 'tf_carrental' ),
		array( 'post_type' => "tf_hotel' UNION SELECT 1--" ),
		array( 'post_type' => array( 'tf_hotel', 'tf_tours' ) ),
		array( 'post_type' => 'tf_hotel', 'post_id' => '-1' ),
		array( 'post_type' => 'tf_hotel', 'post_id' => '1.5' ),
		array( 'post_type' => 'tf_hotel', 'post_id' => "1' OR '1'='1" ),
		array( 'post_type' => 'tf_hotel', 'post_id' => array( 1, 2 ) ),
		array( 'post_type' => 'tf_hotel', 'filters' => 'private' ),
		array( 'post_type' => 'tf_hotel', 'filters' => "unread' UNION SELECT 1--" ),
		array( 'post_type' => 'tf_hotel', 'filters' => array( 'unread', 'read' ) ),
	);

	foreach ( $invalid_enquiry_params as $index => $params ) {
		$response = tf_rest_security_matrix_request( 'GET', '/tf/v1/enquiries', $params );
		tf_rest_security_matrix_assert_status( $response, 400, "Invalid enquiry parameter case {$index} must be rejected." );
		tf_rest_security_matrix_assert_safe_error( $response, "Invalid enquiry parameter case {$index}" );
	}

	foreach ( array( '/tf/v1/order/0', '/tf/v1/enquiries/0' ) as $route ) {
		$response = tf_rest_security_matrix_request( 'GET', $route );
		tf_rest_security_matrix_assert_status( $response, 400, "Zero object ID for {$route} must be rejected." );
		tf_rest_security_matrix_assert_safe_error( $response, "Zero object ID for {$route}" );
	}

	$missing_optional_filters = tf_rest_security_matrix_request(
		'GET',
		'/tf/v1/orders',
		array( 'post_type' => 'hotel' )
	);
	tf_rest_security_matrix_assert_status( $missing_optional_filters, 200, 'Missing optional order filters must remain valid.' );

	$empty_numeric_filter = tf_rest_security_matrix_request(
		'GET',
		'/tf/v1/orders',
		array(
			'post_type'   => 'hotel',
			'post_id'     => '',
		)
	);
	tf_rest_security_matrix_assert_status( $empty_numeric_filter, 400, 'An explicitly empty numeric filter must be rejected.' );
	tf_rest_security_matrix_assert_safe_error( $empty_numeric_filter, 'Empty numeric filter request' );

	$invalid_booking_type = tf_rest_security_matrix_request(
		'GET',
		'/tf/v1/user-bookings',
		array( 'booking_type' => "hotel' UNION SELECT 1--" )
	);
	tf_rest_security_matrix_assert_status( $invalid_booking_type, 400, 'Malformed booking_type must be rejected.' );
	tf_rest_security_matrix_assert_safe_error( $invalid_booking_type, 'Malformed booking_type request' );
} catch ( Throwable $error ) {
	$failure = $error;
} finally {
	tf_rest_security_matrix_cleanup( $fixtures );
}

if ( $failure ) {
	fwrite( STDERR, 'FAIL: ' . $failure->getMessage() . "\n" );
	exit( 1 );
}

echo "REST order/enquiry SQL injection and authorization matrix passed.\n";
