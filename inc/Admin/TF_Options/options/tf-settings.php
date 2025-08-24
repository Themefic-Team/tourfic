<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;


if ( file_exists( TF_ADMIN_PATH . 'TF_Options/options/tf-menu-icon.php' ) ) {
	require_once TF_ADMIN_PATH . 'TF_Options/options/tf-menu-icon.php';
} else {
	// $menu_icon = TF_ASSETS_ADMIN_URL . 'images/icons/tourfic-settings.svg';
}

if ( !function_exists( 'tf_search_page_default' ) ) {
    function tf_search_page_default() {
        $args = array(
            'post_type'      => 'page',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        $loop = new WP_Query( $args );
        if ( $loop->have_posts() ) {
            foreach ( $loop->posts as $post ) {
                if ( $post->post_name == 'tf-search' ) {
                    return $post->ID;
                }
            }
        }
        return;
    }
}

if ( ! function_exists( 'tf_wishlist_page_default') ) {
	function tf_wishlist_page_default() {
		$args = array(
			'post_type'      => 'page',
			'posts_per_page' => - 1,
			'post_status' => 'publish',
		);

		$loop = new WP_Query( $args );
		if( $loop->have_posts() ) {
			foreach( $loop->posts as $post ) {
				if( $post->post_name == 'tf-wishlist') {
					return $post->ID;
				}				
			}
		}
		return;
	}
}

TF_Settings::option( 'tf_settings', array(
	'title'    => esc_html__( 'Tourfic Settings ', 'tourfic' ),
	'icon'     => $menu_icon,
	'position' => 26,
	'sections' => array(
		'general'               => array(
			'title'  => esc_html__( 'General', 'tourfic' ),
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'      => 'general-notice-heading',
					'type'  => 'heading',
					'label' => esc_html__( 'General Settings', 'tourfic' ),
					'subtitle'   => esc_html__( 'This section contains the general settings for Tourfic.', 'tourfic' ),
				),
				array(
					'id'      => 'general-option-notice-one',
					'type'    => 'notice',
					'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="'. Helper::tf_utm_generator("https://themefic.com/docs/tourfic/tourfic-settings/tourfic-general-settings/", array( 'utm_medium' => 'settings_doc_general-settings' )) .'" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'disable-services',
					'type'     => 'imageselect',
					'label'    => esc_html__( 'Disable Post Types', 'tourfic' ),
					'subtitle' => esc_html__( 'Tick the image to disable the Post Type you don\'t need.', 'tourfic' ),
					'multiple' => true,
					'img-width'=> '100',
					'img-height'=> '100',
					'options'  => array(
						'hotel' => array(
							'title' => 'Hotel',
							'url'   => TF_ASSETS_ADMIN_URL . "images/hotel.png",
						),
						'tour' 		=> array(
							'title'	=> 'Tour',
							'url' 	=> TF_ASSETS_ADMIN_URL."images/tour.png",
						),
						'apartment'  => array(
							'title' => 'Apartment',
							'url'   => TF_ASSETS_ADMIN_URL . "images/apartment.png",
						),
						'carrentals'  => array(
							'title' => 'Car',
							'url'   => TF_ASSETS_ADMIN_URL . "images/carrentals.png",
						),
					),
				),
				array(
					'id'       => 'tf-date-format-for-users',
					'type'     => 'select',
					'label'    => esc_html__( 'Select Date Format', 'tourfic' ),
					'subtitle' => esc_html__( 'Choose the display format for the date as seen by the user upon selection.', 'tourfic' ),
					'options'  => array(
						'Y/m/d'  => esc_html__( 'YYYY/MM/DD', 'tourfic' ),
						'd/m/Y'  => esc_html__( 'DD/MM/YYYY', 'tourfic' ),
						'm/d/Y' => esc_html__('MM/DD/YYYY', 'tourfic'),
						'Y-m-d' => esc_html__( 'YYYY-MM-DD', 'tourfic' ),
						'd-m-Y'  => esc_html__( 'DD-MM-YYYY', 'tourfic' ),
						'm-d-Y' => esc_html__('MM-DD-YYYY', 'tourfic'),
						'Y.m.d'  => esc_html__( 'YYYY.MM.DD', 'tourfic' ),
						'd.m.Y'  => esc_html__( 'DD.MM.YYYY', 'tourfic' ),
						'm.d.Y' => esc_html__('MM.DD.YYYY', 'tourfic'),
					),
					'default'    => 'Y/m/d',
				),
				array(
					'id'       => 'tf-week-day-flatpickr',
					'type'     => 'select',
					'label'    => esc_html__( 'Select First Day of Week', 'tourfic' ),
					'subtitle' => esc_html__( 'Select a Day, that will show in the DatePickr of Frontend', 'tourfic' ),
					'options'  => array(
						'0' => esc_html__('Sunday', 'tourfic'),
						'1' => esc_html__('Monday', 'tourfic'),
						'2' => esc_html__('Tuesday', 'tourfic'),
						'3' => esc_html__('Wednesday', 'tourfic'),
						'4' => esc_html__('Thursday', 'tourfic'),
						'5' => esc_html__('Friday', 'tourfic'),
						'6' => esc_html__('Saturday', 'tourfic')
					),
					'default'    => '0',
				),
				array(
					'id'       => 'tf-quick-checkout',
					'type'     => 'switch',
					'label'    => esc_html__( 'Enable Quick Checkout', 'tourfic' ),
					'subtitle' => esc_html__( 'This option allows you to complete the checkout process directly from the single service page, without navigating to the checkout page. Note: The Instantio plugin is required, and this option is only for woocommerce payment system.', 'tourfic' ),
				),
			),
		),

		// Template Settings
		'tf-template-settings' => array(
			'title'  => esc_html__( 'Template', 'tourfic' ),
			'icon'   => 'fa-solid fa-newspaper',
			'fields' => array(
				array(
					'id'       => 'template_heading',
					'type'     => 'heading',
					'label'    => esc_html__( 'Template Settings', 'tourfic' ),
					'subtitle' => esc_html__( 'Select your preferred template from our current offering of two options, with more on the way!', 'tourfic' ),
				),
				array(
					'id'    => 'tf-template',
					'type'  => 'tab',
					'label' => esc_html__('Hotel, Tour, Apartment & Car Template', 'tourfic'),
					'tabs'  => array(
						array(
							'id'     => 'hotel_template',
							'title'  => esc_html__( 'Hotel', 'tourfic' ),
							'icon'   => 'fa fa-gear',
							'post_dependency' => 'hotel',
							'fields' => array(
								array(
									'id'      => 'hotel-title',
									'type'    => 'heading',
									'content' => esc_html__( 'Hotel Single Page', 'tourfic' ),
									'class'   => 'tf-field-class',
								),
								array(
									'id'       => 'single-hotel',
									'type'     => 'imageselect',
									'label'    => esc_html__( 'Select Single Hotel Template', 'tourfic' ),
									'subtitle'   => esc_html__( 'You have the option to override this from the settings specific to each individual hotel page.', 'tourfic' ),
									'options'  => array(
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
									'default'  => 'design-1',
								),
								//design 1
								array(
									'id'         => 'single-hotel-layout',
									'type'       => 'switch_group',
									'column'  	 => 4,
									'label'      => esc_html__( 'Single Hotel Template Sections', 'tourfic' ),
									'subtitle'   => esc_html__( 'You can change the order of sections by dragging and dropping them.', 'tourfic' ),
									'dependency' => array( 'single-hotel', '==', 'design-1' ),
									'default'    => array(
										array(
											'label'  => esc_html__( 'Description', 'tourfic' ),
											'slug'   => 'description',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Features', 'tourfic' ),
											'slug'   => 'features',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Room', 'tourfic' ),
											'slug'   => 'rooms',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Facilities', 'tourfic' ),
											'slug'   => 'facilities',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'FAQ', 'tourfic' ),
											'slug'   => 'faq',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Review', 'tourfic' ),
											'slug'   => 'review',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Terms & Conditions', 'tourfic' ),
											'slug'   => 'trams-condition',
											'status' => 1,
										),
									),
								),
								//design 2
								array(
									'id'         => 'single-hotel-layout-part-1',
									'type'       => 'switch_group',
									'column'  	 => 4,
									'label'      => esc_html__( 'Single Hotel Template Sections Part 1', 'tourfic' ),
									'subtitle'   => esc_html__( 'You can able to change section positions by Drag & Drop.', 'tourfic' ),
									'dependency' => array( 'single-hotel', '==', 'design-2' ),
									'default'    => array(
										array(
											'label'  => esc_html__( 'Description', 'tourfic' ),
											'slug'   => 'description',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Features', 'tourfic' ),
											'slug'   => 'features',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Room', 'tourfic' ),
											'slug'   => 'rooms',
											'status' => 1,
										)
									),
								),
								array(
									'id'         => 'single-hotel-layout-part-2',
									'type'       => 'switch_group',
									'column'  	 => 4,
									'label'      => esc_html__( 'Single Hotel Template Sections Part 2', 'tourfic' ),
									'subtitle'   => esc_html__( 'You can able to change section positions by Drag & Drop.', 'tourfic' ),
									'dependency' => array( 'single-hotel', '==', 'design-2' ),
									'default'    => array(
										array(
											'label'  => esc_html__( 'Facilities', 'tourfic' ),
											'slug'   => 'facilities',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Review', 'tourfic' ),
											'slug'   => 'review',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'FAQ', 'tourfic' ),
											'slug'   => 'faq',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Terms & Conditions', 'tourfic' ),
											'slug'   => 'trams-condition',
											'status' => 1,
										),
									),
								),
								array(
									'id'      => 'hotel-title',
									'type'    => 'heading',
									'content' => esc_html__( 'Hotel Archive & Search Result Page', 'tourfic' ),
									'class'   => 'tf-field-class',
								),
								array(
									'id'       => 'hotel-archive',
									'type'     => 'imageselect',
									'label'    => esc_html__( 'Select Archive & Search Result Template', 'tourfic' ),
									'options'  => array(
										'design-1' => array(
											'title' => esc_html__('Design 1', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-archive-design-1.png",
										),
										'design-2' => array(
											'title' => esc_html__('Design 2', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-archive-design-2.png",
										),
										'default'  => array(
											'title' => esc_html__('Legacy', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-archive-default.png",
										),
									),
									'default'  => 'design-1',
								),
								array(
									'id'      => 'hotel_archive_design_2_bannar',
									'type'    => 'image',
									'label'    => esc_html__( 'Archive & Search Result Banner Image', 'tourfic' ),
									'subtitle' => esc_html__( 'Upload Banner Image for this hotel archive template.', 'tourfic' ),
									'library' => 'image',
									'dependency' => array( 'hotel-archive', '==', 'design-2' ),
								),
								array(
									'id'      => 'hotel_archive_design_3_bannar',
									'type'    => 'image',
									'label'    => esc_html__( 'Archive & Search Result Banner Image', 'tourfic' ),
									'subtitle' => esc_html__( 'Upload Banner Image for this hotel archive template.', 'tourfic' ),
									'library' => 'image',
									'dependency' => array( 'hotel-archive', '==', 'design-3' ),
								),
								array(
									'id'         => 'hotel_archive_view',
									'type'       => 'select',
									'label'      => esc_html__( 'Archive Layout', 'tourfic' ),
									'options'    => array(
										'list' => esc_html__( 'List', 'tourfic' ),
										'grid' => esc_html__( 'Grid', 'tourfic' ),
									),
									'default'    => 'List',
									'dependency' => array( 'hotel-archive', '!=', 'design-2' ),
								),
							
								array(
									'id'      => 'hotel_archive_notice',
									'type'    => 'notice',
									'content' => esc_html__( 'Edit the sidebar filter from Appearance -> Widgets', 'tourfic' ),
								),
							),
						),
						array(
							'id'     => 'tour_template',
							'title'  => esc_html__( 'Tour', 'tourfic' ),
							'post_dependency' => 'tour',
							'fields' => array(
								array(
									'id'      => 'tour-title',
									'type'    => 'heading',
									'content' => esc_html__( 'Tour Single Page', 'tourfic' ),
									'class'   => 'tf-field-class',
								),
								array(
									'id'       => 'single-tour',
									'type'     => 'imageselect',
									'label'    => esc_html__( 'Select Single Tour Template', 'tourfic' ),
									'subtitle'   => esc_html__( 'You have the option to override this from the settings specific to each individual tour page.', 'tourfic' ),
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
									'default'  => 'design-1',
								),
                                //design 1
							
								array(
									'id'         => 'single-tour-layout',
									'type'       => 'switch_group',
									'column'  	 => 4,
									'label'      => esc_html__( 'Single Tour Template Sections', 'tourfic' ),
									'subtitle'   => esc_html__( 'You can change the order of sections by dragging and dropping them.', 'tourfic' ),
									'dependency' => array( 'single-tour', '==', 'design-1' ),
									'default'    => array(
										array(
											'label'  => esc_html__( 'Gallery', 'tourfic' ),
											'slug'   => 'gallery',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Price', 'tourfic' ),
											'slug'   => 'price',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Description', 'tourfic' ),
											'slug'   => 'description',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Information', 'tourfic' ),
											'slug'   => 'information',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Highlights', 'tourfic' ),
											'slug'   => 'highlights',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Include Exclude', 'tourfic' ),
											'slug'   => 'include-exclude',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Itinerary', 'tourfic' ),
											'slug'   => 'itinerary',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Map', 'tourfic' ),
											'slug'   => 'map',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'FAQ', 'tourfic' ),
											'slug'   => 'faq',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Terms & Conditions', 'tourfic' ),
											'slug'   => 'trams-condition',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Review', 'tourfic' ),
											'slug'   => 'review',
											'status' => 1,
										),
									),
								),
                                //design 2
								
								array(
									'id'         => 'single-tour-layout-part-1',
									'type'       => 'switch_group',
									'column'  	 => 4,
									'label'      => esc_html__( 'Single Tour Template Sections Part 1', 'tourfic' ),
									'subtitle'   => esc_html__( 'You can able to change section positions by Drag & Drop.', 'tourfic' ),
									'dependency' => array( 'single-tour', '==', 'design-2' ),
									'default'    => array(
										array(
											'label'  => esc_html__( 'Description', 'tourfic' ),
											'slug'   => 'description',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Information', 'tourfic' ),
											'slug'   => 'information',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Highlights', 'tourfic' ),
											'slug'   => 'highlights',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Include & Exclude', 'tourfic' ),
											'slug'   => 'include-exclude',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Itinerary', 'tourfic' ),
											'slug'   => 'itinerary',
											'status' => 1,
										)
									),
								),
								array(
									'id'         => 'single-tour-layout-part-2',
									'type'       => 'switch_group',
									'column'  	 => 4,
									'label'      => esc_html__( 'Single Tour Template Sections Part 2', 'tourfic' ),
									'subtitle'   => esc_html__( 'You can able to change section positions by Drag & Drop.', 'tourfic' ),
									'dependency' => array( 'single-tour', '==', 'design-2' ),
									'default'    => array(
										array(
											'label'  => esc_html__( 'FAQ', 'tourfic' ),
											'slug'   => 'faq',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Review', 'tourfic' ),
											'slug'   => 'review',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Terms & Conditions', 'tourfic' ),
											'slug'   => 'trams-condition',
											'status' => 1,
										),
									),
								),
								array(
									'id'      => 'tour-title',
									'type'    => 'heading',
									'content' => esc_html__( 'Tour Archive & Search Result Page', 'tourfic' ),
									'class'   => 'tf-field-class',
								),
								array(
									'id'       => 'tour-archive',
									'type'     => 'imageselect',
									'label'    => esc_html__( 'Select Archive & Search Result Template', 'tourfic' ),
									'options'  => array(
										'design-1' => array(
											'title' => esc_html__('Design 1', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-archive-design-1.png",
										),
										'design-2' => array(
											'title' => esc_html__('Design 2', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-archive-design-2.png",
										),
										'default'  => array(
											'title' => esc_html__('Legacy', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-archive-default.png",
										),
									),
									'default'  => 'design-1',
								),
								array(
									'id'      => 'tour_archive_design_2_bannar',
									'type'    => 'image',
									'label'    => esc_html__( 'Archive & Search Result Banner Image', 'tourfic' ),
									'subtitle' => esc_html__( 'Upload Banner Image for this tour archive template.', 'tourfic' ),
									'library' => 'image',
									'dependency' => array( 'tour-archive', '==', 'design-2' ),
								),
								array(
									'id'      => 'tour_archive_design_3_bannar',
									'type'    => 'image',
									'label'    => esc_html__( 'Archive & Search Result Banner Image', 'tourfic' ),
									'subtitle' => esc_html__( 'Upload Banner Image for this tour archive template.', 'tourfic' ),
									'library' => 'image',
									'dependency' => array( 'tour-archive', '==', 'design-3' ),
								),
								array(
									'id'         => 'tour_archive_view',
									'type'       => 'select',
									'label'      => esc_html__( 'Archive Layout', 'tourfic' ),
									'options'    => array(
										'list' => esc_html__( 'List', 'tourfic' ),
										'grid' => esc_html__( 'Grid', 'tourfic' ),
									),
									'default'    => 'List',
									'dependency' => array( 'tour-archive', '!=', 'design-2' ),
								),
								
								array(
									'id'      => 'tour_archive_notice',
									'type'    => 'notice',
									'content' => esc_html__( 'Edit the sidebar filter from Appearance -> Widgets', 'tourfic' ),
								),
							),
						),
						array(
							'id'     => 'apartment_template',
							'title'  => esc_html__( 'Apartment', 'tourfic' ),
							'icon'   => 'fa fa-gear',
							'post_dependency' => 'apartment',
							'fields' => array(
								array(
									'id'      => 'apartment-title',
									'type'    => 'heading',
									'content' => esc_html__( 'Apartment Single Page', 'tourfic' ),
									'class'   => 'tf-field-class',
								),
								array(
									'id'       => 'single-apartment',
									'type'     => 'imageselect',
									'label'    => esc_html__( 'Select Single Apartment Template', 'tourfic' ),
									'subtitle'   => esc_html__( 'You have the option to override this from the settings specific to each individual apartment page.', 'tourfic' ),
									'options'  => array(
										'design-1' => array(
											'title' => esc_html__('Design 1', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-single-apt-design-1.png",
											'preview_link' => esc_url('https://tourfic.com/preview/apartments/2-bedroom-apartment-in-gamle-oslo/'),
										),
										'default'  => array(
											'title' => esc_html__('Legacy', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-single-apt-default.png",
											'preview_link' => esc_url('https://tourfic.com/preview/apartments/barcelo-residences-dubai-marina/'),
										),
									),
									'default'  => 'design-1',
								),
							
								array(
									'id'         => 'single-aprtment-layout-part-1',
									'type'       => 'switch_group',
									'column'  	 => 4,
									'label'      => esc_html__( 'Single Apartment Template Sections Part 1', 'tourfic' ),
									'subtitle'   => esc_html__( 'You can able to change section positions by Drag & Drop.', 'tourfic' ),
									'dependency' => array( 'single-apartment', '==', 'design-1' ),
									'default'    => array(
										array(
											'label'  => esc_html__( 'Description', 'tourfic' ),
											'slug'   => 'description',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Highlights ', 'tourfic' ),
											'slug'   => 'features',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Apartment Rooms', 'tourfic' ),
											'slug'   => 'rooms',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Place offer', 'tourfic' ),
											'slug'   => 'offer',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'House Rules', 'tourfic' ),
											'slug'   => 'rules',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Amenities', 'tourfic' ),
											'slug'   => 'facilities',
											'status' => 1,
										)
									),
								),
								array(
									'id'         => 'single-aprtment-layout-part-2',
									'type'       => 'switch_group',
									'column'  	 => 4,
									'label'      => esc_html__( 'Single Apartment Template Sections Part 2', 'tourfic' ),
									'subtitle'   => esc_html__( 'You can able to change section positions by Drag & Drop.', 'tourfic' ),
									'dependency' => array( 'single-apartment', '==', 'design-1' ),
									'default'    => array(
										array(
											'label'  => esc_html__( 'Review', 'tourfic' ),
											'slug'   => 'review',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'FAQ', 'tourfic' ),
											'slug'   => 'faq',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Terms & Conditions', 'tourfic' ),
											'slug'   => 'trams-condition',
											'status' => 1,
										),
									),
								),
								array(
									'id'      => 'apartment-title',
									'type'    => 'heading',
									'content' => esc_html__( 'Apartment Archive & Search Result Page', 'tourfic' ),
									'class'   => 'tf-field-class',
								),
								array(
									'id'       => 'apartment-archive',
									'type'     => 'imageselect',
									'label'    => esc_html__( 'Select Archive & Search Result Template', 'tourfic' ),
									'options'  => array(
										'design-1' => array(
											'title' => esc_html__('Design 1', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-archive-design-2.png",
										),
										'default'  => array(
											'title' => esc_html__('Legacy', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-archive-default.png",
										),
									),
									'default'  => 'default',
								),
								array(
									'id'      => 'apartment_archive_design_1_bannar',
									'type'    => 'image',
									'label'    => esc_html__( 'Archive & Search Result Banner Image', 'tourfic' ),
									'subtitle' => esc_html__( 'Upload Banner Image for this tour archive template.', 'tourfic' ),
									'library' => 'image',
									'dependency' => array( 'apartment-archive', '==', 'design-1' ),
								),
								array(
									'id'      => 'apartment_archive_design_2_bannar',
									'type'    => 'image',
									'label'    => esc_html__( 'Archive & Search Result Banner Image', 'tourfic' ),
									'subtitle' => esc_html__( 'Upload Banner Image for this tour archive template.', 'tourfic' ),
									'library' => 'image',
									'dependency' => array( 'apartment-archive', '==', 'design-2' ),
								),
								array(
									'id'         => 'apartment_archive_view',
									'type'       => 'select',
									'label'      => esc_html__( 'Archive Layout', 'tourfic' ),
									'options'    => array(
										'list' => esc_html__( 'List', 'tourfic' ),
										'grid' => esc_html__( 'Grid', 'tourfic' ),
									),
									'default'    => 'List',
									'dependency' => array( 'apartment-archive', '!=', 'design-1' ),
								),
							
								array(
									'id'      => 'apartment_archive_notice',
									'type'    => 'notice',
									'content' => esc_html__( 'Edit the sidebar filter from Appearance -> Widgets', 'tourfic' ),
								),
							),
						),
						array(
							'id'     => 'car_template',
							'title'  => esc_html__( 'Car', 'tourfic' ),
							'icon'   => 'fa fa-gear',
							'post_dependency' => 'carrentals',
							'fields' => array(
								array(
									'id'      => 'car-title',
									'type'    => 'heading',
									'content' => esc_html__( 'Car Single Page', 'tourfic' ),
									'class'   => 'tf-field-class',
								),
								array(
									'id'       => 'single-car',
									'type'     => 'imageselect',
									'label'    => esc_html__( 'Select Single Car Template', 'tourfic' ),
									'subtitle'   => esc_html__( 'You have the option to override this from the settings specific to each individual apartment page.', 'tourfic' ),
									'options'  => array(
										'design-1' => array(
											'title' => esc_html__('Design 1', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-single-car-design-1.png",
											'preview_link' => esc_url('https://tourfic.com/preview/cars/honda-city/'),
										),
										'design-2' => array(
											'title' => esc_html__('Design 2', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-coming-soon.png",
											'disabled' => true
										),
									),
									'default'  => 'design-1',
								),
							
								array(
									'id'         => 'single-car-layout',
									'type'       => 'switch_group',
									'column'  	 => 4,
									'label'      => esc_html__( 'Single Car Template Sections', 'tourfic' ),
									'subtitle'   => esc_html__( 'You can able to change section positions by Drag & Drop.', 'tourfic' ),
									'dependency' => array( 'single-car', '==', 'design-1' ),
									'default'    => array(
										array(
											'label'  => esc_html__( 'Description', 'tourfic' ),
											'slug'   => 'description',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Car info', 'tourfic' ),
											'slug'   => 'car-info',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Benefits', 'tourfic' ),
											'slug'   => 'benefits',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Include/Exclude', 'tourfic' ),
											'slug'   => 'inc-exc',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'Location', 'tourfic' ),
											'slug'   => 'location',
											'status' => 1,
										),
										array(
											'label'  => esc_html__( 'FAQs', 'tourfic' ),
											'slug'   => 'faq',
											'status' => 1,
										)
									),
								),
								
								array(
									'id'      => 'car-title',
									'type'    => 'heading',
									'content' => esc_html__( 'Car Archive & Search Result Page', 'tourfic' ),
									'class'   => 'tf-field-class',
								),
								array(
									'id'       => 'car-archive',
									'type'     => 'imageselect',
									'label'    => esc_html__( 'Select Archive & Search Result Template', 'tourfic' ),
									'options'  => array(
										'design-1' => array(
											'title' => esc_html__('Design 1', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-archive-car-design-1.png",
										),
										'design-2' => array(
											'title' => esc_html__('Design 2', 'tourfic'),
											'url'   => TF_ASSETS_ADMIN_URL . "images/template/preview-coming-soon.png",
											'disabled' => true
										),
									),
									'default'  => 'design-1',
								),
								array(
									'id'      => 'car_archive_design_1_bannar',
									'type'    => 'image',
									'label'    => esc_html__( 'Archive & Search Result Banner Image', 'tourfic' ),
									'subtitle' => esc_html__( 'Upload Banner Image for this tour archive template.', 'tourfic' ),
									'library' => 'image',
									// 'dependency' => array( 'car-archive', '==', 'design-1' ),
								),
								array(
									'id'         => 'car_archive_view',
									'type'       => 'select',
									'label'      => esc_html__( 'Archive Layout', 'tourfic' ),
									'options'    => array(
										'grid' => esc_html__( 'Grid', 'tourfic' ),
										'list' => esc_html__( 'List', 'tourfic' ),
									),
									'default'    => 'grid',
									// 'dependency' => array( 'car-archive', '==', 'design-1' ),
								),
								array(
									'id'         => 'car_archive_driver_min_age',
									'type'       => 'number',
									'label'      => esc_html__( 'Archive Filter: Driver Minimum Age', 'tourfic' ),
									'subtitle'      => esc_html__( "This setting allows you to display the driver's minimum age on the archive and search results pages.", 'tourfic' ),
									'default'    => 18,
								),
								array(
									'id'         => 'car_archive_driver_max_age',
									'type'       => 'number',
									'label'      => esc_html__( 'Archive Filter: Driver Maximum Age', 'tourfic' ),
									'subtitle'      => esc_html__( "This setting allows you to display the driver's maximum age on the archive and search results pages.", 'tourfic' ),
									'default'    => 40,
								),
								
								array(
									'id'      => 'car_archive_notice',
									'type'    => 'notice',
									'content' => esc_html__( 'Edit the sidebar filter from Appearance -> Widgets', 'tourfic' ),
								),
							),
						),
					),
				),
				array(
					'id'       => 'container_heading',
					'type'     => 'heading',
					'label'    => esc_html__( 'Container Settings', 'tourfic' ),
					'subtitle' => esc_html__( 'Select your preferred container layout', 'tourfic' ),
				),
				array(
					'id'       => 'tf-container',
					'type'     => 'imageselect',
					'label'    => esc_html__( 'Select Container Layout', 'tourfic' ),
					'img-width'=> '120',
					'img-height'=> '120',
					'options'  => array(
						'boxed' => array(
							'title' => esc_html__('Boxed', 'tourfic'),
							'url'   => TF_ASSETS_ADMIN_URL . "images/boxed.png",
						),
						'full-width' => array(
							'title'	=> esc_html__('Full width', 'tourfic'),
							'url' 	=> TF_ASSETS_ADMIN_URL."images/full-width.png",
						),
					),
					'default'  => 'boxed',
				),
				array(
					'id'       => 'tf-container-width',
					'type'     => 'number',
					'label'    => esc_html__( 'Container Width (px)', 'tourfic' ),
					'default'  => '1280',
					'dependency'  => array( 'tf-container', '==', 'boxed' ),
					'attributes' => array(
						'min' => '770',
						'max' => '1920',
						'step'=> '10'
					),
				),
			),
		),

		//Appearance
		'appearance'               => array(
			'title'  => esc_html__( 'Appearance', 'tourfic' ),
			'icon'   => 'fas fa-palette',
			'fields' => array(
				array(
					'id'       => 'appearance_heading',
					'type'     => 'heading',
					'label'    => esc_html__( 'Preset Colors', 'tourfic' ),
					'subtitle' => esc_html__( 'These colors will be used throughout your website. Choose between these presets or create your own custom palette.', 'tourfic' ),
				),
				array(
					'id'       => 'color-palette-template',
					'type'     => 'colorpalette',
					'inline'   => true,
					'options'  => array(
						'design-1' => array(
							'title' => 'Palette 1',
							'colors' => [
								'#0E3DD8',
								'#0A2B99',
								'#1C2130',
								'#494D59'
							]
						),
						'design-2' => array(
							'title' => 'Palette 2',
							'colors' => [
								'#B58E53',
								'#917242',
								'#30281C',
								'#595349'
							]
						),
						'design-3' => array(
							'title' => 'Palette 3',
							'colors' => [
								'#F97415',
								'#C75605',
								'#30241C',
								'#595049'
							]
						),
						'design-4' => array(
							'title' => 'Palette 4',
							'colors' => [
								'#003061',
								'#002952',
								'#1C2630',
								'#495159'
							]
						),
						'custom' => array(
							'title' => 'Custom Palette',
							'colors' => function_exists('tf_custom_color_palette_values') ? tf_custom_color_palette_values() : '',
						)
					),
					'default'  => 'design-1',
				),
				// Design 1 Fields
				array(
					'id'       => 'tf-d1-brand',
					'label'   => esc_html__( 'Brand Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'default' => '#0E3DD8',
						'dark' => '#0A2B99',
						'lite' => '#C9D4F7',
					),
					'colors'   => array(
						'default' => esc_html__( 'Default', 'tourfic' ),
						'dark' => esc_html__( 'Dark', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-1' ),
					),
				),
				array(
					'id'       => 'tf-d1-text',
					'label'   => esc_html__( 'Text Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'heading' => '#1C2130',
						'paragraph' => '#494D59',
						'lite' => '#F3F5FD',
					),
					'colors'   => array(
						'heading' => esc_html__( 'Heading', 'tourfic' ),
						'paragraph' => esc_html__( 'Paragraph', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-1' ),
					),
				),
				array(
					'id'       => 'tf-d1-border',
					'label'   => esc_html__( 'Border Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'default' => '#16275F',
						'lite' => '#D1D7EE',
					),
					'colors'   => array(
						'default' => esc_html__( 'Default', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-1' ),
					),
				),
				array(
					'id'       => 'tf-d1-filling',
					'label'   => esc_html__( 'Filling Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'background' => '#ffffff',
						'foreground' => '#F5F7FF',
					),
					'colors'   => array(
						'background' => esc_html__( 'Background', 'tourfic' ),
						'foreground' => esc_html__( 'Foreground', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-1' ),
					),
				),
				// Design 2 Fields
				array(
					'id'       => 'tf-d2-brand',
					'label'   => esc_html__( 'Brand Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'default' => '#B58E53',
						'dark' => '#917242',
						'lite' => '#FAEEDC',
					),
					'colors'   => array(
						'default' => esc_html__( 'Default', 'tourfic' ),
						'dark' => esc_html__( 'Dark', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-2' ),
					),
				),
				array(
					'id'       => 'tf-d2-text',
					'label'   => esc_html__( 'Text Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'heading' => '#30281C',
						'paragraph' => '#595349',
						'lite' => '#FDF9F3',
					),
					'colors'   => array(
						'heading' => esc_html__( 'Heading', 'tourfic' ),
						'paragraph' => esc_html__( 'Paragraph', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-2' ),
					),
				),
				array(
					'id'       => 'tf-d2-border',
					'label'   => esc_html__( 'Border Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'default' => '#5F4216',
						'lite' => '#EEE2D1',
					),
					'colors'   => array(
						'default' => esc_html__( 'Default', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-2' ),
					),
				),
				array(
					'id'       => 'tf-d2-filling',
					'label'   => esc_html__( 'Filling Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'background' => '#ffffff',
						'foreground' => '#FDF9F3',
					),
					'colors'   => array(
						'background' => esc_html__( 'Background', 'tourfic' ),
						'foreground' => esc_html__( 'Foreground', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-2' ),
					),
				),
				// Design 3 Fields
				array(
					'id'       => 'tf-d3-brand',
					'label'   => esc_html__( 'Brand Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'default' => '#F97415',
						'dark' => '#C75605',
						'lite' => '#FDDCC3',
					),
					'colors'   => array(
						'default' => esc_html__( 'Default', 'tourfic' ),
						'dark' => esc_html__( 'Dark', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-3' ),
					),
				),
				array(
					'id'       => 'tf-d3-text',
					'label'   => esc_html__( 'Text Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'heading' => '#30241C',
						'paragraph' => '#595049',
						'lite' => '#FDF7F3',
					),
					'colors'   => array(
						'heading' => esc_html__( 'Heading', 'tourfic' ),
						'paragraph' => esc_html__( 'Paragraph', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-3' ),
					),
				),
				array(
					'id'       => 'tf-d3-border',
					'label'   => esc_html__( 'Border Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'default' => '#5F3416',
						'lite' => '#EEDDD1',
					),
					'colors'   => array(
						'default' => esc_html__( 'Default', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-3' ),
					),
				),
				array(
					'id'       => 'tf-d3-filling',
					'label'   => esc_html__( 'Filling Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'background' => '#ffffff',
						'foreground' => '#FFF9F5',
					),
					'colors'   => array(
						'background' => esc_html__( 'Background', 'tourfic' ),
						'foreground' => esc_html__( 'Foreground', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-3' ),
					),
				),
				// Design 4 Fields
				array(
					'id'       => 'tf-d4-brand',
					'label'   => esc_html__( 'Brand Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'default' => '#003061',
						'dark' => '#002952',
						'lite' => '#C2E0FF',
					),
					'colors'   => array(
						'default' => esc_html__( 'Default', 'tourfic' ),
						'dark' => esc_html__( 'Dark', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-4' ),
					),
				),
				array(
					'id'       => 'tf-d4-text',
					'label'   => esc_html__( 'Text Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'heading' => '#1C2630',
						'paragraph' => '#495159',
						'lite' => '#F3F8FD',
					),
					'colors'   => array(
						'heading' => esc_html__( 'Heading', 'tourfic' ),
						'paragraph' => esc_html__( 'Paragraph', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-4' ),
					),
				),
				array(
					'id'       => 'tf-d4-border',
					'label'   => esc_html__( 'Border Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'default' => '#163A5F',
						'lite' => '#D1DFEE',
					),
					'colors'   => array(
						'default' => esc_html__( 'Default', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-4' ),
					),
				),
				array(
					'id'       => 'tf-d4-filling',
					'label'   => esc_html__( 'Filling Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'default'  => array(
						'background' => '#ffffff',
						'foreground' => '#F5FAFF',
					),
					'colors'   => array(
						'background' => esc_html__( 'Background', 'tourfic' ),
						'foreground' => esc_html__( 'Foreground', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'design-4' ),
					),
				),
				// Custom Palette
				array(
					'id'       => 'tf-custom-brand',
					'label'   => esc_html__( 'Brand Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'default' => esc_html__( 'Default', 'tourfic' ),
						'dark' => esc_html__( 'Dark', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'custom' ),
					),
				),
				array(
					'id'       => 'tf-custom-text',
					'label'   => esc_html__( 'Text Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'heading' => esc_html__( 'Heading', 'tourfic' ),
						'paragraph' => esc_html__( 'Paragraph', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'custom' ),
					),
				),
				array(
					'id'       => 'tf-custom-border',
					'label'   => esc_html__( 'Border Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'default' => esc_html__( 'Default', 'tourfic' ),
						'lite' => esc_html__( 'Lite', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'custom' ),
					),
				),
				array(
					'id'       => 'tf-custom-filling',
					'label'   => esc_html__( 'Filling Color', 'tourfic' ),
					'type'     => 'color',
					'multiple' => true,
					'inline'   => true,
					'colors'   => array(
						'background' => esc_html__( 'Background', 'tourfic' ),
						'foreground' => esc_html__( 'Foreground', 'tourfic' ),
					),
					'dependency'   => array(
						array( 'color-palette-template', '==', 'custom' ),
					),
				),
			)
		),

		// Tour Options
		'tour'                  => array(
			'title'  => esc_html__( 'Tour Options', 'tourfic' ),
			'icon'   => 'fas fa-umbrella-beach',
			'post_dependency' => 'tour',
			'fields' => array(),
		),
		'single_tour'           => array(
			'title'  => esc_html__( 'Single Page', 'tourfic' ),
			'parent' => 'tour',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'       => 'signle_tour_heading',
					'type'     => 'heading',
					'label'    => esc_html__( 'Global Settings for Single Tours Page', 'tourfic' ),
					'subtitle' => esc_html__( 'These options can be overridden from Single Tour Settings.', 'tourfic' ),
				),
				array(
					'id'      => 'tour-option-notice-one',
					'type'    => 'notice',
					'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="https://themefic.com/docs/tourfic/tourfic-settings/tourfic-tours-options/" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
				),

				array(
					'id'        => 't-review',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Review Section', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
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
					'id'        => 't-related',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Related Tour Section', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),
				array(
					'id'       => 'rt-title',
					'type'     => 'text',
					'label'    => esc_html__( 'Related Tour Title', 'tourfic' ),
					'subtitle' => esc_html__( "This title will be displayed as the section title in the 'Related Tours' section on individual tour pages.", 'tourfic' ),
					'default'  => esc_html__( 'You might also like', 'tourfic' ),
					'dependency'  => array(
						array( 't-related', '==', 'false' ),
					),
				),
				array(
					'id'       => 'rt-description',
					'type'     => 'text',
					'label'    => esc_html__( 'Related Tour Description', 'tourfic' ),
					'subtitle' => esc_html__( "This Description will be displayed as the Description in the 'Related Tours' section on individual tour pages.", 'tourfic' ),
					'default'  => esc_html__( 'Travel is my life. Since 1999, I have been traveling around the world nonstop. If you also love travel, you are in the right place!', 'tourfic' ),
					'dependency'  => array(
						array( 't-related', '==', 'false' ),
					),
				),
				array(
					'id'      => 'rt_display',
					'type'    => 'radio',
					'label'   => esc_html__( 'Related Tour display logic', 'tourfic' ),
					'options' => array(
						'auto'     => esc_html__( 'Auto', 'tourfic' ),
						'selected' => esc_html__( 'Selected', 'tourfic' )
					),
					'default' => 'auto',
					'inline'  => true,
					'dependency'  => array(
						array( 't-related', '==', 'false' ),
					),
				),
				array(
					'id'         => 'tf-related-tours',
					'type'       => 'select2',
					'multiple'   => 'true',
					'label'      => esc_html__( 'Choose Your Related Tours', 'tourfic' ),
					'subtitle' => esc_html__( 'Select the tour you wish to feature in the “Related Tour” section on each single tour page.', 'tourfic' ),
					'options'    => 'posts',
					'query_args' => array(
						'post_type'      => 'tf_tours',
						'posts_per_page' => - 1,
					),
					'dependency' => array (
						array( 'rt_display', '==', 'selected' ),
						array( 't-related', '==', 'false' ),
					),
				),
				array(
					'id'       => 't-enquiry-email',
					'type'     => 'text',
					'label'    => esc_html__( 'Email for Enquiry Form', 'tourfic' ),
					'subtitle' => esc_html__( 'Enter the email address that will receive all submissions from the enquiry form.', 'tourfic' ),
				),
				array(
					'id'        => 't-auto-draft',
					'type'      => 'switch',
					'label'     => esc_html__( 'Expired Tours for Backend', 'tourfic' ),
					'subtitle'  => esc_html__( 'If this option is activated, the tour will automatically expire after the set date. The status will update every 24 hours.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),
				array(
					'id'        => 't-show-expire-tour',
					'type'      => 'switch',
					'label'     => esc_html__( 'Show All Tours (Publish + Expired)', 'tourfic' ),
					'subtitle'  => esc_html__( "Enabling this option will display all tours, regardless of whether their status is 'Published' or 'Expired'.", 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),
				array(
					'id'        => 't-hide-start-price',
					'type'      => 'switch',
					'label'     => esc_html__( 'Hide Starting Price', 'tourfic' ),
					'subtitle'  => esc_html__( 'By enabling this feature, the starting price will be concealed from the tour listings.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),
				array(
					'id'       => 'tour_archive_price_minimum_settings',
					'type'     => 'select',
					'label'    => esc_html__( 'Show Minimum Price', 'tourfic' ),
					'options'  => array(
						'all'   => esc_html__( 'All', 'tourfic' ),
						'adult'   => esc_html__( 'Adult', 'tourfic' ),
						'child'   => esc_html__( 'Child', 'tourfic' ),
					),
					'default'    => 'All',
				),
				array(
					'id'       => 'tour_booking_form_button_text',
					'type'     => 'text',
					'label'    => esc_html__( 'Change Booking Form Button Text', 'tourfic' ),
					'subtitle'  => esc_html__( 'With this option, you can change the text of the booking form button on the single tour pages.', 'tourfic' ),
					'default'    => esc_html__('Book Now', 'tourfic'),
				),
			),
		),
		// Itinerary Settings
		'tour_itinerary'        => array(
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
									'id'      => 'tour-option-notice-three',
									'type'    => 'notice',
									'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="' . Helper::tf_utm_generator("https://themefic.com/docs/tourfic/tourfic-settings/tourfic-tours-options/#itinerary", array( 'utm_medium' => 'settings_doc_tour-itinerary' )) . '" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
								),							
							),
						),
						array(
							'id'     => 'itinerary-downloader-setting',
							'title'  => esc_html__('Itinerary Downloader Settings', 'tourfic'),
							'icon'   => 'fa fa-gear',
							'fields' => array(
								array(
									'id'      => 'tour-option-notice-three',
									'type'    => 'notice',
									'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="' . Helper::tf_utm_generator("https://themefic.com/docs/tourfic/tourfic-settings/tourfic-tours-options/#itinerary", array( 'utm_medium' => 'settings_doc_tour-itinerary' )) . '" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
								),
							),
						),
					),
				),
			),
		),
		'hotel_option'          => array(
			'title'  => esc_html__( 'Hotel Options', 'tourfic' ),
			'icon'   => 'fas fa-hotel',
			'post_dependency' => 'hotel',
			'fields' => array(),
		),
		'single_page'           => array(
			'title'  => esc_html__( 'Single Page', 'tourfic' ),
			'parent' => 'hotel_option',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'        => 'label_off_heading',
					'type'      => 'heading',
					'label'     => esc_html__( 'Global Settings for Single Hotel Page', 'tourfic' ),
					'sub_title' => esc_html__( 'These options can be overridden from Single Hotel Settings.', 'tourfic' ),
				),

				array(
					'id'      => 'hotel-option-notice-one',
					'type'    => 'notice',
					'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="'. Helper::tf_utm_generator("https://themefic.com/docs/tourfic/tourfic-settings/tourfic-hotel-options/", array( 'utm_medium' => 'settings_doc_hotel-options' )) .'" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'        => 'h-review',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Review Section', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false,
				),

				array(
					'id'        => 'h-share',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Share Option', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false,
				),
				//Feature filter setting
				array(
					'id'        => 'feature-filter',
					'type'      => 'switch',
					'label'     => esc_html__( 'Filter By Feature', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => true,
				),
				array(
					'id'       => 'h-enquiry-email',
					'type'     => 'text',
					'label'    => esc_html__( 'Email for Enquiry Form', 'tourfic' ),
					'subtitle' => esc_html__( 'Enter the email address that will receive all submissions from the enquiry form.', 'tourfic' ),
				),
				array(
					'id'       => 'hotel_archive_price_minimum_settings',
					'type'     => 'select',
					'label'    => esc_html__( 'Show Minimum Price', 'tourfic' ),
					'options'  => array(
						'all'   => esc_html__( 'All', 'tourfic' ),
						'adult'   => esc_html__( 'Adult', 'tourfic' ),
						'child'   => esc_html__( 'Child', 'tourfic' ),
					),
					'default'    => 'All',
				),
				array(
					'id'           => 'hotel_facilities_cats',
					'type'         => 'repeater',
					'label'        => esc_html__( 'Facilities Categories', 'tourfic' ),
					'button_title' => esc_html__( 'Add New', 'tourfic' ),
					'field_title'  => 'hotel_facilities_cat_name',
					'fields'       => array(
						array(
							'id'    => 'hotel_facilities_cat_name',
							'type'  => 'text',
							'label' => esc_html__( 'Category Name', 'tourfic' ),
						),
						array(
							'id'    => 'hotel_facilities_cat_icon',
							'type'  => 'icon',
							'label' => esc_html__( 'Category Icon', 'tourfic' ),
						),
					),
				),
				array(
					'id'       => 'hotel_booking_form_button_text',
					'type'     => 'text',
					'label'    => esc_html__( 'Change Booking Form Button Text', 'tourfic' ),
					'subtitle'  => esc_html__( 'With this option, you can change the text of the booking form button on the single hotel pages.', 'tourfic' ),
					'default'    => esc_html__('Reserve Now', 'tourfic'),
				),
				array(
					'id'       => 'hotel_booking_check_button_text',
					'type'     => 'text',
					'label'    => esc_html__( 'Change Book Availability Button Text', 'tourfic' ),
					'subtitle'  => esc_html__( 'With this option, you can change the text of the check availability button on the single hotel pages.', 'tourfic' ),
					'default'    => esc_html__('Check Availability', 'tourfic'),
				),
			),
		),
		'room_config'=> array(
			'title'  => esc_html__( 'Room Config', 'tourfic' ),
			'parent' => 'hotel_option',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'hotel_room_heading',
					'type'  => 'heading',
					'label' => esc_html__( 'Global Configuration for Hotel Rooms', 'tourfic' ),
				),
				array(
					'id'      => 'hotel-option-notice-two',
					'type'    => 'notice',
					'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="'. Helper::tf_utm_generator("https://themefic.com/docs/tourfic/tourfic-settings/tourfic-hotel-options/#room", array( 'utm_medium' => 'settings_doc_hotel-options_room' )) .'" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'       => 'enable_child_age_limit',
					'type'     => 'switch',
					'label'    => esc_html__( 'Children Age Limit', 'tourfic' ),
					'subtitle' => esc_html__( 'Turn on this option to set the Maximum age limit for Children. This can be overridden from Single Hotel Settings.', 'tourfic' ),
				),
				array(
					'id'         => 'children_age_limit',
					'type'       => 'number',
					'label'      => esc_html__( 'Specify Maximum Age Limit', 'tourfic' ),
					'subtitle'   => esc_html__( 'Set the maximum age limit for children', 'tourfic' ),
					'attributes' => array(
						'min' => '0',
					),
					'dependency' => array( 'enable_child_age_limit', '==', '1' ),
				),
			),
		),

		//Apartment Options
		'apartment_option'      => array(
			'title'  => esc_html__( 'Apartment Options', 'tourfic' ),
			'icon'   => 'fa-solid fa-house-chimney',
			'post_dependency' => 'apartment',
			'fields' => array(),
		),
		'apartment_single_page' => array(
			'title'  => esc_html__( 'Single Page', 'tourfic' ),
			'parent' => 'apartment_option',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'label_off_heading',
					'type'  => 'heading',
					'label' => esc_html__( 'Single Apartment Settings', 'tourfic' ),
					'subtitle'   => esc_html__( 'These options can be overridden from Single Apartment Settings.', 'tourfic' ),
				),

				array(
					'id'      => 'apartment-option-notice',
					'type'    => 'notice',
					'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="'. Helper::tf_utm_generator("https://themefic.com/docs/tourfic/tourfic-settings/apartment-options/", array( 'utm_medium' => 'settings_doc_apartment-options' )) .'" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'           => 'amenities_cats',
					'type'         => 'repeater',
					'label'        => esc_html__( 'Amenities Categories', 'tourfic' ),
					'button_title' => esc_html__( 'Add New', 'tourfic' ),
					'field_title'  => 'amenities_cat_name',
					'fields'       => array(
						array(
							'id'    => 'amenities_cat_name',
							'type'  => 'text',
							'label' => esc_html__( 'Category Name', 'tourfic' ),
						),
						array(
							'id'    => 'amenities_cat_icon',
							'type'  => 'icon',
							'label' => esc_html__( 'Category Icon', 'tourfic' ),
						),
					),
				),

				array(
					'id'        => 'disable-apartment-review',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Review Section', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false
				),

				array(
					'id'        => 'disable-apartment-share',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Share Option', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false
				),

				array(
					'id'        => 'disable-related-apartment',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Related Section', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false
				),
				array(
					'id'       => 'apartment_booking_form_button_text',
					'type'     => 'text',
					'label'    => esc_html__( 'Change Booking Form Button Text', 'tourfic' ),
					'subtitle'  => esc_html__( 'With this option, you can change the text of the booking form button on the single apartment pages.', 'tourfic' ),
					'default'    => esc_html__('Reserve', 'tourfic'),
				),
			),
		),

		//Car Options
		'car_option'      => array(
			'title'  => esc_html__( 'Car Options', 'tourfic' ),
			'icon'   => 'fa-solid fa-car',
			'post_dependency' => 'carrentals',
			'fields' => array(),
		),
		'car_single_page' => array(
			'title'  => esc_html__( 'Single Page', 'tourfic' ),
			'parent' => 'car_option',
			'icon'   => 'fa fa-cog',
			'fields' => array(
				array(
					'id'    => 'label_off_heading',
					'type'  => 'heading',
					'label' => esc_html__( 'Single Car Settings', 'tourfic' ),
					'subtitle'   => esc_html__( 'These options can be overridden from Single Car Settings.', 'tourfic' ),
				),

				array(
					'id'      => 'apartment-option-notice',
					'type'    => 'notice',
					'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="'. Helper::tf_utm_generator("https://themefic.com/docs/tourfic/settings/car-options/#Single_Page_Settings", array( 'utm_medium' => 'settings_doc_car-options_single' )).'" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
				),

				array(
					'id'        => 'disable-car-share',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Share Option', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false
				),
				array(
					'id'       => 'car_booking_form_button_text',
					'type'     => 'text',
					'label'    => esc_html__( 'Change Booking Form Button Text', 'tourfic' ),
					'subtitle'  => esc_html__( 'With this option, you can change the text of the booking form button on the single car pages.', 'tourfic' ),
					'default'    => esc_html__('Reserve', 'tourfic'),
				),
			),
		),
		'car_operating_hours' => array(
			'title'  => esc_html__( 'Operating Hours', 'tourfic' ),
			'parent' => 'car_option',
			'icon'   => 'fa fa-hourglass-half',
			'fields' => array(
				array(
					'id'    => 'label_off_heading',
					'type'  => 'heading',
					'label' => esc_html__( 'Operating Hours Settings', 'tourfic' ),
					'subtitle'   => esc_html__( 'These options can be overridden from Operating Hours Settings.', 'tourfic' ),
				),

				array(
					'id'      => 'apartment-option-notice',
					'type'    => 'notice',
					'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="'. Helper::tf_utm_generator("https://themefic.com/docs/tourfic/settings/car-options/#Single_Page_Settings", array( 'utm_medium' => 'settings_doc_car-options_single' )) .'" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
				),

				array(
					'id'        => 'disable-car-time-slots',
					'type'      => 'switch',
					'label'     => esc_html__( 'Modify Time Slots', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false
				),
				array(
					'id'       => 'car_time_interval',
					'type'     => 'number',
					'label'    => esc_html__( 'Time Interval', 'tourfic' ),
					'subtitle' => esc_html__( 'Set the time interval for search form time slots.', 'tourfic' ),
					'default'  => '30',
					'attributes' => array(
						'min' => '10',
						'max' => '60',
					),
					'dependency' => array( 'disable-car-time-slots', '==', '1' ),
				),
				
			),
		),

		// Search Options
		'search'                => array(
			'title'  => esc_html__( 'Search', 'tourfic' ),
			'icon'   => 'fas fa-search',
			'fields' => array(
				array(
					'id'      => 'search-option-heading',
					'type'    => 'heading',
					'label' => esc_html__( 'Search Page Settings', 'tourfic' ),
					'subtitle'   => esc_html__( 'These settings apply to the search result page of Hotels/Tours/Apartments.', 'tourfic' ),
					'class'   => 'tf-field-class',
				),
				array(
					'id'      => 'search-option-notice',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="'. Helper::tf_utm_generator("https://themefic.com/docs/tourfic/tourfic-settings/search-page/", array( 'utm_medium' => 'settings_doc_search-page' )) .'" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
				),
				// Registration
				array(
					'id'          => 'search-result-page',
					'type'        => 'select2',
					'placeholder' => esc_html__( 'Select a page', 'tourfic' ),
					'label'       => esc_html__( 'Select Search Result Page', 'tourfic' ),
					/* translators: %s: Page template name wrapped in <code> tag */
					'description' => sprintf( esc_html__( 'This page will be used to show the Search form Results. Please make sure Page template: %s is selected while creating this page.', 'tourfic' ), '<code>' . esc_html__( 'Tourfic - Search Result', 'tourfic' ) . '</code>' ),
					'options'     => 'posts',
					'query_args'  => array(
						'post_type'      => 'page',
						'posts_per_page' => - 1,
					),
					'default'     => !empty( get_option( 'tf_search_page_id	' ) ) ? get_option( 'tf_search_page_id	' ) : tf_search_page_default(),
				),

				array(
					'id'       => 'posts_per_page',
					'type'     => 'number',
					'label'    => esc_html__( 'Search Items to show per page', 'tourfic' ),
					'subtitle' => esc_html__( 'Add the total number of hotels/tours/apartments you want to show per page on the Search result.', 'tourfic' ),
					'default'  => 8,
				),

				array(
					'id'       => 'hotel_search_heading',
					'type'     => 'heading',
					'label'    => esc_html__( 'Hotel Search', 'tourfic' ),
				),
				array(
					'id'        => 'date_hotel_search',
					'type'      => 'switch',
					'label'     => esc_html__( 'Date Required in Hotel Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Activate this feature to enable users to pick their check-in and check-out dates for searching.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => true,
				),
				array(
					'id'        => 'disable_hotel_child_search',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Child in Hotel Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Turn on this setting to hide the child option from the search form.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),
				array(
					'id'        => 'required_location_hotel_search',
					'type'      => 'switch',
					'label'     => esc_html__( ' Location Required in Hotel Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Enable this setting to make the location field required for the search form.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => true,
				),
				array(
					'id'        => 'hide_hotel_location_search',
					'type'      => 'switch',
					'label'     => esc_html__( 'Hide Location in Hotel Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Enable this setting to hide the location option from the search form.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false,
					'dependency' => array( "required_location_hotel_search", "==", "false")
				),
				array(
					'id'       => 'tour_search_heading',
					'type'     => 'heading',
					'label'    => esc_html__( 'Tour Search', 'tourfic' ),
				),
				array(
					'id'        => 'date_tour_search',
					'type'      => 'switch',
					'label'     => esc_html__( 'Date Required in Tour Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Activate this feature to enable users to pick their check-in and check-out dates for searching.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => true,
				),
				array(
					'id'        => 'disable_child_search',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Child in Tour Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Turn on this setting to hide the child option from the search form.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),
				array(
					'id'        => 'disable_infant_search',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Infant in Tour Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Turn on this setting to hide the infant option from the search form.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),
				array(
					'id'        => 'required_location_tour_search',
					'type'      => 'switch',
					'label'     => esc_html__( ' Location Required in Tour Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Enable this setting to make the location field required for the search form.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => true,
				),
				array(
					'id'        => 'hide_tour_location_search',
					'type'      => 'switch',
					'label'     => esc_html__( 'Hide Location in Tour Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Enable this setting to hide the location option from the search form.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => false,
					'dependency' => array( "required_location_tour_search", "==", "false")
				),
				array(
					'id'       => 'apartment_search_heading',
					'type'     => 'heading',
					'label'    => esc_html__( 'Apartment Search', 'tourfic' ),
				),
				array(
					'id'        => 'date_apartment_search',
					'type'      => 'switch',
					'label'     => esc_html__( 'Date Required in Apartment Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Activate this feature to enable users to pick their check-in and check-out dates for searching.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),
				array(
					'id'        => 'disable_apartment_child_search',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Child in Apartment Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Turn on this setting to hide the child option from the search form.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),
				array(
					'id'        => 'disable_apartment_infant_search',
					'type'      => 'switch',
					'label'     => esc_html__( 'Disable Infant in Apartment Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Turn on this setting to hide the infant option from the search form.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),
				array(
					'id'       => 'car_search_heading',
					'type'     => 'heading',
					'label'    => esc_html__( 'Car Search', 'tourfic' ),
				),
				array(
					'id'        => 'pick_drop_car_search',
					'type'      => 'switch',
					'label'     => esc_html__( 'Pickup & Dropoff Location Required in Car Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Activate this feature to enable users to pick their pickup and dropoff Location for searching.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => true
				),
				array(
					'id'        => 'pick_drop_date_car_search',
					'type'      => 'switch',
					'label'     => esc_html__( 'Pickup & Dropoff Date Required in Car Search', 'tourfic' ),
					'subtitle'  => esc_html__( 'Activate this feature to enable users to pick their pickup and dropoff Date for searching.', 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
					'default'   => true
				),
			),
		),

		// Miscellaneous Options
		'miscellaneous'         => array(
			'title'  => esc_html__( 'Miscellaneous', 'tourfic' ),
			'icon'   => 'fas fa-globe',
			'fields' => array(),
		),

		/**
		 * Google Map
		 *
		 * Sub Menu
		 */
		'map_settings'          => array(
			'title'  => esc_html__( 'Map Settings', 'tourfic' ),
			'parent' => 'miscellaneous',
			'icon'   => 'fas fa-umbrella-beach',
			'fields' => array(
				array(
					'id'      => 'map_settings_heading',
					'type'    => 'heading',
					'content' => esc_html__( 'Map Settings', 'tourfic' )
				),
				array(
					'id'       => 'google-page-option',
					'type'     => 'select',
					'label'    => esc_html__( 'Select Map', 'tourfic' ),
					'subtitle' => esc_html__( 'This map is used to dynamically search your hotel/tour location on the option panel. The frontend map information is based on this data. We use "OpenStreetMap” by default. You can also use Google Map. To use Google map, you need to insert your Google Map API Key.', 'tourfic' ),
					'options'  => array(
						'default' => esc_html__( 'Default Map', 'tourfic' ),
					),
					'default'  => 'default'
				),
			),
		),
		/**
		 * Wishlist
		 *
		 * Sub Menu
		 */
		'wishlist'              => array(
			'title'  => esc_html__( 'Wishlist', 'tourfic' ),
			'parent' => 'miscellaneous',
			'icon'   => 'fas fa-heart',
			'fields' => array(
				array(
					'id'      => 'wishlist_heading',
					'type'    => 'heading',
					'label' => esc_html__( 'Wishlist Settings', 'tourfic' ),
					'subtitle' => esc_html__( 'The wishlist feature enables customers to curate a collection of hotels, tours, and apartments they are interested in or plan to book in the future.', 'tourfic' ),
				),
				array(
					'id'      => 'wishlistsettings-official-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="'. Helper::tf_utm_generator("https://themefic.com/docs/tourfic/tourfic-settings/tourfic-miscellaneous/#wishlist-settings", array( 'utm_medium' => 'settings_doc_miscellaneous_wishlist' )) .'" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
				),

				array(
					'id'      => 'wl-for',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Enable Wishlist for', 'tourfic' ),
					'options' => array(
						'li' => esc_html__( 'Logged in User', 'tourfic' ),
						'lo' => esc_html__( 'Logged out User', 'tourfic' ),
					),
					'default' => array( 'li', 'lo' )
				),

				array(
					'id'      => 'wl-bt-for',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Show Wishlist Button on', 'tourfic' ),
					'options' => array(
						'1' => esc_html__( 'Single Hotel Page', 'tourfic' ),
						'2' => esc_html__( 'Single Tour Page', 'tourfic' ),
						'3' => esc_html__( 'Single Apartment Page', 'tourfic' ),
					),
					'default' => array( '1', '2', '3' ),
				),

				array(
					'id'          => 'wl-page',
					'type'        => 'select2',
					'label'       => esc_html__( 'Select Wishlist Page', 'tourfic' ),
					'subtitle' => esc_html__( 'Choose a page to serve as the wishlist Page.', 'tourfic' ),
					'placeholder' => esc_html__( 'Select Wishlist Page', 'tourfic' ),
					'options'     => 'posts',
					'query_args'  => array(
						'post_type'      => 'page',
						'posts_per_page' => - 1,
						'orderby'        => 'post_title',
						'order'          => 'ASC'
					),
					'default' => !empty( get_option('tf_wishlist_page_id') ) ? get_option('tf_wishlist_page_id') : tf_wishlist_page_default(),
				),
			),
		),

		/**
		 * Permalink Settings
		 *
		 * Sub Menu
		 */

		'permalink' => array(
			'title'  => esc_html__( 'Permalink Settings', 'tourfic' ),
			'icon'   => 'fas fa-link',
			'fields' => array(
				array(
					'id'      => 'permalink_heading',
					'type'    => 'heading',
					'label' => esc_html__( 'Permalink Settings', 'tourfic' ),
					'subtitle' => esc_html__( 'Select the URL structure for your Hotels, Tours, and Apartments listings.', 'tourfic' ),
				),
				array(
					'id'      => 'permalink_notice',
					'type'    => 'notice',
					'content' => sprintf(
						/* translators: %s is the documentation URL */
						__( 'Anything confusing? <a href="%s" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>Read Documentation</strong></a>', 'tourfic' ),
						esc_url( Helper::tf_utm_generator( "https://themefic.com/docs/tourfic/tourfic-settings/tourfic-miscellaneous/#permalink-settings", array( 'utm_medium' => 'settings_doc_miscellaneous_permalink' ) ) )
					),
				),				

				array(
					'id'       => 'tour-permalink-setting',
					'type'     => 'text',
					'label'    => esc_html__( 'Tour Permalink', 'tourfic' ),
					'subtitle' => esc_html__( 'Enter a permalink for your tour archive page.', 'tourfic' ),
					'default' => "tours",
					'placeholder' => !empty(get_option("tour_slug")) ? get_option("tour_slug") : "tours",
					
				),
				array(
					'id'       => 'hotel-permalink-setting',
					'type'     => 'text',
					'label'    => esc_html__( 'Hotel Permalink', 'tourfic' ),
					'subtitle' => esc_html__( 'Enter a permalink for your hotel archive page.', 'tourfic' ),
					'default' => "hotels",
					'placeholder' => ! empty(get_option("hotel_slug")) ? get_option("hotel_slug") : "hotels",
				),
				array(
					'id'       => 'apartment-permalink-setting',
					'type'     => 'text',
					'label'    => esc_html__( 'Apartment Permalink', 'tourfic' ),
					'subtitle' => esc_html__( 'Enter a permalink for your apartment archive page.', 'tourfic' ),
					'default' => "apartments",
					'placeholder' => ! empty(get_option("apartment_slug")) ? get_option("apartment_slug") : "apartments",
				),
				array(
					'id'       => 'car-permalink-setting',
					'type'     => 'text',
					'label'    => esc_html__( 'Car Permalink', 'tourfic' ),
					'subtitle' => esc_html__( 'Enter a permalink for your car archive page.', 'tourfic' ),
					'default' => "cars",
					'placeholder' => ! empty(get_option("car_slug")) ? get_option("car_slug") : "cars",
				),
			),
		),
		/**
		 * Review
		 *
		 * Sub Menu
		 */

		'review'       => array(
			'title'  => esc_html__( 'Review', 'tourfic' ),
			'parent' => 'miscellaneous',
			'icon'   => 'fas fa-star',
			'fields' => array(
				array(
					'id'      => 'review_heading',
					'type'    => 'heading',
					'label' => esc_html__( 'Review Settings', 'tourfic' ),
					'subtitle' => esc_html__( 'Configure your Hotel/Tour/Apartment Customer Review Section through this settings panel.', 'tourfic' ),
				),
				array(
					'id'      => 'review-settings-official-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="'. Helper::tf_utm_generator("https://themefic.com/docs/tourfic/tourfic-settings/tourfic-miscellaneous/#review-settings", array( 'utm_medium' => 'settings_doc_miscellaneous_review' )) .'" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'      => 'r-for',
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Enable Review for', 'tourfic' ),
					'options' => array(
						'li' => esc_html__( 'Logged in User', 'tourfic' ),
					),
					'default' => array( 'li' ),
				),

				array(
					'id'        => 'r-auto-publish',
					'type'      => 'switch',
					'label'     => esc_html__( 'Auto Publish Review', 'tourfic' ),
					'subtitle'  => esc_html__( "Reviews will be set to pending by default, awaiting administrative approval. However, if enabled, reviews will be automatically published without requiring the admin's approval.", 'tourfic' ),
					'label_on'  => esc_html__( 'Yes', 'tourfic' ),
					'label_off' => esc_html__( 'No', 'tourfic' ),
				),

				array(
					'id'      => 'r-base',
					'type'    => 'radio',
					'label'   => esc_html__( 'Review Parameter', 'tourfic' ),
					'subtitle' => esc_html__( 'Select the option to calculate reviews on a scale of either 5 or 10.', 'tourfic' ),
					'options' => array(
						'5'  => esc_html__( '5', 'tourfic' ),
						'10' => esc_html__( '10', 'tourfic' ),
					),
					'default' => '5',
				),

				array(
					'id'       => 'r-hotel',
					'class'    => 'disable-sortable',
					'type'     => 'repeater',
					'button_title' => esc_html__( 'Add New', 'tourfic' ),
					'label'        => esc_html__( 'Review Fields for Hotels', 'tourfic' ),
					'subtitle'     => esc_html__( 'Design customer review fields for hotels. Custom fields are permitted.', 'tourfic' ),
					'max'      => '6',
					'drag_only'   => true,
					'field_title'  => 'r-field-type',
					'fields'   => array(
						array(
							'id'    => 'r-field-type',
							'type'  => 'text',
							'label' => esc_html__( 'Review for', 'tourfic' ),
						),

					),
					'default'  => array(
						array(
							'r-field-type' => esc_html__( 'Staff', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Facilities', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Cleanliness', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Comfort', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Value for money', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Location', 'tourfic' ),
						),
					)
				),
				array(
					'id'       => 'r-tour',
					'type'     => 'repeater',
					'button_title' => esc_html__( 'Add New', 'tourfic' ),
					'label'        => esc_html__( 'Review Fields for Tours', 'tourfic' ),
					'subtitle'     => esc_html__( 'Design customer review fields for tours. Custom fields are permitted.', 'tourfic' ),
					'max'      => '6',
					'drag_only'   => true,
					'field_title'  => 'r-field-type',
					'fields'   => array(

						array(
							'id'    => 'r-field-type',
							'type'  => 'text',
							'label' => esc_html__( 'Review for', 'tourfic' ),
						),

					),
					'default'  => array(
						array(
							'r-field-type' => esc_html__( 'Guide', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Transportation', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Value for money', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Safety', 'tourfic' ),
						),
					)
				),
				array(
					'id'       => 'r-apartment',
					'class'    => 'disable-sortable',
					'type'     => 'repeater',
					'button_title' => esc_html__( 'Add New', 'tourfic' ),
					'label'    => esc_html__( 'Review Fields for Apartments', 'tourfic' ),
					'subtitle' => esc_html__( 'Design customer review fields for apartments. Custom fields are permitted.', 'tourfic' ),
					'max'      => '6',
					'drag_only'   => true,
					'field_title'  => 'r-field-type',
					'fields'   => array(
						array(
							'id'    => 'r-field-type',
							'type'  => 'text',
							'label' => esc_html__( 'Review for', 'tourfic' ),
						),

					),
					'default'  => array(
						array(
							'r-field-type' => esc_html__( 'Staff', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Facilities', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Cleanliness', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Comfort', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Value for money', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Location', 'tourfic' ),
						),
					)
				),

				array(
					'id'       => 'r-car',
					'class'    => 'disable-sortable',
					'type'     => 'repeater',
					'button_title' => esc_html__( 'Add New', 'tourfic' ),
					'label'        => esc_html__( 'Review Fields for Cars', 'tourfic' ),
					'subtitle'     => esc_html__( 'Design customer review fields for cars. Custom fields are permitted.', 'tourfic' ),
					'max'      => '6',
					'drag_only'   => true,
					'field_title'  => 'r-field-type',
					'fields'   => array(
						array(
							'id'    => 'r-field-type',
							'type'  => 'text',
							'label' => esc_html__( 'Review for', 'tourfic' ),
						),

					),
					'default'  => array(
						array(
							'r-field-type' => esc_html__( 'Staff', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Facilities', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Cleanliness', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Comfort', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Value for money', 'tourfic' ),
						),
						array(
							'r-field-type' => esc_html__( 'Location', 'tourfic' ),
						),
					)
				),


				array(
					'id'       => 'tf_delete_old_review_fields_button',
					'type'     => 'callback',
					'function' => array( '\Tourfic\App\TF_Review', 'tf_delete_old_review_fields_button'),
				),
				array(
					'id'       => 'tf_delete_old_complete_review_button',
					'type'     => 'callback',
					'function' => array( '\Tourfic\App\TF_Review', 'tf_delete_old_complete_review_button' ),
				),

			),
		),
		/**
		 * optimization Settings
		 *
		 * Sub Menu
		 */
		'optimization' => array(
			'title'  => esc_html__( 'Optimization', 'tourfic' ),
			'parent' => 'miscellaneous',
			'icon'   => 'fas fa-star',
			'fields' => array(
				array(
					'id'      => 'optimization_heading',
					'type'    => 'heading',
					'label' => esc_html__( 'Minification Settings', 'tourfic' ),
					'subtitle'  => esc_html__( "Enhance your website's performance by activating the minification for the files listed below. After enabling each setting, please conduct a thorough test of your site to ensure that these changes do not negatively impact your website's functionality.", 'tourfic' ),
				),
				array(
					'id'      => 'optimize-settings-official-docs',
					'type'    => 'notice',
					'style'   => 'success',
					'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="'.Helper::tf_utm_generator("https://themefic.com/docs/tourfic/tourfic-settings/optimization-settings/", array( 'utm_medium' => 'settings_doc_optimization' )).'" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id'        => 'css_min',
					'type'      => 'switch',
					'label'     => esc_html__( 'Minify CSS', 'tourfic' ),
					'subtitle'  => esc_html__( 'Enable/disable Minification of CSS files included with Tourfic.', 'tourfic' ),
					'label_on'  => esc_html__( 'Enabled', 'tourfic' ),
					'label_off' => esc_html__( 'Disabled', 'tourfic' ),
					'width'     => 100,
					'default'   => true,
				),

				array(
					'id'        => 'js_min',
					'type'      => 'switch',
					'label'     => esc_html__( 'Minify JS', 'tourfic' ),
					'subtitle'  => esc_html__( 'Enable/disable Minification of JS files included with Tourfic.', 'tourfic' ),
					'label_on'  => esc_html__( 'Enabled', 'tourfic' ),
					'label_off' => esc_html__( 'Disabled', 'tourfic' ),
					'width'     => 100,
					'default'   => true,
				),
			),
		),


		// Email Settings
		'email-settings'          => array(
			'title'  => esc_html__( 'Email Settings', 'tourfic' ),
			'icon'   => 'fa fa-envelope',
			'fields' => array(),
		),

		//email template settings
		'email_templates' => array(
			'title'  => esc_html__( 'Email Templates', 'tourfic' ),
			'parent' => 'email-settings',
			'icon'   => 'fas fa-cogs',
			'fields' => array(
				array(
					'id'   => 'email-settings',
					'type' => 'tab',
					'label' => 'Email Templates',
					'subtitle'   => esc_html__( 'Tourfic provides a robust and sophisticated Email Template feature, enabling you to easily design and personalize impressive email templates for your business communications.', 'tourfic' ),
					'tabs' => array(
						array(
							'id'     => 'admin_emails',
							'title'  => esc_html__( 'Admin Email', 'tourfic' ),
							'icon'   => 'fa fa-gear',
							'fields' => array(
								//file upload
								array(
									'id'    => 'brand_logo',
									'type'  => 'image',
									'label' => esc_html__( 'Admin Email Logo', 'tourfic' ),
								),
								array(
									'id'      => 'send_notification',
									'type'    => 'select',
									'label'   => esc_html__( 'Send Notification', 'tourfic' ),
									'options' => array(
										'admin'        => esc_html__( 'Admin', 'tourfic' ),
										'admin_vendor' => esc_html__( 'Admin + Vendor', 'tourfic' ),
										'turn_off'     => esc_html__( 'Turn Off', 'tourfic' ),
									),
									'default' => 'admin',
								),
								array(
									'id'      => 'sale_notification_email',
									'type'    => 'text',
									'label'   => esc_html__( 'Sale Notification Email', 'tourfic' ),
									'default' => get_bloginfo( 'admin_email' ),
								),
								//enable disable admin email
								array(
									'id'      => 'admin_email_disable',
									'type'    => 'switch',
									'label'   => esc_html__( 'Disable Admin Email', 'tourfic' ),
									'default' => 'false',
								),
								//heading
								array(
									'id'    => 'admin_email_heading',
									'type'  => 'heading',
									'label' => esc_html__( 'Admin Email Setting', 'tourfic' ),
								),
								array(
									'id'      => 'admin_email_subject',
									'type'    => 'text',
									'label'   => esc_html__( 'Booking Email Subject', 'tourfic' ),
									'default' => esc_html__( 'New Tour Booking', 'tourfic' ),
								),
								array(
									'id'      => 'email_from_name',
									'type'    => 'text',
									'label'   => esc_html__( 'Email From Name', 'tourfic' ),
									'default' => get_bloginfo( 'name' ),
								),
								array(
									'id'      => 'email_from_email',
									'type'    => 'text',
									'label'   => esc_html__( 'Email From Email', 'tourfic' ),
									'default' => get_bloginfo( 'admin_email' ),
								),
								array(
									'id'      => 'order_email_heading',
									'type'    => 'text',
									'label'   => esc_html__( 'Order Email Heading', 'tourfic' ),
									'default' => esc_html__( 'You booking has been received', 'tourfic' ),
								),
								//type color
								array(
									'id'       => 'email_heading_bg',
									'type'     => 'color',
									'label'    => esc_html__( 'Email header background color', 'tourfic' ),
									'default'  => array(
										'bg_color' => '#0209AF'
									),
									'multiple' => true,
									'inline'   => true,
									'colors'   => array(
										'bg_color' => esc_html__( 'Background Color', 'tourfic' ),
									)
								),
								//email body
								array(
									'id'          => 'admin_booking_email_template',
									'type'        => 'editor',
									'label'       => esc_html__( 'Booking Confrimation Template', 'tourfic' ),
									'default'     => Tourfic\Admin\Emails\TF_Handle_Emails::get_email_template( 'order_confirmation', '', 'admin' ),
									'description' => esc_html__( 'This template will be sent to admin', 'tourfic' )
								),
							),
						),

						//customer email tab
						array(
							'id'     => 'customer-email',
							'title'  => esc_html__( 'Customer Email', 'tourfic' ),
							'icon'   => 'fa fa-envelope',
							'fields' => array(
								//disable customer email
								array(
									'id'      => 'customer_email_disable',
									'type'    => 'switch',
									'label'   => esc_html__( 'Disable Customer Email', 'tourfic' ),
									'default' => 'false',
								),
								array(
									'id'      => 'customer_confirm_email_subject',
									'type'    => 'text',
									'label'   => esc_html__( 'Booking Confirmation Email Subject', 'tourfic' ),
									'default' => esc_html__( 'Your booking has been confirmed', 'tourfic' ),
								),
								//from name
								array(
									'id'      => 'customer_from_name',
									'type'    => 'text',
									'label'   => esc_html__( 'Email From Name', 'tourfic' ),
									'default' => get_bloginfo( 'name' ),
								),
								//from email
								array(
									'id'      => 'customer_from_email',
									'type'    => 'text',
									'label'   => esc_html__( 'Email From Email', 'tourfic' ),
									'default' => get_bloginfo( 'admin_email' ),
								),
								array(
									'id'          => 'customer_confirm_email_template',
									'type'        => 'editor',
									'label'       => esc_html__( 'Booking Confirmation Email', 'tourfic' ),
									'default'     => \Tourfic\Admin\Emails\TF_Handle_Emails::get_email_template( 'order_confirmation', '', 'customer' ),
									'description' => esc_html__( 'This template will be sent to customer after booking is confirmed.', 'tourfic' ),
								),
							),
						),
					),
				),
				//notice field
				array(
					'id'      => 'notice',
					'type'    => 'notice',
					'class'   => 'info',
					'title'   => esc_html__( 'Email Shortcodes', 'tourfic' ),
					'content' => esc_html__( 'You can use the following placeholders in the email body:', 'tourfic' ) . '<br><br><strong>{order_id} </strong> : To display the booking ID.<br>
					<strong>{booking_id} </strong> : To display the booking ID.<br>
					<strong>{booking_date} </strong> : To display the booking date.<br>
					<strong>{fullname} </strong> : To display the customer name.<br>
					<strong>{user_email} </strong> : To display the customer email.<br>
					<strong>{phone} </strong> : To display the customer phone.<br>
					<strong>{address} </strong> : To display the customer address.<br>
					<strong>{city} </strong> : To display the customer city.<br>
					<strong>{country} </strong> : To display the customer country.<br>
					<strong>{zip} </strong> : To display the customer zip.<br>
					<strong>{booking_details} </strong> : To display the booking details.<br>
					<strong>{shipping_address} </strong> : To display the shipping address.<br>
					<strong>{shipping_method} </strong> : To display the shipping method.<br>
					<strong>{shipping_city} </strong> : To display the shipping city.<br>
					<strong>{shipping_country} </strong> : To display the shipping country.<br>
					<strong>{shipping_zip} </strong> : To display the shipping zip.<br>
					<strong>{order_total} </strong> : To display the total price.<br>
					<strong>{order_subtotal} </strong> : To display the subtotal price.<br>
					<strong>{order_date} </strong> : To display the order date.<br>
					<strong>{order_status} </strong> : To display the order status.<br>
					<strong>{payment_method} </strong> : To display the payment method.<br>
					<strong>{booking_url} </strong> : To display the booking url.<br>
					<strong>{site_name} </strong> : To display the site name.<br>
					<strong>{site_url} </strong> : To display the site url.<br>

					'

				,
				),
			),
		),

		/**
		 * Import/Export
		 *
		 * Main menu
		 */
		'import_export' => array(
			'title' => esc_html__( 'Import/Export', 'tourfic' ),
			'icon' => 'fas fa-hdd',
			'fields' => array(
				array(
					'id'      => 'export-import-notice-one',
					'type'    => 'notice',
					'content' => esc_html__( 'Anything confusing?', 'tourfic' ) . ' <a href="'.Helper::tf_utm_generator("https://themefic.com/docs/tourfic/settings/import-export/", array( 'utm_medium' => 'settings_doc_import-export' )).'" target="_blank" class="tf-admin-btn tf-btn-secondary tf-small-btn"><strong>' . esc_html__( 'Read Documentation', 'tourfic' ) . '</strong></a>',
				),
				array(
					'id' => 'backup',
					'type' => 'backup',
				),  

			),
		),
		
	),
) );
