<?php
defined( 'ABSPATH' ) || exit;

if( class_exists( 'CSF' ) ) {

  $prefix = 'tour_destination';

  // Create hotel_feature options
  CSF::createTaxonomyOptions( $prefix, array(
    'taxonomy'  => 'tour_destination',
    'data_type' => 'serialize',
  ) );

  // Create a section
  CSF::createSection( $prefix, array(
    'fields' => array(

        array(
            'id'      => 'image',
            'type'    => 'media',
            'title'   => 'Upload destination photo',
            'library' => 'image',
          ),

    )
  ) );

}

?>