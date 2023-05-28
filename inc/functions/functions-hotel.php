<?php
# don't load directly
defined( 'ABSPATH' ) || exit;

#################################
# Custom post types, taxonomies #
#################################
/**
 * Register tf_hotel
 */
function register_tf_hotel_post_type() {

	$hotel_slug = ! empty( get_option( 'hotel_slug' ) ) ? get_option( 'hotel_slug' ) : apply_filters( 'tf_hotel_slug', 'hotels' );

	$hotel_labels = apply_filters( 'tf_hotel_labels', array(
		'name'                  => _x( '%2$s', 'tourfic post type name', 'tourfic' ),
		'singular_name'         => _x( '%1$s', 'singular tourfic post type name', 'tourfic' ),
		'add_new'               => __( 'Add New', 'tourfic' ),
		'add_new_item'          => __( 'Add New %1$s', 'tourfic' ),
		'edit_item'             => __( 'Edit %1$s', 'tourfic' ),
		'new_item'              => __( 'New %1$s', 'tourfic' ),
		'all_items'             => __( 'All %2$s', 'tourfic' ),
		'view_item'             => __( 'View %1$s', 'tourfic' ),
		'view_items'            => __( 'View %2$s', 'tourfic' ),
		'search_items'          => __( 'Search %2$s', 'tourfic' ),
		'not_found'             => __( 'No %2$s found', 'tourfic' ),
		'not_found_in_trash'    => __( 'No %2$s found in Trash', 'tourfic' ),
		'parent_item_colon'     => '',
		'menu_name'             => _x( 'Hotels', 'tourfic post type menu name', 'tourfic' ),
		'featured_image'        => __( '%1$s Image', 'tourfic' ),
		'set_featured_image'    => __( 'Set %1$s Image', 'tourfic' ),
		'remove_featured_image' => __( 'Remove %1$s Image', 'tourfic' ),
		'use_featured_image'    => __( 'Use as %1$s Image', 'tourfic' ),
		'attributes'            => __( '%1$s Attributes', 'tourfic' ),
		'filter_items_list'     => __( 'Filter %2$s list', 'tourfic' ),
		'items_list_navigation' => __( '%2$s list navigation', 'tourfic' ),
		'items_list'            => __( '%2$s list', 'tourfic' ),
	) );

	foreach ( $hotel_labels as $key => $value ) {
		$hotel_labels[ $key ] = sprintf( $value, tf_hotel_singular_label(), tf_hotel_plural_label() );
	}

	$hotel_args = array(
		'labels'             => $hotel_labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'menu_icon'          => 'dashicons-building',
		'rewrite'            => array( 'slug' => $hotel_slug, 'with_front' => false ),
		'capability_type'    => array( 'tf_hotel', 'tf_hotels' ),
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 26.2,
		'supports'           => apply_filters( 'tf_hotel_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
	);

	register_post_type( 'tf_hotel', apply_filters( 'tf_hotel_post_type_args', $hotel_args ) );
}

// Enable/disable check
if ( tfopt( 'disable-services' ) && in_array( 'hotel', tfopt( 'disable-services' ) ) ) {
} else {
	add_action( 'init', 'register_tf_hotel_post_type' );
}

/**
 * Get Default Labels
 *
 * @return array $defaults Default labels
 * @since 1.0
 */
function tf_hotel_default_labels() {
	$default_hotel = array(
		'singular' => __( 'Hotel', 'tourfic' ),
		'plural'   => __( 'Hotels', 'tourfic' ),
	);

	return apply_filters( 'tf_hotel_name', $default_hotel );
}

/**
 * Get Singular Label
 *
 * @param bool $lowercase
 *
 * @return string $defaults['singular'] Singular label
 * @since 1.0
 *
 */
function tf_hotel_singular_label( $lowercase = false ) {
	$default_hotel = tf_hotel_default_labels();

	return ( $lowercase ) ? strtolower( $default_hotel['singular'] ) : $default_hotel['singular'];
}

/**
 * Get Plural Label
 *
 * @return string $defaults['plural'] Plural label
 * @since 1.0
 */
function tf_hotel_plural_label( $lowercase = false ) {
	$default_hotel = tf_hotel_default_labels();

	return ( $lowercase ) ? strtolower( $default_hotel['plural'] ) : $default_hotel['plural'];
}

/**
 * Register taxonomies for tf_hotel
 *
 * hotel_location, hotel_feature
 */
function tf_hotel_taxonomies_register() {

	/**
	 * Taxonomy: hotel_location
	 */
	$hotel_location_slug = apply_filters( 'hotel_location_slug', 'hotel-location' );

	$hotel_location_labels = array(
		'name'                       => __( 'Locations', 'tourfic' ),
		'singular_name'              => __( 'Location', 'tourfic' ),
		'menu_name'                  => __( 'Location', 'tourfic' ),
		'all_items'                  => __( 'All Locations', 'tourfic' ),
		'edit_item'                  => __( 'Edit Location', 'tourfic' ),
		'view_item'                  => __( 'View Location', 'tourfic' ),
		'update_item'                => __( 'Update location name', 'tourfic' ),
		'add_new_item'               => __( 'Add new location', 'tourfic' ),
		'new_item_name'              => __( 'New location name', 'tourfic' ),
		'parent_item'                => __( 'Parent Location', 'tourfic' ),
		'parent_item_colon'          => __( 'Parent Location:', 'tourfic' ),
		'search_items'               => __( 'Search Location', 'tourfic' ),
		'popular_items'              => __( 'Popular Location', 'tourfic' ),
		'separate_items_with_commas' => __( 'Separate location with commas', 'tourfic' ),
		'add_or_remove_items'        => __( 'Add or remove location', 'tourfic' ),
		'choose_from_most_used'      => __( 'Choose from the most used location', 'tourfic' ),
		'not_found'                  => __( 'No location found', 'tourfic' ),
		'no_terms'                   => __( 'No location', 'tourfic' ),
		'items_list_navigation'      => __( 'Location list navigation', 'tourfic' ),
		'items_list'                 => __( 'Locations list', 'tourfic' ),
		'back_to_items'              => __( 'Back to location', 'tourfic' ),
	);

	$hotel_location_args = array(
		'labels'                => $hotel_location_labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => true,
		'query_var'             => true,
		'rewrite'               => array( 'slug' => $hotel_location_slug, 'with_front' => false ),
		'show_admin_column'     => true,
		'show_in_rest'          => true,
		'rest_base'             => 'hotel_location',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'show_in_quick_edit'    => true,
		'capabilities'          => array(
			'assign_terms' => 'edit_tf_hotel',
			'edit_terms'   => 'edit_tf_hotel',
		),
	);
	register_taxonomy( 'hotel_location', 'tf_hotel', apply_filters( 'hotel_location_args', $hotel_location_args ) );

	/**
	 * Taxonomy: hotel_feature.
	 */

	$labels = [
		"name"                       => __( "Features", 'tourfic' ),
		"singular_name"              => __( "Feature", 'tourfic' ),
		"menu_name"                  => __( "Features", 'tourfic' ),
		"all_items"                  => __( "All Features", 'tourfic' ),
		"edit_item"                  => __( "Edit Feature", 'tourfic' ),
		"view_item"                  => __( "View Feature", 'tourfic' ),
		"update_item"                => __( "Update Feature", 'tourfic' ),
		"add_new_item"               => __( "Add new Feature", 'tourfic' ),
		"new_item_name"              => __( "New Feature name", 'tourfic' ),
		"parent_item"                => __( "Parent Feature", 'tourfic' ),
		"parent_item_colon"          => __( "Parent Feature:", 'tourfic' ),
		"search_items"               => __( "Search Feature", 'tourfic' ),
		"popular_items"              => __( "Popular Features", 'tourfic' ),
		"separate_items_with_commas" => __( "Separate Features with commas", 'tourfic' ),
		"add_or_remove_items"        => __( "Add or remove Features", 'tourfic' ),
		"choose_from_most_used"      => __( "Choose from the most used Features", 'tourfic' ),
		"not_found"                  => __( "No Features found", 'tourfic' ),
		"no_terms"                   => __( "No Features", 'tourfic' ),
		"items_list_navigation"      => __( "Features list navigation", 'tourfic' ),
		"items_list"                 => __( "Features list", 'tourfic' ),
		"back_to_items"              => __( "Back to Features", 'tourfic' ),
	];

	$args = [
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'hotel_feature', 'with_front' => true ],
		"show_admin_column"     => true,
		"show_in_rest"          => true,
		"rest_base"             => "hotel_feature",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => true,
		'capabilities'          => array(
			'assign_terms' => 'edit_tf_hotel',
			'edit_terms'   => 'edit_tf_hotel',
		),
	];
	register_taxonomy( 'hotel_feature', 'tf_hotel', apply_filters( 'tf_feature_tax_args', $args ) );

}

add_action( 'init', 'tf_hotel_taxonomies_register' );

###############################################
# Functions related to post types, taxonomies #
###############################################

/**
 * Flushing Rewrite on Tourfic Activation
 *
 * tf_hotel post type
 * hotel_destination taxonomy
 */
function tf_hotel_rewrite_flush() {

	register_tf_hotel_post_type();
	tf_hotel_taxonomies_register();
	flush_rewrite_rules();

}

register_activation_hook( TF_PATH . 'tourfic.php', 'tf_hotel_rewrite_flush' );

/**
 * Get Hotel Locations
 *
 * {taxonomy-hotel_location}
 */
if ( ! function_exists( 'get_hotel_locations' ) ) {
	function get_hotel_locations() {

		$locations = array();

		$location_terms = get_terms( array(
			'taxonomy'   => 'hotel_location',
			'hide_empty' => true,
		) );

		foreach ( $location_terms as $location_term ) {
			$locations[ $location_term->slug ] = $location_term->name;
		}

		return $locations;
	}
}


#################################
# Air port Service          #
#################################

add_action( 'wp_ajax_tf_hotel_airport_service_price', 'tf_hotel_airport_service_callback' );
add_action( 'wp_ajax_nopriv_tf_hotel_airport_service_price', 'tf_hotel_airport_service_callback' );

