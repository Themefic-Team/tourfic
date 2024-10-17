<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

$badge_up     = '<div class="tf-csf-badge"><span class="tf-upcoming">' . esc_html__( "Upcoming", "tourfic" ) . '</span></div>';
$badge_pro    = '<div class="tf-csf-badge"><span class="tf-pro">' . esc_html__( "Pro Feature", "tourfic" ) . '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' . esc_html__( "Upcoming", "tourfic" ) . '</span><span class="tf-pro">' . esc_html__( "Pro Feature", "tourfic" ) . '</span></div>';

if(!function_exists('tf_hotel_facilities_categories')) {
	function tf_hotel_facilities_categories() {
		$facilities_cats = ! empty( Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) : '';
		$all_cats       = [];
		if ( ! empty( $facilities_cats ) && is_array( $facilities_cats ) ) {
			foreach ( $facilities_cats as $key => $cat ) {
				$all_cats[ (string) $key ] = $cat['hotel_facilities_cat_name'];
			}
		}

		if(empty($all_cats)){
			$all_cats[''] = esc_html__( 'Select Category', 'tourfic' );
		}

		return $all_cats;
	}
}

TF_Metabox::metabox( 'tf_hotels_opt', array(
	'title'     => 'Hotel Settings',
	'post_type' => 'tf_hotel',
	'sections'  => array(
		'general' => array(
			'title'  => esc_html__( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'hotel-general-heading',
					'type'  => 'heading',
					'label' => 'General Settings',
					'subtitle' => esc_html__( 'These are some common settings specific to this Hotel.', 'tourfic' ),
				),
				array(
					'id'      => 'hotel-general-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/add-new-hotel/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'        => 'featured',
					'type'      => 'switch',
					'label'     => esc_html__( 'Featured Hotel', 'tourfic' ),
					'subtitle' => esc_html__( 'Enable this option to feature this hotel at the top of search results.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false,
				),
				array(
					'id'          => 'featured_text',
					'type'        => 'text',
					'label'       => esc_html__( 'Hotel Featured Text', 'tourfic' ),
					'subtitle'    => esc_html__( 'Enter Featured Hotel Text', 'tourfic' ),
					'placeholder' => esc_html__( 'Enter Featured Hotel Text', 'tourfic' ),
					'default' => esc_html__( 'Hot Deal', 'tourfic' ),
					'dependency'  => array( 'featured', '==', true ),
				),
				array(
					'id'       => 'tf_single_hotel_layout_opt',
					'type'     => 'select',
					'label'    => esc_html__( 'Single Hotel Template Settings', 'tourfic' ),
					'subtitle' => esc_html__( 'You can keep the Global Template settings or choose a different layout for this hotel.', 'tourfic' ),
					'options'  => [
						'global' => esc_html__( 'Global Settings', 'tourfic' ),
						'single' => esc_html__( 'Single Settings', 'tourfic' ),
					],
					'default'  => 'global',
				),
				array(
					'id'       => 'tf_single_hotel_template',
					'type'     => 'imageselect',
					'label'    => esc_html__( 'Single Hotel Page Layout', 'tourfic' ),
					'multiple' 		=> true,
					'inline'   		=> true,
					'options'   	=> array(
						'design-1' 				=> array(
							'title'			=> 'Design 1',
							'url' 			=> TF_ASSETS_ADMIN_URL."images/template/design1-hotel.jpg",
						),
						'design-2' 				=> array(
							'title'			=> 'Design 2',
							'url' 			=> TF_ASSETS_ADMIN_URL."images/template/design2-hotel.jpg",
						),
						'default' 			=> array(
							'title'			=> 'Defult',
							'url' 			=> TF_ASSETS_ADMIN_URL."images/template/default-hotel.jpg",
						),
					),
					'default'   	=> 'design-1',
					'dependency'  => [
						array( 'tf_single_hotel_layout_opt', '==', 'single' )
					],
				),
				array(
					'id'      => 'Booking-Type',
					'type'    => 'heading',
					'content' => esc_html__( 'Booking Settings', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'      => 'booking-by',
					'type'    => 'select',
					'label'   => esc_html__( 'Booking Type', 'tourfic' ),
					'options' => array(
						'1' => esc_html__( 'Default Booking (WooCommerce)', 'tourfic' ),
					),
					'default' => '1',
				),
				array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'We\'re offering some additional features like <b>external booking</b>, <b>taxable hotel</b>, <b>tax class for Woocommerce</b> in our pro plan. The external booking option provides seamless integration with external booking systems, enhancing your booking capabilities significantly. <a href="https://tourfic.com/" target="_blank">Upgrade to our pro package today to take advantage of this fantastic option!</a>', 'tourfic' ) ),
				),
			),
		),
		'location'         => array(
			'title'  => esc_html__( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'    => 'hotel-location-heading',
					'type'  => 'heading',
					'label' => 'Location Settings',
					'subtitle' => esc_html__( 'The location of a hotel is a crucial element for every booking. Set your hotel locations in this section.', 'tourfic' ),
				),
				array(
					'id'      => 'hotel-location-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/hotel-location/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'map',
					'class'    => 'gmaps',
					'type'     => 'map',
					'label'    => esc_html__( 'Dynamic Location Search', 'tourfic' ),
					/* translators: %s is the link to the Google Maps API Key settings */
					'subtitle' => sprintf( wp_kses_post(__( 'Enter the specific address you wish to use for the hotel and select the correct option from the suggested addresses. This will be used to hyperlink address and display the address on the front-end map. <strong>Google Maps is also available for location. Simply set up your <a href="%s" target="_blank">Google Maps API Key</a></strong>', 'tourfic' )), esc_url( admin_url('admin.php?page=tf_settings#tab=map_settings') ) ),
					'height'   => '250px',
					'settings' => array(
						'scrollWheelZoom' => true,
					),
				),
			),
		),
		//Hotel Info
		'hotel_info' => array(
			'title'  => esc_html__( 'Information', 'tourfic' ),
			'icon'   => 'fa-solid fa-info-circle',
			'fields' => array(
				// nearby Places
				array(
					'id'      => 'nearby-places-heading',
					'type'    => 'heading',
					'content' => esc_html__( 'Nearby Places', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'          => 'section-title',
					'type'        => 'text',
					'label'       => esc_html__( 'Add Section Title', 'tourfic' ),
					'placeholder' => esc_html__( "What's around?", 'tourfic' ),
					'default' => esc_html__( "What's around?", 'tourfic' ),
					'attributes'  => array(
						'required' => 'required',
					),
				),
				array(
					'id'           => 'nearby-places',
					'type'         => 'repeater',
					'label'        => esc_html__( 'Insert / Create your hotel Place', 'tourfic' ),
					'button_title' => esc_html__( 'Add New Place', 'tourfic' ),
					'class'        => 'tf-field-class',
					'field_title'  => 'place-title',
					'fields'       => array(
						array(
							'id'          => 'place-title',
							'type'        => 'text',
							'subtitle'    => esc_html__( 'e.g. Rail Station', 'tourfic' ),
							'label'       => esc_html__( 'Name', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'place-dist',
							'type'        => 'text',
							'label'       => esc_html__( 'Place Distance and Unit', 'tourfic' ),
							'subtitle'    => esc_html__( 'Distance of the place from the Hotel with Unit', 'tourfic' ),
							'field_width' => 50,
							'attributes'  => array(
								'min' => '0',
							),
						),
						array(
							'id'       => 'place-icon',
							'type'     => 'icon',
							'label'    => esc_html__( 'Place Item Icon', 'tourfic' ),
							'subtitle' => esc_html__( 'Choose an appropriate icon', 'tourfic' ),
						),
					)
				), // nearby places end

				// facilities
				array(
					'id'      => 'facilities-heading',
					'type'    => 'heading',
					'content' => esc_html__( 'Hotel Facilities', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'          => 'facilities-section-title',
					'type'        => 'text',
					'label'       => esc_html__( 'Facilities Title', 'tourfic' ),
					'placeholder' => esc_html__( "Property facilities", 'tourfic' ),
					'default' => esc_html__( "Property facilities", 'tourfic' ),
					'attributes'  => array(
						'required' => 'required',
					),
				),
				array(
					'id'           => 'hotel-facilities',
					'type'         => 'repeater',
					'label'        => esc_html__( 'Insert / Create Hotel Facilities', 'tourfic' ),
					'button_title' => esc_html__( 'Add New', 'tourfic' ),
					'class'        => 'tf-field-class',
					'fields'       => array(
						array(
							'id'          => 'facilities-feature',
							'type'        => 'select2',
							'label'       => esc_html__( 'Facilities Feature', 'tourfic' ),
							'placeholder' => __( 'Select facilities feature', 'tourfic' ),
							'options'     => 'terms',
							'query_args'  => array(
								'taxonomy'   => 'hotel_feature',
								'hide_empty' => false,
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'facilities-category',
							'type'        => 'select2',
							'label'       => __( 'Facilities Category', 'tourfic' ),
							'placeholder' => __( 'Select facilities category', 'tourfic' ),
							'options'     => tf_hotel_facilities_categories(),
							'description' => __( 'Add new category from ', 'tourfic' ) . '<a target="_blank" href="' . esc_url( admin_url('admin.php?page=tf_settings#tab=single_page') ) .'">' . __("Facilities Categories", 'tourfic') . '</a>',
							'field_width' => 50,
						),
						array(
							'id'        => 'favorite',
							'type'      => 'switch',
							'label'     => __( 'Mark as Favorite', 'tourfic' ),
							'label_on'  => __( 'Yes', 'tourfic' ),
							'label_off' => __( 'No', 'tourfic' ),
						),
					)
				), // facilities end
			),
		),
		// Hotel Details
		'hotel_details'    => array(
			'title'  => __( 'Gallery & Video', 'tourfic' ),
			'icon'   => 'fa-solid fa-hotel',
			'fields' => array(
				array(
					'id'    => 'hotel-image-heading',
					'type'  => 'heading',
					'label' => 'Upload Images & Videos',
					'subtitle' => __( 'Images and videos are effective methods for showcasing your hotel to guests and have the potential to increase bookings.', 'tourfic' ),
				),
				array(
					'id'      => 'hotel-image-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/add-new-hotel/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'gallery',
					'type'     => 'gallery',
					'label'    => __( 'Hotel Gallery', 'tourfic' ),
					'subtitle' => __( 'Add multiple images to craft a captivating gallery for your hotel, giving potential customers a visual tour.', 'tourfic' ),
				),
				array(
					'id'          => 'video',
					'type'        => 'text',
					'label'       => __( 'Hotel Video', 'tourfic' ),
					'subtitle'    => __( 'If you have an enticing video of your hotel, simply upload it to YouTube or Vimeo and insert the URL here to showcase it to your guests.', 'tourfic' ),
					'placeholder' => __( 'Input full URL here (no embed code)', 'tourfic' ),
				),
			),
		),
		// Hotel Details
		'hotel_service'    => array(
			'title'  => __( 'Hotel Services', 'tourfic' ),
			'icon'   => 'fa-solid fa-van-shuttle',
			'fields' => array(
				array(
					'id'    => 'hotel-service-heading',
					'type'  => 'heading',
					'label' => 'Additional Hotel Services',
					'subtitle' => __( 'This section includes additional services which your hotel may offer. You may offer these services for free, or opt to charge your guests for them.', 'tourfic' ),
				),
				array(
					'id'      => 'hotel-service-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/hotel-services/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'Do you need to add hotel airport services such as pickup, dropoff, or both? Our Pro plan includes the <b>hotel service</b> feature, allowing you to easily add these services with pricing options <b>per person</b>, <b>fixed</b>, or <b>complimentary</b>. Enhance your guest experience by integrating these convenient services seamlessly into your offerings. <a href="https://tourfic.com/" target="_blank">Upgrade to our pro package today to take advantage of this fantastic option!</a>', 'tourfic') ),
				),
			),
		),
		// Room Details
		'room_details'     => array(
			'title'  => __( 'Room Management', 'tourfic' ),
			'icon'   => 'fa-sharp fa-solid fa-door-open',
			'fields' => array(
				array(
					'id'    => 'hotel-room-heading',
					'type'  => 'heading',
					'label' => 'Create & Manage Your Hotel Rooms',
					'subtitle' => __( 'In this section, you are provided with the tools to create and manage your hotel room offerings. ', 'tourfic' ),
				),
				array(
					'id'      => 'notice',
					'type'    => 'notice',
					'notice'  => 'info',
					'content' => __( 'This section includes Hotel Room Management settings.', 'tourfic' ). ' <a href="https://themefic.com/docs/tourfic/how-it-works/room-management/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'room-section-title',
					'type'  => 'text',
					'label' => __( 'Section Title', 'tourfic' ),
					'default' => __( "Available Rooms", 'tourfic' ),
				),
				array(
					'id'          => 'tf_rooms',
					'type'        => 'select2',
					'label'        => __( 'Manage your hotel rooms', 'tourfic' ),
					'subtitle'     => esc_html__('Select an existing hotel room, if available. Note: Rooms already assigned to a hotel cannot be selected.', 'tourfic'),
					'placeholder' => __( 'Select Rooms', 'tourfic' ),
					'options'     => 'posts',
					'multiple'   => true,
					'query_args'  => array(
						'post_type'      => 'tf_room',
						'posts_per_page' => - 1,
					),
					'inline_add_new' => true,
					'inline_delete' => true,
					'add_button_text' => esc_html__('Add New Room', 'tourfic')
				),
				array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'We\'re offering some extra features in every rooms like <b>child age limit</b>, <b>hotel room custom availability</b>, <b>deposit</b>, <b>ical sync</b> and <b>per person basis pricing</b> in our pro plan. <a href="https://tourfic.com/" target="_blank"> Upgrade to our pro package today to take advantage of these fantastic options!</a>', 'tourfic' )),
				),
			),
		),
		// FAQ Details
		'faq'              => array(
			'title'  => __( 'FAQ Section', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'    => 'hotel-faq-heading',
					'type'  => 'heading',
					'label' => 'FAQ Section',
					'subtitle' => __( 'This section is designed to help users find answers to common questions.', 'tourfic' ),
				),
				array(
					'id'      => 'hotel-faq-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/hotel-f-a-q/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'faq-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the Section', 'tourfic' ),
					'subtitle'    => __( 'This text will appear as the heading of the FAQ section on the frontend.', 'tourfic' ),
					'default' => "Faq’s"
				),
				array(
					'id'           => 'faq',
					'type'         => 'repeater',
					'label'        => __( 'Add Your Questions', 'tourfic' ),
					'subtitle'    => __( 'Click the button below to add Frequently Asked Questions (FAQs) for your hotel. Feel free to add as many as needed. Additionally, you can duplicate or rearrange each FAQ using the icons on the right side.', 'tourfic' ),
					'button_title' => __( 'Add New FAQ', 'tourfic' ),
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
							'label' => __( 'Single FAQ Description', 'tourfic' ),
						),

					),
				),
			),
		),
		// Enquiry Section
		'h_enquiry'    => array(
			'title'  => __( 'Enquiry', 'tourfic' ),
			'icon'   => 'fa fa-question-circle-o',
			'fields' => array(
				array(
					'id'      => 'enquiry-section',
					'type'    => 'heading',
					'content' => __( 'Hotel Enquiry Form', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'        => 'h-enquiry-section',
					'type'      => 'switch',
					'label'     => __( 'Enable Hotel Enquiry Form Option', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => true
				),
				array(
					'id'       => 'h-enquiry-option-icon',
					'type'     => 'icon',
					'label'    => __( 'Hotel Enquiry icon', 'tourfic' ),
					'subtitle' => __( 'Choose an Icon', 'tourfic' ),
					'default'  => 'fa fa-question-circle-o',
					'dependency' => array( 'h-enquiry-section', '==', '1' ),
				),
				array(
					'id'         => 'h-enquiry-option-title',
					'type'       => 'text',
					'label' => __( 'Enquiry Title', 'tourfic' ),
					'default'    => "Have a question in mind",
					'dependency' => array( 'h-enquiry-section', '==', '1' ),
				),
				array(
					'id'         => 'h-enquiry-option-content',
					'type'       => 'text',
					'label' => __( 'Enquiry Description', 'tourfic' ),
					'default'    => "Looking for more info? Send a question to the property to find out more.",
					'dependency' => array( 'h-enquiry-section', '==', '1' ),
				),
				array(
					'id'         => 'h-enquiry-option-btn',
					'type'       => 'text',
					'label' => __( 'Enquiry Button Text', 'tourfic' ),
					'default'    => "Ask a Question",
					'dependency' => array( 'h-enquiry-section', '==', '1' ),
				),
			),
		),

		// Multiple tags for hotels
		'hotel_multiple_tags' => array(
			'title'  => __( 'Promotional Tags', 'tourfic' ),
			'icon'   => 'fa fa-list',
			'fields' => array(
				array(
					'id'      => 'tf-hotel-tags-heading',
					'type'    => 'heading',
					'label' => __( 'Hotel tags', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'           => 'tf-hotel-tags',
					'type'         => 'repeater',
					'label'        => __( 'Promotional Tags', 'tourfic' ),
					'subtitle' => __('Add some keywords that highlight your hotel\'s Unique Selling Point (USP). This tag will be displayed on both the Archive Page and the Search Results Page.', 'tourfic'),
					'button_title' => __( 'Add / Insert New Tag', 'tourfic' ),
					'field_title'  => 'hotel-tag-title',
					'fields'       => array(

						array(
							'id'    => 'hotel-tag-title',
							'type'  => 'text',
							'label' => __( 'Tag Title', 'tourfic' ),
						),

						array(
							'id'       => 'hotel-tag-color-settings',
							'type'     => 'color',
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
		// Terms & conditions
		'terms_conditions' => array(
			'title'  => __( 'Terms & Conditions', 'tourfic' ),
			'icon'   => 'fa-regular fa-square-check',
			'fields' => array(
				array(
					'id'    => 'hotel-tnc-heading',
					'type'  => 'heading',
					'label' => 'Terms & Conditions Section',
					'subtitle' => __( 'Include your set of regulations and guidelines that guests must agree to in order to use the service provided in your hotel. ', 'tourfic' ),
				),
				array(
					'id'      => 'hotel-tnc-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/terms-conditions/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'tc-section-title',
					'type'  => 'text',
					'label' => __( 'Title of the Section', 'tourfic' ),
					'subtitle'    => __( 'This text will appear as the heading of the T&C section on the frontend.', 'tourfic' ),
					'default' => "Hotel Terms & Conditions"
				),
				array(
					'id'    => 'tc',
					'type'  => 'editor',
					'label' => __( 'Hotel Terms & Conditions', 'tourfic' ),
					'subtitle'    => __( "Enter your hotel's terms and conditions in the text editor provided below.", 'tourfic' ),
				),
			),
		),
		// Settings
		'settings'         => array(
			'title'  => __( 'Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-viruses',
			'fields' => array(
				array(
					'id'    => 'hotel-settings-heading',
					'type'  => 'heading',
					'label' => 'Other Settings',
					'subtitle' => __( 'These are some additional settings specific to this Hotel. Note that some of these settings may override the global settings. ', 'tourfic' ),
				),
				array(
					'id'      => 'hotel-settings-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/hotel-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'settings',
					'type'  => 'heading',
					'label' => __( 'Settings', 'tourfic' ),
					'class' => 'tf-field-class',
				),
				array(
					'id'        => 'h-review',
					'type'      => 'switch',
					'label'     => __( 'Disable Review Section', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false
				),

				array(
					'id'        => 'h-share',
					'type'      => 'switch',
					'label'     => __( 'Disable Share Option', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false
				),

				array(
					'id'        => 'h-wishlist',
					'type'      => 'switch',
					'label'     => __( 'Disable Wishlist Option', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false
				),

				array(
					'id'      => 'different-sections',
					'type'    => 'heading',
					'content' => __( 'Titles / Heading of Different Sections', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'      => 'popular-section-title',
					'type'    => 'text',
					'label' => __( 'Title for the Popular Features Section', 'tourfic' ),
					'subtitle'    => __( 'This text will appear as the heading of the Popular Features section on the frontend.', 'tourfic' ),
					'default' => "Popular Features"

				),
				array(
					'id'      => 'review-section-title',
					'type'    => 'text',
					'label' => __( 'Title for the Reviews Section', 'tourfic' ),
					'subtitle'    => __( 'This text will appear as the heading of the Reviews section on the frontend.', 'tourfic' ),
					'default' => "Average Guest Reviews"
				),
			),
		),
	),
) );
