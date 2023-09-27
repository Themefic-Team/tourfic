<?php 
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