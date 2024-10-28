<?php

namespace Tourfic\Classes\Room;

use Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;

class Room {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		\Tourfic\Classes\Room\Room_CPT::instance();

		add_action( 'wp_ajax_tf_remove_room_order_ids', array( $this, 'tf_remove_room_order_ids' ) );
	}

	static function get_hotel_rooms( $hotel_id ) {
		$args = array(
			'post_type'      => 'tf_room',
			'posts_per_page' => - 1,
		);

		$rooms = get_posts( $args );

		$hotel_rooms = array();
		foreach ( $rooms as $room ) {
			$room_meta = get_post_meta( $room->ID, 'tf_room_opt', true );
			if ( ! empty( $room_meta['tf_hotel'] ) && $room_meta['tf_hotel'] == $hotel_id ) {
				$hotel_rooms[] = $room;
			}
		}

		return $hotel_rooms;

	}

	static function get_hotel_id_for_assigned_room( $room_id ) {
		$args = array(
			'post_type'      => 'tf_hotel',
			'posts_per_page' => - 1,
		);

		$hotels = get_posts( $args );

		foreach ( $hotels as $hotel ) {
			$hotel_meta = get_post_meta( $hotel->ID, 'tf_hotels_opt', true );
			if ( ! empty( $hotel_meta['tf_rooms'] ) && is_array($hotel_meta['tf_rooms']) ) {
				if(in_array($room_id, $hotel_meta['tf_rooms'])){
					return $hotel->ID;
				}
			}
		}
	}

	static function get_room_options_count( $rooms ) {
		$total_room_option_count = 0;
		if ( ! empty( $rooms ) ) {
			foreach ( $rooms as $room ) {
				$room_meta = get_post_meta( $room->ID, 'tf_room_opt', true );
				$enable    = ! empty( $room_meta['enable'] ) ? $room_meta['enable'] : '';
				if ( $enable == '1' ) {
					$room_options            = ! empty( $room_meta['room-options'] ) ? $room_meta['room-options'] : [];
					$total_room_option_count += count( $room_options );
				}
			}
		}

		return $total_room_option_count;
	}

	/**
	 * Ajax remove room order ids
	 */

	function tf_remove_room_order_ids() {
		if ( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) ), 'updates' ) ) {
			return;
		}
		# Get post id
		$room_id = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
		# Get hotel meta
		$meta = get_post_meta( $room_id, 'tf_room_opt', true );

		# Set order id field's value to blank
		$meta['order_id'] = '';
		# Update whole hotel meta
		update_post_meta( $room_id, 'tf_room_opt', $meta );

		# Send success message
		wp_send_json_success( array(
			'message' => esc_html__( "Order ids removed successfully!", "tourfic" ),
		) );

		wp_die();
	}
}