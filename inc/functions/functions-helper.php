<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Search form action url
 */
function tf_booking_search_action(){

    // get data from global settings else default
	$search_result_action = !empty(tfopt('search-result-page')) ? get_permalink(tfopt('search-result-page')) : home_url('/search-result/');
    // can be override by filter
	return apply_filters( 'tf_booking_search_action', $search_result_action );

}

/**
 * Review Block
 */
function tourfic_item_review_block() {

    $comments = get_comments( array( 'post_id' => get_the_ID() ) );
	$tour_destination = isset($_GET['tour_destination']) ? $_GET['tour_destination'] : "";
	$destination = isset($_GET['tour_destination']) ? $_GET['tour_destination'] : "";
	if('tourfic' == get_post_type()){
		$dest_slug_param = 'destination=' . $destination;
		$room = isset( $_GET['room'] ) ? $_GET['room'] : '';
		$infant = '';
	}else if('tf_tours' == get_post_type()){
		$dest_slug_param = 'tour_destination' . $tour_destination;
		$infant = isset($_GET['infant']) ? $_GET['infant'] : "0";
		$room = '';

	};
	$adults = isset($_GET['adults']) ? $_GET['adults'] : "1";
	$children = isset($_GET['children']) ? $_GET['children'] : "0";
	$check_in_date = isset($_GET['check-in-date']) ? $_GET['check-in-date'] : "";
	$check_out_date = isset($_GET['check-out-date']) ? $_GET['check-out-date'] : "";
    $tf_overall_rate = array();
    $tf_overall_rate['review'] = null;

    $tf_extr_html = '';

    foreach ( $comments as $comment ) {

        $tf_comment_meta = get_comment_meta( $comment->comment_ID, 'tf_comment_meta', true );

        if ( $tf_comment_meta ) {
            foreach ( $tf_comment_meta as $key => $value ) {
                $tf_overall_rate[$key][] = $value ? $value : "5";
            }
        } else {
            $tf_overall_rate['review'][] = "5";
            $tf_overall_rate['sleep'][] = "5";
            $tf_overall_rate['location'][] = "5";
            $tf_overall_rate['services'][] = "5";
            $tf_overall_rate['cleanliness'][] = "5";
            $tf_overall_rate['rooms'][] = "5";
        }
    }

    ?>
	<div class="tf_item_review_block">
		<div class="reviewFloater reviewFloaterBadge__container">
		    <div class="sr-review-score">
		        <a class="sr-review-score__link" href="<?php echo get_the_permalink() . '?'. $dest_slug_param . '&adults=' . $adults . '&children=' . $children . '&room=' . $room . '&check-in-date=' . $check_in_date . '&check-out-date=' . $check_out_date; ?>" target="_blank">
		            <div class="bui-review-score c-score bui-review-score--end">
		                <div class="bui-review-score__badge"> <?php _e( tourfic_avg_ratings( $tf_overall_rate['review'] ) );?> </div>
		                <div class="bui-review-score__content">
		                    <div class="bui-review-score__title"> <?php esc_html_e( 'Customer Rating', 'tourfic' );?> </div>
		                    <div class="bui-review-score__text">
							<?php
$comments_title = apply_filters(
        'tf_comment_form_title',
        sprintf(  // WPCS: XSS OK.
            /* translators: 1: number of comments */
            esc_html( _nx( 'Based on %1$s review', 'Based on %1$s reviews', get_comments_number(), 'comments title', 'tourfic' ) ),
            number_format_i18n( get_comments_number() )
        ) );

    echo esc_html( $comments_title );
    ?>
		                    </div>
		                </div>
		            </div>
		        </a>
		    </div>
		</div>
	</div>
	<?php
}

?>