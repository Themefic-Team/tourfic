<!-- menu section Start -->

<div class="tf-details-menu">
    <ul>
        <li><a class="tf-hashlink" href="#tf-tour-overview">
            <?php esc_html_e("Overview", "tourfic"); ?>
        </a></li>

        <?php if( !empty( $meta["itinerary"] )) : ?>
            <li><a href="#tf-tour-itinerary">
                <?php esc_html_e("Tour Plan", "tourfic"); ?>
            </a></li>
        <?php endif; ?>

        <?php if( !empty( $meta["faqs"] )) : ?>
            <li><a href="#tf-tour-faq">
                <?php esc_html_e("FAQ's", "tourfic"); ?>
            </a></li>
        <?php endif; ?>

        <?php if(!empty( $meta["terms_conditions"] ) ) : ?>
            <li><a href="#tf-tour-policies">
                <?php esc_html_e("Policies", "tourfic"); ?>
            </a></li>
        <?php endif; ?>

        <?php if(!empty($comments)) : ?>
            <li><a href="#tf-tour-reviews">
                <?php esc_html_e("Reviews", "tourfic"); ?>
            </a></li>
        <?php endif; ?>
    </ul>
</div>
<!-- menu section End -->


<!--Overview Start -->
<div class="tf-overview-wrapper">
    <div class="tf-overview-description">
        <div class="tf-short-description">
            <?php 
            if(strlen(get_the_content()) > 300 ){
                echo wp_kses_post(wp_strip_all_tags(\Tourfic\Classes\Helper::tourfic_character_limit_callback(get_the_content(), 300))) . '<span class="tf-see-description">See more</span>';
            }else{
                the_content(); 
            }
            ?>
        </div>
        <div class="tf-full-description">
            <?php 
                the_content();
                echo '<span class="tf-see-less-description">See less</span>';
            ?>
        </div>
    </div>
</div>
<!--Overview End -->