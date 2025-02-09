<?php 
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Hotel\Hotel;
use Tourfic\Classes\Tour\Tour;
use \Tourfic\Classes\Apartment\Apartment;
use \Tourfic\Classes\Hotel\Pricing as hotelPricing;
use \Tourfic\Classes\Tour\Pricing as tourPricing;
use \Tourfic\Classes\Apartment\Pricing as apartmentPricing;

$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
$post_per_page = Helper::tfopt('posts_per_page') ? Helper::tfopt('posts_per_page') : 10;
$tf_expired_tour_showing = ! empty( Helper::tfopt( 't-show-expire-tour' ) ) ? Helper::tfopt( 't-show-expire-tour' ) : '';

if(!empty($tf_expired_tour_showing )){
	$tf_tour_posts_status = array('publish','expired');
}else{
	$tf_tour_posts_status = array('publish');
}

$args = array(
    'post_type' => $post_type,
    'orderby'   => apply_filters( 'tf_archive_post_orderby', 'date' ),
    'order'     => apply_filters( 'tf_archive_post_order', 'DESC' ),
    'tax_query' => array(
        array (
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => $taxonomy_slug,
        )
    ),
    'post_status'    => $tf_tour_posts_status,
    'paged'          => $paged,
    //'posts_per_page' => $post_per_page
);

$loop = new WP_Query( $args );
$post_count = $loop->post_count;
$total_posts = $loop->found_posts;

$tf_tour_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour-archive'] : 'design-1';
$tf_hotel_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';
$tf_apartment_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] : 'default';

// Gird or List View
if($post_type == "tf_hotel"){
    $tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_view'] : 'list';
}elseif($post_type == "tf_apartment"){
    $tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_view'] : 'list';
}elseif($post_type == "tf_tours"){
    $tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_view'] : 'list';
}elseif($post_type == "tf_carrental"){
    $tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_view'] : 'grid';
}

$tf_map_settings = !empty(Helper::tfopt('google-page-option')) ? Helper::tfopt('google-page-option') : "default";
$tf_map_api = !empty(Helper::tfopt('tf-googlemapapi')) ? Helper::tfopt('tf-googlemapapi') : '';
$tf_map_marker = !empty(Helper::tfopt('map_template_marker')) ? Helper::tfopt('map_template_marker') : '';

if( ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template=="design-1" ) || ( $post_type == "tf_tours" && $tf_tour_arc_selected_template=="design-1" ) ){
?>
<div class="tf-column tf-page-content tf-archive-left tf-result-previews">
<!-- Search Head Section -->
    <div class="tf-archive-head tf-flex tf-flex-align-center tf-flex-space-bttn">
        <div class="tf-search-result tf-flex">
            <span class="tf-counter-title"><?php echo esc_html__( 'Total Results ', 'tourfic' ); ?> </span>
            <span><?php echo ' ('; ?> </span>
            <div class="tf-total-results">
                <span><?php echo esc_html($total_posts); ?> </span>
            </div>
            <span><?php echo ')'; ?> </span>
        </div>
        <div class="tf-search-layout tf-flex tf-flex-gap-12">
            <div class="tf-icon tf-serach-layout-list tf-grid-list-layout  <?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" data-id="list-view">
                <div class="defult-view">
                    
                    <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="12" height="2" fill="#0E3DD8"/>
                    <rect x="14" width="2" height="2" fill="#0E3DD8"/>
                    <rect y="5" width="12" height="2" fill="#0E3DD8"/>
                    <rect x="14" y="5" width="2" height="2" fill="#0E3DD8"/>
                    <rect y="10" width="12" height="2" fill="#0E3DD8"/>
                    <rect x="14" y="10" width="2" height="2" fill="#0E3DD8"/>
                    </svg>
                </div>
                <div class="active-view">
                    <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="12" height="2" fill="white"/>
                    <rect x="14" width="2" height="2" fill="white"/>
                    <rect y="5" width="12" height="2" fill="white"/>
                    <rect x="14" y="5" width="2" height="2" fill="white"/>
                    <rect y="10" width="12" height="2" fill="white"/>
                    <rect x="14" y="10" width="2" height="2" fill="white"/>
                    </svg>
                </div>
            </div>
            <div class="tf-icon tf-serach-layout-grid tf-grid-list-layout <?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" data-id="grid-view">
                <div class="defult-view">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="10" width="2" height="2" fill="#0E3DD8"/>
                    <rect x="10" y="5" width="2" height="2" fill="#0E3DD8"/>
                    <rect x="10" y="10" width="2" height="2" fill="#0E3DD8"/>
                    <rect x="5" width="2" height="2" fill="#0E3DD8"/>
                    <rect x="5" y="5" width="2" height="2" fill="#0E3DD8"/>
                    <rect x="5" y="10" width="2" height="2" fill="#0E3DD8"/>
                    <rect width="2" height="2" fill="#0E3DD8"/>
                    <rect y="5" width="2" height="2" fill="#0E3DD8"/>
                    <rect y="10" width="2" height="2" fill="#0E3DD8"/>
                    </svg>
                </div>
                <div class="active-view">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="10" width="2" height="2" fill="white"/>
                    <rect x="10" y="5" width="2" height="2" fill="white"/>
                    <rect x="10" y="10" width="2" height="2" fill="white"/>
                    <rect x="5" width="2" height="2" fill="white"/>
                    <rect x="5" y="5" width="2" height="2" fill="white"/>
                    <rect x="5" y="10" width="2" height="2" fill="white"/>
                    <rect width="2" height="2" fill="white"/>
                    <rect y="5" width="2" height="2" fill="white"/>
                    <rect y="10" width="2" height="2" fill="white"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <!-- Loader Image -->
    <div id="tf_ajax_searchresult_loader">
        <div id="tf-searchresult-loader-img">
            <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
        </div>
    </div>
    <div class="tf-search-results-list tf-mt-30">
        <div class="archive_ajax_result tf-item-cards tf-flex <?php echo $tf_defult_views=="list" ? esc_attr('tf-layout-list') : esc_attr('tf-layout-grid'); ?>">

        <?php
        if ( $loop->have_posts() ) {
            while ( $loop->have_posts() ) {
                $loop->the_post(); 
                if( $post_type == 'tf_hotel' ){
	                Hotel::tf_hotel_archive_single_item();
                } elseif( $post_type == 'tf_tours' ) {
                    Tour::tf_tour_archive_single_item();
                }
            }
        } else {
            echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Tours Found!", "tourfic"). '</div>';
        }
        ?>
            <div class="tf-pagination-bar">
                <?php Helper::tourfic_posts_navigation(); ?>
            </div>
        </div>
    </div>
</div>
<?php }
elseif( ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template=="design-2" ) ||
    ( $post_type == "tf_tours" && $tf_tour_arc_selected_template=="design-2" ) ||
    ( $post_type == "tf_apartment" && $tf_apartment_arc_selected_template=="design-1" ) ){ ?>

    <!--Available rooms start -->
    <div class="tf-available-archive-hetels-wrapper tf-available-rooms-wrapper" id="tf-hotel-rooms">
        <div class="tf-archive-available-rooms-head tf-available-rooms-head">
            <span class="tf-total-results"><?php esc_html_e("Total", "tourfic"); ?> <span><?php echo esc_html($total_posts); ?></span>
            <?php if($post_type == "tf_hotel"){
                esc_html_e("hotels available", "tourfic");
            }elseif($post_type == "tf_apartment"){
                esc_html_e("apartments available", "tourfic");
            }else{
                esc_html_e("tours available", "tourfic");
            } ?>
            </span>
            <div class="tf-archive-filter-showing">
                <i class="ri-equalizer-line"></i>
            </div>
        </div>
        
        <!-- Loader Image -->
        <div id="tour_room_details_loader">
            <div id="tour-room-details-loader-img">
                <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
            </div>
        </div>
        
        <!--Available rooms start -->
        <div class="tf-archive-available-rooms tf-available-rooms archive_ajax_result">
            <?php
            if ( $loop->have_posts() ) {
                while ( $loop->have_posts() ) {
                    $loop->the_post(); 

                    if( $post_type == 'tf_hotel' ){
	                    Hotel::tf_hotel_archive_single_item();
                    } elseif( $post_type == 'tf_tours' ) {
                        Tour::tf_tour_archive_single_item();
                    } elseif( $post_type == 'tf_apartment' ) {
                        Apartment::tf_apartment_archive_single_item();
                    }
                }
            } else {
                echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Tours Found!", "tourfic"). '</div>';
            }
            ?>
            <div class="tf-pagination-bar">
                <?php Helper::tourfic_posts_navigation(); ?>
            </div>
        </div>
        <!-- Available rooms end -->

    </div>
    <!-- Available rooms end -->

<?php } elseif( ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template=="design-3" ) ||
    ( $post_type == "tf_tours" && $tf_tour_arc_selected_template=="design-3" ) ||
    ( $post_type == "tf_apartment" && $tf_apartment_arc_selected_template=="design-2" ) ){ ?>
    <?php if ($loop->have_posts()) : ?>
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
                                            <?php if (is_active_sidebar('tf_archive_booking_sidebar')) { ?>
                                                <div id="tf__booking_sidebar">
                                                    <?php dynamic_sidebar('tf_archive_booking_sidebar'); ?>
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
                                <div class="tf-archive-top">
                                    <h5 class="tf-total-results"><?php esc_html_e("Found", "tourfic"); ?>
                                        <span class="tf-map-item-count"><?php echo esc_html($post_count); ?></span> <?php esc_html_e("of", "tourfic"); ?> <?php echo esc_html($GLOBALS['wp_query']->found_posts); ?> <?php esc_html_e("Hotels", "tourfic"); ?></h5>
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
                                            <span><?php esc_html_e("All Filter", "tourfic"); ?></span>
                                        </li>
                                        <li class="tf-archive-view-item tf-archive-list-view <?php echo $tf_defult_views == "list" ? esc_attr('active') : ''; ?>" data-id="list-view">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M1.33398 7.59996C1.33398 6.82778 1.49514 6.66663 2.26732 6.66663H13.734C14.5062 6.66663 14.6673 6.82778 14.6673 7.59996V8.39996C14.6673 9.17214 14.5062 9.33329 13.734 9.33329H2.26732C1.49514 9.33329 1.33398 9.17214 1.33398 8.39996V7.59996Z"
                                                      stroke="#6E655E" stroke-linecap="round"/>
                                                <path d="M1.33398 2.26665C1.33398 1.49447 1.49514 1.33331 2.26732 1.33331H13.734C14.5062 1.33331 14.6673 1.49447 14.6673 2.26665V3.06665C14.6673 3.83882 14.5062 3.99998 13.734 3.99998H2.26732C1.49514 3.99998 1.33398 3.83882 1.33398 3.06665V2.26665Z"
                                                      stroke="#6E655E" stroke-linecap="round"/>
                                                <path d="M1.33398 12.9333C1.33398 12.1612 1.49514 12 2.26732 12H13.734C14.5062 12 14.6673 12.1612 14.6673 12.9333V13.7333C14.6673 14.5055 14.5062 14.6667 13.734 14.6667H2.26732C1.49514 14.6667 1.33398 14.5055 1.33398 13.7333V12.9333Z"
                                                      stroke="#6E655E" stroke-linecap="round"/>
                                            </svg>
                                        </li>
                                        <li class="tf-archive-view-item tf-archive-grid-view <?php echo $tf_defult_views == "grid" ? esc_attr('active') : ''; ?>" data-id="grid-view">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M1.33398 12C1.33398 10.973 1.33398 10.4595 1.56514 10.0823C1.69448 9.87127 1.87194 9.69381 2.08301 9.56447C2.46021 9.33331 2.97369 9.33331 4.00065 9.33331C5.02761 9.33331 5.54109 9.33331 5.9183 9.56447C6.12936 9.69381 6.30682 9.87127 6.43616 10.0823C6.66732 10.4595 6.66732 10.973 6.66732 12C6.66732 13.0269 6.66732 13.5404 6.43616 13.9176C6.30682 14.1287 6.12936 14.3062 5.9183 14.4355C5.54109 14.6666 5.02761 14.6666 4.00065 14.6666C2.97369 14.6666 2.46021 14.6666 2.08301 14.4355C1.87194 14.3062 1.69448 14.1287 1.56514 13.9176C1.33398 13.5404 1.33398 13.0269 1.33398 12Z"
                                                      stroke="#6E655E" stroke-width="1.2"/>
                                                <path d="M9.33398 12C9.33398 10.973 9.33398 10.4595 9.56514 10.0823C9.69448 9.87127 9.87194 9.69381 10.083 9.56447C10.4602 9.33331 10.9737 9.33331 12.0007 9.33331C13.0276 9.33331 13.5411 9.33331 13.9183 9.56447C14.1294 9.69381 14.3068 9.87127 14.4362 10.0823C14.6673 10.4595 14.6673 10.973 14.6673 12C14.6673 13.0269 14.6673 13.5404 14.4362 13.9176C14.3068 14.1287 14.1294 14.3062 13.9183 14.4355C13.5411 14.6666 13.0276 14.6666 12.0007 14.6666C10.9737 14.6666 10.4602 14.6666 10.083 14.4355C9.87194 14.3062 9.69448 14.1287 9.56514 13.9176C9.33398 13.5404 9.33398 13.0269 9.33398 12Z"
                                                      stroke="#6E655E" stroke-width="1.2"/>
                                                <path d="M1.33398 3.99998C1.33398 2.97302 1.33398 2.45954 1.56514 2.08233C1.69448 1.87127 1.87194 1.69381 2.08301 1.56447C2.46021 1.33331 2.97369 1.33331 4.00065 1.33331C5.02761 1.33331 5.54109 1.33331 5.9183 1.56447C6.12936 1.69381 6.30682 1.87127 6.43616 2.08233C6.66732 2.45954 6.66732 2.97302 6.66732 3.99998C6.66732 5.02694 6.66732 5.54042 6.43616 5.91762C6.30682 6.12869 6.12936 6.30615 5.9183 6.43549C5.54109 6.66665 5.02761 6.66665 4.00065 6.66665C2.97369 6.66665 2.46021 6.66665 2.08301 6.43549C1.87194 6.30615 1.69448 6.12869 1.56514 5.91762C1.33398 5.54042 1.33398 5.02694 1.33398 3.99998Z"
                                                      stroke="#6E655E" stroke-width="1.2"/>
                                                <path d="M9.33398 3.99998C9.33398 2.97302 9.33398 2.45954 9.56514 2.08233C9.69448 1.87127 9.87194 1.69381 10.083 1.56447C10.4602 1.33331 10.9737 1.33331 12.0007 1.33331C13.0276 1.33331 13.5411 1.33331 13.9183 1.56447C14.1294 1.69381 14.3068 1.87127 14.4362 2.08233C14.6673 2.45954 14.6673 2.97302 14.6673 3.99998C14.6673 5.02694 14.6673 5.54042 14.4362 5.91762C14.3068 6.12869 14.1294 6.30615 13.9183 6.43549C13.5411 6.66665 13.0276 6.66665 12.0007 6.66665C10.9737 6.66665 10.4602 6.66665 10.083 6.43549C9.87194 6.30615 9.69448 6.12869 9.56514 5.91762C9.33398 5.54042 9.33398 5.02694 9.33398 3.99998Z"
                                                      stroke="#6E655E" stroke-width="1.2"/>
                                            </svg>
                                        </li>
                                    </ul>
                                </div>

                                <!--Available rooms start -->
                                <div class="tf-archive-hotels archive_ajax_result <?php echo $tf_defult_views == "list" ? esc_attr('tf-layout-list') : esc_attr('tf-layout-grid'); ?>">

                                    <?php
                                    $count = 0;
                                    $locations = [];
                                    while ( $loop->have_posts() ) {
                                        $loop->the_post();

                                        if ( $post_type == 'tf_hotel' ) {
                                            $hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
                                            if ( ! $hotel_meta["featured"] ) {
                                                continue;
                                            }

                                            $count ++;
                                            $map  = ! empty( $hotel_meta['map'] ) ? Helper::tf_data_types( $hotel_meta['map'] ) : '';

                                            $min_price_arr = hotelPricing::instance(get_the_ID())->get_min_price();
                                            $min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
                                            $min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;
                                            $min_discount_type = !empty($min_price_arr['min_discount_type']) ? $min_price_arr['min_discount_type'] : 'none';
                                            $min_discount_amount = !empty($min_price_arr['min_discount_amount']) ? $min_price_arr['min_discount_amount'] : 0;

                                            if ( $min_regular_price != 0 ) {
                                                $price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
                                            } else {
                                                $price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
                                            }

                                            if ( ! empty( $map ) ) {
                                                $lat = $map['latitude'];
                                                $lng = $map['longitude'];
                                                ob_start();
                                                ?>
                                                <div class="tf-map-item">
                                                    <div class="tf-map-item-thumb">
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php
                                                            if ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
                                                                the_post_thumbnail( 'full' );
                                                            } else {
                                                                echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
                                                            }
                                                            ?>
                                                        </a>

                                                        <?php
                                                        if ( ! empty( $min_discount_amount ) ) : ?>
                                                            <div class="tf-map-item-discount">
                                                                <?php echo $min_discount_type == "percent" ? wp_kses_post($min_discount_amount . '%') : wp_kses_post(wc_price( $min_discount_amount )) ?>
                                                                <?php esc_html_e( " Off", "tourfic" ); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="tf-map-item-content">
                                                        <h4>
                                                            <a href="<?php the_permalink(); ?>">
                                                                <?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
                                                            </a>
                                                        </h4>
                                                        <div class="tf-map-item-price">
                                                            <?php echo wp_kses_post(hotelPricing::instance(get_the_ID())->get_min_price_html()); ?>
                                                        </div>
                                                        <?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                                    </div>
                                                </div>
                                                <?php
                                                $infoWindowtext = ob_get_clean();

                                                $locations[ $count ] = [
                                                    'id'      => get_the_ID(),
                                                    'url'	  => get_the_permalink(),
                                                    'lat'     => (float) $lat,
                                                    'lng'     => (float) $lng,
                                                    'price'   => base64_encode( $price_html ),
                                                    'content' => base64_encode( $infoWindowtext )
                                                ];
                                            }

                                            Hotel::tf_hotel_archive_single_item();
                                        } elseif($post_type == 'tf_tours'){
                                            $tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );

                                            if ( ! $tour_meta["tour_as_featured"] ) {
                                                continue;
                                            }
                                            $count ++;
                                            $map            = ! empty( $tour_meta['location'] ) ? Helper::tf_data_types( $tour_meta['location'] ) : '';
                                            $discount_type  = ! empty( $tour_meta['discount_type'] ) ? $tour_meta['discount_type'] : '';
                                            $discount_price = ! empty( $tour_meta['discount_price'] ) ? $tour_meta['discount_price'] : '';

                                            $min_price_arr     = tourPricing::instance( get_the_ID() )->get_min_price();
                                            $min_sale_price    = ! empty( $min_price_arr['min_sale_price'] ) ? $min_price_arr['min_sale_price'] : 0;
                                            $min_regular_price = ! empty( $min_price_arr['min_regular_price'] ) ? $min_price_arr['min_regular_price'] : 0;
                                            $min_discount      = ! empty( $min_price_arr['min_discount'] ) ? $min_price_arr['min_discount'] : 0;

                                            if ( ! empty( $min_discount ) ) {
                                                $price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
                                            } else {
                                                $price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
                                            }

                                            if ( ! empty( $map ) ) {
                                                $lat = $map['latitude'];
                                                $lng = $map['longitude'];
                                                ob_start();
                                                ?>
                                                <div class="tf-map-item">
                                                    <div class="tf-map-item-thumb">
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php
                                                            if ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
                                                                the_post_thumbnail( 'full' );
                                                            } else {
                                                                echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
                                                            }
                                                            ?>
                                                        </a>

                                                        <?php if ( $discount_type !== 'none' && ! empty( $discount_price ) ) : ?>
                                                            <div class="tf-map-item-discount">
                                                                <?php echo $discount_type == "percent" ? wp_kses_post($discount_price . '%') : wp_kses_post(wc_price( $discount_price )) ?>
                                                                <?php esc_html_e( " Off", "tourfic" ); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="tf-map-item-content">
                                                        <h4>
                                                            <a href="<?php the_permalink(); ?>">
                                                                <?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
                                                            </a>
                                                        </h4>
                                                        <div class="tf-map-item-price">
                                                            <?php echo wp_kses_post(tourPricing::instance( get_the_ID() )->get_min_price_html()); ?>
                                                        </div>
                                                        <?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                                    </div>
                                                </div>
                                                <?php
                                                $infoWindowtext = ob_get_clean();

                                                $locations[ $count ] = [
                                                    'id'      => get_the_ID(),
                                                    'url'	  => get_the_permalink(),
                                                    'lat'     => (float) $lat,
                                                    'lng'     => (float) $lng,
                                                    'price'   => base64_encode( $price_html ),
                                                    'content' => base64_encode( $infoWindowtext )
                                                ];
                                            }

                                            Tour::tf_tour_archive_single_item();
                                        } elseif($post_type == 'tf_apartment'){
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
                                                <div class="tf-map-item">
                                                    <div class="tf-map-item-thumb">
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php
                                                            if (!empty(wp_get_attachment_url(get_post_thumbnail_id(), 'tf_gallery_thumb'))) {
                                                                the_post_thumbnail('full');
                                                            } else {
                                                                echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
                                                            }
                                                            ?>
                                                        </a>

                                                        <?php
                                                        if (!empty($discount_price)) : ?>
                                                            <div class="tf-map-item-discount">
                                                                <?php echo $discount_type == "percent" ? wp_kses_post($discount_price . '%') : wp_kses_post(wc_price($discount_price)) ?>
                                                                <?php esc_html_e(" Off", "tourfic"); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="tf-map-item-content">
                                                        <h4>
                                                            <a href="<?php the_permalink(); ?>">
                                                                <?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
                                                            </a>
                                                        </h4>
                                                        <div class="tf-map-item-price">
                                                            <?php echo wp_kses_post(apartmentPricing::instance(get_the_ID())->get_min_price_html()); ?>
                                                        </div>
                                                        <?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                                    </div>
                                                </div>
                                                <?php
                                                $infoWindowtext = ob_get_clean();

                                                $locations[$count] = [
                                                    'id' => get_the_ID(),
                                                    'url'	  => get_the_permalink(),
                                                    'lat' => (float)$lat,
                                                    'lng' => (float)$lng,
                                                    'price' => base64_encode($price_html),
                                                    'content' => base64_encode($infoWindowtext)
                                                ];
                                            }
                                            echo apply_filters("tf_apartment_archive_single_featured_card_design_one", Apartment::tf_apartment_archive_single_item());
                                        }
                                    }
                                    while ( $loop->have_posts() ) {
                                        $loop->the_post();

                                        if ( $post_type == 'tf_hotel' ) {
                                            $hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
                                            if ( !empty($hotel_meta["featured"]) && $hotel_meta["featured"] ) {
                                                continue;
                                            }

                                            $count ++;
                                            $map  = ! empty( $hotel_meta['map'] ) ? Helper::tf_data_types( $hotel_meta['map'] ) : '';

                                            $min_price_arr = hotelPricing::instance(get_the_ID())->get_min_price();
                                            $min_sale_price = !empty($min_price_arr['min_sale_price']) ? $min_price_arr['min_sale_price'] : 0;
                                            $min_regular_price = !empty($min_price_arr['min_regular_price']) ? $min_price_arr['min_regular_price'] : 0;
                                            $min_discount_type = !empty($min_price_arr['min_discount_type']) ? $min_price_arr['min_discount_type'] : 'none';
                                            $min_discount_amount = !empty($min_price_arr['min_discount_amount']) ? $min_price_arr['min_discount_amount'] : 0;

                                            if ( $min_regular_price != 0 ) {
                                                $price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
                                            } else {
                                                $price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
                                            }

                                            if ( ! empty( $map ) ) {
                                                $lat = $map['latitude'];
                                                $lng = $map['longitude'];
                                                ob_start();
                                                ?>
                                                <div class="tf-map-item">
                                                    <div class="tf-map-item-thumb">
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php
                                                            if ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
                                                                the_post_thumbnail( 'full' );
                                                            } else {
                                                                echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
                                                            }
                                                            ?>
                                                        </a>

                                                        <?php
                                                        if ( ! empty( $min_discount_amount ) ) : ?>
                                                            <div class="tf-map-item-discount">
                                                                <?php echo $min_discount_type == "percent" ? wp_kses_post($min_discount_amount . '%') : wp_kses_post(wc_price( $min_discount_amount )) ?>
                                                                <?php esc_html_e( " Off", "tourfic" ); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="tf-map-item-content">
                                                        <h4>
                                                            <a href="<?php the_permalink(); ?>">
                                                                <?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
                                                            </a>
                                                        </h4>
                                                        <div class="tf-map-item-price">
                                                            <?php echo wp_kses_post(hotelPricing::instance(get_the_ID())->get_min_price_html()); ?>
                                                        </div>
                                                        <?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                                    </div>
                                                </div>
                                                <?php
                                                $infoWindowtext = ob_get_clean();

                                                $locations[ $count ] = [
                                                    'id'      => get_the_ID(),
                                                    'url'	  => get_the_permalink(),
                                                    'lat'     => (float) $lat,
                                                    'lng'     => (float) $lng,
                                                    'price'   => base64_encode( $price_html ),
                                                    'content' => base64_encode( $infoWindowtext )
                                                ];
                                            }

                                            Hotel::tf_hotel_archive_single_item();
                                        } elseif($post_type == 'tf_tours'){
                                            $tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );

                                            if ( !empty($tour_meta["tour_as_featured"]) && $tour_meta["tour_as_featured"] ) {
                                                continue;
                                            }
                                            $count ++;
                                            $map            = ! empty( $tour_meta['location'] ) ? Helper::tf_data_types( $tour_meta['location'] ) : '';
                                            $discount_type  = ! empty( $tour_meta['discount_type'] ) ? $tour_meta['discount_type'] : '';
                                            $discount_price = ! empty( $tour_meta['discount_price'] ) ? $tour_meta['discount_price'] : '';

                                            $min_price_arr     = tourPricing::instance( get_the_ID() )->get_min_price();
                                            $min_sale_price    = ! empty( $min_price_arr['min_sale_price'] ) ? $min_price_arr['min_sale_price'] : 0;
                                            $min_regular_price = ! empty( $min_price_arr['min_regular_price'] ) ? $min_price_arr['min_regular_price'] : 0;
                                            $min_discount      = ! empty( $min_price_arr['min_discount'] ) ? $min_price_arr['min_discount'] : 0;

                                            if ( ! empty( $min_discount ) ) {
                                                $price_html = wc_format_sale_price( $min_regular_price, $min_sale_price );
                                            } else {
                                                $price_html = wp_kses_post( wc_price( $min_sale_price ) ) . " ";
                                            }

                                            if ( ! empty( $map ) ) {
                                                $lat = $map['latitude'];
                                                $lng = $map['longitude'];
                                                ob_start();
                                                ?>
                                                <div class="tf-map-item">
                                                    <div class="tf-map-item-thumb">
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php
                                                            if ( ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ) {
                                                                the_post_thumbnail( 'full' );
                                                            } else {
                                                                echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
                                                            }
                                                            ?>
                                                        </a>

                                                        <?php if ( $discount_type !== 'none' && ! empty( $discount_price ) ) : ?>
                                                            <div class="tf-map-item-discount">
                                                                <?php echo $discount_type == "percent" ? wp_kses_post($discount_price . '%') : wp_kses_post(wc_price( $discount_price )) ?>
                                                                <?php esc_html_e( " Off", "tourfic" ); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="tf-map-item-content">
                                                        <h4>
                                                            <a href="<?php the_permalink(); ?>">
                                                                <?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
                                                            </a>
                                                        </h4>
                                                        <div class="tf-map-item-price">
                                                            <?php echo wp_kses_post(tourPricing::instance( get_the_ID() )->get_min_price_html()); ?>
                                                        </div>
                                                        <?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                                    </div>
                                                </div>
                                                <?php
                                                $infoWindowtext = ob_get_clean();

                                                $locations[ $count ] = [
                                                    'id'      => get_the_ID(),
                                                    'url'	  => get_the_permalink(),
                                                    'lat'     => (float) $lat,
                                                    'lng'     => (float) $lng,
                                                    'price'   => base64_encode( $price_html ),
                                                    'content' => base64_encode( $infoWindowtext )
                                                ];
                                            }

                                            Tour::tf_tour_archive_single_item();
                                        } elseif($post_type == 'tf_apartment'){
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
                                                <div class="tf-map-item">
                                                    <div class="tf-map-item-thumb">
                                                        <a href="<?php the_permalink(); ?>">
                                                            <?php
                                                            if (!empty(wp_get_attachment_url(get_post_thumbnail_id(), 'tf_gallery_thumb'))) {
                                                                the_post_thumbnail('full');
                                                            } else {
                                                                echo '<img src="' . esc_url(TF_ASSETS_APP_URL . "images/feature-default.jpg") . '" class="attachment-full size-full wp-post-image">';
                                                            }
                                                            ?>
                                                        </a>

                                                        <?php
                                                        if (!empty($discount_price)) : ?>
                                                            <div class="tf-map-item-discount">
                                                                <?php echo $discount_type == "percent" ? wp_kses_post($discount_price . '%') : wp_kses_post(wc_price($discount_price)) ?>
                                                                <?php esc_html_e(" Off", "tourfic"); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="tf-map-item-content">
                                                        <h4>
                                                            <a href="<?php the_permalink(); ?>">
                                                                <?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title(), 30 ) ) ?>
                                                            </a>
                                                        </h4>
                                                        <div class="tf-map-item-price">
                                                            <?php echo wp_kses_post(apartmentPricing::instance(get_the_ID())->get_min_price_html()); ?>
                                                        </div>
                                                        <?php \Tourfic\App\TF_Review::tf_archive_single_rating(); ?>
                                                    </div>
                                                </div>
                                                <?php
                                                $infoWindowtext = ob_get_clean();

                                                $locations[$count] = [
                                                    'id' => get_the_ID(),
                                                    'url'	  => get_the_permalink(),
                                                    'lat' => (float)$lat,
                                                    'lng' => (float)$lng,
                                                    'price' => base64_encode($price_html),
                                                    'content' => base64_encode($infoWindowtext)
                                                ];
                                            }
                                            echo apply_filters("tf_apartment_archive_single_featured_card_design_one", Apartment::tf_apartment_archive_single_item());
                                        }
                                    }
                                    wp_reset_query();
                                    ?>
                                    <div id="map-datas" style="display: none"><?php echo array_filter($locations) ? wp_json_encode(array_values($locations)) : wp_json_encode([]); ?></div>
                                    <div class="tf-pagination-bar">
                                        <?php Helper::tourfic_posts_navigation(); ?>
                                    </div>
                                </div>
                                <!-- Available rooms end -->

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
    <?php else: ?>
        <div id="map-datas" style="display: none"><?php echo wp_json_encode([]); ?></div>
        <div class="tf-nothing-found" data-post-count="0"><?php echo esc_html__("No Hotels Found!", "tourfic"); ?></div>
    <?php endif; ?>

