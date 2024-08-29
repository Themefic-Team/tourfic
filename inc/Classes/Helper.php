<?php

namespace Tourfic\Classes;
defined( 'ABSPATH' ) || exit;

use \Tourfic\Admin\Emails\TF_Handle_Emails;

class Helper {
	use \Tourfic\Traits\Singleton;
	use \Tourfic\Traits\TF_Fonts;

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
		add_action( 'wp_ajax_nopriv_tf_trigger_tax_filter', array( $this, 'tf_trigger_tax_filter_callback' ) );
		add_action( 'wp_ajax_tf_trigger_tax_filter', array( $this, 'tf_trigger_tax_filter_callback' ) );

		// tax filter
		add_action( 'wp_ajax_nopriv_tf_trigger_filter', array( $this, 'tf_search_result_ajax_sidebar' ) );
		add_action( 'wp_ajax_tf_trigger_filter', array( $this, 'tf_search_result_ajax_sidebar' ) );

		add_action( 'admin_init', array( $this, 'tf_admin_role_caps' ), 999 );
		add_filter( 'template_include', array( $this, 'taxonomy_template' ) );
		add_filter( 'comments_template', array( $this, 'load_comment_template' ) );
		add_filter( 'template_include', array( $this, 'tourfic_archive_page_template' ) );
		add_filter( 'single_template', array( $this, 'tf_single_page_template' ) );
		add_filter( 'after_setup_theme', array( $this, 'tf_image_sizes' ) );

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
		add_action( 'admin_init', array( $this, 'redirect_non_admin_users' ), 9 );
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
				$classes[] = 'tf_template_3_hotel_archive';
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
				$classes[] = 'tf_template_3_single_hotel';
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

