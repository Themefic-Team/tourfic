<!-- Tour itenarary -->
<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Shared\Single\Itinerary::render([
    'wrapper_open' => '<div class="tf-mb-50">',
    'wrapper_close' => '</div>'
]);