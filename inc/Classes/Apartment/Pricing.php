<?php

namespace Tourfic\Classes\Apartment;
defined( 'ABSPATH' ) || exit;

class Pricing {

	use \Tourfic\Traits\Singleton;

	public function __construct() {

	}

	protected int $total_price = 500;
	protected $all_fees;
	protected $days;
	protected $period;
	protected $persons;

	// all price will be calculate here
	function get_total_price( $apt_id ) {
		$total_days = $this->days;
		$additional_fees = $this->set_additional_fees( $apt_id )->get_fees();
		$discount_arr = $this->get_discount( $apt_id );
		$total_adult = 1;
		$child_adult = 1;
		$total_infant = 1;

		$total_person = $this->persons;

		if( !empty($discount_arr) ) {
			if($discount_arr['discount_type'] == 'fixed') {
				$this->total_price -= $discount_arr['discount_amount'];
			} else if($discount_arr['discount_type'] == 'percent') {
				$this->total_price -= ( $this->total_price * $discount_arr['discount_amount'] ) / 100;
			}
		}

		if ( $additional_fees > 0 ) {
			$this->total_price += $additional_fees;
		}

		return $this->total_price;
	}

	function set_additional_fees($apt_id) {
		$meta = get_post_meta( $apt_id, 'tf_apartment_opt', true );
		$total_days = $this->days;
		$total_person = $this->persons;
		$additional_fees = !empty( $meta["additional_fees"] ) ? $meta["additional_fees"] : array();

		if( count($additional_fees) > 0 ) {
			foreach($additional_fees as $fee) {
				if($fee['fee_type'] == 'per_night') {
					$this->all_fees += $fee['additional_fee'] * $total_days;
				} else if($fee['fee_type'] == 'per_person') {
					$this->all_fees += $fee['additional_fee'] * $total_person;
				} else {
					$this->all_fees += $fee['additional_fee'];
				}
			}
		}

		return $this;
	}

	public function set_dates($check_in, $check_out) {
		if ( !empty($check_in) && !empty($check_out) ) {
			$check_in_stt  = !empty($check_in) ? strtotime( $check_in . ' +1 day' ) : 0;
			$check_out_stt = !empty($check_out) ? strtotime( $check_out ) : 0; 
			$days = !empty($check_in_stt) && !empty($check_out_stt) ? ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 : 0;

			$tfperiod = new \DatePeriod(
				new \DateTime( $check_in . ' 00:00' ),
				new \DateInterval( 'P1D' ),
				new \DateTime( $check_out . ' 23:59' )
			);
		}
		$this->days = !empty($days) ? $days : 0;
		$this->period = !empty($tfperiod) ? $tfperiod : 0;

		return $this;
	}

	public function set_persons($adult, $child, $infant) {
		$this->persons = (int) $adult + (int) $child + (int) $infant;

		return $this;
	}

	function get_fees() {
		return !empty($this->all_fees) ? $this->all_fees : 0;
	}

	function get_discount($apt_id) {
		$meta = get_post_meta( $apt_id, 'tf_apartment_opt', true );
		$discount_type = !empty($meta["discount_type"]) ? $meta["discount_type"] : '';
		$discount_amount = ( $discount_type == 'fixed' || $discount_type == 'percent' ) && !empty($meta["discount"]) ? $meta["discount"] : 0;

		return array(
			'discount_type' => $discount_type,
			'discount_amount' => $discount_amount,
		);
	}
	
	function get_days() {
		return !empty($this->days) ? $this->days : 0;
	}

	function get_apartment_min_max_price( $post_id = null ) {
		$min_max_price = array();

		$apartment_args = array(
			'post_type'      => 'tf_apartment',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
		);

		if ( isset( $post_id ) && ! empty( $post_id ) ) {
			$apartment_args['post__in'] = array( $post_id );
		}
		$apartment_query = new \WP_Query( $apartment_args );

		if ( $apartment_query->have_posts() ) {
			while ( $apartment_query->have_posts() ) {
				$apartment_query->the_post();
				$meta                = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
				$pricing_type        = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
				$adult_price         = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
				$enable_availability = ! empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';
				if ( $enable_availability === '1' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
					$apt_availability = ! empty( $meta['apt_availability'] ) ? json_decode( $meta['apt_availability'], true ) : [];

					if ( ! empty( $apt_availability ) && is_array( $apt_availability ) ) {
						foreach ( $apt_availability as $single_avail ) {
							if ( $pricing_type === 'per_night' ) {
								$min_max_price[] = ! empty( $single_avail['price'] ) ? intval( $single_avail['price'] ) : 0;

							} else {
								$min_max_price[] = ! empty( $single_avail['adult_price'] ) ? intval( $single_avail['adult_price'] ) : 0;
							}
						}
					}

				} else {
					$min_max_price[] = $pricing_type === 'per_night' && ! empty( $meta['price_per_night'] ) ? intval( $meta['price_per_night'] ) : intval( $adult_price );
				}
			}
		}

		$min_max_price = array_filter($min_max_price);

		wp_reset_query();

		return array(
			'min' => ! empty( $min_max_price ) ? min( $min_max_price ) : 0,
			'max' => ! empty( $min_max_price ) ? max( $min_max_price ) : 0,
		);
	}

}