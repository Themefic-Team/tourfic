<?php
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Car_Rental\Pricing;

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

	$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );

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
	
	if( !empty($car_booking_by) && '3'==$car_booking_by ){

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

}

add_action( 'woocommerce_checkout_create_order_line_item', 'tf_car_custom_order_data', 10, 4 );