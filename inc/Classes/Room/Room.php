<?php

namespace Tourfic\Classes\Room;

defined( 'ABSPATH' ) || exit;
class Room {
	use \Tourfic\Traits\Singleton;

	static function get_hotel_rooms($hotel_id){
		$args = array(
			'post_type' => 'tf_room',
			'posts_per_page' => -1,
		);

		$rooms = get_posts($args);

		$hotel_rooms = array();
		foreach($rooms as $room){
			$room_meta = get_post_meta($room->ID, 'tf_room_opt', true);
			if(!empty($room_meta['tf_hotel']) && $room_meta['tf_hotel'] == $hotel_id){
				$hotel_rooms[] = $room;
			}
		}

		return $hotel_rooms;

	}


}