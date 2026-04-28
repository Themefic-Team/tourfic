<?php
namespace Tourfic\Admin;

defined( 'ABSPATH' ) || exit;

class TF_API_Documentation {
	use \Tourfic\Traits\Singleton;

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
			array( $this, 'render_page' )
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
		);
	}

	private function get_general_settings_example_response() {
		return wp_json_encode( $this->get_general_settings_example_payload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}

	private function get_general_settings_example_payload() {
		return array(
			'general-notice-heading'           => '',
			'general-option-notice-one'       => '',
			'disable-services'                => '',
			'tf-date-format-for-users'        => 'Y/m/d',
			'tf-week-day-flatpickr'           => '0',
			'tf-quick-checkout'               => '',
			'template_heading'                => '',
			'tf-template'                     => array(
				'single-hotel'                  => 'design-2',
				'single-hotel-layout'           => array(
					array(
						'label'  => 'Description',
						'slug'   => 'description',
						'status' => '1',
					),
				),
				'single-hotel-layout-part-1'    => array(
					array(
						'label'  => 'Description',
						'slug'   => 'description',
						'status' => '1',
					),
				),
				'single-hotel-layout-part-2'    => array(
					array(
						'label'  => 'Facilities',
						'slug'   => 'facilities',
						'status' => '1',
					),
				),
				'hotel-archive'                 => 'design-2',
				'hotel_archive_design_2_bannar' => 'https://example.com/uploads/hotel-archive-2.jpg',
				'hotel_archive_design_3_bannar' => 'https://example.com/uploads/hotel-archive-3.jpg',
				'hotel_archive_view'            => 'list',
				'single-tour'                   => 'design-1',
				'single-tour-layout'            => array(
					array(
						'label'  => 'Gallery',
						'slug'   => 'gallery',
						'status' => '1',
					),
				),
				'single-tour-layout-part-1'     => array(
					array(
						'label'  => 'Description',
						'slug'   => 'description',
						'status' => '1',
					),
				),
				'single-tour-layout-part-2'     => array(
					array(
						'label'  => 'FAQ',
						'slug'   => 'faq',
						'status' => '1',
					),
				),
				'tour-archive'                  => 'design-2',
				'tour_archive_design_2_bannar'  => 'https://example.com/uploads/tour-archive-2.jpg',
				'tour_archive_design_3_bannar'  => 'https://example.com/uploads/tour-archive-3.jpg',
				'tour_archive_view'             => 'list',
				'single-apartment'              => 'default',
				'single-aprtment-layout-part-1' => array(
					array(
						'label'  => 'Description',
						'slug'   => 'description',
						'status' => '1',
					),
				),
				'single-aprtment-layout-part-2' => array(
					array(
						'label'  => 'Review',
						'slug'   => 'review',
						'status' => '1',
					),
				),
				'apartment-archive'             => 'design-1',
				'apartment_archive_design_1_bannar' => 'https://example.com/uploads/apartment-archive-1.jpg',
				'apartment_archive_design_2_bannar' => 'https://example.com/uploads/apartment-archive-2.jpg',
				'apartment_archive_view'        => 'list',
				'single-car'                    => 'design-1',
				'single-car-layout'             => array(
					array(
						'label'  => 'Description',
						'slug'   => 'description',
						'status' => '1',
					),
				),
				'car-archive'                   => 'design-1',
				'car_archive_design_1_bannar'   => 'https://example.com/uploads/car-archive-1.jpg',
				'car_archive_view'              => 'grid',
				'car_archive_driver_min_age'    => '',
				'car_archive_driver_max_age'    => '',
			),
			'container_heading'               => '',
			'tf-container'                    => 'boxed',
			'tf-container-width'              => '1280',
			'signle_tour_heading'             => '',
			'tour-option-notice-one'          => '',
			't-review'                        => '',
			't-share'                         => '',
			't-related'                       => '',
			'rt-title'                        => 'You might also like',
			'rt-description'                  => 'Related tours section description.',
			'rt_display'                      => 'auto',
			'tf-related-tours'                => '',
			't-enquiry-email'                 => '',
			't-auto-draft'                    => '',
			't-show-expire-tour'              => '',
			't-hide-start-price'              => '',
			'tour_archive_price_minimum_settings' => 'all',
			'tour_booking_form_button_text'   => '',
			'tour-option-notice-two'          => '',
			'deposit-title'                   => '',
			'deposit-subtitle'                => 'Partial payment information.',
			'notice_shortcode'                => '',
			'itinerary-builder-setings'       => array(
				'itinerary-field'              => array(
					'1' => array(
						'sleep-mode-title' => '4 days',
						'sleep-mode-icon'  => 'ri-time-line',
					),
				),
				'elevtion_type'                => 'Meter',
				'itinerary-downloader'         => '1',
				'itinerary-downloader-title'   => 'Want to read it later?',
				'itinerary-downloader-desc'    => 'Download this tour PDF brochure.',
				'itinerary-downloader-button'  => 'Download Now',
				'tour_thumbnail_height'        => '',
				'tour_thumbnail_width'         => '',
				'company_logo'                 => '',
				'company_desc'                 => '',
				'company_email'                => '',
				'company_address'              => '',
				'company_phone'                => '',
				'expert_label'                 => '',
				'expert_name'                  => '',
				'expert_email'                 => '',
				'expert_phone'                 => '',
				'expert_logo'                  => '',
			),
			'itinerary_map'                  => '',
			'travel_mode'                    => 'DRIVING',
			'confirmation_fields_heading'    => '',
			'tour-option-notice-four'        => '',
			'book-confirm-field'             => 'serialized string',
			'booking-confirmation-msg'       => 'Booked Successfully',
			'booking_tour_heading'           => '',
			'disable_traveller_info'         => '',
			'custom_fields_heading'          => '',
			'without-payment-field'          => 'serialized string',
			'tour_popup_extras_text'         => 'Popup text for tour extras.',
			'tour_traveler_details_text'     => 'Traveler details privacy notice.',
			'label_off_heading'              => '',
			'hotel-option-notice-one'        => '',
			'h-review'                       => '',
			'h-share'                        => '',
			'feature-filter'                 => '1',
			'h-enquiry-email'                => '',
			'hotel_archive_price_minimum_settings' => 'all',
			'hotel_facilities_cats'          => 'serialized string',
			'hotel_booking_form_button_text' => '',
			'hotel_booking_check_button_text' => '',
			'hotel_room_heading'             => '',
			'hotel-option-notice-two'        => '',
			'enable_child_age_limit'         => '',
			'children_age_limit'             => '',
			'hotel_popup_heading'            => '',
			'hotel-option-notice-three'      => '',
			'hotel_service_popup_title'      => 'Add Service to your Booking.',
			'hotel_service_popup_subtile'    => 'Select the services you want to add to your booking.',
			'hotel_service_popup_action'     => 'Continue to booking',
			'hotel_confirmation_fields_heading' => '',
			'hotel-book-confirm-field'       => 'serialized string',
			'hotel-booking-confirmation-msg' => 'Booked Successfully',
			'booking_hotel_heading'          => '',
			'enable_guest_info'              => '1',
			'hotel_custom_fields_heading'    => '',
			'hotel_guest_info_fields'        => 'serialized string',
			'hotel_guest_details_text'       => 'Guest details privacy notice.',
			'apartment-option-notice'        => '',
			'amenities_cats'                 => array(
				'1' => array(
					'amenities_cat_name' => 'Business Facilities',
					'amenities_cat_icon' => '',
				),
			),
			'disable-apartment-review'       => '',
			'disable-apartment-share'        => '',
			'disable-related-apartment'      => '',
			'apartment_booking_form_button_text' => '',
			'disable-car-share'              => '',
			'car_booking_form_button_text'   => 'Reserve',
			'car-book-confirm-field'         => 'serialized string',
			'car-booking-confirmation-msg'   => '',
			'disable-car-time-slots'         => '',
			'car_time_interval'              => '30',
			'front-dash-heading'             => '',
			'front-dash-notice'              => '',
			'fd_logo'                        => '',
			'fd_logo_minified'               => '',
			'fd_logo_mobile'                 => '',
			'tf_user_permission'             => array(
				'vendor_post_permission'     => array( 'hotel', 'tour', 'apartment', 'car_rental' ),
				'vendor_tax_permission'      => array( 'hotel_location' ),
				'vendor_can_manage'          => array( 'view_hotel_enquiry' ),
				'manager_post_permission'    => array( 'hotel', 'tour', 'apartment', 'car_rental' ),
				'manager_tax_permission'     => array( 'hotel_location' ),
				'manager_can_manage'         => array( 'view_hotels' ),
			),
			'notice'                         => '',
			'search-option-heading'          => '',
			'search-option-notice'           => '',
			'search-result-page'             => 8,
			'posts_per_page'                 => '10',
			'hotel_search_heading'           => '',
			'date_hotel_search'              => '',
			'disable_hotel_child_search'     => '',
			'required_location_hotel_search' => '1',
			'hide_hotel_location_search'     => '',
			'tour_search_heading'            => '',
			'date_tour_search'               => '',
			'disable_child_search'           => '',
			'disable_infant_search'          => '',
			'required_location_tour_search'  => '1',
			'hide_tour_location_search'      => '',
			'apartment_search_heading'       => '',
			'date_apartment_search'          => '',
			'disable_apartment_child_search' => '',
			'disable_apartment_infant_search' => '',
			'car_search_heading'             => '',
			'pick_drop_car_search'           => '1',
			'pick_drop_date_car_search'      => '',
			'log_reg_settings'               => array(
				'login_page'            => '659',
				'login_redirect_type'   => 'page',
				'login_redirect_page'   => '662',
				'login_redirect_url'    => 'https://example.com/tf-dashboard',
				'register_page'         => '660',
				'register_redirect_type'=> 'page',
				'register_redirect_page'=> '659',
				'register_redirect_url' => 'https://example.com/tf-login',
			),
			'map_settings_heading'           => '',
			'mapsettings-official-docs'      => '',
			'google-page-option'             => 'googlemap',
			'tf-googlemapapi'                => 'YOUR_GOOGLE_MAPS_API_KEY',
			'map_template_marker'            => '',
			'map_marker_width'               => '35',
			'map_marker_height'              => '45',
			'wishlist_heading'               => '',
			'wishlistsettings-official-docs' => '',
			'wl-for'                         => array( 'li', 'lo' ),
			'wl-bt-for'                      => array( '1', '2', '3' ),
			'wl-page'                        => '24',
			'review_heading'                 => '',
			'review-settings-official-docs'  => '',
			'r-for'                          => array( 'li' ),
			'r-auto-publish'                 => '',
			'r-base'                         => '5',
			'r-hotel'                        => 'serialized string',
			'r-tour'                         => 'serialized string',
			'r-apartment'                    => 'serialized string',
			'r-car'                          => 'serialized string',
			'tf_delete_old_review_fields_button' => '',
			'tf_delete_old_complete_review_button' => '',
			'optimization_heading'           => '',
			'optimize-settings-official-docs' => '',
			'css_min'                        => '1',
			'js_min'                         => '1',
			'cdn_heading'                    => '',
			'ftpr_cdn'                       => '',
			'fnybx_cdn'                      => '',
			'slick_cdn'                      => '',
			'fa_cdn'                         => '',
			'select2_cdn'                    => '',
			'remix_cdn'                      => '',
			'leaflet_cdn'                    => '',
			'swal_cdn'                       => '',
			'chart_cdn'                      => '',
			'permalink_heading'              => '',
			'permalink_notice'               => '',
			'tour-permalink-setting'         => 'tours',
			'hotel-permalink-setting'        => 'hotels',
			'apartment-permalink-setting'    => 'apartments',
			'car-permalink-setting'          => 'cars',
			'affiliate_heading'              => '',
			'tf-affiliate'                   => '',
			'email_template_settings'        => array(
				'enable_admin_conf_email'             => '1',
				'admin_confirmation_email_template'   => 89,
				'enable_admin_canc_email'             => '1',
				'admin_cancellation_email_template'   => 95,
				'enable_offline_admin_conf_email'     => '1',
				'admin_offline_confirmation_email_template' => 93,
				'enable_vendor_conf_email'            => '1',
				'vendor_confirmation_email_template'  => 90,
				'enable_vendor_canc_email'            => '1',
				'vendor_cancellation_email_template'  => 96,
				'enable_offline_vendor_conf_email'    => '1',
				'vendor_offline_confirmation_email_template' => 94,
				'enable_customer_conf_email'          => '1',
				'customer_confirmation_email_template' => 91,
				'enable_customer_canc_email'          => '1',
				'customer_cancellation_email_template' => 97,
				'enable_offline_customer_conf_email'  => '1',
				'customer_offline_confirmation_email_template' => 92,
			),
			'qr-code-title'                  => '',
			'qrcode-official-docs'           => '',
			'qr_logo'                        => '',
			'qr_background'                  => '',
			'qr-ticket-title'                => '',
			'qr-ticket-prefix'               => '',
			'qr-ticket-content'              => '',
			'qr-ticket-verify'               => '1',
			'integration_heading'            => '',
			'tf-integration'                 => array(
				'hotel-integrate-pabbly-webhook' => '',
				'h-enquiry-pabbly-webhook'      => '',
				'tour-integrate-pabbly-webhook' => '',
				't-enquiry-pabbly-webhook'      => '',
				'apartment-integrate-pabbly-webhook' => '',
				'a-enquiry-pabbly-webhook'      => '',
				'car-integrate-pabbly-webhook'  => '',
				'tf-new-order-pabbly-webhook'   => '',
				'tf-new-customer-pabbly-webhook' => '',
				'hotel-integrate-zapier-webhook' => '',
				'h-enquiry-zapier-webhook'      => '',
				'tour-integrate-zapier-webhook' => '',
				't-enquiry-zapier-webhook'      => '',
				'apartment-integrate-zapier-webhook' => '',
				'a-enquiry-zapier-webhook'      => '',
				'car-integrate-zapier-webhook'  => '',
				'tf-new-order-zapier-webhook'   => '',
				'tf-new-customer-zapier-webhook' => '',
				'tf-google-calendar-client_id'  => '',
				'tf-google-calendar-secret_key' => '',
				'tf-google-calendar-redirect_url' => 'https://example.com/wp-json/tourfic/v1/integration/google-api',
			),
			'cancellation-official-docs'     => '',
			'timezone-title'                 => '',
			'cancellation_time_zone'         => 'Pacific/Midway',
			'hotel-title'                    => '',
			'cancellation_hotel_checkin_time' => '',
			'tour-title'                     => '',
			'cancellation_tour_checkin_time' => '',
			'apartment-title'                => '',
			'cancellation_apartment_checkin_time' => '',
			'export-import-notice-one'       => '',
			'backup'                         => '',
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

	private function get_hotel_room_availability_example_response() {
		return wp_json_encode( $this->get_hotel_room_availability_example_payload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}

	private function get_hotel_room_availability_example_payload() {
		return array(
			array(
				'check_in'    => '2026/05/01',
				'check_out'   => '2026/05/01',
				'price_by'    => '1',
				'price'       => '20',
				'adult_price' => '',
				'child_price' => '',
				'status'      => 'available',
				'editable'    => false,
				'start'       => '2026-05-01',
				'title'       => 'Price: <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&#36;</span>20.00</bdi></span>',
			),
			array(
				'check_in'    => '2026/05/02',
				'check_out'   => '2026/05/02',
				'price_by'    => '1',
				'price'       => '20',
				'adult_price' => '',
				'child_price' => '',
				'status'      => 'available',
				'editable'    => false,
				'start'       => '2026-05-02',
				'title'       => 'Price: <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&#36;</span>20.00</bdi></span>',
			),
		);
	}

	private function get_hotels_example_response() {
		return wp_json_encode( $this->get_hotels_example_payload(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}

	private function get_hotels_example_payload() {
		return array(
			'hotels' => array(
				array(
					'id'             => 52,
					'permalink'      => 'https://example.com/hotels/boutique-city-hotel-gallo/',
					'title'          => 'Boutique City Hotel Gallo',
					'content'        => 'Hotel description content.',
					'status'         => 'publish',
					'author'         => 'admin',
					'hotel_location' => 'Switzerland',
					'hotel_feature'  => 'Bathtub, Breakfast, Swimming Pool, Wifi',
					'hotel_type'     => 'Luxury, Boutique',
					'date'           => 'January 15, 2024',
					'featured_image' => 'https://example.com/uploads/hotel-featured.jpg',
					'tf_hotels_opt'  => array(
						'id'                              => '1099',
						'post_title'                      => 'boutique-city-hotel-gallo',
						'slug'                            => 'boutique-city-hotel-gallo',
						'content'                         => 'Hotel description content.',
						'address'                         => '',
						'[map][address]'                  => 'Example Street, Example City, Country',
						'map'                             => array(
							'address'   => 'Example Street, Example City, Country',
							'latitude'  => '25.701643',
							'longitude' => '32.6422566',
							'zoom'      => '5',
						),
						'[map][latitude]'                 => '25.701643',
						'[map][longitude]'                => '32.6422566',
						'[map][zoom]'                     => '5',
						'gallery'                         => '43,44,45',
						'video'                           => 'https://www.youtube.com/watch?v=example',
						'featured'                        => '',
						'featured_text'                   => 'Hot Deal',
						'tf_single_hotel_layout_opt'      => 'single',
						'tf_single_hotel_template'        => 'design-2',
						'room-section-title'              => 'Available Rooms',
						'room'                            => array(
							array(
								'enable'                   => '1',
								'title'                    => 'VIP Premium Deluxe Twin Room 2 Beds',
								'unique_id'                => '1703066080098',
								'room_preview_img'         => 'https://example.com/uploads/room-preview-1.jpg',
								'num-room'                 => '10',
								'reduce_num_room'          => '1',
								'bed'                      => '2',
								'adult'                    => '2',
								'child'                    => '1',
								'children_age_limit'       => '',
								'footage'                  => '250 sqr',
								'description'              => 'Room description.',
								'minimum_stay_requirement' => '1',
								'maximum_stay_requirement' => '10',
								'pricing-by'               => '1',
								'booking-by'               => '',
								'booking-url'              => '',
								'booking-attribute'        => 0,
								'booking-query'            => '',
								'price'                    => '88',
								'adult_price'              => '',
								'child_price'              => '',
								'discount_hotel_type'      => 'percent',
								'discount_hotel_price'     => '10',
								'price_multi_day'          => '1',
								'allow_deposit'            => '',
								'deposit_type'             => '',
								'deposit_amount'           => '',
								'avil_by_date'             => '',
								'gallery'                  => '46,47,48',
								'features'                 => array(
									'0' => 24,
									'1' => 28,
								),
								'avail_date'               => '0',
							),
						),
						'room_gallery'                    => '{"1":"https://example.com/uploads/room-gallery-1.jpg,https://example.com/uploads/room-gallery-2.jpg"}',
						'features'                        => '[["Bathtub","Breakfast","Swimming Pool","Wifi"]]',
						'avail_date'                      => '0|0',
						'hotel_feature'                   => 'Bathtub, Breakfast, Swimming Pool, Wifi',
						'features_icon'                   => 'Bathtub(ri-css3-line), Breakfast(ri-goblet-line), Swimming Pool(fab fa-audible), Wifi(ri-base-station-line)',
						'hotel_location'                  => 'Switzerland',
						'hotel_type'                      => 'Luxury, Boutique',
						'airport_service'                 => '0',
						'airport_service_type'            => '',
						'[airport_pickup_price][airport_pickup_price_type]' => '',
						'airport_pickup_price'            => array(
							'airport_pickup_price_type'    => '',
							'airport_service_fee_adult'    => '',
							'airport_service_fee_children' => '',
							'airport_service_fee_fixed'    => '',
						),
						'[airport_pickup_price][airport_service_fee_adult]'    => '',
						'[airport_pickup_price][airport_service_fee_children]' => '',
						'[airport_pickup_price][airport_service_fee_fixed]'    => '',
						'[airport_dropoff_price][airport_pickup_price_type]'   => '',
						'airport_dropoff_price'           => array(
							'airport_pickup_price_type'    => '',
							'airport_service_fee_adult'    => '',
							'airport_service_fee_children' => '',
							'airport_service_fee_fixed'    => '',
						),
						'[airport_dropoff_price][airport_service_fee_adult]'    => '',
						'[airport_dropoff_price][airport_service_fee_children]' => '',
						'[airport_dropoff_price][airport_service_fee_fixed]'    => '',
						'[airport_pickup_dropoff_price][airport_pickup_price_type]' => '',
						'airport_pickup_dropoff_price'   => array(
							'airport_pickup_price_type'    => '',
							'airport_service_fee_adult'    => '',
							'airport_service_fee_children' => '',
							'airport_service_fee_fixed'    => '',
						),
						'[airport_pickup_dropoff_price][airport_service_fee_adult]'    => '',
						'[airport_pickup_dropoff_price][airport_service_fee_children]' => '',
						'[airport_pickup_dropoff_price][airport_service_fee_fixed]'    => '',
						'faq-section-title'               => 'Faq’s',
						'faq'                             => 'serialized string',
						'h-enquiry-section'               => '1',
						'h-enquiry-option-icon'           => 'fa fa-question-circle-o',
						'h-enquiry-option-title'          => 'Have a question in mind',
						'h-enquiry-option-content'        => 'Looking for more info? Send a question to the property to find out more.',
						'h-enquiry-option-btn'            => 'Ask a Question',
						'h-review'                        => '',
						'h-share'                         => '',
						'h-wishlist'                      => '',
						'popular-section-title'           => 'Popular Features',
						'review-section-title'            => 'Average Guest Reviews',
						'tc-section-title'                => 'Hotel Terms & Conditions',
						'tc'                              => 'Terms and conditions content.',
						'post_date'                       => '2024-01-15 08:13:23',
						'tf_rooms'                        => array( 69, 71 ),
					),
					'reviews'        => array(
						'hotel_reviews' => 0,
						'review_text'   => '',
					),
					'start_price'    => '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&#36;</span>59.00</bdi></span>',
					'rooms'          => array(
						array(
							'ID'                    => 71,
							'post_author'           => '1',
							'post_date'             => '2026-04-27 03:54:55',
							'post_date_gmt'         => '2026-04-27 03:54:55',
							'post_content'          => 'Room post content.',
							'post_title'            => 'Premium Deluxe Twin',
							'post_excerpt'          => '',
							'post_status'           => 'publish',
							'comment_status'        => 'closed',
							'ping_status'           => 'closed',
							'post_password'         => '',
							'post_name'             => 'premium-deluxe-twin',
							'to_ping'               => '',
							'pinged'                => '',
							'post_modified'         => '2026-04-27 03:54:55',
							'post_modified_gmt'     => '2026-04-27 03:54:55',
							'post_content_filtered' => '',
							'post_parent'           => 0,
							'guid'                  => 'https://example.com/rooms/premium-deluxe-twin/',
							'menu_order'            => 0,
							'post_type'             => 'tf_room',
							'post_mime_type'        => '',
							'comment_count'         => '0',
							'filter'                => 'raw',
						),
					),
				),
			),
			'total' => 6,
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