<?php
/**
 * Regression checks for Tour package traveler and capacity limits.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/tour-package-traveler-limits.php
 */

$root     = dirname( __DIR__, 2 );
$pro_root = dirname( $root ) . '/tourfic-pro';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', $root . '/' );
}

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) );
	}
}

if ( ! function_exists( 'absint' ) ) {
	function absint( $value ) {
		return abs( (int) $value );
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter() {}
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action() {}
}

require_once $root . '/inc/Traits/Singleton.php';
require_once $root . '/inc/Traits/TF_Fonts.php';
require_once $root . '/inc/Traits/Action_Helper.php';
require_once $root . '/inc/Classes/Helper.php';

use Tourfic\Classes\Helper;

function tourfic_package_limit_assert_same( $expected, $actual, $message ) {
	if ( $expected !== $actual ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\nExpected: " . var_export( $expected, true ) . "\nActual: " . var_export( $actual, true ) . "\n";
		exit( 1 );
	}
}

function tourfic_package_limit_assert_contains( $needle, $haystack, $message ) {
	if ( false === strpos( $haystack, $needle ) ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\nMissing: {$needle}\n";
		exit( 1 );
	}
}

$meta = array(
	'pricing'         => 'package',
	'package_pricing' => array(
		array(
			'pricing_type' => 'group',
			'group_tabs'   => array(
				array(),
				array( 'group_price' => '120' ),
				array( 'min_person' => '2' ),
				array( 'max_person' => '8' ),
			),
		),
		array(
			'pricing_type' => 'person',
		),
		array(
			'pricing_type' => 'group',
			'group_tabs'   => array(
				array(),
				array( 'group_price' => '80' ),
				array( 'min_person' => '' ),
				array( 'max_person' => '' ),
			),
		),
	),
);

$at_limit = Helper::tourfic_resolve_tour_package_group_limit( $meta, '0', 3, 2, 3 );
tourfic_package_limit_assert_same( true, $at_limit['is_group'], 'The selected group package must be recognized.' );
tourfic_package_limit_assert_same( 2, $at_limit['minimum'], 'The package minimum must be normalized.' );
tourfic_package_limit_assert_same( 8, $at_limit['maximum'], 'The package maximum must be normalized.' );
tourfic_package_limit_assert_same( 8, $at_limit['requested'], 'Adults, Children, and Infants must all count toward Package Max.' );
tourfic_package_limit_assert_same( false, $at_limit['is_exceeded'], 'A request exactly at Package Max must be allowed.' );

$over_limit = Helper::tourfic_resolve_tour_package_group_limit( $meta, 0, 3, 2, 4 );
tourfic_package_limit_assert_same( 9, $over_limit['requested'], 'Infants must remain part of the server-side package total.' );
tourfic_package_limit_assert_same( true, $over_limit['is_exceeded'], 'A request above Package Max must be rejected.' );

$person_package = Helper::tourfic_resolve_tour_package_group_limit( $meta, 1, 50, 50, 50 );
tourfic_package_limit_assert_same( false, $person_package['is_group'], 'Per-person packages must keep their own per-type limits.' );
tourfic_package_limit_assert_same( 0, $person_package['maximum'], 'Per-person packages must not inherit a group Package Max.' );
tourfic_package_limit_assert_same( false, $person_package['is_exceeded'], 'The group package resolver must not reject per-person packages.' );

$unlimited_package = Helper::tourfic_resolve_tour_package_group_limit( $meta, 2, 20, 10, 5 );
tourfic_package_limit_assert_same( 0, $unlimited_package['maximum'], 'An empty Package Max must remain unlimited at the package level.' );
tourfic_package_limit_assert_same( false, $unlimited_package['is_exceeded'], 'An empty Package Max must not be replaced by another limit.' );

$negative_values = Helper::tourfic_resolve_tour_package_group_limit( $meta, 0, -2, 1, -3 );
tourfic_package_limit_assert_same( 1, $negative_values['requested'], 'Negative traveler values must not reduce the normalized package total.' );

require_once $pro_root . '/inc/classes/TF_Pro_Availability.php';

$availability_reflection = new ReflectionClass( 'TF_Pro_Availability' );
$availability             = $availability_reflection->newInstanceWithoutConstructor();
$capacity_method          = $availability_reflection->getMethod( 'add_capacity_context' );
$capacity_method->setAccessible( true );
$capacity_context = array();
$capacity_method->invokeArgs( $availability, array( &$capacity_context, 8, 5 ) );
tourfic_package_limit_assert_same( true, $capacity_context['adult_child_capacity_limited'], 'A configured schedule capacity must be exposed as limited.' );
tourfic_package_limit_assert_same( 3, $capacity_context['adult_child_remaining_capacity'], 'Only booked Adult/Child travelers may reduce schedule capacity.' );

$unlimited_capacity_context = array();
$capacity_method->invokeArgs( $availability, array( &$unlimited_capacity_context, 0, 5 ) );
tourfic_package_limit_assert_same( false, $unlimited_capacity_context['adult_child_capacity_limited'], 'An empty capacity must remain unlimited.' );
tourfic_package_limit_assert_same( 0, $unlimited_capacity_context['adult_child_remaining_capacity'], 'Unlimited capacity must not create a synthetic package limit.' );

$free_tour    = file_get_contents( $root . '/inc/Classes/Tour/Tour.php' );
$free_submit  = file_get_contents( $root . '/inc/functions/woocommerce/wc-tour.php' );
$free_backend = file_get_contents( $root . '/inc/Admin/Backend_Booking/TF_Tour_Backend_Booking.php' );
$free_source  = file_get_contents( $root . '/sass/app/js/free/tourfic.js' );
$free_asset   = file_get_contents( $root . '/assets/app/js/tourfic-scripts.js' );
$pro_schedule = file_get_contents( $pro_root . '/inc/classes/TF_Pro_Availability.php' );
$pro_backend  = file_get_contents( $pro_root . '/inc/frontend-dashboard/classes/TF_FD_Tour_Backend_Booking_Rest_API.php' );

foreach ( array( $free_tour, $free_submit, $free_backend ) as $source ) {
	tourfic_package_limit_assert_contains(
		'tourfic_resolve_tour_package_group_limit',
		$source,
		'Every Free package booking path must use the shared package resolver.'
	);
}

foreach (
	array(
		"'package_limit'",
		"'remaining_adult_child_capacity'",
		"'adult_child_capacity_exceeded'",
	) as $marker
) {
	tourfic_package_limit_assert_contains( $marker, $free_tour, 'The popup response is missing separate package/capacity data.' );
}

foreach (
	array(
		'tfGetTourPackageAdultChildTotal',
		'tfGetTourPackageRemainingCapacity',
		'tfTourPackageInputUsesCapacity',
		'applyTourPackageLimit',
		'data-package-original-max',
	) as $marker
) {
	tourfic_package_limit_assert_contains( $marker, $free_source, 'Free source is missing package-limit behavior.' );
	tourfic_package_limit_assert_contains( $marker, $free_asset, 'The built Free bundle is missing package-limit behavior.' );
}

foreach (
	array(
		'adult_child_capacity_limited',
		'adult_child_remaining_capacity',
		'add_capacity_context',
	) as $marker
) {
	tourfic_package_limit_assert_contains( $marker, $pro_schedule, 'Pro schedule context is missing Adult/Child capacity data.' );
}

tourfic_package_limit_assert_contains(
	"method_exists( Helper::class, 'tourfic_resolve_tour_package_group_limit' )",
	$pro_backend,
	'Pro frontend-dashboard booking must tolerate a Pro-first staggered update.'
);
tourfic_package_limit_assert_contains(
	"'is_exceeded' => \$is_group && 0 < \$maximum && \$requested > \$maximum",
	$pro_backend,
	'Pro frontend-dashboard booking is missing its old-Free package-limit fallback.'
);

echo "Tour package traveler-limit regression checks passed.\n";
