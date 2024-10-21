<div class="tf-car-info" id="tf-car-info">
    <?php if(!empty($car_info_title)){ ?>
    <h3><?php echo esc_html($car_info_title); ?></h3>
    <?php } ?>
    <ul>
        <?php if(!empty($passengers)){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14.1667 3.75002C14.1667 4.91669 13.25 5.83335 12.0833 5.83335C10.9167 5.83335 10 4.91669 10 3.75002C10 2.58335 10.9167 1.66669 12.0833 1.66669C13.25 1.66669 14.1667 2.58335 14.1667 3.75002ZM12.5 6.66669H11.8333C10.0833 6.66669 8.41667 5.66669 7.58333 4.08335C7.5 4.00002 7.41667 3.91669 7.41667 3.83335L5.91667 4.50002C6.33333 5.66669 7.66667 7.16669 9.58333 7.91669L8.08333 12.0834L4.83333 11.1667L2.5 15.75L4.16667 16.1667L5.66667 13.1667L9.41667 14.1667C10.25 14.3334 11.0833 13.9167 11.4167 13.1667L13.3333 7.83335C13.5 7.25002 13.0833 6.66669 12.5 6.66669ZM15.75 5.83335L12.9167 13.6667C12.4167 15 11.1667 15.8334 9.83333 15.8334C9.58333 15.8334 9.25 15.8334 9 15.75L6.58333 15.0834L5.83333 16.5834L7.5 17L8.66667 17.3334C9.08333 17.4167 9.5 17.5 9.91667 17.5C12 17.5 13.8333 16.25 14.5833 14.25L17.5 5.83335H15.75Z" fill="#566676"/>
            </svg>
            <?php echo esc_attr($passengers); ?> <?php esc_html_e("Persons", "tourfic"); ?>
        </li>
        <?php } ?>
        <?php if(!empty($baggage)){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 10H10.0084M13.3334 5.00002V3.33335C13.3334 2.89133 13.1578 2.4674 12.8452 2.15484C12.5326 1.84228 12.1087 1.66669 11.6667 1.66669H8.33335C7.89133 1.66669 7.4674 1.84228 7.15484 2.15484C6.84228 2.4674 6.66669 2.89133 6.66669 3.33335V5.00002M18.3334 10.8334C15.8607 12.4658 12.963 13.3361 10 13.3361C7.03706 13.3361 4.13936 12.4658 1.66669 10.8334M3.33335 5.00002H16.6667C17.5872 5.00002 18.3334 5.74621 18.3334 6.66669V15C18.3334 15.9205 17.5872 16.6667 16.6667 16.6667H3.33335C2.41288 16.6667 1.66669 15.9205 1.66669 15V6.66669C1.66669 5.74621 2.41288 5.00002 3.33335 5.00002Z" stroke="#566676" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <?php echo esc_attr($baggage); ?> <?php esc_html_e("Bag", "tourfic"); ?>
        </li>
        <?php } ?>
        <?php if(!empty($fuel_types)){ ?>
            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2.5 18.3334H12.5M3.33333 7.50002H11.6667M11.6667 18.3334V3.33335C11.6667 2.89133 11.4911 2.4674 11.1785 2.15484C10.866 1.84228 10.442 1.66669 10 1.66669H5C4.55797 1.66669 4.13405 1.84228 3.82149 2.15484C3.50893 2.4674 3.33333 2.89133 3.33333 3.33335V18.3334M11.6667 10.8334H13.3333C13.7754 10.8334 14.1993 11.0089 14.5118 11.3215C14.8244 11.6341 15 12.058 15 12.5V14.1667C15 14.6087 15.1756 15.0326 15.4882 15.3452C15.8007 15.6578 16.2246 15.8334 16.6667 15.8334C17.1087 15.8334 17.5326 15.6578 17.8452 15.3452C18.1577 15.0326 18.3333 14.6087 18.3333 14.1667V8.19169C18.3335 7.97176 18.2902 7.75397 18.2058 7.55088C18.1214 7.34779 17.9976 7.1634 17.8417 7.00835L15 4.16669" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <?php echo esc_html($fuel_types); ?>
            </li>
        <?php } ?>

        <?php if(!empty($engine_years)){ ?>
            <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-car-line"></i><?php echo esc_html($engine_years); ?></li>
        <?php } ?>
        
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_257_3930)">
                <path d="M13 2.24999C11.2177 1.55755 9.25142 1.49912 7.4311 2.0845C5.61079 2.66988 4.04716 3.86345 3.00251 5.46499C1.95787 7.06652 1.49576 8.97859 1.69372 10.8804C1.89167 12.7823 2.73764 14.5582 4.08972 15.9103C5.44179 17.2624 7.21771 18.1083 9.11955 18.3063C11.0214 18.5042 12.9335 18.0421 14.535 16.9975C16.1365 15.9528 17.3301 14.3892 17.9155 12.5689C18.5009 10.7486 18.4424 8.78233 17.75 6.99999M11.1667 8.83332L15.8333 4.16665M11.6667 9.99999C11.6667 10.9205 10.9205 11.6667 10 11.6667C9.07953 11.6667 8.33334 10.9205 8.33334 9.99999C8.33334 9.07951 9.07953 8.33332 10 8.33332C10.9205 8.33332 11.6667 9.07951 11.6667 9.99999Z" stroke="#566676" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </g>
            <defs>
                <clipPath id="clip0_257_3930">
                <rect width="20" height="20" fill="white"/>
                </clipPath>
            </defs>
            </svg>
            <?php echo $unlimited_mileage ? esc_html_e("Unlimited", "tourfic") : $total_mileage.' '.$mileage_type; ?>
        </li>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-sound-module-fill"></i>
            <?php echo $auto_transmission ? esc_html_e("Auto", "tourfic") : esc_html_e("Manual", "tourfic"); ?>
        </li>
        
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-gas-station-line"></i><?php echo $fuel_included ? esc_html_e('Fuel Included') : esc_html_e('Fuel not Include'); ?></li>
        
        <?php if(!empty($shuttle_car)){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="fa-solid fa-van-shuttle"></i><?php echo 'paid'==$shuttle_car_fee_type && !empty($shuttle_car_fee) ? esc_html_e('Fee:'). wc_price($shuttle_car_fee) : esc_html_e('Fee: Free'); ?></li>
        <?php } ?>

        <?php if(!empty($car_custom_info)){
            foreach($car_custom_info as $info){ ?>
            <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
                <?php if(!empty($info['info_icon'])){ ?>
                    <i class="<?php echo esc_attr($info['info_icon']); ?>"></i>
                <?php } ?>
                <?php echo !empty($info['title']) ? esc_html($info['title']) : ''; ?>
            </li>
        <?php }} ?>
    </ul>
</div>