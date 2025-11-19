<?php
// don't load directly
defined('ABSPATH') || exit;

TF_Taxonomy_Metabox::taxonomy('tf_hotel_location', apply_filters('tf_hotel_location_metabox_args', array(
	'title'    => esc_html__('Hotel Settings', 'tourfic'),
	'taxonomy' => 'hotel_location',
	'fields'   => array(
		array(
			'id'    => 'image',
			'type'  => 'image',
			'title' => esc_html__('Upload location photo', 'tourfic'),
		),
	),
)));
