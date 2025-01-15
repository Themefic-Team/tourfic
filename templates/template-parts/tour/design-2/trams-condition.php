<?php
if($terms_and_conditions){ ?>
<!-- Tourfic tour Terms and conditions -->
<div class="tf-toc-wrapper tf-mb-50 tf-template-section">
    <h2 class="tf-title tf-section-title"><?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : ''; ?></h2>
    <?php echo wp_kses_post(wpautop( $terms_and_conditions )); ?>
</div>
<?php } ?>