	public static function tf_custom_wp_kses_allow_tags() {
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

	function tf_shortcode_type_to_location_callback() {
		//Nonce Verification
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

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

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$search_month = sanitize_key( $_POST['month'] );
		$search_year  = sanitize_key( $_POST['year'] );
		$month_dates  = cal_days_in_month( CAL_GREGORIAN, $search_month, $search_year );

		//Order Data Retrive
		$tf_old_order_limit = new \WC_Order_Query( array(
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

		$relation        = self::tfopt( 'search_relation', 'AND' );
		$filter_relation = self::tfopt( 'filter_relation', 'OR' );

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
			$period = new \DatePeriod(
				new \DateTime( $tf_form_start ),
				new \DateInterval( 'P1D' ),
				new \DateTime( ! empty( $tf_form_end ) ? $tf_form_end : $tf_form_start . '23:59' )
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

		$post_per_page = self::tfopt( 'posts_per_page' ) ? self::tfopt( 'posts_per_page' ) : 10;
		// $paged = !empty($_POST['page']) ? absint( $_POST['page'] ) : 1;
		// Properties args
		if ( $posttype == "tf_tours" ) {
			$tf_expired_tour_showing = ! empty( self::tfopt( 't-show-expire-tour' ) ) ? self::tfopt( 't-show-expire-tour' ) : '';
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

		$loop = new \WP_Query( $args );

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
			$post_per_page = self::tfopt( 'posts_per_page' ) ? self::tfopt( 'posts_per_page' ) : 10;

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

				$result_query  = new \WP_Query( $filter_args );
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
					echo "<div class='tf_posts_navigation tf_posts_ajax_navigation tf_search_ajax_pagination'>";
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

	function tf_trigger_tax_filter_callback() {

		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'tf_ajax_nonce' ) ) {
			return;
		}

		$tax_name              = ! empty( $_POST['tax_name'] ) ? sanitize_text_field( $_POST['tax_name'] ) : '';
		$term_ids              = ! empty( $_POST['term_ids'] ) ? explode( ',', sanitize_text_field( $_POST['term_ids'] ) ) : '';
		$post_type             = ! empty( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'tf_hotel';
		$post_per_page         = self::tfopt( 'posts_per_page' ) ? self::tfopt( 'posts_per_page' ) : 10;
		$relation              = self::tfopt( 'search_relation', 'AND' );
		$filter_relation       = self::tfopt( 'filter_relation', 'OR' );
		$current_page          = ! empty( $_POST["page"] ) ? sanitize_text_field( $_POST["page"] ) : 1;
		$filters               = ! empty( $_POST['filters'] ) ? explode( ',', sanitize_text_field( $_POST['filters'] ) ) : null;
		$features              = ! empty( $_POST['features'] ) ? explode( ',', sanitize_text_field( $_POST['features'] ) ) : null;
		$tf_hotel_types        = ! empty( $_POST['tf_hotel_types'] ) ? explode( ',', sanitize_text_field( $_POST['tf_hotel_types'] ) ) : null;
		$tour_features         = ! empty( $_POST['tour_features'] ) ? explode( ',', sanitize_text_field( $_POST['tour_features'] ) ) : null;
		$attractions           = ! empty( $_POST['attractions'] ) ? explode( ',', sanitize_text_field( $_POST['attractions'] ) ) : null;
		$activities            = ! empty( $_POST['activities'] ) ? explode( ',', sanitize_text_field( $_POST['activities'] ) ) : null;
		$tf_tour_types         = ! empty( $_POST['tf_tour_types'] ) ? explode( ',', sanitize_text_field( $_POST['tf_tour_types'] ) ) : null;
		$tf_apartment_features = ! empty( $_POST['tf_apartment_features'] ) ? explode( ',', sanitize_text_field( $_POST['tf_apartment_features'] ) ) : null;
		$tf_apartment_types    = ! empty( $_POST['tf_apartment_types'] ) ? explode( ',', sanitize_text_field( $_POST['tf_apartment_types'] ) ) : null;
		$place_taxonomy        = $post_type == 'tf_tours' ? 'tour_destination' : ( $post_type == 'tf_apartment' ? 'apartment_location' : 'hotel_location' );
		$filter_taxonomy       = $post_type == 'tf_tours' ? 'null' : 'hotel_feature';


		$args = array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => - 1
		);

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

		$loop = new \WP_Query( $args );

		$total_posts = $loop->found_posts;
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) {

				$loop->the_post();

				if ( $post_type == 'tf_hotel' ) {
					tf_hotel_archive_single_item();
				} else if ( $post_type == 'tf_tours' ) {
					tf_tour_archive_single_item();
				} else {
					tf_apartment_archive_single_item();
				}
			}

			// global $wp_rewrite;

			// $base = trailingslashit( get_post_type_archive_link( $post_type ) ) . "{$wp_rewrite->pagination_base}/%#%/";

			// $total_pages = ceil( $total_posts / $post_per_page );
			// if ( $total_pages > 1 ) {
			// 	echo "<div class='tf_posts_navigation tf_posts_ajax_navigation tf_tax_posts_navigation'>";
			// 	echo wp_kses_post(
			// 		paginate_links( array(
			// 			'base' => $base,
			// 			'total'   => $loop->max_num_pages,
			// 			'current' => max( $current_page, get_query_var( 'paged' ) ),
			// 			'prev_next' => true,
			// 		) )
			// 	);
			// 	echo "</div>";
			// }
		}

		global $wp_rewrite;

		if ( $total_posts == 0 ) {
			echo '<div class="tf-nothing-found" data-post-count="0">' . esc_html__( 'Nothing Found!', 'tourfic' ) . '</div>';
		}

		echo "<span hidden=hidden class='tf-posts-count'>";
		echo ! empty( $total_posts ) ? esc_html( $total_posts ) : 0;
		echo "</span>";
		wp_reset_postdata();

		die();
	}

	function get_current_url() {
		$protocol = is_ssl() ? 'https://' : 'http://';

		return ( $protocol ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	/**
	 * Add tour, hotel & apartment capabilities to admin & editor
	 *
	 * tf_tours, tf_hotel, tf_apartment
	 */
	function tf_admin_role_caps() {

		if ( get_option( 'tf_admin_caps' ) < 4 ) {
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
				// Rooms
				'edit_tf_room',
				'read_tf_room',
				'delete_tf_room',
				'edit_tf_rooms',
				'edit_others_tf_rooms',
				'publish_tf_rooms',
				'read_private_tf_rooms',
				'delete_tf_rooms',
				'delete_private_tf_rooms',
				'delete_published_tf_rooms',
				'delete_others_tf_rooms',
				'edit_private_tf_rooms',
				'edit_published_tf_rooms',
				'create_tf_rooms',
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

			update_option( 'tf_admin_caps', 4 );
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

		if ( ( $post_type == "tf_tours" && $tf_tour_arc_selected_template == "design-1" ) || ( $post_type == "tf_hotel" && $tf_hotel_arc_selected_template == "design-1" ) ) {
			?>
            <div class="tf-box-wrapper tf-box tf-mrbottom-30">
                <form class="widget tf-hotel-side-booking" method="get" autocomplete="off"
                      action="<?php echo esc_url( tf_booking_search_action() ); ?>" id="tf-widget-booking-search">

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

                        $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
                            enableTime: false,
                            minDate: "today",
                            altInput: true,
                            altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',
                            mode: "range",
                            dateFormat: "Y/m/d",
                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
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
							<?php tf_flatpickr_locale( "root" ); ?>

                            $(".tf-template-3 .tf-booking-date-wrap").on("click", function () {
                                $("#check-in-out-date").trigger("click");
                            });
                            $("#check-in-out-date").flatpickr({
                                enableTime: false,
                                mode: "range",
                                dateFormat: "Y/m/d",
                                minDate: "today",

                                // flatpickr locale
								<?php tf_flatpickr_locale(); ?>

                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    dateSetToFields(selectedDates, instance);
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
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

                            // flatpickr locale
							<?php tf_flatpickr_locale( "root" ); ?>

                            $(".tf-template-3 .tf-booking-date-wrap").on("click", function () {
                                $("#check-in-out-date").trigger("click");
                            });
                            $("#check-in-out-date").flatpickr({
                                enableTime: false,
                                mode: "range",
                                dateFormat: "Y/m/d",
                                minDate: "today",

                                // flatpickr locale
								<?php tf_flatpickr_locale(); ?>


                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    dateSetToFields(selectedDates, instance);
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
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
		<?php } else { ?>
            <!-- Start Booking widget -->
            <form class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
                  action="<?php echo esc_url( tf_booking_search_action() ); ?>" id="tf-widget-booking-search">

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
						<?php tf_flatpickr_locale( "root" ); ?>

                        $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
                            enableTime: false,
                            minDate: "today",
                            altInput: true,
                            altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',
                            mode: "range",
                            dateFormat: "Y/m/d",

                            // flatpickr locale
							<?php tf_flatpickr_locale(); ?>

                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
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
                <form action="<?php echo esc_url( tf_booking_search_action() ); ?>" method="get" autocomplete="off" class="tf_archive_search_result tf-hotel-side-booking">
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
						<?php tf_flatpickr_locale( 'root' ); ?>

                        $(document).on("focus", ".tf-hotel-side-booking #check-in-out-date", function (e) {
                            let calander = flatpickr(this, {
                                enableTime: false,
                                minDate: "today",
                                mode: "range",
                                dateFormat: "Y/m/d",
                                altInput: true,
                                altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',

                                // flatpickr locale
								<?php tf_flatpickr_locale(); ?>

                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
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
							<?php tf_flatpickr_locale( "root" ); ?>

                            $(".tf-template-3 .tf-booking-date-wrap").on("click", function () {

                                $("#check-in-out-date").trigger("click");
                            });
                            $("#check-in-out-date").flatpickr({
                                enableTime: false,
                                mode: "range",
                                dateFormat: "Y/m/d",
                                minDate: "today",

                                // flatpickr locale
								<?php tf_flatpickr_locale(); ?>

                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    dateSetToFields(selectedDates, instance);
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
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
                            // flatpickr locale first day of Week
							<?php tf_flatpickr_locale( "root" ); ?>

                            $(".tf-template-3 .tf-booking-date-wrap").on("click", function () {

                                $("#check-in-out-date").trigger("click");
                            });
                            $("#check-in-out-date").flatpickr({
                                enableTime: false,
                                mode: "range",
                                dateFormat: "Y/m/d",
                                minDate: "today",

                                // flatpickr locale
								<?php tf_flatpickr_locale(); ?>

                                onReady: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    dateSetToFields(selectedDates, instance);
                                },
                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
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
			<?php } ?>

		<?php } else { ?>
            <form class="tf_archive_search_result tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
                  action="<?php echo esc_url( tf_booking_search_action() ); ?>">

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
						<?php tf_flatpickr_locale( 'root' ); ?>

                        $(document).on("focus", ".tf-hotel-side-booking #check-in-out-date", function (e) {
                            let calander = flatpickr(this, {
                                enableTime: false,
                                minDate: "today",
                                mode: "range",
                                dateFormat: "Y/m/d",
                                altInput: true,
                                altFormat: '<?php echo esc_html( $date_format_for_users ); ?>',

                                // flatpickr locale
								<?php tf_flatpickr_locale(); ?>

                                onChange: function (selectedDates, dateStr, instance) {
                                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                                    instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
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
				( order_id, post_id, post_type, room_number, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %d, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
				array(
					$order_data['order_id'],
					sanitize_key( $order_data['post_id'] ),
					$order_data['post_type'],
					$order_data['room_number'],
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

		return $order_id;
	}

	function tf_gutenberg_author_dropdown_roles( $args, $request = null ) {

		// get all the roles in a website
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles();
		}

		$tf_all_roles = is_array( $wp_roles->get_names() ) && ! empty( $wp_roles->get_names() ) ? array_keys( $wp_roles->get_names() ) : array( 'administrator', 'author', 'editor', 'tf_vendor', 'tf_manager' );

		// exclude the roles that are not needed
		$tf_all_roles = array_filter( $tf_all_roles, function ( $role ) {
			return $role !== 'contributor' && $role !== 'subscriber' && $role !== 'customer';
		}
		);

		if ( current_user_can( 'edit_posts' ) ) {
			if ( isset( $args['who'] ) && $args['who'] === 'authors' ) {
				unset( $args['who'] );
				$args['role__in'] = $tf_all_roles;
			}

			return $args;
		}

		return $args;
	}

	//  Plugin Page Action Links for Tourfic
	function tf_plugin_action_links( $links ) {

		$settings_link = array(
			'<a href="admin.php?page=tf_dashboard">' . esc_html__( 'Settings', 'tourfic' ) . '</a>',
		);

		if ( ! is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) ) {
			$gopro_link = array(
				'<a href="https://tourfic.com/go/upgrade" target="_blank" style="color:#cc0000;font-weight: bold;text-shadow: 0px 1px 1px hsl(0deg 0% 0% / 28%);">' . esc_html__( 'GO PRO', 'tourfic' ) . '</a>',
			);

			return array_merge( $settings_link, $links, $gopro_link );
		} else {
			return array_merge( $settings_link, $links );
		}
	}

	//  Plugin Page Action Links for Tourfic Pro
	function tf_pro_plugin_licence_action_links( $links ) {

		$active_licence_link = array(
			'<a href="' . admin_url() . 'admin.php?page=tf_license_info" style="color:#cc0000;font-weight: bold;text-shadow: 0px 1px 1px hsl(0deg 0% 0% / 28%);">' . esc_html__( 'Activate the Licence', 'tourfic' ) . '</a>',
		);

		return array_merge( $links, $active_licence_link );
	}

	/**
	 * Go to Documentation Menu Item
	 */
	function tf_documentation_page_integration() {
		global $submenu;
		$doc_url = sanitize_url( 'https://themefic.com/docs/tourfic/' );

		$submenu['edit.php?post_type=tf_hotel'][]     = array(
			sprintf( '<span class="tf-go-docs" style=color:#ffba00;">%s</span>', esc_html__( 'Go to Documentation', 'tourfic' ) ),
			'edit_tf_hotels',
			$doc_url
		);
		$submenu['edit.php?post_type=tf_apartment'][] = array(
			sprintf( '<span class="tf-go-docs" style=color:#ffba00;">%s</span>', esc_html__( 'Go to Documentation', 'tourfic' ) ),
			'edit_tf_apartments',
			$doc_url
		);
		$submenu['edit.php?post_type=tf_tours'][]     = array(
			sprintf( '<span class="tf-go-docs" style=color:#ffba00;">%s</span>', esc_html__( 'Go to Documentation', 'tourfic' ) ),
			'edit_tf_tourss',
			$doc_url
		);

	}

	/**
	 * Go to Documentaion Metabox
	 */
	function tf_hotel_tour_docs() {
		add_meta_box( 'tfhotel_docs', esc_html__( 'Tourfic Documentation', 'tourfic' ), array( $this, 'tf_hotel_docs_callback' ), 'tf_hotel', 'side', 'high' );
		add_meta_box( 'tfapartment_docs', esc_html__( 'Tourfic Documantation', 'tourfic' ), array( $this, 'tf_apartment_docs_callback' ), 'tf_apartment', 'side', 'high' );
		add_meta_box( 'tftour_docs', esc_html__( 'Tourfic Documentation', 'tourfic' ), array( $this, 'tf_tour_docs_callback' ), 'tf_tours', 'side', 'high' );

		add_filter( 'get_user_option_meta-box-order_tf_tours', array( $this, 'tour_metabox_order' ) );
		add_filter( 'get_user_option_meta-box-order_tf_apartment', array( $this, 'apartment_metabox_order' ) );
		add_filter( 'get_user_option_meta-box-order_tf_hotel', array( $this, 'hotel_metabox_order' ) );
	}

	function tf_hotel_docs_callback() {
		$tfhoteldocumentation = sanitize_url( 'https://themefic.com/docs/tourfic/how-it-works/add-new-hotel/' );
		?>
        <div class="tf_docs_preview">
            <a href="<?php echo esc_url( $tfhoteldocumentation ); ?>" target="_blank">
                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL . 'images/banner-cta.png' ); ?>" alt="<?php echo esc_html__( 'Go to Documentation', 'tourfic' ); ?>">
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
                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL . 'images/banner-cta.png' ); ?>" alt="<?php echo esc_html__( 'Go to Documentation', 'tourfic' ); ?>">
            </a>
        </div>
		<?php
	}

	function tf_tour_docs_callback() {
		$tf_tour_documentation = sanitize_url( 'https://themefic.com/docs/tourfic/tours/tourfic-hotel-general-settings/' );
		?>
        <div class="tf_docs_preview">
            <a href="<?php echo esc_url( $tf_tour_documentation ); ?>" target="_blank">
                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL . 'images/banner-cta.png' ); ?>" alt="<?php echo esc_html__( 'Go to Documentation', 'tourfic' ); ?>">
            </a>
        </div>
		<?php
	}

	function apartment_metabox_order( $order ) {
		return array(
			'side' => join(
				",",
				array(
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

	/*
     * User extra fields
     * @author Foysal
     */
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

	/*
     * Save user extra fields
     * @author Foysal
     */
	function tf_save_extra_user_profile_fields( $user_id ) {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-user_' . $user_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		update_user_meta( $user_id, 'language', $_POST['language'] );
	}

	function tourfic_admin_menu_seperator() {

		global $menu;

		$menu[] = array( '', 'read', 'separator-tourfic', '', 'wp-menu-separator tourfic' );
		$menu[] = array( '', 'read', 'separator-tourfic2', '', 'wp-menu-separator tourfic' );
	}

	function tourfic_admin_menu_order_change( $menu_order ) {

		if ( ! empty( $menu_order ) && $menu_order != null ) {
			$tourfic_menu_order = array();

			$tourfic_separator  = array_search( 'separator-tourfic', $menu_order, true );
			$tourfic_separator2 = array_search( 'separator-tourfic2', $menu_order, true );
			$tourfic_tours      = array_search( 'edit.php?post_type=tf_tours', $menu_order, true );
			$tourfic_hotel      = array_search( 'edit.php?post_type=tf_hotel', $menu_order, true );
			$tourfic_hotel_room = array_search( 'edit.php?post_type=tf_room', $menu_order, true );
			$tourfic_apt        = array_search( 'edit.php?post_type=tf_apartment', $menu_order, true );
			$tourfic_emails     = array_search( 'edit.php?post_type=tf_email_templates', $menu_order, true );
			$tourfic_vendor     = array_search( 'tf-multi-vendor', $menu_order, true );

			// // remove previous orders
			unset( $menu_order[ $tourfic_separator ] );
			unset( $menu_order[ $tourfic_separator2 ] );

			if ( ! empty( $tourfic_apt ) ) {
				unset( $menu_order[ $tourfic_apt ] );
			}

			if ( ! empty( $tourfic_tours ) ) {
				unset( $menu_order[ $tourfic_tours ] );
			}

			if ( ! empty( $tourfic_hotel ) ) {
				unset( $menu_order[ $tourfic_hotel ] );
			}

			if ( ! empty( $tourfic_hotel_room ) && ! empty( $tourfic_hotel ) ) {
				unset( $menu_order[ $tourfic_hotel_room ] );
			}

			if ( ! empty( $tourfic_vendor ) ) {
				unset( $menu_order[ $tourfic_vendor ] );
			}

			if ( ! empty( $tourfic_emails ) ) {
				unset( $menu_order[ $tourfic_emails ] );
			}

			foreach ( $menu_order as $index => $item ) {

				if ( 'tf_settings' === $item ) {
					$tourfic_menu_order[] = 'separator-tourfic';
					$tourfic_menu_order[] = $item;
					$tourfic_menu_order[] = 'tf-multi-vendor';
					$tourfic_menu_order[] = 'edit.php?post_type=tf_tours';
					$tourfic_menu_order[] = 'edit.php?post_type=tf_hotel';
					$tourfic_menu_order[] = 'edit.php?post_type=tf_room';
					$tourfic_menu_order[] = 'edit.php?post_type=tf_apartment';
					$tourfic_menu_order[] = 'edit.php?post_type=tf_email_templates';
					$tourfic_menu_order[] = 'separator-tourfic2';

				} elseif ( ! in_array( $item, array( 'separator-tourfic' ), true ) ) {
					$tourfic_menu_order[] = $item;
				}
			}

			return $tourfic_menu_order;

		} else {

			return;
		}
	}

	public function tf_admin_bar_dashboard_link( $wp_admin_bar ) {

		if ( ! is_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
			return;
		}

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$tf_dashboard_page_link = ! empty( get_option( 'tf_dashboard_page_id' ) ) ? get_permalink( get_option( 'tf_dashboard_page_id' ) ) : get_home_url();

			$wp_admin_bar->add_node(
				array(
					'parent' => 'site-name',
					'id'     => 'view-tourfic-vendor-dashboard',
					'title'  => __( 'Visit Vendor Dashboard', 'tourfic' ),
					'href'   => $tf_dashboard_page_link,
				)
			);

		} else {

			return;
		}
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
}