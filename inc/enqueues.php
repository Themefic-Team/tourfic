<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Dequeue frontend scripts to avoid conflict
 *
 * @since 1.0
 */
if ( ! function_exists( 'tf_dequeue_scripts' ) ) {
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

	add_filter( 'wp_enqueue_scripts', 'tf_dequeue_scripts', 9999 );
}

/**
 * Enqueue Frontend scripts
 *
 * @since 1.0
 */
if ( ! function_exists( 'tf_enqueue_scripts' ) ) {
	function tf_enqueue_scripts() {

		$flatpickr_cdn    = ! empty( tfopt( 'ftpr_cdn' ) ) ? tfopt( 'ftpr_cdn' ) : false;
		$flatpickr_locale = ! empty( get_locale() ) ? get_locale() : 'en_US';
		$allowed_locale   = array( 'ar', 'bn_BD', 'de_DE', 'es_ES', 'fr_FR', 'hi_IN', 'it_IT', 'nl_NL', 'ru_RU', 'zh_CN' );
		$fancy_cdn        = ! empty( tfopt( 'fnybx_cdn' ) ) ? tfopt( 'fnybx_cdn' ) : false;
		$slick_cdn        = ! empty( tfopt( 'slick_cdn' ) ) ? tfopt( 'slick_cdn' ) : false;
		$fa_cdn           = ! empty( tfopt( 'fa_cdn' ) ) ? tfopt( 'fa_cdn' ) : false;
		$min_css          = ! empty( tfopt( 'css_min' ) ) ? '.min' : '';
		$min_js           = ! empty( tfopt( 'js_min' ) ) ? '.min' : '';

		//Updated CSS
		wp_enqueue_style( 'tf-app-style', TF_ASSETS_URL . 'app/css/tourfic-style' . $min_css . '.css', null, TOURFIC );
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
				$showxaxis           = ! empty( tf_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-x-axis'] ) ? tf_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-x-axis'] : false;
				$showyaxis           = ! empty( tf_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-y-axis'] ) ? tf_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-y-axis'] : false;
				$showlinegraph       = ! empty( tf_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-line-graph'] ) ? tf_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-line-graph'] : false;
				$showitinerarychart  = ! empty( tf_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-chart'] ) ? tf_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-chart'] : false;
				$showitinerarystatus = ! empty( tf_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-status'] ) ? tf_data_types( tfopt( 'itinerary-builder-setings' ) )['itinerary-status'] : false;
				$elevvationmode      = ! empty( tf_data_types( tfopt( 'itinerary-builder-setings' ) )['elevtion_type'] ) && tf_data_types( tfopt( 'itinerary-builder-setings' ) )['elevtion_type'] == "Feet" ? "Feet" : "Meter";
			}
		}

		/**
		 * Flatpickr
		 * v4.6.13
		 */
		if ( $flatpickr_cdn ) {
			wp_enqueue_style( 'flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css', array(), TOURFIC );
			wp_enqueue_script( 'flatpickr', '//cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js', array( 'jquery' ), TOURFIC, true );
			if ( in_array( $flatpickr_locale, $allowed_locale ) ) {
				wp_enqueue_script( 'flatpickr-locale', TF_ASSETS_URL . 'app/libs/flatpickr/l10n/' . $flatpickr_locale . '.min.js', array( 'jquery' ), TOURFIC, true );
			}
		} else {
			wp_enqueue_style( 'tf-flatpickr', TF_ASSETS_URL . 'app/libs/flatpickr/flatpickr.min.css', '', TOURFIC );
			wp_enqueue_script( 'tf-flatpickr', TF_ASSETS_URL . 'app/libs/flatpickr/flatpickr.min.js', array( 'jquery' ), TOURFIC, true );
			if ( in_array( $flatpickr_locale, $allowed_locale ) ) {
				wp_enqueue_script( 'tf-flatpickr-locale', TF_ASSETS_URL . 'app/libs/flatpickr/l10n/' . $flatpickr_locale . '.min.js', array( 'jquery' ), TOURFIC, true );
			}
		}

		/**
		 * Range Slider
		 */
		wp_enqueue_style( 'al-range-slider', TF_ASSETS_URL . 'app/libs/range-slider/al-range-slider.css', '', TOURFIC );
		wp_enqueue_script( 'al-range-slider', TF_ASSETS_URL . 'app/libs/range-slider/al-range-slider.js', array( 'jquery' ), TOURFIC, true );
		wp_enqueue_script( 'jquery-ui-autocomplete' );


		/**
		 * Fancybox
		 * v3.5.7
		 */
		if ( $fancy_cdn ) {
			wp_enqueue_style( 'fancyBox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css', array(), TOURFIC );
			wp_enqueue_script( 'fancyBox', '//cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js', array( 'jquery' ), TOURFIC, true );
		} else {
			wp_enqueue_style( 'fancybox', TF_ASSETS_URL . 'app/libs/fancybox/jquery.fancybox.min.css', '', TOURFIC );
			wp_enqueue_script( 'fancybox', TF_ASSETS_URL . 'app/libs/fancybox/jquery.fancybox.min.js', array( 'jquery' ), TOURFIC, true );
		}

		/**
		 * Slick
		 * v1.8.1
		 */
		if ( $slick_cdn ) {
			wp_enqueue_style( 'slick', '//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', array(), TOURFIC );
			wp_enqueue_script( 'slick', '//cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array( 'jquery' ), TOURFIC, true );
		} else {
			wp_enqueue_style( 'tf-slick', TF_ASSETS_URL . 'app/libs/slick/slick.css', '', TOURFIC );
			wp_enqueue_script( 'tf-slick', TF_ASSETS_URL . 'app/libs/slick/slick.min.js', array( 'jquery' ), TOURFIC, true );
		}

		/**
		 * Font Awesome Free
		 * v5.15.4
		 */
		if ( $fa_cdn ) {
			wp_enqueue_style( 'font-awesome-5', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', '', TOURFIC );
		} else {
			wp_enqueue_style( 'font-awesome-5', TF_ASSETS_URL . 'app/libs/font-awesome/css/all.min.css', '', TOURFIC );
		}

		/**
		 * Notyf
		 * v3.0
		 */
		wp_enqueue_style( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.css', '', TOURFIC );
		wp_enqueue_script( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.js', array( 'jquery' ), TOURFIC, true );

		/**
		 * Openstreet Map
		 * v1.9
		 */

		$tf_openstreet_map = ! empty( tfopt( 'google-page-option' ) ) ? tfopt( 'google-page-option' ) : "default";
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
		$tfhotel_min_max_query = new WP_Query( $tfhotel_min_max );
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
							$avail_date = json_decode($singleroom['avail_date'], true);
							if(!empty($avail_date) && is_array($avail_date)) {
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

		$tftours_min_max_query = new WP_Query( $tftours_min_max );
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

		$tf_apartment_min_max_price = get_apartment_min_max_price();

		/**
		 * Custom
		 */

		wp_enqueue_script( 'tourfic', TF_ASSETS_APP_URL . 'js/tourfic-scripts' . $min_js . '.js', '', TOURFIC, true );
		wp_localize_script( 'tourfic', 'tf_params',
			array(
				'nonce'                  => wp_create_nonce( 'tf_ajax_nonce' ),
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'single'                 => is_single(),
				'locations'              => get_hotel_locations(),
				'apartment_locations'    => get_apartment_locations(),
				'tour_destinations'      => get_tour_destinations(),
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
				'date_hotel_search'      => tfopt( 'date_hotel_search' ),
				'date_tour_search'       => tfopt( 'date_tour_search' ),
				'date_apartment_search'  => tfopt( 'date_apartment_search' ),
				'tf_apartment_max_price' => isset( $tf_apartment_min_max_price ) ? $tf_apartment_min_max_price['max'] : 0,
				'tf_apartment_min_price' => isset( $tf_apartment_min_max_price ) ? $tf_apartment_min_max_price['min'] : 0,
			)
		);
		//wp_enqueue_style( 'tf-responsive', TF_ASSETS_URL . 'css/old/responsive.css', '', TOURFIC );

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

	add_action( 'wp_enqueue_scripts', 'tf_enqueue_scripts' );
}

/**
 * Enqueue Admin scripts
 *
 * @since 1.0
 */
if ( ! function_exists( 'tf_enqueue_admin_scripts' ) ) {
	function tf_enqueue_admin_scripts( $hook ) {

		/**
		 * Notyf
		 * v3.0
		 */
		wp_enqueue_style( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.css', '', TOURFIC );
		wp_enqueue_script( 'notyf', TF_ASSETS_URL . 'app/libs/notyf/notyf.min.js', array( 'jquery' ), TOURFIC, true );

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

	add_action( 'admin_enqueue_scripts', 'tf_enqueue_admin_scripts' );
}

/**
 * Others Theme & Plugin Compatibility
 * 
 * @since 2.9.27
 */

function tf_dequeue_theplus_script_on_settings_page($screen) {

	// The Plus Addons for Elementor Compatibility
    if ("toplevel_page_tf_settings"==$screen && wp_script_is('theplus-admin-js-pro', 'enqueued')) {
        wp_dequeue_script('theplus-admin-js-pro');
        wp_deregister_script('theplus-admin-js-pro');
    }

	//The Guido theme WP Listings Directory Compatibility
	if ("toplevel_page_tf_settings"==$screen && wp_script_is('wp-listings-directory-custom-field', 'enqueued')) {
        wp_dequeue_script('wp-listings-directory-custom-field');
        wp_deregister_script('wp-listings-directory-custom-field');
    }
	//The Easy Table of Contents Compatibility
	if ("toplevel_page_tf_settings"==$screen && wp_script_is('cn_toc_admin_script', 'enqueued')) {
        wp_dequeue_script('cn_toc_admin_script');
        wp_deregister_script('cn_toc_admin_script');
    }

	$get_screen = get_current_screen();

	if(!empty($get_screen)) {

		if ($get_screen->base == "post" && ($get_screen->id == "tf_hotel" || $get_screen->id == "tf_apartment" ||  $get_screen->id == "tf_tours") ) {

			if (wp_script_is('select2', 'enqueued')) {

				wp_dequeue_script('select2');
				wp_deregister_script('select2');
			}
		}
	}
}
add_action('admin_enqueue_scripts', 'tf_dequeue_theplus_script_on_settings_page', 9999);

/**
 * Template 3 Compatible to others Themes
 * 
 * @since 2.10.8
 */

 function tf_templates_body_classess($classes) {

	// Tour Archive Layout
	$tf_tour_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['tour-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['tour-archive'] : 'design-1';
	// Hotel Archive Layout
	$tf_hotel_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';
	// Hotel Single Global Layout
	$tf_hotel_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-hotel'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-hotel'] : 'design-1';
	// Tour Single Global Layout
	$tf_tour_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-tour'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-tour'] : 'design-1';

	if(is_post_type_archive('tf_tours') || is_tax('tour_destination')){
		if('design-2'==$tf_tour_arc_selected_template){
			$classes[] = 'tf_template_3_global_layouts';
		}
	}
	
	if(is_post_type_archive('tf_hotel') || is_tax('hotel_location')){
		if('design-2'==$tf_hotel_arc_selected_template){
			$classes[] = 'tf_template_3_global_layouts';
		}
	}

	if(is_singular('tf_hotel')){
		$meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
		$tf_hotel_layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
		if("single"==$tf_hotel_layout_conditions){
			$tf_hotel_single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
		}
		$tf_hotel_selected_check = !empty($tf_hotel_single_template) ? $tf_hotel_single_template : $tf_hotel_global_template;
		if('design-2'==$tf_hotel_selected_check){
			$classes[] = 'tf_template_3_global_layouts';
		}
	}

	if(is_singular('tf_tours')){
		$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
		$tf_tour_layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
		if("single"==$tf_tour_layout_conditions){
			$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
		}
		$tf_tour_selected_check = !empty($tf_tour_single_template) ? $tf_tour_single_template : $tf_tour_global_template;
		if('design-2'==$tf_tour_selected_check){
			$classes[] = 'tf_template_3_global_layouts';
		}
	}

	$tf_search_result_page_id  = ! empty( tfopt( 'search-result-page' ) ) ? tfopt( 'search-result-page' ) : '';
	if(!empty($tf_search_result_page_id)){
		$tf_search_result_page_slug = get_post_field( 'post_name', $tf_search_result_page_id );
	}
	if(!empty($tf_search_result_page_slug)){
		$tf_current_page_id = get_post_field( 'post_name', get_the_ID() );
		if($tf_search_result_page_slug==$tf_current_page_id){
			$classes[] = 'tf_template_3_global_layouts';
		}
	}
    return $classes;
}

add_filter('body_class', 'tf_templates_body_classess');