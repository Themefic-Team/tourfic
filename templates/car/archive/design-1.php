<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\App\Templates\Components\Car_Rental\Archive\Listings;
use Tourfic\App\Templates\Components\Shared\Archive\Banner;
use \Tourfic\Classes\Helper;

?>
<div class="tf-archive-template__one">
    <?php Banner::render(); ?>

    <div class="tf-container">
        <div class="tf-container-inner">
            <div class="tf-archive-car-details-warper">
                <?php Helper::tf_archive_sidebar_search_form('tf_carrental'); ?>

                <?php Listings::render_design_1(); ?>
            </div>
        </div>
    </div>
</div>
