<?php

if ( ! class_exists( "WP_List_Table" ) ) {
	require_once( ABSPATH . "wp-admin/includes/class-wp-list-table.php" );
}

class DBTFTOURTable extends WP_List_Table {

	private $_items;

	function __construct( $data ) {
		parent::__construct();
		$this->_items = $data;
	}

	function get_columns() {
        if(function_exists( 'is_tf_pro' ) && is_tf_pro()){
            return [
                'cb'           => '<input type="checkbox">',
                'order_id'        => __( 'Order ID', 'tourfic' ),
                'cdetails'       => __( 'Customer Details', 'tourfic' ),
                'odetails' => __( 'Order Details', 'tourfic' ),
                'odate'   => __( 'Order Date', 'tourfic' ),
                'tprice'   => __( 'Total Price', 'tourfic' ),
                'status'   => __( 'Status', 'tourfic' ),
                'voucher'   => __( 'Vouchers', 'tourfic' ),
                'pmethod'   => __( 'Payment Method', 'tourfic' ),
                'oedit'   => '',
            ];
        }else{
            return [
                'cb'           => '<input type="checkbox">',
                'order_id'        => __( 'Order ID', 'tourfic' ),
                'cdetails'       => __( 'Customer Details', 'tourfic' ),
                'odetails' => __( 'Order Details', 'tourfic' ),
                'odate'   => __( 'Order Date', 'tourfic' ),
                'tprice'   => __( 'Total Price', 'tourfic' ),
                'status'   => __( 'Status', 'tourfic' ),
                'pmethod'   => __( 'Payment Method', 'tourfic' ),
                'oedit'   => '',
            ];
        }
	}

	function column_cb( $item ) {
		return "<input type='checkbox' value='{$item['id']}'>";
	}
    function column_cdetails( $item ) {
		$billing_info = json_decode($item['billing_details']);
        $billing_details = "";
        $customer_name        = $billing_info->billing_first_name . ' ' . $billing_info->billing_last_name;
        $customer_email       = $billing_info->billing_email;
        $customer_phone       = $billing_info->billing_phone;
        $customer_address     = $billing_info->billing_address_1 . ', ' . $billing_info->billing_address_2 . ',<br>' . $billing_info->billing_city . ', ' . WC()->countries->countries[ $billing_info->billing_country ];

        if ( $customer_name ) {
            $billing_details .= '<b>' . __( "Name", "tourfic" ) . ': </b>' . $customer_name . '<br>';
        }
        if ( $customer_email ) {
            $billing_details .= '<b>' . __( "E-mail", "tourfic" ) . ': </b>' . $customer_email . '<br>';
        }
        if ( $customer_phone ) {
            $billing_details .= '<b>' . __( "Phone", "tourfic" ) . ': </b>' . $customer_phone . '<br>';
        }
        if ( $customer_address ) {
            $billing_details .= '<b>' . __( "Address", "tourfic" ) . ': </b>' . $customer_address . '<br>';
        }
        return $billing_details;
	}
    function column_odetails( $item ) {
		$order_details = json_decode($item['order_details']);
        $hotel_order_details = "";
        if ( !empty($item['post_id']) ) {
            $hotel_order_details .= '<b>' . __( "Tour Name", "tourfic" ) . ': </b><a href="'.get_the_permalink($item['post_id']).'" target="_blank">' . get_the_title($item['post_id']) . '</a><br>';
        }
        if ( !empty($order_details->tour_date) ) {
            $hotel_order_details .= '<b>' . __( "Tour Date", "tourfic" ) . ': </b>' . $order_details->tour_date . '<br>';
        }
        if ( !empty($order_details->tour_time) ) {
            $hotel_order_details .= '<b>' . __( "Tour Time", "tourfic" ) . ': </b>' . $order_details->tour_time . '<br>';
        }
        if ( !empty($order_details->adult) ) {
            $hotel_order_details .= '<b>' . __( "Adult Number", "tourfic" ) . ': </b>' . $order_details->adult . '<br>';
        }
        if ( !empty($order_details->child) ) {
            $hotel_order_details .= '<b>' . __( "Child Number", "tourfic" ) . ': </b>' . $order_details->child . '<br>';
        }
        if ( !empty($order_details->infants) ) {
            $hotel_order_details .= '<b>' . __( "Infant Number", "tourfic" ) . ': </b>' . $order_details->infants . '<br>';
        }
        if ( !empty($order_details->tour_extra) ) {
            $hotel_order_details .= '<b>' . __( "Tour Extra", "tourfic" ) . ': </b>' . $order_details->tour_extra . '<br>';
        }
        return $hotel_order_details;
	}
    function column_tprice( $item ) {
		$order_details = json_decode($item['order_details']);
        $hotel_order_details = "";
        if ( !empty($order_details->total_price) ) {
            $hotel_order_details .= '<b>' . __( "Total", "tourfic" ) . ': </b>' . wc_price($order_details->total_price) . '<br>';
        }
        if ( !empty($order_details->due_price) ) {
            $hotel_order_details .= '<b>' . __( "Due", "tourfic" ) . ': </b>' . $order_details->due_price . '<br>';
        }
        return $hotel_order_details;
	}
    function column_odate( $item ) {
		return $item['order_date'];
	}
    function column_status( $item ) {
		return $item['ostatus'];
	}
    function column_voucher( $item ) {
		$order_details = json_decode($item['order_details']);
        if( !empty($order_details->unique_id) ){
            $order_checkin_code = 'tf_'.$order_details->unique_id;
            $tf_order_checkin = get_option( $order_checkin_code );
            if( empty($tf_order_checkin) ){
                return '<div class="tf-booking-status-swt"><div class="tf-booking-status"><span>#'.$order_details->unique_id.'</span><label class="switch"><input type="checkbox" class="tf-ticket-status" value="'.$order_details->unique_id.'"><span class="switcher round"></span>
                </label></div></div>';
            }else{
                return '<div class="tf-booking-status-swt"><div class="tf-booking-status"><span>#'.$order_details->unique_id.'</span><label class="switch"><input type="checkbox" class="tf-ticket-status" value="'.$order_details->unique_id.'" checked=""><span class="switcher round"></span>
                </label></div></div>';
            }
        }

	}
    function column_pmethod( $item ) {
        if( ! function_exists( 'tf_get_payment_method_full_name' ) ){
            function tf_get_payment_method_full_name($sort_name) {
                $payment_gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();
            
                if (isset($payment_gateways[$sort_name])) {
                    return $payment_gateways[$sort_name]->title;
                } else {
                    return 'Unknown Payment Method';
                }
            }
        }
        
        $sort_name = $item['payment_method'];
        $full_name = tf_get_payment_method_full_name($sort_name);
        return $full_name;
        
	}
    function column_oedit( $item ) {
        $current_user = wp_get_current_user();
        // get user id
        $current_user_id = $current_user->ID;
        // get user role
        $current_user_role = $current_user->roles[0];
        if ( $current_user_role == 'administrator' ) {
            return '<a href="'.admin_url().'post.php?post='.$item['order_id'].'&amp;action=edit" class="button button-secondary">Edit</a>';
        }
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
					echo '<tr class="pro-row" style="text-align: center; background-color: #ededf8"><td colspan="9"><a href="https://tourfic.com/" target="_blank"><h3 class="tf-admin-btn tf-btn-secondary" style="color:#fff;margin: 15px 0;">' . __( 'Upgrade to Pro Version to see more', 'tourfic' ) . '</h3></a></td></tr>';
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