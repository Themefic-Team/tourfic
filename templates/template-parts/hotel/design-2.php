<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
?>

<div class="tf-single-template__two">
<!--Hero section start -->
<div class="tf-hero-section-wrap" style="<?php echo !empty(get_the_post_thumbnail_url()) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url(get_the_post_thumbnail_url()).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
    <div class="tf-container">
        <div class="tf-hero-content">
            <div class="tf-wish-and-share">
                <?php \Tourfic\App\Templates\Components\Global\Single\Wishlist::render(['design' => 'design-2']); ?>
                <?php \Tourfic\App\Templates\Components\Global\Single\Share::render(['share_style' => 'style2']); ?>
            </div>
            <div class="tf-hero-bottom-area">
                <div class="tf-head-title">
                    <?php \Tourfic\App\Templates\Components\Global\Single\Title::render(); ?>
                    <?php \Tourfic\App\Templates\Components\Global\Single\Address::render(['design' => 'design-2']); ?>
                </div>
                <div class="tf-hero-gallery-videos">
                    <?php \Tourfic\App\Templates\Components\Global\Single\Video_Button::render(['design' => 'design-2'], '', false); ?>
                    <?php \Tourfic\App\Templates\Components\Global\Single\Gallery_Button::render(['style' => 'style2']); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Hero section End -->


