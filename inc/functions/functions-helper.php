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

// Protected Pass
function tourfic_proctected_product_pass(){
	return "111111";
}

/**
 * Review form
 */
function tourfic_get_review_form( ){

	//tours and hotel comment conditional markup
	if('tf_tours' === get_post_type()){
		$div_start = "<div class='comment_form_fields'>";
		$div_end   = "</div>";
	}else{
		$div_start = '';
		$div_end   = '';
	};
	
	//Declare Vars
	$comment_send = __( 'Submit', 'tourfic' );
	$comment_reply = __( 'Write a Review', 'tourfic' );
	$comment_reply_to = __( 'Reply', 'tourfic' );

	$comment_author = 'Name';
	$comment_email = 'E-Mail';
	$comment_body = 'Comment';
	$comment_url = 'Website';
	$comment_cookies_1 = ' By commenting you accept the';
	$comment_cookies_2 = ' Privacy Policy';

	$comment_before = 'Registration isn\'t required.';

	$comment_cancel = 'Cancel Reply';

	$comment_meta = '<div class="tf_comment-metas">';

	$comment_meta .= '<div class="comment-meta">
		<label>Ratings</label>
		<select name="tf_comment_meta[review]">
			<option value="5">&#9733; &#9733; &#9733; &#9733; &#9733;</option>
			<option value="4">&#9733; &#9733; &#9733; &#9733;</option>
			<option value="3">&#9733; &#9733; &#9733;</option>
			<option value="2">&#9733; &#9733;</option>
			<option value="1">&#9733;</option>
		</select>
	</div>';

	$comment_meta .= '<div class="comment-meta">
		<label>Sleep</label>
		<select name="tf_comment_meta[sleep]">
			<option value="5">&#9733; &#9733; &#9733; &#9733; &#9733;</option>
			<option value="4">&#9733; &#9733; &#9733; &#9733;</option>
			<option value="3">&#9733; &#9733; &#9733;</option>
			<option value="2">&#9733; &#9733;</option>
			<option value="1">&#9733;</option>
		</select>
	</div>';

	$comment_meta .= '<div class="comment-meta">
		<label>Location</label>
		<select name="tf_comment_meta[location]">
			<option value="5">&#9733; &#9733; &#9733; &#9733; &#9733;</option>
			<option value="4">&#9733; &#9733; &#9733; &#9733;</option>
			<option value="3">&#9733; &#9733; &#9733;</option>
			<option value="2">&#9733; &#9733;</option>
			<option value="1">&#9733;</option>
		</select>
	</div>';

	$comment_meta .= '<div class="comment-meta">
		<label>Services</label>
		<select name="tf_comment_meta[services]">
			<option value="5">&#9733; &#9733; &#9733; &#9733; &#9733;</option>
			<option value="4">&#9733; &#9733; &#9733; &#9733;</option>
			<option value="3">&#9733; &#9733; &#9733;</option>
			<option value="2">&#9733; &#9733;</option>
			<option value="1">&#9733;</option>
		</select>
	</div>';

	$comment_meta .= '<div class="comment-meta">
		<label>Cleanliness</label>
		<select name="tf_comment_meta[cleanliness]">
			<option value="5">&#9733; &#9733; &#9733; &#9733; &#9733;</option>
			<option value="4">&#9733; &#9733; &#9733; &#9733;</option>
			<option value="3">&#9733; &#9733; &#9733;</option>
			<option value="2">&#9733; &#9733;</option>
			<option value="1">&#9733;</option>
		</select>
	</div>';

	$comment_meta .= '<div class="comment-meta">
		<label>Room(s)</label>
		<select name="tf_comment_meta[rooms]">
			<option value="5">&#9733; &#9733; &#9733; &#9733; &#9733;</option>
			<option value="4">&#9733; &#9733; &#9733; &#9733;</option>
			<option value="3">&#9733; &#9733; &#9733;</option>
			<option value="2">&#9733; &#9733;</option>
			<option value="1">&#9733;</option>
		</select>
	</div>';

	$comment_meta .= '</div>';

	//Array
	$comments_args = array(
	    //Define Fields
	    'fields' => array(
	        //Author field
	        'author' => '<div class="author-email"><p class="comment-form-author"><input type="text" id="author" name="author" aria-required="true" placeholder="' . $comment_author .'"></input></p>',
	        //Email Field
	        'email' => '<p class="comment-form-email"><input type="email" id="email" name="email" placeholder="' . $comment_email .'"></input></p></div>',
	        //URL Field
	        //'url' => '<p class="comment-form-url"><input type="text" id="url" name="url" placeholder="' . $comment_url .'"></input></p>',
	        //Cookies
	        'cookies' => '<input type="checkbox" required>' . $comment_cookies_1 . '<a href="' . get_privacy_policy_url() . '">' . $comment_cookies_2 . '</a>' . $div_end,
	    ),
	    // Change the title of send button
	    'label_submit' => $comment_send,
	    // Change the title of the reply section
	    'title_reply' => $comment_reply,
	    // Change the title of the reply section
	    'title_reply_to' => $comment_reply_to,
	    // Reply html start
	    'title_reply_before' => '<div id="reply-title" class="comment-reply-title">',
	    // Reply html end
	    'title_reply_after' => '<span class="faq-indicator"> <i class="fa fa-angle-up" aria-hidden="true"></i> <i class="fa fa-angle-down" aria-hidden="true"></i> </span></div>',
	    //Cancel Reply Text
	    'cancel_reply_link' => $comment_cancel,
	    // Redefine your own textarea (the comment body).
	    'comment_field' => $comment_meta . $div_start .'<p class="comment-form-comment"><textarea id="comment" name="comment" aria-required="true" placeholder="' . $comment_body .'"></textarea></p>',
	    //Message Before Comment
	    'comment_notes_before' => $comment_before,
	    // Remove "Text or HTML to be displayed after the set of comment fields".
	    'comment_notes_after' => '',
	    //Submit Button ID
	    'id_submit' => 'comment-submit',
	    //Submit Button html
	    'submit_button' => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
	);

	comment_form( $comments_args );

}

