<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Tour\Tour_Price;
use Tourfic\Classes\Apartment\Pricing as Apt_Pricing;
use Tourfic\App\TF_Review;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Related Post Component
 */
class Related_Post {

	public static function render( $settings = [], $builder = '' ) {
		$post_id       = get_the_ID();
		$post_type     = get_post_type();
		$wrapper_open  = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';
		$style         = ! empty( $settings['related_post_style'] ) ? $settings['related_post_style'] : 'style1';

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'tf_tours' === $post_type ) {
			$meta                 = get_post_meta( $post_id, 'tf_tours_opt', true );
			$disable_related_tour = ! empty( $meta['t-related'] ) ? $meta['t-related'] : '';
			$s_related            = ! empty( Helper::tfopt( 't-related' ) ) ? Helper::tfopt( 't-related' ) : '';
			$disable_related_tour = ! empty( $disable_related_tour ) ? $disable_related_tour : $s_related;

			$destinations           = get_the_terms( $post_id, 'tour_destination' );
			$first_destination_slug = ! empty( $destinations ) && ! is_wp_error( $destinations ) ? $destinations[0]->slug : '';

			if ( 'style1' === $style && ! $disable_related_tour == '1' ) {
				self::render_tour_style_1( $settings, $first_destination_slug );
			} elseif ( 'style2' === $style && ! $disable_related_tour == '1' ) {
				self::render_tour_style_2( $settings, $first_destination_slug );
			} elseif ( 'style3' === $style && ! $disable_related_tour == '1' ) {
				self::render_tour_style_3( $settings, $first_destination_slug );
			}
		} elseif ( 'tf_apartment' === $post_type ) {
			$meta                = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$disable_related_sec = ! empty( $meta['disable-related-apartment'] ) ? $meta['disable-related-apartment'] : '';
			$s_related           = ! empty( Helper::tfopt( 'disable-related-apartment' ) ) ? Helper::tfopt( 'disable-related-apartment' ) : 0;
			$disable_related_sec = ! empty( $disable_related_sec ) ? $disable_related_sec : $s_related;
			$locations           = ! empty( get_the_terms( $post_id, 'apartment_location' ) ) ? get_the_terms( $post_id, 'apartment_location' ) : [];

			if ( 'style1' === $style && $disable_related_sec !== '1' ) {
				self::render_apartment_style_1( $settings, $locations );
			} elseif ( 'style2' === $style && $disable_related_sec !== '1' ) {
				self::render_apartment_style_2( $settings, $locations );
			}
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';

		if ( 'elementor' === $builder && class_exists( '\\Elementor\\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			?>
			<script>
				jQuery(document).ready(function ($) {
					'use strict';

					jQuery('.tf-design-2-slider-items-wrapper, .tf-design-3-slider-items-wrapper').slick({
						dots: false,
						arrows: true,
						infinite: true,
						speed: 300,
						autoplaySpeed: 2000,
						slidesToShow: 3,
						slidesToScroll: 1,
						responsive: [
							{
								breakpoint: 1024,
								settings: {
									slidesToShow: 2,
									slidesToScroll: 1,
									infinite: true,
									dots: false
								}
							},
							{
								breakpoint: 600,
								settings: {
									slidesToShow: 1,
									slidesToScroll: 1
								}
							},
							{
								breakpoint: 480,
								settings: {
									slidesToShow: 1,
									slidesToScroll: 1
								}
							}
						]
					});

					jQuery('.tf-slider-items-wrapper,.tf-slider-activated').slick({
						dots: true,
						arrows: false,
						infinite: true,
						speed: 300,
						autoplaySpeed: 2000,
						slidesToShow: 3,
						slidesToScroll: 1,
						responsive: [
							{
								breakpoint: 1024,
								settings: {
									slidesToShow: 3,
									slidesToScroll: 1,
									infinite: true,
									dots: true
								}
							},
							{
								breakpoint: 767,
								settings: {
									slidesToShow: 2,
									slidesToScroll: 1
								}
							},
							{
								breakpoint: 480,
								settings: {
									slidesToShow: 1,
									slidesToScroll: 1
								}
							}
						]
					});

					jQuery('.tf-related-apartment-slider').slick({
						dots: true,
						arrows: false,
						infinite: true,
						speed: 300,
						autoplay: true,
						autoplaySpeed: 3000,
						slidesToShow: 4,
						slidesToScroll: 1,
						responsive: [
							{
								breakpoint: 1024,
								settings: {
									slidesToShow: 3,
									slidesToScroll: 1,
									infinite: true,
									dots: true
								}
							},
							{
								breakpoint: 600,
								settings: {
									slidesToShow: 2,
									slidesToScroll: 1
								}
							},
							{
								breakpoint: 480,
								settings: {
									slidesToShow: 1,
									slidesToScroll: 1
								}
							}
						]
					});
				});
			</script>
			<?php
		}
	}

	private static function get_tour_query_args( $post_id, $first_destination_slug ) {
		$related_tour_type = Helper::tfopt( 'rt_display' );
		$args              = [
			'post_type'      => 'tf_tours',
			'post_status'    => 'publish',
			'posts_per_page' => 8,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post__not_in'   => [ $post_id ],
			'tax_query'      => [
				[
					'taxonomy' => 'tour_destination',
					'field'    => 'slug',
					'terms'    => $first_destination_slug,
				],
			],
		];

		$selected_ids    = ! empty( Helper::tfopt( 'tf-related-tours' ) ) ? Helper::tfopt( 'tf-related-tours' ) : [];
		$current_post_id = [ $post_id ];

		if ( 'selected' === $related_tour_type ) {
			if ( in_array( $post_id, $selected_ids, true ) ) {
				$index           = array_search( $post_id, $selected_ids, true );
				$current_post_id = [ $selected_ids[ $index ] ];
				unset( $selected_ids[ $index ] );
			}

			if ( count( $selected_ids ) > 0 ) {
				$args['post__in'] = $selected_ids;
			} else {
				$args['post__in'] = [ -1 ];
			}
		}

		return [ $args, $current_post_id ];
	}

	private static function render_tour_style_1( $settings, $first_destination_slug ) {
        $post_id       = get_the_ID();
        $container = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
        $wrapper = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
		list( $args, $current_post_id ) = self::get_tour_query_args( $post_id, $first_destination_slug );
		$tours = new \WP_Query( $args );
		$all_tour_ids = array_filter( wp_list_pluck( $tours->posts, 'ID' ), function( $id ) use ( $current_post_id ) {
			return $id != $current_post_id[0];
		} );
		?>
		<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__one tf-single-tour-related-post-style1">' : ''; ?>
			<?php if ( $tours->have_posts() ) : ?>
				<div class="upcomming-tours">
                    <?php echo 'yes' === $container ? '<div class="tf-container"><div class="tf-container-inner">' : ''; ?>
					<div class="section-title">
						<h2 class="tf-title"><?php echo ! empty( Helper::tfopt( 'rt-title' ) ) ? esc_html( Helper::tfopt( 'rt-title' ) ) : ''; ?></h2>
						<?php if ( ! empty( Helper::tfopt( 'rt-description' ) ) ) : ?>
							<p><?php echo wp_kses_post( Helper::tfopt( 'rt-description' ) ); ?></p>
						<?php endif; ?>
					</div>
					<div class="tf-slider-items-wrapper tf-slick-slider tf-upcomming-tours-list-outter tf-mt-40 tf-flex tf-flex-gap-24">
						<?php while ( $tours->have_posts() ) : $tours->the_post(); ?>
							<?php if ( is_array( $all_tour_ids ) && in_array( get_the_ID(), $all_tour_ids, true ) ) : ?>
								<?php self::render_tour_card_style_1( get_the_ID() ); ?>
							<?php endif; ?>
						<?php endwhile; ?>
					</div>
                    <?php echo 'yes' === $container ? '</div></div>' : ''; ?>
				</div>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
		<?php
	}

	private static function render_tour_style_2( $settings, $first_destination_slug ) {
        $post_id       = get_the_ID();
        $container = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
        $wrapper = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
		list( $args, $current_post_id ) = self::get_tour_query_args( $post_id, $first_destination_slug );
		$tours = new \WP_Query( $args );
		$all_tour_ids = array_filter( wp_list_pluck( $tours->posts, 'ID' ), function( $id ) use ( $current_post_id ) {
			return $id != $current_post_id[0];
		} );
		?>
		<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__two tf-single-tour-related-post-style2">' : ''; ?>
			<?php if ( $tours->have_posts() ) : ?>
				<div class="tf-related-items-section">
                    <?php echo 'yes' === $container ? '<div class="tf-container"><div class="tf-container-inner">' : ''; ?>
					<div class="section-title">
						<h2 class="tf-title"><?php echo ! empty( Helper::tfopt( 'rt-title' ) ) ? esc_html( Helper::tfopt( 'rt-title' ) ) : esc_html__( 'You may also like', 'tourfic' ); ?></h2>
					</div>
					<div class="tf-design-2-slider-items-wrapper tf-slick-slider tf-upcomming-tours-list-outter tf-flex tf-flex-gap-24">
						<?php while ( $tours->have_posts() ) : $tours->the_post(); ?>
							<?php if ( is_array( $all_tour_ids ) && in_array( get_the_ID(), $all_tour_ids, true ) ) : ?>
								<?php self::render_tour_card_style_2( get_the_ID() ); ?>
							<?php endif; ?>
						<?php endwhile; ?>
					</div>
                    <?php echo 'yes' === $container ? '</div></div>' : ''; ?>
				</div>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
		<?php
	}

	private static function render_tour_style_3( $settings, $first_destination_slug ) {
        $post_id       = get_the_ID();
        $container = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
        $wrapper = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
        $wrapper_class = !empty($settings['wrapper_class']) ? $settings['wrapper_class'] : '';
		list( $args, $current_post_id ) = self::get_tour_query_args( $post_id, $first_destination_slug );
		$tours = new \WP_Query( $args );
		$all_tour_ids = array_filter( wp_list_pluck( $tours->posts, 'ID' ), function( $id ) use ( $current_post_id ) {
			return $id != $current_post_id[0];
		} );
		?>
		<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__legacy tf-single-tour-related-post-style3">' : ''; ?>
			<?php if ( $tours->have_posts() ) : ?>
				<div class="tf-suggestion-wrapper <?php echo esc_attr( $wrapper_class ); ?>">
                    <?php echo 'yes' === $container ? '<div class="tf-container">' : ''; ?>
					<div class="tf-slider-content-wrapper">
						<div class="tf-suggestion-sec-head">
							<?php if ( ! empty( Helper::tfopt( 'rt-title' ) ) ) : ?>
								<h2 class="section-heading"><?php echo esc_html( Helper::tfopt( 'rt-title' ) ); ?></h2>
							<?php endif; ?>
							<?php if ( ! empty( Helper::tfopt( 'rt-description' ) ) ) : ?>
								<p><?php echo wp_kses_post( Helper::tfopt( 'rt-description' ) ); ?></p>
							<?php endif; ?>
						</div>
						<div class="tf-slider-items-wrapper tf-slick-slider">
							<?php while ( $tours->have_posts() ) : $tours->the_post(); ?>
								<?php if ( is_array( $all_tour_ids ) && in_array( get_the_ID(), $all_tour_ids, true ) ) : ?>
									<?php self::render_tour_card_style_3( get_the_ID() ); ?>
								<?php endif; ?>
							<?php endwhile; ?>
						</div>
					</div>
                    <?php echo 'yes' === $container ? '</div>' : ''; ?>
				</div>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
		<?php
	}

	private static function render_tour_card_style_1( $post_id ) {
		$destinations = get_the_terms( $post_id, 'tour_destination' );
		$destination_name = ! empty( $destinations ) && ! is_wp_error( $destinations ) ? $destinations[0]->name : '';
		$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
		$tour_price = new Tour_Price( $meta );
		?>
		<div class="tf-slider-item tf-post-box-lists">
			<div class="tf-post-single-box">
				<div class="tf-image-data">
					<img src="<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'full' ) ) ? esc_url( get_the_post_thumbnail_url( $post_id, 'full' ) ) : esc_url( TF_ASSETS_APP_URL . 'images/feature-default.jpg' ); ?>" alt="">
					<div class="tf-meta-data-price">
						<?php esc_html_e( 'From', 'tourfic' ); ?>
						<span><?php echo wp_kses_post( self::get_tour_price_html( $tour_price, $meta ) ); ?></span>
					</div>
				</div>
				<div class="tf-meta-info tf-mt-30">
					<div class="tf-meta-location">
						<i class="fa-solid fa-location-dot"></i> <?php echo esc_html( $destination_name ); ?>
					</div>
					<div class="tf-meta-title">
						<h2><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( html_entity_decode( get_the_title( $post_id ) ), 35 ) ); ?></a></h2>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	private static function render_tour_card_style_2( $post_id ) {
		$destinations = get_the_terms( $post_id, 'tour_destination' );
		$destination_name = ! empty( $destinations ) && ! is_wp_error( $destinations ) ? $destinations[0]->name : '';
		$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
		$tour_price = new Tour_Price( $meta );
		?>
		<div class="tf-slider-item tf-post-box-lists">
			<div class="tf-post-single-box">
				<div class="tf-image-data">
					<img src="<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'full' ) ) ? esc_url( get_the_post_thumbnail_url( $post_id, 'full' ) ) : esc_url( TF_ASSETS_APP_URL . 'images/feature-default.jpg' ); ?>" alt="">
				</div>
				<div class="tf-meta-info">
					<div class="meta-content">
						<div class="tf-meta-title">
							<h2><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( html_entity_decode( get_the_title( $post_id ) ), 35 ) ); ?></a></h2>
							<div class="tf-meta-data-price">
								<span><?php echo wp_kses_post( self::get_tour_price_html( $tour_price, $meta ) ); ?></span>
							</div>
						</div>
						<div class="tf-meta-location">
							<i class="fa-solid fa-location-dot"></i> <?php echo esc_html( $destination_name ); ?>
						</div>
					</div>
					<a class="see-details" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php esc_html_e( 'See details', 'tourfic' ); ?></a>
				</div>
			</div>
		</div>
		<?php
	}

