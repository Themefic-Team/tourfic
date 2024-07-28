<?php

use Tourfic\Classes\Room\Room;

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
/**
 *
 * Field: hotel_room
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'TF_hotel_room' ) ) {
	class TF_hotel_room extends TF_Fields {
		public function __construct( $field, $value = '', $settings_id = '', $parent_field = '' ) {
			parent::__construct( $field, $value, $settings_id, $parent_field );
		}

		public function render() {
			$rooms = Room::get_hotel_rooms( get_the_ID() );
			?>
            <div class="tf-hotel-room <?php echo esc_attr( $this->field['id'] ); ?>">
				<?php if ( ! empty( $rooms ) && is_array( $rooms ) ):
					foreach ( $rooms as $room ) :
						?>
                        <div class="tf-single-room">
                            <span class="tf-room-title"><?php echo ! empty( get_the_title( $room->ID ) ) ? esc_html( get_the_title( $room->ID ) ) : esc_html__( 'Room', 'tourfic' ); ?></span>
                            <a target="_blank" href="<?php echo esc_url( get_edit_post_link( $room->ID ) ); ?>" class="tf-edit-room">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                        </div>
					<?php endforeach;
				endif; ?>
                <a href="<?php echo esc_url(admin_url('post-new.php?post_type=tf_room')); ?>" target="_blank" class="tf-admin-btn tf-btn-secondary"><?php echo esc_html__('Add new room', 'tourfic') ?></a>
            </div>
			<?php
		}

		public function sanitize() {
			return $this->value;
		}
	}
}
