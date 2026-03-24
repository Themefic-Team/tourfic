<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use Tourfic\Classes\Tour\Components\Archive\Listings;
?>

<div class="tf-archive-page tf-archive-design-1 tf-archive-template__one">
    <div class="tf-container">
        <div class="tf-row tf-archive-inner tf-flex">
            <div class="tf-page-content tf-archive-left tf-result-previews">
                <?php do_action( 'tf_before_container' ); ?>
                <?php Listings::render_design_1(); ?>
            </div>

            <!-- SideBar-->
            <div class="tf-sidebar tf-archive-right">

                <?php Helper::tf_archive_sidebar_search_form('tf_tours'); ?>

                <?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
                    <div id="tf__booking_sidebar">
                        <?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>