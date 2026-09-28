<?php
/**
 * Regression coverage for vendor approval and account-email contracts.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/vendor-registration-approval-emails.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}

$tourfic_test_meta    = array();
$tourfic_test_actions = array();
$tourfic_test_filters = array();

function tourfic_vendor_account_assert( $condition, $message ) {
	if ( ! $condition ) {
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	global $tourfic_test_actions;
	$tourfic_test_actions[ $hook ] = array( $callback, $priority, $accepted_args );
}

function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	global $tourfic_test_filters;
	$tourfic_test_filters[ $hook ] = array( $callback, $priority, $accepted_args );
}

function absint( $value ) {
	return abs( (int) $value );
}

function get_user_meta( $user_id, $key, $single = false ) {
	global $tourfic_test_meta;
	$value = isset( $tourfic_test_meta[ $user_id ][ $key ] ) ? $tourfic_test_meta[ $user_id ][ $key ] : '';

	return $single ? $value : array( $value );
}

function update_user_meta( $user_id, $key, $value ) {
	global $tourfic_test_filters, $tourfic_test_meta;
	if ( isset( $tourfic_test_filters['update_user_metadata'] ) ) {
		call_user_func( $tourfic_test_filters['update_user_metadata'][0], null, $user_id, $key, $value, '' );
	}
	$tourfic_test_meta[ $user_id ][ $key ] = $value;

	return true;
}

function delete_user_meta( $user_id, $key ) {
	global $tourfic_test_meta;
	unset( $tourfic_test_meta[ $user_id ][ $key ] );

	return true;
}

$tourfic_root = dirname( __DIR__, 2 );
$plugins_root = dirname( $tourfic_root );
$pro_root     = $plugins_root . '/tourfic-pro';

require_once $pro_root . '/inc/classes/class-vendor-approval.php';

tourfic_vendor_account_assert(
	isset( $tourfic_test_actions['added_user_meta'], $tourfic_test_filters['update_user_metadata'] ),
	'The approval service must observe direct additions and every attempted legacy status update.'
);

update_user_meta( 10, 'tf_vendor_approval', 'disabled' );
tourfic_vendor_account_assert(
	! Tourfic_Pro_Vendor_Approval::is_pending( 10 ),
	'Legacy disabled vendors must not be silently reclassified as pending.'
);

Tourfic_Pro_Vendor_Approval::mark_pending( 10 );
tourfic_vendor_account_assert(
	Tourfic_Pro_Vendor_Approval::is_pending( 10 ),
	'New manual-approval registrations must be distinguishable from disabled vendors.'
);

update_user_meta( 10, 'tf_vendor_approval', 'enabled' );
tourfic_vendor_account_assert(
	! Tourfic_Pro_Vendor_Approval::is_pending( 10 ),
	'Approving a vendor must clear the pending marker.'
);

Tourfic_Pro_Vendor_Approval::mark_pending( 10 );
update_user_meta( 10, 'unrelated_meta', 'disabled' );
tourfic_vendor_account_assert(
	'1' === get_user_meta( 10, Tourfic_Pro_Vendor_Approval::PENDING_META_KEY, true ),
	'Unrelated metadata changes must not alter vendor approval state.'
);

update_user_meta( 10, 'tf_vendor_approval', 'disabled' );
Tourfic_Pro_Vendor_Approval::mark_pending( 10 );
update_user_meta( 10, 'tf_vendor_approval', 'disabled' );
tourfic_vendor_account_assert(
	'' === get_user_meta( 10, Tourfic_Pro_Vendor_Approval::PENDING_META_KEY, true ),
	'Repeating the same explicit disabled status must clear a stale pending marker.'
);

update_user_meta( 20, 'tf_vendor_approval', 'disabled' );
update_user_meta( 20, 'tf_is_activated', '0' );
Tourfic_Pro_Vendor_Approval::mark_pending( 20 );
tourfic_vendor_account_assert(
	Tourfic_Pro_Vendor_Approval::STATE_UNVERIFIED === Tourfic_Pro_Vendor_Approval::get_access_state( 20, true, true ),
	'Email verification must take priority over pending approval.'
);
tourfic_vendor_account_assert(
	Tourfic_Pro_Vendor_Approval::STATE_PENDING === Tourfic_Pro_Vendor_Approval::get_access_state( 20, false, true ),
	'A verified manual-approval vendor must remain pending.'
);
Tourfic_Pro_Vendor_Approval::clear_pending( 20 );
tourfic_vendor_account_assert(
	Tourfic_Pro_Vendor_Approval::STATE_DISABLED === Tourfic_Pro_Vendor_Approval::get_access_state( 20, false, true ),
	'A legacy disabled vendor without a pending marker must remain explicitly disabled.'
);
tourfic_vendor_account_assert(
	Tourfic_Pro_Vendor_Approval::STATE_ACTIVE === Tourfic_Pro_Vendor_Approval::get_access_state( 20, false, false ),
	'Customer accounts must not inherit vendor approval restrictions.'
);
update_user_meta( 20, 'tf_vendor_approval', 'enabled' );
tourfic_vendor_account_assert(
	Tourfic_Pro_Vendor_Approval::STATE_ACTIVE === Tourfic_Pro_Vendor_Approval::get_access_state( 20, false, true ),
	'An approved vendor must be allowed to authenticate.'
);

$register_source = file_get_contents( $pro_root . '/inc/classes/class-register.php' );
$login_source    = file_get_contents( $pro_root . '/inc/classes/class-login.php' );
$template_source = file_get_contents( $pro_root . '/inc/templates/email-verification.php' );
$source_js       = file_get_contents( $pro_root . '/sass/app/js/pro/tourfic-pro.js' );
$built_js        = file_get_contents( $pro_root . '/assets/app/js/tourfic-pro.js' );
$user_api_source = file_get_contents( $pro_root . '/inc/frontend-dashboard/classes/TF_FD_User_Rest_API.php' );
$sidebar_source  = file_get_contents( $tourfic_root . '/inc/App/Widgets/TF_Widget_Base.php' );
$settings_source = file_get_contents( $tourfic_root . '/inc/Admin/TF_Options/options/tf-settings.php' );

tourfic_vendor_account_assert(
	false !== strpos( $register_source, 'Tourfic_Pro_Vendor_Approval::mark_pending( $user_id )' )
		&& false !== strpos( $register_source, 'Your account is awaiting administrator approval.' ),
	'Registration must persist and explain manual approval.'
);

foreach (
	array(
		'tourfic_registration_welcome_email_subject',
		'tourfic_registration_welcome_email_headers',
		'tourfic_registration_welcome_email_message',
		'tourfic_verification_email_subject',
		'tourfic_verification_email_headers',
		'tourfic_verification_email_message',
	) as $filter
) {
	tourfic_vendor_account_assert(
		false !== strpos( $register_source, "'{$filter}'" ),
		"Missing account-email customization filter: {$filter}."
	);
}

tourfic_vendor_account_assert(
	false !== strpos( $register_source, "sprintf( 'From: %1\$s <%2\$s>', \$from_name, \$from_email )" )
		&& false !== strpos( $register_source, "wp_verify_nonce( \$nonce, 'tourfic_resend_verification_' . \$user_id )" ),
	'Verification mail must use the site sender and protect public resend requests.'
);

tourfic_vendor_account_assert(
	false !== strpos( $register_source, 'wp_generate_password( absint( $stringLength ), false, false )' )
		&& false === strpos( $register_source, 'call_user_func( "generateRandomString"' )
		&& false !== strpos( $register_source, "if ( empty( \$response['fieldErrors'] ) )" ),
	'Registration must generate a safe activation code without a missing callback or an uninitialized response warning.'
);

tourfic_vendor_account_assert(
	false !== strpos( $register_source, "'tf_resend_verification' === \$ajax_action" )
		&& false === strpos( $register_source, '\$is_ajax_request = wp_doing_ajax()' ),
	'The user-register email callback must not mistake the registration AJAX request for a resend request.'
);

$verify_position   = strpos( $login_source, 'Tourfic_Pro_Vendor_Approval::STATE_UNVERIFIED === $access_state' );
$pending_position  = strpos( $login_source, 'Tourfic_Pro_Vendor_Approval::STATE_PENDING === $access_state' );
$disabled_position = strpos( $login_source, 'Tourfic_Pro_Vendor_Approval::STATE_DISABLED === $access_state' );
tourfic_vendor_account_assert(
	false !== $verify_position
		&& false !== $pending_position
		&& false !== $disabled_position
		&& $verify_position < $pending_position
		&& $pending_position < $disabled_position
		&& false !== strpos( $login_source, "add_filter( 'authenticate', array( \$this, 'restrict_unverified_user' ), 30, 3 )" )
		&& false === strpos( $login_source, "remove_action( 'authenticate', 'wp_authenticate_username_password', 20 )" ),
	'Login rejection must run after credential validation and prioritize verification, pending approval, then disablement.'
);

tourfic_vendor_account_assert(
	false !== strpos( $template_source, 'tourfic_resend_verification_' )
		&& false !== strpos( $template_source, 'data-nonce=' )
		&& false !== strpos( $source_js, 'nonce: nonce' )
		&& false !== strpos( $built_js, 'nonce: nonce' ),
	'The verification template, source JavaScript, and built JavaScript must share the resend nonce contract.'
);

tourfic_vendor_account_assert(
	false !== strpos( $user_api_source, "esc_html__( 'Disabled', 'tourfic' )" )
		&& substr_count( $user_api_source, 'Tourfic_Pro_Vendor_Approval::clear_pending( $user_id )' ) >= 2
		&& false !== strpos( $user_api_source, 'Tourfic_Pro_Vendor_Approval::clear_pending( $id )' ),
	'Explicit administrator status changes must clear pending state and report disabled vendors accurately.'
);

tourfic_vendor_account_assert(
	false !== strpos( $sidebar_source, "'id'            => 'tf_archive_booking_sidebar'" )
		&& false !== strpos( $sidebar_source, "'id'            => 'tf_search_result'" )
		&& false !== strpos( $sidebar_source, 'Tourfic: Service Archive Sidebar' )
		&& false !== strpos( $settings_source, 'Service archives are generated routes and do not appear under Pages.' ),
	'Sidebar guidance must distinguish stable archive and search-result destinations.'
);

echo "Vendor registration approval and account-email regression checks passed.\n";
