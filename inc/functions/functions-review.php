<?php
const TF_COMMENT_META  = 'tf_comment_meta';
const TF_TOTAL_RATINGS = 'tf_total_ratings';
const TF_BASE_RATE     = 'tf_base_rate';
/**
 * Steps
 * 0. Add styles to frontend
 * 1. Create the rating interface.
 * 2. Saving the user’s input.
 * 3. Making the rating required (optional).
 * 4. Display  the rating on a submitted comment.
 * 5. Get the average rating of a post.
 * 6. Update the average rating of a post when comment is updated
 */
// 0. Add styles to frontend
function tf_review_add_style()
{
    wp_enqueue_style('dashicons');
    wp_enqueue_style('tourfic-review-styles', TF_ASSETS_URL . 'css/review.css', null, '');
}

add_action('wp_enqueue_scripts', 'tf_review_add_style', 99999);

/**
 * Review submit form
 * 
 * Popup
 */
function tf_get_review_form() {

    /**
     * Default fields until user save from option panel
     */
    $default_hotels_field  = [
        ["r-field-type" => "Staff"],
        ["r-field-type" => "Facilities"],
        ["r-field-type" => "Cleanliness"],
        ["r-field-type" => "Comfort"],
        ["r-field-type" => "Value for money"],
        ["r-field-type" => "Location"],
    ];
    $default_tours_field = [
        ["r-field-type" => "Guide"],
        ["r-field-type" => "Transportation"],
        ["r-field-type" => "Value for money"],
        ["r-field-type" => "Safety"]
    ];

    // If user does not have fields from settings, default fields will be loaded
    $tfopt_hotels = !empty(tfopt('r-hotel')) ? tfopt('r-hotel') : $default_hotels_field;
    $tfopt_tours  = !empty(tfopt('r-tour')) ? tfopt('r-tour') : $default_tours_field;

    $fields = 'tf_tours' === get_post_type() ? $tfopt_tours : $tfopt_hotels;

    $fields = array_map(function ($i) {
        return $i['r-field-type'];
    }, $fields);
    
    //tours and hotel comment conditional markup
    if ('tf_tours' === get_post_type()) {
        $div_start = "<div class='comment_form_fields'>";
        $div_end   = "</div>";
    } else {
        $div_start = '';
        $div_end   = '';
    }
    //Declare Vars
    $comment_send      = __('Submit', TFD);
    $comment_reply     = __('Write a Review', TFD);
    $comment_reply_to  = __('Reply', TFD);
    $comment_author    = __('Your Name', TFD);
    $comment_email     = __('Email Address', TFD);
    $comment_body      = __('Review Description', TFD);
    $comment_cookies_1 = __(' By commenting you accept the', TFD);
    $comment_cookies_2 = __(' Privacy Policy', TFD);
    $comment_before    = __('', TFD);
    $comment_cancel    = __('Cancel Reply', TFD);
    $comment_meta      = tf_generate_review_fields($fields);
    //Array
    $comments_args = [
        //Define Fields
        'fields'               => [
            'author'  => '<div class="tf-visitor-info"><div><input type="text" id="author" name="author" aria-required="true" placeholder="' . $comment_author . '"/></div>',
            'email'   => '<div><input type="email" id="email" name="email" placeholder="' . $comment_email . '"/></div></div>',
            'cookies' => '',
        ],
        'class_container' => 'tf-review-form-container',
        'class_form' => 'tf-review-form',
        // Change the title of send button
        'label_submit'         => $comment_send,
        // Change the title of the reply section
        'title_reply'          => null,
        // Change the title of the reply section
        'title_reply_to'       => $comment_reply_to,
        // Reply html start
        'title_reply_before'   => '<div id="reply-title" class="comment-reply-title" style="display:none">',
        // Reply html end
        'title_reply_after'    => '<span class="faq-indicator"> <i class="fa fa-angle-up" aria-hidden="true"></i> <i class="fa fa-angle-down" aria-hidden="true"></i> </span></div>',
        //Cancel Reply Text
        'cancel_reply_link'    => $comment_cancel,
        // Redefine your own textarea (the comment body).
        'comment_field'        => "{$comment_meta}<div class=\"review-desc\"><textarea id=\"comment\" name=\"comment\" aria-required=\"true\" placeholder=\"{$comment_body}\"></textarea></div>",
        //Message Before Comment
        'comment_notes_before' => $comment_before,
        // Remove "Text or HTML to be displayed after the set of comment fields".
        'comment_notes_after'  => '',
        //Submit Button ID
        'id_submit'            => 'comment-submit',
        // The comment submit element class attribute. Default 'submit'.
        // 'class_submit' => 'tf_button',
        //Submit Button html
        'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" value="%4$s" />',
        'submit_field' => '<div class="tf-review-submit">%1$s %2$s</div>',
    ];
    comment_form($comments_args);
}

