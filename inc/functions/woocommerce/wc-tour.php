<?php
defined( 'ABSPATH' ) || exit;
use \Tourfic\Classes\Helper;

/**
 * Tour booking ajax function
 *
 * @since 2.2.0
 */
add_action( 'wp_ajax_tf_tours_booking', 'tf_tours_booking_function' );
add_action( 'wp_ajax_nopriv_tf_tours_booking', 'tf_tours_booking_function' );
function tf_tours_booking_function() {

	if ( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['_ajax_nonce'])), 'tf_ajax_nonce' ) ) {
		return;
	}

	// Declaring errors & tour data array
	$response      = array();
	$tf_tours_data = array();

	/**
	 * Backend options panel data
	 *
	 * @since 2.2.0
	 */
	$post_id              = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : '';
	$product_id           = get_post_meta( $post_id, 'product_id', true );
	$post_author          = get_post_field( 'post_author', $post_id );
	$meta                 = get_post_meta( $post_id, 'tf_tours_opt', true );
	$tour_type            = ! empty( $meta['type'] ) ? $meta['type'] : '';
	$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
	$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
	$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
	$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;

	/**
	 * All form data
	 *
	 */
	// People number
	$adults       = isset( $_POST['adults'] ) ? intval( sanitize_text_field( $_POST['adults'] ) ) : 0;
	$children     = isset( $_POST['childrens'] ) ? intval( sanitize_text_field( $_POST['childrens'] ) ) : 0;
	$infant       = isset( $_POST['infants'] ) ? intval( sanitize_text_field( $_POST['infants'] ) ) : 0;
	$total_people = $adults + $children + $infant;
	$total_people_booking = $adults + $children;
	// Tour date
	$tour_date    = ! empty( $_POST['check-in-out-date'] ) ? sanitize_text_field( $_POST['check-in-out-date'] ) : '';
	$tour_time    = isset( $_POST['check-in-time'] ) ? sanitize_text_field( $_POST['check-in-time'] ) : null;
	$make_deposit = ! empty( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : false;

	// Visitor Details
	$tf_visitor_details = !empty($_POST['traveller']) ? $_POST['traveller'] : "";

	// Booking Confirmation Details
	$tf_confirmation_details = !empty($_POST['booking_confirm']) ? $_POST['booking_confirm'] : "";

	// Booking Type
	$tf_booking_type = function_exists('is_tf_pro') && is_tf_pro() ? ( !empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1 ) : 1;
	$tf_booking_url = function_exists('is_tf_pro') && is_tf_pro() ? ( !empty( $meta['booking-url'] ) ? esc_url($meta['booking-url']) : '' ) : '';
	$tf_booking_query_url = function_exists('is_tf_pro') && is_tf_pro() ? ( !empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&infant={infant}' ) : '';
	$tf_booking_attribute = function_exists('is_tf_pro') && is_tf_pro() ? ( !empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '' ) : '';

	/**
	 * If fixed is selected but pro is not activated
	 *
	 * show error
	 *
	 * @return
	 */
	if ( $tour_type == 'fixed' && function_exists('is_tf_pro') && ! is_tf_pro() ) {
		$response['errors'][] = esc_html__( 'Fixed Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
		$response['status']   = 'error';
		echo wp_json_encode( $response );
		die();

		return;
	}

	if ( $tour_type == 'fixed' ) {

		if( !empty($meta['fixed_availability']) && gettype($meta['fixed_availability'])=="string" ){
            $tf_tour_fixed_avail = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
                return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
            }, $meta['fixed_availability'] );
            $tf_tour_fixed_date = unserialize( $tf_tour_fixed_avail );
			$start_date = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
			$end_date   = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
			$min_people = ! empty( $tf_tour_fixed_date['min_seat'] ) ? $tf_tour_fixed_date['min_seat'] : '';
			$max_people = ! empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
			$tf_tour_booking_limit = ! empty( $tf_tour_fixed_date['max_capacity'] ) ? $tf_tour_fixed_date['max_capacity'] : 0;
		}else{
			$start_date = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
			$end_date   = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
			$min_people = ! empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
			$max_people = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
			$tf_tour_booking_limit = ! empty( $meta['fixed_availability']['max_capacity'] ) ? $meta['fixed_availability']['max_capacity'] : 0; 
		}

		if(!function_exists("selected_day_diff")) {
			function selected_day_diff ($start_date, $end_date) {
				if(!empty($start_date) && !empty($end_date)) {

					$start_date = new DateTime($start_date);
					$end_date   = new DateTime($end_date);
					$interval 	= $start_date->diff($end_date);

					return $interval->days;
				}	
			}
		}

		if(!function_exists("end_date_calculation")) {
			function end_date_calculation ($start_date, $difference) {
				if(!empty($start_date) && !empty($difference)) {
					if(str_contains($start_date, ' - ')) {
						return $start_date;

					} else {
						
						$start_date  = new DateTime($start_date);
						$new_end_day = $start_date->modify("+ $difference day");

						return $new_end_day->format('Y/m/d');
					}
				}	
			}
		}

		if( !empty($start_date) && !empty($end_date)) {
			$day_diff = selected_day_diff($start_date, $end_date );
		}

		if(!empty($tour_type) && ($tour_type == "fixed")) {
			$start_date = ! empty( $_POST['check-in-out-date'] ) ? sanitize_text_field( $_POST['check-in-out-date'] ) : '';
		}

		if(!empty($start_date) && !empty($day_diff)) {
			$end_date = end_date_calculation($start_date, $day_diff);
		}

		// Fixed tour maximum capacity limit
	
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($start_date) && !empty($end_date) ) {
			
			// Tour Order retrive from Tourfic Order Table
			$tf_orders_select = array(
				'select' => "post_id,order_details",
				'post_type' => 'tour',
				'query' => " AND ostatus = 'completed' ORDER BY order_id DESC"
			);
			$tf_tour_book_orders = Helper::tourfic_order_table_data($tf_orders_select);

			$tf_total_adults = 0;
			$tf_total_childrens = 0;

			foreach( $tf_tour_book_orders as $order ){
				$tour_id   = $order['post_id'];
				$order_details = json_decode($order['order_details']);
				$tf_tour_date = !empty($order_details->tour_date) ? $order_details->tour_date : '';
				list( $tf_booking_start, $tf_booking_end ) = explode( " - ", $tf_tour_date );
				if( !empty($tour_id) && $tour_id==$post_id && !empty($tf_booking_start) && $start_date==$tf_booking_start && !empty($tf_booking_end) && $end_date==$tf_booking_end ){
					$book_adult     = !empty( $order_details->adult ) ? $order_details->adult : '';
					if(!empty($book_adult)){
						list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
						$tf_total_adults += $tf_total_adult;
					}

					$book_children  = !empty( $order_details->child ) ? $order_details->child : '';
					if(!empty($book_children)){
						list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
						$tf_total_childrens += $tf_total_children;
					}
				}	
			}

			$tf_total_people = $tf_total_adults+$tf_total_childrens;
			
			if( !empty($tf_tour_booking_limit) ){
				$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;
				if( $tf_total_people > 0 && $tf_total_people==$tf_tour_booking_limit ){
					$response['errors'][] = esc_html__( 'Booking limit is Reached this Tour', 'tourfic' );
				}
				if( $tf_total_people!=$tf_tour_booking_limit && $tf_today_limit < $total_people_booking ){
					/* translators: %1$s Limit  */
					$response['errors'][] = sprintf( esc_html__( 'Only %1$s Adult/Children are available this Tour', 'tourfic' ), $tf_today_limit );
				}
			}
		}

	} elseif ( $tour_type == 'continuous' ) {

		$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;

		if ( $custom_avail == true ) {

			$pricing_rule     = $meta['custom_pricing_by'];
			$cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
			if( !empty($cont_custom_date) && gettype($cont_custom_date)=="string" ){
				$tf_tour_conti_avail = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
					return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
				}, $cont_custom_date );
				$cont_custom_date = unserialize( $tf_tour_conti_avail );
			}

		} elseif ( $custom_avail == false ) {

			$min_people = ! empty( $meta['cont_min_people'] ) ? $meta['cont_min_people'] : '';
			$max_people = ! empty( $meta['cont_max_people'] ) ? $meta['cont_max_people'] : '';
			$allowed_times_field = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';


			// Daily Tour Booking Capacity && Tour Order retrive from Tourfic Order Table
			$tf_orders_select = array(
				'select' => "post_id,order_details",
				'post_type' => 'tour',
				'query' => " AND ostatus = 'completed' ORDER BY order_id DESC"
			);
			$tf_tour_book_orders = Helper::tourfic_order_table_data($tf_orders_select);

			$tf_total_adults = 0;
			$tf_total_childrens = 0;

			if( empty($allowed_times_field) || $tour_time==null ){
				$tf_tour_booking_limit = ! empty( $meta['cont_max_capacity'] ) ? $meta['cont_max_capacity'] : 0;

				foreach( $tf_tour_book_orders as $order ){
                    $tour_id   = $order['post_id'];
                    $order_details = json_decode($order['order_details']);
                    $tf_tour_date = !empty($order_details->tour_date) ? $order_details->tour_date : '';
                    $tf_tour_time = !empty($order_details->tour_time) ? $order_details->tour_time : '';

                    if( !empty($tour_id) && $tour_id==$post_id && !empty($tf_tour_date) && $tour_date==$tf_tour_date && empty($tf_tour_time) ){
                        $book_adult     = !empty( $order_details->adult ) ? $order_details->adult : '';
                        if(!empty($book_adult)){
                            list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
                            $tf_total_adults += $tf_total_adult;
                        }

                        $book_children  = !empty( $order_details->child ) ? $order_details->child : '';
                        if(!empty($book_children)){
                            list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
                            $tf_total_childrens += $tf_total_children;
                        }
                    }
                }
				
			}else{
				if(!empty($allowed_times_field[$tour_time]['time'])){
					$tour_time_title = $allowed_times_field[$tour_time]['time'];
				}

				if(!empty($allowed_times_field[$tour_time]['cont_max_capacity'])){
					$tf_tour_booking_limit = $allowed_times_field[$tour_time]['cont_max_capacity'];

					foreach( $tf_tour_book_orders as $order ){
                        $tour_id   = $order['post_id'];
                        $order_details = json_decode($order['order_details']);
                        $tf_tour_date = !empty($order_details->tour_date) ? $order_details->tour_date : '';
                        $tf_tour_time = !empty($order_details->tour_time) ? $order_details->tour_time : '';
    
                        if( !empty($tour_id) && $tour_id==$post_id && !empty($tf_tour_date) && $tour_date==$tf_tour_date && !empty($tf_tour_time) && $tf_tour_time==$tour_time_title ){
                            $book_adult     = !empty( $order_details->adult ) ? $order_details->adult : '';
                            if(!empty($book_adult)){
                                list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
                                $tf_total_adults += $tf_total_adult;
                            }
    
                            $book_children  = !empty( $order_details->child ) ? $order_details->child : '';
                            if(!empty($book_children)){
                                list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
                                $tf_total_childrens += $tf_total_children;
                            }
                        }
                    }
					
				}
			}
			$tf_total_people = $tf_total_adults+$tf_total_childrens;

			if( !empty($tf_tour_booking_limit) ){
				$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;

				if( $tf_total_people > 0 && $tf_total_people==$tf_tour_booking_limit ){
					$response['errors'][] = esc_html__( 'Booking limit is Reached this Date', 'tourfic' );
				}
				if( $tf_total_people!=$tf_tour_booking_limit && $tf_today_limit < $total_people_booking ){ 
					/* translators: %1$s Limit  */
					$response['errors'][] = sprintf( esc_html__( 'Only %1$s Adult/Children are available this Date', 'tourfic' ), $tf_today_limit );
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
	if ( $tour_type == 'continuous' && $custom_avail == true && function_exists('is_tf_pro') && ! is_tf_pro() ) {
		$response['errors'][] = esc_html__( 'Custom Continous Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
		$response['status']   = 'error';
		echo wp_json_encode( $response );
		die();

		return;
	}


	if ( $tour_type == 'continuous' ) {
		$start_date = $end_date = $tour_date;
	}

	// Tour extra
	$tour_extra_total = 0;
	$tour_extra_title_arr = [];
	
	$tour_extra_meta = ! empty( $meta['tour-extra'] ) ? $meta['tour-extra'] : '';
	if(!empty($tour_extra_meta)){
		$tours_extra = explode(',', $_POST['tour_extra']);
		$tour_extra_quantity = explode(',', $_POST["tour_extra_quantity"]);
		foreach($tours_extra as $extra_key => $extra){
			$tour_extra_pricetype = !empty( $tour_extra_meta[$extra]['price_type'] ) ? $tour_extra_meta[$extra]['price_type'] : 'fixed';
			if( $tour_extra_pricetype=="fixed" ){
				if(!empty($tour_extra_meta[$extra]['title']) && !empty($tour_extra_meta[$extra]['price'])){
					$tour_extra_total += $tour_extra_meta[$extra]['price'];
					$tour_extra_title_arr[] =  $tour_extra_meta[$extra]['title']." (Fixed: ".wc_price($tour_extra_meta[$extra]['price']).")";
				}
			} else if($tour_extra_pricetype == "quantity") {
				if(!empty($tour_extra_meta[$extra]['title']) && !empty($tour_extra_meta[$extra]['price'])){
					$tour_extra_total += $tour_extra_meta[$extra]['price'] * $tour_extra_quantity[$extra_key];
					$tour_extra_title_arr[] = $tour_extra_meta[$extra]['title']." (Per Unit: ".wc_price($tour_extra_meta[$extra]['price']).'*'.$tour_extra_quantity[$extra_key]."=".wc_price($tour_extra_meta[$extra]['price']*$tour_extra_quantity[$extra_key]).")";
				}
			}else{
				if(!empty($tour_extra_meta[$extra]['price']) && !empty($tour_extra_meta[$extra]['title'])){
					$tour_extra_total += ($tour_extra_meta[$extra]['price']*$total_people);
					$tour_extra_title_arr[] =  $tour_extra_meta[$extra]['title']." (Per Person: ".wc_price($tour_extra_meta[$extra]['price']).'*'.$total_people."=".wc_price($tour_extra_meta[$extra]['price']*$total_people).")";
				}
			}
		}
	}

	$tour_extra_title = ! empty( $tour_extra_title_arr ) ? implode(",",$tour_extra_title_arr) : '';

	/**
	 * People 0 number validation
	 *
	 */
	if ( $total_people == 0 ) {
		$response['errors'][] = esc_html__( 'Please Select Adults/Children/Infant required', 'tourfic' );
	}

	/**
	 * People number validation
	 *
	 */
	if ( $tour_type == 'fixed' ) {

		/* translators: %s Min Person  */
		$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
		/* translators: %s Max Person  */
		$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );

		if ( $total_people < $min_people && $min_people > 0 ) {
			/* translators: %s Min Required  */
			$response['errors'][] = sprintf( esc_html__( 'Minimum %1$s required', 'tourfic' ), $min_text );

		} else if ( $total_people > $max_people && $max_people > 0 ) {
			/* translators: %s Max Required  */
			$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

		}

	} elseif ( $tour_type == 'continuous' && $custom_avail == false ) {

		/* translators: %s Min Person  */
		$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
		/* translators: %s Max Person  */
		$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );


		if ( $total_people < $min_people && $min_people > 0 ) {
			/* translators: %s Min Required  */
			$response['errors'][] = sprintf( esc_html__( 'Minimum %1$s required', 'tourfic' ), $min_text );

		} else if ( $total_people > $max_people && $max_people > 0 ) {
			/* translators: %s Max Required  */
			$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

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
			/* translators: %s Min Person  */
			$min_text   = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
			/* translators: %s Min Person  */
			$max_text   = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );


			// Compare backend & frontend date values to show specific people number error
			if ( $front_date >= $back_date_from_stt && $front_date <= $back_date_to_stt ) {
				if ( $total_people < $min_people && $min_people > 0 ) {
					/* translators: %1$s Min Person, $2$s Date From, %3$s Date To  */
					$response['errors'][] = sprintf( esc_html__( 'Minimum %1$s required for date %2$s - %3$s', 'tourfic' ), $min_text, $back_date_from, $back_date_to );

				}
				if ( $total_people > $max_people && $max_people > 0 ) {
					/* translators: %1$s Max Person, $2$s Date From, %3$s Date To  */
					$response['errors'][] = sprintf( esc_html__( 'Maximum %1$s allowed for date %2$s - %3$s', 'tourfic' ), $max_text, $back_date_from, $back_date_to );

				}


				$allowed_times_field = ! empty( $item['allowed_time'] ) ? $item['allowed_time'] : '';

				// Daily Tour Booking Capacity && tour order retrive form tourfic order table
				$tf_orders_select = array(
					'select' => "post_id,order_details",
					'post_type' => 'tour',
					'query' => " AND ostatus = 'completed' ORDER BY order_id DESC"
				);
				$tf_tour_book_orders = Helper::tourfic_order_table_data($tf_orders_select);

                $tf_total_adults = 0;
                $tf_total_childrens = 0;

				if( empty($allowed_times_field) || $tour_time==null ){
					$tf_tour_booking_limit = ! empty( $item['max_capacity'] ) ? $item['max_capacity'] : '';

					foreach( $tf_tour_book_orders as $order ){
                        $tour_id   = $order['post_id'];
                        $order_details = json_decode($order['order_details']);
                        $tf_tour_date = !empty($order_details->tour_date) ? $order_details->tour_date : '';
                        $tf_tour_time = !empty($order_details->tour_time) ? $order_details->tour_time : '';
    
                        if( !empty($tour_id) && $tour_id==$post_id && !empty($tf_tour_date) && $tour_date==$tf_tour_date && empty($tf_tour_time) ){
                            $book_adult     = !empty( $order_details->adult ) ? $order_details->adult : '';
                            if(!empty($book_adult)){
                                list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
                                $tf_total_adults += $tf_total_adult;
                            }
    
                            $book_children  = !empty( $order_details->child ) ? $order_details->child : '';
                            if(!empty($book_children)){
                                list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
                                $tf_total_childrens += $tf_total_children;
                            }
                        }
                    }

				}else{
					if(!empty($allowed_times_field[$tour_time]['time'])){
						$tour_time_title = $allowed_times_field[$tour_time]['time'];
					}

					if(!empty($allowed_times_field[$tour_time]['max_capacity'])){
						$tf_tour_booking_limit = $allowed_times_field[$tour_time]['max_capacity'];

						foreach( $tf_tour_book_orders as $order ){
                            $tour_id   = $order['post_id'];
                            $order_details = json_decode($order['order_details']);
                            $tf_tour_date = !empty($order_details->tour_date) ? $order_details->tour_date : '';
                            $tf_tour_time = !empty($order_details->tour_time) ? $order_details->tour_time : '';
        
                            if( !empty($tour_id) && $tour_id==$post_id && !empty($tf_tour_date) && $tour_date==$tf_tour_date && !empty($tf_tour_time) && $tf_tour_time==$tour_time_title ){
                                $book_adult     = !empty( $order_details->adult ) ? $order_details->adult : '';
                                if(!empty($book_adult)){
                                    list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
                                    $tf_total_adults += $tf_total_adult;
                                }
        
                                $book_children  = !empty( $order_details->child ) ? $order_details->child : '';
                                if(!empty($book_children)){
                                    list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
                                    $tf_total_childrens += $tf_total_children;
                                }
                            }
                        }

					}
				}
				$tf_total_people = $tf_total_adults+$tf_total_childrens;

				if( !empty($tf_tour_booking_limit) ){
					$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;

					if( $tf_total_people > 0 && $tf_total_people==$tf_tour_booking_limit ){
						$response['errors'][] = esc_html__( 'Booking limit is Reached this Date', 'tourfic' );
					}
					if( $tf_total_people!=$tf_tour_booking_limit && $tf_today_limit < $total_people_booking ){
						/* translators: %1$s Person Count  */
						$response['errors'][] = sprintf( esc_html__( 'Only %1$s Adult/Children are available this Date', 'tourfic' ), $tf_today_limit );
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
	/* translators: %1$s Min Day Before Book  */
	$min_days_before_book_text = sprintf( _n( '%s day', '%s days', $min_days_before_book, 'tourfic' ), $min_days_before_book );
	$today_stt                 = new DateTime( gmdate( 'Y-m-d', strtotime( gmdate( 'Y-m-d' ) ) ) );
	$tour_date_stt             = new DateTime( gmdate( 'Y-m-d', strtotime( $start_date ) ) );
	$day_difference            = $today_stt->diff( $tour_date_stt )->days;
	$adult_required_chield = !empty( $meta["require_adult_child_booking"] ) ? $meta["require_adult_child_booking"] : 0;
	$quick_checkout = !empty(Helper::tfopt( 'tf-quick-checkout' )) ? Helper::tfopt( 'tf-quick-checkout' ) : 0;
	$instantio_is_active = 0;

	if( is_plugin_active('instantio/instantio.php') ){
		$instantio_is_active = 1;
	}


	if ( $day_difference < $min_days_before_book ) {
		/* translators: %1$s Minimum Days Gap  */
		$response['errors'][] = sprintf( esc_html__( 'Present date to booking date required minimum %1$s gap', 'tourfic' ), $min_days_before_book_text );
	}
	if ( ! $start_date ) {
		$response['errors'][] = esc_html__( 'You must select booking date', 'tourfic' );
	}
	if ( ! $post_id ) {
		$response['errors'][] = esc_html__( 'Unknown Error! Please try again.', 'tourfic' );
	}

	/**
	 * Price by date range
	 *
	 * Tour type continuous and custom availability is true
	 */
	$tf_cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
	if( !empty($tf_cont_custom_date) && gettype($tf_cont_custom_date)=="string" ){
		$tf_tour_conti_custom_date = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
			return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
		}, $tf_cont_custom_date );
		$tf_cont_custom_date = unserialize( $tf_tour_conti_custom_date );
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

		$group_price    = !empty($seasional_price[0]['group_price']) ? $seasional_price[0]['group_price'] : 0;
		$adult_price    = !empty($seasional_price[0]['adult_price']) ? $seasional_price[0]['adult_price'] : 0;
		$children_price = !empty($seasional_price[0]['child_price']) ? $seasional_price[0]['child_price'] : 0;
		$infant_price   = !empty($seasional_price[0]['infant_price']) ? $seasional_price[0]['infant_price'] : 0;

	} else {

		$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
		$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
		$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
		$infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;

	}

	if ( function_exists('is_tf_pro') && is_tf_pro() && $tour_type == 'continuous' ) {
		$tf_allowed_times = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';
		if( !empty($tf_allowed_times) && gettype($tf_allowed_times)=="string" ){
			$tf_tour_conti_custom_date = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
				return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
			}, $tf_allowed_times );
			$tf_allowed_times = unserialize( $tf_tour_conti_custom_date );
		}
		
		if ( $custom_avail == false && ! empty( $tf_allowed_times ) && empty( $tour_time_title  ) ) {
			$response['errors'][] = esc_html__( 'Please select time', 'tourfic' );
		}
		if ( $custom_avail == true && ! empty( $seasional_price[0]['allowed_time'] ) && empty( $tour_time_title  ) ) {
			$response['errors'][] = esc_html__( 'Please select time', 'tourfic' );
		}
	}

	if ( ( ! empty( $custom_avail ) && $custom_avail == true ) || $pricing_rule == 'person' ) {

		if ( ! $disable_adult_price && $adults > 0 && empty( $adult_price ) ) {
			$response['errors'][] = esc_html__( 'Adult price is blank!', 'tourfic' );
		}
		if ( ! $disable_child_price && $children > 0 && empty( $children_price ) ) {
			$response['errors'][] = esc_html__( 'Childern price is blank!', 'tourfic' );
		}
		if ( ! $disable_infant_price && $infant > 0 && empty( $infant_price ) ) {
			$response['errors'][] = esc_html__( 'Infant price is blank!', 'tourfic' );
		}
		if ( $infant > 0 && ! empty( $infant_price ) && ! $adults ) {
			$response['errors'][] = esc_html__( 'Infant without adults is not allowed!', 'tourfic' );
		} 

		if ( $adult_required_chield && $children > 0 && !empty( $children_price ) && empty( $adults ) ) {
		    $response['errors'][] = esc_html__( 'An adult is required for children booking!', 'tourfic' );
		}

	} else if ( ( ! empty( $custom_avail ) && $custom_avail == true ) || $pricing_rule == 'group' ) {

		if ( empty( $group_price ) ) {
			$response['errors'][] = esc_html__( 'Group price is blank!', 'tourfic' );
		}

	}

	/**
	 * If no errors then process
	 *
	 * Store custom data in array
	 * Add to cart with custom data
	 */

	if( !empty($tf_booking_type) && 3==$tf_booking_type ){

		$tf_booking_fields = !empty(Helper::tfopt( 'book-confirm-field' )) ? Helper::tf_data_types(Helper::tfopt( 'book-confirm-field' )) : '';
		if(empty($tf_booking_fields)){
			$billing_details  = array(
				'billing_first_name' => sanitize_text_field($tf_confirmation_details['tf_first_name']),
				'billing_last_name'  => sanitize_text_field($tf_confirmation_details['tf_last_name']),
				'billing_company'    => '',
				'billing_address_1'  => sanitize_text_field($tf_confirmation_details['tf_street_address']),
				'billing_address_2'  => "",
				'billing_city'       => sanitize_text_field($tf_confirmation_details['tf_town_city']),
				'billing_state'      => sanitize_text_field($tf_confirmation_details['tf_state_country']),
				'billing_postcode'   => sanitize_text_field($tf_confirmation_details['tf_postcode']),
				'billing_country'    => sanitize_text_field($tf_confirmation_details['tf_country']),
				'billing_email'      => sanitize_email($tf_confirmation_details['tf_email']),
				'billing_phone'      => sanitize_text_field($tf_confirmation_details['tf_phone']),
			);
			$shipping_details = array(
				'tf_first_name' => sanitize_text_field($tf_confirmation_details['tf_first_name']),
				'tf_last_name'  => sanitize_text_field($tf_confirmation_details['tf_last_name']),
				'shipping_company'    => '',
				'tf_street_address'  => sanitize_text_field($tf_confirmation_details['tf_street_address']),
				'shipping_address_2'  => "",
				'tf_town_city'       => sanitize_text_field($tf_confirmation_details['tf_town_city']),
				'tf_state_country'      => sanitize_text_field($tf_confirmation_details['tf_state_country']),
				'tf_postcode'   => sanitize_text_field($tf_confirmation_details['tf_postcode']),
				'tf_country'    => sanitize_text_field($tf_confirmation_details['tf_country']),
				'tf_phone'      => sanitize_text_field($tf_confirmation_details['tf_phone']),
				'tf_email'      => sanitize_email($tf_confirmation_details['tf_email']),
			);
		}else{
			$billing_details = [];
			$shipping_details = [];
			
			if(!empty($tf_confirmation_details)){
				foreach( $tf_confirmation_details as $key => $details ){
					if("tf_first_name"==$key){
						$billing_details['billing_first_name'] = sanitize_text_field($details);
						$shipping_details[$key] = sanitize_text_field($details);
					}else if("tf_last_name"==$key){
						$billing_details['billing_last_name'] = sanitize_text_field($details);
						$shipping_details[$key] = sanitize_text_field($details);
					}else if("tf_street_address"==$key){
						$billing_details['billing_address_1'] = sanitize_text_field($details);
						$shipping_details[$key] = sanitize_text_field($details);
					}else if("tf_town_city"==$key){
						$billing_details['billing_city'] = sanitize_text_field($details);
						$shipping_details[$key] = sanitize_text_field($details);
					}else if("tf_state_country"==$key){
						$billing_details['billing_state'] = sanitize_text_field($details);
						$shipping_details[$key] = sanitize_text_field($details);
					}else if("tf_postcode"==$key){
						$billing_details['billing_postcode'] = sanitize_text_field($details);
						$shipping_details[$key] = sanitize_text_field($details);
					}else if("tf_country"==$key){
						$billing_details['billing_country'] = sanitize_text_field($details);
						$shipping_details[$key] = sanitize_text_field($details);
					}else if("tf_email"==$key){
						$billing_details['billing_email'] = sanitize_email($details);
						$shipping_details[$key] = sanitize_email($details);
					}else if("tf_phone"==$key){
						$billing_details['billing_phone'] = sanitize_text_field($details);
						$shipping_details[$key] = sanitize_text_field($details);
					}else{
						$billing_details[$key] = $details;
						$shipping_details[$key] = $details;
					}
				}
			}
		}

		// Price Calculation
		
		$discount_type    = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
		$discounted_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';

		if ( $tour_type == 'continuous' ) {
			$tf_tours_data['tf_tours_data']['tour_time'] = $tour_time_title;
		}

		# Calculate discounted price
		if ( $discount_type == 'percent' ) {

			$adult_price    = floatval(preg_replace('/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $discounted_price ), 2 )));
			$children_price = floatval(preg_replace('/[^\d.]/', '', number_format( $children_price - ( ( $children_price / 100 ) * $discounted_price ), 2 )));
			$infant_price   = floatval(preg_replace('/[^\d.]/', '', number_format( $infant_price - ( ( $infant_price / 100 ) * $discounted_price ), 2 )));
			$group_price    = floatval(preg_replace('/[^\d.]/', '', number_format( $group_price - ( ( $group_price / 100 ) * $discounted_price ), 2 )));

		} elseif ( $discount_type == 'fixed' ) {

			$adult_price    = floatval(preg_replace('/[^\d.]/', '', number_format( ( $adult_price - $discounted_price ), 2 )));
			$children_price = floatval(preg_replace('/[^\d.]/', '', number_format( ( $children_price - $discounted_price ), 2 )));
			$infant_price   = floatval(preg_replace('/[^\d.]/', '', number_format( ( $infant_price - $discounted_price ), 2 )));
			$group_price    = floatval(preg_replace('/[^\d.]/', '', number_format( ( $group_price - $discounted_price ), 2 )));

		}

		# Set pricing based on pricing rule
		if ( $pricing_rule == 'group' ) {
			$without_payment_price     = $group_price;
		} else {
			$without_payment_price     = ( $adult_price * $adults ) + ( $children * $children_price ) + ( $infant * $infant_price );
		}

		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			// get user id
			$tf_offline_user_id = $current_user->ID;
		} else {
			$tf_offline_user_id = 1;
		}

		$order_details = [
			'order_by'    => '',
			'tour_date'   => $tour_date,
			'tour_time'   => !empty($tour_time_title) ? $tour_time_title : '',
			'tour_extra'  => $tour_extra_title,
			'adult'       => $adults,
			'child'       => $children,
			'infants'     => $infant,
			'total_price' => $without_payment_price,
			'due_price'   => wc_price($without_payment_price),
			'visitor_details' => wp_json_encode($tf_visitor_details)
		];

		$order_data = array(
			'post_id'          => $post_id,
			'post_type'        => 'tour',
			'room_number'      => null,
			'check_in'         => $start_date,
			'check_out'        => $end_date,
			'billing_details'  => $billing_details,
			'shipping_details' => $shipping_details,
			'order_details'    => $order_details,
			'payment_method'   => 'offline',
			'customer_id'	   => $tf_offline_user_id,
			'status'           => 'completed',
			'order_date'       => gmdate( 'Y-m-d H:i:s' ),
		);
		$response['without_payment'] = 'true';
		$order_id = Helper::tf_set_order( $order_data );
		if ( function_exists('is_tf_pro') && is_tf_pro() && !empty($order_id) ) {
			do_action( 'tf_offline_payment_booking_confirmation', $order_id, $order_data );
		}

	}else{
		if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {

			$tf_tours_data['tf_tours_data']['order_type']     = 'tour';
			$tf_tours_data['tf_tours_data']['post_author']    = $post_author;
			$tf_tours_data['tf_tours_data']['tour_type']      = $tour_type;
			$tf_tours_data['tf_tours_data']['tour_id']        = $post_id;
			$tf_tours_data['tf_tours_data']['post_permalink'] = get_permalink( $post_id );

			$tf_tours_data['tf_tours_data']['start_date']       = $start_date;
			$tf_tours_data['tf_tours_data']['end_date']         = $end_date;
			$tf_tours_data['tf_tours_data']['tour_date']        = $tour_date;
			$tf_tours_data['tf_tours_data']['tour_extra_total'] = $tour_extra_total;
			// Visitor Details
			$tf_tours_data['tf_tours_data']['visitor_details'] = wp_json_encode($tf_visitor_details);
			if($tour_extra_title){
				$tf_tours_data['tf_tours_data']['tour_extra_title'] = $tour_extra_title;
			}
			# Discount informations
			$discount_type    = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
			$discounted_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';

			if ( $tour_type == 'continuous' ) {
				$tf_tours_data['tf_tours_data']['tour_time'] = !empty($tour_time_title) ? $tour_time_title : '';
			}

			# Calculate discounted price
			if ( $discount_type == 'percent' ) {

				$adult_price    = floatval(preg_replace('/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $discounted_price ), 2 )));
				$children_price = floatval(preg_replace('/[^\d.]/', '', number_format( $children_price - ( ( $children_price / 100 ) * $discounted_price ), 2 )));
				$infant_price   = floatval(preg_replace('/[^\d.]/', '', number_format( $infant_price - ( ( $infant_price / 100 ) * $discounted_price ), 2 )));
				$group_price    = floatval(preg_replace('/[^\d.]/', '', number_format( $group_price - ( ( $group_price / 100 ) * $discounted_price ), 2 )));

			} elseif ( $discount_type == 'fixed' ) {

				$adult_price    = floatval(preg_replace('/[^\d.]/', '', number_format( ( $adult_price - $discounted_price ), 2 )));
				$children_price = floatval(preg_replace('/[^\d.]/', '', number_format( ( $children_price - $discounted_price ), 2 )));
				$infant_price   = floatval(preg_replace('/[^\d.]/', '', number_format( ( $infant_price - $discounted_price ), 2 )));
				$group_price    = floatval(preg_replace('/[^\d.]/', '', number_format( ( $group_price - $discounted_price ), 2 )));

			}

			# Set pricing based on pricing rule
			if ( $pricing_rule == 'group' ) {

				$tf_tours_data['tf_tours_data']['price']     = $group_price;
				$tf_tours_data['tf_tours_data']['adults']    = $adults;
				$tf_tours_data['tf_tours_data']['childrens'] = $children;
				$tf_tours_data['tf_tours_data']['infants']   = $infant;

			} else {

				$tf_tours_data['tf_tours_data']['price']     = ( $adult_price * $adults ) + ( $children * $children_price ) + ( $infant * $infant_price );
				$tf_tours_data['tf_tours_data']['adults']    = $adults . " × " . wp_strip_all_tags(wc_price( $adult_price ));
				$tf_tours_data['tf_tours_data']['childrens'] = $children . " × " . wp_strip_all_tags(wc_price( $children_price ));
				$tf_tours_data['tf_tours_data']['infants']   = $infant . " × " . wp_strip_all_tags(wc_price( $infant_price ));
			}

			# Deposit information
			Helper::tf_get_deposit_amount( $meta, $tf_tours_data['tf_tours_data']['price'], $deposit_amount, $has_deposit );
			if ( function_exists('is_tf_pro') && is_tf_pro() && $has_deposit == true && $make_deposit == true ) {
				$tf_tours_data['tf_tours_data']['due']   = $tf_tours_data['tf_tours_data']['price'] - $deposit_amount;
				$tf_tours_data['tf_tours_data']['price'] = $deposit_amount;
			}

			if( 2==$tf_booking_type && !empty($tf_booking_url) ){
				$external_search_info = array(
					'{adult}'    => $adults,
					'{child}'    => $children,
					'{booking_date}' => $tour_date,
					'{infant}'     => $infant,
					'{id}' => $post_id,
					'{title}' => urlencode(get_the_title($post_id)),
					'{extras}' => sanitize_text_field($_POST["tour_extra"]),
				);

				if( $pricing_rule == 'group' ) {
					$external_search_info['{amount}'] = $group_price;
				} else {
					$external_search_info['{amount}'] = ( $adult_price * $adults ) + ( $children * $children_price ) + ( $infant * $infant_price );
				}

				if( !empty($tours_extra)) {
					$external_search_info['{amount}'] += $tour_extra_total;
				}

				if(!empty($tf_booking_attribute)){
					$tf_booking_query_url = str_replace(array_keys($external_search_info), array_values($external_search_info), $tf_booking_query_url);
					if( !empty($tf_booking_query_url) ){
						$tf_booking_url = $tf_booking_url.'/?'.$tf_booking_query_url;
					}
				}

				$response['product_id']  = $product_id;
				$response['add_to_cart'] = 'true';
				$response['redirect_to'] = html_entity_decode($tf_booking_url);
			}else{
				// Add product to cart with the custom cart item data
				WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_tours_data );

				$response['product_id']  = $product_id;
				$response['add_to_cart'] = 'true';
				$response['redirect_to'] = $instantio_is_active == 1 ? ($quick_checkout == 0 ? wc_get_checkout_url() : '') : wc_get_checkout_url();
			}

		} else {
			# Show errors
			$response['status'] = 'error';

		}
		$response['without_payment'] = 'false';
	}

	// Json Response
	echo wp_json_encode( $response );

	# Close ajax
	die();
}

/**
 * Set tour price in WooCommerce
 */
function tf_tours_set_order_price( $cart ) {

	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
		return;
	}

	foreach ( $cart->get_cart() as $cart_item ) {

		if ( isset( $cart_item['tf_tours_data']['price'] ) && ! empty( $cart_item['tf_tours_data']['tour_extra_total'] ) ) {
			$cart_item['data']->set_price( $cart_item['tf_tours_data']['price'] + $cart_item['tf_tours_data']['tour_extra_total'] );
		} elseif ( isset( $cart_item['tf_tours_data']['price'] ) && empty( $cart_item['tf_tours_data']['tour_extra_total'] ) ) {
			$cart_item['data']->set_price( $cart_item['tf_tours_data']['price'] );
		}

	}
}

add_action( 'woocommerce_before_calculate_totals', 'tf_tours_set_order_price', 30, 1 );

/**
 * Show custom data in Cart & checkout
 */
add_filter( 'woocommerce_get_item_data', 'tf_tours_cart_item_custom_data', 10, 2 );
function tf_tours_cart_item_custom_data( $item_data, $cart_item ) {

	// Assigning data into variables
	$tour_type        = ! empty( $cart_item['tf_tours_data']['tour_type'] ) ? $cart_item['tf_tours_data']['tour_type'] : '';
	$adults_number    = ! empty( $cart_item['tf_tours_data']['adults'] ) ? $cart_item['tf_tours_data']['adults'] : '';
	$childrens_number = ! empty( $cart_item['tf_tours_data']['childrens'] ) ? $cart_item['tf_tours_data']['childrens'] : '';
	$infants_number   = ! empty( $cart_item['tf_tours_data']['infants'] ) ? $cart_item['tf_tours_data']['infants'] : '';
	$start_date       = ! empty( $cart_item['tf_tours_data']['start_date'] ) ? $cart_item['tf_tours_data']['start_date'] : '';
	$end_date         = ! empty( $cart_item['tf_tours_data']['end_date'] ) ? $cart_item['tf_tours_data']['end_date'] : '';
	$tour_date        = ! empty( $cart_item['tf_tours_data']['tour_date'] ) ? $cart_item['tf_tours_data']['tour_date'] : '';
	$tour_time        = ! empty( $cart_item['tf_tours_data']['tour_time'] ) ? $cart_item['tf_tours_data']['tour_time'] : '';
	$tour_extra       = ! empty( $cart_item['tf_tours_data']['tour_extra_title'] ) ? $cart_item['tf_tours_data']['tour_extra_title'] : '';
	$due              = ! empty( $cart_item['tf_tours_data']['due'] ) ? $cart_item['tf_tours_data']['due'] : null;

	/**
	 * Show data in cart & checkout
	 */
	// Adults
	if ( $adults_number && $adults_number >= 1 ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Adults', 'tourfic' ),
			'value' => $adults_number,
		);
	}
	// Childrens
	if ( $childrens_number && $childrens_number >= 1 ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Children', 'tourfic' ),
			'value' => $childrens_number,
		);
	}
	// Infants
	if ( $infants_number && $infants_number >= 1 ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Infant', 'tourfic' ),
			'value' => $infants_number,
		);
	}
	// Tour date, departure date
	if ( ! empty( $tour_type ) && $tour_type == 'fixed' ) {
		if ( $start_date && $end_date ) {
			$item_data[] = array(
				'key'   => esc_html__( 'Tour Date', 'tourfic' ),
				'value' => $start_date . ' - ' . $end_date,
			);
		}
	} elseif ( ! empty( $tour_type ) && $tour_type == 'continuous' ) {
		if ( $tour_date ) {
			$item_data[] = array(
				'key'   => esc_html__( 'Tour Date', 'tourfic' ),
				'value' => gmdate( "F j, Y", strtotime( $tour_date ) ),
			);
		}
		if($tour_time){
			$item_data[] = array(
				'key'   => esc_html__( 'Tour Time', 'tourfic' ),
				'value' => $tour_time,
			);
		}
	}
	// Tour extras
	if ( $tour_extra ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Tour Extra', 'tourfic' ),
			'value' => $tour_extra,
		);
	}

	// Due amount from deposit
	if ( ! empty( $due ) ) {
		$item_data[] = [
			'key'   => esc_html__( 'Due ', 'tourfic' ),
			'value' => wp_strip_all_tags(wc_price( $due )),
		];
	}

	return $item_data;

}

