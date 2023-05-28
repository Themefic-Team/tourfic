<!-- Trip Features -->
<?php if ( $tour_duration || $tour_type || $group_size || $language ) { ?>
<div class="tf-trip-feature-blocks tf-mb-40 tf-template-section">
    <div class="tf-features-block-inner tf-column-4 tf-flex tf-flex-space-bttn tf-flex-gap-16">
        <?php if ( $tour_duration ) { ?>
        <div class="tf-column tf-flex tf-flex-gap-8">
            <div class="tf-feature-block tf-flex tf-flex-gap-8 tf-first">
                <div class="tf-feature-block-icon">
                <i class="fa-regular fa-clock"></i>
                </div>
                <div class="tf-feature-block-details">
                    <h5><?php echo __( 'Duration', 'tourfic' ); ?></h5>
                    <p><?php echo esc_html( $tour_duration ); ?>
                        <?php
                        if( $tour_duration > 1  ){
                            $dur_string = 's';
                            $duration_time_html = $duration_time . $dur_string;
                        }else{
                            $duration_time_html = $duration_time;
                        }
                        echo " " . esc_html( $duration_time_html ); 
                        ?>
                        <?php if( $night ){ ?>
                        <span>
                            <?php echo esc_html( $night_count ); ?>
                                <?php
                                if(!empty($night_count)){
                                    if( $night_count > 1  ){
                                        echo esc_html__( 'Nights', 'tourfic' );
                                    }else{
                                        echo esc_html__( 'Night', 'tourfic'  );
                                    }	
                                }										
                                ?>
                        </span>
                        <?php } ?>
                    </p>
                    
                </div>
            </div>
        </div>
        <?php } ?>
        <?php if ( $tour_type ) { ?>
        <div class="tf-column tf-flex tf-flex-gap-8">
            <div class="tf-feature-block tf-flex tf-flex-gap-8  tf-second">
                <div class="tf-feature-block-icon">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <div class="tf-feature-block-details">
                    <h5><?php echo __( 'Tour Type', 'tourfic' ); ?></h5>
                    <p><?php echo ucfirst( esc_html( $tour_type ) ) ?></p>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php if ( $group_size ) { ?>
        <div class="tf-column tf-flex tf-flex-gap-8">
            <div class="tf-feature-block tf-flex tf-flex-gap-8  tf-third">
                <div class="tf-feature-block-icon">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <div class="tf-feature-block-details">
                    <h5><?php echo __( 'Group Size', 'tourfic' ); ?></h5>
                    <p><?php echo esc_html( $group_size ) ?></p>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php if ( $language ) { ?>
        <div class="tf-column tf-flex tf-flex-gap-8">
            <div class="tf-feature-block tf-flex tf-flex-gap-8  tf-tourth">
                <div class="tf-feature-block-icon">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <div class="tf-feature-block-details">
                    <h5><?php echo __( 'Language', 'tourfic' ); ?></h5>
                    <p><?php echo esc_html( $language ) ?></p>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<?php } ?>