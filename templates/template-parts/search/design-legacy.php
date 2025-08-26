<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
$tf_car_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car-archive'] : 'design-1';
$tf_car_arc_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] : '';
if ( ( ! empty( $_GET['type'] ) && $_GET['type'] == "tf_carrental" && $tf_car_arc_selected_template == "design-1" ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<div class="tf-archive-template__one">
    <div class="tf-archive-car-banner" style="<?php echo !empty($tf_car_arc_banner) ? 'background-image: url('.esc_url($tf_car_arc_banner).')' : ''; ?>">
        <div class="tf-banner-content tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-direction-column">
            <h1><?php esc_html_e("Search results", "tourfic"); ?></h1>
        </div>
    </div>

    <div class="tf-container">
        <div class="tf-container-inner">
            <div class="tf-archive-car-details-warper">

                <?php Helper::tf_archive_sidebar_search_form('tf_carrental'); ?>


                <?php echo do_shortcode("[tf_search_result]"); ?>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="tf-main-wrapper search-result-wrapper tf-archive-template__legacy" data-fullwidth="true">
    <?php do_action( 'tf_before_container' ); ?>
	<div class="tf-container">
        <div class="search-result-inner">
            <!-- Start Content -->           
			<div class="tf-search-left">
				<?php echo do_shortcode("[tf_search_result]"); ?>
			</div>
			<!-- End Content -->

			<!-- Start Sidebar -->
			<div class="tf-search-right">
				<?php Helper::tf_search_result_sidebar_form( 'archive' ); ?>
			</div>
			<!-- End Sidebar -->
		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>
<?php endif; ?>
