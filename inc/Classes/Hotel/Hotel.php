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
		\Tourfic\App\Without_Payment\Hotel_Offline_Booking::instance();

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
		//add_filter( 'comment_form_fields', array($this, 'tf_move_comment_field') );
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
		<?php }elseif( !empty($design) && 3==$design ){ ?>
			<form class="tf-archive-search-box-wrapper <?php echo esc_attr( $classes ); ?>" id="tf_hotel_aval_check" method="get" autocomplete="off" action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>">

				<div class="tf-date-selection-form">
				<div class="tf-date-select-box tf-flex tf-flex-gap-8">
					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn tf-pick-drop-location full-width">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_257_3711)">
                                        <path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_257_3711">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Location", "tourfic"); ?></h5>
									<input type="text" name="place-name" <?php echo $hotel_location_field_required != 1 ? '' : 'required'; ?> id="tf-location" class="" placeholder="<?php esc_html_e( 'Enter Location', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="place" id="tf-search-hotel" class="tf-place-input">
								</div>
							</div>
						</div>

					</div>

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn full-width">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Check-in & Check-out Date", "tourfic"); ?></h5>
									<input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo Helper::tfopt( 'date_hotel_search' ) ? 'required' : ''; ?>>
								</div>
							</div>
						</div>

					</div>

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn full-width">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M9.99992 10.8333C12.3011 10.8333 14.1666 8.96785 14.1666 6.66667C14.1666 4.36548 12.3011 2.5 9.99992 2.5C7.69873 2.5 5.83325 4.36548 5.83325 6.66667C5.83325 8.96785 7.69873 10.8333 9.99992 10.8333ZM9.99992 10.8333C11.768 10.8333 13.4637 11.5357 14.714 12.786C15.9642 14.0362 16.6666 15.7319 16.6666 17.5M9.99992 10.8333C8.23181 10.8333 6.53612 11.5357 5.28587 12.786C4.03563 14.0362 3.33325 15.7319 3.33325 17.5" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Guests & Rooms", "tourfic"); ?></h5>
									<div class="tf_selectperson-wrap">
										<div class="tf_input-inner">
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

								</div>
							</div>
						</div>

					</div>
				</div>

				<div class="tf-driver-location-box">
					<div class="tf-submit-button">
						<input type="hidden" name="type" value="tf_hotel" class="tf-post-type"/>
                        <button type="submit"><?php esc_html_e( apply_filters("tf_hotel_search_form_submit_button_text", 'Search' ), 'tourfic' ); ?> <i class="ri-search-line"></i></button>
					</div>
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
        <?php } elseif (!empty($design) && 4 == $design) { ?>
            <form class="tf-archive-search-box-wrapper tf-search__form tf-shortcode-design-4 <?php echo esc_attr($classes); ?>" id="tf_hotel_aval_check" method="get" autocomplete="off" action="<?php echo esc_url(Helper::tf_booking_search_action()); ?>">
                <fieldset class="tf-search__form__fieldset">
                    <!-- Destination -->
                    <div class="tf-search__form__fieldset__left">
                        <label for="tf-search__form-destination" class="tf-search__form__label">
                            <?php echo esc_html_e("Locations", "tourfic"); ?>
                        </label>
                        <div class="tf-search__form__field" id="locationField">
                            <input type="text" name="place-name" <?php echo $hotel_location_field_required != 1 ? '' : 'required'; ?> id="tf-location" class="tf-search__form__input" placeholder="<?php esc_html_e('Where you wanna stay?', 'tourfic'); ?>" value="">
                            <input type="hidden" name="place" id="tf-search-hotel" class="tf-place-input">
                            <span class="tf-search__form__field__icon icon--location">
							<svg width="12" height="17" viewBox="0 0 12 17" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M5.25 15.625C3.625 13.5938 0 8.75 0 6C0 2.6875 2.65625 0 6 0C9.3125 0 12 2.6875 12 6C12 8.75 8.34375 13.5938 6.71875 15.625C6.34375 16.0938 5.625 16.0938 5.25 15.625ZM6 8C7.09375 8 8 7.125 8 6C8 4.90625 7.09375 4 6 4C4.875 4 4 4.90625 4 6C4 7.125 4.875 8 6 8Z" fill="white" />
							</svg>
						</span>
                        </div>
                    </div>

                    <div class="tf-search__form__fieldset__middle">


                        <!-- Adult Person -->
                        <div class="tf-search__form__group tf_selectperson-wrap">
                            <label for="tf-search__form-adult" class="tf-search__form__label">
                                <?php echo esc_html_e('Adult Person', 'tourfic'); ?>
                            </label>
                            <div class="tf-search__form__field tf-mx-width">
                                <div class="tf-search__form__field__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="40" viewBox="0 0 41 40" fill="none">
                                        <path d="M20.2222 20C22.3439 20 24.3787 19.1571 25.879 17.6569C27.3793 16.1566 28.2222 14.1217 28.2222 12C28.2222 9.87827 27.3793 7.84344 25.879 6.34315C24.3787 4.84285 22.3439 4 20.2222 4C18.1004 4 16.0656 4.84285 14.5653 6.34315C13.065 7.84344 12.2222 9.87827 12.2222 12C12.2222 14.1217 13.065 16.1566 14.5653 17.6569C16.0656 19.1571 18.1004 20 20.2222 20ZM17.3659 23C11.2097 23 6.22217 27.9875 6.22217 34.1437C6.22217 35.1687 7.05342 36 8.07842 36H32.3659C33.3909 36 34.2222 35.1687 34.2222 34.1437C34.2222 27.9875 29.2347 23 23.0784 23H17.3659Z" fill="#3E64E0" />
                                    </svg>
                                </div>
                                <div class="tf-search__form__field__incdec">
                                    <input type="number" name="adults" id="adults" class="tf-search__form__field__input field--title" min="1" value="1">
                                    <span class="tf-search__form__field__incdre__inc form--span acr-inc">
									<svg xmlns="http://www.w3.org/2000/svg" width="33" height="25" viewBox="0 0 33 25" fill="none">
										<rect x="1.25" y="1" width="31" height="23" rx="5.5" stroke="#3E64E0" />
										<path d="M10.75 12.9998H22.4167M16.5833 7.1665V18.8332" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</span>
                                    <span class="tf-search__form__field__incdre__dec form--span acr-dec">
									<svg xmlns="http://www.w3.org/2000/svg" width="33" height="24" viewBox="0 0 33 24" fill="none">
										<rect x="0.722168" y="0.5" width="31" height="23" rx="5.5" stroke="#3E64E0" />
										<path d="M10.2222 12.5H21.8888" stroke="white" stroke-width="2" stroke-linecap="round" stroke-line join="round" />
									</svg>
								</span>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="tf-search__form__divider"></div>

                        <!-- Children -->
                        <?php if (empty($disable_hotel_child_search)) : ?>
                            <div class="tf-search__form__group tf_selectperson-wrap">
                                <label for="tf-search__form-children" class="tf-search__form__label">
                                    <?php echo esc_html_e('Children', 'tourfic'); ?>
                                </label>
                                <div class="tf-search__form__field tf-mx-width">
                                    <div class="tf-search__form__field__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="40" viewBox="0 0 26 40" fill="none">
                                            <path d="M7.99873 5C7.99873 3.67392 8.52552 2.40215 9.4632 1.46447C10.4009 0.526784 11.6727 0 12.9987 0C14.3248 0 15.5966 0.526784 16.5343 1.46447C17.472 2.40215 17.9987 3.67392 17.9987 5C17.9987 6.32608 17.472 7.59785 16.5343 8.53553C15.5966 9.47322 14.3248 10 12.9987 10C11.6727 10 10.4009 9.47322 9.4632 8.53553C8.52552 7.59785 7.99873 6.32608 7.99873 5ZM11.7487 30V37.5C11.7487 38.8828 10.6315 40 9.24873 40C7.86592 40 6.74873 38.8828 6.74873 37.5V22.4844L5.11592 25.0781C4.38155 26.25 2.83467 26.5938 1.67061 25.8594C0.506547 25.125 0.147172 23.5859 0.881547 22.4219L3.99873 17.4766C5.94405 14.375 9.34248 12.5 12.9987 12.5C16.655 12.5 20.0534 14.375 21.9987 17.4688L25.1159 22.4219C25.8503 23.5938 25.4987 25.1328 24.3347 25.8672C23.1706 26.6016 21.6237 26.25 20.8894 25.0859L19.2487 22.4844V37.5C19.2487 38.8828 18.1315 40 16.7487 40C15.3659 40 14.2487 38.8828 14.2487 37.5V30H11.7487Z" fill="#3E64E0" />
                                        </svg>
                                    </div>
                                    <div class="tf-search__form__field__incdec">
                                        <input type="number" name="children" id="children" class="tf-search__form__field__input field--title" min="0" value="0">
                                        <span class="tf-search__form__field__incdre__inc form--span acr-inc">
										<svg xmlns="http://www.w3.org/2000/svg" width="33" height="25" viewBox="0 0 33 25" fill="none">
											<rect x="1.25" y="1" width="31" height="23" rx="5.5" stroke="#3E64E0" />
											<path d="M10.75 12.9998H22.4167M16.5833 7.1665V18.8332" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
										</svg>
									</span>
                                        <span class="tf-search__form__field__incdre__dec form--span acr-dec">
										<svg xmlns="http://www.w3.org/2000/svg" width="33" height="24" viewBox="0 0 33 24" fill="none">
											<rect x="0.722168" y="0.5" width="31" height="23" rx="5.5" stroke="#3E64E0" />
											<path d="M10.2222 12.5H21.8888" stroke="white" stroke-width="2" stroke-linecap="round" stroke-line join="round" />
										</svg>
									</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Divider -->
                            <div class="tf-search__form__divider"></div>
                        <?php endif; ?>
                        <!-- Rooms -->
                        <div class="tf-search__form__group tf_selectperson-wrap">
                            <label for="tf-search__form-rooms" class="tf-search__form__label">
                                <?php echo esc_html_e('Rooms', 'tourfic'); ?>
                            </label>
                            <div class="tf-search__form__field tf-mx-width">
                                <div class="tf-search__form__field__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="41" height="40" viewBox="0 0 41 40" fill="none">
                                        <path d="M38.99 19.9635C38.99 21.1586 38.0002 22.0947 36.8784 22.0947H34.7667L34.8129 32.7309C34.8129 32.9102 34.7997 33.0894 34.7799 33.2687V34.3443C34.7799 35.8116 33.5987 37 32.1403 37H31.0845C31.0119 37 30.9393 37 30.8668 36.9934C30.7744 37 30.682 37 30.5896 37L28.4449 36.9934H26.8612C25.4028 36.9934 24.2216 35.8049 24.2216 34.3376V32.7442V28.495C24.2216 27.3199 23.278 26.3704 22.11 26.3704H17.8867C16.7186 26.3704 15.775 27.3199 15.775 28.495V32.7442V34.3376C15.775 35.8049 14.5938 36.9934 13.1354 36.9934H11.5517H9.44663C9.34765 36.9934 9.24866 36.9867 9.14968 36.9801C9.07049 36.9867 8.9913 36.9934 8.91212 36.9934H7.85629C6.39792 36.9934 5.21672 35.8049 5.21672 34.3376V26.9016C5.21672 26.8418 5.21672 26.7754 5.22331 26.7157V22.0881H3.11166C1.92385 22.0881 1 21.1586 1 19.9568C1 19.3593 1.19797 18.8282 1.65989 18.3634L18.5729 3.53115C19.0349 3.06639 19.5628 3 20.0247 3C20.4866 3 21.0146 3.13279 21.4105 3.46475L38.2642 18.37C38.7921 18.8348 39.056 19.3659 38.99 19.9635Z" fill="#3E64E0" />
                                    </svg>
                                </div>
                                <div class="tf-search__form__field__incdec">
                                    <input type="number" name="room" id="room" class="tf-search__form__field__input field--title" min="1" value="1">
                                    <span class="tf-search__form__field__incdre__inc form--span acr-inc">
									<svg xmlns="http://www.w3.org/2000/svg" width="33" height="25" viewBox="0 0 33 25" fill="none">
										<rect x="1.25" y="1" width="31" height="23" rx="5.5" stroke="#3E64E0" />
										<path d="M10.75 12.9998H22.4167M16.5833 7.1665V18.8332" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
								</span>
                                    <span class="tf-search__form__field__incdre__dec form--span acr-dec">
									<svg xmlns="http://www.w3.org/2000/svg" width="33" height="24" viewBox="0 0 33 24" fill="none">
										<rect x="0.722168" y="0.5" width="31" height="23" rx="5.5" stroke="#3E64E0" />
										<path d="M10.2222 12.5H21.8888" stroke="white" stroke-width="2" stroke-linecap="round" stroke-line join="round" />
									</svg>
								</span>
                                </div>
                            </div>
                        </div>
                        <!-- Divider -->
                        <div class="tf-search__form__divider"></div>
                        <!-- Check-in -->
                        <div class="tf-search__form__group tf-checkin-group">
                            <div class="tf_check_inout_dates">
                                <label for="tf-search__form-checkin" class="tf-search__form__label">
                                    <?php echo esc_html_e('Check-In', 'tourfic'); ?>
                                </label>
                                <div class="tf-search__form__field">
                                    <div class="tf-search__form__field__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="40" viewBox="0 0 36 40" fill="none">
                                            <g clip-path="url(#clip0_2862_2140)">
                                                <path d="M10.7778 0C12.1606 0 13.2778 1.11719 13.2778 2.5V5H23.2778V2.5C23.2778 1.11719 24.395 0 25.7778 0C27.1606 0 28.2778 1.11719 28.2778 2.5V5H32.0278C34.0981 5 35.7778 6.67969 35.7778 8.75V12.5H0.777832V8.75C0.777832 6.67969 2.45752 5 4.52783 5H8.27783V2.5C8.27783 1.11719 9.39502 0 10.7778 0ZM0.777832 15H35.7778V36.25C35.7778 38.3203 34.0981 40 32.0278 40H4.52783C2.45752 40 0.777832 38.3203 0.777832 36.25V15ZM5.77783 21.25V23.75C5.77783 24.4375 6.34033 25 7.02783 25H9.52783C10.2153 25 10.7778 24.4375 10.7778 23.75V21.25C10.7778 20.5625 10.2153 20 9.52783 20H7.02783C6.34033 20 5.77783 20.5625 5.77783 21.25ZM15.7778 21.25V23.75C15.7778 24.4375 16.3403 25 17.0278 25H19.5278C20.2153 25 20.7778 24.4375 20.7778 23.75V21.25C20.7778 20.5625 20.2153 20 19.5278 20H17.0278C16.3403 20 15.7778 20.5625 15.7778 21.25ZM27.0278 20C26.3403 20 25.7778 20.5625 25.7778 21.25V23.75C25.7778 24.4375 26.3403 25 27.0278 25H29.5278C30.2153 25 30.7778 24.4375 30.7778 23.75V21.25C30.7778 20.5625 30.2153 20 29.5278 20H27.0278ZM5.77783 31.25V33.75C5.77783 34.4375 6.34033 35 7.02783 35H9.52783C10.2153 35 10.7778 34.4375 10.7778 33.75V31.25C10.7778 30.5625 10.2153 30 9.52783 30H7.02783C6.34033 30 5.77783 30.5625 5.77783 31.25ZM17.0278 30C16.3403 30 15.7778 30.5625 15.7778 31.25V33.75C15.7778 34.4375 16.3403 35 17.0278 35H19.5278C20.2153 35 20.7778 34.4375 20.7778 33.75V31.25C20.7778 30.5625 20.2153 30 19.5278 30H17.0278ZM25.7778 31.25V33.75C25.7778 34.4375 26.3403 35 27.0278 35H29.5278C30.2153 35 30.7778 34.4375 30.7778 33.75V31.25C30.7778 30.5625 30.2153 30 29.5278 30H27.0278C26.3403 30 25.7778 30.5625 25.7778 31.25Z" fill="#3E64E0" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_2862_2140">
                                                    <rect width="35" height="40" fill="white" transform="translate(0.777832)" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <div class="tf_checkin_dates tf-flex tf-flex-align-center">
                                        <span class="date field--title"><?php echo esc_html(gmdate('d')); ?></span>
                                        <div class="tf-search__form__field__mthyr">
                                            <span class="month form--span"><?php echo esc_html(gmdate('M')); ?></span>
                                            <span class="year form--span"><?php echo esc_html(gmdate('Y')); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="check-in-out-date" class="tf-check-in-out-date tf-check-inout-hidden" onkeypress="return false;" placeholder="<?php esc_attr_e('Check-in - Check-out', 'tourfic'); ?>" <?php echo Helper::tfopt('date_hotel_search') ? 'required' : ''; ?>>
                        </div>
                        <!-- label to -->
                        <div class="tf_checkin_to_label">
                            <?php echo esc_html_e('To', 'tourfic'); ?>
                        </div>
                        <!-- Check-out -->
                        <div class="tf-search__form__group tf_check_inout_dates tf-checkout-group">
                            <label for="tf-search__form-checkout" class="tf-search__form__label">
                                <?php echo esc_html_e('Check-Out', 'tourfic'); ?>
                            </label>
                            <div class="tf-search__form__field">
                                <div class="tf-search__form__field__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="40" viewBox="0 0 36 40" fill="none">
                                        <g clip-path="url(#clip0_2862_2140)">
                                            <path d="M10.7778 0C12.1606 0 13.2778 1.11719 13.2778 2.5V5H23.2778V2.5C23.2778 1.11719 24.395 0 25.7778 0C27.1606 0 28.2778 1.11719 28.2778 2.5V5H32.0278C34.0981 5 35.7778 6.67969 35.7778 8.75V12.5H0.777832V8.75C0.777832 6.67969 2.45752 5 4.52783 5H8.27783V2.5C8.27783 1.11719 9.39502 0 10.7778 0ZM0.777832 15H35.7778V36.25C35.7778 38.3203 34.0981 40 32.0278 40H4.52783C2.45752 40 0.777832 38.3203 0.777832 36.25V15ZM5.77783 21.25V23.75C5.77783 24.4375 6.34033 25 7.02783 25H9.52783C10.2153 25 10.7778 24.4375 10.7778 23.75V21.25C10.7778 20.5625 10.2153 20 9.52783 20H7.02783C6.34033 20 5.77783 20.5625 5.77783 21.25ZM15.7778 21.25V23.75C15.7778 24.4375 16.3403 25 17.0278 25H19.5278C20.2153 25 20.7778 24.4375 20.7778 23.75V21.25C20.7778 20.5625 20.2153 20 19.5278 20H17.0278C16.3403 20 15.7778 20.5625 15.7778 21.25ZM27.0278 20C26.3403 20 25.7778 20.5625 25.7778 21.25V23.75C25.7778 24.4375 26.3403 25 27.0278 25H29.5278C30.2153 25 30.7778 24.4375 30.7778 23.75V21.25C30.7778 20.5625 30.2153 20 29.5278 20H27.0278ZM5.77783 31.25V33.75C5.77783 34.4375 6.34033 35 7.02783 35H9.52783C10.2153 35 10.7778 34.4375 10.7778 33.75V31.25C10.7778 30.5625 10.2153 30 9.52783 30H7.02783C6.34033 30 5.77783 30.5625 5.77783 31.25ZM17.0278 30C16.3403 30 15.7778 30.5625 15.7778 31.25V33.75C15.7778 34.4375 16.3403 35 17.0278 35H19.5278C20.2153 35 20.7778 34.4375 20.7778 33.75V31.25C20.7778 30.5625 20.2153 30 19.5278 30H17.0278ZM25.7778 31.25V33.75C25.7778 34.4375 26.3403 35 27.0278 35H29.5278C30.2153 35 30.7778 34.4375 30.7778 33.75V31.25C30.7778 30.5625 30.2153 30 29.5278 30H27.0278C26.3403 30 25.7778 30.5625 25.7778 31.25Z" fill="#3E64E0" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_2862_2140">
                                                <rect width="35" height="40" fill="white" transform="translate(0.777832)" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="tf_checkout_dates tf-flex tf-flex-align-center">
                                    <span class="date field--title"><?php echo esc_html(gmdate('d')); ?></span>
                                    <div class="tf-search__form__field__mthyr">
                                        <span class="month form--span"><?php echo esc_html(gmdate('M')); ?></span>
                                        <span class="year form--span"><?php echo esc_html(gmdate('Y')); ?></span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tf-search__form__fieldset__right">
                        <!-- Submit Button -->
                        <input type="hidden" name="type" value="tf_hotel" class="tf-post-type" />
                        <button type="submit" class="tf-search__form__submit mh-btn">
                            <?php esc_html_e(apply_filters("tf_hotel_search_form_submit_button_text", 'Search Now'), 'tourfic'); ?>
                            <svg class="tf-search__form__submit__icon" width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.75 14.7188L11.5625 10.5312C12.4688 9.4375 12.9688 8.03125 12.9688 6.5C12.9688 2.9375 10.0312 0 6.46875 0C2.875 0 0 2.9375 0 6.5C0 10.0938 2.90625 13 6.46875 13C7.96875 13 9.375 12.5 10.5 11.5938L14.6875 15.7812C14.8438 15.9375 15.0312 16 15.25 16C15.4375 16 15.625 15.9375 15.75 15.7812C16.0625 15.5 16.0625 15.0312 15.75 14.7188ZM1.5 6.5C1.5 3.75 3.71875 1.5 6.5 1.5C9.25 1.5 11.5 3.75 11.5 6.5C11.5 9.28125 9.25 11.5 6.5 11.5C3.71875 11.5 1.5 9.28125 1.5 6.5Z" fill="white" />
                            </svg>
                        </button>
                    </div>
                </fieldset>
            </form>

            <script>
                (function($) {
                    $(document).ready(function() {
                        // flatpickr locale first day of Week
                        <?php Helper::tf_flatpickr_locale("root"); ?>

                        $(".tf_check_inout_dates").on("click", function() {
                            $(".tf-check-in-out-date").trigger("click");
                        });
                        $(".tf-check-in-out-date").flatpickr({
                            enableTime: false,
                            mode: "range",
                            dateFormat: "Y/m/d",
                            minDate: "today",

                            // flatpickr locale
                            <?php Helper::tf_flatpickr_locale(); ?>

                            onReady: function(selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                dateSetToFields(selectedDates, instance);
                            },
                            onChange: function(selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                dateSetToFields(selectedDates, instance);
                            }
                        });

                        function dateSetToFields(selectedDates, instance) {
                            console.log(selectedDates);
                            if (selectedDates.length === 2) {
                                const monthNames = [
                                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                ];
                                if (selectedDates[0]) {
                                    const startDate = selectedDates[0];
                                    $(".tf_checkin_dates span.date").html(startDate.getDate());
                                    $(".tf_checkin_dates span.month").html(monthNames[startDate.getMonth()]);
                                    $(".tf_checkin_dates span.year").html(startDate.getFullYear());
                                }
                                if (selectedDates[1]) {
                                    const endDate = selectedDates[1];
                                    $(".tf_checkout_dates span.date").html(endDate.getDate());
                                    $(".tf_checkout_dates span.month").html(monthNames[endDate.getMonth()]);
                                    $(".tf_checkout_dates span.year").html(endDate.getFullYear());
                                }
                            }
                        }
                    });
                })(jQuery);
            </script>
        <?php } else { ?>
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
                        <button class="tf_button tf-submit btn-styled" type="submit"><?php echo esc_html(apply_filters("tf_hotel_search_form_submit_button_text", esc_html__('Search', 'tourfic' ))); ?></button>
                    </div>

                </div>

            </form>

            <script>
                (function ($) {
                    $(document).ready(function () {

                        // flatpickr First Day of Week
						<?php Helper::tf_flatpickr_locale( 'root' ); ?>

						const regexMap = {
                            'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                            'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                            'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                            'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                            'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                            'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                            'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                            'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                            'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                        };
                        const dateRegex = regexMap['<?php echo $hotel_date_format_for_users; ?>'];

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
                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                    return `${date1} - ${date2}`;
                                });
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
									return `${date1} - ${date2}`;
								});
								instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
									return `${d1} - ${d2}`;
								});
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

		if ( $tf_hotel_selected_template == "design-1" ) { ?>
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
						const regexMap = {
                            'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                            'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                            'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                            'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                            'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                            'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                            'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                            'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                            'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                        };
                        const dateRegex = regexMap['<?php echo $hotel_date_format_for_users; ?>'];

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
                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
									return `${date1} - ${date2}`;
								});
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
									return `${date1} - ${date2}`;
								});
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

		<?php } elseif($tf_hotel_selected_template == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) { ?>
            <form id="tf-single-hotel-avail" class="tf-hotel-booking-sidebar tf-booking-form" method="get" autocomplete="off">

				<?php wp_nonce_field( 'check_room_avail_nonce', 'tf_room_avail_nonce' ); ?>
                <div class="tf_booking-dates">
                    <label class="tf_label-row">
                        <div class="tf_form-inner tf-field-group">
                            <svg class="tf-hotel-booking-form-icon" width="13" height="15" viewBox="0 0 13 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.0625 0.75C3.28125 0.75 3.5 0.96875 3.5 1.1875V2.5H8.75V1.1875C8.75 0.96875 8.94141 0.75 9.1875 0.75C9.40625 0.75 9.625 0.96875 9.625 1.1875V2.5H10.5C11.457 2.5 12.25 3.29297 12.25 4.25V13C12.25 13.9844 11.457 14.75 10.5 14.75H1.75C0.765625 14.75 0 13.9844 0 13V4.25C0 3.29297 0.765625 2.5 1.75 2.5H2.625V1.1875C2.625 0.96875 2.81641 0.75 3.0625 0.75ZM11.375 6H8.53125V7.96875H11.375V6ZM11.375 8.84375H8.53125V11.0312H11.375V8.84375ZM11.375 11.9062H8.53125V13.875H10.5C10.9648 13.875 11.375 13.4922 11.375 13V11.9062ZM7.65625 11.0312V8.84375H4.59375V11.0312H7.65625ZM4.59375 13.875H7.65625V11.9062H4.59375V13.875ZM3.71875 11.0312V8.84375H0.875V11.0312H3.71875ZM0.875 11.9062V13C0.875 13.4922 1.25781 13.875 1.75 13.875H3.71875V11.9062H0.875ZM0.875 7.96875H3.71875V6H0.875V7.96875ZM4.59375 7.96875H7.65625V6H4.59375V7.96875ZM10.5 3.375H1.75C1.25781 3.375 0.875 3.78516 0.875 4.25V5.125H11.375V4.25C11.375 3.78516 10.9648 3.375 10.5 3.375Z" fill="#6E655E"/>
                            </svg>
                            <input type="text" name="check-in-out-date" id="check-in-out-date" class="tf-field tf-check-in-out-date" onkeypress="return false;"
                                   placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr( $check_in_out ) . '"' : '' ?> required
                                   style="width: 100% !important;">
                        </div>
                    </label>
                </div>
                <div class="tf-booking-person">
                    <div class="tf-field-group tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <svg class="tf-hotel-booking-form-icon" width="13" height="15" viewBox="0 0 13 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.125 7.75C8.03906 7.75 9.625 6.19141 9.625 4.25C9.625 2.33594 8.03906 0.75 6.125 0.75C4.18359 0.75 2.625 2.33594 2.625 4.25C2.625 6.19141 4.18359 7.75 6.125 7.75ZM6.125 1.625C7.54688 1.625 8.75 2.82812 8.75 4.25C8.75 5.69922 7.54688 6.875 6.125 6.875C4.67578 6.875 3.5 5.69922 3.5 4.25C3.5 2.82812 4.67578 1.625 6.125 1.625ZM7.49219 9.0625H4.73047C2.10547 9.0625 0 11.1953 0 13.8203C0 14.3398 0.410156 14.75 0.929688 14.75H11.293C11.8125 14.75 12.25 14.3398 12.25 13.8203C12.25 11.1953 10.1172 9.0625 7.49219 9.0625ZM11.293 13.875H0.929688C0.902344 13.875 0.875 13.8477 0.875 13.8203C0.875 11.6875 2.59766 9.9375 4.73047 9.9375H7.49219C9.625 9.9375 11.375 11.6875 11.375 13.8203C11.375 13.8477 11.3203 13.875 11.293 13.875Z" fill="#6E655E"/>
                                </svg>
                                <?php esc_html_e( 'Adults', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M19 11H5V13H19V11Z" fill="#FF6B00"/>
                                    </svg>
                                </div>
                                <input type="number" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? esc_attr( $adults ) : '1'; ?>">
                                <div class="acr-inc">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M11 11V5H13V11H19V13H13V19H11V13H5V11H11Z" fill="#FF6B00"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tf-field-group tf_acrselection tf_acrselection-child">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <svg class="tf-hotel-booking-form-icon" width="10" height="15" viewBox="0 0 10 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.125 2.5C7.125 3.48438 6.33203 4.25 5.34766 4.25C4.39062 4.25 3.59766 3.48438 3.59766 2.5C3.59766 1.54297 4.39062 0.75 5.34766 0.75C6.33203 0.75 7.125 1.54297 7.125 2.5ZM5.34766 1.625C4.88281 1.625 4.47266 2.03516 4.47266 2.5C4.47266 2.99219 4.88281 3.375 5.34766 3.375C5.83984 3.375 6.25 2.99219 6.25 2.5C6.25 2.03516 5.83984 1.625 5.34766 1.625ZM1.79297 9.30859C1.68359 9.5 1.41016 9.58203 1.19141 9.44531C1 9.30859 0.917969 9.0625 1.05469 8.84375L2.25781 6.875C2.91406 5.78125 4.08984 5.125 5.375 5.125C6.63281 5.125 7.80859 5.78125 8.46484 6.875L9.66797 8.84375C9.80469 9.0625 9.72266 9.30859 9.53125 9.44531C9.3125 9.58203 9.03906 9.5 8.92969 9.30859L7.72656 7.33984C7.67188 7.25781 7.61719 7.17578 7.53516 7.09375V14.3125C7.53516 14.5586 7.34375 14.75 7.09766 14.75C6.87891 14.75 6.66016 14.5586 6.66016 14.3125V11.25H4.0625V14.3125C4.0625 14.5586 3.84375 14.75 3.625 14.75C3.37891 14.75 3.1875 14.5586 3.1875 14.3125V7.09375C3.10547 7.17578 3.05078 7.25781 2.99609 7.33984L1.79297 9.30859ZM6.66016 10.375V6.35547C6.27734 6.13672 5.83984 6 5.375 6C4.88281 6 4.44531 6.13672 4.0625 6.35547V10.375H6.66016Z" fill="#6E655E"/>
                                </svg>
                                <?php esc_html_e( 'Children', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M19 11H5V13H19V11Z" fill="#FF6B00"/>
                                    </svg>
                                </div>
                                <input type="number" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? esc_attr( $child ) : '0'; ?>">
                                <div class="acr-inc">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M11 11V5H13V11H19V13H13V19H11V13H5V11H11Z" fill="#FF6B00"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
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
                        <button class="tf-btn-normal btn-primary tf-submit" type="submit"><?php echo esc_html( $tf_hotel_book_avaibality_button_text ); ?></button>
                        <span class="tf-hotel-error-msg"><?php echo esc_html__('Please select check in and check out date', 'tourfic') ?></span>
                    </div>
                </div>

            </form>
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

					const regexMap = {
						'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
						'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
						'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
						'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
						'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
						'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
						'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
						'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
						'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
					};
					const dateRegex = regexMap['<?php echo $hotel_date_format_for_users; ?>'];

                    const checkinoutdateange = flatpickr(".tf-hotel-booking-sidebar #check-in-out-date", {
                        enableTime: false,
                        mode: "range",
                        minDate: "today",
                        altInput: true,
                        altFormat: '<?php echo esc_html( $hotel_date_format_for_users ); ?>',
                        dateFormat: "Y/m/d",
                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
								return `${date1} - ${date2}`;
							});
                            instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
								return `${d1} - ${d2}`;
							});
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
								return `${date1} - ${date2}`;
							});
                            instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
								return `${d1} - ${d2}`;
							});
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


	static function hotel_booking_popup( $post_id, $room_id, $adult, $child ) {

		$meta                     = get_post_meta( $post_id, 'tf_hotels_opt', true );
		$room_meta                    = get_post_meta( $room_id, 'tf_room_opt', true );
		$enable_airport_service   = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';
		$airport_service_type     = ! empty( $meta['airport_service_type'] ) ? $meta['airport_service_type'] : '';
		$room_book_by             = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
		$room_book_url            = ! empty( $meta['booking-url'] ) ? $meta['booking-url'] : '';
		$room_allow_deposit       = ! empty( $room_meta['allow_deposit'] ) ? $room_meta['allow_deposit'] : '';
		$room_deposit_type       = ! empty( $room_meta['deposit_type'] ) ? $room_meta['deposit_type'] : '';
		$room_deposit_amount       = ! empty( $room_meta['deposit_amount'] ) ? $room_meta['deposit_amount'] : 0;
		$airport_service_type     = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $enable_airport_service ) && ! empty( $airport_service_type ) ? $airport_service_type : null;
		$enable_guest_info_global = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( Helper::tfopt( 'enable_guest_info' ) ) ? Helper::tfopt( 'enable_guest_info' ) : 0;
		$enable_guest_info        = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['enable_guest_info'] ) ? $meta['enable_guest_info'] : $enable_guest_info_global;
		$hotel_guest_details_text = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( Helper::tfopt( 'hotel_guest_details_text' ) ) ? Helper::tfopt( 'hotel_guest_details_text' ) : '';
		?>
        <!-- Loader Image -->
        <div id="tour_room_details_loader">
            <div id="tour-room-details-loader-img">
                <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="Loader">
            </div>
        </div>
        <div class="tf-withoutpayment-booking-confirm">
            <div class="tf-confirm-popup">
                <div class="tf-booking-times">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
                        <path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
                        <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
                        </svg>
                    </span>
                </div>
                <img src="<?php echo TF_ASSETS_APP_URL ?>images/thank-you.gif" alt="Thank You">
                <h2>
					<?php
					$booking_confirmation_msg = ! empty( Helper::tfopt( 'hotel-booking-confirmation-msg' ) ) ? Helper::tfopt( 'hotel-booking-confirmation-msg' ) : esc_html__('Booked Successfully', 'tourfic');
					echo wp_kses_post( $booking_confirmation_msg );
					?>
                </h2>
            </div>
        </div>
        <div class="tf-withoutpayment-booking tf-hotel-withoutpayment-booking">
            <div class="tf-withoutpayment-popup">
                <div class="tf-booking-tabs">
                    <div class="tf-booking-tab-menu">
                        <ul>
							<?php if ( $airport_service_type && ( $room_book_by != 2 || empty( $room_book_url ) ) ) { ?>
                                <li class="tf-booking-step tf-booking-step-1 active">
                                    <i class="ri-price-tag-3-line"></i> <?php echo __( "Airport Service", "tourfic" ); ?>
                                </li>
							<?php }
							if ( $enable_guest_info ) {
								?>
                                <li class="tf-booking-step tf-booking-step-2 <?php echo empty( $airport_service_type ) ? esc_attr( 'active' ) : ''; ?> ">
                                    <i class="ri-group-line"></i> <?php echo __( "Guest details", "tourfic" ); ?>
                                </li>
							<?php }
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && 3 == $room_book_by ) {
								?>
                                <li class="tf-booking-step tf-booking-step-3 <?php echo empty( $airport_service_type ) && empty( $enable_guest_info ) ? esc_attr( 'active' ) : ''; ?>">
                                    <i class="ri-calendar-check-line"></i> <?php echo __( "Booking Confirmation", "tourfic" ); ?>
                                </li>
							<?php } ?>
                        </ul>
                    </div>
                    <div class="tf-booking-times">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
									<path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
								<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="tf-booking-content-summery">

					<?php
					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $airport_service_type ) { ?>
                        <div class="tf-booking-content tf-hotel-booking-content tf-booking-content-1">
                            <div class="tf-hotel-services-text">
                                <h3><?php echo !empty( tfopt( 'hotel_service_popup_title' ) ) ? __( tfopt( 'hotel_service_popup_title' ), 'tourfic' ) : '' ?></h3>
                                <p><?php echo !empty( tfopt( 'hotel_service_popup_subtile') ) ? __( tfopt( 'hotel_service_popup_subtile'), 'tourfic' ) : '' ; ?></p>
                            </div>
                            <div class="tf-booking-content-service">
								<?php if ( ! empty( $airport_service_type ) ) { ?>
									<?php foreach ( $airport_service_type as $key => $single_service_type ) {
										$airport_service = self::tf_hotel_airport_service_title_price( $post_id, $adult, $child, $single_service_type );
										?>
                                        <div class="tf-single-hotel-service tour-extra-single">
                                            <label for="service-<?php echo esc_attr( $key ) . '_' . $room_id; ?>">
                                                <div class="tf-service-radio">
                                                    <input type="radio" value="<?php echo esc_attr( $single_service_type ); ?>" id="service-<?php echo esc_attr( $key) . '_' . $room_id; ?>" name="airport_service">
                                                </div>
                                                <div class="tf-service-content">
                                                    <h5>
														<?php
														if ( "pickup" == $single_service_type ) {
															_e( 'Pickup Service', 'tourfic' );
														}
														if ( "dropoff" == $single_service_type ) {
															_e( 'Drop-off Service', 'tourfic' );
														}
														if ( "both" == $single_service_type ) {
															_e( 'Pickup & Drop-off Service', 'tourfic' );
														}
														?>
                                                    </h5>
													<p><?php echo $airport_service['title']; ?> = <?php echo wc_price( $airport_service['price'] ); ?></p>
                                                </div>
                                            </label>
                                        </div>
									<?php } ?>
									<div class="tf-single-hotel-service tour-extra-single">
										<label for="service-no_<?php echo esc_attr($room_id); ?>">
											<div class="tf-service-radio">
												<input type="radio" value="" id="service-no_<?php echo esc_attr($room_id); ?>" name="airport_service">
											</div>
											<div class="tf-service-content">
												<h5>
													<?php echo esc_html__("No Service", 'tourfic'); ?>
												</h5>
											</div>
										</label>
										<p></p>
									</div>
								<?php } ?>

                            </div>
                        </div>
					<?php }
					if ( $enable_guest_info ) {
						?>
                        <!-- Popup Traveler Info -->
                        <div class="tf-booking-content tf-booking-content-2 <?php echo empty( $airport_service_type ) ? esc_attr( 'show' ) : ''; ?>">
                            <p><?php echo __( $hotel_guest_details_text, "tourfic" ); ?></p>
                            <div class="tf-booking-content-traveller">
                                <div class="tf-traveller-info-box"></div>
                            </div>
                        </div>
					<?php }
					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && 3 == $room_book_by ) {
						?>
                        <!-- Popup Booking Confirmation -->
                        <div class="tf-booking-content tf-booking-content-3 <?php echo empty( $airport_service_type ) && empty( $enable_guest_info ) ? esc_attr( 'show' ) : ''; ?>">
                            <p><?php _e( $hotel_guest_details_text, "tourfic" ); ?></p>
                            <div class="tf-booking-content-traveller">
                                <div class="tf-single-tour-traveller">
                                    <h4><?php echo __( "Billing details", "tourfic" ); ?></h4>
                                    <div class="traveller-info billing-details">
										<?php
										$confirm_book_fields = ! empty( Helper::tfopt( 'hotel-book-confirm-field' ) ) ? Helper::tf_data_types( Helper::tfopt( 'hotel-book-confirm-field' ) ) : '';

										if ( empty( $confirm_book_fields ) ) {
											?>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_first_name"><?php echo __( "First Name", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_first_name]" id="tf_first_name" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_first_name"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_last_name"><?php echo __( "Last Name", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_last_name]" id="tf_last_name" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_last_name"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_email"><?php echo __( "Email", "tourfic" ); ?></label>
                                                <input type="email" name="booking_confirm[tf_email]" id="tf_email" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_email"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_phone"><?php echo __( "Phone", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_phone]" id="tf_phone" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_phone"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_country"><?php echo __( "Country", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_country]" id="tf_country" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_country"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_street_address"><?php echo __( "Street address", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_street_address]" id="tf_street_address" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_street_address"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_town_city"><?php echo __( "Town / City", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_town_city]" id="tf_town_city" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_town_city"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_state_country"><?php echo __( "State / County", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_state_country]" id="tf_state_country" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_state_country"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_postcode"><?php echo __( "Postcode / ZIP", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_postcode]" id="tf_postcode" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_postcode"></div>
                                            </div>
										<?php } else {
											foreach ( $confirm_book_fields as $field ) {
												$reg_field_required = !empty( $field['reg-field-required'] ) ? $field['reg-field-required'] : 0;
												if ( "text" == $field['reg-fields-type'] || "number" == $field['reg-fields-type'] || "email" == $field['reg-fields-type'] || "date" == $field['reg-fields-type'] ) { ?>
                                                    <div class="traveller-single-info tf-confirm-fields">
                                                        <label for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"><?php echo esc_html( $field['reg-field-label'] ); ?></label>
                                                        <input type="<?php echo esc_attr( $field['reg-fields-type'] ); ?>" name="booking_confirm[<?php echo esc_attr( $field['reg-field-name'] ); ?>]"
                                                               id="<?php echo esc_attr( $field['reg-field-name'] ); ?>" data-required="<?php echo esc_attr( $reg_field_required ); ?>" <?php echo $field['reg-fields-type'] == "number" ? 'min="0"' : ''; ?> />
                                                        <div class="error-text" data-error-for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"></div>
                                                    </div>
												<?php }
												if ( "select" == $field['reg-fields-type'] && ! empty( $field['reg-options'] ) ) { ?>
                                                    <div class="traveller-single-info tf-confirm-fields">
                                                        <label for="<?php echo esc_attr( $field['reg-field-name'] ); ?>">
															<?php echo esc_html( $field['reg-field-label'] ); ?>
                                                        </label>
                                                        <select name="booking_confirm[<?php echo esc_attr( $field['reg-field-name'] ); ?>]" id="<?php echo esc_attr( $field['reg-field-name'] ); ?>"
                                                                data-required="<?php echo !empty($field['reg-field-required']) ? esc_html__($field['reg-field-required']) : esc_html(0); ?>">
                                                            <option value="">
																<?php echo sprintf( __( 'Select One', 'tourfic' ) ); ?>
                                                            </option>
															<?php
															foreach ( $field['reg-options'] as $sfield ) {
																if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) { ?>
                                                                    <option value="<?php echo esc_attr( $sfield['option-value'] ); ?>"><?php echo esc_html( $sfield['option-label'] ); ?></option>
																<?php }
															} ?>
                                                        </select>
                                                        <div class="error-text" data-error-for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"></div>
                                                    </div>
												<?php }
												if ( ( "checkbox" == $field['reg-fields-type'] || "radio" == $field['reg-fields-type'] ) && ! empty( $field['reg-options'] ) ) { ?>
                                                    <div class="traveller-single-info tf-confirm-fields">
                                                        <label for="<?php echo esc_attr( $field['reg-field-name'] ); ?>">
															<?php echo esc_html( $field['reg-field-label'] ); ?>
                                                        </label>
														<?php
														foreach ( $field['reg-options'] as $sfield ) {
															if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) { ?>
                                                                <div class="tf-single-checkbox">
                                                                    <input type="<?php echo esc_attr( $field['reg-fields-type'] ); ?>" name="booking_confirm[<?php echo esc_attr( $field['reg-field-name'] ); ?>][]"
                                                                           id="<?php echo esc_attr( $sfield['option-value'] ); ?>" value="<?php echo esc_html( $sfield['option-value'] ); ?>"
                                                                           data-required="<?php echo $field['reg-field-required']; ?>"/>
                                                                    <label for="<?php echo esc_attr( $sfield['option-value'] ); ?>">
																		<?php echo sprintf( __( '%s', 'tourfic' ), $sfield['option-label'] ); ?>
                                                                    </label>
                                                                </div>
															<?php }
														} ?>
                                                        <div class="error-text" data-error-for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"></div>
                                                    </div>
												<?php }
											}
										} ?>
                                    </div>
                                </div>
                            </div>
                        </div>
					<?php } ?>

                    <!-- Popup Booking Summery -->
                    <div class="tf-booking-summery" style="<?php echo empty( $airport_service_type ) && empty( $enable_guest_info ) && 3 != $room_book_by ? esc_attr( "width: 100%;" ) : ''; ?>">
                        <div class="tf-booking-fixed-summery">
                            <h5><?php echo __( "Booking summery", "tourfic" ); ?></h5>
                            <h4><?php echo get_the_title( $post_id ); ?></h4>
                        </div>
                        <div class="tf-booking-traveller-info">

                        </div>
                    </div>
                </div>

                <!-- Popup Footer Control & Partial Payment -->
                <div class="tf-booking-pagination tf-hotel-booking-pagination">
					<?php if ( empty( $airport_service_type ) && 3 != $room_book_by && empty( $enable_guest_info ) ) { ?>
                        <div class="tf-control-pagination show">
                            <button class="hotel-room-book" type="submit"><?php echo __( "Continue", "tourfic" ); ?></button>
                        </div>
						<?php
					}
					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() &&  $airport_service_type ) { ?>
                        <div class="tf-control-pagination show tf-pagination-content-1">
							<?php
							if ( 3 != $room_book_by && empty( $enable_guest_info ) ) { ?>
                                <button type="submit" class="hotel-room-book"><?php echo !empty( tfopt( 'hotel_service_popup_action' ) ) ? tfopt( 'hotel_service_popup_action' ) : __( "Continue", "tourfic" ); ?></button>
							<?php } else { ?>
                                <a href="#" class="tf-next-control tf-tabs-control"
                                   data-step="<?php echo 3 == $room_book_by && empty( $enable_guest_info ) ? esc_attr( "3" ) : esc_attr( "2" ); ?>"><?php echo !empty( tfopt( 'hotel_service_popup_action' ) ) ? tfopt( 'hotel_service_popup_action' ) : __( "Continue", "tourfic" ); ?></a>
							<?php } ?>
                        </div>
					<?php }
					if ( $enable_guest_info ) { ?>

                        <!-- Popup Traveler Info -->
                        <div class="tf-control-pagination tf-pagination-content-2 <?php echo empty( $airport_service_type ) ? esc_attr( 'show' ) : ''; ?>">
							<?php
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $airport_service_type ) { ?>
                                <a href="#" class="tf-back-control tf-step-back" data-step="1"><i class="fa fa-angle-left"></i><?php echo __( "Back", "tourfic" ); ?></a>
							<?php }
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && 3 == $room_book_by ) {
								?>
                                <a href="#" class="tf-next-control tf-tabs-control tf-traveller-error" data-step="3"><?php echo __( "Continue", "tourfic" ); ?></a>
							<?php } else { ?>
                                <button type="submit" class="tf-traveller-error <?php echo !empty( $room_book_by ) && 3 != $room_book_by ? 'hotel-room-book' : '';  ?>"><?php echo __( "Continue", "tourfic" ); ?></button>
							<?php } ?>
                        </div>
					<?php }
					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && 3 == $room_book_by ) {
						?>

                        <!-- Popup Booking Confirmation -->
                        <div class="tf-control-pagination tf-pagination-content-3 <?php echo empty( $airport_service_type ) && empty( $enable_guest_info ) ? esc_attr( 'show' ) : ''; ?>">
							<?php
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( $airport_service_type || $enable_guest_info ) ) { ?>
                                <a href="#" class="tf-back-control tf-step-back" data-step="2"><i class="fa fa-angle-left"></i><?php echo __( "Back", "tourfic" ); ?></a>
							<?php } ?>
                            <button type="submit" class="tf-hotel-book-confirm-error"><?php echo __( "Continue", "tourfic" ); ?></button>
                        </div>
					<?php } ?>
                </div>
            </div>
        </div>
		<?php
	}

	static function tf_hotel_airport_service_title_price( $post_id, $adult, $child, $airport_service ) {
		$meta = get_post_meta( $post_id, 'tf_hotels_opt', true );

		$airport_service_total = 0;
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['airport_service'] ) && 1 == $meta['airport_service'] ) {
			if ( "pickup" == $airport_service ) {
				$airport_pickup_price = ! empty( $meta['airport_pickup_price'] ) ? $meta['airport_pickup_price'] : '';
				if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
					$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $airport_pickup_price );
					$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
				}
				if ( "per_person" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$service_adult_fee           = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee           = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$airport_service_price_total = ( $adult * $service_adult_fee ) + ( $child * $service_child_fee );
					$airport_service_total       += $airport_service_price_total;

					if ( $child != 0 ) {
						$airport_service_arr = array(
							'title' => sprintf( __( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s )', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								$child,
								strip_tags( wc_price( $service_child_fee ) )
							),
							'price' => $airport_service_price_total
						);
					} else {
						$airport_service_arr = array(
							'title' => sprintf( __( 'Adult ( %1$s × %2$s )', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) )
							),
							'price' => $airport_service_price_total
						);
					}
				}
				if ( "fixed" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$airport_service_price_total = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$airport_service_total       += $airport_service_price_total;

					$airport_service_arr = array(
						'title' => __( 'Fixed Price', 'tourfic' ),
						'price' => $airport_service_price_total
					);
				}
				if ( "free" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$airport_service_arr = array(
						'title' => __( 'Free', 'tourfic' ),
						'price' => 0
					);
				}

				$airport_service_arr['label'] = __( 'Pickup Service', 'tourfic' );
			}
			if ( "dropoff" == $airport_service ) {
				$airport_pickup_price = ! empty( $meta['airport_dropoff_price'] ) ? $meta['airport_dropoff_price'] : '';
				if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
					$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $airport_pickup_price );
					$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
				}

				if ( "per_person" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$service_adult_fee           = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee           = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$airport_service_price_total = ( $adult * $service_adult_fee ) + ( $child * $service_child_fee );

					$airport_service_total += $airport_service_price_total;
					if ( $child != 0 ) {
						$airport_service_arr = array(
							'title' => sprintf( __( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s )', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								$child,
								strip_tags( wc_price( $service_child_fee ) )
							),
							'price' => $airport_service_price_total
						);
					} else {
						$airport_service_arr = array(
							'title' => sprintf( __( 'Adult ( %1$s × %2$s )', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) )
							),
							'price' => $airport_service_price_total
						);
					}
				}
				if ( "fixed" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$airport_service_price_total = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$airport_service_total       += $airport_service_price_total;

					$airport_service_arr = array(
						'title' => __( 'Fixed Price', 'tourfic' ),
						'price' => $airport_service_price_total
					);
				}
				if ( "free" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$airport_service_arr = array(
						'title' => __( 'Free', 'tourfic' ),
						'price' => 0
					);
				}

				$airport_service_arr['label'] = __( 'Drop-off Service', 'tourfic' );
			}
			if ( "both" == $airport_service ) {
				$airport_pickup_price = ! empty( $meta['airport_pickup_dropoff_price'] ) ? $meta['airport_pickup_dropoff_price'] : '';
				if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
					$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $airport_pickup_price );
					$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
				}

				if ( "per_person" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$service_adult_fee           = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee           = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$airport_service_price_total = ( $adult * $service_adult_fee ) + ( $child * $service_child_fee );
					$airport_service_total       += $airport_service_price_total;

					if ( $child != 0 ) {
						$airport_service_arr = array(
							'title' => sprintf( __( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s )', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								$child,
								strip_tags( wc_price( $service_child_fee ) )
							),
							'price' => $airport_service_price_total
						);
					} else {
						$airport_service_arr = array(
							'title' => sprintf( __( 'Adult ( %1$s × %2$s )', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) )
							),
							'price' => $airport_service_price_total
						);
					}
				}
				if ( "fixed" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$airport_service_price_total = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$airport_service_total       += $airport_service_price_total;

					$airport_service_arr = array(
						'title' => __( 'Fixed Price', 'tourfic' ),
						'price' => $airport_service_price_total
					);
				}
				if ( "free" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$airport_service_arr = array(
						'title' => __( 'Free', 'tourfic' ),
						'price' => 0
					);
				}

				$airport_service_arr['label'] = __( 'Pickup & Drop-off Service', 'tourfic' );
			}
		}

		return !empty( $airport_service_arr ) ? $airport_service_arr : array( 'title' => '', 'price' => 0 );
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

		$meta_disable_review 			  = !empty($meta["h-review"]) ? $meta["h-review"] : 0;
		$tfopt_disable_review 			  = !empty(Helper::tfopt("h-review")) ? Helper::tfopt("h-review") : 0;
		$disable_review 				  = $tfopt_disable_review == 1 || $meta_disable_review == 1 ? true : $tfopt_disable_review;

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
					<?php if( $disable_review != true ): ?>
						<?php TF_Review::tf_archive_single_rating(); ?>
					<?php endif; ?>
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
							<?php echo wp_kses_post(Pricing::instance( $post_id )->get_min_price_html($period)); ?>
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
                    <?php if( $disable_review != true ): ?>
						<div class="tf-available-ratings">
							<?php TF_Review::tf_archive_single_rating(); ?>
							<i class="fa-solid fa-star"></i>
						</div>
					<?php endif; ?>
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
                                <?php echo wp_kses_post(Pricing::instance( $post_id )->get_min_price_html($period)); ?>
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
                            <?php echo wp_kses_post(Pricing::instance( $post_id )->get_min_price_html($period)); ?>
                            </span>
                            </div>
                        </div>
                        <a href="<?php echo esc_url( $url ); ?>" class="view-hotel"><?php esc_html_e( "See details", "tourfic" ); ?></a>
                    </div>
                </div>
            </div>
        <?php } elseif ( $tf_hotel_arc_selected_template == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) { ?>
            <div class="tf-archive-hotel" data-id="<?php echo esc_attr(get_the_ID()); ?>">
                <div class="tf-archive-hotel-thumb">
                    <a href="<?php echo esc_url( $url ); ?>">
						<?php
						if ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
							the_post_thumbnail( 'full' );
						} else {
							echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </a>

					<div class="tf-tag-items">
						<?php
						if ( ! empty( $min_discount_amount ) ) : ?>
							<div class="tf-tag-item">
								<?php echo $min_discount_type == "percent" ? wp_kses_post($min_discount_amount . '%') : wp_kses_post(wc_price( $min_discount_amount )) ?>
								<?php esc_html_e( " Off", "tourfic" ); ?>
							</div>
						<?php endif; ?>
						<?php if ( $featured ): ?>
							<div class="tf-tag-item">
								<?php echo ! empty( $meta['featured_text'] ) ? esc_html( $meta['featured_text'] ) : esc_html( "HOT DEAL" ); ?>
							</div>
						<?php endif; ?>
					</div>
                </div>
                <div class="tf-archive-hotel-content">
                    <div class="tf-archive-hotel-content-left">
						<?php if ( ! empty( $address ) ) : ?>
                            <div class="tf-title-location">
                                <div class="location-icon">
                                    <i class="ri-map-pin-fill"></i>
                                </div>
                                <span><?php echo wp_kses_post(Helper::tourfic_character_limit_callback( esc_html( $address ), 20 )); ?></span>
                            </div>
						<?php endif; ?>
                        <h4 class="tf-section-title">
                            <a href="<?php echo esc_url( $url ); ?>">
                                <?php echo wp_kses_post(Helper::tourfic_character_limit_callback( get_the_title(), 45 )); ?>
                            </a>
                        </h4>
						<?php if ( $features ) { ?>
                            <ul class="features">
								<?php foreach ( array_slice( $features, 0, 3 ) as $tfkey => $feature ) :
									$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
									if ( ! empty( $feature_meta ) ) {
										$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
									}
									if ( ! empty( $f_icon_type ) && $f_icon_type == 'fa' ) {
										$feature_icon = ! empty( $feature_meta['icon-fa'] ) ? '<i class="' . $feature_meta['icon-fa'] . '"></i>' : '';
									} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'c' ) {
										$feature_icon = ! empty( $feature_meta['icon-c'] ) ? '<img src="' . $feature_meta['icon-c'] . '" style="min-width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />' : '';
									}

									echo '<li>';
									if ( ! empty( $feature_icon ) ) {
										echo wp_kses_post( $feature_icon );
									}
									echo esc_html( $feature->name );
									//add comma after each feature except last one, if only 1/2 exists then don't add comma to last one
									if ( count( $features ) > 1 && $tfkey != count( array_slice( $features, 0, 3 ) ) - 1 ) {
										echo ',';
									}
									echo '</li>';

								endforeach;
								?>
                            </ul>
						<?php } ?>
						<?php TF_Review::tf_archive_single_rating(); ?>
                    </div>
                    <div class="tf-archive-hotel-content-right">
                        <div class="tf-archive-hotel-price">
							<?php echo wp_kses_post(Pricing::instance( $post_id )->get_min_price_html()); ?>
                        </div>
                        <a href="<?php echo esc_url( $url ); ?>" class="view-hotel"><?php esc_html_e( "View Details", "tourfic" ); ?></a>
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
							<?php if( $disable_review != true ): ?>
								<?php TF_Review::tf_archive_single_rating(); ?>
							<?php endif; ?>
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
															<?php echo wp_kses_post(Pricing::instance( $post_id )->get_min_price_html($period)); ?>
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
		if ( $tf_hotel_selected_template == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) {
			foreach ( $rooms as $key => $_room ) :
				$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
				$enable                  = ! empty( $room['enable'] ) ? $room['enable'] : '';
				if ( $enable == '1' && $room['unique_id'] . $_room->ID == $_POST['uniqid_id'] ) :
					$tf_room_gallery     = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
					$tf_room_gallery_ids = ! empty( $tf_room_gallery ) ? explode( ',', $tf_room_gallery ) : '';
					$child_age_limit     = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";
					$footage             = ! empty( $room['footage'] ) ? $room['footage'] : '';
					$bed                 = ! empty( $room['bed'] ) ? $room['bed'] : '';
					$adult_number        = ! empty( $room['adult'] ) ? $room['adult'] : '0';
					$child_number        = ! empty( $room['child'] ) ? $room['child'] : '0';
					$num_room            = ! empty( $room['num-room'] ) ? $room['num-room'] : '0';
					$room_preview_img     = get_the_post_thumbnail_url( $_room->ID, 'full' );
					?>
                    <div class="tf-room-modal-inner">
                        <div class="tf-room-modal-gallery <?php echo empty( $tf_room_gallery ) ? esc_attr('tf-room-modal-no-gallery') : ''?>">
		                    <?php if ( ! empty( $tf_room_gallery ) ): ?>
                                <div class="tf-room-gallery-slider">
				                    <?php
				                    if ( ! empty( $tf_room_gallery_ids ) ) {
					                    foreach ( $tf_room_gallery_ids as $gallery_item_id ) {
						                    $image_url = wp_get_attachment_url( $gallery_item_id );
						                    echo '<img src="' . esc_url( $image_url ) . '" class="tf-room-modal-gallery-thumb" alt="room-gallery">';
					                    }
				                    } ?>
                                </div>
                                <div class="tf-room-gallery-slider-nav">
				                    <?php
				                    if ( ! empty( $tf_room_gallery_ids ) ) {
					                    foreach ( $tf_room_gallery_ids as $gallery_item_id ) {
						                    $image_url = wp_get_attachment_url( $gallery_item_id, 'thumbnail' );
						                    echo '<img src="' . esc_url($image_url) . '" class="tf-room-modal-gallery-nav" alt="room-gallery-nav">';
					                    }
				                    } ?>
                                </div>

                                <script>
                                    jQuery('.tf-room-gallery-slider').slick({
                                        slidesToShow: 1,
                                        slidesToScroll: 1,
                                        arrows: false,
                                        fade: false,
                                        adaptiveHeight: true,
                                        infinite: true,
                                        useTransform: true,
                                        speed: 400,
                                        cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
                                    });

                                    jQuery('.tf-room-gallery-slider-nav')
                                        .on('init', function (event, slick) {
                                            jQuery('.tf-room-gallery-slider-nav .slick-slide.slick-current').addClass('is-active');
                                        })
                                        .slick({
                                            slidesToShow: 5,
                                            slidesToScroll: 5,
                                            dots: false,
                                            focusOnSelect: false,
                                            infinite: false,
                                            centerMode: false,
                                            responsive: [{
                                                breakpoint: 1024,
                                                settings: {
                                                    slidesToShow: 4,
                                                    slidesToScroll: 4,
                                                }
                                            }, {
                                                breakpoint: 640,
                                                settings: {
                                                    slidesToShow: 3,
                                                    slidesToScroll: 3,
                                                }
                                            }, {
                                                breakpoint: 420,
                                                settings: {
                                                    slidesToShow: 2,
                                                    slidesToScroll: 2,
                                                }
                                            }]
                                        });

                                    jQuery('.tf-room-gallery-slider').on('afterChange', function (event, slick, currentSlide) {
                                        jQuery('.tf-room-gallery-slider-nav').slick('slickGoTo', currentSlide);
                                        var currrentNavSlideElem = '.tf-room-gallery-slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
                                        jQuery('.tf-room-gallery-slider-nav .slick-slide.is-active').removeClass('is-active');
                                        jQuery(currrentNavSlideElem).addClass('is-active');
                                    });

                                    jQuery('.tf-room-gallery-slider-nav').on('click', '.slick-slide', function (event) {
                                        event.preventDefault();
                                        var goToSingleSlide = jQuery(this).data('slick-index');

                                        jQuery('.tf-room-gallery-slider').slick('slickGoTo', goToSingleSlide);
                                    });
                                </script>
		                    <?php elseif ( ! empty( $room_preview_img ) ) : ?>
                                <img src="<?php echo esc_url( $room_preview_img ); ?>" alt="<?php esc_html_e( "Room Image", "tourfic" ); ?>">
		                    <?php else: ?>
                                <img src="<?php echo esc_url( TF_ASSETS_APP_URL . '/images/feature-default.jpg' ) ?>" alt="room-thumb"/>
		                    <?php endif; ?>
                        </div>
                        <div class="tf-room-modal-details">
                            <h2 class="tf-room-title"><?php echo esc_html( get_the_title( $_room->ID ) ); ?></h2>
                            <div class="tf-room-modal-desc"><?php echo wp_kses_post( get_post_field( 'post_content', $_room->ID ) ); ?></div>

                            <div class="tf-room-modal-features">
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
                            </div>
                            <div class="pax">
                                <h4><?php esc_html_e( 'Pax', 'tourfic' ); ?></h4>
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
                                            <span class="room-icon-wrap"><i class="ri-user-smile-line"></i></span>
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

							<?php if ( ! empty( $room['features'] ) ) { ?>
                                <div class="room-features">
                                    <h4><?php esc_html_e( 'Amenities', 'tourfic' ); ?></h4>
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
                <h5 class="tf-room-feature-title"><?php echo esc_html__( 'Filter Rooms based on features', 'tourfic' ); ?></h5>
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
				$room_meta   = get_post_meta( intval($room_id), 'tf_room_opt', true );
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

	static function template( $type = 'archive', $post_id = '' ) {
		$hotel_template = '';
		$post_id        = ! empty( $post_id ) ? $post_id : '';

		if ( $type == 'archive' ) {
			$hotel_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel-archive'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
		} elseif ( $type == 'single' && $post_id ) {
			$meta = get_post_meta( $post_id, 'tf_hotels_opt', true );

			$layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
			if ( "single" == $layout_conditions ) {
				$single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
			}
			$global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';
			$hotel_template  = ! empty( $single_template ) ? $single_template : $global_template;
		} elseif ( $type == 'single' ) {
			$hotel_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';
		}

		return $hotel_template;
	}

	function tf_move_comment_field( $fields ) {
        global $post;
		$post_id           = $post->ID;
        if(get_post_type( $post_id ) == "tf_hotel" && self::template('single', $post_id) == 'design-3') {
	        $comment_field = $fields['comment'];
	        unset( $fields['comment'] );
	        $fields['comment'] = $comment_field;
        }
		return $fields;
	}

	static function tf_hotel_without_payment_inventory_data($order_id) {

        # Get completed orders
        $tf_orders_select = array(
            'select' => "post_id,order_details,room_id,post_type",
            'post_type' => 'hotel',
            'query' => " AND ostatus = 'completed' AND order_id = ".$order_id,
        );
        $order_data = Helper::tourfic_order_table_data($tf_orders_select);

        if ( !empty($order_data[0]["post_type"]) && "hotel" == $order_data[0]["post_type"] ) {
			$post_id   = $order_data[0]["post_id"];
			$unique_id = $order_data[0]["room_id"];
			$meta      = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$rooms     = Room::get_hotel_rooms( $post_id );

			if ( ! empty( $rooms ) ) {
				foreach ( $rooms as $_room ) {
					$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
					# Check if order is for this room
					if ( $room['unique_id'] == $unique_id ) {

						$old_order_id = $room['order_id'];

						$old_order_id != "" && $old_order_id .= ",";
						$old_order_id .= $order_id;

						# set old + new data to the oder_id meta
						$room['order_id'] = $old_order_id;
						update_post_meta( $_room->ID, 'tf_room_opt', $room );
					}
				}
			}
		}
    }
}