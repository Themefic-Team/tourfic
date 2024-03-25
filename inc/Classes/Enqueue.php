<?php

namespace Tourfic\Classes;
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Apartment\Pricing as ApartmentPricing;

class Enqueue {
	use \Tourfic\Traits\Singleton;
	use \Tourfic\Traits\Helper;

	public function __construct() {
		add_filter( 'wp_enqueue_scripts', array( $this, 'tf_dequeue_scripts' ), 9999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'tf_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'tf_enqueue_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'tf_dequeue_theplus_script_on_settings_page' ), 9999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'tf_custom_css' ), 99999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'tf_hotel_css' ), 99999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'tf_tour_css' ), 99999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'tf_apartment_css' ), 99999 );
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

		$flatpickr_cdn    = ! empty( self::tfopt( 'ftpr_cdn' ) ) ? self::tfopt( 'ftpr_cdn' ) : false;
		$flatpickr_locale = ! empty( get_locale() ) ? get_locale() : 'en_US';
		$allowed_locale   = array( 'ar', 'bn_BD', 'de_DE', 'es_ES', 'fr_FR', 'hi_IN', 'it_IT', 'nl_NL', 'ru_RU', 'zh_CN' );
		$fancy_cdn        = ! empty( self::tfopt( 'fnybx_cdn' ) ) ? self::tfopt( 'fnybx_cdn' ) : false;
		$slick_cdn        = ! empty( self::tfopt( 'slick_cdn' ) ) ? self::tfopt( 'slick_cdn' ) : false;
		$fa_cdn           = ! empty( self::tfopt( 'fa_cdn' ) ) ? self::tfopt( 'fa_cdn' ) : false;
		$min_css          = ! empty( self::tfopt( 'css_min' ) ) ? '.min' : '';
		$min_js           = ! empty( self::tfopt( 'js_min' ) ) ? '.min' : '';

		//Updated CSS
		wp_enqueue_style( 'tf-app-style', TF_ASSETS_URL . 'app/css/tourfic-style' . $min_css . '.css', null, TF_VERSION );
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
				$showxaxis           = ! empty( self::tf_data_types( self::tfopt( 'itinerary-builder-setings' ) )['itinerary-x-axis'] ) ? self::tf_data_types( self::tfopt( 'itinerary-builder-setings' ) )['itinerary-x-axis'] : false;
				$showyaxis           = ! empty( self::tf_data_types( self::tfopt( 'itinerary-builder-setings' ) )['itinerary-y-axis'] ) ? self::tf_data_types( self::tfopt( 'itinerary-builder-setings' ) )['itinerary-y-axis'] : false;
				$showlinegraph       = ! empty( self::tf_data_types( self::tfopt( 'itinerary-builder-setings' ) )['itinerary-line-graph'] ) ? self::tf_data_types( self::tfopt( 'itinerary-builder-setings' ) )['itinerary-line-graph'] : false;
				$showitinerarychart  = ! empty( self::tf_data_types( self::tfopt( 'itinerary-builder-setings' ) )['itinerary-chart'] ) ? self::tf_data_types( self::tfopt( 'itinerary-builder-setings' ) )['itinerary-chart'] : false;
				$showitinerarystatus = ! empty( self::tf_data_types( self::tfopt( 'itinerary-builder-setings' ) )['itinerary-status'] ) ? self::tf_data_types( self::tfopt( 'itinerary-builder-setings' ) )['itinerary-status'] : false;
				$elevvationmode      = ! empty( self::tf_data_types( self::tfopt( 'itinerary-builder-setings' ) )['elevtion_type'] ) && self::tf_data_types( self::tfopt( 'itinerary-builder-setings' ) )['elevtion_type'] == "Feet" ? "Feet" : "Meter";
			}
		}

		/**
		 * Flatpickr
		 * v4.6.13
		 */
		if ( $flatpickr_cdn ) {
			wp_enqueue_style( 'flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css', array(), TF_VERSION );
			wp_enqueue_script( 'flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js', array( 'jquery' ), TF_VERSION, true );
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
			wp_enqueue_style( 'fancyBox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css', array(), TF_VERSION );
			wp_enqueue_script( 'fancyBox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js', array( 'jquery' ), TF_VERSION, true );
		} else {
			wp_enqueue_style( 'fancybox', TF_ASSETS_URL . 'app/libs/fancybox/jquery.fancybox.min.css', '', TF_VERSION );
			wp_enqueue_script( 'fancybox', TF_ASSETS_URL . 'app/libs/fancybox/jquery.fancybox.min.js', array( 'jquery' ), TF_VERSION, true );
		}

		/**
		 * Slick
		 * v1.8.1
		 */
		if ( $slick_cdn ) {
			wp_enqueue_style( 'slick', '//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', array(), TF_VERSION );
			wp_enqueue_script( 'slick', '//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array( 'jquery' ), TF_VERSION, true );
		} else {
			wp_enqueue_style( 'tf-slick', TF_ASSETS_URL . 'app/libs/slick/slick.css', '', TF_VERSION );
			wp_enqueue_script( 'tf-slick', TF_ASSETS_URL . 'app/libs/slick/slick.min.js', array( 'jquery' ), TF_VERSION, true );
		}

		/**
		 * Font Awesome Free
		 * v5.15.4
		 */
		if ( $fa_cdn ) {
			wp_enqueue_style( 'font-awesome-5', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', '', TF_VERSION );
		} else {
			wp_enqueue_style( 'font-awesome-5', TF_ASSETS_URL . 'app/libs/font-awesome/css/all.min.css', '', TF_VERSION );
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
		$tf_openstreet_map = ! empty( self::tfopt( 'google-page-option' ) ) ? self::tfopt( 'google-page-option' ) : "default";
		if ( $tf_openstreet_map == "default" ) {
			wp_enqueue_script( 'tf-leaflet', esc_url( '//cdn.jsdelivr.net/npm/leaflet@' . '1.9' . '/dist/leaflet.js' ), array(), '1.9' );
			wp_enqueue_style( 'tf-leaflet', esc_url( '//cdn.jsdelivr.net/npm/leaflet@' . '1.9' . '/dist/leaflet.css' ), array(), '1.9' );
		}

		/**
		 * Hotel Min and Max Price
		 */
		$tfhotel_min_max       = array(
			'posts_per_page' => - 1,
			'post_type'      => 'tf_hotel',
			'post_status'    => 'publish'
		);
		$tfhotel_min_max_query = new \WP_Query( $tfhotel_min_max );
		$tfhotel_min_maxprices = array();

		if ( $tfhotel_min_max_query->have_posts() ):
			while ( $tfhotel_min_max_query->have_posts() ) : $tfhotel_min_max_query->the_post();

				$meta  = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
				$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
				if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
					$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $rooms );
					$rooms                = unserialize( $tf_hotel_rooms_value );
				}
				if ( ! empty( $rooms ) ) {
					foreach ( $rooms as $singleroom ) {
						if ( ! empty( $singleroom['price'] ) ) {
							$tfhotel_min_maxprices[] = $singleroom['price'];
						}
						if ( ! empty( $singleroom['adult_price'] ) ) {
							$tfhotel_min_maxprices[] = $singleroom['adult_price'];
						}
						if ( ! empty( $singleroom['child_price'] ) ) {
							$tfhotel_min_maxprices[] = $singleroom['child_price'];
						}
						if ( ! empty( $singleroom['avail_date'] ) ) {
							$avail_date = json_decode( $singleroom['avail_date'], true );
							if ( ! empty( $avail_date ) && is_array( $avail_date ) ) {
								foreach ( $avail_date as $singleavailroom ) {
									if ( ! empty( $singleavailroom['price'] ) ) {
										$tfhotel_min_maxprices[] = $singleavailroom['price'];
									}
									if ( ! empty( $singleavailroom['adult_price'] ) ) {
										$tfhotel_min_maxprices[] = $singleavailroom['adult_price'];
									}
									if ( ! empty( $singleavailroom['child_price'] ) ) {
										$tfhotel_min_maxprices[] = $singleavailroom['child_price'];
									}
								}
							}
						}
					}
				}

			endwhile;
		endif;
		wp_reset_query();
		if ( ! empty( $tfhotel_min_maxprices ) && count( $tfhotel_min_maxprices ) > 1 ) {
			$hotel_max_price_val = max( $tfhotel_min_maxprices );
			$hotel_min_price_val = min( $tfhotel_min_maxprices );
			if ( $hotel_max_price_val == $hotel_min_price_val ) {
				$hotel_max_price = max( $tfhotel_min_maxprices );
				$hotel_min_price = 1;
			} else {
				$hotel_max_price = max( $tfhotel_min_maxprices );
				$hotel_min_price = min( $tfhotel_min_maxprices );
			}
		}
		if ( ! empty( $tfhotel_min_maxprices ) && count( $tfhotel_min_maxprices ) == 1 ) {
			$hotel_max_price = max( $tfhotel_min_maxprices );
			$hotel_min_price = 1;
		}
		if ( empty( $tfhotel_min_maxprices ) ) {
			$hotel_max_price = 0;
			$hotel_min_price = 0;
		}

		/**
		 * Tour Min and Max Price
		 */
		$tftours_min_max = array(
			'posts_per_page' => - 1,
			'post_type'      => 'tf_tours',
			'post_status'    => 'publish'
		);

		$tftours_min_max_query = new \WP_Query( $tftours_min_max );
		$tftours_min_maxprices = array();

		if ( $tftours_min_max_query->have_posts() ):
			while ( $tftours_min_max_query->have_posts() ) : $tftours_min_max_query->the_post();

				$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
				if ( ! empty( $meta['adult_price'] ) ) {
					$tftours_min_maxprices[] = $meta['adult_price'];
				}
				if ( ! empty( $meta['child_price'] ) ) {
					$tftours_min_maxprices[] = $meta['child_price'];
				}
				if ( ! empty( $meta['infant_price'] ) ) {
					$tftours_min_maxprices[] = $meta['infant_price'];
				}
				if ( ! empty( $meta['group_price'] ) ) {
					$tftours_min_maxprices[] = $meta['group_price'];
				}
				if ( ! empty( $meta['cont_custom_date'] ) ) {
					foreach ( $meta['cont_custom_date'] as $minmax ) {
						if ( ! empty( $minmax['adult_price'] ) ) {
							$tftours_min_maxprices[] = $minmax['adult_price'];
						}
						if ( ! empty( $minmax['child_price'] ) ) {
							$tftours_min_maxprices[] = $minmax['child_price'];
						}
						if ( ! empty( $minmax['infant_price'] ) ) {
							$tftours_min_maxprices[] = $minmax['infant_price'];
						}
						if ( ! empty( $minmax['group_price'] ) ) {
							$tftours_min_maxprices[] = $minmax['group_price'];
						}
					}
				}
			endwhile;

		endif;
		wp_reset_query();
		if ( ! empty( $tftours_min_maxprices ) && count( $tftours_min_maxprices ) > 1 ) {
			$tour_max_price_val = max( $tftours_min_maxprices );
			$tour_min_price_val = min( $tftours_min_maxprices );
			if ( $tour_max_price_val == $tour_min_price_val ) {
				$tour_max_price = max( $tftours_min_maxprices );
				$tour_min_price = 1;
			} else {
				$tour_max_price = max( $tftours_min_maxprices );
				$tour_min_price = min( $tftours_min_maxprices );
			}
		}
		if ( ! empty( $tftours_min_maxprices ) && count( $tftours_min_maxprices ) == 1 ) {
			$tour_max_price = max( $tftours_min_maxprices );
			$tour_min_price = 1;
		}
		if ( empty( $tftours_min_maxprices ) ) {
			$tour_max_price = 0;
			$tour_min_price = 0;
		}

		$tf_apartment_min_max_price = ApartmentPricing::get_apartment_min_max_price();

		/**
		 * Custom
		 */
		wp_enqueue_script( 'tourfic', TF_APP_ASSETS_URL . 'js/tourfic-scripts' . $min_js . '.js', '', TF_VERSION, true );
		wp_localize_script( 'tourfic', 'tf_params',
			array(
				'nonce'                  => wp_create_nonce( 'tf_ajax_nonce' ),
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'single'                 => is_single(),
				'locations'              => Helper::get_terms_dropdown('hotel_location'),
				'apartment_locations'    => Helper::get_terms_dropdown('apartment_location'),
				'tour_destinations'      => Helper::get_terms_dropdown('tour_destination'),
				'ajax_result_success'    => __( 'Refreshed Successfully!', 'tourfic' ),
				'wishlist_add'           => __( 'Adding to wishlist...', 'tourfic' ),
				'wishlist_added'         => __( 'Item added to wishlist.', 'tourfic' ),
				'wishlist_add_error'     => __( 'Failed to add in wishlist!', 'tourfic' ),
				'wishlist_removed'       => __( 'Item removed from wishlist', 'tourfic' ),
				'wishlist_remove_error'  => __( 'Failed to remove from wishlist!', 'tourfic' ),
				'field_required'         => __( 'This field is required!', 'tourfic' ),
				'adult'                  => __( 'Adult', 'tourfic' ),
				'children'               => __( 'Children', 'tourfic' ),
				'infant'                 => __( 'Infant', 'tourfic' ),
				'room'                   => __( 'Room', 'tourfic' ),
				'sending_ques'           => __( 'Sending your question...', 'tourfic' ),
				'no_found'               => __( 'Not Found', 'tourfic' ),
				'tf_hotel_max_price'     => isset( $hotel_max_price ) ? $hotel_max_price : '',
				'tf_hotel_min_price'     => isset( $hotel_min_price ) ? $hotel_min_price : '',
				'tf_tour_max_price'      => isset( $tour_max_price ) ? $tour_max_price : '',
				'tf_tour_min_price'      => isset( $tour_min_price ) ? $tour_min_price : '',
				'itinerarayday'          => isset( $itinerarayday ) ? $itinerarayday : '',
				'itineraraymeter'        => isset( $itineraraymeter ) ? $itineraraymeter : '',
				'showxaxis'              => isset( $showxaxis ) ? $showxaxis : '',
				'showyaxis'              => isset( $showyaxis ) ? $showyaxis : '',
				'showlinegraph'          => isset( $showlinegraph ) ? $showlinegraph : '',
				'elevvationmode'         => isset( $elevvationmode ) ? $elevvationmode : '',
				'showitinerarychart'     => isset( $showitinerarychart ) ? $showitinerarychart : '',
				'showitinerarystatus'    => isset( $showitinerarystatus ) ? $showitinerarystatus : '',
				'date_hotel_search'      => self::tfopt( 'date_hotel_search' ),
				'date_tour_search'       => self::tfopt( 'date_tour_search' ),
				'date_apartment_search'  => self::tfopt( 'date_apartment_search' ),
				'tf_apartment_max_price' => isset( $tf_apartment_min_max_price ) ? $tf_apartment_min_max_price['max'] : 0,
				'tf_apartment_min_price' => isset( $tf_apartment_min_max_price ) ? $tf_apartment_min_max_price['min'] : 0,
			)
		);

		/**
		 * Inline scripts
		 */
		// Get single tour meta data
		global $post;
		if ( ! is_404() && ! empty( $post ) ) {
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
	function tf_enqueue_admin_scripts( $hook ) {

		/**
		 * Notyf
		 * v3.0
		 */
		wp_enqueue_style( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.css', '', TF_VERSION );
		wp_enqueue_script( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.js', array( 'jquery' ), TF_VERSION, true );

		if ( $hook == "widgets.php" && function_exists( 'is_woocommerce' ) ) {

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
			}
		}
	}

	function tf_custom_css() {
		// Store as PHP variables
		// Template 1 Global CSS
		$tf_template1_global_reg   = ! empty( self::tf_data_types( self::tfopt( 'tourfic-design1-global-color' ) )['gcolor'] ) ? self::tf_data_types( self::tfopt( 'tourfic-design1-global-color' ) )['gcolor'] : '';
		$tf_template1_p_global_reg = ! empty( self::tf_data_types( self::tfopt( 'tourfic-design1-p-global-color' ) )['pgcolor'] ) ? self::tf_data_types( self::tfopt( 'tourfic-design1-p-global-color' ) )['pgcolor'] : '';

		// Common CSS
		$tf_primary_color_reg        = ! empty( self::tf_data_types( self::tfopt( 'tourfic-button-color' ) )['regular'] ) ? self::tf_data_types( self::tfopt( 'tourfic-button-color' ) )['regular'] : '';
		$tf_primary_color_hov        = ! empty( self::tf_data_types( self::tfopt( 'tourfic-button-color' ) )['hover'] ) ? self::tf_data_types( self::tfopt( 'tourfic-button-color' ) )['hover'] : '';
		$tf_primary_bg_color_reg     = ! empty( self::tf_data_types( self::tfopt( 'tourfic-button-bg-color' ) )['regular'] ) ? self::tf_data_types( self::tfopt( 'tourfic-button-bg-color' ) )['regular'] : '';
		$tf_primary_bg_color_hov     = ! empty( self::tf_data_types( self::tfopt( 'tourfic-button-bg-color' ) )['hover'] ) ? self::tf_data_types( self::tfopt( 'tourfic-button-bg-color' ) )['hover'] : '';
		$tf_sidebar_gradient_one_reg = ! empty( self::tf_data_types( self::tfopt( 'tourfic-sidebar-booking' ) )['gradient_one_reg'] ) ? self::tf_data_types( self::tfopt( 'tourfic-sidebar-booking' ) )['gradient_one_reg'] : '';
		$tf_sidebar_gradient_two_reg = ! empty( self::tf_data_types( self::tfopt( 'tourfic-sidebar-booking' ) )['gradient_two_reg'] ) ? self::tf_data_types( self::tfopt( 'tourfic-sidebar-booking' ) )['gradient_two_reg'] : '';
		$tf_faq_color                = ! empty( self::tf_data_types( self::tfopt( 'tourfic-faq-style' ) )['faq_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-faq-style' ) )['faq_color'] : '';
		$tf_faq_icon_color           = ! empty( self::tf_data_types( self::tfopt( 'tourfic-faq-style' ) )['faq_icon_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-faq-style' ) )['faq_icon_color'] : '';
		$tf_faq_border_color         = ! empty( self::tf_data_types( self::tfopt( 'tourfic-faq-style' ) )['faq_border_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-faq-style' ) )['faq_border_color'] : '';
		$tf_rating_color             = ! empty( self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['rating_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['rating_color'] : '';
		$tf_rating_bg_color          = ! empty( self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['rating_bg_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['rating_bg_color'] : '';
		$tf_param_bg_color           = ! empty( self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['param_bg_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['param_bg_color'] : '';
		$tf_param_txt_color          = ! empty( self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['param_txt_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['param_txt_color'] : '';
		$tf_param_single_bg_color    = ! empty( self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['param_single_bg_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['param_single_bg_color'] : '';
		$tf_review_color             = ! empty( self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['review_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['review_color'] : '';
		$tf_review_bg_color          = ! empty( self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['review_bg_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-review-style' ) )['review_bg_color'] : '';

		// Global Font Family
		$tf_global_font_family         = self::tfopt( 'global-body-fonts-family' ) ? str_replace( '_', ' ', self::tfopt( 'global-body-fonts-family' ) ) : 'Default';
		$tf_global_heading_font_family = self::tfopt( 'global-heading-fonts-family' ) ? str_replace( '_', ' ', self::tfopt( 'global-heading-fonts-family' ) ) : 'Default';

		// Global Typography P
		$tf_global_font_p        = self::tfopt( 'global-p' ) ? self::tfopt( 'global-p' ) : 16;
		$tf_global_font_weight_p = self::tfopt( 'global-p-weight' ) ? self::tfopt( 'global-p-weight' ) : 400;
		$tf_global_font_style_p  = self::tfopt( 'global-p-style' ) ? self::tfopt( 'global-p-style' ) : 'normal';
		$tf_global_line_height_p = self::tfopt( 'global-p-line-height' ) ? self::tfopt( 'global-p-line-height' ) : 1.5;

		// Global Typography H1
		$tf_global_font_h1        = self::tfopt( 'global-h1' ) ? self::tfopt( 'global-h1' ) : 38;
		$tf_global_font_weight_h1 = self::tfopt( 'global-h1-weight' ) ? self::tfopt( 'global-h1-weight' ) : 500;
		$tf_global_font_style_h1  = self::tfopt( 'global-h1-style' ) ? self::tfopt( 'global-h1-style' ) : 'normal';
		$tf_global_line_height_h1 = self::tfopt( 'global-h1-line-height' ) ? self::tfopt( 'global-h1-line-height' ) : 1.2;

		// Global Typography H2
		$tf_global_font_h2        = self::tfopt( 'global-h2' ) ? self::tfopt( 'global-h2' ) : 30;
		$tf_global_font_weight_h2 = self::tfopt( 'global-h2-weight' ) ? self::tfopt( 'global-h2-weight' ) : 500;
		$tf_global_font_style_h2  = self::tfopt( 'global-h2-style' ) ? self::tfopt( 'global-h2-style' ) : 'normal';
		$tf_global_line_height_h2 = self::tfopt( 'global-h2-line-height' ) ? self::tfopt( 'global-h2-line-height' ) : 1.2;

		// Global Typography H3
		$tf_global_font_h3        = self::tfopt( 'global-h3' ) ? self::tfopt( 'global-h3' ) : 24;
		$tf_global_font_weight_h3 = self::tfopt( 'global-h3-weight' ) ? self::tfopt( 'global-h3-weight' ) : 500;
		$tf_global_font_style_h3  = self::tfopt( 'global-h3-style' ) ? self::tfopt( 'global-h3-style' ) : 'normal';
		$tf_global_line_height_h3 = self::tfopt( 'global-h3-line-height' ) ? self::tfopt( 'global-h3-line-height' ) : 1.2;

		// Global Typography H4
		$tf_global_font_h4        = self::tfopt( 'global-h4' ) ? self::tfopt( 'global-h4' ) : 20;
		$tf_global_font_weight_h4 = self::tfopt( 'global-h4-weight' ) ? self::tfopt( 'global-h4-weight' ) : 500;
		$tf_global_font_style_h4  = self::tfopt( 'global-h4-style' ) ? self::tfopt( 'global-h4-style' ) : 'normal';
		$tf_global_line_height_h4 = self::tfopt( 'global-h4-line-height' ) ? self::tfopt( 'global-h4-line-height' ) : 1.2;

		// Global Typography H5
		$tf_global_font_h5        = self::tfopt( 'global-h5' ) ? self::tfopt( 'global-h5' ) : 18;
		$tf_global_font_weight_h5 = self::tfopt( 'global-h5-weight' ) ? self::tfopt( 'global-h5-weight' ) : 500;
		$tf_global_font_style_h5  = self::tfopt( 'global-h5-style' ) ? self::tfopt( 'global-h5-style' ) : 'normal';
		$tf_global_line_height_h5 = self::tfopt( 'global-h5-line-height' ) ? self::tfopt( 'global-h5-line-height' ) : 1.2;

		// Global Typography H6
		$tf_global_font_h6        = self::tfopt( 'global-h6' ) ? self::tfopt( 'global-h6' ) : 14;
		$tf_global_font_weight_h6 = self::tfopt( 'global-h6-weight' ) ? self::tfopt( 'global-h6-weight' ) : 500;
		$tf_global_font_style_h6  = self::tfopt( 'global-h6-style' ) ? self::tfopt( 'global-h6-style' ) : 'normal';
		$tf_global_line_height_h6 = self::tfopt( 'global-h6-line-height' ) ? self::tfopt( 'global-h6-line-height' ) : 1.2;

		// Button
		$tf_global_button_size        = self::tfopt( 'button-font-size' ) ? self::tfopt( 'button-font-size' ) : 14;
		$tf_global_button_line_height = self::tfopt( 'button-line-height' ) ? self::tfopt( 'button-line-height' ) : 1.2;

		// Template 3 Global Settings
		$tf_global_bg_clr_t3        = ! empty( self::tf_data_types( self::tfopt( 'tourfic-template3-bg' ) )['template3-bg'] ) ? self::tf_data_types( self::tfopt( 'tourfic-template3-bg' ) )['template3-bg'] : '';
		$tf_global_highlight_clr_t3 = ! empty( self::tf_data_types( self::tfopt( 'tourfic-template3-bg' ) )["template3-highlight"] ) ? self::tf_data_types( self::tfopt( 'tourfic-template3-bg' ) )["template3-highlight"] : '';
		$tf_global_icon_clr_t3      = ! empty( self::tf_data_types( self::tfopt( 'tourfic-template3-bg' ) )["template3-icon-color"] ) ? self::tf_data_types( self::tfopt( 'tourfic-template3-bg' ) )["template3-icon-color"] : '';

		$output = '';

		// Template 1 Global CSS
		if ( ! empty( $tf_template1_global_reg ) ) {
			$output .= '
			.tf-template-global .tf-bttn-normal.bttn-primary,
			.tf-template-global .tf-archive-head .active,
			.tf-template-global .tf-item-featured .tf-features-box .tf-featur,
			.tf-template-global .tf-itinerary-wrapper .tf-single-itinerary-item.active .accordion-checke::after,
			.tf-template-global .tf-itinerary-wrapper.tf-mb-50 .tf-itinerary-downloader-option,
			.tf-template-global .tf-rooms-sections .tf-rooms .tf-availability-table>thead,
			.tf-template-global .tf-hotel-location-map .tf-hotel-location-preview a i,
			.tf-template-global .tf-search-results-list .tf-item-card .tf-item-featured .tf-features-box .tf-feature,
			.tf-template-global .tf-review-wrapper .tf-review-form .tf-review-form-container .tf-review-submit input[type="submit"],
			.tf-template-global .tf-archive-right .tf_widget .al-range-slider__knob,
			.tf-template-global .tf-search-results-list #tf_posts_navigation_bar .page-numbers.current,
			.tf-template-global .tf-archive-right .tf_widget .al-range-slider__tooltip,
			.tf-template-global .tf-archive-right .tf_widget .al-range-slider_dark .al-range-slider__bar,
			.tf-template-global .tf-btn .btn-primary,
			.tf-hotel-design-1 .tf-rooms-sections .tf-rooms .tf-availability-table>tbody tr td .hotel-room-book,
			.tf-template-global .tf-archive-right .tf-booking-bttns button,
			.tf-hotel-services-wrap.tf-hotel-service-design-1 .tf_button_group button,
			.tf-tours-booking-deposit.tf-tours-booking-design-1 .tf_button_group button{
				background: ' . $tf_template1_global_reg . ' !important;
			}';
		}
		if ( ! empty( $tf_global_font_family ) && $tf_global_font_family != "Default" ) {
			$output .= '
			.tf-container-inner,
			.tf-main-wrapper,
			.tf-container,
			#tour_room_details_qv,
			#tf-hotel-services,
			#tf-hotel-services span,
			#tf-hotel-services select,
			#tour-deposit,
			#tour-deposit .tf_button_group button,
			#tf-hotel-services .tf_button_group button,
			#tf-ask-question button,
			#tf-ask-question input,
			#tf-ask-question textarea,
			.tf-container button,
			.tf-container input,
			.tf-container textarea{
			.gm-style .marker-label,
			.tf-withoutpayment-booking{
				font-family: "' . $tf_global_font_family . '", sans-serif !important;
			}';
		}
		if ( ! empty( $tf_template1_p_global_reg ) ) {
			$output .= '
			.tf-container-inner p,
			.tf-main-wrapper p,
			.tf-container p,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-overview-description .tf-full-description,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-overview-description .tf-short-description,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-highlights-wrapper .ft-highlights-details p,
			.tf-template-3 .tf-bottom-booking-bar .tf-booking-form-fields .tf-booking-form-guest-and-room .tf-booking-form-title,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block .tf-feature-block-details p,
			.tf-template-3 .tf_tours_booking .tf-field-calander .tf-field,
			.tf-template-3 .tf-questions-wrapper .tf-questions .tf-questions-col .tf-question .tf-question-desc,
			.tf-template-3 .tf-policies-wrapper .tf-policies p,
			.tf-template-3 .tf-tour-contact-informations .tf-contact-details-items .tf-list li a,
			#tour_room_details_qv p{
				color: ' . $tf_template1_p_global_reg . '
			}';
		}

		if ( ! empty( $tf_global_heading_font_family ) && $tf_global_heading_font_family != "Default" ) {
			$output .= '
			.tf-container-inner h1,
			.tf-main-wrapper h1,
			.tf-container h1,
			.tf-container-inner h2,
			.tf-main-wrapper h2,
			.tf-container h2,
			.tf-container-inner h3,
			.tf-main-wrapper h3,
			.tf-container h3,
			#tour_room_details_qv h3,
			.tf-container-inner h4,
			.tf-main-wrapper h4,
			#tour_room_details_qv h4,
			.tf-container h4,
			#tf-ask-question h4,
			.tf-container-inner h5,
			.tf-main-wrapper h5,
			.tf-container h5,
			.tf-container-inner h6,
			.tf-main-wrapper h6,
			.tf-container h6{
				font-family: "' . $tf_global_heading_font_family . '", sans-serif !important;
			}';
		}

		// Global typo for P
		if ( ! empty( $tf_global_font_p ) || ! empty( $tf_global_font_weight_p ) || ! empty( $tf_global_font_style_p ) || ! empty( $tf_global_line_height_p ) ) {
			$output .= '
			.tf-container-inner p,
			.tf-main-wrapper p,
			#tour_room_details_qv p,
			.tf-container p,
			.hero-booking .tf_booking-widget{
				font-weight: ' . $tf_global_font_weight_p . ' !important;
				font-size: ' . $tf_global_font_p . 'px !important;
				font-style: ' . $tf_global_font_style_p . ' !important;
				line-height: ' . $tf_global_line_height_p . ' !important;
			}';
		}

		// Global typo for H1
		if ( ! empty( $tf_global_font_h1 ) || ! empty( $tf_global_font_weight_h1 ) || ! empty( $tf_global_font_style_h1 ) || ! empty( $tf_global_line_height_h1 ) ) {
			$output .= '
			.tf-container-inner h1,
			.tf-main-wrapper h1,
			.tf-container h1{
				font-weight: ' . $tf_global_font_weight_h1 . ' !important;
				font-size: ' . $tf_global_font_h1 . 'px !important;
				font-style: ' . $tf_global_font_style_h1 . ' !important;
				line-height: ' . $tf_global_line_height_h1 . ' !important;
			}';
		}

		// Global typo for H2
		if ( ! empty( $tf_global_font_h2 ) || ! empty( $tf_global_font_weight_h2 ) || ! empty( $tf_global_font_style_h2 ) || ! empty( $tf_global_line_height_h2 ) ) {
			$output .= '
			.tf-container-inner h2,
			.tf-main-wrapper h2,
			.tf-container h2{
				font-weight: ' . $tf_global_font_weight_h2 . ' !important;
				font-size: ' . $tf_global_font_h2 . 'px !important;
				font-style: ' . $tf_global_font_style_h2 . ' !important;
				line-height: ' . $tf_global_line_height_h2 . ' !important;
			}';
		}

		// Global typo for H3
		if ( ! empty( $tf_global_font_h3 ) || ! empty( $tf_global_font_weight_h3 ) || ! empty( $tf_global_font_style_h3 ) || ! empty( $tf_global_line_height_h3 ) ) {
			$output .= '
			.tf-container-inner h3,
			.tf-main-wrapper h3,
			#tour_room_details_qv h3,
			.tf-container h3{
				font-weight: ' . $tf_global_font_weight_h3 . ' !important;
				font-size: ' . $tf_global_font_h3 . 'px !important;
				font-style: ' . $tf_global_font_style_h3 . ' !important;
				line-height: ' . $tf_global_line_height_h3 . ' !important;
			}';
		}

		// Global typo for H4
		if ( ! empty( $tf_global_font_h4 ) || ! empty( $tf_global_font_weight_h4 ) || ! empty( $tf_global_font_style_h4 ) || ! empty( $tf_global_line_height_h4 ) ) {
			$output .= '
			.tf-container-inner h4,
			.tf-main-wrapper h4,
			#tf-ask-question h4,
			#tour_room_details_qv h4,
			.tf-container h4{
				font-weight: ' . $tf_global_font_weight_h4 . ' !important;
				font-size: ' . $tf_global_font_h4 . 'px !important;
				font-style: ' . $tf_global_font_style_h4 . ' !important;
				line-height: ' . $tf_global_line_height_h4 . ' !important;
			}';
		}

		// Global typo for H5
		if ( ! empty( $tf_global_font_h5 ) || ! empty( $tf_global_font_weight_h5 ) || ! empty( $tf_global_font_style_h5 ) || ! empty( $tf_global_line_height_h5 ) ) {
			$output .= '
			.tf-container-inner h5,
			.tf-main-wrapper h5,
			.tf-container h5{
				font-weight: ' . $tf_global_font_weight_h5 . ' !important;
				font-size: ' . $tf_global_font_h5 . 'px !important;
				font-style: ' . $tf_global_font_style_h5 . ' !important;
				line-height: ' . $tf_global_line_height_h5 . ' !important;
			}';
		}

		// Global typo for H6
		if ( ! empty( $tf_global_font_h6 ) || ! empty( $tf_global_font_weight_h6 ) || ! empty( $tf_global_font_style_h6 ) || ! empty( $tf_global_line_height_h6 ) ) {
			$output .= '
			.tf-container-inner h6,
			.tf-main-wrapper h6,
			.tf-container h6{
				font-weight: ' . $tf_global_font_weight_h6 . ' !important;
				font-size: ' . $tf_global_font_h6 . 'px !important;
				font-style: ' . $tf_global_font_style_h6 . ' !important;
				line-height: ' . $tf_global_line_height_h6 . ' !important;
			}';
		}

		// Global Button
		if ( ! empty( $tf_global_button_size ) || ! empty( $tf_global_button_line_height ) ) {
			$output .= '
			.tf-btn-normal,
			.btn-styled{
				font-size: ' . $tf_global_button_size . 'px !important;
				line-height: ' . $tf_global_button_line_height . ' !important;
			}';
		}

		if ( ! empty( $tf_template1_global_reg ) ) {
			$output .= '
			.tf-template-global .tf-archive-head i,
			.tf-template-global .tf-search-results-list .tf-item-card .tf-item-details .tf-post-footer .tf-pricing span,
			.tf-template-global .tf-single-head i,
			.tf-template-global .tf-trip-info li i,
			.tf-template-global .tf-trip-feature-blocks .tf-feature-block i,
			.tf-template-global .tf-tour-details-right .tf-tour-booking-advantages li i,
			.tf-template-global .tf-ask-enquiry i,
			.tf-template-global .tf-list li i,
			.tf-template-global .tf-itinerary-wrapper .tf-single-itinerary-item .itinerary-day,
			.tf-template-global .tf-itinerary-wrapper.tf-mrbottom-70 .tf-itinerary-downloader-option a,
			.tf-template-global .tf-review-wrapper .tf-review-data .tf-review-data-average p,
			.tf-template-global .tf-review-wrapper .tf-review-data .tf-review-all-info li,
			.tf-template-global .tf-single-head .more-hotel,
			.tf-template-global .tf-head-info .tf-dropdown-share-content h4,
			.tf-template-global .tf-head-info .tf-dropdown-share-content ul li button span,
			.tf-template-global .itinerary-downloader-right a,
			.tf-hotel-design-1 .tf-rooms-sections .tf-rooms .tf-availability-table>tbody tr td .tf-features-infos > a ,
			.upcomming-tours .tf-meta-data-price span,
			.upcomming-tours .tf-meta-location i,
			.tf-template-global .tf-search-results-list .tf-item-card .tf-item-details i,
			.tf-archive-design-1 .tf-archive-right #tf__booking_sidebar .widget .tf-filter a{
				color: ' . $tf_template1_global_reg . ' !important;
			}';
		}

		if ( ! empty( $tf_template1_global_reg ) ) {
			$output .= '
			.tf-template-global .tf-itinerary-wrapper .tf-single-itinerary-item.active .accordion-checke::before,
			.tf-template-global .tf-archive-right .tf_widget .al-range-slider__knob,
			.tf-tours-booking-deposit.tf-tours-booking-design-1 .tf_button_group button,
			.tf-template-global .tf-review-wrapper .tf-review-form .tf-review-form-container .tf-review-submit input[type="submit"]{
				border: 1px solid ' . $tf_template1_global_reg . ';
				border-color: ' . $tf_template1_global_reg . ' !important;
			}
			.tf-aq-outer span.close-aq {background: ' . $tf_template1_global_reg . ' !important;}
			.tf-aq-field .btn-styled {background: ' . $tf_template1_global_reg . ' !important;}';

		}

		// Common CSS
		if ( $tf_primary_color_reg ) {
			$output .= '
			.tf_button, 
			.tf-btn-flip:before, 
			.tf-btn-flip, 
			.btn-styled, 
			.tf-review-form-container .tf-review-submit input[type="submit"], 
			.tf-template-3 .tf-send-inquiry .tf-send-inquiry-btn,
			.tf-bttn-normal.bttn-primary, 
			.tf-bttn-normal.bttn-secondary, 
			.tf-template-global .tf-archive-head .active, 
			.tf-search-results-list .tf-item-card .tf-item-featured .tf-features-box .tf-feature,
			.tf-template-3 .tf_tours_booking .tf-tours-booking-btn.tf-booking-bttns a,
			.tf-template-3 .tf-booking-form-wrapper .tf-booking-form .tf-booking-form-submit button,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content a.availability,
			.tf-template-3 .tf-available-archive-hetels-wrapper .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-right a.view-hotel,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content button,
			.tf-template-3 .tf-review-form-wrapper .tf-review-form-container form .tf-review-submit input#comment-submit,
			.tf-template-3 .tf-mobile-booking-btn span,
			.tf-template-3 .tf-modify-search-btn,
			.tf_template_3_global_layouts #tf-ask-question button,
			.tf_template_3_global_layouts #tf-ask-question .tf-aq-outer .close-aq,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content a.tf_air_service,
			.tf_template_3_global_layouts #tf-hotel-services .tf-hotel-services .tf_button_group button:hover{
				color: ' . $tf_primary_color_reg . ' !important;
			}
			.tf-ask-question div i:before {
				color: ' . $tf_primary_color_reg . ' !important;
			}
		';
		}
		if ( $tf_primary_color_hov ) {
			$output .= '
			.tf_button:hover, 
			.btn-styled:hover, 
			.tf-btn-flip:after, 
			.tf-review-form-container .tf-review-submit input[type="submit"]:hover, 
			.tf-bttn-normal.bttn-primary:hover, 
			.tf-bttn-normal.bttn-secondary:hover,
			.tf-template-3 .tf_tours_booking .tf-tours-booking-btn.tf-booking-bttns a:hover,
			.tf-template-3 .tf-booking-form-wrapper .tf-booking-form .tf-booking-form-submit button:hover,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content a.availability:hover,
			.tf-template-3 .tf-send-inquiry .tf-send-inquiry-btn:hover,
			.tf-template-3 .tf-available-archive-hetels-wrapper .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-right a.view-hotel:hover,
			.tf-template-3 .tf-review-form-wrapper .tf-review-form-container form .tf-review-submit input#comment-submit:hover,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content button:hover,
			.tf-template-3 .tf-mobile-booking-btn span:hover,
			.tf-template-3 .tf-modify-search-btn:hover,
			.tf_template_3_global_layouts #tf-ask-question button:hover,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content a.tf_air_service:hover,
			.tf_template_3_global_layouts #tf-hotel-services .tf-hotel-services .tf_button_group button:hover {
				color: ' . $tf_primary_color_hov . ' !important;
			}
		';
		}
		if ( $tf_primary_bg_color_reg ) {
			$output .= '
			.tf_button, 
			.tf-btn-flip:before, 
			.tf-btn-flip:after, 
			.btn-styled, 
			.tf-review-form-container .tf-review-submit input[type="submit"], 
			.tf-template-3 .tf-send-inquiry .tf-send-inquiry-btn,
			.tf-bttn-normal.bttn-primary, 
			.tf-bttn-normal.bttn-secondary, 
			.tf-template-global .tf-archive-head .active, 
			.tf-search-results-list .tf-item-card .tf-item-featured .tf-features-box .tf-feature,
			.tf-template-3 .tf_tours_booking .tf-tours-booking-btn.tf-booking-bttns a,
			.tf-template-3 .tf-booking-form-wrapper .tf-booking-form .tf-booking-form-submit button,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content a.availability,
			.tf-template-3 .tf-available-archive-hetels-wrapper .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-right a.view-hotel,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content button,
			.tf-template-3 .tf-review-form-wrapper .tf-review-form-container form .tf-review-submit input#comment-submit,
			.tf-template-3 .tf-mobile-booking-btn,
			.tf-template-3 .tf-modify-search-btn,
			.tf_template_3_global_layouts #tf-ask-question button,
			.tf_template_3_global_layouts #tf-ask-question .tf-aq-outer .close-aq,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content a.tf_air_service,
			.tf_template_3_global_layouts #tf-hotel-services .tf-hotel-services .tf_button_group button {
				background: ' . $tf_primary_bg_color_reg . ' !important;
			}
			.tf_button, 
			.btn-styled, 
			.tf-review-form-container .tf-review-submit input[type="submit"],
			.tf-template-3 .tf_tours_booking .tf-tours-booking-btn.tf-booking-bttns a,
			.tf_template_3_global_layouts #tf-ask-question button,
			.tf_template_3_global_layouts #tf-hotel-services .tf-hotel-services .tf_button_group button {
				border-color: ' . $tf_primary_bg_color_reg . ' !important;
			}
		';
		}
		if ( $tf_primary_bg_color_hov ) {
			$output .= '
			.tf_button:hover, 
			.btn-styled:hover, 
			.tf-review-form-container 
			.tf-review-submit input[type="submit"]:hover, 
			.tf-bttn-normal.bttn-primary:hover, 
			.tf-bttn-normal.bttn-secondary:hover,
			.tf-template-3 .tf_tours_booking .tf-tours-booking-btn.tf-booking-bttns a:hover,
			.tf-template-3 .tf-booking-form-wrapper .tf-booking-form .tf-booking-form-submit button:hover,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content a.availability:hover,
			.tf-template-3 .tf-send-inquiry .tf-send-inquiry-btn:hover,
			.tf-template-3 .tf-available-archive-hetels-wrapper .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-right a.view-hotel:hover,
			.tf-template-3 .tf-review-form-wrapper .tf-review-form-container form .tf-review-submit input#comment-submit:hover,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content button:hover,
			.tf-template-3 .tf-mobile-booking-btn:hover,
			.tf-template-3 .tf-modify-search-btn:hover,
			.tf_template_3_global_layouts #tf-ask-question button:hover,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content a.tf_air_service:hover,
			.tf_template_3_global_layouts #tf-hotel-services .tf-hotel-services .tf_button_group button:hover {
				background: ' . $tf_primary_bg_color_hov . ' !important;
			}
			.tf_button:hover, 
			.btn-styled:hover, .tf-review-form-container .tf-review-submit input[type="submit"]:hover,
			.tf-template-3 .tf_tours_booking .tf-tours-booking-btn.tf-booking-bttns a:hover,
			.tf_template_3_global_layouts #tf-ask-question button:hover,
			.tf_template_3_global_layouts #tf-hotel-services .tf-hotel-services .tf_button_group button:hover{
				border-color: ' . $tf_primary_bg_color_hov . ' !important ;
			}
		';
		}
		if ( $tf_sidebar_gradient_one_reg && $tf_sidebar_gradient_two_reg ) {
			$output .= '
			.tf_booking-widget, .tf-tour-details-right .tf-tour-booking-box, 
			.tf-template-global .tf-box-wrapper.tf-box,
			.tf-template-3 .tf-booking-form-wrapper,
			.tf-template-3 .tf-search-date-wrapper.tf-single-widgets {background: linear-gradient(to bottom, ' . $tf_sidebar_gradient_one_reg . ' 0, ' . $tf_sidebar_gradient_two_reg . ' 100%);}
		';
		}
		if ( $tf_faq_color or $tf_faq_icon_color or $tf_faq_border_color ) {
			$output .= '
			.tf-faq-title h4,
			.tf-single-page .tf-faq-wrapper .tf-faq-single-inner h3,
			.tf-template-global .tf-faq-wrapper .tf-faq-single-inner .tf-faq-collaps h4,
			.tf-template-3 .tf-questions-wrapper .tf-questions .tf-questions-col .tf-question .tf-faq-head h3 {
				color: ' . $tf_faq_color . ';
			}
			#tf-faq-item,
			.tf-single-page .tf-faq-wrapper .tf-faq-inner .tf-faq-single {
				border-color: ' . $tf_faq_border_color . ';
			}
			#tf-faq-item .arrow,
			.tf-single-page .tf-faq-wrapper .tf-faq-single-inner .tf-faq-collaps .faq-icon i.fa-plus,
			.tf-single-page .tf-faq-wrapper .tf-faq-inner .active .tf-faq-single-inner .tf-faq-collaps .faq-icon i.fa-minus,
			.tf-template-3 .tf-questions-wrapper .tf-questions .tf-questions-col .tf-question i {
				color: ' . $tf_faq_icon_color . ';
			}
		';
		}

		if ( $tf_faq_border_color ) {
			$output .= '
			.tf-hotel-design-1 .tf-hotel-faqs-section .tf-hotel-faqs .tf-faq-wrapper .tf-faq-single .tf-faq-single-inner{
				border: 1px solid ' . $tf_faq_border_color . ';
			}
		';
		}

		if ( $tf_review_bg_color or $tf_review_color or $tf_param_single_bg_color or $tf_param_bg_color or $tf_rating_bg_color or $tf_rating_color ) {
			$output .= '
			.tf-single-review .tf-single-details,
			.tf-template-3 .tf-reviews-wrapper .tf-reviews-slider .tf-reviews-item {
				background: ' . $tf_review_bg_color . ';
			}
			.tf-single-review .tf-review-details .tf-name, 
			.tf-single-review .tf-review-details .tf-date, 
			.tf-single-review .tf-review-details .tf-rating-stars, 
			.tf-single-review .tf-review-details .tf-rating-stars i, 
			.tf-single-review .tf-review-details .tf-description p,
			.tf-template-global .tf-review-wrapper .tf-review-reply .tf-review-details,
			.tf-template-global .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-author-name h3,
			.tf-template-global .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-message p,
			.tf-template-global .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-date li,
			.tf-template-global .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-ratings i,
			.tf-template-global .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-date li i,
			.tf-template-3 .tf-reviews-wrapper .tf-reviews-slider .tf-reviews-item .tf-reviews-text h3,
			.tf-template-3 .tf-reviews-wrapper .tf-reviews-slider .tf-reviews-item .tf-reviews-text p,
			.tf-template-3 .tf-reviews-wrapper .tf-reviews-slider .tf-reviews-item .tf-reviews-text span{
				color: ' . $tf_review_color . ' !important;
			}
			.tf-review-container .tf-review-progress-bar .tf-single .tf-p-bar,
			.tf-single-page .tf-review-wrapper .tf-review-data .tf-review-data-features .tf-progress-bar,
			.tf-template-global .tf-review-wrapper .tf-review-data .tf-review-data-features .percent-progress,
			.tf-template-3 .tf-single-widgets .tf-review-data-features span.percent-progress {
				background: ' . $tf_param_single_bg_color . ';
			}
			.tf-review-container .tf-review-progress-bar,
			.tf-single-page .tf-review-wrapper .tf-review-data,
			.tf-template-global .tf-review-wrapper .tf-review-data.tf-box .tf-review-data-features{
				background: ' . $tf_param_bg_color . ';
				border-color: ' . $tf_param_bg_color . ';
			}
			.tf-review-container .tf-review-progress-bar .tf-single .tf-text, .tf-review-container .tf-review-progress-bar .tf-single .tf-p-b-rating,
			.tf-single-page .tf-review-wrapper .tf-review-data p,
			.tf-single-page .tf-review-wrapper .tf-review-data .tf-review-all-info li,
			.tf-single-page .tf-review-wrapper .tf-review-data .tf-review-all-info li i {
				color: ' . $tf_param_txt_color . ';
			}
			.tf-review-container .tf-total-review .tf-total-average div, .tf-archive-rating, .tf-single-page .tf-review-wrapper .tf-review-data .tf-review-data-average p,
			.tf-template-3 .tf-review-data .tf-review-data-average {
				background: ' . $tf_rating_bg_color . '!important;
				color: ' . $tf_rating_color . '!important;
			}
		';
		}
		if ( $tf_param_bg_color ) {
			$output .= '
			.tf-template-3 .tf-single-widgets .tf-review-data-features{
				background: ' . $tf_param_bg_color . ';
				border-color: ' . $tf_param_bg_color . ';
				padding: 32px;
				border-radius: 5px;
				margin-top: 10px;
			}
		';
		}
		if ( $tf_rating_bg_color || $tf_rating_color ) {
			$output .= '
			.tf-template-3 .tf-review-data .tf-review-data-average {
				background: ' . $tf_rating_bg_color . '!important;
				color: ' . $tf_rating_color . '!important;
				border-radius: 5px;
				padding: 10px;
			}
			.tf-template-3 .tf-review-data .tf-review-data-average span{
				color: ' . $tf_rating_color . '!important;
			}
		';
		}
		if ( $tf_review_bg_color ) {
			$output .= '
			.tf-single-page .tf-review-reply .tf-review-reply-data {
				background: ' . $tf_review_bg_color . ';
				padding: 20px;
				border-radius: 5px;
				margin: 10px 0px;
			}
			.tf-single-page .tf-review-reply .tf-review-details h3,
			.tf-single-page .tf-review-reply .tf-review-details p,
			.tf-single-page .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-date li,
			.tf-single-page .tf-review-wrapper .tf-review-reply .tf-review-details .tf-review-date li i,
			.tf-single-page .tf-review-wrapper .tf-review-reply .tf-review-ratings,
			.tf-single-page .tf-review-wrapper .tf-review-reply .tf-review-ratings i{
				color: ' . $tf_review_color . ' !important;
			}
		';
		}

		// Template 3 Global Colors
		if ( ! empty( $tf_global_bg_clr_t3 ) ) {
			$output .= '
			.tf-template-3 .tf-related-tours .tf-slider-item .tf-meta-info,
			.tf-template-3 .tf-questions-wrapper .tf-questions .tf-questions-col .tf-question.tf-active,
			.tf-template-3 .tf_tours_booking .tf-field-calander .tf-field,
			.tf-template-3 .tf-search-date-wrapper .acr-select input[type=tel],
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room,
			.tf-template-3 .tf-itinerary-wrapper .tf-itinerary-wrapper span.tf-itinerary-time,
			.tf-template-3 {
				background: ' . $tf_global_bg_clr_t3 . ' !important;
			}
			';
		}

		if ( ! empty( $tf_global_highlight_clr_t3 ) ) {
			$output .= '
			.tf-template-3 .tf-booking-form-wrapper,
			.tf-template-3 .tf-available-rooms-wrapper,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-details-menu,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-right.tf-archive-right,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-highlights-wrapper,
			.tf-template-3 .tf-bottom-booking-bar,
			.tf-template-3 .tf-related-tours,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block,
			.tf-template-3 .tf-search-date-wrapper,
			.tf-template-3 button.tf-review-open.button, .tf-template-3 .tf-reting-field button,
			.tf-template-3 .tf-review-form-wrapper,
			tf-template-3 .tf-tour-contact-informations,
			.tf-template-3 .tf-itinerary-wrapper .tf-itinerary-wrapper .tf-single-itinerary,
			.tf-template-3 .tf-itinerary-wrapper .section-title a,
			.tf-template-3 .tf-send-inquiry,
			.tf-template-3 .tf-tour-contact-informations,
			.tf-template-3 .tf-popup-wrapper.tf-room-popup .tf-popup-inner,
			.tf-template-3 .tf-popup-wrapper.tf-show .tf-popup-inner,
			.tf-template-3 .tf-archive-search-form .tf-booking-form .tf-booking-form-fields .tf-booking-form-location .tf-booking-location-wrap #tf-locationautocomplete-list,
			.tf-template-3 .tf-section,
			.tf-template-3.tf-apartment-single .tf-apartment-rooms-section .tf-apartment-room-details {
				background: ' . $tf_global_highlight_clr_t3 . ' !important;
			}
			';
		}
		if ( ! empty( $tf_global_icon_clr_t3 ) ) {
			$output .= '
			.tf-template-3 .tf-tour-contact-informations .tf-contact-details-items .tf-list li i,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block i,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-overview-popular-facilities>ul li i,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room .tf-available-room-content .tf-available-room-content-left ul li i,
			.tf-template-3 .tf-facilities-wrapper .tf-facilities .tf-facility-item h4 i,
			.tf-template-3 .tf-popup-wrapper.tf-room-popup .tf-popup-inner .tf-popup-body .tf-popup-right ul li i,
			.tf-template-3 .tf-available-archive-hetels-wrapper .tf-archive-available-rooms .tf-available-room .tf-available-room-content .tf-available-room-content-left .tf-section-title-and-location .tf-title-location i,
			.tf-template-3 .tf-section,
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-rooms-head .tf-filter i,
			.tf-template-3 .tf-send-inquiry i {
				color: ' . $tf_global_icon_clr_t3 . ' !important;
			}
			';
		}

		wp_add_inline_style( 'tf-app-style', apply_filters( 'tf_global_css', $output ) );
	}

	function tf_hotel_css() {
		// Store as PHP variables
		// Hotel CSS
		$tf_hotel_type_color         = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-type-bg-color' ) )['regular'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-type-bg-color' ) )['regular'] : '';
		$tf_hotel_type_bg_color      = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-type-bg-color' ) )['hover'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-type-bg-color' ) )['hover'] : '';
		$tf_share_color_reg          = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-share-icon' ) )['regular'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-share-icon' ) )['regular'] : '';
		$tf_share_color_hov          = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-share-icon' ) )['hover'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-share-icon' ) )['hover'] : '';
		$tf_gradient_one_reg         = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-map-button' ) )['gradient_one_reg'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-map-button' ) )['gradient_one_reg'] : '';
		$tf_gradient_two_reg         = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-map-button' ) )['gradient_two_reg'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-map-button' ) )['gradient_two_reg'] : '';
		$tf_gradient_one_hov         = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-map-button' ) )['gradient_one_hov'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-map-button' ) )['gradient_one_hov'] : '';
		$tf_gradient_two_hov         = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-map-button' ) )['gradient_two_hov'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-map-button' ) )['gradient_two_hov'] : '';
		$tf_map_text_color           = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-map-button-text' ) )['regular'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-map-button-text' ) )['regular'] : '';
		$tf_hotel_features           = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-features-color' ) )['regular'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-features-color' ) )['regular'] : '';
		$tf_hotel_table_color        = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-table-style' ) )['table_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-table-style' ) )['table_color'] : '';
		$tf_hotel_table_bg_color     = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-table-style' ) )['table_bg_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-table-style' ) )['table_bg_color'] : '';
		$tf_hotel_table_border_color = ! empty( self::tf_data_types( self::tfopt( 'tourfic-hotel-table-style' ) )['table_border_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-hotel-table-style' ) )['table_border_color'] : '';

		$output = '';

		// Hotel CSS
		if ( $tf_hotel_type_color ) {
			$output .= '
			.tf-title-left span.post-type {color: ' . $tf_hotel_type_color . ';}
		';
		}
		if ( $tf_hotel_type_bg_color ) {
			$output .= '
			.tf-title-left span.post-type {background: ' . $tf_hotel_type_bg_color . ';}
		';
		}
		if ( $tf_share_color_reg ) {
			$output .= '
			.tf-share .share-toggle i, .tf-single-page .tf-section.tf-single-head .tf-share > a i,
			.tf-wishlist-button i,
			.tf-template-3 .tf-hero-section-wrap .tf-container .tf-hero-content .tf-wish-and-share a.share-toggle i {color: ' . $tf_share_color_reg . ' !important;}
		';
		}
		if ( $tf_share_color_hov ) {
			$output .= '
			.tf-share .share-toggle i:hover, .tf-single-page .tf-section.tf-single-head .tf-share > a i:hover,
			.tf-wishlist-button i:hover,
			.tf-template-3 .tf-hero-section-wrap .tf-container .tf-hero-content .tf-wish-and-share a.share-toggle:hover i {color: ' . $tf_share_color_hov . ' !important;}
		';
		}
		if ( $tf_gradient_one_reg && $tf_gradient_two_reg ) {
			$output .= '
			.show-on-map .btn-styled, .tf-single-page .tf-hotel-location-map .tf-hotel-location-preview a i {background: linear-gradient(to bottom, ' . $tf_gradient_one_reg . ' 0, ' . $tf_gradient_two_reg . ' 100%) !important;}
		';
		}
		if ( $tf_gradient_one_hov && $tf_gradient_two_hov ) {
			$output .= '
			.show-on-map .btn-styled:hover, .tf-single-page .tf-hotel-location-map .tf-hotel-location-preview a i:hover {background: linear-gradient(to bottom, ' . $tf_gradient_one_hov . ' 0, ' . $tf_gradient_two_hov . ' 100%) !important;}
		';
		}
		if ( $tf_map_text_color ) {
			$output .= '
			.show-on-map .btn-styled, .tf-single-page .tf-hotel-location-map .tf-hotel-location-preview a i {color: ' . $tf_map_text_color . ';}
		';
		}
		if ( $tf_hotel_features ) {
			$output .= '
			.tf_features i, 
			.tf-archive-desc i, 
			.tf-single-page .tf-hotel-single-features ul li i,
			.tf-template-global .tf-search-results-list .tf-item-card .tf-item-details .tf-archive-features i,
			.tf-hotel-design-1 .tf-rooms .tf-features-infos ul li,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-overview-popular-facilities > ul li i {color: ' . $tf_hotel_features . '!important;}
		';
		}
		if ( $tf_hotel_table_color or $tf_hotel_table_bg_color ) {
			$output .= '
			.availability-table thead{
				color: ' . $tf_hotel_table_color . ';
				background: ' . $tf_hotel_table_bg_color . ';
			}
			.tf-single-page .tf-rooms-sections .tf-rooms .tf-availability-table>thead tr th{
				background: ' . $tf_hotel_table_bg_color . ';
				color: ' . $tf_hotel_table_color . ';
				border-radius: 0px;
			}
		';
		}
		if ( $tf_hotel_table_color ) {
			$output .= '
			.tf-single-page .tf-rooms-sections .tf-rooms .tf-availability-table>thead tr th{
				color: ' . $tf_hotel_table_color . ';
			}
		';
		}
		if ( $tf_hotel_table_border_color ) {
			$output .= '
			.availability-table td, .availability-table th, .availability-table td.reserve, .tf-single-page .tf-rooms-sections .tf-rooms .tf-availability-table>tr>td {border-color: ' . $tf_hotel_table_border_color . ';}
		';
		}

		if ( $tf_hotel_table_border_color ) {
			$output .= '
			.tf-single-page .tf-rooms-sections .tf-rooms .tf-availability-table {border: 1px solid ' . $tf_hotel_table_border_color . '; border-collapse: inherit;}
			.tf-hotel-design-1 .tf-rooms-sections .tf-rooms .tf-availability-table thead tr th{
				border-color: ' . $tf_hotel_table_border_color . ';
			}
			.tf-hotel-design-1 .tf-rooms-sections .tf-rooms .tf-availability-table tbody tr td{
				border-color: ' . $tf_hotel_table_border_color . ';
			}
		';
		}
		if ( $tf_hotel_table_border_color ) {
			$output .= '
			
			.tf-template-3 .tf-available-rooms-wrapper .tf-available-room{
				border: 1px solid ' . $tf_hotel_table_border_color . ';
			}
		';
		}

		wp_add_inline_style( 'tf-app-style', apply_filters( 'tf_hotel_css', $output ) );
	}

	function tf_tour_css() {
		// Store as PHP variables
		// Tour CSS
		$tf_tour_sale_price      = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['sale_price'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['sale_price'] : '';
		$tf_tour_org_price       = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['org_price'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['org_price'] : '';
		$tf_tour_tab_text        = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['tab_text'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['tab_text'] : '';
		$tf_tour_tab_bg          = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['tab_bg'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['tab_bg'] : '';
		$tf_tour_active_tab_text = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['active_tab_text'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['active_tab_text'] : '';
		$tf_tour_active_tab_bg   = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['active_tab_bg'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['active_tab_bg'] : '';
		$tf_tour_tab_border      = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['tab_border'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-pricing-color' ) )['tab_border'] : '';
		$tf_tour_icon_color      = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['icon_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['icon_color'] : '';
		$tf_tour_heading_color   = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['heading_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['heading_color'] : '';
		$tf_tour_text_color      = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['text_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['text_color'] : '';
		$tf_tour_bg_one          = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['bg_one'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['bg_one'] : '';
		$tf_tour_bg_two          = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['bg_two'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['bg_two'] : '';
		$tf_tour_bg_three        = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['bg_three'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['bg_three'] : '';
		$tf_tour_bg_four         = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['bg_four'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-info-color' ) )['bg_four'] : '';
		$tf_tour_btn_col         = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-sticky-booking' ) )['btn_col'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-sticky-booking' ) )['btn_col'] : '';
		$tf_tour_btn_bg          = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-sticky-booking' ) )['btn_bg'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-sticky-booking' ) )['btn_bg'] : '';
		$tf_tour_btn_hov_bg      = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-sticky-booking' ) )['btn_hov_bg'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-sticky-booking' ) )['btn_hov_bg'] : '';
		$tf_tour_btn_hov_col     = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-sticky-booking' ) )['btn_hov_col'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-sticky-booking' ) )['btn_hov_col'] : '';
		$tf_tour_form_background = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-sticky-booking' ) )['form_background'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-sticky-booking' ) )['form_background'] : '';
		$tf_tour_form_border     = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-sticky-booking' ) )['form_border'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-sticky-booking' ) )['form_border'] : '';
		$tf_inc_gradient_one_reg = ! empty( self::tf_data_types( self::tfopt( 'tourfic-include-exclude' ) )['gradient_one_reg'] ) ? self::tf_data_types( self::tfopt( 'tourfic-include-exclude' ) )['gradient_one_reg'] : '';
		$tf_inc_gradient_two_reg = ! empty( self::tf_data_types( self::tfopt( 'tourfic-include-exclude' ) )['gradient_two_reg'] ) ? self::tf_data_types( self::tfopt( 'tourfic-include-exclude' ) )['gradient_two_reg'] : '';
		$tf_inc_heading_color    = ! empty( self::tf_data_types( self::tfopt( 'tourfic-include-exclude' ) )['heading_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-include-exclude' ) )['heading_color'] : '';
		$tf_inc_text_color       = ! empty( self::tf_data_types( self::tfopt( 'tourfic-include-exclude' ) )['text_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-include-exclude' ) )['text_color'] : '';
		$tf_itin_time_day_txt    = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-itinerary' ) )['time_day_txt'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-itinerary' ) )['time_day_txt'] : '';
		$tf_itin_time_day_bg     = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-itinerary' ) )['time_day_bg'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-itinerary' ) )['time_day_bg'] : '';
		$tf_itin_heading_color   = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-itinerary' ) )['heading_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-itinerary' ) )['heading_color'] : '';
		$tf_itin_text_color      = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-itinerary' ) )['text_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-itinerary' ) )['text_color'] : '';
		$tf_itin_bg_color        = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-itinerary' ) )['bg_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-itinerary' ) )['bg_color'] : '';
		$tf_itin_icon_color      = ! empty( self::tf_data_types( self::tfopt( 'tourfic-tour-itinerary' ) )['icon_color'] ) ? self::tf_data_types( self::tfopt( 'tourfic-tour-itinerary' ) )['icon_color'] : '';

		$output = '';

		// Tour CSS
		if ( $tf_tour_sale_price or $tf_tour_org_price or $tf_tour_tab_text or $tf_tour_tab_bg or $tf_tour_tab_border ) {
			$output .= '
			.tf-single-tour-pricing .tf-price span.sale-price,
			.tf-single-page .tf-trip-info .tf-trip-pricing .tf-price-amount {
				color: ' . $tf_tour_sale_price . ';
			}
			.tf-single-tour-pricing .tf-price {
				color: ' . $tf_tour_org_price . ';
			}
			.tf-single-tour-pricing .tf-price-tab li,
			.tf-single-page .tf-trip-info .person-info,
			.tf-single-page .tf-trip-info .person-info p {
				color: ' . $tf_tour_tab_text . ';
			}
			.tf-single-tour-pricing .tf-price-tab li,
			.tf-single-page .tf-trip-info .person-info {
				background: ' . $tf_tour_tab_bg . ';
			}
			.tf-single-tour-pricing .tf-price-tab li.active,
			.tf-single-page .tf-trip-info .person-info.active {
				color: ' . $tf_tour_active_tab_text . ';
			}
			.tf-single-tour-pricing .tf-price-tab li.active,
			.tf-single-page .tf-trip-info .person-info.active {
				background: ' . $tf_tour_active_tab_bg . ';
			}
			.tf-single-tour-pricing .tf-price-tab, .tf-single-tour-pricing .tf-price-tab li:nth-child(2), .tf-single-tour-pricing .tf-price-tab li:nth-child(3) {
				border-color: ' . $tf_tour_tab_border . ';
			}
		';
		}

		if ( $tf_tour_icon_color or $tf_tour_heading_color or $tf_tour_text_color or $tf_tour_bg_one or $tf_tour_bg_two or $tf_tour_bg_three or $tf_tour_bg_four ) {
			$output .= '
			.tf-single-square-block i,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block i,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block i {
				color: ' . $tf_tour_icon_color . ';
			}
			.tf-single-square-block h4,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block h3,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block .tf-feature-block-details h5 {
				color: ' . $tf_tour_heading_color . ';
			}
			.tf-single-square-block p,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block p,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block .tf-feature-block-details p {
				color: ' . $tf_tour_text_color . ';
			}
			.tf-single-square-block.first,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block.tf-first,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block:nth-child(1) {
				background: ' . $tf_tour_bg_one . ';
			}
			.tf-single-square-block.second,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block.tf-second,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block:nth-child(2) {
				background: ' . $tf_tour_bg_two . ';
			}
			.tf-single-square-block.third,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block.tf-third,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block:nth-child(3) {
				background: ' . $tf_tour_bg_three . ';
			}
			.tf-single-square-block.fourth,
			.tf-single-page .tf-trip-feature-blocks .tf-feature-block.tf-tourth,
			.tf-template-3 .tf-content-wrapper .tf-details .tf-details-left .tf-overview-wrapper .tf-features-block-wrapper .tf-feature-block:nth-child(4) {
				background: ' . $tf_tour_bg_four . ';
			}
		';
		}

		if ( $tf_tour_btn_col or $tf_tour_btn_bg or $tf_tour_btn_hov_col or $tf_tour_btn_hov_bg or $tf_tour_form_background or $tf_tour_form_border ) {
			$output .= '
			.tf-tours-fixed .btn-styled {
				color: ' . $tf_tour_btn_col . ';
				border-color: ' . $tf_tour_btn_bg . ';
				background: ' . $tf_tour_btn_bg . ';
			}
			.tf-tours-fixed .btn-styled:hover {
				color: ' . $tf_tour_btn_hov_col . ';
				border-color: ' . $tf_tour_btn_hov_bg . ';
				background: ' . $tf_tour_btn_hov_bg . ';
			}
			.tf-tour-booking-wrap.tf-tours-fixed {
				background: ' . $tf_tour_form_background . ';
				border-color: ' . $tf_tour_form_border . ';
			}
		';
		}

		if ( $tf_inc_gradient_one_reg or $tf_inc_gradient_two_reg or $tf_inc_heading_color or $tf_inc_text_color ) {
			$output .= '
			.tf-include-section, 
			.tf-exclude-section, 
			.tf-single-page .tf-inex-wrapper .tf-inex,
			.tf-template-3 .tf-include-exclude-wrapper .tf-include-exclude-innter > div{
				background-image: linear-gradient(to right, ' . $tf_inc_gradient_one_reg . ', ' . $tf_inc_gradient_two_reg . ');
				color: ' . $tf_inc_text_color . ';
			}
			.tf-inc-exc-content h4,
			.tf-single-page .tf-inex-wrapper .tf-inex h3 {
				color: ' . $tf_inc_heading_color . ';
			}
		';
		}
		if ( $tf_inc_gradient_one_reg or $tf_inc_gradient_two_reg ) {
			$output .= '
			.tf-template-3 .tf-include-exclude-wrapper .tf-include-exclude-innter > div{
				padding: 15px;
			}
		';
		}
		if ( $tf_itin_time_day_txt or $tf_itin_time_day_bg or $tf_itin_heading_color or $tf_itin_text_color or $tf_itin_bg_color or $tf_itin_icon_color ) {
			$output .= '
			.tf-travel-time span,
			.tf-single-page .tf-itinerary-wrapper .tf-single-itinerary-item .itinerary-day,
			.tf-template-3 .tf-itinerary-wrapper span.tf-itinerary-time {
				color: ' . $tf_itin_time_day_txt . ' !important;
			}
			.tf-travel-time,
			.tf-template-3 .tf-itinerary-wrapper span.tf-itinerary-time {
				background: ' . $tf_itin_time_day_bg . ';
			}
			.tf-accordion-head h4, 
			.tf-accordion-head h4:hover,
			.tf-single-page .tf-itinerary-wrapper .tf-single-itinerary-item h3,
			.tf-tour-design-1 .tf-itinerary-wrapper .tf-itinerary-box .tf-single-itinerary-item h4,
			.tf-template-3 .tf-itinerary-wrapper .tf-itinerary-title h4 {
				color: ' . $tf_itin_heading_color . ';
			}
			.tf-travel-desc,
			.tf-single-page .tf-itinerary-content-details p {
				color: ' . $tf_itin_text_color . ';
			}
			#tf-accordion-wrapper .tf-accordion-content, 
			#tf-accordion-wrapper .tf-accordion-head, 
			.tf-tour-design-1 .tf-itinerary-wrapper .tf-itinerary-box,
			.tf-template-3 .tf-itinerary-wrapper .tf-single-itinerary {
				background: ' . $tf_itin_bg_color . ';
			}
			#tf-accordion-wrapper .arrow-animate, #tf-accordion-wrapper .arrow,
			.tf-template-3 .tf-itinerary-wrapper .tf-itinerary-title i {
				color: ' . $tf_itin_icon_color . ';
			}
			.tf-tour-design-1 .tf-itinerary-wrapper .tf-single-itinerary-item.active .accordion-checke::before,
			.tf-tour-design-1 .tf-itinerary-wrapper .tf-single-itinerary-item .accordion-checke::before {
				border: 1px solid ' . $tf_itin_icon_color . ' !important;
			}
			.tf-tour-design-1 .tf-itinerary-wrapper .tf-single-itinerary-item.active .accordion-checke::after {
				background: ' . $tf_itin_icon_color . ' !important;
			}
		';
		}

		wp_add_inline_style( 'tf-app-style', apply_filters( 'tf_tour_css', $output ) );
	}

	function tf_apartment_css() {
		//amenities
		$amenities_bg           = ! empty( self::tf_data_types( self::tfopt( 'apartment-amenities' ) )['amenities_bg'] ) ? self::tf_data_types( self::tfopt( 'apartment-amenities' ) )['amenities_bg'] : '';
		$amenities_border_color = ! empty( self::tf_data_types( self::tfopt( 'apartment-amenities' ) )['amenities_border_color'] ) ? self::tf_data_types( self::tfopt( 'apartment-amenities' ) )['amenities_border_color'] : '';
		$amenities_text         = ! empty( self::tf_data_types( self::tfopt( 'apartment-amenities' ) )['amenities_text'] ) ? self::tf_data_types( self::tfopt( 'apartment-amenities' ) )['amenities_text'] : '';
		$amenities_icon         = ! empty( self::tf_data_types( self::tfopt( 'apartment-amenities' ) )['amenities_icon'] ) ? self::tf_data_types( self::tfopt( 'apartment-amenities' ) )['amenities_icon'] : '';
		//features
		$features_bg           = ! empty( self::tf_data_types( self::tfopt( 'apartment-features' ) )['features_bg'] ) ? self::tf_data_types( self::tfopt( 'apartment-features' ) )['features_bg'] : '';
		$features_border_color = ! empty( self::tf_data_types( self::tfopt( 'apartment-features' ) )['features_border_color'] ) ? self::tf_data_types( self::tfopt( 'apartment-features' ) )['features_border_color'] : '';
		$features_text         = ! empty( self::tf_data_types( self::tfopt( 'apartment-features' ) )['features_text'] ) ? self::tf_data_types( self::tfopt( 'apartment-features' ) )['features_text'] : '';
		$features_icon         = ! empty( self::tf_data_types( self::tfopt( 'apartment-features' ) )['features_icon'] ) ? self::tf_data_types( self::tfopt( 'apartment-features' ) )['features_icon'] : '';
		//booking form
		$form_heading_color = ! empty( self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_heading_color'] ) ? self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_heading_color'] : '';
		$form_bg            = ! empty( self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_bg'] ) ? self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_bg'] : '';
		$form_border_color  = ! empty( self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_border_color'] ) ? self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_border_color'] : '';
		$form_text          = ! empty( self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_text'] ) ? self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_text'] : '';
		$form_fields_bg     = ! empty( self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_fields_bg'] ) ? self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_fields_bg'] : '';
		$form_fields_border = ! empty( self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_fields_border'] ) ? self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_fields_border'] : '';
		$form_fields_text   = ! empty( self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_fields_text'] ) ? self::tf_data_types( self::tfopt( 'booking-form-design' ) )['form_fields_text'] : '';
		//Host
		$host_heading_color = ! empty( self::tf_data_types( self::tfopt( 'host-card-design' ) )['host_heading_color'] ) ? self::tf_data_types( self::tfopt( 'host-card-design' ) )['host_heading_color'] : '';
		$host_bg            = ! empty( self::tf_data_types( self::tfopt( 'host-card-design' ) )['host_bg'] ) ? self::tf_data_types( self::tfopt( 'host-card-design' ) )['host_bg'] : '';
		$host_border_color  = ! empty( self::tf_data_types( self::tfopt( 'host-card-design' ) )['host_border_color'] ) ? self::tf_data_types( self::tfopt( 'host-card-design' ) )['host_border_color'] : '';
		$host_text          = ! empty( self::tf_data_types( self::tfopt( 'host-card-design' ) )['host_text'] ) ? self::tf_data_types( self::tfopt( 'host-card-design' ) )['host_text'] : '';

		$output = '';
		if ( $amenities_bg || $amenities_border_color || $amenities_text || $amenities_icon ) {
			$output .= '
			.tf-apartment .apartment-amenities ul {
				background-color: ' . $amenities_bg . ';
				border-color: ' . $amenities_border_color . ';
			}
			.tf-apartment .apartment-amenities ul li {
				color: ' . $amenities_text . ';
			}
			.tf-apartment .apartment-amenities ul li i {
				color: ' . $amenities_icon . ';
			}
		';
		}
		if ( $features_bg || $features_border_color || $features_text || $features_icon ) {
			$output .= '
			.tf-apartment .key-features ul {
				background-color: ' . $features_bg . ';
				border-color: ' . $features_border_color . ';
			}
			.tf-apartment .key-features ul li {
				color: ' . $features_text . ';
			}
			.tf-apartment .key-features ul li i {
				color: ' . $features_icon . ';
			}
		';
		}
		if ( $form_heading_color || $form_bg || $form_border_color || $form_text || $form_fields_bg || $form_fields_border || $form_fields_text ) {
			$output .= '
			#tf-apartment-booking h4 {
				color: ' . $form_heading_color . ';
			}
			#tf-apartment-booking {
				background-color: ' . $form_bg . ';
				border-color: ' . $form_border_color . ';
			}
			#tf-apartment-booking .tf-apartment-form-header .tf-apartment-price-per-night span:not(.woocommerce-Price-amount,.woocommerce-Price-currencySymbol),
			#tf-apartment-booking .tf-apartment-form-header .tf-apartment-price-per-night span,
			#tf-apartment-booking .tf-apartment-form-header .tf-single-rating {
				color: ' . $form_text . ';
			}
			#tf-apartment-booking .tf-apartment-form-fields{
				background-color: ' . $form_fields_bg . ';
				border-color: ' . $form_fields_border . ';
			}
			#tf-apartment-booking .tf-apartment-form-fields .tf-apartment-guest-row{
				border-top-color: ' . $form_fields_border . ';
			}
			#tf-apartment-booking .tf-apartment-form-fields .tf_booking-dates .tf-check-in-out-date #check-in-out-date::-webkit-input-placeholder{
				color: ' . $form_fields_text . ';
			}
			#tf-apartment-booking .tf-apartment-form-fields .tf-apartment-guest-row .tf-label,
			.adults-text, .person-sep, .child-text, .room-text, .infant-text,
			#tf-apartment-booking .tf-apartment-form-fields .tf_booking-dates .tf-check-in-out-date #check-in-out-date,
			#tf-apartment-booking .tf-apartment-form-fields .tf_booking-dates .tf-check-in-out-date .tf-label{
				color: ' . $form_fields_text . ';
			}
			';
		}
		if ( $host_heading_color || $host_bg || $host_border_color || $host_text ) {
			$output .= '
			.host-details {
				background-color: ' . $host_bg . ';
				border-color: ' . $host_border_color . ';
			}
			.host-details .host-meta h4 {
				color: ' . $host_heading_color . ';
			}
			.host-details,
			.host-details .host-bottom p,
			.tf-host-rating-wrapper h6{
				color: ' . $host_text . ';
			}
			';
		}

		wp_add_inline_style( 'tf-app-style', apply_filters( 'tf_apartment_css', $output ) );
	}
}