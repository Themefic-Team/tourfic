<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

$meta = get_post_meta( get_the_ID(), 'tf_room_opt', true );
$pricing_by = ! empty( $meta["pricing-by"] ) ? $meta["pricing-by"] : 1;
?>
<div class="tf-single-template__two">
    <!--Hero section start -->
    <div class="tf-hero-section-wrap" style="<?php echo !empty(get_the_post_thumbnail_url()) ? 'background: linear-gradient(180deg, rgba(21, 61, 58, 0.30) 0%, var(--tf-primary) 100%), url('.esc_url(get_the_post_thumbnail_url()).') lightgray 50% / cover no-repeat; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
        <div class="tf-container">
            <div class="tf-hero-content">
                <div class="tf-hero-bottom-area">
                    <div class="tf-head-title">
                        <?php \Tourfic\App\Templates\Components\Shared\Single\Title::render(); ?>
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
                    <?php if(wp_is_mobile()): ?>
                    <div class="tf-room-single-mobile-booking-form-wrap">
                        <?php \Tourfic\App\Templates\Components\Shared\Single\Booking_Form::render(); ?>
                    </div>
                    <?php endif; ?>

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
                        include TF_TEMPLATE_PART_PATH . 'room/design-1/room-options.php';
                        include TF_TEMPLATE_PART_PATH . 'room/design-1/house-rules.php';
                        include TF_TEMPLATE_PART_PATH . 'room/design-1/cancellation-policy.php';
                        include TF_TEMPLATE_PART_PATH . 'room/design-1/reviews.php';
                    }
                    ?>
                </div>
                <div class="tf-details-right tf-sitebar-widgets">
                    <?php if(!wp_is_mobile()): ?>
                    <div class="tf-room-single-booking-form-wrap">
                        <?php \Tourfic\App\Templates\Components\Shared\Single\Booking_Form::render(); ?>
                    </div>
                    <?php endif; ?>

                    <?php
                    \Tourfic\App\Templates\Components\Shared\Single\Review::render([
                        'review_style' => 'design-2',
                        'show_review_states' => 'yes',
                        'show_reviews' => 'no',
                        'show_review_form' => 'yes',
                    ]);
                    ?>
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

            <div id="tour_room_details_loader">
                <div id="tour-room-details-loader-img">
                    <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
                </div>
            </div>
        </div>
    </div>
    <!--Content section end -->
</div>