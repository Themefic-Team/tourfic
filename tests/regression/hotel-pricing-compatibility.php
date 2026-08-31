<?php
/**
 * Regression checks for legacy Hotel room pricing compatibility.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/hotel-pricing-compatibility.php
 */

namespace Tourfic\Traits {
	trait Singleton {
	}
}

namespace {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );

	$GLOBALS['tourfic_hotel_pricing_meta']    = array();
	$GLOBALS['tourfic_hotel_pricing_updates'] = array();

	function get_post_meta( $post_id, $key, $single = false ) {
		unset( $single );

		return $GLOBALS['tourfic_hotel_pricing_meta'][ $post_id ][ $key ] ?? array();
	}

	function update_post_meta( $post_id, $key, $value ) {
		$GLOBALS['tourfic_hotel_pricing_updates'][ $post_id ][ $key ] = $value;

		return true;
	}

	function absint( $value ) {
		return abs( (int) $value );
	}

	function tourfic_hotel_pricing_assert( $condition, $message ) {
		if ( ! $condition ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only diagnostics.
			echo "FAIL: {$message}\n";
			exit( 1 );
		}
	}

	require_once ABSPATH . 'inc/Classes/Room/Room.php';

	$fixtures = array(
		'person-adult-fallback' => array(
			'pricing-by'  => '2',
			'price'       => '',
			'adult_price' => '350',
			'child_price' => '245',
		),
		'person-child-fallback' => array(
			'pricing-by'  => '2',
			'price'       => '',
			'adult_price' => '',
			'child_price' => '245',
		),
		'option-room-fallback'  => array(
			'pricing-by'  => '3',
			'price'       => '',
			'room-options' => array(
				array(
					'option_pricing_type' => 'per_room',
					'option_price'        => '500',
				),
			),
		),
		'option-person-fallback' => array(
			'pricing-by'  => '3',
			'price'       => '',
			'room-options' => array(
				array(
					'option_pricing_type' => 'per_person',
					'option_adult_price'  => '425',
					'option_child_price'  => '250',
				),
			),
		),
	);
	$expected_prices = array( '350', '245', '500', '425' );

	foreach ( array_values( $fixtures ) as $index => $fixture ) {
		$post_id = $index + 1;
		$GLOBALS['tourfic_hotel_pricing_meta'][ $post_id ]['tf_room_opt'] = $fixture;

		$normalized = \Tourfic\Classes\Room\Room::get_normalized_room_meta( $post_id, true );

		tourfic_hotel_pricing_assert(
			$expected_prices[ $index ] === $normalized['price'],
			'Legacy advanced metadata did not receive the expected Room Basis fallback.'
		);
		tourfic_hotel_pricing_assert(
			$fixture['pricing-by'] === $normalized['pricing-by'],
			'The saved advanced pricing mode was overwritten.'
		);
		tourfic_hotel_pricing_assert(
			$normalized === $GLOBALS['tourfic_hotel_pricing_updates'][ $post_id ]['tf_room_opt'],
			'The normalized fallback was not persisted.'
		);
	}

	$existing_price = array(
		'pricing-by'  => '2',
		'price'       => '725',
		'adult_price' => '350',
	);
	$GLOBALS['tourfic_hotel_pricing_meta'][10]['tf_room_opt'] = $existing_price;
	$normalized = \Tourfic\Classes\Room\Room::get_normalized_room_meta( 10, true );
	tourfic_hotel_pricing_assert( '725' === $normalized['price'], 'An existing Room Basis price was replaced.' );
	tourfic_hotel_pricing_assert(
		empty( $GLOBALS['tourfic_hotel_pricing_updates'][10] ),
		'Unchanged metadata was written unnecessarily.'
	);

	$hotel_source = file_get_contents( ABSPATH . 'inc/Classes/Hotel/Hotel.php' );
	$popup_source = file_get_contents( ABSPATH . 'inc/App/Without_Payment/Hotel_Offline_Booking.php' );
	$row_source   = file_get_contents( ABSPATH . 'templates/template-parts/hotel/hotel-availability-table-row.php' );
	$pro_source   = file_get_contents( dirname( ABSPATH ) . '/tourfic-pro/inc/functions.php' );

	tourfic_hotel_pricing_assert(
		false !== strpos( $hotel_source, 'Room::get_normalized_room_meta( $room_id, true )' ),
		'Hotel availability does not consume normalized room metadata.'
	);
	tourfic_hotel_pricing_assert(
		false !== strpos( $hotel_source, "apply_filters( 'tourfic_room_pricing_mode', 1, \$room )" ),
		'Free Hotel availability does not default to Room Basis.'
	);
	tourfic_hotel_pricing_assert(
		false !== strpos( $popup_source, 'Room::get_normalized_room_meta( $room_id, true )' )
			&& false !== strpos( $popup_source, 'is_numeric( $total_price ) ? (float) $total_price : 0.0' ),
		'Hotel popup normalization or numeric guard is missing.'
	);
	tourfic_hotel_pricing_assert(
		false !== strpos( $row_source, 'isset( $price ) && is_numeric( $price )' )
			&& false !== strpos( $row_source, 'isset( $d_price ) && is_numeric( $d_price )' ),
		'Availability-row price inputs are not normalized at the include boundary.'
	);
	tourfic_hotel_pricing_assert(
		false !== strpos( $pro_source, "add_filter( 'tourfic_room_pricing_mode'" )
			&& false !== strpos( $pro_source, "\$room_meta['pricing-by']" ),
		'Tourfic Pro no longer restores the saved advanced pricing mode.'
	);

	echo "Hotel pricing compatibility checks passed.\n";
}
