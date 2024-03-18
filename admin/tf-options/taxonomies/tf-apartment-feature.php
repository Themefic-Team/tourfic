<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_apartment_feature', array(
	'title'    => '',
	'taxonomy' => 'apartment_feature',
	'fields'   => array(
		array(
			'id'      => 'icon-type',
			'type'    => 'select',
			'title'   => esc_html__( 'Select Icon type', 'tourfic' ),
			'options' => array(
				'icon'   => esc_html__( 'Icon Library', 'tourfic' ),
				'custom' => esc_html__( 'Custom Icon', 'tourfic' ),
			),
			'default' => 'icon'
		),

		array(
			'id'         => 'apartment-feature-icon',
			'type'       => 'icon',
			'title'      => esc_html__( 'Select Icon', 'tourfic' ),
			'dependency' => array( 'icon-type', '==', 'icon' ),
		),
		array(
			'id'             => 'apartment-feature-icon-custom',
			'type'           => 'image',
			'label'          => esc_html__( 'Upload Custom Icon', 'tourfic' ),
			'placeholder'    => esc_html__( 'No Icon selected', 'tourfic' ),
			'button_title'   => esc_html__( 'Add Icon', 'tourfic' ),
			'remove_title'   => esc_html__( 'Remove Icon', 'tourfic' ),
			'preview_width'  => '50',
			'preview_height' => '50',
			'dependency'     => array( 'icon-type', '==', 'custom' ),
		),
		array(
			'id'          => 'apartment-feature-icon-dimension',
			'type'        => 'number',
			'label'       => esc_html__( 'Custom Icon Size', 'tourfic' ),
			'description' => esc_html__( 'Size in "px"', 'tourfic' ),
			'show_units'  => false,
			'height'      => false,
			'default'     => '20',
			'dependency'  => array( 'icon-type', '==', 'custom' ),
		),
	),
) );
