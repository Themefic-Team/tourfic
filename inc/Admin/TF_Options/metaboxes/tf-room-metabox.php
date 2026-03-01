<?php
// don't load directly
defined( 'ABSPATH' ) || exit;
$badge_up     = '<div class="tf-csf-badge"><span class="tf-upcoming">' .esc_html__( "Upcoming", "tourfic" ) . '</span></div>';
$badge_pro    = '<div class="tf-csf-badge"><span class="tf-pro">' .esc_html__( "Pro Feature", "tourfic" ) . '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' .esc_html__( "Upcoming", "tourfic" ) . '</span><span class="tf-pro">' .esc_html__( "Pro Feature", "tourfic" ) . '</span></div>';
$hotel_name = apply_filters( 'tf_hotel_post_type_name_change_singular', esc_html__( 'Hotel', 'tourfic' ) );
$hotels_name = apply_filters( 'tf_hotel_post_type_name_change_plural', esc_html__( 'Hotels', 'tourfic' ) );
$adults_name = apply_filters( 'tf_hotel_adults_title_change', esc_html__( 'Adult', 'tourfic' ) );

TF_Metabox::metabox( 'tf_room_opt', array(
	'title'     => 'Room Settings',
	'post_type' => 'tf_room',
	'sections'  => array(
		
		// Room Details
		'room_details_general'     => array(
			'title'  => esc_html__( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'hotel-room-heading',
					'type'  => 'heading',
					'title' => esc_html__( 'General Settings', 'tourfic' ),
					'content' => esc_html__( 'These are some common settings specific to this Room', 'tourfic' ),
                    'docs' => esc_url('https://themefic.com/docs/tourfic/how-it-works/room-management/')
				),
				array(
					'id'          => 'tf_hotel',
					'type'        => 'select2',
					'placeholder' => esc_html__( 'Select a Hotel', 'tourfic' ),
                    /* translators: %s is the hotel name */
                    'label'       => sprintf( esc_html__( 'Select %s (Required)', 'tourfic' ), $hotel_name ),
                    /* translators: %s is the lowercased hotel name */
                    'subtitle'    => sprintf( esc_html__( 'Select the %s where this room will be added', 'tourfic' ), strtolower( $hotel_name ) ),
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
					'label'       => esc_html__( 'Unique ID', 'tourfic' ),
					'attributes'  => array(
						'readonly' => 'readonly',
					),
					'default'     => uniqid(),
				),
                array(
                    'id'          => 'order_id',
                    'class'       => 'tf-order_id',
                    'type'        => 'text',
                    'label'       => esc_html__( 'Order ID', 'tourfic' ),
                    'attributes'  => array(
                        'readonly' => 'readonly',
                    ),
                    'is_search_able' => true
                ),
                array(
                    'id'        => 'enable',
                    'type'      => 'switch',
                    'label'     => esc_html__( 'Status', 'tourfic' ),
                    'subtitle'  => esc_html__( 'Enable/disable this Room', 'tourfic' ),
                    'label_on'  => esc_html__( 'Enabled', 'tourfic' ),
                    'label_off' => esc_html__( 'Disabled', 'tourfic' ),
                    'default'   => true,
                ),
				array(
					'id'       => 'gallery',
					'type'     => 'gallery',
					'label'    => esc_html__( 'Single Room Gallery', 'tourfic' ),
					'subtitle' => esc_html__( 'Upload all the images specific to this room.', 'tourfic' ),
				),
			),
		),

        'room_details'     => array(
			'title'  => esc_html__( 'Room Details', 'tourfic' ),
			'icon'   => 'fa-sharp fa-solid fa-door-open',
			'fields' => array(
                array(
                    'id'      => 'Details',
                    'type'    => 'heading',
                    'title' => esc_html__( 'Details', 'tourfic' ),
                    'class'   => 'tf-field-class',
                ),
                array(
                    'id'          => 'bed',
                    'type'        => 'number',
                    'label'       => esc_html__( 'Number of Beds', 'tourfic' ),
                    'subtitle'    => esc_html__( 'Number of beds available in the room.', 'tourfic' ),
                    'attributes'  => array(
                        'min' => '0',
                    ),
                    'field_width' => 33.33,
                ),
                array(
                    'id'          => 'adult',
                    'type'        => 'number',
                    /* translators: %s is the adults label/name */
                    'label'       => sprintf( esc_html__( 'Number of %s', 'tourfic' ), $adults_name ),
                    /* translators: %s is the lowercased adults label/name */
                    'subtitle'    => sprintf( esc_html__( 'Max number of %s allowed in the room.', 'tourfic' ), strtolower( $adults_name ) ),
                    'attributes'  => array(
                        'min' => '0',
                    ),
                    'field_width'    => 33.33,
                    'is_search_able' => true,
                ),                
                array(
                    'id'          => 'child',
                    'type'        => 'number',
                    'label'       => esc_html__( 'Number of Child', 'tourfic' ),
                    'subtitle'    => esc_html__( 'Max number of children allowed in the room.', 'tourfic' ),
                    'class'       => 'tf_room_child_field',
                    'attributes'  => array(
                        'min' => '0',
                    ),
                    'field_width' => 33.33,
                    'is_search_able' => true
                ),
                array(
                    'id'          => 'children_age_limit',
                    'type'        => 'number',
                    'label'       => esc_html__( 'Child age limit', 'tourfic' ),
                    'subtitle'    => esc_html__( 'Maximum age of a children.', 'tourfic' ),
                    'description' => esc_html__( 'keep blank if don\'t want to add', 'tourfic' ),
                    'class'       => 'tf_room_child_age_field',
                    'attributes'  => array(
                        'min' => '0',
                    ),
                    'field_width' => 50,
                ),

                array(
                    'id'          => 'footage',
                    'type'        => 'text',
                    'label'       => esc_html__( 'Room Footage', 'tourfic' ),
                    'subtitle'    => esc_html__( 'Specify Room Size', 'tourfic' ),
                    'field_width' => 50,
                ),
                array(
                    'id'          => 'features',
                    'type'        => 'select2',
                    'label'       => esc_html__( 'Select Features', 'tourfic' ),
                    'subtitle'    => esc_html__( 'For instance, select amenities like a Coffee Machine, Microwave Oven, Bathtub, and more as applicable. You need to create these features from the ', 'tourfic' ). '<a href="'.admin_url('edit-tags.php?taxonomy=hotel_feature&post_type=tf_hotel').'" target="_blank"><strong>' . esc_html__( 'features', 'tourfic' ) . '</strong></a>'.esc_html__( ' tab first.', 'tourfic' ),
                    'placeholder' => esc_html__( 'Select', 'tourfic' ),
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
			'title'  => esc_html__( 'Booking', 'tourfic' ),
			'icon'   => 'fa-solid fa-bed',
			'fields' => array(
                array(
                    'id'      => 'minimum_maximum_stay_requirements',
                    'type'    => 'heading',
                    'title' => esc_html__( 'Stay Requirements', 'tourfic' ),
                    'class'   => 'tf-field-class',
                ),
                array(
                    'id'          => 'minimum_stay_requirement',
                    'type'        => 'number',
                    'label'       => esc_html__( 'Minimum Stay', 'tourfic' ),
                    'subtitle'    => esc_html__( 'Specify the minimum number of nights required to book this room.', 'tourfic' ),
                    'attributes'  => array(
                        'min' => '1',
                    ),
                    'default'     => '1',
                    'field_width' => 50,
                ),
                array(
                    'id'          => 'maximum_stay_requirement',
                    'type'        => 'number',
                    'label'       => esc_html__( 'Maximum Stay', 'tourfic' ),
                    'subtitle'    => esc_html__( 'Indicate the maximum number of nights a guest can book this room for.', 'tourfic' ),
                    'field_width' => 50,
                ),
                array(
					'id'    => 'room-cancellation-heading',
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

        'room_price'     => array(
			'title'  => esc_html__( 'Room Price', 'tourfic' ),
			'icon'   => 'fa-regular fa-money-bill-1',
			'fields' => array(
                array(
                    'id'      => 'Room Pricing',
                    'type'    => 'heading',
                    'title' => esc_html__( 'Pricing', 'tourfic' ),
                    'class'   => 'tf-field-class',
                ),
                array(
                    'id'      => 'pricing-by',
                    'type'    => 'select',
                    'label'   => esc_html__( 'Room Pricing Logic', 'tourfic' ),
                    'options' => array(
                        '1' => esc_html__( 'Room Basis', 'tourfic' ),
                        '2' => esc_html__( 'Person Basis (Pro)', 'tourfic' ),
                        '3' => esc_html__( 'Option Basis (Pro)', 'tourfic' ),
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
                    'label'      => esc_html__( 'Insert Your Price', 'tourfic' ),
                    'subtitle'   => esc_html__( 'Enter the per-night rate for the room.', 'tourfic' ),
                    'dependency' => array( 'pricing-by', '==', '1' ),
                    'is_search_able' => true
                ),
                array(
                    'id'          => '',
                    'type'        => 'text',
                    /* translators: %s is the adults label/name */
                    'label'       => sprintf( esc_html__( 'Price per %s', 'tourfic' ), $adults_name ),
                    'is_pro'      => true,
                    'dependency'  => array( 'pricing-by', '==', '2' ),
                    'field_width' => 50,
                ),                

                array(
                    'id'          => '',
                    'type'        => 'text',
                    'label'       => esc_html__( 'Price per Children', 'tourfic' ),
                    'is_pro'      => true,
                    'dependency'  => array( 'pricing-by', '==', '2' ),
                    'field_width' => 50,
                ),
                array(
                    'id'       => 'discount_hotel_type',
                    'type'     => 'select',
                    'label'    => esc_html__( 'Discount Type', 'tourfic' ),
                    'subtitle' => esc_html__( 'Set a discount for this room to incentivize bookings. Choose between a fixed amount off or a percentage-based reduction.', 'tourfic' ),
                    'options'  => array(
                        'none'    => esc_html__( 'None', 'tourfic' ),
                        'percent' => esc_html__( 'Percent', 'tourfic' ),
                        'fixed'   => esc_html__( 'Fixed', 'tourfic' ),
                    ),
                    'default'  => 'none',
                ),
                array(
                    'id'         => 'discount_hotel_price',
                    'type'       => 'number',
                    'label'      => esc_html__( 'Discount Price', 'tourfic' ),
                    'subtitle'   => esc_html__( 'Insert amount only', 'tourfic' ),
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
                    'label'     => esc_html__( 'Multiply Pricing By Night', 'tourfic' ),
                    'subtitle'  => esc_html__( 'The total booking cost is calculated by multiplying the nightly rate by the number of nights stayed, from check-in to check-out.', 'tourfic' ),
                    'label_on'  => esc_html__( 'Yes', 'tourfic' ),
                    'label_off' => esc_html__( 'No', 'tourfic' ),
                    'default'   => true,
                ),

                array(
                    'id'      => 'Deposit',
                    'type'    => 'heading',
                    'title' => esc_html__( 'Deposit', 'tourfic' ),
                    'class'   => 'tf-field-class',
                ),
                array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'We\'re offering some extra features like <b>deposit</b> in our pro plan. <a href="https://tourfic.com/" target="_blank">Upgrade to our pro package today to take advantage of these fantastic options!</a>', 'tourfic' )),
				),
			),
		),

        'room_availability'     => array(
			'title'  => esc_html__( 'Availability', 'tourfic' ),
			'icon'   => 'fa-solid fa-clipboard',
			'fields' => array(
                array(
                    'id'      => 'Availability',
                    'type'    => 'heading',
                    'title' => esc_html__( 'Availability Settings', 'tourfic' ),
                    'class'   => 'tf-field-class',
                ),
                array(
                    'id'          => 'num-room',
                    'type'        => 'number',
                    'label'       => esc_html__( 'Room Availability', 'tourfic' ),
                    'subtitle'    => esc_html__( 'Number of rooms available for booking', 'tourfic' ),
                    'attributes'  => array(
                        'min' => '0',
                    ),
                    'field_width' => 100,
                    'is_search_able' => true
                ),
                array(
                    'id'        => 'reduce_num_room',
                    'type'      => 'switch',
                    'label'     => esc_html__( 'Room Inventory Management', 'tourfic' ),
                    'subtitle'  => esc_html__( 'Decrease the inventory count for each room booked to reflect current availability accurately.', 'tourfic' ),
                    'label_on'  => esc_html__( 'Yes', 'tourfic' ),
                    'label_off' => esc_html__( 'No', 'tourfic' ),
                    'default'   => false,
                    'is_search_able' => true
                ),
               
                array(
                    'id'        => '',
                    'type'      => 'room_availability',
                    'label'     => esc_html__( 'Availability Calendar', 'tourfic' ),
                    'is_pro'  => true,
                    'dependency' => array( 'avil_by_date', '!=', 'false' ),
                ),
                array(
                    'id'         => 'tf-others-heading',
                    'type'       => 'heading',
                    'title'    => esc_html__( 'Other', 'tourfic' ),
                    'dependency' => array( 'reduce_num_room', '==', '1' ),
                    'class'      => 'tf-field-class',
                ),
                array(
                    'id'         => 'tf-callback',
                    'type'       => 'callback',
                    'dependency' => array( 'reduce_num_room', '==', '1' ),
                    'function'   => 'tf_remove_order_ids_from_room',
                ),

                array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'We\'re offering some extra features like <b>Availability Calendar</b> in our pro plan. <a href="https://tourfic.com/" target="_blank">Upgrade to our pro package today to take advantage of these fantastic options!</a>', 'tourfic' )),
				),
			),
		),

        'room_ical'     => array(
			'title'  => esc_html__( 'iCal Sync', 'tourfic' ),
			'icon'   => 'fa-solid fa-calendar-days',
			'fields' => array(
                array(
                    'id'      => 'ical',
                    'type'    => 'heading',
                    'title' => esc_html__( 'iCal Sync', 'tourfic' ),
                ),
                array(
					'id'    => 'tf-pro-notice',
					'type'  => 'notice',
					'class' => 'tf-pro-notice',
					'notice' => 'info',
					'icon' => 'ri-information-fill',
					'content' => wp_kses_post(__( 'We\'re offering some extra features like <b>iCal synchronization</b> in our pro plan. <a href="https://tourfic.com/" target="_blank">Upgrade to our pro package today to take advantage of these fantastic options!</a>', 'tourfic' )),
				),
			),
		),

	),
) );
