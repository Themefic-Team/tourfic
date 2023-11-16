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
					tf_filter_apartment_by_date( $period, $not_found, array( 1, 0, 0, '' ) );
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

			if($addional_fees && !empty($addional_fees) && $apartment_pricing > 0 ) {
				foreach($addional_fees as $fees) {
					if(!empty($fees["additional_fee"])) {
						$apartment_pricing += $fees["additional_fee"];
					}
				}
			}
			return $apartment_pricing;
		}

		public function apartment_day_diference_calculation($check_in, $check_out) {
			if ( $check_in && $check_out ) {
				$check_in_stt  = strtotime( $check_in . ' +1 day' );
				$check_out_stt = strtotime( $check_out );
				$days          = ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1;

				$tfperiod = new DatePeriod(
					new DateTime( $check_in . ' 00:00' ),
					new DateInterval( 'P1D' ),
					new DateTime( $check_out . ' 23:59' )
				);
			}

			return ["days" => $days, "period"=>$tfperiod];
		}

		public function tf_check_apartment_aditional_fees() {
			$this->apartment_id = isset( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : '';
			$from = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
			$to   = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';

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

			wp_send_json_success( 
				array(
				'additional_fees' => $all_fees,
				) 
			);
		}
	}
}

TF_Apartment_Backend_Booking::instance();