<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Search form action url
 */
function tf_booking_search_action() {

	// get data from global settings else default
	$search_result_action = ! empty( tfopt( 'search-result-page' ) ) ? get_permalink( tfopt( 'search-result-page' ) ) : home_url( '/search-result/' );

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
		$submenu['edit.php?post_type=tf_hotel'][]     = array( sprintf( '<span class="tf-go-docs" style=color:#ffba00;">%s</span>', __( 'Go to Documentation', 'tourfic' ) ), 'edit_tf_hotels', $tfhoteldocumentation );
		$submenu['edit.php?post_type=tf_apartment'][] = array( sprintf( '<span class="tf-go-docs" style=color:#ffba00;">%s</span>', __( 'Go to Documentation', 'tourfic' ) ), 'edit_tf_apartments', $tfhoteldocumentation );
		$submenu['edit.php?post_type=tf_tours'][]     = array( sprintf( '<span class="tf-go-docs" style=color:#ffba00;">%s</span>', __( 'Go to Documentation', 'tourfic' ) ), 'edit_tf_tourss', $tftourdocumentation );
	}
}

/**
 * Black Friday Deals 2023
 */
if(!function_exists('tf_black_friday_2023_admin_notice') && !is_plugin_active( 'tourfic-pro/tourfic-pro.php' )){
    function tf_black_friday_2023_admin_notice(){
        $deal_link =sanitize_url('https://themefic.com/deals/');
        $get_current_screen = get_current_screen();  
        if(!isset($_COOKIE['tf_dismiss_admin_notice']) && $get_current_screen->base == 'dashboard'){ 
            ?>
            <style> 
                .tf_black_friday_20222_admin_notice a:focus {
                    box-shadow: none;
                } 
                .tf_black_friday_20222_admin_notice {
                    padding: 7px;
                    position: relative;
                    z-index: 10;
					max-width: 825px;
                } 
                .tf_black_friday_20222_admin_notice button:before {
                    color: #fff !important;
                }
                .tf_black_friday_20222_admin_notice button:hover::before {
                    color: #d63638 !important;
                }
            </style>
            <div class="notice notice-success tf_black_friday_20222_admin_notice"> 
                <a href="<?php echo $deal_link; ?>" target="_blank" >
					<img style="width: 100%; height: auto;" src="<?php echo TOURFIC_PLUGIN_URL ?>assets/admin/images/BLACK_FRIDAY_BACKGROUND_GRUNGE.png" alt="">
                </a> 
                <button type="button" class="notice-dismiss tf_black_friday_notice_dismiss"><span class="screen-reader-text"><?php echo __('Dismiss this notice.', 'ultimate-addons-cf7' ) ?></span></button>
            </div>
            <script>
                jQuery(document).ready(function($) {
                    $(document).on('click', '.tf_black_friday_notice_dismiss', function( event ) {
                        jQuery('.tf_black_friday_20222_admin_notice').css('display', 'none')
                        data = {
                            action : 'tf_black_friday_notice_dismiss_callback',
                        };
                        $.ajax({
                            url: ajaxurl,
                            type: 'post',
                            data: data,
                            success: function (data) { ;
                            },
                            error: function (data) { 
                            }
                        });
                    });
                });
            </script>
        
        <?php 
        }
    }
    if (strtotime('2023-12-01') > time()) {
        add_action( 'admin_notices', 'tf_black_friday_2023_admin_notice' );  
    }   
}
if(!function_exists('tf_black_friday_notice_dismiss_callback')){
    function tf_black_friday_notice_dismiss_callback() { 
        $cookie_name = "tf_dismiss_admin_notice";
        $cookie_value = "1"; 
        setcookie($cookie_name, $cookie_value, strtotime('2023-12-01'), "/"); 
        wp_die();
    }
    add_action( 'wp_ajax_tf_black_friday_notice_dismiss_callback', 'tf_black_friday_notice_dismiss_callback' );
}

