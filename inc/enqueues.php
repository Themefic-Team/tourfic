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
		 * Tour booking form
		 */
		global $post;
		$post_id = ! empty( $post->ID ) ? $post->ID : '';
		$post_type = ! empty( $post->post_type ) ? $post->post_type : '';
		if($post_type == 'tf_tours' && !empty($post_id)) {
			$single_tour_form_data = array();

			$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
			$tf_tour_layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
			if ( "single" == $tf_tour_layout_conditions ) {
				$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
			}
			$tf_tour_global_template = ! empty( tf_data_types( tfopt( 'tf-template' ) )['single-tour'] ) ? tf_data_types( tfopt( 'tf-template' ) )['single-tour'] : 'design-1';

			$tf_tour_selected_template = ! empty( $tf_tour_single_template ) ? $tf_tour_single_template : $tf_tour_global_template;



			$tour_type = ! empty( $meta['type'] ) ? $meta['type'] : '';
			// Continuous custom availability
			$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : '';

			# Get Pricing
			$tour_price = new Tour_Price( $meta );
			// Date format for Users Oputput
			$tour_date_format_for_users  = !empty(tfopt( "tf-date-format-for-users")) ? tfopt( "tf-date-format-for-users") : "Y/m/d";

			// Repeated Fixed Tour


			if(!function_exists('fixed_tour_start_date_changer')) {
				function fixed_tour_start_date_changer($date, $months) {
					if( (count($months) > 0) && !empty($date)) {
						preg_match('/(\d{4})\/(\d{2})\/(\d{2})/', $date, $matches);

						foreach($months as $month) {

							if($month < gmdate('m') && $matches[1] < gmdate('Y')) {
								$year = $matches[1] + 1;

							} else $year = $matches[1];


							$day_selected = gmdate('d', strtotime($date));
							$last_day_of_month = gmdate('t', strtotime(gmdate('Y').'-'.$month.'-01'));
							$matches[2] = $month;
							$changed_date = sprintf("%s/%s/%s", $year, $matches[2], $matches[3]);

							if(($day_selected == "31") && ($last_day_of_month != "31")) {
								$new_months[] = gmdate('Y/m/d', strtotime($changed_date . ' -1 day'));
							} else {
								$new_months[] = $changed_date;
							}
						}
						$new_months[] = $matches[0];
						return $new_months;

					} else return array();
				}
			}

			// Same Day Booking
			$disable_same_day = ! empty( $meta['disable_same_day'] ) ? $meta['disable_same_day'] : '';
			if ( $tour_type == 'fixed' ) {
				if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
					$tf_tour_fixed_avail = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $meta['fixed_availability'] );
					$tf_tour_fixed_date  = unserialize( $tf_tour_fixed_avail );
					$departure_date      = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
					$return_date         = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
					$min_people          = ! empty( $tf_tour_fixed_date['min_seat'] ) ? $tf_tour_fixed_date['min_seat'] : '';
					$max_people          = ! empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
					$repeated_fixed_tour_switch = ! empty( $tf_tour_fixed_date['fixed_availability']["tf-repeat-months-switch"] ) ? $tf_tour_fixed_date['fixed_availability']["tf-repeat-months-switch"] : 0;
					$tour_repeat_months = !empty($tf_tour_fixed_date['fixed_availability']['tf-repeat-months-checkbox']) ? $tf_tour_fixed_date['fixed_availability']['tf-repeat-months-checkbox'] : array();
				} else {
					$departure_date = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
					$return_date    = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
					$min_people     = ! empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
					$max_people     = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
					$repeated_fixed_tour_switch = ! empty( $meta['fixed_availability']["tf-repeat-months-switch"] ) ? $meta['fixed_availability']["tf-repeat-months-switch"] : 0;
					$tour_repeat_months = $repeated_fixed_tour_switch && !empty($meta['fixed_availability']['tf-repeat-months-checkbox']) ? $meta['fixed_availability']['tf-repeat-months-checkbox'] : array();
				}

			} elseif ( $tour_type == 'continuous' ) {

				$disabled_day  = ! empty( $meta['disabled_day'] ) ? $meta['disabled_day'] : '';
				$disable_range = ! empty( $meta['disable_range'] ) ? $meta['disable_range'] : '';
				if ( ! empty( $disable_range ) && gettype( $disable_range ) == "string" ) {
					$disable_range_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $disable_range );
					$disable_range          = unserialize( $disable_range_unserial );

				}
				$disable_specific = ! empty( $meta['disable_specific'] ) ? $meta['disable_specific'] : '';
				$disable_specific = str_replace( ', ', '", "', $disable_specific );

				if ( $custom_avail == true ) {

					$cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';

					if ( ! empty( $cont_custom_date ) && gettype( $cont_custom_date ) == "string" ) {
						$cont_custom_date_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
							return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
						}, $cont_custom_date );
						$cont_custom_date          = unserialize( $cont_custom_date_unserial );

					}

				}

			}

			if( !function_exists( "tf_nearest_default_day" ) ) {
				function tf_nearest_default_day ($dates) {
					if(count($dates) > 0 ) {

						$today = time();
						$nearestDate = null;
						$smallestDifference = null;

						foreach($dates as $date) {
							$dateTime = strtotime($date);
							$difference = abs($today - $dateTime);

							if($dateTime > $today) {
								if ($smallestDifference === null || $difference < $smallestDifference) {
									$smallestDifference = $difference;
									$nearestDate = $date;
								}
							}
						}
						return $nearestDate;
					}
				}
			}

			$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
			$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
			$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
			$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
			$group_price          = ! empty( $meta['group_price'] ) ? $meta['group_price'] : false;
			$adult_price          = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : false;
			$child_price          = ! empty( $meta['child_price'] ) ? $meta['child_price'] : false;
			$infant_price         = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : false;
			$tour_extras          = isset( $meta['tour-extra'] ) ? $meta['tour-extra'] : null;

			if ( ! empty( $tour_extras ) && gettype( $tour_extras ) == "string" ) {

				$tour_extras_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $tour_extras );
				$tour_extras          = unserialize( $tour_extras_unserial );

			}
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
				if ( ! empty( $tf_tour_unserial_custom_time ) ) {
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

            /*$(".tours-check-in-out").flatpickr({
                            enableTime: false,
                            dateFormat: "Y/m/d",
							altInput: true,
                			altFormat: '<?php echo esc_html($tour_date_format_for_users); ?>',
					        <?php
					        // Flatpickt locale for translation
					        tf_flatpickr_locale();

					        if ($tour_type && $tour_type == 'fixed') {
								if( !empty($departure_date) && !empty($tour_repeat_months) ) {
									$enable_repeat_dates = fixed_tour_start_date_changer( $departure_date, $tour_repeat_months );
								}

								if(($repeated_fixed_tour_switch == 1) && ($enable_repeat_dates > 0)) { */?><!--
							// setDetfaultDate: true,
							defaultDate: "<?php /*echo esc_html(tf_nearest_default_day($enable_repeat_dates)) */?>",
							enable: [
								<?php
/*								foreach($enable_repeat_dates as $enable_date) {
								*/?>
								'<?php /*echo esc_html($enable_date); */?>',

								<?php /*} */?>
							],

							<?php /*} else {*/?>
							defaultDate: "<?php /*echo esc_html($departure_date) */?>",
							enable: ["<?php /*echo esc_html($departure_date); */?>"],
							<?php /*} */?>
                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
								instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                            },
					        <?php /*} elseif ($tour_type && $tour_type == 'continuous'){ */?>

                            minDate: "today",
                            disableMobile: "true",

					        <?php /*if ($custom_avail && $custom_avail == true){ */?>

                            enable: [

						        <?php /*foreach ( $cont_custom_date as $item ) {
						        echo '{
                                            from: "' . esc_attr($item["date"]["from"]) . '",
                                            to: "' . esc_attr($item["date"]["to"]) . '"
                                        },';
					        } */?>
                            ],

					        <?php /*}
					        if ($custom_avail == false) {
					        if ($disabled_day || $disable_range || $disable_specific || $disable_same_day) {
					        */?>
                            "disable": [
						        <?php /*if ($disabled_day) { */?>
                                function (date) {
                                    return (date.getDay() === 8 <?php /*foreach ( $disabled_day as $dis_day ) {
								        echo '|| date.getDay() === ' . esc_attr($dis_day) . ' ';
							        } */?>);
                                },
						        <?php /*}
						        if ( $disable_range ) {
							        foreach ( $disable_range as $d_item ) {
								        echo '{
                                                    from: "' . esc_attr($d_item["date"]["from"]) . '",
                                                    to: "' . esc_attr($d_item["date"]["to"]) . '"
                                                },';
							        }
						        }
								if ($disable_same_day) {
									echo '"today"';
									if ($disable_specific) {
										echo ",";
									}
								}
						        if ( $disable_specific ) {
							        echo '"' . esc_attr($disable_specific) . '"';
						        }
						        */?>
                            ],
					        --><?php
