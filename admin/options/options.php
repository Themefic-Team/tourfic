<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'CSF' ) ) {

    // Global Settings
    if ( !is_plugin_active('tourfic-pro/tourfic-pro.php') ) {
        require_once TF_OPTIONS_PATH . 'global/settings.php';
    }
    
    /**
     * Post Type: Tour
     */
    // Single Tour Metabox
    if ( is_plugin_active('tourfic-pro/tourfic-pro.php') && defined( 'TF_PRO' )) {
        require_once TF_PRO_OPTIONS_PATH . 'tour/single_tour.php';
    } else {
        require_once TF_OPTIONS_PATH . 'tour/single_tour.php';
    }

    // Taxonomy: Tour Feature
    if ( !is_plugin_active('tourfic-pro/tourfic-pro.php') ) {
        require_once TF_OPTIONS_PATH . 'tour/taxonomy_tour-features.php';
    }
}
?>