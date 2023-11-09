<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Enable tour and hotel add to cart
 * 
 * Extend WooCommerce Product data
 *
 * @since 1.0.0
 */
class TF_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT implements WC_Object_Data_Store_Interface, WC_Product_Data_Store_Interface {


    /**
     * Method to read a product from the database.
     * @param WC_Product
     */
    public function read( &$product ) {
        $product->set_defaults();

        if ( (! $product->get_id() || ! ( $post_object = get_post( $product->get_id() ) ) || 'product' !== $post_object->post_type) && 'tf_tours' !== $post_object->post_type && 'tf_hotel' !== $post_object->post_type && 'tf_apartment' !== $post_object->post_type ) {
            throw new Exception( __( 'Invalid product.', 'tourfic' ) );
        }

        $id = $product->get_id();

        $product->set_props( array(
            'name'              => $post_object->post_title,
            'slug'              => $post_object->post_name,
            'date_created'      => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
            'date_modified'     => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
            'status'            => $post_object->post_status,
            'description'       => $post_object->post_content,
            'short_description' => $post_object->post_excerpt,
            'parent_id'         => $post_object->post_parent,
            'menu_order'        => $post_object->menu_order,
            'reviews_allowed'   => 'open' === $post_object->comment_status,
        ) );

        $this->read_attributes( $product );
        $this->read_downloads( $product );
        $this->read_visibility( $product );
        $this->read_product_data( $product );
        $this->read_extra_data( $product );
        $product->set_object_read( true );
    }


}

function tf_woocommerce_data_stores( $stores ) {

    require_once WP_PLUGIN_DIR . '/woocommerce/includes/class-wc-data-store.php';
    $stores['product'] = 'TF_Product_Data_Store_CPT';

    return $stores;
}
add_filter( 'woocommerce_data_stores', 'tf_woocommerce_data_stores' );

/**
 * Add _price post_meta to hotels & tours upon publish
 * 
 * _pirce field is required for WooCommerce add to cart
 */
function tf_add_price_field_to_post($post_id, $post) {
    update_post_meta( $post_id, '_price', '0' );
}
add_action( 'publish_tf_apartment', 'tf_add_price_field_to_post', 10, 2 );
add_action( 'publish_tf_hotel', 'tf_add_price_field_to_post', 10, 2 );
add_action( 'publish_tf_tours', 'tf_add_price_field_to_post', 10, 2 );

?>