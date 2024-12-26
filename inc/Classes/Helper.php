<?php

namespace Tourfic\Classes;
defined( 'ABSPATH' ) || exit;

use \Tourfic\Admin\Emails\TF_Handle_Emails;
use \Tourfic\Classes\Car_Rental\Availability;
use \Tourfic\Classes\Hotel\Hotel;

class Helper {
	use \Tourfic\Traits\Singleton;
	use \Tourfic\Traits\TF_Fonts;
	use \Tourfic\Traits\Action_Helper;

	public function __construct() {
		add_filter( 'body_class', array( $this, 'tf_templates_body_class' ) );
		add_action( 'admin_footer', array( $this, 'tf_admin_footer' ) );

		add_filter( 'rest_prepare_taxonomy', array( $this, 'tf_remove_metabox_gutenburg' ), 10, 3 );
		add_filter( 'rest_user_query', array( $this, 'tf_gutenberg_author_dropdown_roles' ), 10, 2 );
		add_action( "wp_ajax_tf_shortcode_type_to_location", array( $this, 'tf_shortcode_type_to_location_callback' ) );
		add_action( 'wp_ajax_tf_affiliate_active', array( $this, 'tf_affiliate_active_callback' ) );
		add_action( 'wp_ajax_tf_affiliate_install', array( $this, 'tf_affiliate_install_callback' ) );
		add_action( 'admin_init', array( $this, 'tf_update_email_template_default_content' ) );
		add_action( 'wp_ajax_tf_checkout_cart_item_remove', array( $this, 'tf_checkout_cart_item_remove' ) );
		add_action( 'wp_ajax_nopriv_tf_checkout_cart_item_remove', array( $this, 'tf_checkout_cart_item_remove' ) );
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'tf_remove_icon_add_to_order_item' ), 10, 3 );
		add_action( 'wp_ajax_tf_month_reports', array( $this, 'tf_month_chart_filter_callback' ) );

		// tax filter
		add_action( 'wp_ajax_nopriv_tf_trigger_filter', array( $this, 'tf_search_result_ajax_sidebar' ) );
		add_action( 'wp_ajax_tf_trigger_filter', array( $this, 'tf_search_result_ajax_sidebar' ) );


		add_action( 'admin_init', array( $this, 'tf_admin_role_caps' ), 999 );
        if ( Helper::tf_is_woo_active() ) {
		    add_action( 'init', array( $this, 'tf_customer_role_caps' ), 999 );
        }
		add_filter( 'template_include', array( $this, 'taxonomy_template' ) );
		add_filter( 'comments_template', array( $this, 'load_comment_template' ) );
		add_filter( 'template_include', array( $this, 'tourfic_archive_page_template' ) );
		add_filter( 'single_template', array( $this, 'tf_single_page_template' ) );
		add_filter( 'after_setup_theme', array( $this, 'tf_image_sizes' ) );
        add_filter( 'tf_booking_search_action', array( $this, 'tourfic_booking_set_search_result') );
        add_filter( 'wp_dropdown_cats', array( $this, 'tourfic_wp_dropdown_cats_multiple' ), 10, 2 );
        add_filter( 'excerpt_more', array( $this, 'tf_tours_excerpt_more' ) );

		is_admin() ? add_filter( 'plugin_action_links_' . 'tourfic/tourfic.php', array( $this, 'tf_plugin_action_links' ) ) : '';
		is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) && function_exists( 'is_tf_pro' ) && ! is_tf_pro() ? add_filter( 'plugin_action_links_' . 'tourfic-pro/tourfic-pro.php', array(
			$this,
			'tf_pro_plugin_licence_action_links'
		) ) : '';
		add_action( 'admin_menu', array( $this, 'tf_documentation_page_integration' ), 999 );
		add_action( 'add_meta_boxes', array( $this, 'tf_hotel_tour_docs' ) );
		add_action( 'admin_menu', array( $this, 'tf_documentation_page_integration' ), 999 );
		add_action( 'add_meta_boxes', array( $this, 'tf_hotel_tour_docs' ) );
		add_action( 'show_user_profile', array( $this, 'tf_extra_user_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'tf_extra_user_profile_fields' ) );
		add_action( 'personal_options_update', array( $this, 'tf_save_extra_user_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'tf_save_extra_user_profile_fields' ) );

		add_action( 'admin_menu', array( $this, 'tourfic_admin_menu_seperator' ) );
		add_filter( 'menu_order', array( $this, 'tourfic_admin_menu_order_change' ) );
		add_filter( 'custom_menu_order', '__return_true' );

		// Add dashboard link to admin menu bar
		add_action( 'admin_bar_menu', array( $this, 'tf_admin_bar_dashboard_link' ), 999 );

		// redirect non admin user
		// add_action( 'admin_init', array( $this, 'redirect_non_admin_users' ), 9 );
		add_action( 'admin_init', array( $this, 'tourfic_check_instantio_active' ), 9 );
        add_action( 'tf_before_container', array( $this, 'tourfic_notice_wrapper' ), 10 );
        // add_action('wp_head', array( $this, 'tf_no_idex_search_page'), 1);
        // add_filter( 'wp_robots', array( $this, 'tf_no_idex_search_page') );

        add_filter('upload_mimes', array($this, 'tf_upload_mimes_support'));
        add_filter( 'wp_check_filetype_and_ext', array($this, 'tf_filetype_and_ext_check_support'), 10, 5 );
	}

	static function tfopt( $option = '', $default = null ) {
		$options = get_option( 'tf_settings' );

		return ( isset( $options[ $option ] ) ) ? $options[ $option ] : $default;
	}

	static function tf_data_types( $var ) {
		if ( ! empty( $var ) && gettype( $var ) == "string" ) {
			$tf_serialize_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $var );

			return unserialize( $tf_serialize_date );
		} else {
			return $var;
		}
	}

	static function get_terms_dropdown( $taxonomy, $args = array() ) {
		$defaults         = array(
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		);
		$args             = wp_parse_args( $args, $defaults );
		$args['taxonomy'] = $taxonomy;

		$terms = get_terms( $args );

		$term_dropdown = array();
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$term_dropdown[ $term->slug ] = $term->name;
			}
		}

		return $term_dropdown;
	}

	static function tf_is_gutenberg_active() {
		if ( function_exists( 'is_gutenberg_page' ) ) {
			return true;
		}

		$current_screen = get_current_screen();

		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return true;
		}

		return false;
	}

	static function tf_dashboard_header() {
		?>
        <!-- dashboard-top-section -->
        <div class="tf-setting-top-bar">
            <div class="version">
                <img src="<?php echo esc_url( TF_ASSETS_APP_URL ); ?>images/tourfic-logo.webp" alt="logo">
                <span>v<?php echo esc_html( TF_VERSION ); ?></span>
            </div>
            <div class="other-document">
                <svg width="26" height="25" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg"
                     style="color: #003c79;">
                    <path d="M19.2106 0H6.57897C2.7895 0 0.263184 2.52632 0.263184 6.31579V13.8947C0.263184 17.6842 2.7895 20.2105 6.57897 20.2105V22.9011C6.57897 23.9116 7.70318 24.5179 8.53687 23.9495L14.1579 20.2105H19.2106C23 20.2105 25.5263 17.6842 25.5263 13.8947V6.31579C25.5263 2.52632 23 0 19.2106 0ZM12.8948 15.3726C12.3642 15.3726 11.9474 14.9432 11.9474 14.4253C11.9474 13.9074 12.3642 13.4779 12.8948 13.4779C13.4253 13.4779 13.8421 13.9074 13.8421 14.4253C13.8421 14.9432 13.4253 15.3726 12.8948 15.3726ZM14.4863 10.1305C13.9937 10.4589 13.8421 10.6737 13.8421 11.0274V11.2926C13.8421 11.8105 13.4127 12.24 12.8948 12.24C12.3769 12.24 11.9474 11.8105 11.9474 11.2926V11.0274C11.9474 9.56211 13.0211 8.84211 13.4253 8.56421C13.8927 8.24842 14.0442 8.03368 14.0442 7.70526C14.0442 7.07368 13.5263 6.55579 12.8948 6.55579C12.2632 6.55579 11.7453 7.07368 11.7453 7.70526C11.7453 8.22316 11.3158 8.65263 10.7979 8.65263C10.28 8.65263 9.85055 8.22316 9.85055 7.70526C9.85055 6.02526 11.2148 4.66105 12.8948 4.66105C14.5748 4.66105 15.939 6.02526 15.939 7.70526C15.939 9.14526 14.8779 9.86526 14.4863 10.1305Z"
                          fill="#003c79"></path>
                </svg>

                <div class="dropdown">
                    <div class="list-item">
                        <a href="https://portal.themefic.com/support/" target="_blank">
                            <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.0482 4.37109H4.30125C4.06778 4.37109 3.84329 4.38008 3.62778 4.40704C1.21225 4.6137 0 6.04238 0 8.6751V12.2693C0 15.8634 1.43674 16.5733 4.30125 16.5733H4.66044C4.85799 16.5733 5.1184 16.708 5.23514 16.8608L6.3127 18.2985C6.78862 18.9364 7.56087 18.9364 8.03679 18.2985L9.11435 16.8608C9.24904 16.6811 9.46456 16.5733 9.68905 16.5733H10.0482C12.6793 16.5733 14.107 15.3692 14.3136 12.9432C14.3405 12.7275 14.3495 12.5029 14.3495 12.2693V8.6751C14.3495 5.80876 12.9127 4.37109 10.0482 4.37109ZM4.04084 11.5594C3.53798 11.5594 3.14288 11.1551 3.14288 10.6609C3.14288 10.1667 3.54696 9.76233 4.04084 9.76233C4.53473 9.76233 4.93881 10.1667 4.93881 10.6609C4.93881 11.1551 4.53473 11.5594 4.04084 11.5594ZM7.17474 11.5594C6.67188 11.5594 6.27678 11.1551 6.27678 10.6609C6.27678 10.1667 6.68086 9.76233 7.17474 9.76233C7.66862 9.76233 8.07271 10.1667 8.07271 10.6609C8.07271 11.1551 7.6776 11.5594 7.17474 11.5594ZM10.3176 11.5594C9.81476 11.5594 9.41966 11.1551 9.41966 10.6609C9.41966 10.1667 9.82374 9.76233 10.3176 9.76233C10.8115 9.76233 11.2156 10.1667 11.2156 10.6609C11.2156 11.1551 10.8115 11.5594 10.3176 11.5594Z"
                                      fill="#003c79"></path>
                                <path d="M17.9423 5.08086V8.67502C17.9423 10.4721 17.3855 11.6941 16.272 12.368C16.0026 12.5298 15.6884 12.3141 15.6884 11.9996L15.6973 8.67502C15.6973 5.08086 13.641 3.0232 10.0491 3.0232L4.58048 3.03219C4.26619 3.03219 4.05067 2.7177 4.21231 2.44814C4.88578 1.33395 6.10702 0.776855 7.89398 0.776855H13.641C16.5055 0.776855 17.9423 2.21452 17.9423 5.08086Z"
                                      fill="#003c79"></path>
                            </svg>
                            <span><?php esc_html_e( "Need Help?", "tourfic" ); ?></span>
                        </a>
                        <a href="https://themefic.com/docs/tourfic/" target="_blank">
                            <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.1896 7.57803H13.5902C11.4586 7.57803 9.72274 5.84103 9.72274 3.70803V1.10703C9.72274 0.612031 9.318 0.207031 8.82332 0.207031H5.00977C2.23956 0.207031 0 2.00703 0 5.22003V13.194C0 16.407 2.23956 18.207 5.00977 18.207H12.0792C14.8494 18.207 17.089 16.407 17.089 13.194V8.47803C17.089 7.98303 16.6843 7.57803 16.1896 7.57803ZM8.09478 14.382H4.4971C4.12834 14.382 3.82254 14.076 3.82254 13.707C3.82254 13.338 4.12834 13.032 4.4971 13.032H8.09478C8.46355 13.032 8.76935 13.338 8.76935 13.707C8.76935 14.076 8.46355 14.382 8.09478 14.382ZM9.89363 10.782H4.4971C4.12834 10.782 3.82254 10.476 3.82254 10.107C3.82254 9.73803 4.12834 9.43203 4.4971 9.43203H9.89363C10.2624 9.43203 10.5682 9.73803 10.5682 10.107C10.5682 10.476 10.2624 10.782 9.89363 10.782Z"
                                      fill="#003c79"></path>
                            </svg>
                            <span><?php esc_html_e( "Documentation", "tourfic" ); ?></span>

                        </a>
                        <a href="https://portal.themefic.com/support/" target="_blank">
                            <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M13.5902 7.57803H16.1896C16.6843 7.57803 17.089 7.98303 17.089 8.47803V13.194C17.089 16.407 14.8494 18.207 12.0792 18.207H5.00977C2.23956 18.207 0 16.407 0 13.194V5.22003C0 2.00703 2.23956 0.207031 5.00977 0.207031H8.82332C9.318 0.207031 9.72274 0.612031 9.72274 1.10703V3.70803C9.72274 5.84103 11.4586 7.57803 13.5902 7.57803ZM11.9613 0.396012C11.5926 0.0270125 10.954 0.279013 10.954 0.792013V3.93301C10.954 5.24701 12.0693 6.33601 13.4274 6.33601C14.2818 6.34501 15.4689 6.34501 16.4852 6.34501H16.4854C16.998 6.34501 17.2679 5.74201 16.9081 5.38201C16.4894 4.96018 15.9637 4.42927 15.3988 3.85888L15.3932 3.85325L15.3913 3.85133L15.3905 3.8505L15.3902 3.85016C14.2096 2.65803 12.86 1.29526 11.9613 0.396012ZM3.0145 12.0732C3.0145 11.7456 3.28007 11.48 3.60768 11.48H5.32132V9.76639C5.32132 9.43879 5.58689 9.17321 5.9145 9.17321C6.2421 9.17321 6.50768 9.43879 6.50768 9.76639V11.48H8.22131C8.54892 11.48 8.8145 11.7456 8.8145 12.0732C8.8145 12.4008 8.54892 12.6664 8.22131 12.6664H6.50768V14.38C6.50768 14.7076 6.2421 14.9732 5.9145 14.9732C5.58689 14.9732 5.32132 14.7076 5.32132 14.38V12.6664H3.60768C3.28007 12.6664 3.0145 12.4008 3.0145 12.0732Z"
                                      fill="#003c79"></path>
                            </svg>
                            <span><?php esc_html_e( "Feature Request", "tourfic" ); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- dashboard-top-section -->
		<?php
	}

	function tf_remove_metabox_gutenburg( $response, $taxonomy, $request ) {

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';

		if ( $context === 'edit' && $taxonomy->meta_box_cb === false ) {

			$data_response = $response->get_data();

			$data_response['visibility']['show_ui'] = false;

			$response->set_data( $data_response );
		}

		return $response;
	}

	static function tf_custom_wp_kses_allow_tags() {
		// Allow all HTML tags and attributes
		$allowed_tags = wp_kses_allowed_html( 'post' );

		// Add form-related tags to the allowed tags
		$allowed_tags['form'] = array(
			'action'  => true,
			'method'  => true,
			'enctype' => true,
			'class'   => true,
			'id'      => true,
			'data-*'  => true,
		);

		$allowed_tags['input'] = array(
			'type'        => true,
			'name'        => true,
			'value'       => true,
			'placeholder' => true,
			'class'       => true,
			'id'          => true,
			'checked'     => true,
			'data-*'      => true,
		);

		$allowed_tags['select'] = array(
			'name'     => true,
			'class'    => true,
			'id'       => true,
			'data-*'   => true,
			'multiple' => true,
		);

		$allowed_tags['option'] = array(
			'value'  => true,
			'class'  => true,
			'id'     => true,
			'data-*' => true,
		);

		$allowed_tags['textarea'] = array(
			'name'   => true,
			'rows'   => true,
			'cols'   => true,
			'class'  => true,
			'id'     => true,
			'data-*' => true,
		);

		$allowed_tags['label'] = array(
			'for'    => true,
			'class'  => true,
			'id'     => true,
			'data-*' => true,
		);

		$allowed_tags['fieldset'] = array(
			'name'  => true,
			'class' => true,
			'id'    => true,
		);

		$allowed_tags['legend'] = array(
			'name'  => true,
			'class' => true,
			'id'    => true,
		);

		$allowed_tags['optgroup'] = array(
			'label' => true,
			'class' => true,
			'id'    => true,
		);

		$allowed_tags['script'] = array(
			'src'   => true,
			'type'  => true,
			'class' => true,
			'id'    => true,
			'async' => true,
			'defer' => true,
		);
		$allowed_tags['button'] = array(
			'class'    => true,
			'id'       => true,
			'disabled' => true,
			'data-*'   => true,

		);
		$allowed_tags['style']  = array(
			'class' => true,
			'id'    => true,
		);

		$allowed_tags['iframe'] = array(
			'class'           => true,
			'id'              => true,
			'allowfullscreen' => true,
			'frameborder'     => true,
			'src'             => true,
			'style'           => true,
			'width'           => true,
			'height'          => true,
			'title'           => true,
			'allow'           => true,
			'data-*'          => true,
		);

		$allowed_tags["svg"] = array(
			'class'           => true,
			'aria-hidden'     => true,
			'aria-labelledby' => true,
			'role'            => true,
			'xmlns'           => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true,
			'fill'            => true,
			'data-*'          => true,
		);

		$allowed_tags['g']        = array( 'fill' => true, "clip-path" => true );
		$allowed_tags['title']    = array( 'title' => true );
		$allowed_tags['rect']     = array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'fill' => true );
		$allowed_tags['path']     = array(
			'd'               => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			"stroke-linejoin" => true,
		);
		$allowed_tags['polygon']  = array(
			'points'       => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
		);
		$allowed_tags['circle']   = array(
			'cx'           => true,
			'cy'           => true,
			'r'            => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
		);
		$allowed_tags['line']     = array(
			'x1'           => true,
			'y1'           => true,
			'x2'           => true,
			'y2'           => true,
			'stroke'       => true,
			'stroke-width' => true,
		);
		$allowed_tags['text']     = array(
			'x'           => true,
			'y'           => true,
			'fill'        => true,
			'font-size'   => true,
			'font-family' => true,
			'text-anchor' => true,
		);
		$allowed_tags['defs']     = array(
			'd' => true
		);
		$allowed_tags['clipPath'] = array(
			'd' => true
		);
		$allowed_tags['code']     = true;

		return $allowed_tags;
	}

	static function tourfic_character_limit_callback( $str, $limit, $dots = true ) {
		if ( strlen( $str ) > $limit ) {
			if ( $dots == true ) {
				return substr( $str, 0, $limit ) . '...';
			} else {
				return substr( $str, 0, $limit );
			}
		} else {
			return $str;
		}
	}

	function get_current_url() {
		$protocol = is_ssl() ? 'https://' : 'http://';

		return ( $protocol ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	/**
	 * Hotel gallery video content initialize by this hook
	 * can be filtered the video url by "tf_hotel_gallery_video_url" Filter
	 * @since 2.9.7
	 * @author Abu Hena
	 */
	static function tf_hotel_gallery_video( $meta ) {

		//Hotel video section in the hero
		$url = ! empty( $meta['video'] ) ? $meta['video'] : '';
		if ( ! empty( $url ) ) {
			?>
            <div class="tf-hotel-video">
                <div class="tf-hero-btm-icon tf-hotel-video" data-fancybox="hotel-video" href="<?php echo esc_url( apply_filters( 'tf_hotel_gallery_video_url', $url ) ); ?>">
                    <i class="fab fa-youtube"></i>
                </div>
            </div>
			<?php
		}
	}

	/**
	 * Generate custom taxonomies select dropdown
	 * @author Abu Hena
	 * @since 2.9.4
	 */
	static function tf_terms_dropdown( $term, $attribute, $id, $class, $multiple = false ) {

		//get the terms
		$terms = get_terms( array(
			'taxonomy'   => $term,
			'hide_empty' => false,
		) );

		//define if select field would be multiple or not
		if ( $multiple == true ) {
			$multiple = 'multiple';
		} else {
			$multiple = "";
		}
		$select = '';
		//output the select field
		if ( ! empty( $terms ) && is_array( $terms ) ) {
			$select .= '<select data-placeholder=" Select from Dropdown" id="' . $id . '" data-term="' . $attribute . '" name="' . $term . '" class="tf-shortcode-select2 ' . $class . '" ' . $multiple . '>';
			$select .= '<option value="\'all\'">' . esc_html__( 'All', 'tourfic' ) . '</option>';
			foreach ( $terms as $term ) {
				$select .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
			}
			$select .= "</select>";
		} else {
			$select .= esc_html__( "Invalid taxonomy!!", 'tourfic' );
		}
		echo wp_kses( $select, Helper::tf_custom_wp_kses_allow_tags() );
	}

	/*
     * Search form tab type check
     * @author: Foysal
     * return: boolean
     */
	static function tf_is_search_form_tab_type( $type, $type_arr ) {
		if ( in_array( $type, $type_arr ) || in_array( 'all', $type_arr ) ) {
			return true;
		}

		return false;
	}

	/*
	 * Search form tab type check
	 * @author: Foysal
	 * return: boolean
	 */
	static function tf_is_search_form_single_tab( $type_arr ) {
		if ( count( $type_arr ) === 1 && $type_arr[0] !== 'all' ) {
			return true;
		}

		return false;
	}

	static function tourfic_template_settings() {
		$tf_plugin_installed = get_option( 'tourfic_template_installed' );
		if ( ! empty( $tf_plugin_installed ) ) {
			$template = 'design-1';
		} else {
			$template = 'default';
		}

		return $template;
	}

	/*
     * Retrive Orders Data
     *
     * @return void
     *
     * @since 2.9.26
     * @author Jahid
     */
	static function tourfic_order_table_data( $query ) {
		global $wpdb;
		$query_type          = $query['post_type'];
		$query_select        = $query['select'];
		$query_where         = $query['query'];
		$tf_tour_book_orders = $wpdb->get_results( $wpdb->prepare( "SELECT $query_select FROM {$wpdb->prefix}tf_order_data WHERE post_type = %s $query_where", $query_type ), ARRAY_A );

		return $tf_tour_book_orders;
	}

	static function tf_affiliate_callback() {
		if ( current_user_can( 'activate_plugins' ) ) {
			?>
            <div class="tf-field tf-field-notice" style="width:100%;">
                <div class="tf-fieldset" style="margin: 0px;">
                    <div class="tf-field-notice-inner tf-notice-info">
                        <div class="tf-field-notice-content has-content">
							<?php if ( ! is_plugin_active( 'tourfic-affiliate/tourfic-affiliate.php' ) && ! file_exists( WP_PLUGIN_DIR . '/tourfic-affiliate/tourfic-affiliate.php' ) ) : ?>
                                <span style="margin-right: 15px;"><?php echo esc_html__( "Tourfic affiliate addon is not installed. Please install and activate it to use this feature.", "tourfic" ); ?> </span>
                                <a target="_blank" href="https://portal.themefic.com/my-account/downloads" class="tf-admin-btn tf-btn-secondary tf-submit-btn"
                                   style="margin-top: 5px;"><?php echo esc_html__( "Download", "tourfic" ); ?></a>
							<?php elseif ( ! is_plugin_active( 'tourfic-affiliate/tourfic-affiliate.php' ) && file_exists( WP_PLUGIN_DIR . '/tourfic-affiliate/tourfic-affiliate.php' ) ) : ?>
                                <span style="margin-right: 15px;"><?php echo esc_html__( "Tourfic affiliate addon is not activated. Please activate it to use this feature.", "tourfic" ); ?> </span>
                                <a href="#" class="tf-admin-btn tf-btn-secondary tf-affiliate-active" style="margin-top: 5px;"><?php echo esc_html__( 'Activate Tourfic Affiliate', 'tourfic' ); ?></a>
							<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
			<?php
		}
	}

	/**
	 * Search Result Sidebar form
	 */
	static function tf_search_result_sidebar_form( $placement = 'single' ) {

		// Unwanted Slashes Remove
		if ( isset( $_GET ) ) {
			$_GET = array_map( 'stripslashes_deep', $_GET );
		}

		// Get post type
		$post_type                     = esc_attr( $_GET['type'] ) ?? '';
		$place_title                   = '';
		$date_format_for_users         = ! empty( self::tfopt( "tf-date-format-for-users" ) ) ? self::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
		$hotel_location_field_required = ! empty( self::tfopt( "required_location_hotel_search" ) ) ? self::tfopt( "required_location_hotel_search" ) : 0;
		$tour_location_field_required  = ! empty( self::tfopt( "required_location_tour_search" ) ) ? self::tfopt( "required_location_tour_search" ) : 0;
		$place_input_id                = '';

		if ( ! empty( $post_type ) ) {

			$place_input_id = $post_type == 'tf_hotel' ? 'tf-location' : 'tf-destination';
			if ( $post_type == 'tf_apartment' ) {
				$place_input_id = 'tf-apartment-location';
			}
			$place_placeholder = ( $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ) ? esc_html__( 'Enter Location', 'tourfic' ) : esc_html__( 'Enter Destination', 'tourfic' );

			$place_key   = 'place';
			$place_value = ! empty( $_GET[ $place_key ] ) ? esc_attr( $_GET[ $place_key ] ) : '';
			$place_title = ! empty( $_GET['place-name'] ) ? esc_attr( $_GET['place-name'] ) : '';

			$taxonomy = $post_type == 'tf_hotel' ? 'hotel_location' : ( $post_type == 'tf_tour' ? 'tour_destination' : 'apartment_location' );
			// $place_name = ! empty( $place_value ) ? get_term_by( 'slug', $place_value, $taxonomy )->name : '';
			$place_name = ! empty( $place_value ) ? esc_attr( $place_value ) : '';

			$room = ! empty( $_GET['room'] ) ? esc_attr( $_GET['room'] ) : 0;
		}

		$adult      = ! empty( $_GET['adults'] ) ? esc_attr( $_GET['adults'] ) : 0;
		$children   = ! empty( $_GET['children'] ) ? esc_attr( $_GET['children'] ) : 0;
		$infant     = ! empty( $_GET['infant'] ) ? esc_attr( $_GET['infant'] ) : 0;
		$date       = ! empty( $_GET['check-in-out-date'] ) ? esc_attr( $_GET['check-in-out-date'] ) : '';
		$startprice = ! empty( $_GET['from'] ) ? esc_attr( $_GET['from'] ) : '';
		$endprice   = ! empty( $_GET['to'] ) ? esc_attr( $_GET['to'] ) : '';

		$tf_tour_arc_selected_template      = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['tour-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
		$tf_hotel_arc_selected_template     = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['hotel-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
		$tf_apartment_arc_selected_template = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['apartment-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['apartment-archive'] : 'default';

		$disable_child_search            = ! empty( self::tfopt( 'disable_child_search' ) ) ? self::tfopt( 'disable_child_search' ) : '';
		$disable_infant_search           = ! empty( self::tfopt( 'disable_infant_search' ) ) ? self::tfopt( 'disable_infant_search' ) : '';
		$disable_hotel_child_search      = ! empty( self::tfopt( 'disable_hotel_child_search' ) ) ? self::tfopt( 'disable_hotel_child_search' ) : '';
		$disable_apartment_child_search  = ! empty( self::tfopt( 'disable_apartment_child_search' ) ) ? self::tfopt( 'disable_apartment_child_search' ) : '';
		$disable_apartment_infant_search = ! empty( self::tfopt( 'disable_apartment_infant_search' ) ) ? self::tfopt( 'disable_apartment_infant_search' ) : '';

		if ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-1" ) ||
             ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-1" ) ) {
			?>
            <div class="tf-box-wrapper tf-box tf-mrbottom-30">
                <form class="widget tf-hotel-side-booking" method="get" autocomplete="off"
                      action="<?php echo esc_url( self::tf_booking_search_action() ); ?>" id="tf-widget-booking-search">

                    <div class="tf-field-group tf-destination-box" <?php echo ( $post_type == 'tf_hotel' && self::tfopt( "hide_hotel_location_search" ) == 1 & self::tfopt( "required_location_hotel_search" ) != 1 ) || ( $post_type == 'tf_tours' && self::tfopt( "hide_tour_location_search" ) == 1 && self::tfopt( "required_location_tour_search" ) != 1 ) ? 'style="display:none"' : '' ?>>
                        <i class="fa-solid fa-location-dot"></i>

						<?php if ( $post_type == "tf_hotel" ) { ?>
                            <input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> class="tf-field"
                                   placeholder="<?php echo esc_attr( $place_placeholder ) ?? esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                                   value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
						<?php } elseif ( $post_type == "tf_tours" ) { ?>
                            <input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> class="tf-field"
                                   placeholder="<?php echo esc_attr( $place_placeholder ) ?? esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                                   value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
						<?php } else { ?>
                            <input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" required class="tf-field"
                                   placeholder="<?php echo esc_attr( $place_placeholder ) ?? esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                                   value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
						<?php } ?>

                        <input type="hidden" name="place" id="tf-place" value="<?php echo esc_attr( $place_value ) ?? ''; ?> "/>
                    </div>
                    <div class="tf-field-group tf-mt-8 tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <i class="fa-regular fa-user"></i>
								<?php esc_html_e( 'Adults', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="adults" id="adults" min="1" value="<?php echo ! empty( $adult ) ? esc_attr( $adult ) : 1; ?>">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>

	                <?php if ( ( $post_type == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
	                           ($post_type == 'tf_tours' && empty( $disable_child_search )) ||
	                           ( $post_type == 'tf_apartment' && empty( $disable_apartment_child_search ) )
	                ) { ?>
                    <div class="tf-field-group tf-mt-16 tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <i class="fa-solid fa-child"></i>
								<?php esc_html_e( 'Children', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="childrens" id="children" min="0" value="<?php echo ! empty( $children ) ? esc_attr( $children ) : 0; ?>">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
	                <?php } ?>

					<?php if ( $post_type == 'tf_hotel' ) { ?>
                        <div class="tf-field-group tf-mt-16 tf_acrselection">
                            <div class="tf-field tf-flex">
                                <div class="acr-label tf-flex">
                                    <i class="fa fa-building"></i>
									<?php esc_html_e( 'Rooms', 'tourfic' ); ?>
                                </div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="room" id="room" min="1" value="<?php echo ! empty( $room ) ? esc_attr( $room ) : 1; ?>">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                        </div>
					<?php } ?>

                    <div class="tf-field-group tf-mt-8">
                        <i class="fa-solid fa-calendar-days"></i>
                        <input type="text" class="tf-field time" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                               placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" required value="<?php echo esc_attr( $date ) ?>">
                    </div>

                    <div class="tf-booking-bttns tf-mt-30">
						<?php
						$ptype = esc_attr( $_GET['type'] ) ?? get_post_type();
						?>
                        <input type="hidden" name="type" value="<?php echo esc_attr( $ptype ); ?>" class="tf-post-type"/>
                        <button class="tf-btn-normal btn-primary tf-submit"
                                type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
                    </div>
                </form>
            </div>
            <script>
                (function ($) {
                    $(document).ready(function () {

                        const regexMap = {
                            'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                            'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                            'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                            'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                            'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                            'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                            'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                            'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                            'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                        };
                        const dateRegex = regexMap['<?php echo $date_format_for_users; ?>'];

                        $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
                            enableTime: false,
                            minDate: "today",
                            altInput: true,
                            altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',
                            mode: "range",
                            dateFormat: "Y/m/d",
                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
									return `${date1} - ${date2}`;
								});
                                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                                    return `${d1} - ${d2}`;
                                })
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
									return `${date1} - ${date2}`;
								});
                                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                                    return `${d1} - ${d2}`;
                                })
                            },
                            defaultDate: <?php echo wp_json_encode( explode( '-', $date ) ) ?>,
                        });

                    });
                })(jQuery);
            </script>

			<?php if ( is_active_sidebar( 'tf_search_result' ) ) { ?>
                <div id="tf__booking_sidebar">
					<?php dynamic_sidebar( 'tf_search_result' ); ?>
                </div>
			<?php } ?>

		<?php } elseif ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-2" ) ||
                         ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-2" ) ||
                         ( $post_type == "tf_apartment" && $tf_apartment_arc_selected_template == "design-1" ) ) { ?>
            <div class="tf-booking-form-fields <?php echo $post_type == 'tf_tours' ? esc_attr( 'tf-tour-archive-block' ) : ''; ?>">
                <div class="tf-booking-form-location" <?php echo ( $post_type == 'tf_hotel' && self::tfopt( "hide_hotel_location_search" ) == 1 && self::tfopt( "required_location_hotel_search" ) != 1 ) || ( $post_type == 'tf_tours' && self::tfopt( "hide_tour_location_search" ) == 1 && self::tfopt( "required_location_tour_search" ) != 1 ) ? 'style="display:none"' : '' ?>>
                    <span class="tf-booking-form-title"><?php esc_html_e( "Location", "tourfic" ); ?></span>
                    <label for="tf-search-location" class="tf-booking-location-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
                            <path d="M8.5 13.9317L11.7998 10.6318C13.6223 8.80943 13.6223 5.85464 11.7998 4.0322C9.9774 2.20975 7.02261 2.20975 5.20017 4.0322C3.37772 5.85464 3.37772 8.80943 5.20017 10.6318L8.5 13.9317ZM8.5 15.8173L4.25736 11.5747C1.91421 9.2315 1.91421 5.43254 4.25736 3.08939C6.60051 0.746245 10.3995 0.746245 12.7427 3.08939C15.0858 5.43254 15.0858 9.2315 12.7427 11.5747L8.5 15.8173ZM8.5 8.66536C9.2364 8.66536 9.83333 8.06843 9.83333 7.33203C9.83333 6.59565 9.2364 5.9987 8.5 5.9987C7.7636 5.9987 7.16667 6.59565 7.16667 7.33203C7.16667 8.06843 7.7636 8.66536 8.5 8.66536ZM8.5 9.9987C7.02724 9.9987 5.83333 8.80476 5.83333 7.33203C5.83333 5.85927 7.02724 4.66536 8.5 4.66536C9.97273 4.66536 11.1667 5.85927 11.1667 7.33203C11.1667 8.80476 9.97273 9.9987 8.5 9.9987Z"
                                  fill="#595349"/>
                        </svg>

						<?php if ( $post_type == "tf_hotel" ) { ?>
                            <input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> class="tf-field"
                                   placeholder="<?php echo esc_attr( $place_placeholder ) ?? esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                                   value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
						<?php } elseif ( $post_type == "tf_tours" ) { ?>
                            <input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> class="tf-field"
                                   placeholder="<?php echo esc_attr( $place_placeholder ) ?? esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                                   value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
						<?php } else { ?>
                            <input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" required class="tf-field"
                                   placeholder="<?php echo esc_attr( $place_placeholder ) ?? esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                                   value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
						<?php } ?>

                        <input type="hidden" name="place" id="tf-place" value="<?php echo esc_attr( $place_value ) ?? ''; ?>"/>
                    </label>
                </div>
				<?php if ( $post_type == 'tf_hotel' || $post_type == "tf_apartment" ) { ?>
                    <div class="tf-booking-form-checkin">
                        <span class="tf-booking-form-title"><?php esc_html_e( "Check in", "tourfic" ); ?></span>
                        <div class="tf-booking-date-wrap">
                            <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                            <span class="tf-booking-month">
						<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
                        </div>
                        <div class="tf_booking-dates">
                            <div class="tf_label-row"></div>
                        </div>
                    </div>
                    <div class="tf-booking-form-checkout">
                        <span class="tf-booking-form-title"><?php esc_html_e( "Check out", "tourfic" ); ?></span>
                        <div class="tf-booking-date-wrap">
                            <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                            <span class="tf-booking-month">
						<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
                        </div>
                        <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" value="<?php echo esc_attr( $date ) ?>"
                               required>

                    </div>
				<?php } ?>

				<?php if ( $post_type == 'tf_tours' ) { ?>
                    <div class="tf-booking-form-checkin">
                        <span class="tf-booking-form-title"><?php esc_html_e( "Date", "tourfic" ); ?></span>
                        <div class="tf-tour-searching-date-block">
                            <div class="tf-booking-date-wrap tf-tour-start-date">
                                <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                                <span class="tf-booking-month">
							<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
						</span>
                            </div>
                            <div class="tf-duration">
                                <span>-</span>
                            </div>
                            <div class="tf-booking-date-wrap tf-tour-end-date">
                                <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                                <span class="tf-booking-month">
							<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
							<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
							</svg>
						</span>
                            </div>
                            <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                   placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $date ) ? 'value="' . esc_attr( $date ) . '"' : '' ?> required>
                        </div>
                    </div>
				<?php } ?>

                <div class="tf-booking-form-guest-and-room">
					<?php if ( $post_type == 'tf_hotel' ) { ?>
                        <div class="tf-booking-form-guest-and-room-inner">
                            <span class="tf-booking-form-title"><?php esc_html_e( "Guests & rooms", "tourfic" ); ?></span>
                            <div class="tf-booking-guest-and-room-wrap tf-archive-guest-info">
                                <span class="tf-guest"><?php echo esc_html( $adult + $children ) ?> </span> <?php esc_html_e( "guest", "tourfic" ); ?> <span
                                        class="tf-room"><?php echo esc_html( $room ); ?></span> <?php esc_html_e( "Rooms", "tourfic" ); ?>
                            </div>
                            <div class="tf-arrow-icons">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                    <path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
                                </svg>
                            </div>
                        </div>
					<?php }
					if ( $post_type == 'tf_tours' || $post_type == 'tf_apartment' ) { ?>
                        <div class="tf-booking-form-guest-and-room-inner">
                            <span class="tf-booking-form-title"><?php esc_html_e( "Guests", "tourfic" ); ?></span>
                            <div class="tf-booking-guest-and-room-wrap">
						<span class="tf-guest tf-booking-date">
							0<?php echo esc_html( $adult + $children ) ?>
						</span>
                                <span class="tf-booking-month">
							<span><?php esc_html_e( "Guest", "tourfic" ); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
							<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
							</svg>
						</span>
                            </div>
                        </div>
					<?php } ?>

                    <div class="tf_acrselection-wrap">
                        <div class="tf_acrselection-inner">
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php esc_html_e( "Adults", "tourfic" ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13094)">
                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13094">
                                                    <rect width="20" height="20" fill="white"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <input type="tel" name="adults" id="adults" min="1" value="<?php echo ! empty( $adult ) ? esc_attr( $adult ) : 1; ?>" readonly>
                                    <div class="acr-inc">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13100)">
                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"></path>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13100">
                                                    <rect width="20" height="20" fill="white"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <?php if ( ( $post_type == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
                            ($post_type == 'tf_tours' && empty( $disable_child_search )) ||
                            ( $post_type == 'tf_apartment' && empty( $disable_apartment_child_search ) )
                            ) { ?>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php esc_html_e( "Children", "tourfic" ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13094)">
                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13094">
                                                    <rect width="20" height="20" fill="white"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <input type="tel" name="childrens" id="children" min="0" value="<?php echo ! empty( $children ) ? esc_attr( $children ) : 0; ?>" readonly>
                                    <div class="acr-inc">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13100)">
                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"></path>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13100">
                                                    <rect width="20" height="20" fill="white"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
							<?php if ( $post_type == 'tf_hotel' ) { ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php esc_html_e( "Rooms", "tourfic" ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                        <rect width="20" height="20" fill="white"></rect>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <input type="tel" name="room" id="room" min="1" value="<?php echo ! empty( $room ) ? esc_attr( $room ) : 1; ?>" readonly>
                                        <div class="acr-inc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"></path>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                        <rect width="20" height="20" fill="white"></rect>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tf-booking-form-submit">
				<?php
				$ptype = esc_attr( $_GET['type'] ) ?? get_post_type();
				?>
                <input type="hidden" name="type" value="<?php echo esc_attr( $ptype ); ?>" class="tf-post-type"/>
                <button class="tf-btn-normal btn-primary tf-submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
            </div>
			<?php if ( $post_type == 'tf_tours' ) { ?>
                <script>
                    (function ($) {
                        $(document).ready(function () {
                            // flatpickr locale first day of Week
							<?php self::tf_flatpickr_locale( "root" ); ?>

                            $(".tf-template-3 .tf-booking-date-wrap").on("click", function () {
                                $("#check-in-out-date").trigger("click");
                            });
                            $("#check-in-out-date").flatpickr({
                                enableTime: false,
                                mode: "range",
                                dateFormat: "Y/m/d",
                                minDate: "today",

                                // flatpickr locale
								<?php self::tf_flatpickr_locale(); ?>

                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                        return `${date1} - ${date2}`;
                                    });
                                    dateSetToFields(selectedDates, instance);
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                        return `${date1} - ${date2}`;
                                    });
                                    dateSetToFields(selectedDates, instance);
                                },
								<?php
								if(! empty( $date )){ ?>
                                defaultDate: <?php echo wp_json_encode( explode( '-', $date ) ) ?>,
								<?php } ?>
                            });

                            function dateSetToFields(selectedDates, instance) {
                                if (selectedDates.length === 2) {
                                    const monthNames = [
                                        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                    ];
                                    if (selectedDates[0]) {
                                        const startDate = selectedDates[0];
                                        $(".tf-template-3 .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-date").html(startDate.getDate());
                                        $(".tf-template-3 .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                                    }
                                    if (selectedDates[1]) {
                                        const endDate = selectedDates[1];
                                        $(".tf-template-3 .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-date").html(endDate.getDate());
                                        $(".tf-template-3 .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                                    }
                                }
                            }

                        });
                    })(jQuery);
                </script>
			<?php } ?>

			<?php if ( $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ) { ?>

                <script>
                    (function ($) {
                        $(document).ready(function () {

                            const regexMap = {
                                'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                                'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                                'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                                'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                                'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                            };
                            const dateRegex = regexMap['<?php echo $date_format_for_users; ?>'];

                            // flatpickr locale
							<?php self::tf_flatpickr_locale( "root" ); ?>

                            $(".tf-template-3 .tf-booking-date-wrap").on("click", function () {
                                $("#check-in-out-date").trigger("click");
                            });
                            $("#check-in-out-date").flatpickr({
                                enableTime: false,
                                mode: "range",
                                dateFormat: "Y/m/d",
                                minDate: "today",

                                // flatpickr locale
								<?php self::tf_flatpickr_locale(); ?>


                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                        return `${date1} - ${date2}`;
                                    });
                                    dateSetToFields(selectedDates, instance);
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                        return `${date1} - ${date2}`;
                                    });
                                    dateSetToFields(selectedDates, instance);
                                },
                                defaultDate: <?php echo wp_json_encode( explode( '-', $date ) ) ?>,
                            });

                            function dateSetToFields(selectedDates, instance) {
                                if (selectedDates.length === 2) {
                                    const monthNames = [
                                        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                    ];
                                    if (selectedDates[0]) {
                                        const startDate = selectedDates[0];
                                        $(".tf-template-3 .tf-booking-form-checkin span.tf-booking-date").html(startDate.getDate());
                                        $(".tf-template-3 .tf-booking-form-checkin span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                                    }
                                    if (selectedDates[1]) {
                                        const endDate = selectedDates[1];
                                        $(".tf-template-3 .tf-booking-form-checkout span.tf-booking-date").html(endDate.getDate());
                                        $(".tf-template-3 .tf-booking-form-checkout span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                                    }
                                }
                            }

                        });
                    })(jQuery);
                </script>
			<?php } ?>
		<?php } elseif ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ||
		                 ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ||
		                 ( $post_type == "tf_apartment" && $tf_apartment_arc_selected_template == "design-2" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ) { ?>
            <div class="tf-search-fields <?php echo $post_type == 'tf_tours' ? esc_attr( 'tf-tour-archive-block' ) : ''; ?>">
                <div class="tf-search-field">
                    <div class="tf-search-field-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                            <g clip-path="url(#clip0_1327_100053)">
                                <path d="M15.8033 14.97L10.5 20.2733L5.1967 14.97C2.26777 12.041 2.26777 7.29232 5.1967 4.36339C8.12563 1.43445 12.8743 1.43445 15.8033 4.36339C18.7323 7.29232 18.7323 12.041 15.8033 14.97ZM10.5 13C12.3409 13 13.8333 11.5076 13.8333 9.66669C13.8333 7.82574 12.3409 6.33335 10.5 6.33335C8.65905 6.33335 7.16667 7.82574 7.16667 9.66669C7.16667 11.5076 8.65905 13 10.5 13ZM10.5 11.3334C9.5795 11.3334 8.83333 10.5872 8.83333 9.66669C8.83333 8.74621 9.5795 8.00002 10.5 8.00002C11.4205 8.00002 12.1667 8.74621 12.1667 9.66669C12.1667 10.5872 11.4205 11.3334 10.5 11.3334Z"
                                      fill="#6E655E"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_1327_100053">
                                    <rect width="20" height="20" fill="white" transform="translate(0.5 0.5)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </div>
                    <label for="tf-search-location" class="tf-search-field-content">
                        <span class="tf-search-field-label"><?php echo $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ? esc_html__( 'Location', 'tourfic' ) : esc_html__( 'Destination', 'tourfic' ); ?></span>

                        <input type="text" id="<?php echo esc_attr( $place_input_id ) ?? ''; ?>" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> class="tf-search-input" placeholder="<?php echo $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ? esc_html__( 'Enter Location', 'tourfic' ) : esc_html__( 'Where are you going?', 'tourfic' ); ?>" value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
                        <input type="hidden" name="place" id="tf-place" value="<?php echo esc_attr( $place_value ) ?? ''; ?>"/>
                    </label>
                </div>
                <div class="tf-search-field-divider"></div>
				<?php if ( $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ) { ?>
                    <div class="tf-search-field-checkinout">
                        <div class="tf-search-field tf-search-field-checkin">
                            <div class="tf-search-field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M14.668 2.99992H18.0013C18.4616 2.99992 18.8346 3.37302 18.8346 3.83325V17.1666C18.8346 17.6268 18.4616 17.9999 18.0013 17.9999H3.0013C2.54107 17.9999 2.16797 17.6268 2.16797 17.1666V3.83325C2.16797 3.37302 2.54107 2.99992 3.0013 2.99992H6.33464V1.33325H8.0013V2.99992H13.0013V1.33325H14.668V2.99992ZM3.83464 7.99992V16.3333H17.168V7.99992H3.83464ZM5.5013 9.66658H7.16797V11.3333H5.5013V9.66658ZM5.5013 12.9999H7.16797V14.6666H5.5013V12.9999ZM8.83464 9.66658H15.5013V11.3333H8.83464V9.66658ZM8.83464 12.9999H13.0013V14.6666H8.83464V12.9999Z"
                                          fill="#6E655E"/>
                                </svg>
                            </div>
                            <div class="tf-search-field-content">
                                <span class="tf-search-field-label"><?php esc_html_e( "Check in", "tourfic" ); ?></span>
                                <input type="text" class="tf-search-input" name="tf-check-in" id="tf-check-in" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" value="" readonly>
                                <input type="text" class="tf-search-input" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" value="<?php echo esc_attr( $date ) ?>">
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                            <path d="M11.2824 7.83327L7.70637 4.25726L8.64917 3.31445L13.8346 8.49993L8.64917 13.6853L7.70637 12.7425L11.2824 9.1666H3.16797V7.83327H11.2824Z" fill="#6E655E"/>
                        </svg>
                        <div class="tf-search-field tf-search-field-checkout">
                            <div class="tf-search-field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M14.668 2.99992H18.0013C18.4616 2.99992 18.8346 3.37302 18.8346 3.83325V17.1666C18.8346 17.6268 18.4616 17.9999 18.0013 17.9999H3.0013C2.54107 17.9999 2.16797 17.6268 2.16797 17.1666V3.83325C2.16797 3.37302 2.54107 2.99992 3.0013 2.99992H6.33464V1.33325H8.0013V2.99992H13.0013V1.33325H14.668V2.99992ZM3.83464 7.99992V16.3333H17.168V7.99992H3.83464ZM5.5013 9.66658H7.16797V11.3333H5.5013V9.66658ZM5.5013 12.9999H7.16797V14.6666H5.5013V12.9999ZM8.83464 9.66658H15.5013V11.3333H8.83464V9.66658ZM8.83464 12.9999H13.0013V14.6666H8.83464V12.9999Z"
                                          fill="#6E655E"/>
                                </svg>
                            </div>
                            <div class="tf-search-field-content">
                                <span class="tf-search-field-label"><?php esc_html_e( "Check out", "tourfic" ); ?></span>
                                <input type="text" class="tf-search-input" name="tf-check-out" id="tf-check-out" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" value="">
                            </div>
                        </div>
                    </div>
				<?php } ?>

				<?php if ( $post_type == 'tf_tours' ) { ?>
                    <div class="tf-search-field-checkinout">
                        <div class="tf-search-field">
                            <div class="tf-search-field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M14.668 2.99992H18.0013C18.4616 2.99992 18.8346 3.37302 18.8346 3.83325V17.1666C18.8346 17.6268 18.4616 17.9999 18.0013 17.9999H3.0013C2.54107 17.9999 2.16797 17.6268 2.16797 17.1666V3.83325C2.16797 3.37302 2.54107 2.99992 3.0013 2.99992H6.33464V1.33325H8.0013V2.99992H13.0013V1.33325H14.668V2.99992ZM3.83464 7.99992V16.3333H17.168V7.99992H3.83464ZM5.5013 9.66658H7.16797V11.3333H5.5013V9.66658ZM5.5013 12.9999H7.16797V14.6666H5.5013V12.9999ZM8.83464 9.66658H15.5013V11.3333H8.83464V9.66658ZM8.83464 12.9999H13.0013V14.6666H8.83464V12.9999Z"
                                          fill="#6E655E"/>
                                </svg>
                            </div>
                            <div class="tf-search-field-content">
                                <span class="tf-search-field-label"><?php esc_html_e( "Select Date", "tourfic" ); ?></span>
                                <input type="text" class="tf-search-input" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr($check_in_out) . '"' : '' ?>>
                            </div>
                        </div>
                    </div>
				<?php } ?>
                <div class="tf-search-field-divider"></div>
                <div class="tf-search-guest-and-room">
					<?php if ( $post_type == 'tf_hotel' ) { ?>
                        <div class="tf-search-field">
                            <div class="tf-search-field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M10.5013 8.83341C12.3422 8.83341 13.8346 7.34103 13.8346 5.50008C13.8346 3.65913 12.3422 2.16675 10.5013 2.16675C8.66035 2.16675 7.16797 3.65913 7.16797 5.50008C7.16797 7.34103 8.66035 8.83341 10.5013 8.83341ZM5.08464 11.3334C6.23523 11.3334 7.16797 10.4007 7.16797 9.25008C7.16797 8.09949 6.23523 7.16675 5.08464 7.16675C3.93404 7.16675 3.0013 8.09949 3.0013 9.25008C3.0013 10.4007 3.93404 11.3334 5.08464 11.3334ZM18.0013 9.25008C18.0013 10.4007 17.0686 11.3334 15.918 11.3334C14.7674 11.3334 13.8346 10.4007 13.8346 9.25008C13.8346 8.09949 14.7674 7.16675 15.918 7.16675C17.0686 7.16675 18.0013 8.09949 18.0013 9.25008ZM10.5013 9.66675C12.8025 9.66675 14.668 11.5322 14.668 13.8334V18.8334H6.33464V13.8334C6.33464 11.5322 8.20012 9.66675 10.5013 9.66675ZM4.66797 13.8333C4.66797 13.2559 4.75186 12.6981 4.90812 12.1714L4.76684 12.1837C3.30549 12.3421 2.16797 13.5799 2.16797 15.0833V18.8333H4.66797V13.8333ZM18.8346 18.8333V15.0833C18.8346 13.5316 17.6229 12.2628 16.0945 12.1714C16.2507 12.6981 16.3346 13.2559 16.3346 13.8333V18.8333H18.8346Z"
                                          fill="#6E655E"/>
                                </svg>
                            </div>
                            <div class="tf-search-field-content">
                                <span class="tf-search-field-label"><?php esc_html_e( "Guests & rooms", "tourfic" ); ?></span>
                                <div class="tf-archive-guest-info">
                                    <span class="tf-guest"><?php echo esc_html( $adult + $children ) ?> </span> <?php esc_html_e( "guest", "tourfic" ); ?>
                                    <span class="tf-room"><?php echo esc_html( $room ); ?></span> <?php esc_html_e( "Rooms", "tourfic" ); ?>
                                </div>
                            </div>
                        </div>
					<?php } else { ?>
                        <div class="tf-search-field">
                            <div class="tf-search-field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M10.5013 8.83341C12.3422 8.83341 13.8346 7.34103 13.8346 5.50008C13.8346 3.65913 12.3422 2.16675 10.5013 2.16675C8.66035 2.16675 7.16797 3.65913 7.16797 5.50008C7.16797 7.34103 8.66035 8.83341 10.5013 8.83341ZM5.08464 11.3334C6.23523 11.3334 7.16797 10.4007 7.16797 9.25008C7.16797 8.09949 6.23523 7.16675 5.08464 7.16675C3.93404 7.16675 3.0013 8.09949 3.0013 9.25008C3.0013 10.4007 3.93404 11.3334 5.08464 11.3334ZM18.0013 9.25008C18.0013 10.4007 17.0686 11.3334 15.918 11.3334C14.7674 11.3334 13.8346 10.4007 13.8346 9.25008C13.8346 8.09949 14.7674 7.16675 15.918 7.16675C17.0686 7.16675 18.0013 8.09949 18.0013 9.25008ZM10.5013 9.66675C12.8025 9.66675 14.668 11.5322 14.668 13.8334V18.8334H6.33464V13.8334C6.33464 11.5322 8.20012 9.66675 10.5013 9.66675ZM4.66797 13.8333C4.66797 13.2559 4.75186 12.6981 4.90812 12.1714L4.76684 12.1837C3.30549 12.3421 2.16797 13.5799 2.16797 15.0833V18.8333H4.66797V13.8333ZM18.8346 18.8333V15.0833C18.8346 13.5316 17.6229 12.2628 16.0945 12.1714C16.2507 12.6981 16.3346 13.2559 16.3346 13.8333V18.8333H18.8346Z"
                                          fill="#6E655E"/>
                                </svg>
                            </div>

                            <div class="tf-search-field-content">
                                <span class="tf-search-field-label"><?php esc_html_e( "Persons", "tourfic" ); ?></span>
                                <div class="tf-archive-guest-info">
                                    <span class="tf-adult"><?php echo esc_html( $adult ) ?></span> <?php esc_html_e( "adult", "tourfic" ); ?>
	                                <?php if ( ($post_type == 'tf_tours' && empty( $disable_child_search )) ||
	                                           ( $post_type == 'tf_apartment' && empty( $disable_apartment_child_search ) )
	                                ) { ?>
                                        , <span class="tf-children"><?php echo esc_html( $children ) ?></span> <?php esc_html_e( "children", "tourfic" ); ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
					<?php } ?>

                    <div class="tf_acrselection-wrap">
                        <div class="tf_acrselection-inner">
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php esc_html_e( "Adults", "tourfic" ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13094)">
                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13094">
                                                    <rect width="20" height="20" fill="white"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <input type="tel" name="adults" id="adults" min="1" value="<?php echo ! empty( $adult ) ? esc_attr( $adult ) : 1; ?>" readonly>
                                    <div class="acr-inc">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13100)">
                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13100">
                                                    <rect width="20" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                            </div>
	                        <?php if ( ( $post_type == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
	                                   ($post_type == 'tf_tours' && empty( $disable_child_search )) ||
	                                   ( $post_type == 'tf_apartment' && empty( $disable_apartment_child_search ) )
	                        ) { ?>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php esc_html_e( "Children", "tourfic" ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13094)">
                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13094">
                                                    <rect width="20" height="20" fill="white"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <input type="tel" name="childrens" id="children" min="0" value="<?php echo ! empty( $children ) ? esc_attr( $children ) : 0; ?>" readonly>
                                    <div class="acr-inc">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13100)">
                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13100">
                                                    <rect width="20" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                            </div>
	                        <?php } ?>
	                        <?php if ( $post_type == 'tf_hotel' ) { ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php esc_html_e( "Rooms", "tourfic" ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                        <rect width="20" height="20" fill="white"></rect>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <input type="tel" name="room" id="room" min="1" value="<?php echo ! empty( $room ) ? esc_attr( $room ) : 1; ?>" readonly>
                                        <div class="acr-inc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tf-booking-form-submit">
	            <?php $ptype = esc_attr( $_GET['type'] ) ?? get_post_type(); ?>
                <input type="hidden" name="type" value="<?php echo esc_attr($ptype); ?>" class="tf-post-type"/>
                <button class="tf-btn-normal btn-primary tf-submit"><?php esc_html_e( 'Search Now', 'tourfic' ); ?></button>
            </div>

			<?php if ( $post_type == 'tf_hotel' || $post_type == 'tf_tours' || $post_type == 'tf_apartment' ) : ?>
                <script>
                    (function ($) {
                        $(document).ready(function () {
                            // flatpickr locale first day of Week
							<?php self::tf_flatpickr_locale( "root" ); ?>

                            $(".tf-hotel-template-4 #tf-check-out").on('click', function () {
                                $(".tf-search-input.form-control").click();
                            });

                            $("#check-in-out-date").flatpickr({
                                enableTime: false,
                                mode: "range",
                                dateFormat: "Y/m/d",
                                minDate: "today",
                                altInput: true,
                                altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',
                                showMonths: $(window).width() >= 1240 ? 2 : 1,

                                // flatpickr locale
								<?php self::tf_flatpickr_locale(); ?>

                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                                    dateSetToFields(selectedDates, instance);
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                                    dateSetToFields(selectedDates, instance);
                                },
								<?php if(! empty( $date )){ ?>
                                defaultDate: <?php echo wp_json_encode( explode( '-', $date ) ) ?>,
								<?php } ?>
                            });

                            function dateSetToFields(selectedDates, instance) {
                                const format = '<?php echo esc_html( $date_format_for_users ); ?>';
                                if (selectedDates.length === 2) {
                                    if (selectedDates[0]) {
                                        let checkInDate = instance.formatDate(selectedDates[0], format);
                                        $(".tf-hotel-template-4 #tf-check-in").val(checkInDate);
                                    }

                                    if (selectedDates[1]) {
                                        let checkOutDate = instance.formatDate(selectedDates[1], format);
                                        $(".tf-hotel-template-4 #tf-check-out").val(checkOutDate);
                                    }
                                }
                            }

                        });
                    })(jQuery);
                </script>
			<?php endif; ?>
		<?php } else { ?>
            <!-- Start Booking widget -->
            <form class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
                  action="<?php echo esc_url( self::tf_booking_search_action() ); ?>" id="tf-widget-booking-search">

                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner" <?php echo ( $post_type == 'tf_hotel' && self::tfopt( "hide_hotel_location_search" ) == 1 && self::tfopt( "required_location_hotel_search" ) != 1 ) || ( $post_type == 'tf_tours' && self::tfopt( "hide_tour_location_search" ) == 1 && self::tfopt( "required_location_tour_search" ) != 1 ) ? 'style="display:none"' : '' ?>>
                            <i class="fas fa-map-marker-alt"></i>

							<?php if ( $post_type == "tf_hotel" ) { ?>
                                <input type="text" id="<?php echo isset( $place_input_id ) ? esc_attr( $place_input_id ) : ''; ?>" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> class=""
                                       placeholder="<?php echo isset( $place_placeholder ) ? esc_attr( $place_placeholder ) : esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                                       value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
							<?php } elseif ( $post_type == "tf_tours" ) { ?>
                                <input type="text" id="<?php echo isset( $place_input_id ) ? esc_attr( $place_input_id ) : ''; ?>" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> class=""
                                       placeholder="<?php echo isset( $place_placeholder ) ? esc_attr( $place_placeholder ) : esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                                       value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
							<?php } else { ?>
                                <input type="text" id="<?php echo isset( $place_input_id ) ? esc_attr( $place_input_id ) : ''; ?>" required class=""
                                       placeholder="<?php echo isset( $place_placeholder ) ? esc_attr( $place_placeholder ) : esc_html__( 'Location/Destination', 'tourfic' ); ?>"
                                       value="<?php echo ! empty( $place_title ) ? esc_attr( $place_title ) : ''; ?>">
							<?php } ?>
                            <input type="hidden" name="place" id="tf-place" value="<?php echo isset( $place_value ) ? esc_attr( $place_value ) : ''; ?>"/>
                        </div>
                    </label>
                </div>

                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="fas fa-user-friends"></i>
                            <select name="adults" id="adults" class="">
                                <option <?php echo 1 == $adult ? 'selected' : null ?> value="1">1 <?php esc_html_e( 'Adult', 'tourfic' ); ?></option>
								<?php foreach ( range( 2, 8 ) as $value ) {
									$selected = $value == $adult ? 'selected' : null;
									echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Adults", "tourfic" ) . '</option>';
								} ?>
                            </select>
                        </div>
                    </label>
                </div>
				<?php if ( $post_type == 'tf_tours' && empty( $disable_child_search ) ) : ?>
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <i class="fas fa-child"></i>
                                <select name="children" id="children" class="">
                                    <option value="0">0 <?php esc_html_e( 'Children', 'tourfic' ); ?></option>
                                    <option <?php echo 1 == $children ? 'selected' : null ?> value="1">1 <?php esc_html_e( 'Children', 'tourfic' ); ?></option>
									<?php foreach ( range( 2, 8 ) as $value ) {
										$selected = $value == $children ? 'selected' : null;
										echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Children", "tourfic" ) . '</option>';
									} ?>

                                </select>
                            </div>
                        </label>
                    </div>
				<?php endif; ?>
				<?php if ( ( $post_type == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
				           ( $post_type == 'tf_apartment' && empty( $disable_apartment_child_search ) )
				) { ?>
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <i class="fas fa-child"></i>
                                <select name="children" id="children" class="">
                                    <option value="0">0 <?php esc_html_e( 'Children', 'tourfic' ); ?></option>
                                    <option <?php echo 1 == $children ? 'selected' : null ?> value="1">1 <?php esc_html_e( 'Children', 'tourfic' ); ?></option>
									<?php foreach ( range( 2, 8 ) as $value ) {
										$selected = $value == $children ? 'selected' : null;
										echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Children", "tourfic" ) . '</option>';
									} ?>

                                </select>
                            </div>
                        </label>
                    </div>
				<?php } ?>
				<?php if ( ( $post_type == 'tf_tours' && empty( $disable_infant_search ) ) ||
				           ( $post_type == 'tf_apartment' && empty( $disable_apartment_infant_search ) )
				): ?>
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <i class="fas fa-child"></i>
                                <select name="infant" id="infant" class="">
                                    <option value="0">0 <?php esc_html_e( 'Infant', 'tourfic' ); ?></option>
                                    <option <?php echo 1 == $infant ? 'selected' : null ?> value="1">1 <?php esc_html_e( 'Infant', 'tourfic' ); ?></option>
									<?php foreach ( range( 2, 8 ) as $value ) {
										$selected = $value == $infant ? 'selected' : null;
										echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Infant", "tourfic" ) . '</option>';
									} ?>

                                </select>
                            </div>
                        </label>
                    </div>
				<?php endif; ?>
				<?php if ( $post_type == 'tf_hotel' ) { ?>
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <i class="fas fa-couch"></i>
                                <select name="room" id="room" class="">
                                    <option <?php echo 1 == $room ? 'selected' : null ?> value="1">1 <?php esc_html_e( 'Room', 'tourfic' ); ?></option>
									<?php foreach ( range( 2, 8 ) as $value ) {
										$selected = $value == $room ? 'selected' : null;
										echo '<option ' . esc_attr( $selected ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Rooms", "tourfic" ) . '</option>';
									} ?>
                                </select>
                            </div>
                        </label>
                    </div>
				<?php } ?>
                <div class="tf_booking-dates">
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <i class="far fa-calendar-alt"></i>
                                <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                       placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" required value="<?php echo esc_attr( $date ) ?>">
                            </div>
                        </label>
                    </div>
                </div>

                <div class="tf_form-row">
					<?php
					if ( ! empty( $startprice ) && ! empty( $endprice ) ) { ?>
                        <input type="hidden" id="startprice" value="<?php echo esc_attr( $startprice ); ?>">
                        <input type="hidden" id="endprice" value="<?php echo esc_attr( $endprice ); ?>">
					<?php } ?>
					<?php
					if ( ! empty( $_GET['tf-author'] ) ) { ?>
                        <input type="hidden" id="tf_author" value="<?php echo esc_html( $_GET['tf-author'] ); ?>">
					<?php } ?>
					<?php
					$ptype = esc_attr( $_GET['type'] ) ?? get_post_type();
					?>
                    <input type="hidden" name="type" value="<?php echo esc_attr( $ptype ); ?>" class="tf-post-type"/>
                    <button class="tf_button tf-submit btn-styled"
                            type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
                </div>

            </form>
            <script>
                (function ($) {
                    $(document).ready(function () {

                        // flatpickr locale first day of Week
						<?php self::tf_flatpickr_locale( "root" ); ?>

                        const regexMap = {
                            'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                            'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                            'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                            'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                            'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                            'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                            'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                            'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                            'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                        };
                        const dateRegex = regexMap['<?php echo $date_format_for_users; ?>'];

                        $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
                            enableTime: false,
                            minDate: "today",
                            altInput: true,
                            altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',
                            mode: "range",
                            dateFormat: "Y/m/d",

                            // flatpickr locale
							<?php self::tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                    return `${date1} - ${date2}`;
                                });
                                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                                    return `${d1} - ${d2}`;
                                });
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                    return `${date1} - ${date2}`;
                                });
                                instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                                    return `${d1} - ${d2}`;
                                });
                            },
                            defaultDate: <?php echo wp_json_encode( explode( '-', $date ) ) ?>,
                        });

                    });
                })(jQuery);
            </script>

			<?php if ( is_active_sidebar( 'tf_search_result' ) ) { ?>
                <div id="tf__booking_sidebar">
					<?php dynamic_sidebar( 'tf_search_result' ); ?>
                </div>
			<?php } ?>

		<?php } ?>

		<?php
	}

	/**
	 * Archive Sidebar Search Form
	 */
	static function tf_archive_sidebar_search_form( $post_type, $taxonomy = '', $taxonomy_name = '', $taxonomy_slug = '' ) {
		$place = $post_type == 'tf_hotel' ? 'tf-location' : 'tf-destination';
		if ( $post_type == 'tf_apartment' ) {
			$place = 'tf-apartment-location';
		}
		$place_text            = $post_type == 'tf_hotel' ? esc_html__( 'Enter Location', 'tourfic' ) : esc_html__( 'Enter Destination', 'tourfic' );
		$date_format_for_users = ! empty( self::tfopt( "tf-date-format-for-users" ) ) ? self::tfopt( "tf-date-format-for-users" ) : "Y/m/d";

		$tf_tour_arc_selected_template      = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['tour-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
		$tf_hotel_arc_selected_template     = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['hotel-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
		$tf_apartment_arc_selected_template = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['apartment-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['apartment-archive'] : 'default';

		$tf_car_arc_selected_template = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['car-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['car-archive'] : 'design-1';

		$hotel_location_field_required      = ! empty( self::tfopt( "required_location_hotel_search" ) ) ? self::tfopt( "required_location_hotel_search" ) : 0;
		$tour_location_field_required       = ! empty( self::tfopt( "required_location_tour_search" ) ) ? self::tfopt( "required_location_tour_search" ) : 0;

		$hotel_location_field_required = ! empty( self::tfopt( "required_location_hotel_search" ) ) ? self::tfopt( "required_location_hotel_search" ) : 0;
		$tour_location_field_required  = ! empty( self::tfopt( "required_location_tour_search" ) ) ? self::tfopt( "required_location_tour_search" ) : 0;
		$disable_child_search            = ! empty( self::tfopt( 'disable_child_search' ) ) ? self::tfopt( 'disable_child_search' ) : '';
		$disable_infant_search           = ! empty( self::tfopt( 'disable_infant_search' ) ) ? self::tfopt( 'disable_infant_search' ) : '';
		$disable_hotel_child_search      = ! empty( self::tfopt( 'disable_hotel_child_search' ) ) ? self::tfopt( 'disable_hotel_child_search' ) : '';
		$disable_apartment_child_search  = ! empty( self::tfopt( 'disable_apartment_child_search' ) ) ? self::tfopt( 'disable_apartment_child_search' ) : '';
		$disable_apartment_infant_search = ! empty( self::tfopt( 'disable_apartment_infant_search' ) ) ? self::tfopt( 'disable_apartment_infant_search' ) : '';

		if ( ( is_post_type_archive( 'tf_hotel' ) && $tf_hotel_arc_selected_template == "design-1" ) ||
             ( is_post_type_archive( 'tf_tours' ) && $tf_tour_arc_selected_template == "design-1" ) ||
             ( $post_type == 'tf_hotel' && $tf_hotel_arc_selected_template == "design-1" ) ||
             ( $post_type == 'tf_tours' && $tf_tour_arc_selected_template == "design-1" ) ) {
			?>
            <div class="tf-box-wrapper tf-box tf-mrbottom-30">
                <form action="<?php echo esc_url( self::tf_booking_search_action() ); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking">
                    <div class="tf-field-group tf-destination-box" <?php echo ( $post_type == 'tf_hotel' && self::tfopt( "hide_hotel_location_search" ) == 1 && self::tfopt( "required_location_hotel_search" ) != 1 ) || ( $post_type == 'tf_tours' && self::tfopt( "hide_tour_location_search" ) == 1 && self::tfopt( "required_location_tour_search" ) != 1 ) ? 'style="display:none"' : '' ?>>
                        <i class="fa-solid fa-location-dot"></i>

						<?php if ( is_post_type_archive( "tf_hotel" ) ) { ?>
                            <input type="text" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="tf-field"
                                   placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
						<?php } elseif ( is_post_type_archive( "tf_tours" ) ) { ?>
                            <input type="text" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="tf-field"
                                   placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
						<?php } else { ?>
                            <input type="text" required="" id="<?php echo esc_attr( $place ); ?>" class="tf-field" placeholder="<?php echo esc_attr( $place_text ); ?>"
                                   value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
						<?php } ?>
                        <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? esc_attr( $taxonomy_slug ) : ''; ?>"/>

                    </div>
                    <div class="tf-field-group tf-mt-8 tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <i class="fa-regular fa-user"></i>
								<?php esc_html_e( 'Adults', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="adults" id="adults" min="1" value="1">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
	                <?php if ( ( $post_type == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
	                           ($post_type == 'tf_tours' && empty( $disable_child_search ))
	                ) { ?>
                    <div class="tf-field-group tf-mt-16 tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <i class="fa-solid fa-child"></i>
								<?php esc_html_e( 'Children', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="childrens" id="children" min="0" value="0">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

					<?php if ( $post_type !== 'tf_tours' ) { ?>

                        <div class="tf-field-group tf-mt-16 tf_acrselection">
                            <div class="tf-field tf-flex">
                                <div class="acr-label tf-flex">
                                    <i class="fa fa-building"></i>
									<?php esc_html_e( 'Room', 'tourfic' ); ?>
                                </div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="room" id="room" min="1" value="1">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                        </div>
					<?php } ?>

                    <div class="tf-field-group tf-mt-8">
                        <i class="fa-solid fa-calendar-days"></i>
                        <input type="text" class="tf-field time" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                               placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" required value="" style="width: 100% !important">
                    </div>
                    <div class="tf_booking-dates">
                        <div class="tf_label-row"></div>
                    </div>
                    <div class="tf-booking-bttns tf-mt-30">
                        <input type="hidden" name="type" value="<?php echo esc_attr( $post_type ); ?>" class="tf-post-type"/>
                        <button class="tf-btn-normal btn-primary tf-submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
                    </div>
                </form>
            </div>
            <script>
                (function ($) {
                    $(document).ready(function () {
						<?php self::tf_flatpickr_locale( 'root' ); ?>

                        $(document).on("focus", ".tf-hotel-side-booking #check-in-out-date", function (e) {
                            const regexMap = {
                                'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                                'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                                'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                                'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                                'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                            };
                            const dateRegex = regexMap['<?php echo $date_format_for_users; ?>'];
                            let calander = flatpickr(this, {
                                enableTime: false,
                                minDate: "today",
                                mode: "range",
                                dateFormat: "Y/m/d",
                                altInput: true,
                                altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',

                                // flatpickr locale
								<?php self::tf_flatpickr_locale(); ?>

                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                        return `${date1} - ${date2}`;
                                    });
                                    instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                                        return `${d1} - ${d2}`;
                                    });
                                },
                            });

                            // open flatpickr on focus
                            calander.open();
                        })
                    });
                })(jQuery);
            </script>

			<?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
                <div id="tf__booking_sidebar">
					<?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
                </div>
			<?php } ?>

			<?php
		} elseif ( ( is_post_type_archive( 'tf_hotel' ) && $tf_hotel_arc_selected_template == "design-2" ) ||
                   ( is_post_type_archive( 'tf_tours' ) && $tf_tour_arc_selected_template == "design-2" ) ||
                   ( is_post_type_archive( 'tf_apartment' ) && $tf_apartment_arc_selected_template == "design-1" ) ||
                   ( $post_type == 'tf_hotel' && $tf_hotel_arc_selected_template == "design-2" ) ||
                   ( $post_type == 'tf_tours' && $tf_tour_arc_selected_template == "design-2" ) ||
                   ( $post_type == 'tf_apartment' && $tf_apartment_arc_selected_template == "design-1" )
        ) { ?>
            <div class="tf-booking-form-fields <?php echo $post_type == 'tf_tours' ? esc_attr( 'tf-tour-archive-block' ) : ''; ?>">
                <div class="tf-booking-form-location" <?php echo ( $post_type == 'tf_hotel' && self::tfopt( "hide_hotel_location_search" ) == 1 && self::tfopt( "required_location_hotel_search" ) != 1 ) || ( $post_type == 'tf_tours' && self::tfopt( "hide_tour_location_search" ) == 1 && self::tfopt( "required_location_tour_search" ) != 1 ) ? 'style="display:none"' : '' ?>>
                    <span class="tf-booking-form-title"><?php esc_html_e( "Location", "tourfic" ); ?></span>
                    <label for="tf-search-location" class="tf-booking-location-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
                            <path d="M8.5 13.9317L11.7998 10.6318C13.6223 8.80943 13.6223 5.85464 11.7998 4.0322C9.9774 2.20975 7.02261 2.20975 5.20017 4.0322C3.37772 5.85464 3.37772 8.80943 5.20017 10.6318L8.5 13.9317ZM8.5 15.8173L4.25736 11.5747C1.91421 9.2315 1.91421 5.43254 4.25736 3.08939C6.60051 0.746245 10.3995 0.746245 12.7427 3.08939C15.0858 5.43254 15.0858 9.2315 12.7427 11.5747L8.5 15.8173ZM8.5 8.66536C9.2364 8.66536 9.83333 8.06843 9.83333 7.33203C9.83333 6.59565 9.2364 5.9987 8.5 5.9987C7.7636 5.9987 7.16667 6.59565 7.16667 7.33203C7.16667 8.06843 7.7636 8.66536 8.5 8.66536ZM8.5 9.9987C7.02724 9.9987 5.83333 8.80476 5.83333 7.33203C5.83333 5.85927 7.02724 4.66536 8.5 4.66536C9.97273 4.66536 11.1667 5.85927 11.1667 7.33203C11.1667 8.80476 9.97273 9.9987 8.5 9.9987Z"
                                  fill="#595349"/>
                        </svg>
						<?php if ( is_post_type_archive( "tf_hotel" ) ) { ?>
                            <input type="text" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="tf-field"
                                   placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
						<?php } elseif ( is_post_type_archive( "tf_tours" ) ) { ?>
                            <input type="text" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class="tf-field"
                                   placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
						<?php } else { ?>
                            <input type="text" required="" id="<?php echo esc_attr( $place ); ?>" class="tf-field" placeholder="<?php echo esc_attr( $place_text ); ?>"
                                   value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
						<?php } ?>
                        <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? esc_attr( $taxonomy_slug ) : ''; ?>"/>
                    </label>
                </div>

				<?php if ( $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ) { ?>
                    <div class="tf-booking-form-checkin">
                        <span class="tf-booking-form-title"><?php esc_html_e( "Check in", "tourfic" ); ?></span>
                        <div class="tf-booking-date-wrap">
                            <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                            <span class="tf-booking-month">
						<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
                        </div>
                    </div>
                    <div class="tf-booking-form-checkout">
                        <span class="tf-booking-form-title"><?php esc_html_e( "Check out", "tourfic" ); ?></span>
                        <div class="tf-booking-date-wrap">
                            <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                            <span class="tf-booking-month">
						<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
                        </div>
                        <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                               placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr( $check_in_out ) . '"' : '' ?> required>
                    </div>
				<?php } ?>

				<?php if ( $post_type == 'tf_tours' ) { ?>
                    <div class="tf-booking-form-checkin">
                        <span class="tf-booking-form-title"><?php esc_html_e( "Date", "tourfic" ); ?></span>
                        <div class="tf-tour-searching-date-block">
                            <div class="tf-booking-date-wrap tf-tour-start-date">
                                <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                                <span class="tf-booking-month">
							<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
						</span>
                            </div>
                            <div class="tf-duration">
                                <span>-</span>
                            </div>
                            <div class="tf-booking-date-wrap tf-tour-end-date">
                                <span class="tf-booking-date"><?php esc_html_e( "00", "tourfic" ); ?></span>
                                <span class="tf-booking-month">
							<span><?php echo esc_html( gmdate( 'M' ) ); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
							<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
							</svg>
						</span>
                            </div>
                            <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                   placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr( $check_in_out ) . '"' : '' ?> required>
                        </div>
                    </div>
				<?php } ?>
                <div class="tf-booking-form-guest-and-room">
					<?php if ( $post_type == 'tf_hotel' ) { ?>
                        <div class="tf-booking-form-guest-and-room-inner">
                            <span class="tf-booking-form-title"><?php esc_html_e( "Guests & rooms", "tourfic" ); ?></span>
                            <div class="tf-booking-guest-and-room-wrap tf-archive-guest-info">
                                <span class="tf-guest"><?php esc_html_e( "01", "tourfic" ); ?></span> <?php esc_html_e( "guest", "tourfic" ); ?> <span
                                        class="tf-room"><?php esc_html_e( "01", "tourfic" ); ?></span> <?php esc_html_e( "rooms", "tourfic" ); ?>
                            </div>
                            <div class="tf-arrow-icons">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                                    <path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
                                </svg>
                            </div>
                        </div>
					<?php } else { ?>
                        <div class="tf-booking-form-guest-and-room-inner">
                            <span class="tf-booking-form-title"><?php esc_html_e( "Guests", "tourfic" ); ?></span>
                            <div class="tf-booking-guest-and-room-wrap">
						<span class="tf-guest tf-booking-date">
							<?php esc_html_e( "01", "tourfic" ); ?>
						</span>
                                <span class="tf-booking-month">
							<span><?php esc_html_e( "guest", "tourfic" ); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
							<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
							</svg>
						</span>
                            </div>
                        </div>
					<?php } ?>

                    <div class="tf_acrselection-wrap">
                        <div class="tf_acrselection-inner">
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php esc_html_e( "Adults", "tourfic" ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13094)">
                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13094">
                                                    <rect width="20" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <input type="tel" name="adults" id="adults" min="1" value="1" readonly>
                                    <div class="acr-inc">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13100)">
                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13100">
                                                    <rect width="20" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                            </div>
	                        <?php if ( ( $post_type == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
	                                   ($post_type == 'tf_tours' && empty( $disable_child_search )) ||
	                                   ( $post_type == 'tf_apartment' && empty( $disable_apartment_child_search ) )
	                        ) { ?>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php esc_html_e( "Children", "tourfic" ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13094)">
                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13094">
                                                    <rect width="20" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <input type="tel" name="childrens" id="children" min="0" value="0" readonly>
                                    <div class="acr-inc">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13100)">
                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13100">
                                                    <rect width="20" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
							<?php if ( $post_type == 'tf_hotel' ) { ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php esc_html_e( "Rooms", "tourfic" ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <input type="tel" name="room" id="room" min="1" value="1" readonly>
                                        <div class="acr-inc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tf-booking-form-submit">
                <input type="hidden" name="type" value="<?php echo esc_attr( $post_type ); ?>" class="tf-post-type"/>
                <button class="tf-btn-normal btn-primary tf-submit"><?php echo esc_html__( 'Check Availability', 'tourfic' ); ?></button>
            </div>

			<?php if ( $post_type == 'tf_tours' ) { ?>
                <script>
                    (function ($) {
                        $(document).ready(function () {
                            // flatpickr locale first day of Week
							<?php self::tf_flatpickr_locale( "root" ); ?>

                            $(".tf-template-3 .tf-booking-date-wrap").on("click", function () {

                                $("#check-in-out-date").trigger("click");
                            });
                            $("#check-in-out-date").flatpickr({
                                enableTime: false,
                                mode: "range",
                                dateFormat: "Y/m/d",
                                minDate: "today",

                                // flatpickr locale
								<?php self::tf_flatpickr_locale(); ?>

                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                        return `${date1} - ${date2}`;
                                    });
                                    dateSetToFields(selectedDates, instance);
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                        return `${date1} - ${date2}`;
                                    });
                                    dateSetToFields(selectedDates, instance);
                                },
								<?php
								if(! empty( $check_in_out )){ ?>
                                defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
								<?php } ?>
                            });

                            function dateSetToFields(selectedDates, instance) {
                                if (selectedDates.length === 2) {
                                    const monthNames = [
                                        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                    ];
                                    if (selectedDates[0]) {
                                        const startDate = selectedDates[0];
                                        $(".tf-template-3 .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-date").html(startDate.getDate());
                                        $(".tf-template-3 .tf-booking-form-checkin .tf-tour-start-date span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                                    }
                                    if (selectedDates[1]) {
                                        const endDate = selectedDates[1];
                                        $(".tf-template-3 .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-date").html(endDate.getDate());
                                        $(".tf-template-3 .tf-booking-form-checkin .tf-tour-end-date span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                                    }
                                }
                            }

                        });
                    })(jQuery);
                </script>
			<?php } ?>

			<?php if ( $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ) { ?>
                <script>
                    (function ($) {
                        $(document).ready(function () {
                            const regexMap = {
                                'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                                'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                                'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                                'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                                'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                            };
                            const dateRegex = regexMap['<?php echo $date_format_for_users; ?>'];

                            // flatpickr locale first day of Week
							<?php self::tf_flatpickr_locale( "root" ); ?>

                            $(".tf-template-3 .tf-booking-date-wrap").on("click", function () {

                                $("#check-in-out-date").trigger("click");
                            });
                            $("#check-in-out-date").flatpickr({
                                enableTime: false,
                                mode: "range",
                                dateFormat: "Y/m/d",
                                minDate: "today",

                                // flatpickr locale
								<?php self::tf_flatpickr_locale(); ?>

                                onReady: function (selectedDates, dateStr, instance) {
                                        instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                        return `${date1} - ${date2}`;
                                    });
                                    dateSetToFields(selectedDates, instance);
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                        return `${date1} - ${date2}`;
                                    });
                                    dateSetToFields(selectedDates, instance);
                                },
								<?php
								if(! empty( $check_in_out )){ ?>
                                defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
								<?php } ?>
                            });

                            function dateSetToFields(selectedDates, instance) {
                                if (selectedDates.length === 2) {
                                    const monthNames = [
                                        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                                    ];
                                    if (selectedDates[0]) {
                                        const startDate = selectedDates[0];
                                        $(".tf-template-3 .tf-booking-form-checkin span.tf-booking-date").html(startDate.getDate());
                                        $(".tf-template-3 .tf-booking-form-checkin span.tf-booking-month span").html(monthNames[startDate.getMonth()]);
                                    }
                                    if (selectedDates[1]) {
                                        const endDate = selectedDates[1];
                                        $(".tf-template-3 .tf-booking-form-checkout span.tf-booking-date").html(endDate.getDate());
                                        $(".tf-template-3 .tf-booking-form-checkout span.tf-booking-month span").html(monthNames[endDate.getMonth()]);
                                    }
                                }
                            }

                        });
                    })(jQuery);
                </script>
		<?php }
		} elseif ( $post_type == 'tf_carrental' && $tf_car_arc_selected_template == "design-1" ) { ?>
		<div class="tf-archive-search-box">
			<div class="tf-archive-search-box-wrapper">
				<div class="tf-date-select-box tf-flex tf-flex-gap-8">
					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn tf-pick-drop-location <?php echo !empty($_GET['same_location']) && 'on'==$_GET['same_location'] ? esc_attr('active') : ''; ?>">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_257_3711)">
                                        <path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_257_3711">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Pick-up", "tourfic"); ?></h5>
									<input type="text" placeholder="Pick Up Location" id="tf_pickup_location" value="<?php echo !empty($_GET['pickup-name']) ? esc_html($_GET['pickup-name']) : '' ?>" />
									<input type="hidden" id="tf_pickup_location_id" value="<?php echo !empty($_GET['pickup']) ? esc_html($_GET['pickup']) : '' ?>" />
								</div>
							</div>
						</div>

						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_257_3711)">
                                        <path d="M7.36246 11.6666H4.16663C3.99707 11.6759 3.83438 11.7367 3.70034 11.8409C3.56631 11.9452 3.46732 12.0879 3.41663 12.25L1.74996 17.25C1.66663 17.3333 1.66663 17.4166 1.66663 17.5C1.66663 18 1.99996 18.3333 2.49996 18.3333H17.5C18 18.3333 18.3333 18 18.3333 17.5C18.3333 17.4166 18.3333 17.3333 18.25 17.25L16.5833 12.25C16.5326 12.0879 16.4336 11.9452 16.2996 11.8409C16.1655 11.7367 16.0028 11.6759 15.8333 11.6666H12.6375M15 6.66663C15 10.4166 9.99996 14.1666 9.99996 14.1666C9.99996 14.1666 4.99996 10.4166 4.99996 6.66663C4.99996 5.34054 5.52674 4.06877 6.46442 3.13109C7.40211 2.19341 8.67388 1.66663 9.99996 1.66663C11.326 1.66663 12.5978 2.19341 13.5355 3.13109C14.4732 4.06877 15 5.34054 15 6.66663ZM11.6666 6.66663C11.6666 7.5871 10.9204 8.33329 9.99996 8.33329C9.07948 8.33329 8.33329 7.5871 8.33329 6.66663C8.33329 5.74615 9.07948 4.99996 9.99996 4.99996C10.9204 4.99996 11.6666 5.74615 11.6666 6.66663Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_257_3711">
                                        <rect width="20" height="20" fill="white"/>
                                        </clipPath>
                                    </defs>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Drop-off", "tourfic"); ?></h5>
									<input type="text" placeholder="Drop Off Location" id="tf_dropoff_location" value="<?php echo !empty($_GET['dropoff-name']) ? esc_html($_GET['dropoff-name']) : '' ?>" />
									<input type="hidden" id="tf_dropoff_location_id" value="<?php echo !empty($_GET['dropoff']) ? esc_html($_GET['dropoff']) : '' ?>" />
								</div>
							</div>
						</div>
					</div>

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Pick-up date", "tourfic"); ?></h5>
									<input type="text" placeholder="Pick Up Date" class="tf_pickup_date" value="<?php echo !empty($_GET['pickup-date']) ? esc_html($_GET['pickup-date']) : '' ?>" />
								</div>
							</div>
						</div>

						<div class="tf-select-date">
								<div class="tf-flex tf-flex-gap-4">
									<div class="icon">
									    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_257_3728)">
                                            <path d="M9.99996 4.99996V9.99996L13.3333 11.6666M18.3333 9.99996C18.3333 14.6023 14.6023 18.3333 9.99996 18.3333C5.39759 18.3333 1.66663 14.6023 1.66663 9.99996C1.66663 5.39759 5.39759 1.66663 9.99996 1.66663C14.6023 1.66663 18.3333 5.39759 18.3333 9.99996Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_257_3728">
                                            <rect width="20" height="20" fill="white"/>
                                            </clipPath>
                                        </defs>
                                        </svg>
								    </div>
								<div class="info-select">
									<h5><?php esc_html_e("Time", "tourfic"); ?></h5>
									<input type="text" placeholder="Pick Up Time" class="tf_pickup_time" value="<?php echo !empty($_GET['pickup-time']) ? esc_html($_GET['pickup-time']) : '' ?>" />
								</div>
							</div>
						</div>
					</div>

					<div class="tf-date-single-select tf-flex tf-flex-gap-8 tf-flex-space-bttn">
						<div class="tf-select-date">
							<div class="tf-flex tf-flex-gap-4">
								<div class="icon">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.66667 1.66663V4.99996M13.3333 1.66663V4.99996M2.5 8.33329H17.5M6.66667 11.6666H6.675M10 11.6666H10.0083M13.3333 11.6666H13.3417M6.66667 15H6.675M10 15H10.0083M13.3333 15H13.3417M4.16667 3.33329H15.8333C16.7538 3.33329 17.5 4.07948 17.5 4.99996V16.6666C17.5 17.5871 16.7538 18.3333 15.8333 18.3333H4.16667C3.24619 18.3333 2.5 17.5871 2.5 16.6666V4.99996C2.5 4.07948 3.24619 3.33329 4.16667 3.33329Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
								</div>
								<div class="info-select">
									<h5><?php esc_html_e("Drop-off date", "tourfic"); ?></h5>
									<input type="text" placeholder="Drop Off Date" class="tf_dropoff_date" value="<?php echo !empty($_GET['dropoff-date']) ? esc_html($_GET['dropoff-date']) : '' ?>" />
								</div>
							</div>
						</div>

						<div class="tf-select-date">
								<div class="tf-flex tf-flex-gap-4">
									<div class="icon">
									    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_257_3728)">
                                            <path d="M9.99996 4.99996V9.99996L13.3333 11.6666M18.3333 9.99996C18.3333 14.6023 14.6023 18.3333 9.99996 18.3333C5.39759 18.3333 1.66663 14.6023 1.66663 9.99996C1.66663 5.39759 5.39759 1.66663 9.99996 1.66663C14.6023 1.66663 18.3333 5.39759 18.3333 9.99996Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_257_3728">
                                            <rect width="20" height="20" fill="white"/>
                                            </clipPath>
                                        </defs>
                                        </svg>
								    </div>
								<div class="info-select">
									<h5><?php esc_html_e("Time", "tourfic"); ?></h5>
									<input type="text" placeholder="Drop Off Time" class="tf_dropoff_time" value="<?php echo !empty($_GET['dropoff-time']) ? esc_html($_GET['dropoff-time']) : '' ?>" />
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="tf-driver-location-box tf-flex tf-flex-space-bttn tf-flex-align-center">
					<div class="tf-driver-location">
                        <?php
                        $car_driver_min_age      = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['car_archive_driver_min_age'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['car_archive_driver_min_age'] : 18;

                        $car_driver_max_age      = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['car_archive_driver_max_age'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['car_archive_driver_max_age'] : 40;

                        ?>
						<ul>
							<li>
								<label><?php esc_html_e("Return in the same location", "tourfic"); ?>
									<input type="checkbox" name="same_location" <?php echo !empty($_GET['same_location']) && 'on'==$_GET['same_location'] ? esc_attr('checked') : ''; ?>>
									<span class="checkmark"></span>
								</label>
							</li>
							<li>
								<label><?php esc_html_e("Age of driver ", "tourfic"); ?>
                                <?php echo esc_attr($car_driver_min_age); ?>-<?php echo esc_attr($car_driver_max_age); ?>?
									<input type="checkbox" name="driver_age" <?php echo !empty($_GET['driver_age']) && 'on'==$_GET['driver_age'] ? esc_attr('checked') : ''; ?>>
									<span class="checkmark"></span>
								</label>
							</li>
						</ul>
					</div>
					<div class="tf-submit-button">
						<input type="hidden" class="tf-post-type" value="<?php echo esc_attr("tf_carrental"); ?>">
						<button class="tf-filter-cars"><?php esc_html_e("Search", "tourfic"); ?> <i class="ri-search-line"></i></button>
					</div>

					<script>
						(function ($) {
							$(document).ready(function () {

								// flatpickr locale first day of Week
								<?php self::tf_flatpickr_locale('root'); ?>

								// Initialize the pickup date picker
								var pickupFlatpickr = $(".tf_pickup_date").flatpickr({
									enableTime: false,
									dateFormat: "Y/m/d",
									minDate: "today",
                                    disableMobile: "true",

									// flatpickr locale
									<?php self::tf_flatpickr_locale(); ?>

									onReady: function (selectedDates, dateStr, instance) {
										instance.element.value = dateStr.replace(/[a-z]+/g, '-');
									},
									onChange: function (selectedDates, dateStr, instance) {
										instance.element.value = dateStr.replace(/[a-z]+/g, '-');
										// Update minDate for the dropoff date picker
										dropoffFlatpickr.set("minDate", dateStr);
									}
								});

								// Initialize the dropoff date picker
								var dropoffFlatpickr = $(".tf_dropoff_date").flatpickr({
									enableTime: false,
									dateFormat: "Y/m/d",
									minDate: "today",
                                    disableMobile: "true",

									// flatpickr locale
									<?php self::tf_flatpickr_locale(); ?>

									onReady: function (selectedDates, dateStr, instance) {
										instance.element.value = dateStr.replace(/[a-z]+/g, '-');
									},
									onChange: function (selectedDates, dateStr, instance) {
										instance.element.value = dateStr.replace(/[a-z]+/g, '-');
									}
								});

								// Initialize the pickup time picker
								var pickupTimeFlatpickr = $(".tf_pickup_time").flatpickr({
									enableTime: true,
									noCalendar: true,
									dateFormat: "H:i",
                                    disableMobile: "true",

									// flatpickr locale
									<?php self::tf_flatpickr_locale(); ?>

									onReady: function (selectedDates, dateStr, instance) {
										instance.element.value = dateStr.replace(/[a-z]+/g, '-');
									},
									onChange: function (selectedDates, dateStr, instance) {
										instance.element.value = dateStr.replace(/[a-z]+/g, '-');
										// Update minDate for the dropoff date picker
										dropoffTimeFlatpickr.set("minTime", dateStr);
									}
								});

								var dropoffTimeFlatpickr = $(".tf_dropoff_time").flatpickr({
									enableTime: true,
									noCalendar: true,
									dateFormat: "H:i",
                                    disableMobile: "true",
									// flatpickr locale
									<?php self::tf_flatpickr_locale(); ?>

									onReady: function (selectedDates, dateStr, instance) {
										instance.element.value = dateStr.replace(/[a-z]+/g, '-');
									},
									onChange: function (selectedDates, dateStr, instance) {
										instance.element.value = dateStr.replace(/[a-z]+/g, '-');
										// Update minDate for the dropoff date picker
										dropoffFlatpickr.set("minDate", dateStr);
									}
								});

							});
						})(jQuery);

					</script>
				</div>
			</div>
		</div>
        <?php } elseif (
            ( is_post_type_archive( 'tf_hotel' ) && $tf_hotel_arc_selected_template == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ||
            ( is_post_type_archive( 'tf_tours' ) && $tf_tour_arc_selected_template == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ||
            ( is_post_type_archive( 'tf_apartment' ) && $tf_apartment_arc_selected_template == "design-2" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ||
            ( $post_type == 'tf_hotel' && $tf_hotel_arc_selected_template == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ||
            ( $post_type == 'tf_tours' && $tf_tour_arc_selected_template == "design-3" && function_exists( 'is_tf_pro' ) && is_tf_pro()) ||
            ( $post_type == 'tf_apartment' && $tf_apartment_arc_selected_template == "design-2" && function_exists( 'is_tf_pro' ) && is_tf_pro())
        ){ ?>
            <div class="tf-search-fields <?php echo $post_type == 'tf_tours' ? esc_attr( 'tf-tour-archive-block' ) : ''; ?>">
                <div class="tf-search-field">
                    <div class="tf-search-field-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                            <g clip-path="url(#clip0_1327_100053)">
                                <path d="M15.8033 14.97L10.5 20.2733L5.1967 14.97C2.26777 12.041 2.26777 7.29232 5.1967 4.36339C8.12563 1.43445 12.8743 1.43445 15.8033 4.36339C18.7323 7.29232 18.7323 12.041 15.8033 14.97ZM10.5 13C12.3409 13 13.8333 11.5076 13.8333 9.66669C13.8333 7.82574 12.3409 6.33335 10.5 6.33335C8.65905 6.33335 7.16667 7.82574 7.16667 9.66669C7.16667 11.5076 8.65905 13 10.5 13ZM10.5 11.3334C9.5795 11.3334 8.83333 10.5872 8.83333 9.66669C8.83333 8.74621 9.5795 8.00002 10.5 8.00002C11.4205 8.00002 12.1667 8.74621 12.1667 9.66669C12.1667 10.5872 11.4205 11.3334 10.5 11.3334Z"
                                      fill="#6E655E"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_1327_100053">
                                    <rect width="20" height="20" fill="white" transform="translate(0.5 0.5)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </div>
                    <label for="tf-location" class="tf-search-field-content">
                        <span class="tf-search-field-label"><?php echo $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ? esc_html__( 'Location', 'tourfic' ) : esc_html__( 'Destination', 'tourfic' ); ?></span>

                        <input type="text" required="" id="<?php echo esc_attr($place); ?>" class="tf-search-input" placeholder="<?php echo $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ? esc_html__( 'Enter Location', 'tourfic' ) : esc_html__( 'Where are you going?', 'tourfic' ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr($taxonomy_name) : ''; ?>">
                        <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? esc_attr($taxonomy_slug) : ''; ?>"/>
                    </label>
                </div>
                <div class="tf-search-field-divider"></div>
				<?php if ( $post_type == 'tf_hotel' || $post_type == 'tf_apartment' ) { ?>
                    <div class="tf-search-field-checkinout">
                        <div class="tf-search-field tf-search-field-checkin">
                            <div class="tf-search-field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M14.668 2.99992H18.0013C18.4616 2.99992 18.8346 3.37302 18.8346 3.83325V17.1666C18.8346 17.6268 18.4616 17.9999 18.0013 17.9999H3.0013C2.54107 17.9999 2.16797 17.6268 2.16797 17.1666V3.83325C2.16797 3.37302 2.54107 2.99992 3.0013 2.99992H6.33464V1.33325H8.0013V2.99992H13.0013V1.33325H14.668V2.99992ZM3.83464 7.99992V16.3333H17.168V7.99992H3.83464ZM5.5013 9.66658H7.16797V11.3333H5.5013V9.66658ZM5.5013 12.9999H7.16797V14.6666H5.5013V12.9999ZM8.83464 9.66658H15.5013V11.3333H8.83464V9.66658ZM8.83464 12.9999H13.0013V14.6666H8.83464V12.9999Z"
                                          fill="#6E655E"/>
                                </svg>
                            </div>
                            <label class="tf-search-field-content" for='tf-check-out'>
                                <span class="tf-search-field-label"><?php esc_html_e( "Check in", "tourfic" ); ?></span>
                                <input type="text" class="tf-search-input" name="tf-check-in" id="tf-check-in" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" value="" readonly>
                                <input type="text" class="tf-search-input" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" value="">
                            </label>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                            <path d="M11.2824 7.83327L7.70637 4.25726L8.64917 3.31445L13.8346 8.49993L8.64917 13.6853L7.70637 12.7425L11.2824 9.1666H3.16797V7.83327H11.2824Z" fill="#6E655E"/>
                        </svg>
                        <div class="tf-search-field tf-search-field-checkout">
                            <div class="tf-search-field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M14.668 2.99992H18.0013C18.4616 2.99992 18.8346 3.37302 18.8346 3.83325V17.1666C18.8346 17.6268 18.4616 17.9999 18.0013 17.9999H3.0013C2.54107 17.9999 2.16797 17.6268 2.16797 17.1666V3.83325C2.16797 3.37302 2.54107 2.99992 3.0013 2.99992H6.33464V1.33325H8.0013V2.99992H13.0013V1.33325H14.668V2.99992ZM3.83464 7.99992V16.3333H17.168V7.99992H3.83464ZM5.5013 9.66658H7.16797V11.3333H5.5013V9.66658ZM5.5013 12.9999H7.16797V14.6666H5.5013V12.9999ZM8.83464 9.66658H15.5013V11.3333H8.83464V9.66658ZM8.83464 12.9999H13.0013V14.6666H8.83464V12.9999Z"
                                          fill="#6E655E"/>
                                </svg>
                            </div>
                            <label class="tf-search-field-content" for='tf-check-out'>
                                <span class="tf-search-field-label"><?php esc_html_e( "Check out", "tourfic" ); ?></span>
                                <input type="text" class="tf-search-input" name="tf-check-out" id="tf-check-out" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" value="" readonly>
                            </label>
                        </div>
                    </div>
				<?php } ?>

				<?php if ( $post_type == 'tf_tours' ) { ?>
                    <div class="tf-search-field-checkinout">
                        <div class="tf-search-field">
                            <div class="tf-search-field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M14.668 2.99992H18.0013C18.4616 2.99992 18.8346 3.37302 18.8346 3.83325V17.1666C18.8346 17.6268 18.4616 17.9999 18.0013 17.9999H3.0013C2.54107 17.9999 2.16797 17.6268 2.16797 17.1666V3.83325C2.16797 3.37302 2.54107 2.99992 3.0013 2.99992H6.33464V1.33325H8.0013V2.99992H13.0013V1.33325H14.668V2.99992ZM3.83464 7.99992V16.3333H17.168V7.99992H3.83464ZM5.5013 9.66658H7.16797V11.3333H5.5013V9.66658ZM5.5013 12.9999H7.16797V14.6666H5.5013V12.9999ZM8.83464 9.66658H15.5013V11.3333H8.83464V9.66658ZM8.83464 12.9999H13.0013V14.6666H8.83464V12.9999Z"
                                          fill="#6E655E"/>
                                </svg>
                            </div>
                            <div class="tf-search-field-content">
                                <span class="tf-search-field-label"><?php esc_html_e( "Select Date", "tourfic" ); ?></span>
                                <input type="text" class="tf-search-input" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . esc_attr($check_in_out) . '"' : '' ?>>
                            </div>
                        </div>
                    </div>
				<?php } ?>
                <div class="tf-search-field-divider"></div>
                <div class="tf-search-guest-and-room">
					<?php if ( $post_type == 'tf_hotel' ) { ?>
                        <div class="tf-search-field">
                            <div class="tf-search-field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M10.5013 8.83341C12.3422 8.83341 13.8346 7.34103 13.8346 5.50008C13.8346 3.65913 12.3422 2.16675 10.5013 2.16675C8.66035 2.16675 7.16797 3.65913 7.16797 5.50008C7.16797 7.34103 8.66035 8.83341 10.5013 8.83341ZM5.08464 11.3334C6.23523 11.3334 7.16797 10.4007 7.16797 9.25008C7.16797 8.09949 6.23523 7.16675 5.08464 7.16675C3.93404 7.16675 3.0013 8.09949 3.0013 9.25008C3.0013 10.4007 3.93404 11.3334 5.08464 11.3334ZM18.0013 9.25008C18.0013 10.4007 17.0686 11.3334 15.918 11.3334C14.7674 11.3334 13.8346 10.4007 13.8346 9.25008C13.8346 8.09949 14.7674 7.16675 15.918 7.16675C17.0686 7.16675 18.0013 8.09949 18.0013 9.25008ZM10.5013 9.66675C12.8025 9.66675 14.668 11.5322 14.668 13.8334V18.8334H6.33464V13.8334C6.33464 11.5322 8.20012 9.66675 10.5013 9.66675ZM4.66797 13.8333C4.66797 13.2559 4.75186 12.6981 4.90812 12.1714L4.76684 12.1837C3.30549 12.3421 2.16797 13.5799 2.16797 15.0833V18.8333H4.66797V13.8333ZM18.8346 18.8333V15.0833C18.8346 13.5316 17.6229 12.2628 16.0945 12.1714C16.2507 12.6981 16.3346 13.2559 16.3346 13.8333V18.8333H18.8346Z"
                                          fill="#6E655E"/>
                                </svg>
                            </div>
                            <div class="tf-search-field-content">
                                <span class="tf-search-field-label"><?php esc_html_e( "Guests & rooms", "tourfic" ); ?></span>
                                <div class="tf-archive-guest-info">
                                    <span class="tf-guest"><?php esc_html_e( "01", "tourfic" ); ?></span> <?php esc_html_e( "guest", "tourfic" ); ?>
                                    <span class="tf-room"><?php esc_html_e( "01", "tourfic" ); ?></span> <?php esc_html_e( "rooms", "tourfic" ); ?>
                                </div>
                            </div>
                        </div>
					<?php } else { ?>
                        <div class="tf-search-field">
                            <div class="tf-search-field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                    <path d="M10.5013 8.83341C12.3422 8.83341 13.8346 7.34103 13.8346 5.50008C13.8346 3.65913 12.3422 2.16675 10.5013 2.16675C8.66035 2.16675 7.16797 3.65913 7.16797 5.50008C7.16797 7.34103 8.66035 8.83341 10.5013 8.83341ZM5.08464 11.3334C6.23523 11.3334 7.16797 10.4007 7.16797 9.25008C7.16797 8.09949 6.23523 7.16675 5.08464 7.16675C3.93404 7.16675 3.0013 8.09949 3.0013 9.25008C3.0013 10.4007 3.93404 11.3334 5.08464 11.3334ZM18.0013 9.25008C18.0013 10.4007 17.0686 11.3334 15.918 11.3334C14.7674 11.3334 13.8346 10.4007 13.8346 9.25008C13.8346 8.09949 14.7674 7.16675 15.918 7.16675C17.0686 7.16675 18.0013 8.09949 18.0013 9.25008ZM10.5013 9.66675C12.8025 9.66675 14.668 11.5322 14.668 13.8334V18.8334H6.33464V13.8334C6.33464 11.5322 8.20012 9.66675 10.5013 9.66675ZM4.66797 13.8333C4.66797 13.2559 4.75186 12.6981 4.90812 12.1714L4.76684 12.1837C3.30549 12.3421 2.16797 13.5799 2.16797 15.0833V18.8333H4.66797V13.8333ZM18.8346 18.8333V15.0833C18.8346 13.5316 17.6229 12.2628 16.0945 12.1714C16.2507 12.6981 16.3346 13.2559 16.3346 13.8333V18.8333H18.8346Z"
                                          fill="#6E655E"/>
                                </svg>
                            </div>

                            <div class="tf-search-field-content">
                                <span class="tf-search-field-label"><?php esc_html_e( "Persons", "tourfic" ); ?></span>
                                <div class="tf-archive-guest-info">
                                    <span class="tf-adult"><?php esc_html_e( "1", "tourfic" ); ?></span> <?php esc_html_e( "adult", "tourfic" ); ?>
	                                <?php if ( ($post_type == 'tf_tours' && empty( $disable_child_search )) ||
	                                           ( $post_type == 'tf_apartment' && empty( $disable_apartment_child_search ) )
	                                ) { ?>
                                    , <span class="tf-children"><?php esc_html_e( "0", "tourfic" ); ?></span> <?php esc_html_e( "children", "tourfic" ); ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
					<?php } ?>

                    <div class="tf_acrselection-wrap">
                        <div class="tf_acrselection-inner">
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php esc_html_e( "Adults", "tourfic" ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13094)">
                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13094">
                                                    <rect width="20" height="20" fill="white"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <input type="tel" name="adults" id="adults" min="1" value="1" readonly>
                                    <div class="acr-inc">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13100)">
                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13100">
                                                    <rect width="20" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                            </div>
	                        <?php if ( ( $post_type == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
	                                   ($post_type == 'tf_tours' && empty( $disable_child_search )) ||
	                                   ( $post_type == 'tf_apartment' && empty( $disable_apartment_child_search ) )
	                        ) { ?>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php esc_html_e( "Children", "tourfic" ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13094)">
                                                <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13094">
                                                    <rect width="20" height="20" fill="white"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <input type="tel" name="childrens" id="children" min="0" value="0" readonly>
                                    <div class="acr-inc">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <g clip-path="url(#clip0_3229_13100)">
                                                <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_3229_13100">
                                                    <rect width="20" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
							<?php if ( $post_type == 'tf_hotel' ) { ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php esc_html_e( "Rooms", "tourfic" ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13094)">
                                                    <rect x="4.16602" y="9.16675" width="11.6667" height="1.66667" fill="#595349"></rect>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13094">
                                                        <rect width="20" height="20" fill="white"></rect>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                        <input type="tel" name="room" id="room" min="1" value="1" readonly>
                                        <div class="acr-inc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <g clip-path="url(#clip0_3229_13100)">
                                                    <path d="M9.16602 9.16675V4.16675H10.8327V9.16675H15.8327V10.8334H10.8327V15.8334H9.16602V10.8334H4.16602V9.16675H9.16602Z" fill="#595349"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_3229_13100">
                                                        <rect width="20" height="20" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tf-booking-form-submit">
                <input type="hidden" name="type" value="<?php echo esc_attr($post_type); ?>" class="tf-post-type"/>
                <button class="tf-btn-normal btn-primary tf-submit"><?php esc_html_e( 'Search Now', 'tourfic' ); ?></button>
            </div>

            <?php if ( $post_type == 'tf_hotel' || $post_type == 'tf_tours' || $post_type == 'tf_apartment' ) : ?>
                <script>
                    (function ($) {
                        $(document).ready(function () {
                            // flatpickr locale first day of Week
                            <?php self::tf_flatpickr_locale( "root" ); ?>

                            $(".tf-hotel-template-4 #tf-check-out").on('click', function () {
                                $(".tf-search-input.form-control").click();
                            });

                            $("#check-in-out-date").flatpickr({
                                enableTime: false,
                                mode: "range",
                                dateFormat: "Y/m/d",
                                minDate: "today",
                                altInput: true,
                                altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',
                                showMonths: $(window).width() >= 1240 ? 2 : 1,

                                // flatpickr locale
                                <?php self::tf_flatpickr_locale(); ?>

                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    dateSetToFields(selectedDates, instance);
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                                    dateSetToFields(selectedDates, instance);
                                },
                                <?php if(! empty( $check_in_out )){ ?>
                                defaultDate: <?php echo wp_json_encode( explode( '-', $check_in_out ) ) ?>,
                                <?php } ?>
                            });

                            function dateSetToFields(selectedDates, instance) {
                                const format = '<?php echo esc_html( $date_format_for_users ); ?>';
                                if (selectedDates.length === 2) {
                                    if (selectedDates[0]) {
                                        let checkInDate = instance.formatDate(selectedDates[0], format);
                                        $(".tf-hotel-template-4 #tf-check-in").val(checkInDate);
                                    }

                                    if (selectedDates[1]) {
                                        let checkOutDate = instance.formatDate(selectedDates[1], format);
                                        $(".tf-hotel-template-4 #tf-check-out").val(checkOutDate);
                                    }
                                }
                            }

                        });
                    })(jQuery);
                </script>
            <?php endif; ?>

		<?php } else { ?>
            <form class="tf_archive_search_result tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
                  action="<?php echo esc_url( self::tf_booking_search_action() ); ?>">

                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner" <?php echo ( $post_type == 'tf_hotel' && self::tfopt( "hide_hotel_location_search" ) == 1 && self::tfopt( "required_location_hotel_search" ) != 1 ) || ( $post_type == 'tf_tours' && self::tfopt( "hide_tour_location_search" ) == 1 && self::tfopt( "required_location_tour_search" ) != 1 ) ? 'style="display:none"' : '' ?>>
                            <i class="fas fa-map-marker-alt"></i>

							<?php if ( is_post_type_archive( "tf_hotel" ) ) { ?>
                                <input type="text" <?php echo $hotel_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class=""
                                       placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
							<?php } elseif ( is_post_type_archive( "tf_tours" ) ) { ?>
                                <input type="text" <?php echo $tour_location_field_required == 1 ? 'required=""' : '' ?> id="<?php echo esc_attr( $place ); ?>" class=""
                                       placeholder="<?php echo esc_attr( $place_text ); ?>" value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
							<?php } else { ?>
                                <input type="text" required="" id="<?php echo esc_attr( $place ); ?>" class="" placeholder="<?php echo esc_attr( $place_text ); ?>"
                                       value="<?php echo ! empty( $taxonomy_name ) ? esc_attr( $taxonomy_name ) : ''; ?>">
							<?php } ?>

                            <input type="hidden" id="tf-place" name="place" value="<?php echo ! empty( $taxonomy_slug ) ? esc_attr( $taxonomy_slug ) : ''; ?>"/>
                        </div>
                    </label>
                </div>

                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="fas fa-user-friends"></i>
                            <select name="adults" id="adults" class="">
								<?php
								echo '<option value="1">1 ' . esc_html__( "Adult", "tourfic" ) . '</option>';
								foreach ( range( 2, 8 ) as $value ) {
									echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Adults", "tourfic" ) . '</option>';
								}
								?>
                            </select>
                        </div>
                    </label>
                </div>

	            <?php if ( ( $post_type == 'tf_hotel' && empty( $disable_hotel_child_search ) ) ||
	                       ($post_type == 'tf_tours' && empty( $disable_child_search )) ||
	                       ( $post_type == 'tf_apartment' && empty( $disable_apartment_child_search ) )
	            ) { ?>
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner">
                            <i class="fas fa-child"></i>
                            <select name="children" id="children" class="">
								<?php
								echo '<option value="0">0 ' . esc_html__( "Children", "tourfic" ) . '</option>';
								foreach ( range( 1, 8 ) as $value ) {
									echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Children", "tourfic" ) . '</option>';
								}
								?>
                            </select>
                        </div>
                    </label>
                </div>
                <?php } ?>

				<?php if ( $post_type == 'tf_apartment' ): ?>
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <i class="fas fa-child"></i>
                                <select name="infant" id="infant" class="">
                                    <option value="0">0 <?php esc_html_e( 'Infant', 'tourfic' ); ?></option>
									<?php foreach ( range( 1, 8 ) as $value ) {
										echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Infant", "tourfic" ) . '</option>';
									} ?>

                                </select>
                            </div>
                        </label>
                    </div>
				<?php endif; ?>

				<?php if ( $post_type == 'tf_hotel' ) { ?>
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <i class="fas fa-couch"></i>
                                <select name="room" id="room" class="">
									<?php
									echo '<option value="1">1 ' . esc_html__( "Room", "tourfic" ) . '</option>';
									foreach ( range( 2, 8 ) as $value ) {
										echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $value ) . ' ' . esc_html__( "Rooms", "tourfic" ) . '</option>';
									}
									?>
                                </select>
                            </div>
                        </label>
                    </div>
				<?php } ?>
                <div class="tf_booking-dates">
                    <div class="tf_form-row">
                        <label class="tf_label-row">
                            <div class="tf_form-inner">
                                <i class="far fa-calendar-alt"></i>
                                <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                       placeholder="<?php esc_html_e( 'Select Date', 'tourfic' ); ?>" required value="">
                            </div>
                        </label>
                    </div>
                </div>

                <div class="tf_form-row">
                    <input type="hidden" name="type" value="<?php echo esc_attr( $post_type ); ?>" class="tf-post-type"/>
                    <button class="tf_button tf-submit btn-styled"
                            type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' ); ?></button>
                </div>

            </form>

            <script>
                (function ($) {
                    $(document).ready(function () {
						<?php self::tf_flatpickr_locale( 'root' ); ?>

                        $(document).on("focus", ".tf-hotel-side-booking #check-in-out-date", function (e) {
                            const regexMap = {
                                'Y/m/d': /(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/,
                                'd/m/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                'm/d/Y': /(\d{2}\/\d{2}\/\d{4}).*(\d{2}\/\d{2}\/\d{4})/,
                                'Y-m-d': /(\d{4}-\d{2}-\d{2}).*(\d{4}-\d{2}-\d{2})/,
                                'd-m-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                'm-d-Y': /(\d{2}-\d{2}-\d{4}).*(\d{2}-\d{2}-\d{4})/,
                                'Y.m.d': /(\d{4}\.\d{2}\.\d{2}).*(\d{4}\.\d{2}\.\d{2})/,
                                'd.m.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/,
                                'm.d.Y': /(\d{2}\.\d{2}\.\d{4}).*(\d{2}\.\d{2}\.\d{4})/
                            };
                            const dateRegex = regexMap['<?php echo $date_format_for_users; ?>'];
                            let calander = flatpickr(this, {
                                enableTime: false,
                                minDate: "today",
                                mode: "range",
                                dateFormat: "Y/m/d",
                                altInput: true,
                                altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',

                                // flatpickr locale
								<?php self::tf_flatpickr_locale(); ?>

                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/(\d{4}\/\d{2}\/\d{2}).*(\d{4}\/\d{2}\/\d{2})/g, function (match, date1, date2) {
                                        return `${date1} - ${date2}`;
                                    })
                                    instance.altInput.value = instance.altInput.value.replace( dateRegex, function (match, d1, d2) {
                                        return `${d1} - ${d2}`;
                                    })
                                },
                            });
                        });
                    });
                })(jQuery);
            </script>

			<?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
                <div id="tf__booking_sidebar">
					<?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
                </div>
			<?php } ?>
		<?php } ?>
		<?php
	}

	static function tf_is_woo_active() {
		return is_plugin_active( 'woocommerce/woocommerce.php' );
	}

	static function tf_set_order( $order_data ) {
		global $wpdb;
		$all_order_ids = $wpdb->get_col( "SELECT order_id FROM {$wpdb->prefix}tf_order_data" );
		do {
			$order_id = wp_rand( 10000000, 99999999 );
		} while ( in_array( $order_id, $all_order_ids ) );

		$defaults = array(
			'order_id'         => $order_id,
			'post_id'          => 0,
			'post_type'        => '',
			'room_number'      => 0,
            'room_id'          => null,
			'check_in'         => '',
			'check_out'        => '',
			'billing_details'  => '',
			'shipping_details' => '',
			'order_details'    => '',
			'customer_id'      => 1,
			'payment_method'   => 'cod',
			'status'           => 'processing',
			'order_date'       => gmdate( 'Y-m-d H:i:s' ),
		);

		$order_data = wp_parse_args( $order_data, $defaults );

		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}tf_order_data
				( order_id, post_id, post_type, room_number, room_id, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %d, %s, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
				array(
					$order_data['order_id'],
					sanitize_key( $order_data['post_id'] ),
					$order_data['post_type'],
					$order_data['room_number'],
                    $order_data['room_id'] ? $order_data['room_id'] : null,
					$order_data['check_in'],
					$order_data['check_out'],
					wp_json_encode( $order_data['billing_details'] ),
					wp_json_encode( $order_data['shipping_details'] ),
					wp_json_encode( $order_data['order_details'] ),
					$order_data['customer_id'],
					$order_data['payment_method'],
					$order_data['status'],
					$order_data['order_date']
				)
			)
		);

        $lastid = $wpdb->insert_id;

        if($lastid > 0) {
            Hotel::tf_hotel_without_payment_inventory_data($order_id);
        }

		return $order_id;
	}

    static function tf_booking_search_action() {

        // get data from global settings else default
        $search_result_action = !empty( Helper::tfopt( 'search-result-page' ) ) ? get_permalink( Helper::tfopt( 'search-result-page' ) ) : home_url( '/search-result/' );

        // can be override by filter
        return apply_filters( 'tf_booking_search_action', $search_result_action );

    }

    static function tourfic_posts_navigation( $wp_query = '' ) {
        if ( empty( $wp_query ) ) {
            global $wp_query;
        }
        $max_num_pages = $wp_query->max_num_pages;
        $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
        if ( $max_num_pages > 1 ) {
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

    static function tf_flatpickr_locale( $placement = 0 ) {

		$flatpickr_locale     = ! empty( get_locale() ) ? get_locale() : 'en_US';
		$allowed_locale       = array( 'ar', 'bn_BD', 'de_DE', 'es_ES', 'fr_FR', 'hi_IN', 'it_IT', 'nl_NL', 'ru_RU', 'zh_CN' );
		$tf_first_day_of_week = ! empty( self::tfopt( "tf-week-day-flatpickr" ) ) ? self::tfopt( "tf-week-day-flatpickr" ) : 0;

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
		} else {
			$flatpickr_locale = 'default';
		}

		if ( ! empty( $placement ) && ! empty( $flatpickr_locale ) && $placement == "root" ) {

			echo esc_html( <<<EOD
				window.flatpickr.l10ns.$flatpickr_locale.firstDayOfWeek = $tf_first_day_of_week;
			EOD
			);

		} else {
			echo 'locale: "' . esc_html( $flatpickr_locale ) . '",';
		}
	}

    static function tf_get_deposit_amount( $room, $price, &$deposit_amount, &$has_deposit, $discount = 0 ) {
		$deposit_amount = null;
		if ( $discount > 0 ) {
			$price = $discount;
		}
		$has_deposit = ! empty( $room['allow_deposit'] ) && $room['allow_deposit'] == true;
		if ( $has_deposit == true ) {
			if ( $room['deposit_type'] == 'percent' ) {
				$deposit_amount = $price * ( intval( $room['deposit_amount'] ) / 100 );
			} else {
				$deposit_amount = $room['deposit_amount'];
			}
		}
	}

    static function tf_array_flatten( $array, $depth = INF ) {

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

	function redirect_non_admin_users() {
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

			$user = wp_get_current_user();

			if ( ! defined( 'DOING_AJAX' ) && (in_array( 'tf_vendor', (array) $user->roles ) || in_array( 'tf_manager', (array) $user->roles ) || in_array( 'customer', (array) $user->roles )) ) {
				$tf_dashboard_page_link = ! empty( get_option( 'tf_dashboard_page_id' ) ) ? get_permalink( get_option( 'tf_dashboard_page_id' ) ) : get_home_url();
				wp_redirect( $tf_dashboard_page_link );
				exit;
			} else {
				return;
			}
		}
	}

    function tf_filetype_and_ext_check_support($data, $file, $filename, $mimes, $real_mime) {
        if (!empty($data['ext']) && !empty($data['type'])) {
            return $data;
        }

        $wp_file_type = wp_check_filetype($filename, $mimes);

        // Add support for specific font file types
        if ('ttf' === $wp_file_type['ext']) {
            $data['ext'] = 'ttf';
            $data['type'] = 'font/ttf';
        }

        if ('otf' === $wp_file_type['ext']) {
            $data['ext'] = 'otf';
            $data['type'] = 'font/otf';
        }

        if ('woff' === $wp_file_type['ext']) {
            $data['ext'] = 'woff';
            $data['type'] = 'font/woff';
        }

        if ('woff2' === $wp_file_type['ext']) {
            $data['ext'] = 'woff2';
            $data['type'] = 'font/woff2';
        }

        if ('eot' === $wp_file_type['ext']) {
            $data['ext'] = 'eot';
            $data['type'] = 'application/vnd.ms-fontobject';
        }

        return $data;
    }

    function tf_upload_mimes_support($mimes) {
        $mimes['ttf'] = 'font/ttf';
        $mimes['otf'] = 'font/otf';
        $mimes['woff'] = 'font/woff';
        $mimes['woff2'] = 'font/woff2';
        $mimes['eot'] = 'application/vnd.ms-fontobject';
        return $mimes;
    }

	static function tf_var_dump( $var ) {
		echo '<pre>';
		var_dump( $var );
		echo '</pre>';
	}

    /*
     * Post edit link
     * @author Foysal
     */
	static function edit_link( $post_id ) {
		if ( current_user_can( 'administrator' ) && current_user_can( 'manage_options' ) ) {
			$edit_link = get_edit_post_link( $post_id );

			if ( $edit_link ) {
				return '<a href="' . esc_url( $edit_link ) . '" target="_blank" class="tf-edit-link">' . esc_html__( 'Edit', 'tourfic' ) . '</a>';
			}
		}
	}
}