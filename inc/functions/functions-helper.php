<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

/**
 * Search form action url
 */
function tf_booking_search_action() {

	// get data from global settings else default
	$search_result_action = ! empty( Helper::tfopt( 'search-result-page' ) ) ? get_permalink( Helper::tfopt( 'search-result-page' ) ) : home_url( '/search-result/' );

	// can be override by filter
	return apply_filters( 'tf_booking_search_action', $search_result_action );

}

/**
 * Go to Documentaion Menu Item
 */

add_action( 'admin_menu', 'tf_documentation_page_integration', 999 );
function tf_documentation_page_integration() {
	global $current_user;
	$tf_current_role = $current_user->roles[0];
	global $submenu;
	$tfhoteldocumentation = sanitize_url( 'https://themefic.com/docs/tourfic/' );
	$tftourdocumentation  = sanitize_url( 'https://themefic.com/docs/tourfic/' );
	if ( $tf_current_role == "administrator" ) {
		$submenu['edit.php?post_type=tf_hotel'][]     = array( sprintf( '<span class="tf-go-docs" style=color:#ffba00;">%s</span>', esc_html__( 'Go to Documentation', 'tourfic' ) ), 'edit_tf_hotels', $tfhoteldocumentation );
		$submenu['edit.php?post_type=tf_apartment'][] = array( sprintf( '<span class="tf-go-docs" style=color:#ffba00;">%s</span>', esc_html__( 'Go to Documentation', 'tourfic' ) ), 'edit_tf_apartments', $tfhoteldocumentation );
		$submenu['edit.php?post_type=tf_tours'][]     = array( sprintf( '<span class="tf-go-docs" style=color:#ffba00;">%s</span>', esc_html__( 'Go to Documentation', 'tourfic' ) ), 'edit_tf_tourss', $tftourdocumentation );
	}
}

 


/**
 * Go to Documentaion Metabox
 */


function tf_hotel_tour_docs() {
	global $current_user;
	$tf_current_role = $current_user->roles[0];
	if ( $tf_current_role == "administrator" ) {
		add_meta_box( 'tfhotel_docs', esc_html__( 'Tourfic Documentation', 'tourfic' ), 'tf_hotel_docs_callback', 'tf_hotel', 'side', 'high' );
		add_meta_box( 'tfapartment_docs', esc_html__( 'Tourfic Documantation', 'tourfic' ), 'tf_apartment_docs_callback', 'tf_apartment', 'side', 'high' );
		add_meta_box( 'tftour_docs', esc_html__( 'Tourfic Documentation', 'tourfic' ), 'tf_tour_docs_callback', 'tf_tours', 'side', 'high' );

		add_filter( 'get_user_option_meta-box-order_tf_tours', 'tour_metabox_order' );
		add_filter( 'get_user_option_meta-box-order_tf_apartment', 'apartment_metabox_order' );
		add_filter( 'get_user_option_meta-box-order_tf_hotel', 'hotel_metabox_order' );
	}
}

add_action( 'add_meta_boxes', 'tf_hotel_tour_docs' );

function tf_hotel_docs_callback() {
	$tfhoteldocumentation = sanitize_url( 'https://themefic.com/docs/tourfic/how-it-works/add-new-hotel/' );
	?>
    <div class="tf_docs_preview">
        <a href="<?php echo esc_url( $tfhoteldocumentation ); ?>" target="_blank">
			<img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . 'images/banner-cta.png'); ?>" alt="<?php echo esc_html__( 'Go to Documentation', 'tourfic' ); ?>">
		</a>
    </div>
	<?php
}

function tf_apartment_docs_callback() {
	global $wp_meta_boxes;
	$tf_apartment_documentation = sanitize_url( 'https://themefic.com/docs/tourfic/add-new-apartment/locations-types-and-featured-image/' );
	?>
    <div class="tf_docs_preview">
        <a href="<?php echo esc_url( $tf_apartment_documentation ); ?>" target="_blank">
			<img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . 'images/banner-cta.png'); ?>" alt="<?php echo esc_html__( 'Go to Documentation', 'tourfic' ); ?>">
		</a>
    </div>
	<?php
}

