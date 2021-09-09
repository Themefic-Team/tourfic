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
    'footer_credit'           =>   __( 'Enjoyed Tourfic? Please leave us a <a href="">★★★★★</a> rating. We really appreciate your support!', 'tourfic' ),
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
    'icon'  =>  'dashicons-admin-generic' ,
    
  ) );

  //
  // Create a sub-tab
  CSF::createSection( $prefix, array(
    'parent' => 'general_tab', // The slug id of the parent section
    'title'  => 'Search',
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

    )
  ) );

  //
  // Create a sub-tab
  CSF::createSection( $prefix, array(
    'parent' => 'primary_tab',
    'title'  => 'Sub Tab 2',
    'fields' => array(

      // A textarea field
      array(
        'id'    => 'opt-textarea',
        'type'  => 'textarea',
        'title' => 'Simple Textarea',
      ),

    )
  ) );

  //
  // Create a top-tab
  CSF::createSection( $prefix, array(
    'id'    => 'secondry_tab', // Set a unique slug-like ID
    'title' => 'Secondry Tab',
  ) );


  //
  // Create a sub-tab
  CSF::createSection( $prefix, array(
    'parent' => 'secondry_tab', // The slug id of the parent section
    'title'  => 'Sub Tab 1',
    'fields' => array(

      // A switcher field
      array(
        'id'    => 'opt-switcher',
        'type'  => 'switcher',
        'title' => 'Simple Switcher',
      ),

    )
  ) );

}