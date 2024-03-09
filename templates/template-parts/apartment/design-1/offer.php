<?php if ( isset($meta['amenities']) && ! empty( tf_data_types( $meta['amenities'] ) ) ) :
$fav_amenities = array();
foreach ( tf_data_types( $meta['amenities'] ) as $amenity ) {
    if ( ! isset( $amenity['favorite'] ) || $amenity['favorite'] !== '1' ) {
        continue;
    }
    $fav_amenities[] = $amenity;
}
if ( ! empty( $fav_amenities ) ){
?>
<div class="tf-place-offer-section">
    <h2><?php ! empty( $meta['amenities_title'] ) ? esc_html_e( $meta['amenities_title'] ) : ''; ?></h2>
    <div class="place-offer-items">
        <?php
            foreach ( array_slice( $fav_amenities, 0, 10 ) as $amenity ) :
                $feature = get_term_by( 'id', $amenity['feature'], 'apartment_feature' );
                $feature_meta = get_term_meta( $amenity['feature'], 'tf_apartment_feature', true );
                $f_icon_type = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
                if ( $f_icon_type == 'icon' && !empty($feature_meta['apartment-feature-icon']) ) {
                    $feature_icon = '<i class="' . $feature_meta['apartment-feature-icon'] . '"></i>';
                } elseif ( $f_icon_type == 'custom' && !empty($feature_meta['apartment-feature-icon-custom']) ) {
                    $feature_icon = '<img src="' . esc_url( $feature_meta['apartment-feature-icon-custom'] ) . '" style="width: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px; height: ' . $feature_meta['apartment-feature-icon-dimension'] . 'px;" />';
                }
                ?>
                <div class="tf-apt-amenity">
                    <?php echo ! empty( $feature_icon ) ? "<div class='tf-apt-amenity-icon'>" . esc_attr( $feature_icon ). "</div>" : ""; ?>
                    <span><?php echo esc_html( $feature->name ); ?></span>
                </div>
            <?php endforeach; ?>
    </div>

</div>
<?php } endif; ?>