/**
 * Generate review inputs for fields
 *
 * @param array $fields
 *
 * @return string
 */
function tf_generate_review_fields(array $fields): string
{
    $html = '<div class="tf-rating-wrapper">';
    foreach ($fields as $field) {
        if (empty($field)) {
            continue;
        }
        $html .= '<div class="tf-single-rating">';
        $html .= sprintf('<label for="rating">%s</label>', $field);
        $html .= sprintf('<div class="ratings-container">%s </div>', tf_generate_stars($field));
        $html .= '</div>';
    }
    $html .= '</div>';

    return $html;
}

/**
 * Generate stars for input fields
 *
 * @param string $key
 *
 * @return string
 */
function tf_generate_stars(string $key): string
{
    $limit = tfopt('r-base') ?? 5;
    $html  = '';
    foreach (array_reverse(range(0, $limit, 1)) as $i) {
        $class = $i == 0 ? 'star-cb-clear' : '';
        $html  .= "<input type=\"radio\" id=\"{$key}-{$i}\" class=\"{$class}\" name=\"tf_comment_meta[{$key}]\" value=\"{$i}\" required><label for=\"{$key}-{$i}\">{$i}</label>";
    }

    return $html;
}

/**
 * 2. Saving the user’s input
 *
 * @param int $comment_id
 * @param       $comment_approved
 * @param array $commentdata
 */
function tf_save_rating(int $comment_id, $comment_approved, array $commentdata)
{
    if ((isset($_POST[TF_COMMENT_META])) && ('' !== $_POST[TF_COMMENT_META])) {
        $tf_comment_meta = $_POST[TF_COMMENT_META];
        add_comment_meta($comment_id, TF_COMMENT_META, $tf_comment_meta);
        add_comment_meta($comment_id, TF_BASE_RATE, tfopt('r-base') ?? 5);
    }
}

add_action('comment_post', 'tf_save_rating', 10, 3);
/**
 * 3. Making the rating required (optional)
 *
 */
// Enable empty comment.
add_filter('allow_empty_comment', '__return_true');
// Validation for rating inputs
function tf_review_scripts()
{
    if (is_single() && comments_open()) { ?>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#commentform').validate({
                    ignore: [],
                    rules: {
                        'tf_comment_meta[]': {
                            required: true,
                        },
                        author: {
                            required: true,
                        },
                        email: {
                            required: true,
                        }
                    },
                    messages: {
                        'tf_comment_meta[]': "Please provide a ratings",
                    },
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        if (element.is(":radio")) {
                            error.appendTo(element.parents('.tf-single-rating'));
                        } else { // This is the default behavior
                            error.insertAfter(element);
                        }
                    }
                });
            });
        </script>
<?php
    }
}

add_action('wp_footer', 'tf_review_scripts');
// 4. Display the rating on a submitted comment. (If you need to display the rating)
/**
 * @param $comment
 * @param $args
 * @param $depth
 */
function tf_comment_callback($comment, $args, $depth)
{
    $tf_overall_rate = get_comment_meta($comment->comment_ID, TF_TOTAL_RATINGS, true);
    if ($tf_overall_rate == false) {
        $tf_comment_meta = get_comment_meta($comment->comment_ID, TF_COMMENT_META, true);
        $tf_overall_rate = tf_average_ratings(array_values($tf_comment_meta));
    }
    if ('div' === $args['style']) {
        $tag       = 'div';
        $add_below = 'comment';
    } else {
        $tag       = 'li';
        $add_below = 'div-comment';
    }
    $base_rate = get_comment_meta($comment->comment_ID, TF_BASE_RATE, true);
    ob_start();
    include TF_PATH . "templates/template-parts/review-callback.php";
    echo ob_get_clean();
}

/**
 * Calculate average ratings
 *
 * @param array $ratings collection of array
 *
 * @return string
 */
function tf_average_ratings(array $ratings = []): string
{
    if (!$ratings) {
        return 'N/A';
    }
    // No sub collection of ratings
    if (count($ratings) == count($ratings, COUNT_RECURSIVE)) {
        $average = array_sum($ratings) / count($ratings);
    } // Has sub collection of ratings
    else {
        $average = 0;
        foreach ($ratings as $rating) {
            $average += array_sum($rating) / count($rating);
        }
        $average = $average / count($ratings);
    }

    return sprintf('%.1f', $average);
}

/**
 * Calculate average ratings percent
 *
 * @param int $rating
 * @param int $total
 *
 * @return string
 */
