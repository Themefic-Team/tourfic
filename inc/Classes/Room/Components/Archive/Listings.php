<?php

namespace Tourfic\Classes\Room\Components\Archive;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Room;

/**
 * Centralized room archive listing renderer.
 * Other builders and template files should call the methods here so
 * markup is maintained in a single place.
 */
class Listings {

	/**
	 * Render the Design-1 room archive listing markup.
	 * @param \WP_Query|null $query Optional WP_Query instance. If null, global wp_query is used.
	 * @param array $settings Optional settings array (from widgets).
	 * @param string $builder Optional builder type (from widgets).
	 */
	public static function render_design_1( $query = null, $settings = [], $builder= '' ) {
		if ( ! $query || ! ( $query instanceof \WP_Query ) ) {
			global $wp_query;
			$query = $wp_query;
		}

        $post_count = $query->post_count;

        // if($builder == 'elementor'){
        $show_total_result = Helper::get_switcher_value( $settings, 'show_total_result', 'yes', $builder );
        $show_sorting = Helper::get_switcher_value( $settings, 'show_sorting', 'yes', $builder );
        $show_sidebar = Helper::get_switcher_value( $settings, 'show_sidebar', 'yes', $builder );
        $show_pagination = Helper::get_switcher_value( $settings, 'show_pagination', 'yes', $builder );
		$pagination_prev_label = isset( $settings['pagination_prev_label'] ) ? $settings['pagination_prev_label'] : '';
		$pagination_next_label = isset( $settings['pagination_next_label'] ) ? $settings['pagination_next_label'] : '';
        // }
        ?>
        <div class="tf-archive-room-details">
            <?php echo empty($builder) ? '<div class="tf-container">' : ''; ?>
            <div class="tf-archive-header tf-flex tf-flex-space-bttn tf-flex-align-center">
                <?php if($show_total_result == 'yes') : ?>
                    <h3 class="tf-total-results">
                        <?php 
                        /* translators: %s: number of rooms */ 
                        printf( esc_html__( 'Total %s rooms available', 'tourfic' ), '<span>' . esc_html( $post_count ) . '</span>' ); 
                        ?>
                    </h3>
                <?php endif; ?>

                <div class="tf-archive-header-right tf-flex tf-flex-space-bttn tf-flex-align-center tf-flex-gap-16">
                    <?php if($show_sorting == 'yes') : ?>
                        <form class="tf-archive-ordering" method="get">
                            <select class="tf-orderby tf-room-archive-action-btn" name="tf-orderby" id="tf-orderby">
                                <option value="default"><?php echo esc_html__( 'Default Sorting', 'tourfic' ); ?></option>
                                <option value="rating"><?php echo esc_html__( 'Sort By Average Rating', 'tourfic' ); ?></option>
                                <option value="latest"><?php echo esc_html__( 'Sort By Latest', 'tourfic' ); ?></option>
                                <option value="price-high"><?php echo esc_html__( 'Sort By Price: High to Low', 'tourfic' ); ?></option>
                                <option value="price-low"><?php echo esc_html__( 'Sort By Price: Low to High', 'tourfic' ); ?></option>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </form>
                    <?php endif; ?>

                    <?php if($show_sidebar == 'yes') : ?>
                        <div class="tf-room-archive-action-btn tf-archive-filter-btn">
                            <i class="ri-equalizer-line"></i>
                            <span><?php esc_html_e("All Filter", "tourfic"); ?></span>
                        </div>
                        <div class="tf-archive-filter">
                            <div class="tf-archive-filter-sidebar">
                                <div class="tf-filter-wrapper">
                                    <div class="tf-filter-title">
                                        <h4 class="tf-section-title"><?php echo esc_html__("Filter", "tourfic"); ?></h4>
                                        <button class="filter-reset-btn"><?php echo esc_html__("Reset", "tourfic"); ?></button>
                                    </div>
                                    <?php if (is_active_sidebar('tf_archive_booking_sidebar')) { ?>
                                        <div id="tf__booking_sidebar">
                                            <?php dynamic_sidebar('tf_archive_booking_sidebar'); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tf-room-archive-result">
                <?php do_action("tf_room_archive_roomd_items_before"); ?>
                <div class="tf-room-item-cards tf-flex tf-room-result archive_ajax_result">
                    <?php
                    if ($query->have_posts()) {
                        while ($query->have_posts()) {
                            $query->the_post();
                            Room::tf_room_archive_single_item('', '', '', '', '', '', $settings);
                        }
                    } else {
                        echo '<div class="tf-nothing-found" data-post-count="0" >' . esc_html__("No Rooms Found!", "tourfic") . '</div>';
                    }
                    ?>

                    <?php if($show_pagination == 'yes') : ?>
                        <div class="tf-pagination-bar">
                            <?php Helper::tourfic_posts_navigation($query, $pagination_prev_label, $pagination_next_label); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php do_action("tf_room_archive_roomd_items_after"); ?>
            </div>
            <?php echo empty($builder) ? '</div>' : ''; ?>
        </div>
        <?php
	}
}

