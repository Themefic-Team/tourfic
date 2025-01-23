<?php

namespace Tourfic\Classes\Car_Rental;
use \Tourfic\Classes\Helper;
class Availability {

    // Car Available or Not
    static function tf_car_inventory($post_id, $meta, $tf_pickup_date = '', $tf_dropoff_date = '', $tf_pickup_time = '', $tf_dropoff_time = '') {
        $pricing_by = !empty($meta["price_by"]) ? $meta["price_by"] : 'day';
        $car_numbers = !empty($meta["car_numbers"]) ? $meta["car_numbers"] : 0;

        // Combine date and time for precise checks
        $requested_start = strtotime("$tf_pickup_date $tf_pickup_time");
        $requested_end = strtotime("$tf_dropoff_date $tf_dropoff_time");

        if (!empty($car_numbers)) {
            $tf_orders_select = array(
                'select' => "post_id,order_details",
                'post_type' => 'car',
                'query' => " AND ostatus = 'completed' AND post_id = " . $post_id
            );
            $tf_car_book_orders = Helper::tourfic_order_table_data($tf_orders_select);

            if (!empty($tf_car_book_orders)) {
                $total_booking = 0;

                foreach ($tf_car_book_orders as $order) {
                    $order_details = json_decode($order['order_details'], true);
                    $order_start = strtotime($order_details['pickup_date'] . ' ' . $order_details['pickup_time']);
                    $order_end = strtotime($order_details['dropoff_date'] . ' ' . $order_details['dropoff_time']);

                    // Check for overlap
                    $overlap = !(
                        $requested_end <= $order_start ||  // Request ends before booking starts
                        $requested_start >= $order_end    // Request starts after booking ends
                    );

                    if ($overlap) {
                        $total_booking++;
                    }
                }

                // Check if available cars exceed bookings
                if ($car_numbers > $total_booking) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true; // No existing bookings
            }
        } else {
            return true; // No cars to book, but availability is technically unlimited
        }
    }

}