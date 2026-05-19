<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Elementor\Icons_Manager;
use Tourfic\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Gallery Button Component
 * Shared markup for Elementor and Bricks Gallery Button widgets
 */
class Gallery_Button {

	/**
	 * Static render method for Gallery Button component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();
		$style     = ! empty( $settings['style'] ) ? $settings['style'] : 'style1';
        $design    = ! empty( $settings['design'] ) ? $settings['design'] : 'design-1';

		if ( 'tf_hotel' === $post_type ) {
			$meta    = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$gallery = ! empty( $meta['gallery'] ) ? $meta['gallery'] : '';
		} elseif ( 'tf_room' === $post_type ) {
			$meta    = get_post_meta( $post_id, 'tf_room_opt', true );
			$gallery = ! empty( $meta['gallery'] ) ? $meta['gallery'] : '';
		} elseif ( 'tf_tours' === $post_type ) {
			$meta    = get_post_meta( $post_id, 'tf_tours_opt', true );
			$gallery = ! empty( $meta['tour_gallery'] ) ? $meta['tour_gallery'] : '';
		} elseif ( 'tf_apartment' === $post_type ) {
			$meta    = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$gallery = ! empty( $meta['apartment_gallery'] ) ? $meta['apartment_gallery'] : '';
		} else {
			return;
		}

		if ( empty( $gallery ) ) {
			return;
		}

		$gallery_ids = ! empty( $gallery ) ? explode( ',', $gallery ) : [];

		if ( empty( $gallery_ids ) ) {
			return;
		}

		// Render icon
		$icon_html = ($style == 'style1') ? '<i class="fa-solid fa-camera-retro"></i>' : '<svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g id="content">
                            <path id="Rectangle 2111" d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Rectangle 2109" d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z" stroke="#FDF9F4" stroke-width="1.5"/>
                            <path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"/>
                            <path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
		$label = isset( $settings['label'] ) ? $settings['label'] : esc_html__( 'Gallery', 'tourfic' );

		if ( 'style1' === $style ) {
			?>
			<div class="tf-single-action-btns featured-column tf-gallery-box">
				<a id="featured-gallery" href="#" class="tf-tour-gallery">
					<?php echo wp_kses( $icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
					<?php echo esc_html( $label ); ?>
				</a>
			</div>
			<?php
		} elseif ( 'style2' === $style ) {
			?>
			<div class="tf-single-template__two tf-single-action-btns-style2">
				<div class="tf-hero-hotel tf-popup-buttons">
					<a href="#">
						<?php echo wp_kses( $icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
					</a>
				</div>

				<div class="tf-popup-wrapper tf-hotel-popup">
					<div class="tf-popup-inner">
						<div class="tf-popup-body">
							<?php
							foreach ( $gallery_ids as $key => $gallery_item_id ) {
								$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
								?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="" class="tf-popup-image">
								<?php
							}
							?>
						</div>
						<div class="tf-popup-close">
							<i class="fa-solid fa-xmark"></i>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}
}
