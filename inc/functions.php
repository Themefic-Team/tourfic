<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Helper Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-helper.php' ) ) {
    require_once TF_INC_PATH . 'functions/functions-helper.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/functions-helper.php');
}

/**
 * Hotel Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-hotel.php' ) ) {
    require_once TF_INC_PATH . 'functions/functions-hotel.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/functions-hotel.php');
}

/**
 * Tour Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-tour.php' ) ) {
    require_once TF_INC_PATH . 'functions/functions-tour.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/functions-tour.php');
}

/**
 * Wishlist Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-wishlist.php' ) ) {
    require_once TF_INC_PATH . 'functions/functions-wishlist.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/functions-wishlist.php');
}

/**
 * Including CSS & JS
 * 
 * @since 1.0
 */
if ( file_exists( TF_INC_PATH . 'enqueues.php' ) ) {
    require_once TF_INC_PATH . 'enqueues.php';
} else {
    tf_file_missing(TF_INC_PATH . 'enqueues.php');
}

/**
 * SVG
 * 
 * @since 1.0
 */
if ( file_exists( TF_INC_PATH . 'functions/svg-icons.php' ) ) {
    require_once TF_INC_PATH . 'functions/svg-icons.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/svg-icons.php');
}

/**
 * Shortcodes
 * 
 * @since 1.0
 */
if ( file_exists( TF_INC_PATH . 'functions/shortcodes.php' ) ) {
    require_once TF_INC_PATH . 'functions/shortcodes.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/shortcodes.php');
}

/**
 * Widgets
 * 
 * @since 1.0
 */
if ( file_exists( TF_INC_PATH . 'functions/widgets.php' ) ) {
    require_once TF_INC_PATH . 'functions/widgets.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/widgets.php');
}

