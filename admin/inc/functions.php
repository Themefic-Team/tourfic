<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Require some taxonomies
 *
 * hotel_location, tour_destination
 *
 * @since 1.0.0
 */
function tf_required_taxonomies( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
		return;
	}

	global $post_type;

	$tf_is_gutenberg_active = tf_is_gutenberg_active();

	$default_post_types = array(
		'tf_hotel'     => array(
			'hotel_location' => array(
				'message' => __( 'Please select a location before publishing this hotel', 'tourfic' )
			)
		),
		'tf_tours'     => array(
			'tour_destination' => array(
				'message' => __( 'Please select a destination before publishing this tour', 'tourfic' )
			)
		),
		'tf_apartment' => array(
			'apartment_location' => array(
				'message' => __( 'Please select a location before publishing this apartment', 'tourfic' )
			)
		)
	);

	$post_types = apply_filters( 'tf_post_types', $default_post_types );

	if ( ! is_array( $post_types ) ) {
		return;
	}

	if ( ! isset( $post_types[ $post_type ] ) ) {
		return;
	}

	if ( ! isset( $post_types[ $post_type ] ) || ! is_array( $post_types[ $post_type ] ) || empty( $post_types[ $post_type ] ) ) {
		if ( is_string( $post_types[ $post_type ] ) ) {
			$post_types[ $post_type ] = array(
				'taxonomies' => array(
					$post_types[ $post_type ]
				)
			);
		} else if ( is_array( $post_types[ $post_type ] ) ) {
			$post_types[ $post_type ] = array(
				'taxonomies' => $post_types[ $post_type ]
			);
		} else {
			return;
		}
	}

	$post_type_taxonomies = get_object_taxonomies( $post_type );

	foreach ( $post_types[ $post_type ] as $taxonomy => $config ) {
		if ( is_int( $taxonomy ) && is_string( $config ) ) {
			unset( $post_types[ $post_type ][ $taxonomy ] );
			$taxonomy = $config;

			$post_types[ $post_type ][ $taxonomy ] = $config = array();
		}

		if ( ! taxonomy_exists( $taxonomy ) || ! in_array( $taxonomy, $post_type_taxonomies ) ) {
			unset( $post_types[ $post_type ][ $taxonomy ] );
			continue;
		}

		$taxonomy_object = get_taxonomy( $taxonomy );
		$taxonomy_labels = get_taxonomy_labels( $taxonomy_object );

		$post_types[ $post_type ][ $taxonomy ]['type'] = $config['type'] = ( is_taxonomy_hierarchical( $taxonomy ) ? 'hierarchical' : 'non-hierarchical' );

		if ( ! isset( $config['message'] ) || $taxonomy === $config ) {
			$post_type_labels  = get_post_type_labels( get_post_type_object( $post_type ) );
			$config['message'] = sprintf( __( 'Please choose at least one %s before publishing this %s.', 'tourfic' ), $taxonomy_labels->singular_name, $post_type_labels->singular_name );
		}

		$post_types[ $post_type ][ $taxonomy ]['message'] = $config['message'];

		if ( $tf_is_gutenberg_active && ! empty( $taxonomy_object->rest_base ) && $taxonomy !== $taxonomy_object->rest_base ) {
			$post_types[ $post_type ][ $taxonomy_object->rest_base ] = $post_types[ $post_type ][ $taxonomy ];
			unset( $post_types[ $post_type ][ $taxonomy ] );
		}
	}

	if ( empty( $post_types[ $post_type ] ) ) {
		return;
	}

	wp_localize_script( 'tf-admin', 'tf_admin_params', array(
		'taxonomies'                       => $post_types[ $post_type ],
		'error'                            => false,
		'tf_nonce'                         => wp_create_nonce( 'updates' ),
		'ajax_url'                         => admin_url( 'admin-ajax.php' ),
		'deleting_old_review_fields'       => __( 'Deleting old review fields...', 'tourfic' ),
		'deleting_room_order_ids'          => __( 'Deleting order ids...', 'tourfic' ),
		'tour_location_required'           => __( 'Tour Location is a required field!', 'tourfic' ),
		'hotel_location_required'          => __( 'Hotel Location is a required field!', 'tourfic' ),
		'apartment_location_required'      => __( 'Apartment Location is a required field!', 'tourfic' ),
		'tour_feature_image_required'      => __( 'Tour image is a required!', 'tourfic' ),
		'hotel_feature_image_required'     => __( 'Hotel image is a required!', 'tourfic' ),
		'apartment_feature_image_required' => __( 'Apartment image is a required!', 'tourfic' ),
		'installing'                       => __( 'Installing...', 'tourfic' ),
		'activating'                       => __( 'Activating...', 'tourfic' ),
		'installed'                        => __( 'Installed', 'tourfic' ),
		'activated'                        => __( 'Activated', 'tourfic' ),
		'install_failed'                   => __( 'Install failed', 'tourfic' ),
		'i18n'                             => array(
			'no_services_selected' => __( 'Please select at least one service.', 'tourfic' ),
		)
	) );

}

