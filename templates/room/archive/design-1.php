<?php

use \Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Room;

$tf_room_arc_banner = ! empty(Helper::tf_data_types(Helper::tfopt('tf-template'))['room_archive_design_1_bannar']) ?  Helper::tf_data_types(Helper::tfopt('tf-template'))['room_archive_design_1_bannar'] : '';
$tf_room_arc_banner = !empty($tf_room_arc_banner) ? $tf_room_arc_banner : TF_ASSETS_APP_URL . '/images/room-hero-banner.png';
?>

<div class="tf-archive-template__one sp-0">
    <div class="tf-archive-room-banner" style="<?php echo !empty($tf_room_arc_banner) ? 'background-image: url(' . esc_url($tf_room_arc_banner) . ')' : ''; ?>">
       <div class="tf-container">
            <div class="tf-banner-content tf-flex tf-flex-justify-center tf-flex-direction-column">
                <h1><?php esc_html_e("Rooms", "tourfic"); ?></h1>
                <?php Helper::tf_archive_sidebar_search_form('tf_room'); ?>
            </div>
       </div>
    </div>
    <div class="tf-archive-room-details">
        <div class="tf-container">
            <div class="tf-archive-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                <?php $post_count = $GLOBALS['wp_query']->post_count; ?>
                <h3 class="tf-total-results">
                    <?php 
                    /* translators: %s: number of rooms */ 
                    printf( esc_html__( 'Total %s rooms available', 'tourfic' ), '<span>' . esc_html( $post_count ) . '</span>' ); 
                    ?>
                </h3>
                <div class="tf-archive-header-right tf-flex tf-flex-space-bttn tf-flex-align-center tf-flex-gap-16">
                    <form class="tf-archive-ordering" method="get">
                        <select class="tf-orderby tf-room-archive-action-btn" name="tf-orderby" id="tf-orderby">
                            <option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
                            <option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
                            <option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
                            <option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
                            <option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
                        </select>
                        <i class="fas fa-chevron-down"></i>
                    </form>
                    <div class="tf-room-archive-action-btn tf-archive-filter-btn">
                        <i class="ri-equalizer-line"></i>
                        <span><?php esc_html_e("All Filter", "tourfic"); ?></span>
                    </div>
                    <div class="tf-archive-filter">
                        <div class="tf-archive-filter-sidebar">
                            <div class="tf-filter-wrapper">
                                <div class="tf-filter-title">
                                    <h4 class="tf-section-title"><?php echo esc_html__("Filter", "tourfic"); ?></h4>
                                    <button class="filter-reset-btn"><?php echo esc_html__("Reset", "tourfic"); ?></button>
                                </div>
                                <?php if (is_active_sidebar('tf_archive_booking_sidebar')) { ?>
                                    <div id="tf__booking_sidebar">
                                        <?php dynamic_sidebar('tf_archive_booking_sidebar'); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tf-room-archive-result">
                <?php do_action("tf_room_archive_roomd_items_before"); ?>
                <div class="tf-room-item-cards tf-flex tf-room-result archive_ajax_result">

                    <?php
                    if (have_posts()) {
                        while (have_posts()) {
                            the_post();
                            Room::tf_room_archive_single_item();
                        }
                    } else {
                        echo '<div class="tf-nothing-found" data-post-count="0" >' . esc_html__("No Rooms Found!", "tourfic") . '</div>';
                    }
                    ?>

                    <div class="tf-pagination-bar">
                        <?php Helper::tourfic_posts_navigation(); ?>
                    </div>
                </div>
                <?php do_action("tf_room_archive_roomd_items_after"); ?>
            </div>
        </div>
   </div>
</div>