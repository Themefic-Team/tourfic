<?php

// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

  $prefix = 'tourfic_opt';

  CSF::createOptions( $prefix, array(
    'framework_title'         =>   __( 'Tourfic Settings <small>by <a style="color: #bfbfbf;text-decoration:none;" href="https://themefic.com" target="_blank">Themefic</a></small>', 'tourfic' ),
    'menu_title'              =>   __( 'Tourfic Settings', 'tourfic' ),
    'menu_slug'               =>   'tourfic',
    'menu_icon'               =>   'dashicons-palmtree',
    'footer_credit'           =>   __( '<em>Enjoyed <strong>Tourfic</strong>? Please leave us a <a style="color:#e9570a;" href="https://wordpress.org/support/plugin/tourfic/reviews/?filter=5/#new-post" target="_blank">★★★★★</a> rating. We really appreciate your support!</em>', 'tourfic' ),
    'menu_position'           =>   25,
    'show_sub_menu'           =>   true,
    'theme'                   =>   'dark',   
  ) );

  
    /**
     * General Options
     * 
     * Main menu
     */
    if ( file_exists( TF_OPTIONS_PATH . 'global/parts/general.php' ) ) {
        require_once TF_OPTIONS_PATH . 'global/parts/general.php';
    } else {
        tf_file_missing(TF_OPTIONS_PATH . 'global/parts/general.php');
    }

    /**
     * Hotel Parent
     * 
     * Main menu
     */
    CSF::createSection( $prefix, array(
        'id'    => 'hotel', // Set a unique slug-like ID
        'title' => __( 'Hotel Options', 'instantio' ),
        'icon'  => 'fas fa-hotel',
    ) );
    
    // Single hotel
    if ( file_exists( TF_OPTIONS_PATH . 'global/parts/hotel/single.php' ) ) {
        require_once TF_OPTIONS_PATH . 'global/parts/hotel/single.php';
    } else {
        tf_file_missing(TF_OPTIONS_PATH . 'global/parts/hotel/single.php');
    }

    /**
     * Tour Parent
     * 
     * Main menu
     */
    CSF::createSection( $prefix, array(
        'id'    => 'tour', // Set a unique slug-like ID
        'title' => __( 'Tour Options', 'instantio' ),
        'icon'  => 'fas fa-umbrella-beach',
    ) );

    // Single tour
    if ( file_exists( TF_OPTIONS_PATH . 'global/parts/tour/single.php' ) ) {
        require_once TF_OPTIONS_PATH . 'global/parts/tour/single.php';
    } else {
        tf_file_missing(TF_OPTIONS_PATH . 'global/parts/tour/single.php');
    }

    /**
     * Multi Vendor Options
     * 
     * Main menu
     */
    if ( file_exists( TF_OPTIONS_PATH . 'global/parts/vendor.php' ) ) {
        require_once TF_OPTIONS_PATH . 'global/parts/vendor.php';
    } else {
        tf_file_missing(TF_OPTIONS_PATH . 'global/parts/vendor.php');
    }

    /**
     * Search Options
     * 
     * Main menu
     */
    if ( file_exists( TF_OPTIONS_PATH . 'global/parts/search.php' ) ) {
        require_once TF_OPTIONS_PATH . 'global/parts/search.php';
    } else {
        tf_file_missing(TF_OPTIONS_PATH . 'global/parts/search.php');
    }

    /**
     * Miscellaneous Options
     * 
     * Main menu
     */
    if ( file_exists( TF_OPTIONS_PATH . 'global/parts/miscellaneous.php' ) ) {
        require_once TF_OPTIONS_PATH . 'global/parts/miscellaneous.php';
    } else {
        tf_file_missing(TF_OPTIONS_PATH . 'global/parts/miscellaneous.php');
    }

    /**
     * Import/Export
     * 
     * Main menu
     */
    CSF::createSection( $prefix, array(
        'title'       => __('Import/Export', 'tourfic'),
        'icon'        => 'fas fa-hdd',
        'fields'      => array(   
            array(
                'type' => 'backup',
            ),   
        )
    ) );


}