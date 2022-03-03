<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

CSF::createSection( $prefix, array(
    'parent' => 'tour',
    'title'  => __( 'Single Page', 'tourfic' ),
    'fields' => array(

        array(
            'type'    => 'subheading',
            'content' => __('Single Tour Settings', 'tourfic' ),
        ),

        array(
            'id'       => 't-review',
            'type'     => 'switcher',
            'title'    => __('Disable Review Section', 'tourfic' ),
            'text_on'  => __('Yes', 'tourfic' ),
            'text_off' => __('No', 'tourfic' ),
        ),

        array(
            'id'       => 't-related',
            'type'     => 'switcher',
            'title'    => __('Disable Related Tour Section', 'tourfic' ),
            'text_on'  => __('Yes', 'tourfic' ),
            'text_off' => __('No', 'tourfic' ),
        ),

    )
    
) );
?>