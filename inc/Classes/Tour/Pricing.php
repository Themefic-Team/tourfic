<?php

namespace Tourfic\Classes\Tour;

defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;

class Pricing {

	protected $post_id;
	protected $meta;
	protected $date;
	protected $time;
	protected $days;
	protected $period;
	protected array $persons;

	public static function instance( $post_id = '' ) {
		return new self( $post_id );
	}

	function __construct( $post_id = '' ) {
		$this->post_id = $post_id;
		$this->meta    = get_post_meta( $post_id, 'tf_tours_opt', true );
	}

	/*
	 * Set date
	 */
	function set_date( $date ) {
		$this->date = $date;

		return $this;
	}

	/*
	 * Set time
	 */
	function set_time( $time ) {
		$this->time = $time;

		return $this;
	}

	/*
	 * Set persons
	 */
	public function set_persons( $adult, $child, $infant ) {
		$this->persons = array(
			'adult'  => ! empty( $adult ) ? $adult : 0,
			'child'  => ! empty( $child ) ? $child : 0,
			'infant' => ! empty( $infant ) ? $infant : 0,
		);

		return $this;
	}

	function get_discount() {
		$meta            = $this->meta;
		$discount_type   = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
		$discount_amount = ( $discount_type == 'fixed' || $discount_type == 'percent' ) && ! empty( $meta["discount_price"] ) ? $meta["discount_price"] : 0;

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

	function get_min_price( $period = '' ) {
		$tour_price                       = [];
		$meta                             = $this->meta;
		$pricing_rule                     = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$custom_pricing_by_rule           = ! empty( $meta['custom_pricing_by'] ) ? $meta['custom_pricing_by'] : '';
		$disable_adult_price              = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child_price              = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$tour_archive_page_price_settings = ! empty( Helper::tfopt( 'tour_archive_price_minimum_settings' ) ) ? Helper::tfopt( 'tour_archive_price_minimum_settings' ) : 'all';


		if ( $pricing_rule && $pricing_rule == 'group' ) {
			if ( ! empty( $check_in_out ) ) {
				if ( ! empty( $meta['type'] ) && $meta['type'] === 'continuous' ) {
					$custom_availability = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
					if ( $custom_availability ) {
						foreach ( $meta['cont_custom_date'] as $repval ) {
							//Initial matching date array
							$show_tour = [];
							$dates     = $repval['date'];
							// Check if any date range match with search form date range and set them on array
							if ( ! empty( $period ) ) {
								foreach ( $period as $date ) {
									$show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
								}
							}
							if ( ! in_array( 0, $show_tour ) ) {
								if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'group' ) {
									if ( ! empty( $repval['group_price'] ) ) {
										$tour_price[] = $repval['group_price'];
									}
								}
								if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'person' ) {
									if ( $tour_archive_page_price_settings == "all" ) {
										if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
											$tour_price[] = $repval['adult_price'];
										}
										if ( ! empty( $repval['child_price'] ) && ! $disable_adult_price ) {
											$tour_price[] = $repval['child_price'];
										}
									}
									if ( $tour_archive_page_price_settings == "adult" ) {
										if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
											$tour_price[] = $repval['adult_price'];
										}
									}
									if ( $tour_archive_page_price_settings == "child" ) {
										if ( ! empty( $repval['child_price'] ) && ! $disable_adult_price ) {
											$tour_price[] = $repval['child_price'];
										}
									}
								}
							}
						}
					} else {
						if ( ! empty( $meta['group_price'] ) ) {
							$tour_price[] = $meta['group_price'];
						}
					}
				} else {
					if ( ! empty( $meta['group_price'] ) ) {
						$tour_price[] = $meta['group_price'];
					}
				}
			} else {
				if ( ! empty( $meta['group_price'] ) ) {
					$tour_price[] = $meta['group_price'];
				}
			}
		}
		if ( $pricing_rule && $pricing_rule == 'person' ) {
			if ( ! empty( $check_in_out ) ) {
				if ( ! empty( $meta['type'] ) && $meta['type'] === 'continuous' ) {
					$custom_availability = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;
					if ( $custom_availability ) {
						foreach ( $meta['cont_custom_date'] as $repval ) {
							//Initial matching date array
							$show_tour = [];
							$dates     = $repval['date'];
							// Check if any date range match with search form date range and set them on array
							if ( ! empty( $period ) ) {
								foreach ( $period as $date ) {
									$show_tour[] = intval( strtotime( $date->format( 'Y-m-d' ) ) >= strtotime( $dates['from'] ) && strtotime( $date->format( 'Y-m-d' ) ) <= strtotime( $dates['to'] ) );
								}
							}
							if ( ! in_array( 0, $show_tour ) ) {
								if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'group' ) {
									if ( ! empty( $repval['group_price'] ) ) {
										$tour_price[] = $repval['group_price'];
									}
								}
								if ( $custom_pricing_by_rule && $custom_pricing_by_rule == 'person' ) {
									if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
										if ( $tour_archive_page_price_settings == "all" ) {
											if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
												$tour_price[] = $repval['adult_price'];
											}
											if ( ! empty( $repval['child_price'] ) && ! $disable_adult_price ) {
												$tour_price[] = $repval['child_price'];
											}
										}
										if ( $tour_archive_page_price_settings == "adult" ) {
											if ( ! empty( $repval['adult_price'] ) && ! $disable_adult_price ) {
												$tour_price[] = $repval['adult_price'];
											}
										}
										if ( $tour_archive_page_price_settings == "child" ) {
											if ( ! empty( $repval['child_price'] ) && ! $disable_adult_price ) {
												$tour_price[] = $repval['child_price'];
											}
										}
									}
								}
							}
						}
					} else {
						if ( $tour_archive_page_price_settings == "all" ) {
							if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
								$tour_price[] = $meta['adult_price'];
							}
							if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
								$tour_price[] = $meta['child_price'];
							}
						}

						if ( $tour_archive_page_price_settings == "adult" ) {
							if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
								$tour_price[] = $meta['adult_price'];
							}
						}

						if ( $tour_archive_page_price_settings == "child" ) {
							if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
								$tour_price[] = $meta['child_price'];
							}
						}
					}
				} else {
					if ( $tour_archive_page_price_settings == "all" ) {
						if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
							$tour_price[] = $meta['adult_price'];
						}
						if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
							$tour_price[] = $meta['child_price'];
						}
					}

					if ( $tour_archive_page_price_settings == "adult" ) {
						if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
							$tour_price[] = $meta['adult_price'];
						}
					}

					if ( $tour_archive_page_price_settings == "child" ) {
						if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
							$tour_price[] = $meta['child_price'];
						}
					}
				}
			} else {
				if ( $tour_archive_page_price_settings == "all" ) {
					if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
						$tour_price[] = $meta['adult_price'];
					}
					if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
						$tour_price[] = $meta['child_price'];
					}
				}
				if ( $tour_archive_page_price_settings == "adult" ) {
					if ( ! empty( $meta['adult_price'] ) && ! $disable_adult_price ) {
						$tour_price[] = $meta['adult_price'];
					}
				}
				if ( $tour_archive_page_price_settings == "child" ) {
					if ( ! empty( $meta['child_price'] ) && ! $disable_adult_price ) {
						$tour_price[] = $meta['child_price'];
					}
				}
			}
		}

		//get the lowest price from all available room price
		$tf_tour_min_price      = min( $tour_price );
		$tf_tour_full_price     = min( $tour_price );
		$tf_tour_discount_type  = ! empty( $meta['discount_type'] ) ? $meta['discount_type'] : '';
		$tf_tour_discount_price = ! empty( $meta['discount_price'] ) ? $meta['discount_price'] : '';
        $tf_tour_min_discount = 0;
        if ( ! empty( $tf_tour_discount_type ) && ! empty( $tf_tour_min_price ) && ! empty( $tf_tour_discount_price ) ) {
			if ( $tf_tour_discount_type == "percent" ) {
				$tf_tour_min_discount = ( $tf_tour_min_price * $tf_tour_discount_price ) / 100;
				$tf_tour_min_price    = $tf_tour_min_price - $tf_tour_min_discount;
			}
			if ( $tf_tour_discount_type == "fixed" ) {
				$tf_tour_min_discount = $tf_tour_discount_price;
				$tf_tour_min_price    = $tf_tour_min_price - $tf_tour_discount_price;
			}
		}

		return array(
			'min_regular_price' => $tf_tour_full_price,
			'min_sale_price'    => $tf_tour_min_price,
			'min_discount'      => $tf_tour_min_discount,
			'max_regular_price' => max( $tour_price ),
		);
	}

	/*
	 * Get min price html
	 */
	function get_min_price_html( $period = '' ) {
		$min_max_price = $this->get_min_price( $period );
		$regular_price = $min_max_price['min_regular_price'];
		$sale_price    = $min_max_price['min_sale_price'];
		$min_discount  = $min_max_price['min_discount'];

		$price_html = '';
		if ( ! empty( $min_max_price ) ) {
			$price_html .= esc_html__( "From ", "tourfic" );
			if ( ! empty( $min_discount ) ) {
				$price_html .= wc_format_sale_price( $regular_price, $sale_price );
			} else {
				$price_html .= wp_kses_post( wc_price( $sale_price ) ) . " ";
			}
		}

		return $price_html;
	}

	function get_total_price() {
		$meta = $this->meta;

		// Total person calculation
		$persons              = ! empty( $this->persons ) ? $this->persons : array();
		$adult_count          = ! empty( $persons['adult'] ) ? $persons['adult'] : 0;
		$child_count          = ! empty( $persons['child'] ) ? $persons['child'] : 0;
		$infant_count         = ! empty( $persons['infant'] ) ? $persons['infant'] : 0;
		$total_people         = $adult_count + $child_count + $infant_count;
		$total_people_booking = $adult_count + $child_count;

		$tour_type            = ! empty( $meta['type'] ) ? $meta['type'] : '';
		$pricing_rule         = ! empty( $meta['pricing'] ) ? $meta['pricing'] : '';
		$disable_adult_price  = ! empty( $meta['disable_adult_price'] ) ? $meta['disable_adult_price'] : false;
		$disable_child_price  = ! empty( $meta['disable_child_price'] ) ? $meta['disable_child_price'] : false;
		$disable_infant_price = ! empty( $meta['disable_infant_price'] ) ? $meta['disable_infant_price'] : false;

		// Tour date
		$tour_date = $this->date;
		$tour_time = $this->time;

		if ( $tour_type == 'fixed' ) {

			if ( ! empty( $meta['fixed_availability'] ) && gettype( $meta['fixed_availability'] ) == "string" ) {
				$tf_tour_fixed_avail = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $meta['fixed_availability'] );
				$tf_tour_fixed_date  = unserialize( $tf_tour_fixed_avail );
				$start_date          = ! empty( $tf_tour_fixed_date['date']['from'] ) ? $tf_tour_fixed_date['date']['from'] : '';
				$end_date            = ! empty( $tf_tour_fixed_date['date']['to'] ) ? $tf_tour_fixed_date['date']['to'] : '';
			} else {
				$start_date = ! empty( $meta['fixed_availability']['date']['from'] ) ? $meta['fixed_availability']['date']['from'] : '';
				$end_date   = ! empty( $meta['fixed_availability']['date']['to'] ) ? $meta['fixed_availability']['date']['to'] : '';
			}

			if ( ! function_exists( "selected_day_diff" ) ) {
				function selected_day_diff( $start_date, $end_date ) {
					if ( ! empty( $start_date ) && ! empty( $end_date ) ) {

						$start_date = new \DateTime( $start_date );
						$end_date   = new \DateTime( $end_date );
						$interval   = $start_date->diff( $end_date );

						return $interval->days;
					}
				}
			}

			if ( ! function_exists( "end_date_calculation" ) ) {
				function end_date_calculation( $start_date, $difference ) {
					if ( ! empty( $start_date ) && ! empty( $difference ) ) {
						if ( str_contains( $start_date, ' - ' ) ) {
							return $start_date;

						} else {

							$start_date  = new \DateTime( $start_date );
							$new_end_day = $start_date->modify( "+ $difference day" );

							return $new_end_day->format( 'Y/m/d' );
						}
					}
				}
			}

			if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
				$day_diff = selected_day_diff( $start_date, $end_date );
			}

			if ( ! empty( $tour_type ) && ( $tour_type == "fixed" ) ) {
				$start_date = ! empty( $_POST['check-in-out-date'] ) ? sanitize_text_field( $_POST['check-in-out-date'] ) : '';
			}

			if ( ! empty( $start_date ) && ! empty( $day_diff ) ) {
				$end_date = end_date_calculation( $start_date, $day_diff );
			}

		} elseif ( $tour_type == 'continuous' ) {

			$custom_avail = ! empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : false;

			if ( $custom_avail == true ) {
				$pricing_rule = $meta['custom_pricing_by'];
			}
		}

		if ( $tour_type == 'continuous' ) {
			$start_date = $end_date = $tour_date;
		}

		/**
		 * Price by date range
		 *
		 * Tour type continuous and custom availability is true
		 */
		$tf_cont_custom_date = ! empty( $meta['cont_custom_date'] ) ? $meta['cont_custom_date'] : '';
		if ( ! empty( $tf_cont_custom_date ) && gettype( $tf_cont_custom_date ) == "string" ) {
			$tf_tour_conti_custom_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $tf_cont_custom_date );
			$tf_cont_custom_date       = unserialize( $tf_tour_conti_custom_date );
		}

		$tour = strtotime( $tour_date );
		if ( isset( $custom_avail ) && true == $custom_avail ) {
			$seasional_price = array_values( array_filter( $tf_cont_custom_date, function ( $value ) use ( $tour ) {
				$seasion_start = strtotime( $value['date']['from'] );
				$seasion_end   = strtotime( $value['date']['to'] );

				return $seasion_start <= $tour && $seasion_end >= $tour;
			} ) );
		}


		if ( $tour_type === 'continuous' && ! empty( $tf_cont_custom_date ) && ! empty( $seasional_price ) ) {
			$group_price    = ! empty( $seasional_price[0]['group_price'] ) ? $seasional_price[0]['group_price'] : 0;
			$adult_price    = ! empty( $seasional_price[0]['adult_price'] ) ? $seasional_price[0]['adult_price'] : 0;
			$children_price = ! empty( $seasional_price[0]['child_price'] ) ? $seasional_price[0]['child_price'] : 0;
			$infant_price   = ! empty( $seasional_price[0]['infant_price'] ) ? $seasional_price[0]['infant_price'] : 0;
		} else {
			$group_price    = ! empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
			$adult_price    = ! empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
			$children_price = ! empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
			$infant_price   = ! empty( $meta['infant_price'] ) ? $meta['infant_price'] : 0;
		}

		$adult_price    = $this->calculate_discount( $adult_price );
		$children_price = $this->calculate_discount( $children_price );
		$infant_price   = $this->calculate_discount( $infant_price );
		$group_price    = $this->calculate_discount( $group_price );

		# Set pricing based on pricing rule
		if ( $pricing_rule == 'group' ) {
			$total_price = $group_price;
		} else {
			$total_price = ( $adult_price * $adult_count ) + ( $child_count * $children_price ) + ( $infant_count * $infant_price );
		}

		return $total_price;
	}

	static function get_min_max_price_from_all_tour() {
		$tf_tour_args = array(
			'posts_per_page' => - 1,
			'post_type'      => 'tf_tours',
			'post_status'    => 'publish'
		);

		$tftours_min_max_query = new \WP_Query( $tf_tour_args );
		$tftours_min_maxprices = array();

		if ( $tftours_min_max_query->have_posts() ):
			while ( $tftours_min_max_query->have_posts() ) : $tftours_min_max_query->the_post();

				$meta = get_post_meta( get_the_ID(), 'tf_tours_opt', true );
				if ( ! empty( $meta['adult_price'] ) ) {
					$tftours_min_maxprices[] = $meta['adult_price'];
				}
				if ( ! empty( $meta['child_price'] ) ) {
					$tftours_min_maxprices[] = $meta['child_price'];
				}
				if ( ! empty( $meta['infant_price'] ) ) {
					$tftours_min_maxprices[] = $meta['infant_price'];
				}
				if ( ! empty( $meta['group_price'] ) ) {
					$tftours_min_maxprices[] = $meta['group_price'];
				}
				if ( ! empty( $meta['cont_custom_date'] ) ) {
					foreach ( $meta['cont_custom_date'] as $minmax ) {
						if ( ! empty( $minmax['adult_price'] ) ) {
							$tftours_min_maxprices[] = $minmax['adult_price'];
						}
						if ( ! empty( $minmax['child_price'] ) ) {
							$tftours_min_maxprices[] = $minmax['child_price'];
						}
						if ( ! empty( $minmax['infant_price'] ) ) {
							$tftours_min_maxprices[] = $minmax['infant_price'];
						}
						if ( ! empty( $minmax['group_price'] ) ) {
							$tftours_min_maxprices[] = $minmax['group_price'];
						}
					}
				}
			endwhile;

		endif;
		wp_reset_query();
		if ( ! empty( $tftours_min_maxprices ) && count( $tftours_min_maxprices ) > 1 ) {
			$tour_max_price_val = max( $tftours_min_maxprices );
			$tour_min_price_val = min( $tftours_min_maxprices );
			if ( $tour_max_price_val == $tour_min_price_val ) {
				$tour_max_price = max( $tftours_min_maxprices );
				$tour_min_price = 1;
			} else {
				$tour_max_price = max( $tftours_min_maxprices );
				$tour_min_price = min( $tftours_min_maxprices );
			}
		}
		if ( ! empty( $tftours_min_maxprices ) && count( $tftours_min_maxprices ) == 1 ) {
			$tour_max_price = max( $tftours_min_maxprices );
			$tour_min_price = 1;
		}
		if ( empty( $tftours_min_maxprices ) ) {
			$tour_max_price = 0;
			$tour_min_price = 0;
		}

		return array(
			'min' => $tour_min_price,
			'max' => $tour_max_price,
		);
	}

	public function get_min_max_price() {
		$meta = $this->meta;
		$min_max_prices = array();

		$pricing_type = !empty( $meta['pricing'] ) ? $meta['pricing'] : 'person';
		$adult_price = !empty( $meta['adult_price'] ) ? $meta['adult_price'] : 0;
		$child_price = !empty( $meta['child_price'] ) ? $meta['child_price'] : 0;
		$group_price = !empty( $meta['group_price'] ) ? $meta['group_price'] : 0;
		$custom_avail = !empty( $meta['custom_avail'] ) ? $meta['custom_avail'] : 0;
		$custom_availability = !empty( $meta['cont_custom_date'] ) && $custom_avail ? $meta['cont_custom_date'] : array();
		$availability_pricing_type = !empty( $meta["custom_pricing_by"] ) ? $meta["custom_pricing_by"] : 'person';

		if ( $pricing_type == 'group' ) {
			$min_max_prices[] = $group_price;
		} else {
			$min_max_prices[] = $adult_price;
			$min_max_prices[] = $child_price;
		}

		if ( !empty( $custom_availability ) ) {
			foreach ( $custom_availability as $custom_price ) {
				if ( $availability_pricing_type == 'group' ) {
					$min_max_prices[] = !empty( $custom_price['group_price'] ) ? $custom_price['group_price'] : 0;
				} else {
					$min_max_prices[] = !empty( $custom_price['adult_price'] ) ? $custom_price['adult_price'] : 0;
					$min_max_prices[] = !empty( $custom_price['child_price'] ) ? $custom_price['child_price'] : 0;
				}
			}
		}

		return array(
			'min' => min( $min_max_prices ),
			'max' => max( $min_max_prices ),
		);
	}
}