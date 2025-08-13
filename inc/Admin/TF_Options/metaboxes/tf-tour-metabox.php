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
					'title' => esc_html__( 'General Settings', 'tourfic' ),
					'content' => esc_html__( 'These are some common settings specific to this Tour Package.', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/tourfic-hotel-general-settings/')
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
					'title' => esc_html__( 'Location Settings', 'tourfic' ),
					'content' => esc_html__( 'The location of a tour is a crucial element for every tour package. Set your tour locations in this section.', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/tour-location-settings/')
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
					'title' => esc_html__( 'Tour Information Section', 'tourfic' ),
					'content' => esc_html__( 'Ensure to furnish customers with all the essential information they need to fully understand your tour package.', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/tour-information/')
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
					'id'          => 'language',
					'type'        => 'text',
					'label'       => __( 'Tour Languages', 'tourfic' ),
					'subtitle'    => __( 'Include multiple languages separated by comma (,)', 'tourfic' ),
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
					'id'    => 'highlights-sections',
					'type'  => 'heading',
					'title' => esc_html__( 'Description Icons', 'tourfic' ),
				),
				array(
					'id'       => 'tf-tour-duration-icon',
					'type'     => 'icon',
					'label'    => __( 'Tour Duration icon', 'tourfic' ),
					'subtitle' => __( 'Choose icon', 'tourfic' ),
					'default'  => 'ri-history-line',
					'field_width' => '33',
				),
				array(
					'id'       => 'tf-tour-group-icon',
					'type'     => 'icon',
					'label'    => __( 'Tour Group Size icon', 'tourfic' ),
					'subtitle' => __( 'Choose icon', 'tourfic' ),
					'default'  => 'ri-team-line',
					'field_width' => '33',
				),
				array(
					'id'       => 'tf-tour-lang-icon',
					'type'     => 'icon',
					'label'    => __( 'Tour Language icon', 'tourfic' ),
					'subtitle' => __( 'Choose icon', 'tourfic' ),
					'default'  => 'ri-global-line',
					'field_width' => '33',
				),
				array(
					'id'    => 'highlights-sections',
					'type'  => 'heading',
					'title' => esc_html__( 'Tour Highlights', 'tourfic' ),
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
					'id'         => 'features',
					'type'       => 'select2',
					'multiple'   => true,
					'label'      => __( 'Select features', 'tourfic' ),
					'subtitle'   => __( 'For instance, select amenities like a Breakfast, AC Bus, Tour Guide, and more as applicable. You need to create these features from the ', 'tourfic' ) . ' <a href="'.admin_url('edit-tags.php?taxonomy=tour_features&post_type=tf_tours').'" target="_blank"><strong>' . __( '“Features”', 'tourfic' ) . '</strong></a> tab.',
					'options'    => 'terms',
					'query_args' => array(
						'taxonomy'   => 'tour_features',
						'hide_empty' => false,
					),
					'default'    => 'none',
					'inline_add_new' => true
				),
				array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'We\'re offering some extra features like <b>tour type</b> in our pro plan. <a href="https://tourfic.com/" target="_blank"> Upgrade to our pro package today to take advantage of these fantastic options!</a>', 'tourfic' ) ),
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
					'title' => esc_html__( 'Contact Info Section', 'tourfic' ),
					'content' => esc_html__( 'How can potential or existing customers reach out for more details about your tour? Please share your contact information here.', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/tour-contact-info/')
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
					'title' => esc_html__( 'Offer Tour Extras', 'tourfic' ),
					'content' => esc_html__( 'If you wish to provide additional services that are not included in your current tour package, you can list them here.', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/tour-extra/')
				),
				array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'Are you interested in enriching your tour offerings with exciting services? With our Pro package, you can easily add exciting activities such as paragliding, along with meals and hotel accommodations, through our <b>Tour Extra Services</b>. This feature allows you to customize and expand your services as much as you want, providing a better experience for your customers. <a href="https://tourfic.com/" target="_blank">Upgrade to our Pro package today to take advantage of these fantastic options!</a>', 'tourfic' ) ),
				),
			),
		),

		// // Price
		'price'                => array(
			'title'  => __( 'Pricing', 'tourfic' ),
			'icon'   => 'fa-solid fa-money-check',
			'fields' => array(
				array(
					'id'    => 'tour-pricing-heading',
					'type'  => 'heading',
					'title' => esc_html__( 'Pricing', 'tourfic' ),
					'content' => esc_html__( 'Smart pricing attracts more guests. Configure packages, discounts, and deposits below.', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/tourfic-price-settings/')
				),
				array(
					'id'       => 'pricing',
					'type'     => 'select',
					'label'    => esc_html__( 'How will you charge guests?', 'tourfic' ),
					'subtitle' => esc_html__('Best for fixed itineraries', 'tourfic'),
					'class'    => 'pricing',
					'options'  => [
						'person' => __( 'Per Person', 'tourfic' ),
					],
					'default'  => 'person',
					'attributes' => array(
						'class' => 'tf_tour_pricing_type',
					),
				),
				array(
					'id'          => 'disable_adult_price',
					'type'        => 'switch',
					'label'       => esc_html__( 'Adult Disable', 'tourfic' ),
					'field_width' => 33.33,
				),
				array(
					'id'          => 'disable_child_price',
					'type'        => 'switch',
					'label'       => esc_html__( 'Child Disable', 'tourfic' ),
					'field_width' => 33.33,
				),
				array(
					'id'          => 'disable_infant_price',
					'type'        => 'switch',
					'label'       => esc_html__( 'Infant Disable', 'tourfic' ),
					'field_width' => 33.33,
				),
				array(
					'id'          => 'adult_price',
					'type'        => 'number',
					'description'    => esc_html__( 'Type number only, ex. 250', 'tourfic' ),
					'dependency'  => [
						array( 'pricing', '==', 'person' ),
						[ 'disable_adult_price', '==', 'false' ]
					],
					'attributes'  => array(
						'min' => '0',
					),
					'field_width' => 33.33,
				),
				array(
					'id'          => 'child_price',
					'type'        => 'number',
					'dependency'  => [
						array( 'pricing', '==', 'person' ),
						[ 'disable_child_price', '==', 'false' ]
					],
					'description'    => esc_html__( 'Type number only, ex. 200', 'tourfic' ),
					'attributes'  => array(
						'min' => '0',
					),
					'field_width' => 33.33,
				),
				array(
					'id'          => 'infant_price',
					'type'        => 'number',
					'dependency'  => [
						array( 'pricing', '==', 'person' ),
						[ 'disable_infant_price', '==', 'false' ],
						[ 'disable_adult_price', '==', 'false' ],
					],
					'description'    => esc_html__( 'Type number only, ex. 150', 'tourfic' ),
					'attributes'  => array(
						'min' => '0',
					),
					'field_width' => 33.33,
				),
				array(
					'id'         => 'min_person',
					'type'       => 'number',
					'icon'		 => 'fa-regular fa-user',
					'label'      => esc_html__( 'Number of Persons', 'tourfic' ),
					'placeholder' => esc_html__( 'Min', 'tourfic' ),
					'attributes' => array(
						'min' => '0',
					),
					'range'   => true,
					'related_name' => 'max_person',
					'related_placeholder' => esc_html__( 'Max', 'tourfic' ),
					'description' => esc_html__('Indicate the minimum and maximum number of persons this package can be booked for each booking.','tourfic'),
					'dependency' => array( 'pricing', '!=', 'package' ),
				),
				array(
					'id'      => 'allow_discount',
					'type'    => 'switch',
					'label'   => esc_html__( 'Discount', 'tourfic' ),
					'description' => esc_html__( 'Limited-time offers work best! Use discounts for early birds, groups, or last-minute deals.', 'tourfic' ),
				),
				array(
					'id'         => 'discount_price',
					'type'       => 'number',
					'description'   => esc_html__( 'Enter value (e.g., ‘50’ for $50 off or 10% off)', 'tourfic' ),
					'attributes' => array(
						'min' => '0',
					),
					'placeholder' => esc_html__('Amount', 'tourfic'),
					'dependency'  => array( 'allow_discount', '!=', 'false' ),
					'related'   => true,
					'related_name' => 'discount_type',
					'related_options'  => array(
						'percent' => esc_html__( 'Percent', 'tourfic' ),
						'fixed'   => esc_html__( 'Fixed', 'tourfic' ),
					),
				),
				array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'Are you interested in collecting upfront or partial payments for your tours? Our <b>deposit option</b> allows you to set an upfront payment, either as a <b>percentage</b> or a <b>fixed</b> amount, which travelers can pay at the time of booking. Our pro package also include <b>group pricing</b> for a group of travelers. <a href="https://tourfic.com/" target="_blank">Upgrade to our Pro package today to take advantage of this fantastic option!</a>', 'tourfic' ) ),
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
					'title' => esc_html__( 'Availability', 'tourfic' ),
					'content' => esc_html__( 'Set up your tour schedule so customers know exactly when they can book.', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/tour-availability/')
				),
				array(
					'id'       => 'type',
					'type'     => 'select',
					'label'    => esc_html__( 'How often does this Tour run?', 'tourfic' ),
					'description' => esc_html__( "Continuous: runs regularly (e.g., daily whale watching). Fixed: Specific dates only (e.g., New Year's cruise)", 'tourfic' ),
					'class'    => 'tour-type',
					'options'  => [
						'continuous' => __( 'Continuous', 'tourfic' ),
					],
					'default'  => 'continuous',
					'attributes' => array(
						'class' => 'tf_tour_avail_type',
					),
				),
				array(
					'id'         => 'tour_availability',
					'type'       => 'tourAvailabilityCal',
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
					'title' => esc_html__( 'Booking Settings', 'tourfic' ),
					'content' => esc_html__( 'This section offers the option to customize the booking process for your tours.', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/booking/')
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
					'id'      => 'booking-by',
					'type'    => 'select',
					'label'   => __( 'Booking Type', 'tourfic' ),
					'subtitle'  => __( 'Choose the type of booking you would like to implement for this tour.', 'tourfic' ),
					'options' => array(
						'1' => __( 'Default Booking (WooCommerce)', 'tourfic' ),
					),
					'default' => '1',
				),
				array(
					'id'       => 'single_tour_booking_form_button_text',
					'type'     => 'text',
					'label'    => __( 'Change Booking Form Button Text', 'tourfic' ),
					'subtitle'  => __( 'With this option, you can change the text of the booking form button for this tour', 'tourfic' ),
					'default'    => __('Book Now', 'tourfic'),
				),
				array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'We\'re offering some extra features like <b>minimum days for booking</b>, <b>enable traveler info</b>, <b>external booking</b>, <b>booking without payment</b>, <b>taxable tour</b>, <b>tax class for Woocommerce</b> in our pro plan. <a href="https://tourfic.com/" target="_blank">Upgrade to our pro package today to take advantage of these fantastic options!</a>', 'tourfic' ) ),
				),

				array(
					'id'    => 'tour-cancellation-heading',
					'type'  => 'heading',
					'title' => esc_html__( 'Cancellation Condition', 'tourfic' ),
					'content' => esc_html__( 'Define and customize booking cancellation policies for your offerings. This section allows you to set different cancellation rules, such as timeframes for free cancellations, partial refunds, or no refunds.', 'tourfic' ),
				),
				array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'We\'re offering some extra features like <b>booking cancellation</b> in our pro plan. <a href="https://tourfic.com/" target="_blank">Upgrade to our pro package today to take advantage of these fantastic options!</a>', 'tourfic' )),
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
					'title' => esc_html__( 'Include & Exclude Section', 'tourfic' ),
					'content' => esc_html__( 'Each tour includes certain items, while others are not part of the package. Clearly define these inclusions and exclusions to prevent any misunderstandings during your tour.', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/tour-include-exclude/')
				),
				array(
					'id'           => 'inc',
					'type'         => 'repeater',
					'label'        => __( 'Items Included', 'tourfic' ),
					'subtitle'     => __( 'Add all the items/features included in this tour package.', 'tourfic' ),
					'button_title' => __( 'Add New Include', 'tourfic' ),
					'field_title'  => 'inc',
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
					'field_title'  => 'exc',
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
			'title'  => __( 'Itenerary', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-list',
			'fields' => array(
				array(
					'id'    => 'tour-itibuilder-heading',
					'type'  => 'heading',
					'title' => esc_html__( 'Tour Itinerary Builder', 'tourfic' ),
					'content' => esc_html__( 'Create a detailed schedule for a tour. This builder allows for the organization of various components of a tour into a coherent and structured timeline.', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/tourfic-itinerary-builder/')
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
					'field_title'  => 'title',
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
					),
				),
				array(
					'id'      => 'itinerary-downloader-settings',
					'type'    => 'heading',
					'title' => __( 'Itinerary Downloader Settings', 'tourfic' ),
					'description' => __( 'These are some additional settings specific to the Itinerary PDF downloader. Note that some of these settings may override the global settings. ', 'tourfic' ),
				),
				array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'We\'re offering some advanced itinerary features inside every repeater like <b>duration</b>, <b>itinerary gallery</b>, <b>custom itinerary options</b>, <b>meals</b>, <b>location</b>, <b>altitude</b>. Also our pro package includes a feature that allows customers to <b>download</b> the detailed itinerary directly from the frontend as a <b>PDF</b>. This enhancement is perfect for travelers offline access or who wish to share their itinerary with others. <a href="https://tourfic.com/" target="_blank">Upgrade to our pro package today to take advantage of these fantastic option!</a>', 'tourfic') ),
				),
			),
		),

		// FAQs
		'faqs'                 => array(
			'title'  => __( 'FAQ', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'    => 'tour-faq-heading',
					'type'  => 'heading',
					'title' => esc_html__( 'FAQ Section', 'tourfic' ),
					'content' => esc_html__( 'This section is designed to help users find answers to common questions.', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/tour-faqs/')
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
					'field_title'  => 'title',
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
			'title'  => __( 'Enquiry', 'tourfic' ),
			'icon'   => 'fa-solid fa-question-circle',
			'fields' => array(
				array(
					'id'      => 'enquiry-section',
					'type'    => 'heading',
					'title' => __( 'Tour Enquiry Form', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'        => 't-enquiry-section',
					'type'      => 'switch',
					'label'     => __( 'Enable Tour Enquiry', 'tourfic' ),
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
			'title'  => __( 'Promotional Tags', 'tourfic' ),
			'icon'   => 'fa fa-list',
			'fields' => array(
				array(
					'id'      => 'tf-tour-tags-heading',
					'type'    => 'heading',
					'title' => __( 'Tour tags', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'           => 'tf-tour-tags',
					'type'         => 'repeater',
					'label'        => __( 'Promotional Tags', 'tourfic' ),
					'subtitle' => __('Add some keywords that highlight your tour\'s Unique Selling Point (USP). This tag will be displayed on both the Archive Page and the Search Results Page.', 'tourfic'),
					'button_title' => __( 'Add / Insert New Tag', 'tourfic' ),
					'field_title'  => 'tour-tag-title',
					'fields'       => array(

						array(
							'id'    => 'tour-tag-title',
							'type'  => 'text',
							'label' => __( 'Tag Title', 'tourfic' ),
						),

						array(
							'id'       => 'tour-tag-color-settings',
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

		// Terms & Conditions
		'terms_and_conditions' => array(
			'title'  => __( 'Policy', 'tourfic' ),
			'icon'   => 'fa-regular fa-square-check',
			'fields' => array(
				array(
					'id'    => 'tour-tnc-heading',
					'type'  => 'heading',
					'title' => 'Terms & Conditions Section',
					'description' => __( 'Include your set of regulations and guidelines that customers must agree to in order to use the service provided in your tour package. ', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/tour-terms-conditions/')
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
					'title' =>  esc_html__('Other Settings', 'tourfic' ),
					'content' => esc_html__( 'These are some additional settings specific to this Tour Package. Note that some of these settings may override the global settings. ', 'tourfic' ),
					'docs' => esc_url('https://themefic.com/docs/tourfic/tours/tour-settings/')
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
					'id'          => 'require_adult_child_booking',
					'type'        => 'switch',
					'label'       => __( 'Adult Required for Child Booking', 'tourfic' ),
					'subtitle'       => __( 'By enabling this option, an adult will be required when booking a child. By default, an adult is required for infant bookings.', 'tourfic' ),
					'field_width' => '50%',
					'default' => 0
				),

				array(
					'id'      => 'tour-booking-section',
					'type'    => 'heading',
					'title' => __( 'Titles / Heading of Different Sections', 'tourfic' ),
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
					'id'    => 'tour-features-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the Features Section', 'tourfic' ),
					'default' => __("Popular Features", 'tourfic'),
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
