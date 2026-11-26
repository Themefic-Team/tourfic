<?php
/**
 * Check booking shipping against installed WooCommerce product/cart implementations.
 *
 * Run: php tests/regression/checkout-shipping-compatibility.php [Product_Extend.php] [woocommerce-root]
 * Persistence, customer destination and shipping-method configuration are memory fixtures.
 * WordPress, WooCommerce, database connections and customer sessions are never bootstrapped.
 */

if ( 'cli' !== PHP_SAPI || defined( 'ABSPATH' ) ) {
	exit( 1 );
}

$tourfic_root = dirname( __DIR__, 2 );
$tourfic_source = $argv[1] ?? $tourfic_root . '/inc/Classes/Woocommerce/Product_Extend.php';
$tourfic_wc_root = rtrim( $argv[2] ?? dirname( $tourfic_root ) . '/woocommerce', '/' );
$tourfic_wp_root = dirname( $tourfic_root, 3 );
define( 'ABSPATH', $tourfic_wp_root . '/' );
define( 'WP_PLUGIN_DIR', dirname( $tourfic_root ) );
define( 'WC_ABSPATH', $tourfic_wc_root . '/' );
require ABSPATH . 'wp-includes/plugin.php';

spl_autoload_register( function( $class ) {
	$prefix = 'Automattic\\WooCommerce\\';
	if ( 0 === strpos( $class, $prefix ) ) {
		$file = WC_ABSPATH . 'src/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
		if ( is_readable( $file ) ) {
			require_once $file;
		}
		return;
	}
	if ( 0 !== strpos( $class, 'WC_' ) ) {
		return;
	}
	$name = strtolower( str_replace( '_', '-', $class ) );
	foreach ( array( 'includes/class-', 'includes/interfaces/class-', 'includes/data-stores/class-', 'includes/abstracts/abstract-' ) as $path ) {
		$file = WC_ABSPATH . $path . $name . '.php';
		if ( is_readable( $file ) ) {
			require_once $file;
			return;
		}
	}
} );

$tourfic_shipping_posts = array();
$tourfic_shipping_meta = array();
$tourfic_shipping_options = array();
$tourfic_shipping_method_count = 1;
$tourfic_shipping_checks = 0;
$tourfic_shipping_failures = array();

