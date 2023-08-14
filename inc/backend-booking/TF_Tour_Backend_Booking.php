<?php
defined( 'ABSPATH' ) || exit;
/**
 * TF Tour Backend Booking
 * @since 2.9.26
 * @author Foysal
 */
if ( ! class_exists( 'TF_Tour_Backend_Booking' ) ) {
	class TF_Tour_Backend_Booking {

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
			add_action( 'tf_before_tour_booking_details', array( $this, 'tf_tour_backend_booking_button' ) );
			add_action( 'admin_menu', array( $this, 'tf_backend_booking_menu' ) );
			add_action( 'wp_ajax_tf_check_available_tour', array( $this, 'tf_check_available_tour' ) );
			add_action( 'wp_ajax_tf_backend_tour_booking', array( $this, 'tf_backend_tour_booking' ) );
		}

		function tf_tour_backend_booking_button() {
			?>
            <a href="<?php echo admin_url( 'edit.php?post_type=tf_tours&page=tf-tour-backend-booking' ); ?>" class="button button-primary tf-export-btn"><?php _e( 'Add New Booking', 'tourfic' ); ?></a>
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
				'edit_tf_tourss',
				'tf-tour-backend-booking',
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
            <form method="post" action="" class="tf-backend-tour-booking" enctype="multipart/form-data">
                <h1><?php _e( 'Add New Tour Booking', 'tourfic' ); ?></h1>
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
                    <button type="submit" class="tf-admin-btn tf-btn-secondary tf-submit-btn" id="tf-backend-tour-book-btn"><?php _e( 'Book Now', 'tourfic' ); ?></button>
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
							'id'         => 'tf_tour_booked_by',
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
							'id'      => 'tf_tour_date',
							'label'   => __( 'Date', 'tourfic' ),
							'type'    => 'date',
							'format'  => 'Y/m/d',
							'range'   => true,
							'minDate' => 'today',
						),
						array(
							'id'          => 'tf_tour_adults_number',
							'label'       => __( 'Adults', 'tourfic' ),
							'type'        => 'number',
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'tf_tour_children_number',
							'label'       => __( 'Children', 'tourfic' ),
							'type'        => 'number',
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'tf_tour_infants_number',
							'label'       => __( 'Infants', 'tourfic' ),
							'type'        => 'number',
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'         => 'tf_available_tours',
							'label'      => __( 'Available Tours', 'tourfic' ),
							'type'       => 'select2',
							'options'    => 'posts',
							'query_args' => array(
								'post_type'      => 'tf_tours',
								'posts_per_page' => - 1,
								'post_status'    => 'publish',
							),
						),
					),
				),
			);

			return $fields;
		}

		/*
		 * Check available tour by date
		 * @since 2.9.26
		 */
		public function tf_check_available_tour() {
			$from     = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : '';
			$to       = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : '';
			$adults   = isset( $_POST['adults'] ) ? sanitize_text_field( $_POST['adults'] ) : '';
			$children = isset( $_POST['children'] ) ? sanitize_text_field( $_POST['children'] ) : '';

			$loop = new WP_Query( array(
				'post_type'      => 'tf_tours',
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
				$not_found   = [];
				$total_posts = $loop->post_count;
				while ( $loop->have_posts() ) {
					$loop->the_post();
					tf_filter_tour_by_date( $period, $total_posts, $not_found, array( $adults, $children, '' ) );
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
				'tours' => $tf_total_filters
			) );
		}

		/*
		 * Booking form submit
		 * @since 2.9.26
		 */
		public function tf_backend_tour_booking() {
			$response = array(
				'success' => false,
			);

			$field = [];
			foreach ( $_POST as $key => $value ) {
				if ( $key === 'tf_tour_date' ) {
					$field[ $key ]['from'] = sanitize_text_field( $value['from'] );
					$field[ $key ]['to']   = sanitize_text_field( $value['to'] );
				} else {
					$field[ $key ] = $value;
				}
			}

			$required_fields = array(
				'tf_tour_booked_by',
				'tf_customer_first_name',
				'tf_customer_last_name',
				'tf_customer_email',
				'tf_customer_phone',
				'tf_customer_country',
				'tf_customer_address',
				'tf_customer_city',
				'tf_customer_state',
				'tf_customer_zip',
				'tf_tour_date',
				'tf_available_tours',
				'tf_tour_adults_number'
			);

			if ( ! isset( $field['tf_backend_booking_nonce'] ) || ! wp_verify_nonce( $field['tf_backend_booking_nonce'], 'tf_backend_booking_nonce_action' ) ) {
				$response['message'] = __( 'Sorry, your nonce did not verify.', 'tourfic' );
			} else {
				foreach ( $required_fields as $required_field ) {
					if ( $required_field === 'tf_tour_date' ) {
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

				if ( ! $response['fieldErrors'] ) {
					$total_price      = $this->tf_get_tour_total_price( intval( $field['tf_available_tours'] ), $field['tf_tour_date']['from'], $field['tf_tour_date']['to'], $field['tf_tour_adults_number'], $field['tf_tour_children_number'], $field['tf_tour_infants_number'] );
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

					if ( ! empty( $tour_type ) && $tour_type == 'fixed' ) {
						$tour_date = $field['tf_tour_date']['from'] . ' - ' . $field['tf_tour_date']['to'];
					} elseif ( $tour_type && $tour_type == 'continuous' ) {
						$tour_date = date( "Y/m/d", strtotime( $field['tf_tour_date']['from'] . '-' . $field['tf_tour_date']['to'] ) );
					}

					$order_details = [
						'order_by'    => $field['tf_tour_booked_by'],
						'tour_date'   => $tour_date,
						'tour_time'   => '',
						'tour_extra'  => '',
						'adult'       => $field['tf_tour_adults_number'],
						'child'       => $field['tf_tour_children_number'],
						'infants'     => $field['tf_tour_infants_number'],
						'total_price' => $total_price,
						'due_price'   => '',
					];

					$order_data = array(
						'post_id'          => intval( $field['tf_available_tours'] ),
						'post_type'        => 'tour',
						'room_number'      => null,
						'check_in'         => $field['tf_tour_date']['from'],
						'check_out'        => $field['tf_tour_date']['to'],
						'billing_details'  => $billing_details,
						'shipping_details' => $shipping_details,
						'order_details'    => $order_details,
						'payment_method'   => 'cod',
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
		 * Calculate tour total price
		 * @since 2.9.26
		 */
		public function tf_get_tour_total_price( $tour_id, $check_in, $check_out, $adults, $children, $infant ) {
			$response = [];

			$meta                 = get_post_meta( $tour_id, 'tf_tours_opt', true );
			$tour_type            = ! empty( $meta['type'] ) ? $meta['type'] : '';
			$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
			$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
			$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
			$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;

			// People number
			$total_people         = $adults + $children + $infant;
			$total_people_booking = $adults + $children;
			// Tour date
//			$tour_date    = ! empty( $_POST['check-in-out-date'] ) ? sanitize_text_field( $_POST['check-in-out-date'] ) : '';
//			$tour_time    = isset( $_POST['check-in-time'] ) ? sanitize_text_field( $_POST['check-in-time'] ) : null;
//			$make_deposit = ! empty( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : false;

			if ( $tour_type == 'fixed' && function_exists( 'is_tf_pro' ) && ! is_tf_pro() ) {
				$response['errors'][] = __( 'Fixed Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
			}

			$tf_tour_query_orders = wc_get_orders( array(
				'limit'       => - 1,
				'type'        => 'shop_order',
				'status'      => array( 'wc-completed' ),
				'_order_type' => 'tour'
			) );

			if ( $tour_type == 'fixed' ) {
				if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
					$tf_tour_fixed_avail   = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $meta['fixed_availability'] );
					$tf_tour_fixed_date    = unserialize( $tf_tour_fixed_avail );
					$start_date            = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
					$end_date              = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
					$min_people            = ! empty( $tf_tour_fixed_date['min_seat'] ) ? $tf_tour_fixed_date['min_seat'] : '';
					$max_people            = ! empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
					$tf_tour_booking_limit = ! empty( $tf_tour_fixed_date['max_capacity'] ) ? $tf_tour_fixed_date['max_capacity'] : 0;
				} else {
					$start_date            = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
					$end_date              = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
					$min_people            = ! empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
					$max_people            = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
					$tf_tour_booking_limit = ! empty( $meta['fixed_availability']['max_capacity'] ) ? $meta['fixed_availability']['max_capacity'] : 0;
				}


				// Fixed tour maximum capacity limit

				if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $start_date ) && ! empty( $end_date ) ) {

					$tf_total_adults    = 0;
					$tf_total_childrens = 0;
					foreach ( $tf_tour_query_orders as $order ) {
						foreach ( $order->get_items() as $item_key => $item_values ) {
							$order_type = $item_values->get_meta( '_order_type', true );
							if ( "tour" == $order_type ) {
								$_tour_id     = $item_values->get_meta( '_tour_id', true );
								$tf_tour_date = ! empty( $item_values->get_meta( 'Tour Date', true ) ) ? $item_values->get_meta( 'Tour Date', true ) : '';
								list( $tf_booking_start, $tf_booking_end ) = explode( " - ", $tf_tour_date );
								if ( ! empty( $_tour_id ) && $_tour_id == $tour_id && ! empty( $tf_booking_start ) && $start_date == $tf_booking_start && ! empty( $tf_booking_end ) && $end_date == $tf_booking_end ) {
									$book_adult = ! empty( wc_get_order_item_meta( $item_key, 'Adults', true ) ) ? wc_get_order_item_meta( $item_key, 'Adults', true ) : '';
									if ( ! empty( $book_adult ) ) {
										list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
										$tf_total_adults += $tf_total_adult;
									}

									$book_children = ! empty( wc_get_order_item_meta( $item_key, 'Children', true ) ) ? wc_get_order_item_meta( $item_key, 'Children', true ) : '';
									if ( ! empty( $book_children ) ) {
										list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
										$tf_total_childrens += $tf_total_children;
									}
								}

							}
						}
					}
					$tf_total_people = $tf_total_adults + $tf_total_childrens;

					if ( ! empty( $tf_tour_booking_limit ) ) {
						$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;
						if ( $tf_total_people > 0 && $tf_total_people == $tf_tour_booking_limit ) {
							$response['errors'][] = __( 'Booking limit is Reached this Tour', 'tourfic' );
						}
						if ( $tf_total_people != $tf_tour_booking_limit && $tf_today_limit < $total_people_booking ) {
							$response['errors'][] = sprintf( __( 'Only %1$s Adult/Children are available this Tour', 'tourfic' ), $tf_today_limit );
						}
					}
				}

			} elseif ( $tour_type == 'continuous' ) {

				$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;

				if ( $custom_avail == true ) {

					$pricing_rule     = $meta['custom_pricing_by'];
					$cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
					if ( ! empty( $cont_custom_date ) && gettype( $cont_custom_date ) == "string" ) {
						$tf_tour_conti_avail = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
							return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
						}, $cont_custom_date );
						$cont_custom_date    = unserialize( $tf_tour_conti_avail );
					}

				} elseif ( $custom_avail == false ) {

					$min_people          = ! empty( $meta['cont_min_people'] ) ? $meta['cont_min_people'] : '';
					$max_people          = ! empty( $meta['cont_max_people'] ) ? $meta['cont_max_people'] : '';
					$allowed_times_field = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';

					// Daily Tour Booking Capacity
					$tf_total_adults      = 0;
					$tf_total_childrens   = 0;
					if ( empty( $allowed_times_field ) || $tour_time == null ) {
						$tf_tour_booking_limit = ! empty( $meta['cont_max_capacity'] ) ? $meta['cont_max_capacity'] : 0;
						foreach ( $tf_tour_query_orders as $order ) {
							foreach ( $order->get_items() as $item_key => $item_values ) {
								$order_type = $item_values->get_meta( '_order_type', true );
								if ( "tour" == $order_type ) {
									$_tour_id     = $item_values->get_meta( '_tour_id', true );
									$tf_tour_date = ! empty( $item_values->get_meta( 'Tour Date', true ) ) ? $item_values->get_meta( 'Tour Date', true ) : '';
									$tf_tour_time = ! empty( $item_values->get_meta( 'Tour Time', true ) ) ? $item_values->get_meta( 'Tour Time', true ) : '';
									if ( ! empty( $_tour_id ) && $_tour_id == $tour_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && empty( $tf_tour_time ) ) {
										$book_adult = ! empty( wc_get_order_item_meta( $item_key, 'Adults', true ) ) ? wc_get_order_item_meta( $item_key, 'Adults', true ) : '';
										if ( ! empty( $book_adult ) ) {
											list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
											$tf_total_adults += $tf_total_adult;
										}

										$book_children = ! empty( wc_get_order_item_meta( $item_key, 'Children', true ) ) ? wc_get_order_item_meta( $item_key, 'Children', true ) : '';
										if ( ! empty( $book_children ) ) {
											list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
											$tf_total_childrens += $tf_total_children;
										}
									}
								}

							}
						}
					} else {
						if ( ! empty( $allowed_times_field[ $tour_time ]['time'] ) ) {
							$tour_time_title = $allowed_times_field[ $tour_time ]['time'];
						}

						if ( ! empty( $allowed_times_field[ $tour_time ]['cont_max_capacity'] ) ) {
							$tf_tour_booking_limit = $allowed_times_field[ $tour_time ]['cont_max_capacity'];

							foreach ( $tf_tour_query_orders as $order ) {
								foreach ( $order->get_items() as $item_key => $item_values ) {
									$order_type = $item_values->get_meta( '_order_type', true );
									if ( "tour" == $order_type ) {
										$_tour_id     = $item_values->get_meta( '_tour_id', true );
										$tf_tour_date = ! empty( $item_values->get_meta( 'Tour Date', true ) ) ? $item_values->get_meta( 'Tour Date', true ) : '';
										$tf_tour_time = ! empty( $item_values->get_meta( 'Tour Time', true ) ) ? $item_values->get_meta( 'Tour Time', true ) : '';
										if ( ! empty( $_tour_id ) && $_tour_id == $tour_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && ! empty( $tf_tour_time ) && $tf_tour_time == $tour_time_title ) {
											$book_adult = ! empty( wc_get_order_item_meta( $item_key, 'Adults', true ) ) ? wc_get_order_item_meta( $item_key, 'Adults', true ) : '';
											if ( ! empty( $book_adult ) ) {
												list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
												$tf_total_adults += $tf_total_adult;
											}

											$book_children = ! empty( wc_get_order_item_meta( $item_key, 'Children', true ) ) ? wc_get_order_item_meta( $item_key, 'Children', true ) : '';
											if ( ! empty( $book_children ) ) {
												list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
												$tf_total_childrens += $tf_total_children;
											}
										}
									}

								}
							}
						}
					}
					$tf_total_people = $tf_total_adults + $tf_total_childrens;

					if ( ! empty( $tf_tour_booking_limit ) ) {
						$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;

						if ( $tf_total_people > 0 && $tf_total_people == $tf_tour_booking_limit ) {
							$response['errors'][] = __( 'Booking limit is Reached this Date', 'tourfic' );
						}
						if ( $tf_total_people != $tf_tour_booking_limit && $tf_today_limit < $total_people_booking ) {
							$response['errors'][] = sprintf( __( 'Only %1$s Adult/Children are available this Date', 'tourfic' ), $tf_today_limit );
						}
					}
				}

			}

			/**
			 * If continuous custom availability is selected but pro is not activated
			 *
			 * Show error
			 *
			 * @return
			 */
			if ( $tour_type == 'continuous' && $custom_avail == true && function_exists( 'is_tf_pro' ) && ! is_tf_pro() ) {
				$response['errors'][] = __( 'Custom Continous Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
				$response['status']   = 'error';
				echo wp_json_encode( $response );
				die();

				return;
			}


			if ( $tour_type == 'continuous' ) {
				$start_date = $end_date = $tour_date;
			}

			// Tour extra
			$tour_extra_total     = 0;
			$tour_extra_title_arr = [];
			$tour_extra_meta      = ! empty( $meta['tour-extra'] ) ? $meta['tour-extra'] : '';
			if ( ! empty( $tour_extra_meta ) ) {
				$tours_extra = explode( ',', $_POST['tour_extra'] );
				foreach ( $tours_extra as $extra ) {
					$tour_extra_pricetype = ! empty( $tour_extra_meta[ $extra ]['price_type'] ) ? $tour_extra_meta[ $extra ]['price_type'] : 'fixed';
					if ( $tour_extra_pricetype == "fixed" ) {
						if ( ! empty( $tour_extra_meta[ $extra ]['title'] ) && ! empty( $tour_extra_meta[ $extra ]['price'] ) ) {
							$tour_extra_total       += $tour_extra_meta[ $extra ]['price'];
							$tour_extra_title_arr[] = $tour_extra_meta[ $extra ]['title'] . " (Fixed: " . wc_price( $tour_extra_meta[ $extra ]['price'] ) . ")";
						}
					} else {
						if ( ! empty( $tour_extra_meta[ $extra ]['price'] ) && ! empty( $tour_extra_meta[ $extra ]['title'] ) ) {
							$tour_extra_total       += ( $tour_extra_meta[ $extra ]['price'] * $total_people );
							$tour_extra_title_arr[] = $tour_extra_meta[ $extra ]['title'] . " (Per Person: " . wc_price( $tour_extra_meta[ $extra ]['price'] ) . '*' . $total_people . "=" . wc_price( $tour_extra_meta[ $extra ]['price'] * $total_people ) . ")";
						}
					}
				}
			}

			$tour_extra_title = ! empty( $tour_extra_title_arr ) ? implode( ",", $tour_extra_title_arr ) : '';

			/**
			 * People 0 number validation
			 *
			 */
			if ( $total_people == 0 ) {
				$response['errors'][] = __( 'Please Select Adults/Children/Infant required', 'tourfic' );
			}

			/**
			 * People number validation
			 *
			 */
			if ( $tour_type == 'fixed' ) {

				$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
				$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );

				if ( $total_people < $min_people && $min_people > 0 ) {
					$response['errors'][] = sprintf( __( 'Minimum %1$s required', 'tourfic' ), $min_text );

				} else if ( $total_people > $max_people && $max_people > 0 ) {
					$response['errors'][] = sprintf( __( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

				}

			} elseif ( $tour_type == 'continuous' && $custom_avail == false ) {

				$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
				$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );

				if ( $total_people < $min_people && $min_people > 0 ) {
					$response['errors'][] = sprintf( __( 'Minimum %1$s required', 'tourfic' ), $min_text );

				} else if ( $total_people > $max_people && $max_people > 0 ) {
					$response['errors'][] = sprintf( __( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

				}

			} elseif ( $tour_type == 'continuous' && $custom_avail == true ) {

				foreach ( $cont_custom_date as $item ) {

					// Backend continuous date values
					$back_date_from     = ! empty( $item['date']['from'] ) ? $item['date']['from'] : '';
					$back_date_to       = ! empty( $item['date']['from'] ) ? $item['date']['to'] : '';
					$back_date_from_stt = strtotime( str_replace( '/', '-', $back_date_from ) );
					$back_date_to_stt   = strtotime( str_replace( '/', '-', $back_date_to ) );
					// frontend selected date value
					$front_date = strtotime( str_replace( '/', '-', $tour_date ) );
					// Backend continuous min/max people values
					$min_people = ! empty( $item['min_people'] ) ? $item['min_people'] : '';
					$max_people = ! empty( $item['max_people'] ) ? $item['max_people'] : '';
					$min_text   = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
					$max_text   = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );


					// Compare backend & frontend date values to show specific people number error
					if ( $front_date >= $back_date_from_stt && $front_date <= $back_date_to_stt ) {
						if ( $total_people < $min_people && $min_people > 0 ) {
							$response['errors'][] = sprintf( __( 'Minimum %1$s required for date %2$s - %3$s', 'tourfic' ), $min_text, $back_date_from, $back_date_to );

						}
						if ( $total_people > $max_people && $max_people > 0 ) {
							$response['errors'][] = sprintf( __( 'Maximum %1$s allowed for date %2$s - %3$s', 'tourfic' ), $max_text, $back_date_from, $back_date_to );

						}


						$allowed_times_field = ! empty( $item['allowed_time'] ) ? $item['allowed_time'] : '';

						// Daily Tour Booking Capacity
						$tf_total_adults      = 0;
						$tf_total_childrens   = 0;
						if ( empty( $allowed_times_field ) || $tour_time == null ) {
							$tf_tour_booking_limit = ! empty( $item['max_capacity'] ) ? $item['max_capacity'] : '';
							foreach ( $tf_tour_query_orders as $order ) {
								foreach ( $order->get_items() as $item_key => $item_values ) {
									$order_type = $item_values->get_meta( '_order_type', true );
									if ( "tour" == $order_type ) {
										$_tour_id     = $item_values->get_meta( '_tour_id', true );
										$tf_tour_date = ! empty( $item_values->get_meta( 'Tour Date', true ) ) ? $item_values->get_meta( 'Tour Date', true ) : '';
										$tf_tour_time = ! empty( $item_values->get_meta( 'Tour Time', true ) ) ? $item_values->get_meta( 'Tour Time', true ) : '';
										if ( ! empty( $_tour_id ) && $_tour_id == $tour_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && empty( $tf_tour_time ) ) {
											$book_adult = ! empty( wc_get_order_item_meta( $item_key, 'Adults', true ) ) ? wc_get_order_item_meta( $item_key, 'Adults', true ) : '';
											if ( ! empty( $book_adult ) ) {
												list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
												$tf_total_adults += $tf_total_adult;
											}

											$book_children = ! empty( wc_get_order_item_meta( $item_key, 'Children', true ) ) ? wc_get_order_item_meta( $item_key, 'Children', true ) : '';
											if ( ! empty( $book_children ) ) {
												list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
												$tf_total_childrens += $tf_total_children;
											}
										}
									}

								}
							}
						} else {
							if ( ! empty( $allowed_times_field[ $tour_time ]['time'] ) ) {
								$tour_time_title = $allowed_times_field[ $tour_time ]['time'];
							}

							if ( ! empty( $allowed_times_field[ $tour_time ]['max_capacity'] ) ) {
								$tf_tour_booking_limit = $allowed_times_field[ $tour_time ]['max_capacity'];

								foreach ( $tf_tour_query_orders as $order ) {
									foreach ( $order->get_items() as $item_key => $item_values ) {
										$order_type = $item_values->get_meta( '_order_type', true );
										if ( "tour" == $order_type ) {
											$_tour_id     = $item_values->get_meta( '_tour_id', true );
											$tf_tour_date = ! empty( $item_values->get_meta( 'Tour Date', true ) ) ? $item_values->get_meta( 'Tour Date', true ) : '';
											$tf_tour_time = ! empty( $item_values->get_meta( 'Tour Time', true ) ) ? $item_values->get_meta( 'Tour Time', true ) : '';
											if ( ! empty( $_tour_id ) && $_tour_id == $tour_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && ! empty( $tf_tour_time ) && $tf_tour_time == $tour_time_title ) {
												$book_adult = ! empty( wc_get_order_item_meta( $item_key, 'Adults', true ) ) ? wc_get_order_item_meta( $item_key, 'Adults', true ) : '';
												if ( ! empty( $book_adult ) ) {
													list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
													$tf_total_adults += $tf_total_adult;
												}

												$book_children = ! empty( wc_get_order_item_meta( $item_key, 'Children', true ) ) ? wc_get_order_item_meta( $item_key, 'Children', true ) : '';
												if ( ! empty( $book_children ) ) {
													list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
													$tf_total_childrens += $tf_total_children;
												}
											}
										}

									}
								}
							}
						}
						$tf_total_people = $tf_total_adults + $tf_total_childrens;

						if ( ! empty( $tf_tour_booking_limit ) ) {
							$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;

							if ( $tf_total_people > 0 && $tf_total_people == $tf_tour_booking_limit ) {
								$response['errors'][] = __( 'Booking limit is Reached this Date', 'tourfic' );
							}
							if ( $tf_total_people != $tf_tour_booking_limit && $tf_today_limit < $total_people_booking ) {
								$response['errors'][] = sprintf( __( 'Only %1$s Adult/Children are available this Date', 'tourfic' ), $tf_today_limit );
							}
						}
					}

				}

			}

			/**
			 * Check errors
			 *
			 */
			/* Minimum days to book before departure */
			$min_days_before_book      = ! empty( $meta['min_days_before_book'] ) ? $meta['min_days_before_book'] : '0';
			$min_days_before_book_text = sprintf( _n( '%s day', '%s days', $min_days_before_book, 'tourfic' ), $min_days_before_book );
			$today_stt                 = new DateTime( date( 'Y-m-d', strtotime( date( 'Y-m-d' ) ) ) );
			$tour_date_stt             = new DateTime( date( 'Y-m-d', strtotime( $start_date ) ) );
			$day_difference            = $today_stt->diff( $tour_date_stt )->days;


			if ( $day_difference < $min_days_before_book ) {
				$response['errors'][] = sprintf( __( 'Present date to booking date required minimum %1$s gap', 'tourfic' ), $min_days_before_book_text );
			}
			if ( ! $start_date ) {
				$response['errors'][] = __( 'You must select booking date', 'tourfic' );
			}
			if ( ! $tour_id ) {
				$response['errors'][] = __( 'Unknown Error! Please try again.', 'tourfic' );
			}

			/**
			 * Price by date range
			 *
			 * Tour type continuous and custom availability is true
			 */
			$tf_cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
			if ( ! empty( $tf_cont_custom_date ) && gettype( $tf_cont_custom_date ) == "string" ) {
				$tf_tour_conti_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $tf_cont_custom_date );
				$tf_cont_custom_date       = unserialize( $tf_tour_conti_custom_date );
			}

			$tour = strtotime( $tour_date );
			if ( isset( $custom_avail ) && true == $custom_avail ) {
				$seasional_price = array_values( array_filter( $tf_cont_custom_date, function ( $value ) use ( $tour ) {
					$seasion_start = strtotime( $value['date']['from'] );
					$seasion_end   = strtotime( $value['date']['to'] );

					return $seasion_start <= $tour && $seasion_end >= $tour;
				} ) );
			}


			if ( $tour_type === 'continuous' && ! empty( $tf_cont_custom_date ) && ! empty( $seasional_price ) ) {

				$group_price    = $seasional_price[0]['group_price'];
				$adult_price    = $seasional_price[0]['adult_price'];
				$children_price = $seasional_price[0]['child_price'];
				$infant_price   = $seasional_price[0]['infant_price'];

			} else {

				$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
				$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
				$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
				$infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;

			}

			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type == 'continuous' ) {
				$tf_allowed_times = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';
				if ( ! empty( $tf_allowed_times ) && gettype( $tf_allowed_times ) == "string" ) {
					$tf_tour_conti_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $tf_allowed_times );
					$tf_allowed_times          = unserialize( $tf_tour_conti_custom_date );
				}

				if ( $custom_avail == false && ! empty( $tf_allowed_times ) && empty( $tour_time_title ) ) {
					$response['errors'][] = __( 'Please select time', 'tourfic' );
				}
				if ( $custom_avail == true && ! empty( $seasional_price[0]['allowed_time'] ) && empty( $tour_time_title ) ) {
					$response['errors'][] = __( 'Please select time', 'tourfic' );
				}
			}

			if ( ( ! empty( $custom_avail ) && $custom_avail == true ) || $pricing_rule == 'person' ) {

				if ( ! $disable_adult_price && $adults > 0 && empty( $adult_price ) ) {
					$response['errors'][] = __( 'Adult price is blank!', 'tourfic' );
				}
				if ( ! $disable_child_price && $children > 0 && empty( $children_price ) ) {
					$response['errors'][] = __( 'Childern price is blank!', 'tourfic' );
				}
				if ( ! $disable_infant_price && $infant > 0 && empty( $infant_price ) ) {
					$response['errors'][] = __( 'Infant price is blank!', 'tourfic' );
				}
				if ( $infant > 0 && ! empty( $infant_price ) && ! $adults ) {
					$response['errors'][] = __( 'Infant without adults is not allowed!', 'tourfic' );
				}

			} else if ( ( ! empty( $custom_avail ) && $custom_avail == true ) || $pricing_rule == 'group' ) {

				if ( empty( $group_price ) ) {
					$response['errors'][] = __( 'Group price is blank!', 'tourfic' );
				}

			}

			/**
			 * If no errors then process
			 *
			 * Store custom data in array
			 * Add to cart with custom data
			 */
			if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {

				$tf_tours_data['tf_tours_data']['order_type']     = 'tour';
				$tf_tours_data['tf_tours_data']['post_author']    = $post_author;
				$tf_tours_data['tf_tours_data']['tour_type']      = $tour_type;
				$tf_tours_data['tf_tours_data']['tour_id']        = $tour_id;
				$tf_tours_data['tf_tours_data']['post_permalink'] = get_permalink( $tour_id );

				$tf_tours_data['tf_tours_data']['start_date']       = $start_date;
				$tf_tours_data['tf_tours_data']['end_date']         = $end_date;
				$tf_tours_data['tf_tours_data']['tour_date']        = $tour_date;
				$tf_tours_data['tf_tours_data']['tour_extra_total'] = $tour_extra_total;
				if ( $tour_extra_title ) {
					$tf_tours_data['tf_tours_data']['tour_extra_title'] = $tour_extra_title;
				}
				# Discount informations
				$discount_type    = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
				$discounted_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';

				if ( $tour_type == 'continuous' ) {
					$tf_tours_data['tf_tours_data']['tour_time'] = $tour_time_title;
				}

				# Calculate discounted price
				if ( $discount_type == 'percent' ) {

					$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $discounted_price ), 2 ) ) );
					$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $children_price - ( ( $children_price / 100 ) * $discounted_price ), 2 ) ) );
					$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( $infant_price - ( ( $infant_price / 100 ) * $discounted_price ), 2 ) ) );
					$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $group_price - ( ( $group_price / 100 ) * $discounted_price ), 2 ) ) );

				} elseif ( $discount_type == 'fixed' ) {

					$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $adult_price - $discounted_price ), 2 ) ) );
					$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $children_price - $discounted_price ), 2 ) ) );
					$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $infant_price - $discounted_price ), 2 ) ) );
					$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $group_price - $discounted_price ), 2 ) ) );

				}

				# Set pricing based on pricing rule
				if ( $pricing_rule == 'group' ) {

					$tf_tours_data['tf_tours_data']['price']     = $group_price;
					$tf_tours_data['tf_tours_data']['adults']    = $adults;
					$tf_tours_data['tf_tours_data']['childrens'] = $children;
					$tf_tours_data['tf_tours_data']['infants']   = $infant;

				} else {

					$tf_tours_data['tf_tours_data']['price']     = ( $adult_price * $adults ) + ( $children * $children_price ) + ( $infant * $infant_price );
					$tf_tours_data['tf_tours_data']['adults']    = $adults . " × " . strip_tags( wc_price( $adult_price ) );
					$tf_tours_data['tf_tours_data']['childrens'] = $children . " × " . strip_tags( wc_price( $children_price ) );
					$tf_tours_data['tf_tours_data']['infants']   = $infant . " × " . strip_tags( wc_price( $infant_price ) );
				}

				# Deposit information
				tf_get_deposit_amount( $meta, $tf_tours_data['tf_tours_data']['price'], $deposit_amount, $has_deposit );
				if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && $make_deposit == true ) {
					$tf_tours_data['tf_tours_data']['due']   = $tf_tours_data['tf_tours_data']['price'] - $deposit_amount;
					$tf_tours_data['tf_tours_data']['price'] = $deposit_amount;
				}
				// Add product to cart with the custom cart item data
				WC()->cart->add_to_cart( $tour_id, 1, '0', array(), $tf_tours_data );

				$response['product_id']  = $product_id;
				$response['add_to_cart'] = 'true';
				$response['redirect_to'] = wc_get_checkout_url();

			} else {
				# Show errors
				$response['status'] = 'error';

			}
		}
	}
}

TF_Tour_Backend_Booking::instance();