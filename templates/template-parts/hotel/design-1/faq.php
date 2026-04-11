<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

$tf_enquiry_section_status = !empty($meta['h-enquiry-section']) ? $meta['h-enquiry-section'] : "";
if ( $faqs ): ?>
<div class="tf-hotel-faqs-section tf-mb-50 tf-template-section">
    <h2 class="tf-title tf-section-title" ><?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : ''; ?></h2>
    <div class="tf-section-flex tf-flex">
        <?php \Tourfic\App\Templates\Components\Global\Single\Enquiry::render([
            'wrapper_open' => '<div class="tf-hotel-enquiry">',
            'wrapper_close' => '</div>',
        ]); ?>

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