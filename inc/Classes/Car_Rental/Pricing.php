<?php

namespace Tourfic\Classes\Car_Rental;
defined( 'ABSPATH' ) || exit;

class Pricing {

	use \Tourfic\Traits\Singleton;

    public function __construct() {

	}

    // all price will be calculate here
	function set_total_price( $car_id ) {
        $meta = get_post_meta( $apt_id, 'tf_carrental_opt', true );
		$initial_pricing = !empty($meta["car_rent"]) ? $meta["car_rent"] : '';
		$pricing_type = !empty($meta["pricing_type"]) ? $meta["pricing_type"] : 'day_hour';

        if('day_hour'==$pricing_type){
            $pricing = !empty($meta["day_prices"]) ? $meta["day_prices"] : '';
        }

        if('date'==$pricing_type){
            $pricing = !empty($meta["date_prices"]) ? $meta["date_prices"] : '';
        }
    }
}