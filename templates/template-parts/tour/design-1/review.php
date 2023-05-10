<?php if ( ! $disable_review_sec == 1 ) { ?>
<div class="tf-review-wrapper tf-mrbottom-70">
    <!-- Tourfic review features ratting -->
    <div class="tf-average-review">
        <div class="tf-section-head">
            <h2 class="tf-title"><?php echo !empty($meta['review-section-title']) ? esc_html($meta['review-section-title']) : __("Average Guest Reviews","tourfic"); ?></h2>
        </div>
    </div>
    
    <?php comments_template(); ?>
    
</div>
<?php } ?>