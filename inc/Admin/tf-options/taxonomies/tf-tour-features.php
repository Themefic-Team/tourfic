<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tour_features', array(
	'taxonomy' => 'tour_features',
	'fields'   => array(
		array(
			'id'      => 'icon-type',
			'type'    => 'select',
			'title'   => esc_html__( 'Select Icon type', 'tourfic' ),
			'options' => array(
				'fa' => esc_html__( 'Font Awesome', 'tourfic' ),
				'c'  => esc_html__( 'Custom', 'tourfic' ),
			),
			'default' => 'fa'
		),

		array(
			'id'         => 'icon-fa',
			'type'       => 'icon',
			'title'      => esc_html__( 'Select Font Awesome Icon', 'tourfic' ),
			'dependency' => array( 'icon-type', '==', 'fa' ),
		),
		array(
			'id'             => 'icon-c',
			'type'           => 'image',
			'label'          => esc_html__( 'Upload Custom Icon', 'tourfic' ),
			'placeholder'    => esc_html__( 'No Icon selected', 'tourfic' ),
			'button_title'   => esc_html__( 'Add Icon', 'tourfic' ),
			'remove_title'   => esc_html__( 'Remove Icon', 'tourfic' ),
			'preview_width'  => '50',
			'preview_height' => '50',
			'dependency'     => array( 'icon-type', '==', 'c' ),
		),
		array(
			'id'          => 'dimention',
			'type'        => 'number',
			'label'       => esc_html__( 'Custom Icon Size', 'tourfic' ),
			'description' => esc_html__( 'Size in "px"', 'tourfic' ),
			'show_units'  => false,
			'height'      => false,
			'default'     => '20',
			'dependency'  => array( 'icon-type', '==', 'c' ),
		),
	),
) );
