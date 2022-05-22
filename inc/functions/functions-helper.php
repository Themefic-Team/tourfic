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


if (!function_exists('tf_array_flatten')) {
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @author devkabir
     * 
     * @param  iterable  $array
     * @param  int  $depth
     * @return array
     */
    function tf_array_flatten($array, $depth = INF)
    {
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
};

###############################
# Tour Functions			  #
###############################

/**
 * Price calculation for single tour
 */
if(!function_exists('tf_single_tour_price')) {
	function tf_single_tour_price($meta, $output='') {
	
		# Get tour type
		$tour_type = !empty($meta['type']) ? $meta['type'] : 'continuous';
	
		# Custom availability status
		if($tour_type == 'continuous') {
			$custom_avail = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
		}
	
		# Get discounts
		$discount_type = !empty($meta['discount_type']) ? $meta['discount_type'] : 'none';
		$discounted_price = !empty($meta['discount_price']) ? $meta['discount_price'] : '';
	
		# Get adult, child, infant enable/disable
		$disable_adult = !empty($meta['disable_adult_price']) ? $meta['disable_adult_price'] : false;
		$disable_child = !empty($meta['disable_child_price']) ? $meta['disable_child_price'] : false;
		$disable_infant = !empty($meta['disable_infant_price']) ? $meta['disable_infant_price'] : false;
	
		# Set initial tour price
		$price = '0.0';
	
		/**
		 * Price calculation based on custom availability
		 * 
		 * Custom availability has different pricing calculation
		 */
		if($tour_type == 'continuous' && $custom_avail == true) {
	
			# Get pricing rule person/group
			$pricing_rule = !empty($meta['custom_pricing_by']) ? $meta['custom_pricing_by'] : 'person';
	
			/**
			 * Price calculation based on pricing rule
			 */
			if($pricing_rule == 'group') {
	
				# Get group price from all the arrays
				$group_prices = array_column($meta['cont_custom_date'], 'group_price');
				# Get minimum group price
				$min_group_price = min($group_prices);
				# Get maximum group price
				$max_group_price = max($group_prices);
			
				# Discount price calculation
				if($discount_type == 'percent') {
	
					$sale_min_group_price = number_format( $min_group_price - (( $min_group_price / 100 ) * $discounted_price) ,1 );
					$sale_max_group_price = number_format( $max_group_price - (( $max_group_price / 100 ) * $discounted_price) ,1 );
	
				} else if($discount_type == 'fixed') {
	
					$sale_min_group_price = number_format( ( $min_group_price - $discounted_price ),1 );
					$sale_max_group_price = number_format( ( $max_group_price - $discounted_price ),1 );
	
				}
	
				if($discount_type == 'percent' || $discount_type == 'fixed') {

					# WooCommerce Price
					$wc_min_group_price = wc_price($sale_min_group_price, array('decimals'=>0)); // Discounted price
					$wc_max_group_price = wc_price($sale_max_group_price, array('decimals'=>0)); // // Discounted price

					# Final output (price range)
					$price = $sale_min_group_price. '-' .$sale_max_group_price; // Discounted price range
					$wc_price = $wc_min_group_price. '-' .$wc_max_group_price; // Discounted WooCommerce price range

				} else {

					# WooCommerce Price
					$wc_min_group_price = wc_price($min_group_price, array('decimals'=>0)); // Discounted price
					$wc_max_group_price = wc_price($max_group_price, array('decimals'=>0)); // // Discounted price

					# Final output (price range)
					$price = $min_group_price. '-' .$max_group_price; // Discounted price range
					$wc_price = $wc_min_group_price. '-' .$wc_max_group_price; // Discounted WooCommerce price range

				}
	
			} else if($pricing_rule == 'person') {

				# Get adult, child, infant price from all the arrays
				$adult_price = array_column($meta['cont_custom_date'], 'adult_price');
				$child_price = array_column($meta['cont_custom_date'], 'child_price');
				$infant_price = array_column($meta['cont_custom_date'], 'infant_price');

				# Get minimum price of adult, child, infant
				$min_adult_price = min($adult_price);
				$min_child_price = min($child_price);
				$min_infant_price = min($infant_price);

				# Get maximum price of adult, child, infant
				$max_adult_price = min($adult_price);
				$max_child_price = min($child_price);
				$max_infant_price = min($infant_price);

				# Discount price calculation
				if($discount_type == 'percent') {
	
					# Minimum discounted price
					$sale_min_adult_price = number_format( $min_adult_price - (( $min_adult_price / 100 ) * $discounted_price) ,1 );
					$sale_min_child_price = number_format( $min_child_price - (( $min_child_price / 100 ) * $discounted_price) ,1 );
					$sale_min_infant_price = number_format( $min_infant_price - (( $min_infant_price / 100 ) * $discounted_price) ,1 );
					# Maximum discounted price
					$sale_max_adult_price = number_format( $max_adult_price - (( $max_adult_price / 100 ) * $discounted_price) ,1 );
					$sale_max_child_price = number_format( $max_child_price - (( $max_child_price / 100 ) * $discounted_price) ,1 );
					$sale_max_infant_price = number_format( $max_infant_price - (( $max_infant_price / 100 ) * $discounted_price) ,1 );
	
				} else if($discount_type == 'fixed') {
	
					# Minimum discounted price
					$sale_min_adult_price = number_format( ( $min_adult_price - $discounted_price ),1 );
					$sale_min_child_price = number_format( ( $min_child_price - $discounted_price ),1 );
					$sale_min_infant_price = number_format( ( $min_infant_price - $discounted_price ),1 );
					# Maximum discounted price
					$sale_max_adult_price = number_format( ( $max_adult_price - $discounted_price ),1 );
					$sale_max_child_price = number_format( ( $max_child_price - $discounted_price ),1 );
					$sale_max_infant_price = number_format( ( $max_infant_price - $discounted_price ),1 );
	
				}
	
				# WooCommerce Price
				$wc_min_adult_price = wc_price($sale_min_adult_price, array('decimals'=>0)); // Discounted min price
				$wc_min_child_price = wc_price($sale_min_child_price, array('decimals'=>0)); // Discounted min price
				$wc_min_infant_price = wc_price($sale_min_infant_price, array('decimals'=>0)); // Discounted min price

				$wc_max_adult_price = wc_price($sale_max_adult_price, array('decimals'=>0)); // // Discounted WooCommerce max price
				$wc_max_child_price = wc_price($sale_max_child_price, array('decimals'=>0)); // // Discounted WooCommerce max price
				$wc_max_infant_price = wc_price($sale_max_infant_price, array('decimals'=>0)); // // Discounted WooCommerce max price
	
				# Final output (price range)
				$adult_price = $sale_min_adult_price. '-' .$sale_max_adult_price; // Discounted price range
				$child_price = $sale_min_child_price. '-' .$sale_max_child_price; // Discounted price range
				$infant_price = $sale_min_infant_price. '-' .$sale_max_infant_price; // Discounted price range

				$wc_adult_price = $wc_min_adult_price. '-' .$wc_max_adult_price; // Discounted WooCommerce price range
				$wc_child_price = $wc_min_child_price. '-' .$wc_max_child_price; // Discounted WooCommerce price range
				$wc_infant_price = $wc_min_infant_price. '-' .$wc_max_infant_price; // Discounted WooCommerce price range
	
			}
	
		} else {
	
			/**
			 * Pricing for fixed/continuous
			 */
	
			# Get pricing rule person/group
			$pricing_rule = !empty($meta['pricing']) ? $meta['pricing'] : 'person';
	
			/**
			 * Price calculation based on pricing rule
			 */
			if($pricing_rule == 'group') {
	
				# Get group price. Default 0
				$price = !empty($meta['group_price']) ? $meta['group_price'] : '0.0';
			
				if($discount_type == 'percent') {
					$sale_price = number_format( $price - (( $price / 100 ) * $discounted_price) ,1 );
				} else if($discount_type == 'fixed') {
					$sale_price = number_format( ( $price - $discounted_price ),1 );
				}

				# WooCommerce Price
				$wc_price = wc_price($price, array('decimals'=>0));
				$wc_sale_price = wc_price($sale_price, array('decimals'=>0));
	
			} else if($pricing_rule == 'person') {
	
				$adult_price = !empty($meta['adult_price']) ? $meta['adult_price'] : '';
				$child_price = !empty($meta['child_price']) ? $meta['child_price'] : '';
				$infant_price = !empty($meta['infant_price']) ? $meta['infant_price'] : '';
			
				if($discount_type == 'percent') {
	
					$adult_price ? $sale_adult_price = number_format( $adult_price - (( $adult_price / 100 ) * $discounted_price) ,1 ) : '';
					$child_price ? $sale_child_price = number_format( $child_price - (( $child_price / 100 ) * $discounted_price) ,1 ) : '';
					$infant_price ? $sale_infant_price = number_format( $infant_price - (( $infant_price / 100 ) * $discounted_price) ,1 ) : '';
	
				} else if($discount_type == 'fixed') {
	
					$adult_price ? $sale_adult_price = number_format( ( $adult_price - $discounted_price ),1 ) : '';
					$child_price ? $sale_child_price = number_format( ( $child_price - $discounted_price ),1 ) : '';
					$infant_price ? $sale_infant_price = number_format( ( $infant_price - $discounted_price ),1 ) : '';
	
				}

				# WooCommerce Price
				$wc_adult_price = wc_price($adult_price, array('decimals'=>0));
				$wc_child_price = wc_price($child_price, array('decimals'=>0));
				$wc_infant_price = wc_price($infant_price, array('decimals'=>0));

				$wc_sale_adult_price = wc_price($sale_adult_price, array('decimals'=>0));
				$wc_sale_child_price = wc_price($sale_child_price, array('decimals'=>0));
				$wc_sale_infant_price = wc_price($sale_infant_price, array('decimals'=>0));
	
			}

		}

		/**
		 * Conditional output
		 */
		switch ($output) {
			# Group Price
			case 'group':
				echo $price;
				break;
			case 'wc_group':
				echo $wc_price;
				break;
			# Group discount Price
			case 'sale_group':
				echo $sale_price;
				break;
			case 'wc_sale_group':
				echo $wc_sale_price;
				break;

			# Adult price
			case 'adult':
				echo $adult_price;
				break;
			case 'wc_adult':
				echo $wc_adult_price;
				break;
			# Adult discount Price
			case 'sale_adult':
				echo $sale_adult_price;
				break;
			case 'wc_sale_adult':
				echo $wc_sale_adult_price;
				break;

			# Child price
			case 'child':
				echo $child_price;
				break;
			case 'wc_child':
				echo $wc_child_price;
				break;
			# Child discount Price
			case 'sale_child':
				echo $sale_child_price;
				break;
			case 'wc_sale_child':
				echo $wc_sale_child_price;
				break;

			# Infant price
			case 'infant':
				echo $infant_price;
				break;
			case 'wc_infant':
				echo $wc_infant_price;
				break;
			# Infant discount Price
			case 'sale_infant':
				echo $sale_infant_price;
				break;
			case 'wc_sale_infant':
				echo $wc_sale_infant_price;
				break;
		}
	
	}
}
?>