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
    'id'    => 'design-panel', 
    'title' =>  __( 'Design Panel', 'tourfic' ),
    'icon'  =>  'fas fa-palette' ,   
) );

/**
 * Global
 * 
 * Sub Menu
 */
CSF::createSection( $prefix, array(
    'parent'    => 'design-panel', 
    'title' =>  __( 'Global', 'tourfic' ),
    'icon'   => 'fas fa-cogs',
    'fields' => array(
        array(
            'type'    => 'subheading',
            'content' => __('Global Settings', 'tourfic' ),
        ),
        array(
          'type'    => 'submessage',
          'style'   => 'info',
          'content' => "To ensure maximum compatiblity with your theme, all Heading (h1-h6), Paragraph & Link's Color-Font Styles are not controlled by Tourfic. Those need to be edited using your Theme's option Panel.",
        ),
        array(
            'id'        => 'tourfic-button-color',
            'type'      => 'color_group',
            'title'    => __( 'Button Color', 'tourfic' ),
            'subtitle' => __( 'Button Color of Tourfic (e.g. Blue color on our Demo)', 'tourfic' ),
            'options'   => array(
              'regular' => __('Regular', 'tourfic'),
              'hover' => __('Hover', 'tourfic'),
            )
        ),
        array(
          'id'        => 'tourfic-button-bg-color',
          'type'      => 'color_group',
          'title'    => __( 'Button Background Color', 'tourfic' ),
          'subtitle' => __( 'Button Background Color of Tourfic ', 'tourfic' ),
          'options'   => array(
            'regular' => __('Regular', 'tourfic'),
            'hover' => __('Hover', 'tourfic'),
          )
        ),
        array(
          'id'        => 'tourfic-sidebar-booking',
          'type'      => 'color_group',
          'title'    => __( 'Sidebar Booking Form', 'tourfic' ),
          'subtitle' => __( 'The Gradient color of Sidebar Booking', 'tourfic' ),
          'options'   => array(
            'gradient_one_reg' => __('Gradient One Color', 'tourfic'),
            'gradient_two_reg' => __('Gradient Two Color', 'tourfic'),
          )
        ),
        array(
          'id'        => 'tourfic-faq-style',
          'type'      => 'color_group',
          'title'    => __( 'FAQ Styles', 'tourfic' ),
          'subtitle' => __( 'Style of FAQ Section', 'tourfic' ),
          'options'   => array(
            'faq_color' => __('Heading Color', 'tourfic'),
            'faq_icon_color' => __('Icon Color', 'tourfic'),
            'faq_border_color' => __('Border Color', 'tourfic'),
          )
        ),
        array(
          'id'        => 'tourfic-review-style',
          'type'      => 'color_group',
          'title'    => __( 'Review Styles', 'tourfic' ),
          'subtitle' => __( 'Style of Review Section', 'tourfic' ),
          'options'   => array(
            'rating_color' => __('Rating Color', 'tourfic'),
            'rating_bg_color' => __('Rating Background', 'tourfic'),
            'param_bg_color' => __('Parameter Background', 'tourfic'),
            'param_single_bg_color' => __('Single Parameter', 'tourfic'),
            'param_txt_color' => __('Single Parameter Text', 'tourfic'),
            'review_color' => __('Review Color', 'tourfic'),
            'review_bg_color' => __('Review Background', 'tourfic'),
          )
        ),
    )
    
) );
/**
 * Hotel
 * 
 * Sub Menu
 */
CSF::createSection( $prefix, array(
  'parent'    => 'design-panel', 
  'title' =>  __( 'Hotel', 'tourfic' ),
  'icon'   => 'fas fa-hotel',
  'fields' => array(
      array(
          'type'    => 'subheading',
          'content' => __('Hotel Settings', 'tourfic' ),
      ),
      array(
          'id'        => 'tourfic-hotel-type-bg-color',
          'type'      => 'color_group',
          'title'    => __( 'Hotel Type Color', 'tourfic' ),
          'subtitle' => __( 'The "Hotel" text above heading ', 'tourfic' ),
          'options'   => array(
            'regular' => __('Color', 'tourfic'),
            'hover' => __('Background Color', 'tourfic'),
          )
      ),
      array(
        'id'        => 'tourfic-hotel-share-icon',
        'type'      => 'color_group',
        'title'    => __( 'Share Icon Color', 'tourfic' ),
        'subtitle' => __( 'The color of Share Icon', 'tourfic' ),
        'options'   => array(
          'regular' => __('Regular', 'tourfic'),
          'hover' => __('Hover', 'tourfic'),
        )
      ),
      array(
        'id'        => 'tourfic-hotel-map-button',
        'type'      => 'color_group',
        'title'    => __( 'Map Button', 'tourfic' ),
        'subtitle' => __( 'The Gradient color of Map Button', 'tourfic' ),
        'options'   => array(
          'gradient_one_reg' => __('Gradient One Color', 'tourfic'),
          'gradient_two_reg' => __('Gradient Two Color', 'tourfic'),
          'gradient_one_hov' => __('Gradient One Hover', 'tourfic'),
          'gradient_two_hov' => __('Gradient Two Hover', 'tourfic'),
        )
      ),
      array(
        'id'        => 'tourfic-hotel-features-color',
        'type'      => 'color_group',
        'title'    => __( 'Hotel Features Color', 'tourfic' ),
        'subtitle' => __( 'The Color of Features Icon ', 'tourfic' ),
        'options'   => array(
          'regular' => __('Color', 'tourfic'),
        )
      ),
      array(
        'id'        => 'tourfic-hotel-table-style',
        'type'      => 'color_group',
        'title'    => __( 'Room Table Styles', 'tourfic' ),
        'subtitle' => __( 'The style of Room Table', 'tourfic' ),
        'options'   => array(
          'table_color' => __('Heading Color', 'tourfic'),
          'table_bg_color' => __('Heading Background Color', 'tourfic'),
          'table_border_color' => __('Border Color', 'tourfic'),
        )
      ),
  )
) );

