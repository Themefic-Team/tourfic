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
		'general'       => array(
			'title'  => esc_html__( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'       => 'disable-services',
					'type'     => 'checkbox',
					'label'    => __( 'Disable Services', 'tourfic' ),
					'subtitle' => __( 'Disable or hide the services you don\'t need by ticking the checkbox', 'tourfic' ),
					'options'  => array(
						'hotel' => __( 'Hotel', 'tourfic' ),
						'tour'  => __( 'Tour', 'tourfic' ),
					),
				),
			),
		),
		'hotel_option'  => array(
			'title'  => esc_html__( 'Hotel Options', 'tourfic' ),
			'icon'   => 'fas fa-hotel',
			'fields' => array(),
		),
		'single_page'   => array(
			'title'  => esc_html__( 'Single Page', 'tourfic' ),
			'parent' => 'hotel_option',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'label_off_heading',
					'type'  => 'heading',
					'label' => __( 'Single Hotel Settings', 'tourfic' ),
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
					'id'     => 'h-enquiry-email',
					'type'   => 'text',
					'label'  => __( 'Enquiry Email', 'tourfic' ),
					'is_pro' => true,
				),
			),
		),
		'room_config'   => array(
			'title'  => esc_html__( 'Room Config', 'tourfic' ),
			'parent' => 'hotel_option',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'hotel_room_heading',
					'type'  => 'heading',
					'label' => __( 'Hotel Room Configuration', 'tourfic' ),
				),

				array(
					'id'       => 'children_age_limit',
					'type'     => 'switch',
					'label'    => __( 'Children age limit', 'tourfic' ),
					'subtitle' => __( 'keep blank if don\'t want to add', 'tourfic' ),
					'is_pro'   => true,
				),
			),
		),
		// Tour Options
		'tour'          => array(
			'title'  => __( 'Tour Options', 'tourfic' ),
			'icon'   => 'fas fa-umbrella-beach',
			'fields' => array(),
		),
		'single_tour'   => array(
			'title'  => esc_html__( 'Single Page', 'tourfic' ),
			'parent' => 'tour',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'signle_tour_heading',
					'type'  => 'heading',
					'label' => __( 'Single Tour Settings', 'tourfic' ),
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
					'id'     => 't-enquiry-email',
					'type'   => 'text',
					'label'  => __( 'Enquiry Email', 'tourfic' ),
					'is_pro' => true,
				),
			),
		),
		// Multi Vendor
		'vendor'        => array(
			'title'  => __( 'Multi Vendor', 'tourfic' ),
			'icon'   => 'fas fa-handshake',
			'fields' => array(
				// Registration
				array(
					'id'      => 'Registration_heading',
					'type'    => 'heading',
					'content' => __( 'Registration', 'tourfic' ),
				),

				array(
					'id'        => 'reg-pop',
					'type'      => 'switch',
					'label'     => __( 'Registration Form Popup', 'tourfic' ),
					'is_pro'    => true,
					'subtitle'  => __( 'Add class <code>tf-reg-popup</code> to trigger the popup', 'tourfic' ),
					'label_on'  => __( 'Enabled', 'tourfic' ),
					'label_off' => __( 'Disabled', 'tourfic' ),
					'width'     => 100,
					'default'   => true,
				),

				array(
					'id'      => 'notice',
					'type'    => 'notice',
					'content' => __( 'Use shortcode <code>[tf_registration_form]</code> to show registration form in post/page/widget.', 'tourfic' ),
				),

				array(
					'id'      => 'email-verify',
					'type'    => 'switch',
					'label'   => __( 'Enable Email Verification', 'tourfic' ),
					'is_pro'  => true,
					'default' => true,
				),

				array(
					'id'         => 'prevent-login',
					'type'       => 'switch',
					'label'      => __( 'Login Restriction', 'tourfic' ),
					'subtitle'   => __( 'Prevent unverified user to login', 'tourfic' ),
					'is_pro'     => true,
					'dependency' => array( 'email-verify', '==', 'true' ),
					'default'    => true,
				),

				array(
					'id'      => 'notice_shortcode',
					'type'    => 'notice',
					'content' => __( 'Use shortcode <code>[tf_login_form]</code> to show login form in post/page/widget.', 'tourfic' ),
				),

				// Vendor
				array(
					'id'      => 'Vendor_heading',
					'type'    => 'heading',
					'content' => __( 'Vendor', 'tourfic' ),
				),

				array(
					'id'       => 'vendor-reg',
					'type'     => 'switch',
					'label'    => __( 'Enable Vendor Registration', 'tourfic' ),
					'subtitle' => __( 'Visitor can register as vendor using the registration form', 'tourfic' ),
					'is_pro'   => true,
					'default'  => true,
				),

				array(
					'id'      => 'vendor-tax-add',
					'type'    => 'checkbox',
					'title'   => __( 'Vendor Can Add', 'tourfic' ),
					'is_pro'  => true,
					'options' => array(
						'hl' => __( 'Hotel Location', 'tourfic' ),
						'hf' => __( 'Hotel Feature', 'tourfic' ),
						'td' => __( 'Tour Destination', 'tourfic' ),
					),
				),
			),
		),
		// Search Options
		'search'        => array(
			'title'  => __( 'Search', 'tourfic' ),
			'icon'   => 'fas fa-search',
			'fields' => array(
				// Registration
				array(
					'id'          => 'search-result-page',
					'type'        => 'select2',
					'placeholder' => __( 'Select a page', 'tourfic' ),
					'label'       => __( 'Select Search Result Page', 'tourfic' ),
					'description' => __( 'Page template: <code>Tourfic - Search Result</code> must be selected', 'tourfic' ),
					'options'     => 'posts',
					'query_args'  => array(
						'post_type'      => 'page',
						'posts_per_page' => - 1,
					),
				),

				array(
					'id'        => 'date_hotel_search',
					'type'      => 'switch',
					'label'     => __( 'Date Required in Hotel Search', 'tourfic' ),
					'is_pro'    => true,
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => true,
				),

				array(
					'id'        => 'date_tour_search',
					'type'      => 'switch',
					'label'     => __( 'Date Required in Tour Search', 'tourfic' ),
					'is_pro'    => true,
					'label_off' => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
				),
			),
		),
		// Design Options
		'design-panel'  => array(
			'title'  => __( 'Design Panel', 'tourfic' ),
			'icon'   => 'fas fa-palette',
			'fields' => array(),
		),
		'global_design' => array(
			'title'  => __( 'Global', 'tourfic' ),
			'parent' => 'design-panel',
			'icon'   => 'fas fa-cogs',
			'fields' => array(
				array(
					'id'      => 'global_design_heading',
					'type'    => 'heading',
					'content' => __( 'Global Settings', 'tourfic' ),
				),
				array(
					'id'      => 'global_design_notice',
					'type'    => 'notice',
					'style'   => 'info',
					'content' => __( "To ensure maximum compatiblity with your theme, all Heading (h1-h6), Paragraph & Link's Color-Font Styles are not controlled by Tourfic. Those need to be edited using your Theme's option Panel.", "tourfic" ),
				),
				array(
					'id'       => 'tourfic-button-color',
					'type'     => 'color',
					'multiple' => true,
					'label'    => __( 'Button Color', 'tourfic' ),
					'subtitle' => __( 'Button Color of Tourfic (e.g. Blue color on our Demo)', 'tourfic' ),
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
					'subtitle' => __( 'Button Background Color of Tourfic ', 'tourfic' ),
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
					'subtitle' => __( 'The Gradient color of Sidebar Booking', 'tourfic' ),
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
					'subtitle' => __( 'Style of FAQ Section', 'tourfic' ),
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
					'subtitle' => __( 'Style of Review Section', 'tourfic' ),
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
		'hotel_design'  => array(
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
					'subtitle' => __( 'The "Hotel" text above heading ', 'tourfic' ),
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
					'subtitle' => __( 'The color of Share Icon', 'tourfic' ),
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
					'label'    => __( 'Map Button', 'tourfic' ),
					'subtitle' => __( 'The Gradient color of Map Button', 'tourfic' ),
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
					'id'       => 'tourfic-hotel-features-color',
					'type'     => 'color',
					'label'    => __( 'Hotel Features Color', 'tourfic' ),
					'subtitle' => __( 'The Color of Features Icon ', 'tourfic' ),
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
					'subtitle' => __( 'The style of Room Table', 'tourfic' ),
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
		'tour_design'   => array(
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
					'subtitle' => __( 'Styling of the Pricing', 'tourfic' ),
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
		'miscellaneous' => array(
			'title'  => __( 'Miscellaneous', 'tourfic' ),
			'icon'   => 'fas fa-globe',
			'fields' => array(),
		),
		/**
		 * Google Map
		 *
		 * Sub Menu
		 */
		'map_settings'  => array(
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
					'id'      => 'google-page-option',
					'type'    => 'select',
					'title'   => __( 'Select Map', 'tourfic' ),
					'is_pro'  => true,
					'options' => array(
						'default'   => __( 'Default Map', 'tourfic' ),
						'googlemap' => __( 'Google Map', 'tourfic' ),
					),
					'default' => 'default'
				),
				array(
					'id'         => 'tf-googlemapapi',
					'type'       => 'text',
					'title'      => __( 'Google Map API Key', 'tourfic' ),
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
		'wishlist'      => array(
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
					'type'        => 'select',
					'label'       => __( 'Select Wishlist Page', 'tourfic' ),
					'placeholder' => __( 'Select Wishlist Page', 'tourfic' ),
					'ajax'        => true,
					'options'     => 'pages',
					'query_args'  => array(
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
					'title'     => __( 'Auto Publish Review', 'tourfic' ),
					'subtitle'  => __( 'By default review will be pending and waiting for admin approval', 'tourfic' ),
					'label_on'  => __( 'Yes', 'tourfic' ),
					'label_off' => __( 'No', 'tourfic' ),
					'default'   => true
				),

				array(
					'id'      => 'r-base',
					'type'    => 'radio',
					'label'   => __( 'Calculate Review Based on', 'tourfic' ),
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
					'subtitle' => __( 'Maximum 10 fields allowed', 'tourfic' ),
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
					'subtitle' => __( 'Maximum 10 fields allowed', 'tourfic' ),
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
					'id'      => 'affiliate_heading',
					'type'    => 'heading',
					'content' => __( 'Affiliate Settings', 'tourfic' ),
				),
				array(
					'id'     => 'tf-tab',
					'type'   => 'tab',
					'label'  => 'Affiliate',
					'is_pro' => true,
					'tabs'   => array(
						array(
							'id'     => 'booking.com',
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
