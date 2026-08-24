<?php
/**
 * Regression checks for Car Extras ownership and pricing parity.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/car-extras-free-pro-ownership.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root     = dirname( __DIR__, 2 );
$pro_root = dirname( $root ) . '/tourfic-pro';

function tf_car_extras_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

function tf_car_extras_read( $file ) {
	tf_car_extras_assert( is_readable( $file ), "Missing fixture: {$file}" );

	return file_get_contents( $file );
}

$free_paths = array(
	$root . '/inc',
	$root . '/templates',
	$root . '/sass/app/js/free/car.js',
	$root . '/sass/app/css/free/car',
	$root . '/assets/app/js/tourfic-scripts.js',
	$root . '/assets/app/js/tourfic-scripts.min.js',
	$root . '/assets/app/css/tourfic-carrentals.css',
	$root . '/assets/app/css/tourfic-carrentals.min.css',
);
$free_markers = array(
	'car_extra_sec_title',
	'tf_car_extra_meta',
	'tf_extra_add_to_booking',
	'set_extra_price',
	'selected_extra[]',
	'selected_qty[]',
	'tf-add-extra-section',
	'tf-extra-added-info',
);

foreach ( $free_paths as $path ) {
	$files = is_dir( $path )
		? new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS ) )
		: array( new SplFileInfo( $path ) );

	foreach ( $files as $file ) {
		if ( ! $file->isFile() || ! preg_match( '/\.(?:php|js|css|scss)$/', $file->getFilename() ) ) {
			continue;
		}

		$source = tf_car_extras_read( $file->getPathname() );
		foreach ( $free_markers as $marker ) {
			tf_car_extras_assert(
				false === strpos( $source, $marker ),
				$file->getPathname() . " still contains Pro-owned Car Extras marker {$marker}."
			);
		}
	}
}

$helper_source  = tf_car_extras_read( $root . '/inc/Classes/Helper.php' );
$booking_source = tf_car_extras_read( $root . '/inc/App/Templates/Components/Shared/Single/Booking_Form.php' );
$car_source     = tf_car_extras_read( $root . '/inc/functions/functions-car.php' );
$wc_source      = tf_car_extras_read( $root . '/inc/functions/woocommerce/wc-car.php' );
$free_js        = tf_car_extras_read( $root . '/sass/app/js/free/car.js' );

foreach (
	array(
		'tourfic_car_booking_adjustments'              => $helper_source,
		'tourfic_car_booking_extensions'               => $booking_source,
		'tourfic_car_render_booking_extension'         => $booking_source,
		'tourfic_car_cart_item_data'                   => $wc_source,
		'tourfic_car_checkout_create_order_line_item'  => $wc_source,
		'tourfic_car_order_item_details'               => $wc_source,
		'tourfic_car_integration_order_item'           => $wc_source,
		'tourfic:car-booking:request-data'             => $free_js,
		'tourfic:car-booking:price-request-data'       => $free_js,
	) as $marker => $source
) {
	tf_car_extras_assert( false !== strpos( $source, $marker ), "Free is missing neutral Car booking contract {$marker}." );
}
tf_car_extras_assert( false !== strpos( $car_source, "'source'       => 'price'" ), 'Free price calculation does not aggregate Car extensions.' );

$pro_files = array(
	'class'   => $pro_root . '/inc/classes/TF_Pro_Car_Extras.php',
	'loader'  => $pro_root . '/inc/functions.php',
	'metabox' => $pro_root . '/admin/tf-options/metaboxes/tf-carrental-metabox.php',
	'js'      => $pro_root . '/sass/app/js/pro/tourfic-pro.js',
	'css'     => $pro_root . '/sass/app/css/pro/car/_extras.scss',
);
$pro_sources = array();
foreach ( $pro_files as $label => $file ) {
	$pro_sources[ $label ] = tf_car_extras_read( $file );
}

foreach ( array( 'car_extra_sec_title', "'extras'", 'tf_extra_add_to_booking', 'Extra quantity cannot be negative.', 'tourfic_car_booking_adjustments', "get_meta( 'Extra'" ) as $marker ) {
	tf_car_extras_assert( false !== strpos( $pro_sources['class'], $marker ), "Pro Car Extras class is missing {$marker}." );
}
tf_car_extras_assert(
	false !== strpos( $pro_sources['loader'], "TF_PRO_INC_PATH . 'classes/TF_Pro_Car_Extras.php'" ),
	'Pro does not load its Car Extras owner.'
);
foreach ( array( 'car_extra_sec_title', "'extras'" ) as $marker ) {
	tf_car_extras_assert( false !== strpos( $pro_sources['metabox'], $marker ), "Pro metabox is missing {$marker}." );
}
foreach ( array( 'selected_extra[]', 'selected_qty[]', 'tourfic:car-booking:request-data' ) as $marker ) {
	tf_car_extras_assert( false !== strpos( $pro_sources['js'], $marker ), "Pro JavaScript is missing {$marker}." );
}
foreach ( array( '.tf-add-extra-section', '.tf-extra-added-info' ) as $marker ) {
	tf_car_extras_assert( false !== strpos( $pro_sources['css'], $marker ), "Pro styles are missing {$marker}." );
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter() {}
}
if ( ! function_exists( 'add_action' ) ) {
	function add_action() {}
}
if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $value ) {
		return trim( strip_tags( (string) $value ) );
	}
}
if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $value ) {
		return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) );
	}
}
if ( ! function_exists( 'absint' ) ) {
	function absint( $value ) {
		return abs( (int) $value );
	}
}
if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $value ) {
		return $value;
	}
}
if ( ! function_exists( 'wc_price' ) ) {
	function wc_price( $value ) {
		return '$' . number_format( (float) $value, 2, '.', '' );
	}
}
if ( ! function_exists( 'tf_car_create_datetime' ) ) {
	function tf_car_create_datetime( $date, $time ) {
		return DateTime::createFromFormat( 'Y/m/d H:i', trim( $date . ' ' . $time ) );
	}
}

require_once $pro_files['class'];

$owner      = new TF_Pro_Car_Extras();
$saved_meta = array(
	'car_extra_sec_title' => 'Add extras',
	'extras'               => array(
		array( 'title' => 'GPS', 'content' => 'Navigation', 'price' => 10, 'price_type' => 'fixed' ),
		array( 'title' => 'Child Seat', 'content' => 'Safety seat', 'price' => 5, 'price_type' => 'day' ),
		array( 'title' => 'Free Water', 'content' => 'Complimentary', 'price' => 0, 'price_type' => 'fixed' ),
	),
);
$extensions = $owner->booking_extension( array( array( 'id' => 'existing-extension' ) ), 10, $saved_meta );
tf_car_extras_assert( 2 === count( $extensions ), 'Pro must append Car Extras without replacing another Car booking extension.' );
tf_car_extras_assert( 'car-extras' === $extensions[1]['id'], 'Pro did not identify its appended Car Extras extension.' );

$selection = TF_Pro_Car_Extras::sanitize_selection( array( '0', '1', '1' ), array( '2', '-4', '3' ) );
tf_car_extras_assert( array( '0', '1' ) === $selection['extras'], 'Car Extras selection did not preserve valid aligned IDs.' );
tf_car_extras_assert( array( 2, 3 ) === $selection['quantities'], 'Car Extras quantities no longer preserve positive integers.' );
tf_car_extras_assert( array( 'Extra quantity cannot be negative.' ) === $selection['errors'], 'Negative Car Extras quantities are not rejected.' );

$calculated = TF_Pro_Car_Extras::calculate(
	$saved_meta['extras'],
	$selection['extras'],
	$selection['quantities'],
	array(
		'pickup_date'  => '2026/08/23',
		'dropoff_date' => '2026/08/24',
		'pickup_time'  => '10:00',
		'dropoff_time' => '12:00',
	)
);
tf_car_extras_assert( 50.0 === $calculated['total'], 'Fixed and per-day Car Extras pricing changed.' );
tf_car_extras_assert( false !== strpos( $calculated['title'], 'GPS(fixed) × 2 = $20.00' ), 'Fixed Car Extra persistence text changed.' );
tf_car_extras_assert( false !== strpos( $calculated['title'], 'Child Seat(day) × 3 = $30.00' ), 'Per-day Car Extra persistence text changed.' );

$zero_price = TF_Pro_Car_Extras::calculate(
	$saved_meta['extras'],
	array( '2' ),
	array( 1 ),
	array()
);
tf_car_extras_assert( 0.0 === (float) $zero_price['total'], 'Zero-priced Car Extras must remain free.' );
tf_car_extras_assert( 'Free Water(fixed) × 1 = $0.00' === $zero_price['title'], 'Zero-priced Car Extra persistence text changed.' );
tf_car_extras_assert( 0.0 === (float) $zero_price['items'][0]['unit_price'], 'Zero-priced Apply output must preserve its blank amount state.' );

echo "Car Extras Free/Pro ownership and pricing regression checks passed.\n";
