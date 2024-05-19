<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Hotel availability calendar update
 * @author Foysal
 */
if ( ! function_exists( 'tf_add_hotel_availability' ) ) {
	function tf_add_hotel_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		$date_format         = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";
		$hotel_id            = isset( $_POST['hotel_id'] ) && ! empty( $_POST['hotel_id'] ) ? sanitize_text_field( $_POST['hotel_id'] ) : '';
		$new_post            = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? $_POST['new_post'] : '';
		$room_index          = isset( $_POST['room_index'] ) ? intval( $_POST['room_index'] ) : '';
		$check_in            = isset( $_POST['tf_room_check_in'] ) && ! empty( $_POST['tf_room_check_in'] ) ? sanitize_text_field( $_POST['tf_room_check_in'] ) : '';
		$check_out           = isset( $_POST['tf_room_check_out'] ) && ! empty( $_POST['tf_room_check_out'] ) ? sanitize_text_field( $_POST['tf_room_check_out'] ) : '';
		$status              = isset( $_POST['tf_room_status'] ) && ! empty( $_POST['tf_room_status'] ) ? sanitize_text_field( $_POST['tf_room_status'] ) : '';
		$price_by            = isset( $_POST['price_by'] ) && ! empty( $_POST['price_by'] ) ? sanitize_text_field( $_POST['price_by'] ) : '';
		$tf_room_price       = isset( $_POST['tf_room_price'] ) && ! empty( $_POST['tf_room_price'] ) ? sanitize_text_field( $_POST['tf_room_price'] ) : '';
		$tf_room_adult_price = isset( $_POST['tf_room_adult_price'] ) && ! empty( $_POST['tf_room_adult_price'] ) ? sanitize_text_field( $_POST['tf_room_adult_price'] ) : '';
		$tf_room_child_price = isset( $_POST['tf_room_child_price'] ) && ! empty( $_POST['tf_room_child_price'] ) ? sanitize_text_field( $_POST['tf_room_child_price'] ) : '';
		$avail_date          = isset( $_POST['avail_date'] ) && ! empty( $_POST['avail_date'] ) ? sanitize_text_field( $_POST['avail_date'] ) : '';

		if ( empty( $check_in ) || empty( $check_out ) ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Please select check in and check out date.', 'tourfic' )
			] );
		}

		if ( $date_format == 'Y.m.d' || $date_format == 'd.m.Y' ) {
			$check_in  = gmdate( "Y-m-d", strtotime( str_replace( ".", "-", $check_in ) ) );
			$check_out = gmdate( "Y-m-d", strtotime( str_replace( ".", "-", $check_out ) ) );
		}
		if ( $date_format == 'd/m/Y' ) {
			$check_in  = gmdate( "Y-m-d", strtotime( str_replace( "/", "-", $check_in ) ) );
			$check_out = gmdate( "Y-m-d", strtotime( str_replace( "/", "-", $check_out ) ) );
		}

		$check_in  = strtotime( $check_in );
		$check_out = strtotime( $check_out );
		if ( $check_in > $check_out ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Check in date must be less than check out date.', 'tourfic' )
			] );
		}

		$room_avail_data = [];
		for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
			$tf_room_date                     = gmdate( 'Y/m/d', $i );
			$tf_room_data                     = [
				'check_in'    => $tf_room_date,
				'check_out'   => $tf_room_date,
				'price_by'    => $price_by,
				'price'       => $tf_room_price,
				'adult_price' => $tf_room_adult_price,
				'child_price' => $tf_room_child_price,
				'status'      => $status
			];
			$room_avail_data[ $tf_room_date ] = $tf_room_data;
		}

		$hotel_avail_data = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
		if ( $new_post != 'true' ) {
			$avail_date = json_decode( $hotel_avail_data['room'][ $room_index ]['avail_date'], true );
			if ( isset( $avail_date ) && ! empty( $avail_date ) ) {
				$room_avail_data = array_merge( $avail_date, $room_avail_data );
			}
			$hotel_avail_data['room'][ $room_index ]['avail_date'] = wp_json_encode( $room_avail_data );
			update_post_meta( $hotel_id, 'tf_hotels_opt', $hotel_avail_data );
		} else {
			$avail_date = json_decode( stripslashes( $avail_date ), true );
			if ( isset( $avail_date ) && ! empty( $avail_date ) ) {
				$room_avail_data = array_merge( $avail_date, $room_avail_data );
			}
		}

		wp_send_json_success( [
			'status'     => true,
			'message'    => __( 'Availability updated successfully.', 'tourfic' ),
			'avail_date' => wp_json_encode( $room_avail_data ),
		] );

		die();
	}

	add_action( 'wp_ajax_tf_add_hotel_availability', 'tf_add_hotel_availability' );
}

