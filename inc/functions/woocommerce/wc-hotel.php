<?php
defined( 'ABSPATH' ) || exit;

/**
 * Hotel booking ajax function
 *
 * @since 2.2.0
 */
add_action( 'wp_ajax_tf_hotel_booking', 'tf_hotel_booking_callback' );
add_action( 'wp_ajax_nopriv_tf_hotel_booking', 'tf_hotel_booking_callback' );

/**
 * Handles AJAX for Booking
 *
 * @return void
 * @throws Exception
 * @since 2.2.0
 */


function tf_hotel_booking_callback() {

	// Check nonce security
	if ( ! isset( $_POST['tf_room_booking_nonce'] ) || ! wp_verify_nonce( $_POST['tf_room_booking_nonce'], 'check_room_booking_nonce' ) ) {
		return;
	}

	// Declaring errors & hotel data array
	$response     = [];
	$tf_room_data = [];
	/**
	 * Data from booking form
	 *
	 * With errors
	 */
	$post_id   = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : null;
	$room_id   = isset( $_POST['room_id'] ) ? intval( sanitize_text_field( $_POST['room_id'] ) ) : null;
	$unique_id = isset( $_POST['unique_id'] ) ? sanitize_text_field( $_POST['unique_id'] ) : null;
	$location  = isset( $_POST['location'] ) ? sanitize_text_field( $_POST['location'] ) : '';
	// People number
	$adult          = isset( $_POST['adult'] ) ? intval( sanitize_text_field( $_POST['adult'] ) ) : '0';
	$child          = isset( $_POST['child'] ) ? intval( sanitize_text_field( $_POST['child'] ) ) : '0';
	$children_ages  = isset( $_POST['children_ages'] ) ? sanitize_text_field( $_POST['children_ages'] ) : '0';
	$room_selected  = isset( $_POST['room'] ) ? intval( sanitize_text_field( $_POST['room'] ) ) : '0';
	$check_in       = isset( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
	$check_out      = isset( $_POST['check_out_date'] ) ? sanitize_text_field( $_POST['check_out_date'] ) : '';
	$deposit        = isset( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : false;
	$airport_service = isset($_POST['airport_service']) ? sanitize_text_field($_POST['airport_service']) : '';

	// Check errors
	if ( ! $check_in ) {
		$response['errors'][] = __( 'Check-in date missing.', 'tourfic' );
	}
	if ( ! $check_out ) {
		$response['errors'][] = __( 'Check-out date missing.', 'tourfic' );
	}
	if ( ! $adult ) {
		$response['errors'][] = __( 'Select Adult(s).', 'tourfic' );
	}
	if ( ! $room_selected ) {
		$response['errors'][] = __( 'Select Room(s).', 'tourfic' );
	}
	if ( ! $post_id ) {
		$response['errors'][] = __( 'Unknown Error! Please try again.', 'tourfic' );
	}

	/**
	 * Backend options panel data
	 *
	 * @since 2.2.0
	 */
	$product_id    = get_post_meta( $post_id, 'product_id', true );
	$post_author   = get_post_field( 'post_author', $post_id );
	$meta          = get_post_meta( $post_id, 'tf_hotels_opt', true );
	$rooms         = ! empty( $meta['room'] ) ? $meta['room'] : '';
	if( !empty($rooms) && gettype($rooms)=="string" ){
		$tf_hotel_rooms_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
			return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
		}, $rooms );
		$rooms = unserialize( $tf_hotel_rooms_value );
	}
	$avail_by_date = ! empty( $rooms[ $room_id ]['avil_by_date'] ) && $rooms[ $room_id ]['avil_by_date'];
	if ( $avail_by_date ) {
		$avail_date = ! empty( $rooms[ $room_id ]['avail_date'] ) ? json_decode($rooms[ $room_id ]['avail_date'], true) : [];
	}
	$room_name       = $rooms[ $room_id ]['title'];
	$pricing_by      = $rooms[ $room_id ]['pricing-by'];
	$price_multi_day = ! empty( $rooms[ $room_id ]['price_multi_day'] ) ? $rooms[ $room_id ]['price_multi_day'] : false;

	# Calculate night number
	if(!$price_multi_day){
		if ( $check_in && $check_out ) {
			$check_in_stt   = strtotime( $check_in );
			$check_out_stt  = strtotime( $check_out );
			$day_difference = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
		}
	}else{
		if ( $check_in && $check_out ) {
			$check_in_stt   = strtotime( $check_in . ' +1 day' );
			$check_out_stt  = strtotime( $check_out );
			$day_difference = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
		}
	}

	$room_stay_requirements = array( );
	foreach($rooms as $key => $room) {
		$room_stay_requirements[] = array (
			"uid" => !empty($room["unique_id"]) ? $room["unique_id"] : '',
			'min_stay' => !empty( $room["minimum_stay_requirement"]) ?  $room["minimum_stay_requirement"] : 0,
			"max_stay" => !empty($room["maximum_stay_requirement"]) ? $room["maximum_stay_requirement"] : 0
		);
	}

	foreach($room_stay_requirements as $min_max_days) {
		$room_uid = isset( $_POST['unique_id'] ) ? sanitize_text_field( $_POST['unique_id'] ) : null;

		if($day_difference < $min_max_days["min_stay"] && $min_max_days["min_stay"] > 0) {
			if($min_max_days["uid"] == $room_uid ){
				if( $min_max_days["max_stay"] == 0) {
					$response['errors'][] = __( "Your Stay Requirement is Minimum {$min_max_days['min_stay']} Days", 'tourfic' );
				} else {
					$response['errors'][] = __( "Your Stay Requirement is Minimum {$min_max_days['min_stay']} Days to Maximum {$min_max_days['max_stay']}", 'tourfic' );
				}
			}
		} else if ($day_difference > $min_max_days["max_stay"] && $min_max_days["max_stay"] > 0) {

			if ($min_max_days["uid"] == $room_uid ){
				$response['errors'][] = __( "Your Maximum Stay Requirement is {$min_max_days['max_stay']} Days", 'tourfic' );
			}
		}
	}
	// Hotel Room Discount Data
	$hotel_discount_type = !empty($rooms[$room_id]["discount_hotel_type"]) ? $rooms[$room_id]["discount_hotel_type"] : "none";
	$hotel_discount_amount = !empty($rooms[$room_id]["discount_hotel_price"]) ? $rooms[$room_id]["discount_hotel_price"] : 0;

	/**
	 * If no errors then process
	 */
	if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {

		$tf_room_data['tf_hotel_data']['order_type']         = 'hotel';
		$tf_room_data['tf_hotel_data']['post_id']            = $post_id;
		$tf_room_data['tf_hotel_data']['unique_id']          = $unique_id;
		$tf_room_data['tf_hotel_data']['post_permalink']     = get_permalink( $post_id );
		$tf_room_data['tf_hotel_data']['post_author']        = $post_author;
		$tf_room_data['tf_hotel_data']['post_id']            = $post_id;
		$tf_room_data['tf_hotel_data']['location']           = $location;
		$tf_room_data['tf_hotel_data']['check_in']           = $check_in;
		$tf_room_data['tf_hotel_data']['check_out']          = $check_out;
		$tf_room_data['tf_hotel_data']['room']               = $room_selected;
		$tf_room_data['tf_hotel_data']['room_id']            = $room_id;
		$tf_room_data['tf_hotel_data']['room_name']          = $room_name;
		$tf_room_data['tf_hotel_data']['air_serivicetype']   = $airport_service;
		$tf_room_data['tf_hotel_data']['air_serivice_avail'] = $meta['airport_service'] ?? null;

		// Discount Calculation and Checking

		$adult_price = !empty($rooms[$room_id]['adult_price']) ? $rooms[$room_id]['adult_price'] : 0;
		$child_price = !empty($rooms[$room_id]['child_price']) ? $rooms[$room_id]['child_price'] : 0;
		$room_price = !empty($rooms[$room_id]['price']) ? $rooms[$room_id]['price'] : '';


		if($hotel_discount_type == "percent") {
			if($pricing_by == 1) {
				$room_price = floatval( preg_replace( '/[^\d.]/', '',number_format( (int) $room_price - ( ( (int) $room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
			}
			if($pricing_by == 2) {
				$adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
				$child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
			}
		}

		/**
		 * Calculate Pricing
		 */
		if ( $avail_by_date && function_exists('is_tf_pro') && is_tf_pro() ) {

			if(!$price_multi_day){
				if ( $check_in && $check_out ) {
					// Check availability by date option
					$period = new DatePeriod(
						new DateTime( $check_in . ' 00:00' ),
						new DateInterval( 'P1D' ),
						new DateTime( $check_out . ' 23:59' )
					);
				}
			}else{
				if ( $check_in && $check_out ) {
					$period = new DatePeriod(
						new DateTime( $check_in . ' 00:00' ),
						new DateInterval( 'P1D' ),
						new DateTime( $check_out . ' 00:00' )
					);
				}
			}

			$total_price = 0;
			foreach ( $period as $date ) {

				$available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
					$date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
					$date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

					return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
				} ) );

				if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {
					$room_price  = ! empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $rooms[ $room_id ]['price'];
					$adult_price = ! empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $rooms[ $room_id ]['adult_price'];
					$child_price = ! empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $rooms[ $room_id ]['child_price'];

					if($hotel_discount_type == "percent") {
						if($pricing_by == 1) {
							$room_price = floatval( preg_replace( '/[^\d.]/', '',number_format( (int) $room_price - ( ( (int) $room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
						}
						if($pricing_by == 2) {
							$adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
							$child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
						}
					}

					$total_price += $pricing_by == '1' ? $room_price : ( ( $adult_price * $adult ) + ( $child_price * $child ) );

					if ( $pricing_by == '1' ) {
						$tf_room_data['tf_hotel_data']['adult'] = $adult;
						$tf_room_data['tf_hotel_data']['child'] = $child;
					}
					if ( $pricing_by == '2' ) {
						$tf_room_data['tf_hotel_data']['adult'] = $adult . " × " . strip_tags(wc_price($adult_price ));
						$tf_room_data['tf_hotel_data']['child'] = $child . " × " . strip_tags(wc_price( $child_price ));
					}

				};

			}

			$price_total = $total_price * $room_selected;

		} else {

			if ( $pricing_by == '1' ) {

				$total_price = $rooms[$room_id]['price'];

				if($hotel_discount_type == "percent") {
					$total_price = !empty($total_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $total_price - ( ( (int) $total_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
				}else if($hotel_discount_type == "fixed") {
					$total_price = !empty($total_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $total_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
				}

				$tf_room_data['tf_hotel_data']['adult']                  = $adult;
				$tf_room_data['tf_hotel_data']['child']                  = $child;
				$tf_room_data['tf_hotel_data']['children_ages']          = $children_ages;
			} elseif ( $pricing_by == '2' ) {
				$adult_price = $rooms[$room_id]['adult_price'];
				$child_price = $rooms[$room_id]['child_price'];

				if ($hotel_discount_type == "percent") {
					$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
				}
				$adult_price = $adult_price * $adult;
				$child_price = $child_price * $child;
				$total_price = $adult_price + $child_price;

				$tf_room_data['tf_hotel_data']['adult']          = $adult." × ".strip_tags(wc_price($adult_price));
				$tf_room_data['tf_hotel_data']['child']          = $child." × ".strip_tags(wc_price($child_price));
			}

			# Multiply pricing by night number
			$price_total = $total_price * ($room_selected * $day_difference);

		}
		# Set pricing
		$tf_room_data['tf_hotel_data']['price_total'] = $price_total;

		# Airport Service Fee
		if ( function_exists('is_tf_pro') && is_tf_pro() && ! empty( $tf_room_data['tf_hotel_data']['air_serivice_avail'] ) && 1 == $tf_room_data['tf_hotel_data']['air_serivice_avail'] ) {
			if ( "pickup" == $airport_service ) {
				$airport_pickup_price                        = ! empty( $meta['airport_pickup_price'] ) ? $meta['airport_pickup_price'] : '';
				if( !empty($airport_pickup_price) && gettype($airport_pickup_price)=="string" ){
					$tf_hotel_airport_pickup_price_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
						return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
					}, $airport_pickup_price );
					$airport_pickup_price = unserialize( $tf_hotel_airport_pickup_price_value );
				}
				$tf_room_data['tf_hotel_data']['price_type'] = $airport_pickup_price['airport_pickup_price_type'];
				if ( "per_person" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$service_adult_fee = !empty($airport_pickup_price['airport_service_fee_adult']) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee = !empty($airport_pickup_price['airport_service_fee_children']) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$airport_service_price_total = ( $adult *  $service_adult_fee ) + ( $child * $service_child_fee );

					$tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['price_total']       += $airport_service_price_total;
					if ( $child != 0 ) {

						$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( __( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
							$adult,
							strip_tags(wc_price( $service_adult_fee )),
							$child,
							strip_tags(wc_price( $service_child_fee )),
							strip_tags(wc_price( $airport_service_price_total ))
						);

					} else {
						$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( __( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
							$adult,
							strip_tags(wc_price( $service_adult_fee )),
							strip_tags(wc_price( $airport_service_price_total ))
						);

					}
				}
				if ( "fixed" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$airport_service_price_total                        = !empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['price_total']       += $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['air_service_info']  = sprintf( __( '( Fixed ) = %1$s', 'tourfic' ),
						strip_tags(wc_price( $airport_service_price_total ))
					);
				}
				if ( "free" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$tf_room_data['tf_hotel_data']['air_service_price'] = 0;
					$tf_room_data['tf_hotel_data']['price_total']       += 0;
					$tf_room_data['tf_hotel_data']['air_service_info']  = strip_tags(wc_price( 0 ));
				}
			}
			if ( "dropoff" == $airport_service ) {
				$airport_pickup_price = ! empty( $meta['airport_dropoff_price'] ) ? $meta['airport_dropoff_price'] : '';
				if( !empty($airport_pickup_price) && gettype($airport_pickup_price)=="string" ){
					$tf_hotel_airport_pickup_price_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
						return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
					}, $airport_pickup_price );
					$airport_pickup_price = unserialize( $tf_hotel_airport_pickup_price_value );
				}
				$tf_room_data['tf_hotel_data']['price_type'] = $airport_pickup_price['airport_pickup_price_type'];
				if ( "per_person" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$service_adult_fee = !empty($airport_pickup_price['airport_service_fee_adult']) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee = !empty($airport_pickup_price['airport_service_fee_children']) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$airport_service_price_total = ( $adult *  $service_adult_fee ) + ( $child * $service_child_fee );

					$tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['price_total']       += $airport_service_price_total;
					if ( $child != 0 ) {
						$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( __( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
							$adult,
							strip_tags(wc_price( $service_adult_fee )),
							$child,
							strip_tags(wc_price( $service_child_fee )),
							strip_tags(wc_price( $airport_service_price_total ))
						);

					} else {
						$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( __( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
							$adult,
							strip_tags(wc_price( $service_adult_fee )),
							strip_tags(wc_price( $airport_service_price_total ))
						);

					}
				}
				if ( "fixed" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$airport_service_price_total                        = !empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['price_total']       += $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['air_service_info']  = sprintf( __( '( Fixed ) = %1$s', 'tourfic' ),
						strip_tags(wc_price( $airport_service_price_total ))
					);
				}
				if ( "free" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$tf_room_data['tf_hotel_data']['air_service_price'] = 0;
					$tf_room_data['tf_hotel_data']['price_total']       += 0;
					$tf_room_data['tf_hotel_data']['air_service_info']  = strip_tags(wc_price( 0 ));
				}
			}
			if ( "both" == $airport_service ) {
				$airport_pickup_price                        = ! empty( $meta['airport_pickup_dropoff_price'] ) ? $meta['airport_pickup_dropoff_price'] : '';
				if( !empty($airport_pickup_price) && gettype($airport_pickup_price)=="string" ){
					$tf_hotel_airport_pickup_price_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
						return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
					}, $airport_pickup_price );
					$airport_pickup_price = unserialize( $tf_hotel_airport_pickup_price_value );
				}
				$tf_room_data['tf_hotel_data']['price_type'] = $airport_pickup_price['airport_pickup_price_type'];
				if ( "per_person" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$service_adult_fee = !empty($airport_pickup_price['airport_service_fee_adult']) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee = !empty($airport_pickup_price['airport_service_fee_children']) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$airport_service_price_total = ( $adult *  $service_adult_fee ) + ( $child * $service_child_fee );

					$tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['price_total']       += $airport_service_price_total;
					if ( $child != 0 ) {

						$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( __( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
							$adult,
							strip_tags(wc_price( $service_adult_fee )),
							$child,
							strip_tags(wc_price( $service_child_fee )),
							strip_tags(wc_price( $airport_service_price_total ))
						);

					} else {
						$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( __( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
							$adult,
							strip_tags(wc_price( $service_adult_fee )),
							strip_tags(wc_price( $airport_service_price_total ))
						);

					}
				}
				if ( "fixed" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$airport_service_price_total                        = !empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['price_total']       += $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['air_service_info']  = sprintf( __( '( Fixed ) = %1$s', 'tourfic' ),
						strip_tags(wc_price( $airport_service_price_total ))
					);
				}
				if ( "free" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$tf_room_data['tf_hotel_data']['air_service_price'] = 0;
					$tf_room_data['tf_hotel_data']['price_total']       += 0;
					$tf_room_data['tf_hotel_data']['air_service_info']  = strip_tags(wc_price( 0 ));
				}
			}
		}

		# check for deposit
		if ( $deposit == "true" ) {

			tf_get_deposit_amount( $rooms[ $room_id ], $price_total, $deposit_amount, $has_deposit );
			if ( function_exists('is_tf_pro') && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) {
				$tf_room_data['tf_hotel_data']['price_total'] = $deposit_amount;
				if ( ! empty( $airport_service ) ) {
					$tf_room_data['tf_hotel_data']['due'] = ( $price_total + $airport_service_price_total ) - $deposit_amount;
				} else {
					$tf_room_data['tf_hotel_data']['due'] = $price_total - $deposit_amount;
				}

			}
		}
		// Booking Type
		/*if ( function_exists('is_tf_pro') && is_tf_pro() ){
			$tf_booking_type = !empty($rooms[$room_id]['booking-by']) ? $rooms[$room_id]['booking-by'] : 1;
			$tf_booking_url = !empty($rooms[$room_id]['booking-url']) ? esc_url($rooms[$room_id]['booking-url']) : '';
			$tf_booking_query_url = !empty($rooms[$room_id]['booking-query']) ? $rooms[$room_id]['booking-query'] : 'adult={adult}&child={child}&room={room}';
			$tf_booking_attribute = !empty($rooms[$room_id]['booking-attribute']) ? $rooms[$room_id]['booking-attribute'] : '';
		}
		if( 2==$tf_booking_type && !empty($tf_booking_url) ){
			$external_search_info = array(
				'{adult}'    => $adult,
				'{child}'    => $child,
				'{checkin}'  => $check_in,
				'{checkout}' => $check_out,
				'{room}'     => $room_selected
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
		}else{*/
		# Add product to cart with the custom cart item data
		WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_room_data );

		$response['product_id']  = $product_id;
		$response['add_to_cart'] = 'true';
		$response['redirect_to'] = wc_get_checkout_url();
//		}
	} else {
		$response['status'] = 'error';
	}

	// Json Response
	echo wp_json_encode( $response );

	die();
}

/**
 * Over write WooCommerce Price
 */
function tf_hotel_set_order_price( $cart ) {

	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
		return;
	}

	foreach ( $cart->get_cart() as $cart_item ) {

		if ( isset( $cart_item['tf_hotel_data']['price_total'] ) ) {
			$cart_item['data']->set_price( $cart_item['tf_hotel_data']['price_total'] );
		}
	}

}

add_action( 'woocommerce_before_calculate_totals', 'tf_hotel_set_order_price', 30, 1 );

// Display custom cart item meta data (in cart and checkout)
function display_cart_item_custom_meta_data( $item_data, $cart_item ) {

	if ( isset( $cart_item['tf_hotel_data']['room_name'] ) ) {
		$item_data[] = array(
			'key'   => __( 'Room', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['room_name'],
		);
	}

	if ( isset( $cart_item['tf_hotel_data']['room'] ) && $cart_item['tf_hotel_data']['room'] > 0 ) {
		$item_data[] = array(
			'key'   => __( 'Number of Room Booked', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['room'],
		);
	}

	if ( isset( $cart_item['tf_hotel_data']['adult'] ) && $cart_item['tf_hotel_data']['adult'] >= 1 ) {
		$item_data[] = array(
			'key'   => __( 'Adult Number', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['adult'],
		);
	}

	if ( isset( $cart_item['tf_hotel_data']['child'] ) && $cart_item['tf_hotel_data']['child'] >=1 ) {
		$item_data[] = array(
			'key'       => __('Child Number', 'tourfic'),
			'value'     => $cart_item['tf_hotel_data']['child'],
		);
	}
	//Add children ages data to the cart item
	if ( isset( $cart_item['tf_hotel_data']['children_ages'] ) && $cart_item['tf_hotel_data']['children_ages'] != '' ) {
		$item_data[] = array(
			'key'       => __('Children Ages', 'tourfic'),
			'value'     => $cart_item['tf_hotel_data']['children_ages'],
		);
	}

	if ( isset( $cart_item['tf_hotel_data']['check_in'] ) ) {
		$item_data[] = array(
			'key'   => __( 'Check-in', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['check_in'],
		);
	}

	if ( isset( $cart_item['tf_hotel_data']['check_out'] ) ) {
		$item_data[] = array(
			'key'   => __( 'Check-out', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['check_out'],
		);
	}

	// airport service type

	if ( ! empty( $cart_item['tf_hotel_data']['air_serivice_avail'] ) && 1 == $cart_item['tf_hotel_data']['air_serivice_avail'] && ! empty( $cart_item['tf_hotel_data']['air_serivicetype'] ) && "pickup" == $cart_item['tf_hotel_data']['air_serivicetype'] ) {
		$item_data[] = array(
			'key'   => __( 'Airport Service', 'tourfic' ),
			'value' => __( 'Airport Pickup', 'tourfic' ),
		);
	}
	if ( ! empty( $cart_item['tf_hotel_data']['air_serivice_avail'] ) && 1 == $cart_item['tf_hotel_data']['air_serivice_avail'] && ! empty( $cart_item['tf_hotel_data']['air_serivicetype'] ) && "dropoff" == $cart_item['tf_hotel_data']['air_serivicetype'] ) {
		$item_data[] = array(
			'key'   => __( 'Airport Service', 'tourfic' ),
			'value' => __( 'Airport Dropoff', 'tourfic' ),
		);
	}
	if ( ! empty( $cart_item['tf_hotel_data']['air_serivice_avail'] ) && 1 == $cart_item['tf_hotel_data']['air_serivice_avail'] && ! empty( $cart_item['tf_hotel_data']['air_serivicetype'] ) && "both" == $cart_item['tf_hotel_data']['air_serivicetype'] ) {
		$item_data[] = array(
			'key'   => __( 'Airport Service', 'tourfic' ),
			'value' => __( 'Airport Pickup & Dropoff', 'tourfic' ),
		);
	}

	// airport price type

	if ( ! empty( $cart_item['tf_hotel_data']['air_serivice_avail'] ) && ! empty( $cart_item['tf_hotel_data']['air_service_info'] ) && 1 == $cart_item['tf_hotel_data']['air_serivice_avail'] && ! empty( $cart_item['tf_hotel_data']['air_serivicetype'] ) ) {
		$item_data[] = array(
			'key'   => __( 'Airport Service Fee', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['air_service_info'],
		);
	}

	if ( isset( $cart_item['tf_hotel_data']['due'] ) ) {
		$item_data[] = array(
			'key'   => __( 'Due', 'tourfic' ),
			'value' => strip_tags(wc_price( $cart_item['tf_hotel_data']['due'] )),
		);
	}

	return $item_data;

}

add_filter( 'woocommerce_get_item_data', 'display_cart_item_custom_meta_data', 10, 2 );

/**
 * Change cart item permalink
 */
function tf_hotel_cart_item_permalink( $permalink, $cart_item, $cart_item_key ) {

	$type = ! empty( $cart_item['tf_hotel_data']['order_type'] ) ? $cart_item['tf_hotel_data']['order_type'] : '';
	if ( is_cart() && $type == 'hotel' ) {
		$permalink = $cart_item['tf_hotel_data']['post_permalink'];
	}

	return $permalink;

}

add_filter( 'woocommerce_cart_item_permalink', 'tf_hotel_cart_item_permalink', 10, 3 );

/**
 * Show custom data in order details
 */
function tf_hotel_custom_order_data( $item, $cart_item_key, $values, $order ) {

	// Assigning data into variables
	$order_type = !empty($values['tf_hotel_data']['order_type']) ? $values['tf_hotel_data']['order_type'] : '';
	$post_author = !empty($values['tf_hotel_data']['post_author']) ? $values['tf_hotel_data']['post_author'] : '';
	$post_id = !empty($values['tf_hotel_data']['post_id']) ? $values['tf_hotel_data']['post_id'] : '';
	$unique_id = !empty($values['tf_hotel_data']['unique_id']) ? $values['tf_hotel_data']['unique_id'] : '';
	$room_name = !empty($values['tf_hotel_data']['room_name']) ? $values['tf_hotel_data']['room_name'] : '';
	$room_selected = !empty($values['tf_hotel_data']['room']) ? $values['tf_hotel_data']['room'] : '';
	$adult = !empty($values['tf_hotel_data']['adult']) ? $values['tf_hotel_data']['adult'] : '';
	$child = !empty($values['tf_hotel_data']['child']) ? $values['tf_hotel_data']['child'] : '';
	$children_ages = !empty($values['tf_hotel_data']['children_ages']) ? $values['tf_hotel_data']['children_ages'] : '';
	$check_in = !empty($values['tf_hotel_data']['check_in']) ? $values['tf_hotel_data']['check_in'] : '';
	$check_out = !empty($values['tf_hotel_data']['check_out']) ? $values['tf_hotel_data']['check_out'] : '';
	$due = !empty($values['tf_hotel_data']['due']) ? $values['tf_hotel_data']['due'] : '';
	$airport_service_type = !empty($values['tf_hotel_data']['air_serivicetype']) ? $values['tf_hotel_data']['air_serivicetype'] : null;
	$airport_fees = !empty($values['tf_hotel_data']['air_service_info']) ? $values['tf_hotel_data']['air_service_info'] : null;

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

	if ( $post_id ) {
		$item->update_meta_data( '_post_id', $post_id );
	}

	if ( $unique_id ) {
		$item->update_meta_data( '_unique_id', $unique_id );
	}

	if ( $room_name ) {

		$item->update_meta_data( 'room_name', $room_name );
	}

	if ( $room_selected && $room_selected > 0 ) {

		$item->update_meta_data( 'number_room_booked', $room_selected );
	}

	if ( $adult && $adult > 0 ) {

		$item->update_meta_data( 'adult', $adult );
	}

	if ( $child && $child > 0 ) {

		$item->update_meta_data( 'child', $child );
	}
	if ( $children_ages && $children_ages != '' ) {

		$item->update_meta_data( 'Children Ages', $children_ages );
	}

	if ( $check_in ) {

		$item->update_meta_data( 'check_in', $check_in );
	}

	if ( $check_out ) {

		$item->update_meta_data( 'check_out', $check_out );
	}

	if ( ! empty( $airport_service_type ) && $airport_service_type === 'pickup' ) {
		$item->update_meta_data( 'Airport Service', __( 'Airport Pickup', 'tourfic' ) );
	}
	if ( ! empty( $airport_service_type ) && $airport_service_type === 'dropoff' ) {
		$item->update_meta_data( 'Airport Service', __( 'Airport Dropoff', 'tourfic' ) );
	}
	if ( ! empty( $airport_service_type ) && $airport_service_type === 'both' ) {
		$item->update_meta_data( 'Airport Service', __( 'Airport Pickup & Dropoff', 'tourfic' ) );
	}
	if ( ! empty( $airport_fees ) ) {
		$item->update_meta_data( 'Airport Service Fee', $values['tf_hotel_data']['air_service_info'] );
	}

	if ( ! empty( $due ) ) {
		$item->update_meta_data( 'due', strip_tags(wc_price( $due )) );
	}


}

add_action( 'woocommerce_checkout_create_order_line_item', 'tf_hotel_custom_order_data', 10, 4 );

/**
 * Add order id to the hotel room meta field
 *
 * runs during WooCommerce checkout process
 *
 * @author fida
 */
function tf_add_order_id_room_checkout_order_processed( $order_id, $posted_data, $order ) {

	$tf_integration_order_data = array(
		'order_id' => $order_id
	);
	$tf_integration_order_status = [];
	# Get and Loop Over Order Line Items
	foreach ( $order->get_items() as $item_id => $item ) {

		$order_type = $item->get_meta( '_order_type', true );
		if("hotel"==$order_type){
			$post_id   = $item->get_meta( '_post_id', true ); // Hotel id
			$unique_id = $item->get_meta( '_unique_id', true ); // Unique id of rooms
			$meta      = get_post_meta( $post_id, 'tf_hotels_opt', true ); // Hotel meta
			$rooms     = ! empty( $meta['room'] ) ? $meta['room'] : '';
			if( !empty($rooms) && gettype($rooms)=="string" ){
				$tf_hotel_rooms_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
					return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
				}, $rooms );
				$rooms = unserialize( $tf_hotel_rooms_value );
			}
			$new_rooms = [];

			# Get and Loop Over Room Meta
			if ( ! empty( $rooms ) ) {
				foreach ( $rooms as $room ) {

					# Check if order is for this room
					if ( $room['unique_id'] == $unique_id ) {

						$old_order_id = $room['order_id'];

						$old_order_id != "" && $old_order_id .= ",";
						$old_order_id .= $order_id;

						# set old + new data to the oder_id meta
						$room['order_id'] = $old_order_id;
					}

					# Set whole room array
					$new_rooms[] = $room;
				}
			}

			# Set whole room array to the room meta
			$meta['room'] = $new_rooms;
			# Update hotel post meta with array values
			update_post_meta( $post_id, 'tf_hotels_opt', $meta );
		}

		// Hotel Item Data Insert
		if("hotel"==$order_type){

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

			$unique_id = $item->get_meta( '_unique_id', true ); // Unique id of rooms
			$room_selected = $item->get_meta( 'number_room_booked', true );
			$check_in = $item->get_meta( 'check_in', true );
			$check_out = $item->get_meta( 'check_out', true );
			$price = $item->get_subtotal();
			$due = $item->get_meta( 'due', true );
			$room_name = $item->get_meta( 'room_name', true );
			$adult = $item->get_meta( 'adult', true );
			$child = $item->get_meta( 'child', true );
			$children_ages = $item->get_meta( 'Children Ages', true );
			$airport_service_type = $item->get_meta( 'Airport Service', true );
			$airport_service_fee = $item->get_meta( 'Airport Service Fee', true );

			$iteminfo = [
				'room' => $room_selected,
				'room_unique_id' => $unique_id,
				'check_in' => $check_in,
				'check_out' => $check_out,
				'room_name' => $room_name,
				'adult' => $adult,
				'child' => $child,
				'children_ages' => $children_ages,
				'airport_service_type' => $airport_service_type,
				'airport_service_fee' => $airport_service_fee,
				'total_price' => $price,
				'due_price' => $due,
			];

			$tf_integration_order_data[] = [
				'room' => $room_selected,
				'check_in' => $check_in,
				'check_out' => $check_out,
				'room_name' => $room_name,
				'adult' => $adult,
				'child' => $child,
				'children_ages' => $children_ages,
				'airport_service_type' => $airport_service_type,
				'airport_service_fee' => $airport_service_fee,
				'total_price' => $price,
				'due_price' => $due,
				'customer_id' => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status' => $order->get_status(),
				'order_date' => date('Y-m-d H:i:s')
			];

			$tf_integration_order_status = [
				'customer_id' => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status' => $order->get_status(),
				'order_date' => date('Y-m-d H:i:s')
			];

			$iteminfo_keys = array_keys($iteminfo);
			$iteminfo_keys = array_map('sanitize_key', $iteminfo_keys);

			$iteminfo_values = array_values($iteminfo);
			$iteminfo_values = array_map('sanitize_text_field', $iteminfo_values);

			$iteminfo = array_combine($iteminfo_keys, $iteminfo_values);

			global $wpdb;
			$table_name = $wpdb->prefix.'tf_order_data';
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO $table_name
				( order_id, post_id, post_type, room_number, room_id, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
					array(
						$order_id,
						sanitize_key( $post_id ),
						$order_type,
						$room_selected,
						$unique_id,
						$check_in,
						$check_out,
						json_encode($billinginfo),
						json_encode($shippinginfo),
						json_encode($iteminfo),
						$order->get_customer_id(),
						$order->get_payment_method(),
						$order->get_status(),
						date('Y-m-d H:i:s')
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

add_action( 'woocommerce_checkout_order_processed', 'tf_add_order_id_room_checkout_order_processed', 10, 4 );



/*
* Admin order status change
* @author Jahid
*/

add_action('woocommerce_order_status_changed', 'tf_order_status_changed', 10, 4);

function tf_order_status_changed($order_id, $old_status, $new_status, $order)
{
	global $wpdb;
	$table_name = $wpdb->prefix.'tf_order_data';
	$tf_order_checked = $wpdb->query($wpdb->prepare("SELECT * FROM $table_name WHERE order_id=%s",$order_id));
	if( !empty($tf_order_checked) ){
		$wpdb->query(
			$wpdb->prepare("UPDATE $table_name SET ostatus=%s WHERE order_id=%s",$new_status,$order_id)
		);
	}

	$tf_integration_order_data = array(
		'order_id' => $order_id
	);
	$tf_integration_order_status = [];
	# Get and Loop Over Order Line Items
	foreach ( $order->get_items() as $item_id => $item ) {

		$order_type = $item->get_meta( '_order_type', true );

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

		// Order Type hotel/tour

		// Hotel Item Data Insert
		if("hotel"==$order_type){
			$unique_id = $item->get_meta( '_unique_id', true ); // Unique id of rooms
			$room_selected = $item->get_meta( 'number_room_booked', true );
			$check_in = $item->get_meta( 'check_in', true );
			$check_out = $item->get_meta( 'check_out', true );
			$price = $item->get_subtotal();
			$due = $item->get_meta( 'due', true );
			$room_name = $item->get_meta( 'room_name', true );
			$adult = $item->get_meta( 'adult', true );
			$child = $item->get_meta( 'child', true );
			$children_ages = $item->get_meta( 'Children Ages', true );
			$airport_service_type = $item->get_meta( 'Airport Service', true );
			$airport_service_fee = $item->get_meta( 'Airport Service Fee', true );

			$tf_integration_order_data[] = [
				'room' => $room_selected,
				'room_unique_id' => $unique_id,
				'check_in' => $check_in,
				'check_out' => $check_out,
				'room_name' => $room_name,
				'adult' => $adult,
				'child' => $child,
				'children_ages' => $children_ages,
				'airport_service_type' => $airport_service_type,
				'airport_service_fee' => $airport_service_fee,
				'total_price' => $price,
				'due_price' => $due,
				'customer_id' => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status' => $order->get_status(),
				'order_date' => date('Y-m-d H:i:s')
			];

			$tf_integration_order_status = [
				'customer_id' => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status' => $order->get_status(),
				'order_date' => date('Y-m-d H:i:s')
			];

		}

		// Tour Item Data Insert
		if("tour"==$order_type){
			$tour_date = $item->get_meta( 'Tour Date', true );
			$tour_time = $item->get_meta( 'Tour Time', true );
			$price = $item->get_subtotal();
			$due = $item->get_meta( 'Due', true );
			$tour_extra = $item->get_meta( 'Tour Extra', true );
			$adult = $item->get_meta( 'Adults', true );
			$child = $item->get_meta( 'Children', true );
			$infants = $item->get_meta( 'Infants', true );

			if ( $tour_date ) {
				list( $tour_in, $tour_out ) = explode( ' - ', $tour_date );
			}

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
				'order_date' => date('Y-m-d H:i:s')
			];

		}
	}

	/**
	 * New Order Pabbly Integration
	 * @author Jahid
	 */

	if ( function_exists('is_tf_pro') && is_tf_pro() ) {
		do_action( 'tf_new_order_pabbly_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status);
		do_action( 'tf_new_order_zapier_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status);
	}

}

/*
* Admin order data update
* @author Jahid
*/

add_action( 'woocommerce_saved_order_items', 'tf_woocommerce_before_save_order_items', 10, 2 );
function tf_woocommerce_before_save_order_items( $order_id, $items ) {

	$billinginfo = [
		'billing_first_name' => $items['_billing_first_name'],
		'billing_last_name' => $items['_billing_last_name'],
		'billing_company' => $items['_billing_company'],
		'billing_address_1' => $items['_billing_address_1'],
		'billing_address_2' => $items['_billing_address_2'],
		'billing_city' => $items['_billing_city'],
		'billing_state' => $items['_billing_state'],
		'billing_postcode' => $items['_billing_postcode'],
		'billing_country' => $items['_billing_country'],
		'billing_email' => $items['_billing_email'],
		'billing_phone' => $items['_billing_phone']
	];

	$shippinginfo = [
		'shipping_first_name' => $items['_shipping_first_name'],
		'shipping_last_name' => $items['_shipping_last_name'],
		'shipping_company' => $items['_shipping_company'],
		'shipping_address_1' => $items['_shipping_address_1'],
		'shipping_address_2' => $items['_shipping_address_2'],
		'shipping_city' => $items['_shipping_city'],
		'shipping_state' => $items['_shipping_state'],
		'shipping_postcode' => $items['_shipping_postcode'],
		'shipping_country' => $items['_shipping_country'],
		'shipping_phone' => $items['_shipping_phone']
	];
	$tf_payment_method = $items['_payment_method'];
	global $wpdb;
	$table_name = $wpdb->prefix.'tf_order_data';
	$tf_order_checked = $wpdb->query($wpdb->prepare("SELECT * FROM $table_name WHERE order_id=%s",$order_id));
	if( !empty($tf_order_checked) ){
		$wpdb->query(
			$wpdb->prepare("UPDATE $table_name SET billing_details=%s, shipping_details=%s, payment_method=%s WHERE order_id=%s",json_encode($billinginfo),json_encode($shippinginfo),$tf_payment_method, $order_id)
		);
	}
}

/*
* Admin order data migration
* @author Jahid
*/

function tf_admin_order_data_migration(){

	/**
	 * Order Data
	 * Create Order Data Database
	 * @author jahid
	 */

	global $wpdb;
	$order_table_name = $wpdb->prefix.'tf_order_data';
	$charset_collate = $wpdb->get_charset_collate();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$sql = "CREATE TABLE IF NOT EXISTS $order_table_name (
		 id bigint(20) NOT NULL AUTO_INCREMENT,
		 order_id bigint(20) NOT NULL,
		 post_id bigint(20) NOT NULL,
		 post_type varchar(255),
		 room_number varchar(255) NULL,
		 check_in date NOT NULL,  
		 check_out date NULL,  
		 billing_details text,
		 shipping_details text,
		 order_details text,
		 customer_id bigint(11) NOT NULL,
		 payment_method varchar(255),
		 ostatus varchar(255),
		 order_date datetime NOT NULL,
		 checkinout varchar(255) NULL,
		 checkinout_by varchar(255) NULL,
		 room_id varchar(255) NULL,
		 PRIMARY KEY  (id)
	 ) $charset_collate;";
	dbDelta( $sql );

	if ( empty( get_option( 'tf_old_order_data_migrate' ) ) ) {

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
			$tf_ordering_date =  $itemmeta->get_date_created();

			//Order Data Insert
			$billinginfo = [
				'billing_first_name' => !empty($itemmeta->get_billing_first_name()) ? $itemmeta->get_billing_first_name() : '',
				'billing_last_name' => !empty($itemmeta->get_billing_last_name()) ? $itemmeta->get_billing_last_name() : '',
				'billing_company' => !empty($itemmeta->get_billing_company()) ? $itemmeta->get_billing_company() : '',
				'billing_address_1' => !empty($itemmeta->get_billing_address_1()) ? $itemmeta->get_billing_address_1() : '',
				'billing_address_2' => !empty($itemmeta->get_billing_address_2()) ? $itemmeta->get_billing_address_2() : '',
				'billing_city' => !empty($itemmeta->get_billing_city()) ? $itemmeta->get_billing_city() : '',
				'billing_state' => !empty($itemmeta->get_billing_state()) ? $itemmeta->get_billing_state() : '',
				'billing_postcode' => !empty($itemmeta->get_billing_postcode()) ? $itemmeta->get_billing_postcode() : '',
				'billing_country' => !empty($itemmeta->get_billing_country()) ? $itemmeta->get_billing_country() : '',
				'billing_email' => !empty($itemmeta->get_billing_email()) ? $itemmeta->get_billing_email() : '',
				'billing_phone' => !empty($itemmeta->get_billing_phone()) ? $itemmeta->get_billing_phone() : ''
			];

			$shippinginfo = [
				'shipping_first_name' => !empty($itemmeta->get_shipping_first_name()) ? $itemmeta->get_shipping_first_name() : '',
				'shipping_last_name' => !empty($itemmeta->get_shipping_last_name()) ? $itemmeta->get_shipping_last_name() : '',
				'shipping_company' => !empty($itemmeta->get_shipping_company()) ? $itemmeta->get_shipping_company() : '',
				'shipping_address_1' => !empty($itemmeta->get_shipping_address_1()) ? $itemmeta->get_shipping_address_1() : '',
				'shipping_address_2' => !empty($itemmeta->get_shipping_address_2()) ? $itemmeta->get_shipping_address_2() : '',
				'shipping_city' => !empty($itemmeta->get_shipping_city()) ? $itemmeta->get_shipping_city() : '',
				'shipping_state' => !empty($itemmeta->get_shipping_state()) ? $itemmeta->get_shipping_state() : '',
				'shipping_postcode' => !empty($itemmeta->get_shipping_postcode()) ? $itemmeta->get_shipping_postcode() : '',
				'shipping_country' => !empty($itemmeta->get_shipping_country()) ? $itemmeta->get_shipping_country() : '',
				'shipping_phone' => !empty($itemmeta->get_shipping_phone()) ? $itemmeta->get_shipping_phone() : ''
			];

			foreach ( $itemmeta->get_items() as $item_key => $item_values ) {
				$order_type   = wc_get_order_item_meta( $item_key, '_order_type', true );
				if("hotel"==$order_type){
					$post_id   = wc_get_order_item_meta( $item_key, '_post_id', true );
					$unique_id = wc_get_order_item_meta( $item_key, '_unique_id', true );
					$room_selected = wc_get_order_item_meta( $item_key, 'number_room_booked', true );
					$check_in = wc_get_order_item_meta( $item_key, 'check_in', true );
					$check_out = wc_get_order_item_meta( $item_key, 'check_out', true );
					$price = $itemmeta->get_subtotal();
					$due = wc_get_order_item_meta( $item_key, 'due', true );
					$room_name = wc_get_order_item_meta( $item_key, 'room_name', true );
					$adult = wc_get_order_item_meta( $item_key, 'adult', true );
					$child = wc_get_order_item_meta( $item_key, 'child', true );
					$children_ages = wc_get_order_item_meta( $item_key, 'Children Ages', true );
					$airport_service_type = wc_get_order_item_meta( $item_key, 'Airport Service', true );
					$airport_service_fee = wc_get_order_item_meta( $item_key, 'Airport Service Fee', true );

					$iteminfo = [
						'room' => $room_selected,
						'room_unique_id' => $unique_id,
						'check_in' => $check_in,
						'check_out' => $check_out,
						'room_name' => $room_name,
						'adult' => $adult,
						'child' => $child,
						'children_ages' => $children_ages,
						'airport_service_type' => $airport_service_type,
						'airport_service_fee' => $airport_service_fee,
						'total_price' => $price,
						'due_price' => $due,
					];

					$iteminfo_keys = array_keys($iteminfo);
					$iteminfo_keys = array_map('sanitize_key', $iteminfo_keys);

					$iteminfo_values = array_values($iteminfo);
					$iteminfo_values = array_map('sanitize_text_field', $iteminfo_values);

					$iteminfo = array_combine($iteminfo_keys, $iteminfo_values);

					global $wpdb;
					$table_name = $wpdb->prefix.'tf_order_data';
					$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO $table_name
						( order_id, post_id, post_type, room_number, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
						VALUES ( %d, %d, %s, %d, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
							array(
								$item,
								sanitize_key( $post_id ),
								$order_type,
								$room_selected,
								$check_in,
								$check_out,
								json_encode($billinginfo),
								json_encode($shippinginfo),
								json_encode($iteminfo),
								$itemmeta->get_customer_id(),
								$itemmeta->get_payment_method(),
								$itemmeta->get_status(),
								$tf_ordering_date->date('Y-m-d H:i:s')
							)
						)
					);
				}
				if("tour"==$order_type){
					$post_id   = wc_get_order_item_meta( $item_key, '_tour_id', true );
					$tour_date = wc_get_order_item_meta( $item_key, 'Tour Date', true );
					$tour_time = wc_get_order_item_meta( $item_key, 'Tour Time', true );
					$price = $itemmeta->get_subtotal();
					$due = wc_get_order_item_meta( $item_key, 'Due', true );
					$tour_extra = wc_get_order_item_meta( $item_key, 'Tour Extra', true );
					$adult = wc_get_order_item_meta( $item_key, 'Adults', true );
					$child = wc_get_order_item_meta( $item_key, 'Children', true );
					$infants = wc_get_order_item_meta( $item_key, 'Infants', true );
					$datatype_check = preg_match("/-/", $tour_date);
					if ( !empty($tour_date) && !empty($datatype_check) ) {
						list( $tour_in, $tour_out ) = explode( ' - ', $tour_date );
					}
					if ( !empty($tour_date) && empty($datatype_check) ) {
						$tour_in = date( "Y-m-d", strtotime( $tour_date ) );
						$tour_out = "0000-00-00";
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
					];

					$iteminfo_keys = array_keys($iteminfo);
					$iteminfo_keys = array_map('sanitize_key', $iteminfo_keys);

					$iteminfo_values = array_values($iteminfo);
					$iteminfo_values = array_map('sanitize_text_field', $iteminfo_values);

					$iteminfo = array_combine($iteminfo_keys, $iteminfo_values);

					global $wpdb;
					$table_name = $wpdb->prefix.'tf_order_data';
					$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO $table_name
						( order_id, post_id, post_type, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
						VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
							array(
								$item,
								sanitize_key( $post_id ),
								$order_type,
								date( "Y-m-d", strtotime( $tour_in ) ),
								date( "Y-m-d", strtotime( $tour_out ) ),
								json_encode($billinginfo),
								json_encode($shippinginfo),
								json_encode($iteminfo),
								$itemmeta->get_customer_id(),
								$itemmeta->get_payment_method(),
								$itemmeta->get_status(),
								$tf_ordering_date->date('Y-m-d H:i:s')
							)
						)
					);
				}
			}

		}
		wp_cache_flush();
		flush_rewrite_rules( true );
		update_option( 'tf_old_order_data_migrate', 1 );
	}
}

add_action( 'admin_init', 'tf_admin_order_data_migration' );