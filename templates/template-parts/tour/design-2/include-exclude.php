<?php if($inc || $exc){ ?>
<!-- Include Exclude srart -->
<div class="tf-include-exclude-wrapper">
    <h2 class="tf-section-title"><?php esc_html_e("Include/Exclude", "tourfic"); ?></h2>
    <div class="tf-include-exclude-innter">
        <?php if ( $inc ) { ?>
        <div class="tf-include">
            <ul>
                <?php
                foreach ( $inc as $key => $val ) {
                ?>
                <li>
                    <i class="<?php echo !empty($inc_icon) ? esc_attr( $inc_icon ) : 'fa-regular fa-circle-check'; ?>"></i>
                    <?php echo wp_kses_post($val['inc']); ?>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>
        <?php if ( $exc ) { ?>
        <div class="tf-exclude">
            <ul>
                <?php
                foreach ( $exc as $key => $val ) {
                ?>
                <li>
                    <i class="<?php echo !empty($exc_icon) ? esc_attr( $exc_icon ) : 'fa-regular fa-circle-check'; ?>"></i>
                    <?php echo wp_kses_post($val['exc']); ?>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php } ?>
    </div>
</div>
<!-- Include Exclude End -->
<?php } ?>