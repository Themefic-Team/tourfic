<?php
/**
 *
 * New Booking order template.
 *
 * @since 4.3.0
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
	'{price}'     => __( 'Price', 'tourfic' ),
	'{total_cost}' => __( 'Total Cost', 'tourfic' ),
	'{due}'        => __( 'Due', 'tourfic' ),
);
?>
<table class="main" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="content-wrap aligncenter" style="font-family: Inter,sans-serif">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="content-block">
						<h1 class="aligncenter"><?php echo esc_html( TF_Handle_Emails::get_emails_strings( 'order', $args['send_to'], 'heading' ) ); ?></h1>
					</td>
				</tr>
				<tr>
					<td class="content-block aligncenter">
						<table class="invoice">
							<tr>
								<td style="margin: 0; padding: 5px 0;" valign="top"><?php echo esc_html( TF_Handle_Emails::get_emails_strings( 'order', $args['send_to'], 'greeting' ) ); ?><br><br>
								<?php echo esc_html( TF_Handle_Emails::get_emails_strings( 'order', $args['send_to'], 'greeting_byline' ) ); ?>
								</td>
							</tr>
							<br>
							<tr>
								<td style="margin: 0; padding: 5px 0;" valign="top">
									<table class="invoice-items" cellpadding="0" cellspacing="0">
										<tr>
											<td class="title-holder" style="margin: 0;" valign="top" colspan="2">
												<h3 class="alignleft"><?php echo esc_html__( 'Booking Trips ({booking_trips_count})', 'tourfic' ); ?></h3>
											</td>
										</tr>
										<tr><td colspan="2">{booking_details}</td></tr>
										<tr>
											<td class="title-holder" style="margin: 0;" valign="top">
												<h3 class="alignleft"><?php echo esc_html__( 'Billing Details', 'tourfic' ); ?></h3>
											</td>
										</tr>
										<?php foreach ( $billing_details as $tag => $label ) : ?>
											<tr>
												<td><?php echo esc_html( $label ); ?></td>
												<td class="alignright"><?php echo esc_html( $tag ); ?></td>
											</tr>
										<?php endforeach; ?>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php if ( 'admin' === $args['send_to'] ) : ?>
				<tr>
					<td class="content-block aligncenter">
						<a href="{booking_url}"><?php esc_html_e( 'View booking on your website', 'tourfic' ); ?></a>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<td class="content-block aligncenter">
						{site_name}
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
