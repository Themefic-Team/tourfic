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

	$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );

	// Booking
	$car_booking_by = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : '1';

	$product_id    = get_post_meta( $post_id, 'product_id', true );
	$total_prices = Pricing::set_total_price($meta, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time);


	$response      = array();
	$tf_cars_data = array();


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
	$tf_cars_data['tf_car_data']['price_total']    	   = $total_prices['sale_price'];
	
	if('1'==$car_booking_by){
		WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_cars_data );

		$response['product_id']  = $product_id;
		$response['add_to_cart'] = 'true';
		$response['redirect_to'] = wc_get_checkout_url();
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

	return $item_data;

}

add_filter( 'woocommerce_get_item_data', 'car_display_cart_item_custom_meta_data', 10, 2 );