<?php
/**
 *
 * New Booking order template.
 *
 */

 $billing_details = array(
	'{fullname}'        => __( 'Name', 'tourfic' ),
	'{user_email}'      => __( 'Email', 'tourfic' ),
	'{billing_address}' => __( 'Billing Address', 'tourfic' ),
	'{city}'            => __( 'City', 'tourfic' ),
	'{country}'         => __( 'Country', 'tourfic' ),	
	'{phone}'         	=> __( 'Phone', 'tourfic' ),
);

$payment_details = array(
	'{price}'     	=> __( 'Price', 'tourfic' ),
	'{total_cost}' 	=> __( 'Total Cost', 'tourfic' ),
);

?>

<div class="content">
	<h3 class="greeting">
	<?php echo esc_html( TF_Handle_Emails::get_emails_strings( 'order_confirmation', $args['send_to'], 'greeting' ) ); ?>
	</h3>
	<p>
	<?php echo esc_html( TF_Handle_Emails::get_emails_strings( 'order_confirmation', $args['send_to'], 'greeting_byline' ) ); ?>
	</p>
	<div class="order-table">
		{booking_details}
	</div>
	<div class="customer-details" style="display: flex;flex-direction: row;justify-content:space-between; margin: 24px 0;">
		<div class="billing-info" style=" background: #e0f0fc6e;padding: 25px;">
			<h3><?php esc_html_e( 'Billing Details', 'tourfic' ); ?></h3>
			<table>
			<?php foreach ( $billing_details as $tag => $label ) : ?>
			
				<tr>
					<td><?php echo esc_html( $label ); ?></td>
					<td class="alignright"><?php echo esc_html( $tag ); ?></td>
				</tr>
			<?php endforeach; ?>
			</table>             
		</div>
		<!--<div class="shipping-info" style=" background: #e0f0fc6e;padding: 25px;">
			
		</div>
		-->
	</div>
	<div class="notice">
		<p>
			Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat
			duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet.
		</p>
	</div>
	<p> <?php esc_html_e('Thank you for purchasing.','tourfic') ?></p>
	<?php if ( 'admin' === $args['send_to'] ) : ?>
		<div class="order-button">
		<a href="{booking_url}"><?php esc_html_e( 'View booking on your website', 'tourfic' ); ?></a>
		</div>
	<?php endif; ?>
	
</div>
<div class="footer">
	<p><?php echo __('Sincerely','tourfic') ?>,</p>
	<p><strong>{fullname}</strong></p>
	<p>{site_name}</p>
	<div class="social">
		<a href="#">Facebook</a>
		<a href="#">Twitter</a>
		<a href="#">Instagram</a>
	</div>
</div>

<?php
