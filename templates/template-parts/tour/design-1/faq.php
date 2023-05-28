<?php if ( $faqs ): ?>
<!-- tourfic FAQ -->
<div class="tf-faq-wrapper tf-mb-50">
    <h2 class="tf-title" ><?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : __("Frequently Asked Questions","tourfic"); ?></h2>
    <div class="tf-faq-inner tf-mt-30">
        <?php 
        $faq_key = 1;    
        foreach ( $faqs as $key => $faq ): ?>
        <div class="tf-faq-single <?php echo $faq_key==1 ? esc_attr( 'active' ) : ''; ?>">
            <div class="tf-faq-single-inner">
                <div class="tf-faq-collaps tf-flex tf-flex-align-center tf-flex-space-bttn <?php echo $faq_key==1 ? esc_attr( 'active' ) : ''; ?>">
                    <h4><?php echo esc_html( $faq['title'] ); ?></h4> 
                    <div class="faq-icon"><i class="fa-solid fa-plus"></i><i class="fa-solid fa-minus"></i></div>
                </div>
                <div class="tf-faq-content tf-mt-24">
                    <p><?php echo wp_kses_post( $faq['desc'] ); ?></p>
                </div>
            </div>
        </div>
        <?php $faq_key++; endforeach; ?>
    </div>
</div>
<?php endif; ?>