<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use \Tourfic\App\TF_Review;

class Recent_Tour extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_recent_tour';

	function render( $atts, $content = null ) {
		extract(
			shortcode_atts(
				array(
					'title'        => '',  //title populer section
					'subtitle'     => '',   // Sub title populer section
					'orderby'      => 'date',
					'order'        => 'DESC',
					'count'        => 10,
					'slidestoshow' => 5,
				),
				$atts
			)
		);

		$args = array(
			'post_type'      => 'tf_tours',
			'post_status'    => 'publish',
			'orderby'        => !empty($atts['orderby']) ? $atts['orderby'] : 'date',
			'order'          => !empty($atts['order']) ? $atts['order'] : 'DESC',
			'posts_per_page' => $count,
		);

		ob_start();

		$tour_loop = new \WP_Query( $args );

		// Generate an Unique ID
		$thisid = uniqid( 'tfpopular_' );

		?>
		<?php if ( $tour_loop->have_posts() ) : ?>
			<div class="tf-widget-slider recent-tour-slider">
				<div class="tf-heading">
					<?php
					if ( ! empty( $title ) ) {
						echo '<h2>' . esc_html( $title ) . '</h2>';
					}
					if ( ! empty( $subtitle ) ) {
						echo '<p>' . esc_html( $subtitle ) . '</p>';
					}
					?>
				</div>


				<div class="tf-slider-items-wrapper">
					<?php while ( $tour_loop->have_posts() ) {
						$tour_loop->the_post();
						$post_id          = get_the_ID();
						$related_comments = get_comments( array( 'post_id' => $post_id ) );
						?>
						<div class="tf-slider-item" style="background-image: url(<?php echo esc_attr( get_the_post_thumbnail_url( $post_id, 'full' ) ); ?>);">
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
		wp_reset_postdata(); ?>

		<?php return ob_get_clean();
	}
}