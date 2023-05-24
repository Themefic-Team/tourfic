<?php
/**
 *
 * New order cancellation template.
 * @return string
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

<div style="padding-left:50px;padding-right:50px;margin: 0 auto;margin-top:30px;">
	<h3 class="greeting" style="margin: 0; padding: 0;">
	<?php echo esc_html( TF_Handle_Emails::get_emails_strings( 'cancellation', $args['send_to'], 'greeting' ) ); ?>
	</h3>
	<p style="margin: 10px 0;">
		<?php echo esc_html( TF_Handle_Emails::get_emails_strings( 'cancellation', $args['send_to'], 'greeting_byline' ) ); ?>
	</p>
	<div class="order-table">
		{booking_details}                
	</div>
	<!-- Remaining code of the email template -->
	<table style="width:100%;max-width:600px;margin-top:15px;margin-bottom:15px;border:none" >
		<tr>
			<td style="background: #e0f0fc6e;padding: 20px;float: left;">			
				<h3 style="font-size: 16px; font-weight: bold; color: #0209AF; margin: 0;">Billing address</h3>
				<?php foreach ( $billing_details as $tag => $label ) : ?>
					<p style="margin: 0;"><?php echo esc_html( $tag ); ?></p>
				<?php endforeach; ?>
			</td>
		</tr>
	</div>
	<div style="background: #e0f0fc6e; padding: 20px;">
		<p style="margin: 0;">
			Amet minim mollit non deserunt ullamco est sit aliqua dolor do amet sint. Velit officia consequat
			duis enim velit mollit. Exercitation veniam consequat sunt nostrud amet.
		</p>
	</div>
	<p style="margin:10px 0">Thank you for purchasing.</p>
	<?php if ( 'admin' === $args['send_to'] ) : ?>
	<div style="margin: 10px 0;">
		<a href="{booking_url}" style="display: inline-block; padding: 10px 15px; background-color: #0209AF; color: #fff; text-decoration: none;">View Order</a>
	</div>
	<?php endif; ?>
	<div style="padding: 20px 0;">
		<p style="margin: 5px 0;">{site_name}</p>
		<div style="margin-top: 15px; padding-right: 10px;">
			<a href="#" style="margin: 10px 0 10px; display: inline-block; text-decoration: none; color: #0209AF;">Facebook</a>
			<a href="#" style="margin: 10px 0; display: inline-block; text-decoration: none; color: #0209AF;">Twitter</a>
			<a href="#" style="margin: 10px 0; display: inline-block; text-decoration: none; color: #0209AF;">Instagram</a>
		</div>
	</div>
</div>