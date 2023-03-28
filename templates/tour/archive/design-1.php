<div class="tf-archive-page tf-template-global">
    <div class="tf-container">
        <div class="tf-row tf-archive-inner tf-flex">
            <div class="tf-column tf-page-content tf-archive-left">
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

                    </div>
                    <div class="tf-pagination-outter tf-mrtop-40">
                        <div class="tf-pagination-bar">
                            <?php tourfic_posts_navigation(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SideBar-->
            <div class="tf-column tf-sidebar tf-archive-right">

                <?php tf_archive_sidebar_search_form('tf_tours'); ?>

                <div class="tf-box-wrapper related-list-item tf-box tf-mrtop-30">
                <div class="tf-form-title tf-padbtm-16">
                    <h3>You May Also Like</h3>
                </div>
                <div class="tf-related-items-outtter">
                    
                    <div class="tf-related-single tf-flex tf-flex-gap-12">
                        <div class="tf-related-item-featured">
                            <img src="/assets/img/related-tours.png" alt="">
                        </div>
                        <div class="tf-related-item-details">
                            <h2>Holiday Dubai Maktoum Airport IHG hotel</h2>
                            <div class="tf-related-meta-details tf-flex tf-flex-space-bttn tf-mrtop-16">
                                <div class="tf-related-meta tf-price-meta">
                                    From<span> $181</span>
                                </div>
                                <div class="tf-meta-review">
                                    <i class="fa-solid fa-star"></i>
                                    <span>(26 reviews)</span>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                    <div class="tf-related-single tf-flex tf-flex-gap-12">
                        <div class="tf-related-item-featured">
                            <img src="/assets/img/related-tours.png" alt="">
                        </div>
                        <div class="tf-related-item-details">
                            <h2>Holiday Dubai Maktoum Airport IHG hotel</h2>
                            <div class="tf-related-meta-details tf-flex tf-flex-space-bttn tf-mrtop-16">
                                <div class="tf-related-meta tf-price-meta">
                                    From<span> $181</span>
                                </div>
                                <div class="tf-meta-review">
                                    <i class="fa-solid fa-star"></i>
                                    <span>(26 reviews)</span>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="tf-related-single tf-flex tf-flex-gap-12">
                        <div class="tf-related-item-featured">
                            <img src="/assets/img/related-tours.png" alt="">
                        </div>
                        <div class="tf-related-item-details">
                            <h2>Holiday Dubai Maktoum Airport IHG hotel</h2>
                            <div class="tf-related-meta-details tf-flex tf-flex-space-bttn tf-mrtop-16">
                                <div class="tf-related-meta tf-price-meta">
                                    From<span> $181</span>
                                </div>
                                <div class="tf-meta-review">
                                    <i class="fa-solid fa-star"></i>
                                    <span>(26 reviews)</span>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                    <div class="tf-related-single tf-flex tf-flex-gap-12">
                        <div class="tf-related-item-featured">
                            <img src="/assets/img/related-tours.png" alt="">
                        </div>
                        <div class="tf-related-item-details">
                            <h2>Holiday Dubai Maktoum Airport IHG hotel</h2>
                            <div class="tf-related-meta-details tf-flex tf-flex-space-bttn tf-mrtop-16">
                                <div class="tf-related-meta tf-price-meta">
                                    From<span> $181</span>
                                </div>
                                <div class="tf-meta-review">
                                    <i class="fa-solid fa-star"></i>
                                    <span>(26 reviews)</span>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>
</div>