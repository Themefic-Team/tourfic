<?php

namespace Tourfic\Classes\Car_Rental;
use \Tourfic\Classes\Helper;
class Availability {

    // Car Available or Not
	static function tf_car_inventory( $post_id, $meta, $tf_pickup_date='', $tf_dropoff_date='', $tf_pickup_time='', $tf_dropoff_time='' ) {
		$pricing_by = !empty($meta["price_by"]) ? $meta["price_by"] : 'day';
		$car_numbers = !empty($meta["car_numbers"]) ? $meta["car_numbers"] : 0;
        if(!empty($car_numbers)){
            $tf_orders_select = array(
                'select' => "post_id,order_details",
                'post_type' => 'car',
                'query' => " AND ostatus = 'completed' AND post_id = ".$post_id
            );
            $tf_car_book_orders = Helper::tourfic_order_table_data($tf_orders_select);
            if(!empty($tf_car_book_orders)){
                $total_booking = 0;
                foreach($tf_car_book_orders as $order){
                    $order_details = json_decode($order['order_details'], true);
                    $order_pickup_date  = strtotime( $order_details['pickup_date'] );
                    $order_dropoff_date = strtotime( $order_details['dropoff_date'] );

                    if(!empty($tf_pickup_time) && !empty($tf_dropoff_time) && !empty($order_details['pickup_time']) && !empty($order_details['dropoff_time'])  ){
                        if( $order_pickup_date==strtotime($tf_pickup_date) && $order_dropoff_date==strtotime($tf_dropoff_date) && $tf_pickup_time==$order_details['pickup_time'] && $tf_dropoff_time==$order_details['dropoff_time'] ){
                            $total_booking+=1;
                        }
                    }else{
                        if( $order_pickup_date==strtotime($tf_pickup_date) && $order_dropoff_date==strtotime($tf_dropoff_date) ){
                            $total_booking+=1;
                        }
                    }
                }

                if($car_numbers > $total_booking){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
        }else{
            return true;
        }
    }

}