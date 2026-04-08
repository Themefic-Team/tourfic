<!-- Tourfic Map -->
<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

if ( $location && $itinerary_map != 1 && ! $itineraries ): ?>
<div class="tf-mb-50">
    <?php \Tourfic\App\Templates\Components\Global\Single\Map::render('', '', '500px'); ?> 
</div>
<?php endif; ?>