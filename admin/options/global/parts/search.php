<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

$badge_pro = '<div class="tf-csf-badge"><span class="tf-pro">' .__("Pro Feature", "tourfic"). '</span></div>';

CSF::createSection( $prefix, array(
    'id'     => 'search',
    'title'  => __( 'Search', 'tourfic' ),
    'icon'   => 'fas fa-search',
    'fields' => array(

        array(
            'id'          => 'search-result-page',
            'type'        => 'select',
            'placeholder' => __('Select a page', 'tourfic' ),
            'chosen'      => true,
            'ajax'        => true,
            'title'       => __( 'Select Search Result Page', 'tourfic' ),
            'desc'        => __( 'Page template: <code>Tourfic - Search Result</code> must be selected', 'tourfic' ),
            'options'     => 'pages',
            
        ),

        array(
            'id'       => 'date_hotel_search',
            'class'    => 'tf-csf-disable tf-csf-pro',
            'type'     => 'switcher',
            'title'    => __('Date Required in Hotel Search', 'tourfic'),
            'subtitle' => $badge_pro,
            'text_on'  => __('Yes', 'tourfic'),
            'text_off' => __('No', 'tourfic'),
        ),
          
        array(
            'id'       => 'date_tour_search',
            'class'    => 'tf-csf-disable tf-csf-pro',
            'type'     => 'switcher',
            'title'    => __('Date Required in Tour Search', 'tourfic'),
            'subtitle' => $badge_pro,
            'text_on'  => __('Yes', 'tourfic'),
            'text_off' => __('No', 'tourfic'),
        ),

    )
) );
?>