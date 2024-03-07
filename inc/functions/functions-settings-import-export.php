<?php 
//don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Import settings
 */
add_action( 'wp_ajax_tf_import', 'tf_import_callback' );
function tf_import_callback(){

    if( !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'updates' ) ){
        return;
    }

    $current_user = wp_get_current_user();
    // get user id
    $current_user_id = $current_user->ID;
    // get user role
    $current_user_role = $current_user->roles[0];

    // if is not desired user role die
    if ( $current_user_role == 'administrator' ) {
    } else {
        wp_die( __( 'You are not allowed to import', 'tourfic' ) );
    }

    $imported_data = maybe_unserialize( stripslashes( $_POST['tf_import_option'] ) );
    
    do_action( 'tf_setting_import_before_save', $imported_data );

    update_option( 'tf_settings', $imported_data );
    wp_send_json_success($imported_data);
    die();
}