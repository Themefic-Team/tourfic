<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Settings' ) ) {
	class TF_Settings {

		public $option_id = null;
		public $option_title = null;
		public $option_sections = array();

		public function __construct( $key, $params = array() ) {
			$this->option_id       = $key;
			$this->option_title    = $params['title'];
			$this->option_sections = $params['sections'];

			//options
			add_action( 'admin_menu', array( $this, 'tf_options' ) );
//			add_action( 'admin_init', array( $this, 'tf_register_settings' ) );

		}

		public static function option( $key, $params = array() ) {
			return new self( $key, $params );
		}

		/**
		 * Options
		 *
		 * @since 1.0.0
		 * @version 1.0.0
		 */
		public function tf_options() {
			add_menu_page(
				$this->option_title,
				$this->option_title,
				'manage_options',
				$this->option_id,
				array( $this, 'tf_options_page' ),
				'dashicons-admin-generic',
				5
			);
		}

		public function tf_options_page() {
			?>
            <form method="post" action="options.php">
                <div class="tf-admin-option">
                    <div class="tf-admin-tab">
                        <a class="tf-tablinks" href="#tf-general">General</a>
                        <a class="tf-tablinks" href="#tf-advanced">Advanced</a>
                    </div>

                    <div class="tf-tab-wrapper">
                        <div id="tf-general" class="tf-tab-content">
                            <h1>General</h1>
                        </div>
                        <div id="tf-advanced" class="tf-tab-content">
                            <h1>Advanced</h1>
                        </div>
                    </div>

                </div>
            </form>
			<?php
		}

	}
}
