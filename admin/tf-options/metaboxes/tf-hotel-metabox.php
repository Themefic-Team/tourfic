<?php
// don't load directly
defined( 'ABSPATH' ) || exit;
$badge_up     = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span></div>';
$badge_pro    = '<div class="tf-csf-badge"><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';

if(!function_exists('tf_hotel_amenities_categories')) {
	function tf_hotel_amenities_categories() {
		$amenities_cats = ! empty( tf_data_types( tfopt( 'hotel_amenities_cats' ) ) ) ? tf_data_types( tfopt( 'hotel_amenities_cats' ) ) : '';
		$all_cats       = [];
		if ( ! empty( $amenities_cats ) && is_array( $amenities_cats ) ) {
			foreach ( $amenities_cats as $key => $cat ) {
				$all_cats[ (string) $key ] = $cat['hotel_amenities_cat_name'];
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
					'id'        => 'featured',
					'type'      => 'switch',
					'label'     => __( 'Featured Hotel', 'tourfic' ),
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
					'label'    => __( 'Hotel Page Layout', 'tourfic' ),
					'subtitle' => __( 'Select your Layout logic', 'tourfic' ),
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
			),
		),
		'location'         => array(
			'title'  => __( 'Location', 'tourfic' ),
			'icon'   => 'fa-solid fa-location-dot',
			'fields' => array(
				array(
					'id'          => 'address',
					'type'        => 'textarea',
					'label'       => __( 'Hotel Address', 'tourfic' ),
					'subtitle'    => __( 'The address you want to show below the Hotel Title', 'tourfic' ),
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
					'subtitle' => __( 'Write your desired address and select the address from the suggestions. This address will be used to hyperlink the hotel address on the frontend.', 'tourfic' ),
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

				// Amenities
				array(
					'id'      => 'amenities-heading',
					'type'    => 'heading',
					'content' => __( 'Hotel Aminites', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'          => 'amenities-section-title',
					'type'        => 'text',
					'label'       => __( 'Amenities Title', 'tourfic' ),
					'placeholder' => __( "What this place offers", 'tourfic' ),
					'default' => __( "What this place offers", 'tourfic' ),
					'attributes'  => array(
						'required' => 'required',
					),
				),
				array(
					'id'           => 'hotel-amenities',
					'type'         => 'repeater',
					'label'        => __( 'Insert / Create Hotel Amenities', 'tourfic' ),
					'button_title' => __( 'Add New', 'tourfic' ),
					'class'        => 'tf-field-class',
					'fields'       => array(
						array(
							'id'          => 'amenities-feature',
							'type'        => 'select2',
							'label'       => __( 'Amenities Feature', 'tourfic' ),
							'placeholder' => __( 'Select amenities feature', 'tourfic' ),
							'options'     => 'terms',
							'query_args'  => array(
								'taxonomy'   => 'hotel_feature',
								'hide_empty' => false,
							),
							'field_width' => 50,
						),
						array(
							'id'          => 'amenities-category',
							'type'        => 'select2',
							'label'       => __( 'Amenities Category', 'tourfic' ),
							'placeholder' => __( 'Select amenities category', 'tourfic' ),
							'options'     => tf_hotel_amenities_categories(),
							'description' => __( 'Add new category from <a target="_blank" href="'.admin_url('admin.php?page=tf_settings#tab=single_page').'">Amenities Categories</a>', 'tourfic' ),
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
				), // Amenities end
			),
		),
		// Hotel Details
		'hotel_details'    => array(
			'title'  => __( 'Gallery & Video', 'tourfic' ),
			'icon'   => 'fa-solid fa-hotel',
			'fields' => array(
				array(
					'id'       => 'gallery',
					'type'     => 'gallery',
					'label'    => __( 'Hotel Gallery', 'tourfic' ),
					'subtitle' => __( 'Upload one or many images to create a hotel image gallery for customers. This is common gallery visible at the top part of the hotel page', 'tourfic' ),
				),
				array(
					'id'          => 'video',
					'type'        => 'text',
					'label'       => __( 'Hotel Video', 'tourfic' ),
					'subtitle'    => __( 'Enter YouTube/Vimeo URL here', 'tourfic' ),
					'placeholder' => __( 'Input full url here', 'tourfic' ),
				),
			),
		),
		// Hotel Details
		'hotel_service'    => array(
			'title'  => __( 'Hotel Services', 'tourfic' ),
			'icon'   => 'fa-solid fa-van-shuttle',
			'fields' => array(
				array(
					'id'      => 'hotel-service',
					'type'    => 'switch',
					'label'   => __( 'Airport Pickup Service', 'tourfic' ),
					'default' => true,
					'is_pro'  => true,
				)
			),
		),
		// Room Details
		'room_details'     => array(
			'title'  => __( 'Room Management', 'tourfic' ),
			'icon'   => 'fa-sharp fa-solid fa-door-open',
			'fields' => array(
				array(
					'id'    => 'room-section-title',
					'type'  => 'text',
					'label' => __( 'Section Title', 'tourfic' ),
					'default' => "Available Rooms"
				),
				array(
					'id'           => 'room',
					'type'         => 'repeater',
					'label'        => __( 'Insert / Create your hotel rooms', 'tourfic' ),
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
							'label'   => __( 'Room Thumbnail', 'tourfic' ),
							'subtitle' => __( 'Upload Thumbnail for this room', 'tourfic' ),
							'library' => 'image',
						),
						array(
							'id'       => 'gallery',
							'type'     => 'gallery',
							'label'    => __( 'Room Gallery', 'tourfic' ),
							'subtitle' => __( 'Upload images specific to this room', 'tourfic' ),
							'is_pro'   => true,
						),
						array(
							'id'          => 'bed',
							'type'        => 'number',
							'label'       => __( 'Number of Beds', 'tourfic' ),
							'subtitle'    => __( 'Number of beds available in the room', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'adult',
							'type'        => 'number',
							'label'       => __( 'Number of Adults', 'tourfic' ),
							'subtitle'    => __( 'Max number of persons allowed in the room', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'child',
							'type'        => 'number',
							'label'       => __( 'Number of Children', 'tourfic' ),
							'subtitle'    => __( 'Max number of children allowed in the room', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'children_age_limit',
							'type'        => 'number',
							'is_pro'      => true,
							'label'       => __( 'Children age limit', 'tourfic' ),
							'subtitle'    => __( 'Maximum age of a children', 'tourfic' ),
							'description' => __( 'keep blank if don\'t want to add', 'tourfic' ),
							'attributes'  => array(
								'min' => '0',
							),
							'field_width' => 33.33,
						),
						array(
							'id'          => 'footage',
							'type'        => 'text',
							'label'       => __( 'Room Footage', 'tourfic' ),
							'subtitle'    => __( 'Room footage (in sft)', 'tourfic' ),
							'field_width' => 50,
						),
						array(
							'id'          => 'features',
							'type'        => 'select2',
							'label'       => __( 'Select Features', 'tourfic' ),
							'subtitle'    => __( 'e.g. Coffee Machine, Microwave Oven (Select as many as applicable). You need to create these features from the “Features” tab.', 'tourfic' ),
							'placeholder' => __( 'Select', 'tourfic' ),
							'multiple'    => true,
							'options'     => 'terms',
							'query_args'  => array(
								'taxonomy'   => 'hotel_feature',
								'hide_empty' => false,
							),
							'field_width' => 50,
						),
						array(
							'id'    => 'description',
							'type'  => 'editor',
							'label' => __( 'Room Description', 'tourfic' ),
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
							'label'       => __( 'Minimum Stay Requirement', 'tourfic' ),
							'subtitle'    => __( 'Minimum number of nights required to book this room', 'tourfic' ),
							'attributes'  => array(
								'min' => '1',
							),
							'default'     => '1',
							'field_width' => 50,
						),
						array(
							'id'          => 'maximum_stay_requirement',
							'type'        => 'number',
							'label'       => __( 'Maximum Stay Requirement', 'tourfic' ),
							'subtitle'    => __( 'Maximum number of nights allowed to book this room', 'tourfic' ),
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
							'label'   => __( 'Room Pricing Type', 'tourfic' ),
							'options' => array(
								'1' => __( 'Per room', 'tourfic' ),
								'2' => __( 'Per person (Pro)', 'tourfic' ),
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
							'subtitle'   => __( 'The price of room per one night', 'tourfic' ),
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
							'subtitle' => __( 'Select Discount Type ( Percentage / Fixed )', 'tourfic' ),
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
							'subtitle'  => __( 'During booking, pricing will be multiplied by number of nights (Check-in to Check-out)', 'tourfic' ),
							'label_on'  => __( 'Yes', 'tourfic' ),
							'label_off' => __( 'No', 'tourfic' ),
							'default'   => true,
						),
						array(
							'id'      => 'Booking-Type',
							'type'    => 'heading',
							'content' => __( 'Booking', 'tourfic' ),
							'class'   => 'tf-field-class',
						),
						array(
							'id'      => 'booking-by',
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
							'is_pro'  => true
						),
						array(
							'id'        => '',
							'type'      => 'switch',
							'label'     => __( 'Allow Attribute', 'tourfic' ),
							'subtitle'  => __( 'If attribute allow, You can able to add custom Attribute', 'tourfic' ),
							'label_on'  => __( 'Yes', 'tourfic' ),
							'label_off' => __( 'No', 'tourfic' ),
							'is_pro'  => true
						),
						array(
							'id'          => '',
							'type'        => 'textarea',
							'label'       => __( 'Query Attribute', 'tourfic' ),
							'placeholder' => __( 'adult={adult}&child={child}&room={room}', 'tourfic' ),
							'is_pro'  => true
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
							'is_pro'  => true
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
						),
						array(
							'id'      => 'Availability',
							'type'    => 'heading',
							'content' => __( 'Availability', 'tourfic' ),
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
							'id'        => '',
							'type'      => 'switch',
							'is_pro'    => true,
							'label'     => __( 'Room Inventory Management', 'tourfic' ),
							'subtitle'  => __( 'Reduce total number of available rooms once a rooms is booked by a customer', 'tourfic' ),
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
					),
				)
			),
		),
		// FAQ Details
		'faq'              => array(
			'title'  => __( 'F.A.Q', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard-question',
			'fields' => array(
				array(
					'id'    => 'faq-section-title',
					'type'  => 'text',
					'label' => __( 'Section Title', 'tourfic' ),
					'default' => "Faq’s"
				),
				array(
					'id'           => 'faq',
					'type'         => 'repeater',
					'label'        => __( 'Frequently Asked Questions', 'tourfic' ),
					'button_title' => __( 'Add FAQ', 'tourfic' ),
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
			),
		),
		// Terms & conditions
		'terms_conditions' => array(
			'title'  => __( 'Terms & Conditions', 'tourfic' ),
			'icon'   => 'fa-regular fa-square-check',
			'fields' => array(
				array(
					'id'    => 'tc-section-title',
					'type'  => 'text',
					'label' => __( 'Section Title', 'tourfic' ),
					'default' => "Hotel Terms & Conditions"
				),
				array(
					'id'    => 'tc',
					'type'  => 'editor',
					'label' => __( 'Hotel Terms & Conditions', 'tourfic' ),
				),
			),
		),
		// Settings
		'settings'         => array(
			'title'  => __( 'Settings', 'tourfic' ),
			'icon'   => 'fa-solid fa-viruses',
			'fields' => array(
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
					'id'      => 'notice',
					'type'    => 'notice',
					'notice'  => 'info',
					'content' => __( 'These settings will overwrite global settings', 'tourfic' ),
				),
				array(
					'id'      => 'popular-feature',
					'type'    => 'heading',
					'content' => __( 'Popular Features', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'    => 'popular-section-title',
					'type'  => 'text',
					'label' => __( 'Popular Features Section Title', 'tourfic' ),
					'default' => "Popular Features"
				),
				array(
					'id'      => 'review-sections',
					'type'    => 'heading',
					'content' => __( 'Review', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'    => 'review-section-title',
					'type'  => 'text',
					'label' => __( 'Reviews Section Title', 'tourfic' ),
					'default' => "Average Guest Reviews"
				),
				array(
					'id'      => 'enquiry-section',
					'type'    => 'heading',
					'content' => __( 'Enquiry', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'        => 'h-enquiry-section',
					'type'      => 'switch',
					'label'     => __( 'Hotel Enquiry Option', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => true
				),
				array(
					'id'    => 'h-enquiry-option-title',
					'type'  => 'text',
					'label' => __( 'Hotel Enquiry Title Text', 'tourfic' ),
					'default' => "Have a question in mind",
					'dependency' => array( 'h-enquiry-section', '==', '1' ),
				),
				array(
					'id'    => 'h-enquiry-option-content',
					'type'  => 'text',
					'label' => __( 'Hotel Enquiry Short Text', 'tourfic' ),
					'default' => "Looking for more info? Send a question to the property to find out more.",
					'dependency' => array( 'h-enquiry-section', '==', '1' ),
				),
				array(
					'id'    => 'h-enquiry-option-btn',
					'type'  => 'text',
					'label' => __( 'Hotel Enquiry Button Text', 'tourfic' ),
					'default' => "Ask a Question",
					'dependency' => array( 'h-enquiry-section', '==', '1' ),
				),
			),
		),
	),
) );
