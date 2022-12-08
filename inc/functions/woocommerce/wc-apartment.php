<?php
defined( 'ABSPATH' ) || exit;

/**
 * Hotel booking ajax function
 *
 * @since 2.2.0
 */
add_action( 'wp_ajax_tf_apartment_booking', 'tf_apartment_booking_callback' );
add_action( 'wp_ajax_nopriv_tf_apartment_booking', 'tf_apartment_booking_callback' );

/**
 * Handles AJAX for Booking
 *
 * @return void
 * @throws Exception
 * @since 2.9.1
 */
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

	$product_id      = get_post_meta( $post_id, 'product_id', true );
	$post_author     = get_post_field( 'post_author', $post_id );
	$meta            = get_post_meta( $post_id, 'tf_apartment_opt', true );
	$max_adults      = ! empty( $meta['max_adults'] ) ? $meta['max_adults'] : '';
	$max_children    = ! empty( $meta['max_children'] ) ? $meta['max_children'] : '';
	$max_infants     = ! empty( $meta['max_infants'] ) ? $meta['max_infants'] : '';
	$price_per_night = ! empty( $meta['price_per_night'] ) ? $meta['price_per_night'] : 0;
	$discount_type   = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
	$discount        = ! empty( $meta['discount'] ) ? $meta['discount'] : 0;

	if ( defined( 'TF_PRO' ) ) {
		$additional_fees = ! empty( $meta['additional_fees'] ) ? $meta['additional_fees'] : array();
	} else {
		$additional_fee       = ! empty( $meta['additional_fee'] ) ? $meta['additional_fee'] : 0;
		$fee_type             = ! empty( $meta['fee_type'] ) ? $meta['fee_type'] : '';
	}

	# Calculate nights
	if ( ! empty( $check_in_out_date ) ) {
		$check_in_out = explode( ' - ', $check_in_out_date );
		$check_in_stt      = strtotime( $check_in_out[0] . ' +1 day' );
		$check_out_stt     = strtotime( $check_in_out[1] );
		$days              = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
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
	if ( empty($post_id) ) {
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
			$total_price = $price_per_night * $days;

			if ( defined( 'TF_PRO' ) ){
				foreach ($additional_fees as $key => $item){
					if ( $item['fee_type'] == 'per_night' ){
						$total_price += $item['additional_fee'] * $days;
					} elseif($item['fee_type'] == 'per_person') {
						$total_price += $item['additional_fee'] * ( $adults + $children + $infant );
					} else {
						$total_price += $item['additional_fee'];
					}
				}
			} else {
				if ( $fee_type == 'per_night' ){
					$total_price += $additional_fee * $days;
				} elseif($fee_type == 'per_person') {
					$total_price += $additional_fee * ( $adults + $children + $infant );
				} else {
					$total_price += $additional_fee;
				}
			}

			if ( $discount_type == 'percent' ) {
				$total_price = $total_price - ( $total_price * ( $discount / 100 ) );
			} elseif ( $discount_type == 'fixed' ) {
				$total_price = $total_price - $discount;
			}

			$tf_apartment_data['tf_apartment_data']['total_price'] = $total_price;
		}


		# Add product to cart with the custom cart item data
		WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_apartment_data );

		$response['product_id']  = $product_id;
		$response['add_to_cart'] = 'true';
		$response['redirect_to'] = wc_get_checkout_url();
	} else {
		$response['status'] = 'error';
	}

	// Json Response
	echo wp_json_encode( $response );

	die();
}

/**
 * Override WooCommerce Price
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

// Display custom cart item meta data (in cart and checkout)
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


