<?php

/**
 * Wishlist Functionality
 * @author KK
 */
// initiate ajax call for add to wishlist
add_action('wp_ajax_tf_add_to_wishlists', 'tf_add_to_wishlists');
add_action('wp_ajax_nopriv_tf_add_to_wishlists', 'tf_add_to_wishlists');
/**
 * add items for loogged in user's wishlists
 * 
 * @return void
 */
function tf_add_to_wishlists()
{
    // check nonce
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'wishlist-nonce')) {
        die('Whoops!');
    }
    if (isset($_POST)) {
        if (defined('DOING_AJAX') && DOING_AJAX) {

            // data to save
            $data = [
                'post_type' => $_POST['type'],
                'post_id'   => $_POST['post'],
            ];

            $user_id = get_current_user_id();
            // try to find some `wishlist_item` user meta
            $previous_wishlist_item = get_user_meta($user_id, 'wishlist_item', false);

            /**
             * First, the condition for when no wishlist_item user meta data exists
             **/
            if (empty($previous_wishlist_item)) {
                add_user_meta($user_id, 'wishlist_item', $data);
            }

            /**
             * Second, the condition for when some wishlist_item user_meta data already exists
             **/
            // search recursively through records returned from get_user_meta for the record you want to replace, as identified by `post_id` - credit: http://php.net/manual/en/function.array-search.php#116635
            $post_id = array_search($data['post_id'], array_column($previous_wishlist_item, 'post_id'));
            if (!empty($previous_wishlist_item) && false === $post_id) {
                // add if the wp_usermeta meta_key[wishlist_item] => meta_value[ $parameters[ $post_id ] ] pair does not exist
                add_user_meta($user_id, 'wishlist_item', $data);
            } else {
                // update if the wp_usermeta meta_key[wishlist_item] => meta_value[ $parameters[ $post_id ] ] pair already exists
                update_user_meta($user_id, 'wishlist_item', $data, $previous_wishlist_item[$post_id]);
            }
            wp_send_json_success(__("Item added to wishlist", 'tourfic'));
        }
    }
}


// shortcode for wishlists
add_shortcode('tf-wishlist', 'tf_wishlist_shortcode');
/**
 * shortcode for wishlists template generation
 * 
 * @param array $atts attributes from the shortcode
 * 
 * @return string
 */
function tf_wishlist_shortcode($atts)
{
    $defaults = array(
        'type' => null,
    );
    $atts = wp_parse_args($atts, $defaults);
    $type = $atts['type'];
    if (is_user_logged_in()) {
        return tf_generate_table_for_user($type);
    } else {
        // create a  holder for guest items
        return '<div class="tf-wishlist-holder" data-type="' . $atts['type']  . '" data-nonce="' . wp_create_nonce('populate-wishlist-guest-nonce') . '"> <table class="table"> <thead> <tr> <th>#</th> <th>Name</th> <th></th> </tr> </thead> <tbody> <tr> <td class="tf-text-preloader"> <div class="tf-bar"></div> </td> <td class="tf-text-preloader"> <div class="tf-bar"></div> </td> <td class="tf-text-preloader"> <div class="tf-bar"></div> </td> </tr> </tbody> </table></div>';
    }
}
/**
 * generate the table for loogged in user
 * 
 * @param string $type post type of wishlist items
 * 
 * @return string|false
 */
function tf_generate_table_for_user($type)
{
    // transform shortcode tags into array
    $type_array = !empty($type) ? explode(',', $type) : null;
    // holder for filtered items according to type
    $filtered = [];
    $user_id = get_current_user_id();
    // try to find all `wishlist_item` user meta
    $wishlist_items = get_user_meta($user_id, 'wishlist_item', false);
    // if there is type set, filter all items according to type
    if (is_array($type_array)) {
        foreach ($type_array as $value) {
            $filtered = array_merge($filtered, wp_list_filter($wishlist_items, ['post_type' => $value]));
        }
    } else {
        // otherwise, set whole collection as filtered
        $filtered = $wishlist_items;
    }
    // get only post ids from holder
    $ids = wp_list_pluck($filtered, 'post_id');
    // generate a list of post from ids
    return tf_generate_table($ids, $type);
}
// initiate ajax request for guest
add_action('wp_ajax_nopriv_tf_generate_table', 'tf_generate_table_guest');
/**
 * Generate table for guest
 * @return string
 */
function tf_generate_table_guest()
{
    // check nonce
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'populate-wishlist-guest-nonce')) {
        die('Whoops!');
    }
    if (isset($_POST)) {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            $ids = $_POST['ids'];
            wp_send_json_success(tf_generate_table($ids));
        }
    }
}

/**
 * Generate wishlist table from post ids
 * 
 * @param mixed $ids 
 * 
 * @return string|false
 */
function tf_generate_table($ids, $type = null)
{
    if (empty($ids)) {
        return __('<p>No items added yet!</p>', 'tourfic');
        exit;
    }
    ob_start();
    include TF_TEMPLATE_PATH . 'template-parts/wishlist.php';
    return ob_get_clean();
}
add_action('wp_ajax_tf_remove_wishlist', 'tf_remove_wishlist');
/**
 * Remove item from list
 * 
 * @return void
 */
function tf_remove_wishlist()
{
    // check nonce
    $nonce = $_GET['nonce'];
    if (!wp_verify_nonce($nonce, 'wishlist-nonce')) {
        die('Whoops!');
    }
    if (isset($_GET)) {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            global $wpdb;
            $id = $_GET['id'];
            $type = $_GET['type'];
            $user_id = get_current_user_id();
            $previous_wishlist_item = get_user_meta($user_id, 'wishlist_item', false);
            // search recursively through records returned from get_user_meta for the record you want to replace, as identified by `post_id` - credit: http://php.net/manual/en/function.array-search.php#116635
            $post_id = array_search($id, array_column($previous_wishlist_item, 'post_id'));
            delete_user_meta($user_id, 'wishlist_item', $previous_wishlist_item[$post_id]);

            wp_send_json_success(tf_generate_table_for_user($type));
        }
    }
}



/**
 * Is this item in the list ? 
 * 
 * @param mixed $id post id
 * 
 * @return bool
 */
function tf_has_item_in_wishlist($id)
{
    // when user is logged in handle item with db
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $previous_wishlist_item = get_user_meta($user_id, 'wishlist_item', false);
        $post_id = array_search($id, array_column($previous_wishlist_item, 'post_id'));
        return $post_id !== false;
    } else {
        // otherwise, just return false. javascript will do the rest
        return false;
    }
}
