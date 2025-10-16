<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Tourfic\Classes\Helper;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Address
 */
class Address extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	public function get_name() {
		return 'tf-single-address';
	}

	public function get_title() {
		return esc_html__( 'Address', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-map-pin';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'location',
            'address',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-address'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-address/before-style-controls', $this );
		$this->tf_address_style_controls();
		do_action( 'tf/single-address/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_address_content',[
            'label' => esc_html__('Address', 'tourfic'),
        ]);

        do_action( 'tf/single-address/before-content/controls', $this );
		
		$this->add_control('address_icon',[
			'label' => esc_html__('Address Icon', 'tourfic'),
			'default' => [
				'value' => 'fas fa-map-marker-alt',
				'library' => 'fa-solid',
			],
			'label_block' => true,
			'type' => Controls_Manager::ICONS,
			'fa4compatibility' => 'address_icon_comp',
		]);

        $this->add_control('show_location',[
			'label' => esc_html__('Show Location Link?', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
		]);

		$this->add_responsive_control('address-align',[
			'label' => esc_html__('Alignment', 'tourfic'),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'flex-start' => [
					'title' => esc_html__('Left', 'tourfic'),
					'icon' => 'eicon-text-align-left',
				],
				'center' => [
					'title' => esc_html__('Center', 'tourfic'),
					'icon' => 'eicon-text-align-center',
				],
				'flex-end' => [
					'title' => esc_html__('Right', 'tourfic'),
					'icon' => 'eicon-text-align-right',
				],
			],
			'toggle' => true,
			'selectors' => [
				'{{WRAPPER}} .tf-title-meta' => 'justify-content: {{VALUE}};',
			]
		]);

	    do_action( 'tf/single-address/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_address_style_controls() {
		$this->start_controls_section( 'address_style', [
			'label' => esc_html__( 'Address Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control( 'tf_address_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-address' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_address_typography",
			'selector' => "{{WRAPPER}} .tf-address",
		]);

		$this->add_responsive_control( "tf_address_icon_size", [
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
				"{{WRAPPER}} .tf-address i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-address svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( "tf_address_icon_color", [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-address i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-address svg path" => 'fill: {{VALUE}}',
			],
		] );

		$this->add_control( 'tf_link_type_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => esc_html__( 'Link Style', 'tourfic' ),
		] );

		$this->add_control( 'tf_link_color', [
			'label'     => esc_html__( 'Link Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .more-hotel' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Link Typography', 'tourfic' ),
			'name'     => "tf_link_typography",
			'selector' => "{{WRAPPER}} .more-hotel",
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
        $show_location   = !empty( $settings['show_location'] ) ? $settings['show_location'] : '';
        $post_id   = get_the_ID();
		$post_type = get_post_type();
        $locations = $address = '';
        if($post_type == 'tf_hotel'){
            $post_meta = get_post_meta($post_id, 'tf_hotels_opt', true);
            $locations = ! empty( get_the_terms( $post_id, 'hotel_location' ) ) ? get_the_terms( $post_id, 'hotel_location' ) : '';
            if ( $locations ) {
                $first_location_id   = $locations[0]->term_id;
                $first_location_term = get_term( $first_location_id );
                $first_location_name = $locations[0]->name;
                $first_location_url  = get_term_link( $first_location_term );
            }

            if( !empty($post_meta['map']) && Helper::tf_data_types($post_meta['map'])){
                $address = !empty( Helper::tf_data_types($post_meta['map'])['address'] ) ? Helper::tf_data_types($post_meta['map'])['address'] : '';
            }
        } elseif($post_type == 'tf_tours'){
			$post_meta = get_post_meta($post_id, 'tf_tours_opt', true);
			if( !empty($post_meta['location']) && Helper::tf_data_types($post_meta['location'])){
				$address = !empty( Helper::tf_data_types($post_meta['location'])['address'] ) ? Helper::tf_data_types($post_meta['location'])['address'] : '';
			}
        } elseif($post_type == 'tf_apartment'){
			$post_meta = get_post_meta($post_id, 'tf_apartment_opt', true);
			if( !empty($post_meta['map']) && Helper::tf_data_types($post_meta['map'])){
                $address = !empty( Helper::tf_data_types($post_meta['map'])['address'] ) ? Helper::tf_data_types($post_meta['map'])['address'] : '';
            }
        } else {
			return;
		}

        //Address icon
		$address_icon_html = '<i class="fa-solid fa-location-dot"></i>';
		
        $address_icon_migrated = isset($settings['__fa4_migrated']['address_icon']);
        $address_icon_is_new = empty($settings['address_icon_comp']);

        if ( $address_icon_is_new || $address_icon_migrated ) {
            ob_start();
            Icons_Manager::render_icon( $settings['address_icon'], [ 'aria-hidden' => 'true' ] );
            $address_icon_html = ob_get_clean();
        } else{
            $address_icon_html = '<i class="' . esc_attr( $settings['address_icon_comp'] ) . '"></i>';
        }
        ?>
		<div class="tf-title-meta">
			<?php if ( !empty( $address ) ) {
				echo '<div class="tf-address">'. wp_kses( $address_icon_html, Helper::tf_custom_wp_kses_allow_tags() ) . wp_kses_post($address) . '</div>';
			} ?>
			<?php if($post_type == 'tf_hotel' && $show_location == 'yes'): ?>
				<a href="<?php echo esc_url($first_location_url); ?>" class="more-hotel tf-d-ib">
					<?php
					/* translators: %s location name */
					printf( esc_html__( ' - Show more hotels in %s', 'tourfic' ), esc_html($first_location_name) );
					?>
				</a>
			<?php endif; ?>
        </div>
        <?php
	}
}
