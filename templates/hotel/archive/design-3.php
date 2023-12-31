<div class="tf-hotel-template-4">

    <div class="tf-content-wrapper">
		<?php
		do_action( 'tf_before_container' );
		$post_count = $GLOBALS['wp_query']->post_count;
		?>

        <div class="tf-archive-search-form tf-booking-form-wrapper">
            <div class="tf-container">
                <form action="<?php echo tf_booking_search_action(); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking tf-booking-form">
					<?php tf_archive_sidebar_search_form( 'tf_hotel' ); ?>
                </form>
            </div>
        </div>

		<?php if ( have_posts() ) : ?>
            <div class="tf-archive-details tf-details" id="tf-hotel-overview">
                <div class="tf-details-left tf-result-previews">
                    <!--Available rooms start -->
                    <div class="tf-available-archive-hotels-wrapper tf-available-rooms-wrapper" id="tf-hotel-rooms">
                        <div class="tf-archive-available-rooms-head tf-available-rooms-head">
                            <h2 class="tf-total-results"><?php _e( "Total", "tourfic" ); ?> <span><?php echo $post_count; ?></span> <?php _e( "hotels available", "tourfic" ); ?></h2>
                            <div class="tf-archive-filter-showing">
                                <i class="ri-equalizer-line"></i>
                            </div>
                        </div>

                        <!-- Loader Image -->
                        <div id="tour_room_details_loader">
                            <div id="tour-room-details-loader-img">
                                <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
                            </div>
                        </div>

                        <!--Available rooms start -->
                        <div class="tf-archive-available-rooms tf-available-rooms archive_ajax_result">

							<?php
							$count     = 0;
							$locations = [];
							while ( have_posts() ) {
								the_post();
								$count ++;

								$meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
                                $map = !empty( $meta['map'] ) ? tf_data_types($meta['map']) : '';

								if ( ! empty( $map ) ) {
									$lat     = $map['latitude'];
									$lng     = $map['longitude'];
var_dump($lat);
var_dump($lng);
									// MAP LOCATIONS
									$infoWindoetext = '<div class="map-info-window">
                                                    <div class="item-wrap">
                                                    <div class="item-header">
                                                    <a class="hover-effect" href="' . get_the_permalink() . '" tabindex="0">
                                                    <img class="img-fluid listing-thumbnail" src="' . get_the_post_thumbnail_url() . '" alt="Commercial central shop">
                                                    </a>
                                                    </div>
                                                    <div class="item-body flex-grow-1">
                                                    <h2 class="item-title">
                                                    <a href="' . get_the_permalink() . '">' . get_the_title() . '</a>
                                                    </h2>
                                                    </div>
                                                    </div>
                                                    </div>';

									$locations[ $count ] = [
										'lat'     => (float) $lat,
										'lng'     => (float) $lng,
										'content' => base64_encode( $infoWindoetext )
									];
								}
								tf_hotel_archive_single_item();
							}
                            wp_reset_query();
							?>
                            <div id="map-datas" style="display: none"><?php echo array_filter( $locations ) ? json_encode( array_values( $locations ) ) : []; ?></div>
                            <?php
							if ( tourfic_posts_navigation() ) { ?>
                                <div class="tf-pagination-bar">
									<?php tourfic_posts_navigation(); ?>
                                </div>
							<?php } ?>
                        </div>
                        <!-- Available rooms end -->

                    </div>
                    <!-- Available rooms end -->
                </div>
                <div class="tf-details-right tf-archive-right ic-property-map-wrap">
                    <div id="map-marker" data-marker="<?php echo TF_ASSETS_URL . 'app/images/map-marker.png'; ?>"></div>
                    <div class="ic-properties-map">
                        <div class="ic-map" id="map"></div>
                    </div>
                </div>
            </div>
		<?php else: ?>
            <div class="tf-nothing-found" data-post-count="0"><?php _e( "No Hotels Found!", "tourfic" ); ?></div>
		<?php endif; ?>
    </div>
    <!--Content section end -->


    <!-- Hotel PopUp Starts -->
    <div class="tf-popup-wrapper tf-hotel-popup">
        <div class="tf-popup-inner">
            <div class="tf-popup-body">

            </div>
            <div class="tf-popup-close">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
    </div>
    <!-- Hotel PopUp end -->

</div>