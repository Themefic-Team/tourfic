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
				wp_enqueue_script( 'tf-setup-wizard', TF_URL . 'admin/assets/js/setup-wizard.js', [ 'jquery' ], TOURFIC, true );
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
                            <span class="get-help-link">Having troubles? <a class="" href="https://support.themewinter.com/docs/plugins/docs/eventin/"> Get help </a></span>
                        </div>
                    </div>
					<?php
					//					if ( $step == 'welcome' ) {
					$this->tf_setup_welcome_step();
					//					} elseif ( $step == 'step_one' ) {
					$this->tf_setup_step_one();
					//					} elseif ( $step == 'step_two' ) {
					$this->setup_step_two();
					//					} elseif ( $step == 'finish' ) {
					$this->tf_setup_finish_step();
					//					}
					?>
                </div>
            </div>
			<?php
		}

		/**
		 * Welcome step
		 */
		public function tf_setup_welcome_step() {
			?>
            <div class="tf-setup-content-layout tf-welcome-step <?php echo self::$current_step == 'welcome' ? 'active' : ''; ?>">
                <div class="welcome-img"><img src="http://tourfic.wp/wp-content/plugins/wp-event-solution/build/assets/images/welcome.png" alt="<?php esc_attr_e( 'Welcome to Tourfic!', 'tourfic' ) ?>"></div>
                <h1 class="tf-setup-welcome-title"><?php _e( 'Welcome to Tourfic!', 'tourfic' ) ?></h1>
                <div class="tf-setup-welcome-description"><?php _e( 'Thanks for choosing Tourfic to manage your next events. Easily create and manage unlimited online and offline events.', 'tourfic' ) ?></div>
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
		public function tf_setup_step_one() {
			?>
            <div class="tf-setup-step-container tf-setup-step-1 <?php echo self::$current_step == 'step_one' ? 'active' : ''; ?>">
                <section class="tf-setup-step-layout">
                    <div class="ant-steps ant-steps-horizontal etn-onboard-steps ant-steps-small ant-steps-label-vertical">
                        <div class="ant-steps-item ant-steps-item-process ant-steps-item-custom ant-steps-item-active">
                            <div class="ant-steps-item-container">
                                <div class="ant-steps-item-tail"></div>
                                <div class="ant-steps-item-icon"><span class="ant-steps-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12"
                                                                                                                                                                                                     cy="12"
                                                                                                                                                                                                     r="12"
                                                                                                                                                                                                     fill="#5D5DFF"></circle><circle
                                                    cx="12" cy="12" r="4" fill="white"></circle></svg></span></div>
                                <div class="ant-steps-item-content">
                                    <div class="ant-steps-item-title">
                                        <div class="ant-steps-item-title-color">Step 1</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ant-steps-item ant-steps-item-wait ant-steps-item-custom">
                            <div class="ant-steps-item-container">
                                <div class="ant-steps-item-tail"></div>
                                <div class="ant-steps-item-icon"><span class="ant-steps-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12"
                                                                                                                                                                                                     cy="12"
                                                                                                                                                                                                     r="11"
                                                                                                                                                                                                     stroke="#D8D9DF"
                                                                                                                                                                                                     stroke-width="2"></circle></svg></span>
                                </div>
                                <div class="ant-steps-item-content">
                                    <div class="ant-steps-item-title">
                                        <div class="">Step 2</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ant-steps-item ant-steps-item-wait ant-steps-item-custom">
                            <div class="ant-steps-item-container">
                                <div class="ant-steps-item-tail"></div>
                                <div class="ant-steps-item-icon"><span class="ant-steps-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12"
                                                                                                                                                                                                     cy="12"
                                                                                                                                                                                                     r="11"
                                                                                                                                                                                                     stroke="#D8D9DF"
                                                                                                                                                                                                     stroke-width="2"></circle></svg></span>
                                </div>
                                <div class="ant-steps-item-content">
                                    <div class="ant-steps-item-title">
                                        <div class="">Step 3</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>
                <div class="etn-onboard-action-btn-wrapper">
                    <div></div>
                    <div class="etn-onboard-action-btn-next">
                        <button type="button" class="ant-btn ant-btn-link etn-onboard-action-btn-link"><span>Skip this step</span></button>
                        <button type="button" class="tf-setup-next-btn"><span>Next</span></button>
                    </div>
                </div>
            </div>
			<?php
		}

		/**
		 * Setup step two
		 */
		public function setup_step_two() {
			?>
            <div class="tf-setup-step-container tf-setup-step-2">
                <section class="ant-layout etn-onboard-content-layout"><h1 class="ant-typography onboard-title">Settings</h1>
                    <div class="ant-steps ant-steps-horizontal etn-onboard-steps ant-steps-label-vertical">
                        <div class="ant-steps-item ant-steps-item-finish ant-steps-item-custom">
                            <div class="ant-steps-item-container">
                                <div class="ant-steps-item-tail"></div>
                                <div class="ant-steps-item-icon"><span class="ant-steps-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12"
                                                                                                                                                                                                     cy="12"
                                                                                                                                                                                                     r="12"
                                                                                                                                                                                                     fill="#5D5DFF"></circle><path
                                                    fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M17.7077 8.29352C18.0979 8.68439 18.0974 9.31755 17.7065 9.70773L11.703 15.7007C11.3123 16.0906 10.6796 16.0903 10.2894 15.7L7.29289 12.7036C6.90237 12.3131 6.90237 11.6799 7.29289 11.2894C7.68342 10.8988 8.31658 10.8988 8.70711 11.2894L10.9971 13.5794L16.2935 8.29227C16.6844 7.90209 17.3176 7.90265 17.7077 8.29352Z"
                                                    fill="white"></path></svg></span></div>
                                <div class="ant-steps-item-content">
                                    <div class="ant-steps-item-title">
                                        <div class="ant-steps-item-title-color">Step 1</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ant-steps-item ant-steps-item-process ant-steps-item-custom ant-steps-item-active">
                            <div class="ant-steps-item-container">
                                <div class="ant-steps-item-tail"></div>
                                <div class="ant-steps-item-icon"><span class="ant-steps-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12"
                                                                                                                                                                                                     cy="12"
                                                                                                                                                                                                     r="12"
                                                                                                                                                                                                     fill="#5D5DFF"></circle><circle
                                                    cx="12" cy="12" r="4" fill="white"></circle></svg></span></div>
                                <div class="ant-steps-item-content">
                                    <div class="ant-steps-item-title">
                                        <div class="ant-steps-item-title-color">Step 2</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ant-steps-item ant-steps-item-wait ant-steps-item-custom">
                            <div class="ant-steps-item-container">
                                <div class="ant-steps-item-tail"></div>
                                <div class="ant-steps-item-icon"><span class="ant-steps-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12"
                                                                                                                                                                                                     cy="12"
                                                                                                                                                                                                     r="11"
                                                                                                                                                                                                     stroke="#D8D9DF"
                                                                                                                                                                                                     stroke-width="2"></circle></svg></span>
                                </div>
                                <div class="ant-steps-item-content">
                                    <div class="ant-steps-item-title">
                                        <div class="">Step 3</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form class="ant-form ant-form-horizontal etn-onboard-setting-form">
                        <div class="ant-form-item etn-onboard-settings-form-item">
                            <div class="ant-row ant-form-item-row">
                                <div class="ant-col ant-col-12 ant-form-item-label ant-form-item-label-left"><label class="" title="Sell Ticket on Woocommerce">Sell Ticket on Woocommerce</label></div>
                                <div class="ant-col ant-form-item-control">
                                    <div class="ant-form-item-control-input">
                                        <div class="ant-form-item-control-input-content">
                                            <button name="sell_tickets" type="button" role="switch" aria-checked="false" class="ant-switch" style="background-color: rgb(255, 74, 151);">
                                                <div class="ant-switch-handle"></div>
                                                <span class="ant-switch-inner"></span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ant-form-item etn-onboard-settings-form-item">
                            <div class="ant-row ant-form-item-row">
                                <div class="ant-col ant-col-12 ant-form-item-label ant-form-item-label-left"><label class="" title="Set Date Format">Set Date Format</label></div>
                                <div class="ant-col ant-col-offset-6 ant-form-item-control">
                                    <div class="ant-form-item-control-input">
                                        <div class="ant-form-item-control-input-content">
                                            <div class="ant-select ant-select-in-form-item ant-select-single ant-select-show-arrow" name="date_format">
                                                <div class="ant-select-selector"><span class="ant-select-selection-search"><input type="search" autocomplete="off" class="ant-select-selection-search-input"
                                                                                                                                  role="combobox" aria-haspopup="listbox" aria-owns="rc_select_0_list"
                                                                                                                                  aria-autocomplete="list" aria-controls="rc_select_0_list"
                                                                                                                                  aria-activedescendant="rc_select_0_list_0" readonly="" unselectable="on"
                                                                                                                                  value="" id="rc_select_0" style="opacity: 0;"></span><span
                                                            class="ant-select-selection-placeholder">Select</span></div>
                                                <span class="ant-select-arrow" unselectable="on" aria-hidden="true" style="user-select: none;"><span role="img" aria-label="down"
                                                                                                                                                     class="anticon anticon-down ant-select-suffix"><svg
                                                                viewBox="64 64 896 896" focusable="false" data-icon="down" width="1em" height="1em" fill="currentColor" aria-hidden="true"><path
                                                                    d="M884 256h-75c-5.1 0-9.9 2.5-12.9 6.6L512 654.2 227.9 262.6c-3-4.1-7.8-6.6-12.9-6.6h-75c-6.5 0-10.3 7.4-6.5 12.7l352.6 486.1c12.8 17.6 39 17.6 51.7 0l352.6-486.1c3.9-5.3.1-12.7-6.4-12.7z"></path></svg></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ant-form-item etn-onboard-settings-form-item">
                            <div class="ant-row ant-form-item-row">
                                <div class="ant-col ant-col-12 ant-form-item-label ant-form-item-label-left"><label class="" title="Set Time Format">Set Time Format</label></div>
                                <div class="ant-col ant-col-offset-6 ant-form-item-control">
                                    <div class="ant-form-item-control-input">
                                        <div class="ant-form-item-control-input-content">
                                            <div class="ant-select ant-select-in-form-item ant-select-single ant-select-show-arrow" name="time_format">
                                                <div class="ant-select-selector"><span class="ant-select-selection-search"><input type="search" autocomplete="off" class="ant-select-selection-search-input"
                                                                                                                                  role="combobox" aria-haspopup="listbox" aria-owns="rc_select_1_list"
                                                                                                                                  aria-autocomplete="list" aria-controls="rc_select_1_list"
                                                                                                                                  aria-activedescendant="rc_select_1_list_0" readonly="" unselectable="on"
                                                                                                                                  value="" id="rc_select_1" style="opacity: 0;"></span><span
                                                            class="ant-select-selection-item" title="12h">12h</span></div>
                                                <span class="ant-select-arrow" unselectable="on" aria-hidden="true" style="user-select: none;"><span role="img" aria-label="down"
                                                                                                                                                     class="anticon anticon-down ant-select-suffix"><svg
                                                                viewBox="64 64 896 896" focusable="false" data-icon="down" width="1em" height="1em" fill="currentColor" aria-hidden="true"><path
                                                                    d="M884 256h-75c-5.1 0-9.9 2.5-12.9 6.6L512 654.2 227.9 262.6c-3-4.1-7.8-6.6-12.9-6.6h-75c-6.5 0-10.3 7.4-6.5 12.7l352.6 486.1c12.8 17.6 39 17.6 51.7 0l352.6-486.1c3.9-5.3.1-12.7-6.4-12.7z"></path></svg></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ant-form-item etn-onboard-settings-form-item">
                            <div class="ant-row ant-form-item-row">
                                <div class="ant-col ant-col-12 ant-form-item-label ant-form-item-label-left"><label class="" title="Set Primary Color">Set Primary Color</label></div>
                                <div class="ant-col ant-col-offset-8 ant-form-item-control">
                                    <div class="ant-form-item-control-input">
                                        <div class="ant-form-item-control-input-content">
                                            <div class="etn-onboard-primary-color">
                                                <div style="min-width: 20px; border-radius: 2px; background: rgb(93, 93, 255);"></div>
                                                <span class="etn-onboard-color-picker-hex">#5D5DFF</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ant-form-item etn-onboard-settings-form-item">
                            <div class="ant-row ant-form-item-row">
                                <div class="ant-col ant-col-12 ant-form-item-label ant-form-item-label-left"><label class="" title="Set secondary Color">Set secondary Color</label></div>
                                <div class="ant-col ant-col-offset-8 ant-form-item-control">
                                    <div class="ant-form-item-control-input">
                                        <div class="ant-form-item-control-input-content">
                                            <div class="etn-onboard-primary-color">
                                                <div style="min-width: 20px; border-radius: 2px; background: rgb(255, 74, 151);"></div>
                                                <span class="etn-onboard-color-picker-hex">#FF4A97</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
                <div class="etn-onboard-action-btn-wrapper">
                    <button type="button" class="ant-btn ant-btn-default ant-btn-lg etn-onboard-action-btn onboard-prev-btn"><span>Previous</span></button>
                    <div class="etn-onboard-action-btn-next">
                        <button type="button" class="ant-btn ant-btn-link etn-onboard-action-btn-link"><span>Skip this step</span></button>
                        <button type="button" class="ant-btn ant-btn-primary ant-btn-lg etn-onboard-action-btn etn-onboard-action-btn-primary" ant-click-animating-without-extra-node="false"><span>Next</span>
                        </button>
                    </div>
                </div>
            </div>
			<?php
		}

		/**
		 * Finish step
		 */
		public function tf_setup_finish_step() {
			?>
            <div class="tf-setup-step-container tf-setup-step-3">
                <section class="ant-layout etn-onboard-content-layout"><h1 class="ant-typography onboard-title">Eventin is Ready to
                        Go!</h1>
                    <div class="ant-steps ant-steps-horizontal etn-onboard-steps ant-steps-label-vertical">
                        <div class="ant-steps-item ant-steps-item-finish ant-steps-item-custom">
                            <div class="ant-steps-item-container">
                                <div class="ant-steps-item-tail"></div>
                                <div class="ant-steps-item-icon"><span class="ant-steps-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12"
                                                                                                                                                                                                     cy="12"
                                                                                                                                                                                                     r="12"
                                                                                                                                                                                                     fill="#5D5DFF"></circle><path
                                                    fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M17.7077 8.29352C18.0979 8.68439 18.0974 9.31755 17.7065 9.70773L11.703 15.7007C11.3123 16.0906 10.6796 16.0903 10.2894 15.7L7.29289 12.7036C6.90237 12.3131 6.90237 11.6799 7.29289 11.2894C7.68342 10.8988 8.31658 10.8988 8.70711 11.2894L10.9971 13.5794L16.2935 8.29227C16.6844 7.90209 17.3176 7.90265 17.7077 8.29352Z"
                                                    fill="white"></path></svg></span></div>
                                <div class="ant-steps-item-content">
                                    <div class="ant-steps-item-title">
                                        <div class="ant-steps-item-title-color">Step 1</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ant-steps-item ant-steps-item-finish ant-steps-item-custom">
                            <div class="ant-steps-item-container">
                                <div class="ant-steps-item-tail"></div>
                                <div class="ant-steps-item-icon"><span class="ant-steps-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12"
                                                                                                                                                                                                     cy="12"
                                                                                                                                                                                                     r="12"
                                                                                                                                                                                                     fill="#5D5DFF"></circle><path
                                                    fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M17.7077 8.29352C18.0979 8.68439 18.0974 9.31755 17.7065 9.70773L11.703 15.7007C11.3123 16.0906 10.6796 16.0903 10.2894 15.7L7.29289 12.7036C6.90237 12.3131 6.90237 11.6799 7.29289 11.2894C7.68342 10.8988 8.31658 10.8988 8.70711 11.2894L10.9971 13.5794L16.2935 8.29227C16.6844 7.90209 17.3176 7.90265 17.7077 8.29352Z"
                                                    fill="white"></path></svg></span></div>
                                <div class="ant-steps-item-content">
                                    <div class="ant-steps-item-title">
                                        <div class="ant-steps-item-title-color">Step 2</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ant-steps-item ant-steps-item-process ant-steps-item-custom ant-steps-item-active">
                            <div class="ant-steps-item-container">
                                <div class="ant-steps-item-tail"></div>
                                <div class="ant-steps-item-icon"><span class="ant-steps-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12"
                                                                                                                                                                                                     cy="12"
                                                                                                                                                                                                     r="12"
                                                                                                                                                                                                     fill="#5D5DFF"></circle><circle
                                                    cx="12" cy="12" r="4" fill="white"></circle></svg></span></div>
                                <div class="ant-steps-item-content">
                                    <div class="ant-steps-item-title">
                                        <div class="ant-steps-item-title-color">Step 3</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="etn-onboard-import-btn-wrapper">
                        <div class="ant-row ant-row-center etn-onboard-import-content" style="margin-left: -8px; margin-right: -8px; row-gap: 16px;">
                            <button type="button" class="ant-btn ant-btn-default ant-btn-lg etn-onboard-action-btn etn-onboard-import-btn"><span>Create new Speaker</span></button>
                        </div>
                        <div class="ant-row ant-row-center etn-onboard-import-content" style="margin-left: -8px; margin-right: -8px; row-gap: 16px;">
                            <button type="button" class="ant-btn ant-btn-default ant-btn-lg etn-onboard-action-btn etn-onboard-import-btn"><span>Create new Schedule</span></button>
                        </div>
                        <div class="ant-row ant-row-center etn-onboard-import-content" style="margin-left: -8px; margin-right: -8px; row-gap: 16px;">
                            <button type="button" class="ant-btn ant-btn-default ant-btn-lg etn-onboard-action-btn etn-onboard-import-btn"><span>Create new Event</span></button>
                        </div>
                    </div>
                </section>
                <div class="etn-onboard-action-btn-wrapper">
                    <button type="button" class="ant-btn ant-btn-default ant-btn-lg etn-onboard-action-btn onboard-prev-btn"><span>Previous</span></button>
                    <div class="etn-onboard-action-btn-next">
                        <button type="button" class="ant-btn ant-btn-link etn-onboard-action-btn-link"><span>Skip this step</span></button>
                        <button type="button" class="ant-btn ant-btn-default ant-btn-lg etn-onboard-action-btn etn-onboard-action-btn-primary" ant-click-animating-without-extra-node="false">
                            <span>Finish Setup</span></button>
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


	}
}

TF_Setup_Wizard::instance();