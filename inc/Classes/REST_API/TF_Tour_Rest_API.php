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