function tf_average_rating_percent(int $rating = 0, int $total = 5): string
{
    $percent = ($rating * 100) / $total;

    return sprintf("%.2f", $percent);
}

/**
 * Calculate user's ratings per review
 *
 * @param       $comment
 * @param array $overall_rating
 */
function tf_calculate_user_ratings($comment, array &$overall_rating): void
{
    $tf_comment_meta = get_comment_meta($comment->comment_ID, TF_COMMENT_META, true);
    if ($tf_comment_meta) {
        foreach ($tf_comment_meta as $key => $ratings) {
            if (is_array($ratings)) {
                $overall_rating[$key][] = tf_average_ratings($ratings);
            } else {
                $overall_rating[$key][] = $ratings;
            }
        }
    }
}

/**
 * comment_reply_link_filter
 *
 * @param mixed $content
 *
 * @return string
 */
function tf_comment_reply_link_filter($content): string
{
    return '<div id="tourfic-rating" style="display: none">' . $content . '</div>';
}

add_filter('comment_link', 'tf_comment_reply_link_filter');
/**
 * Review Block
 */
function tf_item_review_block()
{
    $comments         = get_comments(['post_id' => get_the_ID(), 'status' => 'approve']);
    $tour_destination = $_GET['tour_destination'] ?? "";
    $destination      = $_GET['tour_destination'] ?? "";
    if ('tf_hotel' == get_post_type()) {
        $dest_slug_param = 'destination=' . $destination;
        $room            = $_GET['room'] ?? '';
        $infant          = '';
    } else if ('tf_tours' == get_post_type()) {
        $dest_slug_param = 'tour_destination' . $tour_destination;
        $infant          = $_GET['infant'] ?? "0";
        $room            = '';
    }
    $adults          = $_GET['adults'] ?? "1";
    $children        = $_GET['children'] ?? "0";
    $check_in_date   = $_GET['check-in-date'] ?? "";
    $check_out_date  = $_GET['check-out-date'] ?? "";
    $tf_overall_rate = tf_calculate_comments_rating($comments);
    $tf_extr_html    = '';
    ob_start();
    include TF_TEMPLATE_PART_PATH . 'single-review-block.php';

    return ob_get_clean();
}

/**
 * Calculate total ratings for a post
 *
 * @param array $comments All comments for current post
 *
 * @return array
 */
function tf_calculate_comments_rating(array $comments): array
{
    $tf_overall_rate = [];
    foreach ($comments as $comment) {
        tf_calculate_user_ratings($comment, $tf_overall_rate);
    }

    return $tf_overall_rate;
}

/**
 * Generate based on review text
 *
 * @param int $number comment number
 */
function tf_based_on_text(int $number): void
{
    $comments_title = apply_filters(
        'tf_comment_form_title',
        sprintf( // WPCS: XSS OK.
            /* translators: 1: number of comments */
            esc_html(_nx('Based on %1$s review', 'Based on %1$s reviews', $number, 'comments title', TFD)),
            number_format_i18n($number)
        )
    );
    echo esc_html($comments_title);
}

/**
 * Auto approve comment based on settings
 * 
 * @param int $comment_id
 */
function tf_auto_approve_comments(int $comment_id)
{
    $comment                     = [];
    $comment['comment_ID']       = $comment_id;
    $comment['comment_approved'] = intval(tfopt('r-auto-publish') ?? 0);
    wp_update_comment($comment);
}

add_action('wp_insert_comment', 'tf_auto_approve_comments');

/**
 * Remove comment id from url and restore query params
 *
 * @return string
 */
function tf_redirect_user_to_previous_url(): string
{
    return wp_get_referer();
}

add_filter('comment_post_redirect', 'tf_redirect_user_to_previous_url');


/**
 * Is current logged in user has any comments ??
 *
 * @return bool
 */
function tf_user_has_comments(): bool
{
    if (is_user_logged_in()) {
        global $wpdb, $current_user, $post;
        $userId = $current_user->ID;
        $count  = $wpdb->get_var('
             SELECT COUNT(comment_ID) 
             FROM ' . $wpdb->comments . ' 
             WHERE user_id = "' . $userId . '"' . ' and comment_post_ID = "' . $post->ID . '"');

        return boolval($count) ?? false;
    }

    return false;
}

/**
 * Format rating accordion to settings
 *
 * @param int $rating average rating from a review
 * @param int $base_rate comment's base rate
 *
 * @return string
 */
function tf_average_ratings_format(int $rating, int $base_rate = 5): string
{
    $settings_base = tfopt('r-base');

    if ($settings_base != $base_rate) {
        if ($settings_base > 5) {
            $rating = $rating * 2;
        } else {
            $rating = $rating / 2;
        }
    }
    return $rating . '/' . $settings_base;
}
