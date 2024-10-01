<div class="tf-car-info" id="tf-car-info">
    <h3><?php esc_html_e("Car info", "tourfic"); ?></h3>

    <ul>
        <?php if(!empty($passengers)){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="fa-solid fa-wheelchair"></i><?php echo esc_attr($passengers); ?> <?php esc_html_e("Persons", "tourfic"); ?></li>
        <?php } ?>
        <?php if(!empty($baggage)){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-briefcase-line"></i></i><?php echo esc_attr($baggage); ?> <?php esc_html_e("Bag", "tourfic"); ?></li>
        <?php } ?>
        <?php if(!empty($fuel_types)){ ?>
            <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-gas-station-line"></i><?php echo esc_html($fuel_types); ?></li>
        <?php } ?>

        <?php if(!empty($engine_years)){ ?>
            <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-car-line"></i><?php echo esc_html($engine_years); ?></li>
        <?php } ?>
        
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
            <i class="ri-speed-up-line"></i>
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