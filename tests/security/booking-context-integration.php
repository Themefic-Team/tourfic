<?php
/** Run with wp eval-file in the marked, disposable booking security site only. */
use Tourfic\Classes\Woocommerce\Booking_Validation as Validation;
use Tourfic\Classes\Woocommerce\Booking_Context as Context;
use Tourfic\Classes\Car_Rental\Booking as Car_Booking;

if ( ! defined( 'ABSPATH' ) || ! file_exists( ABSPATH . '.tourfic-booking-wishlist-test' ) ) {
	throw new RuntimeException( 'Disposable synthetic site required.' );
}
global $checks, $failed, $wpdb;
$checks = 0;
$failed = array();
function tourfic_fixture_expect( $condition, $label ) {
	++$GLOBALS['checks'];
	if ( ! $condition ) { $GLOBALS['failed'][] = $label; }
}
function tourfic_fixture_post( $type, $meta_key, $meta ) {
	$id = wp_insert_post( array( 'post_type' => $type, 'post_status' => 'publish', 'post_title' => 'Synthetic booking fixture' ) );
	update_post_meta( $id, $meta_key, $meta );
	update_post_meta( $id, '_price', 0 );
	return $id;
}
wp_set_current_user( 0 );
wc_load_cart();
$hotel = tourfic_fixture_post( 'tf_hotel', 'tf_hotels_opt', array( 'booking-by' => '1' ) );
$room_meta = array( 'tf_hotel' => $hotel, 'unique_id' => 'synthetic-room', 'adult' => 4, 'child' => 2, 'num-room' => 10, 'price' => '100', 'price_multi_day' => 1 );
$room = tourfic_fixture_post( 'tf_room', 'tf_room_opt', $room_meta );
$tour = tourfic_fixture_post( 'tf_tours', 'tf_tours_opt', array( 'booking-by' => '1', 'pricing' => 'person', 'adult_price' => '100', 'child_price' => '50', 'infant_price' => '0' ) );
$apt = tourfic_fixture_post( 'tf_apartment', 'tf_apartment_opt', array( 'booking-by' => '1', 'pricing_type' => 'per_night', 'price_per_night' => '80', 'max_adults' => 4, 'max_children' => 2, 'max_infants' => 1 ) );
$car_meta = array( 'booking-by' => '1', 'car_rent' => '100', 'price_by' => 'day', 'pricing_type' => 'day_hour', 'car_numbers' => '10' );
$car = tourfic_fixture_post( 'tf_carrental', 'tf_carrental_opt', $car_meta );
$hotel_selection = array( 'room_id' => $room, 'check_in' => '2030/01/02', 'check_out' => '2030/01/04', 'adult' => 2, 'child' => 0, 'room' => 1, 'option_id' => '' );
$tour_selection = array( 'date' => '2030/01/02', 'package' => '', 'adults' => 2, 'children' => 0, 'infant' => 0 );
$apt_selection = array( 'check_in' => '2030/01/02', 'check_out' => '2030/01/04', 'adults' => 2, 'children' => 0, 'infant' => 0 );
$car_selection = array( 'pickup_date' => '2030/01/02', 'dropoff_date' => '2030/01/04', 'pickup_time' => '10:00', 'dropoff_time' => '10:00' );
$cases = array(
	'hotel' => array( $hotel, $hotel_selection, 'tf_hotel_data', 'post_id', 'price_total', 200.0 ),
	'tour' => array( $tour, $tour_selection, 'tf_tours_data', 'tour_id', 'price', 200.0 ),
	'apartment' => array( $apt, $apt_selection, 'tf_apartment_data', 'post_id', 'total_price', 160.0 ),
	'car' => array( $car, $car_selection, 'tf_car_data', 'post_id', 'price_total', 200.0 ),
);
$validator = new Validation();
foreach ( $cases as $kind => $case ) {
	list( $id, $selection, $data_key, $id_key, $price_key, $price ) = $case;
	$context = 'car' === $kind ? Car_Booking::quote( $id, $selection ) : Context::$kind( $id, $selection );
	tourfic_fixture_expect( ! is_wp_error( $context ), "$kind valid context" );
	if ( 'car' === $kind && ! is_wp_error( $context ) ) { tourfic_fixture_expect( $context['payable'] === $price, 'Car authoritative price' ); }
	$payload = array( 'order_type' => $kind, $id_key => $id, $price_key => $price, 'tourfic_booking_selection' => $selection );
	if ( 'hotel' === $kind ) { $payload = array_merge( $payload, $selection ); }
	$data = Validation::seal( $kind, array( $data_key => $payload ) );
	$item = array_merge( $data, array( 'product_id' => $id, 'quantity' => 1, 'variation_id' => 0, 'data' => wc_get_product( $id ) ) );
	tourfic_fixture_expect( ! is_wp_error( Validation::validate_item( $item ) ), "$kind signed context" );
	$other_product = 'hotel' === $kind ? $car : $hotel;
	$wrong = $item; $wrong['product_id'] = $other_product; $wrong['data'] = wc_get_product( $other_product );
	tourfic_fixture_expect( is_wp_error( Validation::validate_item( $wrong ) ), "$kind payload cannot override another product type" );
	foreach ( array( '0', '-1', 'NaN', INF, null ) as $bad_price ) {
		$bad = $item; $bad[ $data_key ][ $price_key ] = $bad_price;
		tourfic_fixture_expect( is_wp_error( Validation::validate_item( $bad ) ), "$kind altered total rejected" );
	}
	$bad = $item; unset( $bad['tourfic_booking_quote'] );
	tourfic_fixture_expect( is_wp_error( Validation::validate_item( $bad ) ), "$kind historical unsigned cart rejected" );
	$bad = $item; $bad['quantity'] = 2;
	tourfic_fixture_expect( is_wp_error( Validation::validate_item( $bad ) ), "$kind altered cart quantity rejected" );
	$bad = $item; unset( $bad[ $data_key ] );
	tourfic_fixture_expect( is_wp_error( Validation::validate_item( $bad ) ), "$kind bare placeholder rejected" );
	$restored = $validator->restore_item( $bad );
	tourfic_fixture_expect( ! empty( $restored['tourfic_booking_invalid'] ), "$kind session invalidated" );
	WC()->cart->cart_contents = array( 'fixture' => $item );
	Validation::set_prices( WC()->cart, $kind );
	tourfic_fixture_expect( (float) $item['data']->get_price() === $price, "$kind base price retained" );
	$errors = new WP_Error(); $validator->check_store_cart( $errors, WC()->cart );
	tourfic_fixture_expect( ! $errors->has_errors(), "$kind valid Store API cart" );
	WC()->cart->cart_contents = array( 'fixture' => $bad );
	$errors = new WP_Error(); $validator->check_checkout( array(), $errors );
	tourfic_fixture_expect( $errors->has_errors(), "$kind invalid classic checkout blocked" );
	try { $validator->check_order(); $blocked = false; } catch ( Exception $e ) { $blocked = true; }
	tourfic_fixture_expect( $blocked, "$kind final order guard blocked" );
	wp_update_post( array( 'ID' => $id, 'post_status' => 'draft' ) );
	tourfic_fixture_expect( is_wp_error( Validation::validate_item( $item ) ), "$kind publication changed" );
	wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) );
	$key = array( 'hotel' => 'tf_hotels_opt', 'tour' => 'tf_tours_opt', 'apartment' => 'tf_apartment_opt', 'car' => 'tf_carrental_opt' )[ $kind ];
	$meta = get_post_meta( $id, $key, true ); $changed = $meta; $changed['synthetic_configuration_change'] = 1;
	update_post_meta( $id, $key, $changed );
	tourfic_fixture_expect( is_wp_error( Validation::validate_item( $item ) ), "$kind stale quote rejected" );
	update_post_meta( $id, $key, $meta );
	foreach ( array( 0, -1, '1x', '01', array( $id ), true, 1.5, PHP_INT_MAX . '1' ) as $bad_id ) {
		tourfic_fixture_expect( is_wp_error( Validation::product( $bad_id, $kind ) ), "$kind strict product identifier" );
	}
}
foreach ( array( '2030/02/30', 'invalid', '2030/01/04', '2030/01/05' ) as $bad_start ) {
	$selection = $apt_selection; $selection['check_in'] = $bad_start;
	tourfic_fixture_expect( is_wp_error( Context::apartment( $apt, $selection ) ), 'Apartment malformed/equal/reversed date' );
}
$other = tourfic_fixture_post( 'tf_hotel', 'tf_hotels_opt', array( 'booking-by' => '1' ) );
tourfic_fixture_expect( is_wp_error( Context::hotel( $other, $hotel_selection ) ), 'Unrelated room rejected' );
update_post_meta( $room, 'tf_room_opt', array_diff_key( $room_meta, array( 'tf_hotel' => 1 ) ) );
tourfic_fixture_expect( is_wp_error( Context::hotel( $hotel, $hotel_selection ) ), 'Missing room parent rejected' );
update_post_meta( $room, 'tf_room_opt', $room_meta );
foreach ( array( -1, '1.2', true, array( 1 ) ) as $bad_count ) {
	$selection = $hotel_selection; $selection['adult'] = $bad_count;
	tourfic_fixture_expect( is_wp_error( Context::hotel( $hotel, $selection ) ), 'Hotel malformed occupancy rejected' );
}
$selection = $hotel_selection; $selection['adult'] = 5;
tourfic_fixture_expect( is_wp_error( Context::hotel( $hotel, $selection ) ), 'Hotel capacity rejected' );
$selection = $car_selection; $selection['dropoff_date'] = '2030/01/01';
tourfic_fixture_expect( is_wp_error( Car_Booking::quote( $car, $selection ) ), 'Car reversed range rejected' );
$selection = $car_selection; $selection['pickup_time'] = '25:00';
tourfic_fixture_expect( is_wp_error( Car_Booking::quote( $car, $selection ) ), 'Car malformed time rejected' );
foreach ( array( '', null, -1, 'NaN' ) as $missing_price ) {
	$meta = $car_meta; $meta['car_rent'] = $missing_price; update_post_meta( $car, 'tf_carrental_opt', $meta );
	tourfic_fixture_expect( is_wp_error( Car_Booking::quote( $car, $car_selection ) ), 'Car missing/invalid price rejected' );
}
$meta = $car_meta; $meta['car_rent'] = '0'; update_post_meta( $car, 'tf_carrental_opt', $meta );
$quote = Car_Booking::quote( $car, $car_selection );
tourfic_fixture_expect( ! is_wp_error( $quote ) && 0.0 === $quote['payable'], 'Car explicit free rate retained' );
update_post_meta( $car, 'tf_carrental_opt', $car_meta );
$tour_meta = get_post_meta( $tour, 'tf_tours_opt', true );
$package = array( 'pricing_type' => 'person', 'adult_tabs' => array( 1 => array( 'adult_price' => '90' ) ) );
$meta = $tour_meta; $meta['pricing'] = 'package'; $meta['package_pricing'] = array( 0 => $package );
update_post_meta( $tour, 'tf_tours_opt', $meta );
$selection = $tour_selection; $selection['package'] = 'missing';
tourfic_fixture_expect( is_wp_error( Context::tour( $tour, $selection ) ), 'Unknown tour package rejected' );
$selection['package'] = '0';
tourfic_fixture_expect( ! is_wp_error( Context::tour( $tour, $selection ) ), 'Tour package key zero supported' );
update_post_meta( $tour, 'tf_tours_opt', $tour_meta );
$meta = $room_meta; $meta['avil_by_date'] = 1; $meta['avail_date'] = '{malformed'; update_post_meta( $room, 'tf_room_opt', $meta );
tourfic_fixture_expect( is_wp_error( Context::hotel( $hotel, $hotel_selection ) ), 'Malformed room pricing calendar fails closed' );
update_post_meta( $room, 'tf_room_opt', $room_meta );
$meta = get_post_meta( $apt, 'tf_apartment_opt', true ); $bad = $meta; $bad['enable_availability'] = '1'; $bad['apt_availability'] = '{malformed'; update_post_meta( $apt, 'tf_apartment_opt', $bad );
tourfic_fixture_expect( is_wp_error( Context::apartment( $apt, $apt_selection ) ), 'Malformed apartment pricing calendar fails closed' );
update_post_meta( $apt, 'tf_apartment_opt', $meta );
// Optional car extras and deposits use only declared stored records.
$extra_filter = function( $value, $id, $meta ) { return $meta['extras'] ?? null; };
add_filter( 'tourfic_car_extra_meta', $extra_filter, 10, 3 );
$meta = $car_meta; $meta['extras'] = array( array( 'title' => 'Synthetic extra', 'price' => '5', 'price_type' => 'day' ) );
$meta['protections'] = array( array( 'title' => 'Synthetic protection', 'price' => '7', 'price_by' => 'rental' ) );
update_post_meta( $car, 'tf_carrental_opt', $meta );
$selection = $car_selection + array( 'extra_ids' => array( 0 ), 'extra_qty' => array( 2 ), 'protection' => array( 0 ) );
$quote = Car_Booking::quote( $car, $selection );
tourfic_fixture_expect( ! is_wp_error( $quote ) && 227.0 === $quote['payable'], 'Car extras and protection correct' );
foreach ( array( -1, '1.5', true, array( 1 ) ) as $bad_quantity ) {
 $selection['extra_qty'] = array( $bad_quantity );
 tourfic_fixture_expect( is_wp_error( Car_Booking::quote( $car, $selection ) ), 'Car malformed extra quantity rejected' );
}
$deposit_filter = '__return_true'; add_filter( 'tourfic_allow_deposit_feature', $deposit_filter );
foreach ( array( array( 'percent', '50', 100.0, 100.0 ), array( 'fixed', '25', 25.0, 175.0 ), array( 'fixed', '300', 200.0, 0.0 ) ) as $deposit ) {
 $meta = $car_meta; $meta['deposit_type'] = $deposit[0]; $meta['deposit_amount'] = $deposit[1]; update_post_meta( $car, 'tf_carrental_opt', $meta );
 $quote = Car_Booking::quote( $car, $car_selection + array( 'partial_payment' => 'yes' ) );
 tourfic_fixture_expect( ! is_wp_error( $quote ) && $deposit[2] === $quote['payable'] && $deposit[3] === $quote['due'], 'Car deposit policy retained' );
}
remove_filter( 'tourfic_allow_deposit_feature', $deposit_filter ); remove_filter( 'tourfic_car_extra_meta', $extra_filter );
update_post_meta( $car, 'tf_carrental_opt', $car_meta );
$meta = $car_meta; $meta['car_rent'] = '0'; $meta['regular_price'] = '999'; update_post_meta( $car, 'tf_carrental_opt', $meta );
$quote = Car_Booking::quote( $car, $car_selection );
tourfic_fixture_expect( ! is_wp_error( $quote ) && 0.0 === $quote['payable'], 'Canonical free car price overrides stale legacy price' );
update_post_meta( $car, 'tf_carrental_opt', $car_meta );
tourfic_fixture_expect( 1000.0 === Context::total( '1e3' ), 'Numeric prices normalize before currency formatting' );
// Selected hotel options must be real, including index zero and explicitly free prices.
$mode_filter = function() { return 3; }; add_filter( 'tourfic_room_pricing_mode', $mode_filter );
$meta = $room_meta; $meta['room-options'] = array( array( 'option_pricing_type' => 'per_room', 'option_price' => '0' ) ); update_post_meta( $room, 'tf_room_opt', $meta );
$selection = $hotel_selection; $selection['option_id'] = 'synthetic-room_0';
tourfic_fixture_expect( ! is_wp_error( Context::hotel( $hotel, $selection ) ), 'Explicit free room option retained' );
$selection['option_id'] = 'synthetic-room_9';
tourfic_fixture_expect( is_wp_error( Context::hotel( $hotel, $selection ) ), 'Nonexistent room option rejected' );
remove_filter( 'tourfic_room_pricing_mode', $mode_filter ); update_post_meta( $room, 'tf_room_opt', $room_meta );
// WooCommerce retains ownership of discounts and taxes after the verified booking base price.
WC()->cart->empty_cart();
$data = Validation::seal( 'car', array( 'tf_car_data' => array( 'order_type' => 'car', 'post_id' => $car, 'price_total' => 200, 'tourfic_booking_selection' => $car_selection ) ) );
$key = WC()->cart->add_to_cart( $car, 1, 0, array(), $data );
tourfic_fixture_expect( (bool) $key, 'Verified car enters real WooCommerce cart' );
$coupon = new WC_Coupon(); $coupon->set_code( 'synthetic-' . wp_generate_uuid4() ); $coupon->set_discount_type( 'fixed_cart' ); $coupon->set_amount( 10 ); $coupon->save();
WC()->cart->apply_coupon( $coupon->get_code() ); WC()->cart->calculate_totals();
tourfic_fixture_expect( 190.0 === (float) WC()->cart->get_total( 'edit' ), 'WooCommerce coupon retained' );
WC()->cart->remove_coupons();
update_option( 'woocommerce_calc_taxes', 'yes' ); update_option( 'woocommerce_prices_include_tax', 'no' ); update_option( 'woocommerce_tax_based_on', 'billing' );
WC()->customer->set_billing_country( 'US' ); WC()->customer->set_billing_state( '' ); WC()->customer->set_is_vat_exempt( false );
WC_Tax::_insert_tax_rate( array( 'tax_rate_country' => 'US', 'tax_rate_state' => '', 'tax_rate' => '10.0000', 'tax_rate_name' => 'Synthetic tax', 'tax_rate_priority' => 1, 'tax_rate_compound' => 0, 'tax_rate_shipping' => 0, 'tax_rate_order' => 1, 'tax_rate_class' => '' ) );
WC_Cache_Helper::invalidate_cache_group( 'taxes' ); WC()->cart->calculate_totals();
tourfic_fixture_expect( 220.0 === (float) WC()->cart->get_total( 'edit' ), 'WooCommerce tax retained' );
update_option( 'woocommerce_calc_taxes', 'no' );
// Availability changes invalidate otherwise intact quotes without changing their pricing metadata.
$stock_meta = $car_meta; $stock_meta['car_numbers'] = '1'; update_post_meta( $car, 'tf_carrental_opt', $stock_meta );
$payload = array( 'order_type' => 'car', 'post_id' => $car, 'price_total' => 200, 'tourfic_booking_selection' => $car_selection );
$item = array_merge( Validation::seal( 'car', array( 'tf_car_data' => $payload ) ), array( 'product_id' => $car, 'quantity' => 1, 'data' => wc_get_product( $car ) ) );
$wpdb->insert( $wpdb->prefix . 'tf_order_data', array( 'order_id' => 900001, 'post_id' => $car, 'post_type' => 'car', 'ostatus' => 'completed', 'customer_id' => 0,
 'order_details' => wp_json_encode( array( 'pickup_date' => '2030/01/02', 'dropoff_date' => '2030/01/04', 'pickup_time' => '10:00', 'dropoff_time' => '10:00' ) ) ) );
