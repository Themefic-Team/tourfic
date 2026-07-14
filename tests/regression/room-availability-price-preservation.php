<?php
/**
 * Regression checks for room availability price preservation.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/room-availability-price-preservation.php
 */

$root = dirname( __DIR__, 2 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', $root . '/' );
}

require_once $root . '/inc/Classes/Room/Availability.php';

use Tourfic\Classes\Room\Availability;

function tf_room_price_assert_same( $expected, $actual, $message ) {
	if ( $expected !== $actual ) {
		fwrite(
			STDERR,
			"FAIL: {$message}\nExpected: " . var_export( $expected, true ) . "\nActual: " . var_export( $actual, true ) . "\n"
		);
		exit( 1 );
	}
}

$existing_per_room = array(
	'price'  => '175',
	'status' => 'available',
);
$unavailable_rule  = Availability::merge_rule_prices(
	array(
		'price'  => '',
		'status' => 'unavailable',
	),
	$existing_per_room
);
tf_room_price_assert_same( '175', $unavailable_rule['price'], 'Blank per-room price must preserve the existing value.' );
tf_room_price_assert_same( 'unavailable', $unavailable_rule['status'], 'Price merging must not replace the submitted status.' );

$available_rule = Availability::merge_rule_prices(
	array(
		'price'  => '',
		'status' => 'available',
	),
	$unavailable_rule
);
tf_room_price_assert_same( '175', $available_rule['price'], 'The original price must survive unavailable-to-available.' );

$explicit_zero = Availability::merge_rule_prices( array( 'price' => '0' ), $existing_per_room );
tf_room_price_assert_same( '0', $explicit_zero['price'], 'String zero must remain an explicit price update.' );

$numeric_zero = Availability::merge_rule_prices( array( 'price' => 0 ), $existing_per_room );
tf_room_price_assert_same( 0, $numeric_zero['price'], 'Numeric zero must remain an explicit price update.' );

$replacement_rule = Availability::merge_rule_prices( array( 'price' => '225' ), $existing_per_room );
tf_room_price_assert_same( '225', $replacement_rule['price'], 'A non-empty replacement price must override the existing value.' );

$existing_per_person = array(
	'adult_price' => '140',
	'child_price' => '65',
);
$per_person_rule     = Availability::merge_rule_prices(
	array(
		'adult_price' => '',
		'child_price' => '',
	),
	$existing_per_person
);
tf_room_price_assert_same( '140', $per_person_rule['adult_price'], 'Blank adult price must preserve the existing value.' );
tf_room_price_assert_same( '65', $per_person_rule['child_price'], 'Blank child price must preserve the existing value.' );

$existing_options = array(
	'tf_option_room_price_0'  => '310',
	'tf_option_adult_price_1' => '125',
	'tf_option_child_price_1' => '55',
);
$option_rule      = Availability::merge_rule_prices(
	array(
		'tf_option_room_price_0'  => '',
		'tf_option_adult_price_1' => '0',
	),
	$existing_options
);
tf_room_price_assert_same( '310', $option_rule['tf_option_room_price_0'], 'Blank room-option price must preserve the existing value.' );
tf_room_price_assert_same( '0', $option_rule['tf_option_adult_price_1'], 'Explicit room-option zero must override the existing value.' );
tf_room_price_assert_same( '55', $option_rule['tf_option_child_price_1'], 'An omitted room-option price must preserve the existing value.' );

$new_rule = Availability::merge_rule_prices( array( 'price' => '' ) );
tf_room_price_assert_same( '', $new_rule['price'], 'A new date without an existing price must retain its blank value.' );

$distinct_dates = array(
	'2026/08/01' => array( 'price' => '180' ),
	'2026/08/02' => array( 'price' => '240' ),
);
foreach ( $distinct_dates as $date => $existing_rule ) {
	$distinct_dates[ $date ] = Availability::merge_rule_prices( array( 'price' => '', 'status' => 'unavailable' ), $existing_rule );
}
tf_room_price_assert_same( '180', $distinct_dates['2026/08/01']['price'], 'A range update must preserve the first date price.' );
tf_room_price_assert_same( '240', $distinct_dates['2026/08/02']['price'], 'A range update must preserve each date-specific price.' );

$free_handler = file_get_contents( $root . '/inc/Admin/TF_Options/TF_Options.php' );
$pro_handler  = file_get_contents( dirname( $root ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Hotel_Rest_API.php' );
tf_room_price_assert_same( true, false !== strpos( $free_handler, 'Availability::merge_rule_prices' ), 'Free calendar writer must use the shared price merger.' );
tf_room_price_assert_same( true, false !== strpos( $pro_handler, 'Availability::merge_rule_prices' ), 'Pro calendar writer must use the shared price merger.' );

$calendar_source = file_get_contents( $root . '/sass/admin/js/free/tf-options.js' );
$calendar_asset  = file_get_contents( $root . '/assets/admin/js/tourfic-admin-scripts.js' );
$calendar_min    = file_get_contents( $root . '/assets/admin/js/tourfic-admin-scripts.min.js' );
tf_room_price_assert_same( 1, substr_count( $calendar_source, 'new roomCal(' ), 'The source must create each room calendar only during initialization.' );
tf_room_price_assert_same( true, false !== strpos( $calendar_source, ".data('tfRoomCalendar', room)" ), 'The source must retain the initialized calendar instance.' );
tf_room_price_assert_same( 2, substr_count( $calendar_source, 'refetchRoomCalendar(container);' ), 'Save and reset must refetch the retained calendar instance.' );
tf_room_price_assert_same( true, false !== strpos( $calendar_asset, ".data('tfRoomCalendar', room)" ), 'The normal admin asset must contain the retained calendar instance.' );
tf_room_price_assert_same( true, false !== strpos( $calendar_min, 'tfRoomCalendar' ), 'The minified admin asset must contain the retained calendar instance.' );

echo "Room availability price-preservation regression checks passed.\n";
