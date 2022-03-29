<?php

/**
 * Steps
 * 0. Add styles to frontend
 * 1. Create the rating interface.
 * 2. Saving the user’s input.
 * 3. Making the rating required (optional).
 * 4. Display  the rating on a submitted comment.
 * 5. Get the average rating of a post.
 */
// 0. Add styles to frontend
function tourfic_review_add_style()
{
    wp_enqueue_style('dashicons');
    wp_enqueue_style('tourfic-review-styles', TF_ASSETS_URL . 'css/review.css', null, '');
}

add_action('wp_enqueue_scripts', 'tourfic_review_add_style');

// 1. Create the rating interface
function tourfic_get_review_form()
{
    $fields = [
        'sleep',
        'location',
        'services',
        'cleanliness',
        'rooms'
    ];
    //tours and hotel comment conditional markup
    if ('tf_tours' === get_post_type()) {
        $div_start = "<div class='comment_form_fields'>";
        $div_end   = "</div>";
    } else {
        $div_start = '';
        $div_end   = '';
    }

    //Declare Vars
    $comment_send     = __(
        'Submit',
        'tourfic'
    );
    $comment_reply    = __('Write a Review', 'tourfic');
    $comment_reply_to = __('Reply', 'tourfic');

    $comment_email     = 'E-Mail';
    $comment_body      = 'Comment';
    $comment_cookies_1 = ' By commenting you accept the';
    $comment_cookies_2 = ' Privacy Policy';

    $comment_before = 'Registration isn\'t required.';

    $comment_cancel = 'Cancel Reply';


    $comment_meta = tourfic_generate_review_fields($fields);


    //Array
    $comments_args = array(
        //Define Fields
        'fields'               => array(
            'author'  => '<div class="author-email"><p class="comment-form-author"><input type="text" id="author" name="author" aria-required="true" placeholder="{$comment_author}"/></p>',
            'email'   => '<p class="comment-form-email"><input type="email" id="email" name="email" placeholder="' . $comment_email . '"/></p></div>',
            'cookies' => '<input type="checkbox" required>' . $comment_cookies_1 . '<a href="' . get_privacy_policy_url() . '">' . $comment_cookies_2 . '</a>' . $div_end
        ),
        // Change the title of send button
        'label_submit'         => $comment_send,
        // Change the title of the reply section
        'title_reply'          => $comment_reply,
        // Change the title of the reply section
        'title_reply_to'       => $comment_reply_to,
        // Reply html start
        'title_reply_before'   => '<div id="reply-title" class="comment-reply-title">',
        // Reply html end
        'title_reply_after'    => '<span class="faq-indicator"> <i class="fa fa-angle-up" aria-hidden="true"></i> <i class="fa fa-angle-down" aria-hidden="true"></i> </span></div>',
        //Cancel Reply Text
        'cancel_reply_link'    => $comment_cancel,
        // Redefine your own textarea (the comment body).
        'comment_field'        => "{$comment_meta}{$div_start}<p class=\"comment-form-comment\"><textarea id=\"comment\" name=\"comment\" aria-required=\"true\" placeholder=\"{$comment_body}\"></textarea></p>",
        //Message Before Comment
        'comment_notes_before' => $comment_before,
        // Remove "Text or HTML to be displayed after the set of comment fields".
        'comment_notes_after'  => '',
        //Submit Button ID
        'id_submit'            => 'comment-submit',
        //Submit Button html
        'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
    );

    comment_form($comments_args);
}


/**
 * Generate review inputs for fields
 *
 * @param array $fields
 *
 * @return string
 */
function tourfic_generate_review_fields(array $fields): string
{
    $html = '';

    foreach ($fields as $field) {
        $html .= sprintf('<label for="rating">%s</label>', wc_strtoupper($field));
        $html .= sprintf('<fieldset class="comments-rating"> <div class="rating-container">%s </div> </fieldset>', tourfic_generate_stars($field));
    }

    return $html;
}

