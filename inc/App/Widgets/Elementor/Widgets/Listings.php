<?php

namespace Tourfic\App\Widgets\Elementor\Widgets;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Hotel\Hotel;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Search Form Horizontal
 */
class Listings extends \Elementor\Widget_Base {

	use \Tourfic\Traits\Singleton;

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'tf-listings';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Tourfic Listings', 'tourfic' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-list-view';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'tourfic' ];
	}

	public function tf_search_types() {
		$types = array(
			'hotel'     => esc_html__( 'Hotel', 'tourfic' ),
			'tour'      => esc_html__( 'Tour', 'tourfic' ),
			'apartment' => esc_html__( 'Apartment', 'tourfic' ),
			'carrentals' => esc_html__( 'Car', 'tourfic' ),
		);

		return $types;
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'tf_search_content_section',
			[
				'label' => esc_html__( 'Content', 'tourfic' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'service',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => esc_html__( 'Service', 'tourfic' ),
				'options'  => [
					'tf_hotel'     => esc_html__( 'Hotel', 'tourfic' ),
					'tf_tours'     => esc_html__( 'Tour', 'tourfic' ),
					'tf_apartment' => esc_html__( 'Apartment', 'tourfic' ),
					'tf_carrental' => esc_html__( 'Car', 'tourfic' ),
				],
				'default'  => 'tf_hotel',
			]
		);
		
		// Design options for Hotel
		$this->add_control(
			'design_hotel',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => esc_html__( 'Design', 'tourfic' ),
				'options'  => [
					'design-1' => esc_html__( 'Design 1', 'tourfic' ),
					'design-2' => esc_html__( 'Design 2', 'tourfic' ),
					'design-3' => esc_html__( 'Design 3', 'tourfic' ),
					'default'  => esc_html__( 'Legacy', 'tourfic' ),
				],
				'default'  => 'design-1',
				'condition' => [
					'service' => 'tf_hotel',
				],
			]
		);
		
		// Design options for Tour
		$this->add_control(
			'design_tour',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => esc_html__( 'Design', 'tourfic' ),
				'options'  => [
					'design-1' => esc_html__( 'Design 1', 'tourfic' ),
					'design-2' => esc_html__( 'Design 2', 'tourfic' ),
					'design-3' => esc_html__( 'Design 3', 'tourfic' ),
					'default'  => esc_html__( 'Legacy', 'tourfic' ),
				],
				'default'  => 'design-1',
				'condition' => [
					'service' => 'tf_tours',
				],
			]
		);
		
		// Design options for Apartment
		$this->add_control(
			'design_apartment',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => esc_html__( 'Design', 'tourfic' ),
				'options'  => [
					'design-1' => esc_html__( 'Design 1', 'tourfic' ),
					'design-2' => esc_html__( 'Design 2', 'tourfic' ),
					'default'  => esc_html__( 'Legacy', 'tourfic' ),
				],
				'default'  => 'design-1',
				'condition' => [
					'service' => 'tf_apartment',
				],
			]
		);
		
		// Design options for Car Rental
		$this->add_control(
			'design_car',
			[
				'type'     => \Elementor\Controls_Manager::SELECT,
				'label'    => esc_html__( 'Design', 'tourfic' ),
				'options'  => [
					'design-1' => esc_html__( 'Design 1', 'tourfic' ),
				],
				'default'  => 'design-1',
				'condition' => [
					'service' => 'tf_carrental',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'tf_search_style_section',
			[
				'label' => esc_html__( 'Style', 'tourfic' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'tourfic' ),
				'selector' => '{{WRAPPER}} .tf_widget-title h2',
			]
		);
		$this->add_control(
			'tf_search_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf-item-card' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tf_subhr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'subtitle_typography',
				'label'    => esc_html__( 'Subtitle Typography', 'tourfic' ),
				'selector' => '{{WRAPPER}} .tf_widget-subtitle',
			]
		);

		$this->add_control(
			'tf_search_subtitle_color',
			[
				'label'     => esc_html__( 'Subtitle Color', 'tourfic' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tf_widget-subtitle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings           = $this->get_settings_for_display();
		$service            = !empty( $settings['service'] ) ? $settings['service'] : 'tf_hotel';
		$design_hotel       = !empty( $settings['design_hotel'] ) ? $settings['design_hotel'] : 'design-1';
		$design_tour        = !empty( $settings['design_tour'] ) ? $settings['design_tour'] : 'design-1';
		$design_apartment   = !empty( $settings['design_apartment'] ) ? $settings['design_apartment'] : 'design-1';
		$design_car   		= !empty( $settings['design_car'] ) ? $settings['design_car'] : 'design-1';
		if($service == 'tf_hotel'){
			$design = $design_hotel;
		} elseif($service == 'tf_tours'){
			$design = $design_tour;
		} elseif($service == 'tf_apartment'){
			$design = $design_apartment;
		} elseif($service == 'tf_carrental'){
			$design = $design_car;
		}

        $query = new \WP_Query( array(
            'post_type' => $service,
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ) );
        ?>
        <div class="tf-archive-listing__one">
            <?php
                do_action( 'tf_before_container' );
                $post_count = $query->post_count;
            ?>
            <!-- Search Head Section -->
            <div class="tf-archive-head tf-flex tf-flex-align-center tf-flex-space-bttn">
                <div class="tf-search-result tf-flex">
                    <span class="tf-counter-title"><?php echo esc_html__( 'Total Results ', 'tourfic' ); ?> </span>
                    <span><?php echo ' ('; ?> </span>
                    <div class="tf-total-results">
                        <span><?php echo esc_html( $post_count ); ?> </span>
                    </div>
                    <span><?php echo ')'; ?> </span>
                </div>
                <?php 
                $tf_defult_views = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_view'] ) ? Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_view'] : 'list';
                ?>
                <div class="tf-search-layout tf-flex tf-flex-gap-12">
                    <div class="tf-icon tf-serach-layout-list tf-list-active tf-grid-list-layout <?php echo $tf_defult_views=="list" ? esc_attr('active') : ''; ?>" data-id="list-view">
                        <div class="defult-view">
                            <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="12" height="2" fill="white"/>
                            <rect x="14" width="2" height="2" fill="white"/>
                            <rect y="5" width="12" height="2" fill="white"/>
                            <rect x="14" y="5" width="2" height="2" fill="white"/>
                            <rect y="10" width="12" height="2" fill="white"/>
                            <rect x="14" y="10" width="2" height="2" fill="white"/>
                            </svg>
                        </div>
                        <div class="active-view">
                            <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="12" height="2" fill="#0E3DD8"/>
                            <rect x="14" width="2" height="2" fill="#0E3DD8"/>
                            <rect y="5" width="12" height="2" fill="#0E3DD8"/>
                            <rect x="14" y="5" width="2" height="2" fill="#0E3DD8"/>
                            <rect y="10" width="12" height="2" fill="#0E3DD8"/>
                            <rect x="14" y="10" width="2" height="2" fill="#0E3DD8"/>
                            </svg>
                        </div>
                    </div>
                    <div class="tf-icon tf-serach-layout-grid tf-grid-list-layout <?php echo $tf_defult_views=="grid" ? esc_attr('active') : ''; ?>" data-id="grid-view">
                        <div class="defult-view">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="10" width="2" height="2" fill="#0E3DD8"/>
                            <rect x="10" y="5" width="2" height="2" fill="#0E3DD8"/>
                            <rect x="10" y="10" width="2" height="2" fill="#0E3DD8"/>
                            <rect x="5" width="2" height="2" fill="#0E3DD8"/>
                            <rect x="5" y="5" width="2" height="2" fill="#0E3DD8"/>
                            <rect x="5" y="10" width="2" height="2" fill="#0E3DD8"/>
                            <rect width="2" height="2" fill="#0E3DD8"/>
                            <rect y="5" width="2" height="2" fill="#0E3DD8"/>
                            <rect y="10" width="2" height="2" fill="#0E3DD8"/>
                            </svg>
                        </div>
                        <div class="active-view">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="10" width="2" height="2" fill="white"/>
                            <rect x="10" y="5" width="2" height="2" fill="white"/>
                            <rect x="10" y="10" width="2" height="2" fill="white"/>
                            <rect x="5" width="2" height="2" fill="white"/>
                            <rect x="5" y="5" width="2" height="2" fill="white"/>
                            <rect x="5" y="10" width="2" height="2" fill="white"/>
                            <rect width="2" height="2" fill="white"/>
                            <rect y="5" width="2" height="2" fill="white"/>
                            <rect y="10" width="2" height="2" fill="white"/>
                            </svg>
                        </div>
                    </div>
                    <div class="tf-sorting-selection-warper">
                        <form class="tf-archive-ordering" method="get">
                            <select class="tf-orderby" name="tf-orderby" id="tf-orderby">
                                <option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
                                <option value="enquiry"><?php echo esc_html__( 'Sort By Recommended', 'tourfic' ); ?></option>
                                <option value="order"><?php echo esc_html__( 'Sort By Popularity', 'tourfic' ); ?></option>
                                <option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
                                <option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
                                <option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
                                <option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Loader Image -->
            <div id="tf_ajax_searchresult_loader">
                <div id="tf-searchresult-loader-img">
                    <img src="<?php echo esc_url(TF_ASSETS_APP_URL) ?>images/loader.gif" alt="">
                </div>
            </div>
            <div class="tf-search-results-list tf-mt-30">
                <div class="archive_ajax_result tf-item-cards tf-flex <?php echo $tf_defult_views=="list" ? esc_attr('tf-layout-list') : esc_attr('tf-layout-grid'); ?>">

                <?php
                if ( $query->have_posts() ) {
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
                        if ( !empty( $hotel_meta[ "featured" ] ) && $hotel_meta[ "featured" ] == 1 ) {
                            Hotel::tf_hotel_archive_single_item();
                        }
                    }
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $hotel_meta = get_post_meta( get_the_ID() , 'tf_hotels_opt', true );
                        if ( empty($hotel_meta[ "featured" ]) ) {
                            Hotel::tf_hotel_archive_single_item();
                        }
                    }
                } else {
                    echo '<div class="tf-nothing-found" data-post-count="0" >' .esc_html__("No Tours Found!", "tourfic"). '</div>';
                }
                ?>
                    <div class="tf-pagination-bar">
                        <?php Helper::tourfic_posts_navigation(); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
	}


}
