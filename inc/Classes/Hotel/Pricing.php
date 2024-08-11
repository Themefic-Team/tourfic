<?php

namespace Tourfic\Classes\Hotel;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Room;

class Pricing {

	//private static $instance;
	protected $post_id;
	protected $room_id;
	protected $meta;
	protected $room_meta;
	protected $checkin;
	protected $checkout;
	protected $days;
	protected $period;
	protected array $persons;
	protected $room_number;

	public static function instance( $post_id = '', $room_id = '' ) {
		return new self( $post_id, $room_id );
	}

	public function __construct( $post_id, $room_id ) {
		$this->post_id   = $post_id;
		$this->room_id   = $room_id;
		$this->meta      = get_post_meta( $post_id, 'tf_hotels_opt', true );
		$this->room_meta = get_post_meta( $room_id, 'tf_room_opt', true );
	}

	public function set_dates( $check_in, $check_out ) {
		$room_meta       = $this->room_meta;
		$price_multi_day = ! empty( $room_meta['price_multi_day'] ) ? $room_meta['price_multi_day'] : false;

		if ( ! empty( $check_in ) && ! empty( $check_out ) ) {

			if ( ! $price_multi_day ) {
				$check_in_stt = strtotime( $check_in );

				$period = new \DatePeriod(
					new \DateTime( $check_in . ' 00:00' ),
					new \DateInterval( 'P1D' ),
					new \DateTime( $check_out . ' 23:59' )
				);
			} else {
				$check_in_stt = strtotime( $check_in . ' +1 day' );

				$period = new \DatePeriod(
					new \DateTime( $check_in . ' 00:00' ),
					new \DateInterval( 'P1D' ),
					new \DateTime( $check_out . ' 00:00' )
				);
			}
			$check_out_stt = strtotime( $check_out );
			$days          = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
		}

		$this->days     = ! empty( $days ) ? $days : 0;
		$this->period   = ! empty( $period ) ? $period : 0;
		$this->checkin  = ! empty( $check_in ) ? $check_in : '';
		$this->checkout = ! empty( $check_out ) ? $check_out : '';

		return $this;
	}

	public function set_persons( $adult, $child ) {
		$this->persons = array(
			'adult' => ! empty( $adult ) ? $adult : 0,
			'child' => ! empty( $child ) ? $child : 0,
		);

		return $this;
	}

	public function set_room_number( $room_number ) {
		$this->room_number = $room_number;

		return $this;
	}

	function get_discount( $room_id ) {
		$room_meta       = get_post_meta( $room_id, 'tf_room_opt', true );
		$discount_type   = ! empty( $room_meta["discount_hotel_type"] ) ? $room_meta["discount_hotel_type"] : "none";
		$discount_amount = ( $discount_type == 'fixed' || $discount_type == 'percent' ) && ! empty( $room_meta["discount_hotel_price"] ) ? $room_meta["discount_hotel_price"] : 0;

		return array(
			'discount_type'   => $discount_type,
			'discount_amount' => $discount_amount,
		);
	}

	function calculate_discount( $price, $room_id ) {
		$discount_arr = $this->get_discount( $room_id );

		if ( ! empty( $discount_arr ) ) {
			if ( $discount_arr['discount_type'] == 'fixed' ) {
				$price = $price - $discount_arr['discount_amount'];
			} else if ( $discount_arr['discount_type'] == 'percent' ) {
				$price = $price - ( $price * $discount_arr['discount_amount'] ) / 100;
			}
		}

		return $price;
	}

