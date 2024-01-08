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
                            <h2 class="tf-total-results"><?php _e( "Found", "tourfic" ); ?> <span><?php echo $post_count; ?></span> <?php _e( "of", "tourfic" ); ?> <?php echo $GLOBALS['wp_query']->found_posts; ?> <?php _e( "Hotels", "tourfic" ); ?></h2>

                            <ul class="tf-archive-view">
                                <li class="tf-archive-view-item tf-archive-list-view">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M1.33398 7.59996C1.33398 6.82778 1.49514 6.66663 2.26732 6.66663H13.734C14.5062 6.66663 14.6673 6.82778 14.6673 7.59996V8.39996C14.6673 9.17214 14.5062 9.33329 13.734 9.33329H2.26732C1.49514 9.33329 1.33398 9.17214 1.33398 8.39996V7.59996Z" stroke="#FF6B00" stroke-linecap="round"/>
                                        <path d="M1.33398 2.26665C1.33398 1.49447 1.49514 1.33331 2.26732 1.33331H13.734C14.5062 1.33331 14.6673 1.49447 14.6673 2.26665V3.06665C14.6673 3.83882 14.5062 3.99998 13.734 3.99998H2.26732C1.49514 3.99998 1.33398 3.83882 1.33398 3.06665V2.26665Z" stroke="#FF6B00" stroke-linecap="round"/>
                                        <path d="M1.33398 12.9333C1.33398 12.1612 1.49514 12 2.26732 12H13.734C14.5062 12 14.6673 12.1612 14.6673 12.9333V13.7333C14.6673 14.5055 14.5062 14.6667 13.734 14.6667H2.26732C1.49514 14.6667 1.33398 14.5055 1.33398 13.7333V12.9333Z" stroke="#FF6B00" stroke-linecap="round"/>
                                    </svg>
                                </li>
                                <li class="tf-archive-view-item tf-archive-grid-view">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M1.33398 12C1.33398 10.973 1.33398 10.4595 1.56514 10.0823C1.69448 9.87127 1.87194 9.69381 2.08301 9.56447C2.46021 9.33331 2.97369 9.33331 4.00065 9.33331C5.02761 9.33331 5.54109 9.33331 5.9183 9.56447C6.12936 9.69381 6.30682 9.87127 6.43616 10.0823C6.66732 10.4595 6.66732 10.973 6.66732 12C6.66732 13.0269 6.66732 13.5404 6.43616 13.9176C6.30682 14.1287 6.12936 14.3062 5.9183 14.4355C5.54109 14.6666 5.02761 14.6666 4.00065 14.6666C2.97369 14.6666 2.46021 14.6666 2.08301 14.4355C1.87194 14.3062 1.69448 14.1287 1.56514 13.9176C1.33398 13.5404 1.33398 13.0269 1.33398 12Z" stroke="#6E655E" stroke-width="1.2"/>
                                        <path d="M9.33398 12C9.33398 10.973 9.33398 10.4595 9.56514 10.0823C9.69448 9.87127 9.87194 9.69381 10.083 9.56447C10.4602 9.33331 10.9737 9.33331 12.0007 9.33331C13.0276 9.33331 13.5411 9.33331 13.9183 9.56447C14.1294 9.69381 14.3068 9.87127 14.4362 10.0823C14.6673 10.4595 14.6673 10.973 14.6673 12C14.6673 13.0269 14.6673 13.5404 14.4362 13.9176C14.3068 14.1287 14.1294 14.3062 13.9183 14.4355C13.5411 14.6666 13.0276 14.6666 12.0007 14.6666C10.9737 14.6666 10.4602 14.6666 10.083 14.4355C9.87194 14.3062 9.69448 14.1287 9.56514 13.9176C9.33398 13.5404 9.33398 13.0269 9.33398 12Z" stroke="#6E655E" stroke-width="1.2"/>
                                        <path d="M1.33398 3.99998C1.33398 2.97302 1.33398 2.45954 1.56514 2.08233C1.69448 1.87127 1.87194 1.69381 2.08301 1.56447C2.46021 1.33331 2.97369 1.33331 4.00065 1.33331C5.02761 1.33331 5.54109 1.33331 5.9183 1.56447C6.12936 1.69381 6.30682 1.87127 6.43616 2.08233C6.66732 2.45954 6.66732 2.97302 6.66732 3.99998C6.66732 5.02694 6.66732 5.54042 6.43616 5.91762C6.30682 6.12869 6.12936 6.30615 5.9183 6.43549C5.54109 6.66665 5.02761 6.66665 4.00065 6.66665C2.97369 6.66665 2.46021 6.66665 2.08301 6.43549C1.87194 6.30615 1.69448 6.12869 1.56514 5.91762C1.33398 5.54042 1.33398 5.02694 1.33398 3.99998Z" stroke="#6E655E" stroke-width="1.2"/>
                                        <path d="M9.33398 3.99998C9.33398 2.97302 9.33398 2.45954 9.56514 2.08233C9.69448 1.87127 9.87194 1.69381 10.083 1.56447C10.4602 1.33331 10.9737 1.33331 12.0007 1.33331C13.0276 1.33331 13.5411 1.33331 13.9183 1.56447C14.1294 1.69381 14.3068 1.87127 14.4362 2.08233C14.6673 2.45954 14.6673 2.97302 14.6673 3.99998C14.6673 5.02694 14.6673 5.54042 14.4362 5.91762C14.3068 6.12869 14.1294 6.30615 13.9183 6.43549C13.5411 6.66665 13.0276 6.66665 12.0007 6.66665C10.9737 6.66665 10.4602 6.66665 10.083 6.43549C9.87194 6.30615 9.69448 6.12869 9.56514 5.91762C9.33398 5.54042 9.33398 5.02694 9.33398 3.99998Z" stroke="#6E655E" stroke-width="1.2"/>
                                    </svg>
                                </li>
                            </ul>
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

									// MAP LOCATIONS
									$infoWindowtext = '<div class="map-info-window">
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
										'content' => base64_encode( $infoWindowtext )
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