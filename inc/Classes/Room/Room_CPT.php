<?php

namespace Tourfic\Classes\Room;

use Tourfic\Classes\Helper;
use Tourfic\Admin\Menu_Icon;

defined( 'ABSPATH' ) || exit;

class Room_CPT extends \Tourfic\Core\Post_Type {

	use \Tourfic\Traits\Singleton;

	/**
	 * Initialize custom post type
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->set_post_args( array(
			'name'          => esc_html__( 'Rooms', 'tourfic' ),
			'singular_name' => esc_html__( 'Room', 'tourfic' ),
			'slug'          => 'tf_room',
			'menu_icon'     => Menu_Icon::$room_icon,
			'menu_position' => 26.3,
			'supports'      => apply_filters( 'tf_room_supports', array( 'title', 'editor', 'thumbnail', 'author' ) ),
			'capability'    => array( 'tf_room', 'tf_rooms' ),
			'rewrite_slug'  => $this->get_room_slug(),
		) )->set_tax_args( array(
			array(
				'name'          => esc_html__( 'Types', 'tourfic' ),
				'singular_name' => esc_html__( 'Type', 'tourfic' ),
				'taxonomy'      => 'room_type',
				'rewrite_slug'  => apply_filters( 'tf_room_type_slug', 'room-type' ),
				'capability'    => array(
					'assign_terms' => 'edit_tf_room',
					'edit_terms'   => 'edit_tf_room',
				),
				'show_in_menu' => true
			)
		) );

		add_action( 'init', array( $this, 'tf_post_type_taxonomy_register' ) );

		add_filter( 'manage_edit-tf_room_columns', array( $this, 'tf_room_list_column' ) );
		add_action( 'manage_tf_room_posts_custom_column', array( $this, 'tf_room_list_column_value' ), 10, 2 );
	}

	function tf_room_list_column( $columns ) {
		$date   = $columns['date'];
		$author = $columns['author'];
		$comments = $columns['comments'];
		unset( $columns['date'] );
		unset( $columns['author'] );
		unset( $columns['comments'] );
		$columns["hotel_id"] = esc_html__('Hotel', 'tourfic');
		$columns['author']   = $author;
		$columns['comments'] = $comments;
		$columns['date']     = $date;

		return $columns;
	}

	function tf_room_list_column_value( $colname, $post_id ) {

		if ( $colname == 'hotel_id' ) {
			$meta       = get_post_meta( $post_id, 'tf_room_opt', true );
			$hotel_name = ! empty( $meta['tf_hotel'] ) ? esc_html( get_the_title( $meta['tf_hotel'] ) ) : '';
			if ( ! empty( $hotel_name ) ) {
				echo '<a href="' . esc_url(admin_url()) . 'post.php?post=' . esc_attr($meta['tf_hotel']) . '&action=edit" target="_blank">' . esc_html($hotel_name) . '</a>';
			}
		}

	}

	private function get_room_slug() {
		$tf_room_setting_permalink_slug = ! empty( Helper::tfopt( 'room-permalink-setting' ) ) ? Helper::tfopt( 'room-permalink-setting' ) : "rooms";

		update_option( "room_slug", $tf_room_setting_permalink_slug );

		return apply_filters( 'tf_room_slug', get_option( 'room_slug' ) );
	}
}
