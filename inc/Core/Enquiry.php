<?php

namespace Tourfic\Core;

defined( 'ABSPATH' ) || exit;

abstract class Enquiry {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_submenu' ) );
	}

	abstract public function add_submenu();

	function enquiry_table($post_type = 'tf_hotel'){
		global $wpdb;
		$current_user = wp_get_current_user();
		$current_user_role = $current_user->roles[0];

		if ( $current_user_role == 'administrator' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE post_type = %s ORDER BY id DESC", $post_type ), ARRAY_A );
		} elseif ( $current_user_role == 'administrator' ) {
			$enquiry_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}tf_enquiry_data WHERE post_type = %s ORDER BY id DESC LIMIT 15", $post_type ), ARRAY_A );
		}

		$enquiry_results = new \Tourfic\Admin\TF_List_Table( $enquiry_result );
		$enquiry_results->prepare_items();
		$enquiry_results->display();
	}

}