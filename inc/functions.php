<?php
// don't load directly
defined( 'ABSPATH' ) || exit;

use Tourfic\Classes\Helper;
use Tourfic\Classes\Room\Availability;
use Tourfic\Classes\Room\Room;

/**
 * Show admin warning if a required file is missing
 */
function tf_file_missing( $files = '' ) {

	if ( is_admin() ) {
		if ( ! empty( $files ) ) {
			$class   = 'notice notice-error';
			$message = '<strong>' . $files . '</strong>' . esc_html__( ' file is missing! It is required to function Tourfic properly!', 'tourfic' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );
		}
	}

}

add_action( 'admin_notices', 'tf_file_missing' );
add_action( 'plugins_loaded', 'tf_add_elelmentor_addon' );

if ( ! function_exists( 'tf_tour_traveler_info_mode_settings' ) ) {
	/**
	 * Inject tour traveler info collection mode under Tour Options -> Extras.
	 *
	 * @param array $sections Settings sections.
	 * @return array
	 */
	function tf_tour_traveler_info_mode_settings( $sections ) {
		if ( empty( $sections['tour_booking_settings']['fields'] ) || ! is_array( $sections['tour_booking_settings']['fields'] ) ) {
			return $sections;
		}

		foreach ( $sections['tour_booking_settings']['fields'] as $field ) {
			if ( ! empty( $field['id'] ) && 'tour_traveler_info_collection_mode' === $field['id'] ) {
				return $sections;
			}
		}

		$mode_field = array(
			'id'         => 'tour_traveler_info_collection_mode',
			'type'       => 'select',
			'label'      => esc_html__( 'Traveler Info Collection Mode', 'tourfic' ),
			'subtitle'   => esc_html__( 'Choose whether to collect traveler details for all travelers or only one traveler.', 'tourfic' ),
			'options'    => array(
				'all'    => esc_html__( 'All Travelers', 'tourfic' ),
				'single' => esc_html__( 'One Traveler', 'tourfic' ),
			),
			'default'    => 'all',
			'dependency' => array(
				array( 'disable_traveller_info', '==', 'true' ),
			),
		);

		$insert_index = null;
		foreach ( $sections['tour_booking_settings']['fields'] as $index => $field ) {
			if ( ! empty( $field['id'] ) && 'disable_traveller_info' === $field['id'] ) {
				$insert_index = $index + 1;
				break;
			}
		}

		if ( null === $insert_index ) {
			$sections['tour_booking_settings']['fields'][] = $mode_field;
		} else {
			array_splice( $sections['tour_booking_settings']['fields'], $insert_index, 0, array( $mode_field ) );
		}

		return $sections;
	}
}
add_filter( 'tf_settings_sections', 'tf_tour_traveler_info_mode_settings', 30 );

if ( ! function_exists( 'tf_tour_traveler_compliance_settings' ) ) {
	/**
	 * Inject traveler compliance settings into Tour options.
	 *
	 * @param array $sections Settings sections.
	 * @return array
	 */
	function tf_tour_traveler_compliance_settings( $sections ) {
		if ( empty( $sections['tour_booking_settings']['fields'] ) || ! is_array( $sections['tour_booking_settings']['fields'] ) ) {
			return $sections;
		}

		$fields = $sections['tour_booking_settings']['fields'];

		$has_age_validation_setting = false;
		foreach ( $fields as $field ) {
			if ( ! empty( $field['id'] ) && 'tour_traveler_age_validation' === $field['id'] ) {
				$has_age_validation_setting = true;
				break;
			}
		}

		if ( ! $has_age_validation_setting ) {
			$age_validation_fields = array(
				array(
					'id'         => 'tour_traveler_age_validation',
					'type'       => 'switch',
					'label'      => esc_html__( 'Enable Traveler Age Validation', 'tourfic' ),
					'subtitle'   => esc_html__( 'Validate configured traveler date fields against adult, child, and infant age limits during booking.', 'tourfic' ),
					'label_on'   => esc_html__( 'Yes', 'tourfic' ),
					'label_off'  => esc_html__( 'No', 'tourfic' ),
					'default'    => '0',
					'dependency' => array(
						array( 'disable_traveller_info', '==', 'true' ),
					),
				),
				array(
					'id'         => 'tour_traveler_adult_min_age',
					'type'       => 'number',
					'label'      => esc_html__( 'Adult Minimum Age', 'tourfic' ),
					'subtitle'   => esc_html__( 'Travelers at or above this age are treated as adults.', 'tourfic' ),
					'default'    => 12,
					'dependency' => array(
						array( 'disable_traveller_info', '==', 'true' ),
						array( 'tour_traveler_age_validation', '==', 'true' ),
					),
				),
				array(
					'id'         => 'tour_traveler_child_min_age',
					'type'       => 'number',
					'label'      => esc_html__( 'Child Minimum Age', 'tourfic' ),
					'subtitle'   => esc_html__( 'Travelers from this age up to the adult minimum age are treated as children.', 'tourfic' ),
					'default'    => 2,
					'dependency' => array(
						array( 'disable_traveller_info', '==', 'true' ),
						array( 'tour_traveler_age_validation', '==', 'true' ),
					),
				),
				array(
					'id'         => 'tour_traveler_infant_max_age',
					'type'       => 'number',
					'label'      => esc_html__( 'Infant Maximum Age', 'tourfic' ),
					'subtitle'   => esc_html__( 'Travelers below this age are treated as infants.', 'tourfic' ),
					'default'    => 2,
					'dependency' => array(
						array( 'disable_traveller_info', '==', 'true' ),
						array( 'tour_traveler_age_validation', '==', 'true' ),
					),
				),
			);

			$insert_index = null;
			foreach ( $fields as $index => $field ) {
				if ( ! empty( $field['id'] ) && 'tour_traveler_info_collection_mode' === $field['id'] ) {
					$insert_index = $index + 1;
					break;
				}
			}

			if ( null === $insert_index ) {
				$fields = array_merge( $fields, $age_validation_fields );
			} else {
				array_splice( $fields, $insert_index, 0, $age_validation_fields );
			}
		}

		foreach ( $fields as &$field ) {
			if ( empty( $field['id'] ) || 'without-payment-field' !== $field['id'] || empty( $field['fields'] ) || ! is_array( $field['fields'] ) ) {
				continue;
			}

			$has_age_validation_toggle = false;
			foreach ( $field['fields'] as $sub_field ) {
				if ( ! empty( $sub_field['id'] ) && 'reg-field-age-validation' === $sub_field['id'] ) {
					$has_age_validation_toggle = true;
					break;
				}
			}

			foreach ( $field['fields'] as $sub_index => &$sub_field ) {
				if ( ! empty( $sub_field['id'] ) && 'reg-fields-type' === $sub_field['id'] && ! empty( $sub_field['options'] ) && is_array( $sub_field['options'] ) ) {
					if ( ! array_key_exists( 'file', $sub_field['options'] ) ) {
						$sub_field['options']['file'] = esc_html__( 'File Upload', 'tourfic' );
					}
				}

				if ( ! $has_age_validation_toggle && ! empty( $sub_field['id'] ) && 'reg-field-required' === $sub_field['id'] ) {
					array_splice(
						$field['fields'],
						$sub_index + 1,
						0,
						array(
							array(
								'id'         => 'reg-field-age-validation',
								'type'       => 'switch',
								'label'      => esc_html__( 'Validate Age Limit?', 'tourfic' ),
								'label_on'   => esc_html__( 'Yes', 'tourfic' ),
								'label_off'  => esc_html__( 'No', 'tourfic' ),
								'default'    => '0',
								'dependency' => array(
									array( 'reg-fields-type', '==', 'date' ),
								),
								'class'      => 'tf_hidden_fields',
							),
						)
					);
					$has_age_validation_toggle = true;
				}
			}
			unset( $sub_field );
		}
		unset( $field );

		$sections['tour_booking_settings']['fields'] = $fields;

		return $sections;
	}
}
add_filter( 'tf_settings_sections', 'tf_tour_traveler_compliance_settings', 35 );

if ( ! function_exists( 'tf_tour_is_global_traveler_info_enabled' ) ) {
	/**
	 * Check whether global tour traveler info is enabled.
	 *
	 * @return bool
	 */
	function tf_tour_is_global_traveler_info_enabled() {
		return function_exists( 'is_tf_pro' ) && is_tf_pro() && ! empty( Helper::tfopt( 'disable_traveller_info' ) );
	}
}