/**
 * Show custom data in order details
 */
add_action( 'woocommerce_checkout_create_order_line_item', 'tf_tour_custom_order_data', 10, 4 );
function tf_tour_custom_order_data( $item, $cart_item_key, $values, $order ) {

	// Assigning data into variables
	$order_type       = ! empty( $values['tf_tours_data']['order_type'] ) ? $values['tf_tours_data']['order_type'] : '';
	$post_author      = ! empty( $values['tf_tours_data']['post_author'] ) ? $values['tf_tours_data']['post_author'] : '';
	$tour_id          = ! empty( $values['tf_tours_data']['tour_id'] ) ? $values['tf_tours_data']['tour_id'] : '';
	$tour_type        = ! empty( $values['tf_tours_data']['tour_type'] ) ? $values['tf_tours_data']['tour_type'] : '';
	$adults_number    = ! empty( $values['tf_tours_data']['adults'] ) ? $values['tf_tours_data']['adults'] : '';
	$childrens_number = ! empty( $values['tf_tours_data']['childrens'] ) ? $values['tf_tours_data']['childrens'] : '';
	$infants_number   = ! empty( $values['tf_tours_data']['infants'] ) ? $values['tf_tours_data']['infants'] : '';
	$start_date       = ! empty( $values['tf_tours_data']['start_date'] ) ? $values['tf_tours_data']['start_date'] : '';
	$end_date         = ! empty( $values['tf_tours_data']['end_date'] ) ? $values['tf_tours_data']['end_date'] : '';
	$tour_time        = ! empty( $values['tf_tours_data']['tour_time'] ) ? $values['tf_tours_data']['tour_time'] : '';
	$tour_date        = ! empty( $values['tf_tours_data']['tour_date'] ) ? $values['tf_tours_data']['tour_date'] : '';
	$tour_extra       = ! empty( $values['tf_tours_data']['tour_extra_title'] ) ? $values['tf_tours_data']['tour_extra_title'] : '';
	$due              = ! empty( $values['tf_tours_data']['due'] ) ? $values['tf_tours_data']['due'] : null;
	$visitor_details  = ! empty( $values['tf_tours_data']['visitor_details'] ) ? $values['tf_tours_data']['visitor_details'] : '';


	/**
	 * Show data in order meta & email
	 *
	 */
	if ( $order_type ) {
		$item->update_meta_data( '_order_type', $order_type );
	}

	if ( $post_author ) {
		$item->update_meta_data( '_post_author', $post_author );
	}

	if ( $tour_id ) {
		$item->update_meta_data( '_tour_id', $tour_id );
	}

	if ( $adults_number && $adults_number > 0 ) {
		$item->update_meta_data( 'Adults', $adults_number );
	}

	if ( $childrens_number && $childrens_number > 0 ) {
		$item->update_meta_data( 'Children', $childrens_number );
	}

	if ( $infants_number && $infants_number > 0 ) {
		$item->update_meta_data( 'Infants', $infants_number );
	}

	if ( $tour_type && $tour_type == 'fixed' ) {
		if ( $start_date && $end_date ) {
			$item->update_meta_data( 'Tour Date', $start_date . ' - ' . $end_date );
		}
	} elseif ( $tour_type && $tour_type == 'continuous' ) {
		if ( $tour_date ) {
			$item->update_meta_data( 'Tour Date', gmdate( "Y/m/d", strtotime( $tour_date ) ) );
		}
	}
	if($tour_time){
		$item->update_meta_data( 'Tour Time', $tour_time );
	}

	if ( $tour_extra ) {
		$item->update_meta_data( 'Tour Extra', $tour_extra );
	}

	if ( ! empty( $due ) ) {
		$item->update_meta_data( 'Due', wp_strip_all_tags(wc_price( $due ) ));
	}

	// Tour Unique ID 
	$item->update_meta_data( '_tour_unique_id', wp_rand());

	// visitor details
	if ( $visitor_details ) {
		$item->update_meta_data( '_visitor_details', $visitor_details );
	}

}

