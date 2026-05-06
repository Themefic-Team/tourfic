<?php

namespace Tourfic\App\Templates\Components\Tour\Single;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single Tour Info Cards Component
 * Shared markup for Elementor and Bricks Tour Info Cards widgets
 */
class Tour_Info_Cards {

	/**
	 * Static render method for Tour Info Cards component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();

		if ( 'tf_tours' !== $post_type ) {
			return;
		}

		$meta                = get_post_meta( $post_id, 'tf_tours_opt', true );
		$tour_duration       = ! empty( $meta['duration'] ) ? $meta['duration'] : '';
		$duration_time       = ! empty( $meta['duration_time'] ) ? $meta['duration_time'] : 'Day';
		$info_tour_type      = ! empty( $meta['tour_types'] ) ? $meta['tour_types'] : [];
		$group_size          = ! empty( $meta['group_size'] ) ? $meta['group_size'] : '';
		$language            = ! empty( $meta['language'] ) ? $meta['language'] : '';
		$night               = ! empty( $meta['night'] ) ? $meta['night'] : false;
		$night_count         = ! empty( $meta['night_count'] ) ? $meta['night_count'] : '';
		$tour_duration_icon  = ! empty( $meta['tf-tour-duration-icon'] ) ? $meta['tf-tour-duration-icon'] : 'ri-history-line';
		$tour_type_icon      = ! empty( $meta['tf-tour-type-icon'] ) ? $meta['tf-tour-type-icon'] : 'ri-menu-unfold-line';
		$tour_group_icon     = ! empty( $meta['tf-tour-group-icon'] ) ? $meta['tf-tour-group-icon'] : 'ri-team-line';
		$tour_lang_icon      = ! empty( $meta['tf-tour-lang-icon'] ) ? $meta['tf-tour-lang-icon'] : 'ri-global-line';

		$grid_column = isset( $settings['grid_column'] ) ? absint( $settings['grid_column'] ) : 4;
		$style       = ! empty( $settings['info_cards_style'] ) ? $settings['info_cards_style'] : 'style1';
		$wrapper_open = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

        echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( 'style1' === $style && ( $tour_duration || $info_tour_type || $group_size || $language ) ) {
			?>
			<div class="tf-trip-feature-blocks tf-template-section tf-info-cards-style1">
				<div class="tf-features-block-inner tf-flex tf-flex-space-bttn tf-flex-gap-16 tf-grid-<?php echo esc_attr( $grid_column ); ?>">
					<?php if ( $tour_duration ) { ?>
						<div class="tf-feature-block tf-flex tf-flex-gap-8 tf-first">
							<div class="tf-feature-block-icon">
								<i class="<?php echo esc_attr( $tour_duration_icon ); ?>"></i>
							</div>
							<div class="tf-feature-block-details">
								<h5><?php echo esc_html__( 'Duration', 'tourfic' ); ?></h5>
								<p><?php echo esc_html( $tour_duration ); ?>
									<?php
									if ( $tour_duration > 1 ) {
										$dur_string         = 's';
										$_duration_time     = $duration_time . $dur_string;
									} else {
										$_duration_time = $duration_time;
									}
									echo " " . esc_html( $_duration_time );

									if ( $night ) {
										echo '<span>';
										echo esc_html( ', ' . $night_count );
										if ( ! empty( $night_count ) ) {
											if ( $night_count > 1 ) {
												echo esc_html__( ' Nights', 'tourfic' );
											} else {
												echo esc_html__( ' Night', 'tourfic' );
											}
										}
										echo '</span>';
									}
									?>
								</p>
							</div>
						</div>
					<?php } ?>
					<?php
					if ( is_array( $info_tour_type ) && array_filter( $info_tour_type ) ) {
						if ( 'string' === gettype( $info_tour_type ) ) {
							$info_tour_type = ucfirst( esc_html( $info_tour_type ) );
						} elseif ( 'array' === gettype( $info_tour_type ) ) {
							$tour_types = [];
							$types      = ! empty( get_the_terms( $post_id, 'tour_type' ) ) ? get_the_terms( $post_id, 'tour_type' ) : '';
							if ( ! empty( $types ) ) {
								foreach ( $types as $type ) {
									$tour_types[] = $type->name;
								}
							}
							$info_tour_type = implode( ', ', $tour_types );
						}
						?>
						<div class="tf-feature-block tf-flex tf-flex-gap-8  tf-second">
							<div class="tf-feature-block-icon">
								<i class="<?php echo esc_attr( $tour_type_icon ); ?>"></i>
							</div>
							<div class="tf-feature-block-details">
								<h5><?php echo esc_html__( 'Tour Type', 'tourfic' ); ?></h5>
								<p><?php echo esc_html( $info_tour_type ); ?></p>
							</div>
						</div>
					<?php } ?>
					<?php if ( $group_size ) { ?>
						<div class="tf-feature-block tf-flex tf-flex-gap-8  tf-third">
							<div class="tf-feature-block-icon">
								<i class="<?php echo esc_attr( $tour_group_icon ); ?>"></i>
							</div>
							<div class="tf-feature-block-details">
								<h5><?php echo esc_html__( 'Group Size', 'tourfic' ); ?></h5>
								<p><?php echo esc_html( $group_size ); ?></p>
							</div>
						</div>
					<?php } ?>
					<?php if ( $language ) { ?>
						<div class="tf-feature-block tf-flex tf-flex-gap-8  tf-tourth">
							<div class="tf-feature-block-icon">
								<i class="<?php echo esc_attr( $tour_lang_icon ); ?>"></i>
							</div>
							<div class="tf-feature-block-details">
								<h5><?php echo esc_html__( 'Language', 'tourfic' ); ?></h5>
								<p><?php echo esc_html( $language ); ?></p>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php
		} elseif ( 'style2' === $style && ( $tour_duration || $info_tour_type || $group_size || $language ) ) {
			?>
			<div class="tf-square-block tf-info-cards-style2">
				<div class="tf-square-block-content tf-grid-<?php echo esc_attr( $grid_column ); ?>">
					<?php if ( $tour_duration ) { ?>
						<div class="tf-single-square-block first">
							<i class="<?php echo esc_attr( $tour_duration_icon ); ?>"></i>
							<h4><?php echo esc_html__( 'Duration', 'tourfic' ); ?></h4>
							<p><?php echo esc_html( $tour_duration ); ?>
							<span>
								<?php
								if ( $tour_duration > 1 ) {
									$dur_string         = 's';
									$duration_time_html = $duration_time . $dur_string;
								} else {
									$duration_time_html = $duration_time;
								}
								echo " " . esc_html( $duration_time_html );
								?>
							</span></p>
							<?php if ( $night ) { ?>
								<p>
									<?php echo esc_html( $night_count ); ?>
									<span>
										<?php
										if ( ! empty( $night_count ) ) {
											if ( $night_count > 1 ) {
												echo esc_html__( 'Nights', 'tourfic' );
											} else {
												echo esc_html__( 'Night', 'tourfic' );
											}
										}
										?>
									</span>
								</p>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if ( $info_tour_type ) {
						if ( 'string' === gettype( $info_tour_type ) ) {
							$info_tour_type = ucfirst( esc_html( $info_tour_type ) );
						} elseif ( 'array' === gettype( $info_tour_type ) ) {
							$tour_types = [];
							$types      = ! empty( get_the_terms( $post_id, 'tour_type' ) ) ? get_the_terms( $post_id, 'tour_type' ) : '';
							if ( ! empty( $types ) ) {
								foreach ( $types as $type ) {
									$tour_types[] = $type->name;
								}
							}
							$info_tour_type = implode( ', ', $tour_types );
						}
						?>
						<div class="tf-single-square-block second">
							<i class="<?php echo esc_attr( $tour_type_icon ); ?>"></i>
							<h4><?php echo esc_html__( 'Tour Type', 'tourfic' ); ?></h4>
							<p><?php echo esc_html( $info_tour_type ); ?></p>
						</div>
					<?php } ?>
					<?php if ( $group_size ) { ?>
						<div class="tf-single-square-block third">
							<i class="<?php echo esc_attr( $tour_group_icon ); ?>"></i>
							<h4><?php echo esc_html__( 'Group Size', 'tourfic' ); ?></h4>
							<p><?php echo esc_html( $group_size ); ?></p>
						</div>
					<?php } ?>
					<?php if ( $language ) { ?>
						<div class="tf-single-square-block fourth">
							<i class="<?php echo esc_attr( $tour_lang_icon ); ?>"></i>
							<h4><?php echo esc_html__( 'Language', 'tourfic' ); ?></h4>
							<p><?php echo esc_html( $language ); ?></p>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php
		}

		echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}
}
