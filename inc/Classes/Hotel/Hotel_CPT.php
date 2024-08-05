<?php

namespace Tourfic\Classes\Hotel;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Room;
use \Tourfic\Admin\Menu_Icon;

defined( 'ABSPATH' ) || exit;

class Hotel_CPT extends \Tourfic\Core\Post_Type {

	use \Tourfic\Traits\Singleton;

	/**
	 * Initialize custom post type
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		$this->set_post_args( array(
			'name'          => esc_html__( 'Hotels', 'tourfic' ),
			'singular_name' => esc_html__( 'Hotel', 'tourfic' ),
			'slug'          => 'tf_hotel',
			'menu_icon'     => Menu_Icon::$hotel_icon,
			'menu_position' => 26.2,
			'supports'      => apply_filters( 'tf_hotel_supports', array( 'title', 'editor', 'thumbnail', 'comments', 'author' ) ),
			'capability'    => array( 'tf_hotel', 'tf_hotels' ),
			'rewrite_slug'  => $this->get_hotel_slug(),
		) )->set_tax_args( array(
			array(
				'name'          => esc_html__( 'Locations', 'tourfic' ),
				'singular_name' => esc_html__( 'Location', 'tourfic' ),
				'taxonomy'      => 'hotel_location',
				'rewrite_slug'  => apply_filters( 'tf_hotel_location_slug', 'hotel-location' ),
				'capability'    => array(
					'assign_terms' => 'edit_tf_hotel',
					'edit_terms'   => 'edit_tf_hotel',
				),
			),
			array(
				'name'          => esc_html__( 'Features', 'tourfic' ),
				'singular_name' => esc_html__( 'Feature', 'tourfic' ),
				'taxonomy'      => 'hotel_feature',
				'rewrite_slug'  => apply_filters( 'tf_hotel_feature_slug', 'hotel-feature' ),
				'capability'    => array(
					'assign_terms' => 'edit_tf_hotel',
					'edit_terms'   => 'edit_tf_hotel',
				),
			),
			array(
				'name'          => esc_html__( 'Types', 'tourfic' ),
				'singular_name' => esc_html__( 'Type', 'tourfic' ),
				'taxonomy'      => 'hotel_type',
				'rewrite_slug'  => apply_filters( 'tf_hotel_type_slug', 'hotel-type' ),
				'capability'    => array(
					'assign_terms' => 'edit_tf_hotel',
					'edit_terms'   => 'edit_tf_hotel',
				),
			)
		) );

		add_action( 'init', array( $this, 'tf_post_type_taxonomy_register' ) );

		add_filter( 'manage_edit-tf_hotel_columns', array( $this, 'tf_hotel_list_column' ) );
		add_action( 'manage_tf_hotel_posts_custom_column', array( $this, 'tf_hotel_list_column_value' ), 10, 2 );
	}

	private function get_hotel_slug() {
		$tf_hotel_setting_permalink_slug = ! empty( Helper::tfopt( 'hotel-permalink-setting' ) ) ? Helper::tfopt( 'hotel-permalink-setting' ) : "hotels";

		update_option( "hotel_slug", $tf_hotel_setting_permalink_slug );

		return apply_filters( 'tf_hotel_slug', get_option( "hotel_slug" ) );
	}

	function tf_hotel_list_column( $columns ) {
		$date   = $columns['date'];
		$author = $columns['author'];
		$comments = $columns['comments'];
		unset( $columns['date'] );
		unset( $columns['author'] );
		unset( $columns['comments'] );
		$columns["rooms"] = esc_html__('Rooms', 'tourfic');
		$columns['author']   = $author;
		$columns['comments'] = $comments;
		$columns['date']     = $date;

		return $columns;
	}

	function tf_hotel_list_column_value( $colname, $post_id ) {

		if ( $colname == 'rooms' ) {
			$rooms       = Room::get_hotel_rooms($post_id);
			if(!empty($rooms)){
				echo '<ul style="margin: 0">';
				foreach ($rooms as $room) {
					echo '<li><a href="' . admin_url() . 'post.php?post=' . $room->ID . '&action=edit" target="_blank">' . $room->post_title . '</a></li>';
				}
				echo '</ul>';
			}
		}

	}
}
