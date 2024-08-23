<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

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

/**
 * Helper Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-helper.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-helper.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-helper.php' );
}

/**
 * Hotel Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-hotel.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-hotel.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-hotel.php' );
}

/**
 * Apartment Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-apartment.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-apartment.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-apartment.php' );
}

/**
 * Tour Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-tour.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-tour.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-tour.php' );
}

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