<?php
/**
 * Template: Single Apartment (Full Width)
 */

get_header();

/**
 * Query start
 */
while ( have_posts() ) : the_post();

	// get post id
	$post_id = $post->ID;

	/**
	 * Review query
	 */
	$args           = array(
		'post_id' => $post_id,
		'status'  => 'approve',
		'type'    => 'comment',
	);
	$comments_query = new WP_Comment_Query( $args );
	$comments       = $comments_query->comments;

	/**
	 * Get apartment meta values
	 */
	$meta = get_post_meta( $post_id, 'tf_apartment_opt', true );

	$disable_share_opt   = ! empty( $meta['disable-apartment-share'] ) ? $meta['disable-apartment-share'] : '';
	$disable_review_sec  = ! empty( $meta['disable-apartment-review'] ) ? $meta['disable-apartment-review'] : '';
	$disable_related_sec = ! empty( $meta['disable-related-apartment'] ) ? $meta['disable-related-apartment'] : '';

	/**
	 * Get global settings value
	 */
	$s_share   = ! empty( tfopt( 'disable-apartment-share' ) ) ? tfopt( 'disable-apartment-share' ) : 0;
	$s_review  = ! empty( tfopt( 'disable-apartment-review' ) ) ? tfopt( 'disable-apartment-review' ) : 0;
	$s_related = ! empty( tfopt( 'disable-related-apartment' ) ) ? tfopt( 'disable-related-apartment' ) : 0;

	/**
	 * Disable Share and Review section
	 */
	$disable_share_opt   = ! empty( $disable_share_opt ) ? $disable_share_opt : $s_share;
	$disable_review_sec  = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;
	$disable_related_sec = ! empty( $disable_related_sec ) ? $disable_related_sec : $s_related;

	// Wishlist
	$post_type       = str_replace( 'tf_', '', get_post_type() );
	$has_in_wishlist = tf_has_item_in_wishlist( $post_id );

	/**
	 * Get locations
	 * apartment_location
	 */
	$locations = ! empty( get_the_terms( $post_id, 'apartment_location' ) ) ? get_the_terms( $post_id, 'apartment_location' ) : '';
	if ( $locations ) {
		$first_location_id   = $locations[0]->term_id;
		$first_location_term = get_term( $first_location_id );
		$first_location_name = $locations[0]->name;
		$first_location_slug = $locations[0]->slug;
		$first_location_url  = get_term_link( $first_location_term );
	}

	/**
	 * Get features
	 * apartment_feature
	 */
	$features = ! empty( get_the_terms( $post_id, 'apartment_feature' ) ) ? get_the_terms( $post_id, 'apartment_feature' ) : '';

	// Location
	$address = ! empty( $meta['address'] ) ? $meta['address'] : '';
	$map     = ! empty( $meta['map'] ) ? $meta['map'] : '';
	if ( ! empty( $map ) && gettype( $map ) == "string" ) {
		$tf_apartment_map_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $map );
		$map                    = unserialize( $tf_apartment_map_value );
	}

	// Apartment Gallery
	$gallery = ! empty( $meta['apartment_gallery'] ) ? $meta['apartment_gallery'] : '';
	if ( $gallery ) {
		$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
	}
	$video = ! empty( $meta['video'] ) ? $meta['video'] : '';

	// FAQ
	$faqs = ! empty( $meta['faq'] ) ? $meta['faq'] : '';
	if ( ! empty( $faqs ) && gettype( $faqs ) == "string" ) {
		$tf_apartment_faqs_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $faqs );
		$faqs                    = unserialize( $tf_apartment_faqs_value );
	}

	$share_text = get_the_title();
	$share_link = get_permalink( $post_id );
	?>

    <div class="tf-main-wrapper apartment-wrap">
		<?php do_action( 'tf_before_container' ); ?>

        <!-- Start title area -->
        <div class="tf-title-area tf-apartment-title sp-40">
            <div class="tf-container">
                <div class="tf-title-wrap">
                    <div class="tf-title-left">
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
						<?php if ( $comments && ! $disable_review_sec == '1' ): ?>
                            <div class="tf-top-review">
                                <a href="#tf-review">
                                    <div class="tf-single-rating">
                                        <i class="fas fa-star"></i> <span><?php echo tf_total_avg_rating( $comments ); ?></span> (<?php tf_based_on_text( count( $comments ) ); ?>)
                                    </div>
                                </a>
                            </div>
						<?php endif; ?>

						<?php if ( ! $disable_share_opt == '1' ) : ?>
                            <!-- Share Section -->
                            <div class="tf-share">
                                <a href="#dropdown-share-center" class="share-toggle"
                                   data-toggle="true"><i class="fas fa-share-alt"></i> Share</a>
                                <div id="dropdown-share-center" class="share-tour-content">
                                    <ul class="tf-dropdown-content">
                                        <li>
                                            <a href="http://www.facebook.com/share.php?u=<?php _e( $share_link ); ?>" class="tf-dropdown-item" target="_blank">
                                                <span class="tf-dropdown-item-content">
                                                    <i class="fab fa-facebook-square"></i>
                                                    <?php esc_html_e( 'Share on Facebook', 'tourfic' ); ?>
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="http://twitter.com/share?text=<?php _e( $share_text ); ?>&url=<?php _e( $share_link ); ?>" class="tf-dropdown-item" target="_blank">
                                                <span class="tf-dropdown-item-content">
                                                    <i class="fab fa-twitter-square"></i>
                                                    <?php esc_html_e( 'Share on Twitter', 'tourfic' ); ?>
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://www.linkedin.com/cws/share?url=<?php _e( $share_link ); ?>" class="tf-dropdown-item" target="_blank">
                                                <span class="tf-dropdown-item-content">
                                                    <i class="fab fa-linkedin"></i>
                                                    <?php esc_html_e( 'Share on Linkedin', 'tourfic' ); ?>
                                                </span>
                                            </a>
                                        </li>
										<?php
										$share_image_link = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
										?>
                                        <li>
                                            <a href="http://pinterest.com/pin/create/button/?url=<?php _e( $share_link ); ?>&media=<?php _e( $share_image_link[0] ); ?>&description=<?php _e( $share_text ); ?>"
                                               class="tf-dropdown-item" target="_blank">
                                                <span class="tf-dropdown-item-content">
                                                    <i class="fab fa-pinterest"></i>
                                                    <?php esc_html_e( 'Share on Pinterest', 'tourfic' ); ?>
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="share-center-copy-form tf-dropdown-item" title="Share this link" aria-controls="share_link_button">
                                                <label class="share-center-copy-label" for="share_link_input"><?php esc_html_e( 'Share this link', 'tourfic' ); ?></label>
                                                <input type="text" id="share_link_input" class="share-center-url share-center-url-input" value="<?php _e( $share_link ); ?>" readonly>
                                                <button id="share_link_button" class="tf_button share-center-copy-cta" tabindex="0" role="button">
                                                    <span class="tf-button-text share-center-copy-message"><?php _e( 'Copy link', 'tourfic' ); ?></span>
                                                    <span class="tf-button-text share-center-copied-message"><?php _e( 'Link Copied!', 'tourfic' ); ?></span>
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- End Share Section -->
						<?php endif; ?>

                        <!-- Wishlist Section -->
						<?php
						if ( tfopt( 'wl-bt-for' ) && in_array( '1', tfopt( 'wl-bt-for' ) ) ) {
							if ( is_user_logged_in() ) {
								if ( tfopt( 'wl-for' ) && in_array( 'li', tfopt( 'wl-for' ) ) ) {
									?>
                                    <a class="tf-wishlist-button" title="<?php _e( 'Click to toggle wishlist', 'tourfic' ); ?>"><i
                                                class="<?php echo $has_in_wishlist ? 'fas tf-text-red remove-wishlist' : 'far add-wishlist' ?> fa-heart" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>"
                                                data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) {
											echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"';
										} ?>> Save</i></a>
									<?php
								}
							} else {
								if ( tfopt( 'wl-for' ) && in_array( 'lo', tfopt( 'wl-for' ) ) ) {
									?>
                                    <a class="tf-wishlist-button" title="<?php _e( 'Click to toggle wishlist', 'tourfic' ); ?>"><i
                                                class="<?php echo $has_in_wishlist ? 'fas tf-text-red remove-wishlist' : 'far add-wishlist' ?> fa-heart" data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>"
                                                data-id="<?php echo $post_id ?>" data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) {
											echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"';
										} ?>> Save</i></a>
									<?php
								}
							}
						}
						?>
                        <!-- Wishlist Section -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End title area -->

        <!-- Start Hero Section -->
        <div class="hero-section tf-apartment">
            <div class="tf-container">
                <div class="hero-wrapper">
					<?php if ( ! empty( $gallery_ids ) ) :
						$first_image = ! empty( $gallery_ids[0] ) ? wp_get_attachment_image_url( $gallery_ids[0], 'tf_apartment_gallery_large' ) : '';
						$second_image = ! empty( $gallery_ids[1] ) ? wp_get_attachment_image_url( $gallery_ids[1], 'tf_apartment_gallery_small' ) : '';
						$third_image = ! empty( $gallery_ids[2] ) ? wp_get_attachment_image_url( $gallery_ids[2], 'tf_apartment_gallery_small' ) : '';
						?>
                        <div class="hero-left">
                            <div class="hero-first-image">
                                <a href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[0], 'full' ) ); ?>" data-fancybox="hotel-gallery">
                                    <img src="<?php echo esc_url( $first_image ); ?>" alt="">
                                </a>
                            </div>
                        </div>
						<?php if ( $second_image || $third_image ): ?>
                        <div class="hero-right">
							<?php if ( !empty($second_image) ): ?>
                                <div class="hero-second-image">
                                    <a href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[1], 'full' ) ); ?>" data-fancybox="hotel-gallery">
                                        <img src="<?php echo esc_url( $second_image ); ?>" alt="">
                                    </a>
                                </div>
							<?php endif; ?>
							<?php if ( ! empty( $third_image ) ): ?>
                                <div class="hero-third-image">
                                    <a href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[2], 'full' ) ); ?>" data-fancybox="hotel-gallery">
                                        <img src="<?php echo esc_url( $third_image ); ?>" alt="">
                                    </a>
									<?php if ( count( $gallery_ids ) > 3 ): ?>
                                        <a href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[3], 'full' ) ); ?>" class="tf_button btn-styled" data-fancybox="hotel-gallery">
											<?php _e( 'All Photos', 'tourfic' ) ?>
                                        </a>
										<?php foreach ( $gallery_ids as $key => $item ) :
											if ( $key < 4 ) {
												continue;
											}
											?>
                                            <a href="<?php echo esc_url( wp_get_attachment_image_url( $item, 'full' ) ); ?>" data-fancybox="hotel-gallery"></a>
										<?php endforeach; ?>
									<?php endif; ?>
                                </div>
							<?php endif; ?>
                        </div>
					<?php endif; ?>
					<?php else: ?>
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'tf_apartment_single_thumb' );
						}
						?>
					<?php endif; ?>

                </div>
            </div>
        </div>
        <!-- End Hero Section -->

        <!-- Start Content & Feature Section -->
        <div class="content-feature-section tf-apartment">
            <div class="tf-container">
                <div class="cf-wrapper">
                    <div class="cf-left">
                        <!-- Start Description Section -->
                        <div class="apt-description">
							<?php the_content(); ?>
                        </div>


						<?php if ( ! empty( $features ) ) : ?>
                            <!-- Start Key Features Section -->
                            <div class="key-features sp-t-40">
                                <div class="features-details">
                                    <ul>
										<?php foreach ( $features as $feature ):
											$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_apartment_feature', true );
											$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
											if ( $f_icon_type == 'icon' ) {
												$feature_icon = '<i class="' . $feature_meta['apartment-feature-icon'] . '"></i>';
											} elseif ( $f_icon_type == 'custom' ) {
												$feature_icon = '<img src="' . esc_url( $feature_meta['apartment-feature-icon-custom'] ) . '" style="width: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px; height: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px;" />';
											} ?>
                                            <li><?php echo $feature_icon ?? ''; ?> <?php echo $feature->name; ?> <span><?php echo wp_kses_post( $feature->description ); ?></span></li>
										<?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
						<?php endif; ?>

						<?php if ( ! empty( tf_data_types( $meta['amenities'] ) ) ) : ?>
                            <!-- Start Amenities Section -->
                            <div class="apartment-amenities sp-t-50">
								<?php if ( ! empty( $meta['amenities_title'] ) ): ?>
                                    <h2 class="section-heading"><?php echo esc_html( $meta['amenities_title'] ) ?></h2>
								<?php endif; ?>

                                <div class="features-details amenities-details">
                                    <ul>
										<?php
										foreach ( tf_data_types( $meta['amenities'] ) as $amenity ) {
											if ( empty( $amenity['title'] ) ) {
												continue;
											}
											$amenity_icon = ! empty( $amenity['icon'] ) ? '<i class="' . esc_attr( $amenity['icon'] ) . '"  ></i>' : '';
											echo '<li>' . $amenity_icon . esc_html( $amenity['title'] ) . '</li>';
										}
										?>
                                    </ul>
                                </div>
                            </div>
						<?php endif; ?>

						<?php if ( ! empty( tf_data_types( $meta['facilities'] ) ) ) : ?>
                            <!-- Start What you will get here Section -->
                            <div class="apartment-options sp-t-50">
								<?php if ( ! empty( $meta['facilities_title'] ) ): ?>
                                    <h2 class="section-heading"><?php echo esc_html( $meta['facilities_title'] ) ?></h2>
								<?php endif; ?>
                                <div class="tf-apartment-option-slider-wrapper">
									<?php foreach ( tf_data_types( $meta['facilities'] ) as $facility ) : ?>
                                        <div class="tf-apartment-option-slider-item">
                                            <div class="tf-apartment-option-slider-content">
                                                <img src="<?php echo esc_url( $facility['thumbnail'] ) ?>" alt="">
                                                <div class="tf-apartment-option-slider-desc">
													<?php echo ! empty( $facility['title'] ) ? '<h3>' . esc_html( $facility['title'] ) . '</h3>' : ''; ?>
													<?php echo ! empty( $facility['subtitle'] ) ? '<p>' . esc_html( $facility['subtitle'] ) . '</p>' : ''; ?>
                                                </div>
                                            </div>
                                        </div>
									<?php endforeach; ?>
                                </div>
                            </div>
						<?php endif; ?>
                    </div>

                    <div class="cf-right">
                        <div class="host-details">
                            <div class="host-top">
                                <img src="https://cdn.pixabay.com/photo/2018/01/06/09/25/hijab-3064633_960_720.jpg" alt="">
                                <div class="host-meta">
                                    <h4>Hosted by Jorina Bua</h4>
                                    <p>Joined in <span>January 2022</span></p>
                                    <p><i class="fas fa-star"></i> <span>4.5</span> (150 Reviews)</p>
                                </div>
                            </div>
                            <div class="host-bottom">
                                <p class="host-desc">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it
                                    has a more-or-less normal distribution of letters.</p>
                                <ul>
                                    <li>Language: <span>Bangla, English, Hindi</span></li>
                                    <li>Response Time: <span>Within a Day</span></li>
                                </ul>
                                <a href="" class="tf_button btn-styled"><i class="far fa-comments"></i> Contact Host</a>
                            </div>
                        </div>
                        <div class="apartment-booking-form">
                            <h3>Form for Booking</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Content & Feature Section -->

		<?php if ( ! empty( $map['address'] ) || ( ! empty( $meta['location_title'] ) && ! empty( $meta['location_description'] ) ) ): ?>
            <!-- Map Section Start -->
            <div id="tour-map" class="tf-map-wrapper sp-t-70">
                <div class="tf-container">
                    <div class="tf-row">
                        <div class="tf-map-content-wrapper">
                            <div class="about-location">
								<?php echo ! empty( $meta['location_sec_title'] ) ? '<h2 class="section-heading">' . esc_html( $meta['location_sec_title'] ) . '</h2>' : ''; ?>
								<?php echo ! empty( $meta['location_title'] ) ? '<h4>' . esc_html( $meta['location_title'] ) . '</h4>' : ''; ?>
								<?php echo ! empty( $meta['location_description'] ) ? '<p>' . esc_html( $meta['location_description'] ) . '</p>' : ''; ?>
                            </div>
							<?php if ( ! empty( $map['address'] ) ): ?>
                                <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $map["latitude"] ); ?>,<?php echo esc_attr( $map["longitude"] ); ?>&z=15&output=embed" width="100%" height="400"
                                        style="border:0;" allowfullscreen="" loading="lazy"></iframe>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Map Section End -->
		<?php endif; ?>

		<?php if ( ! $disable_review_sec == 1 ) : ?>
            <!-- Start Review Section -->
            <div id="tf-review" class="review-section sp-50">
                <div class="tf-container">
                    <div class="reviews">
                        <h2 class="section-heading"><?php _e( 'Guest Reviews', 'tourfic' ); ?></h2>
						<?php comments_template(); ?>
                    </div>
                </div>
            </div>
            <!-- End Review Section -->
		<?php endif; ?>

		<?php if ( $faqs ): ?>
            <!-- FAQ section Start -->
            <div class="tf-faq-wrapper tf-apartment-faq sp-30">
                <div class="tf-container">
                    <div class="tf-faq-sec-title">
						<?php echo ! empty( $meta['faq_title'] ) ? '<h2 class="section-heading">' . esc_html( $meta['faq_title'] ) . '</h2>' : ''; ?>
						<?php echo ! empty( $meta['faq_desc'] ) ? '<p>' . wp_kses_post( $meta['faq_desc'] ) . '</p>' : ''; ?>
                    </div>

                    <div class="tf-faq-content-wrapper">
                        <div class="tf-faq-items-wrapper">
							<?php foreach ( $faqs as $key => $faq ): ?>
                                <div id="tf-faq-item">
                                    <div class="tf-faq-title">
                                        <h4><?php esc_html_e( $faq['title'] ); ?></h4>
                                        <i class="fas fa-angle-down arrow"></i>
                                    </div>
                                    <div class="tf-faq-desc">
										<?php echo wp_kses_post( $faq['description'] ); ?>
                                    </div>
                                </div>
							<?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- FAQ section end -->
		<?php endif; ?>

        <!-- Start Question Content -->
        <div class="tf-ask-question apartment-question sp-40">
            <div class="tf-container">
                <div class="apartment-qa-wrapper">
                    <div class="question-left">
                        <h3><?php _e( "Have a question in mind", 'tourfic' ); ?></h3>
                        <p><?php _e( "Looking for more info? Send a question to the property to find out more.", 'tourfic' ); ?></p>
                    </div>
                    <div class="tf-btn">
                        <a href="#" id="tf-ask-question-trigger" class="btn-styled"><span><?php esc_html_e( 'Contact Host', 'tourfic' ); ?></span></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Question Content -->

		<?php if ( ! empty( $meta['terms_and_conditions'] ) ) : ?>
            <!-- Start TOC Content -->
            <div class="toc-section apartment-toc sp-50">
                <div class="tf-container">
                    <div class="tf-toc-wrap gray-wrap">
						<?php echo ! empty( $meta['terms_title'] ) ? '<h2 class="section-heading">' . esc_html( $meta['terms_title'] ) . '</h2>' : ''; ?>
                        <div class="tf-toc-inner">
							<?php echo wp_kses_post( $meta['terms_and_conditions'] ); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End TOC Content -->
		<?php endif; ?>


		<?php
		$args              = array(
			'post_type'      => 'tf_apartment',
			'post_status'    => 'publish',
			'posts_per_page' => 8,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post__not_in'   => array( $post_id ),
			'tax_query'      => array(
				array(
					'taxonomy' => 'apartment_location',
					'field'    => 'term_id',
					'terms'    => wp_list_pluck( $locations, 'term_id' ),
				),
			),
		);
		$related_apartment = new WP_Query( $args );

		if ( ! $disable_related_sec == '1' && $related_apartment->have_posts() ) : ?>
            <!-- Apartment Suggestion section Start -->
            <div class="apartment-options apartment-sugestion sp-40">
                <div class="tf-container">
                    <h2 class="section-heading">Related Properties</h2>
                    <div class="tf-apartment-sugestion-slider-wrapper">

						<?php while ( $related_apartment->have_posts() ) : $related_apartment->the_post(); ?>
                            <div class="tf-apartment-option-slider-item">
                                <div class="tf-apartment-option-slider-content">
									<?php if ( has_post_thumbnail() ) : ?>
                                        <div class="tf-apartment-option-slider-img">
                                            <a href="<?php the_permalink(); ?>">
												<?php the_post_thumbnail( 'tourfic-370x250' ); ?>
                                            </a>
                                        </div>
									<?php endif; ?>
                                    <div class="tf-apartment-option-slider-desc">
                                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                                    </div>
                                </div>
                            </div>
						<?php endwhile;
						wp_reset_query(); ?>
                    </div>
                </div>
            </div>
            <!-- Apartment suggestion section End -->
		<?php endif; ?>

		<?php do_action( 'tf_after_container' ); ?>
    </div>
<?php endwhile; ?>
<?php
get_footer();