tourfic_fixture_expect( is_wp_error( Validation::validate_item( $item ) ), 'New car reservation blocks checkout of an intact quote' );
$wpdb->delete( $wpdb->prefix . 'tf_order_data', array( 'order_id' => 900001, 'post_id' => $car ) );
update_post_meta( $car, 'tf_carrental_opt', $car_meta );
$meta = $tour_meta; $meta['tour_availability'] = wp_json_encode( array( array( 'check_in' => '2030/01/02', 'check_out' => '2030/01/02', 'status' => 'available', 'max_capacity' => 2 ) ) );
update_post_meta( $tour, 'tf_tours_opt', $meta );
$payload = array( 'order_type' => 'tour', 'tour_id' => $tour, 'price' => 200, 'tourfic_booking_selection' => $tour_selection );
$item = array_merge( Validation::seal( 'tour', array( 'tf_tours_data' => $payload ) ), array( 'product_id' => $tour, 'quantity' => 1, 'data' => wc_get_product( $tour ) ) );
$wpdb->insert( $wpdb->prefix . 'tf_order_data', array( 'order_id' => 900002, 'post_id' => $tour, 'post_type' => 'tour', 'ostatus' => 'completed', 'customer_id' => 0,
 'order_details' => wp_json_encode( array( 'tour_date' => '2030/01/02', 'adult' => '2', 'child' => '0' ) ) ) );
