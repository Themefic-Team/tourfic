<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

TF_Metabox::metabox( 'tf_apartment_opt', array(
	'title'     => __( 'Apertment Options', 'tourfic' ),
	'post_type' => 'tf_apartment',
	'sections'  => array(
		// General
		'general'     => array(
			'title'  => __( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'apartment_gallery',
					'type'  => 'gallery',
					'label' => __( 'Apartment Gallery', 'tourfic' ),
				),
				array(
					'id'        => 'apartment_as_featured',
					'type'      => 'switch',
					'label'     => __( 'Featured Apartment', 'tourfic' ),
					'subtitle'  => __( 'This apartment will be highlighted at the top of the search result and apartment archive page', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'is_pro'    => true,
				),
			)
		),
		// location
		'location'    => array(
			'title'  => __( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'          => 'address',
					'type'        => 'text',
					'label'       => __( 'Apartment Address', 'tourfic' ),
					'subtitle'    => __( 'Enter apartment adress', 'tourfic' ),
					'placeholder' => __( 'Address', 'tourfic' ),
					'attributes'  => array(
						'required' => 'required',
					),
				),
				array(
					'id'       => 'map',
					'type'     => 'map',
					'label'    => __( 'Location on Map', 'tourfic' ),
					'subtitle' => __( 'Select one location on the map to see latitude and longitude', 'tourfic' ),
					'height'   => '250px',
					'settings' => array(
						'scrollWheelZoom' => true,
					),
					'is_pro'   => true,
				),
				//Property Surroundings
				array(
					'id'    => 'surroundings_heading',
					'type'  => 'heading',
					'label' => __( 'Property Surroundings', 'tourfic' ),
				),
				array(
					'id'       => 'surroundings_sec_title',
					'type'     => 'text',
					'label'    => __( 'Section Title', 'tourfic' ),
					'subtitle' => __( 'Enter surroundings section title', 'tourfic' ),
					'is_pro'   => true,
				),
				array(
					'id'       => 'surroundings_subtitle',
					'type'     => 'text',
					'label'    => __( 'Section sub title', 'tourfic' ),
					'subtitle' => __( 'Enter surroundings section sub title', 'tourfic' ),
					'is_pro'   => true,
				),
				array(
					'id'       => 'surroundings_places',
					'type'     => 'repeater',
					'label'    => __( 'Surroundings Places', 'tourfic' ),
					'is_pro'   => true,
					'subtitle' => __( 'Enter surroundings places', 'tourfic' ),
					'fields'   => array(
						array(
							'id'       => 'place_criteria_label',
							'type'     => 'text',
							'label'    => __( 'Place Criteria Label', 'tourfic' ),
							'subtitle' => __( 'Enter place criteria label', 'tourfic' ),
						),
						array(
							'id'       => 'place_criteria_icon',
							'type'     => 'icon',
							'label'    => __( 'Criteria Icon', 'tourfic' ),
							'subtitle' => __( 'Enter place criteria icon', 'tourfic' ),
						),
						array(
							'id'       => 'places',
							'type'     => 'repeater',
							'label'    => __( 'Places', 'tourfic' ),
							'subtitle' => __( 'Enter places', 'tourfic' ),
							'fields'   => array(
								array(
									'id'       => 'place_name',
									'type'     => 'text',
									'label'    => __( 'Place Name', 'tourfic' ),
									'subtitle' => __( 'Enter place name', 'tourfic' ),
								),
								array(
									'id'       => 'place_distance',
									'type'     => 'text',
									'label'    => __( 'Place Distance', 'tourfic' ),
									'subtitle' => __( 'Enter place distance', 'tourfic' ),
								),
							),
						)
					),
				),
			),
		),
		// Booking
		'booking'     => array(
			'title'  => __( 'Booking', 'tourfic' ),
			'icon'   => 'fa-solid fa-calendar-check',
			'fields' => array(
				array(
					'id'          => 'price_per_night',
					'type'        => 'number',
					'label'       => __( 'Price Per Night', 'tourfic' ),
					'subtitle'    => __( 'Enter price per night', 'tourfic' ),
					'field_width' => 50,
					'attributes'  => array( 'min' => 0 )
				),
				array(
					'id'          => 'min_stay',
					'type'        => 'number',
					'label'       => __( 'Minimum Night Stay', 'tourfic' ),
					'subtitle'    => __( 'Enter minimum night stay', 'tourfic' ),
					'field_width' => 50,
					'attributes'  => array( 'min' => 0 )
				),
				array(
					'id'          => 'max_adults',
					'type'        => 'number',
					'label'       => __( 'Maximum Adults', 'tourfic' ),
					'subtitle'    => __( 'Enter maximum adults', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array( 'min' => 1 )
				),
				array(
					'id'          => 'max_children',
					'type'        => 'number',
					'label'       => __( 'Maximum Children', 'tourfic' ),
					'subtitle'    => __( 'Enter maximum children', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array( 'min' => 0 )
				),
				array(
					'id'          => 'max_infants',
					'type'        => 'number',
					'label'       => __( 'Maximum Infants', 'tourfic' ),
					'subtitle'    => __( 'Enter maximum infants', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array( 'min' => 0 )
				),
				array(
					'id'          => 'additional_fee_label',
					'type'        => 'text',
					'label'       => __( 'Additional Fee Label', 'tourfic' ),
					'subtitle'    => __( 'Enter additional fee', 'tourfic' ),
					'field_width' => 33.33,
				),
				array(
					'id'          => 'additional_fee',
					'type'        => 'number',
					'label'       => __( 'Additional Fee', 'tourfic' ),
					'subtitle'    => __( 'Enter additional fee', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array( 'min' => 0 )
				),
				array(
					'id'          => 'fee_type',
					'type'        => 'select',
					'label'       => __( 'Fee Type', 'tourfic' ),
					'subtitle'    => __( 'Select fee type', 'tourfic' ),
					'options'     => array(
						'per_stay'   => __( 'Per Stay', 'tourfic' ),
						'per_night'  => __( 'Per Night', 'tourfic' ),
						'per_person' => __( 'Per Person', 'tourfic' ),
					),
					'field_width' => 33.33,
				),
				array(
					'id'          => 'discount_type',
					'type'        => 'select',
					'label'       => __( 'Discount Type', 'tourfic' ),
					'subtitle'    => __( 'Select discount type', 'tourfic' ),
					'options'     => array(
						'none'    => __( 'None', 'tourfic' ),
						'fixed'   => __( 'Fixed', 'tourfic' ),
						'percent' => __( 'Percent', 'tourfic' ),
					),
					'field_width' => 50,
				),
				array(
					'id'          => 'discount',
					'type'        => 'number',
					'label'       => __( 'Discount', 'tourfic' ),
					'subtitle'    => __( 'Enter discount', 'tourfic' ),
					'dependency'  => array( 'discount_type', '!=', 'none' ),
					'attributes'  => array( 'min' => 0 ),
					'field_width' => 50,
				),
				/*array(
					'id'       => 'weekly_discount',
					'type'     => 'number',
					'label'    => __( 'Weekly Discount Per Night', 'tourfic' ),
					'subtitle' => __( 'Weekly discounts for stays longer than 7 days (per night)', 'tourfic' ),
				),
				array(
					'id'       => 'monthly_discount',
					'type'     => 'number',
					'label'    => __( 'Monthly Discount Per Night', 'tourfic' ),
					'subtitle' => __( 'Monthly discounts for stays longer than 30 days (per night)', 'tourfic' ),
				),*/
			),
		),
		// Information
		'information' => array(
			'title'  => __( 'Informations', 'tourfic' ),
			'icon'   => 'fa-solid fa-circle-info',
			'fields' => array(
				array(
					'id'    => 'key_features_heading',
					'type'  => 'heading',
					'label' => __( 'Key Features', 'tourfic' ),
				),
				array(
					'id'       => 'key_features_title',
					'type'     => 'text',
					'label'    => __( 'Key Features Title', 'tourfic' ),
					'subtitle' => __( 'Enter key features title', 'tourfic' ),
				),
				array(
					'id'           => 'key_features',
					'type'         => 'repeater',
					'button_title' => __( 'Add New', 'tourfic' ),
					'label'        => __( 'Key Features', 'tourfic' ),
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
						),
						array(
							'id'    => 'icon',
							'type'  => 'icon',
							'label' => __( 'Icon', 'tourfic' ),
						),
					),
				),
				array(
					'id'    => 'facilities_heading',
					'type'  => 'heading',
					'label' => __( 'Facilities', 'tourfic' ),
				),
				array(
					'id'       => 'facilities_title',
					'type'     => 'text',
					'label'    => __( 'Facilities Title', 'tourfic' ),
					'subtitle' => __( 'Enter facilities title', 'tourfic' ),
				),
				array(
					'id'           => 'facilities',
					'type'         => 'repeater',
					'button_title' => __( 'Add New', 'tourfic' ),
					'label'        => __( 'Facilities', 'tourfic' ),
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
						),
						array(
							'id'    => 'subtitle',
							'type'  => 'text',
							'label' => __( 'Sub Title', 'tourfic' ),
						),
						array(
							'id'    => 'thumbnail',
							'type'  => 'image',
							'label' => __( 'Thumbnail', 'tourfic' ),
						),
					),
				),
				array(
					'id'    => 'house_rules_heading',
					'type'  => 'heading',
					'label' => __( 'House Rules', 'tourfic' ),
				),
				array(
					'id'           => 'house_rules',
					'type'         => 'repeater',
					'button_title' => __( 'Add New', 'tourfic' ),
					'label'        => __( 'House Rules', 'tourfic' ),
					'fields'       => array(
						array(
							'id'        => 'include',
							'type'      => 'switch',
							'label'     => __( 'Include?', 'tourfic' ),
							'label_on'  => __( 'Yes', 'tourfic' ),
							'label_off' => __( 'No', 'tourfic' ),
							'default'   => true,
						),
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
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
		// faq and terms and conditions
		'faq'         => array(
			'title'  => __( 'FAQ & Terms', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'    => 'faq_heading',
					'type'  => 'heading',
					'label' => __( 'Frequently Asked Questions', 'tourfic' ),
				),
				array(
					'id'       => 'faq_title',
					'type'     => 'text',
					'label'    => __( 'FAQ Title', 'tourfic' ),
					'subtitle' => __( 'Enter FAQ title', 'tourfic' ),
				),
				array(
					'id'       => 'faq_desc',
					'type'     => 'textarea',
					'label'    => __( 'FAQ Description', 'tourfic' ),
					'subtitle' => __( 'Enter FAQ description', 'tourfic' ),
				),
				array(
					'id'           => 'faq',
					'type'         => 'repeater',
					'button_title' => __( 'Add New', 'tourfic' ),
					'label'        => __( 'Frequently Asked Questions', 'tourfic' ),
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
				array(
					'id'    => 'terms_heading',
					'type'  => 'heading',
					'label' => __( 'Terms & Conditions', 'tourfic' ),
				),
				array(
					'id'       => 'terms_title',
					'type'     => 'text',
					'label'    => __( 'Terms Title', 'tourfic' ),
					'subtitle' => __( 'Enter terms title', 'tourfic' ),
				),
				array(
					'id'       => 'terms_and_conditions',
					'type'     => 'editor',
					'label'    => __( 'Terms & Conditions', 'tourfic' ),
					'subtitle' => __( 'Enter terms & conditions', 'tourfic' ),
				),
			),
		),
		// Settings
		'settings'    => array(
			'title'  => __( 'Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-viruses',
			'fields' => array(
				array(
					'id'      => 'disable_options',
					'type'    => 'heading',
					'label'   => __( 'Disable Options', 'tourfic' ),
					'content' => __( 'These settings will overwrite global settings', 'tourfic' ),
				),
				array(
					'id'          => 'disable-apartment-review',
					'type'        => 'switch',
					'label'       => __( 'Disable Review Section', 'tourfic' ),
					'label_on'    => __( 'Yes', 'tourfic' ),
					'label_off'   => __( 'No', 'tourfic' ),
					'default'     => false,
				),
				array(
					'id'          => 'disable-apartment-share',
					'type'        => 'switch',
					'label'       => __( 'Disable Share Option', 'tourfic' ),
					'label_on'    => __( 'Yes', 'tourfic' ),
					'label_off'   => __( 'No', 'tourfic' ),
					'default'     => false,
				),
				array(
					'id'          => 'disable-related-apartment',
					'type'        => 'switch',
					'label'       => __( 'Disable Related Section', 'tourfic' ),
					'label_on'    => __( 'Yes', 'tourfic' ),
					'label_off'   => __( 'No', 'tourfic' ),
					'default'     => false,
				),
			),
		),
	),
) );
