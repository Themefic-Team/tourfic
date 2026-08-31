<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\App\Templates\Components\Shared\Archive\Banner;
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

$tourfic_term = get_queried_object();
$tourfic_post_type = 'tf_carrental';
$tourfic_taxonomy = $tourfic_term->taxonomy;
$tourfic_taxonomy_name = $tourfic_term->name;
$tourfic_taxonomy_slug = $tourfic_term->slug;
$tourfic_max = '2';

$tourfic_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_view'] : 'grid';
$tourfic_car_search_context = function_exists( 'tourfic_get_car_archive_search_context' ) ? tourfic_get_car_archive_search_context() : array(
	'pickup'       => '',
	'dropoff'      => '',
	'pickup_date'  => '',
	'dropoff_date' => '',
	'pickup_time'  => '',
	'dropoff_time' => '',
);

?>
<div class="tf-archive-template__one">
    <?php Banner::render(); ?>

    <div class="tf-container">
        <div class="tf-container-inner">
            <div class="tf-archive-car-details-warper">

                <?php Helper::tf_archive_sidebar_search_form('tf_carrental'); ?>

                <div class="tf-archive-header tf-flex tf-flex-space-bttn tf-flex-align-center tf-mb-30">
                    <div class="tf-archive-view">
                        <ul class="tf-flex tf-flex-gap-16">
                            <li class="<?php echo $tourfic_defult_views=="grid" ? esc_attr('active') : ''; ?>" data-view="grid"><i class="ri-layout-grid-line"></i></li>
                            <li class="<?php echo $tourfic_defult_views=="list" ? esc_attr('active') : ''; ?>" data-view="list"><i class="ri-list-check"></i></li>
                        </ul>
                    </div>
                    <?php 
                    $tourfic_post_count = $GLOBALS['wp_query']->post_count;
                    ?>
                    <div class="tf-total-result-bar">
                        <span>
                            <?php echo esc_html__( 'Total Results ', 'tourfic' ); ?>
                        </span>
                        <span><?php echo ' ('; ?> </span>
						<div class="tf-total-results">
							<span><?php echo esc_html( $tourfic_post_count ); ?> </span>
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
                        <?php do_action("tourfic_car_archive_card_items_before"); ?>
	                        <div class="tf-car-result archive_ajax_result tf-flex tf-flex-gap-32 <?php echo $tourfic_defult_views=="list" ? esc_attr('list-view') : esc_attr('grid-view'); ?>">
	                            
	                            <?php
	                            if ( have_posts() ) {
	                                while ( have_posts() ) {
	                                    the_post();
	                                    $tourfic_car_meta = get_post_meta( get_the_ID() , 'tf_carrental_opt', true );
	                                    $tourfic_is_car_featured = is_array( $tourfic_car_meta ) && ! empty( $tourfic_car_meta['car_as_featured'] );
	                                    if ( $tourfic_is_car_featured ) {
	                                        tourfic_car_archive_single_item(
												$tourfic_car_search_context['pickup'],
												$tourfic_car_search_context['dropoff'],
												$tourfic_car_search_context['pickup_date'],
												$tourfic_car_search_context['dropoff_date'],
												$tourfic_car_search_context['pickup_time'],
												$tourfic_car_search_context['dropoff_time']
											);
	                                    }
	                                }

	                                rewind_posts();

	                                while ( have_posts() ) {
	                                    the_post();
	                                    $tourfic_car_meta = get_post_meta( get_the_ID() , 'tf_carrental_opt', true );
	                                    $tourfic_is_car_featured = is_array( $tourfic_car_meta ) && ! empty( $tourfic_car_meta['car_as_featured'] );
	                                    if ( ! $tourfic_is_car_featured ) {
	                                        tourfic_car_archive_single_item(
												$tourfic_car_search_context['pickup'],
												$tourfic_car_search_context['dropoff'],
												$tourfic_car_search_context['pickup_date'],
												$tourfic_car_search_context['dropoff_date'],
												$tourfic_car_search_context['pickup_time'],
												$tourfic_car_search_context['dropoff_time']
											);
	                                    }
	                                }
	                            } else {
                                echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Tours Found!", "tourfic"). '</div>';
                            }
                            ?>

                        </div>
                        <?php do_action("tourfic_car_archive_card_items_after"); ?>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer('tourfic');