function tf_hotel_airport_service_callback() {

	$meta            = get_post_meta( sanitize_key( $_POST['id'] ), 'tf_hotels_opt', true );
	$airport_service = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';

	if ( 1 == $airport_service ) {

		$post_id       = isset( $_POST['id'] ) ? intval( sanitize_text_field( $_POST['id'] ) ) : null;
		$room_id       = isset( $_POST['roomid'] ) ? intval( sanitize_text_field( $_POST['roomid'] ) ) : null;
		$adult         = isset( $_POST['hoteladult'] ) ? intval( sanitize_text_field( $_POST['hoteladult'] ) ) : '0';
		$child         = isset( $_POST['hotelchildren'] ) ? intval( sanitize_text_field( $_POST['hotelchildren'] ) ) : '0';
		$room_selected = isset( $_POST['room'] ) ? intval( sanitize_text_field( $_POST['room'] ) ) : '0';
		$check_in      = isset( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
		$check_out     = isset( $_POST['check_out_date'] ) ? sanitize_text_field( $_POST['check_out_date'] ) : '';
		$deposit       = isset( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : false;


		# Calculate night number
		if ( $check_in && $check_out ) {
			$check_in_stt   = strtotime( $check_in . ' +1 day' );
			$check_out_stt  = strtotime( $check_out );
			$day_difference = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
		}


		$meta  = get_post_meta( $post_id, 'tf_hotels_opt', true );
		$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
		if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
			$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $rooms );
			$rooms                = unserialize( $tf_hotel_rooms_value );
		}

		$avail_by_date = ! empty( $rooms[ $room_id ]['avil_by_date'] ) && $rooms[ $room_id ]['avil_by_date'];
		if ( $avail_by_date ) {
			$repeat_by_date = ! empty( $rooms[ $room_id ]['repeat_by_date'] ) ? $rooms[ $room_id ]['repeat_by_date'] : [];
		}

		$pricing_by      = $rooms[ $room_id ]['pricing-by'];
		$price_multi_day = ! empty( $rooms[ $room_id ]['price_multi_day'] ) ? $rooms[ $room_id ]['price_multi_day'] : false;


		/**
		 * Calculate Pricing
		 */
		if ( $avail_by_date && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

			// Check availability by date option
			$period = new DatePeriod(
				new DateTime( $check_in . ' 00:00' ),
				new DateInterval( 'P1D' ),
				new DateTime( $check_out . ' 00:00' )
			);

			$total_price = 0;
			foreach ( $period as $date ) {

				$available_rooms = array_values( array_filter( $repeat_by_date, function ( $date_availability ) use ( $date ) {
					$date_availability_from = strtotime( $date_availability['availability']['from'] . ' 00:00' );
					$date_availability_to   = strtotime( $date_availability['availability']['to'] . ' 23:59' );

					return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
				} ) );

				if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {
					$room_price  = ! empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $rooms[ $room_id ]['price'];
					$adult_price = ! empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $rooms[ $room_id ]['adult_price'];
					$child_price = ! empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $rooms[ $room_id ]['child_price'];
					$total_price += $pricing_by == '1' ? $room_price : ( ( $adult_price * $adult ) + ( $child_price * $child ) );

				};

			}

			$price_total = $total_price * $room_selected;
		} else {

			if ( $pricing_by == '1' ) {
				$total_price = $rooms[ $room_id ]['price'];

			} elseif ( $pricing_by == '2' ) {
				$adult_price = $rooms[ $room_id ]['adult_price'];
				$adult_price = $adult_price * $adult;
				$child_price = $rooms[ $room_id ]['child_price'];
				$child_price = $child_price * $child;
				$total_price = $adult_price + $child_price;

			}

			# Multiply pricing by night number
			if ( ! empty( $day_difference ) && $price_multi_day == true ) {
				$price_total = $total_price * $room_selected * $day_difference;
			} else {
				$price_total = $total_price * $room_selected;
			}

		}

		if ( $deposit == "true" ) {
			tf_get_deposit_amount( $rooms[ $room_id ], $price_total, $deposit_amount, $has_deposit );
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) {
				$deposit_amount;
			}
		}

		if ( "pickup" == $_POST['service_type'] ) {
			$airport_pickup_price = ! empty( $meta['airport_pickup_price'] ) ? $meta['airport_pickup_price'] : '';
			if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
				$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $airport_pickup_price );
				$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
			}
			if ( "per_person" == $airport_pickup_price['airport_pickup_price_type'] ) {
				$service_adult_fee = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
				$service_child_fee = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
				$service_fee       = ( sanitize_key( $_POST['hoteladult'] ) * $service_adult_fee ) + ( sanitize_key( $_POST['hotelchildren'] ) * $service_child_fee );

				if ( sanitize_key( $_POST['hotelchildren'] ) != 0 ) {
					echo "<span>";
					echo sprintf( __( 'Airport Pickup Fee Adult ( %s × %s ) + Child ( %s × %s ) : <b>%s</b>', 'tourfic' ),
						sanitize_key( $_POST['hoteladult'] ),
						wc_price( $service_adult_fee ),
						sanitize_key( $_POST['hotelchildren'] ),
						wc_price( $service_child_fee ),
						wc_price( $service_fee )
					);
					echo "</span></br>";
				} else {
					echo "<span>";
					echo sprintf( __( 'Airport Pickup Fee Adult ( %s × %s ) : <b>%s</b>', 'tourfic' ),
						sanitize_key( $_POST['hoteladult'] ),
						wc_price( $service_adult_fee ),
						wc_price( $service_fee )
					);
					echo "</span></br>";
				}
				if ( $deposit == "true" ) {
					echo "<span>";
					echo sprintf( __( 'Due Amount : <b>%s + %s</b>', 'tourfic' ),
						wc_price( $price_total - $deposit_amount ),
						wc_price( $service_fee )
					);
					echo "</span></br>";
					echo "<span>";
					echo sprintf( __( 'Total Payable Amount : <b>%s</b>', 'tourfic' ),
						wc_price( $deposit_amount )
					);
					echo "</span>";
				} else {
					echo "<span>";
					echo sprintf( __( 'Total Payable Amount : <b>%s</b>', 'tourfic' ),
						wc_price( $price_total + $service_fee )
					);
					echo "</span>";
				}
			}
			if ( "fixed" == $airport_pickup_price['airport_pickup_price_type'] ) {
				$service_fee = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
				echo sprintf( __( '<span>Airport Pickup Fee (Fixed): <b>%s</b></span></br>', 'tourfic' ),
					wc_price( $service_fee )
				);
				if ( $deposit == "true" ) {
					echo sprintf( __( '<span>Due Amount : <b>%s + %s</b></span></br>', 'tourfic' ),
						wc_price( $price_total - $deposit_amount ),
						wc_price( $service_fee )
					);
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $deposit_amount )
					);
				} else {
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $price_total + $service_fee )
					);
				}
			}
			if ( "free" == $airport_pickup_price['airport_pickup_price_type'] ) {
				echo __( '<span>Airport Pickup Fee: <b>Free</b></span></br>', 'tourfic' );
				if ( $deposit == "true" ) {
					echo sprintf( __( '<span>Due Amount : <b>%s</b></span></br>', 'tourfic' ),
						wc_price( $price_total - $deposit_amount )
					);
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $deposit_amount )
					);
				} else {
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $price_total )
					);
				}
			}
		}
		if ( "dropoff" == $_POST['service_type'] ) {
			$airport_dropoff_price = ! empty( $meta['airport_dropoff_price'] ) ? $meta['airport_dropoff_price'] : '';
			if ( ! empty( $airport_dropoff_price ) && gettype( $airport_dropoff_price ) == "string" ) {
				$tf_hotel_airport_dropoff_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $airport_dropoff_price );
				$airport_dropoff_price                = unserialize( $tf_hotel_airport_dropoff_price_value );
			}
			if ( "per_person" == $airport_dropoff_price['airport_pickup_price_type'] ) {
				$service_adult_fee = ! empty( $airport_dropoff_price['airport_service_fee_adult'] ) ? $airport_dropoff_price['airport_service_fee_adult'] : 0;
				$service_child_fee = ! empty( $airport_dropoff_price['airport_service_fee_children'] ) ? $airport_dropoff_price['airport_service_fee_children'] : 0;
				$service_fee       = ( sanitize_key( $_POST['hoteladult'] ) * $service_adult_fee ) + ( sanitize_key( $_POST['hotelchildren'] ) * $service_child_fee );
				if ( sanitize_key( $_POST['hotelchildren'] ) != 0 ) {
					echo sprintf( __( '<span>Airport Dropoff Fee Adult ( %s × %s ) + Child ( %s × %s ) : <b>%s</b></span></br>', 'tourfic' ),
						sanitize_key( $_POST['hoteladult'] ),
						wc_price( $service_adult_fee ),
						sanitize_key( $_POST['hotelchildren'] ),
						wc_price( $service_child_fee ),
						wc_price( $service_fee )
					);
				} else {
					echo sprintf( __( '<span>Airport Dropoff Fee Adult ( %s × %s ) : <b>%s</b></span></br>', 'tourfic' ),
						sanitize_key( $_POST['hoteladult'] ),
						wc_price( $service_adult_fee ),
						wc_price( $service_fee )
					);
				}
				if ( $deposit == "true" ) {
					echo sprintf( __( '<span>Due Amount : <b>%s + %s</b></span></br>', 'tourfic' ),
						wc_price( $price_total - $deposit_amount ),
						wc_price( $service_fee )
					);
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $deposit_amount )
					);
				} else {
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $price_total + $service_fee )
					);
				}
			}
			if ( "fixed" == $airport_dropoff_price['airport_pickup_price_type'] ) {
				$service_fee = ! empty( $airport_dropoff_price['airport_service_fee_fixed'] ) ? $airport_dropoff_price['airport_service_fee_fixed'] : 0;
				echo sprintf( __( '<span>Airport Dropoff Fee (Fixed): <b>%s</b></span></br>', 'tourfic' ),
					wc_price( $service_fee )
				);
				if ( $deposit == "true" ) {
					echo sprintf( __( '<span>Due Amount : <b>%s + %s</b></span></br>', 'tourfic' ),
						wc_price( $price_total - $deposit_amount ),
						wc_price( $service_fee )
					);
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $deposit_amount )
					);
				} else {
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $price_total + $service_fee )
					);
				}
			}
			if ( "free" == $airport_dropoff_price['airport_pickup_price_type'] ) {
				echo __( '<span>Airport Dropoff Fee: <b>Free</b></span></br>', 'tourfic' );
				if ( $deposit == "true" ) {
					echo sprintf( __( '<span>Due Amount : <b>%s</b></span></br>', 'tourfic' ),
						wc_price( $price_total - $deposit_amount )
					);
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $deposit_amount )
					);
				} else {
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $price_total )
					);
				}
			}
		}
		if ( "both" == $_POST['service_type'] ) {
			$airport_pickup_dropoff_price = ! empty( $meta['airport_pickup_dropoff_price'] ) ? $meta['airport_pickup_dropoff_price'] : '';
			if ( ! empty( $airport_pickup_dropoff_price ) && gettype( $airport_pickup_dropoff_price ) == "string" ) {
				$tf_hotel_airport_pickup_dropoff_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $airport_pickup_dropoff_price );
				$airport_pickup_dropoff_price                = unserialize( $tf_hotel_airport_pickup_dropoff_price_value );
			}
			if ( "per_person" == $airport_pickup_dropoff_price['airport_pickup_price_type'] ) {
				$service_adult_fee = ! empty( $airport_pickup_dropoff_price['airport_service_fee_adult'] ) ? $airport_pickup_dropoff_price['airport_service_fee_adult'] : 0;
				$service_child_fee = ! empty( $airport_pickup_dropoff_price['airport_service_fee_children'] ) ? $airport_pickup_dropoff_price['airport_service_fee_children'] : 0;
				$service_fee       = ( sanitize_key( $_POST['hoteladult'] ) * $service_adult_fee ) + ( sanitize_key( $_POST['hotelchildren'] ) * $service_child_fee );

				if ( sanitize_key( $_POST['hotelchildren'] ) != 0 ) {
					echo sprintf( __( '<span>Airport Pickup & Dropoff Fee Adult ( %s × %s ) + Child ( %s × %s ) : <b>%s</b></span></br>', 'tourfic' ),
						sanitize_key( $_POST['hoteladult'] ),
						wc_price( $service_adult_fee ),
						sanitize_key( $_POST['hotelchildren'] ),
						wc_price( $service_child_fee ),
						wc_price( $service_fee )
					);
				} else {
					echo sprintf( __( '<span>Airport Pickup & Dropoff Fee Adult ( %s × %s ) : <b>%s</b></span></br>', 'tourfic' ),
						sanitize_key( $_POST['hoteladult'] ),
						wc_price( $service_adult_fee ),
						wc_price( $service_fee )
					);
				}
				if ( $deposit == "true" ) {
					echo sprintf( __( '<span>Due Amount : <b>%s + %s</b></span></br>', 'tourfic' ),
						wc_price( $price_total - $deposit_amount ),
						wc_price( $service_fee )
					);
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $deposit_amount )
					);
				} else {
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $price_total + $service_fee )
					);
				}

			}
			if ( "fixed" == $airport_pickup_dropoff_price['airport_pickup_price_type'] ) {
				$service_fee = ! empty( $airport_pickup_dropoff_price['airport_service_fee_fixed'] ) ? $airport_pickup_dropoff_price['airport_service_fee_fixed'] : 0;
				echo sprintf( __( '<span>Airport Pickup & Dropoff Fee (Fixed): <b>%s</b></span></br>', 'tourfic' ),
					wc_price( $service_fee )
				);

				if ( $deposit == "true" ) {
					echo sprintf( __( '<span>Due Amount : <b>%s + %s</b></span></br>', 'tourfic' ),
						wc_price( $price_total - $deposit_amount ),
						wc_price( $service_fee )
					);
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $deposit_amount )
					);
				} else {
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $price_total + $service_fee )
					);
				}
			}
			if ( "free" == $airport_pickup_dropoff_price['airport_pickup_price_type'] ) {
				echo __( '<span>Airport Pickup & Dropoff Fee: <b>Free</b></span></br>', 'tourfic' );
				if ( $deposit == "true" ) {
					echo sprintf( __( '<span>Due Amount : <b>%s</b></span></br>', 'tourfic' ),
						wc_price( $price_total - $deposit_amount )
					);
					echo sprintf( __( '<span>Total Payable Amount : <b>%s</b></span>', 'tourfic' ),
						wc_price( $deposit_amount )
					);
				} else {
					echo "<span>Total Payable Amount : <b>" . wc_price( $price_total ) . "</b></span>";
				}
			}
		}

	}
	wp_die();
}


