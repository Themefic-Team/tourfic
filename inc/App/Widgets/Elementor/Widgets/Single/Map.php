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
 * Map
 */
class Map extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-single-map';
	}

	public function get_title() {
		return esc_html__( 'Map', 'tourfic' );
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
            'map',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-map'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-map/before-style-controls', $this );
		// $this->tf_map_style_controls();
		do_action( 'tf/single-map/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_map_content',[
            'label' => esc_html__('Map', 'tourfic'),
        ]);

        do_action( 'tf/single-map/before-content/controls', $this );

        //map height
        $this->add_responsive_control( 'map_height', [
            'label'      => esc_html__( 'Map Height', 'tourfic' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'vh' ],
            'range'      => [
                'px' => [
                    'min'  => 100,
                    'max'  => 1000,
                    'step' => 1,
                ],
                'vh' => [
                    'min'  => 10,
                    'max'  => 100,
                    'step' => 1,
                ],
            ],
            'default'    => [
                'unit' => 'px',
                'size' => 260,
            ],
            'selectors'  => [
                '{{WRAPPER}} .tf-single-map iframe' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .tf-single-map .tf-single-map-div' => 'height: {{SIZE}}{{UNIT}} !important;',
            ],
        ] );
		
		$this->add_control('map_icon',[
			'label' => esc_html__('Map Icon', 'tourfic'),
			'default' => [
				'value' => 'fas fa-map-marker-alt',
				'library' => 'fa-solid',
			],
			'label_block' => true,
			'type' => Controls_Manager::ICONS,
			'fa4compatibility' => 'map_icon_comp',
		]);

        $this->add_control('show_icon',[
			'label' => esc_html__('Show Icon?', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
		]);

	    do_action( 'tf/single-map/after-content/controls', $this );

        $this->end_controls_section();
    }

	protected function render() {
		$settings  = $this->get_settings_for_display();
        $show_icon   = !empty( $settings['show_icon'] ) ? $settings['show_icon'] : '';
        // Map Type
        $tf_openstreet_map = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : "default";
        $post_id   = get_the_ID();
		$post_type = get_post_type();
        if($post_type == 'tf_hotel' || $post_type == 'tf_apartment' || $post_type == 'tf_carrental'){
            $meta_key = $post_type == 'tf_hotel' ? 'tf_hotels_opt' : ( $post_type == 'tf_apartment' ? 'tf_apartment_opt' : 'tf_carrental_opt' );
            $meta = get_post_meta($post_id, $meta_key, true);
            if( !empty($meta['map']) && Helper::tf_data_types($meta['map'])){
                $address = !empty( Helper::tf_data_types($meta['map'])['address'] ) ? Helper::tf_data_types($meta['map'])['address'] : '';
                $address_latitude = !empty( Helper::tf_data_types($meta['map'])['latitude'] ) ? Helper::tf_data_types($meta['map'])['latitude'] : '';
                $address_longitude = !empty( Helper::tf_data_types($meta['map'])['longitude'] ) ? Helper::tf_data_types($meta['map'])['longitude'] : '';
                $address_zoom = !empty( Helper::tf_data_types($meta['map'])['zoom'] ) ? Helper::tf_data_types($meta['map'])['zoom'] : '';
            }

        } elseif($post_type == 'tf_tours'){
			$meta = get_post_meta($post_id, 'tf_tours_opt', true);
            if( !empty($meta['location']) && Helper::tf_data_types($meta['location'])){
				$address = !empty( Helper::tf_data_types($meta['location'])['address'] ) ? Helper::tf_data_types($meta['location'])['address'] : '';
				$address_latitude = !empty( Helper::tf_data_types($meta['location'])['latitude'] ) ? Helper::tf_data_types($meta['location'])['latitude'] : '';
				$address_longitude = !empty( Helper::tf_data_types($meta['location'])['longitude'] ) ? Helper::tf_data_types($meta['location'])['longitude'] : '';
				$address_zoom = !empty( Helper::tf_data_types($meta['location'])['zoom'] ) ? Helper::tf_data_types($meta['location'])['zoom'] : '';
			}
			
        } else {
			return;
		}

        //map icon
		$map_icon_html = '<i class="fa-solid fa-location-dot"></i>';
		
        $map_icon_migrated = isset($settings['__fa4_migrated']['map_icon']);
        $map_icon_is_new = empty($settings['map_icon_comp']);

        if ( $map_icon_is_new || $map_icon_migrated ) {
            ob_start();
            Icons_Manager::render_icon( $settings['map_icon'], [ 'aria-hidden' => 'true' ] );
            $map_icon_html = ob_get_clean();
        } else{
            $map_icon_html = '<i class="' . esc_attr( $settings['map_icon_comp'] ) . '"></i>';
        }
        ?>
        <div class="tf-hotel-location-map tf-single-map">
            <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( ! empty( $address ) || (! empty( $address_latitude ) && ! empty( $address_longitude ) ) ) ) { ?>
                <?php if( $tf_openstreet_map!="default" ){ ?>
                    <div class="tf-hotel-location-preview show-on-map">
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="290" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

                        <?php if($show_icon == 'yes'): ?>
                        <a data-fancybox class="map-pre" data-src="#tf-hotel-google-maps" href="https://www.google.com/maps/search/<?php echo wp_kses_post($address); ?>">
                            <?php echo wp_kses( $map_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                <?php } ?>

                <?php if (  $tf_openstreet_map=="default" && !empty($address_latitude) && !empty($address_longitude) ) {  ?>
                    <div class="tf-hotel-location-preview show-on-map">
                        <div id="hotel-location" class="tf-single-map-div"></div>

                        <?php if($show_icon == 'yes'): ?>
                        <a data-fancybox class="map-pre" data-src="#tf-hotel-google-maps" href="https://www.google.com/maps/search/<?php echo wp_kses_post($address); ?>">
                            <?php echo wp_kses( $map_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                <?php } ?>

                <?php if (  $tf_openstreet_map=="default" && (empty($address_latitude) || empty($address_longitude)) ) {  ?>
                    <div class="tf-hotel-location-preview show-on-map">
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="290" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

                        <?php if($show_icon == 'yes'): ?>
                        <a data-fancybox class="map-pre" data-src="#tf-hotel-google-maps" href="https://www.google.com/maps/search/<?php echo wp_kses_post($address); ?>">
                            <?php echo wp_kses( $map_icon_html, Helper::tf_custom_wp_kses_allow_tags() ); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                <?php } ?>

                <div style="display: none;" id="tf-hotel-google-maps">
                    <div class="tf-hotel-google-maps-container">
                        <?php
                        if ( ! empty( $address ) ) { ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&z=17&output=embed" width="100%" height="550" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        <?php } else { ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&z=17&output=embed" width="100%" height="550" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>

        
        <?php
	}
}
