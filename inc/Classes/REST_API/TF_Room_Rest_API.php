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
	}
}

TF_Room_Rest_API::get_instance();
