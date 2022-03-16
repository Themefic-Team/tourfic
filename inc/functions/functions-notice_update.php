<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Admin Notice
 */
function tf_critical_update_admin_notice() {
    if(is_admin()) {
        if ( get_option( 'tf_dismiss_210' ) < 1 ) {
            ?>
                <div class="tf-critical-update-notice notice notice-error is-dismissible">
                    <p><?php _e( '<b style="color:#d63638;">NOTICE: </b>To provide you with a better and improved experience for the coming days, we have completely revamped our options panel for the <b>Hotel</b> post type. This includes a complete restructuring of the <b>Features</b> section. If you added any icons on the features, then you need to re-add the icons again. Please watch this <a href="https://themefic.com/docs/tourfic/updated-features-section-for-hotel/" target="_blank"><b>video</b></a> to know how to do it. ', 'tourfic' ); ?></p>
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
}
add_action( 'admin_notices', 'tf_critical_update_admin_notice' );

/**
 * Ajax disable critical update admin notice
 */
function tf_disable_critical_update_admin_notice() {
	update_option( 'tf_dismiss_210', 1 );
	wp_die();
}
add_action( 'wp_ajax_tf_disable_critical_update_admin_notice', 'tf_disable_critical_update_admin_notice' );

/**
 * Plugin Update Notice
 * 
 * for critical update
 * 
 * Get contents from trunk/readme.txt (== Upgrade Notice ==)
 */
// For single site
function tf_plugin_update_message( $data, $response ) {
	if( isset( $data['upgrade_notice'] ) ) {
		echo '<span style="background: #D64D21;color: #fff;padding: 10px 10px 12px 10px;margin: 20px 0 15px 2px;display: block;border-radius: 2px;line-height: 18px;"><b>IMPORTANT UPGRADE NOTICE: </b>' . str_replace(['<p>', '</p>'], '', wpautop( $data['upgrade_notice']) ) . '</span>';
	}
}
add_action( 'in_plugin_update_message-tourfic/tourfic.php', 'tf_plugin_update_message', 10, 2 );

// For single site of multisite
function tf_ms_plugin_update_message( $file, $plugin ) {
	if( is_multisite() && version_compare( $plugin['Version'], $plugin['new_version'], '<') ) {
		if( isset( $data['upgrade_notice'] ) ) {
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
				echo '<tr class="plugin-update-tr"><td colspan="' .$wp_list_table->get_column_count(). '" class="plugin-update update-message notice inline notice-warning notice-alt"><div class="update-message"><span style="background: #D64D21;color: #fff;padding: 10px 10px 12px 10px;margin: 20px 0 15px 2px;display: block;border-radius: 2px;line-height: 18px;"><b>IMPORTANT UPGRADE NOTICE: </b>' .str_replace(['<p>', '</p>'], '', wpautop( $plugin['upgrade_notice'] )). '</span></div></td></tr>';		
		}
	}
}
add_action( 'after_plugin_row_tourfic/tourfic.php', 'tf_ms_plugin_update_message', 10, 2 );

/**
 * Add Pro link in menu.
 */
if ( !is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) ) {
	function tf_add_pro_link_menu() {
		$prolink = 'https://tourfic.com/go/upgrade';
		$menuname = '<span style="color:#ffba00;">' .__("Upgrade to Pro", "tourfic"). '</span>';
		add_submenu_page( 'tourfic', __('Upgrade to Pro', 'tourfic'), $menuname, 'manage_options', $prolink);
	}
	add_action('admin_menu', 'tf_add_pro_link_menu', 9999);
}

/**
 * Add plugin action links.
 *
 */
function tf_plugin_action_links( $links ) {

	$settings_link = array(
		'<a href="admin.php?page=tourfic">' . esc_html__( 'Settings', 'tourfic' ) . '</a>',
	);

	if ( !is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) ) {
		$gopro_link = array(
			'<a href="https://tourfic.com/go/upgrade" target="_blank" style="color:#cc0000;font-weight: bold;text-shadow: 0px 1px 1px hsl(0deg 0% 0% / 28%);">' . esc_html__( 'GO PRO', 'tourfic' ) . '</a>',
		);

        return array_merge( $settings_link, $links, $gopro_link );
	} else {
		return array_merge( $settings_link, $links );
	}
}
add_filter( 'plugin_action_links_' . 'tourfic/tourfic.php', 'tf_plugin_action_links' );
?>