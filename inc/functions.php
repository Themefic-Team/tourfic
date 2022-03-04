<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Hotel Functions
 */
require_once TF_INC_PATH . 'functions/functions_hotel.php';

/**
 * Tour Functions
 */
require_once TF_INC_PATH . 'functions/functions_tour.php';

/**
 * Wishlist Functions
 */
require_once TF_INC_PATH . 'functions/functions_wishlist.php';

/**
 * Including CSS & JS
 * 
 * @since 1.0
 */
require_once TF_INC_PATH . 'enqueues.php';

/**
 * Necessary Image Sizes
 * 
 * @since 1.0
 */
if ( !function_exists( 'tf_image_sizes' ) ) {
    function tf_image_sizes() {
        // Hotel gallery, hard crop
        add_image_size( 'tf_gallery_thumb', 900, 490, true );
    }
    add_filter( 'after_setup_theme', 'tf_image_sizes' );
}


/**
 * Assign Single Template
 * 
 * @since 1.0
 */
if ( !function_exists( 'tf_single_page_template' ) ) {
    function tf_single_page_template( $single_template ) {

        global $post;

        $single_tour_style = tfopt( 'single_tour_style' );

        $st = isset( $single_tour_style ) ? $single_tour_style : 'single_hotel.php';
        //$s_tours = isset( $single_tour_style ) ? $single_tour_style : 'single-tf_tours.php';

        if ( 'tf_hotel' === $post->post_type ) {
            $theme_files = array( 'tourfic/single_hotel.php' );
            $exists_in_theme = locate_template( $theme_files, false );
            if ( $exists_in_theme ) {
                return $exists_in_theme;
            } else {
                return TF_TEMPLATE_PATH . "hotel/{$st}";
            }
        }

        /**
         * Tour Single
         * 
         * single_tour.php
         */
        if ( $post->post_type == 'tf_tours' ) {

            $theme_files = array( 'tourfic/single_tour.php' );
            $exists_in_theme = locate_template( $theme_files, false );

            if ( $exists_in_theme ) {
                return $exists_in_theme;
            } else {
                return TF_TEMPLATE_PATH . "tour/single_tour.php";
            }
        }

        return $single_template;
    }
    add_filter( 'single_template', 'tf_single_page_template' );
}

/**
 * Assign Archive Template
 * 
 * @since 1.0
 */
if ( !function_exists( 'tourfic_archive_page_template' ) ) {
    function tourfic_archive_page_template( $template ) {
        if ( is_post_type_archive( 'tf_hotel' ) ) {

            $theme_files = array( 'tourfic/archive_hotels.php' );
            $exists_in_theme = locate_template( $theme_files, false );
            if ( $exists_in_theme ) {
                return $exists_in_theme;
            } else {
                return TF_TEMPLATE_PATH . 'hotel/archive_hotels.php';
            }

        }

        if( is_post_type_archive( 'tf_tours' ) ){
            $theme_files = array( 'tourfic/archive_tours.php' );
            $exists_in_theme = locate_template( $theme_files, false );
            if( $exists_in_theme ){
                return $exists_in_theme;
            }else{
                return TF_TEMPLATE_PATH . 'tour/archive_tours.php';
            }
        }
        return $template;
    }
    add_filter( 'template_include', 'tourfic_archive_page_template' );
}

/**
 * Assign Review Template Part
 * 
 * @since 1.0
 */
if ( !function_exists( 'load_comment_template' ) ) {
    function load_comment_template( $comment_template ) {
        global $post;

        if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
            // leave the standard comments template for standard post types
            return;
        }

        if ( 'tf_hotel' === $post->post_type || 'tf_tours' === $post->post_type ) {
            $theme_files = array( 'tourfic/template-parts/review.php' );
            $exists_in_theme = locate_template( $theme_files, false );
            if ( $exists_in_theme ) {
                return $exists_in_theme;
            } else {
                return TF_TEMPLATE_PATH . 'template-parts/review.php';
            }
        }

        return $comment_template;

    }
    add_filter( 'comments_template', 'load_comment_template' );
}

/**
 * Assign Search Result Template
 * 
 * @since 1.0
 */
