<?php 
namespace Tourfic\App\Widgets\Bricks\Tour;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TF_Description extends \Bricks\Element {

    public $category     = 'tourfic-elements';
	public $name         = 'tf-tour-desc';
	public $icon         = 'ti-loop tf-element';
	public $css_selector = '';
	public $scripts      = [];

	// Return localized element label
	public function get_label() {
		return esc_html__( 'Tour Description', 'tourfic' );
	}

	// Enqueue element styles and scripts
	public function enqueue_scripts() {
		// wp_enqueue_style( 'tf-flip-box' );
	}

	// Set builder control groups
	public function set_control_groups() {
		
	}

	// Set builder controls
	public function set_controls() {
		global $post;
		$this->controls['tourDescription'] = [
			'tab' => 'content',
			'label' => esc_html__( 'Description', 'tourfic' ),
			'type' => 'editor',
			'inlineEditing' => [
			  'selector' => '.text-editor',
			  'toolbar' => true,
			],
			'default' => get_the_content($post->ID),
		];
	}


	// Render element HTML
	public function render() {
		if ( isset( $this->settings['tourDescription'] ) ) {
			echo '<div class="text-editor">' . $this->settings['tourDescription'] . '</div>';
		}
	}
    
}