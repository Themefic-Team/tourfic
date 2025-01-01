<?php if(!empty($faqs)){ ?>
<div class="tf-car-faq-section" id="tf-faq">
    <?php if(!empty($faq_sec_title)){ ?>   
    <h3><?php echo esc_html($faq_sec_title); ?></h3>
    <?php } ?>

    <?php foreach($faqs as $singlefaq){ ?>
    <div class="tf-faq-col">
        <?php if(!empty($singlefaq['title'])){ ?>
        <div class="tf-faq-head">
            <span class="tf-flex tf-flex-space-bttn tf-flex-align-center">
            <?php echo esc_html($singlefaq['title']); ?>
            <i class="fa-solid fa-chevron-down"></i>
            </span>
        </div>
        <?php } ?>
        <?php if(!empty($singlefaq['description'])){ ?>
        <div class="tf-question-desc">
            <?php echo wp_kses_post($singlefaq['description']); ?>
        </div>
        <?php } ?>
    </div>
    <?php } ?>

</div>
<?php } ?>