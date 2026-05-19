<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Shared\Single\FAQ::render([
    'wrapper_open' => '<div class="tf-questions-wrapper tf-section" id="tf-apartment-faq">',
    'wrapper_close' => '</div>',
    'show_description' => 'no',
    'wrapper' => 'no',
    'faq_style' => 'style2',
]);