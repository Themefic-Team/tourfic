<?php
namespace Tourfic\Classes;

defined( 'ABSPATH' ) || exit;

class TF_API_Keys {
	use \Tourfic\Traits\Singleton;

	const TABLE_VERSION = '1.0.1';

	private $last_auth_error = null;
	private $headers_present = false;
	private $authenticated_key_record = null;

	public function __construct() {
		add_action( 'init', array( $this, 'maybe_create_tables' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_filter( 'determine_current_user', array( $this, 'authenticate_current_user' ), 30 );
		add_filter( 'rest_authentication_errors', array( $this, 'rest_authentication_errors' ) );
		add_filter( 'rest_request_before_callbacks', array( $this, 'enforce_request_permissions' ), 10, 3 );

		add_action( 'wp_ajax_tf_generate_api_key', array( $this, 'ajax_generate_api_key' ) );
		add_action( 'wp_ajax_tf_revoke_api_key', array( $this, 'ajax_revoke_api_key' ) );
		add_action( 'wp_ajax_tf_get_api_keys', array( $this, 'ajax_get_api_keys' ) );
	}

	public function maybe_create_tables() {
		if ( get_option( 'tf_api_keys_table_version' ) === self::TABLE_VERSION ) {
			return;
		}

		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name      = $this->get_table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT UNSIGNED NOT NULL,
			name VARCHAR(200) NOT NULL,
			api_key_hash CHAR(64) NOT NULL,
			api_key_preview VARCHAR(80) NOT NULL,
			api_secret_hash CHAR(64) NOT NULL,
			permissions TEXT NOT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'active',
			last_used DATETIME NULL,
			expires_at DATETIME NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY api_key_hash (api_key_hash),
			KEY status (status)
		) {$charset_collate};";

		dbDelta( $sql );
		update_option( 'tf_api_keys_table_version', self::TABLE_VERSION, false );
	}

	public function register_rest_routes() {
		register_rest_route(
			'tf/v1',
			'/auth/generate-key',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'generate_api_key_endpoint' ),
				'permission_callback' => array( $this, 'manage_keys_permission_callback' ),
			)
		);

