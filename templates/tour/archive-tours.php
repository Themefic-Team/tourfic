<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 */


get_header('tourfic');

$meta = get_post_meta( get_the_ID(),'tf_tours_option',true );
$pricing_rule = $meta['pricing'] ? $meta['pricing'] : null;
$tour_type = $meta['type'] ? $meta['type'] : null;
if( $pricing_rule == 'group'){
	$price = $meta['group_price'] ? $meta['group_price'] : null;
}else{
	$price = $meta['adult_price'] ? $meta['adult_price'] : null;
}
$discount_type = $meta['discount_type'] ? $meta['discount_type'] : null;
$discounted_price = $meta['discount_price'] ? $meta['discount_price'] : NULL;
if( $discount_type == 'percent' ){
	$sale_price = number_format( $price - (( $price / 100 ) * $discounted_price) ,1 ); 
}elseif( $discount_type == 'fixed'){
	$sale_price = number_format( ( $price - $discounted_price ),1 );
}

?>

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
							tf_tour_archive_single_item();
						}
					} else {
						get_template_part( 'template-parts/content', 'none' );
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
				<?php tf_archive_sidebar_search_form('tf_tours'); ?>
			</div>
			<!-- End Sidebar -->
		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>
<?php
get_footer('tourfic');