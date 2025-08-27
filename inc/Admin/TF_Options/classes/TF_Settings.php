<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'TF_Settings' ) ) {
	class TF_Settings {

		public $option_id = null;
		public $option_title = null;
		public $option_icon = null;
		public $option_position = null;
		public $option_sections = array();
		public $pre_tabs;
		public $pre_fields;
		public $pre_sections;

		public function __construct( $key, $params = array() ) {
			$this->option_id       = $key;
			$this->option_title    = ! empty( $params['title'] ) ? apply_filters( $key . '_title', $params['title'] ) : '';
			$this->option_icon     = ! empty( $params['icon'] ) ? apply_filters( $key . '_icon', $params['icon'] ) : '';
			$this->option_position = ! empty( $params['position'] ) ? apply_filters( $key . '_position', $params['position'] ) : 5;
			$this->option_sections = ! empty( $params['sections'] ) ? apply_filters( $key . '_sections', $params['sections'] ) : array();

			// run only is admin panel options, avoid performance loss
			$this->pre_tabs     = $this->pre_tabs( $this->option_sections );
			$this->pre_fields   = $this->pre_fields( $this->option_sections );
			$this->pre_sections = $this->pre_sections( $this->option_sections );

			//options
			add_action( 'admin_menu', array( $this, 'tf_options' ) );

			//save options
			add_action( 'admin_init', array( $this, 'save_options' ) );

			//ajax save options
			add_action( 'wp_ajax_tf_options_save', array( $this, 'tf_ajax_save_options' ) );
			add_action( 'wp_ajax_tf_options_reset', array( $this, 'tf_ajax_reset_options' ) );
			add_action( 'wp_ajax_tf_search_settings_autocomplete', array( $this, 'tf_search_settings_autocomplete_callback' ) );

            add_action( 'wp_ajax_tf_export_data', array( $this, 'tf_export_data' ) );
        }

        public static function option( $key, $params = array() ) {
			return new self( $key, $params );
		}

		public function pre_tabs( $sections ) {

			$result  = array();
			$parents = array();

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $section['parent'] ) ) {
					$parents[ $section['parent'] ][ $key ] = $section;
					unset( $sections[ $key ] );
				}
			}

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $key ) && ! empty( $parents[ $key ] ) ) {
					$section['sub_section'] = $parents[ $key ];
				}
				$result[ $key ] = $section;
			}

			return $result;
		}

		public function pre_fields( $sections ) {

			$result = array();

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						$result[] = $field;
					}
				}
			}

			return $result;
		}

		public function pre_sections( $sections ) {

			$result = array();

			foreach ( $this->pre_tabs as $tab ) {
				if ( ! empty( $tab['subs'] ) ) {
					foreach ( $tab['subs'] as $sub ) {
						$sub['ptitle'] = $tab['title'];
						$result[]      = $sub;
					}
				}
				if ( empty( $tab['subs'] ) ) {
					$result[] = $tab;
				}
			}

			return $result;
		}

		/**
		 * Options Page menu
		 * @author Foysal
		 */
		public function tf_options() {
			add_menu_page(
				$this->option_title,
				$this->option_title,
				'manage_options',
				$this->option_id,
				array( $this, 'tf_options_page' ),
				$this->option_icon,
				$this->option_position
			);

            //Dashboard submenu
			add_submenu_page(
				$this->option_id,
				esc_html__('Dashboard', 'tourfic'),
				esc_html__('Dashboard', 'tourfic'),
				'manage_options',
				'tf_dashboard',
				array( $this, 'tf_dashboard_page' ),
			);

			//Setting submenu
			add_submenu_page(
				$this->option_id,
				esc_html__('Settings', 'tourfic'),
				esc_html__('Settings', 'tourfic'),
				'manage_options',
				$this->option_id . '#tab=general',
				array( $this, 'tf_options_page' ),
			);

			//Get Help submenu
			add_submenu_page(
				$this->option_id,
				esc_html__('Get Help', 'tourfic'),
				esc_html__('Get Help', 'tourfic'),
				'manage_options',
				'tf_get_help',
				array( $this,'tf_get_help_callback'),
			);

			// Shortcode submenu
			add_submenu_page(
				$this->option_id,
				esc_html__('Shortcodes', 'tourfic'),
				esc_html__('Shortcodes', 'tourfic'),
				'manage_options',
				'tf_shortcodes',
				array( 'TF_Shortcodes','tf_shortcode_callback'),
			);

			// Library submenu
			if ( is_plugin_active( 'travelfic-toolkit/travelfic-toolkit.php' ) ) {
				$library_url = admin_url( 'admin.php?page=travelfic-template-list' );
				add_submenu_page(
					$this->option_id,
					esc_html__('Template Library', 'tourfic'),
					esc_html__('Template Library', 'tourfic'),
					'manage_options',
					$library_url,
					''
				);
			}

			// remove first submenu
			remove_submenu_page( $this->option_id, $this->option_id );

		}

		/**
		 * Options Page HTML
		 * @author Jahid, Foysal
		 */
		public function tf_dashboard_page() {
			?>
			<div class="tf-setting-dashboard">
				<!-- dashboard-header-include -->
				<?php \Tourfic\Classes\Helper::tf_dashboard_header(); ?>

				<div class="tf-setting-preview">
					<div class="tf-setting-overview-section">
						<h2><?php esc_html_e("Overview","tourfic"); ?></h2>
						<div class="tf-performance-grid">
							<div class="tf-single-performance-grid">
								<div class="tf-single-performance-icon">
								<img src="<?php echo esc_url(TF_ASSETS_APP_URL.'images/tf-hotel.png'); ?>" alt="total Hotel">
								</div>
								<div class="tf-single-performance-content">
									<p><?php esc_html_e("Total Hotels","tourfic"); ?></p>
									<h3>
										<?php
										$tf_total_hotels = array(
											'post_type'      => 'tf_hotel',
											'post_status'    => 'publish',
											'posts_per_page' => - 1
										);
										echo count( get_posts ($tf_total_hotels ) );
										?>
									</h3>
								</div>
							</div>

							<div class="tf-single-performance-grid">
								<div class="tf-single-performance-icon">
								<img src="<?php echo esc_url(TF_ASSETS_APP_URL.'images/tf-tours.png'); ?>" alt="total Tours">
								</div>
								<div class="tf-single-performance-content">
									<p><?php esc_html_e("Total Tours","tourfic"); ?></p>
									<h3>
										<?php
										$tf_total_tours = array(
											'post_type'      => 'tf_tours',
											'post_status'    => 'publish',
											'posts_per_page' => - 1
										);
										echo count( get_posts ($tf_total_tours ));
										?>
									</h3>
								</div>
							</div>

							<div class="tf-single-performance-grid">
								<div class="tf-single-performance-icon">
									<img src="<?php echo esc_url(TF_ASSETS_APP_URL.'images/tf-apartment.png'); ?>" alt="total apartment">
								</div>
								<div class="tf-single-performance-content">
									<p><?php esc_html_e("Total Apartments","tourfic"); ?></p>
									<h3>
										<?php
										$tf_total_apartments = array(
											'post_type'      => 'tf_apartment',
											'post_status'    => 'publish',
											'posts_per_page' => - 1
										);
										echo count( get_posts ($tf_total_apartments ) );
										?>
									</h3>
								</div>
							</div>

							<div class="tf-single-performance-grid">
								<div class="tf-single-performance-icon">
								<img src="<?php echo esc_url(TF_ASSETS_APP_URL.'images/tf-booking-online.png'); ?>" alt="total Booking">
								</div>
								<div class="tf-single-performance-content">
									<p><?php esc_html_e("Total Bookings","tourfic"); ?></p>
									<h3>
										<?php
										if ( Helper::tf_is_woo_active() ) {
											$tf_order_query_orders = wc_get_orders( array(
													'limit'  => - 1,
													'type'   => 'shop_order',
													'status' => array( 'wc-completed' ),
												)
											);
											echo count( $tf_order_query_orders );
										} else {
											echo '0';
										}
										?>
									</h3>
								</div>
							</div>
							<div class="tf-single-performance-grid">
								<div class="tf-single-performance-icon">
								<img src="<?php echo esc_url(TF_ASSETS_APP_URL.'images/tf-add-user.png'); ?>" alt="total Customer">
								</div>
								<div class="tf-single-performance-content">
									<p><?php esc_html_e("Total Customers","tourfic"); ?></p>
									<h3>
										<?php
										$tf_customer_query = new WP_User_Query(
											array(
												'role' => 'customer',
											)
										);
										echo count( $tf_customer_query->get_results() );
										?>
									</h3>
								</div>
							</div>
						</div>
					</div>

					<div class="tf-setting-performace-section">
						<div class="tf-report-wrapper">
							<div id="tf-report-loader">
								<img src="<?php echo esc_url(TF_ASSETS_APP_URL.'images/loader.gif'); ?>" alt="Loader">
							</div>
							<div class="tf-report-filter">
								<h2><?php esc_html_e("Reports","tourfic"); ?></h2>

								<?php if(class_exists('WooCommerce')): ?>
									<div class="tf-dates-filter">
										<div class="tf-month-filter">
											<span><?php esc_html_e("Year","tourfic"); ?></span>
											<select name="tf-year-report" id="tf-year-report">
												<option value="24"><?php esc_html_e("2024","tourfic"); ?></option>
												<option value="23"><?php esc_html_e("2023","tourfic"); ?></option>
												<option value="22"><?php esc_html_e("2022","tourfic"); ?></option>
												<option value="21"><?php esc_html_e("2021","tourfic"); ?></option>
												<option value="20"><?php esc_html_e("2020","tourfic"); ?></option>
												<option value="19"><?php esc_html_e("2019","tourfic"); ?></option>
												<option value="18"><?php esc_html_e("2018","tourfic"); ?></option>
												<option value="17"><?php esc_html_e("2017","tourfic"); ?></option>
											</select>
										</div>
										<div class="tf-month-filter">
											<span><?php esc_html_e("Month","tourfic"); ?></span>
											<select name="tf-month-report" id="tf-month-report">
												<option value=""><?php esc_html_e("Select Month","tourfic"); ?></option>
												<option value="1"><?php esc_html_e("January","tourfic"); ?></option>
												<option value="2"><?php esc_html_e("February","tourfic"); ?></option>
												<option value="3"><?php esc_html_e("March","tourfic"); ?></option>
												<option value="4"><?php esc_html_e("April","tourfic"); ?></option>
												<option value="5"><?php esc_html_e("May","tourfic"); ?></option>
												<option value="6"><?php esc_html_e("June","tourfic"); ?></option>
												<option value="7"><?php esc_html_e("July","tourfic"); ?></option>
												<option value="8"><?php esc_html_e("August","tourfic"); ?></option>
												<option value="9"><?php esc_html_e("September","tourfic"); ?></option>
												<option value="10"><?php esc_html_e("October","tourfic"); ?></option>
												<option value="11"><?php esc_html_e("November","tourfic"); ?></option>
												<option value="12"><?php esc_html_e("December","tourfic"); ?></option>
											</select>
										</div>
									</div>
								<?php endif; ?>
							</div>

							<?php if(class_exists('WooCommerce')): ?>
								<div class="tf-order-report">
									<canvas id="tf_months" height="450"></canvas>
								</div>
							<?php else : ?>
								<div class="tf-field-notice-inner tf-notice-danger" style="margin-top: 20px;">
									<?php esc_html_e( 'Please install and activate WooCommerce plugin to view reports.', 'tourfic' ); ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="tf-settings-sidebar">
							<div class="tf-sidebar-content">
								<div class="tf-customization-quote">
									<div class="tf-quote-header">
										<i class="fa-solid fa-code"></i>
										<h3><?php esc_html_e('Need help building your Travel, Hotel, or Rental Website?', 'tourfic');  ?></h3>
									</div>
									<div class="tf-quote-content">
										<p><?php esc_html_e('Let our expert team craft a custom WordPress site tailored to your business—whether you\'re running a hotel, tour agency, or vacation rental. Optimized for performance, bookings, and conversions.', 'tourfic'); ?></p>
										<a href="<?php echo esc_url( Helper::tf_utm_generator( 'https://portal.themefic.com/hire-us/', array( 'utm_medium' => 'dashboard_free_quote' ) ) ); ?>" target="_blank" class="tf-admin-btn tf-btn-secondary"><?php esc_html_e('Get Free Quote', 'tourfic');  ?></a>								
									</div>
								</div>

								<?php $plugins = [
									[
										'name'       => 'Instantio',
										'slug'       => 'instantio',
										'file_name'  => 'instantio',
										'subtitle'   => 'WooCommerce Quick & Direct Checkout',
										'image'      => 'https://ps.w.org/instantio/assets/icon-128x128.png',
									],
									[
										'name'       => 'Hydra',
										'slug'       => 'hydra-booking',
										'file_name'  => 'hydra-booking',
										'subtitle'   => 'All in One Appointment Booking System',
										'image'      => 'https://ps.w.org/hydra-booking/assets/icon-128x128.jpg',
									],
									[
										'name'       => 'BEAF',
										'slug'       => 'beaf-before-and-after-gallery',
										'file_name'  => 'before-and-after-gallery',
										'subtitle'   => 'Ultimate Before After Image Slider & Gallery',
										'image'      => 'https://ps.w.org/beaf-before-and-after-gallery/assets/icon-128x128.png',
									],
									[
										'name'       => 'UACF7',
										'slug'       => 'ultimate-addons-for-contact-form-7',
										'file_name'  => 'ultimate-addons-for-contact-form-7',
										'subtitle'   => '40+ Essential Addons for Contact Form 7',
										'image'      => 'https://ps.w.org/ultimate-addons-for-contact-form-7/assets/icon-128x128.png',
									],
								];
								?>



								<div class="tf-quick-access">
									<h3><?php esc_html_e('Helpful Resources', 'tourfic');  ?></h3>
									<div class="tf-quick-access-wrapper">
										<div class="tf-access-item">
											<a href="<?php echo esc_url( Helper::tf_utm_generator( 'https://themefic.com/docs/tourfic/', array( 'utm_medium' => 'dashboard_doc_link' ) ) ); ?>" target="_blank">
												<span class="icon"><i class="fa-solid fa-folder-open"></i></span>
												<?php esc_html_e( 'Documentation', 'tourfic' ); ?>
											</a>
										</div>
										<div class="tf-access-item">
											<a href="<?php echo esc_url( Helper::tf_utm_generator( 'https://portal.themefic.com/support/', array( 'utm_medium' => 'dashboard_support_link' ) ) ); ?>" target="_blank">
												<span class="icon"><i class="fa-solid fa-headset"></i></span>
												<?php esc_html_e( 'Get Support', 'tourfic' ); ?>
											</a>
										</div>
										<div class="tf-access-item">
											<a href="https://www.facebook.com/groups/tourfic/" target="_blank">
												<span class="icon"><i class="fa-solid fa-users"></i></span>
												<?php esc_html_e( 'Join our Community', 'tourfic' ); ?>
											</a>
										</div>
										<div class="tf-access-item">
											<a href="https://app.loopedin.io/tourfic" target="_blank">
												<span class="icon"><i class="fa-solid fa-road-circle-check"></i></span>
												<?php esc_html_e( 'See our Roadmap', 'tourfic' ); ?>
											</a>
										</div>
										<div class="tf-access-item">
											<a href="https://app.loopedin.io/tourfic#/ideas-board" target="_blank">
												<span class="icon"><i class="fa-solid fa-lightbulb"></i></span>
												<?php esc_html_e( 'Request a Feature', 'tourfic' ); ?>
											</a>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>

			<?php
		}

		

		/**
		 * Get Help Page
		 * @author Jahid, Foysal
		 */
		public function tf_get_help_callback(){
			?>
			<div class="tf-setting-dashboard">

				<!-- dashboard-header-include -->
				<?php \Tourfic\Classes\Helper::tf_dashboard_header(); ?>

				<div class="tf-settings-help-center">

					<div class="tf-support-cards">
						<!-- Setup Wizard -->
						<div class="tf-single-support-card">
							<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
								<g clip-path="url(#clip0_651_6486)">
									<path d="M14.0332 13.6318L15.2675 10.4432C15.4762 9.90306 15.8432 9.43859 16.3204 9.11066C16.7976 8.78273 17.3627 8.60664 17.9418 8.60547H20.2972C20.8756 8.60733 21.4401 8.78374 21.9166 9.11163C22.3931 9.43951 22.7596 9.90361 22.9681 10.4432L24.2023 13.6318L28.3955 16.0455L31.7898 15.5278C32.3535 15.4516 32.9272 15.5446 33.4381 15.7949C33.949 16.0451 34.3741 16.4414 34.6595 16.9335L35.8081 18.946C36.1031 19.4468 36.2391 20.0254 36.1981 20.6052C36.157 21.185 35.9409 21.7386 35.5783 22.1929L33.4801 24.8638V29.6878L35.6366 32.362C35.9981 32.8162 36.2134 33.3692 36.2545 33.9481C36.2955 34.5271 36.1602 35.1049 35.8664 35.6055L34.7178 37.618C34.4325 38.111 34.0069 38.508 33.4952 38.7583C32.9836 39.0086 32.409 39.101 31.8446 39.0238L28.4538 38.5095L24.2606 40.9198L23.0263 44.1083C22.8177 44.6484 22.4507 45.1129 21.9735 45.4408C21.4963 45.7688 20.9311 45.9449 20.3521 45.946H17.9418C17.3627 45.9449 16.7976 45.7688 16.3204 45.4408C15.8432 45.1129 15.4762 44.6484 15.2675 44.1083L14.0332 40.9198L9.84006 38.506L6.45263 39.0238C5.88833 39.1006 5.31392 39.008 4.80236 38.7577C4.2908 38.5074 3.86516 38.1107 3.57949 37.618L2.43092 35.6055C2.13712 35.1049 2.00181 34.5271 2.04282 33.9481C2.08382 33.3692 2.29922 32.8162 2.66063 32.362L4.75549 29.6878V24.8638L2.60235 22.1895C2.24093 21.7353 2.02554 21.1823 1.98453 20.6034C1.94353 20.0244 2.07883 19.4466 2.37263 18.946L3.5212 16.9369C3.80639 16.4436 4.23183 16.0462 4.74343 15.7953C5.25503 15.5444 5.82969 15.4513 6.39435 15.5278L9.78178 16.042L14.0332 13.6318ZM13.3749 27.2775C13.3562 28.0437 13.4909 28.8059 13.7712 29.5192C14.0515 30.2325 14.4716 30.8826 15.0069 31.4311C15.5422 31.9797 16.1818 32.4156 16.8881 32.7132C17.5944 33.0108 18.3531 33.1641 19.1195 33.1641C19.8859 33.1641 20.6446 33.0108 21.3509 32.7132C22.0572 32.4156 22.6968 31.9797 23.232 31.4311C23.7673 30.8826 24.1875 30.2325 24.4678 29.5192C24.748 28.8059 24.8828 28.0437 24.8641 27.2775C24.8274 25.7782 24.2061 24.3527 23.1328 23.3053C22.0595 22.2578 20.6192 21.6715 19.1195 21.6715C17.6198 21.6715 16.1795 22.2578 15.1062 23.3053C14.0329 24.3527 13.4115 25.7782 13.3749 27.2775Z" fill="#FFC100" fill-opacity="0.3"/>
									<path d="M20.2972 8.60547H17.9418C17.3627 8.60664 16.7976 8.78273 16.3204 9.11066C15.8432 9.43859 15.4762 9.90306 15.2675 10.4432L14.0332 13.6318L9.78177 16.0455L6.39435 15.5278C5.83004 15.4509 5.25564 15.5435 4.74407 15.7938C4.23251 16.0441 3.80687 16.4408 3.5212 16.9335L2.37263 18.946C2.07807 19.447 1.94234 20.0255 1.98336 20.6051C2.02437 21.1848 2.24019 21.7384 2.60235 22.1929L4.75549 24.8638V29.6878L2.66063 32.362C2.29921 32.8162 2.08382 33.3692 2.04282 33.9481C2.00181 34.5271 2.13712 35.1049 2.43092 35.6055L3.57949 37.618C3.86481 38.111 4.2904 38.508 4.80205 38.7583C5.31371 39.0086 5.88829 39.101 6.45263 39.0238L9.84006 38.5095L14.0332 40.9198L15.2675 44.1083C15.4762 44.6484 15.8432 45.1129 16.3204 45.4408C16.7976 45.7688 17.3627 45.9449 17.9418 45.946H20.3555C20.9339 45.9442 21.4983 45.7678 21.9749 45.4399C22.4514 45.112 22.8179 44.6479 23.0263 44.1083L24.2606 40.9198L28.4538 38.506L31.8446 39.0238C32.4089 39.1006 32.9833 39.008 33.4949 38.7577C34.0065 38.5074 34.4321 38.1107 34.7178 37.618L35.8663 35.6055C36.1601 35.1049 36.2955 34.5271 36.2544 33.9481C36.2134 33.3692 35.998 32.8162 35.6366 32.362L33.4801 29.6878V26.578M13.3749 27.2775C13.3562 28.0437 13.4909 28.8059 13.7712 29.5192C14.0515 30.2325 14.4716 30.8826 15.0069 31.4311C15.5422 31.9797 16.1818 32.4156 16.8881 32.7132C17.5944 33.0108 18.3531 33.1641 19.1195 33.1641C19.8859 33.1641 20.6446 33.0108 21.3509 32.7132C22.0572 32.4156 22.6968 31.9797 23.232 31.4311C23.7673 30.8826 24.1875 30.2325 24.4678 29.5192C24.748 28.8059 24.8828 28.0437 24.8641 27.2775C24.8274 25.7782 24.2061 24.3527 23.1328 23.3053C22.0595 22.2578 20.6192 21.6715 19.1195 21.6715C17.6198 21.6715 16.1795 22.2578 15.1062 23.3053C14.0329 24.3527 13.4115 25.7782 13.3749 27.2775Z" stroke="#FFC100" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
									<path d="M27.7715 12.353C26.7223 12.1713 26.7223 10.6593 27.7715 10.4742C29.6367 10.1509 31.3633 9.27944 32.7312 7.97083C34.099 6.66222 35.0461 4.97582 35.4515 3.12674L35.5132 2.83531C35.7429 1.79646 37.2241 1.7896 37.4606 2.82846L37.5395 3.16446C37.9595 5.00442 38.9139 6.67923 40.2828 7.97853C41.6516 9.27783 43.3739 10.1437 45.2332 10.4673C46.2926 10.6559 46.2926 12.1747 45.2332 12.3565C43.3734 12.6803 41.6508 13.5467 40.282 14.8466C38.9131 16.1466 37.9589 17.8221 37.5395 19.6627L37.4606 20.0022C37.2241 21.0376 35.7429 21.0307 35.5132 19.9919L35.4515 19.7039C35.0471 17.8551 34.1014 16.1686 32.7348 14.8595C31.3682 13.5503 29.6428 12.6777 27.7783 12.353H27.7715Z" fill="white"/>
									<path d="M27.7715 12.353C26.7223 12.1713 26.7223 10.6593 27.7715 10.4742C29.6367 10.1509 31.3633 9.27944 32.7312 7.97083C34.099 6.66222 35.0461 4.97582 35.4515 3.12674L35.5132 2.83531C35.7429 1.79646 37.2241 1.7896 37.4606 2.82846L37.5395 3.16446C37.9595 5.00442 38.9139 6.67923 40.2828 7.97853C41.6516 9.27783 43.3739 10.1437 45.2332 10.4673C46.2926 10.6559 46.2926 12.1747 45.2332 12.3565C43.3734 12.6803 41.6508 13.5467 40.282 14.8466C38.9131 16.1466 37.9589 17.8221 37.5395 19.6627L37.4606 20.0022C37.2241 21.0376 35.7429 21.0307 35.5132 19.9919L35.4515 19.7039C35.0471 17.8551 34.1014 16.1686 32.7348 14.8595C31.3682 13.5503 29.6428 12.6777 27.7783 12.353H27.7715Z" stroke="#FFC100" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
								</g>
								<defs>
									<clipPath id="clip0_651_6486">
									<rect width="48" height="48" fill="white"/>
									</clipPath>
								</defs>
							</svg>
							<h3><?php esc_html_e("Get Started Quickly","tourfic"); ?></h3>
							<p><?php esc_html_e("Use our guided setup wizard to get up and running fast.","tourfic"); ?></p>
							<a href="#" target="" class="tf-link-skip-btn"><?php esc_html_e("Setup Wizard","tourfic"); ?></a>
						</div>

						<!-- Customization -->
						<div class="tf-single-support-card">
							<svg xmlns="http://www.w3.org/2000/svg" width="49" height="48" viewBox="0 0 49 48" fill="none">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M8.21669 40.9014C9.68335 41.6348 11.1667 42.0014 12.6667 42.0014C14.8667 42.0014 16.75 41.218 18.3167 39.6514C19.8833 38.0848 20.6667 36.2014 20.6667 34.0014C20.6667 32.3348 20.0833 30.918 18.9167 29.7514C17.75 28.5848 16.3333 28.0014 14.6667 28.0014C13 28.0014 11.5833 28.5848 10.4167 29.7514C9.25003 30.918 8.66669 32.3348 8.66669 34.0014C8.66669 35.3014 8.21669 36.293 7.31669 36.9764C6.41669 37.6598 5.53335 38.0014 4.66669 38.0014C5.56669 39.2014 6.75003 40.168 8.21669 40.9014ZM18.6667 24.5014L24.1667 30.0014L42.0667 12.1014C42.4667 11.7014 42.6667 11.2347 42.6667 10.7014C42.6667 10.1681 42.4667 9.70139 42.0667 9.30139L39.3667 6.60139C38.9667 6.20139 38.4917 6.00971 37.9417 6.02639C37.3917 6.04305 36.9333 6.23471 36.5667 6.60139L18.6667 24.5014ZM34.3667 43.001L34.9667 46.001H38.9667L39.5669 43.001C39.9667 42.8344 40.3417 42.6594 40.6919 42.476C41.0417 42.2926 41.4001 42.0676 41.7667 41.801L44.6667 42.701L46.6667 39.301L44.3667 37.301C44.4335 36.8676 44.4667 36.4344 44.4667 36.001C44.4667 35.5676 44.4335 35.1344 44.3667 34.701L46.6667 32.701L44.6667 29.301L41.7667 30.201C41.4001 29.9344 41.0417 29.7094 40.6919 29.526C40.3417 29.3426 39.9667 29.1676 39.5669 29.001L38.9667 26.001H34.9667L34.3667 29.001C33.9667 29.1676 33.5917 29.3426 33.2417 29.526C32.8917 29.7094 32.5335 29.9344 32.1667 30.201L29.2667 29.301L27.2667 32.701L29.5667 34.701C29.5001 35.1344 29.4667 35.5676 29.4667 36.001C29.4667 36.4344 29.5001 36.8676 29.5667 37.301L27.2667 39.301L29.2667 42.701L32.1667 41.801C32.5335 42.0676 32.8917 42.2926 33.2417 42.476C33.5917 42.6594 33.9667 42.8344 34.3667 43.001ZM39.7917 38.826C39.0085 39.6094 38.0667 40.001 36.9667 40.001C35.8667 40.001 34.9251 39.6094 34.1417 38.826C33.3585 38.0426 32.9667 37.101 32.9667 36.001C32.9667 34.901 33.3585 33.9594 34.1417 33.176C34.9251 32.3926 35.8667 32.001 36.9667 32.001C38.0667 32.001 39.0085 32.3926 39.7917 33.176C40.5751 33.9594 40.9667 34.901 40.9667 36.001C40.9667 37.101 40.5751 38.0426 39.7917 38.826Z" fill="#FFC100"/>
							</svg>
							<h3><?php esc_html_e("Need a Custom Solution?","tourfic"); ?></h3>
							<p><?php esc_html_e("We offer tailored plugin solutions based on your specific needs.","tourfic"); ?></p>
							<a href="<?php echo esc_url(Helper::tf_utm_generator( 'https://portal.themefic.com/hire-us/', array( 'utm_medium' => 'get_help_request_quote' ) )); ?>" target="_blank" class="tf-link-skip-btn"><?php esc_html_e("Request Customization","tourfic"); ?></a>
						</div>

						<!-- Help Center -->
						<div class="tf-single-support-card">
							<svg xmlns="http://www.w3.org/2000/svg" width="49" height="48" viewBox="0 0 49 48" fill="none">
								<g clip-path="url(#clip0_662_774)">
									<path fill-rule="evenodd" clip-rule="evenodd" d="M14.3333 12C13.2333 12 12.2916 11.6083 11.5083 10.825C10.7249 10.0417 10.3333 9.1 10.3333 8C10.3333 6.86666 10.7249 5.91666 11.5083 5.15C12.2916 4.38334 13.2333 4 14.3333 4C15.4666 4 16.4166 4.38334 17.1833 5.15C17.9499 5.91666 18.3333 6.86666 18.3333 8C18.3333 9.1 17.9499 10.0417 17.1833 10.825C16.4166 11.6083 15.4666 12 14.3333 12ZM6.33325 19.2689V18.85C6.33325 17.4167 7.06659 16.25 8.53325 15.35C9.99991 14.45 11.9333 14 14.3333 14C16.1307 14 17.6663 14.2524 18.9403 14.7572C18.6082 15.0073 18.2871 15.284 17.977 15.5875C16.2145 17.3125 15.3333 19.45 15.3333 22H14.1475C13.9082 21.6536 13.6326 21.3244 13.3208 21.0126C11.9791 19.6708 10.3166 19 8.33325 19C7.62955 19 6.96289 19.0896 6.33325 19.2689ZM0.333252 40V36.85C0.333252 35.4166 1.06659 34.25 2.53325 33.35C3.99991 32.45 5.93325 32 8.33325 32C8.76659 32 9.18325 32.0084 9.58325 32.025C9.98325 32.0416 10.3666 32.0834 10.7333 32.15C10.2666 32.85 9.91659 33.5834 9.68325 34.35C9.44991 35.1166 9.33325 35.9166 9.33325 36.75V40H0.333252ZM12.3333 40V36.75C12.3333 35.6834 12.6249 34.7084 13.2083 33.825C13.7916 32.9416 14.6166 32.1666 15.6833 31.5C16.7499 30.8334 18.0249 30.3334 19.5083 30C20.9917 29.6666 22.5999 29.5 24.3333 29.5C26.0999 29.5 27.7249 29.6666 29.2083 30C30.6917 30.3334 31.9667 30.8334 33.0333 31.5C34.0999 32.1666 34.9167 32.9416 35.4833 33.825C36.0499 34.7084 36.3333 35.6834 36.3333 36.75V40H12.3333ZM39.3333 40V36.75C39.3333 35.8834 39.2248 35.0666 39.0083 34.3C38.7917 33.5334 38.4667 32.8166 38.0333 32.15C38.3999 32.0834 38.7749 32.0416 39.1583 32.025C39.5417 32.0084 39.9333 32 40.3333 32C42.7333 32 44.6667 32.4416 46.1333 33.325C47.5998 34.2084 48.3333 35.3834 48.3333 36.85V40H39.3333ZM8.33325 30C7.23325 30 6.29159 29.6084 5.50825 28.825C4.72491 28.0416 4.33325 27.1 4.33325 26C4.33325 24.8666 4.72491 23.9166 5.50825 23.15C6.29159 22.3834 7.23325 22 8.33325 22C9.46659 22 10.4166 22.3834 11.1833 23.15C11.9499 23.9166 12.3333 24.8666 12.3333 26C12.3333 27.1 11.9499 28.0416 11.1833 28.825C10.4166 29.6084 9.46659 30 8.33325 30ZM40.3333 30C39.2333 30 38.2917 29.6084 37.5083 28.825C36.7248 28.0416 36.3333 27.1 36.3333 26C36.3333 24.8666 36.7248 23.9166 37.5083 23.15C38.2917 22.3834 39.2333 22 40.3333 22C41.4667 22 42.4166 22.3834 43.1833 23.15C43.9499 23.9166 44.3333 24.8666 44.3333 26C44.3333 27.1 43.9499 28.0416 43.1833 28.825C42.4166 29.6084 41.4667 30 40.3333 30ZM24.3333 28C22.6667 28 21.2499 27.4166 20.0833 26.25C18.9166 25.0834 18.3333 23.6666 18.3333 22C18.3333 20.3 18.9166 18.875 20.0833 17.725C21.2499 16.575 22.6667 16 24.3333 16C26.0333 16 27.4583 16.575 28.6083 17.725C29.7583 18.875 30.3333 20.3 30.3333 22C30.3333 23.6666 29.7583 25.0834 28.6083 26.25C27.4583 27.4166 26.0333 28 24.3333 28ZM37.1599 10.825C36.3764 11.6083 35.4348 12 34.3349 12C33.2015 12 32.2515 11.6083 31.4849 10.825C30.7181 10.0417 30.3349 9.1 30.3349 8C30.3349 6.86666 30.7181 5.91666 31.4849 5.15C32.2515 4.38334 33.2015 4 34.3349 4C35.4348 4 36.3764 4.38334 37.1599 5.15C37.9431 5.91666 38.3349 6.86666 38.3349 8C38.3349 9.1 37.9431 10.0417 37.1599 10.825ZM42.3349 18.85V19.2689C41.7051 19.0896 41.0385 19 40.3349 19C38.3515 19 36.6889 19.6708 35.3473 21.0126C35.0355 21.3244 34.7598 21.6536 34.5205 22H33.3349C33.3349 19.45 32.4535 17.3125 30.6911 15.5875C30.3811 15.284 30.0599 15.0073 29.7279 14.7572C31.0017 14.2524 32.5374 14 34.3349 14C36.7349 14 38.6681 14.45 40.1349 15.35C41.6015 16.25 42.3349 17.4167 42.3349 18.85Z" fill="#FFC100"/>
								</g>
								<defs>
									<clipPath id="clip0_662_774">
									<rect width="48" height="48" fill="white" transform="translate(0.333252)"/>
									</clipPath>
								</defs>
							</svg>
							<h3><?php esc_html_e("Need a Hand?","tourfic"); ?></h3>
							<p><?php esc_html_e("Get expert support and connect with fellow Tourfic users.","tourfic"); ?></p>
							<a href="<?php echo esc_url(Helper::tf_utm_generator( 'https://www.facebook.com/groups/tourfic/', array( 'utm_medium' => 'get_help_community' ) )); ?>" target="_blank" class="tf-link-skip-btn"><?php esc_html_e("Join the community","tourfic"); ?></a>
						</div>
					</div>

					<div class="tf-support-cards tf-support-cards-4">
						<div class="tf-single-support-card">
							<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M9.175 42.825C9.95834 43.6084 10.9 44 12 44H36C37.1 44 38.0416 43.6084 38.825 42.825C39.6084 42.0416 40 41.1 40 40V16L28 4H12C10.9 4 9.95834 4.39166 9.175 5.175C8.39166 5.95834 8 6.9 8 8V40C8 41.1 8.39166 42.0416 9.175 42.825ZM22 15.8789L23.0606 16.9396L26 19.8789L28.9394 16.9396L31.0606 19.0609L27.0606 23.0608L26 24.1216L24.9394 23.0608L22 20.1216L19.0607 23.0608L16.9393 20.9396L20.9394 16.9396L22 15.8789ZM17 29.5002H31V26.5002H17V29.5002ZM17 35.5002H31V32.5002H17V35.5002Z" fill="#A800FF"/>
							</svg>
							<h3><?php esc_html_e("Documentation","tourfic"); ?></h3>
							<p><?php echo esc_html__('Step-by-step guides to help you use Tourfic.', 'tourfic'); ?></p>
							<a href="<?php echo esc_url(Helper::tf_utm_generator( 'https://themefic.com/docs/tourfic/', array( 'utm_medium' => 'get_help_documentation' ) )); ?>" target="_blank" class="tf-link-skip-btn"><?php esc_html_e("Read More","tourfic"); ?></a>
						</div>
						<div class="tf-single-support-card">
							<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M6 36C4.9 36 3.95834 35.6084 3.175 34.825C2.39166 34.0416 2 33.1 2 32V8C2 6.9 2.39166 5.95834 3.175 5.175C3.95834 4.39166 4.9 4 6 4H38C39.1 4 40.0416 4.39166 40.825 5.175C41.6084 5.95834 42 6.9 42 8V24.985C41.6776 24.9504 41.3514 24.9332 41.0222 24.9332C39.1838 24.9332 37.4752 25.502 36.01 26.5794C34.5538 25.4944 32.8506 24.9332 31.0258 24.9332C29.2432 24.9332 27.5512 25.4282 26.0524 26.4152C24.556 27.4006 23.4338 28.7556 22.725 30.3858C21.98 32.0994 21.807 33.93 22.216 35.7522C22.2348 35.8352 22.2544 35.9178 22.275 36H6ZM22 22L38 12V8L22 18L6 8V12L22 22ZM44.5574 37.4666L36.024 46.0002L27.4904 37.4666C26.7996 36.7352 26.3424 35.8716 26.119 34.876C25.8954 33.8806 25.9868 32.9154 26.3932 31.9808C26.7996 31.0462 27.4194 30.3046 28.2524 29.756C29.0854 29.2074 30.0098 28.9332 31.0258 28.9332C32.0416 28.9332 32.9458 29.248 33.7382 29.8778C34.5306 30.5078 35.2924 31.168 36.024 31.8588C36.7148 31.168 37.4664 30.5078 38.2792 29.8778C39.092 29.248 40.0062 28.9332 41.0222 28.9332C42.038 28.9332 42.9524 29.2176 43.765 29.7864C44.5778 30.3554 45.1872 31.1072 45.5936 32.0418C46 32.9764 46.1016 33.9414 45.8984 34.937C45.6952 35.9326 45.2482 36.7758 44.5574 37.4666Z" fill="#27BE69"/>
							</svg>
							<h3><?php esc_html_e("Email Support","tourfic"); ?></h3>
							<p><?php echo esc_html__('Have needs? Our team offers tailored solutions.', 'tourfic'); ?></p>
							<a href="<?php echo esc_url(Helper::tf_utm_generator( 'https://portal.themefic.com/support/', array( 'utm_medium' => 'get_help_support' ) )); ?>" target="_blank" class="tf-link-skip-btn"><?php esc_html_e("Contact Us","tourfic"); ?></a>
						</div>

						<div class="tf-single-support-card">
							<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M5.9 32.6L2 46L15.4 42.1C16.7667 42.7334 18.1667 43.2084 19.6 43.525C21.0334 43.8416 22.5 44 24 44C26.7666 44 29.3666 43.475 31.8 42.425C34.2334 41.375 36.35 39.95 38.15 38.15C39.95 36.35 41.375 34.2334 42.425 31.8C43.475 29.3666 44 26.7666 44 24C44 21.2334 43.475 18.6333 42.425 16.2C41.375 13.7667 39.95 11.65 38.15 9.85C36.35 8.05 34.2334 6.625 31.8 5.575C29.3666 4.525 26.7666 4 24 4C21.2334 4 18.6333 4.525 16.2 5.575C13.7667 6.625 11.65 8.05 9.85 9.85C8.05 11.65 6.625 13.7667 5.575 16.2C4.525 18.6333 4 21.2334 4 24C4 25.5 4.15834 26.9666 4.475 28.4C4.79166 29.8334 5.26666 31.2334 5.9 32.6ZM17.425 25.425C17.0417 25.8084 16.5667 26 16 26C15.4333 26 14.9583 25.8084 14.575 25.425C14.1917 25.0416 14 24.5666 14 24C14 23.4334 14.1917 22.9584 14.575 22.575C14.9583 22.1916 15.4333 22 16 22C16.5667 22 17.0417 22.1916 17.425 22.575C17.8083 22.9584 18 23.4334 18 24C18 24.5666 17.8083 25.0416 17.425 25.425ZM25.425 25.425C25.0416 25.8084 24.5666 26 24 26C23.4334 26 22.9584 25.8084 22.575 25.425C22.1916 25.0416 22 24.5666 22 24C22 23.4334 22.1916 22.9584 22.575 22.575C22.9584 22.1916 23.4334 22 24 22C24.5666 22 25.0416 22.1916 25.425 22.575C25.8084 22.9584 26 23.4334 26 24C26 24.5666 25.8084 25.0416 25.425 25.425ZM32 26C32.5666 26 33.0416 25.8084 33.425 25.425C33.8084 25.0416 34 24.5666 34 24C34 23.4334 33.8084 22.9584 33.425 22.575C33.0416 22.1916 32.5666 22 32 22C31.4334 22 30.9584 22.1916 30.575 22.575C30.1916 22.9584 30 23.4334 30 24C30 24.5666 30.1916 25.0416 30.575 25.425C30.9584 25.8084 31.4334 26 32 26Z" fill="#295BFF"/>
							</svg>
							<h3><?php esc_html_e("Live Chat","tourfic"); ?></h3>
							<p><?php echo esc_html__('Need help? Chat with our support team directly.', 'tourfic'); ?></p>
							<a href="<?php echo esc_url(Helper::tf_utm_generator( 'https://themefic.com/tourfic/', array( 'utm_medium' => 'get_help_live_chat' ) )); ?>" target="_blank" class="tf-link-skip-btn"><?php esc_html_e("Chat Now","tourfic"); ?></a>
						</div>

						<div class="tf-single-support-card">
							<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M12 16H18L14 8H18L22 16H28L24 8H28L32 16H38L34 8H40C41.1 8 42.0416 8.39166 42.825 9.175C43.6084 9.95834 44 10.9 44 12V24.5094C41.7324 22.9276 38.9744 22 36 22C28.268 22 22 28.268 22 36C22 37.39 22.2026 38.7326 22.5798 40H8C6.9 40 5.95834 39.6084 5.175 38.825C4.39166 38.0416 4 37.1 4 36V12C4 10.9 4.39166 9.95834 5.175 9.175C5.95834 8.39166 6.9 8 8 8L12 16ZM46 36C46 41.5228 41.5228 46 36 46C30.4772 46 26 41.5228 26 36C26 30.4772 30.4772 26 36 26C41.5228 26 46 30.4772 46 36ZM33.5 31L41.5 36L33.5 41V31Z" fill="#BE277C"/>
							</svg>
							<h3><?php esc_html_e("Video Tutorials","tourfic"); ?></h3>
							<p><?php echo esc_html__('Watch videos that walk you through Tourfic.', 'tourfic'); ?></p>
							<a href="<?php echo esc_url(Helper::tf_utm_generator( 'https://www.youtube.com/playlist?list=PLY0rtvOwg0ylCl7NTwNHUPq-eY1qwUH_N', array( 'utm_medium' => 'get_help_youtube' ) )); ?>" target="_blank" class="tf-link-skip-btn"><?php esc_html_e("Watch Video","tourfic"); ?></a>
						</div>
					</div>

					<div class="tf-settings-faq">
						<h2><?php esc_html_e("FAQ's","tourfic"); ?></h2>

						<div class="tf-accordion-wrapper">
							<div class="tf-accrodian-item">
								<div class="tf-single-faq">
									<div class="tf-faq-title">
										<h4><?php esc_html_e("What is Tourfic? ","tourfic"); ?></h4>
										<i class="fas fa-angle-down"></i>
									</div>
									<div class="tf-faq-desc">
										<p><?php esc_html_e("Tourfic is the ultimate WordPress travel plugin for hotel booking, tour operator and travel agency websites.","tourfic"); ?></p>
									</div>
								</div>
							</div>
							<div class="tf-accrodian-item">
								<div class="tf-single-faq">
									<div class="tf-faq-title">
										<h4><?php esc_html_e("How to install Tourfic? ","tourfic"); ?></h4>
										<i class="fas fa-angle-down"></i>
									</div>
									<div class="tf-faq-desc">
										<p><?php esc_html_e("Please check our documentations","tourfic"); ?></p>
									</div>
								</div>
							</div>
							<div class="tf-accrodian-item">
								<div class="tf-single-faq">
									<div class="tf-faq-title">
										<h4><?php esc_html_e("Is Free version fully free or there is a gap? ","tourfic"); ?></h4>
										<i class="fas fa-angle-down"></i>
									</div>
									<div class="tf-faq-desc">
										<p>
										<?php esc_html_e("Yes, Tourfic is fully free which is available on WordPress.org. This free version will always be free. It also has a pro version with additional features which you can purchase from our official website.","tourfic"); ?>
										</p>
									</div>
								</div>
							</div>
							<div class="tf-accrodian-item">
								<div class="tf-single-faq">
									<div class="tf-faq-title">
										<h4><?php esc_html_e("Can I create a hotel booking website with Tourfic? ","tourfic"); ?></h4>
										<i class="fas fa-angle-down"></i>
									</div>
									<div class="tf-faq-desc">
										<p>
										<?php esc_html_e("Yes, You create your own professional hotel booking website easily with tourfic.","tourfic"); ?>
										</p>
									</div>
								</div>
							</div>
							<div class="tf-accrodian-item">
								<div class="tf-single-faq">
									<div class="tf-faq-title">
										<h4><?php esc_html_e("Can I create a travel or tour booking website with Tourfic? ","tourfic"); ?></h4>
										<i class="fas fa-angle-down"></i>
									</div>
									<div class="tf-faq-desc">
										<p>
										<?php esc_html_e("Yes, You create your own professional travel or tour booking website easily with tourfic.","tourfic"); ?>
										</p>
									</div>
								</div>
							</div>
							<div class="tf-accrodian-item">
								<div class="tf-single-faq">
									<div class="tf-faq-title">
										<h4><?php esc_html_e("Can Tourfic be used as WooCommerce Accommodation Bookings? ","tourfic"); ?></h4>
										<i class="fas fa-angle-down"></i>
									</div>
									<div class="tf-faq-desc">
										<p>
										<?php esc_html_e("Yes, You create your own professional accommodation booking website easily with tourfic.","tourfic"); ?>
										</p>
									</div>
								</div>
							</div>
							<div class="tf-accrodian-item">
								<div class="tf-single-faq">
									<div class="tf-faq-title">
										<h4><?php esc_html_e("Can I create a website similar to Booking.com with Tourfic? ","tourfic"); ?></h4>
										<i class="fas fa-angle-down"></i>
									</div>
									<div class="tf-faq-desc">
										<p>
										<?php esc_html_e("Yes, You can create your own professional tour operator and travel agency website within 5 minutes, just like Booking.com, Agoda, Hotels.com, Airbnb etc.","tourfic"); ?>
										</p>
									</div>
								</div>
							</div>
							<div class="tf-accrodian-item">
								<div class="tf-single-faq">
									<div class="tf-faq-title">
										<h4><?php esc_html_e("Is free version supported? ","tourfic"); ?></h4>
										<i class="fas fa-angle-down"></i>
									</div>
									<div class="tf-faq-desc">
										<p>
										<?php esc_html_e("Yes, We provide full support on the WordPress.org forums. You can also post questions or bug reports through our Facebook group! or our website. However, please note that, for free version’s support/replies, there can be delays upto 24-48 hours.","tourfic"); ?>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Options Page
		 * @author Foysal
		 */
		public function tf_options_page() {

			// Retrieve an existing value from the database.
			$tf_option_value = get_option( $this->option_id );

			// Set default values.
			if ( empty( $tf_option_value ) ) {
				$tf_option_value = array();
			}

            $ajax_save_class = 'tf-ajax-save';

			$tf_disable_services = ! empty( Helper::tfopt( 'disable-services' ) ) ? Helper::tfopt( 'disable-services' ) : [];

			if ( ! empty( $this->option_sections ) ) :
				?>
				<div class="tf-setting-dashboard">
				<!-- dashboard-header-include -->
				<?php \Tourfic\Classes\Helper::tf_dashboard_header(); ?>

                <div class="tf-option-wrapper tf-setting-wrapper">

					<!-- Settings Header -->
					<div class="tourfic-settings-header">
						<div class="settings-header-left">
							<h2 class="tf-setting-title"><?php echo esc_html__( "Tourfic Settings", "tourfic" ); ?></h2>
							<div class="tf-setting-search">
								<i class="fa-solid fa-search"></i>
								<div class="search-input">
									<input aria-label="Search" id="tf-settings-header-search-filed" type="text" placeholder="<?php echo esc_attr__( "Search Options", "tourfic" ); ?>" class="ui-autocomplete-input" autocomplete="off">
								</div>
							</div>
							
						</div>
						<div class="settings-header-right">
							<div class="tf-setting-save-btn">
								<button type="submit" class="tf-admin-btn tf-btn-secondary tf-submit-btn"><?php echo esc_html__( "Save", "tourfic" ); ?></button>
								<button type="submit" class="tf-admin-btn tf-btn-secondary tf-reset-btn"><?php echo esc_html__( "Reset", "tourfic" ); ?></button>
							</div>
						</div>
					</div>
					<!-- Search Results Container -->
                    <form method="post" action="" class="tf-option-form <?php echo esc_attr($ajax_save_class) ?>" enctype="multipart/form-data">
                        <!-- Body -->
                        <div class="tf-option">
                            <div class="tf-admin-tab tf-option-nav">
								<?php
								$section_count = 0;
								foreach ( $this->pre_tabs as $key => $section ) :
									$parent_tab_key = ! empty( $section['fields'] ) ? $key : array_key_first( $section['sub_section'] );
									
									if(isset( $section['post_dependency'] ) && !empty( $section['post_dependency'] )){
										if(!empty( $tf_disable_services ) && in_array( $section['post_dependency'], $tf_disable_services )){
											continue;
										}
									}
									?>
                                    <div class="tf-admin-tab-item<?php echo ! empty( $section['sub_section'] ) ? ' tf-has-submenu' : '' ?>">

                                        <a href="#<?php echo esc_attr( $parent_tab_key ); ?>"
                                           class="tf-tablinks <?php echo esc_attr($section_count == 0 ? 'active' : ''); ?>"
                                           data-tab="<?php echo esc_attr( $parent_tab_key ) ?>">
											<?php echo ! empty( $section['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $section['icon'] ) . '"></i></span>' : ''; ?>
											<?php echo esc_html($section['title']); ?>
                                        </a>

										<?php if ( ! empty( $section['sub_section'] ) ): ?>
                                            <ul class="tf-submenu">
												<?php foreach ( $section['sub_section'] as $sub_key => $sub ): ?>
                                                    <li>
                                                        <a href="#<?php echo esc_attr( $sub_key ); ?>"
                                                           class="tf-tablinks <?php echo esc_attr($section_count == 0 ? 'active' : ''); ?>"
                                                           data-tab="<?php echo esc_attr( $sub_key ) ?>">
														<span class="tf-tablinks-inner">
                                                            <?php echo ! empty( $sub['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $sub['icon'] ) . '"></i></span>' : ''; ?>
                                                            <?php echo esc_html($sub['title']); ?>
                                                        </span>
                                                        </a>
                                                    </li>
												<?php endforeach; ?>
                                            </ul>
										<?php endif; ?>
                                    </div>
									<?php $section_count ++; endforeach; ?>
                            </div>

                            <div class="tf-tab-wrapper">
								<div class="tf-mobile-setting">
									<a href="#" class="tf-mobile-tabs"><i class="fa-solid fa-bars"></i></a>
								</div>
								<?php
								$content_count = 0;
								foreach ( $this->option_sections as $key => $section ) : ?>
                                    <div id="<?php echo esc_attr( $key ) ?>" class="tf-tab-content <?php echo esc_attr($content_count == 0 ? 'active' : ''); ?>">

										<?php
										if ( ! empty( $section['fields'] ) ):
											foreach ( $section['fields'] as $field ) :

												$default = isset( $field['default'] ) ? $field['default'] : '';
												$value   = isset( $tf_option_value[ $field['id'] ] ) ? $tf_option_value[ $field['id'] ] : $default;

												$tf_option = new \Tourfic\Admin\TF_Options\TF_Options();
												$tf_option->field( $field, $value, $this->option_id );

											endforeach;
										endif; ?>

                                    </div>
									<?php $content_count ++; endforeach; ?>

									<!-- Footer -->
									<div class="tf-option-footer">
										<button type="submit" class="tf-admin-btn tf-btn-secondary tf-submit-btn"><?php esc_html_e( 'Save', 'tourfic' ); ?></button>
									</div>
                            </div>
                        </div>
						<?php wp_nonce_field( 'tf_option_nonce_action', 'tf_option_nonce' ); ?>
                    </form>
                </div>

                <div class="tf-field-notice-inner tf-notice-danger" style="margin-top: 20px; margin-right: 20px;">
                    <div class="tf-field-notice-content">
                        <?php /* translators: %s: strong tag */ ?>
						<?php echo sprintf( esc_html__( 'Note: If you are having trouble saving your settings, please increase the %1$s "PHP Max Input Vars" %2$s value to save all settings. Contact your hosting provider for help on this matter. Otherwise, you will not be able to save all settings.', 'tourfic' ), '<strong>', '</strong>' ); ?>
                    </div>
                </div>
			<?php
			endif;
		}

		function count_input_vars($array) {
			$count = 0;
			foreach ($array as $key => $value) {
				$count++;
				if (is_array($value)) {
					$count += $this->count_input_vars($value);
				}
			}
			return $count;
		}

		/**
		 * Save Options
		 * @author Foysal
		 */
		public function save_options() {

			// Check if a nonce is valid.
			if (  !isset( $_POST['tf_option_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tf_option_nonce'] ) ), 'tf_option_nonce_action' ) ) {
				return;
			}

			//  Checked Currenct can save option
			$current_user = wp_get_current_user();
			$current_user_role = $current_user->roles[0];

			if ( $current_user_role !== 'administrator' && !is_admin()) {
				wp_die( 'You do not have sufficient permissions to access this page.' );
			}

			$tf_option_value = array();
			$option_request = array();
			if ( ! empty( $_POST[ $this->option_id ] ) ) {
				$option_request = $this->recursive_sanitize( wp_unslash( $_POST[ $this->option_id ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			}

			if ( ! empty( $option_request ) && ! empty( $this->option_sections ) ) {
				foreach ( $this->option_sections as $section ) {
					if ( ! empty( $section['fields'] ) ) {

						foreach ( $section['fields'] as $field ) {

							if ( ! empty( $field['id'] ) ) {

								$fieldClass = 'TF_' . $field['type'];

								if($fieldClass == 'TF_tab'){
									$data = isset( $option_request[ $field['id'] ] ) ? $option_request[ $field['id'] ] : '';
									foreach ( $field['tabs'] as $tab ) {
										foreach ( $tab['fields'] as $tab_fields ) {
											if($tab_fields['type'] == 'repeater') {
												foreach ( $tab_fields['fields'] as $key => $tab_field ) {
													if ( isset( $tab_field['validate'] ) && $tab_field['validate'] == 'no_space_no_special' ) {
														$sanitize_data_array = [];
														if(!empty($data[$tab_fields['id']])){
															foreach ( $data[$tab_fields['id']] as $_key=> $datum ) {
																//unique id 3 digit
																$unique_id = substr(uniqid(), -3);
																$sanitize_data = sanitize_title(str_replace(' ', '_', strtolower($datum[$tab_field['id']])));
																if(in_array($sanitize_data, $sanitize_data_array)){
																	$sanitize_data = $sanitize_data . '_' . $unique_id;
																} else {
																	$sanitize_data_array[] = $sanitize_data;
																}

																$data[$tab_fields['id']][$_key][$tab_field['id']] = $sanitize_data;
															}
														}
													}
												}
											}
										}
									}
								} else {
									$data = isset( $option_request[ $field['id'] ] ) ? $option_request[ $field['id'] ] : '';
								}

								if($fieldClass != 'TF_file'){
									$data       = $fieldClass == 'TF_repeater' || $fieldClass == 'TF_map'  || $fieldClass == 'TF_color' ? serialize( $data ) : $data;
								}
								if(isset($_FILES) && !empty($_FILES['file'])){
									$tf_upload_dir = wp_upload_dir();
									if ( ! empty( $tf_upload_dir['basedir'] ) ) {
										$tf_itinerary_fonts = $tf_upload_dir['basedir'].'/itinerary-fonts';
										if ( ! file_exists( $tf_itinerary_fonts ) ) {
											wp_mkdir_p( $tf_itinerary_fonts );
										}
										// extension want to allow
										$allowed_ext = array('ttf', 'otf', 'woff', 'woff2', 'eot');
										$allowed_mime_types = array('application/octet-stream', 'font/ttf', 'font/otf', 'font/woff', 'font/woff2', 'application/vnd.ms-fontobject');
										$file_name = !empty($_FILES['file']['name']) ? sanitize_file_name(wp_unslash($_FILES['file']['name'])) : [];
										$file_tmp_name = !empty($_FILES['file']['tmp_name']) ? sanitize_file_name(wp_unslash($_FILES['file']['tmp_name'])) : [];
										for($i = 0; $i < count($file_name); $i++) {
											
											$tf_font_filename = sanitize_file_name( wp_unslash($file_name[$i]) );
											$uploaded_file_tmp = sanitize_file_name( wp_unslash($file_tmp_name[$i]) );
											$checked = wp_check_filetype_and_ext( $uploaded_file_tmp, $tf_font_filename);
											if (isset($checked['ext']) && in_array($checked["ext"], $allowed_ext) && in_array($checked['type'], $allowed_mime_types)) {
												$destination_path = $tf_itinerary_fonts .'/'. $tf_font_filename;
												if (copy($uploaded_file_tmp, $destination_path)) {
													// File copied successfully, you can perform further actions if needed
												} else {
													// Handle error if copy operation failed
												}
											} else {
												// Invalid file type or extension
												$response    = [
													'status'  => 'error',
													'message' => esc_html__( 'Invalid file type or extension', 'tourfic' ),
												];
												echo wp_json_encode($response);
												wp_die();
											}
										}
									}
								}

								if ( class_exists( $fieldClass ) ) {
									$_field                          = new $fieldClass( $field, $data, $this->option_id );
									$tf_option_value[ $field['id'] ] = $_field->sanitize();
								}

							}
						}
					}
				}
			}

			if ( ! empty( $tf_option_value ) ) {
				update_option( $this->option_id, $tf_option_value );
			} else {
				delete_option( $this->option_id );
			}
		}

		/*
		 * Ajax Save Options
		 * @author Foysal
		 */
		public function tf_ajax_save_options() {
			$response    = [
				'status'  => 'error',
				'message' => esc_html__( 'Something went wrong!', 'tourfic' ),
			];

			// Check if a nonce is valid.
			if (  !isset( $_POST['tf_option_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tf_option_nonce'] ) ), 'tf_option_nonce_action' ) ) {
				return;
			}

			// Check if the current user has the required capability.
	        if (!current_user_can('manage_options')) {
		        $response['status'] = 'error';
		        $response['message'] = esc_html__('You do not have permission to access this resource.', 'tourfic');
		        echo wp_json_encode($response);
                die();
	        }

			
			$this->save_options();
			$response = [
				'status'  => 'success',
				'message' => esc_html__( 'Options saved successfully!', 'tourfic' ),
			];

			do_action("tourfic_settings_save_hook");

			echo wp_json_encode( $response );
			wp_die();
		}


		public function tf_ajax_reset_options() {
			$response    = [
				'status'  => 'error',
				'message' => esc_html__( 'Something went wrong!', 'tourfic' ),
			];

			// Check if a nonce is valid.
			if (  !isset( $_POST['tf_option_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tf_option_nonce'] ) ), 'tf_option_nonce_action' ) ) {
				return;
			}

			// Check if the current user has the required capability.
	        if (!current_user_can('manage_options')) {
		        $response['status'] = 'error';
		        $response['message'] = esc_html__('You do not have permission to access this resource.', 'tourfic');
		        echo wp_json_encode($response);
                die();
	        }

			if( !empty( get_option( 'tf_settings' ) ) ) {
				update_option( 'tf_settings', '' );
				$response = [
					'status'  => 'success',
					'message' => esc_html__( 'Options Reset successfully!', 'tourfic' ),
				];
			} else {
				$response    = [
					'status'  => 'error',
					'message' => esc_html__( 'Settings are fresh, nothing to reset.', 'tourfic' ),
				];
			}

			echo wp_json_encode( $response );
			wp_die();
		}

		public function tf_search_settings_autocomplete_callback() {
			// Check if a nonce is valid.
			if (  !isset( $_POST['tf_option_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tf_option_nonce'] ) ), 'tf_option_nonce_action' ) ) {
				return;
			}

			// Check if the current user has the required capability.
	        if (!current_user_can('manage_options')) {
		        $response['status'] = 'error';
		        $response['message'] = esc_html__('You do not have permission to access this resource.', 'tourfic');
		        echo wp_json_encode($response);
                die();
	        }
			
			$all_settings = $this->pre_tabs;
			$fields = [];
			$path = '';

			foreach ( $all_settings as $section => $data ) {

				$parent = $parent_title = '';
				$icon = $data['icon'];

				if( !empty( $data["fields"]) ) {
					$path = $data['title'];
					foreach ( $data["fields"] as $field ) {

						if ( !empty( $field['tabs'] )) {
							foreach( $field['tabs'] as $key => $tab) {
								
								if ( !empty( $tab['fields'] )) {
									foreach ( $tab['fields'] as $tab_field ) {
										$fields[] = array(
											'parent' => $parent_title,
											'parent_id' => $section,
											'tab_id' => $tab['id'] ? $tab['id'] : '',
											'field_title' => !empty( $tab_field["label"] ) ? $tab_field["label"] : ( !empty( $tab_field['title'] ) ? $tab_field['title'] : ( !empty( $tab_field['heading'] ) ?  !empty( $tab_field['heading'] ) : ''  )),
											'section' => $tab['title'],
											'icon' => $icon,
											'path' => $path,
											'id' => $tab_field['id'],
										);
									}
								}

							}
						}

						$fields[] = array(
							'parent' => $parent_title,
							'parent_id' => $section,
							'field_title' => !empty( $field["label"] ) ? $field["label"] : ( !empty( $field['title'] ) ? $field['title'] : ( !empty( $field['heading'] ) ?  !empty( $field['heading'] ) : ''  )),
							'section' => $data['title'],
							'icon' => $icon,
							'path' => $path,
							'id' => $field['id'],
						);
					}
				}

				if( !empty( $data["sub_section"])) {
					foreach ( $data["sub_section"] as $key => $sub_section ) {

						$parent_id = $key;

						if( isset( $sub_section["parent"] )) {
							$parent = $sub_section["parent"];
							$parent = !empty($parent) ? $all_settings[$parent] : '';
							$parent_title = !empty($parent) ? $parent['title'] : '';
							$icon = !empty($parent) ? $parent['icon'] : $data['icon'];
						}

						!empty( $parent_title ) ? $path = $parent_title . ' > ' . $sub_section['title'] : $path = $sub_section[$key]['title'];
						if ( !empty( $sub_section["fields"])) {

							foreach ( $sub_section["fields"] as $field ) {

								if ( !empty( $field['tabs'] )) {
									foreach( $field['tabs'] as $key => $tab) {
										
										if ( !empty( $tab['fields'] )) {
											foreach ( $tab['fields'] as $tab_field ) {
												$fields[] = array(
													'parent' => $parent_title,
													'parent_id' => $parent_id,
													'tab_id' => $tab['id'] ? $tab['id'] : '',
													'field_title' => !empty( $tab_field["label"] ) ? $tab_field["label"] : ( !empty( $tab_field['title'] ) ? $tab_field['title'] : ( !empty( $tab_field['heading'] ) ?  !empty( $tab_field['heading'] ) : ''  )),
													'section' => $tab['title'],
													'icon' => $icon,
													'path' => $path,
													'id' => $tab_field['id'],
												);
											}
										}
	
									}
								}
								$fields[] = array(
									'parent' => $parent_title,
									'parent_id' => $parent_id,
									'field_title' => !empty( $field["label"] ) ? $field["label"] : ( !empty( $field['title'] ) ? $field['title'] : ( !empty( $field['heading'] ) ?  !empty( $field['heading'] ) : ''  )),
									'section' => $data['title'],
									'icon' => $icon,
									'path' => $path,
									'id' => $field['id'],
								);
							}
						} 
					}
				}
			}

			$response = [
				'status'  => 'success',
				'message' => $fields,
			];

			echo wp_json_encode( $response );
			wp_die();
		}

        /*
         * Get query string from url
         * @return array
         * @author Foysal
         */
        public function get_query_string( $url ) {
	        $url_parts = wp_parse_url( $url );
	        parse_str( $url_parts['query'], $query_string );

            return $query_string;
        }

        function tf_export_data(){
	        // Add nonce for security and authentication.
	        check_ajax_referer( 'updates', '_nonce' );

            $response = array(
                'status' => 'error',
                'message' => 'Something went wrong!'
            );

	        // Check if the current user has the required capability.
	        if (!current_user_can('manage_options')) {
		        $response['status'] = 'error';
		        $response['message'] = esc_html__('You do not have permission to access this resource.', 'tourfic');
		        echo wp_json_encode($response);
                die();
	        }

	        $current_settings = get_option( $this->option_id );
	        $response['data'] = isset($current_settings) && !empty($current_settings) ? wp_json_encode($current_settings) : '';

            if(!empty($response['data'])){
                $response['status'] = 'success';
                $response['message'] = 'Data exported successfully!';
            }
            echo wp_json_encode($response);
            die();
        }

		/**
		 * Recursively sanitize an array or a scalar value.
		 *
		 * @param mixed $data
		 * @return mixed
		 */
		private function recursive_sanitize( $data ) {
			if ( is_array( $data ) ) {
				return array_map( array( $this, 'recursive_sanitize' ), $data );
			}

			// Default sanitization for scalar values
			return sanitize_text_field( wp_unslash( $data ) );
		}
	}
}
