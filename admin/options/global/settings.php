<?php

// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

  $prefix = 'tourfic_opt';

  //
  // Create options
  CSF::createOptions( $prefix, array(
    'menu_title'              =>   __( 'Tourfic', 'tourfic' ),
    'menu_slug'               =>   'tourfic',
    'framework_title'         =>   __( 'Tourfic Settings <small><a href="https://themefic.com/">By THEMEFIC</a></small>', 'tourfic' ),
    'footer_credit'           =>   __( 'Enjoyed Tourfic? Please leave us a <a target="_blank" href="https://wordpress.org/support/plugin/tourfic/reviews/#new-post">★★★★★</a> rating. We really appreciate your support!', 'tourfic' ),
    'menu_position'           =>   6,
    'show_sub_menu'           =>   false,
    'theme'                   =>   'dark',
    'menu_icon'               =>   'dashicons-calendar-alt',
    
  ) );

  //
  // Create a top-tab
  CSF::createSection( $prefix, array(
    'id'    => 'general_tab', 
    'title' =>  __( 'General', 'tourfic' ),
    'icon'  =>  'fas fa-rocket' ,
    'fields' => array(
      array(
        'id'      => 'post_type_slug',
        'type'    => 'text',
        'title'   => __( 'Input slug of Hotel', 'tourfic' ),
        'desc'   =>  __( 'Default is: <code>tourfic</code> - <strong>Save 2 times if you change this field for permalink flush</strong>', 'tourfic' )
      ),
      array(
        'id'      => 'tour_type_slug',
        'type'    => 'text',
        'title'   => __( 'Input slug of Tour', 'tourfic' ),
        'desc'   =>  __( 'Default is: <code>tour</code> - <strong>Save 2 times if you change this field for permalink flush</strong>', 'tourfic' )
      ),
      array(
        'id'          => 'single_tour_style',
        'type'        => 'select',
        'placeholder' => 'Select a page',
        'chosen'      => true,
        'title'       =>  __( 'Select Single Page Template', 'tourfic' ),
        'desc'        =>  __( 'You can choose signle tour page layout from here', 'tourfic' ),
        'options'  => array(
          'single_hotel.php' => 'Style 1',
      ),
      'default'  => 'single_hotel.php',
      ),

    )
    
  ) );


  // Search tab
  CSF::createSection( $prefix, array(
    'id'    => 'search', 
    'title'  => __( 'Search', 'tourfic' ),
    'icon'  =>  'fas fa-search' ,
    'fields' => array(

      array(
        'id'          => 'search-result-page',
        'type'        => 'select',
        'placeholder' => 'Select a page',
        'chosen'      => true,
        'ajax'        => true,
        'title'       =>  __( 'Select Search Result Page', 'tourfic' ),
        'desc' =>  __( 'Page template: <code>Tourfic - Search Result</code> must be selected', 'tourfic' ),
        'options'     =>  'pages',
        
      ),
      array(
        'id'          => 'search_relation',
        'type'        => 'select',
        'title'       => __( 'Search Result Relation', 'tourfic' ),
        'desc'        => __( 'Search result relation with search widget and filters. OR means matched any query, AND means matched all query.', 'tourfic' ),
        'chosen'      => true,
        'placeholder' => 'Select an option',
        'options'     => array(
          'AND' => 'AND',
          'OR' => 'OR',
        ),
        'default'  => 'AND'
      ),

      array(
        'id'          => 'filter_relation',
        'type'        => 'select',
        'title'       => __( 'Filters Relation', 'tourfic' ),
        'desc'        => __( 'Search result Filters relation with among filters. OR means matched any filter, AND means matched all filter.', 'tourfic' ),
        'chosen'      => true,
        'multiple'    => false,
        'placeholder' => 'Select an option',
        'options'     => array(
          'AND' => 'AND',
          'OR' => 'OR',
        ),
        'default'  => 'OR',
      ),
    )
  ) );


  // Extra tab
  CSF::createSection( $prefix, array(
    'id'    => 'preference', 
    'title' => 'Preferences',
    'icon'  =>  'fas fa-asterisk' ,
    'fields' => array(
      array(
        'id'       => 'custom-css',
        'type'     => 'code_editor',
        'title'    => __( 'Custom CSS', 'tourfic' ),
        'settings' => array(
          'theme'  => 'mbo',
          'mode'   => 'css',
        ),
        'default'  => '.element{ color: #ffbc00; }',
      ),

    )
  ) );




}