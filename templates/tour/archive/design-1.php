<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use Tourfic\App\Templates\Components\Shared\Archive\Sidebar;
use Tourfic\App\Templates\Components\Tour\Archive\Listings;
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
                <?php Sidebar::render( ['design' => 'design-1'] ); ?>
            </div>
        </div>
    </div>
</div>