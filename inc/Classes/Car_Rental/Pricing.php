<?php

namespace Tourfic\Classes\Car_Rental;
defined( 'ABSPATH' ) || exit;

class Pricing {

	use \Tourfic\Traits\Singleton;

    public function __construct() {

	}

    // all price will be calculate here
	static function set_total_price( $meta, $tf_pickup_date='', $tf_dropoff_date='' ) {
        
		$initial_pricing = !empty($meta["car_rent"]) ? $meta["car_rent"] : 0;
		$pricing_type = !empty($meta["pricing_type"]) ? $meta["pricing_type"] : 'day_hour';
		$discount_type = !empty($meta["discount_type"]) ? $meta["discount_type"] : 'none';
		$discount_price = !empty($meta["discount_price"]) ? $meta["discount_price"] : '';

        // if('day_hour'==$pricing_type){
        //     $pricing = !empty($meta["day_prices"]) ? $meta["day_prices"] : '';
        // }

        if(!empty($tf_pickup_date) && !empty($tf_pickup_date)){
            if('date'==$pricing_type){
                $pricing = !empty($meta["date_prices"]) ? $meta["date_prices"] : '';

                $all_prices = [];
                $result = array();
                foreach ($pricing as $entry) {
                    $startDate = strtotime($entry['date']['from']);
                    $endDate = strtotime($entry['date']['to']);
                    $price = $entry['price'];

                    while ($startDate <= $endDate) {  // Adjusted to include the end date
                        $dateKey = date("Y/m/d", $startDate);

                        // Check if the date is already in the result array
                        if (isset($result[$dateKey])) {
                            
                        } else {
                            $result[$dateKey] = $price;
                        }
                        $startDate = strtotime("+1 day", $startDate);
                    }
                }

                // Convert the dates to timestamps
                $pickupDate = strtotime($tf_pickup_date);
                $dropoffDate = strtotime($tf_dropoff_date);

                // Initialize total price
                $totalPrice = 0;

                // Loop through each day in the range
                while ($pickupDate <= $dropoffDate) {
                    $currentDate = date("Y/m/d", $pickupDate);
                    
                    // Check if the date exists in the $result array
                    if (isset($result[$currentDate])) {
                        $totalPrice += $result[$currentDate];
                    } else {
                        $totalPrice += $initial_pricing;
                    }

                    // Move to the next day
                    $pickupDate = strtotime("+1 day", $pickupDate);
                }

                if('fixed'==$discount_type && !empty($discount_price)){
                    $all_prices['sale_price'] = $totalPrice - $discount_price;
                }
                if('percent'==$discount_type && !empty($discount_price)){
                    $discount_price = ($totalPrice * $discount_price)/100;
                    $all_prices['sale_price'] = $totalPrice - $discount_price;
                }

                if(empty($all_prices['sale_price'])){
                    $all_prices['sale_price'] = $totalPrice;
                }else{
                    $all_prices['regular_price'] = $totalPrice;
                }
                
            }
        }else{
            if('fixed'==$discount_type && !empty($discount_price)){
                $all_prices['sale_price'] = $initial_pricing - $discount_price;
            }
            if('percent'==$discount_type && !empty($discount_price)){
                $discount_price = ($initial_pricing * $discount_price)/100;
                $all_prices['sale_price'] = $initial_pricing - $discount_price;
            }

            if(empty($all_prices['sale_price'])){
                $all_prices['sale_price'] = $initial_pricing;
            }else{
                $all_prices['regular_price'] = $initial_pricing;
            }
        }

        return $all_prices;
    }

    // taxable car or not
	static function is_taxable( $meta ) {
        $is_taxable = !empty($meta["is_taxable"]) ? $meta["is_taxable"] : '';
        $taxable_class = !empty($meta["taxable_class"]) ? $meta["taxable_class"] : '';
        if($is_taxable && !empty($taxable_class)){
            return  esc_html_e("With Taxes", "tourfic");
        }else{
            return  esc_html_e("Without Taxes", "tourfic");
        }
    }
}