<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Widget_Base;

use Tourfic\Classes\Helper;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Thumbnail
 */
class Thumbnail extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	public function get_name() {
		return 'tf-single-thumbnail';
	}

	public function get_title() {
		return esc_html__( 'Thumbnail', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-image';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'image',
            'thumbnail',
			'tourfic',
			'media',
			'tf'
        ];
    }

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-thumbnail/before-style-controls', $this );
		$this->tf_thumbnail_style_controls();
		do_action( 'tf/single-thumbnail/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_thumbnail_content',[
            'label' => esc_html__('Thumbnail', 'tourfic'),
        ]);

        do_action( 'tf/single-thumbnail/before-content/controls', $this );

		$this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'image_size',
                'default' => 'full',
                'separator' => 'none',
            ]
        );

		 $this->add_control('object_fit',[
            'label' => esc_html__('Object Fit', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'cover',
            'options' => [
                'contain' => esc_html__('Contain', 'tourfic'),
                'cover' => esc_html__('Cover', 'tourfic'),
                'fill' => esc_html__('Fill', 'tourfic'),
                'none' => esc_html__('None', 'tourfic'),
            ],
        ]);

	    do_action( 'tf/single-thumbnail/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_thumbnail_style_controls() {
		$this->start_controls_section( 'thumbnail_style', [
			'label' => esc_html__( 'Thumbnail Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );
		
		$this->add_responsive_control('thumbnail_height',[
			'label'      => esc_html__('Thumbnail Height', 'tourfic'),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 1000,
					'step' => 1,
				],
				'%'  => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'default'   => [
				'unit' => 'px',
				'size' => 600,
			],
			'size_units' => ['px', '%'],
			'selectors'  => [
				'{{WRAPPER}} .tf-single-thumbnail img' => 'height: {{SIZE}}{{UNIT}} !important; min-height: {{SIZE}}{{UNIT}} !important;', //design-1
			],
		]);
		
		$this->add_responsive_control('thumbnail_width',[
			'label'      => __('Thumbnail Width', 'tourfic'),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 1920,
					'step' => 1,
				],
				'%'  => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'default'   => [
				'unit' => '%',
				'size' => 100,
			],
			'size_units' => ['px', '%'],
			'selectors'  => [
				'{{WRAPPER}} .tf-single-thumbnail img' => 'width: {{SIZE}}{{UNIT}} !important;', //design-1
			],
		]);

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $post_id   = get_the_ID();
        $post_type = get_post_type();

		$thumbnail_url = Group_Control_Image_Size::get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'image_size', $settings );
        if(empty($thumbnail_url)){
			$thumbnail_url = esc_url(TF_ASSETS_APP_URL.'images/feature-default.jpg');
		}
		?>
		<div class="tf-single-thumbnail tf-flex">
			<img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php the_title_attribute(); ?>" style="object-fit: <?php echo esc_attr($settings['object_fit']); ?>;" />
		</div>
		<?php
    }
}
