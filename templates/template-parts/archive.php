<?php 
// don't load directly
defined( 'ABSPATH' ) || exit;

$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

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
);

$loop = new WP_Query( $args );
?>

<div class="tf_search_result">
    <div class="tf-action-top">
        <div class="tf-list-grid">
            <a href="#list-view" data-id="list-view" class="change-view" title="List View"><?php echo tourfic_get_svg('list_view'); ?></a>
            <a href="#grid-view" data-id="grid-view" class="change-view" title="Grid View"><?php echo tourfic_get_svg('grid_view'); ?></a>
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
            echo '<div class="tf-nothing-found">Nothing Found!</div>';
        }
        ?>
    </div>
    <div class="tf_posts_navigation">
        <?php tourfic_posts_navigation(); ?>
    </div>

    </div>

<?php

?>