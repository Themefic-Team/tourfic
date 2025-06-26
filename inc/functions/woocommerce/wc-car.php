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
	$tf_protection  = isset( $_POST['protection'] ) ? $_POST['protection'] : '';
	$extra_ids  = isset( $_POST['extra_ids'] ) ? $_POST['extra_ids'] : '';
	$extra_qty  = isset( $_POST['extra_qty'] ) ? $_POST['extra_qty'] : '';
	$partial_payment  = isset( $_POST['partial_payment'] ) ? $_POST['partial_payment'] : 'no';

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
		$total_extra = Pricing::set_extra_price($meta, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time, $extra_ids, $extra_qty);
		$total_prices = $total_prices + $total_extra['price'];
		$tf_cars_data['tf_car_data']['extras'] = $total_extra['title'];
	}

	if(!empty($tf_protection)){
		$total_protection_prices = Pricing::set_protection_price($meta, $tf_protection, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);
		if(!empty($total_protection_prices['price'])){
			$total_prices = $total_prices + $total_protection_prices['price'];
		}
		if(!empty($total_protection_prices['title'])){
			$tf_cars_data['tf_car_data']['protection'] = $total_protection_prices['title'];
		}
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
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($car_allow_deposit) && 'none'!=$car_deposit_type && 'yes'==$partial_payment) {
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
		
		if( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($car_booking_by) && '3'==$car_booking_by ){

			$tf_booking_fields = !empty(Helper::tfopt( 'car-book-confirm-field' )) ? Helper::tf_data_types(Helper::tfopt( 'car-book-confirm-field' )) : '';
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
				'status'           => 'processing',
				'order_date'       => gmdate( 'Y-m-d H:i:s' ),
			);
			$response['without_payment'] = 'true';
			$order_id = Helper::tf_set_order( $order_data );
			
			if ( function_exists('is_tf_pro') && is_tf_pro() && !empty($order_id) ) {
				do_action( 'tf_offline_payment_booking_confirmation', $order_id, $order_data );

				if ( ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-integration' ) )['tf-new-order-google-calendar'] ) && Helper::tf_data_types( Helper::tfopt( 'tf-integration' ) )['tf-new-order-google-calendar'] == "1" ) {
					
					/**
					 * Filters the data passed to the Google Calendar integration.
					 *
					 * @param int    $order_id   The order ID.
					 * @param array  $order_data The items in the order.
					 * @param string $type Order type
					 */
					apply_filters( 'tf_after_booking_completed_calendar_data', $order_id, $order_data, '' );
				}
			}

		}else{
			if( function_exists( 'is_tf_pro' ) && is_tf_pro() && '2'==$car_booking_by && !empty($tf_booking_url) ){
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


/**
 *
 * runs during WooCommerce checkout process
 *
 * @author Jahid
 */
function tf_add_car_data_checkout_order_processed( $order_id, $posted_data, $order ) {

	$tf_integration_order_data   = array(
		'order_id' => $order_id
	);

	$tf_integration_order_status = [];
	# Get and Loop Over Order Line Items
	foreach ( $order->get_items() as $item_id => $item ) {

		$order_type = $item->get_meta( '_order_type', true );

		if ( "car" == $order_type ) {
			$post_id = $item->get_meta( '_post_id', true ); // Car id

			//Tax Calculation
			$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );
			$tax_labels = array();
			if( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($meta['is_taxable'])){
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

		// Car Item Data Insert 
		if ( "car" == $order_type ) {
			$price = $item->get_subtotal();
			$pickup = $item->get_meta( 'Pick Up Location', true );
			$tf_pickup_date = $item->get_meta( 'Pick Up Date', true );
			$tf_pickup_time = $item->get_meta( 'Pick Up Time', true );
			$dropoff = $item->get_meta( 'Drop Off Location', true );
			$tf_dropoff_date = $item->get_meta( 'Drop Off Date', true );
			$tf_dropoff_time = $item->get_meta( 'Drop Off Time', true );
			$tf_protection = $item->get_meta( 'Protection', true );
			$tf_extra = $item->get_meta( 'Extra', true );
			$tf_due = $item->get_meta( 'Due', true );

			$iteminfo = [
				'pickup_location'   => $pickup,
				'pickup_date'   => $tf_pickup_date,
				'pickup_time'   => $tf_pickup_time,
				'dropoff_location'   => $dropoff,
				'dropoff_date'   => $tf_dropoff_date,
				'dropoff_time'   => $tf_dropoff_time,
				'extra' => !empty($tf_extra) ? $tf_extra : '',
				'protection' => !empty($tf_protection) ? $tf_protection : '',
				'due' => !empty($tf_due) ? $tf_due : '',
				'total_price' => $price,
				'tax_info' => wp_json_encode($fee_sums)
			];

			$tf_integration_order_data[] = [
				'pickup_location'   => $pickup,
				'pickup_date'   => $tf_pickup_date,
				'pickup_time'   => $tf_pickup_time,
				'dropoff_location'   => $dropoff,
				'dropoff_date'   => $tf_dropoff_date,
				'dropoff_time'   => $tf_dropoff_time,
				'extra' => !empty($tf_extra) ? $tf_extra : '',
				'protection' => !empty($tf_protection) ? $tf_protection : '',
				'due' => !empty($tf_due) ? $tf_due : '',
				'total_price' => $price,
				'customer_id'    => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status'   => $order->get_status(),
				'order_date'     => gmdate( 'Y-m-d H:i:s' )
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
				( order_id, post_id, post_type, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
					array(
						$order_id,
						sanitize_key( $post_id ),
						$order_type,
						$tf_pickup_date,
						$tf_dropoff_date,
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

add_action( 'woocommerce_checkout_order_processed', 'tf_add_car_data_checkout_order_processed', 10, 4 );

/**
 *
 * runs during WooCommerce checkout process for block checkout
 *
 * @author Jahid
 */

function tf_add_car_data_checkout_order_processed_block_checkout( $order ) {

	$order_id = $order->get_id();

	$tf_integration_order_data   = array(
		'order_id' => $order_id
	);

	$tf_integration_order_status = [];
	# Get and Loop Over Order Line Items
	foreach ( $order->get_items() as $item_id => $item ) {

		$order_type = $item->get_meta( '_order_type', true );

		if ( "car" == $order_type ) {
			$post_id = $item->get_meta( '_post_id', true ); // Car id

			//Tax Calculation
			$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );
			$tax_labels = array();
			if( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($meta['is_taxable'])){
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

		// Car Item Data Insert 
		if ( "car" == $order_type ) {
			$price = $item->get_subtotal();
			$pickup = $item->get_meta( 'Pick Up Location', true );
			$tf_pickup_date = $item->get_meta( 'Pick Up Date', true );
			$tf_pickup_time = $item->get_meta( 'Pick Up Time', true );
			$dropoff = $item->get_meta( 'Drop Off Location', true );
			$tf_dropoff_date = $item->get_meta( 'Drop Off Date', true );
			$tf_dropoff_time = $item->get_meta( 'Drop Off Time', true );
			$tf_protection = $item->get_meta( 'Protection', true );
			$tf_extra = $item->get_meta( 'Extra', true );
			$tf_due = $item->get_meta( 'Due', true );

			$iteminfo = [
				'pickup_location'   => $pickup,
				'pickup_date'   => $tf_pickup_date,
				'pickup_time'   => $tf_pickup_time,
				'dropoff_location'   => $dropoff,
				'dropoff_date'   => $tf_dropoff_date,
				'dropoff_time'   => $tf_dropoff_time,
				'extra' => !empty($tf_extra) ? $tf_extra : '',
				'protection' => !empty($tf_protection) ? $tf_protection : '',
				'due' => !empty($tf_due) ? $tf_due : '',
				'total_price' => $price,
				'tax_info' => wp_json_encode($fee_sums)
			];

			$tf_integration_order_data[] = [
				'pickup_location'   => $pickup,
				'pickup_date'   => $tf_pickup_date,
				'pickup_time'   => $tf_pickup_time,
				'dropoff_location'   => $dropoff,
				'dropoff_date'   => $tf_dropoff_date,
				'dropoff_time'   => $tf_dropoff_time,
				'extra' => !empty($tf_extra) ? $tf_extra : '',
				'protection' => !empty($tf_protection) ? $tf_protection : '',
				'due' => !empty($tf_due) ? $tf_due : '',
				'total_price' => $price,
				'customer_id'    => $order->get_customer_id(),
				'payment_method' => $order->get_payment_method(),
				'order_status'   => $order->get_status(),
				'order_date'     => gmdate( 'Y-m-d H:i:s' )
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
				( order_id, post_id, post_type, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
					array(
						$order_id,
						sanitize_key( $post_id ),
						$order_type,
						$tf_pickup_date,
						$tf_dropoff_date,
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

add_action( 'woocommerce_store_api_checkout_order_processed', 'tf_add_car_data_checkout_order_processed_block_checkout', 10, 4 );