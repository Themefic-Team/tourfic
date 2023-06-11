<?php 
// don't load directly
defined( 'ABSPATH' ) || exit;

$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
$post_per_page = tfopt('posts_per_page') ? tfopt('posts_per_page') : 10;
$args = array(
    'post_type' => $post_type,
    'orderby'   => 'date',
    'order'     => 'DESC',
    'tax_query' => array(
        array (
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => $taxonomy_slug,
        )
    ),
    'post_status'    => 'publish',
    'paged'          => $paged,
    //'posts_per_page' => $post_per_page
);

$loop = new WP_Query( $args );
$total_posts = $loop->found_posts;
if( ( $post_type == "tf_hotel" && ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] ) && tf_data_types(tfopt( 'tf-template' ))['hotel-archive']=="design-1" ) || ( $post_type == "tf_tours" && ! empty( tf_data_types(tfopt( 'tf-template' ))['tour-archive'] ) && tf_data_types(tfopt( 'tf-template' ))['tour-archive']=="design-1" ) ){
?>
<div class="tf-column tf-page-content tf-archive-left tf-result-previews">
<!-- Search Head Section -->
    <div class="tf-archive-head tf-flex tf-flex-align-center tf-flex-space-bttn">
        <div class="tf-search-result tf-flex">
            <span class="tf-counter-title"><?php echo __( 'Total Results ', 'tourfic' ); ?> </span>
            <span><?php echo ' ('; ?> </span>
            <div class="tf-total-results">
                <span><?php echo $total_posts; ?> </span>
            </div>
            <span><?php echo ')'; ?> </span>
        </div>
        <?php 
        if($post_type == "tf_hotel"){
            $tf_defult_views = ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel_archive_view'] ) ? tf_data_types(tfopt( 'tf-template' ))['hotel_archive_view'] : 'list';
        }else{
            $tf_defult_views = ! empty( tf_data_types(tfopt( 'tf-template' ))['tour_archive_view'] ) ? tf_data_types(tfopt( 'tf-template' ))['tour_archive_view'] : 'list';
        }
        ?>
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
            <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
        </div>
    </div>
    <div class="tf-search-results-list tf-mt-30">
        <div class="archive_ajax_result tf-item-cards tf-flex <?php echo $tf_defult_views=="list" ? esc_attr('tf-layout-list') : esc_attr('tf-layout-grid'); ?>">

        <?php
        if ( have_posts() ) {
            while ( have_posts() ) {
                the_post();
                if( $post_type == 'tf_hotel' ){
                    tf_hotel_archive_single_item();
                } elseif( $post_type == 'tf_tours' ) {
                    tf_tour_archive_single_item();
                }
            }
        } else {
            echo '<div class="tf-nothing-found" data-post-count="0" >' .__("No Tours Found!", "tourfic"). '</div>';
        }
        ?>
            <div class="tf-pagination-bar">
                <?php tourfic_posts_navigation(); ?>
            </div>
        </div>
    </div>
</div>
<?php }else { ?>
<div class="tf_search_result">
    <div class="tf-action-top">
        <div class="tf-total-results">
            <span><?php echo esc_html__( 'Total Results ', 'tourfic' ) . '(' . $total_posts . ')'; ?> </span>
        </div>
        <div class="tf-list-grid">
            <a href="#list-view" data-id="list-view" class="change-view" title="<?php _e('List View', 'tourfic'); ?>"><i class="fas fa-list"></i></a>
            <a href="#grid-view" data-id="grid-view" class="change-view" title="<?php _e('Grid View', 'tourfic'); ?>"><i class="fas fa-border-all"></i></a>
        </div>
    </div>
    <div class="archive_ajax_result">
        <?php
        if ( $loop->have_posts() ) {          
            while ( $loop->have_posts() ) {

                $loop->the_post(); 

                if( $post_type == 'tf_hotel' ){
                    tf_hotel_archive_single_item();
                } elseif( $post_type == 'tf_tours' ) {
                    tf_tour_archive_single_item();
                }
                    
            }           
        } else {
            echo '<div class="tf-nothing-found" data-post-count="0">' .__("Nothing Found!", "tourfic"). '</div>';
        }
        ?>
    </div>
    <div class="tf_posts_navigation">
        <?php tourfic_posts_navigation(); ?>
    </div>
</div>

<?php
}
?>