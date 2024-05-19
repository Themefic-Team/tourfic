<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_apartment_location', array(
	'title'    => esc_html__( 'Apartment Location Settings', 'tourfic' ),
	'taxonomy' => 'apartment_location',
	'fields'   => array(
		array(
			'id'    => 'image',
			'type'  => 'image',
			'title' => esc_html__( 'Upload location photo', 'tourfic' ),
		),
	),
) );
