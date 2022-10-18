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
					'placeholder' => 'Address of the hotel',
					'description' => 'Address of the hotel',
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
				array(
					'id'          => 'phone',
					'title'       => 'Phone',
					'type'        => 'textarea',
					'description' => 'Phone of the hotel',
				),
				//checkbox
				array(
					'id'       => 'checky',
					'type'     => 'checkbox',
					'title'    => 'Checkbox',
					'subtitle' => 'Checkbox',
					'description'     => 'Checkbox',
					'options'  => array(
						'1' => 'Checkbox 1',
						'2' => 'Checkbox 2',
						'3' => 'Checkbox 3',
					),
					'inline'  => true,
				),
				//radio
				array(
					'id'       => 'radio',
					'type'     => 'radio',
					'title'    => 'Radio',
					'subtitle' => 'Radio',
					'description'     => 'Radio',
					'options'  => array(
						'1' => 'Radio 1',
						'2' => 'Radio 2',
						'3' => 'Radio 3',
					),
					'inline'  => true,
				),

			),
		),
		'section_3' => array(
			'title'  => 'Section 1',
			'fields' => array(
				array(
					'id'          => 'zip',
					'title'       => 'Zip',
					'type'        => 'text',
					'placeholder' => 'Zip of the hotel',
					'description' => 'Zip of the hotel',
				),
			),
		),
	),
) );
