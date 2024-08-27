<?php
use \Tourfic\Classes\Helper;
use Tourfic\Classes\Tour\Tour;
?>


<div class="tf-main-wrapper" data-fullwidth="true">
	<?php
		do_action( 'tf_before_container' );
	?>
	<div class="tf-container">
		<?php 
		$tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_view'] : 'list';
		?>
		<div class="search-result-inner">
			<!-- Start Content -->
			<div class="tf-search-left">				
				<div class="tf-action-top">
					<div class="tf-result-counter-info">
						<span class="tf-counter-title"><?php echo esc_html__( 'Total Results', 'tourfic' ); ?> </span>
						<span><?php echo '('; ?> </span>
						<div class="tf-total-results">
							<span><?php echo esc_html($tf_total_results); ?> </span>
						</div>
						<span><?php echo ')'; ?> </span>
					</div>
		            <div class="tf-list-grid">
		                <a href="#list-view" data-id="list-view" class="change-view <?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('List View', 'tourfic'); ?>"><i class="fas fa-list"></i></a>
		                <a href="#grid-view" data-id="grid-view" class="change-view <?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('Grid View', 'tourfic'); ?>"><i class="fas fa-border-all"></i></a>
		            </div>
		        </div>
				<div class="archive_ajax_result <?php echo $tf_defult_views=="grid" ? esc_attr('tours-grid') : '' ?>">
					<?php
					$loop2 = $loop;
                    if ( $loop->have_posts() ) {          
                        while ( $loop->have_posts() ) {
                            $loop->the_post();
                            $tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
                            
                            if(!empty($tour_meta["tour_as_featured"])) {
                                Tour::tf_tour_archive_single_item();
                                $featured_post_id[] = get_the_ID(); 
                            }

                            $tf_total_results+=1;
                        }

                        if (!empty($featured_post_id)) $loop2->set("post__not_in", $featured_post_id);
						
                        while ( $loop2->have_posts() ) {
                            $loop2->the_post();
                            $tour_meta = get_post_meta( get_the_ID() , 'tf_tours_opt', true );
                            
                            if( empty($tour_meta["tour_as_featured"]) ) {
                                Tour::tf_tour_archive_single_item();
                            }
                        }
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Tours Found!", "tourfic"). '</div>';
					}
					?>
					<span class="tf-posts-count" hidden="hidden">
					<?php echo esc_html($tf_total_results); ?>
					</span>
					<div class="tf_posts_navigation">
						<?php Helper::tourfic_posts_navigation(); ?>
					</div>
				</div>
				

			</div>
			<!-- End Content -->

			<!-- Start Sidebar -->
			<div class="tf-search-right">
				<?php Helper::tf_archive_sidebar_search_form('tf_tours'); ?>
			</div>
			<!-- End Sidebar -->
		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>