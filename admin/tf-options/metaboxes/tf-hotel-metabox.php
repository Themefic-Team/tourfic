<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Metabox::metabox( 'tf_hotels', array(
	'title'     => 'Hotel Settings',
	'post_type' => 'tf_hotel',
	'sections'  => array(
		'section_2' => array(
			'title'  => 'Section 1',
			'icon'   => 'ri-home-2-line',
			'fields' => array(
				/*array(
					'id'          => 'address',
					'label'       => 'Address',
					'type'        => 'text',
					'placeholder' => 'Address of the hotel',
					'description' => 'Address of the hotel',
					'class'       => 'tf-address',
				),
				array(
					'id'          => 'email',
					'label'       => 'Email',
					'type'        => 'select',
					'placeholder' => 'Email of the hotel',
					'options'     => array(
						'1' => 'Option 1',
						'2' => 'Option 2',
						'3' => 'Option 3',
					),
				),
				array(
					'id'          => 'phone',
					'label'       => 'Phone',
					'type'        => 'textarea',
					'placeholder' => 'Phone of the hotel',
					'description' => 'Phone of the hotel',
				),
				//checkbox
				array(
					'id'          => 'checky',
					'type'        => 'checkbox',
					'label'       => 'Checkbox',
					'subtitle'    => 'Checkbox',
					'description' => 'Checkbox',
					'options'     => array(
						'1' => 'Checkbox 1',
						'2' => 'Checkbox 2',
						'3' => 'Checkbox 3',
					),
				),
				array(
					'id'          => 'check',
					'type'        => 'checkbox',
					'label'       => 'Checkbox',
					'title'       => 'Single checkbox',
					'subtitle'    => 'Checkbox',
					'description' => 'Checkbox',
				),
				//heading
				array(
					'id'      => 'heading',
					'type'    => 'heading',
					'title'   => 'Awesome heading',
					'content' => 'gsdgsdfgfg gsdfg d<a href="http://localhost:8080">dsfgsdfgsdf</a>',
				),
				//notice
				array(
					'id'      => 'heading',
					'type'    => 'notice',
					'icon'    => 'ri-information-fill',
					'title'   => 'Awesome heading',
					'content' => 'gsdgsdfgfg gsdfg d<a href="http://localhost:8080">dsfgsdfgsdf</a>',
					'notice'  => 'info',
				),

				//radio
				array(
					'id'          => 'radio',
					'type'        => 'radio',
					'label'       => 'Radio',
					'subtitle'    => 'Radio',
					'description' => 'Radio',
					'options'     => array(
						'1' => 'Radio 1',
						'2' => 'Radio 2',
						'3' => 'Radio 3',
					),
					'inline'      => true,
				),
				//switch
				array(
					'id'          => 'switch',
					'type'        => 'switch',
					'label'       => 'Want to disable?',
					'description' => 'This is a description',
					'label_on'    => 'Enable',
					'label_off'   => 'Disable',
					'width'       => 100,
					'default'     => true,
				),*/
				//date
				array(
					'id'          => 'datedsd',
					'type'        => 'date',
					'label'       => 'Date',
					'placeholder' => 'Select a date',
					'description' => 'This is a description',
					'format'      => 'Y/m/d',
					'range'       => true,
					'label_from'  => 'Start Date',
					'label_to'    => 'End Date'
				),
//				array(
//					'id'          => 'date',
//					'type'        => 'date',
//					'label'       => 'Date',
//					'placeholder' => 'Select a date',
//					'description' => 'This is a description',
//					'format'      => 'Y/m/d',
//					'multiple'    => true,
//				),
				array(
					'id'          => 'checky',
					'type'        => 'checkbox',
					'label'       => 'Checkbox',
					'subtitle'    => 'Checkbox',
					'description' => 'Checkbox',
					'options'     => array(
						'1' => 'Checkbox 1',
						'2' => 'Checkbox 2',
						'3' => 'Checkbox 3',
					),
				),
				array(
					'id'          => 'radio',
					'type'        => 'radio',
					'label'       => 'Radio',
					'subtitle'    => 'Radio',
					'description' => 'Radio',
					'options'     => array(
						'1' => 'Radio 1',
						'2' => 'Radio 2',
						'3' => 'Radio 3',
					),
					'inline'      => true,
				),
				//time
				array(
					'id'          => 'time',
					'type'        => 'time',
					'label'       => 'Time',
					'placeholder' => 'Select a time',
					'description' => 'This is a description',
					'format'      => 'h:i K',
				),
				array(
					'id'          => 'features',
					'type'        => 'select2',
					'label'       => 'Select 2',
					'placeholder' => 'Select a time',
					'description' => 'This is a description',
					'multiple'    => true,
					'options'     => array(
						'1' => 'Option 1',
						'2' => 'Option 2',
						'3' => 'Option 3',
					),
				),
				//image
				/*array(
					'id'          => 'image',
					'type'        => 'image',
					'label'       => 'Features',
					'description' => 'This is a description',
				),
				array(
					'id'          => 'gallery',
					'type'        => 'gallery',
					'label'       => 'Features',
					'description' => 'This is a description',
				),*/

						//checkbox
						array(
							'id'          => 'checky',
							'type'        => 'checkbox',
							'label'       => 'Checkbox',
							'subtitle'    => 'Checkbox',
							'description' => 'Checkbox',
							'options'     => array(
								'1' => 'Checkbox 1',
								'2' => 'Checkbox 2',
								'3' => 'Checkbox 3',
							),
						),
						array(
							'id'          => 'check',
							'type'        => 'checkbox',
							'label'       => 'Checkbox',
							'title'       => 'Single checkbox',
							'subtitle'    => 'Checkbox',
							'description' => 'Checkbox',
						), 

						//date
						array(
							'id'          => 'datedsd',
							'type'        => 'date',
							'label'       => 'Date',
							'placeholder' => 'Select a date',
							'description' => 'This is a description',
							'format'      => 'Y/m/d',
							'range'       => true,
							'label_from'  => 'Start Date',
							'label_to'    => 'End Date'
						),
						array(
							'id'          => 'date',
							'type'        => 'date',
							'label'       => 'Date',
							'placeholder' => 'Select a date',
							'description' => 'This is a description',
							'format'      => 'Y/m/d',
							'multiple'    => true,
						),
						array(
							'id'          => 'date1',
							'type'        => 'date',
							'label'       => 'Date 1',
							'placeholder' => 'Select a date',
							'description' => 'This is a description',
							'format'      => 'd/m/Y',
						),
						//time
						array(
							'id'          => 'time',
							'type'        => 'time',
							'label'       => 'Time',
							'placeholder' => 'Select a time',
							'description' => 'This is a description',
							'format'      => 'h:i K',
						),
						array(
							'id'          => 'features',
							'type'        => 'select2',
							'label'       => 'Features',
							'placeholder' => 'Select a time',
							'description' => 'This is a description',
							'multiple'    => true,
							'options'     => array(
								'1' => 'Option 1',
								'2' => 'Option 2',
								'3' => 'Option 3',
							),
						),
					)
				),
			),
		),
		'section_3' => array(
			'title'  => 'Section 1',
			'icon'   => 'fa-solid fa-user-pen',
			'fields' => array(
				array(
					'id'          => 'zip',
					'title'       => 'Zip',
					'type'        => 'text',
					'placeholder' => 'Zip of the hotel',
					'description' => 'Zip of the hotel',
				),
				array(
					'id'          => 'googlemap',
					'label'       => 'Textarea 2',
					'type'        => 'map',
					'description' => 'room details hotel',
				),
				array(
					'id'          => 'googlemap-testing',
					'label'       => 'Textarea 2',
					'type'        => 'map',
					'description' => 'room details hotel',
				),
			),
		),
	),
) );
