<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Metabox::metabox( 'tf_tours', array(
	'title'     => __( 'Tour Setting', 'tourfic' ),
	'post_type' => 'tf_tours',
	'sections'  => array(
		// General
		'general' => array(
			'title'  => __( 'General', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'       => 'tour_as_featured',
					'type'     => 'switch',
					'label'    => __( 'Set this tour as featured', 'tourfic' ),
					'subtitle' => __( 'Tour will be shown under featured sections', 'tourfic' ),
				),
			),
		),


	),
) );
