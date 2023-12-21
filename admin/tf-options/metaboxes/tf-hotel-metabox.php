<?php
// don't load directly
defined( 'ABSPATH' ) || exit;
$badge_up     = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span></div>';
$badge_pro    = '<div class="tf-csf-badge"><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';

if(!function_exists('tf_hotel_facilities_categories')) {
	function tf_hotel_facilities_categories() {
		$facilities_cats = ! empty( tf_data_types( tfopt( 'hotel_facilities_cats' ) ) ) ? tf_data_types( tfopt( 'hotel_facilities_cats' ) ) : '';
		$all_cats       = [];
		if ( ! empty( $facilities_cats ) && is_array( $facilities_cats ) ) {
			foreach ( $facilities_cats as $key => $cat ) {
				$all_cats[ (string) $key ] = $cat['hotel_facilities_cat_name'];
			}
		}
	
		if(empty($all_cats)){
			$all_cats[''] = __( 'Select Category', 'tourfic' );
		}
	
		return $all_cats;
	}
}

TF_Metabox::metabox( 'tf_hotels_opt', array(
	'title'     => 'Hotel Settings',
	'post_type' => 'tf_hotel',
	'sections'  => array(
		'general' => array(
			'title'  => __( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'hotel-general-heading',
					'type'  => 'heading',
					'label' => 'General Settings',
					'subtitle' => __( 'These are some common settings specific to this Hotel.', 'tourfic' ),
				),
				array(
					'id'      => 'hotel-general-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/add-new-hotel/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'        => 'featured',
					'type'      => 'switch',
					'label'     => __( 'Featured Hotel', 'tourfic' ),
					'subtitle' => __( 'Enable this option to feature this hotel at the top of search results.', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false,
				),
				array(
					'id'          => 'featured_text',
					'type'        => 'text',
					'label'       => __( 'Hotel Featured Text', 'tourfic' ),
					'subtitle'    => __( 'Enter Featured Hotel Text', 'tourfic' ),
					'placeholder' => __( 'Enter Featured Hotel Text', 'tourfic' ),
					'default' => __( 'Hot Deal', 'tourfic' ),
					'dependency'  => array( 'featured', '==', true ),
				),
				array(
					'id'       => 'tf_single_hotel_layout_opt',
					'type'     => 'select',
					'label'    => __( 'Single Hotel Template Settings', 'tourfic' ),
					'subtitle' => __( 'You can keep the Global Template settings or choose a different layout for this hotel.', 'tourfic' ),
					'options'  => [
						'global' => __( 'Global Settings', 'tourfic' ),
						'single' => __( 'Single Settings', 'tourfic' ),
					],
					'default'  => 'global',
				),
				array(
					'id'       => 'tf_single_hotel_template',
					'type'     => 'imageselect',
					'label'    => __( 'Single Hotel Page Layout', 'tourfic' ),
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
					'content' => __( 'Booking Settings', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'      => 'booking-by',
					'type'    => 'select',
					'label'   => __( 'Booking Type', 'tourfic' ),
					'options' => array(
						'1' => __( 'Default Booking (WooCommerce)', 'tourfic' ),
						'2' => __( 'External Booking (Pro)', 'tourfic' ),
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
					'subtitle' => __( 'Enable this option to hide the booking form from the single hotel page.', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'is_pro'  => true,
					'dependency' => array( 'booking-by', '==', '2' ),
				),
				array(
					'id'        => '',
					'type'      => 'switch',
					'label'     => __( 'Hide Price', 'tourfic' ),
					'subtitle' => __( 'Enable this option to hide the price from the single hotel page.', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'is_pro'  => true,
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
					'placeholder' => __( 'adult={adult}&child={child}&room={room}', 'tourfic' ),
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
							<strong>{checkin} </strong> : To display the Checkin date from Search.<br>
							<strong>{checkout} </strong> : To display the Checkout date from Search.<br>
							<strong>{room} </strong> : To display the room number from Search.<br>',
					'is_pro'  => true,
					'dependency'  => array( 'booking-by', '==', '2' ),
				),
			),
		),
		'location'         => array(
			'title'  => __( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'    => 'hotel-location-heading',
					'type'  => 'heading',
					'label' => 'Location Settings',
					'subtitle' => __( 'The location of a hotel is a crucial element for every booking. Set your hotel locations in this section.', 'tourfic' ),
				),
				array(
					'id'      => 'hotel-location-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => __( 'If anything is not clear, please', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/how-it-works/hotel-location/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'map',
					'class'    => 'gmaps',
					'type'     => 'map',
					'label'    => __( 'Dynamic Location Search', 'tourfic' ),
					'subtitle' => __( 'Enter the specific address you wish to use for the hotel and select the correct option from the suggested addresses. This will be used to hyperlink address and display the address on the front-end map. Note that the address provided in the previous section is solely for display purposes!', 'tourfic' ),
					'height'   => '250px',
					'settings' => array(
						'scrollWheelZoom' => true,
					),
				),
			),
		),
		//Hotel Info
		'hotel_info' => array(
			'title'  => __( 'Information\'s', 'tourfic' ),
			'icon'   => 'fa-solid fa-info-circle',
			'fields' => array(
				// nearby Places
				array(
					'id'      => 'nearby-places-heading',
					'type'    => 'heading',
					'content' => __( 'Nearby Places', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'          => 'section-title',
					'type'        => 'text',
					'label'       => __( 'Add Section Title', 'tourfic' ),
					'placeholder' => __( "What's around?", 'tourfic' ),
					'default' => __( "What's around?", 'tourfic' ),
					'attributes'  => array(
						'required' => 'required',
					),
				),
				array(
					'id'           => 'nearby-places',
					'type'         => 'repeater',
					'label'        => __( 'Insert / Create your hotel Place', 'tourfic' ),
					'button_title' => __( 'Add New Place', 'tourfic' ),
					'class'        => 'tf-field-class',
					'fields'       => array(
						array(
							'id'          => 'place-title',
							'type'        => 'text',
							'subtitle'    => __( 'e.g. Rail Station', 'tourfic' ),
							'label'       => __( 'Name', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'place-dist',
							'type'        => 'text',
							'label'       => __( 'Place Distance and Unit', 'tourfic' ),
							'subtitle'    => __( 'Distance of the place from the Hotel with Unit', 'tourfic' ),
							'field_width' => 50,
							'attributes'  => array(
								'min' => '0',
							),
						),
						array(
							'id'       => 'place-icon',
							'type'     => 'icon',
							'label'    => __( 'Place Item Icon', 'tourfic' ),
							'subtitle' => __( 'Choose an appropriate icon', 'tourfic' ),
						),
					)
				), // nearby places end

				// facilities
				array(
					'id'      => 'facilities-heading',
					'type'    => 'heading',
					'content' => __( 'Hotel Facilities', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'          => 'facilities-section-title',
					'type'        => 'text',
					'label'       => __( 'Facilities Title', 'tourfic' ),
					'placeholder' => __( "Property facilities", 'tourfic' ),
					'default' => __( "Property facilities", 'tourfic' ),
					'attributes'  => array(
						'required' => 'required',
					),
				),
				array(
					'id'           => 'hotel-facilities',
					'type'         => 'repeater',
					'label'        => __( 'Insert / Create Hotel Facilities', 'tourfic' ),
					'button_title' => __( 'Add New', 'tourfic' ),
					'class'        => 'tf-field-class',
					'fields'       => array(
						array(
							'id'          => 'facilities-feature',
							'type'        => 'select2',
							'label'       => __( 'Facilities Feature', 'tourfic' ),
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
							'description' => __( 'Add new category from <a target="_blank" href="'.admin_url('admin.php?page=tf_settings#tab=single_page').'">Facilities Categories</a>', 'tourfic' ),
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
					'id'      => '',
					'type'    => 'switch',
					'label'   => __( 'Airport Pickup Service', 'tourfic' ),
					'subtitle'    => __( 'Activate this feature to provide airport pickup services as an added convenience for your guests.', 'tourfic' ),
					'default' => true,
					'is_pro'  => true,
				),
				array(
					'id'         => '',
					'type'       => 'checkbox',
					'label'      => __( 'Service Type', 'tourfic' ),
					'inline'     => true,
					'options'    => array(
						'pickup'  => __( 'Pickup (Pro)', 'tourfic' ),
						'dropoff' => __( 'Drop-off (Pro)', 'tourfic' ),
						'both'    => __( 'Pickup & Drop-off (Pro)', 'tourfic' ),
					)
				),
				/**
				 *
				 * Service Type Pick up
				 */
				array(
					'id'         => '',
					'type'       => 'tab',
					'title'      => __( 'Pickup Service', 'tourfic' ),
					'is_pro'  => true,
					'tabs'       => array(
						array(
							'id'     => 'tab-1',
							'title'  => __( 'Pickup', 'tourfic' ),
							'icon'   => 'fa fa-heart',
							'fields' => array(
								array(
									'id'      => 'airport_pickup_price_type',
									'type'    => 'select',
									'label'   => __( 'Pickup Pricing Type', 'tourfic' ),
									'options' => array(
										'per_person' => __( 'Per Person', 'tourfic' ),
										'fixed'      => __( 'Fixed Price', 'tourfic' ),
										'free'       => __( 'Free / Complimentary', 'tourfic' ),
									),
									'default' => 'per_person',
								),
								array(
									'id'          => 'airport_service_fee_adult',
									'type'        => 'number',
									'dependency'  => array(
										array( 'airport_pickup_price_type', '==', 'per_person' ),
									),
									'label'       => __( 'Adult Price', 'tourfic' ),
									'subtitle'    => __( 'Price per adult. Insert number only (No currency sign needed).', 'tourfic' ),
									'attributes'  => array(
										'min' => '0',
									),
									'field_width' => 50,
								),
								array(
									'id'          => 'airport_service_fee_children',
									'type'        => 'number',
									'dependency'  => array(
										array( 'airport_pickup_price_type', '==', 'per_person' ),
									),
									'label'       => __( 'Children Price', 'tourfic' ),
									'subtitle'    => __( 'Price per child. Insert number only (No currency sign needed).', 'tourfic' ),
									'attributes'  => array(
										'min' => '0',
									),
									'field_width' => 50,
								),

								array(
									'id'         => 'airport_service_fee_fixed',
									'type'       => 'number',
									'dependency' => array(
										array( 'airport_pickup_price_type', '==', 'fixed' ),
									),
									'label'      => __( 'Fixed Price', 'tourfic' ),
									'subtitle'   => __( 'Insert number only (No currency sign needed)', 'tourfic' ),
									'attributes' => array(
										'min' => '0',
									),
								),
							)
						)
					)
				),

				/**
				 *
				 * Service Type Drop Off
				 */
				array(
					'id'         => '',
					'type'       => 'tab',
					'title'      => __( 'Drop-off Service', 'tourfic' ),
					'is_pro'  => true,
					'tabs'       => array(
						array(
							'id'     => 'tab-1',
							'title'  => __( 'Drop-off', 'tourfic' ),
							'icon'   => 'fa fa-heart',
							'fields' => array(
								array(
									'id'      => 'airport_pickup_price_type',
									'type'    => 'select',
									'label'   => __( 'Drop-off Pricing Type', 'tourfic' ),
									'options' => array(
										'per_person' => __( 'Per Person', 'tourfic' ),
										'fixed'      => __( 'Fixed Price', 'tourfic' ),
										'free'       => __( 'Free / Complimentary', 'tourfic' ),
									),
									'default' => 'per_person',
								),
								array(
									'id'          => 'airport_service_fee_adult',
									'type'        => 'number',
									'dependency'  => array(
										array( 'airport_pickup_price_type', '==', 'per_person' ),
									),
									'label'       => __( 'Adult Price', 'tourfic' ),
									'subtitle'    => __( 'Price per adult. Insert number only (No currency sign needed).', 'tourfic' ),
									'attributes'  => array(
										'min' => '0',
									),
									'field_width' => 50,
								),
								array(
									'id'          => 'airport_service_fee_children',
									'type'        => 'number',
									'dependency'  => array(
										array( 'airport_pickup_price_type', '==', 'per_person' ),
									),
									'label'       => __( 'Children Price', 'tourfic' ),
									'subtitle'    => __( 'Price per child. Insert number only (No currency sign needed).', 'tourfic' ),
									'attributes'  => array(
										'min' => '0',
									),
									'field_width' => 50,
								),

								array(
									'id'         => 'airport_service_fee_fixed',
									'type'       => 'number',
									'dependency' => array(
										array( 'airport_pickup_price_type', '==', 'fixed' ),
									),
									'label'      => __( 'Fixed Price', 'tourfic' ),
									'subtitle'   => __( 'Insert number only (No currency sign needed)', 'tourfic' ),
									'attributes' => array(
										'min' => '0',
									),
								),
							)
						)
					)
				),

				/**
				 *
				 * Service Type pickup Pickoff (both)
				 */
				array(
					'id'         => '',
					'type'       => 'tab',
					'title'      => __( 'Pickup & Drop-off Service', 'tourfic' ),
					'is_pro'  => true,
					'tabs'       => array(
						array(
							'id'     => 'tab-1',
							'title'  => __( 'Pickup & Drop-off', 'tourfic' ),
							'icon'   => 'fa fa-heart',
							'fields' => array(
								array(
									'id'      => 'airport_pickup_price_type',
									'type'    => 'select',
									'label'   => __( 'Pickup & Drop-off Pricing Type', 'tourfic' ),
									'options' => array(
										'per_person' => __( 'Per Person', 'tourfic' ),
										'fixed'      => __( 'Fixed Price', 'tourfic' ),
										'free'       => __( 'Free / Complimentary', 'tourfic' ),
									),
									'default' => 'per_person',
								),
								array(
									'id'          => 'airport_service_fee_adult',
									'type'        => 'number',
									'dependency'  => array(
										array( 'airport_pickup_price_type', '==', 'per_person' ),
									),
									'label'       => __( 'Adult Price', 'tourfic' ),
									'subtitle'    => __( 'Price per adult. Insert number only (No currency sign needed).', 'tourfic' ),
									'attributes'  => array(
										'min' => '0',
									),
									'field_width' => 50,
								),
								array(
									'id'          => 'airport_service_fee_children',
									'type'        => 'number',
									'dependency'  => array(
										array( 'airport_pickup_price_type', '==', 'per_person' ),
									),
									'label'       => __( 'Children Price', 'tourfic' ),
									'subtitle'    => __( 'Price per child. Insert number only (No currency sign needed).', 'tourfic' ),
									'attributes'  => array(
										'min' => '0',
									),
									'field_width' => 50,
								),

								array(
									'id'         => 'airport_service_fee_fixed',
									'type'       => 'number',
									'dependency' => array(
										array( 'airport_pickup_price_type', '==', 'fixed' ),
									),
									'label'      => __( 'Fixed Price', 'tourfic' ),
									'subtitle'   => __( 'Insert number only (No currency sign needed)', 'tourfic' ),
									'attributes' => array(
										'min' => '0',
									),
								),
							)
						)
					)
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
					'id'           => 'room',
					'type'         => 'repeater',
					'label'        => __( 'Create your hotel rooms', 'tourfic' ),
					'button_title' => __( 'Add New Room', 'tourfic' ),
					'class'        => 'room-repeater',
					'max'          => 5,
					'fields'       => array(
						array(
							'id'          => 'unique_id',
							'class'       => 'unique-id',
							'type'        => 'text',
							'label'       => __( 'Unique ID', 'tourfic' ),
							'attributes'  => array(
								'readonly' => 'readonly',
							),
							'placeholder' => __( '', 'tourfic' ),
						),
						array(
							'id'          => 'order_id',
							'class'       => 'tf-order_id',
							'type'        => 'text',
							'label'       => __( 'Order ID', 'tourfic' ),
							'attributes'  => array(
								'readonly' => 'readonly',
							),
							'placeholder' => __( '', 'tourfic' ),
						),
						array(
							'id'        => 'enable',
							'type'      => 'switch',
							'label'     => __( 'Status', 'tourfic' ),
							'subtitle'  => __( 'Enable/disable this Room', 'tourfic' ),
							'label_on'  => __( 'Enabled', 'tourfic' ),
							'label_off' => __( 'Disabled', 'tourfic' ),
							'width'     => 100,
							'default'   => 1,
						),
						array(
							'id'          => 'title',
							'type'        => 'text',
							'subtitle'    => __( 'e.g. Superior Queen Room with Two Queen Beds', 'tourfic' ),
							'label'       => __( 'Room Title', 'tourfic' ),
							'field_width' => 100,
						),

						array(
							'id'      => 'Details',
							'type'    => 'heading',
							'content' => __( 'Details', 'tourfic' ),
							'class'   => 'tf-field-class',
						),
						array(
							'id'      => 'room_preview_img',
							'type'    => 'image',
							'label'    => __( 'Room Image Thumbnail', 'tourfic' ),
							'subtitle' => __( 'Upload Thumbnail Image for this room', 'tourfic' ),
							'library' => 'image',
						),
						array(
							'id'       => 'gallery',
							'type'     => 'gallery',
							'label'    => __( 'Single Room Gallery', 'tourfic' ),
							'subtitle' => __( 'Upload all the images specific to this room.', 'tourfic' ),
						),
						array(
							'id'          => 'bed',
							'type'        => 'number',
							'label'       => __( 'Number of Beds', 'tourfic' ),
							'subtitle'    => __( 'Number of beds available in the room.', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'adult',
							'type'        => 'number',
							'label'       => __( 'Number of Adults', 'tourfic' ),
							'subtitle'    => __( 'Max number of adults allowed in the room.', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'child',
							'type'        => 'number',
							'label'       => __( 'Number of Child', 'tourfic' ),
							'subtitle'    => __( 'Max number of children allowed in the room.', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'children_age_limit',
							'type'        => 'number',
							'is_pro'      => true,
							'label'       => __( 'Child age limit', 'tourfic' ),
							'subtitle'    => __( 'Maximum age of a children.', 'tourfic' ),
							'description' => __( 'keep blank if don\'t want to add', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'footage',
							'type'        => 'text',
							'label'       => __( 'Room Footage', 'tourfic' ),
							'subtitle'    => __( 'Specify Room Size (in sqft).', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'features',
							'type'        => 'select2',
							'label'       => __( 'Select Features', 'tourfic' ),
							'subtitle'    => __( 'For instance, select amenities like a Coffee Machine, Microwave Oven, Bathtub, and more as applicable. You need to create these features from the ', 'tourfic' ). '<a href="'.admin_url('edit-tags.php?taxonomy=hotel_feature&post_type=tf_hotel').'" target="_blank"><strong>' . __( 'features', 'tourfic' ) . '</strong></a>'.__( ' tab first.', 'tourfic' ),
							'placeholder' => __( 'Select', 'tourfic' ),
							'multiple'    => true,
							'options'     => 'terms',
							'query_args'  => array(
								'taxonomy'   => 'hotel_feature',
								'hide_empty' => false,
							),
							'field_width' => 100,
						),
						array(
							'id'    => 'description',
							'type'  => 'editor',
							'label' => __( 'Room Description', 'tourfic' ),
							'subtitle'    => __( 'Add description specific for this room.', 'tourfic' ),
						),
						array(
							'id'      => 'minimum_maximum_stay_requirements',
							'type'    => 'heading',
							'content' => __( 'Stay Requirements', 'tourfic' ),
							'class'   => 'tf-field-class',
						),
						array(
							'id'          => 'minimum_stay_requirement',
							'type'        => 'number',
							'label'       => __( 'Minimum Stay', 'tourfic' ),
							'subtitle'    => __( 'Specify the minimum number of nights required to book this room.', 'tourfic' ),
							'attributes'  => array(
								'min' => '1',
							),
							'default'     => '1',
							'field_width' => 50,
						),
						array(
							'id'          => 'maximum_stay_requirement',
							'type'        => 'number',
							'label'       => __( 'Maximum Stay', 'tourfic' ),
							'subtitle'    => __( 'Indicate the maximum number of nights a guest can book this room for.', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'      => 'Pricing',
							'type'    => 'heading',
							'content' => __( 'Pricing', 'tourfic' ),
							'class'   => 'tf-field-class',
						),
						array(
							'id'      => 'pricing-by',
							'type'    => 'select',
							'label'   => __( 'Room Pricing Logic', 'tourfic' ),
							'options' => array(
								'1' => __( 'Per Room Basis', 'tourfic' ),
								'2' => __( 'Per Person Basis (Pro)', 'tourfic' ),
							),
							'default' => '1',
							'attributes'  => array(
								'class' => 'tf_room_pricing_by',
							),
						),
						array(
							'id'         => 'price',
							'type'       => 'text',
							'label'      => __( 'Insert Your Price', 'tourfic' ),
							'subtitle'   => __( 'Enter the per-night rate for the room.', 'tourfic' ),
							'dependency' => array( 'pricing-by', '==', '1' ),
						),
						array(
							'id'          => '',
							'type'        => 'text',
							'label'       => __( 'Price per Adult', 'tourfic' ),
							'is_pro'      => true,
							'dependency'  => array( 'pricing-by', '==', '2' ),
							'field_width' => 50,
						),

						array(
							'id'          => '',
							'type'        => 'text',
							'label'       => __( 'Price per Children', 'tourfic' ),
							'is_pro'      => true,
							'dependency'  => array( 'pricing-by', '==', '2' ),
							'field_width' => 50,
						),
						array(
							'id'       => 'discount_hotel_type',
							'type'     => 'select',
							'label'    => __( 'Discount Type', 'tourfic' ),
							'subtitle' => __( 'Set a discount for this room to incentivize bookings. Choose between a fixed amount off or a percentage-based reduction.', 'tourfic' ),
							'options'  => array(
								'none'    => __( 'None', 'tourfic' ),
								'percent' => __( 'Percent', 'tourfic' ),
								'fixed'   => __( 'Fixed', 'tourfic' ),
							),
							'default'  => 'none',
						),
						array(
							'id'         => 'discount_hotel_price',
							'type'       => 'number',
							'label'      => __( 'Discount Price', 'tourfic' ),
							'subtitle'   => __( 'Insert amount only', 'tourfic' ),
							'attributes' => array(
								'min' => '0',
							),
							'dependency' => array(
								array( 'discount_hotel_type', '!=', 'none' ),
							),
						),
						array(
							'id'        => 'price_multi_day',
							'type'      => 'switch',
							'label'     => __( 'Multiply Pricing By Night', 'tourfic' ),
							'subtitle'  => __( 'The total booking cost is calculated by multiplying the nightly rate by the number of nights stayed, from check-in to check-out.', 'tourfic' ),
							'label_on'  => __( 'Yes', 'tourfic' ),
							'label_off' => __( 'No', 'tourfic' ),
							'default'   => true,
						),
						array(
							'id'      => 'Availability',
							'type'    => 'heading',
							'content' => __( 'Availability Settings', 'tourfic' ),
							'class'   => 'tf-field-class',
						),
						array(
							'id'          => 'num-room',
							'type'        => 'number',
							'label'       => __( 'Room Availability', 'tourfic' ),
							'subtitle'    => __( 'Number of rooms available for booking', 'tourfic' ),
							'field_width' => 100,
							'attributes'  => array(
								'min' => '0',
							),
						),
						array(
							'id'        => 'reduce_num_room',
							'type'      => 'switch',
							'label'     => __( 'Room Inventory Management', 'tourfic' ),
							'subtitle'  => __( 'Decrease the inventory count for each room booked to reflect current availability accurately.', 'tourfic' ),
							'label_on'  => __( 'Yes', 'tourfic' ),
							'label_off' => __( 'No', 'tourfic' ),
							'default'   => false,
						),
						array(
							'id'      => '',
							'type'    => 'switch',
							'label'   => __( 'Enable Availability by Date', 'tourfic' ),
							'is_pro'  => true,
							'default' => true,
							'attributes'  => array(
								'class' => 'tf_room_availability_by_date',
							),
						),
						array(
							'id'        => '',
							'type'      => 'hotelAvailabilityCal',
							'label'     => __( 'Availability Calendar', 'tourfic' ),
							'is_pro'  => true,
							'dependency' => array( 'avil_by_date', '!=', 'false' ),
						),
						array(
							'id'      => 'Deposit',
							'type'    => 'heading',
							'content' => __( 'Deposit', 'tourfic' ),
							'class'   => 'tf-field-class',
						),
						array(
							'id'      => '',
							'type'    => 'switch',
							'label'   => __( 'Enable Deposit', 'tourfic' ),
							'is_pro'  => true,
							'default' => false,
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
							'button_class'   => 'room-ical-import',
							'attributes'  => array(
								'class' => 'ical_url_input',
							),
							'is_pro'      => true
						)
					),
				)
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
			'title'  => __( 'Labels', 'tourfic' ),
			'icon'   => 'fa fa-list',
			'fields' => array(
				array(
					'id'      => 'tf-hotel-tags-heading',
					'type'    => 'heading',
					'label' => __( 'Hotel labels', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'           => 'tf-hotel-tags',
					'type'         => 'repeater',
					'label'        => __( 'Labels', 'tourfic' ),
					'subtitle' => __('Add some keywords that highlight your hotel\'s Unique Selling Point (USP). This label will be displayed on both the Archive Page and the Search Results Page.', 'tourfic'),
					'button_title' => __( 'Add / Insert New Label', 'tourfic' ),
					'fields'       => array(

						array(
							'id'    => 'hotel-tag-title',
							'type'  => 'text',
							'label' => __( 'Label Title', 'tourfic' ),
						),

						array(
							'id'       => 'hotel-tag-color-settings',
							'type'     => 'color',
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
