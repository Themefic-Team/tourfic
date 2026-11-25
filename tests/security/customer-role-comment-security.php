<?php
/**
 * Exercise customer role upgrades with WordPress's real roles, users and KSES.
 *
 * Run: php tests/security/customer-role-comment-security.php [wordpress-root]
 * Only core library files are loaded; options and user metadata stay in memory.
 */

if ( 'cli' !== PHP_SAPI || defined( 'ABSPATH' ) ) {
	exit( 1 );
}

$tourfic_root = dirname( __DIR__, 2 );
$wordpress_root = $argv[1] ?? dirname( $tourfic_root, 3 );
if ( ! is_readable( $wordpress_root . '/wp-includes/plugin.php' ) ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- CLI-only STDERR diagnostic; WordPress is deliberately not bootstrapped.
	fwrite( STDERR, "Pass the path to a WordPress installation; its database will not be loaded.\n" );
	exit( 1 );
}

define( 'ABSPATH', rtrim( $wordpress_root, '/' ) . '/' );
define( 'WPINC', 'wp-includes' );

$tourfic_fixture_options = array();
$tourfic_fixture_meta = array();
$tourfic_fixture_writes = array();
$tourfic_fixture_site = 1;
$tourfic_fixture_woo = false;
$tourfic_fixture_checks = 0;
$tourfic_fixture_failures = array();

function get_current_blog_id() { return $GLOBALS['tourfic_fixture_site']; }
function is_multisite() { return false; }
function is_admin() { return false; }
function is_plugin_active( $plugin ) { return 'woocommerce/woocommerce.php' === $plugin && $GLOBALS['tourfic_fixture_woo']; }
function get_option( $key, $default = false ) {
	return $GLOBALS['tourfic_fixture_options'][ get_current_blog_id() ][ $key ] ?? $default;
}
function update_option( $key, $value, $autoload = null ) {
	$GLOBALS['tourfic_fixture_options'][ get_current_blog_id() ][ $key ] = $value;
	$GLOBALS['tourfic_fixture_writes'][] = array( get_current_blog_id(), $key );
	return true;
}
function delete_option( $key ) {
	if ( ! array_key_exists( $key, $GLOBALS['tourfic_fixture_options'][ get_current_blog_id() ] ) ) {
		return false;
	}
	unset( $GLOBALS['tourfic_fixture_options'][ get_current_blog_id() ][ $key ] );
	$GLOBALS['tourfic_fixture_writes'][] = array( get_current_blog_id(), $key );
	return true;
}
function get_user_meta( $id, $key, $single = false ) {
	return $GLOBALS['tourfic_fixture_meta'][ $id ][ $key ] ?? array();
}
function wp_get_current_user() { return $GLOBALS['current_user']; }
function wp_allowed_protocols() { return array( 'http', 'https', 'mailto' ); }
function absint( $value ) { return abs( (int) $value ); }
function wp_is_numeric_array( $value ) { return is_array( $value ) && ! array_filter( array_keys( $value ), 'is_string' ); }

$wpdb = new class {
	public $prefix = 'wp_';
	public function get_blog_prefix( $site = 1 ) { return 1 === $site ? 'wp_' : 'wp_' . $site . '_'; }
};

foreach ( array( 'compat.php', 'compat-utf8.php', 'class-wp-token-map.php', 'utf8.php', 'plugin.php', 'class-wp-role.php', 'class-wp-roles.php', 'class-wp-user.php', 'capabilities.php', 'kses.php', 'comment.php' ) as $tourfic_library ) {
	if ( is_readable( ABSPATH . WPINC . '/' . $tourfic_library ) ) {
		require ABSPATH . WPINC . '/' . $tourfic_library;
	}
}

foreach ( array(
	'html5-named-character-references.php',
	'class-wp-html-attribute-token.php',
	'class-wp-html-span.php',
	'class-wp-html-doctype-info.php',
	'class-wp-html-text-replacement.php',
	'class-wp-html-decoder.php',
	'class-wp-html-tag-processor.php',
) as $tourfic_library ) {
	if ( is_readable( ABSPATH . WPINC . '/html-api/' . $tourfic_library ) ) {
		require_once ABSPATH . WPINC . '/html-api/' . $tourfic_library;
	}
}

$tourfic_source = getenv( 'TOURFIC_CUSTOMER_CAPS_SOURCE' ) ?: $tourfic_root;
foreach ( array( 'Traits/Singleton.php', 'Traits/TF_Fonts.php', 'Traits/Action_Helper.php', 'Classes/Helper.php' ) as $tourfic_library ) {
	require $tourfic_source . '/inc/' . $tourfic_library;
}

function tourfic_customer_caps_expect( $condition, $label ) {
	++$GLOBALS['tourfic_fixture_checks'];
	if ( ! $condition ) {
		$GLOBALS['tourfic_fixture_failures'][] = $label;
	}
}

