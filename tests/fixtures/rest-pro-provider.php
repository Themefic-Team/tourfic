<?php
/**
 * Load the independent Pro read providers into the common isolated REST matrix.
 * These adapters translate method names only; all authorization and SQL run in Pro.
 */

$pro_root = dirname( ABSPATH ) . '/tourfic-pro/';
foreach ( array( 'Rest', 'Booking_Rest', 'Enquiry_Rest', 'User_Rest' ) as $api_class ) {
	require $pro_root . 'inc/frontend-dashboard/classes/TF_FD_' . $api_class . '_API.php';
}

// Load only the query function: including inc/functions.php would bootstrap unrelated runtime hooks.
$tokens = token_get_all( file_get_contents( $pro_root . 'inc/functions.php' ) );
$code = '';
$depth = 0;
$capturing = false;
$opened = false;
foreach ( $tokens as $index => $token ) {
	if ( is_array( $token ) && T_FUNCTION === $token[0] ) {
		$next = $index + 1;
		while ( isset( $tokens[ $next ] ) && is_array( $tokens[ $next ] ) && T_WHITESPACE === $tokens[ $next ][0] ) { ++$next; }
		$capturing = isset( $tokens[ $next ][1] ) && 'tourfic_vendor_order_table_data' === $tokens[ $next ][1];
	}
	if ( ! $capturing ) { continue; }
	$code .= is_array( $token ) ? $token[1] : $token;
	if ( '{' === $token || ( is_array( $token ) && in_array( $token[0], array( T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ), true ) ) ) {
		++$depth;
		$opened = true;
	} elseif ( '}' === $token && 0 === --$depth && $opened ) {
		break;
	}
}
if ( ! $capturing || ! $opened || 0 !== $depth ) { throw new RuntimeException( 'Cannot isolate Pro order query provider.' ); }
eval( $code );

function tfopt( $key = '', $default = null ) {
	return \Tourfic\Classes\Helper::tfopt( $key, $default );
}

class Tourfic_Booking_Rest_API extends TF_FD_Booking_Rest_API {
	public function tf_order_permission_callback( WP_REST_Request $request ) { return $this->tf_fd_order_read_permission_callback( $request ); }
	public function tf_get_orders( $request ) { return $this->tf_fd_get_orders( $request ); }
	public function tf_get_order_details( $request ) { return $this->tf_fd_get_order_details( $request ); }
}
class Tourfic_Enquiry_Rest_API extends TF_FD_Enquiry_Rest_API {
	public function tf_get_enquiries( $request ) { return $this->tf_fd_get_enquiries( $request ); }
	public function tf_get_enquiry_details( $request ) { return $this->tf_fd_get_enquiry_details( $request ); }
}
class Tourfic_User_Rest_API extends TF_FD_User_Rest_API {
	public function tf_get_users( $request ) { return $this->tf_fd_get_users( $request ); }
	public function tf_get_user( $request ) { return $this->tf_fd_get_user( $request ); }
}

function tourfic_rest_verify_pro_contracts( $bookings, $users ) {
	tourfic_rest_actor( array( 'edit_tf_hotels' ) );
	$request = new WP_REST_Request( array( 'post_type' => 'hotel', 'order_id' => 1100 ) );
	tourfic_rest_assert( array( 100 ) === array_map( 'intval', array_column( $bookings->tf_get_orders( $request )['data'], 'id' ) ), 'Pro order_id filter is retained.' );
	$result = $bookings->tf_get_orders( new WP_REST_Request( array( 'post_type' => 'hotel', 'order_id' => 1101 ) ) );
	tourfic_rest_assert( array() === $result['data'], 'Pro order_id filter cannot escape owner scope.' );
	tourfic_rest_error( $bookings->tf_fd_order_permission_callback( $request ), 403, 'New custom-role read permission does not authorize the legacy write callback.' );
	$request = new WP_REST_Request( array( 'id' => 100, 'order_status' => 'cancelled' ) );
	tourfic_rest_error( $bookings->tf_fd_update_order_status( $request ), 403, 'Actual order status writer does not inherit new read permissions.' );
	tourfic_rest_error( $bookings->tf_fd_update_visitor_details( $request ), 403, 'Actual visitor writer does not inherit new read permissions.' );
	tourfic_rest_error( $users->tf_fd_admin_permission_callback( $request ), 403, 'Directory read policy does not replace the user mutation callback.' );

	$routes = file_get_contents( dirname( ABSPATH ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_API_Routes.php' );
	foreach ( array(
		'tf_fd_get_orders' => 'tf_fd_order_read_permission_callback',
		'tf_fd_get_order_details' => 'tf_fd_order_read_permission_callback',
		'tf_fd_get_enquiries' => 'tf_fd_enquiry_read_permission_callback',
		'tf_fd_get_enquiry_details' => 'tf_fd_enquiry_read_permission_callback',
		'tf_fd_get_users' => 'tf_fd_users_read_permission_callback',
		'tf_fd_get_user' => 'tf_fd_user_read_permission_callback',
		'tf_fd_update_order_status' => 'tf_fd_order_permission_callback',
		'tf_fd_update_visitor_details' => 'tf_fd_order_permission_callback',
		'tf_fd_insert_user' => 'tf_fd_admin_permission_callback',
		'tf_fd_update_user' => 'tf_fd_user_permission_callback',
		'tf_fd_update_user_status' => 'tf_fd_admin_permission_callback',
	) as $handler => $permission ) {
		$pattern = "/'callback'\\s*=>\\s*array\\(\\s*\\\$api,\\s*'" . $handler . "'\\s*\\),\\s*'permission_callback'\\s*=>\\s*array\\(\\s*\\\$api,\\s*'" . $permission . "'\\s*\\)/";
		tourfic_rest_assert( 1 === preg_match( $pattern, $routes ), $handler . ': route uses its intended read/write callback.' );
	}
	$legacy = tourfic_vendor_order_table_data( array( 'select' => 'id', 'post_type' => 'hotel', 'author' => 17, 'orderby' => 'id', 'order' => 'ASC' ) );
	tourfic_rest_assert( array( 100, 102 ) === array_map( 'intval', array_column( $legacy, 'id' ) ), 'Existing Pro query callers without the new CPT scope retain their behavior.' );
}