	private static function render_tour_card_style_3( $post_id ) {
		$destinations = get_the_terms( $post_id, 'tour_destination' );
		$destination_name = ! empty( $destinations ) && ! is_wp_error( $destinations ) ? $destinations[0]->name : '';
		$related_comments = get_comments( [ 'post_id' => $post_id ] );
		$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
		$tour_price = new Tour_Price( $meta );
		?>
		<div class="tf-slider-item" style="background-image: url(<?php echo esc_url( get_the_post_thumbnail_url( $post_id, 'full' ) ); ?>);">
			<div class="tf-slider-content">
				<div class="tf-slider-desc">
					<h3>
						<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( html_entity_decode( get_the_title( $post_id ) ), 35 ) ); ?></a>
						<span><?php echo esc_html( $destination_name ); ?></span>
					</h3>
				</div>
				<div class="tf-suggestion-rating">
					<div class="tf-suggestion-price">
						<span><?php echo wp_kses_post( self::get_tour_price_html( $tour_price, $meta ) ); ?></span>
					</div>
					<?php if ( $related_comments ) : ?>
						<div class="tf-slider-rating-star">
							<i class="fas fa-star"></i> <span style="color:#fff;"><?php echo wp_kses_post( TF_Review::tf_total_avg_rating( $related_comments ) ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	private static function get_tour_price_html( $tour_price, $meta ) {
		$pricing_rule  = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$disable_adult = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;

		if ( 'group' === $pricing_rule ) {
			return ! empty( $tour_price->wc_sale_group ) ? $tour_price->wc_sale_group : $tour_price->wc_group;
		}

		if ( 'person' === $pricing_rule || 'package' === $pricing_rule ) {
			if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
				return ! empty( $tour_price->wc_sale_adult ) ? $tour_price->wc_sale_adult : $tour_price->wc_adult;
			}
			if ( ! $disable_child && ! empty( $tour_price->child ) ) {
				return ! empty( $tour_price->wc_sale_child ) ? $tour_price->wc_sale_child : $tour_price->wc_child;
			}
		}

		return '';
	}

