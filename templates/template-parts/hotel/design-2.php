<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
?>

<div class="tf-single-template__two">
<!--Hero section start -->
<div class="tf-hero-section-wrap" style="<?php echo !empty(get_the_post_thumbnail_url()) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url(get_the_post_thumbnail_url()).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
    <div class="tf-container">
        <div class="tf-hero-content">
            <div class="tf-wish-and-share">
                <?php \Tourfic\App\Templates\Components\Shared\Single\Wishlist::render(['design' => 'design-2']); ?>
                <?php \Tourfic\App\Templates\Components\Shared\Single\Share::render(['share_style' => 'style2']); ?>
            </div>
            <div class="tf-hero-bottom-area">
                <div class="tf-head-title">
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Title::render(); ?>
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Address::render(['design' => 'design-2']); ?>
                </div>
                <div class="tf-hero-gallery-videos">
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Video_Button::render(['design' => 'design-2'], '', false); ?>
                    <?php \Tourfic\App\Templates\Components\Shared\Single\Gallery_Button::render(['style' => 'style2']); ?>
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
            <?php \Tourfic\App\Templates\Components\Shared\Single\Sticky_Nav::render(); ?>

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
            \Tourfic\App\Templates\Components\Shared\Single\Nearby_Places::render([
                'nearby_places_style' => 'style2',
                'wrapper_open' => '<div class="tf-single-widgets">', 
                'wrapper_close' => '</div>'
            ]);

            \Tourfic\App\Templates\Components\Shared\Single\Map::render([
                'show_icon' => 'no',
                'wrapper' => 'yes',
            ]);

            \Tourfic\App\Templates\Components\Shared\Single\Review::render([
                'review_style' => 'design-2',
                'show_review_states' => 'yes',
                'show_reviews' => 'no',
                'show_review_form' => 'yes',
            ]);
            
            \Tourfic\App\Templates\Components\Shared\Single\Enquiry::render([
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