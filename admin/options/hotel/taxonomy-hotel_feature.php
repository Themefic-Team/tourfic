<?php
defined( 'ABSPATH' ) || exit;

if( class_exists( 'CSF' ) ) {

  $prefix = 'hotel_feature';

  // Create hotel_feature options
  CSF::createTaxonomyOptions( $prefix, array(
    'taxonomy'  => 'hotel_feature',
    'data_type' => 'serialize',
  ) );

  // Create a section
  CSF::createSection( $prefix, array(
    'fields' => array(

      array(
        'id'    => 'icon',
        'type'  => 'icon',
        'title' => 'Add icon for this feature',
      ),

    )
  ) );

}

?>