add_action( 'admin_enqueue_scripts', 'tf_required_taxonomies' );

function tf_is_gutenberg_active() {
	if ( function_exists( 'is_gutenberg_page' ) ) {
		return true;
	}

	$current_screen = get_current_screen();

	if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
		return true;
	}

	return false;
}

/**
 * Get post id
 */
function tf_admin_footer() {

	$screen = get_current_screen();

	if ( is_admin() && ( $screen->id == 'tf_hotel' ) ) {
		global $post;
		?>
        <script>
            var post_id = '<?php echo $post->ID; ?>';
        </script>
		<?php
	}
}

add_action( 'admin_footer', 'tf_admin_footer' );

/**
 * Dashboard header section
 * @author Jahid,Hena
 */
function tf_dashboard_header() {
	?>
    <!-- dashboard-top-section -->
    <div class="tf-setting-top-bar">
        <div class="version">
            <img src="<?php echo TF_ASSETS_APP_URL; ?>images/tourfic-logo.webp" alt="logo">
            <span>v<?php echo esc_attr( TOURFIC ); ?></span>
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
                        <span><?php _e( "Need Help?", "tourfic" ); ?></span>
                    </a>
                    <a href="https://themefic.com/docs/tourfic/" target="_blank">
                        <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16.1896 7.57803H13.5902C11.4586 7.57803 9.72274 5.84103 9.72274 3.70803V1.10703C9.72274 0.612031 9.318 0.207031 8.82332 0.207031H5.00977C2.23956 0.207031 0 2.00703 0 5.22003V13.194C0 16.407 2.23956 18.207 5.00977 18.207H12.0792C14.8494 18.207 17.089 16.407 17.089 13.194V8.47803C17.089 7.98303 16.6843 7.57803 16.1896 7.57803ZM8.09478 14.382H4.4971C4.12834 14.382 3.82254 14.076 3.82254 13.707C3.82254 13.338 4.12834 13.032 4.4971 13.032H8.09478C8.46355 13.032 8.76935 13.338 8.76935 13.707C8.76935 14.076 8.46355 14.382 8.09478 14.382ZM9.89363 10.782H4.4971C4.12834 10.782 3.82254 10.476 3.82254 10.107C3.82254 9.73803 4.12834 9.43203 4.4971 9.43203H9.89363C10.2624 9.43203 10.5682 9.73803 10.5682 10.107C10.5682 10.476 10.2624 10.782 9.89363 10.782Z"
                                  fill="#003c79"></path>
                        </svg>
                        <span><?php _e( "Documentation", "tourfic" ); ?></span>

                    </a>
                    <a href="https://portal.themefic.com/support/" target="_blank">
                        <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M13.5902 7.57803H16.1896C16.6843 7.57803 17.089 7.98303 17.089 8.47803V13.194C17.089 16.407 14.8494 18.207 12.0792 18.207H5.00977C2.23956 18.207 0 16.407 0 13.194V5.22003C0 2.00703 2.23956 0.207031 5.00977 0.207031H8.82332C9.318 0.207031 9.72274 0.612031 9.72274 1.10703V3.70803C9.72274 5.84103 11.4586 7.57803 13.5902 7.57803ZM11.9613 0.396012C11.5926 0.0270125 10.954 0.279013 10.954 0.792013V3.93301C10.954 5.24701 12.0693 6.33601 13.4274 6.33601C14.2818 6.34501 15.4689 6.34501 16.4852 6.34501H16.4854C16.998 6.34501 17.2679 5.74201 16.9081 5.38201C16.4894 4.96018 15.9637 4.42927 15.3988 3.85888L15.3932 3.85325L15.3913 3.85133L15.3905 3.8505L15.3902 3.85016C14.2096 2.65803 12.86 1.29526 11.9613 0.396012ZM3.0145 12.0732C3.0145 11.7456 3.28007 11.48 3.60768 11.48H5.32132V9.76639C5.32132 9.43879 5.58689 9.17321 5.9145 9.17321C6.2421 9.17321 6.50768 9.43879 6.50768 9.76639V11.48H8.22131C8.54892 11.48 8.8145 11.7456 8.8145 12.0732C8.8145 12.4008 8.54892 12.6664 8.22131 12.6664H6.50768V14.38C6.50768 14.7076 6.2421 14.9732 5.9145 14.9732C5.58689 14.9732 5.32132 14.7076 5.32132 14.38V12.6664H3.60768C3.28007 12.6664 3.0145 12.4008 3.0145 12.0732Z"
                                  fill="#003c79"></path>
                        </svg>
                        <span><?php _e( "Feature Request", "tourfic" ); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- dashboard-top-section -->
	<?php
}

