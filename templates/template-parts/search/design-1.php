<?php
use \Tourfic\Classes\Helper;
?>

<div class="tf-archive-page tf-archive-design-1 tf-archive-template__one">
    <div class="tf-container">
        <div class="tf-row tf-archive-inner tf-flex">
            <?php echo do_shortcode("[tf_search_result]"); ?>
            <!-- SideBar-->
            <div class="tf-sidebar tf-archive-right">

                <?php Helper::tf_search_result_sidebar_form( 'archive' ); ?>

            </div>
        </div>
    </div>
</div>