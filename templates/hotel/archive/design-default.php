
<div class="tf-main-wrapper" data-fullwidth="true">
	<?php
		do_action( 'tf_before_container' );
		$post_count = $GLOBALS['wp_query']->post_count;
	?>
	<div class="tf-container">

		<div class="search-result-inner">
			<?php
			$tf_defult_views = ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel_archive_view'] ) ? tf_data_types(tfopt( 'tf-template' ))['hotel_archive_view'] : 'list';
			?>
			<!-- Start Content -->
			<div class="tf-search-left">
				<div class="tf-action-top">
					<div class="tf-result-counter-info">
						<span class="tf-counter-title"><?php echo __( 'Total Results', 'tourfic' ); ?> </span>
						<span><?php echo '('; ?> </span>
						<div class="tf-total-results">
							<span><?php echo $post_count; ?> </span>
						</div>
						<span><?php echo ')'; ?> </span>
					</div>
					<div class="tf-list-grid">
		                <a href="#list-view" data-id="list-view" class="change-view <?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" title="<?php _e('List View', 'tourfic'); ?>"><i class="fas fa-list"></i></a>
		                <a href="#grid-view" data-id="grid-view" class="change-view <?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" title="<?php _e('Grid View', 'tourfic'); ?>"><i class="fas fa-border-all"></i></a>
		            </div>
		        </div>
				<div class="archive_ajax_result <?php echo $tf_defult_views=="grid" ? esc_attr('tours-grid') : '' ?>">
					<?php
					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
							// Location: functions-hotel.php
							tf_hotel_archive_single_item();
						}
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0">' .__("No Hotels Found!", "tourfic"). '</div>';
					}
					?>
					
					<div class="tf_posts_navigation">
						<?php tourfic_posts_navigation(); ?>
					</div>
				</div>

			</div>
			<!-- End Content -->

			<!-- Start Sidebar -->
			<div class="tf-search-right">
				<?php
				// Location: functions.php
				tf_archive_sidebar_search_form('tf_hotel');
				?>
			</div>
			<!-- End Sidebar -->
		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>