<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( file_exists( TF_ADMIN_PATH . 'tf-options/options/tf-menu-icon.php' ) ) {
	require_once TF_ADMIN_PATH . 'tf-options/options/tf-menu-icon.php';
} else {
	$menu_icon = 'dashicons-palmtree';
}

TF_Settings::option( 'tf_settings', array(
	'title'    => __( 'Tourfic Settings ', 'tourfic' ),
	'icon'     => $menu_icon,
	'position' => 25,
	'sections' => array(
		'general'            => array(
			'title'  => esc_html__( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'       => 'disable-services',
					'type'     => 'checkbox',
					'label'    => __( 'Disable Post Types', 'tourfic' ),
					'subtitle' => __( 'Tick the checkbox to disable the Post Type you don\'t need.', 'tourfic' ),
					'options'  => array(
						'hotel' => __( 'Hotels', 'tourfic' ),
						'tour'  => __( 'Tours', 'tourfic' ),
					),
				)
			),
		),
		'hotel_option'       => array(
			'title'  => esc_html__( 'Hotel Options', 'tourfic' ),
			'icon'   => 'fas fa-hotel',
			'fields' => array(),
		),
		'single_page'        => array(
			'title'  => esc_html__( 'Single Page', 'tourfic' ),
			'parent' => 'hotel_option',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'        => 'label_off_heading',
					'type'      => 'heading',
					'label'     => __( 'Global Settings for Single Hotel Page', 'tourfic' ),
					'sub_title' => __( 'These options can be overridden from Single Hotel Settings.', 'tourfic' ),
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
					'id'        => 'feature-filter',
					'type'      => 'switch',
					'label'     => __( 'Filter By Feature', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => true,
					'is_pro'    => true
				),
				array(
					'id'       => 'h-enquiry-email',
					'type'     => 'text',
					'label'    => __( 'Email for Enquiry Form', 'tourfic' ),
					'subtitle' => __( 'The Email to receive all enquiry form submissions', 'tourfic' ),
					'is_pro'   => true,
				)
			),
		),
		'room_config'        => array(
			'title'  => esc_html__( 'Room Config', 'tourfic' ),
			'parent' => 'hotel_option',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'hotel_room_heading',
					'type'  => 'heading',
					'label' => __( 'Global Configuration for Hotel Rooms', 'tourfic' ),
				),

				array(
					'id'       => '',
					'type'     => 'switch',
					'label'    => __( 'Children Age Limit', 'tourfic' ),
					'subtitle' => __( 'Turn on this option to set the Maximum age limit for Children. This can be overridden from Single Hotel Settings.', 'tourfic' ),
					'is_pro'   => true,
				),
				array(
					'id'         => '',
					'type'       => 'number',
					'label'      => __( 'Insert your Maximum Age Limit', 'tourfic' ),
					'subtitle'   => __( 'Numbers Only', 'tourfic' ),
					'attributes' => array(
						'min' => '0',
					),
					'is_pro'     => true,
				),
			),
		),
		// Hotel service Popup
		'payment_popup'      => array(
			'title'  => esc_html__( 'Popup Settings', 'tourfic' ),
			'parent' => 'hotel_option',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'hotel_popup_heading',
					'type'  => 'heading',
					'label' => __( 'Settings for Popup', 'tourfic' ),
				),
				array(
					'id'     => '',
					'type'   => 'text',
					'label'  => __( 'Popup Title', 'tourfic' ),
					'is_pro' => true,
				),

				array(
					'id'     => '',
					'type'   => 'textarea',
					'label'  => __( 'Popup Description', 'tourfic' ),
					'is_pro' => true,
				),

				array(
					'id'      => '',
					'type'    => 'text',
					'label'   => __( 'Popup Button Text', 'tourfic' ),
					'default' => __( 'Continue to booking', 'tourfic' ),
					'is_pro'  => true,
				)
			),
		),
		// Tour Options
		'tour'               => array(
			'title'  => __( 'Tour Options', 'tourfic' ),
			'icon'   => 'fas fa-umbrella-beach',
			'fields' => array(),
		),
		'single_tour'        => array(
			'title'  => esc_html__( 'Single Page', 'tourfic' ),
			'parent' => 'tour',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'       => 'signle_tour_heading',
					'type'     => 'heading',
					'label'    => __( 'Global Settings for Single Tours Page', 'tourfic' ),
					'subtitle' => __( 'These options can be overridden from Single Hotel Settings.', 'tourfic' ),
				),

				array(
					'id'        => 't-review',
					'type'      => 'switch',
					'label'     => __( 'Disable Review Section', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
				),

				array(
					'id'        => 't-related',
					'type'      => 'switch',
					'label'     => __( 'Disable Related Tour Section', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
				),
				array(
					'id'       => 'rt-title',
					'type'     => 'text',
					'label'    => __( 'Related Tour Title', 'tourfic' ),
					'subtitle' => __( 'This Title will show on single tour, Related tour Section as Section Title.', 'tourfic' ),
					'default' => __( 'You might also like', 'tourfic' ),
				),
				array(
					'id'       => 'rt-description',
					'type'     => 'text',
					'label'    => __( 'Related Tour Description', 'tourfic' ),
					'subtitle' => __( 'This Description will show on single tour, Related tour Section as Section Description.', 'tourfic' ),
					'default' => __( 'Travel is my life. Since 1999, I have been traveling around the world nonstop. If you also love travel, you are in the right place!', 'tourfic' ),
				),
				array(
					'id'      => 'rt-display',
					'type'    => 'radio',
					'is_pro'  => true,
					'label'   => __( 'Related tour display type', 'tourfic' ),
					'options' => array(
						'auto'     => __( 'Auto', 'tourfic' ),
						'selected' => __( 'Selected', 'tourfic' )
					),
					'default' => 'auto',
					'inline'  => true,
				),
				array(
					'id'         => 'tf-ralated-tours',
					'type'       => 'select2',
					'is_pro'     => true,
					'label'      => __( 'Choose related tours for single page', 'tourfic' ),
					'options'    => 'posts',
					'query_args' => array(
						'post_type'      => 'tf_tours',
						'posts_per_page' => - 1,
					),
				),
				array(
					'id'       => 't-enquiry-email',
					'type'     => 'text',
					'label'    => __( 'Email for Enquiry Form', 'tourfic' ),
					'subtitle' => __( 'The Email to receive all enquiry form submissions', 'tourfic' ),
					'is_pro'   => true,
				)
			),
		),
		// Partial Payment Popup
		'tour_payment_popup' => array(
			'title'  => esc_html__( 'Partial Payment', 'tourfic' ),
			'parent' => 'tour',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'signle_tour_heading',
					'type'  => 'heading',
					'label' => __( 'Settings for Partial Payment Popup', 'tourfic' ),
				),
				array(
					'id'      => '',
					'type'    => 'text',
					'label'   => __( 'Title', 'tourfic' ),
					'is_pro'  => true,
					'default' => __( 'Do you want to Partial Payment amount for booking the tour?', 'tourfic' ),
				),
				array(
					'id'      => '',
					'type'    => 'textarea',
					'label'   => __( 'Description', 'tourfic' ),
					'is_pro'  => true,
					'default' => __( 'You can Partial Payment amount for booking the tour. After booking the tour, you can pay the rest amount after the tour is completed.', 'tourfic' ),
				),
				array(
					'id'      => '',
					'type'    => 'text',
					'label'   => __( 'Text before the Price Amount', 'tourfic' ),
					'is_pro'  => true,
					'default' => __( 'Amount of Partial Payment on total price', 'tourfic' ),
				),
				array(
					'id'      => '',
					'type'    => 'text',
					'label'   => __( 'Text for Button (Full Payment)', 'tourfic' ),
					'is_pro'  => true,
					'default' => __( 'Pay full amount', 'tourfic' ),
				),
				array(
					'id'      => '',
					'type'    => 'text',
					'label'   => __( 'Text for Button (Partial Payment)', 'tourfic' ),
					'is_pro'  => true,
					'default' => __( 'Make a Partial Payment', 'tourfic' ),
				),
			),
		),
		// Itinerary Settings
		'tour_itinerary'     => array(
			'title'  => esc_html__( 'Itinerary Settings', 'tourfic' ),
			'parent' => 'tour',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'   => 'itinerary-builder-setings',
					'type' => 'tab',
					'tabs' => array(
						array(
							'id'     => 'itinerary-builder-setting',
							'title'  => 'Itinerary Builder Settings',
							'icon'   => 'fa fa-gear',
							'fields' => array(
								array(
									'id'       => 'itinerary-builder-heading',
									'type'     => 'heading',
									'subtitle' => __( 'You can create your own Itinerary using Tourfic\'s Itinerary builder. The builder can be found on Single Tour Settings', 'tourfic' ),
								),
								array(
									'id'           => '',
									'type'         => 'repeater',
									'label'        => __( 'Create Custom Itinerary options', 'tourfic' ),
									'button_title' => __( 'Add New Options', 'tourfic' ),
									'is_pro'       => true,
									'fields'       => array(
										array(
											'id'    => 'sleep-mode-title',
											'type'  => 'text',
											'label' => __( 'Field Title', 'tourfic' ),
										),
										array(
											'id'    => 'sleep-mode-icon',
											'type'  => 'icon',
											'label' => __( 'Field Icon', 'tourfic' ),
										),
									),
								),
								array(
									'id'           => '',
									'type'         => 'repeater',
									'button_title' => __( 'Add New Meal', 'tourfic' ),
									'label'        => __( 'Include Meal', 'tourfic' ),
									'is_pro'       => true,
									'fields'       => array(
										array(
											'id'    => 'meal',
											'type'  => 'text',
											'label' => __( 'Meal name', 'tourfic' ),
										),
									),
								),
								array(
									'id'      => '',
									'label'   => __( 'Elevation Input', 'tourfic' ),
									'type'    => 'select',
									'options' => [
										'Meter' => __( 'Meter', 'tourfic' ),
										'Feet'  => __( 'Feet', 'tourfic' ),
									],
									'is_pro'  => true,
									'default' => 'Meter',
								),
								array(
									'id'          => '',
									'type'        => 'switch',
									'is_pro'      => true,
									'label'       => __( 'Show Chart on Trip Page', 'tourfic' ),
									'field_width' => 50,
								),
								array(
									'id'          => '',
									'type'        => 'switch',
									'is_pro'      => true,
									'label'       => __( 'Always Show All Itinerary', 'tourfic' ),
									'field_width' => 50,
								),
								array(
									'id'          => '',
									'type'        => 'switch',
									'is_pro'      => true,
									'label'       => __( 'Show X-Axis', 'tourfic' ),
									'field_width' => 50,
								),
								array(
									'id'          => '',
									'type'        => 'switch',
									'is_pro'      => true,
									'label'       => __( 'Show Y-Axis', 'tourfic' ),
									'field_width' => 50,
								),
								array(
									'id'          => '',
									'type'        => 'switch',
									'is_pro'      => true,
									'label'       => __( 'Show line Graph', 'tourfic' ),
									'field_width' => 50,
								),
							),
						),
						array(
							'id'     => 'itinerary-downloader-setting',
							'title'  => 'Itinerary Downloader Settings',
							'icon'   => 'fa fa-gear',
							'fields' => array(
								array(
									'id'       => '',
									'type'     => 'switch',
									'is_pro'   => true,
									'label'    => __( 'Enable Itinerary Downloader', 'tourfic' ),
									'subtitle' => __( 'Enabling this will allow customers to download the itinerary plan in PDF format.', 'tourfic' ),
								),
								array(
									'id'      => 'tour_settings',
									'type'    => 'heading',
									'content' => __( 'Tour Settings in PDF', 'tourfic' ),
								),
								array(
									'id'          => '',
									'type'        => 'number',
									'label'       => __( 'Tour Thumbnail Height', 'tourfic' ),
									'field_width' => 50,
									'is_pro'      => true,
								),
								array(
									'id'          => '',
									'type'        => 'number',
									'label'       => __( 'Tour Thumbnail Width', 'tourfic' ),
									'field_width' => 50,
									'is_pro'      => true,
								),
								array(
									'id'      => 'companey_info_heading',
									'type'    => 'heading',
									'content' => __( 'Company Info', 'tourfic' ),
								),

								array(
									'id'     => '',
									'type'   => 'image',
									'is_pro' => true,
									'label'  => __( 'Company Logo', 'tourfic' ),
								),
								array(
									'id'     => '',
									'type'   => 'textarea',
									'is_pro' => true,
									'label'  => __( 'Short Company Description', 'tourfic' ),
								),
								array(
									'id'          => '',
									'type'        => 'text',
									'is_pro'      => true,
									'label'       => __( 'Company Email Address', 'tourfic' ),
									'field_width' => 33.33,
								),
								array(
									'id'          => '',
									'type'        => 'text',
									'is_pro'      => true,
									'label'       => __( 'Company Address', 'tourfic' ),
									'field_width' => 33.33,
								),
								array(
									'id'          => '',
									'type'        => 'text',
									'is_pro'      => true,
									'label'       => __( 'Company Phone', 'tourfic' ),
									'field_width' => 33.33,
								),
								array(
									'id'    => 'export_heading',
									'type'  => 'heading',
									'label' => __( 'Talk to Expert', 'tourfic' ),
								),
								array(
									'id'      => '',
									'type'    => 'switch',
									'is_pro'  => true,
									'label'   => __( 'Enable Talk To Expert - Section in PDF', 'tourfic' ),
									'default' => false,
								),
								array(
									'id'          => '',
									'type'        => 'text',
									'is_pro'      => true,
									'label'       => __( 'Talk to Expert - Label', 'tourfic' ),
									'field_width' => 25,
									'dependency'  => array(
										array( 'itinerary-expert', '==', 'true' ),
									),
								),
								array(
									'id'          => '',
									'type'        => 'text',
									'label'       => __( 'Expert Name', 'tourfic' ),
									'field_width' => 25,
									'is_pro'      => true,
									'dependency'  => array(
										array( 'itinerary-expert', '==', 'true' ),
									),
								),
								array(
									'id'          => '',
									'type'        => 'text',
									'label'       => __( 'Expert Email Address', 'tourfic' ),
									'field_width' => 25,
									'is_pro'      => true,
									'dependency'  => array(
										array( 'itinerary-expert', '==', 'true' ),
									),
								),
								array(
									'id'          => '',
									'type'        => 'text',
									'label'       => __( 'Expert Phone Address', 'tourfic' ),
									'field_width' => 25,
									'is_pro'      => true,
									'dependency'  => array(
										array( 'itinerary-expert', '==', 'true' ),
									),
								),
								array(
									'id'         => '',
									'type'       => 'image',
									'is_pro'     => true,
									'label'      => __( 'Expert Avatar Image', 'tourfic' ),
									'dependency' => array(
										array( 'itinerary-expert', '==', 'true' ),
									),
								),
								array(
									'id'         => '',
									'type'       => 'switch',
									'label'      => __( 'Viber Contact Available', 'tourfic' ),
									'is_pro'     => true,
									'dependency' => array(
										array( 'itinerary-expert', '==', 'true' ),
									),
								),
								array(
									'id'         => '',
									'type'       => 'switch',
									'is_pro'     => true,
									'label'      => __( 'WhatsApp Contact Available', 'tourfic' ),
									'dependency' => array(
										array( 'itinerary-expert', '==', 'true' ),
									),
								),
								array(
									'id'       => 'signle_tour_fonts',
									'type'     => 'heading',
									'label'    => __( 'PDF Downloader Font Support', 'tourfic' ),
									'subtitle' => __( 'If your site\'s language is not English, then upload your language font. Otherwise, your Downloader PDF may not work properly.', 'tourfic' ),
								),
								array(
									'id'     => '',
									'type'   => 'file',
									'label'  => __( 'Upload Fonts', 'tourfic' ),
									'is_pro' => true,
								),
							),
						),
					),
				),

			),
		),

		// Multi Vendor
		'vendor'             => array(
			'title'  => esc_html__( 'Multi Vendor', 'tourfic' ),
			'icon'   => 'fa fa-handshake',
			'fields' => array(
				array(
					'id'   => 'multi-vendor-setings',
					'type' => 'tab',
					'tabs' => array(
						array(
							'id'     => 'general-setting',
							'title'  => 'General Options',
							'icon'   => 'fa fa-gear',
							'fields' => array(
								array(
									'id'    => 'vendor-reg',
									'type'  => 'switch',
									'label' => __( 'Enable Vendor Registration', 'tourfic' ),
									'subtitle' => __('Visitor can register as vendor using the registration form','tourfic'),
									'is_pro'   => true,
								),
								array(
									'id'    => 'user_approval',
									'type'  => 'switch',
									'label' => __( 'Automatic Approval', 'tourfic' ),
									'subtitle' => __('Partner be automatic approval (register account).','tourfic'),
									'is_pro'   => true,
								),
								array(
									'id'        => 'reg-pop',
									'type'      => 'switch',
									'label'     => __( 'Registration Form Popup', 'tourfic' ),
									'subtitle'  => __( 'Add class <code>tf-reg-popup</code> to trigger the popup', 'tourfic' ),
									'is_pro'   => true,
								),

								array(
									'id'      => 'notice',
									'type'    => 'notice',
									'content' => __( 'Use shortcode <code>[tf_registration_form]</code> to show registration form in post/page/widget.', 'tourfic' ),
								),
								array(
									'id'    => 'email-verify',
									'type'  => 'switch',
									'label' => __( 'Email Verification', 'tourfic' ),
									'subtitle' => __('ON: Vendor must verify by email','tourfic'),
									'is_pro'   => true,
								),
								array(
									'id'    => 'partner_post',
									'type'  => 'switch',
									'label' => __( "Partner's Post Must be Approved by Admin", 'tourfic' ),
									'subtitle' => __('ON: When partner posts a service, it needs to be approved by administrator ','tourfic'),
									'is_pro'   => true,
								),
								array(
									'id'      => 'notice_shortcode',
									'type'    => 'notice',
									'content' => __( 'Use shortcode <code>[tf_login_form]</code> to show login form in post/page/widget.', 'tourfic' ),
								),
								array(
									'id'    => 'partner_commission',
									'type'  => 'number',
									'label' => __( 'Commission(%)', 'tourfic' ),
									'subtitle' => __('Enter commission of partner for admin after each item is booked ','tourfic'),'attributes'  => array(
										'min' => '0',
									),
									'is_pro'   => true,
								),
								array(
									'id'        => 'vendor-integrate-pabbly',
									'type'      => 'switch',
									'label'     => __( 'Enable Pabbly for New Vendor Registration?', 'tourfic' ),
									'subtitle' => __( 'You can able to Integrate Pabbly with New Vendor Registration.', 'tourfic' ),
									'label_on'  => __( 'Yes', 'tourfic' ),
									'label_off' => __( 'No', 'tourfic' ),
									'default'   => false,
									'is_pro'    => true,
								),
								array(
									'id'       => 'vendor-integrate-pabbly-webhook',
									'type'     => 'text',
									'label'    => __( 'Vendor Registration Web Hook', 'tourfic' ),
									'subtitle' => __( 'Enter Here Your Vendor Registration Pabbly Web Hook.', 'tourfic' ),
									'is_pro'   => true,
									'dependency'  => array(
										array( 'vendor-integrate-pabbly', '==', 'true' ),
									),
								),
							),
						),
						array(
							'id'     => 'layout-setting',
							'title'  => 'Vendor Dashboard',
							'icon'   => 'fa fa-gear',
							'fields' => array(
								array(
									'id'    => 'vendor-config',
									'type'  => 'switch',
									'label' => __( 'Configuration Partner Profile info', 'tourfic' ),
									'subtitle' => __('Show/hide sections for partner dashboard','tourfic'),
									'is_pro'   => true,
								),
								array(
									'id'    => 'vendor-earning',
									'type'  => 'switch',
									'label' => __( 'Show total Earning', 'tourfic' ),
									'subtitle' => __('ON: Display earnings information in accordance with time periods','tourfic'),
									'is_pro'   => true,
								),
								array(
									'id'    => 'vendor-each-earning',
									'type'  => 'switch',
									'label' => __( 'Show each service Earning', 'tourfic' ),
									'subtitle' => __('ON: Display earnings according to each service','tourfic'),
									'is_pro'   => true,
								),
								array(
									'id'    => 'vendor-earning-chart',
									'type'  => 'switch',
									'label' => __( 'Show Chart info', 'tourfic' ),
									'subtitle' => __('ON: Display visual graphs to follow your earnings through each time','tourfic'),
									'is_pro'   => true,
								),
								array(
									'id'    => 'vendor-booking-history',
									'type'  => 'switch',
									'label' => __( 'Show Booking history', 'tourfic' ),
									'subtitle' => __('ON: Show booking history of partner','tourfic'),
									'is_pro'   => true,
								),
								array(
									'id'    => 'vendor-enquiry-history',
									'type'  => 'switch',
									'label' => __( 'Show Enquiry history', 'tourfic' ),
									'subtitle' => __('ON: Show Enquiry history of partner','tourfic'),
									'is_pro'   => true,
								),
							),
						),
						array(
							'id'     => 'withdraw-setting',
							'title'  => 'Withdrawal Options',
							'icon'   => 'fa fa-gear',
							'fields' => array(
								array(
									'id'    => 'vendor-withdraw',
									'type'  => 'switch',
									'label' => __( 'Allow Request Withdrawal', 'tourfic' ),
									'subtitle' => __('ON: Partner is allowed to withdraw money','tourfic'),
									'is_pro'   => true,
								),
								array(
									'id'      => 'vendor_min_withdraw',
									'type'    => 'number',
									'label' => __( 'Minimum value request when withdrawal', 'tourfic' ),
									'subtitle' => __('Enter minimum value when a withdrawal is conducted','tourfic'),
									'attributes'  => array(
										'min' => '0',
									),
									'is_pro'   => true,
								),
								array(
									'id'      => 'vendor_withdraw_date',
									'type'    => 'number',
									'label' => __( 'Date of sucessful payment in current month', 'tourfic' ),
									'subtitle' => __('Enter the date monthly payment. Ex: 25','tourfic'),
									'attributes'  => array(
										'min' => '1',
										'max' => '28',
									),
									'is_pro'   => true,
								),
							),
						),
						array(
							'id'     => 'login-setting',
							'title'  => 'Social Login Options',
							'icon'   => 'fa fa-gear',
							'fields' => array(
								array(
									'id'    => 'vendor-google-login',
									'type'  => 'switch',
									'label' => __( 'Allow Google Login', 'tourfic' ),
									'subtitle' => __('ON: Partner is allowed to Google Login','tourfic'),
									'is_pro'   => true,
								),
								array(
									'id'      => 'app_id',
									'type'    => 'text',
									'label' => __( 'Google App Client ID', 'tourfic' ),
									'subtitle' => __('Enter the App ID','tourfic'),
									'dependency' => array(
										array( 'vendor-google-login', '==', true ),
									),
									'is_pro'   => true,
								),
								array(
									'id'      => 'app_secret_id',
									'type'    => 'text',
									'label' => __( 'Google App Client Secret', 'tourfic' ),
									'subtitle' => __('Enter the App Secret','tourfic'),
									'dependency' => array(
										array( 'vendor-google-login', '==', true ),
									),
									'is_pro'   => true,
								),
								array(
									'id'      => 'app_redirect_uri',
									'type'    => 'text',
									'label' => __( 'Google Redirect URI', 'tourfic' ),
									'subtitle' => __('Enter the Redirect URI','tourfic'),
									'dependency' => array(
										array( 'vendor-google-login', '==', true ),
									),
									'is_pro'   => true,
								),
							),
						),
						array(
							'id'     => 'vendor-registration-fields',
							'title'  => 'Custom Registration Fields',
							'icon'   => 'fa fa-gear',
							'fields' => array(
								array(
									'id'       => 'vendor-registration',
									'class'    => 'disable-sortable',
									'type'     => 'repeater',
									'button_title' => __( 'Add New', 'tourfic' ),
									'label'    => __( 'Registration Fields for Vendor', 'tourfic' ),
									'subtitle' => __( 'Custom fields allowed', 'tourfic' ),
									'is_pro'   => true,
									'fields'   => array(
										array(
											'id'    => 'reg-field-label',
											'type'  => 'text',
											'label' => __( 'Label', 'tourfic' ),
										),
										array(
											'id'    => 'reg-field-name',
											'type'  => 'text',
											'label' => __( 'Name', 'tourfic' ),
											'subtitle' => __( 'Space Not allowed (Ex: tf_name)', 'tourfic' ),
										),
										array(
											'id'      => 'reg-fields-type',
											'type'    => 'select',
											'label'   => __( 'Field Type', 'tourfic' ),
											'options' => array(
												'text' => __( 'Text', 'tourfic' ),
												'email' => __( 'Email', 'tourfic' ),
												'password' => __( 'Password', 'tourfic' ),
												'textarea' => __( 'Textarea', 'tourfic' ),
												'radio' => __( 'Radio', 'tourfic' ),
												'select' => __( 'Select', 'tourfic' ),
											),
										),
										array(
											'id'     => 'reg-options',
											'type'   => 'repeater',
											'button_title' => __( 'Add New Option', 'tourfic' ),
											'label'  => __( 'Option Label', 'tourfic' ),
											'dependency' => array(
												array( 'reg-fields-type', '==', 'radio' ),
											),
											'fields' => array(
												array(
													'label'   => __( 'Field Label', 'tourfic' ),
													'id'      => 'option-label',
													'type'    => 'text',
												),
												array(
													'label'   => __( 'Field Value', 'tourfic' ),
													'id'      => 'option-value',
													'type'    => 'text',
												),
											),
										),
										array(
											'id'     => 'reg-options',
											'type'   => 'repeater',
											'button_title' => __( 'Add New Option', 'tourfic' ),
											'label'  => __( 'Option Label', 'tourfic' ),
											'dependency' => array(
												array( 'reg-fields-type', '==', 'select' ),
											),
											'fields' => array(
												array(
													'label'   => __( 'Field Label', 'tourfic' ),
													'id'      => 'option-label',
													'type'    => 'text',
												),
												array(
													'label'   => __( 'Field Value', 'tourfic' ),
													'id'      => 'option-value',
													'type'    => 'text',
												),
											),
										),
										array(
											'id'    => 'reg-field-placeholder',
											'type'  => 'text',
											'label' => __( 'Placeholder', 'tourfic' ),
										),
										array(
											'id'    => 'reg-field-required',
											'type'  => 'switch',
											'label' => __( 'Required Field ?', 'tourfic' ),
										),

									),
								),
							),
						),
					),
				),
			),
		),
		// Search Options
		'search'             => array(
			'title'  => __( 'Search', 'tourfic' ),
			'icon'   => 'fas fa-search',
			'fields' => array(
				// Registration
				array(
					'id'          => 'search-result-page',
					'type'        => 'select2',
					'placeholder' => __( 'Select a page', 'tourfic' ),
					'label'       => __( 'Select Search Result Page', 'tourfic' ),
					'description' => __( 'This page will be used to show the Search form Results. Please make sure Page template: <code>Tourfic - Search Result</code> is selected while creating this page.', 'tourfic' ),
					'options'     => 'posts',
					'query_args'  => array(
						'post_type'      => 'page',
						'posts_per_page' => - 1,
					)
				),

				array(
					'id'       => 'posts_per_page',
					'type'     => 'number',
					'label'    => __( 'Search Items to show per page', 'tourfic' ),
					'subtitle' => __( 'Add the total number of hotels/tours you want to show per page on the Search result.', 'tourfic' ),
				),

				array(
					'id'        => 'date_hotel_search',
					'type'      => 'switch',
					'label'     => __( 'Date Required in Hotel Search', 'tourfic' ),
					'subtitle'  => __( 'Enable this option if you want the user to select their Checkin/Checkout date to search', 'tourfic' ),
					'is_pro'    => true,
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => false,
				),

				array(
					'id'        => 'date_tour_search',
					'type'      => 'switch',
					'label'     => __( 'Date Required in Tour Search', 'tourfic' ),
					'subtitle'  => __( 'Enable this option if you want the user to select their Tour date to search', 'tourfic' ),
					'is_pro'    => true,
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
				),
			),
		),
		// Design Options
		'design-panel'       => array(
			'title'  => __( 'Design Panel', 'tourfic' ),
			'icon'   => 'fas fa-palette',
			'fields' => array(),
		),
		'global_design'      => array(
			'title'  => __( 'Global', 'tourfic' ),
			'parent' => 'design-panel',
			'icon'   => 'fas fa-cogs',
			'fields' => array(
				array(
					'id'      => 'global_design_notice',
					'type'    => 'notice',
					'style'   => 'info',
					'content' => __( "To ensure maximum compatiblity with your theme, all Heading (h1-h6), Paragraph & Link's Color-Font Styles are not controlled by Tourfic. Those need to be edited using your Theme's option Panel.", "tourfic" ),
				),
				array(
					'id'       => 'tourfic-button-color',
					'type'     => 'color',
					'label'    => __( 'Button Color', 'tourfic' ),
					'subtitle' => __( 'Colors of all buttons related to Tourfic', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'regular' => __( 'Regular', 'tourfic' ),
						'hover'   => __( 'Hover', 'tourfic' ),
					),
				),
				array(
					'id'       => 'tourfic-button-bg-color',
					'type'     => 'color',
					'label'    => __( 'Button Background Color', 'tourfic' ),
					'subtitle' => __( 'Background Colors of all buttons related to Tourfic ', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'regular' => __( 'Regular', 'tourfic' ),
						'hover'   => __( 'Hover', 'tourfic' ),
					),
				),
				array(
					'id'       => 'tourfic-sidebar-booking',
					'type'     => 'color',
					'label'    => __( 'Sidebar Booking Form', 'tourfic' ),
					'subtitle' => __( 'The Gradient color of Sidebar Booking (Available on Search Result and Single Hotel Page)', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'gradient_one_reg' => __( 'Gradient One Color', 'tourfic' ),
						'gradient_two_reg' => __( 'Gradient Two Color', 'tourfic' ),
					)
				),
				array(
					'id'       => 'tourfic-faq-style',
					'type'     => 'color',
					'label'    => __( 'FAQ Styles', 'tourfic' ),
					'subtitle' => __( 'Style of FAQ Section for both Hotels and Tours', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'faq_color'        => __( 'Heading Color', 'tourfic' ),
						'faq_icon_color'   => __( 'Icon Color', 'tourfic' ),
						'faq_border_color' => __( 'Border Color', 'tourfic' ),
					)
				),
				array(
					'id'       => 'tourfic-review-style',
					'type'     => 'color',
					'label'    => __( 'Review Styles', 'tourfic' ),
					'subtitle' => __( 'Style of Review Section both Hotels and Tours', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'rating_color'          => __( 'Rating Color', 'tourfic' ),
						'rating_bg_color'       => __( 'Rating Background', 'tourfic' ),
						'param_bg_color'        => __( 'Parameter Background', 'tourfic' ),
						'param_single_bg_color' => __( 'Single Parameter', 'tourfic' ),
						'param_txt_color'       => __( 'Single Parameter Text', 'tourfic' ),
						'review_color'          => __( 'Review Color', 'tourfic' ),
						'review_bg_color'       => __( 'Review Background', 'tourfic' ),
					)
				),

			),
		),
		'hotel_design'       => array(
			'title'  => __( 'Hotel', 'tourfic' ),
			'parent' => 'design-panel',
			'icon'   => 'fas fa-hotel',
			'fields' => array(
				array(
					'id'      => 'hotel_design_heading',
					'type'    => 'heading',
					'content' => __( 'Hotel Settings', 'tourfic' ),
				),
				array(
					'id'       => 'tourfic-hotel-type-bg-color',
					'type'     => 'color',
					'label'    => __( 'Hotel Type Color', 'tourfic' ),
					'subtitle' => __( 'The "Hotel" text above main heading of single hotel ', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'regular' => __( 'Color', 'tourfic' ),
						'hover'   => __( 'Background Color', 'tourfic' ),
					)
				),
				array(
					'id'       => 'tourfic-hotel-share-icon',
					'type'     => 'color',
					'label'    => __( 'Share Icon Color', 'tourfic' ),
					'subtitle' => __( 'The color of the Share Icons', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'regular' => __( 'Regular', 'tourfic' ),
						'hover'   => __( 'Hover', 'tourfic' ),
					)
				),
				array(
					'id'       => 'tourfic-hotel-map-button',
					'type'     => 'color',
					'label'    => __( 'Map Button Background', 'tourfic' ),
					'subtitle' => __( 'Map Button Background Color (Gradient)', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'gradient_one_reg' => __( 'Gradient One Color', 'tourfic' ),
						'gradient_two_reg' => __( 'Gradient Two Color', 'tourfic' ),
						'gradient_one_hov' => __( 'Gradient One Hover', 'tourfic' ),
						'gradient_two_hov' => __( 'Gradient Two Hover', 'tourfic' ),
					)
				),
				array(
					'id'       => 'tourfic-hotel-map-button-text',
					'type'     => 'color',
					'label'    => __( 'Map Button Text Color', 'tourfic' ),
					'subtitle' => __( 'The text color of Map Button', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'regular' => __( 'Text Color', 'tourfic' ),
					)
				),
				array(
					'id'       => 'tourfic-hotel-features-color',
					'type'     => 'color',
					'label'    => __( 'Hotel Features Color', 'tourfic' ),
					'subtitle' => __( 'Features section icon color', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'regular' => __( 'Color', 'tourfic' ),
					)
				),
				array(
					'id'       => 'tourfic-hotel-table-style',
					'type'     => 'color',
					'label'    => __( 'Room Table Styles', 'tourfic' ),
					'subtitle' => __( 'Hotel Room Table styling options', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'table_color'        => __( 'Heading Color', 'tourfic' ),
						'table_bg_color'     => __( 'Heading Background Color', 'tourfic' ),
						'table_border_color' => __( 'Border Color', 'tourfic' ),
					)
				),
			),
		),
		'tour_design'        => array(
			'title'  => __( 'Tour', 'tourfic' ),
			'parent' => 'design-panel',
			'icon'   => 'fas fa-umbrella-beach',
			'fields' => array(
				array(
					'id'      => 'tour_design_heading',
					'type'    => 'heading',
					'content' => __( 'Tour Settings', 'tourfic' )
				),
				array(
					'id'       => 'tourfic-tour-pricing-color',
					'type'     => 'color',
					'label'    => __( 'Price Section', 'tourfic' ),
					'subtitle' => __( 'Styling of the Pricing Section', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'sale_price'      => __( 'Sale Price', 'tourfic' ),
						'org_price'       => __( 'Original Price', 'tourfic' ),
						'tab_text'        => __( 'Tab Text', 'tourfic' ),
						'tab_bg'          => __( 'Tab Background', 'tourfic' ),
						'active_tab_text' => __( 'Active Tab Text', 'tourfic' ),
						'active_tab_bg'   => __( 'Active Tab Background', 'tourfic' ),
						'tab_border'      => __( 'Tab Border', 'tourfic' ),
					)
				),
				array(
					'id'       => 'tourfic-tour-info-color',
					'type'     => 'color',
					'label'    => __( 'Information / Summary Section', 'tourfic' ),
					'subtitle' => __( 'Styling of the Info  / Summary', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'icon_color'    => __( 'Icon Color', 'tourfic' ),
						'heading_color' => __( 'Heading Color', 'tourfic' ),
						'text_color'    => __( 'Text Color', 'tourfic' ),
						'bg_one'        => __( 'Background One', 'tourfic' ),
						'bg_two'        => __( 'Background Two', 'tourfic' ),
						'bg_three'      => __( 'Background Three', 'tourfic' ),
						'bg_four'       => __( 'Background Four', 'tourfic' ),
					)
				),
				array(
					'id'       => 'tourfic-tour-sticky-booking',
					'type'     => 'color',
					'label'    => __( 'Sticky Booking', 'tourfic' ),
					'subtitle' => __( 'Styling of Sticky Booking Form', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'btn_col'         => __( 'Button Color', 'tourfic' ),
						'btn_bg'          => __( 'Button Background', 'tourfic' ),
						'btn_hov_col'     => __( 'Button Hover Color', 'tourfic' ),
						'btn_hov_bg'      => __( 'Button Hover Background', 'tourfic' ),
						'form_background' => __( 'Form Background', 'tourfic' ),
						'form_border'     => __( 'Form Border', 'tourfic' ),
					)
				),
				array(
					'id'       => 'tourfic-include-exclude',
					'type'     => 'color',
					'label'    => __( 'Include - Exclude Section', 'tourfic' ),
					'subtitle' => __( 'Styling of Include - Exclude Section', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'gradient_one_reg' => __( 'Gradient One Color', 'tourfic' ),
						'gradient_two_reg' => __( 'Gradient Two Color', 'tourfic' ),
						'heading_color'    => __( 'Heading Color', 'tourfic' ),
						'text_color'       => __( 'Text Color', 'tourfic' ),
					)
				),
				array(
					'id'       => 'tourfic-tour-itinerary',
					'type'     => 'color',
					'label'    => __( 'Travel Itinerary', 'tourfic' ),
					'subtitle' => __( 'Styling of Travel Itinerary', 'tourfic' ),
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'time_day_txt'  => __( 'Time or Day Text', 'tourfic' ),
						'time_day_bg'   => __( 'Time or Day Background', 'tourfic' ),
						'heading_color' => __( 'Heading Color', 'tourfic' ),
						'text_color'    => __( 'Text Color', 'tourfic' ),
						'bg_color'      => __( 'Background Color', 'tourfic' ),
						'icon_color'    => __( 'Icon Color', 'tourfic' ),
					)
				),
			),
		),

		// Miscellaneous Options
		'miscellaneous'      => array(
			'title'  => __( 'Miscellaneous', 'tourfic' ),
			'icon'   => 'fas fa-globe',
			'fields' => array(),
		),
		/**
		 * Google Map
		 *
		 * Sub Menu
		 */
		'map_settings'       => array(
			'title'  => __( 'Map Settings', 'tourfic' ),
			'parent' => 'miscellaneous',
			'icon'   => 'fas fa-umbrella-beach',
			'fields' => array(
				array(
					'id'      => 'map_settings_heading',
					'type'    => 'heading',
					'content' => __( 'Map Settings', 'tourfic' )
				),
				array(
					'id'       => 'google-page-option',
					'type'     => 'select',
					'label'    => __( 'Select Map', 'tourfic' ),
					'subtitle' => __( 'This map is used to dynamically search your hotel/tour location on the option panel. The frontend map information is based on this data. We use "OpenStreetMap” by default. You can also use Google Map. To use Google map, you need to insert your Google Map API Key.', 'tourfic' ),
					'options'  => array(
						'default' => __( 'Default Map', 'tourfic' ),
						''        => __( 'Google Map (Pro)', 'tourfic' ),
					),
					'default'  => 'default'
				),
				array(
					'id'         => '',
					'type'       => 'text',
					'label'      => __( 'Google Map API Key', 'tourfic' ),
					'dependency' => array(
						array( 'google-page-option', '==', 'googlemap' ),
					),
					'is_pro'     => true,
				),
			),
		),
		/**
		 * Wishlist
		 *
		 * Sub Menu
		 */
		'wishlist'           => array(
			'title'  => __( 'Wishlist', 'tourfic' ),
			'parent' => 'miscellaneous',
			'icon'   => 'fas fa-heart',
			'fields' => array(
				array(
					'id'      => 'wishlist_heading',
					'type'    => 'heading',
					'content' => __( 'Wishlist Settings', 'tourfic' )
				),

				array(
					'id'      => 'wl-for',
					'type'    => 'checkbox',
					'label'   => __( 'Enable Wishlist for', 'tourfic' ),
					'options' => array(
						'li' => __( 'Logged in User', 'tourfic' ),
						'lo' => __( 'Logged out User', 'tourfic' ),
					),
					'default' => array( 'li', 'lo' )
				),

				array(
					'id'      => 'wl-bt-for',
					'type'    => 'checkbox',
					'label'   => __( 'Show Wishlist Button on', 'tourfic' ),
					'options' => array(
						'1' => __( 'Single Hotel Page', 'tourfic' ),
						'2' => __( 'Single Tour Page', 'tourfic' ),
					),
					'default' => array( '1', '2' ),
				),

				array(
					'id'          => 'wl-page',
					'type'        => 'select2',
					'label'       => __( 'Select Wishlist Page', 'tourfic' ),
					'placeholder' => __( 'Select Wishlist Page', 'tourfic' ),
					'options'     => 'posts',
					'query_args'  => array(
						'post_type'      => 'page',
						'posts_per_page' => - 1,
						'orderby'        => 'post_title',
						'order'          => 'ASC'
					)
				),

			),
		),

		/**
		 * Permalink Settings
		 *
		 * Sub Menu
		 */

		'permalink' => array(
			'title'  => __( 'Permalink Settings', 'tourfic' ),
			'parent' => 'miscellaneous',
			'icon'   => 'fas fa-link',
			'fields' => array(
				array(
					'id'      => 'permalink_heading',
					'type'    => 'heading',
					'content' => __( 'Permalink Settings', 'tourfic' )
				),
				array(
					'id'      => 'permalink_notice',
					'type'    => 'notice',
					'content' => __( 'For permalink settings go to default <a href="' . get_admin_url() . 'options-permalink.php">permalink settings page</a>.', 'tourfic' ),
				),

			),
		),
		/**
		 * Review
		 *
		 * Sub Menu
		 */

		'review'       => array(
			'title'  => __( 'Review', 'tourfic' ),
			'parent' => 'miscellaneous',
			'icon'   => 'fas fa-star',
			'fields' => array(
				array(
					'id'      => 'review_heading',
					'type'    => 'heading',
					'content' => __( 'Review Settings', 'tourfic' ),
				),
				array(
					'id'      => 'r-for',
					'type'    => 'checkbox',
					'label'   => __( 'Enable Review for', 'tourfic' ),
					'options' => array(
						'li' => __( 'Logged in User', 'tourfic' ),
						''   => __( 'Log out User (Pro)', 'tourfic' ),
					),
					'default' => array( 'li' ),
				),

				array(
					'id'        => 'r-auto-publish',
					'type'      => 'switch',
					'label'     => __( 'Auto Publish Review', 'tourfic' ),
					'subtitle'  => __( 'By default review will be pending and waiting for admin approval', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
				),

				array(
					'id'      => 'r-base',
					'type'    => 'radio',
					'label'   => __( 'Calculate Review Based on', 'tourfic' ),
					'inlines' => true,
					'options' => array(
						'5'  => __( '5', 'tourfic' ),
						'10' => __( '10', 'tourfic' ),
					),
					'default' => '5',
				),

				array(
					'id'       => 'r-hotel',
					'class'    => 'disable-sortable',
					'type'     => 'repeater',
					'label'    => __( 'Review Fields for Hotels', 'tourfic' ),
					'subtitle' => __( 'Add Custom Review Fields', 'tourfic' ),
					'is_pro'   => true,
					'max'      => '6',
					'fields'   => array(
						array(
							'id'    => 'r-field-type',
							'type'  => 'text',
							'label' => __( 'Review for', 'tourfic' ),
						),

					),
					'default'  => array(
						array(
							'r-field-type' => __( 'Staff', 'tourfic' ),
						),
						array(
							'r-field-type' => __( 'Facilities', 'tourfic' ),
						),
						array(
							'r-field-type' => __( 'Cleanliness', 'tourfic' ),
						),
						array(
							'r-field-type' => __( 'Comfort', 'tourfic' ),
						),
						array(
							'r-field-type' => __( 'Value for money', 'tourfic' ),
						),
						array(
							'r-field-type' => __( 'Location', 'tourfic' ),
						),
					)
				),
				array(
					'id'       => 'r-tour',
					'type'     => 'repeater',
					'label'    => __( 'Review Fields for Tours', 'tourfic' ),
					'subtitle' => __( 'Add Custom Review Fields', 'tourfic' ),
					'is_pro'   => true,
					'max'      => '6',
					'fields'   => array(

						array(
							'id'    => 'r-field-type',
							'type'  => 'text',
							'label' => __( 'Review for', 'tourfic' ),
						),

					),
					'default'  => array(
						array(
							'r-field-type' => __( 'Guide', 'tourfic' ),
						),
						array(
							'r-field-type' => __( 'Transportation', 'tourfic' ),
						),
						array(
							'r-field-type' => __( 'Value for money', 'tourfic' ),
						),
						array(
							'r-field-type' => __( 'Safety', 'tourfic' ),
						),
					)
				),


				array(
					'id'       => 'tf_delete_old_review_fields_button',
					'type'     => 'callback',
					'function' => 'tf_delete_old_review_fields_button',
				),
				array(
					'id'       => 'tf_delete_old_complete_review_button',
					'type'     => 'callback',
					'function' => 'tf_delete_old_complete_review_button',
				),

			),
		),
		/**
		 * optimization Settings
		 *
		 * Sub Menu
		 */
		'optimization' => array(
			'title'  => __( 'Optimization', 'tourfic' ),
			'parent' => 'miscellaneous',
			'icon'   => 'fas fa-star',
			'fields' => array(
				array(
					'id'      => 'optimization_heading',
					'type'    => 'heading',
					'content' => __( 'Minification Settings', 'tourfic' ),
				),
				array(
					'id'        => 'css_min',
					'type'      => 'switch',
					'label'     => __( 'Minify CSS', 'tourfic' ),
					'subtitle'  => __( 'Enable/disable Tourfic CSS minification', 'tourfic' ),
					'is_pro'    => true,
					'label_on'  => __( 'Enabled', 'tourfic' ),
					'label_off' => __( 'Disabled', 'tourfic' ),
					'width'     => 100,
					'default'   => false
				),

				array(
					'id'        => 'js_min',
					'type'      => 'switch',
					'label'     => __( 'Minify JS', 'tourfic' ),
					'subtitle'  => __( 'Enable/disable Tourfic JS minification', 'tourfic' ),
					'is_pro'    => true,
					'label_on'  => __( 'Enabled', 'tourfic' ),
					'label_off' => __( 'Disabled', 'tourfic' ),
					'width'     => 100,
					'default'   => false
				),

				array(
					'id'      => 'cdn_heading',
					'type'    => 'heading',
					'content' => __( 'CDN Settings', 'tourfic' ),
				),

				array(
					'id'        => 'ftpr_cdn',
					'type'      => 'switch',
					'label'     => __( 'Flatpickr CDN', 'tourfic' ),
					'subtitle'  => __( 'Enable/disable cloudflare CDN for Flatpickr CSS & JS', 'tourfic' ),
					'is_pro'    => true,
					'label_on'  => __( 'Enabled', 'tourfic' ),
					'label_off' => __( 'Disabled', 'tourfic' ),
					'width'     => 100
				),

				array(
					'id'        => 'fnybx_cdn',
					'type'      => 'switch',
					'label'     => __( 'Fancybox CDN', 'tourfic' ),
					'subtitle'  => __( 'Enable/disable cloudflare CDN for Fancybox CSS & JS', 'tourfic' ),
					'is_pro'    => true,
					'label_on'  => __( 'Enabled', 'tourfic' ),
					'label_off' => __( 'Disabled', 'tourfic' ),
					'width'     => 100
				),

				array(
					'id'        => 'slick_cdn',
					'type'      => 'switch',
					'label'     => __( 'Slick CDN', 'tourfic' ),
					'subtitle'  => __( 'Enable/disable cloudflare CDN for Slick CSS & JS', 'tourfic' ),
					'is_pro'    => true,
					'label_on'  => __( 'Enabled', 'tourfic' ),
					'label_off' => __( 'Disabled', 'tourfic' ),
					'width'     => 100
				),

				array(
					'id'        => 'fa_cdn',
					'type'      => 'switch',
					'label'     => __( 'Font Awesome CDN', 'tourfic' ),
					'subtitle'  => __( 'Enable/disable cloudflare CDN for Font Awesome CSS', 'tourfic' ),
					'is_pro'    => true,
					'label_on'  => __( 'Enabled', 'tourfic' ),
					'label_off' => __( 'Disabled', 'tourfic' ),
					'width'     => 100
				),


			),
		),

		/**
		 * Affiliate Options
		 *
		 * Main menu
		 */

		'affiliate' => array(
			'title'  => __( 'Affiliate', 'tourfic' ),
			'icon'   => 'fa fa-handshake-o',
			'fields' => array(
				array(
					'id'       => 'affiliate_heading',
					'type'     => 'heading',
					'label'  => __( 'Affiliate Settings', 'tourfic' ),
					'subtitle' => __( 'Use these options if you want to show 3rd party data and earn commission from them. Currently, we only allow Booking.com and TravelPayout. Gradually more options would be added.', 'tourfic' ),
				),
				array(
					'id'     => 'tf-tab',
					'type'   => 'tab',
					'label'  => 'Affiliate',
					'is_pro' => true,
					'tabs'   => array(
						array(
							'id'     => 'affiliate_booking',
							'title'  => __( 'Booking.com', 'tourfic' ),
							'fields' => array(
								array(
									'id'        => 'enable-booking-dot-com',
									'type'      => 'switch',
									'title'     => __( 'Enable Booking.com?', 'tourfic' ),
									'label_on'  => __( 'Yes', 'tourfic' ),
									'label_off' => __( 'No', 'tourfic' ),
									'default'   => true
								),
							),
						),
						array(
							'id'     => 'travelPayouts',
							'title'  => __( 'TravelPayouts', 'tourfic' ),
							'icon'   => 'fa fa-gear',
							'fields' => array(
								array(
									'id'        => 'enable-travel-payouts',
									'type'      => 'switch',
									'title'     => __( 'Enable TravelPayouts?', 'tourfic' ),
									'label_on'  => __( 'Yes', 'tourfic' ),
									'label_off' => __( 'No', 'tourfic' ),
									'default'   => true
								),
							),
						),
					),
				)
			),
		),

		/**
		 * Integration
		 *
		 * Main menu
		 */

		 'integration' => array(
			'title'  => __( 'Integration', 'tourfic' ),
			'icon'   => 'fa fa-plus',
			'fields' => array(
				array(
					'id'       => 'integration_heading',
					'type'     => 'heading',
					'label'  => __( 'Pabbly & Zapier Settings', 'tourfic' ),
					'subtitle' => __( 'If you want to integrate your system with other platforms. Currently, we only allow Pabbly and Zapier.', 'tourfic' ),
				),
				array(
					'id'     => 'tf-integration',
					'type'   => 'tab',
					'label'  => 'Pabbly & Zapier',
					'is_pro' => true,
					'tabs'   => array(
						array(
							'id'     => 'pabbly_integration',
							'title'  => __( 'Pabbly', 'tourfic' ),
							'fields' => array(
								array(
									'id'      => 'hotel-title',
									'type'    => 'heading',
									'content' => __( 'Hotel', 'tourfic' ),
									'class'   => 'tf-field-class',
								),
								array(
									'id'        => 'hotel-integrate-pabbly',
									'type'      => 'switch',
									'label'     => __( 'Enable Pabbly for Hotel?', 'tourfic' ),
									'subtitle' => __( 'You can able to Integrate Pabbly with Hotel create and update.', 'tourfic' ),
									'label_on'  => __( 'Yes', 'tourfic' ),
									'label_off' => __( 'No', 'tourfic' ),
									'default'   => false,
									'is_pro'    => true,
								),
								array(
									'id'       => 'hotel-integrate-pabbly-webhook',
									'type'     => 'text',
									'label'    => __( 'Hotel Web Hook', 'tourfic' ),
									'subtitle' => __( 'Enter Here Your Hotel Pabbly Web Hook.', 'tourfic' ),
									'is_pro'   => true,
									'dependency'  => array(
										array( 'hotel-integrate-pabbly', '==', 'true' ),
									),
								),
								array(
									'id'        => 'h-enquiry-pabbly',
									'type'      => 'switch',
									'label'     => __( 'Enable Pabbly for Hotel Enquiry?', 'tourfic' ),
									'subtitle' => __( 'Integrate Pabbly with Hotel Enquiry Form.', 'tourfic' ),
									'label_on'  => __( 'Yes', 'tourfic' ),
									'label_off' => __( 'No', 'tourfic' ),
									'default'   => false,
									'is_pro'    => true
								),
								array(
									'id'       => 'h-enquiry-pabbly-webhook',
									'type'     => 'text',
									'label'    => __( 'Hotel Enquiry Web Hook', 'tourfic' ),
									'subtitle' => __( 'Enter Here Your Hotel Enquiry Pabbly Web Hook.', 'tourfic' ),
									'is_pro'   => true,
									'dependency'  => array(
										array( 'h-enquiry-pabbly', '==', 'true' ),
									),
								),
								array(
									'id'      => 'tour-title',
									'type'    => 'heading',
									'content' => __( 'Tour', 'tourfic' ),
									'class'   => 'tf-field-class',
								),
								array(
									'id'        => 'tour-integrate-pabbly',
									'type'      => 'switch',
									'label'     => __( 'Enable Pabbly for Tour?', 'tourfic' ),
									'subtitle' => __( 'You can able to Integrate Pabbly with Tour create and update.', 'tourfic' ),
									'label_on'  => __( 'Yes', 'tourfic' ),
									'label_off' => __( 'No', 'tourfic' ),
									'default'   => false,
									'is_pro'    => true,
								),
								array(
									'id'       => 'tour-integrate-pabbly-webhook',
									'type'     => 'text',
									'label'    => __( 'Tour Web Hook', 'tourfic' ),
									'subtitle' => __( 'Enter Here Your Tour Pabbly Web Hook.', 'tourfic' ),
									'is_pro'   => true,
									'dependency'  => array(
										array( 'tour-integrate-pabbly', '==', 'true' ),
									),
								),
								array(
									'id'        => 't-enquiry-pabbly',
									'type'      => 'switch',
									'label'     => __( 'Enable Pabbly for Tour Enquiry?', 'tourfic' ),
									'subtitle' => __( 'Integrate Pabbly with Tour Enquiry Form.', 'tourfic' ),
									'label_on'  => __( 'Yes', 'tourfic' ),
									'label_off' => __( 'No', 'tourfic' ),
									'default'   => false,
									'is_pro'    => true
								),
								array(
									'id'       => 't-enquiry-pabbly-webhook',
									'type'     => 'text',
									'label'    => __( 'Tour Enquiry Web Hook', 'tourfic' ),
									'subtitle' => __( 'Enter Here Your Tour Enquiry Pabbly Web Hook.', 'tourfic' ),
									'is_pro'   => true,
									'dependency'  => array(
										array( 't-enquiry-pabbly', '==', 'true' ),
									),
								),
							),
						),
						array(
							'id'     => 'zapier_integration',
							'title'  => __( 'Zapier', 'tourfic' ),
							'icon'   => 'fa fa-gear',
							'fields' => array(
								
							),
						),
					),
				)
			),
		),

		/**
		 * Import/Export
		 *
		 * Main menu
		 */
		// 'import_export' => array(
		// 	'title' => __( 'Import/Export', 'tourfic' ),
		// 	'icon' => 'fas fa-hdd',
		// 	'fields' => array(
		// 		array(
		// 			'id' => 'backup',
		// 			'type' => 'backup',
		// 		),  

		// 	),
		// ),
	),
) );
