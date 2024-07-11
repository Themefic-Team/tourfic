<?php

namespace Tourfic\App\Widgets\TF_Widgets;

// Exit if accessed directly.
defined('ABSPATH') || exit;


/**
 * Hotel & Tour Price Filter
 */
class Price_Filter extends \WP_Widget {

    use \Tourfic\Traits\Singleton;

    /**
     * Register widget with WordPress.
     */
    public function __construct() {

        parent::__construct(
            'tf_price_filters', // Base ID
            esc_html__( 'Tourfic - Hotel, Tour & Apartment Price Range Filter', 'tourfic' ), // Name
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
            $title = !empty($instance['title']) ? apply_filters( 'widget_title', $instance['title'] ) : '';
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