#################################
# Ajax functions                #
#################################
/**
 * Ajax hotel room availability
 *
 * @author fida
 */
add_action( 'wp_ajax_tf_room_availability', 'tf_room_availability_callback' );
add_action( 'wp_ajax_nopriv_tf_room_availability', 'tf_room_availability_callback' );
function tf_room_availability_callback() {

	// Check nonce security
	if ( ! isset( $_POST['tf_room_avail_nonce'] ) || ! wp_verify_nonce( $_POST['tf_room_avail_nonce'], 'check_room_avail_nonce' ) ) {
		return;
	}

	/**
	 * Form data
	 */
	$form_post_id      = ! empty( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
	$form_adult        = ! empty( $_POST['adult'] ) ? sanitize_text_field( $_POST['adult'] ) : 0;
	$form_child        = ! empty( $_POST['child'] ) ? sanitize_text_field( $_POST['child'] ) : 0;
	$children_ages     = ! empty( $_POST['children_ages'] ) ? sanitize_text_field( $_POST['children_ages'] ) : '';
	$form_check_in_out = ! empty( $_POST['check_in_out'] ) ? sanitize_text_field( $_POST['check_in_out'] ) : '';


	$form_total_person = $form_adult + $form_child;
	if ( $form_check_in_out ) {
		list( $form_start, $form_end ) = explode( ' - ', $form_check_in_out );
	}

	// Custom avail
	$tf_startdate = $form_start;
	$tf_enddate   = $form_end;

	if ( empty( $form_end ) ) {
		$form_end   = date( 'Y/m/d', strtotime( $form_start . " + 1 day" ) );
		$tf_enddate = date( 'Y/m/d', strtotime( $form_start . " + 1 day" ) );
	}
	$form_check_in = $form_start;
	$form_start    = date( 'Y/m/d', strtotime( $form_start . ' +1 day' ) );
	/**
	 * Backend data
	 */
	$meta  = get_post_meta( $form_post_id, 'tf_hotels_opt', true );
	$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
	if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
		$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $rooms );
		$rooms                = unserialize( $tf_hotel_rooms_value );
	}
	$locations           = get_the_terms( $form_post_id, 'hotel_location' );
	$first_location_name = ! empty( $locations ) ? $locations[0]->name : '';

	// start table
	ob_start();
	?>
    <div class="tf-room-table hotel-room-wrap">
    <div id="tour_room_details_loader">
        <div id="tour-room-details-loader-img">
            <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="">
        </div>
    </div>
	<?php 
	// Single Template Style
	$tf_hotel_single_template_check = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
	$tf_hotel_global_template_check = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-hotel'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-hotel'] : 'design-1';
	$tf_hotel_selected_template_check = !empty($tf_hotel_single_template_check) ? $tf_hotel_single_template_check : $tf_hotel_global_template_check;
	if( $tf_hotel_selected_template_check == "design-1" ){
	?>
	<table class="tf-availability-table">
	<thead>
		<tr>
			<th class="description" colspan="3"><?php _e( 'Room Details', 'tourfic' ); ?></th>
		</tr>
	</thead>
	<?php }else{ ?>
    <table class="availability-table">
    <thead>
    <tr>
        <th class="description"><?php _e( 'Room Details', 'tourfic' ); ?></th>
        <th class="pax"><?php _e( 'Pax', 'tourfic' ); ?></th>
        <th class="pricing"><?php _e( 'Price', 'tourfic' ); ?></th>
        <th class="reserve"><?php _e( 'Select Rooms', 'tourfic' ); ?></th>
    </tr>
    </thead>
	<?php } ?>
    <tbody>
	<?php
	echo ob_get_clean();
	$error    = $rows = null;
	$has_room = false;

	// generate table rows
	if ( ! empty( $rooms ) ) {
		ob_start();
		foreach ( $rooms as $room_id => $room ) {
			// Check if room is enabled
			$enable = ! empty( $room['enable'] ) && boolval( $room['enable'] );

			if ( $enable ) {

				/*
                * Backend room options
                */
				$footage          = ! empty( $room['footage'] ) ? $room['footage'] : 0;
				$bed              = ! empty( $room['bed'] ) ? $room['bed'] : 0;
				$adult_number     = ! empty( $room['adult'] ) ? $room['adult'] : 0;
				$child_number     = ! empty( $room['child'] ) ? $room['child'] : 0;
				$pricing_by       = ! empty( $room['pricing-by'] ) ? $room['pricing-by'] : '';
				$room_price       = ! empty( $room['price'] ) ? $room['price'] : 0;
				$room_adult_price = ! empty( $room['adult_price'] ) ? $room['adult_price'] : 0;
				$room_child_price = ! empty( $room['child_price'] ) ? $room['child_price'] : 0;
				$total_person     = $adult_number + $child_number;
				$price            = $pricing_by == '1' ? $room_price : $room_adult_price + $room_child_price;
				$form_check_out   = $form_end;

				// Check availability by date option

				$period = new DatePeriod(
					new DateTime( $form_start . ' 00:00' ),
					new DateInterval( 'P1D' ),
					new DateTime( $form_end . ' 23:59' )
				);

				$days = iterator_count( $period );

				// Check availability by date option

				$tfperiod = new DatePeriod(
					new DateTime( $tf_startdate . ' 00:00' ),
					new DateInterval( 'P1D' ),
					new DateTime( $tf_enddate . ' 23:59' )
				);

				$avail_durationdate = [];
				foreach ( $tfperiod as $date ) {
					$avail_durationdate[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
				}

				/**
				 * Set room availability
				 */
				$unique_id          = ! empty( $room['unique_id'] ) ? $room['unique_id'] : '';
				$order_ids          = ! empty( $room['order_id'] ) ? $room['order_id'] : '';
				$num_room_available = ! empty( $room['num-room'] ) ? $room['num-room'] : '1';
				$reduce_num_room    = ! empty( $room['reduce_num_room'] ) ? $room['reduce_num_room'] : false;
				$number_orders      = '0';
				$avil_by_date       = ! empty( $room['avil_by_date'] ) ? $room['avil_by_date'] : false;      // Room Available by date enabled or  not ?
				if ( $avil_by_date ) {
					$repeat_by_date = ! empty( $room['repeat_by_date'] ) ? $room['repeat_by_date'] : [];
				}

				if ( ! empty( $order_ids ) && function_exists( 'is_tf_pro' ) && is_tf_pro() && $reduce_num_room == true ) {

					# Get backend available date range as an array
					if ( $avil_by_date ) {

						$order_date_ranges = array();

						$backend_date_ranges = array();
						foreach ( $repeat_by_date as $single_date_range ) {

							array_push( $backend_date_ranges, array( strtotime( $single_date_range["availability"]["from"] ), strtotime( $single_date_range["availability"]["to"] ) ) );

						}
					}

					# Convert order ids to array
					$order_ids = explode( ',', $order_ids );

					# Run foreach loop through oder ids
					foreach ( $order_ids as $order_id ) {

						# Get $order object from order ID
						$order = wc_get_order( $order_id );

						# Get Only the completed orders
						if ( $order && $order->get_status() == 'completed' ) {

							# Get and Loop Over Order Items
							foreach ( $order->get_items() as $item_id => $item ) {

								/**
								 * Order item data
								 */
								$ordered_number_of_room = $item->get_meta( 'number_room_booked', true );

								if ( $avil_by_date ) {

									$order_check_in_date  = strtotime( $item->get_meta( 'check_in', true ) );
									$order_check_out_date = strtotime( $item->get_meta( 'check_out', true ) );

									$tf_order_check_in_date  = $item->get_meta( 'check_in', true );
									$tf_order_check_out_date = $item->get_meta( 'check_out', true );
									if ( ! empty( $avail_durationdate ) && ( in_array( $tf_order_check_out_date, $avail_durationdate ) || in_array( $tf_order_check_in_date, $avail_durationdate ) ) ) {
										# Total number of room booked
										$number_orders = $number_orders + $ordered_number_of_room;
									}
									array_push( $order_date_ranges, array( $order_check_in_date, $order_check_out_date ) );

								} else {
									$order_check_in_date  = $item->get_meta( 'check_in', true );
									$order_check_out_date = $item->get_meta( 'check_out', true );
									if ( ! empty( $avail_durationdate ) && ( in_array( $order_check_out_date, $avail_durationdate ) || in_array( $order_check_in_date, $avail_durationdate ) ) ) {
										# Total number of room booked
										$number_orders = $number_orders + $ordered_number_of_room;
									}

								}

							}
						}
					}
					# Calculate available room number after order
					$num_room_available = $num_room_available - $number_orders; // Calculate
					$num_room_available = max( $num_room_available, 0 ); // If negetive value make that 0

				}

				if ( $avil_by_date && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

					// split date range
					$check_in  = strtotime( $form_start . ' 00:00' );
					$check_out = strtotime( $form_end . ' 00:00' );
					$price     = 0;
					$has_room  = [];

					// extract price from available room options
					foreach ( $period as $date ) {

						$available_rooms = array_values( array_filter( $repeat_by_date, function ( $date_availability ) use ( $date ) {

							$date_availability_from = strtotime( $date_availability['availability']['from'] . ' 00:00' );
							$date_availability_to   = strtotime( $date_availability['availability']['to'] . ' 23:59' );

							return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;

						} ) );

						if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {

							$room_price      = ! empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $room_price;
							$adult_price     = ! empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $room_adult_price;
							$child_price     = ! empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $room['child_price'];
							$price_by_date   = $pricing_by == '1' ? $room_price : ( ( $adult_price * $form_adult ) + ( $child_price * $form_child ) );
							$price           += $price_by_date;
							$number_of_rooms = ! empty( $available_rooms[0]['num-room'] ) ? $available_rooms[0]['num-room'] : $room['num-room'];
							$has_room[]      = 1;

						} else {
							$has_room[] = 0;
						}

					}

					// Check if date is provided and within date range
					if ( ! in_array( 0, $has_room ) ) {
						tf_get_deposit_amount( $room, $price, $deposit_amount, $has_deposit );
						if ( $form_total_person <= $total_person ) {

							include TF_TEMPLATE_PART_PATH . 'hotel/hotel-availability-table-row.php';

						} else {

							$error = __( 'No Room Available! Total person number exceeds!', 'tourfic' );
						}

					} else {

						$error = __( 'No Room Available within this Date Range!', 'tourfic' );

					}
				} else {

					if ( $pricing_by == '1' ) {
						$price_by_date = $room_price;
					} else {
						$price_by_date = ( ( $room_adult_price * $form_adult ) + ( $room_child_price * $form_child ) );
					}

					$price = $room['price_multi_day'] == '1' ? $price_by_date * $days : $price_by_date;

					tf_get_deposit_amount( $room, $price, $deposit_amount, $has_deposit );

					/**
					 * filter hotel room with features
					 * @return array
					 * @since 1.6.9
					 * @author Abu Hena
					 */
					$filtered_features = ! empty( $_POST['features'] ) ? $_POST['features'] : array();
					$room_features     = ! empty( $room['features'] ) ? $room['features'] : '';
					if ( ! empty( $room_features ) && is_array( $room_features ) ) {
						$feature_result = array_intersect( $filtered_features, $room_features );
					}

					if ( ! empty( $filtered_features ) && defined( 'TF_PRO' ) ) {
						if ( $feature_result ) {
							if ( $form_total_person <= $total_person ) {

								include TF_TEMPLATE_PART_PATH . 'hotel/hotel-availability-table-row.php';

							} else {

								$error = __( 'No Room Available! Total person number exceeds!', 'tourfic' );

							}
						} else {
							$error = __( 'No Room Available!', 'tourfic' );
						}
						/* feature filter ended here */

					} else {
						if ( $form_total_person <= $total_person ) {

							include TF_TEMPLATE_PART_PATH . 'hotel/hotel-availability-table-row.php';

						} else {

							$error = __( 'No Room Available! Total person number exceeds!', 'tourfic' );

						}
					}
				}

			} else {

				$error = __( 'No Room Available!', 'tourfic' );

			}
		}

		$rows .= ob_get_clean();

	} else {

		$error = __( 'No Room Available!', 'tourfic' );

	}

	if ( ! empty( $rows ) ) {

		echo $rows . '</tbody> </table> </div>';

	} else {

		echo sprintf( "<tr><td colspan=\"4\" style=\"text-align:center;font-weight:bold;\">%s</td></tr>", __( $error, "tourfic" ) );
		?>
        </tbody>
        </table>
        </div>
		<?php

	}

	wp_die();
}


