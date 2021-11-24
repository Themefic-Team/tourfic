<?php

class Tourfic_Tours_WooCommerceHandle {

    public function __construct() {

        // Booking ajax
        add_action( 'wp_ajax_tf_tours_booking', [$this, 'tf_tours_booking_function'] );
        add_action( 'wp_ajax_nopriv_tf_tours_booking', [$this, 'tf_tours_booking_function'] );

        // proccess room price
        add_action( 'woocommerce_before_calculate_totals', [$this, 'tours_set_order_price'], 30, 1 );

        // Display custom cart item meta data (in cart and checkout)
        add_filter( 'woocommerce_get_item_data', [$this, 'tours_display_cart_item_custom_meta_data'], 10, 2 );

        add_action( 'woocommerce_checkout_create_order_line_item', [$this, 'add_custom_data_to_order'], 10, 4 );

    }

    /**
     * Handles AJAX for Booking
     *
     * @return string
     */
    function tf_tours_booking_function() {
        // Verify nonce
        // check_ajax_referer( 'ajax-login-nonce', 'security' );

        $response = array();
        $tf_tours_data = array();

        $tour_id = isset( $_POST['tour_id'] ) ? intval( sanitize_text_field( $_POST['tour_id'] ) ) : null;

        $adults = isset( $_POST['adults'] ) ? intval( sanitize_text_field( $_POST['adults'] ) ) : null;
        $children = isset( $_POST['childrens'] ) ? intval( sanitize_text_field( $_POST['childrens'] ) ) : null;
        $infant = isset( $_POST['infants'] ) ? intval( sanitize_text_field( $_POST['infants'] ) ) : null;

        $destination = isset( $_POST['destination'] ) ? sanitize_text_field( $_POST['destination'] ) : null;
        $check_in = isset( $_POST['check-in-date'] ) ? sanitize_text_field( $_POST['check-in-date'] ) : null;
        $check_out = isset( $_POST['check-out-date'] ) ? sanitize_text_field( $_POST['check-out-date'] ) : null;

        //Validation of Fixed tours person
        $total_person = $adults + $children + $infant;
        $meta = get_post_meta( $tour_id,'tf_tours_option',true );
        $type = $meta['type'];
        $fixed_min_seat = $meta['fixed_availability']['min_seat'] ? $meta['fixed_availability']['min_seat'] : null;
        $fixed_max_seat = $meta['fixed_availability']['max_seat'] ? $meta['fixed_availability']['max_seat'] : null;
        if($type == 'fixed'){
            if( $total_person < $fixed_min_seat && $fixed_min_seat > 0 ){
            $response['errors'][] = __( 'You must select minimum '.$fixed_min_seat.' person ', 'tourfic' );

            }else if( $total_person > $fixed_max_seat && $fixed_max_seat > 0 ){
                $response['errors'][] = __( 'Maximum '.$fixed_max_seat.' person are allowed for this tour ', 'tourfic' );
    
            }

        }

        //validation continuous tours date
        if( $type == 'continuous' ){
            $continuous_availability = $meta['continuous_availability'];
            foreach( $continuous_availability as $key => $availability){
                if( $key === array_key_first( $continuous_availability ) ){
                    $check_in = strtotime(str_replace('/','-',$check_in));
                    $check_out = strtotime(str_replace('/','-',$check_out));
                    $continuous_min_seat    = $availability['min_seat'];
                    $continuous_max_seat    = $availability['max_seat'];
                    $continuous_check_in    = strtotime(str_replace( '/','-', $availability['check_in']));
                    $continuous_check_out   = strtotime(str_replace( '/','-', $availability['check_out']));
                    if( ($check_in >= $continuous_check_in && $check_out <= $continuous_check_out) && $total_person < $continuous_min_seat ){
                        $response['errors'][] = __( 'Minimum '.$continuous_min_seat.' person you must select ', 'tourfic' );            

                    }else{
                        $response['errors'][] = __( 'Select correct date range ', 'tourfic' );            

                    }
                }elseif( $key === array_key_last( $continuous_availability ) ){
                    $check_in = strtotime(str_replace('/','-',$check_in));
                    $check_out = strtotime(str_replace('/','-',$check_out));
                    $continuous_min_seat    = $availability['min_seat'];
                    $continuous_max_seat    = $availability['max_seat'];
                    $continuous_check_in    = strtotime(str_replace( '/','-', $availability['check_in']));
                    $continuous_check_out   = strtotime(str_replace( '/','-', $availability['check_out']));
                    if( ($check_in >= $continuous_check_in && $check_out <= $continuous_check_out) && $total_person < $continuous_min_seat ){
                        $response['errors'][] = __( 'Minimum '.$continuous_min_seat.' person you must select ', 'tourfic' );            

                    }else{
                        $response['errors'][] = __( 'Select correct date range ', 'tourfic' );            

                    }
                }
            }

        }
        
        // Check errors
        if ( !$check_in ) {
            $response['errors'][] = __( 'Check-in date missing.', 'tourfic' );
        }
        if ( !$check_out ) {
            $response['errors'][] = __( 'Check-out date missing.', 'tourfic' );
        }
        if ( !$adults ) {
            $response['errors'][] = __( 'Select Adult(s).', 'tourfic' );
        }
        if ( !$tour_id ) {
            $response['errors'][] = __( 'Unknown Error! Please try again.', 'tourfic' );
        }

        $post_title = get_the_title( $tour_id );

        // Add Product
        $product_arr = apply_filters( 'tf_create_product_array', array(
            'post_title'    => $post_title,
            'post_type'     => 'product',
            'post_status'   => 'publish',
            'post_password' => tourfic_proctected_product_pass(),
            'meta_input'    => array(
                '_price'             => '0',
                '_regular_price'     => '0',
                '_visibility'        => 'visible',
                '_virtual'           => 'yes',
                '_sold_individually' => 'yes',
            ),
        ) );

        $product_id = post_exists( $post_title, '', '', 'product' );

        if ( $product_id ) {

            $response['product_status'] = 'exists';

        } else {

            $product_id = wp_insert_post( $product_arr );

            if ( !is_wp_error( $product_id ) ) {

                $response['product_status'] = 'new';

            } else {

                $response['errors'][] = $product_id->get_error_message();
                $response['status'] = 'error';
            }

        }

        // If no errors then process
        if ( 0 == count( $response['errors'] ) ) {

            $tf_tours_data['tf_tours_data']['tour_id'] = $tour_id;

            $tf_tours_data['tf_tours_data']['adults'] = $adults;
            $tf_tours_data['tf_tours_data']['childrens'] = $children;
            $tf_tours_data['tf_tours_data']['infants'] = $infant;
            $tf_tours_data['tf_tours_data']['check_in'] = $check_in;
            $tf_tours_data['tf_tours_data']['check_out'] = $check_out;

            $meta = get_post_meta( $tour_id, 'tf_tours_option', true );
            $discount_type = $meta['discount_type'];
            $discounted_price = $meta['discount_price'];
            $pricing_rule = $meta['pricing'];
            $group_price = $meta['group_price'];
            $adult_price = $meta['adult_price'];
            $children_price = $meta['child_price'];
            $infant_price = $meta['infant_price'];

            if ( $discount_type == 'percent' ) {
                $adult_price = number_format( $adult_price - (  ( $adult_price / 100 ) * $discounted_price ), 1 );
                $children_price = number_format( $children_price - (  ( $children_price / 100 ) * $discounted_price ), 1 );
                $infant_price = number_format( $infant_price - (  ( $infant_price / 100 ) * $discounted_price ), 1 );
                $group_price = number_format( $group_price - (  ( $group_price / 100 ) * $discounted_price ), 1 );
            } elseif ( $discount_type == 'fixed' ) {
                $adult_price = number_format(  ( $adult_price - $discounted_price ), 1 );
                $children_price = number_format(  ( $children_price - $discounted_price ), 1 );
                $infant_price = number_format(  ( $infant_price - $discounted_price ), 1 );
                $infant_price = number_format(  ( $infant_price - $discounted_price ), 1 );
                $group_price = number_format(  ( $group_price - $discounted_price ), 1 );
            }
            if ( $pricing_rule == 'group' ) {
                $tf_tours_data['tf_tours_data']['price'] = $group_price;
            } else {
                $tf_tours_data['tf_tours_data']['price'] = ( $adult_price * $adults ) + ( $children * $children_price ) + ( $infant * $infant_price );
            }

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
    function tours_set_order_price( $cart ) {
        if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
            return;
        }

        if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
            return;
        }

        foreach ( $cart->get_cart() as $cart_item ) {

            if ( isset( $cart_item['tf_tours_data']['price'] ) ) {
                $cart_item['data']->set_price( $cart_item['tf_tours_data']['price'] );
            }
        }

    }

