<?php if(!empty($calcellation_policy)): ?>
<div class="tf-room-cancellation-policy">
    <h2 class="tf-title tf-section-title"><?php echo esc_html($calcellation_policy_title); ?></h2>
    
    <table>
        <?php
        foreach($calcellation_policy as $policy):
            if( !empty($policy['before_cancel_time']) ): 
        ?>
            <tr>
                <td><?php echo esc_html($policy['before_cancel_time']); ?> <?php echo $policy['cancellation-times'] > 1 ? esc_html($policy['cancellation-times']).'s' : esc_html($policy['cancellation-times']); ?> <?php esc_html_e("Before", "tourfic"); ?></h3> </td>
                <td>
                    <?php if('free'==$policy['cancellation_type']){
                        echo esc_html__("Free Cancellation", "tourfic");
                    }else{
                        if( !empty($policy['refund_amount']) && 'percent'==$policy['refund_amount_type']){ 
                            echo esc_html($policy['refund_amount']).'% '. esc_html__("Deduction", "tourfic");
                        }
                        if( !empty($policy['refund_amount']) && 'fixed'==$policy['refund_amount_type']){ 
                            echo wc_price($policy['refund_amount']).' '. esc_html__("Deduction", "tourfic");
                        }
                    } ?>
                </td>
            </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>