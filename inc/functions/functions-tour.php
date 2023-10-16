<?php
defined( 'ABSPATH' ) || exit;

#################################
# Custom post types, taxonomies #
#################################

/**
 * Register post type: tf_tours
 *
 * @return void
 * @since 1.0
 */
function register_tf_tours_post_type() {

	$tour_slug = ! empty( get_option( 'tour_slug' ) ) ? get_option( 'tour_slug' ) : apply_filters( 'tf_tours_slug', 'tours' );

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
		$tour_labels[ $key ] = sprintf( $value, tf_tours_singular_label(), tf_tours_plural_label() );
	}

	$tour_args = array(
		'labels'             => $tour_labels,
		'public'             => true,
		'show_in_rest'       => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'menu_icon'          => 'dashicons-location-alt',
		'rewrite'            => array( 'slug' => $tour_slug, 'with_front' => false ),
		'capability_type'    => array( 'tf_tours', 'tf_tourss' ),
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 26.3,
		'supports'           => apply_filters( 'tf_tours_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
	);

	register_post_type( 'tf_tours', apply_filters( 'tf_tour_post_type_args', $tour_args ) );
}

// Enable/disable check
if ( tfopt( 'disable-services' ) && in_array( 'tour', tfopt( 'disable-services' ) ) ) {
} else {
	add_action( 'init', 'register_tf_tours_post_type' );
}

add_filter( 'use_block_editor_for_post_type', function ( $enabled, $post_type ) {
	return ( 'tf_tours' === $post_type ) ? false : $enabled;
}, 10, 2 );

/**
 * Get Default Labels
 *
 * @return array $defaults Default labels
 * @since 1.0
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
 * @param bool $lowercase
 *
 * @return string $defaults['singular'] Singular label
 * @since 1.0
 *
 */
function tf_tours_singular_label( $lowercase = false ) {
	$default_tour = tf_tours_default_labels();

	return ( $lowercase ) ? strtolower( $default_tour['singular'] ) : $default_tour['singular'];
}

/**
 * Get Plural Label
 *
 * @return string $defaults['plural'] Plural label
 * @since 1.0
 */
function tf_tours_plural_label( $lowercase = false ) {
	$default_tour = tf_tours_default_labels();

	return ( $lowercase ) ? strtolower( $default_tour['plural'] ) : $default_tour['plural'];
}

/**
 * Register taxonomies for tf_tours
 *
 * tour_destination, tour_attraction, tour_activities, tour_features, tour_type
 */
