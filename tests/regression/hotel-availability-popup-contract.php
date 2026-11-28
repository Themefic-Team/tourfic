<?php
/**
 * Render actual availability rows and real booking popups through WordPress KSES.
 *
 * Run: php tests/regression/hotel-availability-popup-contract.php [availability-row.php]
 * Optional: TOURFIC_POPUP_FIXTURE_EXPORT=/private/tmp/fixture.html exports sanitized
 * design-2 package markup for a separate browser harness after all assertions pass.
 * Only persistence, translations, nonce creation and WooCommerce price formatting
 * are fixtures. Helper's allowlist, pricing and Hotel::hotel_booking_popup are real.
 * No WordPress bootstrap, database, browser, network or customer session is used.
 */

if ( 'cli' !== PHP_SAPI || defined( 'ABSPATH' ) ) {
	exit( 1 );
}

$tourfic_root = dirname( __DIR__, 2 );
$tourfic_row_source = $argv[1] ?? $tourfic_root . '/templates/template-parts/hotel/hotel-availability-table-row.php';
define( 'ABSPATH', dirname( $tourfic_root, 3 ) . '/' );
define( 'WPINC', 'wp-includes' );
define( 'TOURFIC_ASSETS_APP_URL', 'https://example.invalid/tourfic/assets/app/' );

$tourfic_popup_meta = array();
$tourfic_popup_checks = 0;
$tourfic_popup_failures = array();
$tourfic_popup_export_html = '';

