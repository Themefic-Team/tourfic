<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

$tf_enquiry_section_status = !empty($meta['h-enquiry-section']) ? $meta['h-enquiry-section'] : "";
if ( $faqs ): ?>
<div class="tf-hotel-faqs-section tf-mb-50 tf-template-section">
    <h2 class="tf-title tf-section-title" ><?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : ''; ?></h2>
    <div class="tf-section-flex tf-flex">
        <?php \Tourfic\App\Templates\Components\Shared\Single\Enquiry::render([
            'wrapper_open' => '<div class="tf-hotel-enquiry">',
            'wrapper_close' => '</div>',
        ]); ?>

        <div class="tf-hotel-faqs" style="<?php echo empty($tf_enquiry_section_status) ? "flex-basis: 100%;" : ''; ?>">
            <?php \Tourfic\App\Templates\Components\Shared\Single\FAQ::render([
                'wrapper_class' => 'tf-faq-wrapper',
                'show_title' => 'no',
                'show_description' => 'no',
            ]); ?>
        </div>
    </div>
</div>
<?php endif; ?>