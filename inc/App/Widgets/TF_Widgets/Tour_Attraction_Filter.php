<?php

namespace Tourfic\App\Widgets\TF_Widgets;

// Exit if accessed directly.
defined('ABSPATH') || exit;

use Tourfic\Classes\Helper;

/**
 * Tour filter by attraction
 * Works only for Tour
 * @author Abu Hena
 */
class Tour_Attraction_Filter extends \WP_Widget {

    use \Tourfic\Traits\Singleton;

    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_attraction_filter', // Base ID
            esc_html__( 'Tourfic - Tour Filter By Attraction', 'tourfic' ),
            array( 'description' => esc_html__( 'Filter search result by tour attraction', 'tourfic' ) ) // Args
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

        //check if is Hotel
        $posttype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();

        if ( is_admin() || $posttype == 'tf_tours' ) {
            extract( $args );
            $title = apply_filters( 'widget_title', $instance['title'] );

            $terms = isset( $instance['terms'] ) ? $instance['terms'] : 'all';
            $show_count = isset( $instance['show_count'] ) ? $instance['show_count'] : null;
            $hide_empty = ( $instance['hide_empty'] == "on" ) ? true : false;

            echo wp_kses_post($before_widget);
            if ( !empty( $title ) ) {
                echo wp_kses_post($before_title . $title . $after_title);
            }

            $taxonomy = array(
                'hide_empty' => $hide_empty,
                'taxonomy'   => 'tour_attraction',
                'include'    => $terms,
            );

            $get_terms = get_terms( $taxonomy );
            echo "<div class='tf-filter'><ul>";

            foreach ( $get_terms as $key => $term ) {
                $id = $term->term_id;
                $name = $term->name;
                $default_count = $term->count;
                $count = $show_count ? '<span>(' . $default_count . ')</span>' : '';

                echo wp_kses("<li class='tf-filter-item'><label><input type='checkbox' name='tf_attractions[]' value='{$id}' /><span class='tf-checkmark'></span> {$name}</label> {$count}</li>", Helper::tf_custom_wp_kses_allow_tags() );
            }
            echo "</ul><a href='#' class='see-more btn-link'>" . esc_html__( 'See more', 'tourfic' ) . "<span class='fa fa-angle-down'></span></a><a href='#' class='see-less btn-link'>" . esc_html__( 'See Less', 'tourfic' ) . "<span class='fa fa-angle-up'></span></a></div>";

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
        $title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Popular Filters', 'tourfic' );
        $terms = isset( $instance['terms']) && is_array( $instance['terms'] ) ? implode( ',', $instance['terms'] ) : 'all';
        $show_count = isset( $instance['show_count'] ) ? $instance['show_count'] : '';
        $hide_empty = isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : '';

        ?>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'tourfic' );?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <p class="tf-widget-field">
            <label for="<?php echo esc_attr($this->get_field_id( 'terms' )); ?>"><?php esc_html_e( 'Select Terms:', 'tourfic' )?></label>
            <br>
            <?php
            wp_dropdown_categories( array(
                'taxonomy'     => array( 'tour_attraction' ),
                'hierarchical' => false,
                'name'         => $this->get_field_name( 'terms' ),
                'id'           => $this->get_field_id( 'terms' ),
                'selected'     => $terms,  // e.x 86,110,786
                'class'        => 'widefat',
                'show_count'   => true,
                'multiple' => true
            ) );
        ?>
            <br>
            <span>Leave this field empty if you want to show all terms.</span>
        </p>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr($this->get_field_id( 'show_count' )); ?>"><?php esc_html_e( 'Show Count:', 'tourfic' )?></label>
            <input id="<?php echo esc_attr($this->get_field_id( 'show_count' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show_count' )); ?>" type="checkbox" <?php checked( 'on', $show_count );?>>
        </p>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr($this->get_field_id( 'hide_empty' )); ?>"><?php esc_html_e( 'Hide Empty Categories:', 'tourfic' )?></label>
            <input id="<?php echo esc_attr($this->get_field_id( 'hide_empty' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'hide_empty' )); ?>" type="checkbox" <?php checked( 'on', $hide_empty );?>>
        </p>
        <style>
            .tf-widget-field label {
                font-weight: 600;
            }
        </style>
        
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
        $instance['terms'] = ( !empty( $new_instance['terms'] ) ) ? $new_instance['terms'] : 'all';
        $instance['show_count'] = ( !empty( $new_instance['show_count'] ) ) ? wp_strip_all_tags( $new_instance['show_count'] ) : '';
        $instance['hide_empty'] = ( !empty( $new_instance['hide_empty'] ) ) ? wp_strip_all_tags( $new_instance['hide_empty'] ) : '';

        return $instance;
    }

}