/**
 * Tour
 * 
 * Sub Menu
 */
CSF::createSection( $prefix, array(
  'parent'    => 'design-panel', 
  'title' =>  __( 'Tour', 'tourfic' ),
  'icon'   => 'fas fa-umbrella-beach',
  'fields' => array(
      array(
          'type'    => 'subheading',
          'content' => __('Tour Settings', 'tourfic' ),
      ),
      array(
          'id'        => 'tourfic-tour-pricing-color',
          'type'      => 'color_group',
          'title'    => __( 'Price Section', 'tourfic' ),
          'subtitle' => __( 'Styling of the Pricing', 'tourfic' ),
          'options'   => array(
            'sale_price' => __('Sale Price', 'tourfic'),
            'org_price' => __('Original Price', 'tourfic'),
            'tab_text' => __('Tab Text', 'tourfic'),
            'tab_bg' => __('Tab Background', 'tourfic'),
            'active_tab_text' => __('Active Tab Text', 'tourfic'),
            'active_tab_bg' => __('Active Tab Background', 'tourfic'),
            'tab_border' => __('Tab Border', 'tourfic'),
          )
      ),
      array(
        'id'        => 'tourfic-tour-info-color',
        'type'      => 'color_group',
        'title'    => __( 'Information / Summary Section', 'tourfic' ),
        'subtitle' => __( 'Styling of the Info  / Summary', 'tourfic' ),
        'options'   => array(
          'icon_color' => __('Icon Color', 'tourfic'),
          'heading_color' => __('Heading Color', 'tourfic'),
          'text_color' => __('Text Color', 'tourfic'),
          'bg_one' => __('Background One', 'tourfic'),
          'bg_two' => __('Background Two', 'tourfic'),
          'bg_three' => __('Background Three', 'tourfic'),
          'bg_four' => __('Background Four', 'tourfic'),
        )
      ),
      array(
        'id'        => 'tourfic-tour-sticky-booking',
        'type'      => 'color_group',
        'title'    => __( 'Sticky Booking', 'tourfic' ),
        'subtitle' => __( 'Styling of Sticky Booking Form', 'tourfic' ),
        'options'   => array(
          'btn_col' => __('Button Color', 'tourfic'),
          'btn_bg' => __('Button Background', 'tourfic'),
          'btn_hov_col' => __('Button Hover Color', 'tourfic'),
          'btn_hov_bg' => __('Button Hover Background', 'tourfic'),
          'form_background' => __('Form Background', 'tourfic'),
          'form_border' => __('Form Border', 'tourfic'),
        )
      ),
      array(
        'id'        => 'tourfic-include-exclude',
        'type'      => 'color_group',
        'title'    => __( 'Include - Exclude Section', 'tourfic' ),
        'subtitle' => __( 'Styling of Include - Exclude Section', 'tourfic' ),
        'options'   => array(
          'gradient_one_reg' => __('Gradient One Color', 'tourfic'),
          'gradient_two_reg' => __('Gradient Two Color', 'tourfic'),
          'heading_color' => __('Heading Color', 'tourfic'),
          'text_color' => __('Text Color', 'tourfic'),
        )
      ),
      array(
        'id'        => 'tourfic-tour-itinerary',
        'type'      => 'color_group',
        'title'    => __( 'Travel Itinerary', 'tourfic' ),
        'subtitle' => __( 'Styling of Travel Itinerary', 'tourfic' ),
        'options'   => array(
          'time_day_txt' => __('Time or Day Text', 'tourfic'),
          'time_day_bg' => __('Time or Day Background', 'tourfic'),
          'heading_color' => __('Heading Color', 'tourfic'),
          'text_color' => __('Text Color', 'tourfic'),
          'bg_color' => __('Background Color', 'tourfic'),
          'icon_color' => __('Icon Color', 'tourfic'),
        )
      ),
  )
) );
?>