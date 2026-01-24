<?php 
// Don't load directly

use Tourfic\Classes\Helper;

defined( 'ABSPATH' ) || exit;

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
    <div class="tf-house-rules">
        <h2 class="tf-title tf-section-title"><?php echo ! empty( $meta['house_rules_title'] ) ? esc_html( $meta['house_rules_title'] ) : ''; ?></h2>
        <div class="tf-house-rules-wrapper <?php echo empty( $included_house_rules ) || empty( $not_included_house_rules ) ? 'tf-house-rules-full' : ''; ?>">
            <?php if ( ! empty( $included_house_rules ) ): ?>
                <ul class="tf-included-house-rules">
                    <?php
                    foreach ( $included_house_rules as $item ) {
                        echo sprintf( '<li><h6>%s</h6> <span>%s</span></li>', wp_kses_post( $item['title'] ), wp_kses_post( $item['desc'] ) );
                    }
                    ?>
                </ul>
            <?php endif; ?>

            <?php if ( ! empty( $not_included_house_rules ) ): ?>
                <ul class="tf-not-included-house-rules">
                    <?php
                    foreach ( $not_included_house_rules as $item ) {
                        echo sprintf( '<li><h6>%s</h6> <span>%s</span></li>', wp_kses_post( $item['title'] ), wp_kses_post( $item['desc'] ) );
                    }
                    ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>