if ( ! function_exists( 'tf_black_friday_2023_hotel_tour_docs' ) ) {
	function tf_black_friday_2023_hotel_tour_docs() {

		if ( ! isset( $_COOKIE['tf_hotel_friday_sidbar_notice'] ) ) {
			add_meta_box( 'tfhotel_black_friday_docs', __( ' ', 'tourfic' ), 'tf_black_friday_2023_callback_hotel', 'tf_hotel', 'side', 'high' );
		}
		if ( ! isset( $_COOKIE['tf_tour_friday_sidbar_notice'] ) ) {
			add_meta_box( 'tftour_black_friday_docs', __( ' ', 'tourfic' ), 'tf_black_friday_2023_callback_tour', 'tf_tours', 'side', 'high' );
		}
		if ( ! isset( $_COOKIE['tf_apartment_friday_sidbar_notice'] ) ) {
			add_meta_box( 'tfapartment_black_friday_docs', __( ' ', 'tourfic' ), 'tf_black_friday_2023_callback_apartment', 'tf_apartment', 'side', 'high' );
		}
	}

	if ( strtotime( '2023-12-01' ) > time() && !is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) ) {
		add_action( 'add_meta_boxes', 'tf_black_friday_2023_hotel_tour_docs' );
	}
	function tf_black_friday_2023_callback_hotel() {
		$deal_link = sanitize_url( 'https://themefic.com/deals' );
		?>
        <style>
			#tfhotel_black_friday_docs{
				border: 0px solid;
				box-shadow: none;
				background: transparent;
			}
            .back_friday_2023_preview a:focus {
                box-shadow: none;
            }

            .back_friday_2023_preview a {
                display: inline-block;
            }

            #tfhotel_black_friday_docs .inside {
                padding: 0;
                margin-top: 0;
            }

            #tfhotel_black_friday_docs .postbox-header {
                display: none;
                visibility: hidden;
            }
        </style>
        <div class="back_friday_2023_preview" style="text-align: center; overflow: hidden;">
			<button type="button" class="notice-dismiss tf_hotel_friday_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            <a href="<?php echo $deal_link; ?>" target="_blank">
                <img style="width: 100%;" src="<?php echo TOURFIC_PLUGIN_URL ?>assets/admin/images/BLACK_FRIDAY_SIDEBAR-BANNER.png" alt="">
            </a>
			<script>
            jQuery(document).ready(function($) {
                $(document).on('click', '.tf_hotel_friday_notice_dismiss', function( event ) { 
                    jQuery('.back_friday_2023_preview').css('display', 'none')
                    var cookieName = "tf_hotel_friday_sidbar_notice";
					var cookieValue = "1";

					// Create a date object for the expiration date
					var expirationDate = new Date();
					expirationDate.setTime(expirationDate.getTime() + (5 * 24 * 60 * 60 * 1000)); // 5 days in milliseconds

					// Construct the cookie string
					var cookieString = cookieName + "=" + cookieValue + ";expires=" + expirationDate.toUTCString() + ";path=/";

					// Set the cookie
					document.cookie = cookieString;
                });
            });
        	</script>
        </div>
		<?php
	}

	function tf_black_friday_2023_callback_tour() {
		$deal_links = sanitize_url( 'https://themefic.com/deals' );
		?>
        <style>
			#tftour_black_friday_docs{
				border: 0px solid;
				box-shadow: none;
				background: transparent;
			}
            .back_friday_2023_preview a:focus {
                box-shadow: none;
            }

            .back_friday_2023_preview a {
                display: inline-block;
            }

            #tftour_black_friday_docs .inside {
                padding: 0;
                margin-top: 0;
            }

            #tftour_black_friday_docs .postbox-header {
                display: none;
                visibility: hidden;
            }
        </style>
        <div class="back_friday_2023_preview" style="text-align: center; overflow: hidden;">
			<button type="button" class="notice-dismiss tf_tour_friday_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            <a href="<?php echo $deal_links; ?>" target="_blank">
				<img style="width: 100%;" src="<?php echo TOURFIC_PLUGIN_URL ?>assets/admin/images/BLACK_FRIDAY_SIDEBAR-BANNER.png" alt="">
            </a>
        </div>

		<script>
		jQuery(document).ready(function($) {
			$(document).on('click', '.tf_tour_friday_notice_dismiss', function( event ) { 
				jQuery('.back_friday_2023_preview').css('display', 'none')
				var cookieName = "tf_tour_friday_sidbar_notice";
				var cookieValue = "1";

				// Create a date object for the expiration date
				var expirationDate = new Date(); 
				expirationDate.setTime(expirationDate.getTime() + (5 * 24 * 60 * 60 * 1000)); // 5 days in milliseconds

				// Construct the cookie string
				var cookieString = cookieName + "=" + cookieValue + ";expires=" + expirationDate.toUTCString() + ";path=/";

				// Set the cookie
				document.cookie = cookieString;
			});
		});
		</script>
		<?php
	}
	function tf_black_friday_2023_callback_apartment() {
		$deal_links = sanitize_url( 'https://themefic.com/deals' );
		?>
        <style>
			#tfapartment_black_friday_docs{
				border: 0px solid;
				box-shadow: none;
				background: transparent;
			}
            .back_friday_2023_preview a:focus {
                box-shadow: none;
            }

            .back_friday_2023_preview a {
                display: inline-block;
            }

            #tfapartment_black_friday_docs .inside {
                padding: 0;
                margin-top: 0;
            }

            #tfapartment_black_friday_docs .postbox-header {
                display: none;
                visibility: hidden;
            }
        </style>
        <div class="back_friday_2023_preview" style="text-align: center; overflow: hidden;">
			<button type="button" class="notice-dismiss tf_apartment_friday_notice_dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
            <a href="<?php echo $deal_links; ?>" target="_blank">
				<img style="width: 100%;" src="<?php echo TOURFIC_PLUGIN_URL ?>assets/admin/images/BLACK_FRIDAY_SIDEBAR-BANNER.png" alt="">
            </a>
        </div>

		<script>
		jQuery(document).ready(function($) {
			$(document).on('click', '.tf_apartment_friday_notice_dismiss', function( event ) { 
				jQuery('.back_friday_2023_preview').css('display', 'none')
				var cookieName = "tf_apartment_friday_sidbar_notice";
				var cookieValue = "1";

				// Create a date object for the expiration date
				var expirationDate = new Date();
				expirationDate.setTime(expirationDate.getTime() + (5 * 24 * 60 * 60 * 1000)); // 5 days in milliseconds

				// Construct the cookie string
				var cookieString = cookieName + "=" + cookieValue + ";expires=" + expirationDate.toUTCString() + ";path=/";

				// Set the cookie
				document.cookie = cookieString;
			});
		});
		</script>
		<?php
	}
}


