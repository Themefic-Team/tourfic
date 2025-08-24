<!-- Trip Features -->
<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;
if ( $tour_duration || $info_tour_type || $group_size || $language ) {  
    ?>
    <div class="tf-trip-feature-blocks tf-mb-56 tf-template-section">
        <div class="tf-features-block-inner tf-flex tf-flex-space-bttn tf-flex-gap-16">
			<?php if ( $tour_duration ) { ?>
                <div class="tf-feature-block tf-flex tf-flex-gap-8 tf-first">
                    <div class="tf-feature-block-icon">
                        <i class="<?php echo esc_attr($tour_duration_icon); ?>"></i>
                    </div>
                    <div class="tf-feature-block-details">
                        <h5><?php echo esc_html__( 'Duration', 'tourfic' ); ?></h5>
                        <p><?php echo esc_html( $tour_duration ); ?>
                            <?php
                            if ( $tour_duration > 1 ) {
                                $dur_string         = 's';
                                $_duration_time = $duration_time . $dur_string;
                            } else {
                                $_duration_time = $duration_time;
                            }
                            echo " " . esc_html( $_duration_time );

                            if ( $night ) {
                                echo '<span>';
                                    echo esc_html(', '. $night_count );
                                    if ( ! empty( $night_count ) ) {
                                        if ( $night_count > 1 ) {
                                            echo esc_html__( ' Nights', 'tourfic' );
                                        } else {
                                            echo esc_html__( ' Night', 'tourfic' );
                                        }
                                    }
                                echo '</span>';
                            }
                            ?>
                        </p>

                    </div>
                </div>
			<?php } ?>
            
			<?php 
           if ( is_array( $info_tour_type ) && array_filter( $info_tour_type ) ) {
				if ( gettype( $info_tour_type ) === 'string' ) {
					$info_tour_type = ucfirst( esc_html( $info_tour_type ) );
				} else if ( gettype( $info_tour_type ) === 'array' ) {
					$tour_types =[];
					$types = ! empty( get_the_terms( $post_id, 'tour_type' ) ) ? get_the_terms( $post_id, 'tour_type' ) : '';
					if ( ! empty( $types ) ) {
						foreach ( $types as $type ) {
							$tour_types[] = $type->name;
						}
					}
					$info_tour_type = implode( ', ', $tour_types );
				}
				?>
                <div class="tf-feature-block tf-flex tf-flex-gap-8  tf-second">
                    <div class="tf-feature-block-icon">
                    <i class="<?php echo esc_attr($tour_type_icon); ?>"></i>
                    </div>
                    <div class="tf-feature-block-details">
                        <h5><?php echo esc_html__( 'Tour Type', 'tourfic' ); ?></h5>
                        <p><?php echo esc_html( $info_tour_type ) ?></p>
                    </div>
                </div>
			<?php } ?>
			<?php if ( $group_size ) { ?>
                <div class="tf-feature-block tf-flex tf-flex-gap-8  tf-third">
                    <div class="tf-feature-block-icon">
                    <i class="<?php echo esc_attr($tour_group_icon); ?>"></i>
                    </div>
                    <div class="tf-feature-block-details">
                        <h5><?php echo esc_html__( 'Group Size', 'tourfic' ); ?></h5>
                        <p><?php echo esc_html( $group_size ) ?></p>
                    </div>
                </div>
			<?php } ?>
			<?php if ( $language ) { ?>
                <div class="tf-feature-block tf-flex tf-flex-gap-8  tf-tourth">
                    <div class="tf-feature-block-icon">
                    <i class="<?php echo esc_attr($tour_lang_icon); ?>"></i>
                    </div>
                    <div class="tf-feature-block-details">
                        <h5><?php echo esc_html__( 'Language', 'tourfic' ); ?></h5>
                        <p><?php echo esc_html( $language ) ?></p>
                    </div>
                </div>
			<?php } ?>
        </div>
    </div>
<?php } ?>

<?php if( $features && !empty($meta["features"]) ) : ?>
    <div class="tf-tour-features tf-mb-40 tf-template-section">
        <div class="tf-tour-features-container">
            <?php if (!empty($meta["tour-features-section-title"])) : ?>
                <h2 class="tf-title tf-section-title"><?php echo esc_html( $meta["tour-features-section-title"] ); ?></h2>
            <?php endif; ?>
            <ul class="tf-tour-feature-list">

                <?php foreach ( $features as $feature ) {
                    $feature_meta = get_term_meta( $feature->term_taxonomy_id, 'tour_features', true );
                    $f_icon_type  = ! empty( $feature_meta['icon-type'] ) ? $feature_meta['icon-type'] : '';

                    if ( $f_icon_type == 'fa' && !empty($feature_meta['icon-fa']) ) {
                        $feature_icon = '<i class="' . $feature_meta['icon-fa'] . '"></i>';
                    } else if ( $f_icon_type == 'c' && !empty($feature_meta['icon-c']) ) {
                        $feature_icon = '<img src="' . $feature_meta['icon-c'] . '" style="width: ' . $feature_meta['dimention'] . 'px; height: ' . $feature_meta['dimention'] . 'px;" />';
                    } ?>

                <?php if( !empty($feature->name) ) : ?>
                        <li class="single-feature-box">
                            <span class="tf-tour-features-icon"><?php echo !empty($feature_meta['icon-fa']) || !empty($feature_meta['icon-c'])  ? wp_kses_post($feature_icon) : ''; ?></span>
                            <span><?php echo esc_html($feature->name); ?></span>
                        </li>
                <?php endif; ?>
                <?php } ?>
            </ul>
        </div>
    </div>
<?php endif; ?>
