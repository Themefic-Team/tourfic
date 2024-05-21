<?php

namespace Tourfic\App\Widgets;

// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\App\Widgets\Elementor\TF_Widget_Register;

class TF_Widget_Base {

    use \Tourfic\Traits\Singleton;

    public function __construct() {
        add_action( 'init', array( $this, 'tf_add_elelmentor_addon' ) );
    }

    function tf_add_elelmentor_addon() {

        // Check if Elementor installed and activated
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }

        // Include Widget files
        TF_Widget_Register::instance();
    }
}