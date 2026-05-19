<!-- Tourfic Map -->
<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

if ( $location && $itinerary_map != 1 && ! $itineraries ){
    \Tourfic\App\Templates\Components\Shared\Single\Map::render([
        'wrapper_open' => '<div class="tf-mb-50">',
        'wrapper_close' => '</div>'
    ], '', '500px');
}