<?php 
if($highlights){ ?>
<!-- Tour Highlights  -->
<div class="tf-highlights-wrapper tf-mb-50 tf-box tf-template-section">
    <div class="tf-highlights-inner tf-flex">
        <div class="tf-highlights-icon">
            <img src="<?php echo TF_ASSETS_APP_URL.'/images/tour-highlights.png' ?>" alt="<?php _e( 'Highlights Icon', 'tourfic' ); ?>" />
        </div>
        <div class="ft-highlights-details">
            <h2 class="tf-section-title"><?php echo !empty($meta['highlights-section-title']) ? esc_html($meta['highlights-section-title']) : ''; ?></h2>
            <div class="highlights-list">
            <p><?php echo $highlights; ?></p>
            </div>
        </div>
    </div>
</div>
<?php } ?>