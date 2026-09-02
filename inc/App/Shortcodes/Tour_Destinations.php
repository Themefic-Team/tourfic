<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

class Tour_Destinations extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tourfic_tour_destinations';

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

		$tf_disable_services = ! empty( Helper::tfopt( 'disable-services' ) ) ? Helper::tfopt( 'disable-services' ) : [];
		if (in_array('tour', $tf_disable_services)){
			return;
		}
		
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
		if ( is_wp_error( $destinations ) ) {
			return '';
		}

		$tf_expired_tour_showing = ! empty( Helper::tfopt( 't-show-expire-tour' ) ) ? Helper::tfopt( 't-show-expire-tour' ) : '';
		$tour_counts             = $this->get_destination_tour_counts( $destinations, ! empty( $tf_expired_tour_showing ) );

//		shuffle( $destinations );
		ob_start();

		if ( $destinations ) { ?>
			<section id="tf_recomended_section_wrapper">
				<div class="recomended_inner">

					<?php foreach ( $destinations as $term ) {

						$meta       = get_term_meta( $term->term_id, 'tf_tour_destination', true );
						$image_url  = ! empty( $meta['image'] ) ? $meta['image'] : esc_url(TOURFIC_ASSETS_APP_URL . 'images/feature-default.jpg');
						$term_link  = get_term_link( $term );
						$tour_count = $tour_counts[ $term->term_id ] ?? 0;

						if ( is_wp_error( $term_link ) ) {
							continue;
						} ?>

						<div class="single_recomended_item">
							<a href="<?php echo esc_url( $term_link ); ?>">
								<div class="single_recomended_content" style="background-image: url(<?php echo esc_url( $image_url ); ?>);">
									<div class="recomended_place_info_header">
										<h3><?php echo esc_html( $term->name ); ?></h3>
										<?php /* translators: %s Tour Count */ ?>
										<p><?php printf( esc_html( _n( '%s tour', '%s tours', $tour_count, 'tourfic' ) ), esc_html( $tour_count ) ); ?></p>
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

	/**
	 * Get displayed tour counts for destination terms.
	 *
	 * @param array $destinations   Destination term objects.
	 * @param bool  $include_expired Whether expired tours should be counted.
	 * @return array<int, int>
	 */
	private function get_destination_tour_counts( array $destinations, $include_expired ) {
		$tour_counts = array();

		foreach ( $destinations as $destination ) {
			$tour_counts[ $destination->term_id ] = (int) $destination->count;
		}

		if ( ! $include_expired || empty( $tour_counts ) ) {
			return $tour_counts;
		}

		// Core term counts include published posts only, so add expired relationships in one batch.
		$expired_tour_ids = get_posts(
			array(
				'post_type'              => 'tf_tours',
				'post_status'            => 'expired',
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'suppress_filters'       => false,
			)
		);

		if ( empty( $expired_tour_ids ) ) {
			return $tour_counts;
		}

		$expired_destinations = wp_get_object_terms(
			$expired_tour_ids,
			'tour_destination',
			array( 'fields' => 'all_with_object_id' )
		);

		if ( is_wp_error( $expired_destinations ) ) {
			return $tour_counts;
		}

		foreach ( $expired_destinations as $expired_destination ) {
			$term_id = (int) $expired_destination->term_id;
			if ( isset( $tour_counts[ $term_id ] ) ) {
				++$tour_counts[ $term_id ];
			}
		}

		return $tour_counts;
	}
}
