<?php
/**
 * Regression checks for the Tour payment hook contract.
 *
 * Run from the Tourfic Free plugin root:
 * TOURFIC_PRO_ROOT=/path/to/tourfic-pro php tests/regression/tour-payment-hook-compatibility.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}

$root     = dirname( __DIR__, 2 );
$pro_root = getenv( 'TOURFIC_PRO_ROOT' ) ?: dirname( $root ) . '/tourfic-pro';
$actions  = array();
$filters  = array();

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	global $actions;
	$actions[ $hook ][] = array( $callback, $priority, $accepted_args );
}

function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	global $filters;
	$filters[ $hook ][] = array( $callback, $priority, $accepted_args );
}

function tf_tour_payment_hook_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$booking_modes_file = $pro_root . '/inc/classes/TF_Pro_Booking_Modes.php';

if ( is_readable( $booking_modes_file ) ) {
	require_once $booking_modes_file;
}
require_once $pro_root . '/inc/classes/TF_Pro_Deposit.php';

$tour_source   = file_get_contents( $root . '/inc/Classes/Tour/Tour.php' );

tf_tour_payment_hook_assert(
	false !== strpos( $tour_source, "apply_filters( 'tourfic_tour_booking_type', '1', \$post_id, \$meta )" )
		&& false !== strpos( $tour_source, "do_action( 'tourfic_tour_booking_payment_options', \$post_id, \$meta, \$booking_type )" ),
	'Free must publish a normal-booking default and pass three payment-hook arguments.'
);

$payment_registrations = $actions['tourfic_tour_booking_payment_options'] ?? array();
tf_tour_payment_hook_assert(
	1 === count( $payment_registrations ) && 3 === $payment_registrations[0][2],
	'Pro Deposit must register for all three Tour payment-hook arguments.'
);
$deposit = $payment_registrations[0][0][0];

$payment_method = new ReflectionMethod( TF_Pro_Deposit::class, 'render_tour_payment_options' );
tf_tour_payment_hook_assert(
	3 === $payment_method->getNumberOfParameters(),
	'Pro Deposit must expose the complete three-argument Tour payment callback.'
);

if ( is_readable( $booking_modes_file ) ) {
	tf_tour_payment_hook_assert(
		2 === $payment_method->getNumberOfRequiredParameters(),
		'Current Pro Deposit must remain compatible with a two-argument Free call during staggered updates.'
	);

	$booking_type_registrations = $filters['tourfic_tour_booking_type'] ?? array();
	tf_tour_payment_hook_assert(
		1 === count( $booking_type_registrations ) && 3 === $booking_type_registrations[0][2],
		'Pro Booking Modes must own restoration of the saved Tour booking type.'
	);
	$booking_modes = $booking_type_registrations[0][0][0];
	tf_tour_payment_hook_assert(
		'1' === $booking_modes->tour_booking_type( '1', 123, array() )
			&& 'custom' === $booking_modes->tour_booking_type( 'custom', 123, array() )
			&& '3' === $booking_modes->tour_booking_type( '1', 123, array( 'booking-by' => '3' ) ),
		'Pro must preserve the neutral default and restore an existing paid booking type.'
	);

	ob_start();
	$deposit->render_tour_payment_options( 123, array( 'booking-by' => '3' ) );
	$two_argument_output = ob_get_clean();
	tf_tour_payment_hook_assert(
		'' === $two_argument_output,
		'Current Pro Deposit must safely accept a two-argument call and suppress Deposit for without-payment booking.'
	);
} else {
	$pro_functions = file_get_contents( $pro_root . '/inc/functions.php' );
	tf_tour_payment_hook_assert(
		3 === $payment_method->getNumberOfRequiredParameters()
			&& false !== strpos( $pro_functions, "add_filter( 'tourfic_tour_booking_type', 'tf_pro_tour_booking_type', 10, 3 )" ),
		'Previous Pro requires three payment arguments and must expose the neutral booking-type filter used by Free.'
	);
}

echo "Tour payment hook compatibility regression checks passed.\n";
