<!--Popular Features -->
<div class="tf-overview-wrapper">
    <div class="tf-overview-popular-facilities">
        <h3><?php _e("Popular facilities", "tourfic"); ?></h3>
        <ul>
        <?php 
        if(!empty($features)){
        foreach ( $features as $feature ) {
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
                <?php echo !empty($feature_meta) && !empty($feature_icon) ? $feature_icon : ''; ?>
                <?php echo $feature->name; ?>
            </li>
        <?php } } ?>
        </ul>
    </div>
</div>
<!--Popular Features -->