<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Room;

/**
 * Show admin warning if a required file is missing
 */
function tf_file_missing( $files = '' ) {

	if ( is_admin() ) {
		if ( ! empty( $files ) ) {
			$class   = 'notice notice-error';
			$message = '<strong>' . $files . '</strong>' . esc_html__( ' file is missing! It is required to function Tourfic properly!', 'tourfic' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );
		}
	}

}

add_action( 'admin_notices', 'tf_file_missing' );
add_action( 'plugins_loaded', 'tf_add_elelmentor_addon' );


/*
 * Temporary functions
 */
if(!function_exists('tf_data_types')){
	function tf_data_types( $var ) {
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

if(!function_exists('tourfic_character_limit_callback')){
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
}

if(!function_exists('tf_is_search_form_tab_type')){
	function tf_is_search_form_tab_type( $type, $type_arr ) {
		if ( in_array( $type, $type_arr ) || in_array( 'all', $type_arr ) ) {
			return true;
		}

		return false;
	}
}

if(!function_exists('tf_is_search_form_single_tab')){
	function tf_is_search_form_single_tab( $type_arr ) {
		if ( count( $type_arr ) === 1 && $type_arr[0] !== 'all' ) {
			return true;
		}

		return false;
	}
}

function tourfic_template_settings() {
	$tf_plugin_installed = get_option( 'tourfic_template_installed' );
	if ( ! empty( $tf_plugin_installed ) ) {
		$template = 'design-1';
	} else {
		$template = 'default';
	}

	return $template;
}

if(!function_exists('tourfic_order_table_data')){
	function tourfic_order_table_data( $query ) {
		global $wpdb;
		$query_type          = $query['post_type'];
		$query_select        = $query['select'];
		$query_where         = $query['query'];
		$tf_tour_book_orders = $wpdb->get_results( $wpdb->prepare( "SELECT $query_select FROM {$wpdb->prefix}tf_order_data WHERE post_type = %s $query_where", $query_type ), ARRAY_A );

		return $tf_tour_book_orders;
	}
}

if ( ! function_exists( 'tourfic_get_user_order_table_data' ) ) {
	function tourfic_get_user_order_table_data( $query ) {
		global $wpdb;
		$query_select   = $query['select'];
		$query_type     = $query['post_type'];
		$query_customer = $query['customer_id']; // Change from 'author' to 'customer_id'
		$query_limit    = $query['limit'];

		// Adjust the query to use customer_id instead of post_author
		if ( ! is_array( $query_type ) ) {
			$vendor_query = $wpdb->prepare(
				"SELECT $query_select FROM {$wpdb->prefix}tf_order_data WHERE post_type = %s AND customer_id = %d ORDER BY order_id DESC $query_limit",
				$query_type, $query_customer
			);
		} else {
			$vendor_query = $wpdb->prepare(
				"SELECT $query_select FROM {$wpdb->prefix}tf_order_data WHERE post_type IN (" . implode( ',', array_fill( 0, count( $query_type ), '%s' ) ) . ") AND customer_id = %d ORDER BY order_id DESC $query_limit",
				array_merge( $query_type, array( $query_customer ) ) // Add customer_id to the array
			);
		}

		$orders_result = $wpdb->get_results( $vendor_query, ARRAY_A );

		return $orders_result;
	}
}

if(!function_exists('tf_affiliate_callback')){
	function tf_affiliate_callback() {
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
}

if(!function_exists('tf_set_order')){
	function tf_set_order( $order_data ) {
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
}

if(!function_exists('tf_custom_wp_kses_allow_tags')){
	function tf_custom_wp_kses_allow_tags() {
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
}

if(!function_exists('tf_convert_date_format')) {
	function tf_convert_date_format( $date, $currentFormat ) {
		$dateTime = DateTime::createFromFormat( $currentFormat, $date );

		if ( $dateTime === false ) {
			return false;
		}

		return $dateTime->format( 'Y/m/d' );
	}
}

if(!function_exists('tf_tour_date_format_changer')) {
	function tf_tour_date_format_changer($date, $format) {
		if(!empty($date) && !empty($format)) {
			$date = new DateTime($date);
			$formattedDate = $date->format($format);

			return $formattedDate;

		} else return;
	}
}

/**
 * Remove room order ids
 */
function tf_remove_order_ids_from_room() {
	echo '
    <div class="csf-title">
        <h4>' . __( "Reset Room Availability", "tourfic" ) . '</h4>
        <div class="csf-subtitle-text">' . __( "Remove order ids linked with this room.<br><b style='color: red;'>Be aware! It is irreversible!</b>", "tourfic" ) . '</div>
    </div>
    <div class="csf-fieldset">
        <button type="button" class="button button-large tf-order-remove remove-order-ids">' . __( "Reset", "tourfic" ) . '</button>
    </div>
    <div class="clear"></div>
    ';
}

if(!function_exists('tf_filter_hotel_by_date')) {
	function tf_filter_hotel_by_date( $period, array &$not_found, array $data = [] ): void {

		// Form Data
		if ( isset( $data[4] ) && isset( $data[5] ) ) {
			[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $room, $check_in_out ] = $data;
		}

		// Get hotel Room meta options
		$rooms = Room::get_hotel_rooms( get_the_ID() );

		//all rooms meta
		$rooms_meta = [];
		if ( ! empty( $rooms ) ) {
			foreach ( $rooms as $single_room ) {
				$rooms_meta[ $single_room->ID ] = get_post_meta( $single_room->ID, 'tf_room_opt', true );
			}
		}

		// Remove disabled rooms

		if ( ! empty( $rooms_meta ) ):
			$rooms_meta = array_filter( $rooms_meta, function ( $value ) {
				return ! empty( $value ) && empty( $value['enable'] ) ? $value['enable'] : '' != '0';
			} );
		endif;

		// If no room return
		if ( empty( $rooms_meta ) ) {
			return;
		}

		// Set initial room availability status
		$has_hotel = false;

		/**
		 * Adult Number Validation
		 */
		$back_adults   = array_column( $rooms_meta, 'adult' );
		$adult_counter = 0;
		foreach ( $back_adults as $back_adult ) {
			if ( ! empty( $back_adult ) && $back_adult >= $adults ) {
				$adult_counter ++;
			}
		}

		$adult_result = array_filter( $back_adults );

		/**
		 * Child Number Validation
		 */
		$back_childs   = array_column( $rooms_meta, 'child' );
		$child_counter = 0;
		foreach ( $back_childs as $back_child ) {
			if ( ! empty( $back_child ) && $back_child >= $child ) {
				$child_counter ++;
			}
		}

		$childs_result = array_filter( $back_childs );

		/**
		 * Room Number Validation
		 */
		$back_rooms   = array_column( $rooms_meta, 'num-room' );
		$room_counter = 0;
		foreach ( $back_rooms as $back_room ) {
			if ( ! empty( $back_room ) && $back_room >= $room ) {
				$room_counter ++;
			}
		}

		$room_result = array_filter( $back_rooms );

		// If adult and child number validation is true proceed
		if ( ! empty( $adult_result ) && $adult_counter > 0 && ! empty( $childs_result ) && $child_counter > 0 && ! empty( $room_result ) && $room_counter > 0 ) {

			// Check custom date range status of room
			$avil_by_date = array_column( $rooms_meta, 'avil_by_date' );

			// Check if any room available without custom date range
			if ( in_array( 0, $avil_by_date ) || empty( $avil_by_date ) || empty( $avil_by_date[0] ) ) {

				if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
					foreach ( $rooms as $_room ) {
						$room = get_post_meta( $_room->ID, 'tf_room_opt', true );

						if ( '2' == $room['pricing-by'] ) {
							if ( ! empty( $room['adult_price'] ) ) {
								if ( $startprice <= $room['adult_price'] && $room['adult_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $room['child_price'] ) ) {
								if ( $startprice <= $room['child_price'] && $room['child_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
						if ( '1' == $room['pricing-by'] ) {
							if ( ! empty( $room['price'] ) ) {
								if ( $startprice <= $room['price'] && $room['price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
					}
				} else {
					$has_hotel = true; // Show that hotel
				}

			} else {
				// If all the room has custom date range then filter the rooms by date

				// Get custom date range repeater
				$dates = array_column( $rooms_meta, 'avail_date' );
				// If no date range return
				if ( empty( $dates ) ) {
					return;
				}

				$tf_check_in_date = 0;
				$searching_period = [];
				// Check if any date range match with search form date range and set them on array
				if ( ! empty( $period ) ) {
					foreach ( $period as $datekey => $date ) {
						if ( 0 == $datekey ) {
							$tf_check_in_date = $date->format( 'Y/m/d' );
						}
						$searching_period[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
					}
				}

				// Initial available dates array
				$availability_dates     = [];
				$tf_check_in_date_price = [];
				// Run loop through custom date range repeater and filter out only the dates
				foreach ( $dates as $date ) {
					if ( ! empty( $date ) && gettype( $date ) == "string" ) {
						$date = json_decode( $date, true );
						foreach ( $date as $sdate ) {
							if ( $tf_check_in_date == $sdate['check_in'] ) {
								$tf_check_in_date_price['price']       = $sdate['price'];
								$tf_check_in_date_price['adult_price'] = $sdate['adult_price'];
								$tf_check_in_date_price['child_price'] = $sdate['child_price'];
							}
							$availability_dates[ $sdate['check_in'] ] = $sdate['check_in'];
						}
					}
				}

				$tf_common_dates = array_intersect( $availability_dates, $searching_period );

				//Initial matching date array
				$show_hotel = [];

				if ( count( $tf_common_dates ) === count( $searching_period ) ) {
					$show_hotel[] = 1;
				}

				// If any date range matches show hotel
				if ( ! empty( $show_hotel ) && ! in_array( 0, $show_hotel ) ) {
					if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
						foreach ( $rooms as $_room ) {
							$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
							if ( ! empty( $tf_check_in_date_price['adult_price'] ) ) {
								if ( $startprice <= $tf_check_in_date_price['adult_price'] && $tf_check_in_date_price['adult_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $tf_check_in_date_price['child_price'] ) ) {
								if ( $startprice <= $tf_check_in_date_price['child_price'] && $tf_check_in_date_price['child_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $tf_check_in_date_price['price'] ) ) {
								if ( $startprice <= $tf_check_in_date_price['price'] && $tf_check_in_date_price['price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
					} else {
						$has_hotel = true;
					}
				}
			}

		}

		// If adult and child number validation is true proceed
		if ( ! empty( $adult_result ) && $adult_counter > 0 && empty( $childs_result ) && $child_counter == 0 && ! empty( $room_result ) && $room_counter > 0 ) {

			// Check custom date range status of room
			$avil_by_date = array_column( $rooms_meta, 'avil_by_date' );

			// Check if any room available without custom date range
			if ( in_array( 0, $avil_by_date ) || empty( $avil_by_date ) || empty( $avil_by_date[0] ) ) {

				if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
					foreach ( $rooms as $_room ) {
						$room = get_post_meta( $_room->ID, 'tf_room_opt', true );

						if ( '2' == $room['pricing-by'] ) {
							if ( ! empty( $room['adult_price'] ) ) {
								if ( $startprice <= $room['adult_price'] && $room['adult_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $room['child_price'] ) ) {
								if ( $startprice <= $room['child_price'] && $room['child_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
						if ( '1' == $room['pricing-by'] ) {
							if ( ! empty( $room['price'] ) ) {
								if ( $startprice <= $room['price'] && $room['price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
					}
				} else {
					$has_hotel = true; // Show that hotel
				}

			} else {
				// If all the room has custom date range then filter the rooms by date

				// Get custom date range repeater
				$dates = array_column( $rooms_meta, 'avail_date' );

				// If no date range return
				if ( empty( $dates ) ) {
					return;
				}

				$tf_check_in_date = 0;
				$searching_period = [];
				// Check if any date range match with search form date range and set them on array
				if ( ! empty( $period ) ) {
					foreach ( $period as $datekey => $date ) {
						if ( 0 == $datekey ) {
							$tf_check_in_date = $date->format( 'Y/m/d' );
						}
						$searching_period[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
					}
				}

				// Initial available dates array
				$availability_dates     = [];
				$tf_check_in_date_price = [];
				// Run loop through custom date range repeater and filter out only the dates
				foreach ( $dates as $date ) {
					if ( ! empty( $date ) && gettype( $date ) == "string" ) {
						$date = json_decode( $date, true );
						foreach ( $date as $sdate ) {
							if ( $tf_check_in_date == $sdate['check_in'] ) {
								$tf_check_in_date_price['price']       = $sdate['price'];
								$tf_check_in_date_price['adult_price'] = $sdate['adult_price'];
								$tf_check_in_date_price['child_price'] = $sdate['child_price'];
							}
							$availability_dates[ $sdate['check_in'] ] = $sdate['check_in'];
						}
					}
				}

				$tf_common_dates = array_intersect( $availability_dates, $searching_period );

				//Initial matching date array
				$show_hotel = [];

				if ( count( $tf_common_dates ) === count( $searching_period ) ) {
					$show_hotel[] = 1;
				}

				// If any date range matches show hotel
				if ( ! in_array( 0, $show_hotel ) ) {
					if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
						foreach ( $rooms as $_room ) {
							$room = get_post_meta( $_room->ID, 'tf_room_opt', true );
							if ( ! empty( $tf_check_in_date_price['adult_price'] ) ) {
								if ( $startprice <= $tf_check_in_date_price['adult_price'] && $tf_check_in_date_price['adult_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $tf_check_in_date_price['child_price'] ) ) {
								if ( $startprice <= $tf_check_in_date_price['child_price'] && $tf_check_in_date_price['child_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $tf_check_in_date_price['price'] ) ) {
								if ( $startprice <= $tf_check_in_date_price['price'] && $tf_check_in_date_price['price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
					} else {
						$has_hotel = true;
					}
				}

			}

		}

		// Conditional hotel showing
		if ( $has_hotel ) {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 0,
			);

		} else {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 1,
			);
		}

	}
}

//review temp functions
if(!function_exists('tf_calculate_comments_rating')){
	function tf_calculate_comments_rating( $comments, &$tf_overall_rate, &$total_rating ) {

        $tf_overall_rate = [];
        foreach ( $comments as $comment ) {
            tf_calculate_user_ratings( $comment, $tf_overall_rate, $total_rating );
    
        }
        $total_rating = tf_average_ratings( $total_rating );
    
    }
}

if(!function_exists('tf_calculate_user_ratings')){
	function tf_calculate_user_ratings( $comment, &$overall_rating, &$total_rate ) {
        if ( ! is_array( $total_rate ) ) {
            $total_rate = array();
        }
        $tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
        $tf_base_rate    = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
    
        if ( $tf_comment_meta ) {
            $total_rate[] = tf_average_rating_change_on_base( tf_average_ratings( $tf_comment_meta ), $tf_base_rate );
    
            foreach ( $tf_comment_meta as $key => $ratings ) {
                // calculate rate
                $ratings = tf_average_rating_change_on_base( $ratings, $tf_base_rate );
    
                if ( is_array( $ratings ) ) {
                    $overall_rating[ $key ][] = tf_average_ratings( $ratings );
                } else {
                    $overall_rating[ $key ][] = $ratings;
                }
    
            }
        }
    }
}

if(!function_exists('tf_average_ratings')){
	function tf_average_ratings( $ratings = [] ) {

        if ( ! $ratings ) {
            return 0;
        }
    
        // No sub collection of ratings
        if ( count( $ratings ) == count( $ratings, COUNT_RECURSIVE ) ) {
            $average = array_sum( $ratings ) / count( $ratings );
        } else {
            $average = 0;
            foreach ( $ratings as $rating ) {
                $average += array_sum( $rating ) / count( $rating );
            }
            $average = $average / count( $ratings );
        }
    
        return sprintf( '%.1f', $average );
    }
}

if(!function_exists('tf_average_rating_change_on_base')){
	function tf_average_rating_change_on_base( $rating, $base_rate = 5 ) {

        $settings_base = ! empty ( tfopt( 'r-base' ) ) ? tfopt( 'r-base' ) : 5;
        $base_rate     = ! empty ( $base_rate ) ? $base_rate : 5;
    
        if ( $settings_base != $base_rate ) {
            if ( $settings_base > 5 ) {
                $rating = $rating * 2;
            } else {
                $rating = $rating / 2;
            }
        }
    
        return $rating;
    }
}