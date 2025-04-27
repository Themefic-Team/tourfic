<?php

namespace Tourfic\Classes;
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Apartment\Apartment;
use Tourfic\Classes\Apartment\Pricing as ApartmentPricing;
use Tourfic\Classes\Helper;
use Tourfic\Classes\Hotel\Hotel;
use Tourfic\Classes\Hotel\Pricing as HotelPricing;
use Tourfic\Classes\Tour\Pricing as TourPricing;
use Tourfic\Classes\Tour\Tour;
use Tourfic\Classes\Room\Room;

class Enqueue {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		add_filter( 'wp_enqueue_scripts', array( $this, 'tf_dequeue_scripts' ), 9999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'tf_enqueue_scripts' ) );
		// add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'tf_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'tf_enqueue_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'tf_dequeue_theplus_script_on_settings_page' ), 9999 );

		add_action( 'admin_enqueue_scripts', array( $this, 'tf_options_admin_enqueue_scripts' ), 9 );
		add_action( 'wp_enqueue_scripts', array( $this, 'tf_options_wp_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'tf_global_custom_css' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'tf_required_taxonomies' ) );
	}

	/**
	 * Dequeue frontend scripts to avoid conflict
	 * @since 1.0
	 */
	function tf_dequeue_scripts() {

		// Flatpickr
		wp_deregister_script( 'flatpickr' );
		wp_dequeue_script( 'flatpickr' );
		wp_deregister_style( 'flatpickr' );
		wp_dequeue_style( 'flatpickr' );
		// Fancybox
		wp_deregister_script( 'fancyBox' );
		wp_dequeue_script( 'fancyBox' );
		// Slick
		wp_deregister_style( 'slick' );
		wp_dequeue_style( 'slick' );
		wp_deregister_script( 'slick' );
		wp_dequeue_script( 'slick' );
	}

	/**
	 * Enqueue Frontend scripts
	 * @since 1.0
	 */
	function tf_enqueue_scripts() {

		$flatpickr_cdn    = ! empty( Helper::tfopt( 'ftpr_cdn' ) ) ? Helper::tfopt( 'ftpr_cdn' ) : false;
		$flatpickr_locale = ! empty( get_locale() ) ? get_locale() : 'en_US';
		$allowed_locale   = array( 'ar', 'bn_BD', 'de_DE', 'es_ES', 'fr_FR', 'hi_IN', 'it_IT', 'nl_NL', 'ru_RU', 'zh_CN' );
		$fancy_cdn        = ! empty( Helper::tfopt( 'fnybx_cdn' ) ) ? Helper::tfopt( 'fnybx_cdn' ) : false;
		$slick_cdn        = ! empty( Helper::tfopt( 'slick_cdn' ) ) ? Helper::tfopt( 'slick_cdn' ) : false;
		$fa_cdn           = ! empty( Helper::tfopt( 'fa_cdn' ) ) ? Helper::tfopt( 'fa_cdn' ) : false;
		$min_css          = ! empty( Helper::tfopt( 'css_min' ) ) ? '.min' : '';
		$min_js           = ! empty( Helper::tfopt( 'js_min' ) ) ? '.min' : '';		
		$tf_disable_services = ! empty( Helper::tfopt( 'disable-services' ) ) ? Helper::tfopt( 'disable-services' ) : [];
		$tf_services = [
			'apartment' => 'tf_apartment',
			'carrentals' => 'tf_carrental',
			'tour' => 'tf_tours',
			'hotel' => 'tf_hotel',
		];

		$tax_post_type = '';
        if (is_tax()) {
            $taxonomy = get_queried_object();

            if ($taxonomy && !is_wp_error($taxonomy)) {
                $taxonomy_name = $taxonomy->taxonomy;

                // Retrieve the taxonomy object
                $taxonomy_obj = get_taxonomy($taxonomy_name);

                // Get the post types associated with the taxonomy
                if (!empty($taxonomy_obj->object_type)) {
                    $tax_post_type = $taxonomy_obj->object_type[0];
                }
            }
        }

		/*
		 * Ubuntu font load for hotel, tour, apartment template 3
		 */
		global $post;
		$post_id   = ! empty( $post->ID ) ? $post->ID : '';
		$post_type = ! empty( $post->post_type ) ? $post->post_type : '';
		if(function_exists( 'is_tf_pro' ) && is_tf_pro()){
			if ( $post_type == 'tf_hotel' && ! empty( $post_id ) || is_post_type_archive( 'tf_hotel' ) ||
			     $post_type == 'tf_tours' && ! empty( $post_id ) || is_post_type_archive( 'tf_tours' ) ||
			     $post_type == 'tf_apartment' && ! empty( $post_id ) || is_post_type_archive( 'tf_apartment' )) {
				$hotel_archive  = Hotel::template('archive');
				$hotel_single  = Hotel::template('single');
				$tour_archive  = Tour::template('archive');
				$tour_single  = Tour::template('single');
				$apartment_archive  = Apartment::template('archive');
				$apartment_single  = Apartment::template('single');

				if($hotel_archive == 'design-3' || $hotel_single == 'design-3' ||
				   $tour_archive == 'design-3' || $tour_single == 'design-3' ||
				   $apartment_archive == 'design-2' || $apartment_single == 'design-2') {
					wp_enqueue_style( 'tf-template-4-font', '//fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap', null, TF_VERSION );
				}
			}
		}

		//Updated CSS
		wp_enqueue_style( 'tf-app-style', TF_ASSETS_URL . 'app/css/tourfic-style' . $min_css . '.css', null, TF_VERSION );
	
		foreach ($tf_services as $key => $post_type) {
			if (!in_array($key, $tf_disable_services) && (is_singular($post_type) || is_post_type_archive($post_type) || $post_type == $tax_post_type)) {
				wp_enqueue_style("tf-app-{$key}", TF_ASSETS_URL . "app/css/tourfic-{$key}" . $min_css . ".css", null, TF_VERSION);
			}
		}

		if ( get_post_type() == 'tf_tours' ) {

			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
				wp_enqueue_script( 'Chart', '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js', array( 'jquery' ), '2.6.0', true );
				$meta        = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
				$itineraries = ! empty( $meta['itinerary'] ) ? $meta['itinerary'] : null;
				if ( ! empty( $itineraries ) && gettype( $itineraries ) == "string" ) {
					$tf_hotel_itineraries_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $itineraries );
					$itineraries                = unserialize( $tf_hotel_itineraries_value );
				}
				$itinerarayday   = [];
				$itineraraymeter = [];
				if ( $itineraries ) {
					foreach ( $itineraries as $itinerary ) {
						$itinerarayday[]   = ! empty( $itinerary['time'] ) ? $itinerary['time'] : '';
						$itineraraymeter[] = ! empty( $itinerary['altitude'] ) ? intval( $itinerary['altitude'] ) : '';
					}
				}
				$showxaxis           = ! empty( Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) )['itinerary-x-axis'] ) ? Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) )['itinerary-x-axis'] : false;
				$showyaxis           = ! empty( Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) )['itinerary-y-axis'] ) ? Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) )['itinerary-y-axis'] : false;
				$showlinegraph       = ! empty( Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) )['itinerary-line-graph'] ) ? Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) )['itinerary-line-graph'] : false;
				$showitinerarychart  = ! empty( Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) )['itinerary-chart'] ) ? Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) )['itinerary-chart'] : false;
				$showitinerarystatus = ! empty( Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) )['itinerary-status'] ) ? Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) )['itinerary-status'] : false;
				$elevvationmode      = ! empty( Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) )['elevtion_type'] ) && Helper::tf_data_types( Helper::tfopt( 'itinerary-builder-setings' ) )['elevtion_type'] == "Feet" ? "Feet" : "Meter";
			}
		}

		/**
		 * Flatpickr
		 * v4.6.13
		 */
		if ( $flatpickr_cdn ) {
			wp_enqueue_style( 'tf-flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css', array(), TF_VERSION );
			wp_enqueue_script( 'tf-flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js', array( 'jquery' ), TF_VERSION, true );
			if ( in_array( $flatpickr_locale, $allowed_locale ) ) {
				wp_enqueue_script( 'flatpickr-locale', TF_ASSETS_URL . 'app/libs/flatpickr/l10n/' . $flatpickr_locale . '.min.js', array( 'jquery' ), TF_VERSION, true );
			}
		} else {
			wp_enqueue_style( 'tf-flatpickr', TF_ASSETS_URL . 'app/libs/flatpickr/flatpickr.min.css', '', TF_VERSION );
			wp_enqueue_script( 'tf-flatpickr', TF_ASSETS_URL . 'app/libs/flatpickr/flatpickr.min.js', array( 'jquery' ), TF_VERSION, true );
			if ( in_array( $flatpickr_locale, $allowed_locale ) ) {
				wp_enqueue_script( 'tf-flatpickr-locale', TF_ASSETS_URL . 'app/libs/flatpickr/l10n/' . $flatpickr_locale . '.min.js', array( 'jquery' ), TF_VERSION, true );
			}
		}

		/**
		 * Range Slider
		 */
		wp_enqueue_style( 'al-range-slider', TF_ASSETS_URL . 'app/libs/range-slider/al-range-slider.css', '', TF_VERSION );
		wp_enqueue_script( 'al-range-slider', TF_ASSETS_URL . 'app/libs/range-slider/al-range-slider.js', array( 'jquery' ), TF_VERSION, true );
		wp_enqueue_script( 'jquery-ui-autocomplete' );


		/**
		 * Fancybox
		 * v3.5.7
		 */
		if ( $fancy_cdn ) {
			wp_enqueue_style( 'tf-fancyBox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css', array(), TF_VERSION );
			wp_enqueue_script( 'tf-fancyBox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js', array( 'jquery' ), TF_VERSION, true );
		} else {
			wp_enqueue_style( 'fancybox', TF_ASSETS_URL . 'app/libs/fancybox/jquery.fancybox.min.css', '', TF_VERSION );
			wp_enqueue_script( 'fancybox', TF_ASSETS_URL . 'app/libs/fancybox/jquery.fancybox.min.js', array( 'jquery' ), TF_VERSION, true );
		}

		/**
		 * Slick
		 * v1.8.1
		 */
		if ( $slick_cdn ) {
			wp_enqueue_style( 'tf-slick', TF_ASSETS_URL . 'app/libs/slick/slick.css', array(), TF_VERSION );
			wp_enqueue_script( 'tf-slick', '//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array( 'jquery' ), TF_VERSION, true );
		} else {
			wp_enqueue_style( 'tf-slick', TF_ASSETS_URL . 'app/libs/slick/slick.css', '', TF_VERSION );
			wp_enqueue_script( 'tf-slick', TF_ASSETS_URL . 'app/libs/slick/slick.min.js', array( 'jquery' ), TF_VERSION, true );
		}

		/**
		 * Font Awesome Free
		 * v5.15.4
		 */
		if ( $fa_cdn ) {
			wp_enqueue_style( 'tf-fontawesome-4', '//cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css', array(), TF_VERSION );
			wp_enqueue_style( 'tf-fontawesome-5', '//cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css', array(), TF_VERSION );
			wp_enqueue_style( 'tf-fontawesome-6', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css', array(), TF_VERSION );
		} else {
			wp_enqueue_style( 'tf-fontawesome-4', TF_ASSETS_APP_URL . 'libs/font-awesome/fontawesome4/css/font-awesome.min.css', array(), TF_VERSION );
			wp_enqueue_style( 'tf-fontawesome-5', TF_ASSETS_APP_URL . 'libs/font-awesome/fontawesome5/css/all.min.css', array(), TF_VERSION );
			wp_enqueue_style( 'tf-fontawesome-6', TF_ASSETS_APP_URL . 'libs/font-awesome/fontawesome6/css/all.min.css', array(), TF_VERSION );
		}

		/**
		 * Notyf
		 * v3.0
		 */
		wp_enqueue_style( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.css', '', TF_VERSION );
		wp_enqueue_script( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.js', array( 'jquery' ), TF_VERSION, true );

		/**
		 * Openstreet Map
		 * v1.9
		 */

		$tf_openstreet_map = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : "default";
		if ( $tf_openstreet_map == "default" ) {
			wp_enqueue_script( 'tf-leaflet', esc_url( '//cdn.jsdelivr.net/npm/leaflet@' . '1.9' . '/dist/leaflet.js' ), array(), '1.9' );
			wp_enqueue_style( 'tf-leaflet', esc_url( '//cdn.jsdelivr.net/npm/leaflet@' . '1.9' . '/dist/leaflet.css' ), array(), '1.9' );
		}

		/**
		 * Google Map
		 */
		$tf_map_api_key = ! empty( Helper::tfopt( 'tf-googlemapapi' ) ) ? Helper::tfopt( 'tf-googlemapapi' ) : '';
		wp_enqueue_script( 'googleapis', 'https://maps.googleapis.com/maps/api/js?key=' . $tf_map_api_key . '&sensor=false&amp;libraries=places', array(), TOURFIC, true );
		wp_enqueue_script( 'markerclusterer', TF_ASSETS_URL . 'app/libs/markerclusterer.min.js', array(), TOURFIC, true );
		wp_enqueue_script('map-marker-label', TF_ASSETS_URL . 'app/libs/markerwithlabel.js', array(), TOURFIC, true);

		/**
		 * Tour booking form
		 */
		global $post;
		$post_id   = ! empty( $post->ID ) ? $post->ID : '';
		$post_type = ! empty( $post->post_type ) ? $post->post_type : '';
		if ( $post_type == 'tf_tours' && ! empty( $post_id ) && ! is_post_type_archive( 'tf_tours' ) ) {
			$single_tour_form_data = array();

			$meta                      = get_post_meta( $post_id, 'tf_tours_opt', true );
			$tf_tour_layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
			if ( "single" == $tf_tour_layout_conditions ) {
				$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
			}
			$tf_tour_global_template    = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] : 'design-1';
			$tf_tour_selected_template  = ! empty( $tf_tour_single_template ) ? $tf_tour_single_template : $tf_tour_global_template;
			$tour_type                  = ! empty( $meta['type'] ) ? $meta['type'] : '';
			$custom_avail               = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : '';
			$tour_date_format_for_users = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

			// Same Day Booking
			$disable_same_day = ! empty( $meta['disable_same_day'] ) ? $meta['disable_same_day'] : '';
			if ( $tour_type == 'fixed' ) {
				if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
					$tf_tour_fixed_avail        = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $meta['fixed_availability'] );
					$tf_tour_fixed_date         = unserialize( $tf_tour_fixed_avail );
					$departure_date             = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
					$return_date                = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
					$min_people                 = ! empty( $tf_tour_fixed_date['min_seat'] ) ? $tf_tour_fixed_date['min_seat'] : '';
					$max_people                 = ! empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
					$repeated_fixed_tour_switch = ! empty( $tf_tour_fixed_date['fixed_availability']["tf-repeat-months-switch"] ) ? $tf_tour_fixed_date['fixed_availability']["tf-repeat-months-switch"] : 0;
					$tour_repeat_months         = ! empty( $tf_tour_fixed_date['fixed_availability']['tf-repeat-months-checkbox'] ) ? $tf_tour_fixed_date['fixed_availability']['tf-repeat-months-checkbox'] : array();
				} else {
					$departure_date             = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
					$return_date                = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
					$min_people                 = ! empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
					$max_people                 = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
					$repeated_fixed_tour_switch = ! empty( $meta['fixed_availability']["tf-repeat-months-switch"] ) ? $meta['fixed_availability']["tf-repeat-months-switch"] : 0;
					$tour_repeat_months         = $repeated_fixed_tour_switch && ! empty( $meta['fixed_availability']['tf-repeat-months-checkbox'] ) ? $meta['fixed_availability']['tf-repeat-months-checkbox'] : array();
				}

			} elseif ( $tour_type == 'continuous' ) {

				$disabled_day  = ! empty( $meta['disabled_day'] ) ? $meta['disabled_day'] : '';
				$disable_range = ! empty( Helper::tf_data_types($meta['disable_range']) ) ? Helper::tf_data_types($meta['disable_range']) : '';
				$disable_specific = ! empty( $meta['disable_specific'] ) ? $meta['disable_specific'] : '';

				if ( $custom_avail == true ) {
					$cont_custom_date = ! empty( Helper::tf_data_types($meta['cont_custom_date']) ) ? Helper::tf_data_types($meta['cont_custom_date']) : '';
				}

			}

			$tour_extras = isset( $meta['tour-extra'] ) ? Helper::tf_data_types($meta['tour-extra']) : null;

			$times = [];
			if ( ! empty( $meta['cont_custom_date'] ) && gettype( $meta['cont_custom_date'] ) == "string" ) {

				$tf_tour_unserial_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['cont_custom_date'] );
				$tf_tour_unserial_custom_date = unserialize( $tf_tour_unserial_custom_date );

				if ( ! empty( $tf_tour_unserial_custom_date ) ) {
					if ( $custom_avail == true && ! empty( $meta['cont_custom_date'] ) ) {
						$allowed_times = array_map( function ( $v ) {
							return $times[] = [
								'date'  => $v['date'],
								'times' => array_map( function ( $v ) {
									return $v['time'];
								}, $v['allowed_time'] ?? [] )
							];
						}, $tf_tour_unserial_custom_date );
					}
				}

			} else {
				if ( $custom_avail == true && ! empty( $meta['cont_custom_date'] ) ) {
					$allowed_times = array_map( function ( $v ) {
						if ( ! empty( $v['date'] ) ) {
							return $times[] = [
								'date'  => $v['date'],
								'times' => array_map( function ( $v ) {
									return $v['time'];
								}, $v['allowed_time'] ?? [] )
							];
						}
					}, $meta['cont_custom_date'] );
				}

			}
			if ( $tour_type == 'continuous' && $custom_avail == true ) {
				$pricing_rule = ! empty( $meta['custom_pricing_by'] ) ? $meta['custom_pricing_by'] : 'person';
			}
			if ( ! empty( $meta['allowed_time'] ) && gettype( $meta['allowed_time'] ) == "string" ) {

				$tf_tour_unserial_custom_time = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['allowed_time'] );
				$tf_tour_unserial_custom_time = unserialize( $tf_tour_unserial_custom_time );
				if ( ! empty( $tf_tour_unserial_custom_time ) && is_array($tf_tour_unserial_custom_time) ) {
					if ( $custom_avail == false && ! empty( $meta['allowed_time'] ) ) {
						$allowed_times = array_map( function ( $v ) {
							return $v['time'];
						}, $tf_tour_unserial_custom_time ?? [] );
					}
				}
			} else {
				if ( $custom_avail == false && ! empty( $meta['allowed_time'] ) ) {
					$allowed_times = array_map( function ( $v ) {
						return $v['time'];
					}, $meta['allowed_time'] ?? [] );
				}
			}

			$single_tour_form_data['tf_tour_selected_template'] = $tf_tour_selected_template;
			$single_tour_form_data['tour_type']                 = $tour_type;
			$single_tour_form_data['allowed_times']             = wp_json_encode( $allowed_times ?? [] );
			$single_tour_form_data['custom_avail']              = ! empty( $custom_avail ) ? $custom_avail : false;
			$single_tour_form_data['cont_custom_date']          = ! empty( $cont_custom_date ) ? $cont_custom_date : '';
			$single_tour_form_data['first_day_of_week'] = !empty(Helper::tfopt("tf-week-day-flatpickr")) ? Helper::tfopt("tf-week-day-flatpickr") : 0;
			$single_tour_form_data['select_time_text'] = esc_html__( "Select Time", "tourfic" );
			$single_tour_form_data['date_format']      = esc_html( $tour_date_format_for_users );
			$single_tour_form_data['flatpickr_locale'] = ! empty( get_locale() ) ? get_locale() : 'en_US';
			$single_tour_form_data['disabled_day']     = ! empty( $disabled_day ) ? $disabled_day : '';
			$single_tour_form_data['disable_range']    = ! empty( $disable_range ) ? $disable_range : '';
			$single_tour_form_data['disable_specific'] = ! empty( $disable_specific ) ? $disable_specific : '';
			$single_tour_form_data['disable_same_day'] = $disable_same_day;

			$single_tour_form_data['enable'] = array();
			if ( $tour_type && $tour_type == 'fixed' ) {
				if ( ! empty( $departure_date ) && ! empty( $tour_repeat_months ) ) {
					$enable_repeat_dates = Tour::fixed_tour_start_date_changer( $departure_date, $tour_repeat_months );
				}

				if ( ( $repeated_fixed_tour_switch == 1 ) && ! empty( $enable_repeat_dates ) && ( $enable_repeat_dates > 0 ) ) {

					$single_tour_form_data['defaultDate'] = esc_html( Tour::tf_nearest_default_day( $enable_repeat_dates ) );

					foreach ( $enable_repeat_dates as $enable_date ) {
						$single_tour_form_data['enable'][] = esc_html( $enable_date );
					}
				} else {
					$single_tour_form_data['defaultDate'] = esc_html( $departure_date );
					$single_tour_form_data['enable']      = array(
						esc_html( $departure_date )
					);
				}
			} elseif ( $tour_type && $tour_type == 'continuous' ) {
				if ( $custom_avail && $custom_avail == true ) {
					if( is_array($cont_custom_date)) {
						foreach ( $cont_custom_date as $item ) {
							$single_tour_form_data['enable'][] = array(
								'from' => esc_attr( $item["date"]["from"] ),
								'to'   => esc_attr( $item["date"]["to"] )
							);
						}
					}
				}
				if ( $custom_avail == false ) {
					if ( $disabled_day || $disable_range || $disable_specific || $disable_same_day ) {
						$single_tour_form_data['disable'] = array();
						if ( $disabled_day ) {
							$single_tour_form_data['disable'][] = "function (date) {
                                    return (date.getDay() === 8";
							foreach ( $disabled_day as $dis_day ) {
								$single_tour_form_data['disable'][] = '|| date.getDay() === ' . esc_attr( $dis_day );
							}
							$single_tour_form_data['disable'][] = ");";
						}
						if ( !empty($disable_range) && is_array($disable_range) ) {
							foreach ( $disable_range as $d_item ) {
								$single_tour_form_data['disable'][] = array(
									'from' => esc_attr( $d_item["date"]["from"] ),
									'to'   => esc_attr( $d_item["date"]["to"] )
								);
							}
						}
						if ( $disable_same_day ) {
							$single_tour_form_data['disable'][] = '"today"';
							if ( $disable_specific ) {
								$single_tour_form_data['disable'][] = ",";
							}
						}
						if ( $disable_specific ) {
							$single_tour_form_data['disable'][] = '"' . esc_attr( $disable_specific ) . '"';
						}
					}
				}
			}


		}

		// var_dump(Helper::tf_templates_body_class());
		// die();

		/**
		 * Custom
		 */
		wp_enqueue_script( 'tourfic', TF_ASSETS_APP_URL . 'js/tourfic-scripts' . $min_js . '.js', '', TF_VERSION, true );
		wp_localize_script( 'tourfic', 'tf_params',
			array(
				'nonce'                  => wp_create_nonce( 'tf_ajax_nonce' ),
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'single'                 => is_single(),
				'body_classes'           => Helper::tf_templates_body_class(),
				'locations'              => Helper::get_terms_dropdown('hotel_location'),
				'apartment_locations'    => Helper::get_terms_dropdown('apartment_location'),
				'tour_destinations'      => Helper::get_terms_dropdown('tour_destination'),
				'car_locations'      	 => Helper::get_terms_dropdown('carrental_location'),
				'ajax_result_success'    => esc_html__( 'Refreshed Successfully!', 'tourfic' ),
				'wishlist_add'           => esc_html__( 'Adding to wishlist...', 'tourfic' ),
				'wishlist_added'         => esc_html__( 'Item added to wishlist.', 'tourfic' ),
				'wishlist_add_error'     => esc_html__( 'Failed to add in wishlist!', 'tourfic' ),
				'wishlist_removed'       => esc_html__( 'Item removed from wishlist', 'tourfic' ),
				'wishlist_remove_error'  => esc_html__( 'Failed to remove from wishlist!', 'tourfic' ),
				'field_required'         => esc_html__( 'This field is required!', 'tourfic' ),
				'adult'                  => esc_html__( 'Adult', 'tourfic' ),
				'children'               => esc_html__( 'Children', 'tourfic' ),
				'infant'                 => esc_html__( 'Infant', 'tourfic' ),
				'room'                   => esc_html__( 'Room', 'tourfic' ),
				'sending_ques'           => esc_html__( 'Sending your question...', 'tourfic' ),
				'no_found'               => esc_html__( 'Not Found', 'tourfic' ),
				'no_room_found'  		 => esc_html__("No Room is selected from the backend, for this Hotel!", "tourfic"),
				'tf_hotel_max_price'     => isset( $hotel_min_max_price ) ? $hotel_min_max_price['max'] : 0,
				'tf_hotel_min_price'     => isset( $hotel_min_max_price ) ? $hotel_min_max_price['min'] : 0,
				'tf_tour_max_price'      => isset( $tour_min_max_price ) ? $tour_min_max_price['max'] : '',
				'tf_tour_min_price'      => isset( $tour_min_max_price ) ? $tour_min_max_price['min'] : '',
				'itinerarayday'          => isset( $itinerarayday ) ? $itinerarayday : '',
				'itineraraymeter'        => isset( $itineraraymeter ) ? $itineraraymeter : '',
				'showxaxis'              => isset( $showxaxis ) ? $showxaxis : '',
				'showyaxis'              => isset( $showyaxis ) ? $showyaxis : '',
				'showlinegraph'          => isset( $showlinegraph ) ? $showlinegraph : '',
				'elevvationmode'         => isset( $elevvationmode ) ? $elevvationmode : '',
				'showitinerarychart'     => isset( $showitinerarychart ) ? $showitinerarychart : '',
				'showitinerarystatus'    => isset( $showitinerarystatus ) ? $showitinerarystatus : '',
				'date_hotel_search'      => Helper::tfopt( 'date_hotel_search' ),
				'date_tour_search'       => Helper::tfopt( 'date_tour_search' ),
				'date_apartment_search'  => Helper::tfopt( 'date_apartment_search' ),
				'location_car_search'  => Helper::tfopt( 'pick_drop_car_search' ),
				'date_car_search'  => Helper::tfopt( 'pick_drop_date_car_search' ),
				'tf_apartment_max_price' => isset( $tf_apartment_min_max_price ) ? $tf_apartment_min_max_price['max'] : 0,
				'tf_apartment_min_price' => isset( $tf_apartment_min_max_price ) ? $tf_apartment_min_max_price['min'] : 0,
				'tour_form_data'         => isset( $single_tour_form_data ) ? $single_tour_form_data : array(),
				'hotel_archive_template' => Hotel::template(),
				'hotel_single_template' => $post_type == 'tf_hotel' ? Hotel::template('single', $post_id) : '',
				'tour_archive_template' => Tour::template(),
				'tour_single_template' => $post_type == 'tf_tours' ? Tour::template('single', $post_id) : '',
				'apartment_archive_template' => Apartment::template(),
				'apartment_single_template' => $post_type == 'tf_apartment' ? Apartment::template('single', $post_id) : '',
				'tf_hotel_date_required_msg' => esc_html__('Please select check in and check out date', 'tourfic'),
				'tf_tour_date_required_msg' => esc_html__('Please select a date', 'tourfic'),
				'tf_car_max_price' => isset( $tf_car_min_max_price['max'] ) ? $tf_car_min_max_price['max'] : 0,
				'tf_car_min_price' => isset( $tf_car_min_max_price['min'] ) ? $tf_car_min_max_price['min'] : 0,
				'tf_car_min_seat' =>  isset( $tf_car_min_max_price['min_seat'] ) ? $tf_car_min_max_price['min_seat'] : 0,
				'tf_car_max_seat' =>  isset( $tf_car_min_max_price['max_seat'] ) ? $tf_car_min_max_price['max_seat'] : 0,
				'map_marker_width' => !empty(Helper::tfopt( 'map_marker_width' )) ? Helper::tfopt( 'map_marker_width' ) : '35',
				'map_marker_height' => !empty(Helper::tfopt( 'map_marker_height' )) ? Helper::tfopt( 'map_marker_height' ) : '45',
			)
		);

		/**
		 * Inline scripts
		 */
		// Get single tour meta data
		global $post;
		if ( ! is_404() && ! empty( $post ) && is_single() ) {
			$meta = ! empty( get_post_meta( $post->ID, 'tf_tours_opt', true ) ) ? get_post_meta( $post->ID, 'tf_tours_opt', true ) : '';
		}
		$tour_type = ! empty( $meta['type'] ) ? $meta['type'] : '';

		# Inline scripts
		$inline_scripts = '';
		// JS Start
		$inline_scripts .= '(function ($) { $(document).ready(function () {';

		if ( $tour_type == 'fixed' ) {
			// Disable date selection in calendar
			$inline_scripts .= '$(".flatpickr-day").css("pointer-events", "none"); ';
		}

		// JS end
		$inline_scripts .= '}); })(jQuery);';

		wp_add_inline_script( 'tourfic', $inline_scripts );

	}

	/**
	 * Enqueue Admin scripts
	 * @since 1.0
	 */
	function tf_enqueue_admin_scripts( $screen ) {

		/**
		 * Notyf
		 * v3.0
		 */
		wp_enqueue_style( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.css', '', TF_VERSION );
		wp_enqueue_script( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.js', array( 'jquery' ), TF_VERSION, true );

		if ( ($screen == "widgets.php" && function_exists( 'is_woocommerce' )) || 
			$screen == 'tf_hotel_page_tf_export_hotels' ||
			$screen == 'tf_tours_page_tf_export_tours' ||
			$screen == 'tf_apartment_page_tf_export_apartments' ||
			$screen == 'tf_carrental_page_tf_export_cars') {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ?: '.min';

			$assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
			wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' );
			wp_register_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css' );

			wp_enqueue_script( 'select2' );
			wp_enqueue_style( 'select2' );

			$output = "
            (function($) {
                'use strict';
                jQuery(document).ready(function() { ";

			$output .= "$(document).on('tf_select2 widget-added widget-updated', function() {
                        jQuery('.tf-select2').each(function(){
                            if( !$(this).hasClass('select2-hidden-accessible') ){
                                $(this).select2({ width: '100%' });
                            }
                        });

                    });";

			$output .= "
                });
            })(jQuery);";

			wp_add_inline_script( 'select2', $output );

		}

	}

	/**
	 * Others Theme & Plugin Compatibility
	 *
	 * @since 2.9.27
	 */
	function tf_dequeue_theplus_script_on_settings_page( $screen ) {

		// The Plus Addons for Elementor Compatibility
		if ( "toplevel_page_tf_settings" == $screen && wp_script_is( 'theplus-admin-js-pro', 'enqueued' ) ) {
			wp_dequeue_script( 'theplus-admin-js-pro' );
			wp_deregister_script( 'theplus-admin-js-pro' );
		}

		//The Guido theme WP Listings Directory Compatibility
		if ( "toplevel_page_tf_settings" == $screen && wp_script_is( 'wp-listings-directory-custom-field', 'enqueued' ) ) {
			wp_dequeue_script( 'wp-listings-directory-custom-field' );
			wp_deregister_script( 'wp-listings-directory-custom-field' );
		}
		//The Easy Table of Contents Compatibility
		if ( "toplevel_page_tf_settings" == $screen && wp_script_is( 'cn_toc_admin_script', 'enqueued' ) ) {
			wp_dequeue_script( 'cn_toc_admin_script' );
			wp_deregister_script( 'cn_toc_admin_script' );
		}

		$get_screen = get_current_screen();
		global $wp_scripts;

		if ( ! empty( $get_screen ) ) {

			if ( $get_screen->base == "post" && ( $get_screen->id == "tf_hotel" || $get_screen->id == "tf_apartment" || $get_screen->id == "tf_tours" ) ) {

				if ( wp_script_is( 'select2', 'enqueued' ) ) {

					wp_dequeue_script( 'select2' );
					wp_deregister_script( 'select2' );
				}

				if ( wp_script_is( 'acf-color-picker-alpha', 'enqueued' ) ) {

					$acf_script_handle = 'acf-color-picker-alpha';
					$acf_script_data   = $wp_scripts->registered[ $acf_script_handle ];

					wp_dequeue_script( $acf_script_handle );

					if ( isset( $acf_script_data ) ) {
						wp_enqueue_script( $acf_script_handle, $acf_script_data->src, $acf_script_data->deps, $acf_script_data->ver, true );
					}
				}

				if ( wp_script_is( 'revbuilder-utils', 'enqueued' ) ) {

					$rev_script_handle = 'revbuilder-utils';
					$rev_slider_script = $wp_scripts->registered[ $rev_script_handle ];

					wp_dequeue_script( $rev_script_handle );

					if ( isset( $rev_slider_script ) ) {

						wp_enqueue_script( $rev_script_handle, $rev_slider_script->src, $rev_slider_script->deps, $rev_slider_script->ver, true );
					}
				}

				if ( wp_script_is( 'wp-color-picker-alpha', 'enqueued' ) ) {

					$divi_script_handle = 'wp-color-picker-alpha';
					$divi_slider_script = $wp_scripts->registered[ $divi_script_handle ];

					wp_dequeue_script( $divi_script_handle );

					if ( isset( $divi_slider_script ) ) {

						wp_enqueue_script( $divi_script_handle, $divi_slider_script->src, $divi_slider_script->deps, $divi_slider_script->ver, true );
					}
				}
				
				if ( wp_script_is( 'cn_toc_admin_script', 'enqueued' ) ) {

					$easy_toc_script_handle = 'cn_toc_admin_script';
					$easy_toc_slider_script = $wp_scripts->registered[ $easy_toc_script_handle ];

					wp_dequeue_script( $easy_toc_script_handle );
					wp_deregister_script( $easy_toc_script_handle );

					if ( isset( $easy_toc_slider_script ) ) {

						wp_enqueue_script( $easy_toc_script_handle, $easy_toc_slider_script->src, $easy_toc_slider_script->deps, $easy_toc_slider_script->ver, true );
					}
				}
			}
		}
	}

	/**
	 * Admin Enqueue scripts
	 * @author Foysal
	 */
	public function tf_options_admin_enqueue_scripts( $screen ) {
		global $post_type;
		$tf_options_screens          = array(
			'toplevel_page_tf_settings',
			'tourfic-settings_page_tf_get_help',
			'tourfic-settings_page_tf_license_info',
			'tourfic-settings_page_tf_dashboard',
			'tourfic-settings_page_tf_shortcodes',
			'tourfic-vendor_page_tf_vendor_reports',
			'tourfic-vendor_page_tf_vendor_list',
			'tourfic-vendor_page_tf_vendor_commissions',
			'tourfic-vendor_page_tf_vendor_withdraw',
			'tf_hotel_page_tf-hotel-backend-booking',
			'tf_hotel_page_tf_hotel_enquiry',
			'tf_tours_page_tf-tour-backend-booking',
			'tf_tours_page_tf_tours_enquiry',
			'tf_tours_page_tf_tours_booking',
			'tf_hotel_page_tf_hotel_booking',
			'tf_apartment_page_tf_apartment_booking',
			'tf_carrental_page_tf_carrental_booking',
			'tf_apartment_page_tf-apartment-backend-booking',
			'tf_apartment_page_tf_apartment_enquiry',
			'tourfic-settings_page_tf-setup-wizard'
		);
		$tf_options_post_type        = array( 'tf_hotel', 'tf_tours', 'tf_apartment', 'tf_email_templates', 'tf_carrental', 'tf_room' );
		$admin_date_format_for_users = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

		// cdn options
		$flatpickr_cdn    = ! empty( Helper::tfopt( 'ftpr_cdn' ) ) ? Helper::tfopt( 'ftpr_cdn' ) : false;
		$fancy_box_cdn = !empty( Helper::tfopt( 'fnybx_cdn' ) ) ? Helper::tfopt( 'fnybx_cdn' ) : false;
		$slick_cdn = !empty( Helper::tfopt( 'slick_cdn' ) ) ? Helper::tfopt( 'slick_cdn' ) : false;
		$fa_cdn = !empty( Helper::tfopt( 'fa_cdn' ) ) ? Helper::tfopt( 'fa_cdn' ) : false;
		$select2_cdn = !empty( Helper::tfopt( 'select2_cdn' ) ) ? Helper::tfopt( 'select2_cdn' ) : false;
		$remix_cdn = !empty( Helper::tfopt( 'remix_cdn' ) ) ? Helper::tfopt( 'remix_cdn' ) : false;
		$leaflet_cdn = !empty( Helper::tfopt( 'leaflet_cdn' ) ) ? Helper::tfopt( 'leaflet_cdn' ) : false;
		$swal_cdn = !empty( Helper::tfopt( 'swal_cdn' ) ) ? Helper::tfopt( 'swal_cdn' ) : false;
		$chart_cdn = !empty( Helper::tfopt( 'chart_cdn' ) ) ? Helper::tfopt( 'chart_cdn' ) : false;


		if ( Helper::tf_is_woo_active() ) {
			if ( "tourfic-settings_page_tf_dashboard" == $screen ) {
				//Order Data Retrive
				$tf_old_order_limit = new \WC_Order_Query( array(
					'limit'   => - 1,
					'orderby' => 'date',
					'order'   => 'ASC',
					'return'  => 'ids',
				) );
				$order              = $tf_old_order_limit->get_orders();
				// Booking Month
				$tf_co1  = 0;
				$tf_co2  = 0;
				$tf_co3  = 0;
				$tf_co4  = 0;
				$tf_co5  = 0;
				$tf_co6  = 0;
				$tf_co7  = 0;
				$tf_co8  = 0;
				$tf_co9  = 0;
				$tf_co10 = 0;
				$tf_co11 = 0;
				$tf_co12 = 0;
				// Booking Cancel Month
				$tf_cr1  = 0;
				$tf_cr2  = 0;
				$tf_cr3  = 0;
				$tf_cr4  = 0;
				$tf_cr5  = 0;
				$tf_cr6  = 0;
				$tf_cr7  = 0;
				$tf_cr8  = 0;
				$tf_cr9  = 0;
				$tf_cr10 = 0;
				$tf_cr11 = 0;
				$tf_cr12 = 0;
				foreach ( $order as $item_id => $item ) {
					$itemmeta         = wc_get_order( $item );
					$tf_ordering_date = $itemmeta->get_date_created();
					if ( $tf_ordering_date->date( 'n-y' ) == '1-' . gmdate( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co1 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr1 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '2-' . gmdate( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co2 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr2 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '3-' . gmdate( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co3 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr3 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '4-' . gmdate( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co4 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr4 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '5-' . gmdate( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co5 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr5 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '6-' . gmdate( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co6 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr6 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '7-' . gmdate( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co7 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr7 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '8-' . gmdate( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co8 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr8 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '9-' . gmdate( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co9 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr9 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '10-' . gmdate( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co10 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr10 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '11-' . gmdate( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co11 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr11 += 1;
						}
					}
					if ( $tf_ordering_date->date( 'n-y' ) == '12-' . gmdate( 'y' ) ) {
						if ( "completed" == $itemmeta->get_status() ) {
							$tf_co12 += 1;
						}
						if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
							$tf_cr12 += 1;
						}
					}
				}
				$tf_complete_orders = [ $tf_co1, $tf_co2, $tf_co3, $tf_co4, $tf_co5, $tf_co6, $tf_co7, $tf_co8, $tf_co9, $tf_co10, $tf_co11, $tf_co12 ];
				$tf_cancel_orders   = [ $tf_cr1, $tf_cr2, $tf_cr3, $tf_cr4, $tf_cr5, $tf_cr6, $tf_cr7, $tf_cr8, $tf_cr9, $tf_cr10, $tf_cr11, $tf_cr12 ];
				$tf_chart_enable    = 1;
			}
		}

		// Tour Booking Data retrive
		$tf_tour_orders_select = array(
			'select'    => "id, order_id, post_id, check_in, check_out, ostatus",
			'post_type' => 'tour',
			'query'     => " ORDER BY id DESC"
		);
		$tf_tour_order_result = Helper::tourfic_order_table_data( $tf_tour_orders_select );
		$tf_tours_orders = [];
		if(!empty($tf_tour_order_result)){
			foreach($tf_tour_order_result as $order){
				$tf_tours_orders[] = array(
					'title' => '#'.$order['order_id'].' '.html_entity_decode(get_the_title($order['post_id'])),
					'start' => $order['check_in'],
					'end' => $order['check_out'],
					'id' => $order['id'],
					'status' => $order['ostatus'],
					'post_type' => 'tf_tours',
					'page' => 'tf_tours_booking',
					'classNames' => ['tf-order-'.$order['ostatus']]
				);
			}
		}

		// Hotel Booking Data retrive
		$tf_hotel_orders_select = array(
			'select'    => "id, order_id, post_id, check_in, check_out, ostatus",
			'post_type' => 'hotel',
			'query'     => " ORDER BY id DESC"
		);
		$tf_hotel_order_result = Helper::tourfic_order_table_data( $tf_hotel_orders_select );
		$tf_hotels_orders = [];
		if(!empty($tf_hotel_order_result)){
			foreach($tf_hotel_order_result as $order){
				$tf_hotels_orders[] = array(
					'title' => '#'.$order['order_id'].' '.html_entity_decode(get_the_title($order['post_id'])),
					'start' => $order['check_in'],
					'end' => $order['check_out'],
					'id' => $order['id'],
					'status' => $order['ostatus'],
					'post_type' => 'tf_hotel',
					'page' => 'tf_hotel_booking',
					'classNames' => ['tf-order-'.$order['ostatus']]
				);
			}
		}

		// Apartment Booking Data retrive
		$tf_apartment_orders_select = array(
			'select'    => "id, order_id, post_id, check_in, check_out, ostatus",
			'post_type' => 'apartment',
			'query'     => " ORDER BY id DESC"
		);
		$tf_apartment_order_result = Helper::tourfic_order_table_data( $tf_apartment_orders_select );
		$tf_apartments_orders = [];
		if(!empty($tf_apartment_order_result)){
			foreach($tf_apartment_order_result as $order){
				$tf_apartments_orders[] = array(
					'title' => '#'.$order['order_id'].' '.html_entity_decode(get_the_title($order['post_id'])),
					'start' => $order['check_in'],
					'end' => $order['check_out'],
					'id' => $order['id'],
					'status' => $order['ostatus'],
					'post_type' => 'tf_apartment',
					'page' => 'tf_apartment_booking',
					'classNames' => ['tf-order-'.$order['ostatus']]
				);
			}
		}

		// Car Booking Data retrive
		$tf_car_orders_select = array(
			'select'    => "id, order_id, post_id, check_in, check_out, ostatus",
			'post_type' => 'car',
			'query'     => " ORDER BY id DESC"
		);
		$tf_car_order_result = Helper::tourfic_order_table_data( $tf_car_orders_select );
		$tf_cars_orders = [];
		if(!empty($tf_car_order_result)){
			foreach($tf_car_order_result as $order){
				$tf_cars_orders[] = array(
					'title' => '#'.$order['order_id'].' '.html_entity_decode(get_the_title($order['post_id'])),
					'start' => $order['check_in'],
					'end' => $order['check_out'],
					'id' => $order['id'],
					'status' => $order['ostatus'],
					'post_type' => 'tf_carrental',
					'page' => 'tf_carrental_booking',
					'classNames' => ['tf-order-'.$order['ostatus']]
				);
			}
		}

		$travelfic_toolkit_active_plugins = [];
		if ( ! is_plugin_active( 'travelfic-toolkit/travelfic-toolkit.php' ) ) {
			$travelfic_toolkit_active_plugins[] = "travelfic-toolkit";
		}

		$current_active_theme = ! empty( get_option( 'stylesheet' ) ) ? get_option( 'stylesheet' ) : '';


		//Color-Picker Css
		if ( in_array( $screen, $tf_options_screens ) || in_array( $post_type, $tf_options_post_type ) ) {
			wp_enqueue_style( 'tf-admin', TF_ASSETS_ADMIN_URL . 'css/tourfic-admin.min.css', '', TF_VERSION );

			if( $swal_cdn ) {
				wp_enqueue_style( 'tf-admin-jquery-confirm', '//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css', '', TF_VERSION );
				wp_enqueue_script( 'tf-admin-jquery-confirm', '///cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js', array( 'jquery' ), TF_VERSION, true );
			} else {
				wp_enqueue_style( 'tf-admin-jquery-confirm', TF_ASSETS_APP_URL . 'libs/jq-confirm/jquery-confirm.min.css', '', TF_VERSION );
				wp_enqueue_script( 'tf-admin-jquery-confirm', TF_ASSETS_APP_URL . 'libs/jq-confirm/jquery-confirm.min.js', array( 'jquery' ), TF_VERSION, true );
			}

			if( $fa_cdn ) {
				wp_enqueue_style( 'tf-fontawesome-4', '//cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css', array(), TF_VERSION );
				wp_enqueue_style( 'tf-fontawesome-5', '//cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css', array(), TF_VERSION );
				wp_enqueue_style( 'tf-fontawesome-6', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css', array(), TF_VERSION );
			} else {
				wp_enqueue_style( 'tf-fontawesome-4', TF_ASSETS_APP_URL . 'libs/font-awesome/fontawesome4/css/font-awesome.min.css', array(), TF_VERSION );
				wp_enqueue_style( 'tf-fontawesome-5', TF_ASSETS_APP_URL . 'libs/font-awesome/fontawesome5/css/all.min.css', array(), TF_VERSION );
				wp_enqueue_style( 'tf-fontawesome-6', TF_ASSETS_APP_URL . 'libs/font-awesome/fontawesome6/css/all.min.css', array(), TF_VERSION );
			}

			if( $remix_cdn ) {
				wp_enqueue_style( 'tf-remixicon', '//cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css', array(), TF_VERSION );
			} else {
				wp_enqueue_style( 'tf-remixicon', TF_ASSETS_APP_URL . 'libs/remixicon/remixicon.css', array(), TF_VERSION );
			}

			if( $select2_cdn ) {
				wp_enqueue_style( 'tf-select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), TF_VERSION );
				wp_enqueue_script( 'tf-select2', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), TF_VERSION, true );
			} else {
				wp_enqueue_style( 'tf-select2', TF_ASSETS_APP_URL . 'libs/select2/select2.min.css', array(), TF_VERSION );
				wp_enqueue_script( 'tf-select2', TF_ASSETS_APP_URL . 'libs/select2/select2.min.js', array( 'jquery' ), TF_VERSION, true );
			}

			if( $flatpickr_cdn ) {
				wp_enqueue_style( 'tf-flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css', array(), TF_VERSION );
			} else {
				wp_enqueue_style( 'tf-flatpickr', TF_ASSETS_APP_URL . 'libs/flatpickr/flatpickr.min.css', array(), TF_VERSION );
			}

			wp_enqueue_style( 'wp-color-picker' );
		}

		//Js
		if ( in_array( $screen, $tf_options_screens ) || in_array( $post_type, $tf_options_post_type ) ) {

			//date format
			$date_format_change = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

			wp_enqueue_script( 'tf-fullcalender', TF_ASSETS_ADMIN_URL . 'js/lib/fullcalender.min.js', array( 'jquery' ), TF_VERSION, true );

			wp_enqueue_script( 'tf-admin', TF_ASSETS_ADMIN_URL . 'js/tourfic-admin-scripts.min.js', array( 'jquery', 'wp-data', 'wp-editor', 'wp-edit-post' ), TF_VERSION, true );
			wp_localize_script( 'tf-admin', 'tf_admin_params',
				array(
					'tf_nonce'                         => wp_create_nonce( 'updates' ),
					'ajax_url'                         => admin_url( 'admin-ajax.php' ),
					'toolkit_page_url'                 => admin_url( 'admin.php?page=travelfic-template-list' ),
					'is_travelfic_toolkit_active'      => $travelfic_toolkit_active_plugins,
					'current_active_theme'             => $current_active_theme,
					'deleting_old_review_fields'       => esc_html__( 'Deleting old review fields...', 'tourfic' ),
					'deleting_room_order_ids'          => esc_html__( 'Deleting order ids...', 'tourfic' ),
					'tour_location_required'           => esc_html__( 'Tour Location is a required field!', 'tourfic' ),
					'hotel_location_required'          => esc_html__( 'Hotel Location is a required field!', 'tourfic' ),
					'apartment_location_required'      => esc_html__( 'Apartment Location is a required field!', 'tourfic' ),
					'installing'                       => esc_html__( 'Installing...', 'tourfic' ),
					'activating'                       => esc_html__( 'Activating...', 'tourfic' ),
					'installed'                        => esc_html__( 'Installed', 'tourfic' ),
					'activated'                        => esc_html__( 'Activated', 'tourfic' ),
					'install_failed'                   => esc_html__( 'Install failed', 'tourfic' ),
					'setting_search_no_result'         => esc_html__( 'No result found!', 'tourfic' ),
					/* translators: %s: strong tag */
					'max_input_vars_notice'            => sprintf( esc_html__( 'WARNING: If you are having trouble saving your settings, please increase the %1$s "PHP Max Input Vars" %2$s value to save all settings.', 'tourfic' ), '<strong>', '</strong>' ),
					'is_woo_not_active'                => ( ! file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) || ! is_plugin_active( 'woocommerce/woocommerce.php' ) ),
					'date_format_change_backend'       => $date_format_change,
					'no_data_found_with_id'            => esc_html__( 'No results found, with this ID', 'tourfic' ),
					'i18n'                             => array(
						'no_services_selected' => esc_html__( 'Please select at least one service.', 'tourfic' ),
					),
					'is_pro'                           => function_exists( 'is_tf_pro' ) && is_tf_pro(),
				)
			);

			if( $chart_cdn ) {
				wp_enqueue_script( 'Chart-js', '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js', array( 'jquery' ), '2.6.0', true );
			} else {
				wp_enqueue_script( 'Chart-js',  TF_ASSETS_APP_URL . 'libs/chart/chart.js', array( 'jquery' ), '2.6.0', true );
			}

			if( $flatpickr_cdn ) {
				wp_enqueue_script( 'tf-flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js', array( 'jquery' ), TF_VERSION, true );
			} else {
				wp_enqueue_script( 'tf-flatpickr', TF_ASSETS_APP_URL . 'libs/flatpickr/flatpickr.min.js', array( 'jquery' ), TF_VERSION, true );
			}


			$tf_google_map = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : "false";
			if ( $tf_google_map != "googlemap" ) {

				if( $leaflet_cdn ) {
					wp_enqueue_script( 'tf-leaflet', esc_url( '//cdn.jsdelivr.net/npm/leaflet@' . '1.9' . '/dist/leaflet.js' ), array( 'jquery' ), '1.9', true );
					wp_enqueue_style( 'tf-leaflet', esc_url( '//cdn.jsdelivr.net/npm/leaflet@' . '1.9' . '/dist/leaflet.css' ), array(), '1.9' );
				} else {
					wp_enqueue_script( 'tf-leaflet',  TF_ASSETS_APP_URL . 'libs/leaflet/leaflet.js', array( 'jquery' ), '1.9', true );
					wp_enqueue_style( 'tf-leaflet', TF_ASSETS_APP_URL . 'libs/leaflet/leaflet.css', array(), '1.9' );
				}
			}
			wp_enqueue_script( 'jquery-ui-autocomplete' );

			if ( ! wp_script_is( 'jquery-ui-sortable' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}
			wp_enqueue_media();
			wp_enqueue_editor();

			wp_dequeue_script('wp-color-picker');
			wp_enqueue_script( 'wp-color-picker' );
		}

		$tf_google_map = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : "false";
		wp_localize_script( 'tf-admin', 'tf_options', array(
			'ajax_url'             => admin_url( 'admin-ajax.php' ),
			'nonce'                => wp_create_nonce( 'tf_options_nonce' ),
			'gmaps'                => $tf_google_map,
			'tf_complete_order'    => isset( $tf_complete_orders ) ? $tf_complete_orders : '',
			'tf_cancel_orders'     => isset( $tf_cancel_orders ) ? $tf_cancel_orders : '',
			'tf_chart_enable'      => isset( $tf_chart_enable ) ? $tf_chart_enable : '',
			'tf_admin_date_format' => $admin_date_format_for_users,
			'swal_reset_title_text'			   => esc_html__( 'Are you sure you want to reset all settings?', 'tourfic' ),
			'swal_reset_other_text'			   => esc_html__( 'You won\'t be able to retrive this settings, again!', 'tourfic' ),
			'swal_reset_btn_text'			   => esc_html__( 'Confirm', 'tourfic' ),
			'tf_export_import_msg' => array(
				'imported'       => esc_html__( 'Imported successfully!', 'tourfic' ),
				'import_confirm' => esc_html__( 'Are you sure you want to import this data?', 'tourfic' ),
				'import_empty'   => esc_html__( 'Import Data cannot be empty!', 'tourfic' ),
			),
			'tf_tours_orders' => $tf_tours_orders,
			'tf_hotels_orders' => $tf_hotels_orders,
			'tf_apartments_orders' => $tf_apartments_orders,
			'tf_cars_orders' => $tf_cars_orders
		) );
	}

	/**
	 * Dequeue scripts
	 */
	public function tf_options_admin_dequeue_scripts( $screen ) {
		global $post_type;
		$tf_options_post_type = array( 'tf_hotel', 'tf_tours', 'tf_apartment' );

		if ( $screen == 'toplevel_page_tf_settings' || in_array( $post_type, $tf_options_post_type ) ) {
			wp_dequeue_script( 'theplus-admin-js-pro' );
		}
	}

	/**
	 * Enqueue scripts
	 * @author Foysal
	 */
	public function tf_options_wp_enqueue_scripts() {
		wp_enqueue_style( 'tf-fontawesome-4', '//cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css', array(), TF_VERSION );
		wp_enqueue_style( 'tf-fontawesome-5', '//cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css', array(), TF_VERSION );
		wp_enqueue_style( 'tf-fontawesome-6', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css', array(), TF_VERSION );
		wp_enqueue_style( 'tf-remixicon', '//cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css', array(), TF_VERSION );
	}

	/**
	 * Enqueue Global CSS
	 * @author Mofazzal
	*/
	public function tf_global_custom_css() {

		$color_palette_template = ! empty( Helper::tfopt( 'color-palette-template' ) ) ? Helper::tfopt( 'color-palette-template' ) : 'design-1';
		$tf_container = ! empty( Helper::tfopt( 'tf-container' ) ) ? Helper::tfopt( 'tf-container' ) : 'boxed';
		$tf_container_width = ! empty( Helper::tfopt( 'tf-container-width' ) ) ? Helper::tfopt( 'tf-container-width' ) . 'px' : '1280px';

		$design_default = [
			'design-1' => [
				'brand' => array(
					'default' => '#0E3DD8',
					'dark' => '#0A2B99',
					'lite' => '#C9D4F7',
				),
				'text'  => array(
					'heading' => '#1C2130',
					'paragraph' => '#494D59',
					'lite' => '#F3F5FD',
				),
				'border'  => array(
					'default' => '#16275F',
					'lite' => '#D1D7EE',
				),
				'filling'  => array(
					'background' => '#ffffff',
					'foreground' => '#F5F7FF',
				),
			],
			'design-2' => [
				'brand'  => array(
					'default' => '#B58E53',
					'dark' => '#917242',
					'lite' => '#FAEEDC',
				),
				'text'  => array(
					'heading' => '#30281C',
					'paragraph' => '#595349',
					'lite' => '#FDF9F3',
				),
				'border'  => array(
					'default' => '#5F4216',
					'lite' => '#EEE2D1',
				),
				'filling'  => array(
					'background' => '#ffffff',
					'foreground' => '#FDF9F3',
				),
			],
			'design-3' => [
				'brand'  => array(
					'default' => '#F97415',
					'dark' => '#C75605',
					'lite' => '#FDDCC3',
				),
				'text'  => array(
					'heading' => '#30241C',
					'paragraph' => '#595049',
					'lite' => '#FDF7F3',
				),
				'border'  => array(
					'default' => '#5F3416',
					'lite' => '#EEDDD1',
				),
				'filling'  => array(
					'background' => '#ffffff',
					'foreground' => '#FFF9F5',
				),
			],
			'design-4' => [
				'brand'  => array(
					'default' => '#003061',
					'dark' => '#002952',
					'lite' => '#C2E0FF',
				),
				'text'  => array(
					'heading' => '#1C2630',
					'paragraph' => '#495159',
					'lite' => '#F3F8FD',
				),
				'border'  => array(
					'default' => '#163A5F',
					'lite' => '#D1DFEE',
				),
				'filling'  => array(
					'background' => '#ffffff',
					'foreground' => '#F5FAFF',
				),
			],
			'custom' => []
		];

		foreach ( $design_default as $key => $value ) {
			if('custom' !== $key && $color_palette_template === $key){
				
				$tf_key_split = explode('-', $key);
				$tf_id = end($tf_key_split);
				$tf_brand_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-d{$tf_id}-brand" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-d{$tf_id}-brand" ) ) : [];
				$tf_text_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-d{$tf_id}-text" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-d{$tf_id}-text" ) ) : [];
				$tf_border_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-d{$tf_id}-border" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-d{$tf_id}-border" ) ) : [];
				$tf_filling_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-d{$tf_id}-filling" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-d{$tf_id}-filling" ) ) : [];

				$tf_brand_default = ! empty( $tf_brand_data['default'] ) ? $tf_brand_data['default'] : $value['brand']['default'];
				$tf_brand_dark = ! empty( $tf_brand_data['dark'] ) ? $tf_brand_data['dark'] : $value['brand']['dark'];
				$tf_brand_lite = ! empty( $tf_brand_data['lite'] ) ? $tf_brand_data['lite'] : $value['brand']['lite'];
				$tf_text_heading = ! empty( $tf_text_data['heading'] ) ? $tf_text_data['heading'] : $value['text']['heading'];
				$tf_text_paragraph = ! empty( $tf_text_data['paragraph'] ) ? $tf_text_data['paragraph'] : $value['text']['paragraph'];
				$tf_text_lite = ! empty( $tf_text_data['lite'] ) ? $tf_text_data['lite'] : $value['text']['lite'];
				$tf_border_default = ! empty( $tf_border_data['default'] ) ? $tf_border_data['default'] : $value['border']['default'];
				$tf_border_lite = ! empty( $tf_border_data['lite'] ) ? $tf_border_data['lite'] : $value['border']['lite'];
				$tf_filling_background = ! empty( $tf_filling_data['background'] ) ? $tf_filling_data['background'] : $value['filling']['background'];
				$tf_filling_foreground = ! empty( $tf_filling_data['foreground'] ) ? $tf_filling_data['foreground'] : $value['filling']['foreground'];
				
			}else if('custom' === $key && $color_palette_template === $key){
				$tf_brand_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-{$key}-brand" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-{$key}-brand" ) ) : [];
				$tf_text_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-{$key}-text" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-{$key}-text" ) ) : [];
				$tf_border_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-{$key}-border" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-{$key}-border" ) ) : [];
				$tf_filling_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-{$key}-filling" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-{$key}-filling" ) ) : [];

				$tf_brand_default = ! empty( $tf_brand_data['default'] ) ? $tf_brand_data['default'] : '';
				$tf_brand_dark = ! empty( $tf_brand_data['dark'] ) ? $tf_brand_data['dark'] : '';
				$tf_brand_lite = ! empty( $tf_brand_data['lite'] ) ? $tf_brand_data['lite'] : '';
				$tf_text_heading = ! empty( $tf_text_data['heading'] ) ? $tf_text_data['heading'] : '';
				$tf_text_paragraph = ! empty( $tf_text_data['paragraph'] ) ? $tf_text_data['paragraph'] : '';
				$tf_text_lite = ! empty( $tf_text_data['lite'] ) ? $tf_text_data['lite'] : '';
				$tf_border_default = ! empty( $tf_border_data['default'] ) ? $tf_border_data['default'] : '';
				$tf_border_lite = ! empty( $tf_border_data['lite'] ) ? $tf_border_data['lite'] : '';
				$tf_filling_background = ! empty( $tf_filling_data['background'] ) ? $tf_filling_data['background'] : '';
				$tf_filling_foreground = ! empty( $tf_filling_data['foreground'] ) ? $tf_filling_data['foreground'] : '';
			}
		}

		//container
		if($tf_container == 'full-width'){
			$tf_container_width = '100%';
		}
		
		$base_font_size = apply_filters('tf_base_font_size', '16px');
		$output = "
			:root {
				--tf-primary: {$tf_brand_default};
				--tf-brand-dark: {$tf_brand_dark};
				--tf-brand-lite: {$tf_brand_lite};
				--tf-text-heading: {$tf_text_heading};
				--tf-text-paragraph: {$tf_text_paragraph};
				--tf-text-lite: {$tf_text_lite};
				--tf-border-default: {$tf_border_default};
				--tf-border-lite: {$tf_border_lite};
				--tf-filling-background: {$tf_filling_background};
				--tf-filling-foreground: {$tf_filling_foreground};
				--tf-base-font-size: " . esc_attr($base_font_size) . ";
				--tf-container-width: " . esc_attr($tf_container_width) . ";
			}
		";

		if (wp_style_is('tf-app-style', 'enqueued')) {
			wp_add_inline_style('tf-app-style', apply_filters('tf-global-css', $output));
		}
	}

	function tf_required_taxonomies( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
			return;
		}

		global $post_type;

		$tf_is_gutenberg_active = Helper::tf_is_gutenberg_active();

		$default_post_types = array(
			'tf_hotel'     => array(
				'hotel_location' => array(
					'message' => __( 'Please select a location before publishing this hotel', 'tourfic' )
				)
			),
			'tf_tours'     => array(
				'tour_destination' => array(
					'message' => __( 'Please select a destination before publishing this tour', 'tourfic' )
				)
			),
			'tf_apartment' => array(
				'apartment_location' => array(
					'message' => __( 'Please select a location before publishing this apartment', 'tourfic' )
				)
			)
		);

		$post_types = apply_filters( 'tf_post_types', $default_post_types );

		if ( ! is_array( $post_types ) ) {
			return;
		}

		if ( ! isset( $post_types[ $post_type ] ) ) {
			return;
		}

		if ( ! isset( $post_types[ $post_type ] ) || ! is_array( $post_types[ $post_type ] ) || empty( $post_types[ $post_type ] ) ) {
			if ( is_string( $post_types[ $post_type ] ) ) {
				$post_types[ $post_type ] = array(
					'taxonomies' => array(
						$post_types[ $post_type ]
					)
				);
			} else if ( is_array( $post_types[ $post_type ] ) ) {
				$post_types[ $post_type ] = array(
					'taxonomies' => $post_types[ $post_type ]
				);
			} else {
				return;
			}
		}

		$post_type_taxonomies = get_object_taxonomies( $post_type );

		foreach ( $post_types[ $post_type ] as $taxonomy => $config ) {
			if ( is_int( $taxonomy ) && is_string( $config ) ) {
				unset( $post_types[ $post_type ][ $taxonomy ] );
				$taxonomy = $config;

				$post_types[ $post_type ][ $taxonomy ] = $config = array();
			}

			if ( ! taxonomy_exists( $taxonomy ) || ! in_array( $taxonomy, $post_type_taxonomies ) ) {
				unset( $post_types[ $post_type ][ $taxonomy ] );
				continue;
			}

			$taxonomy_object = get_taxonomy( $taxonomy );
			$taxonomy_labels = get_taxonomy_labels( $taxonomy_object );

			$post_types[ $post_type ][ $taxonomy ]['type'] = $config['type'] = ( is_taxonomy_hierarchical( $taxonomy ) ? 'hierarchical' : 'non-hierarchical' );

			if ( ! isset( $config['message'] ) || $taxonomy === $config ) {
				$post_type_labels = get_post_type_labels( get_post_type_object( $post_type ) );
				/* translators: %s taxonomy singular name, translators: %s: post type singular name */
				$config['message'] = sprintf( __( 'Please choose at least one %1$s before publishing this %2$s.', 'tourfic' ), $taxonomy_labels->singular_name, $post_type_labels->singular_name );
			}

			$post_types[ $post_type ][ $taxonomy ]['message'] = $config['message'];

			if ( $tf_is_gutenberg_active && ! empty( $taxonomy_object->rest_base ) && $taxonomy !== $taxonomy_object->rest_base ) {
				$post_types[ $post_type ][ $taxonomy_object->rest_base ] = $post_types[ $post_type ][ $taxonomy ];
				unset( $post_types[ $post_type ][ $taxonomy ] );
			}
		}

		if ( empty( $post_types[ $post_type ] ) ) {
			return;
		}

		wp_localize_script( 'tf-admin', 'tf_admin_params', array(
			'taxonomies'                       => $post_types[ $post_type ],
			'error'                            => false,
			'tf_nonce'                         => wp_create_nonce( 'updates' ),
			'ajax_url'                         => admin_url( 'admin-ajax.php' ),
			'deleting_old_review_fields'       => __( 'Deleting old review fields...', 'tourfic' ),
			'deleting_room_order_ids'          => __( 'Deleting order ids...', 'tourfic' ),
			'tour_location_required'           => __( 'Tour Location is a required field!', 'tourfic' ),
			'hotel_location_required'          => __( 'Hotel Location is a required field!', 'tourfic' ),
			'apartment_location_required'      => __( 'Apartment Location is a required field!', 'tourfic' ),
			'installing'                       => __( 'Installing...', 'tourfic' ),
			'activating'                       => __( 'Activating...', 'tourfic' ),
			'installed'                        => __( 'Installed', 'tourfic' ),
			'activated'                        => __( 'Activated', 'tourfic' ),
			'install_failed'                   => __( 'Install failed', 'tourfic' ),
			'i18n'                             => array(
				'no_services_selected' => __( 'Please select at least one service.', 'tourfic' ),
			)
		) );

	}



}