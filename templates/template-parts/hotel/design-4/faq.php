<?php if ( $faqs ): ?>
    <div class="tf-hotel-faqs-section tf-template-section" id="tf-hotel-faq">
        <div class="tf-hotel-faq-title-area tf-section-toggle-icon active">
            <h5 class="tf-section-title" ><?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : ''; ?></h5>
            <i class="ri-arrow-down-s-line tf-toggle-icon-down"></i>
            <i class="ri-arrow-up-s-line tf-toggle-icon-up"></i>
        </div>
        <div class="tf-hotel-faqs tf-section-toggle" style="<?php echo empty( $tf_enquiry_section_status ) ? "flex-basis: 100%;" : ''; ?>">
			<?php foreach ( $faqs as $key => $faq ): ?>
                <div class="tf-faq-single <?php echo $key == 0 ? esc_attr( 'active' ) : ''; ?>">
                    <div class="tf-faq-collaps <?php echo $key == 0 ? esc_attr( 'active' ) : ''; ?>">
                        <span class="title"><?php echo esc_html( $faq['title'] ); ?></span>
                        <div class="faq-icon"><i class="fa-solid fa-plus"></i><i class="fa-solid fa-minus"></i></div>
                    </div>
                    <div class="tf-faq-content" style="<?php echo $key == 0 ? esc_attr( 'display: block;' ) : ''; ?>">
                        <p><?php echo wp_kses_post( $faq['description'] ); ?></p>
                    </div>
                </div>
			<?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>