<?php
// don't load directly
defined( 'ABSPATH' ) || exit;
$badge_up     = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span></div>';
$badge_pro    = '<div class="tf-csf-badge"><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';

TF_Metabox::metabox( 'tf_hotels_opt', array(
	'title'     => 'Hotel Settings',
	'post_type' => 'tf_hotel',
	'sections'  => array(
		'location'         => array(
			'title'  => __( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'          => 'address',
					'type'        => 'textarea',
					'label'       => __( 'Hotel Address', 'tourfic' ),
					'subtitle'    => __( 'The address you want to show below the Hotel Title', 'tourfic' ),
					'placeholder' => __( 'e.g. 123 ABC Road, Toronto, Ontario 20100', 'tourfic' ),
					'attributes'  => array(
						'required' => 'required',
					),
				),
				array(
					'id'       => 'map',
					'class'    => 'gmaps',
					'type'     => 'map',
					'label'    => __( 'Dynamic Location Search', 'tourfic' ),
					'subtitle' => __( 'Write your desired address and select the address from the suggestions. This address will be used to hyperlink the hotel address on the frontend.', 'tourfic' ),
					'height'   => '250px',
					'settings' => array(
						'scrollWheelZoom' => true,
					),
				),
			),
		),
		// Hotel Details
		'hotel_details'    => array(
			'title'  => __( 'Gallery & Video', 'tourfic' ),
			'icon'   => 'fa-solid fa-hotel',
			'fields' => array(
				array(
					'id'       => 'gallery',
					'type'     => 'gallery',
					'label'    => __( 'Hotel Gallery', 'tourfic' ),
					'subtitle' => __( 'Upload one or many images to create a hotel image gallery for customers. This is common gallery visible at the top part of the hotel page', 'tourfic' ),
				),
				array(
					'id'        => 'featured',
					'type'      => 'switch',
					'label'     => __( 'Featured Hotel', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false,
				),
				array(
					'id'          => 'video',
					'type'        => 'text',
					'label'       => __( 'Hotel Video', 'tourfic' ),
					'subtitle'    => __( 'Enter YouTube/Vimeo URL here', 'tourfic' ),
					'placeholder' => __( 'Input full url here', 'tourfic' ),
				),
			),
		),
		// Hotel Details
		'hotel_service'    => array(
			'title'  => __( 'Hotel Services', 'tourfic' ),
			'icon'   => 'fa-solid fa-van-shuttle',
			'fields' => array(
				array(
					'id'      => 'hotel-service',
					'type'    => 'switch',
					'label'   => __( 'Airport Pickup Service', 'tourfic' ),
					'default' => true,
					'is_pro'  => true,
				)
			),
		),
		// Room Details
		'room_details'     => array(
			'title'  => __( 'Room Management', 'tourfic' ),
			'icon'   => 'fa-sharp fa-solid fa-door-open',
			'fields' => array(
				array(
					'id'           => 'room',
					'type'         => 'repeater',
					'label'        => __( 'Insert / Create your hotel rooms', 'tourfic' ),
					'button_title' => __( 'Add New Room', 'tourfic' ),
					'class'        => 'room-repeater',
					'max'          => 5,
					'fields'       => array(
						array(
							'id'          => 'unique_id',
							'class'       => 'unique-id',
							'type'        => 'text',
							'label'       => __( 'Unique ID', 'tourfic' ),
							'attributes'  => array(
								'readonly' => 'readonly',
							),
							'placeholder' => __( '', 'tourfic' ),
						),
						array(
							'id'          => 'order_id',
							'class'       => 'tf-order_id',
							'type'        => 'text',
							'label'       => __( 'Order ID', 'tourfic' ),
							'attributes'  => array(
								'readonly' => 'readonly',
							),
							'placeholder' => __( '', 'tourfic' ),
						),
						array(
							'id'        => 'enable',
							'type'      => 'switch',
							'label'     => __( 'Status', 'tourfic' ),
							'subtitle'  => __( 'Enable/disable this Room', 'tourfic' ),
							'label_on'  => __( 'Enabled', 'tourfic' ),
							'label_off' => __( 'Disabled', 'tourfic' ),
							'width'     => 100,
							'default'   => 1,
						),
						array(
							'id'          => 'title',
							'type'        => 'text',
							'subtitle'    => __( 'e.g. Superior Queen Room with Two Queen Beds', 'tourfic' ),
							'label'       => __( 'Room Title', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'num-room',
							'type'        => 'number',
							'label'       => __( 'Room Availability', 'tourfic' ),
							'subtitle'    => __( 'Number of rooms available for booking', 'tourfic' ),
							'field_width' => 50,
							'attributes'  => array(
								'min' => '0',
							),
						),
						array(
							'id'        => '',
							'type'      => 'switch',
							'is_pro'    => true,
							'label'     => __( 'Room Inventory Management', 'tourfic' ),
							'subtitle'  => __( 'Reduce total number of available rooms once a rooms is booked by a customer', 'tourfic' ),
							'label_on'  => __( 'Yes', 'tourfic' ),
							'label_off' => __( 'No', 'tourfic' ),
							'default'   => false,
						),

						array(
							'id'      => 'Details',
							'type'    => 'heading',
							'content' => __( 'Details', 'tourfic' ),
							'class'   => 'tf-field-class',
						),
						array(
							'id'       => 'gallery',
							'type'     => 'gallery',
							'label'    => __( 'Room Gallery', 'tourfic' ),
							'subtitle' => __( 'Upload images specific to this room', 'tourfic' ),
							'is_pro'   => true,
						),
						array(
							'id'          => 'bed',
							'type'        => 'number',
							'label'       => __( 'Number of Beds', 'tourfic' ),
							'subtitle'    => __( 'Number of beds available in the room', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'adult',
							'type'        => 'number',
							'label'       => __( 'Number of Adults', 'tourfic' ),
							'subtitle'    => __( 'Max number of persons allowed in the room', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'child',
							'type'        => 'number',
							'label'       => __( 'Number of Children', 'tourfic' ),
							'subtitle'    => __( 'Max number of children allowed in the room', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'children_age_limit',
							'type'        => 'number',
							'is_pro'      => true,
							'label'       => __( 'Children age limit', 'tourfic' ),
							'subtitle'    => __( 'Maximum age of a children', 'tourfic' ),
							'description' => __( 'keep blank if don\'t want to add', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'footage',
							'type'        => 'text',
							'label'       => __( 'Room Footage', 'tourfic' ),
							'subtitle'    => __( 'Room footage (in sft)', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'features',
							'type'        => 'select2',
							'label'       => __( 'Select Features', 'tourfic' ),
							'subtitle'    => __( 'e.g. Coffee Machine, Microwave Oven (Select as many as applicable). You need to create these features from the “Features” tab.', 'tourfic' ),
							'placeholder' => __( 'Select', 'tourfic' ),
							'multiple'    => true,
							'options'     => 'terms',
							'query_args'  => array(
								'taxonomy'   => 'hotel_feature',
								'hide_empty' => false,
							),
							'field_width' => 50,
						),
						array(
							'id'    => 'description',
							'type'  => 'editor',
							'label' => __( 'Room Description', 'tourfic' ),
						),
						array(
							'id'      => 'Pricing',
							'type'    => 'heading',
							'content' => __( 'Pricing', 'tourfic' ),
							'class'   => 'tf-field-class',
						),
						array(
							'id'      => 'pricing-by',
							'type'    => 'select',
							'label'   => __( 'Room Pricing Type', 'tourfic' ),
							'options' => array(
								'1' => __( 'Per room', 'tourfic' ),
								'2' => __( 'Per person (Pro)', 'tourfic' ),
							),
							'default' => '1'
						),
						array(
							'id'         => 'price',
							'type'       => 'text',
							'label'      => __( 'Insert Your Price', 'tourfic' ),
							'subtitle'   => __( 'The price of room per one night', 'tourfic' ),
							'dependency' => array( 'pricing-by', '==', '1' ),
						),
						array(
							'id'          => '',
							'type'        => 'text',
							'label'       => __( 'Price per Adult', 'tourfic' ),
							'is_pro'      => true,
							'dependency'  => array( 'pricing-by', '==', '2' ),
							'field_width' => 50,
						),

						array(
							'id'          => '',
							'type'        => 'text',
							'label'       => __( 'Price per Children', 'tourfic' ),
							'is_pro'      => true,
							'dependency'  => array( 'pricing-by', '==', '2' ),
							'field_width' => 50,
						),
						array(
							'id'        => 'price_multi_day',
							'type'      => 'switch',
							'label'     => __( 'Multiply Pricing By Night', 'tourfic' ),
							'subtitle'  => __( 'During booking, pricing will be multiplied by number of nights (Check-in to Check-out)', 'tourfic' ),
							'label_on'  => __( 'Yes', 'tourfic' ),
							'label_off' => __( 'No', 'tourfic' ),
							'default'   => true,
						),
						array(
							'id'      => 'Deposit',
							'type'    => 'heading',
							'content' => __( 'Deposit', 'tourfic' ),
							'class'   => 'tf-field-class',
						),
						array(
							'id'      => '',
							'type'    => 'switch',
							'label'   => __( 'Enable Deposit', 'tourfic' ),
							'is_pro'  => true,
							'default' => false,
						),
						array(
							'id'      => 'Availability',
							'type'    => 'heading',
							'content' => __( 'Availability', 'tourfic' ),
							'class'   => 'tf-field-class',
						),
						array(
							'id'      => '',
							'type'    => 'switch',
							'label'   => __( 'Enable Availability by Date', 'tourfic' ),
							'is_pro'  => true,
							'default' => true
						),
						array(
							'id'     => '',
							'class'  => 'repeater-by-date',
							'type'   => 'repeater',
							'title'  => __( 'By Date', 'tourfic' ),
							'is_pro' => true,
							'fields' => array(
								array(
									'id'          => '',
									'type'        => 'date',
									'label'       => __( 'Date Range', 'tourfic' ),
									'subtitle'    => __( 'Select availablity date range', 'tourfic' ),
									'placeholder' => __( '', 'tourfic' ),
									'class'       => 'tf-field-class',
									'format'      => 'Y/m/d',
									'range'       => true,
									'label_from'  => 'Start Date',
									'label_to'    => 'End Date',
									'multiple'    => true,
									'is_pro'      => true,
								),
								array(
									'id'       => '',
									'type'     => 'number',
									'label'    => __( 'Number of Rooms', 'tourfic' ),
									'subtitle' => __( 'Number of available rooms for booking on this date range', 'tourfic' ),
									'is_pro'   => true,
								),

								//Disable specific dates within this date range
								array(
									'id'         => '',
									'type'       => 'date',
									'label'      => __( 'Disable Specific Dates', 'tourfic' ),
									'is_pro'     => true,
									'format'     => 'Y/m/d',
									'label_from' => __( 'Start Date','tourfic'),
									'label_to'   => __('End Date','tourfic'),
									'multiple'   => true,
									'attributes' => array(
										'autocomplete' => 'off',
									),
								),

								array(
									'id'       => '',
									'type'     => 'text',
									'label'    => __( 'Pricing', 'tourfic' ),
									'subtitle' => __( 'The price of room per one night', 'tourfic' ),
									'is_pro'   => true,
								),

								array(
									'id'         => '',
									'type'       => 'text',
									'label'      => __( 'Adult Pricing', 'tourfic' ),
									'subtitle'   => __( 'The price of room per one night', 'tourfic' ),
									'is_pro'     => true,
									'dependency' => array(
										array( 'pricing-by', '==', '2' ),
									),
								),

								array(
									'id'         => '',
									'type'       => 'text',
									'title'      => __( 'Children Pricing', 'tourfic' ),
									'subtitle'   => __( 'The price of room per one night', 'tourfic' ),
									'is_pro'     => true,
									'dependency' => array(
										array( 'pricing-by', '==', '2' ),
									),
								),

							),
						),

					),
				)
			),
		),
		// FAQ Details
		'faq'              => array(
			'title'  => __( 'F.A.Q', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'           => 'faq',
					'type'         => 'repeater',
					'label'        => __( 'Frequently Asked Questions', 'tourfic' ),
					'button_title' => __( 'Add FAQ', 'tourfic' ),
					'fields'       => array(

						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
						),

						array(
							'id'    => 'description',
							'type'  => 'editor',
							'label' => __( 'Description', 'tourfic' ),
						),

					),
				),
			),
		),
		// Terms & conditions
		'terms_conditions' => array(
			'title'  => __( 'Terms & Conditions', 'tourfic' ),
			'icon'   => 'fa-regular fa-square-check',
			'fields' => array(
				array(
					'id'    => 'tc',
					'type'  => 'editor',
					'label' => __( 'Hotel Terms & Conditions', 'tourfic' ),
				),
			),
		),
		// Settings
		'settings'         => array(
			'title'  => __( 'Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-viruses',
			'fields' => array(
				array(
					'id'    => 'settings',
					'type'  => 'heading',
					'label' => __( 'Settings', 'tourfic' ),
					'class' => 'tf-field-class',
				),
				array(
					'id'        => 'h-review',
					'type'      => 'switch',
					'label'     => __( 'Disable Review Section', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false
				),

				array(
					'id'        => 'h-share',
					'type'      => 'switch',
					'label'     => __( 'Disable Share Option', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false
				),

				array(
					'id'      => 'notice',
					'type'    => 'notice',
					'notice'  => 'info',
					'content' => __( 'These settings will overwrite global settings', 'tourfic' ),
				),

			),
		),
	),
) );
