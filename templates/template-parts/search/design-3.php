<div class="tf-archive-template__three">
    <?php

    use \Tourfic\Classes\Helper;

    // Check nonce security
    if (!isset($_GET['_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_nonce'])), 'tf_ajax_nonce')) {
        return;
    }

    if( !empty($_GET['type']) && $_GET['type']=="tf_tours" ){
        $tf_search_result_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_3_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_3_bannar'] : '';
    }elseif( !empty($_GET['type']) && $_GET['type']=="tf_hotel" ){
        $tf_search_result_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_3_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_3_bannar'] : '';
    }else{
        $tf_search_result_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_2_bannar'] : '';
    }
    ?>
    <div class="tf-content-wrapper">
        <?php
        do_action('tf_before_container');
        $post_count = $GLOBALS['wp_query']->post_count;
        $tf_map_settings = !empty(Helper::tfopt('google-page-option')) ? Helper::tfopt('google-page-option') : "default";
        $tf_map_api = !empty(Helper::tfopt('tf-googlemapapi')) ? Helper::tfopt('tf-googlemapapi') : '';
        ?>

        <div class="tf-archive-search-form tf-booking-form-wrapper" style="<?php echo !empty($tf_search_result_banner) ? 'background-image: url('.esc_url($tf_search_result_banner).')' : ''; ?>">
            <div class="tf-container">
                <form action="<?php echo esc_url(Helper::tf_booking_search_action()); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking tf-booking-form">
                    <?php Helper::tf_search_result_sidebar_form('archive'); ?>
                </form>
            </div>
        </div>

        <div class="tf-archive-details-wrap">
            <div class="tf-archive-details">

                <?php if ($tf_map_settings == "googlemap") :
                    if (empty($tf_map_api)):
                        ?>
                        <div class="tf-container">
                            <div class="tf-notice tf-mt-24 tf-mb-30">
                                <?php
                                if (current_user_can('administrator')) {
                                    echo '<p>' . esc_html__('Google Maps is selected but the API key is missing. Please configure the API key ', 'tourfic') . '<a href="' . esc_url(admin_url('admin.php?page=tf_settings#tab=map_settings')) . '" target="_blank">' . esc_html__('Map Settings', 'tourfic') . '</a></p>';
                                } else {
                                    echo '<p>' . esc_html__('Access is restricted as Google Maps API key is not configured. Please contact the site administrator.', 'tourfic') . '</p>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="tf-details-left">
                            <!-- Loader Image -->
                            <div id="tf_ajax_searchresult_loader">
                                <div id="tf-searchresult-loader-img">
                                    <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
                                </div>
                            </div>
                            <!--Available rooms start -->
                            <div class="tf-archive-hotels-wrapper">
                                <div class="tf-archive-filter">
                                    <div class="tf-archive-filter-sidebar">
                                        <div class="tf-filter-wrapper">
                                            <div class="tf-filter-title">
                                                <h4 class="tf-section-title"><?php echo esc_html__("Filter", "tourfic"); ?></h4>
                                                <button class="filter-reset-btn"><?php echo esc_html__("Reset", "tourfic"); ?></button>
                                            </div>
                                            <?php if (is_active_sidebar('tf_search_result')) { ?>
                                                <div id="tf__booking_sidebar">
                                                    <?php dynamic_sidebar('tf_search_result'); ?>
                                                </div>
                                            <?php } ?>
                                            <?php if (is_active_sidebar('tf_map_popup_sidebar')) { ?>
                                                <div id="tf_map_popup_sidebar">
                                                    <?php dynamic_sidebar('tf_map_popup_sidebar'); ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <?php echo do_shortcode("[tf_search_result]"); ?>

                            </div>
                            <!-- Available rooms end -->
                        </div>
                        <div class="tf-details-right tf-archive-right">
                            <a href="" class="tf-mobile-list-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M1.33398 7.59935C1.33398 6.82717 1.49514 6.66602 2.26732 6.66602H13.734C14.5062 6.66602 14.6673 6.82717 14.6673 7.59935V8.39935C14.6673 9.17153 14.5062 9.33268 13.734 9.33268H2.26732C1.49514 9.33268 1.33398 9.17153 1.33398 8.39935V7.59935Z"
                                          stroke="#FEF9F6" stroke-linecap="round"/>
                                    <path d="M1.33398 2.26634C1.33398 1.49416 1.49514 1.33301 2.26732 1.33301H13.734C14.5062 1.33301 14.6673 1.49416 14.6673 2.26634V3.06634C14.6673 3.83852 14.5062 3.99967 13.734 3.99967H2.26732C1.49514 3.99967 1.33398 3.83852 1.33398 3.06634V2.26634Z"
                                          stroke="#FEF9F6" stroke-linecap="round"/>
                                    <path d="M1.33398 12.9333C1.33398 12.1612 1.49514 12 2.26732 12H13.734C14.5062 12 14.6673 12.1612 14.6673 12.9333V13.7333C14.6673 14.5055 14.5062 14.6667 13.734 14.6667H2.26732C1.49514 14.6667 1.33398 14.5055 1.33398 13.7333V12.9333Z"
                                          stroke="#FEF9F6" stroke-linecap="round"/>
                                </svg>
                                <span><?php echo esc_html__('List view', 'tourfic') ?></span>
                            </a>
                            <div id="map-marker" data-marker="<?php echo esc_url(TF_ASSETS_URL . 'app/images/cluster-marker.png'); ?>"></div>
                            <div class="tf-hotel-archive-map-wrap">
                                <div id="tf-hotel-archive-map"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="tf-container">
                        <div class="tf-notice tf-mt-24 tf-mb-30">
                            <?php
                            if (current_user_can('administrator')) {
                                echo '<p>' . esc_html__('Google Maps is not selected. Please configure it ', 'tourfic') . '<a href="' . esc_url(admin_url('admin.php?page=tf_settings#tab=map_settings')) . '" target="_blank">' . esc_html__('Map Settings', 'tourfic') . '</a></p>';
                            } else {
                                echo '<p>' . esc_html__('Access is restricted as Google Maps is not enabled. Please contact the site administrator', 'tourfic') . '</p>';
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!--Content section end -->

</div>