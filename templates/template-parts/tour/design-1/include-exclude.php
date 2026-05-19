<!-- Tour include exclude -->
<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Shared\Single\Included_Excluded::render([
    'wrapper_open' => '<div class="tf-mb-50">', 
    'wrapper_close' => '</div>'
]); 