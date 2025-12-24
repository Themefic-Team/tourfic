<?php

namespace Tourfic\Classes;
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Room\Room;

class Migrator {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		add_action( 'init', array( $this, 'tf_permalink_settings_migration' ) );
		add_action( 'init', array( $this, 'tf_template_3_migrate_data' ) );
		add_action( 'init', array( $this, 'tf_migrate_option_data' ) );
		add_action( 'init', array( $this, 'tf_migrate_data' ) );
		if ( Helper::tf_is_woo_active() ) {
			add_action( 'admin_init', array( $this, 'tf_admin_order_data_migration' ) );
		}
		add_action( 'init', array( $this, 'tf_hotel_room_migrate' ) );
		add_action( 'init', array( $this, 'tf_rooms_data_add_in_hotel' ) );
		add_action( 'admin_init', array( $this, 'tf_migrate_tf_enquiry_data' ) );
		add_action( 'init', array( $this, 'tf_template_migrate_data' ) );
		add_action( 'init', array( $this, 'tf_migrate_color_palatte_data' ) );
		add_action( 'init', array( $this, 'tf_tours_availability_migrate' ) );
	}

	function tf_permalink_settings_migration() {

		if ( empty( get_option( 'tf_permalink_settings_migration' ) ) ) {

			$options              = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();
			$hotel_permalink_slug = ! empty( get_option( 'hotel_slug' ) ) ? get_option( 'hotel_slug' ) : '';
			$tour_permalink_slug  = ! empty( get_option( 'tour_slug' ) ) ? get_option( 'tour_slug' ) : '';
			$apt_permalink_slug   = ! empty( get_option( 'apartment_slug' ) ) ? get_option( 'apartment_slug' ) : '';

			if ( ! empty( $hotel_permalink_slug ) ) {
				$options["hotel-permalink-setting"] = $hotel_permalink_slug;
			}

			if ( ! empty( $tour_permalink_slug ) ) {
				$options["tour-permalink-setting"] = $tour_permalink_slug;
			}

			if ( ! empty( $apt_permalink_slug ) ) {
				$options["apartment-permalink-setting"] = $apt_permalink_slug;
			}

			update_option( 'tf_settings', $options );
			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_permalink_settings_migration', 1 );

		}
	}

	/**
	 * Template data migrate repeater to switch-group
	 *
	 * run once
	 */
	function tf_template_migrate_data() {
		if ( empty( get_option( 'tf_template_migrate_data' ) ) || ( ! empty( get_option( 'tf_template_migrate_data' ) ) && get_option( 'tf_template_migrate_data' ) < 1 ) ) {
			$settings = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();
			$single_hotel_layout = $single_hotel_layout1 = $single_hotel_layout2 = [];
			$single_tour_layout = $single_tour_layout1 = $single_tour_layout2 = [];
			$single_apartment_layout1 = $single_apartment_layout2 = [];
			$single_car_layout = [];
			//Hotel
			if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout']) ){
				foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout'] as $key => $section){
					$single_hotel_layout[$key]['label'] = !empty($section['hotel-section']) ? $section['hotel-section'] : '';
					$single_hotel_layout[$key]['slug'] = !empty($section['hotel-section-slug']) ? $section['hotel-section-slug'] : '';
					$single_hotel_layout[$key]['status'] = !empty($section['hotel-section-status']) ? $section['hotel-section-status'] : '0';
				}
			}
			if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout-part-1']) ){
				foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout-part-1'] as $key => $section){
					$single_hotel_layout1[$key]['label'] = !empty($section['hotel-section']) ? $section['hotel-section'] : '';
					$single_hotel_layout1[$key]['slug'] = !empty($section['hotel-section-slug']) ? $section['hotel-section-slug'] : '';
					$single_hotel_layout1[$key]['status'] = !empty($section['hotel-section-status']) ? $section['hotel-section-status'] : '0';
				}
			}
			if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout-part-2']) ){
				foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout-part-2'] as $key => $section){
					$single_hotel_layout2[$key]['label'] = !empty($section['hotel-section']) ? $section['hotel-section'] : '';
					$single_hotel_layout2[$key]['slug'] = !empty($section['hotel-section-slug']) ? $section['hotel-section-slug'] : '';
					$single_hotel_layout2[$key]['status'] = !empty($section['hotel-section-status']) ? $section['hotel-section-status'] : '0';
				}
			}

			//Tour
			if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-tour-layout']) ){
				foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-tour-layout'] as $key => $section){
					$single_tour_layout[$key]['label'] = !empty($section['tour-section']) ? $section['tour-section'] : '';
					$single_tour_layout[$key]['slug'] = !empty($section['tour-section-slug']) ? $section['tour-section-slug'] : '';
					$single_tour_layout[$key]['status'] = !empty($section['tour-section-status']) ? $section['tour-section-status'] : '0';
				}
			}
			if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-tour-layout-part-1']) ){
				foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-tour-layout-part-1'] as $key => $section){
					$single_tour_layout1[$key]['label'] = !empty($section['tour-section']) ? $section['tour-section'] : '';
					$single_tour_layout1[$key]['slug'] = !empty($section['tour-section-slug']) ? $section['tour-section-slug'] : '';
					$single_tour_layout1[$key]['status'] = !empty($section['tour-section-status']) ? $section['tour-section-status'] : '0';
				}
			}
			if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-tour-layout-part-2']) ){
				foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-tour-layout-part-2'] as $key => $section){
					$single_tour_layout2[$key]['label'] = !empty($section['tour-section']) ? $section['tour-section'] : '';
					$single_tour_layout2[$key]['slug'] = !empty($section['tour-section-slug']) ? $section['tour-section-slug'] : '';
					$single_tour_layout2[$key]['status'] = !empty($section['tour-section-status']) ? $section['tour-section-status'] : '0';
				}
			}

			//Apartment
			if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-aprtment-layout-part-1']) ){
				foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-aprtment-layout-part-1'] as $key => $section){
					$single_apartment_layout1[$key]['label'] = !empty($section['aprtment-section']) ? $section['aprtment-section'] : '';
					$single_apartment_layout1[$key]['slug'] = !empty($section['aprtment-section-slug']) ? $section['aprtment-section-slug'] : '';
					$single_apartment_layout1[$key]['status'] = !empty($section['aprtment-section-status']) ? $section['aprtment-section-status'] : '0';
				}
			}
			if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-aprtment-layout-part-2']) ){
				foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-aprtment-layout-part-2'] as $key => $section){
					$single_apartment_layout2[$key]['label'] = !empty($section['aprtment-section']) ? $section['aprtment-section'] : '';
					$single_apartment_layout2[$key]['slug'] = !empty($section['aprtment-section-slug']) ? $section['aprtment-section-slug'] : '';
					$single_apartment_layout2[$key]['status'] = !empty($section['aprtment-section-status']) ? $section['aprtment-section-status'] : '0';
				}
			}

			//Car
			if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-car-layout']) ){
				foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-car-layout'] as $key => $section){
					$single_car_layout[$key]['label'] = !empty($section['car-section']) ? $section['car-section'] : '';
					$single_car_layout[$key]['slug'] = !empty($section['car-section-slug']) ? $section['car-section-slug'] : '';
					$single_car_layout[$key]['status'] = !empty($section['car-section-status']) ? $section['car-section-status'] : '0';
				}
			}

			//Hotel
			$settings['tf-template']['single-hotel-layout'] = $single_hotel_layout;
			$settings['tf-template']['single-hotel-layout-part-1'] = $single_hotel_layout1;
			$settings['tf-template']['single-hotel-layout-part-2'] = $single_hotel_layout2;
			
			//Tour
			$settings['tf-template']['single-tour-layout'] = $single_tour_layout;
			$settings['tf-template']['single-tour-layout-part-1'] = $single_tour_layout1;
			$settings['tf-template']['single-tour-layout-part-2'] = $single_tour_layout2;

			//Apartment
			$settings['tf-template']['single-aprtment-layout-part-1'] = $single_apartment_layout1;
			$settings['tf-template']['single-aprtment-layout-part-2'] = $single_apartment_layout2;
			
			//Car
			$settings['tf-template']['single-car-layout'] = $single_car_layout;

			update_option( 'tf_settings', $settings );
			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_template_migrate_data', 1 );
		}
	}

	/**
	 * Template 3 Default Data Migration v2.10.3
	 *
	 * run once
	 */
	function tf_template_3_migrate_data() {

		// Hotel & Tour
		if ( empty( get_option( 'tf_template_3_migrate_data' ) ) || ( ! empty( get_option( 'tf_template_3_migrate_data' ) ) && get_option( 'tf_template_3_migrate_data' ) < 2 ) ) {

			$options = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();

			$options["tf-template"]["single-hotel-layout"] = array(
				array(
					"label"        => "Description",
					"slug"   => "description",
					"status" => "1"
				),
				array(
					"label"        => "Features",
					"slug"   => "features",
					"status" => "1"
				),
				array(
					"label"        => "Room",
					"slug"   => "rooms",
					"status" => "1"
				),
				array(
					"label"        => "Facilities",
					"slug"   => "facilities",
					"status" => "1"
				),
				array(
					"label"        => "FAQ",
					"slug"   => "faq",
					"status" => "1"
				),
				array(
					"label"        => "Review",
					"slug"   => "review",
					"status" => "1"
				),
				array(
					"label"        => "Terms & Conditions",
					"slug"   => "trams-condition",
					"status" => "1"
				)
			);

			$options["tf-template"]["single-hotel-layout-part-1"] = array(
				array(
					"label"        => "Description",
					"slug"   => "description",
					"status" => "1"
				),
				array(
					"label"        => "Features",
					"slug"   => "features",
					"status" => "1"
				),
				array(
					"label"        => "Room",
					"slug"   => "rooms",
					"status" => "1"
				)
			);
			$options["tf-template"]["single-hotel-layout-part-2"] = array(
				array(
					"label"        => "Facilities",
					"slug"   => "facilities",
					"status" => "1"
				),
				array(
					"label"        => "Review",
					"slug"   => "review",
					"status" => "1"
				),
				array(
					"label"        => "FAQ",
					"slug"   => "faq",
					"status" => "1"
				),
				array(
					"label"        => "Terms & Conditions",
					"slug"   => "trams-condition",
					"status" => "1"
				)
			);

			$options["tf-template"]["single-tour-layout"] = array(
				array(
					"label"        => "Gallery",
					"slug"   => "gallery",
					"status" => "1"
				),
				array(
					"label"        => "Price",
					"slug"   => "price",
					"status" => "1"
				),
				array(
					"label"        => "Description",
					"slug"   => "description",
					"status" => "1"
				),
				array(
					"label"        => "Information",
					"slug"   => "information",
					"status" => "1"
				),
				array(
					"label"        => "Highlights",
					"slug"   => "highlights",
					"status" => "1"
				),
				array(
					"label"        => "Include Exclude",
					"slug"   => "include-exclude",
					"status" => "1"
				),
				array(
					"label"        => "Itinerary",
					"slug"   => "itinerary",
					"status" => "1"
				),
				array(
					"label"        => "Map",
					"slug"   => "map",
					"status" => "1"
				),
				array(
					"label"        => "FAQ",
					"slug"   => "faq",
					"status" => "1"
				),
				array(
					"label"        => "Terms & Conditions",
					"slug"   => "trams-condition",
					"status" => "1"
				),
				array(
					"label"        => "Review",
					"slug"   => "review",
					"status" => "1"
				)
			);


			$options["tf-template"]["single-tour-layout-part-1"] = array(
				array(
					"label"        => "Description",
					"slug"   => "description",
					"status" => "1"
				),
				array(
					"label"        => "Information",
					"slug"   => "information",
					"status" => "1"
				),
				array(
					"label"        => "Highlights",
					"slug"   => "highlights",
					"status" => "1"
				),
				array(
					"label"        => "Include & Exclude",
					"slug"   => "include-exclude",
					"status" => "1"
				),
				array(
					"label"        => "Itinerary",
					"slug"   => "itinerary",
					"status" => "1"
				)
			);

			$options["tf-template"]["single-tour-layout-part-2"] = array(
				array(
					"label"        => "FAQ",
					"slug"   => "faq",
					"status" => "1"
				),
				array(
					"label"        => "Review",
					"slug"   => "review",
					"status" => "1"
				),
				array(
					"label"        => "Terms & Conditions",
					"slug"   => "trams-condition",
					"status" => "1"
				)
			);


			update_option( 'tf_settings', $options );
			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_template_3_migrate_data', 2 );

		}

		// Apartment
		if ( empty( get_option( 'tf_template_2_apartment_migrate_data' ) ) ) {

			$options = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();

			$options["tf-template"]["single-aprtment-layout-part-1"] = array(
				array(
					"label"        => "Description",
					"slug"   => "description",
					"status" => "1"
				),
				array(
					"label"        => "Highlights",
					"slug"   => "features",
					"status" => "1"
				),
				array(
					"label"        => "Apartment Rooms",
					"slug"   => "rooms",
					"status" => "1"
				),
				array(
					"label"        => "Place offer",
					"slug"   => "offer",
					"status" => "1"
				),
				array(
					"label"        => "House Rules",
					"slug"   => "rules",
					"status" => "1"
				),
				array(
					"label"        => "Amenities",
					"slug"   => "facilities",
					"status" => "1"
				)
			);
			$options["tf-template"]["single-aprtment-layout-part-2"] = array(
				array(
					"label"        => "Review",
					"slug"   => "review",
					"status" => "1"
				),
				array(
					"label"        => "FAQ",
					"slug"   => "faq",
					"status" => "1"
				),
				array(
					"label"        => "Terms & Conditions",
					"slug"   => "trams-condition",
					"status" => "1"
				)
			);

			update_option( 'tf_settings', $options );
			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_template_2_apartment_migrate_data', 1 );

		}

		// Car
		if ( empty( get_option( 'tf_template_1_car_migrate_data' ) ) || ( ! empty( get_option( 'tf_template_1_car_migrate_data' ) ) && get_option( 'tf_template_1_car_migrate_data' ) < 2 ) ) {

			$options = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();

			if(empty($options["tf-template"]["single-car"])){
				$options["tf-template"]["single-car"] = 'design-1';
			}
			if(empty($options["tf-template"]["single-car-layout"])){
				$options["tf-template"]["single-car-layout"] = array(
					array(
						"label"        => "Description",
						"slug"   => "description",
						"status" => "1"
					),
					array(
						"label"        => "Car info",
						"slug"   => "car-info",
						"status" => "1"
					),
					array(
						"label"        => "Benefits",
						"slug"   => "benefits",
						"status" => "1"
					),
					array(
						"label"        => "Include/Exclude",
						"slug"   => "inc-exc",
						"status" => "1"
					),
					array(
						"label"        => "Location",
						"slug"   => "location",
						"status" => "1"
					),
					array(
						"label"        => "FAQs",
						"slug"   => "faq",
						"status" => "1"
					),
					array(
						'label'  => 'Terms & Conditions',
						'slug'   => 'tc',
						'status' => "1",
					),
					array(
						'label'  => 'Review',
						'slug'   => 'review',
						'status' => "1",
					)
				);
			}else{
				if(empty(Helper::label_exists_or_not('Terms & Conditions', 'car'))){
					$options["tf-template"]["single-car-layout"][] = array(
						'label'  => 'Terms & Conditions',
						'slug'   => 'tc',
						'status' => "1",
					);
				}
				if(empty(Helper::label_exists_or_not('Review', 'car'))){
					$options["tf-template"]["single-car-layout"][] = array(
						'label'  => 'Review',
						'slug'   => 'review',
						'status' => "1",
					);
				}
			}
			if(empty($options["tf-template"]["car-archive"])){
				$options["tf-template"]["car-archive"] = 'design-1';
			}
			if(empty($options["tf-template"]["car_archive_driver_min_age"])){
				$options["tf-template"]["car_archive_driver_min_age"] = 18;
			}
			if(empty($options["tf-template"]["car_archive_driver_max_age"])){
				$options["tf-template"]["car_archive_driver_max_age"] = 40;
			}
			update_option( 'tf_settings', $options );
			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_template_1_car_migrate_data', 2 );
		}

	}

	/**
	 * Color Migrate
	 *
	 * run once
	 */
	function tf_migrate_color_palatte_data(){
		$migrate_option = get_option('tf_color_data_migrate');
		if ( empty( $migrate_option) || ( ! empty( $migrate_option) && $migrate_option< 1 ) ) {
			$options = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();
			
			if (!empty($options['tf-template']['single-hotel'])) {
    			$options["color-palette-template"] = 'custom'; 
			}

			$prev_primary = !empty($options['tourfic-design1-global-color']) ? unserialize($options['tourfic-design1-global-color']) : '';
			$prev_body_text = !empty($options['tourfic-design1-p-global-color']) ? unserialize($options['tourfic-design1-p-global-color']) : '';
			$prev_template3 = !empty($options['tourfic-template3-bg']) ? unserialize($options['tourfic-template3-bg']) : '';
			$tf_brand_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-custom-brand" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-custom-brand" ) ) : [];
			$tf_text_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-custom-text" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-custom-text" ) ) : [];
			$tf_border_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-custom-border" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-custom-border" ) ) : [];
			$tf_filling_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-custom-filling" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-custom-filling" ) ) : [];

			if(!empty($options['tf-template'])){
				$current_template = !empty($options['tf-template']['single-hotel']) ? $options['tf-template']['single-hotel'] : 'design-1';
		
				if("design-1"==$current_template){

					$tf_brand_data['default'] = !empty($prev_primary['gcolor']) ? $prev_primary['gcolor'] : '#0E3DD8';
					$tf_brand_data['dark'] = '#0A2B99';
					$tf_brand_data['lite'] = '#C9D4F7';
					$tf_text_data['heading'] = '#1C2130';
					$tf_text_data['paragraph'] = !empty($prev_body_text['pgcolor']) ? $prev_body_text['pgcolor'] : '#494D59';
					$tf_text_data['lite'] = '#F3F5FD';
					$tf_border_data['default'] = '#16275F';
					$tf_border_data['lite'] = '#D1D7EE';
					$tf_filling_data['background'] = '#ffffff';
					$tf_filling_data['foreground'] = '#F5F7FF';
					$options["tf-custom-brand"] = $tf_brand_data;
					$options["tf-custom-text"] = $tf_text_data;
					$options["tf-custom-border"] = $tf_border_data;
					$options["tf-custom-filling"] = $tf_filling_data;

				}elseif("design-2"==$current_template){

					$tf_brand_data['default'] = !empty($prev_primary['gcolor']) ? $prev_primary['gcolor'] : '#B58E53';
					$tf_brand_data['dark'] = '#917242';
					$tf_brand_data['lite'] = !empty($prev_template3['template3-highlight']) ? $prev_template3['template3-highlight'] : '#FAEEDC';;
					$tf_text_data['heading'] = '#30281C';
					$tf_text_data['paragraph'] = !empty($prev_body_text['pgcolor']) ? $prev_body_text['pgcolor'] : '#595349';
					$tf_text_data['lite'] = '#FDF9F3';
					$tf_border_data['default'] = '#5F4216';
					$tf_border_data['lite'] = '#EEE2D1';
					$tf_filling_data['background'] = '#ffffff';
					$tf_filling_data['foreground'] = '#FDF9F3';
					$options["tf-custom-brand"] = $tf_brand_data;
					$options["tf-custom-text"] = $tf_text_data;
					$options["tf-custom-border"] = $tf_border_data;
					$options["tf-custom-filling"] = $tf_filling_data;
				
				}elseif("default"==$current_template){

					$tf_brand_data['default'] = !empty($prev_primary['gcolor']) ? $prev_primary['gcolor'] : '#003061';
					$tf_brand_data['dark'] = '#002952';
					$tf_brand_data['lite'] = '#C2E0FF';
					$tf_text_data['heading'] = '#1C2630';
					$tf_text_data['paragraph'] = !empty($prev_body_text['pgcolor']) ? $prev_body_text['pgcolor'] : '#495159';
					$tf_text_data['lite'] = '#F3F8FD';
					$tf_border_data['default'] = '#163A5F';
					$tf_border_data['lite'] = '#D1DFEE';
					$tf_filling_data['background'] = '#ffffff';
					$tf_filling_data['foreground'] = '#F5FAFF';
					$options["tf-custom-brand"] = $tf_brand_data;
					$options["tf-custom-text"] = $tf_text_data;
					$options["tf-custom-border"] = $tf_border_data;
					$options["tf-custom-filling"] = $tf_filling_data;
				}

				update_option( 'tf_settings', $options );
				wp_cache_flush();
				flush_rewrite_rules( true );
				update_option( 'tf_color_data_migrate', 1 );
			}
		}
	}

	/**
	 * Migrate data from v2.0.4 to v2.1.0
	 *
	 * run once
	 */
	function tf_migrate_data() {
		if ( get_option( 'tf_migrate_data_204_210' ) < 1 ) {

			global $wpdb;
			// $wpdb->update( $wpdb->posts, [ 'post_type' => 'tf_hotel' ], [ 'post_type' => 'tourfic' ] );
			// $wpdb->update( $wpdb->term_taxonomy, [ 'taxonomy' => 'hotel_location' ], [ 'taxonomy' => 'destination' ] );
			// $wpdb->update( $wpdb->term_taxonomy, [ 'taxonomy' => 'hotel_feature' ], [ 'taxonomy' => 'tf_filters' ] );


			/** Hotels Migrations */
			$hotels = get_posts( [ 'post_type' => 'tf_hotel', 'numberposts' => - 1, ] );


			foreach ( $hotels as $hotel ) {
				$old_meta = get_post_meta( $hotel->ID );
				if ( ! empty( $old_meta['tf_hotel'] ) ) {
					continue;
				}
				$new_meta = [];
				if ( ! empty( $old_meta['formatted_location'] ) ) {
					$new_meta['address'] = join( ',', $old_meta['formatted_location'] );
				}
				if ( ! empty( $old_meta['tf_gallery_ids'] ) ) {
					$new_meta['gallery'] = join( ',', $old_meta['tf_gallery_ids'] );
				}
				if ( ! empty( $old_meta['additional_information'] ) ) {
					$new_meta['highlights'] = $old_meta['additional_information'];
				}
				if ( ! empty( $old_meta['terms_and_conditions'] ) ) {
					$new_meta['tc'] = join( ' ', $old_meta['terms_and_conditions'] );
				}

				if ( ! empty( $old_meta['tf_room'] ) ) {
					$rooms = unserialize( $old_meta['tf_room'][0] );
					foreach ( $rooms as $room ) {
						$new_meta['room'][] = [
							"enable"      => "1",
							"title"       => $room['name'],
							"adult"       => $room['pax'],
							"description" => $room['short_desc'],
							"pricing-by"  => "1",
							"price"       => $room['sale_price'] ?? $room['price'],
						];
					}
				}

				if ( ! empty( $old_meta['tf_faqs'] ) ) {
					$faqs = unserialize( $old_meta['tf_faqs'][0] );
					foreach ( $faqs as $faq ) {
						$new_meta['faq'][] = [
							'title'       => $faq['name'],
							'description' => $faq['desc'],
						];
					}
				}

				update_post_meta(
					$hotel->ID,
					'tf_hotel',
					$new_meta
				);

			}

			/** Hotels Location Taxonomy Migration */
			$hotel_locations = get_terms( [
				'taxonomy'   => 'hotel_location',
				'hide_empty' => false,
			] );

			foreach ( $hotel_locations as $hotel_location ) {

				$old_locations_meta = '';
				if ( ! is_array( $hotel_location ) ) {
					$old_locations_meta = get_term_meta(
						$hotel_location->term_id,
						'category-image-id',
						true
					);
				}

				$new_meta = [
					"image" => [
						"url"         => wp_get_attachment_url( $old_locations_meta ),
						"id"          => $old_locations_meta,
						"width"       => "1920",
						"height"      => "1080",
						"thumbnail"   => wp_get_attachment_thumb_url( $old_locations_meta ),
						"alt"         => "",
						"title"       => "",
						"description" => ""
					]
				];
				// If the meta field for the term does not exist, it will be added.
				if ( ! is_array( $hotel_location ) ) {
					update_term_meta(
						$hotel_location->term_id,
						"hotel_location",
						$new_meta
					);
				}
			}

			/** Tour Destinations Image Fix */
			$tour_destinations = get_terms( [
				'taxonomy'   => 'tour_destination',
				'hide_empty' => false,
			] );

			foreach ( $tour_destinations as $tour_destination ) {
				if ( ! is_array( $tour_destination ) ) {
					$old_term_metadata = get_term_meta( $tour_destination->term_id, 'tour_destination_meta', true )['tour_destination_meta'] ?? null;

					if ( ! empty( $old_term_metadata ) ) {
						$image_id = attachment_url_to_postid( $old_term_metadata );
						$new_meta = [
							"image" => [
								"url"         => wp_get_attachment_url( $image_id ),
								"id"          => $image_id,
								"width"       => "1920",
								"height"      => "1080",
								"thumbnail"   => wp_get_attachment_thumb_url( $image_id ),
								"alt"         => "",
								"title"       => "",
								"description" => ""
							]
						];
						// If the meta field for the term does not exist, it will be added.
						update_term_meta(
							$tour_destination->term_id,
							"tour_destination",
							$new_meta
						);
					}
				}
			}

			/** Tour Type Fix */
			$tours = get_posts( [ 'post_type' => 'tf_tours', 'numberposts' => - 1, ] );
			foreach ( $tours as $tour ) {
				$old_meta             = get_post_meta( $tour->ID );
				$tour_options         = unserialize( $old_meta['tf_tours_option'][0] );
				$tour_options['type'] = 'continuous';
				update_post_meta(
					$tour->ID,
					'tf_tours_option',
					$tour_options
				);
			}


			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_migrate_data_204_210', 1 );

		}
	}

	/*
	 * TF Options Migrator
	 * @author: Sydur Rahman
	 * */
	function tf_migrate_option_data() {

		if ( empty( get_option( 'tf_migrate_data_204_210_2022' ) ) ) {

			/** Tours Migrations */
			$tours = get_posts( [ 'post_type' => 'tf_tours', 'numberposts' => - 1, ] );
			foreach ( $tours as $tour ) {
				$old_meta = get_post_meta( $tour->ID );
				if ( ! empty( $old_meta['tf_tours_option'] ) ) {
					$tour_options = unserialize( $old_meta['tf_tours_option'][0] );

					if ( isset( $tour_options['hightlights_thumbnail'] ) && is_array( $tour_options['hightlights_thumbnail'] ) ) {
						$tour_options['hightlights_thumbnail'] = $tour_options['hightlights_thumbnail']['url'];
					}
					if ( isset( $tour_options['include-exclude-bg'] ) && is_array( $tour_options['include-exclude-bg'] ) ) {
						$tour_options['include-exclude-bg'] = $tour_options['include-exclude-bg']['url'];
					}
					update_post_meta(
						$tour->ID,
						'tf_tours_opt',
						$tour_options
					);
				}

			}
			/** Tour Destinations Image Fix */
			$tour_destinations = get_terms( [
				'taxonomy'   => 'tour_destination',
				'hide_empty' => false,
			] );


			foreach ( $tour_destinations as $tour_destination ) {
				$old_term_metadata = '';
				if ( ! is_array( $tour_destination ) ) {
					$old_term_metadata = get_term_meta( $tour_destination->term_id, 'tour_destination', true );
				}

				if ( ! empty( $old_term_metadata ) ) {
					if ( isset( $old_term_metadata['image'] ) && is_array( $old_term_metadata['image'] ) ) {
						$old_term_metadata['image'] = $old_term_metadata['image']['url'];
					}

					// If the meta field for the term does not exist, it will be added.
					update_term_meta(
						$tour_destination->term_id,
						"tf_tour_destination",
						$old_term_metadata
					);
				}
			}

			/** Hotel Migrations */
			$hotels = get_posts( [ 'post_type' => 'tf_hotel', 'numberposts' => - 1, ] );

			foreach ( $hotels as $hotel ) {
				$old_meta = get_post_meta( $hotel->ID );
				if ( ! empty( $old_meta['tf_hotel'] ) ) {
					$hotel_options = unserialize( $old_meta['tf_hotel'][0] );


					// $tour_options = serialize( $tour_options );
					update_post_meta(
						$hotel->ID,
						'tf_hotels_opt',
						$hotel_options
					);
				}

			}

			/** Hotel Location Image Fix */
			$hotel_location = get_terms( [
				'taxonomy'   => 'hotel_location',
				'hide_empty' => false,
			] );


			foreach ( $hotel_location as $_hotel_location ) {
				$old_term_metadata = '';
				if ( ! is_array( $tour_destination ) ) {
					$old_term_metadata = get_term_meta( $_hotel_location->term_id, 'hotel_location', true );
				}
				if ( ! empty( $old_term_metadata ) ) {
					if ( isset( $old_term_metadata['image'] ) && is_array( $old_term_metadata['image'] ) ) {
						$old_term_metadata['image'] = $old_term_metadata['image']['url'];
					}

					// If the meta field for the term does not exist, it will be added.
					update_term_meta(
						$_hotel_location->term_id,
						"tf_hotel_location",
						$old_term_metadata
					);
				}
			}

			/** Hotel Feature Image Fix */
			$hotel_feature = get_terms( [
				'taxonomy'   => 'hotel_feature',
				'hide_empty' => false,
			] );


			foreach ( $hotel_feature as $_hotel_feature ) {
				$old_term_metadata = '';
				if ( ! is_array( $tour_destination ) ) {
					$old_term_metadata = get_term_meta( $_hotel_feature->term_id, 'hotel_feature', true );
				}
				if ( ! empty( $old_term_metadata ) ) {
					if ( isset( $old_term_metadata['icon-c'] ) && is_array( $old_term_metadata['icon-c'] ) ) {
						$old_term_metadata['icon-c'] = $old_term_metadata['icon-c']['url'];
					}
					if ( isset( $old_term_metadata['dimention'] ) && is_array( $old_term_metadata['dimention'] ) ) {
						$old_term_metadata['dimention'] = $old_term_metadata['dimention']['width'];
					}

					// If the meta field for the term does not exist, it will be added.
					update_term_meta(
						$_hotel_feature->term_id,
						"tf_hotel_feature",
						$old_term_metadata
					);
				}
			}


			/** settings option migration */
			// company_logo
			$old_setting_option = get_option( 'tourfic_opt' );
			if ( ! is_array( $old_setting_option ) ) {
				$old_setting_option = [];
			}
			if ( isset( $old_setting_option['itinerary-builder-setings']['company_logo'] ) && is_array( $old_setting_option['itinerary-builder-setings']['company_logo'] ) ) {
				$old_setting_option['itinerary-builder-setings']['company_logo'] = $old_setting_option['itinerary-builder-setings']['company_logo']['url'];
			}
			if ( isset( $old_setting_option['itinerary-builder-setings']['expert_logo'] ) && is_array( $old_setting_option['itinerary-builder-setings']['expert_logo'] ) ) {
				$old_setting_option['itinerary-builder-setings']['expert_logo'] = $old_setting_option['itinerary-builder-setings']['expert_logo']['url'];
			}
			update_option( 'tf_settings', $old_setting_option );

			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_migrate_data_204_210_2022', 2 );
		}


		if ( empty( get_option( 'tf_license_data_migrate_data_204_210_2022' ) ) ) {

			/** License Migrate */

			$old_setting_option = get_option( 'tourfic_opt' );
			if ( ! empty( $old_setting_option['license-key'] ) && ! empty( $old_setting_option['license-email'] ) ) {
				$tf_settings['license-key']   = $old_setting_option['license-key'];
				$tf_settings['license-email'] = $old_setting_option['license-email'];
				update_option( 'tf_license_settings', $tf_settings ) || add_option( 'tf_license_settings', $tf_settings );
			} else {
				$tf_setting_option            = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();
				$tf_settings['license-key']   = ! empty( $tf_setting_option['license-key'] ) ? $tf_setting_option['license-key'] : '';
				$tf_settings['license-email'] = ! empty( $tf_setting_option['license-email'] ) ? $tf_setting_option['license-email'] : '';
				update_option( 'tf_license_settings', $tf_settings ) || add_option( 'tf_license_settings', $tf_settings );
			}

			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_license_data_migrate_data_204_210_2022', 2 );
		}


	}

	function tf_admin_order_data_migration() {
		if ( empty( get_option( 'tf_old_order_data_migrate' ) ) ) {

			$tf_old_order_limit = new \WC_Order_Query( array(
				'limit'   => - 1,
				'orderby' => 'date',
				'order'   => 'ASC',
				'return'  => 'ids',
			) );
			$order              = $tf_old_order_limit->get_orders();

			foreach ( $order as $item_id => $item ) {
				$itemmeta = wc_get_order( $item );
				if ( is_a( $itemmeta, 'WC_Order_Refund' ) ) {
					$itemmeta = wc_get_order( $itemmeta->get_parent_id() );
				}
				$tf_ordering_date = !empty( $itemmeta) ? $itemmeta->get_date_created() : '';

				//Order Data Insert
				$billinginfo = [
					'billing_first_name' => ! empty( $itemmeta->get_billing_first_name() ) ? $itemmeta->get_billing_first_name() : '',
					'billing_last_name'  => ! empty( $itemmeta->get_billing_last_name() ) ? $itemmeta->get_billing_last_name() : '',
					'billing_company'    => ! empty( $itemmeta->get_billing_company() ) ? $itemmeta->get_billing_company() : '',
					'billing_address_1'  => ! empty( $itemmeta->get_billing_address_1() ) ? $itemmeta->get_billing_address_1() : '',
					'billing_address_2'  => ! empty( $itemmeta->get_billing_address_2() ) ? $itemmeta->get_billing_address_2() : '',
					'billing_city'       => ! empty( $itemmeta->get_billing_city() ) ? $itemmeta->get_billing_city() : '',
					'billing_state'      => ! empty( $itemmeta->get_billing_state() ) ? $itemmeta->get_billing_state() : '',
					'billing_postcode'   => ! empty( $itemmeta->get_billing_postcode() ) ? $itemmeta->get_billing_postcode() : '',
					'billing_country'    => ! empty( $itemmeta->get_billing_country() ) ? $itemmeta->get_billing_country() : '',
					'billing_email'      => ! empty( $itemmeta->get_billing_email() ) ? $itemmeta->get_billing_email() : '',
					'billing_phone'      => ! empty( $itemmeta->get_billing_phone() ) ? $itemmeta->get_billing_phone() : ''
				];

				$shippinginfo = [
					'shipping_first_name' => ! empty( $itemmeta->get_shipping_first_name() ) ? $itemmeta->get_shipping_first_name() : '',
					'shipping_last_name'  => ! empty( $itemmeta->get_shipping_last_name() ) ? $itemmeta->get_shipping_last_name() : '',
					'shipping_company'    => ! empty( $itemmeta->get_shipping_company() ) ? $itemmeta->get_shipping_company() : '',
					'shipping_address_1'  => ! empty( $itemmeta->get_shipping_address_1() ) ? $itemmeta->get_shipping_address_1() : '',
					'shipping_address_2'  => ! empty( $itemmeta->get_shipping_address_2() ) ? $itemmeta->get_shipping_address_2() : '',
					'shipping_city'       => ! empty( $itemmeta->get_shipping_city() ) ? $itemmeta->get_shipping_city() : '',
					'shipping_state'      => ! empty( $itemmeta->get_shipping_state() ) ? $itemmeta->get_shipping_state() : '',
					'shipping_postcode'   => ! empty( $itemmeta->get_shipping_postcode() ) ? $itemmeta->get_shipping_postcode() : '',
					'shipping_country'    => ! empty( $itemmeta->get_shipping_country() ) ? $itemmeta->get_shipping_country() : '',
					'shipping_phone'      => ! empty( $itemmeta->get_shipping_phone() ) ? $itemmeta->get_shipping_phone() : ''
				];

				foreach ( $itemmeta->get_items() as $item_key => $item_values ) {
					$order_type = wc_get_order_item_meta( $item_key, '_order_type', true );
					if ( "hotel" == $order_type ) {
						$post_id              = wc_get_order_item_meta( $item_key, '_post_id', true );
						$unique_id            = wc_get_order_item_meta( $item_key, '_unique_id', true );
						$room_selected        = wc_get_order_item_meta( $item_key, 'number_room_booked', true );
						$check_in             = wc_get_order_item_meta( $item_key, 'check_in', true );
						$check_out            = wc_get_order_item_meta( $item_key, 'check_out', true );
						$price                = $itemmeta->get_subtotal();
						$due                  = wc_get_order_item_meta( $item_key, 'due', true );
						$room_name            = wc_get_order_item_meta( $item_key, 'room_name', true );
						$adult                = wc_get_order_item_meta( $item_key, 'adult', true );
						$child                = wc_get_order_item_meta( $item_key, 'child', true );
						$children_ages        = wc_get_order_item_meta( $item_key, 'Children Ages', true );
						$airport_service_type = wc_get_order_item_meta( $item_key, 'Airport Service', true );
						$airport_service_fee  = wc_get_order_item_meta( $item_key, 'Airport Service Fee', true );

						$iteminfo = [
							'room'                 => $room_selected,
							'room_unique_id'       => $unique_id,
							'check_in'             => $check_in,
							'check_out'            => $check_out,
							'room_name'            => $room_name,
							'adult'                => $adult,
							'child'                => $child,
							'children_ages'        => $children_ages,
							'airport_service_type' => $airport_service_type,
							'airport_service_fee'  => $airport_service_fee,
							'total_price'          => $price,
							'due_price'            => $due,
						];

						$iteminfo_keys = array_keys( $iteminfo );
						$iteminfo_keys = array_map( 'sanitize_key', $iteminfo_keys );

						$iteminfo_values = array_values( $iteminfo );
						$iteminfo_values = array_map( 'sanitize_text_field', $iteminfo_values );

						$iteminfo = array_combine( $iteminfo_keys, $iteminfo_values );

						global $wpdb;
						$wpdb->query(
							$wpdb->prepare(
								"INSERT INTO {$wpdb->prefix}tf_order_data
						( order_id, post_id, post_type, room_number, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
						VALUES ( %d, %d, %s, %d, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
								array(
									$item,
									sanitize_key( $post_id ),
									$order_type,
									$room_selected,
									$check_in,
									$check_out,
									wp_json_encode( $billinginfo ),
									wp_json_encode( $shippinginfo ),
									wp_json_encode( $iteminfo ),
									$itemmeta->get_customer_id(),
									$itemmeta->get_payment_method(),
									$itemmeta->get_status(),
									$tf_ordering_date->date( 'Y-m-d H:i:s' )
								)
							)
						);
					}
					if ( "tour" == $order_type ) {
						$post_id        = wc_get_order_item_meta( $item_key, '_tour_id', true );
						$tour_date      = wc_get_order_item_meta( $item_key, 'Tour Date', true );
						$tour_time      = wc_get_order_item_meta( $item_key, 'Tour Time', true );
						$price          = $itemmeta->get_subtotal();
						$due            = wc_get_order_item_meta( $item_key, 'Due', true );
						$tour_extra     = wc_get_order_item_meta( $item_key, 'Tour Extra', true );
						$adult          = wc_get_order_item_meta( $item_key, 'Adults', true );
						$child          = wc_get_order_item_meta( $item_key, 'Children', true );
						$infants        = wc_get_order_item_meta( $item_key, 'Infants', true );
						$datatype_check = preg_match( "/-/", $tour_date );
						if ( ! empty( $tour_date ) && ! empty( $datatype_check ) ) {
							list( $tour_in, $tour_out ) = explode( ' - ', $tour_date );
						}
						if ( ! empty( $tour_date ) && empty( $datatype_check ) ) {
							$tour_in  = gmdate( "Y-m-d", strtotime( $tour_date ) );
							$tour_out = "0000-00-00";
						}


						$iteminfo = [
							'tour_date'   => $tour_date,
							'tour_time'   => $tour_time,
							'tour_extra'  => $tour_extra,
							'adult'       => $adult,
							'child'       => $child,
							'infants'     => $infants,
							'total_price' => $price,
							'due_price'   => $due,
						];

						$iteminfo_keys = array_keys( $iteminfo );
						$iteminfo_keys = array_map( 'sanitize_key', $iteminfo_keys );

						$iteminfo_values = array_values( $iteminfo );
						$iteminfo_values = array_map( 'sanitize_text_field', $iteminfo_values );

						$iteminfo = array_combine( $iteminfo_keys, $iteminfo_values );

						global $wpdb;
						$wpdb->query(
							$wpdb->prepare(
								"INSERT INTO {$wpdb->prefix}tf_order_data
						( order_id, post_id, post_type, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
						VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
								array(
									$item,
									sanitize_key( $post_id ),
									$order_type,
									gmdate( "Y-m-d", strtotime( $tour_in ) ),
									gmdate( "Y-m-d", strtotime( $tour_out ) ),
									wp_json_encode( $billinginfo ),
									wp_json_encode( $shippinginfo ),
									wp_json_encode( $iteminfo ),
									$itemmeta->get_customer_id(),
									$itemmeta->get_payment_method(),
									$itemmeta->get_status(),
									$tf_ordering_date->date( 'Y-m-d H:i:s' )
								)
							)
						);
					}
				}

			}
			wp_cache_flush();
			flush_rewrite_rules( true );
			update_option( 'tf_old_order_data_migrate', 1 );
		}
	}


	/*
	 * Hotel room migrate
	 */
	public function tf_hotel_room_migrate() {
		if ( empty( get_option( 'tf_room_data_migration' ) ) ) {
			$this->regenerate_room_meta();
			update_option( 'tf_room_data_migration', 1 );
		}
	}

	function regenerate_room_meta() {

		$args  = array(
			'post_type'      => 'tf_hotel',
			'post_status'    => 'publish',
			'posts_per_page' => - 1
		);
		$posts = new \WP_Query( $args );
		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) {
				$posts->the_post();
				$post_id = get_the_ID();
				$meta    = get_post_meta( $post_id, 'tf_hotels_opt', true );

				$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
				if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
					$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $rooms );
					$rooms                = unserialize( $tf_hotel_rooms_value );
				}

				$current_user_id = get_current_user_id();
				foreach ( $rooms as $room ) {
					$post_data        = array(
						'post_type'    => 'tf_room',
						'post_title'   => ! empty( $room['title'] ) ? $room['title'] : 'No Title',
						'post_status'  => 'publish',
						'post_author'  => $current_user_id,
						'post_content' => ! empty( $room['description'] ) ? $room['description'] : '',
					);
					$room['tf_hotel'] = $post_id;

					$room_post_id = wp_insert_post( $post_data );
					update_post_meta( $room_post_id, 'tf_room_opt', $room );

					//insert thumbnail if has 'room_preview_img' key which return url
					if ( ! empty( $room['room_preview_img'] ) ) {
						$attachment_id = attachment_url_to_postid( $room['room_preview_img'] );
						if ( ! empty( $attachment_id ) ) {
							set_post_thumbnail( $room_post_id, $attachment_id );
						}
					}
				}

			}
		}
	}

	/* 
	 * Rooms data add in hotel tf_rooms field
	 * @author Foysal
	 */
	function tf_rooms_data_add_in_hotel(){
		if ( empty( get_option( 'tf_room_data_add_in_hotel' ) ) ) {
			$args  = array(
				'post_type'      => 'tf_hotel',
				'post_status'    => 'publish',
				'posts_per_page' => - 1
			);
			$posts = new \WP_Query( $args );
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) {
					$posts->the_post();
					$post_id = get_the_ID();
					$meta    = get_post_meta( $post_id, 'tf_hotels_opt', true );
	
					$rooms = Room::get_hotel_rooms( $post_id );
					if(!empty($rooms)){
						$room_ids = array_column($rooms, 'ID');
						$meta['tf_rooms'] = $room_ids;

						update_post_meta($post_id, 'tf_hotels_opt', $meta);
					}
				}
			}

			update_option( 'tf_room_data_add_in_hotel', 1 );
		}
	}

	public function tf_search_keys_migrate() {
		$tf_hotel_search_keys_migration     = ! empty( get_option( 'tf_hotel_search_keys_migration' ) ) ? get_option( 'tf_hotel_search_keys_migration' ) : 0;
		$tf_room_search_keys_migration     = ! empty( get_option( 'tf_room_search_keys_migration' ) ) ? get_option( 'tf_room_search_keys_migration' ) : 0;
		$tf_tour_search_keys_migration      = ! empty( get_option( 'tf_tour_search_keys_migration' ) ) ? get_option( 'tf_tour_search_keys_migration' ) : 0;
		$tf_apartment_search_keys_migration = ! empty( get_option( 'tf_apartment_search_keys_migration' ) ) ? get_option( 'tf_apartment_search_keys_migration' ) : 0;
		if ( $tf_hotel_search_keys_migration < 1 ) {
			$this->regenerate_search_keys( 'tf_hotel' );
			update_option( 'tf_hotel_search_keys_migration', $tf_hotel_search_keys_migration + 1 );
		}
		if ( $tf_room_search_keys_migration < 1 ) {
			$this->regenerate_search_keys( 'tf_room' );
			update_option( 'tf_room_search_keys_migration', $tf_room_search_keys_migration + 1 );
		}
//		if ( $tf_tour_search_keys_migration < 1 ) {
//			$this->regenerate_search_keys( 'tf_tours' );
//			update_option( 'tf_tour_search_keys_migration', $tf_tour_search_keys_migration + 1 );
//		}
//		if ( $tf_apartment_search_keys_migration < 1 ) {
//			$this->regenerate_search_keys( 'tf_apartment' );
//			update_option( 'tf_apartment_search_keys_migration', $tf_apartment_search_keys_migration + 1 );
//		}
	}

	function regenerate_search_keys( $type ) {
		if ( "tf_hotel" == $type ) {

			$searchable_keys = [
				'featured',
				'booking-by',
				'map'
			];
			$args            = array(
				'post_type'      => 'tf_hotel',
				'post_status'    => 'publish',
				'posts_per_page' => - 1
			);
			$posts           = new \WP_Query( $args );
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) {
					$posts->the_post();
					$post_id = get_the_ID();
					$meta    = get_post_meta( $post_id, 'tf_hotels_opt', true );
					if ( ! empty( $searchable_keys ) ) {
						foreach ( $searchable_keys as $search ) {
							$fields_values = ! empty( $meta[ $search ] ) ? $meta[ $search ] : "";
							update_post_meta( $post_id, $search, $fields_values );
						}
					}
				}
			}
			wp_reset_query();

		}
		if ( "tf_room" == $type ) {

			$searchable_keys = [
				'unique_id',
				'order_id',
				'adult',
				'child',
				'pricing-by',
				'price',
				'adult_price',
				'child_price',
				'num-room',
				'reduce_num_room',
				'avail_date'
			];
			$args            = array(
				'post_type'      => 'tf_room',
				'post_status'    => 'publish',
				'posts_per_page' => - 1
			);
			$posts           = new \WP_Query( $args );
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) {
					$posts->the_post();
					$post_id = get_the_ID();
					$meta    = get_post_meta( $post_id, 'tf_hotels_opt', true );
					if ( ! empty( $searchable_keys ) ) {
						foreach ( $searchable_keys as $search ) {
							$fields_values = ! empty( $meta[ $search ] ) ? $meta[ $search ] : "";
							update_post_meta( $post_id, $search, $fields_values );
						}
					}
				}
			}
			wp_reset_query();

		}
		if ( "tf_tours" == $type ) {
			$searchable_keys = [
				'tour_as_featured',
				'location',
				'pricing',
				'adult_price',
				'child_price',
				'infant_price',
				'group_price',
				'type',
				'cont_min_people',
				'cont_max_people',
				'cont_max_capacity',
				'disable_range',
				'custom_avail',
				'cont_custom_date',
				'disable_specific',
				'fixed_availability'
			];
			$args            = array(
				'post_type'      => 'tf_tours',
				'post_status'    => 'publish',
				'posts_per_page' => - 1
			);
			$posts           = new \WP_Query( $args );
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) {
					$posts->the_post();
					$post_id = get_the_ID();
					$meta    = get_post_meta( $post_id, 'tf_tours_opt', true );
					if ( ! empty( $searchable_keys ) ) {
						foreach ( $searchable_keys as $search ) {
							$fields_values = ! empty( $meta[ $search ] ) ? $meta[ $search ] : "";
							update_post_meta( $post_id, 'tf_search_' . $search, $fields_values );
						}
					}
				}
			}
			wp_reset_query();
		}
		if ( "tf_apartment" == $type ) {
			$searchable_keys = [
				'apartment_as_featured',
				'map',
				'pricing_type',
				'price_per_night',
				'adult_price',
				'child_price',
				'infant_price',
				'min_stay',
				'max_adults',
				'max_children',
				'max_infants',
				'apt_availability'
			];
			$args            = array(
				'post_type'      => 'tf_apartment',
				'post_status'    => 'publish',
				'posts_per_page' => - 1
			);
			$posts           = new \WP_Query( $args );
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) {
					$posts->the_post();
					$post_id = get_the_ID();
					$meta    = get_post_meta( $post_id, 'tf_apartment_opt', true );
					if ( ! empty( $searchable_keys ) ) {
						foreach ( $searchable_keys as $search ) {
							$fields_values = ! empty( $meta[ $search ] ) ? $meta[ $search ] : "";
							update_post_meta( $post_id, 'tf_search_' . $search, $fields_values );
						}
					}
				}
			}
			wp_reset_query();
		}
	}

	/**
	 * Migrate enquiry data
	 */

	public function tf_migrate_tf_enquiry_data() {
		if ( empty( get_option( 'tf_enquiry_data_migration' ) ) ) {
			$this->add_enquiry_new_columns();
			update_option( 'tf_enquiry_data_migration', 1 );
		}
	}

	private function add_enquiry_new_columns() {
		global $wpdb;
		$enquiry_table = $wpdb->prefix . 'tf_enquiry_data';

		$columns = $wpdb->get_results("SHOW COLUMNS FROM $enquiry_table", ARRAY_A);
    	$existing_columns = wp_list_pluck($columns, 'Field');

		if (!in_array('enquiry_status', $existing_columns)) {
			$wpdb->query("ALTER TABLE $enquiry_table ADD COLUMN `enquiry_status` VARCHAR(255) NOT NULL DEFAULT 'read' AFTER `author_roles`");
		}

		if (!in_array('server_data', $existing_columns)) {
			$wpdb->query("ALTER TABLE $enquiry_table ADD COLUMN `server_data` VARCHAR(255) NOT NULL DEFAULT '' AFTER `enquiry_status`");
		}
		
		if (!in_array('reply_data', $existing_columns)) {
			$wpdb->query("ALTER TABLE $enquiry_table ADD COLUMN `reply_data` LONGTEXT NOT NULL DEFAULT '' AFTER `server_data`");
		}
	}

	/**
	 * Migrate Tour Availability data
	*/
	public function tf_tours_availability_migrate(){
		if ( empty( get_option( 'tf_tour_availability_migration' ) ) ) {
			$args = array(
				'post_type'      => 'tf_tours',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
			);
			$tour_loop = new \WP_Query( $args );
			$date_format         = "Y/m/d";
			while ( $tour_loop->have_posts() ) : $tour_loop->the_post();
				$post_id = get_the_ID();
				$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
				$tour_type = ! empty( $meta['type'] ) ? $meta['type'] : '';
				$pricing_rule = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
				$discount_type = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : 'none';
				$tour_availability_data = [];


				if ( $tour_type == 'fixed' ) {
					$tf_start_date = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
					$tf_end_date   = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
				
					$min_seat     = ! empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
					$max_seat     = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
					$max_capacity = ! empty( $meta['fixed_availability']['max_capacity'] ) ? $meta['fixed_availability']['max_capacity'] : '';
				
					$meta['min_person'] = $min_seat;
					$meta['max_person'] = $max_seat;
				
					$tf_tour_adult_price  = $pricing_rule == 'person' && ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : '';
					$tf_tour_child_price  = $pricing_rule == 'person' && ! empty( $meta['child_price'] ) ? $meta['child_price'] : '';
					$tf_tour_infant_price = $pricing_rule == 'person' && ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : '';
					$tf_tour_group_price  = $pricing_rule == 'group' && ! empty( $meta['group_price'] ) ? $meta['group_price'] : '';
				
					$tf_start_date = strtotime( $this->tf_convert_date_format( $tf_start_date, $date_format ) );
					$tf_end_date   = strtotime( $this->tf_convert_date_format( $tf_end_date, $date_format ) );
				
					$repeat_years = ! empty( $meta['fixed_availability']['tf-repeat-months-switch'] ) ? $meta['fixed_availability']['tf-repeat-months-switch'] : '';

					$repeat_months = ! empty( $meta['fixed_availability']['tf-repeat-months-checkbox'] )
						? $meta['fixed_availability']['tf-repeat-months-checkbox']
						: [];
					
					if ( !empty($tf_start_date) && ! in_array( gmdate('m', $tf_start_date), $repeat_months, true ) ) {
						$repeat_months[] = gmdate('m', $tf_start_date);
					}
				
					if ( ! empty( $tf_start_date ) && ! empty( $tf_end_date ) ) {

						$start_year = gmdate( 'Y', $tf_start_date );
						if(!empty($repeat_years)){
							$tf_tour_repeat_years = [ $start_year, $start_year + 1 ];
						}else{
							$tf_tour_repeat_years = [ $start_year ];
						}
				
						// Extract original day values
						$original_checkin_day  = gmdate( 'd', $tf_start_date );
						$original_checkout_day = gmdate( 'd', $tf_end_date );
				
						foreach ( $tf_tour_repeat_years as $year ) {
							foreach ( $repeat_months as $month ) {
								$month = str_pad( $month, 2, '0', STR_PAD_LEFT );
				
								$new_check_in_str  = "$year-$month-$original_checkin_day";
								$new_check_out_str = "$year-$month-$original_checkout_day";
				
								$new_check_in  = strtotime( $new_check_in_str );
								$new_check_out = strtotime( $new_check_out_str );
				
								// Skip invalid dates
								if ( ! $new_check_in || ! $new_check_out ) {
									continue;
								}
				
								$tf_checkin_date  = gmdate( 'Y/m/d', $new_check_in );
								$tf_checkout_date = gmdate( 'Y/m/d', $new_check_out );
				
								$tf_tour_date = $tf_checkin_date . ' - ' . $tf_checkout_date;
				
								$tf_tour_data = [
									'check_in'     => $tf_checkin_date,
									'check_out'    => $tf_checkout_date,
									'pricing_type' => $pricing_rule,
									'price'        => $tf_tour_group_price,
									'adult_price'  => $tf_tour_adult_price,
									'child_price'  => $tf_tour_child_price,
									'infant_price' => $tf_tour_infant_price,
									'min_person'   => $min_seat,
									'max_person'   => $max_seat,
									'max_capacity' => $max_capacity,
									'allowed_time' => '',
									'status'       => 'available',
								];
				
								$tour_availability_data[ $tf_tour_date ] = $tf_tour_data;
							}
						}
					}
				}

				if($tour_type=='continuous'){
					$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
					if ( $custom_avail == true ) {

						$cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
						if ( ! empty( $cont_custom_date ) && gettype( $cont_custom_date ) == "string" ) {
							$cont_custom_date_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
								return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
							}, $cont_custom_date );
							$cont_custom_date          = unserialize( $cont_custom_date_unserial );
						}

						if(!empty($cont_custom_date)){
							$custom_pricing_by = ! empty( $meta['custom_pricing_by'] ) ? $meta['custom_pricing_by'] : 'person';
							foreach($cont_custom_date as $date){
								$tf_start_date = ! empty( $date['date']['from'] ) ? $date['date']['from'] : '';
								$tf_end_date = ! empty( $date['date']['to'] ) ? $date['date']['to'] : $tf_start_date;

								$min_seat = ! empty( $date['min_people'] ) ? $date['min_people'] : '';
								$max_seat = ! empty( $date['max_people'] ) ? $date['max_people'] : '';
								$total_capacity = ! empty( $date['max_capacity'] ) ? $date['max_capacity'] : '';

								$adult_price = ! empty( $date['adult_price'] ) ? $date['adult_price'] : '';
								$child_price = ! empty( $date['child_price'] ) ? $date['child_price'] : '';
								$infant_price = ! empty( $date['infant_price'] ) ? $date['infant_price'] : '';
								$group_price = ! empty( $date['group_price'] ) ? $date['group_price'] : '';

								// Convert to DateTime objects
								$start = \DateTime::createFromFormat('Y/m/d', $tf_start_date);
								$end   = \DateTime::createFromFormat('Y/m/d', $tf_end_date);

								if ( $start && $end ) {
									$end = $end->modify('+1 day');
									$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
									foreach ( $period as $pdate ) {
										$current_date = $pdate->format('Y/m/d');
					
										$tf_tour_date = trim($current_date) . ' - ' . trim($current_date);

										$allowed_time = ! empty( $date['allowed_time'] ) ? $date['allowed_time'] : '';

										$tf_tour_allowed_time = [];
										if(!empty($allowed_time)){
											$times = [];
											$max_capacity = [];
											foreach($allowed_time as $time){
												$times[] = $time['time'];
												$max_capacity[] = $time['max_capacity'];
											}
											$tf_tour_allowed_time = [
												'time' => $times,
												'cont_max_capacity' => $max_capacity
											];
										}
										
										$tf_tour_data = [
											'check_in'     => $current_date,
											'check_out'    => $current_date,
											'pricing_type' => $custom_pricing_by,
											'price'        => $group_price,
											'adult_price'  => $adult_price,
											'child_price'  => $child_price,
											'infant_price' => $infant_price,
											'min_person'   => $min_seat,
											'max_person'   => $max_seat,
											'max_capacity' => $total_capacity,
											'allowed_time' => !empty($tf_tour_allowed_time) ? $tf_tour_allowed_time : '',
											'status'       => 'available'
										];

										$tour_availability_data[$tf_tour_date] = $tf_tour_data;
									}
								}

							}
						}
					}else{
						$cont_min_people = ! empty( $meta['cont_min_people'] ) ? $meta['cont_min_people'] : '';
						$cont_max_people = ! empty( $meta['cont_max_people'] ) ? $meta['cont_max_people'] : '';
						$cont_max_capacity = ! empty( $meta['cont_max_capacity'] ) ? $meta['cont_max_capacity'] : '';

						$meta['min_person'] = $cont_min_people;
						$meta['max_person'] = $cont_max_people;

						$allowed_time = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';
						$tf_tour_allowed_time = [];
						if(!empty($allowed_time)){
							$times = [];
							$max_capacity = [];
							foreach($allowed_time as $time){
								$times[] = $time['time'];
								$max_capacity[] = $time['cont_max_capacity'];
							}
							$tf_tour_allowed_time = [
								'time' => $times,
								'cont_max_capacity' => $max_capacity
							];
						}

						$disable_range = ! empty( $meta['disable_range'] ) ? $meta['disable_range'] : '';

						$disable_specific = ! empty( $meta['disable_specific'] ) ? $meta['disable_specific'] : '';
						$disable_specific = !empty($disable_specific) ? explode(",",$disable_specific) : [];

						$disabled_day = ! empty( $meta['disabled_day'] ) ? $meta['disabled_day'] : '';

						if( !empty($disable_range) || !empty($disable_specific) || !empty($disabled_day) ){
							//add next 1 years availability
							for ( $i = strtotime( gmdate( 'Y-m-d' ) ); $i <= strtotime( '+1 year', strtotime( gmdate( 'Y-m-d' ) ) ); $i = strtotime( '+1 day', $i ) ) {
								$tf_tour_inc_date = gmdate( 'Y/m/d', $i );

								$tf_tour_adult_price  = $pricing_rule == 'person' && ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : '';
								$tf_tour_child_price  = $pricing_rule == 'person' && ! empty( $meta['child_price'] ) ? $meta['child_price'] : '';
								$tf_tour_infant_price  = $pricing_rule == 'person' && ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : '';
								$tf_tour_group_price  = $pricing_rule == 'group' && ! empty( $meta['group_price'] ) ? $meta['group_price'] : '';

								$tf_tour_date = $tf_tour_inc_date . ' - ' . $tf_tour_inc_date;
								$tf_tour_data = [
									'check_in'    => $tf_tour_inc_date,
									'check_out'   => $tf_tour_inc_date,
									'pricing_type' => $pricing_rule,
									'price'        => $tf_tour_group_price,
									'adult_price'  => $tf_tour_adult_price,
									'child_price'  => $tf_tour_child_price,
									'infant_price' => $tf_tour_infant_price,
									'min_person'   => $cont_min_people,
									'max_person'   => $cont_max_people,
									'max_capacity' => $cont_max_capacity,
									'allowed_time' => !empty($tf_tour_allowed_time) ? $tf_tour_allowed_time : '',
									'status'       => 'available'
								];
								$tour_availability_data[ $tf_tour_date ] = $tf_tour_data;
							}
						}

						if ( ! empty( $disable_range ) ) {
							foreach ( $disable_range as $disable ) {
								if ( ! empty( $disable['date']['from'] ) ) {
									$tf_checkin_date  = $disable['date']['from'];
									$tf_checkout_date = ! empty( $disable['date']['to'] ) ? $disable['date']['to'] : $disable['date']['from'];
						
									// Convert to DateTime objects
									$start = \DateTime::createFromFormat('d/m/Y', $tf_checkin_date);
									$end   = \DateTime::createFromFormat('d/m/Y', $tf_checkout_date);
						
									if ( $start && $end ) {
						
										$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
						
										foreach ( $period as $date ) {
											$current_date = $date->format('d/m/Y');
						
											$tf_tour_date = trim($current_date) . ' - ' . trim($current_date);
											$tf_tour_data = [
												'check_in'     => $current_date,
												'check_out'    => $current_date,
												'pricing_type' => $pricing_rule,
												'price'        => '',
												'adult_price'  => '',
												'child_price'  => '',
												'infant_price' => '',
												'min_person'   => '',
												'max_person'   => '',
												'max_capacity' => '',
												'allowed_time' => '',
												'status'       => 'unavailable'
											];
						
											if ( ! array_key_exists( $tf_tour_date, $tour_availability_data ) ) {
												$tour_availability_data[ $tf_tour_date ] = $tf_tour_data;
											}
										}
									}
								}
							}
						}
						
						if(!empty($disable_specific)){
							foreach($disable_specific as $disable){
								$tf_tour_date = trim($disable) . ' - ' . trim($disable);
								$tf_tour_data = [
									'check_in'     => $disable,
									'check_out'    => $disable,
									'pricing_type' => $pricing_rule,
									'price'        => '',
									'adult_price'  => '',
									'child_price'  => '',
									'infant_price' => '',
									'min_person'   => '',
									'max_person'   => '',
									'max_capacity' => '',
									'allowed_time' => '',
									'status'       => 'unavailable'
								];
								if ( ! array_key_exists( $tf_tour_date, $tour_availability_data ) ) {
									$tour_availability_data[ $tf_tour_date ] = $tf_tour_data;
								}
							}
						}

						if(!empty($disabled_day)){
							for ( $i = 0; $i <= 350; $i ++ ) {
								$tf_room_date                     = gmdate( 'Y/m/d', strtotime( "+$i day" ) );
								$day_number = gmdate( 'w', strtotime( $tf_room_date ) );
								
								if (in_array($day_number, $disabled_day)){
									$tf_tour_date = trim($tf_room_date) . ' - ' . trim($tf_room_date);
									$tf_tour_data = [
										'check_in'    => $tf_room_date,
										'check_out'   => $tf_room_date,
										'pricing_type' => $pricing_rule,
										'price'        => '',
										'adult_price'  => '',
										'child_price'  => '',
										'infant_price' => '',
										'min_person'   => '',
										'max_person'   => '',
										'max_capacity' => '',
										'allowed_time' => '',
										'status'       => 'unavailable'
									];
									$tour_availability_data[ $tf_tour_date ] = $tf_tour_data;
								}
							}
						}
					}
				}

				// If not an array, initialize it
				if ( ! is_array( $meta ) ) {
					$meta = [];
				}
				$meta['tour_availability'] = wp_json_encode( $tour_availability_data );
				if($discount_type!='none'){
					$meta['allow_discount'] = '1';
				}

				update_post_meta($post_id, 'tf_tours_opt', $meta);

			endwhile;
			wp_reset_postdata();

			update_option( 'tf_tour_availability_migration', 1 );
		}
	}

	function tf_convert_date_format( $date, $currentFormat ) {
		$dateTime = \DateTime::createFromFormat( $currentFormat, $date );
		if ( $dateTime === false ) {
			return false;
		}
		return $dateTime->format( 'Y/m/d' );
	}
}