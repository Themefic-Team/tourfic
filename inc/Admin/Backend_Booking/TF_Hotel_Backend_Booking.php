<?php

namespace Tourfic\Admin\Backend_Booking;

// do not allow direct access
if ( ! defined( 'ABSPATH' ) ) exit;

use \Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Room;
use \Tourfic\Core\TF_Backend_Booking;
use \Tourfic\Classes\Hotel\Hotel;

class TF_Hotel_Backend_Booking extends TF_Backend_Booking {

	use \Tourfic\Traits\Singleton;

	protected array $args = array(
		'name' => 'hotel',
        'prefix' => 'tf-hotel',
        'post_type' => 'tf_hotel',
        'caps' => 'edit_tf_hotels'
	);

	function set_settings_fields() {
		$this->settings = array(
			'tf_booking_fields'	=> array(
				'title'  => esc_html__( 'Booking Information', 'tourfic' ),
				'fields' => array(
					array(
						'id'      => 'tf_hotel_date',
						'label'   => esc_html__( 'Date', 'tourfic' ),
						'type'    => 'date',
						'format'  => 'Y/m/d',
						'range'   => true,
						'minDate' => 'today',
					),
					array(
						'id'          => 'tf_available_hotels',
						'label'       => esc_html__( 'Available Hotels', 'tourfic' ),
						'type'        => 'select2',
						'options'     => 'posts',
						'query_args'  => array(
							'post_type'      => 'tf_hotel',
							'posts_per_page' => - 1,
							'post_status'    => 'publish',
						),
						'field_width' => 50,
					),
					array(
						'id'          => 'tf_available_rooms',
						'label'       => esc_html__( 'Available Rooms', 'tourfic' ),
						'type'        => 'select2',
						'options'     => 'posts',
						'placeholder' => esc_html__( 'Please choose the hotel first', 'tourfic' ),
						'attributes'  => array(
							'disabled' => 'disabled',
						),
						'field_width' => 50,
					),
					array(
						'id'          => 'tf_hotel_rooms_number',
						'label'       => esc_html__( 'Number of Rooms', 'tourfic' ),
						'type'        => 'select',
						'options'     => array(
							'1' => esc_html__( '1 Room', 'tourfic' ),
							'2' => esc_html__( '2 Rooms', 'tourfic' ),
							'3' => esc_html__( '3 Rooms', 'tourfic' ),
							'4' => esc_html__( '4 Rooms', 'tourfic' ),
							'5' => esc_html__( '5 Rooms', 'tourfic' ),
						),
						'field_width' => 50,
					),
					array(
						'id'          => 'tf_hotel_adults_number',
						'label'       => esc_html__( 'Adults', 'tourfic' ),
						'type'        => 'number',
						'attributes'  => array(
							'min' => '0',
						),
						'field_width' => 50,
					),
					array(
						'id'          => 'tf_hotel_children_number',
						'label'       => esc_html__( 'Children', 'tourfic' ),
						'type'        => 'number',
						'attributes'  => array(
							'min' => '0',
						),
						'field_width' => 50,
					),
					array(
						'id'    => 'tf-pro-notice',
						'type'  => 'notice',
						'class' => 'tf-pro-notice',
						'notice' => 'info',
						'icon' => 'ri-information-fill',
						'content' => wp_kses_post(__( 'Do you need to add hotel airport services such as pickup, dropoff, or both? Our Pro plan includes the <b>hotel service</b> feature, allowing you to easily add these services with pricing options <b>per person</b>, <b>fixed</b>, or <b>complimentary</b>. Enhance your guest experience by integrating these convenient services seamlessly into your offerings. <a href="https://tourfic.com/" target="_blank">Upgrade to our pro package today to take advantage of this fantastic option!</a>', 'tourfic') ),
					),
				),
			),
		);

		$hotel_services_setting = array(
			'id'          => 'tf_hotel_service_type',
			'label'       => esc_html__( 'Service Type', 'tourfic' ),
			'type'        => 'select',
			'options'     => array(
				'pickup'  => esc_html__( 'Pickup Service', 'tourfic' ),
				'dropoff' => esc_html__( 'Drop-off Service', 'tourfic' ),
				'both'    => esc_html__( 'Pickup & Drop-off Service', 'tourfic' ),
			),
			'placeholder' => esc_html__( 'Select Service Type', 'tourfic' ),
			'field_width' => 50,
		);

		if( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			array_pop( $this->settings['tf_booking_fields']['fields']);
			array_push( $this->settings['tf_booking_fields']['fields'], $hotel_services_setting );
		}

		$this->set_settings( $this->settings );
	}

