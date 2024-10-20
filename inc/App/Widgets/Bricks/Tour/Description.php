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
			'css' => [
				[
					'property' => 'text-transform',
					'selector' => '.text-editor p',
				],
			],
			'default' => get_the_content($post->ID),
		];

		$this->controls['tourDescriptionDesign'] = [
			'tab'     => 'content',
			'label'   => esc_html__( 'Design', 'tourfic' ),
			'type'    => 'select',
			'options' => [
				'design-1'  => esc_html__( 'Design 1', 'tourfic' ),
				'design-2' => esc_html__( 'Design 2', 'tourfic' ),
			],
			'inline'  => true,
			'default' => 'design-1',
		];
	}


	// Render element HTML
	public function render() {
		if ( isset( $this->settings['tourDescription'] ) ) { ?>
			<div <?php echo wp_kses_post( $this->render_attributes( '_root' ) ); ?>><?php echo $this->settings['tourDescription']; ?></div>
		<?php }
	}
    
}