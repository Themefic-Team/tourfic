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

    // License activation notice for Tourfic Pro
    function tf_plugin_admin_notice( ) { 
		if ( get_option( 'tf_dismiss_211' ) < 1 ) {
            ?>
                <div class="tf-critical-update-notice notice notice-error is-dismissible">
                    <p><?php echo wp_kses_post( __('<b style="color:#d63638;">NOTICE: </b>To provide you with a better and improved experience for the coming days, we have introduced a new post type called <b>Rooms</b> for the <b>Hotel</b> post type in the upcoming update. This includes a complete restructuring of the <b>Room</b> section. If you added a room before, it will automatically migrate to the new post type. To learn more about the upcoming updates <a href="https://themefic.com/introducing-the-rooms-custom-post-type/" target="_blank"><b>check here</b></a>.', "tourfic")); ?></p>
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