/**
 * Notice
 * 
 * Update
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-notice_update.php' ) ) {
    require_once TF_INC_PATH . 'functions/functions-notice_update.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/functions-notice_update.php');
}

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

        /**
         * Hotel Single
         * 
         * single-hotel.php
         */
        if ( 'tf_hotel' === $post->post_type ) {
            
            $theme_files = array( 'tourfic/hotel/single-hotel.php' );
            $exists_in_theme = locate_template( $theme_files, false );

            if ( $exists_in_theme ) {
                return $exists_in_theme;
            } else {
                return TF_TEMPLATE_PATH . "hotel/single-hotel.php";
            }
        }

        /**
         * Tour Single
         * 
         * single-tour.php
         */
        if ( $post->post_type == 'tf_tours' ) {

            $theme_files = array( 'tourfic/tour/single-tour.php' );
            $exists_in_theme = locate_template( $theme_files, false );

            if ( $exists_in_theme ) {
                return $exists_in_theme;
            } else {
                return TF_TEMPLATE_PATH . "tour/single-tour.php";
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

            $theme_files = array( 'tourfic/hotel/archive-hotels.php' );
            $exists_in_theme = locate_template( $theme_files, false );
            if ( $exists_in_theme ) {
                return $exists_in_theme;
            } else {
                return TF_TEMPLATE_PATH . 'hotel/archive-hotels.php';
            }

        }

        if( is_post_type_archive( 'tf_tours' ) ){
            $theme_files = array( 'tourfic/tour/archive-tours.php' );
            $exists_in_theme = locate_template( $theme_files, false );
            if( $exists_in_theme ){
                return $exists_in_theme;
            }else{
                return TF_TEMPLATE_PATH . 'tour/archive-tours.php';
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
        $theme_files = array( 'tourfic/common/search-results.php' );
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
    if ( file_exists( TF_INC_PATH . 'elementor-addon/elementor-addon-register.php' ) ) {
        require_once TF_INC_PATH . 'elementor-addon/elementor-addon-register.php';
    } else {
        tf_file_missing(TF_INC_PATH . 'elementor-addon/elementor-addon-register.php');
    }

}
add_action( 'plugins_loaded', 'add_elelmentor_addon' );


/*
 * Asign Destination taxonomy template
 */

add_filter( 'template_include', 'taxonomy_template' );
function taxonomy_template( $template ) {

    if ( is_tax( 'hotel_location' ) ) {

        $theme_files = array( 'tourfic/hotel/taxonomy-hotel_locations.php' );
        $exists_in_theme = locate_template( $theme_files, false );

        if ( $exists_in_theme ) {
            $template = $exists_in_theme;
        } else {
            $template = TF_TEMPLATE_PATH . 'hotel/taxonomy-hotel_locations.php';
        }

    }

    if ( is_tax( 'tour_destination' ) ) {

        $theme_files = array( 'tourfic/tour/taxonomy-tour_destinations.php' );
        $exists_in_theme = locate_template( $theme_files, false );

        if ( $exists_in_theme ) {
            $template = $exists_in_theme;
        } else {
            $template = TF_TEMPLATE_PATH . 'tour/taxonomy-tour_destinations.php';
        }

    }

    return $template;

}

/**
 * Add tour & hotel capabilities to admin & editor
 * 
 * tf_tours & tf_hotel
 */
if ( !function_exists('tf_admin_role_caps') ) {
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
}

/**
 * Search Result Sidebar form
 */
function tf_search_result_sidebar_form( $placement = 'single' ) { 

    // Unwanted Slashes Remove
    if ( isset( $_GET ) ) {
        $_GET = array_map( 'stripslashes_deep', $_GET );
    } 

    // Get post type
    $post_type = isset( $_GET['type'] ) ? $_GET['type'] : '';

    if(!empty($post_type)){

        $place_input_id = $post_type == 'tf_hotel' ? 'tf-location' : 'tf-destination';
        $place_placeholder = $post_type == 'tf_hotel' ? __('Enter Location', 'tourfic') : __('Enter Destination', 'tourfic');

        $place_key = 'place';
        $place_value = isset($_GET[$place_key]) ? $_GET[$place_key] : '';

        $taxonomy = $post_type == 'tf_hotel' ? 'hotel_location' : 'tour_destination';
        $place_name = get_term_by( 'slug', $place_value , $taxonomy)->name;

        $adult = isset($_GET['adults']) ? $_GET['adults'] : 0;
        $children = isset($_GET['children']) ? $_GET['children'] : 0;
        $room = isset($_GET['room']) ? $_GET['room'] : 0;
        $date = isset($_GET['check-in-out-date']) ? $_GET['check-in-out-date'] : '';

    }

    ?>
    <!-- Start Booking widget -->
    <form class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
        action="<?php echo tf_booking_search_action(); ?>" id="tf-widget-booking-search">
    
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" id="<?php echo $place_input_id; ?>" required=""  class="" placeholder="<?php echo $place_placeholder; ?>" value="<?php echo $place_name; ?>">
                    <input type="hidden" name="place" id="tf-place" value="<?php echo $place_value; ?>"/>
                </div>
            </label>
        </div>
    
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-user-friends"></i>
                    <select name="adults" id="adults" class="">
                        <option <?php echo 1 == $adult ? 'selected' : null ?> value="1">1 <?php _e('Adult', 'tourfic'); ?></option>
                        <?php foreach (range(2,8) as $value) {
                            $selected = $value == $adult ? 'selected' : null;
                            echo '<option ' . $selected . ' value="' . $value . '">' . $value . ' ' . __("Adults", "tourfic") . '</option>';
                        } ?>                   
                    </select>
                </div>
            </label>
        </div>
    
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-child"></i>
                    <select name="children" id="children" class="">
                        <option value="0">0 <?php _e('Children', 'tourfic'); ?></option>
                        <option <?php echo 1 == $children ? 'selected' : null ?> value="1">1 <?php _e('Children', 'tourfic'); ?></option>
                        <?php foreach (range(2,8) as $value) {
                            $selected = $value == $children ? 'selected' : null;
                            echo '<option ' .$selected. ' value="' .$value. '">' . $value . ' ' . __("Children", "tourfic") . '</option>';
                        } ?> 
                      
                    </select>
                </div>
            </label>
        </div>
    <?php if ($post_type !== 'tf_tours') { ?>
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-couch"></i>
                    <select name="room" id="room" class="">
                        <option <?php echo 1 == $room ? 'selected' : null ?> value="1">1 <?php _e('Room', 'tourfic'); ?></option>
                        <?php foreach (range(2,8) as $value) {
                            $selected = $value == $room ? 'selected' : null;
                            echo '<option ' .$selected. ' value="' .$value. '">' . $value . ' ' . __("Rooms", "tourfic") . '</option>';
                        } ?>                   
                    </select>
                </div>
            </label>
        </div>
    <?php } ?>
        <div class="tf_booking-dates">
            <div class="tf_form-row">
                <label class="tf_label-row">
                    <div class="tf_form-inner">
                        <i class="far fa-calendar-alt"></i>
                        <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                            placeholder="<?php _e('Select Date', 'tourfic'); ?>" required value="<?php echo $date ?>">
                    </div>
                </label>
            </div>
        </div>
    
        <div class="tf_form-row">
            <?php
                    $ptype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();
                ?>
            <input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type" />
            <button class="tf_button tf-submit"
                type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' );?></button>
        </div>
    
    </form>
    
    <script>
    (function($) {
        $(document).ready(function() {
    
            $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
                enableTime: false,
                mode: "range",
            });
    
        });
    })(jQuery);
    </script>
    
    <?php if ( is_active_sidebar( 'tf_search_result' ) ) { ?>
        <div id="tf__booking_sidebar">
            <?php dynamic_sidebar( 'tf_search_result' ); ?>
            <br>
        </div>
    <?php } 

    }

