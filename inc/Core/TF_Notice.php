<?php

namespace Tourfic\Core;

// do not allow direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class TF_Notice {

    protected string $type;

    public function __construct() {

        add_action( 'wp_ajax_tf_disable_critical_update_admin_notice', array( $this, 'tf_disable_critical_update_admin_notice' ) );

        if($this->type == 'admin_notice') {
            add_action( 'admin_notices', array( $this,'tf_plugin_admin_notice') );
        } else if( $this->type == 'update_notice') {
            add_action( 'in_plugin_update_message-tourfic/tourfic.php', array( $this, 'tf_in_plugin_update_message' ), 10, 2 );
        } else if( $this->type == 'plugin_row_notice') {
            add_action( 'after_plugin_row_tourfic/tourfic.php', array( $this, 'tf_in_plugin_update_message' ), 10, 2 );
        }
    }

    function tf_disable_critical_update_admin_notice() {
        update_option( 'tf_dismiss_211', 1 );
        wp_die();
    }

    abstract function tf_plugin_admin_notice( );
    abstract function tf_in_plugin_update_message( $data, $response );

}