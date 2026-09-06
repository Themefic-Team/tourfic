<?php
/**
 * Regression coverage for normalized public-search consumers.
 *
 * Run with: php tests/regression/public-search-consumer-contracts.php
 */

$tourfic_root = dirname( __DIR__, 2 );

function tourfic_search_consumer_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

function tourfic_search_consumer_read( $root, $relative_path ) {
	$contents = file_get_contents( $root . '/' . $relative_path );
	tourfic_search_consumer_assert( false !== $contents, "Could not read {$relative_path}" );

	return $contents;
}

$request_boundary = tourfic_search_consumer_read( $tourfic_root, 'inc/functions.php' );
tourfic_search_consumer_assert(
	false !== strpos( $request_boundary, "function tourfic_get_public_search_request()" )
	&& false !== strpos( $request_boundary, "wp_verify_nonce( \$nonce, 'tourfic_public_search' )" )
	&& false !== strpos( $request_boundary, "'type'                     => 'post_type'" ),
	'Public search input must pass through the nonce-verified, schema-based request boundary.'
);

$normalized_consumers = array(
	'inc/App/Widgets/TF_Widgets/Apartment_Features_Filter.php',
	'inc/App/Widgets/TF_Widgets/Apartment_Type_Filter.php',
	'inc/App/Widgets/TF_Widgets/Car_Brand_Filter.php',
	'inc/App/Widgets/TF_Widgets/Car_Category_Filter.php',
	'inc/App/Widgets/TF_Widgets/Car_Connectivity_Filter.php',
	'inc/App/Widgets/TF_Widgets/Car_Engine_Filter.php',
	'inc/App/Widgets/TF_Widgets/Car_Fueltype_Filter.php',
	'inc/App/Widgets/TF_Widgets/Car_Seat_Range_Filter.php',
	'inc/App/Widgets/TF_Widgets/Car_Transmission_Filter.php',
	'inc/App/Widgets/TF_Widgets/Hotel_Feature_Filter.php',
	'inc/App/Widgets/TF_Widgets/Hotel_Type_Filter.php',
	'inc/App/Widgets/TF_Widgets/Map_Filter.php',
	'inc/App/Widgets/TF_Widgets/Price_Filter.php',
	'inc/App/Widgets/TF_Widgets/Room_Type_Filter.php',
	'inc/App/Widgets/TF_Widgets/Similar_Tours.php',
	'inc/App/Widgets/TF_Widgets/Tour_Activities_Filter.php',
	'inc/App/Widgets/TF_Widgets/Tour_Attraction_Filter.php',
	'inc/App/Widgets/TF_Widgets/Tour_Feature_Filter.php',
	'inc/App/Widgets/TF_Widgets/Tour_Type_Filter.php',
	'templates/common/search-results.php',
	'templates/template-parts/car/design-1.php',
	'templates/template-parts/search/design-1.php',
	'templates/template-parts/search/design-legacy.php',
	'templates/template-parts/search/design-2.php',
	'templates/template-parts/search/design-3.php',
);

foreach ( $normalized_consumers as $relative_path ) {
	$contents = tourfic_search_consumer_read( $tourfic_root, $relative_path );
	tourfic_search_consumer_assert(
		false !== strpos( $contents, 'tourfic_get_public_search_request()' ),
		"{$relative_path} must consume the normalized public search request."
	);
	tourfic_search_consumer_assert(
		false === strpos( $contents, '$_GET' ),
		"{$relative_path} must not read raw GET input."
	);
}

foreach ( array( 'templates/template-parts/search/design-2.php', 'templates/template-parts/search/design-3.php' ) as $relative_path ) {
	$contents = tourfic_search_consumer_read( $tourfic_root, $relative_path );
	tourfic_search_consumer_assert(
		false !== strpos( $contents, "wp_nonce_field( 'tourfic_public_search', 'tourfic_search_nonce', false )" ),
		"{$relative_path} must render the public search nonce in its GET form."
	);
}

$server_nonce_form_sources = array(
	'inc/App/Shortcodes/Search_Result.php'                => 4,
	'inc/App/Templates/Components/Apartment/Archive/Listings.php' => 2,
	'inc/App/Templates/Components/Hotel/Archive/Listings.php'     => 3,
	'inc/App/Templates/Components/Room/Archive/Listings.php'      => 1,
	'inc/App/Templates/Components/Tour/Archive/Listings.php'      => 3,
	'inc/Classes/Apartment/Apartment.php'           => 4,
	'inc/Classes/Car_Rental/Car_Rental.php'         => 4,
	'inc/Classes/Helper.php'                        => 7,
	'inc/Classes/Hotel/Hotel.php'                   => 4,
	'inc/Classes/Room/Room.php'                     => 5,
	'inc/Classes/Tour/Tour.php'                     => 4,
	'templates/template-parts/archive.php'           => 1,
	'templates/template-parts/search/design-2.php' => 1,
	'templates/template-parts/search/design-3.php' => 1,
);

foreach ( $server_nonce_form_sources as $relative_path => $expected_count ) {
	$contents = tourfic_search_consumer_read( $tourfic_root, $relative_path );
	tourfic_search_consumer_assert(
		$expected_count === substr_count( $contents, "wp_nonce_field( 'tourfic_public_search', 'tourfic_search_nonce', false )" ),
		"{$relative_path} must render a nonce in every public search form."
	);
}

$similar_tours = tourfic_search_consumer_read( $tourfic_root, 'inc/App/Widgets/TF_Widgets/Similar_Tours.php' );
tourfic_search_consumer_assert(
	false !== strpos( $similar_tours, "'tourfic_search_nonce' => wp_create_nonce( 'tourfic_public_search' )" ),
	'Similar-tour search links must carry a fresh public search nonce.'
);

echo "Public search consumer contract regression checks passed.\n";
