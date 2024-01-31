<?php
$tf_booking_type = '1';
$tf_booking_url = $tf_booking_query_url = $tf_booking_attribute = $tf_hide_booking_form = $tf_hide_price = '';
if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
	$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
	$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
	$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
	$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
	$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
	$tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
}
if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
	$external_search_info = array(
		'{adult}'    => ! empty( $adult ) ? $adult : 1,
		'{child}'    => ! empty( $child ) ? $child : 0,
		'{checkin}'  => ! empty( $check_in ) ? $check_in : date( 'Y-m-d' ),
		'{checkout}' => ! empty( $check_out ) ? $check_out : date( 'Y-m-d', strtotime( '+1 day' ) ),
		'{room}'     => ! empty( $room_selected ) ? $room_selected : 1,
	);
	if ( ! empty( $tf_booking_attribute ) ) {
		$tf_booking_query_url = str_replace( array_keys( $external_search_info ), array_values( $external_search_info ), $tf_booking_query_url );
		if ( ! empty( $tf_booking_query_url ) ) {
			$tf_booking_url = $tf_booking_url . '/?' . $tf_booking_query_url;
		}
	}
}
?>
<div class="tf-main-wrapper">
	<?php do_action( 'tf_before_container' ); ?>

    <!-- Start title area -->
    <div class="tf-title-area tf-hotel-title sp-20">
        <div class="tf-container">
            <div class="tf-title-wrap">
                <div class="tf-title-left">
                    <span class="post-type"><?php _e( 'Hotel', 'tourfic' ) ?></span>
                    <h1><?php the_title(); ?></h1>
                    <!-- Start map link -->
					<?php if ( $locations ) { ?>
                        <div class="tf-map-link">
							<?php if ( $address ) {
								echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' . $address . ' – </span>';
							} ?>

                            <a href="<?php echo $first_location_url; ?>" class="more-hotel tf-d-ib">
								<?php printf( __( 'Show more hotels in %s', 'tourfic' ), $first_location_name ); ?>
                            </a>
                        </div>
					<?php } ?>
                    <!-- End map link -->
                </div>

                <div class="tf-title-right">
					<?php
					// Wishlist
					if ( $disable_wishlist_sec == 0 ) {
						if ( is_user_logged_in() ) {
							if ( tfopt( 'wl-for' ) && in_array( 'li', tfopt( 'wl-for' ) ) ) { ?>
                                <a class="tf-wishlist-button" title="<?php esc_attr_e( 'Click to toggle wishlist', 'tourfic' ); ?>"><i
                                            class="far <?php echo $has_in_wishlist ? 'tf-text-red remove-wishlist fa-heart' : 'add-wishlist fa-heart-o' ?>" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>"
                                            data-id="<?php echo $post_id ?>" data-type="<?php echo esc_attr( $post_type ) ?>" <?php if ( tfopt( 'wl-page' ) ) {
										echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"';
									} ?>></i></a>
							<?php }
						} else {
							if ( tfopt( 'wl-for' ) && in_array( 'lo', tfopt( 'wl-for' ) ) ) { ?>
                                <a class="tf-wishlist-button" title="<?php esc_attr_e( 'Click to toggle wishlist', 'tourfic' ); ?>"><i
                                            class="far <?php echo $has_in_wishlist ? 'tf-text-red remove-wishlist fa-heart' : 'add-wishlist fa-heart-o' ?>" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>"
                                            data-id="<?php echo $post_id ?>" data-type="<?php echo esc_attr( $post_type ) ?>" <?php if ( tfopt( 'wl-page' ) ) {
										echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"';
									} ?>></i></a>
							<?php }
						} ?>
						<?php
					} else {
						if ( tfopt( 'wl-bt-for' ) && in_array( '1', tfopt( 'wl-bt-for' ) ) ) {
							if ( is_user_logged_in() ) {
								if ( tfopt( 'wl-for' ) && in_array( 'li', tfopt( 'wl-for' ) ) ) {
									?>
                                    <a class="tf-wishlist-button" title="<?php esc_attr_e( 'Click to toggle wishlist', 'tourfic' ); ?>"><i
                                                class="far <?php echo $has_in_wishlist ? 'tf-text-red remove-wishlist fa-heart' : 'add-wishlist fa-heart-o' ?>"
                                                data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>"
                                                data-id="<?php echo $post_id ?>" data-type="<?php echo esc_attr( $post_type ) ?>" <?php if ( tfopt( 'wl-page' ) ) {
											echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"';
										} ?>></i></a>
									<?php
								}
							} else {
								if ( tfopt( 'wl-for' ) && in_array( 'lo', tfopt( 'wl-for' ) ) ) {
									?>
                                    <a class="tf-wishlist-button" title="<?php esc_attr_e( 'Click to toggle wishlist', 'tourfic' ); ?>"><i
                                                class="far <?php echo $has_in_wishlist ? 'tf-text-red remove-wishlist fa-heart' : 'add-wishlist fa-heart-o' ?>"
                                                data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>"
                                                data-id="<?php echo $post_id ?>" data-type="<?php echo esc_attr( $post_type ) ?>" <?php if ( tfopt( 'wl-page' ) ) {
											echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"';
										} ?>></i></a>
									<?php
								}
							}
						}
					}
					?>

                    <!-- Share Section -->
					<?php if ( ! $disable_share_opt == '1' ) { ?>
                        <div class="tf-share">
                            <a href="#dropdown-share-center" class="share-toggle"
                               data-toggle="true"><i class="fas fa-share-alt"></i></a>
                            <div id="dropdown-share-center" class="share-tour-content">
                                <ul class="tf-dropdown-content">
                                    <li>
                                        <a href="http://www.facebook.com/share.php?u=<?php echo esc_url( $share_link ); ?>"
                                           class="tf-dropdown-item" target="_blank">
                                    <span class="tf-dropdown-item-content">
                                        <i class="fab fa-facebook-square"></i>
                                        <?php esc_html_e( 'Share on Facebook', 'tourfic' ); ?>
                                    </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="http://twitter.com/share?text=<?php echo esc_attr( $share_text ); ?>&url=<?php echo esc_url( $share_link ); ?>"
                                           class="tf-dropdown-item" target="_blank">
                                    <span class="tf-dropdown-item-content">
                                        <i class="fab fa-twitter-square"></i>
                                        <?php esc_html_e( 'Share on Twitter', 'tourfic' ); ?>
                                    </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://www.linkedin.com/cws/share?url=<?php echo esc_url( $share_link ); ?>"
                                           class="tf-dropdown-item" target="_blank">
                                    <span class="tf-dropdown-item-content">
                                        <i class="fab fa-linkedin"></i>
                                        <?php esc_html_e( 'Share on Linkedin', 'tourfic' ); ?>
                                    </span>
                                        </a>
                                    </li>
									<?php $share_image_link = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' ); ?>
                                    <li>
                                        <a href="http://pinterest.com/pin/create/button/?url=<?php echo esc_url( $share_link ); ?>&media=<?php echo esc_url( $share_image_link[0] ); ?>&description=<?php echo esc_attr( $share_text ); ?>"
                                           class="tf-dropdown-item" target="_blank">
                                    <span class="tf-dropdown-item-content">
                                        <i class="fab fa-pinterest"></i>
                                        <?php esc_html_e( 'Share on Pinterest', 'tourfic' ); ?>
                                    </span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="share-center-copy-form tf-dropdown-item" title="<?php esc_attr_e( 'Share this link', 'tourfic' ); ?>"
                                             aria-controls="share_link_button">
                                            <label class="share-center-copy-label"
                                                   for="share_link_input"><?php esc_html_e( 'Share this link', 'tourfic' ); ?></label>
                                            <input type="text" id="share_link_input"
                                                   class="share-center-url share-center-url-input"
                                                   value="<?php echo esc_attr( $share_link ); ?>" readonly>
                                            <button id="share_link_button" class="tf_button share-center-copy-cta" tabindex="0"
                                                    role="button">
                                                <span class="tf-button-text share-center-copy-message"><?php esc_html_e( 'Copy link', 'tourfic' ); ?></span>
                                                <span class="tf-button-text share-center-copied-message"><?php esc_html_e( 'Link Copied!', 'tourfic' ); ?></span>
                                            </button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
					<?php } ?>
                    <!-- End Share Section -->

                    <div class="reserve-button">
                        <a href="#rooms" class="tf-btn-flip" data-back="<?php esc_attr_e( 'View Rooms', 'tourfic' ); ?>" data-front="<?php esc_attr_e( 'Reserve Now', 'tourfic' ); ?>"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End title area -->

    <!-- Hero Start -->
    <div class="hero-section">
        <div class="tf-container">
            <div class="hero-section-wrap">
                <div class="hero-left">
                    <div class="tf-top-review">
						<?php if ( $comments && ! $disable_review_sec == '1' ) { ?>
                            <a href="#tf-review">
                                <div class="tf-single-rating">
                                    <i class="fas fa-star"></i> <span><?php echo tf_total_avg_rating( $comments ); ?></span> (<?php tf_based_on_text( count( $comments ) ); ?>)
                                </div>
                            </a>
						<?php } ?>
                    </div>
                    <!-- Start Gallery -->
					<?php if ( ! empty( $gallery_ids ) ) { ?>
                        <div class="tf-gallery-wrap">
                            <div class="list-single-main-media fl-wrap" id="sec1">
                                <div class="single-slider-wrapper fl-wrap">
                                    <div class="tf_slider-for fl-wrap">
										<?php foreach ( $gallery_ids as $attachment_id ) {
											echo '<div class="slick-slide-item">';
											echo '<a href="' . esc_url( wp_get_attachment_url( $attachment_id, 'tf_gallery_thumb' ) ) . '" class="slick-slide-item-link" data-fancybox="hotel-gallery">';
											echo wp_get_attachment_image( $attachment_id, 'tf_gallery_thumb' );
											echo '</a>';
											echo '</div>';
										} ?>
                                    </div>
                                    <div class="swiper-button-prev sw-btn"><i class="fa fa-angle-left"></i></div>
                                    <div class="swiper-button-next sw-btn"><i class="fa fa-angle-right"></i></div>

									<?php
									/**
									 * Hotel video section in the hero
									 */
									tf_hotel_gallery_video( $meta );
									?>
                                </div>
                            </div>
                        </div>
					<?php } else { ?>
                        <div class="tf-gallery-wrap">
                            <div class="list-single-main-media fl-wrap" id="sec1">
                                <div class="single-slider-wrapper fl-wrap">
                                    <div class="tf_slider-for fl-wrap">

                                        <a href="<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'tf_gallery_thumb' ) ) ? esc_url( get_the_post_thumbnail_url( $post_id, 'tf_gallery_thumb' ) ) : TF_ASSETS_APP_URL . '/images/feature-default.jpg'; ?>"
                                           class="slick-slide-item-link" data-fancybox="hotel-gallery">
                                            <img src="<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'tf_gallery_thumb' ) ) ? esc_url( get_the_post_thumbnail_url( $post_id, 'tf_gallery_thumb' ) ) : TF_ASSETS_APP_URL . '/images/feature-default.jpg'; ?>"
                                                 alt="">
                                        </a>

                                    </div>

									<?php
									/**
									 * Hotel video section in the hero
									 */
									tf_hotel_gallery_video( $meta );
									?>

                                </div>
                            </div>
                        </div>
					<?php } ?>
                    <!-- End gallery-->
                    <div class="map-for-mobile">
						<?php if ( ! defined( 'TF_PRO' ) && ( $address ) ) { ?>
                            <div class="show-on-map">
                                <div class="tf-btn"><a href="https://www.google.com/maps/search/<?php echo $address; ?>" target="_blank" class="btn-styled"><span><i
                                                    class="fas fa-map-marker-alt"></i><?php esc_html_e( 'Show on map', 'tourfic' ); ?></span></a></div>
                            </div>
						<?php } ?>
                    </div>
                    <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( ! empty( $address ) || (! empty( $address_latitude ) && ! empty( $address_longitude ) ) ) ) { ?>
                        <div class="popupmap-for-mobile">
                            <?php 
                            if( $tf_openstreet_map!="default" ){ ?>
                            <div class="tf-hotel-location-preview show-on-map">
                                <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="150"
                                        style="border:0;" allowfullscreen="" loading="lazy"></iframe>

                                    <a data-fancybox data-src="#tf-hotel-google-maps" href="javascript:;">
                                        <div class="tf-btn">
                                            <span class="btn-styled"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                                        </div>
                                    </a>

                                </div>
							<?php } ?>
							<?php if ( $tf_openstreet_map == "default" && ! empty( $address_latitude ) && ! empty( $address_longitude ) ) { ?>
                                <div class="tf-hotel-location-preview show-on-map">
                                    <div id="mobile-hotel-location" style="height: 130px;"></div>

                                    <a data-fancybox data-src="#tf-hotel-google-maps" href="javascript:;">
                                        <div class="tf-btn">
                                            <span class="btn-styled"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                                        </div>
                                    </a>
                                    <script>
                                        const map = L.map('mobile-hotel-location').setView([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], <?php echo $address_zoom; ?>);

                                        const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                            maxZoom: 20,
                                            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                                        }).addTo(map);

                                        const marker = L.marker([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], {alt: '<?php echo $address; ?>'}).addTo(map)
                                            .bindPopup('<?php echo $address; ?>');
                                    </script>
                                </div>
							<?php } ?>
							<?php if ( $tf_openstreet_map == "default" && ( empty( $address_latitude ) || empty( $address_longitude ) ) ) { ?>

                                <div class="tf-hotel-location-preview show-on-map">

                                    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&z=17&output=embed" width="100%" height="150" style="border:0;"
                                            allowfullscreen="" loading="lazy"></iframe>

                                    <a data-fancybox data-src="#tf-hotel-google-maps" href="javascript:;">
                                        <div class="tf-btn">
                                            <span class="btn-styled"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                                        </div>
                                    </a>

                                </div>

							<?php } ?>
                            <div style="display: none;" id="tf-hotel-google-maps">
                                <div class="tf-hotel-google-maps-container">
                                    <?php
                                    if ( ! empty( $address ) ) {
                                        ?>
                                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&z=15&output=embed" width="100%" height="550" style="border:0;"
                                                allowfullscreen="" loading="lazy"></iframe>
                                    <?php } else { ?>
                                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&z=15&output=embed" width="100%"
                                                height="550" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
									<?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                     <div class="desc-wrap">
                         <?php the_content(); ?>
                    </div>
                     <!-- Start features -->
                    <?php if ( $features && count( $features ) > 0 ) { ?>
                        <div class="tf_features">
                            <?php if( !empty( $meta['popular-section-title'] ) ): ?>
                                <h3 class="section-heading"><?php echo esc_html($meta['popular-section-title']); ?></h3>
                            <?php endif; ?>
                            <div class="tf-feature-list">
                                <?php foreach ( $features as $feature ) {
                                    $feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
                                    $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                                    if ( $f_icon_type == 'fa' ) {
                                        $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
                                    } elseif ( $f_icon_type == 'c' ) {
                                        $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
                                    } ?>

                                    <div class="single-feature-box">
                                        <?php echo $feature_icon ?? ''; ?>
                                        <span class="feature-list-title"><?php echo $feature->name; ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- End features -->
                </div>
                <div class="hero-right">
					<?php if ( ! defined( 'TF_PRO' ) && ( $address ) ) { ?>
                        <div class="show-on-map">
                            <div class="tf-btn"><a href="https://www.google.com/maps/search/<?php echo $address; ?>" target="_blank" class="btn-styled"><span><i
                                                class="fas fa-map-marker-alt"></i><?php esc_html_e( 'Show on map', 'tourfic' ); ?></span></a></div>
                        </div>
                    <?php } ?>
                    <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( ! empty( $address ) || (! empty( $address_latitude ) && ! empty( $address_longitude ) ) ) ) { ?>
                        <?php
                        if( $tf_openstreet_map!="default" ){ ?>
                        <div class="tf-hotel-location-preview show-on-map">
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="150" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            
                            <a data-fancybox data-src="#tf-hotel-google-maps" href="javascript:;">
                            <div class="tf-btn">
                                <span class="btn-styled"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                            </div>
                            </a>

                            </div>
						<?php } ?>
						<?php if ( $tf_openstreet_map == "default" && ! empty( $address_latitude ) && ! empty( $address_longitude ) ) { ?>
                            <div class="tf-hotel-location-preview show-on-map">
                                <div id="hotel-location" style="height: 130px;"></div>

                                <a data-fancybox data-src="#tf-hotel-google-maps" href="javascript:;">
                                    <div class="tf-btn">
                                        <span class="btn-styled"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                                    </div>
                                </a>
                                <script>
                                    const dmap = L.map('hotel-location').setView([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], <?php echo $address_zoom; ?>);

                                    const dtiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        maxZoom: 20,
                                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                                    }).addTo(dmap);

                                    const dmarker = L.marker([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], {alt: '<?php echo $address; ?>'}).addTo(map)
                                        .bindPopup('<?php echo $address; ?>');
                                </script>
                            </div>
						<?php } ?>

						<?php if ( $tf_openstreet_map == "default" && ( empty( $address_latitude ) || empty( $address_longitude ) ) ) { ?>
                            <div class="tf-hotel-location-preview show-on-map">
                                <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&z=17&output=embed" width="100%" height="150" style="border:0;" allowfullscreen=""
                                        loading="lazy"></iframe>

                                <a data-fancybox data-src="#tf-hotel-google-maps" href="javascript:;">
                                    <div class="tf-btn">
                                        <span class="btn-styled"><?php esc_html_e( 'Show on Map', 'tourfic' ); ?></span>
                                    </div>
                                </a>
                            </div>
						<?php } ?>

                        <div style="display: none;" id="tf-hotel-google-maps">
                            <div class="tf-hotel-google-maps-container">
                                <?php
                                if ( ! empty( $address ) ) { ?>
                                    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&z=17&output=embed" width="100%" height="550" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                <?php } else { ?>
                                    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&z=17&output=embed" width="100%" height="550" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
	                <?php if(($tf_booking_type == 2 && $tf_hide_booking_form !== '1') || $tf_booking_type == 1) :?>
                        <div class="hero-booking">
			                <?php tf_hotel_sidebar_booking_form(); ?>
                        </div>
	                <?php endif; ?>
                    <?php
                    $places_section_title = !empty($meta["section-title"]) ? $meta["section-title"] : "What's around?";
                    $places_meta = !empty($meta["nearby-places"]) ? $meta["nearby-places"] : array();
                    ?>
                    <?php if( count($places_meta) > 0 ) : ?> <!-- nearby places - start -->
                        <div class="nearby-container"> 
                            <div class="nearby-container-inner">
                                <?php if(!empty($places_section_title)): ?>
                                    <h3 class="section-heading"><?php echo __($places_section_title, 'tourfic'); ?></h3>
                                <?php endif; ?>
                                <ul>
                                    <?php foreach($places_meta as $place) {
                                        $place_icon = '<i class="' . $place['place-icon'] . '"></i>';
                                        ?>
                                        <li>
                                            <span>
                                                <?php echo $place_icon; ?> <?php echo $place["place-title"]; ?>
                                            </span>
                                            <span>
                                                <?php echo $place["place-dist"]; ?>
                                            </span>
                                        </li>
                                    <?php }; ?>
                                </ul>
                            </div>
                        </div> 
                    <?php endif; ?> <!-- nearby places - end -->
                </div>
            </div>
        </div>
    </div>
    <!-- Hero End -->

    <div class="tf-container">
        <div class="tf-divider"></div>
    </div>

	<?php if ( $rooms ) :

		//getting only selected features for rooms
		$rm_features = [];
		foreach ( $rooms as $key => $room ) {
			//merge for each room's selected features
			if ( ! empty( $room['features'] ) ) {
				$rm_features = array_unique( array_merge( $rm_features, $room['features'] ) );
			}
		}
		?>
        <!-- Start Room Section -->
        <div class="tf-room-section sp-50">
            <div class="tf-container">
                <h2 class="section-heading"><?php echo ! empty( $meta['room-section-title'] ) ? esc_html( $meta['room-section-title'] ) : ''; ?></h2>
                <!-- Hooked in feature filter action -->
				<?php do_action( 'tf_hotel_features_filter', $rm_features, 10 ) ?>
                <div class="tf-room-type" id="rooms">
                    <div class="tf-room-table hotel-room-wrap">
                        <div id="tour_room_details_loader">
                            <div id="tour-room-details-loader-img">
                                <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
                            </div>
                        </div>
                        <table class="availability-table" cellpadding="0" cellspacing="0">
                            <thead>
                            <tr>
                                <th class="description"><?php _e( 'Room Details', 'tourfic' ); ?></th>
                                <th class="pax"><?php _e( 'Pax', 'tourfic' ); ?></th>
                                <?php if(($tf_booking_type == 2 && $tf_hide_price !== '1') || $tf_booking_type == 1) : ?>
                                    <th class="pricing"><?php _e( 'Price', 'tourfic' ); ?></th>
                                <?php endif; ?>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Start Single Room -->
							<?php foreach ( $rooms as $key => $room ) {
								$enable = ! empty( $room['enable'] ) ? $room['enable'] : '';
								if ( $enable == '1' ) {
									$footage         = ! empty( $room['footage'] ) ? $room['footage'] : '';
									$bed             = ! empty( $room['bed'] ) ? $room['bed'] : '';
									$adult_number    = ! empty( $room['adult'] ) ? $room['adult'] : '0';
									$child_number    = ! empty( $room['child'] ) ? $room['child'] : '0';
									$total_person    = $adult_number + $child_number;
									$pricing_by      = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
									$avil_by_date    = ! empty( $room['avil_by_date'] ) ? ! empty( $room['avil_by_date'] ) : false;
									$multi_by_date   = ! empty( $room['price_multi_day'] ) ? ! empty( $room['price_multi_day'] ) : false;
									$child_age_limit = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";

									// Hotel Room Discount Data
									$hotel_discount_type   = ! empty( $room["discount_hotel_type"] ) ? $room["discount_hotel_type"] : "none";
									$hotel_discount_amount = ! empty( $room["discount_hotel_price"] ) ? $room["discount_hotel_price"] : 0;

									if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $avil_by_date == '1' ) {
                                        $avail_date = ! empty( $room['avail_date'] ) ? json_decode($room['avail_date'], true) : [];
                                        if ($pricing_by == '1') {
                                            $prices = array();
                                            $discount_prices = array();
                
                                            foreach ($avail_date as $date => $data) {
                                                if ($data['status'] == 'available') {
                                                    $prices[] = !empty($data['price']) ? $data['price'] : 0;
                
                                                    if($hotel_discount_type == "percent") {
                                                        $discount_prices[] = ! empty($data['price']) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $data['price'] - ( ( (int) $data['price'] / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
                                                    } else if( $hotel_discount_type == "fixed") {
                                                        $discount_prices[] = !empty($data['price']) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $data['price'] - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
                                                    }
                                                }
                                            }
                                        } else {
                                            $prices = array();
                                            $discount_prices = array();
                
                                            foreach ($avail_date as $date => $data) {
                                                if ($data['status'] == 'available') {
                                                    $prices[] = !empty($data['adult_price']) ? $data['adult_price'] : 0;
                
                                                    if($hotel_discount_type == "percent") {
                                                        $discount_prices[] = ! empty($data['adult_price']) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $data['adult_price'] - ( ( (int) $data['adult_price'] / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
                                                    } else if( $hotel_discount_type == "fixed") {
                                                        $discount_prices[] = !empty($data['adult_price']) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $data['adult_price'] - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
                                                    }
                                                }
                                            }
                                        }
                                        if ( ! empty( $prices ) ) {
                                            $range_price = [];
                                            $discount_range_price = array();
                                            foreach ( $prices as $single ) {
                                                if ( ! empty( $single ) ) {
                                                    $range_price[] = $single;
                                                }
                                            }
                                            foreach ( $discount_prices as $discount_single ) {
                                                if ( ! empty( $discount_single ) ) {
                                                    $discount_range_price[] = $discount_single;
                                                }
                                            }
                
                                            if ( sizeof( $range_price ) > 1 ) {
                                                
                                                $discount_price = !empty($discount_prices) ? ( min( $discount_prices ) != max( $discount_prices ) ? wc_format_price_range( min( $discount_prices ), max( $discount_prices ) ) : wc_price( min( $discount_prices ) ) ) : 0;
                                                $price = $prices ? ( min( $prices ) != max( $prices ) ? wc_format_price_range( min( $prices ), max( $prices ) ) : wc_price( min( $prices ) ) ) : wc_price( 0 );
                
                                                
                                            } else {
                                                $price = ! empty( $range_price[0] ) ? $range_price[0] : 0;
                                                $discount_price = ! empty( $discount_range_price[0] ) ? $discount_range_price[0] : '';
                
                                                $price = wc_price( $price );
                                                $discount_price = wc_price( $discount_price );
                                            }
                                        }else{
                                            if ( $pricing_by == '1' ) {
                                                $price = ! empty( $room['price'] ) ? $room['price'] : 0;
                                                $discount_price = 0;
                                                if($hotel_discount_type == "percent") {
                                                    $discount_price = !empty($price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
                                                }else if($hotel_discount_type == "fixed") {
                                                    $discount_price = !empty($price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
                                                }
                                                $price = wc_price($price);
                                                $discount_price = wc_price($discount_price);
                
                                            } else {
                                                $price = ! empty( $room['adult_price'] ) ? $room['adult_price'] : 0;
                                                $discount_price = 0;
                
                                                if($hotel_discount_type == "percent") {
                                                    $discount_price = !empty($price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
                                                }else if($hotel_discount_type == "fixed") {
                                                    $discount_price = !empty($price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ((int) $price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
                                                }
                                                $price = wc_price($price);
                                                $discount_price = wc_price($discount_price);
                                            }
                                        }
                                    } else {
                                        if ( $pricing_by == '1' ) {
                                            $price = ! empty( $room['price'] ) ? $room['price'] : 0;
                                            $discount_price = 0;
                
                                            if($hotel_discount_type == "percent") {
                                                $discount_price = !empty($price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
                                            }
                                            if($hotel_discount_type == "fixed") {
                                                $discount_price = !empty($price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
                                            }
                                            $discount_price = ($hotel_discount_type != "none" && $hotel_discount_amount != 0) ? wc_price($discount_price) : 0;
                                            $price = wc_price( $price );
                                        } else {
                                            $price =! empty( $room['adult_price'] ) ? $room['adult_price'] : 0;
                                            $discount_price = 0;
                
                                            if($hotel_discount_type == "percent") {
                                                $discount_price = !empty($price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
                                            } else if($hotel_discount_type == "fixed") {
                                                $discount_price = !empty($price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - (int) $hotel_discount_amount ), 2 ) ) : 0;
                                            }
                                            $discount_price = wc_price($discount_price);
                                            $price = wc_price( $price );
                                        }
                                    }
									?>
                                    <tr>
                                        <td class="description">
                                            <div class="tf-room-type">
                                                <div class="tf-room-title">
													<?php
													$tour_room_details_gall = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
													if ( $tour_room_details_gall ) {
														$tf_room_gallery_ids = explode( ',', $tour_room_details_gall );
													}
													if ( $tour_room_details_gall ){
														?>
                                                        <h3><a href="#" class="tf-room-detail-qv" data-uniqid="<?php echo ! empty( $room['unique_id'] ) ? $room['unique_id'] . $key : '' ?>"
                                                               data-hotel="<?php echo $post_id; ?>" style="text-decoration: underline;">
																<?php echo esc_html( $room['title'] ); ?>
                                                            </a></h3>

                                                        <div id="tour_room_details_qv" class="">

                                                        </div>
													<?php } else{ ?>
                                                    <h3><?php echo esc_html( $room['title'] ); ?><h3>
															<?php
															}
															?>
                                                </div>
                                                <div class="bed-facilities"><p><?php _e( $room['description'] ); ?></p></div>
                                            </div>

											<?php if ( $footage ) { ?>
                                                <div class="tf-tooltip tf-d-ib">
                                                    <div class="room-detail-icon">
                                            <span class="room-icon-wrap"><i
                                                        class="fas fa-ruler-combined"></i></span>
                                                        <span class="icon-text tf-d-b"><?php echo $footage; ?><?php _e( 'sft', 'tourfic' ); ?></span>
                                                    </div>
                                                    <div class="tf-top">
														<?php _e( 'Room Footage', 'tourfic' ); ?>
                                                        <i class="tool-i"></i>
                                                    </div>
                                                </div>
											<?php }
											if ( $bed ) { ?>
                                                <div class="tf-tooltip tf-d-ib">
                                                    <div class="room-detail-icon">
                                                        <span class="room-icon-wrap"><i class="fas fa-bed"></i></span>
                                                        <span class="icon-text tf-d-b">x<?php echo $bed; ?></span>
                                                    </div>
                                                    <div class="tf-top">
														<?php _e( 'Number of Beds', 'tourfic' ); ?>
                                                        <i class="tool-i"></i>
                                                    </div>
                                                </div>
											<?php } ?>

											<?php if ( ! empty( $room['features'] ) ) { ?>
                                                <div class="room-features">
                                                    <div class="tf-room-title"><h4><?php esc_html_e( 'Amenities', 'tourfic' ); ?></h4>
                                                    </div>
                                                    <ul class="room-feature-list">

														<?php foreach ( $room['features'] as $feature ) {

															$room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
															if ( ! empty( $room_f_meta ) ) {
																$room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
															}
															if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' ) {
																$room_feature_icon = ! empty( $room_f_meta['icon-fa'] ) ? '<i class="' . $room_f_meta['icon-fa'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
															} elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' ) {
																$room_feature_icon = ! empty( $room_f_meta['icon-c'] ) ? '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
															} else {
																$room_feature_icon = '<i class="fas fa-bread-slice"></i>';
															}

															$room_term = get_term( $feature ); ?>
                                                            <li class="tf-tooltip">
																<?php echo ! empty( $room_feature_icon ) ? $room_feature_icon : ''; ?>
                                                                <div class="tf-top">
																	<?php echo $room_term->name; ?>
                                                                    <i class="tool-i"></i>
                                                                </div>
                                                            </li>
														<?php } ?>
                                                    </ul>
                                                </div>
											<?php } ?>
                                        </td>
                                        <td class="pax">
											<?php if ( $adult_number ) { ?>
                                                <div class="tf-tooltip tf-d-b">
                                                    <div class="room-detail-icon">
                                            <span class="room-icon-wrap"><i class="fas fa-male"></i><i
                                                        class="fas fa-female"></i></span>
                                                        <span class="icon-text tf-d-b">x<?php echo $adult_number; ?></span>
                                                    </div>
                                                    <div class="tf-top">
														<?php _e( 'Number of Adults', 'tourfic' ); ?>
                                                        <i class="tool-i"></i>
                                                    </div>
                                                </div>
											<?php }
											if ( $child_number ) { ?>
                                                <div class="tf-tooltip tf-d-b">
                                                    <div class="room-detail-icon">
                                                        <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                                        <span class="icon-text tf-d-b">x<?php echo $child_number; ?></span>
                                                    </div>
                                                    <div class="tf-top">
														<?php
														if ( ! empty( $child_age_limit ) ) {
															printf( __( 'Children Age Limit %s Years', 'tourfic' ), $child_age_limit );
														} else {
															_e( 'Number of Children', 'tourfic' );
														}
														?>
                                                        <i class="tool-i"></i>
                                                    </div>
                                                </div>
											<?php } ?>
                                        </td>
									    <?php if(($tf_booking_type == 2 && $tf_hide_price !== '1') || $tf_booking_type == 1) : ?>
                                            <td class="pricing">
                                                <div class="tf-price-column">
                                                    <?php
                                                    if ( $pricing_by == '1' ) {
                                                        if ( ! empty( $discount_price ) ) {
                                                            ?>
                                                            <span class="tf-price"><del><?php echo $price; ?></del> <?php echo $discount_price ?></span>
                                                            <?php
                                                        } else if ( $hotel_discount_type == "none" || empty( $hotel_discount_amount ) ) {
                                                            ?>
                                                            <span class="tf-price"><?php echo $price ?></span>
                                                            <?php
                                                        } ?>

                                                        <div class="price-per-night">
                                                            <?php
                                                            if ( $multi_by_date ) {
                                                                esc_html_e( 'per night', 'tourfic' );
                                                            } else {
                                                                esc_html_e( 'per day', 'tourfic' );
                                                            } ?>
                                                        </div>
                                                        <?php
                                                    } else {
                                                        if ( ! empty( $discount_price ) ) {
                                                            ?>
                                                            <span class="tf-price"><del><?php echo $price; ?></del> <?php echo $discount_price ?></span>
                                                            <?php
                                                            $discount_price = "";
                                                        } else if ( $hotel_discount_type == "none" || empty( $d_price ) ) {
                                                            ?>
                                                            <span class="tf-price"><?php echo $price ?></span>
                                                            <?php
                                                        } ?>
                                                        <div class="price-per-night">
                                                            <?php
                                                            if ( $multi_by_date ) {
                                                                esc_html_e( 'per person/night', 'tourfic' );
                                                            } else {
                                                                esc_html_e( 'per person/day', 'tourfic' );
                                                            } ?>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                        <td class="reserve tf-t-c">
                                            <div class="tf-btn">
												<?php if ( $tf_booking_type == 2 && ! empty( $tf_booking_url ) ): ?>
                                                    <a href="<?php echo esc_url( $tf_booking_url ); ?>" class="btn-styled tf-sml-btn" target="_blank">
														<?php esc_html_e( 'Book Now', 'tourfic' ); ?>
                                                    </a>
												<?php else: ?>
                                                    <button class="btn-styled hotel-room-availability tf-sml-btn" type="submit">
														<?php esc_html_e( 'Check Availability', 'tourfic' ); ?>
                                                    </button>
												<?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
									<?php
								}
							}
							?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Room Section -->
	<?php endif; ?>

    <!-- FAQ section Start -->
	<?php if ( $faqs ): ?>
        <div class="tf-faq-wrapper hotel-faq sp-50">
            <div class="tf-container">
                <div class="tf-faq-sec-title">
                    <h2 class="section-heading"><?php echo ! empty( $meta['faq-section-title'] ) ? esc_html( $meta['faq-section-title'] ) : ''; ?></h2>
                </div>

                <div class="tf-faq-content-wrapper">
					<?php
					$tf_enquiry_section_status = ! empty( $meta['h-enquiry-section'] ) ? $meta['h-enquiry-section'] : "";
					$tf_enquiry_section_icon   = ! empty( $meta['h-enquiry-option-icon'] ) ? esc_html( $meta['h-enquiry-option-icon'] ) : '';
					$tf_enquiry_section_title  = ! empty( $meta['h-enquiry-option-title'] ) ? esc_html( $meta['h-enquiry-option-title'] ) : '';
					$tf_enquiry_section_des    = ! empty( $meta['h-enquiry-option-content'] ) ? esc_html( $meta['h-enquiry-option-content'] ) : '';
					$tf_enquiry_section_button = ! empty( $meta['h-enquiry-option-btn'] ) ? esc_html( $meta['h-enquiry-option-btn'] ) : '';

					if ( ! empty( $tf_enquiry_section_status ) && ( ! empty( $tf_enquiry_section_icon ) || ! empty( $tf_enquiry_section_title ) || ! empty( $enquery_button_text ) ) ) {
						?>
                        <div class="tf-ask-question">
                            <div class="default-enquiry-title-section">
								<?php
								if ( ! empty( $tf_enquiry_section_icon ) ) {
									?>
                                    <i class="<?php echo $tf_enquiry_section_icon; ?>" aria-hidden="true"></i>
									<?php
								}
								if ( ! empty( $tf_enquiry_section_title ) ) {
									?>
                                    <h3><?php echo $tf_enquiry_section_title; ?></h3>
									<?php
								}
								?>

                            </div>
							<?php
							if ( ! empty( $tf_enquiry_section_des ) ) {
								?>
                                <p><?php echo $tf_enquiry_section_des; ?></p>
								<?php
							}
							if ( ! empty( $tf_enquiry_section_button ) ) {
								?>
                                <div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="btn-styled"><span><?php echo $tf_enquiry_section_button; ?></span></a></div>
								<?php
							}
							?>

                        </div>
					<?php } ?>
                    <div class="tf-faq-items-wrapper">
						<?php foreach ( $faqs as $key => $faq ): ?>
                            <div id="tf-faq-item">
                                <div class="tf-faq-title">
                                    <h4><?php esc_html_e( $faq['title'] ); ?></h4>
                                    <i class="fas fa-angle-down arrow"></i>
                                </div>
                                <div class="tf-faq-desc">
                                    <p><?php echo wp_kses_post( $faq['description'] ); ?></p>
                                </div>
                            </div>
						<?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
	<?php endif; ?>
    <!-- FAQ section end -->

    <!-- Start Review Section -->
	<?php if ( ! $disable_review_sec == 1 ) { ?>
        <div id="tf-review" class="review-section sp-50">
            <div class="tf-container">
                <div class="reviews">
                    <h2 class="section-heading"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
					<?php comments_template(); ?>
                </div>
            </div>
        </div>
	<?php } ?>
    <!-- End Review Section -->

    <div class="tf-container">
        <div class="tf-divider"></div>
    </div>

    <!-- Start TOC Content -->
	<?php if ( $tc ) { ?>
        <div class="toc-section sp-50">
            <div class="tf-container">
                <div class="tf-toc-wrap">
                    <h2 class="section-heading"><?php echo ! empty( $meta['tc-section-title'] ) ? esc_html( $meta['tc-section-title'] ) : ''; ?></h2>
                    <div class="tf-toc-inner">
						<?php echo wpautop( $tc ); ?>
                    </div>
                </div>
            </div>
        </div>
	<?php } ?>
    <!-- End TOC Content -->

	<?php do_action( 'tf_after_container' ); ?>
</div>