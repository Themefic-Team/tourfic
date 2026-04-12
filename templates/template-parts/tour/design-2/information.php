<?php // Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Tour\Single\Tour_Info_Cards::render([
    'grid_column' => 3,
    'wrapper_open' => '<div class="tf-overview-wrapper"><div class="tf-features-block-wrapper tf-informations-secations">', 
    'wrapper_close' => '</div></div>'
]); 