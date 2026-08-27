<?php

namespace Tourfic\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Registers a shared booking-popup AJAX endpoint.
 */
abstract class Booking_Popup {

	/**
	 * Popup configuration.
	 *
	 * @var array
	 */
	protected array $args = array();

	/**
	 * Register the configured AJAX actions.
	 *
	 * @param array $args Popup configuration.
	 */
	public function __construct( array $args ) {
		$this->args = $args;
		$action     = $this->args['post_type'] . '_booking_popup';

		add_action( 'wp_ajax_' . $action, array( $this, 'booking_popup_callback' ) );
		add_action( 'wp_ajax_nopriv_' . $action, array( $this, 'booking_popup_callback' ) );
	}

	/**
	 * Return the booking-popup response.
	 */
	abstract public function booking_popup_callback();

}
