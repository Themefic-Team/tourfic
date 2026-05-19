<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

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
	public static function render( $settings = [], $builder = '', $title = true ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();
        $design    = ! empty( $settings['design'] ) ? $settings['design'] : 'design-1';

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
		$icon_html = ($design == 'design-1') ? '<i class="fa-solid fa-video"></i>' : '<svg width="23" height="18" viewBox="0 0 23 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="content">
                            <path id="Vector 3570" d="M10.5 5L12.5 5" stroke="#FDF9F4" stroke-width="1.5" stroke-linecap="round"/>
                            <path id="Rectangle 368" d="M1.5 8C1.5 4.70017 1.5 3.05025 2.52513 2.02513C3.55025 1 5.20017 1 8.5 1H9.5C12.7998 1 14.4497 1 15.4749 2.02513C16.5 3.05025 16.5 4.70017 16.5 8V10C16.5 13.2998 16.5 14.9497 15.4749 15.9749C14.4497 17 12.7998 17 9.5 17H8.5C5.20017 17 3.55025 17 2.52513 15.9749C1.5 14.9497 1.5 13.2998 1.5 10V8Z" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Rectangle 369" d="M16.5 5.90585L16.6259 5.80196C18.7417 4.05623 19.7996 3.18336 20.6498 3.60482C21.5 4.02628 21.5 5.42355 21.5 8.21808V9.78192C21.5 12.5765 21.5 13.9737 20.6498 14.3952C19.7996 14.8166 18.7417 13.9438 16.6259 12.198L16.5 12.0941" stroke="#FDF9F4" stroke-width="1.5" stroke-linecap="round"/>
                            </g>
                            </svg>';

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

		$label = isset( $settings['label'] ) ? $settings['label'] : esc_html__( 'Video', 'tourfic' );
		?>
		<div class="tf-single-action-btns featured-column tf-video-box">
			<a class="tf-tour-video" id="featured-video" data-fancybox="tour-video" href="<?php echo esc_url( $video ); ?>">
				<?php echo wp_kses( $icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
				<?php echo $title ? esc_html( $label ) : ''; ?>
			</a>
		</div>
		<?php
	}
}
