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
		$this->tf_map_style_controls();
		do_action( 'tf/single-map/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_map_content',[
            'label' => esc_html__('Map', 'tourfic'),
        ]);

        do_action( 'tf/single-map/before-content/controls', $this );
		
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

        $this->add_control('show_location',[
			'label' => esc_html__('Show Location Link?', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
		]);

		$this->add_responsive_control('map-align',[
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

	    do_action( 'tf/single-map/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_map_style_controls() {
		$this->start_controls_section( 'map_style', [
			'label' => esc_html__( 'Map Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control( 'tf_map_color', [
			'label'     => esc_html__( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-map' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Typography', 'tourfic' ),
			'name'     => "tf_map_typography",
			'selector' => "{{WRAPPER}} .tf-map",
		]);

		$this->add_responsive_control( "tf_map_icon_size", [
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
				"{{WRAPPER}} .tf-map i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-map svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( "tf_map_icon_color", [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-map i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-map svg path" => 'fill: {{VALUE}}',
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
        // Map Type
        $tf_openstreet_map = ! empty( Helper::tfopt( 'google-page-option' ) ) ? Helper::tfopt( 'google-page-option' ) : "default";
        $post_id   = get_the_ID();
		$post_type = get_post_type();
        $locations = $address = '';
        if($post_type == 'tf_hotel' || $post_type == 'tf_apartment' || $post_type == 'tf_carrental'){
            $meta = get_post_meta($post_id, 'tf_hotels_opt', true);
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
        ?>
        <div class="tf-hotel-location-map">
            <?php if ( !defined( 'TF_PRO' ) && !empty( $address ) && $tf_openstreet_map!="default" && (empty($address_latitude) || empty($address_longitude)) ) { ?>
                <div class="tf-hotel-location-preview show-on-map">
                <iframe src="https://maps.google.com/maps?q=<?php echo wp_kses_post($address); ?>&output=embed" width="100%" height="258" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <a href="https://www.google.com/maps/search/<?php echo wp_kses_post($address); ?>" class="map-pre" target="_blank"><i class="fa-solid fa-location-dot"></i></a>
                </div>
            <?php } elseif ( !defined( 'TF_PRO' ) && !empty( $address ) && $tf_openstreet_map=="default" && !empty($address_latitude) && !empty($address_longitude)) {  ?>
                <div class="tf-hotel-location-preview show-on-map">
                    <div id="hotel-location"></div>
                </div>
                <script>
                    const map = L.map('hotel-location').setView([<?php echo esc_html($address_latitude); ?>, <?php echo esc_html($address_longitude); ?>], <?php echo esc_html($address_zoom); ?>);

                    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 20,
                        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                    }).addTo(map);

                    const marker = L.marker([<?php echo esc_html($address_latitude); ?>, <?php echo esc_html($address_longitude); ?>], {alt: '<?php echo esc_html($address); ?>'}).addTo(map)
                        .bindPopup('<?php echo esc_html($address); ?>');
                </script>
            <?php } elseif ( !defined( 'TF_PRO' ) && !empty( $address ) && $tf_openstreet_map=="default" && (empty($address_latitude) || empty($address_longitude)) ) {  ?>
                <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="258" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            <?php } ?>

            <!-- Pro Code -->
            <?php if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && ( ! empty( $address ) || (! empty( $address_latitude ) && ! empty( $address_longitude ) ) ) ) { ?>
                <?php 
                if( $tf_openstreet_map!="default" ){ ?>
                <div class="tf-hotel-location-preview show-on-map">
                    <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="290"
                            style="border:0;" allowfullscreen="" loading="lazy"></iframe>

                    <a data-fancybox class="map-pre" data-src="#tf-hotel-google-maps" href="https://www.google.com/maps/search/<?php echo wp_kses_post($address); ?>">
                        <i class="fa-solid fa-location-dot"></i>
                    </a>

                </div>
                <?php } ?>
                <?php if (  $tf_openstreet_map=="default" && !empty($address_latitude) && !empty($address_longitude) ) {  ?>
                    <div class="tf-hotel-location-preview show-on-map">
                        <div id="hotel-location"></div>
                        <a data-fancybox class="map-pre" data-src="#tf-hotel-google-maps" href="https://www.google.com/maps/search/<?php echo wp_kses_post($address); ?>">
                        <i class="fa-solid fa-location-dot"></i>
                    </a>
                    </div>
                    <script>
                        const map = L.map('hotel-location').setView([<?php echo esc_html($address_latitude); ?>, <?php echo esc_html($address_longitude); ?>], <?php echo esc_html($address_zoom); ?>);

                        const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 20,
                            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                        }).addTo(map);

                        const marker = L.marker([<?php echo esc_html($address_latitude); ?>, <?php echo esc_html($address_longitude); ?>], {alt: '<?php echo esc_html($address); ?>'}).addTo(map)
                            .bindPopup('<?php echo esc_html($address); ?>');
                    </script>
                <?php } ?>

                <?php if (  $tf_openstreet_map=="default" && (empty($address_latitude) || empty($address_longitude)) ) {  ?>
                    <div class="tf-hotel-location-preview show-on-map">
                        <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&output=embed" width="100%" height="290"
                                style="border:0;" allowfullscreen="" loading="lazy"></iframe>

                        <a data-fancybox class="map-pre" data-src="#tf-hotel-google-maps" href="https://www.google.com/maps/search/<?php echo wp_kses_post($address); ?>">
                            <i class="fa-solid fa-location-dot"></i>
                        </a>

                    </div>
                <?php } ?>

                <div style="display: none;" id="tf-hotel-google-maps">
                    <div class="tf-hotel-google-maps-container">
                        <?php
                        if ( ! empty( $address ) ) { ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( str_replace( "#", "", $address ) ); ?>&z=17&output=embed" width="100%" height="550" style="border:0;"
                                    allowfullscreen="" loading="lazy"></iframe>
                        <?php } else { ?>
                            <iframe src="https://maps.google.com/maps?q=<?php echo esc_attr( $address_latitude ); ?>,<?php echo esc_attr( $address_longitude ); ?>&z=17&output=embed" width="100%" height="550"
                                    style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php
	}
}
