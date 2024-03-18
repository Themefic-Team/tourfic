<!--Popular Features -->
<div class="tf-overview-wrapper">
    <div class="tf-overview-popular-facilities">
        <span class="tf-popular-facilities-title"><?php esc_html_e("Popular facilities", "tourfic"); ?></span>
        <ul>
        <?php 
        $favo_facilites_list = [];
        if( !empty($meta['hotel-facilities']) ){
            foreach( $meta['hotel-facilities'] as $facility ){
                $favo_facilites_list [$facility['facilities-feature']] = $facility['facilities-feature'];
            }
        } 

        if(!empty($favo_facilites_list)){
        foreach ( $favo_facilites_list as $feature ) {
        $feature_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
        $feature_name = get_term( $feature );
        $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
        if ( $f_icon_type == 'fa' && !empty($feature_meta['icon-fa']) ) {
            $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
        }
        if ( $f_icon_type == 'c' && !empty($feature_meta['icon-c']) ) {
            $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
        }
        ?>
        <li>
                <?php echo !empty($feature_meta) && !empty($feature_icon) ? wp_kses_post($feature_icon) : ''; ?>
                <?php echo !empty($feature_name->name) ? esc_html($feature_name->name) : ''; ?>
            </li>
        <?php } } ?>

        <?php if( !empty($features) && count($favo_facilites_list) < 8 ){
        $features_number = 1;
        foreach ( $features as $feature ) {
            if( $features_number <= 8-count($favo_facilites_list) ){
            $feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
            $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
            if ( $f_icon_type == 'fa' && !empty($feature_meta['icon-fa']) ) {
                $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
            }
            if ( $f_icon_type == 'c' && !empty($feature_meta['icon-c']) ) {
                $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
            }
            ?>

            <li>
                <?php echo !empty($feature_meta) && !empty($feature_icon) ? wp_kses_post($feature_icon) : ''; ?>
                <?php echo !empty($feature->name) ? esc_html($feature->name) : ''; ?>
            </li>
        <?php } $features_number++; } } ?>
        </ul>
    </div>
</div>
<!--Popular Features -->