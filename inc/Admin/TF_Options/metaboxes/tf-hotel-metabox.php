<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

$badge_up     = '<div class="tf-csf-badge"><span class="tf-upcoming">' . esc_html__( "Upcoming", "tourfic" ) . '</span></div>';

$hotel_name = apply_filters( 'tf_hotel_post_type_name_change_singular', esc_html__( 'Hotel', 'tourfic' ) );
$hotels_name = apply_filters( 'tf_hotel_post_type_name_change_plural', esc_html__( 'Hotels', 'tourfic' ) );

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
	/* translators: %s is the hotel name */
	'title' => sprintf( esc_html__( '%s Settings', 'tourfic' ), $hotel_name ),
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
					/* translators: %s is the hotel name */
					'subtitle' => sprintf( esc_html__( 'These are some common settings specific to this %s.', 'tourfic' ), $hotel_name ),
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
					/* translators: %s is the hotel name */
					'label'    => sprintf( esc_html__( 'Featured %s', 'tourfic' ), $hotel_name ),
					/* translators: %s is the hotel name */
					'subtitle' => sprintf( esc_html__( 'Enable this option to feature this %s at the top of search results.', 'tourfic' ), strtolower( $hotel_name ) ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false,
				),
				array(
					'id'          => 'featured_text',
					'type'        => 'text',
					/* translators: %s is the hotel name */
					'label'       => sprintf( esc_html__( '%s Featured Text', 'tourfic' ), $hotel_name ),
					/* translators: %s is the hotel name */
					'subtitle'    => sprintf( esc_html__( 'Enter Featured %s Text', 'tourfic' ), $hotel_name ),
					/* translators: %s is the hotel name */
					'placeholder' => sprintf( esc_html__( 'Enter Featured %s Text', 'tourfic' ), $hotel_name ),
					'default'     => esc_html__( 'Hot Deal', 'tourfic' ),
					'dependency'  => array( 'featured', '==', true ),
				),
				array(
					'id'       => 'tf_single_hotel_layout_opt',
					'type'     => 'select',
					/* translators: %s is the hotel name */
					'label'    => sprintf( esc_html__( 'Single %s Template Settings', 'tourfic' ), $hotel_name ),
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
					/* translators: %s is the hotel name */
					'label' => sprintf( esc_html__( 'Single %s Page Layout', 'tourfic' ), $hotel_name ),
					'options'   	=> array(
						'design-1' => array(
							'title' => esc_html__('Design 1', 'tourfic'),
							'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-single-design-1.png",
							'preview_link' => esc_url('https://tourfic.com/preview/hotels/tuvo-suites-hotel/'),
						),
						'design-2' 	=> array(
							'title'	=> esc_html__('Design 2', 'tourfic'),
							'url' 	=> TF_ASSETS_ADMIN_URL."images/template/preview-single-design-2.png",
							'preview_link' => esc_url('https://tourfic.com/preview/hotels/melbourne-mastlereagh/'),
						),
						'default'  => array(
							'title' => esc_html__('Legacy', 'tourfic'),
							'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-single-default.png",
							'preview_link' => esc_url('https://tourfic.com/preview/hotels/rio-ontho-palace/'),
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
			),
		),
		'location'         => array(
			'title'  => esc_html__( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'    => 'hotel-location-heading',
					'type'  => 'heading',
					'label' => esc_html__( 'Location Settings', 'tourfic' ),
					/* translators: %1$s is the hotel name in lowercase, %2$s is the hotel name in lowercase */
					'subtitle' => sprintf(esc_html__( 'The location of a %1$s is a crucial element for every booking. Set your %2$s locations in this section.', 'tourfic' ),
						strtolower( $hotel_name ),
						strtolower( $hotel_name )
					),
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
					// translators: %s is the link to the Google Maps API Key settings page.
					'subtitle' => wp_kses_post(sprintf(__( 'Enter the specific address you wish to use for the %1$s and select the correct option from the suggested addresses. This will be used to hyperlink the address and display it on the front-end map. <strong>Google Maps is also available for location. Simply set up your <a href="%2$s" target="_blank">Google Maps API Key</a></strong>', 'tourfic'),
							strtolower( $hotel_name ),
							esc_url( admin_url( 'admin.php?page=tf_settings#tab=map_settings' ) )
						)
					),

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
					/* translators: %s is the hotel name */
					'label' => sprintf(esc_html__( 'Insert / Create your %s Place', 'tourfic' ),
						strtolower($hotel_name)
					),
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
							/* translators: %s is the hotel name */
							'subtitle' => sprintf(esc_html__( 'Distance of the place from the %s with Unit', 'tourfic' ),
								$hotel_name
							),
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
					/* translators: %s is the hotel name */
					'content' => sprintf(esc_html__( '%s Facilities', 'tourfic' ),
						$hotel_name
					),
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
					/* translators: %s is the hotel name */
					'label' => sprintf(esc_html__( 'Insert / Create %s Facilities', 'tourfic' ),
						$hotel_name
					),
					'button_title' => esc_html__( 'Add New', 'tourfic' ),
					'class'        => 'tf-field-class',
					'fields'       => array(
						array(
							'id'          => 'facilities-feature',
							'type'        => 'select2',
							'label'       => esc_html__( 'Facilities Feature', 'tourfic' ),
							'placeholder' => esc_html__( 'Select facilities feature', 'tourfic' ),
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
							'label'       => esc_html__( 'Facilities Category', 'tourfic' ),
							'placeholder' => esc_html__( 'Select facilities category', 'tourfic' ),
							'options'     => tf_hotel_facilities_categories(),
							'description' => esc_html__( 'Add new category from ', 'tourfic' ) . '<a target="_blank" href="' . esc_url( admin_url('admin.php?page=tf_settings#tab=single_page') ) .'">' . esc_html__("Facilities Categories", 'tourfic') . '</a>',
							'field_width' => 50,
						),
						array(
							'id'        => 'favorite',
							'type'      => 'switch',
							'label'     => esc_html__( 'Mark as Favorite', 'tourfic' ),
							'label_on'  => esc_html__( 'Yes', 'tourfic' ),
							'label_off' => esc_html__( 'No', 'tourfic' ),
						),
					)
				), // facilities end
			),
		),
		// Hotel Details
		'hotel_details'    => array(
			'title'  => esc_html__( 'Gallery & Video', 'tourfic' ),
			'icon'   => 'fa-solid fa-hotel',
			'fields' => array(
				array(
					'id'    => 'hotel-image-heading',
					'type'  => 'heading',
					'label' => 'Upload Images & Videos',
					/* translators: %s is the hotel name */
					'subtitle' => sprintf(esc_html__( 'Images and videos are effective methods for showcasing your %s to guests and have the potential to increase bookings.', 'tourfic' ),
						strtolower( $hotel_name )
					),
				),
				array(
					'id'      => 'hotel-image-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/add-new-hotel/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'gallery',
					'type'     => 'gallery',
					/* translators: %s is the hotel name */
					'label'    => sprintf( esc_html__( '%s Gallery', 'tourfic' ), $hotel_name ),
					/* translators: %s is the hotel name */
					'subtitle' => sprintf( esc_html__( 'Add multiple images to craft a captivating gallery for your %s, giving potential customers a visual tour.', 'tourfic' ), strtolower( $hotel_name ) ),
				),
				array(
					'id'          => 'video',
					'type'        => 'text',
					/* translators: %s is the hotel name */
					'label'       => sprintf( esc_html__( '%s Video', 'tourfic' ), $hotel_name ),
					/* translators: %s is the hotel name */
					'subtitle'    => sprintf( esc_html__( 'If you have an enticing video of your %s, simply upload it to YouTube or Vimeo and insert the URL here to showcase it to your guests.', 'tourfic' ), $hotel_name ),
					'placeholder' => esc_html__( 'Input full URL here (no embed code)', 'tourfic' ),
				),				
			),
		),
		// Hotel Details
		'hotel_service'    => array(
			// translators: %s is the hotel name.
			'title' => sprintf(esc_html__( '%s Services', 'tourfic' ),
				$hotel_name
			),

			'icon'   => 'fa-solid fa-van-shuttle',
			'fields' => array(
				array(
					'id'    => 'hotel-service-heading',
					'type'  => 'heading',
					// translators: %s is the hotel name.
					'label' => sprintf(esc_html__( 'Additional %s Services', 'tourfic' ),
						$hotel_name
					),
					// translators: %s is the hotel name in lowercase.
					'subtitle' => sprintf(esc_html__( 'This section includes additional services which your %s may offer. You may offer these services for free, or opt to charge your guests for them.', 'tourfic' ),
						strtolower( $hotel_name )
					),
				),				
				array(
					'id'      => 'hotel-service-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/hotel-services/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				
			),
		),
		// Room Details
		'room_details'     => array(
			'title'  => esc_html__( 'Room Management', 'tourfic' ),
			'icon'   => 'fa-sharp fa-solid fa-door-open',
			'fields' => array(
				array(
					'id'    => 'hotel-room-heading',
					'type'  => 'heading',
					/* translators: %s is the hotel name. */
					'label' => sprintf( esc_html__( 'Create & Manage Your %s Rooms', 'tourfic' ), $hotel_name ),
					/* translators: %s is the lowercased hotel name. */
					'subtitle' => sprintf( esc_html__( 'In this section, you are provided with the tools to create and manage your %s room offerings.', 'tourfic' ), strtolower( $hotel_name ) ),
				),
				array(
					'id'     => 'notice',
					'type'   => 'notice',
					'notice' => 'info',
					/* translators: %s is the hotel name. */
					'content' => sprintf( esc_html__( 'This section includes %s Room Management settings.', 'tourfic' ), 
						$hotel_name 
					) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/room-management/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				
				array(
					'id'    => 'room-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Section Title', 'tourfic' ),
					'default' => esc_html__( "Available Rooms", 'tourfic' ),
				),
				array(
					'id'          => 'tf_rooms',
					'type'        => 'select2',
					/* translators: %s is the lowercased hotel name. */
					'label'       => sprintf( esc_html__( 'Manage your %s rooms', 'tourfic' ), strtolower( $hotel_name ) ),
					/* translators: %1$s is the lowercased hotel name and %2$s is return hotel name. */
					'subtitle'    => sprintf( esc_html__( 'Select an existing %1$s room, if available. Note: Rooms already assigned to a %2$s cannot be selected.', 'tourfic' ), strtolower( $hotel_name ), strtolower( $hotel_name ) ),
					'placeholder' => esc_html__( 'Select Rooms', 'tourfic' ),
					'options'     => 'posts',
					'multiple'    => true,
					'query_args'  => array(
						'post_type'      => 'tf_room',
						'posts_per_page' => -1,
					),
					'inline_add_new'  => true,
					'inline_delete'   => true,
					'add_button_text' => esc_html__( 'Add New Room', 'tourfic' ),
				),
						
			),
		),
		// FAQ Details
		'faq'              => array(
			'title'  => esc_html__( 'FAQ Section', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'    => 'hotel-faq-heading',
					'type'  => 'heading',
					'label' => 'FAQ Section',
					'subtitle' => esc_html__( 'This section is designed to help users find answers to common questions.', 'tourfic' ),
				),
				array(
					'id'      => 'hotel-faq-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/hotel-f-a-q/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'faq-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the Section', 'tourfic' ),
					'subtitle'    => esc_html__( 'This text will appear as the heading of the FAQ section on the frontend.', 'tourfic' ),
					'default' => "Faq’s"
				),
				array(
					'id'           => 'faq',
					'type'         => 'repeater',
					'label'        => esc_html__( 'Add Your Questions', 'tourfic' ),
					/* translators: %s is the lowercased hotel name. */
					'subtitle' => sprintf(esc_html__( 'Click the button below to add Frequently Asked Questions (FAQs) for your %s. Feel free to add as many as needed. Additionally, you can duplicate or rearrange each FAQ using the icons on the right side.', 'tourfic' ),
						strtolower( $hotel_name )
					),

					'button_title' => esc_html__( 'Add New FAQ', 'tourfic' ),
					'field_title'  => 'title',
					'fields'       => array(

						array(
							'id'    => 'title',
							'type'  => 'text',
							'label' => esc_html__( 'Single FAQ Title', 'tourfic' ),
						),

						array(
							'id'    => 'description',
							'type'  => 'editor',
							'label' => esc_html__( 'Single FAQ Description', 'tourfic' ),
						),

					),
				),
			),
		),
		// Enquiry Section
		'h_enquiry'    => array(
			'title'  => esc_html__( 'Enquiry', 'tourfic' ),
			'icon'   => 'fa fa-question-circle-o',
			'fields' => array(
				array(
					'id'      => 'enquiry-section',
					'type'    => 'heading',
					/* translators: %s is the hotel name */
					'content' => sprintf( esc_html__( '%s Enquiry Form', 'tourfic' ), $hotel_name ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'        => 'h-enquiry-section',
					'type'      => 'switch',
					/* translators: %s is the hotel name */
					'label'     => sprintf( esc_html__( 'Enable %s Enquiry Form Option', 'tourfic' ), $hotel_name ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => true
				),
				array(
					'id'       => 'h-enquiry-option-icon',
					'type'     => 'icon',
					/* translators: %s is the hotel name */
					'label'    => sprintf( esc_html__( '%s Enquiry icon', 'tourfic' ), $hotel_name ),
					'subtitle' => esc_html__( 'Choose an Icon', 'tourfic' ),
					'default'  => 'fa fa-question-circle-o',
					'dependency' => array( 'h-enquiry-section', '==', '1' ),
				),				
				array(
					'id'         => 'h-enquiry-option-title',
					'type'       => 'text',
					'label' => esc_html__( 'Enquiry Title', 'tourfic' ),
					'default'    => "Have a question in mind",
					'dependency' => array( 'h-enquiry-section', '==', '1' ),
				),
				array(
					'id'         => 'h-enquiry-option-content',
					'type'       => 'text',
					'label' => esc_html__( 'Enquiry Description', 'tourfic' ),
					'default'    => "Looking for more info? Send a question to the property to find out more.",
					'dependency' => array( 'h-enquiry-section', '==', '1' ),
				),
				array(
					'id'         => 'h-enquiry-option-btn',
					'type'       => 'text',
					'label' => esc_html__( 'Enquiry Button Text', 'tourfic' ),
					'default'    => "Ask a Question",
					'dependency' => array( 'h-enquiry-section', '==', '1' ),
				),
			),
		),

		// Multiple tags for hotels
		'hotel_multiple_tags' => array(
			'title'  => esc_html__( 'Promotional Tags', 'tourfic' ),
			'icon'   => 'fa fa-list',
			'fields' => array(
				array(
					'id'    => 'tf-hotel-tags-heading',
					'type'  => 'heading',
					/* translators: %s is the hotel name */
					'label' => sprintf( esc_html__( '%s tags', 'tourfic' ), esc_html($hotel_name) ),
					'class' => 'tf-field-class',
				),
				array(
					'id'           => 'tf-hotel-tags',
					'type'         => 'repeater',
					'label'        => esc_html__( 'Promotional Tags', 'tourfic' ),
					/* translators: %s is the hotel name */
					'subtitle'     => sprintf(esc_html__( 'Add some keywords that highlight your %s\'s Unique Selling Point (USP). This tag will be displayed on both the Archive Page and the Search Results Page.', 'tourfic' ),
						strtolower( $hotel_name )
					),
					'button_title' => esc_html__( 'Add / Insert New Tag', 'tourfic' ),
					'field_title'  => 'hotel-tag-title',
					'fields'       => array(

						array(
							'id'    => 'hotel-tag-title',
							'type'  => 'text',
							'label' => esc_html__( 'Tag Title', 'tourfic' ),
						),

						array(
							'id'       => 'hotel-tag-color-settings',
							'type'     => 'color',
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
		// Terms & conditions
		'terms_conditions' => array(
			'title'  => esc_html__( 'Terms & Conditions', 'tourfic' ),
			'icon'   => 'fa-regular fa-square-check',
			'fields' => array(
				array(
					'id'    => 'hotel-tnc-heading',
					'type'  => 'heading',
					'label' => 'Terms & Conditions Section',
					'subtitle' => esc_html__( 'Include your set of regulations and guidelines that guests must agree to in order to use the service provided in your hotel. ', 'tourfic' ),
				),
				array(
					'id'      => 'hotel-tnc-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/terms-conditions/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'tc-section-title',
					'type'  => 'text',
					'label' => esc_html__( 'Title of the Section', 'tourfic' ),
					'subtitle'    => esc_html__( 'This text will appear as the heading of the T&C section on the frontend.', 'tourfic' ),
					'default' => "Hotel Terms & Conditions"
				),
				array(
					'id'    => 'tc',
					'type'  => 'editor',
					/* translators: %s is the hotel name */
					'label' => sprintf( esc_html__( '%s Terms & Conditions', 'tourfic' ), $hotel_name ),
					/* translators: %s is the hotel name */
					'subtitle' => sprintf( esc_html__( 'Enter your %s\'s terms and conditions in the text editor provided below.', 'tourfic' ), $hotel_name ),
				),				
			),
		),
		// Settings
		'settings'         => array(
			'title'  => esc_html__( 'Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-viruses',
			'fields' => array(
				array(
					'id'    => 'hotel-settings-heading',
					'type'  => 'heading',
					'label' => esc_html__( 'Other Settings', 'tourfic' ),
					/* translators: %s is the hotel name */
					'subtitle' => sprintf(esc_html__( 'These are some additional settings specific to this %s. Note that some of these settings may override the global settings.', 'tourfic' ),
						$hotel_name
					),
				),				
				array(
					'id'      => 'hotel-settings-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/hotel-settings/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'    => 'settings',
					'type'  => 'heading',
					'label' => esc_html__( 'Settings', 'tourfic' ),
					'class' => 'tf-field-class',
				),
				array(
					'id'        => 'h-review',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Review Section', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false
				),

				array(
					'id'        => 'h-share',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Share Option', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false
				),

				array(
					'id'        => 'h-wishlist',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Wishlist Option', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false
				),

				array(
					'id'      => 'different-sections',
					'type'    => 'heading',
					'content' => esc_html__( 'Titles / Heading of Different Sections', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'      => 'popular-section-title',
					'type'    => 'text',
					'label' => esc_html__( 'Title for the Popular Features Section', 'tourfic' ),
					'subtitle'    => esc_html__( 'This text will appear as the heading of the Popular Features section on the frontend.', 'tourfic' ),
					'default' => "Popular Features"

				),
				array(
					'id'      => 'review-section-title',
					'type'    => 'text',
					'label' => esc_html__( 'Title for the Reviews Section', 'tourfic' ),
					'subtitle'    => esc_html__( 'This text will appear as the heading of the Reviews section on the frontend.', 'tourfic' ),
					'default' => "Average Guest Reviews"
				),
			),
		),
	),
) );
