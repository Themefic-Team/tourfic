<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Global\Single\FAQ::render([
    'wrapper_class' => 'tf-faq-wrapper tf-mb-50',
    'label_tag' => 'h4',
]);