/**
 * Archive Sidebar Search Form
 */
function tf_archive_sidebar_search_form($post_type, $taxonomy='', $taxonomy_name='', $taxonomy_slug='') {
    $place = $post_type == 'tf_hotel' ? 'tf-location' : 'tf-destination';
    $place_text = $post_type == 'tf_hotel' ? __('Enter Location', 'tourfic') : __('Enter Destination', 'tourfic');
    ?>

    <form class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
        action="<?php echo tf_booking_search_action(); ?>">
    
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" required="" id="<?php echo $place; ?>"  class="" placeholder="<?php echo $place_text; ?>" value="<?php echo !empty($taxonomy_name) ? $taxonomy_name : ''; ?>">
                    <input type="hidden" name="place" value="<?php echo !empty($taxonomy_slug) ? $taxonomy_slug : ''; ?>"/>
                </div>
            </label>
        </div>
    
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-user-friends"></i>
                    <select name="adults" id="adults" class="">
                        <?php
                        echo '<option value="1">1 ' .__("Adult", "tourfic"). '</option>';                       
                        foreach (range(2,8) as $value) {
                            echo '<option value="' . $value . '">' . $value . ' ' . __("Adults", "tourfic") . '</option>';
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
                        echo '<option value="0">0 ' .__("Children", "tourfic"). '</option>';                       
                        foreach (range(1,8) as $value) {
                            echo '<option value="' .$value. '">' . $value . ' ' . __("Children", "tourfic") . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </label>
        </div>
    <?php if ($post_type !== 'tf_tours') { ?>
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-couch"></i>
                    <select name="room" id="room" class="">
                        <?php
                        echo '<option value="1">1 ' .__("Room", "tourfic"). '</option>';                       
                        foreach (range(2,8) as $value) {
                            echo '<option value="' . $value . '">' . $value . ' ' . __("Rooms", "tourfic") . '</option>';
                        }
                        ?>                  
                    </select>
                </div>
            </label>
        </div>
    <?php } ?>
        <div class="tf_booking-dates">
            <div class="tf_form-row">
                <label class="tf_label-row">
                    <div class="tf_form-inner">
                        <i class="far fa-calendar-alt"></i>
                        <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                            placeholder="<?php _e('Select Date', 'tourfic'); ?>" required value="">
                    </div>
                </label>
            </div>
        </div>
    
        <div class="tf_form-row">
            <input type="hidden" name="type" value="<?php echo $post_type; ?>" class="tf-post-type" />
            <button class="tf_button tf-submit"
                type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' );?></button>
        </div>
    
    </form>
    
    <script>
    (function($) {
        $(document).ready(function() {
    
            $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
                enableTime: false,
                mode: "range",
            });
    
        });
    })(jQuery);
    </script>
    
    <?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
        <div id="tf__booking_sidebar">
            <?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
            <br>
        </div>
    <?php
    }
}

/**
 * Search Result Sidebar check availability
 * 
 * Hotel Filter by Feature
 * 
 * Ajax function
 */
