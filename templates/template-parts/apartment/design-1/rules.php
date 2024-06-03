<?php 

use \Tourfic\Classes\Helper;

if ( isset( $meta['house_rules'] ) && ! empty( Helper::tf_data_types( $meta['house_rules'] ) ) ):
$included_house_rules = array();
$not_included_house_rules = array();
foreach ( Helper::tf_data_types( $meta['house_rules'] ) as $house_rule ) {
    if ( isset( $house_rule['include'] ) && $house_rule['include'] == '1' ) {
        $included_house_rules[] = $house_rule;
    } else {
        $not_included_house_rules[] = $house_rule;
    }
}
?>
<div class="tf-aprtment-rules-section" id="tf-apartment-rules">
    <h2><?php echo ! empty( $meta['house_rules_title'] ) ? esc_html($meta['house_rules_title']) : ''; ?></h2>
    <div class="aprtment-inc-exc <?php echo empty( $included_house_rules ) || empty( $not_included_house_rules ) ? esc_attr('tf-inc-exc-full') : ''; ?>">
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
                        <?php echo ! empty( $item['desc'] ) ? '<p>' . wp_kses_post( $item['desc'] ) . '</p>' : ''; ?>
                    </div>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if ( ! empty( $not_included_house_rules ) ): ?>
        <div class="aprtment-single-rules">
            <ul>
                <?php
                foreach ( $not_included_house_rules as $item ) { ?>
                <li>
                    <div class="rules-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M7.99967 14.6667C4.31777 14.6667 1.33301 11.6819 1.33301 8.00004C1.33301 4.31814 4.31777 1.33337 7.99967 1.33337C11.6815 1.33337 14.6663 4.31814 14.6663 8.00004C14.6663 11.6819 11.6815 14.6667 7.99967 14.6667ZM7.99967 13.3334C10.9452 13.3334 13.333 10.9456 13.333 8.00004C13.333 5.05452 10.9452 2.66671 7.99967 2.66671C5.05415 2.66671 2.66634 5.05452 2.66634 8.00004C2.66634 10.9456 5.05415 13.3334 7.99967 13.3334ZM5.33301 9.33337H10.6663V10.6667H5.33301V9.33337ZM5.33301 7.33337C4.78072 7.33337 4.33301 6.88564 4.33301 6.33337C4.33301 5.78109 4.78072 5.33337 5.33301 5.33337C5.88529 5.33337 6.33301 5.78109 6.33301 6.33337C6.33301 6.88564 5.88529 7.33337 5.33301 7.33337ZM10.6663 7.33337C10.1141 7.33337 9.66634 6.88564 9.66634 6.33337C9.66634 5.78109 10.1141 5.33337 10.6663 5.33337C11.2186 5.33337 11.6663 5.78109 11.6663 6.33337C11.6663 6.88564 11.2186 7.33337 10.6663 7.33337Z" fill="#F01616"/>
                        </svg>
                    </div>
                    <div class="rules-content">
                        <?php echo ! empty( $item['title'] ) ? '<span>' . esc_html( $item['title'] ) . '</span>' : ''; ?>
                        <?php echo ! empty( $item['desc'] ) ? '<p>' . wp_kses_post( $item['desc'] ) . '</p>' : ''; ?>
                    </div>
                </li>
                <?php } ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>