function tf_tours_taxonomies_register() {

	/**
	 * Taxonomy: tour_destination, tour_attraction, tour_activities, tour_features, tour_type
	 */
	$tour_destination_slug = apply_filters( 'tour_destination_slug', 'tour-destination' );
	$tour_attraction_slug  = apply_filters( 'tour_attraction_slug', 'tour-attraction' );
	$tour_actvities_slug   = apply_filters( 'tour_actvities_slug', 'tour-activities' );
	$tour_features_slug    = apply_filters( 'tour_features_slug', 'tour-features' );
	$tour_type_slug        = apply_filters( 'tour_type_slug', 'tour-type' );

	/**
	 * Taxonomy: tour_destination.
	 */
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
		'rewrite'               => array( 'slug' => $tour_destination_slug, 'with_front' => false ),
		'show_admin_column'     => true,
		'show_in_rest'          => true,
		'rest_base'             => 'tour_destination',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'show_in_quick_edit'    => true,
		'capabilities'          => array(
			'assign_terms' => 'edit_tf_tours',
			'edit_terms'   => 'edit_tf_tours',
		),
	);

	/**
	 * Taxonomy: tour_attraction.
	 */
	$tour_attraction_labels = array(
		'name'                       => __( 'Tour Attractions', 'tourfic' ),
		'singular_name'              => __( 'Tour Attractions', 'tourfic' ),
		'menu_name'                  => __( 'Attraction', 'tourfic' ),
		'all_items'                  => __( 'All Attractions', 'tourfic' ),
		'edit_item'                  => __( 'Edit Attractions', 'tourfic' ),
		'view_item'                  => __( 'View Attractions', 'tourfic' ),
		'update_item'                => __( 'Update Attractions name', 'tourfic' ),
		'add_new_item'               => __( 'Add new Attractions', 'tourfic' ),
		'new_item_name'              => __( 'New Attractions name', 'tourfic' ),
		'parent_item'                => __( 'Parent Attractions', 'tourfic' ),
		'parent_item_colon'          => __( 'Parent Attractions:', 'tourfic' ),
		'search_items'               => __( 'Search Attractions', 'tourfic' ),
		'popular_items'              => __( 'Popular Attractions', 'tourfic' ),
		'separate_items_with_commas' => __( 'Separate Attraction with commas', 'tourfic' ),
		'add_or_remove_items'        => __( 'Add or remove Attraction', 'tourfic' ),
		'choose_from_most_used'      => __( 'Choose from the most used Attraction', 'tourfic' ),
		'not_found'                  => __( 'No Attraction found', 'tourfic' ),
		'no_terms'                   => __( 'No Attraction', 'tourfic' ),
		'items_list_navigation'      => __( 'Attraction list navigation', 'tourfic' ),
		'items_list'                 => __( 'Attraction list', 'tourfic' ),
		'back_to_items'              => __( 'Back to Attraction', 'tourfic' ),
	);

	$tour_attraction_args = array(
		'labels'                => $tour_attraction_labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => true,
		'query_var'             => true,
		'rewrite'               => array( 'slug' => $tour_attraction_slug, 'with_front' => false ),
		'show_admin_column'     => true,
		'show_in_rest'          => true,
		'rest_base'             => 'tour_attraction',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'show_in_quick_edit'    => true,
		'capabilities'          => array(
			'assign_terms' => 'edit_tf_tours',
			'edit_terms'   => 'edit_tf_tours',
		),
	);

	/**
	 * Taxonomy: tour_activities.
	 */
	$tour_activities_labels = array(
		'name'                       => __( 'Tour Activities', 'tourfic' ),
		'singular_name'              => __( 'Tour Activity', 'tourfic' ),
		'menu_name'                  => __( 'Activities', 'tourfic' ),
		'all_items'                  => __( 'All Activities', 'tourfic' ),
		'edit_item'                  => __( 'Edit Activity', 'tourfic' ),
		'view_item'                  => __( 'View Activity', 'tourfic' ),
		'update_item'                => __( 'Update Activity name', 'tourfic' ),
		'add_new_item'               => __( 'Add New Activity', 'tourfic' ),
		'new_item_name'              => __( 'New Activity name', 'tourfic' ),
		'parent_item'                => __( 'Parent Activity', 'tourfic' ),
		'parent_item_colon'          => __( 'Parent Activity', 'tourfic' ),
		'search_items'               => __( 'Search Activities', 'tourfic' ),
		'popular_items'              => __( 'Popular Activities', 'tourfic' ),
		'separate_items_with_commas' => __( 'Separate Activities with commas', 'tourfic' ),
		'add_or_remove_items'        => __( 'Add or remove activity', 'tourfic' ),
		'choose_from_most_used'      => __( 'Choose from the most used activity', 'tourfic' ),
		'not_found'                  => __( 'No activity found', 'tourfic' ),
		'no_terms'                   => __( 'No activity', 'tourfic' ),
		'items_list_navigation'      => __( 'Activity list navigation', 'tourfic' ),
		'items_list'                 => __( 'Activity list', 'tourfic' ),
		'back_to_items'              => __( 'Back to Activities', 'tourfic' ),
	);

	$tour_activities_args = array(
		'labels'                => $tour_activities_labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => true,
		'query_var'             => true,
		'rewrite'               => array( 'slug' => $tour_actvities_slug, 'with_front' => false ),
		'show_admin_column'     => true,
		'show_in_rest'          => true,
		'rest_base'             => 'tour_activities',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'show_in_quick_edit'    => true,
		'capabilities'          => array(
			'assign_terms' => 'edit_tf_tours',
			'edit_terms'   => 'edit_tf_tours',
		),
	);

	/**
	 * Taxonomy: tour_features.
	 */
	$tour_features_labels = array(
		'name'                       => __( 'Tour Features', 'tourfic' ),
		'singular_name'              => __( 'Tour Feature', 'tourfic' ),
		'menu_name'                  => __( 'Features', 'tourfic' ),
		'all_items'                  => __( 'All Features', 'tourfic' ),
		'edit_item'                  => __( 'Edit Feature', 'tourfic' ),
		'view_item'                  => __( 'View Feature', 'tourfic' ),
		'update_item'                => __( 'Update Feature name', 'tourfic' ),
		'add_new_item'               => __( 'Add New Feature', 'tourfic' ),
		'new_item_name'              => __( 'New Feature name', 'tourfic' ),
		'parent_item'                => __( 'Parent Feature', 'tourfic' ),
		'parent_item_colon'          => __( 'Parent Feature', 'tourfic' ),
		'search_items'               => __( 'Search Features', 'tourfic' ),
		'popular_items'              => __( 'Popular Features', 'tourfic' ),
		'separate_items_with_commas' => __( 'Separate features with commas', 'tourfic' ),
		'add_or_remove_items'        => __( 'Add or remove feature', 'tourfic' ),
		'choose_from_most_used'      => __( 'Choose from the most used feature', 'tourfic' ),
		'not_found'                  => __( 'No Feature found', 'tourfic' ),
		'no_terms'                   => __( 'No activity', 'tourfic' ),
		'items_list_navigation'      => __( 'Feature list navigation', 'tourfic' ),
		'items_list'                 => __( 'Feature list', 'tourfic' ),
		'back_to_items'              => __( 'Back to Features', 'tourfic' ),
	);

	$tour_features_args = array(
		'labels'                => $tour_features_labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => true,
		'query_var'             => true,
		'rewrite'               => array( 'slug' => $tour_features_slug, 'with_front' => false ),
		'show_admin_column'     => true,
		'show_in_rest'          => true,
		'meta_box_cb'           => false,
		'rest_base'             => 'tour_features',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'show_in_quick_edit'    => true,
		'capabilities'          => array(
			'assign_terms' => 'edit_tf_tours',
			'edit_terms'   => 'edit_tf_tours',
		),
	);

	/**
	 * Taxonomy: tour_type
	 */
	$tour_type_labels = array(
		'name'                       => __( 'Tour Types', 'tourfic' ),
		'singular_name'              => __( 'Tour Type', 'tourfic' ),
		'menu_name'                  => __( 'Types', 'tourfic' ),
		'all_items'                  => __( 'All Types', 'tourfic' ),
		'edit_item'                  => __( 'Edit Type', 'tourfic' ),
		'view_item'                  => __( 'View Type', 'tourfic' ),
		'update_item'                => __( 'Update Type name', 'tourfic' ),
		'add_new_item'               => __( 'Add New Type', 'tourfic' ),
		'new_item_name'              => __( 'New Type name', 'tourfic' ),
		'parent_item'                => __( 'Parent Type', 'tourfic' ),
		'parent_item_colon'          => __( 'Parent Type', 'tourfic' ),
		'search_items'               => __( 'Search Types', 'tourfic' ),
		'popular_items'              => __( 'Popular Types', 'tourfic' ),
		'separate_items_with_commas' => __( 'Separate type with commas', 'tourfic' ),
		'add_or_remove_items'        => __( 'Add or remove feature', 'tourfic' ),
		'choose_from_most_used'      => __( 'Choose from the most used feature', 'tourfic' ),
		'not_found'                  => __( 'No Type found', 'tourfic' ),
		'no_terms'                   => __( 'No activity', 'tourfic' ),
		'items_list_navigation'      => __( 'Type list navigation', 'tourfic' ),
		'items_list'                 => __( 'Type list', 'tourfic' ),
		'back_to_items'              => __( 'Back to Types', 'tourfic' ),
	);

	$tour_type_args = array(
		'labels'                => $tour_type_labels,
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => true,
		'query_var'             => true,
		'rewrite'               => array( 'slug' => $tour_type_slug, 'with_front' => false ),
		'show_admin_column'     => true,
		'show_in_rest'          => true,
		'meta_box_cb'           => false,
		'rest_base'             => 'tour_type',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'show_in_quick_edit'    => true,
		'capabilities'          => array(
			'assign_terms' => 'edit_tf_tours',
			'edit_terms'   => 'edit_tf_tours',
		),
	);

	register_taxonomy( 'tour_destination', 'tf_tours', apply_filters( 'tour_destination_args', $tour_destination_args ) );
	register_taxonomy( 'tour_attraction', 'tf_tours', apply_filters( 'tour_attraction_args', $tour_attraction_args ) );
	register_taxonomy( 'tour_activities', 'tf_tours', apply_filters( 'tour_activities_args', $tour_activities_args ) );
	register_taxonomy( 'tour_features', 'tf_tours', apply_filters( 'tour_features_args', $tour_features_args ) );
	register_taxonomy( 'tour_type', 'tf_tours', apply_filters( 'tour_type_args', $tour_type_args ) );

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
if ( ! function_exists( 'get_tour_destinations' ) ) {
	function get_tour_destinations() {

		$destinations = array();

		$destination_terms = get_terms( array(
			'taxonomy'   => 'tour_destination',
			'hide_empty' => true,
		) );

		foreach ( $destination_terms as $destination_term ) {
			$destinations[ $destination_term->slug ] = $destination_term->name;
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
if ( ! function_exists( 'tf_tour_search_form_horizontal' ) ) {
	function tf_tour_search_form_horizontal( $classes, $title, $subtitle, $author ) {

        // date Format for User Output
        $tour_date_format_for_users = !empty(tfopt( "tf-date-format-for-users")) ? tfopt( "tf-date-format-for-users") : "Y/m/d";

        ?>
        <form class="tf_booking-widget <?php esc_attr_e( $classes ); ?>" id="tf_tour_aval_check" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">
			<?php
			$disable_child_search  = ! empty( tfopt( 'disable_child_search' ) ) ? tfopt( 'disable_child_search' ) : '';
			$disable_infant_search = ! empty( tfopt( 'disable_infant_search' ) ) ? tfopt( 'disable_infant_search' ) : '';
			?>

            <div class="tf_homepage-booking">
                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php _e( 'Destination', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="fas fa-search"></i>
                                    <input type="text" name="place-name" required id="tf-destination" class="" placeholder="<?php _e( 'Enter Destination', 'tourfic' ); ?>" value="">
                                    <input type="hidden" name="place" id="tf-search-tour" class="tf-place-input"/></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_selectperson-wrap">
                    <div class="tf_input-inner">
                        <span class="tf_person-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <div class="adults-text"><?php _e( '1 Adults', 'tourfic' ); ?></div>
						<?php
						if ( empty( $disable_child_search ) ) {
							?>
                            <div class="person-sep"></div>
                            <div class="child-text"><?php _e( '0 Children', 'tourfic' ); ?></div>
						<?php }
						if ( empty( $disable_infant_search ) ) {
							?>
                            <div class="person-sep"></div>
                            <div class="infant-text"><?php _e( '0 Infant', 'tourfic' ); ?></div>
						<?php } ?>
                    </div>
                    <div class="tf_acrselection-wrap">
                        <div class="tf_acrselection-inner">
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e( 'Adults', 'tourfic' ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="adults" id="adults" min="1" value="1">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
							<?php
							if ( empty( $disable_child_search ) ) {
								?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="children" id="children" min="0" value="0">
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
							<?php }
							if ( empty( $disable_infant_search ) ) {
								?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Infant', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="infant" id="infant" min="0" value="0">
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                </div>

                <div class="tf_selectdate-wrap">
                    <!-- @KK Merged two inputs into one  -->
                    <div class="tf_input-inner">
                        <label class="tf_label-row">
                            <span class="tf-label"><?php _e( 'Check-in & Check-out date', 'tourfic' ); ?></span>
                            <div class="tf_form-inner tf-d-g">
                                <i class="far fa-calendar-alt"></i>
                                <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                       placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" <?php echo tfopt( 'date_tour_search' ) ? 'required' : ''; ?>>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="tf_submit-wrap">
                    <input type="hidden" name="type" value="tf_tours" class="tf-post-type"/>
					<?php
					if ( $author ) { ?>
                        <input type="hidden" name="tf-author" value="<?php echo $author; ?>" class="tf-post-type"/>
					<?php } ?>
                    <button class="tf_button tf-submit btn-styled" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
                </div>

            </div>

        </form>
        <script>
            (function ($) {
                $(document).ready(function () {

					$("#tf_tour_aval_check #check-in-out-date").flatpickr({
						enableTime: false,
						mode: "range",
						altInput: true,
						dateFormat: "Y/m/d",
						altFormat: '<?php echo $tour_date_format_for_users; ?>',
						minDate: "today",
						onReady: function(selectedDates, dateStr, instance) {
							instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
						},
						onChange: function(selectedDates, dateStr, instance) {
							instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
						},
					});

                });
            })(jQuery);
        </script>
		<?php
	}
}


/**
 * Tour Advance Search form
 *
 * Horizontal
 *
 * Called in shortcodes
 */
if ( !function_exists('tf_tour_advanced_search_form_horizontal') ) {
    function tf_tour_advanced_search_form_horizontal( $classes, $title, $subtitle, $author ) {
        $tour_date_format_for_users  = !empty(tfopt( "tf-date-format-for-users")) ? tfopt( "tf-date-format-for-users") : "Y/m/d";
        ?>
        <form class="tf_booking-widget <?php esc_attr_e( $classes ); ?>" id="tf_tour_aval_check" method="get" autocomplete="off" action="<?php echo tf_booking_search_action(); ?>">
			<?php
			$disable_child_search  = ! empty( tfopt( 'disable_child_search' ) ) ? tfopt( 'disable_child_search' ) : '';
			$disable_infant_search = ! empty( tfopt( 'disable_infant_search' ) ) ? tfopt( 'disable_infant_search' ) : '';
			?>
            <div class="tf_homepage-booking">
                <div class="tf_destination-wrap">
                    <div class="tf_input-inner">
                        <div class="tf_form-row">
                            <label class="tf_label-row">
                                <span class="tf-label"><?php _e( 'Destination', 'tourfic' ); ?>:</span>
                                <div class="tf_form-inner tf-d-g">
                                    <i class="fas fa-search"></i>
                                    <input type="text" name="place-name" required id="tf-tour-location-adv" class="tf-tour-preview-place" placeholder="<?php _e( 'Enter Location', 'tourfic' ); ?>">
                                    <input type="hidden" name="place" id="tf-tour-place">
                                    <div class="tf-hotel-results tf-tour-results">
                                        <ul id="ui-id-2">
											<?php
											$tf_tour_destination = get_terms( array(
												'taxonomy'     => 'tour_destination',
												'orderby'      => 'title',
												'order'        => 'ASC',
												'hide_empty'   => false,
												'hierarchical' => 0,
											) );
											if ( $tf_tour_destination ) {
												foreach ( $tf_tour_destination as $term ) {
													if ( ! empty( $term->name ) ) {
														?>
                                                        <li data-name="<?php echo $term->name; ?>" data-slug="<?php echo $term->slug; ?>"><i class="fa fa-map-marker"></i><?php echo $term->name; ?></li>
														<?php
													}
												}
											}
											?>
                                        </ul>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf_selectperson-wrap">
                    <div class="tf_input-inner">
                        <span class="tf_person-icon">
                            <i class="fas fa-user"></i>
                        </span>
                        <div class="adults-text"><?php _e( '1 Adults', 'tourfic' ); ?></div>
						<?php
						if ( empty( $disable_child_search ) ) {
							?>
                            <div class="person-sep"></div>
                            <div class="child-text"><?php _e( '0 Children', 'tourfic' ); ?></div>
						<?php }
						if ( empty( $disable_infant_search ) ) {
							?>
                            <div class="person-sep"></div>
                            <div class="infant-text"><?php _e( '0 Infant', 'tourfic' ); ?></div>
						<?php } ?>
                    </div>
                    <div class="tf_acrselection-wrap">
                        <div class="tf_acrselection-inner">
                            <div class="tf_acrselection">
                                <div class="acr-label"><?php _e( 'Adults', 'tourfic' ); ?></div>
                                <div class="acr-select">
                                    <div class="acr-dec">-</div>
                                    <input type="number" name="adults" id="adults" min="1" value="1">
                                    <div class="acr-inc">+</div>
                                </div>
                            </div>
							<?php
							if ( empty( $disable_child_search ) ) {
								?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="children" id="children" min="0" value="0">
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
							<?php }
							if ( empty( $disable_infant_search ) ) {
								?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Infant', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="infant" id="infant" min="0" value="0">
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                </div>

                <div class="tf_selectdate-wrap">
                    <!-- @KK Merged two inputs into one  -->
                    <div class="tf_input-inner">
                        <label class="tf_label-row">
                            <span class="tf-label"><?php _e( 'Check-in & Check-out date', 'tourfic' ); ?></span>
                            <div class="tf_form-inner tf-d-g">
                                <i class="far fa-calendar-alt"></i>
                                <input type="text" name="check-in-out-date" id="check-in-out-date" onkeypress="return false;"
                                       placeholder="<?php _e( 'Select Date', 'tourfic' ); ?>" <?php echo tfopt( 'date_tour_search' ) ? 'required' : ''; ?>>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="tf_selectdate-wrap tf_more_info_selections">
                    <div class="tf_input-inner">
                        <label class="tf_label-row" style="width: 100%;">
                            <span class="tf-label"><?php _e( 'More', 'tourfic' ); ?></span>
                            <span style="text-decoration: none; display: block; cursor: pointer;"><?php _e( 'Filter', 'tourfic' ); ?>  <i class="fas fa-angle-down"></i></span>
                        </label>
                    </div>
                    <div class="tf-more-info">
                        <h3><?php _e( 'Filter Price', 'tourfic' ); ?></h3>
                        <div class="tf-filter-price-range">
                            <div class="tf-tour-filter-range"></div>
                        </div>
                        <h3 style="margin-top: 20px"><?php _e( 'Tour Types', 'tourfic' ); ?></h3>
						<?php
						$tf_tour_type = get_terms( array(
							'taxonomy'     => 'tour_type',
							'orderby'      => 'title',
							'order'        => 'ASC',
							'hide_empty'   => true,
							'hierarchical' => 0,
						) );
						if ( $tf_tour_type ) : ?>
                            <div class="tf-tour-types" style="overflow: hidden">
								<?php foreach ( $tf_tour_type as $term ) : ?>
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="types[]" class="form-check-input" value="<?php _e( $term->slug ); ?>" id="<?php _e( $term->slug ); ?>">
                                        <label class="form-check-label" for="<?php _e( $term->slug ); ?>"><?php _e( $term->name ); ?></label>
                                    </div>
								<?php endforeach; ?>
                            </div>
						<?php endif; ?>
                    </div>
                </div>

                <div class="tf_submit-wrap">
                    <input type="hidden" name="type" value="tf_tours" class="tf-post-type"/>
					<?php
					if ( $author ) { ?>
                        <input type="hidden" name="tf-author" value="<?php echo $author; ?>" class="tf-post-type"/>
					<?php } ?>
                    <button class="tf_button tf-submit tf-tours-btn btn-styled" type="submit"><?php esc_html_e( 'Search', 'tourfic' ); ?></button>
                </div>

            </div>

        </form>
        <script>
            (function ($) {
                $(document).ready(function () {

                    $("#tf_tour_aval_check #check-in-out-date").flatpickr({
                        enableTime: false,
						altInput: true,
                		altFormat: '<?php echo $tour_date_format_for_users; ?>',
                        mode: "range",
                        dateFormat: "Y/m/d",
                        minDate: "today",
                        onReady: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
							instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                        },
                        onChange: function (selectedDates, dateStr, instance) {
                            instance.element.value = dateStr.replace(/[a-z]+/g, '-');
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                        },
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
	$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
	// children
	$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
	// room
	$infant = ! empty( $_GET['infant'] ) ? sanitize_text_field( $_GET['infant'] ) : '';
	// Check-in & out date
	$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';

	$meta      = get_post_meta( $post_id, 'tf_tours_opt', true );
	$tour_type = ! empty( $meta['type'] ) ? $meta['type'] : '';
	// Continuous custom availability
	$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : '';
	
	// Date format for Users Oputput
	$tour_date_format_for_users  = !empty(tfopt( "tf-date-format-for-users")) ? tfopt( "tf-date-format-for-users") : "Y/m/d";

	// Repeat fixed tour by month
	$fixed_tour_repeat_month = 0;
	// $fixed_tour_repeat_months = ($fixed_tour_repeat_month == 1) && !empty()

	// echo "<pre>";
	// print_r($fixed_tour_repeat_month);
	// echo "</pre>";
	// die(); // added by - Sunvi

	// Same Day Booking
	$disable_same_day = ! empty( $meta['disable_same_day'] ) ? $meta['disable_same_day'] : '';
	if ( $tour_type == 'fixed' ) {
		if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
			$tf_tour_fixed_avail = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $meta['fixed_availability'] );
			$tf_tour_fixed_date  = unserialize( $tf_tour_fixed_avail );
			$departure_date      = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
			$return_date         = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
			$min_people          = ! empty( $tf_tour_fixed_date['min_seat'] ) ? $tf_tour_fixed_date['min_seat'] : '';
			$max_people          = ! empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
			$is_fixed_tour_repeat = !empty($tf_tour_fixed_avail["tf-repeat-months-switch"]) ? $tf_tour_fixed_avail["tf-repeat-months-switch"] : 0;
			$fixed_tour_repeat_months = ($is_fixed_tour_repeat && !empty($tf_tour_fixed_avail["tf-repeat-months-checkbox"])) ? $tf_tour_fixed_avail["tf-repeat-months-checkbox"] : array();
		} else {
			$departure_date = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
			$return_date    = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
			$min_people     = ! empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
			$max_people     = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
			$is_fixed_tour_repeat = !empty($meta["fixed_availability"]["tf-repeat-months-switch"]) ? $meta["fixed_availability"]["tf-repeat-months-switch"] : 0;
			$fixed_tour_repeat_months = ($is_fixed_tour_repeat && !empty($meta["fixed_availability"]["tf-repeat-months-checkbox"])) ? $meta["fixed_availability"]["tf-repeat-months-checkbox"] : array();
		}

	} elseif ( $tour_type == 'continuous' ) {

		$disabled_day  = ! empty( $meta['disabled_day'] ) ? $meta['disabled_day'] : '';
		$disable_range = ! empty( $meta['disable_range'] ) ? $meta['disable_range'] : '';
		if ( ! empty( $disable_range ) && gettype( $disable_range ) == "string" ) {
			$disable_range_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $disable_range );
			$disable_range          = unserialize( $disable_range_unserial );

		}
		$disable_specific = ! empty( $meta['disable_specific'] ) ? $meta['disable_specific'] : '';
		$disable_specific = str_replace( ', ', '", "', $disable_specific );

		if ( $custom_avail == true ) {

			$cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';

			if ( ! empty( $cont_custom_date ) && gettype( $cont_custom_date ) == "string" ) {
				$cont_custom_date_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $cont_custom_date );
				$cont_custom_date          = unserialize( $cont_custom_date_unserial );

			}

		}

	}

	function fixed_tour_month_changer($date, $months) {
		if(!empty($months) && !empty($date)) {
			preg_match('/(\d{4})\/(\d{2})\/(\d{2})/', $date, $matches);

			$new_months[] = $matches[0];

			foreach($months as $month) {
				$matches[2] = $month;
				$new_months[] = sprintf("%s/%s/%s", $matches[1], $matches[2], $matches[3]);
			}
			return $new_months;
		} else return array();
	}

	$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
	$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
	$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
	$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
	$group_price          = ! empty( $meta['group_price'] ) ? $meta['group_price'] : false;
	$adult_price          = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : false;
	$child_price          = ! empty( $meta['child_price'] ) ? $meta['child_price'] : false;
	$infant_price         = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : false;
	$tour_extras          = isset( $meta['tour-extra'] ) ? $meta['tour-extra'] : null;
	if ( ! empty( $tour_extras ) && gettype( $tour_extras ) == "string" ) {

		$tour_extras_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $tour_extras );
		$tour_extras          = unserialize( $tour_extras_unserial );

	}
	$times = [];
	if ( ! empty( $meta['cont_custom_date'] ) && gettype( $meta['cont_custom_date'] ) == "string" ) {

		$tf_tour_unserial_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $meta['cont_custom_date'] );
		$tf_tour_unserial_custom_date = unserialize( $tf_tour_unserial_custom_date );

		if ( ! empty( $tf_tour_unserial_custom_date ) ) {
			if ( $custom_avail == true && ! empty( $meta['cont_custom_date'] ) ) {
				$allowed_times = array_map( function ( $v ) {
					return $times[] = [
						'date'  => $v['date'],
						'times' => array_map( function ( $v ) {
							return $v['time'];
						}, $v['allowed_time'] ?? [] )
					];
				}, $tf_tour_unserial_custom_date );
			}
		}

	} else {
		if ( $custom_avail == true && ! empty( $meta['cont_custom_date'] ) ) {
			$allowed_times = array_map( function ( $v ) {
				if ( ! empty( $v['date'] ) ) {
					return $times[] = [
						'date'  => $v['date'],
						'times' => array_map( function ( $v ) {
							return $v['time'];
						}, $v['allowed_time'] ?? [] )
					];
				}
			}, $meta['cont_custom_date'] );
		}

	}

	if ( ! empty( $meta['allowed_time'] ) && gettype( $meta['allowed_time'] ) == "string" ) {

		$tf_tour_unserial_custom_time = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $meta['allowed_time'] );
		$tf_tour_unserial_custom_time = unserialize( $tf_tour_unserial_custom_time );
		if ( ! empty( $tf_tour_unserial_custom_time ) ) {
			if ( $custom_avail == false && ! empty( $meta['allowed_time'] ) ) {
				$allowed_times = array_map( function ( $v ) {
					return $v['time'];
				}, $tf_tour_unserial_custom_time ?? [] );
			}
		}
	} else {
		if ( $custom_avail == false && ! empty( $meta['allowed_time'] ) ) {
			$allowed_times = array_map( function ( $v ) {
				return $v['time'];
			}, $meta['allowed_time'] ?? [] );
		}
	}
	// Single Template Check
	$tf_tour_layout_conditions = ! empty( $meta['tf_single_tour_layout_opt'] ) ? $meta['tf_single_tour_layout_opt'] : 'global';
	if ( "single" == $tf_tour_layout_conditions ) {
		$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'design-1';
	}
	$tf_tour_global_template = ! empty( tf_data_types( tfopt( 'tf-template' ) )['single-tour'] ) ? tf_data_types( tfopt( 'tf-template' ) )['single-tour'] : 'design-1';

	$tf_tour_selected_check = ! empty( $tf_tour_single_template ) ? $tf_tour_single_template : $tf_tour_global_template;

	$tf_plugin_installed = get_option( 'tourfic_template_installed' );
	if ( ! empty( $tf_plugin_installed ) ) {
		$tf_tour_selected_template = $tf_tour_selected_check;
	} else {
		if ( "single" == $tf_tour_layout_conditions ) {
			$tf_tour_single_template = ! empty( $meta['tf_single_tour_template'] ) ? $meta['tf_single_tour_template'] : 'default';
		}
		$tf_tour_global_template = ! empty( tf_data_types( tfopt( 'tf-template' ) )['single-tour'] ) ? tf_data_types( tfopt( 'tf-template' ) )['single-tour'] : 'default';

		$tf_tour_selected_check = ! empty( $tf_tour_single_template ) ? $tf_tour_single_template : $tf_tour_global_template;

		$tf_tour_selected_template = $tf_tour_selected_check ? $tf_tour_selected_check : 'default';
	}
	if ( ! function_exists( 'partial_payment_tag_replacement' ) ) {
		function partial_payment_tag_replacement( $text, $arr ) {
			if(!empty($arr)) {
				$tag = array_keys($arr);
				$value = array_values($arr);
			}
			return str_replace($tag, $value, $text);
		}
	}
	if ( ! function_exists( 'tf_booking_popup' ) ) {
		function tf_booking_popup( $post_id ) {
			?>
            <!-- Loader Image -->
            <div id="tour_room_details_loader">
                <div id="tour-room-details-loader-img">
                    <img src="<?php echo TF_ASSETS_APP_URL ?>images/loader.gif" alt="Loader">
                </div>
            </div>
            <div class="tf-withoutpayment-booking-confirm">
                <div class="tf-confirm-popup">
                    <div class="tf-booking-times">
						<span>
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
							<path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
							<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
							</svg>
						</span>
                    </div>
                    <img src="<?php echo TF_ASSETS_APP_URL ?>images/thank-you.gif" alt="Thank You">
                    <h2>
					<?php
					$booking_confirmation_msg = !empty(tfopt( 'booking-confirmation-msg' )) ? tfopt( 'booking-confirmation-msg' ) : 'Booked Successfully';
					echo $booking_confirmation_msg;
					?>
					</h2>
                </div>
            </div>
            <div class="tf-withoutpayment-booking">
                <div class="tf-withoutpayment-popup">
                    <div class="tf-booking-tabs">
                        <div class="tf-booking-tab-menu">
                            <ul>
								<?php
								$meta        = get_post_meta( $post_id, 'tf_tours_opt', true );
								$tour_extras = function_exists( 'is_tf_pro' ) && is_tf_pro() && isset( $meta['tour-extra'] ) ? $meta['tour-extra'] : null;
								if ( ! empty( $tour_extras ) && gettype( $tour_extras ) == "string" ) {

									$tour_extras_unserial = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
										return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
									}, $tour_extras );
									$tour_extras          = unserialize( $tour_extras_unserial );

							    }
							    $traveller_info_coll_global = function_exists('is_tf_pro') && is_tf_pro() && !empty(tfopt( 'disable_traveller_info' )) ? tfopt( 'disable_traveller_info' ) : '';

							    $traveller_info_coll = function_exists('is_tf_pro') && is_tf_pro() && !empty($meta['tour-traveler-info']) ? $meta['tour-traveler-info'] : $traveller_info_coll_global;

							    if ( function_exists('is_tf_pro') && is_tf_pro() && $tour_extras ) {  ?>
                                    <li class="tf-booking-step tf-booking-step-1 active">
                                        <i class="ri-price-tag-3-line"></i> <?php echo __( "Tour extra", "tourfic" ); ?>
                                    </li>
								<?php }
								if ( $traveller_info_coll ) {
									?>
                                    <li class="tf-booking-step tf-booking-step-2 <?php echo empty( $tour_extras ) ? esc_attr( 'active' ) : ''; ?> ">
                                        <i class="ri-group-line"></i> <?php echo __( "Traveler details", "tourfic" ); ?>
                                    </li>
							    <?php }
								$tf_booking_by = !empty($meta['booking-by']) ? $meta['booking-by'] : 1;
							    if( function_exists('is_tf_pro') && is_tf_pro() && 3==$tf_booking_by ){
								    ?>
                                    <li class="tf-booking-step tf-booking-step-3 <?php echo empty($tour_extras) && empty($traveller_info_coll) ? esc_attr( 'active' ) : ''; ?>">
                                        <i class="ri-calendar-check-line"></i> <?php echo __("Booking Confirmation","tourfic"); ?>
                                    </li>
							    <?php } ?>
                            </ul>
                        </div>
                        <div class="tf-booking-times">
							<span>
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
								<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" fill="#FCFDFF"/>
								<path d="M12 11.1111L15.1111 8L16 8.88889L12.8889 12L16 15.1111L15.1111 16L12 12.8889L8.88889 16L8 15.1111L11.1111 12L8 8.88889L8.88889 8L12 11.1111Z" fill="#666D74"/>
								<rect x="0.5" y="0.5" width="23" height="23" rx="3.5" stroke="#FCFDFF"/>
								</svg>
							</span>
                        </div>
                    </div>
                    <div class="tf-booking-content-summery">

                        <!-- Popup Tour Extra -->
						<?php
						// $popup_extra_default_text = "Here we include our tour extra services. If you want take any of the service. Start and end in Edinburgh! With the In-depth Cultural";
						$tour_popup_extra_text = function_exists('is_tf_pro') && is_tf_pro() && !empty(tfopt( 'tour_popup_extras_text' )) ? tfopt( 'tour_popup_extras_text' ) : '';
						$traveler_details_text = function_exists('is_tf_pro') && is_tf_pro() && !empty(tfopt( 'tour_traveler_details_text' )) ? tfopt( 'tour_traveler_details_text' ) : '';
						if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_extras ) { ?>
                            <div class="tf-booking-content show tf-booking-content-1"> 
								<p><?php echo __( $tour_popup_extra_text, "tourfic" ); ?></p>
                                <div class="tf-booking-content-extra">
									<?php
									if ( ( ! empty( $tour_extras[0]['title'] ) && ! empty( $tour_extras[0]['price'] ) ) || ! empty( $tour_extras[1]['title'] ) && ! empty( $tour_extras[1]['price'] ) ) {
										?>
										<?php foreach ( $tour_extras as $extrakey => $tour_extra ) {
											if ( ! empty( $tour_extra['title'] ) && ! empty( $tour_extra['price'] ) ) {
												$tour_extra_pricetype = ! empty( $tour_extra['price_type'] ) ? $tour_extra['price_type'] : 'fixed';
												?>
                                                <div class="tf-single-tour-extra tour-extra-single">
                                                    <label for="extra<?php echo esc_attr( $extrakey ); ?>">
                                                        <div class="tf-extra-check-box">
                                                            <input type="checkbox" value="<?php echo esc_attr( $extrakey ); ?>" data-title="<?php echo esc_attr( $tour_extra['title'] ); ?>"
                                                                   id="extra<?php echo esc_attr( $extrakey ); ?>" name="tf-tour-extra">
                                                            <span class="checkmark"></span>
                                                        </div>
                                                        <div class="tf-extra-content">
                                                            <h5><?php _e( $tour_extra['title'] ); ?> <?php echo $tour_extra_pricetype == "fixed" ? esc_html( "(Fixed Price)" ) : esc_html( "(Per Person Price)" ); ?>
                                                                <span><?php echo wc_price( $tour_extra['price'] ); ?></span></h5>
															<?php
															if(!empty($tour_extra['desc'])){ ?>
                                                            <p><?php echo esc_html( $tour_extra['desc'] ); ?></p>
															<?php } ?>
                                                        </div>
                                                    </label>
                                                </div>
											<?php }
										} ?>
									<?php } ?>

                                </div>
                            </div>
						<?php }
						if ( $traveller_info_coll ) {
							?>

                            <!-- Popup Traveler Info -->
                            <div class="tf-booking-content tf-booking-content-2 <?php echo empty( $tour_extras ) ? esc_attr( 'show' ) : ''; ?>">
                                <p><?php echo __( $traveler_details_text, "tourfic" ); ?></p>
                                <div class="tf-booking-content-traveller">
                                    <div class="tf-traveller-info-box"></div>
                                </div>
                            </div>
					    <?php }
					    if( function_exists('is_tf_pro') && is_tf_pro() && 3==$tf_booking_by ){
						    ?>

                            <!-- Popup Booking Confirmation -->
                            <div class="tf-booking-content tf-booking-content-3 <?php echo empty( $tour_extras ) && empty( $traveller_info_coll ) ? esc_attr( 'show' ) : ''; ?>">
                                <p><?php echo __( $traveler_details_text, "tourfic" ); ?></p>
                                <div class="tf-booking-content-traveller">
                                    <div class="tf-single-tour-traveller">
                                        <h4><?php echo __( "Billing details", "tourfic" ); ?></h4>
                                        <div class="traveller-info billing-details">
											<?php
											$confirm_book_fields = ! empty( tfopt( 'book-confirm-field' ) ) ? tf_data_types( tfopt( 'book-confirm-field' ) ) : '';
											if ( empty( $confirm_book_fields ) ) {
												?>
                                                <div class="traveller-single-info tf-confirm-fields">
                                                    <label for="tf_first_name"><?php echo __( "First Name", "tourfic" ); ?></label>
                                                    <input type="text" name="booking_confirm[tf_first_name]" id="tf_first_name" data-required="1"/>
                                                    <div class="error-text" data-error-for="tf_first_name"></div>
                                                </div>
                                                <div class="traveller-single-info tf-confirm-fields">
                                                    <label for="tf_last_name"><?php echo __( "Last Name", "tourfic" ); ?></label>
                                                    <input type="text" name="booking_confirm[tf_last_name]" id="tf_last_name" data-required="1"/>
                                                    <div class="error-text" data-error-for="tf_last_name"></div>
                                                </div>
                                                <div class="traveller-single-info tf-confirm-fields">
                                                    <label for="tf_email"><?php echo __( "Email", "tourfic" ); ?></label>
                                                    <input type="email" name="booking_confirm[tf_email]" id="tf_email" data-required="1"/>
                                                    <div class="error-text" data-error-for="tf_email"></div>
                                                </div>
                                                <div class="traveller-single-info tf-confirm-fields">
                                                    <label for="tf_phone"><?php echo __( "Phone", "tourfic" ); ?></label>
                                                    <input type="text" name="booking_confirm[tf_phone]" id="tf_phone" data-required="1"/>
                                                    <div class="error-text" data-error-for="tf_phone"></div>
                                                </div>
                                                <div class="traveller-single-info tf-confirm-fields">
                                                    <label for="tf_country"><?php echo __( "Country", "tourfic" ); ?></label>
                                                    <input type="text" name="booking_confirm[tf_country]" id="tf_country" data-required="1"/>
                                                    <div class="error-text" data-error-for="tf_country"></div>
                                                </div>
                                                <div class="traveller-single-info tf-confirm-fields">
                                                    <label for="tf_street_address"><?php echo __( "Street address", "tourfic" ); ?></label>
                                                    <input type="text" name="booking_confirm[tf_street_address]" id="tf_street_address" data-required="1"/>
                                                    <div class="error-text" data-error-for="tf_street_address"></div>
                                                </div>
                                                <div class="traveller-single-info tf-confirm-fields">
                                                    <label for="tf_town_city"><?php echo __( "Town / City", "tourfic" ); ?></label>
                                                    <input type="text" name="booking_confirm[tf_town_city]" id="tf_town_city" data-required="1"/>
                                                    <div class="error-text" data-error-for="tf_town_city"></div>
                                                </div>
                                                <div class="traveller-single-info tf-confirm-fields">
                                                    <label for="tf_state_country"><?php echo __( "State / County", "tourfic" ); ?></label>
                                                    <input type="text" name="booking_confirm[tf_state_country]" id="tf_state_country" data-required="1"/>
                                                    <div class="error-text" data-error-for="tf_state_country"></div>
                                                </div>
                                                <div class="traveller-single-info tf-confirm-fields">
                                                    <label for="tf_postcode"><?php echo __( "Postcode / ZIP", "tourfic" ); ?></label>
                                                    <input type="text" name="booking_confirm[tf_postcode]" id="tf_postcode" data-required="1"/>
                                                    <div class="error-text" data-error-for="tf_postcode"></div>
                                                </div>
											<?php } else {
												foreach ( $confirm_book_fields as $field ) {
													if ( "text" == $field['reg-fields-type'] || "email" == $field['reg-fields-type'] || "date" == $field['reg-fields-type'] ) { ?>
                                                        <div class="traveller-single-info tf-confirm-fields">
                                                            <label for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"><?php echo esc_html( $field['reg-field-label'] ); ?></label>
                                                            <input type="<?php echo esc_attr( $field['reg-fields-type'] ); ?>" name="booking_confirm[<?php echo esc_attr( $field['reg-field-name'] ); ?>]"
                                                                   id="<?php echo esc_attr( $field['reg-field-name'] ); ?>" data-required="<?php echo $field['reg-field-required']; ?>"/>
                                                            <div class="error-text" data-error-for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"></div>
                                                        </div>
													<?php }
													if ( "select" == $field['reg-fields-type'] && ! empty( $field['reg-options'] ) ) { ?>
                                                        <div class="traveller-single-info tf-confirm-fields">
                                                            <label for="<?php echo esc_attr( $field['reg-field-name'] ); ?>">
																<?php echo esc_html( $field['reg-field-label'] ); ?>
                                                            </label>
                                                            <select name="booking_confirm[<?php echo esc_attr( $field['reg-field-name'] ); ?>]" id="<?php echo esc_attr( $field['reg-field-name'] ); ?>"
                                                                    data-required="<?php echo $field['reg-field-required']; ?>">
                                                                <option value="">
																	<?php echo sprintf( __( 'Select One', 'tourfic' ) ); ?>
                                                                </option>
																<?php
																foreach ( $field['reg-options'] as $sfield ) {
																	if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) { ?>
                                                                        <option value="<?php echo esc_attr( $sfield['option-value'] ); ?>"><?php echo esc_html( $sfield['option-label'] ); ?></option>
																	<?php }
																} ?>
                                                            </select>
                                                            <div class="error-text" data-error-for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"></div>
                                                        </div>
													<?php }
													if ( ( "checkbox" == $field['reg-fields-type'] || "radio" == $field['reg-fields-type'] ) && ! empty( $field['reg-options'] ) ) { ?>
                                                        <div class="traveller-single-info tf-confirm-fields">
                                                            <label for="<?php echo esc_attr( $field['reg-field-name'] ); ?>">
																<?php echo esc_html( $field['reg-field-label'] ); ?>
                                                            </label>
															<?php
															foreach ( $field['reg-options'] as $sfield ) {
																if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) { ?>
                                                                    <div class="tf-single-checkbox">
                                                                        <input type="<?php echo esc_attr( $field['reg-fields-type'] ); ?>" name="booking_confirm[<?php echo esc_attr( $field['reg-field-name'] ); ?>][]"
                                                                               id="<?php echo esc_attr( $sfield['option-value'] ); ?>" value="<?php echo esc_html( $sfield['option-value'] ); ?>"
                                                                               data-required="<?php echo $field['reg-field-required']; ?>"/>
                                                                        <label for="<?php echo esc_attr( $sfield['option-value'] ); ?>">
																			<?php echo sprintf( __( '%s', 'tourfic' ), $sfield['option-label'] ); ?>
                                                                        </label>
                                                                    </div>
																<?php }
															} ?>
                                                            <div class="error-text" data-error-for="<?php echo esc_attr( $field['reg-field-name'] ); ?>"></div>
                                                        </div>
													<?php }
												}
											} ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<?php } ?>

                        <!-- Popup Booking Summery -->
                        <div class="tf-booking-summery" style="<?php echo empty($tour_extras) && empty($traveller_info_coll) && 3!=$tf_booking_by ? esc_attr( "width: 100%;" ) : ''; ?>">
                            <div class="tf-booking-fixed-summery">
                                <h5><?php echo __( "Booking summery", "tourfic" ); ?></h5>
                                <h4><?php echo get_the_title( $post_id ); ?></h4>
                            </div>
                            <div class="tf-booking-traveller-info">

                            </div>
                        </div>
                    </div>

                    <!-- Popup Footer Control & Partial Payment -->
                    <div class="tf-booking-pagination">
					    <?php if ( function_exists('is_tf_pro') && is_tf_pro() && ! empty( $meta['allow_deposit'] ) && $meta['allow_deposit'] == '1' && ! empty( $meta['deposit_amount'] ) && 3!=$tf_booking_by ) {
						    $tf_deposit_amount =  array (
								"{amount}" => $meta['deposit_type'] == 'fixed' ? wc_price( $meta['deposit_amount'] ) : $meta['deposit_amount']. '%'
							);
							$tf_partial_payment_label = !empty(tfopt("deposit-title")) ? tfopt("deposit-title") : 'Pertial payment of {amount} on total';
							$tf_partial_payment_description = !empty(tfopt("deposit-subtitle")) ? tfopt("deposit-subtitle") : 'You can Partial Payment amount for booking the tour. After booking the tour, you can pay the rest amount after the tour is completed.';
						    ?>
                            <div class="tf-diposit-switcher">
                                <label class="switch">
                                    <input type="checkbox" name="deposit" class="diposit-status-switcher">
                                    <span class="switcher round"></span>
                                </label>
								<div class="tooltip-box">
									<h4><?php echo __( partial_payment_tag_replacement($tf_partial_payment_label, $tf_deposit_amount), 'tourfic' ) ?></h4>
									<div class="tf-info-btn">
										<i class="fa fa-circle-exclamation tooltip-title-box" style="padding-left: 5px; padding-top: 5px" title=""></i>
										<div class="tf-tooltip"><?php echo __($tf_partial_payment_description) ?></div>
									</div>
								</div>
                            </div>
					    <?php } ?>
					    <?php if ( empty($tour_extras) && 3!=$tf_booking_by && empty($traveller_info_coll) ){ ?>
                            <div class="tf-control-pagination show">
                                <button type="submit"><?php echo __( "Continue", "tourfic" ); ?></button>
                            </div>
							<?php
						}
						if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( $tour_extras ) ) { ?>
                            <div class="tf-control-pagination show tf-pagination-content-1">
							    <?php
							    if( 3!=$tf_booking_by && empty($traveller_info_coll) ){ ?>
                                    <button type="submit"><?php echo __("Continue", "tourfic"); ?></button>
							    <?php }else{ ?>
                                    <a href="#" class="tf-next-control tf-tabs-control" data-step="<?php echo 3==$tf_booking_by && empty($traveller_info_coll) ? esc_attr("3") : esc_attr("2"); ?>"><?php echo __("Continue", "tourfic"); ?></a>
							    <?php } ?>
                            </div>
						<?php }
						if ( $traveller_info_coll ) { ?>

                            <!-- Popup Traveler Info -->
                            <div class="tf-control-pagination tf-pagination-content-2 <?php echo empty($tour_extras) ? esc_attr( 'show' ) : ''; ?>">
							    <?php
							    if ( function_exists('is_tf_pro') && is_tf_pro() && $tour_extras ) {  ?>
                                    <a href="#" class="tf-back-control tf-step-back" data-step="1"><i class="fa fa-angle-left"></i><?php echo __("Back", "tourfic"); ?></a>
							    <?php }
							    if( function_exists('is_tf_pro') && is_tf_pro() && 3==$tf_booking_by ){
								    ?>
                                    <a href="#" class="tf-next-control tf-tabs-control tf-traveller-error" data-step="3"><?php echo __("Continue", "tourfic"); ?></a>
							    <?php }else { ?>
                                    <button type="submit" class="tf-traveller-error"><?php echo __("Continue", "tourfic"); ?></button>
							    <?php } ?>
                            </div>
					    <?php }
					    if( function_exists('is_tf_pro') && is_tf_pro() && 3==$tf_booking_by ){
						    ?>

                            <!-- Popup Booking Confirmation -->
                            <div class="tf-control-pagination tf-pagination-content-3 <?php echo empty( $tour_extras ) && empty( $traveller_info_coll ) ? esc_attr( 'show' ) : ''; ?>">
								<?php
								if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( $tour_extras || $traveller_info_coll ) ) { ?>
                                    <a href="#" class="tf-back-control tf-step-back" data-step="2"><i class="fa fa-angle-left"></i><?php echo __( "Back", "tourfic" ); ?></a>
								<?php } ?>
                                <button type="submit" class="tf-book-confirm-error"><?php echo __( "Continue", "tourfic" ); ?></button>
                            </div>
						<?php } ?>
                    </div>
                </div>
            </div>
			<?php
		}
	}
	ob_start();
	if ( $tf_tour_selected_template == "design-1" ) :
		?>
        <form class="tf_tours_booking">
            <div class="tf-field-group tf-mt-8">
                <i class="fa-sharp fa-solid fa-calendar-days"></i>
                <input type='text' name='check-in-out-date' id='check-in-out-date' class='tf-field tours-check-in-out' onkeypress="return false;" placeholder='<?php _e( "Select Date", "tourfic" ); ?>' value='' required/>
            </div>
			<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type != 'fixed' ) { ?>
                <div class="tf-field-group check-in-time-div tf-mt-8" id="" style="display: none;">
                    <i class="fa-regular fa-clock"></i>
                    <select class="tf-field" name="check-in-time" id="" style="min-width: 100px;"></select>
                </div>
			<?php } ?>

            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <div class="tf-booking-person tf-mt-30">
                <div class="tf-form-title">
                    <p><?php _e( "Person Info", "tourfic" ); ?></p>
                </div>
				<?php if ( $custom_avail == true || ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                    <div class="tf-field-group tf-mt-16 tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <i class="fa-regular fa-user"></i>
								<?php _e( 'Adults', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="adults" id="adults" min="0" value="<?php echo ! empty( $adults ) ? $adults : '0'; ?>">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
				<?php } ?>

				<?php if ( $custom_avail == true || ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                    <div class="tf-field-group tf-mt-16 tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <i class="fa-solid fa-child"></i>
								<?php _e( 'Children', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0'; ?>">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
				<?php } ?>
				<?php if ( $custom_avail == true || ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                    <div class="tf-field-group tf-mt-16 tf_acrselection">
                        <div class="tf-field tf-flex">
                            <div class="acr-label tf-flex">
                                <i class="fa-solid fa-baby"></i>
								<?php _e( 'Infant', 'tourfic' ); ?>
                            </div>
                            <div class="acr-select">
                                <div class="acr-dec">-</div>
                                <input type="number" name="infants" id="infant" min="0" value="<?php echo ! empty( $infant ) ? $infant : '0'; ?>">
                                <div class="acr-inc">+</div>
                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>

            <div class="tf-tours-booking-btn tf-booking-bttns tf-mt-30">
                <div class="tf-btn">
                    <a href="#" class="tf-btn-normal btn-primary tf-booking-popup-btn" type="submit"><?php _e( 'Book Now', 'tourfic' ); ?></a>
                </div>
				<?php echo tf_booking_popup( $post_id ); ?>
            </div>

            <!-- bottom bar -->
            <div class="tf-bottom-booking-bar">
                <div class="tf-bottom-booking-fields">
                    <div class="tf_selectperson-wrap tf-bottom-booking-field">
                        <div class="tf-bottom-booking-field-icon">
                            <i class="ri-user-line"></i>
                        </div>
                        <div class="tf_input-inner">
			                <?php if ( $custom_avail == true || ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                <div class="adults-text"><?php echo ( ! empty( $adults ) ? $adults : '0' ) . ' ' . __( "Adults", "tourfic" ); ?></div>
			                <?php } ?>
			                <?php if ( $custom_avail == true || ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                <div class="person-sep"></div>
                                <div class="child-text"><?php echo ( ! empty( $child ) ? $child : '0' ) . ' ' . __( "Children", "tourfic" ); ?></div>
			                <?php } ?>
			                <?php if ( $custom_avail == true || ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                <div class="person-sep"></div>
                                <div class="infant-text"><?php echo ( ! empty( $infant ) ? $infant : '0' ) . ' ' . __( "Infant", "tourfic" ); ?></div>
			                <?php } ?>
                        </div>
                        <div class="tf_acrselection-wrap" style="display: none;">
                            <div class="tf_acrselection-inner">
				                <?php if ( $custom_avail == true || ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php _e( 'Adults', 'tourfic' ); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="adults" id="adults" min="0" value="<?php echo ! empty( $adults ) ? $adults : '0'; ?>">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
				                <?php } ?>
				                <?php if ( $custom_avail == true || ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0'; ?>">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
				                <?php } ?>
				                <?php if ( $custom_avail == true || ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                    <div class="tf_acrselection">
                                        <div class="acr-label"><?php _e( 'Infant', 'tourfic' ); ?></div>
                                        <div class="acr-select">
                                            <div class="acr-dec">-</div>
                                            <input type="number" name="infants" id="infant" min="0" value="<?php echo ! empty( $infant ) ? $infant : '0'; ?>">
                                            <div class="acr-inc">+</div>
                                        </div>
                                    </div>
				                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="tf-bottom-booking-field">
                        <div class="tf-bottom-booking-field-icon">
                            <i class="ri-calendar-todo-line"></i>
                        </div>
                        <input type="text" class="tf-field tours-check-in-out" placeholder="<?php _e( "Select Date", "tourfic" ); ?>" value="" required/>
                    </div>

	                <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type != 'fixed' ) { ?>
                        <div class="tf-bottom-booking-field check-in-time-div" id="" style="display: none;">
                            <div class="tf-bottom-booking-field-icon">
                                <i class="ri-time-line"></i>
                            </div>
                            <select class="tf-field" name="check-in-time" id=""></select>
                        </div>
	                <?php } ?>
                </div>

                <div class="tf-tours-booking-btn tf-booking-bttns">
                    <div class="tf-btn">
                        <a href="#" class="tf-btn-normal btn-primary tf-booking-popup-btn" type="submit"><?php _e( 'Book Now', 'tourfic' ); ?></a>
                        <a href="#" class="tf-btn-normal btn-primary tf-booking-mobile-btn"><?php _e( 'Book Now', 'tourfic' ); ?></a>
                    </div>
		            <?php //echo tf_booking_popup( $post_id ); ?>
                </div>
            </div>
            <script>
                (function ($) {
                    $(document).ready(function () {

                        const allowed_times = JSON.parse('<?php echo wp_json_encode( $allowed_times ?? [] ) ?>');
                        const custom_avail = '<?php echo $custom_avail; ?>';
                        if (custom_avail == false && allowed_times.length > 0) {
                            populateTimeSelect(allowed_times)
                        }

                        function populateTimeSelect(times) {
                            let timeSelect = $('select[name="check-in-time"]');
                            let timeSelectDiv = $(".check-in-time-div");
                            timeSelect.empty();
                            if (times.length > 0) {
                                timeSelect.append(`<option value="" selected hidden><?php _e( "Select Time", "tourfic" ); ?></option>`);
                                $.each(times, function (i, v) {
                                    timeSelect.append(`<option value="${i}">${v}</option>`);
                                });
                                timeSelectDiv.show();
                            } else timeSelectDiv.hide();
                        }

                        $(".tours-check-in-out").flatpickr({
                            enableTime: false,
                            dateFormat: "Y/m/d",
							altInput: true,
                			altFormat: '<?php echo $tour_date_format_for_users; ?>',
					        <?php
					        // Flatpickt locale for translation
					        tf_flatpickr_locale();

					        if ($tour_type && $tour_type == 'fixed') { ?>

                            mode: "range",
                            defaultDate: ["<?php echo $departure_date; ?>", "<?php echo $return_date; ?>"],
                            enable: [
                                {
                                    from: "<?php echo $departure_date; ?>",
                                    to: "<?php echo $return_date; ?>"
                                }
                            ],
                            onReady: function (selectedDates, dateStr, instance) {
                                instance.element.value = dateStr.replace(/[a-z]+/g, '-');
								instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                            },

					        <?php } elseif ($tour_type && $tour_type == 'continuous'){ ?>

                            minDate: "today",
                            disableMobile: "true",

					        <?php if ($custom_avail && $custom_avail == true){ ?>

                            enable: [

						        <?php foreach ( $cont_custom_date as $item ) {
						        echo '{
                                            from: "' . $item["date"]["from"] . '",
                                            to: "' . $item["date"]["to"] . '"
                                        },';
					        } ?>
                            ],

					        <?php }
					        if ($custom_avail == false) {
					        if ($disabled_day || $disable_range || $disable_specific || $disable_same_day) {
					        ?>
                            "disable": [
						        <?php if ($disabled_day) { ?>
                                function (date) {
                                    return (date.getDay() === 8 <?php foreach ( $disabled_day as $dis_day ) {
								        echo '|| date.getDay() === ' . $dis_day . ' ';
							        } ?>);
                                },
						        <?php }
						        if ( $disable_range ) {
							        foreach ( $disable_range as $d_item ) {
								        echo '{
                                                    from: "' . $d_item["date"]["from"] . '",
                                                    to: "' . $d_item["date"]["to"] . '"
                                                },';
							        }
						        }
								if ($disable_same_day) {
									echo '"today"';
									if ($disable_specific) {
										echo ",";
									}
								}
						        if ( $disable_specific ) {
							        echo '"' . $disable_specific . '"';
						        }
						        ?>
                            ],
					        <?php
					        }
					        }
					        }
					        ?>

                            onChange: function (selectedDates, dateStr, instance) {
								instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
								$(".tours-check-in-out").val(instance.altInput.value);
                                $('.tours-check-in-out[type="hidden"]').val(dateStr.replace(/[a-z]+/g, '-') );
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

                        $("select[name='check-in-time']").on("change", function () {
                            var selectedTime = $(this).val();
                            $("select[name='check-in-time']").not(this).val(selectedTime);
                        });

                        $(".acr-select input[type='number']").on("change", function () {
                            var inputName = $(this).attr("name");
                            var selectedValue = $(this).val();

                            // Update all inputs with the same name
                            $(".acr-select input[type='number'][name='" + inputName + "']").val(selectedValue)
                        });


                    });
                })(jQuery);
            </script>
        </form>
	<?php else : ?>
        <div class="tf-tour-booking-wrap">
            <form class="tf_tours_booking">
                <div class="tf_selectperson-wrap">
                    <div class="tf_input-inner">
                        <span class="tf_person-icon">
                            <i class="fas fa-user"></i>
                        </span>
						<?php if ( $custom_avail == true || ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                            <div class="adults-text"><?php echo ( ! empty( $adults ) ? $adults : '0' ) . ' ' . __( "Adults", "tourfic" ); ?></div>
						<?php } ?>
						<?php if ( $custom_avail == true || ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                            <div class="person-sep"></div>
                            <div class="child-text"><?php echo ( ! empty( $child ) ? $child : '0' ) . ' ' . __( "Children", "tourfic" ); ?></div>
						<?php } ?>
						<?php if ( $custom_avail == true || ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                            <div class="person-sep"></div>
                            <div class="infant-text"><?php echo ( ! empty( $infant ) ? $infant : '0' ) . ' ' . __( "Infant", "tourfic" ); ?></div>
						<?php } ?>
                    </div>
                    <div class="tf_acrselection-wrap" style="display: none;">
                        <div class="tf_acrselection-inner">
							<?php if ( $custom_avail == true || ( ! $disable_adult_price && $pricing_rule == 'person' && $adult_price != false ) || ( ! $disable_adult_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Adults', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="adults" id="adults" min="0" value="<?php echo ! empty( $adults ) ? $adults : '0'; ?>">
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
							<?php } ?>
							<?php if ( $custom_avail == true || ( ! $disable_child_price && $pricing_rule == 'person' && $child_price != false ) || ( ! $disable_child_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Children', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="childrens" id="children" min="0" value="<?php echo ! empty( $child ) ? $child : '0'; ?>">
                                        <div class="acr-inc">+</div>
                                    </div>
                                </div>
							<?php } ?>
							<?php if ( $custom_avail == true || ( ! $disable_infant_price && $pricing_rule == 'person' && $infant_price != false ) || ( ! $disable_infant_price && $pricing_rule == 'group' && $group_price != false ) ) { ?>
                                <div class="tf_acrselection">
                                    <div class="acr-label"><?php _e( 'Infant', 'tourfic' ); ?></div>
                                    <div class="acr-select">
                                        <div class="acr-dec">-</div>
                                        <input type="number" name="infants" id="infant" min="0" value="<?php echo ! empty( $infant ) ? $infant : '0'; ?>">
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
                            <input type='text' name='check-in-out-date' id='check-in-out-date' class='tours-check-in-out' onkeypress="return false;" placeholder='<?php _e( "Select Date", "tourfic" ); ?>' value=''
                                   required/>
                        </div>
                    </label>
                </div>

				<?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type != 'fixed' ) { ?>
                    <div class='tf_form-row check-in-time-div' id="" style="display: none;">
                        <label class='tf_label-row'>
                            <div class='tf_form-inner'>
                                <select name="check-in-time" id="" style="min-width: 100px;">
                                </select>
                            </div>
                        </label>
                    </div>
				<?php } ?>

                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <div class="tf-tours-booking-btn">
                    <div class="tf-btn">
                        <a href="#" class="tf_button btn-styled tf-booking-popup-btn"><?php _e('Book Now', 'tourfic'); ?></a>
                    </div>
                </div>
				<?php echo tf_booking_popup( $post_id ); ?>
            </form>
        </div>
        <script>
            (function ($) {
                $(document).ready(function () {

                    const allowed_times = JSON.parse('<?php echo wp_json_encode( $allowed_times ?? [] ) ?>');
                    const custom_avail = '<?php echo $custom_avail; ?>';
                    if (custom_avail == false && allowed_times.length > 0) {
                        populateTimeSelect(allowed_times)
                    }

                    function populateTimeSelect(times) {
                        let timeSelect = $('select[name="check-in-time"]');
                        let timeSelectDiv = $(".check-in-time-div");
                        timeSelect.empty();
                        if (times.length > 0) {
                            timeSelect.append(`<option value="" selected hidden><?php _e( "Select Time", "tourfic" ); ?></option>`);
                            $.each(times, function (i, v) {
                                timeSelect.append(`<option value="${i}">${v}</option>`);
                            });
                            timeSelectDiv.show();
                        } else timeSelectDiv.hide();
                    }

					// Here --- 1
                    $("#check-in-out-date").flatpickr({
                        enableTime: false,
                        dateFormat: "Y/m/d",
                        altInput: true,
                        altFormat: '<?php echo $tour_date_format_for_users; ?>',
						<?php
						// Flatpickt locale for translation
						tf_flatpickr_locale();

						if ($tour_type && $tour_type == 'fixed') { ?>

                    mode: "range",
                    defaultDate: ["<?php echo $departure_date; ?>", "<?php echo $return_date; ?>"],
                    enable: [
                        {
                            from: "<?php echo $departure_date; ?>",
                            to: "<?php echo $return_date; ?>"
                        }
                    ],
                    onReady: function (selectedDates, dateStr, instance) {
						instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                        instance.element.value = instance.altInput.value
                    },

						<?php } elseif ($tour_type && $tour_type == 'continuous'){ ?>

                        minDate: "today",
                        disableMobile: "true",

						<?php if ($custom_avail && $custom_avail == true){ ?>

                        enable: [

							<?php foreach ( $cont_custom_date as $item ) {
							echo '{
                            from: "' . $item["date"]["from"] . '",
                            to: "' . $item["date"]["to"] . '"
                        },';
						} ?>

                        ],

						<?php }
						if ($custom_avail == false) {
						if ($disabled_day || $disable_range || $disable_specific || $disable_same_day) {
						?>

                        "disable": [
							<?php if ($disabled_day) { ?>
                            function (date) {
                                return (date.getDay() === 8 <?php foreach ( $disabled_day as $dis_day ) {
									echo '|| date.getDay() === ' . $dis_day . ' ';
								} ?>);
                            },
							<?php }
							if ( $disable_range ) {
								foreach ( $disable_range as $d_item ) {
									echo '{
                                from: "' . $d_item["date"]["from"] . '",
                                to: "' . $d_item["date"]["to"] . '"
                            },';
								}
							}
							if ($disable_same_day) {
								echo '"today"';
								if ($disable_specific) {
									echo ",";
								}
							}
							if ( $disable_specific ) {
								echo '"' . $disable_specific . '"';
							}
							?>
                        ],
						<?php
						}
						}

						}
						?>

                        onChange: function (selectedDates, dateStr, instance) {
                            instance.altInput.value = instance.altInput.value.replace(/[a-z]+/g, '-');
                            $(".tours-check-in-out").not(this).val(instance.altInput.value); // Todo: change the Input Value
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
	<?php endif; ?>


    <script>
        (function ($) {
            $(document).on('click', "#tour-deposit > div > div.tf_button_group > button", function (e) {
                e.preventDefault();
                var form = $(document).find('form.tf_tours_booking');
                var has_deposit = $(this).data('deposit');
                if (has_deposit === true) {
                    form.find('input[name="deposit"]').val(1);
                } else {
                    form.find('input[name="deposit"]').val(0);
                }
                form.submit();
            });
        })(jQuery);

    </script>
	<?php
	return ob_get_clean();
}

#################################
# Layouts                       #
#################################

/**
 * Tours Archive
 */
function tf_tour_archive_single_item( $adults = '', $child = '', $check_in_out = '', $startprice = '', $endprice = '' ) {

	// get post id
	$post_id = get_the_ID();
	//Get hotel meta values
	$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
	// Location
	$location = ! empty( $meta['text_location'] ) ? $meta['text_location'] : '';
	// Featured
	$featured = ! empty( $meta['tour_as_featured'] ) ? $meta['tour_as_featured'] : '';

	// Adults
	if ( empty( $adults ) ) {
		$adults = ! empty( $_GET['adults'] ) ? sanitize_text_field( $_GET['adults'] ) : '';
	}
	// children
	if ( empty( $child ) ) {
		$child = ! empty( $_GET['children'] ) ? sanitize_text_field( $_GET['children'] ) : '';
	}
	// room
	$infant = ! empty( $_GET['infant'] ) ? sanitize_text_field( $_GET['infant'] ) : '';
	// Check-in & out date
	if ( empty( $check_in_out ) ) {
		$check_in_out = ! empty( $_GET['check-in-out-date'] ) ? sanitize_text_field( $_GET['check-in-out-date'] ) : '';
	}

    $disable_adult_price  = !empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
    $disable_child_price  = !empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
    $disable_infant_price = !empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
    $pricing_rule         = !empty( $meta['pricing'] ) ? $meta['pricing'] : '';
    $custom_pricing_by_rule = !empty( $meta['custom_pricing_by'] ) ? $meta['custom_pricing_by'] : '';
    $group_price          = !empty( $meta['group_price'] ) ? $meta['group_price'] : false;
    $adult_price          = !empty( $meta['adult_price'] ) ? $meta['adult_price'] : false;
    $child_price          = !empty( $meta['child_price'] ) ? $meta['child_price'] : false;
    $infant_price         = !empty( $meta['infant_price'] ) ? $meta['infant_price'] : false;
    $tour_archive_page_price_settings = !empty(tfopt('tf-template')["tour_archive_price_minimum_settings"]) ? tfopt('tf-template')["tour_archive_price_minimum_settings"] : 'all';

	if ( ! empty( $check_in_out ) ) {
		list( $tf_form_start, $tf_form_end ) = explode( ' - ', $check_in_out );
	}

	if ( ! empty( $check_in_out ) ) {
		$period = new DatePeriod(
			new DateTime( $tf_form_start ),
			new DateInterval( 'P1D' ),
			new DateTime( ! empty( $tf_form_end ) ? $tf_form_end : $tf_form_start . '23:59' )
		);
	} else {
		$period = '';
	}


	// Single link
	$url = get_the_permalink();
	$url = add_query_arg( array(
		'adults'   => $adults,
		'children' => $child,
		'infant'   => $infant
	), $url );

    // Tour Starting Price
    $tour_price = [];
    if( $pricing_rule  && $pricing_rule == 'group' ){
        if(!empty($check_in_out)){
            if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {
                $custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
                if ($custom_availability) {
                    foreach ( $meta['cont_custom_date'] as $repval ) {
                        //Initial matching date array
                        $show_tour = [];
                        $dates = $repval['date'];
                        // Check if any date range match with search form date range and set them on array
                        if ( ! empty( $period ) ) {
                            foreach ( $period as $date ) {
                                $show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
                            }
                        }
                        if ( ! in_array( 0, $show_tour ) ) {
                            if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'group' ){
                                if(! empty( $repval['group_price'] )){
                                    $tour_price[] = $repval['group_price'];
                                }
                            }
                            if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'person' ){
                                if($tour_archive_page_price_settings == "all") {
                                    if(!empty($repval['adult_price']) && !$disable_adult_price){
                                        $tour_price[] = $repval['adult_price'];
                                    }
                                    if(!empty($repval['child_price']) && !$disable_child_price){
                                        $tour_price[] = $repval['child_price'];
                                    }
                                    if(!empty($repval['infant_price']) && !$disable_infant_price){
                                        $tour_price[] = $repval['infant_price'];
                                    }
                                }
                                if($tour_archive_page_price_settings == "adult") {
                                    if(!empty($repval['adult_price']) && !$disable_adult_price){
                                        $tour_price[] = $repval['adult_price'];
                                    }
                                }
                                if($tour_archive_page_price_settings == "child") {
                                    if(!empty($repval['child_price']) && !$disable_child_price){
                                        $tour_price[] = $repval['child_price'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }else{
            if(!empty($meta['group_price'])){
                $tour_price[] = $meta['group_price'];
            }
        }
    }
    if( $pricing_rule  && $pricing_rule == 'person' ){
        if(!empty($check_in_out)){
            if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {
                $custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
                if ($custom_availability) {
                    foreach ( $meta['cont_custom_date'] as $repval ) {
                        //Initial matching date array   
                        $show_tour = [];
                        $dates = $repval['date'];
                        // Check if any date range match with search form date range and set them on array
                        if ( ! empty( $period ) ) {
                            foreach ( $period as $date ) {
                                $show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
                            }
                        }
                        if ( ! in_array( 0, $show_tour ) ) { 
                            if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'group' ){
                                if(! empty( $repval['group_price'] )){
                                    $tour_price[] = $repval['group_price'];
                                }
                            }
                            if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'person' ){   
                                if($tour_archive_page_price_settings == "all") {
                                    if(!empty($repval['adult_price']) && !$disable_adult_price){
                                        $tour_price[] = $repval['adult_price'];
                                    }
                                    if(!empty($repval['child_price']) && !$disable_child_price){
                                        $tour_price[] = $repval['child_price'];
                                    }
                                    if(!empty($repval['infant_price']) && !$disable_infant_price){
                                        $tour_price[] = $repval['infant_price'];
                                    }
                                }
                                if($tour_archive_page_price_settings == "adult") {
                                    if(!empty($repval['adult_price']) && !$disable_adult_price){
                                        $tour_price[] = $repval['adult_price'];
                                    }
                                }
                                if($tour_archive_page_price_settings == "child") {
                                    if(!empty($repval['child_price']) && !$disable_child_price){
                                        $tour_price[] = $repval['child_price'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else{
            if($tour_archive_page_price_settings == "all") {
                if(!empty($meta['adult_price']) && !$disable_adult_price){
                    $tour_price[] = $meta['adult_price'];
                }
                if(!empty($meta['child_price']) && !$disable_child_price){
                    $tour_price[] = $meta['child_price'];
                }
                if(!empty($meta['infant_price']) && !$disable_infant_price){
                    $tour_price[] = $meta['infant_price'];
                }
            } 
            if($tour_archive_page_price_settings == "adult"){
                if(!empty($meta['adult_price']) && !$disable_adult_price){
                    $tour_price[] = $meta['adult_price'];
                }
            }
            if($tour_archive_page_price_settings == "child"){
                if(!empty($meta['child_price']) && !$disable_child_price){
                    $tour_price[] = $meta['child_price'];
                }
            }
        }
    }
    $tf_plugin_installed = get_option('tourfic_template_installed'); 
    if (!empty($tf_plugin_installed)) {
        $tf_tour_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['tour-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['tour-archive'] : 'design-1';
    }else{
        $tf_tour_arc_selected_template = ! empty( tf_data_types(tfopt( 'tf-template' ))['tour-archive'] ) ?  tf_data_types(tfopt( 'tf-template' ))['tour-archive'] : 'default';
    }
    if( $tf_tour_arc_selected_template=="design-1"){
    ?>
    <div class="tf-item-card tf-flex">
        <div class="tf-item-featured">
            <a href="<?php echo esc_url($url); ?>">
            <?php
                if (has_post_thumbnail()) {
                    the_post_thumbnail( 'full' );
                } else {
                    echo '<img src="' .TF_ASSETS_APP_URL . "images/feature-default.jpg". '" class="attachment-full size-full wp-post-image">';
                }
            ?>
            </a>
            <?php 
            $tf_discount_type = !empty($meta['discount_type']) ? $meta['discount_type'] : '';
            $tf_discount_amount = !empty($meta['discount_price']) ? $meta['discount_price'] : '';
            ?>
            <div class="tf-features-box tf-flex">
                <?php 
                if( !empty($tf_discount_type) && $tf_discount_type!="none" && !empty($tf_discount_amount) ){
                ?>
                <div class="tf-discount"><?php echo $tf_discount_type == "percent" ? $tf_discount_amount."%" : wc_price($tf_discount_amount); ?> <?php _e("Off", "tourfic"); ?></div>
                <?php } ?>

                <?php if( $featured ): ?>
                    <div class="tf-feature">
                    <?php 
						echo !empty( $meta['featured_text'] ) ? $meta['featured_text'] : esc_html( "HOT DEAL" );
					?>    
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="tf-item-details">
            <?php 
            if(!empty($location)){
            ?>
            <div class="tf-title-meta tf-flex tf-flex-align-center tf-flex-gap-8">
                <i class="fa-solid fa-location-dot"></i>
                <p><?php echo $location; ?></p>
            </div>
            <?php } ?>
            <div class="tf-title tf-mt-16">
                <h2><a href="<?php echo esc_url($url); ?>"><?php the_title();?></a></h2>
            </div>
            
            <?php tf_archive_single_rating();?>
            
            <div class="tf-details tf-mt-16">
                <p><?php echo substr(wp_strip_all_tags(get_the_content()), 0, 100). '...'; ?></p>
            </div>
            <div class="tf-post-footer tf-flex tf-flex-align-center tf-flex-space-bttn tf-mt-16">
                <div class="tf-pricing">

                <?php
                if ( ! empty( $tour_price ) ):
                    $lowest_price = wc_price( min( $tour_price ) );
                    echo __( "From ", "tourfic" ) . $lowest_price;
                endif; ?>

                </div>
                <div class="tf-booking-bttns">
                    <a class="tf-btn-normal btn-secondary" href="<?php echo esc_url($url); ?>"><?php _e("View Details","tourfic"); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php
	} else {
		?>
        <div class="single-tour-wrap <?php echo $featured ? esc_attr( 'tf-featured' ) : '' ?>">
            <div class="single-tour-inner">
				<?php if ( $featured ): ?>
                    <div class="tf-featured-badge">
                        <span><?php echo ! empty( $meta['featured_text'] ) ? $meta['featured_text'] : esc_html( "HOT DEAL" ); ?></span>
                    </div>
				<?php endif; ?>
                <div class="tourfic-single-left">
                    <a href="<?php echo esc_url( $url ); ?>">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'full' );
						} else {
							echo '<img width="100%" height="100%" src="' . TF_ASSETS_APP_URL . "images/feature-default.jpg" . '" class="attachment-full size-full wp-post-image">';
						}
						?>
                    </a>
                </div>
                <div class="tourfic-single-right">
                    <div class="tf_property_block_main_row">
                        <div class="tf_item_main_block">
                            <div class="tf-hotel__title-wrap tf-tours-title-wrap">
                                <a href="<?php echo $url; ?>"><h3 class="tourfic_hotel-title"><?php the_title(); ?></h3></a>
                            </div>
							<?php
							if ( $location ) {
								echo '<div class="tf-map-link">';
								echo '<span class="tf-d-ib"><i class="fas fa-map-marker-alt"></i> ' . $location . '</span>';
								echo '</div>';
							}
							?>
                        </div>
						<?php tf_archive_single_rating(); ?>
                    </div>
                    <div class="tf-tour-desc">
                        <p><?php echo substr( wp_strip_all_tags( get_the_content() ), 0, 160 ) . '...'; ?></p>
                    </div>

                    <div class="availability-btn-area tour-search">
                        <a href="<?php echo $url; ?>" class="tf_button btn-styled"><?php esc_html_e( 'View Details', 'tourfic' ); ?></a>
                    </div>

					<?php
					$tour_price = [];
					if( $pricing_rule  && $pricing_rule == 'group' ){
						if(!empty($check_in_out)){
							if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {
								$custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
								if ($custom_availability) {
									foreach ( $meta['cont_custom_date'] as $repval ) {
										//Initial matching date array
										$show_tour = [];
										$dates = $repval['date'];
										// Check if any date range match with search form date range and set them on array
										if ( ! empty( $period ) ) {
											foreach ( $period as $date ) {
												$show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
											}
										}
										if ( ! in_array( 0, $show_tour ) ) {
											if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'group' ){
												if(! empty( $repval['group_price'] )){
													$tour_price[] = $repval['group_price'];
												}
											}
											if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'person' ){
												if($tour_archive_page_price_settings == "all") {
													if(!empty($repval['adult_price']) && !$disable_adult_price){
														$tour_price[] = $repval['adult_price'];
													}
													if(!empty($repval['child_price']) && !$disable_adult_price){
														$tour_price[] = $repval['child_price'];
													}
												}
												if($tour_archive_page_price_settings == "adult") {
													if(!empty($repval['adult_price']) && !$disable_adult_price){
														$tour_price[] = $repval['adult_price'];
													}
												}
												if($tour_archive_page_price_settings == "child") {
													if(!empty($repval['child_price']) && !$disable_adult_price){
														$tour_price[] = $repval['child_price'];
													}
												}
											}
										}
									}
								}
							}
						}else{
							if(!empty($meta['group_price'])){
								$tour_price[] = $meta['group_price'];
							}
						}
					}
					if( $pricing_rule  && $pricing_rule == 'person' ){
						if(!empty($check_in_out)){
							if ( !empty($meta['type'] ) && $meta['type'] === 'continuous' ) {
								$custom_availability = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
								if ($custom_availability) {
									foreach ( $meta['cont_custom_date'] as $repval ) {
										//Initial matching date array
										$show_tour = [];
										$dates = $repval['date'];
										// Check if any date range match with search form date range and set them on array
										if ( ! empty( $period ) ) {
											foreach ( $period as $date ) {
												$show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
											}
										}
										if ( ! in_array( 0, $show_tour ) ) {
											if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'group' ){
												if(! empty( $repval['group_price'] )){
													$tour_price[] = $repval['group_price'];
												}
											}
											if( $custom_pricing_by_rule  && $custom_pricing_by_rule == 'person' ){
												if(!empty($repval['adult_price']) && !$disable_adult_price){
													if($tour_archive_page_price_settings == "all") {
														if(!empty($repval['adult_price']) && !$disable_adult_price){
															$tour_price[] = $repval['adult_price'];
														}
														if(!empty($repval['child_price']) && !$disable_adult_price){
															$tour_price[] = $repval['child_price'];
														}
													}
													if($tour_archive_page_price_settings == "adult") {
														if(!empty($repval['adult_price']) && !$disable_adult_price){
															$tour_price[] = $repval['adult_price'];
														}
													}
													if($tour_archive_page_price_settings == "child") {
														if(!empty($repval['child_price']) && !$disable_adult_price){
															$tour_price[] = $repval['child_price'];
														}
													}
												}
											}
										}
									}
								}
							}
						}else{
							if($tour_archive_page_price_settings == "all") {
								if(!empty($meta['adult_price']) && !$disable_adult_price){
									$tour_price[] = $meta['adult_price'];
								}
								if(!empty($meta['child_price']) && !$disable_adult_price){
									$tour_price[] = $meta['child_price'];
								}
							}
							if($tour_archive_page_price_settings == "adult") {
								if(!empty($meta['adult_price']) && !$disable_adult_price){
									$tour_price[] = $meta['adult_price'];
								}
							}
							if($tour_archive_page_price_settings == "child") {
								if(!empty($meta['child_price']) && !$disable_adult_price){
									$tour_price[] = $meta['child_price'];
								}
							}
						}
					}
					?>
					<?php
                    $hide_price = tfopt( 't-hide-start-price' );
                    if ( isset( $hide_price ) && $hide_price !== '1' && ! empty( $tour_price )) :
						?>
                        <div class="tf-tour-price">
							<?php
							//get the lowest price from all available room price
							$tf_tour_min_price      = min( $tour_price );
							$tf_tour_full_price     = min( $tour_price );
							$tf_tour_discount_type  = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
							$tf_tour_discount_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';
							if ( ! empty( $tf_tour_discount_type ) && ! empty( $tf_tour_min_price ) && ! empty( $tf_tour_discount_price ) ) {
								if ( $tf_tour_discount_type == "percent" ) {
									$tf_tour_min_discount = ( $tf_tour_min_price * $tf_tour_discount_price ) / 100;
									$tf_tour_min_price    = $tf_tour_min_price - $tf_tour_min_discount;
								}
								if ( $tf_tour_discount_type == "fixed" ) {
									$tf_tour_min_discount = $tf_tour_discount_price;
									$tf_tour_min_price    = $tf_tour_min_price - $tf_tour_discount_price;
								}
							}
							$lowest_price = wc_price( $tf_tour_min_price );
							echo __( "From ", "tourfic" ) . $lowest_price;
							if ( ! empty( $tf_tour_min_discount ) ) {
								echo "<del>" . wc_price( $tf_tour_full_price ) . "</del>";
							}
							?>
                        </div>
					<?php endif; ?>
                </div>
            </div>
        </div>
		<?php
	}
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
	tf_file_missing( TF_INC_PATH . 'functions/woocommerce/wc-tour.php' );
}

/**
 * Filter tours on search result page by checkin checkout dates set by backend
 *
 *
 * @param DatePeriod $period collection of dates by user input;
 * @param array $not_found collection of tour exists
 * @param array $data user input for sidebar form
 *
 * @author devkabir, fida
 *
 */
function tf_filter_tour_by_date( $period, &$total_posts, array &$not_found, array $data = [] ): void {
	if ( isset( $data[3] ) && isset( $data[4] ) ) {
		[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
	} else {
		[ $adults, $child, $check_in_out ] = $data;
	}
	// Get tour meta options
	$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

	// Set initial tour availability status
	$has_tour = false;

	// Total People
	$total_people = intval( $adults ) + intval( $child );

	if ( ! empty( $meta['type'] ) && $meta['type'] === 'fixed' ) {

		if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
			$tf_tour_unserial_fixed_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $meta['fixed_availability'] );
			$tf_tour_unserial_fixed_date = unserialize( $tf_tour_unserial_fixed_date );
			$fixed_availability          = ! empty( $tf_tour_unserial_fixed_date ) ? $tf_tour_unserial_fixed_date['date'] : [];
		} else {
			$fixed_availability          = ! empty( $meta['fixed_availability'] ) ? $meta['fixed_availability']['date'] : [];
			$tf_tour_unserial_fixed_date = $meta['fixed_availability'];
		}

		$people_counter = 0;

		// Max & Min People Check
		if ( ! empty( $tf_tour_unserial_fixed_date['max_seat'] ) && $tf_tour_unserial_fixed_date['max_seat'] >= $total_people && $tf_tour_unserial_fixed_date['max_seat'] != 0 && ! empty( $tf_tour_unserial_fixed_date['min_seat'] ) && $tf_tour_unserial_fixed_date['min_seat'] <= $total_people && $tf_tour_unserial_fixed_date['min_seat'] != 0 ) {
			$people_counter ++;
		}
		if ( $people_counter > 0 ) {
			$show_fixed_tour = [];

			foreach ( $period as $date ) {

				$show_fixed_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $fixed_availability['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $fixed_availability['to'] ) );

			}


			if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
				if ( ! empty( $meta['adult_price'] ) ) {
					if ( $startprice <= $meta['adult_price'] && $meta['adult_price'] <= $endprice ) {
						$has_tour = ! in_array( 0, $show_fixed_tour );
					}
				}
				if ( ! empty( $meta['child_price'] ) ) {
					if ( $startprice <= $meta['child_price'] && $meta['child_price'] <= $endprice ) {
						$has_tour = ! in_array( 0, $show_fixed_tour );
					}
				}
				if ( ! empty( $meta['infant_price'] ) ) {
					if ( $startprice <= $meta['infant_price'] && $meta['infant_price'] <= $endprice ) {
						$has_tour = ! in_array( 0, $show_fixed_tour );
					}
				}
				if ( ! empty( $meta['group_price'] ) ) {
					if ( $startprice <= $meta['group_price'] && $meta['group_price'] <= $endprice ) {
						$has_tour = ! in_array( 0, $show_fixed_tour );
					}
				}
			} else {
				$has_tour = true;
			}
		}
	}

	if ( ! empty( $meta['type'] ) && $meta['type'] === 'continuous' ) {

		$custom_availability = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;

		if ( $custom_availability ) {

			if ( ! empty( $meta['cont_custom_date'] ) && gettype( $meta['cont_custom_date'] ) == "string" ) {
				$tf_tour_unserial_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['cont_custom_date'] );
				$tf_tour_unserial_custom_date = unserialize( $tf_tour_unserial_custom_date );
				$custom_dates                 = wp_list_pluck( $tf_tour_unserial_custom_date, 'date' );
			} else {
				$custom_dates = wp_list_pluck( $meta['cont_custom_date'], 'date' );
			}
			$people_counter = 0;
			if ( ! empty( $meta['cont_custom_date'] ) ) {
				foreach ( $meta['cont_custom_date'] as $minmax ) {
					// Max & Min People Check
					if ( ! empty( $minmax['max_people'] ) && $minmax['max_people'] >= $total_people && $minmax['max_people'] != 0 && ! empty( $minmax['min_people'] ) && $minmax['min_people'] <= $total_people && $minmax['min_people'] != 0 ) {
						$people_counter ++;
					}
				}
			}
			if ( $people_counter > 0 ) {
				foreach ( $custom_dates as $custom_date ) {
					$show_continuous_tour = [];
					foreach ( $period as $date ) {
						$show_continuous_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $custom_date['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $custom_date['to'] ) );
					}
					if ( ! in_array( 0, $show_continuous_tour ) ) {
						if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
							foreach ( $meta['cont_custom_date'] as $single_avail ) {
								if ( ! empty( $single_avail['adult_price'] ) ) {
									if ( $startprice <= $single_avail['adult_price'] && $single_avail['adult_price'] <= $endprice ) {
										$has_tour = true;
									}
								}
								if ( ! empty( $single_avail['child_price'] ) ) {
									if ( $startprice <= $single_avail['child_price'] && $single_avail['child_price'] <= $endprice ) {
										$has_tour = true;
									}
								}
								if ( ! empty( $single_avail['infant_price'] ) ) {
									if ( $startprice <= $single_avail['infant_price'] && $single_avail['infant_price'] <= $endprice ) {
										$has_tour = true;
									}
								}
								if ( ! empty( $single_avail['group_price'] ) ) {
									if ( $startprice <= $single_avail['group_price'] && $single_avail['group_price'] <= $endprice ) {
										$has_tour = true;
									}
								}
							}
						} else {
							$has_tour = true;
						}
						break;
					}
				}
			}

		} else {
			$tf_disable_dates = explode( ", ", $meta['disable_specific'] );
			if ( ! empty( $meta['disable_range'] ) && gettype( $meta['disable_range'] ) == "string" ) {
				$tf_tour_unserial_disable_date_range = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['disable_range'] );
				$tf_tour_unserial_disable_date_range = unserialize( $tf_tour_unserial_disable_date_range );
				$tf_disable_range_dates              = wp_list_pluck( $tf_tour_unserial_disable_date_range, 'date' );
			} else {
				$tf_disable_range_dates = wp_list_pluck( $meta['disable_range'], 'date' );
			}

			$tf_disable_ranges = [];
			if ( ! empty( $tf_disable_range_dates ) ) {
				foreach ( $tf_disable_range_dates as $disable_range ) {
					// Create DateTime objects for the start and end dates of the range
					$start = new DateTime( $disable_range["from"] );
					$end   = new DateTime( $disable_range["to"] );

					// Iterate over each day in the range and add it to the tf_disable_ranges array
					while ( $start <= $end ) {
						$tf_disable_ranges[] = $start->format( "Y/m/d" );
						$start->add( new DateInterval( "P1D" ) );
					}
				}
			}
			$people_counter = 0;

			// Max & Min People Check
			if ( ! empty( $meta['cont_max_people'] ) && $meta['cont_max_people'] >= $total_people && $meta['cont_max_people'] != 0 && ! empty( $meta['cont_min_people'] ) && $meta['cont_min_people'] <= $total_people && $meta['cont_min_people'] != 0 ) {
				$people_counter ++;
			}
			if ( $people_counter > 0 ) {
				if ( ! empty( $tf_disable_dates ) || ! empty( $tf_disable_ranges ) ) {
					$tf_all_disable_dates = array_merge( $tf_disable_dates, $tf_disable_ranges );
					$tf_disable_found     = false;

					foreach ( $period as $date ) {
						if ( in_array( $date->format( 'Y/m/d' ), $tf_all_disable_dates ) ) {
							$tf_disable_found = true;
							break;
						}
					}

					if ( $tf_disable_found ) {
						$has_tour = false;
					} else {
						if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
							if ( ! empty( $meta['adult_price'] ) ) {
								if ( $startprice <= $meta['adult_price'] && $meta['adult_price'] <= $endprice ) {
									$has_tour = true;
								}
							}
							if ( ! empty( $meta['child_price'] ) ) {
								if ( $startprice <= $meta['child_price'] && $meta['child_price'] <= $endprice ) {
									$has_tour = true;
								}
							}
							if ( ! empty( $meta['infant_price'] ) ) {
								if ( $startprice <= $meta['infant_price'] && $meta['infant_price'] <= $endprice ) {
									$has_tour = true;
								}
							}
							if ( ! empty( $meta['group_price'] ) ) {
								if ( $startprice <= $meta['group_price'] && $meta['group_price'] <= $endprice ) {
									$has_tour = true;
								}
							}
						} else {
							$has_tour = true;
						}
					}
				} else {
					if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
						if ( ! empty( $meta['adult_price'] ) ) {
							if ( $startprice <= $meta['adult_price'] && $meta['adult_price'] <= $endprice ) {
								$has_tour = true;
							}
						}
						if ( ! empty( $meta['child_price'] ) ) {
							if ( $startprice <= $meta['child_price'] && $meta['child_price'] <= $endprice ) {
								$has_tour = true;
							}
						}
						if ( ! empty( $meta['infant_price'] ) ) {
							if ( $startprice <= $meta['infant_price'] && $meta['infant_price'] <= $endprice ) {
								$has_tour = true;
							}
						}
						if ( ! empty( $meta['group_price'] ) ) {
							if ( $startprice <= $meta['group_price'] && $meta['group_price'] <= $endprice ) {
								$has_tour = true;
							}
						}
					} else {
						$has_tour = true;
					}
				}
			}

		}

	}
	if ( $has_tour ) {

		$not_found[] = array(
			'post_id' => get_the_ID(),
			'found'   => 0,
		);

	} else {
		$not_found[] = array(
			'post_id' => get_the_ID(),
			'found'   => 1,
		);

	}
}

/**
 * Filter tours on search result page by without date dates set by backend
 *
 *
 * @param DatePeriod $period collection of dates by user input;
 * @param array $not_found collection of tour exists
 * @param array $data user input for sidebar form
 *
 * @author Jahid
 *
 */
function tf_filter_tour_by_without_date( $period, &$total_posts, array &$not_found, array $data = [] ): void {
	if ( isset( $data[3] ) && isset( $data[4] ) ) {
		[ $adults, $child, $check_in_out, $startprice, $endprice ] = $data;
	} else {
		[ $adults, $child, $check_in_out ] = $data;
	}
	// Get tour meta options
	$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );

	// Set initial tour availability status
	$has_tour = false;

	if ( ! empty( $meta['type'] ) && $meta['type'] === 'fixed' ) {

		$show_fixed_tour = [];

		if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
			if ( ! empty( $meta['adult_price'] ) ) {
				if ( $startprice <= $meta['adult_price'] && $meta['adult_price'] <= $endprice ) {
					$has_tour = ! in_array( 0, $show_fixed_tour );
				}
			}
			if ( ! empty( $meta['child_price'] ) ) {
				if ( $startprice <= $meta['child_price'] && $meta['child_price'] <= $endprice ) {
					$has_tour = ! in_array( 0, $show_fixed_tour );
				}
			}
			if ( ! empty( $meta['infant_price'] ) ) {
				if ( $startprice <= $meta['infant_price'] && $meta['infant_price'] <= $endprice ) {
					$has_tour = ! in_array( 0, $show_fixed_tour );
				}
			}
			if ( ! empty( $meta['group_price'] ) ) {
				if ( $startprice <= $meta['group_price'] && $meta['group_price'] <= $endprice ) {
					$has_tour = ! in_array( 0, $show_fixed_tour );
				}
			}
		} else {
			$has_tour = true;
		}

	}

	if ( ! empty( $meta['type'] ) && $meta['type'] === 'continuous' ) {

		$custom_availability = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
		if ( $custom_availability ) {

			if ( ! empty( $meta['cont_custom_date'] ) && gettype( $meta['cont_custom_date'] ) == "string" ) {
				$tf_tour_unserial_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['cont_custom_date'] );
				$tf_tour_unserial_custom_date = unserialize( $tf_tour_unserial_custom_date );
				$custom_dates                 = wp_list_pluck( $tf_tour_unserial_custom_date, 'date' );
			} else {
				$custom_dates = wp_list_pluck( $meta['cont_custom_date'], 'date' );
			}

			foreach ( $custom_dates as $custom_date ) {

				$show_continuous_tour = [];

				if ( ! in_array( 0, $show_continuous_tour ) ) {
					if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
						if ( ! empty( $meta['adult_price'] ) ) {
							if ( $startprice <= $meta['adult_price'] && $meta['adult_price'] <= $endprice ) {
								$has_tour = true;
							}
						}
						if ( ! empty( $meta['child_price'] ) ) {
							if ( $startprice <= $meta['child_price'] && $meta['child_price'] <= $endprice ) {
								$has_tour = true;
							}
						}
						if ( ! empty( $meta['infant_price'] ) ) {
							if ( $startprice <= $meta['infant_price'] && $meta['infant_price'] <= $endprice ) {
								$has_tour = true;
							}
						}
						if ( ! empty( $meta['group_price'] ) ) {
							if ( $startprice <= $meta['group_price'] && $meta['group_price'] <= $endprice ) {
								$has_tour = true;
							}
						}
					} else {
						$has_tour = true;
					}

					break;

				}

			}

		} else {

			if ( ! empty( $startprice ) && ! empty( $endprice ) ) {
				if ( ! empty( $meta['adult_price'] ) ) {
					if ( $startprice <= $meta['adult_price'] && $meta['adult_price'] <= $endprice ) {
						$has_tour = true;
					}
				}
				if ( ! empty( $meta['child_price'] ) ) {
					if ( $startprice <= $meta['child_price'] && $meta['child_price'] <= $endprice ) {
						$has_tour = true;
					}
				}
				if ( ! empty( $meta['infant_price'] ) ) {
					if ( $startprice <= $meta['infant_price'] && $meta['infant_price'] <= $endprice ) {
						$has_tour = true;
					}
				}
				if ( ! empty( $meta['group_price'] ) ) {
					if ( $startprice <= $meta['group_price'] && $meta['group_price'] <= $endprice ) {
						$has_tour = true;
					}
				}
			} else {
				$has_tour = true;
			}

		}

	}
	if ( $has_tour ) {

		$not_found[] = array(
			'post_id' => get_the_ID(),
			'found'   => 0,
		);

	} else {
		$not_found[] = array(
			'post_id' => get_the_ID(),
			'found'   => 1,
		);

	}
}

/*
 * Tour search ajax
 * @since 2.9.7
 * @author Foysal
 */
add_action( 'wp_ajax_tf_tour_search', 'tf_tour_search_ajax_callback' );
add_action( 'wp_ajax_nopriv_tf_tour_search', 'tf_tour_search_ajax_callback' );
if ( ! function_exists( 'tf_tour_search_ajax_callback' ) ) {
	function tf_tour_search_ajax_callback() {
		$response = [
			'status'  => 'error',
			'message' => '',
		];

		if ( ! isset( $_POST['place'] ) || empty( $_POST['place'] ) ) {
			$response['message'] = esc_html__( 'Please enter your location', 'tourfic' );
		} elseif ( tfopt( 'date_tour_search' ) && ( ! isset( $_POST['check-in-out-date'] ) || empty( $_POST['check-in-out-date'] ) ) ) {
			$response['message'] = esc_html__( 'Please select a date', 'tourfic' );
		}

		if ( tfopt( 'date_tour_search' ) ) {
			if ( ! empty( $_POST['place'] ) && ! empty( $_POST['check-in-out-date'] ) ) {
				$response['query_string'] = str_replace( '&action=tf_tour_search', '', http_build_query( $_POST ) );
				$response['status']       = 'success';
			}
		} else {
			if ( ! empty( $_POST['place'] ) ) {
				$response['query_string'] = str_replace( '&action=tf_tour_search', '', http_build_query( $_POST ) );
				$response['status']       = 'success';
			}
		}

		echo json_encode( $response );
		wp_die();
	}
}

/*
* Tour will be auto draft after Expire
* Author: Jahid
*/

add_action( 'wp', 'tf_setup_everydate_cron_job' );
function tf_setup_everydate_cron_job() {
	if ( ! wp_next_scheduled( 'tf_everydate_cron_job' ) ) {
		wp_schedule_event( strtotime( 'midnight' ), 'daily', 'tf_everydate_cron_job' );
	}
}

$tf_tours_autodrafts = ! empty( tfopt( 't-auto-draft' ) ) ? tfopt( 't-auto-draft' ) : '';
if ( ! empty( $tf_tours_autodrafts ) ) {
	add_action( 'tf_everydate_cron_job', 'tf_every_date_function' );
}
function tf_every_date_function() {

	$args      = array(
		'post_type'      => 'tf_tours',
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
	);
	$tour_loop = new WP_Query( $args );
	while ( $tour_loop->have_posts() ) : $tour_loop->the_post();
		$post_id = get_the_ID();
		$meta    = get_post_meta( $post_id, 'tf_tours_opt', true );

		if ( $meta['type'] == "fixed" ) {
			if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
				$tf_tour_unserial_fixed_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['fixed_availability'] );
				$tf_tour_unserial_fixed_date = unserialize( $tf_tour_unserial_fixed_date );
				$fixed_availability          = ! empty( $tf_tour_unserial_fixed_date ) ? $tf_tour_unserial_fixed_date['date'] : [];
			} else {
				$fixed_availability = ! empty( $meta['fixed_availability'] ) ? $meta['fixed_availability']['date'] : [];
			}
			if ( ! empty( $fixed_availability ) ) {
				$show_fixed_tour   = [];
				$show_fixed_tour[] = intval( strtotime( date( 'Y-m-d' ) ) >= strtotime( $fixed_availability['from'] ) && strtotime( date( 'Y-m-d' ) ) <= strtotime( $fixed_availability['to'] ) );
				if ( empty( $show_fixed_tour['0'] ) ) {
					$tf_tour_data = array(
						'ID'          => $post_id,
						'post_status' => 'expired',
					);
					wp_update_post( $tf_tour_data );
				}
			}
		}
	endwhile;
	wp_reset_postdata();

}

/*
* Tour Expired Status Add
* Author: Jahid
*/

function tf_tours_custom_status_creation() {
	register_post_status( 'expired', array(
		'label'                     => _x( 'Expired', 'post' ),
		'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true
	) );
}

add_action( 'init', 'tf_tours_custom_status_creation' );

function tf_tours_custom_status_add_in_quick_edit() {
	global $post;
	if ( ! empty( $post->post_type ) && $post->post_type == 'tf_tours' ) {
		echo "<script>
    jQuery(document).ready( function() {
        jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"expired\">Expired</option>' );      
    }); 
    </script>";
	}
}

add_action( 'admin_footer-edit.php', 'tf_tours_custom_status_add_in_quick_edit' );
function tf_tours_custom_status_add_in_post_page() {
	global $post;
	if ( $post->post_type == 'tf_tours' ) {
		echo "<script>
        jQuery(document).ready( function() {        
            jQuery( 'select[name=\"post_status\"]' ).append( '<option value=\"expired\">Expired</option>' );
        });
        </script>";
	}
}

add_action( 'admin_footer-post.php', 'tf_tours_custom_status_add_in_post_page' );
add_action( 'admin_footer-post-new.php', 'tf_tours_custom_status_add_in_post_page' );

/**
 * Assign taxonomy(tour_features) from the single post metabox
 * to a Tour when updated or published
 * @return array();
 * @author Abu Hena
 * @since 2.9.2
 */

add_action( 'wp_after_insert_post', 'tf_tour_features_assign_taxonomies', 100, 3 );
function tf_tour_features_assign_taxonomies( $post_id, $post, $old_status ) {
	if ( 'tf_tours' !== $post->post_type ) {
		return;
	}
	$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
	if ( ! empty( $meta['features'] ) && is_array( $meta['features'] ) ) {
		$features = array_map( 'intval', $meta['features'] );
		wp_set_object_terms( $post_id, $features, 'tour_features' );
	}

}

/**
 * Assign taxonomy(tour_type) from the single post metabox
 * to a Tour when updated or published
 * @return array();
 * @author Foysal
 * @since 2.9.23
 */

add_action( 'wp_after_insert_post', 'tf_tour_type_assign_taxonomies', 100, 3 );
function tf_tour_type_assign_taxonomies( $post_id, $post, $old_status ) {
	if ( 'tf_tours' !== $post->post_type ) {
		return;
	}
	$meta = get_post_meta( $post_id, 'tf_tours_opt', true );
	if ( ! empty( $meta['tour_types'] ) && is_array( $meta['tour_types'] ) ) {
		$tour_types = array_map( 'intval', $meta['tour_types'] );
		wp_set_object_terms( $post_id, $tour_types, 'tour_type' );
	}

}

add_action( 'wp_ajax_nopriv_tf_tour_booking_popup', 'tf_tour_booking_popup_callback' );
add_action( 'wp_ajax_tf_tour_booking_popup', 'tf_tour_booking_popup_callback' );
function tf_tour_booking_popup_callback() {
	$response             = array();
	$adults               = isset( $_POST['adults'] ) ? intval( sanitize_text_field( $_POST['adults'] ) ) : 0;
	$children             = isset( $_POST['children'] ) ? intval( sanitize_text_field( $_POST['children'] ) ) : 0;
	$infant               = isset( $_POST['infant'] ) ? intval( sanitize_text_field( $_POST['infant'] ) ) : 0;
	$total_people         = $adults + $children + $infant;
	$total_people_booking = $adults + $children;
	// Tour date
	$tour_date = ! empty( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
	$tour_time = isset( $_POST['check_in_time'] ) ? sanitize_text_field( $_POST['check_in_time'] ) : null;


	$post_id              = isset( $_POST['post_id'] ) ? intval( sanitize_text_field( $_POST['post_id'] ) ) : '';
	$meta                 = get_post_meta( $post_id, 'tf_tours_opt', true );
	$tour_type            = ! empty( $meta['type'] ) ? $meta['type'] : '';
	$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
	$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
	$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
	$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;

	/**
	 * If fixed is selected but pro is not activated
	 *
	 * show error
	 *
	 * @return
	 */
	if ( $tour_type == 'fixed' && function_exists( 'is_tf_pro' ) && ! is_tf_pro() ) {
		$response['errors'][] = __( 'Fixed Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
		$response['status']   = 'error';
		echo wp_json_encode( $response );
		die();

		return;
	}

	if ( $tour_type == 'fixed' ) {

		if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
			$tf_tour_fixed_avail   = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $meta['fixed_availability'] );
			$tf_tour_fixed_date    = unserialize( $tf_tour_fixed_avail );
			$start_date            = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
			$end_date              = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
			$min_people            = ! empty( $tf_tour_fixed_date['min_seat'] ) ? $tf_tour_fixed_date['min_seat'] : '';
			$max_people            = ! empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
			$tf_tour_booking_limit = ! empty( $tf_tour_fixed_date['max_capacity'] ) ? $tf_tour_fixed_date['max_capacity'] : 0;
		} else {
			$start_date            = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
			$end_date              = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
			$min_people            = ! empty( $meta['fixed_availability']['min_seat'] ) ? $meta['fixed_availability']['min_seat'] : '';
			$max_people            = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
			$tf_tour_booking_limit = ! empty( $meta['fixed_availability']['max_capacity'] ) ? $meta['fixed_availability']['max_capacity'] : 0;
		}


		// Fixed tour maximum capacity limit

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $start_date ) && ! empty( $end_date ) ) {

			// Tour Order retrive from Tourfic Order Table

			$tf_orders_select    = array(
				'select' => "post_id,order_details",
				'post_type' => 'tour',
				'query'  => " AND ostatus = 'completed' ORDER BY order_id DESC"
			);
			$tf_tour_book_orders = tourfic_order_table_data( $tf_orders_select );

			$tf_total_adults    = 0;
			$tf_total_childrens = 0;

			foreach ( $tf_tour_book_orders as $order ) {
				$tour_id       = $order['post_id'];
				$order_details = json_decode( $order['order_details'] );
				$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
				list( $tf_booking_start, $tf_booking_end ) = explode( " - ", $tf_tour_date );
				if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_booking_start ) && $start_date == $tf_booking_start && ! empty( $tf_booking_end ) && $end_date == $tf_booking_end ) {
					$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
					if ( ! empty( $book_adult ) ) {
						list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
						$tf_total_adults += $tf_total_adult;
					}

					$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
					if ( ! empty( $book_children ) ) {
						list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
						$tf_total_childrens += $tf_total_children;
					}
				}
			}

			$tf_total_people = $tf_total_adults + $tf_total_childrens;

			if ( ! empty( $tf_tour_booking_limit ) ) {
				$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;
				if ( $tf_total_people > 0 && $tf_total_people == $tf_tour_booking_limit ) {
					$response['errors'][] = __( 'Booking limit is Reached this Tour', 'tourfic' );
				}
				if ( $tf_total_people != $tf_tour_booking_limit && $tf_today_limit < $total_people_booking ) {
					$response['errors'][] = sprintf( __( 'Only %1$s Adult/Children are available this Tour', 'tourfic' ), $tf_today_limit );
				}
			}
		}

	} elseif ( $tour_type == 'continuous' ) {

		$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;

		if ( $custom_avail == true ) {

			$pricing_rule     = $meta['custom_pricing_by'];
			$cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
			if ( ! empty( $cont_custom_date ) && gettype( $cont_custom_date ) == "string" ) {
				$tf_tour_conti_avail = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $cont_custom_date );
				$cont_custom_date    = unserialize( $tf_tour_conti_avail );
			}

		} elseif ( $custom_avail == false ) {

			$min_people          = ! empty( $meta['cont_min_people'] ) ? $meta['cont_min_people'] : '';
			$max_people          = ! empty( $meta['cont_max_people'] ) ? $meta['cont_max_people'] : '';
			$allowed_times_field = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';


			// Daily Tour Booking Capacity && Tour Order retrive from Tourfic Order Table
			$tf_orders_select    = array(
				'select' => "post_id,order_details",
				'post_type' => 'tour',
				'query'  => " AND ostatus = 'completed' ORDER BY order_id DESC"
			);
			$tf_tour_book_orders = tourfic_order_table_data( $tf_orders_select );

			$tf_total_adults    = 0;
			$tf_total_childrens = 0;

			if ( empty( $allowed_times_field ) || $tour_time == null ) {
				$tf_tour_booking_limit = ! empty( $meta['cont_max_capacity'] ) ? $meta['cont_max_capacity'] : 0;
				foreach ( $tf_tour_book_orders as $order ) {
					$tour_id       = $order['post_id'];
					$order_details = json_decode( $order['order_details'] );
					$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
					$tf_tour_time  = ! empty( $order_details->tour_time ) ? $order_details->tour_time : '';

					if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && empty( $tf_tour_time ) ) {
						$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
						if ( ! empty( $book_adult ) ) {
							list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
							$tf_total_adults += $tf_total_adult;
						}

						$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
						if ( ! empty( $book_children ) ) {
							list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
							$tf_total_childrens += $tf_total_children;
						}
					}
				}
			} else {
				if ( ! empty( $allowed_times_field[ $tour_time ]['time'] ) ) {
					$tour_time_title = $allowed_times_field[ $tour_time ]['time'];
				}

				if ( ! empty( $allowed_times_field[ $tour_time ]['cont_max_capacity'] ) ) {
					$tf_tour_booking_limit = $allowed_times_field[ $tour_time ]['cont_max_capacity'];

					foreach ( $tf_tour_book_orders as $order ) {
						$tour_id       = $order['post_id'];
						$order_details = json_decode( $order['order_details'] );
						$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
						$tf_tour_time  = ! empty( $order_details->tour_time ) ? $order_details->tour_time : '';

						if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && ! empty( $tf_tour_time ) && $tf_tour_time == $tour_time_title ) {
							$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
							if ( ! empty( $book_adult ) ) {
								list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
								$tf_total_adults += $tf_total_adult;
							}

							$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
							if ( ! empty( $book_children ) ) {
								list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
								$tf_total_childrens += $tf_total_children;
							}
						}
					}

				}
			}
			$tf_total_people = $tf_total_adults + $tf_total_childrens;

			if ( ! empty( $tf_tour_booking_limit ) ) {
				$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;

				if ( $tf_total_people > 0 && $tf_total_people == $tf_tour_booking_limit ) {
					$response['errors'][] = __( 'Booking limit is Reached this Date', 'tourfic' );
				}
				if ( $tf_total_people != $tf_tour_booking_limit && $tf_today_limit < $total_people_booking ) {
					$response['errors'][] = sprintf( __( 'Only %1$s Adult/Children are available this Date', 'tourfic' ), $tf_today_limit );
				}
			}
		}

	}

	/**
	 * If continuous custom availability is selected but pro is not activated
	 *
	 * Show error
	 *
	 * @return
	 */
	if ( $tour_type == 'continuous' && $custom_avail == true && function_exists( 'is_tf_pro' ) && ! is_tf_pro() ) {
		$response['errors'][] = __( 'Custom Continous Availability is selected but Tourfic Pro is not activated!', 'tourfic' );
		$response['status']   = 'error';
		echo wp_json_encode( $response );
		die();

		return;
	}


	if ( $tour_type == 'continuous' ) {
		$start_date = $end_date = $tour_date;
	}

	/**
	 * People 0 number validation
	 *
	 */
	if ( $total_people == 0 ) {
		$response['errors'][] = __( 'Please Select Adults/Children/Infant required', 'tourfic' );
	}

	/**
	 * People number validation
	 *
	 */
	if ( $tour_type == 'fixed' ) {

		$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
		$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );

		if ( $total_people < $min_people && $min_people > 0 ) {
			$response['errors'][] = sprintf( __( 'Minimum %1$s required', 'tourfic' ), $min_text );

		} else if ( $total_people > $max_people && $max_people > 0 ) {
			$response['errors'][] = sprintf( __( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

		}

	} elseif ( $tour_type == 'continuous' && $custom_avail == false ) {

		$min_text = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
		$max_text = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );

		if ( $total_people < $min_people && $min_people > 0 ) {
			$response['errors'][] = sprintf( __( 'Minimum %1$s required', 'tourfic' ), $min_text );

		} else if ( $total_people > $max_people && $max_people > 0 ) {
			$response['errors'][] = sprintf( __( 'Maximum %1$s allowed', 'tourfic' ), $max_text );

		}

	} elseif ( $tour_type == 'continuous' && $custom_avail == true ) {

		foreach ( $cont_custom_date as $item ) {

			// Backend continuous date values
			$back_date_from     = ! empty( $item['date']['from'] ) ? $item['date']['from'] : '';
			$back_date_to       = ! empty( $item['date']['from'] ) ? $item['date']['to'] : '';
			$back_date_from_stt = strtotime( str_replace( '/', '-', $back_date_from ) );
			$back_date_to_stt   = strtotime( str_replace( '/', '-', $back_date_to ) );
			// frontend selected date value
			$front_date = strtotime( str_replace( '/', '-', $tour_date ) );
			// Backend continuous min/max people values
			$min_people = ! empty( $item['min_people'] ) ? $item['min_people'] : '';
			$max_people = ! empty( $item['max_people'] ) ? $item['max_people'] : '';
			$min_text   = sprintf( _n( '%s person', '%s people', $min_people, 'tourfic' ), $min_people );
			$max_text   = sprintf( _n( '%s person', '%s people', $max_people, 'tourfic' ), $max_people );


			// Compare backend & frontend date values to show specific people number error
			if ( $front_date >= $back_date_from_stt && $front_date <= $back_date_to_stt ) {
				if ( $total_people < $min_people && $min_people > 0 ) {
					$response['errors'][] = sprintf( __( 'Minimum %1$s required for date %2$s - %3$s', 'tourfic' ), $min_text, $back_date_from, $back_date_to );

				}
				if ( $total_people > $max_people && $max_people > 0 ) {
					$response['errors'][] = sprintf( __( 'Maximum %1$s allowed for date %2$s - %3$s', 'tourfic' ), $max_text, $back_date_from, $back_date_to );

				}


				$allowed_times_field = ! empty( $item['allowed_time'] ) ? $item['allowed_time'] : '';

				// Daily Tour Booking Capacity && tour order retrive form tourfic order table
				$tf_orders_select    = array(
					'select' => "post_id,order_details",
					'post_type' => 'tour',
					'query'  => " AND ostatus = 'completed' ORDER BY order_id DESC"
				);
				$tf_tour_book_orders = tourfic_order_table_data( $tf_orders_select );

				$tf_total_adults    = 0;
				$tf_total_childrens = 0;

				if ( empty( $allowed_times_field ) || $tour_time == null ) {
					$tf_tour_booking_limit = ! empty( $item['max_capacity'] ) ? $item['max_capacity'] : '';

					foreach ( $tf_tour_book_orders as $order ) {
						$tour_id       = $order['post_id'];
						$order_details = json_decode( $order['order_details'] );
						$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
						$tf_tour_time  = ! empty( $order_details->tour_time ) ? $order_details->tour_time : '';

						if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && empty( $tf_tour_time ) ) {
							$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
							if ( ! empty( $book_adult ) ) {
								list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
								$tf_total_adults += $tf_total_adult;
							}

							$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
							if ( ! empty( $book_children ) ) {
								list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
								$tf_total_childrens += $tf_total_children;
							}
						}
					}

				} else {
					if ( ! empty( $allowed_times_field[ $tour_time ]['time'] ) ) {
						$tour_time_title = $allowed_times_field[ $tour_time ]['time'];
					}

					if ( ! empty( $allowed_times_field[ $tour_time ]['max_capacity'] ) ) {
						$tf_tour_booking_limit = $allowed_times_field[ $tour_time ]['max_capacity'];

						foreach ( $tf_tour_book_orders as $order ) {
							$tour_id       = $order['post_id'];
							$order_details = json_decode( $order['order_details'] );
							$tf_tour_date  = ! empty( $order_details->tour_date ) ? $order_details->tour_date : '';
							$tf_tour_time  = ! empty( $order_details->tour_time ) ? $order_details->tour_time : '';

							if ( ! empty( $tour_id ) && $tour_id == $post_id && ! empty( $tf_tour_date ) && $tour_date == $tf_tour_date && ! empty( $tf_tour_time ) && $tf_tour_time == $tour_time_title ) {
								$book_adult = ! empty( $order_details->adult ) ? $order_details->adult : '';
								if ( ! empty( $book_adult ) ) {
									list( $tf_total_adult, $tf_adult_string ) = explode( " × ", $book_adult );
									$tf_total_adults += $tf_total_adult;
								}

								$book_children = ! empty( $order_details->child ) ? $order_details->child : '';
								if ( ! empty( $book_children ) ) {
									list( $tf_total_children, $tf_children_string ) = explode( " × ", $book_children );
									$tf_total_childrens += $tf_total_children;
								}
							}
						}

					}
				}
				$tf_total_people = $tf_total_adults + $tf_total_childrens;

				if ( ! empty( $tf_tour_booking_limit ) ) {
					$tf_today_limit = $tf_tour_booking_limit - $tf_total_people;

					if ( $tf_total_people > 0 && $tf_total_people == $tf_tour_booking_limit ) {
						$response['errors'][] = __( 'Booking limit is Reached this Date', 'tourfic' );
					}
					if ( $tf_total_people != $tf_tour_booking_limit && $tf_today_limit < $total_people_booking ) {
						$response['errors'][] = sprintf( __( 'Only %1$s Adult/Children are available this Date', 'tourfic' ), $tf_today_limit );
					}
				}
			}

		}

	}

	/**
	 * Check errors
	 *
	 */
	/* Minimum days to book before departure */
	$min_days_before_book      = ! empty( $meta['min_days_before_book'] ) ? $meta['min_days_before_book'] : '0';
	$min_days_before_book_text = sprintf( _n( '%s day', '%s days', $min_days_before_book, 'tourfic' ), $min_days_before_book );
	$today_stt                 = new DateTime( date( 'Y-m-d', strtotime( date( 'Y-m-d' ) ) ) );
	$tour_date_stt             = new DateTime( date( 'Y-m-d', strtotime( $start_date ) ) );
	$day_difference            = $today_stt->diff( $tour_date_stt )->days;


	if ( $day_difference < $min_days_before_book ) {
		$response['errors'][] = sprintf( __( 'Present date to booking date required minimum %1$s gap', 'tourfic' ), $min_days_before_book_text );
	}
	if ( ! $start_date ) {
		$response['errors'][] = __( 'You must select booking date', 'tourfic' );
	}
	if ( ! $post_id ) {
		$response['errors'][] = __( 'Unknown Error! Please try again.', 'tourfic' );
	}

	/**
	 * Price by date range
	 *
	 * Tour type continuous and custom availability is true
	 */
	$tf_cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
	if ( ! empty( $tf_cont_custom_date ) && gettype( $tf_cont_custom_date ) == "string" ) {
		$tf_tour_conti_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
			return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
		}, $tf_cont_custom_date );
		$tf_cont_custom_date       = unserialize( $tf_tour_conti_custom_date );
	}

	$tour = strtotime( $tour_date );
	if ( isset( $custom_avail ) && true == $custom_avail ) {
		$seasional_price = array_values( array_filter( $tf_cont_custom_date, function ( $value ) use ( $tour ) {
			$seasion_start = strtotime( $value['date']['from'] );
			$seasion_end   = strtotime( $value['date']['to'] );

			return $seasion_start <= $tour && $seasion_end >= $tour;
		} ) );
	}


	if ( $tour_type === 'continuous' && ! empty( $tf_cont_custom_date ) && ! empty( $seasional_price ) ) {

		$group_price    = $seasional_price[0]['group_price'];
		$adult_price    = $seasional_price[0]['adult_price'];
		$children_price = $seasional_price[0]['child_price'];
		$infant_price   = $seasional_price[0]['infant_price'];

	} else {

		$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
		$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
		$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
		$infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;

	}

	if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $tour_type == 'continuous' ) {
		$tf_allowed_times = ! empty( $meta['allowed_time'] ) ? $meta['allowed_time'] : '';
		if ( ! empty( $tf_allowed_times ) && gettype( $tf_allowed_times ) == "string" ) {
			$tf_tour_conti_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $tf_allowed_times );
			$tf_allowed_times          = unserialize( $tf_tour_conti_custom_date );
		}

		if ( $custom_avail == false && ! empty( $tf_allowed_times ) && empty( $tour_time_title ) ) {
			$response['errors'][] = __( 'Please select time', 'tourfic' );
		}
		if ( $custom_avail == true && ! empty( $seasional_price[0]['allowed_time'] ) && empty( $tour_time_title ) ) {
			$response['errors'][] = __( 'Please select time', 'tourfic' );
		}
	}

	if ( ( ! empty( $custom_avail ) && $custom_avail == true ) || $pricing_rule == 'person' ) {

		if ( ! $disable_adult_price && $adults > 0 && empty( $adult_price ) ) {
			$response['errors'][] = __( 'Adult price is blank!', 'tourfic' );
		}
		if ( ! $disable_child_price && $children > 0 && empty( $children_price ) ) {
			$response['errors'][] = __( 'Childern price is blank!', 'tourfic' );
		}
		if ( ! $disable_infant_price && $infant > 0 && empty( $infant_price ) ) {
			$response['errors'][] = __( 'Infant price is blank!', 'tourfic' );
		}
		if ( $infant > 0 && ! empty( $infant_price ) && ! $adults ) {
			$response['errors'][] = __( 'Infant without adults is not allowed!', 'tourfic' );
		}

	} else if ( ( ! empty( $custom_avail ) && $custom_avail == true ) || $pricing_rule == 'group' ) {

		if ( empty( $group_price ) ) {
			$response['errors'][] = __( 'Group price is blank!', 'tourfic' );
		}

	}

	// Tour extra
	$tour_extra_total     = 0;
	$tour_extra_title_arr = [];
	$tour_extra_meta = ! empty( $meta['tour-extra'] ) ? $meta['tour-extra'] : '';
	if(!empty($tour_extra_meta)){
		$tours_extra = explode(',', $_POST['tour_extra']);
		foreach($tours_extra as $extra){
			$tour_extra_pricetype = !empty( $tour_extra_meta[$extra]['price_type'] ) ? $tour_extra_meta[$extra]['price_type'] : 'fixed';
			if( $tour_extra_pricetype=="fixed" ){
				if(!empty($tour_extra_meta[$extra]['title']) && !empty($tour_extra_meta[$extra]['price'])){
					$tour_extra_total += $tour_extra_meta[$extra]['price'];
					$tour_extra_title_arr[] =  array(
						'title' => $tour_extra_meta[$extra]['title'],
						'price' => $tour_extra_meta[$extra]['price']
					);
				}
			}else{
				if(!empty($tour_extra_meta[$extra]['price']) && !empty($tour_extra_meta[$extra]['title'])){
					$tour_extra_total += ($tour_extra_meta[$extra]['price']*$total_people);
					$tour_extra_title_arr[] =  array(
						'title' => $tour_extra_meta[$extra]['title'],
						'price' => $tour_extra_meta[$extra]['price']*$total_people
					);
				}
			}
		}
	}

	if ( ! array_key_exists( 'errors', $response ) || count( $response['errors'] ) == 0 ) {


		# Discount informations
		$discount_type    = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
		$discounted_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';

		# Calculate discounted price
		if ( $discount_type == 'percent' ) {

			$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $adult_price - ( ( $adult_price / 100 ) * $discounted_price ), 2 ) ) );
			$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( $children_price - ( ( $children_price / 100 ) * $discounted_price ), 2 ) ) );
			$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( $infant_price - ( ( $infant_price / 100 ) * $discounted_price ), 2 ) ) );
			$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( $group_price - ( ( $group_price / 100 ) * $discounted_price ), 2 ) ) );

		} elseif ( $discount_type == 'fixed' ) {

			$adult_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $adult_price - $discounted_price ), 2 ) ) );
			$children_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $children_price - $discounted_price ), 2 ) ) );
			$infant_price   = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $infant_price - $discounted_price ), 2 ) ) );
			$group_price    = floatval( preg_replace( '/[^\d.]/', '', number_format( ( $group_price - $discounted_price ), 2 ) ) );

		}


		# Set pricing based on pricing rule
		if ( $pricing_rule == 'group' ) {
			$tf_tours_data_price = $group_price;
		} else {
			$tf_tours_data_price = ( $adult_price * $adults ) + ( $children * $children_price ) + ( $infant * $infant_price );
		}
		if ( ! empty( $_POST['deposit'] ) && $_POST['deposit'] == "true" ) {
			if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['allow_deposit'] ) && $meta['allow_deposit'] == '1' && ! empty( $meta['deposit_amount'] ) ) {

				if ( ! empty( $meta['deposit_type'] ) && $meta['deposit_type'] == 'fixed' ) {
					$tf_deposit_amount   = ! empty( $meta['deposit_amount'] ) ? $meta['deposit_amount'] : 0;
					$tf_due_amount       = $tf_tours_data_price - $tf_deposit_amount;
					$tf_tours_data_price = $tf_deposit_amount;
				} else {
					$tf_deposit_amount   = ! empty( $meta['deposit_amount'] ) ? ( $tf_tours_data_price * $meta['deposit_amount'] ) / 100 : 0;
					$tf_due_amount       = $tf_tours_data_price - $tf_deposit_amount;
					$tf_tours_data_price = $tf_deposit_amount;
				}
			}
		}
		$traveller_info_fields = ! empty( tfopt( 'without-payment-field' ) ) ? tf_data_types( tfopt( 'without-payment-field' ) ) : '';

		$response['traveller_info']    = '';
		$response['traveller_summery'] = '';
		for ( $traveller_in = 1; $traveller_in <= $total_people; $traveller_in ++ ) {
			$response['traveller_info'] .= '<div class="tf-single-tour-traveller tf-single-travel">
                <h4>' . sprintf( __( 'Traveler ', 'tourfic' ) ) . $traveller_in . '</h4>
                <div class="traveller-info">';
			if ( empty( $traveller_info_fields ) ) {
				$response['traveller_info'] .= '<div class="traveller-single-info">
                        <label for="tf_full_name' . $traveller_in . '">' . sprintf( __( 'Full Name', 'tourfic' ) ) . '</label>
                        <input type="text" name="traveller[' . $traveller_in . '][tf_full_name]" id="tf_full_name' . $traveller_in . '" data-required="1" />
                        <div class="error-text" data-error-for="tf_full_name' . $traveller_in . '"></div>
                    </div>
                    <div class="traveller-single-info">
                        <label for="tf_dob' . $traveller_in . '">' . sprintf( __( 'Date of birth', 'tourfic' ) ) . '</label>
                        <input type="date" name="traveller[' . $traveller_in . '][tf_dob]" id="tf_dob' . $traveller_in . '" data-required="1" />
                        <div class="error-text" data-error-for="tf_dob' . $traveller_in . '"></div>
                    </div>
                    <div class="traveller-single-info">
                        <label for="tf_nid' . $traveller_in . '">' . sprintf( __( 'NID', 'tourfic' ) ) . '</label>
                        <input type="text" name="traveller[' . $traveller_in . '][tf_nid]" id="tf_nid' . $traveller_in . '" data-required="1" />
                        <div class="error-text" data-error-for="tf_nid' . $traveller_in . '"></div>
                    </div>
                    ';
			} else {
				foreach ( $traveller_info_fields as $field ) {
					if ( "text" == $field['reg-fields-type'] || "email" == $field['reg-fields-type'] || "date" == $field['reg-fields-type'] ) {
						$response['traveller_info'] .= '
                            <div class="traveller-single-info">
                                <label for="' . $field['reg-field-name'] . $traveller_in . '">' . sprintf( __( '%s', 'tourfic' ), $field['reg-field-label'] ) . '</label>
                                <input type="' . $field['reg-fields-type'] . '" name="traveller[' . $traveller_in . '][' . $field['reg-field-name'] . ']" data-required="' . $field['reg-field-required'] . '" id="' . $field['reg-field-name'] . $traveller_in . '" />
                                <div class="error-text" data-error-for="' . $field['reg-field-name'] . $traveller_in . '"></div>
                            </div>';
					}
					if ( "select" == $field['reg-fields-type'] && ! empty( $field['reg-options'] ) ) {
						$response['traveller_info'] .= '
                            <div class="traveller-single-info">
                                <label for="' . $field['reg-field-name'] . $traveller_in . '">' . sprintf( __( '%s', 'tourfic' ), $field['reg-field-label'] ) . '</label>
                                <select id="' . $field['reg-field-name'] . $traveller_in . '" name="traveller[' . $traveller_in . '][' . $field['reg-field-name'] . ']" data-required="' . $field['reg-field-required'] . '"><option value="">' . sprintf( __( 'Select One', 'tourfic' ) ) . '</option>';
						foreach ( $field['reg-options'] as $sfield ) {
							if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) {
								$response['traveller_info'] .= '<option value="' . $sfield['option-value'] . '">' . $sfield['option-label'] . '</option>';
							}
						}
						$response['traveller_info'] .= '</select>
                                <div class="error-text" data-error-for="' . $field['reg-field-name'] . $traveller_in . '"></div>
                            </div>';
					}
					if ( ( "checkbox" == $field['reg-fields-type'] || "radio" == $field['reg-fields-type'] ) && ! empty( $field['reg-options'] ) ) {
						$response['traveller_info'] .= '
                            <div class="traveller-single-info">
                            <label for="' . $field['reg-field-name'] . $traveller_in . '">' . sprintf( __( '%s', 'tourfic' ), $field['reg-field-label'] ) . '</label>
                            ';
						foreach ( $field['reg-options'] as $sfield ) {
							if ( ! empty( $sfield['option-label'] ) && ! empty( $sfield['option-value'] ) ) {
								$response['traveller_info'] .= '
                                        <div class="tf-single-checkbox">
                                        <input type="' . esc_attr( $field['reg-fields-type'] ) . '" name="traveller[' . $traveller_in . '][' . $field['reg-field-name'] . '][]" id="' . $sfield['option-value'] . $traveller_in . '" value="' . $sfield['option-value'] . '" data-required="' . $field['reg-field-required'] . '" />
                                        <label for="' . $sfield['option-value'] . $traveller_in . '">' . sprintf( __( '%s', 'tourfic' ), $sfield['option-label'] ) . '</label></div>';
							}
						}
						$response['traveller_info'] .= '
                            <div class="error-text" data-error-for="' . $field['reg-field-name'] . $traveller_in . '"></div>
                            </div>';
					}
				}
			}

			$response['traveller_info'] .= '</div>
            </div>';
			$tour_date_format_for_users = !empty(tfopt( "tf-date-format-for-users")) ? tfopt( "tf-date-format-for-users") : "Y/m/d";
			function tf_date_format_user($date, $format) {
				if(!empty($date) && !empty($format)) {
				   if(str_contains( $date, "-") == true) {
					   list($first_date, $last_date) = explode(" - ", $date);
					   $first_date = date($format, strtotime($first_date));
					   $last_date = date($format, strtotime($last_date));
					   return "{$first_date} - {$last_date}";
				   } else {
					   return date($format, strtotime($date));
				   }
				}else {
					return;
				}
		   }
		}
		$response['traveller_summery'] .= '<h6>On ' . tf_date_format_user($tour_date, $tour_date_format_for_users) . '</h6>
        <table class="table" style="width: 100%">
            <thead>
                <tr>
                    <th align="left">' . sprintf( __( 'Traveller', 'tourfic' ) ) . '</th>
                    <th align="right">' . sprintf( __( 'Price', 'tourfic' ) ) . '</th>
                </tr>
            </thead>
            <tbody>';
		if ( ! empty( $pricing_rule ) && $pricing_rule == "person" ) {
			if ( ! empty( $adult_price ) && ! empty( $adults ) ) {
				$response['traveller_summery'] .= '<tr>
                        <td align="left">' . $adults . sprintf( __( ' adults', 'tourfic' ) ) . ' (' . wc_price( $adult_price ) . '/' . $pricing_rule . ')</td>
                        <td align="right">' . wc_price( $adult_price * $adults ) . '</td>
                    </tr>';
			}
			if ( ! empty( $children_price ) && ! empty( $children ) ) {
				$response['traveller_summery'] .= '<tr>
                        <td align="left">' . $children . sprintf( __( ' childrens', 'tourfic' ) ) . ' (' . wc_price( $children_price ) . '/' . $pricing_rule . ')</td>
                        <td align="right">' . wc_price( $children_price * $children ) . '</td>
                    </tr>';
			}
			if ( ! empty( $infant_price ) && ! empty( $infant ) ) {
				$response['traveller_summery'] .= '<tr>
                        <td align="left">' . $infant . sprintf( __( ' infants', 'tourfic' ) ) . ' (' . wc_price( $infant_price ) . '/' . $pricing_rule . ')</td>
                        <td align="right">' . wc_price( $infant_price * $infant ) . '</td>
                    </tr>';
			}
		} else {
			if ( ! empty( $group_price ) ) {
				$response['traveller_summery'] .= '<tr>
                        <td align="left">' . sprintf( __( 'Group Price', 'tourfic' ) ) . '</td>
                        <td align="right">' . wc_price( $group_price ) . '</td>
                    </tr>';
			}
		}
		if(!empty($tour_extra_title_arr)){
			foreach($tour_extra_title_arr as $extra_info){
				if(!empty($extra_info['title']) && !empty($extra_info['price'])){
					$response['traveller_summery'] .='<tr>
						<td align="left">'.esc_html($extra_info['title']).'</td>
						<td align="right">'.wc_price($extra_info['price']).'</td>
					</tr>';
				}
			}
		}
		if ( ! empty( $tf_due_amount ) ) {
			$response['traveller_summery'] .= '<tr>
                    <td align="left">' . sprintf( __( 'Due', 'tourfic' ) ) . '</td>
                    <td align="right">' . wc_price( $tf_due_amount ) . '</td>
                </tr>';
		}

		$response['traveller_summery'] .= '</tbody>
            <tfoot>
                <tr>
                    <th align="left">' . sprintf( __( 'Total', 'tourfic' ) ) . '</th>
                    <th align="right">' . wc_price( $tf_tours_data_price + $tour_extra_total ) . '</th>
                </tr>
            </tfoot>
        </table>';

	} else {
		# Show errors
		$response['status'] = 'error';

	}

	echo wp_json_encode( $response );
	die();
}