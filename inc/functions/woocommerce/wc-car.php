<?php
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Car_Rental\Pricing;
use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Car_Rental\Availability;

/**
 * car booking ajax function
 *
 * @since 2.12.10
 */
add_action( 'wp_ajax_tf_car_booking', 'tf_car_booking_callback' );
add_action( 'wp_ajax_nopriv_tf_car_booking', 'tf_car_booking_callback' );

/**
 * Handles AJAX for Booking
 *
 * @return void
 * @throws Exception
 * @since 2.12.10
 */


function tf_car_booking_callback() {
	// Check nonce security
	if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['_nonce'])), 'tf_ajax_nonce' ) ) {
		return;
	}

	/**
	 * Get car meta values
	 */
	$post_id   = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : null;
	$pickup   = isset( $_POST['pickup'] ) ? sanitize_text_field( $_POST['pickup'] ) : '';
	$dropoff = isset( $_POST['dropoff'] ) ? sanitize_text_field( $_POST['dropoff'] ) : '';
	$tf_pickup_date  = isset( $_POST['pickup_date'] ) ? sanitize_text_field( $_POST['pickup_date'] ) : '';
	$tf_dropoff_date  = isset( $_POST['dropoff_date'] ) ? sanitize_text_field( $_POST['dropoff_date'] ) : '';
	$tf_pickup_time  = isset( $_POST['pickup_time'] ) ? sanitize_text_field( $_POST['pickup_time'] ) : '';
	$tf_dropoff_time  = isset( $_POST['dropoff_time'] ) ? sanitize_text_field( $_POST['dropoff_time'] ) : '';
	$tf_protection  = isset( $_POST['protection'] ) ? sanitize_text_field( $_POST['protection'] ) : '';
	$extra_ids  = isset( $_POST['extra_ids'] ) ? $_POST['extra_ids'] : '';
	$extra_qty  = isset( $_POST['extra_qty'] ) ? $_POST['extra_qty'] : '';

	// Booking Confirmation Details
	$tf_confirmation_details = !empty($_POST['travellerData']) ? $_POST['travellerData'] : "";

	$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );
	$post_author   = get_post_field( 'post_author', $post_id );

	// Booking
	$car_booking_by = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : '1';
	$tf_booking_url = !empty( $meta['booking-url'] ) ? esc_url($meta['booking-url']) : '';
	$tf_booking_query_url = !empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'pickup={pickup}&dropoff={dropoff}&pickup_date={pickup_date}&dropoff_date={dropoff_date}';
	$tf_booking_attribute = !empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';

	$product_id    = get_post_meta( $post_id, 'product_id', true );
	$get_prices = Pricing::set_total_price($meta, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);
	
	$total_prices = $get_prices['sale_price'] ? $get_prices['sale_price'] : 0;

	$response      = array();
	$tf_cars_data = array();

	// Deposit
	$car_allow_deposit = ! empty( $meta['allow_deposit'] ) ? $meta['allow_deposit'] : '';
	$car_deposit_type = ! empty( $meta['deposit_type'] ) ? $meta['deposit_type'] : 'none';
	$car_deposit_amount = ! empty( $meta['deposit_amount'] ) ? $meta['deposit_amount'] : 0;

	$car_inventory = Availability::tf_car_inventory($post_id, $meta, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);
	// Check Inventory
	if ( ! $car_inventory ) {
		$response['errors'][] = esc_html__( 'Car Not Available this Slot!', 'tourfic' );
	}

	if(!empty($extra_ids)){
		$total_extra = Pricing::set_extra_price($meta, $extra_ids, $extra_qty);
		$total_prices = $total_prices + $total_extra['price'];
		$tf_cars_data['tf_car_data']['extras'] = $total_extra['title'];
	}

	if(!empty('yes'==$tf_protection)){
		$total_protection_prices = Pricing::set_protection_price($meta);
		$total_prices = $total_prices + $total_protection_prices;
		$tf_cars_data['tf_car_data']['protection']         = 'Included';
	}

	/**
	 * If no errors then process
	 */
	if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {
		$tf_cars_data['tf_car_data']['order_type']         = 'car';
		$tf_cars_data['tf_car_data']['post_id']            = $post_id;
		$tf_cars_data['tf_car_data']['post_permalink']     = get_permalink( $post_id );
		$tf_cars_data['tf_car_data']['post_author']        = $post_author;
		$tf_cars_data['tf_car_data']['pickup']             = $pickup;
		$tf_cars_data['tf_car_data']['dropoff']            = $dropoff;
		$tf_cars_data['tf_car_data']['tf_pickup_date']     = $tf_pickup_date;
		$tf_cars_data['tf_car_data']['tf_dropoff_date']    = $tf_dropoff_date;
		$tf_cars_data['tf_car_data']['tf_pickup_time']     = $tf_pickup_time;
		$tf_cars_data['tf_car_data']['tf_dropoff_time']    = $tf_dropoff_time;
		$tf_cars_data['tf_car_data']['price_total']    	   = $total_prices;

		# Deposit information
		if ( !empty($car_allow_deposit) && 'none'!=$car_deposit_type ) {
			if( !empty($car_deposit_amount) ){
				if ( 'percent'==$car_deposit_type ) {
					$deposit_amount = ($tf_cars_data['tf_car_data']['price_total'] * $car_deposit_amount)/100;
				}
				if ( 'fixed'==$car_deposit_type ) {
					$deposit_amount = $car_deposit_amount;
				}
				$tf_cars_data['tf_car_data']['due']   = $tf_cars_data['tf_car_data']['price_total'] - $deposit_amount;
				$tf_cars_data['tf_car_data']['price_total'] = $deposit_amount;
			}
		}
		
		if( !empty($car_booking_by) && '3'==$car_booking_by ){

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

			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				// get user id
				$tf_offline_user_id = $current_user->ID;
			} else {
				$tf_offline_user_id = 1;
			}

			$order_details = [
				'order_by'    => '',
				'pickup_location'   => $pickup,
				'pickup_date'   => $tf_pickup_date,
				'pickup_time'   => $tf_pickup_time,
				'dropoff_location'   => $dropoff,
				'dropoff_date'   => $tf_dropoff_date,
				'dropoff_time'   => $tf_dropoff_time,
				'extra' => !empty($tf_cars_data['tf_car_data']['extras']) ? $tf_cars_data['tf_car_data']['extras'] : '',
				'protection' => !empty($tf_cars_data['tf_car_data']['protection']) ? $tf_cars_data['tf_car_data']['protection'] : '',
				'total_price' => $total_prices
			];

			$order_data = array(
				'post_id'          => $post_id,
				'post_type'        => 'car',
				'room_number'      => null,
				'check_in'         => $tf_pickup_date,
				'check_out'        => $tf_dropoff_date,
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
			if( '2'==$car_booking_by && !empty($tf_booking_url) ){
				$external_search_info = array(
					'{pickup}'    => $pickup,
					'{dropoff}'    => $dropoff,
					'{pickup_date}' => $tf_pickup_date,
					'{dropoff_date}'     => $tf_dropoff_date
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
				WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_cars_data );

				$response['product_id']  = $product_id;
				$response['add_to_cart'] = 'true';
				$response['redirect_to'] = wc_get_checkout_url();
			}
			$response['without_payment'] = 'false';
		}
	}else {
		$response['status'] = 'error';
	}

	// Json Response
	echo wp_json_encode( $response );

	die();
}

