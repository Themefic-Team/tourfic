<!-- Tour include exclude -->
<?php if($inc || $exc){ ?>
<div class="tf-inex-wrapper tf-mb-50 tf-template-section">
    <div class="tf-inex-inner tf-flex tf-flex-gap-24">
        <?php if ( $inc ) { ?>
        <div class="tf-inex tf-tour-include tf-box">
            <h3 class="tf-section-title"><?php esc_html_e( 'Included', 'tourfic' ); ?></h3>
            <ul class="tf-list">
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
        <div class="tf-inex tf-tour-exclude tf-box">
            <h3 class="tf-section-title"><?php esc_html_e( 'Excluded', 'tourfic' ); ?></h3>
            <ul class="tf-list">
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
<?php } ?>