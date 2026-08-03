<?php
/**
 * Regression checks for zero-price Tour Extras.
 *
 * Run from the Tourfic Free plugin root:
 * wp eval-file tests/regression/tour-zero-price-extras.php
 */

use Tourfic\Classes\Helper;

function tf_tour_extra_assert_same( $expected, $actual, $message ) {
	if ( $expected !== $actual ) {
		fwrite(
			STDERR,
			"FAIL: {$message}\nExpected: " . var_export( $expected, true ) . "\nActual: " . var_export( $actual, true ) . "\n"
		);
		exit( 1 );
	}
}

$valid_extras = array(
	2  => array( 'title' => 'Included guidebook', 'price' => 0, 'price_type' => 'fixed' ),
	7  => array( 'title' => 'Included map', 'price' => '0', 'price_type' => 'person' ),
	11 => array( 'title' => 'Included snack', 'price' => '0.00', 'price_type' => 'quantity' ),
	18 => array( 'title' => 'Paid lunch', 'price' => '25', 'price_type' => 'fixed' ),
);

foreach ( $valid_extras as $extra ) {
	tf_tour_extra_assert_same(
		true,
		Helper::tf_tour_extra_is_valid( $extra ),
		'Numeric, non-negative Tour Extra prices must be valid.'
	);
}

$invalid_extras = array(
	array( 'title' => 'Blank price', 'price' => '' ),
	array( 'title' => 'Whitespace price', 'price' => ' ' ),
	array( 'title' => 'Negative price', 'price' => '-1' ),
	array( 'title' => 'Malformed price', 'price' => 'free' ),
	array( 'title' => 'Missing price' ),
	array( 'title' => '', 'price' => '0' ),
	'not-an-array',
);

foreach ( $invalid_extras as $extra ) {
	tf_tour_extra_assert_same(
		false,
		Helper::tf_tour_extra_is_valid( $extra ),
		'Incomplete or malformed Tour Extras must remain invalid.'
	);
}

$filtered_extras = array_filter( $valid_extras, array( Helper::class, 'tf_tour_extra_is_valid' ) );
tf_tour_extra_assert_same( 4, count( $filtered_extras ), 'Filtering must retain both free and paid Tour Extras.' );
tf_tour_extra_assert_same(
	array( 2, 7, 11, 18 ),
	array_keys( $filtered_extras ),
	'Filtering must preserve saved Tour Extra keys used by booking selections.'
);

$root               = dirname( __DIR__, 2 );
$tour_source        = file_get_contents( $root . '/inc/Classes/Tour/Tour.php' );
$woocommerce_source = file_get_contents( $root . '/inc/functions/woocommerce/wc-tour.php' );
$backend_source     = file_get_contents( $root . '/inc/Admin/Backend_Booking/TF_Tour_Backend_Booking.php' );
$pro_source_path    = dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Tour_Backend_Booking_Rest_API.php';

tf_tour_extra_assert_same(
	3,
	substr_count( $tour_source, 'tf_tour_extra_is_valid' ),
	'The popup, frontend calculation, and booking summary must use the shared validity rule.'
);
tf_tour_extra_assert_same(
	1,
	substr_count( $woocommerce_source, 'tf_tour_extra_is_valid' ),
	'WooCommerce booking must use the shared validity rule.'
);
tf_tour_extra_assert_same(
	2,
	substr_count( $backend_source, 'tf_tour_extra_is_valid' ),
	'Free backend booking must use the shared validity rule.'
);
if ( file_exists( $pro_source_path ) ) {
	$pro_source = file_get_contents( $pro_source_path );
	tf_tour_extra_assert_same(
		5,
		substr_count( $pro_source, 'tf_tour_extra_is_valid' ),
		'Pro backend booking must validate both consumers and retain its older-Free compatibility fallback.'
	);
}

echo "Zero-price Tour Extra regression checks passed.\n";