<?php }else { ?>
<div class="tf_search_result">
    <div class="tf-action-top">
        <div class="tf-result-counter-info">
            <span class="tf-counter-title"><?php echo esc_html__( 'Total Results', 'tourfic' ); ?> </span>
            <span><?php echo '('; ?> </span>
            <div class="tf-total-results">
                <span><?php echo esc_html($total_posts); ?> </span>
            </div>
            <span><?php echo ')'; ?> </span>
        </div>
        <div class="tf-list-grid">
            <a href="#list-view" data-id="list-view" class="change-view <?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('List View', 'tourfic'); ?>"><i class="fas fa-list"></i></a>
            <a href="#grid-view" data-id="grid-view" class="change-view <?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('Grid View', 'tourfic'); ?>"><i class="fas fa-border-all"></i></a>
            <div class="tf-sorting-selection-warper">
                <form class="tf-archive-ordering" method="get">
                    <select class="tf-orderby" name="tf-orderby" id="tf-orderby">
                        <option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
                        <option value="enquiry"><?php echo esc_html__( 'Sort By Recommended', 'tourfic' ); ?></option>
                        <option value="order"><?php echo esc_html__( 'Sort By Popularity', 'tourfic' ); ?></option>
                        <option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
                        <option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
                        <option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
                        <option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
                    </select>
                </form>
            </div>
        </div>
    </div>
    <div class="archive_ajax_result <?php echo $tf_defult_views=="grid" ? esc_attr('tours-grid') : '' ?>">
        <?php
        if ( $loop->have_posts() ) {          
            while ( $loop->have_posts() ) {

                $loop->the_post(); 

                if( $post_type == 'tf_hotel' ){
	                Hotel::tf_hotel_archive_single_item();
                } elseif( $post_type == 'tf_tours' ) {
                    Tour::tf_tour_archive_single_item();
                } elseif( $post_type == 'tf_apartment' ) {
                    Apartment::tf_apartment_archive_single_item();
                }
                    
            }           
        } else {
            echo '<div class="tf-nothing-found" data-post-count="0">' .esc_html__("Nothing Found!", "tourfic"). '</div>';
        }
        ?>
    </div>
    <div class="tf_posts_navigation">
        <?php Helper::tourfic_posts_navigation(); ?>
    </div>
</div>

<?php
}
?>