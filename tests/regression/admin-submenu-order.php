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

$settings_source      = file_get_contents( $root . '/inc/Admin/TF_Options/classes/TF_Settings.php' );
$api_source           = file_get_contents( $root . '/inc/Admin/TF_API_Documentation.php' );
$pro_source           = file_get_contents( $pro_root . '/tourfic-pro.php' );
$pro_functions_source = file_get_contents( $pro_root . '/inc/functions.php' );
$pro_export_source    = file_get_contents( $pro_root . '/inc/functions/functions-export.php' );
$license_source       = file_get_contents( $pro_root . '/inc/license/license.php' );
$email_piping_root    = dirname( $root ) . '/tourfic-email-piping';
$imap_source            = file_get_contents( $email_piping_root . '/includes/Classes/ImapConnection.php' );
$gmail_source           = file_get_contents( $email_piping_root . '/includes/Classes/GmailConnection.php' );

$pro_check_tourfic_start = strpos( $pro_source, 'public function check_tourfic()' );
$pro_constants_start     = strpos( $pro_source, 'private function define_constants()' );
$pro_hooks_start         = strpos( $pro_source, 'private function init_hooks()' );
$pro_check_tourfic       = substr( $pro_source, $pro_check_tourfic_start, $pro_constants_start - $pro_check_tourfic_start );
$pro_constants           = substr( $pro_source, $pro_constants_start, $pro_hooks_start - $pro_constants_start );

$shortcodes_offset = strpos( $settings_source, '// Shortcode submenu' );
$help_offset       = strpos( $settings_source, '//Get Help submenu' );
$library_menu_marker = "\$library_url,\n\t\t\t\t\t'',\n\t\t\t\t\t3";
$api_menu_marker   = "'tourfic_api_docs',\n\t\t\tarray( \$this, 'render_page' ),\n\t\t\t\$position";
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
	false === strpos( $pro_constants, 'TOURFIC_PRO_SETTINGS_MENU_SLUG' )
		&& false !== strpos( $pro_check_tourfic, "defined( 'TOURFIC_PRO_SETTINGS_MENU_SLUG' )" )
		&& false !== strpos( $pro_check_tourfic, "defined( 'TOURFIC_SETTINGS_MENU_SLUG' ) ? TOURFIC_SETTINGS_MENU_SLUG : 'tf_settings'" ),
	'Pro must resolve its shared settings parent after Free has loaded.'
);
tf_admin_submenu_order_assert(
	false !== strpos( $license_source, "'position'    => 7" ),
	'License must use submenu position 7.'
);
tf_admin_submenu_order_assert(
	false !== strpos( $pro_functions_source, "defined( 'TOURFIC_SETTINGS_MENU_SLUG' ) ? 'tourfic_dashboard' : 'tf_dashboard'" ),
	'Pro Settings action must resolve the dashboard slug for both released and renamed Free versions.'
);
tf_admin_submenu_order_assert(
	false !== strpos( $pro_export_source, "'tourfic_tours_booking'" )
		&& false !== strpos( $pro_export_source, "'tourfic_apartment_booking'" )
		&& false !== strpos( $pro_export_source, "'tourfic_hotel_booking'" ),
	'Pro booking exports must target the registered Free booking pages.'
);
foreach ( array( 'hotel', 'tours', 'apartment' ) as $enquiry_type ) {
	tf_admin_submenu_order_assert(
		false !== strpos( $pro_export_source, "'tourfic_{$enquiry_type}_enquiry'" )
			&& false !== strpos( $pro_export_source, "'tf_{$enquiry_type}_enquiry'" ),
		"Pro {$enquiry_type} enquiry exports must support both released and renamed Free pages."
	);
	tf_admin_submenu_order_assert(
		false !== strpos( $imap_source, "'tourfic_{$enquiry_type}_enquiry'" )
			&& false !== strpos( $imap_source, "'tf_{$enquiry_type}_enquiry'" )
			&& false !== strpos( $gmail_source, "'tourfic_{$enquiry_type}_enquiry'" )
			&& false !== strpos( $gmail_source, "'tf_{$enquiry_type}_enquiry'" ),
		"Email Piping must initialize on both released and renamed {$enquiry_type} enquiry pages."
	);
}

echo "Tourfic Free and Pro admin submenu order regression checks passed.\n";
