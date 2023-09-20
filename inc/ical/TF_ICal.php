<?php
defined( 'ABSPATH' ) || exit;

/**
 * TF iCal sync
 * @since 2.9.26
 * @author Foysal
 */
if ( ! class_exists( 'TF_ICal' ) ) {
	class TF_ICal {

		private static $instance = null;

		/**
		 * Singleton instance
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( self::$instance == null ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function __construct() {
			add_action( 'wp_ajax_tf_import_ical', array( $this, 'tf_import_ical' ) );
		}

		public function tf_import_ical() {
			$response = array();
			$tfNonce  = isset( $_POST['tf_nonce'] ) ? sanitize_text_field( $_POST['tf_nonce'] ) : '';
			$ical_url = isset( $_POST['ical_url'] ) ? sanitize_url( $_POST['ical_url'] ) : '';

			//check nonce
//			if ( ! wp_verify_nonce( $tfNonce, 'updates' ) ) {
//				$response['status']  = 'error';
//				$response['message'] = esc_html__( 'Invalid nonce', 'tourfic' );
//			}

			if ( trim( $ical_url ) == '' ) {
				$response['status']  = 'error';
				$response['message'] = esc_html__( 'Ical url is required field', 'tourfic' );
			}

			if ( ! empty( $ical_url ) ) {
				$ical = new TF_ICal_Reader( $ical_url );
				if ( ! empty( $ical ) ) {
					$events       = $ical->events();
					$events_count = count( $events );
					$response['eventsgg'] = $events;

					if ( ! empty( $events ) && is_array( $events ) ) {
						foreach ( $events as $key => $event ) {
							$sumary    = explode( '|', $event['SUMMARY'] );

							if ( in_array('Not available', $sumary) || in_array('Blocked', $sumary) ) {
								$available = 'unavailable';
							} else {
								$price = (float) $sumary[0];
								if ( $price < 0 ) {
									$price = 0;
								}
								if ( isset( $sumary[1] ) ) {
									$adult_price = floatval( $sumary[1] );
								}
								if ( isset( $sumary[2] ) ) {
									$child_price = floatval( $sumary[2] );
								}
							}
							if ( isset( $sumary[1] ) && ! empty( $sumary[1] ) && strtolower( $sumary[1] ) == 'unavailable' ) {
								$available = 'unavailable';
							}
							if ( isset( $event['DTSTART'] ) && isset( $event['DTEND'] ) ) {
								if ( strlen( $event['DTSTART'] ) > 8 ) {
									$event['DTSTART'] = substr( $event['DTSTART'], 0, 8 );
								}
								if ( strlen( $event['DTEND'] ) > 8 ) {
									$event['DTEND'] = substr( $event['DTEND'], 0, 8 );
								}
								$start        = DateTime::createFromFormat( 'Ymd', $event['DTSTART'] );
								$start        = strtotime( $start->format( 'Y-m-d' ) );
								$end          = DateTime::createFromFormat( 'Ymd', $event['DTEND'] );
								$end          = strtotime( $end->format( 'Y-m-d' ) );
								$end          = strtotime( '-1 day', $end );
//								$res          = $this->import_event_hotel_room( $post_id, $post_type, $price, $start, $end, $available, $adult_price, $child_price );
//								$result_total += $res;
							}
						}
					}
				}
			}

			echo json_encode( $response );
			wp_die();
		}
	}
}

TF_ICal::instance();