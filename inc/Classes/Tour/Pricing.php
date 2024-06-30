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
	protected $persons;

	function __construct( $post_id ) {
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
	public function set_persons(int $adult, int $child, int $infant) {
		$this->persons = array(
			'adult' => !empty($adult) ? $adult : 0,
			'child' => !empty($child) ? $child : 0,
			'infant' => !empty($infant) ? $infant : 0,
		);

		return $this;
	}

	/*
	 * Get tour type
	 */
	function get_type() {
		return ! empty( $this->meta['type'] ) ? $this->meta['type'] : '';
	}

	/*
	 * Get adult price
	 */
	function get_adult_price() {
		# Get tour type
		$tour_type = !empty($meta['type']) ? $meta['type'] : 'continuous';

		# Custom availability status
		if($tour_type == 'continuous') {
			$custom_avail = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
		}

		# Get discounts
		$discount_type    = !empty($meta['discount_type']) ? $meta['discount_type'] : 'none';
		$discounted_price = !empty($meta['discount_price']) ? $meta['discount_price'] : 0;

		/**
		 * Price calculation based on custom availability
		 *
		 * Custom availability has different pricing calculation
		 */
		if($tour_type == 'continuous' && $custom_avail == true) {

			# Get pricing rule person/group
			$pricing_rule = !empty($meta['custom_pricing_by']) ? $meta['custom_pricing_by'] : 'person';

			/**
			 * Price calculation based on pricing rule
			 */
			if($pricing_rule == 'group') {
				if(!empty($meta['cont_custom_date']) && gettype($meta['cont_custom_date'])=="string"){
					$tf_tour_cont_custom_date = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
						return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
					}, $meta['cont_custom_date'] );
					$tf_tour_custom_date = unserialize( $tf_tour_cont_custom_date );

					$group_prices_array = array_column($tf_tour_custom_date, 'group_price');
				}else{
					# Get group price from all the arrays
					$group_prices_array = is_array($meta['cont_custom_date']) ? array_column($meta['cont_custom_date'], 'group_price') : 0;
				}
				# Get minimum group price
				$min_group_price = is_array($group_prices_array) ? min($group_prices_array) : 0;
				# Get maximum group price
				$max_group_price = is_array($group_prices_array) ? max($group_prices_array) : 0;

				# Discount price calculation
				if($discount_type == 'percent') {

					$sale_min_group_price = number_format( $min_group_price - (( $min_group_price / 100 ) * $discounted_price) , 2, '.', '' );
					$sale_max_group_price = number_format( $max_group_price - (( $max_group_price / 100 ) * $discounted_price) , 2, '.', '' );

				} else if($discount_type == 'fixed') {

					$sale_min_group_price = number_format( ( $min_group_price - $discounted_price ), 2, '.', '' );
					$sale_max_group_price = number_format( ( $max_group_price - $discounted_price ), 2, '.', '' );

				}

				if($discount_type == 'percent' || $discount_type == 'fixed') {


					# WooCommerce Regular Price
					$wc_regular_min_group_price = wc_price($min_group_price );
					$wc_regular_max_group_price = wc_price($max_group_price );

					# Final output Regular (price range)
					if(!empty($wc_regular_min_group_price) && !empty($wc_regular_max_group_price)) {
						$price = ($wc_regular_min_group_price != $wc_regular_max_group_price) ? $wc_regular_min_group_price. '-' .$wc_regular_max_group_price : $wc_regular_min_group_price; // Discounted price range
					}
					if(!empty($wc_regular_min_group_price) && !empty($wc_regular_max_group_price)) {
						$wc_price = ($wc_regular_min_group_price != $wc_regular_max_group_price) ? $wc_regular_min_group_price. '-' .$wc_regular_max_group_price : $wc_regular_min_group_price; // Discounted WooCommerce price range
					}

					# WooCommerce Price
					$wc_min_group_price = wc_price( $sale_min_group_price );
					$wc_max_group_price = wc_price( $sale_max_group_price );

					# Final output (price range)
					if(!empty($sale_min_group_price) && !empty($sale_max_group_price)) {
						$sale_price = ($sale_min_group_price != $sale_max_group_price) ? $sale_min_group_price. '-' .$sale_max_group_price : $sale_min_group_price; // Discounted price range
					}
					if(!empty($wc_min_group_price) && !empty($wc_max_group_price)) {
						$wc_sale_price = ($wc_min_group_price != $wc_max_group_price) ? $wc_min_group_price. '-' .$wc_max_group_price : $wc_min_group_price; // Discounted WooCommerce price range
					}

				} else {

					# WooCommerce Price
					$wc_min_group_price = wc_price($min_group_price);
					$wc_max_group_price = wc_price($max_group_price);

					# Final output (price range)
					if(!empty($min_group_price) && !empty($max_group_price)) {
						$price = ($min_group_price != $max_group_price) ? $min_group_price. '-' .$max_group_price : $min_group_price; // Price range
					}
					if(!empty($wc_min_group_price) && !empty($wc_max_group_price)) {
						$wc_price = ($wc_min_group_price != $wc_max_group_price) ? $wc_min_group_price. '-' .$wc_max_group_price : $wc_min_group_price; // WooCommerce price range
					}

				}

			} else if($pricing_rule == 'person') {

				# Get adult, child, infant price from all the arrays
				if(!empty($meta['cont_custom_date']) && gettype($meta['cont_custom_date'])=="string"){
					$tf_tour_cont_custom_date = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
						return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
					}, $meta['cont_custom_date'] );
					$tf_tour_custom_date = unserialize( $tf_tour_cont_custom_date );
					$adult_price_array  = is_array($tf_tour_custom_date) ? array_column($tf_tour_custom_date, 'adult_price') : 0;
					$child_price_array  = is_array($tf_tour_custom_date) ? array_column($tf_tour_custom_date, 'child_price') : 0;
					$infant_price_array = is_array($tf_tour_custom_date) ? array_column($tf_tour_custom_date, 'infant_price') : 0;
				}else{
					$adult_price_array  = is_array($meta['cont_custom_date']) ? array_column($meta['cont_custom_date'], 'adult_price') : 0;
					$child_price_array  = is_array($meta['cont_custom_date']) ? array_column($meta['cont_custom_date'], 'child_price') : 0;
					$infant_price_array = is_array($meta['cont_custom_date']) ? array_column($meta['cont_custom_date'], 'infant_price') : 0;
				}

				# Get minimum price of adult, child, infant
				$min_adult_price  = !empty($adult_price_array) ? min($adult_price_array) : 0;
				$min_child_price  = !empty($child_price_array) ? min($child_price_array) : 0;
				$min_infant_price = !empty($infant_price_array) ? min($infant_price_array) : 0;

				# Get maximum price of adult, child, infant
				$max_adult_price  = !empty($adult_price_array) ? max($adult_price_array) : 0;
				$max_child_price  = !empty($child_price_array) ? max($child_price_array) : 0;
				$max_infant_price = !empty($infant_price_array) ? max($infant_price_array): 0;

				# Discount price calculation
				if($discount_type == 'percent') {

					# Minimum discounted price
					$sale_min_adult_price  = !empty($min_adult_price) ? number_format( $min_adult_price - (( $min_adult_price / 100 ) * $discounted_price) , 2, '.', '' ) : '';
					$sale_min_child_price  = !empty($min_child_price) ? number_format( $min_child_price - (( $min_child_price / 100 ) * $discounted_price) , 2, '.', '' ) : '';
					$sale_min_infant_price = !empty($min_infant_price) ? number_format( $min_infant_price - (( $min_infant_price / 100 ) * $discounted_price) , 2, '.', '' ) : '';
					# Maximum discounted price
					$sale_max_adult_price  = !empty($max_adult_price) ? number_format( $max_adult_price - (( $max_adult_price / 100 ) * $discounted_price) , 2, '.', '' ) : '';
					$sale_max_child_price  = !empty($max_child_price) ? number_format( $max_child_price - (( $max_child_price / 100 ) * $discounted_price) , 2, '.', '' ) : '';
					$sale_max_infant_price = !empty($max_infant_price) ? number_format( $max_infant_price - (( $max_infant_price / 100 ) * $discounted_price) , 2, '.', '' ) : '';

				} else if($discount_type == 'fixed') {

					# Minimum discounted price
					$sale_min_adult_price  = !empty($min_adult_price) ? number_format( ( $min_adult_price - $discounted_price ), 2, '.', '' ) : '';
					$sale_min_child_price  = !empty($min_child_price) ? number_format( ( $min_child_price - $discounted_price ), 2, '.', '' ) : '';
					$sale_min_infant_price = !empty($min_infant_price) ? number_format( ( $min_infant_price - $discounted_price ), 2, '.', '' ) : '';
					# Maximum discounted price
					$sale_max_adult_price  = !empty($max_adult_price) ? number_format( ( $max_adult_price - $discounted_price ), 2, '.', '' ) : '';
					$sale_max_child_price  = !empty($max_child_price) ? number_format( ( $max_child_price - $discounted_price ), 2, '.', '' ) : '';
					$sale_max_infant_price = !empty($max_infant_price) ? number_format( ( $max_infant_price - $discounted_price ), 2, '.', '' ) : '';

				}

				if($discount_type == 'percent' || $discount_type == 'fixed') {

					# WooCommerce Price
					$wc_min_adult_price  = wc_price($sale_min_adult_price);
					$wc_min_child_price  = wc_price($sale_min_child_price);
					$wc_min_infant_price = wc_price($sale_min_infant_price);

					$wc_max_adult_price  = wc_price($sale_max_adult_price);
					$wc_max_child_price  = wc_price($sale_max_child_price);
					$wc_max_infant_price = wc_price($sale_max_infant_price);

					# Final output (price range)
					if(!empty($sale_min_adult_price) && !empty($sale_max_adult_price)) {
						$sale_adult_price  = ($sale_min_adult_price != $sale_min_adult_price) ? $sale_min_adult_price. '-' .$sale_max_adult_price : $sale_min_adult_price;    // Discounted price range
					}
					if(!empty($sale_min_child_price) && !empty($sale_max_child_price)) {
						$sale_child_price  = ($sale_min_child_price != $sale_max_child_price) ? $sale_min_child_price. '-' .$sale_max_child_price : $sale_min_child_price;    // Discounted price range
					}
					if(!empty($sale_min_infant_price) && !empty($sale_max_infant_price)) {
						$sale_infant_price = ($sale_min_infant_price != $sale_max_infant_price) ? $sale_min_infant_price. '-' .$sale_max_infant_price : $sale_min_infant_price;  // Discounted price range
					}

					if(!empty($wc_min_adult_price) && !empty($wc_max_adult_price)) {
						$wc_sale_adult_price  = ($wc_min_adult_price != $wc_max_adult_price) ?  $wc_min_adult_price. '-' .$wc_max_adult_price : $wc_min_adult_price;    // Discounted WooCommerce price range
					}
					if(!empty($wc_min_child_price) && !empty($wc_max_child_price)) {
						$wc_sale_child_price  = ($wc_min_child_price != $wc_max_child_price) ? $wc_min_child_price. '-' .$wc_max_child_price : $wc_min_child_price;    // Discounted WooCommerce price range
					}
					if(!empty($wc_min_infant_price) && !empty($wc_max_infant_price)) {
						$wc_sale_infant_price = ($wc_min_infant_price != $wc_max_infant_price) ? $wc_min_infant_price. '-' .$wc_max_infant_price : $wc_min_infant_price;  // Discounted WooCommerce price range
					}
				}

				# WooCommerce Price
				$wc_min_adult_price  = wc_price($min_adult_price);
				$wc_min_child_price  = wc_price($min_child_price);
				$wc_min_infant_price = wc_price($min_infant_price);

				$wc_max_adult_price  = wc_price($max_adult_price);
				$wc_max_child_price  = wc_price($max_child_price);
				$wc_max_infant_price = wc_price($max_infant_price);

				# Final output (price range)
				if(!empty($min_adult_price) && !empty($max_adult_price)) {
					$adult_price = ($min_adult_price != $max_adult_price) ? $min_adult_price. '-' .$max_adult_price : $min_adult_price;    // Price range
				}
				if(!empty($min_child_price) && !empty($max_child_price)) {
					$child_price  = ($min_child_price != $max_child_price) ? $min_child_price. '-' .$max_child_price : $min_child_price;    // Price range
				}
				if(!empty($min_infant_price) && !empty($max_infant_price)) {
					$infant_price = ($min_infant_price != $min_infant_price) ? $min_infant_price. '-' .$max_infant_price : $min_infant_price;  // Price range
				}

				if(!empty($wc_min_adult_price) && !empty($wc_max_adult_price)) {
					$wc_adult_price  = ($wc_min_adult_price != $wc_max_adult_price) ? $wc_min_adult_price. '-' .$wc_max_adult_price : $wc_min_adult_price;    // WooCommerce price range
				}
				if(!empty($wc_min_child_price) && !empty($wc_max_child_price)) {
					$wc_child_price  = ($wc_min_child_price != $wc_max_child_price) ? $wc_min_child_price. '-' .$wc_max_child_price : $wc_min_child_price;    // WooCommerce price range
				}
				if(!empty($wc_min_infant_price) && !empty($wc_max_infant_price)) {
					$wc_infant_price = ($wc_min_infant_price != $wc_max_infant_price) ? $wc_min_infant_price. '-' .$wc_max_infant_price : $wc_min_infant_price;  // WooCommerce price range
				}
			}

		} else {

			/**
			 * Pricing for fixed/continuous
			 */

			# Get pricing rule person/group
			$pricing_rule = !empty($meta['pricing']) ? $meta['pricing'] : 'person';

			/**
			 * Price calculation based on pricing rule
			 */
			if($pricing_rule == 'group') {

				# Get group price. Default 0
				$price = !empty($meta['group_price']) ? $meta['group_price'] : 0;

				if($discount_type == 'percent') {
					$sale_price = number_format( $price - (( $price / 100 ) * $discounted_price) , 2, '.', '' );
				} else if($discount_type == 'fixed') {
					$sale_price = number_format( ( $price - $discounted_price ), 2, '.', '' );
				}

				# WooCommerce Price
				$wc_price = wc_price($price);

				if($discount_type == 'percent' || $discount_type == 'fixed') {
					$wc_sale_price = wc_price($sale_price);
				}

			} else if($pricing_rule == 'person') {

				$adult_price  = !empty($meta['adult_price']) ? $meta['adult_price'] : 0;
				$child_price  = !empty($meta['child_price']) ? $meta['child_price'] : 0;
				$infant_price = !empty($meta['infant_price']) ? $meta['infant_price'] : 0;

				if($discount_type == 'percent') {

					$adult_price  ? $sale_adult_price  = number_format( $adult_price - (( $adult_price / 100 ) * $discounted_price) , 2, '.', '' ) : 0;
					$child_price  ? $sale_child_price  = number_format( $child_price - (( $child_price / 100 ) * $discounted_price) , 2, '.', '' ) : 0;
					$infant_price ? $sale_infant_price = number_format( $infant_price - (( $infant_price / 100 ) * $discounted_price) , 2, '.', '' ) : 0;

				} else if($discount_type == 'fixed') {

					$adult_price  ? $sale_adult_price  = number_format( ( $adult_price - $discounted_price ), 2, '.', '' ) : 0;
					$child_price  ? $sale_child_price  = number_format( ( $child_price - $discounted_price ), 2, '.', '' ) : 0;
					$infant_price ? $sale_infant_price = number_format( ( $infant_price - $discounted_price ), 2, '.', '' ) : 0;

				}

				# WooCommerce Price
				$wc_adult_price  = wc_price($adult_price);
				$wc_child_price  = wc_price($child_price);
				$wc_infant_price = wc_price($infant_price);

				if($discount_type == 'percent' || $discount_type == 'fixed') {
					$wc_sale_adult_price  = !empty($sale_adult_price) ? wc_price($sale_adult_price) : 0;
					$wc_sale_child_price  = !empty($sale_child_price) ? wc_price($sale_child_price) : 0;
					$wc_sale_infant_price = !empty($sale_infant_price) ? wc_price($sale_infant_price) : 0;
				}

			}

		}
	}

	function get_total_price(  ) {
		$meta                 = $this->meta;
		$tour_type            = ! empty( $meta['type'] ) ? $meta['type'] : '';
		$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;


		// People number
		$persons = !empty($this->persons) ? $this->persons : array();
		$total_people = $persons['adult'] + $persons['child'] + $persons['infant'];
		$total_people_booking = $persons['adult'] + $persons['child'];
		// Tour date
		$tour_date    = ! empty( $_POST['check-in-out-date'] ) ? sanitize_text_field( $_POST['check-in-out-date'] ) : '';
		$tour_time    = isset( $_POST['check-in-time'] ) ? sanitize_text_field( $_POST['check-in-time'] ) : null;


		// Visitor Details
		$tf_visitor_details = !empty($_POST['traveller']) ? $_POST['traveller'] : "";

		// Booking Confirmation Details
		$tf_confirmation_details = !empty($_POST['booking_confirm']) ? $_POST['booking_confirm'] : "";

		// Booking Type
		$tf_booking_type = function_exists('is_tf_pro') && is_tf_pro() ? ( !empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1 ) : 1;
		$tf_booking_url = function_exists('is_tf_pro') && is_tf_pro() ? ( !empty( $meta['booking-url'] ) ? esc_url($meta['booking-url']) : '' ) : '';
		$tf_booking_query_url = function_exists('is_tf_pro') && is_tf_pro() ? ( !empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&infant={infant}' ) : '';
		$tf_booking_attribute = function_exists('is_tf_pro') && is_tf_pro() ? ( !empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '' ) : '';

		/**
		 * If fixed is selected but pro is not activated
		 *
		 * show error
		 *
		 * @return
		 */
		if ( $tour_type == 'fixed' && function_exists('is_tf_pro') && ! is_tf_pro() ) {
			$response['errors'][] = esc_html__( 'Fixed Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
			$response['status']   = 'error';
			echo wp_json_encode( $response );
			die();

			return;
		}

		if ( $tour_type == 'fixed' ) {

			if( !empty($meta['fixed_availability']) && gettype($meta['fixed_availability'])=="string" ){
				$tf_tour_fixed_avail = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
					return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
				}, $meta['fixed_availability'] );
				$tf_tour_fixed_date = unserialize( $tf_tour_fixed_avail );
				$start_date = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
				$end_date   = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
				$min_people = ! empty( $tf_tour_fixed_date['min_seat'] ) ? $tf_tour_fixed_date['min_seat'] : '';
				$max_people = ! empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
				$tf_tour_booking_limit = ! empty( $tf_tour_fixed_date['max_capacity'] ) ? $tf_tour_fixed_date['max_capacity'] : 0;
			}else{
				$start_date = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
				$end_date   = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
				$min_people = ! empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
				$max_people = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
				$tf_tour_booking_limit = ! empty( $meta['fixed_availability']['max_capacity'] ) ? $meta['fixed_availability']['max_capacity'] : 0;
			}

			if(!function_exists("selected_day_diff")) {
				function selected_day_diff ($start_date, $end_date) {
					if(!empty($start_date) && !empty($end_date)) {

						$start_date = new DateTime($start_date);
						$end_date   = new DateTime($end_date);
						$interval 	= $start_date->diff($end_date);

						return $interval->days;
					}
				}
			}

			if(!function_exists("end_date_calculation")) {
				function end_date_calculation ($start_date, $difference) {
					if(!empty($start_date) && !empty($difference)) {
						if(str_contains($start_date, ' - ')) {
							return $start_date;

						} else {

							$start_date  = new DateTime($start_date);
							$new_end_day = $start_date->modify("+ $difference day");

							return $new_end_day->format('Y/m/d');
						}
					}
				}
			}

			if( !empty($start_date) && !empty($end_date)) {
				$day_diff = selected_day_diff($start_date, $end_date );
			}

			if(!empty($tour_type) && ($tour_type == "fixed")) {
				$start_date = ! empty( $_POST['check-in-out-date'] ) ? sanitize_text_field( $_POST['check-in-out-date'] ) : '';
			}

			if(!empty($start_date) && !empty($day_diff)) {
				$end_date = end_date_calculation($start_date, $day_diff);
			}

			// Fixed tour maximum capacity limit

			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($start_date) && !empty($end_date) ) {

				// Tour Order retrive from Tourfic Order Table
				$tf_orders_select = array(
					'select' => "post_id,order_details",
					'post_type' => 'tour',
					'query' => " AND ostatus = 'completed' ORDER BY order_id DESC"
				);
				$tf_tour_book_orders = Helper::tourfic_order_table_data($tf_orders_select);

				$tf_total_adults = 0;
				$tf_total_childrens = 0;

				foreach( $tf_tour_book_orders as $order ){
					$tour_id   = $order['post_id'];
					$order_details = json_decode($order['order_details']);
					$tf_tour_date = !empty($order_details->tour_date) ? $order_details->tour_date : '';
					list( $tf_booking_start, $tf_booking_end ) = explode( " - ", $tf_tour_date );
					if( !empty($tour_id) && $tour_id==$post_id && !empty($tf_booking_start) && $start_date==$tf_booking_start && !empty($tf_booking_end) && $end_date==$tf_booking_end ){
						$book_adult     = !empty( $order_details->adult ) ? $order_details->adult : '';
						if(!empty($book_adult)){
							list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
							$tf_total_adults += $tf_total_adult;
						}

						$book_children  = !empty( $order_details->child ) ? $order_details->child : '';
						if(!empty($book_children)){
							list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
							$tf_total_childrens += $tf_total_children;
						}
					}
				}

				$tf_total_people = $tf_total_adults+$tf_total_childrens;

				if( !empty($tf_tour_booking_limit) ){
					$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;
					if( $tf_total_people > 0 && $tf_total_people==$tf_tour_booking_limit ){
						$response['errors'][] = esc_html__( 'Booking limit is Reached this Tour', 'tourfic' );
					}
					if( $tf_total_people!=$tf_tour_booking_limit && $tf_today_limit < $total_people_booking ){
						/* translators: %1$s Limit  */
						$response['errors'][] = sprintf( esc_html__( 'Only %1$s Adult/Children are available this Tour', 'tourfic' ), $tf_today_limit );
					}
				}
			}

		} elseif ( $tour_type == 'continuous' ) {

			$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;

			if ( $custom_avail == true ) {

				$pricing_rule     = $meta['custom_pricing_by'];
				$cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
				if( !empty($cont_custom_date) && gettype($cont_custom_date)=="string" ){
					$tf_tour_conti_avail = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
						return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
					}, $cont_custom_date );
					$cont_custom_date = unserialize( $tf_tour_conti_avail );
				}

			} elseif ( $custom_avail == false ) {

				$min_people = ! empty( $meta['cont_min_people'] ) ? $meta['cont_min_people'] : '';
				$max_people = ! empty( $meta['cont_max_people'] ) ? $meta['cont_max_people'] : '';
				$allowed_times_field = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';


				// Daily Tour Booking Capacity && Tour Order retrive from Tourfic Order Table
				$tf_orders_select = array(
					'select' => "post_id,order_details",
					'post_type' => 'tour',
					'query' => " AND ostatus = 'completed' ORDER BY order_id DESC"
				);
				$tf_tour_book_orders = Helper::tourfic_order_table_data($tf_orders_select);

				$tf_total_adults = 0;
				$tf_total_childrens = 0;

				if( empty($allowed_times_field) || $tour_time==null ){
					$tf_tour_booking_limit = ! empty( $meta['cont_max_capacity'] ) ? $meta['cont_max_capacity'] : 0;

					foreach( $tf_tour_book_orders as $order ){
						$tour_id   = $order['post_id'];
						$order_details = json_decode($order['order_details']);
						$tf_tour_date = !empty($order_details->tour_date) ? $order_details->tour_date : '';
						$tf_tour_time = !empty($order_details->tour_time) ? $order_details->tour_time : '';

						if( !empty($tour_id) && $tour_id==$post_id && !empty($tf_tour_date) && $tour_date==$tf_tour_date && empty($tf_tour_time) ){
							$book_adult     = !empty( $order_details->adult ) ? $order_details->adult : '';
							if(!empty($book_adult)){
								list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
								$tf_total_adults += $tf_total_adult;
							}

							$book_children  = !empty( $order_details->child ) ? $order_details->child : '';
							if(!empty($book_children)){
								list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
								$tf_total_childrens += $tf_total_children;
							}
						}
					}

				}else{
					if(!empty($allowed_times_field[$tour_time]['time'])){
						$tour_time_title = $allowed_times_field[$tour_time]['time'];
					}

					if(!empty($allowed_times_field[$tour_time]['cont_max_capacity'])){
						$tf_tour_booking_limit = $allowed_times_field[$tour_time]['cont_max_capacity'];

						foreach( $tf_tour_book_orders as $order ){
							$tour_id   = $order['post_id'];
							$order_details = json_decode($order['order_details']);
							$tf_tour_date = !empty($order_details->tour_date) ? $order_details->tour_date : '';
							$tf_tour_time = !empty($order_details->tour_time) ? $order_details->tour_time : '';

							if( !empty($tour_id) && $tour_id==$post_id && !empty($tf_tour_date) && $tour_date==$tf_tour_date && !empty($tf_tour_time) && $tf_tour_time==$tour_time_title ){
								$book_adult     = !empty( $order_details->adult ) ? $order_details->adult : '';
								if(!empty($book_adult)){
									list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
									$tf_total_adults += $tf_total_adult;
								}

								$book_children  = !empty( $order_details->child ) ? $order_details->child : '';
								if(!empty($book_children)){
									list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
									$tf_total_childrens += $tf_total_children;
								}
							}
						}

					}
				}
				$tf_total_people = $tf_total_adults+$tf_total_childrens;

				if( !empty($tf_tour_booking_limit) ){
					$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;

					if( $tf_total_people > 0 && $tf_total_people==$tf_tour_booking_limit ){
						$response['errors'][] = esc_html__( 'Booking limit is Reached this Date', 'tourfic' );
					}
					if( $tf_total_people!=$tf_tour_booking_limit && $tf_today_limit < $total_people_booking ){
						/* translators: %1$s Limit  */
						$response['errors'][] = sprintf( esc_html__( 'Only %1$s Adult/Children are available this Date', 'tourfic' ), $tf_today_limit );
					}
				}
			}

		}

		/**
		 * If continuous custom availability is selected but pro is not activated
		 *
		 * Show error
		 *
		 * @return
		 */
		if ( $tour_type == 'continuous' && $custom_avail == true && function_exists('is_tf_pro') && ! is_tf_pro() ) {
			$response['errors'][] = esc_html__( 'Custom Continous Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
			$response['status']   = 'error';
			echo wp_json_encode( $response );
			die();

			return;
		}


		if ( $tour_type == 'continuous' ) {
			$start_date = $end_date = $tour_date;
		}

		// Tour extra
		$tour_extra_total = 0;
		$tour_extra_title_arr = [];

		$tour_extra_meta = ! empty( $meta['tour-extra'] ) ? $meta['tour-extra'] : '';
		if(!empty($tour_extra_meta)){
			$tours_extra = explode(',', $_POST['tour_extra']);
			$tour_extra_quantity = explode(',', $_POST["tour_extra_quantity"]);
			foreach($tours_extra as $extra_key => $extra){
				$tour_extra_pricetype = !empty( $tour_extra_meta[$extra]['price_type'] ) ? $tour_extra_meta[$extra]['price_type'] : 'fixed';
				if( $tour_extra_pricetype=="fixed" ){
					if(!empty($tour_extra_meta[$extra]['title']) && !empty($tour_extra_meta[$extra]['price'])){
						$tour_extra_total += $tour_extra_meta[$extra]['price'];
						$tour_extra_title_arr[] =  $tour_extra_meta[$extra]['title']." (Fixed: ".wc_price($tour_extra_meta[$extra]['price']).")";
					}
				} else if($tour_extra_pricetype == "quantity") {
					if(!empty($tour_extra_meta[$extra]['title']) && !empty($tour_extra_meta[$extra]['price'])){
						$tour_extra_total += $tour_extra_meta[$extra]['price'] * $tour_extra_quantity[$extra_key];
						$tour_extra_title_arr[] = $tour_extra_meta[$extra]['title']." (Per Unit: ".wc_price($tour_extra_meta[$extra]['price']).'*'.$tour_extra_quantity[$extra_key]."=".wc_price($tour_extra_meta[$extra]['price']*$tour_extra_quantity[$extra_key]).")";
					}
				}else{
					if(!empty($tour_extra_meta[$extra]['price']) && !empty($tour_extra_meta[$extra]['title'])){
						$tour_extra_total += ($tour_extra_meta[$extra]['price']*$total_people);
						$tour_extra_title_arr[] =  $tour_extra_meta[$extra]['title']." (Per Person: ".wc_price($tour_extra_meta[$extra]['price']).'*'.$total_people."=".wc_price($tour_extra_meta[$extra]['price']*$total_people).")";
					}
				}
			}
		}

		$tour_extra_title = ! empty( $tour_extra_title_arr ) ? implode(",",$tour_extra_title_arr) : '';

		/**
		 * People 0 number validation
		 *
		 */
		if ( $total_people == 0 ) {
			$response['errors'][] = esc_html__( 'Please Select Adults/Children/Infant required', 'tourfic' );
		}

		/**
		 * People number validation
		 *
		 */
		if ( $tour_type == 'fixed' ) {

			/* translators: %s Min Person  */
			$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
			/* translators: %s Max Person  */
			$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );

			if ( $total_people < $min_people && $min_people > 0 ) {
				/* translators: %s Min Required  */
				$response['errors'][] = sprintf( esc_html__( 'Minimum %1$s required', 'tourfic' ), $min_text );

			} else if ( $total_people > $max_people && $max_people > 0 ) {
				/* translators: %s Max Required  */
				$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

			}

		} elseif ( $tour_type == 'continuous' && $custom_avail == false ) {

			/* translators: %s Min Person  */
			$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
			/* translators: %s Max Person  */
			$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );


			if ( $total_people < $min_people && $min_people > 0 ) {
				/* translators: %s Min Required  */
				$response['errors'][] = sprintf( esc_html__( 'Minimum %1$s required', 'tourfic' ), $min_text );

			} else if ( $total_people > $max_people && $max_people > 0 ) {
				/* translators: %s Max Required  */
				$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

			}

		} elseif ( $tour_type == 'continuous' && $custom_avail == true ) {

			foreach ( $cont_custom_date as $item ) {

				// Backend continuous date values
				$back_date_from     = ! empty( $item['date']['from'] ) ? $item['date']['from'] : '';
				$back_date_to       = ! empty( $item['date']['from'] ) ? $item['date']['to'] : '';
				$back_date_from_stt = strtotime( str_replace( '/', '-', $back_date_from ) );
				$back_date_to_stt   = strtotime( str_replace( '/', '-', $back_date_to ) );
				// frontend selected date value
				$front_date = strtotime( str_replace( '/', '-', $tour_date ) );
				// Backend continuous min/max people values
				$min_people = ! empty( $item['min_people'] ) ? $item['min_people'] : '';
				$max_people = ! empty( $item['max_people'] ) ? $item['max_people'] : '';
				/* translators: %s Min Person  */
				$min_text   = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
				/* translators: %s Min Person  */
				$max_text   = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );


				// Compare backend & frontend date values to show specific people number error
				if ( $front_date >= $back_date_from_stt && $front_date <= $back_date_to_stt ) {
					if ( $total_people < $min_people && $min_people > 0 ) {
						/* translators: %1$s Min Person, $2$s Date From, %3$s Date To  */
						$response['errors'][] = sprintf( esc_html__( 'Minimum %1$s required for date %2$s - %3$s', 'tourfic' ), $min_text, $back_date_from, $back_date_to );

					}
					if ( $total_people > $max_people && $max_people > 0 ) {
						/* translators: %1$s Max Person, $2$s Date From, %3$s Date To  */
						$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed for date %2$s - %3$s', 'tourfic' ), $max_text, $back_date_from, $back_date_to );

					}


					$allowed_times_field = ! empty( $item['allowed_time'] ) ? $item['allowed_time'] : '';

					// Daily Tour Booking Capacity && tour order retrive form tourfic order table
					$tf_orders_select = array(
						'select' => "post_id,order_details",
						'post_type' => 'tour',
						'query' => " AND ostatus = 'completed' ORDER BY order_id DESC"
					);
					$tf_tour_book_orders = Helper::tourfic_order_table_data($tf_orders_select);

					$tf_total_adults = 0;
					$tf_total_childrens = 0;

					if( empty($allowed_times_field) || $tour_time==null ){
						$tf_tour_booking_limit = ! empty( $item['max_capacity'] ) ? $item['max_capacity'] : '';

						foreach( $tf_tour_book_orders as $order ){
							$tour_id   = $order['post_id'];
							$order_details = json_decode($order['order_details']);
							$tf_tour_date = !empty($order_details->tour_date) ? $order_details->tour_date : '';
							$tf_tour_time = !empty($order_details->tour_time) ? $order_details->tour_time : '';

							if( !empty($tour_id) && $tour_id==$post_id && !empty($tf_tour_date) && $tour_date==$tf_tour_date && empty($tf_tour_time) ){
								$book_adult     = !empty( $order_details->adult ) ? $order_details->adult : '';
								if(!empty($book_adult)){
									list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
									$tf_total_adults += $tf_total_adult;
								}

								$book_children  = !empty( $order_details->child ) ? $order_details->child : '';
								if(!empty($book_children)){
									list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
									$tf_total_childrens += $tf_total_children;
								}
							}
						}

					}else{
						if(!empty($allowed_times_field[$tour_time]['time'])){
							$tour_time_title = $allowed_times_field[$tour_time]['time'];
						}

						if(!empty($allowed_times_field[$tour_time]['max_capacity'])){
							$tf_tour_booking_limit = $allowed_times_field[$tour_time]['max_capacity'];

							foreach( $tf_tour_book_orders as $order ){
								$tour_id   = $order['post_id'];
								$order_details = json_decode($order['order_details']);
								$tf_tour_date = !empty($order_details->tour_date) ? $order_details->tour_date : '';
								$tf_tour_time = !empty($order_details->tour_time) ? $order_details->tour_time : '';

								if( !empty($tour_id) && $tour_id==$post_id && !empty($tf_tour_date) && $tour_date==$tf_tour_date && !empty($tf_tour_time) && $tf_tour_time==$tour_time_title ){
									$book_adult     = !empty( $order_details->adult ) ? $order_details->adult : '';
									if(!empty($book_adult)){
										list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
										$tf_total_adults += $tf_total_adult;
									}

									$book_children  = !empty( $order_details->child ) ? $order_details->child : '';
									if(!empty($book_children)){
										list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
										$tf_total_childrens += $tf_total_children;
									}
								}
							}

						}
					}
					$tf_total_people = $tf_total_adults+$tf_total_childrens;

					if( !empty($tf_tour_booking_limit) ){
						$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;

						if( $tf_total_people > 0 && $tf_total_people==$tf_tour_booking_limit ){
							$response['errors'][] = esc_html__( 'Booking limit is Reached this Date', 'tourfic' );
						}
						if( $tf_total_people!=$tf_tour_booking_limit && $tf_today_limit < $total_people_booking ){
							/* translators: %1$s Person Count  */
							$response['errors'][] = sprintf( esc_html__( 'Only %1$s Adult/Children are available this Date', 'tourfic' ), $tf_today_limit );
						}
					}
				}

			}

		}

		/**
		 * Check errors
		 *
		 */
		/* Minimum days to book before departure */
		$min_days_before_book      = ! empty( $meta['min_days_before_book'] ) ? $meta['min_days_before_book'] : '0';
		/* translators: %1$s Min Day Before Book  */
		$min_days_before_book_text = sprintf( _n( '%s day', '%s days', $min_days_before_book, 'tourfic' ), $min_days_before_book );
		$today_stt                 = new DateTime( gmdate( 'Y-m-d', strtotime( gmdate( 'Y-m-d' ) ) ) );
		$tour_date_stt             = new DateTime( gmdate( 'Y-m-d', strtotime( $start_date ) ) );
		$day_difference            = $today_stt->diff( $tour_date_stt )->days;
		$adult_required_chield = !empty( $meta["require_adult_child_booking"] ) ? $meta["require_adult_child_booking"] : 0;


		if ( $day_difference < $min_days_before_book ) {
			/* translators: %1$s Minimum Days Gap  */
			$response['errors'][] = sprintf( esc_html__( 'Present date to booking date required minimum %1$s gap', 'tourfic' ), $min_days_before_book_text );
		}
		if ( ! $start_date ) {
			$response['errors'][] = esc_html__( 'You must select booking date', 'tourfic' );
		}
		if ( ! $post_id ) {
			$response['errors'][] = esc_html__( 'Unknown Error! Please try again.', 'tourfic' );
		}

		/**
		 * Price by date range
		 *
		 * Tour type continuous and custom availability is true
		 */
		$tf_cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
		if( !empty($tf_cont_custom_date) && gettype($tf_cont_custom_date)=="string" ){
			$tf_tour_conti_custom_date = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
				return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
			}, $tf_cont_custom_date );
			$tf_cont_custom_date = unserialize( $tf_tour_conti_custom_date );
		}

		$tour = strtotime( $tour_date );
		if ( isset( $custom_avail ) && true == $custom_avail ) {
			$seasional_price = array_values( array_filter( $tf_cont_custom_date, function ( $value ) use ( $tour ) {
				$seasion_start = strtotime( $value['date']['from'] );
				$seasion_end   = strtotime( $value['date']['to'] );

				return $seasion_start <= $tour && $seasion_end >= $tour;
			} ) );
		}


		if ( $tour_type === 'continuous' && ! empty( $tf_cont_custom_date ) && ! empty( $seasional_price ) ) {

			$group_price    = !empty($seasional_price[0]['group_price']) ? $seasional_price[0]['group_price'] : 0;
			$adult_price    = !empty($seasional_price[0]['adult_price']) ? $seasional_price[0]['adult_price'] : 0;
			$children_price = !empty($seasional_price[0]['child_price']) ? $seasional_price[0]['child_price'] : 0;
			$infant_price   = !empty($seasional_price[0]['infant_price']) ? $seasional_price[0]['infant_price'] : 0;

		} else {

			$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
			$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
			$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
			$infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;

		}

		if ( function_exists('is_tf_pro') && is_tf_pro() && $tour_type == 'continuous' ) {
			$tf_allowed_times = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';
			if( !empty($tf_allowed_times) && gettype($tf_allowed_times)=="string" ){
				$tf_tour_conti_custom_date = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
					return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
				}, $tf_allowed_times );
				$tf_allowed_times = unserialize( $tf_tour_conti_custom_date );
			}

			if ( $custom_avail == false && ! empty( $tf_allowed_times ) && empty( $tour_time_title  ) ) {
				$response['errors'][] = esc_html__( 'Please select time', 'tourfic' );
			}
			if ( $custom_avail == true && ! empty( $seasional_price[0]['allowed_time'] ) && empty( $tour_time_title  ) ) {
				$response['errors'][] = esc_html__( 'Please select time', 'tourfic' );
			}
		}

		if ( ( ! empty( $custom_avail ) && $custom_avail == true ) || $pricing_rule == 'person' ) {

			if ( ! $disable_adult_price && $adults > 0 && empty( $adult_price ) ) {
				$response['errors'][] = esc_html__( 'Adult price is blank!', 'tourfic' );
			}
			if ( ! $disable_child_price && $children > 0 && empty( $children_price ) ) {
				$response['errors'][] = esc_html__( 'Childern price is blank!', 'tourfic' );
			}
			if ( ! $disable_infant_price && $infant > 0 && empty( $infant_price ) ) {
				$response['errors'][] = esc_html__( 'Infant price is blank!', 'tourfic' );
			}
			if ( $infant > 0 && ! empty( $infant_price ) && ! $adults ) {
				$response['errors'][] = esc_html__( 'Infant without adults is not allowed!', 'tourfic' );
			}

			if ( $adult_required_chield && $children > 0 && !empty( $children_price ) && empty( $adults ) ) {
				$response['errors'][] = esc_html__( 'An adult is required for children booking!', 'tourfic' );
			}

		} else if ( ( ! empty( $custom_avail ) && $custom_avail == true ) || $pricing_rule == 'group' ) {

			if ( empty( $group_price ) ) {
				$response['errors'][] = esc_html__( 'Group price is blank!', 'tourfic' );
			}

		}

		/**
		 * If no errors then process
		 *
		 * Store custom data in array
		 * Add to cart with custom data
		 */

		if( !empty($tf_booking_type) && 3==$tf_booking_type ){

			$tf_booking_fields = !empty(Helper::tfopt( 'book-confirm-field' )) ? Helper::tf_data_types(Helper::tfopt( 'book-confirm-field' )) : '';
			if(empty($tf_booking_fields)){
				$billing_details  = array(
					'billing_first_name' => sanitize_text_field($tf_confirmation_details['tf_first_name']),
					'billing_last_name'  => sanitize_text_field($tf_confirmation_details['tf_last_name']),
					'billing_company'    => '',
					'billing_address_1'  => sanitize_text_field($tf_confirmation_details['tf_street_address']),
					'billing_address_2'  => "",
					'billing_city'       => sanitize_text_field($tf_confirmation_details['tf_town_city']),
					'billing_state'      => sanitize_text_field($tf_confirmation_details['tf_state_country']),
					'billing_postcode'   => sanitize_text_field($tf_confirmation_details['tf_postcode']),
					'billing_country'    => sanitize_text_field($tf_confirmation_details['tf_country']),
					'billing_email'      => sanitize_email($tf_confirmation_details['tf_email']),
					'billing_phone'      => sanitize_text_field($tf_confirmation_details['tf_phone']),
				);
				$shipping_details = array(
					'tf_first_name' => sanitize_text_field($tf_confirmation_details['tf_first_name']),
					'tf_last_name'  => sanitize_text_field($tf_confirmation_details['tf_last_name']),
					'shipping_company'    => '',
					'tf_street_address'  => sanitize_text_field($tf_confirmation_details['tf_street_address']),
					'shipping_address_2'  => "",
					'tf_town_city'       => sanitize_text_field($tf_confirmation_details['tf_town_city']),
					'tf_state_country'      => sanitize_text_field($tf_confirmation_details['tf_state_country']),
					'tf_postcode'   => sanitize_text_field($tf_confirmation_details['tf_postcode']),
					'tf_country'    => sanitize_text_field($tf_confirmation_details['tf_country']),
					'tf_phone'      => sanitize_text_field($tf_confirmation_details['tf_phone']),
					'tf_email'      => sanitize_email($tf_confirmation_details['tf_email']),
				);
			}else{
				$billing_details = [];
				$shipping_details = [];

				if(!empty($tf_confirmation_details)){
					foreach( $tf_confirmation_details as $key => $details ){
						if("tf_first_name"==$key){
							$billing_details['billing_first_name'] = sanitize_text_field($details);
							$shipping_details[$key] = sanitize_text_field($details);
						}else if("tf_last_name"==$key){
							$billing_details['billing_last_name'] = sanitize_text_field($details);
							$shipping_details[$key] = sanitize_text_field($details);
						}else if("tf_street_address"==$key){
							$billing_details['billing_address_1'] = sanitize_text_field($details);
							$shipping_details[$key] = sanitize_text_field($details);
						}else if("tf_town_city"==$key){
							$billing_details['billing_city'] = sanitize_text_field($details);
							$shipping_details[$key] = sanitize_text_field($details);
						}else if("tf_state_country"==$key){
							$billing_details['billing_state'] = sanitize_text_field($details);
							$shipping_details[$key] = sanitize_text_field($details);
						}else if("tf_postcode"==$key){
							$billing_details['billing_postcode'] = sanitize_text_field($details);
							$shipping_details[$key] = sanitize_text_field($details);
						}else if("tf_country"==$key){
							$billing_details['billing_country'] = sanitize_text_field($details);
							$shipping_details[$key] = sanitize_text_field($details);
						}else if("tf_email"==$key){
							$billing_details['billing_email'] = sanitize_email($details);
							$shipping_details[$key] = sanitize_email($details);
						}else if("tf_phone"==$key){
							$billing_details['billing_phone'] = sanitize_text_field($details);
							$shipping_details[$key] = sanitize_text_field($details);
						}else{
							$billing_details[$key] = $details;
							$shipping_details[$key] = $details;
						}
					}
				}
			}

			// Price Calculation

			$discount_type    = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
			$discounted_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';

			if ( $tour_type == 'continuous' ) {
				$tf_tours_data['tf_tours_data']['tour_time'] = $tour_time_title;
			}

			# Calculate discounted price
			if ( $discount_type == 'percent' ) {

				$adult_price    = floatval(preg_replace('/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $discounted_price ), 2 )));
				$children_price = floatval(preg_replace('/[^\d.]/', '', number_format( $children_price - ( ( $children_price / 100 ) * $discounted_price ), 2 )));
				$infant_price   = floatval(preg_replace('/[^\d.]/', '', number_format( $infant_price - ( ( $infant_price / 100 ) * $discounted_price ), 2 )));
				$group_price    = floatval(preg_replace('/[^\d.]/', '', number_format( $group_price - ( ( $group_price / 100 ) * $discounted_price ), 2 )));

			} elseif ( $discount_type == 'fixed' ) {

				$adult_price    = floatval(preg_replace('/[^\d.]/', '', number_format( ( $adult_price - $discounted_price ), 2 )));
				$children_price = floatval(preg_replace('/[^\d.]/', '', number_format( ( $children_price - $discounted_price ), 2 )));
				$infant_price   = floatval(preg_replace('/[^\d.]/', '', number_format( ( $infant_price - $discounted_price ), 2 )));
				$group_price    = floatval(preg_replace('/[^\d.]/', '', number_format( ( $group_price - $discounted_price ), 2 )));

			}

			# Set pricing based on pricing rule
			if ( $pricing_rule == 'group' ) {
				$without_payment_price     = $group_price;
			} else {
				$without_payment_price     = ( $adult_price * $adults ) + ( $children * $children_price ) + ( $infant * $infant_price );
			}

			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				// get user id
				$tf_offline_user_id = $current_user->ID;
			} else {
				$tf_offline_user_id = 1;
			}

			$order_details = [
				'order_by'    => '',
				'tour_date'   => $tour_date,
				'tour_time'   => !empty($tour_time_title) ? $tour_time_title : '',
				'tour_extra'  => $tour_extra_title,
				'adult'       => $adults,
				'child'       => $children,
				'infants'     => $infant,
				'total_price' => $without_payment_price,
				'due_price'   => wc_price($without_payment_price),
				'visitor_details' => wp_json_encode($tf_visitor_details)
			];

			$order_data = array(
				'post_id'          => $post_id,
				'post_type'        => 'tour',
				'room_number'      => null,
				'check_in'         => $start_date,
				'check_out'        => $end_date,
				'billing_details'  => $billing_details,
				'shipping_details' => $shipping_details,
				'order_details'    => $order_details,
				'payment_method'   => 'offline',
				'customer_id'	   => $tf_offline_user_id,
				'status'           => 'completed',
				'order_date'       => gmdate( 'Y-m-d H:i:s' ),
			);
			$response['without_payment'] = 'true';
			$order_id = Helper::tf_set_order( $order_data );
			if ( function_exists('is_tf_pro') && is_tf_pro() && !empty($order_id) ) {
				do_action( 'tf_offline_payment_booking_confirmation', $order_id, $order_data );
			}

		}else{
			if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {

				$tf_tours_data['tf_tours_data']['order_type']     = 'tour';
				$tf_tours_data['tf_tours_data']['post_author']    = $post_author;
				$tf_tours_data['tf_tours_data']['tour_type']      = $tour_type;
				$tf_tours_data['tf_tours_data']['tour_id']        = $post_id;
				$tf_tours_data['tf_tours_data']['post_permalink'] = get_permalink( $post_id );

				$tf_tours_data['tf_tours_data']['start_date']       = $start_date;
				$tf_tours_data['tf_tours_data']['end_date']         = $end_date;
				$tf_tours_data['tf_tours_data']['tour_date']        = $tour_date;
				$tf_tours_data['tf_tours_data']['tour_extra_total'] = $tour_extra_total;
				// Visitor Details
				$tf_tours_data['tf_tours_data']['visitor_details'] = wp_json_encode($tf_visitor_details);
				if($tour_extra_title){
					$tf_tours_data['tf_tours_data']['tour_extra_title'] = $tour_extra_title;
				}
				# Discount informations
				$discount_type    = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
				$discounted_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';

				if ( $tour_type == 'continuous' ) {
					$tf_tours_data['tf_tours_data']['tour_time'] = !empty($tour_time_title) ? $tour_time_title : '';
				}

				# Calculate discounted price
				if ( $discount_type == 'percent' ) {

					$adult_price    = floatval(preg_replace('/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $discounted_price ), 2 )));
					$children_price = floatval(preg_replace('/[^\d.]/', '', number_format( $children_price - ( ( $children_price / 100 ) * $discounted_price ), 2 )));
					$infant_price   = floatval(preg_replace('/[^\d.]/', '', number_format( $infant_price - ( ( $infant_price / 100 ) * $discounted_price ), 2 )));
					$group_price    = floatval(preg_replace('/[^\d.]/', '', number_format( $group_price - ( ( $group_price / 100 ) * $discounted_price ), 2 )));

				} elseif ( $discount_type == 'fixed' ) {

					$adult_price    = floatval(preg_replace('/[^\d.]/', '', number_format( ( $adult_price - $discounted_price ), 2 )));
					$children_price = floatval(preg_replace('/[^\d.]/', '', number_format( ( $children_price - $discounted_price ), 2 )));
					$infant_price   = floatval(preg_replace('/[^\d.]/', '', number_format( ( $infant_price - $discounted_price ), 2 )));
					$group_price    = floatval(preg_replace('/[^\d.]/', '', number_format( ( $group_price - $discounted_price ), 2 )));

				}

				# Set pricing based on pricing rule
				if ( $pricing_rule == 'group' ) {

					$tf_tours_data['tf_tours_data']['price']     = $group_price;
					$tf_tours_data['tf_tours_data']['adults']    = $adults;
					$tf_tours_data['tf_tours_data']['childrens'] = $children;
					$tf_tours_data['tf_tours_data']['infants']   = $infant;

				} else {

					$tf_tours_data['tf_tours_data']['price']     = ( $adult_price * $adults ) + ( $children * $children_price ) + ( $infant * $infant_price );
					$tf_tours_data['tf_tours_data']['adults']    = $adults . " × " . wp_strip_all_tags(wc_price( $adult_price ));
					$tf_tours_data['tf_tours_data']['childrens'] = $children . " × " . wp_strip_all_tags(wc_price( $children_price ));
					$tf_tours_data['tf_tours_data']['infants']   = $infant . " × " . wp_strip_all_tags(wc_price( $infant_price ));
				}

				# Deposit information
				tf_get_deposit_amount( $meta, $tf_tours_data['tf_tours_data']['price'], $deposit_amount, $has_deposit );
				if ( function_exists('is_tf_pro') && is_tf_pro() && $has_deposit == true && $make_deposit == true ) {
					$tf_tours_data['tf_tours_data']['due']   = $tf_tours_data['tf_tours_data']['price'] - $deposit_amount;
					$tf_tours_data['tf_tours_data']['price'] = $deposit_amount;
				}

				if( 2==$tf_booking_type && !empty($tf_booking_url) ){
					$external_search_info = array(
						'{adult}'    => $adults,
						'{child}'    => $children,
						'{booking_date}' => $tour_date,
						'{infant}'     => $infant
					);
					if(!empty($tf_booking_attribute)){
						$tf_booking_query_url = str_replace(array_keys($external_search_info), array_values($external_search_info), $tf_booking_query_url);
						if( !empty($tf_booking_query_url) ){
							$tf_booking_url = $tf_booking_url.'/?'.$tf_booking_query_url;
						}
					}

					$response['product_id']  = $product_id;
					$response['add_to_cart'] = 'true';
					$response['redirect_to'] = $tf_booking_url;
				}else{
					// Add product to cart with the custom cart item data
					WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_tours_data );

					$response['product_id']  = $product_id;
					$response['add_to_cart'] = 'true';
					$response['redirect_to'] = wc_get_checkout_url();
				}

			} else {
				# Show errors
				$response['status'] = 'error';

			}
			$response['without_payment'] = 'false';
		}
	}
}