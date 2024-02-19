<?php

/**
 * Template Name: Review Template
 */

// don't load directly
defined( 'ABSPATH' ) || exit;

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
$is_user_logged_in = $current_user->exists();
$post_id           = $post->ID;
// Get settings value
$tf_ratings_for = tfopt( 'r-for' ) ?? [ 'li', 'lo' ];

if( get_post_type($post_id) == 'tf_tours' ){

	$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
	// Single Template Check
	$tf_tour_layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
	if("single"==$tf_tour_layout_conditions){
    	$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
	}
	$tf_tour_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-tour'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-tour'] : 'design-1';

	$tf_tour_selected_check = !empty($tf_tour_single_template) ? $tf_tour_single_template : $tf_tour_global_template;

	$tf_tour_selected_template = $tf_tour_selected_check;
	
}
if( get_post_type($post_id) == 'tf_hotel' ){

	$meta = get_post_meta( $post_id, 'tf_hotels_opt', true );
	// Single Template Check
	$tf_hotel_layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
	if("single"==$tf_hotel_layout_conditions){
    	$tf_hotel_single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
	}
	$tf_hotel_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-hotel'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-hotel'] : 'design-1';

	$tf_hotel_selected_check = !empty($tf_hotel_single_template) ? $tf_hotel_single_template : $tf_hotel_global_template;

	$tf_hotel_selected_template = $tf_hotel_selected_check;

}

if( ( get_post_type($post_id) == 'tf_tours' && $tf_tour_selected_template == "design-1" ) || ( get_post_type($post_id) == "tf_hotel" && $tf_hotel_selected_template == "design-1" ) ){

if ( $comments ) {
	$tf_overall_rate        = [];
	tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
	tf_get_review_fields( $fields );
?>
<div class="tf-review-data tf-box">
<div class="tf-review-data-inner tf-flex tf-flex-gap-24">
	<div class="tf-review-data">
		<div class="tf-review-data-average">
			<p><?php _e( sprintf( '%.1f', $total_rating ) ); ?></p>
		</div>
		<div class="tf-review-all-info">
			<ul class="tf-list">
				<li><i class="fa-solid fa-circle-check"></i><?php _e("From", "tourfic"); ?> <?php tf_based_on_text( count( $comments ) ); ?></li>
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
			$value = tf_average_ratings( $value );
			?>
			<div class="tf-progress-item">
				<div class="tf-progress-bar">
					<span class="percent-progress" style="width: <?php echo tf_average_rating_percent( $value, tfopt( 'r-base' ) ); ?>%"></span>
				</div>
				<div class="tf-review-feature-label tf-flex tf-flex-space-bttn">
					<p class="feature-label"><?php esc_html_e( $key, "tourfic" ); ?></p>
					<p class="feature-rating"> <?php echo $value; ?></p>
				</div>
			</div>
			<?php } } ?>
		
		</div>
	</div>
</div>
</div>
<!-- Tourfic review reply -->
<div class="tf-review-reply tf-mt-50">
	<div class="tf-section-head">
		<h2 class="tf-title tf-section-title"><?php _e("Showing", "tourfic"); ?> <span><?php echo count($comments); ?></span> <?php _e("Review", "tourfic"); ?></h2>
	</div>
	<?php
	foreach ( $comments as $comment ) {

		// Get rating details
		$tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
		if ( $tf_overall_rate == false ) {
			$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
			$tf_overall_rate = tf_average_ratings( $tf_comment_meta );
		}
		$base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
		$c_rating  = tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

		// Comment details
		$c_avatar      = get_avatar( $comment, '56' );
		$c_author_name = $comment->comment_author;
		$c_date        = $comment->comment_date;
		$c_content     = $comment->comment_content;
		?>
		<!-- reviews and replies -->
		<div class="tf-review-reply-data tf-flex-gap-24 tf-flex">
			<div class="tf-review-author">
				<?php echo $c_avatar; ?>
			</div>
			<div class="tf-review-details">
				<div class="tf-review-author-name">
					<h3><?php echo $c_author_name; ?></h3>
				</div>
				<div class="tf-review-ratings tf-mt-8">
				<?php echo $c_rating; ?>
				</div>
				<div class="tf-review-message">
					<p><?php echo $c_content; ?></p>
				</div>
				<div class="tf-review-date">
					<ul class="tf-list">
						<li><i class="fa-regular fa-clock"></i> <?php echo date("F d, Y", strtotime($c_date)); ?></li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}
	
	?>
