<?php

namespace Tourfic\Core;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Without_Payment_Booking {

    /**
     * Steps: 
     * 1. Popup - Price and Calculations
     * 2. Popup - Extras (Tour Extras Hotel Airport, Apartment Additional Fees)
     * 2. Popup - Traveler Information
     * 3. Popup - Booking Confirmation Fields
     * 4. Everything Send to Ajax
     * 5. Booking Calculation - JS
     * 6. Booking Confirmation
     */

     /**
      * Arguments = post_type (tf_hotel, tf_apartment), 
      */

    protected array $args = array();

    public function __construct( array $args ) {
        $this->args = $args;

        // Add actions
        add_action("wp_ajax_" . $this->args["post_type"]. '_booking_popup', array( $this, 'without_payment_booking_popup_callback' ) );
        add_action("wp_ajax_nopriv_" . $this->args["post_type"]. '_booking_popup', array( $this, 'without_payment_booking_popup_callback' ) );
        
    }

    abstract public function without_payment_booking_popup_callback();

}