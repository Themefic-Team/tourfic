<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use \Tourfic\App\TF_Review;
use Tourfic\Classes\Room\Room;
use \Tourfic\Classes\Hotel\Pricing;

class Recent_Hotel extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_recent_hotel';

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
			'post_type'      => 'tf_hotel',
			'post_status'    => 'publish',
			'orderby'        => $orderby,
			'order'          => $order,
			'posts_per_page' => $count,
		);

		ob_start();

		$hotel_loop = new \WP_Query( $args );

		// Generate an Unique ID
		$thisid = uniqid( 'tfpopular_' );

		?>
		<?php if ( $hotel_loop->have_posts() ) : ?>
			<div class="tf-widget-slider recent-hotel-slider">
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

				<div class="tf-slider-items-wrapper" data-slick='{"slidesToShow": <?php echo esc_attr( $slidestoshow ); ?>}'>
					<?php while ( $hotel_loop->have_posts() ) {
						$hotel_loop->the_post();
						$post_id                = get_the_ID();
						$related_comments_hotel = get_comments( array( 'post_id' => $post_id ) );
						?>
						<div class="tf-slider-item"
						     style="background-image: url(<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'full' ) ) ? esc_url( get_the_post_thumbnail_url( $post_id, 'full' ) ) : esc_url(TF_ASSETS_APP_URL . '/images/feature-default.jpg'); ?>);">
							<div class="tf-slider-content">
								<div class="tf-slider-desc">
									<h3>
										<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
									</h3>
									<?php if ( $related_comments_hotel ) { ?>
										<div class="tf-slider-rating-star">
											<i class="fas fa-star"></i> <span style="color:#fff;"><?php echo esc_html( TF_review::tf_total_avg_rating( $related_comments_hotel ) ); ?></span>
										</div>
									<?php } ?>
									<p><?php echo wp_kses_post( wp_trim_words( get_the_content(), 10 ) ); ?></p>
                                    <div class="tf-recent-room-price">
                                        <?php echo Pricing::instance( $post_id )->get_min_price_html(); ?>
                                    </div>
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