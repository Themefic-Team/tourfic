<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Metabox::metabox( 'tf_tours_opt', array(
	'title'     => __( 'Tour Setting', 'tourfic' ),
	'post_type' => 'tf_tours',
	'sections'  => array(
		// General
		'general'              => array(
			'title'  => __( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'       => 'tour_as_featured',
					'type'     => 'switch',
					'label'    => __( 'Set this tour as featured', 'tourfic' ),
					'subtitle' => __( 'Tour will be shown under featured sections', 'tourfic' ),
				),
				array(
					'id'      => 'tour_single_page',
					'type'    => 'select',
					'label'   => __( 'Single Tour Page Layout', 'tourfic' ),
					'options' => array(
						'instant' => __( 'Default', 'tourfic' ),
					),
				),

				array(
					'id'    => 'tour_gallery',
					'type'  => 'gallery',
					'label' => __( 'Tour Gallery', 'tourfic' ),
				),

				array(
					'id'     => '',
					'type'   => 'text',
					'label'  => __( 'Tour video', 'tourfic' ),
					'is_pro' => true,
				),
			),
		),
		// Location
		'location'             => array(
			'title'  => __( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'         => 'text_location',
					'type'       => 'textarea',
					'label'      => __( 'Tour Location', 'tourfic' ),
					'subtitle'   => __( 'Manually enter your tour location', 'tourfic' ),
					'attributes' => array(
						'required' => 'required',
					),
				),
				array(
					'id'       => '',
					'type'     => 'map',
					'label'    => __( 'Tour Location Pro (Auto Suggestion)', 'tourfic' ),
					'subtitle' => __( 'Location suggestions will be provided from Google', 'tourfic' ),
					'is_pro'   => true,
					'height'   => '250px',
					'settings' => array(
						'scrollWheelZoom' => true,
					)
				),
			),
		),
		// Information
		'information'          => array(
			'title'  => __( 'Information', 'tourfic' ),
			'icon'   => 'fa-solid fa-circle-info',
			'fields' => array(
				array(
					'id'          => 'duration',
					'type'        => 'text',
					'label'       => __( 'Tour Duration', 'tourfic' ),
					'subtitle'    => __( 'E.g. 3 days', 'tourfic' ),
					'field_width' => '33.33',
				),
				array(
					'id'          => 'group_size',
					'type'        => 'text',
					'label'       => __( 'Group Size', 'tourfic' ),
					'subtitle'    => __( 'E.g. 10 people', 'tourfic' ),
					'field_width' => '33.33',
				),
				array(
					'id'          => 'language',
					'type'        => 'text',
					'label'       => __( 'Languages', 'tourfic' ),
					'subtitle'    => __( 'Input languages seperated by comma (,)', 'tourfic' ),
					'field_width' => '33.33',
				),

				array(
					'id'       => 'additional_information',
					'type'     => 'editor',
					'label'    => __( 'Tour Hightlights', 'tourfic' ),
					'subtitle' => __( 'Enter a summary or full subtitle of your tour', 'tourfic' ),
				),
				array(
					'id'      => 'hightlights_thumbnail',
					'type'    => 'image',
					'label'   => __( 'Tour Hightlights Thumbnail', 'tourfic' ),
					'library' => 'image',
				),
			),
		),
		// Contact Info
		'contact_info'         => array(
			'title'  => __( 'Contact Info', 'tourfic' ),
			'icon'   => 'fa-solid fa-address-book',
			'fields' => array(
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'Email address', 'tourfic' ),
					'is_pro'      => true,
					'badge_up'    => true,
					'field_width' => '50',
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'Phone Number', 'tourfic' ),
					'is_pro'      => true,
					'badge_up'    => true,
					'field_width' => '50',
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'Website Url', 'tourfic' ),
					'is_pro'      => true,
					'badge_up'    => true,
					'field_width' => '50',
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'Fax Number', 'tourfic' ),
					'is_pro'      => true,
					'badge_up'    => true,
					'field_width' => '50',
				),
			),
		),
		// //  Tour Extra
		'tour_extra'           => array(
			'title'  => __( 'Tour Extra', 'tourfic' ),
			'icon'   => 'fa-solid fa-route',
			'fields' => array(
				array(
					'id'     => 'tour-extra',
					'type'   => 'repeater',
					'label'  => __( 'Extra Services on Tour', 'tourfic' ),
					'is_pro' => true,
					'fields' => array(
						array(
							'id'     => '',
							'type'   => 'text',
							'label'  => __( 'Title', 'tourfic' ),
							'is_pro' => true,
						),
						array(
							'id'     => '',
							'type'   => 'textarea',
							'label'  => __( 'Short subtitle', 'tourfic' ),
							'is_pro' => true,
						),
						array(
							'id'         => '',
							'type'       => 'text',
							'label'      => __( 'Price', 'tourfic' ),
							'is_pro'     => true,
							'attributes' => array(
								'min' => '0',
							),
						),
					),
				),
			),
		),

		// // Price
		'price'                => array(
			'title'  => __( 'Price Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-money-check',
			'fields' => array(
				array(
					'id'       => 'pricing',
					'type'     => 'select',
					'label'    => __( 'Pricing rule', 'tourfic' ),
					'subtitle' => __( 'Input pricing rule', 'tourfic' ),
					'class'    => 'pricing',
					'options'  => [
						'person' => __( 'Person', 'tourfic' ),
						'group'  => __( 'Group (Pro)', 'tourfic' ),
					],
					'default'  => 'person',
				),
				array(
					'id'          => 'adult_price',
					'type'        => 'number',
					'label'       => __( 'Price for Adult', 'tourfic' ),
					'subtitle'    => __( 'For no price use 00', 'tourfic' ),
					'dependency'  => [
						array( 'pricing', '==', 'person' ),
						[ 'disable_adult_price', '==', 'false' ]
					],
					'attributes'  => array(
						'min' => '0',
					),
					'field_width' => '33.33',
				),
				array(
					'id'          => 'child_price',
					'type'        => 'number',
					'dependency'  => [
						array( 'pricing', '==', 'person' ),
						[ 'disable_child_price', '==', 'false' ]
					],
					'label'       => __( 'Price for Child', 'tourfic' ),
					'subtitle'    => __( 'For no price use 00', 'tourfic' ),
					'attributes'  => array(
						'min' => '0',
					),
					'field_width' => '33.33',
				),
				array(
					'id'          => 'infant_price',
					'type'        => 'number',
					'dependency'  => [
						array( 'pricing', '==', 'person' ),
						[ 'disable_infant_price', '==', 'false' ],
						[ 'disable_adult_price', '==', 'false' ],
					],
					'label'       => __( 'Price for Infant', 'tourfic' ),
					'subtitle'    => __( 'For no price use 00', 'tourfic' ),
					'attributes'  => array(
						'min' => '0',
					),
					'field_width' => '33.33',
				),
				array(
					'id'         => '',
					'type'       => 'number',
					'dependency' => array( 'pricing', '==', 'group' ),
					'label'      => __( 'Group Price', 'tourfic' ),
					'subtitle'   => __( 'Input group price', 'tourfic' ),
					'is_pro'     => true,
					'attributes' => array(
						'min' => '0',
					),
				),
				array(
					'id'       => 'discount_type',
					'type'     => 'select',
					'label'    => __( 'Discount Type', 'tourfic' ),
					'subtitle' => __( 'Select discount type Percent or Fixed', 'tourfic' ),
					'options'  => array(
						'none'    => __( 'None', 'tourfic' ),
						'percent' => __( 'Percent', 'tourfic' ),
						'fixed'   => __( 'Fixed', 'tourfic' ),
					),
					'default'  => 'none',
				),
				array(
					'id'         => 'discount_price',
					'type'       => 'number',
					'label'      => __( 'Discount Price', 'tourfic' ),
					'subtitle'   => __( 'Input discount price in number', 'tourfic' ),
					'attributes' => array(
						'min' => '0',
					),
					'dependency' => array(
						array( 'discount_type', '!=', 'none' ),
					),
				),
				array(
					'id'          => 'disable_adult_price',
					'type'        => 'switch',
					'label'       => __( 'Disable adult price', 'tourfic' ),
					'subtitle'    => __( 'Hide No of adult in booking form', 'tourfic' ),
					'field_width' => '33.33',
				),
				array(
					'id'          => 'disable_child_price',
					'type'        => 'switch',
					'label'       => __( 'Disable children price', 'tourfic' ),
					'subtitle'    => __( 'Hide No of children in booking form', 'tourfic' ),
					'field_width' => '33.33',
				),
				array(
					'id'          => 'disable_infant_price',
					'type'        => 'switch',
					'label'       => __( 'Disable infant price', 'tourfic' ),
					'subtitle'    => __( 'Hide No of infant in booking form', 'tourfic' ),
					'field_width' => '33.33',
				),
				array(
					'id'      => 'price_deposit',
					'type'    => 'heading',
					'content' => __( 'Deposit', 'tourfic' ),
				),

				array(
					'id'      => 'allow_deposit',
					'type'    => 'switch',
					'label'   => __( 'Enable Deposit', 'tourfic' ),
					'is_pro'  => true,
					'default' => false,
				),
			),
		),

		// // Availability
		'availability'         => array(
			'title'  => __( 'Availability', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard',
			'fields' => array(
				array(
					'id'       => 'type',
					'type'     => 'select',
					'label'    => __( 'Tour Type', 'tourfic' ),
					'subtitle' => __( 'Fixed: Tour will be available on a fixed date. Continous: Tour will be available every month within the mentioned range.', 'tourfic' ),
					'class'    => 'tour-type',
					'options'  => [
						'continuous' => __( 'Continuous', 'tourfic' ),
						'fixed'      => __( 'Fixed (Pro)', 'tourfic' ),
					],
					'default'  => 'continuous',

				),
				array(
					'id'         => 'custom_avail',
					'type'       => 'switch',
					'label'      => __( 'Custom Availability', 'tourfic' ),
					'is_pro'     => true,
					'dependency' => array( 'type', '==', 'continuous' ),
					'label_on'   => __( 'Yes', 'tourfic' ),
					'label_off'  => __( 'No', 'tourfic' ),
				),
				array(
					'id'         => 'cont_custom_date',
					'type'       => 'repeater',
					'label'      => __( 'Allowed Dates', 'tourfic' ),
					'is_pro'     => true,
					'dependency' => array(
						array( 'type', '==', 'continuous' ),
						array( 'custom_avail', '==', 'true' ),
					),
					'fields'     => array(
						array(
							'id'         => '',
							'type'       => 'date',
							'label'      => __( 'Date Range', 'tourfic' ),
							'is_pro'     => true,
							'format'     => 'Y/m/d',
							'range'      => true,
							'label_from' => 'Start Date',
							'label_to'   => 'End Date',
							'attributes' => array(
								'autocomplete' => 'off',
							),
						),
						array(
							'id'     => '',
							'type'   => 'number',
							'label'  => __( 'Min people', 'tourfic' ),
							'is_pro' => true,
						),
						array(
							'id'     => '',
							'type'   => 'number',
							'label'  => __( 'Maximum people', 'tourfic' ),
							'is_pro' => true,
						),
						array(
							'id'       => 'pricing',
							'type'     => 'select',
							'label'    => __( 'Pricing rule', 'tourfic' ),
							'subtitle' => __( 'Input pricing rule', 'tourfic' ),
							'is_pro'   => true,
							'class'    => 'pricing',
							'options'  => [
								'person' => __( 'Person', 'tourfic' ),
								'group'  => __( 'Group', 'tourfic' ),
							],
							'default'  => 'person',
						),
						array(
							'id'         => '',
							'type'       => 'number',
							'label'      => __( 'Price for Adult', 'tourfic' ),
							'subtitle'   => __( 'Input adult price', 'tourfic' ),
							'is_pro'     => true,
							'dependency' => array( 'pricing', '==', 'person' ),
							'attributes' => array(
								'min' => '0',
							),
						),
						array(
							'id'         => '',
							'type'       => 'number',
							'label'      => __( 'Price for Child', 'tourfic' ),
							'subtitle'   => __( 'Input child price', 'tourfic' ),
							'is_pro'     => true,
							'dependency' => array( 'pricing', '==', 'person' ),
							'attributes' => array(
								'min' => '0',
							),
						),
						array(
							'id'         => '',
							'type'       => 'number',
							'label'      => __( 'Price for Infant', 'tourfic' ),
							'subtitle'   => __( 'Input infant price', 'tourfic' ),
							'is_pro'     => true,
							'dependency' => array( 'pricing', '==', 'person' ),
							'attributes' => array(
								'min' => '0',
							),
						),
						array(
							'id'         => '',
							'type'       => 'number',
							'dependency' => array( 'pricing', '==', 'group' ),
							'label'      => __( 'Group Price', 'tourfic' ),
							'subtitle'   => __( 'Input group price', 'tourfic' ),
							'is_pro'     => true,
							'attributes' => array(
								'min' => '0',
							),
						),
						array(
							'id'     => 'allowed_time',
							'type'   => 'repeater',
							'label'  => __( 'Allowed Time', 'tourfic' ),
							'is_pro' => true,
							'fields' => array(

								array(
									'id'       => '',
									'type'     => 'date',
									'label'    => __( 'Time', 'tourfic' ),
									'subtitle' => __( 'Only Time', 'tourfic' ),
									'is_pro'   => true,
									'settings' => array(
										'noCalendar' => true,
										'enableTime' => true,
										'dateFormat' => "h:i K"
									),
								),


							),
						),

					),
				),
				/**
				 * Custom: No
				 *
				 * Continuous Avaialbility
				 */
				array(
					'id'          => 'cont_min_people',
					'type'        => 'number',
					'label'       => __( 'Minimum Person', 'tourfic' ),
					'subtitle'    => __( 'Minimum person to travel', 'tourfic' ),
					'dependency'  => array(
						array( 'type', '==', 'continuous' ),
						array( 'custom_avail', '==', 'false' ),
					),
					'field_width' => '50',
				),
				array(
					'id'          => 'cont_max_people',
					'type'        => 'number',
					'label'       => __( 'Maximum Person', 'tourfic' ),
					'subtitle'    => __( 'Maximum person to travel', 'tourfic' ),
					'dependency'  => array(
						array( 'type', '==', 'continuous' ),
						array( 'custom_avail', '==', 'false' ),
					),
					'field_width' => '50',
				),
				array(
					'id'           => 'allowed_time',
					'type'         => 'repeater',
					'label'        => __( 'Allowed Time', 'tourfic' ),
					'button_title' => __( 'Add New Time', 'tourfic' ),
					'is_pro'       => true,
					'dependency'   => array(
						array( 'type', '==', 'continuous' ),
						array( 'custom_avail', '==', 'false' ),
					),
					'fields'       => array(
						array(
							'id'       => '',
							'type'     => 'datetime',
							'title'    => __( 'Time', 'tourfic' ),
							'subtitle' => __( 'Only Time', 'tourfic' ),
							'is_pro'   => true,
							'settings' => array(
								'noCalendar' => true,
								'enableTime' => true,
								'dateFormat' => "h:i K"
							),
						),
					),
				),
				array(
					'id'         => 'Disabled_Dates',
					'type'       => 'heading',
					'content'    => __( 'Disabled Dates', 'tourfic' ),
					'dependency' => array(
						array( 'type', '==', 'continuous' ),
						array( 'custom_avail', '==', 'false' ),
					),
				),
				array(
					'id'         => '',
					'type'       => 'checkbox',
					'label'      => __( 'Select day to disable', 'tourfic' ),
					'is_pro'     => true,
					'dependency' => array(
						array( 'type', '==', 'continuous' ),
						array( 'custom_avail', '==', 'false' ),
					),
					'inline'     => true,
					'options'    => array(
						'0' => __( 'Sunday', 'tourfic' ),
						'1' => __( 'Monday', 'tourfic' ),
						'2' => __( 'Tuesday', 'tourfic' ),
						'3' => __( 'Wednesday', 'tourfic' ),
						'4' => __( 'Thursday', 'tourfic' ),
						'5' => __( 'Friday', 'tourfic' ),
						'6' => __( 'Saturday', 'tourfic' ),
					),
				),
				array(
					'id'           => 'disable_range',
					'type'         => 'repeater',
					'label'        => __( 'Disabled Date Range', 'tourfic' ),
					'button_title' => __( 'Add New Date', 'tourfic' ),
					'max'          => 2,
					'dependency'   => array(
						array( 'type', '==', 'continuous' ),
						array( 'custom_avail', '==', 'false' ),
					),
					'fields'       => array(

						array(
							'id'         => 'date',
							'type'       => 'date',
							'label'      => __( 'Select date range', 'tourfic' ),
							'format'     => 'Y/m/d',
							'range'      => true,
							'label_from' => 'Start Date',
							'label_to'   => 'End Date',
							'multiple'   => true,
							'attributes' => array(
								'autocomplete' => 'off',
							),
						),

					),
				),
				array(
					'id'         => '',
					'type'       => 'date',
					'label'      => __( 'Disable Specific Dates', 'tourfic' ),
					'is_pro'     => true,
					'dependency' => array(
						array( 'type', '==', 'continuous' ),
						array( 'custom_avail', '==', 'false' ),
					),
					'format'     => 'Y/m/d',
					'label_from' => 'Start Date',
					'label_to'   => 'End Date',
					'multiple'   => true,
					'attributes' => array(
						'autocomplete' => 'off',
					),
				),
				/**
				 * Fixed Availability
				 */
				array(
					'id'         => 'fixed_availability',
					'type'       => 'tab',
					'label'      => __( 'Availability', 'tourfic' ),
					'dependency' => array(
						array( 'type', '==', 'fixed' ),
					),
					'is_pro'     => true,
					'class'      => 'fixed_availability',
					'tabs'       => array(
						array(
							'id'     => 'tab-1',
							'title'  => 'Availability',
							'icon'   => 'fa fa-heart',
							'fields' => array(
								array(
									'id'         => '',
									'type'       => 'date',
									'label'      => __( 'Check In', 'tourfic' ),
									'subtitle'   => __( 'Select check in date', 'tourfic' ),
									'is_pro'     => true,
									'format'     => 'Y/m/d',
									'range'      => true,
									'label_from' => 'Start Date',
									'label_to'   => 'End Date',
									'attributes' => array(
										'autocomplete' => 'off',
									),
									'from_to'    => true,
								),
								array(
									'id'       => '',
									'type'     => 'number',
									'label'    => __( 'Minimum People', 'tourfic' ),
									'is_pro'   => true,
									'subtitle' => __( 'Minimum seat number', 'tourfic' ),
								),
								array(
									'id'       => '',
									'type'     => 'number',
									'label'    => __( 'Maximum People', 'tourfic' ),
									'is_pro'   => true,
									'subtitle' => __( 'Maximum seat number', 'tourfic' ),
								),
							),
						),

					),
				)

			),
		),

		// // Booking
		'booking'              => array(
			'title'  => __( 'Booking', 'tourfic' ),
			'icon'   => 'fa-solid fa-person-walking-luggage',
			'fields' => array(
				array(
					'id'       => '',
					'type'     => 'number',
					'label'    => __( 'Minimum days to book before departure', 'tourfic' ),
					'is_pro'   => true,
					'subtitle' => __( 'Customer can not book after this date', 'tourfic' ),
				),
			),
		),
		// // Exclude/Include
		'exclude_Include'      => array(
			'title'  => __( 'Exclude/Include', 'tourfic' ),
			'icon'   => 'fa-solid fa-square-check',
			'fields' => array(
				array(
					'id'           => 'inc',
					'type'         => 'repeater',
					'label'        => __( 'Include', 'tourfic' ),
					'button_title' => __( 'Add New Include', 'tourfic' ),
					'max'          => 5,
					'fields'       => array(
						array(
							'id'       => 'inc',
							'type'     => 'text',
							'label'    => __( 'Included', 'tourfic' ),
							'subtitle' => __( 'Included facilites', 'tourfic' ),
						),
					),
				),
				array(
					'id'       => 'inc_icon',
					'type'     => 'icon',
					'label'    => __( 'Included item icon', 'tourfic' ),
					'subtitle' => __( 'Choose icon', 'tourfic' ),
				),
				array(
					'id'           => 'exc',
					'type'         => 'repeater',
					'label'        => __( 'Exclude', 'tourfic' ),
					'button_title' => __( 'Add New Exclude', 'tourfic' ),
					'max'          => 5,
					'fields'       => array(
						array(
							'id'       => 'exc',
							'type'     => 'text',
							'label'    => __( 'Excluded', 'tourfic' ),
							'subtitle' => __( 'Excluded facilites', 'tourfic' ),
						),
					),
				),
				array(
					'id'       => 'exc_icon',
					'type'     => 'icon',
					'label'    => __( 'Excluded item icon', 'tourfic' ),
					'subtitle' => __( 'Choose icon', 'tourfic' ),
				),
				array(
					'id'      => 'include-exclude-bg',
					'type'    => 'image',
					'label'   => __( 'Background Image', 'tourfic' ),
					'library' => 'image',
				),
			),
		),

		// // Itinerary
		'itinerary'            => array(
			'title'  => __( 'Itinerary', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-list',
			'fields' => array(
				array(
					'id'           => 'itinerary',
					'type'         => 'repeater',
					'label'        => __( 'Itinerary', 'tourfic' ),
					'button_title' => __( 'Add New Itinerary', 'tourfic' ),
					'max'          => 5,
					'fields'       => array(
						array(
							'id'       => 'time',
							'type'     => 'text',
							'label'    => __( 'Time or Day', 'tourfic' ),
							'subtitle' => __( 'You can place the tour plan', 'tourfic' ),
							'field_width' => '50',
						),
						array(
							'id'       => 'title',
							'type'     => 'text',
							'label'    => __( 'Title', 'tourfic' ),
							'subtitle' => __( 'Input the title here', 'tourfic' ),
							'field_width' => '50',
						),
						array(
							'id'           => 'image',
							'type'         => 'image',
							'label'        => __( 'Upload Image', 'tourfic' ),
							'library'      => 'image',
							'placeholder'  => 'http://',
							'button_title' => __( 'Add Image', 'tourfic' ),
							'remove_title' => __( 'Remove Image', 'tourfic' ),
						),
						array(
							'id'    => 'desc',
							'type'  => 'textarea',
							'label' => __( 'Description', 'tourfic' ),
						),
					),
				),
			),
		),

		// FAQs
		'faqs'                 => array(
			'title'  => __( 'FAQs', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'     => 'faqs',
					'type'   => 'repeater',
					'label'  => __( 'FAQs', 'tourfic' ),
					'button_title' => __( 'Add New Faq', 'tourfic' ),
					'fields' => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'FAQ Title', 'tourfic' ),
						),
						array(
							'id'    => 'desc',
							'type'  => 'editor',
							'label' => __( 'FAQ subtitle', 'tourfic' ),
						),
					),
				),
			),
		),


		// // Terms & Conditions
		'terms_and_conditions' => array(
			'title'  => __( 'Terms & Conditions', 'tourfic' ),
			'icon'   => 'fa-regular fa-square-check',
			'fields' => array(
				array(
					'id'    => 'terms_conditions',
					'type'  => 'editor',
					'label' => __( 'Terms & Conditions', 'tourfic' ),
				),
			),
		),


		// // Settings
		'settings'             => array(
			'title'  => __( 'Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-viruses',
			'fields' => array(
				array(
					'id'    => 'settings_headding',
					'type'  => 'heading',
					'label' => __( 'Settings', 'tourfic' ),
				),
				array(
					'id'        => 't-review',
					'type'      => 'switch',
					'label'     => __( 'Disable Review Section', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false
				),
				array(
					'id'        => 't-related',
					'type'      => 'switch',
					'label'     => __( 'Disable Related Tour Section', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false
				),

				array(
					'id'     => 'notice',
					'type'   => 'notice',
					'notice' => 'info',
					'content'  => __( 'These settings will overwrite global settings', 'tourfic' ),
				),
			),
		),
	),
) );
