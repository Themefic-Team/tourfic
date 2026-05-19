<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Tourfic\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Highlights Component
 * Shared markup for Elementor and Bricks Highlights widgets
 */
class Highlights {

	/**
	 * Static render method for Highlights component
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

        echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'tf_tours' === $post_type ) {
			self::tf_tour_highlight( $post_id, $settings, $builder );
		} elseif ( 'tf_apartment' === $post_type ) {
			self::tf_apartment_highlight( $post_id, $settings, $builder );
		}

        echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}

	/**
	 * Render tour highlights
	 *
	 * @param int   $post_id Post ID
	 * @param array $settings Widget settings
	 *
	 * @return void
	 */
	private static function tf_tour_highlight( $post_id, $settings, $builder ) {
		$meta       = get_post_meta( $post_id, 'tf_tours_opt', true );
		$highlights = ! empty( $meta['additional_information'] ) ? $meta['additional_information'] : '';
		$style      = ! empty( $settings['highlights_style'] ) ? $settings['highlights_style'] : 'style1';
		$container = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
		$wrapper_class = ! empty( $settings['wrapper_class'] ) ? $settings['wrapper_class'] : '';

		if ( 'style1' === $style && $highlights ) {
			?>
			<div class="tf-single-template__one tf-tour-highlights-style1 sp-0">
				<div class="tf-highlights-wrapper tf-box tf-template-section">
					<div class="tf-highlights-inner tf-flex">
						<div class="tf-highlights-icon">
							<?php if ( ! empty( $meta['hightlights_thumbnail'] ) ) : ?>
								<img src="<?php echo esc_url( $meta['hightlights_thumbnail'] ); ?>" alt="<?php esc_attr_e( 'Highlights Icon', 'tourfic' ); ?>" />
							<?php else : ?>
								<img src="<?php echo esc_url( TF_ASSETS_APP_URL ) . 'images/tour-highlights.png'; ?>" alt="<?php esc_attr_e( 'Highlights Icon', 'tourfic' ); ?>" />
							<?php endif; ?>
						</div>
						<div class="ft-highlights-details">
							<h2 class="tf-section-title"><?php echo ! empty( $meta['highlights-section-title'] ) ? esc_html( $meta['highlights-section-title'] ) : ''; ?></h2>
							<div class="highlights-list"><?php echo wp_kses_post( $highlights ); ?></div>
						</div>
					</div>
				</div>
			</div>
			<?php
		} elseif ( 'style2' === $style && $highlights ) {
			?>
			<?php echo !empty($builder) ? '<div class="tf-single-template__legacy tf-tour-highlights-style2 sp-0">' : ''; ?>
				<div class="tf-highlight-wrapper <?php echo esc_attr( $wrapper_class ); ?>">
					<?php echo $container === 'yes' ? '<div class="tf-container">' : ''; ?>
					<div class="tf-highlight-content">
						<div class="tf-highlight-item">
							<div class="tf-highlight-text">
								<h2 class="section-heading"><?php echo ! empty( $meta['highlights-section-title'] ) ? esc_html( $meta['highlights-section-title'] ) : ''; ?></h2>
								<div class="tf-highlight-description">
									<?php echo wp_kses_post( $highlights ); ?>
								</div>
							</div>
							<?php if ( ! empty( $meta['hightlights_thumbnail'] ) ) : ?>
								<div class="tf-highlight-image">
									<img src="<?php echo esc_url( $meta['hightlights_thumbnail'] ); ?>" alt="">
								</div>
							<?php endif; ?>
						</div>
					</div>
					<?php echo $container === 'yes' ? '</div>' : ''; ?>
				</div>
			<?php echo !empty($builder) ? '</div>' : ''; ?>
			<?php
		}
	}

	/**
	 * Render apartment highlights
	 *
	 * @param int   $post_id Post ID
	 * @param array $settings Widget settings
	 *
	 * @return void
	 */
	private static function tf_apartment_highlight( $post_id, $settings, $builder ) {
		$meta                 = get_post_meta( $post_id, 'tf_apartment_opt', true );
		$style                = ! empty( $settings['highlights_style'] ) ? $settings['highlights_style'] : 'style1';
		$tf_highlights_count  = count( Helper::tf_data_types( $meta['highlights'] ) );

		if ( 'style1' === $style && ! empty( Helper::tf_data_types( $meta['highlights'] ) ) ) {
			?>
			<div class="tf-single-template__two tf-apartment-highlights-style1">
				<div class="tf-overview-wrapper">
					<div class="<?php echo $tf_highlights_count > 4 ? esc_attr( 'tf-features-block-slides tf-slick-slider' ) : esc_attr( 'tf-features-block-wrapper' ); ?> tf-informations-secations">
						<?php
						foreach ( Helper::tf_data_types( $meta['highlights'] ) as $highlight ) :
							if ( empty( $highlight['title'] ) ) {
								continue;
							}
							?>
							<div class="tf-feature-block">
								<?php echo ! empty( $highlight['icon'] ) ? "<i class='" . esc_attr( $highlight['icon'] ) . "'></i>" : ''; ?>
								<div class="tf-feature-block-details">
									<h5><?php echo esc_html( $highlight['title'] ); ?></h5>
									<?php
									echo ! empty( $highlight['subtitle'] ) ? '<p>' . esc_html( $highlight['subtitle'] ) . '</p>' : '';
									?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<?php
		} elseif ( 'style2' === $style && ! empty( Helper::tf_data_types( $meta['highlights'] ) ) ) {
			?>
			<?php echo !empty($builder) ? '<div class="tf-single-template__legacy tf-apartment-highlights-style2 sp-0">' : ''; ?>
				<div class="tf-apt-highlights-wrapper">
					<?php if ( ! empty( $meta['highlights_title'] ) ) : ?>
						<h2 class="section-heading"><?php echo esc_html( $meta['highlights_title'] ); ?></h2>
					<?php endif; ?>

					<div class="tf-apt-highlights <?php echo count( Helper::tf_data_types( $meta['highlights'] ) ) > 3 ? 'tf-apt-highlights-slider tf-slick-slider' : ''; ?>">
						<?php
						foreach ( Helper::tf_data_types( $meta['highlights'] ) as $highlight ) :
							if ( empty( $highlight['title'] ) ) {
								continue;
							}
							?>
							<div class="tf-apt-highlight">
								<div class="tf-apt-highlight-top">
									<?php echo ! empty( $highlight['icon'] ) ? "<div class='tf-apt-highlight-icon'><i class='" . esc_attr( $highlight['icon'] ) . "'></i></div>" : ''; ?>
									<h4><?php echo esc_html( $highlight['title'] ); ?></h4>
								</div>
								<?php echo ! empty( $highlight['subtitle'] ) ? '<p>' . esc_html( $highlight['subtitle'] ) . '</p>' : ''; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php echo !empty($builder) ? '</div>' : ''; ?>
			<?php
		}
	}
}
