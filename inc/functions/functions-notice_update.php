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
//add_action( 'admin_notices', 'tf_critical_update_admin_notice' );

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
		?>
		
		<hr class="tf-major-update-warning__separator">
		<div class="tf-major-update-warning">
			<div class="tf-major-update-warning__icon">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 310.285 310.285" xmlns:v="https://vecta.io/nano"><path d="M264.845 45.441C235.542 16.139 196.583 0 155.142 0S74.743 16.139 45.44 45.441 0 113.703 0 155.144s16.138 80.399 45.44 109.701 68.262 45.44 109.702 45.44 80.399-16.138 109.702-45.44 45.44-68.262 45.44-109.701-16.137-80.401-45.439-109.703zm-132.673 3.895c2.399-2.483 5.637-3.873 9.119-3.873h28.04c3.482 0 6.72 1.403 9.114 3.888s3.643 5.804 3.514 9.284l-4.634 104.895c-.263 7.102-6.26 12.933-13.368 12.933H146.33c-7.112 0-13.099-5.839-13.345-12.945L128.64 58.594c-.121-3.48 1.133-6.773 3.532-9.258zm23.306 219.444c-16.266 0-28.532-12.844-28.532-29.876 0-17.223 12.122-30.211 28.196-30.211 16.602 0 28.196 12.423 28.196 30.211.001 17.591-11.456 29.876-27.86 29.876z"/></svg>
			</div>
			<div>
				<div class="tf-major-update-warning__title">
					<?php _e('Heads up, Please backup before upgrade!', 'tourfic'); ?>
				</div>
				<div class="tf-major-update-warning__message">
					<?php echo str_replace(['<p>', '</p>'], '', wpautop( $data['upgrade_notice'])); ?>
				</div>
			</div>
		</div>

		<!-- echo '<span style="background: #D64D21;color: #fff;padding: 10px 10px 12px 10px;margin: 20px 0 15px 2px;display: block;border-radius: 2px;line-height: 18px;"><b>IMPORTANT UPGRADE NOTICE: </b>' . str_replace(['<p>', '</p>'], '', wpautop( $data['upgrade_notice']) ) . '</span>'; -->
	<?php
	}
}
add_action( 'in_plugin_update_message-tourfic/tourfic.php', 'tf_plugin_update_message', 10, 2 );

// For single site of multisite
function tf_ms_plugin_update_message( $file, $plugin ) {
	if( is_multisite() && version_compare( $plugin['Version'], $plugin['new_version'], '<') ) {
		if( isset( $data['upgrade_notice'] ) ) {
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
				echo '<tr class="plugin-update-tr"><td colspan="' .$wp_list_table->get_column_count(). '" class="plugin-update update-message notice inline notice-warning notice-alt"><div class="update-message"><span style="background: #D64D21;color: #fff;padding: 10px 10px 12px 10px;margin: 20px 0 15px 2px;display: block;border-radius: 2px;line-height: 18px;"><b>'. __('IMPORTANT UPGRADE NOTICE:', 'tourfic') .' </b>' .str_replace(['<p>', '</p>'], '', wpautop( $plugin['upgrade_notice'] )). '</span></div></td></tr>';
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
		'<a href="admin.php?page=tf_dashboard">' . esc_html__( 'Settings', 'tourfic' ) . '</a>',
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

/**
 * Licence Activation Admin Notice
 */
function tf_licence_activation_admin_notice() {
    if(is_admin()) { ?>
		<div class="tf-critical-update-notice notice notice-error" style="background: #FFECEC; padding: 20px 12px;">
			<p>
				<?php echo sprintf( 
				__( '<b style="color:#d63638;">NOTICE: </b> Please <a href="%s"><b>Activate</b></a> your Tourfic Pro license. You can get your license key from our Client Portal -> Support -> License Keys.', 'tourfic' ),admin_url().'admin.php?page=tf_license_info'
				); ?>
			</p>
		</div>
	<?php
    }
}
if ( is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) && function_exists( 'is_tf_pro' ) && !is_tf_pro() ) {
	add_action( 'admin_notices', 'tf_licence_activation_admin_notice' );
}

/**
 * Active Licence on plugin action links.
 *
 */
function tf_pro_plugin_licence_action_links( $links ) {

	$active_licence_link = array(
		'<a href="'.admin_url().'admin.php?page=tf_license_info" style="color:#cc0000;font-weight: bold;text-shadow: 0px 1px 1px hsl(0deg 0% 0% / 28%);">' . esc_html__( 'Activate the Licence', 'tourfic' ) . '</a>',
	);

	return array_merge( $links, $active_licence_link );
}
if ( is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) && function_exists( 'is_tf_pro' ) && !is_tf_pro() ) {
	add_filter( 'plugin_action_links_' . 'tourfic-pro/tourfic-pro.php', 'tf_pro_plugin_licence_action_links' );
}