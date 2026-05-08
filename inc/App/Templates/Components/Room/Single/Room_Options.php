<?php

namespace Tourfic\App\Templates\Components\Room\Single;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Pricing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Room_Options Component
 * Shared markup for Elementor and Bricks Room_Options widgets
 */
class Room_Options {

	/**
	 * Static render method for Room_Options component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();

		if ( 'tf_room' !== $post_type ) {
			return;
		}

		self::render_room_options( $settings );
	}

	/**
	 * Render room options
	 *
	 * @param array $settings Settings from widget
	 *
	 * @return void
	 */
	private static function render_room_options( $settings ) {
		$post_id = get_the_ID();
		$meta    = get_post_meta( $post_id, 'tf_room_opt', true );

		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( wp_unslash( $_GET['check-in-out-date'] ) ) : '';

		if ( $check_in_out ) {
			$form_check_in      = substr( $check_in_out, 0, 10 );
			$form_check_in_stt  = strtotime( $form_check_in );
			$form_check_out     = substr( $check_in_out, 14, 10 );
			$form_check_out_stt = strtotime( $form_check_out );
		}

		if ( ! empty( $check_in_out ) ) {
			list( $tf_form_start, $tf_form_end ) = tf_split_date_range( $check_in_out );
		}

		if ( ! empty( $check_in_out ) ) {
			$period = new \DatePeriod(
				new \DateTime( $tf_form_start ),
				new \DateInterval( 'P1D' ),
				new \DateTime( ! empty( $tf_form_end ) ? $tf_form_end : $tf_form_start . '23:59' )
			);
		} else {
			$period = '';
		}

		$min_price_arr       = Pricing::instance( $post_id )->get_min_price( $period );
		$min_discount_type   = ! empty( $min_price_arr['min_discount_type'] ) ? $min_price_arr['min_discount_type'] : 'none';
		$min_discount_amount = ! empty( $min_price_arr['min_discount_amount'] ) ? $min_price_arr['min_discount_amount'] : 0;
		$tf_room_book_button_text = ! empty( Helper::tfopt( 'room_booking_button_text' ) ) ? stripslashes( sanitize_text_field( Helper::tfopt( 'room_booking_button_text' ) ) ) : esc_html__( 'Book Now', 'tourfic' );
		$pricing_by          = ! empty( $meta['pricing-by'] ) ? $meta['pricing-by'] : 1;
		$unique_id           = ! empty( $meta['unique_id'] ) ? $meta['unique_id'] : '';

		if ( '3' === $pricing_by && isset( $meta['room-options'] ) && ! empty( Helper::tf_data_types( $meta['room-options'] ) ) ) :
			?>
			<div class="tf-single-template__two tf-single-hotel-room-options__style-1">
				<div class="tf-room-options" id="tf-room-options">
					<?php foreach ( $meta['room-options'] as $room_option_key => $room_option ) : ?>
						<div class="tf-room-option">
							<div class="tf-room-option-left">
								<h3><?php echo ! empty( $room_option['option_title'] ) ? esc_html( $room_option['option_title'] ) : ''; ?></h3>
								<?php if ( ! empty( $room_option['room-facilities'] ) ) :
									echo '<ul class="tf-room-features">';
									foreach ( $room_option['room-facilities'] as $room_facility ) :
										?>
										<li>
											<span class="room-extra-icon"><i class="<?php echo esc_attr( $room_facility['room_facilities_icon'] ); ?>"></i></span>
											<span class="room-extra-label"><?php echo wp_kses_post( $room_facility['room_facilities_label'] ); ?></span>
										</li>
										<?php
									endforeach;
									echo '</ul>';
								endif; ?>
							</div>

							<div class="tf-room-option-right">
								<div class="tf-room-price-wrap">
									<!-- Discount -->
									<?php if ( ! empty( $min_discount_amount ) ) : ?>
										<div class="tf-room-off">
											<span><?php echo 'percent' === $min_discount_type ? esc_html( $min_discount_amount ) . '%' : wp_kses_post( wc_price( $min_discount_amount ) ); ?><?php esc_html_e( ' Off ', 'tourfic' ); ?></span>
										</div>
									<?php endif; ?>

									<!-- Price -->
									<div class="tf-room-price"><?php Pricing::instance( $post_id )->get_per_price_html( $room_option_key, 'design-2' ); ?></div>
								</div>

								<!-- View Details -->
								<a href="" class="tf_btn tf_btn_rounded tf_btn_large tf-room-option-book" data-option-key="<?php echo esc_attr( $unique_id . '_' . $room_option_key ); ?>"><?php echo esc_html( $tf_room_book_button_text ); ?></a>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php
		endif;
	}
}
