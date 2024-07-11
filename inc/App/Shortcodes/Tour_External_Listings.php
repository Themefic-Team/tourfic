<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

use \Tourfic\App\TF_Review;

class Tour_External_Listings extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_tour_external_listings';

	function render( $atts, $content = null ) {
		extract(
			shortcode_atts(
				array(
					'title'     => '',
					'subtitle'  => '',
					'locations' => '',
					'count'     => '3',
					'style'     => 'grid',
				),
				$atts
			)
		);

		$external_post_ids = $this->tf_get_external_post_ids('tour', $locations);

		$args = array(
			'post_type'      => 'tf_tours',
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => $count,
			'post__in'       => $external_post_ids,
		);

		ob_start();

		if ( $style == 'slider' ) {
			$slider_activate = 'tf-slider-activated';
		} else {
			$slider_activate = 'tf-hotel-grid';
		}
		$post_loop = new \WP_Query( $args );
		?>
		<?php if ( $post_loop->have_posts() ) : ?>
            <div class="tf-widget-slider recent-hotel-slider">
                <div class="tf-heading">
					<?php
					echo ! empty( $title ) ? '<h2>' . esc_html( $title ) . '</h2>' : '';
					echo ! empty( $subtitle ) ? '<p>' . esc_html( $subtitle ) . '</p>' : '';
					?>
                </div>

                <div class="<?php echo esc_attr( $slider_activate ); ?>">
					<?php while ( $post_loop->have_posts() ) :
						$post_loop->the_post();
						$post_id       = get_the_ID();
						$post_comments = get_comments( array( 'post_id' => $post_id ) );
						$meta  = get_post_meta( $post_id, 'tf_tours_opt', true );

						if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
							$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
							$tf_booking_url       = ! empty( $meta['booking-url'] ) ? esc_url( $meta['booking-url'] ) : '';
							$tf_booking_query_url = ! empty( $meta['booking-query'] ) ? $meta['booking-query'] : 'adult={adult}&child={child}&room={room}';
							$tf_booking_attribute = ! empty( $meta['booking-attribute'] ) ? $meta['booking-attribute'] : '';
						}
						if ( 2 == $tf_booking_type && ! empty( $tf_booking_url ) ) {
							$external_search_info = array(
								'{adult}'    => ! empty( $adult ) ? $adult : 1,
								'{child}'    => ! empty( $child ) ? $child : 0,
								'{checkin}'  => ! empty( $check_in ) ? $check_in : gmdate( 'Y-m-d' ),
								'{checkout}' => ! empty( $check_out ) ? $check_out : gmdate( 'Y-m-d', strtotime( '+1 day' ) ),
								'{room}'     => ! empty( $room_selected ) ? $room_selected : 1,
							);
							if ( ! empty( $tf_booking_attribute ) ) {
								$tf_booking_query_url = str_replace( array_keys( $external_search_info ), array_values( $external_search_info ), $tf_booking_query_url );
								if ( ! empty( $tf_booking_query_url ) ) {
									$tf_booking_url = $tf_booking_url . '/?' . $tf_booking_query_url;
								}
							}
						}

						if($tf_booking_type == 2 && !empty($tf_booking_url)):
							?>
                            <div class="tf-slider-item" style="background-image: url(<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'full' ) ) ? esc_url( get_the_post_thumbnail_url( $post_id, 'full' ) ) : esc_url(TF_ASSETS_APP_URL . '/images/feature-default.jpg'); ?>);">
                                <div class="tf-slider-content">
                                    <div class="tf-slider-desc">
                                        <h3><a href="<?php esc_url(the_permalink()) ?>" target="_blank"><?php the_title() ?></a></h3>
										<?php if ( $post_comments ) { ?>
                                            <div class="tf-slider-rating-star">
                                                <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo esc_html( TF_Review::tf_total_avg_rating( $post_comments ) ); ?></span>
                                            </div>
										<?php } ?>
                                        <p><?php echo wp_kses_post( wp_trim_words( get_the_content(), 10 ) ); ?></p>
										<?php if ( ! empty( $rooms ) ): ?>
                                            <div class="tf-recent-room-price">
												<?php
												if ( ! empty( $room_price ) ) {
													//get the lowest price from all available room price
													$lowest_price = wc_price( min( $room_price ) );
													echo esc_html__( "From ", "tourfic" ) . wp_kses_post( $lowest_price );
												}
												?>
                                            </div>
										<?php endif; ?>
                                    </div>
                                </div>
                            </div>
						<?php endif; endwhile; ?>
                </div>
            </div>
		<?php endif;
		wp_reset_postdata();

		return ob_get_clean();
	}
}