function tourfic_customer_caps_fixture( $version, $unsafe, $woo, $roles = array( 'customer' ), $customer_exists = true, $during_init = false, $revoked = false ) {
	$GLOBALS['wp_filter'] = array();
	$GLOBALS['wp_user_roles'] = null;
	$GLOBALS['tourfic_fixture_writes'] = array();
	$GLOBALS['tourfic_fixture_woo'] = $woo;
	$definitions = array(
		'customer' => array( 'name' => 'Customer', 'capabilities' => array( 'read' => true, 'level_0' => true ) ),
		'subscriber' => array( 'name' => 'Subscriber', 'capabilities' => array( 'read' => true ) ),
		'administrator' => array( 'name' => 'Administrator', 'capabilities' => array( 'read' => true, 'manage_options' => true, 'unfiltered_html' => true ) ),
		'editor' => array( 'name' => 'Editor', 'capabilities' => array( 'read' => true, 'edit_others_posts' => true, 'unfiltered_html' => true ) ),
	);
	if ( $unsafe ) {
		$definitions['customer']['capabilities']['unfiltered_html'] = true;
	}
	if ( ! $customer_exists ) {
		unset( $definitions['customer'] );
	}
	$role_key = $GLOBALS['wpdb']->get_blog_prefix( get_current_blog_id() ) . 'user_roles';
	$GLOBALS['tourfic_fixture_options'][ get_current_blog_id() ] = array( $role_key => $definitions );
	if ( null !== $version ) {
		$GLOBALS['tourfic_fixture_options'][ get_current_blog_id() ]['tourfic_customer_caps'] = $version;
	}
	if ( $revoked ) {
		$GLOBALS['tourfic_fixture_options'][ get_current_blog_id() ]['tourfic_customer_unfiltered_html_revoked'] = $revoked;
	}
	$GLOBALS['wp_roles'] = new WP_Roles();
	$cap_key = $GLOBALS['wpdb']->get_blog_prefix( get_current_blog_id() ) . 'capabilities';
	$GLOBALS['tourfic_fixture_meta'][17][ $cap_key ] = array_fill_keys( $roles, true );
	$GLOBALS['current_user'] = new WP_User( (object) array( 'ID' => 17 ) );
	kses_init();
	add_action( 'init', 'kses_init' );
	if ( $during_init ) {
		$helper = ( new ReflectionClass( \Tourfic\Classes\Helper::class ) )->newInstanceWithoutConstructor();
		add_action( 'init', array( $helper, '__construct' ), 0 );
		return $helper;
	}
	return new \Tourfic\Classes\Helper();
}

function tourfic_customer_caps_filtered_comment() {
	$payload = '<strong>Safe review</strong> <em>formatting</em> <a href="https://example.invalid" onclick="void(0)">link</a><script type="text/plain">inert-fixture</script>';
	$result = wp_filter_comment( array(
		'comment_author' => 'Fixture',
		'comment_author_email' => 'fixture@example.invalid',
		'comment_author_url' => '',
		'comment_author_IP' => '127.0.0.1',
		'comment_content' => addslashes( $payload ),
	) );
	return stripslashes( $result['comment_content'] );
}

foreach ( array( null, 0, 1, '1', 2 ) as $tourfic_version ) {
	foreach ( array( false, true ) as $tourfic_woo ) {
		$label = 'version=' . json_encode( $tourfic_version ) . ', Woo=' . (int) $tourfic_woo;
		$helper = tourfic_customer_caps_fixture( $tourfic_version, null !== $tourfic_version, $tourfic_woo );
		tourfic_customer_caps_expect( 1 === has_action( 'init', array( $helper, 'tourfic_maybe_revoke_customer_unfiltered_html' ) ), 'Cleanup precedes core KSES: ' . $label );
		do_action( 'init' );
		tourfic_customer_caps_expect( ! get_role( 'customer' )->has_cap( 'unfiltered_html' ), 'Customer role cannot bypass HTML filtering: ' . $label );
		tourfic_customer_caps_expect( ! current_user_can( 'unfiltered_html' ), 'Preloaded customer capability cache is refreshed: ' . $label );
		tourfic_customer_caps_expect( current_user_can( 'read' ) && get_role( 'customer' )->has_cap( 'level_0' ), 'Normal customer permissions survive: ' . $label );
		tourfic_customer_caps_expect( 1 === get_option( 'tourfic_customer_unfiltered_html_revoked' ), 'Cleanup completes its upgrade gate: ' . $label );
		tourfic_customer_caps_expect( false === get_option( 'tourfic_customer_caps' ), 'Legacy capability marker is removed: ' . $label );
		$html = tourfic_customer_caps_filtered_comment();
		tourfic_customer_caps_expect( false === strpos( $html, '<script' ) && false === strpos( $html, 'onclick' ), 'Core filters unsafe comment markup on the upgrade request: ' . $label );
		tourfic_customer_caps_expect( false !== strpos( $html, '<strong>Safe review</strong>' ) && false !== strpos( $html, '<em>formatting</em>' ) && false !== strpos( $html, 'href=' ), 'Safe review formatting survives: ' . $label );
		$writes = $GLOBALS['tourfic_fixture_writes'];
		$helper->tf_customer_role_caps();
		tourfic_customer_caps_expect( $writes === $GLOBALS['tourfic_fixture_writes'], 'Repeated migration performs no writes: ' . $label );
		$GLOBALS['wp_roles'] = new WP_Roles();
		tourfic_customer_caps_expect( ! get_role( 'customer' )->has_cap( 'unfiltered_html' ), 'Revocation persists after reloading roles: ' . $label );
		tourfic_customer_caps_expect( get_role( 'administrator' )->has_cap( 'unfiltered_html' ) && get_role( 'editor' )->has_cap( 'unfiltered_html' ), 'Privileged role capabilities are unchanged: ' . $label );
	}
}