/*
 * Get hotel availability calendar
 * @author Foysal
 */
if ( ! function_exists( 'tf_get_hotel_availability' ) ) {
	function tf_get_hotel_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		$new_post   = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? sanitize_text_field( $_POST['new_post'] ) : '';
		$hotel_id   = isset( $_POST['hotel_id'] ) && ! empty( $_POST['hotel_id'] ) ? sanitize_text_field( $_POST['hotel_id'] ) : '';
		$room_index = isset( $_POST['room_index'] ) ? intval( $_POST['room_index'] ) : '';
		$avail_date = isset( $_POST['avail_date'] ) && ! empty( $_POST['avail_date'] ) ? sanitize_text_field( $_POST['avail_date'] ) : '';

		if ( $new_post != 'true' ) {
			$hotel_avail_data = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
			$room_avail_data  = isset( $hotel_avail_data['room'][ $room_index ]['avail_date'] ) && ! empty( $hotel_avail_data['room'][ $room_index ]['avail_date'] ) ? json_decode( $hotel_avail_data['room'][ $room_index ]['avail_date'], true ) : [];
		} else {
			$room_avail_data = json_decode( stripslashes( $avail_date ), true );
		}

		if ( ! empty( $room_avail_data ) && is_array( $room_avail_data ) ) {
			$room_avail_data = array_values( $room_avail_data );
			$room_avail_data = array_map( function ( $item ) {
				$item['start'] = gmdate( 'Y-m-d', strtotime( $item['check_in'] ) );
				$item['title'] = $item['price_by'] == '1' ? __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) : __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );
//				$item['title'] = __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) . '<br>' . __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );

				if ( $item['status'] == 'unavailable' ) {
					$item['display'] = 'background';
					$item['color']   = '#003c79';
				}

				return $item;
			}, $room_avail_data );
		} else {
			$room_avail_data = [];
		}

		echo wp_json_encode( $room_avail_data );
		die();
	}

	add_action( 'wp_ajax_tf_get_hotel_availability', 'tf_get_hotel_availability' );
}

/*
 * Update hotel room avail_date price based on pricing type
 * @auther Foysal
 */
if ( ! function_exists( 'tf_update_room_avail_date_price' ) ) {
	function tf_update_room_avail_date_price( $post_id, $post ) {
		if ( $post->post_type == 'tf_hotel' ) {
			$meta  = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
			if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
				$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $rooms );
				$rooms                = unserialize( $tf_hotel_rooms_value );
			}

			if ( ! empty( $rooms ) ) {
				foreach ( $rooms as $roomIndex => $room ) {
					$pricing_by   = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
					$price        = ! empty( $room['price'] ) ? $room['price'] : '';
					$adult_price  = ! empty( $room['adult_price'] ) ? $room['adult_price'] : '';
					$child_price  = ! empty( $room['child_price'] ) ? $room['child_price'] : '';
					$avil_by_date = ! empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : '';

					if ( $avil_by_date === '1' && ! empty( $room['avail_date'] ) ) {
						$hotel_avail_data = json_decode( $room['avail_date'], true );

						if ( isset( $hotel_avail_data ) && ! empty( $hotel_avail_data ) ) {

							$hotel_avail_data = array_map( function ( $item ) use ( $pricing_by, $price, $adult_price, $child_price ) {

								if ( $pricing_by == '1' ) {
									$item['price'] = ! isset( $item['price'] ) ? $price : $item['price'];
								} else {
									$item['adult_price'] = ! isset( $item['adult_price'] ) ? $adult_price : $item['adult_price'];
									$item['child_price'] = ! isset( $item['child_price'] ) ? $child_price : $item['child_price'];
								}
								$item['price_by'] = $pricing_by;

								return $item;
							}, $hotel_avail_data );
						}

						$meta['room'][ $roomIndex ]['avail_date'] = wp_json_encode( $hotel_avail_data );
					} elseif ( $avil_by_date === '1' && empty( $room['avail_date'] ) ) {
						//add next 5 years availability
						$hotel_avail_data = [];
						for ( $i = 0; $i <= 1825; $i ++ ) {
							$tf_room_date                      = gmdate( 'Y/m/d', strtotime( "+$i day" ) );
							$tf_room_data                      = [
								'check_in'    => $tf_room_date,
								'check_out'   => $tf_room_date,
								'price_by'    => $pricing_by,
								'price'       => $price,
								'adult_price' => $adult_price,
								'child_price' => $child_price,
								'status'      => 'available'
							];
							$hotel_avail_data[ $tf_room_date ] = $tf_room_data;
						}

						$meta['room'][ $roomIndex ]['avail_date'] = wp_json_encode( $hotel_avail_data );
					}
				}
			}
			update_post_meta( $post_id, 'tf_hotels_opt', $meta );
		}
	}

	add_action( 'save_post', 'tf_update_room_avail_date_price', 9999, 2 );
}

