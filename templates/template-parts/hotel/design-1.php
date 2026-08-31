<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Hotel\Hotel;

$tourfic_booking_type      = ! empty( $tourfic_meta['booking-by'] ) ? $tourfic_meta['booking-by'] : 1;
$tourfic_hide_booking_form = ! empty( $tourfic_meta['hide_booking_form'] ) ? $tourfic_meta['hide_booking_form'] : '';
$tourfic_ext_booking_type  = ! empty( $tourfic_meta['external-booking-type'] ) ? $tourfic_meta['external-booking-type'] : '1';
$tourfic_ext_booking_code  = ! empty( $tourfic_meta['booking-code'] ) ? $tourfic_meta['booking-code'] : '';
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
                                <?php do_action( "tourfic_hotel_single_widgets" ); ?>
                                <?php do_action( "tourfic_single_hotel_sidebar_area_with_args", $tourfic_post_id ); ?>
                            </div>
                            <!-- Hotel Single Widget Hook are - end -->
                        </div>
                    </div>
                </div>
               
                <?php 
                if(file_exists(TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-1/places.php')) {
                    include TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-1/places.php';
                }
                ?>

                <?php 
                if( !empty(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout']) ){
                    foreach(Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout'] as $tourfic_section){
                        if( !empty($tourfic_section['status']) && $tourfic_section['status']=="1" && !empty($tourfic_section['slug']) ){
                            include TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-1/'.$tourfic_section['slug'].'.php';
                        }
                    }
                }else{
                    include TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-1/description.php';
                    include TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-1/features.php';
                    include TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-1/rooms.php';
                    include TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-1/facilities.php';
                    include TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-1/faq.php';
                    include TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-1/review.php';
                    include TOURFIC_TEMPLATE_PART_PATH . 'hotel/design-1/trams-condition.php';
                }
                ?>
            </div>
            
        </div>
    </div>

    
</div>
