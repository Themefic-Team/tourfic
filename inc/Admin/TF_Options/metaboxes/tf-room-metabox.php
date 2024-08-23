<?php
// don't load directly
defined( 'ABSPATH' ) || exit;
$badge_up     = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span></div>';
$badge_pro    = '<div class="tf-csf-badge"><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';


TF_Metabox::metabox( 'tf_room_opt', array(
	'title'     => 'Room Settings',
	'post_type' => 'tf_room',
	'sections'  => array(
		
		// Room Details
		'room_details_general'     => array(
			'title'  => __( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'hotel-room-heading',
					'type'  => 'heading',
					'label' => 'General Settings',
					'subtitle' => __( 'These are some common settings specific to this Room', 'tourfic' ),
				),
				array(
					'id'      => 'notice',
					'type'    => 'notice',
					'notice'  => 'info',
					'content' => __( 'This section includes Hotel Room Management settings.', 'tourfic' ). ' <a href="https://themefic.com/docs/tourfic/how-it-works/room-management/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . __( 'Check our Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'          => 'tf_hotel',
					'type'        => 'select2',
					'placeholder' => __( 'Select a Hotel', 'tourfic' ),
					'label'       => __( 'Select Hotel (Required)', 'tourfic' ),
					'subtitle'    => __( 'Select the hotel where this room will be added', 'tourfic' ),
					'options'     => 'posts',
					'query_args'  => array(
						'post_type'      => 'tf_hotel',
						'posts_per_page' => - 1,
					)
				),
				array(
					'id'          => 'unique_id',
					'class'       => 'unique-id',
					'type'        => 'text',
					'label'       => __( 'Unique ID', 'tourfic' ),
					'attributes'  => array(
						'readonly' => 'readonly',
					),
					'placeholder' => __( '', 'tourfic' ),
					'default'     => uniqid(),
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
                    'is_search_able' => true
                ),
                array(
                    'id'        => 'enable',
                    'type'      => 'switch',
                    'label'     => __( 'Status', 'tourfic' ),
                    'subtitle'  => __( 'Enable/disable this Room', 'tourfic' ),
                    'label_on'  => __( 'Enabled', 'tourfic' ),
                    'label_off' => __( 'Disabled', 'tourfic' ),
                    'width'     => 100,
                    'default'   => true,
                ),
				array(
					'id'       => 'gallery',
					'type'     => 'gallery',
					'label'    => __( 'Single Room Gallery', 'tourfic' ),
					'subtitle' => __( 'Upload all the images specific to this room.', 'tourfic' ),
				),
			),
		),

        'room_details'     => array(
			'title'  => __( 'Room Details', 'tourfic' ),
			'icon'   => 'fa-sharp fa-solid fa-door-open',
			'fields' => array(
                array(
                    'id'      => 'Details',
                    'type'    => 'heading',
                    'content' => __( 'Details', 'tourfic' ),
                    'class'   => 'tf-field-class',
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
                    'is_search_able' => true
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
                    'is_search_able' => true
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
                    'subtitle'    => __( 'Specify Room Size', 'tourfic' ),
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
			),
		),

        'room_stay_requirement'     => array(
			'title'  => __( 'Booking', 'tourfic' ),
			'icon'   => 'fa-solid fa-bed',
			'fields' => array(
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
			),
		),

        'room_price'     => array(
			'title'  => __( 'Room Price', 'tourfic' ),
			'icon'   => 'fa-regular fa-money-bill-1',
			'fields' => array(
                array(
                    'id'      => 'Room Pricing',
                    'type'    => 'heading',
                    'content' => __( 'Pricing', 'tourfic' ),
                    'class'   => 'tf-field-class',
                ),
                array(
                    'id'      => 'pricing-by',
                    'type'    => 'select',
                    'label'   => __( 'Room Pricing Logic', 'tourfic' ),
                    'options' => array(
                        '1' => __( 'Room Basis', 'tourfic' ),
                        '2' => __( 'Person Basis (Pro)', 'tourfic' ),
                        '3' => __( 'Option Basis (Pro)', 'tourfic' ),
                    ),
                    'default' => '1',
                    'attributes'  => array(
                        'class' => 'tf_room_pricing_by',
                    ),
                    'is_search_able' => true
                ),
                array(
                    'id'         => 'price',
                    'type'       => 'number',
                    'label'      => __( 'Insert Your Price', 'tourfic' ),
                    'subtitle'   => __( 'Enter the per-night rate for the room.', 'tourfic' ),
                    'dependency' => array( 'pricing-by', '==', '1' ),
                    'is_search_able' => true
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
                    'id'         => 'deposit_type',
                    'type'       => 'select',
                    'label'      => __( 'Deposit Type', 'tourfic' ),
                    'subtitle'   => __( 'Select Deposit Type: Percentage or Fixed', 'tourfic' ),
                    'options'    => array(
                        'none'    => __( 'None', 'tourfic' ),
                        'percent' => __( 'Percent', 'tourfic' ),
                        'fixed'   => __( 'Fixed', 'tourfic' ),
                    ),
                    'default'    => 'none',
                    'dependency' => array( 'allow_deposit', '!=', 'false' ),
                ),

                array(
                    'id'         => 'deposit_amount',
                    'type'       => 'number',
                    'label'      => __( 'Deposit Amount', 'tourfic' ),
                    'subtitle'   => __( 'Insert your deposit amount', 'tourfic' ),
                    'attributes' => array(
                        'min' => '0',
                    ),
                    'dependency' => array(
                        array( 'deposit_type', '!=', 'none' ),
                        // array( 'allow_deposit', '!=', 'false' ),
                    ),
                ),
			),
		),

        'room_availability'     => array(
			'title'  => __( 'Availability', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard',
			'fields' => array(
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
                    'attributes'  => array(
                        'min' => '0',
                    ),
                    'field_width' => 100,
                    'is_search_able' => true
                ),
                array(
                    'id'        => 'reduce_num_room',
                    'type'      => 'switch',
                    'label'     => __( 'Room Inventory Management', 'tourfic' ),
                    'subtitle'  => __( 'Decrease the inventory count for each room booked to reflect current availability accurately.', 'tourfic' ),
                    'label_on'  => __( 'Yes', 'tourfic' ),
                    'label_off' => __( 'No', 'tourfic' ),
                    'default'   => false,
                    'is_search_able' => true
                ),
                array(
                    'id'      => '',
                    'type'    => 'switch',
                    'label'   => __( 'Enable Availability by Date', 'tourfic' ),
                    'is_pro'  => true,
                    'default' => true,
                    'attributes'  => array(
                        'class' => 'tf_room_single_availability_by_date',
                    ),
                ),
                array(
                    'id'        => '',
                    'type'      => 'room_availability',
                    'label'     => __( 'Availability Calendar', 'tourfic' ),
                    'is_pro'  => true,
                    'dependency' => array( 'avil_by_date', '!=', 'false' ),
                ),
                array(
                    'id'         => 'tf-others-heading',
                    'type'       => 'heading',
                    'content'    => __( 'Other', 'tourfic' ),
                    'dependency' => array( 'reduce_num_room', '==', '1' ),
                    'class'      => 'tf-field-class',
                ),
                array(
                    'id'         => 'tf-callback',
                    'type'       => 'callback',
                    'dependency' => array( 'reduce_num_room', '==', '1' ),
                    'function'   => 'tf_remove_order_ids_from_room',
                ),
			),
		),

        'room_ical'     => array(
			'title'  => __( 'iCal Sync', 'tourfic' ),
			'icon'   => 'fa-solid fa-calendar-days',
			'fields' => array(
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
		),

	),
) );