function tf_tour_docs_callback() {
	$tf_tour_documentation = sanitize_url( 'https://themefic.com/docs/tourfic/tours/tourfic-hotel-general-settings/' );
	?>
    <div class="tf_docs_preview">
        <a href="<?php echo esc_url( $tf_tour_documentation); ?>" target="_blank">
			<img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . 'images/banner-cta.png'); ?>" alt="<?php echo esc_html__( 'Go to Documentation', 'tourfic' ); ?>">
		</a>
    </div>
	<?php
}

// doc link Positioning Start

function apartment_metabox_order( $order ) {
	return array(
		'side' => join( 
			",", 
			array (
				'submitdiv',
				'tfapartment_docs',
				'tfapartment_black_friday_docs'
			)
		),
	);
}

function tour_metabox_order( $order ) {
	return array(
		'side' => join( 
			",", 
			array(
				'submitdiv',
				'tftour_docs',
				'tftour_black_friday_docs'
			)
		),
	);
}

function hotel_metabox_order( $order ) {
	return array(
		'side' => join( 
			",", 
			array(
				'submitdiv',
				'tfhotel_docs',
				'tfhotel_black_friday_docs'
			)
		),
	);
}

// doc link Positioning end

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
if ( ! function_exists( 'tf_term_count' ) ) {
	function tf_term_count( $filter, $destination, $default_count ) {

		if ( $destination == '' ) {
			return $default_count;
		}

		$term_count = array();

		$args = array(
			'post_type'      => 'tf_hotel',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
			'tax_query'      => array(
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

				if ( has_term( $filter, 'tf_filters', get_the_ID() ) == true ) {
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

	$search_result_page = Helper::tfopt( 'search-result-page' );

	if ( isset( $search_result_page ) ) {
		$url = get_permalink( $search_result_page );
	}

	return $url;

}

add_filter( 'tf_booking_search_action', 'tourfic_booking_set_search_result' );

// Tours price with html format
function tf_tours_price_html() {

	$meta         = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
	$pricing_rule = $meta['pricing'] ? $meta['pricing'] : null;
	$tour_type    = $meta['type'] ? $meta['type'] : null;
	if ( $pricing_rule == 'group' ) {
		$price = $meta['group_price'] ? $meta['group_price'] : 0;
	} else {
		$price = $meta['adult_price'] ? $meta['adult_price'] : 0;
	}
	$discount_type    = $meta['discount_type'] ? $meta['discount_type'] : null;
	$discounted_price = $meta['discount_price'] ? $meta['discount_price'] : null;
	if ( $discount_type == 'percent' ) {
		$sale_price = number_format( $price - ( ( $price / 100 ) * $discounted_price ), 1 );
	} elseif ( $discount_type == 'fixed' ) {
		$sale_price = number_format( ( $price - $discounted_price ), 1 );
	} else if ( $discount_type == 'none' ) {
		$sale_price = number_format( $price, 1 );
	}
	if ( ! $price ) {
		echo "<span class='tf-price'></span>";
	}
	ob_start();
	?>
	<?php if ( $sale_price < $price && $discounted_price > 0 && $discount_type != 'none' ) { ?>
        <span class="tf-price"><del><?php echo wp_kses_post(wc_price( $price )); ?></del></span>
        <span class="tf-price"><?php echo wp_kses_post(wc_price( $sale_price )); ?></span>
	<?php } else { ?>
        <span class="tf-price"><?php echo wp_kses_post(wc_price( $price )); ?></span>
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
            </div>
        </div>
    </div>
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
			<span class="close-aq">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
					<path d="M15 5L5 15" stroke="#4E667E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M5 5L15 15" stroke="#4E667E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</span>
			<div class="tf-aq-inner">
				<div class="tf-ask-question-head-content">
					<div class="ask-question-svg">
						<svg xmlns="http://www.w3.org/2000/svg" width="82" height="65" viewBox="0 0 82 65" fill="none">
							<path d="M39.8433 2.87109C51.7608 2.87109 58.7471 8.37858 58.7471 17.8305C58.7471 29.748 46.4165 32.1321 46.4165 40.763H32.6912C32.6912 28.5165 41.7326 27.2009 41.7326 20.8724C41.7326 18.4883 40.5827 17.42 38.1171 17.42C35.0752 17.42 33.678 19.4751 33.678 23.9143H19.5396C19.5396 10.681 27.0206 2.87109 39.8433 2.87109ZM47.7348 53.5041C47.7348 58.5169 44.2824 61.9693 39.2697 61.9693C34.3384 61.9693 30.8861 58.5169 30.8861 53.5041C30.8861 48.5729 34.3384 45.1206 39.2697 45.1206C44.2824 45.1206 47.7348 48.5729 47.7348 53.5041Z" fill="#CFE6FC"/>
							<path d="M41.688 1C53.6056 1 60.5919 6.50748 60.5919 15.9594C60.5919 27.8769 48.2612 30.261 48.2612 38.8919H34.5359C34.5359 26.6455 43.5774 25.3298 43.5774 19.0013C43.5774 16.6173 42.4274 15.5489 39.9618 15.5489C36.92 15.5489 35.5227 17.604 35.5227 22.0432H21.3843C21.3869 8.80731 28.8653 1 41.688 1ZM49.5795 51.6331C49.5795 56.6458 46.1272 60.0982 41.1144 60.0982C36.1832 60.0982 32.7308 56.6458 32.7308 51.6331C32.7308 46.7018 36.1832 43.2495 41.1144 43.2495C46.1272 43.2468 49.5795 46.6992 49.5795 51.6331Z" stroke="#1B334B" stroke-width="1.5" stroke-miterlimit="10"/>
							<path d="M24.1691 51.6832C19.7378 56.1144 15.0908 56.6644 11.5753 53.1515C7.14403 48.7202 10.8411 43.2496 7.63346 40.0393L12.7383 34.9344C17.2933 39.4893 14.4198 43.3391 16.7723 45.6941C17.659 46.5809 18.4827 46.5493 19.401 45.6336C20.5325 44.5021 20.2878 43.218 18.6379 41.5681L23.8954 36.3106C28.8161 41.2313 28.9371 46.9151 24.1691 51.6832ZM2.40754 35.7896C0.541888 33.924 0.541888 31.3584 2.40754 29.4927C4.24161 27.6587 6.80984 27.6587 8.67286 29.5243C10.5069 31.3584 10.5069 33.9266 8.67286 35.7607C6.80984 37.6237 4.24161 37.6237 2.40754 35.7896Z" fill="#CFE6FC"/>
							<path d="M24.1795 53.064C19.7483 57.4953 15.1012 58.0452 11.5857 54.5323C7.15447 50.1011 10.8516 44.6304 7.64391 41.4202L12.7488 36.3153C17.3037 40.8702 14.4302 44.7199 16.7827 47.075C17.6695 47.9618 18.4931 47.9302 19.4114 47.0145C20.5429 45.883 20.2982 44.5989 18.6483 42.949L23.9058 37.6915C28.8239 42.6122 28.9476 48.296 24.1795 53.064ZM2.41535 37.1705C0.549701 35.3048 0.549701 32.7392 2.41535 30.8736C4.24942 29.0395 6.81765 29.0395 8.68067 30.9052C10.5147 32.7392 10.5147 35.3075 8.68067 37.1415C6.81765 39.0046 4.24942 39.0046 2.41535 37.1705Z" stroke="#1B334B" stroke-width="1.5" stroke-miterlimit="10"/>
							<path d="M75.1344 30.6488C79.9683 34.638 80.9577 39.2113 77.7921 43.0452C73.8029 47.8791 68.0034 44.7188 65.1141 48.2185L59.5461 43.6241C63.6458 38.6561 67.7534 41.1506 69.8717 38.585C70.669 37.6193 70.5611 36.8009 69.5612 35.9747C68.327 34.9563 67.0719 35.3221 65.5878 37.122L59.8566 32.3855C64.2879 27.0201 69.9348 26.357 75.1344 30.6488ZM61.3828 53.8234C59.704 55.8574 57.1489 56.1021 55.1149 54.4233C53.115 52.7734 52.8703 50.2157 54.5491 48.1843C56.199 46.1845 58.7567 45.9397 60.7566 47.5922C62.7906 49.2684 63.0353 51.8235 61.3828 53.8234Z" fill="#CFE6FC"/>
							<path d="M76.5077 30.506C81.3416 34.4951 82.331 39.0685 79.1654 42.9024C75.1762 47.7363 69.3767 44.576 66.4874 48.0757L60.9194 43.4813C65.0191 38.5133 69.1267 41.0078 71.245 38.4422C72.0423 37.4765 71.9344 36.6581 70.9345 35.8319C69.7003 34.8135 68.4452 35.1793 66.9611 36.9792L61.2273 32.2453C65.6612 26.8747 71.3081 26.2116 76.5077 30.506ZM62.7561 53.6806C61.0773 55.7146 58.5222 55.9593 56.4882 54.2805C54.4883 52.6306 54.2436 50.0729 55.9224 48.0415C57.5723 46.0416 60.13 45.7969 62.1299 47.4494C64.1639 49.1256 64.4086 51.6807 62.7561 53.6806Z" stroke="#1B334B" stroke-width="1.5" stroke-miterlimit="10"/>
							<path d="M14.8461 23.9229C13.7856 22.073 12.7252 20.2231 11.6647 18.3733C11.4121 17.9338 10.7306 18.3285 10.9832 18.7706C12.0437 20.6205 13.1041 22.4703 14.1645 24.3202C14.4172 24.7623 15.0987 24.3649 14.8461 23.9229Z" fill="#1B334B"/>
							<path d="M17.0346 22.8627C16.9057 21.2681 16.7794 19.6708 16.6505 18.0762C16.611 17.5736 15.8216 17.5683 15.8611 18.0762C15.99 19.6708 16.1163 21.2681 16.2452 22.8627C16.2873 23.3679 17.0768 23.3705 17.0346 22.8627Z" fill="#1B334B"/>
							<path d="M12.3674 25.4572C11.2912 24.7546 10.2176 24.0494 9.14137 23.3468C8.71508 23.0679 8.31775 23.7521 8.74403 24.0283C9.82027 24.7309 10.8939 25.4361 11.9701 26.1387C12.3938 26.4176 12.7911 25.7335 12.3674 25.4572Z" fill="#1B334B"/>
							<path d="M63.754 20.4706C64.3934 19.1075 65.0302 17.7419 65.6696 16.3788C65.8828 15.9209 65.2039 15.521 64.9881 15.9815C64.3487 17.3445 63.7119 18.7102 63.0724 20.0733C62.8567 20.5311 63.5382 20.9311 63.754 20.4706Z" fill="#1B334B"/>
							<path d="M65.024 22.4106C66.7818 21.2449 68.5369 20.0819 70.2947 18.9162C70.7157 18.6372 70.321 17.9531 69.8973 18.2346C68.1396 19.4003 66.3844 20.5634 64.6267 21.7291C64.203 22.008 64.5977 22.6922 65.024 22.4106Z" fill="#1B334B"/>
							<path d="M66.0904 24.4261C67.5955 24.2129 69.1007 23.9998 70.6084 23.7866C71.1084 23.7156 70.8953 22.9551 70.3979 23.0262C68.8928 23.2393 67.3876 23.4524 65.8798 23.6656C65.3799 23.734 65.593 24.4945 66.0904 24.4261Z" fill="#1B334B"/>
							<path d="M50.6624 59.706C51.9308 61.4164 53.2017 63.1268 54.47 64.8346C54.77 65.2372 55.4542 64.8451 55.1516 64.4372C53.8832 62.7268 52.6123 61.0164 51.344 59.3087C51.044 58.9034 50.3572 59.2981 50.6624 59.706Z" fill="#1B334B"/>
							<path d="M53.1403 58.175C54.2166 58.8776 55.2902 59.5828 56.3664 60.2854C56.7927 60.5643 57.19 59.8802 56.7637 59.6039C55.6875 58.9013 54.6139 58.1961 53.5377 57.4935C53.1114 57.2146 52.7167 57.8961 53.1403 58.175Z" fill="#1B334B"/>
							<path d="M26.5213 58.8527C25.3767 60.6236 24.232 62.3919 23.0874 64.1628C22.8111 64.5917 23.4926 64.9864 23.7689 64.5601C24.9135 62.7892 26.0582 61.0209 27.2029 59.25C27.4791 58.8237 26.7976 58.4264 26.5213 58.8527Z" fill="#1B334B"/>
							<path d="M24.8085 57.3504C23.5139 58.1477 22.2192 58.945 20.922 59.7397C20.4904 60.0054 20.8851 60.6896 21.3193 60.4212C22.6139 59.6239 23.9086 58.8266 25.2058 58.0319C25.64 57.7661 25.2427 57.082 24.8085 57.3504Z" fill="#1B334B"/>
						</svg>
					</div>
					<h3><?php esc_html_e( 'Your Question', 'tourfic' ); ?></h3>
				</div>

				<form id="ask-question" action="" method="post">
					<div class="tf-aq-field">
						<label for="your-name"><?php esc_html_e( 'Name', 'tourfic' ); ?></label>
						<input type="text" name="your-name" placeholder="<?php esc_attr_e( 'Type full name', 'tourfic' ); ?>" required/>
					</div>
					<div class="tf-aq-field">
						<label for="your-email"><?php esc_html_e( 'Email', 'tourfic' ); ?></label>
						<input type="email" name="your-email" placeholder="<?php esc_attr_e( 'Type email', 'tourfic' ); ?>" required/>
					</div>
					<div class="tf-aq-field">
						<label for="your-question"><?php esc_html_e( 'Question', 'tourfic' ); ?></label>
						<textarea placeholder="<?php esc_attr_e( 'Type here...', 'tourfic' ); ?>" name="your-question" required></textarea>
					</div>
					<div class="tf-aq-field">
						<button type="reset" class="screen-reader-text"><?php esc_html_e( 'Reset', 'tourfic' ); ?></button>
						<button type="submit" form="ask-question" class="button tf_button tf-ask-question-submit"><?php esc_html_e( 'Submit', 'tourfic' ); ?></button>
						<input type="hidden" name="post_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
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

	if ( ! check_ajax_referer( 'ask_question_nonce' ) ) {
		$response['status'] = 'error';
		$response['msg']    = esc_html__( 'Security error! Reload the page and try again.', 'tourfic' );
		echo wp_json_encode( $response );
		wp_die();
	}

	$name     = isset( $_POST['your-name'] ) ? sanitize_text_field( $_POST['your-name'] ) : null;
	$email    = isset( $_POST['your-email'] ) ? sanitize_email( $_POST['your-email'] ) : null;
	$question = isset( $_POST['your-question'] ) ? sanitize_text_field( $_POST['your-question'] ) : null;

	$post_id    = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : null;
	$post_title = get_the_title( $post_id );

	//post author mail
	$author_id   = get_post_field( 'post_author', $post_id );
	$author_mail = get_the_author_meta( 'user_email', $author_id );

	// Post Author Data
	$author_data = get_userdata( $author_id );

	// Enquiry Store on Database
	$tf_post_author_id = get_post_field( 'post_author', $post_id );
	$tf_user_meta      = get_userdata( $tf_post_author_id );
	$tf_user_roles     = $tf_user_meta->roles;

	/**
	 * Enquiry Pabbly Integration
	 * @author Jahid
	 */
	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		do_action( 'enquiry_pabbly_form_trigger', $post_id, $name, $email, $question );
		do_action( 'enquiry_zapier_form_trigger', $post_id, $name, $email, $question );
	}

	if ( "tf_hotel" == get_post_type( $post_id ) ) {
		$send_email_to[] = ! empty( Helper::tfopt( 'h-enquiry-email' ) ) ? sanitize_email( Helper::tfopt( 'h-enquiry-email' ) ) : sanitize_email( get_option( 'admin_email' ) );
	} elseif ( "tf_apartment" == get_post_type( $post_id ) ) {
		$send_email_to[] = ! empty( $author_mail ) ? sanitize_email( $author_mail ) : sanitize_email( get_option( 'admin_email' ) );
	} else {
		$send_email_to[] = ! empty( Helper::tfopt( 't-enquiry-email' ) ) ? sanitize_email( Helper::tfopt( 't-enquiry-email' ) ) : sanitize_email( get_option( 'admin_email' ) );
	}

	$tf_vendor_email_enable_setting = !empty(Helper::tfopt( 'email_template_settings')['enable_vendor_enquiry_email']) ? Helper::tfopt( 'email_template_settings')['enable_vendor_enquiry_email'] : 0;


	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( $tf_vendor_email_enable_setting == 1)) {
		if ( in_array( "tf_vendor", $tf_user_roles ) ) {
			if ( "tf_hotel" == get_post_type( $post_id ) ) {
				$send_email_to[] = !empty($author_mail) ? $author_mail : '';
			} elseif ( "tf_tours" == get_post_type( $post_id ) ) {
				$send_email_to[] = !empty($author_mail) ? $author_mail : '';
			}
		}
	}

	$subject     = esc_html__( 'Someone asked question on: ', 'tourfic' ) . esc_html( $post_title );
	$message     = "{$question}";
	$headers[]   = 'Reply-To: ' . $name . ' <' . $email . '>';
	$attachments = array();


	if ( wp_mail( $send_email_to, $subject, $message, $headers, $attachments ) ) {
		$response['status'] = 'sent';
		$response['msg']    = esc_html__( 'Your question has been sent!', 'tourfic' );

		// Data Store to the DB
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}tf_enquiry_data
			( post_id, post_type, uname, uemail, udescription, author_id, author_roles, created_at )
			VALUES ( %d, %s, %s, %s, %s, %d, %s, %s )",
				array(
					sanitize_key( $post_id ),
					get_post_type( $post_id ),
					$name,
					$email,
					$question,
					sanitize_key( $tf_post_author_id ),
					$tf_user_roles[0],
					gmdate( 'Y-m-d H:i:s' )
				)
			)
		);
	} else {
		$response['status'] = 'error';
		$response['msg']    = esc_html__( 'Message sent failed!', 'tourfic' );
	}

	echo wp_json_encode( $response );

	die();
}

