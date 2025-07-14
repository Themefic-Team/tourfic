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
use Tourfic\App\Wishlist as Wishlist_Class;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Wishlist
 */
class Wishlist extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-single-wishlist';
	}

	public function get_title() {
		return esc_html__( 'Wishlist', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-heart-o';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'bookmark',
            'wishlist',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-wishlist'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-wishlist/before-style-controls', $this );
		$this->tf_wishlist_style_controls();
		do_action( 'tf/single-wishlist/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_wishlist_content',[
            'label' => esc_html__('Wishlist', 'tourfic'),
        ]);

        do_action( 'tf/single-wishlist/before-content/controls', $this );

        //icon type
        $this->add_control('icon_type',[
			'type'     => Controls_Manager::SELECT,
			'label'    => esc_html__( 'Icon Type', 'tourfic' ),
			'options'  => [
				'simple'     => esc_html__( 'Simple', 'tourfic' ),
				'rounded'     => esc_html__( 'Rounded', 'tourfic' ),
			],
			'default'  => 'rounded',
		]);
		
		$this->add_control('wishlist_icon',[
			'label' => esc_html__('Wishlist Icon', 'tourfic'),
			'default' => [
				'value' => 'far fa-heart',
				'library' => 'fa-regular',
			],
			'label_block' => true,
			'type' => Controls_Manager::ICONS,
			'fa4compatibility' => 'wishlist_icon_comp',
		]);

		$this->add_control('wishlist_active_icon',[
			'label' => esc_html__('Wishlist Icon', 'tourfic'),
			'default' => [
				'value' => 'fas fa-heart',
				'library' => 'fa-solid',
			],
			'label_block' => true,
			'type' => Controls_Manager::ICONS,
			'fa4compatibility' => 'wishlist_active_icon_comp',
		]);

	    do_action( 'tf/single-wishlist/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_wishlist_style_controls() {
		$this->start_controls_section( 'wishlist_style', [
			'label' => __( 'Wishlist Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

        $this->add_responsive_control( "tf_wishlist_icon_size", [
			'label'      => esc_html__( 'Icon Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
			],
			'range'      => [
				'px' => [
					'min'  => 5,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-wishlist-icon i" => 'font-size: {{SIZE}}{{UNIT}}',
			],
		] );

        $this->add_responsive_control( "tf_wishlist_icon_box_size", [
			'label'      => esc_html__( 'Box Size', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'rem',
			],
			'range'      => [
				'px' => [
					'min'  => 30,
					'max'  => 100,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-wishlist-icon i" => 'height: {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}} !important;',
				"{{WRAPPER}} .tf-wishlist-icon" => 'height: {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}} !important;',
			],
            'condition' => [
				'icon_type' => 'rounded',
			],
		] );

		$this->start_controls_tabs( "tabs_wishlist_icon_style" );
		/*-----Button NORMAL state------ */
		$this->start_controls_tab( "tab_wishlist_icon_normal", [
			'label' => __( 'Normal', 'tourfic' ),
		] );
		$this->add_control( 'tf_wishlist_icon_color', [
			'label'     => __( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-wishlist-icon i" => 'color: {{VALUE}};',
			],
		] );
		$this->add_control( 'wishlist_icon_bg_color', [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-wishlist-icon i" => 'background-color: {{VALUE}};',
			],
            'condition' => [
				'icon_type' => 'rounded',
			],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "wishlist_icon_border",
			'selector' => "{{WRAPPER}} .tf-wishlist-icon i",
            'condition' => [
				'icon_type' => 'rounded',
			],
		] );
		$this->add_control( "wishlist_icon_border_radius", [
			'label'      => __( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-wishlist-icon i" => $this->tf_apply_dim( 'border-radius' ),
			],
            'condition' => [
				'icon_type' => 'rounded',
			],
		] );
		$this->end_controls_tab();

		/*-----Button HOVER state------ */
		$this->start_controls_tab( "tab_wishlist_icon_hover", [
			'label' => __( 'Active', 'tourfic' ),
		] );
		$this->add_control( "wishlist_icon_color_hover", [
			'label'     => __( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-wishlist-icon:hover i" => 'color: {{VALUE}};',
			],
		] );
		$this->add_control( 'wishlist_icon_bg_color_hover', [
			'label'     => __( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-wishlist-icon i:hover" => 'background-color: {{VALUE}};',
			],
            'condition' => [
				'icon_type' => 'rounded',
			],
		] );
		$this->add_control( 'wishlist_icon_border_color_hover', [
			'label'     => __( 'Border Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-wishlist-icon i:hover" => 'border-color: {{VALUE}};',
			],
            'condition' => [
				'icon_type' => 'rounded',
			],
		] );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		/*-----ends Button tabs--------*/

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $post_id   = get_the_ID();
        $post_type = get_post_type();
        $has_in_wishlist = Wishlist_Class::tf_has_item_in_wishlist( $post_id );
        $disable_wishlist_sec = 0;
        
        // Get post meta based on post type
        if ($post_type == 'tf_hotel') {
            $post_meta = get_post_meta($post_id, 'tf_hotels_opt', true);
            $disable_wishlist_sec = !empty($post_meta['h-wishlist']) ? $post_meta['h-wishlist'] : 0;
        } elseif ($post_type == 'tf_tours') {
            $post_meta = get_post_meta($post_id, 'tf_tours_opt', true);
	        $disable_wishlist_sec = ! empty( $post_meta['t-wishlist'] ) ? $post_meta['t-wishlist'] : 0;
        } elseif ($post_type == 'tf_carrental') {
            $post_meta = get_post_meta($post_id, 'tf_carrental_opt', true);
	        $disable_wishlist_sec = ! empty( $post_meta['c-wishlist'] ) ? $post_meta['c-wishlist'] : 0;
        } else {
            return;
        }

        // Generate wishlist icon HTML with dynamic classes and data attributes
        $wishlist_icon_classes = $has_in_wishlist ? 'tf-text-red remove-wishlist' : 'add-wishlist';
        $wishlist_data_attrs = sprintf(
            'data-nonce="%s" data-id="%s" data-type="%s" data-icon="%s" data-active-icon="%s"',
            esc_attr(wp_create_nonce("wishlist-nonce")),
            esc_attr($post_id),
            esc_attr($post_type),
            esc_attr($settings['wishlist_icon']['value']),
            esc_attr($settings['wishlist_active_icon']['value']),
        );
        
        // Add page data if available
        if (Helper::tfopt('wl-page')) {
            $wishlist_data_attrs .= sprintf(
                ' data-page-title="%s" data-page-url="%s"',
                esc_html(get_the_title(Helper::tfopt('wl-page'))),
                esc_url(get_permalink(Helper::tfopt('wl-page')))
            );
        }

        // Build the complete wishlist icon HTML
        $wishlist_icon_html = sprintf(
            '<i class="%s" %s></i>',
            $wishlist_icon_classes,
            $wishlist_data_attrs
        );

        // Handle Elementor icon migration (fallback for custom icons)
        if (!empty($settings['wishlist_icon']['value'])) {
            $wishlist_icon_html = sprintf(
                '<i class="%s %s" %s></i>',
                $has_in_wishlist ? esc_attr($settings['wishlist_active_icon']['value']) : esc_attr($settings['wishlist_icon']['value']),
                $wishlist_icon_classes,
                $wishlist_data_attrs
            );
        }

        //icon type
        $icon_type = !empty($settings['icon_type']) ? $settings['icon_type'] : 'rounded';
        
        // Render wishlist if not disabled and conditions are met
        if ($disable_wishlist_sec != 1 && Helper::tfopt('wl-bt-for') && in_array('1', Helper::tfopt('wl-bt-for'))) {
            $show_for_logged_in = is_user_logged_in() && Helper::tfopt('wl-for') && in_array('li', Helper::tfopt('wl-for'));
            $show_for_logged_out = !is_user_logged_in() && Helper::tfopt('wl-for') && in_array('lo', Helper::tfopt('wl-for'));
            $icon_class = $icon_type == 'rounded' ? "tf-icon tf-wishlist-icon" : "tf-wishlist-icon tf-wishlist-button";
            
            if ($show_for_logged_in || $show_for_logged_out) {
                echo '<div class="'. esc_attr($icon_class) .'">';
                echo wp_kses($wishlist_icon_html, Helper::tf_custom_wp_kses_allow_tags());
                echo '</div>';
            }
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
}
