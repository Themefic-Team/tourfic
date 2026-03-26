<?php

namespace Tourfic\Classes\Car_Rental;
defined( 'ABSPATH' ) || exit;

class Pricing {

	use \Tourfic\Traits\Singleton;

    public function __construct() {

	}

	private static function get_duration_data( $pickup_date, $dropoff_date, $pickup_time, $dropoff_time ) {
		$duration_data = array(
			'hours'    => 1,
			'days'     => 1,
			'raw_days' => 0,
			'valid'    => false,
		);

		$pickup_datetime = self::create_datetime( $pickup_date, $pickup_time );
		$dropoff_datetime = self::create_datetime( $dropoff_date, $dropoff_time );

		if ( ! $pickup_datetime || ! $dropoff_datetime ) {
			return $duration_data;
		}

		$interval = $pickup_datetime->diff( $dropoff_datetime );

		$total_hours = ( $interval->days * 24 ) + $interval->h + ( $interval->i / 60 );
		if ( $total_hours < 1 ) {
			$total_hours = 1;
		}

		$total_days = $interval->days;
		if ( $interval->h > 0 || $interval->i > 0 ) {
			$total_days += 1;
		}
		if ( $total_days < 1 ) {
			$total_days = 1;
		}

		$duration_data['hours'] = $total_hours;
		$duration_data['days'] = $total_days;
		$duration_data['raw_days'] = (int) $interval->days;
		$duration_data['valid'] = true;

		return $duration_data;
	}

	private static function create_datetime( $date, $time ) {
		if ( function_exists( 'tf_car_create_datetime' ) ) {
			$datetime = \tf_car_create_datetime( $date, $time );
			if ( $datetime instanceof \DateTime ) {
				return $datetime;
			}
		}

		$date = ! empty( $date ) ? sanitize_text_field( (string) $date ) : '';
		$time = ! empty( $time ) ? sanitize_text_field( (string) $time ) : '00:00';

		if ( function_exists( 'tf_normalize_date' ) ) {
			$date = tf_normalize_date( $date );
		}

		if ( empty( $date ) ) {
			return false;
		}

		$datetime_string = trim( $date . ' ' . $time );
		$formats = array(
			'Y/m/d H:i',
			'Y/m/d h:i A',
			'Y/m/d g:i A',
			'Y/m/d h:i a',
			'Y/m/d g:i a',
		);

		foreach ( $formats as $format ) {
			$datetime = \DateTime::createFromFormat( $format, $datetime_string );
			if ( false !== $datetime ) {
				return $datetime;
			}
		}

		$timestamp = strtotime( $datetime_string );
		if ( false === $timestamp ) {
			return false;
		}

		return new \DateTime( '@' . $timestamp );
	}

	// all price will be calculate here
	static function set_total_price( $meta, $tf_pickup_date='', $tf_dropoff_date='', $tf_pickup_time='', $tf_dropoff_time='', $tf_archive='' ) {
		if ( function_exists( 'tf_normalize_car_meta' ) ) {
			$meta = tf_normalize_car_meta( $meta );
		}

		$pricing_by = !empty($meta["price_by"]) ? $meta["price_by"] : 'day';
		$initial_pricing = !empty($meta["car_rent"]) ? $meta["car_rent"] : 0;

        $price_type = $pricing_by;

		$pricing_type = !empty($meta["pricing_type"]) ? $meta["pricing_type"] : 'day_hour';
		$discount_type = !empty($meta["discount_type"]) ? $meta["discount_type"] : 'none';
		$discount_price = !empty($meta["discount_price"]) ? $meta["discount_price"] : '';

        $date_pricing = function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($meta["date_prices"]) ? $meta["date_prices"] : '';
        $day_pricing = function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($meta["day_prices"]) ? $meta["day_prices"] : '';

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

        } elseif( !empty($tf_pickup_date) && !empty($tf_dropoff_date) && 'day_hour'==$pricing_type && !empty($day_pricing) ){

            $duration_data = self::get_duration_data( $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time );
            $total_hours = $duration_data['hours'];
            $total_days = $duration_data['days'];
            $raw_total_days = $duration_data['raw_days'];

            $all_prices = [];
            $result = array();
            foreach ($day_pricing as $entry) {
                $day_type = $entry['type'];
                $startDay = $entry['from_day'];
                $endDay = $entry['to_day'];
                $price = $entry['price'];

                $price_type = $day_type;

                if('day'==$day_type && $raw_total_days > 0){
                    if(!empty($tf_archive)){
                        $total_multiply = 1;
                    }else{
                        $total_multiply = $total_days;
                    }
                }else{
                    if(!empty($tf_archive)){
                        $total_multiply = 1;
                    }else{
                        $total_multiply = $total_hours;
                    }
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
                    if(!empty($tf_archive)){
                        $total_multiply = 1;
                    }else{
                        $total_multiply = $total_hours;
                    }
                }
                if('day'==$pricing_by){
                    if(!empty($tf_archive)){
                        $total_multiply = 1;
                    }else{
                        $total_multiply = $total_days;
                    }
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
                $duration_data = self::get_duration_data( $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time );
                $total_hours = $duration_data['hours'];
                $total_days = $duration_data['days'];

                if('hour'==$pricing_by){
                    if(!empty($tf_archive)){
                        $total_multiply = 1;
                    }else{
                        $total_multiply = $total_hours;
                    }
                }
                if('day'==$pricing_by){
                    if(!empty($tf_archive)){
                        $total_multiply = 1;
                    }else{
                        $total_multiply = $total_days;
                    }
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

        $base_sale_price = !empty($all_prices['sale_price']) ? (float) $all_prices['sale_price'] : 0;
        if('fixed'==$discount_type && !empty($discount_price)){
            $discount_amount = (float) $discount_price;
            if($discount_amount > 0 && $base_sale_price > 0){
                $all_prices['regular_price'] = $base_sale_price;
                $all_prices['sale_price'] = max(0, $base_sale_price - $discount_amount);
            }
        }
        if('percent'==$discount_type && !empty($discount_price)){
            $discount_percent = (float) $discount_price;
            if($discount_percent > 0 && $base_sale_price > 0){
                $all_prices['regular_price'] = $base_sale_price;
                $discount_amount = ($base_sale_price * $discount_percent)/100;
                $all_prices['sale_price'] = max(0, $base_sale_price - $discount_amount);
            }
        }

        $all_prices['type'] = esc_html__($price_type, 'tourfic');

        return $all_prices;
    }

    // taxable car or not
	static function is_taxable( $meta ) {
        $is_taxable = !empty($meta["is_taxable"]) ? $meta["is_taxable"] : '';
        $taxable_class = !empty($meta["taxable_class"]) ? $meta["taxable_class"] : '';
        if($is_taxable && !empty($taxable_class)){
            return  esc_html__("With Taxes", "tourfic");
        }else{
            return  esc_html__("Without Taxes", "tourfic");
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
                            $duration_data = self::get_duration_data( $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time );
                            $total_days = $duration_data['days'];

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
                $duration_data = self::get_duration_data( $tf_pickup_date, $tf_dropoff_date, $tf_pickup_time, $tf_dropoff_time );
                $total_days = $duration_data['days'];
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
