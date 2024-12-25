<?php if ( $tc ) { ?>
<div class="tf-toc-wrapper tf-template-section" id="tf-hotel-trams-condition">
    <div class="tf-hotel-toc-title-area tf-section-toggle-icon active">
        <h5 class="tf-section-title" ><?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : ''; ?></h5>
        <i class="ri-arrow-down-s-line tf-toggle-icon-down"></i>
        <i class="ri-arrow-up-s-line tf-toggle-icon-up"></i>
    </div>
    <div class="tf-hotel-toc tf-section-toggle">
        <?php echo wp_kses_post(wpautop( $tc )); ?>
    </div>
</div>
<?php } ?>