<?php
/**
 * Regression checks for the Free Tour booking fallback and enquiry wrappers.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/tour-booking-fallback.php
 */

$root     = dirname( __DIR__, 2 );
$pro_root = getenv( 'TOURFIC_PRO_ROOT' ) ?: dirname( $root ) . '/tourfic-pro';

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

$free_booking = tf_tour_booking_fallback_read( $root . '/inc/functions/woocommerce/wc-tour.php' );
$free_form    = tf_tour_booking_fallback_read( $root . '/inc/App/Templates/Components/Shared/Single/Booking_Form.php' );
$pro_owner    = tf_tour_booking_fallback_read( $pro_root . '/inc/classes/TF_Pro_Booking_Modes.php' );
$enquiry      = tf_tour_booking_fallback_read( $root . '/inc/App/Templates/Components/Shared/Single/Enquiry.php' );

tf_tour_booking_fallback_assert(
	false !== strpos( $free_booking, "'tourfic_tour_booking_mode_response'" )
		&& false !== strpos( $free_form, "apply_filters( 'tourfic_tour_booking_form_visibility', true" ),
	'Free does not expose neutral Tour booking contracts with WooCommerce defaults.'
);
tf_tour_booking_fallback_assert(
	false === strpos( $free_booking . $free_form, 'booking-by' )
		&& false === strpos( $free_booking . $free_form, 'is_tf_pro' ),
	'Free must not interpret the saved Pro Tour booking mode.'
);
tf_tour_booking_fallback_assert(
	false !== strpos( $pro_owner, "add_filter( 'tourfic_tour_booking_mode_response'" )
		&& false !== strpos( $pro_owner, "\$meta['booking-by']" ),
	'Pro does not restore its saved Tour booking mode through the neutral Free contracts.'
);

$status_guard = strpos( $enquiry, "if ( '1' !== (string) \$tf_enquiry_section_status )" );
$wrapper_open = strpos( $enquiry, "echo ! empty( \$wrapper_open )" );
tf_tour_booking_fallback_assert(
	false !== $status_guard && false !== $wrapper_open && $status_guard < $wrapper_open,
	'The Enquiry component can emit an empty caller wrapper while Enquiry is disabled.'
);

echo "Tour booking fallback and enquiry wrapper regression checks passed.\n";
