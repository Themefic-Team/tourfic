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
 * Nearby Places
 */
class Nearby_Places extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;
	
	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-nearby-places';
	}

	public function get_title() {
		return esc_html__( 'Nearby Places', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-google-maps';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'surrounding',
            'nearby-places',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-nearby-places'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-nearby-places/before-style-controls', $this );
		$this->tf_nearby_places_title_style_controls();
		$this->tf_nearby_places_item_style_controls();
		do_action( 'tf/single-nearby-places/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_nearby_places_content',[
            'label' => esc_html__('Nearby Places', 'tourfic'),
        ]);

        do_action( 'tf/single-nearby-places/before-content/controls', $this );
		
		$this->add_control('nearby_places_style',[
            'label' => esc_html__('Nearby Places Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
            ],
        ]);

        $this->add_control('service',[
			'label' => esc_html__( 'Service', 'tourfic' ),
			'type' => \Elementor\Controls_Manager::HIDDEN,
			'default' => $this->get_current_post_type(),
		]);

	    do_action( 'tf/single-nearby-places/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_nearby_places_title_style_controls() {
		$this->start_controls_section( 'nearby_places_title_style', [
			'label' => esc_html__( 'Title Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

        $this->add_control( 'tf_title_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Title', 'tourfic' ),
		] );

		$this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Title Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => 'color: {{VALUE}};',
				'{{WRAPPER}} .surroundings_sec_title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Title Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-section-title, {{WRAPPER}} .surroundings_sec_title",
		]);

		$this->add_control( 'tf_subtitle_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Subtitle', 'tourfic' ),
			'conditions' => $this->tf_display_conditionally_single([
				'tf_apartment' => ['nearby_places_style' => ['style2']],
			]),
		] );

		$this->add_control( 'tf_subtitle_color', [
			'label'     => esc_html__( 'Subtitle Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .surroundings_subtitle' => 'color: {{VALUE}};',
			],
			'conditions' => $this->tf_display_conditionally_single([
				'tf_apartment' => ['nearby_places_style' => ['style2']],
			]),
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'label'    => esc_html__( 'Subtitle Typography', 'tourfic' ),
			'name'     => "tf_subtitle_typography",
			'selector' => "{{WRAPPER}} .surroundings_subtitle",
			'conditions' => $this->tf_display_conditionally_single([
				'tf_apartment' => ['nearby_places_style' => ['style2']],
			]),
		]);

		$this->end_controls_section();
	}

    protected function tf_nearby_places_item_style_controls() {
		$this->start_controls_section( 'nearby_places_item_style', [
			'label' => esc_html__( 'Item Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control( "item_heading", [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Item', 'tourfic' ),
		] );

        $this->add_responsive_control( "tf_items_gap", [
			'label'      => esc_html__( 'Items gap', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-hotel-single-places.tf-hotel-single-places-style1 ul" => 'grid-gap: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-whats-around.tf-hotel-single-places-style2 ul" => 'gap: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .tf-whats-around.tf-apartment-single-places-style1 ul" => 'gap: {{SIZE}}{{UNIT}};',
				"{{WRAPPER}} .about-location.tf-apartment-single-places-style2 .tf-apartment-surronding-criteria" => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
		] );

        $this->add_responsive_control( "item_padding", [
			'label'      => esc_html__( 'Padding', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-hotel-single-places ul li" => $this->tf_apply_dim( 'padding' ),
			],
			'conditions' => $this->tf_display_conditionally_single([
     			'tf_hotel' => ['nearby_places_style' => ['style1']],
     		]),
		] );

        $this->add_control( 'tf_item_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-hotel-single-places ul li' => 'background-color: {{VALUE}};',
			],
            'conditions' => $this->tf_display_conditionally_single([
     			'tf_hotel' => ['nearby_places_style' => ['style1']],
     		]),
		]);

		$this->add_control( "icon_heading", [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Icon', 'tourfic' ),
		] );

		$this->add_control( 'tf_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-hotel-single-places ul li .tf-place .tf-icon i' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-whats-around ul li i' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-apartment-surronding-criteria-label i' => 'color: {{VALUE}};',
			],
		]);

		$this->add_responsive_control( "icon_size", [
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
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-hotel-single-places ul li .tf-place .tf-icon i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-whats-around ul li i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-apartment-surronding-criteria-label i" => 'font-size: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( "label_heading", [
			'type'      => Controls_Manager::HEADING,
			'label'     => __( 'Label', 'tourfic' ),
		] );

		$this->add_control( 'tf_item_label_color', [
			'label'     => esc_html__( 'Label Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-hotel-single-places.tf-hotel-single-places-style1 ul li span' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-whats-around ul li span' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-apartment-surronding-criteria-label' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-apartment-surronding-places li span' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Label Typography', 'tourfic' ),
			'name'     => "tf_item_label_typography",
			'selector' => "{{WRAPPER}} .tf-hotel-single-places.tf-hotel-single-places-style1 ul li span,
						   {{WRAPPER}} .tf-whats-around ul li span,
						   {{WRAPPER}} .tf-apartment-surronding-criteria-label,
						   {{WRAPPER}} .tf-apartment-surronding-places li span",
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

        if($this->post_type == 'tf_hotel'){
            $this->tf_hotel_nearby_places($settings);
        } elseif($this->post_type == 'tf_apartment'){
            $this->tf_apartment_nearby_places($settings);
        } else {
			return;
		}
	}

    private function tf_hotel_nearby_places($settings) {
        $style = !empty($settings['nearby_places_style']) ? $settings['nearby_places_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_hotels_opt', true);
        $places_section_title = !empty($meta["section-title"]) ? $meta["section-title"] : "";
        $places = !empty($meta["nearby-places"]) ? Helper::tf_data_types($meta["nearby-places"]) : array();

		if ($style == 'style1' && is_array($places) && count($places) > 0) {
            ?>
			<div class="tf-hotel-single-places tf-hotel-single-places-style1">
                <?php if(!empty($places_section_title) ) : ?>
                    <h2 class="tf-title tf-section-title"><?php echo esc_html($places_section_title) ?></h2>
                <?php endif; ?>
                <ul>
                <?php foreach ( $places as $place ) {
                    $place_icon = '<i class="' . $place['place-icon'] . '"></i>';
                    ?>
                    <li>
                        <span class="tf-place"> 
                            <div class="tf-icon">
                                <?php echo wp_kses_post($place_icon); ?> 
                            </div>
                            <span class="tf-place-title">
                                <?php echo esc_html($place["place-title"]) ?>
                            </span>
                        </span>
                        <span> <?php echo esc_html($place["place-dist"]) ?></span>
                    </li>
                    <?php } ;?>
                </ul>
            </div>
            <?php
        } elseif ($style == 'style2' && is_array($places) && count($places) > 0) {
            ?>
            <div class="tf-whats-around tf-hotel-single-places-style2">
                <h3 class="tf-section-title"><?php echo !empty($meta['section-title']) ? esc_html($meta['section-title']) : esc_html__("What’s around?", 'tourfic'); ?></h3>
                <ul>
                    <?php foreach($places as $place){ ?>
                    <li>
                        <span class="tf-place">
                            <span class="tf-icon">
                                <?php if( !empty( $place['place-icon'] )){ ?>
                                    <i class="<?php echo esc_attr($place['place-icon']); ?>"></i>
                                <?php } ?> 
                            </span>
                            <span class="tf-place-title">
                                <?php echo !empty( $place['place-title'] ) ? esc_html($place['place-title']) : ''; ?>
                            </span>
                        </span>
                        <span><?php echo !empty( $place['place-dist'] ) ? esc_html($place['place-dist']) : ''; ?></span>
                    </li>
                    <?php } ?>
                </ul>
            </div>
			<?php
        }
	}

    private function tf_apartment_nearby_places($settings) {
        $style = !empty($settings['nearby_places_style']) ? $settings['nearby_places_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_apartment_opt', true);

		if ($style == 'style1' && ! empty( Helper::tf_data_types( $meta['surroundings_places'] ) )) {
            ?>
            <div class="tf-whats-around tf-apartment-single-places-style1">
                <?php if ( ! empty( $meta['surroundings_sec_title'] ) ): ?>
                    <h3 class="tf-section-title"><?php echo esc_html( $meta['surroundings_sec_title'] ); ?></h3>
                <?php endif; ?>
                <ul>
                    <?php foreach ( Helper::tf_data_types( $meta['surroundings_places'] ) as $surroundings_place ) : ?>
                    <?php if ( isset( $surroundings_place['places'] ) && ! empty( Helper::tf_data_types( $surroundings_place['places'] ) ) ): ?>
                    <?php foreach ( Helper::tf_data_types( $surroundings_place['places'] ) as $place ): ?>
                    <li>
                        <span>
                        <?php if(!empty($surroundings_place['place_criteria_icon'])){ ?>
                        <i class="<?php echo esc_attr( $surroundings_place['place_criteria_icon'] ); ?>"></i>
                        <?php } ?>
                        <?php echo esc_html( $surroundings_place['place_criteria_label'] ); ?>
                        </span>
                        <span><?php echo esc_html( $place['place_name'] ) ?> (<?php echo esc_html( $place['place_distance'] ) ?>)</span>
                    </li>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
        } elseif ($style == 'style2' && ! empty( Helper::tf_data_types( $meta['surroundings_places'] ) )) {
            ?>
			<div class="about-location tf-apartment-single-places-style2">
				<?php if ( ! empty( $meta['surroundings_sec_title'] ) ): ?>
					<h3 class="surroundings_sec_title"><?php echo esc_html( $meta['surroundings_sec_title'] ); ?></h3>
				<?php endif; ?>
				<?php if ( ! empty( $meta['surroundings_subtitle'] ) ): ?>
					<p class="surroundings_subtitle"><?php echo esc_html( $meta['surroundings_subtitle'] ); ?></p>
				<?php endif; ?>

				<div class="tf-apartment-surronding-wrapper">
					<?php foreach ( Helper::tf_data_types( $meta['surroundings_places'] ) as $surroundings_place ) : ?>
						<div class="tf-apartment-surronding-criteria">
							<div class="tf-apartment-surronding-criteria-label">
								<?php if ( ! empty( $surroundings_place['place_criteria_icon'] ) ) { ?>
									<i class="<?php echo esc_attr( $surroundings_place['place_criteria_icon'] ); ?>"></i>
								<?php } ?>
								<?php echo esc_html( $surroundings_place['place_criteria_label'] ); ?>
							</div>

							<?php if ( isset( $surroundings_place['places'] ) && ! empty( Helper::tf_data_types( $surroundings_place['places'] ) ) ): ?>
								<ul class="tf-apartment-surronding-places">
									<?php foreach ( Helper::tf_data_types( $surroundings_place['places'] ) as $place ): ?>
										<li>
											<span class="tf-place-name"><?php echo esc_html( $place['place_name'] ) ?></span>
											<span class="tf-place-distance"><?php echo esc_html( $place['place_distance'] ) ?></span>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
        }
	}
}
