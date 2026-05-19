<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

\Tourfic\App\Templates\Components\Shared\Single\FAQ::render([
    'wrapper_class' => 'tf-faq-wrapper tf-mb-50',
    'label_tag' => 'h4',
]);