/*
 * Apartment availability calendar update
 * @auther Foysal
 */
if ( ! function_exists( 'tf_add_apartment_availability' ) ) {
	function tf_add_apartment_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		$date_format         = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";
		$apartment_id        = isset( $_POST['apartment_id'] ) && ! empty( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : '';
		$new_post            = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? $_POST['new_post'] : '';
		$check_in            = isset( $_POST['tf_apt_check_in'] ) && ! empty( $_POST['tf_apt_check_in'] ) ? sanitize_text_field( $_POST['tf_apt_check_in'] ) : '';
		$check_out           = isset( $_POST['tf_apt_check_out'] ) && ! empty( $_POST['tf_apt_check_out'] ) ? sanitize_text_field( $_POST['tf_apt_check_out'] ) : '';
		$status              = isset( $_POST['tf_apt_status'] ) && ! empty( $_POST['tf_apt_status'] ) ? sanitize_text_field( $_POST['tf_apt_status'] ) : '';
		$pricing_type        = isset( $_POST['pricing_type'] ) && ! empty( $_POST['pricing_type'] ) ? sanitize_text_field( $_POST['pricing_type'] ) : '';
		$tf_apt_price        = isset( $_POST['tf_apt_price'] ) && ! empty( $_POST['tf_apt_price'] ) ? sanitize_text_field( $_POST['tf_apt_price'] ) : '';
		$tf_apt_adult_price  = isset( $_POST['tf_apt_adult_price'] ) && ! empty( $_POST['tf_apt_adult_price'] ) ? sanitize_text_field( $_POST['tf_apt_adult_price'] ) : '';
		$tf_apt_child_price  = isset( $_POST['tf_apt_child_price'] ) && ! empty( $_POST['tf_apt_child_price'] ) ? sanitize_text_field( $_POST['tf_apt_child_price'] ) : '';
		$tf_apt_infant_price = isset( $_POST['tf_apt_infant_price'] ) && ! empty( $_POST['tf_apt_infant_price'] ) ? sanitize_text_field( $_POST['tf_apt_infant_price'] ) : '';
		$apt_availability    = isset( $_POST['apt_availability'] ) && ! empty( $_POST['apt_availability'] ) ? sanitize_text_field( $_POST['apt_availability'] ) : '';

		if ( empty( $check_in ) || empty( $check_out ) ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Please select check in and check out date.', 'tourfic' )
			] );
		}

		if ( $date_format == 'Y.m.d' || $date_format == 'd.m.Y' ) {
			$check_in  = gmdate( "Y-m-d", strtotime( str_replace( ".", "-", $check_in ) ) );
			$check_out = gmdate( "Y-m-d", strtotime( str_replace( ".", "-", $check_out ) ) );
		}
		if ( $date_format == 'd/m/Y' ) {
			$check_in  = gmdate( "Y-m-d", strtotime( str_replace( "/", "-", $check_in ) ) );
			$check_out = gmdate( "Y-m-d", strtotime( str_replace( "/", "-", $check_out ) ) );
		}

		$check_in  = strtotime( $check_in );
		$check_out = strtotime( $check_out );
		if ( $check_in > $check_out ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Check in date must be less than check out date.', 'tourfic' )
			] );
		}

		$apt_availability_data = [];
		for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
			$tf_apt_date                           = gmdate( 'Y/m/d', $i );
			$tf_apt_data                           = [
				'check_in'     => $tf_apt_date,
				'check_out'    => $tf_apt_date,
				'pricing_type' => $pricing_type,
				'price'        => $tf_apt_price,
				'adult_price'  => $tf_apt_adult_price,
				'child_price'  => $tf_apt_child_price,
				'infant_price' => $tf_apt_infant_price,
				'status'       => $status
			];
			$apt_availability_data[ $tf_apt_date ] = $tf_apt_data;
		}

		$apartment_data = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
		if ( $new_post != 'true' ) {
			$apt_availability = json_decode( $apartment_data['apt_availability'], true );
			if ( isset( $apt_availability ) && ! empty( $apt_availability ) ) {
				$apt_availability_data = array_merge( $apt_availability, $apt_availability_data );
			}
			$apartment_data['apt_availability'] = wp_json_encode( $apt_availability_data );
			update_post_meta( $apartment_id, 'tf_apartment_opt', $apartment_data );
		} else {
			$apt_availability = json_decode( stripslashes( $apt_availability ), true );
			if ( isset( $apt_availability ) && ! empty( $apt_availability ) ) {
				$apt_availability_data = array_merge( $apt_availability, $apt_availability_data );
			}
		}

		wp_send_json_success( [
			'status'           => true,
			'message'          => __( 'Availability updated successfully.', 'tourfic' ),
			'apt_availability' => wp_json_encode( $apt_availability_data ),
		] );

		die();
	}

	add_action( 'wp_ajax_tf_add_apartment_availability', 'tf_add_apartment_availability' );
}

