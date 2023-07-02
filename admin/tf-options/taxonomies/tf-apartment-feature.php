<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_apartment_feature', array(
	'title'    => __( '', 'tourfic' ),
	'taxonomy' => 'apartment_feature',
	'fields'   => array(
		array(
			'id'      => 'icon-type',
			'type'    => 'select',
			'title'   => __( 'Select Icon type', 'tourfic' ),
			'options' => array(
				'icon'   => __( 'Icon Library', 'tourfic' ),
				'custom' => __( 'Custom Icon', 'tourfic' ),
			),
			'default' => 'icon'
		),

		array(
			'id'         => 'apartment-feature-icon',
			'type'       => 'icon',
			'title'      => __( 'Select Icon', 'tourfic' ),
			'dependency' => array( 'icon-type', '==', 'icon' ),
		),
		array(
			'id'             => 'apartment-feature-icon-custom',
			'type'           => 'image',
			'label'          => __( 'Upload Custom Icon', 'tourfic' ),
			'placeholder'    => __( 'No Icon selected', 'tourfic' ),
			'button_title'   => __( 'Add Icon', 'tourfic' ),
			'remove_title'   => __( 'Remove Icon', 'tourfic' ),
			'preview_width'  => '50',
			'preview_height' => '50',
			'dependency'     => array( 'icon-type', '==', 'custom' ),
		),
		array(
			'id'          => 'apartment-feature-icon-dimension',
			'type'        => 'number',
			'label'       => __( 'Custom Icon Size', 'tourfic' ),
			'description' => __( 'Size in "px"', 'tourfic' ),
			'show_units'  => false,
			'height'      => false,
			'default'     => '20',
			'dependency'  => array( 'icon-type', '==', 'custom' ),
		),
	),
) );
