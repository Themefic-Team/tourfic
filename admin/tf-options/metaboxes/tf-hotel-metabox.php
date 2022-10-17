<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Metabox::metabox( 'tf_hotels', array(
	'title'     => 'Hotel Settings',
	'post_type' => 'tf_hotel',
	'sections'  => array(
		'section_2' => array(
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
