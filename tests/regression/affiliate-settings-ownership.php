<?php
/**
 * Regression checks for Affiliate settings ownership.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/affiliate-settings-ownership.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root     = dirname( __DIR__, 2 );
$pro_root = dirname( $root ) . '/tourfic-pro';

function tf_affiliate_ownership_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$free_files = array(
	'inc/Admin/TF_API_Documentation_Examples.php',
	'inc/Admin/TF_Options/options/tf-settings.php',
	'inc/Classes/Helper.php',
	'inc/Traits/Action_Helper.php',
	'inc/functions.php',
	'sass/admin/js/free/admin.js',
	'assets/admin/js/tourfic-admin-scripts.js',
	'assets/admin/js/tourfic-admin-scripts.min.js',
);
$free_surface = '';

foreach ( $free_files as $file ) {
	$free_surface .= file_get_contents( $root . '/' . $file );
}

foreach (
	array(
		"'affiliate' => array(",
		'affiliate_heading',
		'tf-affiliate',
		'tf_affiliate_callback',
		'tf_affiliate_active',
		'tf_affiliate_install',
		'tourfic-affiliate',
		'Tourfic affiliate addon is not installed',
	) as $locked_marker
) {
	tf_affiliate_ownership_assert(
		false === strpos( $free_surface, $locked_marker ),
		'Free still contains locked Affiliate marker ' . $locked_marker . '.'
	);
}

$pro_settings = file_get_contents( $pro_root . '/admin/tf-options/options/tf-settings.php' );
$pro_loader   = file_get_contents( $pro_root . '/tourfic-pro.php' );

foreach (
	array(
		"'affiliate' => array(",
		"array( 'TourficPro', 'affiliate_settings_callback' )",
	) as $pro_marker
) {
	tf_affiliate_ownership_assert(
		false !== strpos( $pro_settings, $pro_marker ),
		'Pro settings are missing Affiliate ownership marker ' . $pro_marker . '.'
	);
}

foreach (
	array(
		'public static function affiliate_settings_callback()',
		'tourfic-affiliate/tourfic-affiliate.php',
		"current_user_can( 'activate_plugins' )",
		"'activate-plugin_' . \$plugin_file",
	) as $pro_marker
) {
	tf_affiliate_ownership_assert(
		false !== strpos( $pro_loader, $pro_marker ),
		'Pro Affiliate promotion is missing ' . $pro_marker . '.'
	);
}

$callback_start  = strpos( $pro_loader, 'public static function affiliate_settings_callback()' );
$callback_end    = strpos( $pro_loader, "\n\t/**", $callback_start + 1 );
$callback_source = substr( $pro_loader, $callback_start, $callback_end - $callback_start );
tf_affiliate_ownership_assert(
	false === strpos( $callback_source, 'update_option(' ) && false === strpos( $callback_source, 'delete_option(' ),
	'Affiliate promotion must not delete or rewrite existing settings data.'
);

echo "Free and Pro Affiliate settings ownership regression checks passed.\n";
