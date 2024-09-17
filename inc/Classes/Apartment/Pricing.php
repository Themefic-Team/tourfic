<?php

namespace Tourfic\Classes\Apartment;
defined( 'ABSPATH' ) || exit;

class Pricing {

	public static function instance($apt_id) {
		return new self($apt_id);
	}

	
	protected float $total_price = 0; // float
	protected $all_fees;
	protected $days;
	protected $period;
	protected array $persons;
	protected $apt_price;
	protected $adult_price;
	protected $adult_discount_price;
	protected $child_price;
	protected $child_discount_price;
	protected $infant_price;
	protected $infant_discount_price;
	protected $meta;
	protected $apt_id;
	protected $availability_price;
	protected $checkin;
	protected $checkout;

	public function __construct($apt_id) {
		$this->apt_id = $apt_id;
		$this->meta = get_post_meta( $this->apt_id, 'tf_apartment_opt', true );
	}
	

	// all price will be calculate here
	function set_total_price() {
		$apartment_price = $this->set_apartment_price()->get_apartment_price();
		$additional_fees = $this->set_additional_fees()->get_fees();
		$discount_arr = $this->get_discount();

		// need to call the set_dates function before this
		$total_days = !empty( $this->days) ? $this->days : 0;

		if( !empty($apartment_price ) ) {
			$this->total_price += $apartment_price;
		}

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

		return $this;
	}

	public function set_apartment_price() {
		$meta = $this->meta;
		$pricing_type = !empty($meta["pricing_type"]) ? $meta["pricing_type"] : 'per_night';
		$discount_arr = $this->get_discount();
		$adult_price = !empty($meta["adult_price"]) ? $meta["adult_price"] : 0;
		$child_price = !empty($meta["child_price"]) ? $meta["child_price"] : 0;
		$infant_price = !empty($meta["infant_price"]) ? $meta["infant_price"] : 0;
		$per_night_price = !empty($meta["price_per_night"]) ? $meta["price_per_night"] : 0;

		// Total person calculation
		$persons = !empty($this->persons) ? $this->persons : array();
		$adult_count = !empty($persons['adult']) ? $persons['adult'] : 0;
		$child_count = !empty($persons['child']) ? $persons['child'] : 0;
		$infant_count = !empty($persons['infant']) ? $persons['infant'] : 0;
		$days = !empty($this->days) ? $this->days : 0;


		if( $pricing_type == 'per_night' ) {
			$this->apt_price += $per_night_price * $days;
		} else if( $pricing_type == 'per_person' ) {
			$this->adult_price = $adult_price * $adult_count;
			$this->child_price = $child_price * $child_count;
			$this->infant_price = $infant_price * $infant_count;

			$this->apt_price += ($this->adult_price + $this->child_price + $this->infant_price) * $days;
		}

		if( !empty($discount_arr) ) {
			if($discount_arr['discount_type'] == 'fixed') {
				$this->adult_discount_price = $this->adult_price - $discount_arr['discount_amount'];
				$this->child_discount_price = $this->child_price - $discount_arr['discount_amount'];
				$this->infant_discount_price = $this->infant_price - $discount_arr['discount_amount'];
			} else if($discount_arr['discount_type'] == 'percent') {
				$this->adult_discount_price = $this->adult_price - (( $this->adult_price * $discount_arr['discount_amount'] ) / 100);
				$this->child_discount_price = $this->child_price - (( $this->child_price * $discount_arr['discount_amount'] ) / 100);
				$this->infant_discount_price = $this->infant_price - (( $this->infant_price * $discount_arr['discount_amount'] ) / 100);
			}
		}

		return $this;
		
	}

	function get_availability() {
		$adult = !empty( $this->persons["adult"]) ? $this->persons["adult"] : 0;
		$child = !empty( $this->persons["child"]) ? $this->persons["child"] : 0;
		$infant = !empty( $this->persons["infant"]) ? $this->persons["infant"] : 0;
		$discount_type = !empty( $this->meta["discount_type"]) ? $this->meta["discount_type"] : 'none';

		// get total availability price
		$price = Availability::instance($this->apt_id)->set_dates($this->checkin, $this->checkout)->set_persons( $adult, $child, $infant)->get_total_price();
		
		// discount calculation
		if ( $discount_type != 'none' ) {
			$price = $this->calculate_discount( $price );
		}

		// additional fees calculation
		$price+= $this->set_dates($this->checkin, $this->checkout)->set_persons( $adult, $child, $infant )->set_additional_fees()->get_fees();

		return $price;
	}


