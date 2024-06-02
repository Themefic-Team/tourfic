<?php

namespace Tourfic\App\Widgets\TF_Widgets;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Similar Tours
 */
class Similar_Tours extends \WP_Widget {

    use \Tourfic\Traits\Singleton;

    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_similar_tours', // Base ID
            esc_html__( 'Tourfic - Similar Tours', 'tourfic' ), // Name
            array( 'description' => esc_html__( 'Show more tours button on single tour page.', 'tourfic' ) ) // Args
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
        $btn_label = isset( $instance['btn_label'] ) ? $instance['btn_label'] : esc_html__( 'Show more hotels', 'tourfic' );

        if ( !is_singular( 'tourfic' ) ) {
            return;
        }

        $terms = get_the_terms( get_the_ID(), 'destination' );

        echo wp_kses_post($before_widget);
        ?>
		<!-- Start similar tour widget -->
		<div class="tf-similar-tour-wrap">
			<?php
        if ( !empty( $title ) ) {
            echo wp_kses_post("<div class='not-impressive'>{$title}</div>");
        }
        ?>
			<div class="ni-buttons">
				<a href="<?php echo esc_url(tf_booking_search_action()) . '?destination=' . esc_attr( $terms[0]->name ) . '&adults=' . esc_attr($_GET['adults']) . '&children=' . esc_attr($_GET['children']) . '&room=' . esc_attr($_GET['room']) . '&check-in-date=' . esc_attr($_GET['check-in-date']) . '&check-out-date=' . esc_attr($_GET['check-out-date']); ?>" class="button tf_button btn-outline"><?php echo esc_html( $btn_label );?></a>
			</div>
		</div>
		<!-- End similar tour widget -->
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

        $title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Not impressive?', 'tourfic' );
        $btn_label = isset( $instance['btn_label'] ) ? $instance['btn_label'] : esc_html__( 'Show more hotels', 'tourfic' );
        ?>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'tourfic' );?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
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

        return $instance;
    }

}