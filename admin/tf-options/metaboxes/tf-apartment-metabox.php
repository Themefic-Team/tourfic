<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

function tf_apt_amenities_cats() {
	$amenities_cats = ! empty( tf_data_types( tfopt( 'amenities_cats' ) ) ) ? tf_data_types( tfopt( 'amenities_cats' ) ) : '';
	$all_cats       = [];
	if ( ! empty( $amenities_cats ) && is_array( $amenities_cats ) ) {
		foreach ( $amenities_cats as $key => $cat ) {
			$all_cats[ sanitize_title( $cat['amenities_cat_name'] ) ] = $cat['amenities_cat_name'];
		}
	}

	return $all_cats;
}

TF_Metabox::metabox( 'tf_apartment_opt', array(
	'title'     => __( 'Apertment Options', 'tourfic' ),
	'post_type' => 'tf_apartment',
	'sections'  => array(
		// General
		'general'         => array(
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
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
				),
				array(
					'id'          => 'featured_text',
					'type'        => 'text',
					'label'       => __( 'Apartment Featured Text', 'tourfic' ),
					'subtitle'    => __( 'Enter Featured Apartment Text', 'tourfic' ),
					'placeholder' => __( 'Enter Featured Apartment Text', 'tourfic' ),
					'default'     => __( 'Hot Deal', 'tourfic' ),
					'dependency'  => array( 'apartment_as_featured', '==', true ),
				),
			)
		),
		// location
		'location'        => array(
			'title'  => __( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'          => 'address',
					'type'        => 'textarea',
					'label'       => __( 'Apartment Address', 'tourfic' ),
					'subtitle'    => __( 'The address you want to show below the apartment title', 'tourfic' ),
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
					'subtitle' => __( 'Write your desired address and select the address from the suggestions. This address will be used to hyperlink the apartment address on the frontend.', 'tourfic' ),
					'height'   => '250px',
					'settings' => array(
						'scrollWheelZoom' => true,
					)
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
					'label'    => __( 'Section Subtitle', 'tourfic' ),
					'subtitle' => __( 'Enter surroundings section subtitle', 'tourfic' ),
					'is_pro'   => true,
				),
				array(
					'id'     => 'surroundings_places',
					'type'   => 'repeater',
					'label'  => __( 'Surroundings Places', 'tourfic' ),
					'is_pro' => true,
					'fields' => array(
						array(
							'id'          => 'place_criteria_label',
							'type'        => 'text',
							'label'       => __( 'Place Criteria Label', 'tourfic' ),
							'placeholder' => __( 'Enter place criteria label', 'tourfic' ),
						),
						array(
							'id'    => 'place_criteria_icon',
							'type'  => 'icon',
							'label' => __( 'Criteria Icon', 'tourfic' ),
						),
						array(
							'id'     => 'places',
							'type'   => 'repeater',
							'label'  => __( 'Places', 'tourfic' ),
							'fields' => array(
								array(
									'id'          => 'place_name',
									'type'        => 'text',
									'label'       => __( 'Place Name', 'tourfic' ),
									'placeholder' => __( 'Enter place name', 'tourfic' ),
								),
								array(
									'id'          => 'place_distance',
									'type'        => 'text',
									'label'       => __( 'Place Distance', 'tourfic' ),
									'placeholder' => __( 'Enter place distance', 'tourfic' ),
								),
							),
						)
					),
				),
			),
		),
		// Booking
		'booking'         => array(
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
					'subtitle'    => __( 'Select Discount Type: Percentage or Fixed', 'tourfic' ),
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
					'label'       => __( 'Discount Amount', 'tourfic' ),
					'subtitle'    => __( 'Insert your discount amount', 'tourfic' ),
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
		//Room Management
		'room_management' => array(
			'title'  => __( 'Room Management', 'tourfic' ),
			'icon'   => 'fa-solid fa-bed',
			'fields' => array(
				array(
					'id'    => 'room_details_title',
					'type'  => 'text',
					'label' => __( 'Section Title', 'tourfic' ),
				),
				array(
					'id'           => 'rooms',
					'type'         => 'repeater',
					'button_title' => __( 'Add New', 'tourfic' ),
					'label'        => __( 'Rooms', 'tourfic' ),
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Room Title', 'tourfic' ),
						),
						array(
							'id'    => 'subtitle',
							'type'  => 'text',
							'label' => __( 'Room Subtitle', 'tourfic' ),
						),
						array(
							'id'     => 'description',
							'type'   => 'editor',
							'label'  => __( 'Room Description', 'tourfic' ),
							'is_pro' => true,
						),
						array(
							'id'    => 'thumbnail',
							'type'  => 'image',
							'label' => __( 'Room Thumbnail', 'tourfic' ),
						),
						array(
							'id'     => '',
							'type'   => 'gallery',
							'label'  => __( 'Room Gallery', 'tourfic' ),
							'is_pro' => true,
						),
						array(
							'id'          => '',
							'type'        => 'select',
							'label'       => __( 'Room Type', 'tourfic' ),
							'subtitle'    => __( 'Select room type', 'tourfic' ),
							'options'     => array(
								'bedroom'     => __( 'Bedroom', 'tourfic' ),
								'common_room' => __( 'Common Room', 'tourfic' ),
								'kitchen'     => __( 'Kitchen', 'tourfic' ),
								'bathroom'    => __( 'Bathroom', 'tourfic' ),
							),
							'field_width' => 50,
							'is_pro'      => true,
						),
						array(
							'id'          => '',
							'type'        => 'text',
							'label'       => __( 'Room Footage', 'tourfic' ),
							'subtitle'    => __( 'Room footage (in sft)', 'tourfic' ),
							'field_width' => 50,
							'is_pro'      => true,
						),
						array(
							'id'          => '',
							'type'        => 'number',
							'label'       => __( 'Number of Beds', 'tourfic' ),
							'subtitle'    => __( 'Number of beds available in the room', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
							'is_pro'      => true,
						),
						array(
							'id'          => '',
							'type'        => 'number',
							'label'       => __( 'Number of Adults', 'tourfic' ),
							'subtitle'    => __( 'Max number of persons allowed in the room', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
							'is_pro'      => true,
						),
						array(
							'id'          => '',
							'type'        => 'number',
							'label'       => __( 'Number of Children', 'tourfic' ),
							'subtitle'    => __( 'Max number of children allowed in the room', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
							'is_pro'      => true,
						),
						array(
							'id'          => '',
							'type'        => 'number',
							'label'       => __( 'Number of Infants', 'tourfic' ),
							'subtitle'    => __( 'Max number of infants allowed in the room', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
							'is_pro'      => true,
						),
					),
				),
			),
		),

		// Information
		'information'     => array(
			'title'  => __( 'Information\'s', 'tourfic' ),
			'icon'   => 'fa-solid fa-circle-info',
			'fields' => array(
				//highlights
				array(
					'id'    => 'highlights_heading',
					'type'  => 'heading',
					'label' => __( 'Highlights', 'tourfic' ),
				),
				array(
					'id'    => 'highlights_title',
					'type'  => 'text',
					'label' => __( 'Highlights Title', 'tourfic' ),
				),
				array(
					'id'           => 'highlights',
					'type'         => 'repeater',
					'button_title' => __( 'Add New', 'tourfic' ),
					'label'        => __( 'Highlights', 'tourfic' ),
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
						),
						array(
							'id'    => 'subtitle',
							'type'  => 'text',
							'label' => __( 'Subtitle', 'tourfic' ),
						),
						array(
							'id'    => 'icon',
							'type'  => 'icon',
							'label' => __( 'Icon', 'tourfic' ),
						),
					),
				),
				//amenities
				array(
					'id'    => 'amenities_heading',
					'type'  => 'heading',
					'label' => __( 'Amenities', 'tourfic' ),
				),
				array(
					'id'    => 'amenities_title',
					'type'  => 'text',
					'label' => __( 'Amenities Title', 'tourfic' ),
				),
				array(
					'id'           => 'amenities',
					'type'         => 'repeater',
					'button_title' => __( 'Add New', 'tourfic' ),
					'label'        => __( 'Amenities', 'tourfic' ),
					'fields'       => array(
						array(
							'id'          => 'feature',
							'type'        => 'select2',
							'label'       => __( 'Feature', 'tourfic' ),
							'placeholder' => __( 'Select feature', 'tourfic' ),
							'options'     => 'terms',
							'query_args'  => array(
								'taxonomy'   => 'apartment_feature',
								'hide_empty' => false,
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'cat',
							'type'        => 'select2',
							'label'       => __( 'Category', 'tourfic' ),
							'placeholder' => __( 'Select category', 'tourfic' ),
							'options'     => tf_apt_amenities_cats(),
							'field_width' => 50,
						),
						array(
							'id'        => 'favorite',
							'type'      => 'switch',
							'label'     => __( 'Mark as Favorite', 'tourfic' ),
							'label_on'  => __( 'Yes', 'tourfic' ),
							'label_off' => __( 'No', 'tourfic' ),
						),
					),
				),

				//house rules
				array(
					'id'    => 'house_rules_heading',
					'type'  => 'heading',
					'label' => __( 'House Rules', 'tourfic' ),
				),
				array(
					'id'    => 'house_rules_title',
					'type'  => 'text',
					'label' => __( 'House Rules Title', 'tourfic' ),
				),
				array(
					'id'           => 'house_rules',
					'type'         => 'repeater',
					'button_title' => __( 'Add New', 'tourfic' ),
					'label'        => __( 'House Rules', 'tourfic' ),
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
						),
						array(
							'id'    => 'desc',
							'type'  => 'editor',
							'label' => __( 'Description', 'tourfic' ),
						),
						array(
							'id'        => 'include',
							'type'      => 'switch',
							'label'     => __( 'Include?', 'tourfic' ),
							'label_on'  => __( 'Yes', 'tourfic' ),
							'label_off' => __( 'No', 'tourfic' ),
							'default'   => true,
						),
					),
				),
			),
		),
		// faq and terms and conditions
		'faq'             => array(
			'title'  => __( 'FAQ & Terms', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'    => 'faq_heading',
					'type'  => 'heading',
					'label' => __( 'Frequently Asked Questions', 'tourfic' ),
				),
				array(
					'id'    => 'faq_title',
					'type'  => 'text',
					'label' => __( 'Section Title', 'tourfic' ),
				),
				array(
					'id'    => 'faq_desc',
					'type'  => 'textarea',
					'label' => __( 'Section Description', 'tourfic' ),
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
					'id'    => 'terms_title',
					'type'  => 'text',
					'label' => __( 'Section Title', 'tourfic' ),
				),
				array(
					'id'    => 'terms_and_conditions',
					'type'  => 'editor',
					'label' => __( 'Apartment Terms & Conditions', 'tourfic' ),
				),
			),
		),
		// Settings
		'settings'        => array(
			'title'  => __( 'Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-viruses',
			'fields' => array(
				//description
				array(
					'id'    => 'description',
					'type'  => 'heading',
					'label' => __( 'Description', 'tourfic' ),
				),
				array(
					'id'      => 'description_title',
					'type'    => 'text',
					'label'   => __( 'Description Title', 'tourfic' ),
					'default' => __( 'About this place', 'tourfic' ),
				),
				array(
					'id'      => 'disable_options',
					'type'    => 'heading',
					'label'   => __( 'Disable Options', 'tourfic' ),
					'content' => __( 'These settings will overwrite global settings', 'tourfic' ),
				),
				array(
					'id'        => 'disable-apartment-review',
					'type'      => 'switch',
					'label'     => __( 'Disable Review Section', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false,
				),
				array(
					'id'        => 'disable-apartment-share',
					'type'      => 'switch',
					'label'     => __( 'Disable Share Option', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false,
				),
				array(
					'id'        => 'disable-related-apartment',
					'type'      => 'switch',
					'label'     => __( 'Disable Related Section', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false,
				),
			),
		),
	),
) );
