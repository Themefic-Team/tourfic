<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use Tourfic\Classes\Tour\Components\Archive\Listings;

$tf_tour_arc_banner = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_3_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_3_bannar'] : '';
?>

<div class="tf-archive-template__three">
    <div class="tf-content-wrapper">
        <?php do_action('tf_before_container'); ?>

        <div class="tf-archive-search-form tf-booking-form-wrapper" style="<?php echo !empty($tf_tour_arc_banner) ? 'background-image: url('.esc_url($tf_tour_arc_banner).')' : ''; ?>">
            <div class="tf-container">
                <?php Helper::tf_archive_sidebar_search_form('tf_tours'); ?>
            </div>
        </div>

        <?php Listings::render_design_3(); ?>
    </div>
</div>