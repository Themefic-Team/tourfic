<?php
/** Synthetic tests of real wishlist REST, AJAX and template code; no WordPress/database bootstrap. */
namespace Tourfic\Traits { trait Singleton {} }
namespace {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
	define( 'TOURFIC_TEMPLATE_PATH', ABSPATH . 'templates/' );
	define( 'DOING_AJAX', true );
	$actor = 17; $can_read = true; $deleted = array(); $checks = 0; $failures = array();
	$posts = $user_meta = array();
	class WP_Error {
		public $code; public $message; public $data;
		public function __construct( $code, $message = '', $data = array() ) { $this->code = $code; $this->message = $message; $this->data = $data; }
		public function get_error_data() { return $this->data; }
		public function get_error_message() { return $this->message; }
	}
	class WP_REST_Request {
		private $params; private $method;
		public function __construct( $params = array(), $method = 'GET' ) { $this->params = $params; $this->method = $method; }
		public function get_param( $key ) { return $this->params[ $key ] ?? null; }
		public function get_method() { return $this->method; }
		public function get_url_params() { return array(); }
		public function get_query_params() { return $this->params; }
		public function get_body_params() { return array(); }
		public function get_json_params() { return null; }
	}
	class Tourfic_Wishlist_Response extends \RuntimeException {
		public $response; public $status;
		public function __construct( $response, $status ) { $this->response = $response; $this->status = $status; }
	}
	function wishlist_expect( $value, $label ) { ++$GLOBALS['checks']; if ( ! $value ) { $GLOBALS['failures'][] = $label; } }
	function add_action() {} function add_filter() {}
	function get_current_user_id() { return $GLOBALS['actor']; }
	function is_user_logged_in() { return get_current_user_id() > 0; }
	function current_user_can( $cap, ...$args ) { return $GLOBALS['can_read'] && is_user_logged_in(); }
	function get_userdata( $id ) { return (object) array( 'ID' => $id, 'roles' => array( 'customer' ) ); }
	function get_post( $id ) { return $GLOBALS['posts'][ (int) $id ] ?? null; }
	function get_post_type( $id ) { return get_post( $id )->post_type ?? false; }
	function get_post_status( $id ) { return get_post( $id )->post_status ?? false; }
	function get_user_meta( $id, $key, $single = false ) { return $GLOBALS['user_meta'][ $id ] ?? array(); }
	function add_user_meta( $id, $key, $value ) { $GLOBALS['user_meta'][ $id ][] = $value; return 1; }
	function update_user_meta( $id, $key, $value, $previous = null ) {
		foreach ( $GLOBALS['user_meta'][ $id ] as $i => $stored ) { if ( $previous === $stored ) { $GLOBALS['user_meta'][ $id ][ $i ] = $value; } }
	}
	function delete_user_meta( $id, $key, $value ) {
		$GLOBALS['deleted'][] = array( $id, $value );
		$GLOBALS['user_meta'][ $id ] = array_values( array_filter( $GLOBALS['user_meta'][ $id ] ?? array(), function( $stored ) use ( $value ) { return $stored !== $value; } ) );
		return true;
	}
	function wp_verify_nonce( $value, $action ) { return 'fixture-nonce' === $value; }
	function wp_create_nonce() { return 'fixture-nonce'; }
	function wp_unslash( $value ) { return $value; }
	function absint( $value ) { return abs( (int) $value ); }
	function sanitize_text_field( $value ) { return is_scalar( $value ) ? trim( strip_tags( (string) $value ) ) : ''; }
	function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES ); }
	function esc_attr( $value ) { return esc_html( $value ); }
	function esc_url( $value ) { return $value; }
	function __( $value, $domain = '' ) { return $value; }
	function esc_html__( $value, $domain = '' ) { return $value; }
	function esc_html_e( $value, $domain = '' ) { echo esc_html( $value ); }
	function esc_attr_e( $value, $domain = '' ) { echo esc_attr( $value ); }
	function get_permalink( $id ) { return 'https://example.invalid/listing/' . $id; }
	function the_permalink( $id ) { echo get_permalink( $id ); }
	function is_wp_error( $value ) { return $value instanceof WP_Error; }
	function wp_send_json_success( $value, $status = 200 ) { throw new Tourfic_Wishlist_Response( array( 'success' => true, 'data' => $value ), $status ); }
	function wp_send_json_error( $value, $status = 200 ) { throw new Tourfic_Wishlist_Response( array( 'success' => false, 'data' => $value ), $status ); }
	function wp_list_pluck( $items, $key ) { return array_column( $items, $key ); }
	function wp_list_filter( $items, $filter ) { return array_filter( $items, function( $item ) use ( $filter ) { return ! array_diff_assoc( $filter, $item ); } ); }
	function wishlist_ajax( $callback, $post = array(), $get = array() ) {
		$_POST = $post; $_GET = $get;
		try { $callback(); } catch ( Tourfic_Wishlist_Response $result ) { return $result; }
		return null;
	}
	require ABSPATH . 'inc/App/Wishlist.php';
	$pro = in_array( '--pro', $argv, true );
	if ( $pro ) {
		require dirname( ABSPATH ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_Rest_API.php';
		require dirname( ABSPATH ) . '/tourfic-pro/inc/frontend-dashboard/classes/TF_FD_User_Rest_API.php';
		$api = new TF_FD_User_Rest_API(); $method = 'tf_fd_user_wishlist';
	} else {
		require ABSPATH . 'inc/Classes/REST_API/TF_Rest_API.php';
		require ABSPATH . 'inc/Classes/REST_API/TF_User_Rest_API.php';
		$api = new Tourfic_User_Rest_API(); $method = 'tf_user_wishlist';
	}
	$wishlist = new \Tourfic\App\Wishlist();
	foreach ( array( 'tf_hotel', 'tf_tours', 'tf_apartment', 'tf_carrental' ) as $i => $type ) {
		$posts[ 100 + $i ] = (object) array( 'ID' => 100 + $i, 'post_type' => $type, 'post_status' => 'publish', 'post_title' => 'Public fixture ' . $i, 'post_password' => '', 'post_content' => 'Unneeded synthetic content' );
	}
	foreach ( array( 'private', 'draft', 'pending', 'trash', 'future', 'publish', 'publish', 'publish' ) as $i => $status ) {
		$posts[ 200 + $i ] = (object) array( 'ID' => 200 + $i, 'post_type' => 207 === 200 + $i ? 'post' : 'tf_hotel', 'post_status' => $status, 'post_title' => 'Restricted fixture ' . $i, 'post_password' => 205 === 200 + $i ? 'synthetic-protection' : ( 206 === 200 + $i ? '0' : '' ), 'post_content' => 'Restricted synthetic content' );
	}
	$stored = array_map( function( $id ) { return array( 'post_id' => $id, 'post_type' => get_post_type( $id ) ); }, array_keys( $posts ) );
	$user_meta[17] = $stored; $user_meta[29] = array( array( 'post_id' => 103, 'post_type' => 'tf_carrental' ) );
	$result = $api->$method( new WP_REST_Request( array( 'user_id' => 29 ) ) );
	wishlist_expect( ! is_wp_error( $result ) && 4 === count( $result ), 'REST returns only current user public supported listings.' );
	foreach ( is_array( $result ) ? $result : array() as $item ) {
		wishlist_expect( is_array( $item ) && array( 'ID', 'post_title', 'post_type' ) === array_keys( $item ), 'REST uses the explicit three-field dashboard schema.' );
	}
	$actor = 0;
	$result = $api->$method( new WP_REST_Request() );
	wishlist_expect( is_wp_error( $result ) && 401 === $result->data['status'], 'Direct guest REST access is denied.' );
	$actor = 17; $can_read = false;
	wishlist_expect( is_wp_error( $api->$method( new WP_REST_Request() ) ), 'Direct REST access requires account read capability.' );
	$can_read = true;
	$user_meta[17] = array_slice( $stored, 0, 4 ); $deleted = array();
	$api->$method( new WP_REST_Request( array( 'remove' => true, 'post_id' => 9999 ) ) );
	wishlist_expect( ! $deleted && 4 === count( $user_meta[17] ), 'An unmatched legacy GET removal cannot delete the first entry.' );
	$api->$method( new WP_REST_Request( array( 'post_id' => 9999 ), 'DELETE' ) );
	wishlist_expect( ! $deleted && 4 === count( $user_meta[17] ), 'An unmatched DELETE leaves all entries intact.' );
	$api->$method( new WP_REST_Request( array( 'post_id' => 100 ), 'DELETE' ) );
	wishlist_expect( 3 === count( $user_meta[17] ) && 101 === $user_meta[17][0]['post_id'], 'DELETE correctly removes index zero only.' );
	wishlist_expect( 1 === count( $user_meta[29] ), 'Other users wishlist metadata is unchanged.' );
	$html = $wishlist->tf_generate_table( array_keys( $posts ) );
	wishlist_expect( false === strpos( $html, 'Restricted fixture' ) && false !== strpos( $html, 'Public fixture' ), 'Guest table excludes restricted titles and links.' );
	if ( method_exists( $wishlist, 'get_eligible_post' ) ) {
		foreach ( array( 100, 101, 102, 103 ) as $id ) {
			$actor = 17; $user_meta[17] = array();
			$response = wishlist_ajax( array( $wishlist, 'tf_add_to_wishlists' ), array( 'nonce' => 'fixture-nonce', 'post' => $id, 'type' => get_post_type( $id ) ) );
			wishlist_expect( $response && $response->response['success'] && 1 === count( $user_meta[17] ), 'Eligible public listing is saved.' );
		}
		foreach ( array_merge( range( 200, 207 ), array( 999, -100, '100.1', '1e2', '0100', true, array( 100 ), str_repeat( '9', 40 ) ) ) as $id ) {
			$user_meta[17] = array();
			$response = wishlist_ajax( array( $wishlist, 'tf_add_to_wishlists' ), array( 'nonce' => 'fixture-nonce', 'post' => $id, 'type' => 'tf_hotel' ) );
			wishlist_expect( $response && ! $response->response['success'] && ! $user_meta[17], 'Invalid or restricted add is rejected without writing.' );
		}
		$user_meta[17] = array( array( 'post_id' => 100, 'post_type' => 'tf_hotel' ), array( 'post_id' => 100, 'post_type' => 'tf_hotel' ), array( 'broken' => 'value' ) );
		$result = $api->$method( new WP_REST_Request() );
		wishlist_expect( 1 === count( $result ), 'Duplicate and malformed historical entries are handled safely.' );
		$posts[100]->post_status = 'private';
		wishlist_expect( array() === $api->$method( new WP_REST_Request() ), 'Visibility changes are enforced on every read.' );
		$posts[100]->post_status = 'publish';
		$user_meta[17] = array( 8 => array( 'post_id' => 100, 'post_type' => 'tf_hotel' ), 15 => array( 'post_id' => 101, 'post_type' => 'tf_tours' ) );
		wishlist_ajax( array( $wishlist, 'tf_remove_wishlist' ), array(), array( 'nonce' => 'fixture-nonce', 'id' => 9999, 'type' => '' ) );
		wishlist_expect( 2 === count( $user_meta[17] ), 'AJAX nonexistent removal preserves sparse-key historical records.' );
		wishlist_ajax( array( $wishlist, 'tf_remove_wishlist' ), array(), array( 'nonce' => 'fixture-nonce', 'id' => 100, 'type' => '' ) );
		wishlist_expect( 1 === count( $user_meta[17] ) && 101 === array_values( $user_meta[17] )[0]['post_id'], 'AJAX resolves original stored rows without array-column index assumptions.' );
		foreach ( array( -1, '1.2', array( 100 ), true ) as $id ) {
			$result = $api->$method( new WP_REST_Request( array( 'post_id' => $id ), 'DELETE' ) );
			wishlist_expect( is_wp_error( $result ) && 400 === $result->data['status'], 'Malformed REST removal input is rejected.' );
		}
		$result = $api->$method( new WP_REST_Request( array( 'remove' => 'unexpected' ) ) );
		wishlist_expect( is_wp_error( $result ) && 400 === $result->data['status'], 'Unsupported operation value is rejected.' );
		$actor = 0;
		$response = wishlist_ajax( array( $wishlist, 'tf_generate_table_guest' ), array( 'nonce' => 'fixture-nonce', 'ids' => array( 100, 200, 205, 207 ) ) );
		wishlist_expect( $response->response['success'] && false === strpos( $response->response['data'], 'Restricted fixture' ), 'Guest AJAX uses the same public eligibility policy.' );
	} else { wishlist_expect( false, 'Central eligibility helper exists.' ); }
	foreach ( $failures as $failure ) { fwrite( STDERR, 'FAIL: ' . $failure . "\n" ); }
	echo ( $failures ? 'FAIL' : 'PASS' ) . ': ' . $checks . ' ' . ( $pro ? 'Pro' : 'Free' ) . " wishlist security assertions.\n";
	exit( $failures ? 1 : 0 );
}
