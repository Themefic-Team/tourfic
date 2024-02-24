<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_apartment_location', array(
	'title'    => __( 'Apartment Location Settings', 'tourfic' ),
	'taxonomy' => 'apartment_location',
	'fields'   => array(
		array(
			'id'    => 'image',
			'type'  => 'image',
			'title' => __( 'Upload location photo', 'tourfic' ),
		),
	),
) );
