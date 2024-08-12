<?php if(!empty($benefits_status)){ ?>
<div class="tf-car-benefits">
    <h3><?php esc_html_e("Benefits", "tourfic"); ?></h3>

    <ul>
        <?php foreach($benefits as $singlebenefit){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
        <i class="<?php echo !empty($singlebenefit['icon']) ? esc_attr($singlebenefit['icon']) : ''; ?>"></i>
        <?php echo !empty($singlebenefit['title']) ? esc_html($singlebenefit['title']) : ''; ?>
        </li>
        <?php } ?>
    </ul>
</div>
<?php } ?>