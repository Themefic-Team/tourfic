<?php

namespace Tourfic\Admin;

// do not allow direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Notice_Update extends \Tourfic\Core\TF_Notice {

    function __construct() {
        $this->set_notice_type();
        parent::__construct();

        add_action( 'in_plugin_update_message-tourfic/tourfic.php', array( $this, 'tf_in_plugin_update_message' ), 10, 2 );
        add_action( 'after_plugin_row_tourfic/tourfic.php', array( $this, 'tf_ms_plugin_update_message' ), 10, 2 );
    }

    use \Tourfic\Traits\Singleton;

    function set_notice_type() {
        $this->type = 'admin_notice';
    }

	function tf_plugin_admin_notice() {
	}

    // Turned off for now, will be used in future
    function tf_critical_update_admin_notice() {
        if ( get_option( 'tourfic_dismiss_210' ) < 1 ) {
            ?>
                <div class="tf-critical-update-notice notice notice-error is-dismissible">
                    <p><?php esc_html_e( '<b style="color:#d63638;">NOTICE: </b>To provide you with a better and improved experience for the coming days, we have completely revamped our options panel for the <b>Hotel</b> post type. This includes a complete restructuring of the <b>Features</b> section. If you added any icons on the features, then you need to re-add the icons again. Please watch this <a href="https://themefic.com/docs/tourfic/updated-features-section-for-hotel/" target="_blank"><b>video</b></a> to know how to do it. ', 'tourfic' ); ?></p>
                </div>

                <script>
                    jQuery(document).ready(function($) {
                        $(document).on('click', '.tf-critical-update-notice .notice-dismiss', function( event ) {
                            data = {
                                action : 'tf_disable_critical_update_admin_notice',
                                nonce : '<?php echo esc_js( wp_create_nonce( $this->tf_notice_ajax_nonce_action() ) ); ?>',
                            };

                            $.post(ajaxurl, data, function (response) {
                            });
                        });
                    });
                </script>
            <?php
        }
    }

    function tf_in_plugin_update_message( $data, $response ) {
        $upgrade_notice = $this->get_upgrade_notice( $response );

        if ( '' === $upgrade_notice ) {
            return;
        }

        $this->render_upgrade_notice( $upgrade_notice );
    }

    // WordPress Core omits plugin update rows on individual sites in a multisite network.
    function tf_ms_plugin_update_message( $file, $plugin ) {
        if ( ! is_multisite() || is_network_admin() ) {
            return;
        }

        $updates = get_site_transient( 'update_plugins' );

        if ( ! is_object( $updates ) || empty( $updates->response[ $file ] ) ) {
            return;
        }

        $response        = $updates->response[ $file ];
        $upgrade_notice  = $this->get_upgrade_notice( $response );
        $current_version = isset( $plugin['Version'] ) ? (string) $plugin['Version'] : '';
        $new_version     = isset( $response->new_version ) ? (string) $response->new_version : '';

        if (
            '' === $upgrade_notice ||
            '' === $current_version ||
            '' === $new_version ||
            ! version_compare( $current_version, $new_version, '<' )
        ) {
            return;
        }

        $wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
        ?>
        <tr class="plugin-update-tr">
            <td
                colspan="<?php echo esc_attr( $wp_list_table->get_column_count() ); ?>"
                class="plugin-update update-message notice inline notice-warning notice-alt"
            >
                <div class="update-message"><p>
                    <?php $this->render_upgrade_notice( $upgrade_notice ); ?>
                </p></div>
            </td>
        </tr>
        <?php
    }

    private function get_upgrade_notice( $response ) {
        if ( ! is_object( $response ) || ! isset( $response->upgrade_notice ) ) {
            return '';
        }

        return trim( (string) $response->upgrade_notice );
    }

    private function render_upgrade_notice( $upgrade_notice ) {
        ?>
        <br><strong><?php esc_html_e( 'Heads up, Please backup before upgrade!', 'tourfic' ); ?></strong>
        <?php echo wp_kses_post( str_replace( array( '<p>', '</p>' ), '', wpautop( $upgrade_notice ) ) ); ?>
        <?php
    }
}
