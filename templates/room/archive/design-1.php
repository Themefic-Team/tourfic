<?php

use \Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Components\Archive\Listings;
use Tourfic\Classes\Room\Room;

$tf_room_arc_banner = ! empty(Helper::tf_data_types(Helper::tfopt('tf-template'))['room_archive_design_1_bannar']) ?  Helper::tf_data_types(Helper::tfopt('tf-template'))['room_archive_design_1_bannar'] : '';
$tf_room_arc_banner = !empty($tf_room_arc_banner) ? $tf_room_arc_banner : TF_ASSETS_APP_URL . '/images/room-hero-banner.jpg';
?>

<div class="tf-archive-template__one sp-0">
    <div class="tf-archive-room-banner" style="<?php echo !empty($tf_room_arc_banner) ? 'background-image: url(' . esc_url($tf_room_arc_banner) . ')' : ''; ?>">
       <div class="tf-container">
            <div class="tf-banner-content tf-flex tf-flex-justify-center tf-flex-direction-column">
                <h1><?php esc_html_e("Rooms", "tourfic"); ?></h1>
                <?php Helper::tf_archive_sidebar_search_form('tf_room'); ?>
            </div>
       </div>
    </div>
    <?php Listings::render_design_1(); ?>
</div>