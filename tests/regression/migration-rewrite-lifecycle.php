<?php
/**
 * Behavioral regression checks for migration rewrite scheduling.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/migration-rewrite-lifecycle.php
 */

namespace {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );

	$tourfic_test_options       = array();
	$tourfic_test_option_writes = array();
	$tourfic_test_cache_flushes = 0;
	$tourfic_test_rule_flushes  = 0;

	function get_option( $name, $default = false ) {
		global $tourfic_test_options;

		return array_key_exists( $name, $tourfic_test_options ) ? $tourfic_test_options[ $name ] : $default;
	}

	function update_option( $name, $value, $autoload = null ) {
		global $tourfic_test_options, $tourfic_test_option_writes;

		$tourfic_test_options[ $name ] = $value;
		$tourfic_test_option_writes[]  = array( $name, $value, $autoload );

		return true;
	}

	function wp_cache_flush() {
		global $tourfic_test_cache_flushes;

		++$tourfic_test_cache_flushes;
	}

	function flush_rewrite_rules() {
		global $tourfic_test_rule_flushes;

		++$tourfic_test_rule_flushes;
	}

	function get_posts() {
		return array();
	}

	function get_terms() {
		return array();
	}

	class WC_Order_Query {
		public function __construct( $args ) {
		}

		public function get_orders() {
			return array();
		}
	}
}

namespace Tourfic\Traits {
	trait Singleton {
	}
}

namespace Tourfic\Classes {
	class Helper {
		public static function tfopt() {
			return array();
		}

		public static function tf_data_types( $value ) {
			return is_array( $value ) ? $value : array();
		}

		public static function tf_is_woo_active() {
			return false;
		}
	}
}

namespace {
	require_once dirname( __DIR__, 2 ) . '/inc/Classes/Migrator.php';

	function tourfic_migration_lifecycle_assert( $condition, $message ) {
		if ( ! $condition ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
			echo "FAIL: {$message}\n";
			exit( 1 );
		}
	}

	function tourfic_migration_lifecycle_reset( $options = array() ) {
		global $tourfic_test_options, $tourfic_test_option_writes, $tourfic_test_cache_flushes, $tourfic_test_rule_flushes;

		$tourfic_test_options       = $options;
		$tourfic_test_option_writes = array();
		$tourfic_test_cache_flushes = 0;
		$tourfic_test_rule_flushes  = 0;
	}

	function tourfic_migration_lifecycle_assert_no_direct_flush( $context ) {
		global $tourfic_test_cache_flushes, $tourfic_test_rule_flushes;

		tourfic_migration_lifecycle_assert( 0 === $tourfic_test_cache_flushes, "{$context} must not flush the global object cache." );
		tourfic_migration_lifecycle_assert( 0 === $tourfic_test_rule_flushes, "{$context} must not flush rewrite rules directly." );
	}

	$reflection = new \ReflectionClass( \Tourfic\Classes\Migrator::class );
	$migrator   = $reflection->newInstanceWithoutConstructor();

	tourfic_migration_lifecycle_reset(
		array(
			'tourfic_settings'       => array( 'retained' => 'value' ),
			'tourfic_hotel_slug'     => 'stays',
			'tourfic_tour_slug'      => 'experiences',
			'tourfic_apartment_slug' => 'apartments',
		)
	);
	$migrator->tf_permalink_settings_migration();
	tourfic_migration_lifecycle_assert( true === get_option( 'tourfic_flush_rewrite_rules' ), 'Permalink migration must queue a rewrite flush.' );
	tourfic_migration_lifecycle_assert( 1 === get_option( 'tourfic_permalink_settings_migration' ), 'Permalink migration marker must remain intact.' );
	tourfic_migration_lifecycle_assert( 'value' === get_option( 'tourfic_settings' )['retained'], 'Permalink migration must preserve unrelated settings.' );
	tourfic_migration_lifecycle_assert_no_direct_flush( 'Permalink migration' );

	tourfic_migration_lifecycle_reset();
	$migrator->tf_template_migrate_data();
	tourfic_migration_lifecycle_assert( 1 === get_option( 'tourfic_template_migrate_data' ), 'Template migration marker must remain intact.' );
	tourfic_migration_lifecycle_assert( false === get_option( 'tourfic_flush_rewrite_rules' ), 'Template data must not queue a rewrite flush.' );
	tourfic_migration_lifecycle_assert_no_direct_flush( 'Template migration' );

	tourfic_migration_lifecycle_reset(
		array(
			'tourfic_opt' => array(
				'hotel-permalink-setting' => 'legacy-hotels',
			),
		)
	);
	$migrator->tf_migrate_option_data();
	tourfic_migration_lifecycle_assert( true === get_option( 'tourfic_flush_rewrite_rules' ), 'Full settings migration must queue a rewrite flush.' );
	tourfic_migration_lifecycle_assert( 2 === get_option( 'tourfic_migrate_data_204_210_2022' ), 'Full settings migration marker must remain intact.' );
	tourfic_migration_lifecycle_assert( 'legacy-hotels' === get_option( 'tourfic_settings' )['hotel-permalink-setting'], 'Full settings migration must retain legacy permalink values.' );
	tourfic_migration_lifecycle_assert_no_direct_flush( 'Full settings migration' );

	tourfic_migration_lifecycle_reset();
	$migrator->tf_migrate_data();
	tourfic_migration_lifecycle_assert( 1 === get_option( 'tourfic_migrate_data_204_210' ), 'Content migration marker must remain intact.' );
	tourfic_migration_lifecycle_assert( false === get_option( 'tourfic_flush_rewrite_rules' ), 'Content data must not queue a rewrite flush.' );
	tourfic_migration_lifecycle_assert_no_direct_flush( 'Content migration' );

	tourfic_migration_lifecycle_reset();
	$migrator->tf_admin_order_data_migration();
	tourfic_migration_lifecycle_assert( 1 === get_option( 'tourfic_old_order_data_migrate' ), 'Order migration marker must remain intact.' );
	tourfic_migration_lifecycle_assert( false === get_option( 'tourfic_flush_rewrite_rules' ), 'Order data must not queue a rewrite flush.' );
	tourfic_migration_lifecycle_assert_no_direct_flush( 'Order migration' );

	echo "Tourfic migration rewrite lifecycle checks passed.\n";
}