if ( ! function_exists( 'tf_tour_is_traveler_info_enabled' ) ) {
	/**
	 * Check whether traveler info should be collected for a tour.
	 *
	 * @param array $tour_meta Tour meta settings.
	 * @return bool
	 */
	function tf_tour_is_traveler_info_enabled( $tour_meta = array() ) {
		if ( ! tf_tour_is_global_traveler_info_enabled() ) {
			return false;
		}

		if ( ! is_array( $tour_meta ) ) {
			return false;
		}

		return ! empty( $tour_meta['tour-traveler-info'] );
	}
}

if ( ! function_exists( 'tf_tour_get_traveler_info_fields' ) ) {
	/**
	 * Get configured tour traveler info fields.
	 *
	 * @return array
	 */
	function tf_tour_get_traveler_info_fields() {
		$fields = Helper::tfopt( 'without-payment-field' );
		$fields = ! empty( $fields ) ? Helper::tf_data_types( $fields ) : array();

		return is_array( $fields ) ? $fields : array();
	}
}

if ( ! function_exists( 'tf_tour_get_age_validation_settings' ) ) {
	/**
	 * Get traveler age validation settings.
	 *
	 * @return array
	 */
	function tf_tour_get_age_validation_settings() {
		$adult_min_age  = Helper::tfopt( 'tour_traveler_adult_min_age' );
		$child_min_age  = Helper::tfopt( 'tour_traveler_child_min_age' );
		$infant_max_age = Helper::tfopt( 'tour_traveler_infant_max_age' );

		return array(
			'enabled'         => ! empty( Helper::tfopt( 'tour_traveler_age_validation' ) ),
			'adult_min_age'   => '' !== (string) $adult_min_age ? max( 1, absint( $adult_min_age ) ) : 12,
			'child_min_age'   => '' !== (string) $child_min_age ? absint( $child_min_age ) : 2,
			'infant_max_age'  => '' !== (string) $infant_max_age ? max( 1, absint( $infant_max_age ) ) : 2,
			'date_format'     => tf_tour_get_user_date_format(),
			'collection_mode' => ! empty( Helper::tfopt( 'tour_traveler_info_collection_mode' ) ) ? sanitize_key( Helper::tfopt( 'tour_traveler_info_collection_mode' ) ) : 'all',
		);
	}
}

if ( ! function_exists( 'tf_tour_get_user_date_format' ) ) {
	/**
	 * Get configured user-facing date format.
	 *
	 * @return string
	 */
	function tf_tour_get_user_date_format() {
		$date_format = ! empty( Helper::tfopt( 'tf-date-format-for-users' ) ) ? sanitize_text_field( Helper::tfopt( 'tf-date-format-for-users' ) ) : 'Y/m/d';
		$allowed     = array( 'Y/m/d', 'd/m/Y', 'm/d/Y', 'Y-m-d', 'd-m-Y', 'm-d-Y', 'Y.m.d', 'd.m.Y', 'm.d.Y' );

		return in_array( $date_format, $allowed, true ) ? $date_format : 'Y/m/d';
	}
}

if ( ! function_exists( 'tf_tour_get_supported_date_formats' ) ) {
	/**
	 * Get date formats supported by traveler age validation.
	 *
	 * @return array
	 */
	function tf_tour_get_supported_date_formats() {
		return array(
			'Y/m/d',
			'Y-m-d',
			'Y.m.d',
			'd/m/Y',
			'd-m-Y',
			'd.m.Y',
			'm/d/Y',
			'm-d-Y',
			'm.d.Y',
		);
	}
}

if ( ! function_exists( 'tf_tour_get_age_validation_field_names' ) ) {
	/**
	 * Get traveler date field names that should be age-validated.
	 *
	 * @return array
	 */
	function tf_tour_get_age_validation_field_names() {
		$settings = tf_tour_get_age_validation_settings();
		if ( empty( $settings['enabled'] ) ) {
			return array();
		}

		$field_names = array();
		$fields      = tf_tour_get_traveler_info_fields();

		if ( empty( $fields ) ) {
			return array( 'tf_dob' );
		}

		foreach ( $fields as $field ) {
			if ( empty( $field['reg-fields-type'] ) || 'date' !== $field['reg-fields-type'] || empty( $field['reg-field-age-validation'] ) || empty( $field['reg-field-name'] ) ) {
				continue;
			}

			$field_names[] = sanitize_key( $field['reg-field-name'] );
		}

		return array_values( array_unique( array_filter( $field_names ) ) );
	}
}

if ( ! function_exists( 'tf_tour_get_file_upload_fields' ) ) {
	/**
	 * Get configured traveler file upload fields.
	 *
	 * @return array
	 */
	function tf_tour_get_file_upload_fields() {
		$file_fields = array();

		foreach ( tf_tour_get_traveler_info_fields() as $field ) {
			if ( empty( $field['reg-fields-type'] ) || 'file' !== $field['reg-fields-type'] || empty( $field['reg-field-name'] ) ) {
				continue;
			}

			$field_name = sanitize_key( $field['reg-field-name'] );
			$file_fields[ $field_name ] = array(
				'name'     => $field_name,
				'label'    => ! empty( $field['reg-field-label'] ) ? sanitize_text_field( $field['reg-field-label'] ) : $field_name,
				'required' => ! empty( $field['reg-field-required'] ),
			);
		}

		return $file_fields;
	}
}

if ( ! function_exists( 'tf_tour_get_frontend_compliance_config' ) ) {
	/**
	 * Get traveler compliance config for localized frontend JS.
	 *
	 * @return array
	 */
	function tf_tour_get_frontend_compliance_config() {
		$settings = tf_tour_get_age_validation_settings();

		return array(
			'enabled'         => ! empty( $settings['enabled'] ),
			'field_names'     => tf_tour_get_age_validation_field_names(),
			'adult_min_age'   => max( 1, absint( $settings['adult_min_age'] ) ),
			'child_min_age'   => absint( $settings['child_min_age'] ),
			'infant_max_age'  => max( 1, absint( $settings['infant_max_age'] ) ),
			'date_format'     => $settings['date_format'],
			'collection_mode' => $settings['collection_mode'],
		);
	}
}

if ( ! function_exists( 'tf_tour_parse_date_by_format' ) ) {
	/**
	 * Parse a date string by a strict Tourfic date format.
	 *
	 * @param string $date_string Date string.
	 * @param string $date_format Date format.
	 * @return DateTimeImmutable|null
	 */
	function tf_tour_parse_date_by_format( $date_string, $date_format ) {
		$date_string = trim( (string) $date_string );
		$date_format = trim( (string) $date_format );
		if ( '' === $date_string || '' === $date_format ) {
			return null;
		}

		if ( ! preg_match( '/[^A-Za-z]/', $date_format, $separator_match ) ) {
			return null;
		}

		$separator    = $separator_match[0];
		$format_parts = explode( $separator, $date_format );
		if ( 3 !== count( $format_parts ) ) {
			return null;
		}

		$pattern_parts = array();
		foreach ( $format_parts as $part ) {
			if ( 'Y' === $part ) {
				$pattern_parts[] = '(?P<Y>\d{4})';
			} elseif ( 'm' === $part ) {
				$pattern_parts[] = '(?P<m>\d{1,2})';
			} elseif ( 'd' === $part ) {
				$pattern_parts[] = '(?P<d>\d{1,2})';
			} else {
				return null;
			}
		}

		$pattern = '/^' . implode( preg_quote( $separator, '/' ), $pattern_parts ) . '$/';
		if ( ! preg_match( $pattern, $date_string, $matches ) ) {
			return null;
		}

		$year  = absint( $matches['Y'] );
		$month = absint( $matches['m'] );
		$day   = absint( $matches['d'] );
		if ( ! checkdate( $month, $day, $year ) ) {
			return null;
		}

		return new DateTimeImmutable( sprintf( '%04d-%02d-%02d 00:00:00', $year, $month, $day ), wp_timezone() );
	}
}

if ( ! function_exists( 'tf_tour_parse_user_date' ) ) {
	/**
	 * Parse a date using Tourfic's configured date format and canonical fallbacks.
	 *
	 * @param string $date_string Date string.
	 * @return DateTimeImmutable|null
	 */
	function tf_tour_parse_user_date( $date_string ) {
		$date_string = sanitize_text_field( (string) $date_string );
		if ( '' === $date_string ) {
			return null;
		}

		$date_parts = tf_split_date_range( $date_string, false );
		if ( ! empty( $date_parts[0] ) ) {
			$date_string = $date_parts[0];
		}

		$date_formats = array_values(
			array_unique(
				array_merge(
					array( tf_tour_get_user_date_format() ),
					tf_tour_get_supported_date_formats()
				)
			)
		);
		foreach ( $date_formats as $date_format ) {
			$date = tf_tour_parse_date_by_format( $date_string, $date_format );

			if ( $date instanceof DateTimeImmutable ) {
				return $date;
			}
		}

		return null;
	}
}

