<?php

use \Tourfic\Classes\Helper;

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
            <div class="tf-container-inner">
                <div class="tf-archive-room-details-warper">
                    <div class="tf-archive-header tf-flex tf-flex-space-bttn tf-flex-align-center tf-mb-30">
                        <?php
                        $post_count = $GLOBALS['wp_query']->post_count;
                        ?>
                        <div class="tf-total-result-bar">
                            <h3>
                                <span>
                                    <?php echo esc_html__('Total ', 'tourfic'); ?>
                                </span>
                                <div class="tf-total-results">
                                    <span><?php echo esc_html($post_count) . esc_html__(' room type available ', 'tourfic'); ?> </span>
                                </div>
                            </h3>
                        </div>
                        <div class="tf-archive-filter">
                            
                        </div>
                    </div>
                    <div class="tf-room-details-column tf-flex tf-flex-gap-32">
                        <div class="tf-room-archive-result">
                            <?php do_action("tf_room_archive_roomd_items_before"); ?>
                            <div class="tf-room-result archive_ajax_result tf-flex tf-flex-gap-32 <?php echo $tf_defult_views == "list" ? esc_attr('list-view') : esc_attr('grid-view'); ?>">

                                <?php
                                if (have_posts()) {
                                    while (have_posts()) {
                                        the_post();
                                        $room_meta = get_post_meta(get_the_ID(), 'tf_roomrental_opt', true);
                                        if (!empty($room_meta["room_as_featured"]) && $room_meta["room_as_featured"] == 1) {
                                            tf_car_archive_single_item();
                                        }
                                    }
                                    while (have_posts()) {
                                        the_post();
                                        $room_meta = get_post_meta(get_the_ID(), 'tf_roomrental_opt', true);
                                        if (empty($room_meta["room_as_featured"])) {
                                            tf_car_archive_single_item();
                                        }
                                    }
                                } else {
                                    echo '<div class="tf-nothing-found" data-post-count="0" >' . esc_html__("No Tours Found!", "tourfic") . '</div>';
                                }
                                ?>

                            </div>
                            <?php do_action("tf_room_archive_roomd_items_after"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </div>
</div>