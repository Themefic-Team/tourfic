<?php // Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Shared\Single\FAQ::render([
    'wrapper_open' => '<div class="tf-questions-wrapper tf-section" id="tf-tour-faq">',
    'wrapper_close' => '</div>',
    'wrapper' => 'no',
    'faq_style' => 'style2',
]);