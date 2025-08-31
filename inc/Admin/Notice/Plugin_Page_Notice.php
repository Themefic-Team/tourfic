<?php 

namespace Tourfic\Admin\Notice;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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

    function tf_plugin_admin_notice( ) { 
		if ( get_option( $this->notice_id ) < 1 ) {
            
        }
	}

	function tf_disable_critical_update_admin_notice() {
        update_option( $this->notice_id, 1 );
        wp_die();
    }

	function tf_in_plugin_update_message( $data, $response ){
        $versions = array( '2.16.0', '2.16.1' );

        if(  isset( $data['update']) && in_array( $data["new_version"], $versions ) ) {
            return;
        }
        if ( isset( $data['update'] ) && $data['update'] && $data["new_version"] == '2.16.2' ) :

            printf(
                wp_kses_post(
                    // translators: 1: line break <br>, 2: container div start, 3: opening bold tag <b>, 4: closing bold tag </b>, 5: warning span <span style="color:red;">, 6: container div end, 7: closing outer div end.
                    sprintf( esc_html__('%1$s %2$s The wait is Over! 
                        Our %3$s Revamped Design Panel %4$s is now live in this version! We’ve introduced a %3$s new and improved design panel %4$s that enhances usability, organization, and customization options for a smoother experience. 
                        This update also includes major  %3$s option panel changes %4$s and %3$s core improvements %4$s.
                        %3$s %5$s ⚠️ Please make sure to take a full backup or any necessary precautions before updating the plugin to avoid any compatibility issues. %4$s %6$s %1$s  %7$s', 'tourfic'),
                        '<br>',
                        '<div style="padding-left: 26px; padding-right: 12px;">',
                        '<b>',
                        '</b>',
                        '<span style="color: red; margin-bottom:">',
                        '</div>',
                        '</div>'
                    )
                )
            );
        
        endif;        
    }
}
