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
 * Share
 */
class Share extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-single-share';
	}

	public function get_title() {
		return esc_html__( 'Share', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-share';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'share',
			'tourfic',
			'socail',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-share'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-share/before-style-controls', $this );
		$this->tf_share_style_controls();
		do_action( 'tf/single-share/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_share_content',[
            'label' => esc_html__('Share', 'tourfic'),
        ]);

        do_action( 'tf/single-share/before-content/controls', $this );

        $this->add_control('share_style',[
            'label' => esc_html__('Share Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1 - Dropdown Icons', 'tourfic'),
                'style2' => esc_html__('Style 2 - Off Canvas', 'tourfic'),
                'style3' => esc_html__('Style 3 - Dropdown with Labels', 'tourfic'),
            ],
        ]);

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
		
		$this->add_control('share_icon',[
			'label' => esc_html__('Share Icon', 'tourfic'),
			'default' => [
				'value' => 'fas fa-share-nodes',
				'library' => 'fa-solid',
			],
			'label_block' => true,
			'type' => Controls_Manager::ICONS,
			'fa4compatibility' => 'share_icon_comp',
		]);

        $this->add_control('share_icons', [
			'label' => esc_html__('Share Icons', 'tourfic'),
			'type' => Controls_Manager::SELECT2,
			'label_block' => true,
			'multiple' => true,
			'options' => [
				'facebook' => esc_html__('Facebook', 'tourfic'),
				'twitter' => esc_html__('Twitter', 'tourfic'),
				'linkedin' => esc_html__('Linkedin', 'tourfic'),
				'pinterest' => esc_html__('Pinterest', 'tourfic'),
				'copy_link' => esc_html__('Copy Link', 'tourfic'),
			],
			'default' => ['facebook', 'twitter', 'linkedin', 'pinterest', 'copy_link'],
		]);

	    do_action( 'tf/single-share/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_share_style_controls() {
		$this->start_controls_section( 'share_style_section', [
			'label' => esc_html__( 'Share Icon Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

        $this->add_responsive_control( "tf_share_icon_size", [
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
				"{{WRAPPER}} .share-toggle i" => 'font-size: {{SIZE}}{{UNIT}}',
			],
		] );

        $this->add_responsive_control( "tf_share_icon_box_size", [
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
				"{{WRAPPER}} .tf-share-icon i" => 'height: {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}} !important;',
				"{{WRAPPER}} .tf-share-icon" => 'height: {{SIZE}}{{UNIT}} !important; width: {{SIZE}}{{UNIT}} !important;',
			],
            'condition' => [
				'icon_type' => 'rounded',
			],
		] );

		$this->start_controls_tabs( "tabs_share_icon_style" );
		/*-----Button NORMAL state------ */
		$this->start_controls_tab( "tab_share_icon_normal", [
			'label' => esc_html__( 'Normal', 'tourfic' ),
		] );
		$this->add_control( 'tf_share_icon_color', [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-share-icon i" => 'color: {{VALUE}};',
			],
		] );
		$this->add_control( 'share_icon_bg_color', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-share-icon i" => 'background-color: {{VALUE}};',
			],
            'condition' => [
				'icon_type' => 'rounded',
			],
		] );
		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "share_icon_border",
			'selector' => "{{WRAPPER}} .tf-share-icon i",
            'condition' => [
				'icon_type' => 'rounded',
			],
		] );
		$this->add_control( "share_icon_border_radius", [
			'label'      => esc_html__( 'Border Radius', 'tourfic' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-share-icon i" => $this->tf_apply_dim( 'border-radius' ),
			],
            'condition' => [
				'icon_type' => 'rounded',
			],
		] );
		$this->end_controls_tab();

		/*-----Button HOVER state------ */
		$this->start_controls_tab( "tab_share_icon_hover", [
			'label' => esc_html__( 'Active', 'tourfic' ),
		] );
		$this->add_control( "share_icon_color_hover", [
			'label'     => esc_html__( 'Icon Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-share-icon:hover i" => 'color: {{VALUE}};',
			],
		] );
		$this->add_control( 'share_icon_bg_color_hover', [
			'label'     => esc_html__( 'Background Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-share-icon i:hover" => 'background-color: {{VALUE}};',
			],
            'condition' => [
				'icon_type' => 'rounded',
			],
		] );
		$this->add_control( 'share_icon_border_color_hover', [
			'label'     => esc_html__( 'Border Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				"{{WRAPPER}} .tf-share-icon i:hover" => 'border-color: {{VALUE}};',
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
        $share_text = get_the_title();
	    $share_link = get_permalink( $post_id );
        $disable_share_sec = 0;
        
        // Get post meta based on post type
        if ($post_type == 'tf_hotel') {
            $post_meta = get_post_meta($post_id, 'tf_hotels_opt', true);
	        $disable_share_opt    = ! empty( $post_meta['h-share'] ) ? $post_meta['h-share'] : '';
        } elseif ($post_type == 'tf_tours') {
            $post_meta = get_post_meta($post_id, 'tf_tours_opt', true);
	        $disable_share_sec = ! empty( $post_meta['t-share'] ) ? $post_meta['t-share'] : 0;
        } elseif ($post_type == 'tf_carrental') {
            $post_meta = get_post_meta($post_id, 'tf_carrental_opt', true);
	        $disable_share_sec = ! empty( $post_meta['c-share'] ) ? $post_meta['c-share'] : 0;
        } else {
            return;
        }

        //Share icon
		$share_icon_html = '<i class="fa-solid fa-share-nodes"></i>';
		
        $share_icon_migrated = isset($settings['__fa4_migrated']['share_icon']);
        $share_icon_is_new = empty($settings['share_icon_comp']);

        if ( $share_icon_is_new || $share_icon_migrated ) {
            ob_start();
            Icons_Manager::render_icon( $settings['share_icon'], [ 'aria-hidden' => 'true' ] );
            $share_icon_html = ob_get_clean();
        } else{
            $share_icon_html = '<i class="' . esc_attr( $settings['share_icon_comp'] ) . '"></i>';
        }

        //icon type
        $style = !empty($settings['share_style']) ? $settings['share_style'] : 'style1';
        
        if ( $disable_share_opt !== '1' ):
            // Common social share links
            $social_links = [
                'facebook' => 'http://www.facebook.com/share.php?u=' . esc_url($share_link),
                'twitter' => 'http://twitter.com/share?text=' . esc_attr($share_text) . '&url=' . esc_url($share_link),
                'linkedin' => 'https://www.linkedin.com/cws/share?url=' . esc_url($share_link),
                'pinterest' => 'http://pinterest.com/pin/create/button/?url=' . esc_url($share_link) . '&media=' . esc_url(get_the_post_thumbnail_url()) . '&description=' . esc_attr($share_text)
            ];

            // Style 1: Dropdown with icons only
            if ($style == 'style1') {
                ?>
                <div class="tf-share">
                    <a href="#dropdown-share-center" class="share-toggle tf-icon tf-social-box" data-toggle="true">
                        <?php echo wp_kses($share_icon_html, Helper::tf_custom_wp_kses_allow_tags()); ?>
                    </a>

                    <div id="dropdown-share-center" class="share-tour-content">
                        <div class="tf-dropdown-share-content">
                            <h4><?php esc_html_e("Share with friends", "tourfic"); ?></h4>
                            <ul>
                                <?php foreach (['facebook', 'twitter', 'linkedin', 'pinterest'] as $network): ?>
                                <li>
                                    <a href="<?php echo $social_links[$network]; ?>" class="tf-dropdown-item" target="_blank">
                                        <span class="tf-dropdown-item-content">
                                            <i class="fab fa-<?php echo $network; ?>"></i>
                                        </span>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                                <li>
                                    <div title="<?php esc_attr_e('Share this link', 'tourfic'); ?>" aria-controls="share_link_button">
                                        <button id="share_link_button" class="tf_btn tf_btn_small share-center-copy-cta" tabindex="0" role="button">
                                            <i class="fa fa-link" aria-hidden="true"></i>
                                            <span class="tf-button-text share-center-copied-message"><?php esc_html_e('Link Copied!', 'tourfic'); ?></span>
                                        </button>
                                        <input type="text" id="share_link_input" class="share-center-url share-center-url-input" value="<?php echo esc_attr($share_link); ?>" readonly>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php
            }
            // Style 2: Off-canvas style
            elseif ($style == 'style2') {
                ?>
                <div class="tf-share tf-off-canvas-share-box tf-single-template__two">
                    <ul class="tf-off-canvas-share">
                        <?php foreach (['facebook', 'twitter', 'linkedin', 'pinterest'] as $network): ?>
                        <li>
                            <a href="<?php echo $social_links[$network]; ?>" class="tf-dropdown-item" target="_blank">
                                <span class="tf-dropdown-item-content">
                                    <i class="fab fa-<?php echo $network; ?>"></i>
                                </span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        <li>
                            <a href="#" id="share_link_button" class="share-center-copy-cta">
                                <i class="ri-links-line"></i>
                                <span class="tf-button-text share-center-copied-message"><?php esc_html_e('Link Copied!', 'tourfic'); ?></span>
                            </a>
                            <input type="text" id="share_link_input" class="share-center-url share-center-url-input" value="<?php echo esc_attr($share_link); ?>" readonly>
                        </li>
                    </ul>
                    <a href="#dropdown-share-center" class="tf-share-toggle tf-icon tf-social-box" data-toggle="true">
                        <?php echo wp_kses($share_icon_html, Helper::tf_custom_wp_kses_allow_tags()); ?>
                    </a>
                </div>
                <?php
            }
            // Style 3: Dropdown with text labels
            elseif ($style == 'style3') {
                $network_labels = [
                    'facebook' => esc_html__('Share on Facebook', 'tourfic'),
                    'twitter' => esc_html__('Share on Twitter', 'tourfic'),
                    'linkedin' => esc_html__('Share on Linkedin', 'tourfic'),
                    'pinterest' => esc_html__('Share on Pinterest', 'tourfic')
                ];
                ?>
                <div class="tf-share">
                    <a href="#dropdown-share-center" class="share-toggle" data-toggle="true">
                        <?php echo wp_kses($share_icon_html, Helper::tf_custom_wp_kses_allow_tags()); ?>
                    </a>
                    <div id="dropdown-share-center" class="share-tour-content">
                        <ul class="tf-dropdown-content">
                            <?php foreach (['facebook', 'twitter', 'linkedin', 'pinterest'] as $network): ?>
                            <li>
                                <a href="<?php echo $social_links[$network]; ?>" class="tf-dropdown-item" target="_blank">
                                    <span class="tf-dropdown-item-content">
                                        <i class="fab fa-<?php echo $network; ?>-square"></i>
                                        <?php echo esc_html($network_labels[$network]); ?>
                                    </span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                            <li>
                                <div class="share-center-copy-form tf-dropdown-item" title="<?php esc_attr_e('Share this link', 'tourfic'); ?>" aria-controls="share_link_button">
                                    <label class="share-center-copy-label" for="share_link_input"><?php esc_html_e('Share this link', 'tourfic'); ?></label>
                                    <input type="text" id="share_link_input" class="share-center-url share-center-url-input" value="<?php echo esc_attr($share_link); ?>" readonly>
                                    <button id="share_link_button" class="tf_btn tf_btn_small share-center-copy-cta" tabindex="0" role="button">
                                        <span class="tf-button-text share-center-copy-message"><?php esc_html_e('Copy link', 'tourfic'); ?></span>
                                        <span class="tf-button-text share-center-copied-message"><?php esc_html_e('Link Copied!', 'tourfic'); ?></span>
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php
            }
        endif;
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
