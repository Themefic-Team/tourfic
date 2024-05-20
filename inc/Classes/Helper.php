<?php

namespace Tourfic\Classes;
defined( 'ABSPATH' ) || exit;

class Helper {
	use \Tourfic\Traits\Singleton;
	use \Tourfic\Traits\Helper;

	public function __construct() {
		add_filter( 'body_class', array( $this, 'tf_templates_body_class' ) );
		add_action( 'admin_footer', array( $this, 'tf_admin_footer' ) );
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
}