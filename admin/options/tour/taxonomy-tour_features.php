<?php

// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

  //
  // Set a unique slug-like ID
  $prefix = 'feature_meta';

  //
  // Create taxonomy options
  CSF::createTaxonomyOptions( $prefix, array(
    'taxonomy'  => 'tf_feature',
    'data_type' => 'serialize', // The type of the database save options. `serialize` or `unserialize`
  ) );

  //
  // Create a section
  CSF::createSection( $prefix, array(
    'fields' => array(

        array(
            'id'           => 'features_icon',
            'type'         => 'upload',
            'title'        => 'Feature Icon',
            'library'      => 'image',
            'placeholder'  => 'http://',
            'button_title' => 'Add icon',
            'remove_title' => 'Remove Icon',
          ),

    )
  ) );

}

// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

  //
  // Set a unique slug-like ID
  $prefix = 'tour_destination_meta';

  //
  // Create taxonomy options
  CSF::createTaxonomyOptions( $prefix, array(
    'taxonomy'  => 'tour_destination',
    'data_type' => 'serialize', // The type of the database save options. `serialize` or `unserialize`
  ) );

  //
  // Create a section
  CSF::createSection( $prefix, array(
    'fields' => array(

        array(
            'id'           => 'tour_destination_meta',
            'type'         => 'upload',
            'title'        => 'Destination Image',
            'library'      => 'image',
            'placeholder'  => 'http://',
            'button_title' => 'Add image',
            'remove_title' => 'Remove image',
          ),

    )
  ) );

}

?>