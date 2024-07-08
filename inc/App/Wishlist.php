<?php

namespace Tourfic\App;

// don't load directly
defined( 'ABSPATH' ) || exit;

class Wishlist {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		add_action( 'wp_ajax_tf_add_to_wishlists', array($this, 'tf_add_to_wishlists') );
		add_action( 'wp_ajax_nopriv_tf_add_to_wishlists', array($this, 'tf_add_to_wishlists') );
		add_action( 'wp_ajax_nopriv_tf_generate_table', array($this, 'tf_generate_table_guest') );
		add_action( 'wp_ajax_tf_remove_wishlist', array($this, 'tf_remove_wishlist') );
	}

	/**
	 * add items for loogged in user's wishlists
	 *
	 * @return void
	 */
	function tf_add_to_wishlists() {
		// Check nonce security
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wishlist-nonce' ) ) {
			die( esc_html_e( 'Nonce verification failed', 'tourfic' ) );
		}

		if ( isset( $_POST ) ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

				// data to save
				$data = [
					'post_type' => $_POST['type'],
					'post_id'   => $_POST['post'],
				];

				if (is_user_logged_in()) {
					// Save wishlist for logged-in users
					$user_id                = get_current_user_id();
					$previous_wishlist_item = get_user_meta( $user_id, 'wishlist_item', false );

					if ( empty( $previous_wishlist_item ) ) {
						add_user_meta( $user_id, 'wishlist_item', $data );
					} else {
						$post_id = array_search( $data['post_id'], array_column( $previous_wishlist_item, 'post_id' ) );
						if ( ! empty( $previous_wishlist_item ) && false === $post_id ) {
							add_user_meta( $user_id, 'wishlist_item', $data );
						} else {
							update_user_meta( $user_id, 'wishlist_item', $data, $previous_wishlist_item[ $post_id ] );
						}
					}
					wp_send_json_success( esc_html__( "Item added to wishlist", 'tourfic' ) );
				}
			}
		}
	}

	/**
	 * generate the table for loogged in user
	 *
	 * @param string $type post type of wishlist items
	 *
	 * @return string|false
	 */
	function tf_generate_table_for_user( $type ) {
		// transform shortcode tags into array
		$type_array = ! empty( $type ) ? explode( ',', $type ) : null;
		// holder for filtered items according to type
		$filtered = [];
		$user_id  = get_current_user_id();
		// try to find all `wishlist_item` user meta
		$wishlist_items = get_user_meta( $user_id, 'wishlist_item', false );
		// if there is type set, filter all items according to type
		if ( is_array( $type_array ) ) {
			foreach ( $type_array as $value ) {
				$filtered = array_merge( $filtered, wp_list_filter( $wishlist_items, [ 'post_type' => $value ] ) );
			}
		} else {
			// otherwise, set whole collection as filtered
			$filtered = $wishlist_items;
		}
		// get only post ids from holder
		$ids = wp_list_pluck( $filtered, 'post_id' );

		// generate a list of post from ids
		return $this->tf_generate_table( $ids, $type );
	}

	/**
	 * Generate table for guest
	 * @return string
	 */
	function tf_generate_table_guest() {
		// Check nonce security
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'populate-wishlist-guest-nonce' ) ) {
			die( esc_html_e( 'Nonce verification failed', 'tourfic' ) );
		}

		if ( isset( $_POST ) ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$ids = $_POST['ids'];
				wp_send_json_success( $this->tf_generate_table( $ids ) );
			}
		}
	}

	/**
	 * Generate wishlist table from post ids
	 *
	 * @param mixed $ids
	 *
	 * @return string|false
	 */
	function tf_generate_table( $ids, $type = null ) {
		if ( empty( $ids ) ) {
			return '<p>' . esc_html__( 'No items added yet!', 'tourfic' ) . '</p>';
			exit;
		}
		ob_start();
		include TF_TEMPLATE_PATH . 'template-parts/wishlist.php';

		return ob_get_clean();
	}

	/**
	 * Remove item from list
	 *
	 * @return void
	 */
	function tf_remove_wishlist() {
		// Check nonce security
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'wishlist-nonce' ) ) {
			die( esc_html_e( 'Nonce verification failed', 'tourfic' ) );
		}

		if ( isset( $_GET ) ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				global $wpdb;
				$id                     = esc_attr( $_GET['id'] );
				$type                   = esc_attr( $_GET['type'] );
				$user_id                = get_current_user_id();
				$previous_wishlist_item = get_user_meta( $user_id, 'wishlist_item', false );
				// search recursively through records returned from get_user_meta for the record you want to replace, as identified by `post_id` - credit: http://php.net/manual/en/function.array-search.php#116635
				$post_id = array_search( $id, array_column( $previous_wishlist_item, 'post_id' ) );
				delete_user_meta( $user_id, 'wishlist_item', $previous_wishlist_item[ $post_id ] );

				wp_send_json_success( $this->tf_generate_table_for_user( $type ) );
			}
		}
	}


	/**
	 * Is this item in the list ?
	 *
	 * @param mixed $id post id
	 *
	 * @return bool
	 */
	static function tf_has_item_in_wishlist( $id ) {
		// when user is logged in handle item with db
		if ( is_user_logged_in() ) {
			$user_id                = get_current_user_id();
			$previous_wishlist_item = get_user_meta( $user_id, 'wishlist_item', false );
			$post_id                = array_search( $id, array_column( $previous_wishlist_item, 'post_id' ) );

			return $post_id !== false;
		} else {
			// otherwise, just return false. javascript will do the rest
			return false;
		}
	}
}