if ( ! function_exists( 'tf_tour_get_reference_timestamp' ) ) {
	/**
	 * Get booking reference timestamp from selected tour date.
	 *
	 * @param string $tour_date Tour date.
	 * @return int
	 */
	function tf_tour_get_reference_timestamp( $tour_date ) {
		$date = tf_tour_parse_user_date( $tour_date );

		return $date instanceof DateTimeImmutable ? $date->getTimestamp() : current_time( 'timestamp' );
	}
}

if ( ! function_exists( 'tf_tour_calculate_age' ) ) {
	/**
	 * Calculate traveler age.
	 *
	 * @param string   $date_string    Date string.
	 * @param int|null $reference_time Reference timestamp.
	 * @return int|null
	 */
	function tf_tour_calculate_age( $date_string, $reference_time = null ) {
		$dob_date = tf_tour_parse_user_date( $date_string );
		if ( ! $dob_date instanceof DateTimeImmutable ) {
			return null;
		}

		if ( is_numeric( $reference_time ) ) {
			$reference_date = ( new DateTimeImmutable( '@' . (int) $reference_time ) )->setTimezone( wp_timezone() );
		} else {
			$reference_date = new DateTimeImmutable( 'now', wp_timezone() );
		}

		if ( $reference_date < $dob_date ) {
			return null;
		}

		return (int) $dob_date->diff( $reference_date )->y;
	}
}

if ( ! function_exists( 'tf_tour_get_passenger_type_map' ) ) {
	/**
	 * Get traveler index to passenger type map.
	 *
	 * @param int $adults   Adults.
	 * @param int $children Children.
	 * @param int $infants  Infants.
	 * @return array
	 */
	function tf_tour_get_passenger_type_map( $adults, $children, $infants ) {
		$map   = array();
		$index = 1;

		for ( $count = 0; $count < $adults; $count++, $index++ ) {
			$map[ $index ] = 'adult';
		}

		for ( $count = 0; $count < $children; $count++, $index++ ) {
			$map[ $index ] = 'child';
		}

		for ( $count = 0; $count < $infants; $count++, $index++ ) {
			$map[ $index ] = 'infant';
		}

		return $map;
	}
}

if ( ! function_exists( 'tf_tour_age_matches_passenger_type' ) ) {
	/**
	 * Check if age matches passenger type.
	 *
	 * @param string $passenger_type Passenger type.
	 * @param int    $age            Traveler age.
	 * @return bool
	 */
	function tf_tour_age_matches_passenger_type( $passenger_type, $age ) {
		$settings      = tf_tour_get_age_validation_settings();
		$adult_min_age = max( 1, absint( $settings['adult_min_age'] ) );
		$child_min_age = absint( $settings['child_min_age'] );
		$infant_max_age = max( 1, absint( $settings['infant_max_age'] ) );

		if ( 'adult' === $passenger_type ) {
			return $age >= $adult_min_age;
		}

		if ( 'child' === $passenger_type ) {
			return $age >= $child_min_age && $age < $adult_min_age;
		}

		if ( 'infant' === $passenger_type ) {
			return $age < $infant_max_age;
		}

		return true;
	}
}

if ( ! function_exists( 'tf_tour_normalize_file_field_value' ) ) {
	/**
	 * Normalize stored file field value.
	 *
	 * @param mixed $value Raw value.
	 * @return array
	 */
	function tf_tour_normalize_file_field_value( $value ) {
		if ( is_object( $value ) ) {
			$value = (array) $value;
		}

		if ( ! is_array( $value ) ) {
			return array();
		}

		return array(
			'attachment_id' => ! empty( $value['attachment_id'] ) ? absint( $value['attachment_id'] ) : 0,
			'filename'      => ! empty( $value['filename'] ) ? sanitize_file_name( $value['filename'] ) : '',
		);
	}
}

if ( ! function_exists( 'tf_tour_get_uploaded_traveler_file' ) ) {
	/**
	 * Get nested traveler file upload from request.
	 *
	 * @param int    $traveler_index Traveler index.
	 * @param string $field_name     Field name.
	 * @param array  $files          Files array.
	 * @return array|null
	 */
	function tf_tour_get_uploaded_traveler_file( $traveler_index, $field_name, $files = array() ) {
		$files = ! empty( $files ) ? $files : $_FILES; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( empty( $files['traveller']['name'][ $traveler_index ][ $field_name ] ) ) {
			return null;
		}

		return array(
			'name'     => $files['traveller']['name'][ $traveler_index ][ $field_name ],
			'type'     => $files['traveller']['type'][ $traveler_index ][ $field_name ],
			'tmp_name' => $files['traveller']['tmp_name'][ $traveler_index ][ $field_name ],
			'error'    => $files['traveller']['error'][ $traveler_index ][ $field_name ],
			'size'     => $files['traveller']['size'][ $traveler_index ][ $field_name ],
		);
	}
}

if ( ! function_exists( 'tf_tour_validate_traveler_document_upload' ) ) {
	/**
	 * Validate uploaded traveler document.
	 *
	 * @param array $file Uploaded file.
	 * @return true|WP_Error
	 */
	function tf_tour_validate_traveler_document_upload( $file ) {
		if ( ! empty( $file['error'] ) ) {
			return new WP_Error( 'tf_traveler_file_upload_error', esc_html__( 'The traveler document upload failed. Please try again.', 'tourfic' ) );
		}

		$check     = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );
		$extension = ! empty( $check['ext'] ) ? strtolower( $check['ext'] ) : '';

		if ( ! in_array( $extension, array( 'pdf', 'jpg', 'jpeg', 'png' ), true ) ) {
			return new WP_Error( 'tf_traveler_invalid_file_type', esc_html__( 'Only PDF, JPG, JPEG, and PNG files are allowed.', 'tourfic' ) );
		}

		if ( ! empty( $file['size'] ) && (int) $file['size'] > wp_max_upload_size() ) {
			return new WP_Error( 'tf_traveler_file_too_large', esc_html__( 'The uploaded traveler document exceeds the allowed file size.', 'tourfic' ) );
		}

		return true;
	}
}

if ( ! function_exists( 'tf_tour_unique_traveler_document_filename' ) ) {
	/**
	 * Generate a unique traveler document filename.
	 *
	 * @param string $dir  Upload dir.
	 * @param string $name Original name.
	 * @param string $ext  Extension.
	 * @return string
	 */
	function tf_tour_unique_traveler_document_filename( $dir, $name, $ext ) {
		unset( $dir, $name );

		return 'tf-traveler-' . wp_generate_password( 16, false, false ) . $ext;
	}
}

if ( ! function_exists( 'tf_tour_store_traveler_document_upload' ) ) {
	/**
	 * Upload and attach a traveler document.
	 *
	 * @param array  $file           Uploaded file.
	 * @param int    $post_id        Tour post ID.
	 * @param string $field_name     Field name.
	 * @param int    $traveler_index Traveler index.
	 * @return array|WP_Error
	 */
	function tf_tour_store_traveler_document_upload( $file, $post_id, $field_name, $traveler_index ) {
		$validation = tf_tour_validate_traveler_document_upload( $file );
		if ( is_wp_error( $validation ) ) {
			return $validation;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$upload = wp_handle_upload(
			$file,
			array(
				'test_form'                => false,
				'mimes'                    => array(
					'pdf'  => 'application/pdf',
					'jpg'  => 'image/jpeg',
					'jpeg' => 'image/jpeg',
					'png'  => 'image/png',
				),
				'unique_filename_callback' => 'tf_tour_unique_traveler_document_filename',
			)
		);

		if ( ! empty( $upload['error'] ) ) {
			return new WP_Error( 'tf_traveler_upload_error', sanitize_text_field( $upload['error'] ) );
		}

		$attachment = array(
			'post_mime_type' => $upload['type'],
			'post_title'     => sanitize_file_name( wp_basename( $file['name'] ) ),
			'post_content'   => '',
			'post_status'    => 'private',
		);

		$attachment_id = wp_insert_attachment( $attachment, $upload['file'], $post_id );
		if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
			return new WP_Error( 'tf_traveler_attachment_error', esc_html__( 'Unable to save the uploaded traveler document.', 'tourfic' ) );
		}

		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
		wp_update_attachment_metadata( $attachment_id, $attachment_data );

		update_post_meta( $attachment_id, '_tf_traveler_document_original_filename', sanitize_file_name( $file['name'] ) );
		update_post_meta( $attachment_id, '_tf_traveler_document_field_name', sanitize_key( $field_name ) );
		update_post_meta( $attachment_id, '_tf_traveler_document_index', absint( $traveler_index ) );
		update_post_meta( $attachment_id, '_tf_traveler_document_post_id', absint( $post_id ) );

		return array(
			'attachment_id' => absint( $attachment_id ),
			'filename'      => sanitize_file_name( $file['name'] ),
		);
	}
}

