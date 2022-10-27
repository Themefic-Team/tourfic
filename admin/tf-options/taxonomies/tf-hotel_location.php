<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_hotel_location', array(
	'title'    => 'Tour Settings',
	'taxonomy' => 'hotel_location',
	'fields'   => array(
		array(
			'id'          => 'tourfic-image',
			'label'       => 'Upload location photo',
			'type'        => 'image',
		),
	),
) );
