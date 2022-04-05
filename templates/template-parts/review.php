<?php

/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Tourfic
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="tf_comments-area">
    <?php
    $comments = get_comments(array('post_id' => get_the_ID(), 'status'       => 'approve'));

    $tf_extr_html = '';
    $tf_overall_rate = tf_calculate_comments_rating($comments);


    if ($tf_overall_rate) {
        $tf_extr_html .= '<div class="tf_comment-metas">';
        foreach ($tf_overall_rate as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $value = tf_average_ratings($value);
            $tf_extr_html .= '<div class="comment-meta">';
            $tf_extr_html .= '<label class="tf_comment_meta-key">' . $key . '</label>';
            $tf_extr_html .= '<div class="tf_comment_meta-percent"><div class="percent-progress" data-width="' . tf_average_rating_percent($value) . '"></div></div>';
            $tf_extr_html .= '<div class="tf_comment_meta-ratings">' . $value . '</div>';
            $tf_extr_html .= '</div>';
        }
        $tf_extr_html .= '</div>';
    }

    if (count($comments) > 0 && get_post_type() == 'tf_hotel') { ?>
        <div class="tf-comments-count-wrapper">

            <div class="tf-overall-ratings">
                <div class="overall-rate">
                    <span class="tf_button"> <?php _e(tf_average_ratings($tf_overall_rate ?? [])); ?> </span>
                    <?php
                    tf_based_on_text(count($comments));
                    ?>
                </div>

                <div class="based-on-title">
                    <?php
                    // Review Button
                    if (is_user_logged_in()) {
                        if (tfopt('r-for') && in_array('li', tfopt('r-for'))) {        ?>
                            <button data-fancybox data-src="#tourfic-rating" class="tf_button" onclick=" tf_load_rating()"><i class="fas fa-plus"></i> <?php _e('Add a review', 'tourfic') ?></button>

                        <?php
                        }
                    } else {
                        if (tfopt('r-for') && in_array('lo', tfopt('r-for'))) {
                        ?>
                            <button data-fancybox data-src="#tourfic-rating" class="tf_button" onclick=" tf_load_rating()"><i class="fas fa-plus"></i> <?php _e('Add a review', 'tourfic') ?></button>

                    <?php
                        }
                    }
                    ?>

                </div>
            </div>

            <div class="tf-extra-ratings">
                <?php _e($tf_extr_html); ?>
            </div>

        </div>

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) { ?>
            <nav id="comment-nav-above" class="navigation comment-navigation" aria-label="<?php esc_attr_e('Comments Navigation', 'tourfic'); ?>">
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
                    'callback' => 'tf_comment_callback',
                    'style'    => 'ol',
                    'type'      => 'comment',
                    'max_depth' => 1,
                )
            );
            ?>
        </ol><!-- .ast-comment-list -->

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) { ?>
            <nav id="comment-nav-below" class="navigation comment-navigation" aria-label="<?php esc_attr_e('Comments Navigation', 'tourfic'); ?>">
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
        if (is_user_logged_in()) {
            if (tfopt('r-for') && in_array('li', tfopt('r-for'))) {        ?>
                <button data-fancybox data-src="#tourfic-rating" class="tf_button" onclick=" tf_load_rating()"><i class="fas fa-plus"></i> <?php _e('Add a review', 'tourfic') ?></button>

            <?php
            }
        } else {
            if (tfopt('r-for') && in_array('lo', tfopt('r-for'))) {
            ?>
                <button data-fancybox data-src="#tourfic-rating" class="tf_button" onclick=" tf_load_rating()"><i class="fas fa-plus"></i> <?php _e('Add a review', 'tourfic') ?></button>

        <?php
            }
        }
        ?>

    <?php }



    // If comments are closed and there are comments, let's leave a little note, shall we?
    if (!comments_open() && count($comments) && post_type_supports(get_post_type(), 'comments')) {
    ?>
        <p class="no-comments"><?php echo __('Comments are closed.', 'tourfic') ?></p>
    <?php } ?>

    <div style="display: none;" id="tourfic-rating">
        <?php tf_get_review_form(); ?>
    </div>

</div><!-- #comments -->
