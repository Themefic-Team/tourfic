<?php
/**
 * Regression checks for runtime helper and template variable contracts.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/runtime-template-contracts.php
 */

$root = dirname( __DIR__, 2 );

function tf_runtime_contract_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$forbidden_patterns = array(
	'inc/Classes/Hotel/Hotel.php'                         => '/(?<![:>])\btfopt\s*\(/',
	'inc/Classes/REST_API/TF_User_Rest_API.php'                => '/(?<![:>])\btfopt\s*\(/',
	'inc/Admin/TF_List_Table.php'                         => '/(?<![:>])\btfopt\s*\(/',
	'templates/template-parts/tour/design-1.php'          => '/\$(?:meta|post_id)\b/',
	'templates/template-parts/tour/design-2.php'          => '/\$(?:meta|gallery_ids)\b/',
	'templates/template-parts/tour/design-legacy.php'     => '/\$(?:meta|comments|disable_review_sec|gallery_ids|email|phone|fax|website|faqs|tour_video|tf_booking_type|tf_booking_url|tf_hide_booking_form|tf_tour_single_book_now_text)\b/',
	'templates/template-parts/tour/design-1/map.php'      => '/\$(?:location|itinerary_map|itineraries)\b/',
	'templates/template-parts/apartment/design-1.php'     => '/\$gallery_ids\b/',
	'templates/template-parts/apartment/design-legacy.php' => '/\$(?:comments|disable_review_sec|map|meta)\b/',
	'templates/template-parts/hotel/design-1.php'         => '/\$(?:meta|post_id)\b/',
	'templates/template-parts/hotel/design-1/faq.php'     => '/\$(?:meta|faqs)\b/',
	'templates/template-parts/room/design-1.php'          => '/\$gallery_ids\b/',
	'templates/template-parts/car/design-1.php'           => '/\$(?:meta|date_format_for_users)\b/',
	'templates/template-parts/archive.php'                => '/\$(?:post_type|taxonomy|taxonomy_slug|loop|tf_defult_views|total_posts)\b/',
);

foreach ( $forbidden_patterns as $file => $pattern ) {
	$source = file_get_contents( $root . '/' . $file );
	tf_runtime_contract_assert( 0 === preg_match( $pattern, $source ), $file . ' still uses a stale runtime dependency.' );
}

$review_source = file_get_contents( $root . '/templates/template-parts/review.php' );
tf_runtime_contract_assert(
	false === strpos( $review_source, 'if ( $comments )' )
		&& false === strpos( $review_source, 'count( $comments )' )
		&& false === strpos( $review_source, 'foreach ( $comments as $comment )' )
		&& false === strpos( $review_source, 'strtotime( $c_date )' ),
	'Review template still reads variables renamed by the prefix refactor.'
);

$enqueue_source = file_get_contents( $root . '/inc/Classes/Enqueue.php' );
tf_runtime_contract_assert(
	false !== strpos( $enqueue_source, "array( 'jquery', 'tf-flatpickr', 'notyf' )" )
		&& false !== strpos( $enqueue_source, "\$tourfic_script_dependencies[] = 'tf-leaflet';" ),
	'Tourfic frontend script dependencies do not guarantee the calendar and map runtimes.'
);

$gallery_source = file_get_contents( $root . '/inc/App/Templates/Components/Shared/Single/Gallery.php' );
tf_runtime_contract_assert(
	false !== strpos( $gallery_source, "'style3' === \$style" )
		&& false !== strpos( $gallery_source, "'tf_apartment' === \$post_type" )
		&& false !== strpos( $gallery_source, '$featured_image_id = (string) get_post_thumbnail_id( $post_id );' )
		&& false !== strpos( $gallery_source, 'array_unshift( $gallery_ids, $featured_image_id );' ),
	'Apartment legacy gallery does not preserve the featured image as its primary image.'
);

echo "Runtime helper and template contract regression checks passed.\n";
