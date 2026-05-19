<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Shared\Single\FAQ::render([
    'wrapper_open' => '<div class="tf-car-faq-section" id="tf-faq">',
    'wrapper_close' => '</div>',
    'wrapper' => 'no',
    'faq_style' => 'style3',
]);