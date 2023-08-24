<?php
defined( 'ABSPATH' ) || exit;

/**
 * Tour booking ajax function
 *
 * @since 2.2.0
 */
add_action( 'wp_ajax_tf_tours_booking', 'tf_tours_booking_function' );
add_action( 'wp_ajax_nopriv_tf_tours_booking', 'tf_tours_booking_function' );
function tf_tours_booking_function() {

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
	$tf_disable_payment = ! empty( $meta['disable_payment'] ) ? $meta['disable_payment'] : '';

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

	/**
	 * If fixed is selected but pro is not activated
	 *
	 * show error
	 *
	 * @return
	 */
	if ( $tour_type == 'fixed' && function_exists('is_tf_pro') && ! is_tf_pro() ) {
		$response['errors'][] = __( 'Fixed Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
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


		// Fixed tour maximum capacity limit
	
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($start_date) && !empty($end_date) ) {
			
			// Tour Order retrive from Tourfic Order Table
			$tf_orders_select = array(
				'select' => "post_id,order_details",
				'query' => "post_type = 'tour' AND ostatus = 'completed' ORDER BY order_id DESC"
			);
			$tf_tour_book_orders = tourfic_order_table_data($tf_orders_select);

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
					$response['errors'][] = __( 'Booking limit is Reached this Tour', 'tourfic' );
				}
				if( $tf_total_people!=$tf_tour_booking_limit && $tf_today_limit < $total_people_booking ){
					$response['errors'][] = sprintf( __( 'Only %1$s Adult/Children are available this Tour', 'tourfic' ), $tf_today_limit );
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
				'query' => "post_type = 'tour' AND ostatus = 'completed' ORDER BY order_id DESC"
			);
			$tf_tour_book_orders = tourfic_order_table_data($tf_orders_select);

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
					$response['errors'][] = __( 'Booking limit is Reached this Date', 'tourfic' );
				}
				if( $tf_total_people!=$tf_tour_booking_limit && $tf_today_limit < $total_people_booking ){
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
	if ( $tour_type == 'continuous' && $custom_avail == true && function_exists('is_tf_pro') && ! is_tf_pro() ) {
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
	$tour_extra_total = 0;
	$tour_extra_title_arr = [];
	$tour_extra_meta = ! empty( $meta['tour-extra'] ) ? $meta['tour-extra'] : '';
	if(!empty($tour_extra_meta)){
		$tours_extra = explode(',', $_POST['tour_extra']);
		foreach($tours_extra as $extra){
			$tour_extra_pricetype = !empty( $tour_extra_meta[$extra]['price_type'] ) ? $tour_extra_meta[$extra]['price_type'] : 'fixed';
			if( $tour_extra_pricetype=="fixed" ){
				if(!empty($tour_extra_meta[$extra]['title']) && !empty($tour_extra_meta[$extra]['price'])){
					$tour_extra_total += $tour_extra_meta[$extra]['price'];
					$tour_extra_title_arr[] =  $tour_extra_meta[$extra]['title']." (Fixed: ".wc_price($tour_extra_meta[$extra]['price']).")";
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

				// Daily Tour Booking Capacity && tour order retrive form tourfic order table
				$tf_orders_select = array(
					'select' => "post_id,order_details",
					'query' => "post_type = 'tour' AND ostatus = 'completed' ORDER BY order_id DESC"
				);
				$tf_tour_book_orders = tourfic_order_table_data($tf_orders_select);

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
						$response['errors'][] = __( 'Booking limit is Reached this Date', 'tourfic' );
					}
					if( $tf_total_people!=$tf_tour_booking_limit && $tf_today_limit < $total_people_booking ){
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
	if ( ! $post_id ) {
		$response['errors'][] = __( 'Unknown Error! Please try again.', 'tourfic' );
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

	if ( function_exists('is_tf_pro') && is_tf_pro() && $tour_type == 'continuous' ) {
		$tf_allowed_times = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';
		if( !empty($tf_allowed_times) && gettype($tf_allowed_times)=="string" ){
			$tf_tour_conti_custom_date = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
				return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
			}, $tf_allowed_times );
			$tf_allowed_times = unserialize( $tf_tour_conti_custom_date );
		}
		
		if ( $custom_avail == false && ! empty( $tf_allowed_times ) && empty( $tour_time_title  ) ) {
			$response['errors'][] = __( 'Please select time', 'tourfic' );
		}
		if ( $custom_avail == true && ! empty( $seasional_price[0]['allowed_time'] ) && empty( $tour_time_title  ) ) {
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

	if($tf_disable_payment){

		$tf_booking_fields = !empty(tfopt( 'book-confirm-field' )) ? tf_data_types(tfopt( 'book-confirm-field' )) : '';
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

		$order_details = [
			'order_by'    => '',
			'tour_date'   => $tour_date,
			'tour_time'   => $tour_time_title,
			'tour_extra'  => $tour_extra_title,
			'adult'       => $adults,
			'child'       => $children,
			'infants'     => $infant,
			'total_price' => $without_payment_price,
			'due_price'   => $without_payment_price,
			'visitor_details' => json_encode($tf_visitor_details)
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
			'status'           => 'processing',
			'order_date'       => date( 'Y-m-d H:i:s' ),
		);
		$response['without_payment'] = 'true';
		$order_id = tf_set_order( $order_data );
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
			$tf_tours_data['tf_tours_data']['visitor_details'] = json_encode($tf_visitor_details);
			if($tour_extra_title){
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
				$tf_tours_data['tf_tours_data']['adults']    = $adults . " × " . strip_tags(wc_price( $adult_price ));
				$tf_tours_data['tf_tours_data']['childrens'] = $children . " × " . strip_tags(wc_price( $children_price ));
				$tf_tours_data['tf_tours_data']['infants']   = $infant . " × " . strip_tags(wc_price( $infant_price ));
			}

			# Deposit information
			tf_get_deposit_amount( $meta, $tf_tours_data['tf_tours_data']['price'], $deposit_amount, $has_deposit );
			if ( function_exists('is_tf_pro') && is_tf_pro() && $has_deposit == true && $make_deposit == true ) {
				$tf_tours_data['tf_tours_data']['due']   = $tf_tours_data['tf_tours_data']['price'] - $deposit_amount;
				$tf_tours_data['tf_tours_data']['price'] = $deposit_amount;
			}

			// Booking Type
			if ( function_exists('is_tf_pro') && is_tf_pro() ){
				$tf_booking_type = !empty($meta['booking-by']) ? $meta['booking-by'] : 1;
				$tf_booking_url = !empty($meta['booking-url']) ? esc_url($meta['booking-url']) : '';
				$tf_booking_query_url = !empty($meta['booking-query']) ? $meta['booking-query'] : '';
				$tf_booking_attribute = !empty($meta['booking-attribute']) ? $meta['booking-attribute'] : '';
			}
			if( 2==$tf_booking_type && !empty($tf_booking_url) ){
				$external_search_info = array(
					'{adult}'    => $adults,
					'{child}'    => $children,
					'{booking_date}' => $tour_date,
					'{infant}'     => $infant
				);
				if(!empty($tf_booking_attribute)){
					$tf_booking_query_url = str_replace(array_keys($external_search_info), array_values($external_search_info), $tf_booking_query_url);
					if( !empty($tf_booking_query_url) ){
						$tf_booking_url = $tf_booking_url.'/?'.$tf_booking_query_url;
					}
				}

				$response['product_id']  = $product_id;
				$response['add_to_cart'] = 'true';
				$response['redirect_to'] = $tf_booking_url;
			}else{
				// Add product to cart with the custom cart item data
				WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_tours_data );

				$response['product_id']  = $product_id;
				$response['add_to_cart'] = 'true';
				$response['redirect_to'] = wc_get_checkout_url();
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
			'key'   => __( 'Adults', 'tourfic' ),
			'value' => $adults_number,
		);
	}
	// Childrens
	if ( $childrens_number && $childrens_number >= 1 ) {
		$item_data[] = array(
			'key'   => __( 'Children', 'tourfic' ),
			'value' => $childrens_number,
		);
	}
	// Infants
	if ( $infants_number && $infants_number >= 1 ) {
		$item_data[] = array(
			'key'   => __( 'Infant', 'tourfic' ),
			'value' => $infants_number,
		);
	}
	// Tour date, departure date
	if ( ! empty( $tour_type ) && $tour_type == 'fixed' ) {
		if ( $start_date && $end_date ) {
			$item_data[] = array(
				'key'   => __( 'Tour Date', 'tourfic' ),
				'value' => $start_date . ' - ' . $end_date,
			);
		}
	} elseif ( ! empty( $tour_type ) && $tour_type == 'continuous' ) {
		if ( $tour_date ) {
			$item_data[] = array(
				'key'   => __( 'Tour Date', 'tourfic' ),
				'value' => date( "F j, Y", strtotime( $tour_date ) ),
			);
		}
		if($tour_time){
			$item_data[] = array(
				'key'   => __( 'Tour Time', 'tourfic' ),
				'value' => $tour_time,
			);
		}
	}
	// Tour extras
	if ( $tour_extra ) {
		$item_data[] = array(
			'key'   => __( 'Tour Extra', 'tourfic' ),
			'value' => $tour_extra,
		);
	}

	// Due amount from deposit
	if ( ! empty( $due ) ) {
		$item_data[] = [
			'key'   => __( 'Due ', 'tourfic' ),
			'value' => strip_tags(wc_price( $due )),
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
			$item->update_meta_data( 'Tour Date', date( "Y/m/d", strtotime( $tour_date ) ) );
		}
	}
	if($tour_time){
		$item->update_meta_data( 'Tour Time', $tour_time );
	}

	if ( $tour_extra ) {
		$item->update_meta_data( 'Tour Extra', $tour_extra );
	}

	if ( ! empty( $due ) ) {
		$item->update_meta_data( 'Due', strip_tags(wc_price( $due ) ));
	}

	// Tour Unique ID 
	$item->update_meta_data( '_tour_unique_id', rand());

	// visitor details
	if ( $visitor_details ) {
		$item->update_meta_data( '_visitor_details', $visitor_details );
	}

}

?>