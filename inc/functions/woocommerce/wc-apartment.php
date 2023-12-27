<?php
defined( 'ABSPATH' ) || exit;

/**
 * Apartment booking ajax function
 * @author Foysal
 */
add_action( 'wp_ajax_tf_apartment_booking', 'tf_apartment_booking_callback' );
add_action( 'wp_ajax_nopriv_tf_apartment_booking', 'tf_apartment_booking_callback' );
function tf_apartment_booking_callback() {
	$response          = [];
	$tf_apartment_data = [];

	// Check nonce security
	if ( ! isset( $_POST['tf_apartment_nonce'] ) || ! wp_verify_nonce( $_POST['tf_apartment_nonce'], 'tf_apartment_booking' ) ) {
		return;
	}

	$post_id           = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : null;
	$adults            = isset( $_POST['adults'] ) ? intval( sanitize_text_field( $_POST['adults'] ) ) : '0';
	$children          = isset( $_POST['children'] ) ? intval( sanitize_text_field( $_POST['children'] ) ) : '0';
	$infant            = isset( $_POST['infant'] ) ? intval( sanitize_text_field( $_POST['infant'] ) ) : '0';
	$check_in_out_date = isset( $_POST['check-in-out-date'] ) ? sanitize_text_field( $_POST['check-in-out-date'] ) : '';

	$product_id          = get_post_meta( $post_id, 'product_id', true );
	$post_author         = get_post_field( 'post_author', $post_id );
	$meta                = get_post_meta( $post_id, 'tf_apartment_opt', true );
	$max_adults          = ! empty( $meta['max_adults'] ) ? $meta['max_adults'] : '';
	$max_children        = ! empty( $meta['max_children'] ) ? $meta['max_children'] : '';
	$max_infants         = ! empty( $meta['max_infants'] ) ? $meta['max_infants'] : '';
	$pricing_type        = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
	$price_per_night     = ! empty( $meta['price_per_night'] ) ? $meta['price_per_night'] : 0;
	$adult_price         = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
	$child_price         = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
	$infant_price        = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;
	$enable_availability = ! empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';
	$discount_type       = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
	$discount            = ! empty( $meta['discount'] ) ? $meta['discount'] : 0;

	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		$additional_fees = ! empty( $meta['additional_fees'] ) ? $meta['additional_fees'] : array();
	} else {
		$additional_fee = ! empty( $meta['additional_fee'] ) ? $meta['additional_fee'] : 0;
		$fee_type       = ! empty( $meta['fee_type'] ) ? $meta['fee_type'] : '';
	}

	// Booking Type
	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
		$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
		$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&infant={infant}';
		$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
	}

	# Calculate nights
	if ( ! empty( $check_in_out_date ) ) {
		$check_in_out  = explode( ' - ', $check_in_out_date );
		$check_in_stt  = strtotime( $check_in_out[0] . ' +1 day' );
		$check_in      = date( 'Y-m-d', $check_in_stt );
		$check_out_stt = strtotime( $check_in_out[1] );
		$check_out     = date( 'Y-m-d', $check_out_stt );
		$days          = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
	}

	// Check errors
	if ( empty( $check_in_out[0] ) ) {
		$response['errors'][] = __( 'Check-in date missing.', 'tourfic' );
	}
	if ( empty( $check_in_out[1] ) ) {
		$response['errors'][] = __( 'Check-out date missing.', 'tourfic' );
	}
	if ( empty( $adults ) ) {
		$response['errors'][] = __( 'Select Adult(s).', 'tourfic' );
	}
	if ( $max_adults && $adults > $max_adults ) {
		$response['errors'][] = sprintf( __( 'Maximum %s Adult(s) allowed.', 'tourfic' ), $max_adults );
	}
	if ( $max_children && $children > $max_children ) {
		$response['errors'][] = sprintf( __( 'Maximum %s Children(s) allowed.', 'tourfic' ), $max_children );
	}
	if ( $max_infants && $infant > $max_infants ) {
		$response['errors'][] = sprintf( __( 'Maximum %s Infant(s) allowed.', 'tourfic' ), $max_infants );
	}
	if ( empty( $post_id ) ) {
		$response['errors'][] = __( 'Unknown Error! Please try again.', 'tourfic' );
	}

	/**
	 * If no errors then process
	 */
	if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {

		$tf_apartment_data['tf_apartment_data']['order_type']        = 'apartment';
		$tf_apartment_data['tf_apartment_data']['post_id']           = $post_id;
		$tf_apartment_data['tf_apartment_data']['post_permalink']    = get_permalink( $post_id );
		$tf_apartment_data['tf_apartment_data']['post_author']       = $post_author;
		$tf_apartment_data['tf_apartment_data']['check_in_out_date'] = $check_in_out_date;
		$tf_apartment_data['tf_apartment_data']['adults']            = $adults;
		$tf_apartment_data['tf_apartment_data']['children']          = $children;
		$tf_apartment_data['tf_apartment_data']['infant']            = $infant;

		// Calculate price
		$total_price = 0;
		if ( $days > 0 ) {
			if ( $enable_availability === '1' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
				$apt_availability = ! empty( $meta['apt_availability'] ) ? json_decode( $meta['apt_availability'], true ) : [];

				if ( ! empty( $apt_availability ) && is_array( $apt_availability ) ) {
					foreach ( $apt_availability as $key => $single_avail ) {
						$_date_str = strtotime( $key );

						if ( $_date_str >= $check_in_stt && $_date_str <= $check_out_stt ) {
							if ( $pricing_type === 'per_night' ) {
								$total_price += $single_avail['price'];
							} else {
								$total_price += ( ( $single_avail['adult_price'] * $adults ) + ( $single_avail['child_price'] * $children ) + ( $single_avail['infant_price'] * $infant ) );
							}
						}
					}
				}

			} else {
				if ( $pricing_type == 'per_night' ) {
					$total_price = $price_per_night * $days;
				} else {
					$total_price = ( ( $adult_price * $adults ) + ( $child_price * $children ) + ( $infant_price * $infant ) ) * $days;
				}
			}

			if ( $discount_type == 'percent' ) {
				$total_price = $total_price - ( $total_price * ( $discount / 100 ) );
			} elseif ( $discount_type == 'fixed' ) {
				$total_price = $total_price - $discount;
			}

			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $total_price > 0) {
				foreach ( $additional_fees as $key => $item ) {
					if ( $item['fee_type'] == 'per_night' ) {
						$total_price += $item['additional_fee'] * $days;
					} elseif ( $item['fee_type'] == 'per_person' ) {
						$total_price += $item['additional_fee'] * ( $adults + $children + $infant );
					} else {
						$total_price += $item['additional_fee'];
					}
				}
			} else {
				if ( $fee_type == 'per_night' ) {
					$total_price += $additional_fee * $days;
				} elseif ( $fee_type == 'per_person' ) {
					$total_price += $additional_fee * ( $adults + $children + $infant );
				} else {
					$total_price += $additional_fee;
				}
			}

			$tf_apartment_data['tf_apartment_data']['pricing_type'] = $pricing_type;
			$tf_apartment_data['tf_apartment_data']['total_price']  = $total_price;
		}

		if ( $tf_booking_type == 2 && ! empty( $tf_booking_url ) ) {
			$external_search_info = array(
				'{adult}'    => $adults,
				'{child}'    => $children,
				'{infant}'   => $infant,
				'{checkin}'  => $check_in,
				'{checkout}' => $check_out,
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
			WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_apartment_data );

			$response['product_id']  = $product_id;
			$response['add_to_cart'] = 'true';
			$response['redirect_to'] = wc_get_checkout_url();
		}
	} else {
		$response['status'] = 'error';
	}

	// Json Response
	echo wp_json_encode( $response );

	die();
}

