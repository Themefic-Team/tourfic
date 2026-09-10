<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use \Tourfic\Classes\Helper;

if ( ! class_exists( 'Tourfic_Settings' ) ) {
	class Tourfic_Settings {

		public $option_id = null;
		public $menu_slug = null;
		public $option_title = null;
		public $option_icon = null;
		public $option_position = null;
		public $option_sections = array();
		public $pre_tabs;
		public $pre_fields;
		public $pre_sections;

		public function __construct( $key, $params = array() ) {
			if ( 0 === strpos( $key, 'tourfic_' ) ) {
				$hook_key = substr( $key, 8 );
			} elseif ( 0 === strpos( $key, 'tf_' ) ) {
				$hook_key = substr( $key, 3 );
			} else {
				$hook_key = $key;
			}

			$this->option_id       = $key;
			$this->menu_slug       = ! empty( $params['menu_slug'] ) ? sanitize_key( $params['menu_slug'] ) : $key;
			$this->option_title    = ! empty( $params['title'] ) ? apply_filters( 'tourfic_' . $hook_key . '_title', $params['title'] ) : '';
			$this->option_icon     = ! empty( $params['icon'] ) ? apply_filters( 'tourfic_' . $hook_key . '_icon', $params['icon'] ) : '';
			$this->option_position = ! empty( $params['position'] ) ? apply_filters( 'tourfic_' . $hook_key . '_position', $params['position'] ) : 5;
			$this->option_sections = ! empty( $params['sections'] ) ? apply_filters( 'tourfic_' . $hook_key . '_sections', $params['sections'] ) : array();

			// run only is admin panel options, avoid performance loss
			$this->pre_tabs     = $this->pre_tabs( $this->option_sections );
			$this->pre_fields   = $this->pre_fields( $this->option_sections );
			$this->pre_sections = $this->pre_sections( $this->option_sections );

			//options
			add_action( 'admin_menu', array( $this, 'tf_options' ) );

			//save options
			add_action( 'admin_init', array( $this, 'save_options' ) );

			//ajax save options
			add_action( 'wp_ajax_tourfic_options_save', array( $this, 'tf_ajax_save_options' ) );
			add_action( 'wp_ajax_tourfic_options_reset', array( $this, 'tf_ajax_reset_options' ) );
			add_action( 'wp_ajax_tourfic_search_settings_autocomplete', array( $this, 'tf_search_settings_autocomplete_callback' ) );

            add_action( 'wp_ajax_tourfic_export_data', array( $this, 'tf_export_data' ) );
			
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
				$this->menu_slug,
				array( $this, 'tf_options_page' ),
				$this->option_icon,
				$this->option_position
			);

            //Dashboard submenu
			add_submenu_page(
				$this->menu_slug,
				esc_html__('Dashboard', 'tourfic'),
				esc_html__('Dashboard', 'tourfic'),
				'manage_options',
				'tourfic_dashboard',
				array( $this, 'tf_dashboard_page' ),
			);

			//Setting submenu
			add_submenu_page(
				$this->menu_slug,
				esc_html__('Settings', 'tourfic'),
				esc_html__('Settings', 'tourfic'),
				'manage_options',
				$this->menu_slug . '#tab=general',
				array( $this, 'tf_options_page' ),
			);

			// Shortcode submenu
			add_submenu_page(
				$this->menu_slug,
				esc_html__('Shortcodes', 'tourfic'),
				esc_html__('Shortcodes', 'tourfic'),
				'manage_options',
				'tourfic_shortcodes',
				array( 'Tourfic_Shortcodes','tf_shortcode_callback'),
			);

			//Get Help submenu
			add_submenu_page(
				$this->menu_slug,
				esc_html__('Get Help', 'tourfic'),
				esc_html__('Get Help', 'tourfic'),
				'manage_options',
				'tourfic_get_help',
				array( $this,'tf_get_help_callback'),
			);

			// Library submenu
			if ( is_plugin_active( 'travelfic-toolkit/travelfic-toolkit.php' ) ) {
				$library_url = admin_url( 'admin.php?page=travelfic-template-list' );
				add_submenu_page(
					$this->menu_slug,
					esc_html__('Template Library', 'tourfic'),
					esc_html__('Template Library', 'tourfic'),
					'manage_options',
					$library_url,
					'',
					3
				);
			}
			// remove first submenu
			remove_submenu_page( $this->menu_slug, $this->menu_slug );

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

					<div class="tf-setting-performace-section">
						<div class="tf-report-wrapper">
							<div class="tf-setting-overview-section">
								<h2><?php esc_html_e("Overview","tourfic"); ?></h2>
								<div class="tf-performance-grid">

									<div class="tf-single-performance-grid">
										<div class="tf-single-performance-content">
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
											<p>
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<g clip-path="url(#clip0_2021_21400)">
													<path d="M12.0002 14C12.0002 12.5855 11.4383 11.229 10.4381 10.2288C9.43787 9.22857 8.08132 8.66667 6.66683 8.66667M6.66683 8.66667C5.25234 8.66667 3.89579 9.22857 2.89559 10.2288C1.8954 11.229 1.3335 12.5855 1.3335 14M6.66683 8.66667C8.50778 8.66667 10.0002 7.17428 10.0002 5.33333C10.0002 3.49238 8.50778 2 6.66683 2C4.82588 2 3.3335 3.49238 3.3335 5.33333C3.3335 7.17428 4.82588 8.66667 6.66683 8.66667ZM14.6668 13.3333C14.6668 11.0867 13.3335 9 12.0002 8C12.4384 7.67118 12.7889 7.23939 13.0205 6.74285C13.2522 6.2463 13.3578 5.70031 13.3282 5.1532C13.2985 4.60609 13.1344 4.07472 12.8505 3.60613C12.5665 3.13754 12.1714 2.74617 11.7002 2.46667" stroke="#7D8FA0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</g>
												<defs>
													<clipPath id="clip0_2021_21400">
													<rect width="16" height="16" fill="white"/>
													</clipPath>
												</defs>
												</svg>
												<span><?php esc_html_e("Total Customers","tourfic"); ?></span>
											</p>
										</div>
									</div>

									<div class="tf-single-performance-grid">
										<div class="tf-single-performance-content">
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
											<p>
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M5.33333 1.33301V3.99967M10.6667 1.33301V3.99967M14 9.33301V3.99967C14 3.64605 13.8595 3.30691 13.6095 3.05687C13.3594 2.80682 13.0203 2.66634 12.6667 2.66634H3.33333C2.97971 2.66634 2.64057 2.80682 2.39052 3.05687C2.14048 3.30691 2 3.64605 2 3.99967V13.333C2 13.6866 2.14048 14.0258 2.39052 14.2758C2.64057 14.5259 2.97971 14.6663 3.33333 14.6663H8.66667M2 6.66634H14M10.6667 13.333L12 14.6663L14.6667 11.9997" stroke="#7D8FA0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
												<span><?php esc_html_e("Total Bookings","tourfic"); ?></span>
											</p>
										</div>
									</div>

									<div class="tf-single-performance-grid">
										<div class="tf-single-performance-content">
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
											<p>
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M6.6665 14.6663V10.2863M7.99984 7.33301H8.0065M7.99984 4.66634H8.0065M9.33317 10.2863V14.6663M9.99984 10.6663C9.42285 10.2336 8.72107 9.99967 7.99984 9.99967C7.2786 9.99967 6.57682 10.2336 5.99984 10.6663M10.6665 7.33301H10.6732M10.6665 4.66634H10.6732M5.33317 7.33301H5.33984M5.33317 4.66634H5.33984M3.99984 1.33301H11.9998C12.7362 1.33301 13.3332 1.92996 13.3332 2.66634V13.333C13.3332 14.0694 12.7362 14.6663 11.9998 14.6663H3.99984C3.26346 14.6663 2.6665 14.0694 2.6665 13.333V2.66634C2.6665 1.92996 3.26346 1.33301 3.99984 1.33301Z" stroke="#7D8FA0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>	
												<span><?php esc_html_e("Total Hotels","tourfic"); ?></span>
											</p>
										</div>
									</div>

									<div class="tf-single-performance-grid">
										<div class="tf-single-performance-content">
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
											<p>
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M10 3.84247C9.79312 3.84247 9.58907 3.79433 9.404 3.70185L6.596 2.29785C6.41094 2.20537 6.20689 2.15723 6 2.15723M10 3.84247C10.2069 3.84247 10.4109 3.79433 10.596 3.70185L13.0353 2.48185C13.1371 2.43102 13.2501 2.40705 13.3637 2.41223C13.4773 2.4174 13.5876 2.45154 13.6843 2.5114C13.781 2.57126 13.8607 2.65486 13.916 2.75424C13.9713 2.85361 14.0002 2.96547 14 3.07918V11.5885C13.9999 11.7123 13.9654 11.8336 13.9003 11.9389C13.8352 12.0441 13.7421 12.1292 13.6313 12.1845L10.596 13.7025C10.4109 13.795 10.2069 13.8431 10 13.8431M10 3.84247V13.8431M10 13.8431C9.79312 13.8431 9.58907 13.795 9.404 13.7025L6.596 12.2985C6.41094 12.206 6.20689 12.1579 6 12.1579C5.79312 12.1579 5.58907 12.206 5.404 12.2985L2.96467 13.5185C2.8629 13.5694 2.74982 13.5933 2.63617 13.5881C2.52253 13.5829 2.41211 13.5487 2.31541 13.4888C2.21872 13.4288 2.13898 13.3452 2.08377 13.2457C2.02856 13.1462 1.99972 13.0343 2 12.9205V4.41185C2.00007 4.28807 2.03459 4.16676 2.0997 4.0615C2.16482 3.95623 2.25795 3.87118 2.36867 3.81585L5.404 2.29785C5.58907 2.20537 5.79312 2.15723 6 2.15723M6 2.15723V12.1572" stroke="#7D8FA0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
												<span><?php esc_html_e("Total Tours","tourfic"); ?></span>
											</p>
										</div>
									</div>

									<div class="tf-single-performance-grid">
										<div class="tf-single-performance-content">
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
											<p>
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M10 14V8.66666C10 8.48985 9.92976 8.32028 9.80474 8.19526C9.67971 8.07023 9.51014 7.99999 9.33333 7.99999H6.66667C6.48986 7.99999 6.32029 8.07023 6.19526 8.19526C6.07024 8.32028 6 8.48985 6 8.66666V14M2 6.66666C1.99995 6.47271 2.04222 6.28108 2.12386 6.10514C2.20549 5.9292 2.32453 5.77319 2.47267 5.64799L7.13933 1.64799C7.37999 1.4446 7.6849 1.33301 8 1.33301C8.3151 1.33301 8.62001 1.4446 8.86067 1.64799L13.5273 5.64799C13.6755 5.77319 13.7945 5.9292 13.8761 6.10514C13.9578 6.28108 14 6.47271 14 6.66666V12.6667C14 13.0203 13.8595 13.3594 13.6095 13.6095C13.3594 13.8595 13.0203 14 12.6667 14H3.33333C2.97971 14 2.64057 13.8595 2.39052 13.6095C2.14048 13.3594 2 13.0203 2 12.6667V6.66666Z" stroke="#7D8FA0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
												<span><?php esc_html_e("Total Apartments","tourfic"); ?></span>
											</p>
										</div>
									</div>

								</div>
							</div>
							<div id="tf-report-loader">
								<img src="<?php echo esc_url(TOURFIC_ASSETS_APP_URL.'images/loader.gif'); ?>" alt="Loader">
							</div>
							<div class="tf-report-filter">
								<h2><?php esc_html_e("Reports","tourfic"); ?></h2>

								<?php if(class_exists('WooCommerce')): ?>
									<div class="tf-dates-filter">
										<div class="tf-month-filter">
											<select name="tf-year-report" id="tf-year-report">
												<?php
												$current_year = (int) gmdate('Y'); 
												for ( $i = 0; $i <= 5; $i++ ) {
													$year = $current_year - $i;
													$value = substr( $year, -2 ); // last 2 digits
													?>
													<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $year ); ?></option>
													<?php
												}
												?>
											</select>
										</div>

										<div class="tf-month-filter">
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
							<?php $this->tf_settings_sidebar(); ?>
						</div>
					</div>
				</div>
			</div>

			<?php
		}

		public function tf_settings_sidebar() {
			?>
			<div class="tf-sidebar-content">

				<?php if( !empty($_GET['page']) && $_GET['page']!='tourfic_dashboard' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
				<div class="tf-plugin-lists">
					<h3>Power up your website</h3>
					<div class="tf-others-plugin">
						<?php $this->tf_get_sidebar_plugin_list(); ?>
					</div>
				</div>

				<div class="tf-customization-quote">
                    <div class="tf-quote-content">
						<h3><?php echo esc_html__('Need help building your Travel, Hotel, or Rental Website?', 'tourfic');  ?></h3>
                        <p><?php echo esc_html__('Let our expert team craft a custom WordPress site tailored to your business—whether you\'re running a hotel, tour agency, or vacation rental. Optimized for performance, bookings, and conversions.', 'tourfic'); ?></p>
						<a href="<?php echo esc_url( Helper::tf_utm_generator( 'https://tourfic.com/customization-service/', array( 'utm_medium' => 'dashboard_free_quote' ) ) ); ?>" target="_blank" class="tf-admin-btn tf-btn-secondary">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g clip-path="url(#clip0_1066_1543)">
							<path d="M8.3334 7.49995L5.8334 9.99995L8.3334 12.4999M11.6667 12.4999L14.1667 9.99995L11.6667 7.49995M2.4934 13.6183C2.61593 13.9274 2.64321 14.2661 2.57173 14.5908L1.68423 17.3324C1.65564 17.4715 1.66303 17.6155 1.70571 17.7509C1.7484 17.8863 1.82495 18.0085 1.92812 18.106C2.03129 18.2035 2.15766 18.273 2.29523 18.308C2.43281 18.343 2.57704 18.3422 2.71423 18.3058L5.5584 17.4741C5.86483 17.4133 6.18218 17.4399 6.47423 17.5508C8.25372 18.3818 10.2695 18.5576 12.166 18.0472C14.0625 17.5368 15.7178 16.373 16.8398 14.7611C17.9618 13.1492 18.4785 11.1928 18.2986 9.23707C18.1188 7.28136 17.254 5.45201 15.8568 4.07178C14.4596 2.69155 12.6198 1.84915 10.6621 1.6932C8.70429 1.53724 6.75435 2.07777 5.15627 3.2194C3.55819 4.36103 2.41468 6.0304 1.92748 7.93298C1.44028 9.83556 1.64071 11.8491 2.4934 13.6183Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</g>
						<defs>
							<clipPath id="clip0_1066_1543">
							<rect width="20" height="20" fill="white"/>
							</clipPath>
						</defs>
						</svg>	
						<?php echo esc_html__('Get Free Quote', 'tourfic');  ?>
						</a>								
                    </div>
                </div>
				<?php } ?>
				<div class="tf-quick-access">
					<h3><?php echo esc_html__('Helpful Resources', 'tourfic');  ?></h3>
					<div class="tf-quick-access-wrapper">
						<div class="tf-access-item">
							<a href="<?php echo esc_url( Helper::tf_utm_generator( 'https://themefic.com/docs/tourfic/', array( 'utm_medium' => 'dashboard_doc_link' ) ) ); ?>" target="_blank">
								<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M12.0835 1.66699H5.00016C4.55814 1.66699 4.13421 1.84259 3.82165 2.15515C3.50909 2.46771 3.3335 2.89163 3.3335 3.33366V16.667C3.3335 17.109 3.50909 17.5329 3.82165 17.8455C4.13421 18.1581 4.55814 18.3337 5.00016 18.3337H15.0002C15.4422 18.3337 15.8661 18.1581 16.1787 17.8455C16.4912 17.5329 16.6668 17.109 16.6668 16.667V6.25032L12.0835 1.66699Z" stroke="#7D8FA0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M11.6665 1.66699V6.66699H16.6665" stroke="#7D8FA0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M13.3332 10.833H6.6665" stroke="#7D8FA0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M13.3332 14.167H6.6665" stroke="#7D8FA0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M8.33317 7.5H6.6665" stroke="#7D8FA0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
								<?php echo esc_html__( 'Documentation', 'tourfic' ); ?>
							</a>
						</div>
					</div>
				</div>

			</div>
			<?php
			}

		public function tf_get_sidebar_plugin_list(){
			$plugins = [
				[
					'name'       => __('Instantio', 'tourfic'),
					'slug'       => 'instantio',
					'file_name'  => 'instantio',
					'subtitle'   => 'WooCommerce Quick & Direct Checkout',
					'image'      => 'instantio-logo.png',
				],
				[
					'name'       => __('Hydra', 'tourfic'),
					'slug'       => 'hydra-booking',
					'file_name'  => 'hydra-booking',
					'subtitle'   => 'All in One Appointment Booking System',
					'image'      => 'hydra-logo.png',
				],
				[
					'name'       => __('BEAF', 'tourfic'),
					'slug'       => 'beaf-before-and-after-gallery',
					'file_name'  => 'before-and-after-gallery',
					'subtitle'   => 'Ultimate Before After Image Slider & Gallery',
					'image'      => 'beaf-logo.png',
				],
				[
					'name'       => __('UACF7', 'tourfic'),
					'slug'       => 'ultimate-addons-for-contact-form-7',
					'file_name'  => 'ultimate-addons-for-contact-form-7',
					'subtitle'   => '40+ Essential Addons for Contact Form 7',
					'image'      => 'uacf7-logo.png',
				],
			];
			?>

			<ul>
				<?php foreach ($plugins as $plugin): 
					$plugin_path = $plugin['slug'] . '/' . $plugin['file_name'] . '.php';
					$installed = file_exists(WP_PLUGIN_DIR . '/' . $plugin_path);
					$activated = $installed && is_plugin_active($plugin_path);

					?>

					<li class="tf-plugin-item <?php echo esc_attr($plugin['slug'] == 'instantio' ? 'featured' : ''); ?>" data-plugin-slug="<?php echo esc_attr($plugin['slug']); ?>">
						<div class="tf-plugin-info-wrapper">
							<div class="tf-plugin-content">
								<div class="tf-plugin-image">
									<img src="<?php echo esc_url(TOURFIC_ASSETS_ADMIN_URL.'images/'.$plugin['image']); ?>" alt="<?php echo esc_attr($plugin['name']); ?>" class="<?php echo esc_attr($plugin['name'] == 'BEAF' ? 'beaf-logo' : ''); ?>" width="48" height="48">
								</div>
								<div class="tf-plugin-title">
									<h4><?php echo esc_html($plugin['name']); ?>
									<span class="badge free"><?php echo esc_html__('Free', 'tourfic'); ?></span></h4>
									<p><?php echo esc_html($plugin['subtitle']); ?></p>
									<strong></strong>

									<div class="tf-plugin-btn">
										<?php if (!$installed): ?>
											<button class="tf-plugin-button install" data-action="install" data-plugin="<?php echo esc_attr($plugin['slug']); ?>" data-plugin_filename="<?php echo esc_attr($plugin['file_name']); ?>">
												Install 
												<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M4.66675 4.66663H11.3334V11.3333" stroke="#382673" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												<path d="M4.66675 11.3333L11.3334 4.66663" stroke="#382673" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
												</svg>
												<span class="loader"></span>
											</button>
										<?php elseif (!$activated): ?>
											<button class="tf-plugin-button activate" data-action="activate" data-plugin="<?php echo esc_attr($plugin['slug']); ?>" data-plugin_filename="<?php echo esc_attr($plugin['file_name']); ?>" >
												Activate <span class="loader"></span>
											</button>
										<?php else: ?>
											<span class="tf-plugin-button tf-plugin-status active">Activated</span>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					</li>

				<?php endforeach; ?>

			</ul>

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

					<div class="tf-support-cards tf-support-cards-resources">
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
							<a href="<?php echo esc_url(admin_url( 'admin.php?page=tourfic-setup-wizard' )) ?>" target="" class="tf-link-skip-btn"><?php esc_html_e("Setup Wizard","tourfic"); ?></a>
						</div>

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
										<?php esc_html_e( 'Yes. The WordPress.org version of Tourfic is free and fully functional.', 'tourfic' ); ?>
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
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'You do not have sufficient permissions to access this page.' );
			}

			$existing_option = get_option( $this->option_id, array() );
			$tf_option_value = is_array( $existing_option ) ? $existing_option : array();
			$option_request = ( ! empty( $_POST[ $this->option_id ] ) && is_array( $_POST[ $this->option_id ] ) )
				? Helper::tf_sanitize_recursive_input( wp_unslash( $_POST[ $this->option_id ] ), 'wp_kses_post' ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- The recursive sanitizer cleans both keys and scalar values.
				: array();

			$import_json = isset( $_POST['tf_import_option'] ) && is_string( $_POST['tf_import_option'] )
				? wp_unslash( $_POST['tf_import_option'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON is decoded and recursively sanitized before storage.
				: '';
			if ( '' !== trim( $import_json ) ) {

				$tf_import_option = json_decode( trim( $import_json ), true );
				if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $tf_import_option ) ) {
					return;
				}
				$tf_import_option = Helper::tf_sanitize_recursive_input( $tf_import_option, 'wp_kses_post' );

				do_action( 'tourfic_setting_import_before_save', $tf_import_option );

				$option_request = $tf_import_option;
			}

			$uploaded_files = isset( $_FILES['file'] ) && is_array( $_FILES['file'] )
				? map_deep( wp_unslash( $_FILES['file'] ), 'sanitize_text_field' )
				: array();

			if ( ! empty( $option_request ) && ! empty( $this->option_sections ) ) {
				foreach ( $this->option_sections as $section ) {
					if ( ! empty( $section['fields'] ) ) {

						foreach ( $section['fields'] as $field ) {

							if ( ! empty( $field['id'] ) ) {

								$fieldClass = 'Tourfic_' . $field['type'];

								if($fieldClass == 'Tourfic_tab'){
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

								if($fieldClass != 'Tourfic_file'){
									$data       = $fieldClass == 'TF_repeater' || $fieldClass == 'Tourfic_map'  || $fieldClass == 'Tourfic_color' ? serialize( $data ) : $data;
								}
								if ( 'Tourfic_file' === $fieldClass && ! empty( $uploaded_files ) ) {
									$tf_upload_dir = wp_upload_dir();
									if ( ! empty( $tf_upload_dir['basedir'] ) ) {
										$allowed_mime_types = array(
											'ttf'   => 'font/ttf',
											'otf'   => 'font/otf',
											'woff'  => 'font/woff',
											'woff2' => 'font/woff2',
											'eot'   => 'application/vnd.ms-fontobject',
										);
										$file_names = isset( $uploaded_files['name'] ) && is_array( $uploaded_files['name'] ) ? $uploaded_files['name'] : array();
										for ( $i = 0; $i < count( $file_names ); $i++ ) {
											$tf_font_filename = sanitize_file_name( $file_names[ $i ] );
											$uploaded_file_tmp = isset( $uploaded_files['tmp_name'][ $i ] ) ? sanitize_text_field( $uploaded_files['tmp_name'][ $i ] ) : '';
											$upload_error = isset( $uploaded_files['error'][ $i ] ) ? absint( $uploaded_files['error'][ $i ] ) : UPLOAD_ERR_NO_FILE;
											if ( UPLOAD_ERR_OK !== $upload_error || ! is_uploaded_file( $uploaded_file_tmp ) ) {
												continue;
											}

											$font_upload_dir = static function ( $upload_dir ) {
												$upload_dir['subdir'] = '/itinerary-fonts';
												$upload_dir['path']   = $upload_dir['basedir'] . $upload_dir['subdir'];
												$upload_dir['url']    = $upload_dir['baseurl'] . $upload_dir['subdir'];

												return $upload_dir;
											};
											$font_file       = array(
												'name'     => $tf_font_filename,
												'type'     => isset( $uploaded_files['type'][ $i ] ) ? sanitize_mime_type( $uploaded_files['type'][ $i ] ) : '',
												'tmp_name' => $uploaded_file_tmp,
												'error'    => $upload_error,
												'size'     => isset( $uploaded_files['size'][ $i ] ) ? absint( $uploaded_files['size'][ $i ] ) : 0,
											);

											add_filter( 'upload_dir', $font_upload_dir );
											$upload_result = function_exists( 'wp_handle_upload' )
												? wp_handle_upload(
													$font_file,
													array(
														'test_form' => false,
														'mimes'     => $allowed_mime_types,
													)
												)
												: array( 'error' => esc_html__( 'WordPress upload handling is unavailable.', 'tourfic' ) );
											remove_filter( 'upload_dir', $font_upload_dir );

											if ( ! empty( $upload_result['error'] ) ) {
												$response    = [
													'status'  => 'error',
													'message' => sanitize_text_field( $upload_result['error'] ),
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
				$response['status'] = 'error';
				$response['message'] = esc_html__('Security verification failed. This is usually caused by PHP max_input_vars being too low. Please increase it to at least 5000 in your PHP configuration.', 'tourfic');
				echo wp_json_encode( $response );
				wp_die();
			}

			// Check if the current user has the required capability.
	        if (!current_user_can('manage_options')) {
		        $response['status'] = 'error';
		        $response['message'] = esc_html__('You do not have permission to access this resource.', 'tourfic');
		        echo wp_json_encode($response);
                die();
	        }

			$import_json = isset( $_POST['tf_import_option'] ) && is_string( $_POST['tf_import_option'] )
				? wp_unslash( $_POST['tf_import_option'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON is validated here and recursively sanitized by save_options().
				: '';
			if ( '' !== trim( $import_json ) ) {

				$tf_import_option = json_decode( trim( $import_json ), true );
				if ( JSON_ERROR_NONE !== json_last_error() || empty( $tf_import_option ) || ! is_array( $tf_import_option ) ) {
					$response    = [
						'status'  => 'error',
						'message' => esc_html__( 'Your imported data is not valid', 'tourfic' ),
					];
				}else{
					$this->save_options();
					$response = [
						'status'  => 'success',
						'message' => esc_html__( 'Options imported successfully!', 'tourfic' ),
					];
				}
			}else{
				$this->save_options();
				$response = [
					'status'  => 'success',
					'message' => esc_html__( 'Options saved successfully!', 'tourfic' ),
				];
			}

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
			if (  !isset( $_POST['tf_option_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tf_option_nonce'] ) ), 'updates' ) ) {
				return;
			}

			// Check if the current user has the required capability.
	        if (!current_user_can('manage_options')) {
		        $response['status'] = 'error';
		        $response['message'] = esc_html__('You do not have permission to access this resource.', 'tourfic');
		        echo wp_json_encode($response);
                die();
	        }

			if( !empty( get_option( 'tourfic_settings' ) ) ) {
				update_option( 'tourfic_settings', '' );
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
	}
}
