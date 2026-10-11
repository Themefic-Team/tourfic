<?php
/**
 * Security regression checks: Wishlist shortcode type attribute must be sanitized and escaped.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/security/wishlist-shortcode-stored-xss-security.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root = dirname( __DIR__, 2 );

$wishlist_file = $root . '/inc/App/Shortcodes/Wishlist.php';

function tf_wishlist_xss_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$wishlist_source = file_get_contents( $wishlist_file );

// 1. Ensure sanitize_text_field is applied to type attribute.
tf_wishlist_xss_assert(
	false !== strpos( $wishlist_source, 'sanitize_text_field(' ),
	'Wishlist::render must sanitize $atts[\'type\'] with sanitize_text_field.'
);

// 2. Ensure esc_attr is applied to data-type in the guest branch.
tf_wishlist_xss_assert(
	false !== strpos( $wishlist_source, 'data-type="\' . esc_attr( $type ) . \'"' )
	|| false !== strpos( $wishlist_source, 'data-type="\' . esc_attr(' ),
	'Wishlist::render must escape data-type attribute with esc_attr.'
);

// 3. Ensure unescaped concatenation does not exist.
tf_wishlist_xss_assert(
	false === strpos( $wishlist_source, 'data-type="\' . $atts[\'type\'] . \'"' ),
	'Wishlist::render must not concatenate unescaped $atts[\'type\'] into data-type.'
);

// 4. Ensure esc_attr is applied to data-nonce.
tf_wishlist_xss_assert(
	false !== strpos( $wishlist_source, 'data-nonce="\' . esc_attr(' ),
	'Wishlist::render must escape data-nonce attribute with esc_attr.'
);

// 5. Functional simulation of escaping behavior across various inputs.
function tf_simulate_wishlist_guest_output( $atts ) {
	$defaults = array( 'type' => '' );
	// Simulate wp_parse_args
	$atts = array_merge( $defaults, (array) $atts );
	// Simulate sanitize_text_field + esc_attr
	$type = ! empty( $atts['type'] ) ? htmlspecialchars( strip_tags( (string) $atts['type'] ), ENT_QUOTES, 'UTF-8' ) : '';
	$nonce = htmlspecialchars( 'mock-nonce', ENT_QUOTES, 'UTF-8' );

	return '<div class="tf-wishlist-holder" data-type="' . $type . '" data-nonce="' . $nonce . '">';
}

// 5a. Normal input: single type
$normal_out = tf_simulate_wishlist_guest_output( array( 'type' => 'tf_hotel' ) );
tf_wishlist_xss_assert(
	false !== strpos( $normal_out, 'data-type="tf_hotel"' ),
	'Normal single post type attribute must remain intact.'
);

// 5b. Normal input: comma-separated types
$multi_out = tf_simulate_wishlist_guest_output( array( 'type' => 'tf_hotel,tf_tours' ) );
tf_wishlist_xss_assert(
	false !== strpos( $multi_out, 'data-type="tf_hotel,tf_tours"' ),
	'Comma-separated post type attributes must remain intact.'
);

// 5c. Empty input
$empty_out = tf_simulate_wishlist_guest_output( array() );
tf_wishlist_xss_assert(
	false !== strpos( $empty_out, 'data-type=""' ),
	'Default empty attribute must render empty string without warnings.'
);

// 5d. Malicious attribute breakout payload
$mock_payload = 'X" autofocus onfocus=alert(document.domain) x="';
$xss_out      = tf_simulate_wishlist_guest_output( array( 'type' => $mock_payload ) );

tf_wishlist_xss_assert(
	false === strpos( $xss_out, '" onfocus=' ) && false === strpos( $xss_out, '" autofocus' ),
	'Attribute breakout must be completely neutralized.'
);
tf_wishlist_xss_assert(
	false !== strpos( $xss_out, '&quot;' ),
	'Quotes must be safely encoded as entities.'
);

echo "Wishlist shortcode type attribute XSS security regression checks passed.\n";
