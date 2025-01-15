<!-- Single Tour Trip informations -->
<div class="tf-trip-info tf-box tf-mb-30 tf-template-section">
    <div class="tf-trip-info-inner tf-flex tf-flex-space-bttn tf-flex-align-center tf-flex-gap-8">
        <!-- Single Tour short details -->
        <div class="tf-short-info">
            <ul class="tf-list">
				<?php if ( ! empty( $tour_duration ) ) { ?>
                    <li class="tf-flex tf-flex-gap-8">
                        <i class="fa-regular fa-clock"></i>
						<?php echo esc_html( $tour_duration ); ?>
						<?php
						if ( $tour_duration > 1 ) {
							$dur_string         = 's';
							$duration_time_html = $duration_time . $dur_string;
						} else {
							$duration_time_html = $duration_time;
						}
						echo " " . esc_html( $duration_time_html );
						?>
                    </li>
				<?php }

				if ( $tour_type == 'continuous' ) {
					if ( $custom_avail ) {
						$tf_max_people   = array();
						$tf_max_capacity = array();
						$tf_custom_date  = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
						if ( ! empty( $tf_custom_date ) && gettype( $tf_custom_date ) == "string" ) {
							$tf_tour_conti_avail = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
								return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
							}, $tf_custom_date );
							$tf_custom_date      = unserialize( $tf_tour_conti_avail );
						}
						if(is_array($tf_custom_date)) {
							foreach ( $tf_custom_date as $item ) {
							$max_people = ! empty( $item['max_people'] ) ? $item['max_people'] : '';
							if ( ! empty( $max_people ) ) {
								$tf_max_people [] = $max_people;
							}
							$max_capacity = ! empty( $item['max_capacity'] ) ? $item['max_capacity'] : '';
							if ( ! empty( $max_capacity ) ) {
								$tf_max_capacity [] = $max_capacity;
							}
						}
						}
						if ( ! empty( $tf_max_capacity ) ) {
							$tf_tour_booking_limit = max( $tf_max_capacity );
						}
						if ( ! empty( $tf_max_people ) ) {
							$max_people = max( $tf_max_people );
						}
					} else {
						$tf_tour_booking_limit = ! empty( $meta['cont_max_capacity'] ) ? $meta['cont_max_capacity'] : 0;
						$max_people            = ! empty( $meta['cont_max_people'] ) ? $meta['cont_max_people'] : 0;
					}
				}

				if ( $tour_type == 'fixed' ) {
					if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
						$tf_tour_fixed_avail   = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
							return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
						}, $meta['fixed_availability'] );
						$tf_tour_fixed_date    = unserialize( $tf_tour_fixed_avail );
						$max_people            = ! empty( $tf_tour_fixed_date['max_seat'] ) ? $tf_tour_fixed_date['max_seat'] : '';
						$tf_tour_booking_limit = ! empty( $tf_tour_fixed_date['max_capacity'] ) ? $tf_tour_fixed_date['max_capacity'] : 0;
					} else {
						$max_people            = ! empty( $meta['fixed_availability']['max_seat'] ) ? $meta['fixed_availability']['max_seat'] : '';
						$tf_tour_booking_limit = ! empty( $meta['fixed_availability']['max_capacity'] ) ? $meta['fixed_availability']['max_capacity'] : 0;
					}
				}

				if ( ! empty( $tf_tour_booking_limit ) || ! empty( $max_people ) ) { ?>
                    <li class="tf-flex tf-flex-gap-8">
                        <i class="fa-solid fa-people-group"></i>
						<?php if ( ! empty( $tf_tour_booking_limit ) ) {
							echo esc_html__( "Maximum Capacity: ", "tourfic" );
							echo esc_html($tf_tour_booking_limit);
						} else {
							echo esc_html__( "Maximum Allowed Per Booking: ", "tourfic" );
							echo esc_html($max_people);
						} ?>
                    </li>
				<?php }

				if ( ! empty( $tour_refund_policy ) ) { ?>
                    <li class="tf-flex tf-flex-gap-8">
                        <i class="fa-solid fa-person-walking-arrow-loop-left"></i>
						<?php echo esc_html( $tour_refund_policy ); ?>
                    </li>
				<?php } ?>
            </ul>
        </div>

		<?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : ?>
            <!-- Single Tour Person details -->
            <div class="tf-trip-person-info tf-flex tf-flex-gap-12">
                <ul class="tf-flex tf-flex-gap-12">
					<?php
					if ( $pricing_rule == 'group' ) {

						echo '<li data="group" class="person-info active"><i class="fa-solid fa-users"></i><p>' . esc_html__( "Group", "tourfic" ) . '</p></li>';

					} elseif ( $pricing_rule == 'person' ) {

						if ( ! $disable_adult && ! empty( $tour_price->adult ) ) {
							echo '<li data="adult" class="person-info active"><i class="fa-solid fa-user"></i><p>' . esc_html__( "Adult", "tourfic" ) . '</p></li>';
						}
						if ( ! $disable_child && ! empty( $tour_price->child ) ) {
                            $active_class = $disable_adult || empty( $tour_price->adult) ? 'active' : '';
							echo '<li data="child" class="person-info '. esc_attr($active_class) .'"><i class="fa-solid fa-child"></i><p>' . esc_html__( "Child", "tourfic" ) . '</p></li>';
						}
						if ( ! $disable_adult && ( ! $disable_infant && ! empty( $tour_price->infant ) ) ) {
                            $active_class = ($disable_adult || empty( $tour_price->adult)) && ($disable_child || empty( $tour_price->child )) ? 'active' : '';
							echo '<li data="infant" class="person-info '. esc_attr($active_class) .'"><i class="fa-solid fa-baby"></i><p>' . esc_html__( "Infant", "tourfic" ) . '</p></li>';
						}
					}
					?>
                </ul>
            </div>
			<?php if ( $pricing_rule == 'group' ) { ?>
                <div class="tf-trip-pricing tf-flex tf-group active">
                    <span class="tf-price-label"> <?php esc_html_e( "From", "tourfic" ); ?></span>
                    <span class="tf-price-amount"><?php echo isset($tour_price->wc_sale_group) ? wp_kses_post($tour_price->wc_sale_group) : wp_kses_post($tour_price->wc_group); ?></span>
                    <span class="tf-price-label-bttm"><?php esc_html_e( "Per Group", "tourfic" ); ?></span>
                </div>
			<?php } elseif ( $pricing_rule == 'person' ) { ?>
				<?php if ( ! $disable_adult && ! empty( $tour_price->adult ) ) { ?>
                    <div class="tf-trip-pricing tf-flex tf-adult active">
                        <span class="tf-price-label"> <?php esc_html_e( "From", "tourfic" ); ?></span>
                        <span class="tf-price-amount"><?php echo isset($tour_price->wc_sale_adult) ? wp_kses_post($tour_price->wc_sale_adult) : wp_kses_post($tour_price->wc_adult); ?></span>
                        <span class="tf-price-label-bttm"><?php esc_html_e( "Per Adult", "tourfic" ); ?></span>
                    </div>
				<?php }
				if ( ! $disable_child && ! empty( $tour_price->child ) ) { ?>
                    <div class="tf-trip-pricing tf-flex tf-child <?php echo $disable_adult || empty( $tour_price->adult ) ? esc_attr('active') : ''; ?>">
                        <span class="tf-price-label"> <?php esc_html_e( "From", "tourfic" ); ?></span>
                        <span class="tf-price-amount"><?php echo isset( $tour_price->wc_sale_child ) ? wp_kses_post($tour_price->wc_sale_child) : wp_kses_post($tour_price->wc_child); ?></span>
                        <span class="tf-price-label-bttm"><?php esc_html_e( "Per Child", "tourfic" ); ?></span>
                    </div>
				<?php }
				if ( ! $disable_adult && ( ! $disable_infant && ! empty( $tour_price->infant ) ) ) { ?>
                    <div class="tf-trip-pricing tf-flex tf-infant <?php echo ($disable_adult || empty( $tour_price->adult)) && ($disable_child || empty( $tour_price->child )) ? esc_attr('active') : ''; ?>">
                        <span class="tf-price-label"> <?php esc_html_e( "From", "tourfic" ); ?></span>
                        <span class="tf-price-amount"><?php echo isset($tour_price->wc_sale_infant) ? wp_kses_post($tour_price->wc_sale_infant) : wp_kses_post($tour_price->wc_infant); ?></span>
                        <span class="tf-price-label-bttm"><?php esc_html_e( "Per Infant", "tourfic" ); ?></span>
                    </div>
				<?php } ?>
			<?php } ?>
		<?php endif; ?>
    </div>
</div>