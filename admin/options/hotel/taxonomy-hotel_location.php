<?php
defined( 'ABSPATH' ) || exit;

if( class_exists( 'CSF' ) ) {

  $prefix = 'hotel_location';

  // Create hotel_feature options
  CSF::createTaxonomyOptions( $prefix, array(
    'taxonomy'  => 'hotel_location',
    'data_type' => 'serialize',
  ) );

  // Create a section
  CSF::createSection( $prefix, array(
    'fields' => array(

        array(
            'id'      => 'image',
            'type'    => 'media',
            'title'   => 'Upload location photo',
            'library' => 'image',
          ),

    )
  ) );

}

?>