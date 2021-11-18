<?php

class Tourfic_Tours_WooCommerceHandle{

	public function __construct() {

		// Booking ajax
		add_action('wp_ajax_tf_tours_booking', [ $this, 'tf_tours_booking_function' ] );
		add_action('wp_ajax_nopriv_tf_tours_booking', [ $this, 'tf_room_booking_function' ] );

		// proccess room price
		add_action('woocommerce_before_calculate_totals', [ $this, 'set_order_price' ], 30, 1 );

		// Display custom cart item meta data (in cart and checkout)
		add_filter( 'woocommerce_get_item_data', [ $this, 'display_cart_item_custom_meta_data' ], 10, 2 );

		add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'add_custom_data_to_order' ], 10, 4 );

	}

	/**
	 * Handles AJAX for Booking
	 *
	 * @return string
	 */
	function tf_tours_booking_function(){
		// Verify nonce
		// check_ajax_referer( 'ajax-login-nonce', 'security' );

		$response = array();
		$tf_tours_data = array();

		$tour_id = isset( $_POST['tour_id'] ) ? intval( sanitize_text_field( $_POST['tour_id'] ) ) : null;

		$adults = isset( $_POST['adults'] ) ? intval( sanitize_text_field( $_POST['adults'] ) ) : null;
		$children = isset( $_POST['children'] ) ? intval( sanitize_text_field( $_POST['children'] ) ) : null;

		$destination = isset( $_POST['destination'] ) ? sanitize_text_field( $_POST['destination'] ) : null;
		$check_in = isset( $_POST['check-in-date'] ) ? sanitize_text_field( $_POST['check-in-date'] ) : null;
		$check_out = isset( $_POST['check-out-date'] ) ? sanitize_text_field( $_POST['check-out-date'] ) : null;

		// Check errors
		if ( !$check_in ) {
			$response['errors'][] = __('Check-in date missing.','tourfic');
		}
		if ( !$check_out ) {
			$response['errors'][] = __('Check-out date missing.','tourfic');
		}
		if ( !$adults ) {
			$response['errors'][] = __('Select Adult(s).','tourfic');
		}
		if ( !$tour_id  ) {
			$response['errors'][] = __('Unknown Error! Please try again.','tourfic');
		}

		$post_title = get_the_title( $tour_id );

		// Add Product
		$product_arr = apply_filters( 'tf_create_product_array', array(
		    'post_title' => $post_title,
		    'post_type' => 'product',
		    'post_status' => 'publish',
		    'post_password' => tourfic_proctected_product_pass(),
		    'meta_input'   => array(
		        '_price' => '0',
		        '_regular_price' => '0',
		        '_visibility' => 'visible',
		        '_virtual' => 'yes',
		        '_sold_individually' => 'yes',
		    )
		) );

		$product_id = post_exists( $post_title,'','','product');

		if ( $product_id ) {

			$response['product_status'] = 'exists';

		} else {

			$product_id = wp_insert_post( $product_arr );

			if( !is_wp_error($product_id) ){

			  	$response['product_status'] = 'new';

			}else{

			  	$response['errors'][] = $product_id->get_error_message();
			  	$response['status'] = 'error';
			}

		}

		// If no errors then process
		if( 0 == count( $response['errors'] ) ) {

			$tf_tours_data['tf_data']['tour_id'] = $tour_id;

			$tf_tours_data['tf_data']['adults'] = $adults;
			$tf_tours_data['tf_data']['children'] = $children;
			$tf_tours_data['tf_data']['infant'] = $children;
			$tf_tours_data['tf_data']['check_in'] = $check_in;
			$tf_tours_data['tf_data']['check_out'] = $check_out;

			// If want to empty the cart
			//WC()->cart->empty_cart();

			// Add product to cart with the custom cart item data
			WC()->cart->add_to_cart( $product_id, 1, '0', array(), $tf_tours_data );

			$response['product_id'] = $product_id;
			$response['add_to_cart'] = 'true';
			$response['redirect_to'] = wc_get_checkout_url();
		} else {
			$response['status'] = 'error';
		}

		//ppr($get_room_type);

		// Json Response
		echo wp_json_encode( $response );

		die();
	}

	// Set price
	function set_order_price( $cart ) {
	    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
	        return;

	    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
	        return;

	    foreach ( $cart->get_cart() as $cart_item ) {

	        if( isset($cart_item['tf_data']['price_total']) ){
	            $cart_item['data']->set_price( $cart_item['tf_data']['price_total'] );
	        }
	    }

	}

	// Display custom cart item meta data (in cart and checkout)
	function display_cart_item_custom_meta_data( $item_data, $cart_item ) {

	    if ( isset( $cart_item['tf_data']['adults'] ) && $cart_item['tf_data']['adults'] > 0 ) {
	        $item_data[] = array(
	            'key'       => __('Adults', 'tourfic'),
	            'value'     => $cart_item['tf_data']['adults'],
	        );
	    }

	    if ( isset( $cart_item['tf_data']['children'] ) && $cart_item['tf_data']['children'] > 0 ) {
	        $item_data[] = array(
	            'key'       => __('Children', 'tourfic'),
	            'value'     => $cart_item['tf_data']['children'],
	        );
	    }

	    if ( isset( $cart_item['tf_data']['check_in'] ) ) {
	        $item_data[] = array(
	            'key'       => __('Check-in Date', 'tourfic'),
	            'value'     => $cart_item['tf_data']['check_in'],
	        );
	    }

	    if ( isset( $cart_item['tf_data']['check_out'] ) ) {
	        $item_data[] = array(
	            'key'       => __('Check-out Date', 'tourfic'),
	            'value'     => $cart_item['tf_data']['check_out'],
	        );
	    }

	    return $item_data;
	}

	/**
	 * Add custom field to order object
	 */
	function add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {


	    if ( isset( $values['tf_data']['room_name'] ) ) {

	        $item->update_meta_data( __('Room Type', 'tourfic'), $values['tf_data']['room_name'] );
	    }

	    if ( isset( $values['tf_data']['room_selected'] ) && $values['tf_data']['room_selected'] > 0 ) {

	        $item->update_meta_data( __('Room Selected', 'tourfic'), $values['tf_data']['room_selected'] );
	    }

	    if ( isset( $values['tf_data']['adults'] ) && $values['tf_data']['adults'] > 0 ) {

	        $item->update_meta_data( __('Adults', 'tourfic'), $values['tf_data']['adults'] );
	    }

	    if ( isset( $values['tf_data']['children'] ) && $values['tf_data']['children'] > 0 ) {

	        $item->update_meta_data( __('Children', 'tourfic'), $values['tf_data']['children'] );
	    }

	    if ( isset( $values['tf_data']['check_in'] ) ) {

	        $item->update_meta_data( __('Check-in Date', 'tourfic'), $values['tf_data']['check_in'] );
	    }

	    if ( isset( $values['tf_data']['check_out'] ) ) {

	        $item->update_meta_data( __('Check-out Date', 'tourfic'), $values['tf_data']['check_out'] );
	    }

	}


}

new Tourfic_Tours_WooCommerceHandle;