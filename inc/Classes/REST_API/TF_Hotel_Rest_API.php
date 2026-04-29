<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Room;

if ( ! class_exists( 'TF_Hotel_Rest_API' ) ) {
	class TF_Hotel_Rest_API extends TF_Rest_API {

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
			add_action( 'rest_api_init', array( $this, 'add_hotel_meta_to_rest_api' ) );
		}

		/*
		 * Get Hotels
		 * @author Foysal
		 */
		public function tf_get_hotels( $request ) {
			$per_page = $request->get_param( 'per_page' ) ? $request->get_param( 'per_page' ) : 10;
			$page     = $request->get_param( 'page' ) ? $request->get_param( 'page' ) : 1;
			$author   = $request->get_param( 'user' ) ? $request->get_param( 'user' ) : get_current_user_id();

			$query_hotels = new WP_Query( array(
				'post_type'      => 'tf_hotel',
				'posts_per_page' => $per_page,
				'post_status'    => array( 'publish', 'pending', 'draft' ),
				'author'         => $this->user_has_role( $author, 'administrator' ) || $this->user_has_role( $author, 'tf_manager' ) ? '' : $author,
				'paged'          => $page,
			) );
			$hotels       = array();
			if ( $query_hotels->have_posts() ) {
				while ( $query_hotels->have_posts() ) {
					$query_hotels->the_post();
					$hotel_id = get_the_ID();

					$hotel_data   = array();
					$hotel_review = $this->tf_get_post_review( $hotel_id );
					$start_price  = $this->tf_get_hotel_starting_price( $hotel_id );

					$hotel_data['id']             = $hotel_id;
					$hotel_data['permalink']      = get_permalink( $hotel_id );
					$hotel_data['title']          = get_the_title( $hotel_id );
					$hotel_data['content']        = get_the_content( $hotel_id );
					$hotel_data['status']         = get_post_status( $hotel_id );
					$hotel_data['author']         = get_the_author_meta( 'display_name', get_post_field( 'post_author', $hotel_id ) );
					$hotel_data['hotel_location'] = $this->tf_get_post_terms( $hotel_id, 'hotel_location' ) ? $this->tf_get_post_terms( $hotel_id, 'hotel_location' ) : '—';
					$hotel_data['hotel_feature']  = $this->tf_get_post_terms( $hotel_id, 'hotel_feature' ) ? $this->tf_get_post_terms( $hotel_id, 'hotel_feature' ) : '—';
					$hotel_data['hotel_type']     = $this->tf_get_post_terms( $hotel_id, 'hotel_type' ) ? $this->tf_get_post_terms( $hotel_id, 'hotel_type' ) : '—';
					$hotel_data['date']           = get_the_date( '', $hotel_id );
					$hotel_data['featured_image'] = get_the_post_thumbnail_url( $hotel_id );
					$hotel_data['tf_hotels_opt']  = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
					$hotel_data['reviews']        = [
						'hotel_reviews' => $hotel_review['post_reviews'],
						'review_text'   => $hotel_review['review_text'],
					];
					$hotel_data['start_price']    = $start_price;
					$hotel_data['rooms']    	  = Room::get_hotel_rooms($hotel_id);
					$hotels[]                     = $hotel_data;
				}
			}
			wp_reset_postdata();
			$hotels = array(
				'hotels' => $hotels,
				'total'  => $query_hotels->found_posts,
			);

			return $hotels;
		}

		/*
		 * Add hotel meta to /wp-json/wp/v2/tf_hotel api
		 * @author Foysal
		 */
		function add_hotel_meta_to_rest_api() {
			register_rest_field( 'tf_hotel', 'tf_hotels_opt', array(
				'get_callback' => function ( $post_arr ) {
					$tf_hotels_opt     = get_post_meta( $post_arr['id'], 'tf_hotels_opt', true );
					$unserialize_array = array(
						'map',
						'airport_pickup_price',
						'airport_dropoff_price',
						'airport_pickup_dropoff_price'
					);
					foreach ( $unserialize_array as $item ) {
						if ( ! empty( $tf_hotels_opt[ $item ] ) && is_serialized( $tf_hotels_opt[ $item ] ) ) {
							$tf_hotels_opt[ $item ] = unserialize( $tf_hotels_opt[ $item ] );
						}
					}

					return $tf_hotels_opt;
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//featured image
			register_rest_field( 'tf_hotel', 'featured_image', array(
				'get_callback' => function ( $post_arr ) {
					return get_the_post_thumbnail_url( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//hotel reviews
			register_rest_field( 'tf_hotel', 'reviews', array(
				'get_callback' => function ( $post_arr ) {
					return $this->tf_get_post_review( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );

			//hotel start price
			register_rest_field( 'tf_hotel', 'start_price', array(
				'get_callback' => function ( $post_arr ) {
					return $this->tf_get_hotel_starting_price( $post_arr['id'] );
				},
				'schema'       => array(
					'type'    => 'string',
					'context' => array( 'view', 'edit' ),
				),
			) );
		}

		function tf_get_hotel_starting_price( $post_id ) {
			$room_price = [];
			$meta       = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$rooms      = Room::get_hotel_rooms( $post_id );
			if ( ! empty( $rooms ) ):
				foreach ( $rooms as $_room ) {
					$room       = get_post_meta( $_room->ID, 'tf_room_opt', true );
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
		 * Get Hotel Room Availability
		 * @author Foysal
		 */
		function tf_get_hotel_room_availability( $request ) {
			$id = ! empty( $request->get_param( 'id' ) ) ? $request->get_param( 'id' ) : '';

			if ( $id !== 'undefined' ) {
				$room_meta       = get_post_meta( $id, 'tf_room_opt', true );
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
					if ( $item['price_by'] == '1' ) {
						$item['title'] = __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] );
					} elseif ( $item['price_by'] == '2' ) {
						$item['title'] = __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );
					} elseif ( $item['price_by'] == '3' ) {
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
				}, $room_avail_data );
			} else {
				$room_avail_data = [];
			}

			return $room_avail_data;
		}
	}
}

TF_Hotel_Rest_API::get_instance();
