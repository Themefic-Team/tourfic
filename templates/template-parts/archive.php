<?php 
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use Tourfic\Classes\Tour\Tour;
use \Tourfic\Classes\Hotel\Hotel;
use \Tourfic\Classes\Apartment\Apartment;

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
$total_posts = $loop->found_posts;

$tf_tour_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour-archive'] : 'design-1';
$tf_hotel_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';
$tf_apartment_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment-archive'] : 'default';

// Gird or List View
if($post_type == "tf_hotel"){
    $tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_view'] : 'list';
}elseif($post_type == "tf_apartment"){
    $tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_view'] : 'list';
}else{
    $tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_view'] : 'list';
}

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
elseif( ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template=="design-2" ) || ( $post_type == "tf_tours" && $tf_tour_arc_selected_template=="design-2" ) || ( $post_type == "tf_apartment" && $tf_apartment_arc_selected_template=="design-1" ) ){ ?>

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