add_action( 'wp_ajax_tf_ask_question', 'tourfic_ask_question_ajax' );
add_action( 'wp_ajax_nopriv_tf_ask_question', 'tourfic_ask_question_ajax' );
/**
 * Dropdown Multiple Support
 */
add_filter( 'wp_dropdown_cats', 'tourfic_wp_dropdown_cats_multiple', 10, 2 );
function tourfic_wp_dropdown_cats_multiple( $output, $r ) {
	if ( isset( $r['multiple'] ) && $r['multiple'] ) {
		$output = preg_replace( '/^<select/i', '<select multiple', $output );
		$output = str_replace( "name='{$r['name']}'", "name='{$r['name']}[]'", $output );
		//if( is_array($r['selected']) ):
		foreach ( array_map( 'trim', explode( ",", $r['selected'] ) ) as $value ) {
			$output = str_replace( "value=\"{$value}\"", "value=\"{$value}\" selected", $output );
		}
		//endif;
	}

	return $output;
}

/**
 * Filter the excerpt "read more" string.
 *
 * @param string $more "Read more" excerpt string.
 *
 * @return string (Maybe) modified "read more" excerpt string.
 */
function tf_tours_excerpt_more( $more ) {

	if ( 'tf_tours' === get_post_type() ) {
		return '...';
	}

}

