<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Shared\Single\Terms_And_Conditions::render(
    [
        'wrapper_open' => '<div class="tf-toc-wrapper tf-mb-50 tf-template-section">',
        'wrapper_close' => '</div>',
    ]
);