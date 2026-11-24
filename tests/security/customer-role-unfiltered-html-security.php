<?php
/**
 * Security regression checks: Customer role must never receive unfiltered_html capability.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/security/customer-role-unfiltered-html-security.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root = dirname( __DIR__, 2 );

$action_helper_file = $root . '/inc/Traits/Action_Helper.php';
$helper_file        = $root . '/inc/Classes/Helper.php';

function tf_unfiltered_html_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

$action_helper_source = file_get_contents( $action_helper_file );
$helper_source        = file_get_contents( $helper_file );

// 1. Ensure unfiltered_html is never added to customer role.
tf_unfiltered_html_assert(
	false === strpos( $action_helper_source, "\$customer_role->add_cap( 'unfiltered_html' )" )
	&& false === strpos( $action_helper_source, "\$customer_role->add_cap( \$cap )" ),
	'The customer role must never receive unfiltered_html or dynamic capability additions in Action_Helper.'
);

// 2. Ensure init hook granting customer caps is removed.
tf_unfiltered_html_assert(
	false === strpos( $helper_source, "add_action( 'init', array( \$this, 'tf_customer_role_caps' )" ),
	'Helper must not hook tf_customer_role_caps to init.'
);

// 3. Ensure revoke handler is hooked to admin_init.
tf_unfiltered_html_assert(
	false !== strpos( $helper_source, "add_action( 'admin_init', array( \$this, 'tf_revoke_customer_unfiltered_html_cap' )" ),
	'Helper must attach tf_revoke_customer_unfiltered_html_cap to admin_init.'
);

// 4. Ensure Action_Helper defines tf_revoke_customer_unfiltered_html_cap with removal logic.
tf_unfiltered_html_assert(
	false !== strpos( $action_helper_source, 'function tf_revoke_customer_unfiltered_html_cap()' )
	&& false !== strpos( $action_helper_source, "\$customer_role->remove_cap( 'unfiltered_html' )" )
	&& false !== strpos( $action_helper_source, "delete_option( 'tourfic_customer_caps' )" ),
	'Action_Helper must contain tf_revoke_customer_unfiltered_html_cap that removes unfiltered_html and deletes legacy option.'
);

// 5. Scan codebase to ensure unfiltered_html is not granted anywhere in inc/
$inc_dir = new RecursiveDirectoryIterator( $root . '/inc' );
$iterator = new RecursiveIteratorIterator( $inc_dir );
foreach ( $iterator as $file ) {
	if ( $file->isFile() && 'php' === $file->getExtension() ) {
		$content = file_get_contents( $file->getPathname() );
		// Ensure no add_cap with unfiltered_html
		tf_unfiltered_html_assert(
			! preg_match( '/add_cap\s*\(\s*[\'"]unfiltered_html[\'"]\s*\)/', $content ),
			"Disallowed add_cap('unfiltered_html') found in {$file->getPathname()}."
		);
	}
}

echo "Customer role unfiltered_html security regression checks passed.\n";
