<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;
?>

<div class="tf-archive-template__two">

    <?php
    use \Tourfic\Classes\Helper;
    use Tourfic\App\Templates\Components\Shared\Archive\Banner;
    use Tourfic\App\Templates\Components\Shared\Archive\Sidebar;
    use Tourfic\App\Templates\Components\Hotel\Archive\Listings;
    
    Banner::render();
    ?>
    <!--Content section end -->
    <div class="tf-content-wrapper">
        <?php do_action( 'tf_before_container' ); ?>
        <div class="tf-container">
        
            <!-- Hotel details Srart -->
            <div class="tf-archive-details tf-details" id="tf-hotel-overview">

                <div class="tf-details-left tf-result-previews">
                    <?php Helper::tf_archive_sidebar_search_form('tf_hotel'); ?>
                    <?php Listings::render_design_2(); ?>
                </div>
                <?php Sidebar::render( ['design' => 'design-2'] ); ?>       
            </div>        
            <!-- Hotel details End -->

        </div>
    </div>
    <!--Content section end -->
</div>
