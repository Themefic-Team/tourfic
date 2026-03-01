<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;
if ( ! $disable_review_sec == 1 ) { ?>
    <div class="tf-review-wrapper tf-mb-50 tf-template-section" id="tf-review">
        <?php if ( get_comments_number() > 0 ) : ?>
            <!-- Tourfic review features ratting -->
            <div class="tf-average-review">
                <div class="tf-section-head">
                    <h2 class="tf-title tf-section-title"><?php echo !empty($meta['review-section-title']) ? esc_html($meta['review-section-title']) : ''; ?></h2>
                </div>
            </div>
        <?php endif; ?>
    <?php comments_template(); ?>
    
</div>
<?php } ?>