#################################
# All the forms                 #
# Search form, booking form     #
#################################

/**
 * Hotel Search form
 *
 * Horizontal
 *
 * Called in shortcodes
 */
if ( ! function_exists( 'tf_hotel_search_form_horizontal' ) ) {
	function tf_hotel_search_form_horizontal( $classes, $title, $subtitle ) {
		if ( isset( $_GET ) ) {
			$_GET = array_map( 'stripslashes_deep', $_GET );
		}
		// location
		$location = ! empty( $_GET['place'] ) ? sanitize_text_field( $_GET['place'] ) : '';
		// Adults
		$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		// children
		$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		// room
		$room = ! empty( $_GET['room'] ) ? sanitize_text_field( $_GET['room'] ) : '';
		// Check-in & out date
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';

		?>
        <form class="tf_booking-widget <?php echo esc_attr( $classes ); ?>" id="tf_hotel_aval_check" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">
            <div class="tf_homepage-booking">
                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php _e( 'Location', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="fas fa-search"></i>
                                    <input type="text" name="place-name" required="" id="tf-location" class="" placeholder="<?php _e( 'Enter Location', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="place" id="tf-search-hotel" class="tf-place-input">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_selectperson-wrap">
                    <div class="tf_input-inner">
                        <span class="tf_person-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <div class="adults-text"><?php echo ( ! empty( $adults ) ? $adults : '1' ) . ' ' . __( 'Adults', 'tourfic' ); ?></div>
                        <div class="person-sep"></div>
                        <div class="child-text"><?php echo ( ! empty( $child ) ? $child : '0' ) . ' ' . __( 'Children', 'tourfic' ); ?></div>
                        <div class="person-sep"></div>
                        <div class="room-text"><?php echo ( ! empty( $room ) ? $room : '1' ) . ' ' . __( 'Room', 'tourfic' ); ?></div>
                    </div>

                    <div class="tf_acrselection-wrap">
                        <div class="tf_acrselection-inner">
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e( 'Adults', 'tourfic' ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? $adults : '1'; ?>">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0'; ?>">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e( 'Rooms', 'tourfic' ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="room" id="room" min="1" value="<?php echo ! empty( $room ) ? $room : '1'; ?>">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tf_selectdate-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php _e( 'Check-in & Check-out date', 'tourfic' ); ?></span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="far fa-calendar-alt"></i>
                                    <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                           placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo tfopt( 'date_hotel_search' ) ? 'required' : ''; ?>>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_submit-wrap">
                    <input type="hidden" name="type" value="tf_hotel" class="tf-post-type"/>
                    <button class="tf_button tf-submit btn-styled" type="submit"><?php _e( 'Search', 'tourfic' ); ?></button>
                </div>

            </div>

        </form>

        <script>
            (function ($) {
                $(document).ready(function () {

                    $("#tf_hotel_aval_check #check-in-out-date").flatpickr({
                        enableTime: false,
                        mode: "range",
                        dateFormat: "Y/m/d",
                        minDate: "today",
                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        },
                        defaultDate: <?php echo json_encode( explode( '-', $check_in_out ) ) ?>,
                    });

                });
            })(jQuery);
        </script>
		<?php
	}
}


/**
 * Hotel Advance Search form
 *
 * Horizontal
 *
 * Called in shortcodes
 */
if ( ! function_exists( 'tf_hotel_advanced_search_form_horizontal' ) ) {
	function tf_hotel_advanced_search_form_horizontal( $classes, $title, $subtitle ) {
		if ( isset( $_GET ) ) {
			$_GET = array_map( 'stripslashes_deep', $_GET );
		}
		// location
		$location = ! empty( $_GET['place'] ) ? sanitize_text_field( $_GET['place'] ) : '';
		// Adults
		$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
		// children
		$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
		// room
		$room = ! empty( $_GET['room'] ) ? sanitize_text_field( $_GET['room'] ) : '';
		// Check-in & out date
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';

		?>
        <form class="tf_booking-widget <?php echo esc_attr( $classes ); ?>" id="tf_hotel_aval_check" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">
            <div class="tf_homepage-booking">
                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php _e( 'Location', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="fas fa-search"></i>
                                    <input type="text" name="place-name" required id="tf-destination-adv" class="tf-advance-destination tf-preview-destination" placeholder="<?php _e( 'Enter Location', 'tourfic' ); ?>">
									<input type="hidden" name="place" id="tf-place-destination" placeholder="<?php _e( 'Enter Location', 'tourfic' ); ?>" >
                                    <div class="tf-hotel-locations tf-hotel-results">
										<ul id="ui-id-1">
										<?php 
										$tf_hotel_location = get_terms( array(
											'taxonomy' => 'hotel_location',
											'orderby' => 'title',
											'order' => 'ASC',
											'hierarchical' => 0,
										) );
										if ( !empty($tf_hotel_location) ) {
										foreach( $tf_hotel_location as $term ) {
										if( !empty($term->name) ){
										?>
										<li data-name="<?php echo $term->name; ?>" data-slug="<?php echo $term->slug; ?>"><i class="fa fa-map-marker"></i><?php echo $term->name; ?></li>
										<?php }}} ?>
										</ul>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_selectperson-wrap">
                    <div class="tf_input-inner">
                        <span class="tf_person-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <div class="adults-text"><?php echo ( ! empty( $adults ) ? $adults : '1' ) . ' ' . __( 'Adults', 'tourfic' ); ?></div>
                        <div class="person-sep"></div>
                        <div class="child-text"><?php echo ( ! empty( $child ) ? $child : '0' ) . ' ' . __( 'Children', 'tourfic' ); ?></div>
                        <div class="person-sep"></div>
                        <div class="room-text"><?php echo ( ! empty( $room ) ? $room : '1' ) . ' ' . __( 'Room', 'tourfic' ); ?></div>
                    </div>

                    <div class="tf_acrselection-wrap">
                        <div class="tf_acrselection-inner">
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e( 'Adults', 'tourfic' ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? $adults : '1'; ?>" readonly>
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec child-dec">-</div>
                                    <input type="number" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0'; ?>" readonly>
                                    <div class="acr-inc child-inc">+</div>
                                </div>
                            </div>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e( 'Rooms', 'tourfic' ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="room" id="room" min="1" value="<?php echo ! empty( $room ) ? $room : '1'; ?>" readonly>
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                        </div>
                        <!-- Children age input field based on children number -->
						<?php

						$children_age        = tfopt( 'children_age_limit' );
						$children_age_status = tfopt( 'enable_child_age_limit' );
						if ( ! empty( $children_age_status ) && $children_age_status == "1" ) {
							?>
                            <div class="tf-children-age-fields">
                                <div class="tf-children-age" id="tf-age-field-0" style="display:none">
                                    <label for="children-age"><?php __( 'Children 0 Age:', 'tourfic' ) ?></label>
                                    <select>
										<?php for ( $age = 0; $age <= $children_age; $age ++ ) {
											?>
                                            <option value="<?php echo esc_attr( $age ); ?>"><?php echo esc_attr( $age ); ?></option>
										<?php } ?>
                                    </select>
                                </div>
                            </div>
						<?php } ?>
                    </div>
                </div>

                <div class="tf_selectdate-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php _e( 'Check-in & Check-out date', 'tourfic' ); ?></span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="far fa-calendar-alt"></i>
                                    <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                           placeholder="<?php _e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo tfopt( 'date_hotel_search' ) ? 'required' : ''; ?>>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_selectdate-wrap tf_more_info_selections">
                    <div class="tf_input-inner">
                        <label class="tf_label-row" style="width: 100%;">
                            <span class="tf-label"><?php _e( 'More', 'tourfic' ); ?></span>
                            <span style="text-decoration: none; display: block; cursor: pointer;"><?php _e( 'Filter', 'tourfic' ); ?>  <i class="fas fa-angle-down"></i></span>
                        </label>
                    </div>
                    <div class="tf-more-info">
                        <span><?php _e( 'Filter Price', 'tourfic' ); ?></span>
                        <div class="tf-filter-price-range">
                            <div class="tf-hotel-filter-range"></div>
                        </div>

                        <span><?php _e( 'Hotel Features', 'tourfic' ); ?></span>
						<?php
						$tf_hotelfeature = get_terms( array(
							'taxonomy'     => 'hotel_feature',
							'orderby'      => 'title',
							'order'        => 'ASC',
							'hide_empty'   => true,
							'hierarchical' => 0,
						) );
						if ( $tf_hotelfeature ) { ?>
							<?php foreach ( $tf_hotelfeature as $term ) { ?>
                                <div class="form-group form-check">
                                    <input type="checkbox" name="features[]" class="form-check-input" value="<?php _e( $term->slug ); ?>" id="<?php _e( $term->slug ); ?>">
                                    <label class="form-check-label" for="<?php _e( $term->slug ); ?>"><?php _e( $term->name ); ?></label>
                                </div>
							<?php }
						} ?>
                    </div>
                </div>

                <div class="tf_submit-wrap">
                    <input type="hidden" name="type" value="tf_hotel" class="tf-post-type"/>
                    <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
                </div>

            </div>

        </form>

        <script>
            (function ($) {
                $(document).ready(function () {

                    $("#tf_hotel_aval_check #check-in-out-date").flatpickr({
                        enableTime: false,
                        mode: "range",
                        dateFormat: "Y/m/d",
                        minDate: "today",
                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        },
                        defaultDate: <?php echo json_encode( explode( '-', $check_in_out ) ) ?>,
                    });

                });
            })(jQuery);
        </script>
		<?php
	}
}

/**
 * Single Hotel Sidebar Booking Form
 */
function tf_hotel_sidebar_booking_form( $b_check_in = '', $b_check_out = '' ) {

	if ( isset( $_GET ) ) {
		$_GET = array_map( 'stripslashes_deep', $_GET );
	}

	//get children ages
	$children_ages = isset( $_GET['children_ages'] ) ? $_GET['children_ages'] : '';
	// Adults
	$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
	// children
	$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
	// Check-in & out date
	$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';
	//get features
	$features = ! empty( $_GET['features'] ) ? sanitize_text_field( $_GET['features'] ) : '';


	/**
	 * Get each hotel room's disabled date from the available dates
	 * @since 2.9.7
	 * @author Abu Hena
	 */
	$meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
	// Room Details
	$rooms           = ! empty( $meta['room'] ) ? $meta['room'] : '';
	if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
		$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $rooms );
		$rooms                = unserialize( $tf_hotel_rooms_value );
	}

	$total_dis_dates = [];
	$maxadults = [];
	$maxchilds = [];
	if ( ! empty( $rooms ) ):
		foreach ( $rooms as $key => $room ) {
			if ( ! empty( $room['repeat_by_date'] ) ) {
				$disabled_dates = $room['repeat_by_date'];
				//iterate all the available disabled dates
				if ( ! empty( $disabled_dates ) ) {
					foreach ( $disabled_dates as $date ) {
						$dateArr           = explode( ',', !empty($date['disabled_date']) ? $date['disabled_date'] : '' );
						$dateArr           = sprintf( '"%s"', implode( '","', $dateArr ) );
						$total_dis_dates[] = $dateArr;
					}
				}
			}
			// Adult Number Store
			if(!empty($room['adult'])){
				$maxadults[]=$room['adult'];
			}

			// Child Number Store
			if(!empty($room['child'])){
				$maxchilds[]=$room['child'];
			}
			
		}
		//merge the new arrays
		array_merge( $total_dis_dates, $total_dis_dates );
		$total_dis_dates = implode( ',', $total_dis_dates );
	endif;
	$total_dis_dates = is_array( $total_dis_dates ) && empty( $total_dis_dates ) ? '' : $total_dis_dates;

	// Maximum Adults Number
	if(!empty($maxadults)){
        $max_adults_numbers = max($maxadults);
    }else{
        $max_adults_numbers = 1;
    }

	// Maximum Child Number
	if(!empty($maxchilds)){
        $max_childs_numbers = max($maxchilds);
    }else{
        $max_childs_numbers = 0;
    }

	?>
	<?php 
	// Single Template Style
	$tf_hotel_single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
	$tf_hotel_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-hotel'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-hotel'] : 'design-1';
	$tf_hotel_selected_template = !empty($tf_hotel_single_template) ? $tf_hotel_single_template : $tf_hotel_global_template;

	if( $tf_hotel_selected_template == "design-1" ){
	?>
	<form id="tf-single-hotel-avail" class="widget tf-hotel-booking-sidebar tf-booking-form" method="get" autocomplete="off">

		<?php wp_nonce_field( 'check_room_avail_nonce', 'tf_room_avail_nonce' ); ?>
		<div class="tf-booking-person">
			<div class="tf-field-group tf_acrselection">
				<div class="tf-field tf-flex">
					<div class="acr-label tf-flex">
						<i class="fa-regular fa-user"></i>
						<?php _e('Adults', 'tourfic'); ?>
					</div>
					<div class="acr-select">
						<div class="acr-dec">-</div>
							<input type="number" name="adults" id="adults" min="1" value="<?php echo !empty($adults) ? $adults : '1'; ?>">
						<div class="acr-inc">+</div>
					</div>
				</div>
			</div>
			<div class="tf-field-group tf_acrselection">
				<div class="tf-field tf-flex">
					<div class="acr-label tf-flex">
						<i class="fa-solid fa-child"></i>
						<?php _e('Children', 'tourfic'); ?>
					</div>
					<div class="acr-select">
						<div class="acr-dec">-</div>
							<input type="number" name="children" id="children" min="0" value="<?php echo !empty($child) ? $child : '0'; ?>">
						<div class="acr-inc">+</div>
					</div>
				</div>
			</div>
		</div>

		<div class="tf_booking-dates">
			<div class="tf_form-row">
				<label class="tf_label-row">
					<div class="tf_form-inner tf-field-group">
						<i class="far fa-calendar-alt"></i>
						<input type="text" name="check-in-out-date" id="check-in-out-date" class="tf-field" onkeypress="return false;"
							placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . $check_in_out . '"' : '' ?> required style="width: 100% !important;">
					</div>
				</label>
			</div>
		</div>

		<div class="tf_form-row">
			<?php
			$ptype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();
			?>
			<input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type"/>
			<input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>"/>
			<input type="hidden" name="children_ages" value="<?php echo $children_ages; ?>"/>

			<div class="tf-btn">
				<button class="tf-bttn-normal bttn-primary tf-submit"
						type="submit"><?php esc_html_e( 'Booking Availability', 'tourfic' ); ?></button>
			</div>


		</div>

	</form>
	<?php }else{ ?>
    <!-- Start Booking widget -->
    <form id="tf-single-hotel-avail" class="tf_booking-widget widget tf-hotel-side-booking tf-hotel-booking-sidebar" method="get" autocomplete="off">

		<?php wp_nonce_field( 'check_room_avail_nonce', 'tf_room_avail_nonce' ); ?>

        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-user-friends"></i>
                    <select name="adults" id="adults" class="">
						<?php
						echo '<option value="1">1 ' . __( "Adult", "tourfic" ) . '</option>';
						if($max_adults_numbers > 1){
							foreach ( range( 2, $max_adults_numbers ) as $value ) {
								$selected = $value == $adults ? 'selected' : null;
								echo '<option ' . $selected . ' value="' . $value . '">' . $value . ' ' . __( "Adults", "tourfic" ) . '</option>';
							}
						}
						?>

                    </select>
                </div>
            </label>
        </div>

        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-child"></i>
                    <select name="children" id="children" class="">
						<?php
						echo '<option value="0">0 ' . __( "Children", "tourfic" ) . '</option>';
						if($max_childs_numbers > 0){
							foreach ( range( 1, $max_childs_numbers ) as $value ) {
								$selected = $value == $child ? 'selected' : null;
								echo '<option ' . $selected . ' value="' . $value . '">' . $value . ' ' . __( "Children", "tourfic" ) . '</option>';
							}
						}
						?>
                    </select>
                </div>
            </label>
        </div>

        <div class="tf_booking-dates">
            <div class="tf_form-row">
                <label class="tf_label-row">
                    <span class="tf-label"><?php _e( 'Check-in & Check-out date', 'tourfic' ); ?></span>
                    <div class="tf_form-inner">
                        <i class="far fa-calendar-alt"></i>
                        <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                               placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . $check_in_out . '"' : '' ?> required>
                    </div>
                </label>
            </div>
        </div>

        <div class="tf_form-row">
			<?php
			$ptype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();
			?>
            <input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type"/>
            <input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>"/>
            <input type="hidden" name="children_ages" value="<?php echo $children_ages; ?>"/>

            <div class="tf-btn">
                <button class="tf_button tf-submit btn-styled"
                        type="submit"><?php esc_html_e( 'Booking Availability', 'tourfic' ); ?></button>
            </div>


        </div>

    </form>
	<?php } ?>
    <script>
        (function ($) {
            $(document).ready(function () {
                const checkinoutdateange = flatpickr(".tf-hotel-booking-sidebar #check-in-out-date", {
                    enableTime: false,
                    mode: "range",
                    minDate: "today",
                    dateFormat: "Y/m/d",
                    onReady: function (selectedDates, dateStr, instance) {
                        instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                    },
                    onChange: function (selectedDates, dateStr, instance) {
                        instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                    },
                    defaultDate: <?php echo json_encode( explode( '-', $check_in_out ) ) ?>,
					<?php if( function_exists('is_tf_pro') && is_tf_pro()) : ?>
						disable: [<?php echo $total_dis_dates; ?>],
					<?php endif; ?>
					<?php
					// Flatpickr locale for translation
					tf_flatpickr_locale();
					?>
                });

            });
        })(jQuery);
    </script>

	<?php if ( is_active_sidebar( 'tf_single_booking_sidebar' ) ) { ?>
        <div id="tf__booking_sidebar">
			<?php dynamic_sidebar( 'tf_single_booking_sidebar' ); ?>
            <br>
        </div>
	<?php }
}

#################################
# Layouts                       #
#################################

/**
 * Hotel Archive Single Item Layout
 */
function tf_hotel_archive_single_item( $adults = '', $child = '', $room = '', $check_in_out = '', $startprice = '', $endprice = '' ) {

	// get post id
	$post_id = get_the_ID();
	//Get hotel_feature
	$features = ! empty( get_the_terms( $post_id, 'hotel_feature' ) ) ? get_the_terms( $post_id, 'hotel_feature' ) : '';

	$meta = get_post_meta( $post_id, 'tf_hotels_opt', true );
	// Location
	$address = ! empty( $meta['address'] ) ? $meta['address'] : '';
	$map     = ! empty( $meta['map'] ) ? $meta['map'] : '';
	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $map ) && gettype( $map ) == "string" ) {
		$tf_hotel_map_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $map );
		$map                = unserialize( $tf_hotel_map_value );
        $address = !empty($map["address"]) ? $map["address"] : $address;
	}
	// Rooms
	$b_rooms = ! empty( $meta['room'] ) ? $meta['room'] : array();
	if( !empty($b_rooms) && gettype($b_rooms)=="string" ){
        $tf_hotel_b_rooms_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $b_rooms );
        $b_rooms = unserialize( $tf_hotel_b_rooms_value );
    }
	// Featured
	$featured  = !empty($meta['featured']) ? $meta['featured'] : '';
	/**
	 * All values from URL
	 */
	// Adults
	if ( empty( $adults ) ) {
		$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
	}
	// children
	if ( empty( $child ) ) {
		$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
	}

	/**
	 * get children ages
	 * @since 2.8.6
	 */
	$children_ages_array = isset( $_GET['children_ages'] ) ? rest_sanitize_array( $_GET['children_ages'] ) : '';
	if ( is_array( $children_ages_array ) && ! empty( $children_ages_array ) ) {
		$children_ages = implode( ',', $children_ages_array );
	} else {
		$children_ages = '';
	}
	// room
	if ( empty( $room ) ) {
		$room = ! empty( $_GET['room'] ) ? sanitize_text_field( $_GET['room'] ) : '';
	}
	// Check-in & out date
	if ( empty( $check_in_out ) ) {
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';
	}
	if ( $check_in_out ) {
		$form_check_in      = substr( $check_in_out, 0, 10 );
		$form_check_in_stt  = strtotime( $form_check_in );
		$form_check_out     = substr( $check_in_out, 14, 10 );
		$form_check_out_stt = strtotime( $form_check_out );
	}

	if( !empty( $check_in_out ) ){
		list( $tf_form_start, $tf_form_end ) = explode( ' - ', $check_in_out );
	}

	if ( ! empty( $check_in_out ) ) {
		$period = new DatePeriod(
			new DateTime( $tf_form_start ),
			new DateInterval( 'P1D' ),
			new DateTime( !empty($tf_form_end) ? $tf_form_end : $tf_form_start . '23:59' )
		);
	} else {
		$period = '';
	}

	// Single link
	$url = get_the_permalink();
	$url = add_query_arg( array(
		'adults'            => $adults,
		'children'          => $child,
		'room'              => $room,
		'children_ages'     => $children_ages
	), $url );

	/**
	 * Calculate and get the minimum price
	 * @author - Hena
	 */
	$room_price = [];
	if ( ! empty( $b_rooms ) ):
		foreach ( $b_rooms as $b_room ) {
			//room price

			$pricing_by = ! empty( $b_room['pricing-by'] ) ? $b_room['pricing-by'] : 1;
			if ( $pricing_by == 1 ) {
				if(empty($check_in_out)){
					if(! empty( $b_room['price'] )){
						$room_price[] = $b_room['price'];
					}
				}else{
					if( !empty($b_room['avil_by_date']) && $b_room['avil_by_date']=="1"){
						if( !empty($b_room['repeat_by_date'])){
							foreach ( $b_room['repeat_by_date'] as $repval ) {
								//Initial matching date array
								$show_hotel = [];
								$dates = $repval['availability'];
								// Check if any date range match with search form date range and set them on array
								if ( ! empty( $period ) ) {
									foreach ( $period as $date ) {
										$show_hotel[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
									}
								}
								if ( ! in_array( 0, $show_hotel ) ) {
									if(! empty( $repval['price'] )){
										$room_price[] = $repval['price'];
									}
								}
							}
						}
					}
				}
			} else if ( $pricing_by == 2 ) {
				if(empty($check_in_out)){
					if(! empty( $b_room['adult_price'] )){
						$room_price[] = $b_room['adult_price'];
					}
					if(! empty( $b_room['child_price'] )){
						$room_price[] = $b_room['child_price'];
					}
				}else{
					if( !empty($b_room['avil_by_date']) && $b_room['avil_by_date']=="1"){
						if( !empty($b_room['repeat_by_date'])){
							foreach ( $b_room['repeat_by_date'] as $repval ) {
								//Initial matching date array
								$show_hotel = [];
								$dates = $repval['availability'];
								// Check if any date range match with search form date range and set them on array
								if ( ! empty( $period ) ) {
									foreach ( $period as $date ) {
										$show_hotel[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
									}
								}
								if ( ! in_array( 0, $show_hotel ) ) {
									if(! empty( $repval['adult_price'] )){
										$room_price[] = $repval['adult_price'];
									}
									if(! empty( $repval['child_price'] )){
										$room_price[] = $repval['child_price'];
									}
								}
							}
						}
					}
				}
			}
		}
	endif;
	if( ! empty( tf_data_types(tfopt( 'tf-template' ))['hotel-archive'] ) && tf_data_types(tfopt( 'tf-template' ))['hotel-archive']=="design-1"){
	?>
	<div class="tf-item-card tf-flex tf-item-hotel">
        <div class="tf-item-featured">
			<a href="<?php echo esc_url($url); ?>">
            <?php
                if (has_post_thumbnail()) {
                    the_post_thumbnail( 'full' );
                } else {
                    echo '<img src="' .TF_ASSETS_APP_URL . "images/feature-default.jpg". '" class="attachment-full size-full wp-post-image">';
                }
            ?>
			</a>
            <div class="tf-features-box tf-flex">
                <?php if( $featured ): ?>
                    <div class="tf-feature">
						<?php 
						echo !empty( $meta['featured_text'] ) ? $meta['featured_text'] : esc_html( "HOT DEAL" );
						?>
					</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="tf-item-details">
            <?php 
            if(!empty($address)){
            ?>
            <div class="tf-title-meta tf-flex tf-flex-align-center tf-flex-gap-8">
                <i class="fa-solid fa-location-dot"></i>
                <p><?php echo $address; ?></p>
            </div>
            <?php } ?>
            <div class="tf-title tf-mrtop-16">
				<h2><a href="<?php echo esc_url($url); ?>"><?php the_title();?></a></h2>
            </div>
            <?php tf_archive_single_rating();?>
            <?php if ( $features ) { ?>
				<div class="tf-arachive-features tf-mrtop-16">
					<ul>
						<?php foreach ( $features as $tfkey=>$feature ) {
							$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
							if ( ! empty( $feature_meta ) ) {
								$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
							}
							if ( ! empty( $f_icon_type ) && $f_icon_type == 'fa' ) {
								$feature_icon = ! empty( $feature_meta['icon-fa'] ) ? '<i class="' . $feature_meta['icon-fa'] . '"></i>' : '';
							} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'c' ) {
								$feature_icon = ! empty( $feature_meta['icon-c'] ) ? '<img src="' . $feature_meta['icon-c'] . '" style="min-width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />' : '';
							}
							if( $tfkey < 6 ){
							?>
							<li class="tf-feature-lists">
								<?php
								if ( ! empty( $feature_icon ) ) {
									echo $feature_icon;
								} ?>
								<?php echo $feature->name; ?>
							</li>
						<?php }} ?>
					</ul>
				</div>
			<?php } ?>
            
            <div class="tf-details tf-mrtop-16">
                <p><?php echo substr(wp_strip_all_tags(get_the_content()), 0, 160). '...'; ?></p>
            </div>
            <div class="tf-post-footer tf-flex tf-flex-align-center tf-flex-space-bttn tf-mrtop-16">
                <div class="tf-pricing">

                <?php
                if ( ! empty( $room_price ) ):
                    $lowest_price = wc_price( min( $room_price ) );
                    echo __( "From ", "tourfic" ) . $lowest_price;
					echo __(" (Per Night)","tourfic"); 
                endif; ?>

                </div>
                <div class="tf-booking-bttns">
                    <a class="tf-bttn-normal bttn-secondary" href="<?php echo esc_url($url); ?>"><?php _e("View Details","tourfic"); ?></a>
                </div>
            </div>
        </div>
    </div>
	<?php } else{ ?>
    <div class="single-tour-wrap <?php echo $featured ? esc_attr('tf-featured') : ''  ?>">
        <div class="single-tour-inner">
			<?php if( $featured ): ?>
				<div class="tf-featured-badge">
					<span><?php _e( 'Featured','tourfic' ) ?></span>
				</div>
			<?php endif; ?>
            <div class="tourfic-single-left">
                <a href="<?php echo esc_url( $url ); ?>">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'full' );
					} else {
						echo '<img width="100%" height="100%" src="' . TF_ASSETS_APP_URL . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
					}
					?>
                </a>
            </div>
            <div class="tourfic-single-right">
                <div class="tf_property_block_main_row">
                    <div class="tf_item_main_block">
                        <div class="tf-hotel__title-wrap">
                            <a href="<?php echo $url; ?>"><h3 class="tourfic_hotel-title"><?php the_title(); ?></h3></a>
                        </div>
						<?php
						if ( $address ) {
							echo '<div class="tf-map-link">';
							echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' . $address . '</span>';
							echo '</div>';
						}
						?>
                    </div>
					<?php tf_archive_single_rating(); ?>
                </div>

                <div class="sr_rooms_table_block">
                    <div class="room_details">
                        <div class="featuredRooms">
                            <div class="prco-ltr-right-align-helper">
                                <div class="tf-archive-shortdesc">
									<p><?php echo substr( wp_strip_all_tags( get_the_content() ), 0, 160 ) . '...'; ?></p>
                                </div>
                            </div>
                            <div class="roomNameInner">
                                <div class="room_link">
                                    <div class="roomrow_flex">
										<?php if ( $features ) { ?>
                                            <div class="roomName_flex">
                                                <ul class="tf-archive-desc">
													<?php foreach ( $features as $feature ) {
														$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
														if ( ! empty( $feature_meta ) ) {
															$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
														}
														if ( ! empty( $f_icon_type ) && $f_icon_type == 'fa' ) {
															$feature_icon = ! empty( $feature_meta['icon-fa'] ) ? '<i class="' . $feature_meta['icon-fa'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
														} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'c' ) {
															$feature_icon = ! empty( $feature_meta['icon-c'] ) ? '<img src="' . $feature_meta['icon-c'] . '" style="min-width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
														} else {
															$feature_icon = '<i class="fas fa-bread-slice"></i>';
														}
														?>
                                                        <li class="tf-tooltip">
															<?php
															if ( ! empty( $feature_icon ) ) {
																echo $feature_icon;
															} ?>
                                                            <div class="tf-top">
																<?php echo $feature->name; ?>
                                                                <i class="tool-i"></i>
                                                            </div>
                                                        </li>
													<?php } ?>
                                                </ul>
                                            </div>
										<?php } ?>
                                        <div class="roomPrice roomPrice_flex sr_discount" style="<?php echo empty( $features ) ? 'text-align:left' : ''; ?>">
                                            <div class="availability-btn-area">
                                                <a href="<?php echo $url; ?>" class="tf_button btn-styled"><?php esc_html_e( 'View Details', 'tourfic' ); ?></a>
                                            </div>
                                            <!-- Show minimum price @author - Hena -->
                                            <div class="tf-room-price-area">
												<?php

												if ( ! empty( $room_price ) ):
													?>
                                                    <div class="tf-room-price">
														<?php
														//get the lowest price from all available room price
														$lowest_price = wc_price( min( $room_price ) );
														echo __( "From ", "tourfic" ) . $lowest_price;

														?>
                                                    </div>
												<?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php
	}
}

