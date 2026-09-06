<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
$tourfic_car_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car-archive'] : 'design-1';
$tourfic_car_arc_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] : '';
$tourfic_search_request = tourfic_get_public_search_request();
$tourfic_search_type    = isset( $tourfic_search_request['type'] ) ? $tourfic_search_request['type'] : '';
if ( 'tf_carrental' === $tourfic_search_type && $tourfic_car_arc_selected_template == "design-1" ) :
?>
<div class="tf-archive-template__one">
    <div class="tf-archive-car-banner" style="<?php echo !empty($tourfic_car_arc_banner) ? 'background-image: url('.esc_url($tourfic_car_arc_banner).')' : ''; ?>">
        <div class="tf-banner-content tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-direction-column">
            <h1><?php esc_html_e("Search results", "tourfic"); ?></h1>
        </div>
    </div>

    <div class="tf-container">
        <div class="tf-container-inner">
            <div class="tf-archive-car-details-warper">

                <?php Helper::tf_archive_sidebar_search_form('tf_carrental'); ?>


                <?php echo do_shortcode( '[tourfic_search_result]' ); ?>
            </div>
        </div>
    </div>
</div>
<?php else: 
    ob_start();
    ?>

    <div class="tf-main-wrapper search-result-wrapper tf-archive-template__legacy" data-fullwidth="true">
        <?php do_action( 'tourfic_before_container' ); ?>
        <div class="tf-container">
            <div class="search-result-inner">
                <!-- Start Content -->           
                <div class="tf-search-left">
                    <?php echo do_shortcode( '[tourfic_search_result]' ); ?>
                </div>
                <!-- End Content -->

                <!-- Start Sidebar -->
                <div class="tf-search-right">
                    <?php Helper::tf_search_result_sidebar_form( 'archive' ); ?>
                </div>
                <!-- End Sidebar -->
            </div>
        </div>
        <?php do_action( 'tourfic_after_container' ); ?>
    </div>
<?php echo wp_kses( apply_filters( 'tourfic_search_result_legacy_template', ob_get_clean() ), tourfic_custom_wp_kses_allow_tags() ); ?>
<?php endif; ?>