/*					        }
					        }
					        }


                            onChange: function (selectedDates, dateStr, instance) {

								instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
								$(".tours-check-in-out").val(instance.altInput.value);
                                $('.tours-check-in-out[type="hidden"]').val(dateStr.replace(/[a-z]+/g, '-') );
                                if (custom_avail == true) {

                                    let times = allowed_times.filter((v) => {
                                        let date_str = Date.parse(dateStr);
                                        let start_date = Date.parse(v.date.from);
                                        let end_date = Date.parse(v.date.to);
                                        return start_date <= date_str && end_date >= date_str;
                                    });
                                    times = times.length > 0 && times[0].times ? times[0].times : null;
                                    populateTimeSelect(times);
                                }

                            },

                        });*/

			$single_tour_form_data['tf_tour_selected_template'] = $tf_tour_selected_template;
			$single_tour_form_data['tour_type'] = $tour_type;
			$single_tour_form_data['allowed_times'] = wp_json_encode( $allowed_times ?? [] );
			$single_tour_form_data['custom_avail'] = $custom_avail;
			$single_tour_form_data['cont_custom_date'] = $cont_custom_date;
//			$single_tour_form_data['flatpickr_locale'] = tf_flatpickr_locale("root");
			$single_tour_form_data['select_time_text'] = esc_html__( "Select Time", "tourfic" );
			$single_tour_form_data['date_format'] = esc_html($tour_date_format_for_users);