/**
 * Filter hotels on search result page by checkin checkout dates set by backend
 *
 *
 * @param DatePeriod $period collection of dates by user input;
 * @param array $not_found collection of hotels exists
 * @param array $data user input for sidebar form
 *
 * @author devkabir, fida
 *
 */
function tf_filter_hotel_by_date( $period, array &$not_found, array $data = [] ): void {

	// Form Data
	if ( isset( $data[4] ) && isset( $data[5] ) ) {
		[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
	} else {
		[ $adults, $child, $room, $check_in_out ] = $data;
	}

	// Get hotel meta options
	$meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );

	// Get hotel Room meta options
	$filters_rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
	if ( ! empty( $filters_rooms ) && gettype( $filters_rooms ) == "string" ) {
		$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $filters_rooms );
		$filters_rooms                = unserialize( $tf_hotel_rooms_value );
	}

	// Remove disabled rooms
	if ( ! empty( $meta['room'] ) ):
		$meta = array_filter( $meta['room'], function ( $value ) {
			return ! empty( $value ) && !empty($value['enable']) ? $value['enable'] : '' != '0';
		} );
	endif;
	// If no room return
	if ( empty( $meta ) ) {
		return;
	}

	// Set initial room availability status
	$has_hotel = false;

	/**
	 * Adult Number Validation
	 */

	$back_adults   = array_column( $meta, 'adult' );
	$adult_counter = 0;
	foreach ( $back_adults as $back_adult ) {
		if ( !empty($back_adult) && $back_adult >= $adults ) {
			$adult_counter ++;
		}
	}

	$adult_result = array_filter($back_adults);

	/**
	 * Child Number Validation
	 */
	$back_childs   = array_column( $meta, 'child' );
	$child_counter = 0;
	foreach ( $back_childs as $back_child ) {
		if ( !empty($back_child) && $back_child >= $child ) {
			$child_counter ++;
		}
	}
	
	$childs_result = array_filter($back_childs);

	/**
	 * Room Number Validation
	 */
	$back_rooms   = array_column( $meta, 'num-room' );
	$room_counter = 0;
	foreach ( $back_rooms as $back_room ) {
		if ( !empty($back_room) && $back_room >= $room ) {
			$room_counter ++;
		}
	}

	$room_result = array_filter($back_rooms);

	// If adult and child number validation is true proceed
	if ( !empty($adult_result) && $adult_counter > 0 && !empty($childs_result) && $child_counter > 0 && !empty($room_result) && $room_counter > 0 ) {

		// Check custom date range status of room
		$avil_by_date = array_column( $meta, 'avil_by_date' );

		// Check if any room available without custom date range
		if ( in_array( 0, $avil_by_date ) || empty( $avil_by_date ) ) {

			$has_hotel = true; // Show that hotel

		} else {
			// If all the room has custom date range then filter the rooms by date

			// Get custom date range repeater
			$dates = array_column( $meta, 'repeat_by_date' );

			// If no date range return
			if ( empty( $dates ) ) {
				return;
			}

			// Initial available dates array
			$availability_dates = [];

			// Run loop through custom date range repeater and filter out only the dates
			foreach ( $dates as $date ) {
				$availability_dates[] = array_column( $date, 'availability' );
			}

			// Run loop through custom dates & set custom dates on a single array
			foreach ( tf_array_flatten( $availability_dates, 1 ) as $dates ) {

				//Initial matching date array
				$show_hotel = [];

				// Check if any date range match with search form date range and set them on array
				if ( ! empty( $period ) ) {
					foreach ( $period as $date ) {
						$show_hotel[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );

					}
				}

				// If any date range matches show hotel
				if ( ! in_array( 0, $show_hotel ) ) {
					if(!empty($filters_rooms) && !empty($startprice) && !empty($endprice)){
						foreach($filters_rooms as $room){
							if(!empty($room['repeat_by_date'])){
								foreach($room['repeat_by_date'] as $repat_date){
									if(!empty($repat_date['adult_price'])){
										if($startprice<=$repat_date['adult_price'] && $repat_date['adult_price']<=$endprice){
											$has_hotel = true;
										}
									}
									if(!empty($repat_date['child_price'])){
										if($startprice<=$repat_date['child_price'] && $repat_date['child_price']<=$endprice){
											$has_hotel = true;
										}
									}
									if(!empty($repat_date['price'])){
										if($startprice<=$repat_date['price'] && $repat_date['price']<=$endprice){
											$has_hotel = true;
										}
									}
								}
							}
						}
					}else{
						$has_hotel = true;
					}
					break;
				}

			}

		}

	}

	// If adult and child number validation is true proceed
	if ( !empty($adult_result) && $adult_counter > 0 && empty($childs_result) && $child_counter == 0 && !empty($room_result) && $room_counter > 0 ) {

		// Check custom date range status of room
		$avil_by_date = array_column( $meta, 'avil_by_date' );

		// Check if any room available without custom date range
		if ( in_array( 0, $avil_by_date ) || empty( $avil_by_date ) ) {

			$has_hotel = true; // Show that hotel

		} else {
			// If all the room has custom date range then filter the rooms by date

			// Get custom date range repeater
			$dates = array_column( $meta, 'repeat_by_date' );

			// If no date range return
			if ( empty( $dates ) ) {
				return;
			}

			// Initial available dates array
			$availability_dates = [];

			// Run loop through custom date range repeater and filter out only the dates
			foreach ( $dates as $date ) {
				$availability_dates[] = array_column( $date, 'availability' );
			}

			// Run loop through custom dates & set custom dates on a single array
			foreach ( tf_array_flatten( $availability_dates, 1 ) as $dates ) {

				//Initial matching date array
				$show_hotel = [];

				// Check if any date range match with search form date range and set them on array
				if ( ! empty( $period ) ) {
					foreach ( $period as $date ) {
						$show_hotel[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );

					}
				}

				// If any date range matches show hotel
				if ( ! in_array( 0, $show_hotel ) ) {
					if(!empty($filters_rooms) && !empty($startprice) && !empty($endprice)){
						foreach($filters_rooms as $room){
							if(!empty($room['repeat_by_date'])){
								foreach($room['repeat_by_date'] as $repat_date){
									if(!empty($repat_date['adult_price'])){
										if($startprice<=$repat_date['adult_price'] && $repat_date['adult_price']<=$endprice){
											$has_hotel = true;
										}
									}
									if(!empty($repat_date['child_price'])){
										if($startprice<=$repat_date['child_price'] && $repat_date['child_price']<=$endprice){
											$has_hotel = true;
										}
									}
									if(!empty($repat_date['price'])){
										if($startprice<=$repat_date['price'] && $repat_date['price']<=$endprice){
											$has_hotel = true;
										}
									}
								}
							}
						}
					}else{
						$has_hotel = true;
					}
					break;
				}

			}

		}

	}

	// Conditional hotel showing
	if ( $has_hotel ) {

		$not_found[] = array(
            'post_id' => get_the_ID(),
            'found'  => 0,
        );

	} else {

		$not_found[] = array(
            'post_id' => get_the_ID(),
            'found'  => 1,
        );
	}

}

