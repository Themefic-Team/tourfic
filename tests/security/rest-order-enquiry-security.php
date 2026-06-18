<?php
/**
 * Static regression checks for REST order/enquiry authorization and SQL filters.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/security/rest-order-enquiry-security.php
 */

$root = dirname( __DIR__, 2 );

$files = array(
	'free_routes'       => $root . '/inc/Classes/REST_API/TF_API_Routes.php',
	'free_rest'         => $root . '/inc/Classes/REST_API/TF_Rest_API.php',
	'free_booking'      => $root . '/inc/Classes/REST_API/TF_Booking_Rest_API.php',
	'free_enquiry'      => $root . '/inc/Classes/REST_API/TF_Enquiry_Rest_API.php',
	'free_user'         => $root . '/inc/Classes/REST_API/TF_User_Rest_API.php',
	'free_helper'       => $root . '/inc/Classes/Helper.php',
	'pro_routes'        => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_API_Routes.php',
	'pro_rest'          => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Rest_API.php',
	'pro_booking'       => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Booking_Rest_API.php',
	'pro_enquiry'       => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Enquiry_Rest_API.php',
	'pro_user'          => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_User_Rest_API.php',
	'pro_vendor_helper' => dirname( $root ) . '/tourfic-pro/inc/functions.php',
);

foreach ( $files as $label => $file ) {
	if ( ! is_readable( $file ) ) {
		fwrite( STDERR, "Missing fixture {$label}: {$file}\n" );
		exit( 1 );
	}
}

function tf_security_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

function tf_security_file( $file ) {
	return file_get_contents( $file );
}

function tf_security_method_body( $source, $method ) {
	$matched = preg_match( '/function\s+' . preg_quote( $method, '/' ) . '\s*\(/', $source, $matches, PREG_OFFSET_CAPTURE );
	tf_security_assert( 1 === $matched, "Method {$method} not found." );
	$offset = $matches[0][1];

	$brace = strpos( $source, '{', $offset );
	tf_security_assert( false !== $brace, "Method {$method} has no body." );

	$depth  = 0;
	$length = strlen( $source );
	for ( $i = $brace; $i < $length; $i++ ) {
		if ( '{' === $source[ $i ] ) {
			$depth++;
		} elseif ( '}' === $source[ $i ] ) {
			$depth--;
			if ( 0 === $depth ) {
				return substr( $source, $brace, $i - $brace + 1 );
			}
		}
	}

	tf_security_assert( false, "Method {$method} body is not balanced." );
}

$free_routes = tf_security_file( $files['free_routes'] );
$pro_routes  = tf_security_file( $files['pro_routes'] );

tf_security_assert( false !== strpos( $free_routes, 'tf_order_permission_callback' ), 'Free orders route must use order permission callback.' );
tf_security_assert( false !== strpos( $free_routes, 'tf_enquiry_permission_callback' ), 'Free enquiries route must use enquiry permission callback.' );
tf_security_assert( false !== strpos( $free_routes, 'tf_user_permission_callback' ), 'Free user detail route must use user-object permission callback.' );
tf_security_assert( false !== strpos( $free_routes, 'tf_admin_permission_callback' ), 'Free users route must stay admin-gated.' );
tf_security_assert( false !== strpos( $pro_routes, 'tf_fd_order_permission_callback' ), 'Pro orders route must use order permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, 'tf_fd_enquiry_permission_callback' ), 'Pro enquiries route must use enquiry permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, 'tf_fd_user_permission_callback' ), 'Pro user detail route must use user-object permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, 'tf_fd_admin_permission_callback' ), 'Pro users route must stay admin-gated.' );
tf_security_assert( false !== strpos( $free_routes, "'tf_hotel'" ), 'Free orders route must accept documented CPT aliases.' );
tf_security_assert( false !== strpos( $pro_routes, "'tf_hotel'" ), 'Pro orders route must accept documented CPT aliases.' );

foreach ( array( 'free_rest' => 'tf_user_permission_callback', 'pro_rest' => 'tf_fd_user_permission_callback' ) as $label => $method ) {
	$body = tf_security_method_body( tf_security_file( $files[ $label ] ), $method );
	tf_security_assert( false !== strpos( $body, 'is_user_logged_in()' ), "{$method} must require an authenticated user." );
	tf_security_assert( false !== strpos( $body, "get_param( 'id' )" ), "{$method} must authorize the requested user id." );
	tf_security_assert( false !== strpos( $body, 'current_user_can_access_user' ), "{$method} must delegate to object-level user authorization." );
}

