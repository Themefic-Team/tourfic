<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

CSF::createSection( $prefix, array(
    'parent' => 'hotel',
    'title'  => __( 'Single Page', 'tourfic' ),
    'fields' => array(

        array(
            'type'    => 'subheading',
            'content' => __('Single Hotel Settings', 'tourfic' ),
        ),

        array(
            'id'       => 'h-review',
            'type'     => 'switcher',
            'title'    => __('Disable Review Section', 'tourfic' ),
            'text_on'  => __('Yes', 'tourfic' ),
            'text_off' => __('No', 'tourfic' ),
            'default'  => false
        ),

        array(
            'id'       => 'h-share',
            'type'     => 'switcher',
            'title'    => __('Disable Share Option', 'tourfic' ),
            'text_on'  => __('Yes', 'tourfic' ),
            'text_off' => __('No', 'tourfic' ),
            'default'  => false
        ),

    )
    
) );
?>