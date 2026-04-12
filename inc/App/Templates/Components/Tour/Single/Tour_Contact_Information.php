<?php

namespace Tourfic\App\Templates\Components\Tour\Single;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single Tour Contact Information Component
 * Shared markup for Elementor and Bricks Tour Contact Information widgets
 */
class Tour_Contact_Information {

	/**
	 * Static render method for Tour Contact Information component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();
		$wrapper_open = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

		if ( 'tf_tours' !== $post_type ) {
			return;
		}

		$meta    = get_post_meta( $post_id, 'tf_tours_opt', true );
		$email   = ! empty( $meta['email'] ) ? $meta['email'] : '';
		$phone   = ! empty( $meta['phone'] ) ? $meta['phone'] : '';
		$fax     = ! empty( $meta['fax'] ) ? $meta['fax'] : '';
		$website = ! empty( $meta['website'] ) ? $meta['website'] : '';
		$style   = ! empty( $settings['icon_style'] ) ? $settings['icon_style'] : 'style1';

		if ( ! $email || ! $phone || ! $fax || ! $website ) {
			return;
		}

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';
		?>
		<div class="tf-tour-booking-advantages tf-box">
			<div class="tf-head-title">
				<h3 class="tf-section-title"><?php echo ! empty( $meta['contact-info-section-title'] ) ? esc_html( $meta['contact-info-section-title'] ) : ''; ?></h3>
			</div>
			<div class="tf-booking-advantage-items">
				<ul class="tf-list tf-icon-<?php echo esc_attr( $style ); ?>">
					<?php
					if ( ! empty( $phone ) ) {
						?>
						<li><i class="fa-solid fa-headphones"></i> <a href="tel:<?php echo esc_html( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></li>
						<?php
					}
					?>
					<?php
					if ( ! empty( $email ) ) {
						?>
						<li><i class="fa-solid fa-envelope"></i> <a href="mailto:<?php echo esc_html( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
						<?php
					}
					?>
					<?php
					if ( ! empty( $website ) ) {
						?>
						<li><i class="fa-solid fa-link"></i> <a target="_blank" href="<?php echo esc_html( $website ); ?>"><?php echo esc_html( $website ); ?></a></li>
						<?php
					}
					?>
					<?php
					if ( ! empty( $fax ) ) {
						?>
						<li><i class="fa-solid fa-fax"></i> <a href="tel:<?php echo esc_html( $fax ); ?>"><?php echo esc_html( $fax ); ?></a></li>
						<?php
					}
					?>
				</ul>
			</div>
		</div>
		<?php
		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}
}
