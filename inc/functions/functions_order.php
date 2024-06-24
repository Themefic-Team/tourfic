<?php
defined( 'ABSPATH' ) || exit;



/*
 * TF get all order id
 * @author Foysal
 * @since 2.9.26
 */
if ( ! function_exists( 'tf_get_all_order_id' ) ) {
	function tf_get_all_order_id() {
		global $wpdb;
		$order_ids  = $wpdb->get_col( "SELECT order_id FROM {$wpdb->prefix}tf_order_data" );

		return $order_ids;
	}
}
