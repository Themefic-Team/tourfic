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
    'title' =>  __( 'Miscellaneous', TFD ),
    'icon'  =>  'fas fa-boxes' ,   
) );

/**
 * Wishlist
 * 
 * Sub Menu
 */
CSF::createSection( $prefix, array(
    'parent'    => 'miscellaneous', 
    'title' =>  __( 'Wishlist', TFD ),
    'fields' => array(

        array(
            'type'    => 'subheading',
            'content' => __('Wishlist Settings', TFD ),
        ),

        array(
            'id'         => 'wl-for',
            'type'       => 'checkbox',
            'title'      => __('Enable Wishlist for', TFD ),
            'options'    => array(
                'li' => __('Logged in User', TFD ),
                'lo' => __('Logged out User', TFD ),
            ),
            'default'    => array( 'li', 'lo' )
        ),

        array(
            'id'         => 'wl-bt-for',
            'type'       => 'checkbox',
            'title'      => __('Show Wishlist Button on', TFD ),
            'options'    => array(
                '1' => __('Single Hotel Page', TFD ),
                '2' => __('Single Tour Page', TFD ),
            ),
            'default'    => array( '1', '2' ),
        ),

        array(
            'id'          => 'wl-page',
            'type'        => 'select',
            'title'       => __('Select Wishlist Page', TFD ),
            'placeholder' => __('Select Wishlist Page', TFD ),
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
    'title' =>  __( 'Permalink Settings', TFD ),
    'fields' => array(

        array(
            'type'    => 'subheading',
            'content' => __('Permalink Settings', TFD ),
        ),

        array(
            'type'    => 'content',
            'content' => __('For permalink settings go to default <a href="' .get_admin_url(). 'options-permalink.php">permalink settings page</a>.', TFD ),
        ),

    )
    
) );
