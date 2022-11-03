<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_hotel_location', array(
	'title'    => __( 'Hotel Settings', 'tourfic' ),
	'taxonomy' => 'hotel_location',
	'fields'   => array(
		array(
			'id'    => 'image',
			'type'  => 'image',
			'title' => __( 'Upload location photo', 'tourfic' ),
		),
	),
) );
