<?php

namespace Tourfic\Traits;

defined( 'ABSPATH' ) || exit;

trait Database {

	/**
	 * Upgrade the Tourfic tables when their schema version changes.
	 */
	public function tourfic_maybe_upgrade_database() {
		if ( TOURFIC_DATABASE_VERSION === get_option( 'tourfic_database_version' ) ) {
			return;
		}

		$this->tourfic_install_database();
	}

	/**
	 * Install or update all Tourfic database tables.
	 */
	public function tourfic_install_database() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$this->tourfic_create_enquiry_database_table();
		$this->tourfic_create_order_database_table();

		wp_clear_scheduled_hook( 'tf_everydate_cron_job' );
		update_option( 'tourfic_database_version', TOURFIC_DATABASE_VERSION, false );
	}

	/**
	 * Create or update the enquiry table.
	 */
	private function tourfic_create_enquiry_database_table() {
		global $wpdb;
		$table_name      = $wpdb->prefix . 'tf_enquiry_data';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        post_type varchar(255),
        uname varchar(255),
        uemail varchar(255),  
        udescription text,
        author_id bigint(20) NOT NULL,
        author_roles varchar(255),
		enquiry_status varchar(255) NOT NULL DEFAULT 'read',
		server_data varchar(255) NOT NULL DEFAULT '',
		reply_data LONGTEXT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
		dbDelta( $sql );

	}

	/**
	 * Create or update the order table.
	 */
	private function tourfic_create_order_database_table() {

		global $wpdb;
		$order_table_name = $wpdb->prefix.'tf_order_data';
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $order_table_name (
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
}