/*
 * Get apartment availability calendar
 * @auther Foysal
 */
if ( ! function_exists( 'tf_get_apartment_availability' ) ) {
	function tf_get_apartment_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		$new_post         = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? sanitize_text_field( $_POST['new_post'] ) : '';
		$apartment_id     = isset( $_POST['apartment_id'] ) && ! empty( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : '';
		$apt_availability = isset( $_POST['apt_availability'] ) && ! empty( $_POST['apt_availability'] ) ? sanitize_text_field( $_POST['apt_availability'] ) : '';

		if ( $new_post != 'true' ) {
			$apartment_data        = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
			$apt_availability_data = isset( $apartment_data['apt_availability'] ) && ! empty( $apartment_data['apt_availability'] ) ? json_decode( $apartment_data['apt_availability'], true ) : [];
		} else {
			$apt_availability_data = json_decode( stripslashes( $apt_availability ), true );
		}

		if ( ! empty( $apt_availability_data ) && is_array( $apt_availability_data ) ) {
			$apt_availability_data = array_values( $apt_availability_data );
			$apt_availability_data = array_map( function ( $item ) {
				$item['start'] = gmdate( 'Y-m-d', strtotime( $item['check_in'] ) );
				$item['title'] = $item['pricing_type'] == 'per_night' ? __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) : __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] ) . '<br>' . __( 'Infant: ', 'tourfic' ) . wc_price( $item['infant_price'] );

				if ( $item['status'] == 'unavailable' ) {
					$item['display'] = 'background';
					$item['color']   = '#003c79';
				}

				return $item;
			}, $apt_availability_data );
		} else {
			$apt_availability_data = [];
		}

		echo wp_json_encode( $apt_availability_data );
		die();
	}

	add_action( 'wp_ajax_tf_get_apartment_availability', 'tf_get_apartment_availability' );
}

/*
 * Update apt_availability price based on pricing type
 * @auther Foysal
 */
if ( ! function_exists( 'tf_update_apt_availability_price' ) ) {
	function tf_update_apt_availability_price( $post_id, $post ) {
		if ( $post->post_type == 'tf_apartment' ) {
			$meta                = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$pricing_type        = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : '';
			$price               = ! empty( $meta['price_per_night'] ) ? $meta['price_per_night'] : '';
			$adult_price         = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : '';
			$child_price         = ! empty( $meta['child_price'] ) ? $meta['child_price'] : '';
			$infant_price        = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : '';
			$enable_availability = ! empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';

			if ( $enable_availability === '1' && ! empty( $meta['apt_availability'] ) ) {
				$apt_availability_data = json_decode( $meta['apt_availability'], true );

				if ( isset( $apt_availability_data ) && ! empty( $apt_availability_data ) ) {

					$apt_availability_data = array_map( function ( $item ) use ( $pricing_type, $price, $adult_price, $child_price, $infant_price ) {

						if ( $pricing_type == 'per_night' ) {
							$item['price'] = ! isset( $item['price'] ) ? $price : $item['price'];
						} else {
							$item['adult_price']  = ! isset( $item['adult_price'] ) ? $adult_price : $item['adult_price'];
							$item['child_price']  = ! isset( $item['child_price'] ) ? $child_price : $item['child_price'];
							$item['infant_price'] = ! isset( $item['infant_price'] ) ? $infant_price : $item['infant_price'];
						}
						$item['pricing_type'] = $pricing_type;

						return $item;
					}, $apt_availability_data );
				}

				$meta['apt_availability'] = wp_json_encode( $apt_availability_data );
				update_post_meta( $post_id, 'tf_apartment_opt', $meta );

			} elseif ( $enable_availability === '1' && empty( $meta['apt_availability'] ) ) {
				//add next 5 years availability
				$apt_availability_data = [];
				for ( $i = strtotime( gmdate( 'Y-m-d' ) ); $i <= strtotime( '+5 year', strtotime( gmdate( 'Y-m-d' ) ) ); $i = strtotime( '+1 day', $i ) ) {
					$tf_apt_date                           = gmdate( 'Y/m/d', $i );
					$tf_apt_data                           = [
						'check_in'     => $tf_apt_date,
						'check_out'    => $tf_apt_date,
						'pricing_type' => $pricing_type,
						'price'        => $price,
						'adult_price'  => $adult_price,
						'child_price'  => $child_price,
						'infant_price' => $infant_price,
						'status'       => 'available'
					];
					$apt_availability_data[ $tf_apt_date ] = $tf_apt_data;
				}

				$meta['apt_availability'] = wp_json_encode( $apt_availability_data );
				update_post_meta( $post_id, 'tf_apartment_opt', $meta );
			}
		}
	}

	add_action( 'save_post', 'tf_update_apt_availability_price', 99, 2 );
}
/*
 * Get all icons list
 * @author Foysal
 */
