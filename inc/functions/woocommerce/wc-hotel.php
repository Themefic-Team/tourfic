<?php
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Room\Room;
use Tourfic\Classes\Hotel\Pricing;
use Tourfic\Classes\Helper;

/**
 * Hotel booking ajax function
 *
 * @since 2.2.0
 */
add_action( 'wp_ajax_tf_hotel_booking', 'tf_hotel_booking_callback' );
add_action( 'wp_ajax_nopriv_tf_hotel_booking', 'tf_hotel_booking_callback' );

function tf_hotel_booking_callback() {

	// Check nonce security
	if ( ! isset( $_POST['tf_room_booking_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tf_room_booking_nonce'] ) ), 'check_room_booking_nonce' ) ) {
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
	$option_id = isset( $_POST['option_id'] ) ? sanitize_text_field( $_POST['option_id'] ) : null;
	$location  = isset( $_POST['location'] ) ? sanitize_text_field( $_POST['location'] ) : '';
	// People number
	$adult           = isset( $_POST['adult'] ) ? intval( sanitize_text_field( $_POST['adult'] ) ) : '0';
	$child           = isset( $_POST['child'] ) ? intval( sanitize_text_field( $_POST['child'] ) ) : '0';
	$children_ages   = isset( $_POST['children_ages'] ) ? sanitize_text_field( $_POST['children_ages'] ) : '0';
	$room_selected   = isset( $_POST['room'] ) ? intval( sanitize_text_field( $_POST['room'] ) ) : '0';
	$check_in        = isset( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
	$check_out       = isset( $_POST['check_out_date'] ) ? sanitize_text_field( $_POST['check_out_date'] ) : '';
	$deposit         = isset( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : false;
	$airport_service = isset( $_POST['airport_service'] ) ? sanitize_text_field( $_POST['airport_service'] ) : '';
	$quick_checkout = !empty(Helper::tfopt( 'tf-quick-checkout' )) ? Helper::tfopt( 'tf-quick-checkout' ) : 0;
	$instantio_is_active = 0;

	if( is_plugin_active('instantio/instantio.php') ){
		$instantio_is_active = 1;
	}

	// Check errors
	if ( ! $check_in ) {
		$response['errors'][] = esc_html__( 'Check-in date missing.', 'tourfic' );
	}
	if ( ! $check_out ) {
		$response['errors'][] = esc_html__( 'Check-out date missing.', 'tourfic' );
	}
	if ( ! $adult ) {
		$response['errors'][] = esc_html__( 'Select Adult(s).', 'tourfic' );
	}
	if ( ! $room_selected ) {
		$response['errors'][] = esc_html__( 'Select Room(s).', 'tourfic' );
	}
	if ( ! $post_id ) {
		$response['errors'][] = esc_html__( 'Unknown Error! Please try again.', 'tourfic' );
	}

	/**
	 * Backend options panel data
	 *
	 * @since 2.2.0
	 */
	$product_id    = get_post_meta( $post_id, 'product_id', true );
	$post_author   = get_post_field( 'post_author', $post_id );
	$meta          = get_post_meta( $post_id, 'tf_hotels_opt', true );
	$room_meta     = get_post_meta( $room_id, 'tf_room_opt', true );
	$avail_by_date = ! empty( $room_meta['avil_by_date'] ) && $room_meta['avil_by_date'];
	if ( $avail_by_date ) {
		$avail_date = ! empty( $room_meta['avail_date'] ) ? json_decode( $room_meta['avail_date'], true ) : [];
	}
	$room_name       = get_the_title( $room_id );
	$pricing_by      = $room_meta['pricing-by'];
	$price_multi_day = ! empty( $room_meta['price_multi_day'] ) ? $room_meta['price_multi_day'] : false;

	# Calculate night number
	if ( ! $price_multi_day ) {
		if ( $check_in && $check_out ) {
			$check_in_stt   = strtotime( $check_in );
			$check_out_stt  = strtotime( $check_out );
			$day_difference = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
		}
	} else {
		if ( $check_in && $check_out ) {
			$check_in_stt   = strtotime( $check_in . ' +1 day' );
			$check_out_stt  = strtotime( $check_out );
			$day_difference = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
		}
	}

	$min_stay = ! empty( $room_meta["minimum_stay_requirement"] ) ? $room_meta["minimum_stay_requirement"] : 0;
	$max_stay = ! empty( $room_meta["maximum_stay_requirement"] ) ? $room_meta["maximum_stay_requirement"] : 0;

	if ( $day_difference < $min_stay && $min_stay > 0 ) {
		if ( $max_stay == 0 ) {
			/* translators: %1$s Minimum Stay Requirement */
			$response['errors'][] = sprintf( esc_html__( 'Your Stay Requirement is Minimum %1$s Days', 'tourfic' ), $min_stay );
		} else {
			/* translators: %1$s Minimum Stay Requirement, %2$s Maximum Stay Requirement */
			$response['errors'][] = sprintf( esc_html__( 'Your Stay Requirement is Minimum %1$s Days to Maximum %2$s Days', 'tourfic' ),
				$min_stay,
				$max_stay
			);
		}
	} else if ( $day_difference > $max_stay && $max_stay > 0 ) {
		/* translators: %1$s Maximum Stay Requirement */
		$response['errors'][] = sprintf( esc_html__( 'Your Maximum Stay Requirement is %1$s Days', 'tourfic' ), $max_stay );
	}

	// Hotel Room Discount Data
	$hotel_discount_type   = ! empty( $room_meta["discount_hotel_type"] ) ? $room_meta["discount_hotel_type"] : "none";
	$hotel_discount_amount = ! empty( $room_meta["discount_hotel_price"] ) ? $room_meta["discount_hotel_price"] : 0;

	/**
	 * If no errors then process
	 */
	if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {

		$tf_room_data['tf_hotel_data']['order_type']         = 'hotel';
		$tf_room_data['tf_hotel_data']['post_id']            = $post_id;
		$tf_room_data['tf_hotel_data']['unique_id']          = $unique_id;
		$tf_room_data['tf_hotel_data']['option_id']          = $option_id;
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

		/**
		 * Calculate Pricing
		 */
		if ( $avail_by_date && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

			if ( ! $price_multi_day ) {
				if ( $check_in && $check_out ) {
					// Check availability by date option
					$period = new DatePeriod(
						new DateTime( $check_in . ' 00:00' ),
						new DateInterval( 'P1D' ),
						new DateTime( $check_out . ' 23:59' )
					);
				}
			} else {
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

						$tf_room_data['tf_hotel_data']['adult'] = $adult;
						$tf_room_data['tf_hotel_data']['child'] = $child;
					} elseif ( $pricing_by == '2' ) {
						$total_price += ( $adult_price * $adult ) + ( $child_price * $child );

						$tf_room_data['tf_hotel_data']['adult'] = $adult . " × " . wp_strip_all_tags( wc_price( $adult_price ) );
						$tf_room_data['tf_hotel_data']['child'] = $child . " × " . wp_strip_all_tags( wc_price( $child_price ) );
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

								$tf_room_data['tf_hotel_data']['option'] = $data[ 'tf_option_title_' . $i ];
							}
						}
					}
				};

			}

			$price_total = $total_price * $room_selected;

		} else {

			if ( $pricing_by == '1' ) {

				$total_price = $room_meta['price'];

				if ( $hotel_discount_type == "percent" ) {
					$total_price = ! empty( $total_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $total_price - ( ( (int) $total_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
				} else if ( $hotel_discount_type == "fixed" ) {
					$total_price = ! empty( $total_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $total_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
				}

				$tf_room_data['tf_hotel_data']['adult']         = $adult;
				$tf_room_data['tf_hotel_data']['child']         = $child;
				$tf_room_data['tf_hotel_data']['children_ages'] = $children_ages;
			} elseif ( $pricing_by == '2' ) {
				$adult_price = $room_meta['adult_price'];
				$child_price = $room_meta['child_price'];

				if ( $hotel_discount_type == "percent" ) {
					$adult_price = ! empty( $adult_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					$child_price = ! empty( $child_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
				} else if ( $hotel_discount_type == "fixed" ) {
					$adult_price = ! empty( $adult_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
					$child_price = ! empty( $child_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
				}
				$adult_price = (int) $adult_price * (int) $adult;
				$child_price = (int) $child_price * (int) $child;
				$total_price = (int) $adult_price + (int) $child_price;

				$tf_room_data['tf_hotel_data']['adult'] = $adult . " × " . wp_strip_all_tags( wc_price( $adult_price ) );
				$tf_room_data['tf_hotel_data']['child'] = $child . " × " . wp_strip_all_tags( wc_price( $child_price ) );
			} elseif ( $pricing_by == '3' ) {
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

							$tf_room_data['tf_hotel_data']['option'] = $room_option['option_title'];
						}
					}
				}

				$tf_room_data['tf_hotel_data']['adult'] = $adult;
				$tf_room_data['tf_hotel_data']['child'] = $child;
			}

			# Multiply pricing by night number
			$price_total = $total_price * ( $room_selected * $day_difference );

		}

		# Set pricing
		$tf_room_data['tf_hotel_data']['price_total'] = $price_total;

		# Airport Service Fee
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $tf_room_data['tf_hotel_data']['air_serivice_avail'] ) && 1 == $tf_room_data['tf_hotel_data']['air_serivice_avail'] ) {
			if ( "pickup" == $airport_service ) {
				$airport_pickup_price = ! empty( $meta['airport_pickup_price'] ) ? $meta['airport_pickup_price'] : '';
				if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
					$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $airport_pickup_price );
					$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
				}
				$tf_room_data['tf_hotel_data']['price_type'] = $airport_pickup_price['airport_pickup_price_type'];
				if ( "per_person" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$service_adult_fee           = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee           = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$airport_service_price_total = ( $adult * $service_adult_fee ) + ( $child * $service_child_fee );

					$tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['price_total']       += $airport_service_price_total;
					if ( $child != 0 ) {

						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Child Count, %4$s Child Fee, %5$s Service Fee  */
						$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
							$adult,
							wp_strip_all_tags( wc_price( $service_adult_fee ) ),
							$child,
							wp_strip_all_tags( wc_price( $service_child_fee ) ),
							wp_strip_all_tags( wc_price( $airport_service_price_total ) )
						);

					} else {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Service Fee  */
						$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
							$adult,
							wp_strip_all_tags( wc_price( $service_adult_fee ) ),
							wp_strip_all_tags( wc_price( $airport_service_price_total ) )
						);

					}
				}
				if ( "fixed" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$airport_service_price_total                        = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['price_total']       += $airport_service_price_total;
					/* translators: %1$s Service Fee  */
					$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( esc_html__( '( Fixed ) = %1$s', 'tourfic' ),
						wp_strip_all_tags( wc_price( $airport_service_price_total ) )
					);
				}
				if ( "free" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$tf_room_data['tf_hotel_data']['air_service_price'] = 0;
					$tf_room_data['tf_hotel_data']['price_total']       += 0;
					$tf_room_data['tf_hotel_data']['air_service_info']  = wp_strip_all_tags( wc_price( 0 ) );
				}
			}
			if ( "dropoff" == $airport_service ) {
				$airport_pickup_price = ! empty( $meta['airport_dropoff_price'] ) ? $meta['airport_dropoff_price'] : '';
				if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
					$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $airport_pickup_price );
					$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
				}
				$tf_room_data['tf_hotel_data']['price_type'] = $airport_pickup_price['airport_pickup_price_type'];
				if ( "per_person" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$service_adult_fee           = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee           = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$airport_service_price_total = ( $adult * $service_adult_fee ) + ( $child * $service_child_fee );

					$tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['price_total']       += $airport_service_price_total;
					if ( $child != 0 ) {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Child Count, %4$s Child Fee, %5$s Service Fee  */
						$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
							$adult,
							wp_strip_all_tags( wc_price( $service_adult_fee ) ),
							$child,
							wp_strip_all_tags( wc_price( $service_child_fee ) ),
							wp_strip_all_tags( wc_price( $airport_service_price_total ) )
						);

					} else {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Service Fee  */
						$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
							$adult,
							wp_strip_all_tags( wc_price( $service_adult_fee ) ),
							wp_strip_all_tags( wc_price( $airport_service_price_total ) )
						);

					}
				}
				if ( "fixed" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$airport_service_price_total                        = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['price_total']       += $airport_service_price_total;
					/* translators: %1$s Service Fee  */
					$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( esc_html__( '( Fixed ) = %1$s', 'tourfic' ),
						wp_strip_all_tags( wc_price( $airport_service_price_total ) )
					);
				}
				if ( "free" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$tf_room_data['tf_hotel_data']['air_service_price'] = 0;
					$tf_room_data['tf_hotel_data']['price_total']       += 0;
					$tf_room_data['tf_hotel_data']['air_service_info']  = wp_strip_all_tags( wc_price( 0 ) );
				}
			}
			if ( "both" == $airport_service ) {
				$airport_pickup_price = ! empty( $meta['airport_pickup_dropoff_price'] ) ? $meta['airport_pickup_dropoff_price'] : '';
				if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
					$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $airport_pickup_price );
					$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
				}
				$tf_room_data['tf_hotel_data']['price_type'] = $airport_pickup_price['airport_pickup_price_type'];
				if ( "per_person" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$service_adult_fee           = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee           = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$airport_service_price_total = ( $adult * $service_adult_fee ) + ( $child * $service_child_fee );

					$tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['price_total']       += $airport_service_price_total;
					if ( $child != 0 ) {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Child Count, %4$s Child Fee, %5$s Service Fee  */
						$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) = %5$s', 'tourfic' ),
							$adult,
							wp_strip_all_tags( wc_price( $service_adult_fee ) ),
							$child,
							wp_strip_all_tags( wc_price( $service_child_fee ) ),
							wp_strip_all_tags( wc_price( $airport_service_price_total ) )
						);

					} else {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Service Fee  */
						$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( esc_html__( 'Adult ( %1$s × %2$s ) = %3$s', 'tourfic' ),
							$adult,
							wp_strip_all_tags( wc_price( $service_adult_fee ) ),
							wp_strip_all_tags( wc_price( $airport_service_price_total ) )
						);

					}
				}
				if ( "fixed" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$airport_service_price_total                        = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
					$tf_room_data['tf_hotel_data']['price_total']       += $airport_service_price_total;
					/* translators: %1$s Service Fee  */
					$tf_room_data['tf_hotel_data']['air_service_info'] = sprintf( esc_html__( '( Fixed ) = %1$s', 'tourfic' ),
						wp_strip_all_tags( wc_price( $airport_service_price_total ) )
					);
				}
				if ( "free" == $tf_room_data['tf_hotel_data']['price_type'] ) {
					$tf_room_data['tf_hotel_data']['air_service_price'] = 0;
					$tf_room_data['tf_hotel_data']['price_total']       += 0;
					$tf_room_data['tf_hotel_data']['air_service_info']  = wp_strip_all_tags( wc_price( 0 ) );
				}
			}
		}

		# check for deposit
		if ( $deposit == "true" ) {

			Helper::tf_get_deposit_amount( $room_meta, $price_total, $deposit_amount, $has_deposit );
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) {
				$tf_room_data['tf_hotel_data']['price_total'] = $deposit_amount;
				if ( ! empty( $airport_service ) ) {
					$tf_room_data['tf_hotel_data']['due'] = ( $price_total + $airport_service_price_total ) - $deposit_amount;
				} else {
					$tf_room_data['tf_hotel_data']['due'] = $price_total - $deposit_amount;
				}

			}
		}
		// Booking Type
		$tf_booking_type = $tf_booking_url = $tf_booking_query_url = $tf_booking_attribute = '';
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
			$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
			$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
			$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
		}
		if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
			$external_search_info = array(
				'{adult}'    => $adult,
				'{child}'    => $child,
				'{checkin}'  => $check_in,
				'{checkout}' => $check_out,
				'{room}'     => $room_selected
			);
			if ( ! empty( $tf_booking_attribute ) ) {
				$tf_booking_query_url = str_replace( array_keys( $external_search_info ), array_values( $external_search_info ), $tf_booking_query_url );
				if ( ! empty( $tf_booking_query_url ) ) {
					$tf_booking_url = $tf_booking_url . '/?' . $tf_booking_query_url;
				}
			}

			$response['product_id']  = $product_id;
			$response['add_to_cart'] = 'true';
			$response['redirect_to'] = $tf_booking_url;
		} else {
			# Add product to cart with the custom cart item data
			WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_room_data );

			$response['product_id']  = $product_id;
			$response['add_to_cart'] = 'true';
			$response['redirect_to'] = $instantio_is_active == 1 ? ($quick_checkout == 0 ? wc_get_checkout_url() : '') : wc_get_checkout_url();
		}
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
			'key'   => esc_html__( 'Room', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['room_name'],
		);
	}
	if ( isset( $cart_item['tf_hotel_data']['option'] ) ) {
		$item_data[] = array(
			'key'   => __( 'Option', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['option'],
		);
	}

	if ( isset( $cart_item['tf_hotel_data']['room'] ) && $cart_item['tf_hotel_data']['room'] > 0 ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Number of Room Booked', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['room'],
		);
	}

	if ( isset( $cart_item['tf_hotel_data']['adult'] ) && $cart_item['tf_hotel_data']['adult'] >= 1 ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Adult Number', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['adult'],
		);
	}

	if ( isset( $cart_item['tf_hotel_data']['child'] ) && $cart_item['tf_hotel_data']['child'] >= 1 ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Child Number', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['child'],
		);
	}
	//Add children ages data to the cart item
	if ( isset( $cart_item['tf_hotel_data']['children_ages'] ) && $cart_item['tf_hotel_data']['children_ages'] != '' ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Children Ages', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['children_ages'],
		);
	}

	if ( isset( $cart_item['tf_hotel_data']['check_in'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Check-in', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['check_in'],
		);
	}

	if ( isset( $cart_item['tf_hotel_data']['check_out'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Check-out', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['check_out'],
		);
	}

	// airport service type

	if ( ! empty( $cart_item['tf_hotel_data']['air_serivice_avail'] ) && 1 == $cart_item['tf_hotel_data']['air_serivice_avail'] && ! empty( $cart_item['tf_hotel_data']['air_serivicetype'] ) && "pickup" == $cart_item['tf_hotel_data']['air_serivicetype'] ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Airport Service', 'tourfic' ),
			'value' => esc_html__( 'Airport Pickup', 'tourfic' ),
		);
	}
	if ( ! empty( $cart_item['tf_hotel_data']['air_serivice_avail'] ) && 1 == $cart_item['tf_hotel_data']['air_serivice_avail'] && ! empty( $cart_item['tf_hotel_data']['air_serivicetype'] ) && "dropoff" == $cart_item['tf_hotel_data']['air_serivicetype'] ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Airport Service', 'tourfic' ),
			'value' => esc_html__( 'Airport Dropoff', 'tourfic' ),
		);
	}
	if ( ! empty( $cart_item['tf_hotel_data']['air_serivice_avail'] ) && 1 == $cart_item['tf_hotel_data']['air_serivice_avail'] && ! empty( $cart_item['tf_hotel_data']['air_serivicetype'] ) && "both" == $cart_item['tf_hotel_data']['air_serivicetype'] ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Airport Service', 'tourfic' ),
			'value' => esc_html__( 'Airport Pickup & Dropoff', 'tourfic' ),
		);
	}

	// airport price type

	if ( ! empty( $cart_item['tf_hotel_data']['air_serivice_avail'] ) && ! empty( $cart_item['tf_hotel_data']['air_service_info'] ) && 1 == $cart_item['tf_hotel_data']['air_serivice_avail'] && ! empty( $cart_item['tf_hotel_data']['air_serivicetype'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Airport Service Fee', 'tourfic' ),
			'value' => $cart_item['tf_hotel_data']['air_service_info'],
		);
	}

	if ( isset( $cart_item['tf_hotel_data']['due'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Due', 'tourfic' ),
			'value' => wp_strip_all_tags( wc_price( $cart_item['tf_hotel_data']['due'] ) ),
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
	$order_type           = ! empty( $values['tf_hotel_data']['order_type'] ) ? $values['tf_hotel_data']['order_type'] : '';
	$post_author          = ! empty( $values['tf_hotel_data']['post_author'] ) ? $values['tf_hotel_data']['post_author'] : '';
	$post_id              = ! empty( $values['tf_hotel_data']['post_id'] ) ? $values['tf_hotel_data']['post_id'] : '';
	$unique_id            = ! empty( $values['tf_hotel_data']['unique_id'] ) ? $values['tf_hotel_data']['unique_id'] : '';
	$room_name            = ! empty( $values['tf_hotel_data']['room_name'] ) ? $values['tf_hotel_data']['room_name'] : '';
	$option               = ! empty( $values['tf_hotel_data']['option'] ) ? $values['tf_hotel_data']['option'] : '';
	$room_selected        = ! empty( $values['tf_hotel_data']['room'] ) ? $values['tf_hotel_data']['room'] : '';
	$adult                = ! empty( $values['tf_hotel_data']['adult'] ) ? $values['tf_hotel_data']['adult'] : '';
	$child                = ! empty( $values['tf_hotel_data']['child'] ) ? $values['tf_hotel_data']['child'] : '';
	$children_ages        = ! empty( $values['tf_hotel_data']['children_ages'] ) ? $values['tf_hotel_data']['children_ages'] : '';
	$check_in             = ! empty( $values['tf_hotel_data']['check_in'] ) ? $values['tf_hotel_data']['check_in'] : '';
	$check_out            = ! empty( $values['tf_hotel_data']['check_out'] ) ? $values['tf_hotel_data']['check_out'] : '';
	$due                  = ! empty( $values['tf_hotel_data']['due'] ) ? $values['tf_hotel_data']['due'] : '';
	$airport_service_type = ! empty( $values['tf_hotel_data']['air_serivicetype'] ) ? $values['tf_hotel_data']['air_serivicetype'] : null;
	$airport_fees         = ! empty( $values['tf_hotel_data']['air_service_info'] ) ? $values['tf_hotel_data']['air_service_info'] : null;

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

	if ( $option ) {
		$item->update_meta_data( 'option', $option );
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
		$item->update_meta_data( 'Airport Service', esc_html__( 'Airport Pickup', 'tourfic' ) );
	}
	if ( ! empty( $airport_service_type ) && $airport_service_type === 'dropoff' ) {
		$item->update_meta_data( 'Airport Service', esc_html__( 'Airport Dropoff', 'tourfic' ) );
	}
	if ( ! empty( $airport_service_type ) && $airport_service_type === 'both' ) {
		$item->update_meta_data( 'Airport Service', esc_html__( 'Airport Pickup & Dropoff', 'tourfic' ) );
	}
	if ( ! empty( $airport_fees ) ) {
		$item->update_meta_data( 'Airport Service Fee', $values['tf_hotel_data']['air_service_info'] );
	}

	if ( ! empty( $due ) ) {
		$item->update_meta_data( 'due', wp_strip_all_tags( wc_price( $due ) ) );
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

	$tf_integration_order_data   = array(
		'order_id' => $order_id
	);
	$tf_integration_order_status = [];
	# Get and Loop Over Order Line Items
	foreach ( $order->get_items() as $item_id => $item ) {

		$order_type = $item->get_meta( '_order_type', true );
		if ( "hotel" == $order_type ) {
			$post_id   = $item->get_meta( '_post_id', true );
			$unique_id = $item->get_meta( '_unique_id', true );
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

		// Hotel Item Data Insert
		if ( "hotel" == $order_type ) {

			//Tax Calculation
			$tax_labels = array();
			if ( ! empty( $meta['is_taxable'] ) ) {
				$single_price     = $item->get_subtotal();
				$finding_location = array(
					'country'   => ! empty( $order->get_billing_country() ) ? $order->get_billing_country() : '',
					'state'     => ! empty( $order->get_billing_state() ) ? $order->get_billing_state() : '',
					'postcode'  => ! empty( $order->get_billing_postcode() ) ? $order->get_billing_postcode() : '',
					'city'      => ! empty( $order->get_billing_city() ) ? $order->get_billing_city() : '',
					'tax_class' => ! empty( $meta['taxable_class'] ) && "standard" != $meta['taxable_class'] ? $meta['taxable_class'] : ''
				);

				$tax_rate = WC_Tax::find_rates( $finding_location );
				if ( ! empty( $tax_rate ) ) {
					foreach ( $tax_rate as $rate ) {
						$tf_vat        = (float) $single_price * $rate['rate'] / 100;
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
				'billing_last_name'  => $order->get_billing_last_name(),
				'billing_company'    => $order->get_billing_company(),
				'billing_address_1'  => $order->get_billing_address_1(),
				'billing_address_2'  => $order->get_billing_address_2(),
				'billing_city'       => $order->get_billing_city(),
				'billing_state'      => $order->get_billing_state(),
				'billing_postcode'   => $order->get_billing_postcode(),
				'billing_country'    => $order->get_billing_country(),
				'billing_email'      => $order->get_billing_email(),
				'billing_phone'      => $order->get_billing_phone()
			];

			$shippinginfo = [
				'shipping_first_name' => $order->get_shipping_first_name(),
				'shipping_last_name'  => $order->get_shipping_last_name(),
				'shipping_company'    => $order->get_shipping_company(),
				'shipping_address_1'  => $order->get_shipping_address_1(),
				'shipping_address_2'  => $order->get_shipping_address_2(),
				'shipping_city'       => $order->get_shipping_city(),
				'shipping_state'      => $order->get_shipping_state(),
				'shipping_postcode'   => $order->get_shipping_postcode(),
				'shipping_country'    => $order->get_shipping_country(),
				'shipping_phone'      => $order->get_shipping_phone()
			];

			$unique_id            = $item->get_meta( '_unique_id', true ); // Unique id of rooms
			$room_selected        = $item->get_meta( 'number_room_booked', true );
			$check_in             = $item->get_meta( 'check_in', true );
			$check_out            = $item->get_meta( 'check_out', true );
			$price                = $item->get_subtotal();
			$due                  = $item->get_meta( 'due', true );
			$room_name            = $item->get_meta( 'room_name', true );
			$option               = $item->get_meta( 'option', true );
			$adult                = $item->get_meta( 'adult', true );
			$child                = $item->get_meta( 'child', true );
			$children_ages        = $item->get_meta( 'Children Ages', true );
			$airport_service_type = $item->get_meta( 'Airport Service', true );
			$airport_service_fee  = $item->get_meta( 'Airport Service Fee', true );

			$iteminfo = [
				'room'                 => $room_selected,
				'room_unique_id'       => $unique_id,
				'check_in'             => $check_in,
				'check_out'            => $check_out,
				'room_name'            => $room_name,
				'option'               => $option,
				'adult'                => $adult,
				'child'                => $child,
				'children_ages'        => $children_ages,
				'airport_service_type' => $airport_service_type,
				'airport_service_fee'  => $airport_service_fee,
				'total_price'          => $price,
				'due_price'            => $due,
				'tax_info'             => wp_json_encode( $fee_sums )
			];

			$tf_integration_order_data[] = [
				'room'                 => $room_selected,
				'check_in'             => $check_in,
				'check_out'            => $check_out,
				'room_name'            => $room_name,
				'option'               => $option,
				'adult'                => $adult,
				'child'                => $child,
				'children_ages'        => $children_ages,
				'airport_service_type' => $airport_service_type,
				'airport_service_fee'  => $airport_service_fee,
				'total_price'          => $price,
				'due_price'            => $due,
				'customer_id'          => $order->get_customer_id(),
				'payment_method'       => $order->get_payment_method(),
				'order_status'         => $order->get_status(),
				'order_date'           => gmdate( 'Y-m-d H:i:s' )
			];

			$tf_integration_order_status = [
				'customer_id'    => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status'   => $order->get_status(),
				'order_date'     => gmdate( 'Y-m-d H:i:s' )
			];

			$iteminfo_keys = array_keys( $iteminfo );
			$iteminfo_keys = array_map( 'sanitize_key', $iteminfo_keys );

			$iteminfo_values = array_values( $iteminfo );
			$iteminfo_values = array_map( 'sanitize_text_field', $iteminfo_values );

			$iteminfo = array_combine( $iteminfo_keys, $iteminfo_values );

			global $wpdb;
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}tf_order_data
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
						wp_json_encode( $billinginfo ),
						wp_json_encode( $shippinginfo ),
						wp_json_encode( $iteminfo ),
						$order->get_customer_id(),
						$order->get_payment_method(),
						$order->get_status(),
						gmdate( 'Y-m-d H:i:s' )
					)
				)
			);
		}

	}

	/**
	 * New Order Pabbly Integration
	 * @author Jahid
	 */
	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $tf_integration_order_status ) ) {
		do_action( 'tf_new_order_pabbly_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status );
		do_action( 'tf_new_order_zapier_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status );
	}
}

add_action( 'woocommerce_checkout_order_processed', 'tf_add_order_id_room_checkout_order_processed', 10, 4 );

/**
 * Add order id to the hotel room meta field
 * runs during WooCommerce checkout process for block checkout
 *
 * @param $order
 *
 * @return void
 * @since 2.11.10
 * @author Foysal
 */
function tf_add_order_id_room_checkout_order_processed_block_checkout( $order ) {

	$order_id                    = $order->get_id();
	$tf_integration_order_data   = array(
		'order_id' => $order_id
	);
	$tf_integration_order_status = [];
	# Get and Loop Over Order Line Items
	foreach ( $order->get_items() as $item_id => $item ) {

		$order_type = $item->get_meta( '_order_type', true );
		if ( "hotel" == $order_type ) {
			$post_id   = $item->get_meta( '_post_id', true );
			$unique_id = $item->get_meta( '_unique_id', true );
			$meta      = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$rooms     = Room::get_hotel_rooms( $post_id );

			# Get and Loop Over Room Meta
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

		// Hotel Item Data Insert
		if ( "hotel" == $order_type ) {

			//Tax Calculation
			$tax_labels = array();
			if ( ! empty( $meta['is_taxable'] ) ) {
				$single_price     = $item->get_subtotal();
				$finding_location = array(
					'country'   => ! empty( $order->get_billing_country() ) ? $order->get_billing_country() : '',
					'state'     => ! empty( $order->get_billing_state() ) ? $order->get_billing_state() : '',
					'postcode'  => ! empty( $order->get_billing_postcode() ) ? $order->get_billing_postcode() : '',
					'city'      => ! empty( $order->get_billing_city() ) ? $order->get_billing_city() : '',
					'tax_class' => ! empty( $meta['taxable_class'] ) && "standard" != $meta['taxable_class'] ? $meta['taxable_class'] : ''
				);

				$tax_rate = WC_Tax::find_rates( $finding_location );
				if ( ! empty( $tax_rate ) ) {
					foreach ( $tax_rate as $rate ) {
						$tf_vat        = (float) $single_price * $rate['rate'] / 100;
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
				'billing_last_name'  => $order->get_billing_last_name(),
				'billing_company'    => $order->get_billing_company(),
				'billing_address_1'  => $order->get_billing_address_1(),
				'billing_address_2'  => $order->get_billing_address_2(),
				'billing_city'       => $order->get_billing_city(),
				'billing_state'      => $order->get_billing_state(),
				'billing_postcode'   => $order->get_billing_postcode(),
				'billing_country'    => $order->get_billing_country(),
				'billing_email'      => $order->get_billing_email(),
				'billing_phone'      => $order->get_billing_phone()
			];

			$shippinginfo = [
				'shipping_first_name' => $order->get_shipping_first_name(),
				'shipping_last_name'  => $order->get_shipping_last_name(),
				'shipping_company'    => $order->get_shipping_company(),
				'shipping_address_1'  => $order->get_shipping_address_1(),
				'shipping_address_2'  => $order->get_shipping_address_2(),
				'shipping_city'       => $order->get_shipping_city(),
				'shipping_state'      => $order->get_shipping_state(),
				'shipping_postcode'   => $order->get_shipping_postcode(),
				'shipping_country'    => $order->get_shipping_country(),
				'shipping_phone'      => $order->get_shipping_phone()
			];

			$unique_id            = $item->get_meta( '_unique_id', true ); // Unique id of rooms
			$room_selected        = $item->get_meta( 'number_room_booked', true );
			$check_in             = $item->get_meta( 'check_in', true );
			$check_out            = $item->get_meta( 'check_out', true );
			$price                = $item->get_subtotal();
			$due                  = $item->get_meta( 'due', true );
			$room_name            = $item->get_meta( 'room_name', true );
			$option               = $item->get_meta( 'option', true );
			$adult                = $item->get_meta( 'adult', true );
			$child                = $item->get_meta( 'child', true );
			$children_ages        = $item->get_meta( 'Children Ages', true );
			$airport_service_type = $item->get_meta( 'Airport Service', true );
			$airport_service_fee  = $item->get_meta( 'Airport Service Fee', true );

			$iteminfo = [
				'room'                 => $room_selected,
				'room_unique_id'       => $unique_id,
				'check_in'             => $check_in,
				'check_out'            => $check_out,
				'room_name'            => $room_name,
				'option'               => $option,
				'adult'                => $adult,
				'child'                => $child,
				'children_ages'        => $children_ages,
				'airport_service_type' => $airport_service_type,
				'airport_service_fee'  => $airport_service_fee,
				'total_price'          => $price,
				'due_price'            => $due,
				'tax_info'             => wp_json_encode( $fee_sums )
			];

			$tf_integration_order_data[] = [
				'room'                 => $room_selected,
				'check_in'             => $check_in,
				'check_out'            => $check_out,
				'room_name'            => $room_name,
				'option'               => $option,
				'adult'                => $adult,
				'child'                => $child,
				'children_ages'        => $children_ages,
				'airport_service_type' => $airport_service_type,
				'airport_service_fee'  => $airport_service_fee,
				'total_price'          => $price,
				'due_price'            => $due,
				'customer_id'          => $order->get_customer_id(),
				'payment_method'       => $order->get_payment_method(),
				'order_status'         => $order->get_status(),
				'order_date'           => gmdate( 'Y-m-d H:i:s' )
			];

			$tf_integration_order_status = [
				'customer_id'    => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status'   => $order->get_status(),
				'order_date'     => gmdate( 'Y-m-d H:i:s' )
			];

			$iteminfo_keys = array_keys( $iteminfo );
			$iteminfo_keys = array_map( 'sanitize_key', $iteminfo_keys );

			$iteminfo_values = array_values( $iteminfo );
			$iteminfo_values = array_map( 'sanitize_text_field', $iteminfo_values );

			$iteminfo = array_combine( $iteminfo_keys, $iteminfo_values );

			global $wpdb;
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}tf_order_data
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
						wp_json_encode( $billinginfo ),
						wp_json_encode( $shippinginfo ),
						wp_json_encode( $iteminfo ),
						$order->get_customer_id(),
						$order->get_payment_method(),
						$order->get_status(),
						gmdate( 'Y-m-d H:i:s' )
					)
				)
			);
		}

	}

	/**
	 * New Order Pabbly Integration
	 * @author Jahid
	 */
	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $tf_integration_order_status ) ) {
		do_action( 'tf_new_order_pabbly_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status );
		do_action( 'tf_new_order_zapier_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status );
	}
}

add_action( 'woocommerce_store_api_checkout_order_processed', 'tf_add_order_id_room_checkout_order_processed_block_checkout' );


/*
* Admin order status change
* @author Jahid
*/

add_action( 'woocommerce_order_status_changed', 'tf_order_status_changed', 10, 4 );

function tf_order_status_changed( $order_id, $old_status, $new_status, $order ) {
	global $wpdb;
	$tf_order_checked = $wpdb->query( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_order_data WHERE order_id=%s", $order_id ) );
	if ( ! empty( $tf_order_checked ) ) {
		$wpdb->query(
			$wpdb->prepare( "UPDATE {$wpdb->prefix}tf_order_data SET ostatus=%s WHERE order_id=%s", $new_status, $order_id )
		);
	}

	$tf_integration_order_data   = array(
		'order_id' => $order_id
	);
	$tf_integration_order_status = [];
	# Get and Loop Over Order Line Items
	foreach ( $order->get_items() as $item_id => $item ) {

		$order_type = $item->get_meta( '_order_type', true );

		//Order Data Insert
		$billinginfo = [
			'billing_first_name' => $order->get_billing_first_name(),
			'billing_last_name'  => $order->get_billing_last_name(),
			'billing_company'    => $order->get_billing_company(),
			'billing_address_1'  => $order->get_billing_address_1(),
			'billing_address_2'  => $order->get_billing_address_2(),
			'billing_city'       => $order->get_billing_city(),
			'billing_state'      => $order->get_billing_state(),
			'billing_postcode'   => $order->get_billing_postcode(),
			'billing_country'    => $order->get_billing_country(),
			'billing_email'      => $order->get_billing_email(),
			'billing_phone'      => $order->get_billing_phone()
		];

		$shippinginfo = [
			'shipping_first_name' => $order->get_shipping_first_name(),
			'shipping_last_name'  => $order->get_shipping_last_name(),
			'shipping_company'    => $order->get_shipping_company(),
			'shipping_address_1'  => $order->get_shipping_address_1(),
			'shipping_address_2'  => $order->get_shipping_address_2(),
			'shipping_city'       => $order->get_shipping_city(),
			'shipping_state'      => $order->get_shipping_state(),
			'shipping_postcode'   => $order->get_shipping_postcode(),
			'shipping_country'    => $order->get_shipping_country(),
			'shipping_phone'      => $order->get_shipping_phone()
		];

		// Order Type hotel/tour

		// Hotel Item Data Insert
		if ( "hotel" == $order_type ) {
			$unique_id            = $item->get_meta( '_unique_id', true ); // Unique id of rooms
			$room_selected        = $item->get_meta( 'number_room_booked', true );
			$check_in             = $item->get_meta( 'check_in', true );
			$check_out            = $item->get_meta( 'check_out', true );
			$price                = $item->get_subtotal();
			$due                  = $item->get_meta( 'due', true );
			$room_name            = $item->get_meta( 'room_name', true );
			$option               = $item->get_meta( 'option', true );
			$adult                = $item->get_meta( 'adult', true );
			$child                = $item->get_meta( 'child', true );
			$children_ages        = $item->get_meta( 'Children Ages', true );
			$airport_service_type = $item->get_meta( 'Airport Service', true );
			$airport_service_fee  = $item->get_meta( 'Airport Service Fee', true );

			$tf_integration_order_data[] = [
				'room'                 => $room_selected,
				'room_unique_id'       => $unique_id,
				'check_in'             => $check_in,
				'check_out'            => $check_out,
				'room_name'            => $room_name,
				'option'               => $option,
				'adult'                => $adult,
				'child'                => $child,
				'children_ages'        => $children_ages,
				'airport_service_type' => $airport_service_type,
				'airport_service_fee'  => $airport_service_fee,
				'total_price'          => $price,
				'due_price'            => $due,
				'customer_id'          => $order->get_customer_id(),
				'payment_method'       => $order->get_payment_method(),
				'order_status'         => $order->get_status(),
				'order_date'           => gmdate( 'Y-m-d H:i:s' )
			];

			$tf_integration_order_status = [
				'customer_id'    => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status'   => $order->get_status(),
				'order_date'     => gmdate( 'Y-m-d H:i:s' )
			];

		}

		// Tour Item Data Insert
		if ( "tour" == $order_type ) {
			$tour_date  = $item->get_meta( 'Tour Date', true );
			$tour_time  = $item->get_meta( 'Tour Time', true );
			$price      = $item->get_subtotal();
			$due        = $item->get_meta( 'Due', true );
			$tour_extra = $item->get_meta( 'Tour Extra', true );
			$adult      = $item->get_meta( 'Adults', true );
			$child      = $item->get_meta( 'Children', true );
			$infants    = $item->get_meta( 'Infants', true );

			if ( $tour_date ) {
				if ( str_contains( $tour_date, ' - ' ) ) {
					list( $tour_in, $tour_out ) = explode( ' - ', $tour_date );
				} else {
					$tour_in = $tour_date;
				}
			}

			$tf_integration_order_data[] = [
				'tour_date'   => $tour_date,
				'tour_time'   => $tour_time,
				'tour_extra'  => $tour_extra,
				'adult'       => $adult,
				'child'       => $child,
				'infants'     => $infants,
				'total_price' => $price,
				'due_price'   => $due,
			];

			$tf_integration_order_status = [
				'customer_id'    => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status'   => $order->get_status(),
				'order_date'     => gmdate( 'Y-m-d H:i:s' )
			];

		}
	}

	/**
	 * New Order Pabbly Integration
	 * @author Jahid
	 */

	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		do_action( 'tf_new_order_pabbly_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status );
		do_action( 'tf_new_order_zapier_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status );
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
		'billing_last_name'  => $items['_billing_last_name'],
		'billing_company'    => $items['_billing_company'],
		'billing_address_1'  => $items['_billing_address_1'],
		'billing_address_2'  => $items['_billing_address_2'],
		'billing_city'       => $items['_billing_city'],
		'billing_state'      => $items['_billing_state'],
		'billing_postcode'   => $items['_billing_postcode'],
		'billing_country'    => $items['_billing_country'],
		'billing_email'      => $items['_billing_email'],
		'billing_phone'      => $items['_billing_phone']
	];

	$shippinginfo      = [
		'shipping_first_name' => $items['_shipping_first_name'],
		'shipping_last_name'  => $items['_shipping_last_name'],
		'shipping_company'    => $items['_shipping_company'],
		'shipping_address_1'  => $items['_shipping_address_1'],
		'shipping_address_2'  => $items['_shipping_address_2'],
		'shipping_city'       => $items['_shipping_city'],
		'shipping_state'      => $items['_shipping_state'],
		'shipping_postcode'   => $items['_shipping_postcode'],
		'shipping_country'    => $items['_shipping_country'],
		'shipping_phone'      => $items['_shipping_phone']
	];
	$tf_payment_method = $items['_payment_method'];
	global $wpdb;
	$tf_order_checked = $wpdb->query( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_order_data WHERE order_id=%s", $order_id ) );
	if ( ! empty( $tf_order_checked ) ) {
		$wpdb->query(
			$wpdb->prepare( "UPDATE {$wpdb->prefix}tf_order_data SET billing_details=%s, shipping_details=%s, payment_method=%s WHERE order_id=%s", wp_json_encode( $billinginfo ), wp_json_encode( $shippinginfo ), $tf_payment_method, $order_id )
		);
	}
}