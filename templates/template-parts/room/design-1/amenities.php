<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

if ( $features ) { ?>
    <!--Popular Features -->
    <div class="tf-overview-wrapper">
        <div class="tf-overview-popular-facilities">
            <h2 class="tf-title tf-section-title"><?php echo ! empty( $meta["room-feature-section-title"] ) ? esc_html( $meta["room-feature-section-title"]) : ''; ?></h2>
            <ul>
				<?php
				foreach ( $features as $feature_id ) {
					$feature_meta = get_term_meta( $feature_id, 'tf_hotel_feature', true );
                    $feature = get_term( $feature_id );
					$f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
					if ( $f_icon_type == 'fa' && ! empty( $feature_meta['icon-fa'] ) ) {
						$feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
					}
					if ( $f_icon_type == 'c' && ! empty( $feature_meta['icon-c'] ) ) {
						$feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
					}
					?>
                    <li>
						<?php echo ! empty( $feature_meta ) && ! empty( $feature_icon ) ? wp_kses_post( $feature_icon ) : ''; ?>
						<?php echo esc_html($feature->name); ?>
                    </li>
				<?php } ?>
            </ul>
        </div>
    </div>
    <!--Popular Features -->
<?php } ?>