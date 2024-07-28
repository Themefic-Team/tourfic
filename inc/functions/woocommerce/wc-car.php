<?php
defined( 'ABSPATH' ) || exit;

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