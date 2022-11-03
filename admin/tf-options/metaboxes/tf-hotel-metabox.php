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
			'title'  => __('Location', 'tourfic'),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'          => 'address',
					'type'        => 'textarea',
					'label'       => __( 'Hotel Address', 'tourfic' ),
					'subtitle'    => __( 'Enter hotel adress', 'tourfic' ),
					'placeholder' => __( 'Address', 'tourfic' ),
					'attributes'  => array(
						'required' => 'required',
					),
				),
				array(
					'id'       => '',
					'type'     => 'map',
					'is_pro'   => true,
					'label'    => __( 'Location on Map', 'tourfic' ),
					'subtitle' => __( 'Select one location on the map to see latitude and longitude', 'tourfic' ),
					'height'   => '250px',
					'settings' => array(
						'scrollWheelZoom' => true,
					),
				),
			),
		),
		// Hotel Details
		'hotel_details'    => array(
			'title'  => __( 'Hotel Details', 'tourfic' ),
			'icon'   => 'fa-solid fa-hotel',
			'fields' => array(
				array(
					'id'       => 'gallery',
					'type'     => 'gallery',
					'label'    => __( 'Hotel Gallery', 'tourfic' ),
					'subtitle' => __( 'Upload one or many images to make a hotel image gallery for customers', 'tourfic' ),
				),
				array(
					'id'        => 'featured',
					'type'      => 'switch',
					'label'     => __( 'Featured Hotel', 'tourfic' ),
					'is_pro'    => true,
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false,
				),
				array(
					'id'          => 'hotel-video',
					'type'        => 'text',
					'label'       => __( 'Hotel Video', 'tourfic' ),
					'is_pro'      => true,
					'badge_up'    => true,
					'subtitle'    => __( 'Enter YouTube/Vimeo URL here', 'tourfic' ),
					'validate'    => 'csf_validate_url',
					'placeholder' => __( '', 'tourfic' ),
					'dependency'  => array( 'featured', '==', '1' ),
				),
			),
		),
		// Hotel Details
		'hotel_service'    => array(
			'title'  => __( 'Hotel Services', 'tourfic' ),
			'icon'   => 'fa-solid fa-van-shuttle',
			'fields' => array(
				array(
					'id'       => 'hotel-service',
					'type'     => 'switch',
					'label'    => __( 'Pickup Service', 'tourfic' ),
					'subtitle' => __( 'Airport Service', 'tourfic' ),
					'default'  => false,
				)
			),
		),
		// Check-in check-out
		'check_time'       => array(
			'title'  => __( 'Check in/out Time', 'tourfic' ),
			'icon'   => 'fa-solid fa-clock-rotate-left',
			'fields' => array(
				array(
					'id'           => '',
					'type'         => 'switch',
					'label'        => __( 'Allowed Full Day Booking', 'tourfic' ),
					'is_pro'       => true,
					'badge_up_pro' => true,
					'subtitle'     => __( 'You can book room with full day', 'tourfic' ),
					'desc'         => __( 'E.g: booking from 22 -23, then all days 22 and 23 are full, other people cannot book', 'tourfic' ),
				),

			),
		),
		// Room Details
		'room_details'     => array(
			'title'  => __( 'Room Details', 'tourfic' ),
			'icon'   => 'fa-sharp fa-solid fa-door-open',
			'fields' => array(
				array(
					'id'           => 'room',
					'type'         => 'repeater',
					'label'        => __( 'Room Details', 'tourfic' ),
					'button_title' => __( 'Add New Room', 'tourfic' ),
					'class'        => 'room-repeater',
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
							'default'   => true,
						),
						array(
							'id'          => 'title',
							'type'        => 'text',
							'subtitle'    => __( 'Enter your room title', 'tourfic' ),
							'label'       => __( 'Room Title', 'tourfic' ),
							'placeholder' => __( '', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'num-room',
							'type'        => 'number',
							'label'       => __( 'Number of Rooms', 'tourfic' ),
							'subtitle'    => __( 'Number of available rooms for booking', 'tourfic' ),
							'placeholder' => __( '', 'tourfic' ),
							'field_width' => 50,
							'attributes'  => array(
								'min' => '0',
							),
						),
						array(
							'id'        => '',
							'type'      => 'switch',
							'label'     => __( 'Reduce Number of Rooms by Orders', 'tourfic' ),
							'is_pro'    => true,
							'subtitle'  => __( 'Reduce the number of available rooms for booking by WooCommerce orders details', 'tourfic' ),
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
							'id'     => 'gallery',
							'type'   => 'gallery',
							'label'  => __( 'Gallery', 'tourfic' ),
							'is_pro' => true,
						),
						array(
							'id'          => 'bed',
							'type'        => 'number',
							'label'       => __( 'Number of Beds', 'tourfic' ),
							'subtitle'    => __( 'Number of beds present in the room', 'tourfic' ),
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
							'subtitle'    => __( 'Max number of persons allowed in the room', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'footage',
							'type'        => 'text',
							'label'       => __( 'Room Footage', 'tourfic' ),
							'subtitle'    => __( 'Room footage (sft)', 'tourfic' ),
							'field_width' => 33.33,
						),
						array(
							'id'    => 'subtitle',
							'type'  => 'textarea',
							'label' => __( 'Room subtitle', 'tourfic' ),
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
							'label'   => __( 'Pricing by', 'tourfic' ),
							'options' => array(
								'1' => __( 'Per room', 'tourfic' ),
								'2' => __( 'Per person (Pro)', 'tourfic' ),
							),
							'default' => '1'
						),
						array(
							'id'         => 'price',
							'type'       => 'text',
							'label'      => __( 'Pricing', 'tourfic' ),
							'subtitle'   => __( 'The price of room per one night', 'tourfic' ),
							'dependency' => array( 'pricing-by', '==', '1' ),
						),
						array(
							'id'         => '',
							'type'       => 'text',
							'label'      => __( 'Adult Pricing', 'tourfic' ),
							'is_pro'     => true,
							'desc'       => __( 'The price of room per one night', 'tourfic' ),
							'dependency' => array( 'pricing-by', '==', '2' ),
						),

						array(
							'id'         => '',
							'type'       => 'text',
							'label'      => __( 'Children Pricing', 'tourfic' ),
							'is_pro'     => true,
							'desc'       => __( 'The price of room per one night', 'tourfic' ),
							'dependency' => array( 'pricing-by', '==', '2' ),
						),
						array(
							'id'        => 'price_multi_day',
							'type'      => 'switch',
							'label'     => __( 'Multiply Pricing By Night', 'tourfic' ),
							'subtitle'  => __( 'During booking pricing will be multiplied by number of nights (Check-in to Check-out)', 'tourfic' ),
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


								array(
									'id'       => '',
									'type'     => 'text',
									'label'    => __( 'Pricing', 'tourfic' ),
									'subtitle' => __( 'The price of room per one night', 'tourfic' ),
									'is_pro'   => true,
								),

								array(
									'id'       => '',
									'type'     => 'text',
									'label'    => __( 'Adult Pricing', 'tourfic' ),
									'subtitle' => __( 'The price of room per one night', 'tourfic' ),
									'is_pro'   => true,
								),

								array(
									'id'       => '',
									'type'     => 'text',
									'title'    => __( 'Children Pricing', 'tourfic' ),
									'subtitle' => __( 'The price of room per one night', 'tourfic' ),
									'is_pro'   => true,
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
							'type'  => 'textarea',
							'label' => __( 'Description', 'tourfic' ),
						),

					),
				),
			),
		),
		// Terms & conditions
		'terms_conditions' => array(
			'title'  => __( 'Terms & conditions', 'tourfic' ),
			'icon'   => 'fa-regular fa-square-check',
			'fields' => array(
				array(
					'id'    => 'tc',
					'type'  => 'editor',
					'label' => __( 'Terms & Conditions', 'tourfic' ),
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
					'id'     => 'notice',
					'type'   => 'notice',
					'notice' => 'success',
					'label'  => __( 'These settings will overwrite global settings', 'tourfic' ),
				),

			),
		),
	),
) );
