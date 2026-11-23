<?php
/** Synthetic real-WordPress REST dispatch; requires the marked disposable test installation. */
if ( ! defined( 'ABSPATH' ) || ! file_exists( ABSPATH . '.tourfic-booking-wishlist-test' ) ) {
	throw new RuntimeException( 'Disposable synthetic site required.' );
}
global $checks, $failed;
$checks = 0; $failed = array();
function tourfic_wishlist_fixture_expect( $condition, $label ) {
	++$GLOBALS['checks']; if ( ! $condition ) { $GLOBALS['failed'][] = $label; }
}
function tourfic_wishlist_fixture_request( $method, $params = array() ) {
	$request = new WP_REST_Request( $method, '/tf/v1/user-wishlist' );
	$request->set_query_params( $params );
	return rest_do_request( $request );
}
$a = wp_insert_user( array( 'user_login' => 'wishlist-fixture-' . wp_generate_uuid4(), 'role' => 'customer' ) );
$b = wp_insert_user( array( 'user_login' => 'wishlist-fixture-' . wp_generate_uuid4(), 'role' => 'customer' ) );
$ids = array();
foreach ( array( 'publish', 'draft', 'private', 'pending', 'trash' ) as $status ) {
	$id = wp_insert_post( array( 'post_type' => 'tf_hotel', 'post_status' => $status, 'post_title' => 'Synthetic wishlist fixture', 'post_content' => 'Synthetic restricted body', 'post_password' => '' ) );
	$ids[ $status ] = $id; add_user_meta( $a, 'wishlist_item', array( 'post_id' => $id, 'post_type' => 'tf_hotel' ) );
}
$protected = wp_insert_post( array( 'post_type' => 'tf_hotel', 'post_status' => 'publish', 'post_title' => 'Synthetic protected listing', 'post_password' => '0' ) );
add_user_meta( $a, 'wishlist_item', array( 'post_id' => $protected ) );
add_user_meta( $b, 'wishlist_item', array( 'post_id' => $ids['publish'] ) );
rest_get_server();
wp_set_current_user( 0 );
tourfic_wishlist_fixture_expect( 401 === tourfic_wishlist_fixture_request( 'GET' )->get_status(), 'Guest denied' );
wp_set_current_user( $a );
$response = tourfic_wishlist_fixture_request( 'GET', array( 'user_id' => $b ) );
tourfic_wishlist_fixture_expect( 200 === $response->get_status(), 'Customer own wishlist available' );
$data = $response->get_data();
tourfic_wishlist_fixture_expect( 1 === count( $data ) && $data[0]['ID'] === $ids['publish'], 'Only eligible published listing exposed' );
tourfic_wishlist_fixture_expect( array( 'ID', 'post_title', 'post_type' ) === array_keys( $data[0] ), 'Only three declared response fields' );
$before = get_user_meta( $a, 'wishlist_item', false );
tourfic_wishlist_fixture_expect( 405 === tourfic_wishlist_fixture_request( 'GET', array( 'post_id' => $ids['publish'], 'remove' => 'true' ) )->get_status(), 'GET cannot mutate' );
tourfic_wishlist_fixture_expect( $before === get_user_meta( $a, 'wishlist_item', false ), 'GET leaves rows unchanged' );
foreach ( array( array( $ids['publish'] ), '-1', '1.5', '0', '1x', '9999999999999999999999' ) as $id ) {
	tourfic_wishlist_fixture_expect( 400 === tourfic_wishlist_fixture_request( 'DELETE', array( 'post_id' => $id ) )->get_status(), 'Malformed removal rejected' );
}
tourfic_wishlist_fixture_expect( 200 === tourfic_wishlist_fixture_request( 'DELETE', array( 'post_id' => 99999999 ) )->get_status(), 'Missing removal is safe no-op' );
tourfic_wishlist_fixture_expect( $before === get_user_meta( $a, 'wishlist_item', false ), 'Missing removal preserves index zero' );
$keys = \Tourfic\Classes\TF_API_Keys::instance();
$property = new ReflectionProperty( $keys, 'authenticated_key_record' );
$property->setAccessible( true );
$property->setValue( $keys, (object) array( 'permissions' => wp_json_encode( array( 'read' ) ) ) );
tourfic_wishlist_fixture_expect( 200 === tourfic_wishlist_fixture_request( 'GET' )->get_status(), 'Read-only key permits read' );
tourfic_wishlist_fixture_expect( 403 === tourfic_wishlist_fixture_request( 'DELETE', array( 'post_id' => $ids['publish'] ) )->get_status(), 'Read-only key rejects removal' );
tourfic_wishlist_fixture_expect( $before === get_user_meta( $a, 'wishlist_item', false ), 'Read-only key does not mutate' );
$property->setValue( $keys, (object) array( 'permissions' => wp_json_encode( array( 'write' ) ) ) );
tourfic_wishlist_fixture_expect( 200 === tourfic_wishlist_fixture_request( 'DELETE', array( 'post_id' => $ids['publish'], 'user_id' => $b ) )->get_status(), 'Write key removes own item' );
tourfic_wishlist_fixture_expect( 1 === count( get_user_meta( $b, 'wishlist_item', false ) ), 'Other user untouched' );
$property->setValue( $keys, null );
echo wp_json_encode( array( 'checks' => $checks, 'failures' => $failed ) ) . "\n";
if ( $failed ) { throw new RuntimeException( 'Wishlist security assertions failed.' ); }
