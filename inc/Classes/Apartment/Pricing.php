<?php

namespace Tourfic\Classes\Apartment;
defined( 'ABSPATH' ) || exit;

class Pricing {

	use \Tourfic\Traits\Singleton;

	public function __construct() {

	}

	protected int $total_price = 500;
	protected array $all_fees;
	protected $days;
	protected $period;

	// all price will be calculate here
	function get_total_price( $apt_id ) {
		$total_days = $this->check_days('2024/06/26', '2024/06/30')->get_days();
		$additional_fees = $this->check_additional_fees( $apt_id )->get_additional_fees();
		$discount_arr = $this->get_discount( $apt_id );
		$total_adult = 1;
		$child_adult = 1;
		$total_infant = 1;

		$total_person = $total_adult + $child_adult + $total_infant;

		if( !empty($discount_arr) ) {
			if($discount_arr['discount_type'] == 'fixed') {
				$this->total_price -= $discount_arr['discount_amount'];
			} else if($discount_arr['discount_type'] == 'percent') {
				$this->total_price -= ( $this->total_price * $discount_arr['discount_amount'] ) / 100;
			}
		}

		if( count($additional_fees) > 0 ) {
			foreach($additional_fees as $fee) {
				if($fee['type'] == 'per_night') {
					$this->total_price += $fee['fee'] * $total_days;
				} else if($fee['type'] == 'per_person') {
					$this->total_price += $fee['fee'] * $total_person;
				} else {
					$this->total_price += $fee['fee'];
				}
			}
		}

		return $this->total_price;
	}

	function check_additional_fees($apt_id) {
		$meta = get_post_meta( $apt_id, 'tf_apartment_opt', true );
		$additional_fees = !empty( $meta["additional_fees"] ) ? $meta["additional_fees"] : array();

		$additional_feesss = [];
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($additional_fees)) {
			if ( count( $additional_fees ) > 0 ) {
				foreach ( $additional_fees as $fees ) {
					$this->all_fees[] = array(
						"label" => !empty($fees["additional_fee_label"]) ? $fees["additional_fee_label"] : '',
						"fee"   => !empty($fees["additional_fee"]) ? $fees["additional_fee"] : 0,
						"price" =>!empty($fees["additional_fee"]) ? wc_price( $fees["additional_fee"] ) : wc_price( 0 ),
						"type"  => !empty( $fees["fee_type"] ) ? $fees["fee_type"] : '',
					);
				}
			}
		} else {
			$additional_fee_label = !empty( $meta[ "additional_fee_label" ] ) ? $meta["additional_fee_label"] : '';
			$additional_fee_amount = !empty( $meta["additional_fee"] ) ? $meta["additional_fee"] : 0;
			$additional_fee_type = !empty( $meta[ "fee_type" ] ) ? $meta["fee_type"] : '';

			if($additional_fee_amount != 0) {
				$this->all_fees[] = array(
					"label" => $additional_fee_label ?? "",
					"fee"   => $additional_fee_amount,
					"price" => wc_price( $additional_fee_amount ),
					"type"  => $additional_fee_type,
				);
			}
		}

		return $this;
	}

	public function check_days($check_in, $check_out) {
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

	function get_additional_fees() {
		return !empty($this->all_fees) ? $this->all_fees : [];
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