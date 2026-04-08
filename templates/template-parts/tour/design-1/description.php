<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;
?>
<!-- Single trip description -->
<div class="tf-trip-description tf-mb-56 tf-template-section">
    <?php \Tourfic\App\Templates\Components\Global\Single\Description::render(['limit_content' => 'no']); ?>
</div>
