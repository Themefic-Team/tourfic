<?php
/**
 * Regression coverage for removing arbitrary external-booking embed code.
 *
 * Run with: php tests/regression/external-booking-embed-code-removal.php
 */

$tourfic_root = dirname( __DIR__, 2 );

/**
 * Fail the regression with a useful message.
 *
 * @param bool   $condition Whether the contract is satisfied.
 * @param string $message   Failure message.
 * @return void
 */
function tourfic_external_booking_embed_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

/**
 * Read a required source file.
 *
 * @param string $path Source path.
 * @return string
 */
function tourfic_external_booking_embed_read( $path ) {
	tourfic_external_booking_embed_assert( is_file( $path ), "Missing source file: {$path}" );
	$contents = file_get_contents( $path );
	tourfic_external_booking_embed_assert( false !== $contents, "Could not read source file: {$path}" );

	return $contents;
}

$production_paths = array(
	$tourfic_root . '/inc',
	$tourfic_root . '/templates',
);
$forbidden_markers = array(
	'booking-code',
	'external-booking-type',
	'tourfic_show_tour_external_code',
	'tf-external-booking-embaded-form',
);

foreach ( $production_paths as $production_path ) {
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $production_path ) );
	foreach ( $iterator as $file ) {
		if ( ! $file->isFile() || 'php' !== $file->getExtension() ) {
			continue;
		}

		$contents = tourfic_external_booking_embed_read( $file->getPathname() );
		foreach ( $forbidden_markers as $marker ) {
			tourfic_external_booking_embed_assert(
				false === strpos( $contents, $marker ),
				"External embed marker '{$marker}' remains in {$file->getPathname()}"
			);
		}
	}
}

$textarea = tourfic_external_booking_embed_read( $tourfic_root . '/inc/Admin/TF_Options/fields/textarea/TF_textarea.php' );
tourfic_external_booking_embed_assert( false !== strpos( $textarea, 'esc_textarea( $this->value )' ), 'Textarea output must use context-specific escaping.' );
tourfic_external_booking_embed_assert( false !== strpos( $textarea, 'return wp_kses_post( $this->value );' ), 'Textarea values must use the standard sanitizer.' );

foreach ( array( '/inc/Classes/Helper.php', '/inc/functions.php' ) as $allowlist_file ) {
	$allowlist = tourfic_external_booking_embed_read( $tourfic_root . $allowlist_file );
	foreach ( array( 'script', 'style', 'iframe' ) as $unsafe_tag ) {
		tourfic_external_booking_embed_assert(
			1 !== preg_match( '/\\$allowed_tags\\[[\'\"]' . preg_quote( $unsafe_tag, '/' ) . '[\'\"]\\]/', $allowlist ),
			"{$unsafe_tag} must not be added to the shared KSES allowlist in {$allowlist_file}"
		);
	}
	foreach ( array( 'form', 'input', 'svg' ) as $required_tag ) {
		tourfic_external_booking_embed_assert(
			1 === preg_match( '/\\$allowed_tags\\[[\'\"]' . preg_quote( $required_tag, '/' ) . '[\'\"]\\]/', $allowlist ),
			"{$required_tag} support must remain in the shared KSES allowlist in {$allowlist_file}"
		);
	}
}

$metabox = tourfic_external_booking_embed_read( $tourfic_root . '/inc/Admin/TF_Options/classes/TF_Metabox.php' );
tourfic_external_booking_embed_assert( false !== strpos( $metabox, '$existing_meta_value = get_post_meta' ), 'Metabox saves must begin with existing metadata.' );
tourfic_external_booking_embed_assert( false !== strpos( $metabox, 'is_array( $existing_meta_value ) ? $existing_meta_value : array()' ), 'Metabox saves must preserve fields omitted from the active schema.' );
tourfic_external_booking_embed_assert( false !== strpos( $metabox, 'update_post_meta( $post_id, $this->metabox_id, $tf_meta_box_value );' ), 'Metabox saves must retain the merged metadata array.' );

foreach (
	array(
		'/inc/Classes/Tour/Tour.php',
		'/inc/Classes/Apartment/Apartment.php',
		'/inc/App/Templates/Components/Shared/Single/Booking_Form.php',
		'/inc/App/Templates/Components/Shared/Single/Rooms.php',
	) as $booking_file
) {
	$booking_source = tourfic_external_booking_embed_read( $tourfic_root . $booking_file );
	tourfic_external_booking_embed_assert( false !== strpos( $booking_source, "['booking-url']" ), "URL-based external booking was removed from {$booking_file}" );
	tourfic_external_booking_embed_assert( false !== strpos( $booking_source, 'esc_url(' ), "External booking URLs must remain escaped in {$booking_file}" );
}

fwrite( STDOUT, "External booking embed-code removal regression checks passed.\n" );
