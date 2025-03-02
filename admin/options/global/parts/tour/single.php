<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

$badge_pro = '<div class="tf-csf-badge"><span class="tf-pro">' .__("Pro Feature", "tourfic"). '</span></div>';

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

        array(
            'id'       => 't-enquiry-email',
            'class'    => 'tf-csf-disable tf-csf-pro',
            'type'     => 'text',
            'title'    => __('Enquiry Email', 'tourfic' ), 
            'subtitle'   => __( $badge_pro, 'tourfic' ),
        ),

    )
    
) );
?>