<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

$badge_up = '<div class="tf-csf-badge"><span class="tf-upcoming">' .__("Upcoming", "tourfic"). '</span></div>';
$badge_pro = '<div class="tf-csf-badge"><span class="tf-pro">' .__("Pro Feature", "tourfic"). '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' .__("Upcoming", "tourfic"). '</span><span class="tf-pro">' .__("Pro Feature", "tourfic"). '</span></div>';

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
 * Google Map
 * 
 * Sub Menu
 */
CSF::createSection( $prefix, array(
    'parent'    => 'miscellaneous', 
    'title' =>  __( 'Map Settings', 'tourfic' ),
    'icon'   => 'fas fa-globe',
    'fields' => array(

        array(
            'type'    => 'subheading',
            'content' => __('Map Settings', 'tourfic' ),
        ),

        array(
            'id'          => 'google-page-option',
            'class'    => 'tf-csf-disable tf-csf-pro',
            'type'        => 'select',
            'title'       => __('Select Map', 'tourfic' ),
            'subtitle'   => __( $badge_pro, 'tourfic' ),
            'options'     => array(
                'default'  => 'Default Map',
                'googlemap'  => 'Google Map',
            ),
            'default'     => 'default'
        ),
        array(
			'id'         => 'tf-googlemapapi',
            'class'    => 'tf-csf-disable tf-csf-pro',
			'type'       => 'text',
			'title'      => __('Google Map API Key', 'tourfic' ),
            'dependency' => array(
                array( 'google-page-option', '==', 'googlemap' ),
            ),
		),

    )
    
) );


/**
 * Wishlist
 * 
 * Sub Menu
 */
