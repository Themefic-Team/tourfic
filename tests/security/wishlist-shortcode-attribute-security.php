<?php
/**
 * Test actual shortcode parsing, KSES, escaping and wishlist rendering without a database.
 * Run: php tests/security/wishlist-shortcode-attribute-security.php [wordpress-root]
 */

if ( 'cli' !== PHP_SAPI || defined( 'ABSPATH' ) ) {
	exit( 1 );
}

$tourfic_root = dirname( __DIR__, 2 );
$wordpress_root = $argv[1] ?? dirname( $tourfic_root, 3 );
if ( ! is_readable( $wordpress_root . '/wp-includes/shortcodes.php' ) ) {
	echo "Pass the path to a WordPress installation; its database will not be loaded.\n";
	exit( 1 );
}

define( 'ABSPATH', rtrim( $wordpress_root, '/' ) . '/' );
define( 'WPINC', 'wp-includes' );
define( 'TOURFIC_TEMPLATE_PATH', $tourfic_root . '/templates/' );

$tourfic_shortcode_logged_in = false;
$tourfic_shortcode_checks = 0;
$tourfic_shortcode_failures = array();
$tourfic_shortcode_posts = array();

function get_option( $key, $default = false ) { return 'blog_charset' === $key ? 'UTF-8' : $default; }
function is_utf8_charset( $charset = null ) { return _is_utf8_charset( $charset ?? 'UTF-8' ); }
function _canonical_charset( $charset ) { return 'UTF-8'; }
function wp_allowed_protocols() { return array( 'http', 'https', 'mailto' ); }
function wp_parse_args( $args, $defaults = array() ) { return array_merge( $defaults, (array) $args ); }
function is_user_logged_in() { return $GLOBALS['tourfic_shortcode_logged_in']; }
function get_current_user_id() { return is_user_logged_in() ? 17 : 0; }
function get_post( $id ) { return $GLOBALS['tourfic_shortcode_posts'][ $id ] ?? null; }
function get_user_meta( $id, $key, $single = false ) {
	return array_map( function( $post ) {
		return array( 'post_id' => $post->ID, 'post_type' => $post->post_type );
	}, array_values( $GLOBALS['tourfic_shortcode_posts'] ) );
}
function wp_create_nonce( $action ) { return 'fixture-nonce'; }
function the_permalink( $id ) { echo 'https://example.invalid/listing/' . (int) $id; }
function esc_html_e( $text, $domain = '' ) { echo esc_html( $text ); }
function esc_attr_e( $text, $domain = '' ) { echo esc_attr( $text ); }
function esc_html__( $text, $domain = '' ) { return esc_html( $text ); }

foreach ( array( 'compat.php', 'compat-utf8.php', 'class-wp-token-map.php', 'utf8.php', 'plugin.php', 'formatting.php', 'shortcodes.php', 'kses.php' ) as $tourfic_library ) {
	if ( is_readable( ABSPATH . WPINC . '/' . $tourfic_library ) ) {
		require ABSPATH . WPINC . '/' . $tourfic_library;
	}
}
foreach ( array( 'html5-named-character-references.php', 'class-wp-html-attribute-token.php', 'class-wp-html-span.php', 'class-wp-html-doctype-info.php', 'class-wp-html-text-replacement.php', 'class-wp-html-decoder.php', 'class-wp-html-tag-processor.php' ) as $tourfic_library ) {
	if ( is_readable( ABSPATH . WPINC . '/html-api/' . $tourfic_library ) ) {
		require ABSPATH . WPINC . '/html-api/' . $tourfic_library;
	}
}

require $tourfic_root . '/inc/Traits/Singleton.php';
require $tourfic_root . '/inc/Core/Shortcodes.php';
require $tourfic_root . '/inc/App/Wishlist.php';
$tourfic_source = getenv( 'TOURFIC_WISHLIST_SHORTCODE_SOURCE' ) ?: $tourfic_root . '/inc/App/Shortcodes/Wishlist.php';
require $tourfic_source;
$shortcode = new \Tourfic\App\Shortcodes\Wishlist();

foreach ( array( 'tf_hotel', 'tf_tours', 'tf_apartment', 'tf_carrental' ) as $tourfic_index => $tourfic_type ) {
	$id = 100 + $tourfic_index;
	$tourfic_shortcode_posts[ $id ] = (object) array( 'ID' => $id, 'post_type' => $tourfic_type, 'post_status' => 'publish', 'post_password' => '', 'post_title' => 'Public fixture ' . $id );
}

function tourfic_shortcode_attr_expect( $condition, $label ) {
	++$GLOBALS['tourfic_shortcode_checks'];
	if ( ! $condition ) {
		$GLOBALS['tourfic_shortcode_failures'][] = $label;
	}
}

function tourfic_shortcode_attr_dom( $html ) {
	$errors = libxml_use_internal_errors( true );
	$dom = new DOMDocument();
	$dom->loadHTML( $html, LIBXML_NONET );
	libxml_clear_errors();
	libxml_use_internal_errors( $errors );
	return $dom;
}

