<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

const TF_COMMENT_META  = 'tf_comment_meta';
const TF_TOTAL_RATINGS = 'tf_total_ratings';
const TF_BASE_RATE     = 'tf_base_rate';

/**
 * Remove Comment Meta Box from post edit screen
 */
function tf_remove_comment_meta_box() {
	remove_meta_box( 'commentsdiv', array( 'tf_hotel', 'tf_tours' ), 'normal' );
}

add_action( 'admin_init', 'tf_remove_comment_meta_box' );

/**
 * Add script only for review
 */
function tf_review_script() {

	if ( is_singular( array( 'tf_hotel', 'tf_tours', 'tf_apartment' ) ) ) {

		/**
		 * jquery-validate
		 *
		 * v1.19.5
		 */
		wp_enqueue_script( 'jquery-validate', '//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js', array( 'jquery' ), TOURFIC, true );

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
                        "tf_comment_meta[]": "' . __( "Please provide a ratings", "tourfic" ) . '",
                    },
                    errorElement: "span",
                    errorPlacement: function(error, element) {
                        if (element.is(":radio")) {
                            error.appendTo(element.parents(".tf-form-single-rating"));
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

add_action( 'wp_enqueue_scripts', 'tf_review_script', 99999 );

/**
 * Review submit form
 *
 * Popup
 */
if ( ! function_exists( 'tf_review_form' ) ) {
	function tf_review_form() {

		tf_get_review_fields( $fields );

		//Declare Vars
		$comment_send      = __( 'Submit', 'tourfic' );
		$comment_reply     = __( 'Write a Review', 'tourfic' );
		$comment_reply_to  = __( 'Reply', 'tourfic' );
		$comment_author    = __( 'Your Name', 'tourfic' );
		$comment_email     = __( 'Email Address', 'tourfic' );
		$comment_body      = __( 'Review Description', 'tourfic' );
		$comment_cookies_1 = __( ' By commenting you accept the', 'tourfic' );
		$comment_cookies_2 = __( ' Privacy Policy', 'tourfic' );
		$comment_before    = __( '', 'tourfic' );
		$comment_cancel    = __( 'Cancel Reply', 'tourfic' );
		$comment_meta      = tf_generate_review_meta_fields( $fields );
		//Array
		$comments_args = [
			//Define Fields
			'fields'               => [
				'author'  => '<div class="tf-visitor-info"><div><input type="text" id="author" name="author" aria-required="true" placeholder="' . $comment_author . '"/></div>',
				'email'   => '<div><input type="email" id="email" name="email" placeholder="' . $comment_email . '"/></div></div>',
				'cookies' => '',
			],
			'class_container'      => 'tf-review-form-container',
			'class_form'           => 'tf-review-form',
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
			'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="tf_button btn-styled" value="%4$s" />',
			'submit_field'         => '<div class="tf-review-submit">%1$s %2$s</div>',
		];
		comment_form( $comments_args );
	}
}

/**
 * Always open comments for hotel & tour
 */
function tf_comments_open( $open, $post_id ) {

	$post = get_post( $post_id );

	if ( 'tf_hotel' == $post->post_type || 'tf_tours' == $post->post_type || 'tf_apartment' == $post->post_type ) {
		$open = true;
	}

	return $open;

}

add_filter( 'comments_open', 'tf_comments_open', 99, 2 );

/**
 * @param $fields
 */
function tf_get_review_fields( &$fields, $type = null ) {

	$type = $type === null ? get_post_type() : 'tf_hotel';
	/**
	 * Default fields until user save from option panel
	 */
	$default_hotels_field     = [
		array(
			'r-field-type' => __( 'Staff', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Facilities', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Cleanliness', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Comfort', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Value for money', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Location', 'tourfic' ),
		),
	];
	$default_apartments_field = [
		array(
			'r-field-type' => __( 'Staff', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Facilities', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Cleanliness', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Comfort', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Value for money', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Location', 'tourfic' ),
		),
	];
	$default_tours_field      = [
		array(
			'r-field-type' => __( 'Guide', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Transportation', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Value for money', 'tourfic' ),
		),
		array(
			'r-field-type' => __( 'Safety', 'tourfic' ),
		),
	];

	// If user does not have fields from settings, default fields will be loaded
	$tfopt_hotels     = ! empty( tf_data_types( tfopt( 'r-hotel' ) ) ) ? tf_data_types( tfopt( 'r-hotel' ) ) : $default_hotels_field;
	$tfopt_apartments = ! empty( tf_data_types( tfopt( 'r-apartment' ) ) ) ? tf_data_types( tfopt( 'r-apartment' ) ) : $default_apartments_field;
	$tfopt_tours      = ! empty( tf_data_types( tfopt( 'r-tour' ) ) ) ? tf_data_types( tfopt( 'r-tour' ) ) : $default_tours_field;

	$fields = 'tf_tours' === $type ? $tfopt_tours : ( 'tf_apartment' === $type ? $tfopt_apartments : $tfopt_hotels );
	if ( ! empty( $fields ) && gettype( $fields ) == "string" ) {
		$tf_hotel_fields_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $fields );
		$fields                = unserialize( $tf_hotel_fields_value );
	}
	if ( ! empty( $fields ) ) {
		$fields = array_map( function ( $i ) {
			return strtolower( $i['r-field-type'] );
		}, $fields );
	}
}

/**
 * Generate review meta fields
 *
 * @param array $fields
 *
 * @return string
 */
if ( ! function_exists( 'tf_generate_review_meta_fields' ) ) {
	function tf_generate_review_meta_fields( $fields ) {

		$limit = ! empty( tfopt( 'r-base' ) ) ? tfopt( 'r-base' ) : 5;

		$html = '<div class="tf-rating-wrapper">';
		foreach ( $fields as $field ) {
			if ( empty( $field ) ) {
				continue;
			}
			$html .= '<div class="tf-form-single-rating">';
			$html .= sprintf( '<label for="rating">%s</label>', $field );
			$html .= sprintf( '<div class="ratings-container star' . $limit . '">%s </div>', tf_generate_stars( $field ) );
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
if ( ! function_exists( 'tf_generate_stars' ) ) {
	function tf_generate_stars( $key ) {

		$limit = ! empty( tfopt( 'r-base' ) ) ? tfopt( 'r-base' ) : 5;
		$html  = '';
		foreach ( array_reverse( range( 1, $limit, 1 ) ) as $i ) {
			$html .= "<input type=\"radio\" id=\"{$key}-{$i}\" name=\"tf_comment_meta[{$key}]\" value=\"{$i}\" required><label for=\"{$key}-{$i}\">{$i}</label>";
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
if ( ! function_exists( 'tf_save_rating' ) ) {
	function tf_save_rating( $comment_id, $comment_approved, $commentdata ) {

		if ( ( isset( $_POST[ TF_COMMENT_META ] ) ) && ( '' !== $_POST[ TF_COMMENT_META ] ) ) {
			$tf_comment_meta = $_POST[ TF_COMMENT_META ];
			add_comment_meta( $comment_id, TF_COMMENT_META, $tf_comment_meta );
			add_comment_meta( $comment_id, TF_BASE_RATE, tfopt( 'r-base' ) ?? 5 );
		}
	}

	add_action( 'comment_post', 'tf_save_rating', 10, 3 );
}

/**
 * Enable empty comment.
 */
add_filter( 'allow_empty_comment', '__return_true' );

/**
 * Calculate average ratings
 *
 * @param array $ratings collection of array
 *
 * @return float
 */
function tf_average_ratings( $ratings = [] ) {

	if ( ! $ratings ) {
		return 0;
	}

	// No sub collection of ratings
	if ( count( $ratings ) == count( $ratings, COUNT_RECURSIVE ) ) {
		$average = array_sum( $ratings ) / count( $ratings );
	} else {
		$average = 0;
		foreach ( $ratings as $rating ) {
			$average += array_sum( $rating ) / count( $rating );
		}
		$average = $average / count( $ratings );
	}

	return sprintf( '%.1f', $average );
}

/**
 * Calculate average ratings percent
 *
 * @param int $rating
 * @param int $total
 *
 * @return string
 */
function tf_average_rating_percent( $rating = 0, $total = 5 ) {
	if ( empty( $total ) ) {
		$total = 5;
	}
	$percent = ( $rating * 100 ) / $total;

	return sprintf( "%.2f", $percent );
}

/**
 * Calculate user's ratings per review
 *
 * @param       $comment
 * @param array $overall_rating
 */
function tf_calculate_user_ratings( $comment, &$overall_rating, &$total_rate ) {
	if ( ! is_array( $total_rate ) ) {
		$total_rate = array();
	}
	$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
	$tf_base_rate    = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );

	if ( $tf_comment_meta ) {
		$total_rate[] = tf_average_rating_change_on_base( tf_average_ratings( $tf_comment_meta ), $tf_base_rate );

		foreach ( $tf_comment_meta as $key => $ratings ) {
			// calculate rate
			$ratings = tf_average_rating_change_on_base( $ratings, $tf_base_rate );

			if ( is_array( $ratings ) ) {
				$overall_rating[ $key ][] = tf_average_ratings( $ratings );
			} else {
				$overall_rating[ $key ][] = $ratings;
			}

		}
	}
}

/**
 * Format rating accordion to settings
 *
 */
function tf_average_rating_change_on_base( $rating, $base_rate = 5 ) {

	$settings_base = ! empty ( tfopt( 'r-base' ) ) ? tfopt( 'r-base' ) : 5;
	$base_rate     = ! empty ( $base_rate ) ? $base_rate : 5;

	if ( $settings_base != $base_rate ) {
		if ( $settings_base > 5 ) {
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
function tf_single_rating_change_on_base( $rating, $base_rate = 5 ) {


	if ( $rating == 0 ) {
		return '';
	}

	$settings_base = ! empty ( tfopt( 'r-base' ) ) ? tfopt( 'r-base' ) : 5;
	$base_rate     = ! empty ( $base_rate ) ? $base_rate : 5;

	if ( $settings_base != $base_rate ) {
		if ( $settings_base > 5 ) {
			$rating = $rating * 2;
		} else {
			$rating = $rating / 2;
		}
	}

	$rating_star = ceil( $rating / 0.5 ) * 0.5;

	$icons = '';
	if ( $rating_star > 1.5 ) {
		if ( strpos( $rating_star, "." ) !== false ) {
			foreach ( range( 0, abs( $rating_star - 1 ) ) as $i ) {
				$icons .= '<i class="fas fa-star"></i>';
			}
			$icons .= '<i class="fas fa-star-half-alt"></i>';
		} else {
			foreach ( range( 1, $rating_star ) as $i ) {
				$icons .= '<i class="fas fa-star"></i>';
			}
		}
	} else if ( $rating_star == 1.5 ) {
		$icons .= '<i class="fas fa-star"></i>';
		$icons .= '<i class="fas fa-star-half-alt"></i>';
	} else if ( $rating_star == 1 ) {
		$icons .= '<i class="fas fa-star"></i>';
	} else if ( $rating_star == 0.5 ) {
		$icons .= '<i class="fas fa-star-half-alt"></i>';
	}

	return '<div>' . $icons . '</div>' . $rating;
}

/**
 * comment_reply_link_filter
 *
 * @param mixed $content
 *
 * @return string
 */
function tf_comment_reply_link_filter( $content ) {
	return '<div id="tourfic-rating" style="display: none">' . $content . '</div>';
}

add_filter( 'comment_link', 'tf_comment_reply_link_filter' );
/**
 * Show rating on archive single item
 */
function tf_archive_single_rating() {

	$comments        = get_comments( [ 'post_id' => get_the_ID(), 'status' => 'approve' ] );
	$tf_current_post = get_post_type();
	$tf_overall_rate = [];
	tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rate );
	if ( $comments ) {
		ob_start();
		?>
		<?php
		$tf_tour_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['tour-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['tour-archive'] : 'design-1';
		$tf_hotel_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';
		
		if( ( "tf_tours"==$tf_current_post && $tf_tour_arc_selected_template=="design-1" ) || ( "tf_hotel"==$tf_current_post && $tf_hotel_arc_selected_template=="design-1" ) ){
		?>
			<div class="tf-reviews tf-flex tf-mt-16 tf-flex-gap-12">
				<div class="tf-review-items">
				<?php
				$settings_base = ! empty ( tfopt( 'r-base' ) ) ? tfopt( 'r-base' ) : 5;
				$base_rate     = 5;
				$rating = tf_average_ratings( array_values( $tf_overall_rate ?? [] ) );
				if ( $settings_base != $base_rate ) {
					if ( $settings_base > 5 ) {
						$rating = $rating * 2;
					} else {
						$rating = $rating / 2;
					}
				}
				if ( $settings_base > 5 ) {
					$rating_star = $rating / 2;
				}else{
					$rating_star = ceil( $rating / 0.5 ) * 0.5;
				}
				$icons = '';
				if ( $rating_star > 1.5 ) {
					if ( strpos( $rating_star, "." ) !== false ) {
						foreach ( range( 0, abs( $rating_star - 1 ) ) as $i ) {
							$icons .= '<i class="fa-solid fa-star"></i>';
						}
						$icons .= '<i class="fas fa-star-half-alt"></i>';
					} else {
						foreach ( range( 1, $rating_star ) as $i ) {
							$icons .= '<i class="fa-solid fa-star"></i>';
						}
					}
				} else if ( $rating_star == 1.5 ) {
					$icons .= '<i class="fa-solid fa-star"></i>';
					$icons .= '<i class="fas fa-star-half-alt"></i>';
				} else if ( $rating_star == 1 ) {
					$icons .= '<i class="fa-solid fa-star"></i>';
				} else if ( $rating_star == 0.5 ) {
					$icons .= '<i class="fas fa-star-half-alt"></i>';
				}
				echo $icons;
				?>
				</div>
				<div class="tf-avarage-review">
				<?php _e( tf_average_ratings( array_values( $tf_overall_rate ?? [] ) ) ); ?>
				 (<?php tf_based_on_text( count( $comments ) ); ?>)
				</div>
			</div>
		<?php } elseif( ( "tf_tours"==$tf_current_post && $tf_tour_arc_selected_template=="design-2" ) || ( "tf_hotel"==$tf_current_post && $tf_hotel_arc_selected_template=="design-2" ) ){ ?>
			<span class="tf-available-rating-number">
				<?php _e( tf_average_ratings( array_values( $tf_overall_rate ?? [] ) ) ); ?>
			</span>
		<?php }else{ ?>
			<div class="tf-archive-rating-wrapper">
				<div class="tf-archive-rating">
					<span>
						<?php _e( tf_average_ratings( array_values( $tf_overall_rate ?? [] ) ) ); ?>
					</span>
				</div>
				<h6><?php tf_based_on_text( count( $comments ) ); ?></h6>
			</div>
		<?php
		}
		echo ob_get_clean();
	}else{
		
		$tf_tour_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['tour-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['tour-archive'] : 'design-1';
		$tf_hotel_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] : 'design-1';
		
		if( ( "tf_tours"==$tf_current_post && $tf_tour_arc_selected_template=="design-1" ) || ( "tf_hotel"==$tf_current_post && $tf_hotel_arc_selected_template=="design-1" ) ){
		?>
		<div class="tf-reviews tf-flex tf-mt-16 tf-flex-gap-12">
			<div class="tf-review-items">
				<i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i>
			</div>
			<div class="tf-avarage-review">
				<?php _e(" (No Review)", "tourfic"); ?>
			</div>
		</div>
		<?php
		} elseif( ( "tf_tours"==$tf_current_post && $tf_tour_arc_selected_template=="design-2" ) || ( "tf_hotel"==$tf_current_post && $tf_hotel_arc_selected_template=="design-2" ) ){ ?>
			<span class="tf-available-rating-number">
				<?php _e('0.0','tourfic'); ?>
			</span>
		<?php }
	}
}

/**
 * Calculate total ratings for a post
 *
 * @param array $comments All comments for current post
 *
 * @return array
 */
function tf_calculate_comments_rating( $comments, &$tf_overall_rate, &$total_rating ) {

	$tf_overall_rate = [];
	foreach ( $comments as $comment ) {
		tf_calculate_user_ratings( $comment, $tf_overall_rate, $total_rating );

	}
	$total_rating = tf_average_ratings( $total_rating );

}

/**
 * Total Average Rating
 */
function tf_total_avg_rating( $comments ) {

	foreach ( $comments as $comment ) {
		$tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
		$tf_base_rate    = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );

		if ( $tf_comment_meta ) {
			$total_rate[] = tf_average_rating_change_on_base( tf_average_ratings( $tf_comment_meta ), $tf_base_rate );
		}
	}

	return tf_average_ratings( $total_rate );

}

/**
 * Generate based on review text
 *
 * @param int $number comment number
 */
function tf_based_on_text( $number ) {
	$comments_title = apply_filters(
		'tf_comment_form_title',
		sprintf( // WPCS: XSS OK.
		/* translators: 1: number of comments */
			esc_html( _nx( '%1$s review', '%1$s reviews', $number, 'comments title', 'tourfic' ) ),
			number_format_i18n( $number )
		)
	);
	echo esc_html( $comments_title );
}

/**
 * Auto approve comment based on settings
 *
 * @param int $comment_id
 */
function tf_auto_approve_comments( $comment_id ) {
	$comment                     = [];
	$comment['comment_ID']       = $comment_id;
	$comment['comment_approved'] = intval( tfopt( 'r-auto-publish' ) ?? 0 );
	wp_update_comment( $comment );
}

add_action( 'wp_insert_comment', 'tf_auto_approve_comments' );

/**
 * Remove comment id from url and restore query params
 *
 * @return string
 */
function tf_redirect_user_to_previous_url() {
	return wp_get_referer();
}

add_filter( 'comment_post_redirect', 'tf_redirect_user_to_previous_url' );


/**
 * Is current logged in user has any comments ??
 *
 * @return bool
 */
function tf_user_has_comments() {
	if ( is_user_logged_in() ) {
		global $wpdb, $current_user, $post;
		$userId = $current_user->ID;
		$count  = $wpdb->get_var( '
             SELECT COUNT(comment_ID) 
             FROM ' . $wpdb->comments . ' 
             WHERE user_id = "' . $userId . '"' . ' and comment_post_ID = "' . $post->ID . '"' );

		return boolval( $count ) ?? false;
	}

	return false;
}

/**
 * Pending moderation notice
 *
 * @return string
 * @author fida
 */
function tf_pending_review_notice( $post_id ) {

	if ( is_user_logged_in() ) {

		global $wpdb, $current_user;
		$logged_in_id = $current_user->ID;

		$comments_query = new WP_Comment_Query( array( 'post_id' => $post_id, 'status' => 'hold', 'type' => 'comment', ) );
		$comments       = $comments_query->comments;

		if ( $comments ) {

			foreach ( $comments as $comment ) {

				$comment_author_id = $comment->user_id;

				if ( $comment->comment_approved === '0' && $logged_in_id == $comment_author_id ) {
					return '<div class="tf-review-pending">' . __( "Your review is waiting for approval", "tourfic" ) . '</div>';
				}
			}
		}
	} else {

		$comments_query = new WP_Comment_Query( array( 'post_id' => $post_id, 'status' => 'hold', 'type' => 'comment', ) );
		$comments       = $comments_query->comments;

		if ( $comments ) {
			foreach ( $comments as $comment ) {
				$cookie_name = 'tf_review_' . $comment->comment_ID;
				if ( $comment->comment_approved === '0' && isset( $_COOKIE[ $cookie_name ] ) ) {
					return '<div class="tf-review-pending">' . __( "Your review is waiting for approval", "tourfic" ) . '</div>';
				} else {
					return '';
				}
			}
		}

	}
}

/**
 * Delete old review fields button
 */
function tf_delete_old_review_fields_button() {
	echo '
    <div class="csf-title">
        <h4>' . __( "Delete Old Review Fields", "tourfic" ) . '</h4>
        <div class="csf-subtitle-text">' . __( "Delete review fields that don't match with the present fields.<br><b style='color: red;'>Be aware! You will lose your old data!</b>", "tourfic" ) . '</div>
    </div>
    <div class="csf-fieldset">
        <button type="button" data-delete-all="no" class="button button-large csf-warning-primary tf-del-old-review-fields tf-order-remove">' . __( "Delete Fields", "tourfic" ) . '</button>
    </div>
    <div class="clear"></div>
    ';
}

/**
 * Delete old complete review button
 */
function tf_delete_old_complete_review_button() {

	echo '
    <div class="csf-title">
        <h4>' . __( "Delete Old Reviews", "tourfic" ) . '</h4>
        <div class="csf-subtitle-text">' . __( "Delete reviews that don't have any review (rating) fields.<br><b style='color: red;'>Be aware! You will lose your old data!</b>", "tourfic" ) . '</div>
    </div>
    <div class="csf-fieldset">
        <button type="button" data-delete-all="yes" class="button button-large csf-warning-primary tf-del-old-review-fields tf-order-remove">' . __( "Delete Reviews", "tourfic" ) . '</button>
    </div>
    <div class="clear"></div>
    ';
}


/**
 * Ajax delete old review fields
 */
add_action( 'wp_ajax_tf_delete_old_review_fields', 'tf_delete_old_review_fields' );
function tf_delete_old_review_fields() {

	global $wpdb;

	$comments = get_comments();

	foreach ( $comments as $comment ) {

		$review    = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
		$post_type = get_post_type( $comment->comment_post_ID );
		tf_get_review_fields( $fields, $post_type );
		if ( ! empty( $review ) ) {

			$counter = 0;

			foreach ( $review as $key => $r ) {
				if ( ! in_array( $key, $fields ) ) {
					unset( $review[ $key ] );
				} else {
					$counter ++;
				}
			}

			update_comment_meta( $comment->comment_ID, TF_COMMENT_META, $review );
			$review = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );

			if ( count( $review ) == 0 && $_POST['deleteAll'] == 'yes' ) {
				wp_delete_comment( $comment, true );
			}

		} else {

			if ( $_POST['deleteAll'] == 'yes' ) {
				wp_delete_comment( $comment, true );
			}

		}
	}

	wp_send_json_success( "Old review fields deleted." );
}

/**
 * Add cookie for nonlogged in users
 */
add_action( 'set_comment_cookies', function ( $comment, $user ) {
	if ( ! is_user_logged_in() ) {
		$cookie_name  = 'tf_review_' . $comment->comment_ID;
		$cookie_value = '1';
		setcookie( $cookie_name, $cookie_value, time() + 100, "/" );
	}
}, 10, 2 );