<?php
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
/**
 * Add _order_type order meta from line order meta
 */
if ( ! function_exists( 'tf_add_order_type_order_meta' ) ) {
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

	add_action( 'woocommerce_checkout_update_order_meta', 'tf_add_order_type_order_meta' );
}

/**
 * Add custom query var in WooCommerce get orders query
 *
 * _order_type, _post_author
 *
 * https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query#adding-custom-parameter-support
 */
if ( ! function_exists( 'tf_custom_query_var_get_orders' ) ) {
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

	add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'tf_custom_query_var_get_orders', 10, 2 );
}

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

/**
 * Order Data
 * Create Order Data Database
 * @author jahid
 */
function tf_order_table_create(){

	global $wpdb;
	$order_table_name = $wpdb->prefix.'tf_order_data';
	$charset_collate = $wpdb->get_charset_collate();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$sql = "CREATE TABLE IF NOT EXISTS $order_table_name (
		 id bigint(20) NOT NULL AUTO_INCREMENT,
		 order_id bigint(20) NOT NULL,
		 post_id bigint(20) NOT NULL,
		 post_type varchar(255),
		 room_number varchar(255) NULL,
		 check_in date NOT NULL,  
		 check_out date NULL,  
		 billing_details text,
		 shipping_details text,
		 order_details text,
		 customer_id bigint(11) NOT NULL,
		 payment_method varchar(255),
		 ostatus varchar(255),
		 order_date datetime NOT NULL,
		 checkinout varchar(255) NULL,
		 checkinout_by varchar(255) NULL,
		 room_id varchar(255) NULL,
		 PRIMARY KEY  (id)
	 ) $charset_collate;";
	dbDelta( $sql );
}
add_action('admin_init', 'tf_order_table_create');

