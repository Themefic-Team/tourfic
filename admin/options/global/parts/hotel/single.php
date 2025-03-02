<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

$badge_pro = '<div class="tf-csf-badge"><span class="tf-pro">' .__("Pro Feature", "tourfic"). '</span></div>';

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

        array(
            'id'       => 'h-enquiry-email',
            'class'    => 'tf-csf-disable tf-csf-pro',
            'type'     => 'text',
            'title'    => __('Enquiry Email', 'tourfic' ), 
            'subtitle'   => __( $badge_pro, 'tourfic' ),

        ),

    )
    
) );
?>