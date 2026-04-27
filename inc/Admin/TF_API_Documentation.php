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
			)
		);
	}

	public function render_page() {
		?>
		<div class="wrap tf-api-documentation">
			<h1><?php esc_html_e( 'Tourfic REST API Documentation', 'tourfic' ); ?></h1>

			<?php $this->render_api_key_manager(); ?>

			<?php $this->render_endpoint_section( 'Hotel Management', $this->get_hotel_endpoints() ); ?>
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

	private function get_hotel_endpoints() {
		return array(
			array(
				'method'      => 'GET',
				'url'         => '/hotels',
				'description' => 'Get list of hotels for the current user or a target user.',
				'parameters'  => array(
					array(
						'name'        => 'per_page',
						'type'        => 'integer',
						'required'    => false,
						'description' => 'Number of hotels to return (default: 10).',
					),
					array(
						'name'        => 'page',
						'type'        => 'integer',
						'required'    => false,
						'description' => 'Page number for pagination (default: 1).',
					),
					array(
						'name'        => 'user',
						'type'        => 'integer',
						'required'    => false,
						'description' => 'User ID to scope results (admins/managers can view all).',
					),
				),
				'example_request'  => 'GET /wp-json/tf/v1/hotels?page=1&per_page=10\nX-API-Key: your-api-key',
				'example_response' => '{\n    "hotels": [],\n    "total": 0\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/add-hotel',
				'description' => 'Create a new hotel post.',
				'parameters'  => array(
					array(
						'name'        => 'title',
						'type'        => 'string',
						'required'    => true,
						'description' => 'Hotel title.',
					),
					array(
						'name'        => 'content',
						'type'        => 'string',
						'required'    => true,
						'description' => 'Hotel description/content.',
					),
					array(
						'name'        => 'featured_media',
						'type'        => 'integer',
						'required'    => false,
						'description' => 'Attachment ID for featured image.',
					),
					array(
						'name'        => 'hotelLocations',
						'type'        => 'array',
						'required'    => false,
						'description' => 'Hotel location term IDs.',
					),
					array(
						'name'        => 'hotelFeatures',
						'type'        => 'array',
						'required'    => false,
						'description' => 'Hotel feature term IDs.',
					),
					array(
						'name'        => 'hotelTypes',
						'type'        => 'array',
						'required'    => false,
						'description' => 'Hotel type term IDs.',
					),
					array(
						'name'        => 'tf_hotels_opt',
						'type'        => 'object',
						'required'    => false,
						'description' => 'Hotel settings payload stored in post meta.',
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/add-hotel\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "title": "Hotel Sunrise",\n    "content": "Ocean view hotel",\n    "hotelLocations": [12],\n    "hotelFeatures": [5, 8]\n}',
				'example_response' => '{\n    "id": 321\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-hotel',
				'description' => 'Update an existing hotel post.',
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => true,
						'description' => 'Hotel post ID.',
					),
					array(
						'name'        => 'title',
						'type'        => 'string',
						'required'    => true,
						'description' => 'Updated hotel title.',
					),
					array(
						'name'        => 'content',
						'type'        => 'string',
						'required'    => true,
						'description' => 'Updated hotel content.',
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-hotel\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "id": 321,\n    "title": "Hotel Sunrise Deluxe",\n    "content": "Updated description"\n}',
				'example_response' => '{\n    "id": 321\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-hotel-status/{id}',
				'description' => 'Update hotel post status.',
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => true,
						'description' => 'Hotel post ID (path parameter).',
					),
					array(
						'name'        => 'hotel_status',
						'type'        => 'string',
						'required'    => true,
						'description' => 'New status (publish, pending, draft, etc.).',
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-hotel-status/321\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "hotel_status": "publish"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Hotel status updated successfully."\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/hotel-orders',
				'description' => 'Get hotel orders for the current user/vendor.',
				'parameters'  => array(
					array(
						'name'        => 'user_id',
						'type'        => 'integer',
						'required'    => false,
						'description' => 'Optional user ID context for order listing.',
					),
				),
				'example_request'  => 'GET /wp-json/tf/v1/hotel-orders\nX-API-Key: your-api-key',
				'example_response' => '{\n    "orders": [],\n    "total": 0\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/hotel-order/{id}',
				'description' => 'Get a single hotel order details record by ID.',
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => true,
						'description' => 'Order ID from tf_order_data table.',
					),
				),
				'example_request'  => 'GET /wp-json/tf/v1/hotel-order/1001\nX-API-Key: your-api-key',
				'example_response' => '{\n    "id": 1001\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/hotel-room-availability',
				'description' => 'Get room availability calendar data.',
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => false,
						'description' => 'Room ID to fetch availability for.',
					),
				),
				'example_request'  => 'GET /wp-json/tf/v1/hotel-room-availability?id=555\nX-API-Key: your-api-key',
				'example_response' => '[\n    {\n        "check_in": "2026/04/27",\n        "status": "available"\n    }\n]',
			),
			array(
				'method'      => 'POST',
				'url'         => '/hotel-room-availability',
				'description' => 'Create or update room availability range.',
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => true,
						'description' => 'Room ID.',
					),
					array(
						'name'        => 'price_by',
						'type'        => 'string',
						'required'    => true,
						'description' => 'Pricing mode for availability entries.',
					),
					array(
						'name'        => 'check_in',
						'type'        => 'date string',
						'required'    => true,
						'description' => 'Start date.',
					),
					array(
						'name'        => 'check_out',
						'type'        => 'date string',
						'required'    => true,
						'description' => 'End date.',
					),
					array(
						'name'        => 'status',
						'type'        => 'string',
						'required'    => true,
						'description' => 'Availability status (available/unavailable).',
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/hotel-room-availability\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "id": 555,\n    "price_by": "1",\n    "check_in": "2026-05-01",\n    "check_out": "2026-05-05",\n    "price": "120",\n    "status": "available"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Availability updated successfully."\n}',
			),
			array(
				'method'      => 'DELETE',
				'url'         => '/hotel-room-availability/{id}',
				'description' => 'Reset all availability for a room.',
				'parameters'  => array(
					array(
						'name'        => 'id',
						'type'        => 'integer',
						'required'    => true,
						'description' => 'Room ID.',
					),
				),
				'example_request'  => 'DELETE /wp-json/tf/v1/hotel-room-availability/555\nX-API-Key: your-api-key',
				'example_response' => '{\n    "status": true,\n    "message": "Availability reset successfully."\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/hotel-ical-import',
				'description' => 'Import iCal events and mark unavailable dates.',
				'parameters'  => array(
					array(
						'name'        => 'ical_url',
						'type'        => 'string',
						'required'    => true,
						'description' => 'Public iCal URL.',
					),
					array(
						'name'        => 'hotel_id',
						'type'        => 'integer',
						'required'    => true,
						'description' => 'Hotel post ID.',
					),
					array(
						'name'        => 'room_index',
						'type'        => 'integer',
						'required'    => true,
						'description' => 'Index of room in hotel options.',
					),
					array(
						'name'        => 'pricing_by',
						'type'        => 'string',
						'required'    => false,
						'description' => 'Pricing mode to apply to imported blocked dates.',
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/hotel-ical-import\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "ical_url": "https://example.com/calendar.ics",\n    "hotel_id": 321,\n    "room_index": 0,\n    "pricing_by": "1"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "iCal imported successfully."\n}',
			),
		);
	}

}