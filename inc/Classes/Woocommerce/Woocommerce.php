<?php

namespace Tourfic\Classes\Woocommerce;

defined( 'ABSPATH' ) || exit;

class Woocommerce {

	use \Tourfic\Traits\Singleton;

	public function __construct() {
		add_filter( 'woocommerce_data_stores', array( $this, 'tf_woocommerce_data_stores' ) );
		add_action( 'publish_tf_apartment', array($this, 'tf_add_price_field_to_post'), 10, 2 );
		add_action( 'publish_tf_hotel', array($this, 'tf_add_price_field_to_post'), 10, 2 );
		add_action( 'publish_tf_tours', array($this, 'tf_add_price_field_to_post'), 10, 2 );

		add_action( 'woocommerce_checkout_update_order_meta', array($this, 'tf_add_order_type_order_meta') );
		add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array($this, 'tf_custom_query_var_get_orders'), 10, 2 );
		add_filter( 'woocommerce_order_item_display_meta_key', array($this, 'tf_change_meta_key_title'), 20, 3 );
		add_filter( 'woocommerce_hidden_order_itemmeta', array($this, 'tf_hide_order_meta') );
	}

	function tf_woocommerce_data_stores( $stores ) {

		require_once WP_PLUGIN_DIR . '/woocommerce/includes/class-wc-data-store.php';
		$stores['product'] = 'Tourfic\Classes\Woocommerce\Product_Extend';

		return $stores;
	}

	function tf_add_price_field_to_post($post_id, $post) {
		update_post_meta( $post_id, '_price', '0' );
	}

	function tf_add_order_type_order_meta( $order_id ) {

		$order = wc_get_order( $order_id );

		foreach ( $order->get_items() as $item_key => $item_values ) {
			$item_data = $item_values->get_data();

			// Assign _order_type meta in line order meta
			if ( wc_get_order_item_meta( $item_key, '_order_type', true ) == 'tour' ) {
				update_post_meta( $order_id, '_order_type', 'tour' );
			} elseif ( wc_get_order_item_meta( $item_key, '_order_type', true ) == 'hotel' ) {
				update_post_meta( $order_id, '_order_type', 'hotel' );
			} elseif ( wc_get_order_item_meta( $item_key, '_order_type', true ) == 'apartment' ) {
				update_post_meta( $order_id, '_order_type', 'apartment' );
			}

			// Assign _post_author meta in line order meta
			$post_author = wc_get_order_item_meta( $item_key, '_post_author', true );
			update_post_meta( $order_id, '_post_author', $post_author );
		}
	}

	function tf_custom_query_var_get_orders( $query, $query_vars ) {

		if ( ! empty( $query_vars['_order_type'] ) ) {
			$query['meta_query'][] = array(
				'key'   => '_order_type',
				'value' => esc_attr( $query_vars['_order_type'] ),
			);
		}

		if ( ! empty( $query_vars['_post_author'] ) ) {
			$query['meta_query'][] = array(
				'key'   => '_post_author',
				'value' => esc_attr( $query_vars['_post_author'] ),
			);
		}

		return $query;
	}

	/**
	 * Changing meta keys to nice text when display
	 * @param  string        $key  The meta key
	 * @param  \WC_Meta_Data  $meta The meta object
	 * @param  \WC_Order_Item $item The order item object
	 * @return string        The title
	 * @since  1.0.0
	 */
	function tf_change_meta_key_title( $key, $meta, $item ) {

		// By using $meta-key we are sure we have the correct one.
		if ( 'room_name' === $meta->key ) { $key = esc_html__( 'Room Name', 'tourfic'); }
		if ( 'number_room_booked' === $meta->key ) { $key = esc_html__( 'Number of Room Booked', 'tourfic'); }
		if ( 'adult' === $meta->key ) { $key = esc_html__( 'Adult Number', 'tourfic'); }
		if ( 'child' === $meta->key ) { $key = esc_html__( 'Children Number', 'tourfic'); }
		if ( 'check_in' === $meta->key ) { $key = esc_html__( 'Check-in Date', 'tourfic'); }
		if ( 'check_out' === $meta->key ) { $key = esc_html__( 'Check-out Date', 'tourfic'); }
		if ( 'due' === $meta->key ) { $key = esc_html__( 'Due', 'tourfic'); }
		if ( '_tour_id' === $meta->key ) { $key = esc_html__( 'Tour ID', 'tourfic'); }
		if ( 'Adults' === $meta->key ) { $key = esc_html__( 'Adults', 'tourfic'); }
		if ( 'Children' === $meta->key ) { $key = esc_html__( 'Children', 'tourfic'); }
		if ( 'Infants' === $meta->key ) { $key = esc_html__( 'Infants', 'tourfic'); }
		if ( 'Tour Date' === $meta->key ) { $key = esc_html__( 'Tour Date', 'tourfic'); }
		if ( 'Tour Time' === $meta->key ) { $key = esc_html__( 'Tour Time', 'tourfic'); }
		if ( 'Tour Extra' === $meta->key ) { $key = esc_html__( 'Tour Extra', 'tourfic'); }
		if ( 'Due' === $meta->key ) { $key = esc_html__( 'Due', 'tourfic'); }

		return $key;
	}

	/**
	 * Hiding order item meta from order details page
	 *
	 * @param array $hidden_meta Array of all meta data to hide.
	 *
	 * @return array
	 */
	function tf_hide_order_meta( $hidden_meta ) {

		$hidden_meta = array('_order_type', '_post_author', '_post_id', '_unique_id', '_tour_unique_id', '_visitor_details');

		return $hidden_meta;
	}
}