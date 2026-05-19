<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Elementor\Icons_Manager;
use Tourfic\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global FAQ Component
 */
class FAQ {

	public static function render( $settings = [], $builder = '' ) {
		$post_id          = get_the_ID();
		$post_type        = get_post_type();
		$style            = ! empty( $settings['faq_style'] ) ? $settings['faq_style'] : 'style1';
		$description_key  = 'description';
		$is_icon_right    = isset( $settings['tf_faq_icon_postion'] ) ? $settings['tf_faq_icon_postion'] : 'right';
		$item_divider     = Helper::get_switcher_value( $settings, 'tf_faq_item_divider', 'yes', $builder );
		$wrapper_open     = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close    = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';
        $wrapper_class    = ! empty( $settings['wrapper_class'] ) ? $settings['wrapper_class'] : '';
        $wrapper          = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
        $container        = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
        $show_title       = ! empty( $settings['show_title'] ) ? $settings['show_title'] : 'yes';
        $show_description = ! empty( $settings['show_description'] ) ? $settings['show_description'] : 'no';
		$label_tag		 = ! empty( $settings['label_tag'] ) ? $settings['label_tag'] : 'span';

		$faqs = [];
		$title = '';
		$desc = '';
		$meta = [];

		if ( 'tf_hotel' === $post_type ) {
			$meta  = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$faqs  = ! empty( Helper::tf_data_types( $meta['faq'] ) ) ? Helper::tf_data_types( $meta['faq'] ) : [];
			$title = ! empty( $meta['faq-section-title'] ) ? esc_html( $meta['faq-section-title'] ) : '';
		} elseif ( 'tf_tours' === $post_type ) {
			$meta            = get_post_meta( $post_id, 'tf_tours_opt', true );
			$faqs            = ! empty( Helper::tf_data_types( $meta['faqs'] ) ) ? Helper::tf_data_types( $meta['faqs'] ) : [];
			$title           = ! empty( $meta['faq-section-title'] ) ? esc_html( $meta['faq-section-title'] ) : '';
			$description_key = 'desc';
		} elseif ( 'tf_apartment' === $post_type ) {
			$meta  = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$faqs  = ! empty( Helper::tf_data_types( $meta['faq'] ) ) ? Helper::tf_data_types( $meta['faq'] ) : [];
			$title = ! empty( $meta['faq_title'] ) ? esc_html( $meta['faq_title'] ) : '';
			$desc  = ! empty( $meta['faq_desc'] ) ? esc_html( $meta['faq_desc'] ) : '';
		} elseif ( 'tf_carrental' === $post_type ) {
			$meta  = get_post_meta( $post_id, 'tf_carrental_opt', true );
			$faqs  = ! empty( Helper::tf_data_types( $meta['faq'] ) ) ? Helper::tf_data_types( $meta['faq'] ) : [];
			$title = ! empty( $meta['faq_sec_title'] ) ? esc_html( $meta['faq_sec_title'] ) : '';
		} else {
			return;
		}

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'style1' === $style && ! empty( $faqs ) ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-faq-section tf-single-faq-style1 ' . ( ! empty( $wrapper_class ) ? esc_attr( $wrapper_class ) : '' ) . '">' : ''; ?>
				<?php echo ( 'yes' === $show_title && ! empty( $title ) ) ? '<h2 class="tf-title tf-section-title">' . esc_html( $title ) . '</h2>' : ''; ?>
				<?php echo ( 'yes' === $show_description && ! empty( $desc ) ) ? '<p>' . wp_kses_post( $desc ) . '</p>' : ''; ?>

				<div class="tf-faq-inner">
					<?php $faq_key = 1; ?>
					<?php foreach ( $faqs as $faq ) : ?>
						<div class="tf-faq-single <?php echo 1 === $faq_key ? esc_attr( 'active' ) : ''; ?> <?php echo $item_divider == 'yes' ? esc_attr( 'has-devider' ) : ''; ?>">
							<div class="tf-faq-single-inner">
								<div class="tf-faq-collaps tf-faq-head tf-flex tf-flex-align-center tf-flex-space-bttn <?php echo 1 === $faq_key ? esc_attr( 'active' ) : ''; ?> <?php echo $is_icon_right == 'right' ? esc_attr( 'tf-faq-icon-right' ) : ''; ?>">
									<?php if ( 'right' != $is_icon_right ) : ?>
										<div class="faq-icon"><?php self::tf_faq_toggle_icon( $settings, '<i class="fa-solid fa-plus"></i>', '<i class="fa-solid fa-minus"></i>' ); ?></div>
									<?php endif; ?>

									<?php echo sprintf( '<%1$s class="tf-faq-label">%2$s</%1$s>', esc_html( $label_tag ), ! empty( $faq['title'] ) ? esc_html( $faq['title'] ) : '' ); ?>

									<?php if ( 'right' == $is_icon_right ) : ?>
										<div class="faq-icon"><?php self::tf_faq_toggle_icon( $settings, '<i class="fa-solid fa-plus"></i>', '<i class="fa-solid fa-minus"></i>' ); ?></div>
									<?php endif; ?>
								</div>
								<div class="tf-faq-content" style="<?php echo 1 === $faq_key ? esc_attr( 'display: block;' ) : ''; ?>">
									<p><?php echo ! empty( $faq[ $description_key ] ) ? wp_kses_post( $faq[ $description_key ] ) : ''; ?></p>
								</div>
							</div>
						</div>
						<?php $faq_key++; ?>
					<?php endforeach; ?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( 'style2' === $style && ! empty( $faqs ) ) {
			$faqfirstArray = $faqs;
			$faqsecondArray = [];
			if ( count( $faqs ) >= 2 ) {
				$faqchunks     = array_chunk( $faqs, ceil( count( $faqs ) / 2 ), true );
				$faqfirstArray = $faqchunks[0];
				$faqsecondArray = ! empty( $faqchunks[1] ) ? $faqchunks[1] : [];
			}
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__two tf-questions-wrapper tf-single-faq-style2 ' . ( ! empty( $wrapper_class ) ? esc_attr( $wrapper_class ) : '' ) . '" id="tf-hotel-faq">' : ''; ?>
				<?php echo ( 'yes' === $show_title && ! empty( $title ) ) ? '<h2 class="tf-section-title">' . esc_html( $title ) . '</h2>' : ''; ?>
				<?php echo ( 'yes' === $show_description && ! empty( $desc ) ) ? '<p>' . wp_kses_post( $desc ) . '</p>' : ''; ?>

				<div class="tf-questions">
					<?php if ( ! empty( $faqfirstArray ) ) : ?>
						<div class="tf-questions-col">
							<?php foreach ( $faqfirstArray as $key => $faq ) : ?>
								<div class="tf-question <?php echo 0 === $key ? esc_attr( 'tf-active' ) : ''; ?>">
									<div class="tf-faq-head tf-flex tf-flex-align-center tf-flex-space-bttn <?php echo $is_icon_right == 'right' ? esc_attr( 'tf-faq-icon-right' ) : ''; ?>">
										<?php if ( 'right' != $is_icon_right ) : ?>
											<div class="faq-icon"><?php self::tf_faq_toggle_icon( $settings, '<i class="fa-solid fa-chevron-down"></i>' ); ?></div>
										<?php endif; ?>
										<?php echo sprintf( '<%1$s class="tf-faq-label">%2$s</%1$s>', esc_html( $label_tag ), ! empty( $faq['title'] ) ? esc_html( $faq['title'] ) : '' ); ?>
										<?php if ( 'right' == $is_icon_right ) : ?>
											<div class="faq-icon"><?php self::tf_faq_toggle_icon( $settings, '<i class="fa-solid fa-chevron-down"></i>' ); ?></div>
										<?php endif; ?>
									</div>
									<div class="tf-question-desc" style="<?php echo 0 === $key ? esc_attr( 'display: block;' ) : ''; ?>">
										<?php echo ! empty( $faq[ $description_key ] ) ? wp_kses_post( $faq[ $description_key ] ) : ''; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $faqsecondArray ) ) : ?>
						<div class="tf-questions-col">
							<?php foreach ( $faqsecondArray as $key => $faq ) : ?>
								<div class="tf-question <?php echo 0 === $key ? esc_attr( 'tf-active' ) : ''; ?>">
									<div class="tf-faq-head tf-flex tf-flex-align-center tf-flex-space-bttn <?php echo $is_icon_right == 'right' ? esc_attr( 'tf-faq-icon-right' ) : ''; ?>">
										<?php if ( 'right' != $is_icon_right ) : ?>
											<div class="faq-icon"><?php self::tf_faq_toggle_icon( $settings, '<i class="fa-solid fa-chevron-down"></i>' ); ?></div>
										<?php endif; ?>
										<?php echo sprintf( '<%1$s class="tf-faq-label">%2$s</%1$s>', esc_html( $label_tag ), ! empty( $faq['title'] ) ? esc_html( $faq['title'] ) : '' ); ?>
										<?php if ( 'right' == $is_icon_right ) : ?>
											<div class="faq-icon"><?php self::tf_faq_toggle_icon( $settings, '<i class="fa-solid fa-chevron-down"></i>' ); ?></div>
										<?php endif; ?>
									</div>
									<div class="tf-question-desc" style="<?php echo 0 === $key ? esc_attr( 'display: block;' ) : ''; ?>">
										<?php echo ! empty( $faq[ $description_key ] ) ? wp_kses_post( $faq[ $description_key ] ) : ''; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( 'style3' === $style && ! empty( $faqs ) ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-faq-section tf-car-faq-section tf-single-faq-style3 ' . ( ! empty( $wrapper_class ) ? esc_attr( $wrapper_class ) : '' ) . '">' : ''; ?>
				<?php echo ( 'yes' === $show_title && ! empty( $title ) ) ? '<h3 class="tf-title tf-section-title">' . esc_html( $title ) . '</h3>' : ''; ?>
				<?php echo ( 'yes' === $show_description && ! empty( $desc ) ) ? '<p>' . wp_kses_post( $desc ) . '</p>' : ''; ?>

				<?php $faq_key = 1; ?>
				<?php foreach ( $faqs as $faq ) : ?>
					<div class="tf-faq-col tf-faq-single <?php echo 1 === $faq_key ? esc_attr( 'active' ) : ''; ?>">
						<?php if ( ! empty( $faq['title'] ) ) : ?>
							<div class="tf-faq-head <?php echo 1 === $faq_key ? esc_attr( 'active' ) : ''; ?> <?php echo $is_icon_right == 'right' ? esc_attr( 'tf-faq-icon-right' ) : ''; ?>">
								<span class="tf-flex tf-flex-space-bttn tf-flex-align-center">
									<?php if ( 'right' != $is_icon_right ) { self::tf_faq_toggle_icon( $settings, '<i class="fa-solid fa-chevron-down"></i>', '', 'no' ); } ?>
									<?php echo sprintf( '<%1$s class="tf-faq-label">%2$s</%1$s>', esc_html( $label_tag ), ! empty( $faq['title'] ) ? esc_html( $faq['title'] ) : '' ); ?>
									<?php if ( 'right' == $is_icon_right ) { self::tf_faq_toggle_icon( $settings, '<i class="fa-solid fa-chevron-down"></i>', '', 'no' ); } ?>
								</span>
							</div>
						<?php endif; ?>

						<div class="tf-question-desc tf-faq-content" style="<?php echo 1 === $faq_key ? esc_attr( 'display: block;' ) : ''; ?>">
							<?php echo ! empty( $faq[ $description_key ] ) ? wp_kses_post( $faq[ $description_key ] ) : ''; ?>
						</div>
					</div>
					<?php $faq_key++; ?>
				<?php endforeach; ?>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( 'style4' === $style && ! empty( $faqs ) ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__legacy tf-faq-wrapper tf-apartment-faq tf-single-faq-style4' . ( ! empty( $wrapper_class ) ? esc_attr( $wrapper_class ) : '' ) . '">' : ''; ?>
				<?php echo 'yes' == $container ? '<div class="tf-container">' : ''; ?>
				<div class="tf-faq-sec-title">
					<?php echo ( 'yes' === $show_title && ! empty( $title ) ) ? '<h2 class="section-heading">' . esc_html( $title ) . '</h2>' : ''; ?>
					<?php echo ( 'yes' === $show_description && ! empty( $desc ) ) ? '<p>' . wp_kses_post( $desc ) . '</p>' : ''; ?>
				</div>

				<div class="tf-faq-content-wrapper">
					<div class="tf-faq-items-wrapper">
						<?php foreach ( $faqs as $key => $faq ) : ?>
							<div id="tf-faq-item">
								<?php if ( ! empty( $faq['title'] ) ) : ?>
									<div class="tf-faq-title tf-faq-head <?php echo 0 === $key ? esc_attr( 'active' ) : ''; ?> <?php echo $is_icon_right == 'right' ? esc_attr( 'tf-faq-icon-right' ) : ''; ?>">
										<?php if ( 'right' != $is_icon_right ) { self::tf_faq_toggle_icon( $settings, '<svg class="tf-faq-plus" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none"><rect y="9" width="19" height="1" fill="#2979FF"/><rect x="9" width="1" height="19" fill="#2979FF"/></svg>', '<svg class="tf-faq-minus" xmlns="http://www.w3.org/2000/svg" width="19" height="1" viewBox="0 0 19 1" fill="none"><rect width="19" height="1" fill="#2979FF"/></svg>', 'no' ); } ?>
										<?php echo sprintf( '<%1$s class="tf-faq-label">%2$s</%1$s>', esc_html( $label_tag ), ! empty( $faq['title'] ) ? esc_html( $faq['title'] ) : '' ); ?>
										<?php if ( 'right' == $is_icon_right ) { self::tf_faq_toggle_icon( $settings, '<svg class="tf-faq-plus" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none"><rect y="9" width="19" height="1" fill="#2979FF"/><rect x="9" width="1" height="19" fill="#2979FF"/></svg>', '<svg class="tf-faq-minus" xmlns="http://www.w3.org/2000/svg" width="19" height="1" viewBox="0 0 19 1" fill="none"><rect width="19" height="1" fill="#2979FF"/></svg>', 'no' ); } ?>
									</div>
								<?php endif; ?>

								<div class="tf-faq-desc" <?php echo 0 === $key ? 'style="display: block;"' : ''; ?>>
									<?php echo ! empty( $faq[ $description_key ] ) ? wp_kses_post( $faq[ $description_key ] ) : ''; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php echo 'yes' == $container ? '</div>' : ''; ?>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}

	private static function tf_faq_toggle_icon( $settings, $default_open_icon = '', $default_close_icon = '', $wrapper = 'yes' ) {
		$builder = !empty( $settings['builder'] ) ? $settings['builder'] : '';

		echo ('yes' === $wrapper || !empty( $builder )) ? '<span class="tf-faq-open-icon">' : '';
		echo self::render_icon_html( ! empty( $settings['open_icon'] ) ? $settings['open_icon'] : [], $default_open_icon );
		echo ('yes' === $wrapper || !empty( $builder )) ? '</span>' : '';

		echo ('yes' === $wrapper || !empty( $builder )) ? '<span class="tf-faq-close-icon">' : '';
		echo self::render_icon_html( ! empty( $settings['close_icon'] ) ? $settings['close_icon'] : [], $default_close_icon );
		echo ('yes' === $wrapper || !empty( $builder )) ? '</span>' : '';
	}

	private static function render_icon_html( $icon_setting, $default_icon = '' ) {
		if ( empty( $icon_setting ) || ! is_array( $icon_setting ) ) {
			return $default_icon;
		}

		if ( ! empty( $icon_setting['icon'] ) ) {
			return '<i class="' . esc_attr( $icon_setting['icon'] ) . ' fa-toggle"></i>';
		}

		if ( ! empty( $icon_setting['value'] ) && is_string( $icon_setting['value'] ) ) {
			return '<i class="' . esc_attr( $icon_setting['value'] ) . ' fa-toggle"></i>';
		}

		if ( class_exists( '\\Elementor\\Icons_Manager' ) ) {
			ob_start();
			Icons_Manager::render_icon( $icon_setting, [ 'aria-hidden' => 'true', 'class' => 'fa-toggle' ] );
			$rendered = ob_get_clean();
			return ! empty( $rendered ) ? $rendered : $default_icon;
		}

		return $default_icon;
	}
}
