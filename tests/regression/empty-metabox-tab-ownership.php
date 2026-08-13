<?php
/**
 * Regression checks for Free, Pro, and iCal metabox tab ownership.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/empty-metabox-tab-ownership.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root      = dirname( __DIR__, 2 );
$pro_root  = dirname( $root ) . '/tourfic-pro';
$ical_root = dirname( $root ) . '/tourfic-ical';

function tf_empty_metabox_tab_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$free_metaboxes = array(
	'inc/Admin/TF_Options/metaboxes/tf-tour-metabox.php'      => array( "'tour_extra'", 'tour-extras-heading' ),
	'inc/Admin/TF_Options/metaboxes/tf-hotel-metabox.php'     => array( "'hotel_extra'", 'hotel-extras-heading' ),
	'inc/Admin/TF_Options/metaboxes/tf-carrental-metabox.php' => array( "'car_extra'", 'car-extra-heading', "'cancellation'", 'car-cancellation-heading' ),
	'inc/Admin/TF_Options/metaboxes/tf-room-metabox.php'      => array( "'room_ical'", "'id'      => 'ical'" ),
);

foreach ( $free_metaboxes as $file => $markers ) {
	$source = file_get_contents( $root . '/' . $file );

	foreach ( $markers as $marker ) {
		tf_empty_metabox_tab_assert(
			false === strpos( $source, $marker ),
			$file . ' still registers empty Free tab marker ' . $marker . '.'
		);
	}
}

$pro_metaboxes = array(
	'admin/tf-options/metaboxes/tf-tour-metabox.php'      => array( "'tour_extra'", "'id'           => 'tour-extra'" ),
	'admin/tf-options/metaboxes/tf-hotel-metabox.php'     => array( "'hotel_extra'", "'id'      => 'hotel_extra_option'", "'id'           => 'hotel-extra'" ),
	'admin/tf-options/metaboxes/tf-carrental-metabox.php' => array( "'car_extra'", "'id'          => 'car_extra_sec_title'", "'id'           => 'extras'", "'cancellation'", "'id'       => 'cancellation_section'", "'id'           => 'calcellation_policy'" ),
	'admin/tf-options/metaboxes/tf-room-metabox.php'      => array( "'room_ical'", "'id'    => 'ical'", 'tf_tourfic_ical_download_button' ),
);

foreach ( $pro_metaboxes as $file => $markers ) {
	$source = file_get_contents( $pro_root . '/' . $file );

	foreach ( $markers as $marker ) {
		tf_empty_metabox_tab_assert(
			false !== strpos( $source, $marker ),
			$file . ' is missing Pro-owned tab marker ' . $marker . '.'
		);
	}
}

$pro_loader    = file_get_contents( $pro_root . '/tourfic-pro.php' );
$ical_bootstrap = file_get_contents( $ical_root . '/tourfic-ical.php' );
$ical_settings  = file_get_contents( $ical_root . '/inc/ical-settings/room-settings.php' );

tf_empty_metabox_tab_assert(
	false !== strpos( $pro_loader, "add_filter( 'tourfic_admin_metabox_files'" )
		&& false !== strpos( $pro_loader, "glob( TF_PRO_ADMIN_PATH . 'tf-options/metaboxes/*.php' )" ),
	'Pro must replace the Free metabox file set with its complete definitions.'
);
tf_empty_metabox_tab_assert(
	false !== strpos( $ical_bootstrap, 'Requires Plugins: tourfic-pro' ),
	'iCal must continue declaring Tourfic Pro as its required plugin.'
);
foreach ( array( "add_filter( 'tf_room_opt_sections'", "'id'           => 'ical_url'", "'id'           => 'ical_export'" ) as $marker ) {
	tf_empty_metabox_tab_assert(
		false !== strpos( $ical_settings, $marker ),
		'iCal is missing Room metabox injection marker ' . $marker . '.'
	);
}

echo "Free, Pro, and iCal metabox tab ownership regression checks passed.\n";
