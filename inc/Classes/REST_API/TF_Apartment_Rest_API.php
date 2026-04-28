<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_Apartment_Rest_API' ) ) {
	class TF_Apartment_Rest_API extends TF_Rest_API {

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
			add_action( 'rest_api_init', array( $this, 'add_apartment_meta_to_rest_api' ) );
		}

		/*
		 * Get Apartments
		 * @author Foysal
		 */
		public function tf_get_apartments( $request ) {
			$per_page = $request->get_param( 'per_page' ) ? $request->get_param( 'per_page' ) : 10;
			$page     = $request->get_param( 'page' ) ? $request->get_param( 'page' ) : 1;
			$author   = $request->get_param( 'user' ) ? $request->get_param( 'user' ) : get_current_user_id();

			$query_apartments = new WP_Query( array(
				'post_type'      => 'tf_apartment',
				'posts_per_page' => $per_page,
				'post_status'    => array( 'publish', 'pending', 'draft' ),
				'author'         => $this->user_has_role( $author, 'administrator' ) ? '' : $author,
				'paged'          => $page,
			) );
			$apartments       = array();
			if ( $query_apartments->have_posts() ) {
				while ( $query_apartments->have_posts() ) {
					$query_apartments->the_post();
					$apartment_id = get_the_ID();

					$apartment_data                       = array();
					$apartment_data['id']                 = $apartment_id;
					$apartment_data['permalink']          = get_permalink( $apartment_id );
					$apartment_data['title']              = get_the_title( $apartment_id );
					$apartment_data['content']            = get_the_content( $apartment_id );
					$apartment_data['status']             = get_post_status( $apartment_id );
					$apartment_data['author']             = get_the_author_meta( 'display_name', get_post_field( 'post_author', $apartment_id ) );
					$apartment_data['apartment_location'] = $this->tf_get_post_terms( $apartment_id, 'apartment_location' ) ? $this->tf_get_post_terms( $apartment_id, 'apartment_location' ) : '—';
					$apartment_data['apartment_feature']  = $this->tf_get_post_terms( $apartment_id, 'apartment_feature' ) ? $this->tf_get_post_terms( $apartment_id, 'apartment_feature' ) : '—';
					$apartment_data['apartment_type']     = $this->tf_get_post_terms( $apartment_id, 'apartment_type' ) ? $this->tf_get_post_terms( $apartment_id, 'apartment_type' ) : '—';
					$apartment_data['date']               = get_the_date( '', $apartment_id );
					$apartment_data['featured_image']     = get_the_post_thumbnail_url( $apartment_id );
					$apartments[]                         = $apartment_data;
				}
			}
			wp_reset_postdata();
			$apartments = array(
				'apartments' => $apartments,
				'total'      => $query_apartments->found_posts,
			);

			return $apartments;
		}

		/*
		 * Add Apartment
		 * @author Foysal
		 */
		public function tf_add_apartment( $request ) {
			$current_user_id = get_current_user_id();
			$user            = get_user_by( 'id', $current_user_id );

			$apartment_id = wp_insert_post( array(
				'post_title'   => $request['title'],
				'post_content' => $request['content'],
				'post_type'    => 'tf_apartment',
				'post_status'  => $user->has_cap( 'publish_tf_apartments' ) ? 'publish' : 'pending',
				'post_author'  => get_current_user_id(),
			) );

			if ( $apartment_id ) {
				if ( isset( $request['featured_media'] ) ) {
					set_post_thumbnail( $apartment_id, $request['featured_media'] );
				}
				if ( isset( $request['apartmentLocations'] ) && ! empty( $request['apartmentLocations'] ) ) {
					$apartmentLocations = array_map( 'intval', $request['apartmentLocations'] );
					wp_set_object_terms( $apartment_id, $apartmentLocations, 'apartment_location' );
				}
				if ( isset( $request['apartmentFeatures'] ) && ! empty( $request['apartmentFeatures'] ) ) {
					$apartmentFeatures = array_map( 'intval', $request['apartmentFeatures'] );
					wp_set_object_terms( $apartment_id, $apartmentFeatures, 'apartment_feature' );
				}
				if ( isset( $request['apartmentTypes'] ) && ! empty( $request['apartmentTypes'] ) ) {
					$apartmentTypes = array_map( 'intval', $request['apartmentTypes'] );
					wp_set_object_terms( $apartment_id, $apartmentTypes, 'apartment_type' );
				}
				if ( isset( $request['tf_apartment_opt'] ) ) {
					update_post_meta( $apartment_id, 'tf_apartment_opt', $request['tf_apartment_opt'] );
				}
			}

			return $apartment_id;
		}

		/*
		 * Update Apartment
		 * @author Foysal
		 */
		public function tf_update_apartment( $request ) {
			$current_user_id = get_current_user_id();
			$user            = get_user_by( 'id', $current_user_id );

			$apartment_id = $request['id'];
			$apartment    = array(
				'ID'           => $apartment_id,
				'post_title'   => $request['title'],
				'post_content' => $request['content'],
				'post_status'  => $user->has_cap( 'publish_tf_apartments' ) ? 'publish' : 'pending',
				'post_type'    => 'tf_apartment',
				'post_author'  => $this->user_has_role( get_current_user_id(), 'administrator' ) ? $this->tf_get_post_author_id( $apartment_id ) : get_current_user_id(),
			);

			$apartment_id = wp_update_post( $apartment );

			if ( $apartment_id ) {
				if ( isset( $request['featured_media'] ) ) {
					set_post_thumbnail( $apartment_id, $request['featured_media'] );
				}
				if ( isset( $request['apartmentLocations'] ) && ! empty( $request['apartmentLocations'] ) ) {
					$apartmentLocations = array_map( 'intval', $request['apartmentLocations'] );
					wp_set_object_terms( $apartment_id, $apartmentLocations, 'apartment_location' );
				}
				if ( isset( $request['apartmentFeatures'] ) && ! empty( $request['apartmentFeatures'] ) ) {
					$apartmentFeatures = array_map( 'intval', $request['apartmentFeatures'] );
					wp_set_object_terms( $apartment_id, $apartmentFeatures, 'apartment_feature' );
				}
				if ( isset( $request['apartmentTypes'] ) && ! empty( $request['apartmentTypes'] ) ) {
					$apartmentTypes = array_map( 'intval', $request['apartmentTypes'] );
					wp_set_object_terms( $apartment_id, $apartmentTypes, 'apartment_type' );
				}

				if ( isset( $request['tf_apartment_opt'] ) ) {
					update_post_meta( $apartment_id, 'tf_apartment_opt', $request['tf_apartment_opt'] );
				}
			}

			return $apartment_id;
		}

		/*
		 * Update Apartment Status
		 * @auther Foysal
		 */
		public function tf_update_apartment_status( $request ) {
			$current_user_id  = get_current_user_id();
			$id               = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';
			$apartment_status = ! empty( $request->get_param( 'apartment_status' ) ) ? $request->get_param( 'apartment_status' ) : '';
			$user             = get_user_by( 'id', $current_user_id );

			if ( $user->has_cap( 'publish_tf_apartments' ) ) {
				$apartment = array(
					'ID'          => $id,
					'post_status' => $apartment_status,
				);

				$apartment_id = wp_update_post( $apartment );
			}

			return rest_ensure_response( array(
				'status'  => true,
				'message' => 'Apartment status updated successfully.',
			) );
		}

		/*
		 * Add apartment meta to /wp-json/wp/v2/tf_apartment api
		 * @author Foysal
		 */
		function add_apartment_meta_to_rest_api() {
			register_rest_field( 'tf_apartment', 'tf_apartment_opt', array(
				'get_callback' => function ( $post_arr ) {
					$tf_apartment_opt  = get_post_meta( $post_arr['id'], 'tf_apartment_opt', true );
					$unserialize_array = array(
						'map',
					);
					foreach ( $unserialize_array as $item ) {
						if ( ! empty( $tf_apartment_opt[ $item ] ) && is_serialized( $tf_apartment_opt[ $item ] ) ) {
							$tf_apartment_opt[ $item ] = unserialize( $tf_apartment_opt[ $item ] );
						}
					}

					return $tf_apartment_opt;
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//featured image
			register_rest_field( 'tf_apartment', 'featured_image', array(
				'get_callback' => function ( $post_arr ) {
					return get_the_post_thumbnail_url( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//apartment reviews
			register_rest_field( 'tf_apartment', 'reviews', array(
				'get_callback' => function ( $post_arr ) {
					return $this->tf_get_post_review( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//apartment start price
			register_rest_field( 'tf_apartment', 'start_price', array(
				'get_callback' => function ( $post_arr ) {
					return $this->tf_get_apartment_starting_price( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );
		}

		function tf_get_apartment_starting_price( $post_id ) {
			$room_price = [];
			$meta       = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$rooms      = ! empty( $meta['room'] ) ? $meta['room'] : array();
			if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
				$tf_apartment_b_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $rooms );
				$rooms                      = unserialize( $tf_apartment_b_rooms_value );
			}
			if ( ! empty( $rooms ) ):
				foreach ( $rooms as $room ) {
					$pricing_by = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : 1;
					if ( $pricing_by == 1 ) {
						$price        = ! empty( $room['price'] ) ? $room['price'] : '';
						$room_price[] = $price;
					} else if ( $pricing_by == 2 ) {
						$adult_price  = ! empty( $room['adult_price'] ) ? $room['adult_price'] : 0;
						$room_price[] = $adult_price;
					}
				}
			endif;

			return ! empty( $room_price ) ? wc_price( min( $room_price ) ) : '';
		}

		/*
		 * Get Apartment Availability
		 * @author Foysal
		 */
		function tf_get_apartment_availability( $request ) {
			$apartment_id = ! empty( $request->get_param( 'apartment_id' ) ) ? $request->get_param( 'apartment_id' ) : '';

			if ( $apartment_id !== 'undefined' ) {
				$apartment_data        = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
				$apt_availability_data = isset( $apartment_data['apt_availability'] ) && ! empty( $apartment_data['apt_availability'] ) ? json_decode( $apartment_data['apt_availability'], true ) : [];
			} else {
				$apt_availability_data = get_option( 'tf_apt_availability' );
				delete_option( 'tf_apt_availability' );
			}

			if ( ! empty( $apt_availability_data ) && is_array( $apt_availability_data ) ) {
				$apt_availability_data = array_values( $apt_availability_data );
				$apt_availability_data = array_map( function ( $item ) {
					$item['editable'] = false;
					$item['start']    = date( 'Y-m-d', strtotime( $item['check_in'] ) );
					$item['title']    = $item['pricing_type'] == 'per_night' ? esc_html__( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) : esc_html__( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . esc_html__( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] ) . '<br>' . esc_html__( 'Infant: ', 'tourfic' ) . wc_price( $item['infant_price'] );

					if ( $item['status'] == 'unavailable' ) {
						$item['display'] = 'background';
						$item['color']   = '#003c79';
					}

					return $item;
				}, $apt_availability_data );
			} else {
				$apt_availability_data = [];
			}

			return $apt_availability_data;
		}

		/*
		 * Update Apartment Availability
		 * @author Foysal
		 */
		function tf_update_apartment_availability( $request ) {
			$apartment_id     = ! empty( $request->get_param( 'apartment_id' ) ) ? $request->get_param( 'apartment_id' ) : '';
			$pricing_type     = ! empty( $request->get_param( 'pricing_type' ) ) ? $request->get_param( 'pricing_type' ) : '';
			$check_in         = ! empty( $request->get_param( 'check_in' ) ) ? $request->get_param( 'check_in' ) : '';
			$check_out        = ! empty( $request->get_param( 'check_out' ) ) ? $request->get_param( 'check_out' ) : '';
			$price            = ! empty( $request->get_param( 'price' ) ) ? $request->get_param( 'price' ) : '';
			$adult_price      = ! empty( $request->get_param( 'adult_price' ) ) ? $request->get_param( 'adult_price' ) : '';
			$child_price      = ! empty( $request->get_param( 'child_price' ) ) ? $request->get_param( 'child_price' ) : '';
			$infant_price     = ! empty( $request->get_param( 'infant_price' ) ) ? $request->get_param( 'infant_price' ) : '';
			$status           = ! empty( $request->get_param( 'status' ) ) ? $request->get_param( 'status' ) : '';
			$apt_availability = ! empty( $request->get_param( 'apt_availability' ) ) ? $request->get_param( 'apt_availability' ) : '';

			if(empty($id)){
				return rest_ensure_response( [
					'status'  => false,
					'message' => __( 'Publish the Apartment First!', 'tourfic' )
				] );
			}

			if ( empty( $check_in ) || empty( $check_out ) ) {
				return rest_ensure_response( array(
					'status'  => false,
					'message' => esc_html__( 'Please select check in and check out date.', 'tourfic' )
				) );
			}

			$check_in  = strtotime( $check_in );
			$check_out = strtotime( $check_out );
			if ( $check_in > $check_out ) {
				return rest_ensure_response( array(
					'status'  => false,
					'message' => esc_html__( 'Check in date must be less than check out date.', 'tourfic' )
				) );
			}

			$apt_availability_data = [];
			for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
				$tf_apt_date                           = date( 'Y/m/d', $i );
				$tf_apt_data                           = [
					'check_in'     => $tf_apt_date,
					'check_out'    => $tf_apt_date,
					'pricing_type' => $pricing_type,
					'price'        => $price,
					'adult_price'  => $adult_price,
					'child_price'  => $child_price,
					'infant_price' => $infant_price,
					'status'       => $status
				];
				$apt_availability_data[ $tf_apt_date ] = $tf_apt_data;
			}

			if ( ! empty( $apartment_id ) ) {
				$apartment_data   = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
				$apt_availability = gettype($apartment_data['apt_availability']) == 'string' ? json_decode( $apartment_data['apt_availability'], true ) : $apartment_data['apt_availability'];
				if ( isset( $apt_availability ) && ! empty( $apt_availability ) ) {
					$apt_availability_data = array_merge( $apt_availability, $apt_availability_data );
				}
				$apartment_data['apt_availability'] = json_encode( $apt_availability_data );
				update_post_meta( $apartment_id, 'tf_apartment_opt', $apartment_data );
			} else {
				$apt_availability = json_decode( $apt_availability, true );
				if ( isset( $apt_availability ) && ! empty( $apt_availability ) ) {
					$apt_availability_data = array_merge( $apt_availability, $apt_availability_data );
				}
				update_option( 'tf_apt_availability', $apt_availability_data );
			}

			if ( ! empty( $apt_availability_data ) && is_array( $apt_availability_data ) ) {
				$apt_events_data = array_values( $apt_availability_data );
				$apt_events_data = array_map( function ( $item ) {
					$item['editable'] = false;
					$item['start']    = date( 'Y-m-d', strtotime( $item['check_in'] ) );
					$item['title']    = $item['pricing_type'] == 'per_night' ? esc_html__( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) : esc_html__( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . esc_html__( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] ) . '<br>' . esc_html__( 'Infant: ', 'tourfic' ) . wc_price( $item['infant_price'] );

					if ( $item['status'] == 'unavailable' ) {
						$item['display'] = 'background';
						$item['color']   = '#003c79';
					}

					return $item;
				}, $apt_events_data );
			} else {
				$apt_events_data = [];
			}

			return rest_ensure_response( array(
				'status'                   => true,
				'message'                  => esc_html__( 'Availability updated successfully.', 'tourfic' ),
				'apt_availability'         => $apt_events_data,
				'apt_availability_encoded' => json_encode( $apt_availability_data ),
			) );
		}

		/*
		 * Delete Apartment Availability
		 * @author Foysal
		 */
		function tf_delete_apartment_availability( $request ) {
			$apartment_id  = $request->get_param( 'id' );
			$apartment_data = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
			$apartment_data['apt_availability'] = [];

			update_post_meta( $apartment_id, 'tf_apartment_opt', $apartment_data );

			return rest_ensure_response( array(
				'status'  => true,
				'message' => esc_html__( 'Availability Reset Successfully.', 'tourfic' ),
				'apt_availability'         => [],
				'apt_availability_encoded' => json_encode([]),
			) );
		}

		/*
		 * Import Apartment iCal
		 * @author Foysal
		 */
		function tf_apartment_ical_import( $request ) {
			$ical_url     = ! empty( $request->get_param( 'ical_url' ) ) ? $request->get_param( 'ical_url' ) : '';
			$apartment_id = ! empty( $request->get_param( 'apartment_id' ) ) ? $request->get_param( 'apartment_id' ) : '';
			$pricing_type = ! empty( $request->get_param( 'pricing_type' ) ) ? $request->get_param( 'pricing_type' ) : '';

			if ( empty( $ical_url ) ) {
				return rest_ensure_response( array(
					'status'  => false,
					'message' => esc_html__( 'Please enter iCal URL.', 'tourfic' )
				) );
			}

			$apartment_avail_data = get_post_meta( $apartment_id, 'tf_apartment_opt', true );

			try {
				$ical = new TF_FD_ICal_Reader( $ical_url );

				if ( ! empty( $ical ) ) {
					$events = $ical->events();

					if ( ! empty( $events ) && is_array( $events ) ) {
						$date_keys = function_exists( 'tf_ical_get_unavailable_date_keys' ) ? tf_ical_get_unavailable_date_keys( $events ) : array();

						$apt_availability_data = [];
						foreach ( $date_keys as $date_key ) {
							$apt_availability_data[ $date_key ] = array(
								'check_in'     => $date_key,
								'check_out'    => $date_key,
								'pricing_type' => $pricing_type,
								'price'        => '',
								'adult_price'  => '',
								'child_price'  => '',
								'infant_price' => '',
								'status'       => 'unavailable'
							);
						}

						if ( ! empty( $apartment_avail_data ) ) {
							$apt_availability = json_decode( $apartment_avail_data['apt_availability'], true );
							if ( isset( $apt_availability ) && ! empty( $apt_availability ) ) {
								$apt_availability_data = array_merge( $apt_availability, $apt_availability_data );
							}
							$apartment_avail_data['apt_availability'] = json_encode( $apt_availability_data );
							update_post_meta( $apartment_id, 'tf_apartment_opt', $apartment_avail_data );
						}

						if ( ! empty( $apt_availability_data ) && is_array( $apt_availability_data ) ) {
							$apt_events_data = array_values( $apt_availability_data );
							$apt_events_data = array_map( function ( $item ) {
								$item['editable'] = false;
								$item['start']    = date( 'Y-m-d', strtotime( $item['check_in'] ) );
								$item['title']    = $item['pricing_type'] == 'per_night' ? esc_html__( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) : esc_html__( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . esc_html__( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] ) . '<br>' . esc_html__( 'Infant: ', 'tourfic' ) . wc_price( $item['infant_price'] );

								if ( $item['status'] == 'unavailable' ) {
									$item['display'] = 'background';
									$item['color']   = '#003c79';
								}

								return $item;
							}, $apt_events_data );
						} else {
							$apt_events_data = [];
						}

						return rest_ensure_response( array(
							'status'                   => true,
							'message'                  => esc_html__( 'iCal imported successfully.', 'tourfic' ),
							'apt_availability'         => $apt_events_data,
							'apt_availability_encoded' => json_encode( $apt_availability_data ),
						) );
					}
				} else {
					return rest_ensure_response( array(
						'status'  => 'error',
						'message' => esc_html__( 'Failed to create iCal object.', 'tourfic' )
					) );
				}
			} catch ( Exception $e ) {
				return rest_ensure_response( array(
					'status'  => 'error',
					'message' => $e->getMessage()
				) );
			}
		}
	}
}

TF_Apartment_Rest_API::get_instance();
