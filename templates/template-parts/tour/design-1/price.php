

<!-- Single Tour Trip informations -->
<div class="tf-trip-info tf-box tf-mb-30 tf-template-section">
<div class="tf-trip-info-inner tf-flex tf-flex-space-bttn tf-flex-align-center tf-flex-gap-8">
    <!-- Single Tour short details -->
    <div class="tf-short-info">
        <ul class="tf-list">
            <?php 
            if(!empty($tour_duration)){
            ?>
            <li class="tf-flex tf-flex-gap-8">
                <i class="fa-regular fa-clock"></i> 
                <?php echo esc_html( $tour_duration ); ?>
                <?php
                if( $tour_duration > 1  ){
                    $dur_string = 's';
                    $duration_time_html = $duration_time . $dur_string;
                }else{
                    $duration_time_html = $duration_time;
                }
                echo " " . esc_html( $duration_time_html ); 
                ?>
            </li>
            <?php } ?>
        </ul>
    </div>
    <!-- Single Tour Person details -->
    <div class="tf-trip-person-info tf-flex tf-flex-gap-12">
        <ul class="tf-flex tf-flex-gap-12">
            <?php
            if ( $pricing_rule == 'group' ) {

                echo '<li data="group" class="person-info active"><i class="fa-solid fa-users"></i><p>' . __( "Group", "tourfic" ) . '</p></li>';

            } elseif ( $pricing_rule == 'person' ) {

                if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
                    echo '<li data="adult" class="person-info active"><i class="fa-solid fa-user"></i><p>' . __( "Adult", "tourfic" ) . '</p></li>';
                }
                if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                    echo '<li data="child" class="person-info"><i class="fa-solid fa-child"></i><p>' . __( "Child", "tourfic" ) . '</p></li>';
                }
                if ( ! $disable_infant && ! empty( $tour_price->infant ) ) {
                    echo '<li data="infant" class="person-info"><i class="fa-solid fa-baby"></i><p>' . __( "Infant", "tourfic" ) . '</p></li>';
                }

            }
            ?>
        </ul>
    </div>
    <?php if ( $pricing_rule == 'group' ) { ?>
        <div class="tf-trip-pricing tf-flex tf-group active">
            <span class="tf-price-label"> <?php _e("From","tourfic"); ?>, </span>
            <span class="tf-price-amount"><?php echo $tour_price->wc_sale_group ?? $tour_price->wc_group; ?></span>
            <span class="tf-price-label-bttm"><?php _e("Per Group", "tourfic"); ?></span>
        </div>
    <?php } elseif ( $pricing_rule == 'person' ) { ?>
            <?php if ( ! $disable_adult && ! empty( $tour_price->adult ) ) { ?>
            <div class="tf-trip-pricing tf-flex tf-adult active">
                <span class="tf-price-label"> <?php _e("From","tourfic"); ?>, </span>
                <span class="tf-price-amount"><?php echo $tour_price->wc_sale_adult ?? $tour_price->wc_adult; ?></span>
                <span class="tf-price-label-bttm"><?php _e("Per Adult", "tourfic"); ?></span>
            </div>
            <?php }
            if ( ! $disable_child && ! empty( $tour_price->child ) ) { ?>
            <div class="tf-trip-pricing tf-flex tf-child">
                <span class="tf-price-label"> <?php _e("From","tourfic"); ?>, </span>
                <span class="tf-price-amount"><?php echo $tour_price->wc_sale_child ?? $tour_price->wc_child; ?></span>
                <span class="tf-price-label-bttm"><?php _e("Per Child", "tourfic"); ?></span>
            </div>
            <?php }
            if ( ! $disable_infant && ! empty( $tour_price->infant ) ) { ?>
            <div class="tf-trip-pricing tf-flex tf-infant">
                <span class="tf-price-label"> <?php _e("From","tourfic"); ?>, </span>
                <span class="tf-price-amount"><?php echo $tour_price->wc_sale_infant ?? $tour_price->wc_infant; ?></span>
                <span class="tf-price-label-bttm"><?php _e("Per Infant", "tourfic"); ?></span>
            </div>
            <?php } ?>
    <?php } ?>
</div>
</div>