/**
 * Save Comment Meta
 */
function tourfic_save_comment_meta_data( $comment_id ) {
	$meta_values = $_POST['tf_comment_meta'];
    update_comment_meta( $comment_id, 'tf_comment_meta',  $meta_values );
}
add_action( 'comment_post', 'tourfic_save_comment_meta_data' );

/**
 * Generate Star
 */
function tourfic_star_generate( $count ){
	$stars = '';
	// Fill Star
	for ( $i=0; $i < $count; $i++) {
		$stars .= '&#9733;';
	}
	// Outline Star
	for ( $j=$i; $j < 5; $j++) {
		$stars .= '&#9734;';
	}
	return $stars;
}

/**
 * Show Comment meta
 */
add_filter( 'get_comment_author_link', 'tourfic_attach_city_to_author' );
function tourfic_attach_city_to_author( $author ) {

    $tf_comment_meta = get_comment_meta( get_comment_ID(), 'tf_comment_meta', true );

    ob_start(); ?>

    <?php if( $tf_comment_meta ) : ?>
    	<div class="tf_comment-metas">
    	<?php foreach ( $tf_comment_meta as $key => $value ) : ?>
			<div class="comment-meta">
				<label class="tf_comment_meta-key"><?php _e( $key ); ?></label>
				<div class="tf_comment_meta-ratings"><?php _e( tourfic_star_generate($value) ); ?></div>
			</div>
    	<?php endforeach; ?>
    	</div>
    <?php endif; ?>
    <?php
    $output = ob_get_clean();

    if ( $tf_comment_meta )
        $author .= $output;
    return $author;
}

/**
 * Review Block
 */
function tourfic_item_review_block() {

    $comments = get_comments( array( 'post_id' => get_the_ID() ) );
	$tour_destination = isset($_GET['tour_destination']) ? $_GET['tour_destination'] : "";
	$destination = isset($_GET['tour_destination']) ? $_GET['tour_destination'] : "";
        if ('tf_hotel' == get_post_type()) {
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

// Notice wrapper
function tourfic_notice_wrapper(){
	?>
	<div class="tf_container">
		<div class="tf_notice_wrapper"></div>
	</div>
	<?php
}
add_action( 'tf_before_container', 'tourfic_notice_wrapper', 10 );

/**
 * Function: tf_term_count
 * 
 * @return number of available terms
 */
if( !function_exists('tf_term_count') ){
    function tf_term_count( $filter, $destination, $default_count ){
        
        if( $destination == '' ){
            return $default_count;
        }
        
        $term_count = array();
        
        $args = array(
            'post_type' => 'tf_hotel',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'hotel_location',
                    'field'    => 'slug',
                    'terms'    => $destination
                )
            )
        );
        
        $loop = new WP_Query( $args );
                
        if ( $loop->have_posts() ) :
            while ( $loop->have_posts() ) : $loop->the_post(); 
                
            if( has_term( $filter, 'tf_filters', get_the_ID() ) == true ) {
                $term_count[] = 'true';
            }
        
            endwhile; 
        endif;
        
        return count($term_count);
        
        wp_reset_postdata();
    }
}

