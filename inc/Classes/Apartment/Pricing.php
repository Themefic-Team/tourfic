<?php

namespace Tourfic\Classes\Apartment;
defined( 'ABSPATH' ) || exit;

class Pricing {

	use \Tourfic\Traits\Singleton;

	public function __construct() {

	}

	protected $total_price;
	protected array $all_fees;
	protected $days;
	protected $period;

	function get_total_price() {
		return $this->total_price = array();
	}

	function check_additional_fees($apt_id) {
		$meta = get_post_meta( $apt_id, 'tf_apartment_opt', true );
		$additional_fees = !empty( $meta["additional_fees"] ) ? $meta["additional_fees"] : array();

		$additional_feesss = [];
		if ( function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($additional_fees)) {
			if ( count( $additional_fees ) > 0 ) {
				foreach ( $additional_fees as $fees ) {
					$this->all_fees[] = array(
						"label" => !empty($fees["additional_fee_label"]) ? $fees["additional_fee_label"] : '',
						"fee"   => !empty($fees["additional_fee"]) ? $fees["additional_fee"] : 0,
						"price" =>!empty($fees["additional_fee"]) ? wc_price( $fees["additional_fee"] ) : wc_price( 0 ),
						"type"  => !empty( $fees["fee_type"] ) ? $fees["fee_type"] : '',
					);
				}
			}
		} else {
			$additional_fee_label = !empty( $meta[ "additional_fee_label" ] ) ? $meta["additional_fee_label"] : '';
			$additional_fee_amount = !empty( $meta["additional_fee"] ) ? $meta["additional_fee"] : 0;
			$additional_fee_type = !empty( $meta[ "fee_type" ] ) ? $meta["fee_type"] : '';

			if($additional_fee_amount != 0) {
				$this->all_fees[] = array(
					"label" => $additional_fee_label ?? "",
					"fee"   => $additional_fee_amount,
					"price" => wc_price( $additional_fee_amount ),
					"type"  => $additional_fee_type,
				);
			}
		}

		return $this;
	}

	public function check_days($check_in, $check_out) {
		if ( !empty($check_in) && !empty($check_out) ) {
			$check_in_stt  = !empty($check_in) ? strtotime( $check_in . ' +1 day' ) : 0;
			$check_out_stt = !empty($check_out) ? strtotime( $check_out ) : 0; 
			$days = !empty($check_in_stt) && !empty($check_out_stt) ? ( ( $check_out_stt - $check_in_stt ) / ( 60 * 60 * 24 ) ) + 1 : 0;

			$tfperiod = new \DatePeriod(
				new \DateTime( $check_in . ' 00:00' ),
				new \DateInterval( 'P1D' ),
				new \DateTime( $check_out . ' 23:59' )
			);
		}
		$this->days = !empty($days) ? $days : 0;
		$this->period = !empty($tfperiod) ? $tfperiod : 0;

		return $this;
	}

	function get_additional_fees() {
		return !empty($this->all_fees) ? $this->all_fees : [];
	}
	
	function get_days() {
		return !empty($this->days) ? $this->days : 0;
	}





}