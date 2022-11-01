<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_tour_destination', array(
	'title'    => 'Tour Settings',
	'taxonomy' => 'tour_destination',
	'fields'   => array(
		array(
			'id'          => 'image',
			'label'       => __( 'Upload destination photo', 'tourfic' ),
			'type'        => 'image',
		),
	),
) );