//			$single_tour_form_data['flatpickr_locale'] = tf_flatpickr_locale();
            $single_tour_form_data['disabled_day'] = $disabled_day;
            $single_tour_form_data['disable_range'] = $disable_range;
            $single_tour_form_data['disable_specific'] = $disable_specific;
            $single_tour_form_data['disable_same_day'] = $disable_same_day;

			if ($tour_type && $tour_type == 'fixed') {
				if( !empty($departure_date) && !empty($tour_repeat_months) ) {
					$enable_repeat_dates = fixed_tour_start_date_changer( $departure_date, $tour_repeat_months );
				}

				if(($repeated_fixed_tour_switch == 1) && ($enable_repeat_dates > 0)) {
					$single_tour_form_data['defaultDate'] = esc_html(tf_nearest_default_day($enable_repeat_dates));
					$single_tour_form_data['enable'] = array();
					foreach($enable_repeat_dates as $enable_date) {
						$single_tour_form_data['enable'][] = esc_html($enable_date);
					}
				} else {
					$single_tour_form_data['defaultDate'] = esc_html($departure_date);
					$single_tour_form_data['enable'] = esc_html($departure_date);
				}
			} elseif ($tour_type && $tour_type == 'continuous'){
                if ($custom_avail && $custom_avail == true){
                    $single_tour_form_data['enable'] = array();
                    foreach ( $cont_custom_date as $item ) {
                        $single_tour_form_data['enable'][] = array(
                            'from' => esc_attr($item["date"]["from"]),
                            'to' => esc_attr($item["date"]["to"])
                        );
                    }
                }
                if ($custom_avail == false) {
                    if ($disabled_day || $disable_range || $disable_specific || $disable_same_day) {
                        $single_tour_form_data['disable'] = array();
                        if ($disabled_day) {
                            $single_tour_form_data['disable'][] = "function (date) {
                                    return (date.getDay() === 8";
                            foreach ( $disabled_day as $dis_day ) {
                                $single_tour_form_data['disable'][] = '|| date.getDay() === ' . esc_attr($dis_day);
                            }
                            $single_tour_form_data['disable'][] = ");";
                        }
                        if ( $disable_range ) {
                            foreach ( $disable_range as $d_item ) {
                                $single_tour_form_data['disable'][] = array(
                                    'from' => esc_attr($d_item["date"]["from"]),
                                    'to' => esc_attr($d_item["date"]["to"])
                                );
                            }
                        }
                        if ($disable_same_day) {
                            $single_tour_form_data['disable'][] = '"today"';
                            if ($disable_specific) {
                                $single_tour_form_data['disable'][] = ",";
                            }
                        }
                        if ( $disable_specific ) {
                            $single_tour_form_data['disable'][] = '"' . esc_attr($disable_specific) . '"';
                        }
                    }
                }
			}




		}


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
				'tour_form_data'  => isset( $single_tour_form_data ) ? $single_tour_form_data : array(),
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
	global $wp_scripts;

	if(!empty($get_screen)) {

		if ($get_screen->base == "post" && ($get_screen->id == "tf_hotel" || $get_screen->id == "tf_apartment" ||  $get_screen->id == "tf_tours") ) {

			if (wp_script_is('select2', 'enqueued')) {

				wp_dequeue_script('select2');
				wp_deregister_script('select2');
			}

			if ( wp_script_is('acf-color-picker-alpha', 'enqueued') ) {

				$acf_script_handle = 'acf-color-picker-alpha';
				$acf_script_data = $wp_scripts->registered[$acf_script_handle];

				wp_dequeue_script($acf_script_handle);
				
				if( isset( $acf_script_data ) ) {
					wp_enqueue_script( $acf_script_handle, $acf_script_data->src, $acf_script_data->deps, $acf_script_data->ver, true );
				}
			}

			if ( wp_script_is('revbuilder-utils', 'enqueued') ) {

				$rev_script_handle = 'revbuilder-utils';
				$rev_slider_script = $wp_scripts->registered[$rev_script_handle];

				wp_dequeue_script($rev_script_handle);
				
				if( isset( $rev_slider_script ) ) {

					wp_enqueue_script( $rev_script_handle, $rev_slider_script->src, $rev_slider_script->deps, $rev_slider_script->ver, true );
				}
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
	// Apartment Archive Layout
	$tf_apartment_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['apartment-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['apartment-archive'] : 'default';
	// Hotel Single Global Layout
	$tf_hotel_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-hotel'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-hotel'] : 'design-1';
	// Tour Single Global Layout
	$tf_tour_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-tour'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-tour'] : 'design-1';
	// Apartment Single Global Layout
	$tf_apartment_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-apartment'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-apartment'] : 'default';

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

	if(is_post_type_archive('tf_apartment') || is_tax('apartment_location')){
		if('design-1'==$tf_apartment_arc_selected_template){
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

	if(is_singular('tf_apartment')){
		$meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
		$tf_aprtment_layout_conditions = ! empty( $meta['tf_single_apartment_layout_opt'] ) ? $meta['tf_single_apartment_layout_opt'] : 'global';
		if("single"==$tf_aprtment_layout_conditions){
			$tf_apartment_single_template = ! empty( $meta['tf_single_apartment_template'] ) ? $meta['tf_single_apartment_template'] : 'default';
		}
		$tf_apartment_selected_check = !empty($tf_apartment_single_template) ? $tf_apartment_single_template : $tf_apartment_global_template;
		if('design-1'==$tf_apartment_selected_check){
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