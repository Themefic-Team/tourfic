<?php 
$total_facilities_cat = ! empty( tf_data_types( tfopt( 'hotel_facilities_cats' ) ) ) ? tf_data_types( tfopt( 'hotel_facilities_cats' ) ) : '';
if( !empty($total_facilities_cat) && !empty($meta['hotel-facilities']) ){
?>

<!-- Hotel facilities Srart -->
<div class="tf-facilities-wrapper" id="tf-hotel-facilities">
    <h2 class="tf-section-title"><?php echo !empty($meta['facilities-section-title']) ? esc_html($meta['facilities-section-title']) : esc_html("Property facilities"); ?></h2>          
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
            $f_icon_single  = ! empty( $total_facilities_cat[$catkey]['hotel_facilities_cat_icon'] ) ? $total_facilities_cat[$catkey]['hotel_facilities_cat_icon'] : '';
            ?>
            <h4>
            <?php echo !empty($f_icon_single) ? '<i class="' . $f_icon_single . '"></i>' : ''; ?> <?php echo $total_facilities_cat[$catkey]['hotel_facilities_cat_name'] ?? ''; ?>
            </h4>
            <ul>
                <?php 
                foreach( $meta['hotel-facilities'] as $facility ){ 
                if( $facility['facilities-category'] == $catkey ){
                $features_details = get_term($facility['facilities-feature']);
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