add_filter( 'excerpt_more', 'tf_tours_excerpt_more' );

/**
 * Filter the except length to 30 words.
 *
 * @param int $length Excerpt length.
 *
 * @return int (Maybe) modified excerpt length.
 */
function tf_custom_excerpt_length( $length ) {

	if ( 'tf_tours' === get_post_type() ) {
		return 30;
	}

}

/**
 * Pagination for the search page
 * @since 2.9.0
 * @author Abu Hena
 */
function tourfic_posts_navigation( $wp_query = '' ) {
	if ( empty( $wp_query ) ) {
		global $wp_query;
	}
	$max_num_pages = $wp_query->max_num_pages;
	$paged         = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
	if($max_num_pages>1){
		echo "<div id='tf_posts_navigation_bar'>";
		echo wp_kses_post( 
			paginate_links( array(
				'current'   => $paged,
				'total'     => $max_num_pages,
				'mid_size'  => 2,
				'prev_next' => true,
			) )
		);
		echo "</div>";
	}

}

/**
 * Flatpickr locale
 */
if ( ! function_exists( 'tf_flatpickr_locale' ) ) {
	function tf_flatpickr_locale($placement = 0) {

		$flatpickr_locale = ! empty( get_locale() ) ? get_locale() : 'en_US';
		$allowed_locale   = array( 'ar', 'bn_BD', 'de_DE', 'es_ES', 'fr_FR', 'hi_IN', 'it_IT', 'nl_NL', 'ru_RU', 'zh_CN' );
		$tf_first_day_of_week = !empty(Helper::tfopt("tf-week-day-flatpickr")) ? Helper::tfopt("tf-week-day-flatpickr") : 0;

		if ( in_array( $flatpickr_locale, $allowed_locale ) ) {

			switch ( $flatpickr_locale ) {
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
		}else {
			$flatpickr_locale = 'default';
		}

		if(!empty($placement) && !empty($flatpickr_locale) && $placement == "root") {

			echo esc_html( <<<EOD
				window.flatpickr.l10ns.$flatpickr_locale.firstDayOfWeek = $tf_first_day_of_week;
			EOD );

		} else {
			echo 'locale: "' . esc_html( $flatpickr_locale ) . '",';
		}
	}
}


/**
 * Flatten a multi-dimensional array into a single level.
 *
 * @param iterable $array
 * @param int $depth
 *
 * @return array
 * @author devkabir
 *
 */
if ( ! function_exists( 'tf_array_flatten' ) ) {
	function tf_array_flatten( $array, $depth = INF ) {

		$result = [];

		foreach ( $array as $item ) {
			if ( ! is_array( $item ) ) {
				$result[] = $item;
			} else {
				$values = $depth === 1
					? array_values( $item )
					: tf_array_flatten( $item, $depth - 1 );

				foreach ( $values as $value ) {
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
 * @param array $room collection of room data
 * @param float|integer $price calculated price
 * @param float|integer $deposit_amount calculated deposit amount
 * @param boolean $has_deposit is deposit allowed for this room?
 *
 *
 * @author Dev Kabir <dev.kabir01@gmail.com>
 */
if ( ! function_exists( 'tf_get_deposit_amount' ) ) {
	function tf_get_deposit_amount( $room, $price, &$deposit_amount, &$has_deposit, $discount = 0 ) {
		$deposit_amount = null;
		if($discount > 0) {
			$price = $discount;
		}
		$has_deposit    = ! empty( $room['allow_deposit'] ) && $room['allow_deposit'] == true;
		if ( $has_deposit == true ) {
			if ( $room['deposit_type'] == 'percent' ) {
				$deposit_amount = $price * ( intval( $room['deposit_amount'] ) / 100 );
			} else {
				$deposit_amount = $room['deposit_amount'];
			}
		}
	}
};

/*
 * User extra fields
 * @author Foysal
 */
if ( ! function_exists( 'tf_extra_user_profile_fields' ) ) {
	function tf_extra_user_profile_fields( $user ) { ?>
        <h3><?php esc_html_e( 'Tourfic Extra profile information', 'tourfic' ); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="language"><?php esc_html_e( 'Language', 'tourfic' ); ?></label></th>
                <td>
                    <input type="text" name="language" id="language"
                           value="<?php echo esc_attr( get_the_author_meta( 'language', $user->ID ) ); ?>"
                           class="regular-text"/><br/>
                    <span class="description"><?php esc_html_e( "Please enter your languages. Example: Bangla, English, Hindi" ); ?></span>
                </td>
            </tr>
        </table>
	<?php }

	add_action( 'show_user_profile', 'tf_extra_user_profile_fields' );
	add_action( 'edit_user_profile', 'tf_extra_user_profile_fields' );
}

/*
 * Save user extra fields
 * @author Foysal
 */
if ( ! function_exists( 'tf_save_extra_user_profile_fields' ) ) {
	function tf_save_extra_user_profile_fields( $user_id ) {
		if ( !isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'update-user_' . $user_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		update_user_meta( $user_id, 'language', $_POST['language'] );
	}

	add_action( 'personal_options_update', 'tf_save_extra_user_profile_fields' );
	add_action( 'edit_user_profile_update', 'tf_save_extra_user_profile_fields' );
}

// Admin Side Menu Order change

add_action( 'admin_menu', 'tourfic_admin_menu_seperator');
add_filter( 'menu_order', 'tourfic_admin_menu_order_change');
add_filter( 'custom_menu_order', '__return_true');

function tourfic_admin_menu_seperator () {

	global $menu;

    $menu[] = array( '', 'read', 'separator-tourfic', '', 'wp-menu-separator tourfic' );
    $menu[] = array( '', 'read', 'separator-tourfic2', '', 'wp-menu-separator tourfic' );
} 

function tourfic_admin_menu_order_change ( $menu_order ) {
	
	if(!empty($menu_order) && $menu_order != null) {
		$tourfic_menu_order = array();

		$tourfic_separator = array_search( 'separator-tourfic', $menu_order, true );
		$tourfic_separator2 = array_search( 'separator-tourfic2', $menu_order, true );
		$tourfic_tours = array_search( 'edit.php?post_type=tf_tours', $menu_order, true );
		$tourfic_hotel = array_search( 'edit.php?post_type=tf_hotel', $menu_order, true );
		$tourfic_apt = array_search( 'edit.php?post_type=tf_apartment', $menu_order, true );
		$tourfic_emails = array_search( 'edit.php?post_type=tf_email_templates', $menu_order, true );
		$tourfic_vendor = array_search( 'tf-multi-vendor', $menu_order, true );

		// // remove previous orders
		unset( $menu_order[$tourfic_separator] );
		unset( $menu_order[$tourfic_separator2] );

		if(!empty($tourfic_apt)) {
			unset( $menu_order[$tourfic_apt] );
		}

		if(!empty($tourfic_tours)) {
			unset( $menu_order[$tourfic_tours] );
		}

		if(!empty($tourfic_hotel)) {
			unset( $menu_order[$tourfic_hotel] );
		}
		
		if(!empty($tourfic_vendor)) {
			unset( $menu_order[$tourfic_vendor] );
		}

		if ( !empty( $tourfic_emails ) ) {
			unset( $menu_order[$tourfic_emails] );
		}

		foreach ( $menu_order as $index => $item ) {

			if ( 'tf_settings' === $item ) {
				$tourfic_menu_order[] = 'separator-tourfic';
				$tourfic_menu_order[] = $item;
				$tourfic_menu_order[] = 'edit.php?post_type=tf_tours';
				$tourfic_menu_order[] = 'edit.php?post_type=tf_hotel';
				$tourfic_menu_order[] = 'edit.php?post_type=tf_apartment';
				$tourfic_menu_order[] = 'tf-multi-vendor';
				$tourfic_menu_order[] = 'edit.php?post_type=tf_email_templates';
				$tourfic_menu_order[] = 'separator-tourfic2';

			} elseif ( !in_array( $item, array( 'separator-tourfic' ), true ) ) {
				$tourfic_menu_order[] = $item;
			}
		}

		return $tourfic_menu_order;

	} else {

		return;
	}
}
