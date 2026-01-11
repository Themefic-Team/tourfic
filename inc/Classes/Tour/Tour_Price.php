<?php

namespace Tourfic\Classes\Tour;
use Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;

class Tour_Price {

    public $group;
    public $wc_group;
    public $sale_group;
    public $wc_sale_group;
    public $adult;
    public $wc_adult;
    public $sale_adult;
    public $wc_sale_adult;
    public $child;
    public $wc_child;
    public $sale_child;
    public $wc_sale_child;
    public $infant;
    public $wc_infant;
    public $sale_infant;
    public $wc_sale_infant;
    public $meta;
    
    function __construct($meta) {
    
        # Get tour type
        $tour_type = !empty($meta['type']) ? $meta['type'] : 'continuous';
    
        $allow_discount    = ! empty( $meta['allow_discount'] ) ? $meta['allow_discount'] : '';
        # Get discounts
        $discount_type    = !empty($meta['discount_type']) ? $meta['discount_type'] : 'none';
        $discounted_price = !empty($meta['discount_price']) ? $meta['discount_price'] : 0;

        $pricing_rule = !empty($meta['pricing']) ? $meta['pricing'] : 'person';

        $tour_availability_data = isset( $meta['tour_availability'] ) && ! empty( $meta['tour_availability'] ) ? json_decode( $meta['tour_availability'], true ) : [];
		
		$package_pricing = function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( $meta['package_pricing'] ) ? $meta['package_pricing'] : '';

        $adult_price = null;
        $child_price = null;
        $infant_price = null;
        $price = null;
        
		if(!empty($tour_availability_data) && Helper::is_all_unavailable($tour_availability_data)){
            $adult_price = null;
            $child_price = null;
            $infant_price = null;
            $price = null;
			foreach ($tour_availability_data as $data) {
				if ($data['status'] !== 'available') {
					continue;
				}

				if($pricing_rule == 'person' && $data['pricing_type'] == 'person'){
					// Adult Price
                    if (is_null($adult_price) || $data['adult_price'] < $adult_price) {
						$tour_price[] = $data['adult_price'];
					}

					// Child Price
                    if (is_null($child_price) || $data['child_price'] < $child_price) {
						$child_price = $data['child_price'];
					}
				}

				if($pricing_rule == 'group' && $data['pricing_type'] == 'group' ){
					// Group Price
                    if (is_null($price) || $data['price'] < $price) {
                        $price = $data['price'];
                    }
				}
				
				if( $pricing_rule == 'package' && $data['pricing_type'] == 'package'){
					if(!empty($data['options_count'])){
						for($i = 0; $i < $data['options_count']; $i++){

							if (!empty($data['tf_option_adult_price_'.$i])) {
                                if (is_null($adult_price) || $data['tf_option_adult_price_'.$i] < $adult_price) {
                                    $adult_price = $data['tf_option_adult_price_'.$i];
                                }
							}

							if (!empty($data['tf_option_child_price_'.$i])) {
                                if (is_null($child_price) || $data['tf_option_child_price_'.$i] < $child_price) {
                                    $child_price = $data['tf_option_child_price_'.$i];
                                }
							}

							if (!empty($data['tf_option_infant_price_'.$i])) {
								if (is_null($infant_price) || $data['tf_option_infant_price_'.$i] < $infant_price) {
                                    $infant_price = $data['tf_option_infant_price_'.$i];
                                }
							}

							if (!empty($data['tf_option_group_price_'.$i])) {
								if (is_null($price) || $data['tf_option_group_price_'.$i] < $price) {
                                    $price = $data['tf_option_group_price_'.$i];
                                }
							}

						}
					}
				}
			}
		}else{
			if($pricing_rule == 'person'){
				$adult_price  = !empty($meta['adult_price']) ? $meta['adult_price'] : 0;
                $child_price  = !empty($meta['child_price']) ? $meta['child_price'] : 0;
                $infant_price = !empty($meta['infant_price']) ? $meta['infant_price'] : 0;
			}
			if($pricing_rule == 'group'){
				$price = !empty($meta['group_price']) ? $meta['group_price'] : 0;
			}
            if($pricing_rule == 'package' && !empty($package_pricing)){
                $adult_price = null;
                $child_price = null;
                $infant_price = null;
                $price = null;
                foreach($package_pricing as $package){

                    if (!empty($package['adult_tabs'][1]['adult_price'])) {
                        if (is_null($adult_price) || $package['adult_tabs'][1]['adult_price'] < $adult_price) {
                            $adult_price = $package['adult_tabs'][1]['adult_price'];
                        }
                    }

                    if (!empty($package['child_tabs'][1]['child_price'])) {
                        if (is_null($child_price) || $package['child_tabs'][1]['child_price'] < $child_price) {
                            $child_price = $package['child_tabs'][1]['child_price'];
                        }
                    }

                    if (!empty($package['infant_tabs'][1]['infant_price'])) {
                        if (is_null($infant_price) || $package['infant_tabs'][1]['infant_price'] < $infant_price) {
                            $infant_price = $package['infant_tabs'][1]['infant_price'];
                        }
                    }

                    if (!empty($package['group_tabs'][1]['group_price'])) {
                        if (is_null($price) || $package['group_tabs'][1]['group_price'] < $price) {
                            $price = $package['group_tabs'][1]['group_price'];
                        }
                    }

                }
            }
		}

        /**
         * Price calculation based on pricing rule
         */
        if($pricing_rule == 'group') {
        
            if(!empty($allow_discount) && $discount_type == 'percent') {
                $sale_price = number_format( $price - (( $price / 100 ) * $discounted_price) , 2, '.', '' );
            } else if(!empty($allow_discount) && $discount_type == 'fixed') {
                $sale_price = number_format( ( $price - $discounted_price ), 2, '.', '' );
            }

            # WooCommerce Price
            $wc_price = wc_price($price);

            if(!empty($allow_discount) && ($discount_type == 'percent' || $discount_type == 'fixed')) {
                $wc_sale_price = wc_price($sale_price);
            }

        } else {
        
            if(!empty($allow_discount) && $discount_type == 'percent') {

                $adult_price  ? $sale_adult_price  = number_format( $adult_price - (( $adult_price / 100 ) * $discounted_price) , 2, '.', '' ) : 0;
                $child_price  ? $sale_child_price  = number_format( $child_price - (( $child_price / 100 ) * $discounted_price) , 2, '.', '' ) : 0;
                $infant_price ? $sale_infant_price = number_format( $infant_price - (( $infant_price / 100 ) * $discounted_price) , 2, '.', '' ) : 0;

            } else if(!empty($allow_discount) && $discount_type == 'fixed') {

                $adult_price  ? $sale_adult_price  = number_format( ( $adult_price - $discounted_price ), 2, '.', '' ) : 0;
                $child_price  ? $sale_child_price  = number_format( ( $child_price - $discounted_price ), 2, '.', '' ) : 0;
                $infant_price ? $sale_infant_price = number_format( ( $infant_price - $discounted_price ), 2, '.', '' ) : 0;

            }

            # WooCommerce Price
            $wc_adult_price  = wc_price($adult_price);
            $wc_child_price  = wc_price($child_price);
            $wc_infant_price = wc_price($infant_price);

            if(!empty($allow_discount) && ($discount_type == 'percent' || $discount_type == 'fixed')) {
                $wc_sale_adult_price  = !empty($sale_adult_price) ? wc_price($sale_adult_price) : 0;
                $wc_sale_child_price  = !empty($sale_child_price) ? wc_price($sale_child_price) : 0;
                $wc_sale_infant_price = !empty($sale_infant_price) ? wc_price($sale_infant_price) : 0;
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

	# Get date
	function date() {
		return $this->date;
	}

	# Get persons
	function persons() {
		return $this->persons;
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