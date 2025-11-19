<?php

namespace Tourfic\Admin\TF_Options;
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Room;

class TF_Options {

	private static $instance = null;

	/**
	 * Singleton instance
	 * @since 1.0.0
	 */
	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		//load files
		$this->load_files();

		//load metaboxes
		$this->load_metaboxes();

		//load options
		$this->load_options();

		//load taxonomy
		$this->load_taxonomy();

		add_action( 'wp_ajax_tf_load_more_icons', array( $this, 'tf_load_more_icons' ) );
		add_action( 'wp_ajax_tf_icon_search', array( $this, 'tf_icon_search' ) );

		add_action( 'wp_ajax_tf_add_hotel_room_availability', array( $this, 'tf_add_hotel_room_availability' ) );
		add_action( 'wp_ajax_tf_get_hotel_room_availability', array( $this, 'tf_get_hotel_room_availability' ) );
		add_action( 'wp_ajax_tf_reset_room_availability', array( $this, 'tf_reset_room_availability' ) );
		add_action( 'save_post', array( $this, 'tf_update_room_avail_date_price' ), 9999, 2 );
		add_action( 'wp_ajax_tf_add_apartment_availability', array( $this, 'tf_add_apartment_availability' ) );
		add_action( 'wp_ajax_tf_get_apartment_availability', array( $this, 'tf_get_apartment_availability' ) );
		add_action( 'wp_ajax_tf_reset_apt_availability', array( $this, 'tf_reset_apt_availability' ) );

