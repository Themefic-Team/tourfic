<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Changing meta keys to nice text when display
 * @param  string        $key  The meta key
 * @param  WC_Meta_Data  $meta The meta object
 * @param  WC_Order_Item $item The order item object
 * @return string        The title
 */
function tf_change_meta_key_title( $key, $meta, $item ) {
    
    // By using $meta-key we are sure we have the correct one.
    if ( 'room_name' === $meta->key ) { $key = __( 'Room Name', 'tourfic'); }
    if ( 'number_room_booked' === $meta->key ) { $key = __( 'Number of Room Booked', 'tourfic'); }
    if ( 'adult' === $meta->key ) { $key = __( 'Adult Number', 'tourfic'); }
    if ( 'child' === $meta->key ) { $key = __( 'Children Number', 'tourfic'); }
    if ( 'check_in' === $meta->key ) { $key = __( 'Check-in Date', 'tourfic'); }
    if ( 'check_out' === $meta->key ) { $key = __( 'Check-out Date', 'tourfic'); }
    if ( 'due' === $meta->key ) { $key = __( 'Due', 'tourfic'); }
    if ( '_tour_id' === $meta->key ) { $key = __( 'Tour ID', 'tourfic'); }
     
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
  
  $hidden_meta = array('_order_type', '_post_author', '_post_id', '_unique_id');
  
  return $hidden_meta;
}
add_filter( 'woocommerce_hidden_order_itemmeta', 'tf_hide_order_meta' );
?>