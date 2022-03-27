<?php 
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Require some taxonomies
 * 
 * hotel_location, tour_destination
 */
function tf_required_taxonomies( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
		return;
	}
	global $post_type;

	$tf_is_gutenberg_active = tf_is_gutenberg_active();

	$default_post_types = array(
        'tf_hotel' => array(
			'hotel_location' => array(
				'message' => 'Please select a location before publishing this hotel.'
			)
        ),
		'tf_tours' => array(
			'tour_destination' => array(
				'message' => 'Please select a destination before publishing this tour.'
			)
        ),
	);

	$post_types = apply_filters( 'tf_post_types', $default_post_types );

	if ( ! is_array( $post_types ) ) {
		return;
	}

	if ( ! isset( $post_types[ $post_type ] ) ) {
		return;
	}

	if ( ! isset( $post_types[ $post_type ] ) || ! is_array( $post_types[ $post_type ] ) || empty( $post_types[ $post_type ] ) ) {
		if ( is_string( $post_types[ $post_type ] ) ) {
			$post_types[ $post_type ] = array(
				'taxonomies' => array(
					$post_types[ $post_type ]
				)
			);
		} else if ( is_array( $post_types[ $post_type ] ) ) {
			$post_types[ $post_type ] = array(
				'taxonomies' => $post_types[ $post_type ]
			);
		} else {
			return;
		}
	}

	$post_type_taxonomies = get_object_taxonomies( $post_type );

	foreach ( $post_types[ $post_type ] as $taxonomy => $config ) {
		if ( is_int( $taxonomy ) && is_string( $config ) ) {
			unset( $post_types[ $post_type ][ $taxonomy ] );
			$taxonomy = $config;

			$post_types[ $post_type ][ $taxonomy ] = $config = array();
		}

		if ( ! taxonomy_exists( $taxonomy ) || ! in_array( $taxonomy, $post_type_taxonomies ) ) {
			unset( $post_types[ $post_type ][ $taxonomy ] );
			continue;
		}

		$taxonomy_object = get_taxonomy( $taxonomy );
		$taxonomy_labels = get_taxonomy_labels( $taxonomy_object );

		$post_types[ $post_type ][ $taxonomy ]['type'] = $config['type'] = ( is_taxonomy_hierarchical( $taxonomy ) ? 'hierarchical' : 'non-hierarchical' );

		if ( ! isset( $config['message'] ) || $taxonomy === $config ) {
			$post_type_labels  = get_post_type_labels( get_post_type_object( $post_type ) );
			$config['message'] = "Please choose at least one {$taxonomy_labels->singular_name} before publishing this {$post_type_labels->singular_name}.";
		}

		$post_types[ $post_type ][ $taxonomy ]['message'] = __( $config['message'], 'require-post-category' );

		if ( $tf_is_gutenberg_active && !empty($taxonomy_object->rest_base) && $taxonomy !== $taxonomy_object->rest_base ) {
			$post_types[ $post_type ][ $taxonomy_object->rest_base ] = $post_types[ $post_type ][ $taxonomy ];
			unset( $post_types[ $post_type ][ $taxonomy ] );
		}
	}

	if ( empty( $post_types[ $post_type ] ) ) {
		return;
	}

	if ( $tf_is_gutenberg_active ) {
		wp_enqueue_script( 'tf-required', TF_ADMIN_URL . 'assets/js/required-taxonomies-gutenberg.js', array(
			'jquery', 'wp-data', 'wp-editor', 'wp-edit-post'
		));
	} else {
		wp_enqueue_script( 'tf-required', TF_ADMIN_URL . 'assets/js/required-taxonomies.js', array( 'jquery' ), false, true );
	}

	wp_localize_script( 'tf-required', 'tf_params', array(
			'taxonomies' => $post_types[ $post_type ],
			'error'      => false,
			'tour_location_required' => __('Tour Location is a required field!', 'tourfic'),
		)
	);

}
add_action( 'admin_enqueue_scripts', 'tf_required_taxonomies' );

function tf_is_gutenberg_active() {
	if ( function_exists( 'is_gutenberg_page' ) &&
	     is_gutenberg_page()
	) {
		return true;
	}

	$current_screen = get_current_screen();

	if ( method_exists( $current_screen, 'is_block_editor' ) &&
	     $current_screen->is_block_editor()
	) {
		return true;
	}
	
	return false;
}

?>