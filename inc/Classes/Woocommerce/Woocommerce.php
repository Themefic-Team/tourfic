<?php

namespace Tourfic\Classes\Woocommerce;

class Woocommerce {

	use \Tourfic\Traits\Singleton;

	public function __construct() {
		add_filter( 'woocommerce_data_stores', array( $this, 'tf_woocommerce_data_stores' ) );
		add_action( 'publish_tf_apartment', array($this, 'tf_add_price_field_to_post'), 10, 2 );
		add_action( 'publish_tf_hotel', array($this, 'tf_add_price_field_to_post'), 10, 2 );
		add_action( 'publish_tf_tours', array($this, 'tf_add_price_field_to_post'), 10, 2 );

		add_action( 'woocommerce_checkout_update_order_meta', array($this, 'tf_add_order_type_order_meta') );
		add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array($this, 'tf_custom_query_var_get_orders'), 10, 2 );
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

}