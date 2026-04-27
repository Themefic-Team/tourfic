<?php
namespace Tourfic\Admin;

defined( 'ABSPATH' ) || exit;

class TF_API_Documentation {
	use \Tourfic\Traits\Singleton;

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
				'i18n'    => array(
					'noApiKeys'           => esc_html__( 'No API keys found for this user.', 'tourfic' ),
					'untitledKey'         => esc_html__( 'Untitled Key', 'tourfic' ),
					'unknown'             => esc_html__( 'unknown', 'tourfic' ),
					'apiKey'              => esc_html__( 'API Key:', 'tourfic' ),
					'permissions'         => esc_html__( 'Permissions:', 'tourfic' ),
					'none'                => esc_html__( 'None', 'tourfic' ),
					'lastUsed'            => esc_html__( 'Last Used:', 'tourfic' ),
					'never'               => esc_html__( 'Never', 'tourfic' ),
					'created'             => esc_html__( 'Created:', 'tourfic' ),
					'unknownDate'         => esc_html__( 'Unknown', 'tourfic' ),
					'revoke'              => esc_html__( 'Revoke', 'tourfic' ),
					'unableGenerateKey'   => esc_html__( 'Unable to generate API key.', 'tourfic' ),
					'confirmRevoke'       => esc_html__( 'Revoke this API key?', 'tourfic' ),
					'unableRevokeKey'     => esc_html__( 'Unable to revoke API key.', 'tourfic' ),
					'copied'              => esc_html__( 'Copied!', 'tourfic' ),
					'copy'                => esc_html__( 'Copy', 'tourfic' ),
					'copyFailed'          => esc_html__( 'Failed to copy URL to clipboard.', 'tourfic' ),
					'expandGroup'         => esc_html__( 'Expand', 'tourfic' ),
					'collapseGroup'       => esc_html__( 'Collapse', 'tourfic' ),
					'expandGroupLabel'    => esc_html__( 'Expand endpoint group', 'tourfic' ),
					'collapseGroupLabel'  => esc_html__( 'Collapse endpoint group', 'tourfic' ),
				),
			)
		);
	}

	public function render_page() {
		?>
		<div class="wrap tf-api-documentation">
			<h1><?php esc_html_e( 'Tourfic REST API Documentation', 'tourfic' ); ?></h1>

			<?php $this->render_api_key_manager(); ?>

			<?php $this->render_endpoint_section( esc_html__( 'Hotel Management', 'tourfic' ), $this->get_hotel_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Room Management', 'tourfic' ), $this->get_room_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Tour Management', 'tourfic' ), $this->get_tour_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Apartment Management', 'tourfic' ), $this->get_apartment_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Car Rental Management', 'tourfic' ), $this->get_car_rental_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Taxonomy Management', 'tourfic' ), $this->get_taxonomy_endpoints() ); ?>
		</div>
		<?php
	}

	private function render_endpoint_section( $title, $endpoints ) {
		?>
		<div class="tf-api-section tf-api-section-collapsible is-expanded">
			<div class="tf-api-section-header">
				<h2><?php echo esc_html( $title ); ?></h2>
				<button type="button" class="tf-api-section-toggle" aria-expanded="true" aria-label="<?php echo esc_attr__( 'Collapse endpoint group', 'tourfic' ); ?>"></button>
			</div>
			<div class="tf-api-section-content">
				<div class="tf-api-endpoints">
				<?php foreach ( $endpoints as $endpoint ) : ?>
					<?php $full_url = rest_url( 'tf/v1' ) . $endpoint['url']; ?>
					<div class="tf-api-endpoint-card">
						<div class="tf-api-endpoint-header">
							<span class="tf-api-method tf-api-method-<?php echo esc_attr( strtolower( $endpoint['method'] ) ); ?>">
								<?php echo esc_html( $endpoint['method'] ); ?>
							</span>
							<code class="tf-api-route"><?php echo esc_html( $endpoint['url'] ); ?></code>
							<button type="button" class="tf-api-copy-btn" data-url="<?php echo esc_attr( $full_url ); ?>"><?php esc_html_e( 'Copy', 'tourfic' ); ?></button>
						</div>

						<p class="tf-api-endpoint-description"><?php echo esc_html( $endpoint['description'] ); ?></p>

						<?php if ( ! empty( $endpoint['parameters'] ) ) : ?>
							<div class="tf-api-parameters">
								<h3><?php esc_html_e( 'Parameters', 'tourfic' ); ?></h3>
								<table class="widefat striped tf-api-table">
									<thead>
										<tr>
											<th><?php esc_html_e( 'Parameter', 'tourfic' ); ?></th>
											<th><?php esc_html_e( 'Type', 'tourfic' ); ?></th>
											<th><?php esc_html_e( 'Required', 'tourfic' ); ?></th>
											<th><?php esc_html_e( 'Description', 'tourfic' ); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ( $endpoint['parameters'] as $parameter ) : ?>
											<tr>
												<td><code><?php echo esc_html( $parameter['name'] ); ?></code></td>
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
								<pre class="tf-api-code-example"><?php echo esc_html( $this->format_example_text( $endpoint['example_request'] ) ); ?></pre>
							</div>
							<?php if ( ! empty( $endpoint['example_response'] ) ) : ?>
								<div>
									<h3><?php esc_html_e( 'Example Response', 'tourfic' ); ?></h3>
									<pre class="tf-api-code-example"><?php echo esc_html( $this->format_example_text( $endpoint['example_response'] ) ); ?></pre>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	private function format_example_text( $text ) {
		return str_replace( array( '\\r\\n', '\\n', '\\r' ), array( "\n", "\n", "\r" ), (string) $text );
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
									<td><input type="text" id="tf-api-key-name" name="name" class="regular-text" required placeholder="<?php esc_attr_e( 'e.g. Application XYZ', 'tourfic' ); ?>"></td>
								</tr>
								<tr>
									<th><?php esc_html_e( 'Permissions', 'tourfic' ); ?></th>
									<td>
										<label><input type="checkbox" name="permissions[]" value="read" checked> <?php esc_html_e( 'Read', 'tourfic' ); ?></label><br>
										<label><input type="checkbox" name="permissions[]" value="write" checked> <?php esc_html_e( 'Write', 'tourfic' ); ?></label>
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

	private function get_hotel_endpoints() {
		return array(
			array(
				'method'      => 'GET',
				'url'         => '/hotels',
				'description' => __( 'Get list of hotels for the current user or a target user.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'per_page',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Number of hotels to return (default: 10).', 'tourfic' ),
					),
					array(
						'name'        => 'page',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Page number for pagination (default: 1).', 'tourfic' ),
					),
					array(
						'name'        => 'user',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'User ID to scope results (admins/managers can view all).', 'tourfic' ),
					),
				),
				'example_request'  => 'GET /wp-json/tf/v1/hotels?page=1&per_page=10\nX-API-Key: your-api-key',
				'example_response' => '{\n    "hotels": [],\n    "total": 0\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/add-hotel',
				'description' => __( 'Create a new hotel post.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'title',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Hotel title.', 'tourfic' ),
					),
					array(
						'name'        => 'content',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Hotel description/content.', 'tourfic' ),
					),
					array(
						'name'        => 'featured_media',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Attachment ID for featured image.', 'tourfic' ),
					),
					array(
						'name'        => 'hotelLocations',
						'type'        => 'array',
						'required'    => false,
						'description' => __( 'Hotel location term IDs.', 'tourfic' ),
					),
					array(
						'name'        => 'hotelFeatures',
						'type'        => 'array',
						'required'    => false,
						'description' => __( 'Hotel feature term IDs.', 'tourfic' ),
					),
					array(
						'name'        => 'hotelTypes',
						'type'        => 'array',
						'required'    => false,
						'description' => __( 'Hotel type term IDs.', 'tourfic' ),
					),
					array(
						'name'        => 'tf_hotels_opt',
						'type'        => 'object',
						'required'    => false,
						'description' => __( 'Hotel settings payload stored in post meta.', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/add-hotel\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "title": "Hotel Sunrise",\n    "content": "Ocean view hotel",\n    "hotelLocations": [12],\n    "hotelFeatures": [5, 8]\n}',
				'example_response' => '{\n    "id": 321\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-hotel',
				'description' => __( 'Update an existing hotel post.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => true,
						'description' => __( 'Hotel post ID.', 'tourfic' ),
					),
					array(
						'name'        => 'title',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Updated hotel title.', 'tourfic' ),
					),
					array(
						'name'        => 'content',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Updated hotel content.', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-hotel\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "id": 321,\n    "title": "Hotel Sunrise Deluxe",\n    "content": "Updated description"\n}',
				'example_response' => '{\n    "id": 321\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-hotel-status/{id}',
				'description' => __( 'Update hotel post status.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => true,
						'description' => __( 'Hotel post ID (path parameter).', 'tourfic' ),
					),
					array(
						'name'        => 'hotel_status',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'New status (publish, pending, draft, etc.).', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-hotel-status/321\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "hotel_status": "publish"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Hotel status updated successfully."\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/hotel-orders',
				'description' => __( 'Get hotel orders for the current user/vendor.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'user_id',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Optional user ID context for order listing.', 'tourfic' ),
					),
				),
				'example_request'  => 'GET /wp-json/tf/v1/hotel-orders\nX-API-Key: your-api-key',
				'example_response' => '{\n    "orders": [],\n    "total": 0\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/hotel-order/{id}',
				'description' => __( 'Get a single hotel order details record by ID.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => true,
						'description' => __( 'Order ID from tf_order_data table.', 'tourfic' ),
					),
				),
				'example_request'  => 'GET /wp-json/tf/v1/hotel-order/1001\nX-API-Key: your-api-key',

			),
			array(
				'method'      => 'GET',
				'url'         => '/hotel-room-availability',
				'description' => __( 'Get room availability calendar data.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Room ID to fetch availability for.', 'tourfic' ),
					),
				),
				'example_request'  => 'GET /wp-json/tf/v1/hotel-room-availability?id=555\nX-API-Key: your-api-key',
				'example_response' => '[\n    {\n        "check_in": "2026/04/27",\n        "status": "available"\n    }\n]',
			),
			array(
				'method'      => 'POST',
				'url'         => '/hotel-room-availability',
				'description' => __( 'Create or update room availability range.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => true,
						'description' => __( 'Room ID.', 'tourfic' ),
					),
					array(
						'name'        => 'price_by',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Pricing mode for availability entries.', 'tourfic' ),
					),
					array(
						'name'        => 'check_in',
						'type'        => 'date string',
						'required'    => true,
						'description' => __( 'Start date.', 'tourfic' ),
					),
					array(
						'name'        => 'check_out',
						'type'        => 'date string',
						'required'    => true,
						'description' => __( 'End date.', 'tourfic' ),
					),
					array(
						'name'        => 'status',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Availability status (available/unavailable).', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/hotel-room-availability\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "id": 555,\n    "price_by": "1",\n    "check_in": "2026-05-01",\n    "check_out": "2026-05-05",\n    "price": "120",\n    "status": "available"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Availability updated successfully."\n}',
			),
			array(
				'method'      => 'DELETE',
				'url'         => '/hotel-room-availability/{id}',
				'description' => __( 'Reset all availability for a room.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => true,
						'description' => __( 'Room ID.', 'tourfic' ),
					),
				),
				'example_request'  => 'DELETE /wp-json/tf/v1/hotel-room-availability/555\nX-API-Key: your-api-key',
				'example_response' => '{\n    "status": true,\n    "message": "Availability reset successfully."\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/hotel-ical-import',
				'description' => __( 'Import iCal events and mark unavailable dates.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'ical_url',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Public iCal URL.', 'tourfic' ),
					),
					array(
						'name'        => 'hotel_id',
						'type'        => 'integer',
						'required'    => true,
						'description' => __( 'Hotel post ID.', 'tourfic' ),
					),
					array(
						'name'        => 'room_index',
						'type'        => 'integer',
						'required'    => true,
						'description' => __( 'Index of room in hotel options.', 'tourfic' ),
					),
					array(
						'name'        => 'pricing_by',
						'type'        => 'string',
						'required'    => false,
						'description' => __( 'Pricing mode to apply to imported blocked dates.', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/hotel-ical-import\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "ical_url": "https://example.com/calendar.ics",\n    "hotel_id": 321,\n    "room_index": 0,\n    "pricing_by": "1"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "iCal imported successfully."\n}',
			),
		);
	}

	private function get_room_endpoints() {
		return array(
			array(
				'method'      => 'GET',
				'url'         => '/hotel-rooms',
				'description' => __( 'Get all rooms belonging to a specific hotel.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'hotel_id',
						'type'        => 'integer',
						'required'    => true,
						'description' => __( 'Hotel ID used to filter room list.', 'tourfic' ),
					),
				),
				'example_request'  => 'GET /wp-json/tf/v1/hotel-rooms?hotel_id=321\nX-API-Key: your-api-key',
				'example_response' => '{\n    "45": {"id": 45, "title": "Deluxe Room"},\n    "46": {"id": 46, "title": "Family Suite"}\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/rooms',
				'description' => __( 'Get paginated room list for current user or target user.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'per_page',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Number of rooms per page (default: 10).', 'tourfic' ),
					),
					array(
						'name'        => 'page',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Page number for pagination.', 'tourfic' ),
					),
					array(
						'name'        => 'user',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'User ID to scope results.', 'tourfic' ),
					),
				),
				'example_request'  => 'GET /wp-json/tf/v1/rooms?page=1&per_page=10\nX-API-Key: your-api-key',
				'example_response' => '{\n    "rooms": [],\n    "total": 0\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/add-room',
				'description' => __( 'Create a new room post.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'title',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Room title.', 'tourfic' ),
					),
					array(
						'name'        => 'content',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Room description/content.', 'tourfic' ),
					),
					array(
						'name'        => 'featured_media',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Attachment ID for featured image.', 'tourfic' ),
					),
					array(
						'name'        => 'tf_room_opt',
						'type'        => 'object',
						'required'    => false,
						'description' => __( 'Room options/settings stored in post meta.', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/add-room\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "title": "Deluxe Room",\n    "content": "Sea view room",\n    "tf_room_opt": {"tf_hotel": 321}\n}',
				'example_response' => '{\n    "id": 654\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-room',
				'description' => __( 'Update an existing room post.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => true,
						'description' => __( 'Room post ID.', 'tourfic' ),
					),
					array(
						'name'        => 'title',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Updated room title.', 'tourfic' ),
					),
					array(
						'name'        => 'content',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Updated room content.', 'tourfic' ),
					),
					array(
						'name'        => 'featured_media',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Attachment ID for featured image.', 'tourfic' ),
					),
					array(
						'name'        => 'tf_room_opt',
						'type'        => 'object',
						'required'    => false,
						'description' => __( 'Updated room options/settings payload.', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-room\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "id": 654,\n    "title": "Deluxe Room Updated",\n    "content": "Updated room details"\n}',
				'example_response' => '{\n    "id": 654\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-room-status/{id}',
				'description' => __( 'Update room post status.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => true,
						'description' => __( 'Room post ID (path parameter).', 'tourfic' ),
					),
					array(
						'name'        => 'room_status',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'New room status (publish, pending, draft, etc.).', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-room-status/654\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "room_status": "publish"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Room status updated successfully."\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/room-availability',
				'description' => __( 'Get room availability calendar data.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'room_id',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Room ID to load availability data.', 'tourfic' ),
					),
				),
				'example_request'  => 'GET /wp-json/tf/v1/room-availability?room_id=654\nX-API-Key: your-api-key',
				'example_response' => '[\n    {\n        "check_in": "2026/05/01",\n        "status": "available"\n    }\n]',
			),
			array(
				'method'      => 'POST',
				'url'         => '/room-availability',
				'description' => __( 'Create or update room availability for date range.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'hotel_id',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Hotel ID when updating availability from hotel room set.', 'tourfic' ),
					),
					array(
						'name'        => 'room_index',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Room index inside hotel room configuration.', 'tourfic' ),
					),
					array(
						'name'        => 'price_by',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Pricing mode.', 'tourfic' ),
					),
					array(
						'name'        => 'check_in',
						'type'        => 'date string',
						'required'    => true,
						'description' => __( 'Start date.', 'tourfic' ),
					),
					array(
						'name'        => 'check_out',
						'type'        => 'date string',
						'required'    => true,
						'description' => __( 'End date.', 'tourfic' ),
					),
					array(
						'name'        => 'price',
						'type'        => 'number',
						'required'    => false,
						'description' => __( 'Base room price.', 'tourfic' ),
					),
					array(
						'name'        => 'adult_price',
						'type'        => 'number',
						'required'    => false,
						'description' => __( 'Adult pricing value.', 'tourfic' ),
					),
					array(
						'name'        => 'child_price',
						'type'        => 'number',
						'required'    => false,
						'description' => __( 'Child pricing value.', 'tourfic' ),
					),
					array(
						'name'        => 'status',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Availability status (available/unavailable).', 'tourfic' ),
					),
					array(
						'name'        => 'avail_date',
						'type'        => 'array',
						'required'    => false,
						'description' => __( 'Existing availability data to merge.', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/room-availability\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "hotel_id": 321,\n    "room_index": 0,\n    "price_by": "1",\n    "check_in": "2026-05-01",\n    "check_out": "2026-05-05",\n    "price": "120",\n    "status": "available"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Availability updated successfully."\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/room-ical-import',
				'description' => __( 'Import iCal feed and update room unavailable dates.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'ical_url',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Public iCal URL.', 'tourfic' ),
					),
					array(
						'name'        => 'hotel_id',
						'type'        => 'integer',
						'required'    => true,
						'description' => __( 'Hotel post ID.', 'tourfic' ),
					),
					array(
						'name'        => 'room_index',
						'type'        => 'integer',
						'required'    => true,
						'description' => __( 'Room index in hotel room options.', 'tourfic' ),
					),
					array(
						'name'        => 'pricing_by',
						'type'        => 'string',
						'required'    => false,
						'description' => __( 'Pricing mode for imported unavailable dates.', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/room-ical-import\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "ical_url": "https://example.com/room.ics",\n    "hotel_id": 321,\n    "room_index": 0,\n    "pricing_by": "1"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "iCal imported successfully."\n}',
			),
		);
	}

	private function get_tour_endpoints() {
		return array(
			array(
				'method'      => 'GET',
				'url'         => '/tours',
				'description' => __( 'Get paginated tours for current user or target user.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => 'per_page',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Number of tours per page (default: 10).', 'tourfic' ),
					),
					array(
						'name'        => 'page',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Page number for pagination.', 'tourfic' ),
					),
					array(
						'name'        => 'user',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'User ID to scope results.', 'tourfic' ),
					),
				),
				'example_request'  => 'GET /wp-json/tf/v1/tours?page=1&per_page=10\nX-API-Key: your-api-key',
				'example_response' => '{\n    "tours": [],\n    "total": 0\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/add-tour',
				'description' => __( 'Create a new tour post.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'title', 'type' => 'string', 'required' => true, 'description' => __( 'Tour title.', 'tourfic' ) ),
					array( 'name' => 'content', 'type' => 'string', 'required' => true, 'description' => __( 'Tour content/description.', 'tourfic' ) ),
					array( 'name' => 'featured_media', 'type' => 'integer', 'required' => false, 'description' => __( 'Attachment ID for featured image.', 'tourfic' ) ),
					array( 'name' => 'tourDestination', 'type' => 'array', 'required' => false, 'description' => __( 'Destination term IDs.', 'tourfic' ) ),
					array( 'name' => 'tourAttraction', 'type' => 'array', 'required' => false, 'description' => __( 'Attraction term IDs.', 'tourfic' ) ),
					array( 'name' => 'tourActivities', 'type' => 'array', 'required' => false, 'description' => __( 'Activities term IDs.', 'tourfic' ) ),
					array( 'name' => 'tf_tours_opt', 'type' => 'object', 'required' => false, 'description' => __( 'Tour options/settings payload.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/add-tour\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "title": "City Walking Tour",\n    "content": "Guided city tour",\n    "tourDestination": [10],\n    "tourActivities": [3, 5]\n}',
				'example_response' => '{\n    "id": 777\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-tour',
				'description' => __( 'Update an existing tour post.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Tour post ID.', 'tourfic' ) ),
					array( 'name' => 'title', 'type' => 'string', 'required' => true, 'description' => __( 'Updated tour title.', 'tourfic' ) ),
					array( 'name' => 'content', 'type' => 'string', 'required' => true, 'description' => __( 'Updated tour content.', 'tourfic' ) ),
					array( 'name' => 'featured_media', 'type' => 'integer', 'required' => false, 'description' => __( 'Attachment ID for featured image.', 'tourfic' ) ),
					array( 'name' => 'tourDestination', 'type' => 'array', 'required' => false, 'description' => __( 'Destination term IDs.', 'tourfic' ) ),
					array( 'name' => 'tourAttraction', 'type' => 'array', 'required' => false, 'description' => __( 'Attraction term IDs.', 'tourfic' ) ),
					array( 'name' => 'tourActivities', 'type' => 'array', 'required' => false, 'description' => __( 'Activities term IDs.', 'tourfic' ) ),
					array( 'name' => 'tf_tours_opt', 'type' => 'object', 'required' => false, 'description' => __( 'Updated tour options/settings payload.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-tour\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "id": 777,\n    "title": "City Walking Tour Updated",\n    "content": "Updated details"\n}',
				'example_response' => '{\n    "id": 777\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-tour-status/{id}',
				'description' => __( 'Update tour post status.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Tour post ID (path parameter).', 'tourfic' ) ),
					array( 'name' => 'tour_status', 'type' => 'string', 'required' => true, 'description' => __( 'New tour status.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-tour-status/777\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "tour_status": "publish"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Tour status updated successfully."\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/tour-orders',
				'description' => __( 'Get tour orders for current user or target vendor context.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'user_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Optional user ID context for order listing.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/tour-orders\nX-API-Key: your-api-key',
				'example_response' => '{\n    "orders": [],\n    "total": 0\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/tour-order/{id}',
				'description' => __( 'Get single tour order details by order ID.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Order ID from tf_order_data table.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/tour-order/1001\nX-API-Key: your-api-key',
				'example_response' => '{\n    "id": 1001\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-ticket-status/{id}',
				'description' => __( 'Update voucher/ticket check-in status by ticket ID.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Ticket unique ID (path parameter).', 'tourfic' ) ),
					array( 'name' => 'status', 'type' => 'string', 'required' => true, 'description' => __( 'Ticket status value.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-ticket-status/ABC123\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "status": "checked"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Ticket status updated successfully."\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/tour-availability',
				'description' => __( 'Get tour availability calendar data.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => false, 'description' => __( 'Tour post ID to load availability.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/tour-availability?id=777\nX-API-Key: your-api-key',
				'example_response' => '[\n    {\n        "check_in": "2026/05/01",\n        "status": "available"\n    }\n]',
			),
			array(
				'method'      => 'POST',
				'url'         => '/tour-availability',
				'description' => __( 'Create or update tour availability entries.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Tour post ID.', 'tourfic' ) ),
					array( 'name' => 'check_in', 'type' => 'date string', 'required' => false, 'description' => __( 'Start date.', 'tourfic' ) ),
					array( 'name' => 'check_out', 'type' => 'date string', 'required' => false, 'description' => __( 'End date.', 'tourfic' ) ),
					array( 'name' => 'status', 'type' => 'string', 'required' => true, 'description' => __( 'Availability status.', 'tourfic' ) ),
					array( 'name' => 'pricing_type', 'type' => 'string', 'required' => true, 'description' => __( 'Pricing rule (group/person/package).', 'tourfic' ) ),
					array( 'name' => 'price', 'type' => 'number', 'required' => false, 'description' => __( 'Group/base price.', 'tourfic' ) ),
					array( 'name' => 'adult_price', 'type' => 'number', 'required' => false, 'description' => __( 'Adult price.', 'tourfic' ) ),
					array( 'name' => 'child_price', 'type' => 'number', 'required' => false, 'description' => __( 'Child price.', 'tourfic' ) ),
					array( 'name' => 'infant_price', 'type' => 'number', 'required' => false, 'description' => __( 'Infant price.', 'tourfic' ) ),
					array( 'name' => 'tour_availability', 'type' => 'array', 'required' => false, 'description' => __( 'Existing availability data to merge.', 'tourfic' ) ),
					array( 'name' => 'options_count', 'type' => 'integer', 'required' => false, 'description' => __( 'Package option count.', 'tourfic' ) ),
					array( 'name' => 'min_person', 'type' => 'integer', 'required' => false, 'description' => __( 'Minimum person count.', 'tourfic' ) ),
					array( 'name' => 'max_person', 'type' => 'integer', 'required' => false, 'description' => __( 'Maximum person count.', 'tourfic' ) ),
					array( 'name' => 'max_capacity', 'type' => 'integer', 'required' => false, 'description' => __( 'Maximum capacity.', 'tourfic' ) ),
					array( 'name' => 'allowed_time', 'type' => 'array', 'required' => false, 'description' => __( 'Allowed tour time slots.', 'tourfic' ) ),
					array( 'name' => 'tf_tour_repeat_month', 'type' => 'array', 'required' => false, 'description' => __( 'Months for bulk edit.', 'tourfic' ) ),
					array( 'name' => 'tf_tour_repeat_year', 'type' => 'array', 'required' => false, 'description' => __( 'Years for bulk edit.', 'tourfic' ) ),
					array( 'name' => 'tf_tour_repeat_week', 'type' => 'array', 'required' => false, 'description' => __( 'Weekday selections for bulk edit.', 'tourfic' ) ),
					array( 'name' => 'tf_tour_repeat_day', 'type' => 'array', 'required' => false, 'description' => __( 'Day number selections for bulk edit.', 'tourfic' ) ),
					array( 'name' => 'selected_packages', 'type' => 'array', 'required' => false, 'description' => __( 'Selected package indexes.', 'tourfic' ) ),
					array( 'name' => 'bulk_edit_option', 'type' => 'boolean', 'required' => false, 'description' => __( 'Enable bulk edit mode.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/tour-availability\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "id": 777,\n    "check_in": "2026-06-01",\n    "check_out": "2026-06-03",\n    "status": "available",\n    "pricing_type": "group",\n    "price": "99.00"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Availability updated successfully."\n}',
			),
			array(
				'method'      => 'DELETE',
				'url'         => '/tour-availability/{id}',
				'description' => __( 'Reset all availability data for a tour.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Tour post ID.', 'tourfic' ) ),
				),
				'example_request'  => 'DELETE /wp-json/tf/v1/tour-availability/777\nX-API-Key: your-api-key',
				'example_response' => '{\n    "status": true,\n    "message": "Availability Reset Successfully."\n}',
			),
		);
	}

	private function get_apartment_endpoints() {
		return array(
			array(
				'method'      => 'GET',
				'url'         => '/apartments',
				'description' => __( 'Get paginated apartments for current user or target user.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'per_page', 'type' => 'integer', 'required' => false, 'description' => __( 'Number of apartments per page (default: 10).', 'tourfic' ) ),
					array( 'name' => 'page', 'type' => 'integer', 'required' => false, 'description' => __( 'Page number for pagination.', 'tourfic' ) ),
					array( 'name' => 'user', 'type' => 'integer', 'required' => false, 'description' => __( 'User ID to scope results.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/apartments?page=1&per_page=10\nX-API-Key: your-api-key',
				'example_response' => '{\n    "apartments": [],\n    "total": 0\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/add-apartment',
				'description' => __( 'Create a new apartment post.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'title', 'type' => 'string', 'required' => true, 'description' => __( 'Apartment title.', 'tourfic' ) ),
					array( 'name' => 'content', 'type' => 'string', 'required' => true, 'description' => __( 'Apartment content/description.', 'tourfic' ) ),
					array( 'name' => 'featured_media', 'type' => 'integer', 'required' => false, 'description' => __( 'Attachment ID for featured image.', 'tourfic' ) ),
					array( 'name' => 'apartmentLocations', 'type' => 'array', 'required' => false, 'description' => __( 'Apartment location term IDs.', 'tourfic' ) ),
					array( 'name' => 'apartmentFeatures', 'type' => 'array', 'required' => false, 'description' => __( 'Apartment feature term IDs.', 'tourfic' ) ),
					array( 'name' => 'apartmentTypes', 'type' => 'array', 'required' => false, 'description' => __( 'Apartment type term IDs.', 'tourfic' ) ),
					array( 'name' => 'tf_apartment_opt', 'type' => 'object', 'required' => false, 'description' => __( 'Apartment options/settings payload.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/add-apartment\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "title": "City Apartment",\n    "content": "2 bedroom apartment",\n    "apartmentLocations": [12],\n    "apartmentFeatures": [5, 8]\n}',
				'example_response' => '{\n    "id": 888\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-apartment',
				'description' => __( 'Update an existing apartment post.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Apartment post ID.', 'tourfic' ) ),
					array( 'name' => 'title', 'type' => 'string', 'required' => true, 'description' => __( 'Updated apartment title.', 'tourfic' ) ),
					array( 'name' => 'content', 'type' => 'string', 'required' => true, 'description' => __( 'Updated apartment content.', 'tourfic' ) ),
					array( 'name' => 'featured_media', 'type' => 'integer', 'required' => false, 'description' => __( 'Attachment ID for featured image.', 'tourfic' ) ),
					array( 'name' => 'apartmentLocations', 'type' => 'array', 'required' => false, 'description' => __( 'Apartment location term IDs.', 'tourfic' ) ),
					array( 'name' => 'apartmentFeatures', 'type' => 'array', 'required' => false, 'description' => __( 'Apartment feature term IDs.', 'tourfic' ) ),
					array( 'name' => 'apartmentTypes', 'type' => 'array', 'required' => false, 'description' => __( 'Apartment type term IDs.', 'tourfic' ) ),
					array( 'name' => 'tf_apartment_opt', 'type' => 'object', 'required' => false, 'description' => __( 'Updated apartment options/settings payload.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-apartment\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "id": 888,\n    "title": "City Apartment Updated",\n    "content": "Updated details"\n}',
				'example_response' => '{\n    "id": 888\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-apartment-status/{id}',
				'description' => __( 'Update apartment post status.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Apartment post ID (path parameter).', 'tourfic' ) ),
					array( 'name' => 'apartment_status', 'type' => 'string', 'required' => true, 'description' => __( 'New apartment status.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-apartment-status/888\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "apartment_status": "publish"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Apartment status updated successfully."\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/apartment-orders',
				'description' => __( 'Get apartment orders for current user or target vendor context.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'user_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Optional user ID context for order listing.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/apartment-orders\nX-API-Key: your-api-key',
				'example_response' => '{\n    "orders": [],\n    "total": 0\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/apartment-order/{id}',
				'description' => __( 'Get single apartment order details by order ID.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Order ID from tf_order_data table.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/apartment-order/1001\nX-API-Key: your-api-key',
				'example_response' => '{\n    "id": 1001\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/apartment-availability',
				'description' => __( 'Get apartment availability calendar data.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'apartment_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Apartment post ID to load availability.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/apartment-availability?apartment_id=888\nX-API-Key: your-api-key',
				'example_response' => '[\n    {\n        "check_in": "2026/05/01",\n        "status": "available"\n    }\n]',
			),
			array(
				'method'      => 'POST',
				'url'         => '/apartment-availability',
				'description' => __( 'Create or update apartment availability entries.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'apartment_id', 'type' => 'integer', 'required' => true, 'description' => __( 'Apartment post ID.', 'tourfic' ) ),
					array( 'name' => 'pricing_type', 'type' => 'string', 'required' => true, 'description' => __( 'Pricing type (per_night or per_person).', 'tourfic' ) ),
					array( 'name' => 'check_in', 'type' => 'date string', 'required' => true, 'description' => __( 'Start date.', 'tourfic' ) ),
					array( 'name' => 'check_out', 'type' => 'date string', 'required' => true, 'description' => __( 'End date.', 'tourfic' ) ),
					array( 'name' => 'price', 'type' => 'number', 'required' => false, 'description' => __( 'Per-night price.', 'tourfic' ) ),
					array( 'name' => 'adult_price', 'type' => 'number', 'required' => false, 'description' => __( 'Adult price.', 'tourfic' ) ),
					array( 'name' => 'child_price', 'type' => 'number', 'required' => false, 'description' => __( 'Child price.', 'tourfic' ) ),
					array( 'name' => 'infant_price', 'type' => 'number', 'required' => false, 'description' => __( 'Infant price.', 'tourfic' ) ),
					array( 'name' => 'status', 'type' => 'string', 'required' => true, 'description' => __( 'Availability status (available/unavailable).', 'tourfic' ) ),
					array( 'name' => 'apt_availability', 'type' => 'array', 'required' => false, 'description' => __( 'Existing availability data to merge.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/apartment-availability\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "apartment_id": 888,\n    "pricing_type": "per_night",\n    "check_in": "2026-06-01",\n    "check_out": "2026-06-03",\n    "price": "120",\n    "status": "available"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Availability updated successfully."\n}',
			),
			array(
				'method'      => 'DELETE',
				'url'         => '/apartment-availability/{id}',
				'description' => __( 'Reset all availability data for an apartment.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Apartment post ID.', 'tourfic' ) ),
				),
				'example_request'  => 'DELETE /wp-json/tf/v1/apartment-availability/888\nX-API-Key: your-api-key',
				'example_response' => '{\n    "status": true,\n    "message": "Availability Reset Successfully."\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/apartment-ical-import',
				'description' => __( 'Import apartment iCal feed and update unavailable dates.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'ical_url', 'type' => 'string', 'required' => true, 'description' => __( 'Public iCal URL.', 'tourfic' ) ),
					array( 'name' => 'apartment_id', 'type' => 'integer', 'required' => true, 'description' => __( 'Apartment post ID.', 'tourfic' ) ),
					array( 'name' => 'pricing_type', 'type' => 'string', 'required' => false, 'description' => __( 'Pricing type for imported unavailable dates.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/apartment-ical-import\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "ical_url": "https://example.com/apartment.ics",\n    "apartment_id": 888,\n    "pricing_type": "per_night"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "iCal imported successfully."\n}',
			),
		);
	}

	private function get_car_rental_endpoints() {
		return array(
			array(
				'method'      => 'GET',
				'url'         => '/rentals',
				'description' => __( 'Get paginated car rentals for current user or target author.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'per_page', 'type' => 'integer', 'required' => false, 'description' => __( 'Number of rentals per page (default: 10).', 'tourfic' ) ),
					array( 'name' => 'page', 'type' => 'integer', 'required' => false, 'description' => __( 'Page number for pagination.', 'tourfic' ) ),
					array( 'name' => 'author', 'type' => 'integer', 'required' => false, 'description' => __( 'Author/user ID to scope results.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/rentals?page=1&per_page=10\nX-API-Key: your-api-key',
				'example_response' => '{\n    "rentals": [],\n    "total": 0\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/add-rental',
				'description' => __( 'Create a new car rental post.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'title', 'type' => 'string', 'required' => true, 'description' => __( 'Rental title.', 'tourfic' ) ),
					array( 'name' => 'content', 'type' => 'string', 'required' => true, 'description' => __( 'Rental description/content.', 'tourfic' ) ),
					array( 'name' => 'featured_media', 'type' => 'integer', 'required' => false, 'description' => __( 'Attachment ID for featured image.', 'tourfic' ) ),
					array( 'name' => 'carRentalLocations', 'type' => 'array', 'required' => false, 'description' => __( 'Rental location term IDs.', 'tourfic' ) ),
					array( 'name' => 'carRentalCategories', 'type' => 'array', 'required' => false, 'description' => __( 'Rental category term IDs.', 'tourfic' ) ),
					array( 'name' => 'tf_carrental_opt', 'type' => 'object', 'required' => false, 'description' => __( 'Car rental options/settings payload.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/add-rental\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "title": "SUV Rental",\n    "content": "Comfortable SUV",\n    "carRentalLocations": [9],\n    "carRentalCategories": [3]\n}',
				'example_response' => '{\n    "id": 999\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-rental',
				'description' => __( 'Update an existing car rental post.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Rental post ID.', 'tourfic' ) ),
					array( 'name' => 'title', 'type' => 'string', 'required' => true, 'description' => __( 'Updated rental title.', 'tourfic' ) ),
					array( 'name' => 'content', 'type' => 'string', 'required' => true, 'description' => __( 'Updated rental content.', 'tourfic' ) ),
					array( 'name' => 'featured_media', 'type' => 'integer', 'required' => false, 'description' => __( 'Attachment ID for featured image.', 'tourfic' ) ),
					array( 'name' => 'carRentalLocations', 'type' => 'array', 'required' => false, 'description' => __( 'Rental location term IDs.', 'tourfic' ) ),
					array( 'name' => 'carRentalCategories', 'type' => 'array', 'required' => false, 'description' => __( 'Rental category term IDs.', 'tourfic' ) ),
					array( 'name' => 'tf_carrental_opt', 'type' => 'object', 'required' => false, 'description' => __( 'Updated rental options/settings payload.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-rental\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "id": 999,\n    "title": "SUV Rental Updated",\n    "content": "Updated rental details"\n}',
				'example_response' => '{\n    "id": 999\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-rental-status/{id}',
				'description' => __( 'Update car rental post status.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Rental post ID (path parameter).', 'tourfic' ) ),
					array( 'name' => 'rental_status', 'type' => 'string', 'required' => true, 'description' => __( 'New rental status.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-rental-status/999\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "rental_status": "publish"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Car Rental status updated successfully."\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/rental-enquiries',
				'description' => __( 'Get car rental enquiries for current user role context.', 'tourfic' ),
				'parameters'  => array(),
				'example_request'  => 'GET /wp-json/tf/v1/rental-enquiries\nX-API-Key: your-api-key',
				'example_response' => '[\n    {\n        "id": 1,\n        "post_type": "tf_carrental"\n    }\n]',
			),
			array(
				'method'      => 'GET',
				'url'         => '/rental-orders',
				'description' => __( 'Get car rental orders for current user or vendor context.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'user_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Optional user ID context for order listing.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/rental-orders\nX-API-Key: your-api-key',
				'example_response' => '{\n    "orders": [],\n    "total": 0\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/rental-order/{id}',
				'description' => __( 'Get single car rental order details by order ID.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Order ID from tf_order_data table.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/rental-order/1001\nX-API-Key: your-api-key',
				'example_response' => '{\n    "id": 1001\n}',
			),
		);
	}

	private function get_taxonomy_endpoints() {
		$supported_taxonomies = 'hotel_location, hotel_feature, hotel_type, tour_destination, tour_attraction, tour_activities, tour_features, tour_type, apartment_location, apartment_feature, apartment_type, carrental_location, carrental_brand, carrental_fuel_type, carrental_category, carrental_engine_year';

		return array(
			array(
				'method'      => 'POST',
				'url'         => '/{taxonomy}',
				'description' => __( 'Create a new term for a supported taxonomy.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'taxonomy', 'type' => 'string', 'required' => true, 'description' => sprintf( __( 'Taxonomy route segment. Supported: %s.', 'tourfic' ), $supported_taxonomies ) ),
					array( 'name' => 'name', 'type' => 'string', 'required' => true, 'description' => __( 'Term name.', 'tourfic' ) ),
					array( 'name' => 'slug', 'type' => 'string', 'required' => false, 'description' => __( 'Term slug.', 'tourfic' ) ),
					array( 'name' => 'description', 'type' => 'string', 'required' => false, 'description' => __( 'Term description.', 'tourfic' ) ),
					array( 'name' => 'parent', 'type' => 'integer', 'required' => false, 'description' => __( 'Parent term ID.', 'tourfic' ) ),
					array( 'name' => 'meta', 'type' => 'object', 'required' => false, 'description' => __( 'Key/value term meta payload.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/hotel_location\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "name": "Dhaka",\n    "slug": "dhaka",\n    "description": "Capital city",\n    "meta": {\n        "icon": "map-pin"\n    }\n}',
				'example_response' => '{\n    "status": "success",\n    "message": "Term added successfully!",\n    "term_id": 55,\n    "name": "Dhaka"\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/{taxonomy}/{id}',
				'description' => __( 'Update an existing taxonomy term.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'taxonomy', 'type' => 'string', 'required' => true, 'description' => sprintf( __( 'Taxonomy route segment. Supported: %s.', 'tourfic' ), $supported_taxonomies ) ),
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Term ID (path parameter).', 'tourfic' ) ),
					array( 'name' => 'name', 'type' => 'string', 'required' => false, 'description' => __( 'Updated term name.', 'tourfic' ) ),
					array( 'name' => 'slug', 'type' => 'string', 'required' => false, 'description' => __( 'Updated term slug.', 'tourfic' ) ),
					array( 'name' => 'description', 'type' => 'string', 'required' => false, 'description' => __( 'Updated term description.', 'tourfic' ) ),
					array( 'name' => 'parent', 'type' => 'integer', 'required' => false, 'description' => __( 'Updated parent term ID.', 'tourfic' ) ),
					array( 'name' => 'meta', 'type' => 'object', 'required' => false, 'description' => __( 'Term meta values to update.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/hotel_location/55\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "name": "Dhaka City",\n    "description": "Updated description",\n    "meta": {\n        "icon": "location"\n    }\n}',
				'example_response' => '{\n    "term_id": 55,\n    "name": "Dhaka City",\n    "slug": "dhaka",\n    "taxonomy": "hotel_location"\n}',
			),
			array(
				'method'      => 'DELETE',
				'url'         => '/{taxonomy}/{id}',
				'description' => __( 'Delete a taxonomy term. Admins can delete any term; vendors can delete only terms created by themselves.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'taxonomy', 'type' => 'string', 'required' => true, 'description' => sprintf( __( 'Taxonomy route segment. Supported: %s.', 'tourfic' ), $supported_taxonomies ) ),
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Term ID (path parameter).', 'tourfic' ) ),
				),
				'example_request'  => 'DELETE /wp-json/tf/v1/hotel_location/55\nX-API-Key: your-api-key',
				'example_response' => '{\n    "status": "success",\n    "message": "Dhaka City has been deleted successfully."\n}',
			),
		);
	}

}