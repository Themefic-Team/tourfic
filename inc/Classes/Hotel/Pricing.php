<?php

namespace Tourfic\Classes\Hotel;

use Tourfic\Classes\Helper;

class Pricing {
	use \Tourfic\Traits\Singleton;

	public function __construct() {
		add_action( 'wp_ajax_tf_hotel_airport_service_price', array( $this, 'tf_hotel_airport_service_callback') );
		add_action( 'wp_ajax_nopriv_tf_hotel_airport_service_price', array( $this, 'tf_hotel_airport_service_callback' ) );

	}

	function tf_hotel_airport_service_callback() {
		// Check nonce security
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['_nonce'])), 'tf_ajax_nonce' ) ) {
			return;
		}
		$meta            = get_post_meta( sanitize_key( $_POST['id'] ), 'tf_hotels_opt', true );
		$airport_service = ! empty( $meta['airport_service'] ) ? $meta['airport_service'] : '';

		if ( 1 == $airport_service ) {

			$room_id       = isset( $_POST['roomid'] ) ? intval( sanitize_text_field( $_POST['roomid'] ) ) : null;
			$adult         = isset( $_POST['hoteladult'] ) ? intval( sanitize_text_field( $_POST['hoteladult'] ) ) : '0';
			$child         = isset( $_POST['hotelchildren'] ) ? intval( sanitize_text_field( $_POST['hotelchildren'] ) ) : '0';
			$room_selected = isset( $_POST['room'] ) ? intval( sanitize_text_field( $_POST['room'] ) ) : '0';
			$check_in      = isset( $_POST['check_in_date'] ) ? sanitize_text_field( $_POST['check_in_date'] ) : '';
			$check_out     = isset( $_POST['check_out_date'] ) ? sanitize_text_field( $_POST['check_out_date'] ) : '';
			$deposit       = isset( $_POST['deposit'] ) ? sanitize_text_field( $_POST['deposit'] ) : false;

			# Calculate night number
			$day_difference = self::calculate_days( $check_in, $check_out );

			$room_meta = get_post_meta( $room_id, 'tf_room_opt', true );

			$avail_by_date = ! empty( $room_meta['avil_by_date'] ) && $room_meta['avil_by_date'];
			if ( $avail_by_date ) {
				$avail_date = ! empty( $room_meta['avail_date'] ) ? json_decode( $room_meta['avail_date'], true ) : [];
			}

			$pricing_by      = $room_meta['pricing-by'];
			$price_multi_day = ! empty( $room_meta['price_multi_day'] ) ? $room_meta['price_multi_day'] : false;

			// Hotel Discout Data
			$hotel_discount_type   = ! empty( $room_meta["discount_hotel_type"] ) ? $room_meta["discount_hotel_type"] : "none";
			$hotel_discount_amount = ! empty( $room_meta["discount_hotel_price"] ) ? $room_meta["discount_hotel_price"] : 0;

			/**
			 * Calculate Pricing
			 */
			if ( $avail_by_date && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

				// Check availability by date option
				$period = new \DatePeriod(
					new \DateTime( $check_in . ' 00:00' ),
					new \DateInterval( 'P1D' ),
					new \DateTime( $check_out . ' 00:00' )
				);

				$total_price = 0;
				foreach ( $period as $date ) {

					$available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
						$date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
						$date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

						return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
					} ) );

					if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {
						$room_price  = ! empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $room_meta['price'];
						$adult_price = ! empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $room_meta['adult_price'];
						$child_price = ! empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $room_meta['child_price'];

						if ( $hotel_discount_type == "percent" ) {
							$room_price  = !empty($room_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $room_price - ( ( (int) $room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
							$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
							$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
						}
						if ( $hotel_discount_type == "fixed" ) {
							$room_price  = !empty($room_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $room_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
							$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
							$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
						}
						$total_price += $pricing_by == '1' ? $room_price : ( ( $adult_price * $adult ) + ( $child_price * $child ) );
					};

				}

				$price_total = $total_price * $room_selected;

			} else {

				if ( $pricing_by == '1' ) {
					$only_room_price = ! empty( $room_meta['price'] ) ? $room_meta['price'] : 0;
					if ( $hotel_discount_type == "percent" ) {
						$only_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $only_room_price - ( ( (int) $only_room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
					}
					if ( $hotel_discount_type == "fixed" ) {
						$only_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $only_room_price - (int) $hotel_discount_amount ), 2 ) );
					}
					$total_price = $only_room_price;

				} elseif ( $pricing_by == '2' ) {
					$adult_price = ! empty( $room_meta['adult_price'] ) ? $room_meta['adult_price'] : 0;
					$child_price = ! empty( $room_meta['child_price'] ) ? $room_meta['child_price'] : 0;

					if ( $hotel_discount_type == "percent" ) {
						$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
						$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					}
					if ( $hotel_discount_type == "fixed" ) {
						$adult_price = !empty($adult_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
						$child_price = !empty($child_price) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) : 0;
					}

					$adult_price = $adult_price * $adult;
					$child_price = $child_price * $child;
					$total_price = $adult_price + $child_price;

				}

				# Multiply pricing by night number
				if ( ! empty( $day_difference ) && $price_multi_day == true ) {
					$price_total = $total_price * $room_selected * $day_difference;
				} else {
					$price_total = $total_price * ( $room_selected * $day_difference + 1 );
				}

			}

			if ( $deposit == "true" ) {
				tf_get_deposit_amount( $room_meta, $price_total, $deposit_amount, $has_deposit );
				if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $has_deposit == true && ! empty( $deposit_amount ) ) {
					$deposit_amount;
				}
			}

			if ( "pickup" == $_POST['service_type'] ) {
				//$this->calculate_service_fee('airport_pickup_price', $meta, $price_total, $deposit, $deposit_amount);

				$airport_pickup_price = ! empty( $meta['airport_pickup_price'] ) ? Helper::tf_data_types($meta['airport_pickup_price']) : '';

				if ( "per_person" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$service_adult_fee = ! empty( $airport_pickup_price['airport_service_fee_adult'] ) ? $airport_pickup_price['airport_service_fee_adult'] : 0;
					$service_child_fee = ! empty( $airport_pickup_price['airport_service_fee_children'] ) ? $airport_pickup_price['airport_service_fee_children'] : 0;
					$service_fee       = ( sanitize_key( $_POST['hoteladult'] ) * $service_adult_fee ) + ( sanitize_key( $_POST['hotelchildren'] ) * $service_child_fee );

					if ( sanitize_key( $_POST['hotelchildren'] ) != 0 ) {
						echo "<span>";
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Child Count, %4$s Child Fee, %5$s Service Fee */
						echo sprintf( esc_html__( 'Airport Pickup Fee Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) : %5$s', 'tourfic' ),
							sanitize_key( $_POST['hoteladult'] ),
							wp_kses_post(wc_price( $service_adult_fee )),
							sanitize_key( $_POST['hotelchildren'] ),
							wp_kses_post(wc_price( $service_child_fee )),
							"<b>".wp_kses_post(wc_price( $service_fee ))."</b>"
						);
						echo "</span></br>";
					} else {
						echo "<span>";
						/* translators: %1$s Adult Count, %2$s Adult Fee */
						echo sprintf( esc_html__( 'Airport Pickup Fee Adult ( %1$s × %2$s ) :', 'tourfic' ),
								sanitize_key( $_POST['hoteladult'] ),
								wp_kses_post(wc_price( $service_adult_fee )),
						     ) . " " . "<b>" . wp_kses_post(wc_price( $service_fee )) . "</b>";
						echo "</span></br>";
					}
					if ( $deposit == "true" ) {
						echo "<span>";
						/* translators: %1$s Due Amount, %2$s Service Fee */
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post(wc_price( $price_total - $deposit_amount )) . " + " . wp_kses_post(wc_price( $service_fee )). '</b>';
						echo "</span></br>";
						echo "<span>";
						echo esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $deposit_amount )) . '</b>';
						echo "</span>";
					} else {
						echo "<span>";
						echo esc_html__( 'Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $price_total + $service_fee )) . '</b>';
						echo "</span>";
					}
				}
				if ( "fixed" == $airport_pickup_price['airport_pickup_price_type'] ) {
					$service_fee = ! empty( $airport_pickup_price['airport_service_fee_fixed'] ) ? $airport_pickup_price['airport_service_fee_fixed'] : 0;
					/* translators: %s Service Fee */
					echo sprintf( esc_html__( 'Airport Pickup Fee (Fixed): %s', 'tourfic' ),
						wp_kses_post(wc_price( $service_fee ))
					);
					if ( $deposit == "true" ) {
						echo "</br><span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post(wc_price( $price_total - $deposit_amount )) . " + " . wp_kses_post(wc_price( $service_fee )). '</b>';
						echo "</span>";

						/* translators: %s Payable Amount */
						echo '</br><span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $deposit_amount )) . '</b></span>';

					} else {
						/* translators: %s Payable Amount */
						echo '</br><span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $price_total + $service_fee )) . '</b></span>';
					}
				}
				if ( "free" == $airport_pickup_price['airport_pickup_price_type'] ) {
					echo esc_html__( 'Airport Pickup Fee: Free', 'tourfic' );
					if ( $deposit == "true" ) {
						echo '</br><span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $price_total - $deposit_amount )) . '</b></span>';
					} else {

						echo '</br><span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $price_total )) . '</b></span>';
					}
				}
			}
			if ( "dropoff" == $_POST['service_type'] ) {
				$airport_dropoff_price = ! empty( $meta['airport_dropoff_price'] ) ? Helper::tf_data_types($meta['airport_dropoff_price']) : '';

				if ( "per_person" == $airport_dropoff_price['airport_pickup_price_type'] ) {
					$service_adult_fee = ! empty( $airport_dropoff_price['airport_service_fee_adult'] ) ? $airport_dropoff_price['airport_service_fee_adult'] : 0;
					$service_child_fee = ! empty( $airport_dropoff_price['airport_service_fee_children'] ) ? $airport_dropoff_price['airport_service_fee_children'] : 0;
					$service_fee       = ( sanitize_key( $_POST['hoteladult'] ) * $service_adult_fee ) + ( sanitize_key( $_POST['hotelchildren'] ) * $service_child_fee );
					if ( sanitize_key( $_POST['hotelchildren'] ) != 0 ) {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Child Count, %4$s Child Fee, %5$s Service Fee */
						echo "<span>" . sprintf( esc_html__( 'Airport Dropoff Fee Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) : ', 'tourfic' ),
								sanitize_key( $_POST['hoteladult'] ),
								wp_kses_post(wc_price( $service_adult_fee )),
								sanitize_key( $_POST['hotelchildren'] ),
								wp_kses_post(wc_price( $service_child_fee )),
							) . "<b>" . wp_kses_post(wc_price( $service_fee )) . "</b>" . "</span></br>";
					} else {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Service Fee */
						echo "<span>" . sprintf( esc_html__( 'Airport Dropoff Fee Adult ( %1$s × %2$s ) : ', 'tourfic' ),
								sanitize_key( $_POST['hoteladult'] ),
								wp_kses_post(wc_price( $service_adult_fee )),
							) . "<b>" . wp_kses_post(wc_price( $service_fee )) . "</b>" . "</span></br>";
					}
					if ( $deposit == "true" ) {
						/* translators: %1$s Due Amount, %2$s Service Fee */
						echo "<span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post(wc_price( $price_total - $deposit_amount )) . " + " . wp_kses_post(wc_price( $service_fee )). '</b>';
						echo "</span></br>";
						echo '<span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $deposit_amount )) . '</b></span>';
					} else {
						echo '<span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $price_total + $service_fee )) . '</b></span>';
					}
				}
				if ( "fixed" == $airport_dropoff_price['airport_pickup_price_type'] ) {
					$service_fee = ! empty( $airport_dropoff_price['airport_service_fee_fixed'] ) ? $airport_dropoff_price['airport_service_fee_fixed'] : 0;
					/* translators: %s Service Fee */
					echo sprintf( esc_html__( 'Airport Dropoff Fee (Fixed): %s', 'tourfic' ),
						wp_kses_post(wc_price( $service_fee ))
					);
					if ( $deposit == "true" ) {
						/* translators: %1$s Due Amount, %2$s Service Fee */
						echo "</br><span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post(wc_price( $price_total - $deposit_amount )) . " + " . wp_kses_post(wc_price( $service_fee )). '</b>';
						echo "</span>";

						echo '</br><span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $deposit_amount )) . '</b></span>';
					} else {
						echo '</br><span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $price_total + $service_fee )) . '</b></span>';
					}
				}
				if ( "free" == $airport_dropoff_price['airport_pickup_price_type'] ) {
					echo esc_html__( 'Airport Dropoff Fee: Free', 'tourfic' );
					if ( $deposit == "true" ) {
						echo "</br><span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post(wc_price( $price_total - $deposit_amount )) . '</b>';
						echo "</span>";
						/* translators: %s Deposit Amount */
						echo '</br><span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $deposit_amount )) . '</b></span>';
					} else {
						echo '</br><span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $price_total )) . '</b></span>';
					}
				}
			}
			if ( "both" == $_POST['service_type'] ) {
				$airport_pickup_dropoff_price = ! empty( $meta['airport_pickup_dropoff_price'] ) ? Helper::tf_data_types($meta['airport_pickup_dropoff_price']) : '';

				if ( "per_person" == $airport_pickup_dropoff_price['airport_pickup_price_type'] ) {
					$service_adult_fee = ! empty( $airport_pickup_dropoff_price['airport_service_fee_adult'] ) ? $airport_pickup_dropoff_price['airport_service_fee_adult'] : 0;
					$service_child_fee = ! empty( $airport_pickup_dropoff_price['airport_service_fee_children'] ) ? $airport_pickup_dropoff_price['airport_service_fee_children'] : 0;
					$service_fee       = ( sanitize_key( $_POST['hoteladult'] ) * $service_adult_fee ) + ( sanitize_key( $_POST['hotelchildren'] ) * $service_child_fee );

					if ( sanitize_key( $_POST['hotelchildren'] ) != 0 ) {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Child Count, %4$s Child Fee, %5$s Service Fee */
						echo "<span>" . sprintf( esc_html__( 'Airport Pickup & Dropoff Fee Adult ( %1$s × %2$s ) + Child ( %3$s × %4$s ) : ', 'tourfic' ),
								sanitize_key( $_POST['hoteladult'] ),
								wp_kses_post(wc_price( $service_adult_fee )),
								sanitize_key( $_POST['hotelchildren'] ),
								wp_kses_post(wc_price( $service_child_fee )),
							) . "<b>" . wp_kses_post(wc_price( $service_fee )) . "</b>" . "</span></br>";
					} else {
						/* translators: %1$s Adult Count, %2$s Adult Fee, %3$s Service Fee */
						echo "<span>" . sprintf( esc_html__( 'Airport Pickup & Dropoff Fee Adult ( %1$s × %2$s ) : ', 'tourfic' ),
								sanitize_key( $_POST['hoteladult'] ),
								wp_kses_post(wc_price( $service_adult_fee )),
							) . "<b>" . wp_kses_post(wc_price( $service_fee )) . "</b>" . "</span></br>";
					}
					if ( $deposit == "true" ) {
						echo "<span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post(wc_price( $price_total - $deposit_amount )) . " + " . wp_kses_post(wc_price( $service_fee )). '</b>';
						echo "</span></br>";

						/* translators: %s Total Price */
						echo '<span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $deposit_amount )) . '</b></span>';
					} else {
						echo '<span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $price_total + $service_fee )) . '</b></span>';
					}

				}
				if ( "fixed" == $airport_pickup_dropoff_price['airport_pickup_price_type'] ) {
					$service_fee = ! empty( $airport_pickup_dropoff_price['airport_service_fee_fixed'] ) ? $airport_pickup_dropoff_price['airport_service_fee_fixed'] : 0;
					/* translators: %s Service Fee */
					echo sprintf( esc_html__( 'Airport Pickup & Dropoff Fee (Fixed): %s', 'tourfic' ),
						wp_kses_post(wc_price( $service_fee ))
					);

					if ( $deposit == "true" ) {
						echo "</br><span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post(wc_price( $price_total - $deposit_amount )) . " + " . wp_kses_post(wc_price( $service_fee )). '</b>';
						echo "</span>";

						/* translators: %s Deposit Amount */
						echo '</br><span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $deposit_amount )) . '</b></span>';
					} else {
						echo '</br><span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $price_total + $service_fee )) . '</b></span>';
					}
				}
				if ( "free" == $airport_pickup_dropoff_price['airport_pickup_price_type'] ) {
					echo esc_html__( 'Airport Pickup & Dropoff Fee: Free', 'tourfic' );
					if ( $deposit == "true" ) {
						echo "</br><span>";
						echo esc_html__( 'Due Amount : ', 'tourfic' ) . "<b>" . wp_kses_post(wc_price( $price_total - $deposit_amount )) . '</b>';
						echo "</span>";
						/* translators: %s Deposit Amount */
						echo '</br><span>' .  esc_html__('Total Payable Amount : ', 'tourfic' ) . '<b>' . wp_kses_post(wc_price( $deposit_amount )) . '</b></span>';
					} else {
						echo "</br><span>Total Payable Amount : <b>" . wp_kses_post(wc_price( $price_total )) . "</b></span>";
					}
				}
			}

		}
		wp_die();
	}

	static function calculate_days( $check_in, $check_out ) {
		if ( $check_in && $check_out ) {
			$check_in_stt   = strtotime( $check_in . ' +1 day' );
			$check_out_stt  = strtotime( $check_out );
			$day_difference = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
		}

		return $day_difference;
	}

	private function calculate_service_fee($service_key, $meta, &$price_total, $deposit, &$deposit_amount, $is_round_trip = false) {
		$price = !empty($meta[$service_key]) ? $meta[$service_key] : '';
		if (empty($price)) return;

		if ($is_round_trip) {
			$price_total += ($price * 2);
			if ($deposit == "true") {
				$deposit_amount += ($price * 2);
			}
		} else {
			$price_total += $price;
			if ($deposit == "true") {
				$deposit_amount += $price;
			}
		}
	}
}