if ( ! function_exists( 'tf_tour_process_traveler_document_fields' ) ) {
	/**
	 * Process traveler document uploads and preserve existing values.
	 *
	 * @param array $traveler_details          Traveler details.
	 * @param int   $post_id                   Tour post ID.
	 * @param array $files                     Files array.
	 * @param array $expected_traveler_indexes Traveler indexes expected to have upload fields.
	 * @return array|WP_Error
	 */
	function tf_tour_process_traveler_document_fields(
		$traveler_details,
		$post_id,
		$files = array(),
		$expected_traveler_indexes = array()
	) {
		$file_fields = tf_tour_get_file_upload_fields();
		if ( empty( $file_fields ) ) {
			return $traveler_details;
		}

		$traveler_details = is_array( $traveler_details ) ? $traveler_details : array();
		$files            = ! empty( $files ) ? $files : $_FILES; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$file_indexes     = ! empty( $files['traveller']['name'] ) && is_array( $files['traveller']['name'] )
			? array_keys( $files['traveller']['name'] )
			: array();
		$traveler_indexes = array_values(
			array_filter(
				array_unique(
					array_map(
						'absint',
						array_merge( array_keys( $traveler_details ), $file_indexes, $expected_traveler_indexes )
					)
				)
			)
		);

		foreach ( $traveler_indexes as $traveler_index ) {
			$traveler_data = ! empty( $traveler_details[ $traveler_index ] ) && is_array( $traveler_details[ $traveler_index ] )
				? $traveler_details[ $traveler_index ]
				: array();
			if ( empty( $traveler_details[ $traveler_index ] ) || ! is_array( $traveler_details[ $traveler_index ] ) ) {
				$traveler_details[ $traveler_index ] = array();
			}

			foreach ( $file_fields as $field_name => $field ) {
				$uploaded_file = tf_tour_get_uploaded_traveler_file( $traveler_index, $field_name, $files );
				$existing_file = ! empty( $traveler_data[ $field_name ] ) ? tf_tour_normalize_file_field_value( $traveler_data[ $field_name ] ) : array();

				if ( ! empty( $uploaded_file ) ) {
					$stored_file = tf_tour_store_traveler_document_upload( $uploaded_file, $post_id, $field_name, $traveler_index );
					if ( is_wp_error( $stored_file ) ) {
						return $stored_file;
					}

					$traveler_details[ $traveler_index ][ $field_name ] = $stored_file;
					continue;
				}

				if ( ! empty( $existing_file ) ) {
					$traveler_details[ $traveler_index ][ $field_name ] = $existing_file;
					continue;
				}

				if ( ! empty( $field['required'] ) ) {
					return new WP_Error( 'tf_traveler_required_file_missing', esc_html__( 'Please upload the required traveler document before continuing.', 'tourfic' ) );
				}
			}
		}

		return $traveler_details;
	}
}

if ( ! function_exists( 'tf_tour_get_traveler_field_value' ) ) {
	/**
	 * Get a traveler field value by normalized field name.
	 *
	 * @param array  $traveler_data Traveler data.
	 * @param string $field_name     Normalized field name.
	 * @return mixed|null
	 */
	function tf_tour_get_traveler_field_value( $traveler_data, $field_name ) {
		if ( ! is_array( $traveler_data ) ) {
			return null;
		}

		if ( array_key_exists( $field_name, $traveler_data ) ) {
			return $traveler_data[ $field_name ];
		}

		foreach ( $traveler_data as $posted_field_name => $posted_value ) {
			if ( sanitize_key( $posted_field_name ) === $field_name ) {
				return $posted_value;
			}
		}

		return null;
	}
}

if ( ! function_exists( 'tf_tour_validate_traveler_age_limits' ) ) {
	/**
	 * Validate traveler ages against passenger types.
	 *
	 * @param array  $traveler_details        Traveler details.
	 * @param int    $adults                  Adult count.
	 * @param int    $children                Child count.
	 * @param int    $infants                 Infant count.
	 * @param string $tour_date               Tour date.
	 * @param bool   $require_traveler_fields Whether expected traveler fields must be present.
	 * @return true|WP_Error
	 */
	function tf_tour_validate_traveler_age_limits( $traveler_details, $adults, $children, $infants, $tour_date, $require_traveler_fields = false ) {
		$settings    = tf_tour_get_age_validation_settings();
		$field_names = tf_tour_get_age_validation_field_names();

		if ( empty( $settings['enabled'] ) || empty( $field_names ) ) {
			return true;
		}

		$total_people = $adults + $children + $infants;
		if ( 'single' === $settings['collection_mode'] && $total_people > 1 ) {
			return true;
		}

		$reference_time = tf_tour_get_reference_timestamp( $tour_date );
		$type_map       = tf_tour_get_passenger_type_map( $adults, $children, $infants );
		$traveler_details = is_array( $traveler_details ) ? $traveler_details : array();

		if ( empty( $traveler_details ) && ! $require_traveler_fields ) {
			return true;
		}

		$traveler_indexes = $require_traveler_fields ? array_keys( $type_map ) : array_keys( $traveler_details );
		foreach ( $traveler_indexes as $traveler_index ) {
			$traveler_data = ! empty( $traveler_details[ $traveler_index ] ) ? $traveler_details[ $traveler_index ] : array();
			if ( ! is_array( $traveler_data ) ) {
				if ( $require_traveler_fields ) {
					return new WP_Error( 'tf_traveler_age_mismatch', esc_html__( 'The entered date of birth does not match the selected passenger type.', 'tourfic' ) );
				}

				continue;
			}

			$traveler_index = absint( $traveler_index );
			$passenger_type = ! empty( $type_map[ $traveler_index ] ) ? $type_map[ $traveler_index ] : '';
			if ( '' === $passenger_type ) {
				continue;
			}

			foreach ( $field_names as $field_name ) {
				$field_value = tf_tour_get_traveler_field_value( $traveler_data, $field_name );
				if ( empty( $field_value ) || is_array( $field_value ) ) {
					if ( $require_traveler_fields ) {
						return new WP_Error( 'tf_traveler_age_mismatch', esc_html__( 'The entered date of birth does not match the selected passenger type.', 'tourfic' ) );
					}

					continue;
				}

				$age = tf_tour_calculate_age( $field_value, $reference_time );
				if ( null === $age || ! tf_tour_age_matches_passenger_type( $passenger_type, $age ) ) {
					return new WP_Error( 'tf_traveler_age_mismatch', esc_html__( 'The entered date of birth does not match the selected passenger type.', 'tourfic' ) );
				}
			}
		}

		return true;
	}
}

if ( ! function_exists( 'tf_tour_user_can_manage_traveler_documents' ) ) {
	/**
	 * Check if current user can manage traveler documents.
	 *
	 * @return bool
	 */
	function tf_tour_user_can_manage_traveler_documents() {
		return current_user_can( 'manage_options' ) || current_user_can( 'tf_manager_options' ) || current_user_can( 'tf_vendor_options' );
	}
}

if ( ! function_exists( 'tf_tour_get_traveler_document_download_url' ) ) {
	/**
	 * Get secure download URL for traveler document.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return string
	 */
	function tf_tour_get_traveler_document_download_url( $attachment_id ) {
		$attachment_id = absint( $attachment_id );
		if ( $attachment_id <= 0 ) {
			return '';
		}

		return wp_nonce_url(
			admin_url( 'admin-post.php?action=tf_download_traveler_document&attachment_id=' . $attachment_id ),
			'tf_download_traveler_document_' . $attachment_id
		);
	}
}

