<?php

namespace Tourfic\Classes\Tour;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

class Pricing {

	protected $post_id;
	protected $meta;
	protected $date;
	protected $time;
	protected $days;
	protected $period;
	protected array $persons;

	public static function instance( $post_id = '' ) {
		return new self( $post_id );
	}

	function __construct( $post_id = '' ) {
		$this->post_id = $post_id;
		$this->meta    = get_post_meta( $post_id, 'tf_tours_opt', true );
	}

	/*
	 * Set date
	 */
	function set_date( $date ) {
		$this->date = $date;

		return $this;
	}

	/*
	 * Set time
	 */
	function set_time( $time ) {
		$this->time = $time;

		return $this;
	}

	/*
	 * Set persons
	 */
	public function set_persons( $adult, $child, $infant ) {
		$this->persons = array(
			'adult'  => ! empty( $adult ) ? $adult : 0,
			'child'  => ! empty( $child ) ? $child : 0,
			'infant' => ! empty( $infant ) ? $infant : 0,
		);

		return $this;
	}

	function get_discount() {
		$meta            = $this->meta;
		$allow_discount    = ! empty( $meta['allow_discount'] ) ? $meta['allow_discount'] : '';
		$discount_type   = !empty($allow_discount) && ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
		$discount_amount = ( $discount_type == 'fixed' || $discount_type == 'percent' ) && ! empty( $meta["discount_price"] ) ? $meta["discount_price"] : 0;

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

	function get_min_price( $period = '' ) {
		$tour_price                       = [];
		$meta                             = $this->meta;
		$pricing_rule                     = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$disable_adult_price              = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child_price              = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$tour_archive_page_price_settings = ! empty( Helper::tfopt( 'tour_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'tour_archive_price_minimum_settings' ) : 'adult';


		$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
		$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
		$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
		$infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;

		$tour_availability_data = isset( $meta['tour_availability'] ) && ! empty( $meta['tour_availability'] ) ? json_decode( $meta['tour_availability'], true ) : [];
		
		$package_pricing = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : '';

		if(!empty($tour_availability_data) && Helper::is_all_unavailable($tour_availability_data)){
			foreach ($tour_availability_data as $data) {
				if ($data['status'] !== 'available') {
					continue;
				}

				if($pricing_rule == 'person' && $data['pricing_type'] == 'person'){
					// Adult Price
					if (!empty($data['adult_price'])) {
						$tour_price[] = $data['adult_price'];
					} else if(!empty($adult_price)){
						$tour_price[] = $adult_price;
					}

					// Child Price
					if (!empty($data['child_price'])) {
						$tour_price[] = $data['child_price'];
					} else if(!empty($children_price)){
						$tour_price[] = $children_price;
					}

					// Infant Price
					if (!empty($data['infant_price'])) {
						$tour_price[] = $data['infant_price'];
					} else if(!empty($infant_price)){
						$tour_price[] = $infant_price;
					}
				}

				if($pricing_rule == 'group' && $data['pricing_type'] == 'group'){
					// Group Price
					if (!empty($data['price'])) {
						$tour_price[] = $data['price'];
					} else if(!empty($group_price)){
						$tour_price[] = $group_price;
					}
				}
				
				if( $pricing_rule == 'package' && $data['pricing_type'] == 'package'){
					if(!empty($data['options_count'])){
						for($i = 0; $i < $data['options_count']; $i++){

							if (!empty($data['tf_option_adult_price_'.$i])) {
								$tour_price[] = $data['tf_option_adult_price_'.$i];
							}

							if (!empty($data['tf_option_child_price_'.$i])) {
								$tour_price[] = $data['tf_option_child_price_'.$i];
							}

							if (!empty($data['tf_option_infant_price_'.$i])) {
								$tour_price[]= $data['tf_option_infant_price_'.$i];
							}

							if (!empty($data['tf_option_group_price_'.$i])) {
								$tour_price[] = $data['tf_option_group_price_'.$i];
							}

						}
					}
				}
			}
		}else{
			if($pricing_rule == 'person'){
				// Adult Price
				if(!empty($adult_price)){
					$tour_price[] = $adult_price;
				}

				// Child Price
				if(!empty($children_price)){
					$tour_price[] = $children_price;
				}

				// Infant Price
				if(!empty($infant_price)){
					$tour_price[] = $infant_price;
				}
			}
			if($pricing_rule == 'group'){
				if(!empty($group_price)){
					$tour_price[] = $group_price;
				}
			}
			if($pricing_rule == 'package' && !empty($package_pricing)){
				foreach($package_pricing as $package){
					if (!empty($package['adult_tabs'][1]['adult_price'])) {
						$tour_price[] = $package['adult_tabs'][1]['adult_price'];
					}

					if (!empty($package['child_tabs'][1]['child_price'])) {
						$tour_price[] = $package['child_tabs'][1]['child_price'];
					}

					if (!empty($package['infant_tabs'][1]['infant_price'])) {
						$tour_price[] = $package['infant_tabs'][1]['infant_price'];
					}

					if (!empty($package['group_tabs'][1]['group_price'])) {
						$tour_price[] = $package['group_tabs'][1]['group_price'];
					}
				}
			}
		}

		//get the lowest price from all available room price
		$tf_tour_min_price      = !empty($tour_price) ? min( $tour_price ) : 0;
		$tf_tour_full_price     = !empty($tour_price) ? min( $tour_price ) : 0;
		$allow_discount    = ! empty( $meta['allow_discount'] ) ? $meta['allow_discount'] : '';
		$tf_tour_discount_type  = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
		$tf_tour_discount_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';
        $tf_tour_min_discount = 0;
        if ( ! empty( $tf_tour_discount_type ) && ! empty( $tf_tour_min_price ) && ! empty( $tf_tour_discount_price ) ) {
			if ( !empty($allow_discount) && $tf_tour_discount_type == "percent" ) {
				$tf_tour_min_discount = ( $tf_tour_min_price * $tf_tour_discount_price ) / 100;
				$tf_tour_min_price    = $tf_tour_min_price - $tf_tour_min_discount;
			}
			if ( !empty($allow_discount) && $tf_tour_discount_type == "fixed" ) {
				$tf_tour_min_discount = $tf_tour_discount_price;
				$tf_tour_min_price    = $tf_tour_min_price - $tf_tour_discount_price;
			}
		}

		return array(
			'min_regular_price' => $tf_tour_full_price,
			'min_sale_price'    => $tf_tour_min_price,
			'min_discount'      => $tf_tour_min_discount,
			'max_regular_price' => !empty($tour_price) ? max( $tour_price ) : 0,
		);
	}

	function get_avail_price( $period = '') {
		
		$tour_price                       = [];
		$meta                             = $this->meta;
		$pricing_rule                     = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$tour_availability_data = isset( $meta['tour_availability'] ) && ! empty( $meta['tour_availability'] ) ? json_decode( $meta['tour_availability'], true ) : [];

		$package_pricing = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : '';

		$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
		$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
		$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
		$infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;
		
		$min_adult_price = null;
		$min_adult_sale_price = null;
		$min_child_price = null;
		$min_child_sale_price = null;
		$min_infant_price = null;
		$min_infant_sale_price = null;
		$min_group_price = null;
		$min_group_sale_price = null;
		if(!empty($tour_availability_data)){
			foreach ($tour_availability_data as $data) {
				if ($data['status'] !== 'available') {
					continue;
				}

				if($data['pricing_type'] == 'person'){
					// Adult Price
					if (!empty($data['adult_price'])) {
						if (is_null($min_adult_price) || $data['adult_price'] < $min_adult_price) {
							$min_adult_price = $data['adult_price'];
						}
					} else if(!empty($adult_price)){
						if (is_null($min_adult_price) || $adult_price < $min_adult_price) {
							$min_adult_price = $adult_price;
						}
					}

					// Child Price
					if (!empty($data['child_price'])) {
						if (is_null($min_child_price) || $data['child_price'] < $min_child_price) {
							$min_child_price = $data['child_price'];
						}
					} else if(!empty($children_price)){
						if (is_null($min_child_price) || $children_price < $min_child_price) {
							$min_child_price = $children_price;
						}
					}

					// Infant Price
					if (!empty($data['infant_price'])) {
						if (is_null($min_infant_price) || $data['infant_price'] < $min_infant_price) {
							$min_infant_price = $data['infant_price'];
						}
					} else if(!empty($infant_price)){
						if (is_null($min_infant_price) || $infant_price < $min_infant_price) {
							$min_infant_price = $infant_price;
						}
					}
				}

				if($data['pricing_type'] == 'group'){
					// Group Price
					if (!empty($data['price'])) {
						if (is_null($min_group_price) || $data['price'] < $min_group_price) {
							$min_group_price = $data['price'];
						}
					} else if(!empty($group_price)){
						if (is_null($min_group_price) || $group_price < $min_group_price) {
							$min_group_price = $group_price;
						}
					}
				}
				
				if( $data['pricing_type'] == 'package'){
					if(!empty($data['options_count'])){
						for($i = 0; $i < $data['options_count']; $i++){

							if (!empty($data['tf_option_adult_price_'.$i])) {
								if (is_null($min_adult_price) || $data['tf_option_adult_price_'.$i] < $min_adult_price) {
									$min_adult_price = $data['tf_option_adult_price_'.$i];
									
								}
							}

							if (!empty($data['tf_option_child_price_'.$i])) {
								if (is_null($min_child_price) || $data['tf_option_child_price_'.$i] < $min_child_price) {
									$min_child_price = $data['tf_option_child_price_'.$i];
								}
							}

							if (!empty($data['tf_option_infant_price_'.$i])) {
								if (is_null($min_infant_price) || $data['tf_option_infant_price_'.$i] < $min_infant_price) {
									$min_infant_price = $data['tf_option_infant_price_'.$i];
								}
							}

							if (!empty($data['tf_option_group_price_'.$i])) {
								if (is_null($min_group_price) || $data['tf_option_group_price_'.$i] < $min_group_price) {
									$min_group_price = $data['tf_option_group_price_'.$i];
								}
							}

						}
					}
				}
			}
		}else{

			if($pricing_rule == 'person'){
				// Adult Price
				if(!empty($adult_price)){
					if (is_null($min_adult_price) || $adult_price < $min_adult_price) {
						$min_adult_price = $adult_price;
					}
				}

				// Child Price
				if(!empty($children_price)){
					if (is_null($min_child_price) || $children_price < $min_child_price) {
						$min_child_price = $children_price;
					}
				}

				// Infant Price
				if(!empty($infant_price)){
					if (is_null($min_infant_price) || $infant_price < $min_infant_price) {
						$min_infant_price = $infant_price;
					}
				}
			}
			if($pricing_rule == 'group'){
				if(!empty($group_price)){
					$min_group_price = $group_price;
				}
			}

			if($pricing_rule == 'package' && !empty($package_pricing)){
				foreach($package_pricing as $package){
					if (!empty($package['adult_tabs'][1]['adult_price'])) {
						if (is_null($min_adult_price) || $package['adult_tabs'][1]['adult_price'] < $min_adult_price) {
							$min_adult_price = $package['adult_tabs'][1]['adult_price'];
						}
					}

					if (!empty($package['child_tabs'][1]['child_price'])) {
						if (is_null($min_child_price) || $package['child_tabs'][1]['child_price'] < $min_child_price) {
							$min_child_price = $package['child_tabs'][1]['child_price'];
						}
					}

					if (!empty($package['infant_tabs'][1]['infant_price'])) {
						if (is_null($min_infant_price) || $package['infant_tabs'][1]['infant_price'] < $min_infant_price) {
							$min_infant_price = $package['infant_tabs'][1]['infant_price'];
						}
					}

					if (!empty($package['group_tabs'][1]['group_price'])) {
						if (is_null($min_group_price) || $package['group_tabs'][1]['group_price'] < $min_group_price) {
							$min_group_price = $package['group_tabs'][1]['group_price'];
						}
					}

				}
			}
		}

		$allow_discount    = ! empty( $meta['allow_discount'] ) ? $meta['allow_discount'] : '';
		$discount_type    = !empty($meta['discount_type']) ? $meta['discount_type'] : 'none';
		$discounted_price = !empty($meta['discount_price']) ? $meta['discount_price'] : 0;
		if(!empty($allow_discount) && ($discount_type == 'percent' || $discount_type == 'fixed')) {
			if($discount_type == 'percent') {
				if(!empty($min_adult_price)){
					$min_adult_sale_price = $min_adult_price;
					$min_adult_price = number_format( $min_adult_price - (( $min_adult_price / 100 ) * $discounted_price) , 2, '.', '' );
				}
				if(!empty($min_child_price)){
					$min_child_sale_price = $min_child_price;
					$min_child_price = number_format( $min_child_price - (( $min_child_price / 100 ) * $discounted_price) , 2, '.', '' );
				}
				if(!empty($min_infant_price)){
					$min_infant_sale_price = $min_infant_price;
					$min_infant_price = number_format( $min_infant_price - (( $min_infant_price / 100 ) * $discounted_price) , 2, '.', '' );
				}
				if(!empty($min_group_price)){
					$min_group_sale_price = $min_group_price;
					$min_group_price = number_format( $min_group_price - (( $min_group_price / 100 ) * $discounted_price) , 2, '.', '' );
				}
			} else if($discount_type == 'fixed') {
				if(!empty($min_adult_price)){
					$min_adult_sale_price = $min_adult_price;
					$min_adult_price = number_format( ( $min_adult_price - $discounted_price ), 2, '.', '' );
				}
				if(!empty($min_child_price)){
					$min_child_sale_price = $min_child_price;
					$min_child_price = number_format( ( $min_child_price - $discounted_price ), 2, '.', '' );
				}
				if(!empty($min_infant_price)){
					$min_infant_sale_price = $min_infant_price;
					$min_infant_price = number_format( ( $min_infant_price - $discounted_price ), 2, '.', '' );
				}
				if(!empty($min_group_price)){
					$min_group_sale_price = $min_group_price;
					$min_group_price = number_format( ( $min_group_price - $discounted_price ), 2, '.', '' );
				}
			}
		}
		return array(
			'adult_price' => $min_adult_price,
			'sale_adult_price' => $min_adult_sale_price,
			'child_price'    => $min_child_price,
			'sale_child_price'    => $min_child_sale_price,
			'infant_price'      => $min_infant_price,
			'sale_infant_price'      => $min_infant_sale_price,
			'group_price'      => $min_group_price,
			'sale_group_price'      => $min_group_sale_price,
		);
	}

	function get_min_max_person( $period = '') {
		
		$tour_price                       = [];
		$meta                             = $this->meta;
		$pricing_rule                     = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$tour_availability_data = isset( $meta['tour_availability'] ) && ! empty( $meta['tour_availability'] ) ? json_decode( $meta['tour_availability'], true ) : [];
		
		$min_person = null;
		$max_person = null;
		if(!empty($tour_availability_data)){
			foreach ($tour_availability_data as $data) {
				if ($data['status'] !== 'available') {
					continue;
				}

				if($data['pricing_type'] == 'person'){
					if (!empty($data['min_person'])) {
						if (is_null($min_person) || $data['min_person'] < $min_person) {
							$min_person = $data['min_person'];
						}
					}
					if (!empty($data['max_person'])) {
						if (is_null($max_person) || $data['max_person'] > $max_person) {
							$max_person = $data['max_person'];
						}
					}
				}

			}
		}

		$package_pricing = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : '';
		if(!empty($package_pricing) && $pricing_rule=='package'){
			foreach($package_pricing as $package){
				if (!empty($package['adult_tabs'][2]['min_adult'])) {
					if (is_null($min_person) || $package['adult_tabs'][2]['min_adult'] < $min_person) {
						$min_person = $package['adult_tabs'][2]['min_adult'];
					}
				}
				if (!empty($package['adult_tabs'][3]['max_adult'])) {
					if (is_null($max_person) || $package['adult_tabs'][3]['max_adult'] > $max_person) {
						$max_person = $package['adult_tabs'][3]['max_adult'];
					}
				}

				if (!empty($package['child_tabs'][2]['min_child'])) {
					if (is_null($min_person) || $package['child_tabs'][2]['min_child'] < $min_person) {
						$min_person = $package['child_tabs'][2]['min_child'];
					}
				}
				if (!empty($package['child_tabs'][3]['max_child'])) {
					if (is_null($max_person) || $package['child_tabs'][3]['max_child'] > $max_person) {
						$max_person = $package['child_tabs'][3]['max_child'];
					}
				}

				if (!empty($package['infant_tabs'][2]['min_infant'])) {
					if (is_null($min_person) || $package['infant_tabs'][2]['min_infant'] < $min_person) {
						$min_person = $package['infant_tabs'][2]['min_infant'];
					}
				}
				if (!empty($package['infant_tabs'][3]['max_infant'])) {
					if (is_null($max_person) || $package['infant_tabs'][3]['max_infant'] > $max_person) {
						$max_person = $package['infant_tabs'][3]['max_infant'];
					}
				}

				if (!empty($package['group_tabs'][2]['min_person'])) {
					if (is_null($min_person) || $package['group_tabs'][2]['min_person'] < $min_person) {
						$min_person = $package['group_tabs'][2]['min_person'];
					}
				}
				if (!empty($package['group_tabs'][3]['max_person'])) {
					if (is_null($max_person) || $package['group_tabs'][3]['max_person'] > $max_person) {
						$max_person = $package['group_tabs'][3]['max_person'];
					}
				}
			}
		}

		if(empty($tour_availability_data) && ($pricing_rule=='group' || $pricing_rule=='person')){
			$min_person = !empty($meta['min_person']) ? $meta['min_person'] : 0;
			$max_person = !empty($meta['max_person']) ? $meta['max_person'] : 0;
		}

		return array(
			'min_person' => $min_person,
			'max_person' => $max_person,
		);
	}

	/*
	 * Get min price html
	 */
	function get_min_price_html( $period = '' ) {
		$min_max_price = $this->get_min_price( $period );
		$regular_price = $min_max_price['min_regular_price'];
		$sale_price    = $min_max_price['min_sale_price'];
		$min_discount  = $min_max_price['min_discount'];

		$price_html = '';
		if ( ! empty( $min_max_price ) ) {
			$price_html .= esc_html__( "From ", "tourfic" );
			if ( ! empty( $min_discount ) ) {
				$price_html .= wc_format_sale_price( $regular_price, $sale_price );
			} else {
				$price_html .= wp_kses_post( wc_price( $sale_price ) ) . " ";
			}
		}

		return $price_html;
	}

	function get_total_price() {
		$meta = $this->meta;

		// Total person calculation
		$persons              = ! empty( $this->persons ) ? $this->persons : array();
		$adult_count          = ! empty( $persons['adult'] ) ? $persons['adult'] : 0;
		$child_count          = ! empty( $persons['child'] ) ? $persons['child'] : 0;
		$infant_count         = ! empty( $persons['infant'] ) ? $persons['infant'] : 0;
		$total_people         = $adult_count + $child_count + $infant_count;
		$total_people_booking = $adult_count + $child_count;

		$tour_type            = ! empty( $meta['type'] ) ? $meta['type'] : '';
		$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;

		// Tour date
		$tour_date = $this->date;
		$tour_time = $this->time;

		$tour_availability_data = isset( $meta['tour_availability'] ) && ! empty( $meta['tour_availability'] ) ? json_decode( $meta['tour_availability'], true ) : [];

		$matched_availability = null;
		if ( $tour_date && is_array($tour_availability) ) {
			$input_date = strtotime($tour_date);

			foreach ( $tour_availability as $date_range => $details ) {
				if ( !isset($details['check_in'], $details['check_out'], $details['status']) ) {
					continue;
				}

				$check_in  = strtotime(trim($details['check_in']));
				$check_out = strtotime(trim($details['check_out']));
				$status    = $details['status'];

				if ( $status === 'available' && $input_date >= $check_in && $input_date <= $check_out ) {
					$matched_availability = $details;
					break; // Stop loop after first match
				}
			}
		}


		if (! empty($matched_availability) ) {
			$group_price    = ! empty( $matched_availability['price'] ) ? $matched_availability['price'] : $group_price;
			$adult_price    = ! empty( $matched_availability['adult_price'] ) ? $matched_availability['adult_price'] : $adult_price;
			$children_price = ! empty( $matched_availability['child_price'] ) ? $matched_availability['child_price'] : $children_price ;
			$infant_price   = ! empty( $matched_availability['infant_price'] ) ? $matched_availability['infant_price'] : $infant_price;
		} else {
			$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
			$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
			$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
			$infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;
		}

		$adult_price    = $this->calculate_discount( $adult_price );
		$children_price = $this->calculate_discount( $children_price );
		$infant_price   = $this->calculate_discount( $infant_price );
		$group_price    = $this->calculate_discount( $group_price );

		# Set pricing based on pricing rule
		if ( $pricing_rule == 'group' ) {
			$total_price = $group_price;
		} else {
			$total_price = ( $adult_price * $adult_count ) + ( $child_count * $children_price ) + ( $infant_count * $infant_price );
		}

		return $total_price;
	}

	static function get_min_max_price_from_all_tour() {
		$tf_tour_args = array(
			'posts_per_page' => - 1,
			'post_type'      => 'tf_tours',
			'post_status'    => 'publish'
		);

		$tftours_min_max_query = new \WP_Query( $tf_tour_args );
		$tftours_min_maxprices = array();

		if ( $tftours_min_max_query->have_posts() ):
			while ( $tftours_min_max_query->have_posts() ) : $tftours_min_max_query->the_post();

				$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
				if ( ! empty( $meta['adult_price'] ) ) {
					$tftours_min_maxprices[] = $meta['adult_price'];
				}
				if ( ! empty( $meta['child_price'] ) ) {
					$tftours_min_maxprices[] = $meta['child_price'];
				}
				if ( ! empty( $meta['infant_price'] ) ) {
					$tftours_min_maxprices[] = $meta['infant_price'];
				}
				if ( ! empty( $meta['group_price'] ) ) {
					$tftours_min_maxprices[] = $meta['group_price'];
				}
				
				$pricing_rule = !empty( $meta['pricing'] ) ? $meta['pricing'] : 'person';
				$tour_availability_data = isset( $meta['tour_availability'] ) && ! empty( $meta['tour_availability'] ) ? json_decode( $meta['tour_availability'], true ) : [];

				if(!empty($tour_availability_data)){
					foreach ($tour_availability_data as $data) {
						if ($data['status'] !== 'available') {
							continue;
						}
			
						if($data['pricing_type'] == 'person'){
							// Adult Price
							if (!empty($data['adult_price'])) {
								$tftours_min_maxprices[] = $data['adult_price'];
							}
			
							// Child Price
							if (!empty($data['child_price'])) {
								$tftours_min_maxprices[] = $data['child_price'];
							} 
			
							// Infant Price
							if (!empty($data['infant_price'])) {
								$tftours_min_maxprices[] = $data['infant_price'];
							} 
						}
			
						if($pricing_rule == 'group' && $data['pricing_type'] == 'group' ){
							// Group Price
							if (!empty($data['price'])) {
								$tftours_min_maxprices[] = $data['price'];
							}
						}
						
						if( $pricing_rule == 'package' && $data['pricing_type'] == 'package'){
							if(!empty($data['options_count'])){
								for($i = 0; $i < $data['options_count']; $i++){
			
									if (!empty($data['tf_option_adult_price_'.$i])) {
										$tftours_min_maxprices[] = $data['tf_option_adult_price_'.$i];
									}
			
									if (!empty($data['tf_option_child_price_'.$i])) {
										$tftours_min_maxprices[] = $data['tf_option_child_price_'.$i];
									}
			
									if (!empty($data['tf_option_infant_price_'.$i])) {
										$tftours_min_maxprices[] = $data['tf_option_infant_price_'.$i];
									}
			
									if (!empty($data['tf_option_group_price_'.$i])) {
										$tftours_min_maxprices[] = $data['tf_option_group_price_'.$i];
									}
			
								}
							}
						}
					}
				}

			endwhile;

		endif;
		wp_reset_query();
		if ( ! empty( $tftours_min_maxprices ) && count( $tftours_min_maxprices ) > 1 ) {
			$tour_max_price_val = max( $tftours_min_maxprices );
			$tour_min_price_val = min( $tftours_min_maxprices );
			if ( $tour_max_price_val == $tour_min_price_val ) {
				$tour_max_price = max( $tftours_min_maxprices );
				$tour_min_price = 1;
			} else {
				$tour_max_price = max( $tftours_min_maxprices );
				$tour_min_price = min( $tftours_min_maxprices );
			}
		}
		if ( ! empty( $tftours_min_maxprices ) && count( $tftours_min_maxprices ) == 1 ) {
			$tour_max_price = max( $tftours_min_maxprices );
			$tour_min_price = 1;
		}
		if ( empty( $tftours_min_maxprices ) ) {
			$tour_max_price = 0;
			$tour_min_price = 0;
		}

		return array(
			'min' => $tour_min_price,
			'max' => $tour_max_price,
		);
	}

	public function get_min_max_price() {
		$meta = $this->meta;
		$tour_price = array();

		$pricing_rule = !empty( $meta['pricing'] ) ? $meta['pricing'] : 'person';

		$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
		$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
		$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;

		$tour_availability_data = isset( $meta['tour_availability'] ) && ! empty( $meta['tour_availability'] ) ? json_decode( $meta['tour_availability'], true ) : [];

		$package_pricing = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : '';

		if(!empty($tour_availability_data)){
			foreach ($tour_availability_data as $data) {
				if ($data['status'] !== 'available') {
					continue;
				}

				if($pricing_rule == 'person' && $data['pricing_type'] == 'person'){
					// Adult Price
					if (!empty($data['adult_price'])) {
						$tour_price[] = $data['adult_price'];
					} else if(!empty($adult_price)){
						$tour_price[] = $adult_price;
					}

					// Child Price
					if (!empty($data['child_price'])) {
						$tour_price[] = $data['child_price'];
					} else if(!empty($children_price)){
						$tour_price[] = $children_price;
					}
				}

				if($pricing_rule == 'group' && $data['pricing_type'] == 'group' ){
					// Group Price
					if (!empty($data['price'])) {
						$tour_price[] = $data['price'];
					} else if(!empty($group_price)){
						$tour_price[] = $group_price;
					}
				}
				
				if( $pricing_rule == 'package' && $data['pricing_type'] == 'package'){
					if(!empty($data['options_count'])){
						for($i = 0; $i < $data['options_count']; $i++){

							if (!empty($data['tf_option_adult_price_'.$i])) {
								$tour_price[] = $data['tf_option_adult_price_'.$i];
							}

							if (!empty($data['tf_option_child_price_'.$i])) {
								$tour_price[] = $data['tf_option_child_price_'.$i];
							}

							if (!empty($data['tf_option_infant_price_'.$i])) {
								$tour_price[]= $data['tf_option_infant_price_'.$i];
							}

							if (!empty($data['tf_option_group_price_'.$i])) {
								$tour_price[] = $data['tf_option_group_price_'.$i];
							}

						}
					}
				}
			}
		}else{
			if($pricing_rule == 'person'){
				// Adult Price
				if(!empty($adult_price)){
					$tour_price[] = $adult_price;
				}

				// Child Price
				if(!empty($children_price)){
					$tour_price[] = $children_price;
				}
			}
			if($pricing_rule == 'group'){
				if(!empty($group_price)){
					$tour_price[] = $group_price;
				}
			}
			if($pricing_rule == 'package' && !empty($package_pricing)){
				foreach($package_pricing as $package){
					if (!empty($package['adult_tabs'][1]['adult_price'])) {
						$tour_price[] = $package['adult_tabs'][1]['adult_price'];
					}

					if (!empty($package['child_tabs'][1]['child_price'])) {
						$tour_price[] = $package['child_tabs'][1]['child_price'];
					}

					if (!empty($package['infant_tabs'][1]['infant_price'])) {
						$tour_price[] = $package['infant_tabs'][1]['infant_price'];
					}

					if (!empty($package['group_tabs'][1]['group_price'])) {
						$tour_price[] = $package['group_tabs'][1]['group_price'];
					}
				}
			}
		}

		return array(
			'min' => min( $tour_price ),
			'max' => max( $tour_price ),
		);
	}
}