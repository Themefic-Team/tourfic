<?php
/**
 * Regression checks for the supported booking check-in AJAX contract.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/ticket-status-contract.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root          = dirname( __DIR__, 2 );
$pro_root      = dirname( $root ) . '/tourfic-pro';
$free_source   = file_get_contents( $root . '/sass/admin/js/free/admin.js' );
$booking_js    = file_get_contents( $root . '/sass/admin/js/free/booking-details.js' );
$free_bundle   = file_get_contents( $root . '/assets/admin/js/tourfic-admin-scripts.js' );
$free_minified = file_get_contents( $root . '/assets/admin/js/tourfic-admin-scripts.min.js' );
$booking_php   = file_get_contents( $root . '/inc/Core/TF_Booking_Details.php' );
$helper_php    = file_get_contents( $root . '/inc/Classes/Helper.php' );
$migrator_php  = file_get_contents( $root . '/inc/Classes/Migrator.php' );
$checkout_php  = file_get_contents( $root . '/inc/functions/woocommerce/wc-tour.php' );
$pro_qr_php    = file_get_contents( $pro_root . '/inc/functions/functions_qr_code.php' );
$pro_routes    = file_get_contents( $pro_root . '/inc/frontend-dashboard/classes/TF_FD_API_Routes.php' );
$pro_rest      = file_get_contents( $pro_root . '/inc/frontend-dashboard/classes/TF_FD_Tour_Rest_API.php' );
$pro_template  = file_get_contents( $pro_root . '/inc/templates/qr-code-scanner.php' );
$pro_bootstrap = file_get_contents( $pro_root . '/tourfic-pro.php' );

function tourfic_ticket_status_contract_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

foreach ( array( $free_source, $free_bundle, $free_minified, $pro_qr_php ) as $legacy_source ) {
	tourfic_ticket_status_contract_assert(
		false === strpos( $legacy_source, 'ticket_status_change' )
			&& false === strpos( $legacy_source, 'tf-ticket-status' ),
		'The retired ticket-status AJAX contract must not remain in Free or Pro runtime code.'
	);
}

tourfic_ticket_status_contract_assert(
	false !== strpos( $booking_php, "wp_ajax_tourfic_checkinout_details_edit" )
		&& false !== strpos( $booking_php, "check_ajax_referer( 'tourfic_manage_bookings', '_ajax_nonce' )" )
		&& false !== strpos( $booking_php, 'tf_get_authorized_ajax_booking_record( $tf_order_id )' )
		&& false !== strpos( $booking_php, "array( 'in', 'out', 'not' )" )
		&& false !== strpos( $booking_php, "isset( \$tf_order_details['unique_id'] )" )
		&& false !== strpos( $booking_php, 'Helper::tourfic_get_booking_order_id_by_unique_id( $tf_order_uni_id )' )
		&& false !== strpos( $booking_php, 'Helper::tourfic_booking_order_matches_unique_id(' )
		&& false !== strpos( $booking_php, 'Helper::tourfic_get_single_tour_booking_unique_id(' )
		&& false === strpos( $booking_php, "get_option( 'tourfic_order_uni_'" )
		&& false !== strpos( $booking_php, 'Helper::tourfic_update_booking_checkin_status( $tf_order_uni_id, $tf_checkinout )' ),
	'The supported booking check-in endpoint must authorize the request and synchronize only an exact, unambiguous ticket.'
);

tourfic_ticket_status_contract_assert(
	false !== strpos( $helper_php, "'tourfic_booking_order_id_' . \$unique_id" )
		&& false !== strpos( $helper_php, "'tourfic_booking_checkin_status_' . \$unique_id" )
		&& false === strpos( $helper_php, 'tourfic_booking_unique_option_name' )
		&& false !== strpos( $helper_php, "itemmeta.meta_key = %s AND itemmeta.meta_value = %s" )
		&& false === strpos( $helper_php, 'SELECT DISTINCT order_items.order_id' )
		&& false === strpos( $helper_php, 'array_unique( array_filter( array_map( \'absint\', (array) $candidate_order_ids ) ) )' )
		&& false !== strpos( $helper_php, "1 !== count( \$candidate_order_ids )" ),
	'Order lookup and check-in state must use distinct keys and ambiguous compatibility recovery must fail closed.'
);

tourfic_ticket_status_contract_assert(
	2 === substr_count( $checkout_php, 'Helper::tourfic_booking_order_id_option_name( $tour_ides )' )
		&& false === strpos( $checkout_php, 'Helper::tourfic_booking_unique_option_name' )
		&& false !== strpos( $migrator_php, 'Helper::tourfic_booking_checkin_status_option_name( $matches[1] )' ),
	'Classic checkout, block checkout, and untouched legacy migrations must write the correct distinct contracts.'
);

tourfic_ticket_status_contract_assert(
	2 === substr_count( $pro_qr_php, "check_ajax_referer( 'tf_ajax_nonce', '_nonce' )" )
		&& false !== strpos( $pro_qr_php, 'tourfic_pro_get_qr_order_id( $qr_id )' )
		&& false === strpos( $pro_qr_php, 'SELECT DISTINCT order_items.order_id' )
		&& false === strpos( $pro_qr_php, 'array_unique( array_filter( array_map( \'absint\', (array) $candidate_order_ids ) ) )' )
		&& false !== strpos( $pro_qr_php, '1 === count( (array) $records ) && $legacy_record_id' )
		&& false !== strpos( $pro_qr_php, 'tourfic_pro_get_qr_checkin_status( $tour_ides )' )
		&& false !== strpos( $pro_qr_php, 'tourfic_pro_check_in_qr_voucher( $tour_ides )' )
		&& 2 <= substr_count( $pro_qr_php, 'tourfic_pro_current_user_can_scan_tour_qr( $tf_post_id )' )
		&& 1 === preg_match( '/\$response\[\'tf_qr_code_result\'\]\s*\.=\s*\'<\/ul>\';\s+break;/', $pro_qr_php )
		&& 1 === preg_match( '/\$response\[\'qr_code_result\'\]\s*=\s*\$tf_quick_response;\s+break;/', $pro_qr_php )
		&& false === strpos( $pro_qr_php, 'get_option( $_POST' )
		&& false === strpos( $pro_qr_php, "update_option( 'tourfic_'" ),
	'Active Pro scanner requests must validate their nonce, resolve exact vouchers, authorize each tour, and avoid the collided key.'
);

tourfic_ticket_status_contract_assert(
	false === strpos( $pro_routes, 'update-ticket-status' )
		&& false === strpos( $pro_rest, 'tf_fd_update_ticket_status' )
		&& false !== strpos( $pro_template, 'tourfic_pro_current_user_can_scan_qr()' )
		&& false !== strpos( $pro_bootstrap, "array( 'jquery', 'tourfic' )" )
		&& false === strpos( $pro_bootstrap, "\$ticket_id ? wc_get_order" ),
	'The orphan REST mutation must stay removed and the active scanner UI/assets must use the authorized contract.'
);

tourfic_ticket_status_contract_assert(
	false !== strpos( $booking_js, "action: 'tourfic_checkinout_details_edit'" )
		&& false !== strpos( $booking_js, '_ajax_nonce: getBookingNonce()' )
		&& false !== strpos( $free_bundle, "action: 'tourfic_checkinout_details_edit'" ),
	'The booking-details source and committed bundle must call the supported endpoint with its action-specific nonce.'
);

echo "Tourfic ticket-status AJAX contract regression checks passed.\n";
