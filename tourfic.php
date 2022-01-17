<?php
/**
 * Plugin Name: Tourfic - Travel and Hotel Booking Solution for WooCommerce
 * Plugin URI: https://tourfic.com
 * Github Plugin URI: http://github.com/themefic/tourfic
 * Description: The ultimate WordPress tour management plugin for hotel booking, tour operator and travel agency websites. Manage all your online Travel Booking system along with order system and any payment of WooCommerce. 
 * Author: Themefic
 * Text Domain: tourfic
 * Domain Path: /lang/
 * Author URI: https://themefic.com
 * Tags:
 * Version: 2.0.3
 * WC tested up to: 6.0.0
 */

// don't load directly
defined( 'ABSPATH' ) || exit;

// Define WI_VERSION.
if ( !defined( 'TOURFIC_VERSION' ) ) {
    define( 'TOURFIC_VERSION', '2.0.3' );
}

define( 'TOURFIC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TOURFIC_TEMPLATES_URL', TOURFIC_PLUGIN_URL . 'templates/' );
define( 'TOURFIC_ADMIN_URL', TOURFIC_PLUGIN_URL . 'admin/' );
define( 'TF_ASSETS_URL', TOURFIC_PLUGIN_URL.'assets/' );
// Paths
define( 'TF_PATH', plugin_dir_path( __FILE__ ) );
define( 'TF_ADMIN_PATH', TF_PATH.'admin/' );
define( 'TF_ASSETS_PATH', TF_PATH.'assets/' );

/**
 * Including Plugin file for security
 * Include_once
 *
 * @since 1.0.0
 */
include_once ABSPATH . 'wp-admin/includes/plugin.php';

require_once dirname( __FILE__ ) . '/admin/framework/framework.php';
require_once dirname( __FILE__ ) . '/admin/framework/settings.php';
require_once dirname( __FILE__ ) . '/admin/framework/taxonomy-fields.php';
if ( !is_plugin_active('tourfic-pro/tourfic-pro.php') ) {
    require_once dirname( __FILE__ ) . '/admin/inc/tours/tf-tours-metabox.php';
}
require_once dirname( __FILE__ ) . '/admin/framework/calendar.php';

if ( !function_exists( 'tourfic_opt' ) ) {
    function tourfic_opt( $option = '', $default = null ) {
        $options = get_option( 'tourfic_opt' ); // Attention: Set your unique id of the framework
        return ( isset( $options[$option] ) ) ? $options[$option] : $default;
    }
}

/**
 *    Main Class
 *
 */
