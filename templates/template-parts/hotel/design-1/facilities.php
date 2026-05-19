<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Shared\Single\Amenities::render([
    'amenities_style' => 'style2',
    'wrapper_open' => '<div class="tf-mb-50">',
    'wrapper_close' => '</div>',
]);