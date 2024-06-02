<?php

namespace Tourfic\App\Widgets\TF_Widgets;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Ask Question
 */
class Ask_Question extends \WP_Widget {

    use \Tourfic\Traits\Singleton;

    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_ask_question', // Base ID
            esc_html__( 'Tourfic - Ask Question', 'tourfic' ), // Name
            array( 'description' => esc_html__( 'Ask a question button on single hotel page.', 'tourfic' ) ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );
        $subtitle = isset( $instance['subtitle'] ) ? $instance['subtitle'] : esc_html__( 'Find more info in the FAQ section.', 'tourfic' );
        $btn_label = isset( $instance['btn_label'] ) ? $instance['btn_label'] : esc_html__( 'Ask a question', 'tourfic' );

        if ( !is_singular( array( 'tf_hotel', 'tf_tours' ) ) ) {
            return;
        }

        echo wp_kses_post($before_widget);

        ?>
		<!-- Start ask ques tour widget -->
		<div class="tf-gotq-tour-wrap">
			<div class="gotq-top">
				<?php
        if ( !empty( $title ) ) {
            echo wp_kses_post("<h4>{$title}</h4>");
        }
        ?>
				<?php
        if ( !empty( $subtitle ) ) {
            echo wp_kses_post("<p>{$subtitle}</p>");
        }
        ?>
			</div>
			<div class="ni-buttons">
				<a href="#" id="tf-ask-question-trigger" class="button tf_button btn-outline"><?php echo esc_html( $btn_label ); ?></a>
			</div>
		</div>
		<!-- End ask ques tour widget -->
        <?php

        echo wp_kses_post($after_widget);
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {

        $title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Got a question?', 'tourfic' );
        $subtitle = isset( $instance['subtitle'] ) ? $instance['subtitle'] : esc_html__( 'Find more info in the FAQ section.', 'tourfic' );
        $btn_label = isset( $instance['btn_label'] ) ? $instance['btn_label'] : esc_html__( 'Ask a question', 'tourfic' );
        ?>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'tourfic' );?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr($this->get_field_id( 'subtitle' )); ?>"><?php esc_html_e( 'Subtitle', 'tourfic' );?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'subtitle' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'subtitle' )); ?>" type="text" value="<?php echo esc_attr( $subtitle ); ?>" />
        </p>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr($this->get_field_id( 'btn_label' )); ?>"><?php esc_html_e( 'Button Label', 'tourfic' );?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'btn_label' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'btn_label' )); ?>" type="text" value="<?php echo esc_attr( $btn_label ); ?>" />
        </p>
    <?php }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
        $instance['btn_label'] = ( !empty( $new_instance['btn_label'] ) ) ? wp_strip_all_tags( $new_instance['btn_label'] ) : '';
        $instance['subtitle'] = ( !empty( $new_instance['subtitle'] ) ) ? wp_strip_all_tags( $new_instance['subtitle'] ) : '';

        return $instance;
    }

}