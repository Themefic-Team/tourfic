<?php
namespace Tourfic\Admin;

defined( 'ABSPATH' ) || exit;

class TF_API_Documentation {
	use \Tourfic\Traits\Singleton;

	private $group_order = array(
		'General',
		'Hotels',
		'Rooms',
		'Tours',
		'Apartments',
		'Car Rentals',
		'Taxonomies',
		'Users',
		'Vendors & Finance',
		'Orders & Booking Management',
		'Hotel Backend Booking',
		'Tour Backend Booking',
		'Enquiries',
		'Integrations',
	);

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ), 220 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function register_menu() {
		add_submenu_page(
			'tf_settings',
			esc_html__( 'API Documentation', 'tourfic' ),
			esc_html__( 'API Documentation', 'tourfic' ),
			'manage_options',
			'tf_api_docs',
			array( $this, 'render_page' )
		);
	}

	public function enqueue_assets( $hook ) {
		if ( false === strpos( $hook, 'tf_api_docs' ) ) {
			return;
		}

		wp_enqueue_style(
			'tf-api-documentation',
			TF_ASSETS_ADMIN_URL . 'css/tf-api-documentation.css',
			array(),
			TF_VERSION
		);

		wp_enqueue_script(
			'tf-api-documentation',
			TF_ASSETS_ADMIN_URL . 'js/tf-api-documentation.js',
			array( 'jquery' ),
			TF_VERSION,
			true
		);

		wp_localize_script(
			'tf-api-documentation',
			'tfApiDocs',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'tf_api_nonce' ),
			)
		);
	}

	public function render_page() {
		$base_url = untrailingslashit( rest_url( 'tf/v1' ) );
		$groups   = $this->get_route_groups();
		?>
		<div class="wrap tf-api-documentation">
			<h1><?php esc_html_e( 'Tourfic REST API Documentation', 'tourfic' ); ?></h1>

			<?php $this->render_api_key_manager(); ?>

			<?php if ( empty( $groups ) ) : ?>
				<div class="notice notice-warning inline">
					<p><?php esc_html_e( 'No Tourfic REST routes were found beyond authentication endpoints. If you expect the vendor dashboard API, make sure Tourfic Pro and its frontend dashboard module are active.', 'tourfic' ); ?></p>
				</div>
			<?php else : ?>
				<?php foreach ( $groups as $group_title => $endpoints ) : ?>
					<div class="tf-api-section">
						<h2><?php echo esc_html( $group_title ); ?></h2>
						<div class="tf-api-endpoints">
							<?php foreach ( $endpoints as $endpoint ) : ?>
								<div class="tf-api-endpoint-card">
									<div class="tf-api-endpoint-header">
										<span class="tf-api-method tf-api-method-<?php echo esc_attr( strtolower( $endpoint['method'] ) ); ?>">
											<?php echo esc_html( $endpoint['method'] ); ?>
										</span>
										<code class="tf-api-route"><?php echo esc_html( $endpoint['relative_route'] ); ?></code>
									</div>

									<p class="tf-api-endpoint-description"><?php echo esc_html( $endpoint['description'] ); ?></p>

									<?php if ( ! empty( $endpoint['parameters'] ) ) : ?>
										<div class="tf-api-parameters">
											<h3><?php esc_html_e( 'Parameters', 'tourfic' ); ?></h3>
											<table class="widefat striped tf-api-table">
												<thead>
													<tr>
														<th><?php esc_html_e( 'Name', 'tourfic' ); ?></th>
														<th><?php esc_html_e( 'In', 'tourfic' ); ?></th>
														<th><?php esc_html_e( 'Type', 'tourfic' ); ?></th>
														<th><?php esc_html_e( 'Required', 'tourfic' ); ?></th>
														<th><?php esc_html_e( 'Description', 'tourfic' ); ?></th>
													</tr>
												</thead>
												<tbody>
													<?php foreach ( $endpoint['parameters'] as $parameter ) : ?>
														<tr>
															<td><code><?php echo esc_html( $parameter['name'] ); ?></code></td>
															<td><?php echo esc_html( $parameter['location'] ); ?></td>
															<td><?php echo esc_html( $parameter['type'] ); ?></td>
															<td><?php echo ! empty( $parameter['required'] ) ? esc_html__( 'Yes', 'tourfic' ) : esc_html__( 'No', 'tourfic' ); ?></td>
															<td><?php echo esc_html( $parameter['description'] ); ?></td>
														</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
										</div>
									<?php endif; ?>

									<div class="tf-api-example-grid">
										<div>
											<h3><?php esc_html_e( 'Example Request', 'tourfic' ); ?></h3>
											<pre class="tf-api-code-example"><?php echo esc_html( $endpoint['example_request'] ); ?></pre>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_api_key_manager() {
		?>
		<div class="tf-api-section">
			<h2><?php esc_html_e( 'API Key Management', 'tourfic' ); ?></h2>
			<div class="tf-api-key-manager-grid">
				<div class="tf-api-endpoint-card">
					<h3><?php esc_html_e( 'Generate New API Key', 'tourfic' ); ?></h3>
					<form id="tf-generate-api-key-form">
						<table class="form-table">
							<tbody>
								<tr>
									<th><label for="tf-api-key-name"><?php esc_html_e( 'Key Name', 'tourfic' ); ?></label></th>
									<td><input type="text" id="tf-api-key-name" name="name" class="regular-text" required placeholder="<?php esc_attr_e( 'My Mobile App', 'tourfic' ); ?>"></td>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Permissions', 'tourfic' ); ?></th>
									<td>
										<label><input type="checkbox" name="permissions[]" value="read" checked> <?php esc_html_e( 'Read', 'tourfic' ); ?></label><br>
										<label><input type="checkbox" name="permissions[]" value="write"> <?php esc_html_e( 'Write', 'tourfic' ); ?></label>
									</td>
								</tr>
							</tbody>
						</table>
						<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Generate API Key', 'tourfic' ); ?></button></p>
					</form>
					<div id="tf-api-generated-credentials" style="display:none;"></div>
				</div>

				<div class="tf-api-endpoint-card">
					<h3><?php esc_html_e( 'Existing API Keys', 'tourfic' ); ?></h3>
					<div id="tf-api-keys-container"><p class="description"><?php esc_html_e( 'Loading API keys...', 'tourfic' ); ?></p></div>
				</div>
			</div>
		</div>
		<?php
	}

	private function get_route_groups() {
		$grouped_routes = array();
		$server         = rest_get_server();
		$routes         = $server->get_routes();

		foreach ( $routes as $route => $handlers ) {
			if ( 0 !== strpos( $route, '/tf/v1' ) ) {
				continue;
			}

			foreach ( $handlers as $handler ) {
				if ( empty( $handler['callback'] ) || ! is_array( $handler['callback'] ) ) {
					continue;
				}

				$endpoint = $this->build_endpoint_data( $route, $handler );
				if ( empty( $endpoint ) ) {
					continue;
				}

				$grouped_routes[ $endpoint['group'] ][] = $endpoint;
			}
		}

		foreach ( $grouped_routes as &$endpoints ) {
			usort(
				$endpoints,
				static function( $left, $right ) {
					return strcmp( $left['relative_route'] . $left['method'], $right['relative_route'] . $right['method'] );
				}
			);
		}

		uksort( $grouped_routes, array( $this, 'sort_groups' ) );

		return $grouped_routes;
	}

	private function build_endpoint_data( $route, $handler ) {
		$callback = $handler['callback'];
		if ( ! isset( $callback[0], $callback[1] ) || ! is_object( $callback[0] ) ) {
			return array();
		}

		try {
			$reflection = new \ReflectionMethod( $callback[0], $callback[1] );
		} catch ( \ReflectionException $exception ) {
			return array();
		}

		$methods = $this->normalize_methods( $handler['methods'] );
		if ( empty( $methods ) ) {
			return array();
		}

		$method           = $methods[0];
		$relative_route   = $this->normalize_relative_route( $route );
		$parameters       = $this->extract_parameters( $route, $reflection, $method );
		$description      = $this->get_method_summary( $reflection );
		$example_request  = $this->build_example_request( $method, $relative_route, $parameters );
		$group            = $this->get_group_title( get_class( $callback[0] ) );

		return array(
			'group'            => $group,
			'method'           => $method,
			'relative_route'   => $relative_route,
			'description'      => $description,
			'parameters'       => $parameters,
			'example_request'  => $example_request,
		);
	}

	private function normalize_methods( $methods ) {
		if ( is_array( $methods ) ) {
			$normalized = array();
			foreach ( $methods as $method => $allowed ) {
				if ( $allowed ) {
					$normalized[] = strtoupper( $method );
				}
			}

			return array_values( array_diff( $normalized, array( 'HEAD' ) ) );
		}

		if ( is_string( $methods ) ) {
			return array_map( 'trim', explode( ',', strtoupper( $methods ) ) );
		}

		return array();
	}

	private function normalize_relative_route( $route ) {
		$route = preg_replace( '#^/tf/v1#', '', $route );

		return empty( $route ) ? '/' : $route;
	}

	private function get_method_summary( \ReflectionMethod $reflection ) {
		$doc_comment = $reflection->getDocComment();

		if ( $doc_comment ) {
			$lines = preg_split( '/\R/', $doc_comment );
			foreach ( $lines as $line ) {
				$line = trim( trim( $line ), "/*\t\n\r\0\x0B" );
				if ( '' === $line || 0 === strpos( $line, '@' ) ) {
					continue;
				}

				return $line;
			}
		}

		return ucwords( str_replace( '_', ' ', preg_replace( '/^tf_fd_/', '', $reflection->getName() ) ) );
	}

	private function extract_parameters( $route, \ReflectionMethod $reflection, $http_method ) {
		$parameters = array();

		preg_match_all( '/\(\?P<([^>]+)>[^)]+\)/', $route, $path_matches );
		if ( ! empty( $path_matches[1] ) ) {
			foreach ( array_unique( $path_matches[1] ) as $path_param ) {
				$parameters[ $path_param ] = array(
					'name'     => $path_param,
					'location' => 'path',
					'type'     => $this->guess_parameter_type( $path_param, $route ),
					'required' => true,
					'description' => $this->get_parameter_description( $path_param, $route, $http_method ),
				);
			}
		}

		$file_name = $reflection->getFileName();
		if ( empty( $file_name ) || ! is_readable( $file_name ) ) {
			return array_values( $parameters );
		}

		$file_lines = file( $file_name );
		if ( false === $file_lines ) {
			return array_values( $parameters );
		}

		$source = implode( '', array_slice( $file_lines, $reflection->getStartLine() - 1, $reflection->getEndLine() - $reflection->getStartLine() + 1 ) );

		preg_match_all( '/->get_param\(\s*[\'\"]([^\'\"]+)[\'\"]\s*\)/', $source, $request_param_matches );
		preg_match_all( '/\$request\s*\[\s*[\'\"]([^\'\"]+)[\'\"]\s*\]/', $source, $request_array_matches );

		$method_parameters = array_merge(
			! empty( $request_param_matches[1] ) ? $request_param_matches[1] : array(),
			! empty( $request_array_matches[1] ) ? $request_array_matches[1] : array()
		);

		foreach ( array_unique( $method_parameters ) as $parameter_name ) {
			if ( isset( $parameters[ $parameter_name ] ) ) {
				continue;
			}

			$parameters[ $parameter_name ] = array(
				'name'     => $parameter_name,
				'location' => 'GET' === $http_method ? 'query' : 'body',
				'type'     => $this->guess_parameter_type( $parameter_name, $route ),
				'required' => false,
				'description' => $this->get_parameter_description( $parameter_name, $route, $http_method ),
			);
		}

		return array_values( $parameters );
	}

	private function guess_parameter_type( $parameter_name, $route ) {
		if ( false !== strpos( $route, '(?P<' . $parameter_name . '>\\d+)' ) ) {
			return 'integer';
		}

		$integer_names = array( 'id', 'page', 'per_page', 'user', 'user_id', 'post_id', 'hotel_id', 'room_id', 'tour_id' );
		$array_names   = array( 'roles', 'filters', 'permissions', 'apartmentLocations', 'apartmentFeatures', 'apartmentTypes', 'hotelLocations', 'hotelFeatures', 'hotelTypes', 'carRentalLocations', 'carRentalCategories', 'repeat_days', 'repeat_weeks' );

		if ( in_array( $parameter_name, $integer_names, true ) || preg_match( '/(_id|_count|adults|children|infants|hours)$/', $parameter_name ) ) {
			return 'integer';
		}

		if ( in_array( $parameter_name, $array_names, true ) || preg_match( '/(items|types|dates|extras|packages)$/', $parameter_name ) ) {
			return 'array';
		}

		if ( preg_match( '/(price|amount|total|distance)$/', $parameter_name ) ) {
			return 'number';
		}

		if ( preg_match( '/(date)$/', $parameter_name ) ) {
			return 'date string';
		}

		if ( preg_match( '/(time)$/', $parameter_name ) ) {
			return 'time string';
		}

		return 'string';
	}

	private function build_example_request( $http_method, $relative_route, $parameters ) {
		$path     = preg_replace( '/\(\?P<([^>]+)>[^)]+\)/', '<$1>', $relative_route );
		$url_path = '/wp-json/tf/v1' . $path;

		$query_parameters = array();
		$body_parameters  = array();

		foreach ( $parameters as $parameter ) {
			if ( 'path' === $parameter['location'] ) {
				continue;
			}

			$placeholder = $this->get_example_placeholder( $parameter['type'], $parameter['name'] );
			if ( 'query' === $parameter['location'] ) {
				$query_parameters[] = $parameter['name'] . '=' . $placeholder;
			} else {
				$body_parameters[ $parameter['name'] ] = $placeholder;
			}
		}

		if ( ! empty( $query_parameters ) ) {
			$url_path .= '?' . implode( '&', array_slice( $query_parameters, 0, 4 ) );
		}

		$lines   = array();
		$lines[] = $http_method . ' ' . $url_path;
		$lines[] = 'X-API-Key: your-api-key';

		if ( in_array( $http_method, array( 'POST', 'PUT', 'PATCH', 'DELETE' ), true ) && ! empty( $body_parameters ) ) {
			$lines[] = 'Content-Type: application/json';
			$lines[] = '';
			$lines[] = wp_json_encode( $body_parameters, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		}

		return implode( "\n", $lines );
	}

	private function get_parameter_description( $parameter_name, $route, $http_method ) {
		$known_descriptions = array(
			'id' => 'Unique ID of the target resource.',
			'page' => 'Page number for paginated results.',
			'per_page' => 'Number of items per page.',
			'status' => 'Filter or set the current status.',
			'user' => 'User ID used to scope the request.',
			'user_id' => 'User ID used to scope the request.',
			'post_id' => 'Post ID used to scope the request.',
			'hotel_id' => 'Hotel post ID.',
			'room_id' => 'Room identifier.',
			'tour_id' => 'Tour post ID.',
			'query' => 'Search text used for filtering or autocomplete.',
			'filters' => 'Filter rules applied to the result set.',
			'roles' => 'List of user roles to include.',
			'permissions' => 'Permission list for this API key.',
			'name' => 'Human-readable name for the resource or API key.',
			'api_key' => 'API key used for authentication.',
			'api_secret' => 'API secret paired with the API key.',
			'key_id' => 'ID of the API key record.',
			'checkinout' => 'Check-in/check-out state filter.',
			'order_status' => 'Order status filter.',
			'post_type' => 'Target content type.',
			'from' => 'Start date/time value.',
			'to' => 'End date/time value.',
		);

		if ( isset( $known_descriptions[ $parameter_name ] ) ) {
			return $known_descriptions[ $parameter_name ];
		}

		if ( false !== strpos( $route, '(?P<' . $parameter_name . '>\\d+)' ) ) {
			return 'Numeric path parameter used to identify the resource.';
		}

		if ( preg_match( '/(_date|date)$/', $parameter_name ) ) {
			return 'Date value used by this request.';
		}

		if ( preg_match( '/(_time|time)$/', $parameter_name ) ) {
			return 'Time value used by this request.';
		}

		if ( preg_match( '/(_id|Id)$/', $parameter_name ) ) {
			return 'Identifier used to reference a related record.';
		}

		if ( 'GET' === $http_method ) {
			return 'Optional query parameter used to filter or shape the response.';
		}

		return 'Request field used by this endpoint.';
	}

	private function get_example_placeholder( $type, $parameter_name ) {
		switch ( $type ) {
			case 'integer':
				return '1';
			case 'number':
				return '99.99';
			case 'array':
				return '[' . $parameter_name . ']';
			case 'date string':
				return '2026-04-26';
			case 'time string':
				return '10:30';
			default:
				return '<' . $parameter_name . '>';
		}
	}

	private function get_group_title( $class_name ) {
		$group_map = array(
			'TF_FD_Rest_API'                               => 'General',
			'TF_FD_Hotel_Rest_API'                         => 'Hotels',
			'TF_FD_Room_Rest_API'                          => 'Rooms',
			'TF_FD_Tour_Rest_API'                          => 'Tours',
			'TF_FD_Apartment_Rest_API'                     => 'Apartments',
			'TF_FD_Rental_Rest_API'                        => 'Car Rentals',
			'TF_FD_Taxonomy_Rest_API'                      => 'Taxonomies',
			'TF_FD_User_Rest_API'                          => 'Users',
			'TF_FD_Vendor_Rest_API'                        => 'Vendors & Finance',
			'TF_FD_Booking_Rest_API'                       => 'Orders & Booking Management',
			'TF_FD_Hotel_Backend_Booking_Rest_API'         => 'Hotel Backend Booking',
			'TF_FD_Tour_Backend_Booking_Rest_API'          => 'Tour Backend Booking',
			'TF_FD_Enquiry_Rest_API'                       => 'Enquiries',
			'TF_FD_Integration_Rest_API'                   => 'Integrations',
		);

		$class_name = ltrim( $class_name, '\\' );
		$class_name = false !== strrpos( $class_name, '\\' ) ? substr( $class_name, strrpos( $class_name, '\\' ) + 1 ) : $class_name;

		return isset( $group_map[ $class_name ] ) ? $group_map[ $class_name ] : esc_html__( 'Other', 'tourfic' );
	}

	private function route_uses_session_auth( $relative_route ) {
		return in_array( $relative_route, array( '/auth/generate-key', '/auth/revoke-key', '/auth/keys' ), true );
	}

	private function sort_groups( $left, $right ) {
		$left_index  = array_search( $left, $this->group_order, true );
		$right_index = array_search( $right, $this->group_order, true );

		$left_index  = false === $left_index ? PHP_INT_MAX : $left_index;
		$right_index = false === $right_index ? PHP_INT_MAX : $right_index;

		if ( $left_index === $right_index ) {
			return strcmp( $left, $right );
		}

		return $left_index - $right_index;
	}
}