/**
 * Filter hotels on search result page without checkin checkout dates
 *
 *
 * @param DatePeriod $period collection of dates by user input;
 * @param array $not_found collection of hotels exists
 * @param array $data user input for sidebar form
 *
 * @author jahid
 *
 */
function tf_filter_hotel_without_date( $period, array &$not_found, array $data = [] ): void {

	// Form Data
	if ( isset( $data[4] ) && isset( $data[5] ) ) {
		[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
	} else {
		[ $adults, $child, $room, $check_in_out ] = $data;
	}

	// Get hotel meta options
	$meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
	
	// Get hotel Room meta options
	$filters_rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
	if ( ! empty( $filters_rooms ) && gettype( $filters_rooms ) == "string" ) {
		$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $filters_rooms );
		$filters_rooms                = unserialize( $tf_hotel_rooms_value );
	}

	// Remove disabled rooms
	if ( ! empty( $meta['room'] ) ):
		$meta = array_filter( $meta['room'], function ( $value ) {
			return ! empty( $value ) && !empty($value['enable']) ? $value['enable'] : '' != '0';
		} );
	endif;
	// If no room return
	if ( empty( $meta ) ) {
		return;
	}

	// Set initial room availability status
	$has_hotel = false;

	/**
	 * Adult Number Validation
	 */
	$back_adults   = array_column( $meta, 'adult' );
	$adult_counter = 0;
	foreach ( $back_adults as $back_adult ) {
		if ( !empty($back_adult) && $back_adult >= $adults ) {
			$adult_counter ++;
		}
	}

	$adult_result = array_filter($back_adults);

	/**
	 * Child Number Validation
	 */
	$back_childs   = array_column( $meta, 'child' );
	$child_counter = 0;
	foreach ( $back_childs as $back_child ) {
		if ( !empty($back_child) && $back_child >= $child ) {
			$child_counter ++;
		}
	}
	
	$childs_result = array_filter($back_childs);

	/**
	 * Room Number Validation
	 */
	$back_rooms   = array_column( $meta, 'num-room' );
	$room_counter = 0;
	foreach ( $back_rooms as $back_room ) {
		if ( !empty($back_room) && $back_room >= $room ) {
			$room_counter ++;
		}
	}

	$room_result = array_filter($back_rooms);

	// If adult and child number validation is true proceed
	if ( !empty($adult_result) && $adult_counter > 0 && !empty($childs_result) && $child_counter > 0 && !empty($room_result) && $room_counter > 0 ) {

		if(!empty($filters_rooms) && !empty($startprice) && !empty($endprice)){
			foreach($filters_rooms as $room){
				if(!empty($room['adult_price'])){
					if($startprice<=$room['adult_price'] && $room['adult_price']<=$endprice){
						$has_hotel = true;
					}
				}
				if(!empty($room['child_price'])){
					if($startprice<=$room['child_price'] && $room['child_price']<=$endprice){
						$has_hotel = true;
					}
				}
				if(!empty($room['price'])){
					if($startprice<=$room['price'] && $room['price']<=$endprice){
						$has_hotel = true;
					}
				}
			}
		}else{
			$has_hotel = true; // Show that hotel
		}

	}
	if ( !empty($adult_result) && $adult_counter > 0 && empty($childs_result) && !empty($room_result) && $room_counter > 0 ) {
		if(!empty($filters_rooms) && !empty($startprice) && !empty($endprice)){
			foreach($filters_rooms as $room){
				if(!empty($room['adult_price'])){
					if($startprice<=$room['adult_price'] && $room['adult_price']<=$endprice){
						$has_hotel = true;
					}
				}
				if(!empty($room['child_price'])){
					if($startprice<=$room['child_price'] && $room['child_price']<=$endprice){
						$has_hotel = true;
					}
				}
				if(!empty($room['price'])){
					if($startprice<=$room['price'] && $room['price']<=$endprice){
						$has_hotel = true;
					}
				}
			}
		}else{
			$has_hotel = true; // Show that hotel
		}
	}

	// Conditional hotel showing
	if ( $has_hotel ) {

		$not_found[] = array(
            'post_id' => get_the_ID(),
            'found'  => 0,
        );

	} else {

		$not_found[] = array(
            'post_id' => get_the_ID(),
            'found'  => 1,
        );
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

/**
 * Ajax remove room order ids
 */
add_action( 'wp_ajax_tf_remove_room_order_ids', 'tf_remove_room_order_ids' );
function tf_remove_room_order_ids() {

	# Get order id field's name
	$meta_field = isset( $_POST['meta_field'] ) ? sanitize_text_field( $_POST['meta_field'] ) : '';
	# Trim room id from order id name
	$room_id = trim( $meta_field, "tf_hotels_opt[room][][order_id" );
	# Get post id
	$post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
	# Get hotel meta
	$meta = get_post_meta( $post_id, 'tf_hotels_opt', true );

	$order_id_retrive = tf_data_types( $meta['room'] );

	# Set order id field's value to blank
	$order_id_retrive[ $room_id ]['order_id'] = '';

	$meta['room'] = serialize( $order_id_retrive );
	# Update whole hotel meta
	update_post_meta( $post_id, 'tf_hotels_opt', $meta );

	# Send success message
	wp_send_json_success( __( 'Order ids have been removed!', 'tourfic' ) );

	wp_die();
}

/**
 * Ajax hotel quick view
 */

function tf_hotel_quickview_callback() {
	?>
    <div class="tf-hotel-quick-view" style="display: flex">
		<?php
		$meta  = get_post_meta( $_POST['post_id'], 'tf_hotels_opt', true );

		// Single Template Style
		$tf_hotel_single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
		$tf_hotel_global_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-hotel'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-hotel'] : 'design-1';
		$tf_hotel_selected_template = !empty($tf_hotel_single_template) ? $tf_hotel_single_template : $tf_hotel_global_template;

		$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
		if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
			$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $rooms );
			$rooms                = unserialize( $tf_hotel_rooms_value );
		}
		foreach ( $rooms as $key => $room ) :
			$enable = ! empty( $room['enable'] ) ? $room['enable'] : '';
			if ( $enable == '1' && $room['unique_id'] . $key == $_POST['uniqid_id'] ) :
				$tf_room_gallery = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
				?>
                <div class="tf-hotel-details-qc-gallelry" style="width: 545px;">
					<?php
					if ( $tf_room_gallery ) {
						$tf_room_gallery_ids = explode( ',', $tf_room_gallery );
					}

					?>

                    <div class="tf-details-qc-slider tf-details-qc-slider-single">
						<?php
						if ( ! empty( $tf_room_gallery_ids ) ) {
							foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
								?>
                                <div class="tf-details-qcs">
									<?php
									$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
									echo '<img src="' . $image_url . '" alt="">';
									?>
                                </div>
							<?php }
						} ?>
                    </div>
                    <div class="tf-details-qc-slider tf-details-qc-slider-nav">
						<?php
						if ( ! empty( $tf_room_gallery_ids ) ) {
							foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
								?>
                                <div class="tf-details-qcs">
									<?php
									$image_url = wp_get_attachment_url( $gallery_item_id, 'thumbnail' );
									echo '<img src="' . $image_url . '" alt="">';
									?>
                                </div>
							<?php }
						} ?>
                    </div>

                    <script>
                        jQuery('.tf-details-qc-slider-single').slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: <?php echo $tf_hotel_selected_template == "design-1" ? "false" : "true" ?>,
                            fade: false,
                            adaptiveHeight: true,
                            infinite: true,
                            useTransform: true,
                            speed: 400,
                            cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)',
                        });

                        jQuery('.tf-details-qc-slider-nav')
                            .on('init', function (event, slick) {
                                jQuery('.tf-details-qc-slider-nav .slick-slide.slick-current').addClass('is-active');
                            })
                            .slick({
                                slidesToShow: 7,
                                slidesToScroll: 7,
                                dots: false,
                                focusOnSelect: false,
                                infinite: false,
                                responsive: [{
                                    breakpoint: 1024,
                                    settings: {
                                        slidesToShow: 5,
                                        slidesToScroll: 5,
                                    }
                                }, {
                                    breakpoint: 640,
                                    settings: {
                                        slidesToShow: 4,
                                        slidesToScroll: 4,
                                    }
                                }, {
                                    breakpoint: 420,
                                    settings: {
                                        slidesToShow: 3,
                                        slidesToScroll: 3,
                                    }
                                }]
                            });

                        jQuery('.tf-details-qc-slider-single').on('afterChange', function (event, slick, currentSlide) {
                            jQuery('.tf-details-qc-slider-nav').slick('slickGoTo', currentSlide);
                            var currrentNavSlideElem = '.tf-details-qc-slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
                            jQuery('.tf-details-qc-slider-nav .slick-slide.is-active').removeClass('is-active');
                            jQuery(currrentNavSlideElem).addClass('is-active');
                        });

                        jQuery('.tf-details-qc-slider-nav').on('click', '.slick-slide', function (event) {
                            event.preventDefault();
                            var goToSingleSlide = jQuery(this).data('slick-index');

                            jQuery('.tf-details-qc-slider-single').slick('slickGoTo', goToSingleSlide);
                        });
                    </script>

                </div>
                <div class="tf-hotel-details-info" style="width:440px; padding-left: 35px;max-height: 470px;padding-top: 25px; overflow-y: scroll;">
					<?php
					$footage      = ! empty( $room['footage'] ) ? $room['footage'] : '';
					$bed          = ! empty( $room['bed'] ) ? $room['bed'] : '';
					$adult_number = ! empty( $room['adult'] ) ? $room['adult'] : '0';
					$child_number = ! empty( $room['child'] ) ? $room['child'] : '0';
					$num_room     = ! empty( $room['num-room'] ) ? $room['num-room'] : '0';

					if( $tf_hotel_selected_template == "design-1" ){
					?>
					<h3><?php echo esc_html( $room['title'] ); ?></h3>
					<div class="tf-template-1 tf-room-adv-info">
						<ul>
						<?php if ( $num_room ) { ?>
							<li><i class="fas fa-person-booth"></i> <?php echo $num_room; ?> <?php _e( 'Rooms', 'tourfic' ); ?></li>
						<?php }
						if ( $footage ) { ?>
							<li><i class="fas fa-ruler-combined"></i> <?php echo $footage; ?> <?php _e( 'Sft', 'tourfic' ); ?></li>
						<?php }
						if ( $bed ) { ?>
							<li><i class="fas fa-bed"></i> <?php echo $bed; ?> <?php _e( ' Beds', 'tourfic' ); ?></li>
						<?php } ?>
						<?php if ( $adult_number ) { ?>
							<li><i class="fas fa-male"></i> <?php echo $adult_number; ?> <?php _e( 'Adults', 'tourfic' ); ?></li>
						<?php }
						if ( $child_number ) { ?>
							<li>
							<i class="fas fa-baby"></i> <?php echo $child_number; ?> <?php _e( 'Children', 'tourfic' ); ?>
							</li>
						<?php } ?>
						</ul>
						
                    	<p><?php echo $room['description']; ?></p>
					</div>
					<?php if(!empty($room['features'])){ ?>
					
					<div class="tf-template-1 tf-room-adv-info">
						<h4><?php _e("Amenities", "tourfic"); ?></h4>
						<ul>
						<?php foreach ( $room['features'] as $feature ) {
						$room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
						if ( ! empty( $room_f_meta ) ) {
							$room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
						}
						if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' ) {
							$room_feature_icon = ! empty( $room_f_meta['icon-fa'] ) ? '<i class="' . $room_f_meta['icon-fa'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
						} elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' ) {
							$room_feature_icon = ! empty( $room_f_meta['icon-c'] ) ? '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
						} else {
							$room_feature_icon = '<i class="fas fa-bread-slice"></i>';
						}

						$room_term = get_term( $feature ); ?>
						<li>
							<?php echo ! empty( $room_feature_icon ) ? $room_feature_icon : ''; ?>
							<?php echo $room_term->name; ?>
						</li>
						<?php } ?>
						</ul>
					</div>
					<?php } ?>
					<?php } else{ ?>
                    <h3><?php echo esc_html( $room['title'] ); ?></h3>
                    <p><?php echo $room['description']; ?></p>
                    <div class="tf-room-title description">
						<?php if ( $num_room ) { ?>
                            <div class="tf-tooltip tf-d-ib">
                                <div class="room-detail-icon">
                                    <span class="room-icon-wrap"><i class="fas fa-person-booth"></i></span>
                                    <span class="icon-text tf-d-b"><?php echo $num_room; ?></span>
                                </div>
                                <div class="tf-top">
									<?php _e( 'Number of Room', 'tourfic' ); ?>
                                    <i class="tool-i"></i>
                                </div>
                            </div>
						<?php }
						if ( $footage ) { ?>
                            <div class="tf-tooltip tf-d-ib">
                                <div class="room-detail-icon">
                                    <span class="room-icon-wrap"><i class="fas fa-ruler-combined"></i></span>
                                    <span class="icon-text tf-d-b"><?php echo $footage; ?><?php _e( 'sft', 'tourfic' ); ?></span>
                                </div>
                                <div class="tf-top">
									<?php _e( 'Room Footage', 'tourfic' ); ?>
                                    <i class="tool-i"></i>
                                </div>
                            </div>
						<?php }
						if ( $bed ) { ?>
                            <div class="tf-tooltip tf-d-ib">
                                <div class="room-detail-icon">
                                    <span class="room-icon-wrap"><i class="fas fa-bed"></i></span>
                                    <span class="icon-text tf-d-b">x<?php echo $bed; ?></span>
                                </div>
                                <div class="tf-top">
									<?php _e( 'Number of Beds', 'tourfic' ); ?>
                                    <i class="tool-i"></i>
                                </div>
                            </div>
						<?php } ?>

						<?php if ( ! empty( $room['features'] ) ) { ?>
                            <div class="room-features">
                                <div class="tf-room-title"><h4><?php esc_html_e( 'Amenities', 'tourfic' ); ?></h4></div>
                                <ul class="room-feature-list" style="margin: 0;">

									<?php foreach ( $room['features'] as $feature ) {

										$room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
										if ( ! empty( $room_f_meta ) ) {
											$room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
										}
										if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' ) {
											$room_feature_icon = ! empty( $room_f_meta['icon-fa'] ) ? '<i class="' . $room_f_meta['icon-fa'] . '"></i>' : '<i class="fas fa-bread-slice"></i>';
										} elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' ) {
											$room_feature_icon = ! empty( $room_f_meta['icon-c'] ) ? '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />' : '<i class="fas fa-bread-slice"></i>';
										} else {
											$room_feature_icon = '<i class="fas fa-bread-slice"></i>';
										}

										$room_term = get_term( $feature ); ?>
                                        <li class="tf-tooltip tf-d-ib">
											<?php echo ! empty( $room_feature_icon ) ? $room_feature_icon : ''; ?>
                                            <div class="tf-top">
												<?php echo $room_term->name; ?>
                                                <i class="tool-i"></i>
                                            </div>
                                        </li>
									<?php } ?>
                                </ul>
                            </div>
						<?php } ?>
                    </div>
                    <div class="pax">

                        <div class="tf-room-title"><h4><?php esc_html_e( 'Pax', 'tourfic' ); ?></h4>
							<?php if ( $adult_number ) { ?>
                                <div class="tf-tooltip tf-d-ib">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-male"></i><i class="fas fa-female"></i></span>
                                        <span class="icon-text tf-d-b">x<?php echo $adult_number; ?></span>
                                    </div>
                                    <div class="tf-top">
										<?php _e( 'Number of Adults', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>

							<?php }
							if ( $child_number ) { ?>
                                <div class="tf-tooltip tf-d-ib">
                                    <div class="room-detail-icon">
                                        <span class="room-icon-wrap"><i class="fas fa-baby"></i></span>
                                        <span class="icon-text tf-d-b">x<?php echo $child_number; ?></span>
                                    </div>
                                    <div class="tf-top">
										<?php _e( 'Number of Children', 'tourfic' ); ?>
                                        <i class="tool-i"></i>
                                    </div>
                                </div>
							<?php } ?>
                        </div>

                    </div>
					<?php } ?>
                </div>
			<?php
			endif;
		endforeach;
		?>
    </div>
	<?php
	wp_die();
}

