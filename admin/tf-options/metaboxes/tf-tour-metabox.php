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
	'title'     => __( 'Tour Settings', 'tourfic' ),
	'post_type' => 'tf_tours',
	'sections'  => array(
		// General
		'general'              => array(
			'title'  => __( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'tour-general-heading',
					'type'  => 'heading',
					'label' => 'General Settings',
					'subtitle' => __( 'These are some common settings specific to this Tour Package.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-general-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tourfic-hotel-general-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'tour_as_featured',
					'type'     => 'switch',
					'label'    => __( 'Set as featured', 'tourfic' ),
					'subtitle' => __( 'This tour will be featured at the top of both the search results and the tour archive page.', 'tourfic' ),
				),
				array(
					'id'          => 'featured_text',
					'type'        => 'text',
					'label'       => __( 'Tour Featured Text', 'tourfic' ),
					'subtitle'    => __( 'Enter Featured Tour Text', 'tourfic' ),
					'placeholder' => __( 'Enter Featured Tour Text', 'tourfic' ),
					'default' => __( 'Hot Deal', 'tourfic' ),
					'dependency'  => array( 'tour_as_featured', '==', true ),
				),
				array(
					'id'       => 'tf_single_tour_layout_opt',
					'type'     => 'select',
					'label'    => __( 'Single Tour Template Settings', 'tourfic' ),
					'subtitle' => __( 'You can keep the Global Template settings or choose a different layout for this tour.', 'tourfic' ),
					'options'  => [
						'global' => __( 'Global Settings', 'tourfic' ),
						'single' => __( 'Single Settings', 'tourfic' ),
					],
					'default'  => 'global',
				),
				array(
					'id'       => 'tf_single_tour_template',
					'type'     => 'imageselect',
					'label'    => __( 'Single Tour Page Layout', 'tourfic' ),
					'multiple' 		=> true,
					'inline'   		=> true,
					'options'   	=> array( 
						'design-1' 				=> array(
							'title'			=> 'Design 1',
							'url' 			=> TF_ASSETS_ADMIN_URL."images/template/design1-tour.jpg",
						),
						'design-2' 				=> array(
							'title'			=> 'Design 2',
							'url' 			=> TF_ASSETS_ADMIN_URL."images/template/design2-tour.jpg",
						),
						'default' 			=> array(
							'title'			=> 'Defult',
							'url' 			=> TF_ASSETS_ADMIN_URL."images/template/default-tour.jpg",
						),
					),
					'default'   	=> 'design-1',
					'dependency'  => [
						array( 'tf_single_tour_layout_opt', '==', 'single' )
					],
				),

				array(
					'id'    => 'tour_gallery',
					'type'  => 'gallery',
					'label' => __( 'Tour Gallery', 'tourfic' ),
					'subtitle' => __( 'Add multiple images to craft a captivating gallery for your tour, giving potential customers a visual tour.', 'tourfic' ),
				),

				array(
					'id'     => 'tour_video',
					'type'   => 'text',
					'label'  => __( 'Tour Video', 'tourfic' ),
					'subtitle' => __( 'If you have an enticing video of your tour, simply upload it to YouTube or Vimeo and insert the URL here to showcase it to your guests.', 'tourfic' ),
				),
			),
		),
		// Location
		'location'             => array(
			'title'  => __( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'    => 'tour-location-heading',
					'type'  => 'heading',
					'label' => 'Location Settings',
					'subtitle' => __( 'The location of a tour is a crucial element for every tour package. Set your tour locations in this section.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-location-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-location-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'location',
					'class'    => 'gmaps',
					'type'     => 'map',
					'label'    => __( 'Dynamic Location Search', 'tourfic' ),
					'subtitle' => __( 'Enter the specific address you wish to use for the tour and select the correct option from the suggested addresses. This will be used to hyperlink address and display the address on the front-end map. Note that the address provided in the previous section is solely for display purposes!', 'tourfic' ),
					'height'   => '250px',
					'settings' => array(
						'scrollWheelZoom' => true,
					),
					'attributes'  => array(
						'required' => 'required',
					),
				),
			),
		),
		// Information
		'information'          => array(
			'title'  => __( 'Information', 'tourfic' ),
			'icon'   => 'fa-solid fa-circle-info',
			'fields' => array(
				array(
					'id'    => 'tour-info-heading',
					'type'  => 'heading',
					'label' => 'Tour Information Section',
					'subtitle' => __( 'Ensure to furnish customers with all the essential information they need to fully understand your tour package.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-info-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-information/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'          => 'duration',
					'type'        => 'text',
					'label'       => __( 'Tour Duration', 'tourfic' ),
					'subtitle'    => __( 'E.g. 3', 'tourfic' ),
					'field_width' => 50,
				),				
				array(
					'id'      => 'duration_time',
					'type'    => 'select',
					'label'   => __( 'Duration time', 'tourfic' ),
					'subtitle'    => __( 'E.g. Days', 'tourfic' ),
					'options' => array(
						'Day' => __( 'Days', 'tourfic' ),
						'Hour' => __( 'Hours', 'tourfic' ),
						'Minute' => __( 'Minutes', 'tourfic' ),
					),
					'field_width' => 50,
				),
				array(
					'id'       => 'tour_types',
					'type'     => 'select2',
					'multiple' => true,
					'is_pro'   => true,
					'field_width' => 50,
					'label'    => __( 'Select Tour Types', 'tourfic' ),
					'subtitle' => __( 'Choose your tour types. You must first create them from ', 'tourfic' ) . ' <a href="'.admin_url('edit-tags.php?taxonomy=tour_type&post_type=tf_tours').'" target="_blank"><strong>' . __( 'this location', 'tourfic' ) . '</strong></a>.',
				),
				array(
					'id'          => 'language',
					'type'        => 'text',
					'label'       => __( 'Tour Languages', 'tourfic' ),
					'subtitle'    => __( 'Include multiple language seperated by comma (,)', 'tourfic' ),
					'field_width' => 50,
				),
				array(
					'id'          => 'night',
					'type'        => 'switch',
					'label'       => __( 'Multiply Pricing By Night', 'tourfic' ),
					'subtitle'    => __( 'The total booking cost is calculated by multiplying the nightly rate by the number of nights booked.', 'tourfic' ),
					'field_width' => '50',
				),
				
				array(
					'id'          => 'night_count',
					'type'        => 'number',
					'label'       => __( 'Total nights', 'tourfic' ),
					'subtitle'    => __( 'E.g. 2', 'tourfic' ),
					'field_width' => '50',
					'dependency'  => array(
						array( 'night', '==', 'true' ),
					),
				),
				array(
					'id'          => 'group_size',
					'type'        => 'text',
					'label'       => __( 'Group Size', 'tourfic' ),
					'subtitle'    => __( 'E.g. 10 people', 'tourfic' ),
					'field_width' => '50',
				),
				array(
					'id'          => 'refund_des',
					'type'        => 'text',
					'label'       => __( 'Refund Text', 'tourfic' ),
					'subtitle'    => __( 'If you have any refund policy, you can add them here.', 'tourfic' ),
					'field_width' => 100,
				),
				array(
					'id'      => 'highlights-sections',
					'type'    => 'heading',
					'content' => __( 'Tour Highlights', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'    => 'highlights-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the Section ', 'tourfic' ),
					'subtitle'    => __( 'This text will appear as the heading of the highlights section on the frontend.', 'tourfic' ),
					'default' => __("Highlights", 'tourfic'),
				),
				array(
					'id'       => 'additional_information',
					'type'     => 'editor',
					'label'    => __( 'Tour Highlights', 'tourfic' ),
					'subtitle' => __( 'Provide a brief overview of your tour package.', 'tourfic' ),
				),
				array(
					'id'      => 'hightlights_thumbnail',
					'type'    => 'image',
					'label'   => __( 'Tour Highlights Thumbnail', 'tourfic' ),
					'subtitle'    => __( 'Please upload an image to be displayed as the Thumbnail for this section.', 'tourfic' ),
					'library' => 'image',
				),
				array(
					'id'       => 'features',
					'type'     => 'select2',
					'multiple' => true,
					'is_pro'   => true,
					'label'    => __( 'Select features', 'tourfic' ),
					'subtitle'   => __( 'For instance, select amenities like a Breakfast, AC Bus, Tour Guide, and more as applicable. You need to create these features from the ', 'tourfic' ) . ' <a href="'.admin_url('edit-tags.php?taxonomy=tour_features&post_type=tf_tours').'" target="_blank"><strong>' . __( '“Features”', 'tourfic' ) . '</strong></a> tab.',
				),
			),
		),
		// Contact Info
		'contact_info'         => array(
			'title'  => __( 'Contact Info', 'tourfic' ),
			'icon'   => 'fa-solid fa-address-book',
			'fields' => array(
				array(
					'id'    => 'tour-continfo-heading',
					'type'  => 'heading',
					'label' => 'Contact Info Section',
					'subtitle' => __( 'How can potential or existing customers reach out for more details about your tour? Please share your contact information here.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-continfo-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-contact-info/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'contact-info-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the Section', 'tourfic' ),
					'subtitle' => __( 'This text will appear as the heading of the Contact info section on the frontend.', 'tourfic' ),
					'default' => __("Contact Information", 'tourfic'),
				),
				array(
					'id'          => 'email',
					'type'        => 'text',
					'label'       => __( 'Email address', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
				),
				array(
					'id'          => 'phone',
					'type'        => 'text',
					'label'       => __( 'Phone Number', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
				),
				array(
					'id'          => 'website',
					'type'        => 'text',
					'label'       => __( 'Website Url', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
				),
				array(
					'id'          => 'fax',
					'type'        => 'text',
					'label'       => __( 'Fax Number', 'tourfic' ),
					'subtitle'       => __( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
				),
			),
		),
		// //  Tour Extra
		'tour_extra'           => array(
			'title'  => __( 'Tour Extras', 'tourfic' ),
			'icon'   => 'fa-solid fa-route',
			'fields' => array(
				array(
					'id'    => 'tour-extras-heading',
					'type'  => 'heading',
					'label' => 'Offer Tour Extras',
					'subtitle' => __( 'If you wish to provide additional services that are not included in your current tour package, you can list them here.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-extras-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-extra/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'     => 'tour-extra',
					'type'   => 'repeater',
					'label'        => __( 'Add Extra Services Available on Your Tour', 'tourfic' ),
					'subtitle'        => __( 'You may offer these extras for free, or opt to charge your customers for them.', 'tourfic' ),
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
							'label' => __( 'Description', 'tourfic' ),
							'is_pro' => true,
						),
						array(
							'id'       => '',
							'type'     => 'select',
							'label'    => __( 'Pricing rule', 'tourfic' ),
							'subtitle' => __( 'Select Your Pricing Logic', 'tourfic' ),
							'class'    => 'pricing',
							'options'  => [
								'fixed'  => __( 'Fixed', 'tourfic' ),
								'person' => __( 'Per Person', 'tourfic' ),
							],
							'default'  => 'fixed',
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
					'id'    => 'tour-pricing-heading',
					'type'  => 'heading',
					'label' => 'Tour Pricing Settings',
					'subtitle' => __( 'The pricing of a tour package plays a crucial role. Make sure you set it correctly.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-pricing-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tourfic-price-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'pricing',
					'type'     => 'select',
					'label'    => __( 'Pricing rule for the Tour', 'tourfic' ),
					'subtitle' => __( 'Select your pricing logic.', 'tourfic' ),
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
					'id'    => 'tour-availability-heading',
					'type'  => 'heading',
					'label' => 'Tour Availability Settings',
					'subtitle' => __( 'This section provides crucial information on the dates and times when the tour is open for booking.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-availablity-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-availability/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'type',
					'type'     => 'select',
					'label'    => __( 'Tour Type', 'tourfic' ),
					'subtitle' => __( 'Continous: The package will be available every month within the mentioned range. Fixed: The Tour package will be available on a fixed date. ', 'tourfic' ),
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
					'subtitle' => __( 'If you need to set custom availablity for this tour, enable this option. ', 'tourfic' ),
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
							'label'       => __( 'Minimum Person', 'tourfic' ),
							'subtitle'    => __( 'Specify the minimum person required to book this tour.','tourfic' ),
							'is_pro' => true,
						),
						array(
							'id'     => '',
							'type'   => 'number',
							'label'       => __( 'Maximum Person', 'tourfic' ),
							'subtitle'    => __( 'Indicate the maximum number of persons this package can be booked for.','tourfic'), 
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
							'subtitle'    => __( 'Insert amount only. If you chose "per group" pricing rule, then no need to fill this up.', 'tourfic' ),
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
							'subtitle'    => __( 'Insert amount only. If you chose "per group" pricing rule, then no need to fill this up.', 'tourfic' ),
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
							'subtitle'    => __( 'Insert amount only. If you chose "per group" pricing rule, then no need to fill this up.', 'tourfic' ),
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
							'subtitle'    => __( 'Insert amount only. If you chose "per person" pricing rule, then no need to fill this up.', 'tourfic' ),
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
									'subtitle' => __( 'Choose the time of day when bookings for this tour are available within the specified date period.', 'tourfic' ),
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
					'label'       => __( 'Minimum Person (Required for Search)', 'tourfic' ),
					'subtitle'    => __( 'Specify the minimum person required to book this tour.', 'tourfic' ),
					'dependency'  => array(
						array( 'type', '==', 'continuous' ),
						array( 'custom_avail', '==', 'false' ),
					),
					'field_width' => '50',
				),
				array(
					'id'          => 'cont_max_people',
					'type'        => 'number',
					'label'       => __( 'Maximum Person (Required for Search)', 'tourfic' ),
					'subtitle'    => __( 'Indicate the maximum number of persons this package can be booked for.', 'tourfic' ),
					'dependency'  => array(
						array( 'type', '==', 'continuous' ),
						array( 'custom_avail', '==', 'false' ),
					),
					'field_width' => '50',
				),
				array(
					'id'          => 'cont_max_capacity',
					'type'        => 'number',
					'label'       => __( 'Maximum Capacity', 'tourfic' ),
					'subtitle'    => __( 'Indicate the maximum number of people (including adults and children) allowed per day for this tour.', 'tourfic' ),
					'dependency'  => array(
						array( 'type', '==', 'continuous' ),
						array( 'custom_avail', '==', 'false' ),
					),
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
							'subtitle' => __( 'Choose the time of day when bookings for this tour are available.', 'tourfic' ),
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
					'content'    => __( 'Disable Days & Dates', 'tourfic' ),
					'dependency' => array(
						array( 'type', '==', 'continuous' ),
						array( 'custom_avail', '==', 'false' ),
					),
				),
				array(
					'id'         => '',
					'type'       => 'checkbox',
					'label'      => __( 'Select day to disable', 'tourfic' ),
					'subtitle'    => __( 'Specify the day of the week when this tour will be unavailable for booking.', 'tourfic' ),
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
					'label'        => __( 'Disable Date Range', 'tourfic' ),
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
							'subtitle'    => __( 'Specify the date range when this tour will be unavailable for booking.', 'tourfic' ),
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
					'subtitle'    => __( 'Select the specific date when this tour will be unavailable for booking.', 'tourfic' ),
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
									'label'      => __( 'Specify the date range when this tour will be available for booking.', 'tourfic' ),
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
									'subtitle'    => __( 'Specify the minimum person required to book this tour.', 'tourfic' ),
								),
								array(
									'id'       => '',
									'type'     => 'number',
									'label'    => __( 'Maximum People', 'tourfic' ),
									'is_pro'   => true,
									'subtitle'    => __( 'Indicate the maximum number of persons this package can be booked for.', 'tourfic' ),
								),
								array(
									'id'          => '',
									'type'        => 'number',
									'label'       => __( 'Maximum Capacity', 'tourfic' ),
									'subtitle'    => __( 'Indicate the maximum number of people (including adults and children) allowed per day for this tour.', 'tourfic' ),
									'is_pro'   => true,
								),
								array(
									'id'        => 'tf-repeat-months-switch',
									'type'      => 'switch',
									'label'     => __( 'Enable Repeat by Months', 'tourfic' ),
									'subtitle'  => __( 'Enable this option, if you want to repeat fixed tour by months in one calendar year', 'tourfic' ),
									'label_on'  => __( 'Yes', 'tourfic' ),
									'label_off' => __( 'No', 'tourfic' ),
									'default' => false,
									'is_pro'   => true,
								),
								array(
									'id' => 'tf-repeat-months-checkbox',
									'type' => 'checkbox',
									'label' => __('Repeat Fixed Tours', 'tourfic'),
									'subtitle' => __('Select Months you want to Repeat the Tour', 'tourfic'),
									'class' => 'tf-months-checkbox',
									'options' => array(
										'01' => __('January', 'tourfic'),
										'02' => __('February', 'tourfic'),
										'03' => __('March', 'tourfic'),
										'04' => __('April', 'tourfic'),
										'05' => __('May', 'tourfic'),
										'06' => __('June', 'tourfic'),
										'07' => __('July', 'tourfic'),
										'08' => __('August', 'tourfic'),
										'09' => __('September', 'tourfic'),
										'10' => __('October', 'tourfic'),
										'11' => __('November', 'tourfic'),
										'12' => __('December', 'tourfic')
									),
									// 'default' => date('m'),
									'inline' => 1,
									"dependency" => array(
										array("tf-repeat-months-switch", "==", 'true')
									)
								)
							),
						),

					),
				),

			),
		),

		// // Booking
		'booking'              => array(
			'title'  => __( 'Booking', 'tourfic' ),
			'icon'   => 'fa-solid fa-person-walking-luggage',
			'fields' => array(
				array(
					'id'    => 'tour-booking-heading',
					'type'  => 'heading',
					'label' => 'Booking Settings',
					'subtitle' => __( 'This section offer the options to customize the booking process for your tours.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-booking-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/booking/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => '',
					'type'     => 'number',
					'is_pro'   => true,
					'label'    => __( 'Minimum days for Booking', 'tourfic' ),
					'subtitle' => __( 'Set the minimum number of days required to book in advance of the departure date.', 'tourfic' ),
				),
				array(
					'id'        => 'disable_same_day',
					'type'      => 'switch',
					'label'     => __( 'Do you want to disable same-day booking?', 'tourfic' ),
					'subtitle'  => __( 'If enabled,  the tour cannot be booked on the same day.', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
				),
				array(
					'id'        => '',
					'type'      => 'switch',
					'label'     => __( 'Enable Traveler Info', 'tourfic' ),
					'subtitle'  => __( 'Enable this option, if you want to add traveler info.', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => true,
					'is_pro'  => true,
				),
				array(
					'id'      => 'booking-by',
					'type'    => 'select',
					'label'   => __( 'Booking Type', 'tourfic' ),
					'subtitle'  => __( 'Choose the type of booking you would like to implement for this tour.', 'tourfic' ),
					'options' => array(
						'1' => __( 'Default Booking (WooCommerce)', 'tourfic' ),
						'2' => __( 'External Booking (Pro)', 'tourfic' ),
						'' => __( 'Booking Without Payment (Pro)', 'tourfic' ),
					),
					'default' => '1',
				),
				array(
					'id'          => '',
					'type'        => 'text',
					'label'       => __( 'External Booking URL', 'tourfic' ),
					'placeholder' => __( 'https://website.com', 'tourfic' ),
					'is_pro'  => true,
					'dependency'  => array( 'booking-by', '==', '2' ),
				),
				array(
					'id'        => '',
					'type'      => 'switch',
					'label'     => __( 'Hide Booking Form', 'tourfic' ),
					'subtitle' => __( 'Enable this option to hide the booking form from the single tour page.', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false,
					'dependency' => array( 'booking-by', '==', '2' ),
				),
				array(
					'id'        => '',
					'type'      => 'switch',
					'label'     => __( 'Hide Price', 'tourfic' ),
					'subtitle' => __( 'Enable this option to hide the price from the single tour page.', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false,
					'dependency' => array( 'booking-by', '==', '2' ),
				),
				array(
					'id'        => '',
					'type'      => 'switch',
					'label'     => __( 'Allow Attribute', 'tourfic' ),
					'subtitle'  => __( 'If attribute allow, You can able to add custom Attribute', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'is_pro'  => true,
					'dependency'  => array( 'booking-by', '==', '2' ),
				),
				array(
					'id'          => '',
					'type'        => 'textarea',
					'label'       => __( 'Query Attribute', 'tourfic' ),
					'placeholder' => __( 'adult={adult}&child={child}&infant={infant}', 'tourfic' ),
					'is_pro'  => true,
					'dependency'  => array( 'booking-by', '==', '2' ),
				),
				array(
					'id'      => 'booking-notice',
					'type'    => 'notice',
					'class'   => 'info',
					'title'   => __( 'Query Attribute List', 'tourfic' ),
					'content' => __( 'You can use the following placeholders in the Query Attribute body:', 'tourfic' ) . '<br><br><strong>{adult} </strong> : To Display Adult Number from Search.<br>
					<strong>{child} </strong> : To Display Child Number from Search.<br>
					<strong>{booking_date} </strong> : To display the Booking date from Search.<br>
					<strong>{infant} </strong> : To display the infant number from Search.<br>',
					'is_pro'  => true,
					'dependency'  => array( 'booking-by', '==', '2' ),
				),
			),
		),
		// // Exclude/Include
		'exclude_Include'      => array(
			'title'  => __( 'Include & Exclude', 'tourfic' ),
			'icon'   => 'fa-solid fa-square-check',
			'fields' => array(
				array(
					'id'    => 'tour-inc-heading',
					'type'  => 'heading',
					'label' => 'Include & Exclude Section',
					'subtitle' => __( 'Each tour includes certain items, while others are not part of the package. Clearly define these inclusions and exclusions to prevent any misunderstandings during your tour.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-inc-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-include-exclude/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'           => 'inc',
					'type'         => 'repeater',
					'label'        => __( 'Items Included', 'tourfic' ),
					'subtitle'     => __( 'Add all the items/features included in this tour package.', 'tourfic' ),
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
					'label'    => __( 'Icon for Included Item', 'tourfic' ),
					'subtitle' => __( 'Choose icon', 'tourfic' ),
				),
				array(
					'id'           => 'exc',
					'type'         => 'repeater',
					'label'        => __( 'Items Excluded', 'tourfic' ),
					'subtitle'        => __( 'List all the items/features excluded in this tour package.', 'tourfic' ),
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
					'label'    => __( 'Icon for Excluded item', 'tourfic' ),
					'subtitle' => __( 'Choose icon', 'tourfic' ),
				),
				array(
					'id'      => 'include-exclude-bg',
					'type'    => 'image',
					'label'   => __( 'Background Image', 'tourfic' ),
					'subtitle'    => __( 'This will be added as the background image of this section.', 'tourfic' ),
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
					'id'    => 'tour-itibuilder-heading',
					'type'  => 'heading',
					'label' => 'Tour Itinerary Builder',
					'subtitle' => __( 'Create a detailed schedule for a tour. This builder allows for the organization of various components of a tour into a coherent and structured timeline.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-itibuilder-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tourfic-itinerary-builder/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'itinerary-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the section', 'tourfic' ),
					'subtitle' => __( 'This text will appear as the heading of the Tour Itinerary section on the frontend.', 'tourfic' ),
					'default' => __("Travel Itinerary", 'tourfic'),
				),
				array(
					'id'           => 'itinerary',
					'type'         => 'repeater',
					'button_title' => __( 'Add New Itinerary', 'tourfic' ),
					'label'        => __( 'Create your Travel Itinerary', 'tourfic' ),
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
							'label'       => __( 'Itinerary Title', 'tourfic' ),
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
							'label'        => __( 'Upload Image for the Description', 'tourfic' ),
							'library'      => 'image',
							'placeholder'  => 'http://',
							'button_title' => __( 'Add Image', 'tourfic' ),
							'remove_title' => __( 'Remove Image', 'tourfic' ),
						),
						array(
							'id'    => 'desc',
							'type'  => 'editor',
							'label' => __( 'Itinerary Description', 'tourfic' ),
						),
						array(
							'id'     => 'gallery_image',
							'type'   => 'gallery',
							'label' => __( 'Itinerary Gallery Image', 'tourfic' ),
							'is_pro' => true,
						),
						array(
							'id'           => 'itinerary-sleep-mode',
							'type'         => 'repeater',
							'button_title' => __( 'Add New Option', 'tourfic' ),
							'label'        => __( 'Custom Itinerary options', 'tourfic' ),
							'subtitle'     => __( 'You must first set up these options in the ', 'tourfic' ) . ' <a href="'.admin_url('admin.php?page=tf_settings#tab=tour_itinerary').'" target="_blank"><strong>' . __( 'Tourfic Settings', 'tourfic' ) . '</strong></a> (Tour Options > Itinerary Settings) before proceeding.',
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
							'subtitle'     => __( 'You must first set up these options in the ', 'tourfic' ) . ' <a href="'.admin_url('admin.php?page=tf_settings#tab=tour_itinerary').'" target="_blank"><strong>' . __( 'Tourfic Settings', 'tourfic' ) . '</strong></a> (Tour Options > Itinerary Settings) before proceeding.',
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
					'label' => __( 'Itinerary Downloader Settings', 'tourfic' ),
					'subtitle' => __( 'These are some additional settings specific to the Itinerary PDF downloader. Note that some of these settings may override the global settings. ', 'tourfic' ),
				),
				array(
					'id'      => 'itenary_download_glbal_settings',
					'label'   => __( 'Itenary Downolad Settings', 'tourfic' ),
					'type'    => 'select',
					'options' => [
						'global' => __( 'Global Setting', 'tourfic' ),
						'custom'  => __( 'Custom Setting', 'tourfic' ),
					],
					'default' => 'custom',
					'is_pro'   => true,
				),
				array(
					'id'       => 'itinerary-downloader',
					'type'     => 'switch',
					'label'    => __( 'Enable Itinerary Downloader', 'tourfic' ),
					'subtitle' => __( 'Turn this on to give customers the option to download the itinerary plan as a PDF.', 'tourfic' ),
					'dependency'  => array(
						array( 'itenary_download_glbal_settings', '==', 'custom' ),
					),
					'is_pro'   => true,
				),
				array(
					'id'      => 'tour_pdf_downloader_section',
					'type'    => 'heading',
					'content' => __( 'Tour Itinerary Downloader Section', 'tourfic' ),
				),
				array(
					'id'    => '',
					'type'  => 'text',
					'label' => __( 'Itinerary Downloader Title', 'tourfic' ),
					'default' => "Want to read it later?",
					'is_pro'   => true,
				),
				array(
					'id'    => '',
					'type'  => 'text',
					'label' => __( 'Itinerary Downloader Description', 'tourfic' ),
					'default' => "Download this tour's PDF brochure and start your planning offline.",
					'is_pro'   => true,
				),
				array(
					'id'    => '',
					'type'  => 'text',
					'label' => __( 'Itinerary Downloader Button Text', 'tourfic' ),
					'default' => "Download Now",
					'is_pro'   => true,
				),
				array(
					'id'      => 'tour_settings',
					'type'    => 'heading',
					'content' => __( 'Thumbnail Settings in PDF', 'tourfic' ),
				),
				array(
					'id'          => '',
					'type'        => 'number',
					'label'   => __( 'Image Thumbnail Height', 'tourfic' ),
					'field_width' => 50,
					'is_pro'      => true,
				),
				array(
					'id'          => '',
					'type'        => 'number',
					'label'   => __( 'Image Thumbnail Width ', 'tourfic' ),
					'field_width' => 50,
					'is_pro'      => true,
				),
				array(
					'id'      => 'companey_info_heading',
					'type'    => 'heading',
					'content' => __( 'Company Info in PDF', 'tourfic' ),
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
					'label' => __( 'Company Description', 'tourfic' ),
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
					'label' => __( 'Talk to Expert Section', 'tourfic' ),
				),
				array(
					'id'      => '',
					'type'    => 'switch',
					'label'   => __( 'Enable Talk To Expert Section in PDF', 'tourfic' ),
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
					'label'      => __( 'Enable Viber Contact ', 'tourfic' ),
					'is_pro' => true,
				),
				array(
					'id'     => '',
					'type'   => 'switch',
					'label'      => __( 'Enable WhatsApp Contact', 'tourfic' ),
					'is_pro' => true,
				),
			),
		),

		// FAQs
		'faqs'                 => array(
			'title'  => __( 'FAQ Section', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'    => 'tour-faq-heading',
					'type'  => 'heading',
					'label' => 'FAQ Section',
					'subtitle' => __( 'This section is designed to help users find answers to common questions.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-faq-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-faqs/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'faq-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the Section', 'tourfic' ),
					'subtitle' => __( 'This text will appear as the heading of the FAQ section on the frontend.', 'tourfic' ),
					'default' => "Frequently Asked Questions"
				),
				array(
					'id'           => 'faqs',
					'type'         => 'repeater',
					'label'        => __( 'Add Your Questions', 'tourfic' ),
					'subtitle' 	   => __( 'Click the button below to add Frequently Asked Questions (FAQs) for your tour. Feel free to add as many as needed. Additionally, you can duplicate or rearrange each FAQ using the icons on the right side.', 'tourfic' ),
					'button_title' => __( 'Add New FAQ', 'tourfic' ),
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => __( 'Single FAQ Title', 'tourfic' ),
						),
						array(
							'id'    => 'desc',
							'type'  => 'editor',
							'label' => __( 'Single FAQ Description', 'tourfic' ),
						),
					),
				),
			),
		),
		// Tour Enquiry
		't_enquiry'  => array(
			'title'  => __( 'Tour Enquiry', 'tourfic' ),
			'icon'   => 'fa fa-question-circle-o',
			'fields' => array(
				array(
					'id'      => 'enquiry-section',
					'type'    => 'heading',
					'content' => __( 'Tour Enquiry Form', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'        => 't-enquiry-section',
					'type'      => 'switch',
					'label'     => __( 'Enable Tour Enquiry Form Option', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => true
				),
				array(
					'id'       => 't-enquiry-option-icon',
					'type'     => 'icon',
					'label'    => __( 'Tour Enquiry icon', 'tourfic' ),
					'subtitle' => __( 'Choose icon', 'tourfic' ),
					'default'  => 'fa fa-question-circle-o',
					'dependency' => array( 't-enquiry-section', '==', '1' ),
				),
				array(
					'id'    => 't-enquiry-option-title',
					'type'  => 'text',
					'label' => __( 'Enquiry Title', 'tourfic' ),
					'default' => "Have a question in mind",
					'dependency' => array( 't-enquiry-section', '==', '1' ),
				),
				array(
					'id'    => 't-enquiry-option-content',
					'type'  => 'text',
					'label' => __( 'Enquiry Description', 'tourfic' ),
					'default' => "Looking for more info? Send a question to the tour agent to find out more.",
					'dependency' => array( 't-enquiry-section', '==', '1' ),
				),
				array(
					'id'    => 't-enquiry-option-btn',
					'type'  => 'text',
					'label' => __( 'Enquiry Button Text', 'tourfic' ),
					'default' => "Ask a Question",
					'dependency' => array( 't-enquiry-section', '==', '1' ),
				),
			),
		),

		// Multiple tags for tours
		'tours_multiple_tags' => array(
			'title'  => __( 'Labels', 'tourfic' ),
			'icon'   => 'fa fa-list',
			'fields' => array(
				array(
					'id'      => 'tf-tour-tags-heading',
					'type'    => 'heading',
					'label' => __( 'Tour labels', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'           => 'tf-tour-tags',
					'type'         => 'repeater',
					'label'        => __( 'Labels', 'tourfic' ),
					'subtitle' => __('Add some keywords that highlight your tour\'s Unique Selling Point (USP). This label will be displayed on both the Archive Page and the Search Results Page.', 'tourfic'),
					'button_title' => __( 'Add / Insert New Label', 'tourfic' ),
					'fields'       => array(

						array(
							'id'    => 'tour-tag-title',
							'type'  => 'text',
							'label' => __( 'Label Title', 'tourfic' ),
						),

						array(
							'id'       => 'tour-tag-color-settings',
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

		// Terms & Conditions
		'terms_and_conditions' => array(
			'title'  => __( 'Terms & Conditions', 'tourfic' ),
			'icon'   => 'fa-regular fa-square-check',
			'fields' => array(
				array(
					'id'    => 'tour-tnc-heading',
					'type'  => 'heading',
					'label' => 'Terms & Conditions Section',
					'subtitle' => __( 'Include your set of regulations and guidelines that customers must agree to in order to use the service provided in your tour package. ', 'tourfic' ),
				),
				array(
					'id'      => 'tour-tnc-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-terms-conditions/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'tc-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the Section', 'tourfic' ),
					'subtitle' => __( 'This text will appear as the heading of the T&C section on the frontend.', 'tourfic' ),
					'default' => __( "Tour Terms & Conditions", 'tourfic' ),
				),
				array(
					'id'    => 'terms_conditions',
					'type'  => 'editor',
					'label' => __( 'Terms & Conditions of this tour', 'tourfic' ),
					'subtitle' => __( "Enter your tour's terms and conditions in the text editor provided below.", 'tourfic' ),
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
					'label' =>  __('Other Settings', 'tourfic' ),
					'subtitle' => __( 'These are some additional settings specific to this Tour Package. Note that some of these settings may override the global settings. ', 'tourfic' ),
				),
				array(
					'id'      => 'tour-setting-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
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
					'id'        => 't-share',
					'type'      => 'switch',
					'label'     => __( 'Disable Share Option', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false
				),
				array(
					'id'        => 't-wishlist',
					'type'      => 'switch',
					'label'     => __( 'Disable Wishlist Option', 'tourfic' ),
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
					'id'      => 'tour-booking-section',
					'type'    => 'heading',
					'content' => __( 'Titles / Heading of Different Sections', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'    => 'booking-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the Booking Section', 'tourfic' ),
					'default' => __("Book This Tour", 'tourfic'),
				),
				array(
					'id'    => 'description-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the Description Section', 'tourfic' ),
					'default' => __("Description", 'tourfic'),
				),
				
				array(
					'id'    => 'map-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the Map Section', 'tourfic' ),
					'default' => __("Maps", 'tourfic'),
				),
				array(
					'id'    => 'review-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the Reviews Section', 'tourfic' ),
					'default' => __( 'Average Guest Reviews', 'tourfic' ),
				),
			),
		),
	),
) );
