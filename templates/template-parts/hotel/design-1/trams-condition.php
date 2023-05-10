<?php if ( $tc ) { ?>
<!-- Tourfic Hotel Terms and conditions -->
<div class="tf-toc-wrapper tf-mrbottom-70">
    <div class="tf-section-head">
        <h2 class="tf-title"><?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : __("Hotel Terms & Conditions","tourfic"); ?></h2>
        <?php echo wpautop( $tc ); ?>
    </div>
</div>
<?php } ?>