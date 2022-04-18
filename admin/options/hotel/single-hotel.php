<?php
defined( 'ABSPATH' ) || exit;

$badge_up = '<div class="tf-csf-badge"><span class="tf-upcoming">' .__("Upcoming", "tourfic"). '</span></div>';
$badge_pro = '<div class="tf-csf-badge"><span class="tf-pro">' .__("Pro Feature", "tourfic"). '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' .__("Upcoming", "tourfic"). '</span><span class="tf-pro">' .__("Pro Feature", "tourfic"). '</span></div>';

if( class_exists( 'CSF' ) ) {
  
    // Hotel options
    $prefix = 'tf_hotel';

    CSF::createMetabox( $prefix, array(
        'title'     => __( 'Hotel Settings', TFD ),
        'post_type' => 'tf_hotel',
        'context'   => 'advanced',
        'priority'  => 'high',
        'theme'     => 'dark',
    ) );
  
    // Location Details
    CSF::createSection( $prefix, array(
      'title'  => __('Location', TFD),
      'fields' => array(

        array(
          'id'      => 'address',
          'type'    => 'textarea',
          'title'   => __('Hotel Address', TFD),
          'subtitle' => __('Enter hotel adress', TFD),
          'placeholder' => __('Address', TFD),
          'attributes' => array(
            'required' => 'required',
          ),
        ),

        array(
          'id'       => '',
          'class' => 'tf-csf-disable tf-csf-pro',
          'type'     => 'map',
          'title'    => __('Location on Map', TFD),
          'subtitle' => __('Select one location on the map to see latitude and longitude' .$badge_pro, TFD),
          'height'   => '250px',
          'settings' => array(
            'scrollWheelZoom' => true,
          ),
        ),
  
      )
    ) );

    // Hotel Details
    CSF::createSection( $prefix, array(
      'title'  => __( 'Hotel Detail', TFD ),
      'fields' => array(

        array(
          'id'    => 'gallery',
          'type'  => 'gallery',
          'title' => __('Hotel Gallery', TFD),
          'subtitle' => __('Upload one or many images to make a hotel image gallery for customers', TFD),
        ),
  
        array(
          'id'    => 'featured',
          'class' => 'tf-csf-disable',
          'type'  => 'switcher',
          'title' => __('Featured Hotel', TFD ),
          'subtitle'   => $badge_up,
          'text_on'  => __('Yes', TFD ),
          'text_off' => __('No', TFD ),
        ),

        // array(
        //   'id'      => '',
        //   'class' => 'tf-csf-disable tf-csf-pro',
        //   'type'    => 'media',
        //   'title'   => __('Hotel logo', TFD ),
        //   'subtitle'   => $badge_up_pro,
        //   'desc' =>  __( 'Upload the hotel logo (it is recommended using size: 256 x 195 px)', TFD ),
        //   'library' => 'image',
        // ),

        array(
          'id'       => '',
          'class' => 'tf-csf-disable tf-csf-pro',
          'type'     => 'text',
          'title'    => __('Hotel Video', TFD),
          'subtitle'   => $badge_up_pro,
          'desc' =>  __( 'Enter YouTube/Vimeo URL here', TFD ),
          'validate' => 'csf_validate_url',
        ),

        // array(
        //   'id'      => '',
        //   'class' => 'tf-csf-disable tf-csf-pro',
        //   'type'    => 'slider',
        //   'title'   => __('Hotel Rating Standard', TFD),
        //   'subtitle'   => $badge_up_pro,
        //   'min'     => 0,
        //   'max'     => 7,
        //   'step'    => 1,
        //   'default' => 0,
        // ),
  
      )
    ) );

    // Contact Information
    // CSF::createSection( $prefix, array(
    //   'title'  => __('Contact Information', TFD),
    //   'fields' => array(
  
    //     array(
    //       'id'       => 'c-email',
    //       'class' => 'tf-csf-disable',
    //       'type'     => 'text',
    //       'title'    => __('Hotel Email', TFD),
    //       'subtitle'   => $badge_up,
    //       'desc' =>  __( 'This email will received notification when have booking order', TFD ),
    //       //'validate' => 'csf_validate_email',
    //     ),

    //     array(
    //       'id'       => 'c-web',
    //       'class' => 'tf-csf-disable',
    //       'type'     => 'text',
    //       'title'    => __('Hotel Website', TFD),
    //       'subtitle' =>  __( 'Enter hotel website' .$badge_up, TFD ),
    //       //'validate' => 'csf_validate_url',
    //     ),

    //     array(
    //       'id'      => 'c-phone',
    //       'class' => 'tf-csf-disable',
    //       'type'    => 'text',
    //       'title'   => __('Hotel Phone Number', TFD),
    //       'subtitle' => __('Enter hotel phone number' .$badge_up, TFD),
    //     ),

    //     array(
    //       'id'      => 'c-fax',
    //       'class' => 'tf-csf-disable',
    //       'type'    => 'text',
    //       'title'   => __('Hotel Fax', TFD),
    //       'subtitle' => __('Enter hotel fax number' .$badge_up, TFD),
    //     ),
  
    //   )
    // ) );

     
    // Check-in check-out
    CSF::createSection( $prefix, array(
      'title'  => __('Check in/out Time', TFD),
      'fields' => array(
  
        array(
          'id'    => '',
          'class' => 'tf-csf-disable tf-csf-pro',
          'type'  => 'switcher',
          'title' => __('Allowed Full Day Booking', TFD),
          'subtitle' =>  __( 'You can book room with full day' .$badge_up_pro, TFD ),
          'desc' =>  __( 'E.g: booking from 22 -23, then all days 22 and 23 are full, other people cannot book', TFD ),
        ),

        // array(
        //   'id'       => '',
        //   'class' => 'tf-csf-disable tf-csf-pro',
        //   'type'     => 'datetime',
        //   'title'    => __('Time for Check-in', TFD),
        //   'subtitle' => __('Enter time for check-in at hotel' .$badge_up_pro, TFD),
        //   'settings' => array(
        //     'noCalendar' => true,
        //     'enableTime' => true,
        //     'dateFormat' => 'h:i K',
        //   ),
        // ),

        // array(
        //   'id'       => '',
        //   'class' => 'tf-csf-disable tf-csf-pro',
        //   'type'     => 'datetime',
        //   'title'    => __('Time for Check-out', TFD),
        //   'subtitle' => __('Enter time for check-out at hotel' .$badge_up_pro, TFD),
        //   'settings' => array(
        //     'noCalendar' => true,
        //     'enableTime' => true,
        //     'dateFormat' => 'h:i K',
        //   ),
        // ),
  
      )
    ) );

    CSF::createSection( $prefix, array(
      'title'  => __('Room Details', TFD ),
      'fields' => array(
  
        array(
          'id'     => 'room',
          'type'   => 'repeater',
          'title'  => __('Room Details', TFD ),
          'max' => 5,
          'fields' => array(
                  
            array(
              'id'         => 'enable',
              'type'       => 'switcher',
              'title'      => __('Status', TFD ),
              'subtitle'   => __( 'Enable/disable this Room', TFD ),
              'text_on'    => __('Enabled', TFD ),
              'text_off'   => __('Disabled', TFD ),
              'text_width' => 100,
              'default'    => true,
            ),

            array(
              'id'      => 'title',
              'type'    => 'text',
              'title'   => __('Room Title', TFD ),
            ),

            array(
              'id'    => 'num-room',
              'type'  => 'number',
              'title' => __('Number of Rooms', TFD ),
              'subtitle' =>  __( 'Number of available rooms for booking', TFD ),
            ),

            array(
              'type'    => 'subheading',
              'content' => __('Details', TFD ),
            ),

            array(
              'id'    => 'gallery',
              'type'  => 'gallery',
              'title' => __('Gallery', TFD ),
              'subtitle' =>  __( 'Upload images to make a gallery image for room', TFD ),
            ),

            array(
              'id'    => 'bed',
              'type'  => 'number',
              'title' => __('Number of Beds', TFD ),
              'subtitle' =>  __( 'Number of beds present in the room', TFD ),
            ),

            array(
              'id'    => 'adult',
              'type'  => 'number',
              'title' => __('Number of Adults', TFD ),
              'subtitle' =>  __( 'Max number of persons allowed in the room', TFD ),
            ),

            array(
              'id'    => 'child',
              'type'  => 'number',
              'title' => __('Number of Children', TFD ),
              'subtitle' =>  __( 'Max number of persons allowed in the room', TFD ),
            ),

            array(
              'id'      => 'footage',
              'type'    => 'text',
              'title'   => __('Room Footage', TFD ),
              'subtitle' =>  __( 'Room footage (sft)', TFD ),
            ),

            array(
              'id'          => 'features',
              'type'        => 'select',
              'title'       => __('Select Features', TFD ),
              'placeholder' => __('Select', TFD ),
              'empty_message' => __('No feature available', TFD ),
              'chosen'      => true,
              'multiple'    => true,
              'options'     => 'categories',
              'query_args'  => array(
                'taxonomy'  => 'hotel_feature',
              ),
            ),

            array(
              'id'      => 'description',
              'type'    => 'textarea',
              'title'   => __('Room Description', TFD ),
            ),

            array(
              'type'    => 'subheading',
              'content' => __('Pricing', TFD ),
            ),

            array(
              'id'          => 'pricing-by',
              'type'        => 'select',
              'title'       => __('Pricing by', TFD ),
              'options'     => array(
                '1'  => __('Per room', TFD ),
                '2'  => __('Per person (Pro)', TFD ),
              ),
              'default'     => '1'
            ),

            array(
              'id'      => 'price',
              'type'    => 'text',
              'title'   => __('Pricing', TFD ),
              'desc' =>  __( 'The price of room per one night', TFD ),
              'dependency' => array( 'pricing-by', '==', '1' ),
            ),

            array(
              'id'      => '',
              'class' => 'tf-csf-disable tf-csf-pro',
              'type'    => 'text',
              'title'   => __('Adult Pricing', TFD ),
              'subtitle'   => $badge_pro,
              'desc' =>  __( 'The price of room per one night', TFD ),
              'dependency' => array( 'pricing-by', '==', '2' ),
            ),

            array(
              'id'      => '',
              'class' => 'tf-csf-disable tf-csf-pro',
              'type'    => 'text',
              'title'   => __('Children Pricing', TFD ),
              'subtitle'   => $badge_pro,
              'desc' =>  __( 'The price of room per one night', TFD ),
              'dependency' => array( 'pricing-by', '==', '2' ),
            ),

            array(
              'type'    => 'subheading',
              'content' => __('Availability', TFD ),
            ),

            array(
              'id'       => '',
              'class' => 'tf-csf-disable tf-csf-pro',
              'type'     => 'datetime',
              'title'    => __( 'Date', TFD ),
              'subtitle' => __( 'Select availablity date' .$badge_up_pro, TFD ),
              'settings' => array(
                  'dateFormat'      => 'Y/m/d'
              ),
              'from_to'   => true,
              'text_from' => __('Check-in', TFD ),
              'text_to'   => __('Check-out', TFD ),
          ),
        
          ),
        ),
  
      )
    ) );

    // FAQ
    CSF::createSection( $prefix, array(
      'title'  => __('F.A.Q.', TFD ),
      'fields' => array(
  
        array(
          'id'     => 'faq',
          'type'   => 'repeater',
          'title'  => __('Frequently Asked Questions', TFD ),
          'button_title' => __( 'Add FAQ', TFD ),
          'fields' => array(
        
            array(
              'id'    => 'title',
              'type'  => 'text',
              'title' => __('Title', TFD ),
            ),

            array(
              'id'      => 'description',
              'type'    => 'textarea',
              'title'   => __('Description', TFD ),
            ),
        
          ),
        ),
  
      )
    ) );

    // Terms & conditions
    CSF::createSection( $prefix, array(
      'title'  => __('Terms & Conditions', TFD ),
      'fields' => array(
  
        array(
          'id'    => 'tc',
          'type'  => 'wp_editor',
          'title' => __('Terms & Conditions', TFD ),
        ),
  
      )
    ) );

    // Settings
    CSF::createSection(
      $prefix,
      array(
          'title'  => __('Settings', 'tourfic'),
          'fields' => array(

            array(
              'type'    => 'subheading',
              'content' => __('Settings', 'tourfic' ),
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
              'type'    => 'notice',
              'style'   => 'info',
              'content' => __('These settings will overwrite global settings', 'tourfic' ),
            ),

          ),
      )
    );

  
  }
