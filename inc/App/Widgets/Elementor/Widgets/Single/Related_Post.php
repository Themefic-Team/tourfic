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
use \Tourfic\Classes\Tour\Tour_Price;
use \Tourfic\Classes\Apartment\Pricing as Apt_Pricing;
use Tourfic\App\TF_Review;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Related Post
 */
class Related_Post extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-related-post';
	}

	public function get_title() {
		return esc_html__( 'Related Post', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-check';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'related post',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-related-post'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-related-post/before-style-controls', $this );
		$this->tf_related_post_style_controls();
		do_action( 'tf/single-related-post/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_related_post_content',[
            'label' => esc_html__('Related Post', 'tourfic'),
        ]);

        do_action( 'tf/single-related-post/before-content/controls', $this );

        $post_type = $this->get_current_post_type();
		$options = [
			'style1' => esc_html__('Style 1', 'tourfic'),
            'style2' => esc_html__('Style 2', 'tourfic')
		];
		if($post_type == 'tf_tours'){
			$options['style3'] = esc_html__('Style 3', 'tourfic');
		}
		$this->add_control('related_post_style',[
            'label' => esc_html__('Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => $options,
        ]);

	    do_action( 'tf/single-related-post/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_related_post_style_controls() {
		$this->start_controls_section( 'related_post_style_section', [
			'label' => esc_html__( 'Style', 'tourfic' ),
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

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $this->post_id   = get_the_ID();
        $this->post_type = get_post_type();

        if($this->post_type == 'tf_tours'){
            $meta = get_post_meta( $this->post_id, 'tf_tours_opt', true );
	        $disable_related_tour = ! empty( $meta['t-related'] ) ? $meta['t-related'] : '';
            $s_related = ! empty( Helper::tfopt( 't-related' ) ) ?Helper::tfopt( 't-related' ) : '';
            $disable_related_tour = ! empty( $disable_related_tour ) ? $disable_related_tour : $s_related;

            $destinations           = get_the_terms( $this->post_id, 'tour_destination' );
            $first_destination_slug = ! empty( $destinations ) ? $destinations[0]->slug : '';
        }elseif($this->post_type == 'tf_apartment'){
            $meta = get_post_meta( $this->post_id, 'tf_apartment_opt', true );
            $disable_related_sec = ! empty( $meta['disable-related-apartment'] ) ? $meta['disable-related-apartment'] : '';
            $s_related = ! empty( Helper::tfopt( 'disable-related-apartment' ) ) ? Helper::tfopt( 'disable-related-apartment' ) : 0;
            $disable_related_sec = ! empty( $disable_related_sec ) ? $disable_related_sec : $s_related;

            $locations = ! empty( get_the_terms( $this->post_id, 'apartment_location' ) ) ? get_the_terms( $this->post_id, 'apartment_location' ) : array();
        } else {
            return;
        }
        $style = !empty($settings['related_post_style']) ? $settings['related_post_style'] : 'style1';
       
        if($this->post_type == 'tf_tours' && $style == 'style1' && ! $disable_related_tour == '1'){ ?>
            <div class="tf-single-template__one tf-single-tour-related-post-style1">
                <?php
                $related_tour_type = Helper::tfopt( 'rt_display' );
                $args              = array(
                    'post_type'      => 'tf_tours',
                    'post_status'    => 'publish',
                    'posts_per_page' => 8,
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                    'tax_query'      => array( // WPCS: slow query ok.
                        array(
                            'taxonomy' => 'tour_destination',
                            'field'    => 'slug',
                            'terms'    => $first_destination_slug,
                        ),
                    ),
                );

                //show related tour based on selected tours
                $selected_ids = !empty(Helper::tfopt( 'tf-related-tours' )) ? Helper::tfopt( 'tf-related-tours' ) : array();

                if ( $related_tour_type == 'selected') {
                    if(in_array($this->post_id, $selected_ids)) {
                        $index = array_search($this->post_id, $selected_ids);

                        $current_post_id = array($selected_ids[$index]);

                        unset($selected_ids[$index]);
                    } else {
                        $current_post_id = array($this->post_id);
                    }

                    if(count($selected_ids) > 0) {
                        $args['post__in'] = $selected_ids;
                    } else {
                        $args['post__in'] = array(-1);
                    }
                } else {
                    $current_post_id = array($this->post_id);
                }

                $tours = new \WP_Query( $args );

                $all_tour_ids = array_filter( wp_list_pluck( $tours->posts, 'ID' ), function($id) use ($current_post_id) {
                    return $id != $current_post_id[0];
                });

                if ( $tours->have_posts() ) {
                    ?>
                    <div class="upcomming-tours">
                        <div class="section-title">
                            <h2 class="tf-title"><?php echo ! empty( Helper::tfopt( 'rt-title' ) ) ? esc_html( Helper::tfopt( 'rt-title' )) : ''; ?></h2>
                            <?php
                            if ( ! empty( Helper::tfopt( 'rt-description' ) ) ) { ?>
                                <p><?php echo wp_kses_post(Helper::tfopt( 'rt-description')) ?></p>
                            <?php } ?>
                        </div>
                        <div class="tf-slider-items-wrapper tf-slick-slider tf-upcomming-tours-list-outter tf-mt-40 tf-flex tf-flex-gap-24">
                            <?php
                            while ( $tours->have_posts() ) {
                                $tours->the_post();
                                if( is_array($all_tour_ids) && in_array(get_the_ID(), $all_tour_ids) ):
                                    $selected_design_post_id = get_the_ID();
                                    $destinations           = get_the_terms( $selected_design_post_id, 'tour_destination' );

                                    $first_destination_name = $destinations[0]->name;
                                    $related_comments       = get_comments( array( 'post_id' => $selected_design_post_id ) );
                                    $meta                   = get_post_meta( $selected_design_post_id, 'tf_tours_opt', true );
                                    $pricing_rule           = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
                                    $disable_adult          = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
                                    $disable_child          = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
                                    $tour_price             = new Tour_Price( $meta );
                                    ?>
                                    <div class="tf-slider-item tf-post-box-lists">
                                        <div class="tf-post-single-box">
                                            <div class="tf-image-data">
                                                <img src="<?php echo ! empty( get_the_post_thumbnail_url( $selected_design_post_id, 'full' ) ) ? esc_url(get_the_post_thumbnail_url( $selected_design_post_id, 'full' )) : esc_url(TF_ASSETS_APP_URL . 'images/feature-default.jpg'); ?>"
                                                    alt="">
                                                <div class="tf-meta-data-price">
                                                    <?php esc_html_e( "From", "tourfic" ); ?>
                                                    <span>
                                        <?php if ( $pricing_rule == 'group' ) {
                                            echo !empty( $tour_price->wc_sale_group ) ? wp_kses_post($tour_price->wc_sale_group) : wp_kses_post($tour_price->wc_group);
                                        } else if ( $pricing_rule == 'person' ) {
                                            if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                                                echo !empty($tour_price->wc_sale_adult) ? wp_kses_post($tour_price->wc_sale_adult) : wp_kses_post($tour_price->wc_adult);
                                            } else if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                                                echo !empty( $tour_price->wc_sale_child ) ? wp_kses_post($tour_price->wc_sale_child) : wp_kses_post($tour_price->wc_child);

                                            }
                                        } else if ( $pricing_rule == 'package' ) {
                                            if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                                                echo !empty($tour_price->wc_sale_adult) ? wp_kses_post($tour_price->wc_sale_adult) : wp_kses_post($tour_price->wc_adult);
                                            } else if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                                                echo !empty( $tour_price->wc_sale_child ) ? wp_kses_post($tour_price->wc_sale_child) : wp_kses_post($tour_price->wc_child);

                                            }
                                        }
                                        ?>
                                        </span>
                                                </div>
                                            </div>
                                            <div class="tf-meta-info tf-mt-30">
                                                <div class="tf-meta-location">
                                                    <i class="fa-solid fa-location-dot"></i> <?php echo esc_html($first_destination_name); ?>
                                                </div>
                                                <div class="tf-meta-title">
                                                    <h2><a href="<?php the_permalink($selected_design_post_id) ?>"><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( html_entity_decode(get_the_title( $selected_design_post_id )), 35 ) ); ?></a></h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php }
                wp_reset_postdata();
                ?>
            </div>
			<?php
        } elseif($this->post_type == 'tf_tours' && $style == 'style2' && ! $disable_related_tour == '1'){
            ?>
            <div class="tf-single-template__two tf-single-tour-related-post-style2">
                <?php
                $related_tour_type = Helper::tfopt( 'rt_display' );
                $args              = array(
                    'post_type'      => 'tf_tours',
                    'post_status'    => 'publish',
                    'posts_per_page' => 8,
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'tour_destination',
                            'field'    => 'slug',
                            'terms'    => $first_destination_slug,
                        ),
                    ),
                );
                //show related tour based on selected tours
                $selected_ids = ! empty( Helper::tfopt( 'tf-related-tours' ) ) ? Helper::tfopt( 'tf-related-tours' ) : array();

                if ( $related_tour_type == 'selected' && defined( 'TF_PRO' ) ) {
                    if ( in_array( $this->post_id, $selected_ids ) ) {
                        $index = array_search( $this->post_id, $selected_ids );

                        $current_post_id = array( $selected_ids[ $index ] );

                        unset( $selected_ids[ $index ] );
                    } else {
                        $current_post_id = array( $this->post_id );
                    }

                    if ( count( $selected_ids ) > 0 ) {
                        $args['post__in'] = $selected_ids;
                    } else {
                        $args['post__in'] = array( - 1 );
                    }
                } else {
                    $current_post_id = array( $this->post_id );
                }

                $tours = new \WP_Query( $args );

                $all_tour_ids = array_filter( wp_list_pluck( $tours->posts, 'ID' ), function($id) use ($current_post_id) {
                    return $id != $current_post_id[0];
                });

                if ( $tours->have_posts() ) {
                    ?>
                    <div class="tf-related-items-section">
                        <div class="section-title">
                            <h2 class="tf-title"><?php echo ! empty( Helper::tfopt( 'rt-title' ) ) ? esc_html( Helper::tfopt( 'rt-title' ), "tourfic" ) : esc_html_e( "You may also like", "tourfic" ); ?></h2>
                        </div>
                        <div class="tf-design-2-slider-items-wrapper tf-slick-slider tf-upcomming-tours-list-outter tf-flex tf-flex-gap-24">
                            <?php
                            while ( $tours->have_posts() ) {
                                $tours->the_post();

                                if( is_array( $all_tour_ids ) && in_array(get_the_ID(), $all_tour_ids) ):

                                    $selected_design_post_id = get_the_ID();
                                    $destinations            = get_the_terms( $selected_design_post_id, 'tour_destination' );
                                    $first_destination_name  = $destinations[0]->name;
                                    $related_comments        = get_comments( array( 'post_id' => $selected_design_post_id ) );
                                    $meta                    = get_post_meta( $selected_design_post_id, 'tf_tours_opt', true );
                                    $pricing_rule            = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
                                    $disable_adult           = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
                                    $disable_child           = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
                                    $tour_price              = new Tour_Price( $meta );
                                    ?>
                                    <div class="tf-slider-item tf-post-box-lists">
                                        <div class="tf-post-single-box">
                                            <div class="tf-image-data">
                                                <img src="<?php echo ! empty( get_the_post_thumbnail_url( $selected_design_post_id, 'full' ) ) ? esc_url( get_the_post_thumbnail_url( $selected_design_post_id, 'full' ) ) : esc_url( TF_ASSETS_APP_URL . 'images/feature-default.jpg' ); ?>"
                                                    alt="">
                                            </div>
                                            <div class="tf-meta-info">
                                                <div class="meta-content">
                                                    <div class="tf-meta-title">
                                                        <h2><a href="<?php the_permalink( $selected_design_post_id ) ?>">
                                                                <?php echo wp_kses_post( Helper::tourfic_character_limit_callback( html_entity_decode(get_the_title( $selected_design_post_id )), 35 ) ); ?>
                                                            </a></h2>
                                                        <div class="tf-meta-data-price">
                                                            <span>
                                                            <?php if ( $pricing_rule == 'group' ) {
                                                                echo !empty( $tour_price->wc_sale_group ) ? wp_kses_post($tour_price->wc_sale_group) : wp_kses_post($tour_price->wc_group);
                                                            } else if ( $pricing_rule == 'person' ) {
                                                                if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                                                                    echo !empty($tour_price->wc_sale_adult) ? wp_kses_post($tour_price->wc_sale_adult) : wp_kses_post($tour_price->wc_adult);
                                                                } else if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                                                                    echo !empty( $tour_price->wc_sale_child ) ? wp_kses_post($tour_price->wc_sale_child) : wp_kses_post($tour_price->wc_child);
                                                                }
                                                            } else if ( $pricing_rule == 'package' ) {
                                                                if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                                                                    echo !empty($tour_price->wc_sale_adult) ? wp_kses_post($tour_price->wc_sale_adult) : wp_kses_post($tour_price->wc_adult);
                                                                } else if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                                                                    echo !empty( $tour_price->wc_sale_child ) ? wp_kses_post($tour_price->wc_sale_child) : wp_kses_post($tour_price->wc_child);
                                                                }
                                                            }
                                                            ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="tf-meta-location">
                                                        <i class="fa-solid fa-location-dot"></i> <?php echo esc_html( $first_destination_name ); ?>
                                                    </div>
                                                </div>
                                                <a class="see-details" href="<?php the_permalink( $selected_design_post_id ) ?>">
                                                    <?php esc_html_e( "See details", "tourfic" ); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                            <?php endif; ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php }
                wp_reset_postdata();
                ?>
            </div>
			<?php
        } elseif($this->post_type == 'tf_tours' && $style == 'style3' && ! $disable_related_tour == '1'){
            ?>
            <div class="tf-single-template__legacy tf-single-tour-related-post-style3">
                <?php
                $related_tour_type = Helper::tfopt('rt_display');
                $args  = array(
                    'post_type'      => 'tf_tours',
                    'post_status'    => 'publish',
                    'posts_per_page' => 8,
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'tour_destination',
                            'field'    => 'slug',
                            'terms'    => $first_destination_slug,
                        ),
                    ),
                );
                //show related tour based on selected tours
                $selected_ids = !empty(Helper::tfopt( 'tf-related-tours' )) ? Helper::tfopt( 'tf-related-tours' ) : array();

                if ( $related_tour_type == 'selected') {
                    if(in_array($this->post_id, $selected_ids)) {
                        $index = array_search($this->post_id, $selected_ids);

                        $current_post_id = array($selected_ids[$index]);

                        unset($selected_ids[$index]);
                    } else{
                        $current_post_id = array($this->post_id);
                    }

                    if(count($selected_ids) > 0) {
                        $args['post__in'] = $selected_ids;
                    } else {
                        $args['post__in'] = array(-1);
                    }
                } else {
                    $current_post_id = array($this->post_id);
                }

                $tours = new \WP_Query( $args );

                $all_tour_ids = array_filter( wp_list_pluck( $tours->posts, 'ID' ), function($id) use ($current_post_id) {
                    return $id != $current_post_id[0];
                });

                if ( $tours->have_posts() ) {
                    ?>
                    <div class="tf-suggestion-wrapper">
                        <div class="tf-slider-content-wrapper">
                            <div class="tf-suggestion-sec-head">
                                <?php 
                                if( !empty( Helper::tfopt('rt-title') ) ){ ?>
                                    <h2 class="section-heading"><?php echo esc_html( Helper::tfopt('rt-title') ) ?></h2>
                                <?php } ?>
                                <?php 
                                if( !empty( Helper::tfopt('rt-description') ) ){ ?>
                                    <p><?php echo wp_kses_post( Helper::tfopt('rt-description') ) ?></p>
                                <?php } ?>
                            </div>

                            <div class="tf-slider-items-wrapper tf-slick-slider">
                                <?php
                                while ( $tours->have_posts() ) {
                                    $tours->the_post();

                                    if( is_array( $all_tour_ids ) && in_array(get_the_ID(), $all_tour_ids) ):

                                        $selected_post_id       = get_the_ID();
                                        $destinations           = get_the_terms( $selected_post_id, 'tour_destination' );
                                        $first_destination_name = $destinations[0]->name;
                                        $related_comments       = get_comments( array( 'post_id' => $selected_post_id ) );
                                        $meta                   = get_post_meta( $selected_post_id, 'tf_tours_opt', true );
                                        $pricing_rule           = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
                                        $disable_adult          = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
                                        $disable_child          = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
                                        $tour_price             = new Tour_Price( $meta );
                                        ?>
                                        <div class="tf-slider-item" style="background-image: url(<?php echo esc_url(get_the_post_thumbnail_url( $selected_post_id, 'full' )); ?>);">
                                            <div class="tf-slider-content">
                                                <div class="tf-slider-desc">
                                                    <h3>
                                                        <a href="<?php the_permalink($selected_post_id) ?>"><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( html_entity_decode(get_the_title( $selected_post_id )), 35 ) ); ?></a>
                                                        <span><?php echo esc_html($first_destination_name); ?></span>
                                                    </h3>
                                                </div>
                                                <div class="tf-suggestion-rating">
                                                    <div class="tf-suggestion-price">
                                                        <span>
                                                        <?php if ( $pricing_rule == 'group' ) {
                                                            echo !empty( $tour_price->wc_sale_group ) ? wp_kses_post($tour_price->wc_sale_group) : wp_kses_post($tour_price->wc_group);
                                                        } else if ( $pricing_rule == 'person' ) {
                                                            if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                                                                echo !empty($tour_price->wc_sale_adult) ? wp_kses_post($tour_price->wc_sale_adult) : wp_kses_post($tour_price->wc_adult);
                                                            } else if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                                                                echo !empty( $tour_price->wc_sale_child ) ? wp_kses_post($tour_price->wc_sale_child) : wp_kses_post($tour_price->wc_child);

                                                            }
                                                        } else if ( $pricing_rule == 'package' ) {
                                                            if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                                                                echo !empty($tour_price->wc_sale_adult) ? wp_kses_post($tour_price->wc_sale_adult) : wp_kses_post($tour_price->wc_adult);
                                                            } else if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                                                                echo !empty( $tour_price->wc_sale_child ) ? wp_kses_post($tour_price->wc_sale_child) : wp_kses_post($tour_price->wc_child);

                                                            }
                                                        }
                                                        ?>
                                                        </span>
                                                    </div>
                                                    <?php
                                                    if ( $related_comments ) {
                                                        ?>
                                                        <div class="tf-slider-rating-star">
                                                            <i class="fas fa-star"></i> <span style="color:#fff;"><?php echo wp_kses_post( TF_Review::tf_total_avg_rating( $related_comments )); ?></span>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php }
                wp_reset_postdata();
                ?>
            </div>
			<?php 
        } elseif($this->post_type == 'tf_apartment' && $style == 'style1' && $disable_related_sec !== '1'){
            ?>
            <div class="tf-single-template__two tf-single-apartment-related-post-style1">
                <?php
                if ( $disable_related_sec !== '1' ) {
                    $args              = array(
                        'post_type'      => 'tf_apartment',
                        'post_status'    => 'publish',
                        'posts_per_page' => 8,
                        'orderby'        => 'title',
                        'order'          => 'ASC',
                        'tax_query'      => array( // WPCS: slow query ok.
                            array(
                                'taxonomy' => 'apartment_location',
                                'field'    => 'term_id',
                                'terms'    => wp_list_pluck( $locations, 'term_id' ),
                            ),
                        ),
                    );
                    $related_apartment = new \WP_Query( $args );
                    if ( $related_apartment->have_posts() ) { ?>
                        <div class="tf-related-items-section">
                            <div class="section-title">
                                <h2 class="tf-title"><?php echo ! empty( $meta['related_apartment_title'] ) ? esc_html( $meta['related_apartment_title'] ) : ''; ?></h2>
                            </div>
                            <div class="tf-design-3-slider-items-wrapper tf-slick-slider tf-upcomming-tours-list-outter tf-flex tf-flex-gap-24">
                                <?php
                                while ( $related_apartment->have_posts() ) {
                                    $related_apartment->the_post();

                                    $selected_design_post_id = get_the_ID();
                                    $destinations           = get_the_terms( $selected_design_post_id, 'apartment_location' );
                                    $first_destination_name = $destinations[0]->name;
                                    $meta                   = get_post_meta( $selected_design_post_id, 'tf_apartment_opt', true );
                                    $apartment_min_price = Apt_Pricing::instance( $selected_design_post_id )->get_min_max_price();

                                    $pricing_type = ! empty( $meta['pricing_type'] ) && "per_person" == $meta['pricing_type'] ? esc_html__("Person", "tourfic") : esc_html__("Night", "tourfic");
                                    if(!in_array($selected_design_post_id, array($this->post_id))){
                                    ?>
                                        <div class="tf-slider-item tf-post-box-lists">
                                            <div class="tf-post-single-box">
                                                <div class="tf-image-data">
                                                    <img src="<?php echo ! empty( get_the_post_thumbnail_url( $selected_design_post_id, 'full' ) ) ? esc_url( get_the_post_thumbnail_url( $selected_design_post_id, 'full' )  ): esc_url(TF_ASSETS_APP_URL . 'images/feature-default.jpg'); ?>" alt="">
                                                </div>
                                                <div class="tf-meta-info">
                                                    <div class="meta-content">
                                                        <div class="tf-meta-title">
                                                            <h2><a href="<?php echo esc_url( get_permalink($selected_design_post_id) ) ?>">
                                                            <?php echo esc_html( Helper::tourfic_character_limit_callback(get_the_title($selected_design_post_id), 35) ); ?>
                                                            </a></h2>
                                                            <div class="tf-meta-data-price">
                                                                <span><?php echo !empty($apartment_min_price["min"]) ? wp_kses_post(wc_price($apartment_min_price["min"])) : wp_kses_post(wc_price(0));
                                                                ?></span><span class="pricing_calc_type">/<?php echo esc_html( $pricing_type ); ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="tf-meta-location">
                                                            <i class="fa-solid fa-location-dot"></i> <?php echo esc_html( $first_destination_name ); ?>
                                                        </div>
                                                    </div>
                                                    <a class="see-details" href="<?php echo esc_url( get_permalink($selected_design_post_id) ) ?>">
                                                        <?php esc_html_e("See details", "tourfic"); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php }
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
			<?php
        } elseif($this->post_type == 'tf_apartment' && $style == 'style2' && $disable_related_sec !== '1'){
            ?>
            <div class="tf-single-template__legacy tf-single-apartment-related-post-style2">
                <?php
                $args              = array(
                    'post_type'      => 'tf_apartment',
                    'post_status'    => 'publish',
                    'posts_per_page' => 8,
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                    'tax_query'      => array( // WPCS: slow query ok.
                        array(
                            'taxonomy' => 'apartment_location',
                            'field'    => 'term_id',
                            'terms'    => wp_list_pluck( $locations, 'term_id' ),
                        ),
                    ),
                );
                $related_args = array_merge( $args, array( 'post__not_in' => array( $this->post_id ) ) );
                $related_apartment = new \WP_Query( $args );
                $related_apartment_check = new \WP_Query( $related_args );

                if ( $related_apartment_check->have_posts() ) : ?>
                    <div class="tf-related-apartment">
                        <h2 class="section-heading"><?php echo ! empty( $meta['related_apartment_title'] ) ? esc_html( $meta['related_apartment_title'] ) : ''; ?></h2>
                        <div class="tf-related-apartment-slider tf-slick-slider">
                            <?php while ( $related_apartment->have_posts() ) : $related_apartment->the_post();
                                if ( ! in_array( get_the_ID(), array( $this->post_id ) ) ):
                                    ?>
                                    <div class="tf-apartment-item">
                                        <div class="tf-apartment-item-thumb">
                                            <?php if ( has_post_thumbnail() ) { ?>
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php the_post_thumbnail( 'tourfic-370x250' ); ?>
                                                </a>
                                            <?php } else { ?>
                                                <a href="<?php the_permalink(); ?>">
                                                    <img src="<?php echo esc_url( TF_ASSETS_APP_URL ) . "images/feature-default.jpg"; ?>"/>
                                                </a>
                                            <?php } ?>
                                        </div>
                                        <div class="tf-related-apartment-content">
                                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                            <span><?php echo get_the_date( 'F j, Y' ); ?></span>
                                        </div>
                                    </div>
                                <?php
                                endif;
                            endwhile;
                            wp_reset_query(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
			<?php
        }
        
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ): ?>
        <script>
            jQuery(document).ready(function ($) {
                'use strict';

                jQuery('.tf-design-2-slider-items-wrapper, .tf-design-3-slider-items-wrapper').slick({
                    dots: false,
                    arrows: true,
                    infinite: true,
                    speed: 300,
                    autoplaySpeed: 2000,
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1,
                                infinite: true,
                                dots: false
                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
            
                jQuery('.tf-slider-items-wrapper,.tf-slider-activated').slick({
                    dots: true,
                    arrows: false,
                    infinite: true,
                    speed: 300,
                    //autoplay: true,
                    autoplaySpeed: 2000,
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 1,
                                infinite: true,
                                dots: true
                            }
                        },
                        {
                            breakpoint: 767,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });

                jQuery('.tf-related-apartment-slider').slick({
                    dots: true,
                    arrows: false,
                    infinite: true,
                    speed: 300,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 1,
                                infinite: true,
                                dots: true
                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
            });	
        </script>
        <?php endif;
    }
}
