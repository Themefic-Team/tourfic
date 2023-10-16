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
			add_filter( 'woocommerce_enable_setup_wizard', '__return_false' );
			add_action( 'admin_init', [ $this, 'tf_activation_redirect' ] );
			add_action( 'wp_ajax_tf_setup_wizard_submit', [ $this, 'tf_setup_wizard_submit_ajax' ] );
			add_action( 'in_admin_header', [ $this, 'remove_notice' ], 1000 );

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
		 * Remove all notice in setup wizard page
		 */
		public function remove_notice() {
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'tf-setup-wizard' ) {
				remove_all_actions( 'admin_notices' );
				remove_all_actions( 'all_admin_notices' );
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
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=tf_settings' ) ); ?>" class="tf-admin-btn tf-btn-secondary back-to-dashboard"><span><?php _e( 'Back to Dashboard', 'tourfic' ) ?></span></a>
                        </div>
                        <div class="tf-setup-header-right">
                            <span class="get-help-link"><?php _e( 'Having troubles?', 'tourfic' ) ?> <a class="" target="_blank" href="https://portal.themefic.com/support/"><?php _e( 'Get help', 'torufic' ) ?></a></span>
                        </div>
                    </div>
                    <form method="post" id="tf-setup-wizard-form" data-skip-steps="">
						<?php
						$this->tf_setup_welcome_step();
						$this->tf_setup_step_one();
						$this->tf_setup_step_two();
						$this->tf_setup_step_three();
						$this->tf_setup_step_four();
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
                <div class="welcome-img"><img src="<?php echo TF_ASSETS_ADMIN_URL . 'images/welcome.png' ?>" alt="<?php esc_attr_e( 'Welcome to Tourfic!', 'tourfic' ) ?>"></div>
                <h1 class="tf-setup-welcome-title"><?php _e( 'Welcome to Tourfic!', 'tourfic' ) ?></h1>
                <div class="tf-setup-welcome-description"><?php _e( 'Thanks for choosing Tourfic for your travel/hotel/apartment business. We are excited to have you on board. This quick setup wizard is simple and straightforward and shouldn’t take longer than five minutes. It will help you configure the basic settings of Tourfic to get started. Please note that this setup guide is entirely optional.', 'tourfic' ) ?></div>
                <div class="tf-setup-welcome-footer">
                    <button type="button" class="tf-admin-btn tf-btn-secondary tf-setup-start-btn"><span><?php _e( 'Get Started', 'tourfic' ) ?></span></button>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=tf_settings' ) ); ?>" class="tf-link-btn"><?php _e( 'Skip to Dashboard', 'tourfic' ) ?></a>
                </div>
            </div>
			<?php
		}

		/**
		 * Setup step one
		 */
		private function tf_setup_step_one() {
			$tf_disable_services = ! empty( tfopt( 'disable-services' ) ) ? tfopt( 'disable-services' ) : '';
			?>
            <div class="tf-setup-step-container tf-setup-step-1 <?php echo self::$current_step == 'step_1' ? 'active' : ''; ?>" data-step="1">
                <section class="tf-setup-step-layout">
					<?php $this->tf_setup_wizard_steps_header() ?>
                    <h1 class="tf-setup-step-title"><?php _e( 'Select your service type', 'tourfic' ) ?></h1>
                    <p class="tf-setup-step-desc"><?php _e( '(You can choose any one or both)', 'tourfic' ) ?></p>
                    <ul class="tf-select-service">
                        <li>
                            <input type="checkbox" id="tf-hotel" name="tf-services[]"
                                   value="hotel" <?php echo empty( $tf_disable_services ) || ! in_array( 'hotel', $tf_disable_services ) ? esc_attr( 'checked' ) : ''; ?>/>
                            <label for="tf-hotel">
                                <img src="<?php echo TF_ASSETS_ADMIN_URL . 'images/hotel.png' ?>" alt="<?php esc_attr_e( 'Hotel', 'tourfic' ) ?>">
                                <span><?php _e( 'Hotel', 'tourfic' ) ?></span>
                            </label>
                        </li>
                        <li>
                            <input type="checkbox" id="tf-tour" name="tf-services[]" value="tour" <?php echo empty( $tf_disable_services ) || ! in_array( 'tour', $tf_disable_services ) ? esc_attr( 'checked' ) : ''; ?>/>
                            <label for="tf-tour">
                                <img src="<?php echo TF_ASSETS_ADMIN_URL . 'images/tour.png' ?>" alt="<?php esc_attr_e( 'Tour', 'tourfic' ) ?>">
                                <span><?php _e( 'Tour', 'tourfic' ) ?></span>
                            </label>
                        </li>
                        <li>
                            <input type="checkbox" id="tf-apartment" name="tf-services[]"
                                   value="apartment" <?php echo empty( $tf_disable_services ) || ! in_array( 'apartment', $tf_disable_services ) ? esc_attr( 'checked' ) : ''; ?>/>
                            <label for="tf-apartment">
                                <img src="<?php echo TF_ASSETS_ADMIN_URL . 'images/apartment.png' ?>" alt="<?php esc_attr_e( 'Apartment', 'tourfic' ) ?>">
                                <span><?php _e( 'Apartment', 'tourfic' ) ?></span>
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
		private function tf_setup_step_two() {
			$tf_search_result      = ! empty( tfopt( 'posts_per_page' ) ) ? tfopt( 'posts_per_page' ) : 10;
			$tf_review_autopublish = ! empty( tfopt( 'r-auto-publish' ) ) ? tfopt( 'r-auto-publish' ) : '';
			?>
            <div class="tf-setup-step-container tf-setup-step-2 <?php echo self::$current_step == 'step_2' ? 'active' : ''; ?>" data-step="2">
                <section class="tf-setup-step-layout">
					<?php $this->tf_setup_wizard_steps_header( 2 ) ?>
                    <h1 class="tf-setup-step-title"><?php _e( 'General Settings', 'tourfic' ) ?></h1>
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
                        <div class="tf-setup-form-item-label"><label class=""><?php _e( 'Posts Per Page on Search Result', 'tourfic' ) ?></label></div>
                        <div class="tf-setup-form-item-input">
                            <input type="number" name="tf-search-result-posts-per-page" id="tf-search-result-posts-per-page" value="<?php echo esc_attr( $tf_search_result ); ?>">
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
                                <input type="checkbox" id="tf-auto-publish-review" name="tf-auto-publish-review" value="<?php echo ! empty( $tf_review_autopublish ) ? esc_attr( '1' ) : ''; ?>"
                                       class="tf-switch" <?php echo ! empty( $tf_review_autopublish ) ? esc_attr( 'checked' ) : ''; ?>/>
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
		 * Setup step three
		 */
		private function tf_setup_step_three() {
			$tf_hotel_review     = ! empty( tfopt( 'h-review' ) ) ? tfopt( 'h-review' ) : '';
			$tf_hotel_share      = ! empty( tfopt( 'h-share' ) ) ? tfopt( 'h-share' ) : '';
			$tf_hotel_slug       = ! empty( get_option( 'hotel_slug' ) ) ? get_option( 'hotel_slug' ) : 'hotels';
			$tf_tour_review      = ! empty( tfopt( 't-review' ) ) ? tfopt( 't-review' ) : '';
			$tf_tour_related     = ! empty( tfopt( 't-related' ) ) ? tfopt( 't-related' ) : '';
			$tf_tour_slug        = ! empty( get_option( 'tour_slug' ) ) ? get_option( 'tour_slug' ) : 'tours';
			$tf_apartment_review = ! empty( tfopt( 'disable-apartment-review' ) ) ? tfopt( 'disable-apartment-review' ) : '';
			$tf_apartment_share  = ! empty( tfopt( 'disable-apartment-share' ) ) ? tfopt( 'disable-apartment-share' ) : '';
			$tf_apartment_slug   = ! empty( get_option( 'apartment_slug' ) ) ? get_option( 'apartment_slug' ) : 'apartments';
			?>
            <div class="tf-setup-step-container tf-setup-step-3 <?php echo self::$current_step == 'step_3' ? 'active' : ''; ?>" data-step="3">
                <section class="tf-setup-step-layout">
					<?php $this->tf_setup_wizard_steps_header( 3 ) ?>
                    <div class="tf-hotel-setup-wizard">
                        <h3 class="tf-setup-step-subtitle"><?php _e( 'Hotel settings', 'tourfic' ) ?></h3>
                        <p class="tf-setup-step-desc"><?php _e( 'These settings can be overridden from <strong>Tourfic Settings > Hotel Settings</strong>', 'tourfic' ) ?></p>

                        <!--Review Section-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-hotel-review-section"><?php _e( 'Review Section', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-hotel-review-section" class="tf-switch-label">
                                    <input type="checkbox" id="tf-hotel-review-section" name="tf-hotel-review-section" value="<?php echo empty( $tf_hotel_review ) ? esc_attr( '1' ) : ''; ?>"
                                           class="tf-switch" <?php echo empty( $tf_hotel_review ) ? esc_attr( 'checked' ) : ''; ?>/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Share Option-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-hotel-share-option"><?php _e( 'Share Option', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-hotel-share-option" class="tf-switch-label">
                                    <input type="checkbox" id="tf-hotel-share-option" name="tf-hotel-share-option" value="<?php echo empty( $tf_hotel_share ) ? esc_attr( '1' ) : ''; ?>"
                                           class="tf-switch" <?php echo empty( $tf_hotel_share ) ? esc_attr( 'checked' ) : ''; ?>/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Hotel Permalink-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class=""><?php _e( 'Hotel Permalink', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <input type="text" name="tf-hotel-permalink" id="tf-hotel-permalink" value="<?php echo esc_attr( $tf_hotel_slug ); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="tf-tour-setup-wizard">
                        <h3 class="tf-setup-step-subtitle"><?php _e( 'Tour settings', 'tourfic' ) ?></h3>
                        <p class="tf-setup-step-desc"><?php _e( 'These settings can be overridden from <strong>Tourfic Settings > Tour Settings</strong>', 'tourfic' ) ?></p>

                        <!--Review Section-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-tour-review-section"><?php _e( 'Review Section', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-tour-review-section" class="tf-switch-label">
                                    <input type="checkbox" id="tf-tour-review-section" name="tf-tour-review-section" value="<?php echo empty( $tf_tour_review ) ? esc_attr( '1' ) : ''; ?>"
                                           class="tf-switch" <?php echo empty( $tf_tour_review ) ? esc_attr( 'checked' ) : ''; ?>/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Related Section-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-tour-related-section"><?php _e( 'Related Section', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-tour-related-section" class="tf-switch-label">
                                    <input type="checkbox" id="tf-tour-related-section" name="tf-tour-related-section" value="<?php echo empty( $tf_tour_related ) ? esc_attr( '1' ) : ''; ?>"
                                           class="tf-switch" <?php echo empty( $tf_tour_related ) ? esc_attr( 'checked' ) : ''; ?>/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Tour Permalink-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class=""><?php _e( 'Tour Permalink', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <input type="text" name="tf-tour-permalink" id="tf-tour-permalink" value="<?php echo esc_attr( $tf_tour_slug ); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="tf-apartment-setup-wizard">
                        <h3 class="tf-setup-step-subtitle"><?php _e( 'Apartment settings', 'tourfic' ) ?></h3>
                        <p class="tf-setup-step-desc"><?php _e( 'These settings can be overridden from <strong>Tourfic Settings > Apartment Settings</strong>', 'tourfic' ) ?></p>

                        <!--Review Section-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-apartment-review-section"><?php _e( 'Review Section', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-apartment-review-section" class="tf-switch-label">
                                    <input type="checkbox" id="tf-apartment-review-section" name="tf-apartment-review-section" value="<?php echo empty( $tf_apartment_review ) ? esc_attr( '1' ) : ''; ?>"
                                           class="tf-switch" <?php echo empty( $tf_apartment_review ) ? esc_attr( 'checked' ) : ''; ?>/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Share Option-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-apartment-share-option"><?php _e( 'Share Option', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-apartment-share-option" class="tf-switch-label">
                                    <input type="checkbox" id="tf-apartment-share-option" name="tf-apartment-share-option" value="<?php echo empty( $tf_apartment_share ) ? esc_attr( '1' ) : ''; ?>"
                                           class="tf-switch" <?php echo empty( $tf_apartment_share ) ? esc_attr( 'checked' ) : ''; ?>/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Apartment Permalink-->
                        <div class="tf-setup-form-item">
                            <div class="tf-setup-form-item-label"><label class=""><?php _e( 'Apartment Permalink', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <input type="text" name="tf-apartment-permalink" id="tf-apartment-permalink" value="<?php echo esc_attr( $tf_apartment_slug ); ?>">
                            </div>
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
		 * Setup step four
		 */
		private function tf_setup_step_four() {
			$tf_hotel_single_template  = ! empty( tf_data_types( tfopt( 'tf-template' ) )['single-hotel'] ) ? tf_data_types( tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';
			$tf_hotel_archive_template = ! empty( tf_data_types( tfopt( 'tf-template' ) )['hotel-archive'] ) ? tf_data_types( tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
			$tf_tour_single_template   = ! empty( tf_data_types( tfopt( 'tf-template' ) )['single-tour'] ) ? tf_data_types( tfopt( 'tf-template' ) )['single-tour'] : 'design-1';
			$tf_tour_archive_template  = ! empty( tf_data_types( tfopt( 'tf-template' ) )['tour-archive'] ) ? tf_data_types( tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
			$tf_apartment_single_template  = ! empty( tf_data_types( tfopt( 'tf-template' ) )['single-apartment'] ) ? tf_data_types( tfopt( 'tf-template' ) )['single-apartment'] : 'default';
			$tf_apartment_archive_template = ! empty( tf_data_types( tfopt( 'tf-template' ) )['apartment-archive'] ) ? tf_data_types( tfopt( 'tf-template' ) )['apartment-archive'] : 'default';
			?>
            <div class="tf-setup-step-container tf-setup-step-4 <?php echo self::$current_step == 'step_4' ? 'active' : ''; ?>" data-step="4">
                <section class="tf-setup-step-layout tf-template-step">
					<?php $this->tf_setup_wizard_steps_header( 4 ) ?>
                    <div class="tf-hotel-setup-wizard">
                        <div class="tf-field tf-field-heading tf-field-class " style="width:100%;">
                            <div class="tf-fieldset">
                                <div class="tf-field-heading-inner">
                                    <div class="tf-field-heading-content has-content">
                                        <div class="tf-field-heading-main-content"><?php _e( 'Hotel settings', 'tourfic' ) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Hotel Single Template-->
                        <div class="tf-field tf-field-imageselect  " style="width:100%;">
                            <label for="tf_settings[single-hotel]" class="tf-field-label"> <?php echo __( "Select Single Template", "tourfic" ); ?> </label>
                            <div class="tf-fieldset">
                                <ul class="tf-image-radio-group tf-inline">
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_hotel"
                                                   value="design-1" <?php echo ! empty( $tf_hotel_single_template ) && $tf_hotel_single_template == "design-1" ? esc_attr( 'checked' ) : ''; ?> >
                                            <img src="<?php echo TF_ASSETS_ADMIN_URL; ?>images/template/design1-hotel.jpg" alt="Design 1">
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_hotel"
                                                   value="default" <?php echo ! empty( $tf_hotel_single_template ) && $tf_hotel_single_template == "default" ? esc_attr( 'checked' ) : ''; ?> >
                                            <img src="<?php echo TF_ASSETS_ADMIN_URL; ?>images/template/default-hotel.jpg" alt="Defult">
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!--Hotel Archive Page-->
                        <div class="tf-field tf-field-imageselect  " style="width:100%;">
                            <label for="tf_settings[hotel-archive]" class="tf-field-label"> <?php echo __( "Select Archive & Search Result Template ", "tourfic" ); ?> </label>
                            <div class="tf-fieldset">
                                <ul class="tf-image-radio-group tf-inline">
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_hotel_archive"
                                                   value="design-1" <?php echo ! empty( $tf_hotel_archive_template ) && $tf_hotel_archive_template == "design-1" ? esc_attr( 'checked' ) : ''; ?> >
                                            <img src="<?php echo TF_ASSETS_ADMIN_URL; ?>images/template/hotel-archive-design1.jpg" alt="Design 1">
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_hotel_archive"
                                                   value="default" <?php echo ! empty( $tf_hotel_archive_template ) && $tf_hotel_archive_template == "default" ? esc_attr( 'checked' ) : ''; ?> >
                                            <img src="<?php echo TF_ASSETS_ADMIN_URL; ?>images/template/hotel-archive-default.jpg" alt="Defult">
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>

                    <div class="tf-tour-setup-wizard">
                        <div class="tf-field tf-field-heading tf-field-class " style="width:100%;">
                            <div class="tf-fieldset">
                                <div class="tf-field-heading-inner">
                                    <div class="tf-field-heading-content has-content">
                                        <div class="tf-field-heading-main-content"><?php _e( 'Tour settings', 'tourfic' ) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Tour Single Template-->
                        <div class="tf-field tf-field-imageselect  " style="width:100%;">
                            <label for="tf_settings[single-tour]" class="tf-field-label"> <?php echo __( "Select Single Template", "tourfic" ); ?> </label>
                            <div class="tf-fieldset">
                                <ul class="tf-image-radio-group tf-inline">
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_tour"
                                                   value="design-1" <?php echo ! empty( $tf_tour_single_template ) && $tf_tour_single_template == "design-1" ? esc_attr( 'checked' ) : ''; ?> >
                                            <img src="<?php echo TF_ASSETS_ADMIN_URL; ?>images/template/design1-tour.jpg" alt="Design 1">
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_tour"
                                                   value="default" <?php echo ! empty( $tf_tour_single_template ) && $tf_tour_single_template == "default" ? esc_attr( 'checked' ) : ''; ?> >
                                            <img src="<?php echo TF_ASSETS_ADMIN_URL; ?>images/template/default-tour.jpg" alt="Defult">
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!--Tour Archive Page-->
                        <div class="tf-field tf-field-imageselect  " style="width:100%;">
                            <label for="tf_settings[tour-archive]" class="tf-field-label"> <?php echo __( "Select Archive & Search Result Template ", "tourfic" ); ?> </label>
                            <div class="tf-fieldset">
                                <ul class="tf-image-radio-group tf-inline">
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_tour_archive"
                                                   value="design-1" <?php echo ! empty( $tf_tour_archive_template ) && $tf_tour_archive_template == "design-1" ? esc_attr( 'checked' ) : ''; ?> >
                                            <img src="<?php echo TF_ASSETS_ADMIN_URL; ?>images/template/tour-archive-design-1.jpg" alt="Design 1">
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_tour_archive"
                                                   value="default" <?php echo ! empty( $tf_tour_archive_template ) && $tf_tour_archive_template == "default" ? esc_attr( 'checked' ) : ''; ?> >
                                            <img src="<?php echo TF_ASSETS_ADMIN_URL; ?>images/template/tour-archive-default.jpg" alt="Defult">
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>


                    </div>

                    <div class="tf-apartment-setup-wizard">
                        <div class="tf-field tf-field-heading tf-field-class " style="width:100%;">
                            <div class="tf-fieldset">
                                <div class="tf-field-heading-inner">
                                    <div class="tf-field-heading-content has-content">
                                        <div class="tf-field-heading-main-content"><?php _e( 'Apartment settings', 'tourfic' ) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--Hotel Single Template-->
                        <div class="tf-field tf-field-imageselect  " style="width:100%;">
                            <label for="tf_settings[single-apartment]" class="tf-field-label"> <?php echo __( "Select Single Template", "tourfic" ); ?> </label>
                            <div class="tf-fieldset">
                                <ul class="tf-image-radio-group tf-inline">
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_apartment"
                                                   value="default" <?php echo ! empty( $tf_apartment_single_template ) && $tf_apartment_single_template == "default" ? esc_attr( 'checked' ) : ''; ?> >
                                            <img src="<?php echo TF_ASSETS_ADMIN_URL; ?>images/template/default-apartment.jpg" alt="Defult">
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!--Hotel Archive Page-->
                        <div class="tf-field tf-field-imageselect  " style="width:100%;">
                            <label for="tf_settings[apartment-archive]" class="tf-field-label"> <?php echo __( "Select Archive & Search Result Template ", "tourfic" ); ?> </label>
                            <div class="tf-fieldset">
                                <ul class="tf-image-radio-group tf-inline">
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_apartment_archive"
                                                   value="default" <?php echo ! empty( $tf_apartment_archive_template ) && $tf_apartment_archive_template == "default" ? esc_attr( 'checked' ) : ''; ?> >
                                            <img src="<?php echo TF_ASSETS_ADMIN_URL; ?>images/template/apartment-archive-default.jpg" alt="Defult">
                                        </label>
                                    </li>
                                </ul>
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

		/*
		 * Finish setup wizard
		 */
		private function tf_setup_finish_step() {
			?>
            <div class="tf-setup-content-layout tf-finish-step <?php echo self::$current_step == 'finish' ? 'active' : ''; ?>">
                <div class="welcome-img"><img src="<?php echo TF_ASSETS_ADMIN_URL . 'images/hooray.png' ?>" alt="<?php esc_attr_e( 'Thank you', 'tourfic' ) ?>"></div>
                <h1 class="tf-setup-welcome-title"><?php _e( 'Hooray! You’re all set.', 'tourfic' ) ?></h1>
                <div class="tf-setup-welcome-description"><?php _e( 'Let\'s get started and make the most out of Tourfic. With this plugin, you can manage your hotel or travel bookings with ease, and provide your customers with a seamless booking experience. So, let\'s dive in and start streamlining your hotel or travel business operations today!', 'tourfic' ) ?></div>
                <div class="tf-setup-welcome-footer tf-setup-finish-footer">
                    <a href="<?php echo admin_url( 'post-new.php?post_type=tf_hotel' ) ?>" class="tf-admin-btn tf-btn-secondary tf-add-new-hotel"><?php _e( 'Create new Hotel', 'tourfic' ) ?></a>
                    <a href="<?php echo admin_url( 'post-new.php?post_type=tf_tours' ) ?>" class="tf-admin-btn tf-add-new-tour"><?php _e( 'Create new Tour', 'tourfic' ) ?></a>
                    <a href="<?php echo admin_url( 'post-new.php?post_type=tf_apartment' ) ?>" class="tf-admin-btn tf-btn-secondary tf-add-new-apartment"><?php _e( 'Create new Apartment', 'tourfic' ) ?></a>
                    <a href="<?php echo admin_url( 'admin.php?page=tf_settings' ) ?>" class="tf-admin-btn"><?php _e( 'Tourfic Setting', 'tourfic' ) ?></a>
                </div>
            </div>
			<?php
		}

		/**
		 * redirect to set up wizard when active plugin
		 */
		public function tf_activation_redirect() {
			if ( ! get_option( 'tf_setup_wizard' ) && ! get_option( 'tf_settings' ) ) {
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

                <div class="tf-steps-item <?php echo $active_step == 4 ? 'active' : ''; ?>">
                    <div class="tf-steps-item-container">
                        <div class="tf-steps-item-tail"></div>
                        <div class="tf-steps-item-icon">
                            <span class="tf-steps-icon">
                                <?php echo $active_step == 4 ? $active_icon : ( $active_step > 4 ? $finish_icon : $inactive_icon ); ?>
                            </span>
                        </div>
                        <div class="tf-steps-item-title"><?php _e( 'Step 4', 'tourfic' ); ?></div>
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
			$tf_services            = array( 'hotel', 'tour', 'apartment' );
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
			$apartment_review       = isset( $_POST['tf-apartment-review-section'] ) ? $_POST['tf-apartment-review-section'] : '';
			$apartment_share        = isset( $_POST['tf-apartment-share-option'] ) ? $_POST['tf-apartment-share-option'] : '';
			$apartment_permalink    = isset( $_POST['tf-apartment-permalink'] ) ? $_POST['tf-apartment-permalink'] : '';

			// Template Step
			$tf_hotel_single  = isset( $_POST['tf_single_hotel'] ) ? $_POST['tf_single_hotel'] : 'design-1';
			$tf_hotel_archive = isset( $_POST['tf_hotel_archive'] ) ? $_POST['tf_hotel_archive'] : 'design-1';
			$tf_tour_single   = isset( $_POST['tf_single_tour'] ) ? $_POST['tf_single_tour'] : 'design-1';
			$tf_tour_archive  = isset( $_POST['tf_tour_archive'] ) ? $_POST['tf_tour_archive'] : 'design-1';
			$tf_apartment_single  = isset( $_POST['tf_single_apartment'] ) ? $_POST['tf_single_apartment'] : 'default';
			$tf_apartment_archive = isset( $_POST['tf_apartment_archive'] ) ? $_POST['tf_apartment_archive'] : 'default';

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

			if ( ! in_array( 3, $skip_steps ) && ! in_array( 'apartment', $services ) ) {
				$tf_settings['disable-apartment-review'] = ! empty( $apartment_review ) ? 0 : 1;
				$tf_settings['disable-apartment-share']  = ! empty( $apartment_share ) ? 0 : 1;

				if ( ! empty( $apartment_permalink ) ) {
					update_option( 'apartment_slug', $apartment_permalink );
				}
			}

			// Settings Template
			if ( ! in_array( 4, $skip_steps ) && ! in_array( 'hotel', $services ) ) {
				$tf_settings['tf-template']['single-hotel']  = ! empty( $tf_hotel_single ) ? $tf_hotel_single : '';
				$tf_settings['tf-template']['hotel-archive'] = ! empty( $tf_hotel_archive ) ? $tf_hotel_archive : '';
			}

			if ( ! in_array( 4, $skip_steps ) && ! in_array( 'tour', $services ) ) {
				$tf_settings['tf-template']['single-tour']  = ! empty( $tf_tour_single ) ? $tf_tour_single : '';
				$tf_settings['tf-template']['tour-archive'] = ! empty( $tf_tour_archive ) ? $tf_tour_archive : '';
			}

			if ( ! in_array( 4, $skip_steps ) && ! in_array( 'apartment', $services ) ) {
				$tf_settings['tf-template']['single-apartment']  = ! empty( $tf_apartment_single ) ? $tf_apartment_single : '';
				$tf_settings['tf-template']['apartment-archive'] = ! empty( $tf_apartment_archive ) ? $tf_apartment_archive : '';
			}

			update_option( 'tf_settings', $tf_settings );
			$response = [
				'success'      => true,
				'redirect_url' => esc_url( admin_url( 'admin.php?page=tf_settings' ) )
			];

			echo json_encode( $response );
			wp_die();
		}
	}
}

TF_Setup_Wizard::instance();