<?php

if ( !function_exists('get_field') ) {
	function get_field( $selector, $post_id = false, $format_value = true ) {

		// if not $post_id, load queried object
		if( !$post_id ) {
			// try for global post (needed for setup_postdata)
			$post_id = (int) get_the_ID();
			// try for current screen
			if( !$post_id ) {
				$post_id = get_queried_object();
			}

		}

		// format value
		if( $post_id ) {
			// get value for field
			$value = get_post_meta( $post_id, $selector, true );
		}

		// return
		return ($value) ? $value : null;

	}
}


/**
 * Review form
 */
function tourfic_get_review_form( ){
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
	        'cookies' => '<input type="checkbox" required>' . $comment_cookies_1 . '<a href="' . get_privacy_policy_url() . '">' . $comment_cookies_2 . '</a>',
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
	    'comment_field' => $comment_meta.'<p class="comment-form-comment"><textarea id="comment" name="comment" aria-required="true" placeholder="' . $comment_body .'"></textarea></p>',
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
    update_comment_meta( $comment_id, 'tf_comment_meta', sanitize_textarea_field( $_POST[ 'tf_comment_meta' ] ) );
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
    	<?php foreach ($tf_comment_meta as $key => $value) : ?>
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
 * Sample template tag function for outputting a cmb2 file_list
 *
 * @param  string  $file_list_meta_key The field meta key. ('wiki_test_file_list')
 * @param  string  $img_size           Size of image to show
 */
function tourfic_gallery_slider( $file_list_meta_key = array(), $post_id = null ) {

	if ( !$file_list_meta_key ) {
		return;
	}

	$post_id = ( $post_id ) ? $post_id : get_the_ID();
	// Get the list of files
	$tf_gallery_ids = get_post_meta( $post_id, $file_list_meta_key, 1 );

	// Comma seperated list to array
	$files = explode(',', $tf_gallery_ids);

	?>
	<div class="list-single-main-media fl-wrap" id="sec1">
	    <div class="single-slider-wrapper fl-wrap">
	        <div class="tf_slider-for fl-wrap">
				<?php foreach ( $files as $attachment_id ) {
					echo '<div class="slick-slide-item">';
						echo wp_get_attachment_image( $attachment_id, 'tf_gallery_thumb' );
					echo '</div>';
				} ?>
	        </div>
	        <div class="swiper-button-prev sw-btn"><i class="fa fa-angle-left"></i></div>
	        <div class="swiper-button-next sw-btn"><i class="fa fa-angle-right"></i></div>
	    </div>
	    <div class="single-slider-wrapper fl-wrap">
	        <div class="tf_slider-nav fl-wrap">
	        	<?php foreach ( (array) $files as $attachment_id ) {
					echo '<div class="slick-slide-item">';
						echo wp_get_attachment_image( $attachment_id, 'thumbnail' );
					echo '</div>';
				} ?>

	        </div>
	    </div>
	</div>
	<?php
}


function tourfic_booking_widget_field( $args ){
	$defaults = array (
        'type' => '',
        'svg_icon' => '',
        'id' => '',
        'name' => '',
        'default' => '',
        'options' => array(),
        'required' => false,
        'label' => '',
        'placeholder' => '',
        'class' => false,
        'disabled' => false,
        'echo' => TRUE
    );
	$args = wp_parse_args( $args, $defaults );

	$svg_icon     = esc_attr( $args['svg_icon'] );
	$type     = esc_attr( $args['type'] );
	$name     = esc_attr( $args['name'] );
    $class    = esc_attr( $args['class'] );

    $id       = $args['id'] ? esc_attr( $args['id'] ) : $name;
    $required = $args['required'] ? 'required' : '';

    $label = $args['label'] ? "<span class='tf-label'>".$args['label']."</span>" : '';

    $disabled = $args['disabled'] ? "onkeypress='return false';" : '';

    $placeholder = esc_attr( $args['placeholder'] );

    //$default_val =  isset( $_POST[$name] ) ? $_POST[$name] : tourfic_getcookie( $name );
    $default_val =  isset( $_GET[$name] ) ? $_GET[$name] : '';
    $default = $args['default'] ? sanitize_text_field( $args['default'] ) : $default_val;

    if ( !$name ) {
    	return;
    }

    $output = '';

    if ( $type == 'select' ) {

    	$output .= "<div class='tf_form-row'>";
	    	$output .= "<label class='tf_label-row'>";
	    		$output .= "<div class='tf_form-inner'>";
	    		$output .= "<span class='icon'>";
	    			$output .= tourfic_get_svg($svg_icon);
	    		$output .= "</span>";
	    		$output .= "<select $required name='$name' id='$id' class='$class'>";

	    		foreach ( $args['options'] as $key => $value) {
	    			$output .= "<option value='$key' ".selected( $default, $key, false ).">{$value}</option>";
	    		}

				$output .= "</select>
				</div>
			</label>
		</div>";

    } elseif ( $type == 'number' ) {

    	$output .= "<div class='tf_form-row'>";
	    	$output .= "<label class='tf_label-row'>";
	    		$output .= $label;
	    		$output .= "<div class='tf_form-inner'>";
	    			$output .= "<span class='icon'>";
	    				$output .= tourfic_get_svg($svg_icon);
	    			$output .= "</span>";

					$output .= "<input type='number' name='$name' $required  id='$id' $disabled class='$class' placeholder='$placeholder' value='$default' />";

				$output .= "</div>
			</label>
		</div>";

    } else {

    	$output .= "<div class='tf_form-row'>";
	    	$output .= "<label class='tf_label-row'>";
	    		$output .= $label;
	    		$output .= "<div class='tf_form-inner'>";
	    			$output .= "<span class='icon'>";
	    				$output .= tourfic_get_svg($svg_icon);
	    			$output .= "</span>";

					$output .= "<input type='text' name='$name' $required  id='$id' $disabled class='$class' placeholder='$placeholder' value='$default' />";

				$output .= "</div>
			</label>
		</div>";

    }

    if ( $args['echo'] ) {
        echo $output;
    }

    return $output;

}

// Pagination
function tourfic_posts_navigation(){
	global $wp_query;
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
 * Set Cookie Data
 */
function tourfic_setcookie( $cookie = null, $value = null ){

    $expiry = strtotime('+1 day');

	if ( $cookie && $value ) {
	    setcookie( $cookie, $value, $expiry, COOKIEPATH, COOKIE_DOMAIN );
	    return true;
	} else {
		return false;
	}
}

/**
 * Get Cookie Data
 */
function tourfic_getcookie( $cookie = null ){
	if ( $cookie && isset( $_COOKIE[$cookie] ) ) {
		return $_COOKIE[$cookie];
	} else {
		return false;
	}
}

/**
 * Sitewide Set Cookie Function
 *
 */
function tourfic_setcookie_sitewide(){

	if ( is_admin() ) {
		//return;
	}

    $user_id = get_current_user_id();

    if( isset( $_GET['check-in-date'] ) ) {

    	foreach ( $_GET as $key => $value ) {
    		tourfic_setcookie( $key, $value );
    	}

    }

}
add_action('init', 'tourfic_setcookie_sitewide', 5 );
//add_action('template_redirect', 'tourfic_setcookie_sitewide', 5 );

/**
 * Get Cookie Data
 */
function tourfic_delete_cookie( $cookie = null ){

    $expiry = strtotime('-1 day');

	if ( $cookie && isset( $_COOKIE[$cookie] ) ) {
	    unset( $_COOKIE[$cookie] );

	    setcookie($cookie, '', $expiry, COOKIEPATH, COOKIE_DOMAIN);
	    setcookie($cookie, '', $expiry, "/");

	    return true;
	} else {
		return false;
	}
}

/**
 * Submit button data
 */
function tourfic_room_booking_submit_button( $label = null ){

	$booking_fields = array(
		'destination',
		'check-in-date',
		'check-out-date',
		'adults',
		'room',
		'children',
	);

	foreach ( $booking_fields as $key ) {

		$value = isset( $_GET[$key] ) ? $_GET[$key] : tourfic_getcookie( $key );

		echo "<input type='hidden' placeholder='{$key}' name='{$key}' value='{$value}'>";
	}

	?>
	<button class="tf_button" type="submit"><?php esc_html_e( $label ); ?></button>
	<?php
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

// Booking Form Action Link
function tourfic_booking_search_action(){
	return apply_filters( 'tf_booking_search_action', esc_url( home_url('/search-result/') ) );
}

// Set search reult page
function tourfic_booking_set_search_result( $url ){
	global $tourfic_opt;

	if ( isset( $tourfic_opt['search-result-page'] ) ) {
		$url = get_permalink( $tourfic_opt['search-result-page'] );
	}

	return $url;

}
add_filter( 'tf_booking_search_action', 'tourfic_booking_set_search_result' );



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

	<div class="price-per-night"><?php esc_html_e( 'Price per night as low as', 'tourfic' ); ?></div>

	<?php
	return ob_get_clean();
}

// return only raw price
function tourfic_price_raw( $price = null, $sale_price = null ) {
	if ( !$price ) {
		return;
	}

	if ( $sale_price > 0 ) {
		return $sale_price;
	}

	return $price;
}

// Sale tag
function tourfic_sale_tag( $price = null, $sale_price = null ) {
	if ( !$sale_price ) {
		return;
	}

	$parsent = number_format((($price-$sale_price)/$price)*100,1);

	ob_start();
	?>
	<?php if ( $sale_price > 0 ) { ?>
		<div class="tf-sale-tag"><?php printf( esc_html( 'Save %s%% Today', 'tourfic' ), $parsent ); ?></div>
	<?php } ?>
	<?php
	return ob_get_clean();
}


/**
 * Custom Image Upload to taxonomy
 **/
if ( ! class_exists( 'TOURFIC_TAX_META' ) ) {

	class TOURFIC_TAX_META {

	  public function __construct() {
	    //
	  }

	 /*
	  * Initialize the class and start calling our hooks and filters
	  * @since 1.0.0
	 */
	 public function init() {
	   add_action( 'destination_add_form_fields', array ( $this, 'add_category_image' ), 10, 2 );
	   add_action( 'created_destination', array ( $this, 'save_category_image' ), 10, 2 );
	   add_action( 'destination_edit_form_fields', array ( $this, 'update_category_image' ), 10, 2 );
	   add_action( 'edited_destination', array ( $this, 'updated_category_image' ), 10, 2 );
	   add_action( 'admin_enqueue_scripts', array( $this, 'load_media' ) );
	   add_action( 'admin_footer', array ( $this, 'add_script' ) );
	 }

	public function load_media() {
	 wp_enqueue_media();
	}

	 /*
	  * Add a form field in the new category page
	  * @since 1.0.0
	 */
	 public function add_category_image ( $taxonomy ) { ?>
	   <div class="form-field term-group">
	     <label for="category-image-id"><?php _e('Destination Image', 'tourfic'); ?></label>
	     <input type="hidden" id="category-image-id" name="category-image-id" class="custom_media_url" value="">
	     <div id="category-image-wrapper"></div>
	     <p>
	       <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add Image', 'tourfic' ); ?>" />
	       <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove Image', 'tourfic' ); ?>" />
	    </p>
	   </div>
	 <?php
	 }

	 /*
	  * Save the form field
	  * @since 1.0.0
	 */
	 public function save_category_image ( $term_id, $tt_id ) {
	   if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
	     $image = $_POST['category-image-id'];
	     add_term_meta( $term_id, 'category-image-id', $image, true );
	   }
	 }

	 /*
	  * Edit the form field
	  * @since 1.0.0
	 */
	 public function update_category_image ( $term, $taxonomy ) { ?>
	   <tr class="form-field term-group-wrap">
	     <th scope="row">
	       <label for="category-image-id"><?php _e( 'Destination Image', 'tourfic' ); ?></label>
	     </th>
	     <td>
	       <?php $image_id = get_term_meta ( $term -> term_id, 'category-image-id', true ); ?>
	       <input type="hidden" id="category-image-id" name="category-image-id" value="<?php echo $image_id; ?>">
	       <div id="category-image-wrapper">
	         <?php if ( $image_id ) { ?>
	           <?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
	         <?php } ?>
	       </div>
	       <p>
	         <input type="button" class="button button-secondary ct_tax_media_button" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php _e( 'Add Image', 'tourfic' ); ?>" />
	         <input type="button" class="button button-secondary ct_tax_media_remove" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php _e( 'Remove Image', 'tourfic' ); ?>" />
	       </p>
	     </td>
	   </tr>
	 <?php
	 }

	/*
	 * Update the form field value
	 * @since 1.0.0
	 */
	 public function updated_category_image ( $term_id, $tt_id ) {
	   if( isset( $_POST['category-image-id'] ) && '' !== $_POST['category-image-id'] ){
	     $image = $_POST['category-image-id'];
	     update_term_meta ( $term_id, 'category-image-id', $image );
	   } else {
	     update_term_meta ( $term_id, 'category-image-id', '' );
	   }
	 }

	/*
	 * Add script
	* @since 1.0.0
	*/
	public function add_script() {
		global $pagenow;

		if( $pagenow == "edit-tags.php" || $pagenow == "term.php" ) {			
		?>
		<script>
		    jQuery(document).ready( function($) {
		      function ct_media_upload(button_class) {
		        var _custom_media = true,
		        _orig_send_attachment = wp.media.editor.send.attachment;
		        $('body').on('click', button_class, function(e) {
		          var button_id = '#'+$(this).attr('id');
		          var send_attachment_bkp = wp.media.editor.send.attachment;
		          var button = $(button_id);
		          _custom_media = true;
		          wp.media.editor.send.attachment = function(props, attachment){
		            if ( _custom_media ) {
		              $('#category-image-id').val(attachment.id);
		              $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
		              $('#category-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
		            } else {
		              return _orig_send_attachment.apply( button_id, [props, attachment] );
		            }
		           }
		        wp.media.editor.open(button);
		        return false;
		      });
		    }
		    ct_media_upload('.ct_tax_media_button.button');
		    $('body').on('click','.ct_tax_media_remove',function(){
		      $('#category-image-id').val('');
		      $('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
		    });
		    // Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
		    $(document).ajaxComplete(function(event, xhr, settings) {
		      var queryStringArr = settings.data.split('&');
		      if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
		        var xml = xhr.responseXML;
		        $response = $(xml).find('term_id').text();
		        if($response!=""){
		          // Clear the thumb image
		          $('#category-image-wrapper').html('');
		        }
		      }
		    });
		  });
		</script>
		<?php } 
	  }

	}

	$CT_TAX_META = new TOURFIC_TAX_META();
	$CT_TAX_META -> init();

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
 * Change Post Type Slug
 */
function tourfic_change_tourfic_post_type_slug( $slug ){
	global $tourfic_opt;

	if ( isset( $tourfic_opt['post_type_slug'] ) && $tourfic_opt['post_type_slug'] != "" ) {
		$slug = esc_attr( $tourfic_opt['post_type_slug'] );
	}

	return $slug;
}
add_filter( 'tourfic_post_type_slug', 'tourfic_change_tourfic_post_type_slug', 10, 1 );

/**
 * Flush after redux save
 */
function tourfic_flush_permalink( $value ){
	flush_rewrite_rules();
}
add_action('redux/options/tourfic_opt/saved', 'tourfic_flush_permalink' );
add_action('redux/options/tourfic_opt/reset', 'tourfic_flush_permalink' );
add_action('redux/options/tourfic_opt/settings/change', 'tourfic_flush_permalink' );
add_action('redux/options/tourfic_opt/section/reset', 'tourfic_flush_permalink' );


/**
 *	Custom CSS function
 */
function tourfic_custom_css(){
	global $tourfic_opt;

	$output = '';

	//Custom css
	if ( isset( $tourfic_opt['custom-css'] ) ) {
		$output .=  $tourfic_opt['custom-css'];
	}

	wp_add_inline_style( 'tourfic-styles', $output );
}
add_action( 'wp_enqueue_scripts', 'tourfic_custom_css', 200 );

/**
 * Get AVG
 */
function tourfic_avg_ratings( $a = array() ){
	if ( !$a ) {
		return '5.0';
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

/**
 * Add Tourfic sidebar.
 */
function tourfic_sidebar_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'TOURFIC: Single Tour Sidebar', 'tourfic' ),
        'id'            => 'tf_single_booking_sidebar',
        'description'   => __( 'Widgets in this area will be shown on tourfic single page', 'tourfic' ),
        'before_widget' => '<div id="%1$s" class="tf_widget widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="tf_widgettitle">',
        'after_title'   => '</h4>',
    ) );
    register_sidebar( array(
        'name'          => __( 'TOURFIC: Archive Sidebar', 'tourfic' ),
        'id'            => 'tf_archive_booking_sidebar',
        'description'   => __( 'Widgets in this area will be shown on tourfic archive/search page', 'tourfic' ),
        'before_widget' => '<div id="%1$s" class="tf_widget widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="tf_widgettitle">',
        'after_title'   => '</h4>',
    ) );

    // Register Custom Widgets
    $custom_widgets = array(
    	'Tourfic_TourFilter',
    	'Tourfic_Show_On_Map',
    	'Tourfic_Ask_Question',
    	'Tourfic_Similar_Tours'
    );
    foreach ($custom_widgets as $key => $widget) {
    	register_widget( $widget );
    }

}
add_action( 'widgets_init', 'tourfic_sidebar_widgets_init', 100 );

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