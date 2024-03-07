<?php 

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Apartment_Backend_Booking' ) ) {
	class TF_Apartment_Backend_Booking {
		private static $instance = null;

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

		private function get_apartment_meta_options($id, $key = '') {
			if(empty($id) || $id == null || $id == "undefined" || $id == 0) {
				return;
			}

			$meta = get_post_meta( $id, 'tf_apartment_opt', true );

			if(!empty($key)) {
				return $meta[$key];
			} else {
				return $meta;
			}
		}

		function tf_apartment_backend_booking_button() { 
			$edit_url = admin_url( 'edit.php?post_type=tf_apartment&page=tf-apartment-backend-booking' );
			?>
            <a href="<?php echo $edit_url; ?>" class="button button-primary tf-booking-btn"><?php esc_html_e( 'Add New Booking', 'tourfic' ); ?></a>
			<?php
		}

		function tf_apartment_backend_booking_menu() {
			$tf_aprt_parentmenu = !empty($_GET['page']) && "tf-apartment-backend-booking"==$_GET['page'] ? 'edit.php?post_type=tf_apartment' : '';
			add_submenu_page(
				$tf_aprt_parentmenu,
				esc_html__( 'Add New Booking', 'tourfic' ),
				esc_html__( 'Add New Booking', 'tourfic' ),
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
                <h1><?php esc_html_e( 'Add New Apartment Booking', 'tourfic' ); ?></h1>
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
                    <button type="submit" class="tf-admin-btn tf-btn-secondary tf-submit-btn" id="tf-backend-apartment-book-btn"><?php esc_html_e( 'Book Now', 'tourfic' ); ?></button>
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
					'title'  => esc_html__( 'Customer Information', 'tourfic' ),
					'fields' => array(
						array(
							'id'         => 'tf_apartment_booked_by',
							'label'      => esc_html__( 'Booked By', 'tourfic' ),
							'type'       => 'text',
							'default'    => $current_user->display_name ?: $current_user->user_login,
							'attributes' => array(
								'readonly' => 'readonly',
							),
						),
						array(
							'id'          => 'tf_apartment_customer_first_name',
							'label'       => esc_html__( 'First Name', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Enter Customer First Name', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_apartment_customer_last_name',
							'label'       => esc_html__( 'Last Name', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Enter Customer Last Name', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_apartment_customer_email',
							'label'       => esc_html__( 'Email', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Enter Customer Email', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_apartment_customer_phone',
							'label'       => esc_html__( 'Phone', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Enter Customer Phone', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_apartment_customer_country',
							'label'       => esc_html__( 'Country / Region', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Enter Customer Country', 'tourfic' ),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'tf_apartment_customer_address',
							'label'       => esc_html__( 'Address', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Enter Customer Address', 'tourfic' ),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'tf_apartment_customer_address_2',
							'label'       => esc_html__( 'Address 2', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Enter Customer Address 2', 'tourfic' ),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'tf_apartment_customer_city',
							'label'       => esc_html__( 'Town / City', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Enter Customer City', 'tourfic' ),
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_apartment_customer_state',
							'label'       => esc_html__( 'State', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Enter Customer State', 'tourfic' ),
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_apartment_customer_zip',
							'label'       => esc_html__( 'Postcode / ZIP', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => esc_html__( 'Enter Customer Zip', 'tourfic' ),
							'field_width' => 33,
						),
					),
				),
				'tf_booking_fields' => array(
					'title'  => esc_html__( 'Booking Information', 'tourfic' ),
					'fields' => array(
						array(
							'id'      => 'tf_apartment_date',
							'label'   => esc_html__( 'Date', 'tourfic' ),
							'class'   => 'tf-field-class',
							'type'    => 'date',
							'format'  => 'Y/m/d',
							'range'   => true,
							'minDate' => 'today',
						),
						array(
							'id'          => 'tf_available_apartments',
							'label'       => esc_html__( 'Available Apartments', 'tourfic' ),
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
							'label'       => esc_html__( 'Additional Fees', 'tourfic' ),
							'class'   => 'tf-field-class',
							'type'        => 'select2',
							'options'     => 'posts',
							'attributes'  => array( 'disabled' => 'disabled' ),
							'placeholder' => esc_html__( 'Please choose the apartment first', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_apartment_adults_number',
							'label'       => esc_html__( 'Adults', 'tourfic' ),
							'type'        => 'number',
							'attributes'  => array(
								'min' => 1,
							),
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_apartment_children_number',
							'label'       => esc_html__( 'Children', 'tourfic' ),
							'type'        => 'number',
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_apartment_infant_number',
							'label'       => esc_html__( 'Infant', 'tourfic' ),
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
			$apartment_id = isset( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : '';
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

			$check_in_out = "$from - $to";

			if ( $loop->have_posts() ) {
				$not_found = [];
				while ( $loop->have_posts() ) {
					$loop->the_post();
					tf_filter_apartment_by_date( $period, $not_found, array( 1, 0, 0, $check_in_out ) );
				}

				$tf_total_filters = [];

				foreach ( $not_found as $filter_post ) {
					if ( $filter_post['found'] != 1 ) {
						$tf_total_filters[ $filter_post['post_id'] ] = get_the_title( $filter_post['post_id'] );
					}
				}
			}
			wp_reset_postdata();

			wp_send_json_success( array(
				'apartments' => $tf_total_filters,
			) );
		}
		
		function get_total_apartment_price($id, $check_in, $check_out, $adult_count, $child_count, $infant_count, $addional_fees) {

			$adult_count = !empty($adult_count) ? $adult_count : 0;
			$child_count = !empty($child_count) ? $child_count : 0;
			$infant_count = !empty($infant_count) ? $infant_count : 0;
			$day_diff = !empty($this->apartment_day_diference_calculation($check_in, $check_out)) ? $this->apartment_day_diference_calculation($check_in, $check_out) : 0;
			
			if(!empty($id)) {
				$availability_switch = !empty($this->get_apartment_meta_options($id, "enable_availability")) ? $this->get_apartment_meta_options($id, "enable_availability") : '';
				$apt_availability = !empty($this->get_apartment_meta_options($id, "apt_availability")) ? $this->get_apartment_meta_options($id, "apt_availability") : '';
				$apartment_price_type = !empty($this->get_apartment_meta_options($id, "pricing_type")) ? $this->get_apartment_meta_options($id, "pricing_type") : 'per_night';
				$apartment_price = !empty( $this->get_apartment_meta_options($id, 'price_per_night')) ? $this->get_apartment_meta_options($id, 'price_per_night') : 0;
				$apartment_adult_price = !empty( $this->get_apartment_meta_options($id, 'adult_price')) ? $this->get_apartment_meta_options($id, 'adult_price') : 0;
				$apartment_child_price = !empty( $this->get_apartment_meta_options($id, 'child_price')) ? $this->get_apartment_meta_options($id, 'child_price') : 0;
				$apartment_infant_price = !empty( $this->get_apartment_meta_options($id, 'infant_price')) ? $this->get_apartment_meta_options($id, 'infant_price') : 0;
				$discount_type = !empty( $this->get_apartment_meta_options($id, 'discount_type')) ? $this->get_apartment_meta_options($id, 'discount_type') : 'none';
				$discount_amount = !empty( $this->get_apartment_meta_options($id, 'discount')) && $discount_type != 'none' ? $this->get_apartment_meta_options($id, 'discount') : 0;
			}

			$apartment_pricing = 0;

			if($availability_switch === '1' && ! empty( $apt_availability ) && function_exists( 'is_tf_pro' ) && is_tf_pro()) {
				$apartment_avail = json_decode($apt_availability, true);

				if(!empty($apartment_avail)) {
					foreach($apartment_avail as $date) {
						if(!empty($date["check_in"]) && ($date["check_in"] == $check_in)) {
							if($date['pricing_type'] == "per_night") {
								$apartment_pricing = !empty($date["price"]) ? intval( (int) $date["price"] * $day_diff["days"]) : 0 ;
							} else {
								$apartment_adult_price = !empty($date["adult_price"]) ? $date["adult_price"] : 0;
								$apartment_child_price = !empty($date["child_price"]) ? $date["child_price"] : 0;
								$apartment_infant_price = !empty($date["infant_price"]) ? $date["infant_price"] : 0;

								$apartment_pricing = intval((((int) $apartment_adult_price * (int) $adult_count) + ((int) $apartment_child_price * (int) $child_count) + ((int) $apartment_infant_price * (int) $infant_count)) * $day_diff["days"]);
							}
						}
					}
				}
			} else {
				if( !empty($apartment_price_type) && $apartment_price_type == "per_night" ) {
					$apartment_pricing = intval((int) $apartment_price * (int) $day_diff["days"]);
				} else {
					$apartment_pricing = intval((((int) $apartment_adult_price * (int) $adult_count) + ((int) $apartment_child_price * (int) $child_count) + ((int) $apartment_infant_price * (int) $infant_count)) * $day_diff["days"]);
				}
			}

			// Discount Calculation

			if(!empty($discount_type) && !empty($discount_amount) ) { 
				if($discount_type == "percent") {
					$apartment_pricing = !empty( $apartment_pricing ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( $apartment_pricing - (  ( $apartment_pricing / 100 ) * $discount_amount ), 2 ) ) ) : 0;
				}else if($discount_type == "fixed") {
					$apartment_pricing = !empty( $apartment_pricing ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( $apartment_pricing - $discount_amount ), 2 ) ) : 0;
				}
			}

			// additional fees Calculation 
			if(is_array($addional_fees) ) {

				if($addional_fees && !empty($addional_fees) && $apartment_pricing > 0 ) {
					foreach($addional_fees as $fees) {
						if(!empty($fees["additional_fee"])) {
							if(!empty($fees["fee_type"]) && $fees["fee_type"] == "per_stay") {
								$apartment_pricing += $fees["additional_fee"];
							} 
							if (!empty($fees["fee_type"]) && $fees["fee_type"] == "per_person") {
								$apartment_pricing += intval((int) $fees["additional_fee"] * ((int) $adult_count + (int) $child_count + (int) $infant_count));
							}
							if(!empty($fees["fee_type"]) && $fees["fee_type"] == "per_night") {
								$apartment_pricing += !empty($day_diff["days"]) ? intval( (int) $fees["additional_fee"] * $day_diff["days"] ) : $fees["additional_fee"];
							}
						}
					}
				}
			}
			
			return $apartment_pricing;
		}

		public function apartment_day_diference_calculation($check_in, $check_out) {
			if ( !empty($check_in) && !empty($check_out) ) {
				$check_in_stt  = !empty($check_in) ? strtotime( $check_in . ' +1 day' ) : 0;
				$check_out_stt = !empty($check_out) ? strtotime( $check_out ) : 0; 
				$days = !empty($check_in_stt) && !empty($check_out_stt) ? ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 : 0;

				$tfperiod = new DatePeriod(
					new DateTime( $check_in . ' 00:00' ),
					new DateInterval( 'P1D' ),
					new DateTime( $check_out . ' 23:59' )
				);
			}
			return array(
				"days" => !empty($days) ? $days : 0,
				"period" => !empty($tfperiod) ? $tfperiod : 0
			);
		}

		public function tf_check_apartment_aditional_fees() {
			$apartment_id = isset( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : 0;
			$from = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
			$to   = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';

			 // Additional Fees data
			
			 if($apartment_id != 0) {
				$additional_fees = !empty( $this->get_apartment_meta_options( $apartment_id, "additional_fees" ) ) ? $this->get_apartment_meta_options( $apartment_id, "additional_fees" ) : array();
			 }

			$all_fees = [];
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($additional_fees)) {
				if ( count( $additional_fees ) > 0 ) {
					foreach ( $additional_fees as $fees ) {
						$all_fees[] = array(
							"label" => $fees["additional_fee_label"],
							"fee"   => $fees["additional_fee"],
							"price" => wc_price( $fees["additional_fee"] ),
							"type"  => $fees["fee_type"],
						);
					}
				}
			} else {
				$additional_fee_label = !empty( $this->get_apartment_meta_options( $apartment_id, "additional_fee_label" ) ) ? $this->get_apartment_meta_options( $apartment_id, "additional_fee_label" ) : '';
				$additional_fee_amount = !empty( $this->get_apartment_meta_options( $apartment_id, "additional_fee" ) ) ? $this->get_apartment_meta_options( $apartment_id, "additional_fee" ) : 0;
				$additional_fee_type = !empty( $this->get_apartment_meta_options( $apartment_id, "fee_type" ) ) ? $this->get_apartment_meta_options( $apartment_id, "fee_type" ) : '';

				if($additional_fee_amount != 0) {
					$all_fees[] = array(
						"label" => $additional_fee_label,
						"fee"   => $additional_fee_amount,
						"price" => wc_price( $additional_fee_amount ),
						"type"  => $additional_fee_type,
					);
				}
			}

			wp_reset_postdata();

			wp_send_json_success( 
				array(
				'additional_fees' => $all_fees,
				) 
			);
		}
		// Backend Booking

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
				'tf_apartment_customer_first_name',
				'tf_apartment_customer_email',
				'tf_apartment_customer_phone',
				'tf_apartment_customer_country',
				'tf_apartment_customer_address',
				'tf_apartment_customer_city',
				'tf_apartment_customer_state',
				'tf_apartment_customer_zip',
				'tf_apartment_date',
				'tf_available_apartments',
				'tf_apartment_adults_number'
			);

			if ( ! isset( $field['tf_backend_booking_nonce'] ) || ! wp_verify_nonce( $field['tf_backend_booking_nonce'], 'tf_backend_booking_nonce_action' ) ) {
				$response['message'] = esc_html__( 'Sorry, your nonce did not verify.', 'tourfic' );
			} else {
				foreach ( $required_fields as $required_field ) {
					if ( $required_field === 'tf_apartment_date' ) {
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

				$apt_id = !empty($field['tf_available_apartments']) ? intval($field['tf_available_apartments']) : 0;
				$adult_count = !empty($field['tf_apartment_adults_number']) ? intval($field['tf_apartment_adults_number']) : 0;
				$child_count = !empty($field['tf_apartment_children_number']) ? intval($field['tf_apartment_children_number']) : 0;
				$infant_count = !empty($field['tf_apartment_infant_number']) ? intval($field['tf_apartment_infant_number']) : 0;
				$check_from = !empty($field['tf_apartment_date']['from']) ? $field['tf_apartment_date']['from'] : '';
				$check_to = !empty($field['tf_apartment_date']['to']) ? $field['tf_apartment_date']['to'] : '';
				$apt_data = $this->get_apartment_meta_options( intval( $apt_id ) );

				if ( function_exists( 'is_tf_pro' ) && is_tf_pro()) {
					$additional_fees = !empty($apt_data['additional_fees']) ? $apt_data['additional_fees'] : array();
				} else {
					$additional_fees [] = array(
						"additional_fee_label" => !empty($apt_data['additional_fee_label']) ? $apt_data['additional_fee_label'] : '',
						"additional_fee"   => !empty($apt_data['additional_fee']) ? $apt_data['additional_fee'] : 0,
						"fee_type"  => !empty($apt_data['fee_type']) ? $apt_data['fee_type'] : '',
					);
				}


				if ( $apt_data['max_adults'] < $adult_count ) {
					$response['fieldErrors']['tf_apartment_adults_number_error'] = esc_html__( "You can't book more than " . $apt_data['max_adults'] . " adults", 'tourfic' );
				}
				if ( $apt_data['max_children'] < $child_count ) {
					$response['fieldErrors']['tf_apartment_children_number_error'] = esc_html__( "You can't book more than " . $apt_data['max_children'] . " children", 'tourfic' );
				}
				if ( $apt_data['max_infants'] < $infant_count ) {
					$response['fieldErrors']['tf_apartment_infant_number_error'] = esc_html__( "You can't book more than " . $apt_data['max_infants'] . " infants", 'tourfic' );
				}

				if ( ! array_key_exists("fieldErrors", $response) || ! $response['fieldErrors'] ) {
					$total_price = $this->get_total_apartment_price($apt_id, $check_from, $check_to, $adult_count, $child_count, $infant_count, $additional_fees);
					$billing_details  = array(
						'billing_first_name' => $field['tf_apartment_customer_first_name'],
						'billing_last_name'  => $field['tf_apartment_customer_last_name'],
						'billing_company'    => '',
						'billing_address_1'  => $field['tf_apartment_customer_address'],
						'billing_address_2'  => $field['tf_apartment_customer_address_2'],
						'billing_city'       => $field['tf_apartment_customer_city'],
						'billing_state'      => $field['tf_apartment_customer_state'],
						'billing_postcode'   => $field['tf_apartment_customer_zip'],
						'billing_country'    => $field['tf_apartment_customer_country'],
						'billing_email'      => $field['tf_apartment_customer_email'],
						'billing_phone'      => $field['tf_apartment_customer_phone'],
					);
					$shipping_details = array(
						'shipping_first_name' => $field['tf_apartment_customer_first_name'],
						'shipping_last_name'  => $field['tf_apartment_customer_last_name'],
						'shipping_company'    => '',
						'shipping_address_1'  => $field['tf_apartment_customer_address'],
						'shipping_address_2'  => $field['tf_apartment_customer_address_2'],
						'shipping_city'       => $field['tf_apartment_customer_city'],
						'shipping_state'      => $field['tf_apartment_customer_state'],
						'shipping_postcode'   => $field['tf_apartment_customer_zip'],
						'shipping_country'    => $field['tf_apartment_customer_country'],
						'shipping_phone'      => $field['tf_apartment_customer_phone'],
						'tf_email'      => $field['tf_customer_email'],
					);
					$order_details    = [
						'order_by'             => $field['tf_apartment_booked_by'],
						'check_in'             => $field['tf_apartment_date']['from'],
						'check_out'            => $field['tf_apartment_date']['to'],
						'adult'                => $field['tf_apartment_adults_number'],
						'child'                => $field['tf_apartment_children_number'],
						'infant'                => $field['tf_apartment_infant_number'],
						'children_ages'        => '',
						'total_price'          => $total_price,
						'due_price'            => '',
					];

					$order_data = array(
						'post_id'          => intval( $field['tf_available_apartments'] ),
						'post_type'        => 'apartment',
						'room_number'      => null,
						'check_in'         => $check_from,
						'check_out'        => $check_to,
						'billing_details'  => $billing_details,
						'shipping_details' => $shipping_details,
						'order_details'    => $order_details,
						'payment_method'   => "offline",
						'status'           => 'processing',
						'order_date'       => date( 'Y-m-d H:i:s' ),
					);

					tf_set_order( $order_data );

					$response['success'] = true;
					$response['message'] = esc_html__( 'Your booking has been successfully submitted.', 'tourfic' );
				}
			}

			echo json_encode( $response );
			die();
		}
	}
}

TF_Apartment_Backend_Booking::instance();