<?php if ( $meta['hotel-facilities'] ) { ?>
<div class="tf-hotel-single-features tf-mb-50 tf-template-section">
    <h2 class="tf-title tf-section-title"><?php echo !empty($meta['popular-section-title']) ? esc_html($meta['popular-section-title']) : ''; ?></h2>
    <ul>

    <?php
    $favourite_features = array();
    foreach( $meta['hotel-facilities'] as $facility ){
        if( $facility["favorite"] ) {
            $favourite_features[ $facility['facilities-feature'] ] = $facility["facilities-feature"];
        }
    }

    if(!empty($favourite_features) && is_array($favourite_features)) {
        foreach($favourite_features as $feature) {
            $feature_meta = get_term_meta( $feature, 'tf_hotel_feature', true );
            $feature_name = get_term( $feature );
            $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';

            if ( $f_icon_type == 'fa' && !empty($feature_meta['icon-fa']) ) {
                $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
            } elseif ( $f_icon_type == 'c' && !empty($feature_meta['icon-c']) ) {
                $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
            } ?>

            <li>
                <?php echo !empty($feature_meta) && !empty($feature_icon) ? wp_kses_post($feature_icon) : ''; ?>
                <?php echo esc_html($feature_name->name); ?>
            </li>
            <?php

        }
    }
    ?>
    </ul>
</div>
<?php } ?>