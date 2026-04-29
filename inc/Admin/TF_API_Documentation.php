<?php
namespace Tourfic\Admin;

defined( 'ABSPATH' ) || exit;

class TF_API_Documentation {
	use \Tourfic\Traits\Singleton;
	use TF_API_Documentation_Examples;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ), 220 );
	}

	public function register_menu() {
		add_submenu_page(
			'tf_settings',
			esc_html__( 'API Documentation', 'tourfic' ),
			esc_html__( 'API Documentation', 'tourfic' ),
			'manage_options',
			'tf_api_docs',
			array( $this, 'render_page' ),
			function_exists( 'is_tf_pro' ) && is_tf_pro() ? 6 : 5
		);
	}

	public function render_page() {
		?>
		<div class="wrap tf-api-documentation">
			<h1><?php esc_html_e( 'Tourfic REST API Documentation', 'tourfic' ); ?></h1>

			<nav class="tf-api-docs-nav" aria-label="<?php esc_attr_e( 'API sections', 'tourfic' ); ?>">
				<a href="#tf-api-key-manager" class="tf-api-docs-nav__item"><?php esc_html_e( 'API Keys', 'tourfic' ); ?></a>
				<a href="#tf-section-general" class="tf-api-docs-nav__item"><?php esc_html_e( 'General', 'tourfic' ); ?></a>
				<a href="#tf-section-hotel-management" class="tf-api-docs-nav__item"><?php esc_html_e( 'Hotel', 'tourfic' ); ?></a>
				<a href="#tf-section-room-management" class="tf-api-docs-nav__item"><?php esc_html_e( 'Room', 'tourfic' ); ?></a>
				<a href="#tf-section-tour-management" class="tf-api-docs-nav__item"><?php esc_html_e( 'Tour', 'tourfic' ); ?></a>
				<a href="#tf-section-apartment-management" class="tf-api-docs-nav__item"><?php esc_html_e( 'Apartment', 'tourfic' ); ?></a>
				<a href="#tf-section-car-rental-management" class="tf-api-docs-nav__item"><?php esc_html_e( 'Car Rental', 'tourfic' ); ?></a>
				<a href="#tf-section-taxonomy-management" class="tf-api-docs-nav__item"><?php esc_html_e( 'Taxonomy', 'tourfic' ); ?></a>
				<a href="#tf-section-booking-management" class="tf-api-docs-nav__item"><?php esc_html_e( 'Booking', 'tourfic' ); ?></a>
				<a href="#tf-section-enquiry-management" class="tf-api-docs-nav__item"><?php esc_html_e( 'Enquiry', 'tourfic' ); ?></a>
				<a href="#tf-section-user-management" class="tf-api-docs-nav__item"><?php esc_html_e( 'Users', 'tourfic' ); ?></a>
				<a href="#tf-section-vendor-reports" class="tf-api-docs-nav__item"><?php esc_html_e( 'Vendor &amp; Reports', 'tourfic' ); ?></a>
			</nav>

			<div id="tf-api-key-manager">
			<?php $this->render_api_key_manager(); ?>
			</div>

			<?php $this->render_endpoint_section( esc_html__( 'General', 'tourfic' ), $this->get_general_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Hotel Management', 'tourfic' ), $this->get_hotel_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Room Management', 'tourfic' ), $this->get_room_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Tour Management', 'tourfic' ), $this->get_tour_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Apartment Management', 'tourfic' ), $this->get_apartment_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Car Rental Management', 'tourfic' ), $this->get_car_rental_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Taxonomy Management', 'tourfic' ), $this->get_taxonomy_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Booking Management', 'tourfic' ), $this->get_booking_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Enquiry Management', 'tourfic' ), $this->get_enquiry_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'User Management', 'tourfic' ), $this->get_user_endpoints() ); ?>
			<?php $this->render_endpoint_section( esc_html__( 'Vendor &amp; Reports', 'tourfic' ), $this->get_vendor_endpoints() ); ?>
		</div>
		<?php
	}

	private function render_endpoint_section( $title, $endpoints ) {
		$section_id = 'tf-section-' . sanitize_title( $title );
		?>
		<div id="<?php echo esc_attr( $section_id ); ?>" class="tf-api-section tf-api-section-collapsible is-expanded">
			<div class="tf-api-section-header">
				<h2><?php echo esc_html( $title ); ?></h2>
				<button type="button" class="tf-api-section-toggle" aria-expanded="true" aria-label="<?php echo esc_attr__( 'Collapse endpoint group', 'tourfic' ); ?>"></button>
			</div>
			<div class="tf-api-section-content">
				<div class="tf-api-endpoints">
				<?php foreach ( $endpoints as $endpoint ) : ?>
					<?php $full_url = rest_url( 'tf/v1' ) . $endpoint['url']; ?>
					<?php $show_pro_badge = !function_exists( 'is_tf_pro' ) && 'GET' !== strtoupper( $endpoint['method'] ); ?>
					<div class="tf-api-endpoint-card">
						<?php if ( $show_pro_badge ) : ?>
							<span class="tf-api-pro-badge"><?php esc_html_e( 'PRO', 'tourfic' ); ?></span>
						<?php endif; ?>
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
												<td data-label="<?php esc_attr_e( 'Parameter', 'tourfic' ); ?>"><code><?php echo esc_html( $parameter['name'] ); ?></code></td>
												<td data-label="<?php esc_attr_e( 'Type', 'tourfic' ); ?>"><?php echo esc_html( $parameter['type'] ); ?></td>
												<td data-label="<?php esc_attr_e( 'Required', 'tourfic' ); ?>"><?php echo ! empty( $parameter['required'] ) ? esc_html__( 'Yes', 'tourfic' ) : esc_html__( 'No', 'tourfic' ); ?></td>
												<td data-label="<?php esc_attr_e( 'Description', 'tourfic' ); ?>"><?php echo esc_html( $parameter['description'] ); ?></td>
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
		$is_write_allowed = function_exists( 'is_tf_pro' ) && is_tf_pro();
		?>
		<div class="tf-api-section tf-api-key-manager">
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
										<label>
											<input type="checkbox" name="permissions[]" value="write" <?php checked( $is_write_allowed ); ?> <?php disabled( ! $is_write_allowed ); ?>>
											<?php esc_html_e( 'Write', 'tourfic' ); ?>
										</label>
										<?php if ( ! $is_write_allowed ) : ?>
											<p class="description"><?php esc_html_e( 'Write permission is available in Tourfic PRO only.', 'tourfic' ); ?></p>
										<?php endif; ?>
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

	private function get_general_endpoints() {
		return array(
			array(
				'method'      => 'GET',
				'url'         => '/tf-settings',
				'description' => __( 'Retrieve the full Tourfic plugin settings. These settings are global and shared across all users; the response is not user-specific.', 'tourfic' ),
				'parameters'  => array(),
				'example_request'  => 'GET /wp-json/tf/v1/tf-settings' . "\n" . 'X-API-Key: your-api-key',
				'example_response' => $this->get_general_settings_example_response(),
			),
			array(
				'method'      => 'POST',
				'url'         => '/tf-settings',
				'description' => __( 'Update Tourfic plugin settings. Merges the supplied key-value pairs into the existing settings. Requires administrator or tf_manager role. Available in Tourfic PRO.', 'tourfic' ),
				'parameters'  => array(
					array(
						'name'        => '(any setting key)',
						'type'        => 'mixed',
						'required'    => true,
						'description' => __( 'One or more tf_settings keys with their new values. Send as a JSON object in the request body.', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/tf-settings' . "\n" . 'X-API-Key: your-api-key' . "\n" . 'Content-Type: application/json' . "\n\n" . $this->get_general_settings_example_response(),
				'example_response' => '{' . "\n" . '  "success": true,' . "\n" . '  "message": "Settings updated successfully.",' . "\n" . '  "settings": ' . $this->get_general_settings_example_response() . "\n" . '}',
			),
		);
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
				'example_request'  => 'GET /wp-json/tf/v1/hotels?page=1&per_page=10&user=1\nX-API-Key: your-api-key',
				'example_response' => $this->get_hotels_example_response(),
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
				'example_request'  => 'POST /wp-json/tf/v1/add-hotel\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "title": "Boutique City Hotel Gallo",\n    "content": "Hotel description content.",\n    "featured_media": 43,\n    "hotelLocations": [12],\n    "hotelFeatures": [5, 8],\n    "hotelTypes": [3],\n    "tf_hotels_opt": {\n        "map": {\n            "address": "",\n            "latitude": "",\n            "longitude": "",\n            "zoom": 5\n        },\n        "gallery": "43,44,45",\n        "featured": "0",\n        "featured_text": "Hot Deal",\n        "is_taxable": "0",\n        "taxable_class": "standard",\n        "tf_single_hotel_layout_opt": "global",\n        "tf_single_hotel_template": "design-1",\n        "video": "",\n        "airport_service": "0",\n        "airport_service_type": [],\n        "airport_pickup_price": {\n            "airport_pickup_price_type": "per_person",\n            "airport_service_fee_adult": "",\n            "airport_service_fee_children": "",\n            "airport_service_fee_fixed": ""\n        },\n        "airport_dropoff_price": {\n            "airport_pickup_price_type": "per_person",\n            "airport_service_fee_adult": "",\n            "airport_service_fee_children": "",\n            "airport_service_fee_fixed": ""\n        },\n        "airport_pickup_dropoff_price": {\n            "airport_pickup_price_type": "per_person",\n            "airport_service_fee_adult": "",\n            "airport_service_fee_children": "",\n            "airport_service_fee_fixed": ""\n        },\n        "room-section-title": "Available Rooms",\n        "room": [],\n        "faq-section-title": "Faq’s",\n        "faq": [],\n        "tc-section-title": "Hotel Terms & Conditions",\n        "tc": "",\n        "h-review": "0",\n        "h-share": "0",\n        "popular-section-title": "Popular Features",\n        "review-section-title": "Average Guest Reviews",\n        "h-enquiry-section": "0",\n        "h-enquiry-option-title": "Have a question in mind",\n        "h-enquiry-option-content": "Looking for more info? Send a question to the property to find out more.",\n        "h-enquiry-option-btn": "Ask a Question"\n    }\n}',
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
					array(
						'name'        => 'featured_media',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Updated attachment ID for featured image.', 'tourfic' ),
					),
					array(
						'name'        => 'hotelLocations',
						'type'        => 'array',
						'required'    => false,
						'description' => __( 'Updated hotel location term IDs.', 'tourfic' ),
					),
					array(
						'name'        => 'hotelFeatures',
						'type'        => 'array',
						'required'    => false,
						'description' => __( 'Updated hotel feature term IDs.', 'tourfic' ),
					),
					array(
						'name'        => 'hotelTypes',
						'type'        => 'array',
						'required'    => false,
						'description' => __( 'Updated hotel type term IDs.', 'tourfic' ),
					),
					array(
						'name'        => 'tf_hotels_opt',
						'type'        => 'object',
						'required'    => false,
						'description' => __( 'Updated hotel settings payload stored in post meta.', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-hotel\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "id": 321,\n    "title": "Boutique City Hotel Gallo Deluxe",\n    "content": "Updated hotel description content.",\n    "featured_media": 43,\n    "hotelLocations": [12],\n    "hotelFeatures": [5, 8],\n    "hotelTypes": [3],\n    "tf_hotels_opt": {\n        "map": {\n            "address": "",\n            "latitude": "",\n            "longitude": "",\n            "zoom": 5\n        },\n        "gallery": "43,44,45",\n        "featured": "0",\n        "featured_text": "Hot Deal",\n        "is_taxable": "0",\n        "taxable_class": "standard",\n        "tf_single_hotel_layout_opt": "global",\n        "tf_single_hotel_template": "design-1",\n        "video": "",\n        "airport_service": "0",\n        "airport_service_type": [],\n        "airport_pickup_price": {\n            "airport_pickup_price_type": "per_person",\n            "airport_service_fee_adult": "",\n            "airport_service_fee_children": "",\n            "airport_service_fee_fixed": ""\n        },\n        "airport_dropoff_price": {\n            "airport_pickup_price_type": "per_person",\n            "airport_service_fee_adult": "",\n            "airport_service_fee_children": "",\n            "airport_service_fee_fixed": ""\n        },\n        "airport_pickup_dropoff_price": {\n            "airport_pickup_price_type": "per_person",\n            "airport_service_fee_adult": "",\n            "airport_service_fee_children": "",\n            "airport_service_fee_fixed": ""\n        },\n        "room-section-title": "Available Rooms",\n        "room": [],\n        "faq-section-title": "Faq’s",\n        "faq": [],\n        "tc-section-title": "Hotel Terms & Conditions",\n        "tc": "",\n        "h-review": "0",\n        "h-share": "0",\n        "popular-section-title": "Popular Features",\n        "review-section-title": "Average Guest Reviews",\n        "h-enquiry-section": "0",\n        "h-enquiry-option-title": "Have a question in mind",\n        "h-enquiry-option-content": "Looking for more info? Send a question to the property to find out more.",\n        "h-enquiry-option-btn": "Ask a Question"\n    }\n}',
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
				'example_response' => $this->get_hotel_room_availability_example_response(),
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
						'name'        => 'price',
						'type'        => 'string',
						'required'    => false,
						'description' => __( 'Base room price when price_by is set to 1.', 'tourfic' ),
					),
					array(
						'name'        => 'adult_price',
						'type'        => 'string',
						'required'    => false,
						'description' => __( 'Adult price when price_by uses per-person pricing.', 'tourfic' ),
					),
					array(
						'name'        => 'child_price',
						'type'        => 'string',
						'required'    => false,
						'description' => __( 'Child price when price_by uses per-person pricing.', 'tourfic' ),
					),
					array(
						'name'        => 'status',
						'type'        => 'string',
						'required'    => true,
						'description' => __( 'Availability status (available/unavailable).', 'tourfic' ),
					),
					array(
						'name'        => 'avail_date',
						'type'        => 'string|array',
						'required'    => false,
						'description' => __( 'Existing availability payload used when updating unsaved room data.', 'tourfic' ),
					),
					array(
						'name'        => 'options_count',
						'type'        => 'integer',
						'required'    => false,
						'description' => __( 'Number of pricing options when price_by is set to option-based pricing.', 'tourfic' ),
					),
				),
				'example_request'  => 'POST /wp-json/tf/v1/hotel-room-availability\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "id": 555,\n    "price_by": "1",\n    "check_in": "2026-05-01",\n    "check_out": "2026-05-05",\n    "price": "120",\n    "adult_price": "",\n    "child_price": "",\n    "status": "available",\n    "avail_date": "",\n    "options_count": 0\n}',
				'example_response' => $this->get_hotel_room_availability_update_example_response(),
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
				'example_response' => '{\n    "status": true,\n    "message": "Availability reset successfully.",\n    "avail_date": [],\n    "avail_date_encoded": "[]"\n}',
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
				'example_response' => '{\n    "rooms": [\n        {\n            "id": 82,\n            "permalink": "https://example.com/rooms/premium-deluxe-twin/",\n            "title": "Premium Deluxe Twin",\n            "content": "Room description content.",\n            "status": "publish",\n            "author": "admin",\n            "date": "April 27, 2026",\n            "featured_image": "https://example.com/uploads/room-featured.jpg",\n            "tf_room_opt": {\n                "hotel-room-heading": "",\n                "tf_single_room_layout_opt": "global",\n                "tf_single_room_template": "design-1",\n                "tf_hotel": "68",\n                "unique_id": "1703067301412",\n                "order_id": "",\n                "enable": "1",\n                "gallery": "46,46,46,46,46",\n                "Details": "",\n                "bed": "2",\n                "adult": "2",\n                "child": "1",\n                "children_age_limit": "",\n                "footage": "250 sqr",\n                "features": [\n                    "24",\n                    "28",\n                    "35",\n                    "36",\n                    "37",\n                    "38"\n                ],\n                "house_rules_heading": "",\n                "house_rules_title": "House Rules",\n                "house_rules": "",\n                "minimum_maximum_stay_requirements": "",\n                "minimum_stay_requirement": "1",\n                "maximum_stay_requirement": "10",\n                "room-cancellation-heading": "",\n                "cancelation-section-title": "Cancelation Policy",\n                "calcellation_policy": "",\n                "Room Pricing": "",\n                "pricing-by": "1",\n                "price": "79",\n                "adult_price": "",\n                "child_price": "",\n                "discount_hotel_type": "none",\n                "discount_hotel_price": "",\n                "price_multi_day": "1",\n                "room-options-heading": "",\n                "room-options": "",\n                "Deposit": "",\n                "allow_deposit": "0",\n                "deposit_type": "none",\n                "deposit_amount": "",\n                "Availability": "",\n                "num-room": "10",\n                "reduce_num_room": "1",\n                "avil_by_date": "1",\n                "avail_date": [],\n                "tf-others-heading": "",\n                "tf-callback": "",\n                "ical": "",\n                "ical_notice": "",\n                "room-settings-docs": "",\n                "disable-room-review": "0",\n                "different-sections": "",\n                "room-feature-section-title": "Amenities",\n                "review-section-title": "Guest Reviews"\n            },\n            "hotel_id": "68",\n            "hotel_title": "Sample Resort Hotel"\n        }\n    ],\n    "total": 12\n}',
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
				'example_request'  => $this->get_add_room_example_request(),
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
				'example_request'  => $this->get_update_room_example_request(),
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
				'example_response' => $this->get_tours_example_response(),
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
				'example_request'  => $this->get_add_tour_example_request(),
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
				'example_request'  => $this->get_update_tour_example_request(),
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
				'url'         => '/tour-availability',
				'description' => __( 'Get tour availability calendar data.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => false, 'description' => __( 'Tour post ID to load availability.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/tour-availability?id=777\nX-API-Key: your-api-key',
				'example_response' => $this->get_tour_availability_example_response(),
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
				'example_request'  => 'GET /wp-json/tf/v1/apartments?page=1&per_page=10&user=1' . "\n" . 'X-API-Key: your-api-key',
				'example_response' => $this->get_apartments_example_response(),
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
				'example_request'  => $this->get_add_apartment_example_request(),
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
				'example_request'  => $this->get_update_apartment_example_request(),
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
				'url'         => '/apartment-availability',
				'description' => __( 'Get apartment availability calendar data.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'apartment_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Apartment post ID to load availability.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/apartment-availability?apartment_id=888\nX-API-Key: your-api-key',
				'example_response' => $this->get_apartment_availability_example_response(),
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
				'example_request'  => 'GET /wp-json/tf/v1/rentals?page=1&per_page=10&author=1' . "\n" . 'X-API-Key: your-api-key',
				'example_response' => $this->get_car_rentals_example_response(),
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
				'example_request'  => $this->get_add_rental_example_request(),
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
				'example_request'  => $this->get_update_rental_example_request(),
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

	private function get_booking_endpoints() {
		return array(
			array(
				'method'      => 'GET',
				'url'         => '/orders',
				'description' => __( 'Get booking orders list with optional filtering and calendar event data.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'user_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Target user ID. Defaults to current user.', 'tourfic' ) ),
					array( 'name' => 'checkinout', 'type' => 'string', 'required' => false, 'description' => __( 'Check-in status filter (in, out, not).', 'tourfic' ) ),
					array( 'name' => 'post_type', 'type' => 'string', 'required' => false, 'description' => __( 'Booking type filter (tf_hotel, tf_tours, tf_apartment, tf_carrental).', 'tourfic' ) ),
					array( 'name' => 'post_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Filter bookings for a specific post ID.', 'tourfic' ) ),
					array( 'name' => 'order_status', 'type' => 'string', 'required' => false, 'description' => __( 'Order status filter.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/orders?post_type=tf_hotel&order_status=completed\nX-API-Key: your-api-key',
				'example_response' => '{\n    "data": [],\n    "events": []\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/order/{id}',
				'description' => __( 'Get booking order details by internal order row ID.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Order row ID from tf_order_data table.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/order/1001\nX-API-Key: your-api-key',
				'example_response' => '{\n    "id": 1001,\n    "order_id": 2345\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-order-status/{id}',
				'description' => __( 'Update order status in Tourfic order table and related WooCommerce order.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Order row ID (path parameter).', 'tourfic' ) ),
					array( 'name' => 'order_status', 'type' => 'string', 'required' => true, 'description' => __( 'New order status (for example: pending, processing, completed, cancelled).', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-order-status/1001\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "order_status": "completed"\n}',
				'example_response' => '{\n    "status": true\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-visitor-details/{id}',
				'description' => __( 'Update visitor details inside order_details payload.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Order row ID (path parameter).', 'tourfic' ) ),
					array( 'name' => 'visitorDetails', 'type' => 'array', 'required' => true, 'description' => __( 'Visitor details array to store in order details.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-visitor-details/1001\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "visitorDetails": [\n        {\n            "name": "John Doe",\n            "age": 32\n        }\n    ]\n}',
				'example_response' => '{\n    "status": true\n}',
			),
		);
	}

	private function get_enquiry_endpoints() {
		return array(
			array(
				'method'      => 'GET',
				'url'         => '/enquiries',
				'description' => __( 'Get enquiry list by user role context with optional filters.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'user_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Target user ID. Defaults to current user.', 'tourfic' ) ),
					array( 'name' => 'post_type', 'type' => 'string', 'required' => true, 'description' => __( 'Post type for enquiries (tf_hotel, tf_tours, tf_apartment, tf_carrental).', 'tourfic' ) ),
					array( 'name' => 'post_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Filter by specific post ID.', 'tourfic' ) ),
					array( 'name' => 'filters', 'type' => 'string', 'required' => false, 'description' => __( 'Status filter (for example: replied, responded, not-replied, not-responded).', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/enquiries?post_type=tf_hotel&filters=not-replied\nX-API-Key: your-api-key',
				'example_response' => '[\n    {\n        "id": 1,\n        "post_id": 321,\n        "post_title": "Hotel Sunrise"\n    }\n]',
			),
			array(
				'method'      => 'GET',
				'url'         => '/enquiries/{id}',
				'description' => __( 'Get single enquiry details by enquiry ID.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Enquiry ID (path parameter).', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/enquiries/1\nX-API-Key: your-api-key',
				'example_response' => '{\n    "id": 1,\n    "formatted_date": "Apr 27, 2026",\n    "formatted_time": "10:20:15 AM"\n}',
			),
		);
	}

	private function get_user_endpoints() {
		return array(
			array(
				'method'      => 'GET',
				'url'         => '/users',
				'description' => __( 'Get list of users. Admin-only. Optionally filter by role.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'roles', 'type' => 'array', 'required' => false, 'description' => __( 'Array of roles to filter users by (for example: tf_vendor, customer).', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/users?roles[]=tf_vendor\nX-API-Key: your-api-key',
				'example_response' => '[\n    {\n        "id": 5,\n        "username": "johndoe",\n        "email": "john@example.com",\n        "roles": ["tf_vendor"]\n    }\n]',
			),
			array(
				'method'      => 'GET',
				'url'         => '/user/{id}',
				'description' => __( 'Get single user details by user ID. Vendors get extra fields including earning and integration data.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'User ID (path parameter).', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/user/5\nX-API-Key: your-api-key',
				'example_response' => '{\n    "id": 5,\n    "username": "johndoe",\n    "email": "john@example.com",\n    "roles": ["tf_vendor"]\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/user',
				'description' => __( 'Create a new user. Admin-only.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'username', 'type' => 'string', 'required' => true, 'description' => __( 'Username for the new account.', 'tourfic' ) ),
					array( 'name' => 'email', 'type' => 'string', 'required' => true, 'description' => __( 'Email address.', 'tourfic' ) ),
					array( 'name' => 'password', 'type' => 'string', 'required' => true, 'description' => __( 'Account password.', 'tourfic' ) ),
					array( 'name' => 'confirm_password', 'type' => 'string', 'required' => true, 'description' => __( 'Must match password.', 'tourfic' ) ),
					array( 'name' => 'role', 'type' => 'string', 'required' => false, 'description' => __( 'WordPress user role (for example: tf_vendor, customer).', 'tourfic' ) ),
					array( 'name' => 'first_name', 'type' => 'string', 'required' => false, 'description' => __( 'First name.', 'tourfic' ) ),
					array( 'name' => 'last_name', 'type' => 'string', 'required' => false, 'description' => __( 'Last name.', 'tourfic' ) ),
					array( 'name' => 'avatar', 'type' => 'integer', 'required' => false, 'description' => __( 'Attachment ID for profile picture.', 'tourfic' ) ),
					array( 'name' => 'cover_pic', 'type' => 'integer', 'required' => false, 'description' => __( 'Attachment ID for cover photo.', 'tourfic' ) ),
					array( 'name' => 'tf_vendor_enabled', 'type' => 'string', 'required' => false, 'description' => __( 'Vendor approval status (1 = enabled, 0 = disabled).', 'tourfic' ) ),
					array( 'name' => 'tf_vendor_posts', 'type' => 'string', 'required' => false, 'description' => __( 'Require post approval (1 = yes, 0 = auto-publish).', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/user\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "username": "newvendor",\n    "email": "vendor@example.com",\n    "password": "secret",\n    "confirm_password": "secret",\n    "role": "tf_vendor"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "User created successfully."\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/user/{id}',
				'description' => __( 'Update user profile, password, and vendor settings.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'User ID (path parameter).', 'tourfic' ) ),
					array( 'name' => 'first_name', 'type' => 'string', 'required' => false, 'description' => __( 'Updated first name.', 'tourfic' ) ),
					array( 'name' => 'last_name', 'type' => 'string', 'required' => false, 'description' => __( 'Updated last name.', 'tourfic' ) ),
					array( 'name' => 'email', 'type' => 'string', 'required' => false, 'description' => __( 'Updated email.', 'tourfic' ) ),
					array( 'name' => 'description', 'type' => 'string', 'required' => false, 'description' => __( 'Updated bio/description.', 'tourfic' ) ),
					array( 'name' => 'profile_edit', 'type' => 'boolean', 'required' => false, 'description' => __( 'Set true to update profile only; omit or false to update password too.', 'tourfic' ) ),
					array( 'name' => 'current_password', 'type' => 'string', 'required' => false, 'description' => __( 'Current password (required with profile_edit for password change).', 'tourfic' ) ),
					array( 'name' => 'new_password', 'type' => 'string', 'required' => false, 'description' => __( 'New password.', 'tourfic' ) ),
					array( 'name' => 'confirm_password', 'type' => 'string', 'required' => false, 'description' => __( 'Confirm new password.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/user/5\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "first_name": "John",\n    "last_name": "Doe",\n    "profile_edit": true\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Profile updated successfully."\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/user-status/{id}',
				'description' => __( 'Update vendor approval status for a user. Admin-only.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'User ID (path parameter).', 'tourfic' ) ),
					array( 'name' => 'user_status', 'type' => 'string', 'required' => true, 'description' => __( 'New vendor status (enabled or disabled).', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/user-status/5\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "user_status": "enabled"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "User status updated successfully."\n}',
			),
			array(
				'method'      => 'DELETE',
				'url'         => '/user/{id}',
				'description' => __( 'Delete a user by ID. Admin-only.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'User ID (path parameter).', 'tourfic' ) ),
				),
				'example_request'  => 'DELETE /wp-json/tf/v1/user/5\nX-API-Key: your-api-key',
				'example_response' => '{\n    "status": true,\n    "message": "User deleted successfully."\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/auth/logout',
				'description' => __( 'Logout the currently authenticated user.', 'tourfic' ),
				'parameters'  => array(),
				'example_request'  => 'POST /wp-json/tf/v1/auth/logout\nX-API-Key: your-api-key',
				'example_response' => '{\n    "success": true,\n    "message": "You are logged out successfully.",\n    "redirect_url": "https://example.com/login"\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/user-bookings',
				'description' => __( 'Get bookings for a customer user.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'user_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Target user ID. Defaults to current user.', 'tourfic' ) ),
					array( 'name' => 'booking_type', 'type' => 'string', 'required' => false, 'description' => __( 'Booking type filter: all, hotel, tour, apartment, car. Defaults to all.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/user-bookings?booking_type=hotel\nX-API-Key: your-api-key',
				'example_response' => '[\n    {\n        "id": 10,\n        "post_type": "tf_hotel",\n        "total_price": "$200.00"\n    }\n]',
			),
			array(
				'method'      => 'GET',
				'url'         => '/user-wishlist',
				'description' => __( 'Get or update the wishlist for a customer user.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'user_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Target user ID. Defaults to current user.', 'tourfic' ) ),
					array( 'name' => 'remove', 'type' => 'boolean', 'required' => false, 'description' => __( 'Set true to remove a post from the wishlist.', 'tourfic' ) ),
					array( 'name' => 'post_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Post ID to remove from wishlist (used with remove=true).', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/user-wishlist\nX-API-Key: your-api-key',
				'example_response' => '[\n    {\n        "ID": 321,\n        "post_title": "Hotel Sunrise",\n        "post_type": "tf_hotel"\n    }\n]',
			),
		);
	}

	private function get_vendor_endpoints() {
		return array(
			array(
				'method'      => 'GET',
				'url'         => '/reports',
				'description' => __( 'Get summary report data for current admin or vendor (total payouts, monthly earnings, commissions).', 'tourfic' ),
				'parameters'  => array(),
				'example_request'  => 'GET /wp-json/tf/v1/reports\nX-API-Key: your-api-key',
				'example_response' => '{\n    "total_payouts": "$500.00",\n    "total_payouts_this_month": "$100.00",\n    "total_order_amount_this_month": "$1200.00",\n    "total_earnings_this_month": "$120.00"\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/vendor-reports',
				'description' => __( 'Get detailed earning reports for a specific vendor.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'user_id', 'type' => 'integer', 'required' => true, 'description' => __( 'Vendor user ID.', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/vendor-reports?user_id=5\nX-API-Key: your-api-key',
				'example_response' => '{\n    "earning": "$800.00",\n    "paid_earning": "$300.00",\n    "unpaid_earning": "$500.00",\n    "avg_earning": "$80.00"\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/commissions',
				'description' => __( 'Get commission history grouped by order. Optionally scoped to a vendor.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'user_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Vendor user ID to filter commissions. Omit for all commissions (admin).', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/commissions?user_id=5\nX-API-Key: your-api-key',
				'example_response' => '[\n    {\n        "order_id": 2001,\n        "admin_commission": "$20.00",\n        "vendor_earning": "$180.00"\n    }\n]',
			),
			array(
				'method'      => 'GET',
				'url'         => '/payouts',
				'description' => __( 'Get list of vendor payout records. Optionally scoped to a vendor.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'user_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Vendor user ID to filter payouts. Omit for all payouts (admin).', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/payouts?user_id=5\nX-API-Key: your-api-key',
				'example_response' => '[\n    {\n        "id": 1,\n        "amount": "$200.00",\n        "payment_status": "completed"\n    }\n]',
			),
			array(
				'method'      => 'POST',
				'url'         => '/add-payout',
				'description' => __( 'Create a new vendor payout record.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'vendor_id', 'type' => 'integer', 'required' => true, 'description' => __( 'Vendor user ID.', 'tourfic' ) ),
					array( 'name' => 'payment_amount', 'type' => 'number', 'required' => true, 'description' => __( 'Payout amount.', 'tourfic' ) ),
					array( 'name' => 'payment_date', 'type' => 'string', 'required' => true, 'description' => __( 'Payment date (YYYY-MM-DD).', 'tourfic' ) ),
					array( 'name' => 'release_date', 'type' => 'string', 'required' => false, 'description' => __( 'Release/settlement date (YYYY-MM-DD).', 'tourfic' ) ),
					array( 'name' => 'payment_method', 'type' => 'string', 'required' => true, 'description' => __( 'Payment method (for example: paypal, bank).', 'tourfic' ) ),
					array( 'name' => 'payment_note', 'type' => 'string', 'required' => false, 'description' => __( 'Optional note for this payout.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/add-payout\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "vendor_id": 5,\n    "payment_amount": 200,\n    "payment_date": "2026-04-27",\n    "payment_method": "paypal"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Payout added successfully"\n}',
			),
			array(
				'method'      => 'GET',
				'url'         => '/payout/{id}',
				'description' => __( 'Get a single payout record by ID.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Payout record ID (path parameter).', 'tourfic' ) ),
				),
				'example_request'  => 'GET /wp-json/tf/v1/payout/1\nX-API-Key: your-api-key',
				'example_response' => '{\n    "id": 1,\n    "vendor_id": 5,\n    "payment_amount": "200",\n    "payment_status": "pending"\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-payout/{id}',
				'description' => __( 'Update an existing payout record.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Payout record ID (path parameter).', 'tourfic' ) ),
					array( 'name' => 'vendor_id', 'type' => 'integer', 'required' => false, 'description' => __( 'Vendor user ID.', 'tourfic' ) ),
					array( 'name' => 'payment_amount', 'type' => 'number', 'required' => false, 'description' => __( 'Updated payout amount.', 'tourfic' ) ),
					array( 'name' => 'payment_date', 'type' => 'string', 'required' => false, 'description' => __( 'Updated payment date (YYYY-MM-DD).', 'tourfic' ) ),
					array( 'name' => 'release_date', 'type' => 'string', 'required' => false, 'description' => __( 'Updated release date (YYYY-MM-DD).', 'tourfic' ) ),
					array( 'name' => 'payment_method', 'type' => 'string', 'required' => false, 'description' => __( 'Updated payment method.', 'tourfic' ) ),
					array( 'name' => 'payment_note', 'type' => 'string', 'required' => false, 'description' => __( 'Updated note.', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-payout/1\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "payment_amount": 250,\n    "payment_method": "bank"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Payout updated successfully"\n}',
			),
			array(
				'method'      => 'POST',
				'url'         => '/update-payout-status/{id}',
				'description' => __( 'Update payout status. Completing a payout deducts the amount from vendor balance.', 'tourfic' ),
				'parameters'  => array(
					array( 'name' => 'id', 'type' => 'integer', 'required' => true, 'description' => __( 'Payout record ID (path parameter).', 'tourfic' ) ),
					array( 'name' => 'payment_status', 'type' => 'string', 'required' => true, 'description' => __( 'New status (pending, completed, cancelled).', 'tourfic' ) ),
				),
				'example_request'  => 'POST /wp-json/tf/v1/update-payout-status/1\nX-API-Key: your-api-key\nContent-Type: application/json\n\n{\n    "payment_status": "completed"\n}',
				'example_response' => '{\n    "status": true,\n    "message": "Payout status updated successfully"\n}',
			),
		);
	}

}