// Set search reult page
function tourfic_booking_set_search_result( $url ){	
	$search_result_page = tfopt( 'search-result-page' );

	if ( isset( $search_result_page ) ){
		$url = get_permalink( $search_result_page );
	}
	
	return $url;

	

}
add_filter( 'tf_booking_search_action', 'tourfic_booking_set_search_result' );

// Tours price with html format
function tf_tours_price_html() {
	$meta = get_post_meta( get_the_ID(),'tf_tours_option',true );
	$pricing_rule = $meta['pricing'] ? $meta['pricing'] : null;
	$tour_type = $meta['type'] ? $meta['type'] : null;
	if( $pricing_rule == 'group'){
		$price = $meta['group_price'] ? $meta['group_price'] : 0;
	}else{
		$price = $meta['adult_price'] ? $meta['adult_price'] : 0;
	}
	$discount_type = $meta['discount_type'] ? $meta['discount_type'] : null;
	$discounted_price = $meta['discount_price'] ? $meta['discount_price'] : NULL;
	if( $discount_type == 'percent' ){
		$sale_price = number_format( $price - (( $price / 100 ) * $discounted_price) ,1 ); 
	}elseif( $discount_type == 'fixed'){
		$sale_price = number_format( ( $price - $discounted_price ),1 );
	}else if( $discount_type == 'none' ){
		$sale_price = number_format( $price, 1 );
	}
	if ( !$price ) {
		echo  "<span class='tf-price'></span>";
	}
	ob_start();
	?>
	<?php if (  $sale_price < $price && $discounted_price > 0 && $discount_type != 'none' ) { ?>
		<span class="tf-price"><del><?php echo wc_price( $price ); ?></del></span>
		<span class="tf-price"><?php echo wc_price( $sale_price ); ?></span>
	<?php } else { ?>
		<span class="tf-price"><?php echo wc_price( $price ); ?></span>
	<?php } ?>

	<?php
	return ob_get_clean();
}

// price with html format
function tourfic_price_html( $price = null, $sale_price = null ) {
	if ( !$price ) {
		return;
	}
	ob_start();
	?>
	<?php if ( $sale_price > 0 ) { ?>
		<span class="tf-price"><del><?php echo wc_price( $price ); ?></del></span>
		<span class="tf-price"><?php echo wc_price( $sale_price ); ?></span>
	<?php } else { ?>
		<span class="tf-price"><?php echo wc_price( $price ); ?></span>
	<?php } ?>

	<div class="price-per-night"><?php esc_html_e( 'Price per night', 'tourfic' ); ?></div>

	<?php
	return ob_get_clean();
}



/**
 * Full Width Container Start
 */
function tourfic_fullwidth_container_start( $fullwidth ){

    if ( $fullwidth == "true" ) : ?>
        <!-- Start Fullwidth Wrap -->
        <div class="tf_tf_booking-widget-wrap" data-fullwidth="true">
        <div class="tf_custom-container">
        <div class="tf_custom-inner">
    <?php endif;
}

/**
 * Full Width Container Start
 */
function tourfic_fullwidth_container_end( $fullwidth ){

    if ( $fullwidth == "true" ) : ?>
        </div></div></div>
        <!-- Close Fullwidth Wrap -->
    <?php endif;
}

/**
 * Get AVG
 */
function tourfic_avg_ratings( $a = array() ){
	if ( !$a ) {
		return 'N/A';
	}

	$a = array_filter($a);
	$average = array_sum($a)/count($a);
	return sprintf("%.1f", $average);
}

/**
 * Get Percent
 */
function tourfic_avg_rating_percent( $val = 0, $total = 5 ){

	$percent = ($val*100)/$total;
	return sprintf("%.2f", $percent);
}

// Ask Question
function tourfic_ask_question(){
	?>
	<div id="tf-ask-question" style="display: none;">
		<div class="tf-aq-overlay"></div>
		<div class="tf-aq-outer">
			<span class="close-aq">&times;</span>
			<div class="tf-aq-inner">   
				<h4><?php esc_html_e( 'Submit your question', 'tourfic' ); ?></h4>
				<form id="ask-question" action="" method="post">
					<div class="tf-aq-field">
						<input type="text" name="your-name" placeholder="<?php esc_attr_e( 'Your Name', 'tourfic' ); ?>" required />
					</div>
					<div class="tf-aq-field">
						<input type="email" name="your-email" placeholder="<?php esc_attr_e( 'Your email', 'tourfic' ); ?>" required />
					</div>
					<div class="tf-aq-field">
						<textarea placeholder="<?php esc_attr_e( 'Your Question', 'tourfic' ); ?>" name="your-question" required></textarea>
					</div>
					<div class="tf-aq-field">
						<button type="reset" class="screen-reader-text"><?php esc_html_e( 'Reset', 'tourfic' ); ?></button>
						<button type="submit" form="ask-question" class="button tf_button"><?php esc_html_e( 'Submit', 'tourfic' ); ?></button>
						<input type="hidden" name="post_id" value="<?php esc_attr_e( get_the_ID() ); ?>">
						<?php wp_nonce_field( 'ask_question_nonce' ); ?>
						<div class="response"></div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'tourfic_ask_question' );

