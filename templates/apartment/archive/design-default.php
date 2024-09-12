
<div class="tf-main-wrapper" data-fullwidth="true">
	<?php

	use \Tourfic\Classes\Helper;
	use \Tourfic\Classes\Apartment\Apartment;


    $post_count = $GLOBALS['wp_query']->post_count;
    do_action( 'tf_before_container' ); 
    ?>
	<div class="tf-container">

		<div class="search-result-inner">
			<?php
			$tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_view'] : 'list';
			?>
			<!-- Start Content -->
			<div class="tf-search-left">
				<div class="tf-action-top">
                    <div class="tf-result-counter-info">
                        <span class="tf-counter-title"><?php echo esc_html__( 'Total Results', 'tourfic' ); ?> </span>
                        <span><?php echo '('; ?> </span>
                        <div class="tf-total-results">
                            <span><?php echo esc_html( $post_count ); ?> </span>
                        </div>
                        <span><?php echo ')'; ?> </span>
                    </div>
					<div class="tf-list-grid">
		                <a href="#list-view" data-id="list-view" class="change-view <?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('List View', 'tourfic'); ?>"><i class="fas fa-list"></i></a>
		                <a href="#grid-view" data-id="grid-view" class="change-view <?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" title="<?php esc_html_e('Grid View', 'tourfic'); ?>"><i class="fas fa-border-all"></i></a>
		            </div>
		        </div>
				<?php do_action("tf_apartment_archive_card_items_before"); ?>
				<div class="archive_ajax_result <?php echo $tf_defult_views=="grid" ? esc_attr('tours-grid') : '' ?>">
					<?php
					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
							$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
							if (!empty($apartment_meta[ "apartment_as_featured" ])) {
								Apartment::tf_apartment_archive_single_item();
							}
						}
						while ( have_posts() ) {
							the_post();
							$apartment_meta = get_post_meta( get_the_ID() , 'tf_apartment_opt', true );
							if ( empty($apartment_meta[ "apartment_as_featured" ])) {
								Apartment::tf_apartment_archive_single_item();
							}
						}
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0">' .esc_html__("No Apartments Found!", "tourfic"). '</div>';
					}
					?>
				</div>
				<?php do_action("tf_apartment_archive_card_items_after"); ?>
				
				<div class="tf_posts_navigation">
					<?php Helper::tourfic_posts_navigation(); ?>
				</div>

			</div>
			<!-- End Content -->

			
			<!-- Start Sidebar -->
			<div class="tf-search-right">
				<?php
				// Location: functions.php
				Helper::tf_archive_sidebar_search_form('tf_apartment');
				?>
			</div>
			<!-- End Sidebar -->
		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>