<?php
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'CSF' ) ) {

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
				'id'      => 'icon-type',
				'type'    => 'button_set',
				'title'   => __('Select Icon type', 'tourfic'),
				'options' => array(
					'fa' => __('Font Awesome', 'tourfic'),
					'c'  => __('Custom', 'tourfic'),
				),
				'default' => 'fa'
			),

			array(
				'id'         => 'icon-fa',
				'type'       => 'icon',
				'title'      => __('Select Font Awesome Icon', 'tourfic'),
				'dependency' => array( 'icon-type', '==', 'fa' ),
			),

			array(
				'id'             => 'icon-c',
				'type'           => 'media',
				'title'          => __('Upload Custom Icon', 'tourfic'),
				'library'        => 'image',
				'placeholder'    => __('No Icon selected', 'tourfic'),
				'button_title'   => __('Add Icon', 'tourfic'),
				'remove_title'   => __('Remove Icon', 'tourfic'),
				'preview_width'  => '50',
				'preview_height' => '50',
				'dependency'     => array( 'icon-type', '==', 'c' ),
			),

			array(
				'id'         => 'dimention',
				'type'       => 'dimensions',
				'title'      => __('Custom Icon Size', 'tourfic'),
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