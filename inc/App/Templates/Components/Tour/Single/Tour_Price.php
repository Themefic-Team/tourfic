<?php

namespace Tourfic\App\Templates\Components\Tour\Single;

use Tourfic\Classes\Tour\Pricing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global Single Tour Price Component
 * Shared markup for Elementor and Bricks Tour Price widgets
 */
class Tour_Price {

	/**
	 * Static render method for Tour Price component
	 *
	 * @param array  $settings Settings from widget
	 * @param string $builder Builder type (elementor or bricks)
	 *
	 * @return void
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_id   = get_the_ID();
		$post_type = get_post_type();

		if ( 'tf_tours' !== $post_type ) {
			return;
		}

		$meta = get_post_meta( $post_id, 'tf_tours_opt', true );

		$tf_booking_type      = ! empty( $meta['booking-by'] ) ? $meta['booking-by'] : 1;
		$tf_hide_price        = ! empty( $meta['hide_price'] ) ? $meta['hide_price'] : '';
		$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$disable_adult        = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child        = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$disable_infant       = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;
		$wrapper_open = ! empty( $settings['wrapper_open'] ) ? $settings['wrapper_open'] : '';
		$wrapper_close = ! empty( $settings['wrapper_close'] ) ? $settings['wrapper_close'] : '';

		$avail_prices = Pricing::instance( $post_id )->get_avail_price();

        echo ! empty( $wrapper_open ) ? wp_kses_post( $wrapper_open ) : '';
        if(($tf_booking_type == 2 && $tf_hide_price !== '1') || $tf_booking_type == 1 || $tf_booking_type == 3) : ?>
            <div class="tf-single-tour-pricing">
                <?php if ( $pricing_rule == 'group' ) { ?>

                    <div class="tf-price group-price">
                        <span class="sale-price">
                            <?php echo wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['group_price']))) ?>
                        </span>
                        <?php echo ( !empty($avail_prices['sale_group_price']) ) ? '<del>' . wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['sale_group_price']))) . '</del>' : ''; ?>
                    </div>

                <?php } elseif ( $pricing_rule == 'person' ) { ?>

                    <?php if ( ! $disable_adult && ! empty( $avail_prices['adult_price'] ) ) { ?>

                        <div class="tf-price adult-price">
                            <span class="sale-price">
                                <?php echo wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['adult_price']))); ?>
                            </span>
                            <?php echo ( !empty($avail_prices['sale_adult_price']) ) ? '<del>' . wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['sale_adult_price']))) . '</del>' : ''; ?>
                        </div>

                    <?php }
                    if ( ! $disable_child && ! empty( $avail_prices['child_price'] ) ) { ?>

                        <div class="tf-price child-price tf-d-n">
                            <span class="sale-price">
                                <?php echo wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['child_price']))); ?>
                            </span>
                            <?php echo ( !empty($avail_prices['sale_child_price']) ) ? '<del>' . wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['sale_child_price']))) . '</del>' : ''; ?>
                        </div>

                <?php }
                if ( !$disable_adult && (! $disable_infant && ! empty( $avail_prices['infant_price'] )) ) { ?>

                        <div class="tf-price infant-price tf-d-n">
                            <span class="sale-price">
                                <?php echo wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['infant_price']))); ?>
                            </span>
                            <?php echo ( !empty($avail_prices['sale_infant_price']) ) ? '<del>' . wp_kses_post(wp_strip_all_tags(wc_price($avail_prices['sale_infant_price']))) . '</del>' : ''; ?>
                        </div>

                    <?php } ?>
                    <?php
                }
                ?>
                <ul class="tf-price-tab">
                    <?php
                    if ( $pricing_rule == 'group' ) {

                        echo '<li id="group" class="active">' . esc_html__( "Group", "tourfic" ) . '</li>';

                    } elseif ( $pricing_rule == 'person' ) {

                    if ( ! $disable_adult && ! empty( $avail_prices['adult_price'] ) ) {
                        echo '<li id="adult" class="active">' . esc_html__( "Adult", "tourfic" ) . '</li>';
                    }
                    if ( ! $disable_child && ! empty( $avail_prices['child_price'] ) ) {
                        echo '<li id="child">' . esc_html__( "Child", "tourfic" ) . '</li>';
                    }
                    if ( !$disable_adult && (! $disable_infant && ! empty( $avail_prices['infant_price'] )) ) {
                        echo '<li id="infant">' . esc_html__( "Infant", "tourfic" ) . '</li>';
                    }

                    }
                    ?>
                </ul>
            </div>
        <?php endif;
        echo ! empty( $wrapper_close ) ? wp_kses_post( $wrapper_close ) : '';
	}
}
