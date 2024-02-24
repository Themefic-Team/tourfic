<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tour_attraction', array(
	'title'    => __( 'Tour Settings', 'tourfic' ),
	'taxonomy' => 'tour_attraction',
	'fields'   => array(
		array(
			'id'      => 'icon-type',
			'type'    => 'select',
			'title'   => __( 'Select Icon type', 'tourfic' ),
			'options' => array(
				'fa' => __( 'Font Awesome', 'tourfic' ),
				'c'  => __( 'Custom', 'tourfic' ),
			),
			'default' => 'fa'
		),

		array(
			'id'         => 'icon-fa',
			'type'       => 'icon',
			'title'      => __( 'Select Font Awesome Icon', 'tourfic' ),
			'dependency' => array( 'icon-type', '==', 'fa' ),
		),
		array(
			'id'             => 'icon-c',
			'type'           => 'image',
			'label'          => __( 'Upload Custom Icon', 'tourfic' ),
			'placeholder'    => __( 'No Icon selected', 'tourfic' ),
			'button_title'   => __( 'Add Icon', 'tourfic' ),
			'remove_title'   => __( 'Remove Icon', 'tourfic' ),
			'preview_width'  => '50',
			'preview_height' => '50',
			'dependency'     => array( 'icon-type', '==', 'c' ),
		),
		array(
			'id'          => 'dimention',
			'type'        => 'number',
			'label'       => __( 'Custom Icon Size', 'tourfic' ),
			'description' => __( 'Size in "px"', 'tourfic' ),
			'show_units'  => false,
			'height'      => false,
			'default'     => '20',
			'dependency'  => array( 'icon-type', '==', 'c' ),
		),
	),
) );
