<div class="tf-hotel-template-4">

    <div class="tf-content-wrapper">
		<?php
		do_action( 'tf_before_container' );
		$post_count = $GLOBALS['wp_query']->post_count;
		?>

        <div class="tf-archive-search-form tf-booking-form-wrapper">
            <div class="tf-container">
                <form action="<?php echo tf_booking_search_action(); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking tf-booking-form">
	                <?php tf_search_result_sidebar_form( 'archive' ); ?>
                </form>
            </div>
        </div>

	    <?php echo do_shortcode("[tf_search_result]"); ?>
    </div>
    <!--Content section end -->

</div>