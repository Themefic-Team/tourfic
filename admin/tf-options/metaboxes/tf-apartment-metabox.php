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
					'id'       => 'apartment_as_featured',
					'type'     => 'switch',
					'label'    => __( 'Set this apartment as featured', 'tourfic' ),
					'subtitle' => __( 'Apartment will be shown under featured sections', 'tourfic' ),
				),
				array(
					'id'    => 'apartment_gallery',
					'type'  => 'gallery',
					'label' => __( 'Apartment Gallery', 'tourfic' ),
				),

				array(
					'id'          => 'disable-apartment-review',
					'type'        => 'switch',
					'label'       => __( 'Disable Review Section', 'tourfic' ),
					'label_on'    => __( 'Yes', 'tourfic' ),
					'label_off'   => __( 'No', 'tourfic' ),
					'default'     => false,
					'field_width' => 50,
				),

				array(
					'id'          => 'disable-apartment-share',
					'type'        => 'switch',
					'label'       => __( 'Disable Share Option', 'tourfic' ),
					'label_on'    => __( 'Yes', 'tourfic' ),
					'label_off'   => __( 'No', 'tourfic' ),
					'default'     => false,
					'field_width' => 50,
				),

				array(
					'id'          => 'disable-related-apartment',
					'type'        => 'switch',
					'label'       => __( 'Disable Related Section', 'tourfic' ),
					'label_on'    => __( 'Yes', 'tourfic' ),
					'label_off'   => __( 'No', 'tourfic' ),
					'default'     => false,
					'field_width' => 50,
				),

				array(
					'id'      => 'notice',
					'type'    => 'notice',
					'notice'  => 'info',
					'content' => __( 'These settings will overwrite global settings', 'tourfic' ),
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
				),
				//Property Surroundings
				array(
					'id'      => 'surroundings_heading',
					'type'    => 'heading',
					'content' => __( 'Property Surroundings', 'tourfic' ),
				),
				array(
					'id'       => 'surroundings_sec_title',
					'type'     => 'text',
					'label'    => __( 'Section Title', 'tourfic' ),
					'subtitle' => __( 'Enter surroundings section title', 'tourfic' ),
				),
				array(
					'id'       => 'surroundings_subtitle',
					'type'     => 'text',
					'label'    => __( 'Section sub title', 'tourfic' ),
					'subtitle' => __( 'Enter surroundings section sub title', 'tourfic' ),
				),
				array(
					'id'       => 'surroundings_places',
					'type'     => 'repeater',
					'label'    => __( 'Surroundings Places', 'tourfic' ),
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
			'icon'   => 'fa-solid fa-sack-dollar',
			'fields' => array(
				array(
					'id'          => 'price_per_night',
					'type'        => 'number',
					'label'       => __( 'Price Per Night', 'tourfic' ),
					'subtitle'    => __( 'Enter price per night', 'tourfic' ),
					'field_width' => 50,
					'attributes'  => array(
						'min' => 0
					)
				),
				array(
					'id'          => 'min_stay',
					'type'        => 'number',
					'label'       => __( 'Minimum Night Stay', 'tourfic' ),
					'subtitle'    => __( 'Enter minimum night stay', 'tourfic' ),
					'field_width' => 50,
					'attributes'  => array(
						'min' => 0
					)
				),
				array(
					'id'          => 'max_adults',
					'type'        => 'number',
					'label'       => __( 'Maximum Adults', 'tourfic' ),
					'subtitle'    => __( 'Enter maximum adults', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array(
						'min' => 1
					)
				),
				array(
					'id'          => 'max_children',
					'type'        => 'number',
					'label'       => __( 'Maximum Children', 'tourfic' ),
					'subtitle'    => __( 'Enter maximum children', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array(
						'min' => 0
					)
				),
				array(
					'id'          => 'max_infants',
					'type'        => 'number',
					'label'       => __( 'Maximum Infants', 'tourfic' ),
					'subtitle'    => __( 'Enter maximum infants', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array(
						'min' => 0
					)
				),
				array(
					'id'          => 'service_fee',
					'type'        => 'number',
					'label'       => __( 'Service Fee (Per Night)', 'tourfic' ),
					'subtitle'    => __( 'Enter service fee', 'tourfic' ),
					'field_width' => 50,
					'attributes'  => array(
						'min' => 0
					)
				),
				array(
					'id'          => 'cleaning_fee',
					'type'        => 'number',
					'label'       => __( 'Cleaning Fee', 'tourfic' ),
					'subtitle'    => __( 'Enter cleaning fee', 'tourfic' ),
					'field_width' => 50,
					'attributes'  => array(
						'min' => 0
					)
				),
				array(
					'id'       => 'weekly_discount',
					'type'     => 'number',
					'label'    => __( 'Weekly Discount Per Night', 'tourfic' ),
					'subtitle' => __( 'Weekly discounts for stays longer than 7 days (per night)', 'tourfic' ),
				)
			),
		),
		// Information
		'information' => array(
			'title'  => __( 'Informations', 'tourfic' ),
			'icon'   => 'fa-solid fa-circle-info',
			'fields' => array(
				array(
					'id'      => 'key_features_heading',
					'type'    => 'heading',
					'content' => __( 'Key Features', 'tourfic' ),
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
					'id'      => 'facilities_heading',
					'type'    => 'heading',
					'content' => __( 'Facilities', 'tourfic' ),
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
					'id'      => 'house_rules_heading',
					'type'    => 'heading',
					'content' => __( 'House Rules', 'tourfic' ),
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
					'id'      => 'faq_heading',
					'type'    => 'heading',
					'content' => __( 'Frequently Asked Questions', 'tourfic' ),
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
					'id'      => 'terms_heading',
					'type'    => 'heading',
					'content' => __( 'Terms & Conditions', 'tourfic' ),
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
	),
) );
