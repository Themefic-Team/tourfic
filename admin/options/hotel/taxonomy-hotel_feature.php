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
        'id'         => 'icon-type',
        'type'       => 'button_set',
        'title'      => 'Select Icon type',
        'options'    => array(
          'fa'  => 'Font Awesome',
          'c' => 'Custom',
        ),
        'default'    => 'fa'
      ),
      
      array(
        'id'    => 'icon-fa',
        'type'  => 'icon',
        'title' => 'Select Font Awesome Icon',
        'dependency' => array( 'icon-type', '==', 'fa' ),
      ),

      array(
        'id'             => 'icon-c',
        'type'           => 'media',
        'title'          => 'Upload Custom Icon',
        'library'        => 'image',
        'placeholder'    => 'No Icon selected',
        'button_title'   => 'Add Icon',
        'remove_title'   => 'Remove Icon',
        'preview_width'  => '50',
        'preview_height' => '50',
        'dependency'     => array( 'icon-type', '==', 'c' ),
      ),

      array(
        'id'         => 'dimention',
        'type'       => 'dimensions',
        'title'      => 'Custom Icon Size',
        'desc'       => __( 'Size in "px"', 'tourfic' ),
        'show_units' => false,
        'height'     => false,
        'default'    => array(
          'width' => '20',
        ),
        'dependency' => array( 'icon-type', '==', 'c' ),
      ),

    )
  ) );

}

?>