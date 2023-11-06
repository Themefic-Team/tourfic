<?php
defined( 'ABSPATH' ) || exit;
/**
 * TF Hotel Backend Booking
 * @since 2.9.26
 * @author Foysal
 */
if ( ! class_exists( 'TF_Hotel_Backend_Booking' ) ) {
	class TF_Hotel_Backend_Booking {

		private static $instance = null;

		/**
		 * Singleton instance
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( self::$instance == null ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function __construct() {
			add_action( 'tf_before_hotel_booking_details', array( $this, 'tf_hotel_backend_booking_button' ) );
			add_action( 'admin_menu', array( $this, 'tf_backend_booking_menu' ) );
			add_action( 'wp_ajax_tf_check_available_hotel', array( $this, 'tf_check_available_hotel' ) );
			add_action( 'wp_ajax_tf_check_available_room', array( $this, 'tf_check_available_room' ) );
			add_action( 'wp_ajax_tf_update_room_fields', array( $this, 'tf_update_room_fields' ) );
			add_action( 'wp_ajax_tf_backend_hotel_booking', array( $this, 'tf_backend_hotel_booking' ) );
		}

		function tf_hotel_backend_booking_button() {
			?>
            <a href="<?php echo admin_url( 'edit.php?post_type=tf_hotel&page=tf-hotel-backend-booking' ); ?>" class="button button-primary tf-booking-btn"><?php _e( 'Add New Booking', 'tourfic' ); ?></a>
			<?php
		}

		/**
		 * TF Backend Booking Menu
		 * @since 2.9.26
		 */
		public function tf_backend_booking_menu() {
			add_submenu_page(
				null,
				__( 'Add New Booking', 'tourfic' ),
				__( 'Add New Booking', 'tourfic' ),
				'edit_tf_hotels',
				'tf-hotel-backend-booking',
				array( $this, 'tf_backend_booking_page' ),
			);
		}

		/**
		 * TF Backend Booking Page
		 * @since 2.9.26
		 */
		public function tf_backend_booking_page() {
			echo '<div class="tf-setting-dashboard">';
			tf_dashboard_header()
			?>
            <form method="post" action="" class="tf-backend-hotel-booking" enctype="multipart/form-data">
                <h1><?php _e( 'Add New Hotel Booking', 'tourfic' ); ?></h1>
				<?php
				$tf_backend_booking_form_fields = $this->tf_backend_booking_form_fields();
				foreach ( $tf_backend_booking_form_fields as $id => $tf_backend_booking_form_field ) : ?>
                    <div class="tf-backend-booking-card-wrap">
                        <h3 class="tf-backend-booking-card-title"><?php echo esc_html( $tf_backend_booking_form_field['title'] ); ?></h3>

                        <div class="tf-booking-fields-wrapper">
                            <div class="tf-booking-fields">
								<?php
								if ( ! empty( $tf_backend_booking_form_field['fields'] ) ):
									foreach ( $tf_backend_booking_form_field['fields'] as $field ) :

										$default = isset( $field['default'] ) ? $field['default'] : '';
										$value   = isset( $tf_option_value[ $field['id'] ] ) ? $tf_option_value[ $field['id'] ] : $default;

										$tf_option = new TF_Options();
										$tf_option->field( $field, $value, '' );

									endforeach;
								endif; ?>
                            </div>
                        </div>
                    </div>
				<?php endforeach; ?>
				<?php wp_nonce_field( 'tf_backend_booking_nonce_action', 'tf_backend_booking_nonce' ); ?>

                <!-- Footer -->
                <div class="tf-backend-booking-footer">
                    <button type="submit" class="tf-admin-btn tf-btn-secondary tf-submit-btn" id="tf-backend-hotel-book-btn"><?php _e( 'Book Now', 'tourfic' ); ?></button>
                </div>
            </form>
			<?php
			echo '</div>';
		}

		/**
		 * TF Backend Booking Form Fields
		 * @since 2.9.26
		 */
		public function tf_backend_booking_form_fields() {
			$current_user = wp_get_current_user();
			$fields       = array(
				'tf_booking_customer_fields' => array(
					'title'  => __( 'Customer Information', 'tourfic' ),
					'fields' => array(
						array(
							'id'         => 'tf_hotel_booked_by',
							'label'      => __( 'Booked By', 'tourfic' ),
							'type'       => 'text',
							'default'    => $current_user->display_name ?: $current_user->user_login,
							'attributes' => array(
								'readonly' => 'readonly',
							),
						),
						array(
							'id'          => 'tf_customer_first_name',
							'label'       => __( 'First Name', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer First Name', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_customer_last_name',
							'label'       => __( 'Last Name', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Last Name', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_customer_email',
							'label'       => __( 'Email', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Email', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_customer_phone',
							'label'       => __( 'Phone', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Phone', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_customer_country',
							'label'       => __( 'Country / Region', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Country', 'tourfic' ),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'tf_customer_address',
							'label'       => __( 'Address', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Address', 'tourfic' ),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'tf_customer_address_2',
							'label'       => __( 'Address 2', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Address 2', 'tourfic' ),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'tf_customer_city',
							'label'       => __( 'Town / City', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer City', 'tourfic' ),
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_customer_state',
							'label'       => __( 'State', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer State', 'tourfic' ),
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_customer_zip',
							'label'       => __( 'Postcode / ZIP', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Zip', 'tourfic' ),
							'field_width' => 33,
						),
					),
				),
				'tf_booking_fields'          => array(
					'title'  => __( 'Booking Information', 'tourfic' ),
					'fields' => array(
						array(
							'id'      => 'tf_hotel_date',
							'label'   => __( 'Date', 'tourfic' ),
							'type'    => 'date',
							'format'  => 'Y/m/d',
							'range'   => true,
							'minDate' => 'today',
						),
						array(
							'id'          => 'tf_available_hotels',
							'label'       => __( 'Available Hotels', 'tourfic' ),
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
							'id'          => 'tf_hotel_service_type',
							'label'       => __( 'Service Type', 'tourfic' ),
							'type'        => 'select',
							'options'     => array(
								'pickup'  => __( 'Pickup Service', 'tourfic' ),
								'dropoff' => __( 'Drop-off Service', 'tourfic' ),
								'both'    => __( 'Pickup & Drop-off Service', 'tourfic' ),
							),
							'placeholder' => __( 'Select Service Type', 'tourfic' ),
							'field_width' => 50,
							'is_pro'      => true
						),
						array(
							'id'          => 'tf_available_rooms',
							'label'       => __( 'Available Rooms', 'tourfic' ),
							'type'        => 'select2',
							'options'     => 'posts',
							'attributes'  => array(
								'disabled' => 'disabled',
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_hotel_rooms_number',
							'label'       => __( 'Number of Rooms', 'tourfic' ),
							'type'        => 'select',
							'options'     => array(
								'1' => __( '1 Room', 'tourfic' ),
								'2' => __( '2 Rooms', 'tourfic' ),
								'3' => __( '3 Rooms', 'tourfic' ),
								'4' => __( '4 Rooms', 'tourfic' ),
								'5' => __( '5 Rooms', 'tourfic' ),
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_hotel_adults_number',
							'label'       => __( 'Adults', 'tourfic' ),
							'type'        => 'number',
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_hotel_children_number',
							'label'       => __( 'Children', 'tourfic' ),
							'type'        => 'number',
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
						),
					),
				),
			);

			return $fields;
		}

		/*
		 * Check available hotel room from date to date
		 * @since 2.9.26
		 */
		public function tf_check_available_hotel() {
			$from = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
			$to   = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';

			$loop = new WP_Query( array(
				'post_type'      => 'tf_hotel',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
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

			wp_send_json_success( array(
				'hotels' => $tf_total_filters
			) );
		}

		/*
		 * Check available hotel room by hotel id
		 * @since 2.9.26
		 */
		public function tf_check_available_room() {
			$hotel_id = isset( $_POST['hotel_id'] ) ? sanitize_text_field( $_POST['hotel_id'] ) : '';
			$from     = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
			$to       = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';

			// Custom avail
			if ( empty( $to ) ) {
				$to = date( 'Y/m/d', strtotime( $from . " + 1 day" ) );
			}
			$from = date( 'Y/m/d', strtotime( $from . ' +1 day' ) );

			/**
			 * Backend data
			 */
			$meta  = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
			$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
			if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
				$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $rooms );
				$rooms                = unserialize( $tf_hotel_rooms_value );
			}

			$room_array = array();

			if ( ! empty( $rooms ) ) {
				foreach ( $rooms as $room_id => $room ) {
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
									$room_array[ $room['unique_id'] ] = $room['title'];
								}
							}
						} else {
							$room_array[ $room['unique_id'] ] = $room['title'];
						}
					}
				}
			}

			//hotel service
			$hotel_services      = array(
				'' => __( 'Select Service Type', 'tourfic' )
			);
			$hotel_service_avail = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';
			$hotel_service_type  = ! empty( $meta['airport_service_type'] ) ? $meta['airport_service_type'] : '';
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $hotel_service_avail ) && ! empty( $hotel_service_type ) ) {
				foreach ( $hotel_service_type as $single_service_type ) {
					if ( "pickup" == $single_service_type ) {
						$hotel_services['pickup'] = __( 'Pickup Service', 'tourfic' );
					}
					if ( "dropoff" == $single_service_type ) {
						$hotel_services['dropoff'] = __( 'Drop-off Service', 'tourfic' );
					}
					if ( "both" == $single_service_type ) {
						$hotel_services['both'] = __( 'Pickup & Drop-off Service', 'tourfic' );
					}
				}
			}

			wp_send_json_success( array(
				'rooms'    => $room_array,
				'services' => $hotel_services,
			) );
		}

		/*
		 * Room adults, children, infants fields update on room change
		 * @since 2.9.26
		 */
		public function tf_update_room_fields() {
			$response = array(
				'adults'   => 0,
				'children' => 0,
			);

			$hotel_id = isset( $_POST['hotel_id'] ) ? $_POST['hotel_id'] : '';
			$room_id  = isset( $_POST['room_id'] ) ? $_POST['room_id'] : '';

			if ( ! empty( $hotel_id ) && ! empty( $room_id ) ) {

				$meta  = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
				$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
				if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
					$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $rooms );
					$rooms                = unserialize( $tf_hotel_rooms_value );
				}

				if ( ! empty( $rooms ) ) {
					foreach ( $rooms as $room ) {
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
									$tf_hotel_book_orders = tourfic_order_table_data( $tf_orders_select );

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
					'message' => __( 'Something went wrong!', 'tourfic' ),
				) );

			}
		}

		/*
		 * Booking form submit
		 * @since 2.9.26
		 */
		public function tf_backend_hotel_booking() {
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

			if ( ! isset( $field['tf_backend_booking_nonce'] ) || ! wp_verify_nonce( $field['tf_backend_booking_nonce'], 'tf_backend_booking_nonce_action' ) ) {
				$response['message'] = __( 'Sorry, your nonce did not verify.', 'tourfic' );
			} else {
				foreach ( $required_fields as $required_field ) {
					if ( $required_field === 'tf_hotel_date' ) {
						if ( empty( $field[ $required_field ]['from'] ) ) {
							$response['fieldErrors'][ $required_field . '[from]_error' ] = __( 'The field is required', 'tourfic' );
						}
						if ( empty( $field[ $required_field ]['to'] ) ) {
							$response['fieldErrors'][ $required_field . '[to]_error' ] = __( 'The field is required', 'tourfic' );
						}
					} else {
						if ( empty( $field[ $required_field ] ) ) {
							$response['fieldErrors'][ $required_field . '_error' ] = __( 'The field is required', 'tourfic' );
						}
					}
				}

				$room_data = $this->tf_get_room_data( intval( $field['tf_available_hotels'] ), $field['tf_available_rooms'] );

				if ( $field['tf_hotel_rooms_number'] * $room_data['adult'] < $field['tf_hotel_adults_number'] ) {
					$response['fieldErrors']['tf_hotel_adults_number_error'] = __( "You can't book more than " . $field['tf_hotel_rooms_number'] * $room_data['adult'] . " adults", 'tourfic' );
				}
				if ( $field['tf_hotel_rooms_number'] * $room_data['child'] < $field['tf_hotel_children_number'] ) {
					$response['fieldErrors']['tf_hotel_children_number_error'] = __( "You can't book more than " . $field['tf_hotel_rooms_number'] * $room_data['child'] . " children", 'tourfic' );
				}

				if ( ! $response['fieldErrors'] ) {
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
						'payment_method'   => "Booked by " . $field['tf_hotel_booked_by'],
						'status'           => 'processing',
						'order_date'       => date( 'Y-m-d H:i:s' ),
					);

					tf_set_order( $order_data );

					$response['success'] = true;
					$response['message'] = __( 'Your booking has been successfully submitted.', 'tourfic' );
				}
			}

			echo json_encode( $response );
			die();
		}

		/*
		 * Room data from room and hotel id
		 * @since 2.9.26
		 */
		public function tf_get_room_data( $hotel_id, $room_id ) {

			if ( $hotel_id && $room_id ) {
				$meta = get_post_meta( $hotel_id, 'tf_hotels_opt', true );

				$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
				if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
					$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $rooms );
					$rooms                = unserialize( $tf_hotel_rooms_value );
				}

				if ( ! empty( $rooms ) ) {
					foreach ( $rooms as $room ) {
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

							$air_service_info = sprintf( __( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								$child,
								strip_tags( wc_price( $service_child_fee ) ),
								strip_tags( wc_price( $airport_service_price_total ) )
							);

						} else {
							$air_service_info = sprintf( __( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
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
						$air_service_info            = sprintf( __( '( Fixed ) = %1$s', 'tourfic' ),
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
							$air_service_info = sprintf( __( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								$child,
								strip_tags( wc_price( $service_child_fee ) ),
								strip_tags( wc_price( $airport_service_price_total ) )
							);

						} else {
							$air_service_info = sprintf( __( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
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
						$air_service_info            = sprintf( __( '( Fixed ) = %1$s', 'tourfic' ),
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

							$air_service_info = sprintf( __( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								$child,
								strip_tags( wc_price( $service_child_fee ) ),
								strip_tags( wc_price( $airport_service_price_total ) )
							);

						} else {
							$air_service_info = sprintf( __( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
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
						$air_service_info            = sprintf( __( '( Fixed ) = %1$s', 'tourfic' ),
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

TF_Hotel_Backend_Booking::instance();