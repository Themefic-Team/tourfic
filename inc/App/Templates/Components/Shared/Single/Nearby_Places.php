<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Tourfic\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Nearby Places Component
 * Shared markup for Elementor and Bricks Nearby Places widgets
 */
class Nearby_Places {

	/**
	 * Static render method for Nearby Places component
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
			self::render_hotel_nearby_places( $settings, $builder );
		} elseif ( 'tf_apartment' === $post_type ) {
			self::render_apartment_nearby_places( $settings, $builder );
		}
	}

	/**
	 * Render hotel nearby places
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type
	 *
	 * @return void
	 */
	private static function render_hotel_nearby_places( $settings = [], $builder = '' ) {
		$post_id = get_the_ID();
		$style   = ! empty( $settings['nearby_places_style'] ) ? $settings['nearby_places_style'] : 'style1';
		$meta    = get_post_meta( $post_id, 'tf_hotels_opt', true );

		$places_section_title = ! empty( $meta['section-title'] ) ? $meta['section-title'] : '';
		$places               = ! empty( $meta['nearby-places'] ) ? Helper::tf_data_types( $meta['nearby-places'] ) : [];

		if( empty( $places ) || ! is_array( $places ) ) {
			return;
		}

		$wrapper_open = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

        echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'style1' === $style && is_array( $places ) && count( $places ) > 0 ) {
			?>
			<div class="tf-hotel-single-places tf-hotel-single-places-style1">
				<?php if ( ! empty( $places_section_title ) ) : ?>
					<h2 class="tf-title tf-section-title"><?php echo esc_html( $places_section_title ); ?></h2>
				<?php endif; ?>
				<ul>
					<?php foreach ( $places as $place ) {
						$place_icon = '<i class="' . esc_attr( $place['place-icon'] ) . '"></i>';
						?>
						<li>
							<span class="tf-place">
								<div class="tf-icon">
									<?php echo wp_kses_post( $place_icon ); ?>
								</div>
								<span class="tf-place-title">
									<?php echo esc_html( $place['place-title'] ); ?>
								</span>
							</span>
							<span><?php echo esc_html( $place['place-dist'] ); ?></span>
						</li>
					<?php } ?>
				</ul>
			</div>
			<?php
		} elseif ( 'style2' === $style && is_array( $places ) && count( $places ) > 0 ) {
			?>
			<div class="tf-whats-around tf-hotel-single-places-style2">
				<h3 class="tf-section-title"><?php echo ! empty( $meta['section-title'] ) ? esc_html( $meta['section-title'] ) : esc_html__( "What's around?", 'tourfic' ); ?></h3>
				<ul>
					<?php foreach ( $places as $place ) { ?>
						<li>
							<span class="tf-place">
								<span>
									<?php if ( ! empty( $place['place-icon'] ) ) { ?>
										<i class="<?php echo esc_attr( $place['place-icon'] ); ?>"></i>
									<?php } ?>
								</span>
								<span class="tf-place-title">
									<?php echo ! empty( $place['place-title'] ) ? esc_html( $place['place-title'] ) : ''; ?>
								</span>
							</span>
							<span><?php echo ! empty( $place['place-dist'] ) ? esc_html( $place['place-dist'] ) : ''; ?></span>
						</li>
					<?php } ?>
				</ul>
			</div>
			<?php
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}

	/**
	 * Render apartment nearby places
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type
	 *
	 * @return void
	 */
	private static function render_apartment_nearby_places( $settings = [], $builder = '' ) {
		$post_id = get_the_ID();
		$style   = ! empty( $settings['nearby_places_style'] ) ? $settings['nearby_places_style'] : 'style1';
		$meta    = get_post_meta( $post_id, 'tf_apartment_opt', true );
		$places  = ! empty( $meta['surroundings_places'] ) ? Helper::tf_data_types( $meta['surroundings_places'] ) : [];

		if ( empty( $places ) || ! is_array( $places ) ) {
			return;
		}

		$wrapper_open = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

        echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'style1' === $style && ! empty( $places ) ) {
			?>
			<div class="tf-whats-around tf-apartment-single-places-style1">
				<?php if ( ! empty( $meta['surroundings_sec_title'] ) ) : ?>
					<h3 class="tf-section-title"><?php echo esc_html( $meta['surroundings_sec_title'] ); ?></h3>
				<?php endif; ?>
				<ul>
					<?php foreach ( $places as $surroundings_place ) : ?>
						<?php if ( isset( $surroundings_place['places'] ) && ! empty( Helper::tf_data_types( $surroundings_place['places'] ) ) ) : ?>
							<?php foreach ( Helper::tf_data_types( $surroundings_place['places'] ) as $place ) : ?>
								<li>
									<span>
										<?php if ( ! empty( $surroundings_place['place_criteria_icon'] ) ) { ?>
											<i class="<?php echo esc_attr( $surroundings_place['place_criteria_icon'] ); ?>"></i>
										<?php } ?>
										<?php echo esc_html( $surroundings_place['place_criteria_label'] ); ?>
									</span>
									<span><?php echo esc_html( $place['place_name'] ); ?> (<?php echo esc_html( $place['place_distance'] ); ?>)</span>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php
		} elseif ( 'style2' === $style && ! empty( $places ) ) {
			?>
			<div class="about-location tf-apartment-single-places-style2">
				<?php if ( ! empty( $meta['surroundings_sec_title'] ) ) : ?>
					<h3 class="surroundings_sec_title"><?php echo esc_html( $meta['surroundings_sec_title'] ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $meta['surroundings_subtitle'] ) ) : ?>
					<p class="surroundings_subtitle"><?php echo esc_html( $meta['surroundings_subtitle'] ); ?></p>
				<?php endif; ?>

				<div class="tf-apartment-surronding-wrapper">
					<?php foreach ( $places as $surroundings_place ) : ?>
						<div class="tf-apartment-surronding-criteria">
							<div class="tf-apartment-surronding-criteria-label">
								<?php if ( ! empty( $surroundings_place['place_criteria_icon'] ) ) { ?>
									<i class="<?php echo esc_attr( $surroundings_place['place_criteria_icon'] ); ?>"></i>
								<?php } ?>
								<?php echo esc_html( $surroundings_place['place_criteria_label'] ); ?>
							</div>

							<?php if ( isset( $surroundings_place['places'] ) && ! empty( Helper::tf_data_types( $surroundings_place['places'] ) ) ) : ?>
								<ul class="tf-apartment-surronding-places">
									<?php foreach ( Helper::tf_data_types( $surroundings_place['places'] ) as $place ) : ?>
										<li>
											<span class="tf-place-name"><?php echo esc_html( $place['place_name'] ); ?></span>
											<span class="tf-place-distance"><?php echo esc_html( $place['place_distance'] ); ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}
}
