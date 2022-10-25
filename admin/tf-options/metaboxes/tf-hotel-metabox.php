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
				),*/
				array(
					'id'          => 'search_page',
					'label'       => 'Search Page',
					'type'        => 'select2',
					'placeholder' => 'Select a page',
					'description' => 'Select a page for search',
					'class'       => 'tf-search-page',
					'options'     => 'terms',
					'query_args'  => array(
						'taxonomy'   => 'hotel_feature',
						'hide_empty' => false,
					),
					'multiple'    => true,
				),
				/*array(
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
				//color
				array(
					'id'          => 'colorxxxx',
					'type'        => 'color',
					'label'       => 'Color',
					'description' => 'This is a description',
				),
				array(
					'id'       => 'colorss',
					'type'     => 'color',
					'label'    => 'Color2',
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'title'    => 'Title',
						'subtitle' => 'Subtitle',
						'content'  => 'Content',
					),
				),
				//icon
				array(
					'id'          => 'icon',
					'type'        => 'icon',
					'label'       => 'Icon',
					'default'     => 'fa fa-home',
					'description' => 'This is a description',
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

				//tab
				array(
					'id'    => 'opt-tabbed-1',
					'type'  => 'tab',
					'title' => 'Tabbed',
					'tabs'  => array(
						array(
							'id'     => 'tab-1',
							'title'  => 'Tab 1',
							'icon'   => 'fa fa-heart',
							'fields' => array(
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
							)
						),
						array(
							'id'     => 'tab-2',
							'title'  => 'tab 2',
							'icon'   => 'fa fa-gear',
							'fields' => array(
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
							)
						),
					)
				),

				//repeater
				array(
					'id'           => 'repeater2',
					'label'        => 'repeater 1',
					'button_title' => 'Add New Room',
					'type'         => 'repeater',
					'placeholder'  => 'Zip of the hotel',
					'description'  => 'Zip of the hotel',
					'fields'       => array(
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
						//time
						array(
							'id'          => 'time',
							'type'        => 'time',
							'label'       => 'Time',
							'placeholder' => 'Select a time',
							'description' => 'This is a description',
							'format'      => 'h:i K',
						),
						//color
						array(
							'id'          => 'colorzzzzzzzz',
							'type'        => 'color',
							'label'       => 'Color',
							'description' => 'This is a description',
						),
						array(
							'id'       => 'colorss',
							'type'     => 'color',
							'label'    => 'Color2',
							'multiple' => true,
							'inline'   => true,
							'colors'   => array(
								'title'    => 'Title',
								'subtitle' => 'Subtitle',
								'content'  => 'Content',
							),
						),
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
						//icon
						array(
							'id'          => 'icon',
							'type'        => 'icon',
							'label'       => 'Icon',
							'default'     => 'fa fa-home',
							'description' => 'This is a description',
						),
						array(
							'id'          => 'repeater_nested',
							'label'       => 'repeater 2',
							'type'        => 'repeater',
							'placeholder' => 'Zip of the hotel',
							'description' => 'Zip of the hotel',
							'fields'      => array(

								array(
									'id'          => 'name',
									'title'       => 'Name ',
									'type'        => 'text',
									'placeholder' => 'Address of the hotel',
									'description' => 'Address of the hotel',
								),
								array(
									'id'          => 'address',
									'title'       => 'Address',
									'type'        => 'text',
									'placeholder' => 'Address of the hotel',
									'description' => 'Address of the hotel',
								),
								array(
									'id'          => 'repeater_nested2',
									'label'       => 'repeater 3',
									'type'        => 'repeater',
									'placeholder' => 'Zip of the hotel',
									'description' => 'Zip of the hotel',
									'fields'      => array(

										array(
											'id'          => 'name',
											'title'       => 'Name ',
											'type'        => 'text',
											'placeholder' => 'Address of the hotel',
											'description' => 'Address of the hotel',
										),
										array(
											'id'          => 'address',
											'title'       => 'Address',
											'type'        => 'text',
											'placeholder' => 'Address of the hotel',
											'description' => 'Address of the hotel',
										),

									)
								),
							)
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