tourfic_fixture_expect( is_wp_error( Validation::validate_item( $item ) ), 'New tour reservation blocks checkout of an intact quote' );
$wpdb->delete( $wpdb->prefix . 'tf_order_data', array( 'order_id' => 900002, 'post_id' => $tour ) ); update_post_meta( $tour, 'tf_tours_opt', $tour_meta );
class Tourfic_Fixture_Order extends WC_Order { public function get_items( $types = 'line_item' ) { return array( 900011 => (object) array(), 900012 => (object) array(), 900013 => (object) array() ); } }
$orders_filter = function() { return array( new Tourfic_Fixture_Order() ); };
$meta_filter = function( $value, $id, $key ) use ( $hotel, $apt ) {
 if ( ! in_array( $id, array( 900011, 900012, 900013 ), true ) ) { return $value; }
 $record = array( '_post_id' => $hotel, '_unique_id' => 900012 === $id ? 'unrelated-room' : 'synthetic-room',
  'check_in' => 900013 === $id ? 'malformed' : '2030/01/02', 'check_out' => '2030/01/04', 'number_room_booked' => '1' );
 return $record[ $key ] ?? '';
};
add_filter( 'woocommerce_order_query', $orders_filter ); add_filter( 'get_order_item_metadata', $meta_filter, 10, 3 );
$meta = $room_meta; $meta['reduce_num_room'] = 1; update_post_meta( $room, 'tf_room_opt', $meta );
$availability = new \Tourfic\Classes\Room\Availability( $room, '2030/01/02', '2030/01/04' );
tourfic_fixture_expect( 9 === $availability->get_available_count(), 'Room inventory excludes other rooms and malformed historical dates' );
remove_filter( 'get_order_item_metadata', $meta_filter );
$apt_meta_filter = function( $value, $id, $key ) use ( $apt ) {
 if ( ! in_array( $id, array( 900011, 900012, 900013 ), true ) ) { return $value; }
 return array( '_post_id' => $apt, 'check_in_out_date' => '2030/01/02 - 2030/01/04' )[ $key ] ?? '';
};
add_filter( 'get_order_item_metadata', $apt_meta_filter, 10, 3 );
tourfic_fixture_expect( is_wp_error( Context::apartment( $apt, $apt_selection ) ), 'New apartment reservation blocks overlapping selection' );
remove_filter( 'get_order_item_metadata', $apt_meta_filter ); remove_filter( 'woocommerce_order_query', $orders_filter ); update_post_meta( $room, 'tf_room_opt', $room_meta );
// WPML identity resolution is exercised with an isolated adapter, without installing or licensing WPML.
$language_filter = function() { return 'en'; }; add_filter( 'wpml_default_language', $language_filter );
$translated_hotel = tourfic_fixture_post( 'tf_hotel', 'tf_hotels_opt', array( 'booking-by' => '1' ) );
$translated_meta = $room_meta; $translated_meta['tf_hotel'] = $translated_hotel; $translated_meta['unique_id'] = 'translated-room';
$translated_room = tourfic_fixture_post( 'tf_room', 'tf_room_opt', $translated_meta );
$translated_types = function( $value, $type ) { return in_array( $type, array( 'tf_hotel', 'tf_room' ), true ); };
$identity = function( $id, $type ) use ( $translated_hotel, $translated_room, $hotel, $room ) { return $id === $translated_hotel ? $hotel : ( $id === $translated_room ? $room : $id ); };
add_filter( 'wpml_is_translated_post_type', $translated_types, 10, 2 ); add_filter( 'wpml_object_id', $identity, 10, 2 );
$selection = $hotel_selection; $selection['room_id'] = $translated_room;
tourfic_fixture_expect( ! is_wp_error( Context::hotel( $translated_hotel, $selection ) ), 'Translated relationship retained' );
tourfic_fixture_expect( is_wp_error( Context::hotel( $other, $selection ) ), 'Unrelated translated relationship rejected' );
remove_filter( 'wpml_object_id', $identity );
$missing_identity = function( $id, $type ) use ( $translated_room, $identity ) { return $id === $translated_room ? null : $identity( $id, $type ); };
add_filter( 'wpml_object_id', $missing_identity, 10, 2 );
tourfic_fixture_expect( is_wp_error( Context::hotel( $translated_hotel, $selection ) ), 'Missing canonical room does not fall back to caller identity' );
remove_filter( 'wpml_object_id', $missing_identity ); remove_filter( 'wpml_default_language', $language_filter );
tourfic_fixture_expect( false === Validation::canonical( $translated_hotel, 'tf_hotel' ), 'Missing default language fails closed for translated types' );
remove_filter( 'wpml_is_translated_post_type', $translated_types );
WC()->cart->empty_cart();
echo wp_json_encode( array( 'checks' => $checks, 'failures' => $failed ) ) . "\n";
if ( $failed ) { throw new RuntimeException( 'Booking security assertions failed.' ); }