		add_action( 'wp_ajax_tf_add_tour_availability', array( $this, 'tf_add_tour_availability' ) );
		add_action( 'wp_ajax_tf_get_tour_availability', array( $this, 'tf_get_tour_availability' ) );
		add_action( 'wp_ajax_save_tour_package_pricing', array( $this, 'save_tour_package_pricing' ) );
		add_action( 'wp_ajax_save_tour_pricing_type', array( $this, 'save_tour_pricing_type' ) );
		add_action( 'wp_ajax_tf_reset_tour_availability', array( $this, 'tf_reset_tour_availability' ) );
		add_action( 'save_post', array( $this, 'tf_update_apt_availability_price' ), 99, 2 );
		add_action( 'wp_ajax_tf_insert_category_data', array( $this, 'tf_insert_category_data_callback' ) );
		add_action( 'wp_ajax_tf_delete_category_data', array( $this, 'tf_delete_category_data_callback' ) );
		add_action( 'wp_ajax_tf_insert_post_data', array( $this, 'tf_insert_post_data_callback' ) );
		add_action( 'wp_ajax_tf_delete_post_data', array( $this, 'tf_delete_post_data_callback' ) );
	}

	public function tf_options_file_path( $file_path = '' ) {
		return plugin_dir_path( __FILE__ ) . $file_path;
	}

	public function tf_options_file_url( $file_url = '' ) {
		return plugin_dir_url( __FILE__ ) . $file_url;
	}

	/**
	 * Load files
	 * @author Foysal
	 */
	public function load_files() {
		// Metaboxes Class
		require_once $this->tf_options_file_path( 'classes/TF_Metabox.php' );
		// Settings Class
		require_once $this->tf_options_file_path( 'classes/TF_Settings.php' );
		//Shortcodes Class
		require_once $this->tf_options_file_path( 'classes/TF_Shortcodes.php' );
		//Taxonomy Class
		require_once $this->tf_options_file_path( 'classes/TF_Taxonomy_Metabox.php' );

		require_once $this->tf_options_file_path( 'fields/icon/fontawesome-4.php' );
		require_once $this->tf_options_file_path( 'fields/icon/fontawesome-5.php' );
		require_once $this->tf_options_file_path( 'fields/icon/fontawesome-6.php' );
		require_once $this->tf_options_file_path( 'fields/icon/remix-icon.php' );
	}

	/**
	 * Load metaboxes
	 * @author Foysal
	 */
	public function load_metaboxes() {
		if ( $this->is_tf_pro_active() ) {
			$metaboxes = glob( TF_PRO_ADMIN_PATH . 'tf-options/metaboxes/*.php' );
		} else {
			$metaboxes = glob( $this->tf_options_file_path( 'metaboxes/*.php' ) );
		}

		/*if( !empty( $pro_metaboxes ) ) {
			$metaboxes = array_merge( $metaboxes, $pro_metaboxes );
		}*/
		if ( ! empty( $metaboxes ) ) {
			foreach ( $metaboxes as $metabox ) {
				if ( file_exists( $metabox ) ) {
					require_once $metabox;
				}
			}
		}
	}

	/**
	 * Load Options
	 * @author Foysal
	 */
	public function load_options() {
		if ( $this->is_tf_pro_active() ) {
			$options = glob( TF_PRO_ADMIN_PATH . 'tf-options/options/*.php' );
		} else {
			$options = glob( $this->tf_options_file_path( 'options/*.php' ) );
		}

		if ( ! empty( $options ) ) {
			foreach ( $options as $option ) {
				if ( file_exists( $option ) ) {
					require_once $option;
				}
			}
		}
	}

	/**
	 * Load Taxonomy
	 * @author Foysal
	 */
	public function load_taxonomy() {
		if ( $this->is_tf_pro_active() ) {
			$taxonomies = glob( TF_PRO_ADMIN_PATH . 'tf-options/taxonomies/*.php' );
		} else {
			$taxonomies = glob( $this->tf_options_file_path( 'taxonomies/*.php' ) );
		}

		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				if ( file_exists( $taxonomy ) ) {
					require_once $taxonomy;
				}
			}
		}
	}

	/*
	 * Field Base
	 * @author Foysal
	 */
	public function field( $field, $value, $settings_id = '', $parent = '', $related_value = '' ) {
		if ( $field['type'] == 'repeater' ) {
			$id = ( ! empty( $settings_id ) ) ? $settings_id . '[' . $field['id'] . '][0]' . '[' . $field['id'] . ']' : $field['id'] . '[0]' . '[' . $field['id'] . ']';
		} else {
			$id = $settings_id . '[' . $field['id'] . ']';
		}

		$class = isset( $field['class'] ) ? $field['class'] : '';

		$is_pro   = isset( $field['is_pro'] ) ? $field['is_pro'] : '';
		$badge_up = isset( $field['badge_up'] ) ? $field['badge_up'] : '';

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$is_pro = false;
		}
		if ( $is_pro == true ) {
			$class .= ' tf-field-disable tf-field-pro';
		}
		if ( $badge_up == true ) {
			$class .= ' tf-field-disable tf-field-upcoming';
		}
		$tf_meta_box_dep_value = get_post_meta( get_the_ID(), $settings_id, true );


		$depend = '';
		if ( ! empty( $field['dependency'] ) ) {

			$dependency      = $field['dependency'];
			$depend_visible  = '';
			$data_controller = '';
			$data_condition  = '';
			$data_value      = '';
			$data_global     = '';

			if ( is_array( $dependency[0] ) ) {
				$data_controller = implode( '|', array_column( $dependency, 0 ) );
				$data_condition  = implode( '|', array_column( $dependency, 1 ) );
				$data_value      = implode( '|', array_column( $dependency, 2 ) );
				$data_global     = implode( '|', array_column( $dependency, 3 ) );
				$depend_visible  = implode( '|', array_column( $dependency, 4 ) );
			} else {
				$data_controller = ( ! empty( $dependency[0] ) ) ? $dependency[0] : '';
				$data_condition  = ( ! empty( $dependency[1] ) ) ? $dependency[1] : '';
				$data_value      = ( ! empty( $dependency[2] ) ) ? $dependency[2] : '';
				$data_global     = ( ! empty( $dependency[3] ) ) ? $dependency[3] : '';
				$depend_visible  = ( ! empty( $dependency[4] ) ) ? $dependency[4] : '';
			}

			$depend .= ' data-controller="' . esc_attr( $data_controller ) . '' . $parent . '"';
			$depend .= ' data-condition="' . esc_attr( $data_condition ) . '"';
			$depend .= ' data-value="' . esc_attr( $data_value ) . '"';
			$depend .= ( ! empty( $data_global ) ) ? ' data-depend-global="true"' : '';

			$visible = ( ! empty( $depend_visible ) ) ? ' tf-depend-visible' : ' tf-depend-hidden';
		}

		//field width
		$field_width = isset( $field['field_width'] ) && ! empty( $field['field_width'] ) ? esc_attr( $field['field_width'] ) : '100';
		if ( $field_width == '100' ) {
			$field_style = 'width:100%;';
		} else {
			$field_style = 'width:calc(' . $field_width . '% - 10px);';
		}
		?>

        <div class="tf-field tf-field-<?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( $class ); ?> <?php echo ! empty( $visible ) ? wp_kses_post( $visible ) : ''; ?>" <?php echo ! empty( $depend ) ? wp_kses_post( $depend ) : ''; ?>
             style="<?php echo esc_attr( $field_style ); ?>">

			<?php if ( ! empty( $field['label'] ) && $field['type']!='switch' && $field['type']!='accordion' && $field['type']!='heading' ){ ?>
                <label for="<?php echo esc_attr( $id ) ?>" class="tf-field-label">
					<?php echo esc_html( $field['label'] ) ?>
					<?php if ( ! empty( $field['subtitle'] ) ) : ?>
					<span class="tf-desc-tooltip">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<g clip-path="url(#clip0_1017_4247)">
								<path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</g>
							<defs>
								<clipPath id="clip0_1017_4247">
								<rect width="16" height="16" fill="white"/>
								</clipPath>
							</defs>
						</svg>
						<div class="tf-desc-tooltip-content">
							<?php echo wp_kses_post( $field['subtitle'] ) ?>
						</div>
					</span>
					<?php endif; ?>
					<?php if ( $is_pro ): ?>
                        <div class="tf-csf-badge"><span class="tf-pro"><?php esc_html_e( "Pro", "tourfic" ); ?></span></div>
					<?php endif; ?>
					<?php if ( $badge_up ): ?>
                        <div class="tf-csf-badge"><span class="tf-upcoming"><?php esc_html_e( "Upcoming", "tourfic" ); ?></span></div>
					<?php endif; ?>
                </label>
				<?php if ( $field['type']=='repeater' ){ ?>
				<?php if ( ! empty( $field['description'] ) ): ?>
					<span class="tf-field-sub-title tf-field-repeater-desc"><?php echo wp_kses_post( $field['description'] ) ?></span>
				<?php endif; } ?>
			<?php } ?>

            <div class="tf-fieldset">
				<?php
				$fieldClass = 'TF_' . $field['type'];
				if ( class_exists( $fieldClass ) ) {
					$_field = new $fieldClass( $field, $value, $settings_id, $parent, $related_value );
					$_field->render();
				} else {
					echo '<p>' . esc_html__( 'Field not found!', 'tourfic' ) . '</p>';
				}
				?>
            </div>

			<?php if ( $field['type']!='repeater' ){ ?>
			<?php if ( ! empty( $field['description'] ) ): ?>
                <span class="tf-field-sub-title"><?php echo wp_kses_post( $field['description'] ) ?></span>
			<?php endif; } ?>
        </div>
		<?php
	}

	public function is_tf_pro_active() {
		if ( is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) && defined( 'TF_PRO' ) ) {
			return true;
		}

		return false;
	}

	function get_icon_list() {
		$icons = array(
			'all'           => array(
				'label'      => esc_html__( 'All Icons', 'tourfic' ),
				'label_icon' => 'ri-grid-fill',
				'icons'      => array_merge( fontawesome_four_icons(), fontawesome_five_icons(), fontawesome_six_icons(), remix_icon() ),
			),
			'fontawesome_4' => array(
				'label'      => esc_html__( 'Font Awesome 4', 'tourfic' ),
				'label_icon' => 'fa-regular fa-font-awesome',
				'icons'      => fontawesome_four_icons(),
			),
			'fontawesome_5' => array(
				'label'      => esc_html__( 'Font Awesome 5', 'tourfic' ),
				'label_icon' => 'fa-regular fa-font-awesome',
				'icons'      => fontawesome_five_icons(),
			),
			'fontawesome_6' => array(
				'label'      => esc_html__( 'Font Awesome 6', 'tourfic' ),
				'label_icon' => 'fa-regular fa-font-awesome',
				'icons'      => fontawesome_six_icons(),
			),
			'remixicon'     => array(
				'label'      => esc_html__( 'Remix Icon', 'tourfic' ),
				'label_icon' => 'ri-remixicon-line',
				'icons'      => remix_icon(),
			),
		);

		$icons = apply_filters( 'tf_icon_list', $icons );

		return $icons;
	}

	function tf_load_more_icons() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$start_index = isset( $_POST['start_index'] ) ? intval( $_POST['start_index'] ) : 0;
		$type        = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'all';
		$search      = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$icon_list   = $this->get_icon_list();
		$icons       = array_slice( $icon_list[ $type ]['icons'], $start_index, 100 );

		if ( ! empty( $search ) ) {
			$icons = array_filter( $icons, function ( $icon ) use ( $search ) {
				return strpos( $icon, $search ) !== false;
			} );
		}

		$icons_html = '';
		foreach ( $icons as $key => $icon ) {
			$icons_html .= '<li data-icon="' . esc_attr( $icon ) . '">
                            <div class="tf-icon-inner">
                                <i title="' . esc_attr( $icon ) . '" class="tf-main-icon ' . esc_attr( $icon ) . '"></i>
                                <span class="check-icon">
                                    <i class="ri-check-line"></i>
                                </span>
                            </div>
                        </li>';
		}

		wp_send_json_success( $icons_html );
	}

	function tf_icon_search() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$search_text = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';
		$type        = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'all';
		$icon_list   = $this->get_icon_list();
		$icons       = $icon_list[ $type ]['icons'];

		$icons = array_filter( $icons, function ( $icon ) use ( $search_text ) {
			return strpos( $icon, $search_text ) !== false;
		} );

		$icons_html = '';
		foreach ( $icons as $key => $icon ) {
			$icons_html .= '<li data-icon="' . esc_attr( $icon ) . '">
                            <div class="tf-icon-inner">
                                <i title="' . esc_attr( $icon ) . '" class="tf-main-icon ' . esc_attr( $icon ) . '"></i>
                                <span class="check-icon">
                                    <i class="ri-check-line"></i>
                                </span>
                            </div>
                        </li>';
		}

		wp_send_json_success( array(
			'html'  => $icons_html,
			'count' => count( $icons )
		) );
	}

	/**
	 * Room availability calendar update
	 * @author Foysal
	 */
	function tf_add_hotel_room_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error( [
				'status'  => false,
				'message' => esc_html__( 'You do not have permission to access this resource.', 'tourfic' )
			] );
			return;
		}

		$date_format         = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
		$room_id             = isset( $_POST['room_id'] ) && ! empty( $_POST['room_id'] ) ? sanitize_text_field( $_POST['room_id'] ) : '';
		$new_post            = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? sanitize_text_field($_POST['new_post']) : '';
		$check_in            = isset( $_POST['tf_room_check_in'] ) && ! empty( $_POST['tf_room_check_in'] ) ? sanitize_text_field( $_POST['tf_room_check_in'] ) : '';
		$check_out           = isset( $_POST['tf_room_check_out'] ) && ! empty( $_POST['tf_room_check_out'] ) ? sanitize_text_field( $_POST['tf_room_check_out'] ) : '';
		$status              = isset( $_POST['tf_room_status'] ) && ! empty( $_POST['tf_room_status'] ) ? sanitize_text_field( $_POST['tf_room_status'] ) : '';
		$price_by            = isset( $_POST['price_by'] ) && ! empty( $_POST['price_by'] ) ? sanitize_text_field( $_POST['price_by'] ) : '';
		$tf_room_price       = isset( $_POST['tf_room_price'] ) && ! empty( $_POST['tf_room_price'] ) ? sanitize_text_field( $_POST['tf_room_price'] ) : '';
		$tf_room_adult_price = isset( $_POST['tf_room_adult_price'] ) && ! empty( $_POST['tf_room_adult_price'] ) ? sanitize_text_field( $_POST['tf_room_adult_price'] ) : '';
		$tf_room_child_price = isset( $_POST['tf_room_child_price'] ) && ! empty( $_POST['tf_room_child_price'] ) ? sanitize_text_field( $_POST['tf_room_child_price'] ) : '';
		$avail_date          = isset( $_POST['avail_date'] ) && ! empty( $_POST['avail_date'] ) ? sanitize_text_field( $_POST['avail_date'] ) : '';
		$options_count       = isset( $_POST['options_count'] ) && ! empty( $_POST['options_count'] ) ? sanitize_text_field( $_POST['options_count'] ) : '';

		$room_meta = get_post_meta( $room_id, 'tf_room_opt', true );
		if(empty($room_meta)){
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Publish the Room First!', 'tourfic' )
			] );
		}

		if ( empty( $check_in ) || empty( $check_out ) ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => esc_html__( 'Please select check in and check out date.', 'tourfic' )
			] );
		}

		$check_in  = strtotime( $this->tf_convert_date_format( $check_in, $date_format ) );
		$check_out = strtotime( $this->tf_convert_date_format( $check_out, $date_format ) );
		if ( $check_in > $check_out ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => esc_html__( 'Check in date must be less than check out date.', 'tourfic' )
			] );
		}


		$room_avail_data = [];
		for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
			$tf_room_date = gmdate( 'Y/m/d', $i );
			$tf_room_data = [
				'check_in'    => $tf_room_date,
				'check_out'   => $tf_room_date,
				'price_by'    => $price_by,
				'price'       => $tf_room_price,
				'adult_price' => $tf_room_adult_price,
				'child_price' => $tf_room_child_price,
				'status'      => $status
			];

            if($price_by == '3') {
	            if ( $options_count != 0 ) {
		            $options_data = [
			            'options_count' => $options_count,
		            ];
		            for ( $j = 0; $j <= $options_count - 1; $j ++ ) {
			            $options_data[ 'tf_room_option_' . $j ]         = isset( $_POST[ 'tf_room_option_' . $j ] ) && ! empty( $_POST[ 'tf_room_option_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_room_option_' . $j ] ) : '';
			            $options_data[ 'tf_option_title_' . $j ]        = isset( $_POST[ 'tf_option_title_' . $j ] ) && ! empty( $_POST[ 'tf_option_title_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_title_' . $j ] ) : '';
			            $options_data[ 'tf_option_pricing_type_' . $j ] = isset( $_POST[ 'tf_option_pricing_type_' . $j ] ) && ! empty( $_POST[ 'tf_option_pricing_type_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_pricing_type_' . $j ] ) : '';
			            $options_data[ 'tf_option_room_price_' . $j ]   = isset( $_POST[ 'tf_option_room_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_room_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_room_price_' . $j ] ) : '';
			            $options_data[ 'tf_option_adult_price_' . $j ]  = isset( $_POST[ 'tf_option_adult_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_adult_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_adult_price_' . $j ] ) : '';
			            $options_data[ 'tf_option_child_price_' . $j ]  = isset( $_POST[ 'tf_option_child_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_child_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_child_price_' . $j ] ) : '';
		            }
	            }
	            if ( ! empty( $options_data ) ) {
		            $tf_room_data = array_merge( $tf_room_data, $options_data );
	            }
            }

			$room_avail_data[ $tf_room_date ] = $tf_room_data;
		}

		if ( $new_post != 'true' ) {
			$avail_date = json_decode( $room_meta['avail_date'], true );
			if ( isset( $avail_date ) && ! empty( $avail_date ) ) {
				$room_avail_data = array_merge( $avail_date, $room_avail_data );
			}
			$room_meta['avail_date'] = wp_json_encode( $room_avail_data );
			update_post_meta( $room_id, 'tf_room_opt', $room_meta );
		} else {
			$avail_date = json_decode( stripslashes( $avail_date ), true );
			if ( isset( $avail_date ) && ! empty( $avail_date ) ) {
				$room_avail_data = array_merge( $avail_date, $room_avail_data );
			}
		}

		wp_send_json_success( [
			'status'     => true,
			'message'    => esc_html__( 'Availability updated successfully.', 'tourfic' ),
			'avail_date' => wp_json_encode( $room_avail_data ),
		] );

		die();
	}

	/*
     * Get room availability calendar
     * @author Foysal
     */
	function tf_get_hotel_room_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$new_post   = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? sanitize_text_field( $_POST['new_post'] ) : '';
		$room_id    = isset( $_POST['room_id'] ) && ! empty( $_POST['room_id'] ) ? sanitize_text_field( $_POST['room_id'] ) : '';
		$avail_date = isset( $_POST['avail_date'] ) && ! empty( $_POST['avail_date'] ) ? sanitize_text_field( $_POST['avail_date'] ) : '';
		$option_arr = isset( $_POST['option_arr'] ) && ! empty( $_POST['option_arr'] ) ? wp_unslash( $_POST['option_arr'] ) : []; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$room_meta  = get_post_meta( $room_id, 'tf_room_opt', true );
        $pricing_by = ! empty( $room_meta['pricing-by'] ) ? $room_meta['pricing-by'] : '1';
		if ( $new_post != 'true' ) {
			$room_avail_data = isset( $room_meta['avail_date'] ) && ! empty( $room_meta['avail_date'] ) ? json_decode( $room_meta['avail_date'], true ) : [];
		} else {
			$room_avail_data = json_decode( stripslashes( $avail_date ), true );
		}

		if ( ! empty( $room_avail_data ) && is_array( $room_avail_data ) ) {
			$room_avail_data = array_values( $room_avail_data );
			$room_avail_data = array_map( function ( $item ) {
				$item['start'] = gmdate( 'Y-m-d', strtotime( $item['check_in'] ) );
				if ( $item['price_by'] == '1' ) {
					$item['title'] = esc_html__( 'Price: ', 'tourfic' ) . wc_price( $item['price'] );
				} elseif ( $item['price_by'] == '2' ) {
					$item['title'] = esc_html__( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . esc_html__( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );
				} elseif ( $item['price_by'] == '3' ) {
					$item['title'] = '';
					if ( ! empty( $item['options_count'] ) ) {
						for ( $i = 0; $i <= $item['options_count'] - 1; $i ++ ) {
							if ( $item[ 'tf_room_option_' . $i ] == '1' && $item['tf_option_pricing_type_'.$i] == 'per_room') {
								$item['title'] .= esc_html__( 'Title: ', 'tourfic' ) . $item['tf_option_title_'.$i] . '<br>';
								$item['title'] .= esc_html__( 'Price: ', 'tourfic' ) . wc_price($item['tf_option_room_price_'.$i]). '<br><br>';
							} else if($item[ 'tf_room_option_' . $i ] == '1' && $item['tf_option_pricing_type_'.$i] == 'per_person'){
								$item['title'] .= esc_html__( 'Title: ', 'tourfic' ) . $item['tf_option_title_'.$i] . '<br>';
								$item['title'] .= esc_html__( 'Adult: ', 'tourfic' ) . wc_price($item['tf_option_adult_price_'.$i]). '<br>';
								$item['title'] .= esc_html__( 'Child: ', 'tourfic' ) . wc_price($item['tf_option_child_price_'.$i]). '<br><br>';
                            }
						}
					}
				}

				if ( $item['status'] == 'unavailable' ) {
					$item['display'] = 'background';
					$item['color']   = '#003c79';
				}

				return $item;
			}, $room_avail_data );
		} else {
			$room_avail_data = [];
		}

		$options_html = '';

        if($pricing_by == '3'){
            foreach ( $option_arr as $key => $item ) {
                ob_start();
				if(empty($item)){
					continue;
				}
                ?>
                <div class="tf-single-option">
                    <div class="tf-field-switch">
                        <label for="tf_room_option_<?php echo esc_attr( $item['index'] ); ?>" class="tf-field-label"><?php echo esc_html( $item['title'] ); ?></label>
                        <div class="tf-fieldset">
                            <label for="tf_room_option_<?php echo esc_attr( $item['index'] ); ?>" class="tf-switch-label" style="width: 80px">
                                <input type="checkbox" id="tf_room_option_<?php echo esc_attr( $item['index'] ); ?>" name="tf_room_option_<?php echo esc_attr( $item['index'] ); ?>" value="1" class="tf-switch"
                                       checked="checked">
                                <span class="tf-switch-slider">
                                    <span class="tf-switch-on"><?php echo esc_html__( 'Enable', 'tourfic' ) ?></span>
                                    <span class="tf-switch-off"><?php echo esc_html__( 'Disable', 'tourfic' ) ?></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="tf-field-number tf_option_pricing_type_room" style="display: <?php echo $item['type'] == 'per_room' ? 'block' : 'none' ?>; width: calc(100% - 90px)">
                        <label class="tf-field-label"><?php echo esc_html__( 'Room Price', 'tourfic' ); ?></label>
                        <div class="tf-fieldset">
                            <input type="number" min="0" name="tf_option_room_price_<?php echo esc_attr( $item['index'] ); ?>" placeholder="<?php echo esc_attr__( 'Room Price', 'tourfic' ); ?>">
                        </div>
                    </div>
                    <div class="tf-field-number tf_option_pricing_type_person" style="display: <?php echo $item['type'] == 'per_person' ? 'block' : 'none' ?>; width: calc((100% - 80px)/2 - -5px)">
                        <label class="tf-field-label"><?php echo esc_html__( 'Adult Price', 'tourfic' ); ?></label>
                        <div class="tf-fieldset">
                            <input type="number" min="0" name="tf_option_adult_price_<?php echo esc_attr( $item['index'] ); ?>" placeholder="<?php echo esc_attr__( 'Adult Price', 'tourfic' ); ?>">
                        </div>
                    </div>
                    <div class="tf-field-number tf_option_pricing_type_person" style="display: <?php echo $item['type'] == 'per_person' ? 'block' : 'none' ?>; width: calc((100% - 80px)/2 - -5px)">
                        <label class="tf-field-label"><?php echo esc_html__( 'Child Price', 'tourfic' ); ?></label>
                        <div class="tf-fieldset">
                            <input type="number" min="0" name="tf_option_child_price_<?php echo esc_attr( $item['index'] ); ?>" placeholder="<?php echo esc_attr__( 'Child Price', 'tourfic' ); ?>">
                        </div>
                    </div>
                    <input type="hidden" name="tf_option_title_<?php echo esc_attr( $item['index'] ); ?>" value="<?php echo esc_attr( $item['title'] ); ?>"/>
                    <input type="hidden" name="tf_option_pricing_type_<?php echo esc_attr( $item['index'] ); ?>" value="<?php echo esc_attr( $item['type'] ); ?>"/>
                </div>
                <?php
                $options_html .= ob_get_clean();
            }
        }

		echo wp_json_encode( array(
			'avail_data'   => $room_avail_data,
			'options_html' => $options_html,
		) );
		die();
	}

	/*
     * Update room avail_date price based on pricing type
     * @auther Foysal
     */
	function tf_update_room_avail_date_price( $post_id, $post ) {
		if ( $post->post_type == 'tf_room' ) {

			$room         = get_post_meta( $post_id, 'tf_room_opt', true );
			$pricing_by   = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
			$price        = ! empty( $room['price'] ) ? $room['price'] : '';
			$adult_price  = ! empty( $room['adult_price'] ) ? $room['adult_price'] : '';
			$child_price  = ! empty( $room['child_price'] ) ? $room['child_price'] : '';
			$avil_by_date = ! empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : '';

			if ( $avil_by_date === '1' && ! empty( $room['avail_date'] ) ) {
				$room_avail_data = json_decode( $room['avail_date'], true );

				if ( isset( $room_avail_data ) && ! empty( $room_avail_data ) ) {

					$room_avail_data = array_map( function ( $item ) use ( $pricing_by, $price, $adult_price, $child_price ) {

						if ( $pricing_by == '1' ) {
							$item['price'] = ! isset( $item['price'] ) ? $price : $item['price'];
						} else if($pricing_by == '2'){
							$item['adult_price'] = ! isset( $item['adult_price'] ) ? $adult_price : $item['adult_price'];
							$item['child_price'] = ! isset( $item['child_price'] ) ? $child_price : $item['child_price'];
						}
						$item['price_by'] = $pricing_by;

						return $item;
					}, $room_avail_data );
				}

				$room['avail_date'] = wp_json_encode( $room_avail_data );
			} elseif ( $avil_by_date === '1' && empty( $room['avail_date'] ) ) {
				//add next 500 days availability
				$room_avail_data = [];
				for ( $i = 0; $i <= 500; $i ++ ) {
					$tf_room_date                     = gmdate( 'Y/m/d', strtotime( "+$i day" ) );
					$tf_room_data                     = [
						'check_in'    => $tf_room_date,
						'check_out'   => $tf_room_date,
						'price_by'    => $pricing_by,
						'price'       => $price,
						'adult_price' => $adult_price,
						'child_price' => $child_price,
						'status'      => 'available'
					];
					$room_avail_data[ $tf_room_date ] = $tf_room_data;
				}

				$room['avail_date'] = wp_json_encode( $room_avail_data );
			}
			update_post_meta( $post_id, 'tf_room_opt', $room );
		}
	}

	/*
     * Reset room availability calendar
     * @auther Foysal
     */
	function tf_reset_room_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		$room_id     = isset( $_POST['room_id'] ) && ! empty( $_POST['room_id'] ) ? sanitize_text_field( $_POST['room_id'] ) : '';
		$room_data = get_post_meta( $room_id, 'tf_room_opt', true );

		if(empty($room_data)){
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Publish the Room First!', 'tourfic' )
			] );
		}

		$room_data['avail_date'] = [];

		update_post_meta( $room_id, 'tf_room_opt', $room_data );
		wp_send_json_success( [
			'status'     => true,
			'message'    => __( 'Availability Reset Successfully.', 'tourfic' ),
			'avail_date' => [],
		] );
		wp_die();
	}

	/*
	 * Apartment availability calendar update
	 * @auther Foysal
	 */
	function tf_add_apartment_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error( [
				'status'  => false,
				'message' => esc_html__( 'You do not have permission to access this resource.', 'tourfic' )
			] );
			return;
		}

		$date_format         = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
		$apartment_id        = isset( $_POST['apartment_id'] ) && ! empty( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : '';
		$new_post            = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? sanitize_text_field($_POST['new_post']) : '';
		$check_in            = isset( $_POST['tf_apt_check_in'] ) && ! empty( $_POST['tf_apt_check_in'] ) ? sanitize_text_field( $_POST['tf_apt_check_in'] ) : '';
		$check_out           = isset( $_POST['tf_apt_check_out'] ) && ! empty( $_POST['tf_apt_check_out'] ) ? sanitize_text_field( $_POST['tf_apt_check_out'] ) : '';
		$status              = isset( $_POST['tf_apt_status'] ) && ! empty( $_POST['tf_apt_status'] ) ? sanitize_text_field( $_POST['tf_apt_status'] ) : '';
		$pricing_type        = isset( $_POST['pricing_type'] ) && ! empty( $_POST['pricing_type'] ) ? sanitize_text_field( $_POST['pricing_type'] ) : '';
		$tf_apt_price        = isset( $_POST['tf_apt_price'] ) && ! empty( $_POST['tf_apt_price'] ) ? sanitize_text_field( $_POST['tf_apt_price'] ) : '';
		$tf_apt_adult_price  = isset( $_POST['tf_apt_adult_price'] ) && ! empty( $_POST['tf_apt_adult_price'] ) ? sanitize_text_field( $_POST['tf_apt_adult_price'] ) : '';
		$tf_apt_child_price  = isset( $_POST['tf_apt_child_price'] ) && ! empty( $_POST['tf_apt_child_price'] ) ? sanitize_text_field( $_POST['tf_apt_child_price'] ) : '';
		$tf_apt_infant_price = isset( $_POST['tf_apt_infant_price'] ) && ! empty( $_POST['tf_apt_infant_price'] ) ? sanitize_text_field( $_POST['tf_apt_infant_price'] ) : '';
		$apt_availability    = isset( $_POST['apt_availability'] ) && ! empty( $_POST['apt_availability'] ) ? sanitize_text_field( $_POST['apt_availability'] ) : '';

		$apartment_meta = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
		if(empty($apartment_meta)){
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Publish the Apartment First!', 'tourfic' )
			] );
		}

		if ( empty( $check_in ) || empty( $check_out ) ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => esc_html__( 'Please select check in and check out date.', 'tourfic' )
			] );
		}

		/*if ( $date_format == 'Y.m.d' || $date_format == 'd.m.Y' ) {
			$check_in  = gmdate( "Y-m-d", strtotime( str_replace( ".", "-", $check_in ) ) );
			$check_out = gmdate( "Y-m-d", strtotime( str_replace( ".", "-", $check_out ) ) );
		}
		if ( $date_format == 'd/m/Y' ) {
			$check_in  = gmdate( "Y-m-d", strtotime( str_replace( "/", "-", $check_in ) ) );
			$check_out = gmdate( "Y-m-d", strtotime( str_replace( "/", "-", $check_out ) ) );
		}*/

		$check_in  = strtotime( $this->tf_convert_date_format( $check_in, $date_format ) );
		$check_out = strtotime( $this->tf_convert_date_format( $check_out, $date_format ) );
		if ( $check_in > $check_out ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => esc_html__( 'Check in date must be less than check out date.', 'tourfic' )
			] );
		}

		$apt_availability_data = [];
		for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
			$tf_apt_date                           = gmdate( 'Y/m/d', $i );
			$tf_apt_data                           = [
				'check_in'     => $tf_apt_date,
				'check_out'    => $tf_apt_date,
				'pricing_type' => $pricing_type,
				'price'        => $tf_apt_price,
				'adult_price'  => $tf_apt_adult_price,
				'child_price'  => $tf_apt_child_price,
				'infant_price' => $tf_apt_infant_price,
				'status'       => $status
			];
			$apt_availability_data[ $tf_apt_date ] = $tf_apt_data;
		}

		if ( $new_post != 'true' ) {
			$apt_availability = !empty($apartment_meta['apt_availability']) ? json_decode( $apartment_meta['apt_availability'], true ) : [];

			if ( isset( $apt_availability ) && ! empty( $apt_availability ) ) {
				$apt_availability_data = array_merge( $apt_availability, $apt_availability_data );
			}
			$apartment_meta['apt_availability'] = wp_json_encode( $apt_availability_data );
			update_post_meta( $apartment_id, 'tf_apartment_opt', $apartment_meta );
		} else {
			$apt_availability = json_decode( stripslashes( $apt_availability ), true );
			if ( isset( $apt_availability ) && ! empty( $apt_availability ) ) {
				$apt_availability_data = array_merge( $apt_availability, $apt_availability_data );
			}
			$apartment_meta['apt_availability'] = wp_json_encode( $apt_availability_data );
			update_post_meta( $apartment_id, 'tf_apartment_opt', $apartment_meta );
		}

		wp_send_json_success( [
			'status'           => true,
			'message'          => esc_html__( 'Availability updated successfully.', 'tourfic' ),
			'apt_availability' => wp_json_encode( $apt_availability_data ),
		] );

		die();
	}

	/*
     * Get apartment availability calendar
     * @auther Foysal
     */
	function tf_get_apartment_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$new_post         = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? sanitize_text_field( $_POST['new_post'] ) : '';
		$apartment_id     = isset( $_POST['apartment_id'] ) && ! empty( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : '';
		$apt_availability = isset( $_POST['apt_availability'] ) && ! empty( $_POST['apt_availability'] ) ? sanitize_text_field( $_POST['apt_availability'] ) : '';

		if ( $new_post != 'true' ) {
			$apartment_data        = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
			$apt_availability_data = isset( $apartment_data['apt_availability'] ) && ! empty( $apartment_data['apt_availability'] ) ? json_decode( $apartment_data['apt_availability'], true ) : [];
		} else {
			$apt_availability_data = json_decode( stripslashes( $apt_availability ), true );
		}

		if ( ! empty( $apt_availability_data ) && is_array( $apt_availability_data ) ) {
			$apt_availability_data = array_values( $apt_availability_data );
			$apt_availability_data = array_map( function ( $item ) {
				$item['start'] = gmdate( 'Y-m-d', strtotime( $item['check_in'] ) );
				$item['title'] = $item['pricing_type'] == 'per_night' ? esc_html__( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) : esc_html__( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . esc_html__( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] ) . '<br>' . esc_html__( 'Infant: ', 'tourfic' ) . wc_price( $item['infant_price'] );

				if ( $item['status'] == 'unavailable' ) {
					$item['display'] = 'background';
					$item['color']   = '#003c79';
				}

				return $item;
			}, $apt_availability_data );
		} else {
			$apt_availability_data = [];
		}

		echo wp_json_encode( $apt_availability_data );
		die();
	}

	/*
     * Reset apartment availability calendar
     * @auther Foysal
     */
	function tf_reset_apt_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		$apartment_id     = isset( $_POST['apartment_id'] ) && ! empty( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : '';
		$apartment_data = get_post_meta( $apartment_id, 'tf_apartment_opt', true );

		if(empty($apartment_data)){
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Publish the Apartment First!', 'tourfic' )
			] );
		}
		
		$apartment_data['apt_availability'] = [];

		update_post_meta( $apartment_id, 'tf_apartment_opt', $apartment_data );
		wp_send_json_success( [
			'status'     => true,
			'message'    => __( 'Availability Reset Successfully.', 'tourfic' ),
			'apt_availability' => [],
		] );
		wp_die();
	}

	/*
	 * Tour availability calendar update
	 * @auther Jahid
	 */
	function tf_add_tour_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'You do not have permission to access this resource.', 'tourfic' )
			] );
			return;
		}

		$date_format         = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
		$tour_id        = isset( $_POST['tour_id'] ) && ! empty( $_POST['tour_id'] ) ? sanitize_text_field( $_POST['tour_id'] ) : '';
		$new_post            = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? $_POST['new_post'] : '';
		$check_in            = isset( $_POST['tf_tour_check_in'] ) && ! empty( $_POST['tf_tour_check_in'] ) ? sanitize_text_field( $_POST['tf_tour_check_in'] ) : '';
		$check_out           = isset( $_POST['tf_tour_check_out'] ) && ! empty( $_POST['tf_tour_check_out'] ) ? sanitize_text_field( $_POST['tf_tour_check_out'] ) : '';
		$status              = isset( $_POST['tf_tour_status'] ) && ! empty( $_POST['tf_tour_status'] ) ? sanitize_text_field( $_POST['tf_tour_status'] ) : '';
		$pricing_type        = isset( $_POST['pricing_type'] ) && ! empty( $_POST['pricing_type'] ) ? sanitize_text_field( $_POST['pricing_type'] ) : '';
		$tf_tour_price        = isset( $_POST['tf_tour_price'] ) && ! empty( $_POST['tf_tour_price'] ) ? sanitize_text_field( $_POST['tf_tour_price'] ) : '';
		$tf_tour_adult_price  = isset( $_POST['tf_tour_adult_price'] ) && ! empty( $_POST['tf_tour_adult_price'] ) ? sanitize_text_field( $_POST['tf_tour_adult_price'] ) : '';
		$tf_tour_child_price  = isset( $_POST['tf_tour_child_price'] ) && ! empty( $_POST['tf_tour_child_price'] ) ? sanitize_text_field( $_POST['tf_tour_child_price'] ) : '';
		$tf_tour_infant_price = isset( $_POST['tf_tour_infant_price'] ) && ! empty( $_POST['tf_tour_infant_price'] ) ? sanitize_text_field( $_POST['tf_tour_infant_price'] ) : '';
		$tour_availability    = isset( $_POST['tour_availability'] ) && ! empty( $_POST['tour_availability'] ) ? sanitize_text_field( $_POST['tour_availability'] ) : '';
		$options_count       = isset( $_POST['options_count'] ) && ! empty( $_POST['options_count'] ) ? sanitize_text_field( $_POST['options_count'] ) : '';

		$tf_tour_min_person	 = isset( $_POST['tf_tour_min_person'] ) && ! empty( $_POST['tf_tour_min_person'] ) ? sanitize_text_field( $_POST['tf_tour_min_person'] ) : '';
		$tf_tour_max_person	 = isset( $_POST['tf_tour_max_person'] ) && ! empty( $_POST['tf_tour_max_person'] ) ? sanitize_text_field( $_POST['tf_tour_max_person'] ) : '';
		$tf_tour_max_capacity	 = isset( $_POST['tf_tour_max_capacity'] ) && ! empty( $_POST['tf_tour_max_capacity'] ) ? sanitize_text_field( $_POST['tf_tour_max_capacity'] ) : '';

		$tf_tour_repeat_month = isset( $_POST['tf_tour_repeat_month'] ) && ! empty( $_POST['tf_tour_repeat_month'] ) ? $_POST['tf_tour_repeat_month'] : '';
		$tf_tour_repeat_year = isset( $_POST['tf_tour_repeat_year'] ) && ! empty( $_POST['tf_tour_repeat_year'] ) ? $_POST['tf_tour_repeat_year'] : '';
		$tf_tour_repeat_week = isset( $_POST['tf_tour_repeat_week'] ) && ! empty( $_POST['tf_tour_repeat_week'] ) ? $_POST['tf_tour_repeat_week'] : '';
		$tf_tour_repeat_day = isset( $_POST['tf_tour_repeat_day'] ) && ! empty( $_POST['tf_tour_repeat_day'] ) ? $_POST['tf_tour_repeat_day'] : '';


		$tf_tour_allowed_time = isset( $_POST['allowed_time'] ) && ! empty( $_POST['allowed_time'] ) ? $_POST['allowed_time'] : ''; 
		
		$bulk_edit_option = isset( $_POST['bulk_edit_option'] ) && ! empty( $_POST['bulk_edit_option'] ) ? $_POST['bulk_edit_option'] : ''; 

		if ( empty($bulk_edit_option) && ( empty( $check_in ) || empty( $check_out ) ) ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Please select check in and check out date.', 'tourfic' )
			] );
		}

		if ( !empty($bulk_edit_option) && empty( $tf_tour_repeat_month ) ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Please select the months.', 'tourfic' )
			] );
		}

		if ( !empty($bulk_edit_option) && empty( $tf_tour_repeat_year ) ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Please select the years.', 'tourfic' )
			] );
		}

		$meta = get_post_meta( $tour_id, 'tf_tours_opt', true );

		$package_pricing = ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : '';

		$check_in  = strtotime( $this->tf_convert_date_format( $check_in, $date_format ) );
		$check_out = strtotime( $this->tf_convert_date_format( $check_out, $date_format ) );
		if ( $check_in > $check_out ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Check in date must be less than check out date.', 'tourfic' )
			] );
		}

		if($pricing_type == 'person') {
			if(empty($tf_tour_adult_price)){
				$tf_tour_adult_price = !empty( $meta['adult_price'] ) ? $meta['adult_price'] : '';
			}
			if(empty($tf_tour_child_price)){
				$tf_tour_child_price = !empty( $meta['child_price'] ) ? $meta['child_price'] : '';
			}
			if(empty($tf_tour_infant_price)){
				$tf_tour_infant_price = !empty( $meta['infant_price'] ) ? $meta['infant_price'] : '';
			}
		}
		if($pricing_type == 'group') {
			if(empty($tf_tour_price)){
				$tf_tour_price = !empty( $meta['group_price'] ) ? $meta['group_price'] : '';
			}
		}

		$tour_availability_data = [];

		if ( !empty($bulk_edit_option) ) {
			if (!empty($tf_tour_repeat_year)) {
				foreach ($tf_tour_repeat_year as $year) {
					if (!empty($tf_tour_repeat_month)) {
						foreach ($tf_tour_repeat_month as $month) {

							// Date Number wise
							if(!empty($tf_tour_repeat_day)){
								foreach ($tf_tour_repeat_day as $day) {
									$month = str_pad($month, 2, '0', STR_PAD_LEFT);

									$new_check_in_str = "$year-$month-$day";
									$new_check_in = strtotime($new_check_in_str);
									$tf_checkin_date = gmdate('Y/m/d', $new_check_in);
									$day_number = gmdate( 'w', strtotime( $tf_checkin_date ) );

									$tf_tour_date = $tf_checkin_date . ' - ' . $tf_checkin_date;
									$tf_tour_data = [
										'check_in'     => $tf_checkin_date,
										'check_out'    => $tf_checkin_date,
										'pricing_type' => $pricing_type,
										'price'        => $tf_tour_price,
										'adult_price'  => $tf_tour_adult_price,
										'child_price'  => $tf_tour_child_price,
										'infant_price' => $tf_tour_infant_price,
										'min_person'   => $tf_tour_min_person,
										'max_person'   => $tf_tour_max_person,
										'max_capacity' => $tf_tour_max_capacity,
										'allowed_time' => !empty($tf_tour_allowed_time) ? $tf_tour_allowed_time : '',
										'status'       => $status
									];

									if($pricing_type == 'package') {
										if ( $options_count != 0 ) {
											$options_data = [
												'options_count' => $options_count,
											];
											for ( $j = 0; $j <= $options_count - 1; $j ++ ) {
												$options_data[ 'tf_package_option_' . $j ]         = isset( $_POST[ 'tf_package_option_' . $j ] ) && ! empty( $_POST[ 'tf_package_option_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_package_option_' . $j ] ) : '';
												$options_data[ 'tf_option_title_' . $j ]        = isset( $_POST[ 'tf_option_title_' . $j ] ) && ! empty( $_POST[ 'tf_option_title_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_title_' . $j ] ) : '';
												$options_data[ 'tf_option_pricing_type_' . $j ] = isset( $_POST[ 'tf_option_pricing_type_' . $j ] ) && ! empty( $_POST[ 'tf_option_pricing_type_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_pricing_type_' . $j ] ) : '';

												$options_data[ 'tf_option_group_price_' . $j ]   = isset( $_POST[ 'tf_option_group_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_group_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_group_price_' . $j ] ) : '';

												$options_data[ 'tf_option_group_discount_' . $j ]  = isset( $_POST[ 'tf_option_' . $j.'_group_discount' ] ) && ! empty( $_POST[ 'tf_option_' . $j.'_group_discount' ] ) ? $_POST[ 'tf_option_' . $j.'_group_discount' ] : '';

												$options_data[ 'tf_option_adult_price_' . $j ]  = isset( $_POST[ 'tf_option_adult_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_adult_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_adult_price_' . $j ] ) : '';
												$options_data[ 'tf_option_child_price_' . $j ]  = isset( $_POST[ 'tf_option_child_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_child_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_child_price_' . $j ] ) : '';
												$options_data[ 'tf_option_infant_price_' . $j ]  = isset( $_POST[ 'tf_option_infant_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_infant_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_infant_price_' . $j ] ) : '';

												$options_data[ 'tf_option_times_' . $j ]  = isset( $_POST[ 'tf_option_' . $j.'_allowed_time' ] ) && ! empty( $_POST[ 'tf_option_' . $j.'_allowed_time' ] ) ? $_POST[ 'tf_option_' . $j.'_allowed_time' ] : '';
											}
										}
										if ( ! empty( $options_data ) ) {
											$tf_tour_data = array_merge( $tf_tour_data, $options_data );
										}
									}

									$tour_availability_data[$tf_tour_date] = $tf_tour_data;
								}
							}

							// Date Day wise
							if(!empty($tf_tour_repeat_week)){
								$month = str_pad($month, 2, '0', STR_PAD_LEFT);
								$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

								for ($day = 1; $day <= $days_in_month; $day++) {
									$day = str_pad($day, 2, '0', STR_PAD_LEFT);
									$date_str = "$year-$month-$day";
									$timestamp = strtotime($date_str);

									if ($timestamp === false) {
										continue;
									}
									$day_number = (int) gmdate('w', $timestamp);

									if (!in_array((string) $day_number, $tf_tour_repeat_week)) {
										continue;
									}
									$tf_checkin_date = gmdate('Y/m/d', $timestamp);
									$tf_tour_date = $tf_checkin_date . ' - ' . $tf_checkin_date;

									$tf_tour_data = [
										'check_in'     => $tf_checkin_date,
										'check_out'    => $tf_checkin_date,
										'pricing_type' => $pricing_type,
										'price'        => $tf_tour_price,
										'adult_price'  => $tf_tour_adult_price,
										'child_price'  => $tf_tour_child_price,
										'infant_price' => $tf_tour_infant_price,
										'min_person'   => $tf_tour_min_person,
										'max_person'   => $tf_tour_max_person,
										'max_capacity' => $tf_tour_max_capacity,
										'allowed_time' => !empty($tf_tour_allowed_time) ? $tf_tour_allowed_time : '',
										'status'       => $status
									];
	
									if($pricing_type == 'package') {
										if ( $options_count != 0 ) {
											$options_data = [
												'options_count' => $options_count,
											];
											for ( $j = 0; $j <= $options_count - 1; $j ++ ) {
												// $options_data[ 'tf_package_option_' . $j ]         = isset( $_POST[ 'tf_package_option_' . $j ] ) && ! empty( $_POST[ 'tf_package_option_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_package_option_' . $j ] ) : '';
												$options_data[ 'tf_option_title_' . $j ]        = isset( $_POST[ 'tf_option_title_' . $j ] ) && ! empty( $_POST[ 'tf_option_title_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_title_' . $j ] ) : '';
												$options_data[ 'tf_option_pricing_type_' . $j ] = isset( $_POST[ 'tf_option_pricing_type_' . $j ] ) && ! empty( $_POST[ 'tf_option_pricing_type_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_pricing_type_' . $j ] ) : '';
												$options_data[ 'tf_option_group_price_' . $j ]   = isset( $_POST[ 'tf_option_group_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_group_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_group_price_' . $j ] ) : '';
												$options_data[ 'tf_option_adult_price_' . $j ]  = isset( $_POST[ 'tf_option_adult_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_adult_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_adult_price_' . $j ] ) : '';
												$options_data[ 'tf_option_child_price_' . $j ]  = isset( $_POST[ 'tf_option_child_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_child_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_child_price_' . $j ] ) : '';
												$options_data[ 'tf_option_infant_price_' . $j ]  = isset( $_POST[ 'tf_option_infant_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_infant_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_infant_price_' . $j ] ) : '';

												$options_data[ 'tf_option_times_' . $j ]  = isset( $_POST[ 'tf_option_' . $j.'_allowed_time' ] ) && ! empty( $_POST[ 'tf_option_' . $j.'_allowed_time' ] ) ? $_POST[ 'tf_option_' . $j.'_allowed_time' ] : '';
											}
										}
										if ( ! empty( $options_data ) ) {
											$tf_tour_data = array_merge( $tf_tour_data, $options_data );
										}
									}
	
									$tour_availability_data[$tf_tour_date] = $tf_tour_data;
								}
							}

							if(empty($tf_tour_repeat_day) && empty($tf_tour_repeat_week)){
								// Get the total days in the month
								$total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

								// Create the array of day numbers
								$tf_tour_repeat_day = range(1, $total_days);

								foreach ($tf_tour_repeat_day as $day) {
									$month_padded = str_pad($month, 2, '0', STR_PAD_LEFT);
									$day_padded   = str_pad($day, 2, '0', STR_PAD_LEFT);

									$new_check_in_str = "$year-$month_padded-$day_padded";
									$new_check_in     = strtotime($new_check_in_str);
									$tf_checkin_date  = gmdate('Y/m/d', $new_check_in);
									$day_number       = gmdate('w', strtotime($tf_checkin_date));

									$tf_tour_date = $tf_checkin_date . ' - ' . $tf_checkin_date;
									$tf_tour_data = [
										'check_in'     => $tf_checkin_date,
										'check_out'    => $tf_checkin_date,
										'pricing_type' => $pricing_type,
										'price'        => $tf_tour_price,
										'adult_price'  => $tf_tour_adult_price,
										'child_price'  => $tf_tour_child_price,
										'infant_price' => $tf_tour_infant_price,
										'min_person'   => $tf_tour_min_person,
										'max_person'   => $tf_tour_max_person,
										'max_capacity' => $tf_tour_max_capacity,
										'allowed_time' => !empty($tf_tour_allowed_time) ? $tf_tour_allowed_time : '',
										'status'       => $status
									];

									if($pricing_type == 'package') {
										if ( $options_count != 0 ) {
											$options_data = [
												'options_count' => $options_count,
											];
											for ( $j = 0; $j <= $options_count - 1; $j ++ ) {
												$options_data[ 'tf_package_option_' . $j ]         = isset( $_POST[ 'tf_package_option_' . $j ] ) && ! empty( $_POST[ 'tf_package_option_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_package_option_' . $j ] ) : '';
												$options_data[ 'tf_option_title_' . $j ]        = isset( $_POST[ 'tf_option_title_' . $j ] ) && ! empty( $_POST[ 'tf_option_title_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_title_' . $j ] ) : '';
												$options_data[ 'tf_option_pricing_type_' . $j ] = isset( $_POST[ 'tf_option_pricing_type_' . $j ] ) && ! empty( $_POST[ 'tf_option_pricing_type_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_pricing_type_' . $j ] ) : '';
												$options_data[ 'tf_option_group_price_' . $j ]   = isset( $_POST[ 'tf_option_group_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_group_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_group_price_' . $j ] ) : '';
												$options_data[ 'tf_option_adult_price_' . $j ]  = isset( $_POST[ 'tf_option_adult_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_adult_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_adult_price_' . $j ] ) : '';
												$options_data[ 'tf_option_child_price_' . $j ]  = isset( $_POST[ 'tf_option_child_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_child_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_child_price_' . $j ] ) : '';
												$options_data[ 'tf_option_infant_price_' . $j ]  = isset( $_POST[ 'tf_option_infant_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_infant_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_infant_price_' . $j ] ) : '';

												$options_data[ 'tf_option_times_' . $j ]  = isset( $_POST[ 'tf_option_' . $j.'_allowed_time' ] ) && ! empty( $_POST[ 'tf_option_' . $j.'_allowed_time' ] ) ? $_POST[ 'tf_option_' . $j.'_allowed_time' ] : '';
											}
										}
										if ( ! empty( $options_data ) ) {
											$tf_tour_data = array_merge( $tf_tour_data, $options_data );
										}
									}

									$tour_availability_data[$tf_tour_date] = $tf_tour_data;
								}
							}
						}
					}
				}
			}
		}else{
			$tf_checkin_date = gmdate( 'Y/m/d', $check_in );
			$tf_checkout_date = gmdate( 'Y/m/d', $check_out );
			$tf_tour_date = $tf_checkin_date . ' - ' . $tf_checkout_date;
			$tf_tour_data = [
				'check_in'     => $tf_checkin_date,
				'check_out'    => $tf_checkout_date,
				'pricing_type' => $pricing_type,
				'price'        => $tf_tour_price,
				'adult_price'  => $tf_tour_adult_price,
				'child_price'  => $tf_tour_child_price,
				'infant_price' => $tf_tour_infant_price,
				'min_person'   => $tf_tour_min_person,
				'max_person'   => $tf_tour_max_person,
				'max_capacity' => $tf_tour_max_capacity,
				'allowed_time' => !empty($tf_tour_allowed_time) ? $tf_tour_allowed_time : '',
				'status'       => $status
			];

			if($pricing_type == 'package') {
				if ( $options_count != 0 ) {
					$options_data = [
						'options_count' => $options_count,
					];
					for ( $j = 0; $j <= $options_count - 1; $j ++ ) {
						$options_data[ 'tf_option_title_' . $j ]        = isset( $_POST[ 'tf_option_title_' . $j ] ) && ! empty( $_POST[ 'tf_option_title_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_title_' . $j ] ) : '';

						$options_data[ 'tf_option_pricing_type_' . $j ] = isset( $_POST[ 'tf_option_pricing_type_' . $j ] ) && ! empty( $_POST[ 'tf_option_pricing_type_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_pricing_type_' . $j ] ) : '';
						if(!empty($options_data[ 'tf_option_pricing_type_' . $j ]) && $options_data[ 'tf_option_pricing_type_' . $j ]=='group'){
							// Base Price
							$package_group_base_price = !empty($package_pricing[$j]['group_tabs'][1]['group_price']) ? $package_pricing[$j]['group_tabs'][1]['group_price'] : '';

							$options_data[ 'tf_option_group_price_' . $j ]   = isset( $_POST[ 'tf_option_group_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_group_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_group_price_' . $j ] ) : $package_group_base_price;

							$options_data[ 'tf_option_group_discount_' . $j ]  = isset( $_POST[ 'tf_option_' . $j.'_group_discount' ] ) && ! empty( $_POST[ 'tf_option_' . $j.'_group_discount' ] ) ? $_POST[ 'tf_option_' . $j.'_group_discount' ] : '';
						}

						if(!empty($options_data[ 'tf_option_pricing_type_' . $j ]) && $options_data[ 'tf_option_pricing_type_' . $j ]=='person'){
							// Adult Base Price
							$package_adult_base_price = !empty($package_pricing[$j]['adult_tabs'][1]['adult_price']) ? $package_pricing[$j]['adult_tabs'][1]['adult_price'] : '';

							$options_data[ 'tf_option_adult_price_' . $j ]  = isset( $_POST[ 'tf_option_adult_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_adult_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_adult_price_' . $j ] ) : $package_adult_base_price;

							// Child Base Price
							$package_child_base_price = !empty($package_pricing[$j]['child_tabs'][1]['child_price']) ? $package_pricing[$j]['child_tabs'][1]['child_price'] : '';

							$options_data[ 'tf_option_child_price_' . $j ]  = isset( $_POST[ 'tf_option_child_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_child_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_child_price_' . $j ] ) : $package_child_base_price;

							// Infant Base Price
							$package_infant_base_price = !empty($package_pricing[$j]['infant_tabs'][1]['infant_price']) ? $package_pricing[$j]['infant_tabs'][1]['infant_price'] : '';

							$options_data[ 'tf_option_infant_price_' . $j ]  = isset( $_POST[ 'tf_option_infant_price_' . $j ] ) && ! empty( $_POST[ 'tf_option_infant_price_' . $j ] ) ? sanitize_text_field( $_POST[ 'tf_option_infant_price_' . $j ] ) : $package_infant_base_price;
						}

						$options_data[ 'tf_option_times_' . $j ]  = isset( $_POST[ 'tf_option_' . $j.'_allowed_time' ] ) && ! empty( $_POST[ 'tf_option_' . $j.'_allowed_time' ] ) ? $_POST[ 'tf_option_' . $j.'_allowed_time' ] : '';
					}
				}
				if ( ! empty( $options_data ) ) {
					$tf_tour_data = array_merge( $tf_tour_data, $options_data );
				}
			}

			$tour_availability_data[$tf_tour_date] = $tf_tour_data;
		}

		$tour_data = get_post_meta( $tour_id, 'tf_tours_opt', true );
		if(empty($tour_data)){
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Publish the Tour First!', 'tourfic' )
			] );
		}
		if ( $new_post != 'true' ) {
			$tour_availability = !empty($tour_data['tour_availability']) ? json_decode( $tour_data['tour_availability'], true ) : [];

			if ( isset( $tour_availability ) && ! empty( $tour_availability ) ) {
				$tour_availability_data = array_merge( $tour_availability, $tour_availability_data );
			}
			$tour_data['tour_availability'] = wp_json_encode( $tour_availability_data );
			update_post_meta( $tour_id, 'tf_tours_opt', $tour_data );
		} else {
			$tour_availability = json_decode( stripslashes( $tour_availability ), true );
			if ( isset( $tour_availability ) && ! empty( $tour_availability ) ) {
				$tour_availability_data = array_merge( $tour_availability, $tour_availability_data );
			}

			$tour_data['tour_availability'] = wp_json_encode( $tour_availability_data );
			update_post_meta( $tour_id, 'tf_tours_opt', $tour_data );
		}

		wp_send_json_success( [
			'status'           => true,
			'message'          => __( 'Availability updated successfully.', 'tourfic' ),
			'tour_availability' => wp_json_encode( $tour_availability_data ),
		] );

		die();
	}

	/*
     * Get tour availability calendar
     * @auther Jahid
     */
	function tf_get_tour_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$new_post         = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? sanitize_text_field( $_POST['new_post'] ) : '';
		$tour_id     = isset( $_POST['tour_id'] ) && ! empty( $_POST['tour_id'] ) ? sanitize_text_field( $_POST['tour_id'] ) : '';
		$tour_availability = isset( $_POST['tour_availability'] ) && ! empty( $_POST['tour_availability'] ) ? sanitize_text_field( $_POST['tour_availability'] ) : '';
		$option_arr = isset( $_POST['option_arr'] ) && ! empty( $_POST['option_arr'] ) ? $_POST['option_arr'] : [];
		$group_option_arr = isset( $_POST['group_option_arr'] ) && ! empty( $_POST['group_option_arr'] ) ? $_POST['group_option_arr'] : [];

		$tour_data        = get_post_meta( $tour_id, 'tf_tours_opt', true );
		$pricing_by = ! empty( $tour_data['pricing'] ) ? $tour_data['pricing'] : 'person';
		if ( $new_post != 'true' ) {
			$tour_availability_data = isset( $tour_data['tour_availability'] ) && ! empty( $tour_data['tour_availability'] ) ? json_decode( $tour_data['tour_availability'], true ) : [];
		} else {
			$tour_availability_data = json_decode( stripslashes( $tour_availability ), true );
		}

		$group_package_option = ! empty( $tour_data['allow_package_pricing'] ) ? $tour_data['allow_package_pricing'] : '';
        $group_package_pricing = ! empty( $tour_data['group_package_pricing'] ) ? $tour_data['group_package_pricing'] : '';
        $package_pricing = ! empty( $tour_data['package_pricing'] ) ? $tour_data['package_pricing'] : '';

		if ( ! empty( $tour_availability_data ) && is_array( $tour_availability_data ) ) {
			$tour_availability_data = array_values( $tour_availability_data );
			$tour_availability_data = array_map( function ( $item ) use ($group_package_option, $group_package_pricing) {	

				$time_string = '';
				if($item['pricing_type'] == 'group' || $item['pricing_type'] == 'person'){
					$active_times =  $item['allowed_time'] ? $item['allowed_time'] : ''; 
					if(!empty($active_times["time"])){
						$active_time = implode(', ', array_filter($active_times['time']));
					}
					if(!empty($active_time)){
						$time_string = 'Time: '.$active_time;
					}
				}
				if ( $item['pricing_type'] == 'group' ) {
					$item['title'] = __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) . '<br>'. $time_string;
				} elseif ( $item['pricing_type'] == 'person' ) {
					$item['title'] = __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] ). '<br>' . __( 'Infant: ', 'tourfic' ) . wc_price( $item['infant_price'] ). '<br>'. $time_string;
				} elseif ( $item['pricing_type'] == 'package' ) {
					$item['title'] = '';
					if ( ! empty( $item['options_count'] ) ) {
						for ( $i = 0; $i <= $item['options_count'] - 1; $i ++ ) {
							$package_active_times =  !empty($item['tf_option_times_'.$i]) ? $item['tf_option_times_'.$i] : ''; 
							if(!empty($package_active_times["time"])){
								$package_active_time = implode(', ', array_filter($package_active_times['time']));
							}

							if ( $item['tf_option_pricing_type_'.$i] == 'group') {
								$item['title'] .= __( 'Title: ', 'tourfic' ) . $item['tf_option_title_'.$i] . '<br>';
								$item['title'] .= __( 'Group Price: ', 'tourfic' ) . wc_price($item['tf_option_group_price_'.$i]). '<br>';
								$item['title'] .=  !empty($package_active_time) ? 'Time: '.$package_active_time. '<br><br>' : '';
							} else if($item['tf_option_pricing_type_'.$i] == 'person'){
								$item['title'] .= __( 'Title: ', 'tourfic' ) . $item['tf_option_title_'.$i] . '<br>';
								$item['title'] .= __( 'Adult: ', 'tourfic' ) . wc_price($item['tf_option_adult_price_'.$i]). '<br>';
								$item['title'] .= __( 'Child: ', 'tourfic' ) . wc_price($item['tf_option_child_price_'.$i]). '<br>';
								$item['title'] .= __( 'Infant: ', 'tourfic' ) . wc_price($item['tf_option_infant_price_'.$i]). '<br>';
								$item['title'] .=  !empty($package_active_time) ? 'Time: '.$package_active_time. '<br><br>' : '';
                            }
						}
					}
				}

				if(!empty($item['title'])){
					$item['start'] = gmdate( 'Y-m-d', strtotime( $item['check_in'] ) );
					$item['end'] = gmdate('Y-m-d', strtotime($item['check_out'] . ' +1 day'));
				}
				if ( $item['status'] == 'unavailable' ) {
					$item['customClass']   = 'tf_tour_disable_date';
				}

				return $item;
			}, $tour_availability_data );
		} else {
			$tour_availability_data = [];
		}

		$options_html = '';

		ob_start();
        if($pricing_by == 'package' && function_exists( 'is_tf_pro' ) && is_tf_pro()){ ?>
			<div class="tf-repeater">
			<div class="tf-field" style="padding-top: 0px">
				<label class="tf-field-label"><?php echo esc_html__('Packages', 'tourfic'); ?></label>
				<div class="tf-field-sub-title">
					<?php echo esc_html__('You can add, customize any packages from here.', 'tourfic'); ?>
				</div>
			</div>
			<div class="tf-repeater-wrap">
            <?php foreach ( $option_arr as $key => $item ) {
				if(empty($item)){
					continue;
				}
				if(empty($package_pricing[$key]['pack_status'])){
					continue;
				}
                ?>
				<div class="tf-single-repeater">
					<div class="tf-repeater-header">
						<div class="tf-repeater-header-info">
							<span class="tf-repeater-title tf-avail-repeater-title"><?php echo esc_html( $item['title'] ); ?></span>
							<div class="tf-repeater-icon-absulate">
								<span class="tf-repeater-icon tf-repeater-icon-collapse tf-avail-repeater-collapse">
									<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M8 13.332H14" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M11 2.33218C11.2652 2.06697 11.6249 1.91797 12 1.91797C12.1857 1.91797 12.3696 1.95455 12.5412 2.02562C12.7128 2.09669 12.8687 2.20086 13 2.33218C13.1313 2.4635 13.2355 2.61941 13.3066 2.79099C13.3776 2.96257 13.4142 3.14647 13.4142 3.33218C13.4142 3.5179 13.3776 3.7018 13.3066 3.87338C13.2355 4.04496 13.1313 4.20086 13 4.33218L4.66667 12.6655L2 13.3322L2.66667 10.6655L11 2.33218Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										<path d="M10 3.33203L12 5.33203" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span>
							</div>
						</div>
					</div>

					<div class="tf-repeater-content-wrap" style="display: none;">
						<div class="tf-field tf-field-accordion" style="width: 100%;">
							<div class="tf-fieldset">

								<div id="adult_tabs" class="tf-tab-switch-box"  style="display: <?php echo $item['type'] == 'person' && !empty($package_pricing[$key]['adult_tabs'][0]['disable_adult_price']) ? 'block' : 'none' ?>;">
									<div class="tf-tab-field-header">
										<div class="tf-field-collapas">
											<div class="field-label"><?php echo esc_html__( 'Adult', 'tourfic' ); ?></div>
											<i class="fa fa-angle-up" aria-hidden="true"></i>
										</div>
									</div>

									<div class="tf-tab-field-content">
										<div class="tf-field tf-field-number" style="width: 100%;">
											<label for="tf_tours_opt[adult_tabs][adult_price]" class="tf-field-label">
											<?php echo esc_html__( 'Price for Adult', 'tourfic' ); ?>
												<span class="tf-desc-tooltip">
													<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
														<g clip-path="url(#clip0_1017_4247)">
															<path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
														</g>
														<defs>
															<clipPath id="clip0_1017_4247">
																<rect width="16" height="16" fill="white"/>
															</clipPath>
														</defs>
													</svg>
													<div class="tf-desc-tooltip-content">
													<?php echo esc_html__( 'Insert amount only.', 'tourfic' ); ?>
													</div>
												</span>
											</label>

											<div class="tf-fieldset">
												<input type="number" name="tf_option_adult_price_<?php echo esc_attr( $item['index'] ); ?>" min="0">
											</div>
										</div>
									</div> <!-- .tf-tab-field-content -->
								</div> <!-- #adult_tabs -->

								<div id="child_tabs" class="tf-tab-switch-box"  style="display: <?php echo $item['type'] == 'person' && !empty($package_pricing[$key]['child_tabs'][0]['disable_child_price']) ? 'block' : 'none' ?>;">
									<div class="tf-tab-field-header">
										<div class="tf-field-collapas">
											<div class="field-label"><?php echo esc_html__( 'Child', 'tourfic' ); ?></div>
											<i class="fa fa-angle-up" aria-hidden="true"></i>
										</div>
									</div>

									<div class="tf-tab-field-content">
										<div class="tf-field tf-field-number" style="width: 100%;">
											<label for="" class="tf-field-label">
											<?php echo esc_html__( 'Price for Child', 'tourfic' ); ?>
												<span class="tf-desc-tooltip">
													<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
														<g clip-path="url(#clip0_1017_4247)">
															<path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
														</g>
														<defs>
															<clipPath id="clip0_1017_4247">
																<rect width="16" height="16" fill="white"/>
															</clipPath>
														</defs>
													</svg>
													<div class="tf-desc-tooltip-content">
													<?php echo esc_html__( 'Insert amount only.', 'tourfic' ); ?>
													</div>
												</span>
											</label>

											<div class="tf-fieldset">
												<input type="number" name="tf_option_child_price_<?php echo esc_attr( $item['index'] ); ?>" min="0">
											</div>
										</div>
									</div> <!-- .tf-tab-field-content -->
								</div> <!-- #child_tabs -->

								<div id="infant_tabs" class="tf-tab-switch-box"  style="display: <?php echo $item['type'] == 'person' && !empty($package_pricing[$key]['infant_tabs'][0]['disable_infant_price']) ? 'block' : 'none' ?>;">
									<div class="tf-tab-field-header">
										<div class="tf-field-collapas">
											<div class="field-label"><?php echo esc_html__( 'Infant', 'tourfic' ); ?></div>
											<i class="fa fa-angle-up" aria-hidden="true"></i>
										</div>
									</div>

									<div class="tf-tab-field-content">
										<div class="tf-field tf-field-number" style="width: 100%;">
											<label for="" class="tf-field-label">
											<?php echo esc_html__( 'Price for Infant', 'tourfic' ); ?>
												<span class="tf-desc-tooltip">
													<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
														<g clip-path="url(#clip0_1017_4247)">
															<path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
														</g>
														<defs>
															<clipPath id="clip0_1017_4247">
																<rect width="16" height="16" fill="white"/>
															</clipPath>
														</defs>
													</svg>
													<div class="tf-desc-tooltip-content">
													<?php echo esc_html__( 'Insert amount only.', 'tourfic' ); ?>
													</div>
												</span>
											</label>

											<div class="tf-fieldset">
												<input type="number" name="tf_option_infant_price_<?php echo esc_attr( $item['index'] ); ?>" min="0">
											</div>
										</div>
									</div> <!-- .tf-tab-field-content -->
								</div> <!-- #infant_tabs -->

								<div id="group_tabs" class="tf-tab-switch-box"  style="display: <?php echo $item['type'] == 'group' ? 'block' : 'none' ?>;">
									<div class="tf-tab-field-header">
										<div class="tf-field-collapas">
											<div class="field-label"><?php echo esc_html__( 'Group', 'tourfic' ); ?></div>
											<i class="fa fa-angle-up" aria-hidden="true"></i>
										</div>
									</div>

									<div class="tf-tab-field-content">
										<div class="tf-field tf-field-number" style="width: 100%;">
											<label for="" class="tf-field-label">
											<?php echo esc_html__( 'Price for Group', 'tourfic' ); ?>
												<span class="tf-desc-tooltip">
													<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
														<g clip-path="url(#clip0_1017_4247)">
															<path d="M8.00016 10.6654V7.9987M8.00016 5.33203H8.00683M14.6668 7.9987C14.6668 11.6806 11.6821 14.6654 8.00016 14.6654C4.31826 14.6654 1.3335 11.6806 1.3335 7.9987C1.3335 4.3168 4.31826 1.33203 8.00016 1.33203C11.6821 1.33203 14.6668 4.3168 14.6668 7.9987Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
														</g>
														<defs>
															<clipPath id="clip0_1017_4247">
																<rect width="16" height="16" fill="white"/>
															</clipPath>
														</defs>
													</svg>
													<div class="tf-desc-tooltip-content">
													<?php echo esc_html__( 'Insert amount only.', 'tourfic' ); ?>
													</div>
												</span>
											</label>

											<div class="tf-fieldset">
												<input type="number" name="tf_option_group_price_<?php echo esc_attr( $item['index'] ); ?>" min="0">
											</div>
										</div>
										<?php 
										if(!empty($package_pricing[$item['index']]['group_tabs'][4]['group_discount'])){ ?>
										<div class="tf-field tf-field-repeater" style="width:100%;">
											<div class="tf-fieldset">
												<div id="tf-repeater-1" class="tf-repeater group_discount_package" data-max-index="0">
												<div class="tf-repeater-wrap tf-repeater-wrap-group_discount_package ui-sortable tf-group-discount-package_<?php echo esc_attr( $item['index'] ); ?>">

												</div>
												<div class=" tf-single-repeater-clone tf-single-repeater-clone-group_discount_package">
													<div class="tf-single-repeater tf-single-repeater-group_discount_package">
													<input type="hidden" name="tf_parent_field" value="[group_tabs]">
													<input type="hidden" name="tf_repeater_count" value="0">
													<input type="hidden" name="tf_current_field" value="group_discount_package">
													
													<div class="tf-repeater-content-wrap" style="display: none;">
														<div class="tf-field tf-field-number  " style="width:calc(66% - 10px);">
															
														<div class="tf-fieldset">
															<div class="tf-number-range">
															<div class="tf-number-field-box">
																<i class="fa-regular fa-user"></i>
																<input type="number" name="tf_option_<?php echo esc_attr( $item['index'] ); ?>_group_discount[min_person][]" value="" min="0" placeholder="<?php echo esc_html('Min Person', 'tourfic'); ?>">
															</div>
															<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path d="M15.5 6.66797L18.8333 10.0013L15.5 13.3346" stroke="#95A3B2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
																<path d="M2.1665 10H18.8332" stroke="#95A3B2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
															</svg>
															<div class="tf-number-field-box">
																<i class="fa-regular fa-user"></i>
																<input type="number" name="tf_option_<?php echo esc_attr( $item['index'] ); ?>_group_discount[max_person][]" value="" min="0" placeholder="<?php echo esc_html('Max Person', 'tourfic'); ?>">
															</div>
															</div>
														</div>
														</div>
														<div class="tf-field tf-field-number  " style="width:calc(33% - 10px);">
														<div class="tf-fieldset">
															<input type="number" name="tf_option_<?php echo esc_attr( $item['index'] ); ?>_group_discount[price][]" value="" min="0" placeholder="<?php echo esc_html('Price', 'tourfic'); ?>">
														</div>
														</div>

															<span class="tf-repeater-icon tf-repeater-icon-delete">
															<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M15 5L5 15" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
															<path d="M5 5L15 15" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
															</svg>
														</span>
													</div>
													</div>
												</div>
												<div class="tf-repeater-add tf-repeater-add-group_discount_package">
													<span data-repeater-id="group_discount_package" data-repeater-max="" class="tf-repeater-icon tf-repeater-icon-add tf-repeater-add-group_discount_package">
													<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
														<g clip-path="url(#clip0_1017_2374)">
														<path d="M9.99984 18.3346C14.6022 18.3346 18.3332 14.6037 18.3332 10.0013C18.3332 5.39893 14.6022 1.66797 9.99984 1.66797C5.39746 1.66797 1.6665 5.39893 1.6665 10.0013C1.6665 14.6037 5.39746 18.3346 9.99984 18.3346Z" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
														<path d="M6.6665 10H13.3332" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
														<path d="M10 6.66797V13.3346" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
														</g>
														<defs>
														<clipPath id="clip0_1017_2374">
															<rect width="20" height="20" fill="white"></rect>
														</clipPath>
														</defs>
													</svg><?php echo esc_html('Add New Discount', 'tourfic'); ?></span>
												</div>
												</div>
											</div>
										</div>
										<?php } ?>


									</div> <!-- .tf-tab-field-content -->
								</div> <!-- #group_tabs -->

								<!-- repeated package times -->
								<div class="tf-field tf-field-repeater tf-package-time-fields">
									<div class="tf-fieldset">
										<div id="tf-repeater-1" class="tf-repeater allowed_time" data-max-index="0">
										<div class="tf-repeater-wrap tf_tour_allowed_times tf-repeater-wrap-allowed_time ui-sortable tf-tour-package-allowed-time_<?php echo esc_attr( $item['index'] ); ?>">

										</div>
										<div class=" tf-single-repeater-clone tf-single-repeater-clone-allowed_time">
											<div class="tf-single-repeater tf-single-repeater-allowed_time">
												<input type="hidden" name="tf_parent_field" value="">
												<input type="hidden" name="tf_repeater_count" value="0">
												<input type="hidden" name="tf_current_field" value="allowed_time">
												<div class="tf-repeater-content-wrap">
													<div class="tf-field tf-field-time" style="width: calc(50% - 6px);">
														<div class="tf-fieldset">
															<input type="text" name="tf_option_<?php echo esc_attr( $item['index'] ); ?>_allowed_time[time][]" placeholder="Select Time" value="" class="flatpickr flatpickr-input" data-format="h:i K" readonly="readonly">
															<i class="fa-regular fa-clock"></i>
														</div>
													</div>
													<div class="tf-field tf-field-number" style="width: calc(50% - 6px);">
														<div class="tf-fieldset">
															<input type="number" name="tf_option_<?php echo esc_attr( $item['index'] ); ?>_allowed_time[cont_max_capacity][]" id="allowed_time[cont_max_capacity]" value="" placeholder="<?php echo esc_html__( 'Maximum Capacity', 'tourfic' ); ?>">
														</div>
													</div>
													<span class="tf-repeater-icon tf-repeater-icon-delete">
														<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M15 5L5 15" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
														<path d="M5 5L15 15" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
														</svg>
													</span>
												</div>
											</div>
										</div>
										<div class="tf-repeater-add tf-repeater-add-allowed_time tf-package-add-allowed-time">
											<span data-repeater-id="allowed_time" data-repeater-max="" class="tf-repeater-icon tf-repeater-icon-add tf-repeater-add-allowed_time">
												<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
													<g clip-path="url(#clip0_1017_2374)">
														<path d="M9.99984 18.3346C14.6022 18.3346 18.3332 14.6037 18.3332 10.0013C18.3332 5.39893 14.6022 1.66797 9.99984 1.66797C5.39746 1.66797 1.6665 5.39893 1.6665 10.0013C1.6665 14.6037 5.39746 18.3346 9.99984 18.3346Z" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
														<path d="M6.6665 10H13.3332" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
														<path d="M10 6.66797V13.3346" stroke="#003C79" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
													</g>
													<defs>
														<clipPath id="clip0_1017_2374">
														<rect width="20" height="20" fill="white"/>
														</clipPath>
													</defs>
												</svg>
												<?php echo esc_html__( 'Add Start Time', 'tourfic' ); ?> 
											</span>
										</div>
										</div>
									</div>
								</div>

								<input type="hidden" name="tf_option_title_<?php echo esc_attr( $item['index'] ); ?>" value="<?php echo esc_attr($item['title']); ?>"/>
								<input type="hidden" name="tf_option_pricing_type_<?php echo esc_attr( $item['index'] ); ?>" value="<?php echo esc_attr($item['type']); ?>"/>
							</div>
						</div> <!-- .tf-field-accordion -->
					</div> <!-- .tf-repeater-content-wrap -->
				</div> <!-- .tf-single-repeater -->
            <?php
            } ?>
			</div> <!-- .tf-repeater-wrap -->
			</div> <!-- .tf-repeater -->
       <?php }
		$options_html .= ob_get_clean();

		echo wp_json_encode( array(
			'avail_data'   => $tour_availability_data,
			'options_html' => $options_html,
		) );
		die();
	}

	/*
     * Save Tour Package
     * @auther Jahid
     */
	function save_tour_package_pricing(){
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', 'nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'You do not have permission to access this resource.', 'tourfic' )
			] );
			return;
		}

		$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
		$pricing_type = isset($_POST['pricing_type']) ? sanitize_text_field($_POST['pricing_type']) : '';
		$package_index = isset($_POST['package_index']) ? intval($_POST['package_index']) : null;
		$package_data = isset($_POST['package_data']) ? $_POST['package_data'] : array();


		// Get existing data
		$existing = get_post_meta($post_id, 'tf_tours_opt', true) ?: ['package_pricing' => []];

		// Sanitize the incoming data
		$sanitized_package = $this->recursive_sanitize_package($package_data);

		// Update pricing type
		if(!empty($pricing_type)){
			$existing['pricing'] = $pricing_type;
		}

		// Update just this package
		$existing['package_pricing'][$package_index] = $sanitized_package;

		// Save back to post meta
		update_post_meta($post_id, 'tf_tours_opt', $existing);

		wp_send_json_success('Package saved');
	}

	/*
     * Save Tour Package
     * @auther Jahid
     */
	function save_tour_pricing_type(){
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', 'nonce' );

		// Check if the current user has the required capability.
		if (!current_user_can('manage_options')) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'You do not have permission to access this resource.', 'tourfic' )
			] );
			return;
		}

		$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
		$pricing_type = isset($_POST['pricing_type']) ? sanitize_text_field($_POST['pricing_type']) : '';

		// Get existing data
		$existing = get_post_meta($post_id, 'tf_tours_opt', true) ?: ['package_pricing' => []];

		// Update pricing type
		if(!empty($pricing_type)){
			$existing['pricing'] = $pricing_type;
		}

		// Save back to post meta
		update_post_meta($post_id, 'tf_tours_opt', $existing);

		wp_send_json_success('Pricing saved');
	}

	private function recursive_sanitize_package($data) {
		if (!is_array($data)) {
			return sanitize_text_field($data);
		}
	
		$sanitized = [];
		
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$sanitized[$key] = $this->recursive_sanitize_package($value);
			} else {
				switch (true) {
					case $key === 'pack_title':
						$sanitized[$key] = sanitize_text_field($value);
						break;
					case $key === 'desc':
						$sanitized[$key] = sanitize_textarea_field($value);
						break;
					case strpos($key, 'price') !== false:
					case strpos($key, 'discount_price') !== false:
					case preg_match('/^(min|max)_/', $key):
						$sanitized[$key] = is_numeric($value) ? floatval($value) : 0;
						break;
					case strpos($key, 'disable_') === 0:
					case $key === 'pack_status':
					case $key === 'group_discount':
						$sanitized[$key] = $value ? 1 : 0;
						break;
					case $key === 'pricing_type':
						$sanitized[$key] = in_array($value, ['person', 'group']) ? $value : 'person';
						break;
					default:
						$sanitized[$key] = sanitize_text_field($value);
				}
			}
		}
		
		return $sanitized;
	}

	/*
     * Reset tour availability calendar
     * @auther Jahid
     */
	function tf_reset_tour_availability() {
		// Add nonce for security and authentication.
		check_ajax_referer( 'updates', '_nonce' );

		$tour_id     = isset( $_POST['tour_id'] ) && ! empty( $_POST['tour_id'] ) ? sanitize_text_field( $_POST['tour_id'] ) : '';
		$tour_data = get_post_meta( $tour_id, 'tf_tours_opt', true );
		
		if(empty($tour_data)){
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Publish the Tour First!', 'tourfic' )
			] );
		}
		
		$tour_data['tour_availability'] = [];
		update_post_meta( $tour_id, 'tf_tours_opt', $tour_data );
		wp_send_json_success( [
			'status'           => true,
			'message'          => __( 'Availability Reset Successfully.', 'tourfic' ),
			'tour_availability' => [],
		] );
		wp_die();
	}
	/*
     * Update apt_availability price based on pricing type
     * @auther Foysal
     */
	function tf_update_apt_availability_price( $post_id, $post ) {
		if ( $post->post_type == 'tf_apartment' ) {
			$meta                = get_post_meta( $post_id, 'tf_apartment_opt', true );
			$pricing_type        = ! empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : '';
			$price               = ! empty( $meta['price_per_night'] ) ? $meta['price_per_night'] : '';
			$adult_price         = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : '';
			$child_price         = ! empty( $meta['child_price'] ) ? $meta['child_price'] : '';
			$infant_price        = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : '';
			$enable_availability = ! empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';

			if ( $enable_availability === '1' && ! empty( $meta['apt_availability'] ) ) {
				$apt_availability_data = json_decode( $meta['apt_availability'], true );

				if ( isset( $apt_availability_data ) && ! empty( $apt_availability_data ) ) {

					$apt_availability_data = array_map( function ( $item ) use ( $pricing_type, $price, $adult_price, $child_price, $infant_price ) {

						if ( $pricing_type == 'per_night' ) {
							$item['price'] = ! isset( $item['price'] ) ? $price : $item['price'];
						} else {
							$item['adult_price']  = ! isset( $item['adult_price'] ) ? $adult_price : $item['adult_price'];
							$item['child_price']  = ! isset( $item['child_price'] ) ? $child_price : $item['child_price'];
							$item['infant_price'] = ! isset( $item['infant_price'] ) ? $infant_price : $item['infant_price'];
						}
						$item['pricing_type'] = $pricing_type;

						return $item;
					}, $apt_availability_data );
				}

				$meta['apt_availability'] = wp_json_encode( $apt_availability_data );
				update_post_meta( $post_id, 'tf_apartment_opt', $meta );

			} elseif ( $enable_availability === '1' && empty( $meta['apt_availability'] ) ) {
				//add next 5 years availability
				$apt_availability_data = [];
				for ( $i = strtotime( gmdate( 'Y-m-d' ) ); $i <= strtotime( '+5 year', strtotime( gmdate( 'Y-m-d' ) ) ); $i = strtotime( '+1 day', $i ) ) {
					$tf_apt_date                           = gmdate( 'Y/m/d', $i );
					$tf_apt_data                           = [
						'check_in'     => $tf_apt_date,
						'check_out'    => $tf_apt_date,
						'pricing_type' => $pricing_type,
						'price'        => $price,
						'adult_price'  => $adult_price,
						'child_price'  => $child_price,
						'infant_price' => $infant_price,
						'status'       => 'available'
					];
					$apt_availability_data[ $tf_apt_date ] = $tf_apt_data;
				}

				$meta['apt_availability'] = wp_json_encode( $apt_availability_data );
				update_post_meta( $post_id, 'tf_apartment_opt', $meta );
			}
		}
	}


	function tf_convert_date_format( $date, $currentFormat ) {
		$dateTime = \DateTime::createFromFormat( $currentFormat, $date );

		if ( $dateTime === false ) {
			return false;
		}

		return $dateTime->format( 'Y/m/d' );
	}

	/**
	 * Insert Category Data
	 *
	 * @author Jahid
	 */
	function tf_insert_category_data_callback() {
		//Verify Nonce
		check_ajax_referer( 'updates', '_nonce' );

		$categoryName = sanitize_title( $_POST['categoryName'] );
		$categoryTitle = sanitize_text_field( $_POST['categoryTitle'] );
		$parentCategory = sanitize_key( $_POST['parentCategory'] );

		$response = [];
		if ( !empty($categoryName) && !empty($categoryTitle) ) {
			// Insert the term
			$term = wp_insert_term(
				$categoryTitle,   // The term
				$categoryName, // The taxonomy
				array(
					'slug'   => sanitize_title($categoryTitle),
					'parent' => !empty($parentCategory) ? intval($parentCategory) : ''
				)
			);
			$insert_Date = array(
				'id' => $term['term_id'],
				'title' => get_term_field('name', $term['term_id'], $categoryName)
			);

			$response ['insert_category'] = $insert_Date;
		}
		echo wp_json_encode( $response );
		wp_die();
	}

	/**
	 * Delete Category Data
	 *
	 * @author Jahid
	 */
	function tf_delete_category_data_callback() {
		//Verify Nonce
		check_ajax_referer( 'updates', '_nonce' );

		$categoryName = sanitize_title( $_POST['categoryName'] );
		$term_id = intval($_POST['term_id']);

		$response = [];

		if (!empty($term_id)) {
			$result = wp_delete_term($term_id, $categoryName); // Replace 'category' with your taxonomy if it's different

			if (!is_wp_error($result)) {
				$response['success'] = true;
			} else {
				$response['error'] = $result->get_error_message();
			}
		} else {
			$response['error'] = 'Invalid term ID.';
		}

		echo wp_json_encode($response);
		wp_die();
	}

	/**
	 * Insert Post Data
	 *
	 * @author Foysal
	 */
	function tf_insert_post_data_callback() {
		//Verify Nonce
		check_ajax_referer( 'updates', '_nonce' );

		$postType = !empty($_POST['postType']) ? sanitize_title( $_POST['postType'] ) : '';
		$postTitle = !empty($_POST['postTitle']) ? sanitize_text_field( $_POST['postTitle'] ) : '';
		$fieldId = !empty($_POST['fieldId']) ? sanitize_text_field( $_POST['fieldId'] ) : '';
		$postId = !empty($_POST['postId']) ? sanitize_text_field( $_POST['postId'] ) : '';

		$response = [];
		if ( !empty($postType) && !empty($postTitle) ) {
			// Insert the post
			$post_id = wp_insert_post(array(
				'post_type'    => $postType,
				'post_title'   => $postTitle,
				'post_status'  => 'publish'
			));

			if($fieldId == 'tf_rooms'){
				$room_meta['tf_hotel'] = $postId;
				update_post_meta($post_id, 'tf_room_opt', $room_meta);
			}

			$insert_Data = array(
				'id' => $post_id,
				'title' => get_the_title($post_id),
				'edit_url' => esc_url( get_edit_post_link( $post_id ) ),
			);

			$response ['insert_post'] = $insert_Data;
		}
		echo wp_json_encode( $response );
		wp_die();
	}

	/**
	 * Delete Post Data
	 *
	 * @author Foysal
	 */
	function tf_delete_post_data_callback() {
		//Verify Nonce
		check_ajax_referer( 'updates', '_nonce' );

		$categoryName = sanitize_title( $_POST['categoryName'] );
		$term_id = intval($_POST['term_id']);

		$response = [];

		if (!empty($term_id)) {
			$result = wp_delete_term($term_id, $categoryName); // Replace 'category' with your taxonomy if it's different

			if (!is_wp_error($result)) {
				$response['success'] = true;
			} else {
				$response['error'] = $result->get_error_message();
			}
		} else {
			$response['error'] = 'Invalid term ID.';
		}

		echo wp_json_encode($response);
		wp_die();
	}
}