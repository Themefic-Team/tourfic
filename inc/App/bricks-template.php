<?php
defined( 'ABSPATH' ) || exit;

get_header();

$template_post = null;

if ( ! empty( $GLOBALS['tf_bricks_template_id'] ) ) {
	$template_post = get_post( (int) $GLOBALS['tf_bricks_template_id'] );
}

if ( ! $template_post ) {
	$template_post = get_queried_object();
}

if ( empty( $template_post ) || empty( $template_post->ID ) ) {
	get_footer();
	return;
}

global $post, $wp_query;

$original_post = $post;

/**
 * Resolve the real frontend context post.
 *
 * Priority:
 * 1. Explicit preview post for builder preview
 * 2. Real queried singular service post
 * 3. Fallback to current global/queried object
 */
$context_post = null;

if ( isset( $_GET['tf_preview_post_id'] ) ) {
	$preview_post_id = absint( wp_unslash( $_GET['tf_preview_post_id'] ) );
	if ( $preview_post_id ) {
		$context_post = get_post( $preview_post_id );
	}
}

if ( ! $context_post && is_singular() && ! is_singular( 'tf_template_builder' ) ) {
	$queried_id = get_queried_object_id();
	if ( $queried_id ) {
		$context_post = get_post( $queried_id );
	}
}

if ( ! $context_post && is_object( get_queried_object() ) && ! empty( get_queried_object()->ID ) ) {
	$context_post = get_post( get_queried_object()->ID );
}

/**
 * Keep the real frontend/service post as the active global post
 * so widgets like get_the_title(), get_post_type(), get_the_ID()
 * point to the service post instead of tf_template_builder.
 */
if ( $context_post && ! is_wp_error( $context_post ) ) {
	$post = $context_post;
	setup_postdata( $post );

	if ( $wp_query instanceof \WP_Query ) {
		$wp_query->post = $context_post;
	}
}

$rendered = false;

if ( class_exists( '\Bricks\Helpers' ) && method_exists( '\Bricks\Helpers', 'get_bricks_data' ) ) {

	/**
	 * Temporarily switch to template post so Bricks can properly
	 * load the template structure/settings from the template post.
	 */
	$temp_post = $post;
	$post      = $template_post;
	setup_postdata( $post );

	$bricks_data = \Bricks\Helpers::get_bricks_data( $template_post->ID, 'content' );

	/**
	 * Restore real frontend context before actual rendering,
	 * so widgets inside the template read the service post context.
	 */
	if ( $context_post && ! is_wp_error( $context_post ) ) {
		$post = $context_post;
		setup_postdata( $post );

		if ( $wp_query instanceof \WP_Query ) {
			$wp_query->post = $context_post;
		}
	} else {
		$post = $temp_post;
		if ( $post ) {
			setup_postdata( $post );
		}
	}

	if ( ! empty( $bricks_data ) && class_exists( '\Bricks\Frontend' ) && method_exists( '\Bricks\Frontend', 'render_content' ) ) {
		\Bricks\Frontend::$area = 'content';
		echo \Bricks\Frontend::render_content( $bricks_data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$rendered = true;
	}
}

if ( ! $rendered ) {
	$raw_bricks_data = get_post_meta( $template_post->ID, '_bricks_page_content_2', true );

	if ( ! empty( $raw_bricks_data ) && class_exists( '\Bricks\Frontend' ) && method_exists( '\Bricks\Frontend', 'render_content' ) ) {
		\Bricks\Frontend::$area = 'content';
		echo \Bricks\Frontend::render_content( $raw_bricks_data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$rendered = true;
	}
}

if ( ! $rendered ) {
	echo apply_filters( 'the_content', $template_post->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Restore original global post.
 */
wp_reset_postdata();

$post = $original_post;
if ( $post ) {
	setup_postdata( $post );
}

get_footer();