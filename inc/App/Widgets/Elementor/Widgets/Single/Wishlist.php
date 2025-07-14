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

		$this->add_control( 'tf_wishlist_color', [
			'label'     => __( 'Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-wishlist' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => __( 'Typography', 'tourfic' ),
			'name'     => "tf_wishlist_typography",
			'selector' => "{{WRAPPER}} .tf-wishlist",
		]);

		$this->add_responsive_control( "tf_wishlist_icon_size", [
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
				"{{WRAPPER}} .tf-wishlist i" => 'font-size: {{SIZE}}{{UNIT}}',
				"{{WRAPPER}} .tf-wishlist svg" => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->add_control( "tf_wishlist_icon_color", [
			'label'     => __( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-wishlist i" => 'color: {{VALUE}}',
				"{{WRAPPER}} .tf-wishlist svg path" => 'fill: {{VALUE}}',
			],
		] );

		$this->add_control( 'tf_link_type_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Link Style', 'tourfic' ),
		] );

		$this->add_control( 'tf_link_color', [
			'label'     => __( 'Link Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .more-hotel' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => __( 'Link Typography', 'tourfic' ),
			'name'     => "tf_link_typography",
			'selector' => "{{WRAPPER}} .more-hotel",
		]);

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $post_id   = get_the_ID();
        $post_type = get_post_type();
        $has_in_wishlist = Wishlist_Class::tf_has_item_in_wishlist( $post_id );
        $wishlist = '';
        $disable_wishlist_sec = 0;
        
        // Get post meta based on post type
        if ($post_type == 'tf_hotel') {
            $post_meta = get_post_meta($post_id, 'tf_hotels_opt', true);
            $disable_wishlist_sec = !empty($post_meta['h-wishlist']) ? $post_meta['h-wishlist'] : 0;
        } elseif ($post_type == 'tf_tours') {
            $post_meta = get_post_meta($post_id, 'tf_tours_opt', true);
            // Add disable_wishlist_sec logic if needed for tours
        } elseif ($post_type == 'tf_apartment') {
            $post_meta = get_post_meta($post_id, 'tf_apartment_opt', true);
            // Add disable_wishlist_sec logic if needed for apartments
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
        
        // Render wishlist if not disabled and conditions are met
        if ($disable_wishlist_sec != 1 && Helper::tfopt('wl-bt-for') && in_array('1', Helper::tfopt('wl-bt-for'))) {
            $show_for_logged_in = is_user_logged_in() && Helper::tfopt('wl-for') && in_array('li', Helper::tfopt('wl-for'));
            $show_for_logged_out = !is_user_logged_in() && Helper::tfopt('wl-for') && in_array('lo', Helper::tfopt('wl-for'));
            
            if ($show_for_logged_in || $show_for_logged_out) {
                echo '<div class="tf-icon tf-wishlist-box">';
                echo wp_kses($wishlist_icon_html, Helper::tf_custom_wp_kses_allow_tags());
                echo '</div>';
            }
        }
    }
}
