<?php
/**
 * Static regression checks for Patchstack negative booking count handling.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/security/booking-negative-count-security.php
 */

$root = dirname( __DIR__, 2 );

$files = array(
	'hotel'     => $root . '/inc/functions/woocommerce/wc-hotel.php',
	'tour'      => $root . '/inc/functions/woocommerce/wc-tour.php',
	'apartment' => $root . '/inc/functions/woocommerce/wc-apartment.php',
	'car'       => $root . '/inc/functions/woocommerce/wc-car.php',
);

foreach ( $files as $label => $file ) {
	if ( ! is_readable( $file ) ) {
		fwrite( STDERR, "Missing fixture {$label}: {$file}\n" );
		exit( 1 );
	}
}

function tf_booking_security_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

function tf_booking_security_file( $file ) {
	return file_get_contents( $file );
}

function tf_booking_security_function_body( $source, $function ) {
	$offset = strpos( $source, 'function ' . $function );
	tf_booking_security_assert( false !== $offset, "Function {$function} not found." );

	$brace = strpos( $source, '{', $offset );
	tf_booking_security_assert( false !== $brace, "Function {$function} has no body." );

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

	tf_booking_security_assert( false, "Function {$function} body is not balanced." );
}

$tour_source      = tf_booking_security_file( $files['tour'] );
$hotel_source     = tf_booking_security_file( $files['hotel'] );
$apartment_source = tf_booking_security_file( $files['apartment'] );
$car_source       = tf_booking_security_file( $files['car'] );

$tour_booking = tf_booking_security_function_body( $tour_source, 'tf_tours_booking_function' );
tf_booking_security_assert(
	false !== strpos( $tour_source, "wp_ajax_nopriv_tf_tours_booking" ),
	'Tour booking must remain a public booking endpoint.'
);
tf_booking_security_assert(
	false !== strpos( $tour_booking, '0 > $adults || 0 > $children || 0 > $infant' ),
	'Tour booking must reject negative traveler counts.'
);
tf_booking_security_assert(
	false === strpos( $tour_booking, 'absint(' ),
	'Tour booking must not convert negative traveler counts with absint().'
);

$hotel_booking = tf_booking_security_function_body( $hotel_source, 'tf_hotel_booking_callback' );
tf_booking_security_assert(
	false !== strpos( $hotel_source, "wp_ajax_nopriv_tf_hotel_booking" ),
	'Hotel booking must remain a public booking endpoint.'
);
tf_booking_security_assert(
	false !== strpos( $hotel_booking, '0 > $adult || 0 > $child' ),
	'Hotel booking must reject negative guest counts.'
);
tf_booking_security_assert(
	false !== strpos( $hotel_booking, '0 > $room_selected' ),
	'Hotel booking must reject negative room counts.'
);
tf_booking_security_assert(
	false === strpos( $hotel_booking, 'absint(' ),
	'Hotel booking must not convert negative guest counts with absint().'
);

$apartment_validation = tf_booking_security_function_body(
	$apartment_source,
	'tf_apartment_get_booking_validation_errors'
);
tf_booking_security_assert(
	false !== strpos( $apartment_source, "wp_ajax_nopriv_tf_apartment_booking" ),
	'Apartment booking must remain a public booking endpoint.'
);
tf_booking_security_assert(
	false !== strpos( $apartment_validation, '0 > $adults || 0 > $children || 0 > $infant' ),
	'Apartment booking must reject negative guest counts.'
);
tf_booking_security_assert(
	false === strpos( $apartment_validation, 'absint(' ),
	'Apartment booking must not convert negative guest counts with absint().'
);

$car_booking = tf_booking_security_function_body( $car_source, 'tf_car_booking_callback' );
tf_booking_security_assert(
	false !== strpos( $car_source, "wp_ajax_nopriv_tf_car_booking" ),
	'Car booking must remain a public booking endpoint.'
);
tf_booking_security_assert(
	false !== strpos( $car_booking, '0 > intval( $single_extra_qty )' ),
	'Car booking must reject negative extra quantities.'
);

$tour_price      = tf_booking_security_function_body( $tour_source, 'tf_tours_set_order_price' );
$hotel_price     = tf_booking_security_function_body( $hotel_source, 'tf_hotel_set_order_price' );
$apartment_price = tf_booking_security_function_body( $apartment_source, 'tf_aprtment_set_order_price' );
$car_price       = tf_booking_security_function_body( $car_source, 'tf_car_set_order_price' );

tf_booking_security_assert(
	false !== strpos( $tour_price, 'max( 0, $tour_price )' ),
	'Tour cart price setter must not apply negative totals.'
);
tf_booking_security_assert(
	false !== strpos( $hotel_price, "max( 0, \$cart_item['tf_hotel_data']['price_total'] )" ),
	'Hotel cart price setter must not apply negative totals.'
);
tf_booking_security_assert(
	false !== strpos( $apartment_price, "max( 0, \$cart_item['tf_apartment_data']['total_price'] )" ),
	'Apartment cart price setter must not apply negative totals.'
);
tf_booking_security_assert(
	false !== strpos( $car_price, "max( 0, \$cart_item['tf_car_data']['price_total'] )" ),
	'Car cart price setter must not apply negative totals.'
);

echo "Booking negative count security regression checks passed.\n";