    // Display custom cart item meta data (in cart and checkout)
    function tours_display_cart_item_custom_meta_data( $item_data, $cart_item ) {

        if ( isset( $cart_item['tf_tours_data']['adults'] ) && $cart_item['tf_tours_data']['adults'] > 0 ) {
            $item_data[] = array(
                'key'   => __( 'Adults', 'tourfic' ),
                'value' => $cart_item['tf_tours_data']['adults'],
            );
        }

        if ( isset( $cart_item['tf_tours_data']['childrens'] ) && $cart_item['tf_tours_data']['childrens'] > 0 ) {
            $item_data[] = array(
                'key'   => __( 'Children', 'tourfic' ),
                'value' => $cart_item['tf_tours_data']['childrens'],
            );
        }

        if ( isset( $cart_item['tf_tours_data']['infants'] ) && $cart_item['tf_tours_data']['infants'] > 0 ) {
            $item_data[] = array(
                'key'   => __( 'Infant', 'tourfic' ),
                'value' => $cart_item['tf_tours_data']['infants'],
            );
        }

        if ( isset( $cart_item['tf_tours_data']['check_in'] ) ) {
            $item_data[] = array(
                'key'   => __( 'Check-in Date', 'tourfic' ),
                'value' => $cart_item['tf_tours_data']['check_in'],
            );
        }

        if ( isset( $cart_item['tf_tours_data']['check_out'] ) ) {
            $item_data[] = array(
                'key'   => __( 'Check-out Date', 'tourfic' ),
                'value' => $cart_item['tf_tours_data']['check_out'],
            );
        }

        return $item_data;
    }

