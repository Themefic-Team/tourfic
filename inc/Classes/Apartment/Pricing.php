<?php

namespace Tourfic\Classes\Apartment;
defined( 'ABSPATH' ) || exit;

class Pricing {

	use \Tourfic\Traits\Singleton;

	public function __construct() {

	}

	static function get_apartment_min_max_price( $post_id = null ) {
		$min_max_price = array();

		$apartment_args = array(
			'post_type'      => 'tf_apartment',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
		);

		if ( isset( $post_id ) && ! empty( $post_id ) ) {
			$apartment_args['post__in'] = array( $post_id );
		}
		$apartment_query = new \WP_Query( $apartment_args );

		if ( $apartment_query->have_posts() ) {
			while ( $apartment_query->have_posts() ) {
				$apartment_query->the_post();
				$meta                = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
				$pricing_type        = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
				$adult_price         = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
				$enable_availability = ! empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';
				if ( $enable_availability === '1' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
					$apt_availability = ! empty( $meta['apt_availability'] ) ? json_decode( $meta['apt_availability'], true ) : [];

					if ( ! empty( $apt_availability ) && is_array( $apt_availability ) ) {
						foreach ( $apt_availability as $single_avail ) {
							if ( $pricing_type === 'per_night' ) {
								$min_max_price[] = ! empty( $single_avail['price'] ) ? intval( $single_avail['price'] ) : 0;

							} else {
								$min_max_price[] = ! empty( $single_avail['adult_price'] ) ? intval( $single_avail['adult_price'] ) : 0;
							}
						}
					}

				} else {
					$min_max_price[] = $pricing_type === 'per_night' && ! empty( $meta['price_per_night'] ) ? intval( $meta['price_per_night'] ) : intval( $adult_price );
				}
			}
		}

		$min_max_price = array_filter($min_max_price);

		wp_reset_query();

		return array(
			'min' => ! empty( $min_max_price ) ? min( $min_max_price ) : 0,
			'max' => ! empty( $min_max_price ) ? max( $min_max_price ) : 0,
		);
	}
}