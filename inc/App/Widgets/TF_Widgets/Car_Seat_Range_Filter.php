<?php

namespace Tourfic\App\Widgets\TF_Widgets;

// Exit if accessed directly.
defined('ABSPATH') || exit;


/**
 * Hotel & Tour Price Filter
 */
class Car_Seat_Range_Filter extends \WP_Widget {

    use \Tourfic\Traits\Singleton;

    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_seat_filters', // Base ID
            esc_html__( 'Tourfic - By Seat Range Filter', 'tourfic' ), // Name
            array( 'description' => esc_html__( 'Show Seat Range slider on Archive/Search Result page.', 'tourfic' ) ) // Args
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

        //check if is Car
        $posttype = isset( $_GET['type'] ) ? $_GET['type'] : get_post_type();

        if ( $posttype == 'tf_carrental' ) {
            extract( $args );
            echo wp_kses_post($before_widget);
            ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("By Seat","tourfic"); ?></span>
                </div>
                <div class="tf-car-result-seat-range"></div>
            <?php 
        }else{
            
            if( !empty($_GET['type']) && $_GET['type']=="tf_carrental" && !empty($_GET['from']) && !empty($_GET['to'] ) ){
                extract( $args );
            ?>
                <div class="tf-widget-title">
                    <span><?php esc_html_e("By Seat","tourfic"); ?></span>
                </div>
                <div class="tf-car-result-seat-range"></div>
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

        $title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'By Seat', 'tourfic' );
        ?>
        <p class="tf-widget-field">
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'tourfic' );?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
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

        return $instance;
    }

}