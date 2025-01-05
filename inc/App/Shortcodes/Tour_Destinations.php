<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

class Tour_Destinations extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tour_destinations';

	function render( $atts, $content = null ) {
		// Shortcode extract
		extract(
			shortcode_atts(
				array(
					'orderby'    => 'name',
					'order'      => 'ASC',
					'hide_empty' => 0,
					'ids'        => '',
					'limit'      => - 1,
				),
				$atts
			)
		);

		// 1st search on Destination taxonomy
		$destinations = get_terms( array(
			'taxonomy'     => 'tour_destination',
			'orderby'      => $orderby,
			'order'        => $order,
			'hide_empty'   => $hide_empty,
			'hierarchical' => 0,
			'search'       => '',
			'number'       => $limit == - 1 ? false : $limit,
			'include'      => $ids,
		) );

		$tf_expired_tour_showing = ! empty( Helper::tfopt( 't-show-expire-tour' ) ) ? Helper::tfopt( 't-show-expire-tour' ) : '';
		if(!empty($tf_expired_tour_showing )){
			$tf_tour_posts_status = array('publish','expired');
		}else{
			$tf_tour_posts_status = array('publish');
		}

//		shuffle( $destinations );
		ob_start();

		if ( $destinations ) { ?>
			<section id="recomended_section_wrapper">
				<div class="recomended_inner">

					<?php foreach ( $destinations as $term ) {

						$meta      = get_term_meta( $term->term_id, 'tf_tour_destination', true );
						$image_url = ! empty( $meta['image'] ) ? $meta['image'] : esc_url(TF_ASSETS_APP_URL . 'images/feature-default.jpg');
						$term_link = get_term_link( $term );

						$taxonomy_query = new \WP_Query( array(
							'post_status' => $tf_tour_posts_status,
							'posts_per_page' => -1,
							'tax_query' => array(
								array(
									'taxonomy' => 'tour_destination',
									'field' => 'id',
									'terms' => $term->term_id,
								),
							),
						) );

						if ( is_wp_error( $term_link ) ) {
							continue;
						} ?>

						<div class="single_recomended_item">
							<a href="<?php echo esc_url( $term_link ); ?>">
								<div class="single_recomended_content" style="background-image: url(<?php echo esc_url( $image_url ); ?>);">
									<div class="recomended_place_info_header">
										<h3><?php echo esc_html( $term->name ); ?></h3>
										<?php /* translators: %s Tour Count */ ?>
										<p><?php printf( esc_html( _n( '%s tour', '%s tours', !empty( $taxonomy_query ) ? $taxonomy_query->post_count : 0, 'tourfic' ) ), esc_html( !empty( $taxonomy_query ) ? $taxonomy_query->post_count : 0 ) ); ?></p>
									</div>
								</div>
							</a>
						</div>

					<?php } ?>

				</div>
			</section>
		<?php }

		return ob_get_clean();
	}
}