function get_post( $id ) { return $GLOBALS['tourfic_shipping_posts'][ $id ] ?? null; }
function get_post_type( $id ) { return get_post( $id )->post_type ?? false; }
function get_post_meta( $id, $key = '', $single = false ) {
	$meta = $GLOBALS['tourfic_shipping_meta'][ $id ] ?? array();
	return '' === $key ? $meta : ( $single ? ( $meta[ $key ][0] ?? '' ) : ( $meta[ $key ] ?? array() ) );
}
function get_the_terms( $id, $taxonomy ) { return false; }
function get_option( $key, $default = false ) { return $GLOBALS['tourfic_shipping_options'][ $key ] ?? $default; }
function get_current_user_id() { return 0; }
function absint( $value ) { return abs( (int) $value ); }
function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); }
function maybe_unserialize( $value ) { return $value; }
function is_wp_error( $value ) { return false; }
function __( $text, $domain = '' ) { return $text; }
function _x( $text, $context, $domain = '' ) { return $text; }
function esc_html__( $text, $domain = '' ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html_e( $text, $domain = '' ) { echo esc_html__( $text, $domain ); }
function wp_list_pluck( $values, $key ) { return array_column( $values, $key ); }
function wp_parse_id_list( $values ) { return array_values( array_unique( array_map( 'absint', (array) $values ) ) ); }
function checked( $value, $expected = true ) { if ( $value == $expected ) { echo 'checked="checked"'; } }
function wc_get_shipping_method_count( $include_legacy = false ) { return $GLOBALS['tourfic_shipping_method_count']; }
function wc_get_product_visibility_options() { return array_fill_keys( array( 'visible', 'catalog', 'search', 'hidden' ), '' ); }
function wc_get_container() {
	return new class {
		public function get( $class ) {
			return new class {
				public function feature_is_enabled() { return false; }
			};
		}
	};
}
function woocommerce_form_field( $key, $field, $value ) { echo '<input name="' . $key . '">'; }
function WC() { return $GLOBALS['tourfic_shipping_woocommerce']; }

require WC_ABSPATH . 'includes/wc-formatting-functions.php';
require WC_ABSPATH . 'includes/wc-conditional-functions.php';
require WC_ABSPATH . 'includes/wc-order-functions.php';
require $tourfic_source;
require $tourfic_root . '/inc/Traits/Singleton.php';
require $tourfic_root . '/inc/Classes/Woocommerce/Woocommerce.php';
$tourfic_wc_integration = ( new ReflectionClass( '\Tourfic\Classes\Woocommerce\Woocommerce' ) )->newInstanceWithoutConstructor();
add_filter( 'woocommerce_data_stores', array( $tourfic_wc_integration, 'tf_woocommerce_data_stores' ) );

class Tourfic_Shipping_Fixture_Customer {
	public function get_is_vat_exempt() { return false; }
	public function __call( $method, $args ) { return 'get_shipping_country' === $method ? 'BD' : ''; }
}
class Tourfic_Shipping_Fixture_Service {
	public $calls = 0;
	public function calculate_shipping( $packages ) { ++$this->calls; return array(); }
}
class Tourfic_Shipping_Fixture_Woocommerce {
	public $cart;
	public $customer;
	public $shipping;
	public function shipping() { return $this->shipping; }
}
$tourfic_shipping_woocommerce = new Tourfic_Shipping_Fixture_Woocommerce();
$tourfic_shipping_woocommerce->customer = new Tourfic_Shipping_Fixture_Customer();
$tourfic_shipping_woocommerce->shipping = new Tourfic_Shipping_Fixture_Service();
do_action( 'wp_loaded' );
do_action( 'woocommerce_load_cart_from_session' );

function tourfic_shipping_expect( $condition, $message ) {
	++$GLOBALS['tourfic_shipping_checks'];
	if ( ! $condition ) {
		$GLOBALS['tourfic_shipping_failures'][] = $message;
	}
}

function tourfic_shipping_product( $type, $virtual = null ) {
	$id = 100 + count( $GLOBALS['tourfic_shipping_posts'] );
	$GLOBALS['tourfic_shipping_posts'][ $id ] = (object) array(
		'ID' => $id, 'post_type' => $type, 'post_title' => 'Shipping fixture ' . $id,
		'post_name' => 'shipping-fixture-' . $id, 'post_status' => 'publish',
		'post_date_gmt' => '', 'post_modified_gmt' => '', 'post_content' => '',
		'post_excerpt' => '', 'post_parent' => 0, 'menu_order' => 0, 'comment_status' => 'closed',
	);
	$GLOBALS['tourfic_shipping_meta'][ $id ] = null === $virtual ? array() : array( '_virtual' => array( $virtual ) );
	$original_meta = $GLOBALS['tourfic_shipping_meta'][ $id ];
	$product = new WC_Product_Simple( $id );
	tourfic_shipping_expect( $original_meta === $GLOBALS['tourfic_shipping_meta'][ $id ], $type . ': reading shipping state does not alter stored metadata' );
	return $product;
}

function tourfic_shipping_cart( $products, $context ) {
	$cart = ( new ReflectionClass( 'WC_Cart' ) )->newInstanceWithoutConstructor();
	$cart->cart_context = $context;
	$items = array();
	foreach ( $products as $key => $product ) {
		$items[ $key ] = array( 'data' => $product, 'product_id' => $product->get_id(), 'quantity' => 1, 'line_total' => 25 );
	}
	$cart->set_cart_contents( $items );
	WC()->cart = $cart;
	return $cart;
}

function tourfic_shipping_form() {
	$checkout = new class {
		public function get_checkout_fields( $group ) { return 'shipping' === $group ? array( 'shipping_address_1' => array() ) : array(); }
		public function get_value( $key ) { return ''; }
	};
	ob_start();
	include WC_ABSPATH . 'templates/checkout/form-shipping.php';
	return ob_get_clean();
}

$tourfic_bookings = array();
foreach ( array( 'tf_hotel', 'tf_tours', 'tf_apartment', 'tf_carrental' ) as $tourfic_type ) {
	foreach ( array( null, 'no', 'yes' ) as $tourfic_virtual ) {
		$product = tourfic_shipping_product( $tourfic_type, $tourfic_virtual );
		$tourfic_bookings[ $tourfic_type . ':' . ( $tourfic_virtual ?? 'missing' ) ] = $product;
		tourfic_shipping_expect( true === $product->get_virtual() && ! $product->needs_shipping(), $tourfic_type . ': booking is virtual when _virtual=' . ( $tourfic_virtual ?? 'missing' ) );
		tourfic_shipping_expect( $product->get_object_read(), $tourfic_type . ': object finishes normal product hydration' );
	}
}
$tourfic_native_physical = tourfic_shipping_product( 'product', 'no' );
$tourfic_native_default = tourfic_shipping_product( 'product' );
$tourfic_native_virtual = tourfic_shipping_product( 'product', 'yes' );
tourfic_shipping_expect( ! $tourfic_native_physical->get_virtual() && $tourfic_native_physical->needs_shipping(), 'Native physical product retains shipping' );
tourfic_shipping_expect( $tourfic_native_default->needs_shipping(), 'Native product with no virtual metadata remains physical' );
tourfic_shipping_expect( $tourfic_native_virtual->get_virtual() && ! $tourfic_native_virtual->needs_shipping(), 'Native virtual product retains its virtual setting' );
$tourfic_variation = new WC_Product_Variation();
tourfic_shipping_expect( 'WC_Product_Variation_Data_Store_CPT' === $tourfic_variation->get_data_store()->get_current_class_name(), 'Variation keeps the native WooCommerce data store' );
tourfic_shipping_expect( $tourfic_variation->needs_shipping(), 'Native physical variation retains shipping' );
$tourfic_virtual_variation = new WC_Product_Variation();
$tourfic_virtual_variation->set_virtual( true );
tourfic_shipping_expect( ! $tourfic_virtual_variation->needs_shipping(), 'Native virtual variation remains virtual' );

$tourfic_cart_cases = array();
foreach ( $tourfic_bookings as $label => $product ) {
	$tourfic_cart_cases[ $label ] = array( array( 'booking' => $product ), false );
}
$tourfic_cart_cases['all bookings'] = array( $tourfic_bookings, false );
$tourfic_cart_cases['native physical'] = array( array( 'physical' => $tourfic_native_physical ), true );
$tourfic_cart_cases['native virtual'] = array( array( 'virtual' => $tourfic_native_virtual ), false );
$tourfic_cart_cases['native default'] = array( array( 'physical' => $tourfic_native_default ), true );
$tourfic_cart_cases['physical variation'] = array( array( 'physical' => $tourfic_variation ), true );
$tourfic_cart_cases['virtual variation'] = array( array( 'virtual' => $tourfic_virtual_variation ), false );
$tourfic_cart_cases['mixed physical/bookings'] = array( array( 'physical' => $tourfic_native_physical ) + $tourfic_bookings, true );
$tourfic_cart_cases['mixed virtual/bookings'] = array( array( 'virtual' => $tourfic_native_virtual ) + $tourfic_bookings, false );
$tourfic_configs = array(
	'enabled' => array( '', 'shipping', 1 ),
	'no methods' => array( '', 'shipping', 0 ),
	'disabled' => array( 'disabled', 'shipping', 1 ),
	'billing only' => array( '', 'billing_only', 1 ),
);

foreach ( $tourfic_configs as $config_label => $config ) {
	$tourfic_shipping_options = array(
		'woocommerce_ship_to_countries' => $config[0],
		'woocommerce_ship_to_destination' => $config[1],
		'woocommerce_shipping_cost_requires_address' => 'no',
		'woocommerce_enable_order_comments' => 'no',
	);
	$tourfic_shipping_method_count = $config[2];
	foreach ( array( 'shortcode', 'store-api' ) as $context ) {
		foreach ( $tourfic_cart_cases as $case_label => $case ) {
			$cart = tourfic_shipping_cart( $case[0], $context );
			$expected_shipping = $case[1] && 'disabled' !== $config[0] && 0 < $config[2];
			$expected_address = $expected_shipping && 'billing_only' !== $config[1];
			$label = $context . '/' . $config_label . '/' . $case_label;
			tourfic_shipping_expect( $expected_shipping === $cart->needs_shipping(), $label . ': shipping follows product type and store configuration' );
			tourfic_shipping_expect( $expected_address === $cart->needs_shipping_address(), $label . ': address follows shipping and billing-only configuration' );
			$packages = $cart->get_shipping_packages();
			$shipping_items = $packages[0]['contents'] ?? array();
			$expected_items = isset( $case[0]['physical'] ) ? array( 'physical' ) : array();
			tourfic_shipping_expect( $expected_items === array_keys( $shipping_items ), $label . ': packages contain only the native physical item' );
			$before = WC()->shipping->calls;
			$cart->calculate_shipping();
			tourfic_shipping_expect( ( $expected_shipping ? 1 : 0 ) === WC()->shipping->calls - $before, $label . ': only shippable carts reach rate calculation' );
			tourfic_shipping_expect( $expected_address === ( false !== strpos( tourfic_shipping_form(), 'name="shipping_address_1"' ) ), $label . ': actual classic template shows the expected address form' );
		}
	}
}

$tourfic_shipping_options['woocommerce_ship_to_countries'] = '';
$tourfic_shipping_options['woocommerce_ship_to_destination'] = 'shipping';
$tourfic_shipping_method_count = 1;
foreach ( array( 'hotel' => 'tf_hotel_data', 'tour' => 'tf_tours_data', 'apartment' => 'tf_apartment_data', 'car' => 'tf_car_data' ) as $tourfic_kind => $tourfic_key ) {
	$cart = tourfic_shipping_cart( array( 'physical' => $tourfic_native_physical ), 'shortcode' );
	$cart->cart_contents['physical'][ $tourfic_key ] = array( 'order_type' => $tourfic_kind );
	tourfic_shipping_expect( $cart->needs_shipping() && $cart->needs_shipping_address(), $tourfic_kind . ': booking-shaped metadata cannot suppress physical shipping' );
	tourfic_shipping_expect( array( 'physical' ) === array_keys( $cart->get_shipping_packages()[0]['contents'] ), $tourfic_kind . ': booking-shaped metadata leaves physical items in shipping packages' );
}

$tourfic_store_schema = file_get_contents( WC_ABSPATH . 'src/StoreApi/Schemas/V1/CartSchema.php' );
tourfic_shipping_expect( 1 === preg_match( "/'needs_shipping'\s*=>\s*\\\$cart->needs_shipping\(\)/", $tourfic_store_schema ), 'Store API serializes the same tested WC_Cart shipping decision' );
tourfic_shipping_expect( 1 === preg_match( '/\$cart->has_calculated_shipping\(\)\s*\?\s*\$controller->get_shipping_packages\(\)\s*:\s*\[\]/', $tourfic_store_schema ), 'Store API omits rates when the tested cart did not calculate shipping' );

foreach ( $tourfic_shipping_failures as $tourfic_failure ) {
	echo 'FAIL: ' . $tourfic_failure . "\n";
}
echo json_encode( array( 'checks' => $tourfic_shipping_checks, 'failures' => count( $tourfic_shipping_failures ) ) ) . "\n";
exit( $tourfic_shipping_failures ? 1 : 0 );
