<?php

/**
 * Template Name: Review Template
 */

// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;
use \Tourfic\App\TF_Review;

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>
<?php

global $current_user;

// Check if user is logged in
$tourfic_is_user_logged_in = $current_user->exists();
$tourfic_post_id           = $post->ID;
// Get settings value
$tourfic_ratings_for = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];

if ( get_post_type( $tourfic_post_id ) == 'tf_tours' ) {

	$tourfic_meta = get_post_meta( $tourfic_post_id, 'tf_tours_opt', true );
	// Single Template Check
	$tourfic_tour_layout_conditions = ! empty( $tourfic_meta['tf_single_tour_layout_opt'] ) ? $tourfic_meta['tf_single_tour_layout_opt'] : 'global';
	if ( "single" == $tourfic_tour_layout_conditions ) {
		$tourfic_tour_single_template = ! empty( $tourfic_meta['tf_single_tour_template'] ) ? $tourfic_meta['tf_single_tour_template'] : 'design-1';
	}
	$tourfic_tour_global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] : 'design-1';

	$tourfic_tour_selected_check = ! empty( $tourfic_tour_single_template ) ? $tourfic_tour_single_template : $tourfic_tour_global_template;

	$tourfic_tour_selected_template = $tourfic_tour_selected_check;

}
if ( get_post_type( $tourfic_post_id ) == 'tf_hotel' ) {

	$tourfic_meta = get_post_meta( $tourfic_post_id, 'tf_hotels_opt', true );
	// Single Template Check
	$tourfic_hotel_layout_conditions = ! empty( $tourfic_meta['tf_single_hotel_layout_opt'] ) ? $tourfic_meta['tf_single_hotel_layout_opt'] : 'global';
	if ( "single" == $tourfic_hotel_layout_conditions ) {
		$tourfic_hotel_single_template = ! empty( $tourfic_meta['tf_single_hotel_template'] ) ? $tourfic_meta['tf_single_hotel_template'] : 'design-1';
	}
	$tourfic_hotel_global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';

	$tourfic_hotel_selected_check = ! empty( $tourfic_hotel_single_template ) ? $tourfic_hotel_single_template : $tourfic_hotel_global_template;

	$tourfic_hotel_selected_template = $tourfic_hotel_selected_check;

}

if ( get_post_type( $tourfic_post_id ) == 'tf_apartment' ) {

	$tourfic_meta = get_post_meta( $tourfic_post_id, 'tf_apartment_opt', true );
	// Single Template Check
	$tourfic_apartment_layout_conditions = ! empty( $tourfic_meta['tf_single_apartment_layout_opt'] ) ? $tourfic_meta['tf_single_apartment_layout_opt'] : 'global';
	if ( "single" == $tourfic_apartment_layout_conditions ) {
		$tourfic_apartment_single_template = ! empty( $tourfic_meta['tf_single_apartment_template'] ) ? $tourfic_meta['tf_single_apartment_template'] : 'default';
	}
	$tourfic_apartment_global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-apartment'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-apartment'] : 'default';

	$tourfic_apartment_selected_template = ! empty( $tourfic_apartment_single_template ) ? $tourfic_apartment_single_template : $tourfic_apartment_global_template;

}

