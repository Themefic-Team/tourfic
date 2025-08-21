<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;
if($highlights){ ?>
<!-- Tour Highlights  -->
<div class="tf-highlights-wrapper tf-mb-56 tf-box tf-template-section">
    <div class="tf-highlights-inner tf-flex">
        <div class="tf-highlights-icon">
            <?php if ( ! empty( $meta['hightlights_thumbnail'] ) ): ?>
                <img src="<?php echo esc_url( $meta['hightlights_thumbnail'] ); ?>" alt="<?php esc_html_e( 'Highlights Icon', 'tourfic' ); ?>" />
            <?php else: ?>
                <img src="<?php echo esc_url(TF_ASSETS_APP_URL).'images/tour-highlights.png' ?>" alt="<?php esc_html_e( 'Highlights Icon', 'tourfic' ); ?>" />
            <?php endif; ?>
        </div>
        <div class="ft-highlights-details">
            <h2 class="tf-section-title"><?php echo !empty($meta['highlights-section-title']) ? esc_html($meta['highlights-section-title']) : ''; ?></h2>
            <div class="highlights-list">
            <p><?php echo wp_kses_post($highlights); ?></p>
            </div>
        </div>
    </div>
</div>
<?php } ?>
