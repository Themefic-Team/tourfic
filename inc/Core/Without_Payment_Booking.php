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

}