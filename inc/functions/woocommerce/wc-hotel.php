<?php
defined( 'ABSPATH' ) || exit;

/**
 * Hotel booking ajax function
 * 
 * @since 2.2.0
 */
add_action('wp_ajax_tf_hotel_booking', 'tf_hotel_booking_callback');
add_action('wp_ajax_nopriv_tf_hotel_booking', 'tf_hotel_booking_callback');

/**
 * Handles AJAX for Booking
 *
 * @return string
 */
function tf_hotel_booking_callback(){
    
    // Check nonce security
    if ( !isset( $_POST['tf_room_booking_nonce'] ) || !wp_verify_nonce( $_POST['tf_room_booking_nonce'], 'check_room_booking_nonce' ) ) {
        return;
    }

    // Declaring errors & hotel data array
    $response = array();
    $tf_room_data = array();
    $post_id = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : null;
    $room_id = isset( $_POST['room_id'] ) ? intval( sanitize_text_field( $_POST['room_id'] ) ) : null;

    /**
     * Backend options panel data
     * 
     * @since 2.2.0
     */
    $meta = get_post_meta( $post_id, 'tf_hotel', true );
    $rooms = !empty($meta['room']) ? $meta['room'] : '';
    $pricing_by = $rooms[$room_id]['pricing-by'];

    /**
     * All form data
     * 
     */
    $location = isset( $_POST['location'] ) ? sanitize_text_field( $_POST['location'] ) : '';
    // People number
    $adult = isset( $_POST['adult'] ) ? intval( sanitize_text_field( $_POST['adult'] ) ) : '0';
    $child = isset( $_POST['child'] ) ? intval( sanitize_text_field( $_POST['child'] ) ) : '0';
    $room = isset( $_POST['room'] ) ? intval( sanitize_text_field( $_POST['room'] ) ) : '0';
    $check_in = isset( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
    $check_out = isset( $_POST['check_out_date'] ) ? sanitize_text_field( $_POST['check_out_date'] ) : '';

    // Check errors
    if ( !$check_in ) {
        $response['errors'][] = __('Check-in date missing.','tourfic');
    }
    if ( !$check_out ) {
        $response['errors'][] = __('Check-out date missing.','tourfic');
    }
    if ( !$adult ) {
        $response['errors'][] = __('Select Adult(s).','tourfic');
    }
    if ( !$room ) {
        $response['errors'][] = __('Select Room(s).','tourfic');
    }
    if ( !$post_id  ) {
        $response['errors'][] = __('Unknown Error! Please try again.','tourfic');
    }

    $post_title = get_the_title( $post_id );

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

    //echo var_dump($rooms);

    //$response['errors'][] = $pricing_by;
    // If no errors then process
    if( 0 == count( $response['errors'] ) ) {

        $tf_room_data['tf_hotel_data']['post_id'] = $post_id;
        $tf_room_data['tf_hotel_data']['location'] = $location;
        $tf_room_data['tf_hotel_data']['adult'] = $adult;
        $tf_room_data['tf_hotel_data']['child'] = $child;
        $tf_room_data['tf_hotel_data']['check_in'] = $check_in;
        $tf_room_data['tf_hotel_data']['check_out'] = $check_out;
        $tf_room_data['tf_hotel_data']['room'] = $room;

        $tf_room_data['tf_hotel_data']['price'] = $get_room_type['price'];
        $tf_room_data['tf_hotel_data']['sale_price'] = $get_room_type['sale_price'];

        if ($pricing_by == '1') {
            $total_price = $rooms[$room_id]['price'];
        } elseif ($pricing_by == '2') {
            $adult_price = $rooms[$room_id]['adult_price'];
            $adult_price = $adult_price * $adult;
            $child_price = $rooms[$room_id]['child_price'];
            $child_price = $child_price * $child;
            $total_price = $adult_price + $child_price;
        }
        $price_total = $total_price*$room;

        $tf_room_data['tf_hotel_data']['price_total'] = $price_total;

        // If want to empty the cart
        //WC()->cart->empty_cart();

        // Add product to cart with the custom cart item data
        WC()->cart->add_to_cart( $product_id, 1, '0', array(), $tf_room_data );

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

        if( isset($cart_item['tf_hotel_data']['price_total']) ){
            $cart_item['data']->set_price( $cart_item['tf_hotel_data']['price_total'] );
        }
    }

}
add_action('woocommerce_before_calculate_totals', 'set_order_price', 30, 1 );

// Display custom cart item meta data (in cart and checkout)
function display_cart_item_custom_meta_data( $item_data, $cart_item ) {

    if ( isset( $cart_item['tf_hotel_data']['room_name'] ) ) {
        $item_data[] = array(
            'key'       => __('Room Type', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['room_name'],
        );
    }

    if ( isset( $cart_item['tf_hotel_data']['room'] ) && $cart_item['tf_hotel_data']['room'] > 0 ) {
        $item_data[] = array(
            'key'       => __('Room Selected', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['room'],
        );
    }

    if ( isset( $cart_item['tf_hotel_data']['adult'] ) && $cart_item['tf_hotel_data']['adult'] > 0 ) {
        $item_data[] = array(
            'key'       => __('adult', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['adult'],
        );
    }

    if ( isset( $cart_item['tf_hotel_data']['child'] ) && $cart_item['tf_hotel_data']['child'] > 0 ) {
        $item_data[] = array(
            'key'       => __('child', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['child'],
        );
    }

    if ( isset( $cart_item['tf_hotel_data']['check_in'] ) ) {
        $item_data[] = array(
            'key'       => __('Check-in Date', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['check_in'],
        );
    }

    if ( isset( $cart_item['tf_hotel_data']['check_out'] ) ) {
        $item_data[] = array(
            'key'       => __('Check-out Date', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['check_out'],
        );
    }

    return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'display_cart_item_custom_meta_data', 10, 2 );

/**
 * Add custom field to order object
 */
function add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {


    if ( isset( $values['tf_hotel_data']['room_name'] ) ) {

        $item->update_meta_data( __('Room Type', 'tourfic'), $values['tf_hotel_data']['room_name'] );
    }

    if ( isset( $values['tf_hotel_data']['room'] ) && $values['tf_hotel_data']['room'] > 0 ) {

        $item->update_meta_data( __('Room Selected', 'tourfic'), $values['tf_hotel_data']['room'] );
    }

    if ( isset( $values['tf_hotel_data']['adult'] ) && $values['tf_hotel_data']['adult'] > 0 ) {

        $item->update_meta_data( __('adult', 'tourfic'), $values['tf_hotel_data']['adult'] );
    }

    if ( isset( $values['tf_hotel_data']['child'] ) && $values['tf_hotel_data']['child'] > 0 ) {

        $item->update_meta_data( __('child', 'tourfic'), $values['tf_hotel_data']['child'] );
    }

    if ( isset( $values['tf_hotel_data']['check_in'] ) ) {

        $item->update_meta_data( __('Check-in Date', 'tourfic'), $values['tf_hotel_data']['check_in'] );
    }

    if ( isset( $values['tf_hotel_data']['check_out'] ) ) {

        $item->update_meta_data( __('Check-out Date', 'tourfic'), $values['tf_hotel_data']['check_out'] );
    }

}
add_action( 'woocommerce_checkout_create_order_line_item', 'add_custom_data_to_order', 10, 4 );
?>