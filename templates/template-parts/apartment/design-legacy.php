<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Apartment\Apartment;
?>

<div class="tf-single-template__legacy">
	<?php do_action( 'tf_before_container' ); ?>

    <div class="tf-title-area tf-apartment-title">
        <div class="tf-container">
            <div class="tf-title-wrap">
                <div class="tf-title-left">
                    <?php \Tourfic\App\Templates\Components\Global\Single\Title::render(); ?>
                    <div class="tf-title-left-bottom">
						<?php \Tourfic\App\Templates\Components\Global\Single\Address::render(); ?>
						<?php if ( $comments && ! $disable_review_sec == '1' ): ?>
                            <div class="tf-top-review">
                                <a href="#tf-review">
                                    <div class="tf-single-rating">
                                        <i class="fas fa-star"></i>
                                        <span><?php echo wp_kses_post( TF_Review::tf_total_avg_rating( $comments ) ); ?></span>
                                        (<?php TF_Review::tf_based_on_text( count( $comments ) ); ?>)
                                    </div>
                                </a>
                            </div>
						<?php endif; ?>
                    </div>
                </div>

                <div class="tf-title-right">
                    <?php \Tourfic\App\Templates\Components\Global\Single\Share::render(['share_style' => 'style3', 'design' => 'design-2']); ?>
                    <?php \Tourfic\App\Templates\Components\Global\Single\Wishlist::render(['icon_type' => 'simple']); ?>
                </div>
            </div>
        </div>
    </div>

    <?php \Tourfic\App\Templates\Components\Global\Single\Gallery::render(['gallery_style' => 'style3']); ?>

    <div class="content-feature-section">
        <div class="tf-container">
            <div class="tf-apartment-content-wrapper">
                <div class="tf-apartment-left">

					<?php if ( isset( $meta['highlights'] ) && ! empty( Helper::tf_data_types( $meta['highlights'] ) ) ) : ?>
                        <!-- Start highlights Section -->
                        <div class="tf-apt-highlights-wrapper">
							<?php if ( ! empty( $meta['highlights_title'] ) ): ?>
                                <h2 class="section-heading"><?php echo esc_html( $meta['highlights_title'] ) ?></h2>
							<?php endif; ?>

                            <div class="tf-apt-highlights <?php echo count( Helper::tf_data_types( $meta['highlights'] ) ) > 3 ? 'tf-apt-highlights-slider tf-slick-slider' : ''; ?>">
								<?php
								foreach ( Helper::tf_data_types( $meta['highlights'] ) as $highlight ) :
									if ( empty( $highlight['title'] ) ) {
										continue;
									}
									?>
                                    <div class="tf-apt-highlight">
                                        <div class="tf-apt-highlight-top">
											<?php echo ! empty( $highlight['icon'] ) ? "<div class='tf-apt-highlight-icon'><i class='" . esc_attr( $highlight['icon'] ) . "'></i></div>" : ''; ?>
                                            <h4><?php echo esc_html( $highlight['title'] ); ?></h4>
                                        </div>
										<?php echo ! empty( $highlight['subtitle'] ) ? '<p>' . esc_html( $highlight['subtitle'] ) . '</p>' : ''; ?>
                                    </div>
								<?php endforeach; ?>
                            </div>
                        </div>
					<?php endif; ?>

                    <div class="tf-mb-50">
                        <?php \Tourfic\App\Templates\Components\Global\Single\Description::render(['limit_content' => 'no']); ?>
                    </div>

					<?php if ( isset( $meta['rooms'] ) && ! empty( Helper::tf_data_types( $meta['rooms'] ) ) ) : ?>
                        <!-- Apartment Rooms -->
                        <div class="tf-apartment-rooms">
							<?php if ( ! empty( $meta['room_details_title'] ) ): ?>
                                <h2 class="section-heading"><?php echo esc_html( $meta['room_details_title'] ) ?></h2>
							<?php endif; ?>
                            <div class="tf-apartment-default-design-room-slider tf-slick-slider">
								<?php foreach ( Helper::tf_data_types( $meta['rooms'] ) as $key => $room ) : ?>
                                    <div class="tf-apartment-room-item">
                                        <div class="tf-apartment-room-item-thumb">
                                            <a href="#" class="tf-apt-room-qv" data-id="<?php echo esc_attr( $key ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                                                <img src="<?php echo ! empty( $room['thumbnail'] ) ? esc_url( $room['thumbnail'] ) : esc_url( TF_ASSETS_APP_URL . "images/feature-default.jpg" ) ?>" alt="room-thumbnail">
                                            </a>
                                        </div>
                                        <div class="tf-apartment-room-item-content">
                                            <?php if(!empty($room['title'])){ ?>
                                            <a href="#" class="tf-apt-room-qv" data-id="<?php echo esc_attr( $key ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                                                <h3><?php echo esc_html( $room['title'] ) ?></h3>
                                            </a>
                                            <?php } ?>
                                            <p class="tf-apartment-room-item-price">
												<?php echo ! empty( $room['price'] ) ? '<span>' . esc_html( $room['price'] ) . '</span>' : ''; ?>
												<?php echo ! empty( $room['price_label'] ) ? '<span>' . esc_html( $room['price_label'] ) . '</span>' : ''; ?>
                                            </p>
											<?php echo ! empty( $room['subtitle'] ) ? '<p>' . esc_html( $room['subtitle'] ) . '</p>' : ''; ?>
                                        </div>
                                    </div>
								<?php endforeach; ?>
                            </div>
                            <div id="tf_apt_room_details_qv" class=""></div>
                            <!-- Loader Image -->
                            <div id="tour_room_details_loader">
                                <div id="tour-room-details-loader-img">
                                    <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) ?>images/loader.gif" alt="">
                                </div>
                            </div>
                        </div>
					<?php endif; ?>

					<?php if ( isset( $meta['amenities'] ) && ! empty( Helper::tf_data_types( $meta['amenities'] ) ) ) :
						$fav_amenities = array();
						$other_amenities = array();
						foreach (Helper::tf_data_types($meta['amenities']) as $amenity) {
							if (!isset($amenity['favorite']) || $amenity['favorite'] !== '1') {
								$other_amenities[] = $amenity;
							} else {
								$fav_amenities[] = $amenity;
							}
						}
						$all_amenities = array_merge($fav_amenities, $other_amenities);
						?>
                        <!-- Start Key Features Section -->
                        <div class="tf-apartment-amenities-section">
                            <h2 class="section-heading"><?php echo ! empty( $meta['amenities_title'] ) ? esc_html( $meta['amenities_title'] ) : ''; ?></h2>
                            <div class="tf-apartment-amenities-inner">
                                <div class="tf-apartment-amenities">
									<?php if ( ! empty( $all_amenities ) ):
										foreach ( array_slice( $all_amenities, 0, 10 ) as $amenity ) :
											$feature =  isset( $amenity['feature'] ) ? get_term_by( 'id', $amenity['feature'], 'apartment_feature' ) : '';
											$feature_meta = isset( $amenity['feature'] ) ? get_term_meta( $amenity['feature'], 'tf_apartment_feature', true ) : '';
											$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
											if ( $f_icon_type == 'icon' ) {
												$feature_icon = '<i class="' . $feature_meta['apartment-feature-icon'] . '"></i>';
											} elseif ( $f_icon_type == 'custom' ) {
												$feature_icon = '<img src="' . esc_url( $feature_meta['apartment-feature-icon-custom'] ) . '" style="width: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px; height: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px;" />';
											}
											?>
                                            <div class="tf-apt-amenity">
												<?php echo ! empty( $feature_meta['apartment-feature-icon'] ) || !empty($feature_meta['apartment-feature-icon-custom']) ? "<div class='tf-apt-amenity-icon'>" . wp_kses_post( $feature_icon ) . "</div>" : ""; ?>
                                                <?php if(!empty($feature->name)){ ?>
                                                <span><?php echo esc_html( $feature->name ); ?></span>
                                                <?php } ?>
                                            </div>
										<?php endforeach; ?>
									<?php endif; ?>
                                </div>
								<?php if ( count( Helper::tf_data_types( $meta['amenities'] ) ) > 10 ): ?>
                                    <div class="tf-apartment-amenities-more">
                                        <a class="tf-modal-btn" data-target="#tf-amenities-modal">
											<?php esc_html_e( 'All Amenities', 'tourfic' ) ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M10.0252 4.22852L9.08457 5.17353L11.2647 7.34351L2.1947 7.35263L2.19604 8.68597L11.2412 8.67686L9.09779 10.8304L10.0428 11.771L13.8052 7.99092L10.0252 4.22852Z"
                                                      fill="#2A3343"/>
                                            </svg>
                                        </a>
                                    </div>

                                    <!-- Modal -->
                                    <div class="tf-modal" id="tf-amenities-modal">
                                        <div class="tf-modal-dialog">
                                            <div class="tf-modal-content">
                                                <div class="tf-modal-header">
                                                    <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                                                </div>
                                                <div class="tf-modal-body">
                                                    <h2 class="section-heading"><?php echo ! empty( $meta['amenities_title'] ) ? esc_html( $meta['amenities_title'] ) : ''; ?></h2>
													<?php
													$categories     = [];
													$amenities_cats = ! empty( Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) : '';
													foreach ( Helper::tf_data_types( $meta['amenities'] ) as $amenity ) {
														$cat     = $amenity['cat'];
														$feature = $amenity['feature'];

														// Check if the category exists in the $categories array
														if ( ! isset( $categories[ $cat ] ) ) {
															$categories[ $cat ] = [];
														}

														// Add the feature to the category
														$categories[ $cat ][] = $feature;
													}

													foreach ( $categories as $cat => $features ) :
														?>
                                                        <div class="tf-apartment-amenity-cat">
                                                            <h3><?php echo ! empty( $amenities_cats[ $cat ]['amenities_cat_name'] ) ? esc_html( $amenities_cats[ $cat ]['amenities_cat_name'] ) : ''; ?></h3>
                                                            <div class="tf-apartment-amenities">
																<?php foreach ( $features as $feature_id ):
																	$_feature = get_term_by( 'id', $feature_id, 'apartment_feature' );
																	$_feature_meta = get_term_meta( $feature_id, 'tf_apartment_feature', true );
																	$f_icon_type = ! empty( $_feature_meta['icon-type'] ) ? $_feature_meta['icon-type'] : '';
																	if ( $f_icon_type == 'icon' ) {
																		$feature_icon = '<i class="' . $_feature_meta['apartment-feature-icon'] . '"></i>';
																	} elseif ( $f_icon_type == 'custom' ) {
																		$feature_icon = '<img src="' . esc_url( $_feature_meta['apartment-feature-icon-custom'] ) . '" style="width: ' . $_feature_meta['apartment-feature-icon-dimension'] . 'px; height: ' . $_feature_meta['apartment-feature-icon-dimension'] . 'px;" />';
																	}
																	?>
                                                                    <div class="tf-apt-amenity">
																		<?php echo ! empty( $_feature_meta['apartment-feature-icon'] ) || !empty($_feature_meta['apartment-feature-icon-custom']) ? "<div class='tf-apt-amenity-icon'>" . wp_kses_post( $feature_icon ) . "</div>" : ""; ?>
                                                                        <span><?php echo esc_html( $_feature->name ); ?></span>
                                                                    </div>
																<?php endforeach; ?>
                                                            </div>
                                                        </div>
													<?php endforeach; ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
								<?php endif; ?>
                            </div>
                        </div>
					<?php endif; ?>
                </div>
                <!-- Host details -->
                <div class="tf-apartment-right">
                    <div class="apartment-booking-form">
						<?php Apartment::tf_apartment_single_booking_form( $comments, $disable_review_sec ); ?>
                    </div>
					<?php
					$post_author_id = get_post_field( 'post_author', get_the_ID() );
					$author_info    = get_userdata( $post_author_id );
					?>
                    <div class="host-details">
                        <div class="host-top">
                            <img src="<?php echo esc_url( get_avatar_url( $post_author_id ) ); ?>" alt="">
                            <div class="host-meta">
								<?php echo sprintf( '<h4>%s %s</h4>', esc_html__( 'Hosted by', 'tourfic' ), esc_html( $author_info->display_name ) ); ?>
								<?php echo sprintf( '<span class="tf-apartment-joined-text">%s <span>:</span> <span>%s</span></span>', esc_html__( 'Joined', 'tourfic' ), wp_kses_post( wp_date( 'F Y', strtotime( $author_info->user_registered ) ) ) ); ?>
								<?php Apartment::tf_apartment_host_rating( $post_author_id ) ?>

                            </div>
                        </div>
                        <div class="host-bottom">
                            <?php if(!empty( get_the_author_meta( 'description', $post_author_id ))) : ?>
                                <h5><?php echo esc_html__( "During Your Stay", 'tourfic' ); ?></h5>
                                <p class="host-desc">
                                    <?php echo wp_kses_post( get_the_author_meta( 'description', $post_author_id ) ); ?>
                                </p>
                            <?php endif; ?>

                            <ul>
								<?php
								if ( ! empty( get_the_author_meta( 'language', $post_author_id ) ) ) {
									echo sprintf( '<li>%s <span>%s</span></li>', esc_html__( 'Language: ', 'tourfic' ), wp_kses_post( get_the_author_meta( 'language', $post_author_id ) ) );
								}
								?>
                            </ul>
                            <a href="javaScript:void(0);" data-target="#tf-ask-modal" class="tf-modal-btn tf_btn tf_btn_white tf_btn_full"><i class="fas fa-phone"></i><?php esc_html_e( 'Contact Host', 'tourfic' ) ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<?php if ( ! empty( $map['address'] ) || isset( $meta['surroundings_places'] ) && ! empty( Helper::tf_data_types( $meta['surroundings_places'] ) ) ): ?>
        <div id="apartment-map" class="tf-apartment-map-wrapper">
            <div class="tf-container">
                <div class="tf-row">
                    <div class="tf-map-content-wrapper <?php echo empty( $map['address'] ) || empty( $meta['surroundings_places'] ) ? 'tf-map-content-full' : ''; ?> <?php echo ! function_exists( 'is_tf_pro' ) ? 'tf-map-content-full' : '' ?>">
						<?php \Tourfic\App\Templates\Components\Global\Single\Map::render(['design' => 'design-2'], '', '600px'); ?>

						<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && isset( $meta['surroundings_places'] ) && ! empty( Helper::tf_data_types( $meta['surroundings_places'] ) ) ): ?>
                            <div class="about-location">
								<?php if ( ! empty( $meta['surroundings_sec_title'] ) ): ?>
                                    <h3 class="surroundings_sec_title"><?php echo esc_html( $meta['surroundings_sec_title'] ); ?></h3>
								<?php endif; ?>
								<?php if ( ! empty( $meta['surroundings_subtitle'] ) ): ?>
                                    <p class="surroundings_subtitle"><?php echo esc_html( $meta['surroundings_subtitle'] ); ?></p>
								<?php endif; ?>

                                <div class="tf-apartment-surronding-wrapper">
									<?php foreach ( Helper::tf_data_types( $meta['surroundings_places'] ) as $surroundings_place ) : ?>
                                        <div class="tf-apartment-surronding-criteria">
                                            <div class="tf-apartment-surronding-criteria-label">
												<?php if ( ! empty( $surroundings_place['place_criteria_icon'] ) ) { ?>
                                                    <i class="<?php echo esc_attr( $surroundings_place['place_criteria_icon'] ); ?>"></i>
												<?php } ?>
												<?php echo esc_html( $surroundings_place['place_criteria_label'] ); ?>
                                            </div>

											<?php if ( isset( $surroundings_place['places'] ) && ! empty( Helper::tf_data_types( $surroundings_place['places'] ) ) ): ?>
                                                <ul class="tf-apartment-surronding-places">
													<?php foreach ( Helper::tf_data_types( $surroundings_place['places'] ) as $place ): ?>
                                                        <li>
                                                            <span class="tf-place-name"><?php echo esc_html( $place['place_name'] ) ?></span>
                                                            <span class="tf-place-distance"><?php echo esc_html( $place['place_distance'] ) ?></span>
                                                        </li>
													<?php endforeach; ?>
                                                </ul>
											<?php endif; ?>
                                        </div>
									<?php endforeach; ?>
                                </div>
                            </div>
						<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
	<?php endif; ?>

	<?php if ( $disable_review_sec !== '1' ) : ?>
        <div id="tf-review" class="review-section tf-apartment-review">
            <div class="tf-container">
                <div class="reviews">
                    <h2 class="section-heading"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
					<?php comments_template(); ?>
                </div>
            </div>
        </div>
	<?php endif; ?>

	<?php if ( isset( $meta['house_rules'] ) && ! empty( Helper::tf_data_types( $meta['house_rules'] ) ) ):
		$included_house_rules = array();
		$not_included_house_rules = array();
		foreach ( Helper::tf_data_types( $meta['house_rules'] ) as $house_rule ) {
			if ( isset( $house_rule['include'] ) && $house_rule['include'] == '1' ) {
				$included_house_rules[] = $house_rule;
			} else {
				$not_included_house_rules[] = $house_rule;
			}
		}
		?>
        <div class="tf-house-rules">
            <div class="tf-container">
                <h2 class="section-heading"><?php echo ! empty( $meta['house_rules_title'] ) ? esc_html( $meta['house_rules_title'] ) : ''; ?></h2>
                <div class="tf-house-rules-wrapper <?php echo empty( $included_house_rules ) || empty( $not_included_house_rules ) ? 'tf-house-rules-full' : ''; ?>">
					<?php if ( ! empty( $included_house_rules ) ): ?>
                        <ul class="tf-included-house-rules">
							<?php
							foreach ( $included_house_rules as $item ) {
								echo sprintf( '<li><h6>%s</h6> <span>%s</span></li>', wp_kses_post( $item['title'] ), wp_kses_post( $item['desc'] ) );
							}
							?>
                        </ul>
					<?php endif; ?>

					<?php if ( ! empty( $not_included_house_rules ) ): ?>
                        <ul class="tf-not-included-house-rules">
							<?php
							foreach ( $not_included_house_rules as $item ) {
								echo sprintf( '<li><h6>%s</h6> <span>%s</span></li>', wp_kses_post( $item['title'] ), wp_kses_post( $item['desc'] ) );
							}
							?>
                        </ul>
					<?php endif; ?>
                </div>
            </div>
        </div>
	<?php endif; ?>

	<?php if ( isset( $meta['faq'] ) && ! empty( Helper::tf_data_types( $meta['faq'] ) ) ): ?>
        <!-- FAQ section Start -->
        <div class="tf-faq-wrapper tf-apartment-faq">
            <div class="tf-container">
                <div class="tf-faq-sec-title">
					<?php echo ! empty( $meta['faq_title'] ) ? '<h2 class="section-heading">' . esc_html( $meta['faq_title'] ) . '</h2>' : ''; ?>
					<?php echo ! empty( $meta['faq_desc'] ) ? '<p>' . wp_kses_post( $meta['faq_desc'] ) . '</p>' : ''; ?>
                </div>

                <div class="tf-faq-content-wrapper">
                    <div class="tf-faq-items-wrapper">
						<?php foreach ( Helper::tf_data_types( $meta['faq'] ) as $key => $faq ): ?>
                            <div id="tf-faq-item">
                                <div class="tf-faq-title <?php echo $key === 0 ? esc_attr( 'active' ) : ''; ?>">
                                    <svg class="tf-faq-minus" xmlns="http://www.w3.org/2000/svg" width="19" height="1" viewBox="0 0 19 1" fill="none">
                                        <rect width="19" height="1" fill="#2979FF"/>
                                    </svg>
                                    <svg class="tf-faq-plus" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                        <rect y="9" width="19" height="1" fill="#2979FF"/>
                                        <rect x="9" width="1" height="19" fill="#2979FF"/>
                                    </svg>
                                    <h4><?php echo esc_html( $faq['title'] ); ?></h4>
                                </div>
                                <div class="tf-faq-desc" <?php echo $key === 0 ? 'style="display: block;"' : ''; ?>>
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

	<?php
	$enquiry_section        = ! empty( $meta['enquiry-section'] ) ? $meta['enquiry-section'] : '';
	$enquiry_section_icon   = ! empty( $meta['apartment-enquiry-icon'] ) ? $meta['apartment-enquiry-icon'] : '';
	$enquiry_section_title  = ! empty( $meta['enquiry-title'] ) ? $meta['enquiry-title'] : '';
	$enquiry_section_des    = ! empty( $meta['enquiry-content'] ) ? $meta['enquiry-content'] : '';
	$enquiry_section_button = ! empty( $meta['enquiry-btn'] ) ? $meta['enquiry-btn'] : '';

	if ( $enquiry_section === '1' && ( ! empty( $enquiry_section_icon ) || ! empty( $enquiry_section_title ) || ! empty( $enquiry_section_button ) ) ):
		?>
        <div class="tf-ask-question apartment-question">
            <div class="tf-container">
                <div class="apartment-qa-wrapper">
                    <div class="tf-question-left">
                        <?php if ( ! empty( $enquiry_section_icon ) ) : ?>
                            <div class="tf-apartment-question-icon">
                                <i class="<?php echo esc_attr( $enquiry_section_icon ); ?>" aria-hidden="true"></i>
                            </div>
                        <?php endif; ?>
                        <div class="tf-question-left-inner">
                            <div class="default-enquiry-title-section">
                                <?php if ( ! empty( $enquiry_section_title ) ) {?>
                                    <h2><?php echo esc_html( $enquiry_section_title ) ?></h2>
                                <?php } ?>
                            </div>
                            <?php if ( ! empty( $enquiry_section_des ) ) {?>
                                <p><?php echo wp_kses_post( $enquiry_section_des ); ?></p>
                            <?php } ?>
                        </div>
                    </div>
					<?php if ( ! empty( $enquiry_section_button ) ) {?>
                        <div class="tf-btn-wrap">
                            <a href="#" data-target="#tf-ask-modal" class="tf-modal-btn tf_btn tf_btn_large">
                                <span><?php echo wp_kses_post( $enquiry_section_button ) ?></span>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
	<?php endif; ?>

	<?php if ( ! empty( $meta['terms_and_conditions'] ) ) : ?>
        <div class="toc-section apartment-toc">
            <div class="tf-container">
                <div class="tf-toc-wrap">
					<?php echo ! empty( $meta['terms_title'] ) ? '<h2 class="section-heading">' . esc_html( $meta['terms_title'] ) . '</h2>' : ''; ?>
                    <div class="tf-toc-inner">
						<?php echo wp_kses_post( $meta['terms_and_conditions'] ); ?>
                    </div>
                </div>
            </div>
        </div>
	<?php endif; ?>



	<?php
	$args              = array(
		'post_type'      => 'tf_apartment',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'tax_query'      => array( // WPCS: slow query ok.
			array(
				'taxonomy' => 'apartment_location',
				'field'    => 'term_id',
				'terms'    => wp_list_pluck( $locations, 'term_id' ),
			),
		),
	);
    $related_args = array_merge( $args, array( 'post__not_in' => array( $post_id ) ) );
	$related_apartment = new WP_Query( $args );
	$related_apartment_check = new WP_Query( $related_args );

	if ( $disable_related_sec !== '1' && $related_apartment_check->have_posts() ) : ?>
        <div class="tf-related-apartment">
            <div class="tf-container">
                <h2 class="section-heading"><?php echo ! empty( $meta['related_apartment_title'] ) ? esc_html( $meta['related_apartment_title'] ) : ''; ?></h2>
                <div class="tf-related-apartment-slider tf-slick-slider">
					<?php while ( $related_apartment->have_posts() ) : $related_apartment->the_post();
						if ( ! in_array( get_the_ID(), array( $post_id ) ) ):
							?>
                            <div class="tf-apartment-item">
                                <div class="tf-apartment-item-thumb">
									<?php if ( has_post_thumbnail() ) { ?>
                                        <a href="<?php the_permalink(); ?>">
											<?php the_post_thumbnail( 'tourfic-370x250' ); ?>
                                        </a>
									<?php } else { ?>
                                        <a href="<?php the_permalink(); ?>">
                                            <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg"; ?>"/>
                                        </a>
									<?php } ?>
                                </div>
                                <div class="tf-related-apartment-content">
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <span><?php echo get_the_date( 'F j, Y' ); ?></span>
                                </div>
                            </div>
						<?php
						endif;
					endwhile;
					wp_reset_query(); ?>
                </div>
            </div>
        </div>
	<?php endif; ?>

	<?php do_action( 'tf_after_container' ); ?>
</div>