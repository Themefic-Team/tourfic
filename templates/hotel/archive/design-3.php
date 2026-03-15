<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;
use Tourfic\Classes\Hotel\Components\Archive\Listings;
?>
<div class="tf-archive-template__three">

    <div class="tf-content-wrapper">
        <?php

        use \Tourfic\Classes\Helper;
        use \Tourfic\Classes\Hotel\Hotel;
        use \Tourfic\Classes\Hotel\Pricing;

        do_action('tf_before_container');
        $post_count = $GLOBALS['wp_query']->post_count;
        $tf_hotel_arc_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_3_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_3_bannar'] : '';
        $tf_map_settings = !empty(Helper::tfopt('google-page-option')) ? Helper::tfopt('google-page-option') : "default";
        $tf_map_api = !empty(Helper::tfopt('tf-googlemapapi')) ? Helper::tfopt('tf-googlemapapi') : '';
        $hotels_name = apply_filters( 'tf_hotel_post_type_name_change_plural', esc_html__( 'Hotels', 'tourfic' ) );
        ?>

        <div class="tf-archive-search-form tf-booking-form-wrapper" style="<?php echo !empty($tf_hotel_arc_banner) ? 'background-image: url('.esc_url($tf_hotel_arc_banner).')' : ''; ?>">
            <div class="tf-container">
                <?php Helper::tf_archive_sidebar_search_form('tf_hotel'); ?>
            </div>
        </div>

        <?php Listings::render_design_3(); ?>
    </div>
    <!--Content section end -->

</div>