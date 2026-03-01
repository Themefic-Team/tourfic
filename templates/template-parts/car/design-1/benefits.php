<?php 
// Don't load directly
defined( 'ABSPATH' ) || exit;

if(!empty($benefits_status) && !empty($benefits)){ ?>
<div class="tf-car-benefits" id="tf-benefits">
    <?php if(!empty($benefits_sec_title)){ ?>   
    <h3><?php echo esc_html($benefits_sec_title); ?></h3>
    <?php } ?>

    <ul>
        <?php foreach($benefits as $singlebenefit){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
        <i class="<?php echo !empty($singlebenefit['icon']) ? esc_attr($singlebenefit['icon']) : 'ri-check-double-line'; ?>"></i>
        <?php echo !empty($singlebenefit['title']) ? esc_html($singlebenefit['title']) : ''; ?>
        </li>
        <?php } ?>
    </ul>
</div>
<?php } ?>