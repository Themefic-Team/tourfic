<?php
namespace Tourfic\Admin;
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( "WP_List_Table" ) ) {
	require_once( ABSPATH . "wp-admin/includes/class-wp-list-table.php" );
}

class TF_List_Table extends \WP_List_Table {

	private $_items;

	function __construct( $data ) {
		parent::__construct();
		$this->_items = $data;
	}

	function get_columns() {
		return [
			'cb'           => '<input type="checkbox">',
			'uname'        => esc_html__( 'Name', 'tourfic' ),
			'uemail'       => esc_html__( 'Email', 'tourfic' ),
			'udescription' => esc_html__( 'Message', 'tourfic' ),
			'created_at'   => esc_html__( 'Date', 'tourfic' ),
		];
	}

	function column_cb( $item ) {
		return "<input type='checkbox' value='{$item['id']}'>";
	}

	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	//if result more than 15 then add pro row
	public function display_rows() {
		foreach ( $this->items as $key => $item ) {
			if(function_exists( 'is_tf_pro' ) && is_tf_pro()){
				$this->single_row( $item );
			} else {
				if ( $key == 14) {
					$this->single_row( $item );
					echo '<tr class="pro-row" style="text-align: center; background-color: #ededf8"><td colspan="5"><a href="https://tourfic.com/" target="_blank"><h3 class="tf-admin-btn tf-btn-secondary" style="color:#fff;margin: 15px 0;">' . esc_html__( 'Upgrade to Pro Version to see more', 'tourfic' ) . '</h3></a></td></tr>';
				} else {
					$this->single_row( $item );
				}
			}

		}
	}

	function prepare_items() {
		$paged                 = ! empty( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 1;
		$per_page              = 20;
		$total_items           = count( $this->_items );
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$data_chunks           = array_chunk( $this->_items, $per_page );
		$this->items           = ! empty( $data_chunks ) ? $data_chunks[ $paged - 1 ] : '';
		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( count( $this->_items ) / $per_page )
		] );
	}
}