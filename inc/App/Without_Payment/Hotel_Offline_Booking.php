<?php 

namespace Tourfic\App\Without_Payment;

use Tourfic\Core\Without_Payment_Booking;
use Tourfic\Classes\Hotel\Hotel;
use Tourfic\Classes\Helper;
use Tourfic\Classes\Hotel\Pricing;

// don't call the file directly
defined( 'ABSPATH' ) || exit;

class Hotel_Offline_Booking extends Without_Payment_Booking{

    use \Tourfic\Traits\Singleton;

    protected array $args = array(
        "post_type" => "tf_hotel"
    );

    function __construct(){
        parent::__construct($this->args);
    }

    function without_payment_booking_popup_callback() {
		// Check nonce security
		if ( ! isset( $_POST['tf_room_booking_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tf_room_booking_nonce'] ) ), 'check_room_booking_nonce' ) ) {
			return;
		}

		// Declaring errors & hotel data array
		$response = [];
		/**
		 * Data from booking form
		 *
		 * With errors
		 */
		$post_id         = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : null;
		$room_id         = isset( $_POST['room_id'] ) ? intval( sanitize_text_field( $_POST['room_id'] ) ) : null;
		$unique_id       = isset( $_POST['unique_id'] ) ? intval( sanitize_text_field( $_POST['unique_id'] ) ) : null;
		$option_id       = isset( $_POST['option_id'] ) ? sanitize_text_field( $_POST['option_id'] ) : null;
		$location        = isset( $_POST['location'] ) ? sanitize_text_field( $_POST['location'] ) : '';
		$adult           = isset( $_POST['adult'] ) ? intval( sanitize_text_field( $_POST['adult'] ) ) : '0';
		$child           = isset( $_POST['child'] ) ? intval( sanitize_text_field( $_POST['child'] ) ) : '0';
		$children_ages   = isset( $_POST['children_ages'] ) ? sanitize_text_field( $_POST['children_ages'] ) : '0';
		$room_selected   = isset( $_POST['room'] ) ? intval( sanitize_text_field( $_POST['room'] ) ) : '0';
		$check_in        = isset( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
		$check_out       = isset( $_POST['check_out_date'] ) ? sanitize_text_field( $_POST['check_out_date'] ) : '';
		$deposit         = isset( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : false;
		$airport_service = isset( $_POST['airport_service'] ) ? sanitize_text_field( $_POST['airport_service'] ) : '';
		$extras = isset( $_POST['extras'] ) ? $_POST['extras'] : [];

		$total_people    = $adult + $child;

		# Calculate night number
		$day_difference = 0;
		if ( $check_in && $check_out ) {
			$check_in_stt   = strtotime( $check_in . ' +1 day' );
			$check_out_stt  = strtotime( $check_out );
			$day_difference = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
		}

		// Check errors
		if ( ! $check_in ) {
			$response['errors'][] = esc_html__( 'Check-in date missing.', 'tourfic' );
		}
		if ( ! $check_out ) {
			$response['errors'][] = esc_html__( 'Check-out date missing.', 'tourfic' );
		}
		if ( ! $adult ) {
			$response['errors'][] = esc_html__( 'Select Adult(s).', 'tourfic' );
		}
		if ( ! $room_selected ) {
			$response['errors'][] = esc_html__( 'Select Room(s).', 'tourfic' );
		}
		if ( ! $post_id ) {
			$response['errors'][] = esc_html__( 'Unknown Error! Please try again.', 'tourfic' );
		}

		/**
		 * Backend options panel data
		 *
		 * @since 2.2.0
		 */
		$product_id  = get_post_meta( $post_id, 'product_id', true );
		$post_author = get_post_field( 'post_author', $post_id );
		$meta        = get_post_meta( $post_id, 'tf_hotels_opt', true );
		$room_meta   = get_post_meta( $room_id, 'tf_room_opt', true );
		// if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
		// 	$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
		// 		return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		// 	}, $rooms );
		// 	$rooms                = unserialize( $tf_hotel_rooms_value );
		// }
		$avail_by_date = ! empty( $room_meta['avil_by_date'] ) && $room_meta['avil_by_date'];
		if ( $avail_by_date ) {
			$avail_date = ! empty( $room_meta['avail_date'] ) ? json_decode( $room_meta['avail_date'], true ) : [];
		}
		$room_name       = get_the_title( $room_id );
		$pricing_by      = $room_meta['pricing-by'];
		$price_multi_day = ! empty( $room_meta['price_multi_day'] ) ? $room_meta['price_multi_day'] : false;

		$room_stay_requirements = array();
        $room_stay_requirements[] = array(
            "uid"      => ! empty( $room_meta["unique_id"] ) ? $room_meta["unique_id"] : '',
            'min_stay' => ! empty( $room_meta["minimum_stay_requirement"] ) ? $room_meta["minimum_stay_requirement"] : 0,
            "max_stay" => ! empty( $room_meta["maximum_stay_requirement"] ) ? $room_meta["maximum_stay_requirement"] : 0
        );

		$total_extras_title = [];
		$total_extras_price = 0;
		$hotel_extra_option     = ! empty( $meta['hotel_extra_option'] ) ? $meta['hotel_extra_option'] : '';
		if(function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($hotel_extra_option)){
			$hotel_extras     = ! empty( $meta['hotel-extra'] ) ? Helper::tf_data_types($meta['hotel-extra']) : [];
			foreach ( $extras as $key => $extra ) {
				$extra_service = Helper::tf_hotel_extras_title_price( $post_id, $adult, $child, $extra );
				$total_extras_title[] = $hotel_extras[$extra]['title'];
				$total_extras_price += $extra_service['price'];
			}
		}
		$total_extras_titles = implode(',', $total_extras_title);

		foreach ( $room_stay_requirements as $min_max_days ) {
			if ( $day_difference < $min_max_days["min_stay"] && $min_max_days["min_stay"] > 0 ) {
				if ( $min_max_days["uid"] == $unique_id ) {
					if ( $min_max_days["max_stay"] == 0 ) {
						// translators: %d is the minimum number of stay days required.
						$response['errors'][] = sprintf( esc_html__( 'Your Stay Requirement is Minimum %d Days', 'tourfic' ),
							intval( $min_max_days['min_stay'] )
						);

					} else {
						// translators: %1$d is minimum stay days, %2$d is maximum stay days.
						$response['errors'][] = sprintf(esc_html__( 'Your Stay Requirement is Minimum %1$d Days to Maximum %2$d Days', 'tourfic' ),
							intval( $min_max_days['min_stay'] ),
							intval( $min_max_days['max_stay'] )
						);


					}
				}
			} else if ( $day_difference > $min_max_days["max_stay"] && $min_max_days["max_stay"] > 0 ) {
				if ( $min_max_days["uid"] == $unique_id ) {
					// translators: %d is the maximum stay days allowed.
					$response['errors'][] = sprintf(esc_html__( 'Your Maximum Stay Requirement is %d Days', 'tourfic' ),
						intval( $min_max_days['max_stay'] )
					);

				}
			}
		}
		// Hotel Room Discount Data
		$hotel_discount_type   = ! empty( $room_meta["discount_hotel_type"] ) ? $room_meta["discount_hotel_type"] : "none";
		$hotel_discount_amount = ! empty( $room_meta["discount_hotel_price"] ) ? $room_meta["discount_hotel_price"] : 0;

		/**
		 * Set room availability
		 */
		$unique_id          = ! empty( $room_meta['unique_id'] ) ? $room_meta['unique_id'] : '';
		$order_ids          = ! empty( $room_meta['order_id'] ) ? $room_meta['order_id'] : '';
		$num_room_available = ! empty( $room_meta['num-room'] ) ? $room_meta['num-room'] : '1';
		$multi_by_date_ck = ! empty( $room_meta['price_multi_day'] ) ? ! empty( $room_meta['price_multi_day'] ) : false;
		$reduce_num_room    = ! empty( $room_meta['reduce_num_room'] ) ? $room_meta['reduce_num_room'] : false;
		$number_orders      = '0';
		$avil_by_date       = ! empty( $room_meta['avil_by_date'] ) ? $room_meta['avil_by_date'] : false;      // Room Available by date enabled or  not ?
		if ( $avil_by_date ) {
			$avail_date = ! empty( $room_meta['avail_date'] ) ? json_decode($room_meta['avail_date'], true) : [];
		}

		// Check availability by date option
		$tfperiod = new \DatePeriod(
			new \DateTime( $check_in . ' 00:00' ),
			new \DateInterval( 'P1D' ),
			(new \DateTime( $check_out . ' 23:59' ))
		);

		$avail_durationdate = [];
		$is_first = true;
		foreach ( $tfperiod as $date ) {
			if($multi_by_date_ck){
				if ($is_first) {
					$is_first = false;
					continue;
				}
			}
			$avail_durationdate[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
		}

		// Get the original (default language) post ID using WPML
		if ( function_exists( 'wpml_get_default_language' ) ) {
			$original_hotel_id = apply_filters( 'wpml_object_id', $post_id, 'tf_hotel', false, wpml_get_default_language() );
		} else {
			$original_hotel_id = $post_id;
		}
		//room inventory manage
		if ( ! empty( $order_ids ) && $reduce_num_room == true ) {

			# Get backend available date range as an array
			if ( !empty( $avil_by_date ) ) {

				$order_date_ranges = array();

				$backend_date_ranges = array();
				foreach ( $avail_date as $single_date_range ) {

					if(is_array($single_date_range) && !empty( $single_date_range["availability"] )){
						array_push( $backend_date_ranges, array( strtotime( $single_date_range["availability"]["from"] ), strtotime( $single_date_range["availability"]["to"] ) ) );
					}
				}
			}

			# Convert order ids to array
			$order_ids = explode( ',', $order_ids );
			$room_bookings_per_day = array();

			foreach ($avail_durationdate as $available_date) {
				$available_timestamp = strtotime($available_date);

				$room_booked_today = 0;

				foreach ($order_ids as $order_id) {

					# Get completed orders
					$tf_orders_select = array(
						'select' => "post_id,order_details",
						'post_type' => 'hotel',
						'query' => " AND ostatus = 'completed' AND order_id = ".$order_id." AND post_id = ".$original_hotel_id
					);
					$tf_hotel_book_orders = Helper::tourfic_order_table_data($tf_orders_select);

					foreach ($tf_hotel_book_orders as $item) {
						$order_details = json_decode($item['order_details']);
						$order_check_in_date  = strtotime($order_details->check_in);
						$order_check_out_date = strtotime($order_details->check_out);
						$ordered_number_of_room = !empty($order_details->room) ? $order_details->room : 0;

						# Check if the order's date range overlaps with the current available date
						if($multi_by_date_ck){
							if ($order_check_in_date < $available_timestamp && $order_check_out_date >= $available_timestamp) {
								$room_booked_today += $ordered_number_of_room;
							}
						} else {
							if ($order_check_in_date <= $available_timestamp && $order_check_out_date >= $available_timestamp) {
								$room_booked_today += $ordered_number_of_room;
							}
						}
					}
				}

				# Track room availability for this specific date
				$room_bookings_per_day[$available_date] = $room_booked_today;
			}

			# Find the maximum number of rooms booked on any day within the date range
			$number_orders = !empty($room_bookings_per_day) ? max($room_bookings_per_day) : 0;

			# Calculate available rooms
			$num_room_available = $num_room_available - $number_orders;
			$num_room_available = max($num_room_available, 0);
		}

		if ( $room_selected > $num_room_available ) {

			if ( $num_room_available > 0 ) {
				/* translators: %1$s Available rooms */
				$response['errors'][] = sprintf(
					esc_html__( 'Only %1$s room(s) available for the selected date.', 'tourfic' ),
					$num_room_available
				);
			} else {
				$response['errors'][] = esc_html__( 'No rooms available for the selected date.', 'tourfic' );
			}
		}

		/**
		 * If no errors then process
		 */
		if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {

			ob_start();
			Hotel::hotel_booking_popup( $post_id, $room_id, $adult, $child );
			$response['booking_popup'] = ob_get_clean();

			// Discount Calculation and Checking
			$adult_price = ! empty( $room_meta['adult_price'] ) ? $room_meta['adult_price'] : 0;
			$child_price = ! empty( $room_meta['child_price'] ) ? $room_meta['child_price'] : 0;
			$room_price  = ! empty( $room_meta['price'] ) ? $room_meta['price'] : 0;

			if ( $hotel_discount_type == "percent" ) {
				if ( $pricing_by == 1 ) {
					$room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (float) $room_price - ( ( (float) $room_price / 100 ) * (float) $hotel_discount_amount ), 2 ) ) );
				}
				if ( $pricing_by == 2 ) {
					$adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (float) $adult_price - ( ( (float) $adult_price / 100 ) * (float) $hotel_discount_amount ), 2 ) ) );
					$child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (float) $child_price - ( ( (float) $child_price / 100 ) * (float) $hotel_discount_amount ), 2 ) ) );
				}
			}

			/**
			 * Calculate Pricing
			 */
			if ( $avail_by_date && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

				// Check availability by date option
				$period = new \DatePeriod(
					new \DateTime( $check_in . ' 00:00' ),
					new \DateInterval( 'P1D' ),
					new \DateTime( $check_out . ' 00:00' )
				);

				$total_price = 0;
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

						if ( $hotel_discount_type == "percent" ) {
							if ( $pricing_by == 1 ) {
								$room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (float) $room_price - ( ( (float) $room_price / 100 ) * (float) $hotel_discount_amount ), 2 ) ) );
							}
							if ( $pricing_by == 2 ) {
								$adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (float) $adult_price - ( ( (float) $adult_price / 100 ) * (float) $hotel_discount_amount ), 2 ) ) );
								$child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (float) $child_price - ( ( (float) $child_price / 100 ) * (float) $hotel_discount_amount ), 2 ) ) );
							}
						}
						
						if ( $pricing_by == '1' ) {
							$total_price += (float) $room_price;
						} elseif ( $pricing_by == '2' ) {
							$total_price += ( (float) $adult_price * (int) $adult ) + ( (float) $child_price * (int) $child );
						} elseif ( $pricing_by == '3' ) {
							$data          = $available_rooms[0];
							$options_count = $data['options_count'] ?? 0;
							$unique_id     = ! empty( $room_meta['unique_id'] ) ? $room_meta['unique_id'] : '';

							for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
								$_option_id = $unique_id . '_' . $i;
								if ( $_option_id == $option_id ) {
									if ( $data[ 'tf_room_option_' . $i ] == '1' && $data[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
										$room_price  = ! empty( $data[ 'tf_option_room_price_' . $i ] ) ? $data[ 'tf_option_room_price_' . $i ] : 0;
										$room_price  = Pricing::apply_discount($room_price, $hotel_discount_type, $hotel_discount_amount);
										$total_price += $room_price;
									} else if ( $data[ 'tf_room_option_' . $i ] == '1' && $data[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
										$adult_price = ! empty( $data[ 'tf_option_adult_price_' . $i ] ) ? $data[ 'tf_option_adult_price_' . $i ] : 0;
										$child_price = ! empty( $data[ 'tf_option_child_price_' . $i ] ) ? $data[ 'tf_option_child_price_' . $i ] : 0;
										$adult_price = Pricing::apply_discount($adult_price, $hotel_discount_type, $hotel_discount_amount);
										$child_price = Pricing::apply_discount($child_price, $hotel_discount_type, $hotel_discount_amount);

										$total_price += ( $adult_price * $adult ) + ( $child_price * $child );
									}

								}
							}
						}
					};

				}

				$price_total = (float) $total_price * (int) $room_selected;
			} else {

				if ( $pricing_by == '1' ) {

					$total_price = $room_meta['price'];

					if ( $hotel_discount_type == "percent" ) {
						$total_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (float) $total_price - ( ( (float) $total_price / 100 ) * (float) $hotel_discount_amount ), 2 ) ) );
					} else if ( $hotel_discount_type == "fixed" ) {
						$total_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (float) $adult_price - (float) $hotel_discount_amount ), 2 ) ) );
					}
				} elseif ( $pricing_by == '2' ) {
					$adult_price = $room_meta['adult_price'];
					$child_price = $room_meta['child_price'];

					if ( $hotel_discount_type == "percent" ) {
						$adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (float) $adult_price - ( ( (float) $adult_price / 100 ) * (float) $hotel_discount_amount ), 2 ) ) );
						$child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (float) $child_price - ( ( (float) $child_price / 100 ) * (float) $hotel_discount_amount ), 2 ) ) );
					}
					$adult_price = (float) $adult_price * (int) $adult;
					$child_price = (float) $child_price * (int) $child;
					$total_price = (float) $adult_price + (float) $child_price;
				} elseif ( $pricing_by == '3' ) {
					$room_options = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];
					$unique_id    = ! empty( $room_meta['unique_id'] ) ? $room_meta['unique_id'] : '';

					if ( ! empty( $room_options ) ) {
						foreach ( $room_options as $room_option_key => $room_option ) {
							$_option_id = $unique_id . '_' . $room_option_key;
							if ( $_option_id == $option_id ) {
								$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
								if ( $option_price_type == 'per_room' ) {
									$total_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
									$total_price = Pricing::apply_discount($total_price, $hotel_discount_type, $hotel_discount_amount);
								} elseif ( $option_price_type == 'per_person' ) {
									$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
									$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;

									$option_adult_price = Pricing::apply_discount($option_adult_price, $hotel_discount_type, $hotel_discount_amount);
									$option_child_price = Pricing::apply_discount($option_child_price, $hotel_discount_type, $hotel_discount_amount);
									$total_price        = ( $option_adult_price * $adult ) + ( $option_child_price * $child );
								}
							}
						}
					}
				}

				# Multiply pricing by night number
				if ( ! empty( $day_difference ) && $price_multi_day == true ) {
					$price_total = $total_price * $room_selected * $day_difference;
				} else {
					$price_total = $total_price * ( $room_selected * $day_difference + 1 );
				}

			}

			$airport_service_arr = Hotel::tf_hotel_airport_service_title_price( $post_id, $adult, $child, $airport_service );

			# check for deposit
			if ( $deposit == "true" ) {

				Helper::tf_get_deposit_amount( $room_meta, $price_total, $deposit_amount, $has_deposit );
				if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) {
						if ( ! empty( $airport_service ) || ! empty( $total_extras_price ) ) {
							$tf_due_amount = ( $price_total + $airport_service_arr['price'] + $total_extras_price ) - $deposit_amount;
						} else {
							$tf_due_amount = $price_total - $deposit_amount;
						}
					$tf_due_amount = $price_total - $deposit_amount;
				}
			}

			$hotel_guest_info_fields = ! empty( tfopt( 'hotel_guest_info_fields' ) ) ? tf_data_types( tfopt( 'hotel_guest_info_fields' ) ) : '';

			$response['guest_info']            = '';
			$response['hotel_booking_summery'] = '';
			$date_format = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

			// Map PHP date format to JS-friendly placeholder
			$placeholders = array(
				'Y/m/d' => 'YYYY/MM/DD',
				'd/m/Y' => 'DD/MM/YYYY',
				'm/d/Y' => 'MM/DD/YYYY',
				'Y-m-d' => 'YYYY-MM-DD',
				'd-m-Y' => 'DD-MM-YYYY',
				'm-d-Y' => 'MM-DD-YYYY',
				'Y.m.d' => 'YYYY.MM.DD',
				'd.m.Y' => 'DD.MM.YYYY',
				'm.d.Y' => 'MM.DD.YYYY',
			);
			$placeholder = isset( $placeholders[ $date_format ] ) ? $placeholders[ $date_format ] : 'YYYY/MM/DD';
			
			for ( $guest_in = 1; $guest_in <= $total_people; $guest_in ++ ) {
				$response['guest_info'] .= '<div class="tf-single-tour-traveller tf-single-travel">
                <h4>' . sprintf( esc_html__( 'Guest ', 'tourfic' ) ) . $guest_in . '</h4>
                <div class="traveller-info">';
				if ( empty( $hotel_guest_info_fields ) ) {
					$response['guest_info'] .= '<div class="traveller-single-info">
                        <label for="tf_full_name' . $guest_in . '">' . sprintf( esc_html__( 'Full Name', 'tourfic' ) ) . '</label>
                        <input type="text" name="guest[' . $guest_in . '][tf_full_name]" id="tf_full_name' . $guest_in . '" data-required="1" />
                        <div class="error-text" data-error-for="tf_full_name' . $guest_in . '"></div>
                    </div>
                    <div class="traveller-single-info">
                        <label for="tf_dob' . $guest_in . '">' . sprintf( esc_html__( 'Date of birth', 'tourfic' ) ) . '</label>
                        <input type="date" name="guest[' . $guest_in . '][tf_dob]" id="tf_dob' . $guest_in . '" data-required="1" />
						<input type="text" class="tf-date-picker" 
							name="traveller[' . $guest_in . '][tf_dob]" 
							id="tf_dob' . $guest_in . '" 
							data-required="1" 
							placeholder="' . esc_attr( $placeholder ) . '" 
							data-format="' . esc_attr( $date_format ) . '" />
                        <div class="error-text" data-error-for="tf_dob' . $guest_in . '"></div>
                    </div>
                    <div class="traveller-single-info">
                        <label for="tf_nid' . $guest_in . '">' . sprintf( esc_html__( 'NID', 'tourfic' ) ) . '</label>
                        <input type="text" name="guest[' . $guest_in . '][tf_nid]" id="tf_nid' . $guest_in . '" data-required="1" />
                        <div class="error-text" data-error-for="tf_nid' . $guest_in . '"></div>
                    </div>
                    ';
				} else {
					foreach ( $hotel_guest_info_fields as $field ) {
						$reg_field_required = !empty( $field['reg-field-required'] ) ? esc_attr( $field['reg-field-required'] ) : 0;
						$number_field_min_attribuite = $field['reg-fields-type'] == "number" ? 'min="0"' : '';
						if ( "text" == $field['reg-fields-type'] || "number" == $field['reg-fields-type'] || "email" == $field['reg-fields-type'] ) {
							$response['guest_info'] .= '
                            <div class="traveller-single-info">
                                <label for="' . $field['reg-field-name'] . $guest_in . '">' . esc_html( $field['reg-field-label'] ) . '</label>
                                <input type="' . $field['reg-fields-type'] . '" name="guest[' . $guest_in . '][' . $field['reg-field-name'] . ']" data-required="' . $reg_field_required . '" id="' . $field['reg-field-name'] . $guest_in . '"' . $number_field_min_attribuite .' />
                                <div class="error-text" data-error-for="' . $field['reg-field-name'] . $guest_in . '"></div>
                            </div>';
						}
						if ( "date" == $field['reg-fields-type'] ) {
							$response['guest_info'] .= '
                            <div class="traveller-single-info">
                                <label for="' . $field['reg-field-name'] . $guest_in . '">' . esc_html( $field['reg-field-label'] ) . '</label>
                                <input type="date" class="tf-date-picker" name="guest[' . $guest_in . '][' . $field['reg-field-name'] . ']" data-required="' . $reg_field_required . '" id="' . $field['reg-field-name'] . $guest_in . '"' . $number_field_min_attribuite .' placeholder="' . esc_attr( $placeholder ) . '" data-format="' . esc_attr( $date_format ) . '" />
                                <div class="error-text" data-error-for="' . $field['reg-field-name'] . $guest_in . '"></div>
                            </div>';
						}
						if ( "select" == $field['reg-fields-type'] && ! empty( $field['reg-options'] ) ) {
							$response['guest_info'] .= '
                            <div class="traveller-single-info">
                                <label for="' . $field['reg-field-name'] . $guest_in . '">' . esc_html( $field['reg-field-label'] ) . '</label>
                                <select id="' . $field['reg-field-name'] . $guest_in . '" name="guest[' . $guest_in . '][' . $field['reg-field-name'] . ']" data-required="' . $field['reg-field-required'] . '"><option value="">' . sprintf( esc_html__( 'Select One', 'tourfic' ) ) . '</option>';
							foreach ( $field['reg-options'] as $sfield ) {
								if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) {
									$response['guest_info'] .= '<option value="' . $sfield['option-value'] . '">' . $sfield['option-label'] . '</option>';
								}
							}
							$response['guest_info'] .= '</select>
                                <div class="error-text" data-error-for="' . $field['reg-field-name'] . $guest_in . '"></div>
                            </div>';
						}
						if ( ( "checkbox" == $field['reg-fields-type'] || "radio" == $field['reg-fields-type'] ) && ! empty( $field['reg-options'] ) ) {
							$response['guest_info'] .= '
                            <div class="traveller-single-info">
                            <label for="' . $field['reg-field-name'] . $guest_in . '">' . esc_html( $field['reg-field-label'] ) . '</label>
                            ';
							foreach ( $field['reg-options'] as $sfield ) {
								if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) {
									$response['guest_info'] .= '
                                        <div class="tf-single-checkbox">
                                        <input type="' . esc_attr( $field['reg-fields-type'] ) . '" name="guest[' . $guest_in . '][' . $field['reg-field-name'] . '][]" id="' . $sfield['option-value'] . $guest_in . '" value="' . $sfield['option-value'] . '" data-required="' . $field['reg-field-required'] . '" />
                                        <label for="' . $sfield['option-value'] . $guest_in . '">' . esc_html( $sfield['option-label'] ) . '</label></div>';
								}
							}
							$response['guest_info'] .= '
                            <div class="error-text" data-error-for="' . $field['reg-field-name'] . $guest_in . '"></div>
                            </div>';
						}
					}
				}

				$response['guest_info'] .= '</div>
            </div>';
				$date_format_for_users  = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";

			}

			$response['hotel_booking_summery'] .= '<h6>' . esc_html__( 'From ', 'tourfic' ) . date_i18n( $date_format_for_users, strtotime( $check_in ) ) . esc_html__( ' to ', 'tourfic' ) . date_i18n( $date_format_for_users, strtotime( $check_out ) ) . '</h6>
        <table class="table" style="width: 100%">
            <thead>
                <tr>
                    <th align="left">' . sprintf( esc_html__( 'Guest', 'tourfic' ) ) . '</th>
                    <th align="right">' . sprintf( esc_html__( 'Price', 'tourfic' ) ) . '</th>
                </tr>
            </thead>
            <tbody>';

			if ( ! empty( $room_selected ) ) {
				$response['hotel_booking_summery'] .= '<tr>
                    <td align="left">' .
					/* translators: 1: total room, 2: total night */
					sprintf( esc_html__( '%1$s Room × %2$s Night', 'tourfic' ), $room_selected, $day_difference ) . '</td>
                    <td align="right">' . wc_price( $price_total ) . '</td>
                </tr>';
			}

			if ( ! empty( $airport_service_arr['title'] ) ) {
				$response['hotel_booking_summery'] .= '<tr>
						<td align="left">' . esc_html( $airport_service_arr['label'] ) . '</td>
						<td align="right">' . wc_price( $airport_service_arr['price'] ) . '</td>
					</tr>';
			}

			if ( !empty($hotel_extra_option) && ! empty( $total_extras_titles ) ) {
				$response['hotel_booking_summery'] .= '<tr>
						<td align="left">' . esc_html( $total_extras_titles ) . '</td>
						<td align="right">' . wc_price( $total_extras_price ) . '</td>
					</tr>';
			}

			if ( ! empty( $tf_due_amount ) ) {
				$response['hotel_booking_summery'] .= '<tr>
                    <td align="left">' . sprintf( esc_html__( 'Due', 'tourfic' ) ) . '</td>
                    <td align="right">' . wc_price( $tf_due_amount + $airport_service_arr['price'] + $total_extras_price ) . '</td>
                </tr>';
			}

			$total_price = ! empty( $tf_due_amount ) ? wc_price( $price_total - $tf_due_amount ) : ( !empty( $airport_service_arr['price'] ) || !empty( $total_extras_price ) ? wc_price( $price_total + $airport_service_arr['price'] + $total_extras_price ) : wc_price( $price_total ) );

			$response['hotel_booking_summery'] .= '</tbody>
            <tfoot>
                <tr>
                    <th align="left">' . esc_html__( 'Total', 'tourfic' ) . '</th>
                    <th align="right" data-hotel-total-price="' . $price_total . '">' . $total_price . '</th>
                </tr>
            </tfoot>
        </table>';

		} else {
			$response['status'] = 'error';
		}

		// Json Response
		echo wp_json_encode( $response );

		die();
	}

}