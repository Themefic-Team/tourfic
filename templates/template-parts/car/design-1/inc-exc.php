<?php if(!empty($inc_exc_status)){ ?>
<div class="tf-car-inc-exc-section">
    <div class="tf-inc-exe tf-flex tf-flex-gap-16">
        <?php if(!empty($includes)){ ?>
        <div class="tf-inc-list">
            <h3><?php esc_html_e("Include", "tourfic"); ?></h3>
            <ul class="tf-flex tf-flex-gap-16 tf-flex-direction-column">
            <?php foreach($includes as $inc){ ?>
                <li class="tf-flex tf-flex-align-center tf-flex-gap-8">
                    <i class="<?php echo !empty($include_icon) ? esc_attr($include_icon) : 'ri-check-double-line'; ?>"></i>
                    <?php echo !empty($inc['title']) ? esc_html($inc['title']) : ''; ?>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>
        <?php if(!empty($excludes)){ ?>
        <div class="tf-exc-list">
            <h3><?php esc_html_e("Exclude", "tourfic"); ?></h3>
            <ul class="tf-flex tf-flex-gap-16 tf-flex-direction-column">
                <?php foreach($excludes as $exc){ ?>
                <li class="tf-flex tf-flex-align-center tf-flex-gap-8">
                    <i class="<?php echo !empty($exclude_icon) ? esc_attr($exclude_icon) : 'ri-close-circle-line'; ?>"></i>
                    <?php echo !empty($exc['title']) ? esc_html($exc['title']) : ''; ?>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>

    </div>
</div>
<?php } ?>