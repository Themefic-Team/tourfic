<?php
// don't load directly
defined( 'ABSPATH' ) || exit;
$badge_pro = '<div class="tf-csf-badge"><span class="tf-pro">' . __( "Pro Feature", "tourfic" ) . '</span></div>';
/**
 * Create Room Config Section for All global option of Room
 * @author Hena
 * @since 2.8.6
 */
CSF::createSection( $prefix, array(
	'parent' => 'hotel',
	'title'  => __( 'Room Config', 'tourfic' ),
	'fields' => array(

		array(
			'type'    => 'subheading',
			'content' => __( 'Hotel Room Configuration', 'tourfic' ),
		),

		array(
			'id'       => 'children_age_limit',
			'type'     => 'number',
			'class'    => 'tf-csf-disable tf-csf-pro',
			'title'    => __( 'Children age limit', 'tourfic' ),
			'desc'     => __( 'keep blank if don\'t want to add', 'tourfic' ),
			'subtitle' => __( $badge_pro, 'tourfic' ),
		),
	)

) );
?>