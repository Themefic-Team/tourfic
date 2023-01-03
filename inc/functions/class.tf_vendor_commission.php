<?php

if ( ! class_exists( "WP_List_Table" ) ) {
	require_once( ABSPATH . "wp-admin/includes/class-wp-list-table.php" );
}

class TFVENDORTable extends WP_List_Table {

	private $_items;
	function __construct( $data ) {
		parent::__construct();
		$this->_items = $data;
	}

	function get_columns() {
		return [
			'cb'     => '<input type="checkbox">',
			'uid'   => __( 'ID', 'tourfic' ),
			'uname'   => __( 'Affiliate', 'tourfic' ),
			'earns'  => __( 'Total Earns', 'tourfic' ),
			'type'  => __( 'Type', 'tourfic' ),
			'created_at'  => __( 'Registered', 'tourfic' )
		];
	}

    function column_uid( $item ) {
		return $item->ID;
	}
    function column_cb( $item ) {
		return "<input type='checkbox' name='vendor_id' value='{$item->ID}'>";
	}
    function column_uname( $item ) {
		$actions = [
			'edit'   => sprintf( '<a href="admin.php?page=tf_vendor_list&user_id=%s&actions=%s">%s</a>', $item->ID, 'edit', __( 'Edit', 'database-demo' ) ),
		];

		return sprintf('%s %s',$item->display_name,$this->row_actions($actions));
	}
    function column_uemail( $item ) {
		return $item->user_email;
	}
    function column_uphone( $item ) {
		return get_user_meta($item->ID,'tf_user_phone',true);
	}
    function column_type( $item ) {
		return 'Booking';
	}
	function column_earns( $item ) {
		$vendor_total_earning = 0;

		$tf_vendor_commision = !empty(tf_data_types(tfopt( 'multi-vendor-setings' ))["partner_commission"]) ? intval(tf_data_types(tfopt( 'multi-vendor-setings' ))["partner_commission"]) : '';

		$hotel_query_orders = wc_get_orders( array('_order_type' => 'hotel', 'status' => 'wc-completed') );
		foreach ( $hotel_query_orders as $order ) {
			foreach ( $order->get_items() as $item_key => $item_values ) {
				$post_id = !empty(wc_get_order_item_meta( $item_key, '_post_id', true )) ? wc_get_order_item_meta( $item_key, '_post_id', true ) : wc_get_order_item_meta( $item_key, '_tour_id', true );
				$post_author   = get_post_field( 'post_author', $post_id );
				if(intval($post_author)==$item->ID){
					$vendor_due = ! empty( wc_get_order_item_meta( $item_key, 'due', true ) ) ? wc_get_order_item_meta( $item_key, 'due', true ) : 0;
					$vendor_item_total_earning = $item_values->get_subtotal() + $vendor_due;
					$total_commision = ($vendor_item_total_earning*$tf_vendor_commision)/100;
					$vendor_total_earning += $total_commision;
				}
			}
		}

		$tours_query_orders = wc_get_orders( array('_order_type' => 'tour', 'status' => 'wc-completed') );
		foreach ( $tours_query_orders as $order ) {
			foreach ( $order->get_items() as $item_key => $item_values ) {
				$post_id = !empty(wc_get_order_item_meta( $item_key, '_post_id', true )) ? wc_get_order_item_meta( $item_key, '_post_id', true ) : wc_get_order_item_meta( $item_key, '_tour_id', true );
				$post_author   = get_post_field( 'post_author', $post_id );
				if(intval($post_author)==$item->ID){
					$vendor_due = ! empty( wc_get_order_item_meta( $item_key, 'due', true ) ) ? wc_get_order_item_meta( $item_key, 'due', true ) : 0;
					$vendor_item_total_earning = $item_values->get_subtotal() + $vendor_due;
					$total_commision = ($vendor_item_total_earning*$tf_vendor_commision)/100;
					$vendor_total_earning += $total_commision;
				}
			}
		}

		return wc_price($vendor_total_earning);
	}
    
    function column_created_at( $item ) {
		return date("M d, Y", strtotime($item->user_registered));
	}
	function column_default( $item, $column_name ) {
		
	}
	function prepare_items() {
		$paged                 = !empty( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 1;
		$per_page              = 10;
		$total_items           = count( $this->_items );
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$data_chunks           = array_chunk( $this->_items, $per_page );
		$this->items           = !empty( $data_chunks ) ? $data_chunks[ $paged - 1 ] : '';
		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( count( $this->_items ) / $per_page )
		] );
	}
}