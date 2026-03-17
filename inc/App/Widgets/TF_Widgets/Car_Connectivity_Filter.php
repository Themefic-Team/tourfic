<?php

namespace Tourfic\App\Widgets\TF_Widgets;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Car filter by CarPlay / Android Auto availability.
 */
class Car_Connectivity_Filter extends \WP_Widget {

    use \Tourfic\Traits\Singleton;

    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_car_connectivity_filter',
            esc_html__( 'Tourfic - Car Filters by Connectivity', 'tourfic' ),
            array( 'description' => esc_html__( 'Filter search result by CarPlay / Android Auto availability', 'tourfic' ) )
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

        $posttype = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : get_post_type();

        if ( is_admin() || 'tf_carrental' === $posttype ) {
            extract( $args );

            $title       = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Connectivity', 'tourfic' ) );
            $available   = ! empty( $instance['available_label'] ) ? $instance['available_label'] : esc_html__( 'CarPlay / Android Auto', 'tourfic' );
            $unavailable = ! empty( $instance['unavailable_label'] ) ? $instance['unavailable_label'] : esc_html__( 'No CarPlay / Android Auto', 'tourfic' );

            $selected_values = array();
            if ( isset( $_GET['carplay_android_auto'] ) && function_exists( 'tf_normalize_car_binary_filter_values' ) ) {
                $selected_values = tf_normalize_car_binary_filter_values( wp_unslash( $_GET['carplay_android_auto'] ) );
            }

            echo wp_kses_post( $before_widget );
            if ( ! empty( $title ) ) {
                echo wp_kses_post( $before_title . $title . $after_title );
            }
            ?>
            <div class="tf-category-lists">
                <ul>
                    <li class="tf-filter-item">
                        <label>
                            <input type="checkbox" name="carplay_android_auto_filter[]" value="1" <?php checked( true, in_array( '1', $selected_values, true ) ); ?> />
                            <span class="tf-checkmark"></span>
                            <?php echo esc_html( $available ); ?>
                        </label>
                    </li>
                    <li class="tf-filter-item">
                        <label>
                            <input type="checkbox" name="carplay_android_auto_filter[]" value="0" <?php checked( true, in_array( '0', $selected_values, true ) ); ?> />
                            <span class="tf-checkmark"></span>
                            <?php echo esc_html( $unavailable ); ?>
                        </label>
                    </li>
                </ul>
            </div>
            <?php
            echo wp_kses_post( $after_widget );
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

        $title       = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Connectivity', 'tourfic' );
        $available   = isset( $instance['available_label'] ) ? $instance['available_label'] : esc_html__( 'CarPlay / Android Auto', 'tourfic' );
        $unavailable = isset( $instance['unavailable_label'] ) ? $instance['unavailable_label'] : esc_html__( 'No CarPlay / Android Auto', 'tourfic' );

        ?>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'tourfic' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr( $this->get_field_id( 'available_label' ) ); ?>"><?php esc_html_e( 'Available Label:', 'tourfic' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'available_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'available_label' ) ); ?>" type="text" value="<?php echo esc_attr( $available ); ?>" />
        </p>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr( $this->get_field_id( 'unavailable_label' ) ); ?>"><?php esc_html_e( 'Unavailable Label:', 'tourfic' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'unavailable_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'unavailable_label' ) ); ?>" type="text" value="<?php echo esc_attr( $unavailable ); ?>" />
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
        $instance                      = array();
        $instance['title']             = ! empty( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
        $instance['available_label']   = ! empty( $new_instance['available_label'] ) ? wp_strip_all_tags( $new_instance['available_label'] ) : '';
        $instance['unavailable_label'] = ! empty( $new_instance['unavailable_label'] ) ? wp_strip_all_tags( $new_instance['unavailable_label'] ) : '';

        return $instance;
    }
}
