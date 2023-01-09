<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Settings' ) ) {
	class TF_Settings {

		public $option_id = null;
		public $option_title = null;
		public $option_icon = null;
		public $option_position = null;
		public $option_sections = array();

		public function __construct( $key, $params = array() ) {
			$this->option_id       = $key;
			$this->option_title    = ! empty( $params['title'] ) ? $params['title'] : '';
			$this->option_icon     = ! empty( $params['icon'] ) ? $params['icon'] : '';
			$this->option_position = ! empty( $params['position'] ) ? $params['position'] : 5;
			$this->option_sections = ! empty( $params['sections'] ) ? $params['sections'] : array();

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
				__('Dashboard', 'tourfic'),
				__('Dashboard', 'tourfic'),
				'manage_options',
				'tf_dashboard',
				array( $this, 'tf_dashboard_page' ),
			);

			//Setting submenu
			add_submenu_page(
				$this->option_id,
				__('Settings', 'tourfic'),
				__('Settings', 'tourfic'),
				'manage_options',
				$this->option_id . '#tab=general',
				array( $this, 'tf_options_page' ),
			);

			//Get Help submenu
			add_submenu_page(
				$this->option_id,
				__('Get Help', 'tourfic'),
				__('Get Help', 'tourfic'),
				'manage_options',
				'tf_get_help',
				array( $this,'tf_get_help_callback'),
			);

			// Shortcode submenu
			add_submenu_page(
				$this->option_id,
				__('Shortcodes', 'tourfic'),
				__('Shortcodes', 'tourfic'),
				'manage_options',
				'tf_shortcodes',
				array( 'TF_Shortcodes','tf_shortcode_callback'),
			);

			if ( function_exists('is_tf_pro') ) {
				//License Info submenu
				add_submenu_page(
					$this->option_id,
					__('License Info', 'tourfic'),
					__('License Info', 'tourfic'),
					'manage_options',
					'tf_license_info',
					array( $this,'tf_license_info_callback'),					
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
            $current_page_url = $this->get_current_page_url();
            $query_string = $this->get_query_string($current_page_url);

			?>
			<div class="tf-setting-dashboard">				
				<!-- dashboard-header-include -->
				<?php echo tf_dashboard_header(); ?>	

				<div class="tf-setting-preview">
				<!-- dashboard-banner-section -->
				<div class="tf-setting-banner">
					<div class="tf-setting-banner-content">
						<img src="<?php echo TF_ASSETS_URL; ?>/img/tourfic-logo-white.png" alt="logo">
						<span>Build & Manage Your Next <b>Travel or Hotel Booking Website</b>with Tourfic</span>
					</div>
					<div class="tf-setting-banner-image">
						<img src="<?php echo TF_ASSETS_URL; ?>/img/hotel-booking-management-system@2x.webp" alt="Banner Image">
					</div>
				</div>
				<!-- dashboard-banner-section -->

				<!-- dashboard-performance-section -->

				<div class="tf-setting-performace-section">
					<h2><?php _e("Overview","tourfic"); ?></h2>
					<div class="tf-performance-grid">
						<div class="tf-single-performance-grid">
							<div class="tf-single-performance-icon">
							<img src="<?php echo TF_ASSETS_URL; ?>/img/tf-hotel.png" alt="total Hotel">
							</div>
							<div class="tf-single-performance-content">
								<p><?php _e("Total Hotels","tourfic"); ?></p>
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
							<img src="<?php echo TF_ASSETS_URL; ?>/img/tf-tours.png" alt="total Tours">
							</div>
							<div class="tf-single-performance-content">
								<p><?php _e("Total Tours","tourfic"); ?></p>
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
							<img src="<?php echo TF_ASSETS_URL; ?>/img/tf-booking-online.png" alt="total Booking">
							</div>
							<div class="tf-single-performance-content">
								<p><?php _e("Total Bookings","tourfic"); ?></p>
								<h3>
									<?php
									$tf_order_query_orders = wc_get_orders( array(
											'limit'  => - 1,
											'type'   => 'shop_order',
											'status' => array( 'wc-completed' ),
										)
									);
									echo count( $tf_order_query_orders );
									?>
								</h3>
							</div>
						</div>
						<div class="tf-single-performance-grid">
							<div class="tf-single-performance-icon">
							<img src="<?php echo TF_ASSETS_URL; ?>/img/tf-add-user.png" alt="total Customer">
							</div>
							<div class="tf-single-performance-content">
								<p><?php _e("Total Customers","tourfic"); ?></p>
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
						<img src="<?php echo TF_ASSETS_URL; ?>/img/loader.gif" alt="Loader">
					</div>
					<div class="tf-report-filter">
						<h2><?php _e("Reports","tourfic"); ?></h2>
						<div class="tf-month-filter">
							<span><?php _e("Month","tourfic"); ?></span>
							<select name="tf-month-report" id="tf-month-report">
								<option value=""><?php _e("Select Month","tourfic"); ?></option>
								<option value="1"><?php _e("January","tourfic"); ?></option>
								<option value="2"><?php _e("February","tourfic"); ?></option>
								<option value="3"><?php _e("March","tourfic"); ?></option>
								<option value="4"><?php _e("April","tourfic"); ?></option>
								<option value="5"><?php _e("May","tourfic"); ?></option>
								<option value="6"><?php _e("June","tourfic"); ?></option>
								<option value="7"><?php _e("July","tourfic"); ?></option>
								<option value="8"><?php _e("August","tourfic"); ?></option>
								<option value="9"><?php _e("September","tourfic"); ?></option>
								<option value="10"><?php _e("October","tourfic"); ?></option>
								<option value="11"><?php _e("November","tourfic"); ?></option>
								<option value="12"><?php _e("December","tourfic"); ?></option>
							</select>
						</div>
					</div>
					<div class="tf-order-report">
						<canvas id="tf_months" width="800" height="450"></canvas>
					</div>
				</div>

				<!-- deshboar-performance-section -->

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

			<!-- deshboard-header-include -->
			<?php echo tf_dashboard_header(); ?>

			<div class="tf-settings-help-center">
				<div class="tf-help-center-banner">
					<div class="tf-help-center-content">
						<h2><?php _e("Help Center","tourfic"); ?></h2>
						<p><?php _e("To help you to get started, we put together the documentation, support link, videos and FAQs here.","tourfic"); ?></p>
					</div>
					<div class="tf-help-center-image">
						<img src="<?php echo TF_ASSETS_URL; ?>/img/help-center.jpg" alt="HELP Center Image">
					</div>
				</div>

				<div class="tf-support-document">
					<div class="tf-single-support">
						<a href="https://themefic.com/docs/tourfic/">
							<img src="<?php echo TF_ASSETS_URL; ?>/img/tf-documents.png" alt="Document">
							<h3><?php _e("Documentation","tourfic"); ?></h3>
							<span><?php _e("Read More","tourfic"); ?></span>
						</a>
					</div>
					<div class="tf-single-support">
						<a href="https://portal.themefic.com/support/">
							<img src="<?php echo TF_ASSETS_URL; ?>/img/tf-mail.png" alt="Document">
							<h3><?php _e("Email Support","tourfic"); ?></h3>
							<span><?php _e("Contact Us","tourfic"); ?></span>
						</a>
					</div>
					
					<div class="tf-single-support">
						<a href="https://themefic.com/tourfic/">
							<img src="<?php echo TF_ASSETS_URL; ?>/img/tf-comment.png" alt="Document">
							<h3><?php _e("Live Chat","tourfic"); ?></h3>
							<span><?php _e("Chat Now","tourfic"); ?></span>
						</a>
					</div>
					
					<div class="tf-single-support">
						<a href="https://www.youtube.com/playlist?list=PLY0rtvOwg0ylCl7NTwNHUPq-eY1qwUH_N">
							<img src="<?php echo TF_ASSETS_URL; ?>/img/tf-tutorial.png" alt="Document">
							<h3><?php _e("Video Tutorials","tourfic"); ?></h3>
							<span><?php _e("Watch Video","tourfic"); ?></span>
						</a>
					</div>
				</div>

				<div class="tf-settings-faq">
					<h2><?php _e("Common FAQs","tourfic"); ?></h2>

					<div class="tf-accordion-wrapper">
						<div class="tf-accrodian-item">
							<div class="tf-single-faq">
								<div class="tf-faq-title">
									<i class="fas fa-angle-down"></i>
									<h4><?php _e("What is Tourfic? ","tourfic"); ?></h4>
								</div>
								<div class="tf-faq-desc">
									<p>
									<?php _e("Tourfic is the ultimate WordPress travel plugin for hotel booking, tour operator and travel agency websites.","tourfic"); ?>
									</p>
								</div>
							</div>
						</div>
						<div class="tf-accrodian-item">
							<div class="tf-single-faq">
								<div class="tf-faq-title">
									<i class="fas fa-angle-down"></i>
									<h4><?php _e("How to install Tourfic? ","tourfic"); ?></h4>
								</div>
								<div class="tf-faq-desc">
									<p>
									<?php _e("See the installation tab.","tourfic"); ?>
									</p>
								</div>
							</div>
						</div>
						<div class="tf-accrodian-item">
							<div class="tf-single-faq">
								<div class="tf-faq-title">
									<i class="fas fa-angle-down"></i>
									<h4><?php _e("Is Free version fully free or there is a gap? ","tourfic"); ?></h4>
								</div>
								<div class="tf-faq-desc">
									<p>
									<?php _e("Yes, Tourfic is fully free which is available on WordPress.org. This free version will always be free. It also has a pro version (under development) with additional features which you can purchase from our official website.","tourfic"); ?>
									</p>
								</div>
							</div>
						</div>
						<div class="tf-accrodian-item">
							<div class="tf-single-faq">
								<div class="tf-faq-title">
									<i class="fas fa-angle-down"></i>
									<h4><?php _e("Can I create a hotel booking website with Tourfic? ","tourfic"); ?></h4>
								</div>
								<div class="tf-faq-desc">
									<p>
									<?php _e("Yes, You create your own professional hotel booking website easily with tourfic.","tourfic"); ?>
									</p>
								</div>
							</div>
						</div>
						<div class="tf-accrodian-item">
							<div class="tf-single-faq">
								<div class="tf-faq-title">
									<i class="fas fa-angle-down"></i>
									<h4><?php _e("Can I create a travel or tour booking website with Tourfic? ","tourfic"); ?></h4>
								</div>
								<div class="tf-faq-desc">
									<p>
									<?php _e("Yes, You create your own professional travel or tour booking website easily with tourfic.","tourfic"); ?>
									</p>
								</div>
							</div>
						</div>
						<div class="tf-accrodian-item">
							<div class="tf-single-faq">
								<div class="tf-faq-title">
									<i class="fas fa-angle-down"></i>
									<h4><?php _e("Can Tourfic be used as WooCommerce Accommodation Bookings? ","tourfic"); ?></h4>
								</div>
								<div class="tf-faq-desc">
									<p>
									<?php _e("Yes, You create your own professional accommodation booking website easily with tourfic.","tourfic"); ?>
									</p>
								</div>
							</div>
						</div>
						<div class="tf-accrodian-item">
							<div class="tf-single-faq">
								<div class="tf-faq-title">
									<i class="fas fa-angle-down"></i>
									<h4><?php _e("Can I create a website similar to Booking.com with Tourfic? ","tourfic"); ?></h4>
								</div>
								<div class="tf-faq-desc">
									<p>
									<?php _e("Yes, You can create your own professional tour operator and travel agency website within 5 minutes, just like Booking.com, Agoda, Hotels.com, Airbnb etc.","tourfic"); ?>
									</p>
								</div>
							</div>
						</div>
						<div class="tf-accrodian-item">
							<div class="tf-single-faq">
								<div class="tf-faq-title">
									<i class="fas fa-angle-down"></i>
									<h4><?php _e("Is free version supported? ","tourfic"); ?></h4>
								</div>
								<div class="tf-faq-desc">
									<p>
									<?php _e("Yes, We provide full support on the WordPress.org forums. You can also post questions or bug reports through our Facebook group! or our website. However, please note that, for free version’s support/replies, there can be delays upto 24-48 hours.","tourfic"); ?>
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

			<!-- deshboard-header-include -->
			<?php echo tf_dashboard_header(); ?>
			
			<div class="tf-setting-license">
				<div class="tf-setting-license-tabs">
					<ul>
						<li class="active">
							<span>
								<i class="fas fa-key"></i>
								<?php _e("License Info","tourfic"); ?>
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
								<label for="tf_settings[license-key]" class="tf-field-label"> <?php _e("License Key","tourfic"); ?></label>

								<span class="tf-field-sub-title"><?php _e("Enter your license key here, to activate the product, and get full feature updates and premium support.","tourfic"); ?></span>

								<div class="tf-fieldset">
									<input type="text" name="tf_settings[license-key]" id="tf_settings[license-key]" value="" placeholder="xxxxxxxx-xxxxxxxx-xxxxxxxx-xxxxxxxx" />
								</div>
							</div>

							<div class="tf-field tf-field-text" style="width: 100%;">
								<label for="tf_settings[license-email]" class="tf-field-label"> <?php _e("License Email ","tourfic"); ?></label>

								<span class="tf-field-sub-title"><?php _e("We will send update news of this product by this email address, don't worry, we hate spam","tourfic"); ?></span>

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
				<!-- deshboard-header-include -->
				<?php echo tf_dashboard_header(); ?>
				
                <div class="tf-option-wrapper tf-setting-wrapper">
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
                                           class="tf-tablinks <?php echo $section_count == 0 ? 'active' : ''; ?>"
                                           data-tab="<?php echo esc_attr( $parent_tab_key ) ?>">
											<?php echo ! empty( $section['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $section['icon'] ) . '"></i></span>' : ''; ?>
											<?php echo $section['title']; ?>
                                        </a>
										
										<?php if ( ! empty( $section['sub_section'] ) ): ?>
                                            <ul class="tf-submenu">
												<?php foreach ( $section['sub_section'] as $sub_key => $sub ): ?>
                                                    <li>
                                                        <a href="#<?php echo esc_attr( $sub_key ); ?>"
                                                           class="tf-tablinks <?php echo $section_count == 0 ? 'active' : ''; ?>"
                                                           data-tab="<?php echo esc_attr( $sub_key ) ?>">
														<span class="tf-tablinks-inner">
                                                            <?php echo ! empty( $sub['icon'] ) ? '<span class="tf-sec-icon"><i class="' . esc_attr( $sub['icon'] ) . '"></i></span>' : ''; ?>
                                                            <?php echo $sub['title']; ?>
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
                                    <div id="<?php echo esc_attr( $key ) ?>" class="tf-tab-content <?php echo $content_count == 0 ? 'active' : ''; ?>">

										<?php
										if ( ! empty( $section['fields'] ) ):
											foreach ( $section['fields'] as $field ) :
	
												$default = isset( $field['default'] ) ? $field['default'] : '';
												$value   = isset( $tf_option_value[ $field['id'] ] ) ? $tf_option_value[ $field['id'] ] : $default;

												$tf_option = new TF_Options();
												$tf_option->field( $field, $value, $this->option_id );
												
											endforeach;
										endif; ?>

                                    </div>
									<?php $content_count ++; endforeach; ?>

									<!-- Footer -->
									<div class="tf-option-footer">
										<button type="submit" class="tf-admin-btn tf-btn-secondary tf-submit-btn"><?php _e( 'Save', 'tourfic' ); ?></button>
									</div>
                            </div>
                        </div>

                        

						<?php wp_nonce_field( 'tf_option_nonce_action', 'tf_option_nonce' ); ?>
                    </form>
                </div>
			<?php
			endif;
		}

		/**
		 * Save Options
		 * @author Foysal
		 */
		public function save_options() {

			// Add nonce for security and authentication.
			$nonce_name   = isset( $_POST['tf_option_nonce'] ) ? $_POST['tf_option_nonce'] : '';
			$nonce_action = 'tf_option_nonce_action';

			// Check if a nonce is set.
			if ( ! isset( $nonce_name ) ) {
				return;
			}

			// Check if a nonce is valid.
			if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
				return;
			}

			$tf_option_value = array();
			$option_request  = ( ! empty( $_POST[ $this->option_id ] ) ) ? $_POST[ $this->option_id ] : array();
			if ( ! empty( $option_request ) && ! empty( $this->option_sections ) ) {
				foreach ( $this->option_sections as $section ) {
					if ( ! empty( $section['fields'] ) ) {

						foreach ( $section['fields'] as $field ) {

							if ( ! empty( $field['id'] ) ) {
								$data = isset( $option_request[ $field['id'] ] ) ? $option_request[ $field['id'] ] : '';

								$fieldClass = 'TF_' . $field['type'];
								if($fieldClass != 'TF_file'){
									$data       = $fieldClass == 'TF_repeater' || $fieldClass == 'TF_map' || $fieldClass == 'TF_tab' || $fieldClass == 'TF_color' ? serialize( $data ) : $data;
								}
								if($fieldClass == 'TF_file'){
									$tf_upload_dir = wp_upload_dir();

									if ( ! empty( $tf_upload_dir['basedir'] ) ) {
									$tf_itinerary_fonts = $tf_upload_dir['basedir'].'/itinerary-fonts';
									if ( ! file_exists( $tf_itinerary_fonts ) ) {
									wp_mkdir_p( $tf_itinerary_fonts );
									}
									if (!empty($_FILES['file'])) {
										$tf_fonts_extantions = array('application/octet-stream');
										for($i = 0; $i < count($_FILES['file']['name']); $i++) {
										if (in_array($_FILES['file']['type'][$i], $tf_fonts_extantions)) {
											$tf_font_filename = $_FILES['file']['name'][$i];
											move_uploaded_file($_FILES['file']['tmp_name'][$i], $tf_itinerary_fonts .'/'. $tf_font_filename);
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

            if( ! empty( $_POST['tf_option_nonce'] ) && wp_verify_nonce( $_POST['tf_option_nonce'], 'tf_option_nonce_action' ) ) {
                $this->save_options();
                $response = [
                    'status'  => 'success',
                    'message' => __( 'Options saved successfully!', 'tourfic' ),
                ];
            }

            echo json_encode( $response );
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
	        $url_parts = parse_url( $url );
	        parse_str( $url_parts['query'], $query_string );

            return $query_string;
        }
	}
}
