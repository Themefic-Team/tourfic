<?php
use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Hotel\Hotel;
?>

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
                        <span class="tf-counter-title"><?php echo esc_html__( 'Total Results ', 'tourfic' ); ?> </span>
						<span><?php echo ' ('; ?> </span>
						<div class="tf-total-results">
							<span><?php echo esc_html( $post_count ); ?> </span>
						</div>
						<span><?php echo ')'; ?> </span>
                    </div>
                    <?php 
                    $tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_view'] : 'list';
                    ?>
                    <div class="tf-search-layout tf-flex tf-flex-gap-12">
                        <div class="tf-icon tf-serach-layout-list tf-list-active tf-grid-list-layout <?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" data-id="list-view">
                            <div class="defult-view">
                                <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="12" height="2" fill="white"/>
                                <rect x="14" width="2" height="2" fill="white"/>
                                <rect y="5" width="12" height="2" fill="white"/>
                                <rect x="14" y="5" width="2" height="2" fill="white"/>
                                <rect y="10" width="12" height="2" fill="white"/>
                                <rect x="14" y="10" width="2" height="2" fill="white"/>
                                </svg>
                            </div>
                            <div class="active-view">
                                <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect width="12" height="2" fill="#0E3DD8"/>
                                <rect x="14" width="2" height="2" fill="#0E3DD8"/>
                                <rect y="5" width="12" height="2" fill="#0E3DD8"/>
                                <rect x="14" y="5" width="2" height="2" fill="#0E3DD8"/>
                                <rect y="10" width="12" height="2" fill="#0E3DD8"/>
                                <rect x="14" y="10" width="2" height="2" fill="#0E3DD8"/>
                                </svg>
                            </div>
                        </div>
                        <div class="tf-icon tf-serach-layout-grid tf-grid-list-layout <?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" data-id="grid-view">
                            <div class="defult-view">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="10" width="2" height="2" fill="#0E3DD8"/>
                                <rect x="10" y="5" width="2" height="2" fill="#0E3DD8"/>
                                <rect x="10" y="10" width="2" height="2" fill="#0E3DD8"/>
                                <rect x="5" width="2" height="2" fill="#0E3DD8"/>
                                <rect x="5" y="5" width="2" height="2" fill="#0E3DD8"/>
                                <rect x="5" y="10" width="2" height="2" fill="#0E3DD8"/>
                                <rect width="2" height="2" fill="#0E3DD8"/>
                                <rect y="5" width="2" height="2" fill="#0E3DD8"/>
                                <rect y="10" width="2" height="2" fill="#0E3DD8"/>
                                </svg>
                            </div>
                            <div class="active-view">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="10" width="2" height="2" fill="white"/>
                                <rect x="10" y="5" width="2" height="2" fill="white"/>
                                <rect x="10" y="10" width="2" height="2" fill="white"/>
                                <rect x="5" width="2" height="2" fill="white"/>
                                <rect x="5" y="5" width="2" height="2" fill="white"/>
                                <rect x="5" y="10" width="2" height="2" fill="white"/>
                                <rect width="2" height="2" fill="white"/>
                                <rect y="5" width="2" height="2" fill="white"/>
                                <rect y="10" width="2" height="2" fill="white"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Loader Image -->
                <div id="tf_ajax_searchresult_loader">
                    <div id="tf-searchresult-loader-img">
                        <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
                    </div>
                </div>
                <div class="tf-search-results-list tf-mt-30">
                    <div class="archive_ajax_result tf-item-cards tf-flex <?php echo $tf_defult_views=="list" ? esc_attr('tf-layout-list') : esc_attr('tf-layout-grid'); ?>">

                    <?php
					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
							$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
							if ( !empty( $hotel_meta[ "featured" ] ) && $hotel_meta[ "featured" ] == 1 ) {
								Hotel::tf_hotel_archive_single_item();
							}
						}
						while ( have_posts() ) {
							the_post();
							$hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
							if ( empty($hotel_meta[ "featured" ]) ) {
								Hotel::tf_hotel_archive_single_item();
							}
						}
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Tours Found!", "tourfic"). '</div>';
					}
					?>

                        <div class="tf-pagination-bar">
                            <?php Helper::tourfic_posts_navigation(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SideBar-->
            <div class="tf-column tf-sidebar tf-archive-right">

                <?php Helper::tf_archive_sidebar_search_form('tf_hotel'); ?>

            </div>
        </div>
    </div>
</div>