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

    global $wpdb;
    $tf_order = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}tf_order_data WHERE id = %s",sanitize_key( $tf_order_id ) ) );

    // Checkinout Status Update into Database
    if(!empty($tf_order)){
        $wpdb->query(
            $wpdb->prepare("UPDATE {$wpdb->prefix}tf_order_data SET checkinout=%s, checkinout_by=%s WHERE id=%s", sanitize_title( $tf_checkinout ), sanitize_key( $current_user_id ), sanitize_key($tf_order_id))
        );
    }
    die();
}