<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package tourfic
 */


get_header('tourfic'); ?>

<div class="tourfic-wrap" data-fullwidth="true">
	<?php do_action( 'tf_before_container' ); ?>
	<div class="tf_container">

		<div class="tf_row">
			<!-- Start Content -->
			<div class="tf_content">
				<?php echo do_shortcode("[tf_search_result]"); ?>
			</div>
			<!-- End Content -->

			<!-- Start Sidebar -->
			<div class="tf_sidebar">
				<?php tourfic_get_sidebar( 'archive' ); ?>
			</div>
			<!-- End Sidebar -->
		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>
<?php
get_footer('tourfic');