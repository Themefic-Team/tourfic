<?php 

use \Tourfic\Classes\Helper;

$faqs = !empty($meta['faq']) ? Helper::tf_data_types($meta['faq']) : '';
if ( $faqs ): ?>
<div class="tf-questions-wrapper tf-section" id="tf-apartment-faq">
    <h2 class="tf-section-title">
    <?php echo ! empty( $meta['faq_title'] ) ? esc_html( $meta['faq_title'] ) : ''; ?>
    </h2>            
    <div class="tf-questions">
        
        <?php 
        if (count($faqs) >= 2) {
            $faqchunks = array_chunk($faqs, ceil(count($faqs) / 2), true);
            $faqfirstArray = $faqchunks[0];
            $faqsecondArray = $faqchunks[1];
            
        }else{
            $faqfirstArray = $faqs;
        }
        ?>
        <?php if(!empty($faqfirstArray)){ ?>
        <div class="tf-questions-col">
            <?php foreach ($faqfirstArray as $key => $faq) {
                ?>
            <div class="tf-question <?php echo $key == 1 ? 'tf-active' : ''; ?>">
                <div class="tf-faq-head">
                    <span><?php echo esc_html( $faq['title'] ); ?>
                    <i class="fa-solid fa-chevron-down"></i></span>
                </div>
                <div class="tf-question-desc" style="<?php echo $key == 1 ? 'display: block;' : ''; ?>">
                <?php echo wp_kses_post( $faq['description'] ); ?>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } if(!empty($faqsecondArray)){ ?>
        <div class="tf-questions-col">
            <?php foreach ($faqsecondArray as $key => $faq) { ?>
            <div class="tf-question <?php echo $key == 1 ? 'tf-active' : ''; ?>">
                <div class="tf-faq-head">
                    <span><?php echo esc_html( $faq['title'] ); ?>
                    <i class="fa-solid fa-chevron-down"></i></span>
                </div>
                <div class="tf-question-desc" style="<?php echo $key == 1 ? 'display: block;' : ''; ?>">
                <?php echo wp_kses_post( $faq['description'] ); ?>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
    </div>
</div>

<?php endif; ?>