<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_carrental_location', array(
	'title'    => esc_html__( 'Car Rental Location Settings', 'tourfic' ),
	'taxonomy' => 'carrental_location',
	'fields'   => array(
		array(
			'id'    => 'image',
			'type'  => 'image',
			'title' => esc_html__( 'Upload Location Image photo', 'tourfic' ),
		),
	),
) );
