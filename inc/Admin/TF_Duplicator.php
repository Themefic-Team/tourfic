<?php

namespace Tourfic\Admin;
# don't load directly
defined( 'ABSPATH' ) || exit;

class TF_Duplicator {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		add_filter( 'post_row_actions', array( $this, 'tf_add_duplicate_post_button' ), 10, 2 );
		add_action( 'wp_ajax_tourfic_duplicate_post_data', array( $this, 'tf_duplicate_post_data_function' ) );
	}

	function tf_add_duplicate_post_button( $actions, $post ) {
		$supported_post_types = $this->tf_get_supported_post_types();
		$meta_key             = $supported_post_types[ $post->post_type ] ?? '';
		$meta                 = $meta_key ? get_post_meta( $post->ID, $meta_key, true ) : array();

		if ( ! $meta_key || ! $this->tf_current_user_can_duplicate_post( $post, $meta ) ) {
			return $actions;
		}

		$labels = array(
			'tf_tours'      => esc_html__( 'Duplicate Tour', 'tourfic' ),
			'tf_hotel'      => esc_html__( 'Duplicate Hotel', 'tourfic' ),
			'tf_apartment'  => esc_html__( 'Duplicate Apartment', 'tourfic' ),
			'tf_carrental'  => esc_html__( 'Duplicate Car', 'tourfic' ),
			'tf_room'       => esc_html__( 'Duplicate Room', 'tourfic' ),
		);
		$nonce = wp_create_nonce( 'tourfic_duplicate_post_' . $post->ID );

		$actions['duplicate'] = sprintf(
			'<a class="tf-post-data-duplicate" href="#" data-postid="%1$d" data-nonce="%2$s">%3$s</a>',
			absint( $post->ID ),
			esc_attr( $nonce ),
			esc_html( $labels[ $post->post_type ] )
		);

		return $actions;
	}

	/**
	 * Duplicate a supported Tourfic post after object-level authorization.
	 */
	function tf_duplicate_post_data_function() {
		$post_id = isset( $_POST['postID'] ) ? absint( wp_unslash( $_POST['postID'] ) ) : 0;
		if ( $post_id <= 0 ) {
			wp_send_json_error(
				array( 'message' => esc_html__( 'Invalid post ID.', 'tourfic' ) ),
				400
			);
		}

		check_ajax_referer( 'tourfic_duplicate_post_' . $post_id, 'security' );

		$source_post          = get_post( $post_id );
		$supported_post_types = $this->tf_get_supported_post_types();
		$post_type            = $source_post instanceof \WP_Post ? $source_post->post_type : '';
		$meta_key             = $supported_post_types[ $post_type ] ?? '';

		if ( ! $meta_key ) {
			wp_send_json_error(
				array( 'message' => esc_html__( 'This post type cannot be duplicated.', 'tourfic' ) ),
				400
			);
		}

		$meta = get_post_meta( $post_id, $meta_key, true );
		if ( ! $this->tf_current_user_can_duplicate_post( $source_post, $meta ) ) {
			wp_send_json_error(
				array( 'message' => esc_html__( 'You do not have permission to duplicate this post.', 'tourfic' ) ),
				403
			);
		}

		$taxonomy_terms = array();
		foreach ( get_object_taxonomies( $post_type ) as $taxonomy ) {
			$term_ids = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
			if ( is_wp_error( $term_ids ) ) {
				wp_send_json_error(
					array( 'message' => esc_html__( 'The post terms could not be read.', 'tourfic' ) ),
					500
				);
			}
			$taxonomy_terms[ $taxonomy ] = array_map( 'absint', $term_ids );
		}

		$duplicate_post_id = wp_insert_post(
			wp_slash(
				array(
					'post_type'    => $post_type,
					'post_status'  => 'publish',
					'post_title'   => get_the_title( $post_id ) . ' (Copy)',
					'post_content' => get_post_field( 'post_content', $post_id ),
				)
			),
			true
		);

		if ( is_wp_error( $duplicate_post_id ) ) {
			wp_send_json_error(
				array( 'message' => esc_html__( 'The post could not be duplicated.', 'tourfic' ) ),
				500
			);
		}

		if ( 'tf_room' === $post_type ) {
			$meta             = is_array( $meta ) ? $meta : array();
			$hotel_id         = ! empty( $meta['tf_hotel'] ) ? absint( $meta['tf_hotel'] ) : 0;
			$meta['unique_id'] = uniqid();

			if ( $hotel_id ) {
				$hotel_meta = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
				$hotel_meta = is_array( $hotel_meta ) ? $hotel_meta : array();
				if ( ! empty( $hotel_meta['tf_rooms'] ) && is_array( $hotel_meta['tf_rooms'] ) ) {
					$hotel_meta['tf_rooms'][] = $duplicate_post_id;
				} else {
					$hotel_meta['tf_rooms'] = array( $duplicate_post_id );
				}

				update_post_meta( $hotel_id, 'tf_hotels_opt', $hotel_meta );
			}
		}

		update_post_meta( $duplicate_post_id, $meta_key, $meta );

		$featured_image_id = get_post_thumbnail_id( $post_id );
		if ( $featured_image_id ) {
			set_post_thumbnail( $duplicate_post_id, $featured_image_id );
		}

		foreach ( $taxonomy_terms as $taxonomy => $term_ids ) {
			if ( ! empty( $term_ids ) ) {
				wp_set_post_terms( $duplicate_post_id, $term_ids, $taxonomy, false );
			}
		}

		wp_send_json_success( array( 'post_id' => $duplicate_post_id ) );
	}

	/**
	 * Return supported post types and their primary metadata keys.
	 *
	 * @return array
	 */
	private function tf_get_supported_post_types() {
		return array(
			'tf_hotel'     => 'tf_hotels_opt',
			'tf_tours'     => 'tf_tours_opt',
			'tf_apartment' => 'tf_apartment_opt',
			'tf_carrental' => 'tf_carrental_opt',
			'tf_room'      => 'tf_room_opt',
		);
	}

	/**
	 * Check edit, create, publish, and related hotel permissions.
	 *
	 * @param \WP_Post $post Source post.
	 * @param mixed    $meta Source metadata.
	 * @return bool
	 */
	private function tf_current_user_can_duplicate_post( $post, $meta ) {
		if ( ! $post instanceof \WP_Post || ! isset( $this->tf_get_supported_post_types()[ $post->post_type ] ) ) {
			return false;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		if (
			! $post_type_object
			|| empty( $post_type_object->cap->create_posts )
			|| empty( $post_type_object->cap->publish_posts )
			|| ! current_user_can( 'edit_post', $post->ID )
			|| ! current_user_can( $post_type_object->cap->create_posts )
			|| ! current_user_can( $post_type_object->cap->publish_posts )
		) {
			return false;
		}

		if ( 'tf_room' !== $post->post_type ) {
			return true;
		}

		$hotel_id = is_array( $meta ) && ! empty( $meta['tf_hotel'] ) ? absint( $meta['tf_hotel'] ) : 0;
		if ( ! $hotel_id ) {
			return true;
		}

		return 'tf_hotel' === get_post_type( $hotel_id ) && current_user_can( 'edit_post', $hotel_id );
	}
}
