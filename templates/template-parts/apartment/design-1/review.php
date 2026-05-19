<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\App\TF_Review;

\Tourfic\App\Templates\Components\Shared\Single\Review::render([
    'review_style' => 'design-2',
    'show_review_states' => 'no',
    'show_reviews' => 'yes',
    'show_review_form' => 'no',
]);