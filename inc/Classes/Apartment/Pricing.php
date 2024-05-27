<?php

namespace Tourfic\Classes\Apartment;
defined( 'ABSPATH' ) || exit;

class Pricing {

	use \Tourfic\Traits\Singleton;

	public function __construct() {

	}

	protected $total_price;
	protected array $all_fees;

	function get_total_price() {
		return $this->total_price;
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

	function get_additional_fees() {
		return !empty($this->all_fees) ? $this->all_fees : [];
	}





}