if ( ( get_post_type( $tourfic_post_id ) == 'tf_tours' && $tourfic_tour_selected_template == "design-1" ) ||
     ( get_post_type( $tourfic_post_id ) == "tf_hotel" && $tourfic_hotel_selected_template == "design-1" ) ||
     ( get_post_type( $tourfic_post_id ) == "tf_hotel" && $tourfic_hotel_selected_template == "design-3" ) ||
     ( get_post_type( $tourfic_post_id ) == "tf_apartment" && $tourfic_apartment_selected_template != "default" ) ) {

	if ( $tourfic_comments ) {
		$tourfic_overall_rate = [];
		TF_Review::tf_calculate_comments_rating( $tourfic_comments, $tourfic_overall_rate, $total_rating );
		TF_Review::tf_get_review_fields( $fields );
		$tourfic_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
		?>
        <div class="tf-review-data tf-box">
            <?php if(get_post_type( $tourfic_post_id ) == "tf_hotel" && $tourfic_hotel_selected_template == "design-3"): ?>
                <h5 class="tf-section-title"><?php echo !empty($tourfic_meta['review-section-title']) ? esc_html($tourfic_meta['review-section-title']) : ''; ?></h5>
            <?php endif; ?>

            <div class="tf-review-data-inner tf-flex tf-flex-gap-24">
                <div class="tf-review-data">
                    <div class="tf-review-data-average">
                        <p>
                            <?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?>
	                        <?php if(get_post_type( $tourfic_post_id ) == "tf_hotel" && $tourfic_hotel_selected_template == "design-3"): ?>
                                <span>/<?php echo wp_kses_post($tourfic_settings_base); ?></span>
	                        <?php endif; ?>
                        </p>
                    </div>
                    <div class="tf-review-all-info">
	                    <?php if(get_post_type( $tourfic_post_id ) == "tf_hotel" && $tourfic_hotel_selected_template == "design-3"){
		                    echo '<span>'. esc_html__("Wonderful", "tourfic") .'</span>';
	                    } ?>
                        <ul class="tf-list">
                            <li><i class="fa-solid fa-circle-check"></i><?php esc_html_e( "From ", "tourfic" ); ?> <?php TF_Review::tf_based_on_text( count( $tourfic_comments ) ); ?></li>
                        </ul>
                    </div>
                </div>
                <div class="tf-review-data-features">
                    <div class="tf-percent-progress tf-flex tf-flex-space-bttn">
						<?php
						if ( $tourfic_overall_rate ) {
							foreach ( $tourfic_overall_rate as $tourfic_key => $tourfic_value ) {
								if ( empty( $tourfic_value ) || ! in_array( $tourfic_key, $fields ) ) {
									continue;
								}
								$tourfic_value = TF_Review::Tf_average_ratings( $tourfic_value );
								?>
                                <div class="tf-progress-item">
                                    <div class="tf-progress-bar">
                                        <span class="percent-progress" style="width: <?php echo esc_html( TF_Review::tf_average_rating_percent( $tourfic_value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
                                    </div>
                                    <div class="tf-review-feature-label tf-flex tf-flex-space-bttn">
                                        <p class="feature-label"><?php echo esc_html( $tourfic_key ); ?></p>
                                        <p class="feature-rating"> <?php echo esc_html( $tourfic_value ); ?></p>
                                    </div>
                                </div>
							<?php }
						} ?>

                    </div>
                </div>
            </div>
        </div>
        <!-- Tourfic review reply -->
        <div class="tf-review-reply tf-mt-50 tf-mb-56">
            <div class="tf-section-head">
                <?php if(get_post_type( $tourfic_post_id ) == "tf_hotel" && $tourfic_hotel_selected_template == "design-3"): ?>
                    <h5 class="tf-section-title"><?php esc_html_e( "Guest’s reviews", "tourfic" ); ?></h5>
                <?php else : ?>
                    <h2 class="tf-title tf-section-title"><?php esc_html_e( "Showing", "tourfic" ); ?> <span><?php echo count( $tourfic_comments ); ?></span> <?php esc_html_e( "Review", "tourfic" ); ?></h2>
                <?php endif; ?>
            </div>
			<?php
			foreach ( $tourfic_comments as $comment ) {

				// Get rating details
				$tourfic_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
				if ( $tourfic_overall_rate == false ) {
					$tourfic_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
					$tourfic_overall_rate = TF_Review::Tf_average_ratings( $tourfic_comment_meta );
				}
				$tourfic_base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
				$tourfic_c_rating  = TF_Review::tf_single_rating_change_on_base( $tourfic_overall_rate, $tourfic_base_rate );

				// Comment details
				if(get_post_type( $tourfic_post_id ) == "tf_hotel" && $tourfic_hotel_selected_template == "design-3") {
					$tourfic_c_avatar = get_avatar( $comment, '96' );
				} else {
					$tourfic_c_avatar = get_avatar( $comment, '56' );
				}
				$tourfic_c_author_name = $comment->comment_author;
				$tourfic_c_date        = $comment->comment_date;
				$tourfic_c_content     = $comment->comment_content;
				?>
                <!-- reviews and replies -->
                <div class="tf-review-reply-data tf-flex-gap-24 tf-flex">
                    <div class="tf-review-author">
						<?php echo wp_kses_post( $tourfic_c_avatar ); ?>
                    </div>
                    <div class="tf-review-details">
                        <div class="tf-review-author-name">
                            <h3><?php echo esc_html( $tourfic_c_author_name ); ?></h3>
	                        <?php if(get_post_type( $tourfic_post_id ) == "tf_hotel" && $tourfic_hotel_selected_template == "design-3"): ?>
                                <div class="tf-review-date"><?php echo esc_html( wp_date( "F d, Y", strtotime( $tourfic_c_date ) ) ); ?></div>
	                        <?php endif; ?>
                        </div>
                        <div class="tf-review-ratings tf-mt-8">
							<?php echo wp_kses_post( $tourfic_c_rating ); ?>
                        </div>
                        <div class="tf-review-message">
                            <p><?php echo wp_kses_post( $tourfic_c_content ); ?></p>
                        </div>
				        <?php if(get_post_type( $tourfic_post_id ) == "tf_hotel" && $tourfic_hotel_selected_template != "design-3"): ?>
                            <div class="tf-review-date">
                                <ul class="tf-list">
                                    <li><i class="fa-regular fa-clock"></i> <?php echo esc_html( wp_date( "F d, Y", strtotime( $tourfic_c_date ) ) ); ?></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
				<?php
			}

			?>
        </div>
		<?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $tourfic_post_id ) ?? "" ); ?>
		<?php
		if ( ! empty( $tourfic_ratings_for ) ) {
			if ( $tourfic_is_user_logged_in ) {
				if ( in_array( 'li', $tourfic_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
					?>
                    <!-- Replay form  -->
                    <div class="tf-review-form tf-mt-40">
                        <div class="tf-section-head">
                            <h2 class="tf-title tf-section-title"><?php esc_html_e( "Leave a Review", "tourfic" ); ?></h2>
                            <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
                        </div>
						<?php TF_Review::tf_review_form(); ?>
                    </div>
					<?php
				}
			} else {
				if ( in_array( 'lo', $tourfic_ratings_for ) ) {
					?>
                    <!-- Replay form  -->
                    <div class="tf-review-form tf-mt-40">
                        <div class="tf-section-head">
                            <h2 class="tf-title tf-section-title"><?php esc_html_e( "Leave a Review", "tourfic" ); ?></h2>
                            <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
                        </div>
						<?php TF_Review::tf_review_form(); ?>
                    </div>
					<?php
				}
			}
		}
	} else {
        if(get_post_type( $tourfic_post_id ) == "tf_hotel" && $tourfic_hotel_selected_template == "design-3"){
            echo '<div class="tf-review-box">';
            echo '<h5 class="tf-section-title">'. esc_html($tourfic_meta['review-section-title']) .'</h5>';
	        echo '<h4>' . esc_html__( "No Review Available", "tourfic" ) . '</h4>';
            echo '</div>';
        }
		echo '<div class="no-review">';
		if(get_post_type( $tourfic_post_id ) == "tf_hotel" && $tourfic_hotel_selected_template != "design-3") {
			echo '<h4>' . esc_html__( "No Review Available", "tourfic" ) . '</h4>';
		}
		if ( $tourfic_is_user_logged_in ) {

			// Add Review button
			if ( is_array( $tourfic_ratings_for ) && in_array( 'li', $tourfic_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
				?>
                <!-- Replay form  -->
                <div class="tf-review-form tf-mt-40">
                    <div class="tf-section-head">
                        <h2 class="tf-title tf-section-title"><?php esc_html_e( "Leave a Review", "tourfic" ); ?></h2>
                        <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
                    </div>
					<?php TF_Review::tf_review_form(); ?>
                </div>

				<?php
			}
		} else {

			if ( is_array( $tourfic_ratings_for ) && in_array( 'lo', $tourfic_ratings_for ) ) {
				?>
                <!-- Replay form  -->
                <div class="tf-review-form tf-mt-40">
                    <div class="tf-section-head">
                        <h2 class="tf-title tf-section-title"><?php esc_html_e( "Leave a Review", "tourfic" ); ?></h2>
                        <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
                    </div>
					<?php TF_Review::tf_review_form(); ?>
                </div>
				<?php
			}
		}
		// Pending review notice
		echo wp_kses_post( TF_Review::tf_pending_review_notice( $tourfic_post_id ) ?? "" );
		echo '</div>';
	}
} else {
	?>
    <div class="tf-review-container">
		<?php
		// get post id
		$tourfic_post_id = $post->ID;

		if ( get_post_type( $tourfic_post_id ) == "tf_apartment" && $tourfic_apartment_selected_template == "default" ) {
			$tourfic_btn_class = 'tf_btn tf_btn_full';
		} else {
			$tourfic_btn_class = 'tf_btn tf-submit';
		}

		/**
		 * Review query
		 */
		$tourfic_args           = array(
			'post_id' => $tourfic_post_id,
			'status'  => 'approve',
			'type'    => 'comment',
		);
		$tourfic_comments_query = new WP_Comment_Query( $tourfic_args );
		$tourfic_comments       = $tourfic_comments_query->comments;

		if ( $tourfic_comments ) {

			$tourfic_rating_progress_bar = '';
			$tourfic_overall_rate        = [];
			TF_Review::tf_calculate_comments_rating( $tourfic_comments, $tourfic_overall_rate, $total_rating );
			TF_Review::tf_get_review_fields( $fields );

			if ( $tourfic_overall_rate ) {


				foreach ( $tourfic_overall_rate as $tourfic_key => $tourfic_value ) {

					if ( empty( $tourfic_value ) || ! in_array( $tourfic_key, $fields ) ) {
						continue;
					}

					$tourfic_value                  = TF_Review::Tf_average_ratings( $tourfic_value );
					$tourfic_rating_progress_bar .= '<div class="tf-single">';
					$tourfic_rating_progress_bar .= '<div class="tf-text">' . $tourfic_key . '</div>';
					$tourfic_rating_progress_bar .= '<div class="tf-p-bar"><div class="percent-progress" data-width="' . TF_Review::tf_average_rating_percent( $tourfic_value, Helper::tfopt( 'r-base' ) ) . '"></div></div>';
					$tourfic_rating_progress_bar .= '<div class="tf-p-b-rating">' . $tourfic_value . '</div>';
					$tourfic_rating_progress_bar .= '</div>';

				}
			}
			?>

            <div class="tf-total-review">
                <div class="tf-total-average">
                    <div><?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?></div>
                    <span><?php TF_Review::tf_based_on_text( count( $tourfic_comments ) ); ?></span>
                </div>
				<?php
				if ( ! empty( $tourfic_ratings_for ) ) {
					if ( $tourfic_is_user_logged_in ) {
						if ( in_array( 'li', $tourfic_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
							?>
                            <div class="tf-btn-wrap">
                                <button class="<?php echo esc_attr( $tourfic_btn_class ); ?> tf-modal-btn" data-target="#tf-rating-modal">
                                    <i class="fas fa-plus"></i> 
									 <?php echo esc_html( apply_filters( 'tourfic_add_review_button_text', __( 'Add Review', 'tourfic' ) ) );?>
                                </button>
                            </div>
							<?php
						}
					} else {
						if ( in_array( 'lo', $tourfic_ratings_for ) ) {
							?>
                            <div class="tf-btn-wrap">
                                <button class="<?php echo esc_attr( $tourfic_btn_class ); ?> tf-modal-btn" data-target="#tf-rating-modal">
                                    <i class="fas fa-plus"></i> 
									 <?php echo esc_html( apply_filters( 'tourfic_add_review_button_text', __( 'Add Review', 'tourfic' ) ) ); ?>
                                </button>
                            </div>
							<?php
						}
					}
				}
				?>
            </div>
			<?php if ( ! empty( $tourfic_rating_progress_bar ) ) { ?>
                <div class="tf-review-progress-bar">
					<?php echo wp_kses_post( $tourfic_rating_progress_bar ); ?>
                </div>
			<?php } ?>

            <div class="tf-single-review <?php echo esc_attr( get_post_type( $tourfic_post_id ) ) ?>">
				<?php
				if ( $tourfic_comments ) {
					foreach ( $tourfic_comments as $comment ) {

						// Get rating details
						$tourfic_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
						if ( $tourfic_overall_rate == false ) {
							$tourfic_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
							$tourfic_overall_rate = TF_Review::Tf_average_ratings( $tourfic_comment_meta );
						}
						$tourfic_base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
						$tourfic_c_rating  = TF_Review::tf_single_rating_change_on_base( $tourfic_overall_rate, $tourfic_base_rate );

						// Comment details
						$tourfic_c_avatar      = get_avatar( $comment, '56' );
						$tourfic_c_author_name = $comment->comment_author;
						$tourfic_c_date        = $comment->comment_date;
						$tourfic_c_content     = $comment->comment_content;
						global $post_type;
						?>
                        <div class="tf-single-details">
                            <div class="tf-review-avatar"><?php echo wp_kses_post( $tourfic_c_avatar ); ?></div>
                            <div class="tf-review-details">
                                <div class="tf-name"><?php echo esc_html( $tourfic_c_author_name ); ?></div>
                                <div class="tf-date"><?php echo esc_html( $tourfic_c_date ); ?></div>
                                <div class="tf-rating-stars">
									<?php echo wp_kses_post( $tourfic_c_rating ); ?>
                                </div>
								<?php if ( $post_type == 'apartment' ) {
									if ( $tourfic_apartment_selected_template == "default" ) {
										if ( strlen( $tourfic_c_content ) > 120 ) { ?>
                                            <div class="tf-description">
                                                <p><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( $tourfic_c_content, 120 ) ) ?></p>
                                            </div>
                                            <div class="tf-full-description" style="display:none;">
                                                <p><?php echo wp_kses_post( $tourfic_c_content ) ?></p>
                                            </div>
										<?php } else { ?>
                                            <div class="tf-description">
                                                <p><?php echo wp_kses_post( $tourfic_c_content ); ?></p>
                                            </div>
											<?php
										}
									}
								} else { ?>
                                    <div class="tf-description"><p><?php echo wp_kses_post( $tourfic_c_content ); ?></p></div>
								<?php } ?>
								<?php if ( $post_type == 'apartment' && $tourfic_apartment_selected_template == "default" && strlen( $tourfic_c_content ) > 120 ): ?>
                                    <div class="tf-apartment-show-more"><?php esc_html_e( "Show more", "tourfic" ) ?></div>

								<?php endif; ?>
                            </div>
                        </div>
						<?php
					}
				}
				?>
            </div>
			<?php if ( $post_type == "apartment" && $tourfic_apartment_selected_template == 'default' && count( $tourfic_comments ) > 2 ): ?>
                <div class="show-all-review-wrap">
                    <div>
                        <div class="tf-apaartment-show-all">
							<?php esc_html_e( "Show all reviews", "tourfic" ); ?>
                        </div>
                    </div>
                </div>
			<?php endif; ?>

			<?php
			// Review moderation notice
			echo wp_kses_post( TF_Review::tf_pending_review_notice( $tourfic_post_id ) ?? '' );

		} else {

			echo '<div class="no-review">';

			echo '<h4>' . esc_html__( "No Review Available", "tourfic" ) . '</h4>';

			if ( $tourfic_is_user_logged_in ) {

				// Add Review button
				if ( is_array( $tourfic_ratings_for ) && in_array( 'li', $tourfic_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
					?>
                    <div class="tf-btn-wrap">
                        <button class="<?php echo esc_attr( $tourfic_btn_class ); ?> tf-modal-btn" data-target="#tf-rating-modal">
                            <i class="fas fa-plus"></i> 
							 <?php echo esc_html( apply_filters( 'tourfic_add_review_button_text', __( 'Add Review', 'tourfic' ) ) );?>
                        </button>
                    </div>

					<?php
				}

			} else {

				if ( is_array( $tourfic_ratings_for ) && in_array( 'lo', $tourfic_ratings_for ) ) {
					?>
                    <div class="tf-btn-wrap">
                        <button class="<?php echo esc_attr( $tourfic_btn_class ); ?> tf-modal-btn" data-target="#tf-rating-modal">
                            <i class="fas fa-plus"></i> 
							 <?php echo esc_html( apply_filters( 'tourfic_add_review_button_text', __( 'Add Review', 'tourfic' ) ) ); ?>
                        </button>
                    </div>
					<?php
				}
			}
			// Pending review notice
			echo wp_kses_post( TF_Review::tf_pending_review_notice( $tourfic_post_id ) ?? '' );

			echo '</div>';
		}
		?>
    </div>

	<div class="tf-modal" id="tf-rating-modal">
		<div class="tf-modal-dialog">
			<div class="tf-modal-content">
				<div class="tf-modal-header">
						<?php echo wp_kses( apply_filters( 'tf_rating_modal_header_content', '' ), tf_custom_wp_kses_allow_tags() ); ?>
					<a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
				</div>
				<div class="tf-modal-body">
					<div id="tfreview-error-response"></div>
					<?php TF_Review::tf_review_form(); ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
