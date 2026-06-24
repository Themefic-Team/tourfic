<?php
/**
 * Live REST authorization checks for user-object access.
 *
 * Run from the Tourfic Free plugin root:
 * wp eval-file tests/security/rest-user-object-authorization-live.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "This check must run inside WordPress.\n" );
	exit( 1 );
}

function tf_security_live_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

function tf_security_live_request( $route, $id = 0 ) {
	$request = new WP_REST_Request( 'GET', $route );

	if ( ! empty( $id ) ) {
		$request->set_param( 'id', $id );
	}

	return $request;
}

function tf_security_live_is_denied( $result ) {
	return is_wp_error( $result ) && 403 === absint( $result->get_error_data()['status'] ?? 0 );
}

function tf_security_live_low_privilege_user() {
	$subscribers = get_users(
		array(
			'role'   => 'subscriber',
			'number' => 1,
			'fields' => 'all',
		)
	);

	if ( ! empty( $subscribers[0] ) ) {
		return $subscribers[0];
	}

	$users = get_users(
		array(
			'role'   => 'customer',
			'number' => 1,
			'fields' => 'all',
		)
	);

	return ! empty( $users[0] ) ? $users[0] : null;
}

function tf_security_live_vendor_user() {
	$vendors = get_users(
		array(
			'role'   => 'tf_vendor',
			'number' => 1,
			'fields' => 'all',
		)
	);

	return ! empty( $vendors[0] ) ? $vendors[0] : null;
}

function tf_security_live_rest_get( $route, $params = array() ) {
	$request = new WP_REST_Request( 'GET', $route );

	foreach ( $params as $key => $value ) {
		$request->set_param( $key, $value );
	}

	return rest_do_request( $request );
}

function tf_security_live_rest_post( $route, $params = array() ) {
	$request = new WP_REST_Request( 'POST', $route );

	foreach ( $params as $key => $value ) {
		$request->set_param( $key, $value );
	}

	return rest_do_request( $request );
}

function tf_security_live_response_status( $response ) {
	if ( is_wp_error( $response ) ) {
		return absint( $response->get_error_data()['status'] ?? 500 );
	}

	return absint( $response->get_status() );
}

function tf_security_live_response_data( $response ) {
	if ( is_wp_error( $response ) ) {
		return null;
	}

	return $response->get_data();
}

function tf_security_live_assert_status( $response, $status, $message ) {
	tf_security_live_assert(
		$status === tf_security_live_response_status( $response ),
		$message . ' Expected HTTP ' . $status . ', got HTTP ' . tf_security_live_response_status( $response ) . '.'
	);
}

function tf_security_live_assert_no_admin_orders( $response, $admin_user_id ) {
	$data   = tf_security_live_response_data( $response );
	$orders = is_array( $data ) ? $data : array();

	foreach ( $orders as $order ) {
		tf_security_live_assert(
			empty( $order['customer_id'] ) || absint( $order['customer_id'] ) !== absint( $admin_user_id ),
			'User bookings route must not return orders for the attacker-supplied administrator user_id.'
		);
	}
}

$admin_user = get_users(
	array(
		'role'   => 'administrator',
		'number' => 1,
		'fields' => 'all',
	)
);
$admin_user = ! empty( $admin_user[0] ) ? $admin_user[0] : null;
$low_user   = tf_security_live_low_privilege_user();

if ( ! $admin_user || ! $low_user ) {
	echo "REST user-object live authorization checks skipped: administrator and low-privilege users are required.\n";
	return;
}

$checked = 0;

if ( class_exists( 'TF_User_Rest_API' ) ) {
	$free_api = TF_User_Rest_API::get_instance();
	wp_set_current_user( $low_user->ID );

	tf_security_live_assert(
		tf_security_live_is_denied( $free_api->tf_user_permission_callback( tf_security_live_request( '/tf/v1/user/' . $admin_user->ID, $admin_user->ID ) ) ),
		'Free low-privilege user must not access another user object.'
	);
	tf_security_live_assert(
		true === $free_api->tf_user_permission_callback( tf_security_live_request( '/tf/v1/user/' . $low_user->ID, $low_user->ID ) ),
		'Free low-privilege user must retain access to their own user object.'
	);
	tf_security_live_assert(
		tf_security_live_is_denied( $free_api->tf_admin_permission_callback( tf_security_live_request( '/tf/v1/users' ) ) ),
		'Free low-privilege user must not access the bulk users route.'
	);
	tf_security_live_assert(
		tf_security_live_is_denied( $free_api->tf_order_permission_callback( tf_security_live_request( '/tf/v1/order/1', 1 ) ) ),
		'Free low-privilege user must not pass order permission.'
	);
	tf_security_live_assert(
		tf_security_live_is_denied( $free_api->tf_enquiry_permission_callback( tf_security_live_request( '/tf/v1/enquiries/1', 1 ) ) ),
		'Free low-privilege user must not pass enquiry permission.'
	);

	$checked++;
}

if ( class_exists( 'TF_FD_User_Rest_API' ) ) {
	$pro_api = TF_FD_User_Rest_API::get_instance();
	wp_set_current_user( $low_user->ID );

	tf_security_live_assert(
		tf_security_live_is_denied( $pro_api->tf_fd_user_permission_callback( tf_security_live_request( '/tf/v1/user/' . $admin_user->ID, $admin_user->ID ) ) ),
		'Pro low-privilege user must not access another user object.'
	);
	tf_security_live_assert(
		true === $pro_api->tf_fd_user_permission_callback( tf_security_live_request( '/tf/v1/user/' . $low_user->ID, $low_user->ID ) ),
		'Pro low-privilege user must retain access to their own user object.'
	);
	tf_security_live_assert(
		tf_security_live_is_denied( $pro_api->tf_fd_admin_permission_callback( tf_security_live_request( '/tf/v1/users' ) ) ),
		'Pro low-privilege user must not access the bulk users route.'
	);
	tf_security_live_assert(
		tf_security_live_is_denied( $pro_api->tf_fd_order_permission_callback( tf_security_live_request( '/tf/v1/order/1', 1 ) ) ),
		'Pro low-privilege user must not pass order permission.'
	);
	tf_security_live_assert(
		tf_security_live_is_denied( $pro_api->tf_fd_enquiry_permission_callback( tf_security_live_request( '/tf/v1/enquiries/1', 1 ) ) ),
		'Pro low-privilege user must not pass enquiry permission.'
	);

	$checked++;
}

tf_security_live_assert( $checked > 0, 'No Tourfic REST user API class was loaded.' );

wp_set_current_user( $low_user->ID );
rest_get_server();

$admin_user_response = tf_security_live_rest_get( '/tf/v1/user/' . $admin_user->ID, array( 'id' => $admin_user->ID ) );
tf_security_live_assert_status(
	$admin_user_response,
	403,
	'Reported case failed: low-privilege REST request must not read the administrator user object.'
);

$admin_user_update_response = tf_security_live_rest_post(
	'/tf/v1/user/' . $admin_user->ID,
	array(
		'id'           => $admin_user->ID,
		'new_password' => 'tourfic-security-regression-password',
	)
);
tf_security_live_assert_status(
	$admin_user_update_response,
	403,
	'Reported case failed: low-privilege REST request must not update the administrator user object.'
);

$self_user_response = tf_security_live_rest_get( '/tf/v1/user/' . $low_user->ID, array( 'id' => $low_user->ID ) );
tf_security_live_assert_status(
	$self_user_response,
	200,
	'Current user must still read their own user object.'
);
$self_user_data = tf_security_live_response_data( $self_user_response );
tf_security_live_assert(
	is_array( $self_user_data ) && absint( $self_user_data['id'] ?? 0 ) === absint( $low_user->ID ),
	'Current user self response must contain the current user id.'
);
tf_security_live_assert(
	empty( $self_user_data['id'] ) || absint( $self_user_data['id'] ) !== absint( $admin_user->ID ),
	'Current user self response must not expose the administrator user object.'
);

tf_security_live_assert_status(
	tf_security_live_rest_get( '/tf/v1/users' ),
	403,
	'Negative control failed: low-privilege REST request must not read the bulk users route.'
);
tf_security_live_assert_status(
	tf_security_live_rest_get( '/tf/v1/order/1', array( 'id' => 1 ) ),
	403,
	'Reported case failed: low-privilege REST request must not pass order detail authorization.'
);
tf_security_live_assert_status(
	tf_security_live_rest_get( '/tf/v1/enquiries/1', array( 'id' => 1 ) ),
	403,
	'Reported case failed: low-privilege REST request must not pass enquiry detail authorization.'
);

$vendor_user = tf_security_live_vendor_user();
if ( $vendor_user ) {
	wp_set_current_user( $vendor_user->ID );

	tf_security_live_assert_status(
		tf_security_live_rest_get( '/tf/v1/get-google-access-token-url', array( 'user_id' => $admin_user->ID ) ),
		403,
		'Vendor REST request must not generate a Google access URL for an administrator user_id.'
	);

	$original_admin_integration_settings = get_user_meta( $admin_user->ID, '_tf_integration_settings', true );
	$sentinel_admin_integration_settings = array( 'tourfic_security_probe' => 'preserve-admin-integration-meta' );
	update_user_meta( $admin_user->ID, '_tf_integration_settings', $sentinel_admin_integration_settings );

	$admin_reset_response = tf_security_live_rest_post(
		'/tf/v1/reset-google-access-token',
		array(
			'user_id' => $admin_user->ID,
		)
	);
	$admin_integration_settings_after_reset = get_user_meta( $admin_user->ID, '_tf_integration_settings', true );

	if ( '' === $original_admin_integration_settings ) {
		delete_user_meta( $admin_user->ID, '_tf_integration_settings' );
	} else {
		update_user_meta( $admin_user->ID, '_tf_integration_settings', $original_admin_integration_settings );
	}

	tf_security_live_assert_status(
		$admin_reset_response,
		403,
		'Vendor REST request must not reset administrator Google integration settings.'
	);
	tf_security_live_assert(
		$admin_integration_settings_after_reset === $sentinel_admin_integration_settings,
		'Forbidden administrator Google integration reset must not delete administrator integration metadata.'
	);

	tf_security_live_assert_status(
		tf_security_live_rest_get( '/tf/v1/get-google-access-token-url' ),
		200,
		'Vendor REST request without user_id must still generate the current vendor Google access URL.'
	);

	wp_set_current_user( $admin_user->ID );
	tf_security_live_assert_status(
		tf_security_live_rest_get( '/tf/v1/get-google-access-token-url', array( 'user_id' => $vendor_user->ID ) ),
		200,
		'Administrator REST request must still generate a delegated vendor Google access URL.'
	);
}

wp_set_current_user( $low_user->ID );

$bookings_response = tf_security_live_rest_get(
	'/tf/v1/user-bookings',
	array(
		'user_id'      => $admin_user->ID,
		'booking_type' => 'all',
	)
);
tf_security_live_assert_status(
	$bookings_response,
	200,
	'User bookings route should remain available to the authenticated user context.'
);
tf_security_live_assert_no_admin_orders( $bookings_response, $admin_user->ID );

$wishlist_response = tf_security_live_rest_get(
	'/tf/v1/user-wishlist',
	array(
		'user_id' => $admin_user->ID,
	)
);
tf_security_live_assert_status(
	$wishlist_response,
	200,
	'User wishlist route should remain available to the authenticated user context.'
);

wp_set_current_user( 0 );

echo "REST reported-case live authorization checks passed.\n";
