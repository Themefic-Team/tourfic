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

		// Booking Type
		$tf_booking_type      = function_exists( 'is_tf_pro' ) && is_tf_pro() ? ( ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1 ) : 1;
		$tf_booking_url       = function_exists( 'is_tf_pro' ) && is_tf_pro() ? ( ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '' ) : '';
		$tf_booking_query_url = function_exists( 'is_tf_pro' ) && is_tf_pro() ? ( ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&infant={infant}' ) : '';
		$tf_booking_attribute = function_exists( 'is_tf_pro' ) && is_tf_pro() ? ( ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '' ) : '';

		if ( $tour_type == 'fixed' ) {

			if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
				$tf_tour_fixed_avail   = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['fixed_availability'] );
				$tf_tour_fixed_date    = unserialize( $tf_tour_fixed_avail );
				$start_date            = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
				$end_date              = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
			} else {
				$start_date            = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
				$end_date              = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
			}

			if ( ! function_exists( "selected_day_diff" ) ) {
				function selected_day_diff( $start_date, $end_date ) {
					if ( ! empty( $start_date ) && ! empty( $end_date ) ) {

						$start_date = new \DateTime( $start_date );
						$end_date   = new \DateTime( $end_date );
						$interval   = $start_date->diff( $end_date );

						return $interval->days;
					}
				}
			}

			if ( ! function_exists( "end_date_calculation" ) ) {
				function end_date_calculation( $start_date, $difference ) {
					if ( ! empty( $start_date ) && ! empty( $difference ) ) {
						if ( str_contains( $start_date, ' - ' ) ) {
							return $start_date;

						} else {

							$start_date  = new \DateTime( $start_date );
							$new_end_day = $start_date->modify( "+ $difference day" );

							return $new_end_day->format( 'Y/m/d' );
						}
					}
				}
			}

			if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
				$day_diff = selected_day_diff( $start_date, $end_date );
			}

			if ( ! empty( $tour_type ) && ( $tour_type == "fixed" ) ) {
				$start_date = ! empty( $_POST['check-in-out-date'] ) ? sanitize_text_field( $_POST['check-in-out-date'] ) : '';
			}

			if ( ! empty( $start_date ) && ! empty( $day_diff ) ) {
				$end_date = end_date_calculation( $start_date, $day_diff );
			}

		} elseif ( $tour_type == 'continuous' ) {

			$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;

			if ( $custom_avail == true ) {
				$pricing_rule     = $meta['custom_pricing_by'];
			}

		}

		if ( $tour_type == 'continuous' ) {
			$start_date = $end_date = $tour_date;
		}

		// Tour extra
		$tour_extra_total     = 0;
		$tour_extra_title_arr = [];

		$tour_extra_meta = ! empty( $meta['tour-extra'] ) ? $meta['tour-extra'] : '';
		if ( ! empty( $tour_extra_meta ) ) {
			$tours_extra         = explode( ',', $_POST['tour_extra'] );
			$tour_extra_quantity = explode( ',', $_POST["tour_extra_quantity"] );
			foreach ( $tours_extra as $extra_key => $extra ) {
				$tour_extra_pricetype = ! empty( $tour_extra_meta[ $extra ]['price_type'] ) ? $tour_extra_meta[ $extra ]['price_type'] : 'fixed';
				if ( $tour_extra_pricetype == "fixed" ) {
					if ( ! empty( $tour_extra_meta[ $extra ]['title'] ) && ! empty( $tour_extra_meta[ $extra ]['price'] ) ) {
						$tour_extra_total       += $tour_extra_meta[ $extra ]['price'];
						$tour_extra_title_arr[] = $tour_extra_meta[ $extra ]['title'] . " (Fixed: " . wc_price( $tour_extra_meta[ $extra ]['price'] ) . ")";
					}
				} else if ( $tour_extra_pricetype == "quantity" ) {
					if ( ! empty( $tour_extra_meta[ $extra ]['title'] ) && ! empty( $tour_extra_meta[ $extra ]['price'] ) ) {
						$tour_extra_total       += $tour_extra_meta[ $extra ]['price'] * $tour_extra_quantity[ $extra_key ];
						$tour_extra_title_arr[] = $tour_extra_meta[ $extra ]['title'] . " (Per Unit: " . wc_price( $tour_extra_meta[ $extra ]['price'] ) . '*' . $tour_extra_quantity[ $extra_key ] . "=" . wc_price( $tour_extra_meta[ $extra ]['price'] * $tour_extra_quantity[ $extra_key ] ) . ")";
					}
				} else {
					if ( ! empty( $tour_extra_meta[ $extra ]['price'] ) && ! empty( $tour_extra_meta[ $extra ]['title'] ) ) {
						$tour_extra_total       += ( $tour_extra_meta[ $extra ]['price'] * $total_people );
						$tour_extra_title_arr[] = $tour_extra_meta[ $extra ]['title'] . " (Per Person: " . wc_price( $tour_extra_meta[ $extra ]['price'] ) . '*' . $total_people . "=" . wc_price( $tour_extra_meta[ $extra ]['price'] * $total_people ) . ")";
					}
				}
			}
		}

		$tour_extra_title = ! empty( $tour_extra_title_arr ) ? implode( ",", $tour_extra_title_arr ) : '';


		/**
		 * Check errors
		 *
		 */
		/* Minimum days to book before departure */
		$min_days_before_book = ! empty( $meta['min_days_before_book'] ) ? $meta['min_days_before_book'] : '0';
		$today_stt                 = new \DateTime( gmdate( 'Y-m-d', strtotime( gmdate( 'Y-m-d' ) ) ) );
		$tour_date_stt             = new \DateTime( gmdate( 'Y-m-d', strtotime( $start_date ) ) );
		$adult_required_chield     = ! empty( $meta["require_adult_child_booking"] ) ? $meta["require_adult_child_booking"] : 0;


		/**
		 * Price by date range
		 *
		 * Tour type continuous and custom availability is true
		 */
		$tf_cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
		if ( ! empty( $tf_cont_custom_date ) && gettype( $tf_cont_custom_date ) == "string" ) {
			$tf_tour_conti_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $tf_cont_custom_date );
			$tf_cont_custom_date       = unserialize( $tf_tour_conti_custom_date );
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
			$group_price    = ! empty( $seasional_price[0]['group_price'] ) ? $seasional_price[0]['group_price'] : 0;
			$adult_price    = ! empty( $seasional_price[0]['adult_price'] ) ? $seasional_price[0]['adult_price'] : 0;
			$children_price = ! empty( $seasional_price[0]['child_price'] ) ? $seasional_price[0]['child_price'] : 0;
			$infant_price   = ! empty( $seasional_price[0]['infant_price'] ) ? $seasional_price[0]['infant_price'] : 0;
		} else {
			$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
			$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
			$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
			$infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;
		}

		/**
		 * If no errors then process
		 *
		 * Store custom data in array
		 * Add to cart with custom data
		 */
		if ( ! empty( $tf_booking_type ) && 3 == $tf_booking_type ) {

			// Price Calculation

			$discount_type    = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
			$discounted_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';

			# Calculate discounted price
			if ( $discount_type == 'percent' ) {

				$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $discounted_price ), 2 ) ) );
				$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $children_price - ( ( $children_price / 100 ) * $discounted_price ), 2 ) ) );
				$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( $infant_price - ( ( $infant_price / 100 ) * $discounted_price ), 2 ) ) );
				$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $group_price - ( ( $group_price / 100 ) * $discounted_price ), 2 ) ) );

			} elseif ( $discount_type == 'fixed' ) {

				$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $adult_price - $discounted_price ), 2 ) ) );
				$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $children_price - $discounted_price ), 2 ) ) );
				$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $infant_price - $discounted_price ), 2 ) ) );
				$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $group_price - $discounted_price ), 2 ) ) );

			}

			# Set pricing based on pricing rule
			if ( $pricing_rule == 'group' ) {
				$without_payment_price = $group_price;
			} else {
				$without_payment_price = ( $adult_price * $adult_count ) + ( $child_count * $children_price ) + ( $infant_count * $infant_price );
			}

			$order_details = [
				'order_by'        => '',
				'tour_date'       => $tour_date,
				'tour_time'       => ! empty( $tour_time_title ) ? $tour_time_title : '',
				'tour_extra'      => $tour_extra_title,
				'adult'           => $adults,
				'child'           => $children,
				'infants'         => $infant,
				'total_price'     => $without_payment_price,
				'due_price'       => wc_price( $without_payment_price ),
				'visitor_details' => wp_json_encode( $tf_visitor_details )
			];

		} else {
			if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {

				# Discount informations
				$discount_type    = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
				$discounted_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';

				# Calculate discounted price
				if ( $discount_type == 'percent' ) {
					$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $discounted_price ), 2 ) ) );
					$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $children_price - ( ( $children_price / 100 ) * $discounted_price ), 2 ) ) );
					$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( $infant_price - ( ( $infant_price / 100 ) * $discounted_price ), 2 ) ) );
					$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $group_price - ( ( $group_price / 100 ) * $discounted_price ), 2 ) ) );
				} elseif ( $discount_type == 'fixed' ) {
					$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $adult_price - $discounted_price ), 2 ) ) );
					$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $children_price - $discounted_price ), 2 ) ) );
					$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $infant_price - $discounted_price ), 2 ) ) );
					$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $group_price - $discounted_price ), 2 ) ) );
				}

				# Deposit information
				Helper::tf_get_deposit_amount( $meta, $tf_tours_data['tf_tours_data']['price'], $deposit_amount, $has_deposit );
				if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && $make_deposit == true ) {
					$tf_tours_data['tf_tours_data']['due']   = $tf_tours_data['tf_tours_data']['price'] - $deposit_amount;
					$tf_tours_data['tf_tours_data']['price'] = $deposit_amount;
				}

				if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
					$external_search_info = array(
						'{adult}'        => $adults,
						'{child}'        => $children,
						'{booking_date}' => $tour_date,
						'{infant}'       => $infant
					);
					if ( ! empty( $tf_booking_attribute ) ) {
						$tf_booking_query_url = str_replace( array_keys( $external_search_info ), array_values( $external_search_info ), $tf_booking_query_url );
						if ( ! empty( $tf_booking_query_url ) ) {
							$tf_booking_url = $tf_booking_url . '/?' . $tf_booking_query_url;
						}
					}

					$response['product_id']  = $product_id;
					$response['add_to_cart'] = 'true';
					$response['redirect_to'] = $tf_booking_url;
				} else {
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