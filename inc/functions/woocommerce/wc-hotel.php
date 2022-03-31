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
    $post_author = get_post_field( 'post_author', $post_id );
    $meta = get_post_meta( $post_id, 'tf_hotel', true );
    $rooms = !empty($meta['room']) ? $meta['room'] : '';
    $room_name = $rooms[$room_id]['title'];
    $pricing_by = $rooms[$room_id]['pricing-by'];

    /**
     * All form data
     * 
     */
    $location = isset( $_POST['location'] ) ? sanitize_text_field( $_POST['location'] ) : '';
    // People number
    $adult = isset( $_POST['adult'] ) ? intval( sanitize_text_field( $_POST['adult'] ) ) : '0';
    $child = isset( $_POST['child'] ) ? intval( sanitize_text_field( $_POST['child'] ) ) : '0';
    $room_selected = isset( $_POST['room'] ) ? intval( sanitize_text_field( $_POST['room'] ) ) : '0';
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
    if ( !$room_selected ) {
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
    if(!array_key_exists('errors', $response) || count($response['errors']) == 0) {

        $tf_room_data['tf_hotel_data']['order_type'] = 'hotel';
        $tf_room_data['tf_hotel_data']['post_author'] = $post_author;
        $tf_room_data['tf_hotel_data']['post_id'] = $post_id;
        $tf_room_data['tf_hotel_data']['location'] = $location;
        $tf_room_data['tf_hotel_data']['adult'] = $adult;
        $tf_room_data['tf_hotel_data']['child'] = $child;
        $tf_room_data['tf_hotel_data']['check_in'] = $check_in;
        $tf_room_data['tf_hotel_data']['check_out'] = $check_out;
        $tf_room_data['tf_hotel_data']['room'] = $room_selected;
        $tf_room_data['tf_hotel_data']['room_name'] = $room_name;

        if ($pricing_by == '1') {
            $total_price = $rooms[$room_id]['price'];
        } elseif ($pricing_by == '2') {
            $adult_price = $rooms[$room_id]['adult_price'];
            $adult_price = $adult_price * $adult;
            $child_price = $rooms[$room_id]['child_price'];
            $child_price = $child_price * $child;
            $total_price = $adult_price + $child_price;
        }
        $price_total = $total_price*$room_selected;

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
            'key'       => __('Room', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['room_name'],
        );
    }

    if ( isset( $cart_item['tf_hotel_data']['room'] ) && $cart_item['tf_hotel_data']['room'] > 0 ) {
        $item_data[] = array(
            'key'       => __('Number of Room Booked', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['room'],
        );
    }

    if ( isset( $cart_item['tf_hotel_data']['adult'] ) && $cart_item['tf_hotel_data']['adult'] > 0 ) {
        $item_data[] = array(
            'key'       => __('Adult Number', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['adult'],
        );
    }

    if ( isset( $cart_item['tf_hotel_data']['child'] ) && $cart_item['tf_hotel_data']['child'] > 0 ) {
        $item_data[] = array(
            'key'       => __('Child Number', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['child'],
        );
    }

    if ( isset( $cart_item['tf_hotel_data']['check_in'] ) ) {
        $item_data[] = array(
            'key'       => __('Check-in', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['check_in'],
        );
    }

    if ( isset( $cart_item['tf_hotel_data']['check_out'] ) ) {
        $item_data[] = array(
            'key'       => __('Check-out', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['check_out'],
        );
    }

    return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'display_cart_item_custom_meta_data', 10, 2 );

/**
 * Show custom data in order details
 */
function tf_hotel_custom_order_data( $item, $cart_item_key, $values, $order ) {

    // Assigning data into variables
    $order_type = $values['tf_hotel_data']['order_type'];
    $post_author = $values['tf_hotel_data']['post_author'];
    $post_id = !empty($values['tf_hotel_data']['post_id']) ? $values['tf_hotel_data']['post_id'] : '';
    $room_name = !empty($values['tf_hotel_data']['room_name']) ? $values['tf_hotel_data']['room_name'] : '';
    $room_selected = !empty($values['tf_hotel_data']['room']) ? $values['tf_hotel_data']['room'] : '';
    $adult = !empty($values['tf_hotel_data']['adult']) ? $values['tf_hotel_data']['adult'] : '';
    $child = !empty($values['tf_hotel_data']['child']) ? $values['tf_hotel_data']['child'] : '';
    $check_in = !empty($values['tf_hotel_data']['check_in']) ? $values['tf_hotel_data']['check_in'] : '';
    $check_out = !empty($values['tf_hotel_data']['check_out']) ? $values['tf_hotel_data']['check_out'] : '';

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

    if ($room_name) {

        $item->update_meta_data( __('room_name', 'tourfic'), $room_name );
    }

    if ( $room_selected && $room_selected > 0 ) {

        $item->update_meta_data( __('number_room_booked', 'tourfic'), $room_selected );
    }

    if ( $adult && $adult > 0 ) {

        $item->update_meta_data( __('adult', 'tourfic'), $adult );
    }

    if ( $child && $child > 0 ) {

        $item->update_meta_data( __('child', 'tourfic'), $child );
    }

    if ( $check_in ) {

        $item->update_meta_data( __('check_in', 'tourfic'), $check_in );
    }

    if ( $check_out ) {

        $item->update_meta_data( __('check_out', 'tourfic'), $check_out );
    }

}
add_action( 'woocommerce_checkout_create_order_line_item', 'tf_hotel_custom_order_data', 10, 4 );
?>