<!--Highlights Start -->
<div class="tf-overview-wrapper">
    <div class="tf-highlights-wrapper">
        <div class="tf-highlights-icon">
            <img src="<?php echo TF_ASSETS_APP_URL.'/images/tour-highlights-2.png' ?>" alt="Highlights Icon">
        </div>
        <div class="ft-highlights-details">
            <h2 class="tf-section-title">
            <?php echo !empty($meta['highlights-section-title']) ? esc_html($meta['highlights-section-title']) : __("Highlights","tourfic"); ?>
            </h2>
            <p><?php echo $highlights; ?></p>
        </div>
    </div>
</div>
<!--Highlights End -->