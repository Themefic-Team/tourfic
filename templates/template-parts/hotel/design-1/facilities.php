<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

echo '<div class="tf-mb-50">';
\Tourfic\App\Templates\Components\Global\Single\Amenities::render(['amenities_style' => 'style2']);
echo '</div>';