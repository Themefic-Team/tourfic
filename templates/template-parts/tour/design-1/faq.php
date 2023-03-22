
<!-- tourfic FAQ -->
<div class="tf-faq-wrapper tf-mrtop-70">
    <h2 class="tf-title" ><?php _e( "Frequently Asked Questions", 'tourfic' ); ?></h2>
    <div class="tf-faq-inner tf-mrtop-30">
        <?php if ( $faqs ): ?>
            <?php foreach ( $faqs as $key => $faq ): ?>
            <div class="tf-faq-single">
                <div class="tf-faq-single-inner">
                    <div class="tf-faq-collaps tf-flex tf-flex-align-center tf-flex-space-bttn">
                        <h3><?php echo esc_html( $faq['title'] ); ?></h3> 
                        <div class="faq-icon"><i class="fa-solid fa-plus"></i><i class="fa-solid fa-minus"></i></div>
                    </div>
                    <div class="tf-faq-content tf-mrtop-24">
                        <?php echo wp_kses_post( $faq['desc'] ); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>