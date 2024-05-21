<?php
use \Tourfic\Classes\Helper;
?>
<div class="tf-main-wrapper search-result-wrapper" data-fullwidth="true">
    <?php do_action( 'tf_before_container' ); ?>
	<div class="tf-container">
        <div class="search-result-inner">
            <!-- Start Content -->           
			<div class="tf-search-left">
				<?php echo do_shortcode("[tf_search_result]"); ?>
			</div>
			<!-- End Content -->

			<!-- Start Sidebar -->
			<div class="tf-search-right">
				<?php Helper::tf_search_result_sidebar_form( 'archive' ); ?>
			</div>
			<!-- End Sidebar -->
		</div>
	</div>
	<?php do_action( 'tf_after_container' ); ?>
</div>