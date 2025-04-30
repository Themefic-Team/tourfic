<?php
/**
 * Elementor Template Wrapper
 */
get_header();
if (have_posts()) {
    while (have_posts()) {
        the_post();
        echo Elementor\Plugin::instance()->frontend->get_builder_content($post->ID);
    }
}
get_footer();