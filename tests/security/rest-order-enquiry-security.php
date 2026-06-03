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
	'free_booking'      => $root . '/inc/Classes/REST_API/TF_Booking_Rest_API.php',
	'free_enquiry'      => $root . '/inc/Classes/REST_API/TF_Enquiry_Rest_API.php',
	'free_user'         => $root . '/inc/Classes/REST_API/TF_User_Rest_API.php',
	'free_helper'       => $root . '/inc/Classes/Helper.php',
	'pro_routes'        => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_API_Routes.php',
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
	$offset = strpos( $source, 'function ' . $method );
	tf_security_assert( false !== $offset, "Method {$method} not found." );

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
tf_security_assert( false !== strpos( $pro_routes, 'tf_fd_order_permission_callback' ), 'Pro orders route must use order permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, 'tf_fd_enquiry_permission_callback' ), 'Pro enquiries route must use enquiry permission callback.' );
tf_security_assert( false !== strpos( $free_routes, "'tf_hotel'" ), 'Free orders route must accept documented CPT aliases.' );
tf_security_assert( false !== strpos( $pro_routes, "'tf_hotel'" ), 'Pro orders route must accept documented CPT aliases.' );

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

$free_helper       = tf_security_file( $files['free_helper'] );
$pro_vendor_helper = tf_security_file( $files['pro_vendor_helper'] );

tf_security_assert( false !== strpos( $free_helper, 'tf_order_table_structured_sql' ), 'Free order helper must support structured prepared filters.' );
tf_security_assert( false !== strpos( $pro_vendor_helper, "isset( \$query['where'] )" ), 'Pro vendor helper must support structured prepared filters.' );

echo "REST order/enquiry security regression checks passed.\n";
