<?php
/**
 * Template: Hotel Archive
 *
 * Display all hotels here
 * 
 * Default slug: /hotels 
 */


get_header(); ?>

<div class="tf-main-wrapper" data-fullwidth="true">
	<?php do_action( 'tf_before_container' ); ?>
	<div class="tf-container">

		<div class="search-result-inner tf-custom-search-results">
			<!-- Start Sidebar -->
			<div class="tf-search-right">
				<?php tf_search_result_sidebar_form( 'archive' ); ?>
			</div>
			<!-- End Sidebar -->

			<!-- Start Content -->
			<div class="tf-search-left">
				<div class="tf-action-top">
					<div class="tf-list-grid">
		                <a href="#list-view" data-id="list-view" class="change-view" title="<?php _e('List View', 'tourfic'); ?>"><i class="fas fa-list"></i></a>
		                <a href="#grid-view" data-id="grid-view" class="change-view" title="<?php _e('Grid View', 'tourfic'); ?>"><i class="fas fa-border-all"></i></a>
		            </div>
		        </div>
				<div class="archive_ajax_result">
					<?php
					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
							// Location: functions-hotel.php
							tf_hotel_archive_single_item();
						}
					} else {
						echo '<div class="tf-nothing-found">' .__("No Hotels Found!", "tourfic"). '</div>';
					}
					?>
				</div>
				<div class="tf_posts_navigation">
					<?php tourfic_posts_navigation(); ?>
				</div>

			</div>
			<!-- End Content -->

		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>
<?php
get_footer('tourfic');