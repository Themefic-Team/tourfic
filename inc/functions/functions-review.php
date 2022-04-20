<?php
// don't load directly
defined('ABSPATH') || exit;

const TF_COMMENT_META  = 'tf_comment_meta';
const TF_TOTAL_RATINGS = 'tf_total_ratings';
const TF_BASE_RATE     = 'tf_base_rate';

/**
 * Remove Comment Meta Box from post edit screen
 */
function tf_remove_comment_meta_box() {
    remove_meta_box( 'commentsdiv', array('tf_hotel', 'tf_tours'), 'normal' );
}
add_action( 'admin_init', 'tf_remove_comment_meta_box' );

/**
 * Add script only for review
 */
function tf_review_script() {

    if (is_singular( array( 'tf_hotel', 'tf_tours' ) ) && comments_open()) {

        /**
         * jquery-validate
         * 
         * v1.19.3
         */
        wp_enqueue_script( 'jquery-validate', TF_ASSETS_URL . 'js/jquery.validate.min.js', array( 'jquery' ), '1.19.3', true );

        $data = '
        
            jQuery(document).ready(function($) {
                $("#commentform").validate({
                    ignore: [],
                    rules: {
                        "tf_comment_meta[]": {
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
                        "tf_comment_meta[]": "' .__("Please provide a ratings", "tourfic"). '",
                    },
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        if (element.is(":radio")) {
                            error.appendTo(element.parents(".tf-single-rating"));
                        } else { // This is the default behavior
                            error.insertAfter(element);
                        }
                    }
                });
            });
        
        ';

        wp_add_inline_script( 'jquery-validate', $data );

    }

}
add_action('wp_enqueue_scripts', 'tf_review_script', 99999);

/**
 * Review submit form
 * 
 * Popup
 */