if ( ! function_exists( 'tf_download_traveler_document' ) ) {
	/**
	 * Download a traveler document.
	 *
	 * @return void
	 */
	function tf_download_traveler_document() {
		if ( ! tf_tour_user_can_manage_traveler_documents() ) {
			wp_die( esc_html__( 'You do not have permission to download this file.', 'tourfic' ) );
		}

		$attachment_id = isset( $_GET['attachment_id'] ) ? absint( wp_unslash( $_GET['attachment_id'] ) ) : 0;
		$nonce         = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

		if ( $attachment_id <= 0 || ! wp_verify_nonce( $nonce, 'tf_download_traveler_document_' . $attachment_id ) ) {
			wp_die( esc_html__( 'Invalid download request.', 'tourfic' ) );
		}

		$file_path = get_attached_file( $attachment_id );
		if ( empty( $file_path ) || ! file_exists( $file_path ) ) {
			wp_die( esc_html__( 'The requested file is no longer available.', 'tourfic' ) );
		}

		$file_name = get_post_meta( $attachment_id, '_tf_traveler_document_original_filename', true );
		$file_name = $file_name ? sanitize_file_name( $file_name ) : sanitize_file_name( wp_basename( $file_path ) );
		$file_type = wp_check_filetype( $file_path );
		$mime_type = ! empty( $file_type['type'] ) ? $file_type['type'] : 'application/octet-stream';

		nocache_headers();
		header( 'Content-Description: File Transfer' );
		header( 'X-Content-Type-Options: nosniff' );
		header( 'Content-Type: ' . $mime_type );
		header( 'Content-Disposition: attachment; filename="' . $file_name . '"' );
		header( 'Content-Length: ' . filesize( $file_path ) );

		readfile( $file_path );
		exit;
	}
}
add_action( 'admin_post_tf_download_traveler_document', 'tf_download_traveler_document' );

/**
 * Car Functions
 */
if ( file_exists( TF_INC_PATH . 'functions/functions-car.php' ) ) {
	require_once TF_INC_PATH . 'functions/functions-car.php';
} else {
	tf_file_missing( TF_INC_PATH . 'functions/functions-car.php' );
}

/*
 * Temporary functions
 */
if(!function_exists('tf_data_types')){
	function tf_data_types( $var ) {
		if ( ! empty( $var ) && gettype( $var ) == "string" ) {
			$tf_serialize_date = preg_replace_callback( '!s:(\d+):"(.*?)";!', function ( $match ) {
				return ( $match[1] == strlen( $match[2] ) ) ? $match[0] : 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			}, $var );

			return unserialize( $tf_serialize_date );
		} else {
			return $var;
		}
	}
}

if(!function_exists('tourfic_character_limit_callback')){
	function tourfic_character_limit_callback( $str, $limit, $dots = true ) {
		if ( strlen( $str ) > $limit ) {
			if ( $dots == true ) {
				return substr( $str, 0, $limit ) . '...';
			} else {
				return substr( $str, 0, $limit );
			}
		} else {
			return $str;
		}
	}
}

if(!function_exists('tf_is_search_form_tab_type')){
	function tf_is_search_form_tab_type( $type, $type_arr ) {
		if ( in_array( $type, $type_arr ) || in_array( 'all', $type_arr ) ) {
			return true;
		}

		return false;
	}
}

if(!function_exists('tf_is_search_form_single_tab')){
	function tf_is_search_form_single_tab( $type_arr ) {
		if ( count( $type_arr ) === 1 && $type_arr[0] !== 'all' ) {
			return true;
		}

		return false;
	}
}

function tourfic_template_settings() {
	$tf_plugin_installed = get_option( 'tourfic_template_installed' );
	if ( ! empty( $tf_plugin_installed ) ) {
		$template = 'design-1';
	} else {
		$template = 'default';
	}

	return $template;
}

if(!function_exists('tourfic_order_table_data')){
	function tourfic_order_table_data( $query ) {
		if ( class_exists( '\Tourfic\Classes\Helper' ) && is_callable( array( '\Tourfic\Classes\Helper', 'tourfic_order_table_data' ) ) ) {
			return \Tourfic\Classes\Helper::tourfic_order_table_data( $query );
		}

		global $wpdb;
		$query_type          = sanitize_key( $query['post_type'] );
		$query_select        = '*' === trim( $query['select'] ) ? '*' : preg_replace( '/[^a-zA-Z0-9_, ]/', '', $query['select'] );
		$values              = array( $query_type );
		$query_where         = '';

		if ( isset( $query['where'] ) && is_array( $query['where'] ) ) {
			$allowed_columns = array(
				'order_id'    => '%d',
				'post_id'     => '%d',
				'customer_id' => '%d',
				'room_id'     => '%s',
				'ostatus'     => '%s',
				'checkinout'  => '%s',
			);

			foreach ( $query['where'] as $column => $value ) {
				if ( ! isset( $allowed_columns[ $column ] ) || '' === $value || null === $value ) {
					continue;
				}

				$query_where .= " AND {$column} = {$allowed_columns[ $column ]}";
				$values[]     = '%d' === $allowed_columns[ $column ] ? absint( $value ) : sanitize_text_field( $value );
			}
		}

		if ( ! empty( $query['orderby'] ) ) {
			$allowed_orderby = array( 'id', 'order_id', 'order_date', 'check_in', 'check_out' );
			$orderby         = sanitize_key( $query['orderby'] );
			if ( in_array( $orderby, $allowed_orderby, true ) ) {
				$order        = ! empty( $query['order'] ) && 'ASC' === strtoupper( $query['order'] ) ? 'ASC' : 'DESC';
				$query_where .= " ORDER BY {$orderby} {$order}";
			}
		}

		$tf_tour_book_orders = $wpdb->get_results( $wpdb->prepare( "SELECT $query_select FROM {$wpdb->prefix}tf_order_data WHERE post_type = %s $query_where", $values ), ARRAY_A );

		return $tf_tour_book_orders;
	}
}

if ( ! function_exists( 'tourfic_get_user_order_table_data' ) ) {
	function tourfic_get_user_order_table_data( $query ) {
		global $wpdb;
		$query_select   = $query['select'];
		$query_type     = $query['post_type'];
		$query_customer = $query['customer_id']; // Change from 'author' to 'customer_id'
		$query_limit    = $query['limit'];

		// Adjust the query to use customer_id instead of post_author
		if ( ! is_array( $query_type ) ) {
			$orders_result = $wpdb->get_results($wpdb->prepare(
				"SELECT $query_select FROM {$wpdb->prefix}tf_order_data WHERE post_type = %s AND customer_id = %d ORDER BY order_id DESC $query_limit",
				$query_type, $query_customer
			), ARRAY_A );
		} else {
			$orders_result = $wpdb->get_results($wpdb->prepare(
				"SELECT $query_select FROM {$wpdb->prefix}tf_order_data WHERE post_type IN (" . implode( ',', array_fill( 0, count( $query_type ), '%s' ) ) . ") AND customer_id = %d ORDER BY order_id DESC $query_limit",
				array_merge( $query_type, array( $query_customer ) ) // Add customer_id to the array
			), ARRAY_A );
		}

		return $orders_result;
	}
}

