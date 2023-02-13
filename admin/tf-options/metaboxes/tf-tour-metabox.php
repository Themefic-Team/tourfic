<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Get all the meals from glabal settings
 * @author AbuHena
 * @since 1.7.0
 */
function tf_tour_meals() {
	$itinerary_options = ! empty( tf_data_types( tfopt( 'itinerary-builder-setings' ) ) ) ? tf_data_types( tfopt( 'itinerary-builder-setings' ) ) : '';
	$all_meals         = [];
	if ( ! empty( $itinerary_options['meals'] ) && is_array( $itinerary_options['meals'] ) ) {
		$meals = $itinerary_options['meals'];
		foreach ( $meals as $key => $meal ) {
			$all_meals[ $meal['meal'] . $key ] = $meal['meal'];
		}
	}

	return $all_meals;
}

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
					'label'    => __( 'Set as featured', 'tourfic' ),
					'subtitle' => __( 'This tour will be highlighted at the top of the search result and tour archive page', 'tourfic' ),
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
					'id'     => 'tour_video',
					'type'   => 'text',
					'label'  => __( 'Tour Video', 'tourfic' ),
				),
			),
		),
		// Location
		'location'             => array(
			'title'  => __( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'          => 'text_location',
					'type'        => 'textarea',
					'label'       => __( 'Tour Location', 'tourfic' ),
					'subtitle'    => __( 'Manually enter your tour location', 'tourfic' ),
					'placeholder' => __( 'e.g. 123 ABC Road, Toronto, Ontario 20100', 'tourfic' ),
					'attributes'  => array(
						'required' => 'required',
					),
				),
				array(
					'id'       => 'location',
					'class'    => 'gmaps',
					'type'     => 'map',
					'label'    => __( 'Dynamic Location Search', 'tourfic' ),
					'subtitle' => __( 'Location suggestions will be provided from Google or OpenStreetMap (Depending on your selection from the Settings Panel)', 'tourfic' ),
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
					'subtitle'    => __( 'Include multiple language seperated by comma (,)', 'tourfic' ),
					'field_width' => '33.33',
				),

				array(
					'id'       => 'additional_information',
					'type'     => 'editor',
					'label'    => __( 'Tour Highlights', 'tourfic' ),
					'subtitle' => __( 'Enter a summary or full subtitle of your tour', 'tourfic' ),
				),
				array(
					'id'      => 'hightlights_thumbnail',
					'type'    => 'image',
					'label'   => __( 'Tour Highlights Thumbnail', 'tourfic' ),
					'library' => 'image',
				),
				array(
					'id'       => 'features',
					'type'     => 'select2',
					'multiple' => true,
					'is_pro'   => true,
					'label'    => __( 'Select features', 'tourfic' ),
					'subtitle' => __( 'Select features that are available in this tour', 'tourfic' ),
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
					'label'  => __( 'Extra Services Available on Your Tour', 'tourfic' ),
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
					'subtitle' => __( 'Select your pricing logic', 'tourfic' ),
					'class'    => 'pricing',
					'options'  => [
						'person' => __( 'Per Person', 'tourfic' ),
						''       => __( 'Per Group (Pro)', 'tourfic' ),
					],
					'default'  => 'person',
				),
				array(
					'id'          => 'adult_price',
					'type'        => 'number',
					'label'       => __( 'Price for Adult', 'tourfic' ),
					'subtitle'    => __( 'Insert amount only', 'tourfic' ),
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
					'subtitle'    => __( 'Insert amount only', 'tourfic' ),
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
					'subtitle'    => __( 'Insert amount only', 'tourfic' ),
					'attributes'  => array(
						'min' => '0',
					),
					'field_width' => '33.33',
				),
				array(
					'id'         => '',
					'type'       => 'number',
					'dependency' => array( 'pricing', '==', 'group' ),
					'label'      => __( 'Price per Group', 'tourfic' ),
					'subtitle'   => __( 'Insert amount only', 'tourfic' ),
					'is_pro'     => true,
					'attributes' => array(
						'min' => '0',
					),
				),
				array(
					'id'       => 'discount_type',
					'type'     => 'select',
					'label'    => __( 'Discount Type', 'tourfic' ),
					'subtitle' => __( 'Select discount type: Percentage or Fixed', 'tourfic' ),
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
					'subtitle'   => __( 'Insert amount only', 'tourfic' ),
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
					'field_width' => '33.33',
				),
				array(
					'id'          => 'disable_child_price',
					'type'        => 'switch',
					'label'       => __( 'Disable children price', 'tourfic' ),
					'field_width' => '33.33',
				),
				array(
					'id'          => 'disable_infant_price',
					'type'        => 'switch',
					'label'       => __( 'Disable infant price', 'tourfic' ),
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
							'subtitle' => __( 'Select your pricing logic', 'tourfic' ),
							'is_pro'   => true,
							'class'    => 'pricing',
							'options'  => [
								'person' => __( 'Per Person', 'tourfic' ),
								'group'  => __( 'Per Group', 'tourfic' ),
							],
							'default'  => 'person',
						),
						array(
							'id'         => '',
							'type'       => 'number',
							'label'      => __( 'Price for Adult', 'tourfic' ),
							'subtitle'   => __( 'Insert amount only', 'tourfic' ),
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
							'subtitle'   => __( 'Insert amount only', 'tourfic' ),
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
							'subtitle'   => __( 'Insert amount only', 'tourfic' ),
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
							'subtitle'   => __( 'Insert amount only', 'tourfic' ),
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
									'subtitle' => __( 'Select your Time', 'tourfic' ),
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
					'subtitle'    => __( 'Minimum person needed to travel', 'tourfic' ),
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
					'subtitle'    => __( 'Maximum person allowed to travel', 'tourfic' ),
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
							'subtitle' => __( 'Select your Time', 'tourfic' ),
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
									'subtitle' => __( 'Minimum person needed to travel', 'tourfic' ),
								),
								array(
									'id'       => '',
									'type'     => 'number',
									'label'    => __( 'Maximum People', 'tourfic' ),
									'is_pro'   => true,
									'subtitle' => __( 'Maximum person allowed to travel', 'tourfic' ),
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
					'label'        => __( 'Items/Features Included in this tour', 'tourfic' ),
					'button_title' => __( 'Add New Include', 'tourfic' ),
					'fields'       => array(
						array(
							'id'    => 'inc',
							'type'  => 'text',
							'label' => __( 'Insert your item', 'tourfic' ),
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
					'label'        => __( 'Items/Features Excluded in this tour', 'tourfic' ),
					'button_title' => __( 'Add New Exclude', 'tourfic' ),
					'fields'       => array(
						array(
							'id'    => 'exc',
							'type'  => 'text',
							'label' => __( 'Insert your item', 'tourfic' ),
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
			'title'  => __( 'Itinerary Builder', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-list',
			'fields' => array(
				array(
					'id'           => 'itinerary',
					'type'         => 'repeater',
					'label'        => __( 'Create your Travel Itinerary', 'tourfic' ),
					'button_title' => __( 'Add New Itinerary', 'tourfic' ),
					'fields'       => array(
						array(
							'id'          => 'time',
							'type'        => 'text',
							'label'       => __( 'Time or Day', 'tourfic' ),
							'subtitle'    => __( 'e.g. Day 1 or 9:00 am', 'tourfic' ),
							'field_width' => '50',
						),
						array(
							'id'          => 'title',
							'type'        => 'text',
							'label'       => __( 'Title', 'tourfic' ),
							'subtitle'    => __( 'Input the title here', 'tourfic' ),
							'field_width' => '50',
						),
						array(
							'id'          => 'duration',
							'label'       => __( 'Duration', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => 'Duration',
							'field_width' => 50,
							'is_pro'      => true,
						),
						array(
							'id'          => 'timetype',
							'label'       => __( 'Duration Type', 'tourfic' ),
							'type'        => 'select',
							'options'     => [
								'Hour'   => __( 'Hour', 'tourfic' ),
								'Minute' => __( 'Minute', 'tourfic' ),
							],
							'default'     => 'Hour',
							'field_width' => 50,
							'is_pro'      => true,
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
							'type'  => 'editor',
							'label' => __( 'Description', 'tourfic' ),
						),
						array(
							'id'     => 'gallery_image',
							'type'   => 'gallery',
							'label'  => __( 'Gallery Image', 'tourfic' ),
							'is_pro' => true,
						),
						array(
							'id'           => 'itinerary-sleep-mode',
							'type'         => 'repeater',
							'button_title' => __( 'Add New Option', 'tourfic' ),
							'label'        => __( 'Custom Itinerary options', 'tourfic' ),
							'subtitle'     => __( 'You can create these options from Tourfic Settings', 'tourfic' ),
							'is_pro'       => true,
							'fields'       => array(
								array(
									'id'               => 'sleepmode',
									'type'             => 'select',
									'is_pro'           => true,
									'options_callback' => 'sleep_mode_option_callback'
								),
								array(
									'id'            => 'sleep',
									'type'          => 'editor',
									'is_pro'        => true,
									'label'         => __( 'Description', 'tourfic' ),
									'media_buttons' => false,
								)
							),
						),
						array(
							'id'               => 'meals',
							'type'             => 'checkbox',
							'label'            => __( 'Meals Included', 'tourfic' ),
							'inline'           => true,
							'options_callback' => 'tf_tour_meals',
							'is_pro'           => true,
						),
						array(
							'id'          => 'loacation',
							'label'       => __( 'Location', 'tourfic' ),
							'type'        => 'text',
							'class'       => 'ininenary-group',
							'placeholder' => 'Location',
							'field_width' => 33,
							'is_pro'      => true,
						),
						array(
							'id'          => 'altitude',
							'label'       => __( 'Altitude', 'tourfic' ),
							'type'        => 'text',
							'placeholder' => 'Altitude',
							'class'       => 'ininenary-group',
							'field_width' => 33,
							'is_pro'      => true,
						),
						array(
							'id'               => 'valuetype',
							'label'            => __( 'Elevation Input', 'tourfic' ),
							'type'             => 'select',
							'class'            => 'ininenary-group',
							'options_callback' => 'elevation_option_callback',
							'field_width'      => 33,
							'is_pro'           => true,
						),
					),
				),

				array(
					'id'      => 'itinerary-downloader-settings',
					'type'    => 'heading',
					'content' => __( 'Itinerary Downloader Settings', 'tourfic' ),
				),
				array(
					'id'       => '',
					'type'     => 'switch',
					'label'    => __( 'Enable Itinerary Downloader', 'tourfic' ),
					'subtitle' => __( 'Enabling this will allow customers to download the itinerary plan in PDF format.', 'tourfic' ),
					'is_pro'   => true,
				),
				array(
					'id'      => 'tour_settings',
					'type'    => 'heading',
					'content' => __( 'Tour Settings in PDF', 'tourfic' ),
				),
				array(
					'id'          => '',
					'type'        => 'number',
					'label'       => __( 'Tour Thumbnail Height', 'tourfic' ),
					'field_width' => 50,
					'is_pro'      => true,
				),
				array(
					'id'          => '',
					'type'        => 'number',
					'label'       => __( 'Tour Thumbnail Width', 'tourfic' ),
					'field_width' => 50,
					'is_pro'      => true,
				),
				array(
					'id'      => 'companey_info_heading',
					'type'    => 'heading',
					'content' => __( 'Company Info', 'tourfic' ),
				),

				array(
					'id'     => '',
					'type'   => 'image',
					'label'  => __( 'Company Logo', 'tourfic' ),
					'is_pro' => true,
				),
				array(
					'id'     => '',
					'type'   => 'textarea',
					'label'  => __( 'Short Company Description', 'tourfic' ),
					'is_pro' => true,
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'Company Email Address', 'tourfic' ),
					'field_width' => 33.33,
					'is_pro'      => true,
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'Company Address', 'tourfic' ),
					'field_width' => 33.33,
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'Company Phone', 'tourfic' ),
					'field_width' => 33.33,
					'is_pro'      => true,
				),
				array(
					'id'    => 'export_heading',
					'type'  => 'heading',
					'label' => __( 'Talk to Expert', 'tourfic' ),
				),
				array(
					'id'      => '',
					'type'    => 'switch',
					'label'   => __( 'Enable Talk To Expert - Section in PDF', 'tourfic' ),
					'default' => true,
					'is_pro'  => true,
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'Talk to Expert - Label', 'tourfic' ),
					'field_width' => 25,
					'is_pro'      => true,
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'Expert Name', 'tourfic' ),
					'field_width' => 25,
					'is_pro'      => true,
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'Expert Email Address', 'tourfic' ),
					'field_width' => 25,
					'is_pro'      => true,
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'Expert Phone Address', 'tourfic' ),
					'field_width' => 25,
					'is_pro'      => true,
				),
				array(
					'id'     => '',
					'type'   => 'image',
					'label'  => __( 'Expert Avatar Image', 'tourfic' ),
					'is_pro' => true,
				),
				array(
					'id'     => '',
					'type'   => 'switch',
					'label'  => __( 'Viber Contact Available', 'tourfic' ),
					'is_pro' => true,
				),
				array(
					'id'     => '',
					'type'   => 'switch',
					'label'  => __( 'WhatsApp Contact Available', 'tourfic' ),
					'is_pro' => true,
				),
			),
		),

		// FAQs
		'faqs'                 => array(
			'title'  => __( 'FAQs', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'           => 'faqs',
					'type'         => 'repeater',
					'label'        => __( 'FAQs', 'tourfic' ),
					'button_title' => __( 'Add New Faq', 'tourfic' ),
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'FAQ Title', 'tourfic' ),
						),
						array(
							'id'    => 'desc',
							'type'  => 'editor',
							'label' => __( 'FAQ Subtitle', 'tourfic' ),
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
					'label' => __( 'Terms & Conditions of this tour', 'tourfic' ),
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
					'id'      => 'notice',
					'type'    => 'notice',
					'notice'  => 'info',
					'content' => __( 'These settings will overwrite global settings', 'tourfic' ),
				),
			),
		),
	),
) );
