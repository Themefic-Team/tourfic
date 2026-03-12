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

$rendered = false;

/**
 * Keep the real frontend context:
 * - archive pages stay archive pages
 * - taxonomy pages stay taxonomy pages
 * - single preview can use tf_preview_post_id
 *
 * Only if we're directly viewing a template builder post without preview context,
 * then fallback to that template post as global $post.
 */
global $post;

if ( isset( $_GET['tf_preview_post_id'] ) ) {
    $preview_post_id = absint( wp_unslash( $_GET['tf_preview_post_id'] ) );
    $preview_post    = $preview_post_id ? get_post( $preview_post_id ) : null;

    if ( $preview_post && ! is_wp_error( $preview_post ) ) {
        $post = $preview_post;
        setup_postdata( $post );
    }
} elseif ( is_singular( 'tf_template_builder' ) && ! is_post_type_archive() && ! is_tax() ) {
    $post = $template_post;
    setup_postdata( $post );
}

if ( class_exists( '\Bricks\Helpers' ) && method_exists( '\Bricks\Helpers', 'get_bricks_data' ) ) {
    $bricks_data = \Bricks\Helpers::get_bricks_data( $template_post->ID, 'content' );

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

wp_reset_postdata();

get_footer();