	public function __construct(){

		$this->set_settings_fields();

        parent::__construct($this->args);

		// all actions
		add_action( 'wp_ajax_tf_check_available_hotel', array( $this, 'tf_check_available_hotel' ) );
		add_action( 'wp_ajax_tf_check_available_room', array( $this, 'tf_check_available_room' ) );
		add_action( 'wp_ajax_tf_update_room_fields', array( $this, 'tf_update_room_fields' ) );
		add_action( 'wp_ajax_tf_backend_hotel_booking', array( $this, 'backend_booking_callback' ) );
	}

	public function tf_check_available_hotel() {
		// Add nonce for security and authentication.
		check_ajax_referer('updates', '_nonce');

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$from = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
		$to   = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';

		$loop = new \WP_Query( array(
			'post_type'      => 'tf_hotel',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		) );

		$period = '';
		if ( ! empty( $from ) && ! empty( $to ) ) {
			$period = new \DatePeriod(
				new \DateTime( $from ),
				new \DateInterval( 'P1D' ),
				new \DateTime( ! empty( $to ) ? $to : '23:59:59' )
			);
		}

		if ( $loop->have_posts() ) {
			$not_found = [];
			while ( $loop->have_posts() ) {
				$loop->the_post();
				Hotel::tf_filter_hotel_by_date( $period, $not_found, array( 1, 1, 1, '' ) );
			}

			$tf_total_filters = [];
			foreach ( $not_found as $not ) {
				if ( $not['found'] != 1 ) {
					$tf_total_filters[ $not['post_id'] ] = get_the_title( $not['post_id'] );
				}
			}
		}
		wp_reset_postdata();

		wp_send_json_success( array(
			'hotels' => $tf_total_filters
		) );
	}

