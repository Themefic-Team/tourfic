<div class="tf-archive-page tf-template-global tf-archive-design-1">
    <div class="tf-container">
        <div class="tf-row tf-archive-inner tf-flex">
            <div class="tf-column tf-page-content tf-archive-left tf-result-previews">
                <?php
                    do_action( 'tf_before_container' );
                    $post_count = $GLOBALS['wp_query']->post_count;
                ?>
                <!-- Search Head Section -->
                <div class="tf-archive-head tf-flex tf-flex-align-center tf-flex-space-bttn">
                    <div class="tf-search-result tf-flex">
                        <span class="tf-counter-title"><?php echo __( 'Total Results ', 'tourfic' ); ?> </span>
						<span><?php echo ' ('; ?> </span>
						<div class="tf-total-results">
							<span><?php echo $post_count; ?> </span>
						</div>
						<span><?php echo ')'; ?> </span>
                    </div>
                    <div class="tf-search-layout tf-flex tf-flex-gap-12">
                        <div class="tf-icon tf-serach-layout-list tf-grid-list-layout active" data-id="list-view">
                            <i class="fa-solid fa-list-ul"></i>
                        </div>
                        <div class="tf-icon tf-serach-layout-grid tf-grid-list-layout" data-id="grid-view">
                            <i class="fa-solid fa-table-cells"></i>
                        </div>
                    </div>
                </div>
                <!-- Loader Image -->
                <div id="tf_ajax_searchresult_loader">
                    <div id="tf-searchresult-loader-img">
                        <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
                    </div>
                </div>
                <div class="tf-search-results-list tf-mrtop-30">
                    <div class="archive_ajax_result tf-item-cards tf-flex tf-layout-list">

                    <?php
					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
							tf_tour_archive_single_item();
						}
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0" >' .__("No Tours Found!", "tourfic"). '</div>';
					}
					?>

                        <div class="tf-pagination-bar">
                            <?php tourfic_posts_navigation(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SideBar-->
            <div class="tf-column tf-sidebar tf-archive-right">

                <?php tf_archive_sidebar_search_form('tf_tours'); ?>

            </div>
        </div>
    </div>
</div>