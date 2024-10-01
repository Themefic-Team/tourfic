<div class="tf-hotel-template-4 <?php echo ! empty( $_GET['type'] ) && $_GET['type'] == "tf_tours" ? 'tf-tour-template-4' : '' ?> <?php echo ! empty( $_GET['type'] ) && $_GET['type'] == "tf_apartment" ? 'tf-apartment-template-4' : '' ?>">
	<?php
	use \Tourfic\Classes\Helper;

	// Check nonce security
	if ( ! isset( $_GET['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
		return;
	}
	?>
    <div class="tf-content-wrapper">
		<?php
		do_action( 'tf_before_container' );
		$post_count = $GLOBALS['wp_query']->post_count;
		?>

        <div class="tf-archive-search-form tf-booking-form-wrapper">
            <div class="tf-container">
                <form action="<?php echo esc_url( Helper::tf_booking_search_action() ); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking tf-booking-form">
					<?php Helper::tf_search_result_sidebar_form( 'archive' ); ?>
                </form>
            </div>
        </div>


        <div class="tf-archive-details">
            <!-- Loader Image -->
            <div id="tf_ajax_searchresult_loader">
                <div id="tf-searchresult-loader-img">
                    <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/loader.gif" alt="">
                </div>
            </div>

            <div class="tf-details-left">
                <!--Available rooms start -->
                <div class="tf-archive-hotels-wrapper">
                    <div class="tf-archive-filter">
                        <div class="tf-archive-filter-sidebar">
                            <div class="tf-filter-wrapper">
                                <div class="tf-filter-title">
                                    <h4 class="tf-section-title"><?php echo esc_html__( "Filter", "tourfic" ); ?></h4>
                                    <button class="filter-reset-btn"><?php echo esc_html__( "Reset", "tourfic" ); ?></button>
                                </div>
								<?php if ( is_active_sidebar( 'tf_search_result' ) ) { ?>
                                    <div id="tf__booking_sidebar">
										<?php dynamic_sidebar( 'tf_search_result' ); ?>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                    </div>

					<?php echo do_shortcode( "[tf_search_result]" ); ?>

                </div>
                <!-- Available rooms end -->
            </div>
            <div class="tf-details-right tf-archive-right">
                <div id="map-marker" data-marker="<?php echo TF_ASSETS_URL . 'app/images/cluster-marker.png'; ?>"></div>
                <div class="tf-hotel-archive-map-wrap">
                    <div id="tf-hotel-archive-map"></div>
                </div>
            </div>
        </div>
    </div>
    <!--Content section end -->

</div>