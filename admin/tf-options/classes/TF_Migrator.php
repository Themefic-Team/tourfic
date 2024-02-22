<?php 
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TF_Migrator' ) ) {
    class TF_Migrator{

        private static $instance = null;

		/**
		 * Singleton instance
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( self::$instance == null ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

        public function __construct() {
			add_action( 'admin_init', [ $this, 'tf_searchable_meta' ] );
			add_action( 'admin_init', [ $this, 'tf_hotel_room_migrate' ] );
		}

        public function tf_searchable_meta(){
            $tf_hotel_searchable_migration = !empty(get_option( 'tf_hotel_searchable_migration' )) ? get_option( 'tf_hotel_searchable_migration' ) : 0;
            $tf_tour_searchable_migration = !empty(get_option( 'tf_tour_searchable_migration' )) ? get_option( 'tf_tour_searchable_migration' ) : 0;
            $tf_apartment_searchable_migration = !empty(get_option( 'tf_apartment_searchable_migration' )) ? get_option( 'tf_apartment_searchable_migration' ) : 0;
            if ( $tf_hotel_searchable_migration < 1 ) {
                $this->regenerate_search_meta('tf_hotel');
                update_option( 'tf_hotel_searchable_migration', $tf_hotel_searchable_migration+1 );
            }
            if ( $tf_tour_searchable_migration < 1 ) {
                $this->regenerate_search_meta('tf_tours');
                update_option( 'tf_tour_searchable_migration', $tf_tour_searchable_migration+1 );
            }
            if ( $tf_apartment_searchable_migration < 1 ) {
                $this->regenerate_search_meta('tf_apartment');
                update_option( 'tf_apartment_searchable_migration', $tf_apartment_searchable_migration+1 );
            }
        }

        // Migrator Function
        function regenerate_search_meta($type){
            if("tf_hotel"==$type){

                $searchable_keys = [
                    'featured',
                    'map'
                ];
                $args = array(
                    'post_type' => 'tf_hotel',
                    'post_status' => 'publish',
                    'posts_per_page' => -1
                );
                $posts = new WP_Query( $args );
                if ( $posts -> have_posts() ) {
                    while ( $posts -> have_posts() ) {
                        $posts->the_post();
                        $post_id =  get_the_ID();
                        $meta = get_post_meta( $post_id, 'tf_hotels_opt', true );
                        if(!empty($searchable_keys)){
                            foreach($searchable_keys as $search){
                                $fields_values = !empty($meta[$search]) ? $meta[$search] : "";
                                update_post_meta( $post_id, 'tf_search_'.$search, $fields_values );
                            }
                        }
                    }
                }
                wp_reset_query();

            }elseif("tf_tours"==$type){
                $searchable_keys = [
                    'tour_as_featured',
                    'location',
                    'pricing',
                    'adult_price',
                    'child_price',
                    'infant_price',
                    'group_price',
                    'type',
                    'cont_min_people',
                    'cont_max_people',
                    'cont_max_capacity',
                    'disable_range',
                    'custom_avail',
                    'cont_custom_date',
                    'disable_specific',
                    'fixed_availability'
                ];
                $args = array(
                    'post_type' => 'tf_tours',
                    'post_status' => 'publish',
                    'posts_per_page' => -1
                );
                $posts = new WP_Query( $args );
                if ( $posts -> have_posts() ) {
                    while ( $posts -> have_posts() ) {
                        $posts->the_post();
                        $post_id =  get_the_ID();
                        $meta = get_post_meta( $post_id, 'tf_tours_opt', true );
                        if(!empty($searchable_keys)){
                            foreach($searchable_keys as $search){
                                $fields_values = !empty($meta[$search]) ? $meta[$search] : "";
                                update_post_meta( $post_id, 'tf_search_'.$search, $fields_values );
                            }
                        }
                    }
                }
                wp_reset_query();
            }elseif("tf_apartment"==$type){
                $searchable_keys = [
                    'apartment_as_featured',
                    'map',
                    'pricing_type',
                    'price_per_night',
                    'adult_price',
                    'child_price',
                    'infant_price',
                    'min_stay',
                    'max_adults',
                    'max_children',
                    'max_infants',
                    'apt_availability'
                ];
                $args = array(
                    'post_type' => 'tf_apartment',
                    'post_status' => 'publish',
                    'posts_per_page' => -1
                );
                $posts = new WP_Query( $args );
                if ( $posts -> have_posts() ) {
                    while ( $posts -> have_posts() ) {
                        $posts->the_post();
                        $post_id =  get_the_ID();
                        $meta = get_post_meta( $post_id, 'tf_apartment_opt', true );
                        if(!empty($searchable_keys)){
                            foreach($searchable_keys as $search){
                                $fields_values = !empty($meta[$search]) ? $meta[$search] : "";
                                update_post_meta( $post_id, 'tf_search_'.$search, $fields_values );
                            }
                        }
                    }
                }
                wp_reset_query();
            }else{

            }
        }

        // Hotel Room Migration
        public function tf_hotel_room_migrate(){
            $tf_room_data_migration = !empty(get_option( 'tf_room_data_migration' )) ? get_option( 'tf_room_data_migration' ) : 0;
            if ( $tf_room_data_migration < 5 ) {
                $this->regenerate_room_meta();
                update_option( 'tf_room_data_migration', $tf_room_data_migration+1 );
            }
        }
        
        function regenerate_room_meta(){

            $searchable_keys = [
                'order_id',
                'adult',
                'child',
                'pricing-by',
                'price',
                'adult_price',
                'child_price',
                'num-room',
                'reduce_num_room',
                'avail_date'
            ];

            $args = array(
                'post_type' => 'tf_hotel',
                'post_status' => 'publish',
                'posts_per_page' => -1
            );
            $posts = new WP_Query( $args );
            if ( $posts -> have_posts() ) {
                while ( $posts -> have_posts() ) {
                    $posts->the_post();
                    $post_id =  get_the_ID();
                    $meta = get_post_meta( $post_id, 'tf_hotels_opt', true );

                    $rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
                    if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
                        $tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
                            return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
                        }, $rooms );
                        $rooms                = unserialize( $tf_hotel_rooms_value );
                    }

                    $current_user_id = get_current_user_id();
                    foreach($rooms as $room){
                        $post_data = array(
                            'post_type'    => 'tf_room',
                            'post_title'   => !empty($room['title']) ? $room['title'] : 'No Title',
                            'post_status'  => 'publish',
                            'post_author'  => $current_user_id,
                        );
                        $room['tf_hotel'] = $post_id;

                        $room_post_id = wp_insert_post( $post_data );
                        update_post_meta( $room_post_id, 'tf_rooms_opt', $room );

                        if(!empty($searchable_keys)){
                            foreach($searchable_keys as $search){
                                $fields_values = !empty($room[$search]) ? $room[$search] : "";
                                update_post_meta( $room_post_id, 'tf_search_'.$search, $fields_values );
                            }
                        }
                    }
                    
                }
            }
            wp_reset_query();
        }
    }
}

TF_Migrator::instance();