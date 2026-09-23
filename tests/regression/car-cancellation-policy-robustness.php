<?php
/**
 * Regression checks for malformed Car cancellation policies.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/car-cancellation-policy-robustness.php
 */

namespace Tourfic\Classes {
	class Helper {
		public static function tf_is_woo_active() {
			return false;
		}

		public static function tfopt() {
			return false;
		}
	}
}

namespace {
	$root = dirname( __DIR__, 2 );

	define( 'ABSPATH', $root . '/' );
	define( 'TOURFIC_INC_PATH', $root . '/inc/' );

	function add_action() {
		return true;
	}

	function sanitize_text_field( $value ) {
		return is_scalar( $value ) ? trim( strip_tags( (string) $value ) ) : '';
	}

	function tourfic_normalize_date( $date ) {
		return str_replace( '-', '/', $date );
	}

	function assert_car_policy( $condition, $message ) {
		if ( ! $condition ) {
			fwrite( STDERR, "FAIL: {$message}\n" );
			exit( 1 );
		}
	}

	require $root . '/inc/functions/functions-car.php';

	$malformed = array( '[]', null, false, array(), array( 'cancellation_type' => 'free' ) );
	assert_car_policy(
		null === tourfic_getBestRefundPolicy( $malformed, '2030/01/01', '10:00 AM' ),
		'Malformed cancellation data must not produce a best policy.'
	);
	assert_car_policy(
		array() === tourfic_getRefundPolicy( $malformed, '2030/01/01', '10:00 AM' ),
		'Malformed cancellation data must be ignored without a fatal error.'
	);

	$valid_policy = array(
		'cancellation-times' => 'day',
		'cancellation_type'  => 'free',
		'before_cancel_time' => 1,
	);
	$mixed_data   = array_merge( $malformed, array( $valid_policy ) );

	assert_car_policy(
		$valid_policy === tourfic_getBestRefundPolicy( $mixed_data, '2030/01/01', '10:00 AM' ),
		'A valid policy must still be selected when malformed legacy entries are present.'
	);
	assert_car_policy(
		array( $valid_policy ) === tourfic_getRefundPolicy( $mixed_data, '2030/01/01', '10:00 AM' ),
		'Valid policies must remain available after malformed entries are skipped.'
	);

	echo "Car cancellation-policy robustness checks passed.\n";
}
