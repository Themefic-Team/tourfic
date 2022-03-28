<?php
defined( 'ABSPATH' ) || exit;

/**
 * Tour booking ajax function
 * 
 * @since 2.2.0
 */
add_action( 'wp_ajax_tf_tours_booking', 'tf_tours_booking_function' );
add_action( 'wp_ajax_nopriv_tf_tours_booking', 'tf_tours_booking_function' );
function tf_tours_booking_function() {

    // Declaring errors & tour data array
    $response = array();
    $tf_tours_data = array();

    /**
     * Backend options panel data
     * 
     * @since 2.2.0
     */
    $post_id = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : '';
    $post_author = get_post_field( 'post_author', $post_id );
    $meta = get_post_meta( $post_id, 'tf_tours_option', true );
    $tour_type = !empty($meta['type']) ? $meta['type'] : '';
    
    /**
     * If fixed is selected but pro is not activated
     * 
     * show error
     * 
     * @return
     */
    if ($tour_type == 'fixed' && !defined( 'TF_PRO' )) {
        $response['errors'][] = __( 'Fixed Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
        $response['status'] = 'error';
        echo wp_json_encode( $response );
        die();
        return;
    }

    if ($tour_type == 'fixed') {

        $start_date = !empty($meta['fixed_availability']['date']['from']) ? $meta['fixed_availability']['date']['from'] : '';
        $end_date = !empty($meta['fixed_availability']['date']['to']) ? $meta['fixed_availability']['date']['to'] : '';
        $min_people = !empty($meta['fixed_availability']['min_seat']) ? $meta['fixed_availability']['min_seat'] : '';
        $max_people = !empty($meta['fixed_availability']['max_seat']) ? $meta['fixed_availability']['max_seat'] : '';       

    } elseif ($tour_type == 'continuous') {

        $custom_avail = !empty($meta['custom_avail']) ? $meta['custom_avail'] : '';

        if ($custom_avail == true) {

            $cont_custom_date = !empty($meta['cont_custom_date']) ? $meta['cont_custom_date'] : '';

        } elseif ($custom_avail == false) {

            $min_people = !empty($meta['cont_min_people']) ? $meta['cont_min_people'] : '';
            $max_people = !empty($meta['cont_max_people']) ? $meta['cont_max_people'] : '';           

        }

    } else {

    }

    /**
     * If continuous custom availability is selected but pro is not activated
     * 
     * Show error
     * 
     * @return
     */
    if ($tour_type == 'continuous' && $custom_avail == true && !defined( 'TF_PRO' )) {
        $response['errors'][] = __( 'Custom Continous Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
        $response['status'] = 'error';
        echo wp_json_encode( $response );
        die();
        return;
    }

    /**
     * All form data
     * 
     */
    // People number
    $adults = isset( $_POST['adults'] ) ? intval( sanitize_text_field( $_POST['adults'] ) ) : null;
    $children = isset( $_POST['childrens'] ) ? intval( sanitize_text_field( $_POST['childrens'] ) ) : null;
    $infant = isset( $_POST['infants'] ) ? intval( sanitize_text_field( $_POST['infants'] ) ) : null;
    $total_people = $adults + $children + $infant;
    // Tour date
    $tour_date = !empty( $_POST['check-in-out-date'] ) ? sanitize_text_field( $_POST['check-in-out-date'] ) : '';
    if ($tour_type == 'continuous') {
        $start_date = $end_date = $tour_date;
    }
    // Tour extra
    $tour_extra_total = !empty( $_POST['tour_extra_total'] ) ? sanitize_text_field( $_POST['tour_extra_total'] ) : '';
    $tour_extra_title = !empty( $_POST['tour_extra_title'] ) ? str_replace(',', ', ', sanitize_text_field( $_POST['tour_extra_title'] )) : '';

    /**
     * People number validation
     * 
     */
    if ( $tour_type == 'fixed' ) {

        $min_text = sprintf( _n( '%s person', '%s persons', $min_people, 'tourfic' ), $min_people );
        $max_text = sprintf( _n( '%s person', '%s persons', $max_people, 'tourfic' ), $max_people );

        if ( $total_people < $min_people && $min_people > 0 ) {
            $response['errors'][] = sprintf( __( 'Minimum %1$s required', 'tourfic' ), $min_text );

        } else if ( $total_people > $max_people && $max_people > 0 ) {
            $response['errors'][] = sprintf( __( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

        }

    } elseif ( $tour_type == 'continuous' && $custom_avail == false ) {

        $min_text = sprintf( _n( '%s person', '%s persons', $min_people, 'tourfic' ), $min_people );
        $max_text = sprintf( _n( '%s person', '%s persons', $max_people, 'tourfic' ), $max_people ); 

        if ( $total_people < $min_people && $min_people > 0 ) {
            $response['errors'][] = sprintf( __( 'Minimum %1$s required', 'tourfic' ), $min_text );

        } else if ( $total_people > $max_people && $max_people > 0 ) {
            $response['errors'][] = sprintf( __( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

        }

    } elseif ( $tour_type == 'continuous' && $custom_avail == true ) {

        foreach ($cont_custom_date as $item) {

            // Backend continuous date values
            $back_date_from = !empty($item["date"]["from"]) ? $item["date"]["from"] : '';
            $back_date_to = !empty($item["date"]["from"]) ? $item["date"]["to"] : '';
            $back_date_from_stt = strtotime( str_replace( '/', '-', $back_date_from ) );
            $back_date_to_stt = strtotime( str_replace( '/', '-', $back_date_to ) );
            // frontend selected date value
            $front_date = strtotime( str_replace( '/', '-', $tour_date ) );
            // Backend continuous min/max people values
            $min_people = !empty($item["min_people"]) ? $item["min_people"] : '';
            $max_people = !empty($item["max_people"]) ? $item["max_people"] : '';
            $min_text = sprintf( _n( '%s person', '%s persons', $min_people, 'tourfic' ), $min_people );
            $max_text = sprintf( _n( '%s person', '%s persons', $max_people, 'tourfic' ), $max_people );

            // Compare backend & frontend date values to show specific people number error
            if ( $front_date >= $back_date_from_stt && $front_date <= $back_date_to_stt ) {
                if ( $total_people < $min_people && $min_people > 0 ) {
                    $response['errors'][] = sprintf( __( 'Minimum %1$s required for date %2$s - %3$s', 'tourfic' ), $min_text, $back_date_from, $back_date_to );
        
                }
                if ( $total_people > $max_people && $max_people > 0 ) {
                    $response['errors'][] = sprintf( __( 'Maximum %1$s allowed for date %2$s - %3$s', 'tourfic' ), $max_text, $back_date_from, $back_date_to );
        
                }
            }

        }

    }

    /**
     * Check errors
     * 
     */
    /* Minimum days to book before departure */
    $min_days_before_book = !empty($meta['min_days_before_book']) ? $meta['min_days_before_book'] : '0';
    $min_days_before_book_text = sprintf( _n( '%s day', '%s days', $min_days_before_book, 'tourfic' ), $min_days_before_book );
    $today_stt = new DateTime(date('Y-m-d', strtotime(date('Y-m-d'))));
    $tour_date_stt = new DateTime(date('Y-m-d', strtotime($start_date)));
    $day_difference = $today_stt->diff($tour_date_stt)->days;   

    if ( $day_difference < $min_days_before_book ) {
        $response['errors'][] = sprintf( __( 'Present date to booking date required minimum %1$s gap', 'tourfic' ), $min_days_before_book_text );
    }
    if ( !$start_date ) {
        $response['errors'][] = __( 'You must select booking date', 'tourfic' );
    }
    if ( !$post_id ) {
        $response['errors'][] = __( 'Unknown Error! Please try again.', 'tourfic' );
    }

    // Get product title from post id
    $post_title = get_the_title( $post_id );

    /**
     * Create tours as WooCommerce product
     * 
     * Password protected
     */
    // Arguments
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

    // Check if product already exists
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

    /**
     * Seasional price
     * 
     * @author KK
     */
    $tour                                = strtotime($tour_date);
    $seasional_price                     = array_values(array_filter($meta['cont_custom_date'], function ($value) use ($tour) {
        $seasion_start = strtotime($value['date']['from']);
        $seasion_end   = strtotime($value['date']['to']);
        return $seasion_start <= $tour && $seasion_end >= $tour;
    }));
    if ($meta['type'] === 'continuous' && !empty($meta['cont_custom_date']) && !empty($seasional_price)) {
        $pricing_rule   = $seasional_price[0]['pricing'];
        $group_price    = $seasional_price[0]['group_price'];
        $adult_price    = $seasional_price[0]['adult_price'];
        $children_price = $seasional_price[0]['child_price'];
        $infant_price   = $seasional_price[0]['infant_price'];
    } else {
        $pricing_rule   = $meta['pricing'];
        $group_price    = $meta['group_price'];
        $adult_price    = $meta['adult_price'];
        $children_price = $meta['child_price'];
        $infant_price   = $meta['infant_price'];
    }

    if ($tour_type == 'continuous' && empty($tour_time)) {

        if (!empty($meta['allowed_time']) && empty($meta['cont_custom_date']) && empty($seasional_price['allowed_time'])) {
            $response['errors'][]  = __('Please select time', 'tourfic');
        }
        if (!empty($meta['cont_custom_date']) && !empty($seasional_price['allowed_time']) && empty($meta['allowed_time'])) {
            $response['errors'][]  = __('Please select time', 'tourfic');
        }
    }

    if ($adults > 0 && empty($adult_price)) $response['errors'][]               = __('Adult price is blank!', 'tourfic');
    if ($children > 0 && empty($children_price)) $response['errors'][]          = __('Childern price is blank!', 'tourfic');
    if ($infant > 0 && empty($infant_price)) $response['errors'][]              = __('Infant price is blank!', 'tourfic');
    if ($infant > 0 && !empty($infant_price) && !$adults) $response['errors'][] = __('Infant without adults is not allowed!', 'tourfic');
    // End of seasional price

    /**
     * If no errors then process
     * 
     * Store custom data in array
     * Add to cart with custom data
     */
    if (!array_key_exists('errors', $response) || count($response['errors']) == 0) {

        $tf_tours_data['tf_tours_data']['order_type'] = 'tour';
        $tf_tours_data['tf_tours_data']['post_author'] = $post_author;
        $tf_tours_data['tf_tours_data']['tour_type'] = $tour_type;
        $tf_tours_data['tf_tours_data']['tour_id'] = $post_id;

        $tf_tours_data['tf_tours_data']['adults'] = $adults;
        $tf_tours_data['tf_tours_data']['childrens'] = $children;
        $tf_tours_data['tf_tours_data']['infants'] = $infant;
        $tf_tours_data['tf_tours_data']['start_date'] = $start_date;
        $tf_tours_data['tf_tours_data']['end_date'] = $end_date;
        $tf_tours_data['tf_tours_data']['tour_date'] = $tour_date;

        $tf_tours_data['tf_tours_data']['tour_extra_total'] = $tour_extra_total;
        $tf_tours_data['tf_tours_data']['tour_extra_title'] = $tour_extra_title;

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

        // Add product to cart with the custom cart item data
        WC()->cart->add_to_cart( $product_id, 1, '0', array(), $tf_tours_data );

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
 * Set tour price in WooCommerce
 */
add_action( 'woocommerce_before_calculate_totals', 'tf_tours_set_order_price', 30, 1 );
function tf_tours_set_order_price( $cart ) {
    if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
        return;
    }

    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
        return;
    }

    foreach ( $cart->get_cart() as $cart_item ) {

        if ( isset( $cart_item['tf_tours_data']['price'] ) && !empty($cart_item['tf_tours_data']['tour_extra_total']) ) {
            $cart_item['data']->set_price( $cart_item['tf_tours_data']['price'] + $cart_item['tf_tours_data']['tour_extra_total'] );
        } elseif ( isset( $cart_item['tf_tours_data']['price'] ) && empty($cart_item['tf_tours_data']['tour_extra_total']) ) {
            $cart_item['data']->set_price( $cart_item['tf_tours_data']['price'] );
        }
    }
}

/**
 * Show custom data in Cart & checkout
 */
add_filter( 'woocommerce_get_item_data', 'tf_tours_cart_item_custom_data', 10, 2 );
function tf_tours_cart_item_custom_data( $item_data, $cart_item ) {

    // Assigning data into variables
    $tour_type = !empty($cart_item['tf_tours_data']['tour_type']) ? $cart_item['tf_tours_data']['tour_type'] : '';
    $adults_number = !empty($cart_item['tf_tours_data']['adults']) ? $cart_item['tf_tours_data']['adults'] : '';
    $childrens_number = !empty($cart_item['tf_tours_data']['childrens']) ? $cart_item['tf_tours_data']['childrens'] : '';
    $infants_number = !empty($cart_item['tf_tours_data']['infants']) ? $cart_item['tf_tours_data']['infants'] : '';
    $start_date = !empty($cart_item['tf_tours_data']['start_date']) ? $cart_item['tf_tours_data']['start_date'] : '';
    $end_date = !empty($cart_item['tf_tours_data']['end_date']) ? $cart_item['tf_tours_data']['end_date'] : '';
    $tour_date = !empty($cart_item['tf_tours_data']['tour_date']) ? $cart_item['tf_tours_data']['tour_date'] : '';
    $tour_extra = !empty($cart_item['tf_tours_data']['tour_extra_title']) ? $cart_item['tf_tours_data']['tour_extra_title'] : '';

    /**
     * Show data in cart & checkout
     */
    // Adults
    if ( $adults_number && $adults_number > 0 ) {
        $item_data[] = array(
            'key'   => __( 'Adults', 'tourfic' ),
            'value' => $adults_number,
        );
    }
    // Childrens
    if ( $childrens_number && $childrens_number > 0 ) {
        $item_data[] = array(
            'key'   => __( 'Children', 'tourfic' ),
            'value' => $childrens_number,
        );
    }
    // Infants
    if ( $infants_number && $infants_number > 0 ) {
        $item_data[] = array(
            'key'   => __( 'Infant', 'tourfic' ),
            'value' => $infants_number,
        );
    }
    // Tour date, departure date
    if ( !empty($tour_type) && $tour_type == 'fixed') {
        if ( $start_date && $end_date ) {
            $item_data[] = array(
                'key'   => __( 'Tour Date', 'tourfic' ),
                'value' => $start_date. ' - ' .$end_date,
            );
        }
    } elseif ( !empty($tour_type) && $tour_type == 'continuous') {
        if ( $tour_date ) {
            $item_data[] = array(
                'key'   => __( 'Tour Date', 'tourfic' ),
                'value' => date("F j, Y", strtotime($tour_date)),
            );
        }
    }
    // Tour extras
    if ( $tour_extra ) {
        $item_data[] = array(
            'key'   => __( 'Tour Extra: ', 'tourfic' ),
            'value' => $tour_extra,
        );
    }

    return $item_data;
}

/**
 * Show custom data in order details
 */
add_action( 'woocommerce_checkout_create_order_line_item', 'tf_tour_custom_order_data', 10, 4 );
function tf_tour_custom_order_data( $item, $cart_item_key, $values, $order ) {

    // Assigning data into variables
    $order_type = $values['tf_tours_data']['order_type'];
    $post_author = $values['tf_tours_data']['post_author'];
    $tour_id = !empty($values['tf_tours_data']['tour_id']) ? $values['tf_tours_data']['tour_id'] : '';
    $tour_type = !empty($values['tf_tours_data']['tour_type']) ? $values['tf_tours_data']['tour_type'] : '';
    $adults_number = !empty($values['tf_tours_data']['adults']) ? $values['tf_tours_data']['adults'] : '';
    $childrens_number = !empty($values['tf_tours_data']['childrens']) ? $values['tf_tours_data']['childrens'] : '';
    $infants_number = !empty($values['tf_tours_data']['infants']) ? $values['tf_tours_data']['infants'] : '';
    $start_date = !empty($values['tf_tours_data']['start_date']) ? $values['tf_tours_data']['start_date'] : '';
    $end_date = !empty($values['tf_tours_data']['end_date']) ? $values['tf_tours_data']['end_date'] : '';
    $tour_date = !empty($values['tf_tours_data']['tour_date']) ? $values['tf_tours_data']['tour_date'] : '';
    $tour_extra = !empty($values['tf_tours_data']['tour_extra_title']) ? $values['tf_tours_data']['tour_extra_title'] : '';

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

    if ( $tour_id ) {
        $item->update_meta_data( '_tour_id', $tour_id );
    }

    if ( $adults_number && $adults_number > 0 ) {
        $item->update_meta_data( 'Adults', $adults_number );
    }

    if ( $childrens_number && $childrens_number > 0 ) {
        $item->update_meta_data( 'Children', $childrens_number );
    }

    if ( $infants_number && $infants_number > 0 ) {
        $item->update_meta_data( 'Infants', $infants_number );
    }

    if ( $tour_type && $tour_type == 'fixed') {
        if ( $start_date && $end_date ) {
            $item->update_meta_data( 'Tour Date', $start_date. ' - ' .$end_date );
        }
    } elseif ( $tour_type && $tour_type == 'continuous') {
        if ( $tour_date ) {
            $item->update_meta_data( 'Tour Date', date("F j, Y", strtotime($tour_date)) );
        }
    }

    if ( $tour_extra ) {
        $item->update_meta_data( 'Tour Extra', $tour_extra );
    }

}
?>