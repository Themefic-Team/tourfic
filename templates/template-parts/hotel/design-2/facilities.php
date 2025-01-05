<?php 

use \Tourfic\Classes\Helper;

$total_facilities_cat = ! empty( Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'hotel_facilities_cats' ) ) : '';
if( !empty($total_facilities_cat) && !empty($meta['hotel-facilities']) ){
?>

<!-- Hotel facilities Srart -->
<div class="tf-facilities-wrapper" id="tf-hotel-facilities">
    <h2 class="tf-section-title"><?php echo !empty($meta['facilities-section-title']) ? esc_html($meta['facilities-section-title']) : esc_html__("Property facilities", 'tourfic'); ?></h2>          
    <div class="tf-facilities">
        <?php 
        $facilites_list = [];
        if( !empty($meta['hotel-facilities']) ){
            foreach( $meta['hotel-facilities'] as $facility ){
                $facilites_list [$facility['facilities-category']] = $facility['facilities-category'];
            }
        }
        if(!empty($facilites_list)){
        foreach( $facilites_list as $catkey=> $single_feature ){
        ?>
        <div class="tf-facility-item">
            <?php
            $f_icon_single  = ! empty( $total_facilities_cat[$catkey]['hotel_facilities_cat_icon'] ) ? esc_attr($total_facilities_cat[$catkey]['hotel_facilities_cat_icon']) : '';
            ?>
            <span class="single-facilities-title">
            <?php echo !empty($f_icon_single) ? '<i class="' . esc_attr($f_icon_single) . '"></i>' : ''; ?> <?php echo !empty($total_facilities_cat[$catkey]['hotel_facilities_cat_name']) ? esc_html($total_facilities_cat[$catkey]['hotel_facilities_cat_name']) : ''; ?>
            </span>
            <ul>
                <?php 
                foreach( $meta['hotel-facilities'] as $facility ){ 
                if( $facility['facilities-category'] == $catkey ){
                $features_details = !empty( $facility['facilities-feature'] ) ? get_term( $facility['facilities-feature'] ) : '';
                if(!empty($features_details->name)){
                ?>
                <li>
                    <?php echo esc_html($features_details->name); ?>
                </li>
                <?php }}} ?>
            </ul>
        </div>
        <?php } } ?>
        
    </div>
    
</div>
<!--Content facilities end -->
<?php } ?>