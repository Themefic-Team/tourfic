<?php
defined( 'ABSPATH' ) || exit;

if( class_exists( 'CSF' ) ) {

    $prefix = 'tf_hotel';
  
    // Create a metabox
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
          'id'          => 'features',
          'type'        => 'select',
          'title'       => 'Select Features',
          'placeholder' => 'Select',
          'chosen'      => true,
          'multiple'    => true,
          //'ajax'        => true,
          'options'     => 'categories',
          'query_args'  => array(
            'taxonomy'  => 'hotel_feature',
          ),
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
          'id'      => 'opt-slider-3',
          'type'    => 'slider',
          'title'   => 'Hotel rating standard',
          'min'     => 0,
          'max'     => 5,
          'step'    => 1,
          'default' => 0,
        ),
  
      )
    ) );
  
  }
?>