if(!function_exists('tf_affiliate_callback')){
	function tf_affiliate_callback() {
		if ( current_user_can( 'activate_plugins' ) ) {
			?>
			<div class="tf-field tf-field-notice" style="width:100%;">
				<div class="tf-fieldset" style="margin: 0px;">
					<div class="tf-field-notice-inner tf-notice-info">
						<div class="tf-field-notice-content has-content">
							<?php if ( ! is_plugin_active( 'tourfic-affiliate/tourfic-affiliate.php' ) && ! file_exists( WP_PLUGIN_DIR . '/tourfic-affiliate/tourfic-affiliate.php' ) ) : ?>
								<span style="margin-right: 15px;"><?php echo esc_html__( "Tourfic affiliate addon is not installed. Please install and activate it to use this feature.", "tourfic" ); ?> </span>
								<a target="_blank" href="https://portal.themefic.com/my-account/downloads" class="tf-admin-btn tf-btn-secondary tf-submit-btn"
								   style="margin-top: 5px;"><?php echo esc_html__( "Download", "tourfic" ); ?></a>
							<?php elseif ( ! is_plugin_active( 'tourfic-affiliate/tourfic-affiliate.php' ) && file_exists( WP_PLUGIN_DIR . '/tourfic-affiliate/tourfic-affiliate.php' ) ) : ?>
								<span style="margin-right: 15px;"><?php echo esc_html__( "Tourfic affiliate addon is not activated. Please activate it to use this feature.", "tourfic" ); ?> </span>
								<a href="#" class="tf-admin-btn tf-btn-secondary tf-affiliate-active" style="margin-top: 5px;"><?php echo esc_html__( 'Activate Tourfic Affiliate', 'tourfic' ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}
}

if(!function_exists('tf_set_order')){
	function tf_set_order( $order_data ) {
		global $wpdb;
		$all_order_ids = $wpdb->get_col( "SELECT order_id FROM {$wpdb->prefix}tf_order_data" );
		do {
			$order_id = wp_rand( 10000000, 99999999 );
		} while ( in_array( $order_id, $all_order_ids ) );

		$defaults = array(
			'order_id'         => $order_id,
			'post_id'          => 0,
			'post_type'        => '',
			'room_number'      => 0,
			'check_in'         => '',
			'check_out'        => '',
			'billing_details'  => '',
			'shipping_details' => '',
			'order_details'    => '',
			'customer_id'      => 1,
			'payment_method'   => 'cod',
			'status'           => 'processing',
			'order_date'       => gmdate( 'Y-m-d H:i:s' ),
		);

		$order_data = wp_parse_args( $order_data, $defaults );

		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}tf_order_data
				( order_id, post_id, post_type, room_number, check_in, check_out, billing_details, shipping_details, order_details, customer_id, payment_method, ostatus, order_date )
				VALUES ( %d, %d, %s, %d, %s, %s, %s, %s, %s, %d, %s, %s, %s )",
				array(
					$order_data['order_id'],
					sanitize_key( $order_data['post_id'] ),
					$order_data['post_type'],
					$order_data['room_number'],
					$order_data['check_in'],
					$order_data['check_out'],
					wp_json_encode( $order_data['billing_details'] ),
					wp_json_encode( $order_data['shipping_details'] ),
					wp_json_encode( $order_data['order_details'] ),
					$order_data['customer_id'],
					$order_data['payment_method'],
					$order_data['status'],
					$order_data['order_date']
				)
			)
		);

		return $order_id;
	}
}

if(!function_exists('tf_custom_wp_kses_allow_tags')){
	function tf_custom_wp_kses_allow_tags() {
		// Allow all HTML tags and attributes
		$allowed_tags = wp_kses_allowed_html( 'post' );

		// Add form-related tags to the allowed tags
		$allowed_tags['form'] = array(
			'action'  => true,
			'method'  => true,
			'enctype' => true,
			'class'   => true,
			'id'      => true,
			'data-*'  => true,
		);

		$allowed_tags['input'] = array(
			'type'        => true,
			'name'        => true,
			'value'       => true,
			'placeholder' => true,
			'class'       => true,
			'id'          => true,
			'checked'     => true,
			'data-*'      => true,
		);

		$allowed_tags['select'] = array(
			'name'     => true,
			'class'    => true,
			'id'       => true,
			'data-*'   => true,
			'multiple' => true,
		);

		$allowed_tags['option'] = array(
			'value'  => true,
			'class'  => true,
			'id'     => true,
			'data-*' => true,
		);

		$allowed_tags['textarea'] = array(
			'name'   => true,
			'rows'   => true,
			'cols'   => true,
			'class'  => true,
			'id'     => true,
			'data-*' => true,
		);

		$allowed_tags['label'] = array(
			'for'    => true,
			'class'  => true,
			'id'     => true,
			'data-*' => true,
		);

		$allowed_tags['fieldset'] = array(
			'name'  => true,
			'class' => true,
			'id'    => true,
		);

		$allowed_tags['legend'] = array(
			'name'  => true,
			'class' => true,
			'id'    => true,
		);

		$allowed_tags['optgroup'] = array(
			'label' => true,
			'class' => true,
			'id'    => true,
		);

		$allowed_tags['script'] = array(
			'src'   => true,
			'type'  => true,
			'class' => true,
			'id'    => true,
			'async' => true,
			'defer' => true,
		);
		$allowed_tags['button'] = array(
			'class'    => true,
			'id'       => true,
			'disabled' => true,
			'data-*'   => true,

		);
		$allowed_tags['style']  = array(
			'class' => true,
			'id'    => true,
		);

		$allowed_tags['iframe'] = array(
			'class'           => true,
			'id'              => true,
			'allowfullscreen' => true,
			'frameborder'     => true,
			'src'             => true,
			'style'           => true,
			'width'           => true,
			'height'          => true,
			'title'           => true,
			'allow'           => true,
			'data-*'          => true,
		);

		$allowed_tags["svg"] = array(
			'class'           => true,
			'aria-hidden'     => true,
			'aria-labelledby' => true,
			'role'            => true,
			'xmlns'           => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true,
			'fill'            => true,
			'data-*'          => true,
		);

		$allowed_tags['g']        = array( 'fill' => true, "clip-path" => true );
		$allowed_tags['title']    = array( 'title' => true );
		$allowed_tags['rect']     = array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'fill' => true );
		$allowed_tags['path']     = array(
			'd'               => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			"stroke-linejoin" => true,
		);
		$allowed_tags['polygon']  = array(
			'points'       => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
		);
		$allowed_tags['circle']   = array(
			'cx'           => true,
			'cy'           => true,
			'r'            => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
		);
		$allowed_tags['line']     = array(
			'x1'           => true,
			'y1'           => true,
			'x2'           => true,
			'y2'           => true,
			'stroke'       => true,
			'stroke-width' => true,
		);
		$allowed_tags['text']     = array(
			'x'           => true,
			'y'           => true,
			'fill'        => true,
			'font-size'   => true,
			'font-family' => true,
			'text-anchor' => true,
		);
		$allowed_tags['defs']     = array(
			'd' => true
		);
		$allowed_tags['clipPath'] = array(
			'd' => true
		);
		$allowed_tags['code']     = true;

		return $allowed_tags;
	}
}

if ( ! function_exists( 'tf_split_date_range' ) ) {
	/**
	 * Split a Tourfic date range into start and end dates.
	 *
	 * Flatpickr uses locale-specific range separators unless overridden. Older
	 * orders/search URLs may therefore contain separators such as em dash, t/m,
	 * 至, or إلى instead of Tourfic's canonical ` - ` separator.
	 *
	 * @param string $date_range           Date range string.
	 * @param bool   $single_date_as_range Whether a single date should be returned as both start and end.
	 * @return array{0:string,1:string}
	 */
	function tf_split_date_range( $date_range, $single_date_as_range = true ) {
		$date_range = sanitize_text_field( (string) $date_range );
		$date_range = trim( preg_replace( '/\s+/u', ' ', $date_range ) );

		if ( '' === $date_range ) {
			return array( '', '' );
		}

		$separator_patterns = array(
			'/\s+-\/-\s+/u',
			'/\s+-\s+/u',
			'/\s+[–—]\s+/u',
			'/\s+t\/m\s+/iu',
			'/\s+to\s+/iu',
			'/\s+bis\s+/iu',
			'/\s+au\s+/iu',
			'/\s+a\s+/iu',
			'/\s+al\s+/iu',
			'/\s+至\s+/u',
			'/\s+إلى\s+/u',
		);

		foreach ( $separator_patterns as $pattern ) {
			$date_parts = preg_split( $pattern, $date_range, 2 );
			if ( is_array( $date_parts ) && 2 === count( $date_parts ) ) {
				return array(
					trim( $date_parts[0] ),
					trim( $date_parts[1] ),
				);
			}
		}

		if ( preg_match_all( '/\d{4}[\/.-]\d{1,2}[\/.-]\d{1,2}|\d{1,2}[\/.-]\d{1,2}[\/.-]\d{4}/u', $date_range, $matches ) ) {
			$dates = array_values( array_filter( array_map( 'trim', $matches[0] ) ) );
			if ( 2 <= count( $dates ) ) {
				return array( $dates[0], $dates[1] );
			}
			if ( 1 === count( $dates ) ) {
				return array( $dates[0], $single_date_as_range ? $dates[0] : '' );
			}
		}

		return array( $date_range, $single_date_as_range ? $date_range : '' );
	}
}

if(!function_exists('tf_convert_date_format')) {
	function tf_convert_date_format( $date, $currentFormat ) {
		$dateTime = DateTime::createFromFormat( $currentFormat, $date );

		if ( $dateTime === false ) {
			return false;
		}

		return $dateTime->format( 'Y/m/d' );
	}
}

