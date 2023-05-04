<?php if ( $faqs ): ?>
<div class="tf-hotel-faqs-section tf-mrbottom-70">
    <h2 class="tf-title" ><?php _e( "Faq’s", 'tourfic' ); ?></h2>
    <div class="tf-section-flex tf-flex">
        <div class="tf-hotel-enquiry">
            <div class="tf-ask-enquiry">
                <i class="fa fa-question-circle-o" aria-hidden="true"></i>
                <h3><?php _e( "Have a question in mind", 'tourfic' ); ?></h3>
                <p><?php _e( "Looking for more info? Send a question to the property to find out more.", 'tourfic' ); ?></p>
                <div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="tf-bttn-normal bttn-primary"><span><?php esc_html_e( 'Ask a Question', 'tourfic' ); ?></span></a></div>
            </div>
        </div>
        <div class="tf-hotel-faqs">
            <!-- tourfic FAQ -->
            <div class="tf-faq-wrapper">
                <div class="tf-faq-inner">
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