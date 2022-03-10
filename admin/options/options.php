<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'CSF' ) ) {

    // Global Settings
    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        if ( file_exists( TF_PRO_OPTIONS_PATH . 'global/settings.php' ) ) {
            require_once TF_PRO_OPTIONS_PATH . 'global/settings.php';
        } else {
            tf_file_missing(TF_PRO_OPTIONS_PATH . 'global/settings.php');
        }
    } else {
        if ( file_exists( TF_OPTIONS_PATH . 'global/settings.php' ) ) {
            require_once TF_OPTIONS_PATH . 'global/settings.php';
        } else {
            tf_file_missing(TF_OPTIONS_PATH . 'global/settings.php');
        }
    }
    
    /**
     * Post Type: tf_tours
     */
    // Single Tour Metabox
    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        if ( file_exists( TF_PRO_OPTIONS_PATH . 'tour/single-tour.php' ) ) {
            require_once TF_PRO_OPTIONS_PATH . 'tour/single-tour.php';
        } else {
            tf_file_missing(TF_PRO_OPTIONS_PATH . 'tour/single-tour.php');
        }
    } else {
        if ( file_exists( TF_OPTIONS_PATH . 'tour/single-tour.php' ) ) {
            require_once TF_OPTIONS_PATH . 'tour/single-tour.php';
        } else {
            tf_file_missing(TF_OPTIONS_PATH . 'tour/single-tour.php');
        }
    }

    // Taxonomy: Tour Feature
    if ( file_exists( TF_OPTIONS_PATH . 'tour/taxonomy-tour_destination.php' ) ) {
        require_once TF_OPTIONS_PATH . 'tour/taxonomy-tour_destination.php';
    } else {
        tf_file_missing(TF_OPTIONS_PATH . 'tour/taxonomy-tour_destination.php');
    }

    /**
     * Post Type: tf_hotel
     */
    // Single Tour Metabox
    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        if ( file_exists( TF_PRO_OPTIONS_PATH . 'hotel/single-hotel.php' ) ) {
            require_once TF_PRO_OPTIONS_PATH . 'hotel/single-hotel.php';
        } else {
            tf_file_missing(TF_PRO_OPTIONS_PATH . 'hotel/single-hotel.php');
        }
    } else {
        if ( file_exists( TF_OPTIONS_PATH . 'hotel/single-hotel.php' ) ) {
            require_once TF_OPTIONS_PATH . 'hotel/single-hotel.php';
        } else {
            tf_file_missing(TF_OPTIONS_PATH . 'hotel/single-hotel.php');
        }
    }

    /**
     * Taxonomy: hotel_feature
     */
    if ( file_exists( TF_OPTIONS_PATH . 'hotel/taxonomy-hotel_feature.php' ) ) {
        require_once TF_OPTIONS_PATH . 'hotel/taxonomy-hotel_feature.php';
    } else {
        tf_file_missing(TF_OPTIONS_PATH . 'hotel/taxonomy-hotel_feature.php');
    }

    /**
     * Taxonomy: hotel_location
     */
    if ( file_exists( TF_OPTIONS_PATH . 'hotel/taxonomy-hotel_location.php' ) ) {
        require_once TF_OPTIONS_PATH . 'hotel/taxonomy-hotel_location.php';
    } else {
        tf_file_missing(TF_OPTIONS_PATH . 'hotel/taxonomy-hotel_location.php');
    }

}

# ================================== #
# Custom Option Fields               #
# ================================== #

/**
 * Custom permalink settings
 * 
 * @since 2.2.0
 */
/**
 * Add settings field
 */
function tf_add_custom_permalink_fields() {
    
    add_settings_section( 'tf_permalink', __('Tourfic Permalinks', 'tourfic'), 'tf_permalink_section_callback', 'permalink' );
    // Tour
    add_settings_field( 'tour_slug', __('Tour slug', 'tourfic'), 'tf_tour_slug_field_callback', 'permalink', 'tf_permalink', array('label_for' => 'tour_slug'));
    // Hotel
    add_settings_field( 'hotel_slug', __('Hotel slug', 'tourfic'), 'tf_hotel_slug_field_callback', 'permalink', 'tf_permalink', array('label_for' => 'hotel_slug'));

}
add_action( 'admin_init', 'tf_add_custom_permalink_fields' );

// Tourfic Permalinks settings section callback function
function tf_permalink_section_callback() {
    _e('If you like, you may enter custom structures for your archive & single URLs here.', 'tourfic');
}

// Tour slug callback
function tf_tour_slug_field_callback() { ?>
    <input name="tour_slug" id="tour_slug" type="text" value="<?php echo get_option( 'tour_slug' ) ? get_option( 'tour_slug' ) : ''; ?>" class="regular-text code">
    <p class="description"><?php printf(__('Leave blank for default value: %1stours%2s', 'tourfic'), '<code>', '</code>'); ?></p>
<?php }
// Hotel slug callback
function tf_hotel_slug_field_callback() { ?>
    <input name="hotel_slug" id="hotel_slug" type="text" value="<?php echo get_option( 'hotel_slug' ) ? get_option( 'hotel_slug' ) : ''; ?>" class="regular-text code">
    <p class="description"><?php printf(__('Leave blank for default value: %1shotels%2s', 'tourfic'), '<code>', '</code>'); ?></p>
<?php }

/**
 * Register settings field
 */
function tf_save_custom_fields(){

    // Tour
    if( isset($_POST['tour_slug']) ){
        update_option( 'tour_slug',  $_POST['tour_slug'] );
    } 
    // Hotel
    if( isset($_POST['hotel_slug']) ){
        update_option( 'hotel_slug',  $_POST['hotel_slug'] );
    }
}
add_action( 'admin_init', 'tf_save_custom_fields' );

?>