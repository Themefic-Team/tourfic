<?php if ( $faqs ): ?>
<div class="tf-hotel-faqs-section tf-mb-50 tf-template-section">
    <h2 class="tf-title tf-section-title" ><?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : ''; ?></h2>
    <div class="tf-section-flex tf-flex">
        <?php 
        $tf_enquiry_section_status = !empty($meta['h-enquiry-section']) ? $meta['h-enquiry-section'] : "";
        $tf_enquiry_section_icon = !empty($meta['h-enquiry-option-icon']) ? esc_html($meta['h-enquiry-option-icon']) : '';
        $tf_enquiry_section_title = !empty($meta['h-enquiry-option-title']) ? esc_html($meta['h-enquiry-option-title']) : '';
        $tf_enquiry_section_cont = !empty($meta['h-enquiry-option-content']) ? esc_html($meta['h-enquiry-option-content']) : '';
        $tf_enquiry_section_button = !empty($meta['h-enquiry-option-btn']) ? esc_html($meta['h-enquiry-option-btn']) : '';
        if(!empty($tf_enquiry_section_status) && ( !empty($tf_enquiry_section_icon) || !empty($tf_enquiry_section_title) || !empty($enquery_button_text))){
        ?>
        <div class="tf-hotel-enquiry">
            <div class="tf-ask-enquiry">
                <?php 
                if (!empty($tf_enquiry_section_icon)) {
                    ?>
                    <i class="<?php echo esc_attr($tf_enquiry_section_icon); ?>" aria-hidden="true"></i>
                    <?php
                }
                if(!empty($tf_enquiry_section_title)) {
                    ?>
                    <h3><?php echo wp_kses_post($tf_enquiry_section_title); ?></h3>
                    <?php
                }
                if(!empty($tf_enquiry_section_cont)) {
                    ?>
                     <p><?php echo wp_kses_post($tf_enquiry_section_cont);  ?></p>
                    <?php
                }
                if( !empty( $tf_enquiry_section_button )) {
                    ?>
                    <div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="tf-btn-normal btn-primary"><span><?php echo esc_html($tf_enquiry_section_button); ?></span></a></div>
                    <?php
                }
                ?>
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
                            <div class="tf-faq-content" style="<?php echo $faq_key==1 ? esc_attr( 'display: block;' ) : ''; ?>">
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