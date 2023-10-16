<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

function tf_apt_amenities_cats() {
	$amenities_cats = ! empty( tf_data_types( tfopt( 'amenities_cats' ) ) ) ? tf_data_types( tfopt( 'amenities_cats' ) ) : '';
	$all_cats       = [];
	if ( ! empty( $amenities_cats ) && is_array( $amenities_cats ) ) {
		foreach ( $amenities_cats as $key => $cat ) {
			$all_cats[ (string) $key ] = $cat['amenities_cat_name'];
		}
	}

	if ( empty( $all_cats ) ) {
		$all_cats[''] = __( 'Select Category', 'tourfic' );
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
				array(
					'id'       => 'tf_single_apartment_layout_opt',
					'type'     => 'select',
					'label'    => __( 'Apartment Page Layout', 'tourfic' ),
					'subtitle' => __( 'Select your Layout logic', 'tourfic' ),
					'options'  => [
						'global' => __( 'Global Settings', 'tourfic' ),
						'single' => __( 'Single Settings', 'tourfic' ),
					],
					'default'  => 'global',
				),
				array(
					'id'         => 'tf_single_apartment_template',
					'type'       => 'imageselect',
					'label'      => __( 'Single Apartment Page Layout', 'tourfic' ),
					'multiple'   => true,
					'inline'     => true,
					'options'    => array(
						'default' => array(
							'title' => 'Default',
							'url'   => TF_ASSETS_ADMIN_URL . "images/template/default-apartment.jpg",
						),
					),
					'default'    => function_exists( 'tourfic_template_settings' ) ? tourfic_template_settings() : '',
					'dependency' => [
						array( 'tf_single_apartment_layout_opt', '==', 'single' )
					],
				),
			)
		),
		// location
		'location'        => array(
			'title'  => __( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'       => 'location_title',
					'type'     => 'text',
					'label'    => __( 'Section Title', 'tourfic' ),
					'subtitle' => __( 'Enter location section title', 'tourfic' ),
					'default'  => __( 'Where you’ll be', 'tourfic' ),
				),
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
					'id'           => 'surroundings_places',
					'type'         => 'repeater',
					'label'        => __( 'Surroundings Places', 'tourfic' ),
					'button_title' => __( 'Add New Criteria', 'tourfic' ),
					'is_pro'       => true,
					'fields'       => array(
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
							'id'           => 'places',
							'type'         => 'repeater',
							'label'        => __( 'Places', 'tourfic' ),
							'button_title' => __( 'Add New Place', 'tourfic' ),
							'fields'       => array(
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
					'id'      => 'booking_form_title',
					'type'    => 'text',
					'label'   => __( 'Form Title', 'tourfic' ),
					'default' => __( 'Book your apartment', 'tourfic' ),
				),
				array(
					'id'         => 'pricing_type',
					'type'       => 'select',
					'label'      => __( 'Pricing Type', 'tourfic' ),
					'subtitle'   => __( 'Select pricing type', 'tourfic' ),
					'options'    => array(
						'per_night'  => __( 'Per Night', 'tourfic' ),
						'per_person' => __( 'Per Person (pro)', 'tourfic' ),
					),
					'attributes' => array(
						'class' => 'tf_apt_pricing_type',
					),
				),
				array(
					'id'         => 'price_per_night',
					'type'       => 'number',
					'label'      => __( 'Price Per Night', 'tourfic' ),
					'subtitle'   => __( 'Enter price per night', 'tourfic' ),
					'attributes' => array( 'min' => 0 ),
					'dependency' => array( 'pricing_type', '==', 'per_night' ),
				),
				array(
					'id'          => '',
					'type'        => 'number',
					'label'       => __( 'Adult Price', 'tourfic' ),
					'subtitle'    => __( 'Enter adult price', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array( 'min' => 0 ),
					'dependency'  => array( 'pricing_type', '==', 'per_person' ),
				),
				array(
					'id'          => '',
					'type'        => 'number',
					'label'       => __( 'Child Price', 'tourfic' ),
					'subtitle'    => __( 'Enter child price', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array( 'min' => 0 ),
					'dependency'  => array( 'pricing_type', '==', 'per_person' ),
				),
				array(
					'id'          => '',
					'type'        => 'number',
					'label'       => __( 'Infant Price', 'tourfic' ),
					'subtitle'    => __( 'Enter infant price', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array( 'min' => 0 ),
					'dependency'  => array( 'pricing_type', '==', 'per_person' ),
				),
				array(
					'id'         => 'min_stay',
					'type'       => 'number',
					'label'      => __( 'Minimum Night Stay', 'tourfic' ),
					'subtitle'   => __( 'Enter minimum night stay', 'tourfic' ),
					'attributes' => array( 'min' => 1 )
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
				//Booking Type
				array(
					'id'      => '',
					'type'    => 'select',
					'label'   => __( 'Booking Type', 'tourfic' ),
					'options' => array(
						'1' => __( 'Internal', 'tourfic' ),
						'2' => __( 'External', 'tourfic' ),
					),
					'default' => '2',
					'is_pro'  => true,
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'External URL', 'tourfic' ),
					'placeholder' => __( 'https://website.com', 'tourfic' ),
					'is_pro'      => true
				),
				array(
					'id'        => '',
					'type'      => 'switch',
					'label'     => __( 'Allow Attribute', 'tourfic' ),
					'subtitle'  => __( 'If attribute allow, You can able to add custom Attribute', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'is_pro'    => true
				),
				array(
					'id'          => '',
					'type'        => 'textarea',
					'label'       => __( 'Query Attribute', 'tourfic' ),
					'placeholder' => __( 'adult={adult}&child={child}&infant={infant}', 'tourfic' ),
					'is_pro'      => true
				),
				array(
					'id'      => 'booking-notice',
					'type'    => 'notice',
					'class'   => 'info',
					'title'   => __( 'Query Attribute List', 'tourfic' ),
					'content' => __( 'You can use the following placeholders in the Query Attribute body:', 'tourfic' ) . '<br><br><strong>{adult} </strong> : To Display Adult Number from Search.<br>
					<strong>{child} </strong> : To Display Child Number from Search.<br>
					<strong>{infant} </strong> : To display the infant number from Search.<br>
					<strong>{checkin} </strong> : To display the Checkin date from Search.<br>
					<strong>{checkout} </strong> : To display the Checkout date from Search.<br>',
					'is_pro'  => true
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
		'availability'    => array(
			'title'  => __( 'Availability', 'tourfic' ),
			'icon'   => 'fa-solid fa-calendar-alt',
			'fields' => array(
				array(
					'id'      => 'Availability',
					'type'    => 'heading',
					'content' => __( 'Availability', 'tourfic' ),
				),
				array(
					'id'      => '',
					'type'    => 'switch',
					'label'   => __( 'Enable Availability by Date', 'tourfic' ),
					'is_pro'  => true,
					'default' => true
				),
				array(
					'id'        => '',
					'type'      => 'aptAvailabilityCal',
					'label'     => __( 'Availability Calendar', 'tourfic' ),
					'is_pro'  => true,
				),
				array(
					'id'      => 'ical',
					'type'    => 'heading',
					'content' => __( 'iCal Sync', 'tourfic' ),
				),
				array(
					'id'          => '',
					'type'        => 'ical',
					'label'       => __( 'iCal URL', 'tourfic' ),
					'placeholder' => __( 'https://website.com', 'tourfic' ),
					'button_text' => __( 'Import', 'tourfic' ),
					'button_class'   => 'apt-ical-import',
					'is_pro'  => true,
					'attributes'  => array(
						'class' => 'ical_url_input',
					),
				),
			),
		),
		//Room Management
		'room_management' => array(
			'title'  => __( 'Room Management', 'tourfic' ),
			'icon'   => 'fa-solid fa-bed',
			'fields' => array(
				array(
					'id'      => 'room_details_title',
					'type'    => 'text',
					'label'   => __( 'Section Title', 'tourfic' ),
					'default' => __( 'Where you\'ll sleep', 'tourfic' ),
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
					'id'      => 'highlights_title',
					'type'    => 'text',
					'label'   => __( 'Highlights Title', 'tourfic' ),
					'default' => __( 'Discover Our Top Features', 'tourfic' ),
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
					'id'      => 'amenities_title',
					'type'    => 'text',
					'label'   => __( 'Amenities Title', 'tourfic' ),
					'default' => __( 'What this place offers', 'tourfic' ),
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
							'description' => __( 'Add new category from <a target="_blank" href="' . admin_url( 'admin.php?page=tf_settings#tab=apartment_single_page' ) . '">Amenities Categories</a>', 'tourfic' ),
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
					'id'      => 'house_rules_title',
					'type'    => 'text',
					'label'   => __( 'House Rules Title', 'tourfic' ),
					'default' => __( 'House Rules', 'tourfic' ),
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
							'label'     => __( 'Allowed?', 'tourfic' ),
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
					'id'      => 'faq_title',
					'type'    => 'text',
					'label'   => __( 'Section Title', 'tourfic' ),
					'default' => __( 'Frequently Asked Questions', 'tourfic' ),
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
					'id'      => 'terms_title',
					'type'    => 'text',
					'label'   => __( 'Section Title', 'tourfic' ),
					'default' => __( 'Terms & Conditions', 'tourfic' ),
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
				//disable options
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
				//review
				array(
					'id'      => 'review-sections',
					'type'    => 'heading',
					'content' => __( 'Review', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'      => 'review-section-title',
					'type'    => 'text',
					'label'   => __( 'Reviews Section Title', 'tourfic' ),
					'default' => "Guest Reviews"
				),
				//Related Apartment
				array(
					'id'    => 'related',
					'type'  => 'heading',
					'label' => __( 'Related Apartment', 'tourfic' ),
				),
				array(
					'id'      => 'related_apartment_title',
					'type'    => 'text',
					'label'   => __( 'Related Apartment Title', 'tourfic' ),
					'default' => __( 'You may also like', 'tourfic' ),
				),
				//enquiry
				array(
					'id'      => 'enquiry',
					'type'    => 'heading',
					'content' => __( 'Enquiry', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'        => 'enquiry-section',
					'type'      => 'switch',
					'label'     => __( 'Apartment Enquiry Section', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => true
				),
				array(
					'id'         => 'enquiry-title',
					'type'       => 'text',
					'label'      => __( 'Apartment Enquiry Title Text', 'tourfic' ),
					'default'    => __( 'Have a question in mind', 'tourfic' ),
					'dependency' => array( 'enquiry-section', '==', '1' ),
				),
				array(
					'id'         => 'enquiry-content',
					'type'       => 'text',
					'label'      => __( 'Apartment Enquiry Short Text', 'tourfic' ),
					'default'    => __( 'Looking for more info? Send a question to the property to find out more.', 'tourfic' ),
					'dependency' => array( 'enquiry-section', '==', '1' ),
				),
				array(
					'id'         => 'enquiry-btn',
					'type'       => 'text',
					'label'      => __( 'Apartment Enquiry Button Text', 'tourfic' ),
					'default'    => __( 'Contact Host', 'tourfic' ),
					'dependency' => array( 'enquiry-section', '==', '1' ),
				),
			),
		),
	),
) );