function get_icon_list() {
	$icons = array(
		'all'           => array(
			'label'      => __( 'All Icons', 'tourfic' ),
			'label_icon' => 'ri-grid-fill',
			'icons'      => array_merge( fontawesome_four_icons(), fontawesome_five_icons(), fontawesome_six_icons(), remix_icon() ),
		),
		'fontawesome_4' => array(
			'label'      => __( 'Font Awesome 4', 'tourfic' ),
			'label_icon' => 'fa-regular fa-font-awesome',
			'icons'      => fontawesome_four_icons(),
		),
		'fontawesome_5' => array(
			'label'      => __( 'Font Awesome 5', 'tourfic' ),
			'label_icon' => 'fa-regular fa-font-awesome',
			'icons'      => fontawesome_five_icons(),
		),
		'fontawesome_6' => array(
			'label'      => __( 'Font Awesome 6', 'tourfic' ),
			'label_icon' => 'fa-regular fa-font-awesome',
			'icons'      => fontawesome_six_icons(),
		),
		'remixicon'     => array(
			'label'      => __( 'Remix Icon', 'tourfic' ),
			'label_icon' => 'ri-remixicon-line',
			'icons'      => remix_icon(),
		),
	);

	$icons = apply_filters( 'tf_icon_list', $icons );

	return $icons;
}

/*
 * Icon infinite scroll
 * @author Foysal
 */
if ( ! function_exists( 'tf_load_more_icons' ) ) {
	add_action( 'wp_ajax_tf_load_more_icons', 'tf_load_more_icons' );
	function tf_load_more_icons() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		$start_index = isset( $_POST['start_index'] ) ? intval( $_POST['start_index'] ) : 0;
		$type        = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'all';
		$search      = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$icon_list   = get_icon_list();
		$icons       = array_slice( $icon_list[ $type ]['icons'], $start_index, 100 );

		if ( ! empty( $search ) ) {
			$icons = array_filter( $icons, function ( $icon ) use ( $search ) {
				return strpos( $icon, $search ) !== false;
			} );
		}

		$icons_html = '';
		foreach ( $icons as $key => $icon ) {
			$icons_html .= '<li data-icon="' . esc_attr( $icon ) . '">
                            <div class="tf-icon-inner">
                                <i title="' . esc_attr( $icon ) . '" class="tf-main-icon ' . esc_attr( $icon ) . '"></i>
                                <span class="check-icon">
                                    <i class="ri-check-line"></i>
                                </span>
                            </div>
                        </li>';
		}

		wp_send_json_success( $icons_html );
	}
}

/*
 * Icon search filter
 * @auther Foysal
 */
if ( ! function_exists( 'tf_icon_search' ) ) {
	add_action( 'wp_ajax_tf_icon_search', 'tf_icon_search' );
	function tf_icon_search() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		$search_text = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$type        = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'all';
		$icon_list   = get_icon_list();
		$icons       = $icon_list[ $type ]['icons'];

		$icons = array_filter( $icons, function ( $icon ) use ( $search_text ) {
			return strpos( $icon, $search_text ) !== false;
		} );

		$icons_html = '';
		foreach ( $icons as $key => $icon ) {
			$icons_html .= '<li data-icon="' . esc_attr( $icon ) . '">
                            <div class="tf-icon-inner">
                                <i title="' . esc_attr( $icon ) . '" class="tf-main-icon ' . esc_attr( $icon ) . '"></i>
                                <span class="check-icon">
                                    <i class="ri-check-line"></i>
                                </span>
                            </div>
                        </li>';
		}

		wp_send_json_success( array(
			'html'  => $icons_html,
			'count' => count( $icons )
		) );
	}
}