<?php
defined( 'ABSPATH' ) || exit;

/**
 * Hotel booking ajax function
 * 
 * @since 2.2.0
 */
add_action( 'wp_ajax_tf_hotel_booking', 'tf_hotel_booking_callback' );
add_action( 'wp_ajax_nopriv_tf_hotel_booking', 'tf_hotel_booking_callback' );

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

    /**
     * Data from booking form
     * 
     * With errors
     */
    $post_id = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : null;
    $room_id = isset( $_POST['room_id'] ) ? intval( sanitize_text_field( $_POST['room_id'] ) ) : null;
    $unique_id = isset( $_POST['unique_id'] ) ? intval( sanitize_text_field( $_POST['unique_id'] ) ) : null;   
    $location = isset( $_POST['location'] ) ? sanitize_text_field( $_POST['location'] ) : '';
    // People number
    $adult = isset( $_POST['adult'] ) ? intval( sanitize_text_field( $_POST['adult'] ) ) : '0';
    $child = isset( $_POST['child'] ) ? intval( sanitize_text_field( $_POST['child'] ) ) : '0';
    $room_selected = isset( $_POST['room'] ) ? intval( sanitize_text_field( $_POST['room'] ) ) : '0';
    $check_in = isset( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
    $check_out = isset( $_POST['check_out_date'] ) ? sanitize_text_field( $_POST['check_out_date'] ) : '';

    # Calculate night number
    if($check_in && $check_out) {
        $check_in_stt = strtotime($check_in);
        $check_out_stt = strtotime($check_out);
        $day_difference = round((($check_out_stt - $check_in_stt) / (60 * 60 * 24)) + 1);
    }

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

    /**
     * Backend options panel data
     * 
     * @since 2.2.0
     */
    $product_id = get_post_meta( $post_id, 'product_id', true );
    $post_author = get_post_field( 'post_author', $post_id );
    $meta = get_post_meta( $post_id, 'tf_hotel', true );
    $rooms = !empty($meta['room']) ? $meta['room'] : '';
    $avail_by_date = !empty($rooms[$room_id]['avil_by_date']) ? $rooms[$room_id]['avil_by_date'] : false;
    if($avail_by_date) {
        $repeat_by_date = !empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
    }
    $room_name = $rooms[$room_id]['title'];
    $pricing_by = $rooms[$room_id]['pricing-by'];
    $price_multi_day = !empty($rooms[$room_id]['price_multi_day']) ? $rooms[$room_id]['price_multi_day'] : false;   

    /**
     * If no errors then process
     */
    if( !array_key_exists('errors', $response) || count($response['errors']) == 0 ) {

        $tf_room_data['tf_hotel_data']['order_type'] = 'hotel';
        $tf_room_data['tf_hotel_data']['post_id'] = $post_id;
        $tf_room_data['tf_hotel_data']['unique_id'] = $unique_id;
        $tf_room_data['tf_hotel_data']['post_permalink'] = get_permalink($post_id);
        $tf_room_data['tf_hotel_data']['post_author'] = $post_author;
        $tf_room_data['tf_hotel_data']['post_id'] = $post_id;
        $tf_room_data['tf_hotel_data']['location'] = $location;
        $tf_room_data['tf_hotel_data']['adult'] = $adult;
        $tf_room_data['tf_hotel_data']['child'] = $child;
        $tf_room_data['tf_hotel_data']['check_in'] = $check_in;
        $tf_room_data['tf_hotel_data']['check_out'] = $check_out;
        $tf_room_data['tf_hotel_data']['room'] = $room_selected;
        $tf_room_data['tf_hotel_data']['room_name'] = $room_name;

        /**
         * Calculate Pricing
         */
        if( $avail_by_date ) {

        } else {

            if ( $pricing_by == '1' ) {

                $total_price = $rooms[$room_id]['price'];

            } elseif ( $pricing_by == '2' ) {

                $adult_price = $rooms[$room_id]['adult_price'];
                $adult_price = $adult_price * $adult;
                $child_price = $rooms[$room_id]['child_price'];
                $child_price = $child_price * $child;
                $total_price = $adult_price + $child_price;
                
            }

        }

        # Multiply pricing by night number
        if(!empty($day_difference) && $price_multi_day == true) {
            $price_total = $total_price*$room_selected*$day_difference;
        } else {
            $price_total = $total_price*$room_selected;
        }

        # Set pricing
        $tf_room_data['tf_hotel_data']['price_total'] = $price_total;

        # Add product to cart with the custom cart item data
        WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_room_data );

        $response['product_id'] = $product_id;
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
 * Over write WooCommerce Price
 */
function tf_hotel_set_order_price( $cart ) {

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
        return;

    foreach ( $cart->get_cart() as $cart_item ) {

        if( isset( $cart_item['tf_hotel_data']['price_total'] ) ){
            $cart_item['data']->set_price( $cart_item['tf_hotel_data']['price_total'] );
        }
    }

}
add_action( 'woocommerce_before_calculate_totals', 'tf_hotel_set_order_price', 30, 1 );

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
 * Change cart item permalink
 */
function tf_hotel_cart_item_permalink( $permalink, $cart_item, $cart_item_key ) {

    $type = !empty($cart_item['tf_hotel_data']['order_type']) ? $cart_item['tf_hotel_data']['order_type'] : '';
    if ( is_cart() && $type == 'hotel') {
        $permalink = $cart_item['tf_hotel_data']['post_permalink'];
    }

    return $permalink;

}
add_filter ( 'woocommerce_cart_item_permalink', 'tf_hotel_cart_item_permalink' , 10, 3 );

/**
 * Show custom data in order details
 */
function tf_hotel_custom_order_data( $item, $cart_item_key, $values, $order ) {

    // Assigning data into variables
    $order_type = $values['tf_hotel_data']['order_type'];
    $post_author = $values['tf_hotel_data']['post_author'];
    $post_id = !empty($values['tf_hotel_data']['post_id']) ? $values['tf_hotel_data']['post_id'] : '';
    $unique_id = !empty($values['tf_hotel_data']['unique_id']) ? $values['tf_hotel_data']['unique_id'] : '';
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

    if ( $unique_id ) {
        $item->update_meta_data( '_unique_id', $unique_id );
    }

    if ($room_name) {

        $item->update_meta_data( 'room_name', $room_name );
    }

    if ( $room_selected && $room_selected > 0 ) {

        $item->update_meta_data( 'number_room_booked', $room_selected );
    }

    if ( $adult && $adult > 0 ) {

        $item->update_meta_data( 'adult', $adult );
    }

    if ( $child && $child > 0 ) {

        $item->update_meta_data( 'child', $child );
    }

    if ( $check_in ) {

        $item->update_meta_data( 'check_in', $check_in );
    }

    if ( $check_out ) {

        $item->update_meta_data( 'check_out', $check_out );
    }

}
add_action( 'woocommerce_checkout_create_order_line_item', 'tf_hotel_custom_order_data', 10, 4 );

/**
 * Add order id to the hotel room meta field
 * 
 * runs during WooCommerce checkout process
 * 
 * @author fida
 */
function tf_add_order_id_room_checkout_order_processed( $order_id, $posted_data, $order ) {

    # Get and Loop Over Order Line Items
    foreach ( $order->get_items() as $item_id => $item ) {

        $post_id = $item->get_meta( '_post_id', true ); // Hotel id
        $unique_id = $item->get_meta( '_unique_id', true ); // Unique id of rooms
        $meta = get_post_meta( $post_id, 'tf_hotel', true ); // Hotel meta
        $rooms = !empty($meta['room']) ? $meta['room'] : ''; 
        $new_rooms = []; 

        # Get and Loop Over Room Meta
        foreach($rooms as $room) {
            
            # Check if order is for this room
            if($room['unique_id'] == $unique_id){

                $old_order_id = $room['order_id'];

                $old_order_id != "" && $old_order_id .= ",";
                $old_order_id .= $order_id;

                # set old + new data to the oder_id meta
                $room['order_id']  = $old_order_id;
            }

            # Set whole room array
            $new_rooms[] = $room; 
        }

        # Set whole room array to the room meta
        $meta['room'] = $new_rooms;
        # Update hotel post meta with array values
        update_post_meta( $post_id, 'tf_hotel', $meta );

    }
}
add_action( 'woocommerce_checkout_order_processed', 'tf_add_order_id_room_checkout_order_processed', 10, 3 );
?>