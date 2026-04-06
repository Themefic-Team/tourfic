<?php

namespace Tourfic\App\Templates\Components\Global\Single;

use Tourfic\Classes\Helper;
use Tourfic\App\TF_Review;
use Tourfic\Classes\Car_Rental\Pricing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Gallery Component
 * Shared markup for Elementor and Bricks Gallery widgets
 */
class Gallery {

	/**
	 * Static render method for Gallery component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = 'elementor' ) {
		$post_id = get_the_ID();
		$post_type = get_post_type();
		$style = ! empty( $settings['gallery_style'] ) ? $settings['gallery_style'] : 'style1';
		$show_review = Helper::get_switcher_value( $settings, 'show_review', 'yes', $builder );

		// Query reviews/comments
		$args           = [
			'post_id' => $post_id,
			'status'  => 'approve',
			'type'    => 'comment',
		];
		$comments_query = new \WP_Comment_Query( $args );
		$comments       = $comments_query->comments;

		// Get gallery data based on post type
		$gallery = $disable_review_sec = $s_review = '';
		$gallery_ids = [];

		if ( 'tf_hotel' === $post_type ) {
			$meta = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$s_review = ! empty( Helper::tfopt( 'h-review' ) ) ? Helper::tfopt( 'h-review' ) : 0;
			$disable_review_sec = ! empty( $meta['h-review'] ) ? $meta['h-review'] : '';
			$gallery = ! empty( $meta['gallery'] ) ? $meta['gallery'] : '';
		} elseif ( 'tf_tours' === $post_type ) {
			$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
			$s_review = ! empty( Helper::tfopt( 't-review' ) ) ? Helper::tfopt( 't-review' ) : '';
			$disable_review_sec = ! empty( $meta['t-review'] ) ? $meta['t-review'] : '';
			$gallery = ! empty( $meta['tour_gallery'] ) ? $meta['tour_gallery'] : '';
		} elseif ( 'tf_apartment' === $post_type ) {
			$meta = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$s_review = ! empty( Helper::tfopt( 'disable-apartment-review' ) ) ? Helper::tfopt( 'disable-apartment-review' ) : 0;
			$disable_review_sec = ! empty( $meta['disable-apartment-review'] ) ? $meta['disable-apartment-review'] : '';
			$gallery = ! empty( $meta['apartment_gallery'] ) ? $meta['apartment_gallery'] : '';
		} elseif ( 'tf_carrental' === $post_type ) {
			$meta = get_post_meta( $post_id, 'tf_carrental_opt', true );
			$s_review = ! empty( Helper::tfopt( 'disable-apartment-review' ) ) ? Helper::tfopt( 'disable-apartment-review' ) : 0;
			$disable_review_sec = ! empty( $meta['disable-apartment-review'] ) ? $meta['disable-apartment-review'] : '';
			$gallery = ! empty( $meta['car_gallery'] ) ? $meta['car_gallery'] : '';
		} else {
			return;
		}

		if ( $gallery ) {
			$gallery_ids = explode( ',', $gallery );
		}

		$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;

		// Style 1: Bottom Nav
		if ( 'style1' === $style && 'tf_carrental' !== $post_type ) {
			self::render_style_1( $post_id, $post_type, $show_review, $disable_review_sec, $comments, $gallery_ids );
		} elseif ( 'style1' === $style && 'tf_carrental' === $post_type ) {
			self::render_style_1_carrental( $post_id, $post_type, $show_review, $disable_review_sec, $comments, $gallery_ids );
		} elseif ( 'style2' === $style ) {
			self::render_style_2( $post_id, $post_type, $show_review, $disable_review_sec, $comments, $gallery_ids );
		} elseif ( 'style3' === $style ) {
			self::render_style_3( $post_id, $post_type, $gallery_ids );
		}
	}

	/**
	 * Render Style 1 (Bottom Nav)
	 */
	private static function render_style_1( $post_id, $post_type, $show_review, $disable_review_sec, $comments, $gallery_ids ) {
		?>
		<div class="tf-single-gallery__style-1 tf-hero-gallery">
			<div class="tf-gallery-featured <?php echo empty( $gallery_ids ) ? esc_attr( 'tf-without-gallery-featured' ) : ''; ?>">
				<img src="<?php echo ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ? esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) : esc_url( TF_ASSETS_APP_URL . 'images/feature-default.jpg' ); ?>" alt="<?php echo get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ); ?>">

				<?php if ( $show_review && '1' !== $disable_review_sec ) : ?>
					<div class="tf-single-review-box">
						<?php if ( $comments ) : ?>
							<a href="#tf-review" class="tf-single-rating">
								<span><?php echo wp_kses_post( TF_Review::tf_total_avg_rating( $comments ) ); ?></span> (<?php TF_Review::tf_based_on_text( count( $comments ) ); ?>)
							</a>
						<?php else : ?>
							<a href="#tf-review" class="tf-single-rating">
								<span><?php esc_html_e( '0.0', 'tourfic' ); ?></span> (<?php esc_html_e( '0 review', 'tourfic' ); ?>)
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="tf-gallery">
				<?php
				$gallery_count = 1;
				if ( ! empty( $gallery_ids ) ) :
					foreach ( $gallery_ids as $gallery_item_id ) :
						$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
						?>
						<a class="<?php echo 5 === $gallery_count ? esc_attr( 'tf-gallery-more' ) : ''; ?>" id="tour-gallery" href="<?php echo esc_url( $image_url ); ?>" data-fancybox="tour-gallery">
							<img src="<?php echo esc_url( $image_url ); ?>">
						</a>
						<?php
						$gallery_count++;
					endforeach;
				endif;
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Style 1 for Car Rental
	 */
	private static function render_style_1_carrental( $post_id, $post_type, $show_review, $disable_review_sec, $comments, $gallery_ids ) {
		?>
		<div class="tf-single-template__one tf-single-car-gallery-style-1">
			<div class="tf-car-hero-gallery">
				<div class="tf-featured-car">
					<img src="<?php echo ! empty( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) ? esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) : esc_url( TF_ASSETS_APP_URL . 'images/feature-default.jpg' ); ?>" alt="<?php esc_attr_e( 'Car Image', 'tourfic' ); ?>">

					<div class="tf-featured-reviews">
						<a href="#tf-reviews" class="tf-single-rating">
							<span>
								<?php
								if ( $comments ) {
									echo wp_kses_post( TF_Review::tf_total_avg_rating( $comments ) );
								} else {
									?>
									0.0
									<?php
								}
								?>
								<i class="fa-solid fa-star"></i>
							</span> (<?php echo wp_kses_post( Pricing::get_total_trips( $post_id ) ); ?> <?php esc_html_e( 'trips', 'tourfic' ); ?>)
						</a>
					</div>
				</div>

				<div class="tf-gallery tf-flex tf-flex-gap-16">
					<?php
					$gallery_count = 1;
					if ( ! empty( $gallery_ids ) ) {
						foreach ( $gallery_ids as $gallery_item_id ) {
							$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
							?>
							<a class="<?php echo 4 === $gallery_count ? esc_attr( 'tf-gallery-more' ) : ''; ?>" href="<?php echo esc_url( $image_url ); ?>" id="tour-gallery" data-fancybox="tour-gallery">
								<img src="<?php echo esc_url( $image_url ); ?>">
							</a>
							<?php
							$gallery_count++;
						}
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Style 2 (Slider)
	 */
	private static function render_style_2( $post_id, $post_type, $show_review, $disable_review_sec, $comments, $gallery_ids ) {
		?>
		<div class="tf-single-gallery__style-2 tf-hero-gallery">
			<?php if ( $show_review && $comments && '1' !== $disable_review_sec ) { ?>
				<div class="tf-top-review">
					<a href="#tf-review">
						<div class="tf-single-rating">
							<i class="fas fa-star"></i> <span><?php echo wp_kses_post( TF_Review::tf_total_avg_rating( $comments ) ); ?></span> (<?php TF_Review::tf_based_on_text( count( $comments ) ); ?>)
						</div>
					</a>
				</div>
			<?php } ?>

			<?php if ( ! empty( $gallery_ids ) ) { ?>
				<div class="tf-gallery-wrap">
					<div class="list-single-main-media fl-wrap" id="sec1">
						<div class="single-slider-wrapper fl-wrap">
							<div class="tf_slider-for fl-wrap tf-slick-slider">
								<?php
								foreach ( $gallery_ids as $attachment_id ) {
									echo '<div class="slick-slide-item">';
									echo '<a href="' . esc_url( wp_get_attachment_url( $attachment_id, 'tf_gallery_thumb' ) ) . '" class="slick-slide-item-link" data-fancybox="hotel-gallery">';
									echo wp_get_attachment_image( $attachment_id, 'tf_gallery_thumb' );
									echo '</a>';
									echo '</div>';
								}
								?>
							</div>
							<div class="swiper-button-prev sw-btn"></div>
							<div class="swiper-button-next sw-btn"></div>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<div class="tf-gallery-wrap">
					<div class="list-single-main-media fl-wrap" id="sec1">
						<div class="single-slider-wrapper fl-wrap">
							<div class="tf_slider-for fl-wrap tf-slick-slider">
								<a href="<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'tf_gallery_thumb' ) ) ? esc_url( get_the_post_thumbnail_url( $post_id, 'tf_gallery_thumb' ) ) : esc_url( TF_ASSETS_APP_URL . 'images/feature-default.jpg' ); ?>" class="slick-slide-item-link" data-fancybox="hotel-gallery">
									<img src="<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'tf_gallery_thumb' ) ) ? esc_url( get_the_post_thumbnail_url( $post_id, 'tf_gallery_thumb' ) ) : esc_url( TF_ASSETS_APP_URL . 'images/feature-default.jpg' ); ?>" alt="">
								</a>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render Style 3 (Grid)
	 */
	private static function render_style_3( $post_id, $post_type, $gallery_ids ) {
		?>
		<div class="tf-apt-hero-section tf-single-gallery__style-3">
			<div class="tf-apt-hero-wrapper">
				<?php if ( ! empty( $gallery_ids ) ) :
					$first_image = ! empty( $gallery_ids[0] ) ? wp_get_attachment_image( $gallery_ids[0], 'tf_apartment_gallery_large' ) : '';
					$second_image = ! empty( $gallery_ids[1] ) ? wp_get_attachment_image( $gallery_ids[1], 'tf_apartment_gallery_small' ) : '';
					$third_image = ! empty( $gallery_ids[2] ) ? wp_get_attachment_image( $gallery_ids[2], 'tf_apartment_gallery_small' ) : '';
					?>
					<div class="tf-apt-hero-gallery">
						<div class="tf-apt-hero-left <?php echo ( ! empty( $second_image ) || ! empty( $third_image ) ) ? 'has-right' : ''; ?>">
							<a href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[0], 'full' ) ); ?>" data-fancybox="hotel-gallery" class="hero-first-image">
								<?php echo wp_kses_post( $first_image ); ?>
							</a>
						</div>
						<?php if ( $second_image || $third_image ) : ?>
							<div class="tf-apt-hero-right">
								<?php if ( ! empty( $second_image ) ) : ?>
									<a href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[1], 'full' ) ); ?>" data-fancybox="hotel-gallery" class="hero-second-image">
										<?php echo wp_kses_post( $second_image ); ?>
									</a>
								<?php endif; ?>
								<?php if ( ! empty( $third_image ) ) : ?>
									<a href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[2], 'full' ) ); ?>" data-fancybox="hotel-gallery" class="hero-third-image <?php echo count( $gallery_ids ) > 3 ? 'has-more' : ''; ?>">
										<?php echo wp_kses_post( $third_image ); ?>
									</a>
								<?php endif; ?>
							</div>
							<?php if ( count( $gallery_ids ) > 3 ) : ?>
								<div class="gallery-all-photos">
									<a href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[3], 'full' ) ); ?>" class="tf_btn" data-fancybox="hotel-gallery">
										<?php esc_html_e( 'All Photos', 'tourfic' ); ?>
									</a>
									<?php
									foreach ( $gallery_ids as $key => $item ) :
										if ( $key < 4 ) {
											continue;
										}
										?>
										<a href="<?php echo esc_url( wp_get_attachment_image_url( $item, 'full' ) ); ?>" data-fancybox="hotel-gallery"></a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'tf_apartment_single_thumb' );
					} else {
						echo '<img src="' . esc_url( TF_ASSETS_APP_URL . 'images/feature-default.jpg' ) . '" alt="feature-default">';
					}
					?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