		register_rest_route(
			'tf/v1',
			'/auth/validate-key',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'validate_api_key_endpoint' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'tf/v1',
			'/auth/revoke-key',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'revoke_api_key_endpoint' ),
				'permission_callback' => array( $this, 'manage_keys_permission_callback' ),
			)
		);

		register_rest_route(
			'tf/v1',
			'/auth/keys',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_api_keys_endpoint' ),
				'permission_callback' => array( $this, 'manage_keys_permission_callback' ),
			)
		);
	}

	public function authenticate_current_user( $user_id ) {
		$this->last_auth_error          = null;
		$this->headers_present          = false;
		$this->authenticated_key_record = null;

		if ( ! empty( $user_id ) || ! $this->is_tf_rest_request() ) {
			return $user_id;
		}

		$credentials = $this->get_api_credentials_from_request();
		if ( empty( $credentials['api_key'] ) ) {
			return $user_id;
		}

		$this->headers_present = true;

		$record = $this->get_key_record_by_key( $credentials['api_key'] );
		if ( empty( $record ) ) {
			$this->last_auth_error = new \WP_Error( 'tf_api_auth_invalid_key', esc_html__( 'The API key is invalid.', 'tourfic' ), array( 'status' => 401 ) );
			return $user_id;
		}

		if ( 'active' !== $record->status ) {
			$this->last_auth_error = new \WP_Error( 'tf_api_auth_inactive_key', esc_html__( 'This API key is inactive.', 'tourfic' ), array( 'status' => 403 ) );
			return $user_id;
		}

		if ( ! empty( $record->expires_at ) && strtotime( $record->expires_at ) < current_time( 'timestamp', true ) ) {
			$this->last_auth_error = new \WP_Error( 'tf_api_auth_expired_key', esc_html__( 'This API key has expired.', 'tourfic' ), array( 'status' => 403 ) );
			return $user_id;
		}

		$user = get_user_by( 'id', (int) $record->user_id );
		if ( ! $user ) {
			$this->last_auth_error = new \WP_Error( 'tf_api_auth_invalid_user', esc_html__( 'The API key owner no longer exists.', 'tourfic' ), array( 'status' => 401 ) );
			return $user_id;
		}

		$this->authenticated_key_record = $record;
		$this->touch_key_last_used( (int) $record->id );

		return (int) $record->user_id;
	}

	public function rest_authentication_errors( $result ) {
		if ( ! empty( $result ) ) {
			return $result;
		}

		if ( $this->headers_present && is_wp_error( $this->last_auth_error ) ) {
			return $this->last_auth_error;
		}

		return $result;
	}

	public function enforce_request_permissions( $response, $handler, $request ) {
		if ( ! $request instanceof \WP_REST_Request || empty( $this->authenticated_key_record ) ) {
			return $response;
		}

		$route = $request->get_route();
		if ( 0 !== strpos( $route, '/tf/v1/' ) ) {
			return $response;
		}

		$permissions = $this->decode_permissions( $this->authenticated_key_record->permissions );
		$method      = strtoupper( $request->get_method() );

		if ( in_array( $method, array( 'GET', 'HEAD', 'OPTIONS' ), true ) ) {
			if ( ! in_array( 'read', $permissions, true ) && ! in_array( 'write', $permissions, true ) ) {
				return new \WP_Error( 'tf_api_auth_read_forbidden', esc_html__( 'This API key does not have read permission.', 'tourfic' ), array( 'status' => 403 ) );
			}
		} elseif ( ! in_array( 'write', $permissions, true ) ) {
			return new \WP_Error( 'tf_api_auth_write_forbidden', esc_html__( 'This API key does not have write permission.', 'tourfic' ), array( 'status' => 403 ) );
		}

		return $response;
	}

	public function manage_keys_permission_callback() {
		return current_user_can( 'manage_options' );
	}

	public function generate_api_key_endpoint( $request ) {
		$name        = sanitize_text_field( (string) $request->get_param( 'name' ) );
		$permissions = $this->sanitize_permissions( $request->get_param( 'permissions' ) );

		if ( empty( $name ) ) {
			return new \WP_Error( 'tf_api_key_name_required', esc_html__( 'API key name is required.', 'tourfic' ), array( 'status' => 400 ) );
		}

		return rest_ensure_response( $this->create_api_key( get_current_user_id(), $name, $permissions ) );
	}

	public function validate_api_key_endpoint( $request ) {
		$api_key = sanitize_text_field( (string) $request->get_param( 'api_key' ) );

		if ( empty( $api_key ) ) {
			return new \WP_Error( 'tf_api_key_required', esc_html__( 'API key is required.', 'tourfic' ), array( 'status' => 400 ) );
		}

		$record = $this->get_key_record_by_key( $api_key );
		if ( empty( $record ) ) {
			return rest_ensure_response( array( 'success' => true, 'valid' => false ) );
		}

		return rest_ensure_response(
			array(
				'success' => true,
				'valid'   => 'active' === $record->status,
				'data'    => array(
					'user_id'     => (int) $record->user_id,
					'name'        => $record->name,
					'permissions' => $this->decode_permissions( $record->permissions ),
					'status'      => $record->status,
					'last_used'   => $record->last_used,
					'expires_at'  => $record->expires_at,
				),
			)
		);
	}

	public function revoke_api_key_endpoint( $request ) {
		$key_id = absint( $request->get_param( 'key_id' ) );

		if ( empty( $key_id ) ) {
			return new \WP_Error( 'tf_api_key_id_required', esc_html__( 'API key ID is required.', 'tourfic' ), array( 'status' => 400 ) );
		}

		$this->revoke_api_key( $key_id, get_current_user_id() );

		return rest_ensure_response( array( 'success' => true, 'message' => esc_html__( 'API key revoked successfully.', 'tourfic' ) ) );
	}

	public function get_api_keys_endpoint() {
		return rest_ensure_response( $this->get_api_keys_for_user( get_current_user_id() ) );
	}

	public function ajax_generate_api_key() {
		$this->verify_ajax_request();

		$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$permissions = isset( $_POST['permissions'] ) ? $this->sanitize_permissions( wp_unslash( $_POST['permissions'] ) ) : array( 'read' );

		if ( empty( $name ) ) {
			wp_send_json_error( esc_html__( 'API key name is required.', 'tourfic' ), 400 );
		}

		wp_send_json_success( $this->create_api_key( get_current_user_id(), $name, $permissions ) );
	}

	public function ajax_revoke_api_key() {
		$this->verify_ajax_request();

		$key_id = isset( $_POST['key_id'] ) ? absint( $_POST['key_id'] ) : 0;
		if ( empty( $key_id ) ) {
			wp_send_json_error( esc_html__( 'API key ID is required.', 'tourfic' ), 400 );
		}

		$this->revoke_api_key( $key_id, get_current_user_id() );
		wp_send_json_success( array( 'message' => esc_html__( 'API key revoked successfully.', 'tourfic' ) ) );
	}

	public function ajax_get_api_keys() {
		$this->verify_ajax_request();
		wp_send_json_success( $this->get_api_keys_for_user( get_current_user_id() ) );
	}

	private function create_api_key( $user_id, $name, $permissions ) {
		global $wpdb;

		$user_id     = absint( $user_id );
		$created_at  = current_time( 'mysql', true );
		$api_key     = 'tf_' . strtolower( wp_generate_password( 32, false, false ) );
		$api_secret  = wp_generate_password( 40, false, false );
		$key_preview = $api_key;
		$table_name  = $this->get_table_name();

		$wpdb->insert(
			$table_name,
			array(
				'user_id'         => $user_id,
				'name'            => $name,
				'api_key_hash'    => $this->hash_value( $api_key ),
				'api_key_preview' => $key_preview,
				'api_secret_hash' => $this->hash_value( $api_secret ),
				'permissions'     => wp_json_encode( $permissions ),
				'status'          => 'active',
				'created_at'      => $created_at,
				'updated_at'      => $created_at,
			),
			array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		return array(
			'id'          => (int) $wpdb->insert_id,
			'name'        => $name,
			'api_key'     => $api_key,
			'api_secret'  => $api_secret,
			'permissions' => $permissions,
			'status'      => 'active',
			'created_at'  => $created_at,
		);
	}

	private function revoke_api_key( $key_id, $user_id ) {
		global $wpdb;

		$wpdb->update(
			$this->get_table_name(),
			array( 'status' => 'revoked', 'updated_at' => current_time( 'mysql', true ) ),
			array( 'id' => absint( $key_id ), 'user_id' => absint( $user_id ) ),
			array( '%s', '%s' ),
			array( '%d', '%d' )
		);
	}

	private function get_api_keys_for_user( $user_id ) {
		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, name, api_key_preview, permissions, status, last_used, expires_at, created_at
				 FROM {$this->get_table_name()}
				 WHERE user_id = %d
				 ORDER BY created_at DESC",
				absint( $user_id )
			),
			ARRAY_A
		);

		if ( empty( $results ) ) {
			return array();
		}

		foreach ( $results as &$result ) {
			$result['id']          = (int) $result['id'];
			$result['permissions'] = $this->decode_permissions( $result['permissions'] );
		}

		return $results;
	}

	private function get_key_record_by_key( $api_key ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->get_table_name()} WHERE api_key_hash = %s LIMIT 1",
				$this->hash_value( $api_key )
			)
		);
	}

	private function touch_key_last_used( $key_id ) {
		global $wpdb;

		$wpdb->update(
			$this->get_table_name(),
			array( 'last_used' => current_time( 'mysql', true ), 'updated_at' => current_time( 'mysql', true ) ),
			array( 'id' => absint( $key_id ) ),
			array( '%s', '%s' ),
			array( '%d' )
		);
	}

	private function verify_ajax_request() {
		check_ajax_referer( 'tf_api_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have permission to manage API keys.', 'tourfic' ), 403 );
		}
	}

	private function sanitize_permissions( $permissions ) {
		$permissions = is_array( $permissions ) ? $permissions : array( $permissions );
		$permissions = array_filter( array_map( 'sanitize_key', $permissions ) );
		$permissions = array_values( array_intersect( $permissions, array( 'read', 'write' ) ) );

		if ( ! $this->is_write_permission_available() ) {
			$permissions = array_values( array_diff( $permissions, array( 'write' ) ) );
		}

		if ( empty( $permissions ) ) {
			$permissions = array( 'read' );
		}

		return $permissions;
	}

	private function is_write_permission_available() {
		return function_exists( 'is_tf_pro' ) && is_tf_pro();
	}

	private function decode_permissions( $permissions ) {
		if ( is_array( $permissions ) ) {
			return $permissions;
		}

		$decoded = json_decode( (string) $permissions, true );

		return is_array( $decoded ) ? $decoded : array();
	}

	private function hash_value( $value ) {
		return hash_hmac( 'sha256', (string) $value, wp_salt( 'auth' ) );
	}

	private function get_api_credentials_from_request() {
		$api_key = $this->get_request_header( 'X-API-Key' );

		if ( empty( $api_key ) ) {
			$authorization = $this->get_request_header( 'Authorization' );
			if ( $authorization && 0 === stripos( $authorization, 'Basic ' ) ) {
				$decoded = base64_decode( substr( $authorization, 6 ), true );
				if ( $decoded && false !== strpos( $decoded, ':' ) ) {
					list( $basic_key ) = explode( ':', $decoded, 2 );
					$api_key = $api_key ? $api_key : trim( $basic_key );
				}
			}
		}

		return array(
			'api_key' => sanitize_text_field( (string) $api_key ),
		);
	}

	private function get_request_header( $header_name ) {
		$server_key = 'HTTP_' . strtoupper( str_replace( '-', '_', $header_name ) );
		if ( isset( $_SERVER[ $server_key ] ) ) {
			return wp_unslash( $_SERVER[ $server_key ] );
		}

		if ( 'Authorization' === $header_name && isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
			return wp_unslash( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] );
		}

		return '';
	}

	private function is_tf_rest_request() {
		$rest_route = isset( $_GET['rest_route'] ) ? sanitize_text_field( wp_unslash( $_GET['rest_route'] ) ) : '';
		if ( 0 === strpos( $rest_route, '/tf/v1/' ) ) {
			return true;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';

		return false !== strpos( $request_uri, '/' . rest_get_url_prefix() . '/tf/v1/' );
	}

	private function get_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'tf_api_keys';
	}
}