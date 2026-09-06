<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Room;

$tourfic_room_arc_selected_template = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['room-archive'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['room-archive'] : 'design-1';
$tourfic_search_request              = tourfic_get_public_search_request();
$tourfic_search_type                 = isset( $tourfic_search_request['type'] ) ? $tourfic_search_request['type'] : '';

if ( 'tf_room' === $tourfic_search_type && $tourfic_room_arc_selected_template == "design-1" ) :
    $tourfic_room_arc_banner = ! empty(Helper::tf_data_types(Helper::tfopt('tf-template'))['room_archive_design_1_bannar']) ?  Helper::tf_data_types(Helper::tfopt('tf-template'))['room_archive_design_1_bannar'] : '';
    $tourfic_room_arc_banner = !empty($tourfic_room_arc_banner) ? $tourfic_room_arc_banner : TOURFIC_ASSETS_APP_URL . '/images/room-hero-banner.jpg';
    ?>

    <div class="tf-archive-template__one sp-0">
        <div class="tf-archive-room-banner" style="<?php echo !empty($tourfic_room_arc_banner) ? 'background-image: url(' . esc_url($tourfic_room_arc_banner) . ')' : ''; ?>">
            <div class="tf-container">
                <div class="tf-banner-content tf-flex tf-flex-justify-center tf-flex-direction-column">
                    <h1><?php esc_html_e("Rooms", "tourfic"); ?></h1>
                    <?php Helper::tf_archive_sidebar_search_form('tf_room'); ?>
                </div>
            </div>
        </div>
        <div class="tf-archive-room-details">
            <div class="tf-container">
                <?php echo do_shortcode( '[tourfic_search_result]' ); ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="tf-archive-page tf-archive-design-1 tf-archive-template__one">
        <div class="tf-container">
            <div class="tf-row tf-archive-inner tf-flex">
                <?php echo do_shortcode( '[tourfic_search_result]' ); ?>
                <!-- SideBar-->
                <div class="tf-sidebar tf-archive-right">

                    <?php Helper::tf_search_result_sidebar_form( 'archive' ); ?>

                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
