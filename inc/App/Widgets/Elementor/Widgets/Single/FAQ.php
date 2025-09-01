<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Tourfic\Classes\Helper;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * FAQ
 */
class FAQ extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-single-faq';
	}

	public function get_title() {
		return esc_html__( 'FAQ', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-help-o';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'frequently asked questions',
            'faq',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-faq'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-faq/before-style-controls', $this );
		$this->tf_faq_style_controls();
		do_action( 'tf/single-faq/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_faq_content',[
            'label' => esc_html__('FAQ', 'tourfic'),
        ]);

        do_action( 'tf/single-faq/before-content/controls', $this );
		
		$this->add_control('faq_style',[
            'label' => esc_html__('FAQ Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
            ],
        ]);

	    do_action( 'tf/single-faq/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_faq_style_controls() {
		$this->start_controls_section( 'faq_title_style', [
			'label' => esc_html__( 'FAQ Title Style', 'tourfic' ),
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
        $post_id   = get_the_ID();
        $post_type = get_post_type();

        if($post_type == 'tf_hotel'){
            $meta = get_post_meta($post_id, 'tf_hotels_opt', true);
			$faqs = ! empty( Helper::tf_data_types($meta['faq']) ) ? Helper::tf_data_types($meta['faq']) : '';

        } elseif($post_type == 'tf_tours'){
			$meta = get_post_meta($post_id, 'tf_tours_opt', true);
			$faqs = ! empty( Helper::tf_data_types($meta['faqs']) ) ? Helper::tf_data_types($meta['faqs']) : '';
			
			
        } elseif($post_type == 'tf_apartment'){
			$meta = get_post_meta($post_id, 'tf_apartment_opt', true);
			
			
        } elseif($post_type == 'tf_carrental'){
			$meta = get_post_meta($post_id, 'tf_carrental_opt', true);
			$faqs = ! empty( Helper::tf_data_types($meta['faq']) ) ? Helper::tf_data_types($meta['faq']) : '';
			
        } else {
			return;
		}

        //faq style
        $style = !empty($settings['faq_style']) ? $settings['faq_style'] : 'style1';
        $show_review = isset($settings['show_review']) ? $settings['show_review'] : 'yes';
        
        if ($style == 'style1') {
            ?>
            <div class="tf-single-faq-section tf-single-faq-style1">
                <h2 class="tf-title tf-section-title" ><?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : ''; ?></h2>
                <div class="tf-faq-inner">
                    <?php 
                    $faq_key = 1;    
                    foreach ( $faqs as $key => $faq ): ?>
                    <div class="tf-faq-single <?php echo $faq_key==1 ? esc_attr( 'active' ) : ''; ?>">
                        <div class="tf-faq-single-inner">
                            <div class="tf-faq-collaps tf-flex tf-flex-align-center tf-flex-space-bttn <?php echo $faq_key==1 ? esc_attr( 'active' ) : ''; ?>">
                                <h4><?php echo esc_html( $faq['title'] ); ?></h4> 
                                <div class="faq-icon"><i class="fa-solid fa-plus"></i><i class="fa-solid fa-minus"></i></div>
                            </div>
                            <div class="tf-faq-content" style="<?php echo $faq_key==1 ? esc_attr( 'display: block;' ) : ''; ?>">
                            <p><?php echo wp_kses_post( $faq['description'] ); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php $faq_key++; endforeach; ?>
                </div>
            </div>
            <?php
        } elseif ($style == 'style2') {
            ?>
            <div class="tf-single-faq-section tf-car-faq-section tf-single-faq-style2">
                <h2 class="tf-title tf-section-title" ><?php echo !empty($meta['faq-section-title']) ? esc_html($meta['faq-section-title']) : ''; ?></h2>
                <?php foreach ( $faqs as $key => $faq ): ?>
                    <div class="tf-faq-col">
                        <?php if(!empty($faq['title'])){ ?>
                            <div class="tf-faq-head">
                                <span class="tf-flex tf-flex-space-bttn tf-flex-align-center">
                                <?php echo esc_html($faq['title']); ?>
                                <i class="fa-solid fa-chevron-down"></i>
                                </span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($faq['description'])){ ?>
                            <div class="tf-question-desc">
                                <?php echo wp_kses_post($faq['description']); ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php endforeach; ?>
            </div>
			<?php
        }
	}
}