if ( !class_exists( 'Tourfic_WordPress_Plugin' ) ):
    class Tourfic_WordPress_Plugin {

        public function __construct() {
            add_action( 'plugins_loaded', [$this, 'load_text_domain'], 10, 2 );

            add_action( 'wp_enqueue_scripts', [$this, 'enqueue_scripts'], 100 );
            add_action( 'admin_enqueue_scripts', [$this, 'admin_scripts'] );
            add_action( 'wp_enqueue_scripts', [$this, 'enqueue_datepicker'] );

            add_filter( 'single_template', [$this, 'tourfic_single_page_template'] );
            add_filter( 'template_include', [$this, 'tourfic_archive_page_template'] );
            add_filter( 'theme_page_templates', [$this, 'page_templates'], 10, 4 );
            add_filter( 'page_template', [$this, 'load_page_templates'] );

            add_filter( 'comments_template', [$this, 'load_comment_template'] );

            // Admin Notice
            add_filter( 'admin_notices', [$this, 'admin_notices'] );

            // Image sizes
            add_filter( 'after_setup_theme', [$this, 'image_sizes'] );

            add_action( 'plugins_loaded', array( $this, 'add_elelmentor_addon' ) );

        }

        public function includes() {

            /**
             *    Font awesome
             */
            require_once dirname( __FILE__ ) . '/admin/font-awesome.php';

            /**
             *    Custom Meta Fields
             */
            require_once dirname( __FILE__ ) . '/admin/tf-admin.php';

            /**
             *    Layouts Function
             */
            require_once dirname( __FILE__ ) . '/inc/layouts.php';

            /**
             *    Post type
             */
            require_once dirname( __FILE__ ) . '/inc/post-type.php';

            /**
             *    Post type
             */
            require_once dirname( __FILE__ ) . '/inc/tourfic-functions.php';

            /**
             *    SVG Icons
             */
            require_once dirname( __FILE__ ) . '/inc/svg-icons.php';

            /**
             *    Shortcodes
             */
            require_once dirname( __FILE__ ) . '/inc/shortcodes.php';

            /**
             *    WooCommerce booking
             */
            require_once dirname( __FILE__ ) . '/inc/booking/tf-woocommerce-hotel-class.php';
            require_once dirname( __FILE__ ) . '/inc/booking/tf-woocommerce-tours-class.php';

            /**
             *    Widgets
             */
            require_once dirname( __FILE__ ) . '/inc/widgets.php';

        }

        /**
         * Loading Text Domain
         *
         */
        public function load_text_domain() {
            $this->includes();
            //Internationalization
            load_plugin_textdomain( 'tourfic', false, TOURFIC_PLUGIN_URL . '/lang/' );

        }

        // Image sizes
        public function image_sizes() {
            add_image_size( 'tf_gallery_thumb', 900, 490, true );
        }

        /**
         *    Enqueue  scripts
         *
         */
        public function enqueue_scripts() {

            $TOURFIC_VERSION = current_time( 'timestamp' );

            wp_enqueue_script( 'fancybox', TF_ASSETS_URL . 'js/jquery.fancybox.min.js', array( 'jquery' ), '3.5.7' );
            wp_enqueue_style( 'fancybox', TF_ASSETS_URL . 'css/jquery.fancybox.min.css', '', '3.5.7' );

            wp_register_style( 'font-awesome-5', plugin_dir_url( __FILE__ ) . 'assets/font-awesome-4.7.0/css/all.min.css' );
            wp_enqueue_style( 'font-awesome-5' );
           
            wp_enqueue_style( 'magnific-popup-css', plugin_dir_url( __FILE__ ) . 'assets/css/magnific-popup.css', null, $TOURFIC_VERSION );

            wp_enqueue_style( 'tourfic-styles', plugin_dir_url( __FILE__ ) . 'assets/css/tourfic-styles.min.css', null, $TOURFIC_VERSION );

            wp_enqueue_style( 'tourfic-autocomplete', plugin_dir_url( __FILE__ ) . 'assets/css/tourfic-autocomplete.css', null, $TOURFIC_VERSION );
            wp_enqueue_style( 'my-style', plugin_dir_url( __FILE__ ) . 'assets/css/my-style.css', null, $TOURFIC_VERSION );
            wp_enqueue_style( 'owl-carousel', plugin_dir_url( __FILE__ ) . 'assets/css/owl.carousel.min.css', null, $TOURFIC_VERSION );
            wp_enqueue_style( 'tf-style', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', null, $TOURFIC_VERSION );
            wp_enqueue_style( 'tf-responsive', plugin_dir_url( __FILE__ ) . 'assets/css/responsive.css', null, $TOURFIC_VERSION );

            wp_enqueue_script( 'slick', plugin_dir_url( __FILE__ ) . 'assets/slick/slick.min.js', array( 'jquery' ), $TOURFIC_VERSION );

            wp_enqueue_script( 'magnific-popup-js', plugin_dir_url( __FILE__ ) . 'assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), $TOURFIC_VERSION );
            wp_enqueue_script( 'owl-carousel', plugin_dir_url( __FILE__ ) . 'assets/js/owl.carousel.min.js', array( 'jquery' ), $TOURFIC_VERSION );

            wp_enqueue_script( 'tourfic-script', plugin_dir_url( __FILE__ ) . 'assets/js/tourfic-script.js', array( 'jquery' ), $TOURFIC_VERSION, true );

            wp_localize_script( 'tourfic-script', 'tf_params',
                array(
                    'nonce'        => wp_create_nonce( 'tf_ajax_nonce' ),
                    'ajax_url'     => admin_url( 'admin-ajax.php' ),
                    'destinations' => $this->get_tourfic_destinations(),
                    'tour_destinations' => $this->get_tours_destinations(),
                )
            );
        }

        /*
         * Get tourfic destinations
         */

        public function get_tourfic_destinations() {

            $destinations = array();

            $destination_terms = get_terms( array(
                'taxonomy'   => 'destination',
                'hide_empty' => false,
            ) );

            foreach ( $destination_terms as $destination_term ) {

                $destinations[] = $destination_term->name;
            }

            return $destinations;

        }
  /*
         * Get tours destinations{taxonomy-tour_destination}
         */

        public function get_tours_destinations() {

            $destinations = array();

            $destination_terms = get_terms( array(
                'taxonomy'   => 'tour_destination',
                'hide_empty' => false,
            ) );

            foreach ( $destination_terms as $destination_term ) {

                $destinations[] = $destination_term->name;
            }

            return $destinations;

        }

        /**
         *    Enqueue  scripts
         */
        public function admin_scripts( $hook ) {
            if ( $hook == "widgets.php" && function_exists( 'is_woocommerce' ) ) {

                $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ?: '.min';

                $assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
                wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' );
                wp_register_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css' );

                wp_enqueue_script( 'select2' );
                wp_enqueue_style( 'select2' );

                $output = "
				(function($) {
		    		'use strict';
		    		jQuery(document).ready(function() { ";

                $output .= "$(document).on('tf_select2 widget-added widget-updated', function() {

		    				jQuery('.tf-select2').each(function(){
		    					if( !$(this).hasClass('select2-hidden-accessible') ){
		    						$(this).select2({ width: '100%' });
		    					}
		    				});

					    });";

                $output .= "
					});
				})(jQuery);";

                wp_add_inline_script( 'select2', $output );

            }
            wp_register_style( 'font-awesome', plugin_dir_url( __FILE__ ) . 'assets/font-awesome-4.7.0/css/font-awesome.min.css' );
            wp_enqueue_style( 'fullcalendar', plugin_dir_url( __FILE__ ) . 'admin/assets/css/fullcalendar/main.min.css' );
            wp_enqueue_script( 'fullcalendar', plugin_dir_url( __FILE__ ) . 'admin/assets/js/fullcalendar/main.js', array( 'jquery' ), false, true );
            wp_enqueue_script( 'fullcalendar-init', plugin_dir_url( __FILE__ ) . 'admin/assets/js/fullcalendar/calendar.js', array( 'jquery', 'fullcalendar' ), false, true );

        }

        /**
         * Load jQuery datepicker.
         *
         */
        public function enqueue_datepicker() {

            wp_enqueue_style( 'daterangepicker', plugin_dir_url( __FILE__ ) . 'assets/daterangepicker/daterangepicker.css', null, TOURFIC_VERSION );

            wp_enqueue_script( 'moment', plugin_dir_url( __FILE__ ) . 'assets/daterangepicker/moment.min.js', array( 'jquery' ), TOURFIC_VERSION, true );
            wp_enqueue_script( 'daterangepicker', plugin_dir_url( __FILE__ ) . 'assets/daterangepicker/daterangepicker.js', array( 'jquery' ), TOURFIC_VERSION, true );

        }

        /**
         * Load elementor.
         *
         */
        public function add_elelmentor_addon() {

            // Check if Elementor installed and activated
            if ( !did_action( 'elementor/loaded' ) ) {
                return;
            }
            // Once we get here, We have passed all validation checks so we can safely include our plugin
            require_once 'inc/elementor-addon/elementor-addon-register.php';

        }

        // Show Page Template
        public function page_templates( $templates, $wp_theme, $post, $post_type ) {
            $templates['tf_search-result'] = 'Tourfic - Search Result';
            return $templates;
        }

        // Load Page Template
        public function load_page_templates( $page_template ) {

            if ( get_page_template_slug() == 'tf_search-result' ) {
                $theme_files = array( 'search-tourfic.php', 'templates/search-tourfic.php' );
                $exists_in_theme = locate_template( $theme_files, false );
                if ( $exists_in_theme != '' ) {
                    return $exists_in_theme;
                } else {
                    return dirname( __FILE__ ) . '/templates/search-tourfic.php';
                }
            }
            return $page_template;
        }

        // Single Template
        public function tourfic_single_page_template( $single_template ) {
            global $post;

            $single_tour_style = tourfic_opt( 'single_tour_style' );

            $st = isset( $single_tour_style ) ? $single_tour_style : 'single-tourfic.php';
            //$s_tours = isset( $single_tour_style ) ? $single_tour_style : 'single-tf_tours.php';

            if ( 'tourfic' === $post->post_type ) {
                $theme_files = array( 'single-tourfic.php', 'templates/single-tourfic.php' );
                $exists_in_theme = locate_template( $theme_files, false );
                if ( $exists_in_theme != '' ) {
                    return $exists_in_theme;
                } else {
                    return dirname( __FILE__ ) . "/templates/{$st}";
                }
            }

            if ( 'tf_tours' === $post->post_type ) {
                $theme_files = array( 'single-tf_tours.php', 'templates/single-tf_tours.php' );
                $exists_in_theme = locate_template( $theme_files, false );
                if ( $exists_in_theme != '' ) {
                    return $exists_in_theme;
                } else {
                    return dirname( __FILE__ ) . "/templates/single-tf_tours.php";
                }
            }
            return $single_template;
        }

        // Archive Template
        public function tourfic_archive_page_template( $template ) {
            if ( is_post_type_archive( 'tourfic' ) ) {

                $theme_files = array( 'archive-tourfic.php', 'templates/archive-tourfic.php' );
                $exists_in_theme = locate_template( $theme_files, false );
                if ( $exists_in_theme != '' ) {
                    return $exists_in_theme;
                } else {
                    return dirname( __FILE__ ) . '/templates/archive-tourfic.php';
                }

            }

            if( is_post_type_archive( 'tf_tours' ) ){
                $theme_files = array( 'archive-tf_tours.php', 'templates/archive-tf_tours.php' );
                $exists_in_theme = locate_template( $theme_files, false );
                if( $exists_in_theme != '' ){
                    return $exists_in_theme;
                }else{
                    return dirname( __FILE__ ) . '/templates/archive-tf_tours.php';
                }
            }
            return $template;
        }

        // Review form load
        public function load_comment_template( $comment_template ) {
            global $post;

            if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
                // leave the standard comments template for standard post types
                return;
            }

            if ( 'tourfic' === $post->post_type || 'tf_tours' === $post->post_type ) {
                $theme_files = array( 'review.php', 'templates/review.php' );
                $exists_in_theme = locate_template( $theme_files, false );
                if ( $exists_in_theme != '' ) {
                    return $exists_in_theme;
                } else {
                    return dirname( __FILE__ ) . '/templates/review.php';
                }
            }

            return $comment_template;

        }

        /**
         * Notice if WooCommerce is inactive
         */
        public function admin_notices() {
            if ( !class_exists( 'WooCommerce' ) ) {?>
			    <div class="notice notice-warning is-dismissible">
			        <p>
			        	<strong><?php esc_html_e( 'Tourfic requires WooCommerce to be activated ', 'tourfic' );?> <a href="<?php echo esc_url( admin_url( '/plugin-install.php?s=slug:woocommerce&tab=search&type=term' ) ); ?>">Install Now</a></strong>
			        </p>
			    </div> <?php
    }
        }

    }
    new Tourfic_WordPress_Plugin;
endif;

/*
 * Asign Destination taxonomy template
 */

add_filter( 'template_include', 'taxonomy_template' );
function taxonomy_template( $template ) {

    if ( is_tax( 'destination' ) ) {
        $template = dirname( __FILE__ ) . '/templates/taxonomy-destination.php';
    }
    if ( is_tax( 'tour_destination' ) ) {
        $template = dirname( __FILE__ ) . '/templates/taxonomy-tour_destination.php';
    }

    return $template;

}