/**
 * Override WooCommerce Price
 * @author Foysal
 */
function tf_aprtment_set_order_price( $cart ) {

	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
		return;
	}

	foreach ( $cart->get_cart() as $cart_item ) {

		if ( isset( $cart_item['tf_apartment_data']['total_price'] ) ) {
			$cart_item['data']->set_price( $cart_item['tf_apartment_data']['total_price'] );
		}
	}

}

add_action( 'woocommerce_before_calculate_totals', 'tf_aprtment_set_order_price', 30, 1 );

/*
 * Display custom cart item meta data (in cart and checkout)
 * @author Foysal
 */
function tf_apartment_cart_item_custom_meta_data( $item_data, $cart_item ) {

	if ( isset( $cart_item['tf_apartment_data']['adults'] ) && $cart_item['tf_apartment_data']['adults'] >= 1 ) {
		$item_data[] = array(
			'key'   => __( 'Adults', 'tourfic' ),
			'value' => $cart_item['tf_apartment_data']['adults'],
		);
	}

	if ( isset( $cart_item['tf_apartment_data']['children'] ) && $cart_item['tf_apartment_data']['children'] >= 1 ) {
		$item_data[] = array(
			'key'   => __( 'Children', 'tourfic' ),
			'value' => $cart_item['tf_apartment_data']['children'],
		);
	}

	if ( isset( $cart_item['tf_apartment_data']['infant'] ) && $cart_item['tf_apartment_data']['infant'] >= 1 ) {
		$item_data[] = array(
			'key'   => __( 'Infant', 'tourfic' ),
			'value' => $cart_item['tf_apartment_data']['infant'],
		);
	}

	if ( isset( $cart_item['tf_apartment_data']['check_in_out_date'] ) ) {
		$item_data[] = array(
			'key'   => __( 'Check-in-out', 'tourfic' ),
			'value' => $cart_item['tf_apartment_data']['check_in_out_date'],
		);
	}

	return $item_data;

}

add_filter( 'woocommerce_get_item_data', 'tf_apartment_cart_item_custom_meta_data', 10, 2 );

/**
 * Change cart item permalink
 * @author Foysal
 */
