<?php
defined('ABSPATH') || exit;

get_header();

$template_post = null;

if ( ! empty( $GLOBALS['tf_bricks_template_id'] ) ) {
    $template_post = get_post( (int) $GLOBALS['tf_bricks_template_id'] );
}

if ( ! $template_post ) {
    global $post;
    $template_post = $post;
}

if ( empty( $template_post ) ) {
    get_footer();
    return;
}

$rendered = false;

// First try official helper.
if ( class_exists( '\Bricks\Helpers' ) && method_exists( '\Bricks\Helpers', 'get_bricks_data' ) ) {
    $bricks_data = \Bricks\Helpers::get_bricks_data( $template_post->ID, 'content' );

    if ( ! empty( $bricks_data ) && class_exists( '\Bricks\Frontend' ) && method_exists( '\Bricks\Frontend', 'render_content' ) ) {
        \Bricks\Frontend::render_content( $bricks_data );
        $rendered = true;
    }
}

// Fallback to raw Bricks meta.
if ( ! $rendered ) {
    $raw_bricks_data = get_post_meta( $template_post->ID, '_bricks_page_content_2', true );

    if ( ! empty( $raw_bricks_data ) && class_exists( '\Bricks\Frontend' ) && method_exists( '\Bricks\Frontend', 'render_content' ) ) {
        \Bricks\Frontend::render_content( $raw_bricks_data );
        $rendered = true;
    }
}

// Final fallback.
if ( ! $rendered ) {
    echo apply_filters( 'the_content', $template_post->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

get_footer();