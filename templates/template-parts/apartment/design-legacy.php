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

					<?php 
                        \Tourfic\App\Templates\Components\Global\Single\Highlights::render([
                            'highlights_style' => 'style2'
                        ]);

                        \Tourfic\App\Templates\Components\Global\Single\Description::render([
                            'show_title' => 'yes',
                            'limit_content' => 'no',
                            'wrapper_open' => '<div class="tf-mb-50">',
                            'wrapper_close' => '</div>'
                        ]); 
                    ?>

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

                    <?php \Tourfic\App\Templates\Components\Global\Single\Amenities::render(['amenities_style' => 'style2']); ?>
                </div>
                <!-- Host details -->
                <div class="tf-apartment-right">
                    <div class="apartment-booking-form">
						<?php Apartment::tf_apartment_single_booking_form( $comments, $disable_review_sec ); ?>
                    </div>

					<?php \Tourfic\App\Templates\Components\Global\Single\Host_Info::render(); ?>
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

						<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ){
                            \Tourfic\App\Templates\Components\Global\Single\Nearby_Places::render([
                                'nearby_places_style' => 'style2',
                            ]);
                        } ?>
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

	<?php 
    \Tourfic\App\Templates\Components\Global\Single\House_Rules::render([
        'house_rules_style' => 'style2', 
        'container' => 'yes',
    ]); 
    ?>

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
    \Tourfic\App\Templates\Components\Global\Single\Enquiry::render([
        'icon_type' => 'simple',
        'enquiry_style' => 'style2',
        'container' => 'yes',
    ]);
	?>

    <?php
    \Tourfic\App\Templates\Components\Global\Single\Terms_And_Conditions::render([
        'wrapper_open' => '<div class="toc-section apartment-toc"><div class="tf-container">',
        'wrapper_close' => '</div></div>',
    ]);
    ?>

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