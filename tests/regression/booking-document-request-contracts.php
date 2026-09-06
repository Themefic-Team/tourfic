<?php
/**
 * Regression checks for booking pagination and traveler document requests.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/booking-document-request-contracts.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root                   = dirname( __DIR__, 2 );
$booking_details_source = file_get_contents( $root . '/inc/Core/TF_Booking_Details.php' );
$functions_source       = file_get_contents( $root . '/inc/functions.php' );

function tourfic_booking_document_contract_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

tourfic_booking_document_contract_assert(
	false !== strpos( $booking_details_source, "'paged'     => max( 1, absint( \$page ) )" )
		&& false !== strpos( $booking_details_source, "remove_query_arg( '_wpnonce', \$pagination_url )" )
		&& false !== strpos( $booking_details_source, 'return wp_nonce_url(' )
		&& false !== strpos( $booking_details_source, "'tourfic_filter_bookings_' . \$this->booking_args['menu_slug']" ),
	'Every booking pagination URL must replace the page value and receive a fresh filter nonce.'
);
tourfic_booking_document_contract_assert(
	false === strpos( $booking_details_source, 'parse_str($queryString' ),
	'Booking pagination must rebuild an explicit allowlisted query instead of reflecting the raw query string.'
);
tourfic_booking_document_contract_assert(
	false !== strpos( $booking_details_source, "'list_view'  => \$list_view" )
		&& false !== strpos( $booking_details_source, 'tf_booking_details_pagination( $paged - 1, $pagination_filters )' )
		&& false === strpos( $booking_details_source, "function tf_booking_details_pagination( \$page )" ),
	'Booking pagination must reuse the already verified and sanitized filter state.'
);
tourfic_booking_document_contract_assert(
	false !== strpos( $booking_details_source, 'function tf_current_user_can_access_booking_screen()' )
		&& false !== strpos( $booking_details_source, 'if ( ! $this->tf_current_user_can_access_booking_screen() )' )
		&& false !== strpos( $booking_details_source, 'array_filter(' )
		&& false !== strpos( $booking_details_source, "array( \$this, 'tf_current_user_can_manage_booking' )" ),
	'Booking screens must check the type capability before filtering vendor rows by record ownership.'
);

$download_action = 'tourfic_download_traveler_document';
tourfic_booking_document_contract_assert(
	false !== strpos( $functions_source, "admin-post.php?action={$download_action}&attachment_id=" )
		&& false !== strpos( $functions_source, "add_action( 'admin_post_{$download_action}', '{$download_action}' )" ),
	'The generated traveler document URL and registered authenticated handler must use the same action.'
);
tourfic_booking_document_contract_assert(
	2 === substr_count( $functions_source, "'tourfic_download_traveler_document_' . \$attachment_id" ),
	'The traveler document URL and verifier must share the fully prefixed object-bound nonce action.'
);
tourfic_booking_document_contract_assert(
	false === strpos( $functions_source, "admin_post_nopriv_{$download_action}" )
		&& false !== strpos( $functions_source, "tourfic_tour_user_can_manage_traveler_documents( \$attachment_id )" ),
	'Traveler documents must remain authenticated and object-authorized.'
);
tourfic_booking_document_contract_assert(
	false !== strpos( $functions_source, 'Binary file contents must be sent unchanged.' )
		&& false !== strpos( $functions_source, 'echo $file_contents;' ),
	'Authorized binary downloads must remain byte-for-byte output, not HTML escaped.'
);

echo "Tourfic booking and traveler document request contract checks passed.\n";