if(!function_exists('tf_tour_date_format_changer')) {
	function tf_tour_date_format_changer($date, $format) {
		if(!empty($date) && !empty($format)) {
			$date = new DateTime($date);
			$formattedDate = $date->format($format);

			return $formattedDate;

		} else return;
	}
}
function tf_normalize_date( $date ) {
    $date = sanitize_text_field( $date );
    if ( empty( $date ) ) {
        return '';
    }

    // List of supported formats
    $formats = [
        'Y/m/d', 'd/m/Y', 'm/d/Y',
        'Y-m-d', 'd-m-Y', 'm-d-Y',
        'Y.m.d', 'd.m.Y', 'm.d.Y'
    ];

    foreach ( $formats as $format ) {
        $dt = DateTime::createFromFormat( $format, $date );
        if ( $dt && $dt->format($format) === $date ) {
            return $dt->format( 'Y/m/d' ); // normalize
        }
    }

    return ''; // return empty if no match
}
/**
 * Remove room order ids
 */
function tf_remove_order_ids_from_room() {
    $title = esc_html__( "Reset Room Availability", "tourfic" );
    $subtitle = wp_kses_post(
        sprintf(
            __( 'Remove order ids linked with this room.<br><b style="color: red;">Be aware! It is irreversible!</b>', 'tourfic' ),
        )
    );

    $button_text = esc_html__( "Reset", "tourfic" );

    echo '
    <div class="csf-title">
        <h4>' . $title . '</h4>
        <div class="csf-subtitle-text">' . $subtitle . '</div>
    </div>
    <div class="csf-fieldset">
        <button type="button" class="button button-large tf-order-remove remove-order-ids">' . $button_text . '</button>
    </div>
    <div class="clear"></div>
    ';
}


if ( ! function_exists( 'tf_get_room_unit_capacity_values' ) ) {
	function tf_get_room_unit_capacity_values( array $rooms_meta, $capacity_key ) {
		$capacity_values = array();

		foreach ( $rooms_meta as $room_meta ) {
			if ( empty( $room_meta[ $capacity_key ] ) ) {
				continue;
			}

			$room_units = ! empty( $room_meta['num-room'] ) ? max( 1, intval( $room_meta['num-room'] ) ) : 1;
			for ( $i = 0; $i < $room_units; $i ++ ) {
				$capacity_values[] = intval( $room_meta[ $capacity_key ] );
			}
		}

		return $capacity_values;
	}
}

if ( ! function_exists( 'tf_get_total_room_units' ) ) {
	function tf_get_total_room_units( array $rooms_meta ) {
		$total_room_units = 0;

		foreach ( $rooms_meta as $room_meta ) {
			$total_room_units += ! empty( $room_meta['num-room'] ) ? max( 1, intval( $room_meta['num-room'] ) ) : 1;
		}

		return $total_room_units;
	}
}

if ( ! function_exists( 'tf_room_unit_capacity_passes' ) ) {
	function tf_room_unit_capacity_passes( array $capacity_values, $requested_people, $requested_rooms ) {
		if ( empty( $requested_people ) ) {
			return true;
		}

		if ( empty( $capacity_values ) ) {
			return false;
		}

		rsort( $capacity_values, SORT_NUMERIC );

		return array_sum( array_slice( $capacity_values, 0, max( 1, intval( $requested_rooms ) ) ) ) >= intval( $requested_people );
	}
}

if ( ! function_exists( 'tf_filter_room_metas_available_for_period' ) ) {
	function tf_filter_room_metas_available_for_period( array $rooms_meta, array $date_strings ) {
		if ( empty( $date_strings ) ) {
			return $rooms_meta;
		}

		return array_filter( $rooms_meta, function( $room_meta ) use ( $date_strings ) {
			if ( empty( $room_meta['avil_by_date'] ) || empty( $room_meta['avail_date'] ) ) {
				return true;
			}

			return Availability::are_dates_available_for_rules( $room_meta['avail_date'], $date_strings );
		} );
	}
}

