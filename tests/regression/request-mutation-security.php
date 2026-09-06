<?php
/**
 * Regression checks for privileged Tourfic AJAX mutation boundaries.
 *
 * Run from the Tourfic Free plugin root:
 * php tests/regression/request-mutation-security.php
 */

if ( 'cli' === PHP_SAPI && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}
defined( 'ABSPATH' ) || exit;

$root               = dirname( __DIR__, 2 );
$duplicator_source  = file_get_contents( $root . '/inc/Admin/TF_Duplicator.php' );
$options_source     = file_get_contents( $root . '/inc/Admin/TF_Options/TF_Options.php' );
$enqueue_source     = file_get_contents( $root . '/inc/Classes/Enqueue.php' );
$api_keys_source    = file_get_contents( $root . '/inc/Classes/TF_API_Keys.php' );
$hotel_metabox      = file_get_contents( $root . '/inc/Admin/TF_Options/metaboxes/tf-hotel-metabox.php' );
$admin_source       = file_get_contents( $root . '/sass/admin/js/free/admin.js' );
$tf_options_source  = file_get_contents( $root . '/sass/admin/js/free/tf-options.js' );

function tourfic_request_mutation_security_assert( $condition, $message ) {
	if ( ! $condition ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CLI-only test diagnostics.
		echo "FAIL: {$message}\n";
		exit( 1 );
	}
}

tourfic_request_mutation_security_assert(
	false !== strpos( $duplicator_source, "absint( wp_unslash( \$_POST['postID'] ) )" )
		&& false !== strpos( $duplicator_source, "check_ajax_referer( 'tourfic_duplicate_post_' . \$post_id, 'security' )" ),
	'The duplicator must require a positive object-bound request nonce.'
);
tourfic_request_mutation_security_assert(
	false === strpos( $duplicator_source, "\$_POST['postType']" )
		&& false !== strpos( $duplicator_source, '$source_post->post_type' ),
	'The duplicator must derive the post type from the source record, not the request.'
);
tourfic_request_mutation_security_assert(
	false !== strpos( $duplicator_source, "current_user_can( 'edit_post', \$post->ID )" )
		&& false !== strpos( $duplicator_source, 'current_user_can( $post_type_object->cap->create_posts )' )
		&& false !== strpos( $duplicator_source, 'current_user_can( $post_type_object->cap->publish_posts )' ),
	'The duplicator must enforce object edit plus post-type create and publish capabilities.'
);
tourfic_request_mutation_security_assert(
	false !== strpos( $duplicator_source, "'tf_hotel' === get_post_type( \$hotel_id )" )
		&& false !== strpos( $duplicator_source, "current_user_can( 'edit_post', \$hotel_id )" ),
	'Room duplication must validate and authorize its related hotel before writing.'
);

$insert_error_offset = strpos( $duplicator_source, 'if ( is_wp_error( $duplicate_post_id ) )' );
$first_write_offset  = strpos( $duplicator_source, 'update_post_meta( $hotel_id' );
tourfic_request_mutation_security_assert(
	false !== $insert_error_offset && false !== $first_write_offset && $insert_error_offset < $first_write_offset,
	'The duplicate insert result must be checked before metadata side effects.'
);
tourfic_request_mutation_security_assert(
	false === strpos( $admin_source, 'postType: postType' ),
	'The duplicate request must not send a client-selected post type.'
);

foreach (
	array(
		'insert_category_nonce' => 'tourfic_insert_category_data',
		'delete_category_nonce' => 'tourfic_delete_category_data',
		'insert_post_nonce'     => 'tourfic_insert_post_data',
	) as $nonce_key => $nonce_action
) {
	tourfic_request_mutation_security_assert(
		false !== strpos( $options_source, "'{$nonce_action}'" ),
		"The {$nonce_action} endpoint must use its action-specific nonce."
	);
	tourfic_request_mutation_security_assert(
		false !== strpos( $enqueue_source, "'{$nonce_key}'" )
			&& false !== strpos( $enqueue_source, "wp_create_nonce( '{$nonce_action}' )" ),
		"The {$nonce_key} value must be localized for the {$nonce_action} endpoint."
	);
}

foreach (
	array(
		'tour_features',
		'carrental_brand',
		'carrental_fuel_type',
		'carrental_engine_year',
	) as $taxonomy
) {
	tourfic_request_mutation_security_assert(
		false !== strpos( $options_source, "'{$taxonomy}'" ),
		"The inline taxonomy allowlist must include {$taxonomy}."
	);
}

tourfic_request_mutation_security_assert(
	false !== strpos( $options_source, "'tf_room' !== \$post_type" )
		&& false !== strpos( $options_source, "'tf_hotel' !== \$parent_post->post_type" )
		&& false !== strpos( $options_source, "current_user_can( 'edit_post', \$parent_id )" ),
	'Inline post creation must be limited to rooms under an editable hotel.'
);
tourfic_request_mutation_security_assert(
	false !== strpos( $options_source, 'current_user_can( $taxonomy_object->cap->edit_terms )' )
		&& false !== strpos( $options_source, 'current_user_can( $taxonomy_object->cap->delete_terms )' ),
	'Inline term mutations must enforce the registered taxonomy capabilities.'
);
tourfic_request_mutation_security_assert(
	false !== strpos( $tf_options_source, '_nonce: tourficAdminParams.insert_category_nonce' )
		&& false !== strpos( $tf_options_source, '_nonce: tourficAdminParams.delete_category_nonce' )
		&& false !== strpos( $tf_options_source, '_nonce: tourficAdminParams.insert_post_nonce' ),
	'The source JavaScript must send the matching action-specific nonces.'
);

tourfic_request_mutation_security_assert(
	false === strpos( $options_source, 'tourfic_delete_post_data' )
		&& false === strpos( $enqueue_source, 'delete_post_nonce' )
		&& false === strpos( $hotel_metabox, "'inline_delete'   => true" ),
	'The broken room hard-delete control and its unused endpoint must remain removed.'
);

tourfic_request_mutation_security_assert(
	false === strpos( $api_keys_source, "add_filter( 'determine_current_user'" )
		&& false !== strpos( $api_keys_source, "add_filter( 'rest_authentication_errors'" )
		&& false !== strpos( $api_keys_source, "defined( 'REST_REQUEST' )" )
		&& false !== strpos( $api_keys_source, "get_query_var( 'rest_route' )" ),
	'API-key authentication must run only during an actual REST dispatch.'
);

echo "Tourfic privileged request mutation regression checks passed.\n";
