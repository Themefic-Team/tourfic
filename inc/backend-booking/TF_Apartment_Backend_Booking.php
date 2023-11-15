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
		}

		function tf_apartment_backend_booking_button() { 
			$edit_url = admin_url( 'edit.php?post_type=tf_apartment&page=tf-apartment-backend-booking' );
			?>
            <a href="<?php echo admin_url( 'edit.php?post_type=tf_apartment&page=tf-apartment-backend-booking' ); ?>" class="button button-primary tf-booking-btn"><?php _e( 'Add New Booking', 'tourfic' ); ?></a>
			<?php
		}

		function apartment_meta($id, $key) {
			$meta  = get_post_meta( $id, 'tf_apartments_opt', true );

			return $meta[$key];
		}

		function additional_fees_showing() {

			// $fees = $this->apartment_meta('additional_fees');

			return <<<EOD
			Additional Fees will go here
			EOD;
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
		 * @since 2.9.26
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
							'style'   => 'success',
							'content' => __( $this->additional_fees_showing(), 'tourfic' ),
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
			$hotel_id = isset( $_POST['hotel_id'] ) ? sanitize_text_field( $_POST['hotel_id'] ) : '';
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
			$apartment_id = isset( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : '';
			$from     = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
			$to       = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';

			/**
			 * Additional Fees data
			 */
			$meta  = get_post_meta( $apartment_id, 'tf_apartment_opt', true );

			$additional_fees = !empty($meta["additional_fees"]) ? $meta["additional_fees"] : array();

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
		
	}
}

TF_Apartment_Backend_Booking::instance();