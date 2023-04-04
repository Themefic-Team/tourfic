<?php if ( $features ) { ?>
<div class="tf-hotel-single-features tf-mrbottom-70">
    <h2 class="tf-title"><?php echo __("Popular Features","tourfic"); ?></h2>
    <ul>
    <?php foreach ( $features as $feature ) {
        $feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
        $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
        if ( $f_icon_type == 'fa' && !empty($feature_meta['icon-fa']) ) {
            $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
        } elseif ( $f_icon_type == 'c' && !empty($feature_meta['icon-c']) ) {
            $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
        } ?>

        <li>
            <?php echo $feature_icon ?? ''; ?>
            <?php echo $feature->name; ?>
        </li>
    <?php } ?>
    </ul>
</div>
<?php } ?>