<?php if(!empty($benefits_status)){ ?>
<div class="tf-car-benefits" id="tf-benefits">
    <h3><?php esc_html_e("Benefits", "tourfic"); ?></h3>

    <?php if(!empty($benefits)){ ?>
    <ul>
        <?php foreach($benefits as $singlebenefit){ ?>
        <li class="tf-flex tf-flex-align-center tf-flex-gap-6">
        <i class="<?php echo !empty($singlebenefit['icon']) ? esc_attr($singlebenefit['icon']) : 'ri-check-double-line'; ?>"></i>
        <?php echo !empty($singlebenefit['title']) ? esc_html($singlebenefit['title']) : ''; ?>
        </li>
        <?php } ?>
    </ul>
    <?php } ?>
</div>
<?php } ?>