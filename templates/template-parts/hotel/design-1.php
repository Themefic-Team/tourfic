<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Hotel\Hotel;

$tf_booking_type = '1';
$tf_hide_booking_form = '';
$tf_ext_booking_type = '';
$tf_ext_booking_code = '';
if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
	$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
	$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
	$tf_ext_booking_type = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : '1';
    $tf_ext_booking_code = !empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
}
?>
<div class="tf-single-template__one">
    <div class="tf-tour-single">
        <div class="tf-container">
            <div class="tf-container-inner">
                <!-- Single Hotel Heading Section start -->
                <div class="tf-section tf-single-head">
                    <div class="tf-head-info tf-flex tf-flex-space-bttn tf-flex-gap-24">
                        <div class="tf-head-title">
                            <?php \Tourfic\App\Templates\Components\Shared\Single\Title::render(); ?>
                            <?php \Tourfic\App\Templates\Components\Shared\Single\Address::render(); ?>
                        </div>
                        <div class="tf-head-social tf-flex tf-flex-gap-8 tf-flex-align-center">
                            <?php \Tourfic\App\Templates\Components\Shared\Single\Wishlist::render(); ?>
                            <?php \Tourfic\App\Templates\Components\Shared\Single\Share::render(); ?>
                        </div>
                    </div>
                </div>
                <!-- Single Hotel Heading Section End -->

                <!-- Single Hotel Body details start -->
                <div class="tf-single-details-wrapper tf-mt-30 tf-mb-40">
                    <div class="tf-single-details-inner tf-flex">
                        <div class="tf-tour-details-left">
                            <?php \Tourfic\App\Templates\Components\Shared\Single\Gallery::render(['gallery_style' => 'style1']); ?>
                        </div>

                        <!-- SIdebar Tour single -->
                        <div class="tf-tour-details-right">
                            <?php \Tourfic\App\Templates\Components\Shared\Single\Booking_Form::render(['wrapper' => 'no']); ?>
                            <?php \Tourfic\App\Templates\Components\Shared\Single\Map::render([
                                'show_title' => 'no',
                            ]); ?>
                            
                            <!-- Hotel Single Widget Hook are - start -->
                            <div class="tf-hotel-single-custom-widget-wrap">
                                <?php do_action( "tf_hotel_single_widgets" ); ?>
                                <?php do_action( "tf_single_hotel_sidebar_area_with_args", $post_id ); ?>
                            </div>
                            <!-- Hotel Single Widget Hook are - end -->
                        </div>
                    </div>
                </div>
               
                <?php 
                if(file_exists(TF_TEMPLATE_PART_PATH . 'hotel/design-1/places.php')) {
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/places.php';
                }
                ?>

                <?php 
                if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout']) ){
                    foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout'] as $section){
                        if( !empty($section['status']) && $section['status']=="1" && !empty($section['slug']) ){
                            include TF_TEMPLATE_PART_PATH . 'hotel/design-1/'.$section['slug'].'.php';
                        }
                    }
                }else{
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/description.php';
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/features.php';
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/rooms.php';
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/facilities.php';
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/faq.php';
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/review.php';
                    include TF_TEMPLATE_PART_PATH . 'hotel/design-1/trams-condition.php';
                }
                ?>
            </div>
            
        </div>
    </div>

    
</div>