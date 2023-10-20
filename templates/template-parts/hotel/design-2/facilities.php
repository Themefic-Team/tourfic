<!-- Hotel facilities Srart -->
<div class="tf-facilities-wrapper" id="tf-hotel-facilities">       
    <h2 class="tf-section-title"><?php echo !empty($meta['facilities-section-title']) ? esc_html($meta['facilities-section-title']) : esc_html("Property facilities"); ?></h2>          
    <div class="tf-facilities">
        <?php 
        $facilites_list = [];
        if( !empty($meta['hotel-facilities']) ){
            foreach( $meta['hotel-facilities'] as $facility ){
                if(!empty($facility['facilities-feature'])){
                    $facilites_list [$facility['facilities-feature']] = $facility['facilities-feature'];
                }
            }
        }

        if(!empty($facilites_list)){
        foreach( $facilites_list as $single_feature ){
        ?>
        
        <div class="tf-facility-item">
            <?php
            $feature_meta = get_term_meta( $single_feature, 'tf_hotel_feature', true );
            $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
            if ( $f_icon_type == 'fa' && !empty($feature_meta['icon-fa']) ) {
                $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
            } elseif ( $f_icon_type == 'c' && !empty($feature_meta['icon-c']) ) {
                $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
            } ?>
            <h4>
            <?php echo $feature_icon ?? ''; ?> Services
            </h4>
            <ul>
                <li>Car rental</li>
                <li>Elevator</li>
                <li>24 hours security</li>
                <li>Ironing service(Chargable)</li>
            </ul>
        </div>
        <?php } } ?>
        
    </div>
    
</div>
<!--Content facilities end -->