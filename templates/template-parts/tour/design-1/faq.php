<?php if ( $faqs ): ?>
<!-- tourfic FAQ -->
<div class="tf-faq-wrapper tf-mrbottom-70">
    <h2 class="tf-title" ><?php _e( "Frequently Asked Questions", 'tourfic' ); ?></h2>
    <div class="tf-faq-inner tf-mrtop-30">
        <?php 
        $faq_key = 1;    
        foreach ( $faqs as $key => $faq ): ?>
        <div class="tf-faq-single <?php echo $faq_key==1 ? esc_attr( 'active' ) : ''; ?>">
            <div class="tf-faq-single-inner">
                <div class="tf-faq-collaps tf-flex tf-flex-align-center tf-flex-space-bttn <?php echo $faq_key==1 ? esc_attr( 'active' ) : ''; ?>">
                    <h3><?php echo esc_html( $faq['title'] ); ?></h3> 
                    <div class="faq-icon"><i class="fa-solid fa-plus"></i><i class="fa-solid fa-minus"></i></div>
                </div>
                <div class="tf-faq-content tf-mrtop-24">
                    <?php echo wp_kses_post( $faq['desc'] ); ?>
                </div>
            </div>
        </div>
        <?php $faq_key++; endforeach; ?>
    </div>
</div>
<?php endif; ?>