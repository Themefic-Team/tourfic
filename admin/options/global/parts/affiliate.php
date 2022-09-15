<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

$badge_up     = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span></div>';
$badge_pro    = '<div class="tf-csf-badge"><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';
$badge_up_pro = '<div class="tf-csf-badge"><span class="tf-upcoming">' . __( "Upcoming", "tourfic" ) . '</span><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';

/**
 * Parent
 *
 * Main Menu
 */
CSF::createSection( $prefix, array(
	'id'     => 'affiliate',
	'title'  => __( 'Affiliate', 'tourfic' ),
	'icon'   => 'fa fa-handshake-o',
	'fields' => array(
		array(
			'type'    => 'subheading',
			'content' => __( 'Affiliate Settings', 'tourfic' ),
		),

		array(
			'id'         => 'affiliate-tabs',
			'type'       => 'tabbed',
			'class'      => 'tf-csf-disable tf-csf-pro',
			'desc'       => __( $badge_pro, 'tourfic' ),
			'attributes' => array(
				'style' => 'flex-wrap: wrap;',
			),
			'tabs'       => array(
				array(
					'title'  => 'Booking.com',
					'fields' => array(
						array(
							'id'       => 'enable-booking-dot-com',
							'type'     => 'switcher',
							'title'    => 'Enable Booking.com?',
							'text_on'  => 'Yes',
							'text_off' => 'No',
							'default'  => true
						),
					)
				),
				array(
					'title'  => 'TravelPayouts',
					'fields' => array(
						array(
							'id'       => 'enable-travel-payouts',
							'type'     => 'switcher',
							'title'    => 'Enable TravelPayouts?',
							'text_on'  => 'Yes',
							'text_off' => 'No',
							'default'  => true
						),
					)
				),
			)
		),
	)
) );


