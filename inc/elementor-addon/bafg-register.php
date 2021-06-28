<?php
namespace ElementorBafg;

//use ElementorBafg\PageSettings\Page_Settings;

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class BAFGWidget {

	/**
	 * Instance
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Include Widgets files
	 */
	private function include_widgets_files() {
		require_once( __DIR__ . '/widgets/bafg-slider.php' );
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 */
	public function register_widgets() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\BAFG_Slider() );
	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 */
	public function __construct() {
		// Register widgets
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

	}
}

// Instantiate Plugin Class
BAFGWidget::instance();
