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
	'pro_hotel_booking' => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Hotel_Backend_Booking_Rest_API.php',
	'pro_integration'   => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Integration_Rest_API.php',
	'pro_room'          => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Room_Rest_API.php',
	'pro_tour_booking'  => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Tour_Backend_Booking_Rest_API.php',
	'pro_user'          => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_User_Rest_API.php',
	'pro_vendor'        => dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Vendor_Rest_API.php',
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
tf_security_assert( false !== strpos( $pro_routes, "'callback'            => array( \$api, 'tf_fd_update_user' )" ), 'Pro update-user route must remain registered.' );
tf_security_assert( false !== strpos( $pro_routes, "'callback'            => array( \$api, 'tf_fd_update_user' ),\n\t\t\t'permission_callback' => array( \$api, 'tf_fd_user_permission_callback' )" ), 'Pro update-user route must use user-object permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, "'callback'            => array( \$api, 'tf_fd_update_visitor_details' ),\n\t\t\t'permission_callback' => array( \$api, 'tf_fd_order_permission_callback' )" ), 'Pro visitor-detail updates must use order permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, 'tf_fd_payout_permission_callback' ), 'Pro payout routes must use payout-object permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, "'callback'            => array( \$api, 'tf_fd_update_payout_status' ),\n\t\t\t'permission_callback' => array( \$api, 'tf_fd_admin_permission_callback' )" ), 'Pro payout status updates must stay admin-gated.' );
tf_security_assert( false !== strpos( $pro_routes, "'callback'            => array( \$api, 'tf_fd_get_google_access_token_url' ),\n\t\t\t'permission_callback' => array( \$api, 'tf_fd_integration_permission_callback' )" ), 'Pro Google token URL route must use integration object permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, "'callback'            => array( \$api, 'tf_fd_reset_google_access_token' ),\n\t\t\t'permission_callback' => array( \$api, 'tf_fd_integration_permission_callback' )" ), 'Pro Google token reset route must use integration object permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, "'callback'            => array( \$api, 'tf_fd_available_hotel_room_and_service_type' ),\n\t\t\t'permission_callback' => array( \$api, 'tf_fd_post_permission_callback' )" ), 'Pro hotel room/service availability must use post-object permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, "'callback'            => array( \$api, 'tf_fd_available_hotel_room_number' ),\n\t\t\t'permission_callback' => array( \$api, 'tf_fd_post_permission_callback' )" ), 'Pro hotel room-number availability must use post-object permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, "'callback'            => array( \$api, 'tf_fd_hotel_add_booking' ),\n\t\t\t'permission_callback' => array( \$api, 'tf_fd_post_permission_callback' )" ), 'Pro backend hotel booking must use post-object permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, "'callback'            => array( \$api, 'tf_fd_available_tour_date_time' ),\n\t\t\t'permission_callback' => array( \$api, 'tf_fd_post_permission_callback' )" ), 'Pro tour date/time availability must use post-object permission callback.' );
tf_security_assert( false !== strpos( $pro_routes, "'callback'            => array( \$api, 'tf_fd_tour_add_booking' ),\n\t\t\t'permission_callback' => array( \$api, 'tf_fd_post_permission_callback' )" ), 'Pro backend tour booking must use post-object permission callback.' );
tf_security_assert( false !== strpos( $free_routes, "'tf_hotel'" ), 'Free orders route must accept documented CPT aliases.' );
tf_security_assert( false !== strpos( $pro_routes, "'tf_hotel'" ), 'Pro orders route must accept documented CPT aliases.' );
tf_security_assert( false !== strpos( $pro_routes, "'order_id'" ), 'Pro orders route must accept booking ID search filters.' );

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

$pro_orders = tf_security_method_body( tf_security_file( $files['pro_booking'] ), 'tf_fd_get_orders' );
tf_security_assert( false !== strpos( $pro_orders, "tf_fd_get_rest_absint_param( \$request, 'order_id' )" ), 'Pro orders handler must sanitize booking ID search filters.' );
tf_security_assert( false !== strpos( $pro_orders, "\$filters['order_id']" ), 'Pro orders handler must pass booking ID filters to the order helper.' );

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

