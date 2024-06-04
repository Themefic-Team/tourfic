<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

class Vendor_Post extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_vendor_post';

	function render( $atts, $content = null ) {
		ob_start();
		extract(
			shortcode_atts(
				array(
					'type'      => '',
					'style'     => 'grid',
					'count'     => 4,
					'vendor'    => '',
					'vendor_id' => '',
				),
				$atts
			)
		);

		$args = array(
			'post_type'      => $type,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => $count,
			'author'         => sanitize_key( $vendor_id ),
		);

		$tf_vendors_posts = new \WP_Query( $args );
		if ( $tf_vendors_posts->have_posts() ) :
			?>
			<div class="tf-widget-slider recent-tour-slider">
				<div class="tf-hotel-grid">
					<?php while ( $tf_vendors_posts->have_posts() ) {
						$tf_vendors_posts->the_post();
						$post_id          = get_the_ID();
						$related_comments = get_comments( array( 'post_id' => $post_id ) );
						?>
						<div class="tf-slider-item"
						     style="background-image: url(<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'full' ) ) ? esc_url( get_the_post_thumbnail_url( $post_id, 'full' ) ) : esc_url(TF_ASSETS_APP_URL . '/images/feature-default.jpg'); ?>);">
							<div class="tf-slider-content">
								<div class="tf-slider-desc">
									<h3>
										<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
									</h3>
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