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
			),
		),
	),
) );
