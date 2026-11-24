<?php
/** wp eval-file <this file> hotel|tour|apartment|car [invalid] in the disposable site. */
if ( ! defined( 'ABSPATH' ) || ! file_exists( ABSPATH . '.tourfic-booking-wishlist-test' ) ) {
	throw new RuntimeException( 'Disposable synthetic site required.' );
}
$kind = $args[0] ?? '';
$invalid = isset( $args[1] ) && 'invalid' === $args[1];
$definitions = array(
	'hotel' => array( 'tf_hotel', 'tf_hotels_opt', array( 'booking-by' => '1' ), 200.0, 'tourfic_hotel_booking_callback' ),
	'tour' => array( 'tf_tours', 'tf_tours_opt', array( 'booking-by' => '1', 'pricing' => 'person', 'adult_price' => 100, 'child_price' => 50, 'infant_price' => 0, 'type' => 'continuous' ), 200.0, 'tourfic_tours_booking_function' ),
	'apartment' => array( 'tf_apartment', 'tf_apartment_opt', array( 'booking-by' => '1', 'pricing_type' => 'per_night', 'price_per_night' => 80, 'max_adults' => 4, 'max_children' => 2, 'max_infants' => 1, 'additional_fees' => array() ), 160.0, 'tourfic_apartment_booking_callback' ),
	'car' => array( 'tf_carrental', 'tf_carrental_opt', array( 'booking-by' => '1', 'car_rent' => 100, 'price_by' => 'day', 'pricing_type' => 'day_hour', 'car_numbers' => 10 ), 200.0, 'tourfic_car_booking_callback' ),
);
if ( ! isset( $definitions[ $kind ] ) ) { throw new RuntimeException( 'Unknown fixture.' ); }
list( $type, $key, $meta, $expected, $callback ) = $definitions[ $kind ];
$decimal = isset( $args[1] ) && 'decimal' === $args[1];
$offline = isset( $args[1] ) && 'offline' === $args[1];
if ( $offline ) { $meta['booking-by'] = '3'; }
if ( $offline && 'tour' === $kind ) {
	$pro_source = file_get_contents( dirname( getcwd() ) . '/tourfic-pro/inc/functions.php' );
	if ( ! function_exists( 'tf_pro_tour_is_query_booking' ) ) {
		if ( ! preg_match( '/function tf_pro_tour_is_query_booking\s*\([^)]*\)\s*\{[^}]+\}/', $pro_source, $match ) ) { throw new RuntimeException( 'Maintained Pro query-booking provider required.' ); }
		eval( $match[0] );
	}
	add_filter( 'tourfic_tour_is_query_booking', 'tf_pro_tour_is_query_booking', 10, 2 );
}
if ( $decimal ) {
	$expected = 'hotel' === $kind ? 1.0 : 0.5;
	if ( 'car' === $kind ) { $meta['car_rent'] = '0.25'; }
	if ( 'tour' === $kind ) { $meta['adult_price'] = '0.25'; }
	if ( 'apartment' === $kind ) {
		$meta['enable_availability'] = '1';
		$meta['apt_availability'] = wp_json_encode( array(
			array( 'check_in' => '2030/01/02', 'check_out' => '2030/01/02', 'status' => 'available', 'price' => '0.25' ),
			array( 'check_in' => '2030/01/03', 'check_out' => '2030/01/03', 'status' => 'available', 'price' => '0.25' ),
		) );
	}
}
wp_set_current_user( 0 );
wc_load_cart(); WC()->cart->empty_cart();
$id = wp_insert_post( array( 'post_type' => $type, 'post_status' => 'publish', 'post_title' => 'Synthetic handler fixture' ) );
update_post_meta( $id, $key, $meta ); update_post_meta( $id, '_price', 0 );
$_POST = array( 'post_id' => (string) $id );
if ( 'hotel' === $kind ) {
	$room = wp_insert_post( array( 'post_type' => 'tf_room', 'post_status' => 'publish', 'post_title' => 'Synthetic room' ) );
	update_post_meta( $room, 'tf_room_opt', array( 'tf_hotel' => $id, 'unique_id' => 'synthetic-handler-room', 'adult' => 4, 'child' => 2, 'num-room' => 10, 'price' => 100, 'price_multi_day' => 1 ) );
	if ( $decimal ) { $room_meta = get_post_meta( $room, 'tf_room_opt', true ); $room_meta['adult_price'] = '0.25'; $room_meta['child_price'] = '0'; update_post_meta( $room, 'tf_room_opt', $room_meta ); add_filter( 'tourfic_room_pricing_mode', function() { return 2; } ); }
	$_POST += array( 'room_id' => (string) $room, 'adult' => '2', 'child' => '0', 'room' => '1', 'check_in_date' => '2030/01/02', 'check_out_date' => '2030/01/04', 'tf_room_booking_nonce' => wp_create_nonce( 'check_room_booking_nonce' ) );
} elseif ( 'tour' === $kind ) {
	$_POST += array( 'adults' => '2', 'childrens' => '0', 'infants' => '0', 'check-in-out-date' => '2030/01/02', '_ajax_nonce' => wp_create_nonce( 'tf_ajax_nonce' ) );
} elseif ( 'apartment' === $kind ) {
	$_POST += array( 'adults' => '2', 'children' => '0', 'infant' => '0', 'check-in-out-date' => '2030/01/02 - 2030/01/04', 'tf_apartment_nonce' => wp_create_nonce( 'tourfic_apartment_booking' ) );
} else {
	$_POST += array( 'pickup_date' => '2030/01/02', 'dropoff_date' => '2030/01/04', 'pickup_time' => '10:00', 'dropoff_time' => '10:00', '_nonce' => wp_create_nonce( 'tf_ajax_nonce' ) );
}
if ( $offline ) {
	$details = array_fill_keys( array( 'tf_first_name', 'tf_last_name', 'tf_street_address', 'tf_town_city', 'tf_state_country', 'tf_postcode', 'tf_country', 'tf_phone' ), 'Synthetic' );
	$details['tf_email'] = 'synthetic@example.invalid';
	$_POST['booking_confirm'] = $details; $_POST['travellerData'] = $details;
}
if ( $invalid ) { $_POST['post_id'] = array( $id ); }
ob_start();
add_action( 'shutdown', function() use ( $kind, $invalid, $expected, $offline, $id ) {
	$output = ob_get_clean();
	WC()->cart->calculate_totals();
	$items = WC()->cart->get_cart();
	$pass = ( $invalid || $offline ? empty( $items ) : 1 === count( $items ) ) && is_array( json_decode( $output, true ) );
	foreach ( $items as $item ) {
		$pass = $pass && ! is_wp_error( \Tourfic\Classes\Woocommerce\Booking_Validation::validate_item( $item ) )
			&& (float) $item['data']->get_price() === $expected;
	}
	if ( $offline ) {
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT customer_id,order_details,ostatus FROM {$wpdb->prefix}tf_order_data WHERE post_id = %d AND post_type = %s ORDER BY id DESC LIMIT 1", $id, $kind ), ARRAY_A );
		$details = $row ? json_decode( $row['order_details'], true ) : array();
		$pass = $pass && $row && 0 === (int) $row['customer_id'] && 'processing' === $row['ostatus'] && $expected === (float) ( $details['total_price'] ?? -1 );
	}
	echo wp_json_encode( array( 'module' => $kind, 'invalid' => $invalid, 'offline' => $offline, 'pass' => $pass, 'cart_rows' => count( $items ), 'response_is_json' => is_array( json_decode( $output, true ) ) ) ) . "\n";
	if ( ! $pass ) { exit( 1 ); }
}, 0 );
$callback();
