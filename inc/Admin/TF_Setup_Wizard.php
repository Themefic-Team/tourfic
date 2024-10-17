<?php

namespace Tourfic\Admin;
use Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;

/**
 * Setup Wizard Class
 * @since 2.9.3
 * @author Foysal
 */
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
		add_action( 'admin_enqueue_scripts', array( $this, 'tf_setup_wizard_admin_enqueue_scripts' ), 9 );

		add_action( 'wp_ajax_tf_ajax_install_woo', 'wp_ajax_install_plugin' );
		add_action( 'wp_ajax_tf_ajax_activate_woo', array( $this, 'tf_ajax_activate_woo_callback' ) );
		add_action( 'wp_ajax_tf_theme_installing', 'wp_ajax_install_theme' );
		add_action( 'wp_ajax_tf_travelfic_toolkit_installing', 'wp_ajax_install_plugin' );
		add_action( 'wp_ajax_tf_travelfic_toolkit_activate', array( $this, 'tf_travelfic_toolkit_activate_callabck' ) );
		add_action( 'wp_ajax_tf_setup_travelfic_theme_active', array( $this, 'tf_setup_travelfic_theme_active_callabck' ) );


		self::$current_step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : 'welcome';
	}

	/**
	 * Add wizard submenu
	 */
	public function tf_wizard_menu() {

		if ( current_user_can( 'manage_options' ) ) {
			$tf_settings_parentmenu = ! empty( $_GET['page'] ) && "tf-setup-wizard" == $_GET['page'] ? 'tf_settings' : '';
			add_submenu_page(
				$tf_settings_parentmenu,
				esc_html__( 'TF Setup Wizard', 'tourfic' ),
				esc_html__( 'TF Setup Wizard', 'tourfic' ),
				'manage_options',
				'tf-setup-wizard',
				[ $this, 'tf_wizard_page' ],
				99
			);
		}
	}

	public function tf_setup_wizard_admin_enqueue_scripts( $screen ) {
		if ( ! empty( $screen ) && 'tourfic-settings_page_tf-setup-wizard' == $screen ) {
			wp_enqueue_style( 'travelfic-toolkit-fonts', '//fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700&display=swap', array(), '2.11.9' );
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
                        <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ) . 'images/welcome.png' ?>" alt="<?php esc_attr_e( 'Welcome to Tourfic!', 'tourfic' ) ?>">
                    </div>
                    <div class="tf-setup-header-right">
                        <span class="get-help-link"><?php esc_html_e( 'Having troubles?', 'tourfic' ) ?> <a class="" target="_blank"
                                                                                                            href="https://portal.themefic.com/support/"><?php esc_html_e( 'Get help', 'torufic' ) ?></a></span>
                    </div>
                </div>
                <form method="post" id="tf-setup-wizard-form" data-skip-steps="">
					<?php
					$this->tf_setup_welcome_step();
					$this->tf_setup_step_one();
					$this->tf_setup_step_two();
					$this->tf_setup_step_three();
					$this->tf_setup_step_four();
					$this->tf_setup_step_five();
					$this->tf_setup_step_six();
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
        <div class="tf-setup-content-layout tf-welcome-step tf-setup-step-0 <?php echo self::$current_step == 'welcome' ? 'active' : ''; ?>">
            <div class="back-to-dashboard">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=tf_settings' ) ); ?>" class="tf-back-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 19L5 12L12 5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 12H5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span><?php esc_html_e( 'Back', 'tourfic' ) ?></span>
                </a>
            </div>
            <div class="setup-content-warper">
                <div class="welcome-img"><img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ) . 'images/welcome.png' ?>" alt="<?php esc_attr_e( 'Welcome to Tourfic!', 'tourfic' ) ?>"></div>
                <h1 class="tf-setup-welcome-title"><?php esc_html_e( 'Welcome to Tourfic!', 'tourfic' ) ?></h1>
                <div class="tf-setup-welcome-description"><?php esc_html_e( 'This quick setup wizard is simple and straightforward and shouldn’t take longer than five minutes. It will help you configure the basic settings of Tourfic to get started. Please note that this setup guide is entirely optional.', 'tourfic' ) ?></div>
                <div class="tf-setup-welcome-footer">
                    <button type="button" class="tf-quick-setup-btn tf-setup-start-btn">
                        <span><?php esc_html_e( 'Get Started', 'tourfic' ) ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M5 12H19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 5L19 12L12 19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Setup step one
	 *
	 * WooCommerce Check
	 */
	private function tf_setup_step_one() {
		if ( current_user_can( 'activate_plugins' ) ) {
			if ( ! file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) || ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				?>
                <div class="tf-setup-step-container tf-setup-step-1 <?php echo self::$current_step == 'step_1' ? 'active' : ''; ?>" data-step="1">
                    <div class="back-to-dashboard">
                        <a href="#" class="tf-back-btn tf-setup-prev-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M12 19L5 12L12 5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19 12H5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span><?php esc_html_e( 'Back', 'tourfic' ) ?></span>
                        </a>
                    </div>
                    <section class="tf-setup-step-layout tf-setup-woocommerce-step">
						<?php $this->tf_setup_wizard_steps_header() ?>
                        <div class="welcome-img"><img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ) . 'images/woocommerce.png' ?>" alt="<?php esc_attr_e( 'Woocommerce', 'tourfic' ) ?>"></div>
                        <h1 class="tf-setup-step-title"><?php esc_html_e( 'Install WooCommerce', 'tourfic' ) ?></h1>
                        <p class="tf-setup-step-desc"><?php esc_html_e( 'Tourfic requires WooCommerce to be installed and activated.', 'tourfic' ) ?></p>

                        <div class="tf-setup-action-btn-wrapper">
                            <div class="tf-setup-action-btn-next">
								<?php if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) && ! file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) : ?>
                                    <button type="button" class="tf-install-woo-btn tf-quick-setup-btn" data-install="woocommerce">
                                        <span><?php esc_html_e( 'Install WooCommerce', 'tourfic' ) ?></span>
                                    </button>

                                    <button type="button" class="tf-active-woo-btn tf-quick-setup-btn" style="display: none;">
                                        <span><?php esc_html_e( 'WooCommerce Active', 'tourfic' ) ?></span>
                                    </button>
								<?php elseif ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) && file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ) : ?>
                                    <button type="button" class="tf-active-woo-btn tf-quick-setup-btn" data-install="woocommerce">
                                        <span><?php esc_html_e( 'Activate WooCommerce', 'tourfic' ) ?></span>
                                    </button>
								<?php endif; ?>

                                <button type="button" class="tf-setup-next-btn tf-quick-setup-btn" style="display: none">
                                    <span><?php esc_html_e( 'Next', 'tourfic' ) ?></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M5 12H19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 5L19 12L12 19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
				<?php
			}
		}
	}

	/**
	 * Setup step two
	 *
	 * Service Type
	 */
	private function tf_setup_step_two() {
		$tf_disable_services = ! empty( Helper::tfopt( 'disable-services' ) ) ? Helper::tfopt( 'disable-services' ) : '';
		?>
        <div class="tf-setup-step-container tf-setup-step-2 <?php echo self::$current_step == 'step_2' ? 'active' : ''; ?>" data-step="2">
            <div class="back-to-dashboard">
                <a href="#" class="tf-back-btn tf-setup-prev-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 19L5 12L12 5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 12H5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span><?php esc_html_e( 'Back', 'tourfic' ) ?></span>
                </a>
            </div>
            <section class="tf-setup-step-layout">
				<?php $this->tf_setup_wizard_steps_header() ?>
                <h1 class="tf-setup-step-title"><?php esc_html_e( 'Select your Service Type', 'tourfic' ) ?></h1>
                <p class="tf-setup-step-desc"><?php esc_html_e( 'You can choose anyone or all of them', 'tourfic' ) ?></p>
                <ul class="tf-select-service">
                    <li>
                        <input type="checkbox" id="tf-hotel" name="tf-services[]"
                               value="hotel" <?php echo empty( $tf_disable_services ) || ! in_array( 'hotel', $tf_disable_services ) ? esc_attr( 'checked' ) : ''; ?>/>
                        <label for="tf-hotel">
                            <div class="tf-inactive">
                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ) . 'images/hotel.png' ?>" alt="<?php esc_attr_e( 'Hotel', 'tourfic' ) ?>">
                            </div>
                            <div class="tf-active">
                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ) . 'images/active-hotel.png' ?>" alt="<?php esc_attr_e( 'Hotel', 'tourfic' ) ?>">
                            </div>
                            <span><?php esc_html_e( 'Hotel', 'tourfic' ) ?></span>
                        </label>
                    </li>
                    <li>
                        <input type="checkbox" id="tf-tour" name="tf-services[]" value="tour" <?php echo empty( $tf_disable_services ) || ! in_array( 'tour', $tf_disable_services ) ? esc_attr( 'checked' ) : ''; ?>/>
                        <label for="tf-tour">
                            <div class="tf-inactive">
                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ) . 'images/tour.png' ?>" alt="<?php esc_attr_e( 'Tour', 'tourfic' ) ?>">
                            </div>
                            <div class="tf-active">
                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ) . 'images/active-tour.png' ?>" alt="<?php esc_attr_e( 'Tour', 'tourfic' ) ?>">
                            </div>
                            <span><?php esc_html_e( 'Tour', 'tourfic' ) ?></span>
                        </label>
                    </li>
                    <li>
                        <input type="checkbox" id="tf-apartment" name="tf-services[]"
                               value="apartment" <?php echo empty( $tf_disable_services ) || ! in_array( 'apartment', $tf_disable_services ) ? esc_attr( 'checked' ) : ''; ?>/>
                        <label for="tf-apartment">
                            <div class="tf-inactive">
                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ) . 'images/apartment.png' ?>" alt="<?php esc_attr_e( 'Apartment', 'tourfic' ) ?>">
                            </div>
                            <div class="tf-active">
                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ) . 'images/active-apartment.png' ?>" alt="<?php esc_attr_e( 'Apartment', 'tourfic' ) ?>">
                            </div>
                            <span><?php esc_html_e( 'Apartment', 'tourfic' ) ?></span>
                        </label>
                    </li>
                </ul>
                <div class="tf-setup-action-btn-wrapper">
                    <div class="tf-setup-action-btn-next">
                        <?php
                        $tf_current_active_theme = !empty(get_option('stylesheet')) ? get_option('stylesheet') : 'No'; 

                        if ( $tf_current_active_theme != 'travelfic' && $tf_current_active_theme != 'travelfic-child' && $tf_current_active_theme != 'ultimate-hotel-booking' && $tf_current_active_theme != 'ultimate-hotel-booking-child' ) {
                        ?>
                            <button type="button" class="tf-setup-skip-btn tf-link-skip-btn"><?php esc_html_e( 'Skip', 'tourfic' ) ?></button>
                            <button type="button" class="tf-setup-next-btn tf-quick-setup-btn">
                                <span><?php esc_html_e( 'Next', 'tourfic' ) ?></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 12H19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 5L19 12L12 19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        <?php }else{ ?>
                            <button type="button" class="tf-quick-setup-btn tf-setup-travelfic-toolkit-btn" data-install="travelfic-toolkit">
                                <span><?php esc_html_e( 'Template Library', 'tourfic' ) ?></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 12H19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 5L19 12L12 19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </section>

        </div>
		<?php
	}

	/**
	 * Setup step three
	 *
	 * Travelfic Theme
	 */
	private function tf_setup_step_three() {
		?>
        <div class="tf-setup-step-container tf-setup-step-3 <?php echo self::$current_step == 'step_3' ? 'active' : ''; ?>" data-step="3">
            <div class="back-to-dashboard">
                <a href="#" class="tf-back-btn tf-setup-prev-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 19L5 12L12 5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 12H5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span><?php esc_html_e( 'Back', 'tourfic' ) ?></span>
                </a>
            </div>
            <section class="tf-setup-step-layout">
				<?php $this->tf_setup_wizard_steps_header( 2 ) ?>
                <div class="tf-theme-setting-heading">
                    <h1 class="tf-setup-step-title"><?php esc_html_e( 'Travelfic has some Ready to Use Templates for you', 'tourfic' ) ?></h1>
                    <div class="tf-setup-title-shape">
                        <svg xmlns="http://www.w3.org/2000/svg" width="225" height="70" viewBox="0 0 225 70" fill="none">
                            <g filter="url(#filter0_d_92_10373)">
                                <path d="M9 17C28.9996 25.8411 90.7139 19.8131 117.998 19.8131C173.778 19.8131 218.496 23.4299 218.996 39.9065C219.496 56.3832 173.778 60 117.998 60C81.0373 60 41.6647 60 23.9997 54.1542C12 50.1832 7 33.8785 48.9992 26.6449"
                                      stroke="#FFC100" stroke-width="4" stroke-linecap="round"/>
                            </g>
                            <defs>
                                <filter id="filter0_d_92_10373" x="2.99951" y="14.999" width="222.001" height="55.001" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                    <feOffset dy="4"/>
                                    <feGaussianBlur stdDeviation="2"/>
                                    <feComposite in2="hardAlpha" operator="out"/>
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.433333 0 0 0 0 0.325 0 0 0 0 0 0 0 0 0.2 0"/>
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_92_10373"/>
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_92_10373" result="shape"/>
                                </filter>
                            </defs>
                        </svg>
                    </div>
                </div>
                <p class="tf-setup-step-desc"><?php esc_html_e( "Travelfic is a WordPress theme created by us to improve your site’s frontend. While Tourfic manages your backend and booking, Travelfic ensures your travel site looks great and operates smoothly. ", "tourfic" ) ?>
                    <a href="https://wordpress.org/themes/travelfic/" target="_blank"><?php esc_html_e( "Know more about Travelfic", "tourfic" ); ?></a></p>
                <h4 class="tf-select-title"><?php esc_html_e("Select theme from our library", "tourfic"); ?></h4>    

                <div class="tf-template-selection">
                    <div class="tf-single-theme">
                        <label>
                            <input type="radio" value="<?php echo esc_attr('travelfic'); ?>" name="tf_theme_select" checked>
                            <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ) . 'images/travelfic-theme.png' ?>" alt="<?php esc_attr_e( 'Travelfic Theme', 'tourfic' ) ?>">
                            <div class="checked-svg">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="#E6FAEE" stroke="#21A159" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 12L11 14L15 10" stroke="#21A159" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h4><?php esc_html_e("Travelfic", "tourfic"); ?></h4>
                        </label>
                    </div>
                    <div class="tf-single-theme">
                        <label>
                            <input type="radio" value="<?php echo esc_attr('ultimate-hotel-booking'); ?>" name="tf_theme_select">
                            <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ) . 'images/ultimate-hotel-theme.png' ?>" alt="<?php esc_attr_e( 'Travelfic Theme', 'tourfic' ) ?>">
                            <div class="checked-svg">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="#E6FAEE" stroke="#21A159" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 12L11 14L15 10" stroke="#21A159" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h4><?php esc_html_e("Ultimate Hotel Booking ", "tourfic"); ?></h4>
                        </label>
                    </div>
                </div>

                <div class="tf-setup-action-btn-wrapper">

                    <div class="tf-setup-action-btn-next">
                        <button type="button" class="tf-setup-skip-btn tf-link-skip-btn tf-theme-activation-btn"><?php esc_html_e( 'Keep Existing Theme', 'tourfic' ) ?></button>
                        <button type="button" class="tf-setup-travelfic-theme-btn tf-quick-setup-btn tf-theme-activation-btn">
                            <span><?php esc_html_e( 'Next', 'tourfic' ) ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M5 12H19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 5L19 12L12 19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        <button type="button" class="tf-setup-travelfic-theme-active" style="display: none;">
                            <span><?php esc_html_e( 'Travelfic Active', 'tourfic' ) ?></span>
                        </button>

                        <button type="button" class="tf-setup-travelfic-toolkit-btn" data-install="travelfic-toolkit" style="display: none;">
                            <span><?php esc_html_e( 'Travelfic Toolklit', 'tourfic' ) ?></span>
                        </button>

                        <button type="button" class="tf-setup-travelfic-toolkit-active" data-install="travelfic-toolkit" style="display: none;">
                            <span><?php esc_html_e( 'Travelfic Toolklit Active', 'tourfic' ) ?></span>
                        </button>
                    </div>
                </div>

            </section>

        </div>
		<?php
	}

	/**
	 * Setup step four
	 *
	 * General Settings
	 */
	private function tf_setup_step_four() {
		$tf_search_result_page = ! empty( Helper::tfopt( 'search-result-page' ) ) ? Helper::tfopt( 'search-result-page' ) : '';
		$tf_search_result      = ! empty( Helper::tfopt( 'posts_per_page' ) ) ? Helper::tfopt( 'posts_per_page' ) : 10;
		$tf_wishlist_page      = ! empty( Helper::tfopt( 'wl-page' ) ) ? Helper::tfopt( 'wl-page' ) : '';
		$tf_review_autopublish = ! empty( Helper::tfopt( 'r-auto-publish' ) ) ? Helper::tfopt( 'r-auto-publish' ) : '';
		?>
        <div class="tf-setup-step-container tf-setup-step-4 <?php echo self::$current_step == 'step_4' ? 'active' : ''; ?>" data-step="4">
            <div class="back-to-dashboard">
                <a href="#" class="tf-back-btn tf-setup-prev-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 19L5 12L12 5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 12H5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span><?php esc_html_e( 'Back', 'tourfic' ) ?></span>
                </a>
            </div>
            <section class="tf-setup-step-layout">
				<?php $this->tf_setup_wizard_steps_header( 3 ) ?>
                <h1 class="tf-setup-step-title"><?php esc_html_e( 'General Settings', 'tourfic' ) ?></h1>
                <p class="tf-setup-step-desc"><?php esc_html_e( 'From here you can customize your website according to your need', 'tourfic' ) ?></p>

                <div class="setup-form-group">

                    <div class="tf-setup-form-item">
                        <div class="tf-setup-form-item-label"><label class=""><?php esc_html_e( 'Select Search Result Page', 'tourfic' ) ?></label></div>
                        <div class="tf-setup-form-item-input">
                            <select name="tf-search-result-page" id="tf-search-result-page">
                                <option value=""><?php esc_html_e( 'Select a page', 'tourfic' ) ?></option>
								<?php
								$pages              = get_pages();
								$search_result_page = ! empty( $tf_search_result_page ) ? $tf_search_result_page : get_option( 'tf_search_page_id' );
								foreach ( $pages as $page ) {
									echo '<option value="' . esc_attr( $page->ID ) . '" ' . selected( $search_result_page, $page->ID, false ) . '>' . esc_html( $page->post_title ) . '</option>';
								}
								?>
                            </select>
                        </div>
                    </div>

                    <!--Search result posts per page-->
                    <div class="tf-setup-form-item">
                        <div class="tf-setup-form-item-label"><label class=""><?php esc_html_e( 'Posts Per Page on Search Result', 'tourfic' ) ?></label></div>
                        <div class="tf-setup-form-item-input">
                            <input type="number" name="tf-search-result-posts-per-page" id="tf-search-result-posts-per-page" value="<?php echo esc_attr( $tf_search_result ); ?>">
                        </div>
                    </div>

                    <!--wishlist page-->
                    <div class="tf-setup-form-item">
                        <div class="tf-setup-form-item-label"><label class=""><?php esc_html_e( 'Select Wishlist Page', 'tourfic' ) ?></label></div>
                        <div class="tf-setup-form-item-input">
                            <select name="tf-wishlist-page" id="tf-wishlist-page">
                                <option value=""><?php esc_html_e( 'Select a page', 'tourfic' ) ?></option>
								<?php
								$pages         = get_pages();
								$wishlist_page = ! empty( $tf_wishlist_page ) ? $tf_wishlist_page : get_option( 'tf_wishlist_page_id' );
								foreach ( $pages as $page ) {
									echo '<option value="' . esc_attr( $page->ID ) . '" ' . selected( $wishlist_page, $page->ID, false ) . '>' . esc_html( $page->post_title ) . '</option>';
								}
								?>
                            </select>
                        </div>
                    </div>

                    <!--Auto Publish Review-->
                    <div class="tf-setup-form-item tf-setup-form-item-inline tf-auto-publish-field">
                        <div class="tf-setup-form-item-label"><label class="" for="tf-auto-publish-review"><?php esc_html_e( 'Auto Publish Review', 'tourfic' ) ?></label></div>
                        <div class="tf-setup-form-item-input">
                            <label for="tf-auto-publish-review" class="tf-switch-label">
                                <input type="checkbox" id="tf-auto-publish-review" name="tf-auto-publish-review" value="<?php echo ! empty( $tf_review_autopublish ) ? esc_attr( '1' ) : ''; ?>"
                                       class="tf-switch" <?php echo ! empty( $tf_review_autopublish ) ? esc_attr( 'checked' ) : ''; ?>/>
                                <span class="tf-switch-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="tf-setup-action-btn-wrapper">

                    <div class="tf-setup-action-btn-next">
                        <button type="button" class="tf-setup-skip-btn tf-link-skip-btn"><?php esc_html_e( 'Skip', 'tourfic' ) ?></button>
                        <button type="button" class="tf-setup-next-btn tf-quick-setup-btn">
                            <span><?php esc_html_e( 'Next', 'tourfic' ) ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M5 12H19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 5L19 12L12 19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </section>

        </div>
		<?php
	}

	/**
	 * Setup step five
	 *
	 * Hotel, Tour, Apartment Settings
	 */
	private function tf_setup_step_five() {
		$tf_hotel_review     = ! empty( Helper::tfopt( 'h-review' ) ) ? Helper::tfopt( 'h-review' ) : '';
		$tf_hotel_share      = ! empty( Helper::tfopt( 'h-share' ) ) ? Helper::tfopt( 'h-share' ) : '';
		$tf_hotel_slug       = ! empty( get_option( 'hotel_slug' ) ) ? get_option( 'hotel_slug' ) : 'hotels';
		$tf_tour_review      = ! empty( Helper::tfopt( 't-review' ) ) ? Helper::tfopt( 't-review' ) : '';
		$tf_tour_related     = ! empty( Helper::tfopt( 't-related' ) ) ? Helper::tfopt( 't-related' ) : '';
		$tf_tour_slug        = ! empty( get_option( 'tour_slug' ) ) ? get_option( 'tour_slug' ) : 'tours';
		$tf_apartment_review = ! empty( Helper::tfopt( 'disable-apartment-review' ) ) ? Helper::tfopt( 'disable-apartment-review' ) : '';
		$tf_apartment_share  = ! empty( Helper::tfopt( 'disable-apartment-share' ) ) ? Helper::tfopt( 'disable-apartment-share' ) : '';
		$tf_apartment_slug   = ! empty( get_option( 'apartment_slug' ) ) ? get_option( 'apartment_slug' ) : 'apartments';
		?>
        <div class="tf-setup-step-container tf-setup-step-5 <?php echo self::$current_step == 'step_5' ? 'active' : ''; ?>" data-step="5">
            <div class="back-to-dashboard">
                <a href="#" class="tf-back-btn tf-setup-prev-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 19L5 12L12 5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 12H5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span><?php esc_html_e( 'Back', 'tourfic' ) ?></span>
                </a>
            </div>
            <section class="tf-setup-step-layout">
				<?php $this->tf_setup_wizard_steps_header( 4 ) ?>
                <h1 class="tf-setup-step-title"><?php esc_html_e( 'General Settings', 'tourfic' ) ?></h1>
                <p class="tf-setup-step-desc"><?php esc_html_e( 'From here you can customize your website according to your need', 'tourfic' ) ?></p>

                <div class="tf-hotel-setup-wizard">
                    <h3 class="tf-setup-step-subtitle"><?php esc_html_e( 'Hotel settings', 'tourfic' ) ?></h3>
                    <p class="tf-setup-step-desc"><?php echo wp_kses_post( 'These settings can be overridden from <span>Tourfic Settings > Hotel Settings</span>' ) ?></p>

                    <div class="setup-form-group tf-setup-group-general">
                        <!--Review Section-->
                        <div class="tf-setup-form-item tf-setup-form-item-inline">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-hotel-review-section"><?php esc_html_e( 'Review Section', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-hotel-review-section" class="tf-switch-label">
                                    <input type="checkbox" id="tf-hotel-review-section" name="tf-hotel-review-section" value="<?php echo empty( $tf_hotel_review ) ? esc_attr( '1' ) : ''; ?>"
                                           class="tf-switch" <?php echo empty( $tf_hotel_review ) ? esc_attr( 'checked' ) : ''; ?>/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Share Option-->
                        <div class="tf-setup-form-item tf-setup-form-item-inline">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-hotel-share-option"><?php esc_html_e( 'Share Option', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-hotel-share-option" class="tf-switch-label">
                                    <input type="checkbox" id="tf-hotel-share-option" name="tf-hotel-share-option" value="<?php echo empty( $tf_hotel_share ) ? esc_attr( '1' ) : ''; ?>"
                                           class="tf-switch" <?php echo empty( $tf_hotel_share ) ? esc_attr( 'checked' ) : ''; ?>/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Demo Data Import Section-->
                        <div class="tf-setup-form-item-wrap">
                            <div class="tf-setup-form-item tf-setup-form-item-inline">
                                <div class="tf-setup-form-item-label"><label class="" for="tf-hotel-demo-data-import"><?php esc_html_e( 'Import Demo Hotels', 'tourfic' ) ?></label></div>
                                <div class="tf-setup-form-item-input">
                                    <label for="tf-hotel-demo-data-import" class="tf-switch-label">
                                        <input type="checkbox" id="tf-hotel-demo-data-import" name="tf-hotel-demo-data-import" value="" class="tf-switch"/>
                                        <span class="tf-switch-slider"></span>
                                    </label>
                                </div>
                            </div>
                            <span class="tf-setup-field-desc"><?php echo esc_html__( 'Enabling this feature will add some sample hotels to your website', 'tourfic' ) ?></span>
                        </div>

                        <!--Hotel Permalink-->
                        <div class="tf-setup-form-item tf-setup-form-item-inline tf-setup-permalink">
                            <div class="tf-setup-form-item-label"><label class=""><?php esc_html_e( 'Hotel Permalink', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <input type="text" name="tf-hotel-permalink" id="tf-hotel-permalink" value="<?php echo esc_attr( $tf_hotel_slug ); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tf-tour-setup-wizard">
                    <h3 class="tf-setup-step-subtitle"><?php esc_html_e( 'Tour settings', 'tourfic' ) ?></h3>
                    <p class="tf-setup-step-desc"><?php echo wp_kses_post( 'These settings can be overridden from <span>Tourfic Settings > Tour Settings</span>' ) ?></p>

                    <div class="setup-form-group  tf-setup-group-general">
                        <!--Review Section-->
                        <div class="tf-setup-form-item tf-setup-form-item-inline">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-tour-review-section"><?php esc_html_e( 'Review Section', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-tour-review-section" class="tf-switch-label">
                                    <input type="checkbox" id="tf-tour-review-section" name="tf-tour-review-section" value="<?php echo empty( $tf_tour_review ) ? esc_attr( '1' ) : ''; ?>"
                                           class="tf-switch" <?php echo empty( $tf_tour_review ) ? esc_attr( 'checked' ) : ''; ?>/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Related Section-->
                        <div class="tf-setup-form-item tf-setup-form-item-inline">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-tour-related-section"><?php esc_html_e( 'Related Section', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-tour-related-section" class="tf-switch-label">
                                    <input type="checkbox" id="tf-tour-related-section" name="tf-tour-related-section" value="<?php echo empty( $tf_tour_related ) ? esc_attr( '1' ) : ''; ?>"
                                           class="tf-switch" <?php echo empty( $tf_tour_related ) ? esc_attr( 'checked' ) : ''; ?>/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Demo Data Import Section-->
                        <div class="tf-setup-form-item-wrap">
                            <div class="tf-setup-form-item tf-setup-form-item-inline">
                                <div class="tf-setup-form-item-label"><label class="" for="tf-tour-demo-data-import"><?php esc_html_e( 'Import Demo Tours', 'tourfic' ) ?></label></div>
                                <div class="tf-setup-form-item-input">
                                    <label for="tf-tour-demo-data-import" class="tf-switch-label">
                                        <input type="checkbox" id="tf-tour-demo-data-import" name="tf-tour-demo-data-import" value="" class="tf-switch"/>
                                        <span class="tf-switch-slider"></span>
                                    </label>
                                </div>
                            </div>
                            <span class="tf-setup-field-desc"><?php echo esc_html__( 'Enabling this feature will add some sample tours to your website', 'tourfic' ) ?></span>
                        </div>

                        <!--Tour Permalink-->
                        <div class="tf-setup-form-item tf-setup-form-item-inline tf-setup-permalink">
                            <div class="tf-setup-form-item-label"><label class=""><?php esc_html_e( 'Tour Permalink', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <input type="text" name="tf-tour-permalink" id="tf-tour-permalink" value="<?php echo esc_attr( $tf_tour_slug ); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tf-apartment-setup-wizard">
                    <h3 class="tf-setup-step-subtitle"><?php esc_html_e( 'Apartment settings', 'tourfic' ) ?></h3>
                    <p class="tf-setup-step-desc"><?php echo wp_kses_post( 'These settings can be overridden from <span>Tourfic Settings > Apartment Settings</span>' ) ?></p>

                    <div class="setup-form-group">
                        <!--Review Section-->
                        <div class="tf-setup-form-item tf-setup-form-item-inline">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-apartment-review-section"><?php esc_html_e( 'Review Section', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-apartment-review-section" class="tf-switch-label">
                                    <input type="checkbox" id="tf-apartment-review-section" name="tf-apartment-review-section" value="<?php echo empty( $tf_apartment_review ) ? esc_attr( '1' ) : ''; ?>"
                                           class="tf-switch" <?php echo empty( $tf_apartment_review ) ? esc_attr( 'checked' ) : ''; ?>/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Share Option-->
                        <div class="tf-setup-form-item tf-setup-form-item-inline">
                            <div class="tf-setup-form-item-label"><label class="" for="tf-apartment-share-option"><?php esc_html_e( 'Share Option', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <label for="tf-apartment-share-option" class="tf-switch-label">
                                    <input type="checkbox" id="tf-apartment-share-option" name="tf-apartment-share-option" value="<?php echo empty( $tf_apartment_share ) ? esc_attr( '1' ) : ''; ?>"
                                           class="tf-switch" <?php echo empty( $tf_apartment_share ) ? esc_attr( 'checked' ) : ''; ?>/>
                                    <span class="tf-switch-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!--Demo Data Import Section-->
                        <div class="tf-setup-form-item-wrap">
                            <div class="tf-setup-form-item tf-setup-form-item-inline">
                                <div class="tf-setup-form-item-label"><label class="" for="tf-apartment-demo-data-import"><?php esc_html_e( 'Import Demo Apartments', 'tourfic' ) ?></label></div>
                                <div class="tf-setup-form-item-input">
                                    <label for="tf-apartment-demo-data-import" class="tf-switch-label">
                                        <input type="checkbox" id="tf-apartment-demo-data-import" name="tf-apartment-demo-data-import" value="" class="tf-switch"/>
                                        <span class="tf-switch-slider"></span>
                                    </label>
                                </div>
                            </div>
                            <span class="tf-setup-field-desc"><?php echo esc_html__( 'Enabling this feature will add some sample apartments to your website', 'tourfic' ) ?></span>
                        </div>

                        <!--Apartment Permalink-->
                        <div class="tf-setup-form-item tf-setup-form-item-inline tf-setup-permalink">
                            <div class="tf-setup-form-item-label"><label class=""><?php esc_html_e( 'Apartment Permalink', 'tourfic' ) ?></label></div>
                            <div class="tf-setup-form-item-input">
                                <input type="text" name="tf-apartment-permalink" id="tf-apartment-permalink" value="<?php echo esc_attr( $tf_apartment_slug ); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tf-setup-action-btn-wrapper">
                    <div class="tf-setup-action-btn-next">
                        <button type="button" class="tf-setup-skip-btn tf-link-skip-btn"><?php esc_html_e( 'Skip', 'tourfic' ) ?></button>
                        <button type="button" class="tf-setup-next-btn tf-quick-setup-btn">
                            <span><?php esc_html_e( 'Next', 'tourfic' ) ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M5 12H19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 5L19 12L12 19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </section>
        </div>
		<?php
	}

	/**
	 * Setup step six
	 *
	 * Template Settings
	 */
	private function tf_setup_step_six() {
		$tf_hotel_single_template      = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';
		$tf_hotel_archive_template     = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel-archive'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['hotel-archive'] : 'design-1';
		$tf_tour_single_template       = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-tour'] : 'design-1';
		$tf_tour_archive_template      = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['tour-archive'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['tour-archive'] : 'design-1';
		$tf_apartment_single_template  = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-apartment'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-apartment'] : 'default';
		$tf_apartment_archive_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['apartment-archive'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['apartment-archive'] : 'default';
		?>
        <div class="tf-setup-step-container tf-setup-step-6 <?php echo self::$current_step == 'step_6' ? 'active' : ''; ?>" data-step="6">

            <div class="back-to-dashboard">
                <a href="#" class="tf-back-btn tf-setup-prev-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 19L5 12L12 5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 12H5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span><?php esc_html_e( 'Back', 'tourfic' ) ?></span>
                </a>
            </div>
            <section class="tf-setup-step-layout tf-template-step">
				<?php $this->tf_setup_wizard_steps_header( 5 ) ?>

                <h1 class="tf-setup-step-title"><?php esc_html_e( 'Choose Templates for Single & Archive Pages', 'tourfic' ) ?></h1>
                <p class="tf-setup-step-desc"><?php echo wp_kses_post( 'These templates are designed for showcasing your Single Hotel, Tour, and <br> Apartment pages, along with their respective Archive Pages' ) ?></p>

                <div class="tf-hotel-setup-wizard">

                    <h3 class="tf-setup-step-subtitle"><?php esc_html_e( 'Hotel Template', 'tourfic' ) ?></h3>
                    <p class="tf-setup-step-desc"><?php echo wp_kses_post( 'These settings can be overridden from <span>Tourfic Settings > Hotel Settings</span>' ) ?></p>

                    <div class="setup-form-group">
                        <!--Hotel Single Template-->
                        <div class="tf-field tf-field-imageselect  " style="width:100%;">
                            <label for="tf_settings[single-hotel]" class="tf-field-label"> <?php echo esc_html__( "Choose Single Template", "tourfic" ); ?> </label>
                            <div class="tf-fieldset">
                                <ul class="tf-image-radio-group tf-inline">
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_hotel"
                                                   value="design-1" <?php echo ! empty( $tf_hotel_single_template ) && $tf_hotel_single_template == "design-1" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/design1-hotel.jpg" alt="Design 1">
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_hotel"
                                                   value="design-2" <?php echo ! empty( $tf_hotel_single_template ) && $tf_hotel_single_template == "design-2" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/design2-hotel.jpg" alt="Design 2">
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_hotel"
                                                   value="default" <?php echo ! empty( $tf_hotel_single_template ) && $tf_hotel_single_template == "default" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/default-hotel.jpg" alt="Defult">
                                            </div>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!--Hotel Archive Page-->
                        <div class="tf-field tf-field-imageselect tf-archive-imageselect-box " style="width:100%;">
                            <label for="tf_settings[hotel-archive]" class="tf-field-label"> <?php echo esc_html__( "Choose Archive / Search Result Template ", "tourfic" ); ?> </label>
                            <div class="tf-fieldset">
                                <ul class="tf-image-radio-group tf-inline">
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_hotel_archive"
                                                   value="design-1" <?php echo ! empty( $tf_hotel_archive_template ) && $tf_hotel_archive_template == "design-1" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/hotel-archive-design1.jpg" alt="Design 1">
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_hotel_archive"
                                                   value="design-2" <?php echo ! empty( $tf_hotel_archive_template ) && $tf_hotel_archive_template == "design-2" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/hotel-archive-design2.jpg" alt="Design 1">
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_hotel_archive"
                                                   value="default" <?php echo ! empty( $tf_hotel_archive_template ) && $tf_hotel_archive_template == "default" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/hotel-archive-default.jpg" alt="Defult">
                                            </div>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tf-tour-setup-wizard">

                    <h3 class="tf-setup-step-subtitle"><?php esc_html_e( 'Tour settings', 'tourfic' ) ?></h3>
                    <p class="tf-setup-step-desc"><?php echo wp_kses_post( 'These settings can be overridden from <span>Tourfic Settings > Tour Settings</span>' ) ?></p>

                    <div class="setup-form-group">

                        <!--Tour Single Template-->
                        <div class="tf-field tf-field-imageselect  " style="width:100%;">
                            <label for="tf_settings[single-tour]" class="tf-field-label"> <?php echo esc_html__( "Choose Single Template", "tourfic" ); ?> </label>
                            <div class="tf-fieldset">
                                <ul class="tf-image-radio-group tf-inline">
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_tour"
                                                   value="design-1" <?php echo ! empty( $tf_tour_single_template ) && $tf_tour_single_template == "design-1" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/design1-tour.jpg" alt="Design 1">
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_tour"
                                                   value="design-2" <?php echo ! empty( $tf_tour_single_template ) && $tf_tour_single_template == "design-2" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/design2-tour.jpg" alt="Design 2">
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_tour"
                                                   value="default" <?php echo ! empty( $tf_tour_single_template ) && $tf_tour_single_template == "default" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/default-tour.jpg" alt="Defult">
                                            </div>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!--Tour Archive Page-->
                        <div class="tf-field tf-field-imageselect tf-archive-imageselect-box " style="width:100%;">
                            <label for="tf_settings[tour-archive]" class="tf-field-label"> <?php echo esc_html__( "Choose Archive / Search Result Template ", "tourfic" ); ?> </label>
                            <div class="tf-fieldset">
                                <ul class="tf-image-radio-group tf-inline">
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_tour_archive"
                                                   value="design-1" <?php echo ! empty( $tf_tour_archive_template ) && $tf_tour_archive_template == "design-1" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/tour-archive-design-1.jpg" alt="Design 1">
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_tour_archive"
                                                   value="design-2" <?php echo ! empty( $tf_tour_archive_template ) && $tf_tour_archive_template == "design-2" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/tour-archive-design-2.jpg" alt="Design 2">
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_tour_archive"
                                                   value="default" <?php echo ! empty( $tf_tour_archive_template ) && $tf_tour_archive_template == "default" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/tour-archive-default.jpg" alt="Defult">
                                            </div>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="tf-apartment-setup-wizard">

                    <h3 class="tf-setup-step-subtitle"><?php esc_html_e( 'Apartment settings', 'tourfic' ) ?></h3>
                    <p class="tf-setup-step-desc"><?php wp_kses_post( 'These settings can be overridden from <span>Tourfic Settings > Apartment Settings</span>' ) ?></p>

                    <div class="setup-form-group">

                        <!--Apartment Single Template-->
                        <div class="tf-field tf-field-imageselect  " style="width:100%;">
                            <label for="tf_settings[single-apartment]" class="tf-field-label"> <?php echo esc_html__( "Choose Single Template", "tourfic" ); ?> </label>
                            <div class="tf-fieldset">
                                <ul class="tf-image-radio-group tf-inline">
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_apartment"
                                                   value="default" <?php echo ! empty( $tf_apartment_single_template ) && $tf_apartment_single_template == "default" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/default-apartment.jpg" alt="Defult">
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_single_apartment"
                                                   value="design-1" <?php echo ! empty( $tf_apartment_single_template ) && $tf_apartment_single_template == "design-1" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/design1-apartment.jpg" alt="Defult">
                                            </div>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!--Apartment Archive Page-->
                        <div class="tf-field tf-field-imageselect tf-archive-imageselect-box " style="width:100%;">
                            <label for="tf_settings[apartment-archive]" class="tf-field-label"> <?php echo esc_html__( "Choose Archive / Search Result Template ", "tourfic" ); ?> </label>
                            <div class="tf-fieldset">
                                <ul class="tf-image-radio-group tf-inline">
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_apartment_archive"
                                                   value="default" <?php echo ! empty( $tf_apartment_archive_template ) && $tf_apartment_archive_template == "default" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/apartment-archive-default.jpg" alt="Defult">
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <label class="tf-image-checkbox">
                                            <input type="radio" name="tf_apartment_archive"
                                                   value="design-1" <?php echo ! empty( $tf_apartment_archive_template ) && $tf_apartment_archive_template == "design-1" ? esc_attr( 'checked' ) : ''; ?> >
                                            <div class="select-image-box">
                                                <img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ); ?>images/template/tour-archive-design-2.jpg" alt="Defult">
                                            </div>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tf-setup-action-btn-wrapper">
                    <div class="tf-setup-action-btn-next">
                        <button type="submit" class="tf-setup-skip-btn tf-link-skip-btn tf-setup-submit-btn tf-settings-finish-btn"><?php esc_html_e( 'Skip', 'tourfic' ) ?></button>
                        <button type="submit" class="tf-setup-submit-btn tf-quick-setup-btn tf-settings-finish-btn">
                            <span><?php esc_html_e( 'Finish', 'tourfic' ) ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M5 12H19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 5L19 12L12 19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </section>

        </div>
		<?php
	}

	/*
	 * Finish setup wizard
	 */
	private function tf_setup_finish_step() {
		?>
        <div class="tf-setup-step-container tf-setup-content-layout tf-setup-step-7 tf-finish-step <?php echo self::$current_step == 'finish' ? 'active' : ''; ?>" data-step="7">
            <div class="back-to-dashboard">
                <a href="#" class="tf-back-btn tf-setup-prev-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 19L5 12L12 5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 12H5" stroke="#003C79" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span><?php esc_html_e( 'Back', 'tourfic' ) ?></span>
                </a>
            </div>
            <div class="tf-setup-finish-content">
                <div class="welcome-img"><img src="<?php echo esc_url( TF_ASSETS_ADMIN_URL ) . 'images/hooray.gif' ?>" alt="<?php esc_attr_e( 'Thank you', 'tourfic' ) ?>"></div>
                <h1 class="tf-setup-welcome-title"><?php esc_html_e( 'Hooray! You’re all set.', 'tourfic' ) ?></h1>
                <div class="tf-setup-welcome-description"><?php echo wp_kses_post( 'Let\'s get started with Tourfic. Provide your customers with a seamless booking </br> experience with this plugin. Let\'s streamline your business operations now!', 'tourfic' ) ?></div>
                <div class="tf-setup-welcome-footer tf-setup-finish-footer">
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=tf_hotel' ) ) ?>"
                       class="tf-link-skip-btn tf-add-new-hotel tf-settings-default-button"><?php esc_html_e( 'Create Hotel', 'tourfic' ) ?></a>
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=tf_tours' ) ) ?>"
                       class="tf-link-skip-btn tf-add-new-tour tf-settings-default-button"><?php esc_html_e( 'Create Tour', 'tourfic' ) ?></a>
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=tf_apartment' ) ) ?>"
                       class="tf-link-skip-btn tf-add-new-apartment tf-settings-default-button"><?php esc_html_e( 'Create Apartment', 'tourfic' ) ?></a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=tf_settings' ) ) ?>" class="tf-quick-setup-btn tf-settings-default-button">
                        <span><?php esc_html_e( 'Tourfic Setting', 'tourfic' ) ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M5 12H19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 5L19 12L12 19" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * redirect to set up wizard when active plugin
	 */
	public function tf_activation_redirect() {
		if ( ! get_option( 'TF_Setup_Wizard' ) ) {
			update_option( 'TF_Setup_Wizard', 'active' );
			wp_redirect( admin_url( 'admin.php?page=tf-setup-wizard' ) );
			exit;
		}
	}

	private function tf_setup_wizard_steps_header( $active_step = 1 ) {
		$inactive_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 8 8" fill="none"><circle cx="4" cy="4" r="4" fill="#B6D6F7"/></svg>';
		$active_icon   = '<svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 8 8" fill="none"><circle cx="4" cy="4" r="4" fill="#003C79"/></svg>';
		$finish_icon   = '<svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 8 8" fill="none"><circle cx="4" cy="4" r="4" fill="#003C79"/></svg>';
		?>
        <div class="tf-setup-steps">
            <div class="tf-steps-item <?php echo $active_step == 1 ? 'active' : ''; ?>">
                <div class="tf-steps-item-container">
                    <div class="tf-steps-item-icon">
                            <span class="tf-steps-icon">
                                <?php echo $active_step == 1 ? wp_kses( $active_icon, Helper::tf_custom_wp_kses_allow_tags() ) : wp_kses( $finish_icon, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                            </span>
                    </div>
                </div>
            </div>

            <!-- if woocommerce not active or install -->
			<?php if ( ! file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) || ! is_plugin_active( 'woocommerce/woocommerce.php' ) ): ?>
                <div class="tf-steps-item <?php echo $active_step == 2 ? 'active' : ''; ?>">
                    <div class="tf-steps-item-container">
                        <div class="tf-steps-item-icon">
                                <span class="tf-steps-icon">
                                    <?php echo $active_step == 2 ? wp_kses( $active_icon, Helper::tf_custom_wp_kses_allow_tags() ) : ( $active_step > 2 ? wp_kses( $finish_icon, Helper::tf_custom_wp_kses_allow_tags() ) : wp_kses( $inactive_icon, Helper::tf_custom_wp_kses_allow_tags() ) ); ?>
                                </span>
                        </div>
                    </div>
                </div>
			<?php endif; ?>

            <div class="tf-steps-item <?php echo $active_step == 2 ? 'active' : ''; ?>">
                <div class="tf-steps-item-container">
                    <div class="tf-steps-item-icon">
                            <span class="tf-steps-icon">
                                <?php echo $active_step == 2 ? wp_kses( $active_icon, Helper::tf_custom_wp_kses_allow_tags() ) : ( $active_step > 2 ? wp_kses( $finish_icon, Helper::tf_custom_wp_kses_allow_tags() ) : wp_kses( $inactive_icon, Helper::tf_custom_wp_kses_allow_tags() ) ); ?>
                            </span>
                    </div>
                </div>
            </div>
            <div class="tf-steps-item <?php echo $active_step == 3 ? 'active' : ''; ?>">
                <div class="tf-steps-item-container">
                    <div class="tf-steps-item-icon">
                            <span class="tf-steps-icon">
                                <?php echo $active_step == 3 ? wp_kses( $active_icon, Helper::tf_custom_wp_kses_allow_tags() ) : ( $active_step > 3 ? wp_kses( $finish_icon, Helper::tf_custom_wp_kses_allow_tags() ) : wp_kses( $inactive_icon, Helper::tf_custom_wp_kses_allow_tags() ) ); ?>
                            </span>
                    </div>
                </div>
            </div>

            <div class="tf-steps-item <?php echo $active_step == 4 ? 'active' : ''; ?>">
                <div class="tf-steps-item-container">
                    <div class="tf-steps-item-icon">
                            <span class="tf-steps-icon">
                                <?php echo $active_step == 4 ? wp_kses( $active_icon, Helper::tf_custom_wp_kses_allow_tags() ) : ( $active_step > 4 ? wp_kses( $finish_icon, Helper::tf_custom_wp_kses_allow_tags() ) : wp_kses( $inactive_icon, Helper::tf_custom_wp_kses_allow_tags() ) ); ?>
                            </span>
                    </div>
                </div>
            </div>

            <div class="tf-steps-item <?php echo $active_step == 5 ? 'active' : ''; ?>">
                <div class="tf-steps-item-container">
                    <div class="tf-steps-item-icon">
                            <span class="tf-steps-icon">
                                <?php echo $active_step == 5 ? wp_kses( $active_icon, Helper::tf_custom_wp_kses_allow_tags() ) : ( $active_step > 5 ? wp_kses( $finish_icon, Helper::tf_custom_wp_kses_allow_tags() ) : wp_kses( $inactive_icon, Helper::tf_custom_wp_kses_allow_tags() ) ); ?>
                            </span>
                    </div>
                </div>
            </div>

        </div>
		<?php
	}

	function tf_setup_wizard_submit_ajax() {

		// Add nonce for security and authentication.
		if ( ! isset( $_POST['tf_setup_wizard_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tf_setup_wizard_nonce'] ) ), 'tf_setup_wizard_action' ) ) {
			return;
		}

		$tf_settings            = !empty( get_option( 'tf_settings' ) ) ? get_option( 'tf_settings' ) : array();
		$tf_services            = array( 'hotel', 'tour', 'apartment' );
		$services               = isset( $_POST['tf-services'] ) ? $_POST['tf-services'] : [];
		$search_page            = isset( $_POST['tf-search-result-page'] ) ? $_POST['tf-search-result-page'] : '';
		$search_result_per_page = isset( $_POST['tf-search-result-posts-per-page'] ) ? $_POST['tf-search-result-posts-per-page'] : '';
		$wishlist_page          = isset( $_POST['tf-wishlist-page'] ) ? $_POST['tf-wishlist-page'] : '';
		$auto_publish           = isset( $_POST['tf-auto-publish-review'] ) ? $_POST['tf-auto-publish-review'] : '';
		$hotel_review           = isset( $_POST['tf-hotel-review-section'] ) ? $_POST['tf-hotel-review-section'] : '';
		$hotel_share            = isset( $_POST['tf-hotel-share-option'] ) ? $_POST['tf-hotel-share-option'] : '';
		$hotel_permalink        = isset( $_POST['tf-hotel-permalink'] ) ? $_POST['tf-hotel-permalink'] : '';
		$hotel_demo_data        = isset( $_POST['tf-hotel-demo-data-import'] ) ? $_POST['tf-hotel-demo-data-import'] : '';
		$tour_review            = isset( $_POST['tf-tour-review-section'] ) ? $_POST['tf-tour-review-section'] : '';
		$tour_related           = isset( $_POST['tf-tour-related-section'] ) ? $_POST['tf-tour-related-section'] : '';
		$tour_permalink         = isset( $_POST['tf-tour-permalink'] ) ? $_POST['tf-tour-permalink'] : '';
		$tour_demo_data         = isset( $_POST['tf-tour-demo-data-import'] ) ? $_POST['tf-tour-demo-data-import'] : '';
		$apartment_review       = isset( $_POST['tf-apartment-review-section'] ) ? $_POST['tf-apartment-review-section'] : '';
		$apartment_share        = isset( $_POST['tf-apartment-share-option'] ) ? $_POST['tf-apartment-share-option'] : '';
		$apartment_permalink    = isset( $_POST['tf-apartment-permalink'] ) ? $_POST['tf-apartment-permalink'] : '';
		$apartment_demo_data    = isset( $_POST['tf-apartment-demo-data-import'] ) ? $_POST['tf-apartment-demo-data-import'] : '';

		// Template Step
		$tf_hotel_single      = isset( $_POST['tf_single_hotel'] ) ? $_POST['tf_single_hotel'] : 'design-1';
		$tf_hotel_archive     = isset( $_POST['tf_hotel_archive'] ) ? $_POST['tf_hotel_archive'] : 'design-1';
		$tf_tour_single       = isset( $_POST['tf_single_tour'] ) ? $_POST['tf_single_tour'] : 'design-1';
		$tf_tour_archive      = isset( $_POST['tf_tour_archive'] ) ? $_POST['tf_tour_archive'] : 'design-1';
		$tf_apartment_single  = isset( $_POST['tf_single_apartment'] ) ? $_POST['tf_single_apartment'] : 'default';
		$tf_apartment_archive = isset( $_POST['tf_apartment_archive'] ) ? $_POST['tf_apartment_archive'] : 'default';

		//skip steps
		$skip_steps = isset( $_POST['tf-skip-steps'] ) ? $_POST['tf-skip-steps'] : [];
		$skip_steps = explode( ',', $skip_steps );

		if ( ! in_array( 2, $skip_steps ) ) {
			$services = array_diff( $tf_services, $services );
			$services = array_map( 'sanitize_text_field', $services );

			$tf_settings['disable-services'] = [];
			if ( ! empty( $services ) ) {
				foreach ( $services as $service ) {
					$tf_settings['disable-services'][ $service ] = $service;
				}
			}
		}

		//General Settings
		if ( ! in_array( 4, $skip_steps ) ) {
			$tf_settings['search-result-page'] = ! empty( $search_page ) ? $search_page : '';
			$tf_settings['posts_per_page']     = ! empty( $search_result_per_page ) ? $search_result_per_page : '';
			$tf_settings['wl-page']            = ! empty( $wishlist_page ) ? $wishlist_page : '';
			$tf_settings['r-auto-publish']     = ! empty( $auto_publish ) ? $auto_publish : '';
		}

		// Hotel, Tour, Apartment Settings
		if ( ! in_array( 5, $skip_steps ) && ! in_array( 'hotel', $services ) ) {
			$tf_settings['h-review'] = ! empty( $hotel_review ) ? 0 : 1;
			$tf_settings['h-share']  = ! empty( $hotel_share ) ? 0 : 1;

			if ( ! empty( $hotel_permalink ) ) {
				// update_option( 'hotel_slug', $hotel_permalink );
				$tf_settings["hotel-permalink-setting"] = $hotel_permalink;
			}

			if ( ! empty( $hotel_demo_data ) && $hotel_demo_data == '1' ) {
				TF_Demo_Importer::instance()->tf_dummy_hotels_import();
				$migrator = new \Tourfic\Classes\Migrator();
				$migrator->regenerate_room_meta();
			}
		}

		if ( ! in_array( 5, $skip_steps ) && ! in_array( 'tour', $services ) ) {
			$tf_settings['t-review']  = ! empty( $tour_review ) ? 0 : 1;
			$tf_settings['t-related'] = ! empty( $tour_related ) ? 0 : 1;

			if ( ! empty( $tour_permalink ) ) {
				// update_option( 'tour_slug', $tour_permalink );
				$tf_settings["tour-permalink-setting"] = $tour_permalink;
			}

			if ( ! empty( $tour_demo_data ) && $tour_demo_data == '1' ) {
				TF_Demo_Importer::instance()->tf_dummy_tours_import();
			}
		}

		if ( ! in_array( 5, $skip_steps ) && ! in_array( 'apartment', $services ) ) {
			$tf_settings['disable-apartment-review'] = ! empty( $apartment_review ) ? 0 : 1;
			$tf_settings['disable-apartment-share']  = ! empty( $apartment_share ) ? 0 : 1;

			if ( ! empty( $apartment_permalink ) ) {
				// update_option( 'apartment_slug', $apartment_permalink );
				$tf_settings["apartment-permalink-setting"] = $apartment_permalink;
			}

			if ( ! empty( $apartment_demo_data ) && $apartment_demo_data == '1' ) {
				\Tourfic\Admin\TF_Demo_Importer::instance()->tf_dummy_apartments_import();
			}
		}

		// Settings Template
		if ( ! in_array( 6, $skip_steps ) && ! in_array( 'hotel', $services ) ) {
			$tf_settings['tf-template']['single-hotel']  = ! empty( $tf_hotel_single ) ? $tf_hotel_single : '';
			$tf_settings['tf-template']['hotel-archive'] = ! empty( $tf_hotel_archive ) ? $tf_hotel_archive : '';
		}

		if ( ! in_array( 6, $skip_steps ) && ! in_array( 'tour', $services ) ) {
			$tf_settings['tf-template']['single-tour']  = ! empty( $tf_tour_single ) ? $tf_tour_single : '';
			$tf_settings['tf-template']['tour-archive'] = ! empty( $tf_tour_archive ) ? $tf_tour_archive : '';
		}

		if ( ! in_array( 6, $skip_steps ) && ! in_array( 'apartment', $services ) ) {
			$tf_settings['tf-template']['single-apartment']  = ! empty( $tf_apartment_single ) ? $tf_apartment_single : '';
			$tf_settings['tf-template']['apartment-archive'] = ! empty( $tf_apartment_archive ) ? $tf_apartment_archive : '';
		}

		update_option( 'tf_settings', $tf_settings );
		$response = [
			'success'      => true,
			'redirect_url' => esc_url( admin_url( 'admin.php?page=tf_settings' ) )
		];

		echo wp_json_encode( $response );
		wp_die();
	}

	public function tf_travelfic_toolkit_activate_callabck() {
		check_ajax_referer( 'updates', '_ajax_nonce' );
		// Check user capabilities
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( 'Permission denied' );
		}
		//Activation
		$activate_plugin         = activate_plugin( 'travelfic-toolkit/travelfic-toolkit.php' );
		$toolkit_activate_plugin = activate_plugin( 'travelfic-toolkit/travelfic-toolkit.php' );

		if ( is_plugin_active( 'travelfic-toolkit/travelfic-toolkit.php' ) ) {
			wp_send_json_success( 'Toolkit activated successfully.' );
		} else {
			$result = activate_plugin( 'travelfic-toolkit/travelfic-toolkit.php' );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( 'Error: ' . $result->get_error_message() );
			} else {
				wp_send_json_success( 'Toolkit activated successfully!' );
			}
		}
		wp_die();
	}

	public function tf_setup_travelfic_theme_active_callabck() {

		check_ajax_referer( 'updates', '_ajax_nonce' );

		if ( ! current_user_can( 'switch_themes' ) ) {
			wp_send_json_error( 'User does not have permission to switch themes.' );
		}

		$theme_slug = isset( $_POST['slug'] ) ? sanitize_text_field( $_POST['slug'] ) : '';

		if ( ! wp_get_theme( $theme_slug )->exists() ) {
			wp_send_json_error( 'Theme does not exist.' );
		}
		switch_theme( $theme_slug );
		wp_send_json_success( 'Theme activated successfully.' );
	}

	public function tf_ajax_activate_woo_callback() {
		check_ajax_referer( 'updates', '_ajax_nonce' );
		// Check user capabilities
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( 'Permission denied' );
		}

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			wp_send_json_success( 'WooCommerce activated successfully.' );
		} else {
			$result = activate_plugin( 'woocommerce/woocommerce.php' );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( 'Error: ' . $result->get_error_message() );
			} else {
				wp_send_json_success( 'WooCommerce activated successfully!' );
			}
		}
		wp_die();
	}
}

