<?php
/**
 * Template: Hotel Archive
 *
 * Display all hotels here
 * 
 * Default slug: /hotels 
 */


get_header(); ?>

<div class="tourfic-wrap" data-fullwidth="true">
	<?php do_action( 'tf_before_container' ); ?>
	<div class="tf_container">

		<div class="tf_row">
			<!-- Start Content -->
			<div class="tf_content">
				<div class="tf-action-top">
		            <div class="tf-list-grid">
		                <a href="#list-view" data-id="list-view" class="change-view" title="List View"><?php echo tourfic_get_svg('list_view'); ?></a>
		                <a href="#grid-view" data-id="grid-view" class="change-view" title="Grid View"><?php echo tourfic_get_svg('grid_view'); ?></a>
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
						echo '<div class="tf-nothing-found">Nothing Found!</div>';
					}
					?>
				</div>
				<div class="tf_posts_navigation">
					<?php tourfic_posts_navigation(); ?>
				</div>

			</div>
			<!-- End Content -->

			<!-- Start Sidebar -->
			<div class="tf_sidebar">
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
<?php
get_footer('tourfic');