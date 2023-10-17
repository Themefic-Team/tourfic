<?php if ( $tc ) { ?>
<!-- Hotel Policies Starts -->        
<div class="tf-policies-wrapper tf-section" id="tf-hotel-policies">            
    <h2 class="tf-section-title">
        <?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : __("Hotel Terms & Conditions","tourfic"); ?>
    </h2>  
    <div class="tf-policies">
        <?php echo wpautop( $tc ); ?>
    </div>
</div>
<!-- Hotel Policies end -->
<?php } ?>