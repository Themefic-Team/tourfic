<?php
/**
 * Regression checks for Hotel Extras ownership and pricing parity.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/hotel-extras-free-pro-ownership.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root     = dirname( __DIR__, 2 );
$pro_root = dirname( $root ) . '/tourfic-pro';

function tf_hotel_extras_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

function tf_hotel_extras_read( $file ) {
	tf_hotel_extras_assert( is_readable( $file ), "Missing fixture: {$file}" );

	return file_get_contents( $file );
}

$free_files = array(
	$root . '/inc',
	$root . '/sass',
	$root . '/assets/app/js/tourfic-scripts.js',
	$root . '/assets/app/js/tourfic-scripts.min.js',
	$root . '/assets/app/css/tourfic-style.css',
	$root . '/assets/app/css/tourfic-style.min.css',
);
$pro_files = array(
	'class'    => $pro_root . '/inc/classes/TF_Pro_Hotel_Extras.php',
	'loader'   => $pro_root . '/inc/functions.php',
	'metabox'  => $pro_root . '/admin/tf-options/metaboxes/tf-hotel-metabox.php',
	'settings' => $pro_root . '/admin/tf-options/options/tf-settings.php',
	'js'       => $pro_root . '/sass/app/js/pro/tourfic-pro.js',
	'css'      => $pro_root . '/sass/app/css/pro/global/_common.scss',
);

$free_markers = array(
	'hotel_extra_option',
	'hotel-extra',
	'hotel_extra',
	'extra_service',
	'hotel_extra_quantity',
	'tf-single-hotel-service',
	'tf_hotel_extra_meta',
	'tf_hotel_render_extras',
	'tf_hotel_extras_title_price',
	'tf_sanitize_extra_quantities',
);

foreach ( $free_files as $path ) {
	$files = is_dir( $path )
		? new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS ) )
		: array( new SplFileInfo( $path ) );

	foreach ( $files as $file ) {
		if ( ! $file->isFile() || ! preg_match( '/\.(?:php|js|css|scss)$/', $file->getFilename() ) ) {
			continue;
		}

		$source = tf_hotel_extras_read( $file->getPathname() );
		foreach ( $free_markers as $marker ) {
			tf_hotel_extras_assert(
				false === strpos( $source, $marker ),
				$file->getPathname() . " still contains Pro-owned Hotel Extras marker {$marker}."
			);
		}
	}
}

$helper_source = tf_hotel_extras_read( $root . '/inc/Classes/Helper.php' );
$hotel_source  = tf_hotel_extras_read( $root . '/inc/Classes/Hotel/Hotel.php' );
$wc_source     = tf_hotel_extras_read( $root . '/inc/functions/woocommerce/wc-hotel.php' );
$free_js       = tf_hotel_extras_read( $root . '/sass/app/js/free/hotel.js' );

foreach (
	array(
		'tourfic_hotel_booking_adjustments'          => $helper_source,
		'tourfic_hotel_booking_extensions'           => $hotel_source,
		'tourfic_hotel_render_booking_extension'     => $hotel_source,
		'tourfic_hotel_cart_item_data'               => $wc_source,
		'tourfic_hotel_checkout_create_order_line_item' => $wc_source,
		'tourfic_hotel_order_item_details'           => $wc_source,
		'tourfic_hotel_integration_order_item'       => $wc_source,
		'tourfic:hotel-booking:request-data'         => $free_js,
		'tourfic:hotel-booking:form-data'            => $free_js,
		'tourfic:hotel-booking:refresh'              => $free_js,
	) as $marker => $source
) {
	tf_hotel_extras_assert( false !== strpos( $source, $marker ), "Free is missing neutral Hotel booking contract {$marker}." );
}

$pro_sources = array();
foreach ( $pro_files as $label => $file ) {
	$pro_sources[ $label ] = tf_hotel_extras_read( $file );
}

foreach (
	array(
		'hotel_extra_option',
		'hotel-extra',
		'extra_service',
		'hotel_extra_quantity',
		'Hotel Extra Service',
		'Hotel Extra Service Fee',
		'tourfic_hotel_booking_extensions',
		'tourfic_hotel_booking_adjustments',
		'tourfic_hotel_booking_detail_rows',
	) as $marker
) {
	tf_hotel_extras_assert( false !== strpos( $pro_sources['class'], $marker ), "Pro Hotel Extras class is missing {$marker}." );
}

tf_hotel_extras_assert(
	false !== strpos( $pro_sources['loader'], "TF_PRO_INC_PATH . 'classes/TF_Pro_Hotel_Extras.php'" ),
	'Pro does not load its Hotel Extras owner.'
);
tf_hotel_extras_assert(
	false === strpos( $pro_sources['loader'], 'tf_hotel_extra_meta' )
		&& false === strpos( $pro_sources['loader'], 'tf_hotel_render_extras' ),
	'Pro still carries the obsolete partial Hotel Extras bridge.'
);
foreach ( array( 'hotel_extra_option', 'hotel-extra' ) as $marker ) {
	tf_hotel_extras_assert( false !== strpos( $pro_sources['metabox'], $marker ), "Pro metabox is missing {$marker}." );
}
foreach ( array( 'hotel_extra_popup_title', 'hotel_extra_popup_subtile' ) as $marker ) {
	tf_hotel_extras_assert( false !== strpos( $pro_sources['settings'], $marker ), "Pro settings are missing {$marker}." );
}
foreach ( array( 'extra_service', 'hotel_extra_quantity', 'tourfic:hotel-booking:request-data', 'tourfic:hotel-booking:form-data' ) as $marker ) {
	tf_hotel_extras_assert( false !== strpos( $pro_sources['js'], $marker ), "Pro JavaScript is missing {$marker}." );
}
tf_hotel_extras_assert( false !== strpos( $pro_sources['css'], '.tf-single-hotel-service' ), 'Pro styles are missing Hotel Extras UI ownership.' );

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
if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $value ) {
		return $value;
	}
}
if ( ! function_exists( 'wc_price' ) ) {
	function wc_price( $value ) {
		return '$' . number_format( (float) $value, 2, '.', '' );
	}
}
if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $value ) {
		return strip_tags( (string) $value );
	}
}
if ( ! function_exists( '__' ) ) {
	function __( $value ) {
		return $value;
	}
}

require_once $pro_files['class'];

$owner      = new TF_Pro_Hotel_Extras();
$saved_meta = array(
	'hotel_extra_option' => '1',
	'hotel-extra'        => array(
		array( 'title' => 'Breakfast', 'price' => 10, 'price_type' => 'fixed' ),
		array( 'title' => 'Guide', 'price' => 5, 'price_type' => 'person' ),
		array( 'title' => 'Bike', 'price' => 3, 'price_type' => 'quantity' ),
		array( 'title' => 'Welcome Drink', 'price' => 0, 'price_type' => 'fixed' ),
	),
);
$extensions = $owner->booking_extension( array( array( 'id' => 'existing-extension' ) ), 10, $saved_meta );
tf_hotel_extras_assert( 2 === count( $extensions ), 'Pro must append Hotel Extras without replacing another Hotel booking extension.' );
tf_hotel_extras_assert( 'hotel-extras' === $extensions[1]['id'], 'Pro did not identify its appended Hotel Extras extension.' );

$selection = TF_Pro_Hotel_Extras::sanitize_selection( array( '0', '1', '2', array( 'invalid' ) ), array( '1', '-4', '4', '99' ) );
tf_hotel_extras_assert( array( '0', '1', '2' ) === $selection['extras'], 'Hotel Extras selection does not preserve valid aligned IDs.' );
tf_hotel_extras_assert( array( 1, 4, 4 ) === $selection['quantities'], 'Hotel Extras quantities are not normalized to positive integers.' );

$calculated = TF_Pro_Hotel_Extras::calculate(
	$saved_meta['hotel-extra'],
	array_merge( $selection['extras'], array( '3' ) ),
	array_merge( $selection['quantities'], array( 1 ) ),
	2
);

tf_hotel_extras_assert( 32.0 === $calculated['total'], 'Fixed, person, and quantity Hotel Extras pricing no longer matches the legacy contract.' );
tf_hotel_extras_assert( array( '0', '1', '2', '3' ) === $calculated['selected'], 'Calculated Hotel Extras lost valid saved IDs.' );
tf_hotel_extras_assert( false !== strpos( $calculated['title'], 'Bike (Quantity ( 4 × $3.00 ))' ), 'Quantity title persistence changed.' );
tf_hotel_extras_assert( false !== strpos( $calculated['title'], 'Welcome Drink' ), 'Zero-priced configured Hotel Extras no longer preserve their legacy title.' );

$_POST['extra_service']        = array( '0', '1', '2' );
$_POST['hotel_extra_quantity'] = array( '1', '1', '4' );
$adjustments = $owner->booking_adjustments(
	array(),
	array(
		'source'  => 'checkout',
		'post_id' => 10,
		'meta'    => $saved_meta,
		'adult'   => 2,
		'child'   => 1,
	)
);
unset( $_POST['extra_service'], $_POST['hotel_extra_quantity'] );

tf_hotel_extras_assert( 1 === count( $adjustments ), 'Pro did not provide the Hotel Extras booking adjustment.' );
tf_hotel_extras_assert( 32.0 === $adjustments[0]['amount'], 'Hotel Extras adjustment total changed during request processing.' );
tf_hotel_extras_assert( 32.0 === $adjustments[0]['cart_data']['hotel_extra_price'], 'Legacy Hotel Extras cart price key changed.' );
tf_hotel_extras_assert( isset( $adjustments[0]['order_details']['hotel_extra_fee'] ), 'Legacy Hotel Extras order detail key is missing.' );

echo "Hotel Extras Free/Pro ownership and pricing regression checks passed.\n";