// Ask question ajax
function tourfic_ask_question_ajax(){
	$response = array();

	if ( !check_ajax_referer( 'ask_question_nonce' ) ){
		$response['status'] = 'error';
		$response['msg'] = __('Security error! Reload the page and try again.', 'tourfic');
		echo json_encode( $response );
		wp_die();
	}

	$name = isset( $_POST['your-name'] ) ? sanitize_text_field( $_POST['your-name'] ) : null;
	$email = isset( $_POST['your-email'] ) ? sanitize_email( $_POST['your-email'] ) : null;
	$question = isset( $_POST['your-question'] ) ? sanitize_text_field( $_POST['your-question'] ) : null;

	$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : null;
	$post_title = get_the_title( $post_id );

	$author_id = get_post_field('post_author', $post_id);

	$send_email_to = get_post_meta( $post_id, 'send_email_to', true ) ? get_post_meta( $post_id, 'send_email_to', true ) : null;

	$email_replace = array(
	    "{{admin_email}}" => sanitize_email( get_option( 'admin_email' ) ),
	    "{{author_email}}" => sanitize_email( get_the_author_meta( 'user_email' , $author_id ) ),
	);

    $send_email_to = strtr($send_email_to, $email_replace);


	$subject     = sprintf( esc_html__( 'Someone asked question on: %s', 'tourfic' ), $post_title );
	$message     = "{$question}";
	$headers[]   = 'Reply-To: '.$name.' <'.$email.'>';
	$attachments = array();


	if( wp_mail( $send_email_to, $subject, $message, $headers, $attachments ) ){
		$response['status'] = 'sent';
		$response['msg'] = __('Your question has been sent!', 'tourfic');
	} else {
		$response['status'] = 'error';
		$response['msg'] = __('Message sent failed!', 'tourfic');
	}

	echo json_encode( $response );

	die();
}
add_action( 'wp_ajax_tf_ask_question', 'tourfic_ask_question_ajax' );
add_action( 'wp_ajax_nopriv_tf_ask_question', 'tourfic_ask_question_ajax' );

/**
 * Generate PAX
 */
function tourfic_pax( $pax ) {
	if ( $pax ) : ?>
	  	<div class="tf_pax">
	  		<?php for ($i=0; $i < $pax; $i++) {
	  			echo '<i class="fa fa-user"></i>';
	  		} ?>
	  	</div>
	<?php endif;
}

/**
 * Dropdown Multiple Support
 */
add_filter( 'wp_dropdown_cats', 'tourfic_wp_dropdown_cats_multiple', 10, 2 );
function tourfic_wp_dropdown_cats_multiple( $output, $r ) {

    if( isset( $r['multiple'] ) && $r['multiple'] ) {

         $output = preg_replace( '/^<select/i', '<select multiple', $output );

        $output = str_replace( "name='{$r['name']}'", "name='{$r['name']}[]'", $output );

        foreach ( array_map( 'trim', explode( ",", $r['selected'] ) ) as $value )
            $output = str_replace( "value=\"{$value}\"", "value=\"{$value}\" selected", $output );

    }

    return $output;
}

/**
 * Filter the excerpt "read more" string.
 *
 * @param string $more "Read more" excerpt string.
 * @return string (Maybe) modified "read more" excerpt string.
 */
function tf_tours_excerpt_more( $more ) {
	if( 'tf_tours' === get_post_type())
    return '.....';
}
add_filter( 'excerpt_more', 'tf_tours_excerpt_more' );

/**
 * Filter the except length to 30 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function tf_custom_excerpt_length( $length ) {
	if( 'tf_tours' === get_post_type())
    return 30;
}
add_filter( 'excerpt_length', 'tf_custom_excerpt_length', 999 );

// Pagination
function tourfic_posts_navigation($wp_query=''){
	if(empty($wp_query)) {
		global $wp_query;
	}
	$max_num_pages = $wp_query->max_num_pages;
	$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

	echo "<div id='am_posts_navigation'>";
	echo paginate_links( array(
		//'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
	    'format'  => 'page/%#%',
	    'current' => $paged,
	    'total'   => $max_num_pages,
	    'mid_size'        => 2,
	    'prev_next'       => true,
	) );
	echo "</div>";
}
?>
