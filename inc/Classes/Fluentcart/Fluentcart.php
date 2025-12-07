<?php
namespace Tourfic\Classes\Fluentcart;

defined('ABSPATH') || exit;

use FluentCart\App\Models\Product;
use FluentCart\App\Models\ProductDetail;
use FluentCart\App\Models\Cart;
use FluentCart\Api\StoreSettings;
use FluentCart\Api\CurrencySettings;

class Fluentcart {

    use \Tourfic\Traits\Singleton;

    public function __construct() {
        add_action( 'publish_tf_tours', [ $this, 'sync_product' ], 10, 2 );
        add_action( 'publish_tf_hotel', [ $this, 'sync_product' ], 10, 2 );
        add_action( 'publish_tf_apartment', [ $this, 'sync_product' ], 10, 2 );
        add_action( 'publish_tf_carrental', [ $this, 'sync_product' ], 10, 2 );

        // Hook FluentCart order creation
        // add_action( 'fluent_cart/order_created', [ $this, 'handle_order_created' ], 10, 2 );
    }

    public function sync_product($post_id, $post) {
        $fluentcart_product_id = get_post_meta($post_id, '_fluentcart_product_id', true);
        $product = Product::find($fluentcart_product_id);

        if (!$product) {
            $product = Product::create([
                'post_title' => $post->post_title,
                'post_content' => $post->post_content,
                'post_date' => $post->post_date,
                'post_status' => 'publish',
                'post_type' => 'fc_product',
            ]);
        } else {
            $product->post_title = $post->post_title;
            $product->post_content = $post->post_content;
            $product->post_date    = $post->post_date;
            $product->post_status = 'publish';
        }

        // Update product fields
        $product->save();

        // Optionally, store the fluentcart product ID back in the tour/hotel post
        update_post_meta($post_id, '_fluentcart_product_id', $product->ID);

        $detail = ProductDetail::create([
            'post_id'            => $product->ID,
            'fulfillment_type'   => 'digital',
            'min_price'          => 0,
            'max_price'          => 0,
            'variation_type'     => 'simple',
            'stock_availability' => 'in-stock',
            'manage_stock'       => 0,
            'manage_downloadable'=> 0,
            'other_info'         => [
                'tf_post_id' => $post_id,
                'tf_type'    => get_post_type($post_id),
            ],
        ]);

        $detail->save();
    }

    static function tf_add_to_fluentcart($post_id, $total_price, $meta_data = []) {
		if (!class_exists('\FluentCart\App\Models\Cart')) {
			return new \WP_Error('fluentcart_missing', __('FluentCart plugin is not active.', 'tourfic'));
		}

		$store_settings = new StoreSettings();

		// Try to load existing cart using hash cookie
		$cart_hash = isset( $_COOKIE['fct_cart_hash'] ) ? sanitize_text_field( $_COOKIE['fct_cart_hash'] ) : null;
		$cart = $cart_hash ? Cart::find( $cart_hash ) : null;

		// If no cart found, create new one
		if ( ! $cart ) {
			$cart = Cart::create([
				'cart_hash'   => 'cart_' . wp_generate_uuid4(),
				'user_id'     => get_current_user_id(),
				'customer_id' => get_current_user_id(),
				'email'       => wp_get_current_user()->user_email ?? '',
				'stage'       => 'active'
			]);
		}

		$meta_title_parts = [];

		if ( isset( $meta_data['tf_apartment_data']['adults'] ) && $meta_data['tf_apartment_data']['adults'] >= 1 ) {
			$meta_title_parts[] = sprintf( __( 'Adults: %d', 'tourfic' ), $meta_data['tf_apartment_data']['adults'] );
		}

		if ( isset( $meta_data['tf_apartment_data']['children'] ) && $meta_data['tf_apartment_data']['children'] >= 1 ) {
			$meta_title_parts[] = sprintf( __( 'Children: %d', 'tourfic' ), $meta_data['tf_apartment_data']['children'] );
		}

		if ( isset( $meta_data['tf_apartment_data']['infant'] ) && $meta_data['tf_apartment_data']['infant'] >= 1 ) {
			$meta_title_parts[] = sprintf( __( 'Infant: %d', 'tourfic' ), $meta_data['tf_apartment_data']['infant'] );
		}

		if ( !empty( $meta_data['tf_apartment_data']['check_in_out_date'] ) ) {
			$meta_title_parts[] = sprintf( __( 'Check-in-out: %s', 'tourfic' ), $meta_data['tf_apartment_data']['check_in_out_date'] );
		}

		$meta_title = implode(' | ', $meta_title_parts);
		$fluent_product_id = get_post_meta($post_id, '_fluentcart_product_id', true);

		$cart_item = [
			'object_id'        	   => $fluent_product_id,
			'product_id'           => $fluent_product_id,
			'post_title'           => get_the_title($post_id),
			'title'				   => $meta_title,
			'quantity'             => 1,
			// 'price'                => (float) $total_price,
			'unit_price'           => (float) $total_price,
			// 'line_total'           => (float) $total_price,
			'subtotal'             => (float) $total_price,
			// 'line_total_formatted' => CurrencySettings::getFormattedPrice($total_price),
			// 'fulfillment_type'     => 'digital',
			'featured_media'       => get_the_post_thumbnail_url($post_id, 'full'),
			// 'view_url'             => get_permalink($post_id),
			// 'other_info'           => $meta_data,
			// 'variation_type'       => 'simple',
		];

		// Add item and save cart
		$cart->addItem($cart_item);
		// $cart->addItem(['product_id' => 557, 'quantity' => 2]);
		// $cart->delete();

		// Save cart hash cookie
		setcookie('fct_cart_hash', $cart->cart_hash, time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);

		// Generate the checkout URL
		// $checkout_url = $store_settings->getCheckoutPage();
		$checkout_url = $store_settings->getCartPage();

		return $checkout_url;
	}

    public function handle_order_created( $order_id, $order ) {
        $order_items = $order->items;

        foreach ( $order_items as $item ) {
            $post_id = $item->product_id;
            $post_type = get_post_type( $post_id );

            if ( in_array( $post_type, [ 'tf_tours', 'tf_hotel', 'tf_apartment', 'tf_carrental' ], true ) ) {
                update_post_meta( $order_id, '_order_type', str_replace( 'tf_', '', $post_type ) );
                update_post_meta( $order_id, '_post_author', get_post_field( 'post_author', $post_id ) );
            }
        }

        // Optional: sync to your custom table like tf_order_data
        do_action( 'tf_fluentcart_order_synced', $order_id, $order );
    }
}
