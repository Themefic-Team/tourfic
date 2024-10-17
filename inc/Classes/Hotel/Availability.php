<?php

namespace Tourfic\Classes\Hotel;

defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Hotel\Pricing;

class Availability extends Pricing {

	function __construct() {
		parent::__construct();
	}

	function is_available() {

	}

	function get_availability_total_price() {
		$room_meta     = $this->room_meta;
		$period        = $this->period;
		$pricing_by    = $room_meta['pricing-by'] ?? 1;
		$avail_by_date = $room_meta['avil_by_date'] ?? 1;

		// Total person calculation
		$persons     = ! empty( $this->persons ) ? $this->persons : array();
		$adult_count = ! empty( $persons['adult'] ) ? $persons['adult'] : 0;
		$child_count = ! empty( $persons['child'] ) ? $persons['child'] : 0;
		$days        = ! empty( $this->days ) ? $this->days : 0;

		$total_price = 0;
		$avail_date  = ! empty( $room_meta['avail_date'] ) ? json_decode( $room_meta['avail_date'], true ) : [];
		foreach ( $period as $date ) {

			$available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
				$date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
				$date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

				return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
			} ) );

			if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {
				$room_price  = ! empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $room_meta['price'];
				$adult_price = ! empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $room_meta['adult_price'];
				$child_price = ! empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $room_meta['child_price'];

				//discount price
				$room_price  = $this->calculate_discount( $room_price );
				$adult_price = $this->calculate_discount( $adult_price );
				$child_price = $this->calculate_discount( $child_price );

				if ( $pricing_by == '1' ) {
					$total_price += $room_price;
				} elseif ( $pricing_by == '2' ) {
					$total_price += ( $adult_price * $adult_count ) + ( $child_price * $child_count );
				} elseif ( $pricing_by == '3' ) {
					$data          = $available_rooms[0];
					$options_count = $data['options_count'] ?? 0;
					for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
						if ( $data[ 'tf_room_option_' . $i ] == '1' && $data[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
							$room_price  = ! empty( $data[ 'tf_option_room_price_' . $i ] ) ? $data[ 'tf_option_room_price_' . $i ] : 0;
							$room_price  = $this->calculate_discount( $room_price );
							$total_price += $room_price;
						} else if ( $data[ 'tf_room_option_' . $i ] == '1' && $data[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
							$adult_price = ! empty( $data[ 'tf_option_adult_price_' . $i ] ) ? $data[ 'tf_option_adult_price_' . $i ] : 0;
							$child_price = ! empty( $data[ 'tf_option_child_price_' . $i ] ) ? $data[ 'tf_option_child_price_' . $i ] : 0;
							$adult_price = $this->calculate_discount( $adult_price );
							$child_price = $this->calculate_discount( $child_price );

							$total_price += ( $adult_price * $adult_count ) + ( $child_price * $child_count );
						}

						//$option_title = $room_option['option_title'];
					}
				}
			};

		}

		$total_price = $total_price * $this->room_number;

		return $total_price;
	}
}