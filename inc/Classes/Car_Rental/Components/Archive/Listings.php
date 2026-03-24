<?php

namespace Tourfic\Classes\Car_Rental\Components\Archive;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

/**
 * Centralized Car_Rental archive listing renderer.
 * Other builders and template files should call the methods here so
 * markup is maintained in a single place.
 */
class Listings {

	/**
	 * Render the Design-1 Car_Rental archive listing markup.
	 * @param \WP_Query|null $query Optional WP_Query instance. If null, global wp_query is used.
	 * @param array $settings Optional settings array (from widgets).
	 * @param string $builder Optional builder type (from widgets).
	 */
	public static function render_design_1( $query = null, $settings = [], $builder = '' ) {
        if ( ! $query || ! ( $query instanceof \WP_Query ) ) {
            global $wp_query;
            $query = $wp_query;
        }

        $post_count        = $query->post_count;
        $tf_defult_views   = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['car_archive_view'] )
            ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['car_archive_view']
            : 'grid';

        $show_total_result     = Helper::get_switcher_value( $settings, 'show_total_result', 'yes', $builder );
        $show_sidebar          = Helper::get_switcher_value( $settings, 'show_sidebar', 'yes', $builder );
        $listing_layout_toggle = Helper::get_switcher_value( $settings, 'listing_layout_toggle', 'yes', $builder );
        $show_pagination       = Helper::get_switcher_value( $settings, 'show_pagination', 'yes', $builder );

        $grid_column = isset( $settings['grid_column'] ) ? absint( $settings['grid_column'] ) : 2;

        if ( 'yes' === $listing_layout_toggle ) {
            $listing_layout = isset( $settings['listing_default_layout'] ) ? $settings['listing_default_layout'] : $tf_defult_views;
        } else {
            $listing_layout = isset( $settings['listing_layout'] ) ? $settings['listing_layout'] : $tf_defult_views;
        }

        $pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
        $pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';

        $tf_car_search_context = function_exists( 'tf_get_car_archive_search_context' ) ? tf_get_car_archive_search_context() : array(
            'pickup'       => '',
            'dropoff'      => '',
            'pickup_date'  => '',
            'dropoff_date' => '',
            'pickup_time'  => '',
            'dropoff_time' => '',
        );
        ?>
        <div class="tf-archive-header tf-flex tf-flex-space-bttn tf-flex-align-center tf-mb-30">
            <?php if ( 'yes' === $listing_layout_toggle ) : ?>
                <div class="tf-archive-view">
                    <ul class="tf-flex tf-flex-gap-16">
                        <li class="<?php echo 'grid' === $listing_layout ? esc_attr( 'active' ) : ''; ?>" data-view="grid"><i class="ri-layout-grid-line"></i></li>
                        <li class="<?php echo 'list' === $listing_layout ? esc_attr( 'active' ) : ''; ?>" data-view="list"><i class="ri-list-check"></i></li>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ( 'yes' === $show_total_result ) : ?>
                <div class="tf-total-result-bar">
                    <span><?php echo esc_html__( 'Total Results ', 'tourfic' ); ?></span>
                    <span><?php echo ' ('; ?></span>
                    <div class="tf-total-results">
                        <span><?php echo esc_html( $post_count ); ?></span>
                    </div>
                    <span><?php echo ')'; ?></span>

                    <?php if ( 'yes' === $show_sidebar ) : ?>
                        <div class="tf-archive-filter-showing">
                            <i class="ri-equalizer-line"></i>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="tf-car-details-column tf-flex tf-flex-gap-32">
            <?php if ( 'yes' === $show_sidebar ) : ?>
                <div class="tf-car-archive-sidebar">
                    <div class="tf-sidebar-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                        <div class="tf-close-sidebar">
                            <i class="fa-solid fa-xmark"></i>
                        </div>
                        <h4><?php esc_html_e( 'Filter', 'tourfic' ); ?></h4>
                        <button class="filter-reset-btn"><?php esc_html_e( 'Reset', 'tourfic' ); ?></button>
                    </div>

                    <?php if ( is_active_sidebar( 'tf_archive_booking_sidebar' ) ) : ?>
                        <?php dynamic_sidebar( 'tf_archive_booking_sidebar' ); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="tf-car-archive-result">
                <?php do_action( 'tf_car_archive_card_items_before' ); ?>

                <div class="tf-car-result archive_ajax_result tf-flex tf-flex-gap-32 <?php echo 'list' === $listing_layout ? esc_attr( 'list-view' ) : esc_attr( 'grid-view' ); ?> tf-grid-<?php echo esc_attr( $grid_column ); ?>">
                    <?php
                    if ( $query->have_posts() ) {
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            $car_meta        = get_post_meta( get_the_ID(), 'tf_carrental_opt', true );
                            $is_car_featured = is_array( $car_meta ) && ! empty( $car_meta['car_as_featured'] );

                            if ( $is_car_featured ) {
                                tf_car_archive_single_item(
                                    $tf_car_search_context['pickup'],
                                    $tf_car_search_context['dropoff'],
                                    $tf_car_search_context['pickup_date'],
                                    $tf_car_search_context['dropoff_date'],
                                    $tf_car_search_context['pickup_time'],
                                    $tf_car_search_context['dropoff_time'],
                                    $settings
                                );
                            }
                        }

                        $query->rewind_posts();

                        while ( $query->have_posts() ) {
                            $query->the_post();
                            $car_meta        = get_post_meta( get_the_ID(), 'tf_carrental_opt', true );
                            $is_car_featured = is_array( $car_meta ) && ! empty( $car_meta['car_as_featured'] );

                            if ( ! $is_car_featured ) {
                                tf_car_archive_single_item(
                                    $tf_car_search_context['pickup'],
                                    $tf_car_search_context['dropoff'],
                                    $tf_car_search_context['pickup_date'],
                                    $tf_car_search_context['dropoff_date'],
                                    $tf_car_search_context['pickup_time'],
                                    $tf_car_search_context['dropoff_time'],
                                    $settings
                                );
                            }
                        }
                    } else {
                        echo '<div class="tf-nothing-found" data-post-count="0" >' . esc_html__( 'No Cars Found!', 'tourfic' ) . '</div>';
                    }

                    wp_reset_postdata();
                    ?>

                    <?php if ( 'yes' === $show_pagination ) : ?>
                        <div class="tf-pagination-bar">
                            <?php Helper::tourfic_posts_navigation( $query, $pagination_prev_label, $pagination_next_label ); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php do_action( 'tf_car_archive_card_items_after' ); ?>
            </div>
        </div>
        <?php
    }
}

