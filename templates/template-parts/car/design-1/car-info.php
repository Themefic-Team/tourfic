<div class="tf-car-info" id="tf-car-info">
    <h3><?php esc_html_e("Car info", "tourfic"); ?></h3>

    <ul>
        <?php if(!empty($passengers)){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="fa-solid fa-wheelchair"></i><?php echo esc_attr($passengers); ?> <?php esc_html_e("Persons", "tourfic"); ?></li>
        <?php } ?>
        <?php if(!empty($baggage)){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6"><i class="ri-briefcase-line"></i></i><?php echo esc_attr($baggage); ?> <?php esc_html_e("Bag", "tourfic"); ?></li>
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