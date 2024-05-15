<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

/**
 * Hotel filter by features
 *
 * Works only for hotel
 */
class TF_Hotel_Feature_Filter extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_hotel_filter', // Base ID
            esc_html__( 'Tourfic - Hotels Filters by Feature', 'tourfic' ),
            array( 'description' => esc_html__( 'Filter search result by hotel feature', 'tourfic' ) ) // Args
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

        if ( $posttype == 'tf_hotel' ) {
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
                'taxonomy'   => 'hotel_feature',
                'include'    => $terms,
            );

            $get_terms = get_terms( $taxonomy );

            $destination_name = !empty( $_GET['destination'] ) ? $_GET['destination'] : '';
            $search_features_query = !empty($_GET['features']) ? $_GET['features'] : array();
            echo "<div class='tf-filter'><ul>";
            foreach ( $get_terms as $key => $term ) {
                $id = $term->term_id;
                $name = $term->name;
                $fslug = $term->slug;
                $default_count = $term->count;
                $count = $show_count ? '<span>' . tf_term_count( $term->slug, $destination_name, $default_count ) . '</span>' : '';
                $defult_select =  in_array($fslug, $search_features_query) ? 'checked' : '';
                echo wp_kses("<li class='filter-item'><label><input type='checkbox' name='tf_filters[]' value='{$id}' {$defult_select}/><span class='checkmark'></span> {$name}</label> {$count}</li>", tf_custom_wp_kses_allow_tags() );
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
                'taxonomy'     => 'hotel_feature',
                'hierarchical' => false,
                'name'       => $this->get_field_name( 'terms' ),
                'id'         => $this->get_field_id( 'terms' ),
                'selected'   => $terms, // e.x 86,110,786
                'multiple'   => true,
                'class'      => 'widefat tf-select2',
                'show_count' => true
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
        <script>
            jQuery('#<?php echo esc_attr($this->get_field_id( 'terms' )); ?>').select2({
                width: '100%'
            });
            jQuery(document).trigger('tf_select2');
        </script>
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
        $instance['terms'] = (!empty($new_instance['terms'])) ? $new_instance['terms'] : 'all';
        $instance['show_count'] = ( !empty( $new_instance['show_count'] ) ) ? wp_strip_all_tags( $new_instance['show_count'] ) : '';
        $instance['hide_empty'] = ( !empty( $new_instance['hide_empty'] ) ) ? wp_strip_all_tags( $new_instance['hide_empty'] ) : '';

        return $instance;
    }
}

/**
 * Hotel filter by type
 *
 * Works only for hotel
 * @author Foysal
 */
class TF_Hotel_Type_Filter extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_hotel_type_filter', // Base ID
            esc_html__( 'Tourfic - Hotels Filters by Type', 'tourfic' ),
            array( 'description' => esc_html__( 'Filter search result by hotel type', 'tourfic' ) ) // Args
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

        if ( $posttype == 'tf_hotel' ) {
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
                'taxonomy'   => 'hotel_type',
                'include'    => $terms,
            );

            $get_terms = get_terms( $taxonomy );

            $destination_name = !empty( $_GET['destination'] ) ? $_GET['destination'] : '';
            $search_types_query = !empty($_GET['types']) ? $_GET['types'] : array();
            echo "<div class='tf-filter'><ul>";
            foreach ( $get_terms as $key => $term ) {
                $id = $term->term_id;
                $name = $term->name;
                $fslug = $term->slug;
                $default_count = $term->count;
                $count = $show_count ? '<span>' . tf_term_count( $term->slug, $destination_name, $default_count ) . '</span>' : '';
                $defult_select =  in_array($fslug, $search_types_query) ? 'checked' : '';
                echo wp_kses("<li class='filter-item'><label><input type='checkbox' name='tf_hotel_types[]' value='{$id}' {$defult_select}/><span class='checkmark'></span> {$name}</label> {$count}</li>", tf_custom_wp_kses_allow_tags());
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

        $title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Popular Types', 'tourfic' );
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
                'taxonomy'     => 'hotel_type',
                'hierarchical' => false,
                'name'       => $this->get_field_name( 'terms' ),
                'id'         => $this->get_field_id( 'terms' ),
                'selected'   => $terms, // e.x 86,110,786
                'multiple'   => true,
                'class'      => 'widefat tf-select2',
                'show_count' => true
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
        <script>
            jQuery('#<?php echo esc_attr($this->get_field_id( 'terms' )); ?>').select2({
                width: '100%'
            });
            jQuery(document).trigger('tf_select2');
        </script>
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
        $instance['terms'] = (!empty($new_instance['terms'])) ? $new_instance['terms'] : 'all';
        $instance['show_count'] = ( !empty( $new_instance['show_count'] ) ) ? wp_strip_all_tags( $new_instance['show_count'] ) : '';
        $instance['hide_empty'] = ( !empty( $new_instance['hide_empty'] ) ) ? wp_strip_all_tags( $new_instance['hide_empty'] ) : '';

        return $instance;
    }
}

