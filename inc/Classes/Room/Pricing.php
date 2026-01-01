<?php

namespace Tourfic\Classes\Room;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

class Pricing {

	//private static $instance;
	protected $post_id;
	protected $option_id;
	protected $meta;
	protected $checkin;
	protected $checkout;
	protected $days;
	protected $period;
	protected array $persons;
	protected $room_number;
	public $price_settings;
	public $template;

	public static function instance( $post_id = '', $option_id = '' ) {
		return new self( $post_id, $option_id );
	}

	public function __construct( $post_id = '', $option_id = '' ) {
		$this->post_id        = $post_id;
		$this->option_id      = $option_id;
		$this->meta      = get_post_meta( $post_id, 'tf_room_opt', true );
		$this->price_settings = ! empty( Helper::tfopt( 'hotel_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'hotel_archive_price_minimum_settings' ) : 'all';

		// Single Template Style
		// $layout_conditions = ! empty( $this->meta['tf_single_hotel_layout_opt'] ) ? $this->meta['tf_single_hotel_layout_opt'] : 'global';
		// if ( "single" == $layout_conditions ) {
		// 	$single_template = ! empty( $this->meta['tf_single_hotel_template'] ) ? $this->meta['tf_single_hotel_template'] : 'design-1';
		// }
		// $global_template = ! empty( Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] ) ? Helper::tf_data_types( Helper::tfopt( 'tf-template' ) )['single-hotel'] : 'design-1';
		// $this->template  = ! empty( $single_template ) ? $single_template : $global_template;
	}

	public function set_dates( $check_in, $check_out ) {
		$meta       = $this->meta;
		$price_multi_day = ! empty( $meta['price_multi_day'] ) ? $meta['price_multi_day'] : false;

		if ( ! empty( $check_in ) && ! empty( $check_out ) ) {

			if ( ! $price_multi_day ) {
				$check_in_stt = strtotime( $check_in );

				$period = new \DatePeriod(
					new \DateTime( $check_in . ' 00:00' ),
					new \DateInterval( 'P1D' ),
					new \DateTime( $check_out . ' 23:59' )
				);
			} else {
				$check_in_stt = strtotime( $check_in . ' +1 day' );

				$period = new \DatePeriod(
					new \DateTime( $check_in . ' 00:00' ),
					new \DateInterval( 'P1D' ),
					new \DateTime( $check_out . ' 00:00' )
				);
			}
			$check_out_stt = strtotime( $check_out );
			$days          = round( ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 );
		}

		$this->days     = ! empty( $days ) ? $days : 0;
		$this->period   = ! empty( $period ) ? $period : 0;
		$this->checkin  = ! empty( $check_in ) ? $check_in : '';
		$this->checkout = ! empty( $check_out ) ? $check_out : '';

		return $this;
	}

	public function set_persons( $adult, $child ) {
		$this->persons = array(
			'adult' => ! empty( $adult ) ? $adult : 0,
			'child' => ! empty( $child ) ? $child : 0,
		);

		return $this;
	}

	public function set_room_number( $room_number ) {
		$this->room_number = $room_number;

		return $this;
	}

	function get_discount() {
		$meta       = get_post_meta( $this->post_id, 'tf_room_opt', true );
		$discount_type   = ! empty( $meta["discount_hotel_type"] ) ? $meta["discount_hotel_type"] : "none";
		$discount_amount = ( $discount_type == 'fixed' || $discount_type == 'percent' ) && ! empty( $meta["discount_hotel_price"] ) ? $meta["discount_hotel_price"] : 0;

		return array(
			'discount_type'   => $discount_type,
			'discount_amount' => $discount_amount,
		);
	}

	function calculate_discount( $price ) {
		$discount_arr = $this->get_discount();

		if ( ! empty( $discount_arr ) ) {
			if ( $discount_arr['discount_type'] == 'fixed' ) {
				$price = (int) $price - (int) $discount_arr['discount_amount'];
			} else if ( $discount_arr['discount_type'] == 'percent' ) {
				$price = (int) $price - ( (int) $price * (int) $discount_arr['discount_amount'] ) / 100;
			}
		}

		return $price;
	}

	static function apply_discount( $price, $discount_type, $discount_amount ) {
		if ( $discount_type == 'fixed' ) {
			$price = $price - $discount_amount;
		} else if ( $discount_type == 'percent' ) {
			$price = $price - ( $price * $discount_amount ) / 100;
		}

		return $price;
	}

	/*
	 * Get min and max price
	 */
	function get_min_max_price() {
		$room_price = [];
		$meta     = get_post_meta( $this->post_id, 'tf_room_opt', true );
		$pricing_by    = $meta['pricing-by'] ?? 1;
		$avail_by_date = $meta['avil_by_date'] ?? 1;
		$current_date  = strtotime( "today" );

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $avail_by_date == "1" ) {
			$avail_date = json_decode( $meta['avail_date'], true );
			if ( ! empty( $avail_date ) && is_array( $avail_date ) ) {
				foreach ( $avail_date as $singleavailroom ) {
					if ( ! empty( $this->period ) ) {
						foreach ( $this->period as $date ) {
							$singleavailroom_date = gmdate( 'Y-m-d', $date->getTimestamp() );
							if ( $singleavailroom['date'] == $singleavailroom_date ) {
								if ( $pricing_by == 1 ) {
									$room_meta_price = $singleavailroom['price'] ?? 0;
									$discount_price  = $this->calculate_discount( $room_meta_price );

									$room_price[] = [
										"regular_price" => $room_meta_price,
										"sale_price"    => $discount_price
									];
								} elseif ( $pricing_by == 2 ) {
									if ( $this->price_settings == 'adult' || $this->price_settings == 'all' ) {
										$adult_price          = $singleavailroom['adult_price'] ?? 0;
										$discount_adult_price = $this->calculate_discount( $adult_price );

										$room_price[] = [
											"regular_price" => $adult_price,
											"sale_price"    => $discount_adult_price
										];
									}
									if ( $this->price_settings == 'child' || $this->price_settings == 'all' ) {
										$child_price          = $singleavailroom['child_price'] ?? 0;
										$discount_child_price = $this->calculate_discount( $child_price );

										$room_price[] = [
											"regular_price" => $child_price,
											"sale_price"    => $discount_child_price
										];
									}
								} else {
									$options_count = $singleavailroom['options_count'] ?? 0;
									for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
										if ( $singleavailroom[ 'tf_room_option_' . $i ] == '1' && $singleavailroom[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
											$option_price          = $singleavailroom[ 'tf_option_room_price_' . $i ] ?? 0;
											$discount_option_price = $this->calculate_discount( $option_price );

											$room_price[] = [
												"regular_price" => $option_price,
												"sale_price"    => $discount_option_price
											];
										} else if ( $singleavailroom[ 'tf_room_option_' . $i ] == '1' && $singleavailroom[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
											if ( $this->price_settings == 'adult' || $this->price_settings == 'all' ) {
												$option_adult_price          = $singleavailroom[ 'tf_option_adult_price_' . $i ] ?? 0;
												$discount_option_adult_price = $this->calculate_discount( $option_adult_price );

												$room_price[] = [
													"regular_price" => $option_adult_price,
													"sale_price"    => $discount_option_adult_price
												];
											}
											if ( $this->price_settings == 'child' || $this->price_settings == 'all' ) {
												$option_child_price          = $singleavailroom[ 'tf_option_child_price_' . $i ] ?? 0;
												$discount_option_child_price = $this->calculate_discount( $option_child_price );

												$room_price[] = [
													"regular_price" => $option_child_price,
													"sale_price"    => $discount_option_child_price
												];
											}
										}

									}
								}
							}
						}
					} else {
						if ( $current_date < strtotime( $singleavailroom['check_in'] ) && $singleavailroom['status'] == 'available' ) {
							if ( $pricing_by == 1 ) {
								$room_meta_price = $singleavailroom['price'] ?? 0;
								$discount_price  = $this->calculate_discount( $room_meta_price );

								$room_price[] = [
									"regular_price" => $room_meta_price,
									"sale_price"    => $discount_price
								];
							} elseif ( $pricing_by == 2 ) {
								if ( $this->price_settings == 'adult' || $this->price_settings == 'all' ) {
									$adult_price          = $singleavailroom['adult_price'] ?? 0;
									$discount_adult_price = $this->calculate_discount( $adult_price );

									$room_price[] = [
										"regular_price" => $adult_price,
										"sale_price"    => $discount_adult_price
									];
								}
								if ( $this->price_settings == 'child' || $this->price_settings == 'all' ) {
									$child_price          = $singleavailroom['child_price'] ?? 0;
									$discount_child_price = $this->calculate_discount( $child_price );

									$room_price[] = [
										"regular_price" => $child_price,
										"sale_price"    => $discount_child_price
									];
								}
							} else {
								$options_count = $singleavailroom['options_count'] ?? 0;
								for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
									if ( $singleavailroom[ 'tf_room_option_' . $i ] == '1' && $singleavailroom[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
										$option_price          = $singleavailroom[ 'tf_option_room_price_' . $i ] ?? 0;
										$discount_option_price = $this->calculate_discount( $option_price );

										$room_price[] = [
											"regular_price" => $option_price,
											"sale_price"    => $discount_option_price
										];
									} else if ( $singleavailroom[ 'tf_room_option_' . $i ] == '1' && $singleavailroom[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
										if ( $this->price_settings == 'adult' || $this->price_settings == 'all' ) {
											$option_adult_price          = $singleavailroom[ 'tf_option_adult_price_' . $i ] ?? 0;
											$discount_option_adult_price = $this->calculate_discount( $option_adult_price );

											$room_price[] = [
												"regular_price" => $option_adult_price,
												"sale_price"    => $discount_option_adult_price
											];
										}
										if ( $this->price_settings == 'child' || $this->price_settings == 'all' ) {
											$option_child_price          = $singleavailroom[ 'tf_option_child_price_' . $i ] ?? 0;
											$discount_option_child_price = $this->calculate_discount( $option_child_price );

											$room_price[] = [
												"regular_price" => $option_child_price,
												"sale_price"    => $discount_option_child_price
											];
										}
									}
								}
							}
						}
					}
				}
			}
		} else {
			if ( $pricing_by == 1 ) {
				$room_meta_price = $meta['price'] ?? '';
				$discount_price  = $room_meta_price ? $this->calculate_discount( $room_meta_price ) : '';

				$room_price[] = [
					"regular_price" => $room_meta_price,
					"sale_price"    => $discount_price
				];
			} elseif ( $pricing_by == 2 ) {
				if ( $this->price_settings == 'adult' || $this->price_settings == 'all' ) {
					$adult_price          = $meta['adult_price'] ?? '';
					$discount_adult_price = $adult_price ? $this->calculate_discount( $adult_price ) : '';

					$room_price[] = [
						"regular_price" => $adult_price,
						"sale_price"    => $discount_adult_price
					];
				}
				if ( $this->price_settings == 'child' || $this->price_settings == 'all' ) {
					$child_price          = $meta['child_price'] ?? '';
					$discount_child_price = $child_price ? $this->calculate_discount( $child_price ) : '';

					$room_price[] = [
						"regular_price" => $child_price,
						"sale_price"    => $discount_child_price
					];
				}


			} elseif ( $pricing_by == 3 ) {
				$room_options = ! empty( $meta['room-options'] ) ? $meta['room-options'] : [];

				if ( ! empty( $room_options ) ) {
					foreach ( $room_options as $room_option ) {
						$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
						if ( $option_price_type == 'per_room' ) {
							$room_meta_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
							$discount_price  = $this->calculate_discount( $room_meta_price );

							$room_price[] = [
								"regular_price" => $room_meta_price,
								"sale_price"    => $discount_price
							];
						} elseif ( $option_price_type == 'per_person' ) {
							if ( $this->price_settings == 'adult' || $this->price_settings == 'all' ) {
								$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
								$discount_price     = $this->calculate_discount( $option_adult_price );

								$room_price[] = [
									"regular_price" => $option_adult_price,
									"sale_price"    => $discount_price
								];
							}
							if ( $this->price_settings == 'child' || $this->price_settings == 'all' ) {
								$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;
								$discount_price     = $this->calculate_discount( $option_child_price );

								$room_price[] = [
									"regular_price" => $option_child_price,
									"sale_price"    => $discount_price
								];
							}
						}
					}
				}
			}
		}

		// Get min and max price
		return array(
			'min' => array(
				'regular_price' => ! empty( $room_price ) ? min( array_column( $room_price, 'regular_price' ) ) : 0,
				'sale_price'    => ! empty( $room_price ) ? min( array_column( $room_price, 'sale_price' ) ) : 0,
			),
			'max' => array(
				'regular_price' => ! empty( $room_price ) ? max( array_column( $room_price, 'regular_price' ) ) : 0,
				'sale_price'    => ! empty( $room_price ) ? max( array_column( $room_price, 'sale_price' ) ) : 0,
			),
            'room_price' => $room_price
		);
	}

    function get_min_price($period = []){
	    $room_price = [];
	    $tf_lowestAmount = 0;
	    $tf_lowestAmount_items = null;
	    $current_date  = strtotime( "today" );
	    
		$meta = get_post_meta($this->post_id, 'tf_room_opt', true);

		//hotel room discount data
		$hotel_discount_type = !empty($meta["discount_hotel_type"]) ? $meta["discount_hotel_type"] : "none";
		$hotel_discount_amount = !empty($meta["discount_hotel_price"]) ? $meta["discount_hotel_price"] : 0;
		if($hotel_discount_type!="none" && !empty($hotel_discount_amount)){
			$tf_lowestAmount_items['amount'] = $hotel_discount_amount;
			$tf_lowestAmount_items['type'] = $hotel_discount_type;

			$tf_lowestAmount = intval($hotel_discount_amount); // Convert the amount to an integer for comparison
			if ($hotel_discount_amount < $tf_lowestAmount) {
				$tf_lowestAmount = $hotel_discount_amount;
				$tf_lowestAmount_items['amount'] = $hotel_discount_amount;
				$tf_lowestAmount_items['type'] = $hotel_discount_type;
			}
		}

		//room price
		$pricing_by = ! empty( $meta['pricing-by'] ) ? $meta['pricing-by'] : 1;
		$avail_by_date = !empty($meta['avil_by_date']) ? $meta['avil_by_date'] : false;

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $avail_by_date ) {
			$avail_date = json_decode( $meta['avail_date'], true );

			if ( ! empty( $avail_date ) ) {
				foreach ( $avail_date as $repval ) {
					if ( ! empty( $period ) ) {
						foreach ( $period as $date ) {
							$singleavailroom_date = gmdate( 'Y-m-d', $date->getTimestamp() );
							if ( strtotime( $repval['check_in'] ) == strtotime( $singleavailroom_date ) ) {
								if ( $pricing_by == 1 ) {
									if ( ! empty( $repval['price'] ) ) {
										$repval_price         = $repval['price'];
										$dicount_b_room_price = 0;

										if ( $hotel_discount_type == "percent" ) {
											$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $repval_price - ( ( (int) $repval_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
										} else if ( $hotel_discount_type == "fixed" ) {
											$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $repval_price - (int) $hotel_discount_amount ), 2 ) ) );
										}
										if ( $dicount_b_room_price != 0 ) {
											$room_price[] = array(
												"regular_price" => $repval['price'],
												"sale_price"    => $dicount_b_room_price,
												"discount_type" => $hotel_discount_type,
												"discount_amount" => $hotel_discount_amount,
											);
										} else {
											$room_price[] = array(
												"sale_price" => $repval['price'],
											);
										}
									}
								} elseif ( $pricing_by == 2 ) {
									$adult_price         = $repval['adult_price'];
									$child_price         = $repval['child_price'];
									$dicount_adult_price = 0;
									$dicount_child_price = 0;

									if ( $hotel_discount_type == "percent" ) {
										$dicount_adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
										$dicount_child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
									} else if ( $hotel_discount_type == "fixed" ) {
										$dicount_adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) );
										$dicount_child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) );
									}
									// end
									if ( $this->price_settings == "adult" || $this->price_settings == "all" ) {
										if ( ! empty( $repval['adult_price'] ) ) {

											if ( $dicount_adult_price != 0 ) {
												$room_price[] = array(
													"regular_price" => $repval['adult_price'],
													"sale_price"    => $dicount_adult_price,
													"discount_type" => $hotel_discount_type,
													"discount_amount" => $hotel_discount_amount,
												);
											} else {
												$room_price[] = array(
													"sale_price" => $repval['adult_price'],
												);
											}
										}
									}
									if ( $this->price_settings == "child" || $this->price_settings == "all") {
										if ( ! empty( $repval['child_price'] ) ) {

											if ( $dicount_child_price != 0 ) {
												$room_price[] = array(
													"regular_price" => $repval['child_price'],
													"sale_price"    => $dicount_child_price,
													"discount_type" => $hotel_discount_type,
													"discount_amount" => $hotel_discount_amount,
												);
											} else {
												$room_price[] = array(
													"sale_price" => $repval['child_price'],
												);
											}
										}
									}
								} elseif ( $pricing_by == 3 ) {
									$options_count = $repval['options_count'] ?? 0;
									for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
										if ( $repval[ 'tf_room_option_' . $i ] == '1' && $repval[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
											$option_price          = $repval[ 'tf_option_room_price_' . $i ] ?? 0;
											$discount_option_price = self::apply_discount( $option_price, $hotel_discount_type, $hotel_discount_amount );

											if ( $discount_option_price != 0 ) {
												$room_price[] = array(
													"regular_price" => $option_price,
													"sale_price"    => $discount_option_price,
													"discount_type" => $hotel_discount_type,
													"discount_amount" => $hotel_discount_amount,
												);
											} else {
												$room_price[] = array(
													"sale_price" => $option_price,
												);
											}
										} else if ( $repval[ 'tf_room_option_' . $i ] == '1' && $repval[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
											if ( $this->price_settings == 'adult' || $this->price_settings == 'all' ) {
												$option_adult_price          = $repval[ 'tf_option_adult_price_' . $i ] ?? 0;
												$discount_option_adult_price = self::apply_discount( $option_adult_price, $hotel_discount_type, $hotel_discount_amount );

												if ( $discount_option_adult_price != 0 ) {
													$room_price[] = array(
														"regular_price" => $option_adult_price,
														"sale_price"    => $discount_option_adult_price,
														"discount_type" => $hotel_discount_type,
														"discount_amount" => $hotel_discount_amount,
													);
												} else {
													$room_price[] = array(
														"sale_price" => $option_adult_price,
													);
												}
											}
											if ( $this->price_settings == 'child' || $this->price_settings == 'all' ) {
												$option_child_price          = $repval[ 'tf_option_child_price_' . $i ] ?? 0;
												$discount_option_child_price = self::apply_discount( $option_child_price, $hotel_discount_type, $hotel_discount_amount );

												if ( $discount_option_child_price != 0 ) {
													$room_price[] = array(
														"regular_price" => $option_child_price,
														"sale_price"    => $discount_option_child_price,
														"discount_type" => $hotel_discount_type,
														"discount_amount" => $hotel_discount_amount,
													);
												} else {
													$room_price[] = array(
														"sale_price" => $option_child_price,
													);
												}
											}
										}

									}
								}
							}
						}
					} else {
						if ( $current_date < strtotime( $repval['check_in'] ) && $repval['status'] == 'available' ) {
							if ( $pricing_by == 1 ) {
								if ( ! empty( $repval['price'] ) ) {
									$repval_price         = $repval['price'];
									$dicount_b_room_price = 0;

									if ( $hotel_discount_type == "percent" ) {
										$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $repval_price - ( ( (int) $repval_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
									} else if ( $hotel_discount_type == "fixed" ) {
										$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $repval_price - (int) $hotel_discount_amount ), 2 ) ) );
									}
									if ( $dicount_b_room_price != 0 ) {
										$room_price[] = array(
											"regular_price" => $repval['price'],
											"sale_price"    => $dicount_b_room_price,
											"discount_type" => $hotel_discount_type,
											"discount_amount" => $hotel_discount_amount,
										);
									} else {
										$room_price[] = array(
											"sale_price" => $repval['price'],
										);
									}
								}
							} elseif ( $pricing_by == 2 ) {
								$adult_price         = $repval['adult_price'];
								$child_price         = $repval['child_price'];
								$dicount_adult_price = 0;
								$dicount_child_price = 0;

								if ( $hotel_discount_type == "percent" ) {
									// if ( ! empty( $dicount_adult_price ) && ! empty( $dicount_child_price ) ) {
									$dicount_adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
									$dicount_child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
									// }
								} else if ( $hotel_discount_type == "fixed" ) {
									// if ( ! empty( $dicount_adult_price ) && ! empty( $dicount_child_price ) ) {
									$dicount_adult_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) );
									$dicount_child_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) );
									// }
								}
								if ( $this->price_settings == "adult" || $this->price_settings == "all") {
									if ( ! empty( $repval['adult_price'] ) ) {

										if ( $dicount_adult_price != 0 ) {
											$room_price[] = array(
												"regular_price" => $repval['adult_price'],
												"sale_price"    => $dicount_adult_price,
												"discount_type" => $hotel_discount_type,
												"discount_amount" => $hotel_discount_amount,
											);
										} else {
											$room_price[] = array(
												"sale_price" => $repval['adult_price'],
											);
										}
									}
								}
								if ( $this->price_settings == "child" || $this->price_settings == "all") {
									if ( ! empty( $repval['child_price'] ) ) {

										if ( $dicount_child_price != 0 ) {
											$room_price[] = array(
												"regular_price" => $repval['child_price'],
												"sale_price"    => $dicount_child_price,
												"discount_type" => $hotel_discount_type,
												"discount_amount" => $hotel_discount_amount,
											);
										} else {
											$room_price[] = array(
												"sale_price" => $repval['child_price'],
											);
										}
									}
								}
							} elseif ( $pricing_by == 3 ) {
								$options_count = $repval['options_count'] ?? 0;
								for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
									if ( $repval[ 'tf_room_option_' . $i ] == '1' && $repval[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
										$option_price          = intval( $repval[ 'tf_option_room_price_' . $i ] ) ?? 0;
										$discount_option_price = self::apply_discount( $option_price, $hotel_discount_type, $hotel_discount_amount );

										if ( $discount_option_price != 0 ) {
											$room_price[] = array(
												"regular_price" => $option_price,
												"sale_price"    => $discount_option_price,
												"discount_type" => $hotel_discount_type,
												"discount_amount" => $hotel_discount_amount,
											);
										} else {
											$room_price[] = array(
												"sale_price" => $option_price,
											);
										}
									} else if ( $repval[ 'tf_room_option_' . $i ] == '1' && $repval[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
										if ( $this->price_settings == 'adult' || $this->price_settings == 'all' ) {
											$option_adult_price          = intval( $repval[ 'tf_option_adult_price_' . $i ] ) ?? 0;
											$discount_option_adult_price = self::apply_discount( $option_adult_price, $hotel_discount_type, $hotel_discount_amount );

											if ( $discount_option_adult_price != 0 ) {
												$room_price[] = array(
													"regular_price" => $option_adult_price,
													"sale_price"    => $discount_option_adult_price,
													"discount_type" => $hotel_discount_type,
													"discount_amount" => $hotel_discount_amount,
												);
											} else {
												$room_price[] = array(
													"sale_price" => $option_adult_price,
												);
											}
										}
										if ( $this->price_settings == 'child' || $this->price_settings == 'all' ) {
											$option_child_price          = intval( $repval[ 'tf_option_child_price_' . $i ] ) ?? 0;
											$discount_option_child_price = self::apply_discount( $option_child_price, $hotel_discount_type, $hotel_discount_amount );

											if ( $discount_option_child_price != 0 ) {
												$room_price[] = array(
													"regular_price" => $option_child_price,
													"sale_price"    => $discount_option_child_price,
													"discount_type" => $hotel_discount_type,
													"discount_amount" => $hotel_discount_amount,
												);
											} else {
												$room_price[] = array(
													"sale_price" => $option_child_price,
												);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		} else {
			if ( $pricing_by == 1 ) {
				if ( ! empty( $meta['price'] ) ) {
					$b_room_price = $meta['price'];

					$dicount_b_room_price = 0;

					if ( $hotel_discount_type == "percent" ) {
						$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $b_room_price - ( ( (int) $b_room_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) );
					} else if ( $hotel_discount_type == "fixed" ) {
						$dicount_b_room_price = floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $b_room_price - (int) $hotel_discount_amount ), 2 ) ) );
					}
					if ( $dicount_b_room_price != 0 ) {
						$room_price[] = array(
							"regular_price" => $meta['price'],
							"sale_price"    => $dicount_b_room_price,
							"discount_type" => $hotel_discount_type,
							"discount_amount" => $hotel_discount_amount,
						);
					} else {
						$room_price[] = array(
							"sale_price" => $meta['price'],
						);
					}
				}
			} elseif ( $pricing_by == 2 ) {
				$adult_price         = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : '';
				$child_price         = ! empty( $meta['child_price'] ) ? $meta['child_price'] : '';
				$dicount_adult_price = 0;
				$dicount_child_price = 0;
				// discount calculation - start
				if ( $hotel_discount_type == "percent" ) {
					$dicount_adult_price = ! empty( $adult_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $adult_price - ( ( (int) $adult_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					$dicount_child_price = ! empty( $child_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $child_price - ( ( (int) $child_price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
				} else if ( $hotel_discount_type == "fixed" ) {
					$dicount_adult_price = ! empty( $adult_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $adult_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					$dicount_child_price = ! empty( $child_price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $child_price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
				}

				if ( $this->price_settings == "all" ) {
					if ( ! empty( $meta['adult_price'] ) ) {

						if ( $dicount_adult_price != 0 ) {
							$room_price[] = array(
								"regular_price" => $meta['adult_price'],
								"sale_price"    => $dicount_adult_price,
								"discount_type" => $hotel_discount_type,
								"discount_amount" => $hotel_discount_amount,
							);
						} else {
							$room_price[] = array(
								"sale_price" => $meta['adult_price'],
							);
						}
					}
					if ( ! empty( $meta['child_price'] ) ) {

						if ( $dicount_child_price != 0 ) {
							$room_price[] = array(
								"regular_price" => $meta['child_price'],
								"sale_price"    => $dicount_child_price,
								"discount_type" => $hotel_discount_type,
								"discount_amount" => $hotel_discount_amount,
							);
						} else {
							$room_price[] = array(
								"sale_price" => $meta['child_price'],
							);
						}
					}
				}
				if ( $this->price_settings == "adult" ) {
					if ( ! empty( $meta['adult_price'] ) ) {

						if ( $dicount_adult_price != 0 ) {
							$room_price[] = array(
								"regular_price" => $meta['adult_price'],
								"sale_price"    => $dicount_adult_price,
								"discount_type" => $hotel_discount_type,
								"discount_amount" => $hotel_discount_amount,
							);
						} else {
							$room_price[] = array(
								"sale_price" => $meta['adult_price'],
							);
						}
					}
				}
				if ( $this->price_settings == "child" ) {
					if ( ! empty( $meta['child_price'] ) ) {

						if ( $dicount_child_price != 0 ) {
							$room_price[] = array(
								"regular_price" => $meta['child_price'],
								"sale_price"    => $dicount_child_price,
								"discount_type" => $hotel_discount_type,
								"discount_amount" => $hotel_discount_amount,
							);
						} else {
							$room_price[] = array(
								"sale_price" => $meta['child_price'],
							);
						}
					}
				}
			} elseif ( $pricing_by == 3 ) {
				$room_options = ! empty( $meta['room-options'] ) ? $meta['room-options'] : [];

				if ( ! empty( $room_options ) ) {
					foreach ( $room_options as $room_option ) {
						$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
						if ( $option_price_type == 'per_room' ) {
							$room_meta_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
							$discount_price  = self::apply_discount( $room_meta_price, $hotel_discount_type, $hotel_discount_amount );

							if ( $discount_price != 0 ) {
								$room_price[] = array(
									"regular_price" => $room_meta_price,
									"sale_price"    => $discount_price,
									"discount_type" => $hotel_discount_type,
									"discount_amount" => $hotel_discount_amount,
								);
							} else {
								$room_price[] = array(
									"sale_price" => $room_meta_price,
								);
							}
						} elseif ( $option_price_type == 'per_person' ) {
							if ( $this->price_settings == 'adult' || $this->price_settings == 'all' ) {
								$option_adult_price   = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
								$discount_adult_price = self::apply_discount( $option_adult_price, $hotel_discount_type, $hotel_discount_amount );

								if ( $discount_adult_price != 0 ) {
									$room_price[] = array(
										"regular_price" => $option_adult_price,
										"sale_price"    => $discount_adult_price,
										"discount_type" => $hotel_discount_type,
										"discount_amount" => $hotel_discount_amount,
									);
								} else {
									$room_price[] = array(
										"sale_price" => $option_adult_price,
									);
								}
							}
							if ( $this->price_settings == 'child' || $this->price_settings == 'all' ) {
								$option_child_price   = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;
								$discount_child_price = self::apply_discount( $option_child_price, $hotel_discount_type, $hotel_discount_amount );

								if ( $discount_child_price != 0 ) {
									$room_price[] = array(
										"regular_price" => $option_child_price,
										"sale_price"    => $discount_child_price,
										"discount_type" => $hotel_discount_type,
										"discount_amount" => $hotel_discount_amount,
									);
								} else {
									$room_price[] = array(
										"sale_price" => $option_child_price,
									);
								}
							}
						}
					}
				}
			}
		}

	    $room_price     = array_filter( $room_price );
	    $min_sale_price = ! empty( $room_price ) ? min( array_column( $room_price, 'sale_price' ) ) : 0;

	    if ( ! empty( $room_price ) ):
		    $min_regular_price = 0;
		    $min_discount_type = 'none';
		    $min_discount_amount = 0;

		    array_walk( $room_price, function ( $value ) use ( $min_sale_price, &$min_regular_price, &$min_discount_type, &$min_discount_amount ) {
			    if ( is_array( $value ) && count( $value ) > 0 ) {
				    if ( array_key_exists( "regular_price", $value ) ) {
					    if ( $value["sale_price"] == $min_sale_price ) {
						    $min_regular_price = $value["regular_price"];

						    if ( array_key_exists( "discount_type", $value ) ) {
							    $min_discount_type = $value["discount_type"];
						    }
						    if ( array_key_exists( "discount_amount", $value ) ) {
							    $min_discount_amount = $value["discount_amount"];
						    }
					    }
				    }

			    }
		    } );
	    endif;

        return array(
            'min_sale_price' => $min_sale_price ?? 0,
            'min_regular_price' => $min_regular_price ?? 0,
            'min_discount_type' => $min_discount_type ?? 'none',
            'min_discount_amount' => $min_discount_amount ?? 0,
        );
    }

	/*
	 * Get min price html
	 */
	function get_min_price_html($period = '') {
		$min_max_price = $this->get_min_price($period);
		$regular_price = $min_max_price['min_regular_price'];
		$sale_price    = $min_max_price['min_sale_price'];

		$price_html = '';
		if ( ! empty( $min_max_price ) ) {
			$price_html .= esc_html__( "From ", "tourfic" );
			if ( $regular_price != 0 ) {
				$price_html .= wc_format_sale_price( $regular_price, $sale_price );
			} else {
				$price_html .= wp_kses_post( wc_price( $sale_price ) ) . " ";
			}
		}

		return $price_html;
	}

    /*
	 * Get per person / per room price
	 */
	function get_per_price( $option_key = '' ) {
		$meta     = $this->meta;
		$avail_by_date = ! empty( $meta["avil_by_date"] ) ? $meta["avil_by_date"] : false;
		$pricing_by    = ! empty( $meta["pricing-by"] ) ? $meta["pricing-by"] : 1;
		$current_date  = strtotime( "today" );
		$hotel_discount_type   = ! empty( $meta["discount_hotel_type"] ) ? $meta["discount_hotel_type"] : "none";
		$hotel_discount_amount = ! empty( $meta["discount_hotel_price"] ) ? $meta["discount_hotel_price"] : 0;
		$option_type = '';

		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && $avail_by_date == true) {
			$repeat_by_date  = ! empty( $meta['avail_date'] ) ? json_decode( $meta['avail_date'], true ) : [];
			$discount_prices = array();
			$prices          = array();

            foreach ( $repeat_by_date as $date => $data ) {
				if ($current_date < strtotime($data['check_in']) && $data['status'] == 'available') {
					if ( $pricing_by == '1' ) {
						if(!empty($data['price'])) {
							$prices[]          = ! empty( $data['price'] ) ? $data['price'] : 0;
							$discount_prices[] = self::apply_discount( $data['price'], $hotel_discount_type, $hotel_discount_amount );
						}
					} else if ( $pricing_by == '2' ) {
						if(!empty($data['adult_price'])) {
							$prices[]          = ! empty( $data['adult_price'] ) ? $data['adult_price'] : 0;
							$discount_prices[] = self::apply_discount( $data['adult_price'], $hotel_discount_type, $hotel_discount_amount );
						}
					} else {
						$options_count = $data['options_count'] ?? 0;
						if ( $option_key == '' ) {
							for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
								if ( $data[ 'tf_room_option_' . $i ] == '1' && $data[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
									$option_price = $data[ 'tf_option_room_price_' . $i ] ?? 0;
								} else if ( $data[ 'tf_room_option_' . $i ] == '1' && $data[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
									$option_price = $data[ 'tf_option_adult_price_' . $i ] ?? 0;
								}
								if ( ! empty( $option_price ) ) {
									$prices[]          = $option_price;
									$discount_prices[] = self::apply_discount( $option_price, $hotel_discount_type, $hotel_discount_amount );
								}
							}
						} else {
							if ( array_key_exists( 'tf_room_option_' . $option_key, $data) &&  $data[ 'tf_room_option_' . $option_key ] == '1' && $data[ 'tf_option_pricing_type_' . $option_key ] == 'per_room' ) {
								$option_price = $data[ 'tf_option_room_price_' . $option_key ] ?? 0;
							} else if ( array_key_exists( 'tf_room_option_' . $option_key, $data) && $data[ 'tf_room_option_' . $option_key ] == '1' && $data[ 'tf_option_pricing_type_' . $option_key ] == 'per_person' ) {
								$option_price = $data[ 'tf_option_adult_price_' . $option_key ] ?? 0;
							}
							if ( ! empty( $option_price ) ) {
								$prices[]          = $option_price;
								$discount_prices[] = self::apply_discount( $option_price, $hotel_discount_type, $hotel_discount_amount );
							}
						}
					}
				}
            }

			if ( is_array( $prices ) && count( $prices ) > 0 ) {
				foreach ( $prices as $price ) {
					if ( $hotel_discount_type == "percent" ) {
						$discount_prices[] = ! empty( $price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					} else if ( $hotel_discount_type == "fixed" ) {
						$discount_prices[] = ! empty( $price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					}
				}
			}

			if ( ! empty( $prices ) ) {
				$range_price          = [];
				$discount_range_price = array();

				foreach ( $prices as $single ) {
					if ( ! empty( $single ) ) {
						$range_price[] = $single;
					}
				}

				foreach ( $discount_prices as $discount_single ) {
					if ( ! empty( $discount_single ) ) {
						$discount_range_price[] = $discount_single;
					}
				}

				if ( sizeof( $range_price ) > 1 ) {

					$discount_price = $discount_prices ? wc_price( min( $discount_prices ) ) : wc_price( 0 );
					$price          = $prices ? wc_price( min( $prices ) ) : wc_price( 0 );

				} else {
					$price          = ! empty( $range_price[0] ) ? $range_price[0] : 0;
					$discount_price = ! empty( $discount_range_price[0] ) ? $discount_range_price[0] : '';

					$price          = wc_price( $price );
					$discount_price = wc_price( $discount_price );

				}
			} else {
				if ( $pricing_by == '1' ) {
					$price          = ! empty( $meta['price'] ) ? $meta['price'] : 0;
					$discount_price = 0;

					if ( $hotel_discount_type == "percent" ) {
						$discount_price = ! empty( $price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					} else if ( $hotel_discount_type == "fixed" ) {
						$discount_price = ! empty( $price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					}

					$price          = wc_price( $price );
					$discount_price = wc_price( $discount_price );

				} elseif ( $pricing_by == '2' ) {
					$price          = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
					$discount_price = 0;

					if ( $hotel_discount_type == "percent" ) {
						$discount_price = ! empty( $price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					} else if ( $hotel_discount_type == "fixed" ) {
						$discount_price = ! empty( $price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
					}

					$price          = wc_price( $price );
					$discount_price = wc_price( $discount_price );
				}  elseif ( $pricing_by == '3' ) {
					$room_options = ! empty( $meta['room-options'] ) ? $meta['room-options'] : [];

					$option_prices = [];
					if ( ! empty( $room_options ) ) {
						foreach ( $room_options as $room_option ) {
							$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
							if ( $option_price_type == 'per_room' ) {
								$option_prices[] = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
							} elseif ( $option_price_type == 'per_person' ) {
								$option_prices[] = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
							}
						}
					}
					$price = min( $option_prices );

					$discount_price = self::apply_discount($price, $hotel_discount_type, $hotel_discount_amount);
					$price          = wc_price( $price );
					$discount_price = wc_price( $discount_price );
				}
			}
		} else {

			if ( $pricing_by == '1' ) {
				$price          = ! empty( $meta['price'] ) ? $meta['price'] : 0;
				$discount_price = 0;

				if ( $hotel_discount_type == "percent" ) {
					$discount_price = ! empty( $price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
				}
				if ( $hotel_discount_type == "fixed" ) {
					$discount_price = ! empty( $price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( ( (int) $price - (int) $hotel_discount_amount ), 2 ) ) ) : 0;
				}

				$discount_price = ( $hotel_discount_type != "none" && $hotel_discount_amount != 0 ) ? wc_price( $discount_price ) : 0;
				$price          = wc_price( $price );

			} elseif ( $pricing_by == '2' ) {
				$price          = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
				$discount_price = 0;

				if ( $hotel_discount_type == "percent" ) {
					$discount_price = ! empty( $price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - ( ( (int) $price / 100 ) * (int) $hotel_discount_amount ), 2 ) ) ) : 0;
				} else if ( $hotel_discount_type == "fixed" ) {
					$discount_price = ! empty( $price ) ? floatval( preg_replace( '/[^\d.]/', '', number_format( (int) $price - (int) $hotel_discount_amount ), 2 ) ) : 0;
				}
				$discount_price = wc_price( $discount_price );
				$price          = wc_price( $price );
			}  elseif ( $pricing_by == '3' ) {
				$room_options = ! empty( $meta['room-options'] ) ? $meta['room-options'] : [];

				$option_prices = [];
				if ( ! empty( $room_options ) ) {
					foreach ( $room_options as $key => $room_option ) {
						if ( $option_key == '' ) {
							$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
							if ( $option_price_type == 'per_room' ) {
								$option_prices[] = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
							} elseif ( $option_price_type == 'per_person' ) {
								$option_prices[] = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
							}
						} else {
							if ( $key == $option_key ) {
								$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
								if ( $option_price_type == 'per_room' ) {
									$option_prices[] = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
								} elseif ( $option_price_type == 'per_person' ) {
									$option_prices[] = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
								}
							}
						}
					}
				}
				$price = min( $option_prices );

				$discount_price = self::apply_discount($price, $hotel_discount_type, $hotel_discount_amount);
				$discount_price = wc_price( $discount_price );
				$price          = wc_price( $price );
			}
		}

		return array(
			'price'          => $price,
			'discount_price' => $discount_price
		);
	}

	/*
	 * Get per person / per room price html
	 */
	function get_per_price_html( $option_key = '', $design ='' ) {
		$meta      = $this->meta;
		$price_arr      = $this->get_per_price( $option_key );
		$price          = $price_arr['price'];
		$discount_price = $price_arr['discount_price'];

		$pricing_by          = $meta['pricing-by'] ?? 1;
		$multi_by_date       = $meta['price_multi_day'] ?? 0;
		$hotel_discount_type = $meta['discount_hotel_type'] ?? 'none';

		$room_options = ! empty( $meta['room-options'] ) ? $meta['room-options'] : [];
		$option_price_type = ! empty( $room_options[$option_key]['option_pricing_type'] ) ? $room_options[$option_key]['option_pricing_type'] : 'per_room';

		$$design = !empty($design) ? $design : $this->template;

		if ( $$design == 'design-2' ) {
			?>
            <span class="tf-price">
                <span class="discount-price">
                    <?php esc_html_e( 'From ', 'tourfic' ); ?>
	                <?php if ( ! empty( $discount_price ) && $hotel_discount_type != "none" && ! empty( $hotel_discount_type ) ) { ?>
                        <del><?php echo wp_kses_post( $price ); ?></del>
	                <?php } ?>
                </span>
                <span class="sale-price">
                    <?php
                    if ( ! empty( $discount_price ) && $hotel_discount_type != "none" && ! empty( $hotel_discount_type ) ) {
	                    echo wp_kses_post( $discount_price );
                    } else {
	                    echo wp_kses_post( $price ) . " ";
                    }
                    ?>
                    <span class="booking-type">
                    <?php
                    if ( $multi_by_date ) {
	                    esc_html_e( '/night', 'tourfic' );
                    } else {
	                    esc_html_e( '/day', 'tourfic' );
                    } ?>
                    </span>
                </span>
            </span>
			<?php
		} else {
			if ( ! empty( $discount_price ) && $hotel_discount_type != "none" && ! empty( $hotel_discount_type ) ) {
				echo '<span class="tf-price"><del>' . wp_kses_post($price) . '</del> ' . wp_kses_post($discount_price) . '</span>';
			} else if ( $hotel_discount_type == "none" ) {
				echo '<span class="tf-price">' . wp_kses_post($price) . '</span>';
			}
			?>
            <div class="price-per-night">
	            <?php
	            if ( $multi_by_date ) {
		            if ( $pricing_by == 1 ) {
			            echo esc_html__( 'per night', 'tourfic' );
		            } elseif ( $pricing_by == 2 ) {
			            echo esc_html__( 'per person/night', 'tourfic' );
		            } elseif ( $pricing_by == 3 && $option_price_type == 'per_room' ) {
			            echo esc_html__( 'per night', 'tourfic' );
		            } elseif ( $pricing_by == 3 && $option_price_type == 'per_person' ) {
			            echo esc_html__( 'per person/night', 'tourfic' );
		            }
	            } else {
		            if ( $pricing_by == 1 ) {
			            echo esc_html__( 'per day', 'tourfic' );
		            } elseif ( $pricing_by == 2 ) {
			            echo esc_html__( 'per person/day', 'tourfic' );
		            } elseif ( $pricing_by == 3 && $option_price_type == 'per_room' ) {
			            echo esc_html__( 'per day', 'tourfic' );
		            } elseif ( $pricing_by == 3 && $option_price_type == 'per_person' ) {
			            echo esc_html__( 'per person/day', 'tourfic' );
		            }
	            } ?>
            </div>
			<?php
		}
	}

	function get_total_price() {
		$meta = $this->meta;
		$period    = $this->period;

		$pricing_by    = $meta['pricing-by'] ?? 1;
		$avail_by_date = $meta['avil_by_date'] ?? 1;

		// Total person calculation
		$persons     = ! empty( $this->persons ) ? $this->persons : array();
		$adult_count = ! empty( $persons['adult'] ) ? $persons['adult'] : 0;
		$child_count = ! empty( $persons['child'] ) ? $persons['child'] : 0;
		$days        = ! empty( $this->days ) ? $this->days : 0;

		if ( $avail_by_date == 1 && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {

			$availability_price = $this->get_availability_total_price();
			$total_price        = $availability_price['total_price'];
			$option_title       = $availability_price['option_title'];

		} else {

			if ( $pricing_by == '1' ) {
				$total_price = $meta['price'] ?? 0;
				$total_price = $this->calculate_discount( $total_price );
			} elseif ( $pricing_by == '2' ) {
				$adult_price = $meta['adult_price'] ?? 0;
				$child_price = $meta['child_price'] ?? 0;
				$adult_price = $this->calculate_discount( $adult_price );
				$child_price = $this->calculate_discount( $child_price );

				$adult_price = (int) $adult_price * (int) $adult_count;
				$child_price = (int) $child_price * (int) $child_count;
				$total_price = (int) $adult_price + (int) $child_price;
			} elseif ( $pricing_by == '3' ) {
				$room_options = ! empty( $meta['room-options'] ) ? $meta['room-options'] : [];
				$unique_id    = ! empty( $meta['unique_id'] ) ? $meta['unique_id'] : '';

				if ( ! empty( $room_options ) ) {
					foreach ( $room_options as $room_option_key => $room_option ) {
						$_option_id = $unique_id . '_' . $room_option_key;
						if ( $_option_id == $this->option_id ) {
							$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
							if ( $option_price_type == 'per_room' ) {
								$total_price = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
								$total_price = $this->calculate_discount( $total_price );
							} elseif ( $option_price_type == 'per_person' ) {
								$option_adult_price = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
								$option_child_price = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;

								$option_adult_price = $this->calculate_discount( $option_adult_price );
								$option_child_price = $this->calculate_discount( $option_child_price );
								$total_price        = ( $option_adult_price * $adult_count ) + ( $option_child_price * $child_count );
							}

							$option_title = $room_option['option_title'];
						}
					}
				}
			}

			$total_price = $total_price * ( $this->room_number * $days );

		}

		return array(
			'total_price'  => $total_price,
			'adult_price'  => $adult_price ?? 0,
			'child_price'  => $child_price ?? 0,
			'option_title' => $option_title ?? '',
		);
	}

	function get_availability_total_price() {
		$meta  = $this->meta;
		$period     = $this->period;
		$pricing_by = $meta['pricing-by'] ?? 1;

		// Total person calculation
		$persons     = ! empty( $this->persons ) ? $this->persons : array();
		$adult_count = ! empty( $persons['adult'] ) ? $persons['adult'] : 0;
		$child_count = ! empty( $persons['child'] ) ? $persons['child'] : 0;

		$total_price = 0;
		$avail_date  = ! empty( $meta['avail_date'] ) ? json_decode( $meta['avail_date'], true ) : [];
		foreach ( $period as $date ) {

			$available_rooms = array_values( array_filter( $avail_date, function ( $date_availability ) use ( $date ) {
				$date_availability_from = strtotime( $date_availability['check_in'] . ' 00:00' );
				$date_availability_to   = strtotime( $date_availability['check_out'] . ' 23:59' );

				return strtotime( $date->format( 'd-M-Y' ) ) >= $date_availability_from && strtotime( $date->format( 'd-M-Y' ) ) <= $date_availability_to;
			} ) );

			if ( is_iterable( $available_rooms ) && count( $available_rooms ) >= 1 ) {
				$room_price  = ! empty( $available_rooms[0]['price'] ) ? $available_rooms[0]['price'] : $meta['price'];
				$adult_price = ! empty( $available_rooms ) ? $available_rooms[0]['adult_price'] : $meta['adult_price'];
				$child_price = ! empty( $available_rooms ) ? $available_rooms[0]['child_price'] : $meta['child_price'];

				//discount price
				$room_price  = $this->calculate_discount( $room_price );
				$adult_price = $this->calculate_discount( $adult_price );
				$child_price = $this->calculate_discount( $child_price );

				if ( $pricing_by == '1' ) {
					$total_price += $room_price;
				} elseif ( $pricing_by == '2' ) {
					$total_price += ( $adult_price * $adult_count ) + ( $child_price * $child_count );
				} elseif ( $pricing_by == '3' ) {
					$data          = $available_rooms[0];
					$options_count = $data['options_count'] ?? 0;
					$unique_id     = ! empty( $meta['unique_id'] ) ? $meta['unique_id'] : '';

					for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
						$_option_id = $unique_id . '_' . $i;
						if ( $_option_id == $this->option_id ) {
							if ( $data[ 'tf_room_option_' . $i ] == '1' && $data[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
								$room_price  = ! empty( $data[ 'tf_option_room_price_' . $i ] ) ? $data[ 'tf_option_room_price_' . $i ] : 0;
								$room_price  = $this->calculate_discount( $room_price );
								$total_price += $room_price;
							} else if ( $data[ 'tf_room_option_' . $i ] == '1' && $data[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
								$adult_price = ! empty( $data[ 'tf_option_adult_price_' . $i ] ) ? $data[ 'tf_option_adult_price_' . $i ] : 0;
								$child_price = ! empty( $data[ 'tf_option_child_price_' . $i ] ) ? $data[ 'tf_option_child_price_' . $i ] : 0;
								$adult_price = $this->calculate_discount( $adult_price );
								$child_price = $this->calculate_discount( $child_price );

								$total_price += ( $adult_price * $adult_count ) + ( $child_price * $child_count );
							}

							$option_title = $data[ 'tf_option_title_' . $i ];
						}
					}
				}
			};

		}

		$total_price = $total_price * $this->room_number;

		return array(
			'total_price'  => $total_price ?? 0,
			'option_title' => $option_title ?? ''
		);
	}

	static function get_min_max_price_from_all_room() {
		$price_settings = ! empty( Helper::tfopt( 'hotel_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'hotel_archive_price_minimum_settings' ) : 'all';
		$current_date   = strtotime( "today" );

		$room_args    = array(
			'posts_per_page' => - 1,
			'post_type'      => 'tf_room',
			'post_status'    => 'publish'
		);
		$room_query   = new \WP_Query( $room_args );
		$min_max_price = array();

		if ( $room_query->have_posts() ):
			while ( $room_query->have_posts() ) : $room_query->the_post();
				$room_meta     = get_post_meta( get_the_ID(), 'tf_room_opt', true );
				$pricing_by    = $room_meta['pricing-by'] ?? 1;
				$avail_by_date = $room_meta['avil_by_date'] ?? 1;
				$room_options  = $room_meta['room-options'] ?? [];

				if ( $pricing_by == 1 && ! empty( $room_meta['price'] ) ) {
					$min_max_price[] = $room_meta['price'];
				}
				if ( $pricing_by == 2 && ! empty( $room_meta['adult_price'] ) ) {
					$min_max_price[] = $room_meta['adult_price'];
				}
				if ( $pricing_by == 2 && ! empty( $room_meta['child_price'] ) ) {
					$min_max_price[] = $room_meta['child_price'];
				}

				if ( $pricing_by == 3 && ! empty( $room_options ) ) {
					foreach ( $room_options as $room_option ) {
						$option_price_type = ! empty( $room_option['option_pricing_type'] ) ? $room_option['option_pricing_type'] : 'per_room';
						if ( $option_price_type == 'per_room' ) {
							$min_max_price[] = ! empty( $room_option['option_price'] ) ? floatval( $room_option['option_price'] ) : 0;
						} elseif ( $option_price_type == 'per_person' ) {
							if ( $price_settings == 'adult' || $price_settings == 'all' ) {
								$min_max_price[] = ! empty( $room_option['option_adult_price'] ) ? floatval( $room_option['option_adult_price'] ) : 0;
							}
							if ( $price_settings == 'child' || $price_settings == 'all' ) {
								$min_max_price[] = ! empty( $room_option['option_child_price'] ) ? floatval( $room_option['option_child_price'] ) : 0;
							}
						}
					}
				}

				if ( $avail_by_date == '1' && function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $room_meta['avail_date'] ) ) {
					$avail_date = json_decode( $room_meta['avail_date'], true );
					if ( ! empty( $avail_date ) && is_array( $avail_date ) ) {
						foreach ( $avail_date as $singleavailroom ) {
							if ( $current_date < strtotime( $singleavailroom['check_in'] ) && $singleavailroom['status'] == 'available' ) {
								if ( $pricing_by == 1 && ! empty( $singleavailroom['price'] ) ) {
									$min_max_price[] = $singleavailroom['price'];
								}
								if ( $pricing_by == 2 && ! empty( $singleavailroom['adult_price'] ) ) {
									$min_max_price[] = $singleavailroom['adult_price'];
								}
								if ( $pricing_by == 2 && ! empty( $singleavailroom['child_price'] ) ) {
									$min_max_price[] = $singleavailroom['child_price'];
								}
								if ( $pricing_by == 3 && ! empty( $singleavailroom['options_count'] ) ) {
									$options_count = $singleavailroom['options_count'] ?? 0;
									for ( $i = 0; $i <= $options_count - 1; $i ++ ) {
										if ( $singleavailroom[ 'tf_room_option_' . $i ] == '1' && $singleavailroom[ 'tf_option_pricing_type_' . $i ] == 'per_room' ) {
											$min_max_price[] = $singleavailroom[ 'tf_option_room_price_' . $i ] ?? 0;
										} else if ( $singleavailroom[ 'tf_room_option_' . $i ] == '1' && $singleavailroom[ 'tf_option_pricing_type_' . $i ] == 'per_person' ) {
											if ( $price_settings == 'adult' || $price_settings == 'all' ) {
												$min_max_price[] = $singleavailroom[ 'tf_option_adult_price_' . $i ] ?? 0;
											}
											if ( $price_settings == 'child' || $price_settings == 'all' ) {
												$min_max_price[] = $singleavailroom[ 'tf_option_child_price_' . $i ] ?? 0;
											}
										}
									}
								}
							}
						}
					}
				}
			endwhile;
		endif;
		wp_reset_query();

		return array(
			'min' => ! empty( $min_max_price ) && min( $min_max_price ) != max( $min_max_price ) ? min( $min_max_price ) : 0,
			'max' => ! empty( $min_max_price ) ? max( $min_max_price ) : 0,
		);
	}
}