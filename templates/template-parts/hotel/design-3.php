<?php

use \Tourfic\Classes\Helper;
use \Tourfic\Classes\Hotel\Hotel;
use \Tourfic\App\TF_Review;

$tf_booking_type      = '1';
$tf_hide_booking_form = '';
$tf_ext_booking_type  = '';
$tf_ext_booking_code  = '';
if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
	$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
	$tf_hide_booking_form = ! empty( $meta['hide_booking_form'] ) ? $meta['hide_booking_form'] : '';
	$tf_ext_booking_type  = ! empty( $meta['external-booking-type'] ) ? $meta['external-booking-type'] : '1';
	$tf_ext_booking_code  = ! empty( $meta['booking-code'] ) ? $meta['booking-code'] : '';
}
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
                                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>"
                                       data-id="<?php echo esc_html($post_id) ?>" data-type="<?php echo esc_html($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
										echo 'data-page-title="' . esc_html(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"';
									} ?>></i>
                                </a>
							<?php }
						} else {
							if ( Helper::tfopt( 'wl-for' ) && in_array( 'lo', Helper::tfopt( 'wl-for' ) ) ) { ?>
                                <a class="tf-icon tf-wishlist-box tf-wishlist <?php echo $has_in_wishlist ? esc_attr( 'actives' ) : '' ?>">
                                    <i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( "wishlist-nonce" )) ?>"
                                       data-id="<?php echo esc_html($post_id) ?>" data-type="<?php echo esc_html($post_type) ?>" <?php if ( Helper::tfopt( 'wl-page' ) ) {
										echo 'data-page-title="' . esc_html(get_the_title( Helper::tfopt( 'wl-page' ) )) . '" data-page-url="' . esc_url(get_permalink( Helper::tfopt( 'wl-page' ) )) . '"';
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
                <div class="tf-hotel-thumb <?php echo empty( $gallery_ids ) ? esc_attr( 'without-gallery' ) : '' ?>">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'tf_apartment_single_thumb' );
					} else {
						echo '<img src="' . esc_url( TF_ASSETS_APP_URL . '/images/feature-default.jpg' ) . '" alt="hotel-thumb"/>';
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
                                    <i class="fa-solid fa-video"></i> <?php echo esc_html__( "Video", "tourfic" ); ?>
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
            <div class="tf-details">
                <div class="tf-details-left">
                    <!-- menu section Start -->
                    <div class="tf-details-menu">
                        <ul>
							<?php
							if ( ! empty( tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel-layout-3'] ) ) {
								foreach ( tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel-layout-3'] as $section ) {
									if ( ! empty( $section['hotel-section-status'] ) && $section['hotel-section-status'] == "1" && ! empty( $section['hotel-section-slug'] ) ) {
										echo '<li><a class="tf-details-menu-item" href="#tf-hotel-'.esc_attr($section['hotel-section-slug']).'">'. esc_html( $section['hotel-section'] ) .'</a></li>';
									}
								}
							} else {
								?>
                                <li><a class="tf-details-menu-item" href="#tf-hotel-description"><?php echo esc_html__( "Description", "tourfic" ); ?></a></li>
								<?php if ( ! empty( $rooms ) ): ?>
                                    <li><a class="tf-details-menu-item" href="#tf-hotel-rooms"><?php echo esc_html__( "Rooms", "tourfic" ); ?></a></li>
								<?php endif; ?>
								<?php if ( ! empty( $hotel_facilities_categories ) && ! empty( $hotel_facilities ) ): ?>
                                    <li><a class="tf-details-menu-item" href="#tf-hotel-facilities"><?php echo esc_html__( "Amenities", "tourfic" ); ?></a></li>
								<?php endif; ?>
								<?php if ( ! empty( $faqs ) ): ?>
                                    <li><a class="tf-details-menu-item" href="#tf-hotel-faq"><?php echo esc_html__( "FAQ", "tourfic" ); ?></a></li>
								<?php endif; ?>
								<?php if ( ! $disable_review_sec == 1 ): ?>
                                    <li><a class="tf-details-menu-item" href="#tf-hotel-review"><?php echo esc_html__( "Reviews", "tourfic" ); ?></a></li>
								<?php endif; ?>
								<?php if ( ! empty( $tc ) ): ?>
                                    <li><a class="tf-details-menu-item" href="#tf-hotel-trams-condition"><?php echo esc_html__( "Policies", "tourfic" ); ?></a></li>
								<?php endif; ?>
							<?php } ?>
                        </ul>
                    </div>
                    <!-- menu section End -->

					<?php
					if ( ! empty( tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel-layout-3'] ) ) {
						foreach ( tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel-layout-3'] as $section ) {
							if ( ! empty( $section['hotel-section-status'] ) && $section['hotel-section-status'] == "1" && ! empty( $section['hotel-section-slug'] ) ) {
								include TF_TEMPLATE_PART_PATH . 'hotel/design-3/' . $section['hotel-section-slug'] . '.php';
							}
						}
					} else {
						include TF_TEMPLATE_PART_PATH . 'hotel/design-3/description.php';
						include TF_TEMPLATE_PART_PATH . 'hotel/design-3/rooms.php';
						include TF_TEMPLATE_PART_PATH . 'hotel/design-3/facilities.php';
						include TF_TEMPLATE_PART_PATH . 'hotel/design-3/faq.php';
						include TF_TEMPLATE_PART_PATH . 'hotel/design-3/review.php';
						include TF_TEMPLATE_PART_PATH . 'hotel/design-3/trams-condition.php';
					}
					?>
                </div>
                <div class="tf-details-right">
                    <div class="tf-sidebar-widgets">
						<?php if ( ( $tf_booking_type == 2 && $tf_hide_booking_form !== '1' && $tf_ext_booking_type == 1 ) || $tf_booking_type == 1 ) : ?>
                            <div class="tf-hotel-availability-form">
								<?php Hotel::tf_hotel_sidebar_booking_form(); ?>
                            </div>
						<?php endif; ?>
						<?php if ( ! empty( $tf_ext_booking_code ) && $tf_ext_booking_type == 2 ) : ?>
                            <div id="tf-external-booking-embaded-form" class="tf-hotel-availability-form">
								<?php echo wp_kses( $tf_ext_booking_code, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                            </div>
						<?php endif; ?>

                        <div id="hotel-map-location" class="tf-location tf-single-widgets">
							<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) : ?>

								<?php if ( $address && $tf_openstreet_map != "default" && ( empty( $address_latitude ) || empty( $address_longitude ) ) ): ?>
                                <iframe src="https://maps.google.com/maps?q=<?php echo esc_html( $address ); ?>&output=embed" width="100%" height="299" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
							<?php elseif ( $address && $tf_openstreet_map == "default" && ! empty( $address_latitude ) && ! empty( $address_longitude ) ): ?>
                                <div id="hotel-location" style="height: 299px;"></div>
                                <script>
                                    const map = L.map('hotel-location').setView([<?php echo esc_html( $address_latitude ); ?>, <?php echo esc_html( $address_longitude ); ?>], <?php echo esc_html( $address_zoom ); ?>);

                                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        maxZoom: 15,
                                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                                    }).addTo(map);

                                    const svgIcon = L.divIcon({
                                        html: `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="45" viewBox="0 0 32 45" fill="none">
                         <ellipse cx="16" cy="42.5" rx="11" ry="2.5" fill="#DABEA9"/>
                         <path d="M14 41.0171C9.66667 35.6849 0 22.9696 0 15.7506C0 7.05494 7.08333 0 16 0C24.8333 0 32 7.05494 32 15.7506C32 22.9696 22.25 35.6849 17.9167 41.0171C16.9167 42.2476 15 42.2476 14 41.0171ZM16 21.0008C18.9167 21.0008 21.3333 18.7038 21.3333 15.7506C21.3333 12.8794 18.9167 10.5004 16 10.5004C13 10.5004 10.6667 12.8794 10.6667 15.7506C10.6667 18.7038 13 21.0008 16 21.0008Z" fill="#FF6B00"/>
                       </svg>`,
                                        className: '',
                                        iconSize: [32, 45],
                                        iconAnchor: [16, 45],
                                        popupAnchor: [0, -45]
                                    });

                                    L.marker([<?php echo esc_html( $address_latitude ); ?>, <?php echo esc_html( $address_longitude ); ?>], {icon: svgIcon})
                                        .addTo(map)
                                        .bindPopup('<?php echo esc_html( $address ); ?>');
                                </script>
							<?php else: ?>
                                <iframe src="https://maps.google.com/maps?q=<?php echo esc_html( $address ); ?>&output=embed" width="100%" height="299" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
							<?php endif; ?>

							<?php else: ?>

							<?php if ( $address && $tf_openstreet_map != "default" && ( empty( $address_latitude ) || empty( $address_longitude ) ) ): ?>
                                <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address ); ?>&output=embed" width="100%" height="299" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
							<?php elseif ( $address && $tf_openstreet_map == "default" && ! empty( $address_latitude ) && ! empty( $address_longitude ) ): ?>
                                <div id="hotel-location" style="height: 299px;"></div>
                                <script>
                                    const map = L.map('hotel-location').setView([<?php echo esc_html( $address_latitude ); ?>, <?php echo esc_html( $address_longitude ); ?>], <?php echo esc_html( $address_zoom ); ?>);

                                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        maxZoom: 15,
                                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                                    }).addTo(map);

                                    const svgIcon = L.divIcon({
                                        html: `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="45" viewBox="0 0 32 45" fill="none">
                         <ellipse cx="16" cy="42.5" rx="11" ry="2.5" fill="#DABEA9"/>
                         <path d="M14 41.0171C9.66667 35.6849 0 22.9696 0 15.7506C0 7.05494 7.08333 0 16 0C24.8333 0 32 7.05494 32 15.7506C32 22.9696 22.25 35.6849 17.9167 41.0171C16.9167 42.2476 15 42.2476 14 41.0171ZM16 21.0008C18.9167 21.0008 21.3333 18.7038 21.3333 15.7506C21.3333 12.8794 18.9167 10.5004 16 10.5004C13 10.5004 10.6667 12.8794 10.6667 15.7506C10.6667 18.7038 13 21.0008 16 21.0008Z" fill="#FF6B00"/>
                       </svg>`,
                                        className: '',
                                        iconSize: [32, 45],
                                        iconAnchor: [16, 45],
                                        popupAnchor: [0, -45]
                                    });

                                    L.marker([<?php echo esc_html( $address_latitude ); ?>, <?php echo esc_html( $address_longitude ); ?>], {icon: svgIcon})
                                        .addTo(map)
                                        .bindPopup('<?php echo esc_html( $address ); ?>');
                                </script>
							<?php else: ?>
                                <iframe src="https://maps.google.com/maps?q=<?php echo esc_html( $address ); ?>&output=embed" width="100%" height="299" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
							<?php endif; ?>

							<?php endif; ?>

                            <?php 
                                $places_meta = ! empty( $meta["nearby-places"] ) ? Helper::tf_data_types($meta["nearby-places"]) : array();
                                if($places_meta){ ?>
                                <div class="tf-whats-around">
                                    <span class="tf-whats-around-title"><?php echo ! empty( $meta['section-title'] ) ? esc_html( $meta['section-title'] ) : esc_html( "What’s around?" ); ?></span>
                                    <ul>
										<?php foreach(Helper::tf_data_types($meta['nearby-places']) as $place) { ?>
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
                        </div>


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
								<?php if ( ! empty( $tf_enquiry_section_icon ) ) { ?>
                                    <div class="tf-enquiry-icon-wrap">
                                        <i class="<?php echo wp_kses_post( $tf_enquiry_section_icon ); ?>" aria-hidden="true"></i>
                                    </div>
								<?php }
								if ( ! empty( $tf_enquiry_section_title ) ) { ?>
                                    <h5><?php echo wp_kses_post( $tf_enquiry_section_title ); ?></h5>
								<?php }
								if ( ! empty( $tf_enquiry_section_cont ) ) { ?>
                                    <p><?php echo wp_kses_post( $tf_enquiry_section_cont ); ?></p>
								<?php }
								if ( ! empty( $tf_enquiry_section_button ) ) { ?>
                                    <div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="tf-send-inquiry-btn"><span><?php echo esc_html( $tf_enquiry_section_button ); ?></span></a></div>
								<?php } ?>
                            </div>
						<?php } ?>

                        <div class="tf-hotel-single-custom-widget-wrap">
							<?php do_action( "tf_hotel_single_widgets" ); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hotel details End -->

            <!-- Hotel room modal start -->
            <div class="tf-modal tf-room-modal">
                <div class="tf-modal-dialog">
                    <div class="tf-modal-content">
                        <div class="tf-modal-header">
                            <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                        </div>
                        <div class="row tf-modal-body"></div>
                    </div>
                </div>
            </div>
            <!-- Hotel room modal end -->
        </div>
    </div>
    <!--Content section end -->
</div>