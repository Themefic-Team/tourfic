<?php 
//don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Import settings
 */
add_action( 'wp_ajax_tf_import', 'tf_import_callback' );
function tf_import_callback(){

    $imported_data = $_POST['tf_import_option'];
    update_option( 'tf_settings', json_decode( $imported_data, true ) );
    wp_send_json_success('Import completed successfully.');
    die();
}



