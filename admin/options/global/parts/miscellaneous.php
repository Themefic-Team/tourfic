<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Parent
 * 
 * Main Menu
 */
CSF::createSection( $prefix, array(
    'id'    => 'miscellaneous', 
    'title' =>  __( 'Miscellaneous', 'tourfic' ),
    'icon'  =>  'fas fa-boxes' ,   
) );

/**
 * Wishlist
 * 
 * Sub Menu
 */
CSF::createSection( $prefix, array(
    'parent'    => 'miscellaneous', 
    'title' =>  __( 'Wishlist', 'tourfic' ),
    'fields' => array(

        array(
            'type'    => 'subheading',
            'content' => __('Wishlist Settings', 'tourfic' ),
        ),

        array(
            'id'         => 'wl-for',
            'type'       => 'checkbox',
            'title'      => __('Enable Wishlist for', 'tourfic' ),
            'options'    => array(
                'li' => __('Logged in User', 'tourfic' ),
                'lo' => __('Logged out User', 'tourfic' ),
            ),
            'default'    => array( 'li', 'lo' )
        ),

        array(
            'id'         => 'wl-bt-for',
            'type'       => 'checkbox',
            'title'      => __('Show Wishlist Button on', 'tourfic' ),
            'options'    => array(
                '1' => __('Single Hotel Page', 'tourfic' ),
                '2' => __('Single Tour Page', 'tourfic' ),
            ),
            'default'    => array( '1', '2' ),
        ),

        array(
            'id'          => 'wl-page',
            'type'        => 'select',
            'title'       => __('Select Wishlist Page', 'tourfic' ),
            'placeholder' => __('Select Wishlist Page', 'tourfic' ),
            'ajax' => true,
            'options'     => 'pages',
            'query_args'  => array(
                'posts_per_page' => -1,
                'orderby' => 'post_title',
                'order' => 'ASC'
            )
        ),

    )
    
) );

/**
 * Permalink Settings
 * 
 * Sub Menu
 */
CSF::createSection( $prefix, array(
    'parent'    => 'miscellaneous', 
    'title' =>  __( 'Permalink Settings', 'tourfic' ),
    'fields' => array(

        array(
            'type'    => 'subheading',
            'content' => __('Permalink Settings', 'tourfic' ),
        ),

        array(
            'type'    => 'content',
            'content' => __('For permalink settings go to default <a href="' .get_admin_url(). 'options-permalink.php">permalink settings page</a>.', 'tourfic' ),
        ),

    )
    
) );
?>