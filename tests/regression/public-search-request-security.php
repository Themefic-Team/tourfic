<?php
/**
 * Regression coverage for public search request nonces.
 *
 * Run with: php tests/regression/public-search-request-security.php
 */

$tourfic_root = dirname( __DIR__, 2 );

function tourfic_public_search_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

function tourfic_public_search_read( $root, $relative_path ) {
	$contents = file_get_contents( $root . '/' . $relative_path );
	tourfic_public_search_assert( false !== $contents, "Could not read {$relative_path}" );

	return $contents;
}

$enqueue = tourfic_public_search_read( $tourfic_root, 'inc/Classes/Enqueue.php' );
tourfic_public_search_assert(
	false !== strpos( $enqueue, "'search_nonce'           => wp_create_nonce( 'tourfic_public_search' )" ),
	'Frontend configuration must expose an action-specific public search nonce.'
);

$frontend_source = tourfic_public_search_read( $tourfic_root, 'sass/app/js/free/tourfic.js' );
tourfic_public_search_assert(
	false !== strpos( $frontend_source, "name: 'tourfic_search_nonce'" ),
	'Public search forms must submit the localized nonce.'
);
tourfic_public_search_assert(
	false !== strpos( $frontend_source, "'form.tf-archive-ordering, ' +" ),
	'Archive ordering forms must preserve the public search nonce.'
);

$request_boundary = tourfic_public_search_read( $tourfic_root, 'inc/functions.php' );
tourfic_public_search_assert(
	false !== strpos( $request_boundary, 'function tourfic_get_public_search_request()' )
	&& false !== strpos( $request_boundary, "wp_verify_nonce( \$nonce, 'tourfic_public_search' )" )
	&& false !== strpos( $request_boundary, "apply_filters( 'tourfic_public_search_request_schema', \$schema )" ),
	'Public search input must pass through the nonce-verified, extensible schema boundary.'
);

$normalized_sources = array(
	'inc/App/Shortcodes/Search_Result.php',
	'inc/App/Templates/Components/Room/Single/Room_Options.php',
	'inc/App/Templates/Components/Shared/Single/Booking_Form.php',
	'inc/App/Templates/Components/Shared/Single/Sticky_Nav.php',
	'inc/App/Widgets/TF_Widgets/Car_Connectivity_Filter.php',
	'inc/Classes/Apartment/Apartment.php',
	'inc/Classes/Car_Rental/Car_Rental.php',
	'inc/Classes/Helper.php',
	'inc/Classes/Hotel/Hotel.php',
	'inc/Classes/Room/Room.php',
	'inc/Classes/Tour/Tour.php',
	'inc/functions/functions-car.php',
	'templates/common/search-results.php',
	'templates/template-parts/search/design-2.php',
	'templates/template-parts/search/design-3.php',
);

foreach ( $normalized_sources as $relative_path ) {
	$contents = tourfic_public_search_read( $tourfic_root, $relative_path );
	tourfic_public_search_assert(
		false !== strpos( $contents, 'tourfic_get_public_search_request()' )
		&& false === strpos( $contents, '$_GET' ),
		"{$relative_path} must use the centralized request boundary instead of raw GET data."
	);
}

$redirect_sources = array(
	'inc/Classes/Apartment/Apartment.php',
	'inc/Classes/Hotel/Hotel.php',
	'inc/Classes/Room/Room.php',
	'inc/Classes/Tour/Tour.php',
	'inc/functions/functions-car.php',
);

foreach ( $redirect_sources as $relative_path ) {
	$contents = tourfic_public_search_read( $tourfic_root, $relative_path );
	tourfic_public_search_assert(
		false !== strpos( $contents, "\$fields['tourfic_search_nonce'] = wp_create_nonce( 'tourfic_public_search' );" ),
		"{$relative_path} must mint a fresh public nonce for its search-result redirect."
	);
}

echo "Public search request security regression checks passed.\n";
