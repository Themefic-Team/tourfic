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

}