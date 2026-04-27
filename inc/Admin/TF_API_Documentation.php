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
		</div>
		<?php
	}

	private function render_endpoint_section( $title, $endpoints ) {
		?>
		<div class="tf-api-section">
			<h2><?php echo esc_html( $title ); ?></h2>
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

}