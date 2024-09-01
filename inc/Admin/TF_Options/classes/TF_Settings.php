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

			if ( function_exists('is_tf_pro') ) {
				//License Info submenu
				add_submenu_page(
					$this->option_id,
					esc_html__('License Info', 'tourfic'),
					esc_html__('License Info', 'tourfic'),
					'manage_options',
					'tf_license_info',
					array( $this,'tf_license_info_callback'),
				);
			}

			// remove first submenu
			remove_submenu_page( $this->option_id, $this->option_id );

		}

		// page top header
		function tf_top_header(){
		?>
		<div class="tf-setting-top-bar">
			<div class="version">
				<img src="<?php echo esc_url(TF_ASSETS_APP_URL.'images/tourfic-logo.webp'); ?>" alt="logo">
				<span>v<?php echo esc_attr( TF_VERSION ); ?></span>
			</div>
			<div class="other-document">
				<svg width="26" height="25" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #003c79;background: ;">
					<path d="M19.2106 0H6.57897C2.7895 0 0.263184 2.52632 0.263184 6.31579V13.8947C0.263184 17.6842 2.7895 20.2105 6.57897 20.2105V22.9011C6.57897 23.9116 7.70318 24.5179 8.53687 23.9495L14.1579 20.2105H19.2106C23 20.2105 25.5263 17.6842 25.5263 13.8947V6.31579C25.5263 2.52632 23 0 19.2106 0ZM12.8948 15.3726C12.3642 15.3726 11.9474 14.9432 11.9474 14.4253C11.9474 13.9074 12.3642 13.4779 12.8948 13.4779C13.4253 13.4779 13.8421 13.9074 13.8421 14.4253C13.8421 14.9432 13.4253 15.3726 12.8948 15.3726ZM14.4863 10.1305C13.9937 10.4589 13.8421 10.6737 13.8421 11.0274V11.2926C13.8421 11.8105 13.4127 12.24 12.8948 12.24C12.3769 12.24 11.9474 11.8105 11.9474 11.2926V11.0274C11.9474 9.56211 13.0211 8.84211 13.4253 8.56421C13.8927 8.24842 14.0442 8.03368 14.0442 7.70526C14.0442 7.07368 13.5263 6.55579 12.8948 6.55579C12.2632 6.55579 11.7453 7.07368 11.7453 7.70526C11.7453 8.22316 11.3158 8.65263 10.7979 8.65263C10.28 8.65263 9.85055 8.22316 9.85055 7.70526C9.85055 6.02526 11.2148 4.66105 12.8948 4.66105C14.5748 4.66105 15.939 6.02526 15.939 7.70526C15.939 9.14526 14.8779 9.86526 14.4863 10.1305Z" fill="#003c79"></path>
				</svg>

				<div class="dropdown">
					<div class="list-item">
						<a href="https://portal.themefic.com/support/" target="_blank">
							<svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M10.0482 4.37109H4.30125C4.06778 4.37109 3.84329 4.38008 3.62778 4.40704C1.21225 4.6137 0 6.04238 0 8.6751V12.2693C0 15.8634 1.43674 16.5733 4.30125 16.5733H4.66044C4.85799 16.5733 5.1184 16.708 5.23514 16.8608L6.3127 18.2985C6.78862 18.9364 7.56087 18.9364 8.03679 18.2985L9.11435 16.8608C9.24904 16.6811 9.46456 16.5733 9.68905 16.5733H10.0482C12.6793 16.5733 14.107 15.3692 14.3136 12.9432C14.3405 12.7275 14.3495 12.5029 14.3495 12.2693V8.6751C14.3495 5.80876 12.9127 4.37109 10.0482 4.37109ZM4.04084 11.5594C3.53798 11.5594 3.14288 11.1551 3.14288 10.6609C3.14288 10.1667 3.54696 9.76233 4.04084 9.76233C4.53473 9.76233 4.93881 10.1667 4.93881 10.6609C4.93881 11.1551 4.53473 11.5594 4.04084 11.5594ZM7.17474 11.5594C6.67188 11.5594 6.27678 11.1551 6.27678 10.6609C6.27678 10.1667 6.68086 9.76233 7.17474 9.76233C7.66862 9.76233 8.07271 10.1667 8.07271 10.6609C8.07271 11.1551 7.6776 11.5594 7.17474 11.5594ZM10.3176 11.5594C9.81476 11.5594 9.41966 11.1551 9.41966 10.6609C9.41966 10.1667 9.82374 9.76233 10.3176 9.76233C10.8115 9.76233 11.2156 10.1667 11.2156 10.6609C11.2156 11.1551 10.8115 11.5594 10.3176 11.5594Z" fill="#003c79"></path>
								<path d="M17.9423 5.08086V8.67502C17.9423 10.4721 17.3855 11.6941 16.272 12.368C16.0026 12.5298 15.6884 12.3141 15.6884 11.9996L15.6973 8.67502C15.6973 5.08086 13.641 3.0232 10.0491 3.0232L4.58048 3.03219C4.26619 3.03219 4.05067 2.7177 4.21231 2.44814C4.88578 1.33395 6.10702 0.776855 7.89398 0.776855H13.641C16.5055 0.776855 17.9423 2.21452 17.9423 5.08086Z" fill="#003c79"></path>
							</svg>
						<span><?php esc_html_e("Need Help?","tourfic"); ?></span>
						</a>
						<a href="https://themefic.com/docs/tourfic/" target="_blank">
							<svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M16.1896 7.57803H13.5902C11.4586 7.57803 9.72274 5.84103 9.72274 3.70803V1.10703C9.72274 0.612031 9.318 0.207031 8.82332 0.207031H5.00977C2.23956 0.207031 0 2.00703 0 5.22003V13.194C0 16.407 2.23956 18.207 5.00977 18.207H12.0792C14.8494 18.207 17.089 16.407 17.089 13.194V8.47803C17.089 7.98303 16.6843 7.57803 16.1896 7.57803ZM8.09478 14.382H4.4971C4.12834 14.382 3.82254 14.076 3.82254 13.707C3.82254 13.338 4.12834 13.032 4.4971 13.032H8.09478C8.46355 13.032 8.76935 13.338 8.76935 13.707C8.76935 14.076 8.46355 14.382 8.09478 14.382ZM9.89363 10.782H4.4971C4.12834 10.782 3.82254 10.476 3.82254 10.107C3.82254 9.73803 4.12834 9.43203 4.4971 9.43203H9.89363C10.2624 9.43203 10.5682 9.73803 10.5682 10.107C10.5682 10.476 10.2624 10.782 9.89363 10.782Z" fill="#003c79"></path>
							</svg>
							<span><?php esc_html_e ("Documentation","tourfic"); ?></span>

						</a>
						<a href="https://portal.themefic.com/support/" target="_blank">
							<svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" clip-rule="evenodd" d="M13.5902 7.57803H16.1896C16.6843 7.57803 17.089 7.98303 17.089 8.47803V13.194C17.089 16.407 14.8494 18.207 12.0792 18.207H5.00977C2.23956 18.207 0 16.407 0 13.194V5.22003C0 2.00703 2.23956 0.207031 5.00977 0.207031H8.82332C9.318 0.207031 9.72274 0.612031 9.72274 1.10703V3.70803C9.72274 5.84103 11.4586 7.57803 13.5902 7.57803ZM11.9613 0.396012C11.5926 0.0270125 10.954 0.279013 10.954 0.792013V3.93301C10.954 5.24701 12.0693 6.33601 13.4274 6.33601C14.2818 6.34501 15.4689 6.34501 16.4852 6.34501H16.4854C16.998 6.34501 17.2679 5.74201 16.9081 5.38201C16.4894 4.96018 15.9637 4.42927 15.3988 3.85888L15.3932 3.85325L15.3913 3.85133L15.3905 3.8505L15.3902 3.85016C14.2096 2.65803 12.86 1.29526 11.9613 0.396012ZM3.0145 12.0732C3.0145 11.7456 3.28007 11.48 3.60768 11.48H5.32132V9.76639C5.32132 9.43879 5.58689 9.17321 5.9145 9.17321C6.2421 9.17321 6.50768 9.43879 6.50768 9.76639V11.48H8.22131C8.54892 11.48 8.8145 11.7456 8.8145 12.0732C8.8145 12.4008 8.54892 12.6664 8.22131 12.6664H6.50768V14.38C6.50768 14.7076 6.2421 14.9732 5.9145 14.9732C5.58689 14.9732 5.32132 14.7076 5.32132 14.38V12.6664H3.60768C3.28007 12.6664 3.0145 12.4008 3.0145 12.0732Z" fill="#003c79"></path>
							</svg>
							<span><?php esc_html_e("Feature Request","tourfic"); ?></span>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php
		}

		/**
		 * Options Page HTML
		 * @author Jahid, Foysal
		 */
		public function tf_dashboard_page() {
            $current_page_url = $this->get_current_page_url();
            $query_string = $this->get_query_string($current_page_url);

			?>
			<div class="tf-setting-dashboard">
				<!-- dashboard-header-include -->
				<?php \Tourfic\Classes\Helper::tf_dashboard_header(); ?>

				<div class="tf-setting-preview">
				<!-- dashboard-banner-section -->
				<div class="tf-setting-banner">
					<div class="tf-setting-banner-content">
						<img src="<?php echo esc_url(TF_ASSETS_APP_URL.'images/tourfic-logo-white.png'); ?>" alt="logo">
						<span>Build & Manage Your Next <b>Travel or Hotel Booking Website</b>with Tourfic</span>
					</div>
					<div class="tf-setting-banner-image">
						<img src="<?php echo esc_url(TF_ASSETS_APP_URL.'images/hotel-booking-management-system@2x.webp'); ?>" alt="Banner Image">
					</div>
				</div>
				<!-- dashboard-banner-section -->

				<!-- dashboard-performance-section -->

				<div class="tf-setting-performace-section">
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
                            <canvas id="tf_months" width="800" height="450"></canvas>
                        </div>
                    <?php else : ?>
                        <div class="tf-field-notice-inner tf-notice-danger" style="margin-top: 20px;">
                            <?php esc_html_e( 'Please install and activate WooCommerce plugin to view reports.', 'tourfic' ); ?>
                        </div>
                    <?php endif; ?>
				</div>

				<!-- dashboard-performance-section -->

				</div>
			</div>

			<?php
		}

		/**
		 * Get Help Page
		 * @author Jahid
		 */
		public function tf_get_help_callback(){
		?>
		<div class="tf-setting-dashboard">

			<!-- dashboard-header-include -->
			<?php \Tourfic\Classes\Helper::tf_dashboard_header(); ?>

			<div class="tf-settings-help-center">
				<div class="tf-help-center-banner">
					<div class="tf-help-center-content">
						<h2><?php esc_html_e("Setup Wizard","tourfic"); ?></h2>
						<p><?php esc_html_e("Click the button below to run the setup wizard of Tourfic. Your existing settings will not change.","tourfic"); ?></p>
                        <a href="<?php echo esc_url(admin_url( 'admin.php?page=tf-setup-wizard' )) ?>" class="tf-admin-btn tf-btn-secondary"><?php esc_html_e("Setup Wizard","tourfic"); ?></a>
					</div>
					<div class="tf-help-center-image">
						<img src="<?php echo esc_url(TF_ASSETS_APP_URL); ?>images/setup_wizard.png" alt="setup wizard">
					</div>
				</div>

                <div class="tf-help-center-banner">
                    <div class="tf-help-center-content">
                        <h2><?php esc_html_e("Help Center","tourfic"); ?></h2>
                        <p><?php esc_html_e("To help you to get started, we put together the documentation, support link, videos and FAQs here.","tourfic"); ?></p>
                    </div>
                    <div class="tf-help-center-image">
                        <img src="<?php echo esc_url(TF_ASSETS_APP_URL); ?>images/help-center.jpg" alt="HELP Center Image">
                    </div>
                </div>

				<div class="tf-support-document">
					<div class="tf-single-support">
						<a href="https://themefic.com/docs/tourfic/" target="_blank">
							<img src="<?php echo esc_url(TF_ASSETS_APP_URL); ?>images/tf-documents.png" alt="Document">
							<h3><?php esc_html_e("Documentation","tourfic"); ?></h3>
							<span><?php esc_html_e("Read More","tourfic"); ?></span>
						</a>
					</div>
					<div class="tf-single-support">
						<a href="https://portal.themefic.com/support/" target="_blank">
							<img src="<?php echo esc_url(TF_ASSETS_APP_URL); ?>images/tf-mail.png" alt="Document">
							<h3><?php esc_html_e("Email Support","tourfic"); ?></h3>
							<span><?php esc_html_e("Contact Us","tourfic"); ?></span>
						</a>
					</div>

					<div class="tf-single-support">
						<a href="https://themefic.com/tourfic/" target="_blank">
							<img src="<?php echo esc_url(TF_ASSETS_APP_URL); ?>images/tf-comment.png" alt="Document">
							<h3><?php esc_html_e("Live Chat","tourfic"); ?></h3>
							<span><?php esc_html_e("Chat Now","tourfic"); ?></span>
						</a>
					</div>

					<div class="tf-single-support">
						<a href="https://www.youtube.com/playlist?list=PLY0rtvOwg0ylCl7NTwNHUPq-eY1qwUH_N" target="_blank">
							<img src="<?php echo esc_url(TF_ASSETS_APP_URL); ?>images/tf-tutorial.png" alt="Document">
							<h3><?php esc_html_e("Video Tutorials","tourfic"); ?></h3>
							<span><?php esc_html_e("Watch Video","tourfic"); ?></span>
						</a>
					</div>
				</div>

				<div class="tf-settings-faq">
					<h2><?php esc_html_e("Common FAQs","tourfic"); ?></h2>

					<div class="tf-accordion-wrapper">
						<div class="tf-accrodian-item">
							<div class="tf-single-faq">
								<div class="tf-faq-title">
									<i class="fas fa-angle-down"></i>
									<h4><?php esc_html_e("What is Tourfic? ","tourfic"); ?></h4>
								</div>
								<div class="tf-faq-desc">
									<p>
									<?php esc_html_e("Tourfic is the ultimate WordPress travel plugin for hotel booking, tour operator and travel agency websites.","tourfic"); ?>
									</p>
								</div>
							</div>
						</div>
						<div class="tf-accrodian-item">
							<div class="tf-single-faq">
								<div class="tf-faq-title">
									<i class="fas fa-angle-down"></i>
									<h4><?php esc_html_e("How to install Tourfic? ","tourfic"); ?></h4>
								</div>
								<div class="tf-faq-desc">
									<p>
									<?php esc_html_e("Please check our documentations","tourfic"); ?>
									</p>
								</div>
							</div>
						</div>
						<div class="tf-accrodian-item">
							<div class="tf-single-faq">
								<div class="tf-faq-title">
									<i class="fas fa-angle-down"></i>
									<h4><?php esc_html_e("Is Free version fully free or there is a gap? ","tourfic"); ?></h4>
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
									<i class="fas fa-angle-down"></i>
									<h4><?php esc_html_e("Can I create a hotel booking website with Tourfic? ","tourfic"); ?></h4>
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
									<i class="fas fa-angle-down"></i>
									<h4><?php esc_html_e("Can I create a travel or tour booking website with Tourfic? ","tourfic"); ?></h4>
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
									<i class="fas fa-angle-down"></i>
									<h4><?php esc_html_e("Can Tourfic be used as WooCommerce Accommodation Bookings? ","tourfic"); ?></h4>
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
									<i class="fas fa-angle-down"></i>
									<h4><?php esc_html_e("Can I create a website similar to Booking.com with Tourfic? ","tourfic"); ?></h4>
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
									<i class="fas fa-angle-down"></i>
									<h4><?php esc_html_e("Is free version supported? ","tourfic"); ?></h4>
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
		public function tf_license_info_callback(){
		?>
		<div class="tf-setting-dashboard">

			<!-- dashboard-header-include -->
			<?php \Tourfic\Classes\Helper::tf_dashboard_header(); ?>

			<div class="tf-setting-license">
				<div class="tf-setting-license-tabs">
					<ul>
						<li class="active">
							<span>
								<i class="fas fa-key"></i>
								<?php esc_html_e("License Info","tourfic"); ?>
							</span>
						</li>
					</ul>
				</div>
				<div class="tf-setting-license-field">
					<div class="tf-tab-wrapper">
						<div id="license" class="tf-tab-content">
							<div class="tf-field tf-field-callback" style="width: 100%;">
								<div class="tf-fieldset"></div>
							</div>
							<?php
							$licenseKey = ! empty( tfliopt( 'license-key' ) ) ? tfliopt( 'license-key' ) : '';
							$liceEmail  = ! empty( tfliopt( 'license-email' ) ) ? tfliopt( 'license-email' ) : '';

							if ( TourficProBase::CheckWPPlugin( $licenseKey, $liceEmail, $licenseMessage, $responseObj, TF_PRO_PATH . 'tourfic-pro.php' ) ) {
								tf_license_info();
							} else {
							?>
							<div class="tf-field tf-field-text" style="width: 100%;">
								<label for="tf_settings[license-key]" class="tf-field-label"> <?php esc_html_e("License Key","tourfic"); ?></label>

								<span class="tf-field-sub-title"><?php esc_html_e("Enter your license key here, to activate the product, and get full feature updates and premium support.","tourfic"); ?></span>

								<div class="tf-fieldset">
									<input type="text" name="tf_settings[license-key]" id="tf_settings[license-key]" value="" placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx" />
								</div>
							</div>

							<div class="tf-field tf-field-text" style="width: 100%;">
								<label for="tf_settings[license-email]" class="tf-field-label"> <?php esc_html_e("License Email ","tourfic"); ?></label>

								<span class="tf-field-sub-title"><?php esc_html_e("We will send update news of this product by this email address, don't worry, we hate spam","tourfic"); ?></span>

								<div class="tf-fieldset">
									<input type="text" name="tf_settings[license-email]" id="tf_settings[license-email]" value="" />
								</div>
							</div>

							<div class="tf-field tf-field-callback" style="width: 100%;">
								<div class="tf-fieldset">
									<div class="tf-license-activate">
										<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Activate" /></p>
									</div>
								</div>
							</div>
							<?php } ?>
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
			$current_page_url = $this->get_current_page_url();
			$query_string = $this->get_query_string($current_page_url);

			// Set default values.
			if ( empty( $tf_option_value ) ) {
				$tf_option_value = array();
			}

            $ajax_save_class = 'tf-ajax-save';

			

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
			$option_request  = ( ! empty( $_POST[ $this->option_id ] ) ) ? $_POST[ $this->option_id ] : array();

			if(isset($_POST['tf_import_option']) && !empty(wp_unslash( trim( $_POST['tf_import_option']) ))){

				$tf_import_option = json_decode( wp_unslash( trim( $_POST['tf_import_option']) ), true );

				do_action( 'tf_setting_import_before_save', $tf_import_option );

				// $option_request = !empty($tf_import_option) && is_array($tf_import_option) ? $tf_import_option : $option_request;
				update_option( $this->option_id, $tf_import_option );
				return;
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
										$tf_fonts_extantions = array('application/octet-stream');
										for($i = 0; $i < count($_FILES['file']['name']); $i++) {
											if (in_array($_FILES['file']['type'][$i], $tf_fonts_extantions)) {
												$tf_font_filename = $_FILES['file']['name'][$i];
												$uploaded_file_tmp = $_FILES['file']['tmp_name'][$i];
												$destination_path = $tf_itinerary_fonts .'/'. $tf_font_filename;
												if (copy($uploaded_file_tmp, $destination_path)) {
													// File copied successfully, you can perform further actions if needed
												} else {
													// Handle error if copy operation failed
												}
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
				'message' => __( 'Something went wrong!', 'tourfic' ),
			];

			if( isset( $_POST['tf_option_nonce'] ) || wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['tf_option_nonce'])), 'tf_option_nonce_action' ) ) {

				if(isset($_POST['tf_import_option']) && !empty(wp_unslash( trim( $_POST['tf_import_option']) )) ){

					$tf_import_option = json_decode( wp_unslash( trim( $_POST['tf_import_option']) ), true );
					if(empty($tf_import_option) || !is_array($tf_import_option)){
						$response    = [
							'status'  => 'error',
							'message' => __( 'Your imported data is not valid', 'tourfic' ),
						];
					}else{
						$this->save_options();
						$response = [
							'status'  => 'success',
							'message' => __( 'Options imported successfully!', 'tourfic' ),
						];
					}
				}else{
					$this->save_options();
					$response = [
						'status'  => 'success',
						'message' => __( 'Options saved successfully!', 'tourfic' ),
					];

				}

			}

			echo wp_json_encode( $response );
			wp_die();
		}


		public function tf_ajax_reset_options() {
			$response    = [
				'status'  => 'error',
				'message' => __( 'Something went wrong!', 'tourfic' ),
			];


			if( isset( $_POST['tf_option_nonce'] ) || wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['tf_option_nonce'])), 'tf_option_nonce_action' ) ) {

				!empty( get_option( 'tf_settings' ) ) ?  : '';

				if( !empty( get_option( 'tf_settings' ) ) ) {
					update_option( 'tf_settings', '' );
					$response = [
						'status'  => 'success',
						'message' => __( 'Options Reset successfully!', 'tourfic' ),
					];
				} else {
					$response    = [
						'status'  => 'error',
						'message' => __( 'Settings are fresh, nothing to reset.', 'tourfic' ),
					];
				}

			} else {
				$response    = [
					'status'  => 'error',
					'message' => __( 'Something went wrong!', 'tourfic' ),
				];
			}

			echo wp_json_encode( $response );
			wp_die();
		}

		public function tf_search_settings_autocomplete_callback() {
			if( isset( $_POST['tf_option_nonce'] ) || wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['tf_option_nonce'])), 'tf_option_nonce_action' ) ) {
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
				
			} else {
				$response = [
					'status'  => 'error',
					'message' => __( 'Something went wrong!', 'tourfic' ),
				];
			}

			echo wp_json_encode( $response );
			wp_die();
		}

		/*
		 * Get current page url
		 * @return string
		 * @author Foysal
		 */
		public function get_current_page_url() {
            $page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            return $page_url;
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
		        $response['message'] = __('You do not have permission to access this resource.', 'tourfic');
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
