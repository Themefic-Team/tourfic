<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Taxonomy_Metabox::taxonomy( 'tf_hotel_feature', array(
	'title'    => 'Tour Settings',
	'taxonomy' => 'hotel_feature',
	'fields'   => array(
		array(
			'id'          => 'address',
			'title'       => 'Address',
			'type'        => 'text',
			'description' => 'Address of the hotel',
		),
		array(
			'id'          => 'phone',
			'title'       => 'Phone',
			'type'        => 'textarea',
			'description' => 'Phone of the hotel',
		),
		array(
			'id'      => 'email',
			'title'   => 'Email',
			'type'    => 'select',
			'options' => array(
				'1' => 'Option 1',
				'2' => 'Option 2',
				'3' => 'Option 3',
			),
		),
	),
) );
