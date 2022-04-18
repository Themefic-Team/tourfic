<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

CSF::createSection( $prefix, array(
    'parent' => 'hotel',
    'title'  => __( 'Single Page', TFD ),
    'fields' => array(

        array(
            'type'    => 'subheading',
            'content' => __('Single Hotel Settings', TFD ),
        ),

        array(
            'id'       => 'h-review',
            'type'     => 'switcher',
            'title'    => __('Disable Review Section', TFD ),
            'text_on'  => __('Yes', TFD ),
            'text_off' => __('No', TFD ),
            'default'  => false
        ),

        array(
            'id'       => 'h-share',
            'type'     => 'switcher',
            'title'    => __('Disable Share Option', TFD ),
            'text_on'  => __('Yes', TFD ),
            'text_off' => __('No', TFD ),
            'default'  => false
        ),

    )
    
) );
