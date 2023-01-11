<?php
defined( 'ABSPATH' ) || exit;
/**
 * Setup Wizard Class
 * @since 2.9.3
 * @author Foysal
 */
if ( ! class_exists( 'TF_Setup_Wizard' ) ) {
	class TF_Setup_Wizard {

		private static $instance = null;
		private static $current_step = null;

		/**
		 * Singleton instance
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( self::$instance == null ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function __construct() {
			add_action( 'admin_menu', [ $this, 'tf_wizard_menu' ], 100 );
			add_action( 'after_setup_theme', [ $this, 'tf_activation_redirect' ], 99 );
			add_action( 'admin_enqueue_scripts', [ $this, 'tf_setup_wizard_enqueue_scripts' ] );
			add_action( 'wp_ajax_tf_setup_wizard_submit', [ $this, 'tf_setup_wizard_submit_ajax' ] );

			self::$current_step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : 'welcome';
		}

		/**
		 * Add wizard submenu
		 */
		public function tf_wizard_menu() {

			if ( current_user_can( 'manage_options' ) ) {
				add_submenu_page(
					'',
					esc_html__( 'TF Setup Wizard', 'tourfic' ),
					esc_html__( 'TF Setup Wizard', 'tourfic' ),
					'manage_options',
					'tf-setup-wizard',
					[ $this, 'tf_wizard_page' ],
					99
				);
			}
		}

		/**
		 * Enqueue scripts
		 */
		public function tf_setup_wizard_enqueue_scripts( $screen ) {
			if ( $screen == 'admin_page_tf-setup-wizard' ) {
				wp_enqueue_style( 'tf-setup-wizard', TF_URL . 'admin/assets/css/setup-wizard.css', [], TOURFIC );
				wp_enqueue_script( 'tf-setup-wizard', TF_ASSETS_URL . 'admin/js/tourfic-admin-scripts.min.js', [ 'jquery' ], TOURFIC, true );

				wp_localize_script( 'tf-setup-wizard', 'tf_setup_wizard', [
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'tf-setup-wizard' ),
					'i18n'    => array(
						'no_services_selected' => __( 'Please select at least one service.', 'tourfic' ),
					)
				] );
			}
		}

		/**
		 * Setup wizard page
		 */
		public function tf_wizard_page() {
			?>
            <div class="tf-setup-wizard-wrapper" id="tf-setup-wizard-wrapper">
                <div class="tf-setup-container">
                    <div class="tf-setup-header">
                        <div class="tf-setup-header-left">
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=tf_settings' ) ); ?>" class="tf-admin-btn tf-btn-secondary back-to-dashboard"><span><?php _e( 'Back to dashboard', 'tourfic' ) ?></span></a>
                        </div>
                        <div class="tf-setup-header-right">
                            <span class="get-help-link">Having troubles? <a class="" href=""> Get help </a></span>
                        </div>
                    </div>
                    <form method="post" id="tf-setup-wizard-form" data-skip-steps="">
						<?php
						$this->tf_setup_welcome_step();
						$this->tf_setup_step_one();
						$this->setup_step_two();
						$this->tf_setup_finish_step();
						?>
						<?php wp_nonce_field( 'tf_setup_wizard_action', 'tf_setup_wizard_nonce' ); ?>
                        <input type="hidden" name="tf-skip-steps">
                    </form>
                </div>
            </div>
			<?php
		}

		/**
		 * Welcome step
		 */
		private function tf_setup_welcome_step() {
			?>
            <div class="tf-setup-content-layout tf-welcome-step <?php echo self::$current_step == 'welcome' ? 'active' : ''; ?>">
                <div class="welcome-img"><img src="<?php echo TF_URL . 'admin/assets/images/welcome.png' ?>" alt="<?php esc_attr_e( 'Welcome to Tourfic!', 'tourfic' ) ?>"></div>
                <h1 class="tf-setup-welcome-title"><?php _e( 'Welcome to Tourfic!', 'tourfic' ) ?></h1>
                <div class="tf-setup-welcome-description"><?php _e( 'Thanks for choosing Tourfic for your travel business. We are excited to have you on board. This quick setup wizard will help you configure the basic settings. It’s completely optional and shouldn’t take longer than five minutes.', 'tourfic' ) ?></div>
                <div class="tf-setup-welcome-footer">
                    <button type="button" class="tf-admin-btn tf-btn-secondary tf-setup-start-btn"><span><?php _e( 'Get Started', 'tourfic' ) ?></span></button>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=tf_settings' ) ); ?>" class="tf-link-btn"><?php _e( 'Skip to dashboard', 'tourfic' ) ?></a>
                </div>
            </div>
			<?php
		}

		/**
		 * Setup step one
		 */
		private function tf_setup_step_one() {
			?>
            <div class="tf-setup-step-container tf-setup-step-1 <?php echo self::$current_step == 'step_1' ? 'active' : ''; ?>" data-step="1">
                <section class="tf-setup-step-layout">
					<?php $this->tf_setup_wizard_steps_header() ?>
                    <h1 class="tf-setup-step-title"><?php _e( 'Select your services', 'tourfic' ) ?></h1>
                    <ul class="tf-select-service">
                        <li>
                            <input type="checkbox" id="tf-hotel" name="tf-services[]" value="hotel" checked/>
                            <label for="tf-hotel">
                                <img src="<?php echo TF_URL . 'admin/assets/images/hotel.png' ?>" alt="<?php esc_attr_e( 'Hotel', 'tourfic' ) ?>">
                                <span><?php _e( 'Hotel', 'tourfic' ) ?></span>
                            </label>
                        </li>
                        <li>
                            <input type="checkbox" id="tf-tour" name="tf-services[]" value="tour" checked/>
                            <label for="tf-tour">
                                <img src="<?php echo TF_URL . 'admin/assets/images/tour.png' ?>" alt="<?php esc_attr_e( 'Tour', 'tourfic' ) ?>">
                                <span><?php _e( 'Tour', 'tourfic' ) ?></span>
                            </label>
                        </li>
                    </ul>
                </section>
                <div class="tf-setup-action-btn-wrapper">
                    <div></div>
                    <div class="tf-setup-action-btn-next">
                        <button type="button" class="tf-setup-skip-btn tf-link-btn"><?php _e( 'Skip this step', 'tourfic' ) ?></button>
                        <button type="button" class="tf-setup-next-btn tf-admin-btn tf-btn-secondary"><?php _e( 'Next', 'tourfic' ) ?></button>
                    </div>
                </div>
            </div>
			<?php
		}

		/**
		 * Setup step two
		 */
		private function setup_step_two() {
			?>
            <div class="tf-setup-step-container tf-setup-step-2 <?php echo self::$current_step == 'step_2' ? 'active' : ''; ?>" data-step="2">
                <section class="tf-setup-step-layout">
					<?php $this->tf_setup_wizard_steps_header( 2 ) ?>
                    <h1 class="tf-setup-step-title"><?php _e( 'Tourfic Settings', 'tourfic' ) ?></h1>
                    <div class="tf-setup-form-item">
                        <div class="tf-setup-form-item-label"><label class=""><?php _e( 'Select Search Result Page', 'tourfic' ) ?></label></div>
                        <div class="tf-setup-form-item-input">
                            <select name="tf-search-result-page" id="tf-search-result-page">
                                <option value=""><?php _e( 'Select a page', 'tourfic' ) ?></option>
								<?php
								$pages              = get_pages();
								$search_result_page = get_option( 'tf_search_page_id' );
								foreach ( $pages as $page ) {
									echo '<option value="' . $page->ID . '" ' . selected( $search_result_page, $page->ID, false ) . '>' . $page->post_title . '</option>';
								}
								?>
                            </select>
                        </div>
                    </div>

                    <!--Search result posts per page-->
                    <div class="tf-setup-form-item">
                        <div class="tf-setup-form-item-label"><label class=""><?php _e( 'Search Result Posts Per Page', 'tourfic' ) ?></label></div>
                        <div class="tf-setup-form-item-input">
                            <input type="number" name="tf-search-result-posts-per-page" id="tf-search-result-posts-per-page" value="10">
                        </div>
                    </div>

                    <!--wishlist page-->
                    <div class="tf-setup-form-item">
                        <div class="tf-setup-form-item-label"><label class=""><?php _e( 'Select Wishlist Page', 'tourfic' ) ?></label></div>
                        <div class="tf-setup-form-item-input">
                            <select name="tf-wishlist-page" id="tf-wishlist-page">
                                <option value=""><?php _e( 'Select a page', 'tourfic' ) ?></option>
								<?php
								$pages         = get_pages();
								$wishlist_page = get_option( 'tf_wishlist_page_id' );
								foreach ( $pages as $page ) {
									echo '<option value="' . $page->ID . '" ' . selected( $wishlist_page, $page->ID, false ) . '>' . $page->post_title . '</option>';
								}
								?>
                            </select>
                        </div>
                    </div>

                    <!--Auto Publish Review-->
                    <div class="tf-setup-form-item">
                        <div class="tf-setup-form-item-label"><label class="" for="tf-auto-publish-review"><?php _e( 'Auto Publish Review', 'tourfic' ) ?></label></div>
                        <div class="tf-setup-form-item-input">
                            <label for="tf-auto-publish-review" class="tf-switch-label">
                                <input type="checkbox" id="tf-auto-publish-review" name="tf-auto-publish-review" value="1" class="tf-switch" checked/>
                                <span class="tf-switch-slider"></span>
                            </label>
                        </div>
                    </div>

                </section>
                <div class="tf-setup-action-btn-wrapper">
                    <button type="button" class="tf-setup-prev-btn tf-admin-btn tf-btn-secondary"><?php _e( 'Previous', 'tourfic' ) ?></button>
                    <div class="tf-setup-action-btn-next">
                        <button type="button" class="tf-setup-skip-btn tf-link-btn"><?php _e( 'Skip this step', 'tourfic' ) ?></button>
                        <button type="button" class="tf-setup-next-btn tf-admin-btn tf-btn-secondary"><?php _e( 'Next', 'tourfic' ) ?></button>
                    </div>
                </div>
            </div>
			<?php
		}

		/**
		 * Finish step
		 */
		private function tf_setup_finish_step() {
			?>
            <div class="tf-setup-step-container tf-setup-step-3 <?php echo self::$current_step == 'step_3' ? 'active' : ''; ?>" data-step="3">
                <section class="tf-setup-step-layout">
					<?php $this->tf_setup_wizard_steps_header( 3 ) ?>
                    <div class="tf-hotel-setup-wizard">
                        <h3 class="tf-setup-step-subtitle"><?php _e( 'Hotel settings', 'tourfic' ) ?></h3>

                        <!--Review Section-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-hotel-review-section"><?php _e( 'Review Section', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-hotel-review-section" class="tf-switch-label">
                                    <input type="checkbox" id="tf-hotel-review-section" name="tf-hotel-review-section" value="1" class="tf-switch" checked/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Share Option-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-hotel-share-option"><?php _e( 'Share Option', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-hotel-share-option" class="tf-switch-label">
                                    <input type="checkbox" id="tf-hotel-share-option" name="tf-hotel-share-option" value="1" class="tf-switch" checked/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Hotel Permalink-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class=""><?php _e( 'Hotel Permalink', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <input type="text" name="tf-hotel-permalink" id="tf-hotel-permalink" value="hotels">
                            </div>
                        </div>
                    </div>

                    <div class="tf-tour-setup-wizard">
                        <h3 class="tf-setup-step-subtitle"><?php _e( 'Tour settings', 'tourfic' ) ?></h3>

                        <!--Review Section-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-tour-review-section"><?php _e( 'Review Section', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-tour-review-section" class="tf-switch-label">
                                    <input type="checkbox" id="tf-tour-review-section" name="tf-tour-review-section" value="1" class="tf-switch" checked/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Related Section-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-tour-related-section"><?php _e( 'Related Section', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-tour-related-section" class="tf-switch-label">
                                    <input type="checkbox" id="tf-tour-related-section" name="tf-tour-related-section" value="1" class="tf-switch" checked/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Tour Permalink-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class=""><?php _e( 'Tour Permalink', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <input type="text" name="tf-tour-permalink" id="tf-tour-permalink" value="tours">
                            </div>
                        </div>
                    </div>

                </section>
                <div class="tf-setup-action-btn-wrapper">
                    <button type="button" class="tf-setup-prev-btn tf-admin-btn tf-btn-secondary"><?php _e( 'Previous', 'tourfic' ) ?></button>
                    <div class="tf-setup-action-btn-next">
                        <button type="submit" class="tf-setup-skip-btn tf-link-btn tf-setup-submit-btn"><?php _e( 'Skip this step', 'tourfic' ) ?></button>
                        <button type="submit" class="tf-setup-submit-btn tf-admin-btn tf-btn-secondary"><?php _e( 'Finish', 'tourfic' ) ?></button>
                    </div>
                </div>
            </div>
			<?php
		}

		/**
		 * redirect to set up wizard when active plugin
		 */
		public function tf_activation_redirect() {
			if ( ( ! get_option( 'tf_setup_wizard' ) ) ) {
				update_option( 'tf_setup_wizard', 'active' );
				wp_redirect( admin_url( 'admin.php?page=tf-setup-wizard' ) );
				exit;
			}
		}

		private function tf_setup_wizard_steps_header( $active_step = 1 ) {
			$inactive_icon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="11" stroke="#D8D9DF" stroke-width="2"></circle></svg>';
			$active_icon   = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="12" fill="#5D5DFF"></circle><circle cx="12" cy="12" r="4" fill="white"></circle></svg>';
			$finish_icon   = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="12" fill="#5D5DFF"></circle><path fill-rule="evenodd" clip-rule="evenodd" d="M17.7077 8.29352C18.0979 8.68439 18.0974 9.31755 17.7065 9.70773L11.703 15.7007C11.3123 16.0906 10.6796 16.0903 10.2894 15.7L7.29289 12.7036C6.90237 12.3131 6.90237 11.6799 7.29289 11.2894C7.68342 10.8988 8.31658 10.8988 8.70711 11.2894L10.9971 13.5794L16.2935 8.29227C16.6844 7.90209 17.3176 7.90265 17.7077 8.29352Z" fill="white"></path></svg>';
			?>
            <div class="tf-setup-steps">
                <div class="tf-steps-item <?php echo $active_step == 1 ? 'active' : ''; ?>">
                    <div class="tf-steps-item-container">
                        <div class="tf-steps-item-tail"></div>
                        <div class="tf-steps-item-icon">
                            <span class="tf-steps-icon">
                                <?php echo $active_step == 1 ? $active_icon : $finish_icon; ?>
                            </span>
                        </div>
                        <div class="tf-steps-item-title"><?php _e( 'Step 1', 'tourfic' ); ?></div>
                    </div>
                </div>

                <div class="tf-steps-item <?php echo $active_step == 2 ? 'active' : ''; ?>">
                    <div class="tf-steps-item-container">
                        <div class="tf-steps-item-tail"></div>
                        <div class="tf-steps-item-icon">
                            <span class="tf-steps-icon">
                                <?php echo $active_step == 2 ? $active_icon : ( $active_step > 2 ? $finish_icon : $inactive_icon ); ?>
                            </span>
                        </div>
                        <div class="tf-steps-item-title"><?php _e( 'Step 2', 'tourfic' ); ?></div>
                    </div>
                </div>
                <div class="tf-steps-item <?php echo $active_step == 3 ? 'active' : ''; ?>">
                    <div class="tf-steps-item-container">
                        <div class="tf-steps-item-tail"></div>
                        <div class="tf-steps-item-icon">
                            <span class="tf-steps-icon">
                                <?php echo $active_step == 3 ? $active_icon : ( $active_step > 3 ? $finish_icon : $inactive_icon ); ?>
                            </span>
                        </div>
                        <div class="tf-steps-item-title"><?php _e( 'Step 3', 'tourfic' ); ?></div>
                    </div>
                </div>
            </div>
			<?php
		}

		function tf_setup_wizard_submit_ajax() {

			// Add nonce for security and authentication.
			$nonce_name   = isset( $_POST['tf_setup_wizard_nonce'] ) ? $_POST['tf_setup_wizard_nonce'] : '';
			$nonce_action = 'tf_setup_wizard_action';

			// Check if a nonce is set.
			if ( ! isset( $nonce_name ) ) {
				return;
			}

			// Check if a nonce is valid.
			if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
				return;
			}

			$tf_settings            = get_option( 'tf_settings' );
			$tf_services            = array( 'hotel', 'tour' );
			$services               = isset( $_POST['tf-services'] ) ? $_POST['tf-services'] : [];
			$search_page            = isset( $_POST['tf-search-result-page'] ) ? $_POST['tf-search-result-page'] : '';
			$search_result_per_page = isset( $_POST['tf-search-result-posts-per-page'] ) ? $_POST['tf-search-result-posts-per-page'] : '';
			$wishlist_page          = isset( $_POST['tf-wishlist-page'] ) ? $_POST['tf-wishlist-page'] : '';
			$auto_publish           = isset( $_POST['tf-auto-publish-review'] ) ? $_POST['tf-auto-publish-review'] : '';
			$hotel_review           = isset( $_POST['tf-hotel-review-section'] ) ? $_POST['tf-hotel-review-section'] : '';
			$hotel_share            = isset( $_POST['tf-hotel-share-option'] ) ? $_POST['tf-hotel-share-option'] : '';
			$hotel_permalink        = isset( $_POST['tf-hotel-permalink'] ) ? $_POST['tf-hotel-permalink'] : '';
			$tour_review            = isset( $_POST['tf-tour-review-section'] ) ? $_POST['tf-tour-review-section'] : '';
			$tour_related           = isset( $_POST['tf-tour-related-section'] ) ? $_POST['tf-tour-related-section'] : '';
			$tour_permalink         = isset( $_POST['tf-tour-permalink'] ) ? $_POST['tf-tour-permalink'] : '';

			//skip steps
			$skip_steps = isset( $_POST['tf-skip-steps'] ) ? $_POST['tf-skip-steps'] : [];
			$skip_steps = explode( ',', $skip_steps );

			if ( ! in_array( 1, $skip_steps ) ) {
				$services = array_diff( $tf_services, $services );
				$services = array_map( 'sanitize_text_field', $services );

				$tf_settings['disable-services'] = [];
				if ( ! empty( $services ) ) {
					foreach ( $services as $service ) {
						$tf_settings['disable-services'][ $service ] = $service;
					}
				}
			}

			if ( ! in_array( 2, $skip_steps ) ) {
				$tf_settings['search-result-page'] = ! empty( $search_page ) ? $search_page : '';
				$tf_settings['posts_per_page']     = ! empty( $search_result_per_page ) ? $search_result_per_page : '';
				$tf_settings['wl-page']            = ! empty( $wishlist_page ) ? $wishlist_page : '';
				$tf_settings['r-auto-publish']     = ! empty( $auto_publish ) ? $auto_publish : '';
			}

			if ( ! in_array( 3, $skip_steps ) && ! in_array( 'hotel', $services ) ) {
				$tf_settings['h-review'] = ! empty( $hotel_review ) ? 0 : 1;
				$tf_settings['h-share']  = ! empty( $hotel_share ) ? 0 : 1;

				if ( ! empty( $hotel_permalink ) ) {
					update_option( 'hotel_slug', $hotel_permalink );
				}
			}

			if ( ! in_array( 3, $skip_steps ) && ! in_array( 'tour', $services ) ) {
				$tf_settings['t-review']  = ! empty( $tour_review ) ? 0 : 1;
				$tf_settings['t-related'] = ! empty( $tour_related ) ? 0 : 1;

				if ( ! empty( $tour_permalink ) ) {
					update_option( 'tour_slug', $tour_permalink );
				}
			}

			update_option( 'tf_settings', $tf_settings );
			$response              = [
				'success'      => true,
				'redirect_url' => esc_url( admin_url( 'admin.php?page=tf_settings' ) )
			];

			echo json_encode( $response );
			wp_die();
		}
	}
}

TF_Setup_Wizard::instance();