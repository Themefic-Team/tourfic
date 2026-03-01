<?php
// Don't load directly
defined( 'ABSPATH' ) || exit;
?>
<div class="tf-car-info" id="tf-car-info">
    <?php if(!empty($car_info_title)){ ?>
    <h3><?php echo esc_html($car_info_title); ?></h3>
    <?php } ?>
    <ul>
        <?php if(!empty($passengers)){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_1050_4532)">
                <path d="M6.66675 5.83333C6.66675 6.71739 7.01794 7.56523 7.64306 8.19036C8.26818 8.81548 9.11603 9.16667 10.0001 9.16667C10.8841 9.16667 11.732 8.81548 12.3571 8.19036C12.9822 7.56523 13.3334 6.71739 13.3334 5.83333C13.3334 4.94928 12.9822 4.10143 12.3571 3.47631C11.732 2.85119 10.8841 2.5 10.0001 2.5C9.11603 2.5 8.26818 2.85119 7.64306 3.47631C7.01794 4.10143 6.66675 4.94928 6.66675 5.83333Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M5 17.5V15.8333C5 14.9493 5.35119 14.1014 5.97631 13.4763C6.60143 12.8512 7.44928 12.5 8.33333 12.5H11.6667C12.5507 12.5 13.3986 12.8512 14.0237 13.4763C14.6488 14.1014 15 14.9493 15 15.8333V17.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
            <defs>
                <clipPath id="clip0_1050_4532">
                <rect width="20" height="20" fill="white"/>
                </clipPath>
            </defs>
            </svg>
            <?php echo esc_attr($passengers); ?> <?php esc_html_e("Persons", "tourfic"); ?>
            <div class="tf-car-info-tooltip">
                <span><?php esc_html_e("This car has", "tourfic"); ?> <?php echo esc_attr($passengers); ?> <?php esc_html_e("Seats available for passengers.", "tourfic"); ?></span>
            </div>
        </li>
        <?php } ?>
        <?php if(!empty($baggage)){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.0003 5.19983C7.893 5.19983 5.69021 5.19983 3.84363 5.52986C2.9585 5.68804 2.26121 6.36397 2.07119 7.24281C1.77173 8.62763 1.77173 9.91936 1.77173 11.8855C1.77173 13.8517 1.77173 15.1434 2.07119 16.5283C2.26121 17.4071 2.9585 18.083 3.84363 18.2413C5.69021 18.5713 7.893 18.5713 10.0003 18.5713C12.1076 18.5713 14.3104 18.5713 16.157 18.2413C17.0421 18.083 17.7394 17.4071 17.9294 16.5283C18.2288 15.1434 18.2288 13.8517 18.2288 11.8855C18.2288 9.91936 18.2288 8.62763 17.9294 7.24281C17.7394 6.36397 17.0421 5.68804 16.157 5.52986C14.3104 5.19983 12.1076 5.19983 10.0003 5.19983Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6.5708 5.20002V4.28573C6.5708 2.70777 7.84999 1.42859 9.42794 1.42859H10.5708C12.1488 1.42859 13.4279 2.70777 13.4279 4.28573V5.20002" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M1.78149 10L9.10039 12.4432C9.68761 12.6393 10.3226 12.6393 10.9098 12.4432L18.2287 10" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?php echo esc_attr($baggage); ?> <?php esc_html_e("Bag", "tourfic"); ?>
            <div class="tf-car-info-tooltip">
                <span><?php esc_html_e("The car can accommodate up to ", "tourfic"); ?><?php echo esc_attr($baggage); ?> <?php esc_html_e("bags in the luggage compartment.", "tourfic"); ?></span>
            </div>
        </li>
        <?php } ?>
        <?php if(!empty($fuel_types)){ ?>
            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_1055_4099)">
                    <path d="M11.6667 9.16667H12.5001C12.9421 9.16667 13.366 9.34226 13.6786 9.65482C13.9912 9.96738 14.1667 10.3913 14.1667 10.8333V13.3333C14.1667 13.6649 14.2984 13.9828 14.5329 14.2172C14.7673 14.4516 15.0852 14.5833 15.4167 14.5833C15.7483 14.5833 16.0662 14.4516 16.3006 14.2172C16.5351 13.9828 16.6667 13.6649 16.6667 13.3333V7.5L14.1667 5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3.33325 16.6667V5.00004C3.33325 4.55801 3.50885 4.13409 3.82141 3.82153C4.13397 3.50897 4.55789 3.33337 4.99992 3.33337H9.99992C10.4419 3.33337 10.8659 3.50897 11.1784 3.82153C11.491 4.13409 11.6666 4.55801 11.6666 5.00004V16.6667" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2.5 16.6666H12.5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15 5.83337V6.66671C15 6.88772 15.0878 7.09968 15.2441 7.25596C15.4004 7.41224 15.6123 7.50004 15.8333 7.50004H16.6667" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3.33325 9.16663H11.6666" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                    <clipPath id="clip0_1055_4099">
                    <rect width="20" height="20" fill="white"/>
                    </clipPath>
                </defs>
                </svg>
                <?php echo esc_html($fuel_types); ?>
                <div class="tf-car-info-tooltip">
                    <span><?php esc_html_e("The vehicle runs on ", "tourfic"); ?> <?php echo esc_attr($fuel_types); ?> <?php esc_html_e("fuel.", "tourfic"); ?></span>
                </div>
            </li>
        <?php } ?>

        <?php if(!empty($engine_years)){ ?>
            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 16.5C12 16.697 12.0388 16.8921 12.1142 17.074C12.1895 17.256 12.3001 17.4214 12.4393 17.5607C12.5786 17.6999 12.744 17.8105 12.926 17.8858C13.1079 17.9612 13.303 18 13.5 18C13.697 18 13.8921 17.9612 14.074 17.8858C14.256 17.8105 14.4214 17.6999 14.5607 17.5607C14.6999 17.4214 14.8105 17.256 14.8858 17.074C14.9612 16.8921 15 16.697 15 16.5C15 16.303 14.9612 16.1079 14.8858 15.926C14.8105 15.744 14.6999 15.5786 14.5607 15.4393C14.4214 15.3001 14.256 15.1895 14.074 15.1142C13.8921 15.0388 13.697 15 13.5 15C13.303 15 13.1079 15.0388 12.926 15.1142C12.744 15.1895 12.5786 15.3001 12.4393 15.4393C12.3001 15.5786 12.1895 15.744 12.1142 15.926C12.0388 16.1079 12 16.303 12 16.5Z" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4 16.5C4 16.8978 4.15803 17.2793 4.43934 17.5607C4.72065 17.8419 5.10217 18 5.5 18C5.89783 18 6.27935 17.8419 6.56066 17.5607C6.84197 17.2793 7 16.8978 7 16.5C7 16.1022 6.84197 15.7207 6.56066 15.4393C6.27935 15.1581 5.89783 15 5.5 15C5.10217 15 4.72065 15.1581 4.43934 15.4393C4.15803 15.7207 4 16.1022 4 16.5Z" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 17H7" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M3.5048 17H2.2C1.88174 17 1.57651 16.8796 1.35147 16.6653C1.12643 16.451 1 16.1602 1 15.8571V14.7143C1 14.1081 1.25286 13.5267 1.70294 13.0981C2.15303 12.6694 2.76348 12.4286 3.4 12.4286L4.868 9.63162C4.9677 9.44175 5.12096 9.28213 5.31058 9.17059C5.50021 9.05897 5.71871 8.99992 5.9416 9H9.8616C10.0845 8.99992 10.303 9.05897 10.4926 9.17059C10.6822 9.28213 10.8355 9.44175 10.9352 9.63162L12.4 12.4286H16.6C17.2365 12.4286 17.847 12.6694 18.297 13.0981C18.7471 13.5267 19 14.1081 19 14.7143V15.8571C19 16.1602 18.8736 16.451 18.6486 16.6653C18.4235 16.8796 18.1182 17 17.8 17H15.296" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 12H4" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 12L15.6 9H18" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M18.5 6C18.3357 5.82421 18.2063 5.61201 18.1204 5.3771C18.0344 5.1422 17.9936 4.88982 18.0008 4.6363C18.0008 3.72741 18.9992 3.27259 18.9992 2.3637C19.0063 2.11018 18.9657 1.8578 18.8797 1.6229C18.7937 1.38799 18.6643 1.17578 18.5 1" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14.5 7C14.3357 6.82421 14.2063 6.61201 14.1204 6.3771C14.0344 6.1422 13.9936 5.88982 14.0008 5.6363C14.0008 4.72741 14.9992 4.27259 14.9992 3.3637C15.0063 3.11018 14.9657 2.8578 14.8797 2.6229C14.7937 2.38799 14.6643 2.17579 14.5 2" stroke="#566676" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <?php echo esc_html($engine_years); ?>
                <div class="tf-car-info-tooltip">
                <span><?php esc_html_e("This is the vehicle's model year.", "tourfic"); ?></span>
            </div>
            </li>
        <?php } ?>
        
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_1049_4385)">
                <path d="M9.375 12.5L16.875 5" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4.40937 12.5C4.38648 12.2925 4.37501 12.0838 4.375 11.875C4.37576 10.9823 4.58879 10.1026 4.99652 9.30849C5.40425 8.51435 5.99499 7.82856 6.71999 7.3077C7.44498 6.78685 8.28345 6.44588 9.16618 6.31292C10.0489 6.17996 10.9506 6.25882 11.7969 6.54301" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M17.236 8.17496C17.7371 9.15824 18.0335 10.233 18.1071 11.3342C18.1807 12.4353 18.03 13.54 17.6641 14.5812C17.6214 14.7039 17.5415 14.8103 17.4355 14.8855C17.3295 14.9607 17.2027 15.001 17.0727 15.0007H2.92661C2.79641 15.0002 2.66959 14.9592 2.56366 14.8835C2.45773 14.8078 2.37791 14.7011 2.33521 14.5781C2.02273 13.6896 1.86703 12.7535 1.87505 11.8117C1.90943 7.34371 5.60396 3.71011 10.0782 3.74996C11.3392 3.76008 12.5805 4.06452 13.7032 4.63902" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
            <defs>
                <clipPath id="clip0_1049_4385">
                <rect width="20" height="20" fill="white"/>
                </clipPath>
            </defs>
            </svg>
            <?php echo $unlimited_mileage ? esc_html__("Unlimited", "tourfic") : esc_html($total_mileage).' '.esc_html($mileage_type); ?>

            <div class="tf-car-info-tooltip">
                <span><?php echo $unlimited_mileage ? esc_html__("Unlimited mileage", "tourfic") : esc_html($total_mileage).' '.esc_html($mileage_type); ?> <?php esc_html_e("is included in this rental.", "tourfic"); ?></span>
            </div>
        </li>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_1049_4515)">
                <path d="M2.5 5.00004C2.5 5.44207 2.67559 5.86599 2.98816 6.17855C3.30072 6.49111 3.72464 6.66671 4.16667 6.66671C4.60869 6.66671 5.03262 6.49111 5.34518 6.17855C5.65774 5.86599 5.83333 5.44207 5.83333 5.00004C5.83333 4.55801 5.65774 4.13409 5.34518 3.82153C5.03262 3.50897 4.60869 3.33337 4.16667 3.33337C3.72464 3.33337 3.30072 3.50897 2.98816 3.82153C2.67559 4.13409 2.5 4.55801 2.5 5.00004Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8.33325 5.00004C8.33325 5.44207 8.50885 5.86599 8.82141 6.17855C9.13397 6.49111 9.55789 6.66671 9.99992 6.66671C10.4419 6.66671 10.8659 6.49111 11.1784 6.17855C11.491 5.86599 11.6666 5.44207 11.6666 5.00004C11.6666 4.55801 11.491 4.13409 11.1784 3.82153C10.8659 3.50897 10.4419 3.33337 9.99992 3.33337C9.55789 3.33337 9.13397 3.50897 8.82141 3.82153C8.50885 4.13409 8.33325 4.55801 8.33325 5.00004Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14.1667 5.00004C14.1667 5.44207 14.3423 5.86599 14.6549 6.17855C14.9675 6.49111 15.3914 6.66671 15.8334 6.66671C16.2754 6.66671 16.6994 6.49111 17.0119 6.17855C17.3245 5.86599 17.5001 5.44207 17.5001 5.00004C17.5001 4.55801 17.3245 4.13409 17.0119 3.82153C16.6994 3.50897 16.2754 3.33337 15.8334 3.33337C15.3914 3.33337 14.9675 3.50897 14.6549 3.82153C14.3423 4.13409 14.1667 4.55801 14.1667 5.00004Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2.5 15C2.5 15.4421 2.67559 15.866 2.98816 16.1786C3.30072 16.4911 3.72464 16.6667 4.16667 16.6667C4.60869 16.6667 5.03262 16.4911 5.34518 16.1786C5.65774 15.866 5.83333 15.4421 5.83333 15C5.83333 14.558 5.65774 14.1341 5.34518 13.8215C5.03262 13.509 4.60869 13.3334 4.16667 13.3334C3.72464 13.3334 3.30072 13.509 2.98816 13.8215C2.67559 14.1341 2.5 14.558 2.5 15Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8.33325 15C8.33325 15.4421 8.50885 15.866 8.82141 16.1786C9.13397 16.4911 9.55789 16.6667 9.99992 16.6667C10.4419 16.6667 10.8659 16.4911 11.1784 16.1786C11.491 15.866 11.6666 15.4421 11.6666 15C11.6666 14.558 11.491 14.1341 11.1784 13.8215C10.8659 13.509 10.4419 13.3334 9.99992 13.3334C9.55789 13.3334 9.13397 13.509 8.82141 13.8215C8.50885 14.1341 8.33325 14.558 8.33325 15Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4.16675 6.66663V13.3333" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10 6.66663V13.3333" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M15.8334 6.66663V8.33329C15.8334 8.77532 15.6578 9.19924 15.3453 9.5118C15.0327 9.82436 14.6088 9.99996 14.1667 9.99996H4.16675" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
            <defs>
                <clipPath id="clip0_1049_4515">
                <rect width="20" height="20" fill="white"/>
                </clipPath>
            </defs>
            </svg>
            <?php echo $auto_transmission ? esc_html__("Auto", "tourfic") : esc_html__("Manual", "tourfic"); ?>
            <div class="tf-car-info-tooltip">
                <span><?php esc_html_e("This car has", "tourfic"); ?> <?php echo $auto_transmission ? esc_html__("an automatic", "tourfic") : esc_html__("manual", "tourfic"); ?> <?php esc_html_e("transmission", "tourfic"); ?></span>
            </div>
        </li>
        
        <?php if(!empty($fuel_included)){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
            <i class="ri-gas-station-line"></i><?php echo esc_html($fuel_included); ?>
            <div class="tf-car-info-tooltip">
                <span><?php esc_html_e("Fuel:", "tourfic"); ?> <?php echo esc_html($fuel_included); ?></span>
            </div>
        </li>
        <?php } ?>
        <?php if(!empty($shuttle_car)){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.66669 5V10M12.5 5V10M1.66669 10H18M15 15H17.5C17.5 15 17.9167 13.5833 18.1667 12.6667C18.25 12.3333 18.3334 12 18.3334 11.6667C18.3334 11.3333 18.25 11 18.1667 10.6667L17 6.5C16.75 5.66667 15.9167 5 15 5H3.33335C2.89133 5 2.4674 5.17559 2.15484 5.48816C1.84228 5.80072 1.66669 6.22464 1.66669 6.66667V15H4.16669M15 15C15 15.9205 14.2538 16.6667 13.3334 16.6667C12.4129 16.6667 11.6667 15.9205 11.6667 15M15 15C15 14.0795 14.2538 13.3333 13.3334 13.3333C12.4129 13.3333 11.6667 14.0795 11.6667 15M4.16669 15C4.16669 15.9205 4.91288 16.6667 5.83335 16.6667C6.75383 16.6667 7.50002 15.9205 7.50002 15M4.16669 15C4.16669 14.0795 4.91288 13.3333 5.83335 13.3333C6.75383 13.3333 7.50002 14.0795 7.50002 15M7.50002 15H11.6667" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>    
            <?php echo 'paid'==$shuttle_car_fee_type && !empty($shuttle_car_fee) ? wp_kses_post(wc_price($shuttle_car_fee)) : esc_html__('Free', 'tourfic'); ?>
            <div class="tf-car-info-tooltip">
                <span><?php esc_html_e("Shuttle", "tourfic"); ?> <?php echo 'paid'==$shuttle_car_fee_type && !empty($shuttle_car_fee) ? esc_html__('Fee:', 'tourfic'). esc_html(wc_price($shuttle_car_fee)) : esc_html__('Fee: Free', 'tourfic'); ?></span>
            </div>
        </li>
        <?php } ?>

        <?php if(function_exists( 'is_tf_pro' ) && is_tf_pro() && !empty($car_custom_info)){
            foreach($car_custom_info as $info){ 
            if(!empty($info['title'])){
            ?>
            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                <?php if(!empty($info['info_icon'])){ ?>
                    <i class="<?php echo esc_attr($info['info_icon']); ?>"></i>
                <?php } ?>
                <?php echo !empty($info['title']) ? esc_html($info['title']) : ''; ?>

                <?php if(!empty($info['content'])){ ?>
                <div class="tf-car-info-tooltip">
                <span><?php echo esc_html($info['content']); ?></span> </div>
                <?php } ?>
            </li>
        <?php }}} ?>
    </ul>
</div>