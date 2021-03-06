<?php

/**
 * Price calculation for single tour
 */
if(!class_exists('TourPrice')){
    class Tour_Price {

        public $meta;

        function __construct($meta) {
        
            # Get tour type
            $tour_type = !empty($meta['type']) ? $meta['type'] : 'continuous';
        
            # Custom availability status
            if($tour_type == 'continuous') {
                $custom_avail = !empty($meta['custom_avail']) ? $meta['custom_avail'] : false;
            }
        
            # Get discounts
            $discount_type    = !empty($meta['discount_type']) ? $meta['discount_type'] : 'none';
            $discounted_price = !empty($meta['discount_price']) ? $meta['discount_price'] : '';
        
            /**
             * Price calculation based on custom availability
             * 
             * Custom availability has different pricing calculation
             */
            if($tour_type == 'continuous' && $custom_avail == true) {
        
                # Get pricing rule person/group
                $pricing_rule = !empty($meta['custom_pricing_by']) ? $meta['custom_pricing_by'] : 'person';
        
                /**
                 * Price calculation based on pricing rule
                 */
                if($pricing_rule == 'group') {
        
                    # Get group price from all the arrays
                    $group_prices_array = array_column($meta['cont_custom_date'], 'group_price');
                    # Get minimum group price
                    $min_group_price = min($group_prices_array);
                    # Get maximum group price
                    $max_group_price = max($group_prices_array);
                
                    # Discount price calculation
                    if($discount_type == 'percent') {
        
                        $sale_min_group_price = number_format( $min_group_price - (( $min_group_price / 100 ) * $discounted_price) , 1, '.', '' );
                        $sale_max_group_price = number_format( $max_group_price - (( $max_group_price / 100 ) * $discounted_price) , 1, '.', '' );
        
                    } else if($discount_type == 'fixed') {
        
                        $sale_min_group_price = number_format( ( $min_group_price - $discounted_price ), 1, '.', '' );
                        $sale_max_group_price = number_format( ( $max_group_price - $discounted_price ), 1, '.', '' );
        
                    }
        
                    if($discount_type == 'percent' || $discount_type == 'fixed') {

                        # WooCommerce Price
                        $wc_min_group_price = wc_price($sale_min_group_price, array('decimals'=>0));
                        $wc_max_group_price = wc_price($sale_max_group_price, array('decimals'=>0));

                        # Final output (price range)
                        if(!empty($sale_min_group_price) && !empty($sale_max_group_price)) {
                            $price = $sale_min_group_price. '-' .$sale_max_group_price; // Discounted price range
                        }
                        if(!empty($wc_min_group_price) && !empty($wc_max_group_price)) {
                            $wc_price = $wc_min_group_price. '-' .$wc_max_group_price; // Discounted WooCommerce price range
                        }

                    } else {

                        # WooCommerce Price
                        $wc_min_group_price = wc_price($min_group_price, array('decimals'=>0));
                        $wc_max_group_price = wc_price($max_group_price, array('decimals'=>0));

                        # Final output (price range)
                        if(!empty($min_group_price) && !empty($max_group_price)) {
                            $price = $min_group_price. '-' .$max_group_price; // Price range
                        }
                        if(!empty($wc_min_group_price) && !empty($wc_max_group_price)) {
                            $wc_price = $wc_min_group_price. '-' .$wc_max_group_price; // WooCommerce price range
                        }
                        
                    }
        
                } else if($pricing_rule == 'person') {

                    # Get adult, child, infant price from all the arrays
                    $adult_price_array  = array_column($meta['cont_custom_date'], 'adult_price');
                    $child_price_array  = array_column($meta['cont_custom_date'], 'child_price');
                    $infant_price_array = array_column($meta['cont_custom_date'], 'infant_price');

                    # Get minimum price of adult, child, infant
                    $min_adult_price  = min($adult_price_array);
                    $min_child_price  = min($child_price_array);
                    $min_infant_price = min($infant_price_array);

                    # Get maximum price of adult, child, infant
                    $max_adult_price  = max($adult_price_array);
                    $max_child_price  = max($child_price_array);
                    $max_infant_price = max($infant_price_array);

                    # Discount price calculation
                    if($discount_type == 'percent') {
        
                        # Minimum discounted price
                        $sale_min_adult_price  = number_format( $min_adult_price - (( $min_adult_price / 100 ) * $discounted_price) , 1, '.', '' );
                        $sale_min_child_price  = number_format( $min_child_price - (( $min_child_price / 100 ) * $discounted_price) , 1, '.', '' );
                        $sale_min_infant_price = number_format( $min_infant_price - (( $min_infant_price / 100 ) * $discounted_price) , 1, '.', '' );
                        # Maximum discounted price
                        $sale_max_adult_price  = number_format( $max_adult_price - (( $max_adult_price / 100 ) * $discounted_price) , 1, '.', '' );
                        $sale_max_child_price  = number_format( $max_child_price - (( $max_child_price / 100 ) * $discounted_price) , 1, '.', '' );
                        $sale_max_infant_price = number_format( $max_infant_price - (( $max_infant_price / 100 ) * $discounted_price) , 1, '.', '' );
        
                    } else if($discount_type == 'fixed') {
        
                        # Minimum discounted price
                        $sale_min_adult_price  = number_format( ( $min_adult_price - $discounted_price ), 1, '.', '' );
                        $sale_min_child_price  = number_format( ( $min_child_price - $discounted_price ), 1, '.', '' );
                        $sale_min_infant_price = number_format( ( $min_infant_price - $discounted_price ), 1, '.', '' );
                        # Maximum discounted price
                        $sale_max_adult_price  = number_format( ( $max_adult_price - $discounted_price ), 1, '.', '' );
                        $sale_max_child_price  = number_format( ( $max_child_price - $discounted_price ), 1, '.', '' );
                        $sale_max_infant_price = number_format( ( $max_infant_price - $discounted_price ), 1, '.', '' );
        
                    }

                    if($discount_type == 'percent' || $discount_type == 'fixed') {
        
                        # WooCommerce Price
                        $wc_min_adult_price  = wc_price($sale_min_adult_price, array('decimals'=>0));
                        $wc_min_child_price  = wc_price($sale_min_child_price, array('decimals'=>0));
                        $wc_min_infant_price = wc_price($sale_min_infant_price, array('decimals'=>0));

                        $wc_max_adult_price  = wc_price($sale_max_adult_price, array('decimals'=>0));
                        $wc_max_child_price  = wc_price($sale_max_child_price, array('decimals'=>0));
                        $wc_max_infant_price = wc_price($sale_max_infant_price, array('decimals'=>0));
            
                        # Final output (price range)
                        if(!empty($sale_min_adult_price) && !empty($sale_max_adult_price)) {
                            $sale_adult_price  = $sale_min_adult_price. '-' .$sale_max_adult_price;    // Discounted price range
                        }
                        if(!empty($sale_min_child_price) && !empty($sale_max_child_price)) {
                            $sale_child_price  = $sale_min_child_price. '-' .$sale_max_child_price;    // Discounted price range
                        }
                        if(!empty($sale_min_infant_price) && !empty($sale_max_infant_price)) {
                            $sale_infant_price = $sale_min_infant_price. '-' .$sale_max_infant_price;  // Discounted price range
                        }
                        
                        if(!empty($wc_min_adult_price) && !empty($wc_max_adult_price)) {
                            $wc_sale_adult_price  = $wc_min_adult_price. '-' .$wc_max_adult_price;    // Discounted WooCommerce price range
                        }
                        if(!empty($wc_min_child_price) && !empty($wc_max_child_price)) {
                            $wc_sale_child_price  = $wc_min_child_price. '-' .$wc_max_child_price;    // Discounted WooCommerce price range
                        }
                        if(!empty($wc_min_infant_price) && !empty($wc_max_infant_price)) {
                            $wc_sale_infant_price = $wc_min_infant_price. '-' .$wc_max_infant_price;  // Discounted WooCommerce price range
                        }

                    }

                    # WooCommerce Price
                    $wc_min_adult_price  = wc_price($min_adult_price, array('decimals'=>0));
                    $wc_min_child_price  = wc_price($min_child_price, array('decimals'=>0));
                    $wc_min_infant_price = wc_price($min_infant_price, array('decimals'=>0));

                    $wc_max_adult_price  = wc_price($max_adult_price, array('decimals'=>0));
                    $wc_max_child_price  = wc_price($max_child_price, array('decimals'=>0));
                    $wc_max_infant_price = wc_price($max_infant_price, array('decimals'=>0));

                    # Final output (price range)
                    if(!empty($min_adult_price) && !empty($max_adult_price)) {
                        $adult_price = $min_adult_price. '-' .$max_adult_price;    // Price range
                    }
                    if(!empty($min_child_price) && !empty($max_child_price)) {
                        $child_price  = $min_child_price. '-' .$max_child_price;    // Price range
                    }
                    if(!empty($min_infant_price) && !empty($max_infant_price)) {
                        $infant_price = $min_infant_price. '-' .$max_infant_price;  // Price range
                    }
                    
                    if(!empty($wc_min_adult_price) && !empty($wc_max_adult_price)) {
                        $wc_adult_price  = $wc_min_adult_price. '-' .$wc_max_adult_price;    // WooCommerce price range
                    }
                    if(!empty($wc_min_child_price) && !empty($wc_max_child_price)) {
                        $wc_child_price  = $wc_min_child_price. '-' .$wc_max_child_price;    // WooCommerce price range
                    }
                    if(!empty($wc_min_infant_price) && !empty($wc_max_infant_price)) {
                        $wc_infant_price = $wc_min_infant_price. '-' .$wc_max_infant_price;  // WooCommerce price range
                    }                   
        
                }
        
            } else {
        
                /**
                 * Pricing for fixed/continuous
                 */
        
                # Get pricing rule person/group
                $pricing_rule = !empty($meta['pricing']) ? $meta['pricing'] : 'person';
        
                /**
                 * Price calculation based on pricing rule
                 */
                if($pricing_rule == 'group') {
        
                    # Get group price. Default 0
                    $price = !empty($meta['group_price']) ? $meta['group_price'] : '';
                
                    if($discount_type == 'percent') {
                        $sale_price = number_format( $price - (( $price / 100 ) * $discounted_price) , 1, '.', '' );
                    } else if($discount_type == 'fixed') {
                        $sale_price = number_format( ( $price - $discounted_price ), 1, '.', '' );
                    }

                    # WooCommerce Price
                    $wc_price = wc_price($price, array('decimals'=>0));

                    if($discount_type == 'percent' || $discount_type == 'fixed') {
                        $wc_sale_price = wc_price($sale_price, array('decimals'=>0));
                    }
        
                } else if($pricing_rule == 'person') {
        
                    $adult_price  = !empty($meta['adult_price']) ? $meta['adult_price'] : '';
                    $child_price  = !empty($meta['child_price']) ? $meta['child_price'] : '';
                    $infant_price = !empty($meta['infant_price']) ? $meta['infant_price'] : '';
                
                    if($discount_type == 'percent') {
        
                        $adult_price  ? $sale_adult_price  = number_format( $adult_price - (( $adult_price / 100 ) * $discounted_price) , 1, '.', '' ) : '';
                        $child_price  ? $sale_child_price  = number_format( $child_price - (( $child_price / 100 ) * $discounted_price) , 1, '.', '' ) : '';
                        $infant_price ? $sale_infant_price = number_format( $infant_price - (( $infant_price / 100 ) * $discounted_price) , 1, '.', '' ) : '';
        
                    } else if($discount_type == 'fixed') {
        
                        $adult_price  ? $sale_adult_price  = number_format( ( $adult_price - $discounted_price ), 1, '.', '' ) : '';
                        $child_price  ? $sale_child_price  = number_format( ( $child_price - $discounted_price ), 1, '.', '' ) : '';
                        $infant_price ? $sale_infant_price = number_format( ( $infant_price - $discounted_price ), 1, '.', '' ) : '';
        
                    }

                    # WooCommerce Price
                    $wc_adult_price  = wc_price($adult_price, array('decimals'=>0));
                    $wc_child_price  = wc_price($child_price, array('decimals'=>0));
                    $wc_infant_price = wc_price($infant_price, array('decimals'=>0));

                    if($discount_type == 'percent' || $discount_type == 'fixed') {
                        $wc_sale_adult_price  = wc_price($sale_adult_price, array('decimals'=>0));
                        $wc_sale_child_price  = wc_price($sale_child_price, array('decimals'=>0));
                        $wc_sale_infant_price = wc_price($sale_infant_price, array('decimals'=>0));
                    }
        
                }

            }

            $this->group          = $price ?? null;
            $this->wc_group       = $wc_price ?? null;
            $this->sale_group     = $sale_price ?? null;
            $this->wc_sale_group  = $wc_sale_price ?? null;
            $this->adult          = $adult_price ?? null;
            $this->wc_adult       = $wc_adult_price ?? null;
            $this->sale_adult     = $sale_adult_price ?? null;
            $this->wc_sale_adult  = $wc_sale_adult_price ?? null;
            $this->child          = $child_price ?? null;
            $this->wc_child       = $wc_child_price ?? null;
            $this->sale_child     = $sale_child_price ?? null;
            $this->wc_sale_child  = $wc_sale_child_price ?? null;
            $this->infant         = $infant_price ?? null;
            $this->wc_infant      = $wc_infant_price ?? null;
            $this->sale_infant    = $sale_infant_price ?? null;
            $this->wc_sale_infant = $wc_sale_infant_price ?? null;
        
        }

        # Group regular price
        function group() {
            return $this->group;
        }

        # Group WC regular price
        function wc_group() {
            return $this->wc_group;
        }

        # Group sale price
        function sale_group() {
            return $this->sale_group;
        }

        # Group WC sale price
        function wc_sale_group() {
            return $this->wc_sale_group;
        }

        # Adult regular price
        function adult() {
            return $this->adult;
        }

        # Adult WC regular price
        function wc_adult() {
            return $this->wc_adult;
        }

        # Adult sale price
        function sale_adult() {
            return $this->sale_adult;
        }

        # Adult WC sale price
        function wc_sale_adult() {
            return $this->wc_sale_adult;
        }

        # Child regular price
        function child() {
            return $this->child;
        }

        # Child WC regular price
        function wc_child() {
            return $this->wc_child;
        }

        # Child sale price
        function sale_child() {
            return $this->sale_child;
        }

        # Child WC sale price
        function wc_sale_child() {
            return $this->wc_sale_child;
        }

        # Infant regular price
        function infant() {
            return $this->infant;
        }

        # Infant WC regular price
        function wc_infant() {
            return $this->wc_infant;
        }

        # Infant sale price
        function sale_infant() {
            return $this->sale_infant;
        }

        # Infant WC sale price
        function wc_sale_infant() {
            return $this->wc_sale_infant;
        }

    }
}
?>