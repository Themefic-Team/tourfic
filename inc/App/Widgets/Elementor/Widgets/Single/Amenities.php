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
 * Amenities
 */
class Amenities extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-amenities';
	}

	public function get_title() {
		return esc_html__( 'Amenities', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-library-list';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'features',
            'amenities',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-amenities'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-amenities/before-style-controls', $this );
		$this->tf_amenities_style_controls();
		do_action( 'tf/single-amenities/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_amenities_content',[
            'label' => esc_html__('Amenities', 'tourfic'),
        ]);

        do_action( 'tf/single-amenities/before-content/controls', $this );
		
		$this->add_control('amenities_style',[
            'label' => esc_html__('Amenities Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1', 'tourfic'),
                'style2' => esc_html__('Style 2', 'tourfic'),
            ],
        ]);

	    do_action( 'tf/single-amenities/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_amenities_style_controls() {
		$this->start_controls_section( 'amenities_title_style', [
			'label' => esc_html__( 'Amenities Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

        $this->add_control( 'tf_title_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Title', 'tourfic' ),
		] );

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
				'{{WRAPPER}} h2.section-heading' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Title Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-section-title, {{WRAPPER}} .section-heading",
		]);

        $this->add_control( 'tf_category_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Category', 'tourfic' ),
		] );

		$this->add_control( 'tf_category_color', [
			'label'     => esc_html__( 'Category Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .single-facilities-title' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-hotel-facilities-content-area .hotel-facility-item .hotel-single-facility-title' => 'color: {{VALUE}};',
				'{{WRAPPER}} #tf-amenities-modal .tf-apartment-amenity-cat h3' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Category Typography', 'tourfic' ),
			'name'     => "tf_category_typography",
			'selector' => "{{WRAPPER}} .single-facilities-title, {{WRAPPER}} .tf-hotel-facilities-content-area .hotel-facility-item .hotel-single-facility-title, {{WRAPPER}} #tf-amenities-modal .tf-apartment-amenity-cat h3",
		]);

        $this->add_control( 'tf_feature_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Feature', 'tourfic' ),
		] );

		$this->add_control( 'tf_feature_color', [
			'label'     => esc_html__( 'Feature Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-facilities .tf-facility-item ul li' => 'color: {{VALUE}};',
				'{{WRAPPER}} .hotel-facility-item ul li' => 'color: {{VALUE}};',
				'{{WRAPPER}} .tf-apartment-amenities .tf-apt-amenity' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Feature Typography', 'tourfic' ),
			'name'     => "tf_feature_typography",
			'selector' => "{{WRAPPER}} .tf-facilities .tf-facility-item ul li, {{WRAPPER}} .hotel-facility-item ul li, {{WRAPPER}} .tf-apartment-amenities .tf-apt-amenity",
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

        if($this->post_type == 'tf_hotel'){
            $this->tf_hotel_amenities($settings);
        } elseif($this->post_type == 'tf_apartment'){
            $this->tf_apartment_amenities($settings);
        } else {
			return;
		}
	}

    private function tf_hotel_amenities($settings) {
        $style = !empty($settings['amenities_style']) ? $settings['amenities_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_hotels_opt', true);
        $facilities_categories = ! empty( Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) : '';
        $facilities_title = !empty($meta['facilities-section-title']) ? esc_html($meta['facilities-section-title']) : esc_html__("Property facilities", 'tourfic');
        $facilities = !empty($meta['hotel-facilities']) ? Helper::tf_data_types($meta['hotel-facilities']) : [];
		
		if ($style == 'style1' && !empty($facilities_categories) && !empty($facilities)) {
            ?>
			<div class="tf-facilities-wrapper tf-single-amenities-style1" id="tf-hotel-facilities">
                <h2 class="tf-section-title"><?php echo esc_html($facilities_title); ?></h2>          
                <div class="tf-facilities">
                    <?php 
                    $facilites_list = [];
                    if( !empty($facilities) && is_array($facilities) ){
                        foreach( $facilities as $facility ){
                            $facilites_list [$facility['facilities-category']] = $facility['facilities-category'];
                        }
                    }
                    if(!empty($facilites_list)){
                    foreach( $facilites_list as $catkey=> $single_feature ){
                    ?>
                    <div class="tf-facility-item">
                        <?php $f_icon_single  = ! empty( $facilities_categories[$catkey]['hotel_facilities_cat_icon'] ) ? esc_attr($facilities_categories[$catkey]['hotel_facilities_cat_icon']) : '';?>
                        <span class="single-facilities-title">
                            <?php echo !empty($f_icon_single) ? '<i class="' . esc_attr($f_icon_single) . '"></i>' : ''; ?> 
                            <?php echo !empty($facilities_categories[$catkey]['hotel_facilities_cat_name']) ? esc_html($facilities_categories[$catkey]['hotel_facilities_cat_name']) : ''; ?>
                        </span>
                        <ul>
                        <?php 
                        foreach( $meta['hotel-facilities'] as $facility ){ 
                            if( $facility['facilities-category'] == $catkey ){
                                $features_details = !empty( $facility['facilities-feature'] ) ? get_term( $facility['facilities-feature'] ) : '';
                                
                                if(!empty($features_details->name)){
                                ?>
                                <li>
                                    <?php echo esc_html($features_details->name); ?>
                                </li>
                                <?php 
                                }
                            }
                        } ?>
                        </ul>
                    </div>
                    <?php } } ?>
                </div>
            </div>
            <?php
        } elseif ($style == 'style2' && !empty($facilities_categories) && !empty($facilities)) {
            ?>
			<div class="tf-hotel-facilities-section tf-template-section tf-single-hotel-amenities-style2">
                <div class="tf-hotel-facilities-title">
                    <h2 class="section-heading" ><?php echo esc_html($facilities_title); ?></h2>
                </div>
                <div class="tf-hotel-facilities-content-area">
                    <?php
                        $facilities_list = [];
                        if( !empty($facilities) && is_array($facilities) ){
                            foreach( $facilities as $facility ){
                                $facilities_list[$facility['facilities-category']] = $facility['facilities-category'];
                            }
                        }

                        if (!empty($facilities_list)) {
                            foreach($facilities_list as $key => $single_feature ) {
                                $f_icon_single  = ! empty( $facilities_categories[$key]['hotel_facilities_cat_icon'] ) ? esc_attr($facilities_categories[$key]['hotel_facilities_cat_icon']) : '';
                                ?>
                                <div class="hotel-facility-item">
                                    <div class="hotel-single-facility-title">
                                        <?php echo !empty($facilities_categories[$key]['hotel_facilities_cat_name']) ? esc_html($facilities_categories[$key]['hotel_facilities_cat_name']) : ''; ?>
                                    </div>
                                    <ul>
                                        <?php
                                        foreach( $facilities as $facility ) :
                                            if( $facility['facilities-category'] == $key ) {
                                                $features_details = !empty( $facility['facilities-feature'] ) ? get_term( $facility['facilities-feature'] ) : '';
                                                $feature_meta = get_term_meta( $facility['facilities-feature'], 'tf_hotel_feature', true );

                                                $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                                                if ( $f_icon_type == 'fa' && !empty($feature_meta['icon-fa']) ) {
                                                    $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
                                                } else if ( $f_icon_type == 'c' && !empty($feature_meta['icon-c']) ) {
                                                    $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
                                                } else {
                                                    $feature_icon = '<i class="ri-check-line"></i>';
                                                }

                                                if(!empty($features_details->name)) {
                                                    ?>
                                                    <li>
                                                    <span><?php echo !empty($feature_meta) && !empty($feature_icon) ? wp_kses_post($feature_icon) : ''; ?></span>
                                                    <?php echo esc_html($features_details->name); ?>
                                                    </li>
                                                <?php } ?>
                                            <?php } ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php
                            }
                        }
                    ?>
                </div>
            </div>
			<?php
        }
	}

    private function tf_apartment_amenities($settings) {
        $style = !empty($settings['amenities_style']) ? $settings['amenities_style'] : 'style1';
		$meta = get_post_meta($this->post_id, 'tf_apartment_opt', true);
        $facilities_categories = ! empty( Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) : '';
        $facilities_title = ! empty( $meta['amenities_title'] ) ? esc_html( $meta['amenities_title'] ) : esc_html__( "What this place offers", "tourfic" );
        $facilities = !empty($meta['amenities']) ? Helper::tf_data_types($meta['amenities']) : [];
		
		if ($style == 'style1') {
            ?>
			<div class="tf-facilities-wrapper tf-single-amenities-style1" id="tf-apartment-facilities">
                <h2 class="tf-section-title"><?php echo esc_html($facilities_title); ?></h2>
                <div class="tf-facilities">
                    <?php
                    $facilites_list = [];
                    if ( ! empty( $facilities ) && is_array( $facilities ) ) {
                        foreach ( $facilities as $facility ) {
                            $facilites_list[ $facility['cat'] ] = $facility['cat'];
                        }
                    }
                    if ( ! empty( $facilites_list ) ) {
                        foreach ( $facilites_list as $catkey => $single_feature ) {
                            ?>
                            <div class="tf-facility-item">
                                <?php $f_icon_single = ! empty( $facilities_categories[ $catkey ]['amenities_cat_icon'] ) ? $facilities_categories[ $catkey ]['amenities_cat_icon'] : ''; ?>
                                <span class="single-facilities-title">
                                    <?php echo ! empty( $f_icon_single ) ? '<i class="' . esc_attr( $f_icon_single ) . '"></i>' : ''; ?>
                                    <?php echo ! empty( $facilities_categories[ $catkey ]['amenities_cat_name'] ) ? esc_html( $facilities_categories[ $catkey ]['amenities_cat_name'] ) : ''; ?>
                                </span>
                                <ul>
                                <?php
                                if ( ! empty( $facilities ) ) {
                                    foreach ( $facilities as $facility ) {
                                        if ( $facility['cat'] == $catkey ) {
                                            $features_details = get_term( $facility['feature'] );
                                            if ( ! empty( $features_details->name ) ) {
                                                ?>
                                                <li>
                                                    <?php echo esc_html( $features_details->name ); ?>
                                                </li>
                                            <?php }
                                        }
                                    }
                                } ?>
                                </ul>
                            </div>
                        <?php }
                    } ?>
                </div>
            </div>
            <?php
        } elseif ($style == 'style2' && isset( $meta['amenities'] ) && ! empty( Helper::tf_data_types( $meta['amenities'] ) )) {
            
            $fav_amenities = array();
            $other_amenities = array();
            foreach (Helper::tf_data_types($meta['amenities']) as $amenity) {
                if (!isset($amenity['favorite']) || $amenity['favorite'] !== '1') {
                    $other_amenities[] = $amenity;
                } else {
                    $fav_amenities[] = $amenity;
                }
            }
            $all_amenities = array_merge($fav_amenities, $other_amenities);
            ?>
            <!-- Start Key Features Section -->
            <div class="tf-apartment-amenities-section tf-single-apartment-amenities-style2">
                <h2 class="section-heading"><?php echo esc_html($facilities_title); ?></h2>
                <div class="tf-apartment-amenities-inner">
                    <div class="tf-apartment-amenities">
                        <?php if ( ! empty( $all_amenities ) ):
                            foreach ( array_slice( $all_amenities, 0, 10 ) as $amenity ) :
                                $feature =  isset( $amenity['feature'] ) ? get_term_by( 'id', $amenity['feature'], 'apartment_feature' ) : '';
                                $feature_meta = isset( $amenity['feature'] ) ? get_term_meta( $amenity['feature'], 'tf_apartment_feature', true ) : '';
                                $f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                                if ( $f_icon_type == 'icon' ) {
                                    $feature_icon = '<i class="' . $feature_meta['apartment-feature-icon'] . '"></i>';
                                } elseif ( $f_icon_type == 'custom' ) {
                                    $feature_icon = '<img src="' . esc_url( $feature_meta['apartment-feature-icon-custom'] ) . '" style="width: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px; height: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px;" />';
                                }
                                ?>
                                <div class="tf-apt-amenity">
                                    <?php echo ! empty( $feature_meta['apartment-feature-icon'] ) || !empty($feature_meta['apartment-feature-icon-custom']) ? "<div class='tf-apt-amenity-icon'>" . wp_kses_post( $feature_icon ) . "</div>" : ""; ?>
                                    <?php if(!empty($feature->name)){ ?>
                                    <span><?php echo esc_html( $feature->name ); ?></span>
                                    <?php } ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php if ( count( Helper::tf_data_types( $meta['amenities'] ) ) > 10 ): ?>
                        <div class="tf-apartment-amenities-more">
                            <a class="tf-modal-btn" data-target="#tf-amenities-modal">
                                <?php esc_html_e( 'All Amenities', 'tourfic' ) ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M10.0252 4.22852L9.08457 5.17353L11.2647 7.34351L2.1947 7.35263L2.19604 8.68597L11.2412 8.67686L9.09779 10.8304L10.0428 11.771L13.8052 7.99092L10.0252 4.22852Z"
                                            fill="#2A3343"/>
                                </svg>
                            </a>
                        </div>

                        <!-- Modal -->
                        <div class="tf-modal" id="tf-amenities-modal">
                            <div class="tf-modal-dialog">
                                <div class="tf-modal-content">
                                    <div class="tf-modal-header">
                                        <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                                    </div>
                                    <div class="tf-modal-body">
                                        <h2 class="section-heading"><?php echo ! empty( $meta['amenities_title'] ) ? esc_html( $meta['amenities_title'] ) : ''; ?></h2>
                                        <?php
                                        $categories     = [];
                                        $amenities_cats = ! empty( Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) : '';
                                        foreach ( Helper::tf_data_types( $meta['amenities'] ) as $amenity ) {
                                            $cat     = $amenity['cat'];
                                            $feature = $amenity['feature'];

                                            // Check if the category exists in the $categories array
                                            if ( ! isset( $categories[ $cat ] ) ) {
                                                $categories[ $cat ] = [];
                                            }

                                            // Add the feature to the category
                                            $categories[ $cat ][] = $feature;
                                        }

                                        foreach ( $categories as $cat => $features ) :
                                            ?>
                                            <div class="tf-apartment-amenity-cat">
                                                <h3><?php echo ! empty( $amenities_cats[ $cat ]['amenities_cat_name'] ) ? esc_html( $amenities_cats[ $cat ]['amenities_cat_name'] ) : ''; ?></h3>
                                                <div class="tf-apartment-amenities">
                                                    <?php foreach ( $features as $feature_id ):
                                                        $_feature = get_term_by( 'id', $feature_id, 'apartment_feature' );
                                                        $_feature_meta = get_term_meta( $feature_id, 'tf_apartment_feature', true );
                                                        $f_icon_type = ! empty( $_feature_meta['icon-type'] ) ? $_feature_meta['icon-type'] : '';
                                                        if ( $f_icon_type == 'icon' ) {
                                                            $feature_icon = '<i class="' . $_feature_meta['apartment-feature-icon'] . '"></i>';
                                                        } elseif ( $f_icon_type == 'custom' ) {
                                                            $feature_icon = '<img src="' . esc_url( $_feature_meta['apartment-feature-icon-custom'] ) . '" style="width: ' . $_feature_meta['apartment-feature-icon-dimension'] . 'px; height: ' . $_feature_meta['apartment-feature-icon-dimension'] . 'px;" />';
                                                        }
                                                        ?>
                                                        <div class="tf-apt-amenity">
                                                            <?php echo ! empty( $_feature_meta['apartment-feature-icon'] ) || !empty($_feature_meta['apartment-feature-icon-custom']) ? "<div class='tf-apt-amenity-icon'>" . wp_kses_post( $feature_icon ) . "</div>" : ""; ?>
                                                            <span><?php echo esc_html( $_feature->name ); ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
			<?php
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
