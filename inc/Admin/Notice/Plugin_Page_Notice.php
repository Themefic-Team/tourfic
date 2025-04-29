<?php 

namespace Tourfic\Admin\Notice;

// do not allow direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Plugin_Page_Notice extends \Tourfic\Core\TF_Notice {

    function __construct() {
        $this->set_notice_id();
        $this->set_notice_type();
        parent::__construct();
    }

    use \Tourfic\Traits\Singleton;

    function set_notice_type() {
        $this->type = 'update_notice';
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
                    <p></?php echo wp_kses_post( __('We’re excited to announce that our <b>revamped design panel</b> is finally here! In the upcoming update, we’re bringing a <b>new and improved design panel</b> that enhances usability, organization, and customization options for a smoother experience. ', "tourfic")); ?></p>
                    <p></?php echo wp_kses_post( __('Want to know what’s new in this update? Read <a href="https://themefic.com/tourfic-v2-16-0-optimized-design-coming-soon/" target="_blank"><b>this blog</b></a> to explore all the exciting changes.', "tourfic")); ?></p>
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
        wp_die();
    }

	function tf_in_plugin_update_message( $data, $response ){
        if(  isset( $data['update']) && $data["Version"] == "2.16.0") {
            return;
        }
        if ( isset( $data['update'] ) && $data['update'] && $data["new_version"] == '2.16.1') :
            printf( esc_html__('%1$s %2$s The wait is Over! 
            Our %3$s Revamped Design Panel %4$s is now live in this version! We’ve introduced a %3$s new and improved design panel %4$s that enhances usability, organization, and customization options for a smoother experience. 
            This update also includes major  %3$s option panel changes %4$s and %3$s core improvements %4$s.
            %3$s %5$s ⚠️ Please make sure to take a full backup or any necessary precautions before updating the plugin to avoid any compatibility issues. %4$s %6$s %1$s  %7$s', 'tourfic'), 
            '<br>', '<div style="padding-left: 26px; padding-right: 12px;">', '<b>', '</b>', '<span style="color: red; margin-bottom:">', '</div>', '</div>' ) ;
        endif;
    }
}
