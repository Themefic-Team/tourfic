<div class="tf-hotel-desc-section tf-template-section">
    <h3 class="tf-section-title"><?php _e( 'Description', 'tourfic' ); ?></h3>
    <div class="tf-hotel-description">
        <div class="tf-short-description">
			<?php
			if ( strlen( get_the_content() ) > 300 ) {
				echo strip_tags( tourfic_character_limit_callback( get_the_content(), 300 ) ) . '<span class="tf-see-description">See more</span>';
			} else {
				the_content();
			}
			?>
        </div>
        <div class="tf-full-description">
			<?php
			echo get_the_content() . '<span class="tf-see-less-description"> See less</span>';
			?>
        </div>
    </div>

	<?php if ( ! empty( $features ) ) { ?>
        <div class="tf-hotel-features">
            <ul>
				<?php
				foreach ( $features as $feature ) {
					$feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tf_hotel_feature', true );
					$f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';
					if ( $f_icon_type == 'fa' && ! empty( $feature_meta['icon-fa'] ) ) {
						$feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
					}
					if ( $f_icon_type == 'c' && ! empty( $feature_meta['icon-c'] ) ) {
						$feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
					}
					?>
                    <li>
						<?php echo ! empty( $feature_meta ) && ! empty( $feature_icon ) ? $feature_icon : ''; ?>
						<?php echo $feature->name; ?>
                    </li>
				<?php } ?>
            </ul>
        </div>
	<?php } ?>
</div>