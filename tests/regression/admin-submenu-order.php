<?php
/**
 * Regression checks for the shared Tourfic Free and Pro admin submenu order.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/admin-submenu-order.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root     = dirname( __DIR__, 2 );
$pro_root = dirname( $root ) . '/tourfic-pro';

function tf_admin_submenu_order_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$settings_source = file_get_contents( $root . '/inc/Admin/TF_Options/classes/TF_Settings.php' );
$api_source      = file_get_contents( $root . '/inc/Admin/TF_API_Documentation.php' );
$pro_source      = file_get_contents( $pro_root . '/tourfic-pro.php' );
$license_source  = file_get_contents( $pro_root . '/inc/license/license.php' );

$shortcodes_offset = strpos( $settings_source, '// Shortcode submenu' );
$help_offset       = strpos( $settings_source, '//Get Help submenu' );
$library_menu_marker = "\$library_url,\n\t\t\t\t\t'',\n\t\t\t\t\t3";
$api_menu_marker   = "'tf_api_docs',\n\t\t\tarray( \$this, 'render_page' ),\n\t\t\t\$position";
$builder_menu_marker = "'edit.php?post_type=tf_template_builder',\n\t\t\t\t'',\n\t\t\t\t2";
$builder_fallback_marker = "'tf_template_builder',\n"
	. "\t\t\tarray( '\\Tourfic\\App\\Templates\\Template_Builder', 'tf_template_builder_elementor_check' ),\n"
	. "\t\t\t2";

tf_admin_submenu_order_assert(
	false !== $shortcodes_offset && false !== $help_offset && $shortcodes_offset < $help_offset,
	'Free must register Shortcodes before Get Help.'
);
tf_admin_submenu_order_assert(
	false !== strpos( $settings_source, $library_menu_marker ),
	'Template Library must use the position immediately after Template Builder.'
);
tf_admin_submenu_order_assert(
	false !== strpos( $api_source, "add_action( 'admin_menu', array( \$this, 'register_menu' ), 80 )" )
		&& false !== strpos( $api_source, "? 4 : 3" )
		&& false !== strpos( $api_source, $api_menu_marker ),
	'API Documentation must account for the conditional Template Library position.'
);
tf_admin_submenu_order_assert(
	false !== strpos( $pro_source, $builder_menu_marker )
		&& false !== strpos( $pro_source, $builder_fallback_marker ),
	'Template Builder must use submenu position 2 for both supported-builder paths.'
);
tf_admin_submenu_order_assert(
	false !== strpos( $license_source, "'position'    => 7" ),
	'License must use submenu position 7.'
);

echo "Tourfic Free and Pro admin submenu order regression checks passed.\n";
