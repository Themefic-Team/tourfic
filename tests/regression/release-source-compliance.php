<?php
/**
 * Regression checks for release source documentation and bundled Select2 files.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/release-source-compliance.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root          = dirname( __DIR__, 2 );
$readme        = file_get_contents( $root . '/readme.txt' );
$select2_js    = $root . '/assets/app/libs/select2/select2.min.js';
$select2_css   = $root . '/assets/app/libs/select2/select2.min.css';
$composer      = json_decode( file_get_contents( $root . '/composer.json' ), true );
$composer_lock = json_decode( file_get_contents( $root . '/composer.lock' ), true );

function tf_release_source_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

tf_release_source_assert(
	false === strpos( $readme, 'Free up to 20' ),
	'Readme must not advertise an unsupported Free usage quota.'
);
foreach (
	array(
		'https://github.com/Themefic-Team/tourfic',
		'https://github.com/fullcalendar/fullcalendar',
		'https://github.com/chartjs/Chart.js',
		'https://github.com/fancyapps/fancybox',
		'https://github.com/flatpickr/flatpickr',
		'https://github.com/FortAwesome/Font-Awesome',
		'https://github.com/craftpip/jquery-confirm',
		'https://github.com/jquery-validation/jquery-validation',
		'https://github.com/Leaflet/Leaflet',
		'https://github.com/googlemaps/v3-utility-library',
		'https://github.com/caroso1222/notyf',
		'https://github.com/aleinbanger/al-range-slider',
		'https://github.com/Remix-Design/RemixIcon',
		'https://github.com/select2/select2/tree/4.1.0',
		'https://github.com/kenwheeler/slick',
	) as $source_url
) {
	tf_release_source_assert(
		false !== strpos( $readme, $source_url ),
		'Readme is missing public source URL ' . $source_url . '.'
	);
}

tf_release_source_assert(
	'15692c266649fbc8acb4ebfb345e7e9499486d9551148241a235c9d62eea2f55' === hash_file( 'sha256', $select2_js ),
	'Bundled Select2 JavaScript must match the official 4.1.0 distribution.'
);
tf_release_source_assert(
	'5d5e14d587308b5d1ad79f51e9aea2f8f469b0a02b335f559f5a4bb756b81737' === hash_file( 'sha256', $select2_css ),
	'Bundled Select2 CSS must match the official 4.1.0 distribution.'
);
tf_release_source_assert(
	'GPL-2.0-or-later' === ( $composer['license'] ?? '' ),
	'Composer metadata must declare Tourfic\'s GPL-2.0-or-later license.'
);
tf_release_source_assert(
	'themefic/tourfic' === ( $composer['name'] ?? '' ),
	'Composer metadata must declare the Tourfic package name.'
);
tf_release_source_assert(
	! empty( $composer['description'] ),
	'Composer metadata must include a package description.'
);
tf_release_source_assert(
	'wordpress-plugin' === ( $composer['type'] ?? '' ),
	'Composer metadata must identify Tourfic as a WordPress plugin.'
);
tf_release_source_assert(
	'>=7.4' === ( $composer['require']['php'] ?? '' ),
	'Composer metadata must match Tourfic\'s PHP 7.4 minimum requirement.'
);
tf_release_source_assert(
	'>=7.4' === ( $composer_lock['platform']['php'] ?? '' ),
	'Composer lock metadata must match the manifest PHP requirement.'
);

foreach (
	array(
		'assets/admin/images/ai-modal-bg.png',
		'assets/admin/images/Ellipse_2009.png',
		'sass/app/js/itinerary-map.js',
	) as $orphaned_file
) {
	tf_release_source_assert(
		! file_exists( $root . '/' . $orphaned_file ),
		'Orphaned release file still exists: ' . $orphaned_file . '.'
	);
}

echo "PASS: release source documentation and bundled Select2 checks.\n";
