<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Apartment\Apartment;
use \Tourfic\Classes\Apartment\Pricing as Apt_Pricing;
?>

<div class="tf-single-template__two">
<!--Hero section start -->
<div class="tf-hero-section-wrap" style="<?php echo !empty(get_the_post_thumbnail_url()) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url(get_the_post_thumbnail_url()).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background-color: rgba(48, 40, 28, 0.30); background-image: url('.esc_url(TF_ASSETS_APP_URL . 'images/feature-default.jpg').');' ?>">
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
                    <?php
                    if ( ! empty( $gallery_ids ) ) {
                    ?>
                    <div class="tf-hero-hotel tf-popup-buttons">
                        <a href="#">
                            <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="content">
                            <path id="Rectangle 2111" d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Rectangle 2109" d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"/>
                            <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
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
<div class="tf-content-wrapper tf-single-pb-56">

    <div class="tf-container">

    <!-- Hotel details Srart -->
    <div class="tf-details" id="tf-apartment-overview">
        <div class="tf-details-left">
            <?php \Tourfic\App\Templates\Components\Shared\Single\Sticky_Nav::render(); ?>

            <?php
            if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-aprtment-layout-part-1']) ){
                foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-aprtment-layout-part-1'] as $section){
                    if( !empty($section['status']) && $section['status']=="1" && !empty($section['slug']) ){
                        include TF_TEMPLATE_PART_PATH . 'apartment/design-1/'.$section['slug'].'.php';
                    }
                }
            }else{
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/description.php';
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/features.php';
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/rooms.php';
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/offer.php';
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/rules.php';
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/facilities.php';
            }
            ?>
        </div>
        <div class="tf-details-right tf-sitebar-widgets">
            <?php 
            \Tourfic\App\Templates\Components\Shared\Single\Booking_Form::render();

            if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ){
                \Tourfic\App\Templates\Components\Shared\Single\Nearby_Places::render([
                    'wrapper_open' => '<div class="tf-single-widgets">', 
                    'wrapper_close' => '</div>'
                ]);
            } ?>

            <?php 
            \Tourfic\App\Templates\Components\Shared\Single\Map::render([
                'wrapper_open' => '<div class="tf-location tf-single-widgets">',
                'wrapper_close' => '</div>',
            ], '', '250px'); 

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
    if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-aprtment-layout-part-2']) ){
        foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-aprtment-layout-part-2'] as $section){
            if( !empty($section['status']) && $section['status']=="1" && !empty($section['slug']) ){
                include TF_TEMPLATE_PART_PATH . 'apartment/design-1/'.$section['slug'].'.php';
            }
        }
    }else{
        include TF_TEMPLATE_PART_PATH . 'apartment/design-1/review.php';
        include TF_TEMPLATE_PART_PATH . 'apartment/design-1/faq.php';
        include TF_TEMPLATE_PART_PATH . 'apartment/design-1/trams-condition.php';
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

<?php \Tourfic\App\Templates\Components\Shared\Single\Related_Post::render(['container' => 'yes']); ?>
</div>