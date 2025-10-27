<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Tourfic\Classes\Tour\Pricing;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Tour Info Cards
 */
class Tour_Info_Cards extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-tour-info-cards';
	}

	public function get_title() {
		return esc_html__( 'Tour Info Cards', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-info-circle-o';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'tour info cards',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-tour-info-cards'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-tour-info-cards/before-style-controls', $this );
		$this->tf_card_style_controls();
		$this->tf_icon_style_controls();
		$this->tf_title_style_controls();
		$this->tf_content_style_controls();
		do_action( 'tf/single-tour-info-cards/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_tour_info_cards_content',[
            'label' => esc_html__('Tour Info Cards', 'tourfic'),
        ]);

        do_action( 'tf/single-tour-info-cards/before-content/controls', $this );

        $this->add_control('info_cards_style',[
            'label' => esc_html__('Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
            ],
        ]);

		$this->add_responsive_control('grid_column', [
			'label' => esc_html__('Columns', 'tourfic'),
			'type' => Controls_Manager::SELECT,
			'default' => '4',
			'options' => [
				'1' => esc_html__('1', 'tourfic'),
				'2' => esc_html__('2', 'tourfic'),
				'3' => esc_html__('3', 'tourfic'),
				'4' => esc_html__('4', 'tourfic'),
			],
			'toggle' => true,
		]);

	    do_action( 'tf/single-tour-info-cards/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_card_style_controls() {
		$this->start_controls_section( 'card_style', [
			'label' => esc_html__( 'Card Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( "card_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-features-block-inner .tf-feature-block" => $this->tf_apply_dim( 'padding' ),
				"{{WRAPPER}} .tf-square-block-content .tf-single-square-block" => $this->tf_apply_dim( 'padding' ),
			],
		] );

		$this->add_control( 'card_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-features-block-inner .tf-feature-block" => 'background-color: {{VALUE}};',
				"{{WRAPPER}} .tf-square-block-content .tf-single-square-block" => 'background-color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "card_border",
			'selector' => "{{WRAPPER}} .tf-features-block-inner .tf-feature-block,
						   {{WRAPPER}} .tf-square-block-content .tf-single-square-block",
		] );

		$this->add_control( "card_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-features-block-inner .tf-feature-block" => $this->tf_apply_dim( 'border-radius' ),
				"{{WRAPPER}} .tf-square-block-content .tf-single-square-block" => $this->tf_apply_dim( 'border-radius' ),
			],
		] );

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'card_shadow',
			'selector' => '{{WRAPPER}} .tf-features-block-inner .tf-feature-block,
						   {{WRAPPER}} .tf-square-block-content .tf-single-square-block',
		]);
		
		$this->end_controls_section();
	}

	protected function tf_icon_style_controls() {
		$this->start_controls_section( 'icon_style', [
			'label' => esc_html__( 'Icon Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( "tf_icon_size", [
			'label'      => esc_html__( 'Icon Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-feature-block .tf-feature-block-icon i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-square-block-content .tf-single-square-block i" => 'font-size: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( "tf_icon_color", [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-feature-block .tf-feature-block-icon i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-square-block-content .tf-single-square-block i" => 'color: {{VALUE}}',
			],
		] );

		$this->add_responsive_control( "tc_icon_gap", [
			'label'     => esc_html__( 'Icon Gap', 'tourfic' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors' => [
				"{{WRAPPER}} .tf-features-block-inner .tf-feature-block" => 'gap: {{SIZE}}px;',
				"{{WRAPPER}} .tf-square-block-content .tf-single-square-block i" => 'margin-bottom: {{SIZE}}px;',
			],
		] );

		$this->end_controls_section();
	}

    protected function tf_title_style_controls() {
		$this->start_controls_section( 'title_style', [
			'label' => esc_html__( 'Title Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

        $this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-square-block-content .tf-single-square-block h4' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-feature-block .tf-feature-block-details h5' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-square-block-content .tf-single-square-block h4,
						   {{WRAPPER}} .tf-feature-block .tf-feature-block-details h5",
		]);

		$this->add_responsive_control( "title_margin", [
			'label'      => __( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-feature-block .tf-feature-block-details h5' => $this->tf_apply_dim( 'margin' ),
				'{{WRAPPER}} .tf-square-block-content .tf-single-square-block h4' => $this->tf_apply_dim( 'margin' ),
			],
		]);

		$this->end_controls_section();
	}

    protected function tf_content_style_controls() {
        $this->start_controls_section( 'content_style', [
			'label' => esc_html__( 'Content Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'tf_content_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-feature-block p' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-single-square-block p' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_content_typography",
			'selector' => "{{WRAPPER}} .tf-feature-block p,
						   {{WRAPPER}} .tf-single-square-block p",
		]);

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

        if($this->post_type !== 'tf_tours'){
            return;
        }
	    $meta = get_post_meta( $this->post_id, 'tf_tours_opt', true );
        $tour_duration = ! empty( $meta['duration'] ) ? $meta['duration'] : '';
	    $duration_time = ! empty( $meta['duration_time'] ) ? $meta['duration_time'] : 'Day';
        $info_tour_type = ! empty( $meta['tour_types'] ) ? $meta['tour_types'] : [];
        $group_size    = ! empty( $meta['group_size'] ) ? $meta['group_size'] : '';
        $language      = ! empty( $meta['language'] ) ? $meta['language'] : '';
        $night         = ! empty( $meta['night'] ) ? $meta['night'] : false;
	    $night_count   = ! empty( $meta['night_count'] ) ? $meta['night_count'] : '';
        $tour_duration_icon = ! empty( $meta['tf-tour-duration-icon'] ) ? $meta['tf-tour-duration-icon'] : 'ri-history-line';    
        $tour_type_icon = ! empty( $meta['tf-tour-type-icon'] ) ? $meta['tf-tour-type-icon'] : 'ri-menu-unfold-line';    
        $tour_group_icon = ! empty( $meta['tf-tour-group-icon'] ) ? $meta['tf-tour-group-icon'] : 'ri-team-line';    
        $tour_lang_icon = ! empty( $meta['tf-tour-lang-icon'] ) ? $meta['tf-tour-lang-icon'] : 'ri-global-line';

		$grid_column = isset( $settings['grid_column'] ) ? absint($settings['grid_column']) : 4;
        $style = !empty($settings['info_cards_style']) ? $settings['info_cards_style'] : 'style1';
       
        if ( $style == 'style1' && ($tour_duration || $info_tour_type || $group_size || $language) ) {  
            ?>
            <div class="tf-trip-feature-blocks tf-template-section tf-info-cards-style1">
                <div class="tf-features-block-inner tf-flex tf-flex-space-bttn tf-flex-gap-16 tf-grid-<?php echo esc_attr($grid_column); ?>">
                    <?php if ( $tour_duration ) { ?>
                        <div class="tf-feature-block tf-flex tf-flex-gap-8 tf-first">
                            <div class="tf-feature-block-icon">
                                <i class="<?php echo esc_attr($tour_duration_icon); ?>"></i>
                            </div>
                            <div class="tf-feature-block-details">
                                <h5><?php echo esc_html__( 'Duration', 'tourfic' ); ?></h5>
                                <p><?php echo esc_html( $tour_duration ); ?>
                                    <?php
                                    if ( $tour_duration > 1 ) {
                                        $dur_string         = 's';
                                        $_duration_time = $duration_time . $dur_string;
                                    } else {
                                        $_duration_time = $duration_time;
                                    }
                                    echo " " . esc_html( $_duration_time );

                                    if ( $night ) {
                                        echo '<span>';
                                            echo esc_html(', '. $night_count );
                                            if ( ! empty( $night_count ) ) {
                                                if ( $night_count > 1 ) {
                                                    echo esc_html__( ' Nights', 'tourfic' );
                                                } else {
                                                    echo esc_html__( ' Night', 'tourfic' );
                                                }
                                            }
                                        echo '</span>';
                                    }
                                    ?>
                                </p>

                            </div>
                        </div>
                    <?php } ?>
                    
                    <?php 
                if ( is_array( $info_tour_type ) && array_filter( $info_tour_type ) ) {
                        if ( gettype( $info_tour_type ) === 'string' ) {
                            $info_tour_type = ucfirst( esc_html( $info_tour_type ) );
                        } else if ( gettype( $info_tour_type ) === 'array' ) {
                            $tour_types =[];
                            $types = ! empty( get_the_terms( $this->post_id, 'tour_type' ) ) ? get_the_terms( $this->post_id, 'tour_type' ) : '';
                            if ( ! empty( $types ) ) {
                                foreach ( $types as $type ) {
                                    $tour_types[] = $type->name;
                                }
                            }
                            $info_tour_type = implode( ', ', $tour_types );
                        }
                        ?>
                        <div class="tf-feature-block tf-flex tf-flex-gap-8  tf-second">
                            <div class="tf-feature-block-icon">
                            <i class="<?php echo esc_attr($tour_type_icon); ?>"></i>
                            </div>
                            <div class="tf-feature-block-details">
                                <h5><?php echo esc_html__( 'Tour Type', 'tourfic' ); ?></h5>
                                <p><?php echo esc_html( $info_tour_type ) ?></p>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ( $group_size ) { ?>
                        <div class="tf-feature-block tf-flex tf-flex-gap-8  tf-third">
                            <div class="tf-feature-block-icon">
                            <i class="<?php echo esc_attr($tour_group_icon); ?>"></i>
                            </div>
                            <div class="tf-feature-block-details">
                                <h5><?php echo esc_html__( 'Group Size', 'tourfic' ); ?></h5>
                                <p><?php echo esc_html( $group_size ) ?></p>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ( $language ) { ?>
                        <div class="tf-feature-block tf-flex tf-flex-gap-8  tf-tourth">
                            <div class="tf-feature-block-icon">
                            <i class="<?php echo esc_attr($tour_lang_icon); ?>"></i>
                            </div>
                            <div class="tf-feature-block-details">
                                <h5><?php echo esc_html__( 'Language', 'tourfic' ); ?></h5>
                                <p><?php echo esc_html( $language ) ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php 
        } elseif($style == 'style2' && ($tour_duration || $info_tour_type || $group_size || $language)) {
            ?>
            <div class="tf-square-block tf-info-cards-style2">
                <div class="tf-square-block-content tf-grid-<?php echo esc_attr($grid_column); ?>">
                    <?php if ( $tour_duration ) { ?>
                        <div class="tf-single-square-block first">
                            <i class="<?php echo esc_attr($tour_duration_icon); ?>"></i>
                            <h4><?php echo esc_html__( 'Duration', 'tourfic' ); ?></h4>
                            <p><?php echo esc_html( $tour_duration ); ?>
                            <span> 
                                <?php
                                if( $tour_duration > 1  ){
                                    $dur_string = 's';
                                    $duration_time_html = $duration_time . $dur_string;
                                }else{
                                    $duration_time_html = $duration_time;
                                }
                                echo " " . esc_html( $duration_time_html )?>
                            </span></p>
                            <?php if( $night ){ ?>
                            <p>
                                <?php echo esc_html( $night_count ); ?>
                                <span>
                                    <?php
                                    if(!empty($night_count)){
                                        if( $night_count > 1  ){
                                            echo esc_html__( 'Nights', 'tourfic' );
                                        }else{
                                            echo esc_html__( 'Night', 'tourfic'  );
                                        }	
                                    }										
                                    ?>
                                </span>
                            </p>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if ( $info_tour_type ) {

                        if ( gettype( $info_tour_type ) === 'string' ) {
                            $info_tour_type = ucfirst( esc_html( $info_tour_type ) );
                        } else if ( gettype( $info_tour_type ) === 'array' ) {
                            $tour_types =[];
                            $types = ! empty( get_the_terms( $this->post_id, 'tour_type' ) ) ? get_the_terms( $this->post_id, 'tour_type' ) : '';
                            if ( ! empty( $types ) ) {
                                foreach ( $types as $type ) {
                                    $tour_types[] = $type->name;
                                }
                            }
                            $info_tour_type = implode( ', ', $tour_types );
                        }
                        ?>
                        <div class="tf-single-square-block second">
                            <i class="<?php echo esc_attr($tour_type_icon); ?>"></i>
                            <h4><?php echo esc_html__( 'Tour Type', 'tourfic' ); ?></h4>
                            <p><?php echo esc_html( $info_tour_type ); ?></p>
                        </div>
                    <?php } ?>
                    <?php if ( $group_size ) { ?>
                        <div class="tf-single-square-block third">
                            <i class="<?php echo esc_attr($tour_group_icon); ?>"></i>
                            <h4><?php echo esc_html__( 'Group Size', 'tourfic' ); ?></h4>
                            <p><?php echo esc_html( $group_size ) ?></p>
                        </div>
                    <?php } ?>
                    <?php if ( $language ) { ?>
                        <div class="tf-single-square-block fourth">
                            <i class="<?php echo esc_attr($tour_lang_icon); ?>"></i>
                            <h4><?php echo esc_html__( 'Language', 'tourfic' ); ?></h4>
                            <p><?php echo esc_html( $language ) ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php
        }
    }
}