/**
 * Hotel availability calendar update
 * @author Foysal
 */
if ( ! function_exists( 'tf_add_hotel_availability' ) ) {
	function tf_add_hotel_availability() {
		$date_format         = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";
		$hotel_id            = isset( $_POST['hotel_id'] ) && ! empty( $_POST['hotel_id'] ) ? sanitize_text_field( $_POST['hotel_id'] ) : '';
		$new_post            = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? $_POST['new_post'] : '';
		$room_index          = isset( $_POST['room_index'] ) ? intval( $_POST['room_index'] ) : '';
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

		if ( $date_format == 'Y.m.d' || $date_format == 'd.m.Y' ) {
			$check_in  = date( "Y-m-d", strtotime( str_replace( ".", "-", $check_in ) ) );
			$check_out = date( "Y-m-d", strtotime( str_replace( ".", "-", $check_out ) ) );
		}

		$check_in  = strtotime( $check_in );
		$check_out = strtotime( $check_out );
		if ( $check_in > $check_out ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Check in date must be less than check out date.', 'tourfic' )
			] );
		}

		$room_avail_data = [];
		for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
			$tf_room_date                     = date( 'Y/m/d', $i );
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

		$hotel_avail_data = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
		if ( $new_post != 'true' ) {
			$avail_date = json_decode( $hotel_avail_data['room'][ $room_index ]['avail_date'], true );
			if ( isset( $avail_date ) && ! empty( $avail_date ) ) {
				$room_avail_data = array_merge( $avail_date, $room_avail_data );
			}
			$hotel_avail_data['room'][ $room_index ]['avail_date'] = json_encode( $room_avail_data );
			update_post_meta( $hotel_id, 'tf_hotels_opt', $hotel_avail_data );
		} else {
			$avail_date = json_decode( stripslashes( $avail_date ), true );
			if ( isset( $avail_date ) && ! empty( $avail_date ) ) {
				$room_avail_data = array_merge( $avail_date, $room_avail_data );
			}
		}

		wp_send_json_success( [
			'status'     => true,
			'message'    => __( 'Availability updated successfully.', 'tourfic' ),
			'avail_date' => json_encode( $room_avail_data ),
		] );

		die();
	}

	add_action( 'wp_ajax_tf_add_hotel_availability', 'tf_add_hotel_availability' );
}

/*
 * Get hotel availability calendar
 * @author Foysal
 */
