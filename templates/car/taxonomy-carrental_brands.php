<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

get_header();

if ( !Helper::tf_is_woo_active() ) {
	?>
    <div class="tf-container">
        <div class="tf-notice tf-notice-danger">
			<?php esc_html_e( 'Please install and activate WooCommerce plugin to use this feature.', 'tourfic' ); ?>
        </div>
    </div>
	<?php
	get_footer();
	return;
}

$term = get_queried_object();
$post_type = 'tf_carrental';
$taxonomy = $term->taxonomy;
$taxonomy_name = $term->name;
$taxonomy_slug = $term->slug;
$max = '2';

$tf_car_brand_meta      = get_term_meta( $term->term_id, 'tf_carrental_brand', true );
$tf_car_arc_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] : '';
$tf_car_brand_banner = ! empty( $tf_car_brand_meta['image'] ) ? $tf_car_brand_meta['image'] : $tf_car_arc_banner;

$tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_view'] : 'grid';

?>
<div class="tf-archive-template__one">
    <div class="tf-archive-car-banner" style="<?php echo !empty($tf_car_brand_banner) ? 'background-image: url('.esc_url($tf_car_brand_banner).')' : ''; ?>">
        <div class="tf-banner-content tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-direction-column">
            <h1><?php echo esc_html($taxonomy_name); ?></h1>
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
                                echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Tours Found!", "tourfic"). '</div>';
                            }
                            ?>

                        </div>
                        <?php do_action("tf_car_archive_card_items_after"); ?>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer('tourfic');