function tf_apartment_cart_item_permalink( $permalink, $cart_item, $cart_item_key ) {

	$type = ! empty( $cart_item['tf_apartment_data']['order_type'] ) ? $cart_item['tf_apartment_data']['order_type'] : '';
	if ( is_cart() && $type == 'apartment' ) {
		$permalink = $cart_item['tf_apartment_data']['post_permalink'];
	}

	return $permalink;

}

add_filter( 'woocommerce_cart_item_permalink', 'tf_apartment_cart_item_permalink', 10, 3 );

/**
 * Show custom data in order details
 * @author Foysal
 */
function tf_apartment_custom_order_data( $item, $cart_item_key, $values, $order ) {

	// Assigning data into variables
	$order_type        = ! empty( $values['tf_apartment_data']['order_type'] ) ? $values['tf_apartment_data']['order_type'] : '';
	$post_author       = ! empty( $values['tf_apartment_data']['post_author'] ) ? $values['tf_apartment_data']['post_author'] : '';
	$post_id           = ! empty( $values['tf_apartment_data']['post_id'] ) ? $values['tf_apartment_data']['post_id'] : '';
	$adults            = ! empty( $values['tf_apartment_data']['adults'] ) ? $values['tf_apartment_data']['adults'] : '';
	$children          = ! empty( $values['tf_apartment_data']['children'] ) ? $values['tf_apartment_data']['children'] : '';
	$infant            = ! empty( $values['tf_apartment_data']['infant'] ) ? $values['tf_apartment_data']['infant'] : '';
	$check_in_out_date = ! empty( $values['tf_apartment_data']['check_in_out_date'] ) ? $values['tf_apartment_data']['check_in_out_date'] : '';

	/**
	 * Show data in order meta & email
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

	if ( $adults && $adults > 0 ) {
		$item->update_meta_data( 'adults', $adults );
	}

	if ( $children && $children > 0 ) {
		$item->update_meta_data( 'children', $children );
	}

	if ( $infant && $infant > 0 ) {
		$item->update_meta_data( 'infant', $infant );
	}

	if ( $check_in_out_date ) {
		$item->update_meta_data( 'check_in_out_date', $check_in_out_date );
	}
}

add_action( 'woocommerce_checkout_create_order_line_item', 'tf_apartment_custom_order_data', 10, 4 );


/**
 * Add order id to the hotel room meta field
 *
 * runs during WooCommerce checkout process
 *
 * @author Jahid
 */
function tf_add_apartment_data_checkout_order_processed( $order_id, $posted_data, $order ) {

	$tf_integration_order_data   = array(
		'order_id' => $order_id
	);
	$tf_integration_order_status = [];
	# Get and Loop Over Order Line Items
	foreach ( $order->get_items() as $item_id => $item ) {

		$order_type = $item->get_meta( '_order_type', true );

		if ( "apartment" == $order_type ) {
			$post_id = $item->get_meta( '_post_id', true ); // Apartment id

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
		}

		// Apartment Item Data Insert 
		if ( "apartment" == $order_type ) {
			$price             = $item->get_subtotal();
			$check_in_out_date = $item->get_meta( 'check_in_out_date', true );
			$adult             = $item->get_meta( 'adults', true );
			$child             = $item->get_meta( 'children', true );
			$infants           = $item->get_meta( 'infant', true );

			if ( $check_in_out_date ) {
				list( $check_in, $check_out ) = explode( ' - ', $check_in_out_date );
			}

			$iteminfo = [
				'check_in'    => $check_in,
				'check_out'   => $check_out,
				'adult'       => $adult,
				'child'       => $child,
				'infants'     => $infants,
				'total_price' => $price,
			];

			$tf_integration_order_data[] = [
				'check_in'       => $check_in,
				'check_out'      => $check_out,
				'adult'          => $adult,
				'child'          => $child,
				'infants'        => $infants,
				'total_price'    => $price,
				'customer_id'    => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status'   => $order->get_status(),
				'order_date'     => date( 'Y-m-d H:i:s' )
			];

			$tf_integration_order_status = [
				'customer_id'    => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status'   => $order->get_status(),
				'order_date'     => date( 'Y-m-d H:i:s' )
			];

			$iteminfo_keys = array_keys( $iteminfo );
			$iteminfo_keys = array_map( 'sanitize_key', $iteminfo_keys );

			$iteminfo_values = array_values( $iteminfo );
			$iteminfo_values = array_map( 'sanitize_text_field', $iteminfo_values );

			$iteminfo = array_combine( $iteminfo_keys, $iteminfo_values );


			global $wpdb;
			$table_name = $wpdb->prefix . 'tf_order_data';
			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO $table_name
				( order_id, post_id, post_type, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
					array(
						$order_id,
						sanitize_key( $post_id ),
						$order_type,
						$check_in,
						$check_out,
						json_encode( $billinginfo ),
						json_encode( $shippinginfo ),
						json_encode( $iteminfo ),
						$order->get_customer_id(),
						$order->get_payment_method(),
						$order->get_status(),
						date( 'Y-m-d H:i:s' )
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

add_action( 'woocommerce_checkout_order_processed', 'tf_add_apartment_data_checkout_order_processed', 10, 4 );

