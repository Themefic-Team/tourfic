<?php
// don't load directly
defined( 'ABSPATH' ) || exit;
$badge_up     = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span></div>';
$badge_pro    = '<div class="tf-csf-badge"><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';

TF_Metabox::metabox( 'tf_hotels', array(
	'title'     => 'Hotel Settings',
	'post_type' => 'tf_hotel',
	'sections'  => array(
		'location' => array(
			'title'  => 'Location',
			'icon'   => 'ri-home-2-line',
			'fields' => array( 
				array(
					'id' => 'features',
					'type' => 'select',
					'label' => __( 'Select Features', 'tourfic' ),
					'subtitle' => 'Enter your subtitle',
					'placeholder' => __( 'Select', 'tourfic' ),
					'description' => 'Enter your description',
					'class' => 'tf-field-class',
					'hotel_feature', 
					'options' => 'terms',
					'query_args'  => array(
						'taxonomy'   => 'hotel_feature',
						'hide_empty' => false,
					),
					'multiple'    => true,
				),
				array(
					'id'         => 'icon-fa',
					'type'       => 'icon',
					'label'      => __('Select Font Awesome Icon', 'tourfic'),
				),
				array(
					'id'         => 'icon-fass',
					'type'       => 'icon',
					'label'      => __('Select Font Awesome Icon', 'tourfic'),
				),
				array(
					'id'       => 'disable-services',
					'type'     => 'checkbox',
					'label'    => __( 'Disable Services', 'tourfic' ),
					'subtitle' => __( 'Disable or hide the services you don\'t need by ticking the checkbox', 'tourfic' ),
					'options'  => array(
						'hotel' => __( 'Hotel', 'tourfic' ),
						'tour'  => __( 'Tour', 'tourfic' ),
					),
				),
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
					'is_pro'     => true,
					'label'    => __( 'Location on Map' , 'tourfic' ),
					'subtitle' => __( 'Select one location on the map to see latitude and longitude' , 'tourfic' ),
					'height'   => '250px',
					'settings' => array(
						'scrollWheelZoom' => true,
					),
				),
			),
		), 
		// Hotel Details
		'hotel_details' => array(
			'title'  => __( 'Hotel Details', 'tourfic' ),
			'icon'   => 'ri-inbox-fill',
			'fields' => array(
				array(
					'id'       => 'gallery',
					'type'     => 'gallery',
					'label'    => __( 'Hotel Gallery', 'tourfic' ),
					'subtitle' => __( 'Upload one or many images to make a hotel image gallery for customers', 'tourfic' ),
				),
				array(
					'id'       => 'featured', 
					'type'     => 'switch',
					'label'    => __( 'Featured Hotel', 'tourfic' ),
					// 'is_pro'     => true,
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
   					'default' => true,
				),
				array(
					'id'       => '', 
					'type'     => 'text',
					'label'    => __( 'Hotel Video', 'tourfic' ),
					'is_pro'     => true,
					'badge_up' => true,
					'desc'     => __( 'Enter YouTube/Vimeo URL here', 'tourfic' ),
					'validate' => 'csf_validate_url',
					'placeholder' => __( '', 'tourfic' ),
				),
			),
		),
		// Hotel Details
		'hotel_service' => array(
			'title'  => __( 'Hotel Services', 'tourfic' ),
			'icon'   => 'ri-home-2-line',
			'fields' => array(
				array(
					'id'       => '', 
					'type'     => 'switch',
					'is_pro'     => true,
					'label'    => __( 'Pickup Service', 'tourfic' ),
					'subtitle' => __( 'Airport Service', 'tourfic' ),
					'default'  => false,
				)
			),
		),
		// Check-in check-out
		'check_time' => array(
			'title'  => __( 'Check in/out Time', 'tourfic' ),
			'icon'   => 'ri-home-2-line',
			'fields' => array(
				array(
					'id'       => '', 
					'type'     => 'switch',
					'label'    => __( 'Allowed Full Day Booking', 'tourfic' ),
					'is_pro'     => true,
					'badge_up_pro' => true,
					'subtitle' => __( 'You can book room with full day', 'tourfic' ),
					'desc'     => __( 'E.g: booking from 22 -23, then all days 22 and 23 are full, other people cannot book', 'tourfic' ),
				),
	
			),
		),
		// Room Details
		'room_details' => array(
			'title'  => __( 'Room Details', 'tourfic' ),
			'icon'   => 'ri-home-2-line',
			'fields' => array(
				array(
					'id' 			=> 'room',
					'type' 			=> 'repeater',
					'label'  		=> __( 'Room Details', 'tourfic' ), 
					'button_title' 	=> __( 'Add New Room', 'tourfic' ), 
					'class' 		=> 'room-repeater',
					'fields'		=> array(
						array(
							'id'         => 'unique_id',
							'class'      => 'unique-id',
							'type'       => 'text',
							'label'      => __( 'Unique ID', 'tourfic' ),
							'attributes' => array(
								'readonly' => 'readonly',
							),
							'placeholder' => __( '', 'tourfic' ),
						),
						array(
							'id'         => 'order_id',
							'class'      => 'tf-order_id',
							'type'       => 'text',
							'label'      => __( 'Order ID', 'tourfic' ),
							'attributes' => array(
								'readonly' => 'readonly',
							),
							'placeholder' => __( '', 'tourfic' ),
						),
						array(
							'id'         => 'enable',
							'type'       => 'switch',
							'label'      => __( 'Status', 'tourfic' ),
							'subtitle'   => __( 'Enable/disable this Room', 'tourfic' ),
							'label_on'    => __( 'Enabled', 'tourfic' ),
							'label_off'   => __( 'Disabled', 'tourfic' ),
							'text_width' => 100,
							'default'    => true,
						),
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Room Title', 'tourfic' ),
							'placeholder' => __( '', 'tourfic' ),
						),
						array(
							'id'         => 'num-room',
							'type'       => 'number',
							'label'      => __( 'Number of Rooms', 'tourfic' ),
							'subtitle'   => __( 'Number of available rooms for booking', 'tourfic' ),
							'placeholder' => __( '', 'tourfic' ),
							'attributes' => array(
								'min' => '0',
							),
						),
						array(
							'id'       => '', 
							'type'     => 'switch',
							'label'    => __( 'Reduce Number of Rooms by Orders', 'tourfic' ),
							'is_pro'   => true,
							'subtitle' => __( 'Reduce the number of available rooms for booking by WooCommerce orders details', 'tourfic' ),
							'label_on'  => __( 'Yes', 'tourfic' ),
							'label_off' => __( 'No', 'tourfic' ),
							'default'  => false,
						),

						array(
							'id'    => 'Details',
							'type'    => 'heading',
							'content' => __( 'Details', 'tourfic' ),
							'class' => 'tf-field-class',
						),
						array(
							'id'       => 'gallery', 
							'type'     => 'gallery',
							'label'    => __( 'Gallery', 'tourfic' ),
							'is_pro'   => true,
						),
						array(
							'id'         => 'bed',
							'type'       => 'number',
							'label'      => __( 'Number of Beds', 'tourfic' ),
							'subtitle'   => __( 'Number of beds present in the room', 'tourfic' ),
							'attributes' => array(
								'min' => '0',
							),
						),
						array(
							'id'         => 'adult',
							'type'       => 'number',
							'label'      => __( 'Number of Adults', 'tourfic' ),
							'subtitle'   => __( 'Max number of persons allowed in the room', 'tourfic' ),
							'attributes' => array(
								'min' => '0',
							),
						),
						array(
							'id'         => 'child',
							'type'       => 'number',
							'label'      => __( 'Number of Children', 'tourfic' ),
							'subtitle'   => __( 'Max number of persons allowed in the room', 'tourfic' ),
							'attributes' => array(
								'min' => '0',
							),
						),
						array(
							'id'       => 'footage',
							'type'     => 'text',
							'label'    => __( 'Room Footage', 'tourfic' ),
							'subtitle' => __( 'Room footage (sft)', 'tourfic' ),
						),
						array(
							'id'    => 'description',
							'type'  => 'textarea',
							'label' => __( 'Room Description', 'tourfic' ),
						),
						array(
							'id'    => 'Pricing',
							'type'    => 'heading',
							'content' => __( 'Pricing', 'tourfic' ),
							'class' => 'tf-field-class',
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
							'description'       => __( 'The price of room per one night', 'tourfic' ),
							'dependency' => array( 'pricing-by', '==', '1' ),
						),
						array(
							'id'         => '', 
							'type'       => 'text',
							'label'      => __( 'Adult Pricing', 'tourfic' ),
							'is_pro'   => true,
							'desc'       => __( 'The price of room per one night', 'tourfic' ),
							'dependency' => array( 'pricing-by', '==', '2' ),
						),

						array(
							'id'         => '', 
							'type'       => 'text',
							'title'      => __( 'Children Pricing', 'tourfic' ),
							'is_pro'   => true,
							'desc'       => __( 'The price of room per one night', 'tourfic' ),
							'dependency' => array( 'pricing-by', '==', '2' ),
						),
						array(
							'id'       => 'price_multi_day',
							'type'     => 'switch',
							'label'    => __( 'Multiply Pricing By Night', 'tourfic' ),
							'subtitle'    => __( 'During booking pricing will be multiplied by number of nights (Check-in to Check-out)', 'tourfic' ),
							'label_on'  => __( 'Yes', 'tourfic' ),
							'label_off' => __( 'No', 'tourfic' ),
							'default'  => true,
						),
						array(
							'id'    => 'Deposit',
							'type'    => 'heading',
							'content' => __( 'Deposit', 'tourfic' ),
							'class' => 'tf-field-class',
						),
						array(
							'id'       => '',
							'type'     => 'switch', 
							'label'    => __( 'Enable Deposit', 'tourfic' ),
							'is_pro' => true,
							'default'  => false,
						),
						array(
							'id'    => 'Availability',
							'type'    => 'heading',
							'content' => __( 'Availability', 'tourfic' ),
							'class' => 'tf-field-class',
						),
					 ),
				)
			),
		),
		// Hotel Details
		// 'hotel_details' => array(
		// 	'title'  => __( 'Hotel Detail', 'tourfic' ),
		// 	'icon'   => 'ri-home-2-line',
		// 	'fields' => array(
			 
		// 	),
		// ),
	),
) );
