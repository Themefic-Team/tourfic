<?php 
//don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Import settings
 */
add_action( 'wp_ajax_tf_import', 'tf_import_callback' );
function tf_import_callback(){

    $imported_data = stripslashes( $_POST['tf_import_option'] );
    $imported_data = unserialize( $imported_data );
    update_option( 'tf_settings', $imported_data );
    wp_send_json_success($imported_data);
    die();
}