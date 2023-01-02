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
			'uname'   => __( 'Store', 'tourfic' ),
			'uemail'  => __( 'Email', 'tourfic' ),
			'uphone'  => __( 'Phone', 'tourfic' ),
			'created_at'  => __( 'Registered', 'tourfic' ),
			'status'  => __( 'Status', 'tourfic' ),
		];
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
    function column_status( $item ) {
        $vendor_status = get_user_meta($item->ID,'tf_vendor_approval',true);
        if(!empty($vendor_status) && $vendor_status=="enabled"){
			return "
			<div class='tf-users-switcher'>
				<label class='switch'>
				<input type='checkbox' class='vendor-status-switcher' value='{$item->ID}' checked=''>
				<span class='switcher round'></span>
				</label>
			</div>";
        }else{
            return "
            <div class='tf-users-switcher'>
                <label class='switch'>
                <input type='checkbox' value='{$item->ID}' class='vendor-status-switcher'>
                <span class='switcher round'></span>
                </label>
            </div>";
        }
	}
    function column_created_at( $item ) {
		return date("M d, Y", strtotime($item->user_registered));
	}
	function column_default( $item, $column_name ) {
		tf_var_dump($item);
	}
    function extra_tablenav( $which ) {
		if('top'==$which):
		?>
		<div class="actions alignleft vendor-actions">
			<select name="tf_vendor_bulk" id="tf_vendor_bulk">
				<option value="">Bulk Actions</option>
				<option value="approved">Approve Vendors</option>
				<option value="pending">Disable Selling</option>
			</select>
			<?php
			submit_button(__('Apply','tourfic'),'button','submit',false);
			?>
		</div>
    <?php
	endif;
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