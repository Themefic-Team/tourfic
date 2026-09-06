<?php
/**
 * Regression checks for booking email and traveler-edit request contracts.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/booking-email-security.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root                  = dirname( __DIR__, 2 );
$pro_root              = dirname( $root ) . '/tourfic-pro';
$email_source          = file_get_contents( $root . '/inc/Admin/Emails/TF_Handle_Emails.php' );
$booking_source        = file_get_contents( $root . '/inc/Core/TF_Booking_Details.php' );
$booking_script        = file_get_contents( $root . '/sass/admin/js/free/booking-details.js' );
$pro_email_source      = file_get_contents( $pro_root . '/inc/classes/Advanced_Email_Handler.php' );
$pro_dashboard_source  = file_get_contents( $pro_root . '/inc/frontend-dashboard/classes/TF_Frontend_Dashboard.php' );
$pro_booking_script    = file_get_contents( $pro_root . '/src/jsx/components/Booking/Bookings.js' );
$pro_details_script    = file_get_contents( $pro_root . '/src/jsx/components/Booking/BookingsDetails.js' );
$pro_dashboard_bundle  = file_get_contents( $pro_root . '/build/dashboard.js' );

function tourfic_booking_email_security_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$resend_start = strpos( $email_source, 'public function tf_order_status_email_resend_function()' );
$resend_end   = strpos( $email_source, 'private function send_basic_booking_email', $resend_start );
$resend_body  = substr( $email_source, $resend_start, $resend_end - $resend_start );

tourfic_booking_email_security_assert(
	false !== strpos( $resend_body, "check_ajax_referer( 'tourfic_manage_bookings', '_ajax_nonce' )" ),
	'Email resend must use the booking-management nonce.'
);
tourfic_booking_email_security_assert(
	false !== strpos( $resend_body, "SELECT * FROM {\$wpdb->prefix}tf_order_data WHERE id = %d" )
		&& false !== strpos( $resend_body, "\$order_id !== absint( \$order_data['order_id'] )" )
		&& false !== strpos( $resend_body, "current_user_can( 'edit_post', \$post_id )" )
		&& false !== strpos( $resend_body, "in_array( 'tf_vendor', (array) \$current_user->roles, true )" ),
	'Email resend must bind both IDs to one object-authorized booking record.'
);
tourfic_booking_email_security_assert(
	strpos( $resend_body, 'SELECT * FROM' ) < strpos( $resend_body, "apply_filters( 'tourfic_use_companion_email_templates'" ),
	'Free must authorize the booking before dispatching a companion resend hook.'
);
tourfic_booking_email_security_assert(
	false !== strpos( $booking_script, "action: 'tourfic_order_status_email_resend'" )
		&& false !== strpos( $booking_script, '_ajax_nonce: getBookingNonce()' )
		&& false !== strpos( $booking_script, 'if (data.success)' )
		&& false !== strpos( $booking_script, 'Unable to resend this email.' ),
	'The booking screen must send the matching nonce and surface resend failures.'
);

$offline_start = strpos( $email_source, 'public function offline_replace_mail_tags' );
$offline_end   = strpos( $email_source, 'public function replace_mail_tags', $offline_start );
$offline_body  = substr( $email_source, $offline_start, $offline_end - $offline_start );

tourfic_booking_email_security_assert(
	false !== strpos( $offline_body, '$booking_contexts = array(' )
		&& false !== strpos( $offline_body, "admin_url( 'edit.php' )" )
		&& false === strpos( $offline_body, "'book_id'" )
		&& false === strpos( $offline_body, "'action'    => 'preview'" ),
	'Offline email links must lead to the authorized booking list, not a user-bound direct-preview nonce.'
);

$pro_resend_start = strpos( $pro_email_source, 'public function resend_email' );
$pro_resend_end   = strpos( $pro_email_source, 'private function send_woocommerce_event', $pro_resend_start );
$pro_resend_body  = substr( $pro_email_source, $pro_resend_start, $pro_resend_end - $pro_resend_start );

tourfic_booking_email_security_assert(
	false !== strpos( $pro_resend_body, "absint( \$order_data['order_id'] ) !== absint( \$order_id )" )
		&& false !== strpos( $pro_resend_body, "current_user_can( 'edit_post', \$post_id )" )
		&& false !== strpos( $pro_resend_body, "in_array( 'tf_vendor', (array) \$current_user->roles, true )" ),
	'Pro must independently reject mismatched or unauthorized resend records.'
);

tourfic_booking_email_security_assert(
	false !== strpos( $pro_dashboard_source, "'booking_nonce'" )
		&& false !== strpos( $pro_dashboard_source, "wp_create_nonce( 'tourfic_manage_bookings' )" )
		&& false !== strpos( $pro_booking_script, 'tfDashboardObj.booking_nonce' )
		&& false !== strpos( $pro_details_script, 'tfDashboardObj.booking_nonce' )
		&& false !== strpos( $pro_dashboard_bundle, 'booking_nonce' ),
	'Pro dashboard booking mutations must use the nonce required by Free.'
);

tourfic_booking_email_security_assert(
	false !== strpos( $booking_source, 'wp_send_json_error( $tf_visitor_details->get_error_message(), 400 )' )
		&& false !== strpos( $booking_source, "current_user_can( 'manage_options' ) &&" )
		&& false !== strpos( $booking_script, 'Unable to update traveler details.' ),
	'Admin-only traveler editing must hide unavailable controls and report validation failures.'
);

echo "Tourfic booking email security regression checks passed.\n";