/**
 * Tour filter by features
 *
 * Works only for Tour
 */
class TF_Tour_Feature_Filter extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_tour_feature_filter', // Base ID
            esc_html__( 'Tourfic - Tours Filters by Feature', 'tourfic' ),
            array( 'description' => esc_html__( 'Filter search result by tour feature', 'tourfic' ) ) // Args
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
        $posttype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();

        if ( $posttype == 'tf_tours' ) {
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
                'taxonomy'   => 'tour_features',
                'include'    => $terms,
            );

            $get_terms = get_terms( $taxonomy );

            echo "<div class='tf-filter'><ul>";
            foreach ( $get_terms as $key => $term ) {
                $id = $term->term_id;
                $name = $term->name;
                $default_count = $term->count;
                $count = $show_count ? '<span>(' . $default_count . ')</span>' : '';

                echo wp_kses("<li class='filter-item'><label><input type='checkbox' name='tour_features[]' value='{$id}'/><span class='checkmark'></span> {$name}</label> {$count}</li>", tf_custom_wp_kses_allow_tags());
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

        $title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Feature Filters', 'tourfic' );
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
                'taxonomy'     => 'tour_features',
                'hierarchical' => false,
                'name'       => $this->get_field_name( 'terms' ),
                'id'         => $this->get_field_id( 'terms' ),
                'selected'   => $terms, // e.x 86,110,786
                'multiple'   => true,
                'class'      => 'widefat tf-select2', // tf-select2
                'show_count' => true
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
        <script>
            jQuery('#<?php echo esc_attr($this->get_field_id( 'terms' )); ?>').select2({
                width: '100%'
            });
            jQuery(document).trigger('tf_select2');
        </script>
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
        $instance['terms'] = (!empty($new_instance['terms'])) ? $new_instance['terms'] : 'all';
        $instance['show_count'] = ( !empty( $new_instance['show_count'] ) ) ? wp_strip_all_tags( $new_instance['show_count'] ) : '';
        $instance['hide_empty'] = ( !empty( $new_instance['hide_empty'] ) ) ? wp_strip_all_tags( $new_instance['hide_empty'] ) : '';

        return $instance;
    }
}
/**
 * Tour filter by attraction
 * Works only for Tour
 * @author Abu Hena
 */
class TF_Tour_Attraction_Filter extends WP_Widget {
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

