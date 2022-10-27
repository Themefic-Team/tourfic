<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Settings::option( 'tf-settings', array(
	'title'    => esc_html__( 'TF Settings', 'tourfic' ),
	'sections' => array(
		'general' => array(
			'title'  => esc_html__( 'General', 'tourfic' ),
			'icon' => 'fa fa-cog',
		),
		'general1' => array(
			'title'  => esc_html__( 'General One', 'tourfic' ),
			'parent' => 'general',
			'icon' => 'fa fa-cog',
			'fields' => array(
				array(
					'id'       => 'api_key1',
					'label'       => esc_html__( 'API Key One', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API Key', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
			),
		),
		'general2' => array(
			'title'  => esc_html__( 'General Two', 'tourfic' ),
			'parent' => 'general',
			'icon' => 'fa fa-cog',
			'fields' => array(
				array(
					'id'       => 'api_key2',
					'label'       => esc_html__( 'API Key Two', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API Key', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
			),
		),
		'advanced' => array(
			'title'  => esc_html__( 'Advanced', 'tourfic' ),
			'icon' => 'fa fa-cog',
			'fields' => array(
				array(
					'id'          => 'search_page',
					'label'       => 'Search Page',
					'type'        => 'select2',
					'placeholder' => 'Select a page',
					'description' => 'Select a page for search',
					'class'       => 'tf-search-page',
					'options'     => 'terms',
					'query_args'  => array(
						'taxonomy'   => 'hotel_feature',
						'hide_empty' => false,
					),
					'multiple'    => true,
				),
			),
		),
		'new' => array(
			'title'  => esc_html__( 'Advanced New', 'tourfic' ),
			'icon' => 'fa fa-cog',
		),
		'new1' => array(
			'title'  => esc_html__( 'New One', 'tourfic' ),
			'parent' => 'new',
			'icon' => 'fa fa-cog',
			'fields' => array(
				array(
					'id'       => 'api_key3',
					'label'       => esc_html__( 'API Key three', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API Key', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
			),
		),
		'new2' => array(
			'title'  => esc_html__( 'New Two', 'tourfic' ),
			'parent' => 'new',
			'icon' => 'fa fa-cog',
			'fields' => array(
				array(
					'id'       => 'api_key4',
					'label'       => esc_html__( 'API Key four', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API Key', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
			),
		),
	),
) );
