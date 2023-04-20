<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 */


get_header('tourfic');

$meta = get_post_meta( get_the_ID(),'tf_tours_opt',true );
$pricing_rule = !empty($meta['pricing']) ? $meta['pricing'] : null;
$tour_type = !empty($meta['type']) ? $meta['type'] : null;
if( $pricing_rule == 'group'){
	$price = !empty($meta['group_price']) ? $meta['group_price'] : null;
}else{
	$price = !empty($meta['adult_price']) ? $meta['adult_price'] : null;
}
$discount_type = !empty($meta['discount_type']) ? $meta['discount_type'] : null;
$discounted_price = !empty($meta['discount_price']) ? $meta['discount_price'] : NULL;
if( $discount_type == 'percent' ){
	$sale_price = number_format( $price - (( $price / 100 ) * $discounted_price) ,1 ); 
}elseif( $discount_type == 'fixed'){
	$sale_price = number_format( ( $price - $discounted_price ),1 );
}

$tf_expired_tour_showing = ! empty( tfopt( 't-show-expire-tour' ) ) ? tfopt( 't-show-expire-tour' ) : '';
if(!empty($tf_expired_tour_showing )){
	$tf_tour_posts_status = array('publish','expired');
}else{
	$tf_tour_posts_status = array('publish');
}
$args = array(
    'post_type' => "tf_tours",
    'orderby'   => 'date',
    'order'     => 'DESC',
    'post_status'    => $tf_tour_posts_status,
);
$loop = new WP_Query( $args );
$total_posts = $loop->found_posts;
?>

<div class="tf-main-wrapper" data-fullwidth="true">
	<?php
		do_action( 'tf_before_container' );
	?>
	<div class="tf-container">

		<div class="search-result-inner">
			<!-- Start Content -->
			<div class="tf-search-left">				
				<div class="tf-action-top">
					<div class="tf-result-counter-info">
						<span class="tf-counter-title"><?php echo __( 'Total Results', 'tourfic' ); ?> </span>
						<span><?php echo '('; ?> </span>
						<div class="tf-total-results">
							<span><?php echo $total_posts; ?> </span>
						</div>
						<span><?php echo ')'; ?> </span>
					</div>
		            <div class="tf-list-grid">
		                <a href="#list-view" data-id="list-view" class="change-view" title="<?php _e('List View', 'tourfic'); ?>"><i class="fas fa-list"></i></a>
		                <a href="#grid-view" data-id="grid-view" class="change-view" title="<?php _e('Grid View', 'tourfic'); ?>"><i class="fas fa-border-all"></i></a>
		            </div>
		        </div>
				<div class="archive_ajax_result">
					<?php
					if ( $loop->have_posts() ) {          
						while ( $loop->have_posts() ) {
							$loop->the_post();
							tf_tour_archive_single_item();
						}
					} else {
						echo '<div class="tf-nothing-found" data-post-count="0" >' .__("No Tours Found!", "tourfic"). '</div>';
					}
					?>
				</div>
				<div class="tf_posts_navigation">
					<?php tourfic_posts_navigation(); ?>
				</div>

			</div>
			<!-- End Content -->

			<!-- Start Sidebar -->
			<div class="tf-search-right">
				<?php tf_archive_sidebar_search_form('tf_tours'); ?>
			</div>
			<!-- End Sidebar -->
		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>
<?php
get_footer('tourfic');