CSF::createSection( $prefix, array(
    'parent'    => 'miscellaneous', 
    'title' =>  __( 'Wishlist', 'tourfic' ),
    'icon'   => 'fas fa-heart',
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
    'icon'   => 'fas fa-link',
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

/**
 * Review
 * 
 * Sub Menu
 */
CSF::createSection($prefix, array(
    'parent'    => 'miscellaneous',
    'title' =>  __('Review', 'tourfic'),
    'icon'   => 'fas fa-star',
    'fields' => array(

        array(
            'type'    => 'subheading',
            'content' => __('Review Settings', 'tourfic'),
        ),
        // array(
        //     'id'       => 'r-customer',
        //     'class'    => 'tf-csf-disable tf-csf-pro',
        //     'type'     => 'switcher',
        //     'title'    => __('Enable Review Only for Customers', 'tourfic'),
        //     'subtitle' => __('Customers only who have complete orders' . $badge_up_pro, 'tourfic'),
        //     'text_on'  => __('Yes', 'tourfic'),
        //     'text_off' => __('No', 'tourfic'),
        //     'default'  => false
        // ),

        array(
            'id'      => 'r-for',
            'type'    => 'checkbox',
            'title'   => __('Enable Review for', 'tourfic'),
            'options' => array(
                'li' => __('Logged in User', 'tourfic'),
                '' => __('Log out User (Pro)', 'tourfic'),
            ),
            'default'    => array('li'),
        ),

        array(
            'id'       => 'r-auto-publish',
            'type'     => 'switcher',
            'title'    => __('Auto Publish Review', 'tourfic'),
            'subtitle' => __('By default review will be pending and waiting for admin approval', 'tourfic'),
            'text_on'  => __('Yes', 'tourfic'),
            'text_off' => __('No', 'tourfic'),
            'default'  => false
        ),

        array(
            'id'      => 'r-base',
            'type'    => 'radio',
            'title'   => __('Calculate Review Based on', 'tourfic'),
            'options' => array(
                '5'  => __('5', 'tourfic'),
                '10' => __('10', 'tourfic'),
            ),
            'default'    => '5',
        ),

        array(
            'id'       => 'r-hotel',
            'class'    => 'disable-sortable tf-csf-disable tf-csf-pro',
            'type'     => 'repeater',
            'title'    => __('Review Fields for Hotels', 'tourfic'),
            'subtitle' => __('Maximum 10 fields allowed' . $badge_pro, 'tourfic'),
            'max'      => '6',
            'fields'   => array(

                array(
                    'id'    => 'r-field-type',
                    'type'  => 'text',
                    'title' => __('Review for', 'tourfic'),
                ),

            ),
            'default'   => array(
                array(
                    'r-field-type' => __('Staff', 'tourfic'),
                ),
                array(
                    'r-field-type' => __('Facilities', 'tourfic'),
                ),
                array(
                    'r-field-type' => __('Cleanliness', 'tourfic'),
                ),
                array(
                    'r-field-type' => __('Comfort', 'tourfic'),
                ),
                array(
                    'r-field-type' => __('Value for money', 'tourfic'),
                ),
                array(
                    'r-field-type' => __('Location', 'tourfic'),
                ),
            )
        ),
        array(
            'id'       => 'r-tour',
            'class'    => 'disable-sortable tf-csf-disable tf-csf-pro',
            'type'     => 'repeater',
            'title'    => __('Review Fields for Tours', 'tourfic'),
            'subtitle' => __('Maximum 10 fields allowed' . $badge_pro, 'tourfic'),
            'max'      => '6',
            'fields'   => array(

                array(
                    'id'    => 'r-field-type',
                    'type'  => 'text',
                    'title' => __('Review for', 'tourfic'),
                ),

            ),
            'default'   => array(
                array(
                    'r-field-type' => __('Guide', 'tourfic'),
                ),
                array(
                    'r-field-type' => __('Transportation', 'tourfic'),
                ),
                array(
                    'r-field-type' => __('Value for money', 'tourfic'),
                ),
                array(
                    'r-field-type' => __('Safety', 'tourfic'),
                ),
            )
        ),

        array(
            'type'     => 'callback',
            'function' => 'tf_delete_old_review_fields_button',
        ),
        array(
            'type'     => 'callback',
            'function' => 'tf_delete_old_complete_review_button',
        ),

    )

));

/**
 * optimization Settings
 * 
 * Sub Menu
 */
CSF::createSection( $prefix, array(
    'parent' => 'miscellaneous',
    'title'  => __( 'Optimization', 'tourfic' ),
    'icon'   => 'fas fa-bolt',
    'fields' => array(

        array(
            'type'    => 'subheading',
            'content' => __('Minification Settings', 'tourfic' ),
        ),

        array(
            'id'         => 'css_min',
            'class'    => 'tf-csf-disable tf-csf-pro',
            'type'       => 'switcher',
            'title'      => __('Minify CSS', 'tourfic' ),
            'subtitle'   => __('Enable/disable Tourfic CSS minification' . $badge_pro, 'tourfic' ),
            'text_on'    => __('Enabled', 'tourfic' ),
            'text_off'   => __('Disabled', 'tourfic' ),
            'text_width' => 100,
            'default'    => false
        ),

        array(
            'id'         => 'js_min',
            'class'    => 'tf-csf-disable tf-csf-pro',
            'type'       => 'switcher',
            'title'      => __('Minify JS', 'tourfic' ),
            'subtitle'   => __('Enable/disable Tourfic JS minification' . $badge_pro, 'tourfic' ),
            'text_on'    => __('Enabled', 'tourfic' ),
            'text_off'   => __('Disabled', 'tourfic' ),
            'text_width' => 100,
            'default'    => false
        ),

        array(
            'type'    => 'subheading',
            'content' => __('CDN Settings', 'tourfic' ),
        ),

        array(
            'id'         => 'ftpr_cdn',
            'class'    => 'tf-csf-disable tf-csf-pro',
            'type'       => 'switcher',
            'title'      => __('Flatpickr CDN', 'tourfic' ),
            'subtitle'   => __('Enable/disable cloudflare CDN for Flatpickr CSS & JS' . $badge_pro, 'tourfic' ),
            'text_on'    => __('Enabled', 'tourfic' ),
            'text_off'   => __('Disabled', 'tourfic' ),
            'text_width' => 100
        ),

        array(
            'id'         => 'fnybx_cdn',
            'class'    => 'tf-csf-disable tf-csf-pro',
            'type'       => 'switcher',
            'title'      => __('Fancybox CDN', 'tourfic' ),
            'subtitle'   => __('Enable/disable cloudflare CDN for Fancybox CSS & JS' . $badge_pro, 'tourfic' ),
            'text_on'    => __('Enabled', 'tourfic' ),
            'text_off'   => __('Disabled', 'tourfic' ),
            'text_width' => 100
        ),

        array(
            'id'         => 'slick_cdn',
            'class'    => 'tf-csf-disable tf-csf-pro',
            'type'       => 'switcher',
            'title'      => __('Slick CDN', 'tourfic' ),
            'subtitle'   => __('Enable/disable cloudflare CDN for Slick CSS & JS' . $badge_pro, 'tourfic' ),
            'text_on'    => __('Enabled', 'tourfic' ),
            'text_off'   => __('Disabled', 'tourfic' ),
            'text_width' => 100
        ),

        array(
            'id'         => 'fa_cdn',
            'class'    => 'tf-csf-disable tf-csf-pro',
            'type'       => 'switcher',
            'title'      => __('Font Awesome CDN', 'tourfic' ),
            'subtitle'   => __('Enable/disable cloudflare CDN for Font Awesome CSS' . $badge_pro, 'tourfic' ),
            'text_on'    => __('Enabled', 'tourfic' ),
            'text_off'   => __('Disabled', 'tourfic' ),
            'text_width' => 100
        ),

    )
    
) );
?>