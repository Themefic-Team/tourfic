<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

TF_Metabox::metabox( 'tf_carrental_opt', array(
	'title'     => esc_html__( 'Cars Settings', 'tourfic' ),
	'post_type' => 'tf_carrental',
	'sections'  => array(

		// location
		'location'        => array(
			'title'  => esc_html__( 'General Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-cog',
			'fields' => array(
				array(
					'id'    => 'car-General-heading',
					'type'  => 'heading',
					'label' => 'General Information',
				),
				array(
					'id'    => 'car_gallery',
					'type'  => 'gallery',
					'label' => esc_html__( 'Car Gallery', 'tourfic' ),
					'subtitle' => esc_html__( 'Add multiple images to craft a captivating gallery for your car, giving potential customers a visual car.', 'tourfic' ),
				),
                array(
					'id'       => 'tf_single_car_layout_opt',
					'type'     => 'select',
					'label'    => esc_html__( 'Single Car Template Settings', 'tourfic' ),
					'subtitle' => esc_html__( 'You can keep the Global Template settings or choose a different layout for this car.', 'tourfic' ),
					'options'  => [
						'global' => esc_html__( 'Global Settings', 'tourfic' ),
						'single' => esc_html__( 'Single Settings', 'tourfic' ),
					],
					'default'  => 'global',
				),
                array(
					'id'       => 'tf_single_car_template',
					'type'     => 'imageselect',
					'label'    => esc_html__( 'Single Car Page Layout', 'tourfic' ),
					'multiple' 		=> true,
					'inline'   		=> true,
					'options'   	=> array( 
						'design-1' 				=> array(
							'title'			=> 'Design 1',
							'url' 			=> TF_ASSETS_ADMIN_URL."images/template/design1-tour.jpg",
						)
					),
					'default'   	=> 'design-1',
					'dependency'  => [
						array( 'tf_single_car_layout_opt', '==', 'single' )
					],
				),
				array(
					'id'    => 'car-location-heading',
					'type'  => 'heading',
					'label' => 'Location Settings',
					'subtitle' => esc_html__( 'The location of an car is a crucial element for every car. Set your car locations in this section.', 'tourfic' ),
				),
				array(
					'id'      => 'car-location-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/location-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'location_title',
					'type'     => 'text',
					'label'    => esc_html__( 'Title of this Section', 'tourfic' ),
					'subtitle' => esc_html__( 'This text will appear as the heading of the Location section on the frontend.', 'tourfic' ),
					'default'  => esc_html__( 'Location', 'tourfic' ),
				),
				array(
					'id'       => 'map',
					'class'    => 'gmaps',
					'type'     => 'map',
					'label'    => esc_html__( 'Dynamic Location Search', 'tourfic' ),
					/* translators: %s is the link to the Google Maps API Key settings */
					'subtitle' => sprintf( wp_kses_post(__( 'Enter the specific address you wish to use for the car and select the correct option from the suggested addresses. This will be used to hyperlink address and display the address on the front-end map. <strong>Google Maps is also available for location. Simply set up your <a href="%s" target="_blank">Google Maps API Key</a></strong>', 'tourfic' )), esc_url( admin_url('admin.php?page=tf_settings#tab=map_settings') ) ),
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

		// Car Details
		'car_details'         => array(
			'title'  => esc_html__( 'Car Details', 'tourfic' ),
			'icon'   => 'fa-solid fa-calendar-check',
			'fields' => array(
				array(
					'id'    => 'car-car-details-heading',
					'type'  => 'heading',
					'label' => 'Car Details',
					'subtitle' => esc_html__( 'This section offer the options to customize the booking process for this car.', 'tourfic' ),
				),
				array(
					'id'      => 'car-booking-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/booking-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
                array(
					'id'       => 'car_as_featured',
					'type'     => 'switch',
					'label'    => esc_html__( 'Set car as featured', 'tourfic' ),
					'subtitle' => esc_html__( 'This car will be featured at the top of both the search results and the car archive page.', 'tourfic' ),
				),
                
				array(
					'id'          => 'passengers',
					'type'        => 'number',
					'label'       => esc_html__( 'No. Passengers', 'tourfic' ),
					'subtitle'    => esc_html__( 'No. Passengers', 'tourfic' ),
					'attributes'  => array( 'min' => 1 ),
					'is_search_able' => true
				),
				array(
					'id'          => 'baggage',
					'type'        => 'number',
					'label'       => esc_html__( 'Baggage', 'tourfic' ),
					'subtitle'    => esc_html__( 'Baggage', 'tourfic' ),
					'attributes'  => array( 'min' => 0 )
				),
				array(
					'id'       => 'auto_transmission',
					'type'     => 'switch',
					'label'    => esc_html__( 'Auto Transmission', 'tourfic' ),
					'subtitle' => esc_html__( 'Auto Transmission', 'tourfic' ),
				),
				array(
					'id'       => 'unlimited_mileage',
					'type'     => 'switch',
					'label'    => esc_html__( 'Unlimited Mileage', 'tourfic' ),
					'subtitle' => esc_html__( 'Unlimited Mileage', 'tourfic' ),
				),
				array(
					'id'       => 'mileage_type',
					'type'     => 'select',
					'label'    => __( 'Unit Type', 'tourfic' ),
					'options'  => array(
						'km' => __( 'Kilometer', 'tourfic' ),
						'miles'   => __( 'Miles', 'tourfic' ),
					),
					'default'  => 'km',
					'dependency'  => [
						array( 'unlimited_mileage', '==', 'false' )
					],
					'field_width' => 50
				),
				array(
					'id'    => 'mileage',
					'type'  => 'text',
					'label' => __( 'Mileage', 'tourfic' ),
					'dependency'  => [
						array( 'unlimited_mileage', '==', 'false' )
					],
					'field_width' => 50
				),
				array(
					'id'         => 'brands',
					'type'       => 'select2',
					'multiple'   => true,
					'label'      => __( 'Select Brands', 'tourfic' ),
					'options'    => 'terms',
					'query_args' => array(
						'taxonomy'   => 'carrental_brand',
						'hide_empty' => false,
					),
					'default'    => 'none',
					'inline_add_new' => true,
					'inline_delete' => true
				),
                array(
					'id'       => 'pay_pickup',
					'type'     => 'switch',
					'label'    => esc_html__( 'Pay at Pick-up', 'tourfic' ),
					'subtitle' => esc_html__( 'Pay at Pick-up', 'tourfic' ),
				),
				array(
					'id'       => 'fuel_included',
					'type'     => 'switch',
					'label'    => esc_html__( 'Fuel Included', 'tourfic' ),
					'subtitle' => esc_html__( 'Fuel Included', 'tourfic' ),
				),
				array(
					'id'       => 'shuttle_car',
					'type'     => 'switch',
					'label'    => esc_html__( 'Shuttle to Car', 'tourfic' ),
					'subtitle' => esc_html__( 'Shuttle to Car', 'tourfic' )
				),
				array(
					'id'       => 'shuttle_car_fee_type',
					'type'     => 'select',
					'label'    => __( 'Price Type', 'tourfic' ),
					'options'  => array(
						'free' => __( 'Free', 'tourfic' ),
						'paid'   => __( 'Paid', 'tourfic' ),
					),
					'default'  => 'free',
					'dependency'  => [
						array( 'shuttle_car', '==', 'true' )
					],
					'field_width' => 50
				),
				array(
					'id'    => 'shuttle_car_fee',
					'type'  => 'text',
					'label' => __( 'Price', 'tourfic' ),
					'dependency'  => [
						array( 'shuttle_car_fee_type', '==', 'paid' )
					],
					'field_width' => 50
				),
				array(
					'id'       => 'driver_included',
					'type'     => 'switch',
					'label'    => esc_html__( 'Driver included', 'tourfic' ),
					'subtitle' => esc_html__( 'Driver included', 'tourfic' ),
				),
                array(
					'id'    => 'car-driverinfo-heading',
					'type'  => 'heading',
					'label' => 'Driver Details Section',
					'subtitle' => __( 'How can potential or existing customers reach out for more details about your car? Please share your Driver Details here.', 'tourfic' ),
					'dependency'  => [
						array( 'driver_included', '==', 'true' )
					],
				),
				array(
					'id'       => 'car_driverinfo_section',
					'type'     => 'switch',
					'label'    => esc_html__( 'Do you want to show driver information in the frontend?', 'tourfic' ),
					'subtitle' => esc_html__( 'Do you want to show driver information in the frontend?', 'tourfic' ),
					'dependency'  => [
						array( 'driver_included', '==', 'true' )
					],
				),
				array(
					'id'          => 'driver_name',
					'type'        => 'text',
					'label'       => __( 'Name', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
					'dependency'  => [
						array( 'driver_included', '==', 'true' )
					],
				),
				array(
					'id'          => 'driver_email',
					'type'        => 'text',
					'label'       => __( 'Email address', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
					'dependency'  => [
						array( 'driver_included', '==', 'true' )
					],
				),
				array(
					'id'          => 'driver_phone',
					'type'        => 'text',
					'label'       => __( 'Phone Number', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
					'dependency'  => [
						array( 'driver_included', '==', 'true' )
					],
				),
				array(
					'id'          => 'driver_age',
					'type'        => 'number',
					'label'       => __( 'Age', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
					'dependency'  => [
						array( 'driver_included', '==', 'true' )
					],
				),
				array(
					'id'          => 'driver_address',
					'type'        => 'text',
					'label'       => __( 'Address', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'dependency'  => [
						array( 'driver_included', '==', 'true' )
					],
				),
				array(
					'id'      => 'driver_image',
					'type'    => 'image',
					'label'   => __( 'Driver Photo', 'tourfic' ),
					'subtitle'    => __( 'Please upload the driver photo to be displayed in the Contact Section.', 'tourfic' ),
					'library' => 'image',
					'dependency'  => [
						array( 'driver_included', '==', 'true' )
					],
				),
				array(
					'id'    => 'car-custom-info-heading',
					'type'  => 'heading',
					'label' => 'Add Custom Informations',
					'subtitle' => esc_html__( 'This section offer the options to customize the booking process for this car.', 'tourfic' ),
				),
				array(
					'id'           => 'car_custom_info',
					'type'         => 'repeater',
					'button_title' => __( 'Add New Custom Info', 'tourfic' ),
					'label'        => __( 'Add Your Custom Infos', 'tourfic' ),
					'subtitle'        => __( 'Click the button below to add Info for your Car. Feel free to add as many as needed. Additionally, you can duplicate or rearrange each Badge using the icons on the right side.', 'tourfic' ),
					'field_title'  => 'title',
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
						),
                        array(
                            'id'       => 'info_icon',
                            'type'     => 'icon',
                            'label'    => __( 'Icon', 'tourfic' ),
                            'subtitle' => __( 'Choose icon', 'tourfic' ),
                        )
					),
				)
			),
		),
		
		// Additional Info
		'additional_info' => array(
			'title'  => __( 'Additional Info', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'    => 'car-add-info-heading',
					'type'  => 'heading',
					'label' => 'Benefits',
					'subtitle' => __( 'This section is designed to help users find answers to common questions.', 'tourfic' ),
				),
				array(
					'id'      => 'car-add-info-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/faq-terms/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'benefits_section',
					'type'     => 'switch',
					'label'    => esc_html__( 'Do you want to show Benefits in the frontend?', 'tourfic' ),
					'subtitle' => esc_html__( 'Do you want to show Benefits in the frontend?', 'tourfic' )
				),
				array(
					'id'           => 'benefits',
					'type'         => 'repeater',
					'button_title' => __( 'Add New Benefits', 'tourfic' ),
					'label'        => __( 'Add Your Benefits', 'tourfic' ),
					'subtitle'        => __( 'Click the button below to add Benefits for your Car. Feel free to add as many as needed. Additionally, you can duplicate or rearrange each Benefits using the icons on the right side.', 'tourfic' ),
					'field_title'  => 'title',
					'dependency'  => [
						array( 'benefits_section', '==', 'true' )
					],
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
						),
                        array(
                            'id'       => 'icon',
                            'type'     => 'icon',
                            'label'    => __( 'Icon', 'tourfic' ),
                            'subtitle' => __( 'Choose icon', 'tourfic' ),
                        )
					),
				),
				array(
					'id'    => 'car-inc-heading',
					'type'  => 'heading',
					'label' => 'Include & Exclude Section',
					'subtitle' => __( 'Each car includes certain items, while others are not part of the package. Clearly define these inclusions and exclusions to prevent any misunderstandings during your car.', 'tourfic' ),
				),
				array(
					'id'      => 'car-inc-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-include-exclude/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'inc_exc_section',
					'type'     => 'switch',
					'label'    => esc_html__( 'Do you want to show Include and Exclude in the frontend?', 'tourfic' ),
					'subtitle' => esc_html__( 'Do you want to show Include and Exclude in the frontend?', 'tourfic' )
				),
				array(
					'id'           => 'inc',
					'type'         => 'repeater',
					'label'        => __( 'Items Included', 'tourfic' ),
					'subtitle'     => __( 'Add all the items/features included in this car package.', 'tourfic' ),
					'button_title' => __( 'Add New Include', 'tourfic' ),
					'field_title'  => 'title',
					'dependency'  => [
						array( 'inc_exc_section', '==', 'true' )
					],
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Insert your item', 'tourfic' ),
						),
					),
				),
				array(
					'id'       => 'inc_icon',
					'type'     => 'icon',
					'label'    => __( 'Icon for Included Item', 'tourfic' ),
					'subtitle' => __( 'Choose icon', 'tourfic' ),
					'dependency'  => [
						array( 'inc_exc_section', '==', 'true' )
					],
				),
				array(
					'id'           => 'exc',
					'type'         => 'repeater',
					'label'        => __( 'Items Excluded', 'tourfic' ),
					'subtitle'        => __( 'List all the items/features excluded in this car package.', 'tourfic' ),
					'button_title' => __( 'Add New Exclude', 'tourfic' ),
					'field_title'  => 'title',
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Insert your item', 'tourfic' ),
						),
					),
					'dependency'  => [
						array( 'inc_exc_section', '==', 'true' )
					],
				),
				array(
					'id'       => 'exc_icon',
					'type'     => 'icon',
					'label'    => __( 'Icon for Excluded item', 'tourfic' ),
					'subtitle' => __( 'Choose icon', 'tourfic' ),
					'dependency'  => [
						array( 'inc_exc_section', '==', 'true' )
					],
				),
			),
		),

        // Badges
		'badges' => array(
			'title'  => __( 'Badges', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'    => 'car-faq-heading',
					'type'  => 'heading',
					'label' => 'Badge Section',
					'subtitle' => __( 'This section is designed to help users find answers to common questions.', 'tourfic' ),
				),
				array(
					'id'      => 'car-faq-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/faq-terms/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'           => 'badge',
					'type'         => 'repeater',
					'button_title' => __( 'Add New Badge', 'tourfic' ),
					'label'        => __( 'Add Your Badges', 'tourfic' ),
					'subtitle'        => __( 'Click the button below to add Badges for your Car. Feel free to add as many as needed. Additionally, you can duplicate or rearrange each Badge using the icons on the right side.', 'tourfic' ),
					'field_title'  => 'title',
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
						),
						array(
							'id'    => 'description',
							'type'  => 'editor',
							'label' => __( 'Description ', 'tourfic' ),
						),
                        array(
                            'id'       => 'badge_icon',
                            'type'     => 'icon',
                            'label'    => __( 'Icon', 'tourfic' ),
                            'subtitle' => __( 'Choose icon', 'tourfic' ),
                        )
					),
				)
			),
		),

        // Contact Information
		'contact_info'         => array(
			'title'  => __( 'Contact Information', 'tourfic' ),
			'icon'   => 'fa-solid fa-address-book',
			'fields' => array(
				array(
					'id'    => 'car-continfo-heading',
					'type'  => 'heading',
					'label' => 'Contact Info Section',
					'subtitle' => __( 'How can potential or existing customers reach out for more details about your car? Please share your contact information here.', 'tourfic' ),
				),
				array(
					'id'      => 'car-continfo-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-contact-info/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'information_section',
					'type'     => 'switch',
					'label'    => esc_html__( 'Do you want to show Contact information in the frontend?', 'tourfic' ),
					'subtitle' => esc_html__( 'Do you want to show Contact information in the frontend?', 'tourfic' )
				),
				array(
					'id'          => 'owner_name',
					'type'        => 'text',
					'label'       => __( 'Name', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
					'dependency'  => [
						array( 'information_section', '==', 'true' )
					],
				),
				array(
					'id'          => 'email',
					'type'        => 'text',
					'label'       => __( 'Email address', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
					'dependency'  => [
						array( 'information_section', '==', 'true' )
					],
				),
				array(
					'id'          => 'phone',
					'type'        => 'text',
					'label'       => __( 'Phone Number', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
					'dependency'  => [
						array( 'information_section', '==', 'true' )
					],
				),
				array(
					'id'          => 'website',
					'type'        => 'text',
					'label'       => __( 'Website Url', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
					'dependency'  => [
						array( 'information_section', '==', 'true' )
					],
				),
				array(
					'id'          => 'fax',
					'type'        => 'text',
					'label'       => __( 'Fax Number', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
					'dependency'  => [
						array( 'information_section', '==', 'true' )
					],
				),
				array(
					'id'      => 'owner_image',
					'type'    => 'image',
					'label'   => __( 'Owner Photo', 'tourfic' ),
					'subtitle'    => __( 'Please upload the Owner photo to be displayed in the Contact Section.', 'tourfic' ),
					'library' => 'image',
					'dependency'  => [
						array( 'information_section', '==', 'true' )
					],
				),
			),
		),

        // Price
		'price'                => array(
			'title'  => __( 'Price Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-money-check',
			'fields' => array(
				array(
					'id'    => 'car-pricing-heading',
					'type'  => 'heading',
					'label' => 'Car Pricing Settings',
					'subtitle' => __( 'The pricing of a car package plays a crucial role. Make sure you set it correctly.', 'tourfic' ),
				),
				array(
					'id'      => 'car-pricing-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tourfic-price-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'price_by',
					'type'     => 'select',
					'label'    => __( 'Type of Pricing', 'tourfic' ),
					'options'  => array(
						'day' => __( 'Day', 'tourfic' ),
						'hour'   => __( 'Hour', 'tourfic' ),
					),
					'default'  => 'day',
				),
				array(
					'id'          => 'car_rent',
					'type'        => 'number',
					'label'       => __( 'Pricing for Car Rent', 'tourfic' ),
					'subtitle'    => __( 'Price', 'tourfic' ),
					'attributes'  => array(
						'min' => '0',
					),
					'is_search_able' => true
				),
				array(
					'id'       => 'pricing_type',
					'type'     => 'select',
					'label'    => __( 'Custom price for Car Rent', 'tourfic' ),
					'options'  => array(
						'day_hour' => __( 'Day/Hour', 'tourfic' ),
						'date'   => __( 'Price By Date', 'tourfic' ),
					),
					'default'  => 'day_hour',
				),
				array(
					'id'           => 'day_prices',
					'type'         => 'repeater',
					'button_title' => __( 'Add New Price', 'tourfic' ),
					'label'        => __( 'Price by Number of Day/Hour', 'tourfic' ),
					'field_title'  => 'title',
					'dependency' => array(
						array( 'pricing_type', '==', 'day_hour' ),
					),
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
						),
						array(
							'id'       => 'type',
							'type'     => 'select',
							'label'    => __( 'Type', 'tourfic' ),
							'options'  => array(
								'day' => __( 'Day', 'tourfic' ),
								'hour'   => __( 'Hour', 'tourfic' ),
							),
							'default'  => 'day',
						),
						array(
							'id'    => 'from_day',
							'type'  => 'number',
							'label' => __( 'From', 'tourfic' ),
						),
						array(
							'id'    => 'to_day',
							'type'  => 'number',
							'label' => __( 'To', 'tourfic' ),
						),
						array(
							'id'    => 'price',
							'type'  => 'number',
							'label' => __( 'Price', 'tourfic' ),
						)
					),
				),
				array(
					'id'           => 'date_prices',
					'type'         => 'repeater',
					'button_title' => __( 'Add New Price', 'tourfic' ),
					'label'        => __( 'Price by date', 'tourfic' ),
					'dependency' => array(
						array( 'pricing_type', '==', 'date' ),
					),
					'fields'       => array(
						array(
							'id'         => 'date',
							'type'       => 'date',
							'format'     => 'Y/m/d',
							'range'      => true,
							'label_from' => 'Start Date',
							'label_to'   => 'End Date',
							'multiple'   => true,
							'attributes' => array(
								'autocomplete' => 'off',
							),
						),
						array(
							'id'    => 'price',
							'type'  => 'number',
							'label' => __( 'Price', 'tourfic' ),
						)
					),
				),
				array(
					'id'       => 'discount_type',
					'type'     => 'select',
					'label'    => __( 'Discount Type', 'tourfic' ),
					'subtitle' => __( 'Set a discount for this tour to incentivize bookings. Choose between a fixed amount off or a percentage-based reduction.', 'tourfic' ),
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
					'id'      => 'car_availability',
					'type'    => 'heading',
					'content' => __( 'Availability', 'tourfic' ),
				),
				array(
					'id'          => 'car_numbers',
					'type'        => 'number',
					'label'       => __( 'Number of car for rent', 'tourfic' ),
					'subtitle'    => __( 'Number of car for rent', 'tourfic' ),
					'attributes'  => array(
						'min' => '0',
					),
				),
				array(
					'id'      => 'price_deposit',
					'type'    => 'heading',
					'content' => __( 'Deposit', 'tourfic' ),
				),

				array(
					'id'      => 'allow_deposit',
					'type'    => 'switch',
					'label'   => esc_html__( 'Enable Deposit', 'tourfic' ),
					'default' => false,
				),
				array(
					'id'          => 'deposit_type',
					'type'        => 'select',
					'label'       => esc_html__( 'Deposit Type', 'tourfic' ),
					'subtitle'    => esc_html__( 'Select deposit type: Percentage or Fixed', 'tourfic' ),
					'options'     => array(
						'none'    => esc_html__( 'None', 'tourfic' ),
						'percent' => esc_html__( 'Percent', 'tourfic' ),
						'fixed'   => esc_html__( 'Fixed', 'tourfic' ),
					),
					'default'     => 'none',
					'dependency'  => array( 'allow_deposit', '!=', 'false' ),
					'field_width' => 50
				),
				array(
					'id'          => 'deposit_amount',
					'type'        => 'number',
					'label'       => esc_html__( 'Deposit Price', 'tourfic' ),
					'subtitle'    => esc_html__( 'Insert amount only', 'tourfic' ),
					'attributes'  => array(
						'min' => '0',
					),
					'dependency'  => array(
						array( 'deposit_type', '!=', 'none' ),
						array( 'allow_deposit', '!=', 'false' ),
					),
					'field_width' => 50
				),
			),
		),

		// Car Extra
		'car_extra'         => array(
			'title'  => __( 'Car Extra', 'tourfic' ),
			'icon'   => 'fa-solid fa-route',
			'fields' => array(
				array(
					'id'    => 'car-extra-heading',
					'type'  => 'heading',
					'label' => 'Car Extra Section',
					'subtitle' => __( 'How can potential or existing customers reach out for more details about your car? Please share your contact information here.', 'tourfic' ),
				),
				array(
					'id'      => 'car-extra-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-contact-info/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'           => 'extras',
					'type'         => 'repeater',
					'button_title' => __( 'Add New Extra', 'tourfic' ),
					'label'        => __( 'Add Your Extras', 'tourfic' ),
					'subtitle'        => __( 'Click the button below to add Extras for your Car. Feel free to add as many as needed. Additionally, you can duplicate or rearrange each Badge using the icons on the right side.', 'tourfic' ),
					'field_title'  => 'title',
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
						),
						array(
							'id'    => 'content',
							'type'  => 'textarea',
							'label' => __( 'Content', 'tourfic' ),
						),
						array(
							'id'    => 'max_number',
							'type'  => 'number',
							'label' => __( 'Max of number', 'tourfic' ),
						),
						array(
							'id'    => 'price',
							'type'  => 'number',
							'label' => __( 'Price', 'tourfic' ),
						),
                        array(
							'id'       => 'price_type',
							'type'     => 'select',
							'label'    => __( 'Price type', 'tourfic' ),
							'options'  => array(
								'time' => __( 'By Time', 'tourfic' ),
								'fixed'   => __( 'Fixed', 'tourfic' ),
							)
						),
					),
				)
			),
		),

        // // Booking
		'booking'              => array(
			'title'  => esc_html__( 'Booking', 'tourfic' ),
			'icon'   => 'fa-solid fa-person-walking-luggage',
			'fields' => array(
				array(
					'id'    => 'car-protection-heading',
					'type'  => 'heading',
					'label' => 'Booking Protection Section',
					'subtitle' => __( 'Each car includes certain items, while others are not part of the package. Clearly define these inclusions and exclusions to prevent any misunderstandings during your car.', 'tourfic' ),
				),
				array(
					'id'      => 'car-protection-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-include-exclude/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'protection_section',
					'type'     => 'switch',
					'label'    => esc_html__( 'Do you want to show Booking Protection in the frontend?', 'tourfic' ),
					'subtitle' => esc_html__( 'Do you want to show Booking Protection in the frontend?', 'tourfic' )
				),
				array(
					'id'    => 'protection_content',
					'type'  => 'editor',
					'label' => __( 'Protection Content', 'tourfic' ),
					'dependency'  => [
						array( 'protection_section', '==', 'true' )
					],
				),
				array(
					'id'           => 'protections',
					'type'         => 'repeater',
					'label'        => __( 'Items Protection', 'tourfic' ),
					'subtitle'        => __( 'List all the items/features Protection in this car package.', 'tourfic' ),
					'button_title' => __( 'Add New Protection', 'tourfic' ),
					'field_title'  => 'title',
					'dependency'  => [
						array( 'protection_section', '==', 'true' )
					],
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Insert your Title', 'tourfic' ),
						),
						array(
							'id'    => 'content',
							'type'  => 'textarea',
							'label' => __( 'Insert your Content', 'tourfic' ),
						),
						array(
							'id'       => 'include',
							'type'     => 'switch',
							'label'    => esc_html__( 'Include?', 'tourfic' ),
						),
						array(
							'id'    => 'price',
							'type'  => 'number',
							'label' => __( 'Insert your Price', 'tourfic' ),
							'dependency'  => [
								array( 'include', '==', 'true' )
							],
						),
					),
				),
				array(
					'id'    => 'tour-booking-heading',
					'type'  => 'heading',
					'label' => 'Booking Settings',
					'subtitle' => esc_html__( 'This section offer the options to customize the booking process for your tours.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-booking-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/booking/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				
				array(
					'id'      => 'booking-by',
					'type'    => 'select',
					'label'   => esc_html__( 'Booking Type', 'tourfic' ),
					'subtitle'  => esc_html__( 'Choose the type of booking you would like to implement for this tour.', 'tourfic' ),
					'options' => array(
						'1' => esc_html__( 'Default Booking (WooCommerce)', 'tourfic' ),
						'2' => esc_html__( 'External Booking', 'tourfic' ),
						'3' => esc_html__( 'Booking Without Payment', 'tourfic' ),
					),
					'default' => '1'
				),
				array(
					'id'          => 'booking-url',
					'type'        => 'text',
					'label'       => esc_html__( 'External Booking URL', 'tourfic' ),
					'dependency'  => array( 'booking-by', '==', '2' ),
					'placeholder' => esc_html__( 'https://website.com', 'tourfic' )
				),
				// array(
				// 	'id'        => 'hide_booking_form',
				// 	'type'      => 'switch',
				// 	'label'     => esc_html__( 'Hide Booking Form', 'tourfic' ),
				// 	'subtitle' => esc_html__( 'Enable this option to hide the booking form from the single tour page.', 'tourfic' ),
				// 	'label_on'  => esc_html__( 'Yes', 'tourfic' ),
				// 	'label_off' => esc_html__( 'No', 'tourfic' ),
				// 	'default'   => false,
				// 	'dependency' => array( 'booking-by', '==', '2' ),
				// ),
				// array(
				// 	'id'        => 'hide_price',
				// 	'type'      => 'switch',
				// 	'label'     => esc_html__( 'Hide Price', 'tourfic' ),
				// 	'subtitle' => esc_html__( 'Enable this option to hide the price from the single tour page.', 'tourfic' ),
				// 	'label_on'  => esc_html__( 'Yes', 'tourfic' ),
				// 	'label_off' => esc_html__( 'No', 'tourfic' ),
				// 	'default'   => false,
				// 	'dependency' => array( 'booking-by', '==', '2' ),
				// ),
				array(
					'id'        => 'booking-attribute',
					'type'      => 'switch',
					'label'     => esc_html__( 'Allow Attribute', 'tourfic' ),
					'subtitle'  => esc_html__( 'If attribute allow, You can able to add custom Attribute', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'dependency'  => array( 'booking-by', '==', '2' ),
				),
				array(
					'id'          => 'booking-query',
					'type'        => 'textarea',
					'label'       => esc_html__( 'Query Attribute', 'tourfic' ),
					'dependency'  => array(
						array( 'booking-by', '==', '2' ),
						array( 'booking-attribute', '==', '1' )
					),
					'default'     => 'pickup={pickup}&dropoff={dropoff}&pickup_date={pickup_date}&dropoff_date={dropoff_date}',
					'placeholder' => esc_html__( 'pickup={pickup}&dropoff={dropoff}&pickup_date={pickup_date}&dropoff_date={dropoff_date}', 'tourfic' )
				),
				array(
					'id'      => 'booking-notice',
					'type'    => 'notice',
					'class'   => 'info',
					'title'   => esc_html__( 'Query Attribute List', 'tourfic' ),
					'content' => esc_html__( 'You can use the following placeholders in the Query Attribute body:', 'tourfic' ) . '<br><br><strong>{pickup} </strong> : To Display Pickup Location from Search.<br>
					<strong>{dropoff} </strong> : To Display Dropoff Location from Search.<br>
					<strong>{pickup_date} </strong> : To display the Pickup Date from Search.<br>
					<strong>{dropoff_date} </strong> : To display the Dropoff Date from Search.<br>',
					'dependency'  => array(
						array( 'booking-by', '==', '2' ),
						array( 'booking-attribute', '==', '1' )
					),
				),
				array(
					'id'        => 'is_taxable',
					'type'      => 'switch',
					'label'     => esc_html__( 'Taxable', 'tourfic' ),
					'subtitle' => esc_html__( 'Activate this option to enable tax for this tour.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),
				array(
					'id'      => 'taxable_class',
					'type'    => 'select',
					'label'   => esc_html__( 'Tax class', 'tourfic' ),
					'subtitle'  => esc_html__( 'Select your class, and tax will calculate based on your chosen class. PS: If you activate partial payment option tax will be calculated upon partial amount as woocommerce regulations.', 'tourfic' ),
					'options' => function_exists( 'tf_taxable_option_callback' ) ? tf_taxable_option_callback() : [''],
					'dependency'  => array(
						array( 'is_taxable', '==', '1' )
					),
				),
			),
		),

		// FAQ
		'faq' => array(
			'title'  => __( 'FAQ Section', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'    => 'car-faq-heading',
					'type'  => 'heading',
					'label' => 'FAQ Section',
					'subtitle' => __( 'This section is designed to help users find answers to common questions.', 'tourfic' ),
				),
				array(
					'id'      => 'car-faq-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/add-new-apartment/faq-terms/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'           => 'faq',
					'type'         => 'repeater',
					'button_title' => __( 'Add New Faq', 'tourfic' ),
					'label'        => __( 'Add Your Faqs', 'tourfic' ),
					'subtitle'        => __( 'Click the button below to add Faqs for your Car. Feel free to add as many as needed. Additionally, you can duplicate or rearrange each Faq using the icons on the right side.', 'tourfic' ),
					'field_title'  => 'title',
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
						),
						array(
							'id'    => 'description',
							'type'  => 'editor',
							'label' => __( 'Description ', 'tourfic' ),
						),
					),
				)
			),
		),

		// Terms & Conditions
		'terms_and_conditions' => array(
			'title'  => __( 'Terms & Conditions', 'tourfic' ),
			'icon'   => 'fa-regular fa-square-check',
			'fields' => array(
				array(
					'id'    => 'car-tnc-heading',
					'type'  => 'heading',
					'label' => 'Terms & Conditions Section',
					'subtitle' => __( 'Include your set of regulations and guidelines that customers must agree to in order to use the service provided in your car package. ', 'tourfic' ),
				),
				array(
					'id'      => 'car-tnc-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-terms-conditions/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'car-tc-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the Section', 'tourfic' ),
					'subtitle' => __( 'This text will appear as the heading of the T&C section on the frontend.', 'tourfic' ),
					'default' => __( "Tour Terms & Conditions", 'tourfic' ),
				),
				array(
					'id'           => 'terms_conditions',
					'type'         => 'repeater',
					'button_title' => __( 'Add New Terms Condition', 'tourfic' ),
					'label'        => __( 'Add Your Terms Conditions', 'tourfic' ),
					'subtitle'        => __( 'Click the button below to add Terms Conditions for your Car. Feel free to add as many as needed. Additionally, you can duplicate or rearrange each Badge using the icons on the right side.', 'tourfic' ),
					'field_title'  => 'title',
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Title', 'tourfic' ),
						),
						array(
							'id'    => 'content',
							'type'  => 'editor',
							'label' => __( 'Content', 'tourfic' ),
						)
					),
				)
			),
		),

	),
) );
