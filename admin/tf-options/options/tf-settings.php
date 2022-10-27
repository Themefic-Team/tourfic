<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Settings::option( 'tf-settings', array(
	'title'    => esc_html__( 'TF Settings', 'tourfic' ),
	'sections' => array(
		'general' => array(
			'title'  => esc_html__( 'General', 'tourfic' ),
			'fields' => array(
				array(
					'id'       => 'api_key',
					'label'       => esc_html__( 'API Key', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API Key', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
				array(
					'id'       => 'api_secret',
					'label'       => esc_html__( 'API Secret', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API Secret', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
				array(
					'id'       => 'api_url',
					'label'       => esc_html__( 'API URL', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API URL', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
				array(
					'id'       => 'api_version',
					'label'       => esc_html__( 'API Version', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API Version', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
				array(
					'id'       => 'api_language',
					'label'       => esc_html__( 'API Language', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API Language', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
			),
		),
		'general2' => array(
			'title'  => esc_html__( 'General', 'tourfic' ),
			'parent' => 'general',
			'fields' => array(
				array(
					'id'       => 'api_key',
					'label'       => esc_html__( 'API Key', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API Key', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
				array(
					'id'       => 'api_secret',
					'label'       => esc_html__( 'API Secret', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API Secret', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
				array(
					'id'       => 'api_url',
					'label'       => esc_html__( 'API URL', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API URL', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
				array(
					'id'       => 'api_version',
					'label'       => esc_html__( 'API Version', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API Version', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
				array(
					'id'       => 'api_language',
					'label'       => esc_html__( 'API Language', 'tourfic' ),
					'description' => esc_html__( 'Enter your TourFic API Language', 'tourfic' ),
					'type'        => 'text',
					'default'     => '',
				),
			),
		),
		'advanced' => array(
			'title'  => esc_html__( 'Advanced', 'tourfic' ),
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
	),
) );
