<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class TOURFIC_Elementor {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {
		// Init
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Initialize the addon
	 *
	 * @access public
	 */
	public function init() {
        
        // Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}
        
		// Once we get here, We have passed all validation checks so we can safely include our plugin
		require_once( 'elementor-addon-register.php' );
	}

}

// Instantiate BAFG_Elementor.
new BAFG_Elementor();
