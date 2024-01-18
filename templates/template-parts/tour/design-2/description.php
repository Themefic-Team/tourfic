<!-- menu section Start -->
<div class="tf-details-menu">
    <ul>
        <li><a class="tf-hashlink" href="#tf-tour-overview">
            <?php _e("Overview", "tourfic"); ?>
        </a></li>
        <li><a href="#tf-tour-itinerary">
            <?php _e("Tour Plan", "tourfic"); ?>
        </a></li>
        <li><a href="#tf-tour-faq">
            <?php _e("FAQ's", "tourfic"); ?>
        </a></li>
        <li><a href="#tf-tour-policies">
            <?php _e("Policies", "tourfic"); ?>
        </a></li>
        <li><a href="#tf-tour-reviews">
            <?php _e("Reviews", "tourfic"); ?>
        </a></li>
    </ul>
</div>
<!-- menu section End -->


<!--Overview Start -->
<div class="tf-overview-wrapper">
    <div class="tf-overview-description">
        <div class="tf-short-description">
            <?php 
            if(strlen(get_the_content()) > 300 ){
                echo tourfic_character_limit_callback(get_the_content(), 300) .'<span class="tf-see-description">See more</span>';
            }else{
                the_content(); 
            }
            ?>
        </div>
        <div class="tf-full-description">
            <?php 
                echo get_the_content().'<span class="tf-see-less-description"> See less</span>';
            ?>
        </div>
    </div>
</div>
<!--Overview End -->