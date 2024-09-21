<?php
use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
?>

<div class="tf-hotel-template-4 tf-template-global tf-hotel-single">
    <div class="tf-title-area tf-hotel-title">
        <div class="tf-container">
            <div class="tf-title-wrap">
                <div class="tf-title-left">
                    <h1><?php the_title(); ?></h1>
					<?php if ( ! empty( $address ) ) { ?>
                        <div class="tf-map-link"><i class="fa-solid fa-location-dot"></i> <?php echo esc_html( $address ); ?></div>
					<?php } ?>
                </div>

                <div class="tf-title-right tf-wish-and-share">
					<?php
					// Wishlist
					if ( $disable_wishlist_sec != 1 ) {
						if ( is_user_logged_in() ) {
							if ( Helper::tfopt( 'wl-for' ) && in_array( 'li', Helper::tfopt( 'wl-for' ) ) ) { ?>
                                <a class="tf-icon tf-wishlist-box tf-wishlist <?php echo $has_in_wishlist ? esc_attr( 'actives' ) : '' ?>">
                                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>"
                                       data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
										echo 'data-page-title="' . get_the_title( Helper::tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( Helper::tfopt( 'wl-page' ) ) . '"';
									} ?>></i>
                                </a>
							<?php }
						} else {
							if ( Helper::tfopt( 'wl-for' ) && in_array( 'lo', Helper::tfopt( 'wl-for' ) ) ) { ?>
                                <a class="tf-icon tf-wishlist-box tf-wishlist <?php echo $has_in_wishlist ? esc_attr( 'actives' ) : '' ?>">
                                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>"
                                       data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
										echo 'data-page-title="' . get_the_title( Helper::tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( Helper::tfopt( 'wl-page' ) ) . '"';
									} ?>></i>
                                </a>
							<?php }
						} ?>
						<?php
					}
					?>

                    <!-- Share Section -->
					<?php if ( ! $disable_share_opt == '1' ) { ?>
                        <div class="tf-share tf-off-canvas-share-box">
                            <ul class="tf-off-canvas-share">
                                <li>
                                    <a href="http://www.facebook.com/share.php?u=<?php echo esc_url( $share_link ); ?>" class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content"><i class="fa-brands fa-facebook-f"></i></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="http://twitter.com/share?text=<?php echo esc_attr( $share_text ); ?>&url=<?php echo esc_url( $share_link ); ?>" class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content"><i class="fab fa-twitter"></i></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.linkedin.com/cws/share?url=<?php echo esc_url( $share_link ); ?>" class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content"><i class="fa-brands fa-linkedin-in"></i></span>
                                    </a>
                                </li>
								<?php $share_image_link = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' ); ?>
                                <li>
                                    <a href="http://pinterest.com/pin/create/button/?url=<?php echo esc_url( $share_link ); ?>&media=<?php echo esc_url( get_the_post_thumbnail_url() ); ?>&description=<?php echo esc_attr( $share_text ); ?>"
                                       class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content"><i class="fa-brands fa-pinterest-p"></i></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" id="share_link_button" class="share-center-copy-cta">
                                        <i class="ri-links-line"></i>
                                        <span class="tf-button-text share-center-copied-message"><?php esc_html_e( 'Link Copied!', 'tourfic' ); ?></span>
                                    </a>
                                    <input type="text" id="share_link_input" class="share-center-url share-center-url-input" value="<?php echo esc_attr( $share_link ); ?>" readonly
                                           style="opacity: 0; width: 0px !important;margin: 0px">
                                </li>
                            </ul>
                            <a href="#dropdown-share-center" class="tf-share-toggle tf-icon tf-social-box" data-toggle="true">
                                <svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.5 6H14.0938L9.15625 1.90625C8.9375 1.71875 8.9375 1.40625 9.09375 1.1875C9.28125 0.96875 9.59375 0.96875 9.8125 1.125L15.8125 6.09375C15.9062 6.21875 16 6.375 16 6.5C16 6.625 15.9062 6.78125 15.8125 6.875L9.8125 11.8438C9.71875 11.9062 9.59375 11.9688 9.5 11.9688C9.34375 11.9688 9.1875 11.9062 9.09375 11.7812C8.9375 11.5625 8.9375 11.25 9.15625 11.0625L14.0938 6.96875H5.5C3 6.96875 1 8.96875 1 11.4375V12.4062C1 12.7812 0.75 13 0.5 13C0.21875 13 0 12.7812 0 12.5V11.5312C0 8.46875 2.4375 6 5.5 6Z"
                                          fill="#FF6B00"/>
                                </svg>
                            </a>
                        </div>
					<?php } ?>
                    <!-- End Share Section -->
                </div>
            </div>
        </div>
    </div>

    <div class="tf-hotel-hero-section">
        <div class="tf-container">
            <div class="tf-hotel-hero-wrapper">
                <div class="tf-hotel-thumb <?php echo empty( $gallery_ids ) ? esc_attr('without-gallery') : '' ?>">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'tf_apartment_single_thumb' );
					} else {
						echo '<img src="'. esc_url(TF_ASSETS_APP_URL.'/images/feature-default.jpg') .'" alt="hotel-thumb"/>';
                    }
					?>
                    <div class="featured-meta-gallery-videos">
                        <div class="featured-column tf-gallery-box">
							<?php if ( ! empty( $gallery_ids ) ) { ?>
                                <a id="featured-gallery" href="#" class="tf-tour-gallery">
                                    <i class="fa-solid fa-camera-retro"></i><?php echo esc_html__( "Gallery", "tourfic" ); ?>
                                </a>
							<?php } ?>
                        </div>
						<?php
						$hotel_video = ! empty( $meta['video'] ) ? $meta['video'] : '';
						if ( ! empty( $hotel_video ) ) { ?>
                            <div class="featured-column tf-video-box">
                                <a class="tf-tour-video" id="featured-video" data-fancybox="tour-video" href="<?php echo esc_url( $hotel_video ); ?>">
                                    <i class="fa-solid fa-video"></i> <?php echo __( "Video", "tourfic" ); ?>
                                </a>
                            </div>
						<?php } ?>
                    </div>
                </div>
				<?php if ( ! empty( $gallery_ids ) ) : ?>
                    <div class="tf-hotel-gallery tf-gallery-count-<?php echo count( $gallery_ids ) >= 6 ? esc_attr( '6' ) : esc_attr( count( $gallery_ids ) ); ?>">
						<?php
						if ( ! empty( $gallery_ids ) ) {
							foreach ( $gallery_ids as $key => $gallery_item_id ) {
								?>
                                <a
                                        class="<?php echo $key == 5 ? esc_attr( 'tf-gallery-more' ) : ''; ?>"
                                        href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_item_id, 'full' ) ); ?>"
                                        style="<?php echo $key > 5 ? esc_attr( 'display: none;' ) : ''; ?>"
                                        data-fancybox="hotel-gallery"
                                        id="tour-gallery"
                                >
									<?php if ( $key <= 5 ) : ?>
                                        <img src="<?php echo esc_url( wp_get_attachment_image_url( $gallery_item_id, 'full' ) ); ?>" alt=""/>
									<?php endif; ?>
                                </a>
								<?php
							}
						} ?>
                    </div>
				<?php endif; ?>
            </div>
        </div>
    </div>

    <div class="tf-content-wrapper">
        <div class="tf-container">
            <div class="tf-details" id="tf-hotel-description">
                <div class="tf-details-left">
                    <!-- menu section Start -->
                    <div class="tf-details-menu">
                        <ul>
                            <li><a class="tf-hashlink" href="#tf-hotel-description"><?php echo esc_html__( "Description", "tourfic" ); ?></a></li>
                            <li><a href="#tf-hotel-rooms"><?php echo esc_html__( "Rooms", "tourfic" ); ?></a></li>
                            <li><a href="#tf-hotel-facilities"><?php echo esc_html__( "Amenities", "tourfic" ); ?></a></li>
                            <li><a href="#tf-hotel-faq"><?php echo esc_html__( "FAQ", "tourfic" ); ?></a></li>
                            <li><a href="#tf-hotel-reviews"><?php echo esc_html__( "Reviews", "tourfic" ); ?></a></li>
                            <li><a href="#tf-hotel-policies"><?php echo esc_html__( "Policies", "tourfic" ); ?></a></li>
                        </ul>
                    </div>
                    <!-- menu section End -->

	                <?php
	                if( !empty(tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout-3']) ){
		                foreach(tf_data_types(Helper::tfopt( 'tf-template' ))['single-hotel-layout-3'] as $section){
			                if( !empty($section['hotel-section-status']) && $section['hotel-section-status']=="1" && !empty($section['hotel-section-slug']) ){
				                include TF_TEMPLATE_PART_PATH . 'hotel/design-3/'.$section['hotel-section-slug'].'.php';
			                }
		                }
	                }else{
		                include TF_TEMPLATE_PART_PATH . 'hotel/design-3/description.php';
		                include TF_TEMPLATE_PART_PATH . 'hotel/design-3/rooms.php';
		                include TF_TEMPLATE_PART_PATH . 'hotel/design-3/features.php';
		                include TF_TEMPLATE_PART_PATH . 'hotel/design-3/faq.php';
		                include TF_TEMPLATE_PART_PATH . 'hotel/design-3/review.php';
		                include TF_TEMPLATE_PART_PATH . 'hotel/design-3/trams-condition.php';
	                }
	                ?>
                </div>
                <div class="tf-details-right tf-sitebar-widgets">
					<?php if ( ! empty( $meta['nearby-places'] ) ) { ?>
                        <div class="tf-whats-around tf-single-widgets">
                            <h2 class="tf-section-title"><?php echo ! empty( $meta['section-title'] ) ? esc_html( $meta['section-title'] ) : esc_html( "What’s around?" ); ?></h2>
                            <ul>
								<?php foreach ( $meta['nearby-places'] as $place ) { ?>
                                    <li>
                                        <span>
                                            <?php if ( ! empty( $place['place-icon'] ) ) { ?>
                                                <i class="<?php echo esc_attr( $place['place-icon'] ); ?>"></i>
                                            <?php } ?>
                                            <?php echo ! empty( $place['place-title'] ) ? esc_html( $place['place-title'] ) : ''; ?>
                                        </span>
                                        <span><?php echo ! empty( $place['place-dist'] ) ? esc_html( $place['place-dist'] ) : ''; ?></span>
                                    </li>
								<?php } ?>
                            </ul>
                        </div>
					<?php } ?>

                    <div id="hotel-map-location" class="tf-location tf-single-widgets">
                        <h2 class="tf-section-title"><?php _e( "Location", "tourfic" ); ?></h2>
						<?php if ( ! defined( 'TF_PRO' ) ) { ?>
							<?php
						if ( $address && $tf_openstreet_map != "default" && ( empty( $address_latitude ) || empty( $address_longitude ) ) ) { ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo $address; ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
						<?php } elseif ( $address && $tf_openstreet_map == "default" && ! empty( $address_latitude ) && ! empty( $address_longitude ) ) {
						?>
                            <div id="hotel-location" style="height: 250px"></div>
                            <script>
                                const map = L.map('hotel-location').setView([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], <?php echo $address_zoom; ?>);

                                const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    maxZoom: 20,
                                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                                }).addTo(map);

                                const marker = L.marker([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], {alt: '<?php echo $address; ?>'}).addTo(map)
                                    .bindPopup('<?php echo $address; ?>');
                            </script>
						<?php }else{ ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo $address; ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
						<?php } ?>
						<?php }else{ ?>
						<?php
						if ( $address && $tf_openstreet_map != "default" && ( empty( $address_latitude ) || empty( $address_longitude ) ) ){ ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo $address; ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
						<?php } elseif ( $address && $tf_openstreet_map == "default" && ! empty( $address_latitude ) && ! empty( $address_longitude ) ) {
						?>
                            <div id="hotel-location" style="height: 250px"></div>
                            <script>
                                const map = L.map('hotel-location').setView([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], <?php echo $address_zoom; ?>);

                                const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    maxZoom: 20,
                                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                                }).addTo(map);

                                const marker = L.marker([<?php echo $address_latitude; ?>, <?php echo $address_longitude; ?>], {alt: '<?php echo $address; ?>'}).addTo(map)
                                    .bindPopup('<?php echo $address; ?>');
                            </script>
						<?php }else{ ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo $address; ?>&output=embed" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
						<?php } ?>
						<?php } ?>
                    </div>

                    <div class="tf-location tf-single-widgets">
						<?php
						global $current_user;
						// Check if user is logged in
						$is_user_logged_in = $current_user->exists();
						$post_id           = $post->ID;
						// Get settings value
						$tf_ratings_for   = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
						$tf_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
						if ( $comments ) {
							$tf_overall_rate = [];
							TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
							TF_Review::tf_get_review_fields( $fields );
							?>
                            <h2 class="tf-section-title"><?php _e( "Overall reviews", "tourfic" ); ?></h2>
                            <div class="tf-review-data-inner">
                                <div class="tf-review-data">
                                    <div class="tf-review-data-average">
                                        <h2><span>
                                <?php _e( sprintf( '%.1f', $total_rating ) ); ?>
                            </span>/<?php echo $tf_settings_base; ?></h2>
                                    </div>
                                    <div class="tf-review-all-info">
                                        <p><?php _e( "Excellent", "tourfic" ); ?> <span><?php _e( "Total", "tourfic" ); ?><?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span></p>
                                    </div>
                                </div>
                                <div class="tf-review-data-features">
                                    <div class="tf-percent-progress">
										<?php
										if ( $tf_overall_rate ) {
											foreach ( $tf_overall_rate as $key => $value ) {
												if ( empty( $value ) || ! in_array( $key, $fields ) ) {
													continue;
												}
												$value = TF_Review::tf_average_ratings( $value );
												?>
                                                <div class="tf-progress-item">
                                                    <div class="tf-review-feature-label">
                                                        <p class="feature-label"><?php esc_html_e( $key, "tourfic" ); ?></p>
                                                        <p class="feature-rating"> <?php echo $value; ?></p>
                                                    </div>
                                                    <div class="tf-progress-bar">
                                                        <span class="percent-progress" style="width: <?php echo TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ); ?>%"></span>
                                                    </div>
                                                </div>
											<?php }
										} ?>

                                    </div>
                                </div>
                            </div>
                            <a class="tf-all-reviews" href="#tf-hotel-reviews"><?php _e( "See all reviews", "tourfic" ); ?></a>
						<?php } ?>
                        <button class="tf-review-open button">
							<?php _e( "Leave your review", "tourfic" ); ?>
                        </button>
						<?php
						// Review moderation notice
						echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '');
						?>
						<?php
						if ( ! empty( $tf_ratings_for ) ) {
							if ( $is_user_logged_in ) {
								if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
									?>
                                    <div class="tf-review-form-wrapper" action="">
                                        <h3><?php _e( "Leave your review", "tourfic" ); ?></h3>
                                        <p><?php _e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
										<?php TF_Review::tf_review_form(); ?>
                                    </div>
									<?php
								}
							} else {
								if ( in_array( 'lo', $tf_ratings_for ) ) {
									?>
                                    <div class="tf-review-form-wrapper" action="">
                                        <h3><?php _e( "Leave your review", "tourfic" ); ?></h3>
                                        <p><?php _e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
										<?php TF_Review::tf_review_form(); ?>
                                    </div>
								<?php }
							}
						} ?>

                        <!-- Enquery Section -->
						<?php
						$tf_enquiry_section_status = ! empty( $meta['h-enquiry-section'] ) ? $meta['h-enquiry-section'] : "";
						$tf_enquiry_section_icon   = ! empty( $meta['h-enquiry-option-icon'] ) ? esc_html( $meta['h-enquiry-option-icon'] ) : '';
						$tf_enquiry_section_title  = ! empty( $meta['h-enquiry-option-title'] ) ? esc_html( $meta['h-enquiry-option-title'] ) : '';
						$tf_enquiry_section_cont   = ! empty( $meta['h-enquiry-option-content'] ) ? esc_html( $meta['h-enquiry-option-content'] ) : '';
						$tf_enquiry_section_button = ! empty( $meta['h-enquiry-option-btn'] ) ? esc_html( $meta['h-enquiry-option-btn'] ) : '';
						if ( ! empty( $tf_enquiry_section_status ) && ( ! empty( $tf_enquiry_section_icon ) || ! empty( $tf_enquiry_section_title ) || ! empty( $enquery_button_text ) ) ) {
							?>
                            <div class="tf-send-inquiry tf-single-widgets">
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
								if ( ! empty( $tf_enquiry_section_cont ) ) {
									?>
                                    <p><?php echo $tf_enquiry_section_cont; ?></p>
									<?php
								}
								if ( ! empty( $tf_enquiry_section_button ) ) {
									?>
                                    <div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="tf-send-inquiry-btn"><span><?php echo $tf_enquiry_section_button; ?></span></a></div>
									<?php
								}
								?>
                            </div>
						<?php } ?>
                    </div>
                </div>
            </div>
            <!-- Hotel details End -->
        </div>
    </div>
    <!--Content section end -->
</div>