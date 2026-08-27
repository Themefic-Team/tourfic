<?php
/**
 * Regression checks for Apartment booking guest limits.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/apartment-booking-guest-limits.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action() {}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter() {}
}

if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $text ) {
		return $text;
	}
}

require_once dirname( __DIR__, 2 ) . '/inc/functions/woocommerce/wc-apartment.php';

function tf_apartment_guest_limits_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$dates = array( '2026-08-27', '2026-08-29' );

$blank_limit_errors = tf_apartment_get_booking_validation_errors(
	123,
	1,
	2,
	1,
	$dates,
	array(
		'max_adults'   => '',
		'max_children' => '',
		'max_infants'  => '',
	)
);
tf_apartment_guest_limits_assert(
	empty( $blank_limit_errors ),
	'Blank guest limits must allow otherwise valid Apartment bookings.'
);

$zero_limit_errors = tf_apartment_get_booking_validation_errors(
	123,
	1,
	2,
	1,
	$dates,
	array(
		'max_adults'   => '0',
		'max_children' => 0,
		'max_infants'  => '0',
	)
);
tf_apartment_guest_limits_assert(
	empty( $zero_limit_errors ),
	'Zero guest limits must remain the stored representation of no limit.'
);

$configured_limit_errors = tf_apartment_get_booking_validation_errors(
	123,
	3,
	2,
	2,
	$dates,
	array(
		'max_adults'   => 2,
		'max_children' => 1,
		'max_infants'  => 1,
	)
);
tf_apartment_guest_limits_assert(
	3 === count( $configured_limit_errors )
		&& in_array( 'Maximum 2 Adult(s) allowed.', $configured_limit_errors, true )
		&& in_array( 'Maximum 1 Children(s) allowed.', $configured_limit_errors, true )
		&& in_array( 'Maximum 1 Infant(s) allowed.', $configured_limit_errors, true ),
	'Positive Apartment guest limits must still reject counts above the configured maximum.'
);

$negative_errors = tf_apartment_get_booking_validation_errors( 123, -1, 0, 0, $dates, array() );
tf_apartment_guest_limits_assert(
	in_array( 'Guest count cannot be negative.', $negative_errors, true ),
	'Apartment bookings must continue to reject negative guest counts.'
);

$missing_adult_errors = tf_apartment_get_booking_validation_errors( 123, 0, 0, 0, $dates, array() );
tf_apartment_guest_limits_assert(
	in_array( 'Select Adult(s).', $missing_adult_errors, true ),
	'Apartment bookings must continue to require at least one adult.'
);

echo "Apartment booking guest-limit regression checks passed.\n";
