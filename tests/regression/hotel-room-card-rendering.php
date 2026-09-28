<?php
/**
 * Regression checks for hotel room-card rendering parity.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/hotel-room-card-rendering.php
 */

$root = dirname( __DIR__, 2 );

function tourfic_room_card_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$room_styles = file_get_contents( $root . '/sass/app/css/free/hotel/modules/single/_rooms.scss' );
$hotel_css   = file_get_contents( $root . '/assets/app/css/tourfic-hotel.css' );
$hotel_min   = file_get_contents( $root . '/assets/app/css/tourfic-hotel.min.css' );

tourfic_room_card_assert(
	false !== strpos( $room_styles, 'display: block;' )
		&& false !== strpos( $room_styles, 'overflow-x: visible;' )
		&& false !== strpos( $room_styles, 'width: 100%;' ),
	'The source styles must stack the complete room row on mobile.'
);
tourfic_room_card_assert(
	false === strpos( $room_styles, 'display: table-caption;' ),
	'The mobile layout must not detach only the description cell.'
);
foreach ( array( $hotel_css, $hotel_min ) as $stylesheet ) {
	$compact_stylesheet = preg_replace( '/\s+/', '', $stylesheet );

	tourfic_room_card_assert(
		false !== strpos( $compact_stylesheet, '.tf-availability-table>tbody>tr{display:block;width:100%' )
			&& false !== strpos( $compact_stylesheet, 'width:100%!important' ),
		'Both committed hotel stylesheets must contain the mobile stacked-row rules.'
	);
}

$availability_row = file_get_contents( $root . '/templates/template-parts/hotel/hotel-availability-table-row.php' );
$hotel_handler    = file_get_contents( $root . '/inc/Classes/Hotel/Hotel.php' );
$design_two_start = strpos( $availability_row, '} elseif ( $tf_hotel_selected_template_check == "design-2" ) {' );
$design_one_row   = false !== $design_two_start ? substr( $availability_row, 0, $design_two_start ) : '';

tourfic_room_card_assert(
	false !== strpos( $hotel_handler, '$tf_hotel_selected_template_check = !empty($design) ? $design : $tf_hotel_selected_check;' )
		&& false !== strpos( $availability_row, 'if ( $tf_hotel_selected_template_check == "design-1" ) {' )
		&& false === strpos( $availability_row, '$tourfic_hotel_selected_template_check' ),
	'The AJAX handler and included row template must share the same design variable.'
);

tourfic_room_card_assert(
	'' !== $design_one_row
		&& false !== strpos( $design_one_row, 'echo esc_html( $tourfic_room_term->name )' )
		&& false !== strpos( $design_one_row, '<i class="fas fa-baby"></i>' )
		&& false === strpos( $design_one_row, 'ri-user-smile-line' ),
	'The design-1 AJAX row must retain visible amenity names and design-1 pax icons.'
);

tourfic_room_card_assert(
	0 === preg_match( "/esc_html__\(\s*'(?:(?: \/ )?for )%s nights'/", $availability_row ),
	'Availability rows must not use an always-plural night label.'
);
tourfic_room_card_assert(
	false !== strpos( $availability_row, "_n( 'for %s night', 'for %s nights', \$days, 'tourfic' )" )
		&& false !== strpos( $availability_row, "_n( ' / for %s night', ' / for %s nights', \$days, 'tourfic' )" ),
	'Availability rows must select singular and plural night labels from the stay length.'
);

echo "Hotel room-card rendering regression checks passed.\n";
