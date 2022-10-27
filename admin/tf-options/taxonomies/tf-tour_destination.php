<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_tour_destination', array(
	'title'    => 'Tour Settings',
	'taxonomy' => 'tour_destination',
	'fields'   => array(
		array(
			'id'          => 'tourfic-image',
			'label'       => 'Upload Destination photo',
			'type'        => 'image',
		),
	),
) );
