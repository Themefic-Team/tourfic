<?php
defined( 'ABSPATH' ) || exit;

$badge_up     = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span></div>';
$badge_pro    = '<div class="tf-csf-badge"><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';

if ( class_exists( 'CSF' ) ) {

	// Hotel options
	$prefix = 'tf_hotel';

	CSF::createMetabox( $prefix, array(
		'title'     => __( 'Hotel Settings', 'tourfic' ),
		'post_type' => 'tf_hotel',
		'context'   => 'advanced',
		'priority'  => 'high',
		'theme'     => 'dark',
	) );

	// Location Details
	CSF::createSection( $prefix, array(
		'title'  => __( 'Location', 'tourfic' ),
		'fields' => array(
			array(
				'id'            => 'features',
				'type'          => 'select',
				'title'         => __( 'Select Features', 'tourfic' ),
				'placeholder'   => __( 'Select', 'tourfic' ),
				'empty_message' => __( 'No feature available', 'tourfic' ),
				'taxonomy'       => 'hotel_feature',
				 
			),
			array(
				'id'         => 'icon-fa',
				'type'       => 'icon',
				'title'      => __('Select Font Awesome Icon', 'tourfic'),
			),
			array(
				'id'         => 'icon-fass',
				'type'       => 'icon',
				'title'      => __('Select Font Awesome Icon', 'tourfic'),
			),
			array(
				'id'       => 'disable-services',
				'type'     => 'checkbox',
				'title'    => __( 'Disable Services', 'tourfic' ),
				'subtitle' => __( 'Disable or hide the services you don\'t need by ticking the checkbox', 'tourfic' ),
				'options'  => array(
					'hotel' => __( 'Hotel', 'tourfic' ),
					'tour'  => __( 'Tour', 'tourfic' ),
				),
			),
			array(
				'id'          => 'address',
				'type'        => 'textarea',
				'title'       => __( 'Hotel Address', 'tourfic' ),
				'subtitle'    => __( 'Enter hotel adress', 'tourfic' ),
				'placeholder' => __( 'Address', 'tourfic' ),
				'attributes'  => array(
					'required' => 'required',
				),
			),

			array(
				'id'       => '',
				'class'    => 'tf-csf-disable tf-csf-pro',
				'type'     => 'map',
				'title'    => __( 'Location on Map', 'tourfic' ),
				'subtitle' => __( 'Select one location on the map to see latitude and longitude' . $badge_pro, 'tourfic' ),
				'height'   => '250px',
				'settings' => array(
					'scrollWheelZoom' => true,
				),
			),

		)
	) );

	// Hotel Details
	CSF::createSection( $prefix, array(
		'title'  => __( 'Hotel Detail', 'tourfic' ),
		'fields' => array(

			array(
				'id'       => 'gallery',
				'type'     => 'gallery',
				'title'    => __( 'Hotel Gallery', 'tourfic' ),
				'subtitle' => __( 'Upload one or many images to make a hotel image gallery for customers', 'tourfic' ),
			),

			array(
				'id'       => 'featured',
				'class'    => 'tf-csf-disable',
				'type'     => 'switcher',
				'title'    => __( 'Featured Hotel', 'tourfic' ),
				'subtitle' => $badge_up,
				'text_on'  => __( 'Yes', 'tourfic' ),
				'text_off' => __( 'No', 'tourfic' ),
			),

			// array(
			//   'id'      => '',
			//   'class' => 'tf-csf-disable tf-csf-pro',
			//   'type'    => 'media',
			//   'title'   => __('Hotel logo', 'tourfic' ),
			//   'subtitle'   => $badge_up_pro,
			//   'desc' =>  __( 'Upload the hotel logo (it is recommended using size: 256 x 195 px)', 'tourfic' ),
			//   'library' => 'image',
			// ),

			array(
				'id'       => '',
				'class'    => 'tf-csf-disable tf-csf-pro',
				'type'     => 'text',
				'title'    => __( 'Hotel Video', 'tourfic' ),
				'subtitle' => $badge_up_pro,
				'desc'     => __( 'Enter YouTube/Vimeo URL here', 'tourfic' ),
				'validate' => 'csf_validate_url',
			),

			// array(
			//   'id'      => '',
			//   'class' => 'tf-csf-disable tf-csf-pro',
			//   'type'    => 'slider',
			//   'title'   => __('Hotel Rating Standard', 'tourfic'),
			//   'subtitle'   => $badge_up_pro,
			//   'min'     => 0,
			//   'max'     => 7,
			//   'step'    => 1,
			//   'default' => 0,
			// ),

		)
	) );

	// Contact Information
	// CSF::createSection( $prefix, array(
	//   'title'  => __('Contact Information', 'tourfic'),
	//   'fields' => array(

	//     array(
	//       'id'       => 'c-email',
	//       'class' => 'tf-csf-disable',
	//       'type'     => 'text',
	//       'title'    => __('Hotel Email', 'tourfic'),
	//       'subtitle'   => $badge_up,
	//       'desc' =>  __( 'This email will received notification when have booking order', 'tourfic' ),
	//       //'validate' => 'csf_validate_email',
	//     ),

	//     array(
	//       'id'       => 'c-web',
	//       'class' => 'tf-csf-disable',
	//       'type'     => 'text',
	//       'title'    => __('Hotel Website', 'tourfic'),
	//       'subtitle' =>  __( 'Enter hotel website' .$badge_up, 'tourfic' ),
	//       //'validate' => 'csf_validate_url',
	//     ),

	//     array(
	//       'id'      => 'c-phone',
	//       'class' => 'tf-csf-disable',
	//       'type'    => 'text',
	//       'title'   => __('Hotel Phone Number', 'tourfic'),
	//       'subtitle' => __('Enter hotel phone number' .$badge_up, 'tourfic'),
	//     ),

	//     array(
	//       'id'      => 'c-fax',
	//       'class' => 'tf-csf-disable',
	//       'type'    => 'text',
	//       'title'   => __('Hotel Fax', 'tourfic'),
	//       'subtitle' => __('Enter hotel fax number' .$badge_up, 'tourfic'),
	//     ),

	//   )
	// ) );

	// Hotel Service

	CSF::createSection( $prefix, array(
		'title'  => __( 'Hotel Services', 'tourfic' ),
		'fields' => array(
			array(
				'id'       => '',
				'class'    => 'tf-csf-disable tf-csf-pro',
				'type'     => 'switcher',
				'title'    => __( 'Pickup Service', 'tourfic' ),
				'subtitle' => __( 'Airport Service' . $badge_pro, 'tourfic' ),
				'default'  => false,
			)

		)
	) );

	// Check-in check-out
	CSF::createSection( $prefix, array(
		'title'  => __( 'Check in/out Time', 'tourfic' ),
		'fields' => array(

			array(
				'id'       => '',
				'class'    => 'tf-csf-disable tf-csf-pro',
				'type'     => 'switcher',
				'title'    => __( 'Allowed Full Day Booking', 'tourfic' ),
				'subtitle' => __( 'You can book room with full day' . $badge_up_pro, 'tourfic' ),
				'desc'     => __( 'E.g: booking from 22 -23, then all days 22 and 23 are full, other people cannot book', 'tourfic' ),
			),

			// array(
			//   'id'       => '',
			//   'class' => 'tf-csf-disable tf-csf-pro',
			//   'type'     => 'datetime',
			//   'title'    => __('Time for Check-in', 'tourfic'),
			//   'subtitle' => __('Enter time for check-in at hotel' .$badge_up_pro, 'tourfic'),
			//   'settings' => array(
			//     'noCalendar' => true,
			//     'enableTime' => true,
			//     'dateFormat' => 'h:i K',
			//   ),
			// ),

			// array(
			//   'id'       => '',
			//   'class' => 'tf-csf-disable tf-csf-pro',
			//   'type'     => 'datetime',
			//   'title'    => __('Time for Check-out', 'tourfic'),
			//   'subtitle' => __('Enter time for check-out at hotel' .$badge_up_pro, 'tourfic'),
			//   'settings' => array(
			//     'noCalendar' => true,
			//     'enableTime' => true,
			//     'dateFormat' => 'h:i K',
			//   ),
			// ),

		)
	) );

	CSF::createSection( $prefix, array(
		'title'  => __( 'Room Details', 'tourfic' ),
		'fields' => array(

			array(
				'id'           => 'room',
				'class'        => 'room-repeater',
				'type'         => 'repeater',
				'title'        => __( 'Room Details', 'tourfic' ),
				'button_title' => __( 'Add New Room', 'tourfic' ),
				'max'          => 5,
				'fields'       => array(

					array(
						'id'         => 'unique_id',
						'class'      => 'unique-id',
						'type'       => 'text',
						'title'      => __( 'Unique ID', 'tourfic' ),
						'attributes' => array(
							'readonly' => 'readonly',
						),
					),

					array(
						'id'         => 'order_id',
						'class'      => 'tf-order_id',
						'type'       => 'text',
						'title'      => __( 'Order ID', 'tourfic' ),
						'attributes' => array(
							'readonly' => 'readonly',
						),
					),

					array(
						'id'         => 'enable',
						'type'       => 'switcher',
						'title'      => __( 'Status', 'tourfic' ),
						'subtitle'   => __( 'Enable/disable this Room', 'tourfic' ),
						'text_on'    => __( 'Enabled', 'tourfic' ),
						'text_off'   => __( 'Disabled', 'tourfic' ),
						'text_width' => 100,
						'default'    => true,
					),

					array(
						'id'    => 'title',
						'type'  => 'text',
						'title' => __( 'Room Title', 'tourfic' ),
					),

					array(
						'id'         => 'num-room',
						'type'       => 'number',
						'title'      => __( 'Number of Rooms', 'tourfic' ),
						'subtitle'   => __( 'Number of available rooms for booking', 'tourfic' ),
						'attributes' => array(
							'min' => '0',
						),
					),

					array(
						'id'       => '',
						'class'    => 'tf-csf-disable tf-csf-pro',
						'type'     => 'switcher',
						'title'    => __( 'Reduce Number of Rooms by Orders', 'tourfic' ),
						'subtitle' => __( 'Reduce the number of available rooms for booking by WooCommerce orders details' . $badge_pro, 'tourfic' ),
						'text_on'  => __( 'Yes', 'tourfic' ),
						'text_off' => __( 'No', 'tourfic' ),
						'default'  => false,
					),

					array(
						'type'    => 'subheading',
						'content' => __( 'Details', 'tourfic' ),
					),

					array(
						'id'       => 'gallery',
						'class'    => 'tf-csf-disable tf-csf-pro',
						'type'     => 'gallery',
						'title'    => __( 'Gallery', 'tourfic' ),
						'subtitle' => $badge_pro,
					),

					array(
						'id'         => 'bed',
						'type'       => 'number',
						'title'      => __( 'Number of Beds', 'tourfic' ),
						'subtitle'   => __( 'Number of beds present in the room', 'tourfic' ),
						'attributes' => array(
							'min' => '0',
						),
					),

					array(
						'id'         => 'adult',
						'type'       => 'number',
						'title'      => __( 'Number of Adults', 'tourfic' ),
						'subtitle'   => __( 'Max number of persons allowed in the room', 'tourfic' ),
						'attributes' => array(
							'min' => '0',
						),
					),

					array(
						'id'         => 'child',
						'type'       => 'number',
						'title'      => __( 'Number of Children', 'tourfic' ),
						'subtitle'   => __( 'Max number of persons allowed in the room', 'tourfic' ),
						'attributes' => array(
							'min' => '0',
						),
					),

					array(
						'id'       => 'footage',
						'type'     => 'text',
						'title'    => __( 'Room Footage', 'tourfic' ),
						'subtitle' => __( 'Room footage (sft)', 'tourfic' ),
					),

//					array(
//						'id'            => 'features',
//						'type'          => 'select',
//						'title'         => __( 'Select Features', 'tourfic' ),
//						'placeholder'   => __( 'Select', 'tourfic' ),
//						'empty_message' => __( 'No feature available', 'tourfic' ),
//						'chosen'        => true,
//						'multiple'      => true,
//						'options'       => 'categories',
//						'query_args'    => array(
//							'taxonomy' => 'hotel_feature',
//						),
//					),

					array(
						'id'    => 'description',
						'type'  => 'textarea',
						'title' => __( 'Room Description', 'tourfic' ),
					),

					array(
						'type'    => 'subheading',
						'content' => __( 'Pricing', 'tourfic' ),
					),

					array(
						'id'      => 'pricing-by',
						'type'    => 'select',
						'title'   => __( 'Pricing by', 'tourfic' ),
						'options' => array(
							'1' => __( 'Per room', 'tourfic' ),
							'2' => __( 'Per person (Pro)', 'tourfic' ),
						),
						'default' => '1'
					),

					array(
						'id'         => 'price',
						'type'       => 'text',
						'title'      => __( 'Pricing', 'tourfic' ),
						'desc'       => __( 'The price of room per one night', 'tourfic' ),
						'dependency' => array( 'pricing-by', '==', '1' ),
					),

					array(
						'id'         => '',
						'class'      => 'tf-csf-disable tf-csf-pro',
						'type'       => 'text',
						'title'      => __( 'Adult Pricing', 'tourfic' ),
						'subtitle'   => $badge_pro,
						'desc'       => __( 'The price of room per one night', 'tourfic' ),
						'dependency' => array( 'pricing-by', '==', '2' ),
					),

					array(
						'id'         => '',
						'class'      => 'tf-csf-disable tf-csf-pro',
						'type'       => 'text',
						'title'      => __( 'Children Pricing', 'tourfic' ),
						'subtitle'   => $badge_pro,
						'desc'       => __( 'The price of room per one night', 'tourfic' ),
						'dependency' => array( 'pricing-by', '==', '2' ),
					),

					array(
						'id'       => 'price_multi_day',
						'type'     => 'switcher',
						'title'    => __( 'Multiply Pricing By Night', 'tourfic' ),
						'label'    => __( 'During booking pricing will be multiplied by number of nights (Check-in to Check-out)', 'tourfic' ),
						'text_on'  => __( 'Yes', 'tourfic' ),
						'text_off' => __( 'No', 'tourfic' ),
						'default'  => true,
					),
					array(
						'type'    => 'subheading',
						'content' => __( 'Deposit', 'tourfic' ),
					),

					array(
						'id'       => '',
						'type'     => 'switcher',
						'class'    => 'tf-csf-disable tf-csf-pro',
						'title'    => __( 'Enable Deposit', 'tourfic' ),
						'subtitle' => __( $badge_pro, 'tourfic' ),
						'default'  => false,
					),


					array(
						'type'    => 'subheading',
						'content' => __( 'Availability', 'tourfic' ),
					),

					array(
						'id'       => '',
						'class'    => 'tf-csf-disable tf-csf-pro',
						'type'     => 'switcher',
						'title'    => __( 'Enable Availability by Date', 'tourfic' ),
						'subtitle' => __( $badge_pro, 'tourfic' ),
						'default'  => true
					),
					array(
						'id'       => '',
						'class'    => 'repeater-by-date',
						'type'     => 'repeater',
						'title'    => __( 'By Date', 'tourfic' ),
						'subtitle' => __( $badge_pro, 'tourfic' ),
						'fields'   => array(

							array(
								'id'        => '',
								'class'     => 'tf-csf-disable tf-csf-pro',
								'type'      => 'datetime',
								'title'     => __( 'Date Range', 'tourfic' ),
								'subtitle'  => __( 'Select availablity date range', 'tourfic' ),
								'settings'  => array(
									'dateFormat' => 'Y/m/d'
								),
								'from_to'   => true,
								'text_from' => __( 'From', 'tourfic' ),
								'text_to'   => __( 'To', 'tourfic' ),
							),
							array(
								'id'       => '',
								'class'    => 'tf-csf-disable tf-csf-pro',
								'type'     => 'number',
								'title'    => __( 'Number of Rooms', 'tourfic' ),
								'subtitle' => __( 'Number of available rooms for booking on this date range', 'tourfic' ),
							),


							array(
								'id'    => '',
								'class' => 'tf-csf-disable tf-csf-pro',
								'type'  => 'text',
								'title' => __( 'Pricing', 'tourfic' ),
								'desc'  => __( 'The price of room per one night', 'tourfic' ),
							),

							array(
								'id'    => '',
								'class' => 'tf-csf-disable tf-csf-pro',
								'type'  => 'text',
								'title' => __( 'Adult Pricing', 'tourfic' ),
								'desc'  => __( 'The price of room per one night', 'tourfic' ),
							),

							array(
								'id'    => '',
								'class' => 'tf-csf-disable tf-csf-pro',
								'type'  => 'text',
								'title' => __( 'Children Pricing', 'tourfic' ),
								'desc'  => __( 'The price of room per one night', 'tourfic' ),
							),

						),
					),

				),
			),

		)
	) );

	// FAQ
	CSF::createSection( $prefix, array(
		'title'  => __( 'F.A.Q.', 'tourfic' ),
		'fields' => array(

			array(
				'id'           => 'faq',
				'type'         => 'repeater',
				'title'        => __( 'Frequently Asked Questions', 'tourfic' ),
				'button_title' => __( 'Add FAQ', 'tourfic' ),
				'fields'       => array(

					array(
						'id'    => 'title',
						'type'  => 'text',
						'title' => __( 'Title', 'tourfic' ),
					),

					array(
						'id'    => 'description',
						'type'  => 'textarea',
						'title' => __( 'Description', 'tourfic' ),
					),

				),
			),

		)
	) );

	// Terms & conditions
	CSF::createSection( $prefix, array(
		'title'  => __( 'Terms & Conditions', 'tourfic' ),
		'fields' => array(

			array(
				'id'    => 'tc',
				'type'  => 'wp_editor',
				'title' => __( 'Terms & Conditions', 'tourfic' ),
			),

		)
	) );

	// Settings
	CSF::createSection(
		$prefix,
		array(
			'title'  => __( 'Settings', 'tourfic' ),
			'fields' => array(

				array(
					'type'    => 'subheading',
					'content' => __( 'Settings', 'tourfic' ),
				),

				array(
					'id'       => 'h-review',
					'type'     => 'switcher',
					'title'    => __( 'Disable Review Section', 'tourfic' ),
					'text_on'  => __( 'Yes', 'tourfic' ),
					'text_off' => __( 'No', 'tourfic' ),
					'default'  => false
				),

				array(
					'id'       => 'h-share',
					'type'     => 'switcher',
					'title'    => __( 'Disable Share Option', 'tourfic' ),
					'text_on'  => __( 'Yes', 'tourfic' ),
					'text_off' => __( 'No', 'tourfic' ),
					'default'  => false
				),

				array(
					'type'    => 'notice',
					'style'   => 'info',
					'content' => __( 'These settings will overwrite global settings', 'tourfic' ),
				),

			),
		)
	);


}
?>