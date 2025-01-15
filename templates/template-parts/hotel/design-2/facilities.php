<?php 


// do not load directly
if ( ! defined( 'ABSPATH' ) ) {
    die();
}

if( !empty( $hotel_facilities_categories ) && !empty( $hotel_facilities ) ){
    ?>
    <div class="tf-hotel-facilities-section tf-mb-50 tf-template-section">
        <div class="tf-hotel-facilities-container">
            <div class="tf-hotel-facilities-title-area active">
                <h2 class="tf-title tf-section-title" >
                    <?php echo !empty($meta['facilities-section-title']) ? esc_html($meta['facilities-section-title']) : ''; ?>
                </h2>
                <i class="ri-arrow-down-s-line hotel-facilities-icon-down"></i>
                <i class="ri-arrow-up-s-line hotel-facilities-icon-up"></i>
            </div>
            <div class="tf-hotel-facilities-content-area">
                <?php 
                    $facilities_list = [];
                    if( !empty($meta['hotel-facilities']) ){
                        foreach( $meta['hotel-facilities'] as $facility ){
                            $facilities_list [$facility['facilities-category']] = $facility['facilities-category'];
                        }
                    }

                    if (!empty($facilities_list)) {
                        foreach($facilities_list as $key => $single_feature ) {
                            $f_icon_single  = ! empty( $hotel_facilities_categories[$key]['hotel_facilities_cat_icon'] ) ? esc_attr($hotel_facilities_categories[$key]['hotel_facilities_cat_icon']) : '';
                            ?>
                            <div class="hotel-facility-item">
                                <div class="hotel-single-facility-title">
                                    <?php echo !empty($hotel_facilities_categories[$key]['hotel_facilities_cat_name']) ? esc_html( $hotel_facilities_categories[$key]['hotel_facilities_cat_name']) : ''; ?>
                                </div>
                                <ul>
                                    <?php 
                                    foreach( $hotel_facilities as $facility ) :
                                        if( $facility['facilities-category'] == $key ) {
                                            $features_details = !empty( $facility['facilities-feature'] ) ? get_term( $facility['facilities-feature'] ) : '';
                                            $feature_meta = get_term_meta( $facility['facilities-feature'], 'tf_hotel_feature', true );

                                            $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                                            if ( $f_icon_type == 'fa' && !empty($feature_meta['icon-fa']) ) {
                                                $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
                                            } else if ( $f_icon_type == 'c' && !empty($feature_meta['icon-c']) ) {
                                                $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
                                            } else {
                                                $feature_icon = '<i class="ri-check-line"></i>';
                                            }

                                            if(!empty($features_details->name)) {
                                                ?>
                                                <li>
                                                <span><?php echo !empty($feature_meta) && !empty($feature_icon) ? wp_kses_post($feature_icon) : ''; ?></span> 
                                                <?php echo esc_html($features_details->name); ?>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php
                        }
                    }
                ?>
            </div>
        </div>
    </div>
    <?php
}