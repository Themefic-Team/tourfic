<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;
?>
<div class="tf-archive-template__three">

    <div class="tf-content-wrapper">

        <?php
        use \Tourfic\Classes\Helper;
        use Tourfic\Classes\Apartment\Components\Archive\Listings;

        do_action('tf_before_container');
        $tf_apartment_arc_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_2_bannar'] : '';
        ?>

        <div class="tf-archive-search-form tf-booking-form-wrapper" style="<?php echo !empty($tf_apartment_arc_banner) ? 'background-image: url('.esc_url($tf_apartment_arc_banner).')' : ''; ?>">
            <div class="tf-container">
                <?php Helper::tf_archive_sidebar_search_form('tf_apartment'); ?>
            </div>
        </div>

        <?php Listings::render_design_2(); ?>
    </div>
</div>