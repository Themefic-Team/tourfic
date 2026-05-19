<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Tourfic\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Enquiry Component
 * Shared markup for Elementor and Bricks Enquiry widgets
 */
class Enquiry {

	/**
	 * Static render method for Enquiry component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();
		$style     = ! empty( $settings['enquiry_style'] ) ? $settings['enquiry_style'] : 'style1';
		$icon_type = ! empty( $settings['icon_type'] ) ? $settings['icon_type'] : 'rounded';
        $wrapper_open = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';
        $wrapper_class = ! empty( $settings['wrapper_class'] ) ? $settings['wrapper_class'] : '';
        $button_class = ! empty( $settings['button_class'] ) ? $settings['button_class'] : '';
        $container = ! empty( $settings['container'] ) ? $settings['container'] : 'no';

		if ( 'tf_hotel' === $post_type ) {
			$meta = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$tf_enquiry_section_status = ! empty( $meta['h-enquiry-section'] ) ? $meta['h-enquiry-section'] : '';
			$tf_enquiry_section_icon = ! empty( $meta['h-enquiry-option-icon'] ) ? esc_html( $meta['h-enquiry-option-icon'] ) : '';
			$tf_enquiry_section_title = ! empty( $meta['h-enquiry-option-title'] ) ? esc_html( $meta['h-enquiry-option-title'] ) : '';
			$tf_enquiry_section_cont = ! empty( $meta['h-enquiry-option-content'] ) ? esc_html( $meta['h-enquiry-option-content'] ) : '';
			$tf_enquiry_section_button = ! empty( $meta['h-enquiry-option-btn'] ) ? esc_html( $meta['h-enquiry-option-btn'] ) : '';
		} elseif ( 'tf_tours' === $post_type ) {
			$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
			$tf_enquiry_section_status = ! empty( $meta['t-enquiry-section'] ) ? $meta['t-enquiry-section'] : '';
			$tf_enquiry_section_icon = ! empty( $meta['t-enquiry-option-icon'] ) ? esc_html( $meta['t-enquiry-option-icon'] ) : '';
			$tf_enquiry_section_title = ! empty( $meta['t-enquiry-option-title'] ) ? esc_html( $meta['t-enquiry-option-title'] ) : '';
			$tf_enquiry_section_cont = ! empty( $meta['t-enquiry-option-content'] ) ? esc_html( $meta['t-enquiry-option-content'] ) : '';
			$tf_enquiry_section_button = ! empty( $meta['t-enquiry-option-btn'] ) ? esc_html( $meta['t-enquiry-option-btn'] ) : '';
		} elseif ( 'tf_apartment' === $post_type ) {
			$meta = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$tf_enquiry_section_status = ! empty( $meta['enquiry-section'] ) ? $meta['enquiry-section'] : '';
			$tf_enquiry_section_icon = ! empty( $meta['apartment-enquiry-icon'] ) ? $meta['apartment-enquiry-icon'] : '';
			$tf_enquiry_section_title = ! empty( $meta['enquiry-title'] ) ? $meta['enquiry-title'] : '';
			$tf_enquiry_section_cont = ! empty( $meta['enquiry-content'] ) ? $meta['enquiry-content'] : '';
			$tf_enquiry_section_button = ! empty( $meta['enquiry-btn'] ) ? $meta['enquiry-btn'] : '';
		} else {
			return;
		}

        echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'style1' === $style && '1' == $tf_enquiry_section_status ) :
			?>
			<div class="tf-single-enquiry-style-1 tf-ask-enquiry tf-icon-<?php echo esc_attr( $icon_type ); ?> <?php echo esc_attr( $wrapper_class ); ?>">
				<?php if ( ! empty( $tf_enquiry_section_icon ) ) { ?>
					<i class="<?php echo esc_attr( $tf_enquiry_section_icon ); ?>" aria-hidden="true"></i>
					<?php
				}
				if ( ! empty( $tf_enquiry_section_title ) ) {
					?>
					<h3 class="tf-enquiry-title"><?php echo wp_kses_post( $tf_enquiry_section_title ); ?></h3>
					<?php
				}
				if ( ! empty( $tf_enquiry_section_cont ) ) {
					?>
					<p class="tf-enquiry-content"><?php echo wp_kses_post( $tf_enquiry_section_cont ); ?></p>
					<?php
				}
				if ( ! empty( $tf_enquiry_section_button ) ) {
					?>
					<div class="tf-btn-wrap">
						<a href="javaScript:void(0);" data-target="#tf-ask-modal" class="tf-modal-btn tf_btn <?php echo esc_attr( $button_class ); ?>">
							<span><?php echo esc_html( $tf_enquiry_section_button ); ?></span>
						</a>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		elseif ( 'style2' === $style && '1' == $tf_enquiry_section_status ) :
			?>
			<div class="tf-single-enquiry-style-2 apartment-question tf-icon-<?php echo esc_attr( $icon_type ); ?>">
                <?php echo 'yes' === $container ? '<div class="tf-container"><div class="apartment-qa-wrapper">' : ''; ?>
				<div class="tf-question-left">
					<?php if ( ! empty( $tf_enquiry_section_icon ) ) : ?>
						<div class="tf-apartment-question-icon">
							<i class="<?php echo esc_attr( $tf_enquiry_section_icon ); ?>" aria-hidden="true"></i>
						</div>
					<?php endif; ?>
					<div class="tf-question-left-inner">
						<div class="default-enquiry-title-section">
							<?php if ( ! empty( $tf_enquiry_section_title ) ) { ?>
								<h2 class="tf-enquiry-title"><?php echo esc_html( $tf_enquiry_section_title ); ?></h2>
							<?php } ?>
						</div>
						<?php if ( ! empty( $tf_enquiry_section_cont ) ) { ?>
							<p class="tf-enquiry-content"><?php echo wp_kses_post( $tf_enquiry_section_cont ); ?></p>
						<?php } ?>
					</div>
				</div>
				<?php if ( ! empty( $tf_enquiry_section_button ) ) { ?>
					<div class="tf-btn-wrap">
						<a href="#" data-target="#tf-ask-modal" class="tf-modal-btn tf_btn tf_btn_large <?php echo esc_attr( $button_class ); ?>">
							<span><?php echo wp_kses_post( $tf_enquiry_section_button ); ?></span>
						</a>
					</div>
				<?php } ?>
                <?php echo 'yes' === $container ? '</div></div>' : ''; ?>
			</div>
			<?php
		endif;

        echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}
}
