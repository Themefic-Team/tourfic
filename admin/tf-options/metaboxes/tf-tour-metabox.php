<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Metabox::metabox( 'tf_tours', array(
	'title'     => 'Tour Settings',
	'post_type' => 'tf_tours',
	'sections'  => array(
		'section_1' => array(
			'title'  => 'Section 1',
			'fields' => array(
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
		),
	),
) );
