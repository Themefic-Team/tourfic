<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Tourfic\Classes\Helper;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Feature
 */
class Feature extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-feature';
	}

	public function get_title() {
		return esc_html__( 'Feature', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-post-list';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'features',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-feature'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-feature/before-style-controls', $this );
		$this->tf_feature_title_style_controls();
		$this->tf_feature_style_controls();
		do_action( 'tf/single-feature/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_feature_content',[
            'label' => esc_html__('Feature', 'tourfic'),
        ]);

        do_action( 'tf/single-feature/before-content/controls', $this );
		
		$post_type = $this->get_current_post_type();
		$options = [
			'style1' => esc_html__('Style 1', 'tourfic'),
			'style2' => esc_html__('Style 2', 'tourfic'),
		];

		if($post_type == 'tf_apartment'){
			unset($options['style2']);
		}

		$this->add_control('feature_style',[
            'label' => esc_html__('Feature Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => $options,
        ]);

	    do_action( 'tf/single-feature/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_feature_title_style_controls() {
		$this->start_controls_section( 'feature_title_style', [
			'label' => esc_html__( 'Feature Title Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);
		
		$this->add_responsive_control('title_align',[
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
            'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => 'text-align: {{VALUE}};',
				'{{WRAPPER}} h2.section-heading' => 'text-align: {{VALUE}};',
			],
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
				'{{WRAPPER}} .tf-section-title' => $this->tf_apply_dim( 'margin' ),
				'{{WRAPPER}} h2.section-heading' => $this->tf_apply_dim( 'margin' ),
			],
		]);

		$this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Title Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-single-feature-section.tf-single-feature-style2 .tf-overview-popular-facilities span.tf-popular-facilities-title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Title Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-section-title, {{WRAPPER}} .tf-single-feature-section.tf-single-feature-style2 .tf-overview-popular-facilities span.tf-popular-facilities-title",
		]);

		$this->end_controls_section();
	}
    
    protected function tf_feature_style_controls() {
		$this->start_controls_section( 'features_style', [
			'label' => __( 'Features Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'tf_icon_features_color', [
			'label'     => __( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-hotel-single-features ul li i' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-overview-popular-facilities>ul li i' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control( 'tf_features_color', [
			'label'     => __( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-hotel-single-features ul li' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-overview-popular-facilities>ul li' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => __( 'Typography', 'tourfic' ),
			'name'     => "tf_features_typography",
			'selector' => "{{WRAPPER}} .tf-hotel-single-features ul li, {{WRAPPER}} .tf-overview-popular-facilities>ul li",
		]);

		$this->add_responsive_control( "features_padding", [
			'label'      => __( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-hotel-single-features ul li' => $this->tf_apply_dim( 'padding' ),
			],
            'condition' => [
				'feature_style' => ['style1'],
			],
		]);

		$this->add_responsive_control( "features_margin", [
			'label'      => __( 'Margin', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-hotel-single-features ul li' => $this->tf_apply_dim( 'margin' ), //design-1
				'{{WRAPPER}} .tf-overview-popular-facilities>ul li' => $this->tf_apply_dim( 'margin' ), //design-2
			],
		]);

		$this->add_control( 'features_bg_color', [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-hotel-single-features ul li' => 'background-color: {{VALUE}};',
			],
            'condition' => [
				'feature_style' => ['style1'],
			],
		]);

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "features_border",
			'selector' => "{{WRAPPER}} .tf-hotel-single-features ul li",
			'condition' => [
				'feature_style' => ['style1'],
			],
		]);

		$this->add_control( "features_border_radius", [
			'label'      => __( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .tf-hotel-single-features ul li' => $this->tf_apply_dim( 'border-radius' ),
			],
            'condition' => [
				'feature_style' => ['style1'],
			],
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name' => 'features_shadow',
			'selector' => "{{WRAPPER}} .tf-hotel-single-features ul li",
            'condition' => [
				'feature_style' => ['style1'],
			],
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

        if($this->post_type == 'tf_hotel'){
            $this->tf_hotel_features($settings);
        } if($this->post_type == 'tf_tours'){
            $this->tf_tour_features($settings);
        } if($this->post_type == 'tf_apartment'){
            $this->tf_apartment_features($settings);
        } else {
			return;
		}
	}

	private function tf_hotel_features($settings) {
		//feature style
        $style = !empty($settings['feature_style']) ? $settings['feature_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_hotels_opt', true);
		$feature_meta_key = 'tf_hotel_feature';
		$feature_title = !empty($meta['popular-section-title']) ? esc_html($meta['popular-section-title']) : '';
		$features = ! empty( get_the_terms( $this->post_id, 'hotel_feature' ) ) ? get_the_terms( $this->post_id, 'hotel_feature' ) : '';
        
        if ($style == 'style1' && $features) {
            ?>
            <div class="tf-single-template__one tf-single-feature-style1">
                <div class="tf-hotel-single-features tf-template-section">
                    <h2 class="tf-title tf-section-title"><?php echo esc_html($feature_title); ?></h2>
                    <ul>
                        <?php foreach ( $features as $feature ) {
                            $feature_meta = get_term_meta( $feature->term_taxonomy_id, $feature_meta_key, true );
                            $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                            if ( $f_icon_type == 'fa' && !empty($feature_meta['icon-fa']) ) {
                                $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
                            } elseif ( $f_icon_type == 'c' && !empty($feature_meta['icon-c']) ) {
                                $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
                            } ?>

                            <li>
                                <?php echo !empty($feature_meta) && !empty($feature_icon) ? wp_kses_post($feature_icon) : ''; ?>
                                <?php echo esc_html($feature->name); ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <?php
        } elseif ($style == 'style2' && $features) {
            ?>
            <div class="tf-single-feature-section tf-single-feature-style2">
                <div class="tf-overview-popular-facilities">
                    <span class="tf-popular-facilities-title"><?php echo esc_html($feature_title); ?></span>
                    <ul>
                        <?php foreach ( $features as $feature ) {
                            $feature_meta = get_term_meta( $feature->term_taxonomy_id, $feature_meta_key, true );
                            $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                            if ( $f_icon_type == 'fa' && ! empty( $feature_meta['icon-fa'] ) ) {
                                $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
                            }
                            if ( $f_icon_type == 'c' && ! empty( $feature_meta['icon-c'] ) ) {
                                $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
                            }
                            ?>
                            <li>
                                <?php echo ! empty( $feature_meta ) && ! empty( $feature_icon ) ? wp_kses_post( $feature_icon ) : ''; ?>
                                <?php echo esc_html($feature->name); ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
			<?php
        }
	}

	private function tf_tour_features($settings) {
		//feature style
        $style = !empty($settings['feature_style']) ? $settings['feature_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_tours_opt', true);
		$feature_meta_key = 'tour_features';
		$feature_title = !empty($meta['tour-features-section-title']) ? esc_html($meta['tour-features-section-title']) : '';
		$features = ! empty( get_the_terms( $this->post_id, 'tour_features' ) ) ? get_the_terms( $this->post_id, 'tour_features' ) : '';
        
        if ($style == 'style1' && $features) {
            ?>
            <div class="tf-single-template__one tf-single-feature-style1">
				<div class="tf-tour-features tf-template-section">
					<div class="tf-tour-features-container">
						<?php if (!empty($meta["tour-features-section-title"])) : ?>
							<h2 class="tf-title tf-section-title"><?php echo esc_html( $meta["tour-features-section-title"] ); ?></h2>
						<?php endif; ?>
						<ul class="tf-tour-feature-list">

							<?php foreach ( $features as $feature ) {
								$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tour_features', true );
								$f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';

								if ( $f_icon_type == 'fa' && !empty($feature_meta['icon-fa']) ) {
									$feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
								} else if ( $f_icon_type == 'c' && !empty($feature_meta['icon-c']) ) {
									$feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
								} ?>

							<?php if( !empty($feature->name) ) : ?>
									<li class="single-feature-box">
										<span class="tf-tour-features-icon"><?php echo !empty($feature_meta['icon-fa']) || !empty($feature_meta['icon-c'])  ? wp_kses_post($feature_icon) : ''; ?></span>
										<span><?php echo esc_html($feature->name); ?></span>
									</li>
							<?php endif; ?>
							<?php } ?>
						</ul>
					</div>
				</div>
            </div>
            <?php
        } elseif ($style == 'style2' && $features) {
            ?>
            <div class="tf-single-feature-section tf-single-feature-style2">
                <div class="tf-overview-popular-facilities">
                    <span class="tf-popular-facilities-title"><?php echo esc_html($feature_title); ?></span>
                    <ul>
                        <?php foreach ( $features as $feature ) {
                            $feature_meta = get_term_meta( $feature->term_taxonomy_id, $feature_meta_key, true );
                            $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                            if ( $f_icon_type == 'fa' && ! empty( $feature_meta['icon-fa'] ) ) {
                                $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
                            }
                            if ( $f_icon_type == 'c' && ! empty( $feature_meta['icon-c'] ) ) {
                                $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
                            }
                            ?>
                            <li>
                                <?php echo ! empty( $feature_meta ) && ! empty( $feature_icon ) ? wp_kses_post( $feature_icon ) : ''; ?>
                                <?php echo esc_html($feature->name); ?>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
			<?php
        }
	}

	private function tf_apartment_features($settings) {
		//feature style
        $style = !empty($settings['feature_style']) ? $settings['feature_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_apartment_opt', true);
		
        
        if ($style == 'style1' && Helper::tf_data_types( $meta['amenities'] ) ) {
				$fav_amenities = array();
				foreach ( Helper::tf_data_types( $meta['amenities'] ) as $amenity ) {
					if ( ! isset( $amenity['favorite'] ) || $amenity['favorite'] !== '1' ) {
						continue;
					}
					$fav_amenities[] = $amenity;
				}
				if ( ! empty( $fav_amenities ) ){
				?>
				<div class="tf-apartment-feature-style1 tf-place-offer-section">
					<h2 class="tf-section-title"><?php echo ! empty( $meta['amenities_title'] ) ? esc_html($meta['amenities_title']) : ''; ?></h2>
					<div class="place-offer-items">
						<?php
							foreach ( array_slice( $fav_amenities, 0, 10 ) as $amenity ) :
								$feature = get_term_by( 'id', $amenity['feature'], 'apartment_feature' );
								$feature_meta = get_term_meta( $amenity['feature'], 'tf_apartment_feature', true );
								$f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
								if ( $f_icon_type == 'icon' && !empty($feature_meta['apartment-feature-icon']) ) {
									$feature_icon = '<i class="' . $feature_meta['apartment-feature-icon'] . '"></i>';
								} elseif ( $f_icon_type == 'custom' && !empty($feature_meta['apartment-feature-icon-custom']) ) {
									$feature_icon = '<img src="' . esc_url( $feature_meta['apartment-feature-icon-custom'] ) . '" style="width: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px; height: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px;" />';
								}
								?>
								<div class="tf-apt-amenity">
									<?php echo ! empty( $feature_icon ) ? "<div class='tf-apt-amenity-icon'>" . wp_kses_post( $feature_icon ). "</div>" : ""; ?>
									<span><?php echo esc_html( $feature->name ); ?></span>
								</div>
							<?php endforeach; ?>
					</div>
				</div>
            <?php }
        }
	}

    /**
	 * Apply CSS property to the widget
     * @param $css_property
     * @return string
     */
	public function tf_apply_dim( $css_property, $important = false ) {
		return "{$css_property}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} " . ($important ? '!important' : '') . ";";
	}

	/**
	 * Get the current post type being previewed in Elementor editor
	 */
	protected function get_current_post_type() {
		// Check if we're in Elementor editor and have a preview post ID
		if (isset($_GET['tf_preview_post_id']) && !empty($_GET['tf_preview_post_id'])) {
			$preview_post_id = intval($_GET['tf_preview_post_id']);
			$preview_post = get_post($preview_post_id);
			
			if ($preview_post && in_array($preview_post->post_type, ['tf_hotel', 'tf_tours', 'tf_apartment', 'tf_carrental'])) {
				return $preview_post->post_type;
			}
		}
		
		// Fallback to regular post type detection
		return get_post_type();
	}
}
