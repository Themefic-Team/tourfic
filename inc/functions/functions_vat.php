<?php 
add_action('woocommerce_cart_calculate_fees', 'custom_tax_surcharge_for_swiss', 10, 1);

function custom_tax_surcharge_for_swiss($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    // Only for Swiss country (if not we exit)
    if ('BD' != WC()->customer->get_shipping_country()) return;

    $percent = 8;
    $surcharge = 0;
    $tf_vat_charge = 0;

    // Loop through cart items to find 'tf_hotel_data' meta
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if(!empty($cart_item['tf_hotel_data'])){
            $single_id = !empty($cart_item['tf_hotel_data']['post_id']) ? $cart_item['tf_hotel_data']['post_id'] : '';
            $single_price = !empty($cart_item['tf_hotel_data']['price_total']) ? $cart_item['tf_hotel_data']['price_total'] : '';

            //Meta Data
            $meta = get_post_meta( $single_id, 'tf_hotels_opt', true );
            if(!empty($meta['is_taxable'])){
                $tf_vat_charge += (float)$single_price * $percent / 100;
            }
        }elseif(!empty($cart_item['tf_tours_data'])){
            $single_id = !empty($cart_item['tf_tours_data']['tour_id']) ? $cart_item['tf_tours_data']['tour_id'] : '';
            $single_price = !empty($cart_item['tf_tours_data']['price']) ? $cart_item['tf_tours_data']['price'] : '';

            //Meta Data
            $meta = get_post_meta( $single_id, 'tf_tours_opt', true );
            if(!empty($meta['is_taxable'])){
                $tf_vat_charge += (float)$single_price * $percent / 100;
            }
        }else{

        }
    }

    var_dump($tf_vat_charge);

    // Calculation using $surcharge
    $surcharge = ($cart->cart_contents_total + $cart->shipping_total) * $percent / 100;

    // Add the fee (tax third argument disabled: false)
    $cart->add_fee(__('TAX', 'woocommerce') . " ($percent%)", $surcharge, false);
}
