<?php

namespace Tourfic\Classes\Apartment;
class Availability {

	public static function instance( $apt_id ) {
		return new self( $apt_id );
	}

	protected $apt_id;
	protected $meta;
	protected $days;
	protected $period;
	protected $check_in;
	protected $check_out;
	protected $persons;
	public static $is_available = false;

	

	function __construct( $apt_id ){
		$this->apt_id = $apt_id;
		$this->meta = get_post_meta( $this->apt_id, 'tf_apartment_opt', true );
	}

	public function set_dates($check_in, $check_out) {
		if ( !empty($check_in) && !empty($check_out) ) {
			$this->check_in = $check_in;
			$this->check_out = $check_out;
			
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

	public function set_persons($adult, $child, $infant) {
		$this->persons = array(
			'adult' => !empty($adult) ? $adult : 0,
			'child' => !empty($child) ? $child : 0,
			'infant' => !empty($infant) ? $infant : 0,
		);

		return $this;
	}

	function get_total_price() {

		$meta = $this->meta;
		$check_in_stt = strtotime($this->check_in);
		$check_out_stt = strtotime($this->check_out);
		$enable_availability = !empty( $meta['enable_availability'] ) ? $meta['enable_availability'] : '';
		$pricing_type = !empty( $meta['pricing_type'] ) ? $meta['pricing_type'] : 'per_night';
		$total_price = 0;
		$prices = array();
		
		if ( $enable_availability === '1' && function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
			$apt_availability = ! empty( $meta['apt_availability'] ) ? json_decode( $meta['apt_availability'], true ) : [];

			if ( ! empty( $apt_availability ) && is_array( $apt_availability ) ) {
				foreach ( $apt_availability as $key => $single_avail ) {

					$date_stt = strtotime($key);

					if( $date_stt > $check_in_stt && $date_stt <= $check_out_stt && $single_avail['status'] === 'available' ) {

						if ( $pricing_type === 'per_night' ) {
							// $prices[] = $date_stt > $check_in_stt && $date_stt < $check_out_stt;

						$total_price += ! empty( $single_avail['price'] ) ? intval( $single_avail['price'] ) : 0;
						} else {
							$total_price += !empty( $this->persons ) ? ( ( (int) $single_avail['adult_price'] * (int) $this->persons['adult'] ) + ( (int) $single_avail['child_price'] * (int) $this->persons["child"] ) + ( (int) $single_avail['infant_price'] * (int) $this->persons["infant"] ) ) : 0;
						}
					}
				}
			}
		}

		return $total_price;

	}

	// Apt_Availability::instance($id)->set_dates( $chekin, $checkout )->set_person()->get_total_price(); 
}