/*
* Admin order data migration
* @author Jahid
*/
function tf_admin_order_data_migration(){
	if ( empty( get_option( 'tf_old_order_data_migrate' ) ) ) {

		$tf_old_order_limit = new WC_Order_Query( array (
			'limit' => -1,
			'orderby' => 'date',
			'order' => 'ASC',
			'return' => 'ids',
		) );
		$order = $tf_old_order_limit->get_orders();

		foreach ( $order as $item_id => $item ) {
			$itemmeta = wc_get_order( $item);
			if ( is_a( $itemmeta, 'WC_Order_Refund' ) ) {
				$itemmeta = wc_get_order( $itemmeta->get_parent_id() );
			}
			$tf_ordering_date =  $itemmeta->get_date_created();

			//Order Data Insert
			$billinginfo = [
				'billing_first_name' => !empty($itemmeta->get_billing_first_name()) ? $itemmeta->get_billing_first_name() : '',
				'billing_last_name' => !empty($itemmeta->get_billing_last_name()) ? $itemmeta->get_billing_last_name() : '',
				'billing_company' => !empty($itemmeta->get_billing_company()) ? $itemmeta->get_billing_company() : '',
				'billing_address_1' => !empty($itemmeta->get_billing_address_1()) ? $itemmeta->get_billing_address_1() : '',
				'billing_address_2' => !empty($itemmeta->get_billing_address_2()) ? $itemmeta->get_billing_address_2() : '',
				'billing_city' => !empty($itemmeta->get_billing_city()) ? $itemmeta->get_billing_city() : '',
				'billing_state' => !empty($itemmeta->get_billing_state()) ? $itemmeta->get_billing_state() : '',
				'billing_postcode' => !empty($itemmeta->get_billing_postcode()) ? $itemmeta->get_billing_postcode() : '',
				'billing_country' => !empty($itemmeta->get_billing_country()) ? $itemmeta->get_billing_country() : '',
				'billing_email' => !empty($itemmeta->get_billing_email()) ? $itemmeta->get_billing_email() : '',
				'billing_phone' => !empty($itemmeta->get_billing_phone()) ? $itemmeta->get_billing_phone() : ''
			];

			$shippinginfo = [
				'shipping_first_name' => !empty($itemmeta->get_shipping_first_name()) ? $itemmeta->get_shipping_first_name() : '',
				'shipping_last_name' => !empty($itemmeta->get_shipping_last_name()) ? $itemmeta->get_shipping_last_name() : '',
				'shipping_company' => !empty($itemmeta->get_shipping_company()) ? $itemmeta->get_shipping_company() : '',
				'shipping_address_1' => !empty($itemmeta->get_shipping_address_1()) ? $itemmeta->get_shipping_address_1() : '',
				'shipping_address_2' => !empty($itemmeta->get_shipping_address_2()) ? $itemmeta->get_shipping_address_2() : '',
				'shipping_city' => !empty($itemmeta->get_shipping_city()) ? $itemmeta->get_shipping_city() : '',
				'shipping_state' => !empty($itemmeta->get_shipping_state()) ? $itemmeta->get_shipping_state() : '',
				'shipping_postcode' => !empty($itemmeta->get_shipping_postcode()) ? $itemmeta->get_shipping_postcode() : '',
				'shipping_country' => !empty($itemmeta->get_shipping_country()) ? $itemmeta->get_shipping_country() : '',
				'shipping_phone' => !empty($itemmeta->get_shipping_phone()) ? $itemmeta->get_shipping_phone() : ''
			];

			foreach ( $itemmeta->get_items() as $item_key => $item_values ) {
				$order_type   = wc_get_order_item_meta( $item_key, '_order_type', true );
				if("hotel"==$order_type){
					$post_id   = wc_get_order_item_meta( $item_key, '_post_id', true );
					$unique_id = wc_get_order_item_meta( $item_key, '_unique_id', true );
					$room_selected = wc_get_order_item_meta( $item_key, 'number_room_booked', true );
					$check_in = wc_get_order_item_meta( $item_key, 'check_in', true );
					$check_out = wc_get_order_item_meta( $item_key, 'check_out', true );
					$price = $itemmeta->get_subtotal();
					$due = wc_get_order_item_meta( $item_key, 'due', true );
					$room_name = wc_get_order_item_meta( $item_key, 'room_name', true );
					$adult = wc_get_order_item_meta( $item_key, 'adult', true );
					$child = wc_get_order_item_meta( $item_key, 'child', true );
					$children_ages = wc_get_order_item_meta( $item_key, 'Children Ages', true );
					$airport_service_type = wc_get_order_item_meta( $item_key, 'Airport Service', true );
					$airport_service_fee = wc_get_order_item_meta( $item_key, 'Airport Service Fee', true );

					$iteminfo = [
						'room' => $room_selected,
						'room_unique_id' => $unique_id,
						'check_in' => $check_in,
						'check_out' => $check_out,
						'room_name' => $room_name,
						'adult' => $adult,
						'child' => $child,
						'children_ages' => $children_ages,
						'airport_service_type' => $airport_service_type,
						'airport_service_fee' => $airport_service_fee,
						'total_price' => $price,
						'due_price' => $due,
					];

					$iteminfo_keys = array_keys($iteminfo);
					$iteminfo_keys = array_map('sanitize_key', $iteminfo_keys);

					$iteminfo_values = array_values($iteminfo);
					$iteminfo_values = array_map('sanitize_text_field', $iteminfo_values);

					$iteminfo = array_combine($iteminfo_keys, $iteminfo_values);

					global $wpdb;
					$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO {$wpdb->prefix}tf_order_data
						( order_id, post_id, post_type, room_number, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
						VALUES ( %d, %d, %s, %d, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
							array(
								$item,
								sanitize_key( $post_id ),
								$order_type,
								$room_selected,
								$check_in,
								$check_out,
								wp_json_encode($billinginfo),
								wp_json_encode($shippinginfo),
								wp_json_encode($iteminfo),
								$itemmeta->get_customer_id(),
								$itemmeta->get_payment_method(),
								$itemmeta->get_status(),
								$tf_ordering_date->date('Y-m-d H:i:s')
							)
						)
					);
				}
				if("tour"==$order_type){
					$post_id   = wc_get_order_item_meta( $item_key, '_tour_id', true );
					$tour_date = wc_get_order_item_meta( $item_key, 'Tour Date', true );
					$tour_time = wc_get_order_item_meta( $item_key, 'Tour Time', true );
					$price = $itemmeta->get_subtotal();
					$due = wc_get_order_item_meta( $item_key, 'Due', true );
					$tour_extra = wc_get_order_item_meta( $item_key, 'Tour Extra', true );
					$adult = wc_get_order_item_meta( $item_key, 'Adults', true );
					$child = wc_get_order_item_meta( $item_key, 'Children', true );
					$infants = wc_get_order_item_meta( $item_key, 'Infants', true );
					$datatype_check = preg_match("/-/", $tour_date);
					if ( !empty($tour_date) && !empty($datatype_check) ) {
						list( $tour_in, $tour_out ) = explode( ' - ', $tour_date );
					}
					if ( !empty($tour_date) && empty($datatype_check) ) {
						$tour_in = gmdate( "Y-m-d", strtotime( $tour_date ) );
						$tour_out = "0000-00-00";
					}


					$iteminfo = [
						'tour_date' => $tour_date,
						'tour_time' => $tour_time,
						'tour_extra' => $tour_extra,
						'adult' => $adult,
						'child' => $child,
						'infants' => $infants,
						'total_price' => $price,
						'due_price' => $due,
					];

					$iteminfo_keys = array_keys($iteminfo);
					$iteminfo_keys = array_map('sanitize_key', $iteminfo_keys);

					$iteminfo_values = array_values($iteminfo);
					$iteminfo_values = array_map('sanitize_text_field', $iteminfo_values);

					$iteminfo = array_combine($iteminfo_keys, $iteminfo_values);

					global $wpdb;
					$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO {$wpdb->prefix}tf_order_data
						( order_id, post_id, post_type, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
						VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
							array(
								$item,
								sanitize_key( $post_id ),
								$order_type,
								gmdate( "Y-m-d", strtotime( $tour_in ) ),
								gmdate( "Y-m-d", strtotime( $tour_out ) ),
								wp_json_encode($billinginfo),
								wp_json_encode($shippinginfo),
								wp_json_encode($iteminfo),
								$itemmeta->get_customer_id(),
								$itemmeta->get_payment_method(),
								$itemmeta->get_status(),
								$tf_ordering_date->date('Y-m-d H:i:s')
							)
						)
					);
				}
			}

		}
		wp_cache_flush();
		flush_rewrite_rules( true );
		update_option( 'tf_old_order_data_migrate', 1 );
	}
}
if ( Helper::tf_is_woo_active() ) {
	add_action('admin_init', 'tf_admin_order_data_migration');
}
/*
* Admin order data new field "checkinout & checkinout_by" added
* @author Jahid
*/
if ( ! function_exists( 'tf_admin_table_alter_order_data' ) ) {
	function tf_admin_table_alter_order_data() {
		global $wpdb;
		$order_table_name = $wpdb->prefix . 'tf_order_data';
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		// Check if the 'checkinout' & 'checkinout_by' column exists before attempting to add it
		if ( !$wpdb->get_var("SHOW COLUMNS FROM {$wpdb->prefix}tf_order_data LIKE 'checkinout'") &&
		     !$wpdb->get_var("SHOW COLUMNS FROM {$wpdb->prefix}tf_order_data LIKE 'checkinout_by'") ) {
			$wpdb->query($wpdb->prepare(
				"ALTER TABLE %s 
                ADD COLUMN checkinout varchar(255) NULL,
                ADD COLUMN checkinout_by varchar(255) NULL",
				$order_table_name
			));
		}

		// Check if the 'room_id' column exists before attempting to add it
		if ( !$wpdb->get_var("SHOW COLUMNS FROM {$wpdb->prefix}tf_order_data LIKE 'room_id'") ) {
			$wpdb->query($wpdb->prepare(
				"ALTER TABLE %s 
                ADD COLUMN room_id varchar(255) NULL",
				$order_table_name
			));
		}
	}
}

add_action( 'admin_init', 'tf_admin_table_alter_order_data' );


if(! function_exists( 'get_kses_extended_ruleset' ) ) {
	function tf_kses_svg_esc_rule() {
		$tf_kses_defaults = wp_kses_allowed_html( 'post' );

		$tf_svg_args = array(
			'svg'   => array(
				'class'           => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true,
				'fill' 			  => true, // <= Must be lower case!
			),
			'g'     			  => array( 'fill' => true ),
			'title' 			  => array( 'title' => true ),
			'path'  			  => array(
				'd'    			  => true,
				'fill' 			  => true,
				'stroke'		  => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				"stroke-linejoin" => true,
			),
		);

		return array_merge( $tf_kses_defaults, $tf_svg_args );
	}

}