$pro_update_user = tf_security_method_body( tf_security_file( $files['pro_user'] ), 'tf_fd_update_user' );
tf_security_assert( false !== strpos( $pro_update_user, 'tf_fd_current_user_can_access_user' ), 'Pro update-user handler must verify requested user ownership.' );
tf_security_assert( false !== strpos( $pro_update_user, 'wp_check_password( $current_password' ), 'Pro update-user handler must verify current password before self password changes.' );
tf_security_assert( false !== strpos( $pro_update_user, "user_args['user_pass']" ), 'Pro update-user handler must still support authorized password updates.' );

$pro_update_order_status = tf_security_method_body( tf_security_file( $files['pro_booking'] ), 'tf_fd_update_order_status' );
tf_security_assert( false !== strpos( $pro_update_order_status, 'tf_fd_current_user_can_access_order' ), 'Pro update-order-status handler must verify order ownership.' );
$pro_update_visitor_details = tf_security_method_body( tf_security_file( $files['pro_booking'] ), 'tf_fd_update_visitor_details' );
tf_security_assert( false !== strpos( $pro_update_visitor_details, 'tf_fd_current_user_can_access_order' ), 'Pro update-visitor-details handler must verify order ownership.' );

foreach ( array( 'tf_fd_get_payout', 'tf_fd_update_payout' ) as $method ) {
	$body = tf_security_method_body( tf_security_file( $files['pro_vendor'] ), $method );
	tf_security_assert( false !== strpos( $body, 'tf_fd_current_user_can_access_payout' ), "{$method} must verify payout ownership." );
}

$pro_integration_permission = tf_security_method_body( tf_security_file( $files['pro_integration'] ), 'tf_fd_integration_permission_callback' );
tf_security_assert( false !== strpos( $pro_integration_permission, 'tf_fd_admin_vendor_permission_callback' ), 'Pro integration permission must require dashboard admin/vendor access.' );
tf_security_assert( false !== strpos( $pro_integration_permission, 'tf_fd_get_authorized_integration_user_id' ), 'Pro integration permission must authorize the requested user id.' );

foreach ( array( 'tf_fd_get_google_access_token_url', 'tf_fd_reset_google_access_token' ) as $method ) {
	$body          = tf_security_method_body( tf_security_file( $files['pro_integration'] ), $method );
	$access_offset = strpos( $body, 'tf_fd_get_authorized_integration_user_id' );
	tf_security_assert( false !== $access_offset, "{$method} must verify integration user ownership." );
}

$pro_integration_user = tf_security_method_body( tf_security_file( $files['pro_integration'] ), 'tf_fd_get_authorized_integration_user_id' );
tf_security_assert( false !== strpos( $pro_integration_user, 'tf_fd_current_user_can_access_user' ), 'Pro integration user helper must delegate to user-object authorization.' );
tf_security_assert( false !== strpos( $pro_integration_user, 'get_current_user_id()' ), 'Pro integration user helper must default to current user.' );

$pro_room_list = tf_security_method_body( tf_security_file( $files['pro_room'] ), 'tf_fd_get_rooms' );
tf_security_assert( false !== strpos( $pro_room_list, 'tf_fd_get_dashboard_author_id' ), 'Pro rooms list must clamp requested author for non-managers.' );

$pro_available_hotel = tf_security_method_body( tf_security_file( $files['pro_hotel_booking'] ), 'tf_fd_available_hotel' );
tf_security_assert( false !== strpos( $pro_available_hotel, "tf_fd_get_dashboard_author_id( \$request, 'user_id' )" ), 'Pro available-hotel route must clamp requested user_id for non-managers.' );

$free_helper       = tf_security_file( $files['free_helper'] );
$pro_vendor_helper = tf_security_file( $files['pro_vendor_helper'] );

tf_security_assert( false !== strpos( $free_helper, 'tf_order_table_structured_sql' ), 'Free order helper must support structured prepared filters.' );
tf_security_assert( 1 === preg_match( "/'order_id'\\s*=>\\s*'%d'/", $free_helper ), 'Free order helper must allow prepared booking ID filters.' );
tf_security_assert( false !== strpos( $pro_vendor_helper, "isset( \$query['where'] )" ), 'Pro vendor helper must support structured prepared filters.' );

echo "REST user/order/enquiry security regression checks passed.\n";
