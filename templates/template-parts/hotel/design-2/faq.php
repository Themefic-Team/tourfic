<?php if ( $faqs ): ?>
<!-- Hotel Questions Srart -->
<div class="tf-questions-wrapper tf-section" id="tf-hotel-faq">
    <h2 class="tf-section-title">
    <?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : __( "Faq’s", 'tourfic' ); ?>
    </h2>            
    <div class="tf-questions">
        <div class="tf-questions-col">
            <?php 
            $faq_key = 1;    
            foreach ( $faqs as $key => $faq ): ?>
            <div class="tf-question <?php echo $faq_key==1 ? esc_attr( 'tf-active' ) : ''; ?>">
                <div class="tf-faq-head">
                    <h3><?php echo esc_html( $faq['title'] ); ?>
                    <i class="fa-solid fa-chevron-down"></i></h3>
                </div>
                <div class="tf-question-desc" style="<?php echo $faq_key==1 ? esc_attr( 'display: block;' ) : ''; ?>">
                    <?php echo wp_kses_post( $faq['description'] ); ?>
                </div>
            </div>
            <?php $faq_key++; endforeach; ?>
            
        </div>
        
    </div>
</div>

<!-- Hotel Questions end -->
<?php endif; ?>