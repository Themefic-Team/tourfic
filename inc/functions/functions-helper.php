<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Search form action url
 */
function tf_booking_search_action() {

    // get data from global settings else default
	$search_result_action = !empty( tfopt( 'search-result-page' ) ) ? get_permalink( tfopt( 'search-result-page' ) ) : home_url( '/search-result/' );
    // can be override by filter
	return apply_filters( 'tf_booking_search_action', $search_result_action );

}

/**
 * Go to Documentaion Menu Item 
 */



/**
 * Go to Documentaion Metabox
 */

function tf_hotel_tour_docs() {
    add_meta_box( 'tfhotel_docs', __( 'SETUP 2: FILTERS DATA', 'tourfic' ), 'tf_hotel_docs_callback','tf_hotel','side' ,'high');
}
add_action( 'add_meta_boxes', 'tf_hotel_tour_docs' );

function tf_hotel_docs_callback(){
?>

<?php
}

// Removed Site Store
function remove_admin_bar_links() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('view-store');
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

/**
 * Removes some menus by page.
 */
function wpdocs_remove_menus(){
	remove_menu_page( 'edit.php?post_type=product' );
	remove_menu_page( 'edit.php' );
}
add_action( 'admin_menu', 'wpdocs_remove_menus' );

/**
 * Notice wrapper
 */
function tourfic_notice_wrapper() {
	?>
	<div class="tf-container">
		<div class="tf-notice-wrapper"></div>
	</div>
	<?php
}
add_action( 'tf_before_container', 'tourfic_notice_wrapper', 10 );

/**
 * Function: tf_term_count
 * 
 * @return number of available terms
 */
if( !function_exists('tf_term_count') ) {
    function tf_term_count( $filter, $destination, $default_count ) {
        
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
        
        return count( $term_count );
        
        wp_reset_postdata();
    }
}

/**
 * Set search reult page
 */
function tourfic_booking_set_search_result( $url ) {

	$search_result_page = tfopt( 'search-result-page' );

	if ( isset( $search_result_page ) ) {
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

/**
 * Full Width Container Start
 */
function tourfic_fullwidth_container_start( $fullwidth ) {

    if ( $fullwidth == "true" ) : ?>
        <!-- Start Fullwidth Wrap -->
        <div class="tf_tf_booking-widget-wrap" data-fullwidth="true">
        <div class="tf_custom-container">
        <div class="tf_custom-inner">

    <?php endif;
}

/**
 * Full Width Container End
 */
function tourfic_fullwidth_container_end( $fullwidth ) {

    if ( $fullwidth == "true" ) : ?>
        </div></div></div>
        <!-- Close Fullwidth Wrap -->
    <?php endif;
}

/**
 * Ask Question
 */
function tourfic_ask_question() {
	?>
	<div id="tf-ask-question" style="display: none;">
		<div class="tf-aq-overlay"></div>
		<div class="tf-aq-outer">
			<span class="close-aq"><i class="fa fa-times"></i></span>
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
						<button type="submit" form="ask-question" class="button tf_button btn-styled"><?php esc_html_e( 'Submit', 'tourfic' ); ?></button>
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

/**
 * Ask question ajax
 */
function tourfic_ask_question_ajax() {

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
	if (defined( 'TF_PRO' )){
		if( "tf_hotel" == get_post_type( $post_id ) ){
			$send_email_to = !empty( tfopt('h-enquiry-email') ) ? sanitize_email( tfopt('h-enquiry-email') ) : sanitize_email( get_option( 'admin_email' ) );
		}else{
			$send_email_to = !empty( tfopt('t-enquiry-email') ) ? sanitize_email( tfopt('t-enquiry-email') ) : sanitize_email( get_option( 'admin_email' ) );
		}
	}else{
		$send_email_to = sanitize_email( get_option( 'admin_email' ) );
	}
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
    return '...';

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

/**
 * Pagination
 */
function tourfic_posts_navigation($wp_query='') {

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

/**
 * Flatpickr locale
 */
if(!function_exists('tf_flatpickr_locale')) {
	function tf_flatpickr_locale() {
		
		$flatpickr_locale = !empty(get_locale()) ? get_locale() : 'en_US';
		$allowed_locale = array('ar', 'bn_BD', 'de_DE', 'es_ES', 'fr_FR', 'hi_IN', 'it_IT', 'nl_NL', 'ru_RU', 'zh_CN');

		if (in_array($flatpickr_locale, $allowed_locale)) {

			switch ($flatpickr_locale) {
				case "bn_BD":
					$flatpickr_locale = 'bn';
					break;
				case "de_DE":
					$flatpickr_locale = 'de';
					break;
				case "es_ES":
					$flatpickr_locale = 'es';
					break;
				case "fr_FR":
					$flatpickr_locale = 'fr';
					break;
				case "hi_IN":
					$flatpickr_locale = 'hi';
					break;
				case "it_IT":
					$flatpickr_locale = 'it';
					break;
				case "nl_NL":
					$flatpickr_locale = 'nl';
					break;
				case "ru_RU":
					$flatpickr_locale = 'ru';
					break;
				case "zh_CN":
					$flatpickr_locale = 'zh';
					break;
			}

			echo 'locale: "' .$flatpickr_locale. '",';
		}

	}
}

/**
 * Flatten a multi-dimensional array into a single level.
 *
 * @author devkabir
 * 
 * @param  iterable  $array
 * @param  int  $depth
 * @return array
 */
if (!function_exists('tf_array_flatten')) {
    function tf_array_flatten($array, $depth = INF) {

        $result = [];

        foreach ($array as $item) {
            if (!is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : tf_array_flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
		
    }
}
/**
 * Calculate the deposit amount based on the deposit type and pricing. This function will return $deposit_amount and
 * $has_deposit
 *
 * @param array         $room           collection of room data
 * @param float|integer $price          calculated price
 * @param float|integer $deposit_amount calculated deposit amount
 * @param boolean       $has_deposit    is deposit allowed for this room?
 *
 * 
 * @author Dev Kabir <dev.kabir01@gmail.com>
 */
if (!function_exists('tf_get_deposit_amount')) {
    function tf_get_deposit_amount($room, $price, &$deposit_amount,  &$has_deposit)
    {
        $deposit_amount = null;
        $has_deposit = !empty($room['allow_deposit']) && $room['allow_deposit'] == true;
        if ($has_deposit == true) {
            if ($room['deposit_type'] == 'percent') {
                $deposit_amount = $price * (intval($room['deposit_amount']) / 100);
            } else {
                $deposit_amount = $room['deposit_amount'];
            }
        }
    }
};



?>