<?php

namespace Tourfic\App\Widgets\TF_Widgets;

// Exit if accessed directly.
defined('ABSPATH') || exit;

use \Tourfic\Classes\Helper;
use Tourfic\Classes\Hotel\Hotel;
use Tourfic\Classes\Tour\Tour;
use \Tourfic\Classes\Apartment\Apartment;
use \Tourfic\Classes\Hotel\Pricing as hotelPricing;
use \Tourfic\Classes\Tour\Pricing as tourPricing;
use \Tourfic\Classes\Apartment\Pricing as apartmentPricing;

/**
 * Hotel, Tour & Apartment map filter
 */
class Map_Filter extends \WP_Widget {

    use \Tourfic\Traits\Singleton;

    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_map_filters', // Base ID
            esc_html__('Tourfic - Hotel, Tour & Apartment Map Filter', 'tourfic'), // Name
            array('description' => esc_html__('Show map on Archive/Search Result page popup', 'tourfic')) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     * @see WP_Widget::widget()
     *
     */
    public function widget($args, $instance) {

        $tf_query_taxonomy = !empty(get_taxonomy(get_queried_object())) ? get_taxonomy(get_queried_object()->taxonomy)->object_type : '';
        if (is_post_type_archive('tf_tours') || is_post_type_archive('tf_hotel') || is_post_type_archive('tf_apartment') || (!empty($tf_query_taxonomy))) {
            extract($args);
            $title = apply_filters('widget_title', $instance['title']);
            echo wp_kses_post($before_widget);
            if (is_post_type_archive('tf_hotel')) {
                ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Hotel Price Range", "tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-hotel-result-price-range"></div>
                <?php
            }
            if (is_post_type_archive('tf_tours')) {
                ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Tour Price Range", "tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-tour-result-price-range"></div>
                <?php
            }
            if (is_post_type_archive('tf_apartment')) {
                $this->widget_html();
            }
            if (!is_post_type_archive('tf_hotel') &&
                !is_post_type_archive('tf_tours') &&
                !is_post_type_archive('tf_apartment') &&
                (!empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) &&
                    get_taxonomy(get_queried_object()->taxonomy)->object_type[0] == "tf_hotel")) {
                ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Hotel Price Range", "tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-hotel-result-price-range"></div>
                <?php
            }
            if (!is_post_type_archive('tf_hotel') && !is_post_type_archive('tf_tours') && !is_post_type_archive('tf_apartment') && (!empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0] == "tf_tours")) {
                ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Tour Price Range", "tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-tour-result-price-range"></div>
                <?php
            }
            if (!is_post_type_archive('tf_hotel') && !is_post_type_archive('tf_tours') && !is_post_type_archive('tf_apartment') && (!empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0] == "tf_apartment")) {
                ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Apartment Price Range", "tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-apartment-result-price-range"></div>
                <?php
            }
        } else {
            extract($args);
            $title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
            echo wp_kses_post($before_widget);
            if (!empty($_GET['type']) && $_GET['type'] == "tf_tours" && !empty($_GET['from']) && !empty($_GET['to'])) {
                ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Tour Price Range", "tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-tour-result-price-range"></div>
            <?php }
            if (!empty($_GET['type']) && $_GET['type'] == "tf_hotel" && !empty($_GET['from']) && !empty($_GET['to'])) {
                ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Hotel Price Range", "tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-hotel-result-price-range"></div>
            <?php }
            if (!empty($_GET['type']) && $_GET['type'] == "tf_apartment" && !empty($_GET['from']) && !empty($_GET['to'])) {
                ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Apartment Price Range", "tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-apartment-result-price-range"></div>
            <?php }
        } ?>
        <!-- End Price Range widget -->
        <?php

        echo wp_kses_post($after_widget);
    }

    function widget_html() {
        $tf_map_settings = !empty(Helper::tfopt('google-page-option')) ? Helper::tfopt('google-page-option') : "default";
        $tf_map_api = !empty(Helper::tfopt('tf-googlemapapi')) ? Helper::tfopt('tf-googlemapapi') : '';
        $args = array(
            'post_type'      => 'tf_apartment',
            'post_status'    => 'published',
        );
        $loop        = new \WP_Query( $args );
        $total_posts = $loop->found_posts;

        if ( !$loop->have_posts() ) {
            return;
        }

        if ($tf_map_settings == "googlemap") :
            if (empty($tf_map_api)):
                ?>
                <div class="tf-notice">
                    <?php
                    if (current_user_can('administrator')) {
                        echo '<p>' . sprintf(
                                __('Google Maps is selected but the API key is missing. Please configure the API key <a href="%s" target="_blank">Map Settings</a>.', 'tourfic'),
                                admin_url('admin.php?page=tf_settings#tab=map_settings')
                            ) . '</p>';
                    } else {
                        echo '<p>' . __('Access is restricted as Google Maps API key is not configured. Please contact the site administrator.', 'tourfic') . '</p>';
                    }
                    ?>
                </div>
            <?php else: ?>
                <div class="tf-map-widget-wrap">
                    <div class="tf-map-preview">
                        <img src="<?php echo esc_url(TF_ASSETS_URL . 'app/images/map-img.png'); ?>" alt="">

                        <div class="tf-map-preview-content">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="45" viewBox="0 0 32 45" fill="none">
                                <ellipse cx="16" cy="42.5" rx="11" ry="2.5" fill="#141F43" fill-opacity="0.25"/>
                                <path d="M14 41.0171C9.66667 35.6849 0 22.9696 0 15.7506C0 7.05494 7.08333 0 16 0C24.8333 0 32 7.05494 32 15.7506C32 22.9696 22.25 35.6849 17.9167 41.0171C16.9167 42.2476 15 42.2476 14 41.0171ZM16 21.0008C18.9167 21.0008 21.3333 18.7038 21.3333 15.7506C21.3333 12.8794 18.9167 10.5004 16 10.5004C13 10.5004 10.6667 12.8794 10.6667 15.7506C10.6667 18.7038 13 21.0008 16 21.0008Z" fill="#0E3DD8"/>
                            </svg>
                            <span class="btn-styled tf-map-modal-btn"><?php esc_html_e('Show on Map', 'tourfic'); ?></span>
                        </div>
                    </div>

                    <?php
                    if ( Hotel::template( 'archive' ) !== 'design-3' ||
                        Tour::template( 'archive' ) !== 'design-3' ||
                        Apartment::template( 'archive' ) !== 'design-2' ) : ?>
                        <div class="tf-archive-details-wrap tf-map-popup-wrap">
                            <div class="tf-archive-details ">
                                <!-- Loader Image -->
                                <div id="tf_ajax_searchresult_loader">
                                    <div id="tf-searchresult-loader-img">
                                        <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
                                    </div>
                                </div>
                                <div class="tf-details-left">
                                    <div class="tf-archive-hotels-wrapper">
                                        <div class="tf-archive-filter">
                                            <div class="tf-archive-filter-sidebar">
                                                <div class="tf-filter-wrapper">
                                                    <div class="tf-filter-title">
                                                        <h4 class="tf-section-title"><?php echo esc_html__("Filter", "tourfic"); ?></h4>
                                                        <button class="filter-reset-btn"><?php echo esc_html__("Reset", "tourfic"); ?></button>
                                                    </div>
                                                    <?php if (is_active_sidebar('tf_archive_booking_sidebar')) { ?>
                                                        <div id="tf__booking_sidebar">
                                                            <?php //dynamic_sidebar('tf_archive_booking_sidebar'); ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tf-archive-top">
                                            <h5 class="tf-total-results"><?php _e("Found", "tourfic"); ?>
                                                <span><?php echo $total_posts; ?></span> <?php _e("of", "tourfic"); ?> <?php echo $GLOBALS['wp_query']->found_posts; ?> <?php _e("Apartments", "tourfic"); ?></h5>
                                            <a href="" class="tf-mobile-map-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                    <path d="M17.3327 7.33366V6.68156C17.3327 5.06522 17.3327 4.25705 16.8445 3.75491C16.3564 3.25278 15.5707 3.25278 13.9993 3.25278H12.2671C11.5027 3.25278 11.4964 3.25129 10.8089 2.90728L8.03258 1.51794C6.87338 0.93786 6.29378 0.647818 5.67633 0.667975C5.05888 0.688132 4.49833 1.01539 3.37722 1.66992L2.354 2.2673C1.5305 2.74807 1.11876 2.98846 0.892386 3.38836C0.666016 3.78827 0.666016 4.27527 0.666016 5.24927V12.0968C0.666016 13.3765 0.666016 14.0164 0.951234 14.3725C1.14102 14.6095 1.40698 14.7688 1.70102 14.8216C2.1429 14.901 2.68392 14.5851 3.76591 13.9534C4.50065 13.5245 5.20777 13.079 6.08674 13.1998C6.82326 13.301 7.50768 13.7657 8.16602 14.0952"
                                                          stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M5.66602 0.666992L5.66601 13.167" stroke="white" stroke-linejoin="round"/>
                                                    <path d="M11.5 3.16699V6.91699" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M14.2556 17.0696C14.075 17.2388 13.8334 17.3333 13.5821 17.3333C13.3308 17.3333 13.0893 17.2388 12.9086 17.0696C11.254 15.5108 9.0366 13.7695 10.1179 11.2415C10.7026 9.87465 12.1061 9 13.5821 9C15.0581 9 16.4616 9.87465 17.0463 11.2415C18.1263 13.7664 15.9143 15.5162 14.2556 17.0696Z"
                                                          stroke="white"/>
                                                    <path d="M13.582 12.75H13.5895" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span><?php echo esc_html__('Map', 'tourfic') ?></span>
                                            </a>
                                            <ul class="tf-archive-view">
                                                <li class="tf-archive-filter-btn">
                                                    <i class="ri-equalizer-line"></i>
                                                    <span><?php _e("All Filter", "tourfic"); ?></span>
                                                </li>
                                            </ul>
                                        </div>

                                        <!--Available rooms start -->
                                        <div class="tf-archive-hotels archive_ajax_result tf-layout-list">

                                            <?php
                                            $count = 0;
                                            $locations = [];
                                            while ( $loop->have_posts() ) {
                                                $loop->the_post();

                                                $meta = get_post_meta(get_the_ID(), 'tf_apartment_opt', true);
                                                if (!$meta["apartment_as_featured"]) {
                                                    continue;
                                                }

                                                $count++;
                                                $map = !empty($meta['map']) ? Helper::tf_data_types($meta['map']) : '';
                                                $discount_type = !empty($meta['discount_type']) ? $meta['discount_type'] : '';
                                                $discount_price = !empty($meta['discount']) ? $meta['discount'] : '';

                                                $min_price_arr = apartmentPricing::instance(get_the_ID())->get_min_price();
                                                $min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
                                                $min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;

                                                if ($min_regular_price != 0) {
                                                    $price_html = wc_format_sale_price($min_regular_price, $min_sale_price);
                                                } else {
                                                    $price_html = wp_kses_post(wc_price($min_sale_price)) . " ";
                                                }

                                                if (!empty($map)) {
                                                    $lat = $map['latitude'];
                                                    $lng = $map['longitude'];
                                                    ob_start();
                                                    ?>
                                                    <div class="tf-map-item" data-price="<?php //echo esc_attr( wc_price( $min_sale_price ) ); ?>">
                                                        <div class="tf-map-item-thumb">
                                                            <a href="<?php echo get_the_permalink(); ?>">
                                                                <?php
                                                                if (!empty(wp_get_attachment_url(get_post_thumbnail_id(), 'tf_gallery_thumb'))) {
                                                                    the_post_thumbnail('full');
                                                                } else {
                                                                    echo '<img src="' . TF_ASSETS_APP_URL . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
                                                                }
                                                                ?>
                                                            </a>

                                                            <?php
                                                            if (!empty($discount_price)) : ?>
                                                                <div class="tf-map-item-discount">
                                                                    <?php echo $discount_type == "percent" ? $discount_price . '%' : wc_price($discount_price) ?><?php _e(" Off", "tourfic"); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="tf-map-item-content">
                                                            <h4><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h4>
                                                            <div class="tf-map-item-price">
                                                                <?php echo apartmentPricing::instance(get_the_ID())->get_min_price_html(); ?>
                                                            </div>
                                                            <?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $infoWindowtext = ob_get_clean();

                                                    $locations[$count] = [
                                                        'id' => get_the_ID(),
                                                        'lat' => (float)$lat,
                                                        'lng' => (float)$lng,
                                                        'price' => base64_encode($price_html),
                                                        'content' => base64_encode($infoWindowtext)
                                                    ];
                                                }
                                                echo apply_filters("tf_apartment_archive_single_featured_card_design_one", Apartment::tf_apartment_archive_single_item());
                                            }
                                            while ( $loop->have_posts() ) {
                                                $loop->the_post();
                                                the_post();

                                                $meta = get_post_meta(get_the_ID(), 'tf_apartment_opt', true);
                                                if (!empty($meta["apartment_as_featured"]) && $meta["apartment_as_featured"]) {
                                                    continue;
                                                }

                                                $count++;
                                                $map = !empty($meta['map']) ? Helper::tf_data_types($meta['map']) : '';
                                                $discount_type = !empty($meta['discount_type']) ? $meta['discount_type'] : '';
                                                $discount_price = !empty($meta['discount']) ? $meta['discount'] : '';

                                                $min_price_arr = apartmentPricing::instance(get_the_ID())->get_min_price();
                                                $min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
                                                $min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;

                                                if ($min_regular_price != 0) {
                                                    $price_html = wc_format_sale_price($min_regular_price, $min_sale_price);
                                                } else {
                                                    $price_html = wp_kses_post(wc_price($min_sale_price)) . " ";
                                                }

                                                if (!empty($map)) {
                                                    $lat = $map['latitude'];
                                                    $lng = $map['longitude'];
                                                    ob_start();
                                                    ?>
                                                    <div class="tf-map-item" data-price="<?php //echo esc_attr( wc_price( $min_sale_price ) ); ?>">
                                                        <div class="tf-map-item-thumb">
                                                            <a href="<?php echo get_the_permalink(); ?>">
                                                                <?php
                                                                if (!empty(wp_get_attachment_url(get_post_thumbnail_id(), 'tf_gallery_thumb'))) {
                                                                    the_post_thumbnail('full');
                                                                } else {
                                                                    echo '<img src="' . TF_ASSETS_APP_URL . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
                                                                }
                                                                ?>
                                                            </a>

                                                            <?php
                                                            if (!empty($discount_price)) : ?>
                                                                <div class="tf-map-item-discount">
                                                                    <?php echo $discount_type == "percent" ? $discount_price . '%' : wc_price($discount_price) ?><?php _e(" Off", "tourfic"); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="tf-map-item-content">
                                                            <h4><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h4>
                                                            <div class="tf-map-item-price">
                                                                <?php echo apartmentPricing::instance(get_the_ID())->get_min_price_html(); ?>
                                                            </div>
                                                            <?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $infoWindowtext = ob_get_clean();

                                                    $locations[$count] = [
                                                        'id' => get_the_ID(),
                                                        'lat' => (float)$lat,
                                                        'lng' => (float)$lng,
                                                        'price' => base64_encode($price_html),
                                                        'content' => base64_encode($infoWindowtext)
                                                    ];
                                                }
                                                echo apply_filters("tf_apartment_archive_single_card_design_one", Apartment::tf_apartment_archive_single_item());
                                            }
                                            wp_reset_query();
                                            ?>
                                            <div id="map-datas" style="display: none"><?php echo array_filter($locations) ? json_encode(array_values($locations)) : []; ?></div>
                                            <div class="tf-pagination-bar">
                                                <?php Helper::tourfic_posts_navigation(); ?>
                                            </div>
                                        </div>
                                        <!-- Available rooms end -->

                                    </div>
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
                                    <div id="map-marker" data-marker="<?php echo TF_ASSETS_URL . 'app/images/cluster-marker.png'; ?>"></div>
                                    <div class="tf-hotel-archive-map-wrap">
                                        <div id="tf-hotel-archive-map"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="tf-notice">
                <?php
                if (current_user_can('administrator')) {
                    echo '<p>' . sprintf(
                            __('Google Maps is not selected. Please configure it <a href="%s" target="_blank">Map Settings</a>.', 'tourfic'),
                            admin_url('admin.php?page=tf_settings#tab=map_settings')
                        ) . '</p>';
                } else {
                    echo '<p>' . __('Access is restricted as Google Maps is not enabled. Please contact the site administrator', 'tourfic') . '</p>';
                }
                ?>
            </div>
        <?php endif;
    }

    /**
     * Back-end widget form.
     *
     * @param array $instance Previously saved values from database.
     * @see WP_Widget::form()
     *
     */
    public function form($instance) {

        $title = isset($instance['title']) ? $instance['title'] : esc_html__('Price Range Filter', 'tourfic');
        ?>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'tourfic'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
        </p>
    <?php }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     * @see WP_Widget::update()
     *
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? wp_strip_all_tags($new_instance['title']) : '';

        return $instance;
    }

}