<!-- Tourfic Map -->
<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

if ( $tourfic_location && $tourfic_itinerary_map != 1 && ! $tourfic_itineraries ){
    \Tourfic\App\Templates\Components\Shared\Single\Map::render([
        'wrapper_open' => '<div class="tf-mb-50">',
        'wrapper_close' => '</div>'
    ], '', '500px');
}