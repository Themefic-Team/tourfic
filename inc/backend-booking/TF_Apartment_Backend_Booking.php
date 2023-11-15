<?php 

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Apartment_Backend_Booking' ) ) {
	class TF_Apartment_Backend_Booking {
		private static $instance = null;
		private $apartment_id = null;

		public static function instance() {
			if ( self::$instance == null ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		function __construct() {
			add_action( 'tf_before_apartment_booking_details', array( $this, 'tf_apartment_backend_booking_button' ) );
			add_action( 'admin_menu', array( $this, 'tf_apartment_backend_booking_menu' ) );
			add_action( 'wp_ajax_tf_check_available_apartment', array( $this, 'tf_check_available_apartment' ) );
			add_action( 'wp_ajax_tf_check_apartment_aditional_fees', array( $this, 'tf_check_apartment_aditional_fees' ) );
			add_action('wp_head', array($this, 'apartment_booking_title'));
			add_action( 'wp_ajax_tf_backend_apartment_booking', array( $this, 'tf_backend_apartment_booking' ) );
		}

		private function get_apartment_meta_options($id, $key) {
			if(!empty($id)) {
				$meta  = get_post_meta( $id, 'tf_apartment_opt', true );
			}

			if(!empty($key)) {
				return $meta[$key];
			} else {
				return $meta;
			}
		}

		function tf_apartment_backend_booking_button() { 
			$edit_url = admin_url( 'edit.php?post_type=tf_apartment&page=tf-apartment-backend-booking' );
			?>
            <a href="<?php echo admin_url( 'edit.php?post_type=tf_apartment&page=tf-apartment-backend-booking' ); ?>" class="button button-primary tf-booking-btn"><?php _e( 'Add New Booking', 'tourfic' ); ?></a>
			<?php
		}

		function tf_apartment_backend_booking_menu() {
			add_submenu_page(
				null,
				__( 'Add New Booking', 'tourfic' ),
				__( 'Add New Booking', 'tourfic' ),
				'edit_tf_apartments',
				'tf-apartment-backend-booking',
				array( $this, 'tf_backend_booking_page' ),
			);
		}

		public function tf_backend_booking_page() {
			echo '<div class="tf-setting-dashboard">';
			tf_dashboard_header()
			?>
            <form method="post" action="" class="tf-backend-apartment-booking" enctype="multipart/form-data">
                <h1><?php _e( 'Add New Apartment Booking', 'tourfic' ); ?></h1>
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
		 */
		public function tf_backend_booking_form_fields() {
			$current_user = wp_get_current_user();
			$fields       = array(
				'tf_booking_customer_fields' => array(
					'title'  => __( 'Customer Information', 'tourfic' ),
					'fields' => array(
						array(
							'id'         => 'tf_apartment_booked_by',
							'label'      => __( 'Booked By', 'tourfic' ),
							'type'       => 'text',
							'default'    => $current_user->display_name ?: $current_user->user_login,
							'attributes' => array(
								'readonly' => 'readonly',
							),
						),
						array(
							'id'          => 'tf_apartment_customer_first_name',
							'label'       => __( 'First Name', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer First Name', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_apartment_customer_last_name',
							'label'       => __( 'Last Name', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Last Name', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_apartment_customer_email',
							'label'       => __( 'Email', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Email', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_apartment_customer_phone',
							'label'       => __( 'Phone', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Phone', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_apartment_customer_country',
							'label'       => __( 'Country / Region', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Country', 'tourfic' ),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'tf_apartment_customer_address',
							'label'       => __( 'Address', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Address', 'tourfic' ),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'tf_apartment_customer_address_2',
							'label'       => __( 'Address 2', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Address 2', 'tourfic' ),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'tf_apartment_customer_city',
							'label'       => __( 'Town / City', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer City', 'tourfic' ),
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_apartment_customer_state',
							'label'       => __( 'State', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer State', 'tourfic' ),
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_apartment_customer_zip',
							'label'       => __( 'Postcode / ZIP', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => __( 'Enter Customer Zip', 'tourfic' ),
							'field_width' => 33,
						),
					),
				),
				'tf_booking_fields' => array(
					'title'  => __( 'Booking Information', 'tourfic' ),
					'fields' => array(
						array(
							'id'      => 'tf_apartment_date',
							'label'   => __( 'Date', 'tourfic' ),
							'class'   => 'tf-field-class',
							'type'    => 'date',
							'format'  => 'Y/m/d',
							'range'   => true,
							'minDate' => 'today',
						),
						array(
							'id'          => 'tf_available_apartments',
							'label'       => __( 'Available Apartments', 'tourfic' ),
							'type'        => 'select2',
							'class'   => 'tf-field-class',
							'options'     => 'posts',
							'query_args'  => array(
								'post_type'      => 'tf_apartment',
								'posts_per_page' => - 1,
								'post_status'    => 'publish',
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_apartment_additional_fees',
							'label'       => __( 'Additional Fees', 'tourfic' ),
							'class'   => 'tf-field-class',
							'type'        => 'select2',
							'options'     => 'posts',
							'attributes'  => array( 'disabled' => 'disabled' ),
							'placeholder' => __( 'Please choose the apartment first', 'tourfic' ),
							'field_width' => 50,
							'is_pro'      => true
						),
						array(
							'id'          => 'tf_apartment_adults_number',
							'label'       => __( 'Adults', 'tourfic' ),
							'type'        => 'number',
							'attributes'  => array(
								'min' => 1,
							),
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_apartment_children_number',
							'label'       => __( 'Children', 'tourfic' ),
							'type'        => 'number',
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_apartment_infant_number',
							'label'       => __( 'Infant', 'tourfic' ),
							'type'        => 'number',
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33,
						),
					),
				),
			);

			return $fields;
		}

		public function tf_check_available_apartment() {
			$from = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
			$to   = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';

			$loop = new WP_Query( array(
				'post_type'      => 'tf_apartment',
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
					tf_filter_apartment_by_date( $period, $not_found, array( 1, 1, 1, '' ) );
				}

				$tf_total_filters = [];

				foreach ( $not_found as $filter_post ) {
					if ( $filter_post['found'] == 1 ) {
						$tf_total_filters[ $filter_post['post_id'] ] = get_the_title( $filter_post['post_id'] );
					} else {
						$tf_total_filters[] = "Not Found";
					}
				}
			}
			wp_reset_postdata();

			wp_send_json_success( array(
				'apartments' => $tf_total_filters
			) );
		}

		public function tf_check_apartment_aditional_fees() {
			$this->apartment_id = isset( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : '';

			 // Additional Fees data

			$additional_fees = !empty($this->get_apartment_meta_options($this->apartment_id, "additional_fees")) ? $this->get_apartment_meta_options($this->apartment_id, "additional_fees") : array();

			$all_fees = [];
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro()) :
				if(count($additional_fees) > 0) {
					foreach($additional_fees as $fees) {
						$all_fees [] = array (
							"label" => $fees["additional_fee_label"],
							"fee" => $fees["additional_fee"],
							"price" => wc_price($fees["additional_fee"]),
							"type" => $fees["fee_type"]
						); 
					}
				}
			endif;

			wp_reset_postdata();

			wp_send_json_success( array(
				'additional_fees' => $all_fees,
			) );
		}

		function get_total_apartment_price($id, $check_in, $check_out, $adult_count, $child_count, $infant_count, $addiional_fees) {
			if(!empty($id)) {
				$availability_switch = !empty($this->get_apartment_meta_options($id, "enable_availability")) ? $this->get_apartment_meta_options($id, "enable_availability") : '';
				$apt_availability = !empty($this->get_apartment_meta_options($id, "apt_availability")) ? $this->get_apartment_meta_options($id, "apt_availability") : '';
			}

			$apartment_disable_dates = [];
			$apartment_enablee_dates = [];
			$apartment_total_price = [];
			if($availability_switch === '1' && ! empty( $apt_availability ) && function_exists( 'is_tf_pro' ) && is_tf_pro()) {
				$apartment_availability_arr = json_decode($apt_availability, true);

				if(!empty($apartment_availability_arr) && is_array( $apartment_availability_arr)) {
					foreach ( $apartment_availability_arr as $available_date ) {
						
						if ( $available_date['status'] === 'unavailable' ) {
							$apartment_disable_dates[] = $available_date['check_in'];
						}

						if ( $available_date['status'] === 'available' ) {
							$apartment_enablee_dates[] = $available_date['check_in'];
						}

						// if(!empty($available_date["pricing_type"]) && $available_date["pricing_type"] == "per_night") {

						// }

						//TODO: need to calculate the pricing and booking functionality

					}
				}

			}
			

		}

		public function tf_backend_apartment_booking() {
			$response = array(
				'success' => false,
			);

			$field = [];
			foreach ( $_POST as $key => $value ) {
				if ( $key === 'tf_apartment_date' ) {
					$field[ $key ]['from'] = sanitize_text_field( $value['from'] );
					$field[ $key ]['to']   = sanitize_text_field( $value['to'] );
				} else {
					$field[ $key ] = $value;
				}
			}

			$required_fields = array(
				'tf_apartment_booked_by',
				'tf_apartmenmt_customer_first_name',
				'tf_apartmenmt_customer_email',
				'tf_apartmenmt_customer_phone',
				'tf_apartmenmt_customer_country',
				'tf_apartmenmt_customer_address',
				'tf_apartmenmt_customer_city',
				'tf_apartmenmt_customer_state',
				'tf_apartmenmt_customer_zip',
				'tf_apartment_date',
				'tf_available_apartments',
				'tf_apartment_adults_number'
			);

			if ( ! isset( $field['tf_backend_booking_nonce'] ) || ! wp_verify_nonce( $field['tf_backend_booking_nonce'], 'tf_backend_booking_nonce_action' ) ) {
				$response['message'] = __( 'Sorry, your nonce did not verify.', 'tourfic' );
			} else {
				foreach ( $required_fields as $required_field ) {
					if ( $required_field === 'tf_apartment_date' ) {
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
		
	}
}

TF_Apartment_Backend_Booking::instance();