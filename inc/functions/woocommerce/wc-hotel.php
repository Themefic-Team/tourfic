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
 * @return void
 * @throws Exception
 */
function tf_hotel_booking_callback(){
    
    // Check nonce security
    if ( !isset( $_POST['tf_room_booking_nonce'] ) || !wp_verify_nonce( $_POST['tf_room_booking_nonce'], 'check_room_booking_nonce' ) ) {
        return;
    }

    // Declaring errors & hotel data array
    $response     = [];
    $tf_room_data = [];

    /**
     * Data from booking form
     * 
     * With errors
     */
    $post_id   = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : null;
    $room_id   = isset( $_POST['room_id'] ) ? intval( sanitize_text_field( $_POST['room_id'] ) ) : null;
    $unique_id = isset( $_POST['unique_id'] ) ? intval( sanitize_text_field( $_POST['unique_id'] ) ) : null;
    $location  = isset( $_POST['location'] ) ? sanitize_text_field( $_POST['location'] ) : '';
    // People number
    $adult         = isset( $_POST['adult'] ) ? intval( sanitize_text_field( $_POST['adult'] ) ) : '0';
    $child         = isset( $_POST['child'] ) ? intval( sanitize_text_field( $_POST['child'] ) ) : '0';
    $room_selected =     $room_selected = isset( $_POST['room'] ) ? intval( sanitize_text_field( $_POST['room'] ) ) : 1;
    $check_in      = isset( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
    $check_out     = isset( $_POST['check_out_date'] ) ? sanitize_text_field( $_POST['check_out_date'] ) : '';
    $deposit     = isset( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : false;
    $airport_service = isset($_POST['airport_service']) ? sanitize_text_field($_POST['airport_service']) : '';
    $hotel_pack = isset($_POST['hotel_pack']) ? sanitize_text_field($_POST['hotel_pack']) : '';
    $hotel_meal = isset($_POST['mealinfo']) ? sanitize_text_field($_POST['mealinfo']) : '';
    $quickorder = isset($_POST['quickorder']) ? sanitize_text_field($_POST['quickorder']) : '';
    $clients_email = isset($_POST['clients_email']) ? sanitize_email($_POST['clients_email']) : '';
    
    # Calculate night number
    if($check_in && $check_out) {
        $check_in_stt   = strtotime( $check_in . ' +1 day' );
        $check_out_stt  = strtotime( $check_out );
        $day_difference = round(  (  ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
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
    // if ( !$room_selected ) {
    //     $response['errors'][] = __('Select Room(s).','tourfic');
    // }
    if ( !$post_id  ) {
        $response['errors'][] = __('Unknown Error! Please try again.','tourfic');
    }

    /**
     * Backend options panel data
     * 
     * @since 2.2.0
     */
    $product_id    = get_post_meta( $post_id, 'product_id', true );
    $post_author   = get_post_field( 'post_author', $post_id );
    $meta          = get_post_meta( $post_id, 'tf_hotel', true );
    $rooms         = !empty( $meta['room'] ) ? $meta['room'] : '';
    $avail_by_date = !empty( $rooms[$room_id]['avil_by_date'] ) && $rooms[$room_id]['avil_by_date'];
    if ( $avail_by_date ) {
        $repeat_by_date = !empty( $rooms[$room_id]['repeat_by_date'] ) ? $rooms[$room_id]['repeat_by_date'] : [];
    }
    $room_name       = $rooms[$room_id]['title'];
    $pricing_by      = $rooms[$room_id]['pricing-by'];
    $price_multi_day = !empty( $rooms[$room_id]['price_multi_day'] ) ? $rooms[$room_id]['price_multi_day'] : false;


    /**
     * If no errors then process
     */
    if( !array_key_exists('errors', $response) || count($response['errors']) == 0 ) {

        $tf_room_data['tf_hotel_data']['order_type']     = 'hotel';
        $tf_room_data['tf_hotel_data']['post_id']        = $post_id;
        $tf_room_data['tf_hotel_data']['unique_id']      = $unique_id;
        $tf_room_data['tf_hotel_data']['post_permalink'] = get_permalink( $post_id );
        $tf_room_data['tf_hotel_data']['post_author']    = $post_author;
        $tf_room_data['tf_hotel_data']['post_id']        = $post_id;
        $tf_room_data['tf_hotel_data']['location']       = $location;
        $tf_room_data['tf_hotel_data']['check_in']       = $check_in;
        $tf_room_data['tf_hotel_data']['check_out']      = $check_out;
        $tf_room_data['tf_hotel_data']['room']           = $room_selected;
        $tf_room_data['tf_hotel_data']['room_name']      = $room_name;
        $tf_room_data['tf_hotel_data']['air_serivicetype'] = $airport_service;
        $tf_room_data['tf_hotel_data']['air_serivice_avail'] = $meta['airport_service'] ?? null;


        /**
         * Calculate Pricing
         */
        // if ( $avail_by_date && defined( 'TF_PRO' ) ) {
            
        //     // Check availability by date option
        //     $period = new DatePeriod(
        //         new DateTime( $check_in . ' 00:00' ),
        //         new DateInterval( 'P1D' ),
        //         new DateTime( $check_out . ' 00:00' )
        //     );
            
        //     $total_price = 0;
        //     foreach ( $period as $date ) {
                            
        //     $available_rooms = array_values( array_filter( $repeat_by_date, function ($date_availability ) use ( $date ) {
        //         $date_availability_from = strtotime( $date_availability['availability']['from'] . ' 00:00' );
        //         $date_availability_to   = strtotime( $date_availability['availability']['to'] . ' 23:59' );
        //         return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
        //     } ) );

        //     if ( is_iterable($available_rooms) && count( $available_rooms ) >=1) {                    
        //         $room_price    = !empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $rooms[$room_id]['price'];
        //         $adult_price   = !empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $rooms[$room_id]['adult_price'];
        //         $child_price   = !empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $rooms[$room_id]['child_price'];
        //         $total_price += $pricing_by == '1' ? $room_price : (  ( $adult_price * $adult ) + ( $child_price * $child ) );
                
        //         if ( $pricing_by == '1' ) {
        //             $tf_room_data['tf_hotel_data']['adult']          = $adult;
        //             $tf_room_data['tf_hotel_data']['child']          = $child;
        //         }
        //         if ( $pricing_by == '2' ) {
        //             $tf_room_data['tf_hotel_data']['adult']          = $adult." × ".wc_price($available_rooms[0]['adult_price']);
        //             $tf_room_data['tf_hotel_data']['child']          = $child." × ".wc_price($available_rooms[0]['child_price']);
        //         }
                
        //     } ;
                
        //     } 
            
        //     $price_total = $total_price*$room_selected;
        // } else {

            // if ( $pricing_by == '1' ) {
            //     $total_price = $rooms[$room_id]['price'];
                
            //     $tf_room_data['tf_hotel_data']['adult']          = $adult;
            //     $tf_room_data['tf_hotel_data']['child']          = $child;
            // } elseif ( $pricing_by == '2' ) {

                $roompackageprice = $rooms[$room_id]['tf-'.$hotel_pack.'-days'];
                $room_total_price = $rooms[$room_id]['adult'];
                if(!empty($hotel_meal)){
                    $price_total = ($roompackageprice['tf-room'] + $roompackageprice[''.$hotel_meal.''])*$room_total_price;
                    $tf_room_data['tf_hotel_data']['tf-room'] = $roompackageprice['tf-room'];
                    $tf_room_data['tf_hotel_data'][''.$hotel_meal.''] = $roompackageprice[''.$hotel_meal.''];
                }else{
                    $price_total = $roompackageprice['tf-room']*$room_total_price;
                    $tf_room_data['tf_hotel_data']['tf-room'] = $roompackageprice['tf-room'];
                }


                // $adult_price = $rooms[$room_id]['adult_price'];
                // $adult_price = $adult_price * $adult;
                // $child_price = $rooms[$room_id]['child_price'];
                // $child_price = $child_price * $child;
                // $total_price = $adult_price + $child_price;    
                                
                $tf_room_data['tf_hotel_data']['adult']          = $room_total_price;
                // $tf_room_data['tf_hotel_data']['child']          = $child;
            // }

            # Multiply pricing by night number
            // if(!empty($day_difference) && $price_multi_day == true) {
            //     $price_total = $total_price*$room_selected*$day_difference;
            // } else {
            //     $price_total = $total_price*$room_selected;
            // }

        // }

        
        # Set pricing
        $tf_room_data['tf_hotel_data']['price_total'] = $price_total;

        # Airport Service Fee 
        if (defined( 'TF_PRO' ) && !empty($tf_room_data['tf_hotel_data']['air_serivice_avail']) && 1==$tf_room_data['tf_hotel_data']['air_serivice_avail']) {
            if("pickup"==$airport_service){
                $airport_pickup_price  = !empty($meta['airport_pickup_price']) ? $meta['airport_pickup_price'] : '';
                $tf_room_data['tf_hotel_data']['price_type'] = $airport_pickup_price['airport_pickup_price_type'];
                if("per_person"==$tf_room_data['tf_hotel_data']['price_type']){
                    $airport_service_price_total = ($adult*$airport_pickup_price['airport_service_fee_adult']) + ($child*$airport_pickup_price['airport_service_fee_children']);
                    $tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
                    $tf_room_data['tf_hotel_data']['price_total'] += $airport_service_price_total;
                    if($child!=0){
                    
                        $tf_room_data['tf_hotel_data']['air_service_info'] = "Adult ( ".$adult." × ".wc_price($airport_pickup_price['airport_service_fee_adult']). " ) + Child ( ".$child." × ".wc_price($airport_pickup_price['airport_service_fee_children'])." ) = ". wc_price($airport_service_price_total);

                    }else{
                        $tf_room_data['tf_hotel_data']['air_service_info'] = "Adult ( ".$adult." × ".wc_price($airport_pickup_price['airport_service_fee_adult'])." )= ". wc_price($airport_service_price_total);
                        
                    }
                }
                if("fixed"==$tf_room_data['tf_hotel_data']['price_type']){
                    $airport_service_price_total = $airport_pickup_price['airport_service_fee_fixed'];
                    $tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
                    $tf_room_data['tf_hotel_data']['price_total'] += $airport_service_price_total;
                    $tf_room_data['tf_hotel_data']['air_service_info'] = "( Fixed ) = ". wc_price($airport_service_price_total);
                }
                if("free"==$tf_room_data['tf_hotel_data']['price_type']){
                    $tf_room_data['tf_hotel_data']['air_service_price'] = 0;
                    $tf_room_data['tf_hotel_data']['price_total'] += 0;
                    $tf_room_data['tf_hotel_data']['air_service_info'] = wc_price(0);
                }
            }
            if("dropoff"==$airport_service){
                $airport_pickup_price  = !empty($meta['airport_dropoff_price']) ? $meta['airport_dropoff_price'] : '';
                $tf_room_data['tf_hotel_data']['price_type'] = $airport_pickup_price['airport_pickup_price_type'];
                if("per_person"==$tf_room_data['tf_hotel_data']['price_type']){
                    $airport_service_price_total = ($adult*$airport_pickup_price['airport_service_fee_adult']) + ($child*$airport_pickup_price['airport_service_fee_children']);
                    $tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
                    $tf_room_data['tf_hotel_data']['price_total'] += $airport_service_price_total;
                    if($child!=0){
                    
                        $tf_room_data['tf_hotel_data']['air_service_info'] = "Adult ( ".$adult." × ".wc_price($airport_pickup_price['airport_service_fee_adult']). " ) + Child ( ".$child." × ".wc_price($airport_pickup_price['airport_service_fee_children'])." ) = ". wc_price($airport_service_price_total);

                    }else{
                        $tf_room_data['tf_hotel_data']['air_service_info'] = "Adult ( ".$adult." × ".wc_price($airport_pickup_price['airport_service_fee_adult'])." )= ". wc_price($airport_service_price_total);
                        
                    }
                }
                if("fixed"==$tf_room_data['tf_hotel_data']['price_type']){
                    $airport_service_price_total = $airport_pickup_price['airport_service_fee_fixed'];
                    $tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
                    $tf_room_data['tf_hotel_data']['price_total'] += $airport_service_price_total;
                    $tf_room_data['tf_hotel_data']['air_service_info'] = "( Fixed ) = ". wc_price($airport_service_price_total);
                }
                if("free"==$tf_room_data['tf_hotel_data']['price_type']){
                    $tf_room_data['tf_hotel_data']['air_service_price'] = 0;
                    $tf_room_data['tf_hotel_data']['price_total'] += 0;
                    $tf_room_data['tf_hotel_data']['air_service_info'] = wc_price(0);
                }
            }
            if("both"==$airport_service){
                $airport_pickup_price  = !empty($meta['airport_pickup_dropoff_price']) ? $meta['airport_pickup_dropoff_price'] : '';
                $tf_room_data['tf_hotel_data']['price_type'] = $airport_pickup_price['airport_pickup_price_type'];
                if("per_person"==$tf_room_data['tf_hotel_data']['price_type']){
                    $airport_service_price_total = ($adult*$airport_pickup_price['airport_service_fee_adult']) + ($child*$airport_pickup_price['airport_service_fee_children']);
                    $tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
                    $tf_room_data['tf_hotel_data']['price_total'] += $airport_service_price_total;
                    if($child!=0){
                    
                        $tf_room_data['tf_hotel_data']['air_service_info'] = "Adult ( ".$adult." × ".wc_price($airport_pickup_price['airport_service_fee_adult']). " ) + Child ( ".$child." × ".wc_price($airport_pickup_price['airport_service_fee_children'])." ) = ". wc_price($airport_service_price_total);

                    }else{
                        $tf_room_data['tf_hotel_data']['air_service_info'] = "Adult ( ".$adult." × ".wc_price($airport_pickup_price['airport_service_fee_adult'])." )= ". wc_price($airport_service_price_total);
                        
                    }
                }
                if("fixed"==$tf_room_data['tf_hotel_data']['price_type']){
                    $airport_service_price_total = $airport_pickup_price['airport_service_fee_fixed'];
                    $tf_room_data['tf_hotel_data']['air_service_price'] = $airport_service_price_total;
                    $tf_room_data['tf_hotel_data']['price_total'] += $airport_service_price_total;
                    $tf_room_data['tf_hotel_data']['air_service_info'] = "( Fixed ) = ". wc_price($airport_service_price_total);
                }
                if("free"==$tf_room_data['tf_hotel_data']['price_type']){
                    $tf_room_data['tf_hotel_data']['air_service_price'] = 0;
                    $tf_room_data['tf_hotel_data']['price_total'] += 0;
                    $tf_room_data['tf_hotel_data']['air_service_info'] = wc_price(0);
                }
            }
        }

        # check for deposit
        if($deposit=="true"){
           
            tf_get_deposit_amount($rooms[$room_id], $price_total, $deposit_amount, $has_deposit);
            if (defined( 'TF_PRO' ) && $has_deposit == true &&  !empty($deposit_amount) ) {
                $tf_room_data['tf_hotel_data']['price_total'] = $deposit_amount;
                if(!empty($airport_service)){
                    $tf_room_data['tf_hotel_data']['due'] = ($price_total+$airport_service_price_total) - $deposit_amount;
                }else{
                    $tf_room_data['tf_hotel_data']['due'] = $price_total - $deposit_amount;
                }
                
            }
        }
        if($quickorder!="true"){
            # Add product to cart with the custom cart item data
            WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_room_data );

            $response['product_id'] = $product_id;
            $response['add_to_cart'] = 'true';
            $response['redirect_to'] = wc_get_checkout_url();
        }else{
            $user = get_user_by('email', $clients_email);
            if($user){
                $customer = new WP_User($user->ID);
            }else{
                $email = strtolower($clients_email);
                $password = sanitize_text_field("vx@@4321");
                $customer = wp_create_user($email, $password, $email);
                $customer = new WP_User($customer);
            }
            WC()->cart->add_to_cart( $post_id, 1, '0', array(), $tf_room_data );
            $response['product_id'] = $product_id;
            $response['add_to_cart'] = 'true';

            $checkout = WC()->checkout();
            $order_id = $checkout->create_order(array(
                'billing_email' => $customer->user_email,
                'payment_method' => 'cash',
                'billing_first_name' => $customer->first_name,
                'billing_last_name' => $customer->last_name,
                'billing_address_1' => get_user_meta( $user->ID, 'billing_address_1', true ) ? get_user_meta( $user->ID, 'billing_address_1', true ) : '',
                'billing_address_2' => get_user_meta( $user->ID, 'billing_address_2', true ) ? get_user_meta( $user->ID, 'billing_address_2', true ) : '',
                'billing_city' => get_user_meta( $user->ID, 'billing_city', true ) ? get_user_meta( $user->ID, 'billing_city', true ) : '',
                'billing_state' => get_user_meta( $user->ID, 'billing_state', true ) ? get_user_meta( $user->ID, 'billing_state', true ) : '',
                'billing_country' => get_user_meta( $user->ID, 'billing_country', true ) ? get_user_meta( $user->ID, 'billing_country', true ) : '',
                'billing_postcode' => get_user_meta( $user->ID, 'billing_postcode', true ) ? get_user_meta( $user->ID, 'billing_postcode', true ) : ''
            ));


            $order = wc_get_order($order_id);
            update_post_meta($order_id, '_customer_user', $customer->ID);
            
            $order->set_total($tf_room_data['tf_hotel_data']['price_total']);
            
            $order_status = apply_filters('qofw_order_status', 'processing');
            $order->set_status($order_status);
            WC()->cart->empty_cart();
            return $order->save();
        }
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

    if ( isset( $cart_item['tf_hotel_data']['tf-room'] ) && $cart_item['tf_hotel_data']['tf-room'] > 0 ) {
        $item_data[] = array(
            'key'       => __('Alojamiento', 'tourfic'),
            'value'     => wc_price( $cart_item['tf_hotel_data']['tf-room'] ),
        );
    }
    if ( isset( $cart_item['tf_hotel_data']['tf-breakfast'] ) && $cart_item['tf_hotel_data']['tf-breakfast'] > 0 ) {
        $item_data[] = array(
            'key'       => __('Con Desayuno', 'tourfic'),
            'value'     => wc_price( $cart_item['tf_hotel_data']['tf-breakfast'] ),
        );
    }
    if ( isset( $cart_item['tf_hotel_data']['tf-half-b'] ) && $cart_item['tf_hotel_data']['tf-half-b'] > 0 ) {
        $item_data[] = array(
            'key'       => __('Con Media Pensión', 'tourfic'),
            'value'     => wc_price( $cart_item['tf_hotel_data']['tf-half-b'] ),
        );
    }
    if ( isset( $cart_item['tf_hotel_data']['tf-full-b'] ) && $cart_item['tf_hotel_data']['tf-full-b'] > 0 ) {
        $item_data[] = array(
            'key'       => __('Pensión Completa', 'tourfic'),
            'value'     => wc_price( $cart_item['tf_hotel_data']['tf-full-b'] ),
        );
    }
    if ( isset( $cart_item['tf_hotel_data']['tf-inclusive'] ) && $cart_item['tf_hotel_data']['tf-inclusive'] > 0 ) {
        $item_data[] = array(
            'key'       => __('Todo Incluido', 'tourfic'),
            'value'     => wc_price( $cart_item['tf_hotel_data']['tf-inclusive'] ),
        );
    }
    if ( isset( $cart_item['tf_hotel_data']['tf-inclusive-gold'] ) && $cart_item['tf_hotel_data']['tf-inclusive-gold'] > 0 ) {
        $item_data[] = array(
            'key'       => __('Todo Incluído Plus, Lavandería incluída', 'tourfic'),
            'value'     => wc_price( $cart_item['tf_hotel_data']['tf-inclusive-gold'] ),
        );
    }

    if ( isset( $cart_item['tf_hotel_data']['adult'] ) && $cart_item['tf_hotel_data']['adult'] >=1 ) {
        $item_data[] = array(
            'key'       => __('Adult Number', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['adult'],
        );
    }

    // if ( isset( $cart_item['tf_hotel_data']['child'] ) && $cart_item['tf_hotel_data']['child'] >=1 ) {
    //     $item_data[] = array(
    //         'key'       => __('Child Number', 'tourfic'),
    //         'value'     => $cart_item['tf_hotel_data']['child'],
    //     );
    // }

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

    // airport service type

    if (!empty($cart_item['tf_hotel_data']['air_serivice_avail']) && 1==$cart_item['tf_hotel_data']['air_serivice_avail'] && !empty($cart_item['tf_hotel_data']['air_serivicetype']) && "pickup"==$cart_item['tf_hotel_data']['air_serivicetype'] ) {
        $item_data[] = array(
            'key'       => __('Airport Service', 'tourfic'),
            'value'     => __('Airport Pickup', 'tourfic'),
        );
    }
    if (!empty($cart_item['tf_hotel_data']['air_serivice_avail']) && 1==$cart_item['tf_hotel_data']['air_serivice_avail'] && !empty($cart_item['tf_hotel_data']['air_serivicetype']) && "dropoff"==$cart_item['tf_hotel_data']['air_serivicetype'] ) {
        $item_data[] = array(
            'key'       => __('Airport Service', 'tourfic'),
            'value'     => __('Airport Dropoff', 'tourfic'),
        );
    }
    if (!empty($cart_item['tf_hotel_data']['air_serivice_avail']) && 1==$cart_item['tf_hotel_data']['air_serivice_avail'] && !empty($cart_item['tf_hotel_data']['air_serivicetype']) && "both"==$cart_item['tf_hotel_data']['air_serivicetype'] ) {
        $item_data[] = array(
            'key'       => __('Airport Service', 'tourfic'),
            'value'     => __('Airport Pickup & Dropoff', 'tourfic'),
        );
    }

    // airport price type

    if (!empty($cart_item['tf_hotel_data']['air_serivice_avail']) && !empty($cart_item['tf_hotel_data']['air_service_info']) && 1==$cart_item['tf_hotel_data']['air_serivice_avail'] && !empty($cart_item['tf_hotel_data']['air_serivicetype'])) {
        $item_data[] = array(
            'key'       => __('Airport Service Fee', 'tourfic'),
            'value'     => $cart_item['tf_hotel_data']['air_service_info'],
        );
    }

    if ( isset( $cart_item['tf_hotel_data']['due'] ) ) {
        $item_data[] = array(
            'key'       => __('Due', 'tourfic'),
            'value'     => wc_price($cart_item['tf_hotel_data']['due']),
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
    $order_type = !empty($values['tf_hotel_data']['order_type']) ? $values['tf_hotel_data']['order_type'] : '';
    $post_author = !empty($values['tf_hotel_data']['post_author']) ? $values['tf_hotel_data']['post_author'] : '';
    $post_id = !empty($values['tf_hotel_data']['post_id']) ? $values['tf_hotel_data']['post_id'] : '';
    $unique_id = !empty($values['tf_hotel_data']['unique_id']) ? $values['tf_hotel_data']['unique_id'] : '';
    $room_name = !empty($values['tf_hotel_data']['room_name']) ? $values['tf_hotel_data']['room_name'] : '';
    $room_selected = !empty($values['tf_hotel_data']['room']) ? $values['tf_hotel_data']['room'] : '';
    $adult = !empty($values['tf_hotel_data']['adult']) ? $values['tf_hotel_data']['adult'] : '';
    // $child = !empty($values['tf_hotel_data']['child']) ? $values['tf_hotel_data']['child'] : '';
    $tf_room = !empty($values['tf_hotel_data']['tf-room']) ? $values['tf_hotel_data']['tf-room'] : '';
    $tf_breakfast = !empty($values['tf_hotel_data']['tf-breakfast']) ? $values['tf_hotel_data']['tf-breakfast'] : '';
    $tf_half_b = !empty($values['tf_hotel_data']['tf-half-b']) ? $values['tf_hotel_data']['tf-half-b'] : '';
    $tf_full_b = !empty($values['tf_hotel_data']['tf-full-b']) ? $values['tf_hotel_data']['tf-full-b'] : '';
    $tf_inclusive = !empty($values['tf_hotel_data']['tf-inclusive']) ? $values['tf_hotel_data']['tf-inclusive'] : '';
    $tf_inclusive_gold = !empty($values['tf_hotel_data']['tf-inclusive-gold']) ? $values['tf_hotel_data']['tf-inclusive-gold'] : '';

    $check_in = !empty($values['tf_hotel_data']['check_in']) ? $values['tf_hotel_data']['check_in'] : '';
    $check_out = !empty($values['tf_hotel_data']['check_out']) ? $values['tf_hotel_data']['check_out'] : '';
    $due = !empty($values['tf_hotel_data']['due']) ? $values['tf_hotel_data']['due'] : '';
    $airport_service_type = !empty($values['tf_hotel_data']['air_serivicetype']) ? $values['tf_hotel_data']['air_serivicetype'] : null;
    $airport_fees = !empty($values['tf_hotel_data']['air_service_info']) ? $values['tf_hotel_data']['air_service_info'] : null;

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
    if ( $tf_room ) {

        $item->update_meta_data( 'Alojamiento', wc_price ( $tf_room ) );
    }
    if ( $tf_breakfast ) {

        $item->update_meta_data( 'Con Desayuno', wc_price ( $tf_breakfast ) );
    }
    if ( $tf_half_b ) {

        $item->update_meta_data( 'Con Media Pensión', wc_price ( $tf_half_b ) );
    }
    if ( $tf_full_b ) {

        $item->update_meta_data( 'Pensión Completa', wc_price ( $tf_full_b ) );
    }
    
    if ( $tf_inclusive ) {

        $item->update_meta_data( 'Todo Incluido', wc_price ( $tf_inclusive ) );
    }
    if ( $tf_inclusive_gold ) {

        $item->update_meta_data( 'Todo Incluído Plus, Lavandería incluída', wc_price ( $tf_inclusive_gold ) );
    }

    if ( $check_in ) {

        $item->update_meta_data( 'check_in', $check_in );
    }

    if ( $check_out ) {

        $item->update_meta_data( 'check_out', $check_out );
    }

    if ( ! empty( $airport_service_type ) && $airport_service_type === 'pickup' ) {
        $item->update_meta_data( 'Airport Service',  __( 'Airport Pickup', 'tourfic' ));
    }
    if ( ! empty( $airport_service_type ) && $airport_service_type === 'dropoff' ) {
        $item->update_meta_data( 'Airport Service',  __( 'Airport Dropoff', 'tourfic' ));
    }
    if ( ! empty( $airport_service_type ) && $airport_service_type === 'both' ) {
        $item->update_meta_data( 'Airport Service',  __( 'Airport Pickup & Dropoff', 'tourfic' ));
    }
    if ( ! empty( $airport_fees ) ) {
        $item->update_meta_data( 'Airport Service Fee', $values['tf_hotel_data']['air_service_info']);
    }

	if ( ! empty( $due ) ) {
		$item->update_meta_data( 'due', wc_price($due) );
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
        if(!empty($rooms)){
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
        }

        # Set whole room array to the room meta
        $meta['room'] = $new_rooms;
        # Update hotel post meta with array values
        update_post_meta( $post_id, 'tf_hotel', $meta );

    }
}
add_action( 'woocommerce_checkout_order_processed', 'tf_add_order_id_room_checkout_order_processed', 10, 3 );
?>