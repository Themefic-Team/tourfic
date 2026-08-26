<?php
/**
 * Regression checks for the Free Tour booking fallback and enquiry wrappers.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/tour-booking-fallback.php
 */

$root     = dirname( __DIR__, 2 );
$pro_root = dirname( $root ) . '/tourfic-pro';

function tf_tour_booking_fallback_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

function tf_tour_booking_fallback_read( $file ) {
	tf_tour_booking_fallback_assert( is_readable( $file ), "Missing fixture: {$file}" );

	return file_get_contents( $file );
}

$free_functions = tf_tour_booking_fallback_read( $root . '/inc/functions.php' );
$pro_functions  = tf_tour_booking_fallback_read( $pro_root . '/inc/functions.php' );
$enquiry        = tf_tour_booking_fallback_read( $root . '/inc/App/Templates/Components/Shared/Single/Enquiry.php' );

tf_tour_booking_fallback_assert(
	false !== strpos( $free_functions, "apply_filters( 'tourfic_tour_booking_type', '1', \$post_id, \$meta )" ),
	'Free does not own WooCommerce as the default Tour booking type.'
);
tf_tour_booking_fallback_assert(
	false === strpos( $free_functions, "function_exists( 'is_tf_pro' )" ),
	'The Free booking resolver must not depend on Tourfic Pro.'
);
tf_tour_booking_fallback_assert(
	false !== strpos( $pro_functions, "add_filter( 'tourfic_tour_booking_type', 'tf_pro_tour_booking_type', 10, 3 )" )
		&& false !== strpos( $pro_functions, "! empty( \$meta['booking-by'] ) ? (string) \$meta['booking-by'] : \$booking_type" ),
	'Pro does not restore its saved Tour booking type through the Free contract.'
);

$free_runtime_files = array(
	'inc/App/Templates/Components/Shared/Single/Booking_Form.php',
	'inc/App/Templates/Components/Tour/Single/Tour_Information.php',
	'inc/App/Templates/Components/Tour/Single/Tour_Price.php',
	'inc/Classes/Tour/Tour.php',
	'inc/functions/woocommerce/wc-tour.php',
	'templates/template-parts/tour/design-1.php',
	'templates/template-parts/tour/design-2.php',
	'templates/template-parts/tour/design-legacy.php',
);

foreach ( $free_runtime_files as $file ) {
	$source = tf_tour_booking_fallback_read( $root . '/' . $file );
	tf_tour_booking_fallback_assert(
		false !== strpos( $source, 'tf_get_tour_booking_type(' ),
		"{$file} bypasses the shared Tour booking type resolver."
	);
}

$status_guard = strpos( $enquiry, "if ( '1' !== (string) \$tf_enquiry_section_status )" );
$wrapper_open = strpos( $enquiry, "echo ! empty( \$wrapper_open )" );
tf_tour_booking_fallback_assert(
	false !== $status_guard && false !== $wrapper_open && $status_guard < $wrapper_open,
	'The Enquiry component can emit an empty caller wrapper while Enquiry is disabled.'
);

echo "Tour booking fallback and enquiry wrapper regression checks passed.\n";
