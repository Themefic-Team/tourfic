<?php

namespace Tourfic\App\Templates\Components\Shared\Single;

use Tourfic\App\TF_Review;
use Tourfic\Classes\Helper;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Review — Shared global component for Elementor & Bricks
 */
class Review {

	/**
	 * Main render dispatcher.
	 *
	 * @param array  $settings Widget settings.
	 * @param string $builder  'elementor' | 'bricks'
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type( $post_id );
        $wrapper_open  = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

        echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';

		if ( $post_type === 'tf_hotel' ) {
			static::tf_hotel_review( $settings, $builder );
		} elseif ( $post_type === 'tf_room' ) {
			static::tf_room_review( $settings, $builder );
		} elseif ( $post_type === 'tf_tours' ) {
			static::tf_tour_review( $settings, $builder );
		} elseif ( $post_type === 'tf_apartment' ) {
			static::tf_apartment_review( $settings, $builder );
		} elseif ( $post_type === 'tf_carrental' ) {
			static::tf_car_review( $settings, $builder );
		}

        echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}

	// Hotel
	private static function tf_hotel_review( $settings, $builder ) {
        $post_id   = get_the_ID();
		$style              = ! empty( $settings['review_style'] ) ? $settings['review_style'] : 'design-1';
		$show_review_states = Helper::get_switcher_value( $settings, 'show_review_states', 'yes', $builder );
		$show_reviews       = Helper::get_switcher_value( $settings, 'show_reviews', 'yes', $builder );
		$show_review_form   = Helper::get_switcher_value( $settings, 'show_review_form', 'yes', $builder );
        $container          = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
        $wrapper            = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';

		$meta               = get_post_meta( $post_id, 'tf_hotels_opt', true );
		$s_review           = ! empty( Helper::tfopt( 'h-review' ) ) ? Helper::tfopt( 'h-review' ) : 0;
		$disable_review_sec = ! empty( $meta['h-review'] ) ? $meta['h-review'] : '';
		$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;

		$comments_query = new \WP_Comment_Query( [
			'post_id' => $post_id,
			'status'  => 'approve',
			'type'    => 'comment',
		] );
		$comments = $comments_query->comments;

		if ( $style === 'design-1' && ! $disable_review_sec == 1 ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__one tf-single-review__style-1 sp-0">' : ''; ?>
				<div class="tf-review-wrapper" id="tf-review">
					<?php if ( get_comments_number() > 0 ) : ?>
						<div class="tf-average-review">
							<div class="tf-section-head">
								<h2 class="tf-title tf-section-title"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
							</div>
						</div>
					<?php endif; ?>
					<?php 
                        if(!empty($builder)){
                            static::review_template( $settings, $post_id );
                        } else {
                            comments_template();
                        }
                    ?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( $style === 'design-2' && $disable_review_sec != 1 ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__two tf-single-review__style-2">' : ''; ?>
				<div class="tf-sitebar-widgets tf-single-widgets">
					<?php
					global $current_user;
					$is_user_logged_in = $current_user->exists();
					$tf_ratings_for    = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
					$tf_settings_base  = ! empty( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
					if ( $comments && $show_review_states == 'yes' ) :
						$tf_overall_rate = [];
						TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
						TF_Review::tf_get_review_fields( $fields );
					?>
						<h2 class="tf-section-title"><?php esc_html_e( 'Overall reviews', 'tourfic' ); ?></h2>
						<div class="tf-review-data-inner">
							<div class="tf-review-data">
								<div class="tf-review-data-average">
									<span class="avg-review"><span>
										<?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?>
									</span>/ <?php echo wp_kses_post( $tf_settings_base ); ?></span>
								</div>
								<div class="tf-review-all-info">
									<p><?php esc_html_e( 'Excellent', 'tourfic' ); ?> <span><?php esc_html_e( 'Total ', 'tourfic' ); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span></p>
								</div>
							</div>
							<div class="tf-review-data-features">
								<div class="tf-percent-progress">
									<?php
									if ( $tf_overall_rate ) {
										foreach ( $tf_overall_rate as $key => $value ) {
											if ( empty( $value ) || ! in_array( $key, $fields ) ) {
												continue;
											}
											$value = TF_Review::tf_average_ratings( $value );
											?>
											<div class="tf-progress-item">
												<div class="tf-review-feature-label">
													<p class="feature-label"><?php echo esc_html( $key ); ?></p>
													<p class="feature-rating"> <?php echo wp_kses_post( $value ); ?></p>
												</div>
												<div class="tf-progress-bar">
													<span class="percent-progress" style="width: <?php echo wp_kses_post( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
												</div>
											</div>
											<?php
										}
									}
									?>
								</div>
							</div>
						</div>
						<a class="tf-all-reviews" href="#tf-hotel-reviews"><?php esc_html_e( 'See all reviews', 'tourfic' ); ?></a>
					<?php endif; ?>

					<?php
					if ( $show_review_form == 'yes' ) :
						$tf_comment_counts = get_comments( [
							'post_id' => $post_id,
							'user_id' => $current_user->ID,
							'count'   => true,
						] );
					?>
						<?php if ( empty( $tf_comment_counts ) && $tf_comment_counts == 0 ) : ?>
							<button class="tf_btn tf_btn_full tf_btn_sharp tf_btn_large tf-review-open">
								<?php esc_html_e( 'Leave your review', 'tourfic' ); ?>
							</button>
						<?php endif; ?>
						<?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '' ); ?>
						<?php
						if ( ! empty( $tf_ratings_for ) ) {
							if ( $is_user_logged_in ) {
								if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
									?>
									<div class="tf-review-form-wrapper">
										<h3><?php esc_html_e( 'Leave your review', 'tourfic' ); ?></h3>
										<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
										<?php TF_Review::tf_review_form(); ?>
									</div>
									<?php
								}
							} else {
								if ( in_array( 'lo', $tf_ratings_for ) ) {
									?>
									<div class="tf-review-form-wrapper">
										<h3><?php esc_html_e( 'Leave your review', 'tourfic' ); ?></h3>
										<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
										<?php TF_Review::tf_review_form(); ?>
									</div>
									<?php
								}
							}
						}
					endif;
					?>
				</div>

				<?php if ( $comments && $show_reviews == 'yes' ) { ?>
				<div class="tf-reviews-wrapper tf-section" id="tf-hotel-reviews">
					<h2 class="tf-section-title"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
					<p><?php esc_html_e( 'Total', 'tourfic' ); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></p>
					<div class="tf-reviews-slider tf-slick-slider">
						<?php
						foreach ( $comments as $comment ) {
							$tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
							if ( $tf_overall_rate == false ) {
								$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
								$tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
							}
							$base_rate     = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
							$c_rating      = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );
							$c_avatar      = get_avatar( $comment, '56' );
							$c_author_name = $comment->comment_author;
							$c_date        = $comment->comment_date;
							$c_content     = $comment->comment_content;
							?>
							<div class="tf-reviews-item">
								<div class="tf-reviews-avater"><?php echo wp_kses_post( $c_avatar ); ?></div>
								<div class="tf-reviews-text">
									<span class="tf-review-rating"><?php echo wp_kses_post( $c_rating ); ?></span>
									<span class="tf-reviews-meta"><?php echo esc_html( $c_author_name ); ?>, <?php echo wp_kses_post( wp_date( 'F Y', strtotime( $c_date ) ) ); ?></span>
									<p><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( $c_content, 180 ) ); ?></p>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php } ?>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( $style === 'design-3' && ! $disable_review_sec == 1 ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__legacy tf-single-review__style-legacy sp-0">' : ''; ?>
				<div id="tf-review" class="review-section">
                    <?php echo 'yes' === $container ? '<div class="tf-container">' : ''; ?>
					<div class="reviews">
						<h2 class="section-heading"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
						<?php 
                            if(!empty($builder)){
                                static::review_template( $settings, $post_id );
                            } else {
                                comments_template();
                            }
                        ?>
					</div>
                    <?php echo 'yes' === $container ? '</div>' : ''; ?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		}
	}

	// Room
	private static function tf_room_review( $settings, $builder ) {
        $post_id   = get_the_ID();
		$style              = ! empty( $settings['review_style'] ) ? $settings['review_style'] : 'design-2';
		$show_review_states = Helper::get_switcher_value( $settings, 'show_review_states', 'yes', $builder );
		$show_reviews       = Helper::get_switcher_value( $settings, 'show_reviews', 'yes', $builder );
		$show_review_form   = Helper::get_switcher_value( $settings, 'show_review_form', 'yes', $builder );
        $container          = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
        $wrapper            = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';

		$meta               = get_post_meta( $post_id, 'tf_room_opt', true );
		$s_review           = ! empty( Helper::tfopt( 'disable-room-review' ) ) ? Helper::tfopt( 'disable-room-review' ) : 0;
		$disable_review_sec = ! empty( $meta['disable-room-review'] ) ? $meta['disable-room-review'] : '';
		$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;

		$comments_query = new \WP_Comment_Query( [
			'post_id' => $post_id,
			'status'  => 'approve',
			'type'    => 'comment',
		] );
		$comments = $comments_query->comments;

		if ( $style === 'design-2' && $disable_review_sec != 1 ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__two tf-room-single-review__style-2">' : ''; ?>
				<?php echo ($show_review_states == 'yes' || $show_review_form == 'yes') ? '<div class="tf-sitebar-widgets tf-single-widgets">' : ''; ?>
					<?php
					global $current_user;
					$is_user_logged_in = $current_user->exists();
					$tf_ratings_for    = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
					$tf_settings_base  = ! empty( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
					if ( $comments && $show_review_states == 'yes' ) :
						$tf_overall_rate = [];
						TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
						TF_Review::tf_get_review_fields( $fields );
					?>
						<h2 class="tf-section-title"><?php esc_html_e( 'Overall reviews', 'tourfic' ); ?></h2>
						<div class="tf-review-data-inner">
							<div class="tf-review-data">
								<div class="tf-review-data-average">
									<span class="avg-review"><span>
										<?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?>
									</span>/ <?php echo wp_kses_post( $tf_settings_base ); ?></span>
								</div>
								<div class="tf-review-all-info">
									<p><?php esc_html_e( 'Excellent', 'tourfic' ); ?> <span><?php esc_html_e( 'Total ', 'tourfic' ); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span></p>
								</div>
							</div>
							<div class="tf-review-data-features">
								<div class="tf-percent-progress">
									<?php
									if ( $tf_overall_rate ) {
										foreach ( $tf_overall_rate as $key => $value ) {
											if ( empty( $value ) || ! in_array( $key, $fields ) ) {
												continue;
											}
											$value = TF_Review::tf_average_ratings( $value );
											?>
											<div class="tf-progress-item">
												<div class="tf-review-feature-label">
													<p class="feature-label"><?php echo esc_html( $key ); ?></p>
													<p class="feature-rating"> <?php echo wp_kses_post( $value ); ?></p>
												</div>
												<div class="tf-progress-bar">
													<span class="percent-progress" style="width: <?php echo wp_kses_post( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
												</div>
											</div>
											<?php
										}
									}
									?>
								</div>
							</div>
						</div>
						<a class="tf-all-reviews" href="#tf-hotel-reviews"><?php esc_html_e( 'See all reviews', 'tourfic' ); ?></a>
					<?php endif; ?>

					<?php
					if ( $show_review_form == 'yes' ) :
						$tf_comment_counts = get_comments( [
							'post_id' => $post_id,
							'user_id' => $current_user->ID,
							'count'   => true,
						] );
					?>
						<?php if ( empty( $tf_comment_counts ) && $tf_comment_counts == 0 ) : ?>
							<button class="tf_btn tf_btn_full tf_btn_rounded tf_btn_large tf-review-open">
								<?php esc_html_e( 'Leave your review', 'tourfic' ); ?>
							</button>
						<?php endif; ?>
						<?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '' ); ?>
						<?php
						if ( ! empty( $tf_ratings_for ) ) {
							if ( $is_user_logged_in ) {
								if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
									?>
									<div class="tf-review-form-wrapper">
										<h3><?php esc_html_e( 'Leave your review', 'tourfic' ); ?></h3>
										<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
										<?php TF_Review::tf_review_form(); ?>
									</div>
									<?php
								}
							} else {
								if ( in_array( 'lo', $tf_ratings_for ) ) {
									?>
									<div class="tf-review-form-wrapper">
										<h3><?php esc_html_e( 'Leave your review', 'tourfic' ); ?></h3>
										<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
										<?php TF_Review::tf_review_form(); ?>
									</div>
									<?php
								}
							}
						}
					endif;
					?>
                <?php echo ($show_review_states == 'yes' || $show_review_form == 'yes') ? '</div>' : ''; ?>

				<?php if ( $comments && $show_reviews == 'yes' ) { ?>
				<div class="tf-reviews-wrapper" id="tf-hotel-reviews">
					<h2 class="tf-section-title"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
					<div class="tf-review-items">
						<?php
						foreach ( $comments as $comment ) {
							$tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
							if ( $tf_overall_rate == false ) {
								$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
								$tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
							}
							$base_rate     = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
							$c_rating      = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );
							$c_avatar      = get_avatar( $comment, '64' );
							$c_author_name = $comment->comment_author;
							$c_date        = $comment->comment_date;
							$c_content     = $comment->comment_content;
							?>
							<div class="tf-reviews-item">
								<div class="tf-reviews-item-header">
									<div class="tf-reviews-item-header-left">
										<div class="tf-reviews-avater"><?php echo wp_kses_post( $c_avatar ); ?></div>
										<div class="tf-reviews-avater-right">
											<span class="tf-reviews-name"><?php echo esc_html( $c_author_name ); ?></span>
											<span class="tf-review-rating"><?php echo wp_kses_post( $c_rating ); ?></span>
										</div>
									</div>
									<div class="tf-reviews-item-header-right">
										<?php echo wp_kses_post( gmdate( 'd M, Y', strtotime( $c_date ) ) ); ?>
									</div>
								</div>
								<div class="tf-reviews-text">
									<p><?php echo wp_kses_post( $c_content ); ?></p>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php } ?>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		}
	}

	// Tours
	private static function tf_tour_review( $settings, $builder ) {
        $post_id   = get_the_ID();
		$style              = ! empty( $settings['review_style'] ) ? $settings['review_style'] : 'design-1';
		$show_review_states = Helper::get_switcher_value( $settings, 'show_review_states', 'yes', $builder );
		$show_reviews       = Helper::get_switcher_value( $settings, 'show_reviews', 'yes', $builder );
		$show_review_form   = Helper::get_switcher_value( $settings, 'show_review_form', 'yes', $builder );
        $container          = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
        $wrapper            = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';

		$meta               = get_post_meta( $post_id, 'tf_tours_opt', true );
		$s_review           = ! empty( Helper::tfopt( 't-review' ) ) ? Helper::tfopt( 't-review' ) : 0;
		$disable_review_sec = ! empty( $meta['t-review'] ) ? $meta['t-review'] : '';
		$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;

		$comments_query = new \WP_Comment_Query( [
			'post_id' => $post_id,
			'status'  => 'approve',
			'type'    => 'comment',
		] );
		$comments = $comments_query->comments;

		if ( $style === 'design-1' && ! $disable_review_sec == 1 ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__one tf-single-review__style-1 sp-0">' : ''; ?>
				<div class="tf-review-wrapper" id="tf-review">
					<?php if ( get_comments_number() > 0 ) : ?>
						<div class="tf-average-review">
							<div class="tf-section-head">
								<h2 class="tf-title tf-section-title"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
							</div>
						</div>
					<?php endif; ?>
					<?php 
                        if(!empty($builder)){
                            static::review_template( $settings, $post_id );
                        } else {
                            comments_template();
                        }
                    ?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( $style === 'design-2' && $disable_review_sec != 1 ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__two tf-single-review__style-2">' : ''; ?>
				<div class="tf-sitebar-widgets tf-single-widgets">
					<?php
					global $current_user;
					$is_user_logged_in = $current_user->exists();
					$tf_ratings_for    = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
					$tf_settings_base  = ! empty( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
					if ( $comments && $show_review_states == 'yes' ) :
						$tf_overall_rate = [];
						TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
						TF_Review::tf_get_review_fields( $fields );
					?>
						<h2 class="tf-section-title"><?php esc_html_e( 'Overall reviews', 'tourfic' ); ?></h2>
						<div class="tf-review-data-inner">
							<div class="tf-review-data">
								<div class="tf-review-data-average">
									<span class="avg-review"><span>
										<?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?>
									</span>/ <?php echo wp_kses_post( $tf_settings_base ); ?></span>
								</div>
								<div class="tf-review-all-info">
									<p><?php esc_html_e( 'Excellent', 'tourfic' ); ?> <span><?php esc_html_e( 'Total ', 'tourfic' ); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span></p>
								</div>
							</div>
							<div class="tf-review-data-features">
								<div class="tf-percent-progress">
									<?php
									if ( $tf_overall_rate ) {
										foreach ( $tf_overall_rate as $key => $value ) {
											if ( empty( $value ) || ! in_array( $key, $fields ) ) {
												continue;
											}
											$value = TF_Review::tf_average_ratings( $value );
											?>
											<div class="tf-progress-item">
												<div class="tf-review-feature-label">
													<p class="feature-label"><?php echo esc_html( $key ); ?></p>
													<p class="feature-rating"> <?php echo wp_kses_post( $value ); ?></p>
												</div>
												<div class="tf-progress-bar">
													<span class="percent-progress" style="width: <?php echo wp_kses_post( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
												</div>
											</div>
											<?php
										}
									}
									?>
								</div>
							</div>
						</div>
						<a class="tf-all-reviews" href="#tf-hotel-reviews"><?php esc_html_e( 'See all reviews', 'tourfic' ); ?></a>
					<?php endif; ?>

					<?php
					if ( $show_review_form == 'yes' ) :
						$tf_comment_counts = get_comments( [
							'post_id' => $post_id,
							'user_id' => $current_user->ID,
							'count'   => true,
						] );
					?>
						<?php if ( empty( $tf_comment_counts ) && $tf_comment_counts == 0 ) : ?>
							<button class="tf_btn tf_btn_full tf_btn_sharp tf_btn_large tf-review-open">
								<?php esc_html_e( 'Leave your review', 'tourfic' ); ?>
							</button>
						<?php endif; ?>
						<?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '' ); ?>
						<?php
						if ( ! empty( $tf_ratings_for ) ) {
							if ( $is_user_logged_in ) {
								if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
									?>
									<div class="tf-review-form-wrapper">
										<h3><?php esc_html_e( 'Leave your review', 'tourfic' ); ?></h3>
										<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
										<?php TF_Review::tf_review_form(); ?>
									</div>
									<?php
								}
							} else {
								if ( in_array( 'lo', $tf_ratings_for ) ) {
									?>
									<div class="tf-review-form-wrapper">
										<h3><?php esc_html_e( 'Leave your review', 'tourfic' ); ?></h3>
										<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
										<?php TF_Review::tf_review_form(); ?>
									</div>
									<?php
								}
							}
						}
					endif;
					?>
				</div>

				<?php if ( $comments && $show_reviews == 'yes' ) { ?>
				<div class="tf-reviews-wrapper tf-section" id="tf-hotel-reviews">
					<h2 class="tf-section-title"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
					<p><?php esc_html_e( 'Total', 'tourfic' ); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></p>
					<div class="tf-reviews-slider tf-slick-slider">
						<?php
						foreach ( $comments as $comment ) {
							$tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
							if ( $tf_overall_rate == false ) {
								$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
								$tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
							}
							$base_rate     = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
							$c_rating      = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );
							$c_avatar      = get_avatar( $comment, '56' );
							$c_author_name = $comment->comment_author;
							$c_date        = $comment->comment_date;
							$c_content     = $comment->comment_content;
							?>
							<div class="tf-reviews-item">
								<div class="tf-reviews-avater"><?php echo wp_kses_post( $c_avatar ); ?></div>
								<div class="tf-reviews-text">
									<span class="tf-review-rating"><?php echo wp_kses_post( $c_rating ); ?></span>
									<span class="tf-reviews-meta"><?php echo esc_html( $c_author_name ); ?>, <?php echo wp_kses_post( wp_date( 'F Y', strtotime( $c_date ) ) ); ?></span>
									<p><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( $c_content, 180 ) ); ?></p>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php } ?>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( $style === 'design-3' && ! $disable_review_sec == 1 ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__legacy tf-single-review__style-legacy">' : ''; ?>
				<div id="tf-review" class="review-section">
                    <?php echo 'yes' === $container ? '<div class="tf-container">' : ''; ?>
                        <div class="reviews">
                            <h2 class="section-heading"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
                            <?php 
                                if(!empty($builder)){
                                    static::review_template( $settings, $post_id );
                                } else {
                                    comments_template();
                                }
                            ?>
                        </div>
                    <?php echo 'yes' === $container ? '</div>' : ''; ?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		}
	}

	// Apartment
	private static function tf_apartment_review( $settings, $builder ) {
        $post_id   = get_the_ID();
		$style              = ! empty( $settings['review_style'] ) ? $settings['review_style'] : 'design-1';
		$show_review_states = Helper::get_switcher_value( $settings, 'show_review_states', 'yes', $builder );
		$show_reviews       = Helper::get_switcher_value( $settings, 'show_reviews', 'yes', $builder );
		$show_review_form   = Helper::get_switcher_value( $settings, 'show_review_form', 'yes', $builder );
        $container          = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
        $wrapper            = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';

		$meta               = get_post_meta( $post_id, 'tf_apartment_opt', true );
		$s_review           = ! empty( Helper::tfopt( 'disable-apartment-review' ) ) ? Helper::tfopt( 'disable-apartment-review' ) : 0;
		$disable_review_sec = ! empty( $meta['disable-apartment-review'] ) ? $meta['disable-apartment-review'] : '';
		$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;

		$comments_query = new \WP_Comment_Query( [
			'post_id' => $post_id,
			'status'  => 'approve',
			'type'    => 'comment',
		] );
		$comments = $comments_query->comments;

		if ( $style === 'design-2' && $disable_review_sec != 1 ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__two tf-single-review__style-2">' : ''; ?>
				<div class="tf-sitebar-widgets tf-single-widgets">
					<?php
					global $current_user;
					$is_user_logged_in = $current_user->exists();
					$tf_ratings_for    = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
					$tf_settings_base  = ! empty( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
					if ( $comments && $show_review_states == 'yes' ) :
						$tf_overall_rate = [];
						TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
						TF_Review::tf_get_review_fields( $fields );
					?>
						<h2 class="tf-section-title"><?php esc_html_e( 'Overall reviews', 'tourfic' ); ?></h2>
						<div class="tf-review-data-inner">
							<div class="tf-review-data">
								<div class="tf-review-data-average">
									<span class="avg-review"><span>
										<?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?>
									</span>/ <?php echo wp_kses_post( $tf_settings_base ); ?></span>
								</div>
								<div class="tf-review-all-info">
									<p><?php esc_html_e( 'Excellent', 'tourfic' ); ?> <span><?php esc_html_e( 'Total ', 'tourfic' ); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span></p>
								</div>
							</div>
							<div class="tf-review-data-features">
								<div class="tf-percent-progress">
									<?php
									if ( $tf_overall_rate ) {
										foreach ( $tf_overall_rate as $key => $value ) {
											if ( empty( $value ) || ! in_array( $key, $fields ) ) {
												continue;
											}
											$value = TF_Review::tf_average_ratings( $value );
											?>
											<div class="tf-progress-item">
												<div class="tf-review-feature-label">
													<p class="feature-label"><?php echo esc_html( $key ); ?></p>
													<p class="feature-rating"> <?php echo wp_kses_post( $value ); ?></p>
												</div>
												<div class="tf-progress-bar">
													<span class="percent-progress" style="width: <?php echo wp_kses_post( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
												</div>
											</div>
											<?php
										}
									}
									?>
								</div>
							</div>
						</div>
						<a class="tf-all-reviews" href="#tf-hotel-reviews"><?php esc_html_e( 'See all reviews', 'tourfic' ); ?></a>
					<?php endif; ?>

					<?php
					if ( $show_review_form == 'yes' ) :
						$tf_comment_counts = get_comments( [
							'post_id' => $post_id,
							'user_id' => $current_user->ID,
							'count'   => true,
						] );
					?>
						<?php if ( empty( $tf_comment_counts ) && $tf_comment_counts == 0 ) : ?>
							<button class="tf_btn tf_btn_full tf_btn_sharp tf_btn_large tf-review-open">
								<?php esc_html_e( 'Leave your review', 'tourfic' ); ?>
							</button>
						<?php endif; ?>
						<?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '' ); ?>
						<?php
						if ( ! empty( $tf_ratings_for ) ) {
							if ( $is_user_logged_in ) {
								if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
									?>
									<div class="tf-review-form-wrapper">
										<h3><?php esc_html_e( 'Leave your review', 'tourfic' ); ?></h3>
										<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
										<?php TF_Review::tf_review_form(); ?>
									</div>
									<?php
								}
							} else {
								if ( in_array( 'lo', $tf_ratings_for ) ) {
									?>
									<div class="tf-review-form-wrapper">
										<h3><?php esc_html_e( 'Leave your review', 'tourfic' ); ?></h3>
										<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
										<?php TF_Review::tf_review_form(); ?>
									</div>
									<?php
								}
							}
						}
					endif;
					?>
				</div>

				<?php if ( $comments && $show_reviews == 'yes' ) { ?>
				<div class="tf-reviews-wrapper tf-section" id="tf-hotel-reviews">
					<h2 class="tf-section-title"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
					<p><?php esc_html_e( 'Total', 'tourfic' ); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></p>
					<div class="tf-reviews-slider tf-slick-slider">
						<?php
						foreach ( $comments as $comment ) {
							$tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
							if ( $tf_overall_rate == false ) {
								$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
								$tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
							}
							$base_rate     = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
							$c_rating      = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );
							$c_avatar      = get_avatar( $comment, '56' );
							$c_author_name = $comment->comment_author;
							$c_date        = $comment->comment_date;
							$c_content     = $comment->comment_content;
							?>
							<div class="tf-reviews-item">
								<div class="tf-reviews-avater"><?php echo wp_kses_post( $c_avatar ); ?></div>
								<div class="tf-reviews-text">
									<span class="tf-review-rating"><?php echo wp_kses_post( $c_rating ); ?></span>
									<span class="tf-reviews-meta"><?php echo esc_html( $c_author_name ); ?>, <?php echo wp_kses_post( wp_date( 'F Y', strtotime( $c_date ) ) ); ?></span>
									<p><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( $c_content, 180 ) ); ?></p>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<?php } ?>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		} elseif ( $style === 'design-1' && ! $disable_review_sec == 1 ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__legacy tf-single-review__style-legacy">' : ''; ?>
				<div id="tf-review" class="review-section">
                    <?php echo 'yes' === $container ? '<div class="tf-container">' : ''; ?>
					<div class="reviews">
						<h2 class="section-heading"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
						<?php 
                            if(!empty($builder)){
                                static::review_template( $settings, $post_id );
                            } else {
                                comments_template();
                            }
                        ?>
					</div>
                    <?php echo 'yes' === $container ? '</div>' : ''; ?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		}
	}

	// Car Rental
	private static function tf_car_review( $settings, $builder ) {
		$style = ! empty( $settings['review_style'] ) ? $settings['review_style'] : 'design-1';

        $post_id   = get_the_ID();
		$meta              = get_post_meta( $post_id, 'tf_carrental_opt', true );
		$review_sec_title  = ! empty( $meta['review_sec_title'] ) ? $meta['review_sec_title'] : '';
        $container          = ! empty( $settings['container'] ) ? $settings['container'] : 'no';
        $wrapper            = ! empty( $settings['wrapper'] ) ? $settings['wrapper'] : 'yes';

		global $current_user;
		$is_user_logged_in = $current_user->exists();
		$tf_ratings_for    = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
		$tf_settings_base  = ! empty( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;

		$tf_comment_counts = get_comments( [
			'post_id' => $post_id,
			'user_id' => $current_user->ID,
			'count'   => true,
		] );
		$comments_query = new \WP_Comment_Query( [
			'post_id' => $post_id,
			'status'  => 'approve',
			'type'    => 'comment',
		] );
		$comments = $comments_query->comments;

		if ( $style === 'design-1' ) {
			?>
			<?php echo 'yes' === $wrapper ? '<div class="tf-single-template__one tf-car-single-review__style-1 sp-0">' : ''; ?>
				<div class="tf-review-section" id="tf-reviews">
					<?php if ( $comments ) {
						$tf_overall_rate = [];
						TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
						TF_Review::tf_get_review_fields( $fields );
					?>
						<?php if ( ! empty( $review_sec_title ) ) { ?>
							<h3 class="section-heading"><?php echo esc_html( $review_sec_title ); ?></h3>
						<?php } ?>
						<div class="tf-review-data-inner">
							<div class="tf-review-data">
								<div class="tf-review-data-average">
									<span class="avg-review tf-flex tf-flex-align-center tf-flex-gap-8">
										<?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?>
										<i class="fa fa-star"></i>
									</span>
									<div class="tf-review-all-info">
										<p><?php esc_html_e( 'From ', 'tourfic' ); ?><?php TF_Review::tf_based_on_text( count( $comments ) ); ?></p>
									</div>
								</div>
							</div>
							<div class="tf-review-data-features">
								<div class="tf-percent-progress">
									<?php
									if ( $tf_overall_rate ) {
										foreach ( $tf_overall_rate as $key => $value ) {
											if ( empty( $value ) || ! in_array( $key, $fields ) ) {
												continue;
											}
											$value = TF_Review::tf_average_ratings( $value );
											?>
											<div class="tf-progress-item">
												<div class="tf-progress-bar">
													<span class="percent-progress" style="width: <?php echo esc_attr( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
												</div>
												<div class="tf-review-feature-label">
													<p class="feature-label"><?php echo esc_html( $key ); ?></p>
													<p class="feature-rating"> <?php echo esc_html( $value ); ?></p>
												</div>
											</div>
											<?php
										}
									}
									?>
								</div>
							</div>
						</div>
						<div class="tf-clients-reviews">
							<?php
							foreach ( $comments as $comment ) {
								$tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
								if ( $tf_overall_rate == false ) {
									$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
									$tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
								}
								$base_rate     = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
								$c_rating      = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );
								$c_avatar      = get_avatar( $comment, '56' );
								$c_author_name = $comment->comment_author;
								$c_date        = $comment->comment_date;
								$c_content     = $comment->comment_content;
								?>
								<div class="tf-reviews-item tf-flex tf-flex-gap-16">
									<div class="tf-reviews-avater"><?php echo wp_kses_post( $c_avatar ); ?></div>
									<div class="tf-reviews-text">
										<span class="tf-review-rating"><?php echo wp_kses_post( $c_rating ); ?></span>
										<span class="tf-reviews-meta"><?php echo esc_html( $c_author_name ); ?> <span class="tf-reviews-time">| <?php echo wp_kses_post( wp_date( 'F Y', strtotime( $c_date ) ) ); ?></span></span>
										<p><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( $c_content, 180 ) ); ?></p>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					<?php } ?>
					<?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '' ); ?>
					<?php
					if ( ! empty( $tf_ratings_for ) && empty( $tf_comment_counts ) && $tf_comment_counts == 0 ) {
						if ( $is_user_logged_in ) {
							if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
								?>
								<div class="tf-review-form-wrapper">
									<h3><?php esc_html_e( 'Leave your review', 'tourfic' ); ?></h3>
									<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
									<?php TF_Review::tf_review_form(); ?>
								</div>
								<?php
							}
						} else {
							if ( in_array( 'lo', $tf_ratings_for ) ) {
								?>
								<div class="tf-review-form-wrapper">
									<h3><?php esc_html_e( 'Leave your review', 'tourfic' ); ?></h3>
									<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
									<?php TF_Review::tf_review_form(); ?>
								</div>
								<?php
							}
						}
					}
					?>
				</div>
			<?php echo 'yes' === $wrapper ? '</div>' : ''; ?>
			<?php
		}
	}
    
	// Shared review template (design-1 for hotel/tours, design-2 for apartment)
	private static function review_template( $settings, $post_id ) {
		global $current_user;
		$is_user_logged_in = $current_user->exists();
		$tf_ratings_for    = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
		$style             = ! empty( $settings['review_style'] ) ? $settings['review_style'] : 'design-1';

		$comments_query = new \WP_Comment_Query( [
			'post_id' => $post_id,
			'status'  => 'approve',
			'type'    => 'comment',
		] );
		$comments = $comments_query->comments;

		$post_type = get_post_type( $post_id );

		if ( ( $post_type === 'tf_tours' && $style === 'design-1' ) ||
			( $post_type === 'tf_hotel' && $style === 'design-1' ) ||
			( $post_type === 'tf_apartment' && $style === 'design-2' ) ) {

			if ( $comments ) {
				$tf_overall_rate = [];
				TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
				TF_Review::tf_get_review_fields( $fields );
				$tf_settings_base = ! empty( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
				?>
				<div class="tf-review-data tf-box">
					<div class="tf-review-data-inner tf-flex tf-flex-gap-24">
						<div class="tf-review-data">
							<div class="tf-review-data-average">
								<p><?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?></p>
							</div>
							<div class="tf-review-all-info">
								<ul class="tf-list">
									<li><i class="fa-solid fa-circle-check"></i><?php esc_html_e( 'From ', 'tourfic' ); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></li>
								</ul>
							</div>
						</div>
						<div class="tf-review-data-features">
							<div class="tf-percent-progress tf-flex tf-flex-space-bttn">
								<?php
								if ( $tf_overall_rate ) {
									foreach ( $tf_overall_rate as $key => $value ) {
										if ( empty( $value ) || ! in_array( $key, $fields ) ) {
											continue;
										}
										$value = TF_Review::tf_average_ratings( $value );
										?>
										<div class="tf-progress-item">
											<div class="tf-progress-bar">
												<span class="percent-progress" style="width: <?php echo esc_html( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
											</div>
											<div class="tf-review-feature-label tf-flex tf-flex-space-bttn">
												<p class="feature-label"><?php echo esc_html( $key ); ?></p>
												<p class="feature-rating"> <?php echo esc_html( $value ); ?></p>
											</div>
										</div>
										<?php
									}
								}
								?>
							</div>
						</div>
					</div>
				</div>
				<div class="tf-review-reply tf-mt-50">
					<div class="tf-section-head">
						<h2 class="tf-title tf-section-title"><?php esc_html_e( 'Showing', 'tourfic' ); ?> <span><?php echo count( $comments ); ?></span> <?php esc_html_e( 'Review', 'tourfic' ); ?></h2>
					</div>
					<?php
					foreach ( $comments as $comment ) {
						$tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
						if ( $tf_overall_rate == false ) {
							$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
							$tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
						}
						$base_rate     = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
						$c_rating      = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );
						$c_avatar      = get_avatar( $comment, '56' );
						$c_author_name = $comment->comment_author;
						$c_date        = $comment->comment_date;
						$c_content     = $comment->comment_content;
						?>
						<div class="tf-review-reply-data tf-flex-gap-24 tf-flex">
							<div class="tf-review-author">
								<?php echo wp_kses_post( $c_avatar ); ?>
							</div>
							<div class="tf-review-details">
								<div class="tf-review-author-name">
									<h3><?php echo esc_html( $c_author_name ); ?></h3>
								</div>
								<div class="tf-review-ratings tf-mt-8">
									<?php echo wp_kses_post( $c_rating ); ?>
								</div>
								<div class="tf-review-message">
									<p><?php echo wp_kses_post( $c_content ); ?></p>
								</div>
								<?php if ( $post_type === 'tf_hotel' && $style !== 'design-3' ) : ?>
									<div class="tf-review-date">
										<ul class="tf-list">
											<li><i class="fa-regular fa-clock"></i> <?php echo esc_html( wp_date( 'F d, Y', strtotime( $c_date ) ) ); ?></li>
										</ul>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<?php
					}
					?>
				</div>
				<?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '' ); ?>
				<?php
				if ( ! empty( $tf_ratings_for ) ) {
					if ( $is_user_logged_in ) {
						if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
							?>
							<div class="tf-review-form tf-mt-40">
								<div class="tf-section-head">
									<h2 class="tf-title tf-section-title"><?php esc_html_e( 'Leave a Review', 'tourfic' ); ?></h2>
									<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
								</div>
								<?php TF_Review::tf_review_form(); ?>
							</div>
							<?php
						}
					} else {
						if ( in_array( 'lo', $tf_ratings_for ) ) {
							?>
							<div class="tf-review-form tf-mt-40">
								<div class="tf-section-head">
									<h2 class="tf-title tf-section-title"><?php esc_html_e( 'Leave a Review', 'tourfic' ); ?></h2>
									<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
								</div>
								<?php TF_Review::tf_review_form(); ?>
							</div>
							<?php
						}
					}
				}

			} else {
				echo '<div class="no-review">';
				echo '<h4>' . esc_html__( 'No Review Available', 'tourfic' ) . '</h4>';

				if ( $is_user_logged_in ) {
					if ( is_array( $tf_ratings_for ) && in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
						?>
						<div class="tf-review-form tf-mt-40">
							<div class="tf-section-head">
								<h2 class="tf-title tf-section-title"><?php esc_html_e( 'Leave a Review', 'tourfic' ); ?></h2>
								<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
							</div>
							<?php TF_Review::tf_review_form(); ?>
						</div>
						<?php
					}
				} else {
					if ( is_array( $tf_ratings_for ) && in_array( 'lo', $tf_ratings_for ) ) {
						?>
						<div class="tf-review-form tf-mt-40">
							<div class="tf-section-head">
								<h2 class="tf-title tf-section-title"><?php esc_html_e( 'Leave a Review', 'tourfic' ); ?></h2>
								<p><?php esc_html_e( 'Your email address will not be published. Required fields are marked.', 'tourfic' ); ?></p>
							</div>
							<?php TF_Review::tf_review_form(); ?>
						</div>
						<?php
					}
				}
				echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '' );
				echo '</div>';
			}

		} else {
			?>
			<div class="tf-review-container">
				<?php
				$btn_class = ( $post_type === 'tf_apartment' && $style === 'default' )
					? 'tf_btn tf_btn_full'
					: 'tf_btn tf-submit';

				$args2          = [
					'post_id' => $post_id,
					'status'  => 'approve',
					'type'    => 'comment',
				];
				$comments_query2 = new \WP_Comment_Query( $args2 );
				$comments2       = $comments_query2->comments;

				if ( $comments2 ) {
					$tf_rating_progress_bar = '';
					$tf_overall_rate        = [];
					TF_Review::tf_calculate_comments_rating( $comments2, $tf_overall_rate, $total_rating );
					TF_Review::tf_get_review_fields( $fields );

					if ( $tf_overall_rate ) {
						foreach ( $tf_overall_rate as $key => $value ) {
							if ( empty( $value ) || ! in_array( $key, $fields ) ) {
								continue;
							}
							$value                   = TF_Review::tf_average_ratings( $value );
							$tf_rating_progress_bar .= '<div class="tf-single">';
							$tf_rating_progress_bar .= '<div class="tf-text">' . esc_html( $key ) . '</div>';
							$tf_rating_progress_bar .= '<div class="tf-p-bar"><div class="percent-progress" data-width="' . esc_attr( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ) . '"></div></div>';
							$tf_rating_progress_bar .= '<div class="tf-p-b-rating">' . esc_html( $value ) . '</div>';
							$tf_rating_progress_bar .= '</div>';
						}
					}
					?>
					<div class="tf-total-review">
						<div class="tf-total-average">
							<div><?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?></div>
							<span><?php TF_Review::tf_based_on_text( count( $comments2 ) ); ?></span>
						</div>
						<?php
						if ( ! empty( $tf_ratings_for ) ) {
							if ( $is_user_logged_in ) {
								if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
									?>
									<div class="tf-btn-wrap">
										<button class="<?php echo esc_attr( $btn_class ); ?> tf-modal-btn" data-target="#tf-rating-modal">
											<i class="fas fa-plus"></i> <?php esc_html_e( 'Add Review', 'tourfic' ); ?>
										</button>
									</div>
									<?php
								}
							} else {
								if ( in_array( 'lo', $tf_ratings_for ) ) {
									?>
									<div class="tf-btn-wrap">
										<button class="<?php echo esc_attr( $btn_class ); ?> tf-modal-btn" data-target="#tf-rating-modal">
											<i class="fas fa-plus"></i> <?php esc_html_e( 'Add Review', 'tourfic' ); ?>
										</button>
									</div>
									<?php
								}
							}
						}
						?>
					</div>
					<?php if ( ! empty( $tf_rating_progress_bar ) ) { ?>
						<div class="tf-review-progress-bar">
							<?php echo wp_kses_post( $tf_rating_progress_bar ); ?>
						</div>
					<?php } ?>
					<div class="tf-single-review <?php echo esc_attr( $post_type ); ?>">
						<?php
						foreach ( $comments2 as $comment ) {
							$tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
							if ( $tf_overall_rate == false ) {
								$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
								$tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
							}
							$base_rate     = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
							$c_rating      = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );
							$c_avatar      = get_avatar( $comment, '56' );
							$c_author_name = $comment->comment_author;
							$c_date        = $comment->comment_date;
							$c_content     = $comment->comment_content;
							?>
							<div class="tf-single-details">
								<div class="tf-review-avatar"><?php echo wp_kses_post( $c_avatar ); ?></div>
								<div class="tf-review-details">
									<div class="tf-name"><?php echo esc_html( $c_author_name ); ?></div>
									<div class="tf-date"><?php echo esc_html( $c_date ); ?></div>
									<div class="tf-rating-stars"><?php echo wp_kses_post( $c_rating ); ?></div>
									<?php if ( $post_type === 'tf_apartment' ) {
										if ( $style === 'default' ) {
											if ( strlen( $c_content ) > 120 ) { ?>
												<div class="tf-description">
													<p><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( $c_content, 120 ) ); ?></p>
												</div>
												<div class="tf-full-description" style="display:none;">
													<p><?php echo wp_kses_post( $c_content ); ?></p>
												</div>
											<?php } else { ?>
												<div class="tf-description">
													<p><?php echo wp_kses_post( $c_content ); ?></p>
												</div>
											<?php }
										}
									} else { ?>
										<div class="tf-description"><p><?php echo wp_kses_post( $c_content ); ?></p></div>
									<?php } ?>
									<?php if ( $post_type === 'tf_apartment' && $style === 'default' && strlen( $c_content ) > 120 ) : ?>
										<div class="tf-apartment-show-more"><?php esc_html_e( 'Show more', 'tourfic' ); ?></div>
									<?php endif; ?>
								</div>
							</div>
							<?php
						}
						?>
					</div>
					<?php if ( $post_type === 'tf_apartment' && $style === 'default' && count( $comments2 ) > 2 ) : ?>
						<div class="show-all-review-wrap">
							<div>
								<div class="tf-apaartment-show-all">
									<?php esc_html_e( 'Show all reviews', 'tourfic' ); ?>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '' ); ?>

				<?php } else { ?>
					<div class="no-review">
						<h4><?php esc_html_e( 'No Review Available', 'tourfic' ); ?></h4>
						<?php
						if ( $is_user_logged_in ) {
							if ( is_array( $tf_ratings_for ) && in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
								?>
								<div class="tf-btn-wrap">
									<button class="<?php echo esc_attr( $btn_class ); ?> tf-modal-btn" data-target="#tf-rating-modal">
										<i class="fas fa-plus"></i> <?php esc_html_e( 'Add Review', 'tourfic' ); ?>
									</button>
								</div>
								<?php
							}
						} else {
							if ( is_array( $tf_ratings_for ) && in_array( 'lo', $tf_ratings_for ) ) {
								?>
								<div class="tf-btn-wrap">
									<button class="<?php echo esc_attr( $btn_class ); ?> tf-modal-btn" data-target="#tf-rating-modal">
										<i class="fas fa-plus"></i> <?php esc_html_e( 'Add Review', 'tourfic' ); ?>
									</button>
								</div>
								<?php
							}
						}
						echo wp_kses_post( TF_Review::tf_pending_review_notice( $post_id ) ?? '' );
						?>
					</div>
				<?php } ?>
			</div>

			<div class="tf-modal" id="tf-rating-modal">
				<div class="tf-modal-dialog">
					<div class="tf-modal-content">
						<div class="tf-modal-header">
							<a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
						</div>
						<div class="tf-modal-body">
							<div id="tfreview-error-response"></div>
							<?php TF_Review::tf_review_form(); ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}
}
