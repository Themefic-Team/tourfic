<?php
/**
 * Regression checks for legacy Tour availability migration ownership.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/legacy-tour-availability-migration.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root     = dirname( __DIR__, 2 );
$pro_root = dirname( $root ) . '/tourfic-pro';

function tf_legacy_availability_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

function add_filter() {}
function add_action() {}
function sanitize_key( $value ) {
	return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) );
}
function absint( $value ) {
	return abs( (int) $value );
}
function maybe_unserialize( $value ) {
	$result = @unserialize( $value );

	return false === $result ? $value : $result;
}

$tf_legacy_availability_options = array();
function get_option( $key ) {
	global $tf_legacy_availability_options;

	return $tf_legacy_availability_options[ $key ] ?? false;
}
function update_option( $key, $value ) {
	global $tf_legacy_availability_options;

	$tf_legacy_availability_options[ $key ] = $value;
}

if ( ! class_exists( 'Tourfic\\Classes\\Helper' ) ) {
	// phpcs:ignore Squiz.PHP.Eval.Discouraged -- Isolated CLI test stub.
	eval( 'namespace Tourfic\\Classes; class Helper { public static function tf_is_woo_active() { return false; } }' );
}
require_once $root . '/inc/Traits/Singleton.php';
require_once $root . '/inc/Classes/Migrator.php';
require_once $pro_root . '/inc/classes/TF_Pro_Availability.php';

$free_migrator = new Tourfic\Classes\Migrator();
$free_builder  = new ReflectionMethod( $free_migrator, 'tf_migrate_continuous_tour_availability' );
$free_builder->setAccessible( true );
$free_rules = $free_builder->invoke(
	$free_migrator,
	array(
		'custom_avail'    => 1,
		'cont_custom_date' => array(
			array(
				'date'         => array(
					'from' => '2026/08/17',
					'to'   => '2026/08/18',
				),
				'adult_price'  => '25',
				'child_price'  => '10',
				'infant_price' => '0',
				'group_price'  => '250',
				'allowed_time' => array(
					array(
						'time'         => '12:00 PM',
						'max_capacity' => '8',
					),
				),
			),
		),
	)
);
tf_legacy_availability_assert( 2 === count( $free_rules ), 'Free changed the inclusive custom-date migration range.' );
foreach ( $free_rules as $free_rule ) {
	tf_legacy_availability_assert( 'person' === $free_rule['pricing_type'], 'Free did not retain per-person pricing.' );
	tf_legacy_availability_assert( ! isset( $free_rule['allowed_time'] ), 'Free migrated a Pro-owned start time.' );
	tf_legacy_availability_assert( '' === $free_rule['price'], 'Free migrated a Pro-owned group price.' );
}

$owner = new TF_Pro_Availability();
$core  = array(
	'2026/08/17 - 2026/08/17' => array(
		'check_in'     => '2026/08/17',
		'check_out'    => '2026/08/17',
		'pricing_type' => 'person',
		'price'        => '',
		'adult_price'  => '25',
		'child_price'  => '10',
		'infant_price' => '0',
		'min_person'   => '1',
		'max_person'   => '10',
		'max_capacity' => '10',
		'status'       => 'available',
	),
);

$unchanged = $owner->legacy_tour_availability_data( $core, array(), 10 );
tf_legacy_availability_assert( $core === $unchanged, 'Pro changed a Free-only continuous availability rule.' );

$enhanced = $owner->legacy_tour_availability_data(
	$core,
	array(
		'type'         => 'continuous',
		'pricing'      => 'group',
		'group_price'  => '250',
		'allowed_time' => array(
			array(
				'time'              => '12:00 PM',
				'cont_max_capacity' => '8',
			),
		),
	),
	10
);
$enhanced_rule = $enhanced['2026/08/17 - 2026/08/17'];
tf_legacy_availability_assert( 'group' === $enhanced_rule['pricing_type'], 'Pro did not restore legacy group pricing.' );
tf_legacy_availability_assert( '250' === $enhanced_rule['price'], 'Pro did not restore the legacy group price.' );
tf_legacy_availability_assert(
	array( '12:00 PM' ) === $enhanced_rule['allowed_time']['time'],
	'Pro did not restore the legacy start time.'
);

$fixed = $owner->legacy_tour_availability_data(
	array(),
	array(
		'type'               => 'fixed',
		'pricing'            => 'person',
		'adult_price'        => '100',
		'child_price'        => '50',
		'infant_price'       => '0',
		'fixed_availability' => array(
			'date'                      => array(
				'from' => '2026/08/17',
				'to'   => '2026/08/22',
			),
			'min_seat'                  => '2',
			'max_seat'                  => '20',
			'max_capacity'              => '20',
			'tf-repeat-months-checkbox' => array( '08' ),
		),
	),
	10
);
tf_legacy_availability_assert(
	isset( $fixed['2026/08/17 - 2026/08/22'] ),
	'Pro did not rebuild legacy Fixed Tour availability.'
);
tf_legacy_availability_assert(
	'100' === $fixed['2026/08/17 - 2026/08/22']['adult_price'],
	'Fixed Tour person pricing changed during migration.'
);

$tf_legacy_availability_options = array( 'tf_tour_availability_migration' => 1 );
$owner->migrate_legacy_tour_availability();
tf_legacy_availability_assert(
	1 === get_option( 'tf_pro_tour_availability_migration' ),
	'Pro did not recognize a legacy migration completed by the former combined owner.'
);

echo "Legacy Tour availability migration ownership checks passed.\n";
