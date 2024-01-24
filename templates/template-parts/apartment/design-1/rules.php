<?php if ( isset( $meta['house_rules'] ) && ! empty( tf_data_types( $meta['house_rules'] ) ) ):
$included_house_rules = array();
$not_included_house_rules = array();
foreach ( tf_data_types( $meta['house_rules'] ) as $house_rule ) {
    if ( isset( $house_rule['include'] ) && $house_rule['include'] == '1' ) {
        $included_house_rules[] = $house_rule;
    } else {
        $not_included_house_rules[] = $house_rule;
    }
}
?>
<div class="tf-aprtment-rules-section">
    <h2><?php ! empty( $meta['house_rules_title'] ) ? esc_html_e( $meta['house_rules_title'] ) : ''; ?></h2>
    <div class="aprtment-inc-exc">
        <?php if ( ! empty( $included_house_rules ) ): ?>
        <div class="aprtment-single-rules">
            <ul>
                <?php
                foreach ( $included_house_rules as $item ) { ?>
                <li>
                    <div class="rules-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M7.77945 9.03072L8.68236 9.85296L14.0956 4.92324L15 5.74677L8.68236 11.5L4.6129 7.79409L5.51721 6.97057L6.87591 8.20789L7.77945 9.03072ZM7.78054 7.38408L10.9475 4.5L11.8493 5.32124L8.68236 8.20527L7.78054 7.38408ZM5.97299 10.6772L5.06944 11.5L1 7.79409L1.90432 6.97057L2.80787 7.79345L2.80711 7.79409L5.97299 10.6772Z" fill="#22C55E"/>
                        </svg>
                    </div>
                    <div class="rules-content">
                        <?php echo ! empty( $item['title'] ) ? '<span>' . esc_html( $item['title'] ) . '</span>' : ''; ?>
                        <?php echo ! empty( $item['desc'] ) ? '<p>' . esc_html( $item['desc'] ) . '</p>' : ''; ?>
                    </div>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>