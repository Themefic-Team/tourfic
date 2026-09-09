<?php
/**
 * Regression checks for the version-specific pre-update backup notice.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/update-backup-notice.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$tourfic_update_notice_actions        = array();
$tourfic_update_notice_is_multisite   = false;
$tourfic_update_notice_network_admin  = false;
$tourfic_update_notice_site_transient = false;

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	global $tourfic_update_notice_actions;

	$tourfic_update_notice_actions[ $hook ] = array( $callback, $priority, $accepted_args );
}

function is_multisite() {
	global $tourfic_update_notice_is_multisite;

	return $tourfic_update_notice_is_multisite;
}

function is_network_admin() {
	global $tourfic_update_notice_network_admin;

	return $tourfic_update_notice_network_admin;
}

function get_site_transient( $name ) {
	global $tourfic_update_notice_site_transient;

	return 'update_plugins' === $name ? $tourfic_update_notice_site_transient : false;
}

function _get_list_table( $class_name ) {
	return new class() {
		public function get_column_count() {
			return 4;
		}
	};
}

function esc_html_e( $text, $domain = 'default' ) {
	echo htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
}

function esc_attr( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}

function wp_kses_post( $text ) {
	return $text;
}

function wpautop( $text ) {
	return '<p>' . $text . '</p>';
}

function tourfic_update_notice_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$root           = dirname( __DIR__, 2 );
$plugin_source  = file_get_contents( $root . '/tourfic.php' );
$readme_source  = file_get_contents( $root . '/readme.txt' );
$version_match  = array();
$upgrade_match  = array();

tourfic_update_notice_assert(
	1 === preg_match( '/^[ \t*#@]*Version:\s*(.+)$/mi', $plugin_source, $version_match ),
	'The main plugin header must declare a version.'
);

$plugin_version = trim( $version_match[1] );
$upgrade_pattern = '/== Upgrade Notice ==\s+= ' . preg_quote( $plugin_version, '/' ) . ' =\s+(.+?)(?:\n= |\n== )/s';

tourfic_update_notice_assert(
	1 === preg_match( $upgrade_pattern, $readme_source, $upgrade_match ),
	'The readme must contain an Upgrade Notice entry matching the plugin version.'
);

$upgrade_notice = trim( $upgrade_match[1] );

tourfic_update_notice_assert(
	false !== stripos( $upgrade_notice, 'back up' ),
	'The Upgrade Notice must tell administrators to create a backup.'
);
tourfic_update_notice_assert(
	false !== stripos( $upgrade_notice, 'compatible Tourfic Pro/add-on versions' ),
	'The Upgrade Notice must explain the companion-version requirement.'
);
tourfic_update_notice_assert(
	300 >= strlen( $upgrade_notice ),
	'The Upgrade Notice must remain within the WordPress.org 300-character limit.'
);

require_once $root . '/inc/Traits/Singleton.php';
require_once $root . '/inc/Core/TF_Notice.php';
require_once $root . '/inc/Admin/Notice_Update.php';

$notice = new \Tourfic\Admin\Notice_Update();

ob_start();
$notice->tf_in_plugin_update_message(
	array(),
	(object) array(
		'upgrade_notice' => $upgrade_notice,
	)
);
$single_site_output = ob_get_clean();

tourfic_update_notice_assert(
	false !== strpos( $single_site_output, 'Heads up, Please backup before upgrade!' )
		&& false !== strpos( $single_site_output, $upgrade_notice ),
	'The standard plugin update hook must render the response upgrade notice.'
);

ob_start();
$notice->tf_in_plugin_update_message( array(), (object) array() );
$empty_output = ob_get_clean();

tourfic_update_notice_assert(
	'' === $empty_output,
	'The standard plugin update hook must remain silent without upgrade metadata.'
);

$tourfic_update_notice_is_multisite   = true;
$tourfic_update_notice_site_transient = (object) array(
	'response' => array(
		'tourfic/tourfic.php' => (object) array(
			'new_version'   => $plugin_version,
			'upgrade_notice' => $upgrade_notice,
		),
	),
);

ob_start();
$notice->tf_ms_plugin_update_message(
	'tourfic/tourfic.php',
	array(
		'Version' => '2.23.3',
	)
);
$multisite_output = ob_get_clean();

tourfic_update_notice_assert(
	false !== strpos( $multisite_output, 'plugin-update-tr' )
		&& false !== strpos( $multisite_output, $upgrade_notice ),
	'The individual multisite plugin screen must render the pending upgrade notice.'
);

$tourfic_update_notice_network_admin = true;

ob_start();
$notice->tf_ms_plugin_update_message(
	'tourfic/tourfic.php',
	array(
		'Version' => '2.23.3',
	)
);
$network_output = ob_get_clean();

tourfic_update_notice_assert(
	'' === $network_output,
	'The multisite fallback must not duplicate the notice in Network Admin.'
);

echo "Tourfic version-specific update backup notice regression checks passed.\n";
