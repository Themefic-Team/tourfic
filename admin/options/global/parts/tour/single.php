<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

CSF::createSection( $prefix, array(
    'parent' => 'tour',
    'title'  => __( 'Single Page', TFD ),
    'fields' => array(

        array(
            'type'    => 'subheading',
            'content' => __('Single Tour Settings', TFD ),
        ),

        array(
            'id'       => 't-review',
            'type'     => 'switcher',
            'title'    => __('Disable Review Section', TFD ),
            'text_on'  => __('Yes', TFD ),
            'text_off' => __('No', TFD ),
        ),

        array(
            'id'       => 't-related',
            'type'     => 'switcher',
            'title'    => __('Disable Related Tour Section', TFD ),
            'text_on'  => __('Yes', TFD ),
            'text_off' => __('No', TFD ),
        ),

    )
    
) );
