<?php 
/**
 * Ajax tour booking Details Update
 *
 * tf_visitor_details_edit
 */

add_action( 'wp_ajax_tf_visitor_details_edit', 'tf_visitor_details_edit_function' );
function tf_visitor_details_edit_function() {
    // Order Id
    $tf_order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : "";
    // Visitor Details
    $tf_visitor_details = !empty($_POST['traveller']) ? $_POST['traveller'] : "";

    global $wpdb;
    $tf_order = $wpdb->get_row( $wpdb->prepare( "SELECT id,order_details FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $tf_order_id ) ) );
    $tf_order_details = json_decode($tf_order->order_details);
    $tf_order_details->visitor_details = json_encode($tf_visitor_details);

    // Visitor Details Update
    if(!empty($tf_order)){
        $wpdb->query(
            $wpdb->prepare("UPDATE {$wpdb->prefix}tf_order_data SET order_details=%s WHERE id=%s", json_encode($tf_order_details), sanitize_key($tf_order_id))
        );
    }
    die();
}

/**
 * Ajax Checkinout Status Update
 *
 * tf_checkinout_details_edit
 */

add_action( 'wp_ajax_tf_checkinout_details_edit', 'tf_checkinout_details_edit_function' );
function tf_checkinout_details_edit_function() {
    // Order Id
    $tf_order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : "";
    // Checkinout Value
    $tf_checkinout = !empty($_POST['checkinout']) ? $_POST['checkinout'] : "";

    /**
     * Get current logged in user
    */
    $current_user = wp_get_current_user();
    // get user id
    $current_user_id = $current_user->ID;
    $ft_checkinout_by = array(
        'userid' => $current_user_id,
        'time'   => date("d F, Y h:i:s a")
    );

    global $wpdb;
    $tf_order = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $tf_order_id ) ) );

    // Checkinout Status Update into Database
    if(!empty($tf_order)){
        $wpdb->query(
            $wpdb->prepare("UPDATE {$wpdb->prefix}tf_order_data SET checkinout=%s, checkinout_by=%s WHERE id=%s", sanitize_title( $tf_checkinout ), json_encode( $ft_checkinout_by ), sanitize_key($tf_order_id))
        );
    }
    die();
}

/**
 * Ajax Order Status Update
 *
 * tf_order_status_edit
 */

 add_action( 'wp_ajax_tf_order_status_edit', 'tf_order_status_edit_function' );
 function tf_order_status_edit_function() {
    // Order Id
    $tf_order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : "";
    // status Value
    $tf_status = !empty($_POST['status']) ? $_POST['status'] : "";

    global $wpdb;
    $tf_order = $wpdb->get_row( $wpdb->prepare( "SELECT id, order_id FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $tf_order_id ) ) );

    // Order Status Update into Database
    if(!empty($tf_order)){
        $wpdb->query(
        $wpdb->prepare("UPDATE {$wpdb->prefix}tf_order_data SET ostatus=%s WHERE id=%s", sanitize_title( $tf_status ), sanitize_key($tf_order_id))
        );

        // Woocommerce status
        $order = wc_get_order($tf_order->order_id);
        if (!empty($order)) {
            $order->update_status( sanitize_key($tf_status), '', true );
        }
    }
    
    die();
}

/**
 * Ajax Order Bulk Action Update
 *
 * tf_order_bulk_action_edit
 */

 add_action( 'wp_ajax_tf_order_bulk_action_edit', 'tf_order_bulk_action_edit_function' );
 function tf_order_bulk_action_edit_function() {
    // Order Id
    $tf_orders = !empty($_POST['orders']) ? $_POST['orders'] : "";
    // status Value
    $tf_status = !empty($_POST['status']) ? $_POST['status'] : "";

    global $wpdb;
    foreach($tf_orders as $order){
        if("trash"==$tf_status){
            $wpdb->query(
                $wpdb->prepare( "DELETE FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $order ) )
            );
        }else{
            $tf_single_order = $wpdb->get_row( $wpdb->prepare( "SELECT id, order_id FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $order ) ) );

            // Order Status Update into Database
            if(!empty($tf_single_order)){
                $wpdb->query(
                $wpdb->prepare("UPDATE {$wpdb->prefix}tf_order_data SET ostatus=%s WHERE id=%s", sanitize_title( $tf_status ), sanitize_key($order))
                );

                // Woocommerce status
                $order = wc_get_order($tf_single_order->order_id);
                if (!empty($order)) {
                    $order->update_status( sanitize_key($tf_status), '', true );
                }
            }
        }
    }
    die();
}

/**
 * Booking Details Pagination
 *
 * tf_booking_details_pagination
 */
if ( ! function_exists( 'tf_booking_details_pagination' ) ) {
    function tf_booking_details_pagination($page){
        $currentURL = home_url($_SERVER['REQUEST_URI']);
        $BaseURL = strtok($currentURL, '?');
        $queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        
        parse_str($queryString, $currentURLParams);

        if (array_key_exists('paged', $currentURLParams)) {
            $currentURLParams['paged'] = $page;
            $updatedQuery = http_build_query($currentURLParams);
            return $updatedUrl = $BaseURL . '?' . $updatedQuery;
        } else {
            return $updatedUrl = $currentURL . '&paged=' . $page;
        }
    }
}