add_action( 'wp_ajax_nopriv_tf_trigger_filter', 'tf_search_result_ajax_sidebar' );
add_action( 'wp_ajax_tf_trigger_filter', 'tf_search_result_ajax_sidebar' );
function tf_search_result_ajax_sidebar(){

    /**
     * Get form data
     */
    $adults = !empty($_POST['adults']) ? sanitize_text_field($_POST['adults']) : '';
    $child = !empty($_POST['children']) ? sanitize_text_field($_POST['children']) : '';
    $room = !empty($_POST['room']) ? sanitize_text_field($_POST['room']) : '';
    $check_in_out = !empty($_POST['checked']) ? sanitize_text_field($_POST['checked']) : '';

    $relation = tfopt( 'search_relation', 'AND' );
    $filter_relation = tfopt( 'filter_relation', 'OR' );

    $search = ( $_POST['dest'] ) ? sanitize_text_field( $_POST['dest'] ) : null;
    $filters = ( $_POST['filters'] ) ? explode(',', sanitize_text_field( $_POST['filters'] )) : null;
    $features = ( $_POST['features'] ) ? explode(',', sanitize_text_field( $_POST['features'] )) : null;
    $posttype = $_POST['type']  ? sanitize_text_field( $_POST['type'] ): 'tf_hotel';
    // @KK separate texonomy input for filter query
    $place_taxonomy = $posttype == 'tf_tours' ? 'tour_destination' : 'hotel_location';
    $filter_taxonomy = $posttype == 'tf_tours' ? 'null' : 'hotel_feature';
    // @KK take dates for filter query
    $checkin = isset($_POST['checkin']) ? trim($_POST['checkin']) : null;
    $checkout = isset($_POST['checkout']) ? trim($_POST['checkout']) : null;
    // Propertise args
    $args = array(
        'post_type' => $posttype,
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );


    if ( $search ) {

        // 1st search on Destination taxonomy
        $destinations = new WP_Term_Query( array(
            'taxonomy' => $place_taxonomy,
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 0, //can be 1, '1' too
            'hierarchical' => 0, //can be 1, '1' too
            'slug' => sanitize_title($search, ''),
        ) );

        if ( $destinations ) {
            // Define Featured Category IDs first
            $destinations_ids = array();

            // Creating loop to insert IDs to array.
            foreach( $destinations->get_terms() as $cat ) {
                $destinations_ids[] = $cat->term_id;
            }

            $args['tax_query']['relation'] = $relation;
            $args['tax_query'][] = array(
                'taxonomy' => $place_taxonomy,
                'terms'    => $destinations_ids,
            );

        } else {
            $args['s'] = $search;
        }
    }

    if ( $filters ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => $filter_taxonomy,
                'terms'    => $filters,
            );
        } else {
            $args['tax_query']['tf_filters']['relation'] = 'AND';

            foreach ($filters as $key => $term_id) {
                $args['tax_query']['tf_filters'][] = array(
                    'taxonomy' => $filter_taxonomy,
                    'terms'    => array($term_id),
                );
            }

        }

    }
    
    //Query for the features filter of tours
    if ( $features ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'tf_feature',
                'terms'    => $features,
            );
        } else {
            $args['tax_query']['tf_feature']['relation'] = 'AND';

            foreach ($filters as $key => $term_id) {
                $args['tax_query']['tf_feature'][] = array(
                    'taxonomy' => 'tf_feature',
                    'terms'    => array($term_id),
                );
            }

        }

    }
    // @KK Add meta if dates exists and post type is tours
    if ($checkin && $checkout && $posttype == ' tf_tours'){
        $args['tax_query']['relation'] = $relation;
        $args['meta_query'] = array(
                array(
                    'key'     => 'tf_tours_option',
                    'value'   => str_replace('-', '/', $checkin),
                    'compare' => 'LIKE',
                ),
                array(
                    'key'     => 'tf_tours_option',
                    'value'   => str_replace('-', '/', $checkout),
                    'compare' => 'LIKE',
                ),
            );        
    }   
    $loop = new WP_Query( $args ); ?>
    <?php
    if ( $loop->have_posts() ) { 
        while ( $loop->have_posts() ) {
            
            $loop->the_post(); 

            if( $posttype == 'tf_tours' ){
                tf_tour_archive_single_item($adults, $child, $check_in_out);
            }else{
                tf_hotel_archive_single_item($adults, $child, $room, $check_in_out);
            }  
        } 
    } else {
        echo '<div class="tf-nothing-found">Nothing Found!</div>';
    }
    wp_reset_postdata();

    die();
}


/**
 * Migrate data from v2.0.4 to v2.1.0
 * 
 * run once
 */
