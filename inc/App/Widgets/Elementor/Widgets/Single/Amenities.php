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
			'label' => esc_html__( 'Amenities Title Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Title Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control( Group_Control_Typography::get_type(), [
            'label'    => esc_html__( 'Title Typography', 'tourfic' ),
			'name'     => "tf_title_typography",
			'selector' => "{{WRAPPER}} .tf-section-title",
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

        if($this->post_type == 'tf_hotel'){
            $meta = get_post_meta($this->post_id, 'tf_hotels_opt', true);
			$total_facilities_cat = ! empty( Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) : '';
            $facilities_title = !empty($meta['facilities-section-title']) ? esc_html($meta['facilities-section-title']) : esc_html__("Property facilities", 'tourfic');

        } elseif($this->post_type == 'tf_tours'){
			$meta = get_post_meta($this->post_id, 'tf_tours_opt', true);

        } elseif($this->post_type == 'tf_apartment'){
			$meta = get_post_meta($this->post_id, 'tf_apartment_opt', true);

        } elseif($this->post_type == 'tf_carrental'){
			$meta = get_post_meta($this->post_id, 'tf_carrental_opt', true);
            
        } else {
			return;
		}

        //amenities style
        $style = !empty($settings['amenities_style']) ? $settings['amenities_style'] : 'style1';
        $item_divider = isset($settings['tf_amenities_item_divider']) ? $settings['tf_amenities_item_divider'] : '';
        
        if ($style == 'style1' && !empty($total_facilities_cat) && !empty($meta['hotel-facilities'])) {
            ?>
            <div class="tf-facilities-wrapper tf-single-amenities-style1" id="tf-hotel-facilities">
                <h2 class="tf-section-title"><?php echo esc_html($facilities_title); ?></h2>          
                <div class="tf-facilities">
                    <?php 
                    $facilites_list = [];
                    if( !empty($meta['hotel-facilities']) && is_array($meta['hotel-facilities']) ){
                        foreach( $meta['hotel-facilities'] as $facility ){
                            $facilites_list [$facility['facilities-category']] = $facility['facilities-category'];
                        }
                    }
                    if(!empty($facilites_list)){
                    foreach( $facilites_list as $catkey=> $single_feature ){
                    ?>
                    <div class="tf-facility-item">
                        <?php $f_icon_single  = ! empty( $total_facilities_cat[$catkey]['hotel_facilities_cat_icon'] ) ? esc_attr($total_facilities_cat[$catkey]['hotel_facilities_cat_icon']) : '';?>
                        <span class="single-facilities-title">
                            <?php echo !empty($f_icon_single) ? '<i class="' . esc_attr($f_icon_single) . '"></i>' : ''; ?> <?php echo !empty($total_facilities_cat[$catkey]['hotel_facilities_cat_name']) ? esc_html($total_facilities_cat[$catkey]['hotel_facilities_cat_name']) : ''; ?>
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
        } elseif ($style == 'style2') {
            ?>
            <div class="tf-single-amenities-section tf-car-amenities-section tf-single-amenities-style2">
                
            </div>
			<?php
        }
	}
}
