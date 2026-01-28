<?php 
// Don't load directly

use Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Pricing;

defined( 'ABSPATH' ) || exit;
$tf_room_book_button_text = ! empty( Helper::tfopt( 'room_booking_button_text' ) ) ? stripslashes( sanitize_text_field( Helper::tfopt( 'room_booking_button_text' ) ) ) : esc_html__( 'Book Now', 'tourfic' );
$pricing_by = ! empty( $meta["pricing-by"] ) ? $meta["pricing-by"] : 1;
$unique_id  = ! empty( $meta['unique_id'] ) ? $meta['unique_id'] : '';

if ( $pricing_by == '3' && isset( $meta['room-options'] ) && ! empty( Helper::tf_data_types( $meta['room-options'] ) ) ):
    ?>
    <div class="tf-room-options" id="tf-room-options">
        <?php foreach ( $meta['room-options'] as $room_option_key => $room_option ): ?>
            <div class="tf-room-option">
                <div class="tf-room-option-left">
                    <h3><?php echo !empty($room_option['option_title']) ? esc_html($room_option['option_title']) : ''; ?></h3>
                    <?php if ( ! empty( $room_option['room-facilities'] ) ) :
                        echo '<ul class="tf-room-features">';
                        foreach ( $room_option['room-facilities'] as $room_facility ) :
                        ?>
                            <li>
                                <span class="room-extra-icon"><i class="<?php echo esc_attr( $room_facility['room_facilities_icon'] ); ?>"></i></span>
                                <span class="room-extra-label"><?php echo wp_kses_post( $room_facility['room_facilities_label'] ); ?></span>
                            </li>
                        <?php
                        endforeach;
                        echo '</ul>';
                    endif; ?>
                </div>

                <div class="tf-room-option-right">
                    <div class="tf-room-price-wrap">
                        <!-- Discount -->
                        <?php if ( ! empty( $min_discount_amount ) ) : ?>
                            <div class="tf-room-off">
                                <span><?php echo $min_discount_type == "percent" ? esc_html( $min_discount_amount ) . '%' : wp_kses_post( wc_price( $min_discount_amount ) ) ?><?php esc_html_e( " Off ", "tourfic" ); ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Price -->
                        <div class="tf-room-price"><?php Pricing::instance( $post_id )->get_per_price_html( $room_option_key, 'design-2' ); ?></div>
                    </div>

                    <!-- View Details -->
                    <a href="" class="tf_btn tf_btn_rounded tf_btn_large tf-room-option-book" data-option-key="<?php echo esc_attr($unique_id . '_' . $room_option_key); ?>"><?php echo esc_html( $tf_room_book_button_text ); ?></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>