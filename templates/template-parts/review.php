<?php

/**
 * Template Name: Review Template
 */

// don't load directly
defined('ABSPATH') || exit;

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
?>

<div class="tf-review-container">
    <?php
    global $current_user;

    // Check if user is logged in
    $is_user_logged_in = $current_user->exists();
    $post_id = $post->ID;
    // Get settings value
    $tf_ratings_for = tfopt('r-for') ?? ['li', 'lo'];

    if($comments) {

        $tf_rating_progress_bar    = '';
        $tf_overall_rate = [];
        tf_calculate_comments_rating($comments, $tf_overall_rate, $total_rating);
        tf_get_review_fields($fields);
        
        if ($tf_overall_rate) {

          
            
            foreach ($tf_overall_rate as $key => $value) {

                if (empty($value) || !in_array($key, $fields)) {
                    continue;
                }

                $value        = tf_average_ratings($value);
                $tf_rating_progress_bar .= '<div class="tf-single">';
                $tf_rating_progress_bar .= '<div class="tf-text">' . $key . '</div>';
                $tf_rating_progress_bar .= '<div class="tf-p-bar"><div class="percent-progress" data-width="' . tf_average_rating_percent($value, tfopt('r-base')) . '"></div></div>';
                $tf_rating_progress_bar .= '<div class="tf-p-b-rating">' . $value . '</div>';
                $tf_rating_progress_bar .= '</div>';

            }
        }
        ?>

            <div class="tf-total-review">
                <div class="tf-total-average">
                    <div><?php _e(sprintf('%.1f', $total_rating)); ?></div>
                    <span><?php tf_based_on_text(count($comments)); ?></span>
                </div>           
                <?php
                if (!empty($tf_ratings_for)) {
                    if ($is_user_logged_in) {
                        if (in_array('li', $tf_ratings_for) && !tf_user_has_comments()) {
                        ?>
                        <div class="tf-btn">
                            <button class="tf_button tf-submit btn-styled" data-fancybox data-src="#tourfic-rating" onclick=" tf_load_rating()">
                                <i class="fas fa-plus"></i> <?php _e('Add Review', 'tourfic'); ?>
                            </button>
                        </div>
                        <?php
                        }
                    } else {
                        if (in_array('lo', $tf_ratings_for)) {
                        ?>
                        <div class="tf-btn">
                            <button class="tf_button tf-submit btn-styled" data-fancybox data-src="#tourfic-rating" onclick=" tf_load_rating()">
                                <i class="fas fa-plus"></i> <?php _e('Add Review', 'tourfic') ?>
                            </button>
                            </div>
                        <?php
                        }
                    }
                }
                ?>
            </div>
            <?php if (!empty($tf_rating_progress_bar)) { ?>
            <div class="tf-review-progress-bar">
                <?php _e($tf_rating_progress_bar); ?>
            </div>
            <?php } ?>

            <div class="tf-single-review">
                <?php
                if ( $comments ) {
                    foreach ( $comments as $comment ) {

                        // Get rating details
                        $tf_overall_rate = get_comment_meta($comment->comment_ID, TF_TOTAL_RATINGS, true);
                        if ($tf_overall_rate == false) {
                            $tf_comment_meta = get_comment_meta($comment->comment_ID, TF_COMMENT_META, true);
                            $tf_overall_rate = tf_average_ratings($tf_comment_meta);
                        }
                        $base_rate = get_comment_meta($comment->comment_ID, TF_BASE_RATE, true);
                        $c_rating = tf_single_rating_change_on_base($tf_overall_rate, $base_rate);

                        // Comment details
                        $c_avatar = get_avatar($comment, '56');
                        $c_author_name = $comment->comment_author;
                        $c_date = $comment->comment_date;
                        $c_content = $comment->comment_content;
                        ?>                  
                        <div class="tf-single-details">
                            <div class="tf-review-avatar"><?php echo $c_avatar; ?></div>
                            <div class="tf-review-details">
                                <div class="tf-name"><?php echo $c_author_name; ?></div>
                                <div class="tf-date"><?php echo $c_date; ?></div>
                                <div class="tf-rating-stars">
                                    <?php echo $c_rating;  ?>
                                </div>
                                <div class="tf-description"><?php echo $c_content; ?></div>
                            </div>
                        </div>                  
                    <?php
                    }
                }
                ?>
            </div>

        <?php
        // Review moderation notice
        echo tf_pending_review_notice($post_id);

    } else {

        echo '<div class="no-review">';

        echo '<h4>' .__("No Review Available", "tourfic"). '</h4>';

        if ($is_user_logged_in) {

            // Add Review button
            if (in_array('li', $tf_ratings_for) && !tf_user_has_comments()) {
                ?>
                <div class="tf-btn">
                    <button class="tf_button tf-submit btn-styled" data-fancybox data-src="#tourfic-rating" onclick=" tf_load_rating()">
                        <i class="fas fa-plus"></i> <?php _e('Add Review', 'tourfic'); ?>
                    </button>
                </div>
        
                <?php
            } else {
                // Pending review notice
                echo tf_pending_review_notice($post_id);
            }

        } else {

            if (in_array('lo', $tf_ratings_for)) {
            ?>
            <div class="tf-btn">
                <button class="tf_button tf-submit btn-styled" data-fancybox data-src="#tourfic-rating" onclick=" tf_load_rating()">
                    <i class="fas fa-plus"></i> <?php _e('Add Review', 'tourfic') ?>
                </button>
                </div>
            <?php
            }

        }

        echo '</div>';
    }
    ?>
</div>

<div style="display: none;" id="tourfic-rating">
    <div id="tfreview-error-response"></div>
    <?php tf_review_form(); ?>
</div>