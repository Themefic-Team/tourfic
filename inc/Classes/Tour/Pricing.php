<?php

namespace Tourfic\Classes\Tour;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

class Pricing {
	use \Tourfic\Traits\Singleton;

	protected $post_id;
	protected $meta;
	protected $date;
	protected $time;
	protected $days;
	protected $period;
	protected array $persons;

	public static function instance( $post_id = '') {
		return new self( $post_id);
	}

	function __construct( $post_id = '' ) {
		$this->post_id = $post_id;
		$this->meta = get_post_meta( $post_id, 'tf_tours_opt', true );
	}

	/*
	 * Set date
	 */
	function set_date($date) {
		$this->date = $date;
		return $this;
	}

	/*
	 * Set time
	 */
	function set_time($time){
		$this->time = $time;
		return $this;
	}

	/*
	 * Set persons
	 */
	public function set_persons($adult, $child, $infant) {
		$this->persons = array(
			'adult' => !empty($adult) ? $adult : 0,
			'child' => !empty($child) ? $child : 0,
			'infant' => !empty($infant) ? $infant : 0,
		);

		return $this;
	}

	function get_discount() {
		$room_meta       = get_post_meta( $this->room_id, 'tf_room_opt', true );
		$discount_type   = ! empty( $room_meta["discount_hotel_type"] ) ? $room_meta["discount_hotel_type"] : "none";
		$discount_amount = ( $discount_type == 'fixed' || $discount_type == 'percent' ) && ! empty( $room_meta["discount_hotel_price"] ) ? $room_meta["discount_hotel_price"] : 0;

		return array(
			'discount_type'   => $discount_type,
			'discount_amount' => $discount_amount,
		);
	}

	function calculate_discount( $price ) {
		$discount_arr = $this->get_discount();

		if ( ! empty( $discount_arr ) ) {
			if ( $discount_arr['discount_type'] == 'fixed' ) {
				$price = (int) $price - (int) $discount_arr['discount_amount'];
			} else if ( $discount_arr['discount_type'] == 'percent' ) {
				$price = (int) $price - ( (int) $price * (int) $discount_arr['discount_amount'] ) / 100;
			}
		}

		return $price;
	}

	static function apply_discount( $price, $discount_type, $discount_amount ) {
		if ( $discount_type == 'fixed' ) {
			$price = $price - $discount_amount;
		} else if ( $discount_type == 'percent' ) {
			$price = $price - ( $price * $discount_amount ) / 100;
		}

		return $price;
	}


}