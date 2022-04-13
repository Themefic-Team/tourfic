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
    $comments = get_comments(array('post_id' => get_the_ID(), 'status' => 'approve'));

    $tf_rating_progress_bar    = '';
    $tf_overall_rate = tf_calculate_comments_rating($comments);


    if ($tf_overall_rate) {

        foreach ($tf_overall_rate as $key => $value) {

            if (empty($value)) {
                continue;
            }
            $value        = tf_average_rating_change_on_base(tf_average_ratings($value));
            $tf_rating_progress_bar .= '<div class="tf-single">';
            $tf_rating_progress_bar .= '<div class="tf-text">' . $key . '</div>';
            $tf_rating_progress_bar .= '<div class="tf-p-bar"><div class="percent-progress" data-width="' . tf_average_rating_percent($value, tfopt('r-base')) . '"></div></div>';
            $tf_rating_progress_bar .= '<div class="tf-p-b-rating">' . $value . '</div>';
            $tf_rating_progress_bar .= '</div>';

        }
    }

    $tf_ratings_for = tfopt('r-for') ?? ['li', 'lo'];

    if (count($comments) > 0 && get_post_type() == 'tf_hotel') {
    ?>

        <div class="tf-total-review">
            <div class="tf-total-average">
                <div><?php _e(tf_average_rating_change_on_base(tf_average_ratings($tf_overall_rate ?? []))); ?></div>
                <span><?php tf_based_on_text(count($comments)); ?></span>
            </div>           
            <?php
            if (!empty($tf_ratings_for)) {
                if (is_user_logged_in()) {
                    if (in_array('li', $tf_ratings_for) && !tf_user_has_comments()) {
                    ?>
                        <button data-fancybox data-src="#tourfic-rating" onclick=" tf_load_rating()">
                            <i class="fas fa-plus"></i> <?php _e('Add Review', TFD); ?>
                        </button>

                    <?php
                    }
                } else {
                    if (in_array('lo', $tf_ratings_for)) {
                    ?>
                        <button data-fancybox data-src="#tourfic-rating" onclick=" tf_load_rating()">
                            <i class="fas fa-plus"></i> <?php _e('Add Review', TFD) ?>
                        </button>
                    <?php
                    }
                }
            }
            ?>
        </div>
        <div class="tf-review-progress-bar">
            <?php _e($tf_rating_progress_bar); ?>
        </div>

        <div class="tf-single-review">
            <div class="tf-single-details">
                <div class="tf-review-avatar"></div>
                <div class="tf-review-details">
                    <div class="tf-name"></div>
                    <div class="tf-date"></div>
                    <div class="tf-rating-stars">

                    </div>
                    <div class="tf-description"></div>
                </div>
            </div>
        </div>

        <?php
        if (get_comment_pages_count() > 1 && get_option('page_comments')) {
        ?>
            <nav id="comment-nav-above" class="navigation comment-navigation" aria-label="<?php esc_attr_e('Comments Navigation', TFD); ?>">
                <h3 class="screen-reader-text"><?php echo esc_html(astra_default_strings('string-comment-navigation-next', false)); ?></h3>
                <div class="nav-links">

                    <div class="nav-previous"><?php previous_comments_link(astra_default_strings('string-comment-navigation-previous', false)); ?></div>
                    <div class="nav-next"><?php next_comments_link(astra_default_strings('string-comment-navigation-next', false)); ?></div>

                </div><!-- .nav-links -->
            </nav><!-- #comment-nav-above -->
        <?php } ?>

        <ol class="tf-comment-list">
            <?php
            wp_list_comments(
                array(
                    'callback'  => 'tf_single_review',
                    'style'     => 'ol',
                    'type'      => 'comment',
                    'max_depth' => 1,
                )
            );
            ?>
        </ol><!-- .ast-comment-list -->

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) { ?>
            <nav id="comment-nav-below" class="navigation comment-navigation" aria-label="<?php esc_attr_e('Comments Navigation', TFD); ?>">
                <h3 class="screen-reader-text"><?php echo esc_html(astra_default_strings('string-comment-navigation-next', false)); ?></h3>
                <div class="nav-links">

                    <div class="nav-previous"><?php previous_comments_link(astra_default_strings('string-comment-navigation-previous', false)); ?></div>
                    <div class="nav-next"><?php next_comments_link(astra_default_strings('string-comment-navigation-next', false)); ?></div>

                </div><!-- .nav-links -->
            </nav><!-- #comment-nav-below -->
        <?php } ?>

    <?php } else { ?>
        <?php
        // Review Button
        if (!empty($tf_ratings_for)) {
            if (is_user_logged_in()) {
                if (in_array('li', $tf_ratings_for) && !tf_user_has_comments()) { ?>
                    <button data-fancybox data-src="#tourfic-rating" class="tf_button" onclick=" tf_load_rating()"><i class="fas fa-plus"></i> <?php _e('Add a review', TFD) ?></button>

                <?php
                }
            } else {
                if (in_array('lo', $tf_ratings_for)) {
                ?>
                    <button data-fancybox data-src="#tourfic-rating" class="tf_button" onclick=" tf_load_rating()"><i class="fas fa-plus"></i> <?php _e('Add a review', TFD) ?></button>

        <?php
                }
            }
        }

        ?>

    <?php }


    // If comments are closed and there are comments, let's leave a little note, shall we?
    if (!comments_open() && count($comments) && post_type_supports(get_post_type(), 'comments')) {
    ?>
        <p class="no-comments"><?php echo __('Comments are closed.', TFD) ?></p>
    <?php } ?>

    <div style="display: none;" id="tourfic-rating">
        <?php tf_review_form(); ?>
    </div>

</div><!-- #comments -->
