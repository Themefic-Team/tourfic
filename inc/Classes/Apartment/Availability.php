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

	// Apt_Availability::instance()->set_dates( $chekin, $checkout )->set_person()->get_total_price(); 
}