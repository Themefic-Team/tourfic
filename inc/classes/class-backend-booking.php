<?php
defined( 'ABSPATH' ) || exit;
/**
 * TF Backend Booking
 * @since 2.9.26
 * @author Foysal
 */
if ( ! class_exists( 'TF_Backend_Booking' ) ) {
	class TF_Backend_Booking {

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
            add_action('wp_ajax_tf_check_available_hotel', array($this, 'tf_check_available_hotel'));
		}

		function tf_hotel_backend_booking_button() {
			?>
            <a href="<?php echo admin_url( 'edit.php?post_type=tf_hotel&page=tf-backend-booking' ); ?>" class="button button-primary tf-export-btn"><?php _e( 'Add New Booking', 'tourfic' ); ?></a>
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
				'tf-backend-booking',
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
            <form method="post" action="" class="tf-add-new-booking" enctype="multipart/form-data">
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
										$tf_option->field( $field, $value, $id );

									endforeach;
								endif; ?>
                            </div>
                        </div>
                    </div>
				<?php endforeach; ?>
				<?php wp_nonce_field( 'tf_backend_booking_nonce_action', 'tf_backend_booking_nonce' ); ?>

                <!-- Footer -->
                <div class="tf-backend-booking-footer">
                    <button type="submit" class="tf-admin-btn tf-btn-secondary tf-submit-btn"><?php _e( 'Book Now', 'tourfic' ); ?></button>
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
							'id'         => 'tf_hotel_booker_id',
							'label'      => __( 'Booked By', 'tourfic' ),
							'type'       => 'text',
							'default'    => $current_user->display_name ? $current_user->display_name : $current_user->user_login,
							'attributes' => array(
								'readonly' => 'readonly',
								'disabled' => 'disabled',
							),
						),
						array(
							'id'          => 'tf_customer_first_name',
							'label'       => __( 'First Name', 'tourfic' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Enter Customer First Name', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_customer_last_name',
							'label'       => __( 'Last Name', 'tourfic' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Enter Customer Last Name', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_customer_email',
							'label'       => __( 'Email', 'tourfic' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Enter Customer Email', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_customer_phone',
							'label'       => __( 'Phone', 'tourfic' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Enter Customer Phone', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_customer_address',
							'label'       => __( 'Address', 'tourfic' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Enter Customer Address', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_customer_address_2',
							'label'       => __( 'Address 2', 'tourfic' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Enter Customer Address 2', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'tf_customer_city',
							'label'       => __( 'Town / City', 'tourfic' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Enter Customer City', 'tourfic' ),
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_customer_state',
							'label'       => __( 'State', 'tourfic' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Enter Customer State', 'tourfic' ),
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_customer_zip',
							'label'       => __( 'Postcode / ZIP', 'tourfic' ),
							'type'        => 'text',
							'default'     => '',
							'placeholder' => __( 'Enter Customer Zip', 'tourfic' ),
							'field_width' => 33,
						),
					),
				),
				'tf_booking_fields'          => array(
					'title'  => __( 'Booking Information', 'tourfic' ),
					'fields' => array(
						array(
							'id'     => 'tf_hotel_date',
							'label'  => __( 'Date', 'tourfic' ),
							'type'   => 'date',
							'format' => 'Y/m/d',
							'range'  => true,
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
							'id'          => 'tf_hotel_adults_number',
							'label'       => __( 'Adults', 'tourfic' ),
							'type'        => 'number',
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_hotel_children_number',
							'label'       => __( 'Children', 'tourfic' ),
							'type'        => 'number',
							'field_width' => 33,
						),
						array(
							'id'          => 'tf_hotel_infants_number',
							'label'       => __( 'Infants', 'tourfic' ),
							'type'        => 'number',
							'field_width' => 33,
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
        public function tf_check_available_hotel(){
            $from = isset($_POST['from']) ? sanitize_text_field($_POST['from']) : '';
            $to = isset($_POST['to']) ? sanitize_text_field($_POST['to']) : '';

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

            if($loop->have_posts()){
	            $not_found = [];
                while($loop->have_posts()){
                    $loop->the_post();
	                tf_filter_hotel_by_date( $period, $not_found, array( 1, 1, 1, '' ) );
                }

	            $tf_total_filters = [];
	            foreach ( $not_found as $not ) {
		            if ( $not['found'] != 1 ) {
			            $tf_total_filters[] = $not['post_id'];
		            }
	            }
            }
            wp_reset_postdata();

            $tf_backend_booking_form_fields = $this->tf_backend_booking_form_fields();
            $tf_backend_booking_form_fields['tf_booking_fields']['fields'][1]['query_args'] = array(
                'post_type'      => 'tf_hotel',
                'posts_per_page' => - 1,
                'post_status'    => 'publish',
                'post__in'       => $tf_total_filters
            );

            ob_start();
            $this->tf_render_form_fields( $tf_backend_booking_form_fields );
            $form = ob_get_clean();

            wp_send_json_success(array(
                'form' => $form
            ));
        }
	}
}

TF_Backend_Booking::instance();