/**
 * Go to Documentaion Metabox
 */


function tf_hotel_tour_docs() {
	global $current_user;
	$tf_current_role = $current_user->roles[0];
	if ( $tf_current_role == "administrator" ) {
		add_meta_box( 'tfhotel_docs', __( 'Tourfic Documentation', 'tourfic' ), 'tf_hotel_docs_callback', 'tf_hotel', 'side', 'high' );
		add_meta_box( 'tfapartment_docs', __( 'Tourfic Documantation', 'tourfic' ), 'tf_apartment_docs_callback', 'tf_apartment', 'side', 'high' );
		add_meta_box( 'tftour_docs', __( 'Tourfic Documentation', 'tourfic' ), 'tf_tour_docs_callback', 'tf_tours', 'side', 'high' );

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
        <a href="<?php echo $tfhoteldocumentation; ?>" target="_blank">
			<img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . 'images/banner-cta.png'); ?>" alt="<?php echo __( 'Go to Documentation', 'tourfic' ); ?>">
		</a>
    </div>
	<?php
}

function tf_apartment_docs_callback() {
	global $wp_meta_boxes;
	$tf_apartment_documentation = sanitize_url( 'https://themefic.com/docs/tourfic/add-new-apartment/locations-types-and-featured-image/' );
	?>
    <div class="tf_docs_preview">
        <a href="<?php echo $tf_apartment_documentation; ?>" target="_blank">
			<img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . 'images/banner-cta.png'); ?>" alt="<?php echo __( 'Go to Documentation', 'tourfic' ); ?>">
		</a>
    </div>
	<?php
}

