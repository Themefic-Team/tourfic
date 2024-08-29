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
		add_action( 'save_post', array( $this, 'tf_update_room_avail_date_price' ), 9999, 2 );
		add_action( 'wp_ajax_tf_add_apartment_availability', array( $this, 'tf_add_apartment_availability' ) );
		add_action( 'wp_ajax_tf_get_apartment_availability', array( $this, 'tf_get_apartment_availability' ) );
		add_action( 'save_post', array( $this, 'tf_update_apt_availability_price' ), 99, 2 );
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
	public function field( $field, $value, $settings_id = '', $parent = '' ) {
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

			<?php if ( ! empty( $field['label'] ) ): ?>
                <label for="<?php echo esc_attr( $id ) ?>" class="tf-field-label">
					<?php echo esc_html( $field['label'] ) ?>
					<?php if ( $is_pro ): ?>
                        <div class="tf-csf-badge"><span class="tf-pro"><?php esc_html_e( "Pro", "tourfic" ); ?></span></div>
					<?php endif; ?>
					<?php if ( $badge_up ): ?>
                        <div class="tf-csf-badge"><span class="tf-upcoming"><?php esc_html_e( "Upcoming", "tourfic" ); ?></span></div>
					<?php endif; ?>
                </label>
			<?php endif; ?>

			<?php if ( ! empty( $field['subtitle'] ) ) : ?>
                <span class="tf-field-sub-title"><?php echo wp_kses_post( $field['subtitle'] ) ?></span>
			<?php endif; ?>

            <div class="tf-fieldset">
				<?php
				$fieldClass = 'TF_' . $field['type'];
				if ( class_exists( $fieldClass ) ) {
					$_field = new $fieldClass( $field, $value, $settings_id, $parent );
					$_field->render();
				} else {
					echo '<p>' . esc_html__( 'Field not found!', 'tourfic' ) . '</p>';
				}
				?>
            </div>
			<?php if ( ! empty( $field['description'] ) ): ?>
                <p class="description"><?php echo wp_kses_post( $field['description'] ) ?></p>
			<?php endif; ?>
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
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
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
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
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
				'message' => __( 'You do not have permission to access this resource.', 'tourfic' )
			] );
			return;
		}

		$date_format         = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
		$room_id             = isset( $_POST['room_id'] ) && ! empty( $_POST['room_id'] ) ? sanitize_text_field( $_POST['room_id'] ) : '';
		$new_post            = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? $_POST['new_post'] : '';
		$check_in            = isset( $_POST['tf_room_check_in'] ) && ! empty( $_POST['tf_room_check_in'] ) ? sanitize_text_field( $_POST['tf_room_check_in'] ) : '';
		$check_out           = isset( $_POST['tf_room_check_out'] ) && ! empty( $_POST['tf_room_check_out'] ) ? sanitize_text_field( $_POST['tf_room_check_out'] ) : '';
		$status              = isset( $_POST['tf_room_status'] ) && ! empty( $_POST['tf_room_status'] ) ? sanitize_text_field( $_POST['tf_room_status'] ) : '';
		$price_by            = isset( $_POST['price_by'] ) && ! empty( $_POST['price_by'] ) ? sanitize_text_field( $_POST['price_by'] ) : '';
		$tf_room_price       = isset( $_POST['tf_room_price'] ) && ! empty( $_POST['tf_room_price'] ) ? sanitize_text_field( $_POST['tf_room_price'] ) : '';
		$tf_room_adult_price = isset( $_POST['tf_room_adult_price'] ) && ! empty( $_POST['tf_room_adult_price'] ) ? sanitize_text_field( $_POST['tf_room_adult_price'] ) : '';
		$tf_room_child_price = isset( $_POST['tf_room_child_price'] ) && ! empty( $_POST['tf_room_child_price'] ) ? sanitize_text_field( $_POST['tf_room_child_price'] ) : '';
		$avail_date          = isset( $_POST['avail_date'] ) && ! empty( $_POST['avail_date'] ) ? sanitize_text_field( $_POST['avail_date'] ) : '';

		if ( empty( $check_in ) || empty( $check_out ) ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Please select check in and check out date.', 'tourfic' )
			] );
		}

		$check_in  = strtotime( $this->tf_convert_date_format( $check_in, $date_format ) );
		$check_out = strtotime( $this->tf_convert_date_format( $check_out, $date_format ) );
		if ( $check_in > $check_out ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Check in date must be less than check out date.', 'tourfic' )
			] );
		}

		$room_avail_data = [];
		for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
			$tf_room_date                     = gmdate( 'Y/m/d', $i );
			$tf_room_data                     = [
				'check_in'    => $tf_room_date,
				'check_out'   => $tf_room_date,
				'price_by'    => $price_by,
				'price'       => $tf_room_price,
				'adult_price' => $tf_room_adult_price,
				'child_price' => $tf_room_child_price,
				'status'      => $status
			];
			$room_avail_data[ $tf_room_date ] = $tf_room_data;
		}

		$room_meta = get_post_meta( $room_id, 'tf_room_opt', true );
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
			'message'    => __( 'Availability updated successfully.', 'tourfic' ),
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
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
			return;
		}

		$new_post   = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? sanitize_text_field( $_POST['new_post'] ) : '';
		$room_id    = isset( $_POST['room_id'] ) && ! empty( $_POST['room_id'] ) ? sanitize_text_field( $_POST['room_id'] ) : '';
		$avail_date = isset( $_POST['avail_date'] ) && ! empty( $_POST['avail_date'] ) ? sanitize_text_field( $_POST['avail_date'] ) : '';

		if ( $new_post != 'true' ) {
			$room_meta       = get_post_meta( $room_id, 'tf_room_opt', true );
			$room_avail_data = isset( $room_meta['avail_date'] ) && ! empty( $room_meta['avail_date'] ) ? json_decode( $room_meta['avail_date'], true ) : [];
		} else {
			$room_avail_data = json_decode( stripslashes( $avail_date ), true );
		}

		if ( ! empty( $room_avail_data ) && is_array( $room_avail_data ) ) {
			$room_avail_data = array_values( $room_avail_data );
			$room_avail_data = array_map( function ( $item ) {
				$item['start'] = gmdate( 'Y-m-d', strtotime( $item['check_in'] ) );
				$item['title'] = $item['price_by'] == '1' ? __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) : __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );

				if ( $item['status'] == 'unavailable' ) {
					$item['display'] = 'background';
					$item['color']   = '#003c79';
				}

				return $item;
			}, $room_avail_data );
		} else {
			$room_avail_data = [];
		}

		echo wp_json_encode( $room_avail_data );
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
						} else {
							$item['adult_price'] = ! isset( $item['adult_price'] ) ? $adult_price : $item['adult_price'];
							$item['child_price'] = ! isset( $item['child_price'] ) ? $child_price : $item['child_price'];
						}
						$item['price_by'] = $pricing_by;

						return $item;
					}, $room_avail_data );
				}

				$room['avail_date'] = wp_json_encode( $room_avail_data );
			} elseif ( $avil_by_date === '1' && empty( $room['avail_date'] ) ) {
				//add next 5 years availability
				$room_avail_data = [];
				for ( $i = 0; $i <= 1825; $i ++ ) {
					$tf_room_date                      = gmdate( 'Y/m/d', strtotime( "+$i day" ) );
					$tf_room_data                      = [
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
				'message' => __( 'You do not have permission to access this resource.', 'tourfic' )
			] );
			return;
		}

		$date_format         = ! empty( Helper::tfopt( "tf-date-format-for-users" ) ) ? Helper::tfopt( "tf-date-format-for-users" ) : "Y/m/d";
		$apartment_id        = isset( $_POST['apartment_id'] ) && ! empty( $_POST['apartment_id'] ) ? sanitize_text_field( $_POST['apartment_id'] ) : '';
		$new_post            = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? $_POST['new_post'] : '';
		$check_in            = isset( $_POST['tf_apt_check_in'] ) && ! empty( $_POST['tf_apt_check_in'] ) ? sanitize_text_field( $_POST['tf_apt_check_in'] ) : '';
		$check_out           = isset( $_POST['tf_apt_check_out'] ) && ! empty( $_POST['tf_apt_check_out'] ) ? sanitize_text_field( $_POST['tf_apt_check_out'] ) : '';
		$status              = isset( $_POST['tf_apt_status'] ) && ! empty( $_POST['tf_apt_status'] ) ? sanitize_text_field( $_POST['tf_apt_status'] ) : '';
		$pricing_type        = isset( $_POST['pricing_type'] ) && ! empty( $_POST['pricing_type'] ) ? sanitize_text_field( $_POST['pricing_type'] ) : '';
		$tf_apt_price        = isset( $_POST['tf_apt_price'] ) && ! empty( $_POST['tf_apt_price'] ) ? sanitize_text_field( $_POST['tf_apt_price'] ) : '';
		$tf_apt_adult_price  = isset( $_POST['tf_apt_adult_price'] ) && ! empty( $_POST['tf_apt_adult_price'] ) ? sanitize_text_field( $_POST['tf_apt_adult_price'] ) : '';
		$tf_apt_child_price  = isset( $_POST['tf_apt_child_price'] ) && ! empty( $_POST['tf_apt_child_price'] ) ? sanitize_text_field( $_POST['tf_apt_child_price'] ) : '';
		$tf_apt_infant_price = isset( $_POST['tf_apt_infant_price'] ) && ! empty( $_POST['tf_apt_infant_price'] ) ? sanitize_text_field( $_POST['tf_apt_infant_price'] ) : '';
		$apt_availability    = isset( $_POST['apt_availability'] ) && ! empty( $_POST['apt_availability'] ) ? sanitize_text_field( $_POST['apt_availability'] ) : '';

		if ( empty( $check_in ) || empty( $check_out ) ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Please select check in and check out date.', 'tourfic' )
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
				'message' => __( 'Check in date must be less than check out date.', 'tourfic' )
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

		$apartment_data = get_post_meta( $apartment_id, 'tf_apartment_opt', true );
		if ( $new_post != 'true' ) {
			$apt_availability = json_decode( $apartment_data['apt_availability'], true );
			if ( isset( $apt_availability ) && ! empty( $apt_availability ) ) {
				$apt_availability_data = array_merge( $apt_availability, $apt_availability_data );
			}
			$apartment_data['apt_availability'] = wp_json_encode( $apt_availability_data );
			update_post_meta( $apartment_id, 'tf_apartment_opt', $apartment_data );
		} else {
			$apt_availability = json_decode( stripslashes( $apt_availability ), true );
			if ( isset( $apt_availability ) && ! empty( $apt_availability ) ) {
				$apt_availability_data = array_merge( $apt_availability, $apt_availability_data );
			}
		}

		wp_send_json_success( [
			'status'           => true,
			'message'          => __( 'Availability updated successfully.', 'tourfic' ),
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
			wp_send_json_error(__('You do not have permission to access this resource.', 'tourfic'));
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
				$item['title'] = $item['pricing_type'] == 'per_night' ? __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) : __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] ) . '<br>' . __( 'Infant: ', 'tourfic' ) . wc_price( $item['infant_price'] );

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
}