add_action( 'wp_ajax_tf_tour_details_qv', 'tf_hotel_quickview_callback' );
add_action( 'wp_ajax_nopriv_tf_tour_details_qv', 'tf_hotel_quickview_callback' );

#################################
# WooCommerce integration       #
#################################
/**
 * WooCommerce hotel Functions
 *
 * @include
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-hotel.php' ) ) {
	require_once TF_INC_PATH . 'functions/woocommerce/wc-hotel.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-hotel.php' );
}

#################################
#           Temporary           #
#################################
/**
 * Add missing unique id to hotel room
 */
function tf_update_missing_room_id() {

	if ( get_option( 'tf_miss_room_id' ) < 1 ) {

		$args        = array(
			'posts_per_page'   => - 1,
			'post_type'        => 'tf_hotel',
			'suppress_filters' => true
		);
		$posts_array = get_posts( $args );
		foreach ( $posts_array as $post_array ) {
			$meta  = get_post_meta( $post_array->ID, 'tf_hotel', true );
			$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
			if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
				$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $rooms );
				$rooms                = unserialize( $tf_hotel_rooms_value );
			}
			$new_rooms = [];
			foreach ( $rooms as $room ) {

				if ( empty( $room['unique_id'] ) ) {
					$room['unique_id'] = mt_rand( 1, time() );
				}
				$new_rooms[] = $room;
			}
			$meta['room'] = $new_rooms;
			update_post_meta( $post_array->ID, 'tf_hotel', $meta );

		}
		update_option( 'tf_miss_room_id', 1 );
	}
}

