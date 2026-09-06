<?php
/**
 * Regression checks for activation and database lifecycle behavior.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/plugin-lifecycle.php
 */

$tourfic_test_root      = dirname( __DIR__, 2 );
$tourfic_activator_file = file_get_contents( $tourfic_test_root . '/inc/Classes/Activator.php' );
$tourfic_base_file      = file_get_contents( $tourfic_test_root . '/inc/Classes/Base.php' );
$tourfic_database_file  = file_get_contents( $tourfic_test_root . '/inc/Traits/Database.php' );
$tourfic_plugin_file    = file_get_contents( $tourfic_test_root . '/tourfic.php' );
$tourfic_enqueue_file   = file_get_contents( $tourfic_test_root . '/inc/Classes/Enqueue.php' );

function tourfic_lifecycle_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

tourfic_lifecycle_assert(
	false === strpos( $tourfic_activator_file, "add_action( 'init', array( \$this, 'activate' )" ),
	'Activation installation must not run on every init request.'
);
tourfic_lifecycle_assert(
	false === strpos( $tourfic_base_file, "add_action( 'admin_init', array(\$this, 'create_enquiry_database_table')" )
		&& false === strpos( $tourfic_base_file, "add_action('admin_init', array(\$this, 'tf_order_table_create')" ),
	'Database installers must not run unconditionally on every admin request.'
);
tourfic_lifecycle_assert(
	false !== strpos( $tourfic_plugin_file, "register_activation_hook( __FILE__, 'tourfic_activate_plugin' )" )
		&& false !== strpos( $tourfic_plugin_file, '\\Tourfic\\Classes\\Activator::activate( $network_wide )' ),
	'The main plugin file must own the activation installer.'
);
tourfic_lifecycle_assert(
	false !== strpos( $tourfic_database_file, "get_option( 'tourfic_database_version' )" )
		&& false !== strpos( $tourfic_database_file, "update_option( 'tourfic_database_version', TOURFIC_DATABASE_VERSION, false )" ),
	'Database installation must be guarded by a full-prefix schema version option.'
);
tourfic_lifecycle_assert(
	false !== strpos( $tourfic_database_file, "wp_clear_scheduled_hook( 'tf_everydate_cron_job' )" ),
	'The version-gated upgrade must clear the orphaned legacy tour cron event.'
);
tourfic_lifecycle_assert(
	false !== strpos( $tourfic_activator_file, "add_action( 'init', array( \$this, 'tourfic_maybe_flush_rewrite_rules' ), 999 )" )
		&& false !== strpos( $tourfic_activator_file, "delete_option( 'tourfic_flush_rewrite_rules' )" ),
	'Rewrite rules must flush once on normal init after registrations.'
);
tourfic_lifecycle_assert(
	false !== strpos( $tourfic_activator_file, "get_option( 'tourfic_activation_pages_signature' )" )
		&& false !== strpos( $tourfic_activator_file, "update_option( 'tourfic_activation_pages_signature', \$signature, false )" ),
	'Filtered activation pages must only reconcile when their deterministic signature changes.'
);
tourfic_lifecycle_assert(
	false !== strpos( $tourfic_activator_file, "\$option        = 'tourfic_' . \$key . '_page_id'" )
		&& false !== strpos( $tourfic_activator_file, 'delete_option( $legacy_option )' ),
	'Gated page reconciliation must migrate page IDs without recreating short-prefix options.'
);
tourfic_lifecycle_assert(
	false !== strpos( $tourfic_plugin_file, "add_action( 'wp_initialize_site', 'tourfic_initialize_network_site', 200 )" )
		&& false !== strpos( $tourfic_plugin_file, "get_site_option( 'active_sitewide_plugins', array() )" )
		&& false !== strpos( $tourfic_activator_file, 'switch_to_blog( $site_id )' ),
	'Network-active installations must initialize new and existing multisite blogs.'
);
tourfic_lifecycle_assert(
	false !== strpos( $tourfic_enqueue_file, "if ( ! wp_style_is( 'tf-admin', 'enqueued' ) )" ),
	'Upgrade menu CSS must only be attached when Tourfic has enqueued its admin stylesheet.'
);

$generic_admin_enqueue_start = strpos( $tourfic_enqueue_file, 'function tf_enqueue_admin_scripts( $screen )' );
$generic_admin_enqueue_end   = strpos( $tourfic_enqueue_file, 'function tf_dequeue_theplus_script_on_settings_page', $generic_admin_enqueue_start );
$generic_admin_enqueue       = substr( $tourfic_enqueue_file, $generic_admin_enqueue_start, $generic_admin_enqueue_end - $generic_admin_enqueue_start );
tourfic_lifecycle_assert(
	false === strpos( $generic_admin_enqueue, "wp_enqueue_script( 'notyf'" )
		&& false === strpos( $generic_admin_enqueue, "wp_enqueue_style( 'notyf'" ),
	'Generic admin requests must not load Tourfic notification assets.'
);

echo "Tourfic plugin lifecycle regression checks passed.\n";
