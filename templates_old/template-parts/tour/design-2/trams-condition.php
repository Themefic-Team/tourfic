<?php
if($terms_and_conditions){ ?>
<!-- Hotel Policies Starts -->
<div class="tf-policies-wrapper tf-section" id="tf-tour-policies">
    <h2 class="tf-section-title">
    <?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : __("Tour Terms & Conditions","tourfic"); ?>
    </h2>
    <div class="tf-policies">
        <?php echo wpautop( $terms_and_conditions ); ?>
    </div>
</div>
<!-- Hotel Policies end -->
<?php } ?>