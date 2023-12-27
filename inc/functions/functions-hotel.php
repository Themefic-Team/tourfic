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
		'show_in_rest'       => true,
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

add_filter( 'use_block_editor_for_post_type', function ( $enabled, $post_type ) {
	return ( 'tf_hotel' === $post_type ) ? false : $enabled;
}, 10, 2 );

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
 * hotel_location, hotel_feature, hotel_type
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

	/**
	 * Taxonomy: hotel_feature.
	 */
	$hotel_feature_slug   = apply_filters( 'hotel_feature_slug', 'hotel-feature' );
	$hotel_feature_labels = [
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

	$hotel_feature_args = [
		"labels"                => $hotel_feature_labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => $hotel_feature_slug, 'with_front' => true ],
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

	/**
	 * Taxonomy: hotel_type.
	 */
	$hotel_type_slug   = apply_filters( 'hotel_type_slug', 'hotel-type' );
	$hotel_type_labels = [
		"name"                       => __( "Types", 'tourfic' ),
		"singular_name"              => __( "Type", 'tourfic' ),
		"menu_name"                  => __( "Types", 'tourfic' ),
		"all_items"                  => __( "All Types", 'tourfic' ),
		"edit_item"                  => __( "Edit Type", 'tourfic' ),
		"view_item"                  => __( "View Type", 'tourfic' ),
		"update_item"                => __( "Update Type", 'tourfic' ),
		"add_new_item"               => __( "Add new Type", 'tourfic' ),
		"new_item_name"              => __( "New Type name", 'tourfic' ),
		"parent_item"                => __( "Parent Type", 'tourfic' ),
		"parent_item_colon"          => __( "Parent Type:", 'tourfic' ),
		"search_items"               => __( "Search Type", 'tourfic' ),
		"popular_items"              => __( "Popular Types", 'tourfic' ),
		"separate_items_with_commas" => __( "Separate Types with commas", 'tourfic' ),
		"add_or_remove_items"        => __( "Add or remove Types", 'tourfic' ),
		"choose_from_most_used"      => __( "Choose from the most used Types", 'tourfic' ),
		"not_found"                  => __( "No Types found", 'tourfic' ),
		"no_terms"                   => __( "No Types", 'tourfic' ),
		"items_list_navigation"      => __( "Types list navigation", 'tourfic' ),
		"items_list"                 => __( "Types list", 'tourfic' ),
		"back_to_items"              => __( "Back to Types", 'tourfic' ),
	];

	$hotel_type_args = [
		"labels"                => $hotel_type_labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => $hotel_type_slug, 'with_front' => true ],
		"show_admin_column"     => true,
		"show_in_rest"          => true,
		"rest_base"             => "hotel_type",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => true,
		'capabilities'          => array(
			'assign_terms' => 'edit_tf_hotel',
			'edit_terms'   => 'edit_tf_hotel',
		),
	];

	register_taxonomy( 'hotel_location', 'tf_hotel', apply_filters( 'hotel_location_args', $hotel_location_args ) );
	register_taxonomy( 'hotel_feature', 'tf_hotel', apply_filters( 'tf_hotel_feature_args', $hotel_feature_args ) );
	register_taxonomy( 'hotel_type', 'tf_hotel', apply_filters( 'tf_hotel_type_args', $hotel_type_args ) );

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
			$avail_date = ! empty( $rooms[ $room_id ]['avail_date'] ) ? json_decode( $rooms[ $room_id ]['avail_date'], true ) : [];
		}

		$pricing_by      = $rooms[ $room_id ]['pricing-by'];
		$price_multi_day = ! empty( $rooms[ $room_id ]['price_multi_day'] ) ? $rooms[ $room_id ]['price_multi_day'] : false;

		// Hotel Discout Data
		$hotel_discount_type   = ! empty( $rooms[ $room_id ]["discount_hotel_type"] ) ? $rooms[ $room_id ]["discount_hotel_type"] : "none";
		$hotel_discount_amount = ! empty( $rooms[ $room_id ]["discount_hotel_price"] ) ? $rooms[ $room_id ]["discount_hotel_price"] : 0;

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

				$available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
					$date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
					$date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

					return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
				} ) );

				if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {
					$room_price  = ! empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $rooms[ $room_id ]['price'];
					$adult_price = ! empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $rooms[ $room_id ]['adult_price'];
					$child_price = ! empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $rooms[ $room_id ]['child_price'];

					if ( $hotel_discount_type == "percent" ) {
						$room_price  = !empty($room_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $room_price - ( ( (int) $room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
						$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
						$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					}
					if ( $hotel_discount_type == "fixed" ) {
						$room_price  = !empty($room_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $room_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
						$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
						$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
					}
					$total_price += $pricing_by == '1' ? $room_price : ( ( $adult_price * $adult ) + ( $child_price * $child ) );
				};

			}

			$price_total = $total_price * $room_selected;
			
		} else {

			if ( $pricing_by == '1' ) {
				$only_room_price = ! empty( $rooms[ $room_id ]['price'] ) ? $rooms[ $room_id ]['price'] : 0;
				if ( $hotel_discount_type == "percent" ) {
					$only_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $only_room_price - ( ( (int) $only_room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
				}
				if ( $hotel_discount_type == "fixed" ) {
					$only_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $only_room_price - (int) $hotel_discount_amount ), 2 ) );
				}
				$total_price = $only_room_price;

			} elseif ( $pricing_by == '2' ) {
				$adult_price = ! empty( $rooms[ $room_id ]['adult_price'] ) ? $rooms[ $room_id ]['adult_price'] : 0;
				$child_price = ! empty( $rooms[ $room_id ]['child_price'] ) ? $rooms[ $room_id ]['child_price'] : 0;

				if ( $hotel_discount_type == "percent" ) {
					$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
				}
				if ( $hotel_discount_type == "fixed" ) {
					$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
					$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
				}

				$adult_price = $adult_price * $adult;
				$child_price = $child_price * $child;
				$total_price = $adult_price + $child_price;

			}

			# Multiply pricing by night number
			if ( ! empty( $day_difference ) && $price_multi_day == true ) {
				$price_total = $total_price * $room_selected * $day_difference;
			} else {
				$price_total = $total_price * ( $room_selected * $day_difference + 1 );
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
    $room_book_by        = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
    $room_book_url       = ! empty( $meta['booking-url'] ) ? $meta['booking-url'] : '';

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
	$tf_hotel_layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
	if("single"==$tf_hotel_layout_conditions){
		$tf_hotel_single_template_check = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
	}
	$tf_hotel_global_template_check = ! empty( tf_data_types(tfopt( 'tf-template' ))['single-hotel'] ) ? tf_data_types(tfopt( 'tf-template' ))['single-hotel'] : 'design-1';

	$tf_hotel_selected_check = !empty($tf_hotel_single_template_check) ? $tf_hotel_single_template_check : $tf_hotel_global_template_check;

	$tf_hotel_selected_template_check = $tf_hotel_selected_check;

	if( $tf_hotel_selected_template_check == "design-1" ){
	?>
	<table class="tf-availability-table" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th class="description" colspan="3"><?php _e( 'Room Details', 'tourfic' ); ?></th>
		</tr>
	</thead>
	<?php } elseif($tf_hotel_selected_template_check == "default"){ ?>
    <table class="availability-table" cellpadding="0" cellspacing="0">
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
				$multi_by_date_ck = ! empty( $room['price_multi_day'] ) ? ! empty( $room['price_multi_day'] ) : false;
				$child_age_limit  = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";
				$room_price       = ! empty( $room['price'] ) ? $room['price'] : 0;
				$room_adult_price = ! empty( $room['adult_price'] ) ? $room['adult_price'] : 0;
				$room_child_price = ! empty( $room['child_price'] ) ? $room['child_price'] : 0;
				$total_person     = $adult_number + $child_number;
				$price 			  = $pricing_by == '1' ? $room_price : $room_adult_price + $room_child_price;
				$form_check_out   = $form_end;

				// Hotel Room Discount Data
				$hotel_discount_type = !empty($room["discount_hotel_type"]) ? $room["discount_hotel_type"] : "none";
				$hotel_discount_amount = !empty($room["discount_hotel_price"]) ? $room["discount_hotel_price"] : 0;
				$d_room_price = 0;
				$d_room_adult_price = 0;
				$d_room_child_price = 0;

				if($pricing_by == 1) {
					if($hotel_discount_type == "percent") {
						$d_room_price = !empty($room_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $room_price - ( ( (int) $room_price / 100 ) * (int) (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					}else if($hotel_discount_type == "fixed") {
						$d_room_price = !empty($room_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $room_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					}
				} else {
					if($hotel_discount_type == "percent") {
						$d_room_adult_price = !empty($room_adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $room_adult_price - ( ( (int) $room_adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
						$d_room_child_price = !empty($room_child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $room_child_price - ( ( (int) $room_child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					}else if($hotel_discount_type == "fixed") {
						$room_adult_price = !empty($room_adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $room_adult_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
						$room_child_price = !empty($room_child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $room_child_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					}
				}

				$d_price = $pricing_by == '1' ? $d_room_price : $d_room_adult_price + $d_room_child_price;

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
				$is_first = true;
				foreach ( $tfperiod as $date ) {
					if($multi_by_date_ck){
						if ($is_first) {
							$is_first = false;
							continue;
						}
					}
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
					$avail_date = ! empty( $room['avail_date'] ) ? json_decode($room['avail_date'], true) : [];
				}

				if ( ! empty( $order_ids ) && $reduce_num_room == true ) {

					# Get backend available date range as an array
					if ( $avil_by_date ) {

						$order_date_ranges = array();

						$backend_date_ranges = array();
						foreach ( $avail_date as $single_date_range ) {

							array_push( $backend_date_ranges, array( strtotime( $single_date_range["availability"]["from"] ), strtotime( $single_date_range["availability"]["to"] ) ) );

						}
					}

					# Convert order ids to array
					$order_ids = explode( ',', $order_ids );

					# Run foreach loop through oder ids
					foreach ( $order_ids as $order_id ) {

					# Get Only the completed orders
					$tf_orders_select = array(
						'select' => "post_id,order_details",
						'post_type' => 'hotel',
						'query' => " AND ostatus = 'completed' AND order_id = ".$order_id
					);
					$tf_hotel_book_orders = tourfic_order_table_data($tf_orders_select);

						# Get and Loop Over Order Items
						foreach ( $tf_hotel_book_orders as $item ) {
							$order_details = json_decode($item['order_details']);
							/**
							 * Order item data
							 */
							$ordered_number_of_room = !empty($order_details->room) ? $order_details->room : 0;

							if ( $avil_by_date ) {

								$order_check_in_date  = strtotime( $order_details->check_in );
								$order_check_out_date = strtotime( $order_details->check_out );

								$tf_order_check_in_date  = $order_details->check_in;
								$tf_order_check_out_date = $order_details->check_out;
								if ( ! empty( $avail_durationdate ) && ( in_array( $tf_order_check_out_date, $avail_durationdate ) || in_array( $tf_order_check_in_date, $avail_durationdate ) ) ) {
									# Total number of room booked
									$number_orders = $number_orders + $ordered_number_of_room;
								}
								array_push( $order_date_ranges, array( $order_check_in_date, $order_check_out_date ) );

							} else {
								$order_check_in_date  = $order_details->check_in;
								$order_check_out_date = $order_details->check_out;
								if ( ! empty( $avail_durationdate ) && ( in_array( $order_check_out_date, $avail_durationdate ) || in_array( $order_check_in_date, $avail_durationdate ) ) ) {
									# Total number of room booked
									$number_orders = $number_orders + $ordered_number_of_room;
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
					$d_price   = 0;
					$has_room  = [];

					// extract price from available room options
					foreach ( $period as $date ) {

                        $available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
                            if( $date_availability['status'] == 'available' ){
                                $date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
                                $date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

							    return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
                            } else {
                                return false;
                            }
						} ) );

						if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {

							$room_price      = ! empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $room_price;
							$adult_price     = ! empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $room_adult_price;
							$child_price     = ! empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $room['child_price'];
							$price_by_date   = $pricing_by == '1' ? $room_price : ( ( $adult_price * $form_adult ) + ( $child_price * $form_child ) );
							$d_price_by_date = 0;
							$price 			+= $price_by_date;
							$number_of_rooms = ! empty( $available_rooms[0]['num-room'] ) ? $available_rooms[0]['num-room'] : $room['num-room'];
							$has_room[]      = 1;

							if($pricing_by == 1) {
								if($hotel_discount_type == "percent") {
									$d_room_price = !empty($room_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $room_price - ( ( (int) $room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
								}else if($hotel_discount_type == "fixed") {
									$d_room_price = !empty($room_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $room_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
								}
							} else {
								if($hotel_discount_type == "percent") {
									$d_adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
									$d_child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
								}else if($hotel_discount_type == "fixed") {
									$d_adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
									$d_child_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
								}
							}
							$d_price_by_date = $pricing_by == '1' ? $d_room_price : ( ( $d_adult_price * $form_adult ) + ( $d_child_price * $form_child ) );
							$d_price += $d_price_by_date;

						} else {
							$has_room[] = 0;
						}
					}
					if(!$multi_by_date_ck){
						$days = $days+1;
					}

					$d_price = $d_price_by_date * $days;
					$price = $price_by_date * $days;

					// Check if date is provided and within date range
					if ( ! in_array( 0, $has_room ) ) {
						tf_get_deposit_amount( $room, $price, $deposit_amount, $has_deposit, $d_price);
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
						if($hotel_discount_type == "percent" || $hotel_discount_type == "fixed") {
							$d_price_by_date = $d_room_price;
						}
						$price_by_date = $room_price;
					} else {
						if($hotel_discount_type == "percent" || $hotel_discount_type == "fixed") {
							$d_price_by_date = ( ( $d_room_adult_price * $form_adult ) + ( $d_room_child_price * $form_child ) );
						}
						$price_by_date = ( ( $room_adult_price * $form_adult ) + ( $room_child_price * $form_child ) );
					}

					if(!$multi_by_date_ck){
						$days = $days+1;
					}

					$price = !empty($room['price_multi_day']) && $room['price_multi_day'] == '1' ? $price_by_date * $days : $price_by_date * $days;
					$d_price = !empty($room['price_multi_day']) && $room['price_multi_day'] == '1' ? $d_price_by_date * $days : $d_price_by_date * $days;

					tf_get_deposit_amount( $room, $price, $deposit_amount, $has_deposit, $d_price );

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
	function tf_hotel_search_form_horizontal( $classes, $title, $subtitle, $author, $advanced, $design ) {
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

		// date format for users output
		$hotel_date_format_for_users = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";

		$disable_hotel_child_search  = ! empty( tfopt( 'disable_hotel_child_search' ) ) ? tfopt( 'disable_hotel_child_search' ) : '';
		if( !empty($design) && 2==$design ){
		?>
		<form class="tf_booking-widget-design-2 tf_hotel-shortcode-design-2" id="tf_hotel_aval_check" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">
			<div class="tf_hotel_searching">
				<div class="tf_form_innerbody">
					<div class="tf_form_fields">
						<div class="tf_destination_fields">
							<label class="tf_label_location">
								<span class="tf-label"><?php _e( 'Location', 'tourfic' ); ?></span>
								<div class="tf_form_inners tf_form-inner">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
									<path d="M8 13.9317L11.2998 10.6318C13.1223 8.80943 13.1223 5.85464 11.2998 4.0322C9.4774 2.20975 6.52261 2.20975 4.70017 4.0322C2.87772 5.85464 2.87772 8.80943 4.70017 10.6318L8 13.9317ZM8 15.8173L3.75736 11.5747C1.41421 9.2315 1.41421 5.43254 3.75736 3.08939C6.10051 0.746245 9.89947 0.746245 12.2427 3.08939C14.5858 5.43254 14.5858 9.2315 12.2427 11.5747L8 15.8173ZM8 8.66536C8.7364 8.66536 9.33333 8.06843 9.33333 7.33203C9.33333 6.59565 8.7364 5.9987 8 5.9987C7.2636 5.9987 6.66667 6.59565 6.66667 7.33203C6.66667 8.06843 7.2636 8.66536 8 8.66536ZM8 9.9987C6.52724 9.9987 5.33333 8.80476 5.33333 7.33203C5.33333 5.85927 6.52724 4.66536 8 4.66536C9.47273 4.66536 10.6667 5.85927 10.6667 7.33203C10.6667 8.80476 9.47273 9.9987 8 9.9987Z" fill="#FAEEDD"/>
									</svg>
									<input type="text" name="place-name" required="" id="tf-location" class="" placeholder="<?php _e( 'Enter Location', 'tourfic' ); ?>" value="">
									<input type="hidden" name="place" id="tf-search-hotel" class="tf-place-input">
								</div>
							</label>
						</div>
						
						<div class="tf_checkin_date">
							<label class="tf_label_checkin tf_check_inout_dates tf_hotel_check_in_out_date">
								<span class="tf-label"><?php _e( 'Check in', 'tourfic' ); ?></span>
								<div class="tf_form_inners">
									<div class="tf_checkin_dates">
										<span class="date"><?php echo date('d'); ?></span>
										<span class="month"><?php echo date('M'); ?></span>
									</div>
									<div class="tf_check_arrow">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
										<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
										</svg>
									</div>
								</div>
							</label>
							
							<input type="hidden" name="check-in-out-date" class="tf-check-in-out-date" onkeypress="return false;" placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo tfopt( 'date_hotel_search' ) ? 'required' : ''; ?>>
						</div>
						
						<div class="tf_checkin_date tf_check_inout_dates tf_hotel_check_in_out_date">
							<label class="tf_label_checkin">
								<span class="tf-label"><?php _e( 'Check Out', 'tourfic' ); ?></span>
								<div class="tf_form_inners">
									<div class="tf_checkout_dates">
										<span class="date"><?php echo date('d'); ?></span>
										<span class="month"><?php echo date('M'); ?></span>
									</div>
									<div class="tf_check_arrow">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
										<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
										</svg>
									</div>
								</div>
							</label>
						</div>

						<div class="tf_guest_info tf_selectperson-wrap">
							<label class="tf_label_checkin tf_input-inner">
								<span class="tf-label"><?php _e( 'Guests & rooms', 'tourfic' ); ?></span>
								<div class="tf_form_inners">
									<div class="tf_guest_calculation">
										<div class="tf_guest_number">
											<span class="guest"><?php _e( '1', 'tourfic' ); ?></span>
											<span class="label"><?php _e( 'Guest', 'tourfic' ); ?></span>
										</div>
										<div class="tf_guest_number">
											<span class="room"><?php _e( '1', 'tourfic' ); ?></span>
											<span class="label"><?php _e( 'Room', 'tourfic' ); ?></span>
										</div>
									</div>
									<div class="tf_check_arrow">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
										<path d="M8 10.668L4 6.66797H12L8 10.668Z" fill="#FDF9F4"/>
										</svg>
									</div>
								</div>
							</label>

							<div class="tf_acrselection-wrap">
								<div class="tf_acrselection-inner">
									<div class="tf_acrselection">
										<div class="acr-label"><?php _e( 'Adults', 'tourfic' ); ?></div>
										<div class="acr-select">
											<div class="acr-dec">-</div>
											<input type="tel" class="adults-style2" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? $adults : '1'; ?>">
											<div class="acr-inc">+</div>
										</div>
									</div>
									<div class="tf_acrselection">
										<div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
										<div class="acr-select">
											<div class="acr-dec child-dec">-</div>
											<input type="tel" name="children" class="childs-style2" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0'; ?>">
											<div class="acr-inc child-inc">+</div>
										</div>
									</div>
									<div class="tf_acrselection">
										<div class="acr-label"><?php _e( 'Rooms', 'tourfic' ); ?></div>
										<div class="acr-select">
											<div class="acr-dec">-</div>
											<input type="tel" name="room" class="rooms-style2" id="room" min="1" value="<?php echo ! empty( $room ) ? $room : '1'; ?>">
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
					</div>
					<div class="tf_availability_checker_box">
						<input type="hidden" name="type" value="tf_hotel" class="tf-post-type"/>
						<?php
						if ( $author ) { ?>
							<input type="hidden" name="tf-author" value="<?php echo $author; ?>" class="tf-post-type"/>
						<?php } ?>
						<button><?php echo _e("Check availability", "tourfic"); ?></button>
					</div>
				</div>
			</div>

		</form>
		<script>
			(function ($) {
				$(document).ready(function () {

						// flatpickr locale first day of Week
						<?php tf_flatpickr_locale("root"); ?>

					$(".tf_check_inout_dates").click(function(){
						$(".tf-check-in-out-date").click();
					});
					$(".tf-check-in-out-date").flatpickr({
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
						defaultDate: <?php echo json_encode( explode( '-', $check_in_out ) ) ?>,
					});

					function dateSetToFields(selectedDates, instance) {
						if (selectedDates.length === 2) {
							const monthNames = [
								"Jan", "Feb", "Mar", "Apr", "May", "Jun",
								"Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
							];
							if(selectedDates[0]){
								const startDate = selectedDates[0];
								$(".tf_hotel_check_in_out_date .tf_checkin_dates span.date").html(startDate.getDate());
								$(".tf_hotel_check_in_out_date .tf_checkin_dates span.month").html(monthNames[startDate.getMonth()]);
							}
							if(selectedDates[1]){
								const endDate = selectedDates[1];
								$(".tf_hotel_check_in_out_date .tf_checkout_dates span.date").html(endDate.getDate());
								$(".tf_hotel_check_in_out_date .tf_checkout_dates span.month").html(monthNames[endDate.getMonth()]);
							}
						}
					}

				});
			})(jQuery);
		</script>
		<?php
		}else{ ?>
        <form class="tf_booking-widget <?php echo esc_attr( $classes ); ?>" id="tf_hotel_aval_check" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">
            <div class="tf_homepage-booking">
                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php _e( 'Location', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="fas fa-search"></i>
									<?php if ( empty($advanced) && "enabled"!=$advanced ){ ?>
                                    <input type="text" name="place-name" required="" id="tf-location" class="" placeholder="<?php _e( 'Enter Location', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="place" id="tf-search-hotel" class="tf-place-input">
									<?php } 
									if ( !empty($advanced) && "enabled"==$advanced ){ ?>
									<input type="text" name="place-name" required id="tf-destination-adv" class="tf-advance-destination tf-preview-destination" placeholder="<?php _e( 'Enter Location', 'tourfic' ); ?>">
                                    <input type="hidden" name="place" id="tf-place-destination" placeholder="<?php _e( 'Enter Location', 'tourfic' ); ?>">
									<div class="tf-hotel-locations tf-hotel-results">
                                        <ul id="ui-id-1">
											<?php
											$tf_hotel_location = get_terms( array(
												'taxonomy'     => 'hotel_location',
												'orderby'      => 'title',
												'order'        => 'ASC',
												'hierarchical' => 0,
											) );
											if ( ! empty( $tf_hotel_location ) ) {
												foreach ( $tf_hotel_location as $term ) {
													if ( ! empty( $term->name ) ) {
														?>
                                                        <li data-name="<?php echo $term->name; ?>" data-slug="<?php echo $term->slug; ?>"><i class="fa fa-map-marker"></i><?php echo $term->name; ?></li>
													<?php }
												}
											} ?>
                                        </ul>
                                    </div>
									<?php } ?>
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
                        <?php if(empty($disable_hotel_child_search)) : ?>
                            <div class="person-sep"></div>
                            <div class="child-text"><?php echo ( ! empty( $child ) ? $child : '0' ) . ' ' . __( 'Children', 'tourfic' ); ?></div>
                        <?php endif; ?>
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
	                        <?php if(empty($disable_hotel_child_search)) : ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0'; ?>">
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e( 'Rooms', 'tourfic' ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="room" id="room" min="1" value="<?php echo ! empty( $room ) ? $room : '1'; ?>">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                        </div>
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
                                           placeholder="<?php esc_attr_e( 'Check-in - Check-out', 'tourfic' ); ?>" <?php echo tfopt( 'date_hotel_search' ) ? 'required' : ''; ?>>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
										
				<?php if ( !empty($advanced) && "enabled"==$advanced ){ ?>
				<div class="tf_selectdate-wrap tf_more_info_selections">
                    <div class="tf_input-inner">
                        <label class="tf_label-row" style="width: 100%;">
                            <span class="tf-label"><?php _e( 'More', 'tourfic' ); ?></span>
                            <span style="text-decoration: none; display: block; cursor: pointer;"><?php _e( 'Filter', 'tourfic' ); ?>  <i class="fas fa-angle-down"></i></span>
                        </label>
                    </div>
                    <div class="tf-more-info">
                        <h3><?php _e( 'Filter Price', 'tourfic' ); ?></h3>
                        <div class="tf-filter-price-range">
                            <div class="tf-hotel-filter-range"></div>
                        </div>

                        <h3 style="margin-top: 20px"><?php _e( 'Hotel Features', 'tourfic' ); ?></h3>
						<?php
						$tf_hotelfeature = get_terms( array(
							'taxonomy'     => 'hotel_feature',
							'orderby'      => 'title',
							'order'        => 'ASC',
							'hide_empty'   => true,
							'hierarchical' => 0,
						) );
						if ( $tf_hotelfeature ) : ?>
                            <div class="tf-hotel-features" style="overflow: hidden">
								<?php foreach ( $tf_hotelfeature as $term ) : ?>
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="features[]" class="form-check-input" value="<?php _e( $term->slug ); ?>" id="<?php _e( $term->slug ); ?>">
                                        <label class="form-check-label" for="<?php _e( $term->slug ); ?>"><?php _e( $term->name ); ?></label>
                                    </div>
								<?php endforeach; ?>
                            </div>
						<?php endif; ?>

                        <h3 style="margin-top: 20px"><?php _e( 'Hotel Types', 'tourfic' ); ?></h3>
						<?php
						$tf_hoteltype = get_terms( array(
							'taxonomy'     => 'hotel_type',
							'orderby'      => 'title',
							'order'        => 'ASC',
							'hide_empty'   => true,
							'hierarchical' => 0,
						) );
						if ( $tf_hoteltype ) : ?>
                            <div class="tf-hotel-types" style="overflow: hidden">
								<?php foreach ( $tf_hoteltype as $term ) : ?>
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="types[]" class="form-check-input" value="<?php _e( $term->slug ); ?>" id="<?php _e( $term->slug ); ?>">
                                        <label class="form-check-label" for="<?php _e( $term->slug ); ?>"><?php _e( $term->name ); ?></label>
                                    </div>
								<?php endforeach; ?>
                            </div>
						<?php endif; ?>
                    </div>
                </div>
				<?php } ?>

                <div class="tf_submit-wrap">
                    <input type="hidden" name="type" value="tf_hotel" class="tf-post-type"/>
					<?php
					if ( $author ) { ?>
                        <input type="hidden" name="tf-author" value="<?php echo $author; ?>" class="tf-post-type"/>
					<?php } ?>
                    <button class="tf_button tf-submit btn-styled" type="submit"><?php _e( 'Search', 'tourfic' ); ?></button>
                </div>

            </div>

        </form>

        <script>
            (function ($) {
                $(document).ready(function () {

					// flatpickr First Day of Week
					<?php tf_flatpickr_locale('root'); ?>

                    $("#tf_hotel_aval_check #check-in-out-date").flatpickr({
                        enableTime: false,
                        mode: "range",
                        dateFormat: "Y/m/d",
                        altInput: true,
                        altFormat: '<?php echo $hotel_date_format_for_users; ?>',
                        minDate: "today",

						// flatpickr locale
						<?php tf_flatpickr_locale(); ?>

                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                        },
                        defaultDate: <?php echo json_encode( explode( '-', $check_in_out ) ) ?>,
                    });

                });
            })(jQuery);
        </script>
		<?php
		}
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

	// date format for users output
	$hotel_date_format_for_users = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";


	/**
	 * Get each hotel room's disabled date from the available dates
	 * @since 2.9.7
	 * @author Abu Hena
	 */
	$meta = get_post_meta( get_the_ID(), 'tf_hotels_opt', true );
	// Room Details
	$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
	if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
		$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $rooms );
		$rooms                = unserialize( $tf_hotel_rooms_value );
	}

	$total_dis_dates = [];
	$maxadults       = [];
	$maxchilds       = [];

	if ( ! empty( $rooms ) ):
		foreach ( $rooms as $key => $room ) {
			if ( ! empty( $room['repeat_by_date'] ) ) {
				$disabled_dates = $room['repeat_by_date'];
				//iterate all the available disabled dates
				if ( ! empty( $disabled_dates ) && is_array( $disabled_dates ) ) {
					foreach ( $disabled_dates as $date ) {
						$dateArr           = explode( ',', !empty($date['disabled_date']) ? $date['disabled_date'] : '' );
						$dateArr           = sprintf( '"%s"', implode( '","', $dateArr ) );
						$total_dis_dates[] = $dateArr;
					}
				}
			}
			
			if ( ! empty( $room['avail_date'] ) ) {
				$avail_dates = json_decode( $room['avail_date'], true );
				if ( ! empty( $avail_dates ) ) {
					foreach ( $avail_dates as $date ) {
						if ( $date['status'] === 'unavailable' ) {
							$total_dis_dates[] = $date['check_in'];
						}
					}
				}
			}
			// Adult Number Store
			if ( ! empty( $room['adult'] ) ) {
				$maxadults[] = $room['adult'];
			}

			// Child Number Store
			if ( ! empty( $room['child'] ) ) {
				$maxchilds[] = $room['child'];
			}

		}
		//merge the new arrays
		array_merge( $total_dis_dates, $total_dis_dates );
		$total_dis_dates = implode( ',', $total_dis_dates );
	endif;
	$total_dis_dates = is_array( $total_dis_dates ) && empty( $total_dis_dates ) ? '' : $total_dis_dates;

	// Maximum Adults Number
	if ( ! empty( $maxadults ) ) {
		$max_adults_numbers = max( $maxadults );
	} else {
		$max_adults_numbers = 1;
	}

	// Maximum Child Number
	if ( ! empty( $maxchilds ) ) {
		$max_childs_numbers = max( $maxchilds );
	} else {
		$max_childs_numbers = 0;
	}

	?>
	<?php
	// Single Template Style
	$tf_hotel_layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
	if ( "single" == $tf_hotel_layout_conditions ) {
		$tf_hotel_single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
	}
	$tf_hotel_global_template = ! empty( tf_data_types( tfopt( 'tf-template' ) )['single-hotel'] ) ? tf_data_types( tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';

	$tf_hotel_selected_check = ! empty( $tf_hotel_single_template ) ? $tf_hotel_single_template : $tf_hotel_global_template;

	$tf_hotel_selected_template = $tf_hotel_selected_check;

	$tf_hotel_book_avaibality_button_text = !empty(tfopt('hotel_booking_check_button_text')) ? stripslashes(sanitize_text_field(tfopt('hotel_booking_check_button_text'))) : "Booking Availability";

	if ( $tf_hotel_selected_template == "design-1" ) {
		?>
        <form id="tf-single-hotel-avail" class="widget tf-hotel-booking-sidebar tf-booking-form" method="get" autocomplete="off">

			<?php wp_nonce_field( 'check_room_avail_nonce', 'tf_room_avail_nonce' ); ?>
            <div class="tf-booking-person">
                <div class="tf-field-group tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <i class="fa-regular fa-user"></i>
							<?php _e( 'Adults', 'tourfic' ); ?>
                        </div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? $adults : '1'; ?>">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>
                <div class="tf-field-group tf_acrselection">
                    <div class="tf-field tf-flex">
                        <div class="acr-label tf-flex">
                            <i class="fa-solid fa-child"></i>
							<?php _e( 'Children', 'tourfic' ); ?>
                        </div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                            <input type="number" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0'; ?>">
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
                    <button class="tf-btn-normal btn-primary tf-submit"
                            type="submit"><?php esc_html_e( $tf_hotel_book_avaibality_button_text, 'tourfic' ); ?></button>
                </div>


            </div>

        </form>
		<?php if ( is_active_sidebar( 'tf_single_booking_sidebar' ) ) { ?>
        <div id="tf__booking_sidebar">
			<?php dynamic_sidebar( 'tf_single_booking_sidebar' ); ?>
            <br>
        </div>
		<?php } ?>
	<?php }
	elseif ( $tf_hotel_selected_template == "design-2" ) { ?>

	<form id="tf-single-hotel-avail" class="tf-booking-form" method="get" autocomplete="off">
	<?php wp_nonce_field( 'check_room_avail_nonce', 'tf_room_avail_nonce' ); ?>
		<div class="tf-booking-form-fields">
			<div class="tf-booking-form-checkin">
				<span class="tf-booking-form-title"><?php _e("Check in", "tourfic"); ?></span>
				<div class="tf-booking-date-wrap">
					<span class="tf-booking-date"><?php _e("00", "tourfic"); ?></span>
					<span class="tf-booking-month">
						<span>
							<?php echo date('M'); ?>
						</span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
				</div>
			</div>
			<div class="tf-booking-form-checkout">
				<span class="tf-booking-form-title"><?php _e("Check out", "tourfic"); ?></span>
				<div class="tf-booking-date-wrap">
					<span class="tf-booking-date"><?php _e("00", "tourfic"); ?></span>
					<span class="tf-booking-month">
						<span><?php echo date('M'); ?></span>
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
						</svg>
					</span>
				</div>
				<input type="text" name="check-in-out-date" class="tf-check-in-out-date" onkeypress="return false;" placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" <?php echo ! empty( $check_in_out ) ? 'value="' . $check_in_out . '"' : '' ?> required>

			</div>
			<div class="tf-booking-form-guest-and-room">
				<div class="tf-booking-form-guest-and-room-inner">
					<span class="tf-booking-form-title"><?php _e("Guests", "tourfic"); ?></span>
					<div class="tf-booking-guest-and-room-wrap">
						<span class="tf-guest tf-booking-date"><?php _e("01", "tourfic"); ?></span> 
						<span class="tf-booking-month">
							<span><?php _e("Guest", "tourfic"); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
							<path d="M8 11.1641L4 7.16406H12L8 11.1641Z" fill="#595349"/>
							</svg>
						</span>
					</div>
				</div>

				
				<div class="tf_acrselection-wrap">
					<div class="tf_acrselection-inner">
						<div class="tf_acrselection">
							<div class="acr-label"><?php _e("Adults", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">-</div>
								<input type="tel" name="adults" id="adults" min="1" value="<?php echo ! empty( $adults ) ? $adults : '1'; ?>">
								<div class="acr-inc">+</div>
							</div>
						</div>
						<div class="tf_acrselection">
							<div class="acr-label"><?php _e("Children", "tourfic"); ?></div>
							<div class="acr-select">
								<div class="acr-dec">-</div>
								<input type="tel" name="children" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0'; ?>">
								<div class="acr-inc">+</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tf-booking-form-submit">
			<?php
			$ptype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();
			?>
			<input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type"/>
			<input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>"/>
			<input type="hidden" name="children_ages" value="<?php echo $children_ages; ?>"/>
			<button type="submit" class="btn-primary tf-submit"><?php esc_html_e($tf_hotel_book_avaibality_button_text, "tourfic"); ?></button>
		</div>
	</form>

	<script>
		(function ($) {
			$(document).ready(function () {

				// flatpickr locale first day of Week
				<?php tf_flatpickr_locale("root"); ?>
				
				$(".tf-template-3 .tf-booking-date-wrap").click(function(){
					$(".tf-check-in-out-date").click();
				});
				$(".tf-check-in-out-date").flatpickr({
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
					defaultDate: <?php echo json_encode( explode( '-', $check_in_out ) ) ?>,
				});

				function dateSetToFields(selectedDates, instance) {
					if (selectedDates.length === 2) {
						const monthNames = [
							"Jan", "Feb", "Mar", "Apr", "May", "Jun",
							"Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
						];
						if(selectedDates[0]){
							const startDate = selectedDates[0];
							$(".tf-template-3 .tf-booking-form-checkin span.tf-booking-date").html(startDate.getDate());
							$(".tf-template-3 .tf-booking-form-checkin span.tf-booking-month span").html(monthNames[startDate.getMonth()+1]);
						}
						if(selectedDates[1]){
							const endDate = selectedDates[1];
							$(".tf-template-3 .tf-booking-form-checkout span.tf-booking-date").html(endDate.getDate());
							$(".tf-template-3 .tf-booking-form-checkout span.tf-booking-month span").html(monthNames[endDate.getMonth()+1]);
						}
					}
				}

			});
		})(jQuery);
	</script>

	<?php } else { ?>
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
							if ( $max_adults_numbers > 1 ) {
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
							if ( $max_childs_numbers > 0 ) {
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
                            type="submit"><?php esc_html_e( $tf_hotel_book_avaibality_button_text, 'tourfic' ); ?></button>
                </div>


            </div>

        </form>
		<?php if ( is_active_sidebar( 'tf_single_booking_sidebar' ) ) { ?>
        <div id="tf__booking_sidebar">
			<?php dynamic_sidebar( 'tf_single_booking_sidebar' ); ?>
            <br>
        </div>
		<?php } ?>
	<?php } ?>
    <script>
        (function ($) {
            $(document).ready(function () {

				// First Day of Week
				   <?php tf_flatpickr_locale("root"); ?>

                const checkinoutdateange = flatpickr(".tf-hotel-booking-sidebar #check-in-out-date", {
                    enableTime: false,
                    mode: "range",
                    minDate: "today",
                    altInput: true,
                    altFormat: '<?php echo $hotel_date_format_for_users; ?>',
                    dateFormat: "Y/m/d",
                    onReady: function (selectedDates, dateStr, instance) {
                        instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                    },
                    onChange: function (selectedDates, dateStr, instance) {
                        instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                        instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                    },
                    defaultDate: <?php echo json_encode( explode( '-', $check_in_out ) ) ?>,
					<?php
					// Flatpickr locale for translation
					tf_flatpickr_locale();
					?>
                });

            });
        })(jQuery);
    </script>

<?php
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
	if( !empty($meta['map']) && tf_data_types($meta['map'])){
		$address = !empty( tf_data_types($meta['map'])['address'] ) ? tf_data_types($meta['map'])['address'] : '';
    }
	// Rooms
	$b_rooms = ! empty( $meta['room'] ) ? $meta['room'] : array();
	if( !empty($b_rooms) && gettype($b_rooms)=="string" ){
        $tf_hotel_b_rooms_value = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $b_rooms );
        $b_rooms = unserialize( $tf_hotel_b_rooms_value );
    }
	// Gallery Image
	$gallery = ! empty( $meta['gallery'] ) ? $meta['gallery'] : '';
	if ( $gallery ) {
		$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
	}

	// Archive Page Minimum Price
	$archive_page_price_settings = ! empty( tfopt( 'hotel_archive_price_minimum_settings' ) ) ? tfopt( 'hotel_archive_price_minimum_settings' ) : 'all';

	// Featured
	$featured = ! empty( $meta['featured'] ) ? $meta['featured'] : '';
	$hotel_multiple_tags = !empty($meta['tf-hotel-tags']) ? $meta['tf-hotel-tags'] : array();
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

	// Single link
	$url = get_the_permalink();
	$url = add_query_arg( array(
		'adults'            => $adults,
		'children'          => $child,
		'room'              => $room,
		'children_ages'     => $children_ages,
		'check-in-out-date' => $check_in_out
	), $url );

	/**
	 * Calculate and get the minimum price
	 * @author - Hena
	 */
	$room_price = [];
	$tf_lowestAmount = 0;
	$tf_lowestAmount_items = null;
	if ( ! empty( $b_rooms ) ):
		foreach ( $b_rooms as $rkey => $b_room ) {

			//hotel room discount data
			$hotel_discount_type = !empty($b_room["discount_hotel_type"]) ? $b_room["discount_hotel_type"] : "none";
			$hotel_discount_amount = !empty($b_room["discount_hotel_price"]) ? $b_room["discount_hotel_price"] : 0;
			if($hotel_discount_type!="none" && !empty($hotel_discount_amount)){
				$tf_lowestAmount_items['amount'] = $hotel_discount_amount;
				$tf_lowestAmount_items['type'] = $hotel_discount_type;

				$tf_lowestAmount = intval($hotel_discount_amount); // Convert the amount to an integer for comparison
				if ($hotel_discount_amount < $tf_lowestAmount) {
					$tf_lowestAmount = $hotel_discount_amount;
					$tf_lowestAmount_items['amount'] = $hotel_discount_amount;
					$tf_lowestAmount_items['type'] = $hotel_discount_type;
				}
			}


			//room price
			$pricing_by = ! empty( $b_room['pricing-by'] ) ? $b_room['pricing-by'] : 1;
			if ( $pricing_by == 1 ) {
				if ( empty( $check_in_out ) ) {
					if ( ! empty( $b_room['price'] ) ) {
						$b_room_price = $b_room['price'];

						$dicount_b_room_price = 0;

						if ( $hotel_discount_type == "percent" ) {
							$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $b_room_price - ( ( (int) $b_room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
						} else if ( $hotel_discount_type == "fixed" ) {
							$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $b_room_price - (int) $hotel_discount_amount ), 2 ) ) );
						}
						if($dicount_b_room_price != 0) {
							$room_price[] = array(
								"regular_price" => $b_room['price'],
								"sale_price"    => $dicount_b_room_price,
							);
						} else {
							$room_price[] = array(
								"sale_price" => $b_room['price']
							);
						}
					}
				} else {
					if ( ! empty( $b_room['avil_by_date'] ) && $b_room['avil_by_date'] == "1" ) {
						$avail_date = json_decode( $b_room['avail_date'], true );

						if ( ! empty( $avail_date ) ) {
							foreach ( $avail_date as $repval ) {
								//Initial matching date array
								$show_hotel = [];
								// Check if any date range match with search form date range and set them on array
								if ( ! empty( $period ) ) {
									foreach ( $period as $date ) {
										$show_hotel[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $repval['check_in'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $repval['check_out'] ) );
									}
								}
								if ( ! in_array( 0, $show_hotel )) {
									if ( ! empty( $repval['price'] ) ) {
										$repval_price = $repval['price'];
										$dicount_b_room_price = 0;

										if ( $hotel_discount_type == "percent" ) {
											$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $repval_price - ( ( (int) $repval_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
										} else if ( $hotel_discount_type == "fixed" ) {
											$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $repval_price - (int) $hotel_discount_amount ), 2 ) ) );
										}
										if($dicount_b_room_price != 0) {
											$room_price[] = array(
												"regular_price" => $repval['price'],
												"sale_price"    => $dicount_b_room_price
											);
										} else {
											$room_price[] = array(
												"sale_price" => $repval['price'],
											);
										}
									}
								} else {
									if ( ! empty( $repval['price'] ) ) {
										$repval_price = $repval['price'];
										$dicount_b_room_price = 0;
										
										if ( $hotel_discount_type == "percent" ) {
											$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $repval_price - ( ( (int) $repval_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
										} else if ( $hotel_discount_type == "fixed" ) {
											$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $repval_price - (int) $hotel_discount_amount ), 2 ) ) );
										}
										
										if($dicount_b_room_price != 0) {
											$room_price[] = array(
												"regular_price" => $repval['price'],
												"sale_price"    => $dicount_b_room_price
											);
										} else {
											$room_price[] = array(
												"sale_price" => $repval['price'],
											);
										}
									}
								}
							}
						}else{
							$b_room_price = $b_room['price'];
							$room_price[] = $b_room_price;
							$dicount_b_room_price = 0;
							if ( $hotel_discount_type == "percent" ) {
								$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $b_room_price - ( ( $b_room_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
							} else if ( $hotel_discount_type == "fixed" ) {
								$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $b_room_price - $hotel_discount_amount ), 2 ) ) );
							}
							if($dicount_b_room_price != 0) {
								$room_price[] = $dicount_b_room_price;
							}
						}
					} else {
						if ( ! empty( $b_room['price'] ) ) {
							$b_room_price = $b_room['price'];
							
							$dicount_b_room_price = 0;

							if ( $hotel_discount_type == "percent" ) {
								$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $b_room_price - ( ( (int) $b_room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
							} else if ( $hotel_discount_type == "fixed" ) {
								$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $b_room_price - (int) $hotel_discount_amount ), 2 ) ) );
							}
							if($dicount_b_room_price != 0) {
								$room_price[] = array(
									"regular_price" => $b_room['price'],
									"sale_price"    => $dicount_b_room_price
								);
							} else {
								$room_price[] = array(
									"sale_price" => $b_room['price'],
								);
							}
						}
					}
					
				}
			} else if ( $pricing_by == 2 ) {
				if ( empty( $check_in_out ) ) {
					$adult_price = !empty($b_room['adult_price']) ? $b_room['adult_price'] : 0;
					$child_price = !empty($b_room['child_price']) ? $b_room['child_price'] : 0;
					$dicount_adult_price = 0;
					$dicount_child_price = 0;
					// discount calculation - start
					if ( $hotel_discount_type == "percent" ) {
						$dicount_adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
						$dicount_child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
					} else if ( $hotel_discount_type == "fixed" ) {
						$dicount_adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) );
						$dicount_child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) );
					}

					if ( $archive_page_price_settings == "all" ) {
						if ( ! empty( $b_room['adult_price'] ) ) {

							if ( $dicount_adult_price != 0 ) {
								$room_price[] = $room_price[] = array(
									"regular_price" => $b_room['adult_price'],
									"sale_price"    => $dicount_adult_price
								);
							} else {
								$room_price[] = array(
									"sale_price" => $b_room['adult_price'],
								);
							}
						}
						if ( ! empty( $b_room['child_price'] ) ) {

							if ( $dicount_child_price != 0 ) {
								$room_price[] = array(
									"regular_price" => $b_room['child_price'],
									"sale_price"    => $dicount_child_price
								);
							} else {
								$room_price[] = array(
									"sale_price" => $b_room['child_price'],
								);
							}
						}
					}
					if ( $archive_page_price_settings == "adult" ) {
						if ( ! empty( $b_room['adult_price'] ) ) {

							if ( $dicount_adult_price != 0 ) {
								$room_price[] = array(
									"regular_price" => $b_room['adult_price'],
									"sale_price"    => $dicount_adult_price
								);
							} else {
								$room_price[] = array(
									"sale_price" => $b_room['adult_price'],
								);
							}
						}
					}
					if ( $archive_page_price_settings == "child" ) {
						if ( ! empty( $b_room['child_price'] ) ) {

							if ( $dicount_child_price != 0 ) {
								$room_price[] = array(
									"regular_price" => $b_room['child_price'],
									"sale_price"    => $dicount_child_price
								);
							} else {
								$room_price[] = array(
									"sale_price" => $b_room['child_price'],
								);
							}
						}
					}
				} else {
					if ( ! empty( $b_room['avil_by_date'] ) && $b_room['avil_by_date'] == "1" ) {
						$avail_date = json_decode( $b_room['avail_date'], true );
						if ( ! empty( $avail_date ) ) {
							foreach ( $avail_date as $repval ) {
								//Initial matching date array
								$show_hotel = [];
								// Check if any date range match with search form date range and set them on array
								if ( ! empty( $period ) ) {
									foreach ( $period as $date ) {
										$show_hotel[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $repval['check_in'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $repval['check_out'] ) );
									}
								}
								if ( ! in_array( 0, $show_hotel ) ) {

									// discount calculation - start
									$adult_price = $repval['adult_price'];
									$child_price = $repval['child_price'];
									$dicount_adult_price = 0;
									$dicount_child_price = 0;

									if ( $hotel_discount_type == "percent" ) {
										// if ( ! empty( $dicount_adult_price ) && ! empty( $dicount_child_price ) ) {
											$dicount_adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
											$dicount_child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
										// }
									} else if ( $hotel_discount_type == "fixed" ) {
										// if ( ! empty( $dicount_adult_price ) && ! empty( $dicount_child_price ) ) {
											$dicount_adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) );
											$dicount_child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) );
										// }
									}
									// end
									if ( $archive_page_price_settings == "all" ) {
										if ( ! empty( $repval['adult_price'] ) ) {

											if ( $dicount_adult_price != 0 ) {
												$room_price[] = array(
													"regular_price" => $repval['adult_price'],
													"sale_price"    => $dicount_adult_price
												);
											} else {
												$room_price[] = array(
													"sale_price" => $repval['adult_price'],
												);
											}
										}
										if ( ! empty( $repval['child_price'] ) ) {

											if ( $dicount_child_price != 0  ) {
												$room_price[] = array(
													"regular_price" => $repval['child_price'],
													"sale_price"    => $dicount_child_price
												);
											} else {
												$room_price[] = array(
													"sale_price" => $repval['child_price'],
												);
											}
										}
									}
									if ( $archive_page_price_settings == "adult" ) {
										if ( ! empty( $repval['adult_price'] ) ) {

											if ( $dicount_adult_price != 0 ) {
												$room_price[] = array(
													"regular_price" => $repval['adult_price'],
													"sale_price"    => $dicount_adult_price
												);
											} else {
												$room_price[] = array(
													"sale_price" => $repval['adult_price'],
												);
											}
										}
									}
									if ( $archive_page_price_settings == "child" ) {
										if ( ! empty( $repval['child_price'] ) ) {
											
											if ( $dicount_child_price != 0  ) {
												$room_price[] = array(
													"regular_price" => $repval['child_price'],
													"sale_price"    => $dicount_child_price
												);
											} else {
												$room_price[] = array(
													"sale_price" => $repval['child_price'],
												);
											}
										}
									}
								} else {
									// discount calculation - start
									$adult_price = $repval['adult_price'];
									$child_price = $repval['child_price'];
									$dicount_adult_price = 0;
									$dicount_child_price = 0;

									if ( $hotel_discount_type == "percent" ) {
										$dicount_adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
										$dicount_child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
									} else if ( $hotel_discount_type == "fixed" ) {
										$dicount_adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
										$dicount_child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
									}
									// end
									if ( $archive_page_price_settings == "all" ) {
										if ( ! empty( $repval['adult_price'] ) ) {
											
											if ( $dicount_adult_price != 0 ) {
												$room_price[] = array(
													"regular_price" => $repval['adult_price'],
													"sale_price"    => $dicount_adult_price
												);
											} else {
												$room_price[] = array(
													"sale_price" => $repval['adult_price'],
												);
											}
										}
										if ( ! empty( $repval['child_price'] ) ) {
											
											if ( $dicount_child_price != 0 ) {
												$room_price[] = array(
													"regular_price" => $repval['child_price'],
													"sale_price"    => $dicount_child_price
												);
											} else {
												$room_price[] = array(
													"sale_price" => $repval['child_price'],
												);
											}
										}
									}
									if ( $archive_page_price_settings == "adult" ) {
										if ( ! empty( $repval['adult_price'] ) ) {
											
											if ( $dicount_adult_price != 0 ) {
												$room_price[] = array(
													"regular_price" => $repval['adult_price'],
													"sale_price"    => $dicount_adult_price
												);
											} else {
												$room_price[] = array(
													"sale_price" => $repval['adult_price'],
												);
											}
										}
									}
									if ( $archive_page_price_settings == "child" ) {
										if ( $repval['child_price'] ) {
											
											if ( $dicount_child_price != 0 ) {
												$room_price[] = array(
													"regular_price" => $repval['child_price'],
													"sale_price"    => $dicount_child_price
												);
											} else {
												$room_price[] = array(
													"sale_price" => $repval['child_price'],
												);
											}
										}
									}
								}
							}
						}
						
					} else {

						$adult_price = !empty($b_room['adult_price']) ? $b_room['adult_price'] : '';
						$child_price = !empty($b_room['child_price']) ? $b_room['child_price'] : '';
						$dicount_adult_price = 0;
						$dicount_child_price = 0;
						// discount calculation - start
						if ( $hotel_discount_type == "percent" ) {
							$dicount_adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
							$dicount_child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
						} else if ( $hotel_discount_type == "fixed" ) {
							$dicount_adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
							$dicount_child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
						}

						if ( $archive_page_price_settings == "all" ) {
							if ( ! empty( $b_room['adult_price'] ) ) {
								
								if ( $dicount_adult_price != 0  ) {
									$room_price[] = array(
										"regular_price" => $b_room['adult_price'],
										"sale_price"    => $dicount_adult_price
									);
								} else {
									$room_price[] = array(
										"sale_price" => $b_room['adult_price'],
									);
								}
							}
							if ( ! empty( $b_room['child_price'] ) ) {

								if ( $dicount_child_price != 0  ) {
									$room_price[] = array(
										"regular_price" => $b_room['child_price'],
										"sale_price"    => $dicount_child_price
									);
								} else {
									$room_price[] = array(
										"sale_price" => $b_room['child_price'],
									);
								}
							}
						}
						if ( $archive_page_price_settings == "adult" ) {
							if ( ! empty( $b_room['adult_price'] ) ) {

								if ( $dicount_adult_price != 0 ) {
									$room_price[] = array(
										"regular_price" => $b_room['adult_price'],
										"sale_price"    => $dicount_adult_price
									);
								} else {
									$room_price[] = array(
										"sale_price" => $b_room['adult_price'],
									);
								}
							}
						}
						if ( $archive_page_price_settings == "child" ) {
							if ( ! empty( $b_room['child_price'] ) ) {

								if ( $dicount_child_price != 0  ) {
									$room_price[] = array(
										"regular_price" => $b_room['child_price'],
										"sale_price"    => $dicount_child_price
									);
								} else {
									$room_price[] = array(
										"sale_price" => $b_room['child_price'],
									);
								}
							}
						}
					}
				}
			}
		}
	endif;

	$tf_hotel_arc_selected_template = ! empty( tf_data_types( tfopt( 'tf-template' ) )['hotel-archive'] ) ? tf_data_types( tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
	
	if ( $tf_hotel_arc_selected_template == "design-1" ) {
		?>
        <div class="tf-item-card tf-flex tf-item-hotel">
            <div class="tf-item-featured">
				<div class="tf-tag-items">
				<div class="tf-features-box">
					<?php if ( $featured ): ?>
						<div class="tf-feature tf-flex">
							<?php
							echo ! empty( $meta['featured_text'] ) ? $meta['featured_text'] : esc_html( "HOT DEAL" );
							?>
						</div>
					<?php endif; ?>
				</div>
					<?php
						if(sizeof($hotel_multiple_tags) > 0) {
							foreach($hotel_multiple_tags as $tag) {
								$hotel_tag_name = !empty($tag['hotel-tag-title']) ? __($tag['hotel-tag-title'], "tourfic") : '';
								$tag_background_color = !empty($tag["hotel-tag-color-settings"]["background"]) ? $tag["hotel-tag-color-settings"]["background"] : "#003162";
								$tag_font_color = !empty($tag["hotel-tag-color-settings"]["font"]) ? $tag["hotel-tag-color-settings"]["font"] : "#fff";

								echo <<<EOD
									<div class="tf-multiple-tag-item" style="color: $tag_font_color; background-color: $tag_background_color ">
										<span class="tf-multiple-tag">$hotel_tag_name</span>
									</div>
								EOD;
							}
						}
					?>
				</div>
                <a href="<?php echo esc_url( $url ); ?>">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'full' );
					} else {
						echo '<img src="' . TF_ASSETS_APP_URL . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
					}
					?>
                </a>
            </div>
            <div class="tf-item-details">
				<?php
				if ( ! empty( $address ) ) {
					?>
                    <div class="tf-title-meta tf-flex tf-flex-align-center tf-flex-gap-8">
                        <i class="fa-solid fa-location-dot"></i>
                        <p><?php echo $address; ?></p>
                    </div>
				<?php } ?>
                <div class="tf-title tf-mt-16">
                    <h2><a href="<?php echo esc_url( $url ); ?>"><?php the_title(); ?></a></h2>
                </div>
				<?php tf_archive_single_rating(); ?>
				<?php if ( $features ) { ?>
                    <div class="tf-archive-features tf-mt-16">
                        <ul>
							<?php foreach ( $features as $tfkey => $feature ) {
								$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
								if ( ! empty( $feature_meta ) ) {
									$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
								}
								if ( ! empty( $f_icon_type ) && $f_icon_type == 'fa' ) {
									$feature_icon = ! empty( $feature_meta['icon-fa'] ) ? '<i class="' . $feature_meta['icon-fa'] . '"></i>' : '';
								} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'c' ) {
									$feature_icon = ! empty( $feature_meta['icon-c'] ) ? '<img src="' . $feature_meta['icon-c'] . '" style="min-width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />' : '';
								}
								if ( $tfkey < 4 ) {
									?>
                                    <li class="tf-feature-lists">
										<?php
										if ( ! empty( $feature_icon ) ) {
											echo $feature_icon;
										} ?>
										<?php echo $feature->name; ?>
                                    </li>
								<?php }
							} ?>
							<?php
							if ( ! empty( $features ) ) {
								if ( count( $features ) > 4 ) {
									echo '<span>More....</span>';
								}
							}
							?>
                        </ul>
                    </div>
				<?php } ?>

                <div class="tf-details tf-mt-16">
                    <p><?php echo substr( wp_strip_all_tags( get_the_content() ), 0, 100 ) . '...'; ?></p>
                </div>
                <div class="tf-post-footer tf-flex tf-flex-align-center tf-flex-space-bttn tf-mt-16">
                    <div class="tf-pricing">
						<?php
							$room_price = array_filter( $room_price );
							$min_sale_price =  !empty($room_price) ? min( array_column( $room_price, 'sale_price' ) ) : 0;

							if ( ! empty( $room_price ) ):
								$min_regular_price = 0;

								array_walk( $room_price, function ( $value ) use ( $min_sale_price, &$min_regular_price ) {
									if ( is_array( $value )  && count($value) > 0) {
										if ( array_key_exists( "regular_price", $value ) ) {
											if ( $value["sale_price"] == $min_sale_price ) {
												$min_regular_price = $value["regular_price"];
											}
										}
									}
								} );
								echo __( "From ", "tourfic" );
								//get the lowest price from all available room price
								$lowest_sale_price = wc_price( $min_sale_price );
								echo " $lowest_sale_price" . " ";
								if ( $min_regular_price != 0 ) {
									$lowest_regular_price = wc_price( $min_regular_price );
									echo "<del>" . $lowest_regular_price . "<del>";
								}
							endif; 
						?>
                    </div>
                    <div class="tf-booking-bttns">
                        <a class="tf-btn-normal btn-secondary" href="<?php echo esc_url( $url ); ?>"><?php _e( "View Details", "tourfic" ); ?></a>
                    </div>
                </div>
            </div>
        </div>
	<?php }
	elseif ( $tf_hotel_arc_selected_template == "design-2" ) {
		$first_gallery_image = explode(',', $gallery);	
	?>
	<div class="tf-available-room">
		<div class="tf-available-room-gallery">                       
			<div class="tf-room-gallery">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'full' );
					} else {
						echo '<img src="' . TF_ASSETS_APP_URL . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
					}
					?>
			</div>
			<?php 
			if( !empty($gallery_ids) ){ ?>                                                                     
			<div data-id="<?php echo get_the_ID(); ?>" data-type="tf_hotel" class="tf-room-gallery tf-popup-buttons tf-hotel-room-popup" style="<?php echo !empty($first_gallery_image[0]) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.70) 0%, rgba(48, 40, 28, 0.70) 100%), url('.esc_url(wp_get_attachment_image_url($first_gallery_image[0])).'), lightgray 50% / cover no-repeat; background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
				<svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
				<g id="content">
				<path id="Rectangle 2111" d="M5.5 16.9745C5.6287 18.2829 5.91956 19.1636 6.57691 19.8209C7.75596 21 9.65362 21 13.4489 21C17.2442 21 19.1419 21 20.3209 19.8209C21.5 18.6419 21.5 16.7442 21.5 12.9489C21.5 9.15362 21.5 7.25596 20.3209 6.07691C19.6636 5.41956 18.7829 5.1287 17.4745 5" stroke="#FDF9F4" stroke-width="1.5"></path>
				<path id="Rectangle 2109" d="M1.5 9C1.5 5.22876 1.5 3.34315 2.67157 2.17157C3.84315 1 5.72876 1 9.5 1C13.2712 1 15.1569 1 16.3284 2.17157C17.5 3.34315 17.5 5.22876 17.5 9C17.5 12.7712 17.5 14.6569 16.3284 15.8284C15.1569 17 13.2712 17 9.5 17C5.72876 17 3.84315 17 2.67157 15.8284C1.5 14.6569 1.5 12.7712 1.5 9Z" stroke="#FDF9F4" stroke-width="1.5"></path>
				<path id="Vector" d="M1.5 10.1185C2.11902 10.0398 2.74484 10.001 3.37171 10.0023C6.02365 9.9533 8.61064 10.6763 10.6711 12.0424C12.582 13.3094 13.9247 15.053 14.5 17" stroke="#FDF9F4" stroke-width="1.5" stroke-linejoin="round"></path>
				<path id="Vector_2" d="M12.4998 6H12.5088" stroke="#FDF9F4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
				</g>
				</svg>
			</div>
			<?php } ?>
			<div class="tf-available-labels">
				<?php if ( $featured ): ?>
				<span class="tf-available-labels-featured"><?php _e("Featured", "tourfic"); ?></span>
				<?php endif; ?>
				<?php
					if(sizeof($hotel_multiple_tags) > 0) {
						foreach($hotel_multiple_tags as $tag) {
							$hotel_tag_name = !empty($tag['hotel-tag-title']) ? __($tag['hotel-tag-title'], "tourfic") : '';
							$tag_background_color = !empty($tag["hotel-tag-color-settings"]["background"]) ? $tag["hotel-tag-color-settings"]["background"] : "#003162";
							$tag_font_color = !empty($tag["hotel-tag-color-settings"]["font"]) ? $tag["hotel-tag-color-settings"]["font"] : "#fff";

							echo <<<EOD
								<span class="tf-multiple-tag" style="color: $tag_font_color; background-color: $tag_background_color ">$hotel_tag_name</span>
							EOD;
						}
					}
				?>
			</div>  
			<div class="tf-available-ratings">
				<?php tf_archive_single_rating(); ?>
				<i class="fa-solid fa-star"></i>
			</div>  
		</div>
		<div class="tf-available-room-content">
			<div class="tf-available-room-content-left">
				<div class="tf-card-heading-info">
				<div class="tf-section-title-and-location">
					<h2 class="tf-section-title"><?php echo tourfic_character_limit_callback( get_the_title(), 55 ); ?></h2>
					<?php
					if ( ! empty( $address ) ) {
					?>
					<div class="tf-title-location">
						<div class="location-icon">
							<i class="ri-map-pin-line"></i>
						</div>
						<span><?php echo tourfic_character_limit_callback( esc_html( $address ), 65 ); ?></span>
					</div>
					<?php } ?>
				</div>
				<div class="tf-mobile tf-pricing-info">
					<?php if ( ! empty( $discount_amount ) ){ ?>
						<div class="tf-available-room-off">
							<span>
								<?php echo min( $discount_amount ); ?>% <?php _e( "Off ", "tourfic" ); ?>
							</span>
						</div>
					<?php } ?>
					<div class="tf-available-room-price">
						<span class="tf-price-from">
						<?php
							$room_price = array_filter( $room_price );
							$min_sale_price =  !empty($room_price) ? min( array_column( $room_price, 'sale_price' ) ) : 0;

							if ( ! empty( $room_price ) ):
								$min_regular_price = 0;

								array_walk( $room_price, function ( $value ) use ( $min_sale_price, &$min_regular_price ) {
									if ( is_array( $value )  && count($value) > 0) {
										if ( array_key_exists( "regular_price", $value ) ) {
											if ( $value["sale_price"] == $min_sale_price ) {
												$min_regular_price = $value["regular_price"];
											}
										}
									}
								} );
								echo __( "From ", "tourfic" );
								//get the lowest price from all available room price
								$lowest_sale_price = wc_price( $min_sale_price );
								echo " $lowest_sale_price" . " ";
								if ( $min_regular_price != 0 ) {
									$lowest_regular_price = wc_price( $min_regular_price );
									echo "<del>" . $lowest_regular_price . "<del>";
								}
							endif; 
						?>
						</span>
					</div>
				</div>
				</div>
				<?php if ( $features ) { ?>
				<ul class="features">
				<?php foreach ( $features as $tfkey => $feature ) {
				$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
				if ( ! empty( $feature_meta ) ) {
					$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
				}
				if ( ! empty( $f_icon_type ) && $f_icon_type == 'fa' ) {
					$feature_icon = ! empty( $feature_meta['icon-fa'] ) ? '<i class="' . $feature_meta['icon-fa'] . '"></i>' : '';
				} elseif ( ! empty( $f_icon_type ) && $f_icon_type == 'c' ) {
					$feature_icon = ! empty( $feature_meta['icon-c'] ) ? '<img src="' . $feature_meta['icon-c'] . '" style="min-width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />' : '';
				}
				if ( $tfkey < 5 ) {
				?>
					<li>
					<?php
					if ( ! empty( $feature_icon ) ) {
						echo $feature_icon;
					} ?>
					<?php echo $feature->name; ?>
					</li>
				<?php } } ?>
				<?php if(count($features)>5){ ?>
					<li><a href="<?php echo esc_url( $url ); ?>"><?php _e("View More", "tourfic"); ?></a></li>
				<?php } ?>
				</ul>
				<?php } ?>
			</div>
			<div class="tf-available-room-content-right">
				<div class="tf-card-pricing-heading">
				<?php
				if ( ! empty( $tf_lowestAmount_items ) ){ ?>
					<div class="tf-available-room-off">
						<span>
							<?php echo $tf_lowestAmount_items['type']=="percent" ? $tf_lowestAmount.'%' : wc_price($tf_lowestAmount) ?> <?php _e( "Off ", "tourfic" ); ?>
						</span>
					</div>
				<?php } ?>
				<div class="tf-available-room-price">
					<span class="tf-price-from">
					<?php
						$room_price = array_filter( $room_price );
						$min_sale_price =  !empty($room_price) ? min( array_column( $room_price, 'sale_price' ) ) : 0;

						if ( ! empty( $room_price ) ):
							$min_regular_price = 0;

							array_walk( $room_price, function ( $value ) use ( $min_sale_price, &$min_regular_price ) {
								if ( is_array( $value )  && count($value) > 0) {
									if ( array_key_exists( "regular_price", $value ) ) {
										if ( $value["sale_price"] == $min_sale_price ) {
											$min_regular_price = $value["regular_price"];
										}
									}
								}
							} );
							echo __( "From ", "tourfic" );
							//get the lowest price from all available room price
							$lowest_sale_price = wc_price( $min_sale_price );
							if ( $min_regular_price != 0 ) {
								$lowest_regular_price = strip_tags( wc_price( $min_regular_price ) );
								echo "<del>" . $lowest_regular_price . "</del>" . " " . "<span>" . $lowest_sale_price . "</span>";
							} else {
								echo " $lowest_sale_price" . " ";
							}
						endif; 
					?>
					</span>
				</div>
				</div>              
				<a href="<?php echo esc_url( $url ); ?>" class="view-hotel"><?php _e("See details", "tourfic"); ?></a>
			</div>
		</div>
	</div>
	<?php } else { ?>
        <div class="single-tour-wrap <?php echo $featured ? esc_attr( 'tf-featured' ) : '' ?>">
            <div class="single-tour-inner">
				<?php if ( $featured ): ?>
                    <div class="tf-featured-badge">
                        <span><?php echo ! empty( $meta['featured_text'] ) ? $meta['featured_text'] : esc_html( "HOT DEAL" ); ?></span>
                    </div>
				<?php endif; ?>
                <div class="tourfic-single-left">
					<div class="default-tags-container">
						<?php 
						if(sizeof($hotel_multiple_tags) > 0) {
							foreach($hotel_multiple_tags as $tag) {
								$hotel_tag_name = !empty($tag['hotel-tag-title']) ? __($tag['hotel-tag-title'], "tourfic") : '';
								$tag_background_color = !empty($tag["hotel-tag-color-settings"]["background"]) ? $tag["hotel-tag-color-settings"]["background"] : "#003162";
								$tag_font_color = !empty($tag["hotel-tag-color-settings"]["font"]) ? $tag["hotel-tag-color-settings"]["font"] : "#fff";

								echo <<<EOD
									<span class="default-single-tag" style="color: $tag_font_color; background-color: $tag_background_color">$hotel_tag_name</span>
								EOD;
							}
						}
						?>
					</div>
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
													$room_price = array_filter( $room_price );
													if ( ! empty( $room_price ) ):
														?>
                                                        <div class="tf-room-price">
															<?php
																$min_sale_price = min( array_column( $room_price, 'sale_price' ) );
																$min_regular_price = 0;

																array_walk($room_price, function($value) use ($min_sale_price, &$min_regular_price) {
																	if(!empty($value)) {
																		if(is_array($value)) {
																			if(array_key_exists("regular_price", $value)) {
																				if($value["sale_price"] == $min_sale_price) {
																					$min_regular_price = $value["regular_price"];
																				}
																			}
																		}
																	}
																});
																echo __( "From ", "tourfic" );
																//get the lowest price from all available room price
																$lowest_sale_price  = wc_price( $min_sale_price );
																echo " $lowest_sale_price" . " ";
																if($min_regular_price != 0) {
																	$lowest_regular_price  = wc_price( $min_regular_price );
																	echo "<del>" . $lowest_regular_price . "<del>";
																}
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
		$filters_rooms        = unserialize( $tf_hotel_rooms_value );
	}

	// Remove disabled rooms
	if ( ! empty( $meta['room'] ) ):
		$meta = array_filter( $meta['room'], function ( $value ) {
			return ! empty( $value ) && ! empty( $value['enable'] ) ? $value['enable'] : '' != '0';
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
		if ( ! empty( $back_adult ) && $back_adult >= $adults ) {
			$adult_counter ++;
		}
	}

	$adult_result = array_filter( $back_adults );

	/**
	 * Child Number Validation
	 */
	$back_childs   = array_column( $meta, 'child' );
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
	$back_rooms   = array_column( $meta, 'num-room' );
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
		$avil_by_date = array_column( $meta, 'avil_by_date' );

		// Check if any room available without custom date range
		if ( in_array( 0, $avil_by_date ) || empty( $avil_by_date ) ) {

			$has_hotel = true; // Show that hotel

		} else {
			// If all the room has custom date range then filter the rooms by date

			// Get custom date range repeater
			$dates = array_column( $meta, 'avail_date' );
			// If no date range return
			if ( empty( $dates ) ) {
				return;
			}

			$tf_check_in_date = 0;
			$searching_period = [];
			// Check if any date range match with search form date range and set them on array
			if ( ! empty( $period ) ) {
				foreach ( $period as $datekey => $date ) {
					if(0==$datekey){
						$tf_check_in_date = $date->format( 'Y/m/d' );
					}
					$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
				}
			}

			// Initial available dates array
			$availability_dates = [];
			$tf_check_in_date_price = [];
			// Run loop through custom date range repeater and filter out only the dates
			foreach ( $dates as $date ) {
				if ( ! empty( $date ) && gettype( $date ) == "string" ) {
					$date                 = json_decode( $date, true );
					foreach($date as $sdate){
						if($tf_check_in_date==$sdate['check_in']){
							$tf_check_in_date_price['price'] = $sdate['price'];
							$tf_check_in_date_price['adult_price'] = $sdate['adult_price'];
							$tf_check_in_date_price['child_price'] = $sdate['child_price'];
						}
						$availability_dates[$sdate['check_in']] = $sdate['check_in'];
					}
				}
			}

			$tf_common_dates = array_intersect($availability_dates, $searching_period);

			//Initial matching date array
			$show_hotel = [];

			if (count($tf_common_dates) === count($searching_period)) {
				$show_hotel[] = 1;
			}

			// If any date range matches show hotel
			if ( !empty($show_hotel) && ! in_array( 0, $show_hotel ) ) {
				if ( ! empty( $filters_rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
					foreach ( $filters_rooms as $room ) {
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
		$avil_by_date = array_column( $meta, 'avil_by_date' );

		// Check if any room available without custom date range
		if ( in_array( 0, $avil_by_date ) || empty( $avil_by_date ) ) {

			$has_hotel = true; // Show that hotel

		} else {
			// If all the room has custom date range then filter the rooms by date

			// Get custom date range repeater
			$dates = array_column( $meta, 'avail_date' );

			// If no date range return
			if ( empty( $dates ) ) {
				return;
			}

			$tf_check_in_date = 0;
			$searching_period = [];
			// Check if any date range match with search form date range and set them on array
			if ( ! empty( $period ) ) {
				foreach ( $period as $datekey => $date ) {
					if(0==$datekey){
						$tf_check_in_date = $date->format( 'Y/m/d' );
					}
					$searching_period[$date->format( 'Y/m/d' )] = $date->format( 'Y/m/d' );
				}
			}

			// Initial available dates array
			$availability_dates = [];
			$tf_check_in_date_price = [];
			// Run loop through custom date range repeater and filter out only the dates
			foreach ( $dates as $date ) {
				if ( ! empty( $date ) && gettype( $date ) == "string" ) {
					$date                 = json_decode( $date, true );
					foreach($date as $sdate){
						if($tf_check_in_date==$sdate['check_in']){
							$tf_check_in_date_price['price'] = $sdate['price'];
							$tf_check_in_date_price['adult_price'] = $sdate['adult_price'];
							$tf_check_in_date_price['child_price'] = $sdate['child_price'];
						}
						$availability_dates[$sdate['check_in']] = $sdate['check_in'];
					}
				}
			}

			$tf_common_dates = array_intersect($availability_dates, $searching_period);

			//Initial matching date array
			$show_hotel = [];

			if (count($tf_common_dates) === count($searching_period)) {
				$show_hotel[] = 1;
			}

			// If any date range matches show hotel
			if ( ! in_array( 0, $show_hotel ) ) {
				if ( ! empty( $filters_rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
					foreach ( $filters_rooms as $room ) {
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
		$filters_rooms        = unserialize( $tf_hotel_rooms_value );
	}

	// Remove disabled rooms
	if ( ! empty( $meta['room'] ) ):
		$meta = array_filter( $meta['room'], function ( $value ) {
			return ! empty( $value ) && ! empty( $value['enable'] ) ? $value['enable'] : '' != '0';
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
		if ( ! empty( $back_adult ) && $back_adult >= $adults ) {
			$adult_counter ++;
		}
	}

	$adult_result = array_filter( $back_adults );

	/**
	 * Child Number Validation
	 */
	$back_childs   = array_column( $meta, 'child' );
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
	$back_rooms   = array_column( $meta, 'num-room' );
	$room_counter = 0;
	foreach ( $back_rooms as $back_room ) {
		if ( ! empty( $back_room ) && $back_room >= $room ) {
			$room_counter ++;
		}
	}

	$room_result = array_filter( $back_rooms );

	// If adult and child number validation is true proceed
	if ( ! empty( $adult_result ) && $adult_counter > 0 && ! empty( $childs_result ) && $child_counter > 0 && ! empty( $room_result ) && $room_counter > 0 ) {

		if ( ! empty( $filters_rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
			foreach ( $filters_rooms as $room ) {
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
				if ( ! empty( $room['price'] ) ) {
					if ( $startprice <= $room['price'] && $room['price'] <= $endprice ) {
						$has_hotel = true;
					}
				}
			}
		} else {
			$has_hotel = true; // Show that hotel
		}

	}
	if ( ! empty( $adult_result ) && $adult_counter > 0 && empty( $childs_result ) && ! empty( $room_result ) && $room_counter > 0 ) {
		if ( ! empty( $filters_rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
			foreach ( $filters_rooms as $room ) {
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
				if ( ! empty( $room['price'] ) ) {
					if ( $startprice <= $room['price'] && $room['price'] <= $endprice ) {
						$has_hotel = true;
					}
				}
			}
		} else {
			$has_hotel = true; // Show that hotel
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
	$room_id = trim( $meta_field, "tf_hotels_opt[room][][order_id]" );
	# Get post id
	$post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
	# Get hotel meta
	$meta = get_post_meta( $post_id, 'tf_hotels_opt', true );

	$order_id_retrive = tf_data_types( $meta['room'] );

	# Set order id field's value to blank
	$order_id_retrive[ $room_id ]['order_id'] = '';

	$meta['room'] = $order_id_retrive;
	# Update whole hotel meta
	update_post_meta( $post_id, 'tf_hotels_opt', $meta );

	# Send success message
	wp_send_json_success( array(
		'message' => __( "Order ids removed successfully!", "tourfic" ),
	) );

	wp_die();
}

/**
 * Ajax hotel quick view
 */

function tf_hotel_quickview_callback() {

	$meta = get_post_meta( $_POST['post_id'], 'tf_hotels_opt', true );

	// Single Template Style
	$tf_hotel_layout_conditions = ! empty( $meta['tf_single_hotel_layout_opt'] ) ? $meta['tf_single_hotel_layout_opt'] : 'global';
	if ( "single" == $tf_hotel_layout_conditions ) {
		$tf_hotel_single_template = ! empty( $meta['tf_single_hotel_template'] ) ? $meta['tf_single_hotel_template'] : 'design-1';
	}
	$tf_hotel_global_template = ! empty( tf_data_types( tfopt( 'tf-template' ) )['single-hotel'] ) ? tf_data_types( tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';

	$tf_hotel_selected_check = ! empty( $tf_hotel_single_template ) ? $tf_hotel_single_template : $tf_hotel_global_template;

	$tf_hotel_selected_template = $tf_hotel_selected_check;

	$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
	if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
		$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $rooms );
		$rooms                = unserialize( $tf_hotel_rooms_value );
	}
	if( $tf_hotel_selected_template == "design-1" || $tf_hotel_selected_template == "default" ){
	?>
	<div class="tf-hotel-quick-view" style="display: flex">
		<?php
		foreach ( $rooms as $key => $room ) :
			$enable = ! empty( $room['enable'] ) ? $room['enable'] : '';
			if ( $enable == '1' && $room['unique_id'] . $key == $_POST['uniqid_id'] ) :
				$tf_room_gallery = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
				$child_age_limit = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";
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

					if ( $tf_hotel_selected_template == "design-1" ) {
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
						<?php if ( ! empty( $room['features'] ) ) { ?>

                            <div class="tf-template-1 tf-room-adv-info">
                                <h4><?php _e( "Amenities", "tourfic" ); ?></h4>
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
					<?php } else { ?>
                        <?php if(!empty($room['title'])) : ?>
							<h3><?php echo esc_html( $room['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( !empty( $room['description'] ) ) : ?>
							<p><?php echo $room['description']; ?></p>
						<?php endif; ?>
                        
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
											<?php
											if ( ! empty( $child_age_limit ) ) {
												printf( __( 'Children Age Limit %s Years', 'tourfic' ), $child_age_limit );
											} else {
												_e( 'Number of Children', 'tourfic' );
											}
											?>
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
	} if( $tf_hotel_selected_template == "design-2" ){ 
		foreach ( $rooms as $key => $room ) :
		
		$enable = ! empty( $room['enable'] ) ? $room['enable'] : '';
		if ( $enable == '1' && $room['unique_id'] . $key == $_POST['uniqid_id'] ) :
			$tf_room_gallery = ! empty( $room['gallery'] ) ? $room['gallery'] : '';
			$child_age_limit = ! empty( $room['children_age_limit'] ) ? $room['children_age_limit'] : "";

			$footage      = ! empty( $room['footage'] ) ? $room['footage'] : '';
			$bed          = ! empty( $room['bed'] ) ? $room['bed'] : '';
			$adult_number = ! empty( $room['adult'] ) ? $room['adult'] : '0';
			$child_number = ! empty( $room['child'] ) ? $room['child'] : '0';
			
			if ( $tf_room_gallery ) {
				$tf_room_gallery_ids = explode( ',', $tf_room_gallery );
			}
		?>
		<div class="tf-popup-inner">
            
            <div class="tf-popup-body">
                <div class="tf-popup-left">
					<?php 
						if ( ! empty( $tf_room_gallery_ids ) ) {
						foreach ( $tf_room_gallery_ids as $key => $gallery_item_id ) {
						$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
					?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php _e("Room Image","tourfic"); ?>" class="tf-popup-image">
					<?php } } ?>
                </div>
                <div class="tf-popup-right">
                    <h4 class="tf-popup-info-title"><?php _e("Room details", "tourfic"); ?></h4>
                    <ul>
						<?php if ( $footage ) { ?>
							<li><i class="fas fa-ruler-combined"></i> <?php echo $footage; ?><?php _e( 'sft', 'tourfic' ); ?></li>
						<?php } ?>
						<?php if ( $bed ) { ?>
							<li><i class="fas fa-bed"></i> <?php echo $bed; ?><?php _e( ' Beds', 'tourfic' ); ?></li>
						<?php } ?>
						<?php if ( $adult_number ) { ?>
							<li><i class="fas fa-male"></i> <?php echo $adult_number; ?><?php _e( ' Adults', 'tourfic' ); ?></li>
						<?php } ?>
						<?php if ( $child_number ) { ?>
							<li><i class="fas fa-baby"></i><?php echo $child_number; ?><?php _e( ' Child', 'tourfic' ); ?></li>
						<?php } ?>                        
                    </ul> 
					  
                    <h4 class="tf-popup-info-title"><?php _e("Other benefits", "tourfic"); ?></h4>
                    <ul>
						<?php 
						if( !empty($room['features']) ){
						foreach ( $room['features'] as $feature ) {
						$room_f_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
						if ( ! empty( $room_f_meta ) ) {
							$room_icon_type = ! empty( $room_f_meta['icon-type'] ) ? $room_f_meta['icon-type'] : '';
						}
						if ( ! empty( $room_icon_type ) && $room_icon_type == 'fa' && !empty($room_f_meta['icon-fa']) ) {
							$room_feature_icon = '<i class="' . $room_f_meta['icon-fa'] . '"></i>';
						} elseif ( ! empty( $room_icon_type ) && $room_icon_type == 'c' && ! empty( $room_f_meta['icon-c'] )) {
							$room_feature_icon = '<img src="' . $room_f_meta['icon-c'] . '" style="min-width: ' . $room_f_meta['dimention'] . 'px; height: ' . $room_f_meta['dimention'] . 'px;" />';
						}

						$room_term = get_term( $feature ); ?>
						<li>
							<?php echo ! empty( $room_feature_icon ) ? $room_feature_icon : ''; ?>
							<?php echo $room_term->name; ?>
						</li>
						<?php } } ?>
						                       
                    </ul>
                </div>
            </div>                
            <div class="tf-popup-close">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
	<?php
	endif;
	endforeach;
	}
	wp_die();
}

add_action( 'wp_ajax_tf_tour_details_qv', 'tf_hotel_quickview_callback' );
add_action( 'wp_ajax_nopriv_tf_tour_details_qv', 'tf_hotel_quickview_callback' );

/**
 * Ajax hotel Archive Hotel Gallery quick view
 * @author Jahid
 */

function tf_hotel_archive_popup_qv_callback(){
	if( !empty($_POST['post_type']) && "tf_hotel"==$_POST['post_type']){
		$meta = get_post_meta( $_POST['post_id'], 'tf_hotels_opt', true );
		$gallery = ! empty( $meta['gallery'] ) ? $meta['gallery'] : '';
		if ( $gallery ) {
			$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
		}
	}

	if( !empty($_POST['post_type']) && "tf_tours"==$_POST['post_type']){
		$meta = get_post_meta( $_POST['post_id'], 'tf_tours_opt', true );
		$gallery = ! empty( $meta['tour_gallery'] ) ? $meta['tour_gallery'] : '';
		if ( $gallery ) {
			$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
		}
	}

	if ( ! empty( $gallery_ids ) ) {
	foreach ( $gallery_ids as $key => $gallery_item_id ) {
	$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
	?>
		<img src="<?php echo esc_url($image_url); ?>" alt="" class="tf-popup-image">
	<?php } }
	wp_die();
}
add_action( 'wp_ajax_tf_hotel_archive_popup_qv', 'tf_hotel_archive_popup_qv_callback' );
add_action( 'wp_ajax_nopriv_tf_hotel_archive_popup_qv', 'tf_hotel_archive_popup_qv_callback' );

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
			if(!empty($meta) && !empty($meta['room'])){
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

		if ( tfopt( 'date_hotel_search' ) ) {
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

/*
 * Hotel without booking ajax
 * @since 2.10.3
 * @author Foysal
 */
if ( ! function_exists( 'tf_hotel_booking_popup_callback' ) ) {
	add_action( 'wp_ajax_tf_hotel_booking_popup', 'tf_hotel_booking_popup_callback' );
	add_action( 'wp_ajax_nopriv_tf_hotel_booking_popup', 'tf_hotel_booking_popup_callback' );

	function tf_hotel_booking_popup_callback() {
		// Check nonce security
		if ( ! isset( $_POST['tf_room_booking_nonce'] ) || ! wp_verify_nonce( $_POST['tf_room_booking_nonce'], 'check_room_booking_nonce' ) ) {
			return;
		}

		// Declaring errors & hotel data array
		$response = [];
		/**
		 * Data from booking form
		 *
		 * With errors
		 */
		$post_id         = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : null;
		$room_id         = isset( $_POST['room_id'] ) ? intval( sanitize_text_field( $_POST['room_id'] ) ) : null;
		$unique_id       = isset( $_POST['unique_id'] ) ? intval( sanitize_text_field( $_POST['unique_id'] ) ) : null;
		$location        = isset( $_POST['location'] ) ? sanitize_text_field( $_POST['location'] ) : '';
		$adult           = isset( $_POST['adult'] ) ? intval( sanitize_text_field( $_POST['adult'] ) ) : '0';
		$child           = isset( $_POST['child'] ) ? intval( sanitize_text_field( $_POST['child'] ) ) : '0';
		$children_ages   = isset( $_POST['children_ages'] ) ? sanitize_text_field( $_POST['children_ages'] ) : '0';
		$room_selected   = isset( $_POST['room'] ) ? intval( sanitize_text_field( $_POST['room'] ) ) : '0';
		$check_in        = isset( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
		$check_out       = isset( $_POST['check_out_date'] ) ? sanitize_text_field( $_POST['check_out_date'] ) : '';
		$deposit         = isset( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : false;
		$airport_service = isset( $_POST['airport_service'] ) ? sanitize_text_field( $_POST['airport_service'] ) : '';
		$total_people    = $adult + $child;

		# Calculate night number
		if ( $check_in && $check_out ) {
			$check_in_stt   = strtotime( $check_in . ' +1 day' );
			$check_out_stt  = strtotime( $check_out );
			$day_difference = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
		}

		// Check errors
		if ( ! $check_in ) {
			$response['errors'][] = __( 'Check-in date missing.', 'tourfic' );
		}
		if ( ! $check_out ) {
			$response['errors'][] = __( 'Check-out date missing.', 'tourfic' );
		}
		if ( ! $adult ) {
			$response['errors'][] = __( 'Select Adult(s).', 'tourfic' );
		}
		if ( ! $room_selected ) {
			$response['errors'][] = __( 'Select Room(s).', 'tourfic' );
		}
		if ( ! $post_id ) {
			$response['errors'][] = __( 'Unknown Error! Please try again.', 'tourfic' );
		}

		/**
		 * Backend options panel data
		 *
		 * @since 2.2.0
		 */
		$product_id  = get_post_meta( $post_id, 'product_id', true );
		$post_author = get_post_field( 'post_author', $post_id );
		$meta        = get_post_meta( $post_id, 'tf_hotels_opt', true );
		$rooms       = ! empty( $meta['room'] ) ? $meta['room'] : '';
		if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
			$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $rooms );
			$rooms                = unserialize( $tf_hotel_rooms_value );
		}
		$avail_by_date = ! empty( $rooms[ $room_id ]['avil_by_date'] ) && $rooms[ $room_id ]['avil_by_date'];
		if ( $avail_by_date ) {
			$avail_date = ! empty( $rooms[ $room_id ]['avail_date'] ) ? json_decode( $rooms[ $room_id ]['avail_date'], true ) : [];
		}
		$room_name       = $rooms[ $room_id ]['title'];
		$pricing_by      = $rooms[ $room_id ]['pricing-by'];
		$price_multi_day = ! empty( $rooms[ $room_id ]['price_multi_day'] ) ? $rooms[ $room_id ]['price_multi_day'] : false;

		$room_stay_requirements = array();
		foreach ( $rooms as $key => $room ) {
			$room_stay_requirements[] = array(
				"uid"      => ! empty( $room["unique_id"] ) ? $room["unique_id"] : '',
				'min_stay' => ! empty( $room["minimum_stay_requirement"] ) ? $room["minimum_stay_requirement"] : 0,
				"max_stay" => ! empty( $room["maximum_stay_requirement"] ) ? $room["maximum_stay_requirement"] : 0
			);
		}

		foreach ( $room_stay_requirements as $min_max_days ) {
			if ( $day_difference < $min_max_days["min_stay"] && $min_max_days["min_stay"] > 0 ) {
				if ( $min_max_days["uid"] == $unique_id ) {
					if ( $min_max_days["max_stay"] == 0 ) {
						$response['errors'][] = __( "Your Stay Requirement is Minimum {$min_max_days['min_stay']} Days", 'tourfic' );
					} else {
						$response['errors'][] = __( "Your Stay Requirement is Minimum {$min_max_days['min_stay']} Days to Maximum {$min_max_days['max_stay']}", 'tourfic' );


					}
				}
			} else if ( $day_difference > $min_max_days["max_stay"] && $min_max_days["max_stay"] > 0 ) {
				if ( $min_max_days["uid"] == $unique_id ) {
					$response['errors'][] = __( "Your Maximum Stay Requirement is {$min_max_days['max_stay']} Days", 'tourfic' );
				}
			}
		}
		// Hotel Room Discount Data
		$hotel_discount_type   = ! empty( $rooms[ $room_id ]["discount_hotel_type"] ) ? $rooms[ $room_id ]["discount_hotel_type"] : "none";
		$hotel_discount_amount = ! empty( $rooms[ $room_id ]["discount_hotel_price"] ) ? $rooms[ $room_id ]["discount_hotel_price"] : '';

		/**
		 * If no errors then process
		 */
		if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {

			// Discount Calculation and Checking
			$adult_price = ! empty( $rooms[ $room_id ]['adult_price'] ) ? $rooms[ $room_id ]['adult_price'] : '';
			$child_price = ! empty( $rooms[ $room_id ]['child_price'] ) ? $rooms[ $room_id ]['child_price'] : '';
			$room_price  = ! empty( $rooms[ $room_id ]['price'] ) ? $rooms[ $room_id ]['price'] : '';

			if ( $hotel_discount_type == "percent" ) {
				if ( $pricing_by == 1 ) {
					$room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $room_price - ( ( $room_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
				}
				if ( $pricing_by == 2 ) {
					$adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
					$child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $child_price - ( ( $child_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
				}
			}

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

					$available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
						$date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
						$date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

						return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
					} ) );

					if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {
						$room_price  = ! empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $rooms[ $room_id ]['price'];
						$adult_price = ! empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $rooms[ $room_id ]['adult_price'];
						$child_price = ! empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $rooms[ $room_id ]['child_price'];

						if ( $hotel_discount_type == "percent" ) {
							if ( $pricing_by == 1 ) {
								$room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $room_price - ( ( $room_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
							}
							if ( $pricing_by == 2 ) {
								$adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
								$child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $child_price - ( ( $child_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
							}
						}

						$total_price += $pricing_by == '1' ? $room_price : ( ( $adult_price * $adult ) + ( $child_price * $child ) );
					};

				}

				$price_total = $total_price * $room_selected;
			} else {

				if ( $pricing_by == '1' ) {

					$total_price = $rooms[ $room_id ]['price'];

					if ( $hotel_discount_type == "percent" ) {
						$total_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $total_price - ( ( $total_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
					} else if ( $hotel_discount_type == "fixed" ) {
						$total_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $adult_price - $hotel_discount_amount ), 2 ) ) );
					}
				} elseif ( $pricing_by == '2' ) {
					$adult_price = $rooms[ $room_id ]['adult_price'];
					$child_price = $rooms[ $room_id ]['child_price'];

					if ( $hotel_discount_type == "percent" ) {
						$adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
						$child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $child_price - ( ( $child_price / 100 ) * $hotel_discount_amount ), 2 ) ) );
					}
					$adult_price = $adult_price * $adult;
					$child_price = $child_price * $child;
					$total_price = $adult_price + $child_price;
				}

				# Multiply pricing by night number
				if ( ! empty( $day_difference ) && $price_multi_day == true ) {
					$price_total = $total_price * $room_selected * $day_difference;
				} else {
					$price_total = $total_price * ( $room_selected * $day_difference + 1 );
				}

			}

			$airport_service_arr = tf_hotel_airport_service_title_price( $post_id, $adult, $child, $airport_service );

			# check for deposit
			if ( $deposit == "true" ) {

				tf_get_deposit_amount( $rooms[ $room_id ], $price_total, $deposit_amount, $has_deposit );
				if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) {
//					if ( ! empty( $airport_service ) ) {
//						$tf_due_amount = ( $price_total + $airport_service_arr['price'] ) - $deposit_amount;
//					} else {
//						$tf_due_amount = $price_total - $deposit_amount;
//					}
					$tf_due_amount = $price_total - $deposit_amount;
				}
			}

			$hotel_guest_info_fields = ! empty( tfopt( 'hotel_guest_info_fields' ) ) ? tf_data_types( tfopt( 'hotel_guest_info_fields' ) ) : '';

			$response['guest_info']            = '';
			$response['hotel_booking_summery'] = '';
			for ( $guest_in = 1; $guest_in <= $total_people; $guest_in ++ ) {
				$response['guest_info'] .= '<div class="tf-single-tour-traveller tf-single-travel">
                <h4>' . sprintf( __( 'Guest ', 'tourfic' ) ) . $guest_in . '</h4>
                <div class="traveller-info">';
				if ( empty( $hotel_guest_info_fields ) ) {
					$response['guest_info'] .= '<div class="traveller-single-info">
                        <label for="tf_full_name' . $guest_in . '">' . sprintf( __( 'Full Name', 'tourfic' ) ) . '</label>
                        <input type="text" name="guest[' . $guest_in . '][tf_full_name]" id="tf_full_name' . $guest_in . '" data-required="1" />
                        <div class="error-text" data-error-for="tf_full_name' . $guest_in . '"></div>
                    </div>
                    <div class="traveller-single-info">
                        <label for="tf_dob' . $guest_in . '">' . sprintf( __( 'Date of birth', 'tourfic' ) ) . '</label>
                        <input type="date" name="guest[' . $guest_in . '][tf_dob]" id="tf_dob' . $guest_in . '" data-required="1" />
                        <div class="error-text" data-error-for="tf_dob' . $guest_in . '"></div>
                    </div>
                    <div class="traveller-single-info">
                        <label for="tf_nid' . $guest_in . '">' . sprintf( __( 'NID', 'tourfic' ) ) . '</label>
                        <input type="text" name="guest[' . $guest_in . '][tf_nid]" id="tf_nid' . $guest_in . '" data-required="1" />
                        <div class="error-text" data-error-for="tf_nid' . $guest_in . '"></div>
                    </div>
                    ';
				} else {
					foreach ( $hotel_guest_info_fields as $field ) {
						if ( "text" == $field['reg-fields-type'] || "email" == $field['reg-fields-type'] || "date" == $field['reg-fields-type'] ) {
							$response['guest_info'] .= '
                            <div class="traveller-single-info">
                                <label for="' . $field['reg-field-name'] . $guest_in . '">' . sprintf( __( '%s', 'tourfic' ), $field['reg-field-label'] ) . '</label>
                                <input type="' . $field['reg-fields-type'] . '" name="guest[' . $guest_in . '][' . $field['reg-field-name'] . ']" data-required="' . $field['reg-field-required'] . '" id="' . $field['reg-field-name'] . $guest_in . '" />
                                <div class="error-text" data-error-for="' . $field['reg-field-name'] . $guest_in . '"></div>
                            </div>';
						}
						if ( "select" == $field['reg-fields-type'] && ! empty( $field['reg-options'] ) ) {
							$response['guest_info'] .= '
                            <div class="traveller-single-info">
                                <label for="' . $field['reg-field-name'] . $guest_in . '">' . sprintf( __( '%s', 'tourfic' ), $field['reg-field-label'] ) . '</label>
                                <select id="' . $field['reg-field-name'] . $guest_in . '" name="guest[' . $guest_in . '][' . $field['reg-field-name'] . ']" data-required="' . $field['reg-field-required'] . '"><option value="">' . sprintf( __( 'Select One', 'tourfic' ) ) . '</option>';
							foreach ( $field['reg-options'] as $sfield ) {
								if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) {
									$response['guest_info'] .= '<option value="' . $sfield['option-value'] . '">' . $sfield['option-label'] . '</option>';
								}
							}
							$response['guest_info'] .= '</select>
                                <div class="error-text" data-error-for="' . $field['reg-field-name'] . $guest_in . '"></div>
                            </div>';
						}
						if ( ( "checkbox" == $field['reg-fields-type'] || "radio" == $field['reg-fields-type'] ) && ! empty( $field['reg-options'] ) ) {
							$response['guest_info'] .= '
                            <div class="traveller-single-info">
                            <label for="' . $field['reg-field-name'] . $guest_in . '">' . sprintf( __( '%s', 'tourfic' ), $field['reg-field-label'] ) . '</label>
                            ';
							foreach ( $field['reg-options'] as $sfield ) {
								if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) {
									$response['guest_info'] .= '
                                        <div class="tf-single-checkbox">
                                        <input type="' . esc_attr( $field['reg-fields-type'] ) . '" name="guest[' . $guest_in . '][' . $field['reg-field-name'] . '][]" id="' . $sfield['option-value'] . $guest_in . '" value="' . $sfield['option-value'] . '" data-required="' . $field['reg-field-required'] . '" />
                                        <label for="' . $sfield['option-value'] . $guest_in . '">' . sprintf( __( '%s', 'tourfic' ), $sfield['option-label'] ) . '</label></div>';
								}
							}
							$response['guest_info'] .= '
                            <div class="error-text" data-error-for="' . $field['reg-field-name'] . $guest_in . '"></div>
                            </div>';
						}
					}
				}

				$response['guest_info'] .= '</div>
            </div>';
				$date_format_for_users  = ! empty( tfopt( "tf-date-format-for-users" ) ) ? tfopt( "tf-date-format-for-users" ) : "Y/m/d";

			}

			$response['hotel_booking_summery'] .= '<h6>' . __( 'From ', 'tourfic' ) . date_i18n( $date_format_for_users, strtotime( $check_in ) ) . __( ' to ', 'tourfic' ) . date_i18n( $date_format_for_users, strtotime( $check_out ) ) . '</h6>
        <table class="table" style="width: 100%">
            <thead>
                <tr>
                    <th align="left">' . sprintf( __( 'Guest', 'tourfic' ) ) . '</th>
                    <th align="right">' . sprintf( __( 'Price', 'tourfic' ) ) . '</th>
                </tr>
            </thead>
            <tbody>';

			if ( ! empty( $room_selected ) ) {
				$response['hotel_booking_summery'] .= '<tr>
                    <td align="left">' . sprintf( __( '%1$s Room × %2$s Night', 'tourfic' ), $room_selected, $day_difference ) . '</td>
                    <td align="right">' . wc_price( $price_total ) . '</td>
                </tr>';
			}

			if ( ! empty( $airport_service_arr['title'] ) ) {
				$response['hotel_booking_summery'] .= '<tr>
						<td align="left">' . esc_html( $airport_service_arr['label'] ) . '</td>
						<td align="right">' . wc_price( $airport_service_arr['price'] ) . '</td>
					</tr>';
			}
			if ( ! empty( $tf_due_amount ) ) {
				$response['hotel_booking_summery'] .= '<tr>
                    <td align="left">' . sprintf( __( 'Due', 'tourfic' ) ) . '</td>
                    <td align="right">' . wc_price( $tf_due_amount ) . '</td>
                </tr>';
			}

			$response['hotel_booking_summery'] .= '</tbody>
            <tfoot>
                <tr>
                    <th align="left">' . sprintf( __( 'Total', 'tourfic' ) ) . '</th>
                    <th align="right">' . wc_price( $price_total + $airport_service_arr['price'] ) . '</th>
                </tr>
            </tfoot>
        </table>';

		} else {
			$response['status'] = 'error';
		}

		// Json Response
		echo wp_json_encode( $response );

		die();
	}
}

/*
 * Hotel booking popup
 * @since 2.10.3
 * @author Foysal
 */
if ( ! function_exists( 'tf_hotel_without_booking_popup' ) ) {
	function tf_hotel_without_booking_popup( $post_id, $room_index, $adult, $child ) {

		$meta                     = get_post_meta( $post_id, 'tf_hotels_opt', true );
		$rooms                    = ! empty( $meta['room'] ) ? $meta['room'] : '';
		$enable_airport_service   = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';
		$airport_service_type     = ! empty( $meta['airport_service_type'] ) ? $meta['airport_service_type'] : '';
		$room_book_by             = ! empty( $rooms[ $room_index ]['booking-by'] ) ? $rooms[ $room_index ]['booking-by'] : 1;
		$room_book_url            = ! empty( $rooms[ $room_index ]['booking-url'] ) ? $rooms[ $room_index ]['booking-url'] : '';
		$airport_service_type     = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $enable_airport_service ) && ! empty( $airport_service_type ) ? $airport_service_type : null;
		$enable_guest_info_global = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( tfopt( 'enable_guest_info' ) ) ? tfopt( 'enable_guest_info' ) : '1';
		$enable_guest_info        = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $rooms[ $room_index ]['enable_guest_info'] ) ? $rooms[ $room_index ]['enable_guest_info'] : $enable_guest_info_global;
		$hotel_guest_details_text = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( tfopt( 'hotel_guest_details_text' ) ) ? tfopt( 'hotel_guest_details_text' ) : '';
		?>
        <!-- Loader Image -->
        <div id="tour_room_details_loader">
            <div id="tour-room-details-loader-img">
                <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="Loader">
            </div>
        </div>
        <div class="tf-withoutpayment-booking-confirm">
            <div class="tf-confirm-popup">
                <div class="tf-booking-times">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
                        <path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
                        <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
                        </svg>
                    </span>
                </div>
                <img src="<?php echo TF_ASSETS_APP_URL ?>images/thank-you.gif" alt="Thank You">
                <h2>
					<?php
					$booking_confirmation_msg = ! empty( tfopt( 'booking-confirmation-msg' ) ) ? tfopt( 'booking-confirmation-msg' ) : 'Booked Successfully';
					echo $booking_confirmation_msg;
					?>
                </h2>
            </div>
        </div>
        <div class="tf-withoutpayment-booking">
            <div class="tf-withoutpayment-popup">
                <div class="tf-booking-tabs">
                    <div class="tf-booking-tab-menu">
                        <ul>
							<?php if ( $airport_service_type && ( $room_book_by != 2 || empty( $room_book_url ) ) ) { ?>
                                <li class="tf-booking-step tf-booking-step-1 active">
                                    <i class="ri-price-tag-3-line"></i> <?php echo __( "Airport Service", "tourfic" ); ?>
                                </li>
							<?php }
							if ( $enable_guest_info ) {
								?>
                                <li class="tf-booking-step tf-booking-step-2 <?php echo empty( $airport_service_type ) ? esc_attr( 'active' ) : ''; ?> ">
                                    <i class="ri-group-line"></i> <?php echo __( "Guest details", "tourfic" ); ?>
                                </li>
							<?php }
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && 3 == $room_book_by ) {
								?>
                                <li class="tf-booking-step tf-booking-step-3 <?php echo empty( $airport_service_type ) && empty( $enable_guest_info ) ? esc_attr( 'active' ) : ''; ?>">
                                    <i class="ri-calendar-check-line"></i> <?php echo __( "Booking Confirmation", "tourfic" ); ?>
                                </li>
							<?php } ?>
                        </ul>
                    </div>
                    <div class="tf-booking-times">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
                            <path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
                            <rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="tf-booking-content-summery">

					<?php
					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $airport_service_type ) { ?>
                        <div class="tf-booking-content show tf-booking-content-1">
                            <div class="tf-hotel-services-text">
                                <h3><?php _e( tfopt( 'hotel_service_popup_title', 'Add Service to your Booking.' ), 'tourfic' ); ?></h3>
                                <p><?php _e( tfopt( 'hotel_service_popup_subtile', 'Select the services you want to add to your booking.' ), 'tourfic' ); ?></p>
                            </div>
                            <div class="tf-booking-content-service">
								<?php if ( ! empty( $airport_service_type ) ) { ?>
									<?php foreach ( $airport_service_type as $key => $single_service_type ) {
										$airport_service = tf_hotel_airport_service_title_price( $post_id, $adult, $child, $single_service_type );
										?>
                                        <div class="tf-single-hotel-service tour-extra-single">
                                            <label for="service-<?php echo esc_attr( $key ); ?>">
                                                <div class="tf-service-radio">
                                                    <input type="radio" value="<?php echo esc_attr( $single_service_type ); ?>" id="service-<?php echo esc_attr( $key ); ?>" name="tf_airport_service">
                                                    <span class="checkmark"></span>
                                                </div>
                                                <div class="tf-service-content">
                                                    <h5>
														<?php
														if ( "pickup" == $single_service_type ) {
															_e( 'Pickup Service', 'tourfic' );
														}
														if ( "dropoff" == $single_service_type ) {
															_e( 'Drop-off Service', 'tourfic' );
														}
														if ( "both" == $single_service_type ) {
															_e( 'Pickup & Drop-off Service', 'tourfic' );
														}
														?>
                                                    </h5>
                                                    <p><?php echo $airport_service['title']; ?> = <?php echo wc_price( $airport_service['price'] ); ?></p>
                                                </div>
                                            </label>
                                        </div>
									<?php } ?>
								<?php } ?>

                            </div>
                        </div>
					<?php }
					if ( $enable_guest_info ) {
						?>
                        <!-- Popup Traveler Info -->
                        <div class="tf-booking-content tf-booking-content-2 <?php echo empty( $airport_service_type ) ? esc_attr( 'show' ) : ''; ?>">
                            <p><?php echo __( $hotel_guest_details_text, "tourfic" ); ?></p>
                            <div class="tf-booking-content-traveller">
                                <div class="tf-traveller-info-box"></div>
                            </div>
                        </div>
					<?php }
					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && 3 == $room_book_by ) {
						?>
                        <!-- Popup Booking Confirmation -->
                        <div class="tf-booking-content tf-booking-content-3 <?php echo empty( $airport_service_type ) && empty( $enable_guest_info ) ? esc_attr( 'show' ) : ''; ?>">
                            <p><?php _e( $hotel_guest_details_text, "tourfic" ); ?></p>
                            <div class="tf-booking-content-traveller">
                                <div class="tf-single-tour-traveller">
                                    <h4><?php echo __( "Billing details", "tourfic" ); ?></h4>
                                    <div class="traveller-info billing-details">
										<?php
										$confirm_book_fields = ! empty( tfopt( 'book-confirm-field' ) ) ? tf_data_types( tfopt( 'book-confirm-field' ) ) : '';
										if ( empty( $confirm_book_fields ) ) {
											?>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_first_name"><?php echo __( "First Name", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_first_name]" id="tf_first_name" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_first_name"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_last_name"><?php echo __( "Last Name", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_last_name]" id="tf_last_name" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_last_name"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_email"><?php echo __( "Email", "tourfic" ); ?></label>
                                                <input type="email" name="booking_confirm[tf_email]" id="tf_email" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_email"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_phone"><?php echo __( "Phone", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_phone]" id="tf_phone" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_phone"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_country"><?php echo __( "Country", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_country]" id="tf_country" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_country"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_street_address"><?php echo __( "Street address", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_street_address]" id="tf_street_address" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_street_address"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_town_city"><?php echo __( "Town / City", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_town_city]" id="tf_town_city" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_town_city"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_state_country"><?php echo __( "State / County", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_state_country]" id="tf_state_country" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_state_country"></div>
                                            </div>
                                            <div class="traveller-single-info tf-confirm-fields">
                                                <label for="tf_postcode"><?php echo __( "Postcode / ZIP", "tourfic" ); ?></label>
                                                <input type="text" name="booking_confirm[tf_postcode]" id="tf_postcode" data-required="1"/>
                                                <div class="error-text" data-error-for="tf_postcode"></div>
                                            </div>
										<?php } else {
											foreach ( $confirm_book_fields as $field ) {
												if ( "text" == $field['reg-fields-type'] || "email" == $field['reg-fields-type'] || "date" == $field['reg-fields-type'] ) { ?>
                                                    <div class="traveller-single-info tf-confirm-fields">
                                                        <label for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"><?php echo esc_html( $field['reg-field-label'] ); ?></label>
                                                        <input type="<?php echo esc_attr( $field['reg-fields-type'] ); ?>" name="booking_confirm[<?php echo esc_attr( $field['reg-field-name'] ); ?>]"
                                                               id="<?php echo esc_attr( $field['reg-field-name'] ); ?>" data-required="<?php echo $field['reg-field-required']; ?>"/>
                                                        <div class="error-text" data-error-for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"></div>
                                                    </div>
												<?php }
												if ( "select" == $field['reg-fields-type'] && ! empty( $field['reg-options'] ) ) { ?>
                                                    <div class="traveller-single-info tf-confirm-fields">
                                                        <label for="<?php echo esc_attr( $field['reg-field-name'] ); ?>">
															<?php echo esc_html( $field['reg-field-label'] ); ?>
                                                        </label>
                                                        <select name="booking_confirm[<?php echo esc_attr( $field['reg-field-name'] ); ?>]" id="<?php echo esc_attr( $field['reg-field-name'] ); ?>"
                                                                data-required="<?php echo $field['reg-field-required']; ?>">
                                                            <option value="">
																<?php echo sprintf( __( 'Select One', 'tourfic' ) ); ?>
                                                            </option>
															<?php
															foreach ( $field['reg-options'] as $sfield ) {
																if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) { ?>
                                                                    <option value="<?php echo esc_attr( $sfield['option-value'] ); ?>"><?php echo esc_html( $sfield['option-label'] ); ?></option>
																<?php }
															} ?>
                                                        </select>
                                                        <div class="error-text" data-error-for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"></div>
                                                    </div>
												<?php }
												if ( ( "checkbox" == $field['reg-fields-type'] || "radio" == $field['reg-fields-type'] ) && ! empty( $field['reg-options'] ) ) { ?>
                                                    <div class="traveller-single-info tf-confirm-fields">
                                                        <label for="<?php echo esc_attr( $field['reg-field-name'] ); ?>">
															<?php echo esc_html( $field['reg-field-label'] ); ?>
                                                        </label>
														<?php
														foreach ( $field['reg-options'] as $sfield ) {
															if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) { ?>
                                                                <div class="tf-single-checkbox">
                                                                    <input type="<?php echo esc_attr( $field['reg-fields-type'] ); ?>" name="booking_confirm[<?php echo esc_attr( $field['reg-field-name'] ); ?>][]"
                                                                           id="<?php echo esc_attr( $sfield['option-value'] ); ?>" value="<?php echo esc_html( $sfield['option-value'] ); ?>"
                                                                           data-required="<?php echo $field['reg-field-required']; ?>"/>
                                                                    <label for="<?php echo esc_attr( $sfield['option-value'] ); ?>">
																		<?php echo sprintf( __( '%s', 'tourfic' ), $sfield['option-label'] ); ?>
                                                                    </label>
                                                                </div>
															<?php }
														} ?>
                                                        <div class="error-text" data-error-for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"></div>
                                                    </div>
												<?php }
											}
										} ?>
                                    </div>
                                </div>
                            </div>
                        </div>
					<?php } ?>

                    <!-- Popup Booking Summery -->
                    <div class="tf-booking-summery" style="<?php echo empty( $airport_service_type ) && empty( $enable_guest_info ) && 3 != $room_book_by ? esc_attr( "width: 100%;" ) : ''; ?>">
                        <div class="tf-booking-fixed-summery">
                            <h5><?php echo __( "Booking summery", "tourfic" ); ?></h5>
                            <h4><?php echo get_the_title( $post_id ); ?></h4>
                        </div>
                        <div class="tf-booking-traveller-info">

                        </div>
                    </div>
                </div>

                <!-- Popup Footer Control & Partial Payment -->
                <div class="tf-booking-pagination">
					<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['allow_deposit'] ) && $meta['allow_deposit'] == '1' && ! empty( $meta['deposit_amount'] ) && 3 != $room_book_by ) {
						$tf_deposit_amount              = array(
							"{amount}" => $meta['deposit_type'] == 'fixed' ? wc_price( $meta['deposit_amount'] ) : $meta['deposit_amount'] . '%'
						);
						$tf_partial_payment_label       = ! empty( tfopt( "deposit-title" ) ) ? tfopt( "deposit-title" ) : 'Pertial payment of {amount} on total';
						$tf_partial_payment_description = ! empty( tfopt( "deposit-subtitle" ) ) ? tfopt( "deposit-subtitle" ) : 'You can Partial Payment amount for booking the tour. After booking the tour, you can pay the rest amount after the tour is completed.';
						?>
                        <div class="tf-diposit-switcher">
                            <label class="switch">
                                <input type="checkbox" name="deposit" class="diposit-status-switcher">
                                <span class="switcher round"></span>
                            </label>
                            <div class="tooltip-box">
                                <h4><?php echo __( partial_payment_tag_replacement( $tf_partial_payment_label, $tf_deposit_amount ), 'tourfic' ) ?></h4>
                                <div class="tf-info-btn">
                                    <i class="fa fa-circle-exclamation tooltip-title-box" style="padding-left: 5px; padding-top: 5px" title=""></i>
                                    <div class="tf-tooltip"><?php echo __( $tf_partial_payment_description ) ?></div>
                                </div>
                            </div>
                        </div>
					<?php } ?>
					<?php if ( empty( $airport_service_type ) && 3 != $room_book_by && empty( $enable_guest_info ) ) { ?>
                        <div class="tf-control-pagination show">
                            <button type="submit"><?php echo __( "Continue", "tourfic" ); ?></button>
                        </div>
						<?php
					}
					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() &&  $airport_service_type ) { ?>
                        <div class="tf-control-pagination show tf-pagination-content-1">
							<?php
							if ( 3 != $room_book_by && empty( $enable_guest_info ) ) { ?>
                                <button type="submit"><?php echo __( "Continue", "tourfic" ); ?></button>
							<?php } else { ?>
                                <a href="#" class="tf-next-control tf-tabs-control"
                                   data-step="<?php echo 3 == $room_book_by && empty( $enable_guest_info ) ? esc_attr( "3" ) : esc_attr( "2" ); ?>"><?php echo __( "Continue", "tourfic" ); ?></a>
							<?php } ?>
                        </div>
					<?php }
					if ( $enable_guest_info ) { ?>

                        <!-- Popup Traveler Info -->
                        <div class="tf-control-pagination tf-pagination-content-2 <?php echo empty( $airport_service_type ) ? esc_attr( 'show' ) : ''; ?>">
							<?php
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $airport_service_type ) { ?>
                                <a href="#" class="tf-back-control tf-step-back" data-step="1"><i class="fa fa-angle-left"></i><?php echo __( "Back", "tourfic" ); ?></a>
							<?php }
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && 3 == $room_book_by ) {
								?>
                                <a href="#" class="tf-next-control tf-tabs-control tf-traveller-error" data-step="3"><?php echo __( "Continue", "tourfic" ); ?></a>
							<?php } else { ?>
                                <button type="submit" class="tf-traveller-error"><?php echo __( "Continue", "tourfic" ); ?></button>
							<?php } ?>
                        </div>
					<?php }
					if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && 3 == $room_book_by ) {
						?>

                        <!-- Popup Booking Confirmation -->
                        <div class="tf-control-pagination tf-pagination-content-3 <?php echo empty( $airport_service_type ) && empty( $enable_guest_info ) ? esc_attr( 'show' ) : ''; ?>">
							<?php
							if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( $airport_service_type || $enable_guest_info ) ) { ?>
                                <a href="#" class="tf-back-control tf-step-back" data-step="2"><i class="fa fa-angle-left"></i><?php echo __( "Back", "tourfic" ); ?></a>
							<?php } ?>
                            <button type="submit" class="tf-book-confirm-error"><?php echo __( "Continue", "tourfic" ); ?></button>
                        </div>
					<?php } ?>
                </div>
            </div>
        </div>
		<?php
	}
}

/*
 * Hotel Airport service type title and price
 * @params $post_id, $adult, $child, $meta
 */
if ( ! function_exists( 'tf_hotel_airport_service_title_price' ) ) {
	function tf_hotel_airport_service_title_price( $post_id, $adult, $child, $airport_service ) {
		$meta = get_post_meta( $post_id, 'tf_hotels_opt', true );

		$airport_service_total = 0;
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['airport_service'] ) && 1 == $meta['airport_service'] ) {
			if ( "pickup" == $airport_service ) {
				$airport_pickup_price = ! empty( $meta['airport_pickup_price'] ) ? $meta['airport_pickup_price'] : '';
				if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
					$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $airport_pickup_price );
					$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
				}
				if ( "per_person" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$service_adult_fee           = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee           = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$airport_service_price_total = ( $adult * $service_adult_fee ) + ( $child * $service_child_fee );
					$airport_service_total       += $airport_service_price_total;

					if ( $child != 0 ) {
						$airport_service_arr = array(
							'title' => sprintf( __( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s )', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								$child,
								strip_tags( wc_price( $service_child_fee ) )
							),
							'price' => $airport_service_price_total
						);
					} else {
						$airport_service_arr = array(
							'title' => sprintf( __( 'Adult ( %1$s × %2$s )', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) )
							),
							'price' => $airport_service_price_total
						);
					}
				}
				if ( "fixed" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$airport_service_price_total = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$airport_service_total       += $airport_service_price_total;

					$airport_service_arr = array(
						'title' => __( 'Fixed Price', 'tourfic' ),
						'price' => $airport_service_price_total
					);
				}
				if ( "free" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$airport_service_arr = array(
						'title' => __( 'Free', 'tourfic' ),
						'price' => 0
					);
				}

				$airport_service_arr['label'] = __( 'Pickup Service', 'tourfic' );
			}
			if ( "dropoff" == $airport_service ) {
				$airport_pickup_price = ! empty( $meta['airport_dropoff_price'] ) ? $meta['airport_dropoff_price'] : '';
				if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
					$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $airport_pickup_price );
					$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
				}

				if ( "per_person" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$service_adult_fee           = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee           = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$airport_service_price_total = ( $adult * $service_adult_fee ) + ( $child * $service_child_fee );

					$airport_service_total += $airport_service_price_total;
					if ( $child != 0 ) {
						$airport_service_arr = array(
							'title' => sprintf( __( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s )', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								$child,
								strip_tags( wc_price( $service_child_fee ) )
							),
							'price' => $airport_service_price_total
						);
					} else {
						$airport_service_arr = array(
							'title' => sprintf( __( 'Adult ( %1$s × %2$s )', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) )
							),
							'price' => $airport_service_price_total
						);
					}
				}
				if ( "fixed" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$airport_service_price_total = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$airport_service_total       += $airport_service_price_total;

					$airport_service_arr = array(
						'title' => __( 'Fixed Price', 'tourfic' ),
						'price' => $airport_service_price_total
					);
				}
				if ( "free" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$airport_service_arr = array(
						'title' => __( 'Free', 'tourfic' ),
						'price' => 0
					);
				}

				$airport_service_arr['label'] = __( 'Drop-off Service', 'tourfic' );
			}
			if ( "both" == $airport_service ) {
				$airport_pickup_price = ! empty( $meta['airport_pickup_dropoff_price'] ) ? $meta['airport_pickup_dropoff_price'] : '';
				if ( ! empty( $airport_pickup_price ) && gettype( $airport_pickup_price ) == "string" ) {
					$tf_hotel_airport_pickup_price_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
						return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
					}, $airport_pickup_price );
					$airport_pickup_price                = unserialize( $tf_hotel_airport_pickup_price_value );
				}

				if ( "per_person" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$service_adult_fee           = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee           = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$airport_service_price_total = ( $adult * $service_adult_fee ) + ( $child * $service_child_fee );
					$airport_service_total       += $airport_service_price_total;

					if ( $child != 0 ) {
						$airport_service_arr = array(
							'title' => sprintf( __( 'Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s )', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) ),
								$child,
								strip_tags( wc_price( $service_child_fee ) )
							),
							'price' => $airport_service_price_total
						);
					} else {
						$airport_service_arr = array(
							'title' => sprintf( __( 'Adult ( %1$s × %2$s )', 'tourfic' ),
								$adult,
								strip_tags( wc_price( $service_adult_fee ) )
							),
							'price' => $airport_service_price_total
						);
					}
				}
				if ( "fixed" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$airport_service_price_total = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					$airport_service_total       += $airport_service_price_total;

					$airport_service_arr = array(
						'title' => __( 'Fixed Price', 'tourfic' ),
						'price' => $airport_service_price_total
					);
				}
				if ( "free" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$airport_service_arr = array(
						'title' => __( 'Free', 'tourfic' ),
						'price' => 0
					);
				}

				$airport_service_arr['label'] = __( 'Pickup & Drop-off Service', 'tourfic' );
			}
		}

		return $airport_service_arr;
	}
}