        if ( $posttype == 'tf_tours' ) {
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

                echo wp_kses("<li class='filter-item'><label><input type='checkbox' name='tf_attractions[]' value='{$id}'/><span class='checkmark'></span> {$name}</label> {$count}</li>", tf_custom_wp_kses_allow_tags() );
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
                'class'        => 'widefat tf-select2',
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

/**
 * Tour filter by activities
 * Works only for Tour
 * @author Abu Hena
 */
class TF_Tour_Activities_Filter extends WP_Widget {
    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_activities_filter', // Base ID
            esc_html__( 'Tourfic - Tour Filter By Activities', 'tourfic' ),
            array( 'description' => esc_html__( 'Filter search result by tour activities', 'tourfic' ) ) // Args
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

        if ( $posttype == 'tf_tours' ) {
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
                'taxonomy'   => 'tour_activities',
                'include'    => $terms,
            );

            $get_terms = get_terms( $taxonomy );
            echo "<div class='tf-filter'><ul>";

            foreach ( $get_terms as $key => $term ) {
                $id = $term->term_id;
                $name = $term->name;
                $default_count = $term->count;
                $count = $show_count ? '<span>(' . $default_count . ')</span>' : '';

                echo wp_kses("<li class='filter-item'><label><input type='checkbox' name='tf_activities[]' value='{$id}'/><span class='checkmark'></span> {$name}</label> {$count}</li>", tf_custom_wp_kses_allow_tags());
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
        $title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Popular Activities', 'tourfic' );
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
                    'taxonomy'     => array( 'tour_activities' ),
                    'hierarchical' => false,
                    'name'         => $this->get_field_name( 'terms' ),
                    'id'           => $this->get_field_id( 'terms' ),
                    'selected'     => $terms, // e.x 86,110,786
                    'multiple'     => true,
                    'class'        => 'widefat tf-select2',
                    'show_count'   => true
                ) );
            ?>
            <br>
            <span><?php echo esc_html__( 'Leave this field empty if you want to show all terms.', 'tourfic' ); ?></span>
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
        <script>
            jQuery('#<?php echo esc_attr($this->get_field_id( 'terms' )); ?>').select2({
                width: '100%'
            });
            jQuery(document).trigger('tf_select2');
        </script>
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

/**
 * Tour filter by type
 *
 * Works only for tour
 * @author Foysal
 */
class TF_Tour_Type_Filter extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		parent::__construct(
			'tf_tour_type_filter', // Base ID
			esc_html__( 'Tourfic - Tours Filters by Type', 'tourfic' ),
			array( 'description' => esc_html__( 'Filter search result by tour type', 'tourfic' ) ) // Args
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

		if ( $posttype == 'tf_tours' ) {
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
				'taxonomy'   => 'tour_type',
				'include'    => $terms,
			);

			$get_terms = get_terms( $taxonomy );

			$search_types_query = !empty($_GET['types']) ? $_GET['types'] : array();
			echo "<div class='tf-filter'><ul>";
			foreach ( $get_terms as $key => $term ) {
				$id = $term->term_id;
				$name = $term->name;
				$fslug = $term->slug;
				$default_count = $term->count;
				$count = $show_count ? '<span>(' . $default_count . ')</span>' : '';
				$defult_select =  in_array($fslug, $search_types_query) ? 'checked' : '';
				echo wp_kses("<li class='filter-item'><label><input type='checkbox' name='tf_tour_types[]' value='{$id}' {$defult_select}/><span class='checkmark'></span> {$name}</label> {$count}</li>", tf_custom_wp_kses_allow_tags() );
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

		$title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Popular Types', 'tourfic' );
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
				'taxonomy'     => 'tour_type',
				'hierarchical' => false,
				'name'       => $this->get_field_name( 'terms' ),
				'id'         => $this->get_field_id( 'terms' ),
				'selected'   => $terms, // e.x 86,110,786
				'multiple'   => true,
				'class'      => 'widefat tf-select2',
				'show_count' => true
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
        <script>
            jQuery('#<?php echo esc_attr($this->get_field_id( 'terms' )); ?>').select2({
                width: '100%'
            });
            jQuery(document).trigger('tf_select2');
        </script>
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
		$instance['terms'] = (!empty($new_instance['terms'])) ? $new_instance['terms'] : 'all';
		$instance['show_count'] = ( !empty( $new_instance['show_count'] ) ) ? wp_strip_all_tags( $new_instance['show_count'] ) : '';
		$instance['hide_empty'] = ( !empty( $new_instance['hide_empty'] ) ) ? wp_strip_all_tags( $new_instance['hide_empty'] ) : '';

		return $instance;
	}
}

/**
 * Apartment filter by features
 *
 * Works only for apartment
 * @author Foysal
 */
class TF_Apartment_Features_Filter extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		parent::__construct(
			'tf_apartment_features_filter', // Base ID
			esc_html__( 'Tourfic - Apartments Filters by Features', 'tourfic' ),
			array( 'description' => esc_html__( 'Filter search result by apartment features', 'tourfic' ) ) // Args
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

		//check if is Apartment
		$posttype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();

		if ( $posttype == 'tf_apartment' ) {
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
				'taxonomy'   => 'apartment_feature',
				'include'    => $terms,
			);

			$get_terms = get_terms( $taxonomy );


			$search_types_query = !empty($_GET['features']) ? $_GET['features'] : array();
			echo "<div class='tf-filter'><ul>";
			foreach ( $get_terms as $key => $term ) {
				$id = $term->term_id;
				$name = $term->name;
				$fslug = $term->slug;
				$default_count = $term->count;
				$count = $show_count ? '<span>' . ( $default_count ) . '</span>' : '';
				$defult_select =  in_array($fslug, $search_types_query) ? 'checked' : '';
				echo wp_kses("<li class='filter-item'><label><input type='checkbox' name='tf_apartment_features[]' value='{$id}' {$defult_select}/><span class='checkmark'></span> {$name}</label> {$count}</li>", tf_custom_wp_kses_allow_tags() );
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

		$title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Apartment Features', 'tourfic' );
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
				'taxonomy'     => 'apartment_feature',
				'hierarchical' => false,
				'name'       => $this->get_field_name( 'terms' ),
				'id'         => $this->get_field_id( 'terms' ),
				'selected'   => $terms, // e.x 86,110,786
				'multiple'   => true,
				'class'      => 'widefat tf-select2',
				'show_count' => true
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
        <script>
            jQuery('#<?php echo esc_attr($this->get_field_id( 'terms' )); ?>').select2({
                width: '100%'
            });
            jQuery(document).trigger('tf_select2');
        </script>
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
		$instance['terms'] = (!empty($new_instance['terms'])) ? $new_instance['terms'] : 'all';
		$instance['show_count'] = ( !empty( $new_instance['show_count'] ) ) ? wp_strip_all_tags( $new_instance['show_count'] ) : '';
		$instance['hide_empty'] = ( !empty( $new_instance['hide_empty'] ) ) ? wp_strip_all_tags( $new_instance['hide_empty'] ) : '';

		return $instance;
	}
}

/**
 * Apartment filter by type
 *
 * Works only for apartment
 * @author Foysal
 */
class TF_Apartment_Type_Filter extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {

		parent::__construct(
			'tf_apartment_type_filter', // Base ID
			esc_html__( 'Tourfic - Apartments Filters by Type', 'tourfic' ),
			array( 'description' => esc_html__( 'Filter search result by apartment type', 'tourfic' ) ) // Args
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

		//check if is Apartment
		$posttype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();

		if ( $posttype == 'tf_apartment' ) {
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
				'taxonomy'   => 'apartment_type',
				'include'    => $terms,
			);

			$get_terms = get_terms( $taxonomy );

			$search_types_query = !empty($_GET['types']) ? $_GET['types'] : array();
			echo "<div class='tf-filter'><ul>";
			foreach ( $get_terms as $key => $term ) {
				$id = $term->term_id;
				$name = $term->name;
				$fslug = $term->slug;
				$default_count = $term->count;
				$count = $show_count ? '<span>' . ( $default_count ) . '</span>' : '';
				$defult_select =  in_array($fslug, $search_types_query) ? 'checked' : '';
				echo wp_kses("<li class='filter-item'><label><input type='checkbox' name='tf_apartment_types[]' value='{$id}' {$defult_select}/><span class='checkmark'></span> {$name}</label> {$count}</li>", tf_custom_wp_kses_allow_tags() );
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

		$title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Apartment Types', 'tourfic' );
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
				'taxonomy'     => 'apartment_type',
				'hierarchical' => false,
				'name'       => $this->get_field_name( 'terms' ),
				'id'         => $this->get_field_id( 'terms' ),
				'selected'   => $terms, // e.x 86,110,786
				'multiple'   => true,
				'class'      => 'widefat tf-select2',
				'show_count' => true
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
        <script>
            jQuery('#<?php echo esc_attr($this->get_field_id( 'terms' )); ?>').select2({
                width: '100%'
            });
            jQuery(document).trigger('tf_select2');
        </script>
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
		$instance['terms'] = (!empty($new_instance['terms'])) ? $new_instance['terms'] : 'all';
		$instance['show_count'] = ( !empty( $new_instance['show_count'] ) ) ? wp_strip_all_tags( $new_instance['show_count'] ) : '';
		$instance['hide_empty'] = ( !empty( $new_instance['hide_empty'] ) ) ? wp_strip_all_tags( $new_instance['hide_empty'] ) : '';

		return $instance;
	}
}

/**
 * Similar Tours
 */
class Tourfic_Similar_Tours extends WP_Widget {

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
        $instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
        $instance['btn_label'] = ( !empty( $new_instance['btn_label'] ) ) ? wp_strip_all_tags( $new_instance['btn_label'] ) : '';

        return $instance;
    }

}


/**
 * Hotel & Tour Price Filter
 */
class Tourfic_Price_Filter extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_price_filters', // Base ID
            esc_html__( 'Tourfic - Hotel & Tour Price Range Filter', 'tourfic' ), // Name
            array( 'description' => esc_html__( 'Show Price Range slider on Archive/Search Result page.', 'tourfic' ) ) // Args
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
        ?>
		<!-- Start Price Range widget -->
		<?php 
        $tf_query_taxonomy = !empty( get_taxonomy(get_queried_object()) ) ? get_taxonomy(get_queried_object()->taxonomy)->object_type : '' ;
        if( is_post_type_archive('tf_tours') || is_post_type_archive('tf_hotel') || is_post_type_archive('tf_apartment') || ( !empty( $tf_query_taxonomy ) ) ){
            extract( $args );
            $title = apply_filters( 'widget_title', $instance['title'] );
            echo wp_kses_post($before_widget);
            if( is_post_type_archive('tf_hotel') ){
            ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Hotel Price Range","tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-hotel-result-price-range"></div>
            <?php
            } 
            if( is_post_type_archive('tf_tours') ){
            ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Tour Price Range","tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-tour-result-price-range"></div>
            <?php
            }
            if( is_post_type_archive('tf_apartment') ){
            ?>
            <div class="tf-widget-title">
                <span><?php esc_html_e("Apartment Price Range","tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
            </div>
            <div class="tf-apartment-result-price-range"></div>
            <?php 
            }
            if( !is_post_type_archive('tf_hotel') && !is_post_type_archive('tf_tours') && !is_post_type_archive('tf_apartment') && ( !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0]=="tf_hotel" ) ){
                ?>
                    <div class="tf-widget-title">
                        <span><?php esc_html_e("Hotel Price Range","tourfic"); ?></span> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)
                    </div>
                    <div class="tf-hotel-result-price-range"></div>
                <?php
            } 
            if( !is_post_type_archive('tf_hotel') && !is_post_type_archive('tf_tours') && !is_post_type_archive('tf_apartment') && ( !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0]=="tf_tours" ) ){
                ?>
                    <div class="tf-widget-title">
                        <span><?php esc_html_e("Tour Price Range","tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                    </div>
                    <div class="tf-tour-result-price-range"></div>
                <?php
            }
            if( !is_post_type_archive('tf_hotel') && !is_post_type_archive('tf_tours') && !is_post_type_archive('tf_apartment') && ( !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0]=="tf_apartment" ) ){
                ?>
                    <div class="tf-widget-title">
                        <span><?php esc_html_e("Apartment Price Range","tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                    </div>
                    <div class="tf-apartment-result-price-range"></div>
                <?php
            }
        }else{
            extract( $args );
            $title = apply_filters( 'widget_title', $instance['title'] );
            echo wp_kses_post($before_widget);
            if( !empty($_GET['type']) && $_GET['type']=="tf_tours" && !empty($_GET['from']) && !empty($_GET['to'] ) ){
            ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Tour Price Range","tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-tour-result-price-range"></div>
            <?php }
            if( !empty($_GET['type']) && $_GET['type']=="tf_hotel" && !empty($_GET['from']) && !empty($_GET['to'] ) ){
            ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Hotel Price Range","tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-hotel-result-price-range"></div>
            <?php }
            if( !empty($_GET['type']) && $_GET['type']=="tf_apartment" && !empty($_GET['from']) && !empty($_GET['to'] ) ){
            ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("Apartment Price Range","tourfic"); ?> (<?php echo wp_kses_post(get_woocommerce_currency_symbol()); ?>)</span>
                </div>
                <div class="tf-apartment-result-price-range"></div>
		<?php } } ?>
		<!-- End Price Range widget -->
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

