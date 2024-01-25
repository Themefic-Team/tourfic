<?php 
$tc = !empty($meta['terms_and_conditions']) ? $meta['terms_and_conditions'] : '';
if ( $tc ) { ?>
<!-- apartment Policies Starts -->        
<div class="tf-policies-wrapper tf-section" id="tf-apartment-policies">            
    <h2 class="tf-section-title">
        <?php echo !empty($meta['tc-section-title']) ? esc_html($meta['tc-section-title']) : __("Hotel Terms & Conditions","tourfic"); ?>
    </h2>  
    <div class="tf-policies">
        <?php echo wpautop( $tc ); ?>
    </div>
</div>
<!-- apartment Policies end -->
<?php } ?>