<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_Room_Rest_API' ) ) {
	class TF_Room_Rest_API extends TF_Rest_API {

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
			add_action( 'rest_api_init', array( $this, 'add_room_meta_to_rest_api' ) );
		}

		/*
		 * Add room meta to /wp-json/wp/v2/tf_room api
		 * @author Foysal
		 */
		function add_room_meta_to_rest_api() {
			register_rest_field( 'tf_room', 'tf_room_opt', array(
				'get_callback' => function ( $post_arr ) {
					$tf_room_opt = get_post_meta( $post_arr['id'], 'tf_room_opt', true );

					return $tf_room_opt;
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//featured image
			register_rest_field( 'tf_room', 'featured_image', array(
				'get_callback' => function ( $post_arr ) {
					return get_the_post_thumbnail_url( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );
		}

		/*
		 * Get Hotel Rooms
		 * @author Foysal
		 */
		public function tf_get_hotel_rooms( $request ) {
			$hotel_id = ! empty( $request->get_param( 'hotel_id' ) ) ? $request->get_param( 'hotel_id' ) : '';
			$args     = array(
				'post_type'      => 'tf_room',
				'posts_per_page' => - 1,
			);

			$rooms = get_posts( $args );

			$hotel_rooms = array();
			foreach ( $rooms as $room ) {
				$room_meta = get_post_meta( $room->ID, 'tf_room_opt', true );
				if ( ! empty( $room_meta['tf_hotel'] ) && $room_meta['tf_hotel'] == $hotel_id ) {
					$hotel_rooms[ $room->ID ] = array(
						'id'    => $room->ID,
						'title' => get_the_title( $room->ID ),
					);
				}
			}

			return $hotel_rooms;
		}

		/*
		 * Get Rooms
		 * @author Foysal
		 */
		public function tf_get_rooms( $request ) {
			$per_page = $request->get_param( 'per_page' ) ? $request->get_param( 'per_page' ) : 10;
			$page     = $request->get_param( 'page' ) ? $request->get_param( 'page' ) : 1;
			$author   = $request->get_param( 'user' ) ? $request->get_param( 'user' ) : get_current_user_id();

			$query_rooms = new WP_Query( array(
				'post_type'      => 'tf_room',
				'posts_per_page' => $per_page,
				'post_status'    => array( 'publish', 'pending', 'draft' ),
				'author'         => $this->user_has_role( $author, 'administrator' ) || $this->user_has_role( $author, 'tf_manager' ) ? '' : $author,
				'paged'          => $page,
			) );
			$rooms       = array();
			if ( $query_rooms->have_posts() ) {
				while ( $query_rooms->have_posts() ) {
					$query_rooms->the_post();
					$room_id   = get_the_ID();
					$room_meta = get_post_meta( $room_id, 'tf_room_opt', true );
					$hotel_id  = ! empty( $room_meta['tf_hotel'] ) ? $room_meta['tf_hotel'] : '';

					$room_data                   = array();
					$room_data['id']             = $room_id;
					$room_data['permalink']      = get_permalink( $room_id );
					$room_data['title']          = get_the_title( $room_id );
					$room_data['content']        = get_the_content( $room_id );
					$room_data['status']         = get_post_status( $room_id );
					$room_data['author']         = get_the_author_meta( 'display_name', get_post_field( 'post_author', $room_id ) );
					$room_data['date']           = get_the_date( '', $room_id );
					$room_data['featured_image'] = get_the_post_thumbnail_url( $room_id );
					$room_data['tf_room_opt']    = $room_meta;
					$room_data['hotel_id']       = $hotel_id;
					$room_data['hotel_title']    = ! empty( $room_meta['tf_hotel'] ) ? get_the_title( $hotel_id ) : '';
					$rooms[]                     = $room_data;
				}
			}
			wp_reset_postdata();
			$rooms = array(
				'rooms' => $rooms,
				'total' => $query_rooms->found_posts,
			);

			return $rooms;
		}

		/*
		 * Add Room
		 * @author Foysal
		 */
		public function tf_add_room( $request ) {
			$current_user_id = get_current_user_id();
			$user            = get_user_by( 'id', $current_user_id );

			$room_id = wp_insert_post( array(
				'post_title'   => $request['title'],
				'post_content' => $request['content'],
				'post_type'    => 'tf_room',
				'post_status'  => $user->has_cap( 'publish_tf_hotels' ) ? 'publish' : 'pending',
				'post_author'  => get_current_user_id(),
			) );

			if ( $room_id ) {
				if ( isset( $request['featured_media'] ) ) {
					set_post_thumbnail( $room_id, $request['featured_media'] );
				}

				if ( isset( $request['tf_room_opt'] ) ) {
					update_post_meta( $room_id, 'tf_room_opt', $request['tf_room_opt'] );
				}
			}

			return $room_id;
		}

		/*
		 * Update Room
		 * @author Foysal
		 */
		public function tf_update_room( $request ) {
			$current_user_id = get_current_user_id();
			$user            = get_user_by( 'id', $current_user_id );

			$room_id     = $request['id'];
			$post_status = get_post_status( $room_id );
			$room        = array(
				'ID'           => $room_id,
				'post_title'   => $request['title'],
				'post_content' => $request['content'],
				'post_status'  => $post_status == 'publish' ? 'publish' : ( $user->has_cap( 'publish_tf_hotels' ) ? 'publish' : 'pending' ),
				'post_type'    => 'tf_room',
				'post_author'  => $this->user_has_role( get_current_user_id(), 'administrator' ) ? $this->tf_get_post_author_id( $room_id ) : get_current_user_id(),
			);

			$room_id = wp_update_post( $room );

			if ( $room_id ) {
				if ( isset( $request['featured_media'] ) ) {
					set_post_thumbnail( $room_id, $request['featured_media'] );
				}

				if ( isset( $request['tf_room_opt'] ) ) {
					update_post_meta( $room_id, 'tf_room_opt', $request['tf_room_opt'] );
				}
			}

			return $room_id;
		}

		/*
		 * Update Room Status
		 * @auther Foysal
		 */
		public function tf_update_room_status( $request ) {
			$current_user_id = get_current_user_id();
			$id              = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';
			$room_status     = ! empty( $request->get_param( 'room_status' ) ) ? $request->get_param( 'room_status' ) : '';
			$user            = get_user_by( 'id', $current_user_id );

			if ( $user->has_cap( 'publish_tf_hotels' ) ) {
				$room = array(
					'ID'          => $id,
					'post_status' => $room_status,
				);

				$room_id = wp_update_post( $room );
			}

			return rest_ensure_response( array(
				'status'  => true,
				'message' => 'Room status updated successfully.',
			) );
		}

		/*
		 * Get Room Availability
		 * @author Foysal
		 */
		function tf_get_room_availability( $request ) {
			$room_id = ! empty( $request->get_param( 'room_id' ) ) ? $request->get_param( 'room_id' ) : '';

			if ( $room_id !== 'undefined' ) {
				$room_meta       = get_post_meta( $room_id, 'tf_room_opt', true );
				$room_avail_data = isset( $room_meta['avail_date'] ) && ! empty( $room_meta['avail_date'] ) ? json_decode( $room_meta['avail_date'], true ) : [];
			} else {
				$room_avail_data = get_option( 'tf_hotel_avail_date' );
				delete_option( 'tf_hotel_avail_date' );
			}

			if ( ! empty( $room_avail_data ) && is_array( $room_avail_data ) ) {
				$room_avail_data = array_values( $room_avail_data );
				$room_avail_data = array_map( function ( $item ) {
					$item['editable'] = false;
					$item['start']    = date( 'Y-m-d', strtotime( $item['check_in'] ) );
					$item['title']    = $item['price_by'] == '1' ? esc_html__( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) : esc_html__( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . esc_html__( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );

					if ( $item['status'] == 'unavailable' ) {
						$item['display'] = 'background';
						$item['color']   = '#003c79';
					}

					return $item;
				}, $room_avail_data );
			} else {
				$room_avail_data = [];
			}

			return $room_avail_data;
		}

		/*
		 * Update Hotel Room Availability
		 * @author Foysal
		 */
		function tf_update_room_availability( $request ) {
			$hotel_id    = ! empty( $request->get_param( 'hotel_id' ) ) ? $request->get_param( 'hotel_id' ) : '';
			$room_index  = intval( $request->get_param( 'room_index' ) );
			$price_by    = ! empty( $request->get_param( 'price_by' ) ) ? $request->get_param( 'price_by' ) : '';
			$check_in    = ! empty( $request->get_param( 'check_in' ) ) ? $request->get_param( 'check_in' ) : '';
			$check_out   = ! empty( $request->get_param( 'check_out' ) ) ? $request->get_param( 'check_out' ) : '';
			$price       = ! empty( $request->get_param( 'price' ) ) ? $request->get_param( 'price' ) : '';
			$adult_price = ! empty( $request->get_param( 'adult_price' ) ) ? $request->get_param( 'adult_price' ) : '';
			$child_price = ! empty( $request->get_param( 'child_price' ) ) ? $request->get_param( 'child_price' ) : '';
			$status      = ! empty( $request->get_param( 'status' ) ) ? $request->get_param( 'status' ) : '';
			$avail_date  = ! empty( $request->get_param( 'avail_date' ) ) ? $request->get_param( 'avail_date' ) : '';

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

			$room_avail_data = [];
			for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
				$tf_room_date                     = date( 'Y/m/d', $i );
				$tf_room_data                     = [
					'check_in'    => $tf_room_date,
					'check_out'   => $tf_room_date,
					'price_by'    => $price_by,
					'price'       => $price,
					'adult_price' => $adult_price,
					'child_price' => $child_price,
					'status'      => $status
				];
				$room_avail_data[ $tf_room_date ] = $tf_room_data;
			}

			if ( ! empty( $hotel_id ) ) {
				$hotel_avail_data = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
				$avail_date       = json_decode( $hotel_avail_data['room'][ $room_index ]['avail_date'], true );
				if ( isset( $avail_date ) && ! empty( $avail_date ) ) {
					$room_avail_data = array_merge( $avail_date, $room_avail_data );
				}
				$hotel_avail_data['room'][ $room_index ]['avail_date'] = json_encode( $room_avail_data );
				update_post_meta( $hotel_id, 'tf_hotels_opt', $hotel_avail_data );
			} else {
				$avail_date = json_decode( $avail_date, true );
				if ( isset( $avail_date ) && ! empty( $avail_date ) ) {
					$room_avail_data = array_merge( $avail_date, $room_avail_data );
				}
				update_option( 'tf_hotel_avail_date', $room_avail_data );
			}

			if ( ! empty( $room_avail_data ) && is_array( $room_avail_data ) ) {
				$room_events_data = array_values( $room_avail_data );
				$room_events_data = array_map( function ( $item ) {
					$item['editable'] = false;
					$item['start']    = date( 'Y-m-d', strtotime( $item['check_in'] ) );
					$item['title']    = $item['price_by'] == '1' ? esc_html__( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) : esc_html__( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . esc_html__( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );
//					$item['title'] = esc_html__( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) . '<br>' . esc_html__( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . esc_html__( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );

					if ( $item['status'] == 'unavailable' ) {
						$item['display'] = 'background';
						$item['color']   = '#003c79';
					}

					return $item;
				}, $room_events_data );
			} else {
				$room_events_data = [];
			}

			return rest_ensure_response( array(
				'status'             => true,
				'message'            => esc_html__( 'Availability updated successfully.', 'tourfic' ),
				'avail_date'         => $room_events_data,
				'avail_date_encoded' => json_encode( $room_avail_data ),
			) );
		}

		/*
		 * Import Hotel iCal
		 * @author Foysal
		 */
		function tf_room_ical_import( $request ) {
			$ical_url   = ! empty( $request->get_param( 'ical_url' ) ) ? $request->get_param( 'ical_url' ) : '';
			$hotel_id   = ! empty( $request->get_param( 'hotel_id' ) ) ? $request->get_param( 'hotel_id' ) : '';
			$room_index = intval( $request->get_param( 'room_index' ) );
			$pricing_by = ! empty( $request->get_param( 'pricing_by' ) ) ? $request->get_param( 'pricing_by' ) : '';

			if ( empty( $ical_url ) ) {
				return rest_ensure_response( array(
					'status'  => false,
					'message' => esc_html__( 'Please enter iCal URL.', 'tourfic' )
				) );
			}

			$hotel_avail_data = get_post_meta( $hotel_id, 'tf_hotels_opt', true );

			try {
				$ical = new TF_FD_ICal_Reader( $ical_url );

				if ( ! empty( $ical ) ) {
					$events = $ical->events();

					if ( ! empty( $events ) && is_array( $events ) ) {
						$date_keys = function_exists( 'tf_ical_get_unavailable_date_keys' ) ? tf_ical_get_unavailable_date_keys( $events ) : array();

						$room_avail_data = [];
						foreach ( $date_keys as $date_key ) {
							$room_avail_data[ $date_key ] = array(
								'check_in'    => $date_key,
								'check_out'   => $date_key,
								'price_by'    => $pricing_by,
								'price'       => '',
								'adult_price' => '',
								'child_price' => '',
								'status'      => 'unavailable',
							);
						}

						if ( ! empty( $hotel_avail_data ) ) {
							$avail_date = json_decode( $hotel_avail_data['room'][ $room_index ]['avail_date'], true );
							if ( isset( $avail_date ) && ! empty( $avail_date ) ) {
								$room_avail_data = array_merge( $avail_date, $room_avail_data );
							}
							$hotel_avail_data['room'][ $room_index ]['avail_date'] = json_encode( $room_avail_data );
							update_post_meta( $hotel_id, 'tf_hotels_opt', $hotel_avail_data );
						}

						if ( ! empty( $room_avail_data ) && is_array( $room_avail_data ) ) {
							$room_events_data = array_values( $room_avail_data );
							$room_events_data = array_map( function ( $item ) {
								$item['start'] = date( 'Y-m-d', strtotime( $item['check_in'] ) );
								$item['title'] = esc_html__( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) . '<br>' . esc_html__( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . esc_html__( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );

								if ( $item['status'] == 'unavailable' ) {
									$item['display'] = 'background';
									$item['color']   = '#003c79';
								}

								return $item;
							}, $room_events_data );
						} else {
							$room_events_data = [];
						}

						return rest_ensure_response( array(
							'status'             => true,
							'message'            => esc_html__( 'iCal imported successfully.', 'tourfic' ),
							'avail_date'         => $room_events_data,
							'avail_date_encoded' => json_encode( $room_avail_data ),
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

TF_Room_Rest_API::get_instance();
