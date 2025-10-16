<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Title
 */
class Title extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	public function get_name() {
		return 'tf-single-title';
	}

	public function get_title() {
		return esc_html__( 'Title', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-post-title';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'title',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-title'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-title/before-style-controls', $this );
		$this->tf_title_style_controls();
		do_action( 'tf/single-title/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_title_content',[
            'label' => esc_html__('Title', 'tourfic'),
        ]);

        do_action( 'tf/single-title/before-content/controls', $this );
		
		$this->add_control('tf-title-tag',[
			'label' => esc_html__('Title Tag', 'tourfic'),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'h1'  => [
					'title' => esc_html__('H1', 'tourfic'),
					'icon' => 'eicon-editor-h1'
				],
				'h2'  => [
					'title' => esc_html__('H2', 'tourfic'),
					'icon' => 'eicon-editor-h2'
				],
				'h3'  => [
					'title' => esc_html__('H3', 'tourfic'),
					'icon' => 'eicon-editor-h3'
				],
				'h4'  => [
					'title' => esc_html__('H4', 'tourfic'),
					'icon' => 'eicon-editor-h4'
				],
				'h5'  => [
					'title' => esc_html__('H5', 'tourfic'),
					'icon' => 'eicon-editor-h5'
				],
				'h6'  => [
					'title' => esc_html__('H6', 'tourfic'),
					'icon' => 'eicon-editor-h6'
				]
			],
			'default' => 'h1',
			'toggle' => false,
		]);

		$this->add_responsive_control('title-align',[
			'label' => esc_html__('Alignment', 'tourfic'),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'left' => [
					'title' => esc_html__('Left', 'tourfic'),
					'icon' => 'eicon-text-align-left',
				],
				'center' => [
					'title' => esc_html__('Center', 'tourfic'),
					'icon' => 'eicon-text-align-center',
				],
				'right' => [
					'title' => esc_html__('Right', 'tourfic'),
					'icon' => 'eicon-text-align-right',
				],
			],
			'toggle' => true,
			'selectors' => [
				'{{WRAPPER}} .tf-head-title' => 'text-align: {{VALUE}};',
			]
		]);

	    do_action( 'tf/single-title/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_title_style_controls() {
		$this->start_controls_section( 'title_style', [
			'label' => esc_html__( 'Title Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Title Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-post-title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Title Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-post-title",
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$tf_cars_slug = get_option('car_slug');
		$post_type = get_post_type();
		if($post_type == 'tf_carrental'):
        ?>
			<div class="tf-car-title">
				<h1><?php the_title(); ?></h1>
				<div class="breadcrumb">
					<ul>
						<li><a href="<?php echo esc_url(site_url()); ?>"><?php esc_html_e( "Home", "tourfic" ) ?></a></li>
						<li>/</li>
						<li><a href="<?php echo esc_url(site_url()); ?>/<?php echo esc_attr($tf_cars_slug); ?>"><?php esc_html_e( "Cars", "tourfic" ) ?></a></li>
						<li>/</li>
						<li><?php the_title(); ?></li>
					</ul>
				</div>
			</div>
		<?php else: ?>
			<div class="tf-head-title">
			<?php 
			/* translators: %1$s title Tag, %2$s post title */
			printf('<%1$s class="tf-post-title">%2$s</%1$s>', esc_attr( $settings['tf-title-tag'], 'h1' ), esc_html(get_the_title())); 
			?>
			</div>
        <?php
		endif;
	}
}
