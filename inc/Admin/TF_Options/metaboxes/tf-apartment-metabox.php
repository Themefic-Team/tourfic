<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

function tf_apt_amenities_cats() {
	$amenities_cats = ! empty( Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) : '';
	$all_cats       = [];
	if ( ! empty( $amenities_cats ) && is_array( $amenities_cats ) ) {
		foreach ( $amenities_cats as $key => $cat ) {
			$all_cats[ (string) $key ] = $cat['amenities_cat_name'];
		}
	}

	if ( empty( $all_cats ) ) {
		$all_cats[''] = esc_html__( 'Select Category', 'tourfic' );
	}

	return $all_cats;
}

TF_Metabox::metabox( 'tf_apartment_opt', array(
	'title'     => esc_html__( 'Apartment Settings', 'tourfic' ),
	'post_type' => 'tf_apartment',
	'sections'  => array(
		// General
		'general'         => array(
			'title'  => esc_html__( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'apartment-general-heading',
					'type'  => 'heading',
					'label' => 'General Settings ',
					'subtitle' => esc_html__( 'These are some common settings specific to this Apartment.', 'tourfic' ),
				),
				array(
					'id'      => 'apartment-general-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/general-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'apartment_gallery',
					'type'  => 'gallery',
					'label' => esc_html__( 'Apartment Gallery', 'tourfic' ),
					'subtitle'    => esc_html__( 'Add multiple images to craft a captivating gallery for your apartments, giving potential customers a visual tour.', 'tourfic' ),
				),
				array(
					'id'        => 'apartment_as_featured',
					'type'      => 'switch',
					'label'     => esc_html__( 'Featured Apartment', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'subtitle'    => esc_html__( 'Enable this option to feature this apartment at the top of search results.', 'tourfic' ),
				),
				array(
					'id'          => 'featured_text',
					'type'        => 'text',
					'label'       => esc_html__( 'Apartment Featured Text', 'tourfic' ),
					'subtitle'    => esc_html__( 'Enter Featured Apartment Text', 'tourfic' ),
					'placeholder' => esc_html__( 'Enter Featured Apartment Text', 'tourfic' ),
					'default'     => esc_html__( 'Hot Deal', 'tourfic' ),
					'dependency'  => array( 'apartment_as_featured', '==', true ),
				),
				array(
					'id'       => 'tf_single_apartment_layout_opt',
					'type'     => 'select',
					'label'    => esc_html__( 'Single Apartment Template Settings', 'tourfic' ),
					'subtitle' => esc_html__( 'You can keep the Global Template settings or choose a different layout for this apartment.', 'tourfic' ),
					'options'  => [
						'global' => esc_html__( 'Global Settings', 'tourfic' ),
						'single' => esc_html__( 'Single Settings', 'tourfic' ),
					],
					'default'  => 'global',
				),
				array(
					'id'       => 'tf_single_apartment_template',
					'type'     => 'imageselect',
					'label'    => esc_html__( 'Single Apartment Page Layout', 'tourfic' ),
					'multiple' 		=> true,
					'inline'   		=> true,
					'options'   	=> array(
						'default' 			=> array(
							'title'			=> 'Default',
							'url' 			=> TF_ASSETS_ADMIN_URL."images/template/default-apartment.jpg",
						),
						'design-1' => array(
							'title' => 'Design 1',
							'url'   => TF_ASSETS_ADMIN_URL . "images/template/design1-apartment.jpg",
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
			'title'  => esc_html__( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'    => 'apartment-location-heading',
					'type'  => 'heading',
					'label' => 'Location Settings',
					'subtitle' => esc_html__( 'The location of an apartment is a crucial element for every apartment. Set your apartment locations in this section.', 'tourfic' ),
				),
				array(
					'id'      => 'apartment-location-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/location-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'location_title',
					'type'     => 'text',
					'label'    => esc_html__( 'Title of this Section', 'tourfic' ),
					'subtitle' => esc_html__( 'This text will appear as the heading of the Location section on the frontend.', 'tourfic' ),
					'default'  => esc_html__( 'Where you’ll be', 'tourfic' ),
				),
				array(
					'id'       => 'map',
					'class'    => 'gmaps',
					'type'     => 'map',
					'label'    => esc_html__( 'Dynamic Location Search', 'tourfic' ),
					/* translators: %s is the link to the Google Maps API Key settings */
					'subtitle' => sprintf( wp_kses_post(__( 'Enter the specific address you wish to use for the apartment and select the correct option from the suggested addresses. This will be used to hyperlink address and display the address on the front-end map. <strong>Google Maps is also available for location. Simply set up your <a href="%s" target="_blank">Google Maps API Key</a></strong>', 'tourfic' )), esc_url( admin_url('admin.php?page=tf_settings#tab=map_settings') ) ),
					'height'   => '250px',
					'settings' => array(
						'scrollWheelZoom' => true,
					)
				),
				//Property Surroundings
				array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'We\'re offering an additional feature called <b>property surroundings</b> in our pro plan. <a href="https://tourfic.com/" target="_blank">Upgrade to our pro package today to take advantage of this fantastic option!</a>', 'tourfic' ) ),
				),
			),
		),
		// Booking
		'booking'         => array(
			'title'  => esc_html__( 'Booking Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-calendar-check',
			'fields' => array(
				array(
					'id'    => 'apartment-booking-heading',
					'type'  => 'heading',
					'label' => 'Booking Settings',
					'subtitle' => esc_html__( 'This section offer the options to customize the booking process for this apartment.', 'tourfic' ),
				),
				array(
					'id'      => 'apartment-booking-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/booking-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'booking_form_title',
					'type'  => 'text',
					'label' => esc_html__( 'Form Title', 'tourfic' ),
					'default' => esc_html__( 'Book your apartment', 'tourfic' ),
				),
				array(
					'id'         => 'pricing_type',
					'type'       => 'select',
					'label'      => esc_html__( 'Pricing Type', 'tourfic' ),
					'subtitle'   => esc_html__( 'Select pricing type', 'tourfic' ),
					'options'    => array(
						'per_night'  => esc_html__( 'Per Night', 'tourfic' ),
					),
					'attributes' => array(
						'class' => 'tf_apt_pricing_type',
					),
				),
				array(
					'id'          => 'price_per_night',
					'type'        => 'number',
					'label'       => esc_html__( 'Price Per Night', 'tourfic' ),
					'subtitle'    => esc_html__( 'The total booking cost is calculated by multiplying the nightly rate by the number of nights stayed, from check-in to check-out.', 'tourfic' ),
					'field_width' => 50,
					'attributes'  => array( 'min' => 0 ),
					'dependency' => array( 'pricing_type', '==', 'per_night' ),
				),
				array(
					'id'          => 'min_stay',
					'type'        => 'number',
					'label'       => esc_html__( 'Minimum Night Stay', 'tourfic' ),
					'subtitle'    => esc_html__( 'Specify the minimum number of nights required to book this room.', 'tourfic' ),
					'attributes'  => array( 'min' => 1 )
				),
				array(
					'id'          => 'max_adults',
					'type'        => 'number',
					'label'       => esc_html__( 'Maximum Adults', 'tourfic' ),
					'subtitle'    => esc_html__( 'Max number of adults allowed in the apartment.', 'tourfic' ),
					'field_width' => 33.33,
					'attributes'  => array( 'min' => 1 )
				),
				array(
					'id'          => 'max_children',
					'type'        => 'number',
					'label'       => esc_html__( 'Maximum Children', 'tourfic' ),
					'subtitle'    => esc_html__( 'Max number of child allowed in the apartment.', 'tourfic' ),
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
					),
					'default' => '1',
				),
				array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'We\'re offering some extra features like <b>per person pricing</b>, <b>external booking</b>, <b>taxable apartment</b>, <b>tax class for Woocommerce</b> in our pro plan, also you can add unlimited additional fees for apartment. <a href="https://tourfic.com/" target="_blank">Upgrade to our pro package today to take advantage of these fantastic option!</a>', 'tourfic')),
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
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'Are you looking to enhance the booking system for your apartment? Our pro package offers a powerful feature that includes <strong>custom availability settings</strong> and <strong>iCal sync</strong>. This integration will streamline your operations and improve the booking experience, giving a significant boost to your apartment business. <a href="https://tourfic.com/" target="_blank">Upgrade to our pro package today to take advantage of these fantastic option!</a>', 'tourfic' ) ),
				),

			),
		),
		//Room Management
		'room_management' => array(
			'title'  => esc_html__( 'Room Management', 'tourfic' ),
			'icon'   => 'fa-solid fa-bed',
			'fields' => array(
				array(
					'id'    => 'apartment-room-heading',
					'type'  => 'heading',
					'label' => 'Create & Manage Your Apartment Rooms',
					'subtitle' => esc_html__( 'In this section, you are provided with the tools to create and manage your apartment room offerings.', 'tourfic' ),
				),
				array(
					'id'      => 'apartment-room-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/room-management/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'room_details_title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the Section', 'tourfic' ),
					'subtitle' => esc_html__( 'This text will appear as the heading of the Rooms section on the frontend.', 'tourfic' ),
					'default' => esc_html__( 'Where you\'ll sleep', 'tourfic' ),
				),
				array(
					'id'           => 'rooms',
					'type'         => 'repeater',
					'button_title' => esc_html__( 'Add New', 'tourfic' ),
					'label'        => esc_html__( 'Create your apartment rooms ', 'tourfic' ),
					'field_title'  => 'title',
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => esc_html__( 'Room Title', 'tourfic' ),
						),
						array(
							'id'    => 'subtitle',
							'type'  => 'text',
							'label' => esc_html__( 'Room Subtitle', 'tourfic' ),
						),
						array(
							'id'    => 'description',
							'type'  => 'editor',
							'label' => esc_html__( 'Room Description', 'tourfic' ),
						),
						array(
							'id'    => 'thumbnail',
							'type'  => 'image',
							'label' => esc_html__( 'Room Image Thumbnail ', 'tourfic' ),
						),
						array(
							'id'    => 'gallery',
							'type'  => 'gallery',
							'label' => esc_html__( 'Room Gallery', 'tourfic' ),
							'subtitle' => esc_html__( 'Upload all the images specific to this room.', 'tourfic' ),
						),
						array(
							'id'    => 'room_type',
							'type'  => 'select',
							'label' => esc_html__( 'Room Type', 'tourfic' ),
							'subtitle' => esc_html__( 'Select the type of room you are offering.', 'tourfic' ),
							'options' => array(
								'bedroom'     => esc_html__( 'Bedroom', 'tourfic' ),
								'common_room' => esc_html__( 'Common Room', 'tourfic' ),
								'kitchen'     => esc_html__( 'Kitchen', 'tourfic' ),
								'bathroom'    => esc_html__( 'Bathroom', 'tourfic' ),
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'footage',
							'type'        => 'text',
							'label'       => esc_html__( 'Room Footage', 'tourfic' ),
							'subtitle'    => esc_html__( 'Room footage (in sft).', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'bed',
							'type'        => 'number',
							'label'       => esc_html__( 'Number of Beds', 'tourfic' ),
							'subtitle'    => esc_html__( 'Number of beds available in the room.', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'adult',
							'type'        => 'number',
							'label'       => esc_html__( 'Number of Adults', 'tourfic' ),
							'subtitle'    => esc_html__( 'Max number of adults allowed in the room.', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'child',
							'type'        => 'number',
							'label'       => esc_html__( 'Number of Children', 'tourfic' ),
							'subtitle'    => esc_html__( 'Max number of children allowed in the room.', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'infant',
							'type'        => 'number',
							'label'       => esc_html__( 'Number of Infants', 'tourfic' ),
							'subtitle'    => esc_html__( 'Max number of infants allowed in the room.', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
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
					'field_title'  => 'title',
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
							'description' => __( 'Add new features from ', 'tourfic' ) . '<a target="_blank" href="' . esc_url( admin_url('edit-tags.php?taxonomy=apartment_feature&post_type=tf_apartment') ) .'">' . __("Apartment Features", 'tourfic') . '</a>',
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
							'description' => __( 'Add new category from ', 'tourfic' ) . '<a target="_blank" href="' . esc_url( admin_url('admin.php?page=tf_settings#tab=apartment_single_page') ) .'">' . __("Amenities Categories", 'tourfic') . '</a>',
							'field_width' => 50,
						),
						array(
							'id'        => 'favorite',
							'type'      => 'switch',
							'label'     => __( 'Mark as favourite', 'tourfic' ),
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
					'field_title'  => 'title',
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
					'field_title'  => 'title',
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
					'dependency' => array( 'enquiry-section', '==', '1' ),
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
			'title'  => __( 'Promotional Tags', 'tourfic' ),
			'icon'   => 'fa fa-list',
			'fields' => array(
				array(
					'id'      => 'tf-apartment-tags-heading',
					'type'    => 'heading',
					'label' => __( 'Apartment tags', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'           => 'tf-apartment-tags',
					'type'         => 'repeater',
					'label'        => __( 'Promotional Tags', 'tourfic' ),
					'subtitle' => __('Add some keywords that highlight your apartment\'s Unique Selling Point (USP). This tag will be displayed on both the Archive Page and the Search Results Page.', 'tourfic'),
					'button_title' => __( 'Add / Insert New Tag', 'tourfic' ),
					'field_title'  => 'apartment-tag-title',
					'fields'       => array(
						array(
							'id'    => 'apartment-tag-title',
							'type'  => 'text',
							'label' => __( 'Tag Title', 'tourfic' ),
						),

						array(
							'id'       => 'apartment-tag-color-settings',
							'type'     => 'color',
							'class'    => 'tf-label-field',
							'label'    => __( 'Tag Colors', 'tourfic' ),
							'subtitle' => __( 'Colors of Tag Background and Font', 'tourfic' ),
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
