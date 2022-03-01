<?php

// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

  $prefix = 'tourfic_opt';

  CSF::createOptions( $prefix, array(
    'menu_title'              =>   __( 'Tourfic Settings', 'tourfic' ),
    'menu_slug'               =>   'tourfic',
    'framework_title'         =>   __( 'Tourfic Settings <small>by <a style="color: #bfbfbf;text-decoration:none;" href="https://themefic.com" target="_blank">Themefic</a></small>', 'tourfic' ),
    'footer_credit'           =>   __( '<em>Enjoyed <strong>Tourfic</strong>? Please leave us a <a style="color:#e9570a;" href="https://wordpress.org/support/plugin/tourfic/reviews/?filter=5/#new-post" target="_blank">★★★★★</a> rating. We really appreciate your support!</em>', 'tourfic' ),
    'menu_position'           =>   25,
    'show_sub_menu'           =>   true,
    'theme'                   =>   'dark',
    'menu_icon'               =>   'dashicons-palmtree',
    
  ) );

  //
  // Create a top-tab
  CSF::createSection( $prefix, array(
    'id'    => 'general_tab', 
    'title' =>  __( 'General', 'tourfic' ),
    'icon'  =>  'fas fa-rocket' ,
    'fields' => array(
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


  // Vendor
  CSF::createSection( $prefix, array(
    'id'    => 'vendor', 
    'title' =>  __( 'Multi Vendor', 'tourfic' ),
    'icon'  =>  'fas fa-handshake' ,
    'fields' => array(

      // Registration
      array(
        'type'    => 'subheading',
        'content' => 'Registration',
      ),

      array(
        'id'         => 'reg-pop',
        'type'       => 'switcher',
        'title'      => 'Registration Form Popup',
        'subtitle'      => 'Add class <code>tf-reg-popup</code> to trigger the popup',
        'text_on'    => 'Enabled',
        'text_off'   => 'Disabled',
        'text_width' => 100,
        'default' => true,
      ),

      array(
        'type'    => 'content',
        'content' => 'Use shortcode <code>[tf_registration_form]</code> to show registration form in post/page.',
      ),

      array(
        'id'         => 'email-verify',
        'type'       => 'switcher',
        'title'      => 'Enable Email Verification',
        'default' => true,
      ),

      array(
        'id'         => 'prevent-login',
        'type'       => 'switcher',
        'title'      => 'Login Restriction',
        'subtitle'      => 'Prevent unverified user to login',
        'dependency' => array( 'email-verify', '==', 'true' ),
        'default' => true,
      ),
      
      // Vendor
      array(
        'type'    => 'subheading',
        'content' => 'Vendor',
      ),

      array(
        'id'         => 'vendor-reg',
        'type'       => 'switcher',
        'title'      => 'Enable Vendor Registration',
        'subtitle'      => 'Visitor can register as vendor using the registration form',
        'default' => true,
      ),

      array(
        'id'         => 'vendor-tax-add',
        'type'       => 'checkbox',
        'title'      => 'Vendor Can Add',
        'options'    => array(
          'hl' => 'Hotel Location',
          'hf' => 'Hotel Feature',
          'td' => 'Tour Destination',
        ),
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