<?php

namespace Tourfic\Classes;
defined( 'ABSPATH' ) || exit;

class Helper {
	use \Tourfic\Traits\Singleton;
	use \Tourfic\Traits\Helper;

	public function __construct() {
		add_filter( 'body_class', array( $this, 'tf_templates_body_class' ) );
		add_action( 'admin_footer', array( $this, 'tf_admin_footer' ) );

		add_filter( 'rest_prepare_taxonomy', array( $this, 'tf_remove_metabox_gutenburg' ), 10, 3 );
		add_action( "wp_ajax_tf_shortcode_type_to_location", array( $this, 'tf_shortcode_type_to_location_callback' ) );
		add_action( 'wp_ajax_tf_affiliate_active', array( $this, 'tf_affiliate_active_callback' ) );
		add_action( 'wp_ajax_tf_affiliate_install', array( $this, 'tf_affiliate_install_callback' ) );
		add_action( 'admin_init', array( $this, 'tf_update_email_template_default_content' ) );
		add_action( 'wp_ajax_tf_checkout_cart_item_remove', array( $this, 'tf_checkout_cart_item_remove' ) );
		add_action( 'wp_ajax_nopriv_tf_checkout_cart_item_remove', array( $this, 'tf_checkout_cart_item_remove' ) );
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'tf_remove_icon_add_to_order_item' ), 10, 3 );
		add_action( 'wp_ajax_tf_month_reports', array( $this, 'tf_month_chart_filter_callback' ) );
		add_action( 'wp_ajax_nopriv_tf_trigger_filter', array( $this, 'tf_search_result_ajax_sidebar' ) );
		add_action( 'wp_ajax_tf_trigger_filter', array( $this, 'tf_search_result_ajax_sidebar' ) );
		add_action( 'admin_init', array( $this, 'tf_admin_role_caps' ), 999 );
		add_filter( 'template_include', array( $this, 'taxonomy_template' ) );
		add_filter( 'comments_template', array( $this, 'load_comment_template' ) );
		add_filter( 'template_include', array( $this, 'tourfic_archive_page_template' ) );
		add_filter( 'single_template', array( $this, 'tf_single_page_template' ) );
		add_filter( 'after_setup_theme', array( $this, 'tf_image_sizes' ) );
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

	/**
	 * Template 3 Compatible to others Themes
	 *
	 * @since 2.10.8
	 */
	function tf_templates_body_class( $classes ) {

		$tf_tour_arc_selected_template      = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['tour-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
		$tf_hotel_arc_selected_template     = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['hotel-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
		$tf_apartment_arc_selected_template = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['apartment-archive'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['apartment-archive'] : 'default';
		$tf_hotel_global_template           = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['single-hotel'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';
		$tf_tour_global_template            = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['single-tour'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['single-tour'] : 'design-1';
		$tf_apartment_global_template       = ! empty( self::tf_data_types( self::tfopt( 'tf-template' ) )['single-apartment'] ) ? self::tf_data_types( self::tfopt( 'tf-template' ) )['single-apartment'] : 'default';

		if ( is_post_type_archive( 'tf_tours' ) || is_tax( 'tour_destination' ) ) {
			if ( 'design-2' == $tf_tour_arc_selected_template ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		if ( is_post_type_archive( 'tf_hotel' ) || is_tax( 'hotel_location' ) ) {
			if ( 'design-2' == $tf_hotel_arc_selected_template ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		if ( is_post_type_archive( 'tf_apartment' ) || is_tax( 'apartment_location' ) ) {
			if ( 'design-1' == $tf_apartment_arc_selected_template ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		if ( is_singular( 'tf_hotel' ) ) {
			$meta                       = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
			$tf_hotel_layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
			if ( "single" == $tf_hotel_layout_conditions ) {
				$tf_hotel_single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
			}
			$tf_hotel_selected_check = ! empty( $tf_hotel_single_template ) ? $tf_hotel_single_template : $tf_hotel_global_template;
			if ( 'design-2' == $tf_hotel_selected_check ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		if ( is_singular( 'tf_tours' ) ) {
			$meta                      = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
			$tf_tour_layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
			if ( "single" == $tf_tour_layout_conditions ) {
				$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
			}
			$tf_tour_selected_check = ! empty( $tf_tour_single_template ) ? $tf_tour_single_template : $tf_tour_global_template;
			if ( 'design-2' == $tf_tour_selected_check ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		if ( is_singular( 'tf_apartment' ) ) {
			$meta                          = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
			$tf_aprtment_layout_conditions = ! empty( $meta['tf_single_apartment_layout_opt'] ) ? $meta['tf_single_apartment_layout_opt'] : 'global';
			if ( "single" == $tf_aprtment_layout_conditions ) {
				$tf_apartment_single_template = ! empty( $meta['tf_single_apartment_template'] ) ? $meta['tf_single_apartment_template'] : 'default';
			}
			$tf_apartment_selected_check = ! empty( $tf_apartment_single_template ) ? $tf_apartment_single_template : $tf_apartment_global_template;
			if ( 'design-1' == $tf_apartment_selected_check ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		$tf_search_result_page_id = ! empty( self::tfopt( 'search-result-page' ) ) ? self::tfopt( 'search-result-page' ) : '';
		if ( ! empty( $tf_search_result_page_id ) ) {
			$tf_search_result_page_slug = get_post_field( 'post_name', $tf_search_result_page_id );
		}
		if ( ! empty( $tf_search_result_page_slug ) ) {
			$tf_current_page_id = get_post_field( 'post_name', get_the_ID() );
			if ( $tf_search_result_page_slug == $tf_current_page_id ) {
				$classes[] = 'tf_template_3_global_layouts';
			}
		}

		return $classes;
	}

	static function get_terms_dropdown( $taxonomy, $args = array() ) {
		$defaults = array(
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		);
		$args     = wp_parse_args( $args, $defaults );

		$terms = get_terms( $taxonomy, $args );

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

	function tf_admin_footer() {

		$screen = get_current_screen();

		if ( is_admin() && ( $screen->id == 'tf_hotel' ) ) {
			global $post;
			?>
            <script>
                var post_id = '<?php echo esc_html( $post->ID ); ?>';
            </script>
			<?php
		}
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
		);

		$allowed_tags['input'] = array(
			'type'        => true,
			'name'        => true,
			'value'       => true,
			'placeholder' => true,
			'class'       => true,
			'id'          => true,
		);

		$allowed_tags['select'] = array(
			'name'     => true,
			'class'    => true,
			'id'       => true,
			'data-*'   => true,
			'multiple' => true
		);

		$allowed_tags['option'] = array(
			'value' => true,
			'class' => true,
			'id'    => true,
		);

		$allowed_tags['textarea'] = array(
			'name'  => true,
			'rows'  => true,
			'cols'  => true,
			'class' => true,
			'id'    => true,
		);

		$allowed_tags['button'] = array(
			'type'  => true,
			'name'  => true,
			'class' => true,
			'id'    => true,
		);

		$allowed_tags['label'] = array(
			'for'   => true,
			'class' => true,
			'id'    => true,
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

	function tf_shortcode_type_to_location_callback() {
		//Nonce Verification
		check_ajax_referer( 'updates', '_nonce' );

		$term_name = $_POST['termName'] ? sanitize_text_field( $_POST['termName'] ) : 'tf_hotel';

		$terms = get_terms( array(
			'taxonomy'   => $term_name,
			'hide_empty' => false,
		) );

		wp_reset_postdata();

		wp_send_json_success( array(
			'value'    => $terms,
			"termName" => $term_name
		) );
	}

	function tourfic_character_limit_callback( $str, $limit, $dots = true ) {
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

	/*
     * Install and active Tourfic Affiliate
     */
	function tf_affiliate_install_callback() {
		$response = [
			'status'  => 'error',
			'message' => esc_html__( 'Something went wrong. Please try again.', 'tourfic' )
		];
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'tf_affiliate_install' ) ) {
			wp_send_json_error( $response );
		}
		if ( current_user_can( 'activate_plugins' ) ) {
			$plugin = 'tourfic-affiliate';
			$result = install_plugin_install_status( $plugin );
			if ( is_wp_error( $result ) ) {
				$response['message'] = $result->get_error_message();
			} else {
				$response['status']  = 'success';
				$response['message'] = esc_html__( 'Tourfic Affiliate installed successfully.', 'tourfic' );
			}
		}

		echo wp_json_encode( $response );
		die();
	}

	/*
     * Activate Tourfic Affiliate
     */
	function tf_affiliate_active_callback() {
		$response = [
			'status'  => 'error',
			'message' => esc_html__( 'Something went wrong. Please try again.', 'tourfic' )
		];
//    $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
//    if ( ! wp_verify_nonce( $nonce, 'tf_affiliate_active' ) ) {
//        wp_send_json_error( $response );
//    }
		if ( current_user_can( 'activate_plugins' ) ) {
			$plugin = 'tourfic-affiliate/tourfic-affiliate.php';
			$result = activate_plugin( $plugin );
			if ( is_wp_error( $result ) ) {
				$response['message'] = $result->get_error_message();
			} else {
				$response['status']  = 'success';
				$response['message'] = esc_html__( 'Tourfic Affiliate activated successfully.', 'tourfic' );
			}
		}

		echo wp_json_encode( $response );
		die();
	}

	/**
	 * Update options of email templates[admin,vendor, customer]
	 *
	 * @return void
	 *
	 * @since 2.9.19
	 * @author Abu Hena
	 */
	function tf_update_email_template_default_content() {

		$tf_settings = ! empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();
		if ( isset( $tf_settings['email-settings'] ) ) {
			$tf_settings = $tf_settings['email-settings'];

			if ( ! is_array( $tf_settings ) ) {
				return;
			}

			//update email template for admin
			if ( empty( $tf_settings['admin_booking_email_template'] ) ) {
				update_option( $tf_settings['admin_booking_email_template'], TF_Handle_Emails::get_email_template( 'order_confirmation', '', 'admin' ) );
			}
			//update email template for vendor
			if ( empty( $tf_settings['vendor_booking_email_template'] ) ) {
				if ( array_key_exists( 'vendor_booking_email_template', $tf_settings ) ) {
					update_option( $tf_settings['vendor_booking_email_template'], TF_Handle_Emails::get_email_template( 'order_confirmation', '', 'vendor' ) );
				}
			}
			//update email template for customer
			if ( empty( $tf_settings['customer_confirm_email_template'] ) ) {
				update_option( $tf_settings['customer_confirm_email_template'], TF_Handle_Emails::get_email_template( 'order_confirmation', '', 'customer' ) );
			}
		}
	}

	/**
	 * Remove cart item from checkout page
	 * @since 2.9.6
	 * @author Foysal
	 */
	function tf_checkout_cart_item_remove() {
		check_ajax_referer( 'tf_ajax_nonce', '_nonce' );

		if ( isset( $_POST['cart_item_key'] ) ) {
			$cart_item_key = sanitize_key( $_POST['cart_item_key'] );

			// Remove cart item
			WC()->cart->remove_cart_item( $cart_item_key );
		}

		die();
	}

	/**
	 * Remove icon add to order item
	 * @since 2.9.6
	 * @author Foysal
	 */
	function tf_remove_icon_add_to_order_item( $subtotal, $cart_item, $cart_item_key ) {
		if ( ! is_checkout() ) {
			return $subtotal;
		}
		$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
		?>
        <div class="tf-product-total">
			<?php echo wp_kses_post( $subtotal ); ?>
			<?php
			echo sprintf(
				'<a href="#" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">&times;</a>',
//		        esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
				esc_attr__( 'Remove this item', 'woocommerce' ),
				esc_attr( $product_id ),
				esc_attr( $cart_item_key ),
				esc_attr( $_product->get_sku() )
			);
			?>
        </div>
		<?php
	}

	/**
	 * Monthwise Chart Ajax function
	 *
	 * @author Jahid
	 */
	function tf_month_chart_filter_callback() {
		//Verify Nonce
		check_ajax_referer( 'updates', '_nonce' );

		$search_month = sanitize_key( $_POST['month'] );
		$search_year  = sanitize_key( $_POST['year'] );
		$month_dates  = cal_days_in_month( CAL_GREGORIAN, $search_month, $search_year );

		//Order Data Retrive
		$tf_old_order_limit = new WC_Order_Query( array(
			'limit'   => - 1,
			'orderby' => 'date',
			'order'   => 'ASC',
			'return'  => 'ids',
		) );
		$order              = $tf_old_order_limit->get_orders();
		$months_day_number  = [];
		for ( $i = 1; $i <= $month_dates; $i ++ ) {
			$months_day_number [] = $i;

			// Booking Month
			${"tf_co$i"} = 0;
			// Booking Cancel Month
			${"tf_cr$i"} = 0;
		}

		foreach ( $order as $item_id => $item ) {
			$itemmeta         = wc_get_order( $item );
			$tf_ordering_date = $itemmeta->get_date_created();
			for ( $i = 1; $i <= $month_dates; $i ++ ) {
				if ( $tf_ordering_date->date( 'n-j-y' ) == $search_month . '-' . $i . '-' . $search_year ) {
					if ( "completed" == $itemmeta->get_status() ) {
						${"tf_co$i"} += 1;
					}
					if ( "cancelled" == $itemmeta->get_status() || "refunded" == $itemmeta->get_status() ) {
						${"tf_cr$i"} += 1;
					}
				}
			}
		}
		$tf_complete_orders = [];
		$tf_cancel_orders   = [];
		for ( $i = 1; $i <= $month_dates; $i ++ ) {
			$tf_complete_orders [] = ${"tf_co$i"};
			$tf_cancel_orders []   = ${"tf_cr$i"};
		}

		$response['months_day_number']  = $months_day_number;
		$response['tf_complete_orders'] = $tf_complete_orders;
		$response['tf_cancel_orders']   = $tf_cancel_orders;
		$response['tf_search_month']    = gmdate( "F", strtotime( '2000-' . $search_month . '-01' ) );
		echo wp_json_encode( $response );

		die();
	}

	/**
	 * Search Result Sidebar check availability
	 *
	 * Hotel Filter by Feature
	 *
	 * Ajax function
	 */
	function tf_search_result_ajax_sidebar() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
			return;
		}
		/**
		 * Get form data
		 */
		$adults = ! empty( $_POST['adults'] ) ? sanitize_text_field( $_POST['adults'] ) : '';
		$child  = ! empty( $_POST['children'] ) ? sanitize_text_field( $_POST['children'] ) : '';
		$infant = ! empty( $_POST['infant'] ) && $_POST['infant'] != "undefined" ? sanitize_text_field( $_POST['infant'] ) : '';

		$room         = ! empty( $_POST['room'] ) ? sanitize_text_field( $_POST['room'] ) : '';
		$check_in_out = ! empty( $_POST['checked'] ) ? sanitize_text_field( $_POST['checked'] ) : '';

		$relation        = Helper::tfopt( 'search_relation', 'AND' );
		$filter_relation = Helper::tfopt( 'filter_relation', 'OR' );

		$search                = ( $_POST['dest'] ) ? sanitize_text_field( $_POST['dest'] ) : null;
		$filters               = ( $_POST['filters'] ) ? explode( ',', sanitize_text_field( $_POST['filters'] ) ) : null;
		$features              = ( $_POST['features'] ) ? explode( ',', sanitize_text_field( $_POST['features'] ) ) : null;
		$tf_hotel_types        = ( $_POST['tf_hotel_types'] ) ? explode( ',', sanitize_text_field( $_POST['tf_hotel_types'] ) ) : null;
		$tour_features         = ( $_POST['tour_features'] ) ? explode( ',', sanitize_text_field( $_POST['tour_features'] ) ) : null;
		$attractions           = ( $_POST['attractions'] ) ? explode( ',', sanitize_text_field( $_POST['attractions'] ) ) : null;
		$activities            = ( $_POST['activities'] ) ? explode( ',', sanitize_text_field( $_POST['activities'] ) ) : null;
		$tf_tour_types         = ( $_POST['tf_tour_types'] ) ? explode( ',', sanitize_text_field( $_POST['tf_tour_types'] ) ) : null;
		$tf_apartment_features = ( $_POST['tf_apartment_features'] ) ? explode( ',', sanitize_text_field( $_POST['tf_apartment_features'] ) ) : null;
		$tf_apartment_types    = ( $_POST['tf_apartment_types'] ) ? explode( ',', sanitize_text_field( $_POST['tf_apartment_types'] ) ) : null;
		$posttype              = $_POST['type'] ? sanitize_text_field( $_POST['type'] ) : 'tf_hotel';
		# Separate taxonomy input for filter query
		$place_taxonomy  = $posttype == 'tf_tours' ? 'tour_destination' : ( $posttype == 'tf_apartment' ? 'apartment_location' : 'hotel_location' );
		$filter_taxonomy = $posttype == 'tf_tours' ? 'null' : 'hotel_feature';
		# Take dates for filter query
		$checkin    = isset( $_POST['checkin'] ) ? trim( $_POST['checkin'] ) : array();
		$startprice = ! empty( $_POST['startprice'] ) ? $_POST['startprice'] : '';
		$endprice   = ! empty( $_POST['endprice'] ) ? $_POST['endprice'] : '';

		// Author Id if any
		$tf_author_ids = ! empty( $_POST['tf_author'] ) ? $_POST['tf_author'] : '';

		if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
			if ( $posttype == "tf_tours" ) {
				$data = array( $adults, $child, $check_in_out, $startprice, $endprice );
			} elseif ( $posttype == "tf_hotel" ) {
				$data = array( $adults, $child, $room, $check_in_out, $startprice, $endprice );
			} else {
				$data = array( $adults, $child, $infant, $check_in_out, $startprice, $endprice );
			}
		} else {
			if ( $posttype == "tf_tours" ) {
				$data = array( $adults, $child, $check_in_out );
			} elseif ( $posttype == "tf_hotel" ) {
				$data = array( $adults, $child, $room, $check_in_out );
			} else {
				$data = array( $adults, $child, $infant, $check_in_out );
			}
		}

		if ( ! empty( $check_in_out ) ) {
			list( $tf_form_start, $tf_form_end ) = explode( ' - ', $check_in_out );
		}

		if ( ! empty( $check_in_out ) ) {
			$period = new DatePeriod(
				new DateTime( $tf_form_start ),
				new DateInterval( 'P1D' ),
				new DateTime( ! empty( $tf_form_end ) ? $tf_form_end : $tf_form_start . '23:59' )
			);
		} else {
			$period = '';
		}
		if ( $check_in_out ) {
			$form_check_in      = substr( $check_in_out, 0, 10 );
			$form_check_in_stt  = strtotime( $form_check_in );
			$form_check_out     = substr( $check_in_out, 13, 10 );
			$form_check_out_stt = strtotime( $form_check_out );
		}

		$post_per_page = Helper::tfopt( 'posts_per_page' ) ? Helper::tfopt( 'posts_per_page' ) : 10;
		// $paged = !empty($_POST['page']) ? absint( $_POST['page'] ) : 1;
		// Properties args
		if ( $posttype == "tf_tours" ) {
			$tf_expired_tour_showing = ! empty( Helper::tfopt( 't-show-expire-tour' ) ) ? Helper::tfopt( 't-show-expire-tour' ) : '';
			if ( ! empty( $tf_expired_tour_showing ) ) {
				$tf_tour_posts_status = array( 'publish', 'expired' );
			} else {
				$tf_tour_posts_status = array( 'publish' );
			}

			$args = array(
				'post_type'      => $posttype,
				'post_status'    => $tf_tour_posts_status,
				'posts_per_page' => - 1,
				'author'         => $tf_author_ids,
			);
		} else {
			$args = array(
				'post_type'      => $posttype,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'author'         => $tf_author_ids,
			);
		}

		if ( $search ) {

			$args['tax_query'] = array(
				'relation' => 'AND',
				array(
					'taxonomy' => $place_taxonomy,
					'field'    => 'slug',
					'terms'    => sanitize_title( $search, '' ),
				),
			);
		}

		if ( $filters ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => $filter_taxonomy,
					'terms'    => $filters,
				);
			} else {
				$args['tax_query']['tf_filters']['relation'] = 'AND';

				foreach ( $filters as $key => $term_id ) {
					$args['tax_query']['tf_filters'][] = array(
						'taxonomy' => $filter_taxonomy,
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		//Query for the features filter of hotel
		if ( $features ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'tf_feature',
					'terms'    => $features,
				);
			} else {
				$args['tax_query']['tf_feature']['relation'] = 'AND';

				foreach ( $filters as $key => $term_id ) {
					$args['tax_query']['tf_feature'][] = array(
						'taxonomy' => 'tf_feature',
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		//Query for the types filter of hotel
		if ( $tf_hotel_types ) {

			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'hotel_type',
					'terms'    => $tf_hotel_types,
				);
			} else {
				$args['tax_query']['hotel_type']['relation'] = 'AND';

				foreach ( $tf_hotel_types as $key => $term_id ) {
					$args['tax_query']['hotel_type'][] = array(
						'taxonomy' => 'hotel_type',
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		//Query for the features filter of Tour
		if ( $tour_features ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'tour_features',
					'terms'    => $tour_features,
				);
			} else {
				$args['tax_query']['tour_features']['relation'] = 'AND';

				foreach ( $tour_features as $key => $term_id ) {
					$args['tax_query']['tour_features'][] = array(
						'taxonomy' => 'tour_features',
						'terms'    => array( $term_id ),
					);
				}

			}

		}

		//Query for the attractions filter of tours
		if ( $attractions ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'tour_attraction',
					'terms'    => $attractions,
				);
			} else {
				$args['tax_query']['tour_attraction']['relation'] = 'AND';

				foreach ( $attractions as $key => $term_id ) {
					$args['tax_query']['tour_attraction'][] = array(
						'taxonomy' => 'tour_attraction',
						'terms'    => array( $term_id ),
					);
				}

			}

		}

		//Query for the activities filter of tours
		if ( $activities ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'tour_activities',
					'terms'    => $activities,
				);
			} else {
				$args['tax_query']['tour_activities']['relation'] = 'AND';

				foreach ( $activities as $key => $term_id ) {
					$args['tax_query']['tour_activities'][] = array(
						'taxonomy' => 'tour_activities',
						'terms'    => array( $term_id ),
					);
				}

			}

		}

		//Query for the types filter of tours
		if ( $tf_tour_types ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'tour_type',
					'terms'    => $tf_tour_types,
				);
			} else {
				$args['tax_query']['tour_type']['relation'] = 'AND';

				foreach ( $tf_tour_types as $key => $term_id ) {
					$args['tax_query']['tour_type'][] = array(
						'taxonomy' => 'tour_type',
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		//Query for the features filter of apartments
		if ( $tf_apartment_features ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'apartment_feature',
					'terms'    => $tf_apartment_features,
				);
			} else {
				$args['tax_query']['apartment_feature']['relation'] = 'AND';

				foreach ( $tf_apartment_features as $key => $term_id ) {
					$args['tax_query']['apartment_feature'][] = array(
						'taxonomy' => 'apartment_feature',
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		//Query for the types filter of apartments
		if ( $tf_apartment_types ) {
			$args['tax_query']['relation'] = $relation;

			if ( $filter_relation == "OR" ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'apartment_type',
					'terms'    => $tf_apartment_types,
				);
			} else {
				$args['tax_query']['apartment_type']['relation'] = 'AND';

				foreach ( $tf_apartment_types as $key => $term_id ) {
					$args['tax_query']['apartment_type'][] = array(
						'taxonomy' => 'apartment_type',
						'terms'    => array( $term_id ),
					);
				}
			}
		}

		$loop = new WP_Query( $args );

		//get total posts count
		$total_posts = $loop->found_posts;
		if ( $loop->have_posts() ) {
			$not_found = [];
			while ( $loop->have_posts() ) {

				$loop->the_post();

				if ( $posttype == 'tf_hotel' ) {

					if ( empty( $check_in_out ) ) {
						tf_filter_hotel_without_date( $period, $not_found, $data );
					} else {
						tf_filter_hotel_by_date( $period, $not_found, $data );
					}

				} elseif ( $posttype == 'tf_tours' ) {
					if ( empty( $check_in_out ) ) {
						/**
						 * Check if minimum and maximum people limit matches with the search query
						 */
						$total_person = intval( $adults ) + intval( $child );
						$meta         = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

						//skip the tour if the search form total people  exceeds the maximum number of people in tour
						if ( ! empty( $meta['cont_max_people'] ) && $meta['cont_max_people'] < $total_person && $meta['cont_max_people'] != 0 ) {
							$total_posts --;
							continue;
						}

						//skip the tour if the search form total people less than the maximum number of people in tour
						if ( ! empty( $meta['cont_min_people'] ) && $meta['cont_min_people'] > $total_person && $meta['cont_min_people'] != 0 ) {
							$total_posts --;
							continue;
						}
						tf_filter_tour_by_without_date( $period, $total_posts, $not_found, $data );
					} else {
						tf_filter_tour_by_date( $period, $total_posts, $not_found, $data );
					}
				} else {
					if ( empty( $check_in_out ) ) {
						tf_filter_apartment_without_date( $period, $not_found, $data );
					} else {
						tf_filter_apartment_by_date( $period, $not_found, $data );
					}
				}
			}
			$tf_total_results = 0;
			$tf_total_filters = [];
			foreach ( $not_found as $not ) {
				if ( $not['found'] != 1 ) {
					$tf_total_results   = $tf_total_results + 1;
					$tf_total_filters[] = $not['post_id'];
				}
			}

			if ( empty( $tf_total_filters ) ) {
				echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
			}
			$post_per_page = Helper::tfopt( 'posts_per_page' ) ? Helper::tfopt( 'posts_per_page' ) : 10;

			$total_filtered_results = count( $tf_total_filters );
			$current_page           = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
			$offset                 = ( $current_page - 1 ) * $post_per_page;
			$displayed_results      = array_slice( $tf_total_filters, $offset, $post_per_page );
			if ( ! empty( $displayed_results ) ) {
				$filter_args = array(
					'post_type'      => $posttype,
					'posts_per_page' => $post_per_page,
					'post__in'       => $displayed_results,
				);

				$result_query  = new WP_Query( $filter_args );
				$result_query2 = $result_query;
				if ( $result_query->have_posts() ) {
					while ( $result_query->have_posts() ) {
						$result_query->the_post();

						if ( $posttype == 'tf_hotel' ) {
							$hotel_meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
							if ( ! empty( $data ) ) {
								if ( isset( $data[4] ) && isset( $data[5] ) ) {
									[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;

									if ( $hotel_meta["featured"] ) {
										tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
									}
								} else {
									[ $adults, $child, $room, $check_in_out ] = $data;
									if ( $hotel_meta["featured"] ) {
										tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
									}
								}
							} else {
								if ( $hotel_meta["featured"] ) {
									tf_hotel_archive_single_item();
								}
							}
						} elseif ( $posttype == 'tf_tours' ) {
							$tour_meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
							if ( ! empty( $data ) ) {
								if ( isset( $data[3] ) && isset( $data[4] ) ) {
									[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
									if ( $tour_meta["tour_as_featured"] ) {
										tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
									}
								} else {
									[ $adults, $child, $check_in_out ] = $data;

									if ( $tour_meta["tour_as_featured"] ) {
										tf_tour_archive_single_item( $adults, $child, $check_in_out );
									}
								}
							} else {
								if ( $tour_meta["tour_as_featured"] ) {
									tf_tour_archive_single_item();
								}
							}
						} else {
							$apartment_meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
							if ( ! empty( $data ) ) {
								if ( isset( $data[4] ) && isset( $data[5] ) ) {
									if ( $apartment_meta["apartment_as_featured"] ) {
										tf_apartment_archive_single_item( $data );
									}
								} else {
									if ( $apartment_meta["apartment_as_featured"] ) {
										tf_apartment_archive_single_item( $data );
									}
								}
							} else {
								if ( $apartment_meta["apartment_as_featured"] ) {
									tf_apartment_archive_single_item();
								}
							}
						}

					}

					while ( $result_query2->have_posts() ) {
						$result_query2->the_post();

						if ( $posttype == 'tf_hotel' ) {
							$hotel_meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );

							if ( ! empty( $data ) ) {
								if ( isset( $data[4] ) && isset( $data[5] ) ) {
									[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;

									if ( ! $hotel_meta["featured"] ) {
										tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out, $startprice, $endprice );
									}
								} else {
									[ $adults, $child, $room, $check_in_out ] = $data;

									if ( ! $hotel_meta["featured"] ) {
										tf_hotel_archive_single_item( $adults, $child, $room, $check_in_out );
									}
								}
							} else {
								if ( ! $hotel_meta["featured"] ) {
									tf_hotel_archive_single_item();
								}
							}
						} elseif ( $posttype == 'tf_tours' ) {
							$tour_meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
							if ( ! empty( $data ) ) {
								if ( isset( $data[3] ) && isset( $data[4] ) ) {
									[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
									if ( ! $tour_meta["tour_as_featured"] ) {
										tf_tour_archive_single_item( $adults, $child, $check_in_out, $startprice, $endprice );
									}
								} else {
									[ $adults, $child, $check_in_out ] = $data;
									if ( ! $tour_meta["tour_as_featured"] ) {
										tf_tour_archive_single_item( $adults, $child, $check_in_out );
									}
								}
							} else {
								if ( ! $tour_meta["tour_as_featured"] ) {
									tf_tour_archive_single_item();
								}
							}
						} else {
							$apartment_meta = get_post_meta( get_the_ID(), 'tf_apartment_opt', true );
							if ( ! empty( $data ) ) {
								if ( isset( $data[4] ) && isset( $data[5] ) ) {
									if ( ! $apartment_meta["apartment_as_featured"] ) {
										tf_apartment_archive_single_item( $data );
									}
								} else {
									if ( ! $apartment_meta["apartment_as_featured"] ) {
										tf_apartment_archive_single_item( $data );
									}
								}
							} else {
								if ( ! $apartment_meta["apartment_as_featured"] ) {
									tf_apartment_archive_single_item();
								}
							}
						}

					}
				}
				$total_pages = ceil( $total_filtered_results / $post_per_page );
				if ( $total_pages > 1 ) {
					echo "<div class='tf_posts_navigation tf_posts_ajax_navigation'>";
					echo wp_kses_post(
						paginate_links( array(
							'total'   => $total_pages,
							'current' => $current_page
						) )
					);
					echo "</div>";
				}
			}
		} else {

			echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';

		}

		echo "<span hidden=hidden class='tf-posts-count'>";
		echo ! empty( $tf_total_results ) ? esc_html( $tf_total_results ) : 0;
		echo "</span>";
		wp_reset_postdata();

		die();
	}

	/**
	 * Add tour, hotel & apartment capabilities to admin & editor
	 *
	 * tf_tours, tf_hotel, tf_apartment
	 */
	function tf_admin_role_caps() {

		if ( get_option( 'tf_admin_caps' ) < 2 ) {
			$admin_role  = get_role( 'administrator' );
			$editor_role = get_role( 'editor' );

			// Add a new capability.
			$caps = array(
				// Hotels
				'edit_tf_hotel',
				'read_tf_hotel',
				'delete_tf_hotel',
				'edit_tf_hotels',
				'edit_others_tf_hotels',
				'publish_tf_hotels',
				'read_private_tf_hotels',
				'delete_tf_hotels',
				'delete_private_tf_hotels',
				'delete_published_tf_hotels',
				'delete_others_tf_hotels',
				'edit_private_tf_hotels',
				'edit_published_tf_hotels',
				'create_tf_hotels',
				// Apartment
				'edit_tf_apartment',
				'read_tf_apartment',
				'delete_tf_apartment',
				'edit_tf_apartments',
				'edit_others_tf_apartments',
				'publish_tf_apartments',
				'read_private_tf_apartments',
				'delete_tf_apartments',
				'delete_private_tf_apartments',
				'delete_published_tf_apartments',
				'delete_others_tf_apartments',
				'edit_private_tf_apartments',
				'edit_published_tf_apartments',
				'create_tf_apartments',
				// Tours
				'edit_tf_tours',
				'read_tf_tours',
				'delete_tf_tours',
				'edit_tf_tourss',
				'edit_others_tf_tourss',
				'publish_tf_tourss',
				'read_private_tf_tourss',
				'delete_tf_tourss',
				'delete_private_tf_tourss',
				'delete_published_tf_tourss',
				'delete_others_tf_tourss',
				'edit_private_tf_tourss',
				'edit_published_tf_tourss',
				'create_tf_tourss',
			);

			foreach ( $caps as $cap ) {
				$admin_role->add_cap( $cap );
				$editor_role->add_cap( $cap );
			}

			update_option( 'tf_admin_caps', 2 );
		}
	}

	/*
     * Asign Destination taxonomy template
     */
	function taxonomy_template( $template ) {

		if ( is_tax( 'hotel_location' ) ) {

			$theme_files     = array( 'tourfic/hotel/taxonomy-hotel_locations.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				$template = $exists_in_theme;
			} else {
				$template = TF_TEMPLATE_PATH . 'hotel/taxonomy-hotel_locations.php';
			}
		}

		if ( is_tax( 'apartment_location' ) ) {
			$theme_files     = array( 'tourfic/apartment/taxonomy-apartment_locations.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				$template = $exists_in_theme;
			} else {
				$template = TF_TEMPLATE_PATH . 'apartment/taxonomy-apartment_locations.php';
			}
		}

		if ( is_tax( 'tour_destination' ) ) {

			$theme_files     = array( 'tourfic/tour/taxonomy-tour_destinations.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				$template = $exists_in_theme;
			} else {
				$template = TF_TEMPLATE_PATH . 'tour/taxonomy-tour_destinations.php';
			}

		}

		return $template;

	}

	/**
	 * Assign Review Template Part
	 *
	 * @since 1.0
	 */
	function load_comment_template( $comment_template ) {
		global $post;

		if ( 'tf_hotel' === $post->post_type || 'tf_tours' === $post->post_type || 'tf_apartment' === $post->post_type ) {
			$theme_files     = array( 'tourfic/template-parts/review.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'template-parts/review.php';
			}
		}

	}

	/**
	 * Assign Archive Template
	 *
	 * @since 1.0
	 */
	function tourfic_archive_page_template( $template ) {
		if ( is_post_type_archive( 'tf_hotel' ) ) {
			$theme_files     = array( 'tourfic/hotel/archive-hotels.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'hotel/archive-hotels.php';
			}
		}


		if ( is_post_type_archive( 'tf_apartment' ) ) {
			$theme_files     = array( 'tourfic/apartment/archive-apartments.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'apartment/archive-apartments.php';
			}
		}

		if ( is_post_type_archive( 'tf_tours' ) ) {
			$theme_files     = array( 'tourfic/tour/archive-tours.php' );
			$exists_in_theme = locate_template( $theme_files, false );
			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . 'tour/archive-tours.php';
			}
		}

		return $template;
	}

	/**
	 * Assign Single Template
	 *
	 * @since 1.0
	 */
	function tf_single_page_template( $single_template ) {

		global $post;

		/**
		 * Hotel Single
		 *
		 * single-hotel.php
		 */
		if ( 'tf_hotel' === $post->post_type ) {

			$theme_files     = array( 'tourfic/hotel/single-hotel.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . "hotel/single-hotel.php";
			}
		}

		/**
		 * Apartment Single
		 *
		 * single-apartment.php
		 */
		if ( 'tf_apartment' === $post->post_type ) {

			$theme_files     = array( 'tourfic/apartment/single-apartment.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . "apartment/single-apartment.php";
			}
		}

		/**
		 * Tour Single
		 *
		 * single-tour.php
		 */
		if ( $post->post_type == 'tf_tours' ) {

			$theme_files     = array( 'tourfic/tour/single-tour.php' );
			$exists_in_theme = locate_template( $theme_files, false );

			if ( $exists_in_theme ) {
				return $exists_in_theme;
			} else {
				return TF_TEMPLATE_PATH . "tour/single-tour.php";
			}
		}

		return $single_template;
	}

	function tf_image_sizes() {
		// Hotel gallery, hard crop
		add_image_size( 'tf_apartment_gallery_large', 819, 475, true );
		add_image_size( 'tf_apartment_gallery_small', 333, 231, true );
		add_image_size( 'tf_apartment_single_thumb', 1170, 500, true );
		add_image_size( 'tf_gallery_thumb', 900, 490, true );
		add_image_size( 'tf-thumb-480-320', 480, 320, true );
	}
}