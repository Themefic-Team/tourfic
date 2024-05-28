<?php 

use \Tourfic\Classes\Helper;

$faqs = !empty($meta['faq']) ? Helper::tf_data_types($meta['faq']) : '';
if ( $faqs ): ?>
<!-- apartment Questions Srart -->
<div class="tf-questions-wrapper tf-section" id="tf-apartment-faq">
    <h2 class="tf-section-title">
    <?php echo ! empty( $meta['faq_title'] ) ? esc_html( $meta['faq_title'] ) : ''; ?>
    </h2>            
    <div class="tf-questions">
        
        <?php 
        $faqs_itemsPerColumn = ceil(count($faqs) / 2);
        ?>
        <div class="tf-questions-col">
            <?php 
            for ($i = 0; $i < $faqs_itemsPerColumn; $i++) { ?>
            <div class="tf-question <?php echo $i==0 ? esc_attr( 'tf-active' ) : ''; ?>">
                <div class="tf-faq-head">
                    <span><?php echo esc_html( $faqs[$i]['title'] ); ?>
                    <i class="fa-solid fa-chevron-down"></i></span>
                </div>
                <div class="tf-question-desc" style="<?php echo $i==0 ? esc_attr( 'display: block;' ) : ''; ?>">
                <?php echo wp_kses_post( $faqs[$i]['description'] ); ?>
                </div>
            </div>
            <?php } ?>
            
        </div>
        <div class="tf-questions-col">
            <?php 
            for ($i = $faqs_itemsPerColumn; $i < count($faqs); $i++) { ?>
            <div class="tf-question">
                <div class="tf-faq-head">
                    <span><?php echo esc_html( $faqs[$i]['title'] ); ?>
                    <i class="fa-solid fa-chevron-down"></i></span>
                </div>
                <div class="tf-question-desc">
                <?php echo wp_kses_post( $faqs[$i]['description'] ); ?>
                </div>
            </div>
            <?php } ?>
            
        </div>
    </div>
</div>

<!-- Hotel Questions end -->
<?php endif; ?>