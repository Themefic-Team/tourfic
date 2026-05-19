<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Shared\Single\Gallery::render([
    'gallery_style' => 'style1',
    'wrapper_open' => '<div class="tf-mb-30">',
    'wrapper_close' => '</div>',
]);