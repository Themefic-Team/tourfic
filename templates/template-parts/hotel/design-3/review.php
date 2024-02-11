<?php if ( ! $disable_review_sec == 1 ) { ?>
<div class="tf-review-wrapper tf-mb-50 tf-template-section" id="tf-review">
    <!-- Tourfic review features ratting -->
    <div class="tf-average-review">
        <div class="tf-section-head">
            <h2 class="tf-title tf-section-title"><?php echo !empty($meta['review-section-title']) ? esc_html($meta['review-section-title']) : ''; ?></h2>
        </div>
    </div>
    
    <?php comments_template(); ?>
    
</div>
<?php } ?>