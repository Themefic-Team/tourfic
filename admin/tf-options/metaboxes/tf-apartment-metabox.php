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
	'title'     => __( 'Apartment Settings', 'tourfic' ),
	'post_type' => 'tf_apartment',
	'sections'  => array(
		// General
		'general'         => array(
			'title'  => __( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'apartment-general-heading',
					'type'  => 'heading',
					'label' => 'General Settings ',
					'subtitle' => __( 'These are some common settings specific to this Apartment.', 'tourfic' ),
				),
				array(
					'id'      => 'apartment-general-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/general-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'apartment_gallery',
					'type'  => 'gallery',
					'label' => __( 'Apartment Gallery', 'tourfic' ),
					'subtitle'    => __( 'Add multiple images to craft a captivating gallery for your apartments, giving potential customers a visual tour.', 'tourfic' ),
				),
				array(
					'id'        => 'apartment_as_featured',
					'type'      => 'switch',
					'label'     => __( 'Featured Apartment', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'subtitle'    => __( 'Enable this option to feature this apartment at the top of search results.', 'tourfic' ),
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
					'label'    => __( 'Single Apartment Template Settings', 'tourfic' ),
					'subtitle' => __( 'You can keep the Global Template settings or choose a different layout for this apartment.', 'tourfic' ),
					'options'  => [
						'global' => __( 'Global Settings', 'tourfic' ),
						'single' => __( 'Single Settings', 'tourfic' ),
					],
					'default'  => 'global',
				),
				array(
					'id'       => 'tf_single_apartment_template',
					'type'     => 'imageselect',
					'label'    => __( 'Single Apartment Page Layout', 'tourfic' ),
					'multiple' 		=> true,
					'inline'   		=> true,
					'options'   	=> array(
						'default' 			=> array(
							'title'			=> 'Default',
							'url' 			=> TF_ASSETS_ADMIN_URL."images/template/default-apartment.jpg",
						),
					),
					'default'   	=> 'default',
					'dependency'  => [
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
					'id'    => 'apartment-location-heading',
					'type'  => 'heading',
					'label' => 'Location Settings',
					'subtitle' => __( 'The location of an apartment is a crucial element for every apartment. Set your apartment locations in this section.', 'tourfic' ),
				),
				array(
					'id'      => 'apartment-location-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/location-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'location_title',
					'type'     => 'text',
					'label'    => __( 'Title of this Section', 'tourfic' ),
					'subtitle' => __( 'This text will appear as the heading of the Location section on the frontend.', 'tourfic' ),
					'default'  => __( 'Where you’ll be', 'tourfic' ),
				),
				array(
					'id'       => 'map',
					'class'    => 'gmaps',
					'type'     => 'map',
					'label'    => __( 'Dynamic Location Search', 'tourfic' ),
					'subtitle' => __( 'Enter the specific address you wish to use for the apartment and select the correct option from the suggested addresses. This will be used to hyperlink address and display the address on the front-end map. Note that the address provided in the previous section is solely for display purposes!', 'tourfic' ),
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
					'label'    => __( 'Title of the Section', 'tourfic' ),
					'is_pro'   => true,
				),
				array(
					'id'       => 'surroundings_subtitle',
					'type'     => 'text',
					'label'    => __( 'Section Description', 'tourfic' ),
					'is_pro'   => true,
				),
				array(
					'id'     => 'surroundings_places',
					'type'   => 'repeater',
					'label'    => __( 'Add Surrounding Places', 'tourfic' ),
					'button_title' => __( 'Add New Criteria & Place', 'tourfic' ),
					'is_pro' => true,
					'fields' => array(
						array(
							'id'          => 'place_criteria_label',
							'type'        => 'text',
							'label'    => __( 'Place Criteria Heading', 'tourfic' ),
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
							'button_title' => __( 'Add New Place', 'tourfic' ),
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
			'title'  => __( 'Booking Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-calendar-check',
			'fields' => array(
				array(
					'id'    => 'apartment-booking-heading',
					'type'  => 'heading',
					'label' => 'Booking Settings',
					'subtitle' => __( 'This section offer the options to customize the booking process for this apartment.', 'tourfic' ),
				),
				array(
					'id'      => 'apartment-booking-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/booking-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'booking_form_title',
					'type'  => 'text',
					'label' => __( 'Form Title', 'tourfic' ),
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
					'id'          => 'price_per_night',
					'type'        => 'number',
					'label'       => __( 'Price Per Night', 'tourfic' ),
					'subtitle'    => __( 'The total booking cost is calculated by multiplying the nightly rate by the number of nights stayed, from check-in to check-out.', 'tourfic' ),
					'field_width' => 50,
					'attributes'  => array( 'min' => 0 ),
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
					'id'          => 'min_stay',
					'type'        => 'number',
					'label'       => __( 'Minimum Night Stay', 'tourfic' ),
					'subtitle'    => __( 'Specify the minimum number of nights required to book this room.', 'tourfic' ),
					'attributes'  => array( 'min' => 1 )
				),
				array(
					'id'          => 'max_adults',
					'type'        => 'number',
					'label'       => __( 'Maximum Adults', 'tourfic' ),
					'subtitle'    => __( 'Max number of adults allowed in the apartment.', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array( 'min' => 1 )
				),
				array(
					'id'          => 'max_children',
					'type'        => 'number',
					'label'       => __( 'Maximum Children', 'tourfic' ),
					'subtitle'    => __( 'Max number of child allowed in the apartment.', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array( 'min' => 0 )
				),
				array(
					'id'          => 'max_infants',
					'type'        => 'number',
					'label'       => __( 'Maximum Infants', 'tourfic' ),
					'subtitle'    => __( 'Max number of infants allowed in the apartment.', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array( 'min' => 0 )
				),
				array(
					'id'          => 'additional_fee_label',
					'type'        => 'text',
					'label'       => __( 'Additional Fee Label', 'tourfic' ),
					'field_width' => 33.33,
				),
				array(
					'id'          => 'additional_fee',
					'type'        => 'number',
					'label'       => __( 'Additional Fee Amount', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array( 'min' => 0 )
				),
				array(
					'id'          => 'fee_type',
					'type'        => 'select',
					'label'       => __( 'Fee Type', 'tourfic' ),
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
					'subtitle'    => __( 'Set a discount for this room to incentivize bookings. Choose between a fixed amount off or a percentage-based reduction.', 'tourfic' ),
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
					'id'      => 'apt-booking-by',
					'type'    => 'select',
					'label'   => __( 'Booking Type', 'tourfic' ),
					'options' => array(
						'1' => __( 'Default Booking (WooCommerce)', 'tourfic' ),
						'2' => __( 'External Booking (Pro)', 'tourfic' ),
					),
					'default' => '2',
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'External Booking URL', 'tourfic' ),
					'placeholder' => __( 'https://website.com', 'tourfic' ),
					'is_pro'      => true,
					'dependency'  => array( 'apt-booking-by', '==', '2' ),
				),
				array(
					'id'        => '',
					'type'      => 'switch',
					'label'     => __( 'Hide Booking Form', 'tourfic' ),
					'subtitle' => __( 'Enable this option to hide the booking form from the single hotel page.', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'is_pro'  => true,
					'dependency' => array( 'apt-booking-by', '==', '2' ),
				),
				array(
					'id'        => '',
					'type'      => 'switch',
					'label'     => __( 'Hide Price', 'tourfic' ),
					'subtitle' => __( 'Enable this option to hide the price from the single hotel page.', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'is_pro'  => true,
					'dependency' => array( 'apt-booking-by', '==', '2' ),
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
					'id'    => 'apartment-room-heading',
					'type'  => 'heading',
					'label' => 'Create & Manage Your Apartment Rooms',
					'subtitle' => __( 'In this section, you are provided with the tools to create and manage your apartment room offerings.', 'tourfic' ),
				),
				array(
					'id'      => 'apartment-room-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/room-management/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'room_details_title',
					'type'  => 'text',
					'label' => __( 'Title of the Section', 'tourfic' ),
					'subtitle' => __( 'This text will appear as the heading of the Rooms section on the frontend.', 'tourfic' ),
					'default' => __( 'Where you\'ll sleep', 'tourfic' ),
				),
				array(
					'id'           => 'rooms',
					'type'         => 'repeater',
					'button_title' => __( 'Add New', 'tourfic' ),
					'label'        => __( 'Create your apartment rooms ', 'tourfic' ),
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
							'label' => __( 'Room Image Thumbnail ', 'tourfic' ),
						),
						array(
							'id'     => '',
							'type'   => 'gallery',
							'label' => __( 'Room Gallery', 'tourfic' ),
							'subtitle' => __( 'Upload all the images specific to this room.', 'tourfic' ),
							'is_pro' => true,
						),
						array(
							'id'          => '',
							'type'        => 'select',
							'label'       => __( 'Room Type', 'tourfic' ),
							'subtitle' => __( 'Select the type of room you are offering.', 'tourfic' ),
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
							'subtitle'    => __( 'Room footage (in sft).', 'tourfic' ),
							'field_width' => 50,
							'is_pro'      => true,
						),
						array(
							'id'          => '',
							'type'        => 'number',
							'label'       => __( 'Number of Beds', 'tourfic' ),
							'subtitle'    => __( 'Number of beds available in the room.', 'tourfic' ),
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
							'subtitle'    => __( 'Max number of adults allowed in the room.', 'tourfic' ),
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
							'subtitle'    => __( 'Max number of children allowed in the room.', 'tourfic' ),
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
							'subtitle'    => __( 'Max number of infants allowed in the room.', 'tourfic' ),
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
			'title'  => __( 'Information', 'tourfic' ),
			'icon'   => 'fa-solid fa-circle-info',
			'fields' => array(
				array(
					'id'    => 'apartment-info-heading',
					'type'  => 'heading',
					'label' => 'Information Section ',
					'subtitle' => __( 'Ensure to furnish customers with all the essential information they need to fully understand your apartment offer.', 'tourfic' ),
				),
				array(
					'id'      => 'apartment-info-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/information-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				//highlights
				array(
					'id'    => 'highlights_heading',
					'type'  => 'heading',
					'label' => __( 'Highlights', 'tourfic' ),
				),
				array(
					'id'    => 'highlights_title',
					'type'  => 'text',
					'label' => __( 'Title of the Section', 'tourfic' ),
					'subtitle' => __( 'This text will appear as the heading of the Highlights section on the frontend.', 'tourfic' ),
					'default' => __( 'Discover Our Top Features', 'tourfic' ),
				),
				array(
					'id'           => 'highlights',
					'type'         => 'repeater',
					'button_title' => __( 'Add New', 'tourfic' ),
					'label'        => __( 'Add Highlights', 'tourfic' ),
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
					'label' => __( 'Title of the Section', 'tourfic' ),
					'subtitle' => __( 'This text will appear as the heading of the Amenities section on the frontend.', 'tourfic' ),
					'default' => __( 'What this place offers', 'tourfic' ),
				),
				array(
					'id'           => 'amenities',
					'type'         => 'repeater',
					'button_title' => __( 'Add New', 'tourfic' ),
					'label'        => __( 'Add Amenities', 'tourfic' ),
					'fields'       => array(
						array(
							'id'          => 'feature',
							'type'        => 'select2',
							'label'       => __( 'Feature', 'tourfic' ),
							'placeholder' => __( 'Select feature', 'tourfic' ),
							'description' => __( 'Add new features from <a target="_blank" href="'.admin_url('edit-tags.php?taxonomy=apartment_feature&post_type=tf_apartment').'">Apartment Features</a>.', 'tourfic' ),
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
							'description' => __( 'Add new category from <a target="_blank" href="'.admin_url('admin.php?page=tf_settings#tab=apartment_single_page').'">Amenities Categories</a>.', 'tourfic' ),
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
					'label' => __( 'Title of the Section', 'tourfic' ),
					'subtitle' => __( 'This text will appear as the heading of the House Rules section on the frontend.', 'tourfic' ),
					'default' => __( 'House Rules', 'tourfic' ),
				),
				array(
					'id'           => 'house_rules',
					'type'         => 'repeater',
					'button_title' => __( 'Add New', 'tourfic' ),
					'label'        => __( 'Add House Rules', 'tourfic' ),
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
			'title'  => __( 'FAQ and Terms', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'    => 'apartment-faq-heading',
					'type'  => 'heading',
					'label' => 'FAQ Section',
					'subtitle' => __( 'This section is designed to help users find answers to common questions.', 'tourfic' ),
				),
				array(
					'id'      => 'apartment-faq-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/faq-terms/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'faq_heading',
					'type'  => 'heading',
					'label' => __( 'Frequently Asked Questions', 'tourfic' ),
				),
				array(
					'id'    => 'faq_title',
					'type'  => 'text',
					'label'    => __( 'Title of the Section', 'tourfic' ),
					'subtitle' => __( 'This text will appear as the heading of the FAQ section on the frontend.', 'tourfic' ),
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
					'button_title' => __( 'Add New FAQ', 'tourfic' ),
					'label'        => __( 'Add Your Questions', 'tourfic' ),
					'subtitle'        => __( 'Click the button below to add Frequently Asked Questions (FAQs) for your apartment. Feel free to add as many as needed. Additionally, you can duplicate or rearrange each FAQ using the icons on the right side.', 'tourfic' ),
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Single FAQ Title', 'tourfic' ),
						),
						array(
							'id'    => 'description',
							'type'  => 'editor',
							'label' => __( 'Single FAQ Description ', 'tourfic' ),
						),
					),
				),
				array(
					'id'    => 'terms_heading',
					'type'  => 'heading',
					'label' => __( 'Terms & Conditions', 'tourfic' ),
					'subtitle' => __( 'Include your set of regulations and guidelines that guests must agree to in order to use the service provided in your apartment.', 'tourfic' ),
				),
				array(
					'id'    => 'terms_title',
					'type'  => 'text',
					'label' => __( 'Title of the Section', 'tourfic' ),
					'subtitle' => __( 'This text will appear as the heading of the T&C section on the frontend.', 'tourfic' ),
					'default' => __( 'Terms & Conditions', 'tourfic' ),
				),
				array(
					'id'    => 'terms_and_conditions',
					'type'  => 'editor',
					'label' => __( 'Apartment Terms & Conditions', 'tourfic' ),
					'subtitle' => __( "Enter your apartment's terms and conditions in the text editor provided below.", 'tourfic' ),
				),
			),
		),

		//enquiry section
		'a_enquiry'  => array(
			'title'  => __( 'Apartment Enquiry', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'      => 'enquiry',
					'type'    => 'heading',
					'content' => __( 'Apartment Enquiry Form', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'        => 'enquiry-section',
					'type'      => 'switch',
					'label'     => __( 'Enable Apartment Enquiry Form Option', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => true
				),
				array(
					'id'       => 'apartment-enquiry-icon',
					'type'     => 'icon',
					'label'    => __( 'Apartment Enquiry icon', 'tourfic' ),
					'subtitle' => __( 'Choose icon', 'tourfic' ),
					'default'  => 'fa fa-question-circle-o',
					'dependency' => array( 'h-enquiry-section', '==', '1' ),
				),
				array(
					'id'    => 'enquiry-title',
					'type'  => 'text',
					'label' => __( 'Enquiry Title ', 'tourfic' ),
					'default' => __('Have a question in mind', 'tourfic'),
					'dependency' => array( 'enquiry-section', '==', '1' ),
				),
				array(
					'id'    => 'enquiry-content',
					'type'  => 'text',
					'label' => __( 'Enquiry Description ', 'tourfic' ),
					'default' => __('Looking for more info? Send a question to the property to find out more.', 'tourfic'),
					'dependency' => array( 'enquiry-section', '==', '1' ),
				),
				array(
					'id'    => 'enquiry-btn',
					'type'  => 'text',
					'label' => __( 'Enquiry Button Text ', 'tourfic' ),
					'default' => __('Contact Host', 'tourfic'),
					'dependency' => array( 'enquiry-section', '==', '1' ),
				),
			),
		),

		// Multiple tags for apartments
		'apartments_multiple_tags' => array(
			'title'  => __( 'Labels', 'tourfic' ),
			'icon'   => 'fa fa-list',
			'fields' => array(
				array(
					'id'      => 'tf-apartment-tags-heading',
					'type'    => 'heading',
					'label' => __( 'Apartment labels', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'           => 'tf-apartment-tags',
					'type'         => 'repeater',
					'label'        => __( 'Labels', 'tourfic' ),
					'subtitle' => __('Add some keywords that highlight your apartment\'s Unique Selling Point (USP). This label will be displayed on both the Archive Page and the Search Results Page.', 'tourfic'),
					'button_title' => __( 'Add / Insert New Label', 'tourfic' ),
					'fields'       => array(
						array(
							'id'    => 'apartment-tag-title',
							'type'  => 'text',
							'label' => __( 'Label Title', 'tourfic' ),
						),

						array(
							'id'       => 'apartment-tag-color-settings',
							'type'     => 'color',
							'class'    => 'tf-label-field',
							'label'    => __( 'Label Colors', 'tourfic' ),
							'subtitle' => __( 'Colors of Label Background and Font', 'tourfic' ),
							'multiple' => true,
							'inline'   => true,
							'colors'   => array(
								'background' => __( 'Background', 'tourfic' ),
								'font'   => __( 'Font', 'tourfic' ),
							),
							'default' => array(
								'background' => '#003162',
								'font' => '#fff'
							),
						),
					),
				),
			),
		),
		// Settings
		'settings'        => array(
			'title'  => __( 'Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-viruses',
			'fields' => array(
				array(
					'id'    => 'apartment--heading',
					'type'  => 'heading',
					'label' => 'Other Settings',
					'subtitle' => __( 'These are some additional settings specific to this Apartment. Note that some of these settings may override the global settings.', 'tourfic' ),
				),
				array(
					'id'      => 'apartment--docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/apartments-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
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
					'label' => __( 'Titles / Heading of Different Sections', 'tourfic' ),
				),
				array(
					'id'      => 'description_title',
					'type'    => 'text',
					'label'   => __( 'Description Section Title', 'tourfic' ),
					'default' => __( 'About this place', 'tourfic' ),
				),
				//review
				array(
					'id'    => 'review-section-title',
					'type'  => 'text',
					'label' => __( 'Reviews Section Title', 'tourfic' ),
					'default' => "Guest Reviews"
				),
				//Related Apartment
				array(
					'id'      => 'related_apartment_title',
					'type'    => 'text',
					'label'   => __( 'Related Apartment Section Title', 'tourfic' ),
					'default' => __( 'You may also like', 'tourfic' ),
				),
			),
		),
	),
) );
