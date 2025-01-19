<?php 

namespace Tourfic\Admin\Notice;

// do not allow direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Room_Notice extends \Tourfic\Core\TF_Notice {

    function __construct() {
        $this->set_notice_type();
        parent::__construct();
    }

    use \Tourfic\Traits\Singleton;

    function set_notice_type() {
        $this->type = 'admin_notice';
    }

    // Red Color: style="color:#d63638;

    // License activation notice for Tourfic Pro
    function tf_plugin_admin_notice( ) { 
		if ( get_option( 'tf_dismiss_211' ) < 1 ) {
            ?>
                <div class="tf-critical-update-notice notice notice-info is-dismissible">
                    <p><?php echo wp_kses_post( __('<b">A Big Improvement to Tourfic Coming Soon: </b>We’re excited to announce that we’ve decided to take the <b>Rooms</b> for the <b>Tourfic Design Panel</b> to the next level! The existing design panel will be revamped into a cleaner, smarter, and more user-friendly interface. To learn more about the upcoming design panel <a href="https://themefic.com/tourfics-big-improvement-a-cleaner-smarter-design-panel/" target="_blank"><b>check this blog</b></a>.', "tourfic")); ?></p>
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
        update_option( 'tf_dismiss_211', 1 );
        wp_die();
    }

	function tf_in_plugin_update_message( $data, $response ){}
}