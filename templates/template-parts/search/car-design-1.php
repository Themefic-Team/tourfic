<?php
use \Tourfic\Classes\Helper;
$tf_car_arc_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] : '';
?>
<div class="tf-archive-car-section">
    <div class="tf-archive-car-banner" style="<?php echo !empty($tf_car_arc_banner) ? 'background-image: url('.esc_url($tf_car_arc_banner).')' : ''; ?>">
        <div class="tf-banner-content tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-direction-column">
            <h1><?php esc_html_e("Search results", "tourfic"); ?></h1>
        </div>
    </div>

    <div class="tf-car-template-container">
        <div class="tf-container-inner">
            <div class="tf-archive-car-details-warper">

                <?php Helper::tf_archive_sidebar_search_form('tf_carrental'); ?>


                <?php echo do_shortcode("[tf_search_result]"); ?>
            </div>
        </div>
    </div>
</div>