if ( ! function_exists( 'tf_get_hotel_availability' ) ) {
	function tf_get_hotel_availability() {
		$new_post   = isset( $_POST['new_post'] ) && ! empty( $_POST['new_post'] ) ? sanitize_text_field( $_POST['new_post'] ) : '';
		$hotel_id   = isset( $_POST['hotel_id'] ) && ! empty( $_POST['hotel_id'] ) ? sanitize_text_field( $_POST['hotel_id'] ) : '';
		$room_index = isset( $_POST['room_index'] ) ? intval( $_POST['room_index'] ) : '';
		$avail_date = isset( $_POST['avail_date'] ) && ! empty( $_POST['avail_date'] ) ? sanitize_text_field( $_POST['avail_date'] ) : '';

		if ( $new_post != 'true' ) {
			$hotel_avail_data = get_post_meta( $hotel_id, 'tf_hotels_opt', true );
			$room_avail_data  = isset( $hotel_avail_data['room'][ $room_index ]['avail_date'] ) && ! empty( $hotel_avail_data['room'][ $room_index ]['avail_date'] ) ? json_decode( $hotel_avail_data['room'][ $room_index ]['avail_date'], true ) : [];
		} else {
			$room_avail_data = json_decode( stripslashes( $avail_date ), true );
		}

		if ( ! empty( $room_avail_data ) && is_array( $room_avail_data ) ) {
			$room_avail_data = array_values( $room_avail_data );
			$room_avail_data = array_map( function ( $item ) {
				$item['start'] = date( 'Y-m-d', strtotime( $item['check_in'] ) );
				$item['title'] = $item['price_by'] == '1' ? __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) : __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );
//				$item['title'] = __( 'Price: ', 'tourfic' ) . wc_price( $item['price'] ) . '<br>' . __( 'Adult: ', 'tourfic' ) . wc_price( $item['adult_price'] ) . '<br>' . __( 'Child: ', 'tourfic' ) . wc_price( $item['child_price'] );

				if ( $item['status'] == 'unavailable' ) {
					$item['display'] = 'background';
					$item['color']   = '#003c79';
				}

				return $item;
			}, $room_avail_data );
		} else {
			$room_avail_data = [];
		}

		echo json_encode( $room_avail_data );
		die();
	}

	add_action( 'wp_ajax_tf_get_hotel_availability', 'tf_get_hotel_availability' );
}

/*
 * Update hotel room avail_date price based on pricing type
 * @auther Foysal
 */
if ( ! function_exists( 'tf_update_room_avail_date_price' ) ) {
	function tf_update_room_avail_date_price( $post_id, $post ) {
		if ( $post->post_type == 'tf_hotel' ) {
			$meta  = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
			if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
				$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $rooms );
				$rooms                = unserialize( $tf_hotel_rooms_value );
			}

			if ( ! empty( $rooms ) ) {
				foreach ( $rooms as $roomIndex => $room ) {
					$pricing_by   = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
					$price        = ! empty( $room['price'] ) ? $room['price'] : '';
					$adult_price  = ! empty( $room['adult_price'] ) ? $room['adult_price'] : '';
					$child_price  = ! empty( $room['child_price'] ) ? $room['child_price'] : '';
					$avil_by_date = ! empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : '';

					if ( $avil_by_date === '1' && ! empty( $room['avail_date'] ) ) {
						$hotel_avail_data = json_decode( $room['avail_date'], true );

						if ( isset( $hotel_avail_data ) && ! empty( $hotel_avail_data ) ) {

							$hotel_avail_data = array_map( function ( $item ) use ( $pricing_by, $price, $adult_price, $child_price ) {

								if ( $pricing_by == '1' ) {
									$item['price'] = !isset($item['price']) ? $price : $item['price'];
								} else {
									$item['adult_price'] = !isset($item['adult_price']) ? $adult_price : $item['adult_price'];
                                    $item['child_price'] = !isset($item['child_price']) ? $child_price : $item['child_price'];
								}
								$item['price_by'] = $pricing_by;

								return $item;
							}, $hotel_avail_data );
						}

						$meta['room'][ $roomIndex ]['avail_date'] = json_encode( $hotel_avail_data );
					} elseif ( $avil_by_date === '1' && empty( $room['avail_date'] ) ) {
						//add next 5 years availability
						$hotel_avail_data = [];
                        for ( $i = 0; $i <= 1825; $i ++ ) {
                            $tf_room_date                     = date( 'Y/m/d', strtotime( "+$i day" ) );
                            $tf_room_data                     = [
                                'check_in'    => $tf_room_date,
                                'check_out'   => $tf_room_date,
                                'price_by'    => $pricing_by,
                                'price'       => $price,
                                'adult_price' => $adult_price,
                                'child_price' => $child_price,
                                'status'      => 'available'
                            ];
                            $hotel_avail_data[ $tf_room_date ] = $tf_room_data;
                        }

                        $meta['room'][ $roomIndex ]['avail_date'] = json_encode( $hotel_avail_data );
					}
				}
			}
			update_post_meta( $post_id, 'tf_hotels_opt', $meta );
		}
	}

	add_action( 'save_post', 'tf_update_room_avail_date_price', 9999, 2 );
}

/*
 * Apartment availability calendar update
 * @auther Foysal
 */