/**
 * Generate stars for input fields
 *
 * @param string $key
 *
 * @return string
 */
function tourfic_generate_stars(string $key): string
{
    $html = '';
    foreach (array_reverse(range(0, 5, 1)) as $i) {
        $class = $i == 0 ? 'star-cb-clear' : '';
        $html  .= "<input type=\"radio\" id=\"{$key}-{$i}\" class=\"{$class}\" name=\"tf_comment_meta[{$key}]\" value=\"{$i}\" required><label for=\"{$key}-{$i}\">{$i}</label>";
    }

    return $html;
}

/**
 * 2. Saving the user’s input
 *
 * @param $comment_id
 */
function tf_save_rating($comment_id)
{

    if ((isset($_POST['tf_comment_meta'])) && ('' !== $_POST['tf_comment_meta'])) {
        $tf_comment_meta = $_POST['tf_comment_meta'];
    }
    add_comment_meta($comment_id, 'tf_comment_meta', $tf_comment_meta);
}

add_action('comment_post', 'tf_save_rating');

/**
 * 3. Making the rating required (optional)
 *
 * @param $commentdata
 *
 * @return mixed
 */
function tourfic_make_rating_required($commentdata)
{
    if (!is_admin() && (!isset($_POST['tf_comment_meta']))) {
        wp_die(__('Error: You did not add a rating. Hit the Back button on your Web browser and resubmit your comment with a rating.'));
    }

    return $commentdata;
}

add_filter('preprocess_comment', 'tourfic_make_rating_required');

// 4. Display the rating on a submitted comment.

/**
 * @param $comment_text
 *
 * @return mixed|string
 */
function tf_rating_display($comment_text)
{
    if ($rating = get_comment_meta(get_comment_ID(), 'tf_comment_meta', true)) {

        $stars = '<div class="tf_comment-metas">';

        foreach ($rating as $key => $value) {
            $stars .= '<div class="comment-meta"><label class="tf_comment_meta-key">' . strtoupper($key) . '</label><div class="tf_comment_meta-ratings">';
            foreach (range(1, 5, 1) as $i) {
                $stars .= ($value >= $i) ? '<i class="dashicons dashicons-star-filled"></i>' : '<i class="dashicons dashicons-star-empty"></i>';
            }
            $stars .= '</div></div>';
        }
        $stars .= '</div>';

        return $stars . $comment_text;
    }

    return $comment_text;
}

add_filter('comment_text', 'tf_rating_display');


/**
 * Show Comment meta
 * 
 * @param $author
 *
 * @return mixed|string
 */
function tourfic_attach_city_to_author($author)
{


    if ($rating = get_comment_meta(get_comment_ID(), 'tf_comment_meta', true)) {

        $stars = '<div class="tf_comment-metas">';

        foreach ($rating as $key => $value) {
            $stars .= '<div class="comment-meta"><label class="tf_comment_meta-key">' . strtoupper($key) . '</label><div class="tf_comment_meta-ratings">';
            foreach (range(1, 5, 1) as $i) {
                $stars .= ($value >= $i) ? '<i class="dashicons dashicons-star-filled"></i>' : '<i class="dashicons dashicons-star-empty"></i>';
            }
            $stars .= '</div></div>';
        }
        $stars .= '</div>';

        return  $author . $stars;
    }

    return $author;
}
// add_filter('get_comment_author_link', 'tourfic_attach_city_to_author');

/**
 * @param array $a
 *
 * @return string
 */
function tourfic_avg_ratings(array $a = array()): string
{
    if (!$a) {
        return 'N/A';
    }
    $average = array_sum($a) / count($a);
    return sprintf('%.1f', $average);
}


/**
 * @param int $val
 * @param int $total
 *
 * @return string
 */
function tourfic_avg_rating_percent(int $val = 0, int $total = 5): string
{
    $percent = ($val * 100) / $total;
    return sprintf("%.2f", $percent);
}
