<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_Hotel_Backend_Booking_Rest_API' ) ) {
	class TF_Hotel_Backend_Booking_Rest_API extends TF_Rest_API {

		/*
		 * instance
		 */
		private static $instance = null;

		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function __construct() {
			parent::__construct();
		}



		/*
		 * Check available hotel room from date to date
		 * @author Foysal
		 */
		public function tf_available_hotel( $request ) {
			$from = $request->get_param( 'from' );
			$to   = $request->get_param( 'to' );
			$author   = $request->get_param( 'user_id' ) ? $request->get_param( 'user_id' ) : get_current_user_id();

			$loop = new WP_Query( array(
				'post_type'      => 'tf_hotel',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'author'    => $this->user_has_role( $author, 'administrator' ) || $this->user_has_role( $author, 'tf_manager' ) ? '' : $author,
			) );

			$period = '';
			if ( ! empty( $from ) && ! empty( $to ) ) {
				$period = new DatePeriod(
					new DateTime( $from ),
					new DateInterval( 'P1D' ),
					new DateTime( ! empty( $to ) ? $to : '23:59:59' )
				);
			}

			if ( $loop->have_posts() ) {
				$not_found = [];
				while ( $loop->have_posts() ) {
					$loop->the_post();
					tf_filter_hotel_by_date( $period, $not_found, array( 1, 1, 1, '' ) );
				}

				$tf_total_filters = [];
				foreach ( $not_found as $not ) {
					if ( $not['found'] != 1 ) {
						$tf_total_filters[ $not['post_id'] ] = get_the_title( $not['post_id'] );
					}
				}
			}
			wp_reset_postdata();

			return rest_ensure_response( $tf_total_filters );
		}

		/*
		 * Check available hotel room and service type from date to date
		 */
		public function tf_available_hotel_room_and_service_type( $request ) {
			$hotel_id = $request->get_param( 'hotel_id' );
			$from     = $request->get_param( 'from' );
			$to       = $request->get_param( 'to' );

			// Custom avail
			if ( empty( $to ) ) {
				$to = date( 'Y/m/d', strtotime( $from . " + 1 day" ) );
			}
			$from = date( 'Y/m/d', strtotime( $from . ' +1 day' ) );

			/**
			 * Backend data
			 */
			$meta  = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
			$rooms = get_hotel_rooms( $hotel_id);

			$room_array = array();

			if ( ! empty( $rooms ) ) {
				foreach ( $rooms as $_room ) {
					$room = get_post_meta($_room->ID, 'tf_room_opt', true);
					// Check if room is enabled
					$enable = ! empty( $room['enable'] ) && boolval( $room['enable'] );

					if ( $enable ) {
						// Check availability by date option
						$period = new DatePeriod(
							new DateTime( $from . ' 00:00' ),
							new DateInterval( 'P1D' ),
							new DateTime( $to . ' 23:59' )
						);

						$avail_durationdate = [];
						foreach ( $period as $date ) {
							$avail_durationdate[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
						}

						/**
						 * Set room availability
						 */
						$avil_by_date = ! empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : false;      // Room Available by date enabled or  not ?
						if ( $avil_by_date ) {
							$avail_date = ! empty( $room['avail_date'] ) ? json_decode( $room['avail_date'], true ) : [];
						}

						if ( $avil_by_date && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

							foreach ( $period as $date ) {
								$available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
									$date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
									$date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

									return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
								} ) );

								if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {
									$room_array[ $room['unique_id'] ] = get_the_title($_room->ID);
								}
							}
						} else {
							$room_array[ $room['unique_id'] ] = get_the_title($_room->ID);
						}
					}
				}
			}

			//hotel service
			$hotel_services      = array(
				'' => esc_html__( 'Select Service Type', 'tourfic' )
			);
			$hotel_service_avail = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';
			$hotel_service_type  = ! empty( $meta['airport_service_type'] ) ? $meta['airport_service_type'] : '';
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $hotel_service_avail ) && ! empty( $hotel_service_type ) ) {
				foreach ( $hotel_service_type as $single_service_type ) {
					if ( "pickup" == $single_service_type ) {
						$hotel_services['pickup'] = esc_html__( 'Pickup Service', 'tourfic' );
					}
					if ( "dropoff" == $single_service_type ) {
						$hotel_services['dropoff'] = esc_html__( 'Drop-off Service', 'tourfic' );
					}
					if ( "both" == $single_service_type ) {
						$hotel_services['both'] = esc_html__( 'Pickup & Drop-off Service', 'tourfic' );
					}
				}
			}

			return rest_ensure_response( array(
				'rooms'   => $room_array,
				'service' => $hotel_services
			) );
		}

		/*
		 * Check available hotel room number
		 */
		public function tf_available_hotel_room_number( $request ) {
			$hotel_id = $request->get_param( 'hotel_id' );
			$room_id  = $request->get_param( 'room_id' );
			$from  = $request->get_param( 'from' );
			$to  = $request->get_param( 'to' );

			if ( ! empty( $hotel_id ) && ! empty( $room_id ) ) {
				$rooms = get_hotel_rooms($hotel_id);

				if ( ! empty( $rooms ) ) {
					foreach ( $rooms as $_room ) {
						$room = get_post_meta($_room->ID, 'tf_room_opt', true);
						if ( $room['unique_id'] == $room_id ) {

							$avil_by_date = ! empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : false;
							if ( $avil_by_date ) {
								$avail_date = ! empty( $room['avail_date'] ) ? json_decode( $room['avail_date'], true ) : [];
							}
							$order_ids          = ! empty( $room['order_id'] ) ? $room['order_id'] : '';
							$num_room_available = ! empty( $room['num-room'] ) ? $room['num-room'] : '1';
							$reduce_num_room    = ! empty( $room['reduce_num_room'] ) ? $room['reduce_num_room'] : false;
							$multi_by_date_ck   = ! empty( $room['price_multi_day'] ) ? ! empty( $room['price_multi_day'] ) : false;
							$number_orders      = '0';

							// Check availability by date option
							$tfperiod = new \DatePeriod(
								new \DateTime( $from . ' 00:00' ),
								new \DateInterval( 'P1D' ),
								(new \DateTime( $to . ' 23:59' ))
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
							
							if ( ! empty( $order_ids ) && $reduce_num_room == true ) {

								//Get backend available date range as an array
								if ( $avil_by_date ) {
									$order_date_ranges   = array();
									$backend_date_ranges = array();
									foreach ( $avail_date as $single_date_range ) {
										array_push( $backend_date_ranges, array( strtotime( $single_date_range["availability"]["from"] ), strtotime( $single_date_range["availability"]["to"] ) ) );
									}
								}

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
											'query' => " AND ostatus = 'completed' AND order_id = ".$order_id
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
								$number_orders = max($room_bookings_per_day);
		
								# Calculate available rooms
								$num_room_available = $num_room_available - $number_orders;
								$num_room_available = max($num_room_available, 0);
							}

							$response['room_data'] = $room;
							$response['rooms']     = $num_room_available;
						}
					}
				}
			}

			return rest_ensure_response( $response );
		}

		/*
		 * Hotel add booking
		 */
		public function tf_hotel_add_booking( $request ) {

			$room_data  = $this->tf_get_room_data( intval( $request['tf_available_hotels'] ), $request['tf_available_rooms'] );
			$room_price = $this->tf_get_room_total_price( intval( $request['tf_available_hotels'] ), $room_data, $request['tf_hotel_date']['from'], $request['tf_hotel_date']['to'], (int) $request['tf_hotel_rooms_number'], (int) $request['tf_hotel_adults_number'], (int) $request['tf_hotel_children_number'], $request['tf_hotel_service_type'] );

			$billing_details  = array(
				'billing_first_name' => $request['tf_customer_first_name'],
				'billing_last_name'  => $request['tf_customer_last_name'],
				'billing_company'    => '',
				'billing_address_1'  => $request['tf_customer_address'],
				'billing_address_2'  => $request['tf_customer_address_2'],
				'billing_city'       => $request['tf_customer_city'],
				'billing_state'      => $request['tf_customer_state'],
				'billing_postcode'   => $request['tf_customer_zip'],
				'billing_country'    => $request['tf_customer_country'],
				'billing_email'      => $request['tf_customer_email'],
				'billing_phone'      => $request['tf_customer_phone'],
			);
			$shipping_details = array(
				'shipping_first_name' => $request['tf_customer_first_name'],
				'shipping_last_name'  => $request['tf_customer_last_name'],
				'shipping_company'    => '',
				'shipping_address_1'  => $request['tf_customer_address'],
				'shipping_address_2'  => $request['tf_customer_address_2'],
				'shipping_city'       => $request['tf_customer_city'],
				'shipping_state'      => $request['tf_customer_state'],
				'shipping_postcode'   => $request['tf_customer_zip'],
				'shipping_country'    => $request['tf_customer_country'],
				'shipping_phone'      => $request['tf_customer_phone'],
			);
			$order_details    = [
				'order_by'             => $request['tf_hotel_booked_by'],
				'room'                 => $request['tf_hotel_rooms_number'],
				'check_in'             => $request['tf_hotel_date']['from'],
				'check_out'            => $request['tf_hotel_date']['to'],
				'room_name'            => !empty( $room_data['title'] ) ? $room_data['title'] : '',
				'adult'                => $request['tf_hotel_adults_number'],
				'child'                => $request['tf_hotel_children_number'],
				'children_ages'        => '',
				'airport_service_type' => $request['tf_hotel_service_type'],
				'airport_service_fee'  => $room_price['air_service_info'],
				'total_price'          => $room_price['price_total'],
				'due_price'            => '',
			];

			$order_data = array(
				'post_id'          => intval( $request['tf_available_hotels'] ),
				'post_type'        => 'hotel',
				'room_number'      => intval( $request['tf_hotel_rooms_number'] ),
				'check_in'         => $request['tf_hotel_date']['from'],
				'check_out'        => $request['tf_hotel_date']['to'],
				'billing_details'  => $billing_details,
				'shipping_details' => $shipping_details,
				'order_details'    => $order_details,
				'payment_method'   => 'offline',
				'status'           => 'processing',
				'order_date'       => date( 'Y-m-d H:i:s' ),
			);

			$order_id = Helper::tf_set_order( $order_data );
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $order_id ) ) {
				do_action( 'tf_offline_payment_booking_confirmation', $order_id, $order_data );
			}

			$response['success'] = true;
			$response['message'] = esc_html__( 'Your booking has been successfully submitted.', 'tourfic' );

			return rest_ensure_response( $response );
		}

		/*
		 * Room data from room and hotel id
		 * @since 2.9.26
		 */
		public function tf_get_room_data( $hotel_id, $room_id ) {

			if ( $hotel_id && $room_id ) {
				$rooms = get_hotel_rooms($hotel_id);

				if ( ! empty( $rooms ) ) {
					foreach ( $rooms as $_room ) {
						$room = get_post_meta($_room->ID, 'tf_room_opt', true);
						if ( $room['unique_id'] == $room_id ) {
							return $room;
						}
					}
				}
			}

		}

		/*
		 * Calculate room total price
		 * @since 2.9.26
		 */
		public function tf_get_room_total_price( $hotel_id, $room_data, $check_in, $check_out, $room_selected, $adult, $child, $service_type ) {
			$meta            = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
			$airport_service = $meta['airport_service'] ?? null;
			$avail_by_date   = ! empty( $room_data['avil_by_date'] ) ? $room_data['avil_by_date'] : '';
			$air_service_price = 0;
			$air_service_info = '';
			if ( $avail_by_date ) {
				$avail_date = ! empty( $room_data['avail_date'] ) ? json_decode( $room_data['avail_date'], true ) : [];
			}
			$pricing_by      = $room_data['pricing-by'];
			$price_multi_day = ! empty( $room_data['price_multi_day'] ) ? $room_data['price_multi_day'] : false;

			# Calculate night number
			if ( $check_in && $check_out ) {
				$check_in_stt   = strtotime( $check_in . ' +1 day' );
				$check_out_stt  = strtotime( $check_out );
				$day_difference = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
			}

			/**
			 * Calculate Pricing
			 */
			if ( $avail_by_date && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

				// Check availability by date option
				$period = new DatePeriod(
					new DateTime( $check_in . ' 00:00' ),
					new DateInterval( 'P1D' ),
					new DateTime( $check_out . ' 00:00' )
				);

				$total_price = 0;
				foreach ( $period as $date ) {

					$available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
						$date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
						$date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

						return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
					} ) );

					if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {
						$room_price  = ! empty( $available_rooms[0]['price'] ) ? (float) $available_rooms[0]['price'] : (float) $room_data['price'];
						$adult_price = ! empty( $available_rooms ) ? (float) $available_rooms[0]['adult_price'] : (float) $room_data['adult_price'];
						$child_price = ! empty( $available_rooms ) ? (float) $available_rooms[0]['child_price'] : (float) $room_data['child_price'];
						$total_price += $pricing_by == '1' ? $room_price : ( ( $adult_price * $adult ) + ( $child_price * $child ) );
					};

				}

				$price_total = (float) $total_price * (int) $room_selected;
			} else {

				if ( $pricing_by == '1' ) {
					$total_price = $room_data['price'];
				} elseif ( $pricing_by == '2' ) {
					$adult_price = $room_data['adult_price'];
					$adult_price = (float) $adult_price * (int) $adult;
					$child_price = $room_data['child_price'];
					$child_price = (float) $child_price * (int) $child;
					$total_price = $adult_price + $child_price;
				}

				# Multiply pricing by night number
				if ( ! empty( $day_difference ) && $price_multi_day == true ) {
					$price_total = $total_price * $room_selected * $day_difference;
				} else {
					$price_total = $total_price * $room_selected;
				}
			}

			# Airport Service Fee
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $airport_service ) && $airport_service == 1 ) {
				if ( "pickup" == $service_type ) {
					$airport_pickup_price = ! empty( $meta['airport_pickup_price'] ) ? $meta['airport_pickup_price'] : '';
					if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
						$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
							return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
						}, $airport_pickup_price );
						$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
					}
					$price_type = $airport_pickup_price['airport_pickup_price_type'];
					if ( "per_person" == $price_type ) {
						$service_adult_fee           = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
						$service_child_fee           = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
						$airport_service_price_total = ( $adult * $service_adult_fee ) + ( $child * $service_child_fee );

						$air_service_price = $airport_service_price_total;
						$price_total       += $airport_service_price_total;
						if ( $child != 0 ) {

							$air_service_info = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								$child,
								strip_tags( wc_price( $service_child_fee ) ),
								strip_tags( wc_price( $airport_service_price_total ) )
							);

						} else {
							$air_service_info = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								strip_tags( wc_price( $airport_service_price_total ) )
							);

						}
					}
					if ( "fixed" == $price_type ) {
						$airport_service_price_total = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
						$air_service_price           = $airport_service_price_total;
						$price_total                 += $airport_service_price_total;
						$air_service_info            = sprintf( esc_html__( '( Fixed ) = %1$s', 'tourfic' ),
							strip_tags( wc_price( $airport_service_price_total ) )
						);
					}
					if ( "free" == $price_type ) {
						$air_service_price = 0;
						$price_total       += 0;
						$air_service_info  = strip_tags( wc_price( 0 ) );
					}
				}
				if ( "dropoff" == $service_type ) {
					$airport_pickup_price = ! empty( $meta['airport_dropoff_price'] ) ? $meta['airport_dropoff_price'] : '';
					if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
						$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
							return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
						}, $airport_pickup_price );
						$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
					}
					$price_type = $airport_pickup_price['airport_pickup_price_type'];
					if ( "per_person" == $price_type ) {
						$service_adult_fee           = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
						$service_child_fee           = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
						$airport_service_price_total = ( $adult * $service_adult_fee ) + ( $child * $service_child_fee );

						$air_service_price = $airport_service_price_total;
						$price_total       += $airport_service_price_total;
						if ( $child != 0 ) {
							$air_service_info = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								$child,
								strip_tags( wc_price( $service_child_fee ) ),
								strip_tags( wc_price( $airport_service_price_total ) )
							);

						} else {
							$air_service_info = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								strip_tags( wc_price( $airport_service_price_total ) )
							);

						}
					}
					if ( "fixed" == $price_type ) {
						$airport_service_price_total = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
						$air_service_price           = $airport_service_price_total;
						$price_total                 += $airport_service_price_total;
						$air_service_info            = sprintf( esc_html__( '( Fixed ) = %1$s', 'tourfic' ),
							strip_tags( wc_price( $airport_service_price_total ) )
						);
					}
					if ( "free" == $price_type ) {
						$air_service_price = 0;
						$price_total       += 0;
						$air_service_info  = strip_tags( wc_price( 0 ) );
					}
				}
				if ( "both" == $service_type ) {
					$airport_pickup_price = ! empty( $meta['airport_pickup_dropoff_price'] ) ? $meta['airport_pickup_dropoff_price'] : '';
					if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
						$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
							return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
						}, $airport_pickup_price );
						$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
					}
					$price_type = $airport_pickup_price['airport_pickup_price_type'];
					if ( "per_person" == $price_type ) {
						$service_adult_fee           = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
						$service_child_fee           = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
						$airport_service_price_total = ( $adult * $service_adult_fee ) + ( $child * $service_child_fee );

						$air_service_price = $airport_service_price_total;
						$price_total       += $airport_service_price_total;
						if ( $child != 0 ) {

							$air_service_info = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								$child,
								strip_tags( wc_price( $service_child_fee ) ),
								strip_tags( wc_price( $airport_service_price_total ) )
							);

						} else {
							$air_service_info = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								strip_tags( wc_price( $airport_service_price_total ) )
							);

						}
					}
					if ( "fixed" == $price_type ) {
						$airport_service_price_total = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
						$air_service_price           = $airport_service_price_total;
						$price_total                 += $airport_service_price_total;
						$air_service_info            = sprintf( esc_html__( '( Fixed ) = %1$s', 'tourfic' ),
							strip_tags( wc_price( $airport_service_price_total ) )
						);
					}
					if ( "free" == $price_type ) {
						$air_service_price = 0;
						$price_total       += 0;
						$air_service_info  = strip_tags( wc_price( 0 ) );
					}
				}
			}

			return array(
				'price_total'       => $price_total,
				'air_service_price' => $air_service_price,
				'air_service_info'  => $air_service_info,
			);
		}
	}
}

TF_Hotel_Backend_Booking_Rest_API::get_instance();
