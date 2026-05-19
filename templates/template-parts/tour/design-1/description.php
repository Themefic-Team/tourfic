<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Shared\Single\Description::render([
    'show_title' => 'yes',
    'limit_content' => 'no',
    'wrapper_open' => '<div class="tf-trip-description tf-mb-56 tf-template-section">',
    'wrapper_close' => '</div>'
]);
