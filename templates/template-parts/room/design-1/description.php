<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;
?>
<!--Overview Start -->
<div class="tf-overview-wrapper">
    <h2 class="tf-title tf-section-title"><?php echo esc_html__( 'Description', 'tourfic'); ?></h2>
    <div class="tf-overview-description">
        <?php \Tourfic\App\Templates\Components\Shared\Single\Description::render(); ?>
    </div>
</div>
<!--Overview End -->