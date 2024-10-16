<?php

namespace Tourfic\Classes\Hotel;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use \Tourfic\Classes\Hotel\Pricing;
use Tourfic\Classes\Room\Room;
use Tourfic\App\TF_Review;

class Hotel {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		\Tourfic\Classes\Hotel\Hotel_CPT::instance();

		if ( Helper::tf_is_woo_active() ) {
			if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-hotel.php' ) ) {
				require_once TF_INC_PATH . 'functions/woocommerce/wc-hotel.php';
			} else {
				tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-hotel.php' );
			}
		}

		add_action( 'wp_ajax_tf_room_availability', array( $this, 'tf_room_availability_callback' ) );
		add_action( 'wp_ajax_nopriv_tf_room_availability', array( $this, 'tf_room_availability_callback' ) );
		add_action( 'wp_ajax_tf_hotel_airport_service_price', array( $this, 'tf_hotel_airport_service_callback' ) );
		add_action( 'wp_ajax_nopriv_tf_hotel_airport_service_price', array( $this, 'tf_hotel_airport_service_callback' ) );
		add_action( 'wp_ajax_tf_tour_details_qv', array( $this, 'tf_hotel_quickview_callback' ) );
		add_action( 'wp_ajax_nopriv_tf_tour_details_qv', array( $this, 'tf_hotel_quickview_callback' ) );
		add_action( 'wp_ajax_tf_hotel_archive_popup_qv', array( $this, 'tf_hotel_archive_popup_qv_callback' ) );
		add_action( 'wp_ajax_nopriv_tf_hotel_archive_popup_qv', array( $this, 'tf_hotel_archive_popup_qv_callback' ) );
		add_action( 'wp_ajax_tf_hotel_search', array( $this, 'tf_hotel_search_ajax_callback' ) );
		add_action( 'wp_ajax_nopriv_tf_hotel_search', array( $this, 'tf_hotel_search_ajax_callback' ) );
		add_action( 'tf_hotel_features_filter', array( $this, 'tf_hotel_filter_by_features' ), 10, 1 );
		add_action( 'wp_after_insert_post', array( $this, 'tf_hotel_features_assign_taxonomies' ), 100, 3 );
		add_action( 'wp_after_insert_post', array( $this, 'tf_hotel_rooms_assign' ), 100, 3 );
		add_action( 'wp_after_insert_post', array( $this, 'tf_room_assign_to_hotel' ), 100, 3 );
	}

	/**
	 * Ajax hotel room availability
	 *
	 * @author fida
	 */
	function tf_room_availability_callback() {

        // Check nonce security
        if ( ! isset( $_POST['tf_room_avail_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['tf_room_avail_nonce'])), 'check_room_avail_nonce' ) ) {
            return;
        }

        /**
         * Form data
         */
        $hotel_id          = ! empty( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
        $form_adult        = ! empty( $_POST['adult'] ) ? sanitize_text_field( $_POST['adult'] ) : 0;
        $form_child        = ! empty( $_POST['child'] ) ? sanitize_text_field( $_POST['child'] ) : 0;
        $children_ages     = ! empty( $_POST['children_ages'] ) ? sanitize_text_field( $_POST['children_ages'] ) : '';
        $form_check_in_out = ! empty( $_POST['check_in_out'] ) ? sanitize_text_field( $_POST['check_in_out'] ) : '';


        $form_total_person = $form_adult + $form_child;
        if ( $form_check_in_out ) {
            list( $form_start, $form_end ) = explode( ' - ', $form_check_in_out );
        }

        // Custom avail
        $tf_startdate = $form_start;
        $tf_enddate   = $form_end;

        if ( empty( $form_end ) ) {
            $form_end   = gmdate( 'Y/m/d', strtotime( $form_start . " + 1 day" ) );
            $tf_enddate = gmdate( 'Y/m/d', strtotime( $form_start . " + 1 day" ) );
        }
        $form_check_in = $form_start;
        $form_start    = gmdate( 'Y/m/d', strtotime( $form_start . ' +1 day' ) );
        /**
         * Backend data
         */
        $meta  = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
        $rooms = Room::get_hotel_rooms( $hotel_id );
        $locations           = get_the_terms( $hotel_id, 'hotel_location' );
        $first_location_name = ! empty( $locations ) ? $locations[0]->name : '';
        $room_book_by        = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
        $room_book_url       = ! empty( $meta['booking-url'] ) ? $meta['booking-url'] : '';
        $total_room_option_count = Room::get_room_options_count($rooms);

        // start table
        ob_start();
        ?>
        <div class="tf-room-table hotel-room-wrap">
        <div id="tour_room_details_loader">
            <div id="tour-room-details-loader-img">
                <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
            </div>
        </div>
        <?php
        // Single Template Style
        $tf_hotel_layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
        if("single"==$tf_hotel_layout_conditions){
            $tf_hotel_single_template_check = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
        }
        $tf_hotel_global_template_check = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel'] : 'design-1';

        $tf_hotel_selected_check = !empty($tf_hotel_single_template_check) ? $tf_hotel_single_template_check : $tf_hotel_global_template_check;

        $tf_hotel_selected_template_check = $tf_hotel_selected_check;

        if( $tf_hotel_selected_template_check == "design-1" ){
        ?>
        <table class="tf-availability-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th class="description" colspan="4"><?php esc_html_e( 'Room Details', 'tourfic' ); ?></th>
            </tr>
        </thead>
        <?php } elseif($tf_hotel_selected_template_check == "default"){ ?>
        <table class="availability-table" cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th class="description"><?php esc_html_e( 'Room Details', 'tourfic' ); ?></th>
            <?php if ( $total_room_option_count > 0 ) : ?>
                <th class="options"><?php esc_html_e( 'Options', 'tourfic' ); ?></th>
            <?php endif; ?>
            <th class="pax"><?php esc_html_e( 'Pax', 'tourfic' ); ?></th>
            <th class="pricing"><?php esc_html_e( 'Price', 'tourfic' ); ?></th>
            <th class="reserve"><?php esc_html_e( 'Select Rooms', 'tourfic' ); ?></th>
        </tr>
        </thead>
        <?php } ?>
        <tbody>
        <?php
        echo wp_kses_post( ob_get_clean() );
        $error    = $rows = null;
        $has_room = false;

        // generate table rows
        if ( ! empty( $rooms ) ) {
            ob_start();
            foreach ( $rooms as $_room ) {
                $room_id = $_room->ID;
                $room = get_post_meta($room_id, 'tf_room_opt', true);
                // Check if room is enabled
                $enable = ! empty( $room['enable'] ) && boolval( $room['enable'] );

                if ( $enable ) {

                    /*
                    * Backend room options
                    */
                    $footage          = ! empty( $room['footage'] ) ? $room['footage'] : 0;
                    $bed              = ! empty( $room['bed'] ) ? $room['bed'] : 0;
                    $adult_number     = ! empty( $room['adult'] ) ? $room['adult'] : 0;
                    $child_number     = ! empty( $room['child'] ) ? $room['child'] : 0;
                    $pricing_by       = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
                    $multi_by_date_ck = ! empty( $room['price_multi_day'] ) ? ! empty( $room['price_multi_day'] ) : false;
                    $child_age_limit  = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";
                    $room_price       = ! empty( $room['price'] ) ? $room['price'] : 0;
                    $room_adult_price = ! empty( $room['adult_price'] ) ? $room['adult_price'] : 0;
                    $room_child_price = ! empty( $room['child_price'] ) ? $room['child_price'] : 0;
                    $total_person     = $adult_number + $child_number;
                    $price 			  = $pricing_by == '1' ? $room_price : $room_adult_price + $room_child_price;
                    $room_options     = ! empty( $room['room-options'] ) ? $room['room-options'] : [];
                    $form_check_out   = $form_end;

                    // Hotel Room Discount Data
                    $hotel_discount_type = !empty($room["discount_hotel_type"]) ? $room["discount_hotel_type"] : "none";
                    $hotel_discount_amount = !empty($room["discount_hotel_price"]) ? $room["discount_hotel_price"] : 0;
                    $d_room_price = 0;
                    $d_room_adult_price = 0;
                    $d_room_child_price = 0;

                    if($pricing_by == 1) {
                        $d_room_price = Pricing::instance($hotel_id, $room_id)->calculate_discount($room_price);
                        $d_price = $d_room_price;
                    } elseif($pricing_by == 2) {
                        $d_room_adult_price = Pricing::instance($hotel_id, $room_id)->calculate_discount($room_adult_price);
                        $d_room_child_price = Pricing::instance($hotel_id, $room_id)->calculate_discount($room_child_price);
                        $d_price = $d_room_adult_price + $d_room_child_price;
                    }

                    // Check availability by date option
                    $period = new \DatePeriod(
                        new \DateTime( $form_start . ' 00:00' ),
                        new \DateInterval( 'P1D' ),
                        new \DateTime( $tf_enddate . ' 23:59' )
                    );

                    $days = iterator_count( $period );

                    // Check availability by date option
                    $tfperiod = new \DatePeriod(
                        new \DateTime( $tf_startdate . ' 00:00' ),
                        new \DateInterval( 'P1D' ),
                        (new \DateTime( $tf_enddate . ' 23:59' ))
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

					// Check availability by date option for disable date
                    $disable_date_period = new \DatePeriod(
                        new \DateTime( $tf_startdate . ' 00:00' ),
                        new \DateInterval( 'P1D' ),
                        (new \DateTime( $tf_enddate . ' 23:59' ))->modify('-1 day')
                    );

                    $tf_durationdate = [];
                    $tf_is_first = true;
                    foreach ( $disable_date_period as $date ) {
                        if($multi_by_date_ck){
                            if ($tf_is_first) {
                                $tf_is_first = false;
                                continue;
                            }
                        }
                        $tf_durationdate[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
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
                        $avail_date = ! empty( $room['avail_date'] ) ? json_decode($room['avail_date'], true) : [];
                    }

                    if ( ! empty( $order_ids ) && $reduce_num_room == true ) {

                        # Get backend available date range as an array
                        if ( !empty( $avil_by_date ) ) {

                            $order_date_ranges = array();

                            $backend_date_ranges = array();
                            foreach ( $avail_date as $single_date_range ) {

                                if(is_array($single_date_range) && !empty( $single_date_range["availability"] )){
                                    array_push( $backend_date_ranges, array( strtotime( $single_date_range["availability"]["from"] ), strtotime( $single_date_range["availability"]["to"] ) ) );
                                }
                            }
                        }

                        # Convert order ids to array
                        $order_ids = explode( ',', $order_ids );

                        # Run foreach loop through oder ids
                        /* foreach ( $order_ids as $order_id ) {

							# Get Only the completed orders
							$tf_orders_select = array(
								'select' => "post_id,order_details",
								'post_type' => 'hotel',
								'query' => " AND ostatus = 'completed' AND order_id = ".$order_id
							);
							$tf_hotel_book_orders = Helper::tourfic_order_table_data($tf_orders_select);

                            # Get and Loop Over Order Items
                            foreach ( $tf_hotel_book_orders as $item ) {
                                $order_details = json_decode($item['order_details']);
                                $ordered_number_of_room = !empty($order_details->room) ? $order_details->room : 0;

                                if ( $avil_by_date ) {
                                    $order_check_in_date  = strtotime($order_details->check_in);
									$order_check_out_date = strtotime($order_details->check_out);
                                    // if ( ! empty( $avail_durationdate ) && ( in_array( $order_check_out_date, $avail_durationdate ) ) ) {
                                    //     # Total number of room booked
                                    //     $number_orders = $number_orders + $ordered_number_of_room;
                                    // }
									if (!empty($avail_durationdate)) {
										foreach ($avail_durationdate as $available_date) {
											$available_timestamp = strtotime($available_date);
										
											// Reduce room availability 
											if($multi_by_date_ck){
												if (($order_check_in_date) < $available_timestamp && $order_check_out_date >= $available_timestamp) {
													$number_orders += $ordered_number_of_room;
													break;
												}
											} else {
												if (($order_check_in_date) <= $available_timestamp && $order_check_out_date >= $available_timestamp) {
													$number_orders += $ordered_number_of_room;
													break;
												}
											}
										}
									}
									
                                    array_push( $order_date_ranges, array( $order_check_in_date, $order_check_out_date ) );

                                } else {
                                    $order_check_in_date  = strtotime($order_details->check_in);
									$order_check_out_date = strtotime($order_details->check_out);
                                    // if ( ! empty( $avail_durationdate ) && ( in_array( $order_check_out_date, $avail_durationdate ) ) ) {
                                    //     # Total number of room booked
                                    //     $number_orders = $number_orders + $ordered_number_of_room;
                                    // }
									if (!empty($avail_durationdate)) {
										foreach ($avail_durationdate as $available_date) {
											$available_timestamp = strtotime($available_date);
											
											// Reduce room availability
											if($multi_by_date_ck){
												if (($order_check_in_date) < $available_timestamp && $order_check_out_date >= $available_timestamp) {
													$number_orders += $ordered_number_of_room;
													break;
												}
											} else {
												if (($order_check_in_date) <= $available_timestamp && $order_check_out_date >= $available_timestamp) {
													$number_orders += $ordered_number_of_room;
													break;
												}
											}
										}
									}								
                                }
                            }
                        }

                        # Calculate available room number after order
                        $num_room_available = $num_room_available - $number_orders; // Calculate
                        $num_room_available = max( $num_room_available, 0 ); // If negetive value make that 0 */

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

                    if ( $avil_by_date == '1' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

                        if(!$multi_by_date_ck){
                            if ( $tf_startdate && $tf_enddate ) {
                                // Check availability by date option
                                $period = new \DatePeriod(
                                    new \DateTime( $tf_startdate . ' 00:00' ),
                                    new \DateInterval( 'P1D' ),
                                    new \DateTime( $tf_enddate . ' 23:59' )
                                );
                            }
                        }else{
                            if ( $tf_startdate && $tf_enddate ) {
                                $period = new \DatePeriod(
                                    new \DateTime( $tf_startdate . ' 00:00' ),
                                    new \DateInterval( 'P1D' ),
                                    new \DateTime( $tf_enddate . ' 00:00' )
                                );
                            }
                        }

                        // split date range
                        $check_in  = strtotime( $form_start . ' 00:00' );
                        $check_out = strtotime( $form_end . ' 00:00' );
                        $price     = 0;
                        $d_price   = 0;
                        $has_room  = [];

                        // extract price from available room options
                        foreach ( $period as $date ) {

                            $available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
                                if( $date_availability['status'] == 'available' ){
                                    $date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
                                    $date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

                                    return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
                                } else {
                                    return false;
                                }
                            } ) );

                            if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {

                                $room_price      = ! empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $room_price;
                                $adult_price     = ! empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $room_adult_price;
                                $child_price     = ! empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $room['child_price'];
                                $price_by_date   = $pricing_by == '1' ? $room_price : ( ( (int) $adult_price * (int) $form_adult ) + ( (int) $child_price * (int) $form_child ) );
                                $d_price_by_date = 0;
                                $price 			+= $price_by_date;
                                $number_of_rooms = ! empty( $available_rooms[0]['num-room'] ) ? $available_rooms[0]['num-room'] : $room['num-room'];
                                $has_room[]      = 1;

                                if($pricing_by == 1) {
                                    $d_room_price = !empty($room_price) ? Pricing::apply_discount($room_price, $hotel_discount_type, $hotel_discount_amount) : 0;
                                } elseif($pricing_by == 2) {
                                    $d_adult_price = !empty($adult_price) ? Pricing::apply_discount($adult_price, $hotel_discount_type, $hotel_discount_amount) : 0;
                                    $d_child_price = !empty($child_price) ? Pricing::apply_discount($child_price, $hotel_discount_type, $hotel_discount_amount) : 0;
                                }
                                $d_price_by_date = $pricing_by == '1' ? $d_room_price : ( ( $d_adult_price * $form_adult ) + ( $d_child_price * $form_child ) );
                                $d_price += $d_price_by_date;

                            } else {
                                $has_room[] = 0;
                            }
                        }
                        if(!$multi_by_date_ck){
                            $days = $days+1;
                        }

                        // Check if date is provided and within date range
                        if ( ! in_array( 0, $has_room ) ) {
                            Helper::tf_get_deposit_amount( $room, $price, $deposit_amount, $has_deposit, $d_price);
                            if ( $form_adult <= $adult_number ) {
                                if ( !empty($form_child) ){
                                    if($form_child <= $child_number ) {
                                        include TF_TEMPLATE_PART_PATH . 'hotel/hotel-availability-table-row.php';
                                    }
                                }else{
                                    include TF_TEMPLATE_PART_PATH . 'hotel/hotel-availability-table-row.php';
                                }

                            } else {

                                $error = esc_html__( 'No Room Available! Total person number exceeds!', 'tourfic' );
                            }

                        } else {

                            $error = esc_html__( 'No Room Available within this Date Range!', 'tourfic' );

                        }
                    } else {
                        $d_price_by_date = 0;
                        if ( $pricing_by == '1' ) {
                            if($hotel_discount_type == "percent" || $hotel_discount_type == "fixed") {
                                $d_price_by_date = $d_room_price;
                            }
                            $price_by_date = $room_price;
                        } elseif($pricing_by == '2') {
                            if($hotel_discount_type == "percent" || $hotel_discount_type == "fixed") {
                                $d_price_by_date = ( ( $d_room_adult_price * $form_adult ) + ( $d_room_child_price * $form_child ) );
                            }
                            $price_by_date = ( ( $room_adult_price * $form_adult ) + ( $room_child_price * $form_child ) );
                        } elseif($pricing_by == '3') {
                            foreach ( $room_options as $room_option_key => $room_option ){
                                $option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';

                                if ( $option_price_type == 'per_room' ) {
                                    $price_by_date = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
                                } elseif ( $option_price_type == 'per_person' ) {
                                    $option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
                                    $option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;

                                    $price_by_date = ( ( $option_adult_price * $form_adult ) + ( $option_child_price * $form_child ) );
                                }
                            }
                        }

                        if(!$multi_by_date_ck){
                            $days = $days+1;
                        }

                        $price = !empty($room['price_multi_day']) && $room['price_multi_day'] == '1' && !empty( $price_by_date ) ? $price_by_date * $days : $price_by_date * $days;
                        $d_price = !empty($room['price_multi_day']) && $room['price_multi_day'] == '1' && !empty( $price_by_date ) ? $d_price_by_date * $days : $d_price_by_date * $days;

                        Helper::tf_get_deposit_amount( $room, $price, $deposit_amount, $has_deposit, $d_price );

                        /**
                         * filter hotel room with features
                         * @return array
                         * @since 1.6.9
                         * @author Abu Hena
                         */
                        $filtered_features = ! empty( $_POST['features'] ) ? $_POST['features'] : array();
                        $room_features     = ! empty( $room['features'] ) ? $room['features'] : '';
                        if ( ! empty( $room_features ) && is_array( $room_features ) ) {
                            $feature_result = array_intersect( $filtered_features, $room_features );
                        }

                        if ( ! empty( $filtered_features ) ) {
                            if ( $feature_result ) {
                                if ( $form_adult <= $adult_number ) {
                                    if ( !empty($form_child) ){
                                        if($form_child <= $child_number ) {
                                            include TF_TEMPLATE_PART_PATH . 'hotel/hotel-availability-table-row.php';
                                        }
                                    }else{
                                        include TF_TEMPLATE_PART_PATH . 'hotel/hotel-availability-table-row.php';
                                    }

                                } else {

                                    $error = esc_html__( 'No Room Available! Total person number exceeds!', 'tourfic' );

                                }
                            } else {
                                $error = esc_html__( 'No Room Available!', 'tourfic' );
                            }
                            /* feature filter ended here */

                        } else {
                            if ( $form_adult <= $adult_number ) {
                                if ( !empty($form_child) ){
                                    if($form_child <= $child_number ) {
                                        include TF_TEMPLATE_PART_PATH . 'hotel/hotel-availability-table-row.php';
                                    }
                                }else{
                                    include TF_TEMPLATE_PART_PATH . 'hotel/hotel-availability-table-row.php';
                                }

                            } else {

                                $error = esc_html__( 'No Room Available! Total person number exceeds!', 'tourfic' );

                            }
                        }
                    }

                } else {

                    $error = esc_html__( 'No Room Available!', 'tourfic' );

                }
            }

            $rows .= ob_get_clean();

        } else {

            $error = esc_html__( 'No Room Available!', 'tourfic' );

        }

        if ( ! empty( $rows ) ) {

            echo wp_kses( $rows, Helper::tf_custom_wp_kses_allow_tags()) . '</tbody> </table> </div>';

        } else {

            echo '<tr><td colspan="4" style="text-align:center;font-weight:bold;">' . esc_html( $error ) . '</td></tr>';
            ?>
            </tbody>
            </table>
            </div>
            <?php

        }

        wp_die();
    }

	/**
	 * Filter hotels on search result page by checkin checkout dates set by backend
	 *
	 *
	 * @param \DatePeriod $period collection of dates by user input;
	 * @param array $not_found collection of hotels exists
	 * @param array $data user input for sidebar form
	 *
	 * @author devkabir, fida
	 *
	 */
	static function tf_filter_hotel_by_date( $period, array &$not_found, array $data = [] ): void {

		// Form Data
		if ( isset( $data[4] ) && isset( $data[5] ) ) {
			[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $room, $check_in_out ] = $data;
		}

		// Get hotel Room meta options
		$rooms = Room::get_hotel_rooms( get_the_ID() );

		//all rooms meta
		$rooms_meta = [];
		if ( ! empty( $rooms ) ) {
			foreach ( $rooms as $single_room ) {
				$rooms_meta[ $single_room->ID ] = get_post_meta( $single_room->ID, 'tf_room_opt', true );
			}
		}

		// Remove disabled rooms

		if ( ! empty( $rooms_meta ) ):
			$rooms_meta = array_filter( $rooms_meta, function ( $value ) {
				return ! empty( $value ) && empty( $value['enable'] ) ? $value['enable'] : '' != '0';
			} );
		endif;

		// If no room return
		if ( empty( $rooms_meta ) ) {
			return;
		}

		// Set initial room availability status
		$has_hotel = false;

		/**
		 * Adult Number Validation
		 */
		$back_adults   = array_column( $rooms_meta, 'adult' );
		$adult_counter = 0;
		foreach ( $back_adults as $back_adult ) {
			if ( ! empty( $back_adult ) && $back_adult >= $adults ) {
				$adult_counter ++;
			}
		}

		$adult_result = array_filter( $back_adults );

		/**
		 * Child Number Validation
		 */
		$back_childs   = array_column( $rooms_meta, 'child' );
		$child_counter = 0;
		foreach ( $back_childs as $back_child ) {
			if ( ! empty( $back_child ) && $back_child >= $child ) {
				$child_counter ++;
			}
		}

		$childs_result = array_filter( $back_childs );

		/**
		 * Room Number Validation
		 */
		$back_rooms   = array_column( $rooms_meta, 'num-room' );
		$room_counter = 0;
		foreach ( $back_rooms as $back_room ) {
			if ( ! empty( $back_room ) && $back_room >= $room ) {
				$room_counter ++;
			}
		}

		$room_result = array_filter( $back_rooms );

		// If adult and child number validation is true proceed
		if ( ! empty( $adult_result ) && $adult_counter > 0 && ! empty( $childs_result ) && $child_counter > 0 && ! empty( $room_result ) && $room_counter > 0 ) {

			// Check custom date range status of room
			$avil_by_date = array_column( $rooms_meta, 'avil_by_date' );

			// Check if any room available without custom date range
			if ( in_array( 0, $avil_by_date ) || empty( $avil_by_date ) || empty( $avil_by_date[0] ) ) {

				if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
					foreach ( $rooms as $_room ) {
						$room = get_post_meta( $_room->ID, 'tf_room_opt', true );

						if('2'==$room['pricing-by']){
							if ( ! empty( $room['adult_price'] ) ) {
								if ( $startprice <= $room['adult_price'] && $room['adult_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $room['child_price'] ) ) {
								if ( $startprice <= $room['child_price'] && $room['child_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
						if('1'==$room['pricing-by']){
							if ( ! empty( $room['price'] ) ) {
								if ( $startprice <= $room['price'] && $room['price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
                        if($room['pricing-by']== '3'){
	                        $room_options = ! empty( $room['room-options'] ) ? $room['room-options'] : [];
	                        foreach ( $room_options as $room_option_key => $room_option ) {
		                        $option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
		                        if ( $option_price_type == 'per_room' ) {
			                        $room_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
		                        } elseif ( $option_price_type == 'per_person' ) {
			                        $option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
			                        $option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;
		                        }
		                        if ( ! empty( $room_price ) ) {
			                        if ( $startprice <= $room_price && $room_price <= $endprice ) {
				                        $has_hotel = true;
			                        }
		                        }
		                        if ( ! empty( $option_adult_price ) ) {
			                        if ( $startprice <= $option_adult_price && $option_adult_price <= $endprice ) {
				                        $has_hotel = true;
			                        }
		                        }
		                        if ( ! empty( $option_child_price ) ) {
			                        if ( $startprice <= $option_child_price && $option_child_price <= $endprice ) {
				                        $has_hotel = true;
			                        }
		                        }
	                        }
                        }
					}
				}else{
					$has_hotel = true; // Show that hotel
				}

			} else {
				// If all the room has custom date range then filter the rooms by date

				// Get custom date range repeater
				$dates = array_column( $rooms_meta, 'avail_date' );
				// If no date range return
				if ( empty( $dates ) ) {
					return;
				}

				$tf_check_in_date = 0;
				$searching_period = [];
				// Check if any date range match with search form date range and set them on array
				if ( ! empty( $period ) ) {
					foreach ( $period as $datekey => $date ) {
						if ( 0 == $datekey ) {
							$tf_check_in_date = $date->format( 'Y/m/d' );
						}
						$searching_period[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
					}
				}

				// Initial available dates array
				$availability_dates     = [];
				$tf_check_in_date_price = [];
				// Run loop through custom date range repeater and filter out only the dates
				foreach ( $dates as $date ) {
					if ( ! empty( $date ) && gettype( $date ) == "string" ) {
						$date = json_decode( $date, true );
						foreach ( $date as $sdate ) {
							if ( $tf_check_in_date == $sdate['check_in'] ) {
								$tf_check_in_date_price['price']       = $sdate['price'];
								$tf_check_in_date_price['adult_price'] = $sdate['adult_price'];
								$tf_check_in_date_price['child_price'] = $sdate['child_price'];

								$options_count = $sdate['options_count'] ?? 0;
								for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
                                    if ( $sdate[ 'tf_room_option_' . $i ] == '1' && $sdate[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
	                                    $tf_check_in_date_price['tf_option_room_price_' . $i]  = ! empty( $sdate[ 'tf_option_room_price_' . $i ] ) ? $sdate[ 'tf_option_room_price_' . $i ] : 0;
                                    } else if ( $sdate[ 'tf_room_option_' . $i ] == '1' && $sdate[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
	                                    $tf_check_in_date_price['tf_option_adult_price_' . $i] = ! empty( $sdate[ 'tf_option_adult_price_' . $i ] ) ? $sdate[ 'tf_option_adult_price_' . $i ] : 0;
	                                    $tf_check_in_date_price['tf_option_child_price_' . $i] = ! empty( $sdate[ 'tf_option_child_price_' . $i ] ) ? $sdate[ 'tf_option_child_price_' . $i ] : 0;
                                    }
								}
							}
							$availability_dates[ $sdate['check_in'] ] = $sdate['check_in'];
						}
					}
				}

				$tf_common_dates = array_intersect( $availability_dates, $searching_period );

				//Initial matching date array
				$show_hotel = [];

				if ( count( $tf_common_dates ) === count( $searching_period ) ) {
					$show_hotel[] = 1;
				}

				// If any date range matches show hotel
				if ( ! empty( $show_hotel ) && ! in_array( 0, $show_hotel ) ) {
					if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
						foreach ( $rooms as $_room ) {
							$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
							$room_options = ! empty( $room['room-options'] ) ? $room['room-options'] : [];

							if ( ! empty( $tf_check_in_date_price['adult_price'] ) ) {
								if ( $startprice <= $tf_check_in_date_price['adult_price'] && $tf_check_in_date_price['adult_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $tf_check_in_date_price['child_price'] ) ) {
								if ( $startprice <= $tf_check_in_date_price['child_price'] && $tf_check_in_date_price['child_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $tf_check_in_date_price['price'] ) ) {
								if ( $startprice <= $tf_check_in_date_price['price'] && $tf_check_in_date_price['price'] <= $endprice ) {
									$has_hotel = true;
								}
							}

							foreach ( $room_options as $room_option_key => $room_option ) {
								if ( ! empty( $tf_check_in_date_price['tf_option_room_price_'.$room_option_key] ) ) {
									if ( $startprice <= $tf_check_in_date_price['tf_option_room_price_'.$room_option_key] && $tf_check_in_date_price['tf_option_room_price_'.$room_option_key] <= $endprice ) {
										$has_hotel = true;
									}
								}
								if ( ! empty( $tf_check_in_date_price['tf_option_adult_price_'.$room_option_key] ) ) {
									if ( $startprice <= $tf_check_in_date_price['tf_option_adult_price_'.$room_option_key] && $tf_check_in_date_price['tf_option_adult_price_'.$room_option_key] <= $endprice ) {
										$has_hotel = true;
									}
								}
								if ( ! empty( $tf_check_in_date_price['tf_option_child_price_'.$room_option_key] ) ) {
									if ( $startprice <= $tf_check_in_date_price['tf_option_child_price_'.$room_option_key] && $tf_check_in_date_price['tf_option_child_price_'.$room_option_key] <= $endprice ) {
										$has_hotel = true;
									}
								}
							}
						}
					} else {
						$has_hotel = true;
					}
				}
			}

		}

		// If adult and child number validation is true proceed
		if ( ! empty( $adult_result ) && $adult_counter > 0 && empty( $childs_result ) && $child_counter == 0 && ! empty( $room_result ) && $room_counter > 0 ) {

			// Check custom date range status of room
			$avil_by_date = array_column( $rooms_meta, 'avil_by_date' );

			// Check if any room available without custom date range
			if ( in_array( 0, $avil_by_date ) || empty( $avil_by_date ) || empty( $avil_by_date[0] ) ) {

				if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
					foreach ( $rooms as $_room ) {
						$room = get_post_meta( $_room->ID, 'tf_room_opt', true );

						if('2'==$room['pricing-by']){
							if ( ! empty( $room['adult_price'] ) ) {
								if ( $startprice <= $room['adult_price'] && $room['adult_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $room['child_price'] ) ) {
								if ( $startprice <= $room['child_price'] && $room['child_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
						if('1'==$room['pricing-by']){
							if ( ! empty( $room['price'] ) ) {
								if ( $startprice <= $room['price'] && $room['price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
						if($room['pricing-by']== '3'){
							$room_options = ! empty( $room['room-options'] ) ? $room['room-options'] : [];
							foreach ( $room_options as $room_option_key => $room_option ) {
								$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
								if ( $option_price_type == 'per_room' ) {
									$room_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
								} elseif ( $option_price_type == 'per_person' ) {
									$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
									$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;
								}
								if ( ! empty( $room_price ) ) {
									if ( $startprice <= $room_price && $room_price <= $endprice ) {
										$has_hotel = true;
									}
								}
								if ( ! empty( $option_adult_price ) ) {
									if ( $startprice <= $option_adult_price && $option_adult_price <= $endprice ) {
										$has_hotel = true;
									}
								}
								if ( ! empty( $option_child_price ) ) {
									if ( $startprice <= $option_child_price && $option_child_price <= $endprice ) {
										$has_hotel = true;
									}
								}
							}
						}
					}
				}else{
					$has_hotel = true; // Show that hotel
				}

			} else {
				// If all the room has custom date range then filter the rooms by date

				// Get custom date range repeater
				$dates = array_column( $rooms_meta, 'avail_date' );

				// If no date range return
				if ( empty( $dates ) ) {
					return;
				}

				$tf_check_in_date = 0;
				$searching_period = [];
				// Check if any date range match with search form date range and set them on array
				if ( ! empty( $period ) ) {
					foreach ( $period as $datekey => $date ) {
						if ( 0 == $datekey ) {
							$tf_check_in_date = $date->format( 'Y/m/d' );
						}
						$searching_period[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
					}
				}

				// Initial available dates array
				$availability_dates     = [];
				$tf_check_in_date_price = [];
				// Run loop through custom date range repeater and filter out only the dates
				foreach ( $dates as $date ) {
					if ( ! empty( $date ) && gettype( $date ) == "string" ) {
						$date = json_decode( $date, true );
						foreach ( $date as $sdate ) {
							if ( $tf_check_in_date == $sdate['check_in'] ) {
								$tf_check_in_date_price['price']       = $sdate['price'];
								$tf_check_in_date_price['adult_price'] = $sdate['adult_price'];
								$tf_check_in_date_price['child_price'] = $sdate['child_price'];

								$options_count = $sdate['options_count'] ?? 0;
								for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
									if ( $sdate[ 'tf_room_option_' . $i ] == '1' && $sdate[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
										$tf_check_in_date_price['tf_option_room_price_' . $i]  = ! empty( $sdate[ 'tf_option_room_price_' . $i ] ) ? $sdate[ 'tf_option_room_price_' . $i ] : 0;
									} else if ( $sdate[ 'tf_room_option_' . $i ] == '1' && $sdate[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
										$tf_check_in_date_price['tf_option_adult_price_' . $i] = ! empty( $sdate[ 'tf_option_adult_price_' . $i ] ) ? $sdate[ 'tf_option_adult_price_' . $i ] : 0;
										$tf_check_in_date_price['tf_option_child_price_' . $i] = ! empty( $sdate[ 'tf_option_child_price_' . $i ] ) ? $sdate[ 'tf_option_child_price_' . $i ] : 0;
									}
								}
							}
							$availability_dates[ $sdate['check_in'] ] = $sdate['check_in'];
						}
					}
				}

				$tf_common_dates = array_intersect( $availability_dates, $searching_period );

				//Initial matching date array
				$show_hotel = [];

				if ( count( $tf_common_dates ) === count( $searching_period ) ) {
					$show_hotel[] = 1;
				}

				// If any date range matches show hotel
				if ( ! in_array( 0, $show_hotel ) ) {
					if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
						foreach ( $rooms as $_room ) {
							$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
							$room_options = ! empty( $room['room-options'] ) ? $room['room-options'] : [];

							if ( ! empty( $tf_check_in_date_price['adult_price'] ) ) {
								if ( $startprice <= $tf_check_in_date_price['adult_price'] && $tf_check_in_date_price['adult_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $tf_check_in_date_price['child_price'] ) ) {
								if ( $startprice <= $tf_check_in_date_price['child_price'] && $tf_check_in_date_price['child_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $tf_check_in_date_price['price'] ) ) {
								if ( $startprice <= $tf_check_in_date_price['price'] && $tf_check_in_date_price['price'] <= $endprice ) {
									$has_hotel = true;
								}
							}

							foreach ( $room_options as $room_option_key => $room_option ) {
								if ( ! empty( $tf_check_in_date_price['tf_option_room_price_'.$room_option_key] ) ) {
									if ( $startprice <= $tf_check_in_date_price['tf_option_room_price_'.$room_option_key] && $tf_check_in_date_price['tf_option_room_price_'.$room_option_key] <= $endprice ) {
										$has_hotel = true;
									}
								}
								if ( ! empty( $tf_check_in_date_price['tf_option_adult_price_'.$room_option_key] ) ) {
									if ( $startprice <= $tf_check_in_date_price['tf_option_adult_price_'.$room_option_key] && $tf_check_in_date_price['tf_option_adult_price_'.$room_option_key] <= $endprice ) {
										$has_hotel = true;
									}
								}
								if ( ! empty( $tf_check_in_date_price['tf_option_child_price_'.$room_option_key] ) ) {
									if ( $startprice <= $tf_check_in_date_price['tf_option_child_price_'.$room_option_key] && $tf_check_in_date_price['tf_option_child_price_'.$room_option_key] <= $endprice ) {
										$has_hotel = true;
									}
								}
							}
						}
					} else {
						$has_hotel = true;
					}
				}

			}

		}

		// Conditional hotel showing
		if ( $has_hotel ) {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 0,
			);

		} else {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 1,
			);
		}

	}

	/**
	 * Filter hotels on search result page without checkin checkout dates
	 *
	 *
	 * @param \DatePeriod $period collection of dates by user input;
	 * @param array $not_found collection of hotels exists
	 * @param array $data user input for sidebar form
	 *
	 * @author jahid
	 *
	 */
	static function tf_filter_hotel_without_date( $period, array &$not_found, array $data = [] ): void {

		// Form Data
		if ( isset( $data[4] ) && isset( $data[5] ) ) {
			[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $room, $check_in_out ] = $data;
		}

		// Get hotel meta options
		$meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );

		// Get hotel Room meta options
		$rooms = Room::get_hotel_rooms( get_the_ID() );

		//all rooms meta
		$rooms_meta = [];
		if ( ! empty( $rooms ) ) {
			foreach ( $rooms as $_room ) {
				$rooms_meta[ $_room->ID ] = get_post_meta( $_room->ID, 'tf_room_opt', true );
			}
		}

		// Remove disabled rooms
		if ( ! empty( $rooms_meta ) ):
			$rooms_meta = array_filter( $rooms_meta, function ( $value ) {
				return ! empty( $value ) && empty( $value['enable'] ) ? $value['enable'] : '' != '0';
			} );
		endif;

		// If no room return
		if ( empty( $rooms_meta ) ) {
			return;
		}

		// Set initial room availability status
		$has_hotel = false;

		/**
		 * Adult Number Validation
		 */
		$back_adults   = array_column( $rooms_meta, 'adult' );
		$adult_counter = 0;
		foreach ( $back_adults as $back_adult ) {
			if ( ! empty( $back_adult ) && $back_adult >= $adults ) {
				$adult_counter ++;
			}
		}

		$adult_result = array_filter( $back_adults );

		/**
		 * Child Number Validation
		 */
		$back_childs   = array_column( $rooms_meta, 'child' );
		$child_counter = 0;
		foreach ( $back_childs as $back_child ) {
			if ( ! empty( $back_child ) && $back_child >= $child ) {
				$child_counter ++;
			}
		}

		$childs_result = array_filter( $back_childs );

		/**
		 * Room Number Validation
		 */
		$back_rooms   = array_column( $rooms_meta, 'num-room' );
		$room_counter = 0;
		foreach ( $back_rooms as $back_room ) {
			if ( ! empty( $back_room ) && $back_room >= $room ) {
				$room_counter ++;
			}
		}

		$room_result = array_filter( $back_rooms );

		// If adult and child number validation is true proceed
		if ( ! empty( $adult_result ) && $adult_counter > 0 && ! empty( $childs_result ) && $child_counter > 0 && ! empty( $room_result ) && $room_counter > 0 ) {

			if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
				foreach ( $rooms as $_room ) {
					$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
					if ( ! empty( $room['adult_price'] ) ) {
						if ( $startprice <= $room['adult_price'] && $room['adult_price'] <= $endprice ) {
							$has_hotel = true;
						}
					}
					if ( ! empty( $room['child_price'] ) ) {
						if ( $startprice <= $room['child_price'] && $room['child_price'] <= $endprice ) {
							$has_hotel = true;
						}
					}
					if ( ! empty( $room['price'] ) ) {
						if ( $startprice <= $room['price'] && $room['price'] <= $endprice ) {
							$has_hotel = true;
						}
					}

					if($room['pricing-by']== '3'){
						$room_options = ! empty( $room['room-options'] ) ? $room['room-options'] : [];
						foreach ( $room_options as $room_option_key => $room_option ) {
							$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
							if ( $option_price_type == 'per_room' ) {
								$room_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
							} elseif ( $option_price_type == 'per_person' ) {
								$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
								$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;
							}
							if ( ! empty( $room_price ) ) {
								if ( $startprice <= $room_price && $room_price <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $option_adult_price ) ) {
								if ( $startprice <= $option_adult_price && $option_adult_price <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $option_child_price ) ) {
								if ( $startprice <= $option_child_price && $option_child_price <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
					}
				}
			} else {
				$has_hotel = true; // Show that hotel
			}

		}
		if ( ! empty( $adult_result ) && $adult_counter > 0 && empty( $childs_result ) && ! empty( $room_result ) && $room_counter > 0 ) {
			if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
				foreach ( $rooms as $_room ) {
					$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
					if ( ! empty( $room['adult_price'] ) ) {
						if ( $startprice <= $room['adult_price'] && $room['adult_price'] <= $endprice ) {
							$has_hotel = true;
						}
					}
					if ( ! empty( $room['child_price'] ) ) {
						if ( $startprice <= $room['child_price'] && $room['child_price'] <= $endprice ) {
							$has_hotel = true;
						}
					}
					if ( ! empty( $room['price'] ) ) {
						if ( $startprice <= $room['price'] && $room['price'] <= $endprice ) {
							$has_hotel = true;
						}
					}

					if($room['pricing-by']== '3'){
						$room_options = ! empty( $room['room-options'] ) ? $room['room-options'] : [];
						foreach ( $room_options as $room_option_key => $room_option ) {
							$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
							if ( $option_price_type == 'per_room' ) {
								$room_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
							} elseif ( $option_price_type == 'per_person' ) {
								$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
								$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;
							}
							if ( ! empty( $room_price ) ) {
								if ( $startprice <= $room_price && $room_price <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $option_adult_price ) ) {
								if ( $startprice <= $option_adult_price && $option_adult_price <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $option_child_price ) ) {
								if ( $startprice <= $option_child_price && $option_child_price <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
					}
				}
			} else {
				$has_hotel = true; // Show that hotel
			}
		}

		// Conditional hotel showing
		if ( $has_hotel ) {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 0,
			);

		} else {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 1,
			);
		}

	}


	function tf_hotel_airport_service_callback() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
			return;
		}
		$hotel_id        = ! empty( $_POST['id'] ) ? sanitize_key( $_POST['id'] ) : '';
		$meta            = get_post_meta( sanitize_key( $_POST['id'] ), 'tf_hotels_opt', true );
		$airport_service = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';

		if ( 1 == $airport_service ) {

			$room_id       = isset( $_POST['roomid'] ) ? intval( sanitize_text_field( $_POST['roomid'] ) ) : null;
			$option_id     = isset( $_POST['option_id'] ) ? sanitize_text_field( $_POST['option_id'] ) : null;
			$adult         = isset( $_POST['hoteladult'] ) ? intval( sanitize_text_field( $_POST['hoteladult'] ) ) : '0';
			$child         = isset( $_POST['hotelchildren'] ) ? intval( sanitize_text_field( $_POST['hotelchildren'] ) ) : '0';
			$room_selected = isset( $_POST['room'] ) ? intval( sanitize_text_field( $_POST['room'] ) ) : '0';
			$check_in      = isset( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
			$check_out     = isset( $_POST['check_out_date'] ) ? sanitize_text_field( $_POST['check_out_date'] ) : '';
			$deposit       = isset( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : false;

			# Calculate night number
			$day_difference = self::calculate_days( $check_in, $check_out );

			$room_meta = get_post_meta( $room_id, 'tf_room_opt', true );

			$avail_by_date = ! empty( $room_meta['avil_by_date'] ) && $room_meta['avil_by_date'];
			if ( $avail_by_date ) {
				$avail_date = ! empty( $room_meta['avail_date'] ) ? json_decode( $room_meta['avail_date'], true ) : [];
			}

			$pricing_by      = $room_meta['pricing-by'];
			$price_multi_day = ! empty( $room_meta['price_multi_day'] ) ? $room_meta['price_multi_day'] : false;

			// Hotel Discout Data
			$hotel_discount_type   = ! empty( $room_meta["discount_hotel_type"] ) ? $room_meta["discount_hotel_type"] : "none";
			$hotel_discount_amount = ! empty( $room_meta["discount_hotel_price"] ) ? $room_meta["discount_hotel_price"] : 0;
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
						$room_price  = ! empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $room_meta['price'];
						$adult_price = ! empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $room_meta['adult_price'];
						$child_price = ! empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $room_meta['child_price'];

						if ( $hotel_discount_type == "percent" ) {
							$room_price  = !empty($room_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $room_price - ( ( (int) $room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
							$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
							$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
						}
						if ( $hotel_discount_type == "fixed" ) {
							$room_price  = !empty($room_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $room_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
							$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
							$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
						}

						if ( $pricing_by == '1' ) {
							$total_price += $room_price;
						} elseif ( $pricing_by == '2' ) {
							$total_price += ( $adult_price * $adult ) + ( $child_price * $child );
						} elseif ( $pricing_by == '3' ) {
							$data          = $available_rooms[0];
							$options_count = $data['options_count'] ?? 0;
							$unique_id     = ! empty( $room_meta['unique_id'] ) ? $room_meta['unique_id'] : '';

							for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
								$_option_id = $unique_id . '_' . $i;
								if ( $_option_id == $option_id ) {
									if ( $data[ 'tf_room_option_' . $i ] == '1' && $data[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
										$room_price  = ! empty( $data[ 'tf_option_room_price_' . $i ] ) ? $data[ 'tf_option_room_price_' . $i ] : 0;
										$room_price  = Pricing::apply_discount($room_price, $hotel_discount_type, $hotel_discount_amount);
										$total_price += $room_price;
									} else if ( $data[ 'tf_room_option_' . $i ] == '1' && $data[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
										$adult_price = ! empty( $data[ 'tf_option_adult_price_' . $i ] ) ? $data[ 'tf_option_adult_price_' . $i ] : 0;
										$child_price = ! empty( $data[ 'tf_option_child_price_' . $i ] ) ? $data[ 'tf_option_child_price_' . $i ] : 0;
										$adult_price = Pricing::apply_discount($adult_price, $hotel_discount_type, $hotel_discount_amount);
										$child_price = Pricing::apply_discount($child_price, $hotel_discount_type, $hotel_discount_amount);

										$total_price += ( $adult_price * $adult ) + ( $child_price * $child );
									}
								}
							}
						}
					};

				}

				$price_total = $total_price * $room_selected;

			} else {

				if ( $pricing_by == '1' ) {
					$only_room_price = ! empty( $room_meta['price'] ) ? $room_meta['price'] : 0;
					if ( $hotel_discount_type == "percent" ) {
						$only_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $only_room_price - ( ( (int) $only_room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
					}
					if ( $hotel_discount_type == "fixed" ) {
						$only_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $only_room_price - (int) $hotel_discount_amount ), 2 ) );
					}
					$total_price = $only_room_price;

				} elseif ( $pricing_by == '2' ) {
					$adult_price = ! empty( $room_meta['adult_price'] ) ? $room_meta['adult_price'] : 0;
					$child_price = ! empty( $room_meta['child_price'] ) ? $room_meta['child_price'] : 0;

					if ( $hotel_discount_type == "percent" ) {
						$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
						$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					}
					if ( $hotel_discount_type == "fixed" ) {
						$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
						$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
					}

					$adult_price = $adult_price * $adult;
					$child_price = $child_price * $child;
					$total_price = $adult_price + $child_price;

				}  elseif ( $pricing_by == '3' ) {
					$room_options = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];
					$unique_id    = ! empty( $room_meta['unique_id'] ) ? $room_meta['unique_id'] : '';

					if ( ! empty( $room_options ) ) {
						foreach ( $room_options as $room_option_key => $room_option ) {
							$_option_id = $unique_id . '_' . $room_option_key;

							if ( $_option_id == $option_id ) {
								$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
								if ( $option_price_type == 'per_room' ) {
									$total_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
									$total_price = Pricing::apply_discount($total_price, $hotel_discount_type, $hotel_discount_amount);
								} elseif ( $option_price_type == 'per_person' ) {
									$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
									$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;

									$option_adult_price = Pricing::apply_discount($option_adult_price, $hotel_discount_type, $hotel_discount_amount);
									$option_child_price = Pricing::apply_discount($option_child_price, $hotel_discount_type, $hotel_discount_amount);
									$total_price        = ( $option_adult_price * $adult ) + ( $option_child_price * $child );
								}
							}
						}
					}
				}



				# Multiply pricing by night number
				if ( ! empty( $day_difference ) && $price_multi_day == true ) {
					$price_total = $total_price * $room_selected * $day_difference;
				} else {
					$price_total = $total_price * ( $room_selected * $day_difference + 1 );
				}

			}

			if ( $deposit == "true" ) {
				Helper::tf_get_deposit_amount( $room_meta, $price_total, $deposit_amount, $has_deposit );
				if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) {
					$deposit_amount;
				}
			}

			if ( "pickup" == $_POST['service_type'] ) {
				$airport_pickup_price = ! empty( $meta['airport_pickup_price'] ) ? Helper::tf_data_types( $meta['airport_pickup_price'] ) : '';

				if ( "per_person" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$service_adult_fee = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$service_fee       = ( sanitize_key( $_POST['hoteladult'] ) * $service_adult_fee ) + ( sanitize_key( $_POST['hotelchildren'] ) * $service_child_fee );

					if ( sanitize_key( $_POST['hotelchildren'] ) != 0 ) {
						echo "<span>";
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Child Count, %4$s Child Fee, %5$s Service Fee */
						echo sprintf( esc_html__( 'Airport Pickup Fee Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) : %5$s', 'tourfic' ),
							sanitize_key( $_POST['hoteladult'] ),
							wp_kses_post( wc_price( $service_adult_fee ) ),
							sanitize_key( $_POST['hotelchildren'] ),
							wp_kses_post( wc_price( $service_child_fee ) ),
							"<b>" . wp_kses_post( wc_price( $service_fee ) ) . "</b>"
						);
						echo "</span></br>";
					} else {
						echo "<span>";
						/* translators: %1$s Adult Count, %2$s Adult Fee */
						echo sprintf( esc_html__( 'Airport Pickup Fee Adult ( %1$s × %2$s ) :', 'tourfic' ),
								sanitize_key( $_POST['hoteladult'] ),
								wp_kses_post( wc_price( $service_adult_fee ) ),
						     ) . " " . "<b>" . wp_kses_post( wc_price( $service_fee ) ) . "</b>";
						echo "</span></br>";
					}
					if ( $deposit == "true" ) {
						echo "<span>";
						/* translators: %1$s Due Amount, %2$s Service Fee */
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post( wc_price( $price_total - $deposit_amount ) ) . " + " . wp_kses_post( wc_price( $service_fee ) ) . '</b>';
						echo "</span></br>";
						echo "<span>";
						echo esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $deposit_amount ) ) . '</b>';
						echo "</span>";
					} else {
						echo "<span>";
						echo esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $price_total + $service_fee ) ) . '</b>';
						echo "</span>";
					}
				}
				if ( "fixed" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$service_fee = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					/* translators: %s Service Fee */
					echo sprintf( esc_html__( 'Airport Pickup Fee (Fixed): %s', 'tourfic' ),
						wp_kses_post( wc_price( $service_fee ) )
					);
					if ( $deposit == "true" ) {
						echo "</br><span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post( wc_price( $price_total - $deposit_amount ) ) . " + " . wp_kses_post( wc_price( $service_fee ) ) . '</b>';
						echo "</span>";

						/* translators: %s Payable Amount */
						echo '</br><span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $deposit_amount ) ) . '</b></span>';

					} else {
						/* translators: %s Payable Amount */
						echo '</br><span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $price_total + $service_fee ) ) . '</b></span>';
					}
				}
				if ( "free" == $airport_pickup_price['airport_pickup_price_type'] ) {
					echo esc_html__( 'Airport Pickup Fee: Free', 'tourfic' );
					if ( $deposit == "true" ) {
						echo '</br><span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $price_total - $deposit_amount ) ) . '</b></span>';
					} else {

						echo '</br><span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $price_total ) ) . '</b></span>';
					}
				}
			}
			if ( "dropoff" == $_POST['service_type'] ) {
				$airport_dropoff_price = ! empty( $meta['airport_dropoff_price'] ) ? Helper::tf_data_types( $meta['airport_dropoff_price'] ) : '';

				if ( "per_person" == $airport_dropoff_price['airport_pickup_price_type'] ) {
					$service_adult_fee = ! empty( $airport_dropoff_price['airport_service_fee_adult'] ) ? $airport_dropoff_price['airport_service_fee_adult'] : 0;
					$service_child_fee = ! empty( $airport_dropoff_price['airport_service_fee_children'] ) ? $airport_dropoff_price['airport_service_fee_children'] : 0;
					$service_fee       = ( sanitize_key( $_POST['hoteladult'] ) * $service_adult_fee ) + ( sanitize_key( $_POST['hotelchildren'] ) * $service_child_fee );
					if ( sanitize_key( $_POST['hotelchildren'] ) != 0 ) {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Child Count, %4$s Child Fee, %5$s Service Fee */
						echo "<span>" . sprintf( esc_html__( 'Airport Dropoff Fee Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) : ', 'tourfic' ),
								sanitize_key( $_POST['hoteladult'] ),
								wp_kses_post( wc_price( $service_adult_fee ) ),
								sanitize_key( $_POST['hotelchildren'] ),
								wp_kses_post( wc_price( $service_child_fee ) ),
							) . "<b>" . wp_kses_post( wc_price( $service_fee ) ) . "</b>" . "</span></br>";
					} else {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Service Fee */
						echo "<span>" . sprintf( esc_html__( 'Airport Dropoff Fee Adult ( %1$s × %2$s ) : ', 'tourfic' ),
								sanitize_key( $_POST['hoteladult'] ),
								wp_kses_post( wc_price( $service_adult_fee ) ),
							) . "<b>" . wp_kses_post( wc_price( $service_fee ) ) . "</b>" . "</span></br>";
					}
					if ( $deposit == "true" ) {
						/* translators: %1$s Due Amount, %2$s Service Fee */
						echo "<span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post( wc_price( $price_total - $deposit_amount ) ) . " + " . wp_kses_post( wc_price( $service_fee ) ) . '</b>';
						echo "</span></br>";
						echo '<span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $deposit_amount ) ) . '</b></span>';
					} else {
						echo '<span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $price_total + $service_fee ) ) . '</b></span>';
					}
				}
				if ( "fixed" == $airport_dropoff_price['airport_pickup_price_type'] ) {
					$service_fee = ! empty( $airport_dropoff_price['airport_service_fee_fixed'] ) ? $airport_dropoff_price['airport_service_fee_fixed'] : 0;
					/* translators: %s Service Fee */
					echo sprintf( esc_html__( 'Airport Dropoff Fee (Fixed): %s', 'tourfic' ),
						wp_kses_post( wc_price( $service_fee ) )
					);
					if ( $deposit == "true" ) {
						/* translators: %1$s Due Amount, %2$s Service Fee */
						echo "</br><span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post( wc_price( $price_total - $deposit_amount ) ) . " + " . wp_kses_post( wc_price( $service_fee ) ) . '</b>';
						echo "</span>";

						echo '</br><span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $deposit_amount ) ) . '</b></span>';
					} else {
						echo '</br><span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $price_total + $service_fee ) ) . '</b></span>';
					}
				}
				if ( "free" == $airport_dropoff_price['airport_pickup_price_type'] ) {
					echo esc_html__( 'Airport Dropoff Fee: Free', 'tourfic' );
					if ( $deposit == "true" ) {
						echo "</br><span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post( wc_price( $price_total - $deposit_amount ) ) . '</b>';
						echo "</span>";
						/* translators: %s Deposit Amount */
						echo '</br><span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $deposit_amount ) ) . '</b></span>';
					} else {
						echo '</br><span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $price_total ) ) . '</b></span>';
					}
				}
			}
			if ( "both" == $_POST['service_type'] ) {
				$airport_pickup_dropoff_price = ! empty( $meta['airport_pickup_dropoff_price'] ) ? Helper::tf_data_types( $meta['airport_pickup_dropoff_price'] ) : '';

				if ( "per_person" == $airport_pickup_dropoff_price['airport_pickup_price_type'] ) {
					$service_adult_fee = ! empty( $airport_pickup_dropoff_price['airport_service_fee_adult'] ) ? $airport_pickup_dropoff_price['airport_service_fee_adult'] : 0;
					$service_child_fee = ! empty( $airport_pickup_dropoff_price['airport_service_fee_children'] ) ? $airport_pickup_dropoff_price['airport_service_fee_children'] : 0;
					$service_fee       = ( sanitize_key( $_POST['hoteladult'] ) * $service_adult_fee ) + ( sanitize_key( $_POST['hotelchildren'] ) * $service_child_fee );

					if ( sanitize_key( $_POST['hotelchildren'] ) != 0 ) {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Child Count, %4$s Child Fee, %5$s Service Fee */
						echo "<span>" . sprintf( esc_html__( 'Airport Pickup & Dropoff Fee Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) : ', 'tourfic' ),
								sanitize_key( $_POST['hoteladult'] ),
								wp_kses_post( wc_price( $service_adult_fee ) ),
								sanitize_key( $_POST['hotelchildren'] ),
								wp_kses_post( wc_price( $service_child_fee ) ),
							) . "<b>" . wp_kses_post( wc_price( $service_fee ) ) . "</b>" . "</span></br>";
					} else {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Service Fee */
						echo "<span>" . sprintf( esc_html__( 'Airport Pickup & Dropoff Fee Adult ( %1$s × %2$s ) : ', 'tourfic' ),
								sanitize_key( $_POST['hoteladult'] ),
								wp_kses_post( wc_price( $service_adult_fee ) ),
							) . "<b>" . wp_kses_post( wc_price( $service_fee ) ) . "</b>" . "</span></br>";
					}
					if ( $deposit == "true" ) {
						echo "<span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post( wc_price( $price_total - $deposit_amount ) ) . " + " . wp_kses_post( wc_price( $service_fee ) ) . '</b>';
						echo "</span></br>";

						/* translators: %s Total Price */
						echo '<span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $deposit_amount ) ) . '</b></span>';
					} else {
						echo '<span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $price_total + $service_fee ) ) . '</b></span>';
					}

				}
				if ( "fixed" == $airport_pickup_dropoff_price['airport_pickup_price_type'] ) {
					$service_fee = ! empty( $airport_pickup_dropoff_price['airport_service_fee_fixed'] ) ? $airport_pickup_dropoff_price['airport_service_fee_fixed'] : 0;
					/* translators: %s Service Fee */
					echo sprintf( esc_html__( 'Airport Pickup & Dropoff Fee (Fixed): %s', 'tourfic' ),
						wp_kses_post( wc_price( $service_fee ) )
					);

					if ( $deposit == "true" ) {
						echo "</br><span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post( wc_price( $price_total - $deposit_amount ) ) . " + " . wp_kses_post( wc_price( $service_fee ) ) . '</b>';
						echo "</span>";

						/* translators: %s Deposit Amount */
						echo '</br><span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $deposit_amount ) ) . '</b></span>';
					} else {
						echo '</br><span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $price_total + $service_fee ) ) . '</b></span>';
					}
				}
				if ( "free" == $airport_pickup_dropoff_price['airport_pickup_price_type'] ) {
					echo esc_html__( 'Airport Pickup & Dropoff Fee: Free', 'tourfic' );
					if ( $deposit == "true" ) {
						echo "</br><span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post( wc_price( $price_total - $deposit_amount ) ) . '</b>';
						echo "</span>";
						/* translators: %s Deposit Amount */
						echo '</br><span>' . esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post( wc_price( $deposit_amount ) ) . '</b></span>';
					} else {
						echo "</br><span>Total Payable Amount : <b>" . wp_kses_post( wc_price( $price_total ) ) . "</b></span>";
					}
				}
			}

		}
		wp_die();
	}

	/**
	 * Hotel Search form
	 *
	 * Horizontal
	 *
	 * Called in shortcodes
	 */
	static function tf_hotel_search_form_horizontal( $classes, $title, $subtitle, $author, $advanced, $design ) {
		if ( isset( $_GET ) ) {
			$_GET = array_map( 'stripslashes_deep', $_GET );
		}
		// location
		$location = ! empty( $_GET['place'] ) ? esc_html( $_GET['place'] ) : '';
		// Adults
		$adults = ! empty( $_GET['adults'] ) ? esc_html( $_GET['adults'] ) : '';
		// children
		$child = ! empty( $_GET['children'] ) ? esc_html( $_GET['children'] ) : '';
		// room
		$room = ! empty( $_GET['room'] ) ? esc_html( $_GET['room'] ) : '';
		// Check-in & out date
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? esc_html( $_GET['check-in-out-date'] ) : '';

		// date format for users output
		$hotel_date_format_for_users   = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
		$hotel_location_field_required = ! empty( Helper::tfopt( "required_location_hotel_search" ) ) ? Helper::tfopt( "required_location_hotel_search" ) : 0;

		$disable_hotel_child_search = ! empty( Helper::tfopt( 'disable_hotel_child_search' ) ) ? Helper::tfopt( 'disable_hotel_child_search' ) : '';
		if ( ! empty( $design ) && 2 == $design ) {
			?>
            <form class="tf_booking-widget-design-2 tf_hotel-shortcode-design-2 <?php echo esc_attr( $classes ); ?>" id="tf_hotel_aval_check" method="get" autocomplete="off"
                  action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
                <div class="tf_hotel_searching">
                    <div class="tf_form_innerbody">
                        <div class="tf_form_fields">
                            <div class="tf_destination_fields">
                                <label class="tf_label_location">
                                    <span class="tf-label"><?php esc_html_e( 'Location', 'tourfic' ); ?></span>
                                    <div class="tf_form_inners tf_form-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <path d="M8 13.9317L11.2998 10.6318C13.1223 8.80943 13.1223 5.85464 11.2998 4.0322C9.4774 2.20975 6.52261 2.20975 4.70017 4.0322C2.87772 5.85464 2.87772 8.80943 4.70017 10.6318L8 13.9317ZM8 15.8173L3.75736 11.5747C1.41421 9.2315 1.41421 5.43254 3.75736 3.08939C6.10051 0.746245 9.89947 0.746245 12.2427 3.08939C14.5858 5.43254 14.5858 9.2315 12.2427 11.5747L8 15.8173ZM8 8.66536C8.7364 8.66536 9.33333 8.06843 9.33333 7.33203C9.33333 6.59565 8.7364 5.9987 8 5.9987C7.2636 5.9987 6.66667 6.59565 6.66667 7.33203C6.66667 8.06843 7.2636 8.66536 8 8.66536ZM8 9.9987C6.52724 9.9987 5.33333 8.80476 5.33333 7.33203C5.33333 5.85927 6.52724 4.66536 8 4.66536C9.47273 4.66536 10.6667 5.85927 10.6667 7.33203C10.6667 8.80476 9.47273 9.9987 8 9.9987Z"
                                                  fill="#FAEEDD"/>
                                        </svg>
                                        <input type="text" name="place-name" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> id="tf-location" class=""
                                               placeholder="<?php esc_html_e( 'Enter Location', 'tourfic' ); ?>" value="">
                                        <input type="hidden" name="place" id="tf-search-hotel" class="tf-place-input">
                                    </div>
                                </label>
                            </div>

                            <div class="tf_checkin_date">
                                <label class="tf_label_checkin tf_check_inout_dates tf_hotel_check_in_out_date">
                                    <span class="tf-label"><?php esc_html_e( 'Check in', 'tourfic' ); ?></span>
                                    <div class="tf_form_inners">
                                        <div class="tf_checkin_dates">
                                            <span class="date"><?php echo esc_html( gmdate( 'd' ) ); ?></span>
                                            <span class="month">
											<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
											<div class="tf_check_arrow">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
												<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
												</svg>
											</div>
										</span>
                                        </div>

                                    </div>
                                </label>

                                <input type="hidden" name="check-in-out-date" class="tf-check-in-out-date" onkeypress="return false;"
                                       placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_hotel_search' ) ? 'required' : ''; ?>>
                            </div>

                            <div class="tf_checkin_date tf_check_inout_dates tf_hotel_check_in_out_date">
                                <label class="tf_label_checkin">
                                    <span class="tf-label"><?php esc_html_e( 'Check Out', 'tourfic' ); ?></span>
                                    <div class="tf_form_inners">
                                        <div class="tf_checkout_dates">
                                            <span class="date"><?php echo esc_html( gmdate( 'd' ) ); ?></span>
                                            <span class="month">
											<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
											<div class="tf_check_arrow">
												<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
												<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
												</svg>
											</div>
										</span>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="tf_guest_info tf_selectperson-wrap">
                                <label class="tf_label_checkin tf_input-inner">
                                    <span class="tf-label"><?php esc_html_e( 'Guests & rooms', 'tourfic' ); ?></span>
                                    <div class="tf_form_inners">
                                        <div class="tf_guest_calculation">
                                            <div class="tf_guest_number">
                                                <span class="guest"><?php esc_html_e( '1', 'tourfic' ); ?></span>
                                                <span class="label"><?php esc_html_e( 'Guest', 'tourfic' ); ?></span>
                                            </div>
                                            <div class="tf_guest_number">
                                                <span class="room"><?php esc_html_e( '1', 'tourfic' ); ?></span>
                                                <span class="label"><?php esc_html_e( 'Room', 'tourfic' ); ?></span>
                                            </div>
                                        </div>
                                        <div class="tf_check_arrow">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
                                            </svg>
                                        </div>
                                    </div>
                                </label>

                                <div class="tf_acrselection-wrap">
                                    <div class="tf_acrselection-inner">
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13094)">
                                                            <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13094">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <input type="tel" class="adults-style2" name="adults" id="adults" min="1" value="1" readonly>
                                                <div class="acr-inc">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13100)">
                                                            <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13100">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec child-dec">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13094)">
                                                            <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13094">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <input type="tel" name="children" class="childs-style2" id="children" min="0" value="0" readonly>
                                                <div class="acr-inc child-inc">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13100)">
                                                            <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13100">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tf_acrselection">
                                            <div class="acr-label"><?php esc_html_e( 'Rooms', 'tourfic' ); ?></div>
                                            <div class="acr-select">
                                                <div class="acr-dec">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13094)">
                                                            <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13094">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <input type="tel" name="room" class="rooms-style2" id="room" min="1" value="1" readonly>
                                                <div class="acr-inc">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                        <g clip-path="url(#clip0_3229_13100)">
                                                            <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                        </g>
                                                        <defs>
                                                            <clipPath id="clip0_3229_13100">
                                                                <rect width="20" height="20" fill="white"/>
                                                            </clipPath>
                                                        </defs>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Children age input field based on children number -->
									<?php

									$children_age        = Helper::tfopt( 'children_age_limit' );
									$children_age_status = Helper::tfopt( 'enable_child_age_limit' );
									if ( ! empty( $children_age_status ) && $children_age_status == "1" ) {
										?>
                                        <div class="tf-children-age-fields">
                                            <div class="tf-children-age" id="tf-age-field-0" style="display:none">
                                                <label for="children-age"><?php esc_html__( 'Children 0 Age:', 'tourfic' ) ?></label>
                                                <select>
													<?php for ( $age = 0; $age <= $children_age; $age ++ ) {
														?>
                                                        <option value="<?php echo esc_attr( $age ); ?>"><?php echo esc_attr( $age ); ?></option>
													<?php } ?>
                                                </select>
                                            </div>
                                        </div>
									<?php } ?>
                                </div>

                            </div>
                        </div>
                        <div class="tf_availability_checker_box">
                            <input type="hidden" name="type" value="tf_hotel" class="tf-post-type"/>
							<?php
							if ( $author ) { ?>
                                <input type="hidden" name="tf-author" value="<?php echo esc_attr( $author ); ?>" class="tf-post-type"/>
							<?php } ?>
                            <button><?php echo esc_html_e( "Check availability", "tourfic" ); ?></button>
                        </div>
                    </div>
                </div>

            </form>
            <script>
                (function ($) {
                    $(document).ready(function () {

                        // flatpickr locale first day of Week
						<?php Helper::tf_flatpickr_locale( "root" ); ?>

                        $(".tf_check_inout_dates").on("click", function () {
                            $(".tf-check-in-out-date").trigger("click");
                        });
                        $(".tf-check-in-out-date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            dateFormat: "Y/m/d",
                            minDate: "today",

                            // flatpickr locale
							<?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                dateSetToFields(selectedDates, instance);
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                dateSetToFields(selectedDates, instance);
                            }
                        });

                        function dateSetToFields(selectedDates, instance) {
                            if (selectedDates.length === 2) {
                                const monthNames = [
                                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                ];
                                if (selectedDates[0]) {
                                    const startDate = selectedDates[0];
                                    $(".tf_hotel_check_in_out_date .tf_checkin_dates span.date").html(startDate.getDate());
                                    $(".tf_hotel_check_in_out_date .tf_checkin_dates span.month span").html(monthNames[startDate.getMonth()]);
                                }
                                if (selectedDates[1]) {
                                    const endDate = selectedDates[1];
                                    $(".tf_hotel_check_in_out_date .tf_checkout_dates span.date").html(endDate.getDate());
                                    $(".tf_hotel_check_in_out_date .tf_checkout_dates span.month span").html(monthNames[endDate.getMonth()]);
                                }
                            }
                        }

                    });
                })(jQuery);
            </script>
			<?php
		} else { ?>
            <form class="tf_booking-widget <?php echo esc_attr( $classes ); ?>" id="tf_hotel_aval_check" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">
                <div class="tf_homepage-booking">
					<?php if ( Helper::tfopt( 'hide_hotel_location_search' ) != 1 || Helper::tfopt( 'required_location_hotel_search' ) ): ?>
                        <div class="tf_destination-wrap">
                            <div class="tf_input-inner">
                                <div class="tf_form-row">
                                    <label class="tf_label-row">
                                        <span class="tf-label"><?php esc_html_e( 'Location', 'tourfic' ); ?>:</span>
                                        <div class="tf_form-inner">
                                            <div class="tf-search-form-field-icon">
                                                <i class="fas fa-search"></i>
                                            </div>
											<?php
											if ( ( empty( $advanced ) || ! empty( $advanced ) ) && "enabled" != $advanced ) { ?>
                                                <input type="text" name="place-name" <?php echo $hotel_location_field_required != 1 ? '' : 'required'; ?> id="tf-location" class=""
                                                       placeholder="<?php esc_html_e( 'Enter Location', 'tourfic' ); ?>" value="">
                                                <input type="hidden" name="place" id="tf-search-hotel" class="tf-place-input">
											<?php }
											if ( ! empty( $advanced ) && "enabled" == $advanced ) { ?>
                                                <input type="text" name="place-name" <?php echo $hotel_location_field_required != 1 ? '' : 'required'; ?> id="tf-destination-adv"
                                                       class="tf-advance-destination tf-preview-destination" placeholder="<?php esc_html_e( 'Enter Location', 'tourfic' ); ?>">
                                                <input type="hidden" name="place" id="tf-place-destination" placeholder="<?php esc_html_e( 'Enter Location', 'tourfic' ); ?>">
                                                <div class="tf-hotel-locations tf-hotel-results">
                                                    <ul id="ui-id-1">
														<?php
														$tf_hotel_location = get_terms( array(
															'taxonomy'     => 'hotel_location',
															'orderby'      => 'title',
															'order'        => 'ASC',
															'hierarchical' => 0,
														) );
														if ( ! empty( $tf_hotel_location ) ) {
															foreach ( $tf_hotel_location as $term ) {
																if ( ! empty( $term->name ) ) {
																	?>
                                                                    <li data-name="<?php echo esc_attr( $term->name ); ?>" data-slug="<?php echo esc_attr( $term->slug ); ?>"><i
                                                                                class="fa fa-map-marker"></i><?php echo esc_html( $term->name ); ?></li>
																<?php }
															}
														} ?>
                                                    </ul>
                                                </div>
											<?php } ?>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
					<?php endif; ?>

                    <div class="tf_selectperson-wrap">
                        <div class="tf_input-inner">
                        <span class="tf_person-icon tf-search-form-field-icon">
                            <i class="fas fa-user"></i>
                        </span>
                            <div class="adults-text"><?php echo esc_html__( '1 Adults', 'tourfic' ); ?></div>
							<?php if ( empty( $disable_hotel_child_search ) ) : ?>
                                <div class="person-sep"></div>
                                <div class="child-text"><?php echo esc_html__( '0 Children', 'tourfic' ); ?></div>
							<?php endif; ?>
                            <div class="person-sep"></div>
                            <div class="room-text"><?php echo esc_html__( '1 Room', 'tourfic' ); ?></div>
                        </div>

                        <div class="tf_acrselection-wrap">
                            <div class="tf_acrselection-inner">
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php esc_html_e( 'Adults', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="adults" id="adults" min="1" value="1" readonly>
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
								<?php
								$children_age        = Helper::tfopt( 'children_age_limit' );
								$children_age_status = Helper::tfopt( 'enable_child_age_limit' );
								if ( empty( $disable_hotel_child_search ) ) : ?>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php esc_html_e( 'Children', 'tourfic' ); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec <?php echo ! empty( $children_age_status ) && ! empty( $children_age_status ) ? esc_attr( 'child-dec' ) : ''; ?>">-</div>
                                            <input type="number" name="children" id="children" min="0" value="0">
                                            <div class="acr-inc <?php echo ! empty( $children_age_status ) && ! empty( $children_age_status ) ? esc_attr( 'child-inc' ) : ''; ?>">+</div>
                                        </div>
                                    </div>
								<?php endif; ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php esc_html_e( 'Rooms', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="room" id="room" min="1" value="1">
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
                            </div>
							<?php
							if ( ! empty( $children_age_status ) && $children_age_status == "1" ) {
								?>
                                <div class="tf-children-age-fields">
                                    <div class="tf-children-age" id="tf-age-field-0" style="display:none">
                                        <label for="children-age"><?php esc_html__( 'Children 0 Age:', 'tourfic' ) ?></label>
                                        <select>
											<?php for ( $age = 0; $age <= $children_age; $age ++ ) {
												?>
                                                <option value="<?php echo esc_attr( $age ); ?>"><?php echo esc_attr( $age ); ?></option>
											<?php } ?>
                                        </select>
                                    </div>
                                </div>
							<?php } ?>

                        </div>
                    </div>

                    <div class="tf_selectdate-wrap">
                        <div class="tf_input-inner">
                            <div class="tf_form-row">
                                <label class="tf_label-row">
                                    <span class="tf-label"><?php esc_html_e( 'Check-in & Check-out date', 'tourfic' ); ?></span>
                                    <div class="tf_form-inner">
                                        <div class="tf-search-form-field-icon">
                                            <i class="far fa-calendar-alt"></i>
                                        </div>
                                        <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                               placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_hotel_search' ) ? 'required' : ''; ?>>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

					<?php if ( ! empty( $advanced ) && "enabled" == $advanced ) { ?>
                        <div class="tf_selectdate-wrap tf_more_info_selections">
                            <div class="tf_input-inner">
                                <label class="tf_label-row" style="width: 100%;">
                                    <span class="tf-label"><?php esc_html_e( 'More', 'tourfic' ); ?></span>
                                    <span style="text-decoration: none; display: block; cursor: pointer;"><?php esc_html_e( 'Filter', 'tourfic' ); ?>  <i class="fas fa-angle-down"></i></span>
                                </label>
                            </div>
                            <div class="tf-more-info">
                                <h3><?php esc_html_e( 'Filter Price', 'tourfic' ); ?></h3>
                                <div class="tf-filter-price-range">
                                    <div class="tf-hotel-filter-range"></div>
                                </div>

                                <h3 style="margin-top: 20px"><?php esc_html_e( 'Hotel Features', 'tourfic' ); ?></h3>
								<?php
								$tf_hotelfeature = get_terms( array(
									'taxonomy'     => 'hotel_feature',
									'orderby'      => 'title',
									'order'        => 'ASC',
									'hide_empty'   => true,
									'hierarchical' => 0,
								) );
								if ( $tf_hotelfeature ) : ?>
                                    <div class="tf-hotel-features" style="overflow: hidden">
										<?php foreach ( $tf_hotelfeature as $term ) : ?>
                                            <div class="form-group form-check">
                                                <input type="checkbox" name="features[]" class="form-check-input" value="<?php echo esc_html( $term->slug ); ?>" id="<?php echo esc_html( $term->slug ); ?>">
                                                <label class="form-check-label" for="<?php echo esc_html( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></label>
                                            </div>
										<?php endforeach; ?>
                                    </div>
								<?php endif; ?>

                                <h3 style="margin-top: 20px"><?php esc_html_e( 'Hotel Types', 'tourfic' ); ?></h3>
								<?php
								$tf_hoteltype = get_terms( array(
									'taxonomy'     => 'hotel_type',
									'orderby'      => 'title',
									'order'        => 'ASC',
									'hide_empty'   => true,
									'hierarchical' => 0,
								) );
								if ( $tf_hoteltype ) : ?>
                                    <div class="tf-hotel-types" style="overflow: hidden">
										<?php foreach ( $tf_hoteltype as $term ) : ?>
                                            <div class="form-group form-check">
                                                <input type="checkbox" name="types[]" class="form-check-input" value="<?php echo esc_attr( $term->slug ); ?>" id="<?php echo esc_attr( $term->slug ); ?>">
                                                <label class="form-check-label" for="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></label>
                                            </div>
										<?php endforeach; ?>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
					<?php } ?>

                    <div class="tf_submit-wrap">
                        <input type="hidden" name="type" value="tf_hotel" class="tf-post-type"/>
						<?php
						if ( $author ) { ?>
                            <input type="hidden" name="tf-author" value="<?php echo esc_attr( $author ); ?>" class="tf-post-type"/>
						<?php } ?>
                        <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( apply_filters("tf_hotel_search_form_submit_button_text", 'Search' ), 'tourfic' ); ?></button>
                    </div>

                </div>

            </form>

            <script>
                (function ($) {
                    $(document).ready(function () {

                        // flatpickr First Day of Week
						<?php Helper::tf_flatpickr_locale( 'root' ); ?>

                        $("#tf_hotel_aval_check #check-in-out-date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            dateFormat: "Y/m/d",
                            altInput: true,
                            altFormat: '<?php echo esc_html( $hotel_date_format_for_users ); ?>',
                            minDate: "today",

                            // flatpickr locale
							<?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                            }
                        });

                    });
                })(jQuery);
            </script>
			<?php
		}
	}

	/**
	 * Single Hotel Sidebar Booking Form
	 */
	static function tf_hotel_sidebar_booking_form( $b_check_in = '', $b_check_out = '' ) {

		if ( isset( $_GET ) ) {
			$_GET = array_map( 'stripslashes_deep', $_GET );
		}

		//get children ages
		$children_ages = isset( $_GET['children_ages'] ) ? $_GET['children_ages'] : '';
		// Adults
		$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		// children
		$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		// Check-in & out date
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';
		//get features
		$features = ! empty( $_GET['features'] ) ? sanitize_text_field( $_GET['features'] ) : '';

		// date format for users output
		$hotel_date_format_for_users = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";


		/**
		 * Get each hotel room's disabled date from the available dates
		 * @since 2.9.7
		 * @author Abu Hena
		 */
		$meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
		// Room Details
		$rooms = Room::get_hotel_rooms( get_the_ID() );

		$total_dis_dates = [];
		$maxadults       = [];
		$maxchilds       = [];

		if ( ! empty( $rooms ) ):
			foreach ( $rooms as $_room ) {
				$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
				if ( ! empty( $room['repeat_by_date'] ) ) {
					$disabled_dates = $room['repeat_by_date'];
					//iterate all the available disabled dates
					if ( ! empty( $disabled_dates ) && is_array( $disabled_dates ) ) {
						foreach ( $disabled_dates as $date ) {
							$dateArr           = explode( ',', ! empty( $date['disabled_date'] ) ? $date['disabled_date'] : '' );
							$dateArr           = sprintf( '"%s"', implode( '","', $dateArr ) );
							$total_dis_dates[] = $dateArr;
						}
					}
				}

				if ( ! empty( $room['avail_date'] ) ) {
					$avail_dates = json_decode( $room['avail_date'], true );
					if ( ! empty( $avail_dates ) ) {
						foreach ( $avail_dates as $date ) {
							if ( $date['status'] === 'unavailable' ) {
								$total_dis_dates[] = $date['check_in'];
							}
						}
					}
				}
				// Adult Number Store
				if ( ! empty( $room['adult'] ) ) {
					$maxadults[] = $room['adult'];
				}

				// Child Number Store
				if ( ! empty( $room['child'] ) ) {
					$maxchilds[] = $room['child'];
				}

			}
			//merge the new arrays
			array_merge( $total_dis_dates, $total_dis_dates );
			$total_dis_dates = implode( ',', $total_dis_dates );
		endif;
		$total_dis_dates = is_array( $total_dis_dates ) && empty( $total_dis_dates ) ? '' : $total_dis_dates;

		// Maximum Adults Number
		if ( ! empty( $maxadults ) ) {
			$max_adults_numbers = max( $maxadults );
		} else {
			$max_adults_numbers = 1;
		}

		// Maximum Child Number
		if ( ! empty( $maxchilds ) ) {
			$max_childs_numbers = max( $maxchilds );
		} else {
			$max_childs_numbers = 0;
		}

		?>
		<?php
		// Single Template Style
		$tf_hotel_layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
		if ( "single" == $tf_hotel_layout_conditions ) {
			$tf_hotel_single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
		}
		$tf_hotel_global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';

		$tf_hotel_selected_check = ! empty( $tf_hotel_single_template ) ? $tf_hotel_single_template : $tf_hotel_global_template;

		$tf_hotel_selected_template = $tf_hotel_selected_check;

		$tf_hotel_book_avaibality_button_text = ! empty( Helper::tfopt( 'hotel_booking_check_button_text' ) ) ? stripslashes( sanitize_text_field( Helper::tfopt( 'hotel_booking_check_button_text' ) ) ) : "Booking Availability";
		$hotel_location_field_required        = ! empty( Helper::tfopt( "required_location_hotel_search" ) ) ? Helper::tfopt( "required_location_hotel_search" ) : 1;

		if ( $tf_hotel_selected_template == "design-1" ) {
			?>
            <form id="tf-single-hotel-avail" class="widget tf-hotel-booking-sidebar tf-booking-form" method="get" autocomplete="off">

				<?php wp_nonce_field( 'check_room_avail_nonce', 'tf_room_avail_nonce' ); ?>
                <div class="tf-booking-person">
                    <div class="tf-field-group tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <i class="fa-regular fa-user"></i>
								<?php esc_html_e( 'Adults', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '1'; ?>">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
                    <div class="tf-field-group tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <i class="fa-solid fa-child"></i>
								<?php esc_html_e( 'Children', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tf_booking-dates">
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner tf-field-group">
                                <i class="far fa-calendar-alt"></i>
                                <input type="text" name="check-in-out-date" id="check-in-out-date" class="tf-field" onkeypress="return false;"
                                       placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr( $check_in_out ) . '"' : '' ?> required
                                       style="width: 100% !important;">
                            </div>
                        </label>
                    </div>
                </div>

                <div class="tf_form-row">
					<?php
					$ptype = isset( $_GET['type'] ) ? esc_attr( $_GET['type'] ) : get_post_type();
					?>
                    <input type="hidden" name="type" value="<?php echo esc_attr( $ptype ); ?>" class="tf-post-type"/>
                    <input type="hidden" name="post_id" value="<?php echo esc_attr( get_the_ID() ); ?>"/>
                    <input type="hidden" name="children_ages" value="<?php echo esc_attr( $children_ages ); ?>"/>

                    <div class="tf-btn">
                        <button class="tf-btn-normal btn-primary tf-submit"
                                type="submit"><?php echo esc_html( $tf_hotel_book_avaibality_button_text ); ?></button>
                    </div>


                </div>

            </form>
		<?php } elseif ( $tf_hotel_selected_template == "design-2" ) { ?>

            <form id="tf-single-hotel-avail" class="tf-booking-form" method="get" autocomplete="off">
				<?php wp_nonce_field( 'check_room_avail_nonce', 'tf_room_avail_nonce' ); ?>
                <div class="tf-booking-form-fields">
                    <div class="tf-booking-form-checkin">
                        <span class="tf-booking-form-title"><?php esc_html_e( "Check in", "tourfic" ); ?></span>
                        <div class="tf-booking-date-wrap">
                            <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                            <span class="tf-booking-month">
						<span>
							<?php echo esc_html( gmdate( 'M' ) ); ?>
						</span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
                        </div>
                    </div>
                    <div class="tf-booking-form-checkout">
                        <span class="tf-booking-form-title"><?php esc_html_e( "Check out", "tourfic" ); ?></span>
                        <div class="tf-booking-date-wrap">
                            <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                            <span class="tf-booking-month">
						<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
                        </div>
                        <input type="text" name="check-in-out-date" class="tf-check-in-out-date" onkeypress="return false;"
                               placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr( $check_in_out ) . '"' : '' ?> required>

                    </div>
                    <div class="tf-booking-form-guest-and-room">
                        <div class="tf-booking-form-guest-and-room-inner">
                            <span class="tf-booking-form-title"><?php esc_html_e( "Guests", "tourfic" ); ?></span>
                            <div class="tf-booking-guest-and-room-wrap">
                                <span class="tf-guest tf-booking-date"><?php esc_html_e( "01", "tourfic" ); ?></span>
                                <span class="tf-booking-month">
							<span><?php esc_html_e( "Guest", "tourfic" ); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
							<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
							</svg>
						</span>
                            </div>
                        </div>


                        <div class="tf_acrselection-wrap">
                            <div class="tf_acrselection-inner">
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php esc_html_e( "Adults", "tourfic" ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <input type="tel" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '1'; ?>" readonly>
                                        <div class="acr-inc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php esc_html_e( "Children", "tourfic" ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <input type="tel" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>" readonly>
                                        <div class="acr-inc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tf-booking-form-submit">
					<?php
					$ptype = isset( $_GET['type'] ) ? esc_attr( $_GET['type'] ) : get_post_type();
					?>
                    <input type="hidden" name="type" value="<?php echo esc_html( $ptype ); ?>" class="tf-post-type"/>
                    <input type="hidden" name="post_id" value="<?php echo esc_html( get_the_ID() ); ?>"/>
                    <input type="hidden" name="children_ages" value="<?php echo esc_html( $children_ages ); ?>"/>
                    <button type="submit" class="btn-primary tf-submit"><?php echo esc_html( $tf_hotel_book_avaibality_button_text ); ?></button>
                </div>
            </form>

            <script>
                (function ($) {
                    $(document).ready(function () {
                        var selectedTemplate = '<?php echo esc_html( $tf_hotel_selected_template ); ?>';
                        var month = 1;

                        if ($(window).width() >= 1240) {
                            month = 2;
                        }

                        // flatpickr locale first day of Week
						<?php Helper::tf_flatpickr_locale( "root" ); ?>

                        $(".tf-template-3 .tf-booking-date-wrap").on("click", function () {
                            $(".tf-check-in-out-date").trigger("click");
                        });
                        $(".tf-check-in-out-date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            dateFormat: "Y/m/d",
                            minDate: "today",
                            showMonths: selectedTemplate == "design-2" ? month : 1,

                            // flatpickr locale
							<?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                dateSetToFields(selectedDates, instance);
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                dateSetToFields(selectedDates, instance);
                            },
                            defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
                        });

                        function dateSetToFields(selectedDates, instance) {
                            if (selectedDates.length === 2) {
                                const monthNames = [
                                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                ];
                                if (selectedDates[0]) {
                                    const startDate = selectedDates[0];
                                    $(".tf-template-3 .tf-booking-form-checkin span.tf-booking-date").html(startDate.getDate());
                                    $(".tf-template-3 .tf-booking-form-checkin span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                                }
                                if (selectedDates[1]) {
                                    const endDate = selectedDates[1];
                                    $(".tf-template-3 .tf-booking-form-checkout span.tf-booking-date").html(endDate.getDate());
                                    $(".tf-template-3 .tf-booking-form-checkout span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                                }
                            }
                        }

                    });
                })(jQuery);
            </script>

		<?php } else { ?>
            <!-- Start Booking widget -->
            <form id="tf-single-hotel-avail" class="tf_booking-widget widget tf-hotel-side-booking tf-hotel-booking-sidebar" method="get" autocomplete="off">

				<?php wp_nonce_field( 'check_room_avail_nonce', 'tf_room_avail_nonce' ); ?>

                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="fas fa-user-friends"></i>
                            <select name="adults" id="adults" class="">
								<?php
								echo '<option value="1">1 ' . esc_html__( "Adult", "tourfic" ) . '</option>';
								if ( $max_adults_numbers > 1 ) {
									foreach ( range( 2, $max_adults_numbers ) as $value ) {
										$selected = $value == $adults ? 'selected' : null;
										echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Adults", "tourfic" ) . '</option>';
									}
								}
								?>

                            </select>
                        </div>
                    </label>
                </div>

                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="fas fa-child"></i>
                            <select name="children" id="children" class="">
								<?php
								echo '<option value="0">0 ' . esc_html__( "Children", "tourfic" ) . '</option>';
								if ( $max_childs_numbers > 0 ) {
									foreach ( range( 1, $max_childs_numbers ) as $value ) {
										$selected = $value == $child ? 'selected' : null;
										echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Children", "tourfic" ) . '</option>';
									}
								}
								?>
                            </select>
                        </div>
                    </label>
                </div>

                <div class="tf_booking-dates">
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <i class="far fa-calendar-alt"></i>
                                <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                       placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr( $check_in_out ) . '"' : '' ?> required>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="tf_form-row">
					<?php
					$ptype = isset( $_GET['type'] ) ? esc_attr( $_GET['type'] ) : get_post_type();
					?>
                    <input type="hidden" name="type" value="<?php echo esc_attr( $ptype ); ?>" class="tf-post-type"/>
                    <input type="hidden" name="post_id" value="<?php echo esc_attr( get_the_ID() ); ?>"/>
                    <input type="hidden" name="children_ages" value="<?php echo esc_attr( $children_ages ); ?>"/>

                    <div class="tf-btn">
                        <button class="tf_button tf-submit btn-styled"
                                type="submit"><?php echo esc_html( $tf_hotel_book_avaibality_button_text ); ?></button>
                    </div>


                </div>

            </form>
		<?php } ?>
        <script>
            (function ($) {
                $(document).ready(function () {

                    // First Day of Week
					<?php Helper::tf_flatpickr_locale( "root" ); ?>

                    const checkinoutdateange = flatpickr(".tf-hotel-booking-sidebar #check-in-out-date", {
                        enableTime: false,
                        mode: "range",
                        minDate: "today",
                        altInput: true,
                        altFormat: '<?php echo esc_html( $hotel_date_format_for_users ); ?>',
                        dateFormat: "Y/m/d",
                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                        },
                        defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
						<?php
						// Flatpickr locale for translation
						Helper::tf_flatpickr_locale();
						?>
                    });

                });
            })(jQuery);
        </script>

		<?php
	}

	/**
	 * Hotel Archive Single Item Layout
	 */
	static function tf_hotel_archive_single_item( $adults = '', $child = '', $room = '', $check_in_out = '', $startprice = '', $endprice = '' ) {

		// get post id
		$post_id = get_the_ID();
		//Get hotel_feature
		$features = ! empty( get_the_terms( $post_id, 'hotel_feature' ) ) ? get_the_terms( $post_id, 'hotel_feature' ) : '';
		$meta     = get_post_meta( $post_id, 'tf_hotels_opt', true );

		// Location
		if ( ! empty( $meta['map'] ) && Helper::tf_data_types( $meta['map'] ) ) {
			$address = ! empty( Helper::tf_data_types( $meta['map'] )['address'] ) ? Helper::tf_data_types( $meta['map'] )['address'] : '';
		}
		// Rooms
		$b_rooms = Room::get_hotel_rooms( $post_id );
		// Gallery Image
		$gallery = ! empty( $meta['gallery'] ) ? $meta['gallery'] : '';
		if ( $gallery ) {
			$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
		}

		// Archive Page Minimum Price
		$archive_page_price_settings = ! empty( Helper::tfopt( 'hotel_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'hotel_archive_price_minimum_settings' ) : 'all';

		// Featured
		$featured            = ! empty( $meta['featured'] ) ? $meta['featured'] : '';
		$hotel_multiple_tags = ! empty( $meta['tf-hotel-tags'] ) ? $meta['tf-hotel-tags'] : array();
		/**
		 * All values from URL
		 */
		// Adults
		if ( empty( $adults ) ) {
			$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		}
		// children
		if ( empty( $child ) ) {
			$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		}

		/**
		 * get children ages
		 * @since 2.8.6
		 */
		$children_ages_array = isset( $_GET['children_ages'] ) ? rest_sanitize_array( $_GET['children_ages'] ) : '';
		if ( is_array( $children_ages_array ) && ! empty( $children_ages_array ) ) {
			$children_ages = implode( ',', $children_ages_array );
		} else {
			$children_ages = '';
		}
		// room
		if ( empty( $room ) ) {
			$room = ! empty( $_GET['room'] ) ? sanitize_text_field( $_GET['room'] ) : '';
		}
		// Check-in & out date
		if ( empty( $check_in_out ) ) {
			$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';
		}
		if ( $check_in_out ) {
			$form_check_in      = substr( $check_in_out, 0, 10 );
			$form_check_in_stt  = strtotime( $form_check_in );
			$form_check_out     = substr( $check_in_out, 14, 10 );
			$form_check_out_stt = strtotime( $form_check_out );
		}

		if ( ! empty( $check_in_out ) ) {
			list( $tf_form_start, $tf_form_end ) = explode( ' - ', $check_in_out );
		}

		if ( ! empty( $check_in_out ) ) {
			$period = new \DatePeriod(
				new \DateTime( $tf_form_start ),
				new \DateInterval( 'P1D' ),
				new \DateTime( ! empty( $tf_form_end ) ? $tf_form_end : $tf_form_start . '23:59' )
			);
		} else {
			$period = '';
		}

		// Single link
		$url = get_the_permalink();
		$url = add_query_arg( array(
			'adults'            => $adults,
			'children'          => $child,
			'room'              => $room,
			'children_ages'     => $children_ages,
			'check-in-out-date' => $check_in_out
		), $url );

		$tf_hotel_arc_selected_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel-archive'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';

        $min_price_arr = Pricing::instance($post_id)->get_min_price($period);
		$min_discount_type = !empty($min_price_arr['min_discount_type']) ? $min_price_arr['min_discount_type'] : 'none';
		$min_discount_amount = !empty($min_price_arr['min_discount_amount']) ? $min_price_arr['min_discount_amount'] : 0;

		if ( $tf_hotel_arc_selected_template == "design-1" ) {
			?>
            <div class="tf-item-card tf-flex tf-item-hotel">
                <div class="tf-item-featured">
                    <div class="tf-tag-items">
                        <div class="tf-features-box">
							<?php if ( $featured ): ?>
                                <div class="tf-feature tf-flex">
									<?php
									echo ! empty( $meta['featured_text'] ) ? esc_html( $meta['featured_text'] ) : esc_html( "HOT DEAL" );
									?>
                                </div>
							<?php endif; ?>
                        </div>
						<?php
						if ( sizeof( $hotel_multiple_tags ) > 0 ) {
							foreach ( $hotel_multiple_tags as $tag ) {
								$hotel_tag_name       = ! empty( $tag['hotel-tag-title'] ) ? esc_html( $tag['hotel-tag-title'] ) : '';
								$tag_background_color = ! empty( $tag["hotel-tag-color-settings"]["background"] ) ? $tag["hotel-tag-color-settings"]["background"] : "#003162";
								$tag_font_color       = ! empty( $tag["hotel-tag-color-settings"]["font"] ) ? $tag["hotel-tag-color-settings"]["font"] : "#fff";

								if ( ! empty( $hotel_tag_name ) ) {
									echo wp_kses_post(
										<<<EOD
											<div class="tf-multiple-tag-item" style="color: $tag_font_color; background-color: $tag_background_color ">
												<span class="tf-multiple-tag">$hotel_tag_name</span>
											</div>
										EOD
									);
								}
							}
						}
						?>
                    </div>
                    <a href="<?php echo esc_url( $url ); ?>">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'full' );
						} else {
							echo '<img src="' . esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </a>
                </div>
                <div class="tf-item-details">
					<?php
					if ( ! empty( $address ) ) {
						?>
                        <div class="tf-title-meta tf-flex tf-flex-align-center tf-flex-gap-8">
                            <i class="fa-solid fa-location-dot"></i>
                            <p>
								<?php
								if ( strlen( $address ) > 120 ) {
									echo esc_html( Helper::tourfic_character_limit_callback( $address, 120 ) );
								} else {
									echo esc_html( $address );
								}
								?>
                            </p>
                        </div>
					<?php } ?>
                    <div class="tf-title tf-mt-16">
                        <h2><a href="<?php echo esc_url( $url ); ?>"><?php the_title(); ?></a></h2>
                    </div>
					<?php TF_Review::tf_archive_single_rating(); ?>
					<?php if ( $features ) { ?>
                        <div class="tf-archive-features tf-mt-16">
                            <ul>
								<?php foreach ( $features as $tfkey => $feature ) {
									$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
									if ( ! empty( $feature_meta ) ) {
										$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
									}
									if ( ! empty( $f_icon_type ) && $f_icon_type == 'fa' ) {
										$feature_icon = ! empty( $feature_meta['icon-fa'] ) ? '<i class="' . $feature_meta['icon-fa'] . '"></i>' : '';
									} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'c' ) {
										$feature_icon = ! empty( $feature_meta['icon-c'] ) ? '<img src="' . $feature_meta['icon-c'] . '" style="min-width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />' : '';
									}
									if ( $tfkey < 4 ) {
										?>
                                        <li class="tf-feature-lists">
											<?php
											if ( ! empty( $feature_icon ) ) {
												echo wp_kses_post( $feature_icon );
											} ?>
											<?php echo esc_html( $feature->name ); ?>
                                        </li>
									<?php }
								} ?>
								<?php
								if ( ! empty( $features ) ) {
									if ( count( $features ) > 4 ) {
										echo '<span>More....</span>';
									}
								}
								?>
                            </ul>
                        </div>
					<?php } ?>
                    <!-- Hotel Template 2 Archive Feature End -->

                    <div class="tf-details tf-mt-16">
                        <p><?php echo wp_kses_post( substr( wp_strip_all_tags( get_the_content() ), 0, 100 ) ) . '...'; ?></p>
                    </div>
                    <div class="tf-post-footer tf-flex tf-flex-align-center tf-flex-space-bttn tf-mt-16">
                        <div class="tf-pricing">
							<?php echo Pricing::instance( $post_id )->get_min_price_html($period); ?>
                        </div>
                        <div class="tf-booking-bttns">
                            <a class="tf-btn-normal btn-secondary" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( "View Details", "tourfic" ); ?></a>
                        </div>
                    </div>
                </div>
            </div>
		<?php } elseif ( $tf_hotel_arc_selected_template == "design-2" ) {
			$first_gallery_image = explode( ',', $gallery );
			?>
            <div class="tf-available-room">
                <div class="tf-available-room-gallery">
                    <div class="tf-room-gallery">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'full' );
						} else {
							echo '<img src="' . esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </div>
					<?php
					if ( ! empty( $gallery_ids ) ) { ?>
                        <div data-id="<?php echo esc_attr( get_the_ID() ); ?>" data-type="tf_hotel" class="tf-room-gallery tf-popup-buttons tf-hotel-room-popup"
                             style="<?php echo ! empty( $first_gallery_image[0] ) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.70) 0%, rgba(48, 40, 28, 0.70) 100%), url(' . esc_url( wp_get_attachment_image_url( $first_gallery_image[0] ) ) . '), lightgray 50% / cover no-repeat; background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
                            <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g id="content">
                                    <path id="Rectangle 2111"
                                          d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5"
                                          stroke="#FDF9F4" stroke-width="1.5"></path>
                                    <path id="Rectangle 2109"
                                          d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z"
                                          stroke="#FDF9F4" stroke-width="1.5"></path>
                                    <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4"
                                          stroke-width="1.5" stroke-linejoin="round"></path>
                                    <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </g>
                            </svg>
                        </div>
					<?php } ?>
                    <div class="tf-available-labels">
						<?php if ( $featured ): ?>
                            <span class="tf-available-labels-featured"><?php esc_html_e( "Featured", "tourfic" ); ?></span>
						<?php endif; ?>
						<?php
						if ( sizeof( $hotel_multiple_tags ) > 0 ) {
							foreach ( $hotel_multiple_tags as $tag ) {
								$hotel_tag_name       = ! empty( $tag['hotel-tag-title'] ) ? esc_html( $tag['hotel-tag-title'] ) : '';
								$tag_background_color = ! empty( $tag["hotel-tag-color-settings"]["background"] ) ? $tag["hotel-tag-color-settings"]["background"] : "#003162";
								$tag_font_color       = ! empty( $tag["hotel-tag-color-settings"]["font"] ) ? $tag["hotel-tag-color-settings"]["font"] : "#fff";

								if ( ! empty( $hotel_tag_name ) ) {
									echo wp_kses_post(
										<<<EOD
										<span class="tf-multiple-tag" style="color: $tag_font_color; background-color: $tag_background_color ">$hotel_tag_name</span>
									EOD
									);
								}
							}
						}
						?>
                    </div>
                    <div class="tf-available-ratings">
						<?php TF_Review::tf_archive_single_rating(); ?>
                        <i class="fa-solid fa-star"></i>
                    </div>
                </div>
                <div class="tf-available-room-content">
                    <div class="tf-available-room-content-left">
                        <div class="tf-card-heading-info">
                            <div class="tf-section-title-and-location">
                                <a href="<?php echo esc_url( get_the_permalink() ); ?>"><h2 class="tf-section-title"><?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 55 ) ); ?></h2></a>
								<?php
								if ( ! empty( $address ) ) {
									?>
                                    <div class="tf-title-location">
                                        <div class="location-icon">
                                            <i class="ri-map-pin-line"></i>
                                        </div>
                                        <span><?php echo esc_html( Helper::tourfic_character_limit_callback( esc_html( $address ), 65 ) ); ?></span>
                                    </div>
								<?php } ?>
                            </div>
                            <div class="tf-mobile tf-pricing-info">
								<?php if ( ! empty( $min_discount_amount ) ) { ?>
                                    <div class="tf-available-room-off">
                                        <span>
                                            <?php echo $min_discount_type == "percent" ? esc_html( $min_discount_amount ) . '%' : wp_kses_post( wc_price( $min_discount_amount ) ) ?><?php esc_html_e( "Off ", "tourfic" ); ?>
                                        </span>
                                    </div>
								<?php } ?>
                                <div class="tf-available-room-price">
                                <span class="tf-price-from">
                                <?php echo Pricing::instance( $post_id )->get_min_price_html($period); ?>
                                </span>
                                </div>
                            </div>
                        </div>
                        <!-- Hotel Template 3 Archive Feature Start -->
						<?php if ( $features ) { ?>
                            <ul class="features">
								<?php foreach ( $features as $tfkey => $feature ) {
									$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
									if ( ! empty( $feature_meta ) ) {
										$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
									}
									if ( ! empty( $f_icon_type ) && $f_icon_type == 'fa' ) {
										$feature_icon = ! empty( $feature_meta['icon-fa'] ) ? '<i class="' . $feature_meta['icon-fa'] . '"></i>' : '';
									} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'c' ) {
										$feature_icon = ! empty( $feature_meta['icon-c'] ) ? '<img src="' . $feature_meta['icon-c'] . '" style="min-width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />' : '';
									}
									if ( $tfkey < 5 ) { ?>
                                        <li>
											<?php
											if ( ! empty( $feature_icon ) ) {
												echo wp_kses_post( $feature_icon );
											} ?>
											<?php echo esc_html( $feature->name ); ?>
                                        </li>
									<?php } ?>
								<?php } ?>
								<?php if ( count( $features ) > 5 ) { ?>
                                    <li><a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( "View More", "tourfic" ); ?></a></li>
								<?php } ?>
                            </ul>
						<?php } ?>
                        <!-- Hotel Template 3 Archive Feature End -->
                    </div>
                    <div class="tf-available-room-content-right">
                        <div class="tf-card-pricing-heading">
							<?php
							if ( ! empty( $min_discount_amount ) ) { ?>
                                <div class="tf-available-room-off">
                                    <span>
                                        <?php echo $min_discount_type == "percent" ? esc_html( $min_discount_amount ) . '%' : wp_kses_post( wc_price( $min_discount_amount ) ) ?><?php esc_html_e( "Off ", "tourfic" ); ?>
                                    </span>
                                </div>
							<?php } ?>
                            <div class="tf-available-room-price">
                            <span class="tf-price-from">
                            <?php echo Pricing::instance( $post_id )->get_min_price_html($period); ?>
                            </span>
                            </div>
                        </div>
                        <a href="<?php echo esc_url( $url ); ?>" class="view-hotel"><?php esc_html_e( "See details", "tourfic" ); ?></a>
                    </div>
                </div>
            </div>
		<?php } else { ?>
            <div class="single-tour-wrap <?php echo $featured ? esc_attr( 'tf-featured' ) : '' ?>">
                <div class="single-tour-inner">
					<?php if ( $featured ): ?>
                        <div class="tf-featured-badge">
                            <span><?php echo ! empty( $meta['featured_text'] ) ? esc_html( $meta['featured_text'] ) : esc_html( "HOT DEAL" ); ?></span>
                        </div>
					<?php endif; ?>
                    <div class="tourfic-single-left">
                        <div class="default-tags-container">
							<?php
							if ( sizeof( $hotel_multiple_tags ) > 0 ) {
								foreach ( $hotel_multiple_tags as $tag ) {
									$hotel_tag_name       = ! empty( $tag['hotel-tag-title'] ) ? esc_html( $tag['hotel-tag-title'] ) : '';
									$tag_background_color = ! empty( $tag["hotel-tag-color-settings"]["background"] ) ? $tag["hotel-tag-color-settings"]["background"] : "#003162";
									$tag_font_color       = ! empty( $tag["hotel-tag-color-settings"]["font"] ) ? $tag["hotel-tag-color-settings"]["font"] : "#fff";

									if ( ! empty( $hotel_tag_name ) ) {
										echo wp_kses_post(
											<<<EOD
											<span class="default-single-tag" style="color: $tag_font_color; background-color: $tag_background_color">$hotel_tag_name</span>
										EOD
										);
									}
								}
							}
							?>
                        </div>
                        <a href="<?php echo esc_url( $url ); ?>">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'full' );
							} else {
								echo '<img width="100%" height="100%" src="' . esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
							}
							?>
                        </a>
                    </div>
                    <div class="tourfic-single-right">
                        <div class="tf_property_block_main_row">
                            <div class="tf_item_main_block">
                                <div class="tf-hotel__title-wrap">
                                    <a href="<?php echo esc_url( $url ); ?>"><h3 class="tourfic_hotel-title"><?php the_title(); ?></h3></a>
                                </div>
								<?php
								if ( $address ) {
									echo '<div class="tf-map-link">';
									echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' . strlen( $address ) > 75 ? esc_html( Helper::tourfic_character_limit_callback( $address, 76 ) ) : esc_html( $address ) . '</span>';
									echo '</div>';
								}
								?>
                            </div>
							<?php TF_Review::tf_archive_single_rating(); ?>
                        </div>

                        <div class="sr_rooms_table_block">
                            <div class="room_details">
                                <div class="featuredRooms">
                                    <div class="prco-ltr-right-align-helper">
                                        <div class="tf-archive-shortdesc">
                                            <p><?php echo esc_html( substr( wp_strip_all_tags( get_the_content() ), 0, 160 ) ) . '...'; ?></p>
                                        </div>
                                    </div>
                                    <div class="roomNameInner">
                                        <div class="room_link">
                                            <div class="roomrow_flex">

                                                <!-- Hotel Template 1 Archive Feature Start -->
												<?php if ( $features ) { ?>
                                                    <div class="roomName_flex">
                                                        <ul class="tf-archive-desc">
															<?php foreach ( $features as $feature ) {
																$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
																if ( ! empty( $feature_meta ) ) {
																	$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
																}
																if ( ! empty( $f_icon_type ) && $f_icon_type == 'fa' ) {
																	$feature_icon = ! empty( $feature_meta['icon-fa'] ) ? '<i class="' . $feature_meta['icon-fa'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
																} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'c' ) {
																	$feature_icon = ! empty( $feature_meta['icon-c'] ) ? '<img src="' . $feature_meta['icon-c'] . '" style="min-width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
																} else {
																	$feature_icon = '<i class="fas fa-bread-slice"></i>';
																}
																?>
                                                                <li class="tf-tooltip">
																	<?php
																	if ( ! empty( $feature_icon ) ) {
																		echo wp_kses_post( $feature_icon );
																	} ?>
                                                                    <div class="tf-top">
																		<?php echo esc_html( $feature->name ); ?>
                                                                        <i class="tool-i"></i>
                                                                    </div>
                                                                </li>
															<?php } ?>
                                                        </ul>
                                                    </div>
												<?php } ?>
                                                <!-- Hotel Template 1 Archive Feature End -->

                                                <div class="roomPrice roomPrice_flex sr_discount" style="<?php echo empty( $features ) ? 'text-align:left' : ''; ?>">
                                                    <div class="availability-btn-area">
                                                        <a href="<?php echo esc_url( $url ); ?>" class="tf_button btn-styled"><?php esc_html_e( 'View Details', 'tourfic' ); ?></a>
                                                    </div>
                                                    <!-- Show minimum price @author - Hena -->
                                                    <div class="tf-room-price-area">
                                                        <div class="tf-room-price">
															<?php echo Pricing::instance( $post_id )->get_min_price_html($period); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<?php
		}
	}

	function tf_hotel_quickview_callback() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
			return;
		}
		$meta = get_post_meta( $_POST['post_id'], 'tf_hotels_opt', true );

		// Single Template Style
		$tf_hotel_layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
		if ( "single" == $tf_hotel_layout_conditions ) {
			$tf_hotel_single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
		}
		$tf_hotel_global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';

		$tf_hotel_selected_check = ! empty( $tf_hotel_single_template ) ? $tf_hotel_single_template : $tf_hotel_global_template;

		$tf_hotel_selected_template = $tf_hotel_selected_check;

		$rooms                       = Room::get_hotel_rooms( $_POST['post_id'] );
		if ( $tf_hotel_selected_template == "design-1" || $tf_hotel_selected_template == "default" ) {
			?>
            <div class="tf-hotel-quick-view" style="display: flex">
				<?php
				foreach ( $rooms as $_room ) :
					$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
					$enable = ! empty( $room['enable'] ) ? $room['enable'] : '';
					if ( $enable == '1' && $room['unique_id'] . $_room->ID == $_POST['uniqid_id'] ) :
						$tf_room_gallery = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
						$child_age_limit = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";
						?>
                        <div class="tf-hotel-details-qc-gallelry" style="width: 545px;">
							<?php
							if ( $tf_room_gallery ) {
								$tf_room_gallery_ids = explode( ',', $tf_room_gallery );
							}

							?>

                            <div class="tf-details-qc-slider tf-details-qc-slider-single">
								<?php
								if ( ! empty( $tf_room_gallery_ids ) ) {
									foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
										?>
                                        <div class="tf-details-qcs">
											<?php
											$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
											echo '<img src="' . esc_url( $image_url ) . '" alt="">';
											?>
                                        </div>
									<?php }
								} ?>
                            </div>
                            <div class="tf-details-qc-slider tf-details-qc-slider-nav">
								<?php
								if ( ! empty( $tf_room_gallery_ids ) ) {
									foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
										?>
                                        <div class="tf-details-qcs">
											<?php
											$image_url = wp_get_attachment_url( $gallery_item_id, 'thumbnail' );
											echo '<img src="' . esc_url( $image_url ) . '" alt="">';
											?>
                                        </div>
									<?php }
								} ?>
                            </div>

                            <script>
                                jQuery('.tf-details-qc-slider-single').slick({
                                    slidesToShow: 1,
                                    slidesToScroll: 1,
                                    arrows: <?php echo $tf_hotel_selected_template == "design-1" ? "false" : "true" ?>,
                                    fade: false,
                                    adaptiveHeight: true,
                                    infinite: true,
                                    useTransform: true,
                                    speed: 400,
                                    cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
                                });

                                jQuery('.tf-details-qc-slider-nav')
                                    .on('init', function (event, slick) {
                                        jQuery('.tf-details-qc-slider-nav .slick-slide.slick-current').addClass('is-active');
                                    })
                                    .slick({
                                        slidesToShow: 7,
                                        slidesToScroll: 7,
                                        dots: false,
                                        focusOnSelect: false,
                                        infinite: false,
                                        responsive: [{
                                            breakpoint: 1024,
                                            settings: {
                                                slidesToShow: 5,
                                                slidesToScroll: 5,
                                            }
                                        }, {
                                            breakpoint: 640,
                                            settings: {
                                                slidesToShow: 4,
                                                slidesToScroll: 4,
                                            }
                                        }, {
                                            breakpoint: 420,
                                            settings: {
                                                slidesToShow: 3,
                                                slidesToScroll: 3,
                                            }
                                        }]
                                    });

                                jQuery('.tf-details-qc-slider-single').on('afterChange', function (event, slick, currentSlide) {
                                    jQuery('.tf-details-qc-slider-nav').slick('slickGoTo', currentSlide);
                                    var currrentNavSlideElem = '.tf-details-qc-slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
                                    jQuery('.tf-details-qc-slider-nav .slick-slide.is-active').removeClass('is-active');
                                    jQuery(currrentNavSlideElem).addClass('is-active');
                                });

                                jQuery('.tf-details-qc-slider-nav').on('click', '.slick-slide', function (event) {
                                    event.preventDefault();
                                    var goToSingleSlide = jQuery(this).data('slick-index');

                                    jQuery('.tf-details-qc-slider-single').slick('slickGoTo', goToSingleSlide);
                                });
                            </script>

                        </div>
                        <div class="tf-hotel-details-info" style="width:440px; padding-left: 35px;max-height: 470px;padding-top: 25px; overflow-y: scroll;">
							<?php
							$footage      = ! empty( $room['footage'] ) ? $room['footage'] : '';
							$bed          = ! empty( $room['bed'] ) ? $room['bed'] : '';
							$adult_number = ! empty( $room['adult'] ) ? $room['adult'] : '0';
							$child_number = ! empty( $room['child'] ) ? $room['child'] : '0';
							$num_room     = ! empty( $room['num-room'] ) ? $room['num-room'] : '0';

							if ( $tf_hotel_selected_template == "design-1" ) {
								?>
                                <h3><?php echo esc_html( get_the_title( $_room->ID ) ); ?></h3>
                                <div class="tf-template-1 tf-room-adv-info">
                                    <ul>
										<?php if ( $num_room ) { ?>
                                            <li><i class="fas fa-person-booth"></i> <?php echo esc_html( $num_room ); ?> <?php esc_html_e( 'Rooms', 'tourfic' ); ?></li>
										<?php }
										if ( $footage ) { ?>
                                            <li><i class="ri-user-2-line"></i> <?php echo esc_html( $footage ); ?> <?php esc_html_e( 'Sft', 'tourfic' ); ?></li>
										<?php }
										if ( $bed ) { ?>
                                            <li><i class="ri-hotel-bed-line"></i> <?php echo esc_html( $bed ); ?> <?php esc_html_e( ' Beds', 'tourfic' ); ?></li>
										<?php } ?>
										<?php if ( $adult_number ) { ?>
                                            <li><i class="ri-user-2-line"></i> <?php echo esc_html( $adult_number ); ?> <?php esc_html_e( 'Adults', 'tourfic' ); ?></li>
										<?php }
										if ( $child_number ) { ?>
                                            <li>
                                                <i class="ri-user-smile-line"></i> <?php echo esc_html( $child_number ); ?> <?php esc_html_e( 'Children', 'tourfic' ); ?>
                                            </li>
										<?php } ?>
                                    </ul>

                                    <p><?php echo wp_kses_post( get_post_field( 'post_content', $_room->ID ) ); ?></p>
                                </div>
								<?php if ( ! empty( $room['features'] ) ) { ?>

                                    <div class="tf-template-1 tf-room-adv-info">
                                        <h4><?php esc_html_e( "Amenities", "tourfic" ); ?></h4>
                                        <ul>
											<?php foreach ( $room['features'] as $feature ) {
												$room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
												if ( ! empty( $room_f_meta ) ) {
													$room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
												}
												if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' ) {
													$room_feature_icon = ! empty( $room_f_meta['icon-fa'] ) ? '<i class="' . $room_f_meta['icon-fa'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
												} elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' ) {
													$room_feature_icon = ! empty( $room_f_meta['icon-c'] ) ? '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
												} else {
													$room_feature_icon = '<i class="fas fa-bread-slice"></i>';
												}

												$room_term = get_term( $feature ); ?>
                                                <li>
													<?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
													<?php echo esc_html( $room_term->name ); ?>
                                                </li>
											<?php } ?>
                                        </ul>
                                    </div>
								<?php } ?>
							<?php } else { ?>
                                <h3><?php echo esc_html( get_the_title( $_room->ID ) ); ?></h3>
                                <p><?php echo wp_kses_post( get_post_field( 'post_content', $_room->ID ) ); ?></p>

                                <div class="tf-room-title description">
									<?php if ( $num_room ) { ?>
                                        <div class="tf-tooltip tf-d-ib">
                                            <div class="room-detail-icon">
                                                <span class="room-icon-wrap"><i class="fas fa-person-booth"></i></span>
                                                <span class="icon-text tf-d-b"><?php echo esc_html( $num_room ); ?></span>
                                            </div>
                                            <div class="tf-top">
												<?php esc_html_e( 'Number of Room', 'tourfic' ); ?>
                                                <i class="tool-i"></i>
                                            </div>
                                        </div>
									<?php }
									if ( $footage ) { ?>
                                        <div class="tf-tooltip tf-d-ib">
                                            <div class="room-detail-icon">
                                                <span class="room-icon-wrap"><i class="ri-pencil-ruler-2-line"></i></span>
                                                <span class="icon-text tf-d-b"><?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></span>
                                            </div>
                                            <div class="tf-top">
												<?php esc_html_e( 'Room Footage', 'tourfic' ); ?>
                                                <i class="tool-i"></i>
                                            </div>
                                        </div>
									<?php }
									if ( $bed ) { ?>
                                        <div class="tf-tooltip tf-d-ib">
                                            <div class="room-detail-icon">
                                                <span class="room-icon-wrap"><i class="ri-hotel-bed-line"></i></i></span>
                                                <span class="icon-text tf-d-b">x<?php echo esc_html( $bed ); ?></span>
                                            </div>
                                            <div class="tf-top">
												<?php esc_html_e( 'Number of Beds', 'tourfic' ); ?>
                                                <i class="tool-i"></i>
                                            </div>
                                        </div>
									<?php } ?>

									<?php if ( ! empty( $room['features'] ) ) { ?>
                                        <div class="room-features">
                                            <div class="tf-room-title"><h4><?php esc_html_e( 'Amenities', 'tourfic' ); ?></h4></div>
                                            <ul class="room-feature-list" style="margin: 0;">

												<?php foreach ( $room['features'] as $feature ) {

													$room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
													if ( ! empty( $room_f_meta ) ) {
														$room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
													}
													if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' ) {
														$room_feature_icon = ! empty( $room_f_meta['icon-fa'] ) ? '<i class="' . $room_f_meta['icon-fa'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
													} elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' ) {
														$room_feature_icon = ! empty( $room_f_meta['icon-c'] ) ? '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
													} else {
														$room_feature_icon = '<i class="fas fa-bread-slice"></i>';
													}

													$room_term = get_term( $feature ); ?>
                                                    <li class="tf-tooltip tf-d-ib">
														<?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
                                                        <div class="tf-top">
															<?php echo esc_html( $room_term->name ); ?>
                                                            <i class="tool-i"></i>
                                                        </div>
                                                    </li>
												<?php } ?>
                                            </ul>
                                        </div>
									<?php } ?>
                                </div>
                                <div class="pax">

                                    <div class="tf-room-title"><h4><?php esc_html_e( 'Pax', 'tourfic' ); ?></h4>
										<?php if ( $adult_number ) { ?>
                                            <div class="tf-tooltip tf-d-ib">
                                                <div class="room-detail-icon">
                                                    <span class="room-icon-wrap"><i class="fas fa-male"></i><i class="fas fa-female"></i></span>
                                                    <span class="icon-text tf-d-b">x<?php echo esc_html( $adult_number ); ?></span>
                                                </div>
                                                <div class="tf-top">
													<?php esc_html_e( 'Number of Adults', 'tourfic' ); ?>
                                                    <i class="tool-i"></i>
                                                </div>
                                            </div>

										<?php }
										if ( $child_number ) { ?>
                                            <div class="tf-tooltip tf-d-ib">
                                                <div class="room-detail-icon">
                                                    <span class="room-icon-wrap"><i class="ri-user-smile-line"></i></i></span>
                                                    <span class="icon-text tf-d-b">x<?php echo esc_html( $child_number ); ?></span>
                                                </div>
                                                <div class="tf-top">
													<?php
													if ( ! empty( $child_age_limit ) ) {
														/* translators: %s Child Age Limit */
														printf( esc_html__( 'Children Age Limit %s Years', 'tourfic' ), esc_html( $child_age_limit ) );
													} else {
														esc_html_e( 'Number of Children', 'tourfic' );
													}
													?>
                                                    <i class="tool-i"></i>
                                                </div>
                                            </div>
										<?php } ?>
                                    </div>

                                </div>
							<?php } ?>
                        </div>
					<?php
					endif;
				endforeach;
				?>
            </div>
			<?php
		}
		if ( $tf_hotel_selected_template == "design-2" ) {
			foreach ( $rooms as $_room ) :
				$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
				$enable              = ! empty( $room['enable'] ) ? $room['enable'] : '';
				if ( $enable == '1' && $room['unique_id'] . $_room->ID == $_POST['uniqid_id'] ) :
					$tf_room_gallery = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
					$child_age_limit = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";

					$footage      = ! empty( $room['footage'] ) ? $room['footage'] : '';
					$bed          = ! empty( $room['bed'] ) ? $room['bed'] : '';
					$adult_number = ! empty( $room['adult'] ) ? $room['adult'] : '0';
					$child_number = ! empty( $room['child'] ) ? $room['child'] : '0';

					if ( $tf_room_gallery ) {
						$tf_room_gallery_ids = explode( ',', $tf_room_gallery );
					}
					?>
                    <div class="tf-popup-inner">

                        <div class="tf-popup-body">
                            <div class="tf-popup-left">
								<?php
								if ( ! empty( $tf_room_gallery_ids ) ) {
									foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
										$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
										?>
                                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>" class="tf-popup-image">
									<?php }
								} ?>
                            </div>
                            <div class="tf-popup-right">
                                <span class="tf-popup-info-title"><?php esc_html_e( "Room details", "tourfic" ); ?></span>
                                <ul>
									<?php if ( $footage ) { ?>
                                        <li><i class="ri-pencil-ruler-2-line"></i> <?php echo esc_html( $footage ); ?><?php esc_html_e( 'sft', 'tourfic' ); ?></li>
									<?php } ?>
									<?php if ( $bed ) { ?>
                                        <li><i class="ri-hotel-bed-line"></i> <?php echo esc_html( $bed ); ?><?php esc_html_e( ' Beds', 'tourfic' ); ?></li>
									<?php } ?>
									<?php if ( $adult_number ) { ?>
                                        <li><i class="ri-user-2-line"></i> <?php echo esc_html( $adult_number ); ?><?php esc_html_e( ' Adults', 'tourfic' ); ?></li>
									<?php } ?>
									<?php if ( $child_number ) { ?>
                                        <li><i class="ri-user-smile-line"></i><?php echo esc_html( $child_number ); ?><?php esc_html_e( ' Child', 'tourfic' ); ?></li>
									<?php } ?>
                                </ul>

                                <span class="tf-popup-info-title"><?php esc_html_e( "Other benefits", "tourfic" ); ?></span>
                                <ul>
									<?php
									if ( ! empty( $room['features'] ) ) {
										foreach ( $room['features'] as $feature ) {
											$room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
											if ( ! empty( $room_f_meta ) ) {
												$room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
											}
											if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' && ! empty( $room_f_meta['icon-fa'] ) ) {
												$room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
											} elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' && ! empty( $room_f_meta['icon-c'] ) ) {
												$room_feature_icon = '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />';
											}

											$room_term = get_term( $feature ); ?>
                                            <li>
												<?php echo ! empty( $room_feature_icon ) ? wp_kses_post( $room_feature_icon ) : ''; ?>
												<?php echo esc_html( $room_term->name ); ?>
                                            </li>
										<?php }
									} ?>

                                </ul>
                            </div>
                        </div>
                        <div class="tf-popup-close">
                            <i class="fa-solid fa-xmark"></i>
                        </div>
                    </div>
				<?php
				endif;
			endforeach;
		}
		wp_die();
	}

	/**
	 * Ajax hotel Archive Hotel Gallery quick view
	 * @author Jahid
	 */
	function tf_hotel_archive_popup_qv_callback() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
			return;
		}
		if ( ! empty( $_POST['post_type'] ) && "tf_hotel" == $_POST['post_type'] ) {
			$meta    = get_post_meta( $_POST['post_id'], 'tf_hotels_opt', true );
			$gallery = ! empty( $meta['gallery'] ) ? $meta['gallery'] : '';
			if ( $gallery ) {
				$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
			}
		}

		if ( ! empty( $_POST['post_type'] ) && "tf_tours" == $_POST['post_type'] ) {
			$meta    = get_post_meta( $_POST['post_id'], 'tf_tours_opt', true );
			$gallery = ! empty( $meta['tour_gallery'] ) ? $meta['tour_gallery'] : '';
			if ( $gallery ) {
				$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
			}
		}

		if ( ! empty( $_POST['post_type'] ) && "tf_apartment" == $_POST['post_type'] ) {
			$meta    = get_post_meta( $_POST['post_id'], 'tf_apartment_opt', true );
			$gallery = ! empty( $meta['apartment_gallery'] ) ? $meta['apartment_gallery'] : '';
			if ( $gallery ) {
				$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
			}
		}

		if ( ! empty( $gallery_ids ) ) {
			foreach ( $gallery_ids as $key => $gallery_item_id ) {
				$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
				?>
                <img src="<?php echo esc_url( $image_url ); ?>" alt="" class="tf-popup-image">
			<?php }
		}
		wp_die();
	}

	/*
     * Hotel search ajax
     * @since 2.9.7
     * @author Foysal
     */
	function tf_hotel_search_ajax_callback() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
			return;
		}
		$response = [
			'status'  => 'error',
			'message' => '',
		];

		if ( Helper::tfopt( 'required_location_hotel_search' ) && ( ! isset( $_POST['place'] ) || empty( $_POST['place'] ) ) ) {
			$response['message'] = esc_html__( 'Please enter your location', 'tourfic' );
		} elseif ( Helper::tfopt( 'date_hotel_search' ) && ( ! isset( $_POST['check-in-out-date'] ) || empty( $_POST['check-in-out-date'] ) ) ) {
			$response['message'] = esc_html__( 'Please select check in and check out date', 'tourfic' );
		}

		if ( Helper::tfopt( 'date_hotel_search' ) ) {
			if ( ! empty( $_POST['check-in-out-date'] ) ) {
				$response['query_string'] = str_replace( '&action=tf_hotel_search', '', http_build_query( $_POST ) );
				$response['status']       = 'success';
			}
		} else {
			if ( ! Helper::tfopt( 'required_location_hotel_search' ) || ! empty( $_POST['place'] ) ) {
				$response['query_string'] = str_replace( '&action=tf_hotel_search', '', http_build_query( $_POST ) );
				$response['status']       = 'success';
			}
		}

		echo wp_json_encode( $response );
		wp_die();
	}

	function tf_hotel_filter_by_features( $features ) {
		//get all the hotel features
		$feature_filter = ! empty( Helper::tfopt( 'feature-filter' ) ) ? Helper::tfopt( 'feature-filter' ) : false;

		if ( ! empty( $features ) && $feature_filter ):
			?>
            <!-- Filter by feature  -->
            <div class="tf-room-filter" style="display: none">
                <h4 class="tf-room-feature-title"><?php echo esc_html__( 'Filter Rooms based on features', 'tourfic' ); ?></h4>
                <ul class="tf-room-checkbox">
					<?php
					foreach ( $features as $feature ) {
						//get the feature details by it's id
						$term = get_term_by( 'id', $feature, 'hotel_feature' );

						if ( $term ) {
							echo '<li><label for="' . esc_attr( $term->slug ) . '">';
							echo '<input type="checkbox" name="features" class="" value="' . esc_attr( $feature ) . '" id="' . esc_attr( $term->slug ) . '">';
							echo "<span class='checkmark'></span>";
							echo esc_html( $term->name ) . '</label>';
							echo "</li>";
						}
					}
					?>
                </ul>
            </div>
		<?php endif;
	}

	/**
	 * Assign taxonomy(hotel_feature) from the single post metabox when a hour updated or published
	 * @return array();
	 * @author Foysal
	 * @since 2.11.25
	 */
	function tf_hotel_features_assign_taxonomies( $post_id, $post, $old_status ) {
		if ( 'tf_room' !== $post->post_type ) {
			return;
		}

		$room          = get_post_meta( $post_id, 'tf_room_opt', true );
		$room_hotel    = ! empty( $room['tf_hotel'] ) ? $room['tf_hotel'] : '';
		$room_features = ! empty( $room['features'] ) ? $room['features'] : '';
		if ( ! empty( $room_features ) ) {
			$room_features = array_map( 'intval', $room_features );
			wp_set_object_terms( $room_hotel, $room_features, 'hotel_feature' );
		}
	}

	/**
	 * Assign hotel rooms when a hotel updated or published
	 * @return array();
	 * @author Foysal
	 * @since 2.11.25
	 */
	function tf_hotel_rooms_assign( $post_id, $post, $old_status ) {
		if ( 'tf_hotel' !== $post->post_type ) {
			return;
		}

		$hotel_meta = get_post_meta( $post_id, 'tf_hotels_opt', true );
		$rooms    	= ! empty( $hotel_meta['tf_rooms'] ) ? $hotel_meta['tf_rooms'] : [];
		$assigned_rooms = Room::get_hotel_rooms( $post_id );
		$assigned_room_ids = array_column($assigned_rooms, 'ID');
		$removed_rooms = array_diff($assigned_room_ids, $rooms);
		
		if(!empty($rooms)){
			foreach($rooms as $room_id){
				$room_meta   = get_post_meta( intval($room_id), 'tf_room_opt', true );
				$room_meta = is_array($room_meta) ? $room_meta : [];
				$room_meta['tf_hotel'] = $post_id;

				update_post_meta($room_id, 'tf_room_opt', $room_meta);
			}
			foreach($removed_rooms as $room_id){
				$room_meta   = get_post_meta( $room_id, 'tf_room_opt', true );
				if($post_id == $room_meta['tf_hotel']){
					$room_meta['tf_hotel'] = '';
				}

				update_post_meta($room_id, 'tf_room_opt', $room_meta);
			}
		}
	}

	/**
	 * Assign room to a hotel when updated or published
	 * @return array();
	 * @author Foysal
	 * @since 2.11.25
	 */
	function tf_room_assign_to_hotel( $post_id, $post, $old_status ) {
		if ( 'tf_room' !== $post->post_type ) {
			return;
		}

		$room_meta = get_post_meta( $post_id, 'tf_room_opt', true );
		$hotel_id  = ! empty( $room_meta['tf_hotel'] ) ? intval($room_meta['tf_hotel']) : '';
		
		//insert in hotel tf_rooms field
		if(!empty($hotel_id)){
			$hotel_meta = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
			$hotel_meta = is_array($hotel_meta) ? $hotel_meta : [];
			if(! empty( $hotel_meta['tf_rooms'] ) && is_array($hotel_meta['tf_rooms'])){
				array_push($hotel_meta['tf_rooms'], $post_id);
			} else {
				$hotel_meta['tf_rooms'] = array($post_id);
			}
			
			update_post_meta($hotel_id, 'tf_hotels_opt', $hotel_meta);
		}

		//remove from tf_rooms fields if exists
		if(empty($hotel_id)){
			$assign_hotel_id = Room::get_hotel_id_for_assigned_room($post_id);
			$hotel_meta = get_post_meta( $assign_hotel_id, 'tf_hotels_opt', true );

			if(! empty( $hotel_meta['tf_rooms'] ) && is_array($hotel_meta['tf_rooms'])){
				$hotel_meta['tf_rooms'] = array_diff($hotel_meta['tf_rooms'], [$post_id]);
			} else {
				$hotel_meta['tf_rooms'] = '';
			}
			
			update_post_meta($assign_hotel_id, 'tf_hotels_opt', $hotel_meta);
		}
	}

	static function calculate_days( $check_in, $check_out ) {
		if ( $check_in && $check_out ) {
			$check_in_stt   = strtotime( $check_in . ' +1 day' );
			$check_out_stt  = strtotime( $check_out );
			$day_difference = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
		}

		return $day_difference;
	}

	static function tf_term_count( $filter, $destination, $default_count ) {

		if ( $destination == '' ) {
			return $default_count;
		}

		$term_count = array();

		$args = array(
			'post_type'      => 'tf_hotel',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'hotel_location',
					'field'    => 'slug',
					'terms'    => $destination
				)
			)
		);

		$loop = new \WP_Query( $args );

		if ( $loop->have_posts() ) :
			while ( $loop->have_posts() ) : $loop->the_post();

				if ( has_term( $filter, 'tf_filters', get_the_ID() ) == true ) {
					$term_count[] = 'true';
				}

			endwhile;
		endif;

		return count( $term_count );

		wp_reset_postdata();
	}
}