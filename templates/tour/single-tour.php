<?php
/**
 * Template: Signle Tour (Full width)
 */
// Get header
get_header();

// Main query
while ( have_posts() ) : the_post();

	// get post id
	$post_id = get_the_ID();

	// Get Tour Meta
	$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
	/**
	 * Show/hide sections
	 */
	$disable_review_sec   = ! empty( $meta['t-review'] ) ? $meta['t-review'] : '';
	$disable_related_tour = ! empty( $meta['t-related'] ) ? $meta['t-related'] : '';

	/**
	 * Get global settings value
	 */
	$s_review  = ! empty( tfopt( 't-review' ) ) ? tfopt( 't-review' ) : '';
	$s_related = ! empty( tfopt( 't-related' ) ) ? tfopt( 't-related' ) : '';

	/**
	 * Disable Review Section
	 */
	$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;

	/**
	 * Disable Related Tour
	 */
	$disable_related_tour = ! empty( $disable_related_tour ) ? $disable_related_tour : $s_related;


	// Get destination
	$destinations           = get_the_terms( $post_id, 'tour_destination' );
	$first_destination_slug = ! empty( $destinations ) ? $destinations[0]->slug : '';

	// Wishlist
	$post_type       = substr( get_post_type(), 3, - 1 );
	$has_in_wishlist = tf_has_item_in_wishlist( $post_id );

	// Address
	$location = isset( $meta['text_location'] ) ? $meta['text_location'] : '';
	
	if( !empty($meta['location']) && tf_data_types($meta['location'])){
		$location = !empty( tf_data_types($meta['location'])['address'] ) ? tf_data_types($meta['location'])['address'] : $location;
    }
	// Gallery
	$gallery = ! empty( $meta['tour_gallery'] ) ? $meta['tour_gallery'] : array();
	if ( $gallery ) {
		$gallery_ids = explode( ',', $gallery );
	}
	$hero_title = ! empty( $meta['hero_title'] ) ? $meta['hero_title'] : '';

	// Highlights
	$highlights = ! empty( $meta['additional_information'] ) ? $meta['additional_information'] : '';
	// Informations
	$tour_duration = ! empty( $meta['duration'] ) ? $meta['duration'] : '';
	$group_size    = ! empty( $meta['group_size'] ) ? $meta['group_size'] : '';
	$language      = ! empty( $meta['language'] ) ? $meta['language'] : '';
	/**
	 * Get features
	 * hotel_feature
	 */
	$features = ! empty( get_the_terms( $post_id, 'tour_features' ) ) ? get_the_terms( $post_id, 'tour_features' ) : '';

	$min_days = ! empty( $meta['min_days'] ) ? $meta['min_days'] : '';

	$faqs            = !empty($meta['faqs']) ? $meta['faqs'] : null;
	if( !empty($faqs) && gettype($faqs)=="string" ){
        $tf_hotel_faqs_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $faqs );
        $faqs = unserialize( $tf_hotel_faqs_value );
    }
	$inc             = !empty($meta['inc']) ? $meta['inc'] : null;
	if( !empty($inc) && gettype($inc)=="string" ){
        $tf_hotel_inc_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $inc );
        $inc = unserialize( $tf_hotel_inc_value );
    }
	$exc             = !empty($meta['exc']) ? $meta['exc'] : null;
	if( !empty($exc) && gettype($exc)=="string" ){
        $tf_hotel_exc_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $exc );
        $exc = unserialize( $tf_hotel_exc_value );
	}

	$inc_icon        = ! empty( $meta['inc_icon'] ) ? $meta['inc_icon'] : null;
	$exc_icon        = ! empty( $meta['exc_icon'] ) ? $meta['exc_icon'] : null;
	$custom_inc_icon = ! empty( $inc_icon ) ? "custom-inc-icon" : '';
	$custom_exc_icon = ! empty( $exc_icon ) ? "custom-exc-icon" : '';
	$itineraries     = !empty($meta['itinerary']) ? $meta['itinerary'] : null;
	if( !empty($itineraries) && gettype($itineraries)=="string" ){
        $tf_hotel_itineraries_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $itineraries );
        $itineraries = unserialize( $tf_hotel_itineraries_value );
    }
	//continuous tour
	$share_text = get_the_title();
	$share_link = esc_url( home_url( "/?p=" ) . $post_id );

	$terms_and_conditions = ! empty( $meta['terms_conditions'] ) ? $meta['terms_conditions'] : '';
	$tf_faqs              = ( get_post_meta( $post->ID, 'tf_faqs', true ) ) ? get_post_meta( $post->ID, 'tf_faqs', true ) : array();

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
	 * Pricing
	 */
	$pricing_rule = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
	$tour_type    = ! empty( $meta['type'] ) ? $meta['type'] : '';
	if ( $tour_type && $tour_type == 'continuous' ) {
		$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
	}
	$discount_type  = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : 'none';
	$disable_adult  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
	$disable_child  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
	$disable_infant = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
	if ( $tour_type == 'continuous' && $custom_avail == true ) {
		$pricing_rule = ! empty( $meta['custom_pricing_by'] ) ? $meta['custom_pricing_by'] : 'person';
	}

	# Get Pricing
	$tour_price = new Tour_Price( $meta );
	?>

    <div class="tf-main-wrapper">
		<?php do_action( 'tf_before_container' ); ?>
        <!-- Hero section Start -->
        <div class="tf-hero-wrapper">
            <div class="tf-container">
                <div class="tf-hero-content" style="background-image: url(<?php echo esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ); ?>);">
                    <div class="tf-hero-top">
                        <div class="tf-top-review">
							<?php if ( $comments && ! $disable_review_sec == '1' ) { ?>
                                <a href="#tf-review">
                                    <div class="tf-single-rating">
                                        <i class="fas fa-star"></i> <span><?php echo tf_total_avg_rating( $comments ); ?></span> (<?php tf_based_on_text( count( $comments ) ); ?>)
                                    </div>
                                </a>
							<?php } ?>
                        </div>
                        <div class="tf-wishlist">
							<?php
							// Wishlist
							if ( tfopt( 'wl-bt-for' ) && in_array( '2', tfopt( 'wl-bt-for' ) ) ) {
								if ( is_user_logged_in() ) {
									if ( tfopt( 'wl-for' ) && in_array( 'li', tfopt( 'wl-for' ) ) ) {
										?>
                                        <span class="single-tour-wish-bt"><i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>"
                                                                             data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>" data-id="<?php echo $post_id ?>"
                                                                             data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) {
												echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"';
											} ?>></i></span>
										<?php
									}
								} else {
									if ( tfopt( 'wl-for' ) && in_array( 'lo', tfopt( 'wl-for' ) ) ) {
										?>
                                        <span class="single-tour-wish-bt"><i class="far <?php echo $has_in_wishlist ? 'fa-heart tf-text-red remove-wishlist' : 'fa-heart-o add-wishlist' ?>"
                                                                             data-nonce="<?php echo wp_create_nonce( "wishlist-nonce" ) ?>" data-id="<?php echo $post_id ?>"
                                                                             data-type="<?php echo $post_type ?>" <?php if ( tfopt( 'wl-page' ) ) {
												echo 'data-page-title="' . get_the_title( tfopt( 'wl-page' ) ) . '" data-page-url="' . get_permalink( tfopt( 'wl-page' ) ) . '"';
											} ?>></i></span>
										<?php
									}
								}
							}
							?>
                        </div>
                    </div>
                    <div class="tf-tours-form-wrap">
						<?php echo tf_single_tour_booking_form( $post->ID ); ?>
                    </div>
                    <div class="tf-hero-bottom-area">
						<?php
						$tour_video = ! empty( $meta['tour_video'] ) ? $meta['tour_video'] : '';
						if ( !empty($tour_video) ) {
							?>
                            <div class="tf-hero-btm-icon tf-tour-video" data-fancybox="tour-video" href="<?php echo $tour_video; ?>">
                                <i class="fab fa-youtube"></i>
                            </div>
						<?php }
						// Gallery
						if ( ! empty( $gallery_ids ) ) {
							foreach ( $gallery_ids as $key => $gallery_item_id ) {
								$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
								if ( $key === array_key_first( $gallery_ids ) ) {
									?>
                                    <div data-fancybox="tour-gallery" class="tf-hero-btm-icon tf-tour-gallery" data-src="<?php echo $image_url; ?>">
                                        <i class="far fa-image"></i>
                                    </div>
								<?php } else {
									echo '<a data-fancybox="tour-gallery" href="' . $image_url . '" style="display:none;"></a>';
								}
							}
						}
						?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero section end -->

        <!-- Start title area -->
        <div class="tf-title-area tf-tour-title sp-30">
            <div class="tf-container">
                <div class="tf-title-wrap">
                    <div class="tf-title-left">
                        <h1><?php the_title(); ?></h1>
                        <!-- Start map link -->
                        <div class="tf-map-link">
							<?php if ( $location ) {
								echo '<a href="#tour-map"><span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' . $location . '.</span></a>';
							} ?>
                        </div>
                        <!-- End map link -->
                    </div>

                    <div class="tf-title-right">
                        <div class="tf-single-tour-pricing">
							<?php if ( $pricing_rule == 'group' ) { ?>

                                <div class="tf-price group-price">
									<span class="sale-price">
										<?php echo $tour_price->wc_sale_group ?? $tour_price->wc_group; ?>
									</span>
									<?php echo ( $discount_type != 'none' ) ? '<del>' . $tour_price->wc_group . '</del>' : ''; ?>
                                </div>

							<?php } elseif ( $pricing_rule == 'person' ) { ?>

								<?php if ( ! $disable_adult && ! empty( $tour_price->adult ) ) { ?>

                                    <div class="tf-price adult-price">
										<span class="sale-price">
											<?php echo $tour_price->wc_sale_adult ?? $tour_price->wc_adult; ?>
										</span>
										<?php echo ( $discount_type != 'none' ) ? '<del>' . $tour_price->wc_adult . '</del>' : ''; ?>
                                    </div>

								<?php }
								if ( ! $disable_child && ! empty( $tour_price->child ) ) { ?>

                                    <div class="tf-price child-price tf-d-n">
										<span class="sale-price">
											<?php echo $tour_price->wc_sale_child ?? $tour_price->wc_child; ?>
										</span>
										<?php echo ( $discount_type != 'none' ) ? '<del>' . $tour_price->wc_child . '</del>' : ''; ?>
                                    </div>

								<?php }
								if ( ! $disable_infant && ! empty( $tour_price->infant ) ) { ?>

                                    <div class="tf-price infant-price tf-d-n">
										<span class="sale-price">
											<?php echo $tour_price->wc_sale_infant ?? $tour_price->wc_infant; ?>
										</span>
										<?php echo ( $discount_type != 'none' ) ? '<del>' . $tour_price->wc_infant . '</del>' : ''; ?>
                                    </div>

								<?php } ?>
								<?php
							}
							?>
                            <ul class="tf-price-tab">
								<?php
								if ( $pricing_rule == 'group' ) {

									echo '<li id="group" class="active">' . __( "Group", "tourfic" ) . '</li>';

								} elseif ( $pricing_rule == 'person' ) {

									if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
										echo '<li id="adult" class="active">' . __( "Adult", "tourfic" ) . '</li>';
									}
									if ( ! $disable_child && ! empty( $tour_price->child ) ) {
										echo '<li id="child">' . __( "Child", "tourfic" ) . '</li>';
									}
									if ( ! $disable_infant && ! empty( $tour_price->infant ) ) {
										echo '<li id="infant">' . __( "Infant", "tourfic" ) . '</li>';
									}

								}
								?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End title area -->

        <div class="tf-container">
            <div class="tf-divider"></div>
        </div>

        <!-- Start description -->
        <div class="description-section sp-30">
            <div class="tf-container">
                <div class="desc-wrap">
					<?php the_content(); ?>
                </div>

                <!-- Square block section Start -->
				<?php if ( $tour_duration || $tour_type || $group_size || $language ) { ?>
                    <div class="tf-square-block sp-20">
                        <div class="tf-square-block-content">
							<?php if ( $tour_duration ) { ?>
                                <div class="tf-single-square-block first">
                                    <i class="fas fa-clock"></i>
                                    <h4><?php echo __( 'Duration', 'tourfic' ); ?></h4>
                                    <p><?php echo esc_html( $tour_duration ); ?></p>
                                </div>
							<?php } ?>
							<?php if ( $tour_type ) { ?>
                                <div class="tf-single-square-block second">
                                    <i class="fas fa-map"></i>
                                    <h4><?php echo __( 'Tour Type', 'tourfic' ); ?></h4>
                                    <p><?php echo ucfirst( esc_html( $tour_type ) ) ?></p>
                                </div>
							<?php } ?>
							<?php if ( $group_size ) { ?>
                                <div class="tf-single-square-block third">
                                    <i class="fas fa-users"></i>
                                    <h4><?php echo __( 'Group Size', 'tourfic' ); ?></h4>
                                    <p><?php echo esc_html( $group_size ) ?></p>
                                </div>
							<?php } ?>
							<?php if ( $language ) { ?>
                                <div class="tf-single-square-block fourth">
                                    <i class="fas fa-language"></i>
                                    <h4><?php echo __( 'Language', 'tourfic' ); ?></h4>
                                    <p><?php echo esc_html( $language ) ?></p>
                                </div>
							<?php } ?>
                        </div>
                    </div>
				<?php } ?>
                <!-- Square block section End -->
            </div>
        </div>
        <!-- End description -->
		
		<?php if ( $highlights ) : ?>
		<!-- Highlight section Start -->
        <div class="tf-highlight-wrapper gray-wrap sp-50">
            <div class="tf-container">
                <div class="tf-highlight-content">
					<div class="tf-highlight-item">
						<div class="tf-highlight-text">
							<h2 class="section-heading"><?php _e( 'Highlights', 'tourfic' ); ?></h2>
							<?php echo $highlights; ?>
						</div>
						<?php if ( ! empty( $meta['hightlights_thumbnail'] ) ): ?>
							<div class="tf-highlight-image">
								<img src="<?php echo esc_url( $meta['hightlights_thumbnail'] ); ?>" alt="">
							</div>
						<?php endif; ?>
					</div>
                </div>
            </div>
        </div>
        <!-- Highlight section end -->
		<?php endif; ?>

		<?php if ( $features ) { ?>
			<!-- Start features -->
			<div class="tf_features sp-50">
			    <div class="tf-container">
					<h3 class="section-heading"><?php esc_html_e( 'Popular Features', 'tourfic' ); ?></h3>
					<div class="tf-feature-list">
						<?php foreach ( $features as $feature ) {
							$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tour_features', true );
							$f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
							if ( $f_icon_type == 'fa' ) {
								$feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
							} elseif ( $f_icon_type == 'c' ) {
								$feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
							} ?>

							<div class="single-feature-box">
								<?php echo $feature_icon ?? ''; ?>
								<p class="feature-list-title"><?php echo $feature->name; ?></p>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<!-- End features -->
		<?php } ?>
        <!-- Include-Exclude section Start -->
		<?php
		if ( $inc || $exc ) :
			$inc_exc_bg = ! empty( $meta['include-exclude-bg'] ) ? $meta['include-exclude-bg'] : '';
			?>
            <div class="tf-inc-exc-wrapper sp-70" style="background-image: url(<?php echo esc_url( $inc_exc_bg ) ?>);">
                <div class="tf-container">
                    <div class="tf-inc-exc-content">
						<?php if ( $inc ) { ?>
                            <div class="tf-include-section <?php echo esc_attr( $custom_inc_icon ); ?>">
                                <h4><?php _e( 'Included', 'tourfic' ); ?></h4>
                                <ul>
									<?php
									foreach ( $inc as $key => $val ) {
										echo "<li><i class='" . esc_attr( $inc_icon ) . "'></i>" . $val['inc'] . "</li>";
									}
									?>
                                </ul>
                            </div>
						<?php } ?>
						<?php if ( $exc ) { ?>
                            <div class="tf-exclude-section <?php echo esc_attr( $custom_exc_icon ); ?>">
                                <h4><?php _e( 'Excluded', 'tourfic' ); ?></h4>
                                <ul>
									<?php
									foreach ( $exc as $key => $val ) {
										echo "<li><i class='" . esc_attr( $exc_icon ) . "'></i>" . $val['exc'] . "</li>";
									}
									?>
                                </ul>
                            </div>
						<?php } ?>
                    </div>
                </div>
            </div>
		<?php endif; ?>
        <!-- Include-Exclude section End -->

        <!-- Travel Itinerary section Start -->
		<?php
		if ( function_exists('is_tf_pro') && is_tf_pro() ) {
			do_action( 'after_itinerary_builder', $itineraries );
		} else {
			?>
            <!-- Travel Itinerary section Start -->
			<?php if ( $itineraries ) { ?>
                <div class="tf-travel-itinerary-wrapper gray-wrap sp-50">
                    <div class="tf-container">
                        <div class="tf-travel-itinerary-content">
                            <h2 class="section-heading"><?php _e( "Travel Itinerary", 'tourfic' ); ?></h2>
                            <div class="tf-travel-itinerary-items-wrapper">
								<?php foreach ( $itineraries as $itinerary ) { ?>
                                    <div id="tf-accordion-wrapper">
                                        <div class="tf-accordion-head">
                                            <div class="tf-travel-time">
                                                <span><?php echo esc_html( $itinerary['time'] ) ?></span>
                                            </div>
                                            <h4><?php echo esc_html( $itinerary['title'] ); ?></h4>
                                            <i class="fas fa-angle-down arrow"></i>
                                        </div>
                                        <div class="tf-accordion-content">
                                            <div class="tf-travel-desc">
												<?php if ( $itinerary['image'] ) {
													echo '<div class="tf-ititnerary-img"><a class="tf-itinerary-gallery" href="' . esc_url( $itinerary['image'] ) . '"><img src="' . esc_url( $itinerary['image'] ) . '"></a></div>';
												} ?>
                                                <div class="trav-cont tf-travel-description">
                                                    <p><?php _e( $itinerary['desc'] ); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

				<!-- Itinerary map -->
				<div id="tf-map"></div>

			<?php }
		} ?>
        <!-- Travel Itinerary section End -->

        <!-- Map Section Start -->
		<?php if ( $location ): ?>
            <div id="tour-map" class="tf-map-wrapper">
                <div class="tf-container">
                    <div class="tf-row">
                        <div class="tf-map-content-wrapper">
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $location ) ); ?>&output=embed" width="100%" height="600" style="border:0;" allowfullscreen=""
                                    loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            </div>
		<?php endif; ?>
        <!-- Map Section End -->

        <!-- FAQ section Start -->
		<?php if ( $faqs ): ?>
            <div class="tf-faq-wrapper tour-faq sp-50">
                <div class="tf-container">
                    <div class="tf-faq-sec-title">
                        <h2 class="section-heading"><?php _e( "Frequently Asked Questions", 'tourfic' ); ?></h2>
                        <p><?php _e( "Let’s clarify your confusions. Here are some of the Frequently Asked Questions which most of our client asks.", 'tourfic' ); ?></p>
                    </div>

                    <div class="tf-faq-content-wrapper">
                        <div class="tf-ask-question">
                            <h3><?php _e( "Have a question in mind", 'tourfic' ); ?></h3>
                            <p><?php _e( "Looking for more info? Send a question to the property to find out more.", 'tourfic' ); ?></p>
                            <div class="tf-btn"><a href="#" id="tf-ask-question-trigger" class="btn-styled"><span><?php esc_html_e( 'Ask a Question', 'tourfic' ); ?></span></a></div>
                        </div>

                        <div class="tf-faq-items-wrapper">
							<?php foreach ( $faqs as $key => $faq ): ?>
                                <div id="tf-faq-item">
                                    <div class="tf-faq-title">
                                        <h4><?php echo esc_html( $faq['title'] ); ?></h4>
                                        <i class="fas fa-angle-down arrow"></i>
                                    </div>
                                    <div class="tf-faq-desc">
                                        <?php echo wp_kses_post( $faq['desc'] ); ?>
                                    </div>
                                </div>
							<?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
		<?php endif; ?>
        <!-- FAQ section end -->

        <!-- Start TOC Content -->
		<?php if ( $terms_and_conditions ) : ?>
            <div class="toc-section gray-wrap sp-50">
                <div class="tf-container">
                    <div class="tf-toc-wrap">
                        <h2 class="section-heading"><?php _e( 'Tour Terms & Conditions', 'tourfic' ); ?></h2>
                        <div class="tf-toc-inner">
							<?php echo wpautop( $terms_and_conditions ); ?>
                        </div>
                    </div>
                </div>
            </div>
		<?php endif; ?>
        <!-- End TOC Content -->

        <!-- Start Review Section -->
		<?php if ( ! $disable_review_sec == 1 ) { ?>
            <div id="tf-review" class="review-section sp-50">
                <div class="tf-container">
                    <div class="reviews">
                        <h2 class="section-heading"><?php _e( 'Guest Reviews', 'tourfic' ); ?></h2>
						<?php comments_template(); ?>
                    </div>
                </div>
            </div>
		<?php } ?>
        <!-- End Review Section -->

        <!-- Tours suggestion section Start -->
		<?php if ( ! $disable_related_tour == '1' ) {

			$related_tour_type = tfopt('rt_display');
			$args  = array(
				'post_type'      => 'tf_tours',
				'post_status'    => 'publish',
				'posts_per_page' => 8,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'tax_query'      => array(
					array(
						'taxonomy' => 'tour_destination',
						'field'    => 'slug',
						'terms'    => $first_destination_slug,
					),
				),
			);
			//show related tour based on selected tours
			$selected_ids = tfopt('tf-related-tours');
			$args['post__not_in'] = array( $post_id );

			if( $related_tour_type == 'selected' && defined( 'TF_PRO' ) ){
				$args['post__in'] = $selected_ids;
			}
			$tours = new WP_Query( $args );
			if ( $tours->have_posts() ) {
				?>

                <div class="tf-suggestion-wrapper gray-wrap sp-50">
                    <div class="tf-container">
                        <div class="tf-slider-content-wrapper">
                            <div class="tf-suggestion-sec-head">
                                <h2 class="section-heading"><?php echo __( 'You might also like', 'tourfic' ) ?></h2>
                                <p><?php echo __( 'Travel is my life. Since 1999, I’ve been traveling around the world nonstop. If you also love travel, you’re in the right place!', 'tourfic' ) ?></p>
                            </div>

                            <div class="tf-slider-items-wrapper">
								<?php
								while ( $tours->have_posts() ) {
									$tours->the_post();

									$post_id                = get_the_ID();
									$destinations           = get_the_terms( $post_id, 'tour_destination' );
									$first_destination_name = $destinations[0]->name;
									$related_comments       = get_comments( array( 'post_id' => $post_id ) );
									$meta                   = get_post_meta( $post_id, 'tf_tours_opt', true );
									$pricing_rule           = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
									$disable_adult          = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
									$disable_child          = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
									$tour_price             = new Tour_Price( $meta );
									?>

                                    <div class="tf-slider-item" style="background-image: url(<?php echo get_the_post_thumbnail_url( $post_id, 'full' ); ?>);">
                                        <div class="tf-slider-content">
                                            <div class="tf-slider-desc">
                                                <h3>
                                                    <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                                                    <span><?php echo $first_destination_name; ?></span>
                                                </h3>
                                            </div>
                                            <div class="tf-suggestion-rating">
                                                <div class="tf-suggestion-price">
                                        <span>
                                        <?php if ( $pricing_rule == 'group' ) {
	                                        echo $tour_price->wc_sale_group ?? $tour_price->wc_group;
                                        } else if ( $pricing_rule == 'person' ) {
	                                        if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
		                                        echo $tour_price->wc_sale_adult ?? $tour_price->wc_adult;
	                                        } else if ( ! $disable_child && ! empty( $tour_price->child ) ) {
		                                        echo $tour_price->wc_sale_child ?? $tour_price->wc_child;

	                                        }
                                        }
                                        ?>
                                        </span>
                                                </div>
												<?php
												if ( $related_comments ) {
													?>
                                                    <div class="tf-slider-rating-star">
                                                        <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo tf_total_avg_rating( $related_comments ); ?></span>
                                                    </div>
													<?php
												}
												?>

                                            </div>
                                        </div>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

			<?php }
			wp_reset_postdata();
			?>
		<?php } ?>
        <!-- Tours suggestion section End -->
		<?php do_action( 'tf_after_container' ); ?>
    </div>

<?php
endwhile;
?>
<?php
get_footer();
