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
//			add_action( 'admin_menu', [ $this, 'update_menu_items' ], 99 );
//			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
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
		 * Setup wizard page
		 */
		public function tf_wizard_page() {
			$step = isset( $_GET['step'] ) ? sanitize_text_field( $_GET['step'] ) : 'welcome';
			?>
			<div class="tf-setup-wizard">
				<div class="tf-setup-wizard-header">
					<h1><?php esc_html_e( 'TourFic Setup Wizard', 'tourfic' ); ?></h1>
				</div>
				<div class="tf-setup-wizard-body">
					<?php
					if ( $step == 'welcome' ) {
						$this->welcome_step();
					} elseif ( $step == 'import' ) {
						$this->import_step();
					} elseif ( $step == 'finish' ) {
						$this->finish_step();
					}
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Welcome step
		 */
		public function welcome_step() {
			?>
			<div class="tf-setup-wizard-step">
				<h2><?php esc_html_e( 'Welcome to TourFic', 'tourfic' ); ?></h2>
				<p><?php esc_html_e( 'Thank you for choosing TourFic. This quick setup wizard will help you configure the basic settings. It’s completely optional and shouldn’t take longer than five minutes.', 'tourfic' ); ?></p>
				<p><?php esc_html_e( 'No time right now? If you don’t want to go through the wizard, you can skip and return to the WordPress dashboard. Come back anytime if you change your mind!', 'tourfic' ); ?></p>
				<p class="tf-setup-wizard-actions step">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=tf-setup-wizard&step=import' ) ); ?>" class="button button-primary button-large"><?php esc_html_e( 'Let\'s Go!', 'tourfic' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=tf-setup-wizard&step=finish' ) ); ?>" class="button button-large"><?php esc_html_e( 'Not right now', 'tourfic' ); ?></a>
				</p>
			</div>
			<?php
		}

		/**
		 * Setup step
		 */
		public function import_step() {
			?>
			<div class="tf-setup-wizard-step">
				<h2><?php esc_html_e( 'Import Demo Content', 'tourfic' ); ?></h2>
				<p><?php esc_html_e( 'Importing demo content (posts, pages, images, theme settings, ...) is the easiest way to setup your theme. It will allow you to quickly edit everything instead of creating content from scratch. When you import the data following things will happen:', 'tourfic' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'No existing posts, pages, categories, images, custom post types or any other data will be deleted or modified.', 'tourfic' ); ?></li>
					<li><?php esc_html_e( 'Posts, pages, some images, some widgets and menus will get imported.', 'tourfic' ); ?></li>
					<li><?php esc_html_e( 'Please click import only once and wait, it can take a couple of minutes.', 'tourfic' ); ?></li>
				</ul>
				<p class="tf-setup-wizard-actions step">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=tf-setup-wizard&step=finish' ) ); ?>" class="button button-primary button-large"><?php esc_html_e( 'Import Demo Content', 'tourfic' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=tf-setup-wizard&step=finish' ) ); ?>" class="button button-large"><?php esc_html_e( 'Skip import', 'tourfic' ); ?></a>
				</p>
			</div>
			<?php
		}

		/**
		 * Finish step
		 */
		public function finish_step() {
			?>
			<div class="tf-setup-wizard-step">
				<h2><?php esc_html_e( 'All done!', 'tourfic' ); ?></h2>
				<p><?php esc_html_e( 'Your theme is ready to use. We hope you enjoy it!', 'tourfic' ); ?></p>
				<p class="tf-setup-wizard-actions step">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=tf-setup-wizard&step=finish' ) ); ?>" class="button button-primary button-large"><?php esc_html_e( 'View your website', 'tourfic' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=tf-setup-wizard&step=finish' ) ); ?>" class="button button-large"><?php esc_html_e( 'Exit the wizard', 'tourfic' ); ?></a>
				</p>
			</div>
			<?php
		}

		/**
		 * redirect to setup wizard when active plugin
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