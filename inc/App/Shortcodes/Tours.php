<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use \Tourfic\App\TF_Review;

class Tours extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_tour';

	function render( $atts, $content = null ) {
		extract(
			shortcode_atts(
				array(
					'title'        => '',
					'subtitle'     => '',
					'destinations' => '',
					'count'        => '3',
					'style'        => 'grid',
				),
				$atts
			)
		);

		$args = array(
			'post_type'      => 'tf_tours',
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => $count,
		);
		//Check if destination selected/choosen
		if ( ! empty( $destinations ) && $destinations !== 'all' ) {
			$destinations      = explode( ',', $destinations );
			$args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'tour_destination',
					'field'    => 'term_id',
					'terms'    => $destinations,
				)
			);
		}
		ob_start();

		if ( $style == 'slider' ) {
			$slider_activate = 'tf-slider-activated';
		} else {
			$slider_activate = 'tf-hotel-grid';
		}
		$tour_loop = new \WP_Query( $args );

		?>
		<?php if ( $tour_loop->have_posts() ) : ?>
			<div class="tf-widget-slider recent-tour-slider">
				<div class="tf-heading">
					<?php
					echo ! empty( $title ) ? '<h2>' . esc_html( $title ) . '</h2>' : '';
					echo ! empty( $subtitle ) ? '<p>' . esc_html( $subtitle ) . '</p>' : '';
					?>
				</div>

				<div class="<?php echo esc_attr( $slider_activate ); ?>">
					<?php while ( $tour_loop->have_posts() ) {
						$tour_loop->the_post();
						$post_id          = get_the_ID();
						$related_comments = get_comments( array( 'post_id' => $post_id ) );
						?>
						<div class="tf-slider-item" style="background-image: url(<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'full' ) ) ? esc_url( get_the_post_thumbnail_url( $post_id, 'full' ) ) : esc_url(TF_ASSETS_APP_URL . '/images/feature-default.jpg'); ?>);">
							<div class="tf-slider-content">
								<div class="tf-slider-desc">
									<h3>
										<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
									</h3>
									<?php if ( $related_comments ) { ?>
										<div class="tf-slider-rating-star">
											<i class="fas fa-star"></i> <span style="color:#fff;"><?php echo esc_html( TF_Review::tf_total_avg_rating( $related_comments ) ); ?></span>
										</div>
									<?php } ?>
									<p><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 10 ) ); ?></p>

								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php endif;
		wp_reset_postdata();

		return ob_get_clean();
	}
}