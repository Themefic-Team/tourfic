<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_carrental_brand', array(
	'title'    => esc_html__( 'Car Rental Brand Settings', 'tourfic' ),
	'taxonomy' => 'carrental_brand',
	'fields'   => array(
		array(
			'id'    => 'image',
			'type'  => 'image',
			'title' => esc_html__( 'Upload Brand Image photo', 'tourfic' ),
		),
	),
) );