	function get_min_max_price() {
		$room_price = [];
		$rooms      = Room::get_hotel_rooms( $this->post_id );
		if ( ! empty( $rooms ) ) {
			foreach ( $rooms as $room ) {
				$room_meta     = get_post_meta( $room->ID, 'tf_room_opt', true );
				$pricing_by    = $room_meta['pricing-by'] ?? 1;
				$avail_by_date = $room_meta['avil_by_date'] ?? 1;

				if ( $avail_by_date == "1" ) {
					$avail_date = json_decode( $room_meta['avail_date'], true );
					if ( ! empty( $avail_date ) && is_array( $avail_date ) ) {
						foreach ( $avail_date as $singleavailroom ) {
							if ( ! empty( $this->period ) ) {
								foreach ( $this->period as $date ) {
									$singleavailroom_date = date( 'Y-m-d', $date->getTimestamp() );
									if ( $singleavailroom['date'] == $singleavailroom_date ) {
										if ( $pricing_by == 1 ) {
											$room_meta_price = $singleavailroom['price'] ?? 0;
											$discount_price  = $this->calculate_discount( $room_meta_price, $room->ID );

											$room_price[] = [
												"regular_price" => $room_meta_price,
												"sale_price"    => $discount_price
											];
										} elseif ( $pricing_by == 2 ) {
											$adult_price          = $singleavailroom['adult_price'] ?? 0;
											$discount_adult_price = $this->calculate_discount( $adult_price, $room->ID );

											$room_price[] = [
												"regular_price" => $adult_price,
												"sale_price"    => $discount_adult_price
											];
										}
									}
								}
							} else {
								if ( $pricing_by == 1 ) {
									$room_meta_price = $singleavailroom['price'] ?? 0;
									$discount_price  = $this->calculate_discount( $room_meta_price, $room->ID );

									$room_price[] = [
										"regular_price" => $room_meta_price,
										"sale_price"    => $discount_price
									];
								} elseif ( $pricing_by == 2 ) {
									$adult_price          = $singleavailroom['adult_price'] ?? 0;
									$discount_adult_price = $this->calculate_discount( $adult_price, $room->ID );

									$room_price[] = [
										"regular_price" => $adult_price,
										"sale_price"    => $discount_adult_price
									];
								}
							}
						}
					}
				} else {
					if ( $pricing_by == 1 ) {
						$room_meta_price = $room_meta['price'] ?? 0;
						$discount_price  = $this->calculate_discount( $room_meta_price, $room->ID );

						$room_price[] = [
							"regular_price" => $room_meta_price,
							"sale_price"    => $discount_price
						];
					} elseif ( $pricing_by == 2 ) {
						$adult_price          = $room_meta['adult_price'] ?? 0;
						$discount_adult_price = $this->calculate_discount( $adult_price, $room->ID );

						$room_price[] = [
							"regular_price" => $adult_price,
							"sale_price"    => $discount_adult_price
						];
					} elseif ( $pricing_by == 3 ) {
						$room_options = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];

						if ( ! empty( $room_options ) ) {
							foreach ( $room_options as $room_option ) {
								$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
								if ( $option_price_type == 'per_room' ) {
									$room_meta_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
									$discount_price  = $this->calculate_discount( $room_meta_price, $room->ID );

									$room_price[] = [
										"regular_price" => $room_meta_price,
										"sale_price"    => $discount_price
									];
								} elseif ( $option_price_type == 'per_person' ) {
									$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
									$discount_price     = $this->calculate_discount( $option_adult_price, $room->ID );

									$room_price[] = [
										"regular_price" => $option_adult_price,
										"sale_price"    => $discount_price
									];
								}
							}
						}
					}
				}
			}
		}

