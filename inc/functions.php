<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Helper Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-helper.php' ) ) {
    require_once TF_INC_PATH . 'functions/functions-helper.php';
} else {
    tf_file_missing('functions-helper.php');
}

/**
 * Hotel Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions_hotel.php' ) ) {
    require_once TF_INC_PATH . 'functions/functions_hotel.php';
} else {
    tf_file_missing('functions/functions_hotel.php');
}

/**
 * Tour Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions_tour.php' ) ) {
    require_once TF_INC_PATH . 'functions/functions_tour.php';
} else {
    tf_file_missing('functions/functions_tour.php');
}

/**
 * Wishlist Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions_wishlist.php' ) ) {
    require_once TF_INC_PATH . 'functions/functions_wishlist.php';
} else {
    tf_file_missing('functions/functions_wishlist.php');
}

/**
 * Including CSS & JS
 * 
 * @since 1.0
 */
if ( file_exists( TF_INC_PATH . 'enqueues.php' ) ) {
    require_once TF_INC_PATH . 'enqueues.php';
} else {
    tf_file_missing('enqueues.php');
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
        $template = TF_TEMPLATE_PATH . 'hotel/taxonomy-hotel_locations.php';
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
 * Search Result Sidebar form
 */
function tf_search_result_sidebar_form( $placement = 'single' ) { ?>
    <?php
    /**
     * Populate search form from url
     * @author KK
     */
     // Unwanted Slashes Remove
    if ( isset( $_GET ) ) {
        $_GET = array_map( 'stripslashes_deep', $_GET );
    } 
    $post_type = isset( $_GET['type'] ) ? $_GET['type'] : null;
    if(isset($post_type)){
       $id = $post_type == 'tf_tours' ? 'tour_destination' : 'location';
       $placeholder =  $post_type == 'tf_tours' ? 'Destination' : 'Location';
       $location = isset($_GET[$id]) ? $_GET[$id] : null;
       $adult = isset($_GET['adults']) ? $_GET['adults'] : 0;
       $children = isset($_GET['children']) ? $_GET['children'] : 0;
       $room = isset($_GET['room']) ? $_GET['room'] : 0;
       $date = isset($_GET['check-in-out-date']) ? $_GET['check-in-out-date'] : null;
       $place_taxonomy = $post_type == 'tf_tours' ? 'tour_destination' : 'hotel_location';
       $place_name = get_term_by( 'slug', $location , $place_taxonomy)->name;
    }
    ?>
    <!-- Start Booking widget -->
    <form class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
        action="<?php echo tf_booking_search_action(); ?>" id="tf-widget-booking-search">
    
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="location" required=""  class="" placeholder="<?php echo $placeholder ??  "Enter Location" ?>" 
                        value="<?php echo $place_name; ?>">
                        <input type="hidden" id="<?php echo $id; ?>" value="<?php echo $location; ?>"/>
                </div>
            </label>
        </div>
    
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-user-friends"></i>
                    <select name="adults" id="adults" class="">
                        <option <?php echo 1 == $adult ? 'selected' : null ?> value="1">1 adult</option>
                        <?php foreach (range(2,6) as $value) {
                            $selected = $value == $adult ? 'selected' : null;
                            echo "<option $selected value='$value'>$value adults</option>";
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
                        <option value="0">0 child</option>
                        <option <?php echo 1 == $children ? 'selected' : null ?> value="1">1 child</option>
                        <?php foreach (range(2,5) as $value) {
                            $selected = $value == $children ? 'selected' : null;
                            echo "<option $selected value='$value'>$value children</option>";
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
                        <option <?php echo 1 == $room ? 'selected' : null ?> value="1">1 room</option>
                        <?php foreach (range(2,5) as $value) {
                            $selected = $value == $room ? 'selected' : null;
                            echo "<option $selected value='$value'>$value rooms</option>";
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
                            placeholder="Select Date" required value="<?php echo $date ?>">
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
    
    <?php if ( $placement == 'single' ) { ?>
    <?php if ( is_active_sidebar( 'tf_single_booking_sidebar' ) ) { ?>
    <div id="tf__booking_sidebar">
        <?php dynamic_sidebar( 'tf_single_booking_sidebar' ); ?>
        <br>
    </div>
    <?php } ?>
    <?php } else { ?>
    <?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) { ?>
    <div id="tf__booking_sidebar">
        <?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
        <br>
    </div>
    <?php } ?>
    <?php } ?>
    
    <?php
    }

/**
 * Archive Sidebar Search Form
 */
function tf_archive_sidebar_search_form($post_type, $taxonomy, $taxonomy_name, $taxonomy_slug) {
    $taxonomy_type = $post_type == 'tf_tours' ? 'tour_destination' : 'location';
    ?>

    <form class="tf_booking-widget widget tf-hotel-side-booking" method="get" autocomplete="off"
        action="<?php echo tf_booking_search_action(); ?>">
    
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" required=""  class="" placeholder="<?php echo $placeholder ??  "Enter Location" ?>" 
                        value="<?php echo $taxonomy_name; ?>">
                    <input type="hidden" name="location" id="<?php echo $taxonomy_type; ?>" value="<?php echo $taxonomy_slug; ?>"/>
                </div>
            </label>
        </div>
    
        <div class="tf_form-row">
            <label class="tf_label-row">
                <div class="tf_form-inner">
                    <i class="fas fa-user-friends"></i>
                    <select name="adults" id="adults" class="">
                        <option value="1">1 adult</option>
                        <?php foreach (range(2,6) as $value) {                      
                            echo "<option value='$value'>$value adults</option>";
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
                        <option value="0">0 child</option>
                        <option value="1">1 child</option>
                        <?php foreach (range(2,5) as $value) {                          
                            echo "<option value='$value'>$value children</option>";
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
                        <option value="1">1 room</option>
                        <?php foreach (range(2,5) as $value) {
                            echo "<option value='$value'>$value rooms</option>";
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
                            placeholder="Select Date" required value="">
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
 * Migrate data from v2.0.4 to v2.1.0
 * 
 * run once
 */
function tf_migrate_data() {
    if ( get_option( 'tf_migrate_data_204' ) < 1 ) {

        global $wpdb;
        $wpdb->update($wpdb->posts, ['post_type' => 'tf_hotel'], ['post_type' => 'tourfic']);
        $wpdb->update($wpdb->term_taxonomy, ['taxonomy' => 'hotel_location'], ['taxonomy' => 'destination']);
        $wpdb->update($wpdb->term_taxonomy, ['taxonomy' => 'hotel_feature'], ['taxonomy' => 'tf_filters']);


        /** Hotels Migrations */
        $hotels = get_posts(['post_type'   => 'tf_hotel']);
        foreach ($hotels as   $hotel) {
            $old_meta = get_post_meta($hotel->ID);
            if (empty($old_meta['tf_hotel'])) {
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
        $tours = get_posts(['post_type'   => 'tf_tours']);
        foreach ($tours as $tour) {
            $old_meta = get_post_meta($tour->ID);
            $tour_options = unserialize($old_meta['tf_tours_option'][0]);
            $tour_options['type'] = 'continuous';
            update_post_meta(
                $tour->ID,
                'tf_tours_option',
                $tour_options,
            );
        }


        wp_cache_flush();
        flush_rewrite_rules(true);
       update_option( 'tf_migrate_data_204', 1 );
	}
}
add_action( 'init', 'tf_migrate_data' );
?>
