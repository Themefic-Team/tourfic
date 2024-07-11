<?php

namespace Tourfic\Classes\Woocommerce;

class Product_Extend extends \WC_Product_Data_Store_CPT implements \WC_Object_Data_Store_Interface, \WC_Product_Data_Store_Interface {


	/**
	 * Method to read a product from the database.
	 * @param \WC_Product
	 */
	public function read( &$product ) {
		$product->set_defaults();

		if ( (! $product->get_id() || ! ( $post_object = get_post( $product->get_id() ) ) || 'product' !== $post_object->post_type) && !empty($post_object) && 'tf_tours' !== $post_object->post_type && 'tf_hotel' !== $post_object->post_type && 'tf_apartment' !== $post_object->post_type ) {
			throw new \Exception( esc_html__( 'Invalid product.', 'tourfic' ) );
		}

		$id = $product->get_id();

		$product->set_props( array(
			'name'              => !empty($post_object->post_title) ? $post_object->post_title : '',
			'slug'              => !empty($post_object->post_name) ? $post_object->post_name : '',
			'date_created'      => !empty($post_object->post_date_gmt) && 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
			'date_modified'     => !empty( $post_object->post_modified_gmt) && 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
			'status'            => !empty($post_object->post_status) ? $post_object->post_status : '',
			'description'       => !empty($post_object->post_content) ? $post_object->post_content : '',
			'short_description' => !empty($post_object->post_excerpt) ? $post_object->post_excerpt : '',
			'parent_id'         => !empty($post_object->post_parent) ? $post_object->post_parent : '',
			'menu_order'        => !empty($post_object->menu_order) ? $post_object->menu_order : '',
			'reviews_allowed'   => !empty($post_object->comment_status) && 'open' === $post_object->comment_status,
		) );

		$this->read_attributes( $product );
		$this->read_downloads( $product );
		$this->read_visibility( $product );
		$this->read_product_data( $product );
		$this->read_extra_data( $product );
		$product->set_object_read( true );
	}


}