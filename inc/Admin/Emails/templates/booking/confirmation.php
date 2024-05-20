<?php
/**
 *
 * New Booking order template.
 *
 */

 $billing_details = array(
	'{fullname}'        => esc_html__( 'Name', 'tourfic' ),
	'{user_email}'      => esc_html__( 'Email', 'tourfic' ),
	'{billing_address}' => esc_html__( 'Billing Address', 'tourfic' ),
	'{city}'            => esc_html__( 'City', 'tourfic' ),
	'{country}'         => esc_html__( 'Country', 'tourfic' ),
	'{phone}'         	=> esc_html__( 'Phone', 'tourfic' ),
);

$payment_details = array(
	'{price}'     	=> esc_html__( 'Price', 'tourfic' ),
	'{total_cost}' 	=> esc_html__( 'Total Cost', 'tourfic' ),
);
?>

<div style="padding-left:40px;padding-right:40px;margin: 0 auto;padding-top:30px;border: 1px solid #ddd;font-family: Inter,sans-serif">
	<h3 class="greeting" style="margin: 0; padding: 0;color:#5a5a5a;font-family: Inter,sans-serif;font-size:24px;">
	<?php echo esc_html( \Tourfic\Admin\Emails\TF_Handle_Emails::get_emails_strings( 'order_confirmation', $args['send_to'], 'greeting' ) ); ?>
	</h3>
	<p style="margin: 10px 0;font-family: Inter,sans-serif">
		<?php echo esc_html( \Tourfic\Admin\Emails\TF_Handle_Emails::get_emails_strings( 'order_confirmation', $args['send_to'], 'greeting_byline' ) ); ?>
	</p>
	<div class="order-table">
		{booking_details}                
	</div>
	<!-- Remaining code of the email template -->
	<table style="width:100%;max-width:600px;margin-top:15px;margin-bottom:15px;border:none" >
		<tr>
			<td style="background-color: #F2F9FE;padding: 20px;float: left; font-family: Inter,sans-serif">			
				<h3 style="font-size: 16px; font-weight: bold; color: #0209AF; margin: 0;">Billing address</h3>
				<?php foreach ( $billing_details as $tag => $label ) : ?>
					<p style="margin: 0;"><?php echo esc_html( $tag ); ?></p>
				<?php endforeach; ?>
			</td>
		</tr>
	</table>
	<div style="background-color: #F5F5FC; padding: 20px;">
		<p style="margin: 0;color:#81818F; font-family: Inter,sans-serif">
			Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat
			duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet.
		</p>
	</div>
	<p style="margin:10px 0;font-family: Inter,sans-serif">Thank you for purchasing.</p>
	<?php if ( 'admin' === $args['send_to'] ) : ?>
	<div style="margin: 10px 0;font-family: Inter,sans-serif">
		<a href="{booking_url}" style="display: inline-block; padding: 10px 15px; background-color: #0209AF; color: #fff; text-decoration: none;">View Order</a>
	</div>
	<?php endif; ?>
	<div style="padding: 20px 0;font-family: Inter,sans-serif">
		<p style="margin: 5px 0;">{site_name}</p>
		<div style="margin-top: 15px; padding-right: 10px;">
			<a href="#" style="margin: 10px 0 10px; display: inline-block; text-decoration: none; color: #0209AF;">Facebook</a>
			<a href="#" style="margin: 10px 0; display: inline-block; text-decoration: none; color: #0209AF;">Twitter</a>
			<a href="#" style="margin: 10px 0; display: inline-block; text-decoration: none; color: #0209AF;">Instagram</a>
		</div>
	</div>
</div>