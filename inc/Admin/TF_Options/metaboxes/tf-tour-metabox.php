<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

/**
 * Get all the meals from glabal settings
 * @author AbuHena
 * @since 1.7.0
 */
if(!function_exists('tf_tour_meals')){
	function tf_tour_meals() {
		$itinerary_options = ! empty( Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) ) : '';
		$all_meals         = [];
		if ( ! empty( $itinerary_options['meals'] ) && is_array( $itinerary_options['meals'] ) ) {
			$meals = $itinerary_options['meals'];
			foreach ( $meals as $key => $meal ) {
				$all_meals[ $meal['meal'] . $key ] = $meal['meal'];
			}
		}

		return $all_meals;
	}
}

TF_Metabox::metabox( 'tf_tours_opt', array(
	'title'     => esc_html__( 'Tour Settings', 'tourfic' ),
	'post_type' => 'tf_tours',
	'sections'  => array(
		// General
		'general'              => array(
			'title'  => esc_html__( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'tour-general-heading',
					'type'  => 'heading',
					'label' => 'General Settings',
					'subtitle' => esc_html__( 'These are some common settings specific to this Tour Package.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-general-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tourfic-hotel-general-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'tour_as_featured',
					'type'     => 'switch',
					'label'    => esc_html__( 'Set as featured', 'tourfic' ),
					'subtitle' => esc_html__( 'This tour will be featured at the top of both the search results and the tour archive page.', 'tourfic' ),
				),
				array(
					'id'          => 'featured_text',
					'type'        => 'text',
					'label'       => esc_html__( 'Tour Featured Text', 'tourfic' ),
					'subtitle'    => esc_html__( 'Enter Featured Tour Text', 'tourfic' ),
					'placeholder' => esc_html__( 'Enter Featured Tour Text', 'tourfic' ),
					'default' => esc_html__( 'Hot Deal', 'tourfic' ),
					'dependency'  => array( 'tour_as_featured', '==', true ),
				),
				array(
					'id'       => 'tf_single_tour_layout_opt',
					'type'     => 'select',
					'label'    => esc_html__( 'Single Tour Template Settings', 'tourfic' ),
					'subtitle' => esc_html__( 'You can keep the Global Template settings or choose a different layout for this tour.', 'tourfic' ),
					'options'  => [
						'global' => esc_html__( 'Global Settings', 'tourfic' ),
						'single' => esc_html__( 'Single Settings', 'tourfic' ),
					],
					'default'  => 'global',
				),
				array(
					'id'       => 'tf_single_tour_template',
					'type'     => 'imageselect',
					'label'    => esc_html__( 'Single Tour Page Layout', 'tourfic' ),
					'options'  => array(
						'design-1' => array(
							'title' => esc_html__('Design 1', 'tourfic'),
							'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-single-design-1.png",
							'preview_link' => esc_url('https://tourfic.com/preview/tours/amplified-nz-tour/'),
						),
						'design-2' => array(
							'title' => esc_html__('Design 2', 'tourfic'),
							'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-single-design-2.png",
							'preview_link' => esc_url('https://tourfic.com/preview/tours/ancient-trails-of-japan/'),
						),
						'default'  => array(
							'title' => esc_html__('Legacy', 'tourfic'),
							'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-single-default.png",
							'preview_link' => esc_url('https://tourfic.com/preview/tours/magical-russia/'),
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
					'label' => esc_html__( 'Tour Gallery', 'tourfic' ),
					'subtitle' => esc_html__( 'Add multiple images to craft a captivating gallery for your tour, giving potential customers a visual tour.', 'tourfic' ),
				),

				array(
					'id'     => 'tour_video',
					'type'   => 'text',
					'label'  => esc_html__( 'Tour Video', 'tourfic' ),
					'subtitle' => esc_html__( 'If you have an enticing video of your tour, simply upload it to YouTube or Vimeo and insert the URL here to showcase it to your guests.', 'tourfic' ),
				),
			),
		),
		// Location
		'location'             => array(
			'title'  => esc_html__( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'    => 'tour-location-heading',
					'type'  => 'heading',
					'label' => 'Location Settings',
					'subtitle' => esc_html__( 'The location of a tour is a crucial element for every tour package. Set your tour locations in this section.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-location-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-location-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'location',
					'class'    => 'gmaps',
					'type'     => 'map',
					'label'    => esc_html__( 'Dynamic Location Search', 'tourfic' ),
					/* translators: %s is the link to the Google Maps API Key settings */
					'subtitle' => sprintf( wp_kses_post(__( 'Enter the specific address you wish to use for the tour and select the correct option from the suggested addresses. This will be used to hyperlink address and display the address on the front-end map. <strong>Google Maps is also available for location. Simply set up your <a href="%s" target="_blank">Google Maps API Key</a></strong>', 'tourfic' )), esc_url( admin_url('admin.php?page=tf_settings#tab=map_settings') ) ),
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
			'title'  => esc_html__( 'Information', 'tourfic' ),
			'icon'   => 'fa-solid fa-circle-info',
			'fields' => array(
				array(
					'id'    => 'tour-info-heading',
					'type'  => 'heading',
					'label' => 'Tour Information Section',
					'subtitle' => esc_html__( 'Ensure to furnish customers with all the essential information they need to fully understand your tour package.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-info-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-information/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'          => 'duration',
					'type'        => 'text',
					'label'       => esc_html__( 'Tour Duration', 'tourfic' ),
					'subtitle'    => esc_html__( 'E.g. 3', 'tourfic' ),
					'field_width' => 50,
				),				
				array(
					'id'      => 'duration_time',
					'type'    => 'select',
					'label'   => esc_html__( 'Duration time', 'tourfic' ),
					'subtitle'    => esc_html__( 'E.g. Days', 'tourfic' ),
					'options' => array(
						'Day' => esc_html__( 'Days', 'tourfic' ),
						'Hour' => esc_html__( 'Hours', 'tourfic' ),
						'Minute' => esc_html__( 'Minutes', 'tourfic' ),
					),
					'field_width' => 50,
				),
				array(
					'id'          => 'language',
					'type'        => 'text',
					'label'       => esc_html__( 'Tour Languages', 'tourfic' ),
					'subtitle'    => esc_html__( 'Include multiple languages separated by comma (,)', 'tourfic' ),
					'field_width' => 50,
				),
				array(
					'id'          => 'night',
					'type'        => 'switch',
					'label'       => esc_html__( 'Multiply Pricing By Night', 'tourfic' ),
					'subtitle'    => esc_html__( 'The total booking cost is calculated by multiplying the nightly rate by the number of nights booked.', 'tourfic' ),
					'field_width' => '50',
				),
				
				array(
					'id'          => 'night_count',
					'type'        => 'number',
					'label'       => esc_html__( 'Total nights', 'tourfic' ),
					'subtitle'    => esc_html__( 'E.g. 2', 'tourfic' ),
					'field_width' => '50',
					'dependency'  => array(
						array( 'night', '==', 'true' ),
					),
				),
				array(
					'id'          => 'group_size',
					'type'        => 'text',
					'label'       => esc_html__( 'Group Size', 'tourfic' ),
					'subtitle'    => esc_html__( 'E.g. 10 people', 'tourfic' ),
					'field_width' => '50',
				),
				array(
					'id'          => 'refund_des',
					'type'        => 'text',
					'label'       => esc_html__( 'Refund Text', 'tourfic' ),
					'subtitle'    => esc_html__( 'If you have any refund policy, you can add them here.', 'tourfic' ),
					'field_width' => 100,
				),
				array(
					'id'      => 'description-icon-sections',
					'type'    => 'heading',
					'content' => esc_html__( 'Description Icons', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'       => 'tf-tour-duration-icon',
					'type'     => 'icon',
					'label'    => esc_html__( 'Tour Duration icon', 'tourfic' ),
					'subtitle' => esc_html__( 'Choose icon', 'tourfic' ),
					'default'  => 'ri-history-line',
					'field_width' => '33',
				),
				array(
					'id'       => 'tf-tour-group-icon',
					'type'     => 'icon',
					'label'    => esc_html__( 'Tour Group Size icon', 'tourfic' ),
					'subtitle' => esc_html__( 'Choose icon', 'tourfic' ),
					'default'  => 'ri-team-line',
					'field_width' => '33',
				),
				array(
					'id'       => 'tf-tour-lang-icon',
					'type'     => 'icon',
					'label'    => esc_html__( 'Tour Language icon', 'tourfic' ),
					'subtitle' => esc_html__( 'Choose icon', 'tourfic' ),
					'default'  => 'ri-global-line',
					'field_width' => '33',
				),
				array(
					'id'      => 'highlights-sections',
					'type'    => 'heading',
					'content' => esc_html__( 'Tour Highlights', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'    => 'highlights-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the Section ', 'tourfic' ),
					'subtitle'    => esc_html__( 'This text will appear as the heading of the highlights section on the frontend.', 'tourfic' ),
					'default' => esc_html__("Highlights", 'tourfic'),
				),
				array(
					'id'       => 'additional_information',
					'type'     => 'editor',
					'label'    => esc_html__( 'Tour Highlights', 'tourfic' ),
					'subtitle' => esc_html__( 'Provide a brief overview of your tour package.', 'tourfic' ),
				),
				array(
					'id'      => 'hightlights_thumbnail',
					'type'    => 'image',
					'label'   => esc_html__( 'Tour Highlights Thumbnail', 'tourfic' ),
					'subtitle'    => esc_html__( 'Please upload an image to be displayed as the Thumbnail for this section.', 'tourfic' ),
					'library' => 'image',
				),
				array(
					'id'         => 'features',
					'type'       => 'select2',
					'multiple'   => true,
					'label'      => esc_html__( 'Select features', 'tourfic' ),
					'subtitle'   => esc_html__( 'For instance, select amenities like a Breakfast, AC Bus, Tour Guide, and more as applicable. You need to create these features from the ', 'tourfic' ) . ' <a href="'.admin_url('edit-tags.php?taxonomy=tour_features&post_type=tf_tours').'" target="_blank"><strong>' . esc_html__( '“Features”', 'tourfic' ) . '</strong></a> tab.',
					'options'    => 'terms',
					'query_args' => array(
						'taxonomy'   => 'tour_features',
						'hide_empty' => false,
					),
					'default'    => 'none',
					'inline_add_new' => true
				),
			),
		),
		// Contact Info
		'contact_info'         => array(
			'title'  => esc_html__( 'Contact Info', 'tourfic' ),
			'icon'   => 'fa-solid fa-address-book',
			'fields' => array(
				array(
					'id'    => 'tour-continfo-heading',
					'type'  => 'heading',
					'label' => 'Contact Info Section',
					'subtitle' => esc_html__( 'How can potential or existing customers reach out for more details about your tour? Please share your contact information here.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-continfo-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-contact-info/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'contact-info-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the Section', 'tourfic' ),
					'subtitle' => esc_html__( 'This text will appear as the heading of the Contact info section on the frontend.', 'tourfic' ),
					'default' => esc_html__("Contact Information", 'tourfic'),
				),
				array(
					'id'          => 'email',
					'type'        => 'text',
					'label'       => esc_html__( 'Email address', 'tourfic' ),
					'subtitle'       => esc_html__( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
				),
				array(
					'id'          => 'phone',
					'type'        => 'text',
					'label'       => esc_html__( 'Phone Number', 'tourfic' ),
					'subtitle'       => esc_html__( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
				),
				array(
					'id'          => 'website',
					'type'        => 'text',
					'label'       => esc_html__( 'Website Url', 'tourfic' ),
					'subtitle'       => esc_html__( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
				),
				array(
					'id'          => 'fax',
					'type'        => 'text',
					'label'       => esc_html__( 'Fax Number', 'tourfic' ),
					'subtitle'       => esc_html__( 'This will be displayed in the Contact Section. Leave it blank if it is not necessary.', 'tourfic' ),
					'field_width' => '50',
				),
			),
		),
		// //  Tour Extra
		'tour_extra'           => array(
			'title'  => esc_html__( 'Tour Extras', 'tourfic' ),
			'icon'   => 'fa-solid fa-route',
			'fields' => array(
				array(
					'id'    => 'tour-extras-heading',
					'type'  => 'heading',
					'label' => 'Offer Tour Extras',
					'subtitle' => esc_html__( 'If you wish to provide additional services that are not included in your current tour package, you can list them here.', 'tourfic' ),
				),
			),
		),

		// // Price
		'price'                => array(
			'title'  => esc_html__( 'Price Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-money-check',
			'fields' => array(
				array(
					'id'    => 'tour-pricing-heading',
					'type'  => 'heading',
					'label' => esc_html__( 'Tour Pricing Settings', 'tourfic' ),
					'subtitle' => esc_html__( 'The pricing of a tour package plays a crucial role. Make sure you set it correctly.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-pricing-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tourfic-price-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'pricing',
					'type'     => 'select',
					'label'    => esc_html__( 'Pricing rule for the Tour', 'tourfic' ),
					'subtitle' => esc_html__( 'Select your pricing logic.', 'tourfic' ),
					'class'    => 'pricing',
					'options'  => [
						'person' => esc_html__( 'Per Person', 'tourfic' ),
					],
					'default'  => 'person',
				),
				array(
					'id'          => 'adult_price',
					'type'        => 'number',
					'label'       => esc_html__( 'Price for Adult', 'tourfic' ),
					'subtitle'    => esc_html__( 'Insert amount only', 'tourfic' ),
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
					'label'       => esc_html__( 'Price for Child', 'tourfic' ),
					'subtitle'    => esc_html__( 'Insert amount only', 'tourfic' ),
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
					'label'       => esc_html__( 'Price for Infant', 'tourfic' ),
					'subtitle'    => esc_html__( 'Insert amount only', 'tourfic' ),
					'attributes'  => array(
						'min' => '0',
					),
					'field_width' => '33.33',
				),
				array(
					'id'       => 'discount_type',
					'type'     => 'select',
					'label'    => esc_html__( 'Discount Type', 'tourfic' ),
					'subtitle' => esc_html__( 'Set a discount for this tour to incentivize bookings. Choose between a fixed amount off or a percentage-based reduction.', 'tourfic' ),
					'options'  => array(
						'none'    => esc_html__( 'None', 'tourfic' ),
						'percent' => esc_html__( 'Percent', 'tourfic' ),
						'fixed'   => esc_html__( 'Fixed', 'tourfic' ),
					),
					'default'  => 'none',
				),
				array(
					'id'         => 'discount_price',
					'type'       => 'number',
					'label'      => esc_html__( 'Discount Price', 'tourfic' ),
					'subtitle'   => esc_html__( 'Insert amount only', 'tourfic' ),
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
					'label'       => esc_html__( 'Disable adult price', 'tourfic' ),
					'field_width' => '33.33',
				),
				array(
					'id'          => 'disable_child_price',
					'type'        => 'switch',
					'label'       => esc_html__( 'Disable children price', 'tourfic' ),
					'field_width' => '33.33',
				),
				array(
					'id'          => 'disable_infant_price',
					'type'        => 'switch',
					'label'       => esc_html__( 'Disable infant price', 'tourfic' ),
					'field_width' => '33.33',
				),
				array(
					'id'      => 'price_deposit',
					'type'    => 'heading',
					'content' => esc_html__( 'Deposit', 'tourfic' ),
				),

			),
		),

		// // Availability
		'availability'         => array(
			'title'  => esc_html__( 'Availability', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard',
			'fields' => array(
				array(
					'id'    => 'tour-availability-heading',
					'type'  => 'heading',
					'label' => 'Tour Availability Settings',
					'subtitle' => esc_html__( 'This section provides crucial information on the dates and times when the tour is open for booking.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-availablity-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-availability/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'type',
					'type'     => 'select',
					'label'    => esc_html__( 'Tour Type', 'tourfic' ),
					'subtitle' => esc_html__( 'Continuous: The package will be available every month within the mentioned range. Fixed: The Tour package will be available on a fixed date. ', 'tourfic' ),
					'class'    => 'tour-type',
					'options'  => [
						'continuous' => esc_html__( 'Continuous', 'tourfic' ),
					],
					'default'  => 'continuous',

				),
				/**
				 * Custom: No
				 *
				 * Continuous Availability
				 */
				array(
					'id'          => 'cont_min_people',
					'type'        => 'number',
					'label'       => esc_html__( 'Minimum Person (Required for Search)', 'tourfic' ),
					'subtitle'    => esc_html__( 'Specify the minimum person required to book this tour.', 'tourfic' ),
					'dependency'  => array(
						array( 'type', '==', 'continuous' ),
					),
					'default'     => 1,
					'field_width' => '50',
				),
				array(
					'id'          => 'cont_max_people',
					'type'        => 'number',
					'label'       => esc_html__( 'Maximum Person (Required for Search)', 'tourfic' ),
					'subtitle'    => esc_html__( 'Indicate the maximum number of persons this package can be booked for each booking.', 'tourfic' ),
					'dependency'  => array(
						array( 'type', '==', 'continuous' ),
					),
					'field_width' => '50',
				),
				array(
					'id'          => 'cont_max_capacity',
					'type'        => 'number',
					'label'       => esc_html__( 'Maximum Capacity', 'tourfic' ),
					'subtitle'    => esc_html__( 'Indicate the maximum number of people (including adults and children) allowed per day for this tour.', 'tourfic' ),
					'dependency'  => array(
						array( 'type', '==', 'continuous' ),
					),
				),
				array(
					'id'         => 'Disabled_Dates',
					'type'       => 'heading',
					'content'    => esc_html__( 'Disable Days & Dates', 'tourfic' ),
					'dependency' => array(
						array( 'type', '==', 'continuous' ),
					),
				),
				array(
					'id'           => 'disable_range',
					'type'         => 'repeater',
					'label'        => esc_html__( 'Disable Date Range', 'tourfic' ),
					'button_title' => esc_html__( 'Add New Date', 'tourfic' ),
					'max'          => 2,
					'dependency'   => array(
						array( 'type', '==', 'continuous' ),
					),
					'field_title'  => 'date',
					'fields'       => array(

						array(
							'id'         => 'date',
							'type'       => 'date',
							'label'      => esc_html__( 'Select date range', 'tourfic' ),
							'subtitle'    => esc_html__( 'Specify the date range when this tour will be unavailable for booking.', 'tourfic' ),
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

			),
		),

		// // Booking
		'booking'              => array(
			'title'  => esc_html__( 'Booking', 'tourfic' ),
			'icon'   => 'fa-solid fa-person-walking-luggage',
			'fields' => array(
				array(
					'id'    => 'tour-booking-heading',
					'type'  => 'heading',
					'label' => 'Booking Settings',
					'subtitle' => esc_html__( 'This section offers the option to customize the booking process for your tours.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-booking-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/booking/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'        => 'disable_same_day',
					'type'      => 'switch',
					'label'     => esc_html__( 'Do you want to disable same-day booking?', 'tourfic' ),
					'subtitle'  => esc_html__( 'If enabled,  the tour cannot be booked on the same day.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),
				array(
					'id'      => 'booking-by',
					'type'    => 'select',
					'label'   => esc_html__( 'Booking Type', 'tourfic' ),
					'subtitle'  => esc_html__( 'Choose the type of booking you would like to implement for this tour.', 'tourfic' ),
					'options' => array(
						'1' => esc_html__( 'Default Booking (WooCommerce)', 'tourfic' ),
					),
					'default' => '1',
				),
				array(
					'id'       => 'single_tour_booking_form_button_text',
					'type'     => 'text',
					'label'    => esc_html__( 'Change Booking Form Button Text', 'tourfic' ),
					'subtitle'  => esc_html__( 'With this option, you can change the text of the booking form button for this tour', 'tourfic' ),
					'default'    => esc_html__('Book Now', 'tourfic'),
				),

				array(
					'id'    => 'tour-cancellation-heading',
					'type'  => 'heading',
					'label' => 'Cancellation Condition',
					'subtitle' => esc_html__( 'Define and customize booking cancellation policies for your offerings. This section allows you to set different cancellation rules, such as timeframes for free cancellations, partial refunds, or no refunds.', 'tourfic' ),
				),

			),
		),
		// // Exclude/Include
		'exclude_Include'      => array(
			'title'  => esc_html__( 'Include & Exclude', 'tourfic' ),
			'icon'   => 'fa-solid fa-square-check',
			'fields' => array(
				array(
					'id'    => 'tour-inc-heading',
					'type'  => 'heading',
					'label' => 'Include & Exclude Section',
					'subtitle' => esc_html__( 'Each tour includes certain items, while others are not part of the package. Clearly define these inclusions and exclusions to prevent any misunderstandings during your tour.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-inc-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-include-exclude/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'           => 'inc',
					'type'         => 'repeater',
					'label'        => esc_html__( 'Items Included', 'tourfic' ),
					'subtitle'     => esc_html__( 'Add all the items/features included in this tour package.', 'tourfic' ),
					'button_title' => esc_html__( 'Add New Include', 'tourfic' ),
					'field_title'  => 'inc',
					'fields'       => array(
						array(
							'id'    => 'inc',
							'type'  => 'text',
							'label' => esc_html__( 'Insert your item', 'tourfic' ),
						),
					),
				),
				array(
					'id'       => 'inc_icon',
					'type'     => 'icon',
					'label'    => esc_html__( 'Icon for Included Item', 'tourfic' ),
					'subtitle' => esc_html__( 'Choose icon', 'tourfic' ),
				),
				array(
					'id'           => 'exc',
					'type'         => 'repeater',
					'label'        => esc_html__( 'Items Excluded', 'tourfic' ),
					'subtitle'        => esc_html__( 'List all the items/features excluded in this tour package.', 'tourfic' ),
					'button_title' => esc_html__( 'Add New Exclude', 'tourfic' ),
					'field_title'  => 'exc',
					'fields'       => array(
						array(
							'id'    => 'exc',
							'type'  => 'text',
							'label' => esc_html__( 'Insert your item', 'tourfic' ),
						),
					),
				),
				array(
					'id'       => 'exc_icon',
					'type'     => 'icon',
					'label'    => esc_html__( 'Icon for Excluded item', 'tourfic' ),
					'subtitle' => esc_html__( 'Choose icon', 'tourfic' ),
				),
				array(
					'id'      => 'include-exclude-bg',
					'type'    => 'image',
					'label'   => esc_html__( 'Background Image', 'tourfic' ),
					'subtitle'    => esc_html__( 'This will be added as the background image of this section.', 'tourfic' ),
					'library' => 'image',
				),
			),
		),

		// // Itinerary
		'itinerary'            => array(
			'title'  => esc_html__( 'Itinerary Builder', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-list',
			'fields' => array(
				array(
					'id'    => 'tour-itibuilder-heading',
					'type'  => 'heading',
					'label' => 'Tour Itinerary Builder',
					'subtitle' => esc_html__( 'Create a detailed schedule for a tour. This builder allows for the organization of various components of a tour into a coherent and structured timeline.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-itibuilder-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tourfic-itinerary-builder/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'itinerary-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the section', 'tourfic' ),
					'subtitle' => esc_html__( 'This text will appear as the heading of the Tour Itinerary section on the frontend.', 'tourfic' ),
					'default' => esc_html__("Travel Itinerary", 'tourfic'),
				),
				array(
					'id'           => 'itinerary',
					'type'         => 'repeater',
					'button_title' => esc_html__( 'Add New Itinerary', 'tourfic' ),
					'label'        => esc_html__( 'Create your Travel Itinerary', 'tourfic' ),
					'field_title'  => 'title',
					'fields'       => array(
						array(
							'id'          => 'time',
							'type'        => 'text',
							'label'       => esc_html__( 'Time or Day', 'tourfic' ),
							'subtitle'    => esc_html__( 'e.g. Day 1 or 9:00 am', 'tourfic' ),
							'field_width' => '50',
						),
						array(
							'id'          => 'title',
							'type'        => 'text',
							'label'       => esc_html__( 'Itinerary Title', 'tourfic' ),
							'subtitle'    => esc_html__( 'Input the title here', 'tourfic' ),
							'field_width' => '50',
						),
						array(
							'id'           => 'image',
							'type'         => 'image',
							'label'        => esc_html__( 'Upload Image for the Description', 'tourfic' ),
							'library'      => 'image',
							'placeholder'  => 'http://',
							'button_title' => esc_html__( 'Add Image', 'tourfic' ),
							'remove_title' => esc_html__( 'Remove Image', 'tourfic' ),
						),
						array(
							'id'    => 'desc',
							'type'  => 'editor',
							'label' => esc_html__( 'Itinerary Description', 'tourfic' ),
						),
					),
				),
				array(
					'id'      => 'itinerary-downloader-settings',
					'type'    => 'heading',
					'label' => esc_html__( 'Itinerary Downloader Settings', 'tourfic' ),
					'subtitle' => esc_html__( 'These are some additional settings specific to the Itinerary PDF downloader. Note that some of these settings may override the global settings. ', 'tourfic' ),
				),

			),
		),

		// FAQs
		'faqs'                 => array(
			'title'  => esc_html__( 'FAQ Section', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'    => 'tour-faq-heading',
					'type'  => 'heading',
					'label' => 'FAQ Section',
					'subtitle' => esc_html__( 'This section is designed to help users find answers to common questions.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-faq-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-faqs/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'faq-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the Section', 'tourfic' ),
					'subtitle' => esc_html__( 'This text will appear as the heading of the FAQ section on the frontend.', 'tourfic' ),
					'default' => "Frequently Asked Questions"
				),
				array(
					'id'           => 'faqs',
					'type'         => 'repeater',
					'label'        => esc_html__( 'Add Your Questions', 'tourfic' ),
					'subtitle' 	   => esc_html__( 'Click the button below to add Frequently Asked Questions (FAQs) for your tour. Feel free to add as many as needed. Additionally, you can duplicate or rearrange each FAQ using the icons on the right side.', 'tourfic' ),
					'button_title' => esc_html__( 'Add New FAQ', 'tourfic' ),
					'field_title'  => 'title',
					'fields'       => array(
						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => esc_html__( 'Single FAQ Title', 'tourfic' ),
						),
						array(
							'id'    => 'desc',
							'type'  => 'editor',
							'label' => esc_html__( 'Single FAQ Description', 'tourfic' ),
						),
					),
				),
			),
		),
		// Tour Enquiry
		't_enquiry'  => array(
			'title'  => esc_html__( 'Tour Enquiry', 'tourfic' ),
			'icon'   => 'fa-solid fa-question-circle',
			'fields' => array(
				array(
					'id'      => 'enquiry-section',
					'type'    => 'heading',
					'content' => esc_html__( 'Tour Enquiry Form', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'        => 't-enquiry-section',
					'type'      => 'switch',
					'label'     => esc_html__( 'Enable Tour Enquiry', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => true
				),
				array(
					'id'       => 't-enquiry-option-icon',
					'type'     => 'icon',
					'label'    => esc_html__( 'Tour Enquiry icon', 'tourfic' ),
					'subtitle' => esc_html__( 'Choose icon', 'tourfic' ),
					'default'  => 'fa fa-question-circle-o',
					'dependency' => array( 't-enquiry-section', '==', '1' ),
				),
				array(
					'id'    => 't-enquiry-option-title',
					'type'  => 'text',
					'label' => esc_html__( 'Enquiry Title', 'tourfic' ),
					'default' => "Have a question in mind",
					'dependency' => array( 't-enquiry-section', '==', '1' ),
				),
				array(
					'id'    => 't-enquiry-option-content',
					'type'  => 'text',
					'label' => esc_html__( 'Enquiry Description', 'tourfic' ),
					'default' => "Looking for more info? Send a question to the tour agent to find out more.",
					'dependency' => array( 't-enquiry-section', '==', '1' ),
				),
				array(
					'id'    => 't-enquiry-option-btn',
					'type'  => 'text',
					'label' => esc_html__( 'Enquiry Button Text', 'tourfic' ),
					'default' => "Ask a Question",
					'dependency' => array( 't-enquiry-section', '==', '1' ),
				),
			),
		),

		// Multiple tags for tours
		'tours_multiple_tags' => array(
			'title'  => esc_html__( 'Promotional Tags', 'tourfic' ),
			'icon'   => 'fa fa-list',
			'fields' => array(
				array(
					'id'      => 'tf-tour-tags-heading',
					'type'    => 'heading',
					'label' => esc_html__( 'Tour tags', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'           => 'tf-tour-tags',
					'type'         => 'repeater',
					'label'        => esc_html__( 'Promotional Tags', 'tourfic' ),
					'subtitle' => esc_html__('Add some keywords that highlight your tour\'s Unique Selling Point (USP). This tag will be displayed on both the Archive Page and the Search Results Page.', 'tourfic'),
					'button_title' => esc_html__( 'Add / Insert New Tag', 'tourfic' ),
					'field_title'  => 'tour-tag-title',
					'fields'       => array(

						array(
							'id'    => 'tour-tag-title',
							'type'  => 'text',
							'label' => esc_html__( 'Tag Title', 'tourfic' ),
						),

						array(
							'id'       => 'tour-tag-color-settings',
							'type'     => 'color',
							'class'    => 'tf-label-field',
							'label'    => esc_html__( 'Tag Colors', 'tourfic' ),
							'subtitle' => esc_html__( 'Colors of Tag Background and Font', 'tourfic' ),
							'multiple' => true,
							'inline'   => true,
							'colors'   => array(
								'background' => esc_html__( 'Background', 'tourfic' ),
								'font'   => esc_html__( 'Font', 'tourfic' ),
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
			'title'  => esc_html__( 'Terms & Conditions', 'tourfic' ),
			'icon'   => 'fa-regular fa-square-check',
			'fields' => array(
				array(
					'id'    => 'tour-tnc-heading',
					'type'  => 'heading',
					'label' => 'Terms & Conditions Section',
					'subtitle' => esc_html__( 'Include your set of regulations and guidelines that customers must agree to in order to use the service provided in your tour package. ', 'tourfic' ),
				),
				array(
					'id'      => 'tour-tnc-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-terms-conditions/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'tc-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the Section', 'tourfic' ),
					'subtitle' => esc_html__( 'This text will appear as the heading of the T&C section on the frontend.', 'tourfic' ),
					'default' => esc_html__( "Tour Terms & Conditions", 'tourfic' ),
				),
				array(
					'id'    => 'terms_conditions',
					'type'  => 'editor',
					'label' => esc_html__( 'Terms & Conditions of this tour', 'tourfic' ),
					'subtitle' => esc_html__( "Enter your tour's terms and conditions in the text editor provided below.", 'tourfic' ),
				),
			),
		),


		// // Settings
		'settings'             => array(
			'title'  => esc_html__( 'Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-viruses',
			'fields' => array(
				array(
					'id'    => 'settings_headding',
					'type'  => 'heading',
					'label' =>  esc_html__('Other Settings', 'tourfic' ),
					'subtitle' => esc_html__( 'These are some additional settings specific to this Tour Package. Note that some of these settings may override the global settings. ', 'tourfic' ),
				),
				array(
					'id'      => 'tour-setting-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tours/tour-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'        => 't-review',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Review Section', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false
				),
				array(
					'id'        => 't-share',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Share Option', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false
				),
				array(
					'id'        => 't-wishlist',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Wishlist Option', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false
				),
				array(
					'id'        => 't-related',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Related Tour Section', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false
				),
				array(
					'id'          => 'require_adult_child_booking',
					'type'        => 'switch',
					'label'       => esc_html__( 'Adult Required for Child Booking', 'tourfic' ),
					'subtitle'       => esc_html__( 'By enabling this option, an adult will be required when booking a child. By default, an adult is required for infant bookings.', 'tourfic' ),
					'field_width' => '50%',
					'default' => 0
				),

				array(
					'id'      => 'tour-booking-section',
					'type'    => 'heading',
					'content' => esc_html__( 'Titles / Heading of Different Sections', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'    => 'booking-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the Booking Section', 'tourfic' ),
					'default' => esc_html__("Book This Tour", 'tourfic'),
				),
				array(
					'id'    => 'description-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the Description Section', 'tourfic' ),
					'default' => esc_html__("Description", 'tourfic'),
				),

				array(
					'id'    => 'tour-features-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the Features Section', 'tourfic' ),
					'default' => esc_html__("Popular Features", 'tourfic'),
				),
				
				array(
					'id'    => 'map-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the Map Section', 'tourfic' ),
					'default' => esc_html__("Maps", 'tourfic'),
				),
				array(
					'id'    => 'review-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the Reviews Section', 'tourfic' ),
					'default' => esc_html__( 'Average Guest Reviews', 'tourfic' ),
				),
			),
		),
	),
) );