        $title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Price Range Filter', 'tourfic' );
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
        $instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';

        return $instance;
    }

}

/**
 * Ask Question
 */
class Tourfic_Ask_Question extends WP_Widget {

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
        $instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
        $instance['btn_label'] = ( !empty( $new_instance['btn_label'] ) ) ? wp_strip_all_tags( $new_instance['btn_label'] ) : '';
        $instance['subtitle'] = ( !empty( $new_instance['subtitle'] ) ) ? wp_strip_all_tags( $new_instance['subtitle'] ) : '';

        return $instance;
    }

}

/**
 * Add Tourfic sidebar.
 */
function tourfic_sidebar_widgets_init() {

    register_sidebar( array(
        'name'          => esc_html__( 'TOURFIC: Archive Sidebar', 'tourfic' ),
        'id'            => 'tf_archive_booking_sidebar',
        'description'   => esc_html__( 'Widgets in this area will be shown on tourfic archive/search page', 'tourfic' ),
        'before_widget' => '<div id="%1$s" class="tf_widget widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="tf-widget-title"><span>',
        'after_title'   => '</span><i class="fa fa-angle-up"></i></div>',
    ) );

    register_sidebar( array(
        'name'          => esc_html__( 'Tourfic: Search Result Sidebar', 'tourfic' ),
        'id'            => 'tf_search_result',
        'description'   => esc_html__( 'Widgets in this area will be shown on tourfic search page', 'tourfic' ),
        'before_widget' => '<div id="%1$s" class="tf_widget widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="tf-widget-title"><span>',
        'after_title'   => '</span><i class="fa fa-angle-up"></i></div>',
    ) );

    // Register Custom Widgets
    $custom_widgets = array(
        'Tourfic_Ask_Question',
        'Tourfic_Similar_Tours',
        'TF_Hotel_Feature_Filter',
        'TF_Hotel_Type_Filter',
        'TF_Tour_Feature_Filter',
        'TF_Tour_Attraction_Filter',
        'TF_Tour_Activities_Filter',
        'TF_Tour_Type_Filter',
        'TF_Apartment_Features_Filter',
        'TF_Apartment_Type_Filter',
        'Tourfic_Price_Filter'
    );
    foreach ( $custom_widgets as $key => $widget ) {
        register_widget( $widget );
    }

}
add_action( 'widgets_init', 'tourfic_sidebar_widgets_init', 100 );
?>