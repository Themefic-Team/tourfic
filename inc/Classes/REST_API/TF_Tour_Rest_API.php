<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_Tour_Rest_API' ) ) {
	class TF_Tour_Rest_API extends TF_Rest_API {

		/*
		 * instance
		 */
		private static $instance = null;

		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function __construct() {
			parent::__construct();
			add_action( 'rest_api_init', array( $this, 'add_tour_meta_to_rest_api' ) );
		}

		/**
		 * Get total day count for a month/year pair.
		 *
		 * Supports environments where PHP `ext-calendar` is unavailable.
		 *
		 * @param int|string $month Month number.
		 * @param int|string $year  Year number.
		 * @return int
		 */
		private function tf_get_days_in_month( $month, $year ) {
			$month = (int) $month;
			$year  = (int) $year;

			if ( $month < 1 || $month > 12 || $year < 1 ) {
				return 0;
			}

			if ( function_exists( 'cal_days_in_month' ) ) {
				return (int) cal_days_in_month( CAL_GREGORIAN, $month, $year );
			}

			$month_start = strtotime( sprintf( '%04d-%02d-01', $year, $month ) );

			return $month_start ? (int) gmdate( 't', $month_start ) : 0;
		}

		/**
		 * Resolve bulk-edit day numbers for a month.
		 *
		 * When both day numbers and weekdays are selected, dates must satisfy
		 * both conditions instead of being generated in separate passes.
		 *
		 * @param int|string $month        Month number.
		 * @param int|string $year         Year number.
		 * @param mixed      $repeat_days  Selected day numbers.
		 * @param mixed      $repeat_weeks Selected weekdays.
		 * @return array
		 */
		private function tf_get_tour_bulk_edit_days( $month, $year, $repeat_days, $repeat_weeks ) {
			$days_in_month = $this->tf_get_days_in_month( $month, $year );
			if ( $days_in_month <= 0 ) {
				return array();
			}

			$resolved_days = range( 1, $days_in_month );

			if ( ! empty( $repeat_days ) && is_array( $repeat_days ) ) {
				$normalized_days = array();
				foreach ( $repeat_days as $day ) {
					$day = is_scalar( $day ) ? trim( (string) $day ) : '';

					if ( '' === $day || ! preg_match( '/^-?\d+$/', $day ) ) {
						continue;
					}

					$normalized_days[] = (int) $day;
				}

				$resolved_days = array_values(
					array_filter(
						array_unique( $normalized_days ),
						static function( $day ) use ( $days_in_month ) {
							return $day >= 1 && $day <= $days_in_month;
						}
					)
				);
			}

			if ( ! empty( $repeat_weeks ) && is_array( $repeat_weeks ) ) {
				$normalized_weeks = array();
				foreach ( $repeat_weeks as $week_day ) {
					$week_day = is_scalar( $week_day ) ? trim( (string) $week_day ) : '';

					if ( '' === $week_day || ! preg_match( '/^-?\d+$/', $week_day ) ) {
						continue;
					}

					$normalized_weeks[] = (int) $week_day;
				}

				$valid_weeks = array_values(
					array_filter(
						array_unique( $normalized_weeks ),
						static function( $week_day ) {
							return $week_day >= 0 && $week_day <= 6;
						}
					)
				);

				if ( empty( $valid_weeks ) ) {
					return array();
				}

				$month_padded  = str_pad( (string) $month, 2, '0', STR_PAD_LEFT );
				$resolved_days = array_values(
					array_filter(
						$resolved_days,
						static function( $day ) use ( $month_padded, $year, $valid_weeks ) {
							$timestamp = strtotime( sprintf( '%04d-%02d-%02d', (int) $year, (int) $month_padded, (int) $day ) );

							return false !== $timestamp && in_array( (int) gmdate( 'w', $timestamp ), $valid_weeks, true );
						}
					)
				);
			}

			sort( $resolved_days );

			return $resolved_days;
		}

		/**
		 * Get active package indexes from package pricing config.
		 *
		 * @param array $package_pricing Package pricing config.
		 * @param int   $options_count   Number of package options.
		 * @return array<int>
		 */
		private function tf_get_active_tour_package_indexes( $package_pricing, $options_count = 0 ) {
			$active_indexes = array();

			if ( is_array( $package_pricing ) ) {
				foreach ( $package_pricing as $index => $package ) {
					if ( ! is_array( $package ) ) {
						continue;
					}

					$is_active = ! isset( $package['pack_status'] ) || '1' === (string) $package['pack_status'];
					if ( $is_active ) {
						$active_indexes[] = (int) $index;
					}
				}
			}

			if ( empty( $active_indexes ) && $options_count > 0 ) {
				for ( $index = 0; $index < $options_count; $index++ ) {
					$active_indexes[] = (int) $index;
				}
			}

			return array_values( array_unique( $active_indexes ) );
		}

		/**
		 * Sanitize selected package indexes from request.
		 *
		 * @param mixed $selected_packages Selected package values.
		 * @param int   $options_count     Number of package options.
		 * @param array $package_pricing   Package pricing config.
		 * @return array<int>
		 */
		private function tf_sanitize_selected_tour_packages( $selected_packages, $options_count, $package_pricing ) {
			if ( is_string( $selected_packages ) ) {
				$selected_packages = array_filter( array_map( 'trim', explode( ',', $selected_packages ) ) );
			}

			if ( ! is_array( $selected_packages ) ) {
				$selected_packages = array();
			}

			$selected_packages = array_values(
				array_unique(
					array_map(
						'intval',
						array_filter(
							$selected_packages,
							static function( $value ) {
								return '' !== (string) $value;
							}
						)
					)
				)
			);

			if ( $options_count > 0 ) {
				$selected_packages = array_values(
					array_filter(
						$selected_packages,
						static function( $value ) use ( $options_count ) {
							return $value >= 0 && $value < $options_count;
						}
					)
				);
			}

			if ( empty( $selected_packages ) ) {
				$selected_packages = $this->tf_get_active_tour_package_indexes( $package_pricing, $options_count );
			}

			return $selected_packages;
		}

		/**
		 * Build package-specific availability data while preserving non-selected package values.
		 *
		 * @param WP_REST_Request $request            REST request.
		 * @param int             $options_count      Number of package options.
		 * @param array           $package_pricing    Package pricing config.
		 * @param array           $selected_packages  Selected package indexes.
		 * @param array           $existing_date_data Existing date availability data.
		 * @param string          $status             Selected status from form.
		 * @return array
		 */
		private function tf_build_package_availability_data( $request, $options_count, $package_pricing, $selected_packages, $existing_date_data, $status ) {
			$existing_selected_packages = ! empty( $existing_date_data['selected_packages'] ) && is_array( $existing_date_data['selected_packages'] )
				? array_map( 'strval', $existing_date_data['selected_packages'] )
				: array();
			$selected_packages         = array_values( array_unique( array_merge( $existing_selected_packages, array_map( 'strval', $selected_packages ) ) ) );

			$options_data          = array(
				'options_count'      => $options_count,
				'selected_packages'  => array_map( 'strval', $selected_packages ),
			);
			$selected_package_map  = array_flip( array_map( 'intval', $selected_packages ) );
			$active_package_indexes = $this->tf_get_active_tour_package_indexes( $package_pricing, $options_count );
			$has_available_package = false;

			for ( $index = 0; $index < $options_count; $index++ ) {
				$package_option = ! empty( $package_pricing[ $index ] ) && is_array( $package_pricing[ $index ] ) ? $package_pricing[ $index ] : array();
				$is_selected    = isset( $selected_package_map[ $index ] );

				$title_key        = 'tf_option_title_' . $index;
				$type_key         = 'tf_option_pricing_type_' . $index;
				$adult_key        = 'tf_option_adult_price_' . $index;
				$child_key        = 'tf_option_child_price_' . $index;
				$infant_key       = 'tf_option_infant_price_' . $index;
				$group_key        = 'tf_option_group_price_' . $index;
				$group_discount   = 'tf_option_group_discount_' . $index;
				$times_key        = 'tf_option_times_' . $index;
				$package_status   = 'tf_option_status_' . $index;

				$options_data[ $title_key ] = ! empty( $request->get_param( $title_key ) )
					? sanitize_text_field( $request->get_param( $title_key ) )
					: ( ! empty( $existing_date_data[ $title_key ] ) ? $existing_date_data[ $title_key ] : ( ! empty( $package_option['pack_title'] ) ? $package_option['pack_title'] : '' ) );

				$options_data[ $type_key ] = ! empty( $request->get_param( $type_key ) )
					? sanitize_text_field( $request->get_param( $type_key ) )
					: ( ! empty( $existing_date_data[ $type_key ] ) ? $existing_date_data[ $type_key ] : ( ! empty( $package_option['pricing_type'] ) ? $package_option['pricing_type'] : '' ) );

				if ( $is_selected ) {
					$options_data[ $package_status ] = sanitize_text_field( $status );
				} elseif ( isset( $existing_date_data[ $package_status ] ) ) {
					$options_data[ $package_status ] = sanitize_text_field( $existing_date_data[ $package_status ] );
				} else {
					$options_data[ $package_status ] = 'available';
				}

				if ( in_array( $index, $active_package_indexes, true ) && 'unavailable' !== $options_data[ $package_status ] ) {
					$has_available_package = true;
				}

				if ( $is_selected ) {
					$options_data[ $times_key ] = ! empty( $request->get_param( $times_key ) ) ? $request->get_param( $times_key ) : array();
				} else {
					$options_data[ $times_key ] = ! empty( $existing_date_data[ $times_key ] ) ? $existing_date_data[ $times_key ] : array();
				}

				if ( 'group' === $options_data[ $type_key ] ) {
					$default_group_price = ! empty( $package_option['group_tabs'][1]['group_price'] ) ? $package_option['group_tabs'][1]['group_price'] : '';
					if ( $is_selected ) {
						$options_data[ $group_key ]      = ! empty( $request->get_param( $group_key ) ) ? sanitize_text_field( $request->get_param( $group_key ) ) : $default_group_price;
						$options_data[ $group_discount ] = ! empty( $request->get_param( $group_discount ) ) ? $request->get_param( $group_discount ) : array();
					} else {
						$options_data[ $group_key ]      = isset( $existing_date_data[ $group_key ] ) ? $existing_date_data[ $group_key ] : $default_group_price;
						$options_data[ $group_discount ] = ! empty( $existing_date_data[ $group_discount ] ) ? $existing_date_data[ $group_discount ] : array();
					}
				}

				if ( 'person' === $options_data[ $type_key ] ) {
					$default_adult_price  = ! empty( $package_option['adult_tabs'][1]['adult_price'] ) ? $package_option['adult_tabs'][1]['adult_price'] : '';
					$default_child_price  = ! empty( $package_option['child_tabs'][1]['child_price'] ) ? $package_option['child_tabs'][1]['child_price'] : '';
					$default_infant_price = ! empty( $package_option['infant_tabs'][1]['infant_price'] ) ? $package_option['infant_tabs'][1]['infant_price'] : '';

					if ( $is_selected ) {
						$options_data[ $adult_key ] = ! empty( $request->get_param( $adult_key ) ) ? sanitize_text_field( $request->get_param( $adult_key ) ) : $default_adult_price;
						$options_data[ $child_key ] = ! empty( $request->get_param( $child_key ) ) ? sanitize_text_field( $request->get_param( $child_key ) ) : $default_child_price;
						$options_data[ $infant_key ] = ! empty( $request->get_param( $infant_key ) ) ? sanitize_text_field( $request->get_param( $infant_key ) ) : $default_infant_price;
					} else {
						$options_data[ $adult_key ] = isset( $existing_date_data[ $adult_key ] ) ? $existing_date_data[ $adult_key ] : $default_adult_price;
						$options_data[ $child_key ] = isset( $existing_date_data[ $child_key ] ) ? $existing_date_data[ $child_key ] : $default_child_price;
						$options_data[ $infant_key ] = isset( $existing_date_data[ $infant_key ] ) ? $existing_date_data[ $infant_key ] : $default_infant_price;
					}
				}
			}

			$options_data['status'] = $has_available_package ? 'available' : 'unavailable';

			return $options_data;
		}

		/*
		 * Get Tours
		 * @author Foysal
		 */
		public function tf_get_tours( $request ) {
			$per_page = $request->get_param( 'per_page' ) ? $request->get_param( 'per_page' ) : 10;
			$page     = $request->get_param( 'page' ) ? $request->get_param( 'page' ) : 1;
			$author   = $request->get_param( 'user' ) ? $request->get_param( 'user' ) : get_current_user_id();

			$query_tours = new WP_Query( array(
				'post_type'      => 'tf_tours',
				'posts_per_page' => $per_page,
				'post_status'    => array( 'publish', 'pending', 'draft' ),
				'author'         => $this->user_has_role( $author, 'administrator' ) || $this->user_has_role( $author, 'tf_manager' ) ? '' : $author,
				'paged'          => $page,
			) );
			$tours       = array();
			if ( $query_tours->have_posts() ) {
				while ( $query_tours->have_posts() ) {
					$query_tours->the_post();
					$tour_id     = get_the_ID();
					$tour_data   = array();
					$tour_review = $this->tf_get_post_review( $tour_id );
					$start_price = $this->tf_get_tour_starting_price( $tour_id );

					$tour_data['id']               = $tour_id;
					$tour_data['permalink']        = get_permalink( $tour_id );
					$tour_data['title']            = get_the_title( $tour_id );
					$tour_data['content']          = get_the_content( $tour_id );
					$tour_data['status']           = get_post_status( $tour_id );
					$tour_data['author']           = get_the_author_meta( 'display_name', get_post_field( 'post_author', $tour_id ) );
					$tour_data['tour_destination'] = $this->tf_get_post_terms( $tour_id, 'tour_destination' ) ? $this->tf_get_post_terms( $tour_id, 'tour_destination' ) : '—';
					$tour_data['tour_attraction']  = $this->tf_get_post_terms( $tour_id, 'tour_attraction' ) ? $this->tf_get_post_terms( $tour_id, 'tour_attraction' ) : '—';
					$tour_data['tour_activities']  = $this->tf_get_post_terms( $tour_id, 'tour_activities' ) ? $this->tf_get_post_terms( $tour_id, 'tour_activities' ) : '—';
					$tour_data['tour_features']    = $this->tf_get_post_terms( $tour_id, 'tour_features' ) ? $this->tf_get_post_terms( $tour_id, 'tour_features' ) : '—';
					$tour_data['tour_type']        = $this->tf_get_post_terms( $tour_id, 'tour_type' ) ? $this->tf_get_post_terms( $tour_id, 'tour_type' ) : '—';
					$tour_data['date']             = get_the_date( '', $tour_id );
					$tour_data['featured_image']   = get_the_post_thumbnail_url( $tour_id );
					$tour_data['tf_tours_opt']     = get_post_meta( $tour_id, 'tf_tours_opt', true );
					$tour_data['reviews']          = [
						'tour_reviews' => $tour_review['post_reviews'],
						'review_text'  => $tour_review['review_text'],
					];
					$tour_data['start_price']      = $start_price;
					$tours[]                       = $tour_data;
				}
			}
			wp_reset_postdata();
			$tours = array(
				'tours' => $tours,
				'total' => $query_tours->found_posts,
			);

			return $tours;
		}

		/*
		 * Add Tour
		 * @author Foysal
		 */
		public function tf_add_tour( $request ) {
			$current_user_id = get_current_user_id();
			$user            = get_user_by( 'id', $current_user_id );

			$tour_id = wp_insert_post( array(
				'post_title'   => $request['title'],
				'post_content' => $request['content'],
				'post_status'  => $user->has_cap( 'publish_tf_tourss' ) ? 'publish' : 'pending',
				'post_type'    => 'tf_tours',
				'post_author'  => get_current_user_id(),
			) );

			if ( $tour_id ) {
				if ( isset( $request['featured_media'] ) ) {
					set_post_thumbnail( $tour_id, $request['featured_media'] );
				}
				if ( isset( $request['tourDestination'] ) && ! empty( $request['tourDestination'] ) ) {
					$tourDestination = array_map( 'intval', $request['tourDestination'] );
					wp_set_object_terms( $tour_id, $tourDestination, 'tour_destination' );
				}
				if ( isset( $request['tourAttraction'] ) && ! empty( $request['tourAttraction'] ) ) {
					$tourAttraction = array_map( 'intval', $request['tourAttraction'] );
					wp_set_object_terms( $tour_id, $tourAttraction, 'tour_attraction' );
				}
				if ( isset( $request['tourActivities'] ) && ! empty( $request['tourActivities'] ) ) {
					$tourActivities = array_map( 'intval', $request['tourActivities'] );
					wp_set_object_terms( $tour_id, $tourActivities, 'tour_activities' );
				}

				if ( isset( $request['tf_tours_opt'] ) ) {
					update_post_meta( $tour_id, 'tf_tours_opt', $request['tf_tours_opt'] );
				}
			}

			return $tour_id;
		}

		/*
		 * Update Tour
		 * @author Foysal
		 */
		public function tf_update_tour( $request ) {
			$current_user_id = get_current_user_id();
			$user            = get_user_by( 'id', $current_user_id );

			$tour_id     = $request['id'];
			$post_status = get_post_status( $tour_id );
			$tour        = array(
				'ID'           => $tour_id,
				'post_title'   => $request['title'],
				'post_content' => $request['content'],
				'post_status'  => $post_status == 'publish' ? 'publish' : ( $user->has_cap( 'publish_tf_tourss' ) ? 'publish' : 'pending' ),
				'post_type'    => 'tf_tours',
				'post_author'  => $this->user_has_role( get_current_user_id(), 'administrator' ) ? $this->tf_get_post_author_id( $tour_id ) : get_current_user_id(),
			);

			$tour_id = wp_update_post( $tour );

			if ( $tour_id ) {
				if ( isset( $request['featured_media'] ) ) {
					set_post_thumbnail( $tour_id, $request['featured_media'] );
				}
				if ( isset( $request['tourDestination'] ) && ! empty( $request['tourDestination'] ) ) {
					$tourDestination = array_map( 'intval', $request['tourDestination'] );
					wp_set_object_terms( $tour_id, $tourDestination, 'tour_destination' );
				}
				if ( isset( $request['tourAttraction'] ) && ! empty( $request['tourAttraction'] ) ) {
					$tourAttraction = array_map( 'intval', $request['tourAttraction'] );
					wp_set_object_terms( $tour_id, $tourAttraction, 'tour_attraction' );
				}
				if ( isset( $request['tourActivities'] ) && ! empty( $request['tourActivities'] ) ) {
					$tourActivities = array_map( 'intval', $request['tourActivities'] );
					wp_set_object_terms( $tour_id, $tourActivities, 'tour_activities' );
				}

				if ( isset( $request['tf_tours_opt'] ) ) {
					update_post_meta( $tour_id, 'tf_tours_opt', $request['tf_tours_opt'] );
				}
			}

			return $tour_id;
		}

		/*
		 * Update Tour Status
		 * @auther Foysal
		 */
		public function tf_update_tour_status( $request ) {
			$current_user_id = get_current_user_id();
			$id              = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';
			$tour_status     = ! empty( $request->get_param( 'tour_status' ) ) ? $request->get_param( 'tour_status' ) : '';
			$user            = get_user_by( 'id', $current_user_id );

			if ( $user->has_cap( 'publish_tf_tourss' ) ) {
				$tour = array(
					'ID'          => $id,
					'post_status' => $tour_status,
				);

				$tour_id = wp_update_post( $tour );
			}

			return rest_ensure_response( array(
				'status'  => true,
				'message' => 'Tour status updated successfully.',
			) );
		}

		/*
		 * Ticket status update
		 * @auther Foysal
		 */
		public function tf_update_ticket_status( $request ) {
			$id     = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';
			$status = ! empty( $request->get_param( 'status' ) ) ? $request->get_param( 'status' ) : '';

			if ( ! empty( $id ) ) {
				$order_checkin_code = 'tf_' . $id;
				update_option( $order_checkin_code, $status );
			}

			return rest_ensure_response( array(
				'status'  => true,
				'message' => 'Ticket status updated successfully.',
			) );
		}

		/*
		 * Get Tour Availability
		 * @author Foysal
		 */
		function tf_get_tour_availability( $request ) {
			$id = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';

			$package_pricing = array();
			if ( $id !== 'undefined' ) {
				$tour_meta       = get_post_meta( $id, 'tf_tours_opt', true );
				$tour_availability_data = isset( $tour_meta['tour_availability'] ) && ! empty( $tour_meta['tour_availability'] ) ? json_decode( $tour_meta['tour_availability'], true ) : [];
				$package_pricing = ! empty( $tour_meta['package_pricing'] ) && is_array( $tour_meta['package_pricing'] ) ? $tour_meta['package_pricing'] : array();
			} else {
				$tour_availability_data = get_option( 'tf_tour_availability' );
				delete_option( 'tf_tour_availability' );
			}

			if ( ! empty( $tour_availability_data ) && is_array( $tour_availability_data ) ) {
				$tour_availability_data = array_values( $tour_availability_data );
				$tour_availability_data = array_map( function ( $item ) use ( $package_pricing ) {	

					$time_string = '';
					if($item['pricing_type'] == 'group' || $item['pricing_type'] == 'person'){
						$active_times =  $item['allowed_time'] ? $item['allowed_time'] : ''; 
						if(!empty($active_times["time"])){
							$active_time = implode(', ', array_filter($active_times['time']));
						}
						if(!empty($active_time)){
							$time_string = 'Time: '.$active_time;
						}
					}
					if ( $item['pricing_type'] == 'group' ) {
						$item['title'] = __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) . '<br>'. $time_string;
					} elseif ( $item['pricing_type'] == 'person' ) {
						$item['title'] = __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] ). '<br>' . __( 'Infant: ', 'tourfic' ) . wc_price( $item['infant_price'] ). '<br>'. $time_string;
						} elseif ( $item['pricing_type'] == 'package' ) {
							$item['title']       = '';
							$package_lines       = array();
							$package_indexes     = array();
							$selected_packages   = ! empty( $item['selected_packages'] ) && is_array( $item['selected_packages'] ) ? array_map( 'strval', $item['selected_packages'] ) : array();

							if ( ! empty( $package_pricing ) && is_array( $package_pricing ) ) {
								foreach ( $package_pricing as $package_index => $package_data ) {
									if ( ! is_array( $package_data ) || empty( $package_data['pack_status'] ) ) {
										continue;
									}
									$package_indexes[] = (string) $package_index;
								}
							}

							if ( empty( $package_indexes ) && ! empty( $item['options_count'] ) ) {
								for ( $i = 0; $i <= $item['options_count'] - 1; $i ++ ) {
									$package_indexes[] = (string) $i;
								}
							}

							if ( empty( $selected_packages ) ) {
								$selected_packages = $package_indexes;
							}

							foreach ( $package_indexes as $package_index ) {
								if ( ! empty( $selected_packages ) && ! in_array( (string) $package_index, $selected_packages, true ) ) {
									continue;
								}

								$status_key        = 'tf_option_status_' . $package_index;
								$title_key         = 'tf_option_title_' . $package_index;
								$pricing_type_key  = 'tf_option_pricing_type_' . $package_index;
								$group_price_key   = 'tf_option_group_price_' . $package_index;
								$adult_price_key   = 'tf_option_adult_price_' . $package_index;
								$child_price_key   = 'tf_option_child_price_' . $package_index;
								$infant_price_key  = 'tf_option_infant_price_' . $package_index;
								$times_key         = 'tf_option_times_' . $package_index;
								$package_base_data = ! empty( $package_pricing[ $package_index ] ) && is_array( $package_pricing[ $package_index ] ) ? $package_pricing[ $package_index ] : array();

								$package_status = ! empty( $item[ $status_key ] ) ? sanitize_text_field( $item[ $status_key ] ) : '';
								if ( '' === $package_status && ! empty( $item['status'] ) && 'unavailable' === $item['status'] ) {
									$package_status = 'unavailable';
								}

								$package_title = ! empty( $item[ $title_key ] ) ? sanitize_text_field( $item[ $title_key ] ) : '';
								if ( '' === $package_title && ! empty( $package_base_data['pack_title'] ) ) {
									$package_title = sanitize_text_field( $package_base_data['pack_title'] );
								}
								if ( '' === $package_title ) {
									continue;
								}

								$package_pricing_type = ! empty( $item[ $pricing_type_key ] ) ? sanitize_text_field( $item[ $pricing_type_key ] ) : '';
								if ( '' === $package_pricing_type && ! empty( $package_base_data['pricing_type'] ) ) {
									$package_pricing_type = sanitize_text_field( $package_base_data['pricing_type'] );
								}

								$line = __( 'Title: ', 'tourfic' ) . $package_title . '<br>';

								if ( 'unavailable' === $package_status ) {
									$line .= __( 'Status: ', 'tourfic' ) . __( 'Unavailable', 'tourfic' ) . '<br>';
								} elseif ( 'group' === $package_pricing_type ) {
									$group_price = isset( $item[ $group_price_key ] ) && '' !== $item[ $group_price_key ] ? $item[ $group_price_key ] : '';
									if ( '' === $group_price && ! empty( $package_base_data['group_tabs'][1]['group_price'] ) ) {
										$group_price = $package_base_data['group_tabs'][1]['group_price'];
									}
									$line .= __( 'Group Price: ', 'tourfic' ) . wc_price( $group_price ) . '<br>';
								} else {
									$adult_price = isset( $item[ $adult_price_key ] ) && '' !== $item[ $adult_price_key ] ? $item[ $adult_price_key ] : '';
									$child_price = isset( $item[ $child_price_key ] ) && '' !== $item[ $child_price_key ] ? $item[ $child_price_key ] : '';
									$infant_price = isset( $item[ $infant_price_key ] ) && '' !== $item[ $infant_price_key ] ? $item[ $infant_price_key ] : '';

									if ( '' === $adult_price && ! empty( $package_base_data['adult_tabs'][1]['adult_price'] ) ) {
										$adult_price = $package_base_data['adult_tabs'][1]['adult_price'];
									}
									if ( '' === $child_price && ! empty( $package_base_data['child_tabs'][1]['child_price'] ) ) {
										$child_price = $package_base_data['child_tabs'][1]['child_price'];
									}
									if ( '' === $infant_price && ! empty( $package_base_data['infant_tabs'][1]['infant_price'] ) ) {
										$infant_price = $package_base_data['infant_tabs'][1]['infant_price'];
									}

									$line .= __( 'Adult: ', 'tourfic' ) . wc_price( $adult_price ) . '<br>';
									$line .= __( 'Child: ', 'tourfic' ) . wc_price( $child_price ) . '<br>';
									$line .= __( 'Infant: ', 'tourfic' ) . wc_price( $infant_price ) . '<br>';
								}

								$package_active_time = '';
								if ( ! empty( $item[ $times_key ] ) && is_array( $item[ $times_key ] ) && ! empty( $item[ $times_key ]['time'] ) ) {
									$package_active_time = implode( ', ', array_filter( $item[ $times_key ]['time'] ) );
								}
								if ( ! empty( $package_active_time ) ) {
									$line .= 'Time: ' . $package_active_time . '<br>';
								}

								$package_lines[] = $line;
							}

							if ( ! empty( $package_lines ) ) {
								$item['title'] = implode( '<br>', $package_lines );
							}
							if ( empty( $item['title'] ) && ! empty( $item['status'] ) && 'unavailable' === $item['status'] ) {
								$item['title'] = __( 'Unavailable', 'tourfic' );
							}
						} elseif ( $item['pricing_type'] == 'group') {
						$item['title'] = '';
						if ( ! empty( $item['options_count'] ) ) {
							for ( $i = 0; $i <= $item['options_count'] - 1; $i ++ ) {
								if( !empty($item['tf_option_title_'.$i]) && !empty($item['tf_option_group_price_'.$i]) ){
									$item['title'] .= __( 'Title: ', 'tourfic' ) . $item['tf_option_title_'.$i] . '<br>';
									$item['title'] .= __( 'Price: ', 'tourfic' ) . wc_price($item['tf_option_group_price_'.$i]). '<br><br>';
								}
							}
						}
						$item['title'] .=  $time_string;
					}

					if(!empty($item['title'])){
						$item['start'] = gmdate( 'Y-m-d', strtotime( $item['check_in'] ) );
						$item['end'] = gmdate('Y-m-d', strtotime($item['check_out'] . ' +1 day'));
					}
					if ( $item['status'] == 'unavailable' ) {
						$item['customClass']   = 'tf_tour_disable_date';
					}

					return $item;
				}, $tour_availability_data );
			} else {
				$tour_availability_data = [];
			}

			/* if ( ! empty( $tour_avail_data ) && is_array( $tour_avail_data ) ) {
				$tour_avail_data = array_values( $tour_avail_data );
				$tour_avail_data = array_map( function ( $item ) {
					$item['editable'] = false;
					$item['start']    = date( 'Y-m-d', strtotime( $item['check_in'] ) );
					if ( $item['pricing_type'] == 'group' ) {
						$item['title'] = __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] );

					} elseif ( $item['pricing_type'] == 'person' ) {
						$item['title'] = __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] ) . '<br>' . __( 'Infant: ', 'tourfic' ) . wc_price( $item['infant_price'] );
					
					} elseif ( $item['pricing_type'] == 'package' ) {
						$item['title'] = '';
						if ( ! empty( $item['options_count'] ) ) {
							for ( $i = 0; $i <= $item['options_count'] - 1; $i ++ ) {
								if ( $item[ 'tf_room_option_' . $i ] == '1' && $item[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
									$item['title'] .= __( 'Title: ', 'tourfic' ) . $item[ 'tf_option_title_' . $i ] . '<br>';
									$item['title'] .= __( 'Price: ', 'tourfic' ) . wc_price( $item[ 'tf_option_room_price_' . $i ] ) . '<br><br>';
								} else if ( $item[ 'tf_room_option_' . $i ] == '1' && $item[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
									$item['title'] .= __( 'Title: ', 'tourfic' ) . $item[ 'tf_option_title_' . $i ] . '<br>';
									$item['title'] .= __( 'Adult: ', 'tourfic' ) . wc_price( $item[ 'tf_option_adult_price_' . $i ] ) . '<br>';
									$item['title'] .= __( 'Child: ', 'tourfic' ) . wc_price( $item[ 'tf_option_child_price_' . $i ] ) . '<br><br>';
								}
							}
						}
					}

					if ( $item['status'] == 'unavailable' ) {
						$item['display'] = 'background';
						$item['color']   = '#003c79';
					}

					return $item;
				}, $tour_avail_data );
			} else {
				$tour_avail_data = [];
			} */

			return $tour_availability_data;
		}

		/*
		 * Update Tour Availability
		 * @author Foysal
		 */
		function tf_update_tour_availability( $request ) {
			$date_format         = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
			$tour_id        	 = ! empty( $request->get_param('id') ) ? sanitize_text_field( $request->get_param('id') ) : '';
			$check_in            = ! empty( $request->get_param('check_in') ) ? sanitize_text_field( $request->get_param('check_in') ) : '';
			$check_out           = ! empty( $request->get_param('check_out') ) ? sanitize_text_field( $request->get_param('check_out') ) : '';
			$status              = ! empty( $request->get_param('status') ) ? sanitize_text_field( $request->get_param('status') ) : '';
			$pricing_type        = ! empty( $request->get_param('pricing_type') ) ? sanitize_text_field( $request->get_param('pricing_type') ) : '';
			$tf_tour_price       = ! empty( $request->get_param('price') ) ? sanitize_text_field( $request->get_param('price') ) : '';
			$tf_tour_adult_price = ! empty( $request->get_param('adult_price') ) ? sanitize_text_field( $request->get_param('adult_price') ) : '';
			$tf_tour_child_price = ! empty( $request->get_param('child_price') ) ? sanitize_text_field( $request->get_param('child_price') ) : '';
			$tf_tour_infant_price= ! empty( $request->get_param('infant_price') ) ? sanitize_text_field( $request->get_param('infant_price') ) : '';
			$tour_availability   = ! empty( $request->get_param('tour_availability') ) ? sanitize_text_field( $request->get_param('tour_availability') ) : '';
			$options_count       = ! empty( $request->get_param('options_count') ) ? intval( sanitize_text_field( $request->get_param('options_count') ) ) : 0;
			$tf_tour_min_person	 = ! empty( $request->get_param('min_person') ) ? sanitize_text_field( $request->get_param('min_person') ) : '';
			$tf_tour_max_person	 = ! empty( $request->get_param('max_person') ) ? sanitize_text_field( $request->get_param('max_person') ) : '';
			$tf_tour_max_capacity= ! empty( $request->get_param('max_capacity') ) ? sanitize_text_field( $request->get_param('max_capacity') ) : '';
			$tf_tour_allowed_time = ! empty( $request->get_param('allowed_time') ) ? $request->get_param('allowed_time') : []; 
			$tf_tour_repeat_month =! empty( $request->get_param('tf_tour_repeat_month') ) ? $request->get_param('tf_tour_repeat_month') : '';
			$tf_tour_repeat_year = ! empty( $request->get_param('tf_tour_repeat_year') ) ? $request->get_param('tf_tour_repeat_year') : '';
			$tf_tour_repeat_week = ! empty( $request->get_param('tf_tour_repeat_week') ) ? $request->get_param('tf_tour_repeat_week') : '';
			$tf_tour_repeat_day = ! empty( $request->get_param('tf_tour_repeat_day') ) ? $request->get_param('tf_tour_repeat_day') : '';
			$selected_packages  = $request->get_param( 'selected_packages' );
			$bulk_edit_option = ! empty( $request->get_param('bulk_edit_option') ) ? $request->get_param('bulk_edit_option') : false; 

			if ( empty( $tour_id ) ) {
				return rest_ensure_response( [
					'status'  => false,
					'message' => __( 'Publish the Tour First!', 'tourfic' )
				] );
			}
			
			if ( !$bulk_edit_option && (empty( $check_in ) || empty( $check_out )) ) {
				return rest_ensure_response( array(
					'status'  => false,
					'message' => esc_html__( 'Please select check in and check out date.', 'tourfic' )
				) );
			}
			if ( $bulk_edit_option && empty( $tf_tour_repeat_month) ) {
				return rest_ensure_response( array(
					'status'  => false,
					'message' => esc_html__( 'Please select the months.', 'tourfic' )
				) );
			}
			if ( $bulk_edit_option && empty( $tf_tour_repeat_year) ) {
				return rest_ensure_response( array(
					'status'  => false,
					'message' => esc_html__( 'Please select the years.', 'tourfic' )
				) );
			}

			$check_in  = strtotime( $check_in );
			$check_out = strtotime( $check_out );
			if ( !$bulk_edit_option && $check_in > $check_out ) {
				return rest_ensure_response( array(
					'status'  => false,
					'message' => esc_html__( 'Check in date must be less than check out date.', 'tourfic' )
				) );
			}

			$meta = get_post_meta( $tour_id, 'tf_tours_opt', true );
			$package_pricing = ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : '';
			if ( empty( $options_count ) && is_array( $package_pricing ) ) {
				$options_count = count( $package_pricing );
			}
			$selected_packages = $this->tf_sanitize_selected_tour_packages( $selected_packages, $options_count, $package_pricing );

			$existing_tour_availability = array();
			if ( ! empty( $tour_id ) ) {
				$tour_meta_existing = get_post_meta( $tour_id, 'tf_tours_opt', true );
				$existing_tour_availability = ! empty( $tour_meta_existing['tour_availability'] ) && 'string' === gettype( $tour_meta_existing['tour_availability'] )
					? json_decode( $tour_meta_existing['tour_availability'], true )
					: ( ! empty( $tour_meta_existing['tour_availability'] ) && is_array( $tour_meta_existing['tour_availability'] ) ? $tour_meta_existing['tour_availability'] : array() );
			} else {
				$existing_tour_availability = json_decode( stripslashes( $tour_availability ), true );
			}

			if ( ! is_array( $existing_tour_availability ) ) {
				$existing_tour_availability = array();
			}

			if($pricing_type == 'person') {
				if(empty($tf_tour_adult_price)){
					$tf_tour_adult_price = !empty( $meta['adult_price'] ) ? $meta['adult_price'] : '';
				}
				if(empty($tf_tour_child_price)){
					$tf_tour_child_price = !empty( $meta['child_price'] ) ? $meta['child_price'] : '';
				}
				if(empty($tf_tour_infant_price)){
					$tf_tour_infant_price = !empty( $meta['infant_price'] ) ? $meta['infant_price'] : '';
				}
			}
			if($pricing_type == 'group') {
				if(empty($tf_tour_price)){
					$tf_tour_price = !empty( $meta['group_price'] ) ? $meta['group_price'] : '';
				}
			}

			$tour_availability_data = [];

			if ( $bulk_edit_option ) {
				if (!empty($tf_tour_repeat_year)) {
					foreach ($tf_tour_repeat_year as $year) {
						if (!empty($tf_tour_repeat_month)) {
							foreach ($tf_tour_repeat_month as $month) {
								$month_padded  = str_pad( (string) $month, 2, '0', STR_PAD_LEFT );
								$resolved_days = $this->tf_get_tour_bulk_edit_days( $month_padded, $year, $tf_tour_repeat_day, $tf_tour_repeat_week );

								foreach ( $resolved_days as $day ) {
									$day_padded       = str_pad( (string) $day, 2, '0', STR_PAD_LEFT );
									$new_check_in_str = "$year-$month_padded-$day_padded";
									$new_check_in     = strtotime( $new_check_in_str );

									if ( false === $new_check_in ) {
										continue;
									}

									$tf_checkin_date = gmdate( 'Y/m/d', $new_check_in );
									$tf_tour_date    = $tf_checkin_date . ' - ' . $tf_checkin_date;
									$tf_tour_data    = [
										'check_in'     => $tf_checkin_date,
										'check_out'    => $tf_checkin_date,
										'pricing_type' => $pricing_type,
										'price'        => $tf_tour_price,
										'adult_price'  => $tf_tour_adult_price,
										'child_price'  => $tf_tour_child_price,
										'infant_price' => $tf_tour_infant_price,
										'min_person'   => $tf_tour_min_person,
										'max_person'   => $tf_tour_max_person,
										'max_capacity' => $tf_tour_max_capacity,
										'allowed_time' => $tf_tour_allowed_time,
										'status'       => $status
									];

									if ( $pricing_type == 'package' && $options_count > 0 ) {
										$existing_date_data   = ! empty( $existing_tour_availability[ $tf_tour_date ] ) && is_array( $existing_tour_availability[ $tf_tour_date ] ) ? $existing_tour_availability[ $tf_tour_date ] : array();
										$options_data         = $this->tf_build_package_availability_data( $request, $options_count, $package_pricing, $selected_packages, $existing_date_data, $status );
										$tf_tour_data         = array_merge( $tf_tour_data, $options_data );
										$tf_tour_data['status'] = ! empty( $options_data['status'] ) ? $options_data['status'] : $tf_tour_data['status'];
									}

									$tour_availability_data[ $tf_tour_date ] = $tf_tour_data;
								}
							}
						}
					}
				}
			}else{
				$tf_checkin_date = gmdate( 'Y/m/d', $check_in );
				$tf_checkout_date = gmdate( 'Y/m/d', $check_out );
				$tf_tour_date = $tf_checkin_date . ' - ' . $tf_checkout_date;
				$tf_tour_data = [
					'check_in'     => $tf_checkin_date,
					'check_out'    => $tf_checkout_date,
					'pricing_type' => $pricing_type,
					'price'        => $tf_tour_price,
					'adult_price'  => $tf_tour_adult_price,
					'child_price'  => $tf_tour_child_price,
					'infant_price' => $tf_tour_infant_price,
					'min_person'   => $tf_tour_min_person,
					'max_person'   => $tf_tour_max_person,
					'max_capacity' => $tf_tour_max_capacity,
					'allowed_time' => $tf_tour_allowed_time,
					'status'       => $status
				];

				if ( $pricing_type == 'package' && $options_count > 0 ) {
					$existing_date_data = ! empty( $existing_tour_availability[ $tf_tour_date ] ) && is_array( $existing_tour_availability[ $tf_tour_date ] ) ? $existing_tour_availability[ $tf_tour_date ] : array();
					$options_data = $this->tf_build_package_availability_data( $request, $options_count, $package_pricing, $selected_packages, $existing_date_data, $status );
					$tf_tour_data = array_merge( $tf_tour_data, $options_data );
					$tf_tour_data['status'] = ! empty( $options_data['status'] ) ? $options_data['status'] : $tf_tour_data['status'];
				}

				$tour_availability_data[$tf_tour_date] = $tf_tour_data;
			}

			if ( ! empty( $tour_id ) ) {
				$tour_meta  = get_post_meta( $tour_id, 'tf_tours_opt', true );
				$tour_availability = gettype($tour_meta['tour_availability']) == 'string' ? json_decode( $tour_meta['tour_availability'], true ) : $tour_meta['tour_availability'];
				if ( isset( $tour_availability ) && ! empty( $tour_availability ) ) {
					$tour_availability_data = array_merge( $tour_availability, $tour_availability_data );
				}
				$tour_meta['tour_availability'] = wp_json_encode( $tour_availability_data );
				update_post_meta( $tour_id, 'tf_tours_opt', $tour_meta );
			} else {
				$tour_availability = json_decode( stripslashes( $tour_availability ), true );
				if ( isset( $tour_availability ) && ! empty( $tour_availability ) ) {
					$tour_availability_data = array_merge( $tour_availability, $tour_availability_data );
				}
				update_option( 'tf_tour_availability', $tour_availability_data );
			}

			if ( ! empty( $tour_availability_data ) && is_array( $tour_availability_data ) ) {
				$tour_events_data = array_values( $tour_availability_data );
				$tour_events_data = array_map( function ( $item ) {	

					$time_string = '';
					if($item['pricing_type'] == 'group' || $item['pricing_type'] == 'person'){
						$active_times =  $item['allowed_time'] ? $item['allowed_time'] : ''; 
						if(!empty($active_times["time"])){
							$active_time = implode(', ', array_filter($active_times['time']));
						}
						if(!empty($active_time)){
							$time_string = 'Time: '.$active_time;
						}
					}
					if ( $item['pricing_type'] == 'group' ) {
						$item['title'] = __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) . '<br>'. $time_string;
					} elseif ( $item['pricing_type'] == 'person' ) {
						$item['title'] = __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] ). '<br>' . __( 'Infant: ', 'tourfic' ) . wc_price( $item['infant_price'] ). '<br>'. $time_string;
					} elseif ( $item['pricing_type'] == 'package' ) {
						$item['title'] = '';
						if ( ! empty( $item['options_count'] ) ) {
							for ( $i = 0; $i <= (int) $item['options_count'] - 1; $i ++ ) {
								$package_active_times =  !empty($item['tf_option_times_'.$i]) ? $item['tf_option_times_'.$i] : ''; 
								if(!empty($package_active_times["time"])){
									$package_active_time = implode(', ', array_filter($package_active_times['time']));
								}

								if ( $item['tf_option_pricing_type_'.$i] == 'group') {
									$item['title'] .= __( 'Title: ', 'tourfic' ) . $item['tf_option_title_'.$i] . '<br>';
									$item['title'] .= __( 'Group Price: ', 'tourfic' ) . wc_price($item['tf_option_group_price_'.$i]). '<br>';
									$item['title'] .=  !empty($package_active_time) ? 'Time: '.$package_active_time. '<br><br>' : '';
								} else if($item['tf_option_pricing_type_'.$i] == 'person'){
									$item['title'] .= __( 'Title: ', 'tourfic' ) . $item['tf_option_title_'.$i] . '<br>';
									$item['title'] .= __( 'Adult: ', 'tourfic' ) . wc_price($item['tf_option_adult_price_'.$i]). '<br>';
									$item['title'] .= __( 'Child: ', 'tourfic' ) . wc_price($item['tf_option_child_price_'.$i]). '<br>';
									$item['title'] .= __( 'Infant: ', 'tourfic' ) . wc_price($item['tf_option_infant_price_'.$i]). '<br>';
									$item['title'] .=  !empty($package_active_time) ? 'Time: '.$package_active_time. '<br><br>' : '';
								}
							}
						}
					} elseif ( $item['pricing_type'] == 'group') {
						$item['title'] = '';
						if ( ! empty( $item['options_count'] ) ) {
							for ( $i = 0; $i <= $item['options_count'] - 1; $i ++ ) {
								if( !empty($item['tf_option_title_'.$i]) && !empty($item['tf_option_group_price_'.$i]) ){
									$item['title'] .= __( 'Title: ', 'tourfic' ) . $item['tf_option_title_'.$i] . '<br>';
									$item['title'] .= __( 'Price: ', 'tourfic' ) . wc_price($item['tf_option_group_price_'.$i]). '<br><br>';
								}
							}
						}
						$item['title'] .=  $time_string;
					}

					if(!empty($item['title'])){
						$item['start'] = gmdate( 'Y-m-d', strtotime( $item['check_in'] ) );
						$item['end'] = gmdate('Y-m-d', strtotime($item['check_out'] . ' +1 day'));
					}
					if ( $item['status'] == 'unavailable' ) {
						$item['customClass']   = 'tf_tour_disable_date';
					}

					return $item;
				}, $tour_events_data );
			} else {
				$tour_events_data = [];
			}

			return rest_ensure_response( array(
				'status'             => true,
				'message'            => esc_html__( 'Availability updated successfully.', 'tourfic' ),
				'tour_availability'         => $tour_events_data,
				'tour_availability_encoded' => json_encode( $tour_availability_data ),
			) );
		}

		/*
		 * Delete Tour Availability
		 * @author Foysal
		 */
		function tf_delete_tour_availability( $request ) {
			$tour_id  = $request->get_param( 'id' );
			$tour_data = get_post_meta( $tour_id, 'tf_tours_opt', true );
			$tour_data['tour_availability'] = [];

			update_post_meta( $tour_id, 'tf_tours_opt', $tour_data );

			return rest_ensure_response( array(
				'status'  => true,
				'message' => esc_html__( 'Availability Reset Successfully.', 'tourfic' ),
				'tour_availability'         => [],
				'tour_availability_encoded' => json_encode([]),
			) );
		}

		function tf_get_tour_starting_price( $post_id ) {
			$meta                = get_post_meta( $post_id, 'tf_tours_opt', true );
			$pricing_rule        = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
			$group_price         = ! empty( $meta['group_price'] ) ? $meta['group_price'] : false;
			$adult_price         = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : false;
			$child_price         = ! empty( $meta['child_price'] ) ? $meta['child_price'] : false;
			$infant_price        = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : false;
			$disable_adult_price = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;

			if ( $pricing_rule && $pricing_rule == 'group' ) {
				$price = $group_price;
			} elseif ( $pricing_rule && ! $disable_adult_price && $pricing_rule == 'person' ) {
				$price = $adult_price;
			} else {
				$price = $child_price;
			}

			return ! empty( $price ) ? wc_price( $price ) : '';
		}

		/*
		 * Add tour meta to /wp-json/wp/v2/tf_tours api
		 * @author Foysal
		 */
		function add_tour_meta_to_rest_api() {
			register_rest_field( 'tf_tours', 'tf_tours_opt', array(
				'get_callback' => function ( $post_arr ) {
					$tf_tours_opt      = get_post_meta( $post_arr['id'], 'tf_tours_opt', true );
					$unserialize_array = array( 'location', 'fixed_availability' );
					foreach ( $unserialize_array as $item ) {
						if ( ! empty( $tf_tours_opt[ $item ] ) && is_serialized( $tf_tours_opt[ $item ] ) ) {
							$tf_tours_opt[ $item ] = unserialize( $tf_tours_opt[ $item ] );
						}
					}

					return $tf_tours_opt;
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//featured image
			register_rest_field( 'tf_tours', 'featured_image', array(
				'get_callback' => function ( $post_arr ) {
					return get_the_post_thumbnail_url( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//tour reviews
			register_rest_field( 'tf_tours', 'reviews', array(
				'get_callback' => function ( $post_arr ) {
					return $this->tf_get_post_review( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//tour start price
			register_rest_field( 'tf_tours', 'start_price', array(
				'get_callback' => function ( $post_arr ) {
					return $this->tf_get_tour_starting_price( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );
		}
	}
}

TF_Tour_Rest_API::get_instance();