// Show Page Template
function page_templates( $templates, $wp_theme, $post, $post_type ) {
    $templates['tf_search-result'] = 'Tourfic - Search Result';
    return $templates;
}
add_filter( 'theme_page_templates', 'page_templates', 10, 4 );

// Load Page Template
function load_page_templates( $page_template ) {

    if ( get_page_template_slug() == 'tf_search-result' ) {
        $theme_files = array( 'search-tourfic.php', 'templates/common/search-results.php' );
        $exists_in_theme = locate_template( $theme_files, false );
        if ( $exists_in_theme ) {
            return $exists_in_theme;
        } else {
            return TF_TEMPLATE_PATH . 'common/search-results.php';
        }
    }
    return $page_template;
}
add_filter( 'page_template', 'load_page_templates' );

/**
 * Load elementor.
 *
 */
function add_elelmentor_addon() {

    // Check if Elementor installed and activated
    if ( !did_action( 'elementor/loaded' ) ) {
        return;
    }
    // Once we get here, We have passed all validation checks so we can safely include our plugin
    require_once TF_INC_PATH . 'elementor-addon/elementor-addon-register.php';

}
add_action( 'plugins_loaded', 'add_elelmentor_addon' );


/*
 * Asign Destination taxonomy template
 */

add_filter( 'template_include', 'taxonomy_template' );
function taxonomy_template( $template ) {

    if ( is_tax( 'hotel_location' ) ) {
        $template = TF_TEMPLATE_PATH . 'hotel/taxonomy_hotel-destinations.php';
    }
    if ( is_tax( 'tour_destination' ) ) {
        $template = TF_TEMPLATE_PATH . 'tour/taxonomy_tour-destinations.php';
    }

    return $template;

}

/**
 * Add tour & hotel capabilities to admin & editor
 * 
 * tf_tours & tf_hotel
 */
function tf_admin_role_caps() {

    if ( get_option( 'tf_admin_caps' ) < 1 ) {
        $admin_role = get_role( 'administrator' );
        $editor_role = get_role( 'editor' );
    
        // Add a new capability.
        $caps = array (
            // Hotels
            'edit_tf_hotel',
            'read_tf_hotel',
            'delete_tf_hotel',
            'edit_tf_hotels',
            'edit_others_tf_hotels',
            'publish_tf_hotels',
            'read_private_tf_hotels',
            'delete_tf_hotels',
            'delete_private_tf_hotels',
            'delete_published_tf_hotels',
            'delete_others_tf_hotels',
            'edit_private_tf_hotels',
            'edit_published_tf_hotels',
            'create_tf_hotels',
            // Tours
            'edit_tf_tours',
            'read_tf_tours',
            'delete_tf_tours',
            'edit_tf_tourss',
            'edit_others_tf_tourss',
            'publish_tf_tourss',
            'read_private_tf_tourss',
            'delete_tf_tourss',
            'delete_private_tf_tourss',
            'delete_published_tf_tourss',
            'delete_others_tf_tourss',
            'edit_private_tf_tourss',
            'edit_published_tf_tourss',
            'create_tf_tourss',
        );
    
        foreach ( $caps as $cap ) {   
            $admin_role->add_cap( $cap );
            $editor_role->add_cap( $cap );
        }

        update_option( 'tf_admin_caps', 1 );
    }
}
add_action( 'admin_init', 'tf_admin_role_caps', 999 );

/**
 * Migrate data from v2.0.4 to v2.1.0
 * 
 * run once
 */
function tf_migrate_data() {
    if ( get_option( 'tf_migrate_data_204' ) < 1 ) {
 
		   global $wpdb;
		   $wpdb->update( $wpdb->posts, ['post_type'=>'tf_hotel'] , ['post_type' => 'tourfic'] );
		   $wpdb->update( $wpdb->term_taxonomy, ['taxonomy'=>'hotel_location'] , ['taxonomy' => 'destination'] );
		   $wpdb->update( $wpdb->term_taxonomy, ['taxonomy'=>'hotel_feature'] , ['taxonomy' => 'tf_filters'] );
 
       update_option( 'tf_migrate_data_204', 1 );
	}
}
add_action( 'init', 'tf_migrate_data' );
?>
