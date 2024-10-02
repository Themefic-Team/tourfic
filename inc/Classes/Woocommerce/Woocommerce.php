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
		add_action( 'publish_tf_carrental', array($this, 'tf_add_price_field_to_post'), 10, 2 );

		add_action( 'woocommerce_checkout_update_order_meta', array($this, 'tf_add_order_type_order_meta') );
		add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array($this, 'tf_custom_query_var_get_orders'), 10, 2 );
		add_filter( 'woocommerce_order_item_display_meta_key', array($this, 'tf_change_meta_key_title'), 20, 3 );
		add_filter( 'woocommerce_hidden_order_itemmeta', array($this, 'tf_hide_order_meta') );

		add_action( 'woocommerce_order_status_changed', array($this, 'tf_order_status_changed'), 10, 4);
		add_action( 'woocommerce_saved_order_items', array($this, 'tf_woocommerce_before_save_order_items'), 10, 2);
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
			} elseif ( wc_get_order_item_meta( $item_key, '_order_type', true ) == 'car' ) {
				update_post_meta( $order_id, '_order_type', 'car' );
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

	/*
	* Admin order status change
	* @author Jahid
	*/
	function tf_order_status_changed( $order_id, $old_status, $new_status, $order ) {
		global $wpdb;
		$tf_order_checked = $wpdb->query( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_order_data WHERE order_id=%s", $order_id ) );
		if ( ! empty( $tf_order_checked ) ) {
			$wpdb->query(
				$wpdb->prepare( "UPDATE {$wpdb->prefix}tf_order_data SET ostatus=%s WHERE order_id=%s", $new_status, $order_id )
			);
		}
	
		$tf_integration_order_data   = array(
			'order_id' => $order_id
		);
		$tf_integration_order_status = [];
		# Get and Loop Over Order Line Items
		foreach ( $order->get_items() as $item_id => $item ) {
	
			$order_type = $item->get_meta( '_order_type', true );
	
			//Order Data Insert
			$billinginfo = [
				'billing_first_name' => $order->get_billing_first_name(),
				'billing_last_name'  => $order->get_billing_last_name(),
				'billing_company'    => $order->get_billing_company(),
				'billing_address_1'  => $order->get_billing_address_1(),
				'billing_address_2'  => $order->get_billing_address_2(),
				'billing_city'       => $order->get_billing_city(),
				'billing_state'      => $order->get_billing_state(),
				'billing_postcode'   => $order->get_billing_postcode(),
				'billing_country'    => $order->get_billing_country(),
				'billing_email'      => $order->get_billing_email(),
				'billing_phone'      => $order->get_billing_phone()
			];
	
			$shippinginfo = [
				'shipping_first_name' => $order->get_shipping_first_name(),
				'shipping_last_name'  => $order->get_shipping_last_name(),
				'shipping_company'    => $order->get_shipping_company(),
				'shipping_address_1'  => $order->get_shipping_address_1(),
				'shipping_address_2'  => $order->get_shipping_address_2(),
				'shipping_city'       => $order->get_shipping_city(),
				'shipping_state'      => $order->get_shipping_state(),
				'shipping_postcode'   => $order->get_shipping_postcode(),
				'shipping_country'    => $order->get_shipping_country(),
				'shipping_phone'      => $order->get_shipping_phone()
			];
	
			// Order Type hotel/tour
	
			// Hotel Item Data Insert
			if ( "hotel" == $order_type ) {
				$unique_id            = $item->get_meta( '_unique_id', true ); // Unique id of rooms
				$room_selected        = $item->get_meta( 'number_room_booked', true );
				$check_in             = $item->get_meta( 'check_in', true );
				$check_out            = $item->get_meta( 'check_out', true );
				$price                = $item->get_subtotal();
				$due                  = $item->get_meta( 'due', true );
				$room_name            = $item->get_meta( 'room_name', true );
				$option               = $item->get_meta( 'option', true );
				$adult                = $item->get_meta( 'adult', true );
				$child                = $item->get_meta( 'child', true );
				$children_ages        = $item->get_meta( 'Children Ages', true );
				$airport_service_type = $item->get_meta( 'Airport Service', true );
				$airport_service_fee  = $item->get_meta( 'Airport Service Fee', true );
	
				$tf_integration_order_data[] = [
					'room'                 => $room_selected,
					'room_unique_id'       => $unique_id,
					'check_in'             => $check_in,
					'check_out'            => $check_out,
					'room_name'            => $room_name,
					'option'               => $option,
					'adult'                => $adult,
					'child'                => $child,
					'children_ages'        => $children_ages,
					'airport_service_type' => $airport_service_type,
					'airport_service_fee'  => $airport_service_fee,
					'total_price'          => $price,
					'due_price'            => $due,
					'customer_id'          => $order->get_customer_id(),
					'payment_method'       => $order->get_payment_method(),
					'order_status'         => $order->get_status(),
					'order_date'           => gmdate( 'Y-m-d H:i:s' )
				];
	
				$tf_integration_order_status = [
					'customer_id'    => $order->get_customer_id(),
					'payment_method' => $order->get_payment_method(),
					'order_status'   => $order->get_status(),
					'order_date'     => gmdate( 'Y-m-d H:i:s' )
				];
	
			}
	
			// Tour Item Data Insert
			if ( "tour" == $order_type ) {
				$tour_date  = $item->get_meta( 'Tour Date', true );
				$tour_time  = $item->get_meta( 'Tour Time', true );
				$price      = $item->get_subtotal();
				$due        = $item->get_meta( 'Due', true );
				$tour_extra = $item->get_meta( 'Tour Extra', true );
				$adult      = $item->get_meta( 'Adults', true );
				$child      = $item->get_meta( 'Children', true );
				$infants    = $item->get_meta( 'Infants', true );
	
				if ( $tour_date ) {
					if ( str_contains( $tour_date, ' - ' ) ) {
						list( $tour_in, $tour_out ) = explode( ' - ', $tour_date );
					} else {
						$tour_in = $tour_date;
					}
				}
	
				$tf_integration_order_data[] = [
					'tour_date'   => $tour_date,
					'tour_time'   => $tour_time,
					'tour_extra'  => $tour_extra,
					'adult'       => $adult,
					'child'       => $child,
					'infants'     => $infants,
					'total_price' => $price,
					'due_price'   => $due,
				];
	
				$tf_integration_order_status = [
					'customer_id'    => $order->get_customer_id(),
					'payment_method' => $order->get_payment_method(),
					'order_status'   => $order->get_status(),
					'order_date'     => gmdate( 'Y-m-d H:i:s' )
				];
	
			}
	
			// Apartment Item Data Insert
			if ( "apartment" == $order_type ) {
				$price             = $item->get_subtotal();
				$check_in_out_date = $item->get_meta( 'check_in_out_date', true );
				$adult             = $item->get_meta( 'adults', true );
				$child             = $item->get_meta( 'children', true );
				$infants           = $item->get_meta( 'infant', true );
	
				if ( $check_in_out_date ) {
					list( $check_in, $check_out ) = explode( ' - ', $check_in_out_date );
				}
	
				$tf_integration_order_data[] = [
					'check_in'       => $check_in,
					'check_out'      => $check_out,
					'adult'          => $adult,
					'child'          => $child,
					'infants'        => $infants,
					'total_price'    => $price,
				];
	
				$tf_integration_order_status = [
					'customer_id'    => $order->get_customer_id(),
					'payment_method' => $order->get_payment_method(),
					'order_status'   => $order->get_status(),
					'order_date'     => gmdate( 'Y-m-d H:i:s' )
				];
	
			}
	
			// Car Item Data Insert
			if ( "car" == $order_type ) {
				$price = $item->get_subtotal();
				$pickup = $item->get_meta( 'Pick Up Location', true );
				$tf_pickup_date = $item->get_meta( 'Pick Up Date', true );
				$tf_pickup_time = $item->get_meta( 'Pick Up Time', true );
				$dropoff = $item->get_meta( 'Drop Off Location', true );
				$tf_dropoff_date = $item->get_meta( 'Drop Off Date', true );
				$tf_dropoff_time = $item->get_meta( 'Drop Off Time', true );
				$tf_protection = $item->get_meta( 'Protection', true );
				$tf_extra = $item->get_meta( 'Extra', true );
				$tf_due = $item->get_meta( 'Due', true );
	
				$tf_integration_order_data[] = [
					'pickup_location'   => $pickup,
					'pickup_date'   => $tf_pickup_date,
					'pickup_time'   => $tf_pickup_time,
					'dropoff_location'   => $dropoff,
					'dropoff_date'   => $tf_dropoff_date,
					'dropoff_time'   => $tf_dropoff_time,
					'extra' => !empty($tf_extra) ? $tf_extra : '',
					'protection' => !empty($tf_protection) ? $tf_protection : '',
					'due' => !empty($tf_due) ? $tf_due : '',
					'total_price' => $price
				];
	
				$tf_integration_order_status = [
					'customer_id'    => $order->get_customer_id(),
					'payment_method' => $order->get_payment_method(),
					'order_status'   => $order->get_status(),
					'order_date'     => gmdate( 'Y-m-d H:i:s' )
				];
	
			}
		}
	
		/**
		 * New Order Pabbly Integration
		 * @author Jahid
		 */
	
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			do_action( 'tf_new_order_pabbly_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status );
			do_action( 'tf_new_order_zapier_form_trigger', $tf_integration_order_data, $billinginfo, $shippinginfo, $tf_integration_order_status );
		}
	
	}

	/*
	* Admin order data update
	* @author Jahid
	*/
	function tf_woocommerce_before_save_order_items( $order_id, $items ) {

		$billinginfo = [
			'billing_first_name' => $items['_billing_first_name'],
			'billing_last_name'  => $items['_billing_last_name'],
			'billing_company'    => $items['_billing_company'],
			'billing_address_1'  => $items['_billing_address_1'],
			'billing_address_2'  => $items['_billing_address_2'],
			'billing_city'       => $items['_billing_city'],
			'billing_state'      => $items['_billing_state'],
			'billing_postcode'   => $items['_billing_postcode'],
			'billing_country'    => $items['_billing_country'],
			'billing_email'      => $items['_billing_email'],
			'billing_phone'      => $items['_billing_phone']
		];

		$shippinginfo      = [
			'shipping_first_name' => $items['_shipping_first_name'],
			'shipping_last_name'  => $items['_shipping_last_name'],
			'shipping_company'    => $items['_shipping_company'],
			'shipping_address_1'  => $items['_shipping_address_1'],
			'shipping_address_2'  => $items['_shipping_address_2'],
			'shipping_city'       => $items['_shipping_city'],
			'shipping_state'      => $items['_shipping_state'],
			'shipping_postcode'   => $items['_shipping_postcode'],
			'shipping_country'    => $items['_shipping_country'],
			'shipping_phone'      => $items['_shipping_phone']
		];
		$tf_payment_method = $items['_payment_method'];
		global $wpdb;
		$tf_order_checked = $wpdb->query( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_order_data WHERE order_id=%s", $order_id ) );
		if ( ! empty( $tf_order_checked ) ) {
			$wpdb->query(
				$wpdb->prepare( "UPDATE {$wpdb->prefix}tf_order_data SET billing_details=%s, shipping_details=%s, payment_method=%s WHERE order_id=%s", wp_json_encode( $billinginfo ), wp_json_encode( $shippinginfo ), $tf_payment_method, $order_id )
			);
		}
	}

}