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

// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Review
 */
class Review extends Widget_Base {

	use \Tourfic\Traits\Singleton;
	use \Tourfic\App\Widgets\Elementor\Support\Utils;

	protected $post_id;
	protected $post_type;

	public function get_name() {
		return 'tf-single-review';
	}

	public function get_title() {
		return esc_html__( 'Review', 'tourfic' );
	}

	public function get_icon() {
		return 'tf eicon-review';
	}

	public function get_categories() {
		return [ 'tourfic-pro' ];
	}

	public function get_keywords(){
        return [
            'ratting',
            'review',
			'tourfic',
			'tf'
        ];
    }

	public function get_style_depends(){
		return ['tf-elementor-single-review'];
	}

	protected function register_controls() {

		$this->tf_content_layout_controls();

		do_action( 'tf/single-review/before-style-controls', $this );
		// $this->tf_review_style_controls();
		do_action( 'tf/single-review/after-style-controls', $this );
	}

	protected function tf_content_layout_controls(){
        $this->start_controls_section('tf_post_review_content',[
            'label' => esc_html__('Review', 'tourfic'),
        ]);

        do_action( 'tf/single-review/before-content/controls', $this );

		$post_type = $this->get_current_post_type();
		$options = [
			'design-1' => esc_html__('Style 1', 'tourfic')
		];
		if(in_array($post_type, ['tf_hotel', 'tf_tours'])){
			$options['design-2'] = esc_html__('Style 2', 'tourfic');
			$options['design-3'] = esc_html__('Style 3', 'tourfic');
		}
		if($post_type == 'tf_apartment'){
			$options['design-2'] = esc_html__('Style 2', 'tourfic');
		}
		$this->add_control('review_style',[
			'label' => esc_html__('Review Style', 'tourfic'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'design-1',
			'options' => $options,
		]);

        $this->add_control('show_review_states',[
			'label' => esc_html__('Show Review States?', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
            'condition' => [
				'review_style' => ['design-2'],
			],
		]);

        $this->add_control('show_reviews',[
			'label' => esc_html__('Show Reviews?', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
            'condition' => [
				'review_style' => ['design-2'],
			],
		]);

        $this->add_control('show_review_form',[
			'label' => esc_html__('Show Review Form?', 'tourfic'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => esc_html__('Show', 'tourfic'),
			'label_off' => esc_html__('Hide', 'tourfic'),
			'return_value' => 'yes',
			'default' => 'yes',
            'condition' => [
				'review_style' => ['design-2'],
			],
		]);

	    do_action( 'tf/single-review/after-content/controls', $this );

        $this->end_controls_section();
    }

    protected function tf_review_style_controls() {
		$this->start_controls_section( 'review_style_section', [
			'label' => esc_html__( 'Style', 'tourfic' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

        $this->add_control( 'tf_title_heading', [
			'type'  => Controls_Manager::HEADING,
			'label' => __( 'Title', 'tourfic' ),
		] );

		$this->add_control( 'tf_title_color', [
			'label'     => esc_html__( 'Title Color', 'tourfic' ),
			'type'      => Controls_Manager::COLOR,
			'selectors'  => [
				'{{WRAPPER}} .tf-section-title' => 'color: {{VALUE}};',
				'{{WRAPPER}} .section-heading' => 'color: {{VALUE}};',
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

		if($this->post_type == 'tf_hotel'){
			$this->tf_hotel_review($settings);
        } elseif($this->post_type == 'tf_tours'){
			$this->tf_tour_review($settings);
        } elseif($this->post_type == 'tf_apartment'){
			$this->tf_apartment_review($settings);
        } elseif($this->post_type == 'tf_carrental'){
			$this->tf_car_review($settings);
        } else {
			return;
		}   
    }

	private function tf_hotel_review($settings) {
        $style = !empty($settings['review_style']) ? $settings['review_style'] : 'design-1';
        $show_review_states = isset($settings['show_review_states']) ? $settings['show_review_states'] : '';
        $show_reviews = isset($settings['show_reviews']) ? $settings['show_reviews'] : '';
        $show_review_form = isset($settings['show_review_form']) ? $settings['show_review_form'] : '';
		$meta = get_post_meta($this->post_id, 'tf_hotels_opt', true);
		$s_review = ! empty( Helper::tfopt( 'h-review' ) ) ? Helper::tfopt( 'h-review' ) : 0;
		$disable_review_sec   = ! empty( $meta['h-review'] ) ? $meta['h-review'] : '';
		$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;

        $comments_query = new \WP_Comment_Query( array(
            'post_id' => $this->post_id,
            'status'  => 'approve',
            'type'    => 'comment',
        ) );
        $comments       = $comments_query->comments;
		
		if ($style == 'design-1' && ! $disable_review_sec == 1) {
            ?>
            <div class="tf-single-template__one tf-single-review__style-1">
                <div class="tf-review-wrapper" id="tf-review">
                    <?php if ( get_comments_number() > 0 ) : ?>
                        <div class="tf-average-review">
                            <div class="tf-section-head">
                                <h2 class="tf-title tf-section-title"><?php echo !empty($meta['review-section-title']) ? esc_html($meta['review-section-title']) : ''; ?></h2>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php $this->review_template($settings); ?>
                </div>
            </div>
            <?php
        } elseif ($style == 'design-2' && $disable_review_sec != 1) {
            ?>
			<div class="tf-single-template__two tf-single-review__style-2">
                <div class="tf-sitebar-widgets tf-single-widgets">
                    <?php
                    global $current_user;
                    // Check if user is logged in
                    $is_user_logged_in = $current_user->exists();
                    // Get settings value
                    $tf_ratings_for = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
                    $tf_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
                    if ( $comments && $show_review_states == 'yes' ) :
                        $tf_overall_rate        = [];
                        TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
                        TF_Review::tf_get_review_fields( $fields );
                    ?>
                        <h2 class="tf-section-title"><?php esc_html_e("Overall reviews", "tourfic"); ?></h2>
                        <div class="tf-review-data-inner">
                            <div class="tf-review-data">
                                <div class="tf-review-data-average">
                                    <span class="avg-review"><span>
                                        <?php echo esc_html(sprintf( '%.1f', $total_rating )); ?>
                                    </span>/ <?php echo wp_kses_post($tf_settings_base); ?></span>
                                </div>
                                <div class="tf-review-all-info">
                                    <p><?php esc_html_e("Excellent", "tourfic"); ?> <span><?php esc_html_e("Total ", "tourfic"); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span></p>
                                </div>
                            </div>
                            <div class="tf-review-data-features">
                                <div class="tf-percent-progress">
                                <?php 
                                if ( $tf_overall_rate ) {
                                foreach ( $tf_overall_rate as $key => $value ) {
                                if ( empty( $value ) || ! in_array( $key, $fields ) ) {
                                    continue;
                                }
                                $value = TF_Review::tf_average_ratings( $value );
                                ?>
                                    <div class="tf-progress-item">                                    
                                        <div class="tf-review-feature-label">
                                            <p class="feature-label"><?php echo esc_html( $key ); ?></p>
                                            <p class="feature-rating"> <?php echo wp_kses_post($value); ?></p>
                                        </div>
                                        <div class="tf-progress-bar">
                                            <span class="percent-progress" style="width: <?php echo wp_kses_post( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) )); ?>%"></span>
                                        </div>
                                    </div>
                                    <?php } } ?>
                                        
                                </div>
                            </div>
                        </div>
                        <a class="tf-all-reviews" href="#tf-hotel-reviews"><?php esc_html_e("See all reviews", "tourfic"); ?></a>
                    <?php endif; ?>

                    <?php
                    if($show_review_form == 'yes'):
                        $tf_comment_counts = get_comments( array(
                            'post_id' => $this->post_id,
                            'user_id' => $current_user->ID,
                            'count'   => true,
                        ) );
                        ?>
                        <?php if( empty($tf_comment_counts) && $tf_comment_counts == 0 ) : ?>
                            <button class="tf_btn tf_btn_full tf_btn_sharp tf_btn_large tf-review-open">
                            <?php esc_html_e("Leave your review", "tourfic"); ?>
                        </button>
                        <?php endif; ?>
                        <?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $this->post_id ) ?? ''); ?>
                        <?php
                        if ( ! empty( $tf_ratings_for ) ) {
                            if ( $is_user_logged_in ) {
                            if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
                            ?>
                        <div class="tf-review-form-wrapper" action="">
                            <h3><?php esc_html_e("Leave your review", "tourfic"); ?></h3>
                            <p><?php esc_html_e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                        <?php
                            }
                        } else {
                        if ( in_array( 'lo', $tf_ratings_for ) ) {
                        ?>
                        <div class="tf-review-form-wrapper" action="">
                            <h3><?php esc_html_e("Leave your review", "tourfic"); ?></h3>
                            <p><?php esc_html_e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                        <?php } } } ?>
                    <?php endif; ?>

                </div>

                <?php if ( $comments && $show_reviews == 'yes' ) { ?>
                <div class="tf-reviews-wrapper tf-section" id="tf-hotel-reviews">         
                    <h2 class="tf-section-title"><?php echo !empty( $meta['review-section-title'] ) ? esc_html($meta['review-section-title']) : ''; ?></h2>
                    <p><?php esc_html_e("Total", "tourfic"); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></p>
                    <div class="tf-reviews-slider tf-slick-slider">
                        <?php
                        foreach ( $comments as $comment ) {
                        // Get rating details
                        $tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
                        if ( $tf_overall_rate == false ) {
                            $tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
                            $tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
                        }
                        $base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
                        $c_rating  = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

                        // Comment details
                        $c_avatar      = get_avatar( $comment, '56' );
                        $c_author_name = $comment->comment_author;
                        $c_date        = $comment->comment_date;
                        $c_content     = $comment->comment_content;
                        ?>
                        <div class="tf-reviews-item">
                            <div class="tf-reviews-avater">
                                <?php echo wp_kses_post($c_avatar); ?>
                            </div>
                            <div class="tf-reviews-text">
                                <span class="tf-review-rating"><?php echo wp_kses_post($c_rating); ?></span>
                                <span class="tf-reviews-meta"><?php echo esc_html($c_author_name); ?>, <?php echo wp_kses_post(gmdate("F Y", strtotime($c_date))); ?></span>
                                <p><?php echo wp_kses_post(\Tourfic\Classes\Helper::tourfic_character_limit_callback($c_content, 180)); ?></p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
			<?php
        } elseif ($style == 'design-3' && ! $disable_review_sec == 1) {
            ?>
            <div class="tf-single-template__legacy tf-single-review__style-legacy">
                <div id="tf-review" class="review-section">
                    <div class="reviews">
                        <h2 class="section-heading"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
                        <?php $this->review_template($settings); ?>
                    </div>
                </div>
            </div>
			<?php
        }
	}

	private function tf_tour_review($settings) {
		$style = !empty($settings['review_style']) ? $settings['review_style'] : 'design-1';
        $show_review_states = isset($settings['show_review_states']) ? $settings['show_review_states'] : '';
        $show_reviews = isset($settings['show_reviews']) ? $settings['show_reviews'] : '';
        $show_review_form = isset($settings['show_review_form']) ? $settings['show_review_form'] : '';
		$meta = get_post_meta($this->post_id, 'tf_tours_opt', true);
		$s_review = ! empty( Helper::tfopt( 't-review' ) ) ? Helper::tfopt( 't-review' ) : 0;
		$disable_review_sec   = ! empty( $meta['t-review'] ) ? $meta['t-review'] : '';
		$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;

        $comments_query = new \WP_Comment_Query( array(
            'post_id' => $this->post_id,
            'status'  => 'approve',
            'type'    => 'comment',
        ) );
        $comments       = $comments_query->comments;
		
		if ($style == 'design-1' && ! $disable_review_sec == 1) {
            ?>
            <div class="tf-single-template__one tf-single-review__style-1">
                <div class="tf-review-wrapper" id="tf-review">
                    <?php if ( get_comments_number() > 0 ) : ?>
                        <div class="tf-average-review">
                            <div class="tf-section-head">
                                <h2 class="tf-title tf-section-title"><?php echo !empty($meta['review-section-title']) ? esc_html($meta['review-section-title']) : ''; ?></h2>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php $this->review_template($settings); ?>
                </div>
            </div>
            <?php
        } elseif ($style == 'design-2' && $disable_review_sec != 1) {
            ?>
			<div class="tf-single-template__two tf-single-review__style-2">
                <div class="tf-sitebar-widgets tf-single-widgets">
                    <?php
                    global $current_user;
                    // Check if user is logged in
                    $is_user_logged_in = $current_user->exists();
                    // Get settings value
                    $tf_ratings_for = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
                    $tf_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
                    if ( $comments && $show_review_states == 'yes') :
                        $tf_overall_rate        = [];
                        TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
                        TF_Review::tf_get_review_fields( $fields );
                    ?>
                        <h2 class="tf-section-title"><?php esc_html_e("Overall reviews", "tourfic"); ?></h2>
                        <div class="tf-review-data-inner">
                            <div class="tf-review-data">
                                <div class="tf-review-data-average">
                                    <span class="avg-review"><span>
                                        <?php echo esc_html(sprintf( '%.1f', $total_rating )); ?>
                                    </span>/ <?php echo wp_kses_post($tf_settings_base); ?></span>
                                </div>
                                <div class="tf-review-all-info">
                                    <p><?php esc_html_e("Excellent", "tourfic"); ?> <span><?php esc_html_e("Total ", "tourfic"); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span></p>
                                </div>
                            </div>
                            <div class="tf-review-data-features">
                                <div class="tf-percent-progress">
                                <?php 
                                if ( $tf_overall_rate ) {
                                foreach ( $tf_overall_rate as $key => $value ) {
                                if ( empty( $value ) || ! in_array( $key, $fields ) ) {
                                    continue;
                                }
                                $value = TF_Review::tf_average_ratings( $value );
                                ?>
                                    <div class="tf-progress-item">                                    
                                        <div class="tf-review-feature-label">
                                            <p class="feature-label"><?php echo esc_html( $key ); ?></p>
                                            <p class="feature-rating"> <?php echo wp_kses_post($value); ?></p>
                                        </div>
                                        <div class="tf-progress-bar">
                                            <span class="percent-progress" style="width: <?php echo wp_kses_post( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) )); ?>%"></span>
                                        </div>
                                    </div>
                                    <?php } } ?>
                                        
                                </div>
                            </div>
                        </div>
                        <a class="tf-all-reviews" href="#tf-hotel-reviews"><?php esc_html_e("See all reviews", "tourfic"); ?></a>
                    <?php endif; ?>

                    <?php
                    if($show_review_form == 'yes'):
                        $tf_comment_counts = get_comments( array(
                            'post_id' => $this->post_id,
                            'user_id' => $current_user->ID,
                            'count'   => true,
                        ) );
                        ?>
                        <?php if( empty($tf_comment_counts) && $tf_comment_counts == 0 ) : ?>
                            <button class="tf_btn tf_btn_full tf_btn_sharp tf_btn_large tf-review-open">
                            <?php esc_html_e("Leave your review", "tourfic"); ?>
                        </button>
                        <?php endif; ?>
                        <?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $this->post_id ) ?? ''); ?>
                        <?php
                        if ( ! empty( $tf_ratings_for ) ) {
                            if ( $is_user_logged_in ) {
                            if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
                            ?>
                        <div class="tf-review-form-wrapper" action="">
                            <h3><?php esc_html_e("Leave your review", "tourfic"); ?></h3>
                            <p><?php esc_html_e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                        <?php
                            }
                        } else {
                        if ( in_array( 'lo', $tf_ratings_for ) ) {
                        ?>
                        <div class="tf-review-form-wrapper" action="">
                            <h3><?php esc_html_e("Leave your review", "tourfic"); ?></h3>
                            <p><?php esc_html_e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                        <?php } } } ?>
                    <?php endif; ?>
                </div>

                <?php if ( $comments && $show_reviews == 'yes') { ?>
                <div class="tf-reviews-wrapper tf-section" id="tf-hotel-reviews">         
                    <h2 class="tf-section-title"><?php echo !empty( $meta['review-section-title'] ) ? esc_html($meta['review-section-title']) : ''; ?></h2>
                    <p><?php esc_html_e("Total", "tourfic"); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></p>
                    <div class="tf-reviews-slider tf-slick-slider">
                        <?php
                        foreach ( $comments as $comment ) {
                        // Get rating details
                        $tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
                        if ( $tf_overall_rate == false ) {
                            $tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
                            $tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
                        }
                        $base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
                        $c_rating  = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

                        // Comment details
                        $c_avatar      = get_avatar( $comment, '56' );
                        $c_author_name = $comment->comment_author;
                        $c_date        = $comment->comment_date;
                        $c_content     = $comment->comment_content;
                        ?>
                        <div class="tf-reviews-item">
                            <div class="tf-reviews-avater">
                                <?php echo wp_kses_post($c_avatar); ?>
                            </div>
                            <div class="tf-reviews-text">
                                <span class="tf-review-rating"><?php echo wp_kses_post($c_rating); ?></span>
                                <span class="tf-reviews-meta"><?php echo esc_html($c_author_name); ?>, <?php echo wp_kses_post(gmdate("F Y", strtotime($c_date))); ?></span>
                                <p><?php echo wp_kses_post(\Tourfic\Classes\Helper::tourfic_character_limit_callback($c_content, 180)); ?></p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
			<?php
        } elseif ($style == 'design-3' && ! $disable_review_sec == 1) {
            ?>
            <div class="tf-single-template__legacy tf-single-review__style-legacy">
                <div id="tf-review" class="review-section">
                    <div class="reviews">
                        <h2 class="section-heading"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
                        <?php $this->review_template($settings); ?>
                    </div>
                </div>
            </div>
			<?php
        }
	}

	private function tf_apartment_review($settings) {
        $style = !empty($settings['review_style']) ? $settings['review_style'] : 'design-1';
        $show_review_states = isset($settings['show_review_states']) ? $settings['show_review_states'] : '';
        $show_reviews = isset($settings['show_reviews']) ? $settings['show_reviews'] : '';
        $show_review_form = isset($settings['show_review_form']) ? $settings['show_review_form'] : '';
		$meta = get_post_meta($this->post_id, 'tf_apartment_opt', true);
		
		$s_review = ! empty( Helper::tfopt( 'disable-apartment-review' ) ) ? Helper::tfopt( 'disable-apartment-review' ) : 0;
		$disable_review_sec   = ! empty( $meta['disable-apartment-review'] ) ? $meta['disable-apartment-review'] : '';
		$disable_review_sec = ! empty( $disable_review_sec ) ? $disable_review_sec : $s_review;

        $comments_query = new \WP_Comment_Query( array(
            'post_id' => $this->post_id,
            'status'  => 'approve',
            'type'    => 'comment',
        ) );
        $comments       = $comments_query->comments;
		
		if ($style == 'design-2' && $disable_review_sec != 1) {
            ?>
			<div class="tf-single-template__two tf-single-review__style-2">
                <div class="tf-sitebar-widgets tf-single-widgets">
                    <?php
                    global $current_user;
                    // Check if user is logged in
                    $is_user_logged_in = $current_user->exists();
                    // Get settings value
                    $tf_ratings_for = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
                    $tf_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
                    if ( $comments && $show_review_states == 'yes') :
                        $tf_overall_rate        = [];
                        TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
                        TF_Review::tf_get_review_fields( $fields );
                    ?>
                        <h2 class="tf-section-title"><?php esc_html_e("Overall reviews", "tourfic"); ?></h2>
                        <div class="tf-review-data-inner">
                            <div class="tf-review-data">
                                <div class="tf-review-data-average">
                                    <span class="avg-review"><span>
                                        <?php echo esc_html(sprintf( '%.1f', $total_rating )); ?>
                                    </span>/ <?php echo wp_kses_post($tf_settings_base); ?></span>
                                </div>
                                <div class="tf-review-all-info">
                                    <p><?php esc_html_e("Excellent", "tourfic"); ?> <span><?php esc_html_e("Total ", "tourfic"); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span></p>
                                </div>
                            </div>
                            <div class="tf-review-data-features">
                                <div class="tf-percent-progress">
                                <?php 
                                if ( $tf_overall_rate ) {
                                foreach ( $tf_overall_rate as $key => $value ) {
                                if ( empty( $value ) || ! in_array( $key, $fields ) ) {
                                    continue;
                                }
                                $value = TF_Review::tf_average_ratings( $value );
                                ?>
                                    <div class="tf-progress-item">                                    
                                        <div class="tf-review-feature-label">
                                            <p class="feature-label"><?php echo esc_html( $key ); ?></p>
                                            <p class="feature-rating"> <?php echo wp_kses_post($value); ?></p>
                                        </div>
                                        <div class="tf-progress-bar">
                                            <span class="percent-progress" style="width: <?php echo wp_kses_post( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) )); ?>%"></span>
                                        </div>
                                    </div>
                                    <?php } } ?>
                                        
                                </div>
                            </div>
                        </div>
                        <a class="tf-all-reviews" href="#tf-hotel-reviews"><?php esc_html_e("See all reviews", "tourfic"); ?></a>
                    <?php endif; ?>

                    <?php
                    if($show_review_form == 'yes'):
                        $tf_comment_counts = get_comments( array(
                            'post_id' => $this->post_id,
                            'user_id' => $current_user->ID,
                            'count'   => true,
                        ) );
                        ?>
                        <?php if( empty($tf_comment_counts) && $tf_comment_counts == 0 ) : ?>
                            <button class="tf_btn tf_btn_full tf_btn_sharp tf_btn_large tf-review-open">
                            <?php esc_html_e("Leave your review", "tourfic"); ?>
                        </button>
                        <?php endif; ?>
                        <?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $this->post_id ) ?? ''); ?>
                        <?php
                        if ( ! empty( $tf_ratings_for ) ) {
                            if ( $is_user_logged_in ) {
                            if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
                            ?>
                        <div class="tf-review-form-wrapper" action="">
                            <h3><?php esc_html_e("Leave your review", "tourfic"); ?></h3>
                            <p><?php esc_html_e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                        <?php
                            }
                        } else {
                        if ( in_array( 'lo', $tf_ratings_for ) ) {
                        ?>
                        <div class="tf-review-form-wrapper" action="">
                            <h3><?php esc_html_e("Leave your review", "tourfic"); ?></h3>
                            <p><?php esc_html_e("Your email address will not be published. Required fields are marked.", "tourfic"); ?></p>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                        <?php } } } ?>
                    <?php endif; ?>

                </div>

                <?php if ( $comments && $show_reviews == 'yes') { ?>
                <div class="tf-reviews-wrapper tf-section" id="tf-hotel-reviews">         
                    <h2 class="tf-section-title"><?php echo !empty( $meta['review-section-title'] ) ? esc_html($meta['review-section-title']) : ''; ?></h2>
                    <p><?php esc_html_e("Total", "tourfic"); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></p>
                    <div class="tf-reviews-slider tf-slick-slider">
                        <?php
                        foreach ( $comments as $comment ) {
                        // Get rating details
                        $tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
                        if ( $tf_overall_rate == false ) {
                            $tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
                            $tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
                        }
                        $base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
                        $c_rating  = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

                        // Comment details
                        $c_avatar      = get_avatar( $comment, '56' );
                        $c_author_name = $comment->comment_author;
                        $c_date        = $comment->comment_date;
                        $c_content     = $comment->comment_content;
                        ?>
                        <div class="tf-reviews-item">
                            <div class="tf-reviews-avater">
                                <?php echo wp_kses_post($c_avatar); ?>
                            </div>
                            <div class="tf-reviews-text">
                                <span class="tf-review-rating"><?php echo wp_kses_post($c_rating); ?></span>
                                <span class="tf-reviews-meta"><?php echo esc_html($c_author_name); ?>, <?php echo wp_kses_post(gmdate("F Y", strtotime($c_date))); ?></span>
                                <p><?php echo wp_kses_post(\Tourfic\Classes\Helper::tourfic_character_limit_callback($c_content, 180)); ?></p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
			<?php
        } elseif ($style == 'design-1' && ! $disable_review_sec == 1) {
            ?>
            <div class="tf-single-template__legacy tf-single-review__style-legacy">
                <div id="tf-review" class="review-section">
                    <div class="reviews">
                        <h2 class="section-heading"><?php echo ! empty( $meta['review-section-title'] ) ? esc_html( $meta['review-section-title'] ) : ''; ?></h2>
                        <?php $this->review_template($settings); ?>
                    </div>
                </div>
            </div>
			<?php
        }
	}

	private function tf_car_review($settings) {
        $style = !empty($settings['review_style']) ? $settings['review_style'] : 'design-1';
		$meta = get_post_meta($this->post_id, 'tf_carrental_opt', true);
	    $review_sec_title  = ! empty( $meta['review_sec_title'] ) ? $meta['review_sec_title'] : '';
		global $current_user;
        $is_user_logged_in = $current_user->exists();
        $tf_ratings_for   = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
        $tf_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;

        $tf_comment_counts = get_comments( array(
            'post_id' => $this->post_id,
            'user_id' => $current_user->ID,
            'count'   => true,
        ) );
        $comments_query = new \WP_Comment_Query( array(
            'post_id' => $this->post_id,
            'status'  => 'approve',
            'type'    => 'comment',
        ) );
        $comments       = $comments_query->comments;

		if ($style == 'design-1') {
		?>
        <div class="tf-single-template__one tf-car-single-review__style-1">
            <div class="tf-review-section" id="tf-reviews">
            <?php if ( $comments ) {
                $tf_overall_rate = [];
                TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
                TF_Review::tf_get_review_fields( $fields );
                ?>
                <?php if(!empty($review_sec_title)){ ?>   
                    <h3 class="section-heading"><?php echo esc_html($review_sec_title); ?></h3>
                <?php } ?>
                <div class="tf-review-data-inner">

                    <div class="tf-review-data">
                        <div class="tf-review-data-average">
                            <span class="avg-review tf-flex tf-flex-align-center tf-flex-gap-8">
                                <?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?>
                                <i class="fa fa-star"></i>
                            </span>
                            <div class="tf-review-all-info">
                                <p><?php esc_html_e( "From ", "tourfic" ); ?><?php TF_Review::tf_based_on_text( count( $comments ) ); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="tf-review-data-features">
                        <div class="tf-percent-progress">
                            <?php
                            if ( $tf_overall_rate ) {
                                foreach ( $tf_overall_rate as $key => $value ) {
                                    if ( empty( $value ) || ! in_array( $key, $fields ) ) {
                                        continue;
                                    }
                                    $value = TF_Review::tf_average_ratings( $value );
                                    ?>
                                    <div class="tf-progress-item">
                                        <div class="tf-progress-bar">
                                            <span class="percent-progress" style="width: <?php echo esc_attr( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
                                        </div>
                                        <div class="tf-review-feature-label">
                                            <p class="feature-label"><?php echo esc_html( $key ); ?></p>
                                            <p class="feature-rating"> <?php echo esc_html( $value ); ?></p>
                                        </div>
                                    </div>
                                <?php }
                            } ?>

                        </div>
                    </div>
                </div>
                <div class="tf-clients-reviews">
                    <?php
                    foreach ( $comments as $comment ) {
                        // Get rating details
                        $tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
                        if ( $tf_overall_rate == false ) {
                            $tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
                            $tf_overall_rate = TF_Review::tf_average_ratings( $tf_comment_meta );
                        }
                        $base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
                        $c_rating  = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

                        // Comment details
                        $c_avatar      = get_avatar( $comment, '56' );
                        $c_author_name = $comment->comment_author;
                        $c_date        = $comment->comment_date;
                        $c_content     = $comment->comment_content;
                        ?>
                        <div class="tf-reviews-item tf-flex tf-flex-gap-16">
                            <div class="tf-reviews-avater">
                                <?php echo wp_kses_post( $c_avatar ); ?>
                            </div>
                            <div class="tf-reviews-text">
                                <span class="tf-review-rating"><?php echo wp_kses_post( $c_rating ); ?></span>
                                <span class="tf-reviews-meta"><?php echo esc_html( $c_author_name ); ?> <span class="tf-reviews-time">| <?php echo wp_kses_post( gmdate( "F Y", strtotime( $c_date ) ) ); ?></span></span>
                                <p><?php echo wp_kses_post( \Tourfic\Classes\Helper::tourfic_character_limit_callback( $c_content, 180 ) ); ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $this->post_id ) ?? '' ); ?>
            <?php

            if ( ! empty( $tf_ratings_for ) && empty( $tf_comment_counts ) && $tf_comment_counts == 0 ) {
                if ( $is_user_logged_in ) {
                    if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
                        ?>
                        <div class="tf-review-form-wrapper" action="">
                            <h3><?php esc_html_e( "Leave your review", "tourfic" ); ?></h3>
                            <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                        <?php
                    }
                } else {
                    if ( in_array( 'lo', $tf_ratings_for ) ) {
                        ?>
                        <div class="tf-review-form-wrapper" action="">
                            <h3><?php esc_html_e( "Leave your review", "tourfic" ); ?></h3>
                            <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                    <?php }
                }
            } ?>
            </div>
        </div>
		<?php
        }
	}

    private function review_template($settings) {
        global $current_user;
        $is_user_logged_in = $current_user->exists();
        $tf_ratings_for = Helper::tfopt( 'r-for' ) ?? [ 'li', 'lo' ];
        $style = !empty($settings['review_style']) ? $settings['review_style'] : 'design-1';

        $args           = array(
            'post_id' => $this->post_id,
            'status'  => 'approve',
            'type'    => 'comment',
        );
        $comments_query = new \WP_Comment_Query( $args );
        $comments       = $comments_query->comments;

        if ( ( get_post_type( $this->post_id ) == 'tf_tours' && $style == "design-1" ) ||
            ( get_post_type( $this->post_id ) == "tf_hotel" && $style == "design-1" ) ||
            ( get_post_type( $this->post_id ) == "tf_apartment" && $style == "design-2" ) ) {

            if ( $comments ) {
                $tf_overall_rate = [];
                TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
                TF_Review::tf_get_review_fields( $fields );
                $tf_settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
                ?>
                <div class="tf-review-data tf-box">

                    <div class="tf-review-data-inner tf-flex tf-flex-gap-24">
                        <div class="tf-review-data">
                            <div class="tf-review-data-average">
                                <p><?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?></p>
                            </div>
                            <div class="tf-review-all-info">
                                <ul class="tf-list">
                                    <li><i class="fa-solid fa-circle-check"></i><?php esc_html_e( "From ", "tourfic" ); ?> <?php TF_Review::tf_based_on_text( count( $comments ) ); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="tf-review-data-features">
                            <div class="tf-percent-progress tf-flex tf-flex-space-bttn">
                                <?php
                                if ( $tf_overall_rate ) {
                                    foreach ( $tf_overall_rate as $key => $value ) {
                                        if ( empty( $value ) || ! in_array( $key, $fields ) ) {
                                            continue;
                                        }
                                        $value = TF_Review::Tf_average_ratings( $value );
                                        ?>
                                        <div class="tf-progress-item">
                                            <div class="tf-progress-bar">
                                                <span class="percent-progress" style="width: <?php echo esc_html( TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) ); ?>%"></span>
                                            </div>
                                            <div class="tf-review-feature-label tf-flex tf-flex-space-bttn">
                                                <p class="feature-label"><?php echo esc_html( $key ); ?></p>
                                                <p class="feature-rating"> <?php echo esc_html( $value ); ?></p>
                                            </div>
                                        </div>
                                    <?php }
                                } ?>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tourfic review reply -->
                <div class="tf-review-reply tf-mt-50">
                    <div class="tf-section-head">
                        <h2 class="tf-title tf-section-title"><?php esc_html_e( "Showing", "tourfic" ); ?> <span><?php echo count( $comments ); ?></span> <?php esc_html_e( "Review", "tourfic" ); ?></h2>
                    </div>
                    <?php
                    foreach ( $comments as $comment ) {

                        // Get rating details
                        $tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
                        if ( $tf_overall_rate == false ) {
                            $tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
                            $tf_overall_rate = TF_Review::Tf_average_ratings( $tf_comment_meta );
                        }
                        $base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
                        $c_rating  = Tf_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

                        // Comment details
                        $c_avatar = get_avatar( $comment, '56' );
                        $c_author_name = $comment->comment_author;
                        $c_date        = $comment->comment_date;
                        $c_content     = $comment->comment_content;
                        ?>
                        <!-- reviews and replies -->
                        <div class="tf-review-reply-data tf-flex-gap-24 tf-flex">
                            <div class="tf-review-author">
                                <?php echo wp_kses_post( $c_avatar ); ?>
                            </div>
                            <div class="tf-review-details">
                                <div class="tf-review-author-name">
                                    <h3><?php echo esc_html( $c_author_name ); ?></h3>
                                </div>
                                <div class="tf-review-ratings tf-mt-8">
                                    <?php echo wp_kses_post( $c_rating ); ?>
                                </div>
                                <div class="tf-review-message">
                                    <p><?php echo wp_kses_post( $c_content ); ?></p>
                                </div>
                                <?php if(get_post_type( $this->post_id ) == "tf_hotel" && $style != "design-3"): ?>
                                    <div class="tf-review-date">
                                        <ul class="tf-list">
                                            <li><i class="fa-regular fa-clock"></i> <?php echo esc_html( gmdate( "F d, Y", strtotime( $c_date ) ) ); ?></li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <?php echo wp_kses_post( TF_Review::tf_pending_review_notice( $this->post_id ) ?? "" ); ?>

                <?php
                if ( ! empty( $tf_ratings_for ) ) {
                    if ( $is_user_logged_in ) {
                        if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
                            ?>
                            <!-- Replay form  -->
                            <div class="tf-review-form tf-mt-40">
                                <div class="tf-section-head">
                                    <h2 class="tf-title tf-section-title"><?php esc_html_e( "Leave a Review", "tourfic" ); ?></h2>
                                    <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
                                </div>
                                <?php TF_Review::tf_review_form(); ?>
                            </div>
                            <?php
                        }
                    } else {
                        if ( in_array( 'lo', $tf_ratings_for ) ) {
                            ?>
                            <!-- Replay form  -->
                            <div class="tf-review-form tf-mt-40">
                                <div class="tf-section-head">
                                    <h2 class="tf-title tf-section-title"><?php esc_html_e( "Leave a Review", "tourfic" ); ?></h2>
                                    <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
                                </div>
                                <?php TF_Review::tf_review_form(); ?>
                            </div>
                            <?php
                        }
                    }
                }
            } else {
                echo '<div class="no-review">';
                echo '<h4>' . esc_html__( "No Review Available", "tourfic" ) . '</h4>';
                
                if ( $is_user_logged_in ) {

                    // Add Review button
                    if ( is_array( $tf_ratings_for ) && in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
                        ?>
                        <!-- Replay form  -->
                        <div class="tf-review-form tf-mt-40">
                            <div class="tf-section-head">
                                <h2 class="tf-title tf-section-title"><?php esc_html_e( "Leave a Review", "tourfic" ); ?></h2>
                                <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
                            </div>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                        <?php
                    }
                } else {

                    if ( is_array( $tf_ratings_for ) && in_array( 'lo', $tf_ratings_for ) ) {
                        ?>
                        <!-- Replay form  -->
                        <div class="tf-review-form tf-mt-40">
                            <div class="tf-section-head">
                                <h2 class="tf-title tf-section-title"><?php esc_html_e( "Leave a Review", "tourfic" ); ?></h2>
                                <p><?php esc_html_e( "Your email address will not be published. Required fields are marked.", "tourfic" ); ?></p>
                            </div>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                        <?php
                    }
                }
                // Pending review notice
                echo wp_kses_post( TF_Review::tf_pending_review_notice( $this->post_id ) ?? "" );
                echo '</div>';
            }
        } else {
            ?>
            <div class="tf-review-container">
                <?php
                if ( get_post_type( $this->post_id ) == "tf_apartment" && $style == "default" ) {
                    $btn_class = 'tf_btn tf_btn_full';
                } else {
                    $btn_class = 'tf_btn tf-submit';
                }

                /**
                 * Review query
                 */
                $args           = array(
                    'post_id' => $this->post_id,
                    'status'  => 'approve',
                    'type'    => 'comment',
                );
                $comments_query = new \WP_Comment_Query( $args );
                $comments       = $comments_query->comments;

                if ( $comments ) {

                    $tf_rating_progress_bar = '';
                    $tf_overall_rate        = [];
                    TF_Review::tf_calculate_comments_rating( $comments, $tf_overall_rate, $total_rating );
                    TF_Review::tf_get_review_fields( $fields );

                    if ( $tf_overall_rate ) {


                        foreach ( $tf_overall_rate as $key => $value ) {

                            if ( empty( $value ) || ! in_array( $key, $fields ) ) {
                                continue;
                            }

                            $value                  = TF_Review::Tf_average_ratings( $value );
                            $tf_rating_progress_bar .= '<div class="tf-single">';
                            $tf_rating_progress_bar .= '<div class="tf-text">' . $key . '</div>';
                            $tf_rating_progress_bar .= '<div class="tf-p-bar"><div class="percent-progress" data-width="' . TF_Review::tf_average_rating_percent( $value, Helper::tfopt( 'r-base' ) ) . '"></div></div>';
                            $tf_rating_progress_bar .= '<div class="tf-p-b-rating">' . $value . '</div>';
                            $tf_rating_progress_bar .= '</div>';

                        }
                    }
                    ?>

                    <div class="tf-total-review">
                        <div class="tf-total-average">
                            <div><?php echo esc_html( sprintf( '%.1f', $total_rating ) ); ?></div>
                            <span><?php TF_Review::tf_based_on_text( count( $comments ) ); ?></span>
                        </div>
                        <?php
                        if ( ! empty( $tf_ratings_for ) ) {
                            if ( $is_user_logged_in ) {
                                if ( in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
                                    ?>
                                    <div class="tf-btn-wrap">
                                        <button class="<?php echo esc_attr( $btn_class ); ?> tf-modal-btn" data-target="#tf-rating-modal">
                                            <i class="fas fa-plus"></i> <?php esc_html_e( 'Add Review', 'tourfic' ); ?>
                                        </button>
                                    </div>
                                    <?php
                                }
                            } else {
                                if ( in_array( 'lo', $tf_ratings_for ) ) {
                                    ?>
                                    <div class="tf-btn-wrap">
                                        <button class="<?php echo esc_attr( $btn_class ); ?> tf-modal-btn" data-target="#tf-rating-modal">
                                            <i class="fas fa-plus"></i> <?php esc_html_e( 'Add Review', 'tourfic' ) ?>
                                        </button>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                    <?php if ( ! empty( $tf_rating_progress_bar ) ) { ?>
                        <div class="tf-review-progress-bar">
                            <?php echo wp_kses_post( $tf_rating_progress_bar ); ?>
                        </div>
                    <?php } ?>

                    <div class="tf-single-review <?php echo esc_attr( get_post_type( $this->post_id ) ) ?>">
                        <?php
                        if ( $comments ) {
                            foreach ( $comments as $comment ) {

                                // Get rating details
                                $tf_overall_rate = get_comment_meta( $comment->comment_ID, TF_TOTAL_RATINGS, true );
                                if ( $tf_overall_rate == false ) {
                                    $tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
                                    $tf_overall_rate = TF_Review::Tf_average_ratings( $tf_comment_meta );
                                }
                                $base_rate = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
                                $c_rating  = TF_Review::tf_single_rating_change_on_base( $tf_overall_rate, $base_rate );

                                // Comment details
                                $c_avatar      = get_avatar( $comment, '56' );
                                $c_author_name = $comment->comment_author;
                                $c_date        = $comment->comment_date;
                                $c_content     = $comment->comment_content;
                                global $post_type;
                                ?>
                                <div class="tf-single-details">
                                    <div class="tf-review-avatar"><?php echo wp_kses_post( $c_avatar ); ?></div>
                                    <div class="tf-review-details">
                                        <div class="tf-name"><?php echo esc_html( $c_author_name ); ?></div>
                                        <div class="tf-date"><?php echo esc_html( $c_date ); ?></div>
                                        <div class="tf-rating-stars">
                                            <?php echo wp_kses_post( $c_rating ); ?>
                                        </div>
                                        <?php if ( $post_type == 'apartment' ) {
                                            if ( $style == "default" ) {
                                                if ( strlen( $c_content ) > 120 ) { ?>
                                                    <div class="tf-description">
                                                        <p><?php echo wp_kses_post( Helper::tourfic_character_limit_callback( $c_content, 120 ) ) ?></p>
                                                    </div>
                                                    <div class="tf-full-description" style="display:none;">
                                                        <p><?php echo wp_kses_post( $c_content ) ?></p>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="tf-description">
                                                        <p><?php echo wp_kses_post( $c_content ); ?></p>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        } else { ?>
                                            <div class="tf-description"><p><?php echo wp_kses_post( $c_content ); ?></p></div>
                                        <?php } ?>
                                        <?php if ( $post_type == 'apartment' && $style == "default" && strlen( $c_content ) > 120 ): ?>
                                            <div class="tf-apartment-show-more"><?php esc_html_e( "Show more", "tourfic" ) ?></div>

                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <?php if ( $post_type == "apartment" && $style == 'default' && count( $comments ) > 2 ): ?>
                        <div class="show-all-review-wrap">
                            <div>
                                <div class="tf-apaartment-show-all">
                                    <?php esc_html_e( "Show all reviews", "tourfic" ); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Review moderation notice
                    echo wp_kses_post( TF_Review::tf_pending_review_notice( $this->post_id ) ?? '' );

                } else {

                    echo '<div class="no-review">';

                    echo '<h4>' . esc_html__( "No Review Available", "tourfic" ) . '</h4>';

                    if ( $is_user_logged_in ) {

                        // Add Review button
                        if ( is_array( $tf_ratings_for ) && in_array( 'li', $tf_ratings_for ) && ! TF_Review::tf_user_has_comments() ) {
                            ?>
                            <div class="tf-btn-wrap">
                                <button class="<?php echo esc_attr( $btn_class ); ?> tf-modal-btn" data-target="#tf-rating-modal">
                                    <i class="fas fa-plus"></i> <?php esc_html_e( 'Add Review', 'tourfic' ); ?>
                                </button>
                            </div>

                            <?php
                        }

                    } else {

                        if ( is_array( $tf_ratings_for ) && in_array( 'lo', $tf_ratings_for ) ) {
                            ?>
                            <div class="tf-btn-wrap">
                                <button class="<?php echo esc_attr( $btn_class ); ?> tf-modal-btn" data-target="#tf-rating-modal">
                                    <i class="fas fa-plus"></i> <?php esc_html_e( 'Add Review', 'tourfic' ) ?>
                                </button>
                            </div>
                            <?php
                        }
                    }
                    // Pending review notice
                    echo wp_kses_post( TF_Review::tf_pending_review_notice( $this->post_id ) ?? '' );

                    echo '</div>';
                }
                ?>
            </div>

            <div class="tf-modal" id="tf-rating-modal">
                <div class="tf-modal-dialog">
                    <div class="tf-modal-content">
                        <div class="tf-modal-header">
                            <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                        </div>
                        <div class="tf-modal-body">
                            <div id="tfreview-error-response"></div>
                            <?php TF_Review::tf_review_form(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
    }
}
