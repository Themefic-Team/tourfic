<?php
/**
 * Regression checks for shared admin asset dependencies.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/companion-admin-asset-dependencies.php
 */

$tourfic_test_root = dirname( __DIR__, 2 );
$plugins_root      = dirname( $tourfic_test_root );
$free_source       = file_get_contents( $tourfic_test_root . '/inc/Classes/Enqueue.php' );
$pro_source        = file_get_contents( $plugins_root . '/tourfic-pro/tourfic-pro.php' );
$ical_source       = file_get_contents( $plugins_root . '/tourfic-ical/inc/functions.php' );

function tourfic_companion_asset_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

tourfic_companion_asset_assert(
	false !== strpos( $free_source, "add_action( 'admin_enqueue_scripts', array( \$this, 'register_admin_dependencies' ), 1 )" )
		&& false !== strpos( $free_source, "wp_register_style( 'notyf'" )
		&& false !== strpos( $free_source, "wp_register_script( 'notyf'" ),
	'Free must register shared Notyf assets before companion enqueue callbacks run.'
);
tourfic_companion_asset_assert(
	false !== strpos( $pro_source, "\$tf_pro_dependencies = array( 'jquery', 'notyf' )" )
		&& false !== strpos( $pro_source, "array( 'notyf' ), TF_PRO" ),
	'Pro must declare Notyf as both a script and style dependency.'
);
tourfic_companion_asset_assert(
	false !== strpos( $ical_source, "wp_enqueue_style( 'notyf' )" )
		&& false !== strpos( $ical_source, "array( 'jquery', 'notyf' )" ),
	'iCal must enqueue Notyf styling and declare its script dependency.'
);

echo "Tourfic companion admin asset dependency regression checks passed.\n";
