<?php if ( $tc ) { ?>
<!-- Tourfic Hotel Terms and conditions -->
<div class="tf-toc-wrapper tf-mb-50 tf-template-section">
    <div class="tf-section-head">
        <h2 class="tf-title tf-section-title"><?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : ''; ?></h2>
        <?php echo wp_kses_post(wpautop( $tc )); ?>
    </div>
</div>
<?php } ?>