/**
 * Over write WooCommerce Price
 */
function tf_car_set_order_price( $cart ) {

	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
		return;
	}

	foreach ( $cart->get_cart() as $cart_item ) {
		if ( isset( $cart_item['tf_car_data']['price_total'] ) ) {
			$cart_item['data']->set_price( $cart_item['tf_car_data']['price_total'] );
		}
	}

}

add_action( 'woocommerce_before_calculate_totals', 'tf_car_set_order_price', 30, 1 );

// Display custom cart item meta data (in cart and checkout)
function car_display_cart_item_custom_meta_data( $item_data, $cart_item ) {

	if ( isset( $cart_item['tf_car_data']['pickup'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Pick Up Location', 'tourfic' ),
			'value' => $cart_item['tf_car_data']['pickup'],
		);
	}
	if ( isset( $cart_item['tf_car_data']['dropoff'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Drop Off Location', 'tourfic' ),
			'value' => $cart_item['tf_car_data']['dropoff'],
		);
	}
	if ( isset( $cart_item['tf_car_data']['tf_pickup_date'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Pick Up Date', 'tourfic' ),
			'value' => $cart_item['tf_car_data']['tf_pickup_date'].' - '. $cart_item['tf_car_data']['tf_pickup_time'],
		);
	}
	if ( isset( $cart_item['tf_car_data']['tf_dropoff_date'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Drop Off Date', 'tourfic' ),
			'value' => $cart_item['tf_car_data']['tf_dropoff_date'].' - '. $cart_item['tf_car_data']['tf_dropoff_time'],
		);
	}
	if ( isset( $cart_item['tf_car_data']['extras'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Extra', 'tourfic' ),
			'value' => $cart_item['tf_car_data']['extras'],
		);
	}
	if ( isset( $cart_item['tf_car_data']['protection'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Car Protection', 'tourfic' ),
			'value' => $cart_item['tf_car_data']['protection'],
		);
	}
	if ( isset( $cart_item['tf_car_data']['due'] ) ) {
		$item_data[] = array(
			'key'   => esc_html__( 'Due', 'tourfic' ),
			'value' => wc_price($cart_item['tf_car_data']['due']),
		);
	}

	return $item_data;

}

add_filter( 'woocommerce_get_item_data', 'car_display_cart_item_custom_meta_data', 10, 2 );


/**
 * Show custom data in order details
 */
function tf_car_custom_order_data( $item, $cart_item_key, $values, $order ) {

	// Assigning data into variables
	$order_type = !empty($values['tf_car_data']['order_type']) ? $values['tf_car_data']['order_type'] : '';
	$post_author = !empty($values['tf_car_data']['post_author']) ? $values['tf_car_data']['post_author'] : '';
	$post_id = !empty($values['tf_car_data']['post_id']) ? $values['tf_car_data']['post_id'] : '';
	$pickup = !empty($values['tf_car_data']['pickup']) ? $values['tf_car_data']['pickup'] : '';
	$dropoff = !empty($values['tf_car_data']['dropoff']) ? $values['tf_car_data']['dropoff'] : '';
	$tf_pickup_date = !empty($values['tf_car_data']['tf_pickup_date']) ? $values['tf_car_data']['tf_pickup_date'] : '';
	$tf_dropoff_date = !empty($values['tf_car_data']['tf_dropoff_date']) ? $values['tf_car_data']['tf_dropoff_date'] : '';
	$tf_pickup_time = !empty($values['tf_car_data']['tf_pickup_time']) ? $values['tf_car_data']['tf_pickup_time'] : '';
	$tf_dropoff_time = !empty($values['tf_car_data']['tf_dropoff_time']) ? $values['tf_car_data']['tf_dropoff_time'] : '';
	$extras = !empty($values['tf_car_data']['extras']) ? $values['tf_car_data']['extras'] : '';
	$protection = !empty($values['tf_car_data']['protection']) ? $values['tf_car_data']['protection'] : '';
	$due = !empty($values['tf_car_data']['due']) ? wc_price($values['tf_car_data']['due']) : '';
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

	if ( $pickup ) {
		$item->update_meta_data( 'Pick Up Location', $pickup );
	}
	if ( $tf_pickup_date ) {
		$item->update_meta_data( 'Pick Up Date', $tf_pickup_date );
	}
	if ( $tf_pickup_time ) {
		$item->update_meta_data( 'Pick Up Time', $tf_pickup_time );
	}

	if ( $dropoff ) {
		$item->update_meta_data( 'Drop Off Location', $dropoff );
	}
	if ( $tf_dropoff_date ) {
		$item->update_meta_data( 'Drop Off Date', $tf_dropoff_date );
	}
	if ( $tf_dropoff_time ) {
		$item->update_meta_data( 'Drop Off Time', $tf_dropoff_time );
	}
	if ( $extras ) {
		$item->update_meta_data( 'Extra', $extras );
	}

	if ( $protection ) {
		$item->update_meta_data( 'Protection', $protection );
	}

	if ( $due ) {
		$item->update_meta_data( 'Due', $due );
	}

}

add_action( 'woocommerce_checkout_create_order_line_item', 'tf_car_custom_order_data', 10, 4 );