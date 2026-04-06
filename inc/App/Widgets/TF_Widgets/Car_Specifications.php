<?php

namespace Tourfic\App\Widgets\TF_Widgets;

// Exit if accessed directly.
defined('ABSPATH') || exit;

use \Tourfic\Classes\Helper;

/**
 * Tour filter by features
 *
 * Works only for Tour
 */
class Car_Specifications extends \WP_Widget {

    use \Tourfic\Traits\Singleton;

    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_car_specifications', // Base ID
            esc_html__( 'Tourfic - Car Specifications', 'tourfic' ),
            array( 'description' => esc_html__( 'Car Will be Filter Based on Unlimited Mileage', 'tourfic' ) ) // Args
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

        //check if is Tours
        $posttype = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash($_GET['type']) ) : get_post_type();

        if ( is_admin() || $posttype == 'tf_carrental' ) {
            extract( $args );
            $title = apply_filters( 'widget_title', $instance['title'] );


            echo wp_kses_post($before_widget);
            if ( !empty( $title ) ) {
                echo wp_kses_post($before_title . $title . $after_title);
            }

            echo "<div class='tf-category-lists'><ul>";
            
            echo wp_kses("
            <li class='tf-filter-item'><label><input type='checkbox' name='car_unlimited_mileage' value='1'/><span class='tf-checkmark'></span>Unlimited Mileage</label></li>", Helper::tf_custom_wp_kses_allow_tags());

            echo "</ul></div>";

            echo wp_kses_post($after_widget);
        }
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {

        $title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Specifications', 'tourfic' );
        ?>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'tourfic' );?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

    <?php
    }

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
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';

        return $instance;
    }
}