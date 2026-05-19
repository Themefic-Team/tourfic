<?php

namespace Tourfic\App\Templates\Components\Shared\Archive;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

/**
 * Centralized archive banner renderer.
 * Other builders and template files should call the methods here so
 * markup is maintained in a single place.
 */
class Banner {

	/**
	 * Render the archive banner markup.
	 * @param array $settings Optional settings array (from widgets).
	 * @param string $builder Optional builder type (from widgets).
	 */
	public static function render( $settings = [], $builder = '' ) {
		$post_type    = get_post_type();
		$term         = get_queried_object();
		$banner_title = '';
		$banner_thumbnail = '';

		if(is_post_type_archive('tf_hotel')){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] : '';
            $banner_title = esc_html__("Hotels", "tourfic");
        } elseif($post_type == 'tf_hotel' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_queried_object()->taxonomy=="hotel_location"){
            $tf_location_meta      = get_term_meta( $term->term_id, 'tf_hotel_location', true );
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] : '';
            $banner_thumbnail = ! empty( $tf_location_meta['image'] ) ? $tf_location_meta['image'] : $banner_thumbnail;
            $banner_title = $term->name;
        } elseif($post_type == 'tf_hotel' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0]=="tf_hotel" ){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['hotel_archive_design_2_bannar'] : '';
            $banner_title = $term->name;
        }

        if(is_post_type_archive('tf_tours')){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_2_bannar'] : '';
            $banner_title = esc_html__("Tours", "tourfic");
        } elseif($post_type == 'tf_tours' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_queried_object()->taxonomy=="tour_destination"){
            $tf_location_meta      = get_term_meta( $term->term_id, 'tf_tour_destination', true );
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_2_bannar'] : '';
            $banner_thumbnail = ! empty( $tf_location_meta['image'] ) ? $tf_location_meta['image'] : $banner_thumbnail;
            $banner_title = $term->name;
        } elseif($post_type == 'tf_tours' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0]=="tf_tours" ){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_2_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['tour_archive_design_2_bannar'] : '';
            $banner_title = $term->name;
        }

        if(is_post_type_archive('tf_apartment')){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] : '';
            $banner_title = esc_html__("Apartments", "tourfic");
        } elseif($post_type == 'tf_apartment' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_queried_object()->taxonomy=="apartment_location"){
            $tf_location_meta      = get_term_meta( $term->term_id, 'tf_apartment_location', true );
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] : '';
            $banner_thumbnail = ! empty( $tf_location_meta['image'] ) ? $tf_location_meta['image'] : $banner_thumbnail;
            $banner_title = $term->name;
        } elseif($post_type == 'tf_apartment' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0]=="tf_apartment" ){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['apartment_archive_design_1_bannar'] : '';
            $banner_title = $term->name;
        }

        if(is_post_type_archive('tf_carrental') || $post_type == 'tf_carrental'){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] : '';
            $banner_title = esc_html__("Cars", "tourfic");
        } elseif($post_type == 'tf_carrental' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_queried_object()->taxonomy=="carrental_location"){
            $tf_location_meta      = get_term_meta( $term->term_id, 'tf_carrental_location', true );
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] : '';
            $banner_thumbnail = ! empty( $tf_location_meta['image'] ) ? $tf_location_meta['image'] : $banner_thumbnail;
            $banner_title = $term->name;
        } elseif($post_type == 'tf_carrental' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_queried_object()->taxonomy=="carrental_brand"){
            $tf_location_meta      = get_term_meta( $term->term_id, 'tf_carrental_brand', true );
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] : '';
            $banner_thumbnail = ! empty( $tf_location_meta['image'] ) ? $tf_location_meta['image'] : $banner_thumbnail;
            $banner_title = $term->name;
        } elseif($post_type == 'tf_carrental' && !empty(get_taxonomy(get_queried_object()->taxonomy)->object_type) && get_taxonomy(get_queried_object()->taxonomy)->object_type[0]=="tf_carrental" ){
            $banner_thumbnail = ! empty( Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] ) ?  Helper::tf_data_types(Helper::tfopt( 'tf-template' ))['car_archive_design_1_bannar'] : '';
            $banner_title = $term->name;
        }
        ?>
        <?php if($post_type == 'tf_carrental') : ?>
            <div class="tf-archive-template__one" style="padding: 0;">
                <div class="tf-archive-car-banner" style="<?php echo !empty($banner_thumbnail) ? 'background-image: url('.esc_url($banner_thumbnail).');' : ''; ?>">
                    <div class="tf-banner-content tf-flex tf-flex-align-center tf-flex-justify-center tf-flex-direction-column">
                        <h1><?php echo esc_html($banner_title); ?></h1>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="tf-archive-template__two" style="padding: 0;">
                <div class="tf-hero-section-wrap" style="<?php echo !empty($banner_thumbnail) ? 'background: linear-gradient(0deg, rgba(48, 40, 28, 0.40) 0%, rgba(48, 40, 28, 0.40) 100%), url('.esc_url($banner_thumbnail).'), lightgray 0px -268.76px / 100% 249.543% no-repeat;background-size: cover; background-position: center;' : 'background: rgba(48, 40, 28, 0.30);'; ?>">
                    <div class="tf-container">
                        <div class="tf-hero-content tf-archive-hero-content tf-banner-content">
                            <div class="tf-head-title">
                                <h1><?php echo esc_html($banner_title); ?></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;
	}
}
