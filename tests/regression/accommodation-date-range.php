<?php
/**
 * Regression checks for accommodation date ranges.
 *
 * Run from the Tourfic Free plugin root:
 * wp eval-file tests/regression/accommodation-date-range.php
 */

function tf_accommodation_date_assert_same( $expected, $actual, $message ) {
	if ( $expected !== $actual ) {
		fwrite(
			STDERR,
			"FAIL: {$message}\nExpected: " . var_export( $expected, true ) . "\nActual: " . var_export( $actual, true ) . "\n"
		);
		exit( 1 );
	}
}

tf_accommodation_date_assert_same(
	true,
	tf_is_valid_accommodation_date_range( '2026/08/04', '2026/08/05' ),
	'A one-night canonical range must be valid.'
);
tf_accommodation_date_assert_same(
	true,
	tf_is_valid_accommodation_date_range( '2026-08-04', '2026-08-10' ),
	'A later checkout must remain valid.'
);
tf_accommodation_date_assert_same(
	false,
	tf_is_valid_accommodation_date_range( '2026/08/04', '2026/08/04' ),
	'A same-day checkout must be rejected.'
);
tf_accommodation_date_assert_same(
	false,
	tf_is_valid_accommodation_date_range( '2026/08/05', '2026/08/04' ),
	'A checkout before check-in must be rejected.'
);
tf_accommodation_date_assert_same(
	true,
	tf_is_valid_accommodation_date_range( '2028/02/29', '2028/03/01' ),
	'Leap-day rollover must remain valid.'
);
tf_accommodation_date_assert_same(
	false,
	tf_is_valid_accommodation_date_range( '2026/02/30', '2026/03/01' ),
	'An invalid calendar date must be rejected.'
);

$root              = dirname( __DIR__, 2 );
$frontend_source   = file_get_contents( $root . '/sass/app/js/free/accommodation-date-range.js' );
$frontend_asset    = file_get_contents( $root . '/assets/app/js/tourfic-scripts.js' );
$frontend_min      = file_get_contents( $root . '/assets/app/js/tourfic-scripts.min.js' );
$hotel_template    = file_get_contents( $root . '/inc/Classes/Hotel/Hotel.php' );
$hotel_booking     = file_get_contents( $root . '/inc/functions/woocommerce/wc-hotel.php' );
$apartment_booking = file_get_contents( $root . '/inc/functions/woocommerce/wc-apartment.php' );

tf_accommodation_date_assert_same(
	true,
	false !== strpos( $frontend_source, 'state.previousCheckout && isAfter(state.previousCheckout, checkIn)' ),
	'The frontend must preserve a checkout that remains after the new check-in.'
);
tf_accommodation_date_assert_same(
	true,
	false !== strpos( $frontend_source, 'runHooks(originalOnChange, this, [effectiveDates, instance.input.value, instance])' ),
	'Existing datepicker callbacks must receive the effective two-date accommodation range.'
);
tf_accommodation_date_assert_same(
	true,
	false !== strpos( $frontend_source, "const accommodationTypes = ['tf_hotel', 'tf_apartment', 'tf_room']" ),
	'The shared controller must be limited to overnight accommodation types.'
);
tf_accommodation_date_assert_same(
	true,
	false !== strpos( $frontend_source, 'data-tf-accommodation-checkin' ),
	'The selected check-in day must be disabled while checkout is pending.'
);
tf_accommodation_date_assert_same(
	true,
	false !== strpos( $frontend_asset, 'tfAccommodationDateRange' ),
	'The normal frontend asset must include the accommodation controller.'
);
tf_accommodation_date_assert_same(
	true,
	false !== strpos( $frontend_min, 'tfAccommodationDateRange' ),
	'The minified frontend asset must include the accommodation controller.'
);
tf_accommodation_date_assert_same(
	false,
	false !== strpos( $hotel_template, "esc_html__( '00', 'tourfic' )" ),
	'The Hotel booking template must not render a zero-day placeholder.'
);
tf_accommodation_date_assert_same(
	1,
	substr_count( $hotel_booking, 'tf_is_valid_accommodation_date_range' ),
	'Hotel and Room bookings must enforce the shared server-side rule.'
);
tf_accommodation_date_assert_same(
	1,
	substr_count( $apartment_booking, 'tf_is_valid_accommodation_date_range' ),
	'Apartment bookings must enforce the shared server-side rule.'
);

echo "Accommodation date-range regression checks passed.\n";
