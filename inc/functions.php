<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * WC Product Extend
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php' ) ) {
    function fida() {
    require_once TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php';
    }
    add_action( 'init', 'fida' );
} else {
    tf_file_missing(TF_INC_PATH . 'functions/woocommerce/wc-product-extend.php');
}

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
 * WooCommerce Common Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-common.php' ) ) {
    require_once TF_INC_PATH . 'functions/woocommerce/wc-common.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/woocommerce/wc-common.php');
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
 * Review Functions
 */
if (file_exists(TF_INC_PATH . 'functions/functions-review.php')) {
    require_once TF_INC_PATH . 'functions/functions-review.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/functions-review.php');
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
 * Elementor Widgets
 *
 */
function tf_add_elelmentor_addon() {

    // Check if Elementor installed and activated
    if ( !did_action( 'elementor/loaded' ) ) {
        return;
    }
    // Once we get here, We have passed all validation checks so we can safely include our plugin
    if ( file_exists( TF_INC_PATH . 'elementor/widget-register.php' ) ) {
        require_once TF_INC_PATH . 'elementor/widget-register.php';
    } else {
        tf_file_missing(TF_INC_PATH . 'elementor/widget-register.php');
    }

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

        if ( 'tf_hotel' === $post->post_type || 'tf_tours' === $post->post_type ) {
            $theme_files = array( 'tourfic/template-parts/review.php' );
            $exists_in_theme = locate_template( $theme_files, false );
            if ( $exists_in_theme ) {
                return $exists_in_theme;
            } else {
                return TF_TEMPLATE_PATH . 'template-parts/review.php';
            }
        } 

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
    $post_type = $_GET['type'] ?? '';

    if(!empty($post_type)){

        $place_input_id = $post_type == 'tf_hotel' ? 'tf-location' : 'tf-destination';
        $place_placeholder = $post_type == 'tf_hotel' ? __('Enter Location', 'tourfic') : __('Enter Destination', 'tourfic');

        $place_key = 'place';
        $place_value = $_GET[ $place_key ] ?? '';

        $taxonomy = $post_type == 'tf_hotel' ? 'hotel_location' : 'tour_destination';
        $place_name = !empty($place_value) ? get_term_by( 'slug', $place_value , $taxonomy)->name : '';

        $room = $_GET['room'] ?? 0;
    }

    $adult = $_GET['adults'] ?? 0;
    $children = $_GET['children'] ?? 0;
    $date = $_GET['check-in-out-date'] ?? '';
    $startprice = $_GET['from'] ?? '';
    $endprice = $_GET['to'] ?? '';

    $pcountry = !empty($_GET['country']) ? $_GET['country'] : '';
    $pmonth = !empty($_GET['month']) ? $_GET['month'] : '';

    if ( !defined( 'TF_PRO' ) ){
    ?>
    <!-- Start Booking widget -->
    
    <form class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
        action="<?php echo tf_booking_search_action(); ?>" id="tf-widget-booking-search">
    
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" id="<?php echo $place_input_id ?? ''; ?>" required=""  class="" placeholder="<?php echo $place_placeholder ?? __('Location/Destination', 'tourfic'); ?>" value="<?php echo $place_name ?? ''; ?>">
                    <input type="hidden" name="place" id="tf-place" value="<?php echo $place_value ?? ''; ?>"/>
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
    <?php if ($post_type == 'tf_hotel') { ?>
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
            if(!empty($startprice) && !empty($endprice)){ ?>
            <input type="hidden" id="startprice" value="<?php echo $startprice; ?>">
            <input type="hidden" id="endprice" value="<?php echo $endprice; ?>">
            <?php } ?>
            <?php
                    $ptype = $_GET['type'] ?? get_post_type();
                ?>
            <input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type" />
            <button class="tf_button tf-submit btn-styled"
                type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' );?></button>
        </div>
    
    </form>
    
    <script>
    (function($) {
        $(document).ready(function() {
    
            $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
                enableTime: false,
                mode: "range",
                dateFormat: "Y/m/d",
                onReady: function(selectedDates, dateStr, instance) {
                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                },
                onChange: function(selectedDates, dateStr, instance) {
                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                },
                defaultDate: <?php echo json_encode(explode('-', $date)) ?>,
            });
    
        });
    })(jQuery);
    </script>
    <?php }else{ ?>
        <form class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
        action="<?php echo tf_booking_search_action(); ?>" id="tf-widget-booking-search">
            <h2><?php _e("Search", "tourfic" ); ?></h2>
            <div class="tf_form-row">
                <label class="tf_label-row">
                    <h4><?php _e("Country", "tourfic" ); ?></h4>
                    <div class="tf_form-inner">
                        <input type="text" name="country" required id="tf-country-name" class="tf-advance-destination tf-post-country" value="<?php echo $pcountry; ?>">               
                        <div class="ui-widget ui-widget-content results tf-hotel-results tf-hotel-adv-results">
                        </div>
                    </div>
                </label>
            </div>

            <div class="tf_booking-dates">
                <div class="tf_form-row">
                    <label class="tf_label-row">
                        <div class="tf_form-inner tf_calendar_select">
                            <i class="far fa-calendar-alt"></i>
                            <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                placeholder="<?php _e('Select Date', 'tourfic'); ?>" required value="<?php echo $date ?>">
                        </div>
                    </label>
                </div>
            </div>

            <!-- <div class="tf_form-row">
                <label class="tf_label-row">
                    <h4><?php _e("Month", "tourfic" ); ?></h4>
                    <div class="tf_form-inner">
                        <input type="text" name="month" required id="tf-month-name" class="tf-advance-destination tf-post-month" value="<?php echo $pmonth; ?>">               
                        <div class="ui-widget ui-widget-content results tf-hotel-results tf-hotel-month-results">
                        </div>
                    </div>
                </label>
            </div> -->
        
            <div class="tf_form-row">
                <?php
                    $ptype = $_GET['type'] ?? get_post_type();
                ?>
                <input type="hidden" name="type" value="<?php echo $ptype; ?>" class="tf-post-type" />
                <button class="tf_button tf-submit btn-styled"
                    type="submit"><?php esc_html_e( 'Search', 'tourfic' );?></button>
            </div>
        
        </form>
        <script>
        (function($) {
            $(document).ready(function() {
        
                $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
                    enableTime: false,
                    mode: "range",
                    dateFormat: "Y/m/d",
                    onReady: function(selectedDates, dateStr, instance) {
                        instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                    },
                    onChange: function(selectedDates, dateStr, instance) {
                        instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                    },
                    defaultDate: <?php echo json_encode(explode('-', $date)) ?>,
                });
        
            });
        })(jQuery);
        </script>
        
        <div class="tf-archive-filter-box">
        <h2><?php _e("Filter By", "tourfic" ); ?></h2>

        <div id="tf_hotel_filter-3" class="tf_widget widget widget_tf_hotel_filter">
            <h4 class="tf_widgettitle"><?php _e("Price Per Month", "tourfic" ); ?></h4>
            <div class="tf-filter">
                <ul>
                    <li>
                        <label>
                            <input type="checkbox" name="tf_month_pricing[]" value="1000" /> 
                            Hasta 1.000 <?php echo get_woocommerce_currency_symbol(); ?>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="tf_month_pricing[]" value="1000-1500" /> 
                            1.000 <?php echo get_woocommerce_currency_symbol(); ?> - 1.500 <?php echo get_woocommerce_currency_symbol(); ?>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="checkbox" name="tf_month_pricing[]" value="1500-2000" /> 
                            1.500 <?php echo get_woocommerce_currency_symbol(); ?> - 2.000 <?php echo get_woocommerce_currency_symbol(); ?>
                        </label>
                    </li>
                    <li>
                        <label style="text-transform: none;">
                            <input type="checkbox" name="tf_month_pricing[]" value="2000" /> 
                            Más de 2.000 <?php echo get_woocommerce_currency_symbol(); ?>
                        </label>
                    </li>
                </ul>
            </div>
        </div>

        <div id="tf_hotel_filter-3" class="tf_widget widget widget_tf_hotel_filter">
            <h4 class="tf_widgettitle"><?php _e("Month", "tourfic" ); ?></h4>
            <div class="tf-filter">
                <ul>
                <?php 
                $all_months = [
                    'Enero', 
                    'Febrero', 
                    'March', 
                    'Abril', 
                    'Mayo', 
                    'Junio', 
                    'Julio', 
                    'Agosto', 
                    'Septiembre', 
                    'Octubre', 
                    'Noviembre', 
                    'Diciembre'
                ];

                // Fetch terms
                $tf_property_month = get_terms(array(
                    'taxonomy' => 'hotel_month',
                    'orderby' => 'title', // Original order
                    'order' => 'ASC',
                    'hide_empty' => false,
                    'hierarchical' => 0,
                ));

                if ($tf_property_month) {
                    // Reorder terms based on $all_months
                    usort($tf_property_month, function($a, $b) use ($all_months) {
                        $pos_a = array_search($a->name, $all_months);
                        $pos_b = array_search($b->name, $all_months);
                        return $pos_a - $pos_b; // Sort by position in $all_months
                    });

                    // Display terms
                    foreach ($tf_property_month as $term) {
                        ?>
                        <li>
                            <label>
                                <input type="checkbox" name="tf_property_month[]" value="<?php echo $term->term_id; ?>" <?php echo $pmonth==$term->name ? 'checked' : ''; ?> > 
                                <?php echo ucfirst(strtolower($term->name)); ?>
                            </label>
                        </li>
                        <?php 
                    }
                }
                ?>
                </ul>
            </div>
        </div>

        <div id="tf_hotel_filter-3" class="tf_widget widget widget_tf_hotel_filter">
            <h4 class="tf_widgettitle tf_widge_location_title"><?php _e("Location", "tourfic" ); ?></h4>
            <div class="tf-filter tf_widge_location_content">
                <?php 
                    $tf_hotel_location = get_terms(array(
                        'taxonomy' => 'hotel_location',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hide_empty' => false,
                    ));
                    $total_hotel_locations = count($tf_hotel_location);
                    
                    // Build a hierarchical structure
                    function build_location_hierarchy($terms, $parent = 0) {
                        $hierarchy = [];
                        foreach ($terms as $term) {
                            if ($term->parent == $parent) {
                                $term->children = build_location_hierarchy($terms, $term->term_id);
                                $hierarchy[] = $term;
                            }
                        }
                        return $hierarchy;
                    }

                    // Generate hierarchical terms
                    $hierarchical_terms = build_location_hierarchy($tf_hotel_location);

                    // Recursive function to render the terms with a visible count limit
                    function render_location_terms($terms, &$visible_location_count, $limit = 200) {
                        foreach ($terms as $term) {
                            $is_hidden = $visible_location_count >= $limit; // Hide items beyond the limit
                            ?>
                            <li <?php echo $is_hidden ? 'class="tf-hide-theme"' : ''; ?>>
                                <label>
                                    <input type="checkbox" name="tf_location[]" value="<?php echo $term->term_id; ?>"> 
                                    <?php echo ucfirst(strtolower($term->name)); ?>
                                </label>
                            </li>
                            <?php
                            $visible_location_count++;
                            if (!empty($term->children)) {
                                echo '<ul>';
                                render_location_terms($term->children, $visible_location_count, $limit);
                                echo '</ul>';
                            }
                        }
                    }
                ?>

                <ul>
                    <?php 
                    $visible_location_count = 0; // Initialize visible count
                    if (!empty($hierarchical_terms)) {
                        render_location_terms($hierarchical_terms, $visible_location_count);
                    }
                    ?>
                </ul>

            </div>
        </div>

        <div id="tf_hotel_filter-3" class="tf_widget widget widget_tf_hotel_filter">
            <h4 class="tf_widgettitle tf_widge_prop_title"><?php _e("Property Type", "tourfic" ); ?></h4>
            <div class="tf-filter tf_widge_prop_content">
                <ul>
                    <?php 
                    $tf_property_type= get_terms( array(
                        'taxonomy' => 'hotel_type_property',
                        'orderby' => 'title',
                        'order' => 'ASC',
                        'hide_empty' => false,
                        'hierarchical' => 0,
                    ) );
                    if ( $tf_property_type ) { 
                    foreach( $tf_property_type as $term ) {
                    ?>
                    <li><label><input type="checkbox" name="tf_property_type[]" value="<?php echo $term->term_id; ?>"> <?php echo ucfirst(strtolower($term->name)); ?></label></li>
                    <?php } } ?>
                </ul>
            </div>
        </div>

        <div id="tf_hotel_filter-3" class="tf_widget widget widget_tf_hotel_filter">
            <h4 class="tf_widgettitle tf_widge_stars_title"><?php _e("Stars", "tourfic" ); ?></h4>
            <div class="tf-filter tf_widge_stars_content">
               
                <ul>
                    <?php 
                    $hotel_rating= get_terms( array(
                        'taxonomy' => 'hotel_rating',
                        'orderby' => 'title',
                        'order' => 'ASC',
                        'hide_empty' => false,
                        'hierarchical' => 0,
                    ) );
                    if ( $hotel_rating ) { 
                    foreach( $hotel_rating as $term ) {
                    ?>
                    <li><label><input type="checkbox" name="tf_stars[]" value="<?php echo $term->term_id; ?>"> <?php echo ucfirst(strtolower($term->name)); ?></label></li>
                    <?php } } ?>
                </ul>
            </div>
        </div>
        
        
        <div id="tf_hotel_filter-3" class="tf_widget widget widget_tf_hotel_filter">
            <h4 class="tf_widgettitle tf_widge_prop_style_title"><?php _e("Style of Property", "tourfic" ); ?></h4>
            <div class="tf-filter tf_widge_prop_style_content">
                <ul>
                    <?php 
                    $tf_hotel_property_style= get_terms( array(
                        'taxonomy' => 'hotel_style_property',
                        'orderby' => 'title',
                        'order' => 'ASC',
                        'hide_empty' => false,
                        'hierarchical' => 0,
                    ) );
                    if ( $tf_hotel_property_style ) { 
                    foreach( $tf_hotel_property_style as $term ) {
                    ?>
                    <li><label><input type="checkbox" name="tf_property_style[]" value="<?php echo $term->term_id; ?>"> <?php echo ucfirst(strtolower($term->name)); ?></label></li>
                    <?php } } ?>
                </ul>
            </div>
        </div>
		
		<div id="tf_hotel_filter-3" class="tf_widget widget widget_tf_hotel_filter">
            <h4 class="tf_widgettitle tf_widge_theme_title"><?php _e("Theme", "tourfic" ); ?></h4>
            <div class="tf-filter tf_widge_theme_content">
                    <?php 
                    // Fetch all terms for the 'hotel_theme' taxonomy
                    $tf_hotel_theme = get_terms(array(
                        'taxonomy' => 'hotel_theme',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'hide_empty' => false,
                    ));
                    $total_hotel_themes = count($tf_hotel_theme);
                    
                    // Build a hierarchical structure
                    function build_hierarchy($terms, $parent = 0) {
                        $hierarchy = [];
                        foreach ($terms as $term) {
                            if ($term->parent == $parent) {
                                $term->children = build_hierarchy($terms, $term->term_id);
                                $hierarchy[] = $term;
                            }
                        }
                        return $hierarchy;
                    }

                    // Generate hierarchical terms
                    $hierarchical_terms = build_hierarchy($tf_hotel_theme);

                    // Recursive function to render the terms with a visible count limit
                    function render_terms($terms, &$visible_count, $limit = 5) {
                        foreach ($terms as $term) {
                            $is_hidden = $visible_count >= $limit; // Hide items beyond the limit
                            ?>
                            <li <?php echo $is_hidden ? 'class="tf-hide-theme"' : ''; ?>>
                                <label>
                                    <input type="checkbox" name="tf_theme[]" value="<?php echo $term->term_id; ?>"> 
                                    <?php echo ucfirst(strtolower($term->name)); ?>
                                </label>
                            </li>
                            <?php
                            $visible_count++;
                            if (!empty($term->children)) {
                                echo '<ul>';
                                render_terms($term->children, $visible_count, $limit);
                                echo '</ul>';
                            }
                        }
                    }
                ?>

                <ul>
                    <?php 
                    $visible_count = 0; // Initialize visible count
                    if (!empty($hierarchical_terms)) {
                        render_terms($hierarchical_terms, $visible_count);
                    }
                    ?>
                </ul>

                <?php 
                if(!empty($tf_hotel_theme) && $total_hotel_themes>5){ ?>
                <div class="tf-theme-all-show">
                    <span class="tf-show-all-theme"><?php _e("Show All", "tourfic" ); ?> <i class="fas fa-angle-down"></i></span>
                    <span class="tf-hide-all-theme"><?php _e("Hide All", "tourfic" ); ?> <i class="fas fa-angle-up"></i></span>
                </div>
                <?php } ?>
            </div>
        </div>

        <div id="tf_hotel_filter-3" class="tf_widget widget widget_tf_hotel_filter">
            <h4 class="tf_widgettitle tf_widge_activities_title"><?php _e("Activities", "tourfic" ); ?></h4>
            <div class="tf-filter tf_widge_activities_content">
                <ul>
                    <?php 
                    $tf_hotel_activities= get_terms( array(
                        'taxonomy' => 'hotel_activities',
                        'orderby' => 'title',
                        'order' => 'ASC',
                        'hide_empty' => false,
                        'hierarchical' => 0,
                    ) );
                    if ( $tf_hotel_activities ) { 
                        
                    $total_hotel_activities = count($tf_hotel_activities);
                    $tf_activities_count=1;
                    foreach( $tf_hotel_activities as $term ) {
                    ?>
                    <li <?php echo $tf_activities_count>5 ? 'class="tf-hide-activities"' : ''; ?>><label><input type="checkbox" name="tf_activities[]" value="<?php echo $term->term_id; ?>"> <?php echo ucfirst(strtolower($term->name)); ?></label></li>
                    <?php $tf_activities_count++; } } ?>
                </ul>
                <?php 
                if(!empty($tf_hotel_activities) && $total_hotel_activities>5){ ?>
                <div class="tf-activities-all-show">
                    <span class="tf-show-all-activities"><?php _e("Show All", "tourfic" ); ?> <i class="fas fa-angle-down"></i></span>
                    <span class="tf-hide-all-activities"><?php _e("Hide All", "tourfic" ); ?> <i class="fas fa-angle-up"></i></span>
                </div>
                <?php } ?>
            </div>
        </div>

        <div id="tf_hotel_filter-3" class="tf_widget widget widget_tf_hotel_filter">
            <h4 class="tf_widgettitle tf_widge_meals_title"><?php _e("Meals", "tourfic" ); ?></h4>
            <div class="tf-filter tf_widge_meals_content">
                <ul>
                    <?php 
                    $tf_hotel_meals= get_terms( array(
                        'taxonomy' => 'hotel_meals',
                        'orderby' => 'title',
                        'order' => 'ASC',
                        'hide_empty' => false,
                        'hierarchical' => 0,
                    ) );
                    if ( $tf_hotel_meals ) { 
                    foreach( $tf_hotel_meals as $term ) {
                    ?>
                    <li><label><input type="checkbox" name="tf_meals[]" value="<?php echo $term->term_id; ?>"> <?php echo ucfirst(strtolower($term->name)); ?></label></li>
                    <?php } } ?>
                </ul>
            </div>
        </div>

		<div id="tf_hotel_filter-3" class="tf_widget widget widget_tf_hotel_filter">
            <h4 class="tf_widgettitle tf_widge_stay_title"><?php _e("Duration of Stay", "tourfic" ); ?></h4>
            <div class="tf-filter tf_widge_stay_content">
                <ul>
                <?php 
                $tf_hotel_days = get_terms(array(
                    'taxonomy' => 'hotel_day',
                    'orderby' => 'title', // Default ordering; we'll sort numerically later
                    'order' => 'ASC',
                    'hide_empty' => false,
                    'hierarchical' => false,
                ));

                // Sort terms numerically based on the numeric part of their names
                if (!is_wp_error($tf_hotel_days) && !empty($tf_hotel_days)) {
                    usort($tf_hotel_days, function ($a, $b) {
                        $numA = (int) filter_var($a->name, FILTER_SANITIZE_NUMBER_INT);
                        $numB = (int) filter_var($b->name, FILTER_SANITIZE_NUMBER_INT);
                        return $numA <=> $numB;
                    });

                    // Render the sorted terms
                    foreach ($tf_hotel_days as $term) {
                        ?>
                        <li>
                            <label>
                                <input type="checkbox" name="tf_days[]" value="<?php echo esc_attr($term->term_id); ?>"> 
                                <?php echo ucfirst(strtolower($term->name)); ?>
                            </label>
                        </li>
                        <?php
                    }
                }
                ?>

                </ul>
            </div>
        </div>
        
        <div id="tf_hotel_filter-3" class="tf_widget widget widget_tf_hotel_filter">
            <h4 class="tf_widgettitle tf_widge_features_title"><?php _e("Features", "tourfic" ); ?></h4>
            <div class="tf-filter tf_widge_features_content">
                <ul>
                    <?php 
                    $tf_hotel_feature= get_terms( array(
                        'taxonomy' => 'hotel_feature',
                        'orderby' => 'title',
                        'order' => 'ASC',
                        'hide_empty' => false,
                        'hierarchical' => 0,
                    ) );
                    if ( $tf_hotel_feature ) { 
                    $total_hotel_fature = count($tf_hotel_feature);
                    $tf_feature_count=1;
                    foreach( $tf_hotel_feature as $term ) {
                    ?>
                    <li <?php echo $tf_feature_count>5 ? 'class="tf-hide-feature"' : ''; ?>><label><input type="checkbox" name="tf_filters[]" value="<?php echo $term->term_id; ?>"> <?php echo ucfirst(strtolower($term->name)); ?></label></li>
                    <?php $tf_feature_count++; } } ?>
                </ul>
                <?php 
                if(!empty($tf_hotel_feature) && $total_hotel_fature>5){ ?>
                <div class="tf-feature-all-show">
                    <span class="tf-show-all-feature"><?php _e("Show All", "tourfic" ); ?> <i class="fas fa-angle-down"></i></span>
                    <span class="tf-hide-all-feature"><?php _e("Hide All", "tourfic" ); ?> <i class="fas fa-angle-up"></i></span>
                </div>
                <?php } ?>
            </div>
        </div>
        
        </div>

    <?php } ?>
    
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
            <button class="tf_button tf-submit btn-styled"
                type="submit"><?php esc_html_e( 'Check Availability', 'tourfic' );?></button>
        </div>
    
    </form>
    
    <script>
    (function($) {
        $(document).ready(function() {
    
            $(".tf-hotel-side-booking #check-in-out-date").flatpickr({
                enableTime: false,
                mode: "range",
                dateFormat: "Y/m/d",
                onChange: function(selectedDates, dateStr, instance) {
                    instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                },
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
    $tf_property_month = ( $_POST['tf_property_month'] ) ? explode(',', sanitize_text_field( $_POST['tf_property_month'] )) : null;
    $tf_property_type = ( $_POST['tf_property_type'] ) ? explode(',', sanitize_text_field( $_POST['tf_property_type'] )) : null;
    $tf_month_pricing = ( $_POST['tf_month_pricing'] ) ? explode(',', sanitize_text_field( $_POST['tf_month_pricing'] )) : null;
    $tf_location = ( $_POST['tf_location'] ) ? explode(',', sanitize_text_field( $_POST['tf_location'] )) : null;
    $tf_property_style = ( $_POST['tf_property_style'] ) ? explode(',', sanitize_text_field( $_POST['tf_property_style'] )) : null;
    $tf_days = ( $_POST['tf_days'] ) ? explode(',', sanitize_text_field( $_POST['tf_days'] )) : null;
    $tf_meals = ( $_POST['tf_meals'] ) ? explode(',', sanitize_text_field( $_POST['tf_meals'] )) : null;
    $tf_theme = ( $_POST['tf_theme'] ) ? explode(',', sanitize_text_field( $_POST['tf_theme'] )) : null;
    $tf_activities = ( $_POST['tf_activities'] ) ? explode(',', sanitize_text_field( $_POST['tf_activities'] )) : null;
    $tf_stars = ( $_POST['tf_stars'] ) ? explode(',', sanitize_text_field( $_POST['tf_stars'] )) : null;
    $posttype = $_POST['type']  ? sanitize_text_field( $_POST['type'] ): 'tf_hotel';
   
    # Take dates for filter query
    $checkin = isset($_POST['checkin']) ? trim($_POST['checkin']) : null;
    $startprice = !empty($_POST['startprice']) ? $_POST['startprice'] : '';
    $endprice = !empty($_POST['endprice']) ? $_POST['endprice'] : '';

    $post_country = ( $_POST['post_country'] ) ? sanitize_text_field( $_POST['post_country'] ) : null;
    $post_month = ( $_POST['post_month'] ) ? sanitize_text_field( $_POST['post_month'] ) : null;
    
    list( $tf_form_start, $tf_form_end ) = explode( ' - ', $checkin );

    if(!empty($checkin)) {
        $period = new DatePeriod(
            new DateTime( $tf_form_start ),
            new DateInterval( 'P1D' ),
            new DateTime( $tf_form_end . '23:59' )
        );
    } else {
        $period = '';
    }

    // Properties args
    $args = array(
        'post_type' => 'tf_hotel',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );

    // hotel Country
    if ( $post_country ) {

        $args['tax_query']['relation'] = $relation;
        $args['tax_query'][] = array(
            'taxonomy' => 'hotel_country',
            'field' => 'slug',
            'terms'    => $post_country,
        );
    }
    // hotel month
    // if ( $post_month!=null ) {

    //     $args['tax_query']['relation'] = $relation;
    //     $args['tax_query'][] = array(
    //         'taxonomy' => 'hotel_month',
    //         'field' => 'slug',
    //         'terms'    => $post_month,
    //     );
    // }

    // hotel Month
    if ( $tf_property_month ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'hotel_month',
                'terms'    => $tf_property_month,
            );
        } else {
            $args['tax_query']['tf_filters']['relation'] = 'AND';

            foreach ($tf_property_month as $key => $term_id) {
                $args['tax_query']['tf_filters'][] = array(
                    'taxonomy' => 'hotel_month',
                    'terms'    => array($term_id),
                );
            }

        }
    }

    // hotel Property Type

    if ( $tf_property_type ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'hotel_type_property',
                'terms'    => $tf_property_type,
            );
        } else {
            $args['tax_query']['tf_filters']['relation'] = 'AND';

            foreach ($tf_property_type as $key => $term_id) {
                $args['tax_query']['tf_filters'][] = array(
                    'taxonomy' => 'hotel_type_property',
                    'terms'    => array($term_id),
                );
            }

        }
    }
    // hotel Location

    if ( $tf_location ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'hotel_location',
                'terms'    => $tf_location,
            );
        } else {
            $args['tax_query']['tf_filters']['relation'] = 'AND';

            foreach ($tf_location as $key => $term_id) {
                $args['tax_query']['tf_filters'][] = array(
                    'taxonomy' => 'hotel_location',
                    'terms'    => array($term_id),
                );
            }

        }
    }
    // Style of Property

    if ( $tf_property_style ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'hotel_style_property',
                'terms'    => $tf_property_style,
            );
        } else {
            $args['tax_query']['tf_filters']['relation'] = 'AND';

            foreach ($tf_property_style as $key => $term_id) {
                $args['tax_query']['tf_filters'][] = array(
                    'taxonomy' => 'hotel_style_property',
                    'terms'    => array($term_id),
                );
            }

        }
    }

    // Stars

    if ( $tf_stars ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'hotel_rating',
                'terms'    => $tf_stars,
            );
        } else {
            $args['tax_query']['tf_filters']['relation'] = 'AND';

            foreach ($tf_stars as $key => $term_id) {
                $args['tax_query']['tf_filters'][] = array(
                    'taxonomy' => 'hotel_rating',
                    'terms'    => array($term_id),
                );
            }

        }
    }

    // HOtel Days

    if ( $tf_days ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'hotel_day',
                'terms'    => $tf_days,
            );
        } else {
            $args['tax_query']['tf_filters']['relation'] = 'AND';

            foreach ($tf_days as $key => $term_id) {
                $args['tax_query']['tf_filters'][] = array(
                    'taxonomy' => 'hotel_day',
                    'terms'    => array($term_id),
                );
            }

        }
    }
    // Meals

    if ( $tf_meals ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'hotel_meals',
                'terms'    => $tf_meals,
            );
        } else {
            $args['tax_query']['tf_filters']['relation'] = 'AND';

            foreach ($tf_meals as $key => $term_id) {
                $args['tax_query']['tf_filters'][] = array(
                    'taxonomy' => 'hotel_meals',
                    'terms'    => array($term_id),
                );
            }

        }
    }
    // Theme

    if ( $tf_theme ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'hotel_theme',
                'terms'    => $tf_theme,
            );
        } else {
            $args['tax_query']['tf_filters']['relation'] = 'AND';

            foreach ($tf_theme as $key => $term_id) {
                $args['tax_query']['tf_filters'][] = array(
                    'taxonomy' => 'hotel_theme',
                    'terms'    => array($term_id),
                );
            }

        }
    }
    // Activities

    if ( $tf_activities ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'hotel_activities',
                'terms'    => $tf_activities,
            );
        } else {
            $args['tax_query']['tf_filters']['relation'] = 'AND';

            foreach ($tf_activities as $key => $term_id) {
                $args['tax_query']['tf_filters'][] = array(
                    'taxonomy' => 'hotel_activities',
                    'terms'    => array($term_id),
                );
            }

        }
    }

    // hotel feature

    if ( $filters ) {
        $args['tax_query']['relation'] = $relation;

        if ( $filter_relation == "OR" ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'hotel_feature',
                'terms'    => $filters,
            );
        } else {
            $args['tax_query']['tf_filters']['relation'] = 'AND';

            foreach ($filters as $key => $term_id) {
                $args['tax_query']['tf_filters'][] = array(
                    'taxonomy' => 'hotel_feature',
                    'terms'    => array($term_id),
                );
            }

        }
    }
    

    $loop = new WP_Query( $args );

    if ( $loop->have_posts() ) { 
        $not_found = [];
        while ( $loop->have_posts() ) {
            
            $loop->the_post(); 

            if( $posttype == 'tf_hotel' ){

                // $not_found[] = 0;
                // tf_hotel_archive_single_item($tf_stars);

                if( empty( $check_in_out ) && empty( $tf_month_pricing ) ) {

                    $not_found[] = 0;
                    tf_hotel_archive_single_item();

                } else {

                    $data = [$adults, $child, $room, $check_in_out, $tf_month_pricing];
	                tf_filter_hotel_by_date( $period,$not_found, $data);

                }
            } 
        } 

        if (!in_array(0, $not_found)) {
            echo '<div class="tf-nothing-found">'. __('Nothing Found!', 'tourfic').'</div>';
        }

    } else {

        echo '<div class="tf-nothing-found">'. __('Nothing Found!', 'tourfic').'</div>';

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