if(!function_exists('tf_review_form')) {
    function tf_review_form() {

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
        
        
        //Declare Vars
        $comment_send      = __('Submit', 'tourfic');
        $comment_reply     = __('Write a Review', 'tourfic');
        $comment_reply_to  = __('Reply', 'tourfic');
        $comment_author    = __('Your Name', 'tourfic');
        $comment_email     = __('Email Address', 'tourfic');
        $comment_body      = __('Review Description', 'tourfic');
        $comment_cookies_1 = __(' By commenting you accept the', 'tourfic');
        $comment_cookies_2 = __(' Privacy Policy', 'tourfic');
        $comment_before    = __('', 'tourfic');
        $comment_cancel    = __('Cancel Reply', 'tourfic');
        $comment_meta      = tf_generate_review_meta_fields($fields);
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
}

/**
 * Generate review meta fields
 *
 * @param array $fields
 *
 * @return string
 */
if(!function_exists('tf_generate_review_meta_fields')) {
    function tf_generate_review_meta_fields(array $fields): string {

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
}

/**
 * Generate stars for meta fields
 *
 * @param string $key
 *
 * @return string
 */
if(!function_exists('tf_generate_stars')) {
    function tf_generate_stars(string $key): string {

        $limit = tfopt('r-base') ?? 5;
        $html  = '';
        foreach (array_reverse(range(1, $limit, 1)) as $i) {
            $html  .= "<input type=\"radio\" id=\"{$key}-{$i}\" name=\"tf_comment_meta[{$key}]\" value=\"{$i}\" required><label for=\"{$key}-{$i}\">{$i}</label>";
        }

        return $html;
    }
}

/**
 * 2. Saving the user’s input
 *
 * @param int $comment_id
 * @param       $comment_approved
 * @param array $commentdata
 */
if(!function_exists('tf_save_rating')) {
    function tf_save_rating(int $comment_id, $comment_approved, array $commentdata) {

        if ((isset($_POST[TF_COMMENT_META])) && ('' !== $_POST[TF_COMMENT_META])) {
            $tf_comment_meta = $_POST[TF_COMMENT_META];
            add_comment_meta($comment_id, TF_COMMENT_META, $tf_comment_meta);
            add_comment_meta($comment_id, TF_BASE_RATE, tfopt('r-base') ?? 5);
        }
    }
    add_action('comment_post', 'tf_save_rating', 10, 3);
}

/**
 * Enable empty comment.
 */
add_filter('allow_empty_comment', '__return_true');

/**
 * Calculate average ratings
 *
 * @param array $ratings collection of array
 *
 * @return float
 */
function tf_average_ratings(array $ratings = []) {
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
    $tf_base_rate = get_comment_meta($comment->comment_ID, TF_BASE_RATE, true);

    if ($tf_comment_meta) {
        foreach ($tf_comment_meta as $key => $ratings) {
            // calculate rate 
            $ratings = tf_average_rating_change_on_base($ratings, $tf_base_rate);
           
            if (is_array($ratings)) {
                $overall_rating[$key][] = tf_average_ratings($ratings);
            } else {
                $overall_rating[$key][] = $ratings;
            }

        }
    }
}

/**
 * Format rating accordion to settings
 * 
 */
function tf_average_rating_change_on_base( $rating,  $base_rate = 5)
{
    $settings_base = tfopt('r-base');

    if ($settings_base != $base_rate) {
        if ($settings_base > 5) {
            $rating = $rating * 2;
        } else {
            $rating = $rating / 2;
        }
    }
    return $rating;
}

/**
 * Format rating accordion to settings
 *
 * @param float $rating average rating from a review
 * @param int $base_rate comment's base rate
 *
 * @return string
 */
function tf_single_rating_change_on_base(float $rating, int $base_rate = 5): string {

    $settings_base = tfopt('r-base');

    if ($settings_base != $base_rate) {
        if ($settings_base > 5) {
            $rating = $rating * 2;
        } else {
            $rating = $rating / 2;
        }
    }

    $rating_star = ceil($rating/0.5)*0.5;

    $icons = '';
    if($rating_star > 1.5) {
        if(strpos($rating_star,".") !== false){
            foreach(range(0,abs($rating_star-1)) as $i) {
                $icons .= '<i class="fas fa-star"></i>';
            }
            $icons .= '<i class="fas fa-star-half-alt"></i>';
        }else{
            foreach(range(1,$rating_star) as $i) {
                $icons .= '<i class="fas fa-star"></i>';
            }
        }
    } else if($rating_star == 1.5){
        $icons .= '<i class="fas fa-star"></i>';
        $icons .= '<i class="fas fa-star-half-alt"></i>';
    } else if($rating_star == 1){
        $icons .= '<i class="fas fa-star"></i>';
    } else if($rating_star == 0.5){
        $icons .= '<i class="fas fa-star-half-alt"></i>';
    }
    
    return '<div>' .$icons . '</div>' . $rating;
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
 * Show rating on archive single item
 */
function tf_archive_single_rating() {

    $comments         = get_comments(['post_id' => get_the_ID(), 'status' => 'approve']);
    $tf_overall_rate = tf_calculate_comments_rating($comments);
    if($comments) {
        ob_start();
        ?>
        
        <div class="tf_item_review_block">
            <div class="reviewFloater reviewFloaterBadge__container">
                <div class="sr-review-score">
                    <div class="bui-review-score c-score bui-review-score--end">
                        <div class="bui-review-score__badge"> 
                            <?php _e( tf_average_ratings( array_values( $tf_overall_rate ?? [] ) ) ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        echo ob_get_clean();
    }
}

/**
 * Calculate total ratings for a post
 *
 * @param array $comments All comments for current post
 *
 * @return array
 */
function tf_calculate_comments_rating(array $comments): array {
    
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
            esc_html(_nx('%1$s review', '%1$s reviews', $number, 'comments title', 'tourfic')),
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
 * Pending moderation notice
 * 
 * @author fida
 * @return string
 */
function tf_pending_review_notice($post_id) {

    if (is_user_logged_in()) {

        global $wpdb, $current_user;
        $logged_in_id = $current_user->ID;

        $comments_query = new WP_Comment_Query( array( 'post_id' => $post_id, 'status' => 'hold', 'type' => 'comment', ) ); 
        $comments = $comments_query->comments;

        if($comments) {

            foreach($comments as $comment) {

                $comment_author_id = $comment->user_id;
    
                if($comment->comment_approved === '0' && $logged_in_id == $comment_author_id) {
                    return '<div class="tf-review-pending">' .__("Your review is awaiting moderation", "tourfic"). '</div>';
                }
            }
        }
    }
}