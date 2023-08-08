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
            add_action('wp_ajax_tf_check_available_room', array($this, 'tf_check_available_room'));
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
			            $tf_total_filters[$not['post_id']] = get_the_title($not['post_id']);
		            }
	            }
            }
            wp_reset_postdata();

            wp_send_json_success(array(
                'hotels' => $tf_total_filters
            ));
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
                $to   = date( 'Y/m/d', strtotime( $from . " + 1 day" ) );
            }
            $from    = date( 'Y/m/d', strtotime( $from . ' +1 day' ) );

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

            $error    = $rows = null;
            $room_array = array();

            if ( ! empty( $rooms ) ) {
                foreach ( $rooms as $room_id => $room ) {
                    // Check if room is enabled
                    $enable = ! empty( $room['enable'] ) && boolval( $room['enable'] );

                    if ( $enable ) {
//                        $footage          = ! empty( $room['footage'] ) ? $room['footage'] : 0;
//                        $bed              = ! empty( $room['bed'] ) ? $room['bed'] : 0;
//                        $adult_number     = ! empty( $room['adult'] ) ? $room['adult'] : 0;
//                        $child_number     = ! empty( $room['child'] ) ? $room['child'] : 0;
//                        $pricing_by       = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
//                        $room_price       = ! empty( $room['price'] ) ? $room['price'] : 0;
//                        $room_adult_price = ! empty( $room['adult_price'] ) ? $room['adult_price'] : 0;
//                        $room_child_price = ! empty( $room['child_price'] ) ? $room['child_price'] : 0;
//                        $total_person     = $adult_number + $child_number;
//                        $price            = $pricing_by == '1' ? $room_price : $room_adult_price + $room_child_price;

                        // Check availability by date option

                        $period = new DatePeriod(
                            new DateTime( $from . ' 00:00' ),
                            new DateInterval( 'P1D' ),
                            new DateTime( $to . ' 23:59' )
                        );

                        $days = iterator_count( $period );

                        // Check availability by date option
                        $avail_durationdate = [];
                        foreach ( $period as $date ) {
                            $avail_durationdate[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
                        }

                        /**
                         * Set room availability
                         */
                        $unique_id          = ! empty( $room['unique_id'] ) ? $room['unique_id'] : '';
                        $order_ids          = ! empty( $room['order_id'] ) ? $room['order_id'] : '';
                        $num_room_available = ! empty( $room['num-room'] ) ? $room['num-room'] : '1';
                        $reduce_num_room    = ! empty( $room['reduce_num_room'] ) ? $room['reduce_num_room'] : false;
                        $number_orders      = '0';
                        $avil_by_date       = ! empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : false;      // Room Available by date enabled or  not ?
                        if ( $avil_by_date ) {
                            $repeat_by_date = ! empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
                        }

                        if ( ! empty( $order_ids ) && function_exists( 'is_tf_pro' ) && is_tf_pro() && $reduce_num_room == true ) {

                            # Get backend available date range as an array
                            if ( $avil_by_date ) {

                                $order_date_ranges = array();

                                $backend_date_ranges = array();
                                foreach ( $repeat_by_date as $single_date_range ) {

                                    array_push( $backend_date_ranges, array( strtotime( $single_date_range["availability"]["from"] ), strtotime( $single_date_range["availability"]["to"] ) ) );

                                }
                            }

                            # Convert order ids to array
                            $order_ids = explode( ',', $order_ids );

                            # Run foreach loop through oder ids
                            foreach ( $order_ids as $order_id ) {

                                # Get $order object from order ID
                                $order = wc_get_order( $order_id );

                                # Get Only the completed orders
                                if ( $order && $order->get_status() == 'completed' ) {

                                    # Get and Loop Over Order Items
                                    foreach ( $order->get_items() as $item_id => $item ) {

                                        /**
                                         * Order item data
                                         */
                                        $ordered_number_of_room = $item->get_meta( 'number_room_booked', true );

                                        if ( $avil_by_date ) {

                                            $order_check_in_date  = strtotime( $item->get_meta( 'check_in', true ) );
                                            $order_check_out_date = strtotime( $item->get_meta( 'check_out', true ) );

                                            $tf_order_check_in_date  = $item->get_meta( 'check_in', true );
                                            $tf_order_check_out_date = $item->get_meta( 'check_out', true );
                                            if ( ! empty( $avail_durationdate ) && ( in_array( $tf_order_check_out_date, $avail_durationdate ) || in_array( $tf_order_check_in_date, $avail_durationdate ) ) ) {
                                                # Total number of room booked
                                                $number_orders = $number_orders + $ordered_number_of_room;
                                            }
                                            array_push( $order_date_ranges, array( $order_check_in_date, $order_check_out_date ) );

                                        } else {
                                            $order_check_in_date  = $item->get_meta( 'check_in', true );
                                            $order_check_out_date = $item->get_meta( 'check_out', true );
                                            if ( ! empty( $avail_durationdate ) && ( in_array( $order_check_out_date, $avail_durationdate ) || in_array( $order_check_in_date, $avail_durationdate ) ) ) {
                                                # Total number of room booked
                                                $number_orders = $number_orders + $ordered_number_of_room;
                                            }

                                        }

                                    }
                                }
                            }
                            # Calculate available room number after order
                            $num_room_available = $num_room_available - $number_orders; // Calculate
                            $num_room_available = max( $num_room_available, 0 ); // If negetive value make that 0

                        }

                        if ( $avil_by_date && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

                            // split date range
                            $check_in  = strtotime( $from . ' 00:00' );
                            $check_out = strtotime( $to . ' 00:00' );

                            // extract price from available room options
                            foreach ( $period as $date ) {

                                $available_rooms = array_values( array_filter( $repeat_by_date, function ( $date_availability ) use ( $date ) {

                                    $date_availability_from = strtotime( $date_availability['availability']['from'] . ' 00:00' );
                                    $date_availability_to   = strtotime( $date_availability['availability']['to'] . ' 23:59' );

                                    return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;

                                } ) );

                                if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {
	                                $room_array[ $room['unique_id'] ] = $room['title'];
                                }

                            }


                        } else {
	                        $room_array[ $room['unique_id'] ] = $room['title'];
                        }

                    } else {

                        $error = __( 'No Room Available!', 'tourfic' );

                    }
                }
            } else {

                $error = __( 'No Room Available!', 'tourfic' );

            }

            wp_send_json_success( array(
                'rooms' => $room_array
            ) );
        }




	}
}

TF_Backend_Booking::instance();