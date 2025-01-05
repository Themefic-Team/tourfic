<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

class Recent_Blog extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_recent_blog';

	function render( $atts, $content = null ) {
		extract(
			shortcode_atts(
				array(
					'title'    => '',
					'subtitle' => '',
					'count'    => '5',
					'cats'     => '',

				),
				$atts
			)
		);

		$args = array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => $count,
		);

		//Check if category selected/choosen
		if ( ! empty( $cats ) && $cats !== 'all' ) {
			$cats              = explode( ',', $cats );
			$args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $cats,
				)
			);
		}
		$loop = new \WP_Query( $args );

		ob_start();

		?>
		<?php if ( $loop->have_posts() ) { ?>
			<div class="tf-recent-blog-wrapper">
				<div class="tf-heading">
					<?php
					echo ! empty( $title ) ? '<h2>' . esc_html( $title ) . '</h2>' : '';
					echo ! empty( $subtitle ) ? '<p>' . esc_html( $subtitle ) . '</p>' : '';
					?>
				</div>


				<div class="recent-blogs">
					<?php while ( $loop->have_posts() ) {
						$loop->the_post();
						$post_id = get_the_ID();

						//different markup for first 3 posts
						if ( $loop->current_post == 0 ) {
							echo "<div class='post-section-one'>";
						}

						if ( $loop->current_post <= 2 ) {
							?>

							<div class="tf-single-item" style="background-image: url(<?php echo esc_url( get_the_post_thumbnail_url( $post_id, 'full' ) ); ?>);">
								<div class="tf-post-content">
									<div class="tf-post-desc">
										<h3>
											<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
										</h3>
										<p><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 10 ) ); ?></p>

									</div>
								</div>
							</div>
							<?php
							if ( $loop->current_post == 2 ) {
								echo "</div>";
							}
						} else { ?>
							<div class="tf-single-item" style="background-image: url(<?php echo esc_url( get_the_post_thumbnail_url( $post_id, 'full' ) ); ?>);">
								<div class="tf-post-content">
									<div class="tf-post-desc">
										<h3>
											<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
										</h3>
										<p><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 10 ) ); ?></p>

									</div>
								</div>
							</div>

						<?php }
					} ?>
				</div>
			</div>
		<?php } else {
			echo esc_html__( 'No posts found', 'tourfic' );
		}
		wp_reset_postdata();

		return ob_get_clean();
	}
}