function tf_tour_docs_callback() {
	$tf_tour_documentation = sanitize_url( 'https://themefic.com/docs/tourfic/tours/tourfic-hotel-general-settings/' );
	?>
    <div class="tf_docs_preview">
        <a href="<?php echo $tf_tour_documentation; ?>" target="_blank">
			<img src="<?php echo esc_url(TF_ASSETS_ADMIN_URL . 'images/banner-cta.png'); ?>" alt="<?php echo __( 'Go to Documentation', 'tourfic' ); ?>">
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

	$search_result_page = tfopt( 'search-result-page' );

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
            <span class="close-aq">&times;</span>
            <div class="tf-aq-inner">
                <h4><?php _e( 'Submit your question', 'tourfic' ); ?></h4>
                <form id="ask-question" action="" method="post">
                    <div class="tf-aq-field">
                        <input type="text" name="your-name" placeholder="<?php esc_attr_e( 'Your Name', 'tourfic' ); ?>" required/>
                    </div>
                    <div class="tf-aq-field">
                        <input type="email" name="your-email" placeholder="<?php esc_attr_e( 'Your email', 'tourfic' ); ?>" required/>
                    </div>
                    <div class="tf-aq-field">
                        <textarea placeholder="<?php esc_attr_e( 'Your Question', 'tourfic' ); ?>" name="your-question" required></textarea>
                    </div>
                    <div class="tf-aq-field">
                        <button type="reset" class="screen-reader-text"><?php esc_html_e( 'Reset', 'tourfic' ); ?></button>
                        <button type="submit" form="ask-question" class="button tf_button btn-styled"><?php esc_html_e( 'Submit', 'tourfic' ); ?></button>
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
		$response['msg']    = __( 'Security error! Reload the page and try again.', 'tourfic' );
		echo json_encode( $response );
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

	// Enquiry Store on Database
	$tf_post_author_id = get_post_field( 'post_author', $post_id );
	$tf_user_meta      = get_userdata( $tf_post_author_id );
	$tf_user_roles     = $tf_user_meta->roles;
	global $wpdb;
	$table_name = $wpdb->prefix . 'tf_enquiry_data';
	$wpdb->query(
		$wpdb->prepare(
			"INSERT INTO $table_name
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
				date( 'Y-m-d H:i:s' )
			)
		)
	);

	/**
	 * Enquiry Pabbly Integration
	 * @author Jahid
	 */
	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		do_action( 'enquiry_pabbly_form_trigger', $post_id, $name, $email, $question );
		do_action( 'enquiry_zapier_form_trigger', $post_id, $name, $email, $question );
	}

	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
		if ( "tf_hotel" == get_post_type( $post_id ) ) {
			$send_email_to = ! empty( tfopt( 'h-enquiry-email' ) ) ? sanitize_email( tfopt( 'h-enquiry-email' ) ) : sanitize_email( get_option( 'admin_email' ) );
		} elseif ( "tf_apartment" == get_post_type( $post_id ) ) {
			$send_email_to = ! empty( $author_mail ) ? sanitize_email( $author_mail ) : sanitize_email( get_option( 'admin_email' ) );
		} else {
			$send_email_to = ! empty( tfopt( 't-enquiry-email' ) ) ? sanitize_email( tfopt( 't-enquiry-email' ) ) : sanitize_email( get_option( 'admin_email' ) );
		}
	} else {
		if ( "tf_apartment" == get_post_type( $post_id ) ) {
			$send_email_to = ! empty( $author_mail ) ? sanitize_email( $author_mail ) : sanitize_email( get_option( 'admin_email' ) );
		} else {
			$send_email_to = sanitize_email( get_option( 'admin_email' ) );
		}
	}
	$subject     = sprintf( __( 'Someone asked question on: %s', 'tourfic' ), $post_title );
	$message     = "{$question}";
	$headers[]   = 'Reply-To: ' . $name . ' <' . $email . '>';
	$attachments = array();


	if ( wp_mail( $send_email_to, $subject, $message, $headers, $attachments ) ) {
		$response['status'] = 'sent';
		$response['msg']    = __( 'Your question has been sent!', 'tourfic' );
	} else {
		$response['status'] = 'error';
		$response['msg']    = __( 'Message sent failed!', 'tourfic' );
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
	echo "<div id='tf_posts_navigation_bar'>";
	echo paginate_links( array(
		'current'   => $paged,
		'total'     => $max_num_pages,
		'mid_size'  => 2,
		'prev_next' => true,
	) );
	echo "</div>";

}

/**
 * Flatpickr locale
 */
if ( ! function_exists( 'tf_flatpickr_locale' ) ) {
	function tf_flatpickr_locale() {

		$flatpickr_locale = ! empty( get_locale() ) ? get_locale() : 'en_US';
		$allowed_locale   = array( 'ar', 'bn_BD', 'de_DE', 'es_ES', 'fr_FR', 'hi_IN', 'it_IT', 'nl_NL', 'ru_RU', 'zh_CN' );

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

			echo 'locale: "' . $flatpickr_locale . '",';
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
        <h3><?php _e( 'Tourfic Extra profile information', 'tourfic' ); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="language"><?php _e( 'Language', 'tourfic' ); ?></label></th>
                <td>
                    <input type="text" name="language" id="language"
                           value="<?php echo esc_attr( get_the_author_meta( 'language', $user->ID ) ); ?>"
                           class="regular-text"/><br/>
                    <span class="description"><?php _e( "Please enter your languages. Example: Bangla, English, Hindi" ); ?></span>
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
		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
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