if ( ! function_exists( 'tf_add_apartment_availability' ) ) {
	function tf_add_apartment_availability() {
		$date_format         = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";
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

		if ( $date_format == 'Y.m.d' || $date_format == 'd.m.Y' ) {
			$check_in  = date( "Y-m-d", strtotime( str_replace( ".", "-", $check_in ) ) );
			$check_out = date( "Y-m-d", strtotime( str_replace( ".", "-", $check_out ) ) );
		}

		$check_in  = strtotime( $check_in );
		$check_out = strtotime( $check_out );
		if ( $check_in > $check_out ) {
			wp_send_json_error( [
				'status'  => false,
				'message' => __( 'Check in date must be less than check out date.', 'tourfic' )
			] );
		}

		$apt_availability_data = [];
		for ( $i = $check_in; $i <= $check_out; $i = strtotime( '+1 day', $i ) ) {
			$tf_apt_date                           = date( 'Y/m/d', $i );
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
			$apartment_data['apt_availability'] = json_encode( $apt_availability_data );
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
			'apt_availability' => json_encode( $apt_availability_data ),
		] );

		die();
	}

	add_action( 'wp_ajax_tf_add_apartment_availability', 'tf_add_apartment_availability' );
}

/*
 * Get apartment availability calendar
 * @auther Foysal
 */
if ( ! function_exists( 'tf_get_apartment_availability' ) ) {
	function tf_get_apartment_availability() {
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
				$item['start'] = date( 'Y-m-d', strtotime( $item['check_in'] ) );
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

		echo json_encode( $apt_availability_data );
		die();
	}

	add_action( 'wp_ajax_tf_get_apartment_availability', 'tf_get_apartment_availability' );
}

/*
 * Update apt_availability price based on pricing type
 * @auther Foysal
 */
if ( ! function_exists( 'tf_update_apt_availability_price' ) ) {
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

				$meta['apt_availability'] = json_encode( $apt_availability_data );
				update_post_meta( $post_id, 'tf_apartment_opt', $meta );

			} elseif ( $enable_availability === '1' && empty( $meta['apt_availability'] ) ) {
				//add next 5 years availability
				$apt_availability_data = [];
				for ( $i = strtotime( date( 'Y-m-d' ) ); $i <= strtotime( '+5 year', strtotime( date( 'Y-m-d' ) ) ); $i = strtotime( '+1 day', $i ) ) {
					$tf_apt_date                           = date( 'Y/m/d', $i );
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

				$meta['apt_availability'] = json_encode( $apt_availability_data );
				update_post_meta( $post_id, 'tf_apartment_opt', $meta );
			}
		}
	}

	add_action( 'save_post', 'tf_update_apt_availability_price', 99, 2 );
}
/*
 * Get all icons list
 * @author Foysal
 */
function get_icon_list() {
	$icons = array(
		'all'           => array(
			'label'      => __( 'All Icons', 'tourfic' ),
			'label_icon' => 'ri-grid-fill',
			'icons'      => array_merge( fontawesome_four_icons(), fontawesome_five_icons(), fontawesome_six_icons(), remix_icon() ),
		),
		'fontawesome_4' => array(
			'label'      => __( 'Font Awesome 4', 'tourfic' ),
			'label_icon' => 'fa-regular fa-font-awesome',
			'icons'      => fontawesome_four_icons(),
		),
		'fontawesome_5' => array(
			'label'      => __( 'Font Awesome 5', 'tourfic' ),
			'label_icon' => 'fa-regular fa-font-awesome',
			'icons'      => fontawesome_five_icons(),
		),
		'fontawesome_6' => array(
			'label'      => __( 'Font Awesome 6', 'tourfic' ),
			'label_icon' => 'fa-regular fa-font-awesome',
			'icons'      => fontawesome_six_icons(),
		),
		'remixicon'     => array(
			'label'      => __( 'Remix Icon', 'tourfic' ),
			'label_icon' => 'ri-remixicon-line',
			'icons'      => remix_icon(),
		),
	);

	$icons = apply_filters( 'tf_icon_list', $icons );

	return $icons;
}

/*
 * Icon infinite scroll
 * @author Foysal
 */
if ( ! function_exists( 'tf_load_more_icons' ) ) {
	add_action( 'wp_ajax_tf_load_more_icons', 'tf_load_more_icons' );
	function tf_load_more_icons() {
		$start_index = isset( $_POST['start_index'] ) ? intval( $_POST['start_index'] ) : 0;
		$type        = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'all';
		$icon_list   = get_icon_list();
		$icons       = array_slice( $icon_list[ $type ]['icons'], $start_index, 100 );

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
}