$helper = tourfic_customer_caps_fixture( 1, false, false, array( 'subscriber' ), false );
try {
	do_action( 'init' );
	tourfic_customer_caps_expect( false === get_option( 'tourfic_customer_unfiltered_html_revoked' ), 'Missing role does not prematurely complete cleanup.' );
	tourfic_customer_caps_expect( false === get_option( 'tourfic_customer_caps' ), 'Missing role still clears the legacy capability marker.' );
} catch ( Throwable $error ) {
	tourfic_customer_caps_expect( false, 'Missing role must not cause an exception: ' . $error->getMessage() );
}
add_role( 'customer', 'Customer', array( 'read' => true, 'unfiltered_html' => true ) );
$helper->tf_customer_role_caps();
tourfic_customer_caps_expect( 1 === get_option( 'tourfic_customer_unfiltered_html_revoked' ) && ! get_role( 'customer' )->has_cap( 'unfiltered_html' ), 'A subsequently available role is cleaned up.' );

foreach ( array( array( 'administrator' ), array( 'editor' ), array( 'customer', 'administrator' ) ) as $tourfic_roles ) {
	tourfic_customer_caps_fixture( 1, true, false, $tourfic_roles );
	do_action( 'init' );
	tourfic_customer_caps_expect( current_user_can( 'unfiltered_html' ), 'Legitimate privilege is retained for ' . implode( '+', $tourfic_roles ) );
}

$helper = tourfic_customer_caps_fixture( null, false, false, array( 'customer' ), true, false, 1 );
do_action( 'init' );
tourfic_customer_caps_expect( array() === $GLOBALS['tourfic_fixture_writes'], 'An already migrated site performs no option or role writes.' );
tourfic_customer_caps_expect( ! current_user_can( 'unfiltered_html' ), 'A site already migrated by staging remains safe.' );
$GLOBALS['tourfic_fixture_site'] = 2;
tourfic_customer_caps_fixture( 1, true, false );
do_action( 'init' );
tourfic_customer_caps_expect( ! get_role( 'customer' )->has_cap( 'unfiltered_html' ) && 1 === get_option( 'tourfic_customer_unfiltered_html_revoked' ), 'Another site prefix receives its own cleanup.' );
tourfic_customer_caps_expect( 1 === $GLOBALS['tourfic_fixture_options'][1]['tourfic_customer_unfiltered_html_revoked'], 'Cleaning another site preserves the first site marker.' );

tourfic_customer_caps_fixture( 1, true, false, array( 'customer' ), true, true );
do_action( 'init' );
tourfic_customer_caps_expect( 1 === get_option( 'tourfic_customer_unfiltered_html_revoked' ), 'Helper construction during init priority zero completes cleanup on that request.' );
tourfic_customer_caps_expect( ! current_user_can( 'unfiltered_html' ), 'Late helper construction refreshes the customer capability cache.' );
$html = tourfic_customer_caps_filtered_comment();
tourfic_customer_caps_expect( false === strpos( $html, '<script' ) && false === strpos( $html, 'onclick' ), 'Late helper construction still restores core comment filtering before submission.' );

foreach ( $GLOBALS['tourfic_fixture_failures'] as $tourfic_failure ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- CLI-only STDERR diagnostic; WordPress is deliberately not bootstrapped.
	fwrite( STDERR, 'FAIL: ' . $tourfic_failure . "\n" );
}
echo json_encode( array( 'checks' => $GLOBALS['tourfic_fixture_checks'], 'failures' => count( $GLOBALS['tourfic_fixture_failures'] ) ) ) . "\n";
exit( $GLOBALS['tourfic_fixture_failures'] ? 1 : 0 );