		// Get min and max price
		return array(
			'min' => array(
				'regular_price' => ! empty( $room_price ) ? min( array_column( $room_price, 'regular_price' ) ) : 0,
				'sale_price'    => ! empty( $room_price ) ? min( array_column( $room_price, 'sale_price' ) ) : 0,
			),
			'max' => array(
				'regular_price' => ! empty( $room_price ) ? max( array_column( $room_price, 'regular_price' ) ) : 0,
				'sale_price'    => ! empty( $room_price ) ? max( array_column( $room_price, 'sale_price' ) ) : 0,
			)
		);
	}

	function get_hotel_price() {
		$meta      = $this->meta;
		$room_meta = $this->room_meta;

		$pricing_by = ! empty( $room_meta['pricing-by'] ) ? $room_meta['pricing-by'] : '1';

		// Total person calculation
		$persons     = ! empty( $this->persons ) ? $this->persons : array();
		$adult_count = ! empty( $persons['adult'] ) ? $persons['adult'] : 0;
		$child_count = ! empty( $persons['child'] ) ? $persons['child'] : 0;
		$days        = ! empty( $this->days ) ? $this->days : 0;

		if ( $pricing_by == '1' ) {

			$total_price = $room_meta['price'];
			$total_price = $this->calculate_discount( $total_price, $this->room_id );
		} elseif ( $pricing_by == '2' ) {
			$adult_price = $room_meta['adult_price'];
			$child_price = $room_meta['child_price'];

			$adult_price = (int) $adult_price * (int) $adult_count;
			$child_price = (int) $child_price * (int) $child_count;
			$total_price = (int) $adult_price + (int) $child_price;
			$total_price = $this->calculate_discount( $total_price, $this->room_id );
		} elseif ( $pricing_by == '3' ) {
			$room_options = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];

			if ( ! empty( $room_options ) ) {
				foreach ( $room_options as $room_option_key => $room_option ) {
					$_option_id = $unique_id . '_' . $room_option_key;
					if ( $_option_id == $option_id ) {
						$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
						if ( $option_price_type == 'per_room' ) {
							$total_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
						} elseif ( $option_price_type == 'per_person' ) {
							$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
							$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;
							$total_price        = ( $option_adult_price * $adult_count ) + ( $option_child_price * $child_count );
						}

						if ( ! empty( $room_option['room-facilities'] ) ) {
							foreach ( $room_option['room-facilities'] as $room_facility ) {
								$facility_price_switch = ! empty( $room_facility['room_facilities_price_switch'] ) ? $room_facility['room_facilities_price_switch'] : '0';
								$facility_price        = ! empty( $room_facility['room_facilities_price'] ) ? floatval( $room_facility['room_facilities_price'] ) : 0;
								$facility_type         = ! empty( $room_facility['room_facilities_price_type'] ) ? $room_facility['room_facilities_price_type'] : 'per_person';

								if ( $facility_price_switch == '1' ) {
									switch ( $facility_type ) {
										case 'per_person':
											$total_price += ( $facility_price * $adult_count ) + ( $facility_price * $child_count );
											break;
										case 'per_night':
											$total_price += $facility_price * $days;
											break;
										case 'per_stay':
											$total_price += $facility_price;
											break;
									}
								}
							}
						}

						$total_price = $this->calculate_discount( $total_price, $this->room_id );
					}
				}
			}
		}

		return $total_price;
	}

	function get_total_price() {

	}

	static function get_min_max_price_from_all_hotel() {
		$hotel_args    = array(
			'posts_per_page' => - 1,
			'post_type'      => 'tf_hotel',
			'post_status'    => 'publish'
		);
		$hotel_query   = new \WP_Query( $hotel_args );
		$min_max_price = array();

		if ( $hotel_query->have_posts() ):
			while ( $hotel_query->have_posts() ) : $hotel_query->the_post();
				$rooms = Room::get_hotel_rooms( get_the_ID() );
				if ( ! empty( $rooms ) ) {
					foreach ( $rooms as $_room ) {
						$room_meta = get_post_meta( $_room->ID, 'tf_room_opt', true );
						if ( ! empty( $room_meta['price'] ) ) {
							$min_max_price[] = $room_meta['price'];
						}
						if ( ! empty( $room_meta['adult_price'] ) ) {
							$min_max_price[] = $room_meta['adult_price'];
						}
						if ( ! empty( $room_meta['child_price'] ) ) {
							$min_max_price[] = $room_meta['child_price'];
						}
						if ( ! empty( $room_meta['avail_date'] ) ) {
							$avail_date = json_decode( $room_meta['avail_date'], true );
							if ( ! empty( $avail_date ) && is_array( $avail_date ) ) {
								foreach ( $avail_date as $singleavailroom ) {
									if ( ! empty( $singleavailroom['price'] ) ) {
										$min_max_price[] = $singleavailroom['price'];
									}
									if ( ! empty( $singleavailroom['adult_price'] ) ) {
										$min_max_price[] = $singleavailroom['adult_price'];
									}
									if ( ! empty( $singleavailroom['child_price'] ) ) {
										$min_max_price[] = $singleavailroom['child_price'];
									}
								}
							}
						}
					}
				}
			endwhile;
		endif;
		wp_reset_query();

		return array(
			'min' => ! empty( $min_max_price ) ? min( $min_max_price ) : 0,
			'max' => ! empty( $min_max_price ) ? max( $min_max_price ) : 0,
		);
	}
}