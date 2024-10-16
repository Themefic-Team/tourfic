<?php 
namespace Tourfic\App\Widgets\Bricks\Tour;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TF_Description extends \Bricks\Element {

    public $category     = 'tf-addons-elements';
	public $name         = 'tf-tour-desc';
	public $icon         = 'ti-loop tf-element';
	public $css_selector = '';
	public $scripts      = [];

	// Return localized element label
	public function get_label() {
		return esc_html__( 'TF Tour Description', 'tourfic' );
	}

	// Enqueue element styles and scripts
	public function enqueue_scripts() {
		wp_enqueue_style( 'tf-flip-box' );
	}

	// Set builder control groups
	public function set_control_groups() {
		
	}

	// Set builder controls
	public function set_controls() {

		
	}


	// Render element HTML
	public function render() {
		
	}
    
}