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
		}

        public function tf_searchable_meta(){
            $this->regenerate_search_meta('tf_hotel');
            $this->regenerate_search_meta('tf_tours');
            $this->regenerate_search_meta('tf_apartment');
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
    }
}

TF_Migrator::instance();