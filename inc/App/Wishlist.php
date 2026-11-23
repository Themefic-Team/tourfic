<?php

namespace Tourfic\App;

defined( 'ABSPATH' ) || exit;

class Wishlist {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		add_action( 'wp_ajax_tourfic_add_to_wishlists', array( $this, 'tf_add_to_wishlists' ) );
		add_action( 'wp_ajax_nopriv_tourfic_add_to_wishlists', array( $this, 'tf_add_to_wishlists' ) );
		add_action( 'wp_ajax_nopriv_tourfic_generate_table', array( $this, 'tf_generate_table_guest' ) );
		add_action( 'wp_ajax_tourfic_remove_wishlist', array( $this, 'tf_remove_wishlist' ) );
	}

	/**
	 * Parse an identifier without converting malformed input into another post ID.
	 */
	public static function parse_id( $value ) {
		if ( ( ! is_string( $value ) && ! is_int( $value ) )
			|| 1 !== preg_match( '/^[1-9][0-9]*$/D', (string) $value )
			|| (string) (int) $value !== (string) $value ) {
			return false;
		}
		return (int) $value;
	}

	/**
	 * Public wishlists never contain protected or unpublished listings, even for administrators.
	 */
	public static function get_eligible_post( $id ) {
		$id = self::parse_id( $id );
		$post = $id ? get_post( $id ) : null;
		if ( ! $post || 'publish' !== $post->post_status || '' !== (string) $post->post_password
			|| ! in_array( $post->post_type, array( 'tf_hotel', 'tf_tours', 'tf_apartment', 'tf_carrental' ), true ) ) {
			return false;
		}
		if ( function_exists( 'is_post_publicly_viewable' ) && ! is_post_publicly_viewable( $post ) ) {
			return false;
		}
		return $post;
	}

	private static function public_ids( $items ) {
		$ids = array();
		foreach ( (array) $items as $item ) {
			$id = is_array( $item ) && isset( $item['post_id'] ) ? $item['post_id'] : null;
			$post = self::get_eligible_post( $id );
			if ( $post ) {
				$ids[ $post->ID ] = $post->ID;
			}
		}
		return array_values( $ids );
	}

	/**
	 * Delete only complete matching records from the authenticated user's own metadata.
	 */
	private static function remove_item( $id ) {
		$user_id = get_current_user_id();
		foreach ( (array) get_user_meta( $user_id, 'wishlist_item', false ) as $item ) {
			if ( is_array( $item ) && isset( $item['post_id'] ) && $id === self::parse_id( $item['post_id'] ) ) {
				delete_user_meta( $user_id, 'wishlist_item', $item );
			}
		}
	}

	public static function rest_permission( $request ) {
		if ( ! is_user_logged_in() ) {
			return new \WP_Error( 'rest_not_logged_in', __( 'Authentication is required.', 'tourfic' ), array( 'status' => 401 ) );
		}
		if ( ! current_user_can( 'read' ) ) {
			return new \WP_Error( 'rest_forbidden', __( 'You cannot access this wishlist.', 'tourfic' ), array( 'status' => 403 ) );
		}
		return true;
	}

	private static function validate_rest_request( $request ) {
		foreach ( array( 'post_id', 'user_id' ) as $key ) {
			$value = $request->get_param( $key );
			if ( null !== $value && '' !== $value && false === self::parse_id( $value ) ) {
				return new \WP_Error( 'rest_invalid_param', __( 'Invalid wishlist identifier.', 'tourfic' ), array( 'status' => 400 ) );
			}
		}
		$remove = $request->get_param( 'remove' );
		if ( ! in_array( $remove, array( null, '', false, true, 'false', 'true', '0', '1', 0, 1 ), true ) ) {
			return new \WP_Error( 'rest_invalid_param', __( 'Invalid wishlist operation.', 'tourfic' ), array( 'status' => 400 ) );
		}
		$seen = array();
		foreach ( array( $request->get_url_params(), $request->get_query_params(), $request->get_body_params(), $request->get_json_params() ) as $source ) {
			foreach ( array_intersect_key( (array) $source, array_flip( array( 'post_id', 'user_id', 'remove' ) ) ) as $key => $value ) {
				if ( array_key_exists( $key, $seen ) && $seen[ $key ] !== $value
					&& ( ! is_scalar( $value ) || ! is_scalar( $seen[ $key ] ) || (string) $seen[ $key ] !== (string) $value ) ) {
					return new \WP_Error( 'rest_invalid_param', __( 'Conflicting wishlist parameters.', 'tourfic' ), array( 'status' => 400 ) );
				}
				$seen[ $key ] = $value;
			}
		}
		return true;
	}

	/**
	 * Keep the dashboard's three public fields; never serialize a WP_Post object.
	 */
	public static function rest_items( $request ) {
		$permission = self::rest_permission( $request );
		if ( is_wp_error( $permission ) ) {
			return $permission;
		}
		$validation = self::validate_rest_request( $request );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}
		if ( 'DELETE' === $request->get_method() ) {
			$id = self::parse_id( $request->get_param( 'post_id' ) );
			if ( ! $id ) {
				return new \WP_Error( 'rest_invalid_param', __( 'A wishlist identifier is required.', 'tourfic' ), array( 'status' => 400 ) );
			}
			self::remove_item( $id );
		} elseif ( 'GET' !== $request->get_method() || in_array( $request->get_param( 'remove' ), array( true, 'true', '1', 1 ), true ) ) {
			return new \WP_Error( 'rest_invalid_method', __( 'Use DELETE to remove a wishlist item.', 'tourfic' ), array( 'status' => 405 ) );
		}
		$wishlist_data = array();
		$items = get_user_meta( get_current_user_id(), 'wishlist_item', false );
		foreach ( self::public_ids( $items ) as $id ) {
			$post = self::get_eligible_post( $id );
			if ( $post ) {
				$wishlist_data[] = array( 'ID' => (int) $post->ID, 'post_title' => $post->post_title, 'post_type' => $post->post_type );
			}
		}
		return $wishlist_data;
	}

	public function tf_add_to_wishlists() {
		if ( ! isset( $_POST['nonce'] ) || ! is_string( $_POST['nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wishlist-nonce' ) ) {
			wp_send_json_error( __( 'Nonce verification failed.', 'tourfic' ), 403 );
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Strict ID parsing and published-listing eligibility run in get_eligible_post before storage.
		$id = isset( $_POST['post'] ) ? wp_unslash( $_POST['post'] ) : null;
		$post = self::get_eligible_post( $id );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- The value must exactly match the eligible post's authoritative type; only that stored type is persisted.
		$type = isset( $_POST['type'] ) ? wp_unslash( $_POST['type'] ) : '';
		if ( ! $post || ! is_string( $type ) || $post->post_type !== $type ) {
			wp_send_json_error( __( 'This listing is not available for wishlists.', 'tourfic' ), 400 );
		}
		if ( is_user_logged_in() ) {
			$data = array( 'post_type' => $post->post_type, 'post_id' => (int) $post->ID );
			if ( ! self::tf_has_item_in_wishlist( $post->ID ) ) {
				add_user_meta( get_current_user_id(), 'wishlist_item', $data );
			}
		}
		wp_send_json_success( __( 'Item added to wishlist', 'tourfic' ) );
	}

	public function tf_generate_table_for_user( $type ) {
		$items = is_user_logged_in() ? get_user_meta( get_current_user_id(), 'wishlist_item', false ) : array();
		$ids = self::public_ids( $items );
		if ( is_string( $type ) && '' !== $type ) {
			$types = array_map( 'trim', explode( ',', $type ) );
			$ids = array_values( array_filter( $ids, function( $id ) use ( $types ) {
				$post = self::get_eligible_post( $id );
				return $post && in_array( $post->post_type, $types, true );
			} ) );
		}
		return $this->tf_generate_table( $ids, $type );
	}

	public function tf_generate_table_guest() {
		if ( ! isset( $_POST['nonce'] ) || ! is_string( $_POST['nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'populate-wishlist-guest-nonce' ) ) {
			wp_send_json_error( __( 'Nonce verification failed.', 'tourfic' ), 403 );
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- The following loop validates every member with parse_id before rendering any public listing.
		$ids = isset( $_POST['ids'] ) ? wp_unslash( $_POST['ids'] ) : array();
		if ( ! is_array( $ids ) ) {
			wp_send_json_error( __( 'Invalid wishlist identifiers.', 'tourfic' ), 400 );
		}
		foreach ( $ids as $id ) {
			if ( false === self::parse_id( $id ) ) {
				wp_send_json_error( __( 'Invalid wishlist identifier.', 'tourfic' ), 400 );
			}
		}
		wp_send_json_success( $this->tf_generate_table( $ids ) );
	}

	public function tf_generate_table( $ids, $type = null ) {
		$ids = array_values( array_unique( array_filter( (array) $ids, array( __CLASS__, 'get_eligible_post' ) ) ) );
		if ( empty( $ids ) ) {
			return '<p>' . esc_html__( 'No items added yet!', 'tourfic' ) . '</p>';
		}
		ob_start();
		include TOURFIC_TEMPLATE_PATH . 'template-parts/wishlist.php';
		return ob_get_clean();
	}

	public function tf_remove_wishlist() {
		if ( ! is_user_logged_in() || ! current_user_can( 'read' ) ) {
			wp_send_json_error( __( 'Authentication is required.', 'tourfic' ), 401 );
		}
		if ( ! isset( $_GET['nonce'] ) || ! is_string( $_GET['nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'wishlist-nonce' ) ) {
			wp_send_json_error( __( 'Nonce verification failed.', 'tourfic' ), 403 );
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- parse_id rejects malformed values; removal targets only the authenticated user's matching row.
		$id = isset( $_GET['id'] ) ? self::parse_id( wp_unslash( $_GET['id'] ) ) : false;
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- The string is validated below and sanitized before the display-only table filter.
		$type = isset( $_GET['type'] ) ? wp_unslash( $_GET['type'] ) : '';
		if ( ! $id || ! is_string( $type ) ) {
			wp_send_json_error( __( 'Invalid wishlist identifier.', 'tourfic' ), 400 );
		}
		self::remove_item( $id );
		wp_send_json_success( $this->tf_generate_table_for_user( sanitize_text_field( $type ) ) );
	}

	public static function tf_has_item_in_wishlist( $id ) {
		$id = self::parse_id( $id );
		if ( ! $id || ! is_user_logged_in() || ! self::get_eligible_post( $id ) ) {
			return false;
		}
		$items = get_user_meta( get_current_user_id(), 'wishlist_item', false );
		return in_array( $id, self::public_ids( $items ), true );
	}
}
