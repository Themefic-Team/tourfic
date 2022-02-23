<?php
defined( 'ABSPATH' ) || exit;

if( class_exists( 'CSF' ) ) {
  
    // Hotel options
    $prefix = 'tf_hotel';

    CSF::createMetabox( $prefix, array(
        'title'     => __( 'Hotel Settings', 'tourfic' ),
        'post_type' => 'tf_hotel',
        'context'   => 'advanced',
        'priority'  => 'high',
        'theme'     => 'dark',
    ) );
  
    // Location Details
    CSF::createSection( $prefix, array(
      'title'  => 'Location',
      'fields' => array(
  
        array(
          'id'          => 'location',
          'type'        => 'select',
          'title'       => 'Hotel location',
          'subtitle' => __( 'Select one or more locations for your hotel', 'tourfic' ),
          'placeholder' => 'Type to search',
          'desc' =>  __( 'Enter the name you need to search in search box to filter address faster', 'tourfic' ),
          'chosen'      => true,
          'multiple'    => true,
          'ajax'        => true,
          'options'     => 'categories',
          'query_args'  => array(
            'taxonomy'  => 'hotel_location',
          ),
          'settings'  => array(
            'min_length'  => '1',
          ),
        ),

        array(
          'id'      => 'address',
          'type'    => 'textarea',
          'title'   => __('Hotel address', 'tourfic'),
          'subtitle' => __('Enter your hotel address detail', 'tourfic'),
          'placeholder' => __('Address', 'tourfic'),
        ),

        array(
          'id'       => 'map',
          'type'     => 'map',
          'title'    => __('Location on map', 'tourfic'),
          'subtitle' => __('Select one location on map to see latiture and longiture', 'tourfic'),
          'height'   => '250px',
          'settings' => array(
            'scrollWheelZoom' => true,
          )
        ),
  
      )
    ) );

    // Hotel Details
    CSF::createSection( $prefix, array(
      'title'  => __( 'Hotel Detail', 'tourfic' ),
      'fields' => array(
  
        array(
          'id'    => 'featured',
          'type'  => 'switcher',
          'title' => 'Set hotel as featured',
        ),

        array(
          'id'      => 'logo',
          'type'    => 'media',
          'title'   => 'Hotel logo',
          'desc' =>  __( 'Upload the hotel logo (it is recommended using size: 256 x 195 px)', 'tourfic' ),
          'library' => 'image',
        ),

        array(
          'id'    => 'gallery',
          'type'  => 'gallery',
          'title' => 'Hotel gallery',
          'subtitle' => __('Upload one or many images to make a hotel image gallery for customers', 'tourfic'),
        ),

        array(
          'id'       => 'video',
          'type'     => 'text',
          'title'    => 'Hotel video',
          'desc' =>  __( 'Enter YouTube/Vimeo URL here', 'tourfic' ),
          'validate' => 'csf_validate_url',
        ),

        array(
          'id'      => 'rating',
          'type'    => 'slider',
          'title'   => 'Hotel rating standard',
          'min'     => 0,
          'max'     => 5,
          'step'    => 1,
          'default' => 0,
        ),
  
      )
    ) );

    // Contact Information
    CSF::createSection( $prefix, array(
      'title'  => 'Contact Information',
      'fields' => array(
  
        array(
          'id'       => 'c-email',
          'type'     => 'text',
          'title'    => 'Hotel email',
          'desc' =>  __( 'This email will received notification when have booking order', 'tourfic' ),
          'validate' => 'csf_validate_email',
        ),

        array(
          'id'       => 'c-web',
          'type'     => 'text',
          'title'    => 'Hotel website',
          'subtitle' =>  __( 'Enter hotel website', 'tourfic' ),
          'validate' => 'csf_validate_url',
        ),

        array(
          'id'      => 'c-phone',
          'type'    => 'text',
          'title'   => 'Hotel phone number',
          'subtitle' => 'Enter hotel phone number'
        ),

        array(
          'id'      => 'c-fax',
          'type'    => 'text',
          'title'   => 'Hotel fax',
          'subtitle' => 'Enter hotel fax number'
        ),
  
      )
    ) );

     
    // Check-in check-out
    CSF::createSection( $prefix, array(
      'title'  => 'Check in/out Time',
      'fields' => array(
  
        array(
          'id'    => 'full-day',
          'type'  => 'switcher',
          'title' => 'Allowed full day booking',
          'subtitle' =>  __( 'You can book room with full day', 'tourfic' ),
          'desc' =>  __( 'E.g: booking from 22 -23, then all days 22 and 23 are full, other people cannot book', 'tourfic' ),
        ),

        array(
          'id'       => 'check-in',
          'type'     => 'datetime',
          'title'    => 'Time for check in',
          'subtitle' => 'Enter time for check in at hotel',
          'settings' => array(
            'noCalendar' => true,
            'enableTime' => true,
            'dateFormat' => 'h:i K',
          ),
        ),

        array(
          'id'       => 'check-out',
          'type'     => 'datetime',
          'title'    => 'Time for check out',
          'subtitle' => 'Enter time for checkout at hotel',
          'settings' => array(
            'noCalendar' => true,
            'enableTime' => true,
            'dateFormat' => 'h:i K',
          ),
        ),
  
      )
    ) );

    CSF::createSection( $prefix, array(
      'title'  => 'Room Details',
      'fields' => array(
  
        array(
          'id'     => 'room',
          'type'   => 'repeater',
          'title'  => 'Room Details',
          'fields' => array(
                  
            array(
              'id'         => 'enable',
              'type'       => 'switcher',
              'title'      => 'Status',
              'subtitle'   => __( 'Enable/disable this room', 'tourfic' ),
              'text_on'    => 'Enabled',
              'text_off'   => 'Disabled',
              'text_width' => 100,
              'default'    => true,
            ),

            array(
              'id'      => 'title',
              'type'    => 'text',
              'title'   => 'Room Title',
            ),

            array(
              'id'    => 'num-room',
              'type'  => 'number',
              'title' => 'Number of rooms',
              'subtitle' =>  __( 'Number of available rooms for booking', 'tourfic' ),
            ),

            array(
              'type'    => 'subheading',
              'content' => 'Details',
            ),

            array(
              'id'    => 'gallery',
              'type'  => 'gallery',
              'title' => 'Gallery',
              'subtitle' =>  __( 'Upload images to make a gallery image for room', 'tourfic' ),
            ),

            array(
              'id'    => 'bed',
              'type'  => 'number',
              'title' => 'Number of beds',
              'subtitle' =>  __( 'Number of beds present in the room', 'tourfic' ),
            ),

            array(
              'id'    => 'adult',
              'type'  => 'number',
              'title' => 'Number of adults',
              'subtitle' =>  __( 'Max number of persons allowed in the room', 'tourfic' ),
            ),

            array(
              'id'    => 'child',
              'type'  => 'number',
              'title' => 'Number of children',
              'subtitle' =>  __( 'Max number of persons allowed in the room', 'tourfic' ),
            ),

            array(
              'id'      => 'footage',
              'type'    => 'text',
              'title'   => 'Room footage',
              'subtitle' =>  __( 'Room footage (sft)', 'tourfic' ),
            ),

            array(
              'id'          => 'features',
              'type'        => 'select',
              'title'       => 'Select Features',
              'placeholder' => 'Select',
              'empty_message' => 'No feature available',
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
              'title'   => 'Room description',
            ),

            array(
              'type'    => 'subheading',
              'content' => 'Pricing',
            ),

            array(
              'id'          => 'pricing-by',
              'type'        => 'select',
              'title'       => 'Pricing by',
              'options'     => array(
                '1'  => 'Per room',
                '2'  => 'Per person',
              ),
              'default'     => '1'
            ),

            array(
              'id'      => 'price',
              'type'    => 'text',
              'title'   => 'Pricing',
              'desc' =>  __( 'The price of room per one night', 'tourfic' ),
              'dependency' => array( 'pricing-by', '==', '1' ),
            ),

            array(
              'id'      => 'adult_price',
              'type'    => 'text',
              'title'   => 'Adult Pricing',
              'desc' =>  __( 'The price of room per one night', 'tourfic' ),
              'dependency' => array( 'pricing-by', '==', '2' ),
            ),

            array(
              'id'      => 'child_price',
              'type'    => 'text',
              'title'   => 'Children Pricing',
              'desc' =>  __( 'The price of room per one night', 'tourfic' ),
              'dependency' => array( 'pricing-by', '==', '2' ),
            ),

            array(
              'type'    => 'subheading',
              'content' => 'Availability',
            ),

            array(
              'id'       => 'availability',
              'type'     => 'datetime',
              'title'    => __( 'Date', 'tourfic' ),
              'subtitle' => __( 'Select availablity date', 'tourfic' ),
              'settings' => array(
                  'dateFormat'      => 'Y/m/d'
              ),
              'from_to'   => true,
              'text_from' => 'Check In',
              'text_to'   => 'Check Out',
          ),
        
          ),
        ),
  
      )
    ) );

    // FAQ
    CSF::createSection( $prefix, array(
      'title'  => 'F.A.Q.',
      'fields' => array(
  
        array(
          'id'     => 'faq',
          'type'   => 'repeater',
          'title'  => 'Frequently Asked Questions',
          'button_title' => __( 'Add FAQ', 'tourfic' ),
          'fields' => array(
        
            array(
              'id'    => 'title',
              'type'  => 'text',
              'title' => 'Title'
            ),

            array(
              'id'      => 'description',
              'type'    => 'textarea',
              'title'   => 'Description',
            ),
        
          ),
        ),
  
      )
    ) );

    // Terms & conditions
    CSF::createSection( $prefix, array(
      'title'  => 'T&C',
      'fields' => array(
  
        array(
          'id'    => 'tc',
          'type'  => 'wp_editor',
          'title' => 'Terms & Conditions',
        ),
  
      )
    ) );

  
  }
?>