function get_option( $key, $default = false ) {
	if ( 'blog_charset' === $key ) { return 'UTF-8'; }
	return 'tourfic_settings' === $key ? array() : $default;
}
function get_post_meta( $id, $key, $single = false ) { return $GLOBALS['tourfic_popup_meta'][ $id ][ $key ] ?? ''; }
function get_post_field( $key, $id ) { return ''; }
function get_the_title( $id ) { return 'Fixture ' . (int) $id; }
function get_the_post_thumbnail_url( $id, $size = '' ) { return ''; }
function get_term_meta( $id, $key, $single = false ) { return array(); }
function get_term( $id ) { return (object) array( 'name' => 'Fixture feature' ); }
function wp_get_attachment_url( $id ) { return 'https://example.invalid/fixture.jpg'; }
function __( $text, $domain = '' ) { return $text; }
function _n( $single, $plural, $count, $domain = '' ) { return 1 === (int) $count ? $single : $plural; }
function esc_html__( $text, $domain = '' ) { return esc_html( $text ); }
function esc_html_e( $text, $domain = '' ) { echo esc_html( $text ); }
function esc_attr_e( $text, $domain = '' ) { echo esc_attr( $text ); }
function is_utf8_charset( $charset = null ) { return _is_utf8_charset( $charset ?? 'UTF-8' ); }
function _canonical_charset( $charset ) { return 'UTF-8'; }
function wp_allowed_protocols() { return array( 'http', 'https', 'mailto' ); }
function wp_create_nonce( $action ) { return 'fixture-' . $action; }
function wp_nonce_field( $action, $name ) { echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="' . esc_attr( wp_create_nonce( $action ) ) . '">'; }
function wc_price( $value ) { return '<span class="woocommerce-Price-amount">$' . number_format( (float) $value, 2 ) . '</span>'; }

foreach ( array( 'compat.php', 'compat-utf8.php', 'class-wp-token-map.php', 'utf8.php', 'plugin.php', 'formatting.php', 'kses.php' ) as $tourfic_library ) {
	if ( is_readable( ABSPATH . WPINC . '/' . $tourfic_library ) ) {
		require ABSPATH . WPINC . '/' . $tourfic_library;
	}
}
foreach ( array( 'html5-named-character-references.php', 'class-wp-html-attribute-token.php', 'class-wp-html-span.php', 'class-wp-html-doctype-info.php', 'class-wp-html-text-replacement.php', 'class-wp-html-decoder.php', 'class-wp-html-tag-processor.php' ) as $tourfic_library ) {
	if ( is_readable( ABSPATH . WPINC . '/html-api/' . $tourfic_library ) ) {
		require ABSPATH . WPINC . '/html-api/' . $tourfic_library;
	}
}
foreach ( array( 'Singleton', 'TF_Fonts', 'Action_Helper' ) as $tourfic_trait ) {
	require $tourfic_root . '/inc/Traits/' . $tourfic_trait . '.php';
}
require $tourfic_root . '/inc/Classes/Helper.php';
require $tourfic_root . '/inc/Classes/Hotel/Pricing.php';
require $tourfic_root . '/inc/Classes/Hotel/Hotel.php';

function tourfic_popup_expect( $condition, $label ) {
	++$GLOBALS['tourfic_popup_checks'];
	if ( ! $condition ) {
		$GLOBALS['tourfic_popup_failures'][] = $label;
	}
}

/** Include successive rooms in one shared scope, as the availability callback does. */
function tourfic_popup_render_rows( $design, $options_enabled, $airport_enabled ) {
	$hotel_id = $post_id = 41;
	$tf_hotel_selected_template_check = $design;
	$meta = array(
		'booking-by' => '1', 'booking-url' => '',
		'airport_service' => $airport_enabled ? '1' : '',
		'airport_service_type' => $airport_enabled ? array( 'pickup' ) : array(),
		'airport_pickup_price' => array( 'airport_pickup_price_type' => 'fixed', 'airport_service_fee_fixed' => '25' ),
	);
	$GLOBALS['tourfic_popup_meta'][ $hotel_id ]['tf_hotels_opt'] = $meta;
	$room_book_by = '1';
	$room_book_url = '';
	$pricing_by = $options_enabled ? '3' : '1';
	$form_adult = 2;
	$form_child = 1;
	$children_ages = '8';
	$adult_number = 2;
	$child_number = 1;
	$child_age_limit = 12;
	$footage = 25;
	$bed = 1;
	$first_location_name = 'Fixture location';
	$form_start = $form_check_in = $tf_startdate = '2026/12/01';
	$form_end = $form_check_out = $tf_enddate = '2026/12/02';
	$days = 1;
	$avil_by_date = false;
	$multi_by_date_ck = true;
	$avail_durationdate = array( '2026/12/01' );
	$tf_room_disable_date = $avail_date = array();
	$hotel_discount_type = 'none';
	$hotel_discount_amount = 0;
	$room_gallerys = '';
	$price = 100;
	$d_price = 0;
	$has_deposit = false;
	$deposit_amount = 0;
	$total_room_option_count = $options_enabled ? 2 : 0;

	ob_start();
	try {
		foreach ( array( 1490 => 3, 1491 => 2 ) as $room_id => $num_room_available ) {
			$unique_id = 'fixture-room-' . $room_id;
			$room_options = $options_enabled ? array(
				array( 'option_title' => 'Fixture Premium Package', 'option_pricing_type' => 'per_room', 'option_price' => '100' ),
				array( 'option_title' => 'Fixture Standard Package', 'option_pricing_type' => 'per_person', 'option_adult_price' => '75', 'option_child_price' => '35' ),
			) : array();
			$room = array(
				'unique_id' => $unique_id, 'room-options' => $room_options,
				'features' => array(), 'gallery' => '', 'avail_date' => '',
				'booking-by' => '1', 'booking-url' => '', 'deposit_type' => 'none',
			);
			$GLOBALS['tourfic_popup_meta'][ $room_id ]['tf_room_opt'] = $room;
			include $GLOBALS['tourfic_row_source'];
		}
		$html = ob_get_clean();
	} catch ( Throwable $error ) {
		ob_end_clean();
		throw $error;
	}
	return wp_kses( $html, \Tourfic\Classes\Helper::tf_custom_wp_kses_allow_tags() );
}

function tourfic_popup_dom( $html, $design ) {
	$errors = libxml_use_internal_errors( true );
	$dom = new DOMDocument();
	$table_design = in_array( $design, array( 'design-1', 'default' ), true );
	$wrapper = $table_design ? '<table><tbody>' . $html . '</tbody></table>' : '<div>' . $html . '</div>';
	$dom->loadHTML( '<!DOCTYPE html><html><body>' . $wrapper . '</body></html>', LIBXML_NONET );
	libxml_clear_errors();
	libxml_use_internal_errors( $errors );
	return $dom;
}

$tourfic_class_xpath = "contains(concat(' ',normalize-space(@class),' '),' %s ')";
set_error_handler( function( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );
foreach ( array( 'design-1', 'design-2', 'design-3', 'default' ) as $tourfic_design ) {
	foreach ( array( false, true ) as $tourfic_options ) {
		foreach ( array( false, true ) as $tourfic_airport ) {
			$label = $tourfic_design . '/' . ( $tourfic_options ? 'options' : 'base' ) . '/airport-' . (int) $tourfic_airport;
			try {
				$html = tourfic_popup_render_rows( $tourfic_design, $tourfic_options, $tourfic_airport );
				if ( 'design-2' === $tourfic_design && $tourfic_options && $tourfic_airport ) {
					$tourfic_popup_export_html = $html;
				}
				$dom = tourfic_popup_dom( $html, $tourfic_design );
				$xpath = new DOMXPath( $dom );
				$forms = $xpath->query( '//form[' . sprintf( $tourfic_class_xpath, 'tf-room' ) . ']' );
				tourfic_popup_expect( 2 <= $forms->length, $label . ': both successive rooms retain their forms after KSES' );
				if ( $tourfic_options ) {
					tourfic_popup_expect( 4 === $forms->length, $label . ': exactly two package forms exist for each room' );
					if ( 'design-2' === $tourfic_design ) {
						tourfic_popup_expect( 0 === $xpath->query( '//*[' . sprintf( $tourfic_class_xpath, 'tf-tabs-room' ) . ']//form' )->length, $label . ': package rooms do not render a standard mobile booking fallback' );
					}
				}
				$seen_rooms = array();
				$seen_options = array();
				foreach ( $forms as $index => $form ) {
					$form_label = $label . '/form-' . $index;
					$room_field = $xpath->query( './/input[@name="room_id"]', $form )->item( 0 );
					$room_id = $room_field ? (int) $room_field->getAttribute( 'value' ) : 0;
					tourfic_popup_expect( in_array( $room_id, array( 1490, 1491 ), true ), $form_label . ': room identifier survives sanitization' );
					$seen_rooms[ $room_id ] = true;
					$quantities = array();
					foreach ( $xpath->query( './/select[@name="hotel_room_selected"]/option', $form ) as $option ) {
						$quantities[] = '' !== $option->getAttribute( 'value' ) ? $option->getAttribute( 'value' ) : trim( $option->textContent );
					}
					$expected_quantity = 1490 === $room_id ? array( '1', '2', '3' ) : array( '1', '2' );
					tourfic_popup_expect( $expected_quantity === $quantities, $form_label . ': quantities are positive and reset for each successive room' );
					$wraps = $xpath->query( './/*[' . sprintf( $tourfic_class_xpath, 'room-submit-wrap' ) . ']', $form );
					$wrap = $wraps->item( 0 );
					$triggers = $wrap ? $xpath->query( './/*[self::a or self::button]', $wrap ) : array();
					tourfic_popup_expect( 1 === count( $triggers ), $form_label . ': one primary reserve trigger is available' );
					foreach ( $triggers as $trigger ) {
						$classes = preg_split( '/\s+/', trim( $trigger->getAttribute( 'class' ) ) );
						tourfic_popup_expect( in_array( 'tf-hotel-booking-popup-btn', $classes, true ), $form_label . ': trigger matches the delegated popup click handler' );
						tourfic_popup_expect( in_array( 'tf_btn', $classes, true ), $form_label . ': reserve trigger retains shared button styling' );
						if ( 'a' === $trigger->tagName ) {
							tourfic_popup_expect( '#' === $trigger->getAttribute( 'href' ), $form_label . ': popup anchor remains a safe fragment after KSES' );
						}
					}
					$popups = $xpath->query( './*[' . sprintf( $tourfic_class_xpath, 'tf-withoutpayment-booking' ) . ']', $form );
					tourfic_popup_expect( 1 === $popups->length, $form_label . ': actual booking popup is inside this form and sibling to its trigger wrapper' );
					$nonce = $xpath->query( './/input[@name="tf_room_booking_nonce"]', $form )->item( 0 );
					tourfic_popup_expect( $nonce && 'fixture-check_room_booking_nonce' === $nonce->getAttribute( 'value' ), $form_label . ': booking nonce survives sanitization' );
					if ( $tourfic_airport ) {
						tourfic_popup_expect( 1 === $xpath->query( './/select[@name="airport_service"]/option[@value="pickup"]', $form )->length, $form_label . ': real popup retains the configured airport service' );
					}
					$option_field = $xpath->query( './/input[@name="option_id"]', $form )->item( 0 );
					if ( $tourfic_options ) {
						$option_id = $option_field ? $option_field->getAttribute( 'value' ) : '';
						$allowed_options = array( 'fixture-room-' . $room_id . '_0', 'fixture-room-' . $room_id . '_1' );
						tourfic_popup_expect( in_array( $option_id, $allowed_options, true ), $form_label . ': booking keeps an option identifier owned by this room' );
						$seen_options[ $option_id ] = true;
					}
				}
				tourfic_popup_expect( isset( $seen_rooms[1490], $seen_rooms[1491] ), $label . ': successive includes preserve both room identities' );
				if ( $tourfic_options ) {
					$expected_options = array( 'fixture-room-1490_0', 'fixture-room-1490_1', 'fixture-room-1491_0', 'fixture-room-1491_1' );
					tourfic_popup_expect( ! array_diff( $expected_options, array_keys( $seen_options ) ), $label . ': both package options survive for each room' );
				}
			} catch ( Throwable $tourfic_error ) {
				tourfic_popup_expect( false, $label . ': rendering raised ' . $tourfic_error->getMessage() );
			}
		}
	}
}
restore_error_handler();

$tourfic_popup_export = getenv( 'TOURFIC_POPUP_FIXTURE_EXPORT' );
if ( $tourfic_popup_export && ! $tourfic_popup_failures ) {
	$tourfic_export_dir = realpath( dirname( $tourfic_popup_export ) );
	if ( ! $tourfic_export_dir || ( '/private/tmp' !== $tourfic_export_dir && 0 !== strpos( $tourfic_export_dir, '/private/tmp/' ) )
		|| 'html' !== pathinfo( $tourfic_popup_export, PATHINFO_EXTENSION ) || is_link( $tourfic_popup_export ) ) {
		throw new RuntimeException( 'Fixture export requires an HTML file inside /private/tmp.' );
	}
	$tourfic_document = '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>'
		. '<body class="single-tf_hotel"><div class="tf-single-template__two"><div class="tf-available-rooms-wrapper">'
		. $tourfic_popup_export_html . '</div></div></body></html>';
	file_put_contents( $tourfic_popup_export, $tourfic_document );
}

foreach ( $tourfic_popup_failures as $tourfic_failure ) {
	echo 'FAIL: ' . $tourfic_failure . "\n";
}
echo json_encode( array( 'checks' => $tourfic_popup_checks, 'failures' => count( $tourfic_popup_failures ) ) ) . "\n";
exit( $tourfic_popup_failures ? 1 : 0 );
