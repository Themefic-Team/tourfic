<?php

namespace Tourfic\App\Templates\Components\Global\Single;

use Elementor\Icons_Manager;
use Tourfic\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Video Button Component
 * Shared markup for Elementor and Bricks Video Button widgets
 */
class Video_Button {

	/**
	 * Static render method for Video Button component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();

		if ( 'tf_hotel' === $post_type ) {
			$meta  = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$video = ! empty( $meta['video'] ) ? $meta['video'] : '';
		} elseif ( 'tf_tours' === $post_type ) {
			$meta  = get_post_meta( $post_id, 'tf_tours_opt', true );
			$video = ! empty( $meta['tour_video'] ) ? $meta['tour_video'] : '';
		} elseif ( 'tf_apartment' === $post_type ) {
			$meta  = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$video = ! empty( $meta['video'] ) ? $meta['video'] : '';
		} else {
			return;
		}

		if ( empty( $video ) ) {
			return;
		}

		// Render icon
		$icon_html = '<i class="fas fa-video" aria-hidden="true"></i>';

		if ( 'elementor' === $builder && class_exists( '\Elementor\Icons_Manager' ) ) {
			$icon_migrated = isset( $settings['__fa4_migrated']['icon'] );
			$icon_is_new   = empty( $settings['icon_comp'] );

			if ( $icon_is_new || $icon_migrated ) {
				ob_start();
				Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
				$icon_html = ob_get_clean();
			} else {
				$icon_html = '<i class="' . esc_attr( $settings['icon_comp'] ) . '" aria-hidden="true"></i>';
			}
		} elseif ( 'bricks' === $builder ) {
			if ( ! empty( $settings['icon']['library'] ) && ! empty( $settings['icon']['icon'] ) ) {
				$icon_html = '<i class="' . esc_attr( $settings['icon']['icon'] ) . '" aria-hidden="true"></i>';
			} elseif ( ! empty( $settings['icon']['class'] ) ) {
				$icon_html = '<i class="' . esc_attr( $settings['icon']['class'] ) . '" aria-hidden="true"></i>';
			} elseif ( ! empty( $settings['icon'] ) && is_string( $settings['icon'] ) ) {
				$icon_html = '<i class="' . esc_attr( $settings['icon'] ) . '" aria-hidden="true"></i>';
			}
		}

		$label = ! empty( $settings['label'] ) ? $settings['label'] : esc_html__( 'Video', 'tourfic' );
		?>
		<div class="tf-single-action-btns featured-column tf-video-box">
			<a class="tf-tour-video" id="featured-video" data-fancybox="tour-video" href="<?php echo esc_url( $video ); ?>">
				<?php echo wp_kses( $icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
				<?php echo esc_html( $label ); ?>
			</a>
		</div>
		<?php
	}
}