	public function tf_check_available_room() {
		// Add nonce for security and authentication.
		check_ajax_referer('updates', '_nonce');

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$hotel_id = isset( $_POST['hotel_id'] ) ? sanitize_text_field( $_POST['hotel_id'] ) : '';
		$from     = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
		$to       = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';

		// Custom avail
		if ( empty( $to ) ) {
			$to = gmdate( 'Y/m/d', strtotime( $from . " + 1 day" ) );
		}
		$from = gmdate( 'Y/m/d', strtotime( $from . ' +1 day' ) );

		/**
		 * Backend data
		 */
		$meta  = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
		$rooms = Room::get_hotel_rooms( $hotel_id);

		$room_array = array();

		if ( ! empty( $rooms ) ) {
			foreach ( $rooms as $_room ) {
				$room = get_post_meta($_room->ID, 'tf_room_opt', true);
				// Check if room is enabled
				$enable = ! empty( $room['enable'] ) && boolval( $room['enable'] );

				if ( $enable ) {
					// Check availability by date option
					$period = new \DatePeriod(
						new \DateTime( $from . ' 00:00' ),
						new \DateInterval( 'P1D' ),
						new \DateTime( $to . ' 23:59' )
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
						$avail_date = ! empty( $room['avail_date'] ) ? json_decode($room['avail_date'], true) : [];
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

		wp_send_json_success( array(
			'rooms'    => $room_array,
			'services' => $hotel_services,
		) );
	}

	public function tf_update_room_fields() {
		// Add nonce for security and authentication.
		check_ajax_referer('updates', '_nonce');

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}
		
		$response = array(
			'adults'   => 0,
			'children' => 0,
		);

		$hotel_id = isset( $_POST['hotel_id'] ) ? $_POST['hotel_id'] : '';
		$room_id  = isset( $_POST['room_id'] ) ? $_POST['room_id'] : '';

		if ( ! empty( $hotel_id ) && ! empty( $room_id ) ) {
			$rooms = Room::get_hotel_rooms( $hotel_id);

			if ( ! empty( $rooms ) ) {
				foreach ( $rooms as $_room ) {
					$room = get_post_meta($_room->ID, 'tf_room_opt', true);
					if ( $room['unique_id'] == $room_id ) {

						$avil_by_date = ! empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : false;
						if ( $avil_by_date ) {
							$avail_date = ! empty( $room['avail_date'] ) ? json_decode($room['avail_date'], true) : [];
						}
						$order_ids          = ! empty( $room['order_id'] ) ? $room['order_id'] : '';
						$num_room_available = ! empty( $room['num-room'] ) ? $room['num-room'] : '1';
						$reduce_num_room    = ! empty( $room['reduce_num_room'] ) ? $room['reduce_num_room'] : false;
						$number_orders      = '0';

						if ( ! empty( $order_ids ) && $reduce_num_room == true ) {

							//Get backend available date range as an array
							if ( $avil_by_date ) {
								$order_date_ranges   = array();
								$backend_date_ranges = array();
								foreach ( $avail_date as $single_date_range ) {
									array_push( $backend_date_ranges, array( strtotime( $single_date_range["check_in"] ), strtotime( $single_date_range["check_out"] ) ) );
								}
							}

							$order_ids = explode( ',', $order_ids );

							foreach ( $order_ids as $order_id ) {
								# Get Only the completed orders
								$tf_orders_select     = array(
									'select' => "post_id,order_details",
									'post_type' => 'hotel',
									'query'  => " AND ostatus = 'completed' AND order_id = " . $order_id
								);
								$tf_hotel_book_orders = Helper::tourfic_order_table_data( $tf_orders_select );

								# Get and Loop Over Order Items
								foreach ( $tf_hotel_book_orders as $item ) {
									$order_details = json_decode( $item['order_details'] );
									/**
									 * Order item data
									 */
									$ordered_number_of_room = ! empty( $order_details->room ) ? $order_details->room : 0;

									if ( $avil_by_date ) {

										$order_check_in_date  = strtotime( $order_details->check_in );
										$order_check_out_date = strtotime( $order_details->check_out );

										$tf_order_check_in_date  = $order_details->check_in;
										$tf_order_check_out_date = $order_details->check_out;
										if ( ! empty( $avail_durationdate ) && ( in_array( $tf_order_check_out_date, $avail_durationdate ) || in_array( $tf_order_check_in_date, $avail_durationdate ) ) ) {
											# Total number of room booked
											$number_orders = $number_orders + $ordered_number_of_room;
										}
										array_push( $order_date_ranges, array( $order_check_in_date, $order_check_out_date ) );

									} else {
										$order_check_in_date  = $order_details->check_in;
										$order_check_out_date = $order_details->check_out;
										if ( ! empty( $avail_durationdate ) && ( in_array( $order_check_out_date, $avail_durationdate ) || in_array( $order_check_in_date, $avail_durationdate ) ) ) {
											# Total number of room booked
											$number_orders = $number_orders + $ordered_number_of_room;
										}
									}
								}
							}
							//Calculate available room number after order
							$num_room_available = $num_room_available - $number_orders; // Calculate
							$num_room_available = max( $num_room_available, 0 ); // If negetive value make that 0
						}

						$response['adults']   = $room['adult'];
						$response['children'] = $room['child'];
						$response['rooms']    = $num_room_available;
					}
				}
			}

			wp_send_json_success( $response );

		} else {

			wp_send_json_error( array(
				'message' => esc_html__( 'Something went wrong!', 'tourfic' ),
			) );

		}
	}

    function backend_booking_callback(){
		// Add nonce for security and authentication.
		check_ajax_referer('tf_backend_booking_nonce_action', 'tf_backend_booking_nonce');

		$response = array(
			'success' => false,
		);

		$field = [];
		foreach ( $_POST as $key => $value ) {
			if ( $key === 'tf_hotel_date' ) {
				$field[ $key ]['from'] = sanitize_text_field( $value['from'] );
				$field[ $key ]['to']   = sanitize_text_field( $value['to'] );
			} else {
				$field[ $key ] = $value;
			}
		}

		$required_fields = array(
			'tf_hotel_booked_by',
			'tf_customer_first_name',
			'tf_customer_email',
			'tf_customer_phone',
			'tf_customer_country',
			'tf_customer_address',
			'tf_customer_city',
			'tf_customer_state',
			'tf_customer_zip',
			'tf_hotel_date',
			'tf_available_hotels',
			'tf_available_rooms',
			'tf_hotel_rooms_number',
			'tf_hotel_adults_number'
		);

		foreach ( $required_fields as $required_field ) {
			if ( $required_field === 'tf_hotel_date' ) {
				if ( empty( $field[ $required_field ]['from'] ) ) {
					$response['fieldErrors'][ $required_field . '[from]_error' ] = esc_html__( 'The field is required', 'tourfic' );
				}
				if ( empty( $field[ $required_field ]['to'] ) ) {
					$response['fieldErrors'][ $required_field . '[to]_error' ] = esc_html__( 'The field is required', 'tourfic' );
				}
			} else {
				if ( empty( $field[ $required_field ] ) ) {
					$response['fieldErrors'][ $required_field . '_error' ] = esc_html__( 'The field is required', 'tourfic' );
				}
			}
		}

		$room_data = $this->tf_get_room_data( intval( $field['tf_available_hotels'] ), $field['tf_available_rooms'] );

		if ( $field['tf_hotel_rooms_number'] * $room_data['adult'] < $field['tf_hotel_adults_number'] ) {
			/* translators: %s maximum adult number */
			$response['fieldErrors']['tf_hotel_adults_number_error'] = sprintf(esc_html__( "You can't book more than %s adults", 'tourfic' ), $field['tf_hotel_rooms_number'] * $room_data['adult']);
		}
		if ( $field['tf_hotel_rooms_number'] * $room_data['child'] < $field['tf_hotel_children_number'] ) {
			/* translators: %s maximum child number */
			$response['fieldErrors']['tf_hotel_children_number_error'] = sprintf(esc_html__( "You can't book more than %s children", 'tourfic' ), $field['tf_hotel_rooms_number'] * $room_data['child']);
		}

		if ( ! array_key_exists("fieldErrors", $response) || ! $response['fieldErrors'] ) {
			$room_price       = $this->tf_get_room_total_price( intval( $field['tf_available_hotels'] ), $room_data, $field['tf_hotel_date']['from'], $field['tf_hotel_date']['to'], intval( $field['tf_hotel_rooms_number'] ), intval( $field['tf_hotel_adults_number'] ), intval( $field['tf_hotel_children_number'] ), $field['tf_hotel_service_type'] );
			$billing_details  = array(
				'billing_first_name' => $field['tf_customer_first_name'],
				'billing_last_name'  => $field['tf_customer_last_name'],
				'billing_company'    => '',
				'billing_address_1'  => $field['tf_customer_address'],
				'billing_address_2'  => $field['tf_customer_address_2'],
				'billing_city'       => $field['tf_customer_city'],
				'billing_state'      => $field['tf_customer_state'],
				'billing_postcode'   => $field['tf_customer_zip'],
				'billing_country'    => $field['tf_customer_country'],
				'billing_email'      => $field['tf_customer_email'],
				'billing_phone'      => $field['tf_customer_phone'],
			);
			$shipping_details = array(
				'shipping_first_name' => $field['tf_customer_first_name'],
				'shipping_last_name'  => $field['tf_customer_last_name'],
				'shipping_company'    => '',
				'shipping_address_1'  => $field['tf_customer_address'],
				'shipping_address_2'  => $field['tf_customer_address_2'],
				'shipping_city'       => $field['tf_customer_city'],
				'shipping_state'      => $field['tf_customer_state'],
				'shipping_postcode'   => $field['tf_customer_zip'],
				'shipping_country'    => $field['tf_customer_country'],
				'shipping_phone'      => $field['tf_customer_phone'],
				'tf_email'      => $field['tf_customer_email'],
			);
			$order_details    = [
				'order_by'             => $field['tf_hotel_booked_by'],
				'room'                 => $field['tf_hotel_rooms_number'],
				'check_in'             => $field['tf_hotel_date']['from'],
				'check_out'            => $field['tf_hotel_date']['to'],
				'room_name'            => $room_data['title'],
				'adult'                => $field['tf_hotel_adults_number'],
				'child'                => $field['tf_hotel_children_number'],
				'children_ages'        => '',
				'airport_service_type' => $field['tf_hotel_service_type'],
				'airport_service_fee'  => $room_price['air_service_info'],
				'total_price'          => $room_price['price_total'],
				'due_price'            => '',
			];

			$order_data = array(
				'post_id'          => intval( $field['tf_available_hotels'] ),
				'post_type'        => 'hotel',
				'room_number'      => intval( $field['tf_hotel_rooms_number'] ),
				'check_in'         => $field['tf_hotel_date']['from'],
				'check_out'        => $field['tf_hotel_date']['to'],
				'billing_details'  => $billing_details,
				'shipping_details' => $shipping_details,
				'order_details'    => $order_details,
				'payment_method'   => "offline",
				'status'           => 'processing',
				'order_date'       => gmdate( 'Y-m-d H:i:s' ),
			);

			Helper::tf_set_order( $order_data );

			$response['success'] = true;
			$response['message'] = esc_html__( 'Your booking has been successfully submitted.', 'tourfic' );
		}
		

		echo wp_json_encode( $response );
		die();
	}

	public function tf_get_room_data( $hotel_id, $room_id ) {

		if ( $hotel_id && $room_id ) {
			$meta = get_post_meta( $hotel_id, 'tf_hotels_opt', true );

			$rooms = Room::get_hotel_rooms( $hotel_id);

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

	public function tf_get_room_total_price( $hotel_id, $room_data, $check_in, $check_out, $room_selected, $adult, $child, $service_type ) {
		$meta            = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
		$airport_service = $meta['airport_service'] ?? null;
		$avail_by_date   = ! empty( $room_data['avil_by_date'] ) ? $room_data['avil_by_date'] : '';
		$tf_room_discount_type = !empty($room_data['discount_hotel_type']) ? $room_data['discount_hotel_type'] : 'none';
		$tf_room_discount_amount = $tf_room_discount_type != 'none' ? ( !empty($room_data['discount_hotel_price']) ? $room_data['discount_hotel_price'] : 0 ) : 0;
		if ( $avail_by_date ) {
			$avail_date = ! empty( $room['avail_date'] ) ? json_decode($room['avail_date'], true) : [];
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
					$room_price  = ! empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $room_data['price'];
					$adult_price = ! empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $room_data['adult_price'];
					$child_price = ! empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $room_data['child_price'];
					$total_price += $pricing_by == '1' ? $room_price : ( ( $adult_price * $adult ) + ( $child_price * $child ) );
				};

			}

			$price_total = $total_price * $room_selected;
		} else {

			if ( $pricing_by == '1' ) {
				$total_price = $room_data['price'];
			} elseif ( $pricing_by == '2' ) {
				$adult_price = $room_data['adult_price'];
				$adult_price = $adult_price * $adult;
				$child_price = $room_data['child_price'];
				$child_price = $child_price * $child;
				$total_price = $adult_price + $child_price;
			}

			# Multiply pricing by night number
			if ( ! empty( $day_difference ) && $price_multi_day == true ) {
				$price_total = $total_price * $room_selected * $day_difference;
			} else {
				$price_total = (int) $total_price * (int) $room_selected;
			}
		}

		// Discount Calculation
		if($tf_room_discount_type == "percent") {
			$price_total = !empty($price_total) ? floatval( preg_replace( '/[^\d.]/', '',number_format( (int) $price_total - ( ( (int) $price_total / 100 ) * (int) $tf_room_discount_amount ), 2 ) ) ) : 0;
		}elseif ( $tf_room_discount_type == 'fixed' ) {
			$price_total = !empty( $price_total ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price_total - (int) $tf_room_discount_amount ), 2 ) ) : 0;
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

						/* translators: %1$s adult number, %2$s adult price, %3$s child number, %4$s child price, %5$s total price */
						$air_service_info = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
							$adult,
							wp_strip_all_tags( wc_price( $service_adult_fee ) ),
							$child,
							wp_strip_all_tags( wc_price( $service_child_fee ) ),
							wp_strip_all_tags( wc_price( $airport_service_price_total ) )
						);

					} else {
						/* translators: %1$s adult number, %2$s adult price, %3$s total price */
						$air_service_info = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
							$adult,
							wp_strip_all_tags( wc_price( $service_adult_fee ) ),
							wp_strip_all_tags( wc_price( $airport_service_price_total ) )
						);

					}
				}
				if ( "fixed" == $price_type ) {
					$airport_service_price_total = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$air_service_price           = $airport_service_price_total;
					$price_total                 += $airport_service_price_total;
					/* translators: %1$s total price */
					$air_service_info            = sprintf( esc_html__( '( Fixed ) = %1$s', 'tourfic' ),
						wp_strip_all_tags( wc_price( $airport_service_price_total ) )
					);
				}
				if ( "free" == $price_type ) {
					$air_service_price = 0;
					$price_total       += 0;
					$air_service_info  = wp_strip_all_tags( wc_price( 0 ) );
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
						/* translators: %1$s adult number, %2$s adult price, %3$s child number, %4$s child price, %5$s total price */
						$air_service_info = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
							$adult,
							wp_strip_all_tags( wc_price( $service_adult_fee ) ),
							$child,
							wp_strip_all_tags( wc_price( $service_child_fee ) ),
							wp_strip_all_tags( wc_price( $airport_service_price_total ) )
						);

					} else {
						/* translators: %1$s adult number, %2$s adult price, %3$s total price */
						$air_service_info = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
							$adult,
							wp_strip_all_tags( wc_price( $service_adult_fee ) ),
							wp_strip_all_tags( wc_price( $airport_service_price_total ) )
						);

					}
				}
				if ( "fixed" == $price_type ) {
					$airport_service_price_total = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$air_service_price           = $airport_service_price_total;
					$price_total                 += $airport_service_price_total;
					/* translators: %1$s total price */
					$air_service_info            = sprintf( esc_html__( '( Fixed ) = %1$s', 'tourfic' ),
						wp_strip_all_tags( wc_price( $airport_service_price_total ) )
					);
				}
				if ( "free" == $price_type ) {
					$air_service_price = 0;
					$price_total       += 0;
					$air_service_info  = wp_strip_all_tags( wc_price( 0 ) );
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
						/* translators: %1$s adult number, %2$s adult price, %3$s child number, %4$s child price, %5$s total price */
						$air_service_info = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
							$adult,
							wp_strip_all_tags( wc_price( $service_adult_fee ) ),
							$child,
							wp_strip_all_tags( wc_price( $service_child_fee ) ),
							wp_strip_all_tags( wc_price( $airport_service_price_total ) )
						);

					} else {
						/* translators: %1$s adult number, %2$s adult price, %3$s total price */
						$air_service_info = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
							$adult,
							wp_strip_all_tags( wc_price( $service_adult_fee ) ),
							wp_strip_all_tags( wc_price( $airport_service_price_total ) )
						);

					}
				}
				if ( "fixed" == $price_type ) {
					$airport_service_price_total = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$air_service_price           = $airport_service_price_total;
					$price_total                 += $airport_service_price_total;
					/* translators: %1$s total price */
					$air_service_info            = sprintf( esc_html__( '( Fixed ) = %1$s', 'tourfic' ),
						wp_strip_all_tags( wc_price( $airport_service_price_total ) )
					);
				}
				if ( "free" == $price_type ) {
					$air_service_price = 0;
					$price_total       += 0;
					$air_service_info  = wp_strip_all_tags( wc_price( 0 ) );
				}
			}
		}

		return array(
			'price_total'       => $price_total,
			'air_service_price' => $air_service_price,
			'air_service_info'  => $air_service_info,
		);
	}

	function check_avaibility_callback(){}
    function check_price_callback(){}
}