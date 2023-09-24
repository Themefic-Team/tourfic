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
			$post_id  = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
			$room_id  = isset( $_POST['room_id'] ) ? sanitize_text_field( $_POST['room_id'] ) : '';

			if ( trim( $ical_url ) == '' ) {
				$response['status']  = 'error';
				$response['message'] = esc_html__( 'Ical url is required field', 'tourfic' );
			}

			$meta  = get_post_meta( $post_id, 'tf_hotels_opt', true );
			$rooms = ! empty( $meta['room'] ) ? $meta['room'] : '';
			if ( ! empty( $rooms ) && gettype( $rooms ) == "string" ) {
				$tf_hotel_rooms_value = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
					return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
				}, $rooms );
				$rooms                = unserialize( $tf_hotel_rooms_value );
			}

			if ( ! empty( $ical_url ) ) {
				$ical = new TF_ICal_Reader( $ical_url );
				$response['$ical'] = $ical;
				if ( ! empty( $ical ) ) {
					$events               = $ical->events();

					if ( ! empty( $events ) && is_array( $events ) ) {
						foreach ( $events as $key => $event ) {
							$summary                      = explode( '-', $event['SUMMARY'] );
							$response['$event'. $key] = $event;
							if ( in_array( ' Not available', $summary ) || in_array( 'Blocked', $summary ) ) {

								if ( isset( $event['DTSTART'] ) && isset( $event['DTEND'] ) ) {
									if ( strlen( $event['DTSTART'] ) > 8 ) {
										$event['DTSTART'] = substr( $event['DTSTART'], 0, 8 );
									}
									if ( strlen( $event['DTEND'] ) > 8 ) {
										$event['DTEND'] = substr( $event['DTEND'], 0, 8 );
									}
									$start                      = DateTime::createFromFormat( 'Ymd', $event['DTSTART'] );
									$start                      = strtotime( $start->format( 'Y-m-d' ) );
									$end                        = DateTime::createFromFormat( 'Ymd', $event['DTEND'] );
									$end                        = strtotime( $end->format( 'Y-m-d' ) );

									//all date between start and end if start is 2023-09-22 and end is 2023-09-26 then all date between 2023-09-22 to 2023-09-26 will be in array like 2023-09-22, 2023-09-23, 2023-09-24, 2023-09-25, 2023-09-26
									$periods = new DatePeriod(
										new DateTime( date( 'Y-m-d', $start ) ),
										new DateInterval( 'P1D' ),
										new DateTime( date( 'Y-m-d', $end ) )
									);

									$all_dates = array();
									foreach ( $periods as $period ) {
										$all_dates[] = $period->format( 'Y-m-d' );
									}

									$response['period'.$key] = $all_dates;
								}
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