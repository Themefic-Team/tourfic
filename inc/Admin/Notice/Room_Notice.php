<?php 

namespace Tourfic\Admin\Notice;

// do not allow direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Room_Notice extends \Tourfic\Core\TF_Notice {

    function __construct() {
        $this->set_notice_id();
        $this->set_notice_type();
        parent::__construct();
    }

    use \Tourfic\Traits\Singleton;

    function set_notice_type() {
        $this->type = 'admin_notice';
    }

    function set_notice_id() {
        $this->notice_id = 'tf_dismiss_222';
    }

    // Red Color: style="color:#d63638;

    // License activation notice for Tourfic Pro
    function tf_plugin_admin_notice( ) { 
		if ( get_option( $this->notice_id ) < 1 ) {
            ?>
                <div class="tf-critical-update-notice notice notice-info is-dismissible">
                    <h2><?php echo esc_html__("After Update Support", 'tourfic') ?></h2>
                    <p><?php echo wp_kses_post( __(' If you experience any inconvenience after updating to <b>Tourfic version 2.16.5 </b>, please don\'t hesitate to reach out to our <a href="https://portal.themefic.com/support/" target="_blank"><b>Support Team</b></a>. We\'re  here to assist you with any inconvenience.', "tourfic")); ?></p>
                </div>

                <script>
                    jQuery(document).ready(function($) {
                        $(document).on('click', '.tf-critical-update-notice .notice-dismiss', function( event ) {
                            data = {
                                action : 'tf_disable_critical_update_admin_notice',
                            };

                            $.post(ajaxurl, data, function (response) {
                            });
                        });
                    });
                </script>
            <?php
        }
	}

	function tf_disable_critical_update_admin_notice() {
        update_option( $this->notice_id, 1 );
        echo "<pre>";
        print_r($this->notice_id);
        echo "</pre>";
        die(); // added by - Sunvi
        wp_die();
    }

	function tf_in_plugin_update_message( $data, $response ){}
}
