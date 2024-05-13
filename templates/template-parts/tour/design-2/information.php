<?php if ( $tour_duration || $info_tour_type || $group_size || $language ) { ?>
<!--Information Section Start -->
<div class="tf-overview-wrapper">
    <div class="tf-features-block-wrapper tf-informations-secations">
        <?php if ( $tour_duration ) { ?>
        <div class="tf-feature-block">
            <i class="ri-history-line"></i>
            <div class="tf-feature-block-details">
                <h5><?php echo esc_html__( 'Duration', 'tourfic' ); ?></h5>
                <p><?php echo esc_html( $tour_duration ); ?>
                <?php
                if ( $tour_duration > 1 ) {
                    $dur_string         = 's';
                    $duration_time_html = $duration_time . $dur_string;
                } else {
                    $duration_time_html = $duration_time;
                }
                echo " " . esc_html( $duration_time_html );
                ?>
                <?php if ( $night ) { ?>
                    <span>
                        <?php echo esc_html( $night_count ); ?>
                        <?php
                        if ( ! empty( $night_count ) ) {
                            if ( $night_count > 1 ) {
                                echo esc_html__( 'Nights', 'tourfic' );
                            } else {
                                echo esc_html__( 'Night', 'tourfic' );
                            }
                        }
                        ?>
                    </span>
                <?php } ?>
            </p>
            </div>
        </div>
        <?php } ?>
        <?php if ( $group_size ) { ?>
        <div class="tf-feature-block">
            <i class="ri-team-line"></i>
            <div class="tf-feature-block-details">
                <h5><?php echo esc_html__( 'Group Size', 'tourfic' ); ?></h5>
                <p><?php echo esc_html( $group_size ) ?></p>
            </div>
        </div>
        <?php } ?>
        <?php if ( $info_tour_type ) {
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
        <div class="tf-feature-block">
            <i class="ri-menu-unfold-line"></i>
            <div class="tf-feature-block-details">
                <h5><?php echo esc_html__( 'Tour Type', 'tourfic' ); ?></h5>
                <p><?php echo esc_html( $info_tour_type ) ?></p>
            </div>
        </div>
        <?php } ?>
        <?php if ( $language ) { ?>
        <div class="tf-feature-block">
            <i class="ri-global-line"></i>
            <div class="tf-feature-block-details">
                <h5><?php echo esc_html__( 'Language', 'tourfic' ); ?></h5>
                <p><?php echo esc_html( $language ) ?></p>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<!--Information Section End -->
<?php } ?>