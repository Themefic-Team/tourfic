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
        $this->notice_id = 'tf_dismiss_221';
    }

    // Red Color: style="color:#d63638;

    // License activation notice for Tourfic Pro
    function tf_plugin_admin_notice( ) { 
		if ( get_option( $this->notice_id ) < 1 ) {
            ?>
                <!-- <div class="tf-critical-update-notice notice notice-info is-dismissible">
                    <h2></?php echo esc_html__("The Wait is Over! Our Revamped Design Panel will be Introducing Soon!", 'tourfic') ?></h2>
                    <p></?php echo wp_kses_post( __('We are pleased to introduce the beta version of our <b>revamped design panel</b>, offering an enhanced and more intuitive customization experience. You can now explore the improved interface, test the latest features, and provide valuable feedback before the official release.', "tourfic")); ?></p>
		    <p></?php echo wp_kses_post( __('Discover the latest enhancements in our revamped design panel! Test the beta version today and experience the improved customization firsthand. Read <a href="https://themefic.com/tourfic-v2-16-0-revamped-design-panel-launching-soon/" target="_blank"><b>this blog</b></a> for a detailed overview and access to the beta version download link.', "tourfic")); ?></p>
                </div> -->

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