add_action( 'init', 'tf_update_missing_room_id' );

/**
 * Run Once
 * Add _price post_meta to all hotels & tours
 *
 * Will be delete in future version
 */
function tf_update_meta_all_hotels_tours() {

	// Run once only
	if ( get_option( 'tf_update_hotel_price' ) < 1 ) {

		// Update hotels meta
		$args        = array(
			'posts_per_page'   => - 1,
			'post_type'        => 'tf_hotel',
			'suppress_filters' => true
		);
		$posts_array = get_posts( $args );
		foreach ( $posts_array as $post_array ) {
			update_post_meta( $post_array->ID, '_price', '0' );
		}

		// Update tours meta
		$args        = array(
			'posts_per_page'   => - 1,
			'post_type'        => 'tf_tours',
			'suppress_filters' => true
		);
		$posts_array = get_posts( $args );
		foreach ( $posts_array as $post_array ) {
			update_post_meta( $post_array->ID, '_price', '0' );
		}

		update_option( 'tf_update_hotel_price', 1 );

	}
}

add_action( 'wp_loaded', 'tf_update_meta_all_hotels_tours' );

/*
 * Hotel total room, adult, child
 * @return int or array
 * @since 2.8.7
 * @author Mehedi Foysal
 */
if ( ! function_exists( 'tf_hotel_total_room_adult_child' ) ) {
	function tf_hotel_total_room_adult_child( $hotel_id, $type = 'room' ) {
		$meta  = get_post_meta( $hotel_id, 'tf_hotel', true );
		$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
		if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
			$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $rooms );
			$rooms                = unserialize( $tf_hotel_rooms_value );
		}
		$total_room   = 0;
		$total_adults = 0;
		$total_child  = 0;
		foreach ( $rooms as $room ) {
			$enable = ! empty( $room['enable'] ) ? $room['enable'] : '';
			if ( $enable ) {
				$total_room   += ! empty( $room['num-room'] ) ? $room['num-room'] : 0;
				$total_adults += ! empty( $room['adult'] ) ? $room['adult'] : 0;
				$total_child  += ! empty( $room['child'] ) ? $room['child'] : 0;
			}
		}

		if ( $type == 'room' ) {
			return $total_room;
		} elseif ( $type == 'adult' ) {
			return $total_adults;
		} elseif ( $type == 'child' ) {
			return $total_child;
		} else {
			return array(
				'room'  => $total_room,
				'adult' => $total_adults,
				'child' => $total_child,
			);
		}
	}
}


/*
 * Hotel room matched by date
 * @return boolean
 * @since 2.8.7
 * @author Mehedi Foysal
 */
if ( ! function_exists( 'tf_hotel_room_matched_by_date' ) ) {
	function tf_hotel_room_matched_by_date( $rooms, $check_in, $check_out ) {
		$room_date_match = array();
		if ( ! empty( $check_in ) && ! empty( $check_out ) ) {
			foreach ( $rooms as $_room ) {
				$enable = ! empty( $_room['enable'] ) ? $_room['enable'] : '';
				if ( $enable == '1' ) {
					$avil_by_date = ! empty( $_room['avil_by_date'] ) ? $_room['avil_by_date'] : '';
					if ( $avil_by_date == '1' && ! empty( $_room['repeat_by_date'] ) ) {
						foreach ( $_room['repeat_by_date'] as $date ) {
							$from = ! empty( $date['availability']['from'] ) ? strtotime( $date['availability']['from'] ) : '';
							$to   = ! empty( $date['availability']['to'] ) ? strtotime( $date['availability']['to'] ) : '';
							if ( $from <= $check_in && $to >= $check_out ) {
								$room_date_match[] = 'matched';
							} else {
								$room_date_match[] = 'not matched';
							}
						}
					} else {
						$room_date_match[] = 'matched';
					}
				}
			}
		} else {
			$room_date_match[] = 'matched';
		}

		return in_array( 'matched', $room_date_match );
	}
}

/*
 * Hotel search ajax
 * @since 2.9.7
 * @author Foysal
 */
add_action( 'wp_ajax_tf_hotel_search', 'tf_hotel_search_ajax_callback' );
add_action( 'wp_ajax_nopriv_tf_hotel_search', 'tf_hotel_search_ajax_callback' );
if ( ! function_exists( 'tf_hotel_search_ajax_callback' ) ) {
	function tf_hotel_search_ajax_callback() {
		$response = [
			'status'  => 'error',
			'message' => '',
		];

		if ( ! isset( $_POST['place'] ) || empty( $_POST['place'] ) ) {
			$response['message'] = esc_html__( 'Please enter your location', 'tourfic' );
		} elseif ( tfopt( 'date_hotel_search' ) && ( ! isset( $_POST['check-in-out-date'] ) || empty( $_POST['check-in-out-date'] ) ) ) {
			$response['message'] = esc_html__( 'Please select check in and check out date', 'tourfic' );
		}

        if(tfopt( 'date_hotel_search' )){
	        if ( ! empty( $_POST['place'] ) && ! empty( $_POST['check-in-out-date'] ) ) {
		        $response['query_string'] = str_replace( '&action=tf_hotel_search', '', http_build_query( $_POST ) );
		        $response['status']       = 'success';
	        }
        } else {
            if ( ! empty( $_POST['place'] ) ) {
                $response['query_string'] = str_replace( '&action=tf_hotel_search', '', http_build_query( $_POST ) );
                $response['status']       = 'success';
            }
        }

		echo json_encode( $response );
		wp_die();
	}
}