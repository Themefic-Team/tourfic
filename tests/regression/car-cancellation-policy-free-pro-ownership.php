<?php
/**
 * Regression checks for Car Cancellation Policy ownership and behavior parity.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/car-cancellation-policy-free-pro-ownership.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root     = dirname( __DIR__, 2 );
$pro_root = dirname( $root ) . '/tourfic-pro';

function tf_car_cancellation_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

function tf_car_cancellation_read( $file ) {
	tf_car_cancellation_assert( is_readable( $file ), "Missing fixture: {$file}" );

	return file_get_contents( $file );
}

$free_sources = array(
	'car'          => tf_car_cancellation_read( $root . '/inc/functions/functions-car.php' ),
	'booking_form' => tf_car_cancellation_read( $root . '/inc/App/Templates/Components/Shared/Single/Booking_Form.php' ),
	'api_example'  => tf_car_cancellation_read( $root . '/inc/Admin/TF_API_Documentation_Examples.php' ),
	'demo_importer' => tf_car_cancellation_read( $root . '/inc/Admin/TF_Demo_Importer.php' ),
	'js'           => tf_car_cancellation_read( $root . '/sass/app/js/free/car.js' ),
	'compiled_js'  => tf_car_cancellation_read( $root . '/assets/app/js/tourfic-scripts.js' ),
	'minified_js'  => tf_car_cancellation_read( $root . '/assets/app/js/tourfic-scripts.min.js' ),
	'car_css'      => tf_car_cancellation_read( $root . '/sass/app/css/free/car/modules/single/_booking-form.scss' ),
	'legacy_css'   => tf_car_cancellation_read( $root . '/sass/app/css/free/global/design-legacy/_design-legacy.scss' ),
	'compiled_css' => tf_car_cancellation_read( $root . '/assets/app/css/tourfic-carrentals.css' ),
	'minified_css' => tf_car_cancellation_read( $root . '/assets/app/css/tourfic-carrentals.min.css' ),
);

foreach ( array( 'tf_getBestRefundPolicy', 'tf_getRefundPolicy', 'tf_cancellation_policy_meta' ) as $marker ) {
	tf_car_cancellation_assert(
		false === strpos( $free_sources['car'], $marker ),
		"Free Car runtime still contains Pro-owned {$marker}."
	);
}
foreach ( array( 'tf_car_cancellation', 'tf-cancellation-box', 'tf-car-cancellation-popup' ) as $marker ) {
	tf_car_cancellation_assert(
		false === strpos( $free_sources['booking_form'], $marker ),
		"Free booking form still renders Pro-owned {$marker}."
	);
}
foreach ( array( 'response.data.cancellation', '.tf-cancellation-box', '.tf-cancelltion-popup-btn' ) as $marker ) {
	tf_car_cancellation_assert(
		false === strpos( $free_sources['js'], $marker ),
		"Free JavaScript still understands Pro-owned {$marker}."
	);
}
foreach ( array( '.tf-cancellation-box', '.tf-cancelltion-popup-btn' ) as $marker ) {
	tf_car_cancellation_assert(
		false === strpos( $free_sources['car_css'], $marker ),
		"Free Car styles still contain Pro-owned {$marker}."
	);
}
foreach (
	array( '.tf-car-cancellation-popup', '.tf-cancellation-popup-warp', '.tf-cancellation-content-wraper' ) as $marker
) {
	tf_car_cancellation_assert(
		false === strpos( $free_sources['legacy_css'], $marker ),
		"Free legacy styles still contain Pro-owned {$marker}."
	);
}
foreach ( array( 'response.data.cancellation', '.tf-cancellation-box', '.tf-car-cancellation-popup' ) as $marker ) {
	foreach ( array( 'compiled_js', 'minified_js', 'compiled_css', 'minified_css' ) as $asset ) {
		tf_car_cancellation_assert(
			false === strpos( $free_sources[ $asset ], $marker ),
			"Free distributed asset {$asset} still contains Pro-owned {$marker}."
		);
	}
}
tf_car_cancellation_assert(
	false === strpos(
		substr( $free_sources['api_example'], strpos( $free_sources['api_example'], "'price_by'" ) ),
		"'cancellation_section'"
	),
	'Free Car API example still advertises the Pro cancellation schema.'
);
tf_car_cancellation_assert(
	false === strpos( $free_sources['demo_importer'], "\t\t\t'cancellation_section'," ),
	'Free Car demo importer still maps the Pro cancellation section.'
);
tf_car_cancellation_assert(
	false === strpos( $free_sources['demo_importer'], "\t\t\t'calcellation_policy'," ),
	'Free Car demo importer still maps the Pro cancellation policy.'
);

foreach (
	array(
		'tourfic_car_booking_after_fields'          => $free_sources['booking_form'],
		'tourfic:car-booking:price-response'        => $free_sources['js'],
		"'response_data'"                          => $free_sources['car'],
	) as $marker => $source
) {
	tf_car_cancellation_assert(
		false !== strpos( $source, $marker ),
		"Free is missing neutral Car cancellation contract {$marker}."
	);
}

$pro_files = array(
	'class'   => $pro_root . '/inc/classes/TF_Pro_Car_Cancellation_Policy.php',
	'loader'  => $pro_root . '/inc/functions.php',
	'metabox' => $pro_root . '/admin/tf-options/metaboxes/tf-carrental-metabox.php',
	'js'      => $pro_root . '/sass/app/js/pro/tourfic-pro.js',
	'built_js' => $pro_root . '/assets/app/js/tourfic-pro.js',
	'css'     => $pro_root . '/sass/app/css/pro/car/_cancellation.scss',
	'built_css' => $pro_root . '/assets/app/css/tourfic-pro.css',
);
$pro_sources = array();
foreach ( $pro_files as $label => $file ) {
	$pro_sources[ $label ] = tf_car_cancellation_read( $file );
}

foreach (
	array(
		'tourfic_car_booking_after_fields',
		'tourfic_car_booking_adjustments',
		'cancellation_section',
		'calcellation_policy',
		'tf-car-cancellation-popup',
	) as $marker
) {
	tf_car_cancellation_assert(
		false !== strpos( $pro_sources['class'], $marker ),
		"Pro Car Cancellation Policy owner is missing {$marker}."
	);
}
tf_car_cancellation_assert(
	false !== strpos( $pro_sources['loader'], "TF_PRO_INC_PATH . 'classes/TF_Pro_Car_Cancellation_Policy.php'" ),
	'Pro does not load its Car Cancellation Policy owner.'
);
foreach ( array( 'cancellation_section', 'calcellation_policy' ) as $marker ) {
	tf_car_cancellation_assert(
		false !== strpos( $pro_sources['metabox'], $marker ),
		"Pro metabox is missing {$marker}."
	);
}
tf_car_cancellation_assert(
	false !== strpos( $pro_sources['js'], 'tourfic:car-booking:price-response' ),
	'Pro JavaScript is missing the neutral price-response integration.'
);
tf_car_cancellation_assert(
	false !== strpos( $pro_sources['built_js'], 'tourfic:car-booking:price-response' ),
	'Pro distributed JavaScript is stale.'
);
foreach ( array( '.tf-cancellation-box', '.tf-car-cancellation-popup' ) as $marker ) {
	tf_car_cancellation_assert( false !== strpos( $pro_sources['css'], $marker ), "Pro styles are missing {$marker}." );
	tf_car_cancellation_assert(
		false !== strpos( $pro_sources['built_css'], $marker ),
		"Pro distributed styles are missing {$marker}."
	);
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter() {}
}
if ( ! function_exists( 'add_action' ) ) {
	function add_action() {}
}
if ( ! function_exists( 'absint' ) ) {
	function absint( $value ) {
		return abs( (int) $value );
	}
}
if ( ! function_exists( 'get_post_meta' ) ) {
	function get_post_meta() {
		return array();
	}
}
if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $value ) {
		return (string) $value;
	}
}
if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $value ) {
		return (string) $value;
	}
}
if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $value ) {
		return $value;
	}
}
if ( ! function_exists( 'wp_kses_post' ) ) {
	function wp_kses_post( $value ) {
		return $value;
	}
}
if ( ! function_exists( 'wc_price' ) ) {
	function wc_price( $value ) {
		return '$' . number_format( (float) $value, 2, '.', '' );
	}
}
if ( ! function_exists( 'tf_car_create_datetime' ) ) {
	function tf_car_create_datetime( $date, $time, $timezone = false ) {
		return DateTime::createFromFormat(
			'Y/m/d H:i',
			trim( $date . ' ' . $time ),
			$timezone instanceof DateTimeZone ? $timezone : null
		);
	}
}
if ( ! class_exists( 'Tourfic\\Classes\\Helper' ) ) {
	// phpcs:ignore Squiz.PHP.Eval.Discouraged -- Isolated CLI test stub.
	eval( 'namespace Tourfic\\Classes; class Helper { public static function tfopt() { return "UTC"; } }' );
}

require_once $pro_files['class'];

$future = new DateTime( '+15 days', new DateTimeZone( 'UTC' ) );
$date   = $future->format( 'Y/m/d' );
$time   = $future->format( 'H:i' );
$policies = array(
	array(
		'cancellation_type'  => 'free',
		'before_cancel_time' => '2',
		'cancellation-times' => 'day',
		'refund_amount'      => '',
		'refund_amount_type' => 'percent',
	),
	array(
		'cancellation_type'  => 'free',
		'before_cancel_time' => '5',
		'cancellation-times' => 'day',
		'refund_amount'      => '',
		'refund_amount_type' => 'percent',
	),
	array(
		'cancellation_type'  => 'paid',
		'before_cancel_time' => '1',
		'cancellation-times' => 'day',
		'refund_amount'      => '25',
		'refund_amount_type' => 'percent',
	),
);

$best = TF_Pro_Car_Cancellation_Policy::get_best_policy( $policies, $date, $time );
tf_car_cancellation_assert( '5' === $best['before_cancel_time'], 'Best free cancellation policy selection changed.' );

$timeline = TF_Pro_Car_Cancellation_Policy::get_timeline_policies( $policies, $date, $time );
tf_car_cancellation_assert(
	2 === count( $timeline ),
	'Car cancellation timeline no longer preserves its two-policy limit.'
);
tf_car_cancellation_assert( '5' === $timeline[0]['before_cancel_time'], 'Car cancellation timeline ordering changed.' );

$owner       = new TF_Pro_Car_Cancellation_Policy();
$adjustments = $owner->booking_adjustments(
	array( array( 'amount' => 10 ) ),
	array(
		'source'      => 'price',
		'post_id'     => 10,
		'meta'        => array( 'calcellation_policy' => $policies ),
		'pickup_date' => $date,
		'pickup_time' => $time,
	)
);
tf_car_cancellation_assert(
	2 === count( $adjustments ),
	'Pro must append cancellation output without replacing another booking adjustment.'
);
tf_car_cancellation_assert(
	0 === $adjustments[1]['amount'],
	'Cancellation display must not alter the Car booking total.'
);
tf_car_cancellation_assert(
	false !== strpos( $adjustments[1]['response_data']['cancellation'], 'Free cancellation' ),
	'Car price response lost the selected cancellation summary.'
);
tf_car_cancellation_assert(
	false !== strpos( $adjustments[1]['response_data']['cancellation'], 'See Cancellation Policy' ),
	'Car price response lost the cancellation popup link.'
);

$payload = $owner->api_example_payload( array( 'price_by' => 'day' ) );
tf_car_cancellation_assert(
	array_key_exists( 'cancellation_section', $payload ),
	'Pro API example does not advertise its cancellation section.'
);
tf_car_cancellation_assert(
	array_key_exists( 'calcellation_policy', $payload ),
	'Pro API example does not advertise its cancellation policy.'
);

echo "Car Cancellation Policy Free/Pro ownership and behavior regression checks passed.\n";