/**
 * Add order id to the hotel room meta field
 *
 * runs during WooCommerce checkout process
 *
 * @author fida
 */
function tf_add_order_tour_details_checkout_order_processed( $order_id, $posted_data, $order ) {

	$tf_integration_order_data = array(
		'order_id' => $order_id
	);
	$tf_integration_order_status = [];
	# Get and Loop Over Order Line Items
	foreach ( $order->get_items() as $item_id => $item ) {
		
		$order_type = $item->get_meta( '_order_type', true );

		if("tour"==$order_type){
			$post_id   = $item->get_meta( '_tour_id', true ); // Tour id

			//Tax Calculation
			$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
			$tax_labels = array();
			if(!empty($meta['is_taxable'])){
				$single_price = $item->get_subtotal();
				$finding_location = array(
					'country' => !empty($order->get_billing_country()) ? $order->get_billing_country() : '',
					'state' => !empty($order->get_billing_state()) ? $order->get_billing_state() : '',
					'postcode' => !empty($order->get_billing_postcode()) ? $order->get_billing_postcode() : '',
					'city' => !empty($order->get_billing_city()) ? $order->get_billing_city() : '',
					'tax_class' => !empty($meta['taxable_class']) && "standard"!=$meta['taxable_class'] ? $meta['taxable_class'] : ''
				);
	
				$tax_rate = WC_Tax::find_rates( $finding_location );
				if(!empty($tax_rate)){
					foreach($tax_rate as $rate){
						$tf_vat =  (float)$single_price * $rate['rate'] / 100;
						$tax_labels [] = array(
							'label' => $rate['label'],
							'price' => $tf_vat
						);
					}
					
				}
			}
		
			$fee_sums = array();
			// Sum the prices for each label
			foreach ( $tax_labels as $fee ) {
				$label = $fee["label"];
				$price = $fee["price"];
				if ( isset( $fee_sums[ $label ] ) ) {
					$fee_sums[ $label ] += $price;
				} else {
					$fee_sums[ $label ] = $price;
				}
			}
			
			//Order Data Insert 
			$billinginfo = [
				'billing_first_name' => $order->get_billing_first_name(),
				'billing_last_name' => $order->get_billing_last_name(),
				'billing_company' => $order->get_billing_company(),
				'billing_address_1' => $order->get_billing_address_1(),
				'billing_address_2' => $order->get_billing_address_2(),
				'billing_city' => $order->get_billing_city(),
				'billing_state' => $order->get_billing_state(),
				'billing_postcode' => $order->get_billing_postcode(),
				'billing_country' => $order->get_billing_country(),
				'billing_email' => $order->get_billing_email(),
				'billing_phone' => $order->get_billing_phone()
			];

			$shippinginfo = [
				'shipping_first_name' => $order->get_shipping_first_name(),
				'shipping_last_name' => $order->get_shipping_last_name(),
				'shipping_company' => $order->get_shipping_company(),
				'shipping_address_1' => $order->get_shipping_address_1(),
				'shipping_address_2' => $order->get_shipping_address_2(),
				'shipping_city' => $order->get_shipping_city(),
				'shipping_state' => $order->get_shipping_state(),
				'shipping_postcode' => $order->get_shipping_postcode(),
				'shipping_country' => $order->get_shipping_country(),
				'shipping_phone' => $order->get_shipping_phone()
			];

			// Tour Unique ID Store to Option
			$tour_ides = $item->get_meta( '_tour_unique_id', true );
			update_option( $tour_ides, $order_id);
			update_option( 'tf_order_uni_'.$order_id, $tour_ides);
			update_option( 'tf_order_tour_'.$tour_ides, $post_id);
			$tour_date = $item->get_meta( 'Tour Date', true );
			$tour_time = $item->get_meta( 'Tour Time', true );
			$price = $item->get_subtotal();
			$due = $item->get_meta( 'Due', true );
			$tour_extra = $item->get_meta( 'Tour Extra', true );
			$adult = $item->get_meta( 'Adults', true );
			$child = $item->get_meta( 'Children', true );
			$infants = $item->get_meta( 'Infants', true );
			$visitor_details = $item->get_meta( '_visitor_details', true );
			
			if ( $tour_date ) {
				if (str_contains($tour_date, " - ")) {
					list( $tour_in, $tour_out ) = explode( ' - ', $tour_date );
				} else {
					$tour_in = $tour_date;
					$tour_out = '';
				}
			}

			$iteminfo = [
				'tour_date' => $tour_date,
				'tour_time' => $tour_time,
				'tour_extra' => $tour_extra,
				'adult' => $adult,
				'child' => $child,
				'infants' => $infants,
				'total_price' => $price,
				'due_price' => $due,
				'unique_id' => $tour_ides,
				'visitor_details' => $visitor_details,
				'tax_info' => wp_json_encode($fee_sums)
			];

			$tf_integration_order_data[] = [
				'tour_date' => $tour_date,
				'tour_time' => $tour_time,
				'tour_extra' => $tour_extra,
				'adult' => $adult,
				'child' => $child,
				'infants' => $infants,
				'total_price' => $price,
				'due_price' => $due,
			];

			$tf_integration_order_status = [
				'customer_id' => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status' => $order->get_status(),
				'order_date' => gmdate('Y-m-d H:i:s')
			];

			$iteminfo_keys = array_keys($iteminfo);
			$iteminfo_keys = array_map('sanitize_key', $iteminfo_keys);

			$iteminfo_values = array_values($iteminfo);
			$iteminfo_values = array_map('sanitize_text_field', $iteminfo_values);

			$iteminfo = array_combine($iteminfo_keys, $iteminfo_values);
			
			global $wpdb;     
			$wpdb->query(
				$wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}tf_order_data
				( order_id, post_id, post_type, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
					array(
						$order_id,
						sanitize_key( $post_id ),
						$order_type,
						$tour_in,
						$tour_out,
						wp_json_encode($billinginfo),
						wp_json_encode($shippinginfo),
						wp_json_encode($iteminfo),
						$order->get_customer_id(),
						$order->get_payment_method(),
						$order->get_status(),
						gmdate('Y-m-d H:i:s')
					)
				)
			);
		}
	}

	/**
	 * New Order Pabbly Integration
	 * @author Jahid
	 */

	if ( function_exists('is_tf_pro') && is_tf_pro() && !empty($tf_integration_order_status) ) {
		do_action( 'tf_new_order_pabbly_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status);
		do_action( 'tf_new_order_zapier_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status);
	} 
}

add_action( 'woocommerce_checkout_order_processed', 'tf_add_order_tour_details_checkout_order_processed', 10, 4 );

/**
 * Add order id to the tour meta field
 * runs during WooCommerce checkout process for block checkout
 * @param $order
 * @return void
 * @since 2.11.10
 * @author Foysal
 */
function tf_add_order_tour_details_checkout_order_processed_block_checkout( $order ) {

	$order_id = $order->get_id();

	$tf_integration_order_data = array(
		'order_id' => $order_id,
	);
	$tf_integration_order_status = [];
	# Get and Loop Over Order Line Items
	foreach ( $order->get_items() as $item_id => $item ) {

		$order_type = $item->get_meta( '_order_type', true );

		if("tour"==$order_type){
			$post_id   = $item->get_meta( '_tour_id', true ); // Tour id

			//Tax Calculation
			$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
			$tax_labels = array();
			if(!empty($meta['is_taxable'])){
				$single_price = $item->get_subtotal();
				$finding_location = array(
					'country' => !empty($order->get_billing_country()) ? $order->get_billing_country() : '',
					'state' => !empty($order->get_billing_state()) ? $order->get_billing_state() : '',
					'postcode' => !empty($order->get_billing_postcode()) ? $order->get_billing_postcode() : '',
					'city' => !empty($order->get_billing_city()) ? $order->get_billing_city() : '',
					'tax_class' => !empty($meta['taxable_class']) && "standard"!=$meta['taxable_class'] ? $meta['taxable_class'] : ''
				);
	
				$tax_rate = WC_Tax::find_rates( $finding_location );
				if(!empty($tax_rate)){
					foreach($tax_rate as $rate){
						$tf_vat =  (float)$single_price * $rate['rate'] / 100;
						$tax_labels [] = array(
							'label' => $rate['label'],
							'price' => $tf_vat
						);
					}
					
				}
			}
		
			$fee_sums = array();
			// Sum the prices for each label
			foreach ( $tax_labels as $fee ) {
				$label = $fee["label"];
				$price = $fee["price"];
				if ( isset( $fee_sums[ $label ] ) ) {
					$fee_sums[ $label ] += $price;
				} else {
					$fee_sums[ $label ] = $price;
				}
			}

			//Order Data Insert
			$billinginfo = [
				'billing_first_name' => $order->get_billing_first_name(),
				'billing_last_name' => $order->get_billing_last_name(),
				'billing_company' => $order->get_billing_company(),
				'billing_address_1' => $order->get_billing_address_1(),
				'billing_address_2' => $order->get_billing_address_2(),
				'billing_city' => $order->get_billing_city(),
				'billing_state' => $order->get_billing_state(),
				'billing_postcode' => $order->get_billing_postcode(),
				'billing_country' => $order->get_billing_country(),
				'billing_email' => $order->get_billing_email(),
				'billing_phone' => $order->get_billing_phone()
			];

			$shippinginfo = [
				'shipping_first_name' => $order->get_shipping_first_name(),
				'shipping_last_name' => $order->get_shipping_last_name(),
				'shipping_company' => $order->get_shipping_company(),
				'shipping_address_1' => $order->get_shipping_address_1(),
				'shipping_address_2' => $order->get_shipping_address_2(),
				'shipping_city' => $order->get_shipping_city(),
				'shipping_state' => $order->get_shipping_state(),
				'shipping_postcode' => $order->get_shipping_postcode(),
				'shipping_country' => $order->get_shipping_country(),
				'shipping_phone' => $order->get_shipping_phone()
			];

			// Tour Unique ID Store to Option
			$tour_ides = $item->get_meta( '_tour_unique_id', true );
			update_option( $tour_ides, $order_id);
			update_option( 'tf_order_uni_'.$order_id, $tour_ides);
			update_option( 'tf_order_tour_'.$tour_ides, $post_id);
			$tour_date = $item->get_meta( 'Tour Date', true );
			$tour_time = $item->get_meta( 'Tour Time', true );
			$price = $item->get_subtotal();
			$due = $item->get_meta( 'Due', true );
			$tour_extra = $item->get_meta( 'Tour Extra', true );
			$adult = $item->get_meta( 'Adults', true );
			$child = $item->get_meta( 'Children', true );
			$infants = $item->get_meta( 'Infants', true );
			$visitor_details = $item->get_meta( '_visitor_details', true );

			if ( $tour_date ) {
				if( str_contains($tour_date, " - ") ){
					list( $tour_in, $tour_out ) = explode( ' - ', $tour_date );
				} else {
					$tour_in = $tour_date;
					$tour_out = '';
				}
			}

			$iteminfo = [
				'tour_date' => $tour_date,
				'tour_time' => $tour_time,
				'tour_extra' => $tour_extra,
				'adult' => $adult,
				'child' => $child,
				'infants' => $infants,
				'total_price' => $price,
				'due_price' => $due,
				'unique_id' => $tour_ides,
				'visitor_details' => $visitor_details,
				'tax_info' => wp_json_encode($fee_sums)
			];

			$tf_integration_order_data[] = [
				'tour_date' => $tour_date,
				'tour_time' => $tour_time,
				'tour_extra' => $tour_extra,
				'adult' => $adult,
				'child' => $child,
				'infants' => $infants,
				'total_price' => $price,
				'due_price' => $due,
			];

			$tf_integration_order_status = [
				'customer_id' => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status' => $order->get_status(),
				'order_date' => gmdate('Y-m-d H:i:s')
			];

			$iteminfo_keys = array_keys($iteminfo);
			$iteminfo_keys = array_map('sanitize_key', $iteminfo_keys);

			$iteminfo_values = array_values($iteminfo);
			$iteminfo_values = array_map('sanitize_text_field', $iteminfo_values);

			$iteminfo = array_combine($iteminfo_keys, $iteminfo_values);

			global $wpdb;
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}tf_order_data
				( order_id, post_id, post_type, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
					array(
						$order_id,
						sanitize_key( $post_id ),
						$order_type,
						$tour_in,
						$tour_out,
						wp_json_encode($billinginfo),
						wp_json_encode($shippinginfo),
						wp_json_encode($iteminfo),
						$order->get_customer_id(),
						$order->get_payment_method(),
						$order->get_status(),
						gmdate('Y-m-d H:i:s')
					)
				)
			);
		}
	}

	/**
	 * New Order Pabbly Integration
	 * @author Jahid
	 */

	if ( function_exists('is_tf_pro') && is_tf_pro() && !empty($tf_integration_order_status) ) {
		do_action( 'tf_new_order_pabbly_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status);
		do_action( 'tf_new_order_zapier_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status);
	}
}
add_action('woocommerce_store_api_checkout_order_processed', 'tf_add_order_tour_details_checkout_order_processed_block_checkout');


/*
* Admin order data migration
* @author Jahid
* @since 2.9.28
*/ 

function tf_tour_unique_id_order_data_migration(){

	if ( empty( get_option( 'tf_old_tour_order_unique_id_data_migrate' ) ) ) {

		global $wpdb;
		$tf_old_order_limit = new WC_Order_Query( array (
			'limit' => -1,
			'orderby' => 'date',
			'order' => 'ASC',
			'return' => 'ids',
		) );
		$order = $tf_old_order_limit->get_orders();

		foreach ( $order as $item_id => $item ) {
			$itemmeta = wc_get_order( $item);
			if ( is_a( $itemmeta, 'WC_Order_Refund' ) ) {
				$itemmeta = wc_get_order( $itemmeta->get_parent_id() );
			}

			foreach ( $itemmeta->get_items() as $item_key => $item_values ) {
				$order_type   = wc_get_order_item_meta( $item_key, '_order_type', true );
				
				if("tour"==$order_type){
					$post_id   = wc_get_order_item_meta( $item_key, '_tour_id', true );
					$unique_id   = wc_get_order_item_meta( $item_key, '_tour_unique_id', true );

					$tf_order_checked = $wpdb->get_row( $wpdb->prepare("SELECT id,order_details FROM {$wpdb->prefix}tf_order_data WHERE order_id=%s AND post_id=%s",$item,$post_id) );
					if( !empty($tf_order_checked) && !empty($unique_id) ){
						$order_details = json_decode($tf_order_checked->order_details);
						if(empty($order_details->unique_id)){
							$order_details->unique_id = $unique_id;
							$wpdb->query(
								$wpdb->prepare("UPDATE {$wpdb->prefix}tf_order_data SET order_details=%s WHERE id=%d",wp_json_encode($order_details), $tf_order_checked->id)
							);

						}
					}
				}
			}
				
		}

		wp_cache_flush();
		flush_rewrite_rules( true );
		update_option( 'tf_old_tour_order_unique_id_data_migrate', 1 );
	}
}

add_action( 'admin_init', 'tf_tour_unique_id_order_data_migration' );
?>