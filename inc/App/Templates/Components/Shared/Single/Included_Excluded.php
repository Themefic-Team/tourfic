<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Tourfic\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Included Excluded Component
 */
class Included_Excluded {

	public static function render( $settings = [], $builder = '' ) {
		$post_id       = get_the_ID();
		$post_type     = get_post_type();
		$wrapper  = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';
		$wrapper_open  = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';
		$style         = ! empty( $settings['included_excluded_style'] ) ? $settings['included_excluded_style'] : 'style1';

		if ( 'tf_tours' === $post_type ) {
			$meta           = get_post_meta( $post_id, 'tf_tours_opt', true );
			$inc            = ! empty( Helper::tf_data_types( $meta['inc'] ?? [] ) ) ? Helper::tf_data_types( $meta['inc'] ?? [] ) : null;
			$exc            = ! empty( Helper::tf_data_types( $meta['exc'] ?? [] ) ) ? Helper::tf_data_types( $meta['exc'] ?? [] ) : null;
			$inc_icon       = ! empty( $meta['inc_icon'] ) ? $meta['inc_icon'] : null;
			$exc_icon       = ! empty( $meta['exc_icon'] ) ? $meta['exc_icon'] : null;
			$custom_inc_icon = ! empty( $inc_icon ) ? 'custom-inc-icon' : '';
			$custom_exc_icon = ! empty( $exc_icon ) ? 'custom-exc-icon' : '';
			$inc_exc_bg     = ! empty( $meta['include-exclude-bg'] ) ? $meta['include-exclude-bg'] : '';
		} elseif ( 'tf_carrental' === $post_type ) {
			$meta           = get_post_meta( $post_id, 'tf_carrental_opt', true );
			$inc_exc_status = ! empty( $meta['inc_exc_section'] ) ? $meta['inc_exc_section'] : '';
			$includes       = ! empty( $meta['inc'] ) ? $meta['inc'] : '';
			$include_icon   = ! empty( $meta['inc_icon'] ) ? $meta['inc_icon'] : '';
			$excludes       = ! empty( $meta['exc'] ) ? $meta['exc'] : '';
			$exclude_icon   = ! empty( $meta['exc_icon'] ) ? $meta['exc_icon'] : '';
			$inc_sec_title  = ! empty( $meta['inc_sec_title'] ) ? $meta['inc_sec_title'] : '';
			$exc_sec_title  = ! empty( $meta['exc_sec_title'] ) ? $meta['exc_sec_title'] : '';
		} else {
			return;
		}

		echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'tf_tours' === $post_type && 'style1' === $style && ( $inc || $exc ) ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__one tf-single-tour-inc-exc-style1 sp-0">' : ''; ?>
				<div class="tf-inex-wrapper tf-template-section">
					<div class="tf-inex-inner tf-flex tf-flex-gap-24">
						<?php if ( $inc ) : ?>
							<div class="tf-inex tf-tour-include tf-box">
								<h2 class="tf-section-title"><?php esc_html_e( 'Included', 'tourfic' ); ?></h2>
								<ul class="tf-list">
									<?php foreach ( $inc as $val ) : ?>
										<li>
											<i class="<?php echo ! empty( $inc_icon ) ? esc_attr( $inc_icon ) : 'fa-regular fa-circle-check'; ?>"></i>
											<?php echo wp_kses_post( $val['inc'] ?? '' ); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
						<?php if ( $exc ) : ?>
							<div class="tf-inex tf-tour-exclude tf-box">
								<h2 class="tf-section-title"><?php esc_html_e( 'Excluded', 'tourfic' ); ?></h2>
								<ul class="tf-list">
									<?php foreach ( $exc as $val ) : ?>
										<li>
											<i class="<?php echo ! empty( $exc_icon ) ? esc_attr( $exc_icon ) : 'fa-regular fa-circle-check'; ?>"></i>
											<?php echo wp_kses_post( $val['exc'] ?? '' ); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( 'tf_tours' === $post_type && 'style2' === $style && ( $inc || $exc ) ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__two tf-single-tour-inc-exc-style2">' : ''; ?>
				<div class="tf-include-exclude-wrapper">
					<h2 class="tf-section-title"><?php esc_html_e( 'Include/Exclude', 'tourfic' ); ?></h2>
					<div class="tf-include-exclude-innter">
						<?php if ( $inc ) : ?>
							<div class="tf-include">
								<ul>
									<?php foreach ( $inc as $val ) : ?>
										<li>
											<i class="<?php echo ! empty( $inc_icon ) ? esc_attr( $inc_icon ) : 'fa-regular fa-circle-check'; ?>"></i>
											<?php echo wp_kses_post( $val['inc'] ?? '' ); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
						<?php if ( $exc ) : ?>
							<div class="tf-exclude">
								<ul>
									<?php foreach ( $exc as $val ) : ?>
										<li>
											<i class="<?php echo ! empty( $exc_icon ) ? esc_attr( $exc_icon ) : 'fa-regular fa-circle-check'; ?>"></i>
											<?php echo wp_kses_post( $val['exc'] ?? '' ); ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( 'tf_tours' === $post_type && 'style3' === $style && ( $inc || $exc ) ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__legacy tf-single-tour-inc-exc-legacy">' : ''; ?>
				<div class="tf-inc-exc-wrapper sp-70" style="background-image: url(<?php echo esc_url( $inc_exc_bg ); ?>);">
					<div class="tf-container">
						<div class="tf-inc-exc-content">
							<?php if ( $inc ) : ?>
								<div class="tf-include-section tf-inc-exc-card <?php echo esc_attr( $custom_inc_icon ); ?>">
									<h2 class="tf-section-title"><?php esc_html_e( 'Included', 'tourfic' ); ?></h2>
									<ul>
										<?php foreach ( $inc as $val ) : ?>
											<li><i class="<?php echo esc_attr( $inc_icon ); ?>"></i><?php echo wp_kses_post( $val['inc'] ?? '' ); ?></li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>
							<?php if ( $exc ) : ?>
								<div class="tf-exclude-section tf-inc-exc-card <?php echo esc_attr( $custom_exc_icon ); ?>">
									<h2 class="tf-section-title"><?php esc_html_e( 'Excluded', 'tourfic' ); ?></h2>
									<ul>
										<?php foreach ( $exc as $val ) : ?>
											<li><i class="<?php echo esc_attr( $exc_icon ); ?>"></i><?php echo wp_kses_post( $val['exc'] ?? '' ); ?></li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( 'tf_carrental' === $post_type && 'style1' === $style && ! empty( $inc_exc_status ) && ( $includes || $excludes ) ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-car-inc-exc-section" id="tf-inc-exc">' : ''; ?>
				<div class="tf-inc-exe tf-flex tf-flex-gap-16">
					<?php if ( ! empty( $includes ) ) : ?>
						<div class="tf-inc-list">
							<?php if ( ! empty( $inc_sec_title ) ) : ?>
								<h3 class="tf-section-title"><?php echo esc_html( $inc_sec_title ); ?></h3>
							<?php endif; ?>
							<ul class="tf-flex tf-flex-gap-16 tf-flex-direction-column">
								<?php foreach ( $includes as $inc_item ) : ?>
									<li class="tf-flex tf-flex-align-center tf-flex-gap-8">
										<i class="<?php echo ! empty( $include_icon ) ? esc_attr( $include_icon ) : 'ri-check-double-line'; ?>"></i>
										<?php echo ! empty( $inc_item['title'] ) ? esc_html( $inc_item['title'] ) : ''; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $excludes ) ) : ?>
						<div class="tf-exc-list">
							<?php if ( ! empty( $exc_sec_title ) ) : ?>
								<h3 class="tf-section-title"><?php echo esc_html( $exc_sec_title ); ?></h3>
							<?php endif; ?>
							<ul class="tf-flex tf-flex-gap-16 tf-flex-direction-column">
								<?php foreach ( $excludes as $exc_item ) : ?>
									<li class="tf-flex tf-flex-align-center tf-flex-gap-8">
										<i class="<?php echo ! empty( $exclude_icon ) ? esc_attr( $exclude_icon ) : 'ri-close-circle-line'; ?>"></i>
										<?php echo ! empty( $exc_item['title'] ) ? esc_html( $exc_item['title'] ) : ''; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}
}
