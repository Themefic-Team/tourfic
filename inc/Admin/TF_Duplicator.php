<?php

namespace Tourfic\Admin;

class TF_Duplicator {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		add_filter('post_row_actions', array($this, 'tf_add_duplicate_post_button'), 10, 2);
		add_action('wp_ajax_tf_duplicate_post_data', array($this, 'tf_duplicate_post_data_function'));
	}

	function tf_add_duplicate_post_button($actions, $post) {
		if (current_user_can('edit_posts') && ( "tf_tours"==$post->post_type || "tf_hotel"==$post->post_type || "tf_apartment"==$post->post_type )) {
			if("tf_tours"==$post->post_type){
				$tf_duplicate_label = esc_html__("Duplicate Tour", "tourfic");
			}elseif("tf_hotel"==$post->post_type){
				$tf_duplicate_label = esc_html__("Duplicate Hotel", "tourfic");
			}else{
				$tf_duplicate_label = esc_html__("Duplicate Apartment", "tourfic");
			}
			$tf_duplicate_nonce = wp_create_nonce('tf_duplicate_nonce_' . $post->ID);
			$actions['duplicate'] = '<a class="tf-post-data-duplicate" href="#" data-postid="'. $post->ID .'" data-posttype="' . $post->post_type . '" data-nonce="' . esc_attr($tf_duplicate_nonce) . '">'. $tf_duplicate_label .'</a>';
		}
		return $actions;
	}

	function tf_duplicate_post_data_function() {

		// Verify nonce
		check_ajax_referer('tf_duplicate_nonce_' . $_POST['postID'], 'security');

		$postID = intval($_POST['postID']);
		$postType = esc_attr($_POST['postType']);
		if( "tf_hotel"==$postType ){
			$meta = get_post_meta( $postID, 'tf_hotels_opt', true );
		}
		if( "tf_tours"==$postType ){
			$meta = get_post_meta( $postID, 'tf_tours_opt', true );
		}
		if( "tf_apartment"==$postType ){
			$meta = get_post_meta( $postID, 'tf_apartment_opt', true );
		}

		$tf_duplicate_post = wp_insert_post(wp_slash([
			'post_type' => $postType,
			'post_status' => 'publish',
			'post_title' => get_the_title($postID) . ' (Copy)',
			'post_content' => get_post_field('post_content', $postID)
		]));

		//Update Post Meta
		if( "tf_hotel"==$postType ){
			update_post_meta($tf_duplicate_post, 'tf_hotels_opt', $meta);
		}
		if( "tf_tours"==$postType ){
			update_post_meta($tf_duplicate_post, 'tf_tours_opt', $meta);
		}
		if( "tf_apartment"==$postType ){
			update_post_meta($tf_duplicate_post, 'tf_apartment_opt', $meta);
		}

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
}