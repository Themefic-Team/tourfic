<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use Tourfic\Classes\Room\Pricing;
use Tourfic\Classes\Room\Room;

$room_option = ! empty( $_GET['room-option'] ) ? sanitize_text_field( $_GET['room-option'] ) : '';
$meta = get_post_meta( get_the_ID(), 'tf_room_opt', true );
$room_options = ! empty( $meta['room-options'] ) ? $meta['room-options'] : [];
$option_title = '';
if ( ! empty( $room_options ) ) {
    foreach ( $room_options as $option_key => $option ) {
        if($room_option == $option_key ){
            $option_title = ! empty( $option['option_title'] ) ? '<br>('. $option['option_title'] . ')' : '';
            break;
        }
    }
}
?>

<div class="tf-single-template__two">
    <!--Hero section start -->
    <div class="tf-hero-section-wrap" style="<?php echo !empty(get_the_post_thumbnail_url()) ? 'background: linear-gradient(180deg, rgba(21, 61, 58, 0.30) 0%, #153D3A 100%), url('.esc_url(get_the_post_thumbnail_url()).') lightgray 50% / cover no-repeat; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
        <div class="tf-container">
            <div class="tf-hero-content">
                <div class="tf-hero-bottom-area">
                    <div class="tf-head-title">
                        <h1><?php the_title(); ?><?php echo wp_kses_post($option_title); ?></h1>
                    </div>
                    <div class="tf-hero-gallery-videos">
                        <?php if ( ! empty( $gallery_ids ) ) { ?>
                            <div class="tf-hero-hotel tf-popup-buttons">
                                <a href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                                    <path d="M42 30L35.828 23.828C35.0779 23.0781 34.0607 22.6569 33 22.6569C31.9393 22.6569 30.9221 23.0781 30.172 23.828L12 42M10 6H38C40.2091 6 42 7.79086 42 10V38C42 40.2091 40.2091 42 38 42H10C7.79086 42 6 40.2091 6 38V10C6 7.79086 7.79086 6 10 6ZM22 18C22 20.2091 20.2091 22 18 22C15.7909 22 14 20.2091 14 18C14 15.7909 15.7909 14 18 14C20.2091 14 22 15.7909 22 18Z" stroke="#F5FFFE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Hero section End -->

    <!--Content section end -->
    <div class="tf-content-wrapper">
        
        <div class="tf-container">
        
            <!-- Hotel details Srart -->
            <div class="tf-details" id="tf-hotel-overview">
                <div class="tf-details-left">
                    <div class="tf-room-single-mobile-booking-form-wrap">
                        <div class="tf-room-price"><?php Pricing::instance( get_the_ID() )->get_per_price_html( $room_option, 'design-2' ); ?></div>
                        <div class="tf-room-booking-box">
                            <?php Room::tf_room_sidebar_booking_form(); ?>
                        </div>
                    </div>
                    <?php 
                    if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-room-layout']) ){
                        foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-room-layout'] as $section){
                            if( !empty($section['status']) && $section['status']=="1" && !empty($section['slug']) ){
                                include TF_TEMPLATE_PART_PATH . 'room/design-1/'.$section['slug'].'.php';
                            }
                        }
                    }else{
                        include TF_TEMPLATE_PART_PATH . 'room/design-1/description.php';
                        include TF_TEMPLATE_PART_PATH . 'room/design-1/amenities.php';
                        include TF_TEMPLATE_PART_PATH . 'room/design-1/house-rules.php';
                        include TF_TEMPLATE_PART_PATH . 'room/design-1/cancellation-policy.php';
                        include TF_TEMPLATE_PART_PATH . 'room/design-1/reviews.php';
                    }
                    ?>
                </div>
                <div class="tf-details-right tf-sitebar-widgets">
                    <div class="tf-room-single-booking-form-wrap">
                        <div class="tf-room-price"><?php Pricing::instance( get_the_ID() )->get_per_price_html( $room_option, 'design-2' ); ?></div>
                        <div class="tf-room-booking-box">
                            <?php Room::tf_room_sidebar_booking_form(); ?>
                        </div>
                    </div>
                    
                    <div class="tf-review-widget tf-single-widgets ">
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
                            <button class="tf_btn tf_btn_rounded tf_btn_full tf_btn_large tf-review-open">
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
                    
                </div>        
            </div>        
            <!-- Hotel details End -->
            
            

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