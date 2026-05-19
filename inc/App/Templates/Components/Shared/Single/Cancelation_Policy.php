<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Cancelation_Policy Component
 * Shared markup for Elementor and Bricks Cancelation_Policy widgets
 */
class Cancelation_Policy {

	/**
	 * Static render method for Cancelation_Policy component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();

		if ( 'tf_room' === $post_type ) {
			self::render_room_cancelation_policy( $settings );
		}
	}

	/**
	 * Render room cancelation policy
	 *
	 * @param array $settings Settings from widget
	 *
	 * @return void
	 */
	private static function render_room_cancelation_policy( $settings ) {
		$post_id                  = get_the_ID();
		$meta                     = get_post_meta( $post_id, 'tf_room_opt', true );
		$cancelation_policy_title = ! empty( $meta['cancelation-section-title'] ) ? esc_html( $meta['cancelation-section-title'] ) : esc_html__( 'Cancelation Policy', 'tourfic' );
		$cancelation_policy       = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['calcellation_policy'] ) ? (array) $meta['calcellation_policy'] : [];

		if ( empty( $cancelation_policy ) ) {
			return;
		}

		?>
		<div class="tf-single-template__two">
			<div class="tf-room-cancellation-policy">
				<h2 class="tf-title tf-section-title"><?php echo esc_html( $cancelation_policy_title ); ?></h2>
				<table>
					<?php
					foreach ( $cancelation_policy as $policy ) :
						if ( ! empty( $policy['before_cancel_time'] ) ) :
							?>
							<tr>
								<td><?php echo esc_html( $policy['before_cancel_time'] ); ?> <?php echo $policy['cancellation-times'] > 1 ? esc_html( $policy['cancellation-times'] ) . 's' : esc_html( $policy['cancellation-times'] ); ?> <?php esc_html_e( 'Before', 'tourfic' ); ?></td>
								<td>
									<?php
									if ( 'free' === $policy['cancellation_type'] ) {
										echo esc_html__( 'Free Cancellation', 'tourfic' );
									} else {
										if ( ! empty( $policy['refund_amount'] ) && 'percent' === $policy['refund_amount_type'] ) {
											echo esc_html( $policy['refund_amount'] ) . '% ' . esc_html__( 'Deduction', 'tourfic' );
										}
										if ( ! empty( $policy['refund_amount'] ) && 'fixed' === $policy['refund_amount_type'] ) {
											echo wc_price( $policy['refund_amount'] ) . ' ' . esc_html__( 'Deduction', 'tourfic' );
										}
									}
									?>
								</td>
							</tr>
							<?php
						endif;
					endforeach;
					?>
				</table>
			</div>
		</div>
		<?php
	}
}