	private static function render_apartment_style_1( $settings, $locations ) {
        $post_id       = get_the_ID();
        $meta = get_post_meta( $post_id, 'tf_apartment_opt', true );
        $container = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
        $wrapper = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
        
		$args = [
			'post_type'      => 'tf_apartment',
			'post_status'    => 'publish',
			'posts_per_page' => 8,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post__not_in'   => [ $post_id ],
			'tax_query'      => [
				[
					'taxonomy' => 'apartment_location',
					'field'    => 'term_id',
					'terms'    => wp_list_pluck( $locations, 'term_id' ),
				],
			],
		];
		$related_apartment = new \WP_Query( $args );
		?>
		<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__two tf-single-apartment-related-post-style1">' : ''; ?>
			<?php if ( $related_apartment->have_posts() ) : ?>
				<div class="tf-related-items-section">
                    <?php echo 'yes' === $container ? '<div class="tf-container"><div class="tf-container-inner">' : ''; ?>
					<div class="section-title">
						<h2 class="tf-title"><?php echo ! empty( $meta['related_apartment_title'] ) ? esc_html( $meta['related_apartment_title'] ) : ''; ?></h2>
					</div>
					<div class="tf-design-3-slider-items-wrapper tf-slick-slider tf-upcomming-tours-list-outter tf-flex tf-flex-gap-24">
						<?php while ( $related_apartment->have_posts() ) : $related_apartment->the_post(); ?>
							<?php
							$selected_post_id = get_the_ID();
							if ( in_array( $selected_post_id, [ $post_id ], true ) ) {
								continue;
							}
							$destinations = get_the_terms( $selected_post_id, 'apartment_location' );
							$destination_name = ! empty( $destinations ) && ! is_wp_error( $destinations ) ? $destinations[0]->name : '';
							$post_meta = get_post_meta( $selected_post_id, 'tf_apartment_opt', true );
							$apartment_min_price = Apt_Pricing::instance( $selected_post_id )->get_min_max_price();
							$pricing_type = ! empty( $post_meta['pricing_type'] ) && 'per_person' === $post_meta['pricing_type'] ? esc_html__( 'Person', 'tourfic' ) : esc_html__( 'Night', 'tourfic' );
							?>
							<div class="tf-slider-item tf-post-box-lists">
								<div class="tf-post-single-box">
									<div class="tf-image-data">
										<img src="<?php echo ! empty( get_the_post_thumbnail_url( $selected_post_id, 'full' ) ) ? esc_url( get_the_post_thumbnail_url( $selected_post_id, 'full' ) ) : esc_url( TF_ASSETS_APP_URL . 'images/feature-default.jpg' ); ?>" alt="">
									</div>
									<div class="tf-meta-info">
										<div class="meta-content">
											<div class="tf-meta-title">
												<h2><a href="<?php echo esc_url( get_permalink( $selected_post_id ) ); ?>"><?php echo esc_html( Helper::tourfic_character_limit_callback( get_the_title( $selected_post_id ), 35 ) ); ?></a></h2>
												<div class="tf-meta-data-price">
													<span><?php echo ! empty( $apartment_min_price['min'] ) ? wp_kses_post( wc_price( $apartment_min_price['min'] ) ) : wp_kses_post( wc_price( 0 ) ); ?></span>
													<span class="pricing_calc_type">/<?php echo esc_html( $pricing_type ); ?></span>
												</div>
											</div>
											<div class="tf-meta-location">
												<i class="fa-solid fa-location-dot"></i> <?php echo esc_html( $destination_name ); ?>
											</div>
										</div>
										<a class="see-details" href="<?php echo esc_url( get_permalink( $selected_post_id ) ); ?>"><?php esc_html_e( 'See details', 'tourfic' ); ?></a>
									</div>
								</div>
							</div>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
					</div>
                    <?php echo 'yes' === $container ? '</div></div>' : ''; ?>
				</div>
			<?php endif; ?>
		<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
		<?php
	}