function tf_migrate_data() {
    if ( get_option( 'tf_migrate_data_204_210' ) < 1 ) {

        global $wpdb;
        $wpdb->update($wpdb->posts, ['post_type' => 'tf_hotel'], ['post_type' => 'tourfic']);
        $wpdb->update($wpdb->term_taxonomy, ['taxonomy' => 'hotel_location'], ['taxonomy' => 'destination']);
        $wpdb->update($wpdb->term_taxonomy, ['taxonomy' => 'hotel_feature'], ['taxonomy' => 'tf_filters']);


        /** Hotels Migrations */
        $hotels = get_posts(['post_type' => 'tf_hotel', 'numberposts' => -1,]);
        foreach ($hotels as $hotel) {
            $old_meta = get_post_meta($hotel->ID);
            if (!empty($old_meta['tf_hotel'])) {
                continue;
            } 
            $new_meta = [];
            if (!empty($old_meta['formatted_location'])) {
                    $new_meta['address'] = join(',', $old_meta['formatted_location']);
            }
            if (!empty($old_meta['tf_gallery_ids'])) {
                    $new_meta['gallery'] = join(',', $old_meta['tf_gallery_ids']);
            }
            if (!empty($old_meta['additional_information'])) {
                $new_meta['highlights'] = $old_meta['additional_information'];
            }
            if (!empty($old_meta['terms_and_conditions'])) {
                    $new_meta['tc'] = join(' ', $old_meta['terms_and_conditions']);
            }

            if (!empty($old_meta['tf_room'])) {
                $rooms =  unserialize($old_meta['tf_room'][0]);
                foreach ($rooms as $room) {
                    $new_meta['room'][] = [
                        "enable" => "1",
                        "title" => $room['name'],
                        "adult" => $room['pax'],
                        "description" => $room['short_desc'],
                        "pricing-by" => "1",
                        "price" => $room['sale_price'] ?? $room['price'],
                    ];
                }
            }

            if (!empty($old_meta['tf_faqs'])) {
                $faqs = unserialize($old_meta['tf_faqs'][0]);
                foreach ($faqs as  $faq) {
                    $new_meta['faq'][] = [
                        'title' => $faq['name'],
                        'description' => $faq['desc'],
                    ];
                }
            }

            update_post_meta(
                $hotel->ID,
                'tf_hotel',
                $new_meta
            );
        
        }

        /** Hotels Location Taxonomy Migration */
        $hotel_locations = get_terms([
            'taxonomy' => 'hotel_location',
            'hide_empty' => false,
        ]);

        foreach ($hotel_locations as $hotel_location) {

            $old_locations_meta = get_term_meta(
                $hotel_location->term_id,
                'category-image-id',
                true
            );
            $new_meta = [
                "image" => [
                    "url" => wp_get_attachment_url($old_locations_meta),
                    "id" => $old_locations_meta,
                    "width" => "1920",
                    "height" => "1080",
                    "thumbnail" => wp_get_attachment_thumb_url($old_locations_meta),
                    "alt" => "",
                    "title" => "",
                    "description" => ""
                ]
            ];
            // If the meta field for the term does not exist, it will be added.
            update_term_meta(
                $hotel_location->term_id,
                "hotel_location",
                $new_meta
            );
        }
        /** Tour Destinations Image Fix */
        $tour_destinations = get_terms([
            'taxonomy' => 'tour_destination',
            'hide_empty' => false,
        ]);

        foreach ($tour_destinations as  $tour_destination) {
            $old_term_metadata =  get_term_meta($tour_destination->term_id, 'tour_destination_meta', true)['tour_destination_meta'] ?? null;
            if (!empty($old_term_metadata)) {
                $image_id = attachment_url_to_postid($old_term_metadata);
                $new_meta = [
                    "image" => [
                        "url" => wp_get_attachment_url($image_id),
                        "id" => $image_id,
                        "width" => "1920",
                        "height" => "1080",
                        "thumbnail" => wp_get_attachment_thumb_url($image_id),
                        "alt" => "",
                        "title" => "",
                        "description" => ""
                    ]
                ];
                // If the meta field for the term does not exist, it will be added.
                update_term_meta(
                    $tour_destination->term_id,
                    "tour_destination",
                    $new_meta
                );
            }
        }
        /** Tour Type Fix */
        $tours = get_posts(['post_type'   => 'tf_tours', 'numberposts' => -1,]);
        foreach ($tours as $tour) {
            $old_meta = get_post_meta($tour->ID);
            $tour_options = unserialize($old_meta['tf_tours_option'][0]);
            $tour_options['type'] = 'continuous';
            update_post_meta(
                $tour->ID,
                'tf_tours_option',
                $tour_options
            );
        }


        wp_cache_flush();
        flush_rewrite_rules(true);
        update_option('tf_migrate_data_204_210', 1);
	}
}
add_action( 'init', 'tf_migrate_data' );
?>
