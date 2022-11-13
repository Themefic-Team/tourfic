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
			'desc'       => $badge_pro,
			'attributes' => array(
				'style' => 'flex-wrap: wrap;',
			),
			'tabs'       => array(
				array(
					'title'  => __('Booking.com', 'tourfic'),
					'fields' => array(
						array(
							'id'       => 'enable-booking-dot-com',
							'type'     => 'switcher',
							'title'    => __( 'Enable Booking.com?', 'tourfic' ),
							'text_on'  => __( 'Yes', 'tourfic' ),
							'text_off' => __( 'No', 'tourfic' ),
							'default'  => true
						),
					)
				),
				array(
					'title'  => __('TravelPayouts', 'tourfic'),
					'fields' => array(
						array(
							'id'       => 'enable-travel-payouts',
							'type'     => 'switcher',
							'title'    => __( 'Enable TravelPayouts?', 'tourfic' ),
							'text_on'  => __( 'Yes', 'tourfic' ),
							'text_off' => __( 'No', 'tourfic' ),
							'default'  => true
						),
					)
				),
			)
		),
	)
) );