	private static function render_apartment_style_2( $settings, $locations ) {
        $post_id       = get_the_ID();
        $meta = get_post_meta( $post_id, 'tf_apartment_opt', true );
        $container = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
        $wrapper = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
        
		$args = [
			'post_type'      => 'tf_apartment',
			'post_status'    => 'publish',
			'posts_per_page' => 8,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post__not_in'   => [ $post_id ],
			'tax_query'      => [
				[
					'taxonomy' => 'apartment_location',
					'field'    => 'term_id',
					'terms'    => wp_list_pluck( $locations, 'term_id' ),
				],
			],
		];
		$related_args             = array_merge( $args, [ 'post__not_in' => [ $post_id ] ] );
		$related_apartment        = new \WP_Query( $args );
		$related_apartment_check  = new \WP_Query( $related_args );
		?>
		<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__legacy tf-single-apartment-related-post-style2">' : ''; ?>
			<?php if ( $related_apartment_check->have_posts() ) : ?>
				<div class="tf-related-apartment">
                    <?php echo 'yes' === $container ? '<div class="tf-container">' : ''; ?>
					<h2 class="section-heading"><?php echo ! empty( $meta['related_apartment_title'] ) ? esc_html( $meta['related_apartment_title'] ) : ''; ?></h2>
					<div class="tf-related-apartment-slider tf-slick-slider">
						<?php while ( $related_apartment->have_posts() ) : $related_apartment->the_post(); ?>
							<?php if ( ! in_array( get_the_ID(), [ $post_id ], true ) ) : ?>
								<div class="tf-apartment-item">
									<div class="tf-apartment-item-thumb">
										<?php if ( has_post_thumbnail() ) : ?>
											<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'tourfic-370x250' ); ?></a>
										<?php else : ?>
											<a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url( TF_ASSETS_APP_URL ) . 'images/feature-default.jpg'; ?>"/></a>
										<?php endif; ?>
									</div>
									<div class="tf-related-apartment-content">
										<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
										<span><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></span>
									</div>
								</div>
							<?php endif; ?>
						<?php endwhile; ?>
						<?php wp_reset_query(); ?>
					</div>
                    <?php echo 'yes' === $container ? '</div>' : ''; ?>
				</div>
			<?php endif; ?>
		<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
		<?php
	}
}
