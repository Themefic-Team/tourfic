<!-- Trip Features -->
<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Tour\Single\Tour_Info_Cards::render([
    'wrapper_open' => '<div class="tf-mb-56">', 
    'wrapper_close' => '</div>'
]); 

if( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
    \Tourfic\App\Templates\Components\Shared\Single\Feature::render([
        'wrapper' => 'no',
        'wrapper_open' => '<div class="tf-mb-40">', 
        'wrapper_close' => '</div>'
    ]); 
}        
?>