foreach ( array( 'free_rest' => 'tf_current_user_can_access_user', 'pro_rest' => 'tf_fd_current_user_can_access_user' ) as $label => $method ) {
	$body = tf_security_method_body( tf_security_file( $files[ $label ] ), $method );
	tf_security_assert( false !== strpos( $body, 'get_current_user_id()' ), "{$method} must compare against the current user." );
	tf_security_assert( false !== strpos( $body, 'current_user_can( \'list_users\' )' ), "{$method} must allow users with user-list capability." );
	tf_security_assert( false !== strpos( $body, 'current_user_can( \'edit_user\', $user_id )' ), "{$method} must allow users who can edit the requested user." );
}

foreach ( array( 'free_booking' => 'tf_get_orders', 'pro_booking' => 'tf_fd_get_orders' ) as $label => $method ) {
	$body = tf_security_method_body( tf_security_file( $files[ $label ] ), $method );
	tf_security_assert( false === strpos( $body, "get_param( 'user_id' )" ), "{$method} must not trust client user_id." );
	tf_security_assert( false === strpos( $body, '$tf_filter_query' ), "{$method} must not build raw SQL filter strings." );
	tf_security_assert( false !== strpos( $body, "'where'" ), "{$method} must pass structured filters to the order helper." );
	tf_security_assert( false !== strpos( $body, 'normalize_order_post_type' ), "{$method} must normalize CPT aliases before querying." );
}

foreach ( array( 'free_enquiry' => 'tf_get_enquiries', 'pro_enquiry' => 'tf_fd_get_enquiries' ) as $label => $method ) {
	$body = tf_security_method_body( tf_security_file( $files[ $label ] ), $method );
	tf_security_assert( false === strpos( $body, "get_param( 'user_id' )" ), "{$method} must not trust client user_id." );
	tf_security_assert( false === strpos( $body, '$tf_filter_query' ), "{$method} must not build raw SQL filter strings." );
	tf_security_assert( false !== strpos( $body, 'implode( \' AND \', $where )' ), "{$method} must build SQL from prepared where clauses." );
}

foreach ( array( 'free_user' => 'tf_user_bookings', 'pro_user' => 'tf_fd_user_bookings' ) as $label => $method ) {
	$body = tf_security_method_body( tf_security_file( $files[ $label ] ), $method );
	tf_security_assert( false === strpos( $body, "get_param( 'user_id' )" ), "{$method} must use the current user only." );
	tf_security_assert( false !== strpos( $body, 'get_current_user_id()' ), "{$method} must scope bookings to get_current_user_id()." );
}

foreach ( array( 'free_user' => 'tf_user_wishlist', 'pro_user' => 'tf_fd_user_wishlist' ) as $label => $method ) {
	$body = tf_security_method_body( tf_security_file( $files[ $label ] ), $method );
	tf_security_assert( false === strpos( $body, "get_param( 'user_id' )" ), "{$method} must use the current user only." );
	tf_security_assert( false !== strpos( $body, 'get_current_user_id()' ), "{$method} must scope wishlist data to get_current_user_id()." );
	tf_security_assert( false !== strpos( $body, '$wishlist_data' ), "{$method} must return a defined wishlist response for non-customer users." );
}

foreach ( array( 'free_user' => 'tf_get_user', 'pro_user' => 'tf_fd_get_user' ) as $label => $method ) {
	$body          = tf_security_method_body( tf_security_file( $files[ $label ] ), $method );
	$access_offset = strpos( $body, 'current_user_can_access_user' );
	$data_offset   = strpos( $body, '$user_data = array' );
	tf_security_assert( false !== $access_offset, "{$method} must perform handler-level user authorization." );
	tf_security_assert( false !== $data_offset, "{$method} must still build a user data response." );
	tf_security_assert( $access_offset < $data_offset, "{$method} must authorize before building the user data response." );
}

$free_helper       = tf_security_file( $files['free_helper'] );
$pro_vendor_helper = tf_security_file( $files['pro_vendor_helper'] );

tf_security_assert( false !== strpos( $free_helper, 'tf_order_table_structured_sql' ), 'Free order helper must support structured prepared filters.' );
tf_security_assert( false !== strpos( $pro_vendor_helper, "isset( \$query['where'] )" ), 'Pro vendor helper must support structured prepared filters.' );

echo "REST user/order/enquiry security regression checks passed.\n";
