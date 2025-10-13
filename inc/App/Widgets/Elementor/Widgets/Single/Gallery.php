<?php

namespace Tourfic\App\Widgets\Elementor\Widgets\Single;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Tourfic\App\TF_Review;
use Tourfic\Classes\Helper;
use \Tourfic\Classes\Car_Rental\Pricing;

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Gallery
 */
class Gallery extends Widget_Base {

	use \Tourfic\Traits\Singleton;

	public function get_name() {
		return 'tf-single-gallery';
	}

	public function get_title() {
		return esc_html__( 'Gallery', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'gallery',
			'tourfic',
			'media',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-gallery'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-gallery/before-style-controls', $this );
		$this->tf_gallery_style_controls();
		do_action( 'tf/single-gallery/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_gallery_content',[
            'label' => esc_html__('Gallery', 'tourfic'),
        ]);

        do_action( 'tf/single-gallery/before-content/controls', $this );

        $this->add_control('gallery_style',[
            'label' => esc_html__('Gallery Style', 'tourfic'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'style1',
            'options' => [
                'style1' => esc_html__('Style 1 - Bottom Nav', 'tourfic'),
                'style2' => esc_html__('Style 2 - Slider', 'tourfic'),
                'style3' => esc_html__('Style 3 - Grid', 'tourfic'),
            ],
        ]);

		$this->add_control('show_review', [
			'label' => esc_html__('Review Badge', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
            'condition' => [
				'gallery_style' => ['style1', 'style2'],
			],
		]);

	    do_action( 'tf/single-gallery/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_gallery_style_controls() {
		$this->start_controls_section( 'gallery_style_section', [
			'label' => esc_html__( 'Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

        $this->add_responsive_control( "tf_nav_item_gap", [
			'label'      => esc_html__( 'Nav Items Gap', 'tourfic' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
			],
			'range'      => [
				'px' => [
					'min'  => 5,
					'max'  => 50,
					'step' => 1,
				],
			],
			'selectors'  => [
				"{{WRAPPER}} .tf-single-gallery__style-1.tf-hero-gallery .tf-gallery" => 'gap: {{SIZE}}{{UNIT}};',
			],
            'condition' => [
				'gallery_style' => ['style1'],
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
        $settings  = $this->get_settings_for_display();
        $post_id   = get_the_ID();
        $post_type = get_post_type();

		/**
		 * Review query
		 */
		$args           = array(
			'post_id' => $post_id,
			'status'  => 'approve',
			'type'    => 'comment',
		);
		$comments_query = new \WP_Comment_Query( $args );
		$comments       = $comments_query->comments;

		if($post_type == 'tf_hotel'){
            $meta = get_post_meta($post_id, 'tf_hotels_opt', true);
			$s_review = ! empty( Helper::tfopt( 'h-review' ) ) ? Helper::tfopt( 'h-review' ) : 0;
			$disable_review_sec   = ! empty( $meta['h-review'] ) ? $meta['h-review'] : '';
			$gallery = ! empty( $meta['gallery'] ) ? $meta['gallery'] : '';
			if ( $gallery ) {
				$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
			}

        } elseif($post_type == 'tf_tours'){
			$meta = get_post_meta($post_id, 'tf_tours_opt', true);
			$s_review  = ! empty( Helper::tfopt( 't-review' ) ) ? Helper::tfopt( 't-review' ) : '';
			$disable_review_sec   = ! empty( $meta['t-review'] ) ? $meta['t-review'] : '';
			$gallery = ! empty( $meta['tour_gallery'] ) ? $meta['tour_gallery'] : array();
			if ( $gallery ) {
				$gallery_ids = explode( ',', $gallery );
			}
			
        } elseif($post_type == 'tf_apartment'){
			$meta = get_post_meta($post_id, 'tf_apartment_opt', true);
			$s_review  = ! empty( Helper::tfopt( 'disable-apartment-review' ) ) ? Helper::tfopt( 'disable-apartment-review' ) : 0;
			$disable_review_sec  = ! empty( $meta['disable-apartment-review'] ) ? $meta['disable-apartment-review'] : '';
			$gallery = ! empty( $meta['apartment_gallery'] ) ? $meta['apartment_gallery'] : '';
			if ( $gallery ) {
				$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
			}
			
        } elseif($post_type == 'tf_carrental'){
			$meta = get_post_meta($post_id, 'tf_carrental_opt', true);
			$disable_share_opt    = ! empty( $meta['c-share'] ) ? $meta['c-share'] : '';
			$disable_wishlist_sec = ! empty( $meta['c-wishlist'] ) ? $meta['c-wishlist'] : 0;
			$s_review  = ! empty( Helper::tfopt( 'disable-apartment-review' ) ) ? Helper::tfopt( 'disable-apartment-review' ) : 0;
			$disable_review_sec  = ! empty( $meta['disable-apartment-review'] ) ? $meta['disable-apartment-review'] : '';
			$gallery = ! empty( $meta['car_gallery'] ) ? $meta['car_gallery'] : '';
			if ( $gallery ) {
				$gallery_ids = explode( ',', $gallery ); // Comma seperated list to array
			}
			
        } else {
			return;
		}
		$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;

        //gallery style
        $style = !empty($settings['gallery_style']) ? $settings['gallery_style'] : 'style1';
        $show_review = isset($settings['show_review']) ? $settings['show_review'] : '';

        // Style 1: Bottom Nav
        if ($style == 'style1' && $post_type !== 'tf_carrental') {
            ?>
            <div class="tf-single-gallery__style-1 tf-hero-gallery">
				<div class="tf-gallery-featured <?php echo empty($gallery_ids) ? esc_attr('tf-without-gallery-featured') : ''; ?>">
					<img src="<?php echo !empty(wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' )) ? esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) : esc_url(TF_ASSETS_APP_URL.'images/feature-default.jpg'); ?>" alt="<?php esc_html_e( 'Hotel Image', 'tourfic' ); ?>">

					<?php if ( $show_review == 'yes' && ! $disable_review_sec == '1' ) : ?>
					<div class="tf-single-review-box">
						<?php if($comments): ?>
							<a href="#tf-review" class="tf-single-rating">
								<span><?php echo wp_kses_post( TF_Review::tf_total_avg_rating( $comments )); ?></span> (<?php TF_Review::tf_based_on_text( count( $comments ) ); ?>)
							</a>
						<?php else: ?>
							<a href="#tf-review" class="tf-single-rating">
								<span><?php esc_html_e( "0.0", "tourfic" ) ?></span> (<?php esc_html_e( "0 review", "tourfic" ) ?>)
							</a>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>

				<div class="tf-gallery">
					<?php 
					$gallery_count = 1;
					if ( ! empty( $gallery_ids ) ) :
						foreach ( $gallery_ids as $key => $gallery_item_id ) :
						$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
						?>
						<a class="<?php echo $gallery_count==5 ? esc_attr( 'tf-gallery-more' ) : ''; ?>" id="tour-gallery" href="<?php echo esc_url($image_url); ?>" data-fancybox="tour-gallery">
							<img src="<?php echo esc_url($image_url); ?>">
						</a>
						<?php $gallery_count++; 
						endforeach;
					endif; ?>
				</div>
			</div>
            <?php
        }
        elseif ($style == 'style1' && $post_type == 'tf_carrental') {
            ?>
			<div class="tf-single-template__one tf-single-car-gallery-style-1">
				<div class="tf-car-hero-gallery">
					<div class="tf-featured-car">
						<img src="<?php echo !empty(wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' )) ? esc_url( wp_get_attachment_url( get_post_thumbnail_id(), 'tf_gallery_thumb' ) ) : esc_url(TF_ASSETS_APP_URL.'images/feature-default.jpg'); ?>" alt="<?php esc_html_e( 'Car Image', 'tourfic' ); ?>">

						<div class="tf-featured-reviews">
							<a href="#tf-reviews" class="tf-single-rating">
								<span>
									<?php 
									if($comments){
									echo wp_kses_post( TF_Review::tf_total_avg_rating( $comments )); 
									}else{ 
									?>
									0.0
									<?php } ?>
									<i class="fa-solid fa-star"></i>
								</span> (<?php echo wp_kses_post( Pricing::get_total_trips($post_id) ); ?> <?php esc_html_e( "trips", "tourfic" ) ?>)
							</a>
						</div>
					</div>

					<div class="tf-gallery tf-flex tf-flex-gap-16">
					<?php 
					$gallery_count = 1;
						if ( ! empty( $gallery_ids ) ) {
						foreach ( $gallery_ids as $key => $gallery_item_id ) {
						$image_url = wp_get_attachment_url( $gallery_item_id, 'full' );
					?>
						<a class="<?php echo $gallery_count==4 ? esc_attr( 'tf-gallery-more' ) : ''; ?> " href="<?php echo esc_url($image_url); ?>" id="tour-gallery" data-fancybox="tour-gallery">
							<img src="<?php echo esc_url($image_url); ?>">
						</a>
					<?php $gallery_count++; } } ?>
					</div>
				</div>
			</div>
            <?php
        }
        // Style 2: Slider
        elseif ($style == 'style2') {
            ?>
            <div class="tf-single-gallery__style-2 tf-hero-gallery">
				<?php if ( $show_review == 'yes' && $comments && ! $disable_review_sec == '1' ) { ?>
					<div class="tf-top-review">
						<a href="#tf-review">
							<div class="tf-single-rating">
								<i class="fas fa-star"></i> <span><?php echo wp_kses_post( TF_Review::tf_total_avg_rating( $comments ) ); ?></span> (<?php TF_Review::tf_based_on_text( count( $comments ) ); ?>)
							</div>
						</a>
					</div>
				<?php } ?>
				
				<?php if ( ! empty( $gallery_ids ) ) { ?>
					<div class="tf-gallery-wrap">
						<div class="list-single-main-media fl-wrap" id="sec1">
							<div class="single-slider-wrapper fl-wrap">
								<div class="tf_slider-for fl-wrap tf-slick-slider">
									<?php foreach ( $gallery_ids as $attachment_id ) {
										echo '<div class="slick-slide-item">';
										echo '<a href="' . esc_url( wp_get_attachment_url( $attachment_id, 'tf_gallery_thumb' ) ) . '" class="slick-slide-item-link" data-fancybox="hotel-gallery">';
										echo wp_get_attachment_image( $attachment_id, 'tf_gallery_thumb' );
										echo '</a>';
										echo '</div>';
									} ?>
								</div>
								<div class="swiper-button-prev sw-btn"></div>
								<div class="swiper-button-next sw-btn"></div>
							</div>
						</div>
					</div>
				<?php } else { ?>
					<div class="tf-gallery-wrap">
						<div class="list-single-main-media fl-wrap" id="sec1">
							<div class="single-slider-wrapper fl-wrap">
								<div class="tf_slider-for fl-wrap tf-slick-slider">

									<a href="<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'tf_gallery_thumb' ) ) ? esc_url( get_the_post_thumbnail_url( $post_id, 'tf_gallery_thumb' ) ) : esc_url( TF_ASSETS_APP_URL . 'images/feature-default.jpg' ); ?>"
										class="slick-slide-item-link" data-fancybox="hotel-gallery">
										<img src="<?php echo ! empty( get_the_post_thumbnail_url( $post_id, 'tf_gallery_thumb' ) ) ? esc_url( get_the_post_thumbnail_url( $post_id, 'tf_gallery_thumb' ) ) : esc_url( TF_ASSETS_APP_URL . 'images/feature-default.jpg' ); ?>"
												alt="">
									</a>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
            <?php if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ): ?>
			<script>
				jQuery(document).ready(function ($) {
					'use strict';
				
					var sbp = $('.swiper-button-prev'),
						sbn = $('.swiper-button-next');

					$('.single-slider-wrapper .tf_slider-for').slick({
						slide: '.slick-slide-item',
						slidesToShow: 1,
						slidesToScroll: 1,
						arrows: false,
						fade: false,
						dots: false,
						centerMode: false,
						variableWidth: false,
						adaptiveHeight: true
					});

					sbp.on("click", function () {
						$(this).closest(".single-slider-wrapper").find('.tf_slider-for').slick('slickPrev');
					});

					sbn.on("click", function () {
						$(this).closest(".single-slider-wrapper").find('.tf_slider-for').slick('slickNext');
					});
				});	
			</script>
			<?php endif;
        }
		elseif( $style == 'style3'){
			?>
			<div class="tf-apt-hero-section tf-single-gallery__style-3">
				<div class="tf-apt-hero-wrapper">
					<?php if ( ! empty( $gallery_ids ) ) :
						$first_image = ! empty( $gallery_ids[0] ) ? wp_get_attachment_image( $gallery_ids[0], 'tf_apartment_gallery_large' ) : '';
						$second_image = ! empty( $gallery_ids[1] ) ? wp_get_attachment_image( $gallery_ids[1], 'tf_apartment_gallery_small' ) : '';
						$third_image = ! empty( $gallery_ids[2] ) ? wp_get_attachment_image( $gallery_ids[2], 'tf_apartment_gallery_small' ) : '';
						?>
						<div class="tf-apt-hero-gallery">
							<div class="tf-apt-hero-left <?php echo ( ! empty( $second_image ) || ! empty( $third_image ) ) ? 'has-right' : ''; ?>">
								<a href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[0], 'full' ) ); ?>"
								data-fancybox="hotel-gallery" class="hero-first-image">
									<?php echo wp_kses_post( $first_image ); ?>
								</a>
							</div>
							<?php if ( $second_image || $third_image ): ?>
								<div class="tf-apt-hero-right">
									<?php if ( ! empty( $second_image ) ): ?>
										<a href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[1], 'full' ) ); ?>"
										data-fancybox="hotel-gallery" class="hero-second-image">
											<?php echo wp_kses_post( $second_image ); ?>
										</a>
									<?php endif; ?>
									<?php if ( ! empty( $third_image ) ): ?>
										<a href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[2], 'full' ) ); ?>"
										data-fancybox="hotel-gallery" class="hero-third-image <?php echo count( $gallery_ids ) > 3 ? 'has-more' : ''; ?>">
											<?php echo wp_kses_post( $third_image ); ?>
										</a>
									<?php endif; ?>
								</div>
								<?php if ( count( $gallery_ids ) > 3 ): ?>
									<div class="gallery-all-photos">
										<a href="<?php echo esc_url( wp_get_attachment_image_url( $gallery_ids[3], 'full' ) ); ?>"
										class="tf_btn" data-fancybox="hotel-gallery">
											<?php esc_html_e( 'All Photos', 'tourfic' ) ?>
										</a>
										<?php foreach ( $gallery_ids as $key => $item ) :
											if ( $key < 4 ) {
												continue;
											}
											?>
											<a href="<?php echo esc_url( wp_get_attachment_image_url( $item, 'full' ) ); ?>"
											data-fancybox="hotel-gallery"></a>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					<?php else: ?>
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'tf_apartment_single_thumb' );
						} else {
							echo '<img src="' . esc_url( TF_ASSETS_APP_URL . 'images/feature-default.jpg' ) . '" alt="feature-default">';
						}
						?>
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
