<?php 

use \Tourfic\Classes\Helper;

if ( isset( $meta['highlights'] ) && ! empty( Helper::tf_data_types( $meta['highlights'] ) ) ) :
   $tf_highlights_count = count(Helper::tf_data_types( $meta['highlights'] ));
?>
<!--Information Section Start -->
<div class="tf-overview-wrapper">
    <div class="<?php echo $tf_highlights_count > 4 ? esc_attr('tf-features-block-slides') : esc_attr('tf-features-block-wrapper'); ?> tf-informations-secations">
        
        <?php
        foreach ( Helper::tf_data_types( $meta['highlights'] ) as $highlight ) :
        if ( empty( $highlight['title'] ) ) {
            continue;
        }
        ?>
        <div class="tf-feature-block">
            <?php echo ! empty( $highlight['icon'] ) ? "<i class='" . esc_attr( $highlight['icon'] ) . "'></i>" : ''; ?>
            <div class="tf-feature-block-details">
                <h5><?php echo esc_html( $highlight['title'] ); ?></h5>
                <?php 
                echo ! empty( $highlight['subtitle'] ) ? '<p>' . esc_html( $highlight['subtitle'] ) . '</p>' : ''; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
    </div>
</div>
<?php endif; ?>