    /**
     * Add custom field to order object
     */
    function add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {

        if ( isset( $values['tf_tours_data']['adults'] ) && $values['tf_tours_data']['adults'] > 0 ) {

            $item->update_meta_data( __( 'Adults', 'tourfic' ), $values['tf_tours_data']['adults'] );
        }

        if ( isset( $values['tf_tours_data']['childrens'] ) && $values['tf_tours_data']['childrens'] > 0 ) {

            $item->update_meta_data( __( 'Children', 'tourfic' ), $values['tf_tours_data']['childrens'] );
        }
        if ( isset( $values['tf_tours_data']['infants'] ) && $values['tf_tours_data']['infants'] > 0 ) {

            $item->update_meta_data( __( 'Infants', 'tourfic' ), $values['tf_tours_data']['infants'] );
        }
        if ( isset( $values['tf_tours_data']['check_in'] ) ) {

            $item->update_meta_data( __( 'Check-in Date', 'tourfic' ), $values['tf_tours_data']['check_in'] );
        }

        if ( isset( $values['tf_tours_data']['check_out'] ) ) {

            $item->update_meta_data( __( 'Check-out Date', 'tourfic' ), $values['tf_tours_data']['check_out'] );
        }

    }

}

new Tourfic_Tours_WooCommerceHandle;