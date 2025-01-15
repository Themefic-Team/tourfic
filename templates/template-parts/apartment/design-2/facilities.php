<?php

use \Tourfic\Classes\Helper;

$total_facilities_cat = ! empty( Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) ) ? Helper::tf_data_types( Helper::tfopt( 'amenities_cats' ) ) : '';
if ( ! empty( $total_facilities_cat ) && ! empty( $meta['amenities'] ) ) {
	?>
    <!-- apartment facilities Srart -->
    <div class="tf-facilities-wrapper" id="tf-apartment-facilities">
        <h2 class="tf-section-title"><?php echo ! empty( $meta['facilities-section-title'] ) ? esc_html( $meta['facilities-section-title'] ) : esc_html( "Amenities" ); ?></h2>
        <div class="tf-facilities">
			<?php
			$facilites_list = [];
			if ( ! empty( $meta['amenities'] ) ) {
				foreach ( $meta['amenities'] as $facility ) {
					$facilites_list [ $facility['cat'] ] = $facility['cat'];
				}
			}
			if ( ! empty( $facilites_list ) ) {
				foreach ( $facilites_list as $catkey => $single_feature ) {
					?>
                    <div class="tf-facility-item">
						<?php
						$f_icon_single = ! empty( $total_facilities_cat[ $catkey ]['amenities_cat_icon'] ) ? $total_facilities_cat[ $catkey ]['amenities_cat_icon'] : '';
						?>
                        <span class="single-facilities-title">
                            <?php echo ! empty( $f_icon_single ) ? '<i class="' . esc_attr( $f_icon_single ) . '"></i>' : ''; ?><?php echo ! empty( $total_facilities_cat[ $catkey ]['amenities_cat_name'] ) ? esc_html( $total_facilities_cat[ $catkey ]['amenities_cat_name'] ) : ''; ?>
                        </span>
                        <ul>
							<?php
							if ( ! empty( $meta['amenities'] ) ) {
								foreach ( $meta['amenities'] as $facility ) {
									if ( $facility['cat'] == $catkey ) {
										$features_details = get_term( $facility['feature'] );
										if ( ! empty( $features_details->name ) ) {
											?>
                                            <li>
												<?php echo esc_html( $features_details->name ); ?>
                                            </li>
										<?php }
									}
								}
							} ?>
                        </ul>
                    </div>
				<?php }
			} ?>
        </div>
    </div>
    <!--Content facilities end -->
<?php } ?>