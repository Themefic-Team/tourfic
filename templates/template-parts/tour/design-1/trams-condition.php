<?php
if($terms_and_conditions){ ?>
<!-- Tourfic tour Terms and conditions -->
<div class="tf-toc-wrapper tf-mb-50 tf-template-section">
    <div class="tf-section-head">
        <h2 class="tf-title tf-section-title"><?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : __("Tour Terms & Conditions","tourfic"); ?></h2>
        <?php echo wpautop( $terms_and_conditions ); ?>
    </div>
</div>
<?php } ?>