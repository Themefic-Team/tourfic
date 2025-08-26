<?php

namespace Tourfic\Classes\Car_Rental;
defined( 'ABSPATH' ) || exit;

class Pricing {

	use \Tourfic\Traits\Singleton;

    public function __construct() {

	}

    // all price will be calculate here
	static function set_total_price( $meta, $tf_pickup_date='', $tf_dropoff_date='', $tf_pickup_time='', $tf_dropoff_time='' ) {

		$pricing_by = !empty($meta["price_by"]) ? $meta["price_by"] : 'day';
		$initial_pricing = !empty($meta["car_rent"]) ? $meta["car_rent"] : 0;

        $price_type = $pricing_by;

		$pricing_type = !empty($meta["pricing_type"]) ? $meta["pricing_type"] : 'day_hour';
		$discount_type = !empty($meta["discount_type"]) ? $meta["discount_type"] : 'none';
		$discount_price = !empty($meta["discount_price"]) ? $meta["discount_price"] : '';

        $date_pricing = !empty($meta["date_prices"]) ? $meta["date_prices"] : '';
        $day_pricing = !empty($meta["day_prices"]) ? $meta["day_prices"] : '';

        if( !empty($tf_pickup_date) && !empty($tf_dropoff_date) && 'date'==$pricing_type && !empty($date_pricing) ){

            $all_prices = [];
            $result = array();
            foreach ($date_pricing as $entry) {
                $startDate = strtotime($entry['date']['from']);
                $endDate = strtotime($entry['date']['to']);
                $price = $entry['price'];

                while ($startDate <= $endDate) {  // Adjusted to include the end date
                    $dateKey = gmdate("Y/m/d", $startDate);

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
                $currentDate = gmdate("Y/m/d", $pickupDate);
                
                // Check if the date exists in the $result array
                if (isset($result[$currentDate])) {
                    $totalPrice += $result[$currentDate];
                } else {
                    $totalPrice += $initial_pricing;
                }

                // Move to the next day
                $pickupDate = strtotime("+1 day", $pickupDate);
            }

            if(empty($all_prices['sale_price'])){
                $all_prices['sale_price'] = $totalPrice;
            }else{
                $all_prices['regular_price'] = $totalPrice;
            }

        }
        elseif( !empty($tf_pickup_date) && !empty($tf_dropoff_date) && 'day_hour'==$pricing_type && !empty($day_pricing) ){
          

            // Combine date and time
            $pickup_datetime = new \DateTime("$tf_pickup_date $tf_pickup_time");
            $dropoff_datetime = new \DateTime("$tf_dropoff_date $tf_dropoff_time");

     

            // Calculate the difference
            $interval = $pickup_datetime->diff($dropoff_datetime);

            // Convert the difference to total hours
            $total_hours = ($interval->days * 24) + $interval->h + ($interval->i / 60);

            $all_prices = [];
            $result = array();
            foreach ($day_pricing as $entry) {
                $day_type = $entry['type'];
                $startDay = $entry['from_day'];
                $endDay = $entry['to_day'];
                $price = $entry['price'];

                $price_type = $day_type;

                if('day'==$day_type && $interval->days > 0){
                    // Get total days
                    $total_days = $interval->days;
                    
                    // If there are leftover hours that count as a partial day
                    if ($interval->h > 0 || $interval->i > 0) {
                        $total_days += 1;  // Add an extra day for any remaining hours
                    }
                    $total_multiply = $total_days;
                }else{
                    $total_multiply = $total_hours;
                }

                if( $startDay <= $total_multiply  && $endDay >= $total_multiply ){
                    $result['price'] = $price;
                    $result['total_multiply'] = $total_multiply;
                    break;
                }
            }
            if(!empty($result)){
                $totalPrice = $result['price'] ? $result['price'] : 0;
                $total_multiply = $result['total_multiply'] ? $result['total_multiply'] : 1;
            }else{
                $totalPrice = $initial_pricing;
                if('hour'==$pricing_by){
                    $total_multiply = $total_hours;
                }
                if('day'==$pricing_by){
                    
                    // Get total days
                    $total_days = $interval->days;
                    
                    // If there are leftover hours that count as a partial day
                    if ($interval->h > 0 || $interval->i > 0) {
                        $total_days += 1;  // Add an extra day for any remaining hours
                    }
                    $total_multiply = $total_days;
                    
                }
            }

            if(empty($all_prices['sale_price'])){
                $all_prices['sale_price'] = $totalPrice * $total_multiply;
            }else{
                $all_prices['regular_price'] = $totalPrice * $total_multiply;
            }

        }else{
            $all_prices = [];
            if(!empty($tf_pickup_date) && !empty($tf_dropoff_date)){
                // Combine date and time
                $pickup_datetime = new \DateTime("$tf_pickup_date $tf_pickup_time");
                $dropoff_datetime = new \DateTime("$tf_dropoff_date $tf_dropoff_time");

                // Calculate the difference
                $interval = $pickup_datetime->diff($dropoff_datetime);

                // Convert the difference to total hours
                $total_hours = ($interval->days * 24) + $interval->h + ($interval->i / 60);
                
                if('hour'==$pricing_by){
                    $total_multiply = $total_hours;
                }
                if('day'==$pricing_by){
                    
                    // Get total days
                    $total_days = $interval->days;
                    
                    // If there are leftover hours that count as a partial day
                    if ($interval->h > 0 || $interval->i > 0) {
                        $total_days += 1;  // Add an extra day for any remaining hours
                    }
                    $total_multiply = $total_days;
                    
                }
            }else{
                $total_multiply = 1;
            }

            if(empty($all_prices['sale_price'])){
                $all_prices['sale_price'] = $initial_pricing * $total_multiply;
            }else{
                $all_prices['regular_price'] = $initial_pricing * $total_multiply;
            }
        }

        if('fixed'==$discount_type && !empty($discount_price)){
            $all_prices['sale_price'] = $all_prices['sale_price'] - $discount_price;
        }
        if('percent'==$discount_type && !empty($discount_price)){
            $discount_price = ($all_prices['sale_price'] * $discount_price)/100;
            $all_prices['sale_price'] = $all_prices['sale_price'] - $discount_price;
        }

        $all_prices['type'] = $price_type;

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

    // Return Tour Extras Price
    static function set_extra_price($meta, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time, $extra_ids=[], $extra_qty=[]){

        $car_extra = !empty($meta['extras']) ? $meta['extras'] : '';
        $prices = 0;
        $extra_title = [];
        if(!empty($extra_qty)){
            foreach($extra_qty as $key => $singleqty){
                if(!empty($singleqty)){
                    $extra_key = $extra_ids[$key];
                    $single_extra_info = !empty($car_extra[$extra_key]) ? $car_extra[$extra_key] : '';
                    if(!empty($single_extra_info)){ 
                        $price_type = $single_extra_info['price_type'];
                        if('day'==$price_type){
                            // Combine date and time
                            $pickup_datetime = new \DateTime("$tf_pickup_date $tf_pickup_time");
                            $dropoff_datetime = new \DateTime("$tf_dropoff_date $tf_dropoff_time");

                            // Calculate the difference
                            $interval = $pickup_datetime->diff($dropoff_datetime);

                            // Get total days
                            $total_days = $interval->days;
                            
                            // If there are leftover hours that count as a partial day
                            if ($interval->h > 0 || $interval->i > 0) {
                                $total_days += 1;  // Add an extra day for any remaining hours
                            }

                            $price = $single_extra_info['price'] * $total_days;

                        }else{
                            $price = $single_extra_info['price'];
                        }
                        $prices += $price * $singleqty;
                        $extra_title[] = $single_extra_info['title'].'('.$price_type.') × '. $singleqty. ' = ' . wc_price($price * $singleqty);
                    }
                }
            }
        }
        $extras = array(
         'title' => implode(", ",$extra_title),
         'price' => $prices
        );

        return $extras;
    }

    static function set_protection_price($meta, $tf_protection, $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time){
        $car_protections = ! empty( $meta['protections'] ) ? $meta['protections'] : '';
        $selected_protection_title = [];
        $prices = 0;
        foreach($tf_protection as $protection){
            $tf_single_protection = $car_protections[$protection];

            if( 'day' == $tf_single_protection['price_by'] ){
                // Combine date and time
                $pickup_datetime = new \DateTime("$tf_pickup_date $tf_pickup_time");
                $dropoff_datetime = new \DateTime("$tf_dropoff_date $tf_dropoff_time");

                // Calculate the difference
                $interval = $pickup_datetime->diff($dropoff_datetime);

                // Get total days
                $total_days = $interval->days;
                            
                // If there are leftover hours that count as a partial day
                if ($interval->h > 0 || $interval->i > 0) {
                    $total_days += 1;  // Add an extra day for any remaining hours
                }
            }else{
                $total_days = 1;
            }

            if( !empty($tf_single_protection['title']) && !empty($tf_single_protection['price']) ){
                $selected_protection_title[] = $tf_single_protection['title']. '('. $tf_single_protection['price_by'] .') × ' . wc_price($tf_single_protection['price'] * $total_days);
            }
            if( !empty($tf_single_protection['title']) && empty($tf_single_protection['price']) ){
                $selected_protection_title[] = $tf_single_protection['title']. '('. $tf_single_protection['price_by'] .')';
            }

            if(!empty($tf_single_protection['price'])){ 
                $prices += $tf_single_protection['price'] * $total_days;
            }
        }
        return $protections = array(
            'title' => implode(", ", $selected_protection_title),
            'price' => $prices
        );
    }

    static function get_total_trips($post_id){
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $total_completed_trip = $wpdb->get_results( 
            $wpdb->prepare( 
                "SELECT id FROM {$wpdb->prefix}tf_order_data WHERE post_id = %s AND ostatus = %s", 
                $post_id, 
                'completed' 
            ), 
            ARRAY_A 
        );

        // Get the number of rows
        $number_of_rows = count($total_completed_trip);
        return $number_of_rows;
    }

}