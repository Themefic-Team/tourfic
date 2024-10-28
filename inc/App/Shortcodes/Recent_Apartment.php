<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use \Tourfic\App\TF_Review;
use \Tourfic\Classes\Apartment\Pricing;

class Recent_Apartment extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_recent_apartment';

	function render( $atts, $content = null ) {
		extract(
			shortcode_atts(
				array(
					'title'        => '',
					'subtitle'     => '',
					'count'        => 10,
					'slidestoshow' => 5,
				),
				$atts
			)
		);

		$args = array(
			'post_type'      => 'tf_apartment',
			'post_status'    => 'publish',
			'orderby'        => !empty($atts['orderby']) ? $atts['orderby'] : 'date',
			'order'          => !empty($atts["order"]) ? $atts["order"] : "DESC",
			'posts_per_page' => $count,
		);

		ob_start();

		$apartment_loop = new \WP_Query( $args );

		// Generate an Unique ID
		$thisid = uniqid( 'tfpopular_' );

		?>
		<?php if ( $apartment_loop->have_posts() ) : ?>
			<div class="tf-widget-slider recent-apartment-slider">
				<div class="tf-heading">
					<?php
					echo ! empty( $title ) ? '<h2>' . esc_html( $title ) . '</h2>' : '';
					echo ! empty( $subtitle ) ? '<p>' . esc_html( $subtitle ) . '</p>' : '';
					?>
				</div>

				<div class="tf-slider-items-wrapper">
					<?php while ( $apartment_loop->have_posts() ) {
						$apartment_loop->the_post();
						$post_id                    = get_the_ID();
						$related_comments_apartment = get_comments( array( 'post_id' => $post_id ) );
						$meta                       = get_post_meta( $post_id, 'tf_apartment_opt', true );
						$min_price = Pricing::instance( $post_id )->get_min_max_price();
						$discounted_price = Pricing::instance( $post_id )->calculate_discount( $min_price["min"] );

						?>
						<div class="tf-slider-item" style="background-image: url(<?php echo esc_url( get_the_post_thumbnail_url( $post_id, 'full' ) ); ?>);">
							<div class="tf-slider-content">
								<div class="tf-slider-desc">
									<h3>
										<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
									</h3>
									<?php if ( $related_comments_apartment ) { ?>
										<div class="tf-slider-rating-star">
											<i class="fas fa-star"></i> <span style="color:#fff;"><?php echo esc_html( TF_Review::tf_total_avg_rating( $related_comments_apartment ) ); ?></span>
										</div>
									<?php } ?>
									<p><?php echo wp_kses_post( wp_trim_words( get_the_content(), 10 ) ); ?></p>
									<div class="tf-recent-room-price">
                                        <?php echo esc_html("From "); ?>
										<?php echo $min_price["min"] == $discounted_price ? wp_kses_post( wc_price($min_price["min"]) ) : '<del>' . wp_kses_post( wc_price($min_price["min"]) ) . '</del>' . ' ' . wp_kses_post( wc_price( $discounted_price ) ); ?>
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