<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

if ( ! function_exists( 'tf_is_woo_active' ) ) {
	function tf_is_woo_active() {
		return is_plugin_active( 'woocommerce/woocommerce.php' );
	}
}

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
 * WC Product Extend
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php' ) ) {
	function fida() {
		require_once TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php';
	}

	if ( tf_is_woo_active() ) {
		add_action( 'init', 'fida' );
	}
} else {
	tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php' );
}

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

/**
 * WooCommerce Common Functions
 */
if ( tf_is_woo_active() ) {
	if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-common.php' ) ) {
		require_once TF_INC_PATH . 'functions/woocommerce/wc-common.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-common.php' );
	}
}

/**
 * Wishlist Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-wishlist.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-wishlist.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-wishlist.php' );
}

/**
 * Review Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-review.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-review.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-review.php' );
}

/**
 * inquiry Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions_enquiry.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions_enquiry.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions_enquiry.php' );
}

/**
 * Include export import function file
 */
if( file_exists( TF_INC_PATH . 'functions/functions-settings-import-export.php' ) ){
	require_once TF_INC_PATH . 'functions/functions-settings-import-export.php';
}else{
	tf_file_missing( TF_INC_PATH . 'functions/functions-settings-import-export.php' );
}

/**
 * Include Post Duplicator function file
 */
if( file_exists( TF_INC_PATH . 'functions/functions_duplicator.php' ) ){
	require_once TF_INC_PATH . 'functions/functions_duplicator.php';
}else{
	tf_file_missing( TF_INC_PATH . 'functions/functions_duplicator.php' );
}

/**
 * Include Functions Vat
 */
if ( file_exists( TF_INC_PATH . 'functions/functions_vat.php' ) ) {
    require_once TF_INC_PATH . 'functions/functions_vat.php';
} else {
    tf_file_missing( TF_INC_PATH . 'functions/functions_vat.php' );
}

/**
 * Shortcodes
 *
 * @since 1.0
 */
if ( tf_is_woo_active() ) {
	if ( file_exists( TF_INC_PATH . 'functions/shortcodes.php' ) ) {
		require_once TF_INC_PATH . 'functions/shortcodes.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'functions/shortcodes.php' );
	}
}

/**
 * Widgets
 *
 * @since 1.0
 */
if ( tf_is_woo_active() ) {
	if ( file_exists( TF_INC_PATH . 'functions/widgets.php' ) ) {
		require_once TF_INC_PATH . 'functions/widgets.php';
	} else {
		tf_file_missing( TF_INC_PATH . 'functions/widgets.php' );
	}
}

# Google Fonts
if ( file_exists( TF_INC_PATH . 'functions/functions-fonts.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-fonts.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-fonts.php' );
}

add_action( 'plugins_loaded', 'tf_add_elelmentor_addon' );

/**
 * Notice
 *
 * Update
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-notice_update.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-notice_update.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-notice_update.php' );
}























/*
 * Search form tab type check
 * @author: Foysal
 * return: boolean
 */
function tf_is_search_form_tab_type( $type, $type_arr ) {
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
function tf_is_search_form_single_tab( $type_arr ) {
	if ( count( $type_arr ) === 1 && $type_arr[0] !== 'all' ) {
		return true;
	}

	return false;
}

/**
 * Generate custom taxonomies select dropdown
 * @author Abu Hena
 * @since 2.9.4
 */
if ( ! function_exists( 'tf_terms_dropdown' ) ) {
	function tf_terms_dropdown( $term, $attribute, $id, $class, $multiple = false ) {

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
}









/**
 * Hotel gallery video content initialize by this hook
 * can be filtered the video url by "tf_hotel_gallery_video_url" Filter
 * @since 2.9.7
 * @author Abu Hena
 */
if ( ! function_exists( 'tf_hotel_gallery_video' ) ) {
	function tf_hotel_gallery_video( $meta ) {

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
}

if ( ! function_exists( 'tourfic_template_settings' ) ) {
	function tourfic_template_settings() {
		$tf_plugin_installed = get_option( 'tourfic_template_installed' );
		if ( ! empty( $tf_plugin_installed ) ) {
			$template = 'design-1';
		} else {
			$template = 'default';
		}

		return $template;
	}
}




/*
 * Retrive Orders Data
 *
 * @return void
 *
 * @since 2.9.26
 * @author Jahid
 */

if ( ! function_exists( 'tourfic_order_table_data' ) ) {
	function tourfic_order_table_data( $query ) {
		global $wpdb;
		$query_type          = $query['post_type'];
		$query_select        = $query['select'];
		$query_where         = $query['query'];
		$tf_tour_book_orders = $wpdb->get_results( $wpdb->prepare( "SELECT $query_select FROM {$wpdb->prefix}tf_order_data WHERE post_type = %s $query_where", $query_type ), ARRAY_A );

		return $tf_tour_book_orders;
	}
}

/*
 * Affiliate callback function
 */
if ( ! function_exists( 'tf_affiliate_callback' ) ) {
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