</div>
<?php
// Review moderation notice
echo tf_pending_review_notice( $post_id );
?>
<?php
if ( ! empty( $tf_ratings_for ) ) {
	if ( $is_user_logged_in ) {
		if ( in_array( 'li', $tf_ratings_for ) && ! tf_user_has_comments() ) {
			?>
			<!-- Replay form  -->
			<div class="tf-review-form tf-mt-40">
				<div class="tf-section-head">
					<h2 class="tf-title tf-section-title"><?php _e("Leave a Review", "tourfic"); ?></h2>
					<p><?php _e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
				</div>
				<?php tf_review_form(); ?>
			</div>
			<?php
		}
	} else {
		if ( in_array( 'lo', $tf_ratings_for ) ) {
			?>
			<!-- Replay form  -->
			<div class="tf-review-form tf-mt-40">
				<div class="tf-section-head">
					<h2 class="tf-title tf-section-title"><?php _e("Leave a Review", "tourfic"); ?></h2>
					<p><?php _e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
				</div>
				<?php tf_review_form(); ?>
			</div>
			<?php
		}
	}
}
}else{
	echo '<div class="no-review">';
	echo '<h4>' . __( "No Review Available", "tourfic" ) . '</h4>';
	if ( $is_user_logged_in ) {

		// Add Review button
		if ( is_array( $tf_ratings_for ) && in_array( 'li', $tf_ratings_for ) && ! tf_user_has_comments() ) {
			?>
			<!-- Replay form  -->
			<div class="tf-review-form tf-mt-40">
				<div class="tf-section-head">
					<h2 class="tf-title tf-section-title"><?php _e("Leave a Review", "tourfic"); ?></h2>
					<p><?php _e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
				</div>
				<?php tf_review_form(); ?>
			</div>

			<?php
		}
	} else {

		if ( is_array( $tf_ratings_for ) && in_array( 'lo', $tf_ratings_for ) ) {
			?>
			<!-- Replay form  -->
			<div class="tf-review-form tf-mt-40">
				<div class="tf-section-head">
					<h2 class="tf-title tf-section-title"><?php _e("Leave a Review", "tourfic"); ?></h2>
					<p><?php _e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
				</div>
				<?php tf_review_form(); ?>
			</div>
			<?php
		}
	}
	// Pending review notice
	echo tf_pending_review_notice( $post_id );
	echo '</div>';
} 
}else{
?>
<div class="tf-review-container">
	<?php
	if ( $comments ) {

		$tf_rating_progress_bar = '';
		$tf_overall_rate        = [];
		tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
		tf_get_review_fields( $fields );

		if ( $tf_overall_rate ) {


			foreach ( $tf_overall_rate as $key => $value ) {

				if ( empty( $value ) || ! in_array( $key, $fields ) ) {
					continue;
				}

				$value                  = tf_average_ratings( $value );
				$tf_rating_progress_bar .= '<div class="tf-single">';
				$tf_rating_progress_bar .= '<div class="tf-text">' . $key . '</div>';
				$tf_rating_progress_bar .= '<div class="tf-p-bar"><div class="percent-progress" data-width="' . tf_average_rating_percent( $value, tfopt( 'r-base' ) ) . '"></div></div>';
				$tf_rating_progress_bar .= '<div class="tf-p-b-rating">' . $value . '</div>';
				$tf_rating_progress_bar .= '</div>';

			}
		}
		?>

        <div class="tf-total-review">
            <div class="tf-total-average">
                <div><?php _e( sprintf( '%.1f', $total_rating ) ); ?></div>
                <span><?php tf_based_on_text( count( $comments ) ); ?></span>
            </div>
			<?php
			if ( ! empty( $tf_ratings_for ) ) {
				if ( $is_user_logged_in ) {
					if ( in_array( 'li', $tf_ratings_for ) && ! tf_user_has_comments() ) {
						?>
                        <div class="tf-btn">
                            <button class="tf_button tf-submit btn-styled" data-fancybox data-src="#tourfic-rating" onclick=" tf_load_rating()">
                                <i class="fas fa-plus"></i> <?php _e( 'Add Review', 'tourfic' ); ?>
                            </button>
                        </div>
						<?php
					}
				} else {
					if ( in_array( 'lo', $tf_ratings_for ) ) {
						?>
                        <div class="tf-btn">
                            <button class="tf_button tf-submit btn-styled" data-fancybox data-src="#tourfic-rating" onclick=" tf_load_rating()">
                                <i class="fas fa-plus"></i> <?php _e( 'Add Review', 'tourfic' ) ?>
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
				<?php _e( $tf_rating_progress_bar ); ?>
            </div>
		<?php } ?>

        <div class="tf-single-review">
			<?php
			if ( $comments ) {
				foreach ( $comments as $comment ) {

					// Get rating details
					$tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
					if ( $tf_overall_rate == false ) {
						$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
						$tf_overall_rate = tf_average_ratings( $tf_comment_meta );
					}
					$base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
					$c_rating  = tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

					// Comment details
					$c_avatar      = get_avatar( $comment, '56' );
					$c_author_name = $comment->comment_author;
					$c_date        = $comment->comment_date;
					$c_content     = $comment->comment_content;
					?>
                    <div class="tf-single-details">
                        <div class="tf-review-avatar"><?php echo $c_avatar; ?></div>
                        <div class="tf-review-details">
                            <div class="tf-name"><?php echo $c_author_name; ?></div>
                            <div class="tf-date"><?php echo $c_date; ?></div>
                            <div class="tf-rating-stars">
								<?php echo $c_rating; ?>
                            </div>
                            <div class="tf-description"><p><?php echo $c_content; ?></p></div>
                        </div>
                    </div>
					<?php
				}
			}
			?>
        </div>

		<?php
		// Review moderation notice
		echo tf_pending_review_notice( $post_id );

	} else {

		echo '<div class="no-review">';

		echo '<h4>' . __( "No Review Available", "tourfic" ) . '</h4>';

		if ( $is_user_logged_in ) {

			// Add Review button
			if ( is_array( $tf_ratings_for ) && in_array( 'li', $tf_ratings_for ) && ! tf_user_has_comments() ) {
				?>
                <div class="tf-btn">
                    <button class="tf_button tf-submit btn-styled" data-fancybox data-src="#tourfic-rating" onclick=" tf_load_rating()">
                        <i class="fas fa-plus"></i> <?php _e( 'Add Review', 'tourfic' ); ?>
                    </button>
                </div>

				<?php
			}

		} else {

			if ( is_array( $tf_ratings_for ) && in_array( 'lo', $tf_ratings_for ) ) {
				?>
                <div class="tf-btn">
                    <button class="tf_button tf-submit btn-styled" data-fancybox data-src="#tourfic-rating" onclick=" tf_load_rating()">
                        <i class="fas fa-plus"></i> <?php _e( 'Add Review', 'tourfic' ) ?>
                    </button>
                </div>
				<?php
			}
		}
		// Pending review notice
		echo tf_pending_review_notice( $post_id );

		echo '</div>';
	}
	?>
</div>

<div style="display: none;" id="tourfic-rating">
    <div id="tfreview-error-response"></div>
	<?php tf_review_form(); ?>
</div>
<?php } ?>