if(!function_exists('tf_filter_hotel_by_date')) {
	function tf_filter_hotel_by_date( $period, array &$not_found, array $data = [] ): void {

		// Form Data
		if ( isset( $data[4] ) && isset( $data[5] ) ) {
			[ $adults, $child, $room, $check_in_out, $startprice, $endprice ] = $data;
		} else {
			[ $adults, $child, $room, $check_in_out ] = $data;
		}

		// Get hotel Room meta options
		$rooms = Room::get_hotel_rooms( get_the_ID() );

		//all rooms meta
		$rooms_meta = [];
		if ( ! empty( $rooms ) ) {
			foreach ( $rooms as $single_room ) {
				$rooms_meta[ $single_room->ID ] = get_post_meta( $single_room->ID, 'tf_room_opt', true );
			}
		}

		// Remove disabled rooms

		if ( ! empty( $rooms_meta ) ):
			$rooms_meta = array_filter( $rooms_meta, function ( $value ) {
				return ! empty( $value ) && empty( $value['enable'] ) ? $value['enable'] : '' != '0';
			} );
		endif;

		// If no room return
		if ( empty( $rooms_meta ) ) {
			return;
		}

		// Set initial room availability status
		$has_hotel = false;

		$requested_rooms = max( 1, intval( $room ) );
		$searching_period = [];
		if ( ! empty( $period ) ) {
			foreach ( $period as $date ) {
				$searching_period[] = $date->format( 'Y/m/d' );
			}
		}

		$capacity_rooms_meta = tf_filter_room_metas_available_for_period( $rooms_meta, $searching_period );
		$adult_capacities = tf_get_room_unit_capacity_values( $capacity_rooms_meta, 'adult' );
		$adult_capacity_pass = tf_room_unit_capacity_passes( $adult_capacities, $adults, $requested_rooms );
		$adult_result = ! empty( $adult_capacities );

		$child_capacities = tf_get_room_unit_capacity_values( $capacity_rooms_meta, 'child' );
		$child_capacity_pass = tf_room_unit_capacity_passes( $child_capacities, $child, $requested_rooms );
		$childs_result = ! empty( $child_capacities );

		$total_room_units = tf_get_total_room_units( $capacity_rooms_meta );
		$room_validation = $total_room_units >= $requested_rooms;

		// If adult and child number validation is true proceed
		if ( $adult_result && $adult_capacity_pass && $childs_result && $child_capacity_pass && $room_validation ) {

			// Check custom date range status of room
			$avil_by_date = array_column( $rooms_meta, 'avil_by_date' );

			// Check if any room available without custom date range
			if ( in_array( 0, $avil_by_date ) || empty( $avil_by_date ) || empty( $avil_by_date[0] ) ) {

				if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
					foreach ( $rooms as $_room ) {
						$room = get_post_meta( $_room->ID, 'tf_room_opt', true );

						if ( '2' == $room['pricing-by'] ) {
							if ( ! empty( $room['adult_price'] ) ) {
								if ( $startprice <= $room['adult_price'] && $room['adult_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $room['child_price'] ) ) {
								if ( $startprice <= $room['child_price'] && $room['child_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
						if ( '1' == $room['pricing-by'] ) {
							if ( ! empty( $room['price'] ) ) {
								if ( $startprice <= $room['price'] && $room['price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
					}
				} else {
					$has_hotel = true; // Show that hotel
				}

			} else {
				// If all the room has custom date range then filter the rooms by date

				// Get custom date range repeater
				$dates = array_column( $rooms_meta, 'avail_date' );
				// If no date range return
				if ( empty( $dates ) ) {
					return;
				}

				$tf_check_in_date = 0;
				$searching_period = [];
				// Check if any date range match with search form date range and set them on array
				if ( ! empty( $period ) ) {
					foreach ( $period as $datekey => $date ) {
						if ( 0 == $datekey ) {
							$tf_check_in_date = $date->format( 'Y/m/d' );
						}
						$searching_period[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
					}
				}

				// Initial available dates array
				$availability_dates     = [];
				$tf_check_in_date_price = [];
				// Run loop through custom date range repeater and filter out only the dates
				foreach ( $dates as $date ) {
					if ( ! empty( $date ) && gettype( $date ) == "string" ) {
						$date = json_decode( $date, true );
						foreach ( $date as $sdate ) {
							if ( $tf_check_in_date == $sdate['check_in'] ) {
								$tf_check_in_date_price['price']       = $sdate['price'];
								$tf_check_in_date_price['adult_price'] = $sdate['adult_price'];
								$tf_check_in_date_price['child_price'] = $sdate['child_price'];
							}
							$availability_dates[ $sdate['check_in'] ] = $sdate['check_in'];
						}
					}
				}

				foreach ( $rooms as $_room ) {
					$room = get_post_meta( $_room->ID, 'tf_room_opt', true );

					if ( empty( $room['avail_date'] ) || ! Availability::are_dates_available_for_rules( $room['avail_date'], array_values( $searching_period ) ) ) {
						continue;
					}

					if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
						if ( Room::room_matches_price_range( $room, $startprice, $endprice, $tf_check_in_date_price ) ) {
							$has_hotel = true;
							break;
						}
					} else {
						$has_hotel = true;
						break;
					}
				}
			}

		}

		// If adult and child number validation is true proceed
		if ( $adult_result && $adult_capacity_pass && empty( $childs_result ) && $room_validation ) {

			// Check custom date range status of room
			$avil_by_date = array_column( $rooms_meta, 'avil_by_date' );

			// Check if any room available without custom date range
			if ( in_array( 0, $avil_by_date ) || empty( $avil_by_date ) || empty( $avil_by_date[0] ) ) {

				if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
					foreach ( $rooms as $_room ) {
						$room = get_post_meta( $_room->ID, 'tf_room_opt', true );

						if ( '2' == $room['pricing-by'] ) {
							if ( ! empty( $room['adult_price'] ) ) {
								if ( $startprice <= $room['adult_price'] && $room['adult_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
							if ( ! empty( $room['child_price'] ) ) {
								if ( $startprice <= $room['child_price'] && $room['child_price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
						if ( '1' == $room['pricing-by'] ) {
							if ( ! empty( $room['price'] ) ) {
								if ( $startprice <= $room['price'] && $room['price'] <= $endprice ) {
									$has_hotel = true;
								}
							}
						}
					}
				} else {
					$has_hotel = true; // Show that hotel
				}

			} else {
				// If all the room has custom date range then filter the rooms by date

				// Get custom date range repeater
				$dates = array_column( $rooms_meta, 'avail_date' );

				// If no date range return
				if ( empty( $dates ) ) {
					return;
				}

				$tf_check_in_date = 0;
				$searching_period = [];
				// Check if any date range match with search form date range and set them on array
				if ( ! empty( $period ) ) {
					foreach ( $period as $datekey => $date ) {
						if ( 0 == $datekey ) {
							$tf_check_in_date = $date->format( 'Y/m/d' );
						}
						$searching_period[ $date->format( 'Y/m/d' ) ] = $date->format( 'Y/m/d' );
					}
				}

				// Initial available dates array
				$availability_dates     = [];
				$tf_check_in_date_price = [];
				// Run loop through custom date range repeater and filter out only the dates
				foreach ( $dates as $date ) {
					if ( ! empty( $date ) && gettype( $date ) == "string" ) {
						$date = json_decode( $date, true );
						foreach ( $date as $sdate ) {
							if ( $tf_check_in_date == $sdate['check_in'] ) {
								$tf_check_in_date_price['price']       = $sdate['price'];
								$tf_check_in_date_price['adult_price'] = $sdate['adult_price'];
								$tf_check_in_date_price['child_price'] = $sdate['child_price'];
							}
							$availability_dates[ $sdate['check_in'] ] = $sdate['check_in'];
						}
					}
				}

				foreach ( $rooms as $_room ) {
					$room = get_post_meta( $_room->ID, 'tf_room_opt', true );

					if ( empty( $room['avail_date'] ) || ! Availability::are_dates_available_for_rules( $room['avail_date'], array_values( $searching_period ) ) ) {
						continue;
					}

					if ( ! empty( $rooms ) && ! empty( $startprice ) && ! empty( $endprice ) ) {
						if ( Room::room_matches_price_range( $room, $startprice, $endprice, $tf_check_in_date_price ) ) {
							$has_hotel = true;
							break;
						}
					} else {
						$has_hotel = true;
						break;
					}
				}

			}

		}

		// Conditional hotel showing
		if ( $has_hotel ) {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 0,
			);

		} else {

			$not_found[] = array(
				'post_id' => get_the_ID(),
				'found'   => 1,
			);
		}

	}
}

//review temp functions
if(!function_exists('tf_calculate_comments_rating')){
	function tf_calculate_comments_rating( $comments, &$tf_overall_rate, &$total_rating ) {

        $tf_overall_rate = [];
        foreach ( $comments as $comment ) {
            tf_calculate_user_ratings( $comment, $tf_overall_rate, $total_rating );
    
        }
        $total_rating = tf_average_ratings( $total_rating );
    
    }
}

if(!function_exists('tf_calculate_user_ratings')){
	function tf_calculate_user_ratings( $comment, &$overall_rating, &$total_rate ) {
        if ( ! is_array( $total_rate ) ) {
            $total_rate = array();
        }
        $tf_comment_meta = get_comment_meta( $comment->comment_ID, TF_COMMENT_META, true );
        $tf_base_rate    = get_comment_meta( $comment->comment_ID, TF_BASE_RATE, true );
    
        if ( $tf_comment_meta ) {
            $total_rate[] = tf_average_rating_change_on_base( tf_average_ratings( $tf_comment_meta ), $tf_base_rate );
    
            foreach ( $tf_comment_meta as $key => $ratings ) {
                // calculate rate
                $ratings = tf_average_rating_change_on_base( $ratings, $tf_base_rate );
    
                if ( is_array( $ratings ) ) {
                    $overall_rating[ $key ][] = tf_average_ratings( $ratings );
                } else {
                    $overall_rating[ $key ][] = $ratings;
                }
    
            }
        }
    }
}

if(!function_exists('tf_average_ratings')){
	function tf_average_ratings( $ratings = [] ) {

        if ( ! $ratings ) {
            return 0;
        }
    
        // No sub collection of ratings
        if ( count( $ratings ) == count( $ratings, COUNT_RECURSIVE ) ) {
            $average = array_sum( $ratings ) / count( $ratings );
        } else {
            $average = 0;
            foreach ( $ratings as $rating ) {
                $average += array_sum( $rating ) / count( $rating );
            }
            $average = $average / count( $ratings );
        }
    
        return sprintf( '%.1f', $average );
    }
}

if(!function_exists('tf_average_rating_change_on_base')){
	function tf_average_rating_change_on_base( $rating, $base_rate = 5 ) {

        $settings_base = ! empty ( Helper::tfopt( 'r-base' ) ) ? Helper::tfopt( 'r-base' ) : 5;
        $base_rate     = ! empty ( $base_rate ) ? $base_rate : 5;
    
        if ( $settings_base != $base_rate ) {
            if ( $settings_base > 5 ) {
                $rating = $rating * 2;
            } else {
                $rating = $rating / 2;
            }
        }
    
        return $rating;
    }
}

// Admin Color Palette

if(!function_exists('tf_custom_color_palette_values')){
	function tf_custom_color_palette_values(){
		$tf_brand_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-custom-brand" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-custom-brand" ) ) : [];
		$tf_text_data = ! empty( Helper::tf_data_types( Helper::tfopt( "tf-custom-text" ) ) ) ? Helper::tf_data_types( Helper::tfopt( "tf-custom-text" ) ) : [];
		
		$tf_brand_default = ! empty( $tf_brand_data['default'] ) ? $tf_brand_data['default'] : '#ddd';
		$tf_brand_dark = ! empty( $tf_brand_data['dark'] ) ? $tf_brand_data['dark'] : '#ccc';
		$tf_text_heading = ! empty( $tf_text_data['heading'] ) ? $tf_text_data['heading'] : '#ddd';
		$tf_text_paragraph = ! empty( $tf_text_data['paragraph'] ) ? $tf_text_data['paragraph'] : '#ccc';

		return [
			$tf_brand_default,
			$tf_brand_dark,
			$tf_text_heading,
			$tf_text_paragraph
		];
	}
}

function tf_get_main_post_meta($translated_post_id, $meta_key) {
	if (function_exists('wpml_get_default_language')) {
		$default_lang = wpml_get_default_language();
		$main_post_id = apply_filters('wpml_object_id', $translated_post_id, 'tf_hotel', false, $default_lang);
	} else {
		$main_post_id = $translated_post_id;
	}

	return get_post_meta($main_post_id, $meta_key, true);
}

function tf_update_main_post_meta($translated_post_id, $meta_key, $value) {
	if (function_exists('wpml_get_default_language')) {
		$default_lang = wpml_get_default_language();
		$main_post_id = apply_filters('wpml_object_id', $translated_post_id, 'tf_hotel', false, $default_lang);
	} else {
		$main_post_id = $translated_post_id;
	}

	update_post_meta($main_post_id, $meta_key, $value);
}