	function set_additional_fees() {
		$meta = $this->meta;
		$total_days = $this->days;
		$total_person = !empty( $this->persons ) ? array_sum( $this->persons ) : 0;
		$additional_fees = !empty( $meta["additional_fees"] ) ? $meta["additional_fees"] : array();
		
		// if free version
		$additional_fee = ! empty( $meta["additional_fee"] ) ? $meta["additional_fee"] : 0;
		$additional_fee_type   = ! empty( $meta["fee_type"] ) ? $meta["fee_type"] : '';


		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
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
		} else {
			if($additional_fee_type == 'per_night') {
				$this->all_fees += $additional_fee * $total_days;
			} else if($additional_fee_type == 'per_person') {
				$this->all_fees += $additional_fee * $total_person;
			} else {
				$this->all_fees += $additional_fee;
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
		$this->checkin = !empty( $check_in ) ? $check_in : '';
		$this->checkout = !empty( $check_out ) ? $check_out : '';

		return $this;
	}

	public function set_persons($adult, $child, $infant) {
		$this->persons = array(
			'adult' => !empty($adult) ? $adult : 0,
			'child' => !empty($child) ? $child : 0,
			'infant' => !empty($infant) ? $infant : 0,
		);

		return $this;
	}

	function calculate_discount( $price ) {
		$discount_arr = $this->get_discount();

		if( !empty($discount_arr) ) {
			if($discount_arr['discount_type'] == 'fixed') {
				$price = (int) $price - (int) $discount_arr['discount_amount'];
			} else if($discount_arr['discount_type'] == 'percent') {
				$price = (int) $price - ( (int) $price * (int) $discount_arr['discount_amount'] ) / 100;
			}
		}

		return $price;
	}

	function get_fees() {
		return !empty($this->all_fees) ? $this->all_fees : 0;
	}

	public function get_persons() {
		return !empty($this->persons) ? $this->persons : array();
	}

	function get_discount() {
		$meta = $this->meta;
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

	function get_apartment_price() {
		return !empty($this->apt_price) ? $this->apt_price : 0;
	}

	function get_total_price() {
		return !empty($this->total_price) ? $this->total_price : 0;
	}

	function get_total_price_html() {
		return !empty($this->total_price) ? wc_price( $this->total_price ) : wc_price( 0 );
	}

	function get_adult_price() {
		return !empty($this->adult_price) ? $this->adult_price : 0;
	}
	
	function get_adult_sale_price() {
		return !empty($this->adult_discount_price) ? $this->adult_discount_price : 0;
	}

	function get_child_price() {
		return !empty($this->child_price) ? $this->child_price : 0;
	}

	function get_child_sale_price() {
		return !empty($this->child_discount_price) ? $this->child_discount_price : 0;
	}

	function get_infant_price() {
		return !empty($this->infant_price) ? $this->infant_price : 0;
	}

	function get_infant_sale_price() {
		return !empty($this->infant_discount_price) ? $this->infant_discount_price : 0;
	}

	function get_min_max_price() {
		$min_max_price = array();

		$meta                = $this->meta;
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

		$min_max_price = array_filter($min_max_price);

		return array(
			'min' => ! empty( $min_max_price ) ? min( $min_max_price ) : 0,
			'max' => ! empty( $min_max_price ) ? max( $min_max_price ) : 0,
		);
	}

	public static function get_min_max_price_from_all_apartment() {
		$min_max_price = array();
		
		$apt_args = array(
			'post_type' => 'tf_apartment',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		);

		$query = new \WP_Query( $apt_args );

		if( $query->have_posts() ) {
			while( $query->have_posts() ) {
				$query->the_post();
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

		if ( ! empty( $min_max_price ) && count( $min_max_price ) > 1 ) {
			$max_price = max( $min_max_price );
			$min_price = min( $min_max_price );
			if ( $max_price == $min_price ) {
				$min_price = 1;
			}
		}
		if ( ! empty( $min_max_price ) && count( $min_max_price ) == 1 ) {
			$max_price = max( $min_max_price );
			$min_price = 1;
		}
		if ( empty( $min_max_price ) ) {
			$max_price = 0;
			$min_price = 0;
		}

		return array(
			'max' => $max_price,
			'min' => $min_price,
		);
	}

}