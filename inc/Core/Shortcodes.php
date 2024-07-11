<?php

namespace Tourfic\Core;

defined( 'ABSPATH' ) || exit;

abstract class Shortcodes {

	protected $shortcode = '';

	public function __construct() {
		add_shortcode( $this->shortcode, array( $this, 'render' ) );
	}

	abstract public function render( $atts, $content = '' );

	/*
	 * Filter external post ids from post type, location
	 * @author Foysal
	 */
	function tf_get_external_post_ids($post_type, $location){

		$args = array(
			'post_type'      => $post_type == 'hotel' ? 'tf_hotel' : ( $post_type == 'tour' ? 'tf_tours' : 'tf_apartment' ),
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);

		if ( ! empty( $location ) && $location !== 'all' ) {
			$locations         = explode( ',', $location );
			$args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => $post_type == 'hotel' ? 'hotel_location' : ( $post_type == 'tour' ? 'tour_destination' : 'apartment_location' ),
					'field'    => 'term_id',
					'terms'    => $locations,
				)
			);
		}

		$post_loop = new \WP_Query( $args );
		$post_ids = [];
		if ( $post_loop->have_posts() ) :
			while ( $post_loop->have_posts() ) :
				$post_loop->the_post();

				if($post_type == 'hotel'){
					$meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
				} elseif($post_type == 'tour'){
					$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
				} elseif($post_type == 'apartment'){
					$meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
				}

				$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
				$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';

				if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
					$post_ids[] = get_the_ID();
				}
			endwhile;
		endif;
		wp_reset_postdata();

		return $post_ids;
	}
}