<!--Content section end -->
<div class="tf-content-wrapper tf-single-pb-56">
    
    <div class="tf-container">
    
    <!-- Hotel details Srart -->
    <div class="tf-details" id="tf-hotel-overview">
        <div class="tf-details-left">
            <?php \Tourfic\App\Templates\Components\Global\Single\Sticky_Nav::render(); ?>

            <?php 
            if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout-part-1']) ){
                foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout-part-1'] as $section){
                    if( !empty($section['status']) && $section['status']=="1" && !empty($section['slug']) ){
                        include TF_TEMPLATE_PART_PATH . 'hotel/design-2/'.$section['slug'].'.php';
                    }
                }
            }else{
                include TF_TEMPLATE_PART_PATH . 'hotel/design-2/description.php';
                include TF_TEMPLATE_PART_PATH . 'hotel/design-2/features.php';
                include TF_TEMPLATE_PART_PATH . 'hotel/design-2/rooms.php';
            }
            ?>
        </div>
        <div class="tf-details-right tf-sitebar-widgets">
            <?php
            \Tourfic\App\Templates\Components\Global\Single\Nearby_Places::render([
                'nearby_places_style' => 'style2',
                'wrapper_open' => '<div class="tf-single-widgets">', 
                'wrapper_close' => '</div>'
            ]);
            ?>
            
            <div id="hotel-map-location" class="tf-location tf-single-widgets">
                <h3 class="tf-section-title"><?php esc_html_e("Location", "tourfic"); ?></h3>
                <?php \Tourfic\App\Templates\Components\Global\Single\Map::render(['show_icon' => 'no']); ?>
            </div>
            
            <div class="tf-location tf-single-widgets">
                <?php
                if($disable_review_sec != 1) :
                global $current_user;
                // Check if user is logged in
                $is_user_logged_in = $current_user->exists();
                $post_id           = $post->ID;
                // Get settings value
                $tf_ratings_for = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
                $tf_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
                if ( $comments ) {
                    $tf_overall_rate        = [];
                    TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
                    TF_Review::tf_get_review_fields( $fields );
                ?>
                <h2 class="tf-section-title"><?php esc_html_e("Overall reviews", "tourfic"); ?></h2>
                <div class="tf-review-data-inner">
                    <div class="tf-review-data">
                        <div class="tf-review-data-average">
                            <span class="avg-review"><span>
                                <?php echo esc_html(sprintf( '%.1f', $total_rating )); ?>
                            </span>/ <?php echo wp_kses_post($tf_settings_base); ?></span>
                        </div>
                        <div class="tf-review-all-info">
                            <p><?php esc_html_e("Excellent", "tourfic"); ?> <span><?php esc_html_e("Total ", "tourfic"); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span></p>
                        </div>
                    </div>
                    <div class="tf-review-data-features">
                        <div class="tf-percent-progress">
                        <?php 
                        if ( $tf_overall_rate ) {
                        foreach ( $tf_overall_rate as $key => $value ) {
                        if ( empty( $value ) || ! in_array( $key, $fields ) ) {
                            continue;
                        }
                        $value = TF_Review::tf_average_ratings( $value );
                        ?>
                            <div class="tf-progress-item">                                    
                                <div class="tf-review-feature-label">
                                    <p class="feature-label"><?php echo esc_html( $key ); ?></p>
                                    <p class="feature-rating"> <?php echo wp_kses_post($value); ?></p>
                                </div>
                                <div class="tf-progress-bar">
                                    <span class="percent-progress" style="width: <?php echo wp_kses_post( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) )); ?>%"></span>
                                </div>
                            </div>
                            <?php } } ?>
                                   
                        </div>
                    </div>
                </div>
                <a class="tf-all-reviews" href="#tf-hotel-reviews"><?php esc_html_e("See all reviews", "tourfic"); ?></a>
                <?php } ?>

                <?php
                $tf_comment_counts = get_comments( array(
                    'post_id' => $post_id,
                    'user_id' => $current_user->ID,
                    'count'   => true,
                ) );
                ?>
                <?php if( empty($tf_comment_counts) && $tf_comment_counts == 0 ) : ?>
                    <button class="tf_btn tf_btn_secondary tf_btn_full tf_btn_sharp tf_btn_large tf-review-open">
                    <?php esc_html_e("Leave your review", "tourfic"); ?>
                </button>
                <?php endif; ?>
                <?php
                // Review moderation notice
                echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '');
                ?>
                <?php
                if ( ! empty( $tf_ratings_for ) ) {
                    if ( $is_user_logged_in ) {
                    if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
                    ?>
                <div class="tf-review-form-wrapper" action="">
                    <h3><?php esc_html_e("Leave your review", "tourfic"); ?></h3>
                    <p><?php esc_html_e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                    <?php TF_Review::tf_review_form(); ?>
                </div>
                <?php
		            }
	            } else {
		        if ( in_array( 'lo', $tf_ratings_for ) ) {
			    ?>
                <div class="tf-review-form-wrapper" action="">
                    <h3><?php esc_html_e("Leave your review", "tourfic"); ?></h3>
                    <p><?php esc_html_e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                    <?php TF_Review::tf_review_form(); ?>
                </div>
                <?php } } } ?>
                <?php endif; ?>
            </div>
            
            <?php
            \Tourfic\App\Templates\Components\Global\Single\Enquiry::render([
                'icon_type' => 'simple',
                'wrapper_class' => 'tf-send-inquiry tf-single-widgets',
                'button_class' => 'tf_btn_large tf_btn_sharp',
            ]);
            ?>
            
            <!-- Hotel Single Widget Hook are - start -->
            <div class="tf-hotel-single-custom-widget-wrap">
                <?php do_action( "tf_hotel_single_widgets" ); ?>
                <?php do_action( "tf_single_hotel_sidebar_area_with_args", $post_id ); ?>
            </div>       
            <!-- Hotel Single Widget Hook are - end -->
        </div>        
    </div>        
    <!-- Hotel details End -->
    
    <?php 
    if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout-part-2']) ){
        foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout-part-2'] as $section){
            if( !empty($section['status']) && $section['status']=="1" && !empty($section['slug']) ){
                include TF_TEMPLATE_PART_PATH . 'hotel/design-2/'.$section['slug'].'.php';
            }
        }
    }else{
        include TF_TEMPLATE_PART_PATH . 'hotel/design-2/facilities.php';
        include TF_TEMPLATE_PART_PATH . 'hotel/design-2/review.php';
        include TF_TEMPLATE_PART_PATH . 'hotel/design-2/faq.php';
        include TF_TEMPLATE_PART_PATH . 'hotel/design-2/trams-condition.php';
    }
    ?>

    <?php 
    if ( ! empty( $gallery_ids ) ) {
    ?>
    <!-- Hotel PopUp Starts -->       
    <div class="tf-popup-wrapper tf-hotel-popup">
        <div class="tf-popup-inner">
            <div class="tf-popup-body">
                <?php 
                    if ( ! empty( $gallery_ids ) ) {
                    foreach ( $gallery_ids as $key => $gallery_item_id ) {
                    $image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
                ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="" class="tf-popup-image">
                <?php } } ?>
            </div>                
            <div class="tf-popup-close">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
    </div>
    <!-- Hotel PopUp end -->  
    <?php } ?>


    <!-- Room PopUp Starts -->        
    <div class="tf-popup-wrapper tf-room-popup"></div>
    <!-- Room PopUp end --> 


    </div>
</div>
<!--Content section end -->
</div>