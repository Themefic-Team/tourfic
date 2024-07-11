<?php

namespace Tourfic\Traits;

defined( 'ABSPATH' ) || exit;

trait Database {

	function create_enquiry_database_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . 'tf_enquiry_data';
		$charset_collate = $wpdb->get_charset_collate();
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        post_type varchar(255),
        uname varchar(255),
        uemail varchar(255),  
        udescription text,
        author_id bigint(20) NOT NULL,
        author_roles varchar(255),
        created_at datetime NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
		dbDelta( $sql );

	}

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