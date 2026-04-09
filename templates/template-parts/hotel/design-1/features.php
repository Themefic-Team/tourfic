<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

echo '<div class="tf-mb-50">';
\Tourfic\App\Templates\Components\Global\Single\Feature::render(['wrapper' => 'no']);
echo '</div>';