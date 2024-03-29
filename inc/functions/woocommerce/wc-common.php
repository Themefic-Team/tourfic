<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Changing meta keys to nice text when display
 * @param  string        $key  The meta key
 * @param  WC_Meta_Data  $meta The meta object
 * @param  WC_Order_Item $item The order item object
 * @return string        The title
 * @since  1.0.0
 */
function tf_change_meta_key_title( $key, $meta, $item ) {
    
    // By using $meta-key we are sure we have the correct one.
    if ( 'room_name' === $meta->key ) { $key = esc_html__( 'Room Name', 'tourfic'); }
    if ( 'number_room_booked' === $meta->key ) { $key = esc_html__( 'Number of Room Booked', 'tourfic'); }
    if ( 'adult' === $meta->key ) { $key = esc_html__( 'Adult Number', 'tourfic'); }
    if ( 'child' === $meta->key ) { $key = esc_html__( 'Children Number', 'tourfic'); }
    if ( 'check_in' === $meta->key ) { $key = esc_html__( 'Check-in Date', 'tourfic'); }
    if ( 'check_out' === $meta->key ) { $key = esc_html__( 'Check-out Date', 'tourfic'); }
    if ( 'due' === $meta->key ) { $key = esc_html__( 'Due', 'tourfic'); }
    if ( '_tour_id' === $meta->key ) { $key = esc_html__( 'Tour ID', 'tourfic'); }
    if ( 'Adults' === $meta->key ) { $key = esc_html__( 'Adults', 'tourfic'); }
	if ( 'Children' === $meta->key ) { $key = esc_html__( 'Children', 'tourfic'); }
	if ( 'Infants' === $meta->key ) { $key = esc_html__( 'Infants', 'tourfic'); }
	if ( 'Tour Date' === $meta->key ) { $key = esc_html__( 'Tour Date', 'tourfic'); }
	if ( 'Tour Time' === $meta->key ) { $key = esc_html__( 'Tour Time', 'tourfic'); }
	if ( 'Tour Extra' === $meta->key ) { $key = esc_html__( 'Tour Extra', 'tourfic'); }
	if ( 'Due' === $meta->key ) { $key = esc_html__( 'Due', 'tourfic'); }

    return $key;
}
add_filter( 'woocommerce_order_item_display_meta_key', 'tf_change_meta_key_title', 20, 3 );

/**
 * Hiding order item meta from order details page
 * 
 * @param array $hidden_meta Array of all meta data to hide.
 *
 * @return array
 */
function tf_hide_order_meta( $hidden_meta ) {
  
  $hidden_meta = array('_order_type', '_post_author', '_post_id', '_unique_id', '_tour_unique_id', '_visitor_details');
  
  return $hidden_meta;
}
add_filter( 'woocommerce_hidden_order_itemmeta', 'tf_hide_order_meta' );
?>