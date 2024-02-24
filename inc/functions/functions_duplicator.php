<?php 

// Add Duplicate Button Into a Row Actions
if ( ! function_exists( 'tf_add_duplicate_post_button' ) ) {
    function tf_add_duplicate_post_button($actions, $post) {
        if (current_user_can('edit_posts') && ( "tf_tours"==$post->post_type || "tf_hotel"==$post->post_type || "tf_apartment"==$post->post_type )) {
            if("tf_tours"==$post->post_type){
                $tf_duplicate_label = __("Duplicate Tour", "tourfic");
            }elseif("tf_hotel"==$post->post_type){
                $tf_duplicate_label = __("Duplicate Hotel", "tourfic");
            }else{
                $tf_duplicate_label = __("Duplicate Apartment", "tourfic");
            }
            $tf_duplicate_nonce = wp_create_nonce('tf_duplicate_nonce_' . $post->ID);
            $actions['duplicate'] = '<a class="tf-post-data-duplicate" href="#" data-postid="'. $post->ID .'" data-posttype="' . $post->post_type . '" data-nonce="' . esc_attr($tf_duplicate_nonce) . '">'. $tf_duplicate_label .'</a>';
        }
        return $actions;
    }
}
add_filter('post_row_actions', 'tf_add_duplicate_post_button', 10, 2);


// Duplicate Post Ajax Action
add_action('wp_ajax_tf_duplicate_post_data', 'tf_duplicate_post_data_function');
function tf_duplicate_post_data_function() {

    // Verify nonce
    check_ajax_referer('tf_duplicate_nonce_' . $_POST['postID'], 'security');
    
    $postID = intval($_POST['postID']);
    $postType = $_POST['postType'];
    $post_meta     = array();
    if( "tf_hotel"==$postType ){
	    $meta = get_post_meta( $postID, 'tf_hotels_opt', true );
        $post_meta['tf_hotels_opt'] = $meta;
    }
    if( "tf_tours"==$postType ){
	    $meta = get_post_meta( $postID, 'tf_tours_opt', true );
        $post_meta['tf_tours_opt'] = $meta;
    }
    if( "tf_apartment"==$postType ){
	    $meta = get_post_meta( $postID, 'tf_apartment_opt', true );
        $post_meta['tf_apartment_opt'] = $meta;
    }

    $tf_duplicate_post = wp_insert_post(wp_slash([
        'post_type' => $postType,
        'post_status' => 'publish',
        'post_title' => get_the_title($postID) . ' (Copy)',
        'post_content' => get_post_field('post_content', $postID),
        'meta_input'   => $post_meta,
    ]));

    // Duplicate featured image
    $featured_image_id = get_post_thumbnail_id($postID);
    if ($featured_image_id) {
        set_post_thumbnail($tf_duplicate_post, $featured_image_id);
    }

    // Duplicate taxonomy terms
    $tf_taxonomies = get_object_taxonomies($postType);

    if (!empty($tf_taxonomies)) {
        foreach ($tf_taxonomies as $taxonomy) {
            $terms = wp_get_post_terms($postID, $taxonomy);
            if (!empty($terms)) {
                $termIds = [];
                foreach ($terms as $term) {
                    $termIds[] = $term->term_id;
                }
                wp_set_post_terms($tf_duplicate_post, $termIds, $taxonomy, false);
            }
        }
    }

    if (!is_wp_error($tf_duplicate_post)) {
        // Return a success response
        echo 'success';
    } else {
        // Return an error response
        echo 'error';
    }

    wp_die();
}
