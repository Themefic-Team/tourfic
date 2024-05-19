<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_tour_destination', array(
	'title'    => esc_html__( 'Tour Settings', 'tourfic' ),
	'taxonomy' => 'tour_destination',
	'fields'   => array(
		array(
			'id'    => 'image',
			'label' => esc_html__( 'Upload destination photo', 'tourfic' ),
			'type'  => 'image',
		),
	),
) );