function tourfic_shortcode_attr_safe( $dom ) {
	foreach ( $dom->getElementsByTagName( '*' ) as $element ) {
		if ( in_array( strtolower( $element->tagName ), array( 'script', 'iframe', 'svg', 'img' ), true ) ) {
			return false;
		}
		foreach ( $element->attributes as $attribute ) {
			if ( 0 === stripos( $attribute->name, 'on' ) || 'autofocus' === strtolower( $attribute->name ) ) {
				return false;
			}
		}
	}
	return true;
}

$tourfic_cases = array(
	'' => 4,
	'tf_hotel' => 1,
	'tf_tours' => 1,
	'tf_apartment' => 1,
	'tf_carrental' => 1,
	'tf_hotel,tf_tours' => 2,
	'tf_hotel,tf_tours,tf_apartment,tf_carrental' => 4,
);
foreach ( array( false, true ) as $tourfic_logged_in ) {
	$tourfic_shortcode_logged_in = $tourfic_logged_in;
	foreach ( $tourfic_cases as $tourfic_type => $tourfic_count ) {
		$html = do_shortcode( '[tourfic_wishlist type="' . $tourfic_type . '"]' );
		$dom = tourfic_shortcode_attr_dom( $html );
		$xpath = new DOMXPath( $dom );
		$node = $xpath->query( $tourfic_logged_in ? '//table' : '//div[@class="tf-wishlist-holder"]' )->item( 0 );
		tourfic_shortcode_attr_expect( $node && $tourfic_type === $node->getAttribute( 'data-type' ), 'Type filter survives for login=' . (int) $tourfic_logged_in . ', type=' . $tourfic_type );
		tourfic_shortcode_attr_expect( tourfic_shortcode_attr_safe( $dom ), 'Normal rendering has no active injected markup.' );
		if ( $tourfic_logged_in ) {
			tourfic_shortcode_attr_expect( $tourfic_count === $xpath->query( '//tbody/tr' )->length, 'Logged-in filter keeps the expected listing count.' );
		} else {
			tourfic_shortcode_attr_expect( 'fixture-nonce' === $node->getAttribute( 'data-nonce' ), 'Guest nonce remains available.' );
		}
	}
	$dom = tourfic_shortcode_attr_dom( do_shortcode( '[tourfic_wishlist]' ) );
	tourfic_shortcode_attr_expect( tourfic_shortcode_attr_safe( $dom ), 'Default shortcode remains safe for login=' . (int) $tourfic_logged_in );
}

$tourfic_shortcode_logged_in = false;
foreach ( array(
	'tf_hotel" onmouseover="void(0)',
	'tf_tours" autofocus onfocus="void(0)',
	'tf_apartment" onpointerenter="void(0)',
) as $tourfic_payload ) {
	$content = "[tourfic_wishlist type='" . $tourfic_payload . "']";
	$stored = wp_kses_post( $content );
	tourfic_shortcode_attr_expect( $content === $stored, 'Quote-only shortcode survives core save filtering, reproducing the reported boundary.' );
	$dom = tourfic_shortcode_attr_dom( do_shortcode( $stored ) );
	tourfic_shortcode_attr_expect( tourfic_shortcode_attr_safe( $dom ), 'Stored shortcode cannot create a new HTML event attribute.' );
	$node = ( new DOMXPath( $dom ) )->query( '//div[@class="tf-wishlist-holder"]' )->item( 0 );
	tourfic_shortcode_attr_expect( $node && $tourfic_payload === $node->getAttribute( 'data-type' ), 'Decoded payload stays inside the data attribute.' );
}

foreach ( array( array( 'tf_hotel' ), (object) array( 'type' => 'tf_hotel' ) ) as $tourfic_bad_type ) {
	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_set_error_handler -- CLI regression converts malformed-input warnings into test failures.
	set_error_handler( function( $severity, $message ) {
		throw new ErrorException( $message, 0, $severity );
	} );
	try {
		$dom = tourfic_shortcode_attr_dom( $shortcode->render( array( 'type' => $tourfic_bad_type ) ) );
		$node = ( new DOMXPath( $dom ) )->query( '//div[@class="tf-wishlist-holder"]' )->item( 0 );
		tourfic_shortcode_attr_expect( $node && '' === $node->getAttribute( 'data-type' ) && tourfic_shortcode_attr_safe( $dom ), 'Non-string programmatic type values fall back safely.' );
	} catch ( Throwable $tourfic_error ) {
		tourfic_shortcode_attr_expect( false, 'Non-string type causes a rendering error: ' . $tourfic_error->getMessage() );
	} finally {
		restore_error_handler();
	}
}

foreach ( $tourfic_shortcode_failures as $tourfic_failure ) {
	echo 'FAIL: ' . $tourfic_failure . "\n";
}
echo json_encode( array( 'checks' => $tourfic_shortcode_checks, 'failures' => count( $tourfic_shortcode_failures ) ) ) . "\n";
exit( $tourfic_shortcode_failures ? 1 : 0 );
