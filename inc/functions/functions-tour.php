<?php
defined( 'ABSPATH' ) || exit;

#################################
# Custom post types, taxonomies #
#################################

/**
 * Register post type: tf_tours
 * 
 * @since 1.0
 * @return void
 */
function register_tf_tours_post_type() {

    $tour_slug = !empty(get_option( 'tour_slug' )) ? get_option( 'tour_slug' ) : apply_filters( 'tf_tours_slug', 'tours' );

    $tour_labels = apply_filters( 'tf_tours_labels', array(
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
        'menu_name'             => _x( 'Tours', 'tourfic post type menu name', 'tourfic' ),
        'featured_image'        => __( '%1$s Image', 'tourfic' ),
        'set_featured_image'    => __( 'Set %1$s Image', 'tourfic' ),
        'remove_featured_image' => __( 'Remove %1$s Image', 'tourfic' ),
        'use_featured_image'    => __( 'Use as %1$s Image', 'tourfic' ),
        'attributes'            => __( '%1$s Attributes', 'tourfic' ),
        'filter_items_list'     => __( 'Filter %2$s list', 'tourfic' ),
        'items_list_navigation' => __( '%2$s list navigation', 'tourfic' ),
        'items_list'            => __( '%2$s list', 'tourfic' ),
    ) );

    foreach ( $tour_labels as $key => $value ) {
        $tour_labels[$key] = sprintf( $value, tf_tours_singular_label(), tf_tours_plural_label() );
    }

    $tour_args = array(
        'labels'             => $tour_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'menu_icon'          => 'dashicons-location-alt',
        'rewrite'            => array( 'slug' => $tour_slug, 'with_front' => false ),
        'capability_type'    => array( 'tf_tours', 'tf_tourss' ),
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 25,
        'supports'           => apply_filters( 'tf_tours_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
    );

    register_post_type( 'tf_tours', apply_filters( 'tf_tour_post_type_args', $tour_args ) );
}
// Enable/disable check
if(tfopt('disable-services') && in_array('tour', tfopt('disable-services'))) {} else {
    add_action( 'init', 'register_tf_tours_post_type' );
}

/**
 * Get Default Labels
 *
 * @since 1.0
 * @return array $defaults Default labels
 */
function tf_tours_default_labels() {
    $default_tour = array(
        'singular' => __( 'Tour', 'tourfic' ),
        'plural'   => __( 'Tours', 'tourfic' ),
    );
    return apply_filters( 'tf_tours_name', $default_tour );
}

/**
 * Get Singular Label
 *
 * @since 1.0
 *
 * @param bool $lowercase
 * @return string $defaults['singular'] Singular label
 */
function tf_tours_singular_label( $lowercase = false ) {
    $default_tour = tf_tours_default_labels();
    return ( $lowercase ) ? strtolower( $default_tour['singular'] ) : $default_tour['singular'];
}

/**
 * Get Plural Label
 *
 * @since 1.0
 * @return string $defaults['plural'] Plural label
 */
function tf_tours_plural_label( $lowercase = false ) {
    $default_tour = tf_tours_default_labels();
    return ( $lowercase ) ? strtolower( $default_tour['plural'] ) : $default_tour['plural'];
}

/**
 * Register taxonomies for tf_tours
 * 
 * tour_destination
 */
function tf_tours_taxonomies_register() {

    /**
     * Taxonomy: tour_destination.
     */
    $tour_destination_slug = apply_filters( 'tour_destination_slug', 'tour-destination' );

    $tour_destination_labels = array(
        'name'                       => __( 'Tour Destinations', 'tourfic' ),
        'singular_name'              => __( 'Tour Destination', 'tourfic' ),
        'menu_name'                  => __( 'Destination', 'tourfic' ),
        'all_items'                  => __( 'All Destinations', 'tourfic' ),
        'edit_item'                  => __( 'Edit Destinations', 'tourfic' ),
        'view_item'                  => __( 'View Destinations', 'tourfic' ),
        'update_item'                => __( 'Update Destinations name', 'tourfic' ),
        'add_new_item'               => __( 'Add new Destinations', 'tourfic' ),
        'new_item_name'              => __( 'New Destinations name', 'tourfic' ),
        'parent_item'                => __( 'Parent Destinations', 'tourfic' ),
        'parent_item_colon'          => __( 'Parent Destinations:', 'tourfic' ),
        'search_items'               => __( 'Search Destination', 'tourfic' ),
        'popular_items'              => __( 'Popular Destination', 'tourfic' ),
        'separate_items_with_commas' => __( 'Separate Destination with commas', 'tourfic' ),
        'add_or_remove_items'        => __( 'Add or remove Destination', 'tourfic' ),
        'choose_from_most_used'      => __( 'Choose from the most used Destination', 'tourfic' ),
        'not_found'                  => __( 'No Destination found', 'tourfic' ),
        'no_terms'                   => __( 'No Destination', 'tourfic' ),
        'items_list_navigation'      => __( 'Destination list navigation', 'tourfic' ),
        'items_list'                 => __( 'Destination list', 'tourfic' ),
        'back_to_items'              => __( 'Back to Destination', 'tourfic' ),
    );

    $tour_destination_args = array(
        'labels'                => $tour_destination_labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'hierarchical'          => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'query_var'             => true,
        'rewrite'               => array('slug' => $tour_destination_slug, 'with_front' => false ),
        'show_admin_column'     => true,
        'show_in_rest'          => true,
        'rest_base'             => 'tour_destination',
        'rest_controller_class' => 'WP_REST_Terms_Controller',
        'show_in_quick_edit'    => true,
        'capabilities'          => array( 
            'assign_terms' => 'edit_tf_tours',
            'edit_terms' => 'edit_tf_tours',
         ),
    );
    register_taxonomy( 'tour_destination', 'tf_tours', apply_filters( 'tour_destination_args', $tour_destination_args ) );

}
add_action( 'init', 'tf_tours_taxonomies_register' );

###############################################
# Functions related to post types, taxonomies #
###############################################

/**
 * Flushing Rewrite on Tourfic Activation
 * 
 * tf_tours post type
 * tour_destination taxonomy
 */
function tf_tours_rewrite_flush() {

    register_tf_tours_post_type();
    tf_tours_taxonomies_register();
    flush_rewrite_rules();

}
register_activation_hook( TF_PATH . 'tourfic.php', 'tf_tours_rewrite_flush' );

/**
 * Get tour destinations
 * 
 * {taxonomy-tour_destination}
 */
if ( !function_exists( 'get_tour_destinations' ) ) {
    function get_tour_destinations() {

        $destinations = array();

        $destination_terms = get_terms( array(
            'taxonomy'   => 'tour_destination',
            'hide_empty' => false,
        ) );

        foreach ( $destination_terms as $destination_term ) {
            $destinations[$destination_term->slug] = $destination_term->name;
        }

        return $destinations;

    }
}

#################################
# All the forms                 #
# Search form, booking form     #
#################################

/**
 * Tour Search form
 * 
 * Horizontal
 * 
 * Called in shortcodes
 */
if ( !function_exists('tf_tour_search_form_horizontal') ) {
    function tf_tour_search_form_horizontal( $classes, $title, $subtitle ){
        ?>
        <form class="tf_booking-widget <?php esc_attr_e( $classes ); ?>" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">

        <?php if( $title ): ?>
            <div class="tf_widget-title"><h2><?php esc_html_e( $title ); ?></h2></div>
        <?php endif; ?>

        <?php if( $subtitle ): ?>
            <div class="tf_widget-subtitle"><?php esc_html_e( $subtitle ); ?></div>
        <?php endif; ?>
    <div class="tf_homepage-booking">
        <div class="tf_destination-wrap">
            <div class="tf_input-inner">
            <div class="tf_form-row">
                    <label class="tf_label-row">
                        <span class="tf-label"><?php _e('Destination', 'tourfic'); ?>:</span>
                        <div class="tf_form-inner tf-d-g">
                            <i class="fas fa-search"></i>
                            <input type="text" required id="tf-destination" class="" placeholder="<?php _e('Enter Destination', 'tourfic'); ?>" value="">
                            <input type="hidden" name="place" class="tf-place-input" />                    </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="tf_selectperson-wrap">
            <div class="tf_input-inner">
                <span class="tf_person-icon">
                    <?php echo tourfic_get_svg('person'); ?>
                </span>
                <div class="adults-text">1 <?php _e('Adults', 'tourfic'); ?></div>
                <div class="person-sep"></div>
                <div class="child-text">0 <?php _e('Children', 'tourfic'); ?></div>
                <div class="person-sep"></div>
                <div class="infant-text">0 <?php _e('Infant', 'tourfic'); ?></div>
            </div>
            <div class="tf_acrselection-wrap">
                <div class="tf_acrselection-inner">
                    <div class="tf_acrselection">
                        <div class="acr-label"><?php _e('Adults', 'tourfic'); ?></div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                                <input type="number" name="adults" id="adults" min="1" value="1">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                    <div class="tf_acrselection">
                        <div class="acr-label"><?php _e('Children', 'tourfic'); ?></div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                                <input type="number" name="children" id="children" min="0" value="0">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                    <div class="tf_acrselection">
                        <div class="acr-label"><?php _e('Infant', 'tourfic'); ?></div>
                        <div class="acr-select">
                            <div class="acr-dec">-</div>
                                <input type="number" name="infant" id="infant" min="0" value="0">
                            <div class="acr-inc">+</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="tf_selectdate-wrap">
        <!-- @KK Merged two inputs into one  -->
        <div class="tf_input-inner">
            <label class="tf_label-row">
                        <span class="tf-label"><?php _e('Check-in & Check-out date', 'tourfic'); ?></span>
                        <div class="tf_form-inner tf-d-g">
                            <i class="far fa-calendar-alt"></i>
                            <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;" placeholder="<?php _e('Select Date', 'tourfic'); ?>" required>
                        </div>
                    </label>
            </div>
        </div>

        <div class="tf_submit-wrap">
            <input type="hidden" name="type" value="tf_tours" class="tf-post-type"/>
            <button class="tf_button tf-submit tf-tours-btn" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
        </div>

    </div>

    </form>
    <script>
    (function($) {
        $(document).ready(function() {

            $(".tf_booking-widget #check-in-out-date").flatpickr({
                enableTime: false,
                mode: "range",
                dateFormat: "Y/m/d",
                allowInput: true,
            });

        });
    })(jQuery);
    </script>
    <?php
    }
}

/**
 * Single Tour Booking Bar
 * 
 * Single Tour Page
 */
function tf_single_tour_booking_form( $post_id ) {

    // Value from URL
    // Adults
    $adults = !empty($_GET['adults']) ? sanitize_text_field($_GET['adults']) : '';
    // children
    $child = !empty($_GET['children']) ? sanitize_text_field($_GET['children']) : '';
    // room
    $infant = !empty($_GET['infant']) ? sanitize_text_field($_GET['infant']) : '';
    // Check-in & out date
    $check_in_out = !empty($_GET['check-in-out-date']) ? sanitize_text_field($_GET['check-in-out-date']) : '';
    
    $meta = get_post_meta( $post_id, 'tf_tours_option', true );
    $tour_type = !empty($meta['type']) ? $meta['type'] : '';
    // Continuous custom availability
    $custom_avail = !empty($meta['custom_avail']) ? $meta['custom_avail'] : '';

    if ($tour_type == 'fixed') {

        $departure_date = !empty($meta['fixed_availability']['date']['from']) ? $meta['fixed_availability']['date']['from'] : '';
        $return_date = !empty($meta['fixed_availability']['date']['to']) ? $meta['fixed_availability']['date']['to'] : '';
        $min_people = !empty($meta['fixed_availability']['min_seat']) ? $meta['fixed_availability']['min_seat'] : '';
        $max_people = !empty($meta['fixed_availability']['max_seat']) ? $meta['fixed_availability']['max_seat'] : '';

    } elseif ($tour_type == 'continuous') {

        $disabled_day = !empty($meta['disabled_day']) ? $meta['disabled_day'] : '';
        $disable_range = !empty($meta['disable_range']) ? $meta['disable_range'] : '';
        $disable_specific = !empty($meta['disable_specific']) ? $meta['disable_specific'] : '';
        $disable_specific = str_replace(', ', '", "', $disable_specific);

        if ($custom_avail == true) {

            $cont_custom_date = !empty($meta['cont_custom_date']) ? $meta['cont_custom_date'] : '';

        }     

    }

    $disable_adult_price = !empty($meta['disable_adult_price']) ? $meta['disable_adult_price'] : false;
    $disable_child_price = !empty($meta['disable_child_price']) ? $meta['disable_child_price'] : false;
    $disable_infant_price = !empty($meta['disable_infant_price']) ? $meta['disable_infant_price'] : false;
    $adult_price = !empty($meta['adult_price']) ? $meta['adult_price'] : false;
    $child_price = !empty($meta['child_price']) ? $meta['child_price'] : false;
    $infant_price = !empty($meta['infant_price']) ? $meta['infant_price'] : false;


	$tour_extras = isset($meta['tour-extra']) ? $meta['tour-extra'] : null;

    $times = [];

    if ($custom_avail == true && !empty($meta['cont_custom_date'])) {
        $allowed_times = array_map(function ($v) {
            return $times[] = ['date' => $v['date'], 'times' => array_map(function ($v) {
                return $v['time'];
            }, $v['allowed_time'] ?? [])];
        }, $meta['cont_custom_date']);
    }
    
    if ($custom_avail == false && !empty($meta['allowed_time'])) {
        $allowed_times = array_map(function ($v) {
            return $v['time'];          
        }, $meta['allowed_time'] ?? []);
    }
	
    ob_start();
    ?>
        <div class="tf-tour-booking-wrap">
            <form class="tf_tours_booking">
                <div class="tf_selectperson-wrap">
                    <div class="tf_input-inner">
                        <span class="tf_person-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M16.5 6a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0zM18 6A6 6 0 1 0 6 6a6 6 0 0 0 12 0zM3 23.25a9 9 0 1 1 18 0 .75.75 0 0 0 1.5 0c0-5.799-4.701-10.5-10.5-10.5S1.5 17.451 1.5 23.25a.75.75 0 0 0 1.5 0z"></path></svg>
                        </span>
                        <?php if ($custom_avail == true || (!$disable_adult_price && $adult_price != false)) { ?>
                            <div class="adults-text"><?php echo (!empty($adults) ? $adults : '0') . ' ' . __("Adults", "tourfic"); ?></div>
                        <?php } ?>
                        <?php if ($custom_avail == true || (!$disable_child_price && $child_price != false)) { ?>
                            <div class="person-sep"></div>
                            <div class="child-text"><?php echo (!empty($child) ? $child : '0') . ' ' . __("Children", "tourfic"); ?></div>
                        <?php } ?>
                        <?php if ($custom_avail == true || (!$disable_infant_price && $infant_price != false)) { ?>
                            <div class="person-sep"></div>
                            <div class="infant-text"><?php echo (!empty($infant) ? $infant : '0') . ' ' . __("Infant", "tourfic"); ?></div>
                        <?php } ?>
                    </div>
                    <div class="tf_acrselection-wrap" style="display: none;">
                        <div class="tf_acrselection-inner">
                            <?php if ($custom_avail == true || (!$disable_adult_price && $adult_price != false)) { ?>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e('Adults', 'tourfic'); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="adults" id="adults" min="0" value="<?php echo !empty($adults) ? $adults : '0'; ?>">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if ($custom_avail == true || (!$disable_child_price && $child_price != false)) { ?>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e('Children', 'tourfic'); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="childrens" id="children" min="0" value="<?php echo !empty($child) ? $child : '0'; ?>">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if ($custom_avail == true || (!$disable_infant_price && $infant_price != false)) { ?>
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e('Infant', 'tourfic'); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                        <input type="number" name="infants" id="infant" min="0" value="<?php echo !empty($infant) ? $infant : '0'; ?>">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <div class='tf_form-row'>
	    	        <label class='tf_label-row'>
	    		        <div class='tf_form-inner'>
                            <span class='icon'>
                                <?php tourfic_get_svg('calendar_today'); ?>
                            </span>
                            <input type='text' name='check-in-out-date' id='check-in-out-date' class='tours-check-in-out' onkeypress="return false;" placeholder='Select Date' value='' required />
				        </div>
			        </label>
		        </div>

                <div class='tf_form-row' id="check-in-time-div" style="display: none;">
                    <label class='tf_label-row'>
                        <div class='tf_form-inner'>
                            <span class='icon'>
                                <?php tourfic_get_svg('calendar_today'); ?>
                            </span>
                            <select name="check-in-time" id="check-in-time" style="min-width: 100px;">
                            </select>
                        </div>
                    </label>
                </div>

                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <script>
                    (function ($) {
                        $(document).ready(function () {

                            const allowed_times = JSON.parse('<?php echo wp_json_encode($allowed_times ?? []) ?>');
                            const custom_avail = '<?php echo $custom_avail; ?>';
                            if (custom_avail == false && allowed_times.length > 0) {
                                populateTimeSelect(allowed_times)
                            }

                            function populateTimeSelect(times) {
                                let timeSelect = $('#check-in-time');
                                let timeSelectDiv = $("#check-in-time-div");
                                timeSelect.empty();
                                if (times.length > 0) {
                                    timeSelect.append(`<option value="" selected hidden>Select time</option>`);
                                    $.each(times, function(i, v) {
                                        timeSelect.append(`<option value="${v}">${v}</option>`);
                                    });
                                    timeSelectDiv.show();
                                } else timeSelectDiv.hide();
                            }

                            $("#check-in-out-date").flatpickr({  
                                enableTime: false,
                                dateFormat: "Y/m/d",                               

                            <?php if ($tour_type && $tour_type == 'fixed') { ?>

                                mode: "range",
                                defaultDate: ["<?php echo $departure_date; ?>", "<?php echo $return_date; ?>"],
                                enable: [
                                    {
                                        from: "<?php echo $departure_date; ?>",
                                        to: "<?php echo $return_date; ?>"
                                    }
                                ],

                            <?php } elseif ($tour_type && $tour_type == 'continuous'){ ?>

                                minDate: "today",

                                <?php if ($custom_avail && $custom_avail == true){ ?>

                                enable: [

                                <?php foreach ($cont_custom_date as $item) {
                                    echo '{
                                            from: "' .$item["date"]["from"]. '",
                                            to: "' .$item["date"]["to"]. '"
                                        },';
                                } ?>

                                ],

                                <?php }
                                if ($custom_avail == false) {
                                    if ($disabled_day || $disable_range || $disable_specific) {
                                ?>

                                "disable": [
                                    <?php if ($disabled_day) { ?>
                                    function(date) {
                                        return (date.getDay() === 8 <?php foreach($disabled_day as $dis_day) { echo '|| date.getDay() === ' .$dis_day. ' '; } ?>);
                                    },
                                    <?php }
                                    if ($disable_range) {
                                        foreach ($disable_range as $d_item) {
                                            echo '{
                                                from: "' .$d_item["date"]["from"]. '",
                                                to: "' .$d_item["date"]["to"]. '"
                                            },';
                                        }
                                    }

                                    if ($disable_specific) {
                                        echo '"' .$disable_specific. '"';
                                    }
                                    ?>
                                ],
                            <?php 
                                }
                                }
                                
                            } 
                            ?>

                            onChange: function(selectedDates, dateStr, instance) {
                                if (custom_avail == true) {

                                    let times = allowed_times.filter((v) => {
                                        let date_str = Date.parse(dateStr);
                                        let start_date = Date.parse(v.date.from);
                                        let end_date = Date.parse(v.date.to);
                                        return start_date <= date_str && end_date >= date_str;
                                    });
                                    times = times.length > 0 && times[0].times ? times[0].times : null;
                                    populateTimeSelect(times);
                                }

                            },

                            });

                        });
                    })(jQuery);
                </script>

                <?php if (defined( 'TF_PRO' ) && $tour_extras) { ?>
                <div class="tour-extra">
                    <a data-fancybox data-src="#tour-extra" href="javascript:;"><i class="far fa-plus-square"></i></a>
                    <div style="display: none;" id="tour-extra">
                        <div class="tour-extra-container">
                        <?php foreach( $tour_extras as $tour_extra ){ ?>
                            <div class="tour-extra-single">
                                <div class="tour-extra-left">
                                    <h4><?php _e( $tour_extra['title'] ); ?></h4>
                                    <?php if ($tour_extra['desc']) { ?><p><?php _e( $tour_extra['desc'] ); ?></p><?php } ?>
                                </div>
                                <div class="tour-extra-right">
                                    <span><?php echo wc_price( $tour_extra['price'] ); ?></span>
                                    <input type="checkbox" value="<?php _e( $tour_extra['price'] ); ?>" data-title="<?php _e( $tour_extra['title'] ); ?>">
                                </div>												
                            </div>					
                        <?php } ?>
                        </div>
                    </div>
                </div>	
                <?php } ?>	
                <div class="tf-tours-booking-btn">
                    <input type="hidden" placeholder="location" name="location" value="">
                    <button class="tf_button" type="submit"><?php _e('Book Now', 'tourfic'); ?></button>
                </div>
            </form>
	    </div>
	<?php
return ob_get_clean();
}

#################################
# Layouts                       #
#################################

/**
 * Tours Archive
 */
function tf_tour_archive_single_item($adults='', $child='', $check_in_out='') {

    // get post id
    $post_id = get_the_ID();
    //Get hotel meta values
    $meta = get_post_meta( get_the_ID(),'tf_tours_option',true );
    // Location
    $location  = !empty($meta['text_location']) ? $meta['text_location'] : '';
    // Featured
    $featured  = !empty($meta['tour_as_featured']) ? $meta['tour_as_featured'] : '';

    // Adults
    if(empty($adults)) {
        $adults = !empty($_GET['adults']) ? sanitize_text_field($_GET['adults']) : '';
    }
    // children
    if(empty($child)) {
        $child = !empty($_GET['children']) ? sanitize_text_field($_GET['children']) : '';
    }
    // room
    $infant = !empty($_GET['infant']) ? sanitize_text_field($_GET['infant']) : '';
    // Check-in & out date
    if(empty($check_in_out)) {
        $check_in_out = !empty($_GET['check-in-out-date']) ? sanitize_text_field($_GET['check-in-out-date']) : '';
    }
    // Single link
    $url = get_the_permalink() . '?adults=' . ($adults ?? '') . '&children=' . ($child ?? '') . '&infant=' . ($infant ?? '') . '&check-in-out-date=' . ($check_in_out ?? '');

    ?>
	<div class="single-tour-wrap">
		<div class="single-tour-inner">
			<?php if($featured){ ?>
				<div class="tf-featured"><?php _e( 'Featured','tourfic' ) ?></div>
			<?php }	?>
			<div class="tourfic-single-left">
                <a href="<?php echo $url; ?>">
				<?php
                if (has_post_thumbnail()) {
					the_post_thumbnail( 'full' );
				} else {
                    echo '<img width="100%" height="100%" src="' .TF_ASSETS_URL . "img/img-not-available.svg". '" class="attachment-full size-full wp-post-image">';
                }
                ?>
                </a>
			</div>
			<div class="tourfic-single-right">
				<div class="tf_property_block_main_row">
					<div class="tf_item_main_block">
						<div class="tf-hotel__title-wrap tf-tours-title-wrap">
                            <a href="<?php echo $url; ?>"><h3 class="tourfic_hotel-title"><?php the_title();?></h3></a>
						</div>
						<?php
                        if($location) {
                            echo '<div class="tf_map-link">';
                            echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' .$location. '</span>';
                            echo '</div>';
                        }
                        ?>
					</div>
					<?php tourfic_item_review_block();?>
				</div>
				<div class="tf-tour-desc">
					<p><?php echo substr(wp_strip_all_tags(get_the_content()), 0, 200). '...'; ?></p>
				</div>

				<div class="availability-btn-area">
					<a href="<?php echo $url; ?>" class="button tf_button"><?php esc_html_e( 'Details', 'tourfic' );?></a>
				</div>
			</div>
		</div>
	</div>

	<?php
}

#################################
# WooCommerce integration       #
#################################
/**
 * WooCommerce Tour Functions
 * 
 * @include
 */
if ( file_exists( TF_INC_PATH . 'functions/woocommerce/wc-tour.php' ) ) {
    require_once TF_INC_PATH . 'functions/woocommerce/wc-tour.php';
} else {
    tf_file_missing(TF_INC_PATH . 'functions/woocommerce/wc-tour.php');
}
?>
