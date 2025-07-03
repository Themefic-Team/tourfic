<?php 
use \Tourfic\Classes\Helper;
$tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_view'] : 'grid';

$tf_car_arc_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] : '';

?>
<div class="tf-archive-template__one">
    <div class="tf-archive-car-banner" style="<?php echo !empty($tf_car_arc_banner) ? 'background-image: url('.esc_url($tf_car_arc_banner).')' : ''; ?>">
        <div class="tf-banner-content tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-direction-column">
            <h1><?php esc_html_e("Cars", "tourfic"); ?></h1>
        </div>
    </div>

    <div class="tf-container">
        <div class="tf-container-inner">
            <div class="tf-archive-car-details-warper">

                <?php Helper::tf_archive_sidebar_search_form('tf_carrental'); ?>

                <div class="tf-archive-header tf-flex tf-flex-space-bttn tf-flex-align-center tf-mb-30">
                    <div class="tf-archive-view">
                        <ul class="tf-flex tf-flex-gap-16">
                            <li class="<?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" data-view="grid"><i class="ri-layout-grid-line"></i></li>
                            <li class="<?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" data-view="list"><i class="ri-list-check"></i></li>
                        </ul>
                    </div>
                    <?php 
                    $post_count = $GLOBALS['wp_query']->post_count;
                    ?>
                    <div class="tf-total-result-bar">
                        <span>
                            <?php echo esc_html__( 'Total Results ', 'tourfic' ); ?>
                        </span>
                        <span><?php echo ' ('; ?> </span>
						<div class="tf-total-results">
							<span><?php echo esc_html( $post_count ); ?> </span>
						</div>
						<span><?php echo ')'; ?> </span>
                    </div>
                </div>
                <div class="tf-car-details-column tf-flex tf-flex-gap-32">
                    
                    <div class="tf-car-archive-sidebar">
                        <div class="tf-sidebar-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                            <h4><?php esc_html_e("Filter", "tourfic") ?></h4>
                            <button class="filter-reset-btn"><?php esc_html_e("Reset", "tourfic"); ?></button>
                        </div>
                        
                        <?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
                            <?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
                        <?php } ?>

                    </div>

                    <div class="tf-car-archive-result">
                        <?php do_action("tf_car_archive_card_items_before"); ?>
                        <div class="tf-car-result archive_ajax_result tf-flex tf-flex-gap-32 <?php echo $tf_defult_views=="list" ? esc_attr('list-view') : esc_attr('grid-view'); ?>">
                            
                            <?php
                            if ( have_posts() ) {
                                while ( have_posts() ) {
                                    the_post();
                                    $car_meta = get_post_meta( get_the_ID() , 'tf_carrental_opt', true );
                                    if ( !empty( $car_meta[ "car_as_featured" ] ) && $car_meta[ "car_as_featured" ] == 1 ) {
                                        tf_car_archive_single_item();
                                    }
                                }
                                while ( have_posts() ) {
                                    the_post();
                                    $car_meta = get_post_meta( get_the_ID() , 'tf_carrental_opt', true );
                                    if ( empty($car_meta[ "car_as_featured" ]) ) {
                                        tf_car_archive_single_item();
                                    }
                                }
                            } else {
                                echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Cars Found!", "tourfic"). '</div>';
                            }
                            ?>

                            <div class="tf-pagination-bar">
                                <?php Helper::tourfic_posts_navigation(); ?>
                            </div>
                        </div>
                        <?php do_action("tf_car_archive_card_items_after"); ?>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>