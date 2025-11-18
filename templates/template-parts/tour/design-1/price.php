<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;
?>
<!-- Single Tour Trip informations -->
<div class="tf-trip-info tf-box tf-mb-56 tf-template-section">
    <div class="tf-trip-info-inner tf-flex tf-flex-space-bttn tf-flex-align-center tf-flex-gap-8">
        <!-- Single Tour short details -->
        <div class="tf-short-info">
            <ul class="tf-list">
				<?php 
				if ( ! empty( $tour_duration ) ) { ?>
                    <li class="tf-flex tf-flex-gap-8">
                        <i class="fa-regular fa-clock"></i>
						<?php echo esc_html( $tour_duration ); ?>
						<?php
						if ( $tour_duration > 1 ) {
							$dur_string         = 's';
							$_duration_time = $duration_time . $dur_string;
						} else {
							$_duration_time = $duration_time;
						}
						echo " " . esc_html( $_duration_time );
						?>
                    </li>
				<?php }

					
				$tour_availability_data = isset( $meta['tour_availability'] ) && ! empty( $meta['tour_availability'] ) ? json_decode( $meta['tour_availability'], true ) : [];

				$tf_package_pricing = ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : '';

				$tf_max_people = [];
				$tf_max_capacity = [];
				if( !empty($tour_availability_data) && ('person'==$pricing_rule || 'group'==$pricing_rule) ){
					foreach ($tour_availability_data as $data) {
						if ($data['status'] !== 'available') {
							continue;
						}
		
						if($data['pricing_type'] == 'person'){
							if (!empty($data['max_person'])) {
								$tf_max_people [] = $data['max_person'];
							} 
							if (!empty($data['max_capacity'])) {
								$tf_max_capacity [] = $data['max_capacity'];
							} 
						}
		
						if($data['pricing_type'] == 'group'){
							if (!empty($data['max_person'])) {
								$tf_max_people [] = $data['max_person'];
							} 
							if (!empty($data['max_capacity'])) {
								$tf_max_capacity [] = $data['max_capacity'];
							}
						}
						

					}
				}

				if('package'==$pricing_rule && !empty($tf_package_pricing)){
					foreach($tf_package_pricing as $package){
						if (!empty($package['max_adult'])) {
							$tf_max_people [] = $package['max_adult'];
						} 
					}
				}

				if ( ! empty( $tf_max_capacity ) ) {
					$tf_tour_booking_limit = max( $tf_max_capacity );
				}
				if ( ! empty( $tf_max_people ) ) {
					$max_people = max( $tf_max_people );
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

		<?php if ( ( $tf_booking_type == 2 && $tf_hide_price !== '1' ) || $tf_booking_type == 1 || $tf_booking_type == 3 ) : 
			$adult_price = !empty($avail_prices['adult_price']) ? $avail_prices['adult_price'] : $adult_price;
			$child_price = !empty($avail_prices['child_price']) ? $avail_prices['child_price'] : $children_price;
			$infant_price = !empty($avail_prices['infant_price']) ? $avail_prices['infant_price'] : $infant_price;
			$group_price = !empty($avail_prices['group_price']) ? $avail_prices['group_price'] : $group_price;
		?>
            <!-- Single Tour Person details -->
            <div class="tf-trip-person-info tf-flex tf-flex-gap-12">
                <ul class="tf-flex tf-flex-gap-12">
					<?php
					if ( $pricing_rule == 'group' ) {

						echo '<li data="group" class="person-info active"><i class="fa-solid fa-users"></i><p>' . esc_html__( "Group", "tourfic" ) . '</p></li>';

					} elseif ( $pricing_rule == 'person' ) {

						if ( ! $disable_adult && ! empty( $adult_price ) ) {
							echo '<li data="adult" class="person-info active"><i class="fa-solid fa-user"></i><p>' . esc_html__( "Adult", "tourfic" ) . '</p></li>';
						}
						if ( ! $disable_child && ! empty( $child_price ) ) {
                            $active_class = $disable_adult || empty( $adult_price) ? 'active' : '';
							echo '<li data="child" class="person-info '. esc_attr($active_class) .'"><i class="fa-solid fa-child"></i><p>' . esc_html__( "Child", "tourfic" ) . '</p></li>';
						}
						if ( ! $disable_adult && ( ! $disable_infant && ! empty( $infant_price ) ) ) {
                            $active_class = ($disable_adult || empty( $adult_price)) && ($disable_child || empty( $child_price )) ? 'active' : '';
							echo '<li data="infant" class="person-info '. esc_attr($active_class) .'"><i class="fa-solid fa-baby"></i><p>' . esc_html__( "Infant", "tourfic" ) . '</p></li>';
						}
					}
					?>
                </ul>
            </div>
			<?php if ( $pricing_rule == 'group' ) { ?>
                <div class="tf-trip-pricing tf-flex tf-group active">
                    <span class="tf-price-label"> <?php esc_html_e( "From", "tourfic" ); ?></span>
                    <span class="tf-price-amount"><?php echo wp_kses_post(wc_price($group_price)); ?></span>
                    <span class="tf-price-label-bttm"><?php esc_html_e( "Per Group", "tourfic" ); ?></span>
                </div>
			<?php } elseif ( $pricing_rule == 'person' ) { ?>
				<?php if ( ! $disable_adult && ! empty( $adult_price ) ) { ?>
                    <div class="tf-trip-pricing tf-flex tf-adult active">
                        <span class="tf-price-label"> <?php esc_html_e( "From", "tourfic" ); ?></span>
                        <span class="tf-price-amount"><?php echo wp_kses_post(wc_price($adult_price)); ?></span>
                        <span class="tf-price-label-bttm"><?php esc_html_e( "Per Adult", "tourfic" ); ?></span>
                    </div>
				<?php }
				if ( ! $disable_child && ! empty( $child_price ) ) { ?>
                    <div class="tf-trip-pricing tf-flex tf-child <?php echo $disable_adult || empty( $adult_price ) ? esc_attr('active') : ''; ?>">
                        <span class="tf-price-label"> <?php esc_html_e( "From", "tourfic" ); ?></span>
                        <span class="tf-price-amount"><?php echo wp_kses_post(wc_price($child_price)); ?></span>
                        <span class="tf-price-label-bttm"><?php esc_html_e( "Per Child", "tourfic" ); ?></span>
                    </div>
				<?php }
				if ( ! $disable_adult && ( ! $disable_infant && ! empty( $infant_price ) ) ) { ?>
                    <div class="tf-trip-pricing tf-flex tf-infant <?php echo ($disable_adult || empty( $adult_price)) && ($disable_child || empty( $child_price )) ? esc_attr('active') : ''; ?>">
                        <span class="tf-price-label"> <?php esc_html_e( "From", "tourfic" ); ?></span>
                        <span class="tf-price-amount"><?php echo wp_kses_post(wc_price($infant_price)); ?></span>
                        <span class="tf-price-label-bttm"><?php esc_html_e( "Per Infant", "tourfic" ); ?></span>
                    </div>
				<?php } ?>
			<?php } ?>
		<?php endif; ?>
    </div>
</div>