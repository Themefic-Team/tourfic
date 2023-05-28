<?php if ( $faqs ): ?>
<div class="tf-hotel-faqs-section tf-mb-50">
    <h2 class="tf-title" ><?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : __( "Faq’s", 'tourfic' ); ?></h2>
    <div class="tf-section-flex tf-flex">
        <?php 
        $tf_enquiry_section_status = !empty($meta['h-enquiry-section']) ? $meta['h-enquiry-section'] : "";
        if(!empty($tf_enquiry_section_status)){
        ?>
        <div class="tf-hotel-enquiry">
            <div class="tf-ask-enquiry">
                <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                <h3><?php echo !empty($meta['h-enquiry-option-title']) ? esc_html($meta['h-enquiry-option-title']) : __( "Have a question in mind", 'tourfic' ); ?></h3>
                <p><?php echo !empty($meta['h-enquiry-option-content']) ? esc_html($meta['h-enquiry-option-content']) : __( "Looking for more info? Send a question to the property to find out more.", 'tourfic' ); ?></p>
                <div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="tf-btn-normal btn-primary"><span><?php echo !empty($meta['h-enquiry-option-btn']) ? esc_html($meta['h-enquiry-option-btn']) : __( 'Ask a Question', 'tourfic' ); ?></span></a></div>
            </div>
        </div>
        <?php } ?>
        <div class="tf-hotel-faqs" style="<?php echo empty($tf_enquiry_section_status) ? "flex-basis: 100%;" : ''; ?>">
            <!-- tourfic FAQ -->
            <div class="tf-faq-wrapper">
                <div class="tf-faq-inner">
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
                            <p><?php echo wp_kses_post( $faq['description'] ); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php $faq_key++; endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>